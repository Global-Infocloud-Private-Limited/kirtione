<?php

defined('BASEPATH') or exit('No direct script access allowed'); 

/**
 * This class describes a purchase model. 
 */
class Inventory_model extends App_Model
{  
    public function __construct()
    {
        parent::__construct();
    }
    public function GetItemList()
    {
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('ItemID AS id,ItemName AS label');
        $this->db->where(db_prefix() . 'items.isactive', 'Y');
        $this->db->order_by('ItemName', 'ASC');
        return $this->db->get(db_prefix() . 'items')->result_array();
    }
    
    public function add_issue_order($data)
    {
        
        
        if(isset($data['pur_order_detail'])){
            $pur_order_detail = json_decode($data['pur_order_detail']);
            
            unset($data['pur_order_detail']);
            $es_detail = [];
            $row = [];
            $rq_val = [];
            $header = [];
            $header[] = 'ItemID';
            $header[] = 'UOM';
            $header[] = 'system_qty';
            $header[] = 'isuue_qty';
            $header[] = 'rate';
            $header[] = 'value';
            $header[] = 'reason';
            foreach ($pur_order_detail as $key => $value) {
                if($value[0] != ''){
                    $es_detail[] = array_combine($header, $value);
                }
            }
        }
        /*
        echo "<pre>";
        print_r($es_detail);
        print_r($data);
        die;
        */
        $PlantID = $this->session->userdata('root_company'); 
        $FY = $this->session->userdata('finacial_year');
        $GodownID = $data['WHID'];
        $CenterID = $data['CenterID'];
        $total_issue_qty = $data['total_issue_qty'];
        $total_issue_amt = $data['total_issue_amt'];
        $remarks = $data['remarks'];
        $Issue_number = get_option('next_issue_number_for_kirti');
        $NewIssue_number = 'IO'.$FY.$Issue_number;   
        $ItCount = count($es_detail);
        $Transdate =  to_sql_date($data['issue_date'])." ".date('H:i:s');
        
        $IssueMaster = array(
            'PlantID'=>$PlantID,
            'FY'=>$FY,
            'IssueID' =>$NewIssue_number,
            'Transdate' =>$Transdate,
            'TransDate2' =>date('Y-m-d H:i:s'),
            'AccountID'=>"KASPL",
            'CenterID' =>$CenterID,
            'WHID' =>$GodownID,
            'IssueAmt' =>$total_issue_amt,
            'IssueQty' =>$total_issue_qty,
            'remarks'=>$remarks,
            'ItCount'=>$ItCount,
            'UserID'=>$_SESSION['username'],
        );
        $this->db->insert(db_prefix() . 'issuemaster',$IssueMaster);
        if($this->db->affected_rows() > 0){
            $this->increment_next_issue_number();
            foreach($es_detail as $value){
                $data_array_result = array(
                    'PlantID'=>$PlantID,
                    'FY'=>$FY,
                    'OrderID' =>$NewIssue_number,
                    'BillID' =>$NewIssue_number,
                    'TransID' =>$NewIssue_number,
                    'TransDate2'=>$Transdate,
                    'TransDate' =>date('Y-m-d H:i:s'),
                    'TType'=>'I',
                    'TType2'=> 'Issue',
                    'AccountID'=>"KASPL",
                    'ItemID'=>$value['ItemID'],
                    'TypeID'=>"I",
                    'CenterID'=>$CenterID,
                    'GodownID' =>$GodownID,
                    'PartyID'=>'KASPL',
                    'PurchRate'=>$value['rate'] / 10,
                    'SaleRate'=>$value['rate'] / 10,
                    'BasicRate'=>$value['rate'] / 10,
                    'final_rate'=>$value['rate'] / 10,
                    'SuppliedIn'=>1,
                    'OrderQty'=>$value['isuue_qty'],
                    'BilledQty'=>$value['isuue_qty'],
                    'DiscAmt'=>0.00,
                    'cgst'=>0.00,
                    'sgst'=>0.00,
                    'igst'=>0.00,
                    'cgstamt'=>0.00,
                    'sgstamt'=>0.00,
                    'igstamt'=>0.00,
                    'CaseQty'=>1,
                    'Cases'=>1,
                    'OrderAmt'=>$value['value'],
                    'ChallanAmt'=>$value['value'],
                    'NetOrderAmt'=>$value['value'],
                    'NetChallanAmt'=>$value['value'],
                    'cnfid' =>1,
                    'Ordinalno'=>1,
                    'UserID'=>$_SESSION['username'],
                    'reason'=>$value['reason'],
                );
                $this->db->insert(db_prefix() . 'history',$data_array_result);
            }
            return true;
        }
    }
    
    public function increment_next_issue_number()
    {
        $FY = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('name', 'next_issue_number_for_kirti');
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }
    
}