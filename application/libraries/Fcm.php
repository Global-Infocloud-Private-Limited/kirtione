<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fcm
{
    private $serviceAccountFile;
    private $projectId;
    private $client;

    public function __construct()
    {
        // Path to your service account JSON
        $this->serviceAccountFile = APPPATH . '../firebase-key.json';
        $this->projectId = 'kirti-demo-app'; // replace with actual project ID

        // Load Google Client manually
        require_once APPPATH . 'third_party/google-api-php-client/autoload.php';

        $this->client = new Google_Client();
        $this->client->setAuthConfig($this->serviceAccountFile);
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->client->useApplicationDefaultCredentials();
    }

    public function send($token, $title, $body, $data = [])
    {
        $this->client->fetchAccessTokenWithAssertion();
        $accessToken = $this->client->getAccessToken()['access_token'];

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $data
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return ['http_code' => $httpCode, 'response' => $response];
    }
}
