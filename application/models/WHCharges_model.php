<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WHCharges_model extends App_Model
{
    public function getItemsData(){
		$this->db->select('tblitems.ItemID,tblitems.ItemName');
		return $this->db->get('tblitems')->result_array();
	}
	public function getWarehouseData(){
		$this->db->order_by('tblwarehouse.w_id', 'ASC');
		return $this->db->get('tblwarehouse')->result_array();
	}
	
	public function getCenter()
	{
	    $this->db->where('status','Y');
		$this->db->order_by('CenterName', 'ASC');
		return $this->db->get('tblCenterMaster')->result_array();
	}
	
//======================== All Deposite Trade list =============================
	public function GetDepositeTrade($data)
	{   
		$CenterID = $data['CenterID'];
		$ItemID = $data['ItemID'];
		$IsApprove = $data['IsApprove'];
		$TradeType = $data['TradeType'];
		
		$this->db->select('tbllead_master.*,tblCenterMaster.CenterName,tblitems.ItemName,
		tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.lastname
		');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID','left');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
		if($data["CenterID"] && $data["CenterID"] !== ""){
			$this->db->where_in('tbllead_master.CenterID', $CenterID);
		}
		if($data["ItemID"] && $data["ItemID"] !== ""){
			$this->db->where('tbllead_master.ItemID', $ItemID);
		}
		if($data["IsApprove"] && $data["IsApprove"] !== ""){
			$this->db->where('tbllead_master.IsApprove', $data['IsApprove']);
		}
		
		if($data["TradeType"] && $data["TradeType"] !== ""){
			$this->db->where('tbllead_master.TType', $data['TradeType']);
		}
		$this->db->order_by('tbllead_master.id','DESC');
		return $this->db->get('tbllead_master')->result_array();
	}
//======================= Get Trade Details By Trade ID ========================
	public function GetBookingListDetailsDB($BookingID)
	{
		$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname,
		tblwarehouse.center,tblwarehouse.w_name,TD.MinQty,TD.DepositPeriod,TD.RateType,TD.IsFumigation,TD.RateIncFumigation,TD.FumigationAmt,TD.CreditDays,
		PC.CycleName,PC.CycleDays,LP.LockName,LP.LockDays');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','left');
		$this->db->join('tblDepositTradeDetails AS TD','TD.BookingID = tbllead_master.BookingID',"LEFT");
		$this->db->join('tblPaymentCycle AS PC','PC.CycleID = tbllead_master.payment_cycle',"LEFT");
		$this->db->join('tblLocking AS LP','LP.LockID = tbllead_master.locking_period',"LEFT");
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
		$this->db->where('tbllead_master.BookingID',$BookingID);
		$this->db->order_by('tbllead_master.id','DESC');
		return $this->db->get('tbllead_master')->row();
	}
//================ Get Inward Details against TradeID ==========================
	public function GetInwardListByTradeID($BookingID)
	{
		$this->db->select('tblGateMaster.*');
		$this->db->where('tblGateMaster.BookingID',$BookingID);
		return $this->db->get('tblGateMaster')->result_array();
	}
	
	public function GetLoanHistoryListByTradeID($BookingID)
	{
	    $this->db->select('tblloan_history.*,tblGateMaster.ASNID');
	    $this->db->join('tblGateMaster','tblGateMaster.BookingID = tblloan_history.BookingID AND tblGateMaster.Gate_in_ID = tblloan_history.GateINID',"LEFT");
		$this->db->where('tblloan_history.BookingID',$BookingID);
		return $this->db->get('tblloan_history')->result_array();
	}
//================ Get Outward Details against TradeID ==========================
	public function GetOutwardByTradeID($BookingID)
	{
		$this->db->select('tblWithdrawalDetail.BookingID AS WTradeID,tblGateMaster.ASNID WAsnID,tblGateMaster.id,tblGateMaster.TType,
		tblGateMaster.Gate_in_ID AS WGateINID,tblGateMaster.gate_in_date AS WGatINDate,tblGateMaster.status AS WStatus,tblwithdrawalmaster.PurchID,
		tblWithdrawalDetail.TradeID AS DTradeID,tblWithdrawalDetail.GateINID AS DGateINID');
		$this->db->join('tblGateMaster','tblGateMaster.BookingID = tblWithdrawalDetail.BookingID');
		$this->db->join('tblwithdrawalmaster','tblwithdrawalmaster.TransID = tblGateMaster.Gate_in_ID');
		$this->db->where('tblWithdrawalDetail.TradeID',$BookingID);
		$this->db->order_by('tblGateMaster.gate_in_date',"ASC");
		$Result =  $this->db->get('tblWithdrawalDetail')->result_array();
		if($Result){
		    $this->db->select('tblstockInventory.*');
		    $this->db->where('tblstockInventory.BookingID',$BookingID);
		    $this->db->where('tblstockInventory.TType', $Result[0]['TType']);
		    $InventoryDetails =  $this->db->get('tblstockInventory')->result_array();
		    $i = 0;
		    foreach($Result as $val){
		        $OutwardQty = 0;
		        $OutwardWeight = 0;
		        foreach($InventoryDetails as $Ikey=>$Ival){
		            if($val["DTradeID"] == $Ival["BookingID"] && $val["DGateINID"] == $Ival["GateINID"] && $val["PurchID"] == $Ival["TransID"]){
		                $OutwardWeight += $Ival['Weight'];
		                $OutwardQty += $Ival['BagQty'];
		            }
		        }
		        $Result[$i]["OutwardWeight"] = $OutwardWeight;
		        $Result[$i]["OutwardQty"] = $OutwardQty;
		        $i++;
		    }
		}
		return $Result;
	}
//================ Get Inward Stock Lot wise against TradeID ===================
	public function GetLotWiseStockByTradeID($BookingID)
	{
		$this->db->select('tblstockInventory.*');
		$this->db->where('tblstockInventory.BookingID',$BookingID);
		return $this->db->get('tblstockInventory')->result_array();
	}
	
	public function get_company_detail()
    {  
        $selected_company = $this->session->userdata('root_company');
      
        
        $sql ='SELECT '.db_prefix().'rootcompany.*
        FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
        $result = $this->db->query($sql)->row();
        
        return $result;
    }
}
?>