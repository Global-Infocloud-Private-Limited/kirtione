<?php
// Autoload Composer dependencies
require_once '/home/kirti2ginf9/public_html/vendor/autoload.php';

// Load the service account credentials JSON file
$serviceAccountPath = '/home/kirti2ginf9/firebase-creds/kirti-demo-app-firebase-adminsdk-3a53r-bfae7170bd.json';

// Create a Google Client instance
$client = new Google_Client();
$client->setAuthConfig($serviceAccountPath);
$client->addScope('https://www.googleapis.com/auth/cloud-platform');

// Fetch OAuth 2.0 access token
$accessToken = $client->fetchAccessTokenWithAssertion();

// Prepare HTTP client (Guzzle)
$httpClient = new GuzzleHttp\Client([
    'base_uri' => 'https://fcm.googleapis.com/',
    'headers' => [
        'Authorization' => 'Bearer ' . $accessToken['access_token'],
        'Content-Type'  => 'application/json',
    ],
]);

// Define the message payload
$deviceToken = 'd7XL91OTSLiDxOYbiX3aaY:APA91bHW26Y4zsAFmbdDM_syQhCfqr5uW9z2451ITT8LYnEQ3qh-GWetT0ls6Wg1ofUQI6sf01d3EC0pDwzOR5KEI1xr9kGORh52Z2AYTN4RotEHLDP2BM0';
$projectId   = 'kirti-demo-app'; // Your Firebase project ID

$payload = [
    'message' => [
        'token' => $deviceToken,
        'notification' => [
            'title' => 'Hello!',
            'body'  => 'This is a test push notification from PHP using the HTTP v1 API.',
        ],
    ]
];

// Send the notification
try {
    $response = $httpClient->post("v1/projects/{$projectId}/messages:send", [
        'json' => $payload,
    ]);

    echo 'Notification sent successfully: ' . $response->getBody();
} catch (Exception $e) {
    echo 'Error sending notification: ' . $e->getMessage();
}
?>
