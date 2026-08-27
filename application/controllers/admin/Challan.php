<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Challan extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
        $this->load->model('order_model');
        $this->load->model('challan_model');
         $this->load->model('clients_model');
        $this->load->model('invoice_items_model');
        $this->load->helper('sales_helper');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = '')
    {
        //$this->list_challan($id);
        //$this->challanAddEdit();
        $redUrl = admin_url('challan/challanAddEdit');
        redirect($redUrl);
    }
    
    public function itemlist(){
        $this->load->model('invoice_items_model');
    // POST data
    $postData = $this->input->post();

    // Get data
    $data = $this->invoice_items_model->getitem($postData);

    echo json_encode($data);
  }
  
  public function challan_list()
    {
        if (!has_permission_new('challan_list', '', 'view')) {
            access_denied('invoices');
        }
        
        $data['title']                = " Challan List";
        $data['bodyclass']            = 'challan-total-manual';
        $this->load->view('admin/challan/challan_list', $data);
    }
    
    public function get_Account_Details(){
        
        // POST data
        $postData = $this->input->post();
    
        // Get data
        $Account_data = $this->challan_model->get_Account_Details($postData);
    
        echo json_encode($Account_data);
    }
    public function accountlist_driver(){
        
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->challan_model->accountlist_driver($postData);
    
        echo json_encode($data);
    }
    
    public function get_Loader_Details(){
        
        // POST data
        $postData = $this->input->post();
        // Get data
        $Account_data = $this->challan_model->get_Loader_Details($postData);
    
        echo json_encode($Account_data);
    }
    public function accountlist_Loader(){
        
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->challan_model->accountlist_Loader($postData);
    
        echo json_encode($data);
    }
    
    public function accountlist_salesMan(){
        
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->challan_model->accountlist_salesMan($postData);
    
        echo json_encode($data);
    }
    
    public function get_Account_Details_salesman(){
        
        // POST data
        $postData = $this->input->post();
    
        // Get data
        $Account_data = $this->challan_model->get_Account_Details_salesman($postData);
    
        echo json_encode($Account_data);
    }
    
    public function GetTaxableTransaction(){
        // POST data
        $postData = $this->input->post();
        // Get data
        $Salesdata = $this->challan_model->GetTaxableTransaction($postData);
        echo json_encode($Salesdata);
    }
    
    public function AddEdit($id = '')
    {
        if (!has_permission_new('challan_list', '', 'view')) {
            access_denied('challan');
        }
        if ($this->input->post()) {
            
            $selected_company = $this->session->userdata('root_company');
            if($selected_company == "1"){
            $GodownID = 'CSPL';
        }else if($selected_company == "2"){
            $GodownID = 'CFF';
        }else if($selected_company == "3"){
            $GodownID = 'CBUPL';
        }
            $fy = $this->session->userdata('finacial_year');
            $data = $this->input->post();
            $formSubmit = $this->input->post('submitForm');
            
           if($formSubmit == "cancel"){
               if (!has_permission_new('challan_list', '', 'delete')) {
                    access_denied('challan');
                    }
               //echo "<pre>";
               $trans_id_array = explode(',', $data["trans_id"]);
               $date = $data['org_date'];
               $newmonth = substr($date,5,2);
               $month = $newmonth;
            if($month == "01"){
               $m = 11; 
            }
            if($month == "02"){
               $m = 12; 
            }
            if($month == "03"){
               $m = 13; 
            }
            if($month == "04"){
               $m = 2; 
            }
            if($month == "05"){
               $m = 3; 
            }
            if($month == "06"){
               $m = 4; 
            }
            if($month == "07"){
               $m = 5; 
            }
            if($month == "08"){
               $m = 6; 
            }
            if($month == "09"){
               $m = 7; 
            }
            if($month == "10"){
               $m = 8; 
            }
            if($month == "11"){
               $m = 9; 
            }
            if($month == "12"){
               $m = 10; 
            }
            $mm = "BAL".$m;
            //echo $mm;
               
               
               $this->db->where('ChallanID', $data["number"]);
               $this->db->update(db_prefix() . 'challanmaster', [
                    'Crates' =>0,
                    'Cases' =>0,
                    'ChallanAmt' =>0.00,
                    'UserID2' =>$this->session->userdata('username'),
                    'Lupdate' =>date('Y-m-d H:i:s'),
                ]);
                if($this->db->affected_rows() > 0){
                    
                    foreach ($trans_id_array as $value) {
                   $get_ledger_details = $this->challan_model->get_ledger_data($value);
                   
                   //print_r($get_ledger_details);
                   foreach ($get_ledger_details as $key_ledger => $value_ledger) {
                         # code...
                            $acc_bal_data = $this->challan_model->get_acc_bal($value_ledger["AccountID"]);
                            $current_bal = $acc_bal_data->$mm;
                            if($value_ledger["TType"] == "C"){
                                $current_amt = $current_bal + $value_ledger["Amount"];
                                
                            }else{
                                $current_amt = $current_bal - $value_ledger["Amount"];
                               
                            }
                            
                                $this->db->where('PlantID', $selected_company);
                                $this->db->LIKE('FY', $fy);
                                $this->db->LIKE('AccountID', $value_ledger["AccountID"]);
                                $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $current_amt,
                                                ]);
                          
                       }
                    
               }
                //die;    
                $challan_item = $this->challan_model->get_challan_item_data($data["number"]);
                    foreach ($challan_item as $key1 => $value1) {
                        # code...
                        $stock_data = $this->challan_model->get_stock_item($value1["ItemID"]);
                        $item_stock= $stock_data->SQty;
                        $new_stock = $item_stock - $value1["BilledQty"];
                        // Stock update
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy);  
                        $this->db->where('ItemID', $value1["ItemID"]);
                        $this->db->where('GodownID',$GodownID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                            'SQty' => $new_stock,
                                        ]);
                    }
                    
                   $this->db->where('ChallanID', $data["number"]);
                   $this->db->update(db_prefix() . 'ordermaster', [
                                'OrderAmt' =>0.00,
                                'Crates' =>0,
                                'Cases' =>0,
                            ]);
                    $this->db->where('BillID', $data["number"]);
                    $this->db->update(db_prefix() . 'history', [
                                
                                'BilledQty' =>0.00,
                                'OrderAmt' =>0.00,
                                'cgstamt' =>0.00,
                                'sgstamt' =>0.00,
                                'igstamt' =>0.00,
                                'ChallanAmt' =>0.00,
                                'NetOrderAmt' =>0.00,
                                'NetChallanAmt' =>0.00,
                            ]);
                    $this->db->where('ChallanID', $data["number"]);
                    $this->db->update(db_prefix() . 'salesmaster', [
                                'SaleAmt' =>0.00,
                                'sgstamt' =>0.00,
                                'cgstamt' =>0.00,
                                'igstamt' =>0.00,
                                'BillAmt' =>0.00,
                                'RndAmt' =>0.00,
                                'tcsAmt' =>0.00,
                                'DiscAmt' =>0.00,
                            ]);
                    
                    foreach ($trans_id_array as $value) {
                         # code...
                         //echo $value;
                        $this->db->where('VoucherID', $value);
                        $this->db->update(db_prefix() . 'accountledger', [
                                'Amount' =>0.00,
                            ]);
                        }
                    
                    set_alert('success', 'Updated challan Successfully');
                    $redUrl = admin_url('challan/edit_challan/' . $id);
                }
           }else{
            
               if (!has_permission_new('challan_list', '', 'edit')) {
                    access_denied('challan');
                    }
               if($data["number"]){
                $tcs_detail = $this->challan_model->get_tcsper();
                $tcsper = $tcs_detail[0]['tcs'];
                
                if($data["challan_vehicle"]=="TV"){
                    $challan_vehicle = strtoupper($data["vahicle_number"]);
                }else{
                    $challan_vehicle = $data["challan_vehicle"];
                }
                $CHLUpdate = array(
                    'RouteID'=>$data["challan_route"],
                    'Crates'=>$data["txtCrates"],
                    'Cases'=>$data["txtCases"],
                    'ChallanAmt'=>$data["txtchalanvalue"],
                    'UserID2' =>$this->session->userdata('username'),
                    'Lupdate' =>date('Y-m-d H:i:s'),
                );
                $this->db->where('ChallanID', $data["number"]);
                
                $order_ids = $data["order_id"];
                // Check order Amount is greter than accountBalance Amount
                foreach ($order_ids as $orderid1) {
                    $order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
                    $get_account_details = $this->challan_model->get_account_detailId($order_data1->AccountID);
                    if($get_account_details->bill_till_bal == "Y"){
                        $get_account_bal = $this->challan_model->get_account_balance($order_data1->AccountID);
                        $sum_bal = $get_account_bal->BAL1 + $get_account_bal->BAL2 + $get_account_bal->BAL3 + $get_account_bal->BAL4 + $get_account_bal->BAL5 + $get_account_bal->BAL6 + $get_account_bal->BAL7 + $get_account_bal->BAL8 + $get_account_bal->BAL9 + $get_account_bal->BAL10 + $get_account_bal->BAL11 + $get_account_bal->BAL12 + $get_account_bal->BAL13;
                        if($sum_bal >= 0){
                            set_alert('warning', "Challan Amt is greater than Balance Amt... ");
                            $redUrl = admin_url('challan/');
                            redirect($redUrl);
                        }else{
                            if(abs($sum_bal) >= $data['txtchalanvalue']){
                            $available = true;
                            }else{
                                set_alert('warning', "Challan Amt is greater than Balance Amt... ");
                                $redUrl = admin_url('challan/');
                                redirect($redUrl);
                                //$available = false;
                            }
                        }
                    }
                }
                
                // Order History table Update
            foreach ($order_ids as $orderid1) {
                $order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
                foreach ($order_data1->items as $key=>$value) {
                    
                    if(is_null($value['BillID'])){
                        
                    }else{
                        $stock_data = $this->challan_model->get_stock_item($value["ItemID"]);
                        $item_stock= $stock_data->SQty;
                        $new_stock = $item_stock - $value['BilledQty'];
                    // Stock Revert
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy);  
                        $this->db->where('GodownID',$GodownID);
                        $this->db->where('ItemID', $value["ItemID"]);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                            'SQty' => $new_stock,
                                        ]);
                    }
                    $qty_name = 'qty_'.$orderid1.'_'.$value['ItemID'];
                    $qty = $data[$qty_name];
                    $packQty = $value['CaseQty'];
                    $orderQty = $packQty * $qty;
                    
                    $BasicRate = $value['BasicRate'];
                    $OrderAmt = $BasicRate * $orderQty;
                    $CGSTAmt = '';
                    $SGSTAmt = '';
                    $IGSTAmt = '';
                    if($value['igst'] == ""){
                        $CGSTAmt = ($OrderAmt / 100 ) * $value['cgst'];
                        $SGSTAmt = ($OrderAmt / 100 ) * $value['sgst'];
                        $NetOrderAmt = $OrderAmt + $CGSTAmt + $SGSTAmt;
                    }else{
                        $IGSTAmt = ($OrderAmt / 100 ) * $value['igst'];
                        $NetOrderAmt = $OrderAmt + $IGSTAmt;
                    }
                    
                    $update_array = array(
                        'eOrderQty'=>$orderQty,
                        'BilledQty'=>$orderQty,
                        'OrderAmt'=>$OrderAmt,
                        'ChallanAmt'=>$OrderAmt,
                        'cgstamt'=>$CGSTAmt,
                        'sgstamt'=>$SGSTAmt,
                        'igstamt'=>$IGSTAmt,
                        'NetOrderAmt'=>$NetOrderAmt,
                        'NetChallanAmt'=>$NetOrderAmt,
                        'UserID2'=>$this->session->userdata('username'),
                        'Lupdate'=>date('Y-m-d H:i:s'),
                        );
                //print_r($update_array);
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $fy);  
                    $this->db->where('OrderID', $orderid1);
                    $this->db->where('ItemID', $value['ItemID']);
                    $this->db->update(db_prefix() . 'history', $update_array);
                    
                        $stock_data = $this->challan_model->get_stock_item($value["ItemID"]);
                        $item_stock= $stock_data->SQty;
                        $new_stock = $item_stock + $orderQty;
                    // Stock Revert
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy); 
                        $this->db->where('GodownID',$GodownID);
                        $this->db->where('ItemID', $value["ItemID"]);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                            'SQty' => $new_stock,
                                        ]);
                }
            }
            
        foreach ($order_ids as $orderID) {
            $order_data1 = $this->challan_model->getorderSum_by_orderId($orderID);
            
            
            if($order_data1->istcs == '1'){
                $TcsAmt = ($order_data1->OrderSum /100) * $tcsper;
            }else{
                $TcsAmt = 0;
            }
            $OrdAmt = $order_data1->OrderSum + $TcsAmt;
            
            $updateORD = array(
                'OrderAmt'=>$OrdAmt,
                'Crates'=>$order_data1->crateSum,
                'Cases'=>$order_data1->casesSum,
                'UserID2'=>$this->session->userdata('username'),
                'Lupdate'=>date('Y-m-d H:i:s'),
            );
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $fy);  
            $this->db->where('OrderID', $orderID);
            $this->db->update(db_prefix() . 'ordermaster', $updateORD);
        }
        
        // Free Driver, Loader and SalesMan
        
            $getCHLDetails = $this->challan_model->get($data["number"]);
            //print_r($getCHLDetails);
            // Dreiver Free
            $this->db->where('PlantID', $selected_company);
            $this->db->where('AccountID', $getCHLDetails->DriverID);
            $this->db->where('EngageID', $getCHLDetails->VehicleID);
            $this->db->where('SLDTypeID', '3');
            $this->db->delete(db_prefix() . 'accountsld');
            
            // Loader Free
            if($getCHLDetails->LoaderID !== ''){
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $getCHLDetails->LoaderID);
                $this->db->where('EngageID', $getCHLDetails->VehicleID);
                $this->db->where('SLDTypeID', '2');
                $this->db->delete(db_prefix() . 'accountsld');
            }
            // SalesMan Free
            if($getCHLDetails->SalesmanID !== ''){
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $getCHLDetails->SalesmanID);
                $this->db->where('EngageID', $getCHLDetails->VehicleID);
                $this->db->where('SLDTypeID', '1');
                $this->db->delete(db_prefix() . 'accountsld');
            }
        // Enggaged Driver , loader And SalesMan
            
            if($data['challan_vehicle'] !== "TV"){
                $driver_Eng =array(
                    "PlantID"=>$selected_company,
                    "AccountID"=>$data['challan_driver'],
                    "SLDTypeID"=>3,
                    "EngageID"=>$vahicle_number,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountsld', $driver_Eng);
            }
            
            $loader_Eng =array(
                    "PlantID"=>$selected_company,
                    "AccountID"=>$data['challan_loader'],
                    "SLDTypeID"=>2,
                    "EngageID"=>$vahicle_number,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountsld', $loader_Eng);
                
                $salesman_Eng =array(
                    "PlantID"=>$selected_company,
                    "AccountID"=>$data['challan_sales_man'],
                    "SLDTypeID"=>1,
                    "EngageID"=>$vahicle_number,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountsld', $salesman_Eng);
            $Oldmonth = substr(to_sql_date($data["OldDate"]),5,2);
            if($Oldmonth == "01"){
                $mOld = 11; 
            }
            if($Oldmonth == "02"){
                $mOld = 12; 
            }
            if($Oldmonth == "03"){
                $mOld = 13; 
            }
            if($Oldmonth == "04"){
                $mOld = 2; 
            }
            if($Oldmonth == "05"){
                $mOld = 3; 
            }
            if($Oldmonth == "06"){
                $mOld = 4; 
            }
            if($Oldmonth == "07"){
                $mOld = 5; 
            }
            if($Oldmonth == "08"){
                $mOld = 6; 
            }
            if($Oldmonth == "09"){
                $mOld = 7; 
            }
            if($Oldmonth == "10"){
                $mOld = 8; 
            }
            if($Oldmonth == "11"){
                $mOld = 9; 
            }
            if($Oldmonth == "12"){
                $mOld = 10; 
            }
            $mmOld = "BAL".$mOld;
            
            $month = substr(to_sql_date($data["date"]),5,2);
            if($month == "01"){
                $m = 11; 
            }
            if($month == "02"){
                $m = 12; 
            }
            if($month == "03"){
                $m = 13; 
            }
            if($month == "04"){
                $m = 2; 
            }
            if($month == "05"){
                $m = 3; 
            }
            if($month == "06"){
            $m = 4; 
            }
            if($month == "07"){
            $m = 5; 
            }
            if($month == "08"){
            $m = 6; 
            }
            if($month == "09"){
                $m = 7; 
            }
            if($month == "10"){
                $m = 8; 
            }
            if($month == "11"){
                $m = 9; 
            }
            if($month == "12"){
                $m = 10; 
            }
            $mm = "BAL".$m;    
                
            foreach ($order_ids as $orderID) {
                $itemCount = 0;
                $order_data = $this->challan_model->getorderdetail_by_orderId($orderID);
                $itemCount = count($order_data->items);
                $order_AmtSum = $this->challan_model->getorderSum_by_orderId($orderID);
                
                $BillAmt = $order_AmtSum->OrderSum;
                $saleAmt = $order_AmtSum->SaleAmtSum;
                $cgstAmt = $order_AmtSum->cgstAmtSum;
                $sgstAmt = $order_AmtSum->sgstAmtSum;
                $igstAmt = $order_AmtSum->igstAmtSum;
                $roundup1 = 0;
                if($order_AmtSum->istcs == '1'){
                $Y = ($order_AmtSum->OrderSum /100) * $tcsper;
                }else{
                    $Y = 0;
                }
                $BillAmtF = $BillAmt + $Y;
                $RndAmt = round($BillAmtF);
                $roundup2 = $BillAmtF - $RndAmt;
                $round_variation = $roundup2 + $roundup1;
                
                if(is_null($order_data->ChallanID) || $order_data->ChallanID == ''){
                   
                    if($order_data->client->ActSalestype =="Sales"){
                        if($order_data->OrderType=="TaxItems"){
                            $bt = "T";
                            if($selected_company == 1){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbupl');
                            }
                        $full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "TAX".$fy.$full_tax_number;
                        }else {
                            $bt = "B";
                            if($selected_company == 1){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbupl');
                            }
                        $full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "BOS".$fy.$full_nontax_number;
                        }
                    }elseif($order_data->client->ActSalestype =="StockTransfer"){
                        $bt = "M";
                        if($selected_company == 1){
                            $new_trnNumber = get_option('next_trn_number_for_cspl');
                        }elseif($selected_company == 2){
                            $new_trnNumber = get_option('next_trn_number_for_cff');
                        }elseif($selected_company == 3){
                            $new_trnNumber = get_option('next_trn_number_for_cbu');
                        }elseif($selected_company == 4){
                            $new_trnNumber = get_option('next_trn_number_for_cbupl');
                        }
                    $full_trnnumber = str_pad($new_trnNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    $saleid = "TRN".$fy.$full_trnnumber;
                    }elseif($order_data->client->ActSalestype =="CNF"){
                        $bt = "C";
                        if($selected_company == 1){
                            $new_cnfNumber = get_option('next_cnf_number_for_cspl');
                        }elseif($selected_company == 2){
                            $new_cnfNumber = get_option('next_cnf_number_for_cff');
                        }elseif($selected_company == 3){
                            $new_cnfNumber = get_option('next_cnf_number_for_cbu');
                        }elseif($selected_company == 4){
                            $new_cnfNumber = get_option('next_cnf_number_for_cbupl');
                        }
                    $full_cnfnumber = str_pad($new_cnfNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    $saleid = "CNF".$fy.$full_cnfnumber;
                    }else{
                        if($order_data->OrderType=="TaxItems"){
                            $bt = "T";
                            if($selected_company == 1){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbupl');
                            }
                        $full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "TAX".$fy.$full_tax_number;
                        }else {
                            $bt = "B";
                            if($selected_company == 1){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbupl');
                            }
                        $full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "BOS".$fy.$full_nontax_number;
                        }
                    }
                
                
                        $salesdata_new=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "BT"=>$bt,
                            "SalesID"=>$saleid,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "OrderID"=>$orderID,
                            "ChallanID"=>$data["number"],
                            "AccountID"=>$order_data->AccountID,
                            "gstno"=>$order_data->GSTNO,
                            "ItCount"=>$itemCount,
                            "cnfid"=>1,
                            "tcs"=>$tcsper,
                            "tcsAmt"=>$Y,
                            "PayType"=>"C",
                            "SaleAmt"=>$saleAmt,
                            "DiscAmt"=>$dis_amt,
                            "sgstamt"=>$sgstAmt,
                            "cgstamt"=>$cgstAmt,
                            "igstamt"=>$igstAmt,
                            "BillAmt"=>$BillAmtF,
                            "RndAmt"=>$RndAmt,
                            "UserID"=>$this->session->userdata('username'),
                        );
                    $this->db->insert(db_prefix() . 'salesmaster', $salesdata_new);
                    if($order_data->client->ActSalestype =="Sales"){
                        if($bt =="T"){
                            $this->challan_model->increment_next_tax_transaction_number();
                        }else{
                            $this->challan_model->increment_next_nontax_transaction_number();
                        }
                    }elseif($order_data->client->ActSalestype =="StockTransfer"){
                        $this->challan_model->increment_trn_transaction_number();
                    }elseif($order_data->client->ActSalestype =="CNF"){
                        $this->challan_model->increment_cnf_transaction_number();
                    }else{
                        if($bt =="T"){
                            $this->challan_model->increment_next_tax_transaction_number();
                        }else{
                            $this->challan_model->increment_next_nontax_transaction_number();
                        }
                    }
                // Update OrderMaster table
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('OrderID', $orderID);
                    $this->db->update(db_prefix() . 'ordermaster', [
                                        'ChallanID' => $data["number"],
                                        'SalesID'   => $saleid,
                                        'OrderAmt' => $BillAmtF,
                                    ]);
                // Update History table             
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);             
                    $this->db->where('OrderID', $orderID);
                    $this->db->update(db_prefix() . 'history', [
                                        'BillID' => $data["number"],
                                        'TransID'   => $saleid,
                                        'TransDate2' =>to_sql_date($data["date"]).' '.date('H:i:s'),
                                    ]);
                    $narration = "By SalesID ".$saleid."/".$data["number"]; 
                    $narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$data["number"];
                // Creates ledger insert    
                    if($order_data->Crates !=="0.00" || $order_data->Crates !== "0"){
                        $narration_create = "Against SalesID ".$saleid."/ ChallanID ".$ChallanID;
                
                        $create_ledgerdata = array(
                            "PlantID"=>$selected_company,
                            "VoucherID"=>$saleid,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "ChallanID"=>$data["number"],
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Qty"=>$order_AmtSum->crateSum,
                            "PassedFrom"=>"CHALLAN",
                            "Narration"=>$narration_create,
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                            "FY"=>$fy
                        );
                        $this->db->insert(db_prefix() . 'accountcrates', $create_ledgerdata);
                    }
                // Sale Ledger insert 
                        $ledgerdata_credit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"SALE",
                            "TType"=>"C",
                            "Amount"=>$saleAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
                // CGST,SGST and IGST ledger insert
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        $acct_name1 = "SGST";
                        $acct_name2 = "CGST";
                        $ledgerdata_credit_sgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name1,
                            "TType"=>"C",
                            "Amount"=>$sgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
                        $ledgerdata_credit_cgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name2,
                            "TType"=>"C",
                            "Amount"=>$cgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
                    }else{
                        $acct_name3 = "IGST";
                        $ledgerdata_credit_igst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name3,
                            "TType"=>"C",
                            "Amount"=>$igstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
                    }
                // Party account ledger insert
                        $ledgerdata_debit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Amount"=>$RndAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
                // TCS Ledger insert
                    if($round_variation >=0){
                        $rTType = "C";
                        $round_variation_new = abs($round_variation);
                    }else{
                        $rTType = "D";
                        $round_variation_new = abs($round_variation);
                    }
                        $ledgerdata_roundoff =array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"ROUNDOFF",
                            "TType"=>$rTType,
                            "Amount"=>$round_variation_new,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
                // TCS ledger insert
                    if($order_AmtSum->istcs=="1"){
                        $ledgerdata_tcs=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"TCS",
                            "TType"=>"C",
                            "Amount"=>$Y,
                            "Narration"=>$narration_tcs,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
                    }
                // get Account Balances
                    $acc_bal_data = $this->challan_model->get_acc_bal($order_data->AccountID);
                    $acc_bal_sale = $this->challan_model->get_acc_bal("SALE");
                    $acc_bal_tcs = $this->challan_model->get_acc_bal("TCS");
                    $acc_bal_igst = $this->challan_model->get_acc_bal("IGST");
                    $acc_bal_sgst = $this->challan_model->get_acc_bal("SGST");
                    $acc_bal_cgst = $this->challan_model->get_acc_bal("CGST");
                    $acc_bal_roundoff = $this->challan_model->get_acc_bal("ROUNDOFF");
                    
                    
                // Debit customer account balance
                    $current_bal = $acc_bal_data->$mm;
                    $current_bal_total = $current_bal + $RndAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('AccountID', $order_data->AccountID);
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $current_bal_total,
                                    ]);
                // Credit Sale Account balance
                    $current_bal_for_sale_act = $acc_bal_sale->$mm;
                    $total_bal_for_sale_act = $current_bal_for_sale_act - $saleAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('AccountID', "SALE");
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $total_bal_for_sale_act,
                                    ]);    
                // credit tcs account balance
                    if($get_account_detail->istcs=="1"){
                        $current_bal_for_tcs_act = $acc_bal_tcs->$mm;
                        $total_bal_for_tcs_act = $current_bal_for_tcs_act - $Y;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "TCS");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_tcs_act,
                                        ]);
                    }
                // credit Roundoff account balance
                        $current_bal_for_roundoff_act = $acc_bal_roundoff->$mm;
                        $total_bal_for_roundoff_act = $current_bal_for_roundoff_act - $round_variation;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "ROUNDOFF");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_roundoff_act,
                                        ]);
                // CGST,SGST and IGST balances update
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        // ADD SGST Amount
                        $sgst_bal_total = $acc_bal_sgst->$mm - $sgstAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "SGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $sgst_bal_total,
                                        ]);
                        // Add CGST Amount
                        $cgst_bal_total = $acc_bal_cgst->$mm - $cgstAmt;
                    
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "CGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $cgst_bal_total,
                                        ]);
                    }else {
                    // Add IGST Amount
                        $igst_bal_total = $acc_bal_igst->$mm - $igstAmt;
                            
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "IGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $igst_bal_total,
                                        ]);
                    }
                }else{
                    // Exiting order in Challan
                    // Balance revert and ledger delete for SALE 
                    $ledgerDetails = $this->challan_model->get_ledgerDetails($order_data->SalesID);
                    foreach ($ledgerDetails as $key => $value) {
                        $acc_bal_data = $this->challan_model->get_acc_bal($value["AccountID"]);
                        if($value['TType']=="C"){
                            $newBal = $acc_bal_data->$mmOld + $value['Amount'];
                        }else{
                            $newBal = $acc_bal_data->$mmOld - $value['Amount'];
                        }
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', $value["AccountID"]);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mmOld => $igst_bal_total,
                                        ]);
                    }
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('VoucherID', $order_data->SalesID);
                    $this->db->delete(db_prefix() . 'accountledger');
                    
                // update Sales Master table
                        $salesdataUpdate =array(
                            "tcsAmt"=>$Y,
                            "SaleAmt"=>$saleAmt,
                            "sgstamt"=>$sgstAmt,
                            "cgstamt"=>$cgstAmt,
                            "igstamt"=>$igstAmt,
                            "BillAmt"=>$BillAmtF,
                            "RndAmt"=>$RndAmt,
                            "UserID2"=>$this->session->userdata('username'),
                            "Lupdate"=>date('Y-m-d H:i:s')
                        );
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('SalesID', $order_data->SalesID);
                    $this->db->update(db_prefix() . 'salesmaster', $salesdataUpdate);
                    
                    // update Crates 
                    $getCratesDetails = $this->challan_model->getCratesDetails($order_data->SalesID);
                    $create_ledgerdata = array(
                            "Qty"=>$order_AmtSum->crateSum,
                            "UserID2"=>$this->session->userdata('username'),
                            "Lupdate"=>date('Y-m-d H:i:s')
                        );
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('VoucherID', $order_data->SalesID);
                    $this->db->update(db_prefix() . 'accountcrates', $create_ledgerdata);
                    
                    
                    $narration = "By SalesID ".$order_data->SalesID."/".$order_data->ChallanID; 
                    $narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$order_data->ChallanID;
                    
                // new Create ledger and update Account balance
                        $ledgerdata_credit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"SALE",
                            "TType"=>"C",
                            "Amount"=>$saleAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
                // CGST,SGST and IGST ledger insert
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        $acct_name1 = "SGST";
                        $acct_name2 = "CGST";
                        $ledgerdata_credit_sgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name1,
                            "TType"=>"C",
                            "Amount"=>$sgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
                        $ledgerdata_credit_cgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name2,
                            "TType"=>"C",
                            "Amount"=>$cgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
                    }else{
                        $acct_name3 = "IGST";
                        $ledgerdata_credit_igst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name3,
                            "TType"=>"C",
                            "Amount"=>$igstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
                    }
                // Party account ledger insert
                        $ledgerdata_debit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Amount"=>$RndAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
                // TCS Ledger insert
                    if($round_variation >=0){
                        $rTType = "C";
                        $round_variation_new = abs($round_variation);
                    }else{
                        $rTType = "D";
                        $round_variation_new = abs($round_variation);
                    }
                        $ledgerdata_roundoff =array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"ROUNDOFF",
                            "TType"=>$rTType,
                            "Amount"=>$round_variation_new,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
                // TCS ledger insert
                    if($order_AmtSum->istcs=="1"){
                        $ledgerdata_tcs=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"TCS",
                            "TType"=>"C",
                            "Amount"=>$Y,
                            "Narration"=>$narration_tcs,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
                    }
                // get Account Balances
                    $acc_bal_data = $this->challan_model->get_acc_bal($order_data->AccountID);
                    $acc_bal_sale = $this->challan_model->get_acc_bal("SALE");
                    $acc_bal_tcs = $this->challan_model->get_acc_bal("TCS");
                    $acc_bal_igst = $this->challan_model->get_acc_bal("IGST");
                    $acc_bal_sgst = $this->challan_model->get_acc_bal("SGST");
                    $acc_bal_cgst = $this->challan_model->get_acc_bal("CGST");
                    $acc_bal_roundoff = $this->challan_model->get_acc_bal("ROUNDOFF");
                    
                    
                // Debit customer account balance
                    $current_bal = $acc_bal_data->$mm;
                    $current_bal_total = $current_bal + $RndAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('AccountID', $order_data->AccountID);
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $current_bal_total,
                                    ]);
                // Credit Sale Account balance
                    $current_bal_for_sale_act = $acc_bal_sale->$mm;
                    $total_bal_for_sale_act = $current_bal_for_sale_act - $saleAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->LIKE('AccountID', "SALE");
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $total_bal_for_sale_act,
                                    ]);    
                // credit tcs account balance
                    if($get_account_detail->istcs=="1"){
                        $current_bal_for_tcs_act = $acc_bal_tcs->$mm;
                        $total_bal_for_tcs_act = $current_bal_for_tcs_act - $Y;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "TCS");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_tcs_act,
                                        ]);
                    }
                // credit Roundoff account balance
                        $current_bal_for_roundoff_act = $acc_bal_roundoff->$mm;
                        $total_bal_for_roundoff_act = $current_bal_for_roundoff_act - $round_variation;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "ROUNDOFF");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_roundoff_act,
                                        ]);
                // CGST,SGST and IGST balances update
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        // ADD SGST Amount
                        $sgst_bal_total = $acc_bal_sgst->$mm - $sgstAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "SGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $sgst_bal_total,
                                        ]);
                        // Add CGST Amount
                        $cgst_bal_total = $acc_bal_cgst->$mm - $cgstAmt;
                    
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "CGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $cgst_bal_total,
                                        ]);
                    }else {
                    // Add IGST Amount
                        $igst_bal_total = $acc_bal_igst->$mm - $igstAmt;
                            
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->LIKE('AccountID', "IGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $igst_bal_total,
                                        ]);
                    }
                }
            }
            
            $CHLUpdate2 = array(
                'VehicleID' =>$challan_vehicle,
                'DriverID' =>strtoupper($data["challan_driver"]),
                'LoaderID' =>strtoupper($data["challan_loader"]),
                'SalesmanID' =>strtoupper($data["challan_sales_man"])
            );
                
            $this->db->where('ChallanID', $data["number"]);
            $this->db->update(db_prefix() . 'challanmaster', $CHLUpdate2);
            
            if($this->db->affected_rows() > 0){
                set_alert('success', 'Updated challan Successfully');
            }  
                $redUrl = admin_url('challan/edit_challan/' . $id);
                redirect($redUrl);
            }else{
                    $redUrl = admin_url('challan/list_orders/');
                    redirect($redUrl);
            }
          }
        
        }
        
        close_setup_menu();
        
        if ($id == '') {
            $data['title']                = "Create Challan";
        }else {
            $data['title']                = "Update Challan";
            $data['challan']    = $this->challan_model->get($id);
            
        }
        
        
       
        $this->load->model('payment_modes_model');
        $data['invoiceid']            = $id;
        
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        /*echo "<pre>";
        print_r($data['vehicle']);
        die;*/
        //$data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/challan/edit_challan', $data);
    }
    
    public function edit_challan($id = '')
    {
        
        
        if (!has_permission_new('challan_list', '', 'view')) {
            access_denied('challan');
        }
        
        
        $redUrl = admin_url('challan/UpdateChallan/'.$id);
        redirect($redUrl);
        
        close_setup_menu();
        
        if ($id == '') {
            $data['title']                = "Create Challan";
        }else {
            $data['title']                = "Update Challan";
            $data['challan']    = $this->challan_model->get($id);
            
        }
        
        /*echo "<pre>";
        print_r($data['challan']);
        die;*/
       
        $this->load->model('payment_modes_model');
        $data['invoiceid']            = $id;
        
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/challan/edit_challan', $data);
    }

    /* List all invoices datatables */
    public function list_challan($id = '')
    {
        
        if ($this->input->post()) {
            $challan_data = $this->input->post();
        }
        close_setup_menu();
        
        if ($id == '') {
            $data['title']                = "Create Challan";
        }else {
            $data['title']                = "Update Challan";
            $data['challan']    = $this->challan_model->get($id);
        }
       
        $this->load->model('payment_modes_model');
        
        $data['invoiceid']            = $id;
        
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/challan/manage', $data);
    }
    
    /* List all invoices datatables */
    public function challanAddEdit($id = '')
    {
        
        if ($this->input->post()) {
            $challan_data = $this->input->post();
            $challan_data["route"] = $challan_data["challan_route"];
            $challan_data["vehicle"] = $challan_data["challan_vehicle"];
            unset($challan_data["challan_route"]);
            unset($challan_data["challan_vehicle"]);
           
            if (!has_permission('challan', '', 'create')) {
                    access_denied('challan');
                }
                
                $challan = $this->challan_model->checkorder($challan_data["order_id"]);
                
                if(empty($challan)){
                    $challan_data["challan_driver"] = strtoupper($challan_data["challan_driver"]);
                    $challan_data["challan_loader"] = strtoupper($challan_data["challan_loader"]);
                    $challan_data["challan_sales_man"] = strtoupper($challan_data["challan_sales_man"]);
                    $challan_data["vahicle_number"] = strtoupper($challan_data["vahicle_number"]);
                    $id = $this->challan_model->addNew($challan_data);
                        if ($id == false) {
                            set_alert('warning', 'Stock Not Available...');
                            $redUrl = admin_url('challan/challanAddEdit');
                            redirect($redUrl);
                        }else{
                            set_alert('success', _l('added_successfully', 'Challan'));
                            $redUrl = admin_url('challan/challan_list/');
                            redirect($redUrl);
                        }
                }else{
                        set_alert('warning', "Challan already created for this order");
                        redirect(admin_url('challan/challan_list'));
                }
        }
        close_setup_menu();
        
        if ($id == '') {
            $data['title']                = "Create Challan";
        }else {
            $data['title']                = "Update Challan";
            $data['challan']    = $this->challan_model->get($id);
        }
        $this->load->model('payment_modes_model');
        $data['invoiceid']            = $id;
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        $data['bodyclass']            = 'invoices-total-manual';
       /* echo "<pre>";
        print_r($data);
        die;*/
        $this->load->view('admin/challan/manageNew', $data);
    }
    
    public function UpdateChallan($id = '')
    {
        if (!has_permission_new('challan_list', '', 'view')) {
            access_denied('challan');
        }
        if ($this->input->post()) {
            if (!has_permission_new('challan_list', '', 'edit')) {
                access_denied('challan');
            }
            $selected_company = $this->session->userdata('root_company');
            if($selected_company == "1"){
                $GodownID = 'CSPL';
            }else if($selected_company == "2"){
                $GodownID = 'CFF';
            }else if($selected_company == "3"){
                $GodownID = 'CBUPL';
            }
            $fy = $this->session->userdata('finacial_year');
            $data = $this->input->post();
            
            $Oldmonth = substr($data["OldDate"],5,2);
            if($Oldmonth == "01"){
                $mOld = 11; 
            }
            if($Oldmonth == "02"){
                $mOld = 12; 
            }
            if($Oldmonth == "03"){
                $mOld = 13; 
            }
            if($Oldmonth == "04"){
                $mOld = 2; 
            }
            if($Oldmonth == "05"){
                $mOld = 3; 
            }
            if($Oldmonth == "06"){
                $mOld = 4; 
            }
            if($Oldmonth == "07"){
                $mOld = 5; 
            }
            if($Oldmonth == "08"){
                $mOld = 6; 
            }
            if($Oldmonth == "09"){
                $mOld = 7; 
            }
            if($Oldmonth == "10"){
                $mOld = 8; 
            }
            if($Oldmonth == "11"){
                $mOld = 9; 
            }
            if($Oldmonth == "12"){
                $mOld = 10; 
            }
            $mmOld = "BAL".$mOld;
            
            $month = substr(to_sql_date($data["date"]),5,2);
            if($month == "01"){
                $m = 11; 
            }
            if($month == "02"){
                $m = 12; 
            }
            if($month == "03"){
                $m = 13; 
            }
            if($month == "04"){
                $m = 2; 
            }
            if($month == "05"){
                $m = 3; 
            }
            if($month == "06"){
            $m = 4; 
            }
            if($month == "07"){
            $m = 5; 
            }
            if($month == "08"){
            $m = 6; 
            }
            if($month == "09"){
                $m = 7; 
            }
            if($month == "10"){
                $m = 8; 
            }
            if($month == "11"){
                $m = 9; 
            }
            if($month == "12"){
                $m = 10; 
            }
            $mm = "BAL".$m;    
            $order_ids = $data["order_id"];
            // Check order Amount is greter than accountBalance Amount
                foreach ($order_ids as $orderid1) {
                    $order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
                    $get_account_details = $this->challan_model->get_account_detailId($order_data1->AccountID);
                    if($get_account_details->bill_till_bal == "Y"){
                        $get_account_bal = $this->challan_model->get_account_balance($order_data1->AccountID);
                        $sum_bal = $get_account_bal->BAL1 + $get_account_bal->BAL2 + $get_account_bal->BAL3 + $get_account_bal->BAL4 + $get_account_bal->BAL5 + $get_account_bal->BAL6 + $get_account_bal->BAL7 + $get_account_bal->BAL8 + $get_account_bal->BAL9 + $get_account_bal->BAL10 + $get_account_bal->BAL11 + $get_account_bal->BAL12 + $get_account_bal->BAL13;
                        if($sum_bal >= 0){
                            set_alert('warning', "Challan Amt is greater than Balance Amt... ");
                            $redUrl = admin_url('challan/');
                            redirect($redUrl);
                        }else{
                            if(abs($sum_bal) >= $data['txtchalanvalue']){
                            $available = true;
                            }else{
                                set_alert('warning', "Challan Amt is greater than Balance Amt... ");
                                $redUrl = admin_url('challan/');
                                redirect($redUrl);
                                //$available = false;
                            }
                        }
                    }
                }
            $cancelOrder = array();
            $ChallanDetail = $this->challan_model->ChallanDetails($data["number"]);
            foreach ($ChallanDetail as $key=>$value) {
               if (in_array($value['OrderID'], $order_ids)){
                   
               }else{
                   array_push($cancelOrder,$value['OrderID']);
                   $get_ledger_details = $this->challan_model->get_ledger_data($value['SalesID']);
                        foreach ($get_ledger_details as $key_ledger => $value_ledger) {
                         # code...
                            $acc_bal_data = $this->challan_model->get_acc_bal($value_ledger["AccountID"]);
                            $current_bal = $acc_bal_data->$mmOld;
                            if($value_ledger["TType"] == "C"){
                                $current_amt = $current_bal + $value_ledger["Amount"];
                            }else{
                                $current_amt = $current_bal - $value_ledger["Amount"];
                            }
                            
                            $this->db->where('PlantID', $selected_company);
                            $this->db->LIKE('FY', $fy);
                            $this->db->where('AccountID', $value_ledger["AccountID"]);
                            $this->db->update(db_prefix() . 'accountbalances', [
                                                $mmOld => $current_amt,
                                            ]);
                       }
                       $OrderItem = $this->challan_model->get_OrderItem_data($value['OrderID']);
                        foreach ($OrderItem as $key1 => $value1) {
                            # code...
                            $stock_data = $this->challan_model->get_stock_item($value1["ItemID"]);
                            $item_stock= $stock_data->SQty;
                            $new_stock = $item_stock - $value1["BilledQty"];
                            // Stock update
                            $this->db->where('PlantID', $selected_company);
                            $this->db->where('FY', $fy);  
                            $this->db->where('GodownID',$GodownID);
                            $this->db->where('ItemID', $value1["ItemID"]);
                            $this->db->update(db_prefix() . 'stockmaster', [
                                                'SQty' => $new_stock,
                                            ]);
                        }
                        $this->db->where('OrderID', $value['OrderID']);
                        $this->db->update(db_prefix() . 'ordermaster', [
                                'OrderAmt' =>0.00,
                                'Crates' =>0,
                                'Cases' =>0,
                                'UserID2' =>$this->session->userdata('username'),
                                'Lupdate' =>date('Y-m-d H:i:s'),
                            ]);
                    // History Table Update
                        $this->db->where('OrderID', $value['OrderID']);
                        $this->db->update(db_prefix() . 'history', [
                                'BilledQty' =>0.00,
                                'OrderAmt' =>0.00,
                                'cgstamt' =>0.00,
                                'sgstamt' =>0.00,
                                'igstamt' =>0.00,
                                'ChallanAmt' =>0.00,
                                'NetOrderAmt' =>0.00,
                                'NetChallanAmt' =>0.00,
                                'UserID2' =>$this->session->userdata('username'),
                                'Lupdate' =>date('Y-m-d H:i:s'),
                            ]);
                    // Update SaleMaster
                        $this->db->where('SalesID', $value['SalesID']);
                        $this->db->update(db_prefix() . 'salesmaster', [
                                'SaleAmt' =>0.00,
                                'sgstamt' =>0.00,
                                'cgstamt' =>0.00,
                                'igstamt' =>0.00,
                                'BillAmt' =>0.00,
                                'RndAmt' =>0.00,
                                'tcsAmt' =>0.00,
                                'DiscAmt' =>0.00,
                                'UserID2' =>$this->session->userdata('username'),
                                'Lupdate' =>date('Y-m-d H:i:s'),
                            ]);
                    // Update Ledger
                        $this->db->where('VoucherID', $value['SalesID']);
                        $this->db->update(db_prefix() . 'accountledger', [
                                'Amount' =>0.00,
                            ]);
               }
            }
            /*echo '<pre>';
            print_r($get_ledger_details);
            die;*/
             
            if($data["number"]){
                $tcs_detail = $this->challan_model->get_tcsper();
                $tcsper = $tcs_detail[0]['tcs'];
            
            if($data["challan_vehicle"]=="TV"){
                $challan_vehicle = strtoupper($data["vahicle_number"]);
            }else{
                $challan_vehicle = $data["challan_vehicle"];
            }
        if($selected_company !== "1"){
                // Stock Check
            foreach ($order_ids as $orderid1) {
                $order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
                foreach ($order_data1->items as $key=>$value) {
                    $PQty = 0;
                    $PRQty = 0;
                    $IQty = 0;
                    $PRDQty = 0;
                    $SQty = 0;
                    $SRQty = 0;
                    $ADJQTY = 0;
                    $GIQTY = 0;
                    $GOQTY = 0;
                    $bal = $value["BilledQty"];
                    $balance = 0;
                    
                    $qty_name = 'qty_'.$orderid1.'_'.$value['ItemID'];
                    $qty = $data[$qty_name];
                    $packQty = $value['CaseQty'];
                    $orderQty = $packQty * $qty;
                    $checkStock = $this->challan_model->CheckStockQty($value['ItemID']);
                    $checkStockDetails = $this->challan_model->getStocksDetails($value['ItemID']);
                    
                    foreach ($checkStockDetails as $stock) {
                        if($stock['TType'] == 'P'){
                            $PQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'N'){
                            $PRQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'A'){
                            $IQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'B'){
                            $PRDQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){
                            $SQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){
                            $SRQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){
                            $ADJQTY += $stock['BilledQty'];
                        }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){
                            $ADJQTY += $stock['BilledQty'];
                        }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution'){
                            $ADJQTY += $stock['BilledQty'];
                        }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free distribution'){
                            $ADJQTY += $stock['BilledQty'];
                        }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){
                            $GIQTY += $stock['BilledQty'];
                        }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){
                            $GOQTY += $stock['BilledQty'];
                        }
                    }
                    $balance = (float) $bal + (float) $checkStock->OQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY  - (float) $GOQTY + (float) $GIQTY;
                    $balCase = $balance / $packQty;
                    if((float) $balCase < (float) $qty && $qty !== '0'){
                        //echo '<script>alert("Stock Not Avalable")</script>';
                        set_alert('warning', "Stock not available... ".$value['ItemID'].' -'.$balCase." -".$qty);
                            $redUrl = admin_url('challan/UpdateChallan/'.$data["number"]);
                            redirect($redUrl);
                    }
                }
            }
        }
            //die;
            $CHLUpdate = array(
                /*'RouteID'=>$data["challan_route"],*/
                'Crates'=>$data["txtCrates"],
                'Transdate'=>to_sql_date($data["date"]).' '.date('H:i:s'),
                'Cases'=>$data["txtCases"],
                'ChallanAmt'=>$data["txtchalanvalue"],
                'UserID2' =>$this->session->userdata('username'),
                'Lupdate' =>date('Y-m-d H:i:s'),
            );
            $this->db->where('ChallanID', $data["number"]);
            $this->db->update(db_prefix() . 'challanmaster', $CHLUpdate);
           $TotalChallanAmt = 0;
           $TotalChallanCR = 0;
           $TotalChallanCS = 0;
        // Order History table Update
        
            foreach ($order_ids as $orderid1) {
                $order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
                foreach ($order_data1->items as $key=>$value) {
                    if(is_null($value['BillID'])){
                        
                    }else{
                        $stock_data = $this->challan_model->get_stock_item($value["ItemID"]);
                        $item_stock= $stock_data->SQty;
                        $new_stock = $item_stock - $value['BilledQty'];
                    // Stock Revert
                    if($selected_company == "1"){
                        $GodownID = 'CSPL';
                    }else if($selected_company == "2"){
                        $GodownID = 'CFF';
                    }else if($selected_company == "3"){
                        $GodownID = 'CBUPL';
                    }
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy);  
                        $this->db->where('ItemID', $value["ItemID"]);
                        $this->db->where('GodownID',$GodownID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                            'SQty' => $new_stock,
                                        ]);
                    }
                    $qty_name = 'qty_'.$orderid1.'_'.$value['ItemID'];
                    $qty = $data[$qty_name];
                    $packQty = $value['CaseQty'];
                    $orderQty = $packQty * $qty;
                    
                    $BasicRate = $value['BasicRate'];
                    $OrderAmt = $BasicRate * $orderQty;
                    $CGSTAmt = '';
                    $SGSTAmt = '';
                    $IGSTAmt = '';
                    if($value['igst'] == "" || $value['igst'] == "0.00" || $value['igst'] == NULL){
                        $CGSTAmt = ($OrderAmt / 100 ) * $value['cgst'];
                        $SGSTAmt = ($OrderAmt / 100 ) * $value['sgst'];
                        $NetOrderAmt = $OrderAmt + $CGSTAmt + $SGSTAmt;
                    }else{
                        $IGSTAmt = ($OrderAmt / 100 ) * $value['igst'];
                        $NetOrderAmt = $OrderAmt + $IGSTAmt;
                    }
                    
                    $update_array = array(
                        'eOrderQty'=>$orderQty,
                        'TransDate2' =>to_sql_date($data["date"]).' '.date('H:i:s'),
                        'BilledQty'=>$orderQty,
                        'OrderAmt'=>$OrderAmt,
                        'ChallanAmt'=>$OrderAmt,
                        'cgstamt'=>$CGSTAmt,
                        'sgstamt'=>$SGSTAmt,
                        'igstamt'=>$IGSTAmt,
                        'NetOrderAmt'=>$NetOrderAmt,
                        'NetChallanAmt'=>$NetOrderAmt,
                        'UserID2'=>$this->session->userdata('username'),
                        'Lupdate'=>date('Y-m-d H:i:s'),
                    );
                //print_r($update_array);
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $fy);  
                    $this->db->where('OrderID', $orderid1);
                    $this->db->where('ItemID', $value['ItemID']);
                    $this->db->update(db_prefix() . 'history', $update_array);
                    
                        $stock_data = $this->challan_model->get_stock_item($value["ItemID"]);
                        $item_stock= $stock_data->SQty;
                        $new_stock = $item_stock + $orderQty;
                    // Stock Update
                    
                    if($selected_company == "1"){
                        $GodownID = 'CSPL';
                    }else if($selected_company == "2"){
                        $GodownID = 'CFF';
                    }else if($selected_company == "3"){
                        $GodownID = 'CBUPL';
                    }
        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy);  
                        $this->db->where('GodownID',$GodownID);
                        $this->db->where('ItemID', $value["ItemID"]);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                            'SQty' => $new_stock,
                                        ]);
                }
            }
            
            foreach ($order_ids as $orderID) {
                $order_data1 = $this->challan_model->getorderSum_by_orderId($orderID);
                if($order_data1->istcs == '1'){
                    $TcsAmt = ($order_data1->OrderSum /100) * $tcsper;
                }else{
                    $TcsAmt = 0;
                }
                $OrdAmt = $order_data1->OrderSum + $TcsAmt;
                
                $updateORD = array(
                    'OrderAmt'=>$OrdAmt,
                    'Crates'=>$order_data1->crateSum,
                    'Cases'=>$order_data1->casesSum,
                    'UserID2'=>$this->session->userdata('username'),
                    'Lupdate'=>date('Y-m-d H:i:s'),
                );
                
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $fy);  
                $this->db->where('OrderID', $orderID);
                $this->db->update(db_prefix() . 'ordermaster', $updateORD);
            }
            
            // Free Driver, Loader and SalesMan
        
            $getCHLDetails = $this->challan_model->get($data["number"]);
            //print_r($getCHLDetails);
            // Dreiver Free
           
            $updateDrv = array(
                    'EngageID'=>NULL
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $getCHLDetails->DriverID);  
                $this->db->where('SLDTypeID', '3');
                $this->db->update(db_prefix() . 'accountsld', $updateDrv);
            
            // Loader Free
            if($getCHLDetails->LoaderID !== ''){
                $updateLdr = array(
                    'EngageID'=>NULL
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $getCHLDetails->LoaderID);  
                $this->db->where('SLDTypeID', '2');
                $this->db->update(db_prefix() . 'accountsld', $updateLdr);
            }
            // SalesMan Free
            if($getCHLDetails->SalesmanID !== ''){
                
                $updateSls = array(
                    'EngageID'=>NULL
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $getCHLDetails->SalesmanID);  
                $this->db->where('SLDTypeID', '1');
                $this->db->update(db_prefix() . 'accountsld', $updateSls);
            }
        // Enggaged Driver , loader And SalesMan
            
            if($data['challan_vehicle'] !== "TV"){
                
                $updateDrv = array(
                    'EngageID'=>$challan_vehicle
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $data['challan_driver']);  
                $this->db->where('SLDTypeID', '3');
                $this->db->update(db_prefix() . 'accountsld', $updateDrv);
            }
            if($data["challan_loader"] !== ''){
                
                $updateLdr = array(
                    'EngageID'=>$challan_vehicle
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $data['challan_loader']);  
                $this->db->where('SLDTypeID', '2');
                $this->db->update(db_prefix() . 'accountsld', $updateLdr);
            }
            if($data["challan_sales_man"] !== ''){
                
                $updateSls = array(
                    'EngageID'=>$challan_vehicle
                );
                $this->db->where('PlantID', $selected_company);
                $this->db->where('AccountID', $data['challan_sales_man']);  
                $this->db->where('SLDTypeID', '1');
                $this->db->update(db_prefix() . 'accountsld', $updateSls);
            }
            
                
            foreach ($order_ids as $orderID) {
                $itemCount = 0;
                $order_data = $this->challan_model->getorderdetail_by_orderId($orderID);
                $itemCount = count($order_data->items);
                $order_AmtSum = $this->challan_model->getorderSum_by_orderId($orderID);
                
                $BillAmt = $order_AmtSum->OrderSum;
                $saleAmt = $order_AmtSum->SaleAmtSum;
                $cgstAmt = $order_AmtSum->cgstAmtSum;
                $sgstAmt = $order_AmtSum->sgstAmtSum;
                $igstAmt = $order_AmtSum->igstAmtSum;
                $roundup1 = 0;
                if($order_AmtSum->istcs == '1'){
                $Y = ($order_AmtSum->OrderSum /100) * $tcsper;
                }else{
                    $Y = 0;
                }
                $BillAmtF = $BillAmt + $Y;
                $RndAmt = round($BillAmtF);
                $roundup2 = $BillAmtF - $RndAmt;
                $round_variation = $roundup2 + $roundup1;
                
                $TotalChallanAmt += $BillAmtF;
                $TotalChallanCR += $order_AmtSum->crateSum;
                $TotalChallanCS += $order_AmtSum->casesSum;
                
                if(is_null($order_data->ChallanID) || $order_data->ChallanID == ''){
                   
                    if($order_data->client->ActSalestype =="Sales"){
                        if($order_data->OrderType=="TaxItems"){
                            $bt = "T";
                            if($selected_company == 1){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbupl');
                            }
                        $full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "TAX".$fy.$full_tax_number;
                        }else {
                            $bt = "B";
                            if($selected_company == 1){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbupl');
                            }
                        $full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "BOS".$fy.$full_nontax_number;
                        }
                    }elseif($order_data->client->ActSalestype =="StockTransfer"){
                        $bt = "M";
                        if($selected_company == 1){
                            $new_trnNumber = get_option('next_trn_number_for_cspl');
                        }elseif($selected_company == 2){
                            $new_trnNumber = get_option('next_trn_number_for_cff');
                        }elseif($selected_company == 3){
                            $new_trnNumber = get_option('next_trn_number_for_cbu');
                        }elseif($selected_company == 4){
                            $new_trnNumber = get_option('next_trn_number_for_cbupl');
                        }
                    $full_trnnumber = str_pad($new_trnNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    $saleid = "TRN".$fy.$full_trnnumber;
                    }elseif($order_data->client->ActSalestype =="CNF"){
                        $bt = "C";
                        if($selected_company == 1){
                            $new_cnfNumber = get_option('next_cnf_number_for_cspl');
                        }elseif($selected_company == 2){
                            $new_cnfNumber = get_option('next_cnf_number_for_cff');
                        }elseif($selected_company == 3){
                            $new_cnfNumber = get_option('next_cnf_number_for_cbu');
                        }elseif($selected_company == 4){
                            $new_cnfNumber = get_option('next_cnf_number_for_cbupl');
                        }
                    $full_cnfnumber = str_pad($new_cnfNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    $saleid = "CNF".$fy.$full_cnfnumber;
                    }else{
                        if($order_data->OrderType=="TaxItems"){
                            $bt = "T";
                            if($selected_company == 1){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbupl');
                            }
                        $full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "TAX".$fy.$full_tax_number;
                        }else {
                            $bt = "B";
                            if($selected_company == 1){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
                            }elseif($selected_company == 2){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
                            }elseif($selected_company == 3){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
                            }elseif($selected_company == 4){
                                $new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbupl');
                            }
                        $full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        $saleid = "BOS".$fy.$full_nontax_number;
                        }
                    }
                
                
                        $salesdata_new=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "BT"=>$bt,
                            "SalesID"=>$saleid,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "OrderID"=>$orderID,
                            "ChallanID"=>$data["number"],
                            "AccountID"=>$order_data->AccountID,
                            "gstno"=>$order_data->GSTNO,
                            "ItCount"=>$itemCount,
                            "cnfid"=>1,
                            "tcs"=>$tcsper,
                            "tcsAmt"=>$Y,
                            "PayType"=>"C",
                            "SaleAmt"=>$saleAmt,
                            "DiscAmt"=>$dis_amt,
                            "sgstamt"=>$sgstAmt,
                            "cgstamt"=>$cgstAmt,
                            "igstamt"=>$igstAmt,
                            "BillAmt"=>$BillAmtF,
                            "RndAmt"=>$RndAmt,
                            "UserID"=>$this->session->userdata('username'),
                        );
                    $this->db->insert(db_prefix() . 'salesmaster', $salesdata_new);
                    
                    
                    if($order_data->client->ActSalestype =="Sales"){
                        if($bt =="T"){
                            $this->challan_model->increment_next_tax_transaction_number();
                        }else{
                            $this->challan_model->increment_next_nontax_transaction_number();
                        }
                    }elseif($order_data->client->ActSalestype =="StockTransfer"){
                        $this->challan_model->increment_trn_transaction_number();
                    }elseif($order_data->client->ActSalestype =="CNF"){
                        $this->challan_model->increment_cnf_transaction_number();
                    }else{
                        if($bt =="T"){
                            $this->challan_model->increment_next_tax_transaction_number();
                        }else{
                            $this->challan_model->increment_next_nontax_transaction_number();
                        }
                    }
                // Update OrderMaster table
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('OrderID', $orderID);
                    $this->db->update(db_prefix() . 'ordermaster', [
                                        'ChallanID' => $data["number"],
                                        'SalesID'   => $saleid,
                                        'OrderAmt' => $BillAmtF,
                                    ]);
                // Update History table             
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);             
                    $this->db->where('OrderID', $orderID);
                    $this->db->update(db_prefix() . 'history', [
                                        'BillID' => $data["number"],
                                        'TransID'   => $saleid,
                                        'GodownID'   => $GodownID,
                                        'TransDate2' =>to_sql_date($data["date"]).' '.date('H:i:s'),
                                    ]);
                    $narration = "By SalesID ".$saleid."/".$data["number"]; 
                    $narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$data["number"];
                // Creates ledger insert    
                    if($order_data->Crates !=="0.00" || $order_data->Crates !== "0"){
                        $narration_create = "Against SalesID ".$saleid."/ ChallanID ".$ChallanID;
                
                        $create_ledgerdata = array(
                            "PlantID"=>$selected_company,
                            "VoucherID"=>$saleid,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "ChallanID"=>$data["number"],
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Qty"=>$order_AmtSum->crateSum,
                            "PassedFrom"=>"CHALLAN",
                            "Narration"=>$narration_create,
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                            "FY"=>$fy
                        );
                        $this->db->insert(db_prefix() . 'accountcrates', $create_ledgerdata);
                    }
                // Sale Ledger insert 
                        $ledgerdata_credit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"SALE",
                            "TType"=>"C",
                            "Amount"=>$saleAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
                // CGST,SGST and IGST ledger insert
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        $acct_name1 = "SGST";
                        $acct_name2 = "CGST";
                        $ledgerdata_credit_sgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name1,
                            "TType"=>"C",
                            "Amount"=>$sgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
                        $ledgerdata_credit_cgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name2,
                            "TType"=>"C",
                            "Amount"=>$cgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
                    }else{
                        $acct_name3 = "IGST";
                        $ledgerdata_credit_igst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$acct_name3,
                            "TType"=>"C",
                            "Amount"=>$igstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
                    }
                // Party account ledger insert
                        $ledgerdata_debit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Amount"=>$RndAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
                // TCS Ledger insert
                    if($round_variation >=0){
                        $rTType = "C";
                        $round_variation_new = abs($round_variation);
                    }else{
                        $rTType = "D";
                        $round_variation_new = abs($round_variation);
                    }
                        $ledgerdata_roundoff =array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"ROUNDOFF",
                            "TType"=>$rTType,
                            "Amount"=>$round_variation_new,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
                // TCS ledger insert
                    if($order_AmtSum->istcs=="1"){
                        $ledgerdata_tcs=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$saleid,
                            "AccountID"=>"TCS",
                            "TType"=>"C",
                            "Amount"=>$Y,
                            "Narration"=>$narration_tcs,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
                    }
                // get Account Balances
                    $acc_bal_data = $this->challan_model->get_acc_bal($order_data->AccountID);
                    $acc_bal_sale = $this->challan_model->get_acc_bal("SALE");
                    $acc_bal_tcs = $this->challan_model->get_acc_bal("TCS");
                    $acc_bal_igst = $this->challan_model->get_acc_bal("IGST");
                    $acc_bal_sgst = $this->challan_model->get_acc_bal("SGST");
                    $acc_bal_cgst = $this->challan_model->get_acc_bal("CGST");
                    $acc_bal_roundoff = $this->challan_model->get_acc_bal("ROUNDOFF");
                    
                    
                // Debit customer account balance
                    $current_bal = $acc_bal_data->$mm;
                    $current_bal_total = $current_bal + $RndAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('AccountID', $order_data->AccountID);
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $current_bal_total,
                                    ]);
                // Credit Sale Account balance
                    $current_bal_for_sale_act = $acc_bal_sale->$mm;
                    $total_bal_for_sale_act = $current_bal_for_sale_act - $saleAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('AccountID', "SALE");
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $total_bal_for_sale_act,
                                    ]);    
                // credit tcs account balance
                    if($get_account_detail->istcs=="1"){
                        $current_bal_for_tcs_act = $acc_bal_tcs->$mm;
                        $total_bal_for_tcs_act = $current_bal_for_tcs_act - $Y;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "TCS");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_tcs_act,
                                        ]);
                    }
                // credit Roundoff account balance
                        $current_bal_for_roundoff_act = $acc_bal_roundoff->$mm;
                        $total_bal_for_roundoff_act = $current_bal_for_roundoff_act - $round_variation;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "ROUNDOFF");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_roundoff_act,
                                        ]);
                // CGST,SGST and IGST balances update
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        // ADD SGST Amount
                        $sgst_bal_total = $acc_bal_sgst->$mm - $sgstAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "SGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $sgst_bal_total,
                                        ]);
                        // Add CGST Amount
                        $cgst_bal_total = $acc_bal_cgst->$mm - $cgstAmt;
                    
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "CGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $cgst_bal_total,
                                        ]);
                    }else {
                    // Add IGST Amount
                        $igst_bal_total = $acc_bal_igst->$mm - $igstAmt;
                            
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "IGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $igst_bal_total,
                                        ]);
                    }
                }else{
                    // Exiting order in Challan
                    // Balance revert and ledger delete for SALE 
                    $ledgerDetails = $this->challan_model->get_ledgerDetails($order_data->SalesID);
                    foreach ($ledgerDetails as $key => $value) {
                        $acc_bal_data = $this->challan_model->get_acc_bal($value["AccountID"]);
                        if($value['TType']=="C"){
                            $newBal = $acc_bal_data->$mmOld + $value['Amount'];
                        }else{
                            $newBal = $acc_bal_data->$mmOld - $value['Amount'];
                        }
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', $value["AccountID"]);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mmOld => $newBal,
                                        ]);
                    }
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('VoucherID', $order_data->SalesID);
                    $this->db->delete(db_prefix() . 'accountledger');
                    
                // update Sales Master table
                        $salesdataUpdate =array(
                            "tcsAmt"=>$Y,
                            "SaleAmt"=>$saleAmt,
                            "sgstamt"=>$sgstAmt,
                            "cgstamt"=>$cgstAmt,
                            "igstamt"=>$igstAmt,
                            "BillAmt"=>$BillAmtF,
                            "RndAmt"=>$RndAmt,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "UserID2"=>$this->session->userdata('username'),
                            "Lupdate"=>date('Y-m-d H:i:s')
                        );
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('SalesID', $order_data->SalesID);
                    $this->db->update(db_prefix() . 'salesmaster', $salesdataUpdate);
                    
                    // update Crates 
                    $getCratesDetails = $this->challan_model->getCratesDetails($order_data->SalesID);
                    $create_ledgerdata = array(
                            "Qty"=>$order_AmtSum->crateSum,
                            "UserID2"=>$this->session->userdata('username'),
                            "Lupdate"=>date('Y-m-d H:i:s')
                        );
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('VoucherID', $order_data->SalesID);
                    $this->db->update(db_prefix() . 'accountcrates', $create_ledgerdata);
                    
                    
                    $narration = "By SalesID ".$order_data->SalesID."/".$order_data->ChallanID; 
                    $narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$order_data->ChallanID;
                    
                // new Create ledger and update Account balance
                        $ledgerdata_credit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"SALE",
                            "TType"=>"C",
                            "Amount"=>$saleAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
                // CGST,SGST and IGST ledger insert
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        $acct_name1 = "SGST";
                        $acct_name2 = "CGST";
                        $ledgerdata_credit_sgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name1,
                            "TType"=>"C",
                            "Amount"=>$sgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
                        $ledgerdata_credit_cgst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name2,
                            "TType"=>"C",
                            "Amount"=>$cgstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
                    }else{
                        $acct_name3 = "IGST";
                        $ledgerdata_credit_igst=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$acct_name3,
                            "TType"=>"C",
                            "Amount"=>$igstAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
                    }
                // Party account ledger insert
                        $ledgerdata_debit=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>$order_data->AccountID,
                            "TType"=>"D",
                            "Amount"=>$RndAmt,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
                // TCS Ledger insert
                    if($round_variation >=0){
                        $rTType = "C";
                        $round_variation_new = abs($round_variation);
                    }else{
                        $rTType = "D";
                        $round_variation_new = abs($round_variation);
                    }
                        $ledgerdata_roundoff =array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"ROUNDOFF",
                            "TType"=>$rTType,
                            "Amount"=>$round_variation_new,
                            "Narration"=>$narration,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username')
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
                // TCS ledger insert
                    if($order_AmtSum->istcs=="1"){
                        $ledgerdata_tcs=array(
                            "PlantID"=>$selected_company,
                            "FY"=>$fy,
                            "Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
                            "TransDate2"=>date('Y-m-d H:i:s'),
                            "VoucherID"=>$order_data->SalesID,
                            "AccountID"=>"TCS",
                            "TType"=>"C",
                            "Amount"=>$Y,
                            "Narration"=>$narration_tcs,
                            "PassedFrom"=>"SALE",
                            "OrdinalNo"=>1,
                            "UserID"=>$this->session->userdata('username'),
                        );
                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
                    }
                // get Account Balances
                    $acc_bal_data = $this->challan_model->get_acc_bal($order_data->AccountID);
                    $acc_bal_sale = $this->challan_model->get_acc_bal("SALE");
                    $acc_bal_tcs = $this->challan_model->get_acc_bal("TCS");
                    $acc_bal_igst = $this->challan_model->get_acc_bal("IGST");
                    $acc_bal_sgst = $this->challan_model->get_acc_bal("SGST");
                    $acc_bal_cgst = $this->challan_model->get_acc_bal("CGST");
                    $acc_bal_roundoff = $this->challan_model->get_acc_bal("ROUNDOFF");
                    
                    
                // Debit customer account balance
                    $current_bal = $acc_bal_data->$mm;
                    $current_bal_total = $current_bal + $RndAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('AccountID', $order_data->AccountID);
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $current_bal_total,
                                    ]);
                // Credit Sale Account balance
                    $current_bal_for_sale_act = $acc_bal_sale->$mm;
                    $total_bal_for_sale_act = $current_bal_for_sale_act - $saleAmt;
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $fy);
                    $this->db->where('AccountID', "SALE");
                    $this->db->update(db_prefix() . 'accountbalances', [
                                        $mm => $total_bal_for_sale_act,
                                    ]);    
                // credit tcs account balance
                    if($get_account_detail->istcs=="1"){
                        $current_bal_for_tcs_act = $acc_bal_tcs->$mm;
                        $total_bal_for_tcs_act = $current_bal_for_tcs_act - $Y;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "TCS");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_tcs_act,
                                        ]);
                    }
                // credit Roundoff account balance
                        $current_bal_for_roundoff_act = $acc_bal_roundoff->$mm;
                        $total_bal_for_roundoff_act = $current_bal_for_roundoff_act - $round_variation;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "ROUNDOFF");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $total_bal_for_roundoff_act,
                                        ]);
                // CGST,SGST and IGST balances update
                    if($igstAmt == "0.00" || $igstAmt == ''){
                        // ADD SGST Amount
                        $sgst_bal_total = $acc_bal_sgst->$mm - $sgstAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "SGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $sgst_bal_total,
                                        ]);
                        // Add CGST Amount
                        $cgst_bal_total = $acc_bal_cgst->$mm - $cgstAmt;
                    
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "CGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $cgst_bal_total,
                                        ]);
                    }else {
                    // Add IGST Amount
                        $igst_bal_total = $acc_bal_igst->$mm - $igstAmt;
                            
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $fy);
                        $this->db->where('AccountID', "IGST");
                        $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $igst_bal_total,
                                        ]);
                    }
                }
            }
            
             $CHLUpdate2 = array(
                'ChallanAmt' =>$TotalChallanAmt,
                'Crates' =>$TotalChallanCR,
                'Cases' =>$TotalChallanCS,
                'VehicleID' =>$challan_vehicle,
                'DriverID' =>strtoupper($data["challan_driver"]),
                'LoaderID' =>strtoupper($data["challan_loader"]),
                'SalesmanID' =>strtoupper($data["challan_sales_man"])
            );
                
            $this->db->where('ChallanID', $data["number"]);
            $this->db->update(db_prefix() . 'challanmaster', $CHLUpdate2);
            if($this->db->affected_rows() > 0){
                set_alert('success', 'Updated challan Successfully');
            }  
            set_alert('success', 'Updated challan Successfully');
                $redUrl = admin_url('challan/UpdateChallan/'.$data["number"]);
                    redirect($redUrl);
            }
        }
        
        close_setup_menu();
        
        if ($id == '') {
            $data['title']                = "Create Challan";
        }else {
            $data['title']                = "Update Challan";
            $Order_item = array();
            $OrderIds = array();
            $AccountIds = array();
            // Existing order
            $data['challan']    = $this->challan_model->get($id);
            $challan    = $this->challan_model->getNew($id);
            foreach ($challan["item_list"] as $key => $code) {
                array_push($Order_item, $code["ItemID"]);
                array_push($OrderIds, $code["OrderID"]);
                array_push($AccountIds, $code["AccountID"]);
            }
            $get_order_list = $this->challan_model->get_order_by_routeNew($data['challan']->RouteID);
            foreach ($get_order_list["item_list"] as $key1 => $code1) {
                array_push($Order_item, $code1["ItemID"]);
                array_push($OrderIds, $code1["OrderID"]);
                array_push($AccountIds, $code1["AccountID"]);
            }
            $Order_item =  array_unique($Order_item);
            $OrderIds =  array_unique($OrderIds);
            $AccountIds =  array_unique($AccountIds);
        
            if(empty($Order_item)){
                
            }else{
                $get_item_rate = $this->challan_model->get_order_Item_rateNew($Order_item);
                $ItemSum = $this->challan_model->GetItemSum($OrderIds);
                $ItemStockDetails = $this->challan_model->GetStockDetails($Order_item);
            }
            $AccountBalances = $this->challan_model->GetAccountBalancec($AccountIds);
            $GetTcsPer = $this->challan_model->get_tcsperNew();
            $tcsPerValue = $GetTcsPer[0]['tcs'];
            
            $data['Curchallan']    = $challan;
            $data['ORDItem']    = $Order_item;
            $data['ItemRate']    = $get_item_rate;
            $data['TCSValue']    = $tcsPerValue;
            $data['AccountBalances']    = $AccountBalances;
            $data['get_order_list']    = $get_order_list;
            $data['AllItemSum']    = $ItemSum;
            $data['ItemStockDetails']    = $ItemStockDetails;
            
        }
       
        $this->load->model('payment_modes_model');
        $data['invoiceid']            = $id;
        
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/challan/manageNew', $data);
    }
    
    //------------------- Vehicle Detail -------------------------------
    public function get_vehicle_detail()
    {
        $id=$this->input->post('id'); 
        $vehicle_data = $this->challan_model->get_vehicle_detail($id);
        echo json_encode($vehicle_data);
    }
    
    public function update_rate()
    {
        
        $RCHID = $this->input->post('RCHID'); 
        $update_rate = $this->challan_model->update_rate($RCHID);
        echo json_encode($update_rate);
    }
    
    // New Code start
    
    
    public function get_order_by_routeNew()
    {
        $selected_company = $this->session->userdata('root_company');
        $id = $this->input->post('id'); 
        $Order_item = array();
        $OrderIds = array();
        $AccountIds = array();
        $get_order_list = $this->challan_model->get_order_by_routeNew($id);
        
        foreach ($get_order_list["item_list"] as $key1 => $code1) {
            array_push($Order_item, $code1["ItemID"]);
            array_push($OrderIds, $code1["OrderID"]);
        }
        
        foreach ($get_order_list["order_ids"] as $key2 => $code2) {
            array_push($AccountIds, $code2["AccountID"]);
        }
        
        $Order_item =  array_unique($Order_item);
        /*echo json_encode($get_order_list["order_ids"]);
        die;*/
        if(empty($Order_item)){
            
        }else{
            $get_item_rate = $this->challan_model->get_order_Item_rateNew($Order_item);
            if($selected_company !== "1"){
                $ItemStockDetails = $this->challan_model->GetStockDetails($Order_item);
            }
            $AllItemSum = $this->challan_model->GetItemSum($OrderIds);
            $AccountBalances = $this->challan_model->GetAccountBalancec($AccountIds);
        }
        /*echo json_encode($AccountBalances);
        die;*/
        $GetTcsPer = $this->challan_model->get_tcsperNew();
        $tcsPerValue = $GetTcsPer[0]['tcs'];
        /*echo json_encode($get_order_list["item_list"]);
        die;*/
        if($get_order_list["order_ids"]){
            
            $html = '';
            $html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;height: 400px;"><thead style="background: #438EB9;color: #FFF;">';
            $html .='<th class="col-id-no fixed-header">Tag</th>';
            $html .='<th class="col-id-ordid fixed-header">OrderNo</th>';
            $html .='<th class="col-id-custname fixed-header">AccountName</th>';
            $html .='<th class="col-id-custstate fixed-header">StateID</th>';
            $html .='<th class="col-id-ordtype fixed-header">Ordertype</th>';
            $html .='<th>SalesID</th>';
            $html .='<th>SalesDate</th>';
            foreach ($Order_item as $code) {
                $html .='<th width="5%">'.$code.'</th>';
            }
            $html .='<th>Crates</th>';
            $html .='<th>Cases</th>';
            $html .='<th>OrderAmt</th>';
            $html .='<th>SaleAmt</th>';
            $html .='<th>DiscAmt</th>';
            $html .='<th>CGSTAMT</th>';
            $html .='<th>SGSTAMT</th>';
            $html .='<th>IGSTAMT</th>';
            $html .='<th>TCSPer</th>';
            $html .='<th>TCSAmt</th>';
            $html .='<th>BillAmt</th>';
            $html .='</thead>';
            $challan_cases = 0;
            $challan_crate = 0;
            $challan_subtotal = 0;
            $challan_total = 0;
            $DiscAmtSum = 0;
            $CGSTAMTSum = 0;
            $SGSTAMTSum = 0;
            $IGSTAMTSum = 0;
            $html .='<tbody>';
            
            foreach ($get_order_list["order_ids"] as $key1 => $ids) {
            $html .='<tr>';
            //$order_data = $this->challan_model->getorderdetail_by_orderId($ids["OrderID"]);
            $html .='<td scope="row" class="col-id-no"><input type="checkbox" name="order_id[]" class="chk" value="'.$ids["OrderID"].'"><input type="hidden" name="OrderID" value="'.$ids["OrderID"].'"></td>';
            $BAL = 0;
            foreach ($AccountBalances as $BalKey => $BalVal) {
                if($ids["AccountID"] === $BalVal["AccountID"]){
                    $BAL = $BalVal["Balance"] - $ids["MaxCrdAmt"];
                }
            }
            $html .='<td scope="row" class="col-id-ordid"><input type="hidden" name="Balance" value="'.$BAL.'"><input type="hidden" name="MaxCrdAmt" value="'.$ids["MaxCrdAmt"].'">'.$ids["OrderID"].'</td>';
            
            $html .='<td scope="row" class="col-id-custname">'.$ids["company"].'</td>';
            $html .='<td scope="row" class="col-id-custstate">'.$ids["state"].'</td>';
            
            $html .='<td scope="row" class="col-id-ordtype">'.$ids["OrderType"].'</td>';
            if($ids["istcs"] == "1"){
                $tcs = $tcsPerValue;
            }else{
                $tcs = 0.00;
            }
            $html .='<td><input type="hidden" name="istcs" value="'.$tcs.'"></td>';
            
            $html .='<td></td>';
            $mm = 0;
            $OrderSaleAmt = 0;
            $OrderBillAmt = 0;
            $DiscAmt = 0; 
            $OSGST = 0; 
            $OCGST = 0; 
            $OIGST = 0; 
            foreach ($Order_item as $ItemIDc) {
                $isItem = '';
                foreach ($get_order_list["item_list"] as $key => $code) {
                    if($code["ItemID"] == $ItemIDc){
                            $matched = '';
                            
                            if($ids["OrderID"] == $code["OrderID"]){
                                $isItem = 1;
                                foreach ($get_item_rate as $key2 => $code2) {
                                    if($code2["item_id"]==$code["ItemID"] && $ids["state"] == $code2["state_id"] && $ids["DistributorType"]==$code2["distributor_id"] && $code["BasicRate"] !== $code2["assigned_rate"]){
                                        $matched= 'color:red;';
                                        $mm++;
                                        
                                    }
                                }
                                /*if($mm == 0){
                                    $his_rate = $this->challan_model->get_order_Item_rate_history($code["ItemID"]);
                                    if($his_rate->ItemID ==$code["ItemID"] &&  $ids["state"] == $his_rate->StateID && $ids["DistributorType"]==$his_rate->DistributorType && $code["BasicRate"] !== $his_rate->BasicRate){
                                        $matched= 'style="color:red"';
                                        $mm++;
                                    }
                                }*/
                                
                                $pack_qty = $code["CaseQty"];
                                $rate = $code["BasicRate"];
                                $gst = $code["cgst"] + $code["sgst"] + $code["igst"];
                                if($ids["state"] == "UP"){
                                    $cscr = $code["local_supply_in"];
                                }else{
                                    $cscr = $code["outst_supply_in"];
                                }
                                
                                $qty = $code["orderqty"] / $code["CaseQty"];
                                $OrderSaleAmt = $OrderSaleAmt + $code["OrderAmt"];
                                $OrderBillAmt += $code["NetOrderAmt"];
                                $DiscAmt += $code["DiscAmt"];
                                $OSGST += $code["sgstamt"];
                                $OCGST += $code["cgstamt"];
                                $OIGST += $code["igstamt"];
                                
                                //$html .='<td width="5%" align="right" '.$matched.'>'.$qty1.'</td>';
                            }
                    }
                }
                $balCase = 0;
                if($selected_company !== "1"){
                    $PQty = 0;
                    $PRQty = 0;
                    $IQty = 0;
                    $PRDQty = 0;
                    $SQty = 0;
                    $SRQty = 0;
                    $ADJQTY = 0;
                    $GIQTY = 0;
                    $GOQTY = 0;
                    foreach ($ItemStockDetails as $key => $value) {
                        if($value['ItemID'] == $ItemIDc){
                            $oQty = $value['OQty'];
                            $caseQty = $value['CaseQty'];
                            if($value['TType'] == 'P'){
                                $PQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'N'){
                                $PRQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'A'){
                                $IQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'B'){
                                $PRDQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'O' && $value['TType2'] == 'Order'){
                                $SQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'R' && $value['TType2'] == 'Fresh'){
                                $SRQty = $value['BilledQty'];
                            }elseif($value['TType'] == 'X' && $value['TType2'] == 'Stock Adjustment'){
                                $ADJQTY += $value['BilledQty'];
                            }elseif($value['TType'] == 'X' && $value['TType2'] == 'Promotional Activity'){
                                $ADJQTY += $value['BilledQty'];
                            }elseif($value['TType'] == 'X' && $value['TType2'] == 'Free Distribution'){
                                $ADJQTY += $value['BilledQty'];
                            }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){
                            $GIQTY += $stock['BilledQty'];
                            }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){
                                $GOQTY += $stock['BilledQty'];
                            }
                        }
                    }
                $balance = (float) $oQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY  - (float) $GOQTY +  - (float) $GIQTY;
                $balCase = $balance / $caseQty;
                }
                if($isItem == ""){
                    $html .='<td width="5%" align="right" ></td>';
                }else{
                    $html .='<td width="5%"><input type="hidden" value="'.$qty.'_'.$pack_qty.'_'.$rate.'_'.$gst.'_'.$cscr.'_'.$ids["state"].'_'.$balCase.'" id="qtyhidden"/><input type="hidden" id="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" name="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"/><input class= "QtyInput" style="width: 45px;'.$matched.'" type="text" onchange="total(this,'.$qty.')" name="qty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"></td>';
                }
            }
            
            $html .='<td style="text-align: right;">'.$ids["Crates"];
            if($mm > 0){
                $html .='<input type="hidden" name="rate_change" id="rate_change" value="Y">';
            }
            $html .='</td>';
            $challan_crate = $challan_crate + $ids["Crates"];
            $html .='<td style="text-align: right;">'.$ids["Cases"].'</td>';
            $challan_cases = $challan_cases + $ids["Cases"];
            // bill Amt
            $html .='<td style="text-align: right;">'.$OrderBillAmt.' </td>';
            $challan_total = $challan_total + $OrderBillAmt;
            //sale Amt
            $html .='<td style="text-align: right;">'.$OrderSaleAmt.'</td>';
            $challan_subtotal = $challan_subtotal + $OrderSaleAmt;
            // Disc Amt
            $html .='<td style="text-align: right;">'.$DiscAmt.'</td>';
            $DiscAmtSum = $DiscAmtSum + $DiscAmt;
            // CGST Amt
            $html .='<td style="text-align: right;">'.$OCGST.'</td>';
            $CGSTAMTSum = $CGSTAMTSum + $OCGST;
            // SGST Amt
            $html .='<td style="text-align: right;">'.$OSGST.'</td>';
            $SGSTAMTSum = $SGSTAMTSum + $OSGST;
            // IGST Amt
            $html .='<td style="text-align: right;">'.$OIGST.'</td>';
            $IGSTAMTSum = $IGSTAMTSum + $OIGST;
            // TCS Amt
            $html .='<td style="text-align: right;"><input type="hidden" name="tcsper" value="'.$tcs.'">'.$tcs.'</td>';
            if($tcs !=="0.00"){
                $tcsAmt = ($OrderBillAmt / 100) * $tcs;
            }else{
                $tcsAmt = 0.00;
            }
            
            $html .='<td style="text-align: right;">'.round($tcsAmt,2).'</td>';
            // Bill Amt Include TCSAMT
            $finalBillAmt = $OrderBillAmt + $tcsAmt;
            $html .='<td style="text-align: right;">'.round($finalBillAmt,2).'<input type="hidden" name="FBilAmt" id="FBilAmt" value="'.$finalBillAmt.'"></td>';
            $html .='</tr>';
        }
        
        $html .='<tfoot><tr>';
        
        $html .='<td style="text-align:center; scope="row" class="col-id-no"">Total</td>
        <td scope="row" class="col-id-ordid"></td>
        <td scope="row" class="col-id-custname"></td>
        <td scope="row" class="col-id-custstate"></td>
			                    <td scope="row" class="col-id-ordtype"></td>
        <td></td><td></td>';
       
		foreach ($Order_item as $ItemIDc) {
            foreach ($AllItemSum as $keys => $values) {
                if($ItemIDc == $values['ItemID']){
                    $ItemSum = $values['OrderQty'] / $values['CaseQty'];
                        $html .='<td style="text-align: right;">'.$ItemSum.'</td>';
                }
            }                  
        }
			                   
        $html .='<td style="text-align: right;">'.$challan_crate.'</td>';
            $html .='<td style="text-align: right;">'.$challan_cases.'</td>';
            $html .='<td style="text-align: right;">'.$challan_total.'</td>';
            $html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';
            $html .='<td style="text-align: right;">'.$DiscAmtSum.'</td>';
            $html .='<td style="text-align: right;">'.$CGSTAMTSum.'</td>';
            $html .='<td style="text-align: right;">'.$SGSTAMTSum.'</td>';
            $html .='<td style="text-align: right;">'.$IGSTAMTSum.'</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">'.$challan_total.'</td>';
        $html .='</tr></tfoot>';
        
            $html .='</tbody>';
            $html .='</table>';
        }else{
            $html = '<p style="color:red;">No data found...</p>';
        }
        
        echo json_encode($html);
    }
    // New Code End 
    public function get_order_by_route2()
    {
        $selected_company = $this->session->userdata('root_company');
        $id = $this->input->post('id'); 
        $Order_item = array();
        
        $get_order_list = $this->challan_model->get_order_by_route($id);
       
        foreach ($get_order_list["item_list"] as $key1 => $code1) {
                array_push($Order_item, $code1["ItemID"]);
            }
        if(empty($Order_item)){
            
        }else{
            $get_item_rate = $this->challan_model->get_order_Item_rate($Order_item);
        }
        //echo json_encode($get_item_rate);
        if($get_order_list["order_ids"]){
            
            $html = '';
            $html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';
            $html .='<th>Tag</th>';
            $html .='<th>OrderNo</th>';
            $html .='<th>AccountName</th>';
            $html .='<th>StateID</th>';
            $html .='<th>Ordertype</th>';
            $html .='<th>SalesID</th>';
            $html .='<th>SalesDate</th>';
            foreach ($get_order_list["item_list"] as $key => $code) {
                $html .='<th width="5%">'.$code["ItemID"].'</th>';
            }
            $html .='<th>Crates</th>';
            $html .='<th>Cases</th>';
            $html .='<th>OrderAmt</th>';
            $html .='<th>SaleAmt</th>';
            $html .='<th>TCSPer</th>';
            $html .='<th>TCSAmt</th>';
            $html .='</thead>';
            $challan_cases = 0;
            $challan_crate = 0;
            $challan_subtotal = 0;
            $challan_total = 0;
            $html .='<tbody>';
            
            foreach ($get_order_list["order_ids"] as $key1 => $ids) {
            $html .='<tr>';
            //$order_data = $this->challan_model->getorderdetail_by_orderId($ids["OrderID"]);
            $html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids["OrderID"].'"><input type="hidden" name="OrderID" value="'.$ids["OrderID"].'"></td>';
            $html .='<td>'.$ids["OrderID"].'</td>';
            //$account_name = get_account_name($order_data->AccountID,$selected_company);
            $html .='<td>'.$ids["company"].'</td>';
            $html .='<td>'.$ids["state"].'</td>';
            
            $html .='<td>'.$ids["OrderType"].'</td>';
            $html .='<td></td>';
            
            $html .='<td></td>';
            $mm = 0;
            
            foreach ($get_order_list["item_list"] as $key => $code) {
                $matched = '';
                if($ids["OrderID"] == $code["OrderID"]){
                    foreach ($get_item_rate as $key2 => $code2) {
                        if($code2["item_id"]==$code["ItemID"] && $ids["state"] == $code2["state_id"] && $ids["DistributorType"]==$code2["distributor_id"] && $code["BasicRate"] !== $code2["assigned_rate"]){
                            $matched= 'style="color:red"';
                            $mm++;
                            
                        }
                    }
                    if($mm == 0){
                        $his_rate = $this->challan_model->get_order_Item_rate_history($code["ItemID"]);
                        if($his_rate->ItemID ==$code["ItemID"] &&  $ids["state"] == $his_rate->StateID && $ids["DistributorType"]==$his_rate->DistributorType && $code["BasicRate"] !== $his_rate->BasicRate){
                            $matched= 'style="color:red"';
                            $mm++;
                        }
                    }
                    $qty1 = $code["orderqty"] / $code["CaseQty"];
                    $html .='<td width="5%" align="right" '.$matched.'>'.$qty1.'</td>';
                }else{
                    $html .='<td></td>';
                }
                //$item_data1 = $this->challan_model->get_order_singleitem($ids["OrderID"],$code["ItemID"]);
                /*if($item_data1){
                    if(is_null($item_data1->eOrderQty)){
                        $qty1 = $item_data1->OrderQty / $item_data1->CaseQty;
                    }else{
                        $qty1 = $item_data1->eOrderQty / $item_data1->CaseQty;
                    }
                    
                    $html .='<td width="5%">'.$qty1.'</td>';
                }else{
                    
                    $html .='<td></td>';
                }*/
                
                
            }
            
            $html .='<td style="text-align: right;">'.$ids["Crates"];
            if($mm > 0){
                $html .='<input type="hidden" name="rate_change" id="rate_change" value="Y">';
            }
            $html .='</td>';
            $challan_crate = $challan_crate + $ids["Crates"];
            $html .='<td style="text-align: right;">'.$ids["Cases"].'</td>';
            $challan_cases = $challan_cases + $ids["Cases"];
            $html .='<td style="text-align: right;">'.$ids["OrderAmt"].'</td>';
            $challan_subtotal = $challan_subtotal + $ids["OrderAmt"];
            $html .='<td style="text-align: right;"></td>';
            $challan_total = $challan_total + $ids["OrderAmt"];
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='</tr>';
        }
        
        $html .='<tfoot><tr>';
        
        $html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';
        foreach ($get_order_list["item_list"] as $key => $code1) {
            
                $item_count = $this->challan_model->get_itemcout_all_order($id,$code1["ItemID"]);
            
            $item_count_new = (int) $item_count->OrderQty;
            $html .='<td style="text-align: right;">'.$item_count_new.'</td>';
        }
        $html .='<td style="text-align: right;">'.$challan_crate.'</td>';
            $html .='<td style="text-align: right;">'.$challan_cases.'</td>';
            $html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';
            $html .='<td style="text-align: right;">'.$challan_total.'</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
        $html .='</tr></tfoot>';
        
            $html .='</tbody>';
            $html .='</table>';
        }
        
        echo json_encode($html);
    }
    
    //------------------- List of Order By route-------------------------------
    public function get_order_by_route()
    {
        $selected_company = $this->session->userdata('root_company');
        $id = $this->input->post('id'); 
        
        $get_acc_by_route = $this->challan_model->get_acc_by_route($id);
        
        $order_ids = array();
        $account_ids = array();
       
        
        foreach ($get_acc_by_route as $key => $value) {
            
            array_push($account_ids,$value['AccountID']);
        }
        $order_ids_details = $this->challan_model->getorderlist_by_accId($account_ids);
        
        foreach ($order_ids_details as $key1 => $value1) {
            
            array_push($order_ids,$value1['OrderID']);
        }
        
        
        if($order_ids){
        $item_code_list_new = array();
        
        
        
        $item_code_list = $this->challan_model->get_item_code_list_by_order_ids($order_ids);
        
        foreach ($item_code_list as $key2 => $value2) {
            
            array_push($item_code_list_new,$value2['ItemID']);
        }
        
        $item_code_list_new_unique = array_unique($item_code_list_new);
        
        $html = '';
        $html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';
        $html .='<th>Tag</th>';
        $html .='<th>OrderNo</th>';
        $html .='<th>AccountName</th>';
        $html .='<th>StateID</th>';
        $html .='<th>Ordertype</th>';
        $html .='<th>SalesID</th>';
        $html .='<th>SalesDate</th>';
        foreach ($item_code_list_new_unique as $code) {
            $html .='<th width="5%">'.$code.'</th>';
        }
        $html .='<th>Crates</th>';
        $html .='<th>Cases</th>';
        $html .='<th>OrderAmt</th>';
        $html .='<th>SaleAmt</th>';
        $html .='<th>TCSPer</th>';
        $html .='<th>TCSAmt</th>';
        $html .='</thead>';
        $challan_cases = 0;
        $challan_crate = 0;
        $challan_subtotal = 0;
        $challan_total = 0;
        $html .='<tbody>';
        foreach ($order_ids as $ids) {
            $html .='<tr>';
            $order_data = $this->challan_model->getorderdetail_by_orderId($ids);
            $html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids.'"></td>';
            $html .='<td>'.$ids.'</td>';
            $account_name = get_account_name($order_data->AccountID,$selected_company);
            $html .='<td>'.$account_name->company.'</td>';
            $html .='<td>'.$order_data->client->state.'</td>';
            $html .='<td>'.$order_data->OrderType.'</td>';
            $html .='<td></td>';
            
            $html .='<td></td>';
            
            foreach ($item_code_list_new_unique as $code) {
                $item_data1 = $this->challan_model->get_order_singleitem($ids,$code);
                if($item_data1){
                    if(is_null($item_data1->eOrderQty)){
                        $qty1 = $item_data1->OrderQty / $item_data1->CaseQty;
                    }else{
                        $qty1 = $item_data1->eOrderQty / $item_data1->CaseQty;
                    }
                    
                    $html .='<td width="5%"><input style="width: 50px;" type="text" name="qty" value="'.$qty1.'"></td>';
                }else{
                    
                    $html .='<td></td>';
                }
                
            }
            
            $html .='<td style="text-align: right;">'.$order_data->Crates.'</td>';
            $challan_crate = $challan_crate + $order_data->Crates;
            $html .='<td style="text-align: right;">'.$order_data->Cases.'</td>';
            $challan_cases = $challan_cases + $order_data->Cases;
            $html .='<td style="text-align: right;">'.$order_data->OrderAmt.'</td>';
            $challan_subtotal = $challan_subtotal + $order_data->OrderAmt;
            $html .='<td style="text-align: right;"></td>';
            $challan_total = $challan_total + $order_data->OrderAmt;
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='</tr>';
        }
        
        $html .='<tfoot><tr>';
        
        $html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';
        foreach ($item_code_list_new_unique as $code) {
            
                $item_count = $this->challan_model->get_itemcout_all_order($order_ids,$code);
            
            $item_count_new = (int) $item_count;
            $html .='<td style="text-align: right;">'.$item_count_new.'</td>';
        }
        $html .='<td style="text-align: right;">'.$challan_crate.'</td>';
            $html .='<td style="text-align: right;">'.$challan_cases.'</td>';
            $html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';
            $html .='<td style="text-align: right;">'.$challan_total.'</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
        $html .='</tr></tfoot>';
        
            
        $html .='</tbody>';
        $html .='</table>';
        
        /*echo json_encode($html);
        die;*/
        /*//
        $html = '';
        $html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';
        $html .='<th>Tag</th>';
        $html .='<th>OrderNo</th>';
        $html .='<th>AccountName</th>';
        $html .='<th>StateID</th>';
        $html .='<th>Ordertype</th>';
        $html .='<th>SalesID</th>';
        $html .='<th>SalesDate</th>';
        foreach ($item_code_list_new_unique as $code) {
            $html .='<th width="5%">'.$code.'</th>';
        }
        $html .='<th>Crates</th>';
        $html .='<th>Cases</th>';
        $html .='<th>OrderAmt</th>';
        $html .='<th>SaleAmt</th>';
        $html .='<th>TCSPer</th>';
        $html .='<th>TCSAmt</th>';
        $html .='</thead>';
        $challan_cases = 0;
        $challan_crate = 0;
        $challan_subtotal = 0;
        $challan_total = 0;
        
       $html .='<tbody>';
       
      
        foreach ($order_ids as $ids) {
            $html .='<tr>';
            $order_data = $this->challan_model->getorderdetail_by_orderId($ids);
            $html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids.'"></td>';
            $html .='<td>'.$ids.'</td>';
            $account_name = get_account_name($order_data->AccountID,$selected_company);
            $html .='<td>'.$account_name->company.'</td>';
            $html .='<td>'.get_state_code($order_data->client->billing_state).'</td>';
            $html .='<td>'.$order_data->OrderType.'</td>';
            $html .='<td></td>';
            
            $html .='<td></td>';
            
            foreach ($item_code_list_new_unique as $code) {
                $item_data1 = $this->challan_model->get_order_singleitem($ids,$code);
                
                if($item_data1){
                    
                  $html .='<td width="5%"><input style="width: 50px;" type="text" onchange="total()" name="qty_'.$ids.'_'.$item_data1->ItemID.'" value="'.$item_data1->OrderQty / $item_data1->CaseQty.'"></td>';
                  
                }else {
                   
                    $html .='<td width="5%"><input style="width: 50px;" type="text" onchange="total()" name="qty_'.$ids.'_'.$item_data1->ItemID.'" value="0"></td>';
                }
            }
            
            $html .='<td style="text-align: right;">'.$order_data->Crates.'</td>';
            $challan_crate = $challan_crate + $order_data->Crates;
            $html .='<td style="text-align: right;">'.$order_data->Cases.'</td>';
            $challan_cases = $challan_cases + $order_data->Cases;
            $html .='<td style="text-align: right;">'.$order_data->subtotal.'</td>';
            $challan_subtotal = $challan_subtotal + $order_data->subtotal;
            $html .='<td style="text-align: right;">'.$order_data->OrderAmt.'</td>';
            $challan_total = $challan_total + $order_data->OrderAmt;
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='</tr>';
        }
        $html .='</tbody>';
        
        
        $html .='<tfoot><tr>';
        
        $html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';
        foreach ($item_code_list as $code) {
            
                $item_count = $this->challan_model->get_itemcout_all_order($order_ids,$code);
            
            $item_count_new = (int) $item_count;
            $html .='<td style="text-align: right;">'.$item_count_new.'</td>';
        }
        $html .='<td style="text-align: right;">'.$challan_crate.'</td>';
            $html .='<td style="text-align: right;">'.$challan_cases.'</td>';
            $html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';
            $html .='<td style="text-align: right;">'.$challan_total.'</td>';
            $html .='<td style="text-align: right;">0</td>';
            $html .='<td style="text-align: right;">0</td>';
        $html .='</tr></tfoot>';
        $html .='</table>';*/
        }else {
            $html = '<h3 style="color:#fc0a0b;">No Record Found...</h3>';
        }
        
        
    echo json_encode($html);
    die;
    }
    
    /* List all Gatepass datatables */
    public function view_gatepass($id = '')
    {
        
        close_setup_menu();

        
        $data['title']                = "View Gatepass";
        
        $data['bodyclass']            = 'invoices-total-manual';
        $this->load->view('admin/gatepass/manage', $data);
    }
    
    public function gatepass_list()
    {
        if (!has_permission_new('gatepass', '', 'view')) {
            ajax_access_denied();
        }
        if ($this->input->is_ajax_request()) {
			if($this->input->post()){
        $this->app->get_table_data('gatepass');
			}
        }
    }

    /* List all recurring invoices */
    public function recurring($id = '')
    {
        

        close_setup_menu();

        $data['invoiceid']            = $id;
        $data['title']                = _l('invoices_list_recurring');
        $data['invoices_years']       = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $this->load->view('admin/invoices/recurring/list', $data);
    }

    public function table($clientid = '')
    {
        

        $this->app->get_table_data(($this->input->get('recurring') ? 'recurring_invoices' : 'challan'), [
            'clientid' => $clientid,
            'data'     => $data,
        ]);
    }

    public function client_change_data($customer_id, $current_invoice = '')
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('projects_model');
            $this->load->model('invoice_items_model');
            $data                     = [];
            $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
            $data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);
            $data['client_details']  = $this->clients_model->get($customer_id);
            $client_item_div = unserialize($data['client_details']->itemdivision);
            $client_item_div2 = implode(" ",$client_item_div);
            $data['client_details']->itemdivision = $client_item_div2;
            //$data['division'] = $client_item_div;
            $data['item_data'] = $this->invoice_items_model->get2($client_item_div);
            $data['customer_groups'] = $this->clients_model->get_customer_groups($customer_id);
            $data['customer_groups_name'] = $this->clients_model->get_customer_groups_name($data['customer_groups']['0']['groupid']);
            $data['customer_has_projects'] = customer_has_projects($customer_id);
            $data['billable_tasks']        = $this->tasks_model->get_billable_tasks($customer_id);

            if ($current_invoice != '') {
                $this->db->select('status');
                $this->db->where('id', $current_invoice);
                $current_invoice_status = $this->db->get(db_prefix() . 'invoices')->row()->status;
            }

            $_data['invoices_to_merge'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice) : [];

            $data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

            $this->load->model('currencies_model');

            $__data['expenses_to_bill'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->get_expenses_to_bill($customer_id) : [];

            $data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);
            echo json_encode($data);
        }
    }

    

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_invoice($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_invoice($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'invoice');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function pause_overdue_reminders($id)
    {
        if (has_permission('challan', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);
        }
        redirect(admin_url('order/list_orders/' . $id));
    }

    public function resume_overdue_reminders($id)
    {
        if (has_permission('challan', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);
        }
        redirect(admin_url('order/list_orders/' . $id));
    }

    public function mark_as_cancelled($id)
    {
        if (!has_permission('challan', '', 'edit') && !has_permission('challan', '', 'create')) {
            access_denied('invoices');
        }

        $success = $this->invoices_model->mark_as_cancelled($id);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
        }

        redirect(admin_url('order/list_orders/' . $id));
    }

    public function unmark_as_cancelled($id)
    {
        if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->unmark_as_cancelled($id);
        if ($success) {
            set_alert('success', _l('invoice_unmarked_as_cancelled'));
        }
        redirect(admin_url('order/list_orders/' . $id));
    }

    public function copy($id)
    {
        if (!$id) {
            redirect(admin_url('invoices'));
        }
        if (!has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        $new_id = $this->invoices_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('invoice_copy_success'));
            redirect(admin_url('invoices/invoice/' . $new_id));
        } else {
            set_alert('success', _l('invoice_copy_fail'));
        }
        redirect(admin_url('invoices/invoice/' . $id));
    }

    public function get_merge_data($id)
    {
        $invoice = $this->invoices_model->get($id);
        $cf      = get_custom_fields('items');

        $i = 0;

        foreach ($invoice->items as $item) {
            $invoice->items[$i]['taxname']          = get_invoice_item_taxes($item['id']);
            $invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);
            $this->db->where('item_id', $item['id']);
            $rel              = $this->db->get(db_prefix() . 'related_items')->result_array();
            $item_related_val = '';
            $rel_type         = '';
            foreach ($rel as $item_related) {
                $rel_type = $item_related['rel_type'];
                $item_related_val .= $item_related['rel_id'] . ',';
            }
            if ($item_related_val != '') {
                $item_related_val = substr($item_related_val, 0, -1);
            }
            $invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;
            $invoice->items[$i]['rel_type']                         = $rel_type;

            $invoice->items[$i]['custom_fields'] = [];

            foreach ($cf as $custom_field) {
                $custom_field['value']                 = get_custom_field_value($item['id'], $custom_field['id'], 'items');
                $invoice->items[$i]['custom_fields'][] = $custom_field;
            }
            $i++;
        }
        echo json_encode($invoice);
    }

    public function get_bill_expense_data($id)
    {
        $this->load->model('expenses_model');
        $expense = $this->expenses_model->get($id);

        $expense->qty              = 1;
        $expense->long_description = clear_textarea_breaks($expense->description);
        $expense->description      = $expense->name;
        $expense->rate             = $expense->amount;
        if ($expense->tax != 0) {
            $expense->taxname = [];
            array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
        }
        if ($expense->tax2 != 0) {
            array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
        }
        echo json_encode($expense);
    }
    
    public function challan($id = '')
    {
        $redUrl = admin_url('challan/challanAddEdit');
        redirect($redUrl);
        /*if ($this->input->post()) {
            
            $challan_data = $this->input->post();
            $challan_data["route"] = $challan_data["challan_route"];
            $challan_data["vehicle"] = $challan_data["challan_vehicle"];
            unset($challan_data["challan_route"]);
            unset($challan_data["challan_vehicle"]);
           
            if (!has_permission('challan', '', 'create')) {
                    access_denied('challan');
                }
                
                $challan = $this->challan_model->checkorder($challan_data["order_id"]);
                
                if(empty($challan)){
                    $challan_data["challan_driver"] = strtoupper($challan_data["challan_driver"]);
                    $challan_data["challan_loader"] = strtoupper($challan_data["challan_loader"]);
                    $challan_data["challan_sales_man"] = strtoupper($challan_data["challan_sales_man"]);
                    $challan_data["vahicle_number"] = strtoupper($challan_data["vahicle_number"]);
                    $id = $this->challan_model->add($challan_data);
                        if ($id == false) {
                            set_alert('warning', 'Stock Not Available...');
                            $redUrl = admin_url('challan');
                            redirect($redUrl);
                        }else{
                            set_alert('success', _l('added_successfully', 'Challan'));
                            $redUrl = admin_url('challan/challan_list/');
                            redirect($redUrl);
                        }
                }else{
                    
                    set_alert('warning', "Challan already created for this order");
                    redirect(admin_url('challan/challan_list'));
                }
                
                
        }
        
        $data['order']        = $this->order_model->get2($id);
        $data['routes']    = $this->clients_model->getroute();
        $data['vehicle']    = $this->clients_model->getvehicle();
        $data['title']     = "Challan";
        $data['bodyclass'] = 'invoice';
        
        $this->load->view('admin/challan/add_challan', $data);*/
    }

    /* Add new Challan or update existing */
    public function challan2($id = '')
    {
        if ($this->input->post()) {
            $invoice_data = $this->input->post();
            if ($id == '') {
                if (!has_permission('challan', '', 'create')) {
                    access_denied('challan');
                }
                
                $id = $this->order_model->add($invoice_data);
                if ($id == false) {
                    set_alert('warning', "Challan already created for this order");
                    redirect(admin_url('challan/challan_list'));
                    
                }else{
                    set_alert('success', _l('added_successfully', _l('invoice')));
                    $redUrl = admin_url('order/list_orders/' . $id);
                    redirect($redUrl);
                }
            } else {
                if (!has_permission('challan', '', 'edit')) {
                    access_denied('challan');
                }
                $success = $this->invoices_model->update($invoice_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice')));
                }
                redirect(admin_url('order/list_orders/' . $id));
            }
        }
        if ($id == '') {
            $title                  = _l('create_new_order');
            $data['billable_tasks'] = [];
        } else {
            $invoice = $this->invoices_model->get($id);

            if (!$invoice || !user_can_view_invoice($id)) {
                blank_page(_l('invoice_not_found'));
            }

            $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
            $data['expenses_to_bill']  = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

            $data['invoice']        = $invoice;
            $data['edit']           = true;
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');

            $title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $this->load->model('clients_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['rootcompany'] = $this->clients_model->get_rootcompany();
        // Customer groups
            $data['groups'] = $this->clients_model->get_groups();
        $data['title']     = $title;
        $data['bodyclass'] = 'invoice';
        $this->load->view('admin/order/order', $data);
    }

    /* Get all invoice data used when user click on invoiec number in a datatable left side*/
    public function get_order_data_ajax($id)
    {
        if (!has_permission('challan', '', 'view')
            && !has_permission('challan', '', 'view_own')
            && get_option('allow_staff_view_invoices_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die(_l('invoice_not_found'));
        }

        $invoice = $this->order_model->get($id);

        if (!$invoice || !user_can_view_invoice($id)) {
            echo _l('invoice_not_found');
            die;
        }

        $template_name = 'invoice_send_to_customer';

        if ($invoice->sent == 1) {
            $template_name = 'invoice_send_to_customer_already_sent';
        }

        $data = prepare_mail_preview_data($template_name, $invoice->clientid);

        // Check for recorded payments
        $this->load->model('payments_model');
        $data['invoices_to_merge']          = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);
        $data['members']                    = $this->staff_model->get('', ['active' => 1]);
        $data['payments']                   = $this->payments_model->get_invoice_payments($id);
        $data['activity']                   = $this->invoices_model->get_invoice_activity($id);
        $data['totalNotes']                 = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'invoice']);
        $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);

        $data['applied_credits'] = $this->credit_notes_model->get_applied_invoice_credits($id);
        // This data is used only when credit can be applied to invoice
        if (credits_can_be_applied_to_invoice($invoice->status)) {
            $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($invoice->clientid);

            if ($data['credits_available'] > 0) {
                $data['open_credits'] = $this->credit_notes_model->get_open_credits($invoice->clientid);
            }

            $customer_currency = $this->clients_model->get_customer_default_currency($invoice->clientid);
            $this->load->model('currencies_model');

            if ($customer_currency != 0) {
                $data['customer_currency'] = $this->currencies_model->get($customer_currency);
            } else {
                $data['customer_currency'] = $this->currencies_model->get_base_currency();
            }
        }

        $data['invoice'] = $invoice;
        $data['invoice_generate'] = $this->order_model->check_invoice_generate($id);
        $data['record_payment'] = false;
        $data['send_later']     = false;

        if ($this->session->has_userdata('record_payment')) {
            $data['record_payment'] = true;
            $this->session->unset_userdata('record_payment');
        } elseif ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        $this->load->view('admin/order/invoice_preview_template', $data);
    }

    public function apply_credits($invoice_id)
    {
        $total_credits_applied = 0;
        foreach ($this->input->post('amount') as $credit_id => $amount) {
            $success = $this->credit_notes_model->apply_credits($credit_id, [
            'invoice_id' => $invoice_id,
            'amount'     => $amount,
        ]);
            if ($success) {
                $total_credits_applied++;
            }
        }

        if ($total_credits_applied > 0) {
            update_invoice_status($invoice_id, true);
            set_alert('success', _l('invoice_credits_applied'));
        }
        redirect(admin_url('order/list_orders/' . $invoice_id));
    }

    public function get_invoices_total()
    {
        if ($this->input->post()) {
            load_invoices_total_template();
        }
    }

    /* Record new inoice payment view */
    public function record_invoice_payment_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $data['invoice']  = $this->invoices_model->get($id);
        $data['payments'] = $this->payments_model->get_invoice_payments($id);
        $this->load->view('admin/invoices/record_payment_template', $data);
    }
    
    /* Record new inoice  */
    public function crate_invoice_by_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $order  = $this->order_model->get($id);
        //echo "<pre>";
        $addedfrom = !DEFINED('CRON') ? get_staff_user_id() : 0;
        $invoicedata=array(
                                "sent"=>$order->sent,
                                "datesend"=>$order->datesend,
                                "clientid"=>$order->clientid,
                                "deleted_customer_name"=>$order->deleted_customer_name,
                                "order_id"=>$order->number,
                                "order_type"=>$order->order_type,
                                "dist_comp"=>$order->dist_comp,
                                "dist_sale_agent"=>$order->dist_sale_agent,
                                "prefix"=>'INV-',
                                "number_format"=>$order->number_format,
                                "datecreated"=>date('Y-m-d H:i:s'),
                                "date"=>date('Y-m-d'),
                                "currency"=>$order->currency,
                                "subtotal"=>$order->subtotal,
                                "total_tax"=>$order->total_tax,
                                "total"=>$order->total,
                                "total_cases"=>$order->total_cases,
                                "adjustment"=>$order->adjustment,
                                "addedfrom"=>$addedfrom,
                                "hash"=>$order->hash,
                                "status"=>$order->status,
                                "allowed_payment_modes"=>$order->allowed_payment_modes,
                                "token"=>$order->token,
                                "discount_percent"=>$order->discount_percent,
                                "discount_total"=>$order->discount_total,
                                "discount_type"=>$order->discount_type,
                                "sale_agent"=>$order->sale_agent,
                                "billing_street"=>$order->billing_street,
                                "billing_city"=>$order->billing_city,
                                "billing_state"=>$order->billing_state,
                                "billing_zip"=>$order->billing_zip,
                                "billing_country"=>$order->billing_country,
                                "shipping_street"=>$order->shipping_street,
                                "shipping_state"=>$order->shipping_state,
                                "shipping_city"=>$order->shipping_city,
                                "shipping_zip"=>$order->shipping_zip,
                                "shipping_country"=>$order->shipping_country,
                                "include_shipping"=>$order->include_shipping,
                                "show_shipping_on_invoice"=>$order->show_shipping_on_invoice,
                                "show_quantity_as"=>$order->show_quantity_as,
                                "subscription_id"=>$order->subscription_id,
                                "short_link"=>$order->short_link,
                                "project_id"=>$order->project_id
                            );
                            
            $items = $order->items;
            /*foreach ($items as $key => $value) {
               # code...
               echo $value["description"];
               echo "<br>";
               
            }*/
            //print_r($items);
            //echo $items[0]['rel_type'];
            //die;
            $this->db->insert(db_prefix() . 'invoices', $invoicedata);
            $invoice_id = $this->db->insert_id();
            if($invoice_id){
                
                $this->db->where('id', $invoice_id);
                $this->db->update(db_prefix() . 'invoices', [
                            'number' => $invoice_id,
                        ]);
                
                foreach ($items as $key => $item) {
               # code...
               
               $itemdata=array(
                                "rel_id"=>$invoice_id,
                                "rel_type"=>$item['rel_type'],
                                "description"=>$item['description'],
                                "long_description"=>$item['long_description'],
                                "hsn_code"=>$item['hsn_code'],
                                "qty"=>$item['qty'],
                                "pack_qty"=>$item['pack_qty'],
                                "rate"=>$item['rate'],
                                "total_amt"=>$item['total_amt'],
                                "discount_amt"=>$item['discount_amt'],
                                "taxable_amt"=>$item['taxable_amt'],
                                "gst"=>$item['gst'],
                                "gst_amt"=>$item['gst_amt'],
                                "unit"=>$item['unit'],
                                "grand_total"=>$item['grand_total'],
                                "item_order"=>$item['item_order']
                            );
                $this->db->insert(db_prefix() . 'itemable', $itemdata);
            }        
            
            }
          
        redirect(admin_url('order/get_order_data_ajax/' . $id));
        
    }

    /* This is where invoice payment record $_POST data is send */
    public function record_payment()
    {
        if (!has_permission('payments', '', 'create')) {
            access_denied('Record Payment');
        }
        if ($this->input->post()) {
            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($this->input->post(), '');
            if ($id) {
                set_alert('success', _l('invoice_payment_recorded'));
                redirect(admin_url('payments/payment/' . $id));
            } else {
                set_alert('danger', _l('invoice_payment_record_failed'));
            }
            redirect(admin_url('order/list_orders/' . $this->input->post('invoiceid')));
        }
    }

    /* Send invoice to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                access_denied('Invoices');
            }
        }

        try {
            $statementData = [];
            if ($this->input->post('attach_statement')) {
                $statementData['attach'] = true;
                $statementData['from']   = to_sql_date($this->input->post('statement_from'));
                $statementData['to']     = to_sql_date($this->input->post('statement_to'));
            }

            $success = $this->invoices_model->send_invoice_to_client(
                $id,
                '',
                $this->input->post('attach_pdf'),
                $this->input->post('cc'),
                false,
                $statementData
            );
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }
        redirect(admin_url('order/list_orders/' . $id));
    }

    /* Delete invoice payment*/
    public function delete_payment($id, $invoiceid)
    {
        if (!has_permission('payments', '', 'delete')) {
            access_denied('payments');
        }
        $this->load->model('payments_model');
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('order/list_orders/' . $invoiceid));
    }

    /* Delete invoice */
    public function delete($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('invoices');
        }
        if (!$id) {
            redirect(admin_url('order/list_orders'));
        }
        $success = $this->invoices_model->delete($id);

        if ($success) {
            set_alert('success', _l('deleted', _l('invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'list_orders') !== false) {
            redirect(admin_url('order/list_orders'));
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->invoices_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Will send overdue notice to client */
    public function send_overdue_notice($id)
    {
        $canView = user_can_view_invoice($id);
        if (!$canView) {
            access_denied('Invoices');
        } else {
            if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {
                access_denied('Invoices');
            }
        }

        $send = $this->invoices_model->send_invoice_overdue_notice($id);
        if ($send) {
            set_alert('success', _l('invoice_overdue_reminder_sent'));
        } else {
            set_alert('warning', _l('invoice_reminder_send_problem'));
        }
        redirect(admin_url('order/list_orders/' . $id));
    }

    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {
        if (!$id) {
            redirect(admin_url('challan/challan_list'));
        }

        if (!has_permission_new('challan_list', '', 'view')) {
                access_denied('Invoices');
            }

        $invoice        = $this->challan_model->getchallandetail($id);
        //print_r($invoice);
        
        $invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
        //$invoice_number = format_invoice_number($invoice->id);

        try {
            $pdf = invoice_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);
    }
    
    public function dispatchsheet($challan_id)
    {
        if (!$challan_id) {
            redirect(admin_url('challan/challan_list'));
        }

        if (!has_permission_new('challan_list', '', 'view')) {
                access_denied('Invoices');
            }

        $invoice        = $this->challan_model->getchallandetail($challan_id);
        /*print_r($invoice);
        die;*/
        try {
            $pdf = dispatch_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($challan_id)) . '-Dispatch.pdf', $type);
    }
    
    /* Generates Gate Pass PDF and senting to email of $send_to_email = true is passed */
    public function gatepass($challan_id)
    {
        if (!$challan_id) {
            redirect(admin_url('challan/challan_list'));
        }

        if (!has_permission_new('gatepass', '', 'view')) {
                access_denied('Invoices');
            }

        $invoice        = $this->challan_model->getchallandetail($challan_id);
        if(is_null($invoice->Gatepassuserid)){
            $selected_company = $this->session->userdata('root_company');
            $fy = $this->session->userdata('finacial_year');
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $fy);  
            $this->db->where('ChallanID', $challan_id);
            $this->db->update(db_prefix() . 'challanmaster', [
                            'gatepasstime' => date('Y-m-d H:i:s'),
                            'Gatepassuserid' => $this->session->userdata('username'),
                            'GetPassTime' => date('Y-m-d H:i:s'),
                            ]);
        }
        
                                    
        try {
            $pdf = gatepass_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(mb_strtoupper(slug_it($challan_id)) . '-Gatepass.pdf', $type);
    }


    public function mark_as_sent($id)
    {
        if (!$id) {
            redirect(admin_url('order/list_orders'));
        }
        if (!user_can_view_invoice($id)) {
            access_denied('Invoice Mark As Sent');
        }

        $success = $this->invoices_model->set_invoice_sent($id, true);

        if ($success) {
            set_alert('success', _l('invoice_marked_as_sent'));
        } else {
            set_alert('warning', _l('invoice_marked_as_sent_failed'));
        }

        redirect(admin_url('order/list_orders/' . $id));
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('invoice_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }
}
