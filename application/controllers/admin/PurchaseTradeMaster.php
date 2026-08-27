<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PurchaseTradeMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PurchaseTradeModel');                   
    }
	
	public function AddEditPurchaseTrade()
	{
		if (!has_permission_new('DepositeTradePunch', '', 'view')) {
            access_denied('trade list');
        }
		$data['title']  = "Deposit Trade Punch";        
        $data['company_detail'] = $this->PurchaseTradeModel->get_company_detail();
        $data['center'] = $this->PurchaseTradeModel->GetAllActiveCenterList();
		$data['lockingPeriod'] = $this->PurchaseTradeModel->getAllLockingDB();
		$data['BillingCycle'] = $this->PurchaseTradeModel->getAllCycles();
		$this->load->view('admin/PurchaseTradeMaster/AddEditPurchaseTrade',$data);
	}
	
	public function GetWarehouse()
	{
		$CenterID = $this->input->post('CenterID');
		$WarehouseList = $this->PurchaseTradeModel->GetWarehouseListForCompanyMaster($CenterID);
		echo json_encode($WarehouseList);
	}
	
	public function SaveOrder()
	{	
	    if (!has_permission_new('DepositeTradePunch', '', 'create')) {
            access_denied('trade list');
        }
		$fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
		$AccountID = $this->input->post('AccountID');
        $CenterID = $this->input->post('center');
		$WhID = $this->input->post('Warehouse');
		$Commodity = $this->input->post('Commodity');
        $ItemID = $this->input->post('item');
        $TradeQty = $this->input->post('TradeQty');
		$MinQty = $this->input->post('MinQty');
        $DepositPeriod = $this->input->post('DepositPeriod');
		$LockingPeriod = $this->input->post('LockingPeriod');
		$BillCycle = $this->input->post('BillCycle');
		$ChargeRate = $this->input->post('ChargeRate');
		$RateType = $this->input->post('RateType');
		$FumigationCharge = $this->input->post('FumigationCharge');
		$RateFumigationCharge = $this->input->post('RateFumigationCharge');
		$FumigationChargeAmt = $this->input->post('fumigationChargeAmtValue'); 
		$TradeType = $this->input->post('TradeType');
		$CreditDays = $this->input->post('CreditDays'); 
		$SaudaDate = $this->input->post('SaudaDate');
		$dateObj = DateTime::createFromFormat('d/m/Y', $SaudaDate);
        $formattedDate = $dateObj ? $dateObj->format('Y-m-d H:i:s') : null;
		
		$new_Number = get_number($CenterID,'D');         
        $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
        $bookingID = $CenterID.'D'.date('d').date('m').date('y').$number;	
        
        if($TradeType == "D")
        { $TType2 = "Deposit"; }
        else if($TradeType == "T")
        { $TType2 = "Trade Finance"; }
        else if($TradeType == "A")
        { $TType2 = "Anamat"; }
		
		$DepositLeadMaster_data = array(
			"PlantID" => $selected_company,
            "FY"=>$fy,   
			"PartyID"=>"KASPL",			
            "BookingID"=>$bookingID,            
            "TransDate"=> $formattedDate,
            "TType"=> $TradeType,
            "TType2"=> $TType2,
            "AccountID"=>$AccountID,
            "UserID"=>$this->session->userdata('username'),
			"BrokerID"=>$AccountID,
			"WHID"=>$WhID,			
            "CenterID"=>$CenterID,			
            "ItemID"=>$ItemID,
            "quantity"=>$TradeQty,
            "e_quantity"=>$TradeQty,
            "unit"=>'MT',
            "basic_rate"=>$ChargeRate,
            "Mastercurrentrate"=>$ChargeRate,
            "IsApprove"=>"Y",		
			"BrokerApproveTime"=>date('Y-m-d H:i:s'),
			"BrokerApprove"=>"Y",
			"payment_cycle"=>$BillCycle,
			"locking_period"=>$LockingPeriod,
			"status"=>"1",
			"today_rate"=>$ChargeRate			
        );
		$this->db->insert(db_prefix().'lead_master', $DepositLeadMaster_data);
		$insert_id = $this->db->insert_id();
		if($insert_id)
		{
			$this->increment_bookingId_number($CenterID,'D');
			$DepositTrade_data = array(
				"BookingID"=>$bookingID,
				"TType"=>$TradeType,
				"MinQty"=>$MinQty,
				"DepositPeriod"=>$DepositPeriod,
				"RateType"=>$RateType,
				"IsFumigation"=>$FumigationCharge,
				"RateIncFumigation"=>$RateFumigationCharge,
				"FumigationAmt"=>$FumigationChargeAmt,
				"CreditDays"=>$CreditDays
			);
			$this->db->insert(db_prefix().'DepositTradeDetails', $DepositTrade_data);
			echo json_encode(true);
            die;
		}
		echo json_encode(false);
        die;
	}
	
	public function increment_bookingId_number($CenterID,$TType)
    {
        $this->db->set('Number', 'Number+1', false);
        $this->db->WHERE('CenterID', $CenterID);
        $this->db->WHERE('TType', $TType);
        $this->db->update(db_prefix() . 'numberformat');
    }
}