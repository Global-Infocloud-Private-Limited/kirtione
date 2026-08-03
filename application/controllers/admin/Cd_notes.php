<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cd_notes extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('cd_notes_model');
    }

    /* List all available items */
    public function index()
    {
        if (!has_permission_new('cd_notes', '', 'view')) {
            access_denied('TCS Master');
        }
        
        $data['cd_notes'] = $this->cd_notes_model->get_cd_notes_list();

        $data['title'] = "CD Notes";
        $this->load->view('admin/cd_notes/manage', $data);
    }
    
    public function accountlist()
    { 
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->cd_notes_model->getaccounts($postData);
        echo json_encode($data);
    }
    
    public function GetAccountDetailsByID()
    {
        // POST data
        $postData = $this->input->post();
        $AccountID = $postData['AccountID'];
        // Get data
        $Account_data = $this->cd_notes_model->GetAccountDetailsByID($AccountID);
        if($Account_data->CustomerType !='1'){
            $AccountName = $Account_data->business_name;
            $GST_Aadhar = $Account_data->gstin;
            $state = $Account_data->GSTState;
            $state_Short_name = $Account_data->GstState_short_name;
            $address = $Account_data->address;
            $AccountType = $Account_data->CustomerType;
        }else{
            $AccountName = $Account_data->firstname.' '.$Account_data->middlename.' '.$Account_data->lastname;
            $GST_Aadhar = $Account_data->aadhaar_number;
            if($Account_data->AState_short_name == null){
                $state_Short_name = $Account_data->stateshort_name;
            }else{
                $state_Short_name = $Account_data->AState_short_name;
            }
            $state = $Account_data->state;
            
            $address = $Account_data->house.' '.$Account_data->street.' '.$Account_data->loc.' '.$Account_data->accounts->po.' '.$Account_data->subdist.' '.$Account_data->dist.' '.$Account_data->state.' - '.$Account_data->pincode;
            $AccountType = $Account_data->CustomerType;
        }
        $AccountDetails = array(
            "AccountID"=>$AccountID,
            "AccountName"=>$AccountName,
            "GST_Aadhar"=>$GST_Aadhar,
            "state"=>$state,
            "state_short_code"=>$state_Short_name,
            "address"=>$address,
            "AccountType"=>$AccountType
        );
        echo json_encode($AccountDetails);
    }
    public function load_data_for_cd_notes()
    {
        $data = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date')
          );
      $data = $this->cd_notes_model->load_data_for_cd_notes($data);
      echo json_encode($data);
    }
  
  
    public function itemlist()
    {
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->cd_notes_model->getitems($postData);
        echo json_encode($data);
    }
  
    public function itemlistDetails(){
        
        // POST data
        $postData = $this->input->post();
    
        // Get data
        $data = $this->cd_notes_model->getitemsDetails($postData);
    
        echo json_encode($data);
    }
    
  public function bill_list(){
        
    // POST data
    $postData = $this->input->post();

    // Get data
    $data = $this->cd_notes_model->getpending_bills($postData);

    echo json_encode($data);
  }
  
    public function edit($id = '')
    {
        if ($this->input->post()) {
            if (!has_permission_new('cd_notes', '', 'edit')) {
                access_denied('CD Note');
            }
            $data = $this->input->post();
            $success = $this->cd_notes_model->update_cd_notes_return($data);
            if ($success == "true") {
                set_alert('success', 'CDNoted update Successfully..');
                redirect(admin_url('cd_notes/edit/'.$data["ex_credit_noteid"]));
            }
               
        }
       
        if($id != ''){
            $data['cd_notes_details'] = $this->cd_notes_model->get_cdnotes_details($id);
            $data['party_details'] = $this->cd_notes_model->get_gatemaster_details($data['cd_notes_details']->TransID);
           
        }
        $this->load->model('sale_reports_model');
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        $data['cd_notes'] = $this->cd_notes_model->get_cd_notes_list();
        $data['title'] = "CD Notes";
        $this->load->view('admin/cd_notes/manage', $data);
    }

    
    
    public function add()
    {
        if ($this->input->post()) 
        {
            $data = $this->input->post();
        
            if($data["act_name"] == "" || $data["act_name"] == null || $data["net_total_val"]=="0.00")
            {
                set_alert('warning', "Somthing went wrong..");
                redirect(admin_url('cd_notes'));
            }
            $selected_company = $this->session->userdata('root_company');
           
            $new_creditNumber = get_option('next_credit_number_for_kirti');
            $new_debitNumber = get_option('next_debit_number_for_kirti');
            $fy = $this->session->userdata('finacial_year');
            $date = $data['credit_note_date'];
            $transdate = to_sql_date($date)." ".date('H:i:s');
            if($data["type_select"]== "credit"){
                $Billno = "CR".$fy.$new_creditNumber;
                $ttype = "C";
            }else{
                $Billno = "DR".$fy.$new_debitNumber;
                $ttype = "D";
            }
            $roundoff = round($data["net_total_val"]);
            $rnd_amt = $roundoff - $data["net_total_val"];
        
        
            $cd_notes = array(
                "FY"=>$fy,
                "plantid"=>$selected_company,
                "BT"=>$ttype,
                "Billno"=>$Billno,
                "Transdate"=>$transdate,
                "AccountID"=>$data["act_name"],
                "SaleAmt"=>$data["gross_total_val"],
                "cgstamt"=>$data["cgst_total_val"],
                "sgstamt"=>$data["sgst_total_val"],
                "igstamt"=>$data["igst_total_val"],
                "BillAmt"=>$data["net_total_val"],
                "RndAmt"=>round($data["net_total_val"]),
                "passedfrom"=>"SALESRECEIPT",
                "Userid"=>$this->session->userdata('username'),
                "narration"=>$data["narration"],
            );
            $this->db->insert(db_prefix() . 'cdnote', $cd_notes);
            (int) $count = $data["countof_record"]; 
            for($i=1; $i<$count; $i++) 
            { 
                $itemid = "item_code".$i;
                $hsn_val = "hsn_val".$i;
                $cgst_per_val = "cgst_per_val".$i;
                $cgst_amt_val = "cgst_amt_val".$i;
                $sgst_per_val = "sgst_per_val".$i;
                $sgst_amt_val = "sgst_amt_val".$i;
                $igst_per_val = "igst_per_val".$i;
                $igst_amt_val = "igst_amt_val".$i;
                $total_amt_val = "total_amt_val".$i;
                $sale_id = "sale_id".$i;
                $paidamth = "paidamth".$i;
                $TransID = $data[$sale_id];
                $cd_notes_details = array(
                    "fy"=>$fy,
                    "plantid"=>$selected_company,
                    "billno"=>$Billno,
                    "transdate"=>$transdate,
                    "ttype"=>$ttype,
                    "AccountID"=>$data["act_name"],
                    "itemid"=>$data[$itemid],
                    "hsncode"=>$data[$hsn_val],
                    "rate"=>$data[$paidamth],
                    "qty"=>"0.00",
                    "cgst"=>$data[$cgst_per_val],
                    "cgstamt"=>$data[$cgst_amt_val],
                    "sgst"=>$data[$sgst_per_val],
                    "sgstamt"=>$data[$sgst_amt_val],
                    "igst"=>$data[$igst_per_val],
                    "igstamt"=>$data[$igst_amt_val],
                    "amount"=>$data[$total_amt_val],
                    "ordinalno"=>$i,
                    "TransID"=>$data[$sale_id],
                );
                $this->db->insert(db_prefix() . 'cdnotehistory', $cd_notes_details); 
            }
            $this->db->where('plantid', $selected_company);
            $this->db->where('FY', $fy);
            $this->db->where('Billno', $Billno);
            $this->db->update(db_prefix() . 'cdnote', [
                                        'TransID' => $TransID,
                                    ]);
        //die;
            if($data["type_select"]== "credit"){
                $cus_tt = "C";
                $comp_tt = "D";
            }else{
                $cus_tt = "D";
                $comp_tt = "C";
            }
            $PurchaseDetails = $this->cd_notes_model->GetPurchaseDetails($TransID);
            $PurchID = $PurchaseDetails->PurchID; 
            $BookingID = $PurchaseDetails->BookingID; 
            $PartyID = $PurchaseDetails->PartyID; 
            $CenterID = $PurchaseDetails->CenterID; 
            $CommodityID = $PurchaseDetails->ItemID; 
            $narretion = "By CDNote ".$Billno."/Quality and other deductions Against ".$PurchID." BookingID ".$BookingID." / GateINID ".$TransID;
            $ord_no = 1;
            $credit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$Billno,
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$cus_tt,
                "AccountID"=>$data['act_name'],
                "Amount"=>round($data["net_total_val"]),
                "Narration"=>$narretion,
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
                );
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
            $ord_no++;
        
        
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$Billno,
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$comp_tt,
                "AccountID"=>"CLAIM",
                "Amount"=>$data['gross_total_val'],
                "Narration"=>$narretion,
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            if($data['igst_total_val']=="0.00"){
                
                $debit_ledger_sgst = array(
                    "FY"=>$fy,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$Billno,
                    "PartyID"=>$PartyID,
                    "CenterID"=>$CenterID,
                    "CommodityID"=>$CommodityID,
                    "EntryFor"=>'2',
                    "Transdate"=>$transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>$comp_tt,
                    "AccountID"=>"SGST",
                    "Amount"=>$data['sgst_total_val'],
                    "Narration"=>$data['narration']."/".$Billno,
                    "PassedFrom"=>"CDNOTE",
                    "OrdinalNo"=>$ord_no,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_sgst);
                $ord_no++;
                
                $debit_ledger_cgst = array(
                    "FY"=>$fy,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$Billno,
                    "PartyID"=>$PartyID,
                    "CenterID"=>$CenterID,
                    "CommodityID"=>$CommodityID,
                    "EntryFor"=>'2',
                    "Transdate"=>$transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>$comp_tt,
                    "AccountID"=>"CGST",
                    "Amount"=>$data['cgst_total_val'],
                    "Narration"=>$data['narration']."/".$Billno,
                    "PassedFrom"=>"CDNOTE",
                    "OrdinalNo"=>"1",
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_cgst);
                $ord_no++;
                
            }else{
                
                $debit_ledger_igst = array(
                    "FY"=>$fy,
                    "PlantID"=>$selected_company,
                    "VoucherID"=>$Billno,
                    "PartyID"=>$PartyID,
                    "CenterID"=>$CenterID,
                    "CommodityID"=>$CommodityID,
                    "EntryFor"=>'2',
                    "Transdate"=>$transdate,
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "TType"=>$comp_tt,
                    "AccountID"=>"IGST",
                    "Amount"=>$data['igst_total_val'],
                    "Narration"=>$data['narration']."/".$Billno,
                    "PassedFrom"=>"CDNOTE",
                    "OrdinalNo"=>$ord_no,
                    "UserID"=>$this->session->userdata('username'),
                );
                $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_igst);
                $ord_no++;
            }
            $debit_ledger_roundoff = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$Billno,
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"ROUNDOFF",
                "Amount"=>$rnd_amt,
                "Narration"=>$data['narration']."/".$Billno,
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_roundoff);
            $ord_no++;
        
            if($data["type_select"]== "credit"){
                $this->increment_next_number();
                set_alert('success', 'Credit Note added Successfully..');
            }else{
                $this->increment_next_number2();
                set_alert('success', 'Debit Note added Successfully..');
            }
            redirect(admin_url('cd_notes'));
        }
    }
    
    /* Get item by id / ajax */
    public function get_bill_id($item_hsn,$act_code,$CDType)
    {
        if ($this->input->is_ajax_request()) {
            $item = $this->cd_notes_model->get_bill($item_hsn,$act_code,$CDType);
            echo json_encode($item);
        }
    }
    
    /* Get bill details by id / ajax */
    public function get_bill_detail($hsn_code,$bill_id,$item_code)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->cd_notes_model->get_bill_details($hsn_code,$bill_id,$item_code);
            
            echo json_encode($item);
        }
    }
    
    public function get_acc_bal($id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->where('AccountID', $id);

        return $this->db->get(db_prefix() . 'accountbalances')->row();
    }
    
    /**
     * @since  2.7.0
     *
     * Increment the Receipts next nubmer
     *
     * @return void
     */
    public function increment_next_number()
    {
        // Update next Receipts number in settings
       $FY = $this->session->userdata('finacial_year'); 
       $selected_company = $this->session->userdata('root_company');
            /*if($selected_company == 1){
                $this->db->where('name', 'next_credit_number_for_cspl');
            }elseif($selected_company == 2){
                $this->db->where('name', 'next_credit_number_for_cff');
            }elseif($selected_company == 3){
                $this->db->where('name', 'next_credit_number_for_cbu');
            }elseif($selected_company == 4){
                $this->db->where('name', 'next_credit_number_for_cbupl');
            }*/
        $this->db->where('name', 'next_credit_number_for_kirti');
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }
    public function increment_next_number2()
    {
        // Update next Receipts number in settings
        $FY = $this->session->userdata('finacial_year');
       $selected_company = $this->session->userdata('root_company');
            /*if($selected_company == 1){
                $this->db->where('name', 'next_debit_number_for_cspl');
            }elseif($selected_company == 2){
                $this->db->where('name', 'next_debit_number_for_cff');
            }elseif($selected_company == 3){
                $this->db->where('name', 'next_debit_number_for_cbu');
            }elseif($selected_company == 4){
                $this->db->where('name', 'next_debit_number_for_cbupl');
            }*/
        $this->db->where('name', 'next_debit_number_for_kirti');
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission_new('cd_notes', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                
                    if (!has_permission('cd_notes', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    $id      = $this->tcs_master_model->add($data);
                    
                    if ($id) {
                        set_alert('success', 'TCS added Successfully..');
                        redirect(admin_url('tcs_master'));
                    }
               
            }
        }
    }

    

    

    /* Delete item*/
    public function delete($id)
    {
        if (!has_permission_new('cd_notes', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('vehicles'));
        }

        $response = $this->tcs_master_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', 'HSN Code Delected Successfully..');
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        redirect(admin_url('hsn_master'));
    }

    
    
   
}
