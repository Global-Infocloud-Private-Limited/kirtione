<?php
	
defined('BASEPATH') or exit('No direct script access allowed');

class Withdrawal_model extends App_Model
{
    public function GetWithdrawalBookingDB($data)
    {
        $from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		$CustomerType = $data['CustomerType'];
		$CenterID = $data['CenterID'];
		$ItemID = $data['ItemID'];
		$IsApprove = $data['IsApprove'];
		
		$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblitems.PlantID,
		tblclients.CustomerType,tblclients.AccountID,tblclients.company,tblcontacts.AccountID,tblcontacts.firstname,tblcontacts.firstname,
		tblwarehouse.w_name,tblCenterMaster.CenterName');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID',"LEFT");
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
		$this->db->where('tbllead_master.TType', 'W');
		$this->db->where('tbllead_master.TType2', 'Withdrawal');
		if($data["from_date"] !== "" && $data["to_date"] !== ""){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data["CustomerType"] && $data["CustomerType"] !== ""){
			$this->db->where('tblclients.CustomerType', $CustomerType);
		}
		if($data["CenterID"] && $data["CenterID"] !== ""){
			$this->db->where('tbllead_master.CenterID', $CenterID);
		}
		if($data["ItemID"] && $data["ItemID"] !== ""){
			$this->db->where('tbllead_master.ItemID', $ItemID);
		}
		if($data["IsApprove"] && $data["IsApprove"] !== ""){
			if($data["IsApprove"] == 'NA'){
				$this->db->where('tbllead_master.IsApprove ','NA');
			}
			elseif($data["IsApprove"] == 'Y'){
		        $this->db->where('tbllead_master.IsApprove ','Y');
			}
			elseif($data["IsApprove"] == 'N'){
				$this->db->where('tbllead_master.IsApprove ','N');
			}
		}
		$this->db->order_by('tbllead_master.id','DESC');
		return $this->db->get('tbllead_master')->result_array();
    }
    
    public function GetCenterList()
    {
		$this->db->select('tblCenterMaster.CenterName,tblCenterMaster.CenterID');
		$this->db->join('tbllead_master','tbllead_master.CenterID = tblCenterMaster.CenterID');
		$this->db->group_by('tblCenterMaster.CenterID');
		$this->db->order_by('tblCenterMaster.CenterName', 'ASC');
		return $this->db->get('tblCenterMaster')->result_array();
	}
    public function getWarehouseData()
    {
		$this->db->order_by('tblwarehouse.w_id', 'ASC');
		return $this->db->get('tblwarehouse')->result_array();
	}
	
	public function getItemsData()
	{
		$this->db->select('tblitems.ItemID,tblitems.ItemName');
		return $this->db->get('tblitems')->result_array();
	}
	
	public function getCustomersData()
	{
		$this->db->select('tblCustomerType.id,tblCustomerType.Name');
		return $this->db->get('tblCustomerType')->result_array();
	}
	
	public function getModalDataDb($BookingID)
	{
        $this->db->select('tbllead_master.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
        $this->db->where('tbllead_master.BookingID',$BookingID);
        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
        $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
        return $this->db->get('tbllead_master')->row();
    }

    public function AcceptTradeDb($data)
    {
        $this->db->where('BookingID',$data['BookingID']);
        $this->db->set('IsApprove','Y');
        $this->db->set('ApproveUserID',$data['ApproveUserID']);
        $this->db->set('ApproveTime',$data['ApproveTime']);
        return $this->db->update('tbllead_master');
    }
    
    public function ModifyTradeDb($data)
    {
        $this->db->where('BookingID',$data['BookingID']);
        return $this->db->update('tbllead_master',$data);
    }
}