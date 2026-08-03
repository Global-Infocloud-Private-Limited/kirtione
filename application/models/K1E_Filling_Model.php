<?php

defined('BASEPATH') or exit('No direct script access allowed');

class K1E_Filling_Model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_purchase_data_for_table($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        if($data['bill_wise_type'] == 2){
          $this->db->select('tblK1purchasemaster.Transdate,tblK1purchasemaster.PurchID,sum(tblK1purchasemaster.Purchamt) as Purchamt,
          sum(tblK1purchasemaster.Invamt) as Invamt,sum(tblK1purchasemaster.sgstamt) as sgstamt,sum(tblK1purchasemaster.cgstamt) as cgstamt,
          sum(tblK1purchasemaster.igstamt) as igstamt,sum(tblK1purchasemaster.Invamt) as Invamt');  
        }else{
          $this->db->select('tblK1purchasemaster.PurchID,tblK1purchasemaster.Transdate,tblK1purchasemaster.Purchamt,tblK1purchasemaster.Invamt,
          tblK1purchasemaster.sgstamt,tblK1purchasemaster.cgstamt,tblK1purchasemaster.igstamt,tblK1purchasemaster.Invamt,
          tblK1purchasemaster.GSTIN,tblclients.company');  
        }
        $this->db->from(db_prefix() . 'K1purchasemaster');
        $this->db->join('tblclients', 'tblclients.AccountID = tblK1purchasemaster.AccountID AND tblclients.PlantID = tblK1purchasemaster.PlantID');
        
        $this->db->where(db_prefix() . 'K1purchasemaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1purchasemaster.FY', $year);
        $this->db->where(db_prefix() . 'K1purchasemaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        // $this->db->where(db_prefix() . 'purchasemaster.BT != B');
        if($data['accountId'] != ''){
            $this->db->where(db_prefix() . 'K1purchasemaster.AccountID', $data['accountId']);
       }
        if($data['bill_type'] == 2){
            $this->db->where('tblK1purchasemaster.BT', "Y");
        }else if($data['bill_type'] == 3){
            $this->db->where('tblK1purchasemaster.BT', "N");
        }
       if($data['bill_wise_type'] == 2){
           $this->db->group_by('DAY('.db_prefix() . 'K1purchasemaster.Transdate)');
       }
       $this->db->order_by(db_prefix() . 'K1purchasemaster.PurchID', 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function get_GstTypeP($data){
        
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $this->db->select(db_prefix() . 'K1history.*');  
        $this->db->from(db_prefix() . 'K1history');
        $this->db->join(db_prefix() . 'K1purchasemaster', db_prefix() . 'K1purchasemaster.PurchID = ' . db_prefix() . 'K1history.OrderID AND  '.db_prefix() . 'K1purchasemaster.PlantID = ' . db_prefix() . 'K1history.PlantID AND '.db_prefix() . 'K1purchasemaster.FY = ' . db_prefix() . 'K1history.FY','left');
        $this->db->where(db_prefix() . 'K1history.TransDate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
        $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1history.FY', $year);
        $this->db->order_by(db_prefix() . 'K1history.TransDate', 'ASC');
        return $this->db->get()->result_array();
    }
    
    public function get_GstTypeWiseValueP($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $this->db->select(db_prefix() . 'K1history.TransID, '. db_prefix() . 'K1history.OrderID, '. db_prefix() . 'K1history.cgst, '. db_prefix() . 'K1history.sgst, '. db_prefix() . 'K1history.igst, 
        SUM('. db_prefix() . 'K1history.sgstamt ) AS sgstsum, SUM('. db_prefix() . 'K1history.cgstamt ) AS cgstsum, SUM('. db_prefix() . 'K1history.igstamt ) AS igstsum ,
        SUM('. db_prefix() . 'K1history.ChallanAmt ) AS taxableAmt, '.db_prefix() . 'K1purchasemaster.Transdate,'.db_prefix() . 'K1history.TransDate2,'. 'K1history.TransDate,'.db_prefix() . 'clients.vat');  
        $this->db->from(db_prefix() . 'K1history');
        $this->db->join(db_prefix() . 'K1purchasemaster', db_prefix() . 'K1purchasemaster.PurchID = ' . db_prefix() . 'K1history.OrderID AND  '.db_prefix() . 'K1purchasemaster.PlantID = ' . db_prefix() . 'K1history.PlantID AND '.db_prefix() . 'K1purchasemaster.FY = ' . db_prefix() . 'K1history.FY','left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1purchasemaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1purchasemaster.PlantID','left');
        $this->db->where(db_prefix() . 'K1history.TransDate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
        $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1history.FY', $year);
        $this->db->where(db_prefix() . 'K1history.TType', 'P');
        $this->db->where(db_prefix() . 'K1history.TType2', 'Purchase');
        if($data['accountId'] != ''){
            $this->db->where(db_prefix() . 'K1purchasemaster.AccountID', $data['accountId']);
        }
        if($data['bill_type'] == 2){
            $this->db->where('tblK1purchasemaster.BT', "Y");
        }else if($data['bill_type'] == 3){
            $this->db->where('tblK1purchasemaster.BT', "N");
        }
        if($data['bill_wise_type'] == 2){
           //$this->db->group_by('DAY('.db_prefix() . 'salesmaster.Transdate)');
           $this->db->group_by(db_prefix() . 'K1history.igst, DAY('.db_prefix() . 'K1purchasemaster.Transdate),'.db_prefix() . 'K1history.sgst,'.db_prefix() . 'K1history.cgst');
       
           $this->db->order_by(db_prefix() . 'K1purchasemaster.Transdate', 'ASC');
       }else{
          $this->db->group_by(db_prefix() . 'K1history.TransID, '.db_prefix() . 'K1history.igst,'.db_prefix() . 'K1history.sgst,'.db_prefix() . 'K1history.cgst'); 
          $this->db->order_by(db_prefix() . 'K1purchasemaster.Transdate', 'ASC');
           
       }
        return $this->db->get()->result_array();
    }
    
    public function get_vendor_list()
    {
        $this->db->distinct();
        $this->db->select('tblK1purchasemaster.AccountID,tblclients.company');
        $this->db->from(db_prefix().'K1purchasemaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1purchasemaster.AccountID','left');
        return $this->db->get()->result_array();
    }
    
    public function get_sales_vendor_list()
    {
        $this->db->distinct();
        $this->db->select('tblK1salesmaster.AccountID,tblclients.company');
        $this->db->from(db_prefix().'K1salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1salesmaster.AccountID','left');
        return $this->db->get()->result_array();
    }
    
//========================= K1 GSt Sale Report data fetch ====================== 
    public function get_data_for_table($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        if($data['bill_wise_type'] == 2){
          $this->db->select(db_prefix() . 'K1salesmaster.Transdate,sum('.db_prefix() . 'K1salesmaster.SaleAmt) as SaleAmt,sum('.db_prefix() . 'K1salesmaster.sgstamt) as sgstamt,sum('.db_prefix() . 'K1salesmaster.cgstamt) as cgstamt,sum('.db_prefix() . 'K1salesmaster.igstamt) as igstamt,sum('.db_prefix() . 'K1salesmaster.BillAmt) as BillAmt');  
        }else{
          $this->db->select(db_prefix() . 'K1salesmaster.SalesID,'.db_prefix() . 'K1salesmaster.Transdate,'.db_prefix() . 'K1salesmaster.SaleAmt,'.db_prefix() . 'K1salesmaster.sgstamt,'.db_prefix() . 'K1salesmaster.cgstamt,'.db_prefix() . 'K1salesmaster.igstamt,'.db_prefix() . 'K1salesmaster.BillAmt,'.db_prefix() . 'K1salesmaster.GSTIN,'.db_prefix() . 'clients.company');  
        }
 
        $this->db->from(db_prefix() . 'K1salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1salesmaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1salesmaster.PlantID');
        
        $this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1salesmaster.FY', $year);
        $this->db->where(db_prefix() . 'K1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        
        if($data['accountId'] != ''){
            $this->db->where(db_prefix() . 'K1salesmaster.AccountID', $data['accountId']);
        }
        if($data['bill_type'] == 2){
            $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS NOT NULL');
        }else if($data['bill_type'] == 3){
            $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS  NULL');
        }
        if($data['bill_wise_type'] == 2){
           $this->db->group_by('DAY('.db_prefix() . 'K1salesmaster.Transdate)');
        }
        $this->db->order_by(db_prefix() . 'K1salesmaster.SalesID', 'ASC');
        return $this->db->get()->result_array();
    }
//============================ Get K1 Sale GST Pecentage From History ==========
    public function get_GstType($data)
    {   
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $this->db->select(db_prefix() . 'K1history.cgst,'.db_prefix() . 'K1history.sgst,'.db_prefix() . 'K1history.igst');  
        $this->db->from(db_prefix() . 'K1history');
        $this->db->where(db_prefix() . 'K1history.TransDate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->where(db_prefix() . 'K1history.BillID IS NOT NULL');
        $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1history.FY', $year);
        $this->db->where(db_prefix() . 'K1history.TType', "O");
        $this->db->where(db_prefix() . 'K1history.TType2', "SALE");
        $this->db->order_by(db_prefix() . 'K1history.TransDate', 'ASC');
        return $this->db->get()->result_array();
    }
//=================== GST Type Wise Sale Amt Sum ===============================
    public function get_GstTypeWiseValue($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $this->db->select('tblK1history.TransID,tblK1history.cgst,tblK1history.sgst,tblK1history.igst, 
        SUM(tblK1history.sgstamt ) AS sgstsum, SUM(tblK1history.cgstamt ) AS cgstsum, SUM(tblK1history.igstamt ) AS igstsum ,
         SUM(tblK1history.ChallanAmt) AS taxableAmt,tblK1history.TransDate');  
        $this->db->from(db_prefix() . 'K1history');
        $this->db->where(db_prefix() . 'K1history.TransDate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->where(db_prefix() . 'K1history.TransID IS NOT NULL');
        $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1history.FY', $year);
        $this->db->where(db_prefix() . 'K1history.TType', 'O');
        $this->db->where(db_prefix() . 'K1history.TType2', 'SALE');
        if($data['bill_type'] == 2 || $data['bill_type'] == 3){
           $this->db->join(db_prefix() . 'K1salesmaster', db_prefix() . 'K1salesmaster.SalesID = ' . db_prefix() . 'K1history.TransID AND  '.db_prefix() . 'K1salesmaster.PlantID = ' . db_prefix() . 'K1history.PlantID AND '.db_prefix() . 'K1salesmaster.FY = ' . db_prefix() . 'K1history.FY');
        }
        
        if($data['accountId'] != ''){
            $this->db->where(db_prefix() . 'K1history.AccountID', $data['accountId']);
        }
        if($data['bill_type'] == 2){
            $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS NOT NULL');
        }else if($data['bill_type'] == 3){
            $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS  NULL');
        }
        if($data['bill_wise_type'] == 2){
            $this->db->group_by('tblK1history.igst, DAY(tblK1history.TransDate),tblK1history.sgst,tblK1history.cgst');
            $this->db->order_by('tblK1history.TransDate', 'ASC');
        }else{
          $this->db->group_by('tblK1history.TransID,tblK1history.igst,tblK1history.sgst,tblK1history.cgst'); 
          $this->db->order_by(db_prefix() . 'K1history.TransID', 'ASC');
        }
        return $this->db->get()->result_array();
    }
//========================== K1 GSTR 1 Report ==================================
    public function GetDataForGSTR1($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        // B2B Data
        // GST Registered party Invoice wise GST percentage wise sale list
        $this->db->select('tblK1salesmaster.SalesID,tblK1salesmaster.BillAmt AS INVAMT,tblK1salesmaster.AccountID,
        tblK1salesmaster.GSTIN,tblclients.state');
        $this->db->from('tblK1salesmaster');
        $this->db->join('tblclients', 'tblclients.AccountID = tblK1salesmaster.AccountID AND tblclients.PlantID = tblK1salesmaster.PlantID',"LEFT");
        $this->db->where('tblK1salesmaster.PlantID', $selected_company);
        $this->db->where('tblK1salesmaster.FY', $year);
        //$this->db->where('tblK1salesmaster.BT', "T");
        $this->db->where('tblK1salesmaster.GSTIN IS NOT NULL');
        $this->db->where('tblK1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $B2BSaleList = $this->db->get()->result_array();
        $B2BSaleIds = array();
        foreach ($B2BSaleList as $key => $value) {
            array_push($B2BSaleIds,$value['SalesID']);
        }
         if(!empty($B2BSaleIds)){
            $this->db->select('tblK1history.TransID,tblK1history.TransDate,tblK1history.igst,tblK1history.cgst,tblK1history.sgst,
            SUM(tblK1history.ChallanAmt) AS TaxableAmt ,SUM(tblK1history.NetChallanAmt) AS BillAmt');
            $this->db->from('tblK1history');
            $this->db->where('tblK1history.PlantID', $selected_company);
            $this->db->where('tblK1history.FY', $year);
            $this->db->where('tblK1history.PartyID', "KASPL");
            $this->db->group_by('tblK1history.TransID,tblK1history.igst,tblK1history.cgst,tblK1history.sgst');
            $this->db->where_in('tblK1history.TransID', $B2BSaleIds);
            $this->db->order_by('tblK1history.TransID', 'ASC');
            $B2BhistoryData = $this->db->get()->result_array(); 
         }else{
            $B2BhistoryData = array();
         }
        
        // B2CL
        // B2CL (Large) invoices are B2C invoices where:
        //Invoice value > ₹2,50,000
        // The total invoice value (including tax) should be more than ₹2,50,000.
        //Supply is Inter-State
        //Place of Supply (POS) is different from Supplier State, meaning IGST supply.
        
        $this->db->select('tblK1salesmaster.SalesID,tblK1salesmaster.BillAmt AS INVAMT,
        tblK1salesmaster.AccountID,tblK1salesmaster.Transdate AS BillDate,tblclients.state');
        $this->db->from('tblK1salesmaster');
        $this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1salesmaster.AccountID AND tblclients.PlantID = tblK1salesmaster.PlantID',"LEFT");
        $this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1salesmaster.FY', $year);
        $this->db->where(db_prefix() . 'clients.state !=', 'MH');
        $this->db->where(db_prefix() . 'K1salesmaster.BillAmt >', '250000');
        $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS NULL');
        $this->db->where(db_prefix() . 'K1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $B2CLSaleList = $this->db->get()->result_array();
        
        $B2CLSaleIDs = array();
        foreach ($B2CLSaleList as $key => $value) {
            array_push($B2CLSaleIDs,$value['SalesID']);
        }
        if(!empty($B2CLSaleIDs)){
            $this->db->select(db_prefix() . 'K1history.TransID,'.db_prefix() . 'K1history.TransDate,
            '.db_prefix() . 'K1history.igst,'.db_prefix() . 'K1history.cgst,'.db_prefix() . 'K1history.sgst,
            SUM('.db_prefix() . 'K1history.ChallanAmt) AS TaxableAmt ,SUM('.db_prefix() . 'K1history.NetChallanAmt) AS BillAmt');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'K1history.FY', $year);
            $this->db->where('tblK1history.PartyID', "KASPL");
            $this->db->group_by(db_prefix() . 'K1history.TransID,'.db_prefix() . 'K1history.igst,'.db_prefix() . 'K1history.cgst,'.db_prefix() . 'K1history.sgst');
            $this->db->where_in(db_prefix() . 'K1history.TransID', $B2CLSaleIDs);
            $this->db->order_by(db_prefix() . 'K1history.TransID', 'ASC');
            $B2CLhistoryData = $this->db->get()->result_array();
        }else{
            $B2CLhistoryData = array();
        }
        
        // B2CS – Business to Consumer Small
        // Customer GSTIN blank
        // Intrastate supply (POS = Supplier State) (ANY value), OR
        // Interstate supply (POS ≠ Supplier State) AND invoice ≤ 2,50,000
        $B2CSSaleIds1 = array();
        
        // Intrastate supply (POS = Supplier State) (ANY value), OR
        $this->db->select(db_prefix() . 'K1salesmaster.SalesID');
        $this->db->from(db_prefix() . 'K1salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1salesmaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1salesmaster.PlantID',"LEFT");
        $this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1salesmaster.FY', $year);
        $this->db->where(db_prefix() . 'clients.state ', 'MH');
        $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS NULL');
        $this->db->where(db_prefix() . 'K1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $B2CSSaleID = $this->db->get()->result_array();
        
        foreach ($B2CSSaleID as $key => $value) {
            array_push($B2CSSaleIds1,$value['SalesID']);
        }
        
        // 2. Interstate supply (POS ≠ Supplier State) AND invoice ≤ 2,50,000
        $this->db->select(db_prefix() . 'K1salesmaster.SalesID');
        $this->db->from(db_prefix() . 'K1salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1salesmaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1salesmaster.PlantID',"LEFT");
        $this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1salesmaster.FY', $year);
        $this->db->where(db_prefix() . 'clients.state !=', 'MH');
        $this->db->where(db_prefix() . 'K1salesmaster.BillAmt <=', '250000');
        $this->db->where(db_prefix() . 'K1salesmaster.GSTIN IS NULL');
        $this->db->where(db_prefix() . 'K1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $B2CSSaleID2 = $this->db->get()->result_array();
        foreach ($B2CSSaleID2 as $key => $value) {
            array_push($B2CSSaleIds1,$value['SalesID']);
        }
        if(!empty($B2CSSaleIds1)){
            $this->db->select(db_prefix() . 'K1history.TransID,'.db_prefix() . 'clients.state,'.db_prefix() . 'K1history.TransDate,
            '.db_prefix() . 'K1history.cgst,'.db_prefix() . 'K1history.sgst,'.db_prefix() . 'K1history.igst,
            SUM('.db_prefix() . 'K1history.NetChallanAmt) AS BillAmt ');
            $this->db->from(db_prefix() . 'K1history');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1history.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1history.PlantID',"LEFT");
            $this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'K1history.FY', $year);
            $this->db->where('tblK1history.PartyID', "KASPL");
            $this->db->group_by(db_prefix() . 'clients.state,'.db_prefix() . 'K1history.cgst,'.db_prefix() . 'K1history.sgst,'.db_prefix() . 'K1history.igst');
            $this->db->where_in(db_prefix() . 'K1history.TransID', $B2CSSaleIds1);
            $this->db->order_by(db_prefix() . 'clients.state', 'ASC');
            $B2CS2 = $this->db->get()->result_array();
        }else{
            $B2CS2 = array();
        }
        
        
        // CDNR – Credit/Debit Notes for Registered Customer
        // Customer GSTIN present
        // Affects an earlier B2B invoice
        
        $this->db->select('tblK1salesreturn.SalesRtnID,tblK1salesreturn.Transdate AS SaleRTNDate,tblK1salesreturn.SaleID,tblK1salesmaster.Transdate AS SaleDate,
        tblK1salesreturn.BillAmt,tblK1salesreturn.AccountID,tblK1salesreturn.GSTIN,tblclients.state,tblclients.company');
        $this->db->from(db_prefix() . 'K1salesreturn');
        $this->db->join('tblK1salesmaster','tblK1salesmaster.SalesID = tblK1salesreturn.SaleID');
        $this->db->join('tblclients', 'tblclients.AccountID = tblK1salesreturn.AccountID AND tblclients.PlantID = tblK1salesreturn.PlantID',"LEFT");
        $this->db->where('tblK1salesreturn.PlantID', $selected_company);
        $this->db->where('tblK1salesreturn.FY', $year);
        //$this->db->where('tblK1salesmaster.BT', "T");
        $this->db->where('tblK1salesreturn.GSTIN IS NOT NULL');
        $this->db->where('tblK1salesreturn.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $CDNRSaleList = $this->db->get()->result_array();
        $CDNRSaleIds = array();
        foreach ($CDNRSaleList as $key => $value) {
            array_push($CDNRSaleIds,$value['SalesID']);
        }
         if(!empty($CDNRSaleIds)){
            $this->db->select('tblK1history.OrderID,tblK1history.TransDate,tblK1history.igst,tblK1history.cgst,tblK1history.sgst,
            SUM(tblK1history.ChallanAmt) AS TaxableAmt ,SUM(tblK1history.NetChallanAmt) AS BillAmt');
            $this->db->from('tblK1history');
            $this->db->where('tblK1history.PlantID', $selected_company);
            $this->db->where('tblK1history.FY', $year);
            $this->db->where('tblK1history.PartyID', "KASPL");
            $this->db->group_by('tblK1history.OrderID,tblK1history.igst,tblK1history.cgst,tblK1history.sgst');
            $this->db->where_in('tblK1history.OrderID', $CDNRSaleIds);
            $this->db->order_by('tblK1history.OrderID', 'ASC');
            $CDNRhistoryData = $this->db->get()->result_array(); 
         }else{
            $CDNRhistoryData = array();
         }
         
        // CDNUR – Credit/Debit Notes for Unregistered Customer
        // Customer GSTIN blank
        // Affects a previous B2CL or B2CS invoice
        
        $this->db->select('tblK1salesreturn.SalesRtnID,tblK1salesreturn.Transdate AS SaleRTNDate,tblK1salesreturn.SaleID,tblK1salesmaster.Transdate AS SaleDate,
        tblK1salesreturn.BillAmt,tblK1salesreturn.AccountID,tblK1salesreturn.GSTIN,tblclients.state,tblclients.company');
        $this->db->from('tblK1salesreturn');
        $this->db->join('tblK1salesmaster', 'tblK1salesmaster.SalesID = tblK1salesreturn.SaleID');
        $this->db->join('tblclients', 'tblclients.AccountID = tblK1salesreturn.AccountID AND tblclients.PlantID = tblK1salesreturn.PlantID',"LEFT");
        $this->db->where('tblK1salesreturn.PlantID', $selected_company);
        $this->db->where('tblK1salesreturn.FY', $year);
        //$this->db->where('tblK1salesmaster.BT', "T");
        $this->db->where('tblK1salesreturn.GSTIN IS NULL');
        $this->db->where('tblK1salesreturn.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $CDNURSaleList = $this->db->get()->result_array();
        $CDNURSaleIds = array();
        foreach ($CDNURSaleList as $key => $value) {
            array_push($CDNURSaleIds,$value['SalesRtnID']);
        }
        if(!empty($CDNURSaleIds)){
            $this->db->select('tblK1history.OrderID,tblK1history.TransDate,tblK1history.igst,tblK1history.cgst,tblK1history.sgst,
            SUM(tblK1history.ChallanAmt) AS TaxableAmt ,SUM(tblK1history.NetChallanAmt) AS BillAmt');
            $this->db->from('tblK1history');
            $this->db->where('tblK1history.PlantID', $selected_company);
            $this->db->where('tblK1history.FY', $year);
            $this->db->where('tblK1history.PartyID', "KASPL");
            $this->db->group_by('tblK1history.OrderID,tblK1history.igst,tblK1history.cgst,tblK1history.sgst');
            $this->db->where_in('tblK1history.OrderID', $CDNURSaleIds);
            $this->db->order_by('tblK1history.OrderID', 'ASC');
            $CDNURhistoryData = $this->db->get()->result_array(); 
        }else{
            $CDNURhistoryData = array();
        }
    // HSN Data
        $TType = array("O","SR");
        $this->db->select('tblproduct.hsn_code,tblhsn.hsndesc,tblK1history.TType,tblK1history.cgst,tblK1history.sgst,tblK1history.igst,
        SUM(tblK1history.BilledQty * tblK1history.CaseQty) AS TotalSaleQty, SUM(tblK1history.NetChallanAmt) AS NetAmt');
        $this->db->from('tblK1history');
        $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
        $this->db->join('tblhsn', 'tblhsn.name = tblproduct.hsn_code');
        $this->db->where('tblK1history.PlantID', $selected_company);
        $this->db->where('tblK1history.FY', $year);
        $this->db->where('tblK1history.PartyID', "KASPL");
        $this->db->where_in('tblK1history.TType', $TType);
        $this->db->where('tblK1history.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->group_by('tblproduct.hsn_code,tblK1history.cgst,tblK1history.sgst,tblK1history.igst,tblK1history.TType');
        $this->db->order_by('tblproduct.hsn_code', 'ASC');
        $HSNWiseData = $this->db->get()->result_array();
        $HSNList = array();
        $TaxrateList = array();
        foreach($HSNWiseData as $key=>$val){
            array_push($HSNList,$val["hsn_code"]);
            $taxrate = $val["igst"] + $val["sgst"] + $val["cgst"];
            array_push($TaxrateList,$taxrate);
        }
        $HSNList = array_unique($HSNList);
        $TaxrateList = array_unique($TaxrateList);
        
    // Docs Data
        $this->db->select(db_prefix() . 'K1salesmaster.SalesID,tblK1salesmaster.BillAmt');
        $this->db->from(db_prefix() . 'K1salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1salesmaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'K1salesmaster.PlantID',"LEFT");
        $this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1salesmaster.FY', $year);
        $this->db->where(db_prefix() . 'K1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
        $this->db->order_by(db_prefix() . 'K1salesmaster.SalesID', "ASC");
        $InvoiceList = $this->db->get()->result_array();
        $OkInvoice = 0;
        $CancelInvoice = 0;
        $FirstInv = '';
        $LastInv = '';
        foreach($InvoiceList as $IKey=>$Ival){
            if($FirstInv == ""){
                $FirstInv = $Ival["SalesID"];
            }
            $LastInv = $Ival["SalesID"];
            if($Ival["BillAmt"]>0){
                $OkInvoice++;
            }else{
                $CancelInvoice++;
            }
        }
        
        $GSTR1Data = array();
        $GSTR1Data['B2BSaleList'] = $B2BSaleList;
        $GSTR1Data['B2BhistoryData'] = $B2BhistoryData;
        $GSTR1Data['B2CLSaleList'] = $B2CLSaleList;
        $GSTR1Data['B2CLhistoryData'] = $B2CLhistoryData;
        $GSTR1Data['B2CS2'] = $B2CS2;
        $GSTR1Data['CDNRSaleList'] = $CDNRSaleList;
        $GSTR1Data['CDNRhistoryData'] = $CDNRhistoryData;
        $GSTR1Data['CDNURSaleList'] = $CDNURSaleList;
        $GSTR1Data['CDNURhistoryData'] = $CDNURhistoryData;
        $GSTR1Data['HSNWiseData'] = $HSNWiseData;
        $GSTR1Data['HSNList'] = $HSNList;
        $GSTR1Data['TaxrateList'] = $TaxrateList;
        $GSTR1Data['OkInvoice'] = $OkInvoice;
        $GSTR1Data['CancelInvoice'] = $CancelInvoice;
        $GSTR1Data['FirstInv'] = $FirstInv;
        $GSTR1Data['LastInv'] = $LastInv;
        return $GSTR1Data;
    }
//=================== Get Unregister Party Taxable Purchase List ===============
    public function get_data_for_gstr_3_1_d($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $sqlMFirst = 'SELECT Purchamt,igstamt,cgstamt,sgstamt 
        FROM `tblK1purchasemaster` 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$year.'" AND GSTIN IS NULL AND Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
        $resultMFirst = $this->db->query($sqlMFirst)->result_array();
        
        $TaxableAmt = 0.00;
        $IAmt = 0.00;
        $SAmt = 0.00;
        $CAmt = 0.00;
        foreach ($resultMFirst as $key => $value) {
            if($value['igstamt'] == "0.00" && $value['sgstamt'] == "0.00" && $value['cgstamt'] == "0.00"){
                
            }else{
                $TaxableAmt += $value['Purchamt'];
                $IAmt += $value['igstamt'];
                $SAmt += $value['sgstamt'];
                $CAmt += $value['cgstamt'];
            }
        }
        $response = array(
            'TaxableAmt'=>$TaxableAmt,
            'IAmt'=>$IAmt,
            'CAmt'=>$CAmt,
            'SAmt'=>$SAmt,
        );
        return $response;
    }
    
//=================== Get All Purchase Purchase List ===========================
    public function get_data_for_gstr_4_A_5($data)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $_SESSION['finacial_year'];
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $from_date = $from_date.' 00:00:00';
        $to_date = $to_date.' 23:59:59';
        
        $sqlMFirst = 'SELECT Purchamt,igstamt,cgstamt,sgstamt 
        FROM `tblK1purchasemaster` 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$year.'" AND Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"';
        $resultMFirst = $this->db->query($sqlMFirst)->result_array();
        
        $TaxableAmt = 0.00;
        $InterStateTaxableAmt = 0;
        $IntraStateTaxableAmt = 0;
        $IAmt = 0.00;
        $SAmt = 0.00;
        $CAmt = 0.00;
        foreach ($resultMFirst as $key => $value) {
            if($value['igstamt']>0){
                $InterStateTaxableAmt += $value['Purchamt'];
            }else{
                $IntraStateTaxableAmt += $value['Purchamt'];
            }
            $TaxableAmt += $value['Purchamt'];
            $IAmt += $value['igstamt'];
            $SAmt += $value['sgstamt'];
            $CAmt += $value['cgstamt'];
        }
        $response = array(
            'InterStateTaxableAmt'=>$InterStateTaxableAmt,
            'IntraStateTaxableAmt'=>$IntraStateTaxableAmt,
            'TaxableAmt'=>$TaxableAmt,
            'IAmt'=>$IAmt,
            'CAmt'=>$CAmt,
            'SAmt'=>$SAmt,
        );
        return $response;
    }
    
    
}
?>