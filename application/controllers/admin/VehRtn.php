<?php

defined('BASEPATH') or exit('No direct script access allowed');

class VehRtn extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vehicle_return_model');
        $this->load->model('VehRtn_model');
    }
    
    public function index(){
        
        if (!has_permission_new('vehicle_return', '', 'view')) {
            access_denied('invoices');
        }
        $title = "Vehicle Rtn";
        $data['title'] = $title;
        $this->load->model('sale_reports_model');
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        //$data['clients_details'] = $this->vehicle_return_model->get_vendor_data();
        //$data['staff_details'] = $this->vehicle_return_model->get_staff_data();
        $fy = $this->session->userdata('finacial_year');
        $fy_new  = $fy + 1;
        $lastdate_date = '20'.$fy_new.'-03-31';
        $firstdate_date = '20'.$fy_new.'-04-01';
        $curr_date = date('Y-m-d');
        $curr_date_new    = new DateTime($curr_date);
        $last_date_yr = new DateTime($lastdate_date);
        if($last_date_yr < $curr_date_new){
            $to_date = '31/03/20'.$fy_new;
            $from_date = '01/03/20'.$fy_new;
        }else{
            $from_date = "01/06/".date('Y');
            $to_date = date('d/m/Y');
        }
        $date = array(
            "from_date"=>$from_date,
            "to_date"=>$to_date,
            );
        $data['vRtnlist'] =  $this->VehRtn_model->vehicle_return_table($date);
        $data['chllist'] =  $this->VehRtn_model->challan_model_table($date);
        $this->load->view('admin/VehRtn/Manage', $data);
    }
    
    public function GetDetail(){
        $VRtnID = $this->input->post('VRtnID');
        // Get data
        $data = $this->VehRtn_model->GetDetails($VRtnID);
        echo json_encode($data);
    }
    // Get Account List For Crates and Payments
    public function GetAccountlistForCrates(){
        $postData = $this->input->post();
        $data = $this->VehRtn_model->GetAccountlistForCrates($postData);
        echo json_encode($data);
    }
    
    // Get Account Details For Crates and Payments
    public function getAccountDetails(){
        $postData = $this->input->post();
        $Account_data = $this->VehRtn_model->getAccountDetails($postData);
        echo json_encode($Account_data);
    }
    
    // Get Account list For Expense
    public function staffaccountlist(){
        $postData = $this->input->post();
        //$data = $this->VehRtn_model->staffgetaccounts($postData);
        $data = $this->VehRtn_model->GetAccountlistForExpenses($postData);
        echo json_encode($data);
    }
    
     // Get Account Details For Expense
    public function get_staffAccount_Details(){
        $postData = $this->input->post();
        //$Account_data = $this->VehRtn_model->get_staffAccount_Details($postData);
        $Account_data = $this->VehRtn_model->getAccountDetailsForExpenses($postData);
        echo json_encode($Account_data);
    }
    
    public function unique_challan_details(){
        
         $data =  $this->VehRtn_model->challan_unique_data($this->input->post());
         echo json_encode($data);
    }
    
     public function vehicle_return_model(){
        $data =  $this->VehRtn_model->vehicle_return_table($this->input->post());
        $html ='';
        if(count($data) >0 ){
         foreach($data as $value){
            $url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";
            //$html.= '<tr onclick="location.href='.$url.'">';
            $html.= '<tr class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';
            $html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';
            $html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';
            $html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 
            $html.= '<td></td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';
           
           $html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;">'.$value["loader_fn"].' '.$value["loader_ln"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;">'.$value["Salesman_fn"].' '.$value["Salesman_ln"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 
           $html.= '<td style="padding:0px 3px !important;">'.$value["OtherVehicleDetails"].'</td>'; 
           $html.= '</tr>'; 
       } 
        }else{
            $html.= '<tr>'; 
            $html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';
            $html.= '</tr>'; 
        }
       echo $html;
    }
    
    public function challan_details_model(){
       $data =  $this->VehRtn_model->challan_model_table($this->input->post());
        $html ='';
        if(count($data) >0 ){
         foreach($data as $value){
            $html.= '<tr class="get_challan_id" data-id="'.$value["ChallanID"].'">'; 
            $html.= '<td style="padding:0px 3px !important;" >'.$value["ChallanID"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 
            $html.= '<td></td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';
            $html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["loader_fn"].' '.$value["loader_ln"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["Salesman_fn"].' '.$value["Salesman_ln"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 
            $html.= '<td style="padding:0px 3px !important;">'.$value["OtherVehicleDetails"].'</td>'; 
            $html.= '</tr>'; 
       } 
        }else{
            $html.= '<tr>'; 
            $html.= '<td colspan="12"><span style="color:red;">No data found..</span></td>';
            $html.= '</tr>'; 
        }
       
       echo $html;
    }
    
    public function all_challan_details(){
        
        $data_a =  $this->VehRtn_model->challan_all_data($this->input->post());
         echo json_encode($data_a);
    }
    
    public function SaveVehRtn()
    {
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        if($selected_company == 1){
            $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');
        }elseif($selected_company == 2){
            $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');
        }elseif($selected_company == 3){
            $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');
        }elseif($selected_company == 4){
            $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');
        }
        
        if($selected_company == "1"){
            $GodownID = 'CSPL';
        }else if($selected_company == "2"){
            $GodownID = 'CFF';
        }else if($selected_company == "3"){
            $GodownID = 'CBUPL';
        }
            
            
        $new_vehicle_return_Numbar = 'VRT'.$FY.$new_vehicle_returnNumber;
        $ChallanID = $this->input->post('challan_n');
        $CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);
        if(empty($CheckVehRtnForChallan)){
            $RtnCrates = $this->input->post('refund_crates');
            $vehicle_number = $this->input->post('vehicle_number');
            $Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');
            $affectedRow = 0;
            $vehicleRtn_data = array(
                'PlantID'=> $selected_company,
                'ReturnID'=>$new_vehicle_return_Numbar,
                'Transdate'=>$Transdate,
                'Crates'=>$RtnCrates,
                'ChallanID'=>$ChallanID,
                'UserID'=>$_SESSION['username'],
                'FY'=>$FY
                );
            
            $this->db->insert(db_prefix() . 'vehiclereturn',$vehicleRtn_data);
        if($this->db->affected_rows() > 0){
            $this->VehRtn_model->increment_next_number();
            $affectedRow++;
            $this->db->where('PlantID', $selected_company);
            $this->db->where('EngageID', $vehicle_number); 
            $this->db->update(db_prefix() . 'accountsld', [
                                        'EngageID' => NULL,
                                    ]);
            $newmonth = substr($Transdate,5,2);
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
            $refund_crates = $this->input->post('refund_crates');
            
        // Fresh Rtn Values
            $frRtnSerializedArr = $this->input->post('frRtnSerializedArr');
            $FreshRtnValArray = json_decode($frRtnSerializedArr, true);
            $frRtnValCount = $this->input->post('frRtnVal');
            $ord_no5 = 1;
            $igst_total = 0;
            $cgst_total = 0;
            $sgst_total = 0;
            for($k=0; $k<$frRtnValCount; $k++) {
                $AccountID_SRtn = $FreshRtnValArray[$k][0];
                $RtnAmt_val = $FreshRtnValArray[$k][1];
                $cgst_val = $FreshRtnValArray[$k][2];
                $sgst_val = $FreshRtnValArray[$k][3];
                $igst_val = $FreshRtnValArray[$k][4];
                $sub_total = $RtnAmt_val - $sgst_val - $cgst_val - $igst_val;
                if($RtnAmt_val !== "0.00" && $RtnAmt_val !== "0"){
                    $igst_total = $igst_total + $igst_val;
                    $cgst_total = $cgst_total + $cgst_val;
                    $sgst_total = $sgst_total + $sgst_val;
                    
                    // Respective Account ledger Entry for Credit
                    $credit_ledger = array(
                        "FY"=>$FY,
                        "PlantID"=>$selected_company,
                        "VoucherID"=>$new_vehicle_return_Numbar,
                        "Transdate"=>$Transdate,
                        "TransDate2"=>date('Y-m-d H:i:s'),
                        "TType"=>"C",
                        "AccountID"=>$AccountID_SRtn,
                        "Amount"=>$RtnAmt_val,
                        "Narration"=>'By Fresh stock return/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        "PassedFrom"=>"VEHRTNFRESH",
                        "OrdinalNo"=>$ord_no5,
                        "UserID"=>$this->session->userdata('username'),
                    );
                    $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
                        if($this->db->affected_rows()>0){
                            $affectedRow++;
                            $get_account_bal11 = $this->VehRtn_model->get_acc_bal($AccountID_SRtn);
                            $current_bal11 = $get_account_bal11->$mm;
                            $newCRAmt = $current_bal11 - $net_total;
                            
                            $this->db->where('PlantID', $selected_company);
                            $this->db->LIKE('FY', $FY);
                            $this->db->where('AccountID', $AccountID_SRtn);
                            $this->db->update(db_prefix() . 'accountbalances', [
                                                        $mm => $newCRAmt,
                                                    ]);
                        }
                    $ord_no5++;
                    // Cash Account ledger Entry for Credit
                    $debit_ledger = array(
                        "FY"=>$FY,
                        "PlantID"=>$selected_company,
                        "VoucherID"=>$new_vehicle_return_Numbar,
                        "Transdate"=>$Transdate,
                        "TransDate2"=>date('Y-m-d H:i:s'),
                        "TType"=>"D",
                        "AccountID"=>"SALE",
                        "Amount"=>$sub_total,
                        "Narration"=>'By Fresh stock return/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        "PassedFrom"=>"VEHRTNFRESH",
                        "OrdinalNo"=>$ord_no5,
                        "UserID"=>$this->session->userdata('username'),
                    );
                    $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                        if($this->db->affected_rows()>0){
                            $affectedRow++;
                            $get_account_bal111 = $this->VehRtn_model->get_acc_bal('SALE');
                            $current_bal111 = $get_account_bal111->$mm;
                            $newDRAmt = $current_bal111 + $RtnAmt_val;
                            
                            $this->db->where('PlantID', $selected_company);
                            $this->db->LIKE('FY', $FY);
                            $this->db->where('AccountID', 'SALE');
                            $this->db->update(db_prefix() . 'accountbalances', [
                                                        $mm => $newDRAmt,
                                                    ]);
                        }
                    $ord_no5++;
                }
                
            }
            // Ledger fr CGST,SGST and IGST
            
            if($cgst_total !== 0){
            // SGST Account Ledger & Balance     
                $debit_ledger = array(
                    "FY"=>$FY,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$new_vehicle_return_Numbar,
                    "Transdate"=>$Transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>"D",
                    "AccountID"=>"SGST",
                    "Amount"=>$sgst_total,
                    "Narration"=>'By Fresh stock return/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    "PassedFrom"=>"VEHRTNFRESH",
                    "OrdinalNo"=>$ord_no5,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal21 = $this->VehRtn_model->get_acc_bal('SGST');
                        $current_bal21 = $get_account_bal21->$mm;
                        $newDRAmt = $current_bal21 + $sgst_total;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'SGST');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                $ord_no5++;
                // CGST Account Ledger & Balance     
                $debit_ledger = array(
                    "FY"=>$FY,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$new_vehicle_return_Numbar,
                    "Transdate"=>$Transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>"D",
                    "AccountID"=>"CGST",
                    "Amount"=>$cgst_total,
                    "Narration"=>'By Fresh stock return/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    "PassedFrom"=>"VEHRTNFRESH",
                    "OrdinalNo"=>$ord_no5,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal22 = $this->VehRtn_model->get_acc_bal('CGST');
                        $current_bal22 = $get_account_bal22->$mm;
                        $newDRAmt = $current_bal22 + $cgst_total;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'CGST');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                $ord_no5++;
            }
            if($igst_total !== 0){
                
                $debit_ledger = array(
                    "FY"=>$FY,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$new_vehicle_return_Numbar,
                    "Transdate"=>$Transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>"D",
                    "AccountID"=>"IGST",
                    "Amount"=>$igst_total,
                    "Narration"=>'By Fresh stock return/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    "PassedFrom"=>"VEHRTNFRESH",
                    "OrdinalNo"=>$ord_no5,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal23 = $this->VehRtn_model->get_acc_bal('IGST');
                        $current_bal23 = $get_account_bal23->$mm;
                        $newDRAmt = $current_bal23 + $igst_total;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'IGST');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
            }
        // For Fresh Rtn
            $FreshRtnSerializedArr = $this->input->post('FreshRtnSerializedArr');
            $FreshRtnArray = json_decode($FreshRtnSerializedArr, true);
            $FrtRtnCount = $this->input->post('ItemCount');
            $ord_no4 = 1;
            for($k=0; $k<$FrtRtnCount; $k++) {
                $rtnqty = $FreshRtnArray[$k][0];
                $TransID_val = $FreshRtnArray[$k][1];
                $ItemID_val = $FreshRtnArray[$k][2];
                $AccountID_val = $FreshRtnArray[$k][3];
                $rate_val = $FreshRtnArray[$k][4];
                $gst_val = $FreshRtnArray[$k][5];
                $state_val = $FreshRtnArray[$k][6];
                $PackQty_val = $FreshRtnArray[$k][7];
                if($rtnqty == ''){
                    
                }else{
                    $ChallanAmt = $rate_val * $rtnqty;
                    $gst_amt = ($ChallanAmt/100) * $gst_val;
                    $NetChallanAmt = $ChallanAmt + $gst_amt;
                    $gstRate = ($rate_val/100) * $gst_val;
                    $saleRate = $gstRate + $rate_val;
                    $CaseQty = $PackQty_val;
                    if($state_val == "UP"){
                        $cgstAmt = $gst_amt / 2;
                        $sgstAmt = $gst_amt / 2;
                        $igstAmt = 0.00;
                        
                        $cgstPer = $gst_val / 2;
                        $sgstPer = $gst_val / 2;
                        $igstPer = 0.00;
                    }else{
                        $cgstAmt = 0.00;
                        $sgstAmt = 0.00;
                        $igstAmt = $gst_amt;
                        
                        $cgstPer = 0.00;
                        $sgstPer = 0.00;
                        $igstPer = $gst_val;
                    }
                    
                        // stock update
                        $stock_data = $this->VehRtn_model->get_stock_item($ItemID_val);
                        $item_stock= $stock_data->SRQty;
                        $new_stock = $item_stock + $rtnqty;
                    
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $FY);  
                        $this->db->where('ItemID', $ItemID_val);
                        $this->db->where('GodownID',$GodownID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                                    'SRQty' => $new_stock,
                                                ]);
                    
                    $new_record_details = array(
                            "PlantID"=>$selected_company,
                            "FY"=>$FY,
                            "cnfid"=>"1",
                            "OrderID"=>$new_vehicle_return_Numbar,
                            "TransDate"=>$Transdate,
                            "TransDate2"=>$Transdate,
                            "BillID"=>$ChallanID,
                            "TransID"=>$TransID_val,
                            "GodownID"=>$GodownID,
                            "TType"=>"R",
                            "TType2"=>"Fresh",
                            "AccountID"=>$AccountID_val,
                            "ItemID"=>$ItemID_val,
                            "CaseQty"=>$CaseQty,
                            "SaleRate"=>$saleRate,
                            "BasicRate"=>$rate_val,
                            "SuppliedIn"=>"CS",
                            "BilledQty"=>$rtnqty,
                            "DiscPerc"=>"0.00",
                            "DiscAmt"=>"0.00",
                            "cgst"=>$cgstPer,
                            "cgstamt"=>$cgstAmt,
                            "sgst"=>$sgstPer,
                            "sgstamt"=>$sgstAmt,
                            "igst"=>$igstPer,
                            "igstamt"=>$igstAmt,
                            "ChallanAmt"=>$ChallanAmt,
                            "NetChallanAmt"=>$NetChallanAmt,
                            "Ordinalno"=>$ord_no4,
                            "UserID"=>$this->session->userdata('username'),
                        );
                    //print_r($new_record_details);
                    $this->db->insert(db_prefix() . 'history', $new_record_details);
                }
            }
            
        // For Crate Ledger
            $CratesSerializedArr = $this->input->post('CratesSerializedArr');
            $CrateArray = json_decode($CratesSerializedArr, true);
            $CrateCount = $this->input->post('CrateCount');
            $ord_no3 = 1;
            for($k=0; $k<$CrateCount; $k++) {
                $AccountID = $CrateArray[$k][0];
                $RtnCrates = $CrateArray[$k][1];
                if($RtnCrates =="" || $RtnCrates == '0'){
                    
                }else{
                    $vehicleCrates_data = array(
                        'PlantID'=>$selected_company,
                        'VoucherID' =>$new_vehicle_return_Numbar,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'ChallanID' =>$ChallanID,
                        'AccountID' =>$AccountID,
                        'TType' =>'C',
                        'Qty'=>$RtnCrates,
                        'PassedFrom'=>'VEHRTNCRATES',
                        'Narration'=> 'Against VehicleID '.$new_vehicle_return_Numbar.'/ChallanID /'.$ChallanID,
                        'Ordinalno'=>$ord_no3,
                        'UserID'=>$_SESSION['username'],
                         'FY'=>$FY,
                    );
                    //print_r($vehicleCrates_data);
                    $data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);
                    $ord_no3++;
                }
                
            }
        // For Expenses
            $ExpSerializedArr = $this->input->post('ExpSerializedArr');
            $EXPArray = json_decode($ExpSerializedArr, true);
            $row_count_exp = $this->input->post('ExpCount');
            $ord_no = 1;
        
            for($k=0; $k<$row_count_exp; $k++) {
                $AccountID = $EXPArray[$k][0];
                $ExpAmt = $EXPArray[$k][1];
            if($ExpAmt !== "0.00" && $ExpAmt !== "0"){
                $expense_detail_result = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$new_vehicle_return_Numbar,
                        'AccountID' =>$AccountID,
                        'TType' =>'D',
                        'Amount'=>$ExpAmt,
                        'PassedFrom'=>'VEHRTNEXP',
                        'Narration'=> 'By Vehicle Expense '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no,
                        'UserID'=>$_SESSION['username'], 
                        );
                    //print_r($expense_detail_result);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal1 = $this->VehRtn_model->get_acc_bal($AccountID);
                        $current_bal1 = $get_account_bal1->$mm;
                        $newDRAmt = $current_bal1 + $ExpAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', $AccountID);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                    
                    $expense_detail_result_debit = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$new_vehicle_return_Numbar,
                        'AccountID' =>'CASH',
                        'TType' =>'C',
                        'Amount'=>$ExpAmt,
                        'PassedFrom'=>'VEHRTNEXP',
                        'Narration'=> 'By Vehicle Expense '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no,
                        'UserID'=>$_SESSION['username'],
                        );
                    //print_r($expense_detail_result_debit);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal2 = $this->VehRtn_model->get_acc_bal('CASH');
                        $current_bal2 = $get_account_bal2->$mm;
                        $newCRAmt = $current_bal2 - $ExpAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'CASH');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newCRAmt,
                                                ]);
                    }
                    
                $ord_no++;
            }    
                
            }
            
            // For Payment Ledger 
            $PaymentSerializedArr = $this->input->post('PaymentSerializedArr');
            $PayArray = json_decode($PaymentSerializedArr, true);
            $PayCount = $this->input->post('PayCount');
            $ord_no2 = 1;
            
                for($k=0; $k<$PayCount; $k++) {
                    $AccountID = $PayArray[$k][0];
                    $PayAmt = $PayArray[$k][1];
                if($PayAmt !== "0.00" && $PayAmt !== "0"){
                    $payment_reciept_result = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$new_vehicle_return_Numbar,
                        'AccountID' =>$AccountID,
                        'TType' =>'C',
                        'Amount'=>$PayAmt,
                        'PassedFrom'=>'VEHRTNPYMTS',
                        'Narration'=> 'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no2,
                        'UserID'=>$_SESSION['username'],
                    );
                        //print_r($expense_detail_result);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);
                    
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal3 = $this->VehRtn_model->get_acc_bal($AccountID);
                        $current_bal3 = $get_account_bal3->$mm;
                        $newCRAmt = $current_bal3 + $PayAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', $AccountID);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newCRAmt,
                                                ]);
                    }
                    
                    $payment_reciept_result_debit = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$new_vehicle_return_Numbar,
                        'AccountID' =>'CASH',
                        'TType' =>'D',
                        'Amount'=>$PayAmt,
                        'PassedFrom'=>'VEHRTNPYMTS',
                        'Narration'=> 'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no2,
                        'UserID'=>$_SESSION['username'],
                        );
                    //print_r($payment_reciept_result_debit);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal4 = $this->VehRtn_model->get_acc_bal('CASH');
                        $current_bal4 = $get_account_bal4->$mm;
                        $newDRAmt = $current_bal4 + $PayAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'CASH');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                    $ord_no2++;
                }    
                    
                }
        }    
            if($affectedRow > 0){
                if($selected_company == 1){
                    $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');
                }elseif($selected_company == 2){
                    $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');
                }elseif($selected_company == 3){
                    $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');
                }elseif($selected_company == 4){
                    $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');
                }
                $new_vehicle_return_Numbar = 'VRT'.$FY.$next_vehicle_returnNumber;
                echo json_encode($new_vehicle_return_Numbar);
                die;
            }else{
                echo json_encode(false);
                die;
            }
            
        }else{
            echo json_encode('Created');
            die;
        }
        
    }
    
    public function UpdateVehRtn()
    {
        
        $selected_company = $this->session->userdata('root_company');
        if($selected_company == "1"){
            $GodownID = 'CSPL';
        }else if($selected_company == "2"){
            $GodownID = 'CFF';
        }else if($selected_company == "3"){
            $GodownID = 'CBUPL';
        }
        $FY = $this->session->userdata('finacial_year');
        $ChallanID = $this->input->post('challan_n');
        $RtnCrates = $this->input->post('refund_crates');
        $vehicle_number = $this->input->post('vehicle_number');
        $VRtnID = $this->input->post('VRtnID');
        $GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   
        $Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');
        $oldDate = $GetVRtnDetails->Transdate;
        $affectedRow = 0;
        $vehicleRtn_data = array(
            'Transdate'=>$Transdate,
            'Crates'=>$RtnCrates,
            'UserID2'=>$_SESSION['username'],
            'Lupdate'=>date('Y-m-d H:i:s')
        );
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY); 
        $this->db->where('ReturnID', $VRtnID);
        $this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);
        if($this->db->affected_rows() > 0){
            $affectedRow++;
        }
        $newmonth = substr($Transdate,5,2);
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
        $oldmonth = substr($oldDate,5,2);
            if($oldmonth == "01"){
               $oldm = 11; 
            }
            if($oldmonth == "02"){
               $oldm = 12; 
            }
            if($oldmonth == "03"){
               $oldm = 13; 
            }
            if($oldmonth == "04"){
               $oldm = 2; 
            }
            if($oldmonth == "05"){
               $oldm = 3; 
            }
            if($oldmonth == "06"){
               $oldm = 4; 
            }
            if($oldmonth == "07"){
               $oldm = 5; 
            }
            if($oldmonth == "08"){
               $oldm = 6; 
            }
            if($oldmonth == "09"){
               $oldm = 7; 
            }
            if($oldmonth == "10"){
               $oldm = 8; 
            }
            if($oldmonth == "11"){
               $oldm = 9; 
            }
            if($oldmonth == "12"){
               $oldm = 10; 
            }
            $oldmm = "BAL".$oldm;
        // Fresh Rtn Values
        // Delete and Revert balances from privous ledger
        $GetPreLedger = $this->VehRtn_model->GetPreLedger($VRtnID);   
        foreach($GetPreLedger as $key => $value){
            
            $get_account_bal = $this->VehRtn_model->get_acc_bal($value["AccountID"]);
            $Cbal = $get_account_bal->$oldmm;
            if($value["TType"] == "C"){
                $NewCbal = $Cbal + $value["Amount"];
            }else{
                $NewCbal = $Cbal - $value["Amount"];
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $FY);
            $this->db->where('AccountID', $value["AccountID"]);
            $this->db->update(db_prefix() . 'accountbalances', [
                                    $oldmm => $NewCbal,
                                ]);
            $ledger_audit = array(
                    "PlantID"=>$value["PlantID"],
                    "FY"=>$value["FY"],
                    "Transdate"=>$value["Transdate"],
                    "TransDate2"=>$value["TransDate2"],
                    "VoucherID"=>$value["VoucherID"],
                    "AccountID"=>$value["AccountID"],
                    "TType"=>$value["TType"],
                    "Amount"=>$value["Amount"],
                    "Narration"=>$value["Narration"],
                    "PassedFrom"=>$value["PassedFrom"],
                    "OrdinalNo"=>$value["OrdinalNo"],
                    "UserID"=>$value["UserID"],
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            $UserID1 = $value["UserID"];
        }
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $FY);
        $this->db->where('PassedFrom', "VEHRTNFRESH");
        $this->db->where('VoucherID', $VRtnID);
        $this->db->delete(db_prefix() . 'accountledger');
        
    // New Ledger Entery created
        $frRtnSerializedArr = $this->input->post('frRtnSerializedArr');
        $FreshRtnValArray = json_decode($frRtnSerializedArr, true);
        $frRtnValCount = $this->input->post('frRtnVal');
        $ord_no5 = 1;
        $igst_total = 0;
        $cgst_total = 0;
        $sgst_total = 0;
        for($k=0; $k<$frRtnValCount; $k++) {
            $AccountID_SRtn = $FreshRtnValArray[$k][0];
            $RtnAmt_val = $FreshRtnValArray[$k][1];
            $cgst_val = $FreshRtnValArray[$k][2];
            $sgst_val = $FreshRtnValArray[$k][3];
            $igst_val = $FreshRtnValArray[$k][4];
            $Sub_total =  $RtnAmt_val - $sgst_val - $cgst_val - $igst_val;
            if($RtnAmt_val !== "0.00"  && $RtnAmt_val !== "0"){
                $igst_total = $igst_total + $igst_val;
                $cgst_total = $cgst_total + $cgst_val;
                $sgst_total = $sgst_total + $sgst_val;
                
                // Respective Account ledger Entry for Credit
                $credit_ledger = array(
                    "FY"=>$FY,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$VRtnID,
                    "Transdate"=>$Transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>"C",
                    "AccountID"=>$AccountID_SRtn,
                    "Amount"=>$RtnAmt_val,
                    "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                    "PassedFrom"=>"VEHRTNFRESH",
                    "OrdinalNo"=>$ord_no5,
                    "UserID2"=>$this->session->userdata('username'),
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID"=>$UserID1
                );
                $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal11 = $this->VehRtn_model->get_acc_bal($AccountID_SRtn);
                        $current_bal11 = $get_account_bal11->$mm;
                        $newCRAmt = $current_bal11 - $net_total;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', $AccountID_SRtn);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newCRAmt,
                                                ]);
                    }
                $ord_no5++;
                // Cash Account ledger Entry for Credit
                $debit_ledger = array(
                    "FY"=>$FY,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$VRtnID,
                    "Transdate"=>$Transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>"D",
                    "AccountID"=>"SALE",
                    "Amount"=>$Sub_total,
                    "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                    "PassedFrom"=>"VEHRTNFRESH",
                    "OrdinalNo"=>$ord_no5,
                    "UserID2"=>$this->session->userdata('username'),
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID"=>$UserID1
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal111 = $this->VehRtn_model->get_acc_bal('SALE');
                        $current_bal111 = $get_account_bal111->$mm;
                        $newDRAmt = $current_bal111 + $RtnAmt_val;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'SALE');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                $ord_no5++;
            }
            
        }
        // Ledger fr CGST,SGST and IGST
        
        if($cgst_total !== 0){
        // SGST Account Ledger & Balance     
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$VRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"SGST",
                "Amount"=>$sgst_total,
                "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no5,
                "UserID2"=>$this->session->userdata('username'),
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID"=>$UserID1
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            if($this->db->affected_rows()>0){
                    $affectedRow++;
                    $get_account_bal21 = $this->VehRtn_model->get_acc_bal('SGST');
                    $current_bal21 = $get_account_bal21->$mm;
                    $newDRAmt = $current_bal21 + $sgst_total;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $FY);
                    $this->db->where('AccountID', 'SGST');
                    $this->db->update(db_prefix() . 'accountbalances', [
                                                $mm => $newDRAmt,
                                            ]);
                }
            $ord_no5++;
            // CGST Account Ledger & Balance     
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$VRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"CGST",
                "Amount"=>$cgst_total,
                "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no5,
                "UserID2"=>$this->session->userdata('username'),
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID"=>$UserID1
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
                if($this->db->affected_rows()>0){
                    $affectedRow++;
                    $get_account_bal22 = $this->VehRtn_model->get_acc_bal('CGST');
                    $current_bal22 = $get_account_bal22->$mm;
                    $newDRAmt = $current_bal22 + $cgst_total;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $FY);
                    $this->db->where('AccountID', 'CGST');
                    $this->db->update(db_prefix() . 'accountbalances', [
                                                $mm => $newDRAmt,
                                            ]);
                }
            $ord_no5++;
        }
        if($igst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$VRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"IGST",
                "Amount"=>$igst_total,
                "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no5,
                "UserID2"=>$this->session->userdata('username'),
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID"=>$UserID1
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            if($this->db->affected_rows()>0){
                    $affectedRow++;
                    $get_account_bal23 = $this->VehRtn_model->get_acc_bal('IGST');
                    $current_bal23 = $get_account_bal23->$mm;
                    $newDRAmt = $current_bal23 + $igst_total;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $FY);
                    $this->db->where('AccountID', 'IGST');
                    $this->db->update(db_prefix() . 'accountbalances', [
                                                $mm => $newDRAmt,
                                            ]);
                }
        }
        
    // For Fresh Rtn
        $SaleRtnItemList = array();
        $GetSaleRtn = $this->VehRtn_model->GetSaleRtn($VRtnID);
        $ord_no4 = 1;
        foreach($GetSaleRtn as $key => $value){
            $ord_no4++;
            array_push($SaleRtnItemList, $value["ItemID"]);
            $stock_data = $this->VehRtn_model->get_stock_item($value["ItemID"]);
            $item_stock= $stock_data->SRQty;
            $new_stock = $item_stock - $value["BilledQty"];
                
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $FY);  
            $this->db->where('ItemID', $value["ItemID"]);
            $this->db->where('GodownID',$GodownID);
            $this->db->update(db_prefix() . 'stockmaster', [
                        'SRQty' => $new_stock,
                    ]);
            
            $Updatedetails = array(
                "BilledQty"=>0.00,
                "cgstamt"=>0.00,
                "sgstamt"=>0.00,
                "igstamt"=>0.00,
                "ChallanAmt"=>0.00,
                "NetChallanAmt"=>0.00,
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$this->session->userdata('username')
            );
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $FY);  
            $this->db->where('ItemID', $value["ItemID"]);
            $this->db->where('OrderID', $value["OrderID"]);
            $this->db->update(db_prefix() . 'history', $Updatedetails);
        }
        
        $FreshRtnSerializedArr = $this->input->post('FreshRtnSerializedArr');
        $FreshRtnArray = json_decode($FreshRtnSerializedArr, true);
        $FrtRtnCount = $this->input->post('ItemCount');
        for($k=0; $k<$FrtRtnCount; $k++) {
            $rtnqty = $FreshRtnArray[$k][0];
            $TransID_val = $FreshRtnArray[$k][1];
            $ItemID_val = $FreshRtnArray[$k][2];
            $AccountID_val = $FreshRtnArray[$k][3];
            $rate_val = $FreshRtnArray[$k][4];
            $gst_val = $FreshRtnArray[$k][5];
            $state_val = $FreshRtnArray[$k][6];
            $PackQty_val = $FreshRtnArray[$k][7];
            if($rtnqty == ''){
                
            }else{
                $ChallanAmt = $rate_val * $rtnqty;
                $gst_amt = ($ChallanAmt/100) * $gst_val;
                $NetChallanAmt = $ChallanAmt + $gst_amt;
                $gstRate = ($rate_val/100) * $gst_val;
                $saleRate = $gstRate + $rate_val;
                $CaseQty = $PackQty_val;
                if($state_val == "UP"){
                    $cgstAmt = $gst_amt / 2;
                    $sgstAmt = $gst_amt / 2;
                    $igstAmt = 0.00;
                    
                    $cgstPer = $gst_val / 2;
                    $sgstPer = $gst_val / 2;
                    $igstPer = 0.00;
                }else{
                    $cgstAmt = 0.00;
                    $sgstAmt = 0.00;
                    $igstAmt = $gst_amt;
                    
                    $cgstPer = 0.00;
                    $sgstPer = 0.00;
                    $igstPer = $gst_val;
                }
                // stock update
                $stock_data = $this->VehRtn_model->get_stock_item($ItemID_val);
                $item_stock= $stock_data->SRQty;
                $new_stock = $item_stock + $rtnqty;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $FY);  
                $this->db->where('ItemID', $ItemID_val);
                $this->db->where('GodownID',$GodownID);
                $this->db->update(db_prefix() . 'stockmaster', [
                                            'SRQty' => $new_stock,
                                        ]);
                if (in_array($ItemID_val, $SaleRtnItemList)){
                    $Updatedetails = array(
                        "BilledQty"=>$rtnqty,
                        "cgstamt"=>$cgstAmt,
                        "sgstamt"=>$sgstAmt,
                        "igstamt"=>$igstAmt,
                        "ChallanAmt"=>$ChallanAmt,
                        "NetChallanAmt"=>$NetChallanAmt,
                    );
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $FY);  
                    $this->db->where('ItemID', $ItemID_val);
                    $this->db->where('OrderID', $VRtnID);
                    $this->db->where('TransID', $TransID_val);
                    $this->db->update(db_prefix() . 'history', $Updatedetails);
                }else{
                    $new_record_details = array(
                        "PlantID"=>$selected_company,
                        "FY"=>$FY,
                        "cnfid"=>"1",
                        "OrderID"=>$VRtnID,
                        "TransDate"=>$Transdate,
                        "TransDate2"=>$Transdate,
                        "BillID"=>$ChallanID,
                        "TransID"=>$TransID_val,
                        "GodownID"=>$GodownID,
                        "TType"=>"R",
                        "TType2"=>"Fresh",
                        "AccountID"=>$AccountID_val,
                        "ItemID"=>$ItemID_val,
                        "CaseQty"=>$CaseQty,
                        "SaleRate"=>$saleRate,
                        "BasicRate"=>$rate_val,
                        "SuppliedIn"=>"CS",
                        "BilledQty"=>$rtnqty,
                        "DiscPerc"=>"0.00",
                        "DiscAmt"=>"0.00",
                        "cgst"=>$cgstPer,
                        "cgstamt"=>$cgstAmt,
                        "sgst"=>$sgstPer,
                        "sgstamt"=>$sgstAmt,
                        "igst"=>$igstPer,
                        "igstamt"=>$igstAmt,
                        "ChallanAmt"=>$ChallanAmt,
                        "NetChallanAmt"=>$NetChallanAmt,
                        "Ordinalno"=>$ord_no4,
                        "UserID"=>$this->session->userdata('username'),
                    );
                    //print_r($new_record_details);
                    $this->db->insert(db_prefix() . 'history', $new_record_details);
                }
                
            }
        }
        
    // For Crate Ledger
    // Delete Previoud ledger
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $FY);
        $this->db->where('PassedFrom', "VEHRTNCRATES");
        $this->db->where('VoucherID', $VRtnID);
        $this->db->delete(db_prefix() . 'accountcrates');
    // Create New
        $CratesSerializedArr = $this->input->post('CratesSerializedArr');
        $CrateArray = json_decode($CratesSerializedArr, true);
        $CrateCount = $this->input->post('CrateCount');
        $ord_no3 = 1;
        for($k=0; $k<$CrateCount; $k++) {
            $AccountID = $CrateArray[$k][0];
            $RtnCrates = $CrateArray[$k][1];
            if($RtnCrates =="" || $RtnCrates == '0'){
                
            }else{
                $vehicleCrates_data = array(
                    'PlantID'=>$selected_company,
                    'VoucherID' =>$VRtnID,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'ChallanID' =>$ChallanID,
                    'AccountID' =>$AccountID,
                    'TType' =>'C',
                    'Qty'=>$RtnCrates,
                    'PassedFrom'=>'VEHRTNCRATES',
                    'Narration'=> 'Against VehicleID '.$VRtnID.'/ChallanID /'.$ChallanID,
                    'Ordinalno'=>$ord_no3,
                    'UserID'=>$_SESSION['username'],
                    'FY'=>$FY,
                );
                //print_r($vehicleCrates_data);
                $data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);
                $ord_no3++;
            }
        }
        
    // For Expenses
    // Delete and Revert balances from privous ledger
        $GetPreLedger2 = $this->VehRtn_model->GetPreLedger2($VRtnID);   
        foreach($GetPreLedger2 as $key => $value){
            
            $get_account_bal = $this->VehRtn_model->get_acc_bal($value["AccountID"]);
            $Cbal = $get_account_bal->$oldmm;
            if($value["TType"] == "C"){
                $NewCbal = $Cbal + $value["Amount"];
            }else{
                $NewCbal = $Cbal - $value["Amount"];
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $FY);
            $this->db->where('AccountID', $value["AccountID"]);
            $this->db->update(db_prefix() . 'accountbalances', [
                                    $oldmm => $NewCbal,
                                ]);
            $ledger_audit = array(
                    "PlantID"=>$value["PlantID"],
                    "FY"=>$value["FY"],
                    "Transdate"=>$value["Transdate"],
                    "TransDate2"=>$value["TransDate2"],
                    "VoucherID"=>$value["VoucherID"],
                    "AccountID"=>$value["AccountID"],
                    "TType"=>$value["TType"],
                    "Amount"=>$value["Amount"],
                    "Narration"=>$value["Narration"],
                    "PassedFrom"=>$value["PassedFrom"],
                    "OrdinalNo"=>$value["OrdinalNo"],
                    "UserID"=>$value["UserID"],
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            $UserID2 = $value["UserID"];
        }
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $FY);
        $this->db->where('PassedFrom', "VEHRTNEXP");
        $this->db->where('VoucherID', $VRtnID);
        $this->db->delete(db_prefix() . 'accountledger');
    // Create New ledger
        $ExpSerializedArr = $this->input->post('ExpSerializedArr');
        $EXPArray = json_decode($ExpSerializedArr, true);
        $row_count_exp = $this->input->post('ExpCount');
        $ord_no = 1;
    
        for($k=0; $k<$row_count_exp; $k++) {
            $AccountID = $EXPArray[$k][0];
            $ExpAmt = $EXPArray[$k][1];
            if($ExpAmt !== "0.00" && $ExpAmt !== "0"){
                $expense_detail_result = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$VRtnID,
                    'AccountID' =>$AccountID,
                    'TType' =>'D',
                    'Amount'=>$ExpAmt,
                    'PassedFrom'=>'VEHRTNEXP',
                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID2'=>$_SESSION['username'], 
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID"=>$UserID2
                );
                //print_r($expense_detail_result);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);
                if($this->db->affected_rows()>0){
                    $affectedRow++;
                    $get_account_bal1 = $this->VehRtn_model->get_acc_bal($AccountID);
                    $current_bal1 = $get_account_bal1->$mm;
                    $newDRAmt = $current_bal1 + $ExpAmt;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $FY);
                    $this->db->where('AccountID', $AccountID);
                    $this->db->update(db_prefix() . 'accountbalances', [
                                                $mm => $newDRAmt,
                                            ]);
                }
                
                $expense_detail_result_debit = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$VRtnID,
                    'AccountID' =>'CASH',
                    'TType' =>'C',
                    'Amount'=>$ExpAmt,
                    'PassedFrom'=>'VEHRTNEXP',
                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID2'=>$_SESSION['username'],
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID"=>$UserID2
                );
                //print_r($expense_detail_result_debit);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);
                if($this->db->affected_rows()>0){
                    $affectedRow++;
                    $get_account_bal2 = $this->VehRtn_model->get_acc_bal('CASH');
                    $current_bal2 = $get_account_bal2->$mm;
                    $newCRAmt = $current_bal2 - $ExpAmt;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->LIKE('FY', $FY);
                    $this->db->where('AccountID', 'CASH');
                    $this->db->update(db_prefix() . 'accountbalances', [
                                                $mm => $newCRAmt,
                                            ]);
                }
            $ord_no++;
            }
                
        }
        
    // For Payment Ledger 
    // Delete and Revert balances from privous ledger
        $GetPreLedger3 = $this->VehRtn_model->GetPreLedger3($VRtnID);   
        foreach($GetPreLedger3 as $key => $value){
            
            $get_account_bal = $this->VehRtn_model->get_acc_bal($value["AccountID"]);
            $Cbal = $get_account_bal->$oldmm;
            if($value["TType"] == "C"){
                $NewCbal = $Cbal + $value["Amount"];
            }else{
                $NewCbal = $Cbal - $value["Amount"];
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('AccountID', $value["AccountID"]);
            $this->db->update(db_prefix() . 'accountbalances', [
                                    $oldmm => $NewCbal,
                                ]);
            $ledger_audit = array(
                    "PlantID"=>$value["PlantID"],
                    "FY"=>$value["FY"],
                    "Transdate"=>$value["Transdate"],
                    "TransDate2"=>$value["TransDate2"],
                    "VoucherID"=>$value["VoucherID"],
                    "AccountID"=>$value["AccountID"],
                    "TType"=>$value["TType"],
                    "Amount"=>$value["Amount"],
                    "Narration"=>$value["Narration"],
                    "PassedFrom"=>$value["PassedFrom"],
                    "OrdinalNo"=>$value["OrdinalNo"],
                    "UserID"=>$value["UserID"],
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            $UserID3 = $value["UserID"];
        }
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $FY);
        $this->db->where('PassedFrom', "VEHRTNPYMTS");
        $this->db->where('VoucherID', $VRtnID);
        $this->db->delete(db_prefix() . 'accountledger');
        
        $PaymentSerializedArr = $this->input->post('PaymentSerializedArr');
        $PayArray = json_decode($PaymentSerializedArr, true);
        $PayCount = $this->input->post('PayCount');
        $ord_no2 = 1;
        
            for($k=0; $k<$PayCount; $k++) {
                $AccountID = $PayArray[$k][0];
                $PayAmt = $PayArray[$k][1];
                if($PayAmt !== "0.00" && $PayAmt !== "0"){
                    $payment_reciept_result = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$VRtnID,
                        'AccountID' =>$AccountID,
                        'TType' =>'C',
                        'Amount'=>$PayAmt,
                        'PassedFrom'=>'VEHRTNPYMTS',
                        'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no2,
                        'UserID'=>$UserID3,
                        "Lupdate"=>date('Y-m-d H:i:s'),
                        "UserID2"=>$this->session->userdata('username')
                    );
                        //print_r($expense_detail_result);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);
                    
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal3 = $this->VehRtn_model->get_acc_bal($AccountID);
                        $current_bal3 = $get_account_bal3->$mm;
                        $newCRAmt = $current_bal3 + $PayAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', $AccountID);
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newCRAmt,
                                                ]);
                    }
                    
                    $payment_reciept_result_debit = array(
                        'PlantID'=>$selected_company,
                        'FY' =>$FY,
                        'Transdate' =>$Transdate,
                        'TransDate2' =>date('Y-m-d H:i:s'),
                        'VoucherID' =>$VRtnID,
                        'AccountID' =>'CASH',
                        'TType' =>'D',
                        'Amount'=>$PayAmt,
                        'PassedFrom'=>'VEHRTNPYMTS',
                        'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,
                        'OrdinalNo'=>$ord_no2,
                        'UserID'=>$UserID3,
                        "Lupdate"=>date('Y-m-d H:i:s'),
                        "UserID2"=>$this->session->userdata('username')
                        );
                    //print_r($payment_reciept_result_debit);
                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);
                    if($this->db->affected_rows()>0){
                        $affectedRow++;
                        $get_account_bal4 = $this->VehRtn_model->get_acc_bal('CASH');
                        $current_bal4 = $get_account_bal4->$mm;
                        $newDRAmt = $current_bal4 + $PayAmt;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $FY);
                        $this->db->where('AccountID', 'CASH');
                        $this->db->update(db_prefix() . 'accountbalances', [
                                                    $mm => $newDRAmt,
                                                ]);
                    }
                    $ord_no2++;
                }
            }
        
        if($affectedRow > 0){
            if($selected_company == 1){
                $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');
            }elseif($selected_company == 2){
                $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');
            }elseif($selected_company == 3){
                $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');
            }elseif($selected_company == 4){
                $next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');
            }
            $new_vehicle_return_Numbar = 'VRT'.$FY.$next_vehicle_returnNumber;
            echo json_encode($new_vehicle_return_Numbar);
            die;
        }else{
            echo json_encode(false);
            die;
        }
    }
    
    
    
    
}