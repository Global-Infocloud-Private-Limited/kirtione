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
      $date10Days = date( 'Y-m-d', strtotime($currentDate . ' +3 days') );
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

          $FinalData[] = [
              'ItemID'      => $Batch['ItemID'],
              'ProductName' => $Batch['ProductName'],
              'CenterID'    => $Batch['CenterID'],
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

    function send_notification($title,$screen,$body,$booking_id,$to)
    {
        $data_arrary = array(
            "title"=>$title,
            "screen"=>$screen,
            "body"=>$body,
            "booking_id"=>$booking_id
        );
        $post_data = array(
            "priority"=>"HIGH",
            "data"=>$data_arrary,
            "to"=>$to
        );
        $finel_data = json_encode($post_data);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $finel_data,
            CURLOPT_HTTPHEADER => array(
                    "authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",
                    "content-type: application/json"
                ),
            )
        );
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
       // return $response;
        
    }
}
