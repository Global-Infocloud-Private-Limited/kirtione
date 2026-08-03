<?php
	
defined('BASEPATH') or exit('No direct script access allowed');
	
class PurchaseTradeModel extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_company_detail()
	{   
		$selected_company = $this->session->userdata('root_company');
		$sql ='SELECT '.db_prefix().'rootcompany.*
		FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
		$result = $this->db->query($sql)->row();
		return $result;		
	}
	
	//============= Get All Active Center ==========================================
    public function GetAllActiveCenterList()
    {
        $this->db->select('tblCenterMaster.*');
        $this->db->from(db_prefix() . 'CenterMaster');
        $this->db->where('tblCenterMaster.status', "Y");
        return $this->db->get()->result_array();
    }
	
	public function GetWarehouseListForCompanyMaster($CenterID)
	{
		$this->db->select('tblwarehouse.*');
        $this->db->from(db_prefix() . 'warehouse');
        $this->db->where(db_prefix() . 'warehouse.center', $CenterID);
        return $this->db->get()->result_array();
	}

	public function getAllLockingDB(){
        return $this->db->get('tblLocking')->result_array();
    }	
	
	public function getAllCycles(){
        return $this->db->get('tblPaymentCycle')->result_array();
    }
}