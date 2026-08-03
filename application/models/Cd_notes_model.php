<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cd_notes_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
   
    
    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get_ganeral_account($id = '')
    {
        $selected_company = $this->session->userdata('root_company');
        $subgroup = array('60001007','60001008','50003001');
        $this->db->where('active', 1);
        $this->db->where('PlantID', $selected_company);
        $this->db->where_in('SubActGroupID',$subgroup);
        $this->db->order_by('company', 'ASC');
        $accounts = $this->db->get(db_prefix() . 'clients')->result_array();
        return $accounts;
    }
    
    
    function getaccounts($postData)
    {
        $KYCStatus = 6; // KYC Done
        $response = array();
        $selected_company = $this->session->userdata('root_company');
        $where_clients = '';
        if(isset($postData['search']) ){
            $q = $postData['search'];
            
            $this->db->select(db_prefix() . 'clients.company,tblclients.AccountID,tblcontacts.firstname,tblcontacts.lastname,tblcontacts.middlename');
            $where_clients .= '(tblclients.company LIKE "%' . $q . '%" ESCAPE \'!\' OR tblcontacts.firstname LIKE "%' . $q. '%" ESCAPE \'!\' OR tblcontacts.lastname LIKE "%' . $q . '%" ESCAPE \'!\' OR ' . db_prefix() . 'clients.AccountID LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND (' . db_prefix() . 'clients.SubActGroupID = 1000006 OR ' . db_prefix() . 'clients.SubActGroupID = 1000016)';
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND tblcontacts.PlantID = tblclients.PlantID');
            $this->db->where($where_clients);
            $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'clients.KYCStatus', $KYCStatus);
            $records = $this->db->get(db_prefix() . 'clients')->result();
            
            foreach($records as $row ){
                $lebel = $row->firstname.' '.$row->middlename.' '.$row->lastname.'('.$row->AccountID.')';
                $response[] = array("label"=>$lebel,"value"=>$row->AccountID);
            }
        }
        return $response;
    }
   
  
  public function load_data_for_cd_notes($data)
     {  
        $from_date = to_sql_date($data["from_date"]).' 00:00:00';
        $to_date = to_sql_date($data["to_date"]).' 23:59:59';
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
      /*
        $sql1 = '('.db_prefix().'cdnote.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")  AND '.db_prefix().'cdnote.FY = "'.$fy.'" AND '.db_prefix().'cdnote.plantid = "'.$selected_company.'" ORDER BY Billno DESC';
        
        $sql ='SELECT '.db_prefix().'cdnote.*,  
        (SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'cdnote.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
        (SELECT GROUP_CONCAT(state SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'cdnote.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as state
        FROM '.db_prefix().'cdnote WHERE '.$sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;*/
        
        $this->db->select('tblclients.CustomerType,tblcdnote.AccountID,tblcdnote.TransID,tblcdnote.Billno,tblcdnote.Transdate,tblcdnote.SaleAmt,tblcdnote.cgstamt,tblcdnote.sgstamt,tblcdnote.igstamt,tblcdnote.BillAmt,tblcdnote.BT,
        tblcontacts.firstname,tblcontacts.middlename,tblcontacts.lastname,tblGstRecord.gstin,tblGstRecord.state AS GSTState,tblGstRecord.business_name,
        tblcontacts.aadhaar_number,tblAadharDetails.state As AadharState');
        $this->db->join('tblclients', 'tblclients.AccountID = tblcdnote.AccountID AND tblclients.PlantID = tblcdnote.PlantID');
        $this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblcdnote.AccountID AND tblGstRecord.IsPrimary = "1"' ,"LEFT");
        $this->db->join('tblAadharDetails', 'tblAadharDetails.AccountID = tblcdnote.AccountID AND tblAadharDetails.Type = "1"' ,"LEFT");
        $this->db->join('tblcontacts', 'tblcontacts.AccountID = tblcdnote.AccountID AND tblcontacts.PlantID = tblcdnote.PlantID');
        //$this->db->where('tblcdnote.AccountID', $AccountID);
        $this->db->where('tblcdnote.plantid', $selected_company);
        $this->db->where('tblcdnote.FY', $fy);
        $this->db->order_by('tblcdnote.Billno', 'DESC');
        $this->db->where('tblcdnote.Transdate BETWEEN "' . $from_date . '" AND "' . $to_date.'"');
        $result = $this->db->get(db_prefix() . 'cdnote')->result_array();
        $CDListArray = array();
        foreach($result as $key=>$val){
            if($val["CustomerType"] !='1'){
                $AccountName = $val["business_name"];
                $GST_Aadhar = $val["gstin"];
                $state = $val["GSTState"];
            }else{
                $AccountName = $val["firstname"].' '.$val["middlename"].' '.$val["lastname"];
                $GST_Aadhar = $val["aadhaar_number"];
                $state = $val["AadharState"];
            }
            if($val["BT"] == "C"){
                $bt = "CreditNote";
            }else if($val["BT"] == "D"){
                $bt = "DebitNote";
            }
            $new_array = array(
                "Billno"=>$val["Billno"],
                "bt"=>$bt,
                "Transdate"=>_d(substr($val["Transdate"],0,10)),
                "TransID"=>$val["TransID"],
                "AccountName"=>$AccountName,
                "GST_Aadhar"=>$GST_Aadhar,
                "state"=>$state,
                "SaleAmt"=>$val["SaleAmt"],
                "sgstamt"=>$val["sgstamt"],
                "cgstamt"=>$val["cgstamt"],
                "igstamt"=>$val["igstamt"],
                "BillAmt"=>$val["BillAmt"],
            );
            array_push($CDListArray,$new_array);
        }
        return $CDListArray;
    }
  
    function getitems($postData)
    {
     $response = array();
     $selected_company = $this->session->userdata('root_company');

     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       $type_select = $postData['type_select'];
       $where_item = '';
       $subgroup = array('9','20','36');
       $where_item .= '('.db_prefix() .'items.ItemName LIKE "%' . $q . '%" ESCAPE \'!\' OR ItemID LIKE "%' . $q . '%" ESCAPE \'!\') ';
        $where_item2 = 'SEELCT TransID';
       
       $fy = $this->session->userdata('finacial_year');
       $lastFY = $fy -1;
       $FYs = array($fy,$lastFY);
       
       $this->db->select(db_prefix() .'items.*, ' . db_prefix() . 'hsn.hsndesc,'.db_prefix() . 'taxes.taxrate');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'hsn', '' . db_prefix() . 'hsn.name = ' . db_prefix() . 'items.hsn_code','LEFT');
        $this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax','LEFT');
        //$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.ItemID = ' . db_prefix() . 'items.ItemID AND ' . db_prefix() . 'history.PlantID = ' . db_prefix() . 'items.PlantID');
        $this->db->where($where_item);
        if($type_select == "credit"){
            $this->db->where_not_in(db_prefix() . 'items.subgroup_id',$subgroup);
        }else{
            
        }
        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
        $records = $this->db->get()->result();
        
        foreach($records as $row ){
          $response[] = array("label"=>$row->ItemName,"value"=>$row->ItemID,"hsn_code"=>$row->hsn_code,"tax"=>$row->taxrate,"hsndesc"=>$row->hsndesc,"trans_id"=>$row->TransID);
       }

     }
     return $response;
    }
  
  function getitemsDetails($postData){

     $response = array();
     $selected_company = $this->session->userdata('root_company');

     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       $type_select = $postData['type_select'];
       $where_item = '';
       $subgroup = array('9','20','36');
       $where_item .= '('.db_prefix() .'items.ItemName LIKE "%' . $q . '%" ESCAPE \'!\' OR ItemID LIKE "%' . $q . '%" ESCAPE \'!\') ';
        $where_item2 = 'SEELCT TransID';
       
       $fy = $this->session->userdata('finacial_year');
       $lastFY = $fy -1;
       $FYs = array($fy,$lastFY);
       
       $this->db->select(db_prefix() .'items.*, ' . db_prefix() . 'hsn.hsndesc,'.db_prefix() . 'taxes.taxrate');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'hsn', '' . db_prefix() . 'hsn.name = ' . db_prefix() . 'items.hsn_code','LEFT');
        $this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax','LEFT');
        //$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.ItemID = ' . db_prefix() . 'items.ItemID AND ' . db_prefix() . 'history.PlantID = ' . db_prefix() . 'items.PlantID');
        $this->db->where(db_prefix() .'items.ItemID',$q);
        if($type_select == "credit"){
            $this->db->where_not_in(db_prefix() . 'items.subgroup_id',$subgroup);
        }else{
            
        }
        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
        $records = $this->db->get()->result();
        
        foreach($records as $row ){
          $response[] = array("label"=>$row->ItemName,"value"=>$row->ItemID,"hsn_code"=>$row->hsn_code,"tax"=>$row->taxrate,"hsndesc"=>$row->hsndesc,"trans_id"=>$row->TransID);
       }
     }
     return $response;
  }
  
    public function get_cd_notes_list()
    {
        
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $this->db->select(db_prefix() . 'cdnote.*,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.state');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'cdnote.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'cdnote.plantid');
        
        $this->db->where(db_prefix() . 'cdnote.plantid', $selected_company);
        $this->db->where(db_prefix() . 'cdnote.FY', $fy);
        $this->db->order_by(db_prefix() . 'cdnote.Transdate', "DESC");

        return $this->db->get(db_prefix() . 'cdnote')->result_array();
        
        
    }
    
    function get_bill($item_hsn,$act_code,$CDType)
    {
        $response = array();
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $TType = array("S","P");
        $lastFY = $fy -1;
        $FYs = array($fy,$lastFY);
        
        if($CDType == "purchasecd"){
            $this->db->select(db_prefix() . 'history.*,'.db_prefix() . 'purchasemaster.PurchID,'.db_prefix() . 'purchasemaster.Invoicedate,'.db_prefix() . 'purchasemaster.Invoiceno, SUM('.db_prefix() . 'history.ChallanAmt) as sum_total');
        }else{
            $this->db->select(db_prefix() . 'history.*, SUM('.db_prefix() . 'history.NetChallanAmt) as sum_total');
        }
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
        if($CDType == "purchasecd"){
            $this->db->join(db_prefix() . 'purchasemaster', '' . db_prefix() . 'purchasemaster.FY = ' . db_prefix() . 'history.FY AND ' . db_prefix() . 'purchasemaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'purchasemaster.PurchID = ' . db_prefix() . 'history.TransID');
            
        }else{
           $this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.FY = ' . db_prefix() . 'history.FY AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'salesmaster.SalesID = ' . db_prefix() . 'history.TransID');
        }
        $this->db->where(db_prefix() . 'history.TransID IS NOT NULL');
        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);
        $this->db->where_in(db_prefix() . 'history.FY', $FYs);
        $this->db->where(db_prefix() . 'history.AccountID', $act_code);
        $this->db->where(db_prefix() . 'items.hsn_code', $item_hsn);
        $this->db->where_in(db_prefix() . 'history.TType', $TType);
       
        $this->db->group_by(db_prefix() . 'history.TransID');
        $this->db->order_by(db_prefix() . 'history.TransID', "DESC");
       
        $records = $this->db->get(db_prefix() . 'history')->result();
        return $records;
    }
  
    public function get_cdnotes_details($id = '')
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select('*');
        $this->db->from(db_prefix() . 'cdnote');
        $this->db->where(db_prefix() . 'cdnote.Billno', $id);
        $this->db->where(db_prefix() . 'cdnote.FY', $fy);
        $this->db->where(db_prefix() . 'cdnote.plantid', $selected_company);
        $cd_notes = $this->db->get()->row();
        if ($cd_notes) {
            $item = $this->get_cd_notes_items($cd_notes->Billno,$selected_company,$fy);
            foreach ($item as $key => $value) {
                $needle = "PO";
                $haystack = $value["TransID"];
                if (strpos($haystack, $needle) !== false) {
                    $selectType = "purchasecd";
                }else{
                    $selectType = "salecd";
                }
            }
            $account = $this->GetAccountDetailsByID($cd_notes->AccountID);
            $postData = array(
                "account_id"=>$cd_notes->AccountID,
            );
            $purchased_item          = $this->getsale_item_list($postData);
            
            $cd_notes->selectType = $selectType;
            $cd_notes->items = $item;
            $cd_notes->accounts = $account;
            $cd_notes->purchased_item = $purchased_item;
        }
        return $cd_notes;
    }
    
    public function get_cd_notes_items($cd_notes_id,$PlantID,$FY)
    {
        $this->db->select(db_prefix() . 'cdnotehistory.*,'.db_prefix() . 'items.ItemName');
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'cdnotehistory.itemid AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'cdnotehistory.plantid');
        $this->db->where(db_prefix() . 'cdnotehistory.billno', $cd_notes_id);
        $this->db->where(db_prefix() . 'cdnotehistory.plantid', $PlantID);
        $this->db->where(db_prefix() . 'cdnotehistory.fy', $FY);
        return $this->db->get(db_prefix() . 'cdnotehistory')->result_array();
    }
    
    public function GetAccountDetailsByID($AccountID)
    {
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblclients.CustomerType,tblclients.state AS stateshort_name,tblclients.AccountID,tblcontacts.firstname,tblcontacts.middlename,tblcontacts.lastname,tblGstRecord.gstin,tblGstRecord.state AS GSTState,tblGstRecord.business_name,tblGstRecord.address,tblGstRecord.state_code AS GstState_short_name,
        tblcontacts.aadhaar_number,tblAadharDetails.state,tblAadharDetails.dist,tblAadharDetails.subdist,tblAadharDetails.po,tblAadharDetails.loc,tblAadharDetails.street,tblAadharDetails.house,tblAadharDetails.pincode,Atblstate.short_name AS AState_short_name');
        $this->db->join(db_prefix() . 'GstRecord', '' . db_prefix() . 'GstRecord.AccountID = ' . db_prefix() . 'clients.AccountID' ,"LEFT");
        $this->db->join(db_prefix() . 'AadharDetails', '' . db_prefix() . 'AadharDetails.AccountID = ' . db_prefix() . 'clients.AccountID AND tblAadharDetails.Type = "1"' ,"LEFT");
        $this->db->join(db_prefix() . 'AadharDetails A2', 'A2.AccountID = ' . db_prefix() . 'clients.AccountID AND A2.Type = "2"' ,"LEFT");
        $this->db->join(db_prefix() . 'xx_statelist AS Atblstate', 'Atblstate.short_name = A2.state' ,"LEFT");
        $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND tblcontacts.PlantID = tblclients.PlantID');
        $this->db->where('tblclients.AccountID', $AccountID);
        $this->db->where('tblclients.PlantID', $selected_company);
        return $this->db->get(db_prefix() . 'clients')->row();
      
    }
    
    public function get_gatemaster_details($TransID)
    {
        $this->db->select(db_prefix() . 'history.TransID,' . db_prefix() . 'PlantMaster.PlantName,' . db_prefix() . 'PlantMaster.address,tblCenterMaster.CenterName');
        $this->db->where('tblhistory.TransID', $TransID);
        $this->db->join(db_prefix() . 'PlantMaster', '' . db_prefix() . 'PlantMaster.PlantID = ' . db_prefix() . 'history.PartyID');
        $this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'history.CenterID');
       return $this->db->get(db_prefix() . 'history')->row();
    }
    
    function getsale_item_list($postData){

     $response = array();
     $selected_company = $this->session->userdata('root_company');
     $fy = $this->session->userdata('finacial_year');

     
       $this->db->select(db_prefix() . 'history.ItemID');
       $this->db->distinct();
       //$this->db->where("TransID like '%".$postData['search']."%' ");
       $this->db->where(db_prefix() . 'history.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'history.FY', $fy);
       $this->db->where(db_prefix() . 'history.AccountID', $postData['account_id']);
       $this->db->where(db_prefix() . 'history.BillID !=', null);
       //$this->db->where(db_prefix() . 'history.ItemID', $postData['ItemID']);
       $records = $this->db->get(db_prefix() . 'history')->result();
      
        $record_string = '';
       foreach($records as $row ){
         // $response[] = array("label"=>$row->TransID,"value"=>$row->OrderID,"order_amt"=>$row->NetOrderAmt);
         $record_string .= $row->ItemID.",";
       }


     return $record_string;
  }
  
  function get_bill_details($hsn_code,$bill_id,$ItemID){

     $response = array();
     $selected_company = $this->session->userdata('root_company');
     $fy = $this->session->userdata('finacial_year');

     
       $this->db->select(db_prefix() . 'items.ItemID');
       $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'items.hsn_code', $hsn_code);
       $records = $this->db->get(db_prefix() . 'items')->result_array();
       $item_codes = array();
        
       foreach ($records as $key => $value) {
            # code...
            array_push($item_codes, $value["ItemID"]);
        }
        
       $this->db->select('SUM(ChallanAmt) as total_amount');
       $this->db->where(db_prefix() . 'history.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'history.FY', $fy);
       $this->db->where_in(db_prefix() . 'history.ItemID', $item_codes);
       $this->db->where(db_prefix() . 'history.TType', "O");
       $this->db->where(db_prefix() . 'history.TransID', $bill_id);
       $sum_amt = $this->db->get(db_prefix() . 'history')->row();
       
      
       $this->db->select('*');
       $this->db->where(db_prefix() . 'history.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'history.FY', $fy);
       $this->db->where(db_prefix() . 'history.ItemID', $ItemID);
       $this->db->where(db_prefix() . 'history.TransID', $bill_id);
       $bill_details = $this->db->get(db_prefix() . 'history')->row();
       $bill_details->sum_amt = $sum_amt->total_amount;

     return $bill_details;
  }
  
  function getpending_bills($postData)
  {

     $response = array();
     $selected_company = $this->session->userdata('root_company');
     $fy = $this->session->userdata('finacial_year');

     
       $this->db->select('*');
       //$this->db->where("TransID like '%".$postData['search']."%' ");
       
       $this->db->where(db_prefix() . 'history.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'history.FY', $fy);
       $this->db->where(db_prefix() . 'history.AccountID', $postData['account_id']);
       $this->db->where(db_prefix() . 'history.ItemID', $postData['ItemID']);
       $records = $this->db->get(db_prefix() . 'history')->row();
      
     return $records;
  }
  
  
  
  public function get_accounts_data_model($id)
{
    $selected_company = $this->session->userdata('root_company');
    $fy = 21;
	
        $sql = 'SELECT '.db_prefix().'salesmaster.SalesID, '.db_prefix().'salesmaster.Transdate, '.db_prefix().'salesmaster.BillAmt, '.db_prefix().'salesmaster.AccountID,
        '.db_prefix().'clients.company FROM '.db_prefix().'salesmaster
 INNER JOIN '.db_prefix().'clients
 ON '.db_prefix().'clients.AccountID = '.db_prefix().'salesmaster.AccountID
 WHERE '.db_prefix().'salesmaster.AccountID = '.$id.' AND      
 '.db_prefix().'salesmaster.PlantID = '.$selected_company.' AND '.db_prefix().'salesmaster.FY = '.$fy.' AND '.db_prefix().'clients.PlantID = '.$selected_company;
 $result = $this->db->query($sql)->result_array();
 return $result;
}

    function GetCDDetails($BillID)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->select('tblcdnote.TransID,tblpurchasemaster.TransID AS GateINID,tblpurchasemaster.PurchID');
        $this->db->join(db_prefix() . 'purchasemaster', '' . db_prefix() . 'purchasemaster.PurchID = ' . db_prefix() . 'cdnote.TransID');
        $this->db->where(db_prefix() . 'cdnote.plantid', $selected_company);
        $this->db->where(db_prefix() . 'cdnote.FY', $fy);
        $this->db->where(db_prefix() . 'cdnote.Billno', $BillID);
        $records = $this->db->get(db_prefix() . 'cdnote')->row();
        return $records;
    }
    
    function GetPurchaseDetails($GateINID)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->select('tblGateMaster.Gate_in_ID,tblGateMaster.BookingID,tblGateMaster.PartyID,tblGateMaster.CenterID,tblGateMaster.ItemID,tblpurchasemaster.PurchID');
        $this->db->join(db_prefix() . 'purchasemaster', '' . db_prefix() . 'purchasemaster.TransID = ' . db_prefix() . 'GateMaster.Gate_in_ID');
        $this->db->where(db_prefix() . 'GateMaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'GateMaster.FY', $fy);
        $this->db->where(db_prefix() . 'GateMaster.Gate_in_ID', $GateINID);
        $records = $this->db->get(db_prefix() . 'GateMaster')->row();
        return $records;
    }
    
    public function update_cd_notes_return($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        if($data["act_name"] == "" || $data["act_name"] == null || $data["countof_record"]=="1")
        {
            set_alert('warning', "please add atleast one item..");
            redirect(admin_url('cd_notes/edit/'.$data["ex_credit_noteid"]));
        }
        
        if($data["type_select2"]== "credit"){
            $cus_tt = "C";
            $comp_tt = "D";
        }else{
            $cus_tt = "D";
            $comp_tt = "C";
        }
        
        $roundoff = round($data["net_total_val"]);
        $rnd_amt = $roundoff - $data["net_total_val"];
        (int) $count = $data["countof_record"]; 
        $ItCount = $count - 1;    
        /*echo $count;
        echo $roundoff;*/
        $new_record = $data["new_record"];
        $new_record = str_replace(" ,",'',$data["new_record"]);
        $new_record_array = explode(',', $new_record);
        
        $edit_record = $data["updated_record"];
        $edit_record = str_replace(" ,",'',$data["updated_record"]);
        $edit_record_array = explode(',', $edit_record);
       
        
        $date = $data['credit_note_date'];
        $transdate = to_sql_date($date)." ".date('H:i:s');
        
       
        $update_record = array(
            "AccountID"=>$data["act_name"],
            "SaleAmt"=>$data["gross_total_val"],
            "cgstamt"=>$data["cgst_total_val"],
            "sgstamt"=>$data["sgst_total_val"],
            "igstamt"=>$data["igst_total_val"],
            "RndAmt"=>$roundoff,
            "narration"=>$data["narration"],
            "BillAmt"=>$data["net_total_val"],
            'Transdate' =>$transdate,
            "UserID2"=>$this->session->userdata('username'),
            "Lupdate"=>date('Y-m-d H:i:s'),
        );

        $this->db->where('plantid', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->where('AccountID', $data['old_act_name']);
        $this->db->where('Billno', $data["ex_credit_noteid"]);
        $this->db->update(db_prefix() . 'cdnote', $update_record);
        
        $CDDetails = $this->GetCDDetails($data["ex_credit_noteid"]);
        $TransID = $CDDetails->GateINID;
        $PurchaseDetails = $this->GetPurchaseDetails($TransID);
        $PurchID = $PurchaseDetails->PurchID; 
        $BookingID = $PurchaseDetails->BookingID; 
        $PartyID = $PurchaseDetails->PartyID; 
        $CenterID = $PurchaseDetails->CenterID; 
        $CommodityID = $PurchaseDetails->ItemID; 
            
        $ord_no = 1;    
        // Delete previous Old AccoutID ledger
        $get_lastamount = $this->get_last_ledger_amt($data["ex_credit_noteid"],$data['old_act_name']);
            if($get_lastamount){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount->PlantID,
                    "FY"=>$get_lastamount->FY,
                    "Transdate"=>$get_lastamount->Transdate,
                    "TransDate2"=>$get_lastamount->TransDate2,
                    "VoucherID"=>$get_lastamount->VoucherID,
                    "AccountID"=>$get_lastamount->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount->TType,
                    "Amount"=>$get_lastamount->Amount,
                    "Narration"=>$get_lastamount->Narration,
                    "PassedFrom"=>$get_lastamount->PassedFrom,
                    "OrdinalNo"=>$get_lastamount->OrdinalNo,
                    "UserID"=>$get_lastamount->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', $data['old_act_name']);
            $this->db->LIKE('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            
        $credit_ledger = array(
            "FY"=>$fy,
            "PlantID"=>$selected_company,
            "VoucherID"=>$data["ex_credit_noteid"],
            "Transdate"=>$transdate,
            "TransDate2"=>date('Y-m-d H:i:s'),
            "TType"=>$cus_tt,
            "AccountID"=>$data['act_name'],
            "PartyID"=>$PartyID,
            "CenterID"=>$CenterID,
            "CommodityID"=>$CommodityID,
            "EntryFor"=>'2',
            "Amount"=>round($data["net_total_val"]),
            "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
            "PassedFrom"=>"CDNOTE",
            "OrdinalNo"=>$ord_no,
            "UserID"=>$this->session->userdata('username'),
        );
        $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
        $ord_no++;    
        
        // ledger and balance update for CLAIM Account        
            
            $get_lastamount3 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"CLAIM");
            
        // Delete previous CLAIM ledger
        
        if($get_lastamount3){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount3->PlantID,
                    "FY"=>$get_lastamount3->FY,
                    "Transdate"=>$get_lastamount3->Transdate,
                    "TransDate2"=>$get_lastamount3->TransDate2,
                    "VoucherID"=>$get_lastamount3->VoucherID,
                    "AccountID"=>$get_lastamount3->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount3->TType,
                    "Amount"=>$get_lastamount3->Amount,
                    "Narration"=>$get_lastamount3->Narration,
                    "PassedFrom"=>$get_lastamount3->PassedFrom,
                    "OrdinalNo"=>$get_lastamount3->OrdinalNo,
                    "UserID"=>$get_lastamount3->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "CLAIM");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$data["ex_credit_noteid"],
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$comp_tt,
                "AccountID"=>"CLAIM",
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Amount"=> $data["gross_total_val"],
                "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
        $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
        $ord_no++;
        
            $get_lastamount4 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"CLAIM");
            
    // Ledger & balances update for GST
    
        if($data['state_short_code']=="MH"){
            
            // credit IGST ledger balance
            $get_lastamount55 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"IGST");
            
        // ledger & balances for SGST 
        // credit SGST ledger &  balance 
            $get_lastamount5 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"SGST");
            
            // Debit SGST ledger & balance                            
            
            // Delete previous SGST ledger
            
            if($get_lastamount5){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount5->PlantID,
                    "FY"=>$get_lastamount5->FY,
                    "Transdate"=>$get_lastamount5->Transdate,
                    "TransDate2"=>$get_lastamount5->TransDate2,
                    "VoucherID"=>$get_lastamount5->VoucherID,
                    "AccountID"=>$get_lastamount5->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount5->TType,
                    "Amount"=>$get_lastamount5->Amount,
                    "Narration"=>$get_lastamount5->Narration,
                    "PassedFrom"=>$get_lastamount5->PassedFrom,
                    "OrdinalNo"=>$get_lastamount5->OrdinalNo,
                    "UserID"=>$get_lastamount5->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "SGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            $debit_ledger_for_sgst = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$data["ex_credit_noteid"],
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$comp_tt,
                "AccountID"=>"SGST",
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Amount"=>$data["sgst_total_val"],
                "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_for_sgst);    
            $ord_no++;
            
            $get_lastamount6 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"SGST");
            
                                        
            // ledger & balances for CGST 
            // credit CGST ledger &  balance  
            
            $get_lastamount7 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"CGST");
            
            // Debit CGST ledger & balance                            
            
            // Delete previous CGST ledger
            
            if($get_lastamount7){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount7->PlantID,
                    "FY"=>$get_lastamount7->FY,
                    "Transdate"=>$get_lastamount7->Transdate,
                    "TransDate2"=>$get_lastamount7->TransDate2,
                    "VoucherID"=>$get_lastamount7->VoucherID,
                    "AccountID"=>$get_lastamount7->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount7->TType,
                    "Amount"=>$get_lastamount7->Amount,
                    "Narration"=>$get_lastamount7->Narration,
                    "PassedFrom"=>$get_lastamount7->PassedFrom,
                    "OrdinalNo"=>$get_lastamount7->OrdinalNo,
                    "UserID"=>$get_lastamount7->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "CGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            $debit_ledger_for_sgst = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$data["ex_credit_noteid"],
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$comp_tt,
                "AccountID"=>"CGST",
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Amount"=>$data["sgst_total_val"],
                "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_for_sgst);    
            $ord_no++;
            
            $get_lastamount8 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"CGST");
            
            // Delete IGST previous ladger
            
            if($get_lastamount55){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount55->PlantID,
                    "FY"=>$get_lastamount55->FY,
                    "Transdate"=>$get_lastamount55->Transdate,
                    "TransDate2"=>$get_lastamount55->TransDate2,
                    "VoucherID"=>$get_lastamount55->VoucherID,
                    "AccountID"=>$get_lastamount55->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount55->TType,
                    "Amount"=>$get_lastamount55->Amount,
                    "Narration"=>$get_lastamount55->Narration,
                    "PassedFrom"=>$get_lastamount55->PassedFrom,
                    "OrdinalNo"=>$get_lastamount55->OrdinalNo,
                    "UserID"=>$get_lastamount55->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "IGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
        }else{
            
            // credit previous cgst amount 
            $get_lastamount99 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"CGST");
            
        // credit previous Sgst amount
            $get_lastamount999 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"SGST");
            
            
        // ledger & balances for IGST 
            // credit IGST ledger &  balance   
            $get_lastamount9 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"IGST");
            
            // DELETE IGST previous ledger
            
            if($get_lastamount9){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount9->PlantID,
                    "FY"=>$get_lastamount9->FY,
                    "Transdate"=>$get_lastamount9->Transdate,
                    "TransDate2"=>$get_lastamount9->TransDate2,
                    "VoucherID"=>$get_lastamount9->VoucherID,
                    "AccountID"=>$get_lastamount9->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount9->TType,
                    "Amount"=>$get_lastamount9->Amount,
                    "Narration"=>$get_lastamount9->Narration,
                    "PassedFrom"=>$get_lastamount9->PassedFrom,
                    "OrdinalNo"=>$get_lastamount9->OrdinalNo,
                    "UserID"=>$get_lastamount9->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "IGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // DELETE CGST previous ledger
            
            if($get_lastamount99){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount99->PlantID,
                    "FY"=>$get_lastamount99->FY,
                    "Transdate"=>$get_lastamount99->Transdate,
                    "TransDate2"=>$get_lastamount99->TransDate2,
                    "VoucherID"=>$get_lastamount99->VoucherID,
                    "AccountID"=>$get_lastamount99->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount99->TType,
                    "Amount"=>$get_lastamount99->Amount,
                    "Narration"=>$get_lastamount99->Narration,
                    "PassedFrom"=>$get_lastamount99->PassedFrom,
                    "OrdinalNo"=>$get_lastamount99->OrdinalNo,
                    "UserID"=>$get_lastamount99->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "CGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // DELETE SGST previous ledger
            
            if($get_lastamount999){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount999->PlantID,
                    "FY"=>$get_lastamount999->FY,
                    "Transdate"=>$get_lastamount999->Transdate,
                    "TransDate2"=>$get_lastamount999->TransDate2,
                    "VoucherID"=>$get_lastamount999->VoucherID,
                    "AccountID"=>$get_lastamount999->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount999->TType,
                    "Amount"=>$get_lastamount999->Amount,
                    "Narration"=>$get_lastamount999->Narration,
                    "PassedFrom"=>$get_lastamount999->PassedFrom,
                    "OrdinalNo"=>$get_lastamount999->OrdinalNo,
                    "UserID"=>$get_lastamount999->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "CDNOTE");
            $this->db->where('AccountID', "SGST");
            $this->db->where('VoucherID', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'accountledger');
            
            $debit_ledger_for_igst = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$data["ex_credit_noteid"],
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>$comp_tt,
                "AccountID"=>"IGST",
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Amount"=>$data["igst_total_val"],
                "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger_for_igst);
            $ord_no++;
            
            $get_lastamount10 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"IGST");
            
        }
        
        $get_lastamount11 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"ROUNDOFF");
        
        if($get_lastamount11){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount11->PlantID,
                    "FY"=>$get_lastamount11->FY,
                    "Transdate"=>$get_lastamount11->Transdate,
                    "TransDate2"=>$get_lastamount11->TransDate2,
                    "VoucherID"=>$get_lastamount11->VoucherID,
                    "AccountID"=>$get_lastamount11->AccountID,
                    "PartyID"=>$get_lastamount->PartyID,
                    "CenterID"=>$get_lastamount->CenterID,
                    "CommodityID"=>$get_lastamount->CommodityID,
                    "EntryFor"=>$get_lastamount->EntryFor,
                    "TType"=>$get_lastamount11->TType,
                    "Amount"=>$get_lastamount11->Amount,
                    "Narration"=>$get_lastamount11->Narration,
                    "PassedFrom"=>$get_lastamount11->PassedFrom,
                    "OrdinalNo"=>$get_lastamount11->OrdinalNo,
                    "UserID"=>$get_lastamount11->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('PassedFrom', "CDNOTE");
        $this->db->where('AccountID', "ROUNDOFF");
        $this->db->where('VoucherID', $data["ex_credit_noteid"]);
        $this->db->delete(db_prefix() . 'accountledger');
        
        
            $credit_ledger_for_rnd = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$data["ex_credit_noteid"],
                "Transdate"=>$transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"C",
                "AccountID"=>"ROUNDOFF",
                "PartyID"=>$PartyID,
                "CenterID"=>$CenterID,
                "CommodityID"=>$CommodityID,
                "EntryFor"=>'2',
                "Amount"=>$rnd_amt,
                "Narration"=>"By CDNote ".$data["ex_credit_noteid"]."/".$data["narration"],
                "PassedFrom"=>"CDNOTE",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger_for_rnd);
            $ord_no++;
            $get_lastamount12 = $this->get_last_ledger_amt($data["ex_credit_noteid"],"ROUNDOFF");
            
          
        //end balance and ledger entry code
        
    if($data["old_act_name"] == $data["act_name"]){
        
        for($i=1; $i<$count; $i++) { 
            $itemid = "item_code".$i;
            $hsn_val = "hsn_val".$i;
            $cgst_per_val = "cgst_per_val".$i;
            $cgst_amt_val = "cgst_amt_val".$i;
            $sgst_per_val = "sgst_per_val".$i;
            $sgst_amt_val = "sgst_amt_val".$i;
            $igst_per_val = "igst_per_val".$i;
            $igst_amt_val = "igst_amt_val".$i;
            $total_amt_val = "total_amt_val".$i;
            $paidamth = "paidamth".$i;
            $sale_id = "sale_id".$i;
            
            if($data['account_state']=="MH"){
                $gst = $data[$cgst_per_val] + $data[$cgst_per_val];
            }else{
                $gst = $data[$igst_per_val];
            }
                $edit_date = array(
                    "transdate"=>$transdate,
                    "UserID2"=>$this->session->userdata('username'),
                    "Lupdate"=>date('Y-m-d H:i:s'),
                );
                $this->db->where('plantid', $selected_company);
                $this->db->where('fy', $fy);
                $this->db->where('AccountID', $data['act_name']);
                $this->db->where('TransID', $data[$sale_id]);
                $this->db->where('itemid', $data[$itemid]);
                $this->db->where('billno', $data["ex_credit_noteid"]);
                $this->db->update(db_prefix() . 'cdnotehistory', $edit_date);
                
            if(in_array($data[$itemid], $new_record_array)){
               
                $new_record_details = array(
                    "plantid"=>$selected_company,
                    "fy"=>$fy,
                    "billno"=>$data["ex_credit_noteid"],
                    "transdate"=>$transdate,
                    "ttype"=>"C",
                    "AccountID"=>$data["act_name"],
                    "itemid"=>$data[$itemid],
                    "hsncode" =>$data[$hsn_val],
                    "rate" =>$data[$paidamth],
                    "gst" =>$gst,
                    "cgst" =>$data[$cgst_per_val],
                    "cgstamt" =>$data[$cgst_amt_val],
                    "sgst" =>$data[$cgst_per_val],
                    "sgstamt" =>$data[$cgst_amt_val],
                    "igstamt" =>$data[$igst_amt_val],
                    "igst" =>$data[$igst_per_val],
                    "amount" =>$data[$total_amt_val],
                    "ordinalno" =>$i,
                    "TransID" =>$data[$sale_id],
                );
                $this->db->insert(db_prefix() . 'cdnotehistory', $new_record_details);
            }
         
            if(in_array($data[$itemid], $edit_record_array)){
                
                $edit_record_details = array(
                    "rate" =>$data[$paidamth],
                    "gst" =>$gst,
                    "cgst" =>$data[$cgst_per_val],
                    "cgstamt" =>$data[$cgst_amt_val],
                    "sgst" =>$data[$cgst_per_val],
                    "sgstamt" =>$data[$cgst_amt_val],
                    "igstamt" =>$data[$igst_amt_val],
                    "igst" =>$data[$igst_per_val],
                    "amount" =>$data[$total_amt_val],
                    "transdate"=>$transdate,
                    "UserID2"=>$this->session->userdata('username'),
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    );
                
                $this->db->where('plantid', $selected_company);
                $this->db->where('fy', $fy);
                $this->db->where('AccountID', $data['act_name']);
                $this->db->where('TransID', $data[$sale_id]);
                $this->db->where('itemid', $data[$itemid]);
                $this->db->where('billno', $data["ex_credit_noteid"]);
                $this->db->update(db_prefix() . 'cdnotehistory', $edit_record_details);
            }
            
        } 
    }else{
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('AccountID', $data['old_act_name']);
            $this->db->where('billno', $data["ex_credit_noteid"]);
            $this->db->delete(db_prefix() . 'cdnotehistory');
            
        for($i=1; $i<$count; $i++) {
                
                $itemid = "item_code".$i;
                $hsn_val = "hsn_val".$i;
                $cgst_per_val = "cgst_per_val".$i;
                $cgst_amt_val = "cgst_amt_val".$i;
                $sgst_per_val = "sgst_per_val".$i;
                $sgst_amt_val = "sgst_amt_val".$i;
                $igst_per_val = "igst_per_val".$i;
                $igst_amt_val = "igst_amt_val".$i;
                $total_amt_val = "total_amt_val".$i;
                $paidamth = "paidamth".$i;
                $sale_id = "sale_id".$i;
                
                if($data['account_state']=="UP"){
                    $gst = $data[$cgst_per_val] + $data[$cgst_per_val];
                }else{
                    $gst = $data[$igst_per_val];
                }
                
            $new_record_details = array(
                "plantid"=>$selected_company,
                "fy"=>$fy,
                "billno"=>$data["ex_credit_noteid"],
                "transdate"=>$transdate,
                "ttype"=>"C",
                "AccountID"=>$data["act_name"],
                "itemid"=>$data[$itemid],
                "hsncode" =>$data[$hsn_val],
                "rate" =>$data[$paidamth],
                "gst" =>$gst,
                "cgst" =>$data[$cgst_per_val],
                "cgstamt" =>$data[$cgst_amt_val],
                "sgst" =>$data[$cgst_per_val],
                "sgstamt" =>$data[$cgst_amt_val],
                "igstamt" =>$data[$igst_amt_val],
                "igst" =>$data[$igst_per_val],
                "amount" =>$data[$total_amt_val],
                "ordinalno" =>$i,
                "TransID" =>$data[$sale_id],
            );
            $this->db->insert(db_prefix() . 'cdnotehistory', $new_record_details);
        }
    }
        return true;
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
    
    public function get_last_ledger_amt($id,$account_id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->LIKE('AccountID', $account_id);
        $this->db->LIKE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "CDNOTE");
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'hsn');
        if ($this->db->affected_rows() > 0) {
            

            log_activity('HSN Code Deleted [ID: ' . $id . ']');

            

            return true;
        }

        return false;
    }

    
    
}
