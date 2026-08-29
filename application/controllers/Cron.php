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
