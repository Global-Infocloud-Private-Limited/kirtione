<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{
    public function index($key = '')
    {
        update_option('cron_has_run_from_cli', 1);

        if (defined('APP_CRON_KEY') && (APP_CRON_KEY != $key)) {
            header('HTTP/1.0 401 Unauthorized');
            die('Passed cron job key is not correct. The cron job key should be the same like the one defined in APP_CRON_KEY constant.');
        }

        $last_cron_run                  = get_option('last_cron_run');
        $seconds = hooks()->apply_filters('cron_functions_execute_seconds', 300);

        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->load->model('cron_model');
            $this->cron_model->run();
        }
    }

    public function POOrder()
	{
	    $this->load->model('cron_model');
	    $this->cron_model->POOrder();
	}

    public function PurchaseOrderReminder()
    {
        update_option('cron_has_run_from_cli', 1);
        $this->load->model('PurchaseModel');
        $this->PurchaseModel->process_purchase_order_reminders();
        echo 'Purchase Order reminder cron executed.';
    }

    public function generate_database_backup()
    {
            $tables = ['tblaccountledger','tblK1history'];

            // Backup directory
            $backupPath = APPPATH . 'storage/backups/';

            // Create directory if not exists
            if (!is_dir($backupPath)) {
                    mkdir($backupPath, 0755, true);
            }

            // File name
            $fileName = 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . $fileName;

            // Open file
            $handle = fopen($filePath, 'w');

            if (!$handle) {
                    show_error('Unable to create backup file.');
                    return;
            }

            // Backup header
            fwrite($handle, "-- Database Backup\n");
            fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Tables: " . implode(', ', $tables) . "\n\n");

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // Process selected tables
            foreach ($tables as $table) {
                    // Security check for table name
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
                            continue;
                    }
                    // Check table exists
                    if (!$this->db->table_exists($table)) {
                            fwrite(
                                    $handle,
                                    "-- Table not found: {$table}\n\n"
                            );

                            continue;
                    }

                    fwrite($handle, "\n");
                    fwrite($handle, "-- ------------------------------------------\n");
                    fwrite($handle, "-- Table: {$table}\n");
                    fwrite($handle, "-- ------------------------------------------\n\n");

                    // Get CREATE TABLE statement
                    $createQuery = $this->db->query(
                            "SHOW CREATE TABLE `{$table}`"
                    );

                    if ($createQuery->num_rows() > 0) {

                            $createRow = $createQuery->row_array();

                            $createSql = $createRow['Create Table'];

                            fwrite(
                                    $handle,
                                    "DROP TABLE IF EXISTS `{$table}`;\n\n"
                            );

                            fwrite(
                                    $handle,
                                    $createSql . ";\n\n"
                            );
                    }

                    // Get table data
                    $query = $this->db->get($table);

                    $batchSize = 50;
                    $batchRows = [];

                    foreach ($query->result_array() as $row) {

                            $columns = [];
                            $values  = [];

                            foreach ($row as $column => $value) {

                                    $columns[] = '`' . $column . '`';

                                    if ($value === null) {
                                            $values[] = 'NULL';
                                    } else {
                                            $values[] = $this->db->escape($value);
                                    }
                            }

                            $batchRows[] = '(' . implode(', ', $values) . ')';

                            // Write every 50 rows
                            if (count($batchRows) >= $batchSize) {

                                    $insertSql =
                                            "INSERT INTO `{$table}` (" .
                                            implode(', ', $columns) .
                                            ") VALUES\n" .
                                            implode(",\n", $batchRows) .
                                            ";\n";

                                    fwrite($handle, $insertSql);

                                    $batchRows = [];
                            }
                    }

                    // Write remaining rows
                    if (!empty($batchRows)) {

                            $insertSql =
                                    "INSERT INTO `{$table}` (" .
                                    implode(', ', $columns) .
                                    ") VALUES\n" .
                                    implode(",\n", $batchRows) .
                                    ";\n";

                            fwrite($handle, $insertSql);
                    }

                    fwrite($handle, "\n");
            }

            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");

            fclose($handle);

            // Response
            echo '<pre>';
            print_r([
                    'status'   => true,
                    'message'  => 'Database backup generated successfully.',
                    'file'     => $fileName,
                    'path'     => $filePath,
                    'tables'   => $tables
            ]);
            echo '</pre>';
    }

    public function ExpiredStockNotification(){
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      header('Content-Type: application/json');

      if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
      }
      
      $DaysFilter = 30;
      $this->db->select('tblK1history.*,tblproduct.ProductName');
      $this->db->from('tblK1history');
      $this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID','inner');

      $currentDate = date('Y-m-d');
      $date10Days = date( 'Y-m-d', strtotime($currentDate . ' +10 days') );
      $date30Days = date( 'Y-m-d', strtotime($currentDate . ' +30 days') );
      $expiryDates = [ $currentDate, $date10Days, $date30Days];
      $expiryDatesSql = implode( ',', array_map( [$this->db, 'escape'], $expiryDates ) );

      $this->db->where("
				CASE
					WHEN tblK1history.ExpDate LIKE '%/%'
						THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')

					WHEN tblK1history.ExpDate LIKE '%:%'
						THEN DATE(tblK1history.ExpDate)

					ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
				END IN ($expiryDatesSql)
      ", null, false);

      $this->db->where('tblK1history.BatchNo !=', '');

      $this->db->order_by("
				CASE
					WHEN tblK1history.ExpDate LIKE '%/%'
						THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')

					WHEN tblK1history.ExpDate LIKE '%:%'
						THEN DATE(tblK1history.ExpDate)

					ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
				END
      ", '', false);

      $query = $this->db->get();
      $GetHistoryData = $query->result_array();

      $centers = $this->db->select('tcm.CenterID, tcm.CenterName, tcm.MobileNo, ts.fcm_token')
        	                ->from('tblCenterMaster as tcm')
													->join('tblstaff as ts', 'ts.phonenumber = tcm.MobileNo', 'left')
													->get()
													->result_array();

			$centers = array_column($centers, null, 'CenterID');
			// echo json_encode($centers); die;

      $BatchList = [];
      foreach ($GetHistoryData as $row) {
          if (empty($row['BatchNo'])) {
              continue;
          }

          $ItemID   = $row['ItemID'];
          $BatchNo  = $row['BatchNo'];
          $Center   = $row['CenterID'];
          $ExpDate  = $row['ExpDate'];

          if (!empty($ExpDate)) {
              if (strpos($ExpDate, '/') !== false) {
                  $timestamp = strtotime(str_replace('/', '-', $ExpDate));
              } else {
                  $timestamp = strtotime($ExpDate);
              }

              $NormalizedExpDate = $timestamp ? date('Y-m-d', $timestamp) : '';
          } else {
              $NormalizedExpDate = '';
          }

          $BatchKey = $ItemID . '_' . $BatchNo . '_' . $Center;
          if (!isset($BatchList[$BatchKey])) {
              $BatchList[$BatchKey] = [
                  'ItemID'      => $ItemID,
                  'BatchNo'     => $BatchNo,
                  'ExpDate'     => $NormalizedExpDate,
                  'ProductName' => $row['ProductName'],
                  'CenterID'    => $Center,
									'FCM_Token'   => $centers[$Center]['fcm_token'],

                  'OpeningQty'  => 0,
                  'InwardQty'   => 0,
                  'PurchQty'    => 0,
                  'PurchRtnQty' => 0,
                  'SaleQty'     => 0,
                  'SaleRtnQty'  => 0,
                  'PrdQty'      => 0,
                  'IssueQty'    => 0,
                  'AdjQty'      => 0,
                  'InQty'       => 0,
                  'OutQty'      => 0
              ];
          }

          $Qty = (float)$row['BilledQty'];
          // Sale
          if ( $row['TType'] == 'O' && $row['TType2'] == 'SALE' ) {
              $BatchList[$BatchKey]['SaleQty'] += $Qty;
          }

          // Fresh Sale Return
          elseif ( $row['TType'] == 'SR' && $row['TType2'] == 'FRESH RETURN' ) {
              $BatchList[$BatchKey]['SaleRtnQty'] += $Qty;
          }

          // Purchase
          elseif ( $row['TType'] == 'P' && $row['TType2'] == 'Purchase' ) {
              $BatchList[$BatchKey]['PurchQty'] += $Qty;
          }

          // Purchase Return
          elseif ( $row['TType'] == 'PR' && $row['TType2'] == 'PURCHASE RETURN' ) {
              $BatchList[$BatchKey]['PurchRtnQty'] += $Qty;
          }

          // Transfer IN
          elseif ( $row['TType'] == 'T' && $row['TType2'] == 'IN' ) {
              $BatchList[$BatchKey]['InQty'] += $Qty;
          }

          // Transfer OUT
          elseif ( $row['TType'] == 'T' && $row['TType2'] == 'OUT' ) {
              $BatchList[$BatchKey]['OutQty'] += $Qty;
          }

          // Purchase Inward
          elseif ( $row['TType'] == 'I' && $row['TType2'] == 'INWARD' ) {
              $BatchList[$BatchKey]['InwardQty'] += $Qty;
          }

          // Adjustment
          elseif ($row['TType'] == 'X') {
              $BatchList[$BatchKey]['AdjQty'] += $Qty;
          }
      }

      $FinalData = [];
      foreach ($BatchList as $Batch) {

          $StockQty =
              $Batch['OpeningQty']
              + $Batch['InwardQty']
              + $Batch['PurchQty']
              - $Batch['PurchRtnQty']
              - $Batch['SaleQty']
              + $Batch['SaleRtnQty']
              + $Batch['PrdQty']
              - $Batch['IssueQty']
              - $Batch['AdjQty']
              + $Batch['InQty']
              - $Batch['OutQty'];

          if ((float)$StockQty == 0) {
              continue;
          }

					$description = $Batch['ProductName'].' Stock for '.$Batch['BatchNo'].' Expired on '.$Batch['ExpDate'];
					switch ($Batch['ExpDate']) {
						case $currentDate:
							$description = $Batch['ProductName'].' Stock for '.$Batch['BatchNo'].' Expired today. Qty: '.$StockQty;
							break;
						case $date10Days:
							$description = $Batch['ProductName'].' Stock for '.$Batch['BatchNo'].' Expired in 10 days. Qty: '.$StockQty;
							break;
						case $date30Days:
							$description = $Batch['ProductName'].' Stock for '.$Batch['BatchNo'].' Expired in 30 days. Qty: '.$StockQty;
							break;
						default:
							break;
					}

					if(!empty($Batch['FCM_Token']) && $Batch['FCM_Token'] != null){
						$this->send_notifications('Stock Alert', '1', $description, '', $Batch['FCM_Token']);
					}

          $FinalData[] = [
						'ItemID'      => $Batch['ItemID'],
						'ProductName' => $Batch['ProductName'],
						'CenterID'    => $Batch['CenterID'],
						'FCM_Token'   => $Batch['FCM_Token'],
						'BatchNo'     => $Batch['BatchNo'],
						'ExpDate'     => $Batch['ExpDate'],
						'StockQty'    => round($StockQty, 2)
          ];
      }

      if(empty($FinalData)){
        echo  json_encode(["status" => false, "message" => "List not found"]);
      }else{
        echo  json_encode(["status" => true, "message" => "List found", "data" => $FinalData]);
      }
    }

    // function send_notification($title,$screen,$body,$booking_id,$to)
    // {
    //     $data_arrary = array(
    //         "title"=>$title,
    //         "screen"=>$screen,
    //         "body"=>$body,
    //         "booking_id"=>$booking_id
    //     );
    //     $post_data = array(
    //         "priority"=>"HIGH",
    //         "data"=>$data_arrary,
    //         "to"=>$to
    //     );
    //     $finel_data = json_encode($post_data);
        
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "POST",
    //         CURLOPT_POSTFIELDS => $finel_data,
    //         CURLOPT_HTTPHEADER => array(
    //                 "authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",
    //                 "content-type: application/json"
    //             ),
    //         )
    //     );
    //     $response = curl_exec($curl);
    //     $err = curl_error($curl);
    //     curl_close($curl);
    //    // return $response;
        
    // }

    public function send_notifications($title, $screen, $body, $booking_id='', $to) {
        
        // header('Access-Control-Allow-Origin: *');
        // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        // header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        // header('Content-Type: application/json');

        // if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        //     http_response_code(200);
        //     exit;
        // }
        
        // 1. Define the path to your service account JSON file
        $jsonPath = APPPATH . 'config/service-account.json';

        if (!file_exists($jsonPath)) {
            log_message('error', 'FCM: Service account file not found.');
            return;
        }

        // 2. Fetch the Access Token
        $accessToken = $this->get_fcm_access_token($jsonPath);

        if (!$accessToken) {
            log_message('error', 'FCM: Failed to generate Access Token.');
            return;
        }

        // 3. Read your project_id dynamically from the JSON file
        $serviceAccount = json_decode(file_get_contents($jsonPath), true);
        $projectId = $serviceAccount['project_id'];

        // 4. Send notification via FCM v1 HTTP API
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $to, // Replace with receiver token
                'notification' => [
                    'title' => $title,
                    'body'  => $body
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // echo "Status Code: " . $httpCode . "\n";
        // echo "Response: " . $response;
    }

    /**
     * Helper function to generate OAuth 2.0 Access Token from Service Account JSON
     */
    private function get_fcm_access_token($filePath) {
        $jsonKey = json_decode(file_get_contents($filePath), true);

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss'   => $jsonKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => $jsonKey['token_uri'],
            'exp'   => $now + 3600,
            'iat'   => $now
        ]);

        // Base64Url Encoding
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Sign JWT using Private Key from JSON
        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $jsonKey['private_key'], 'SHA256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        // Exchange JWT for OAuth2 Access Token
        $ch = curl_init($jsonKey['token_uri']);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt
            ]),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $res['access_token'] ?? null;
    }
}
