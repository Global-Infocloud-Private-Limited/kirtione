<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Booking_list_model extends App_Model
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
//======================== All Type of Trade list ==============================
	public function GetAllBookingsDB($data)
	{
	    $AccountID = $data["AccountID"];
		$from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		//$WHID = $data['WHID'];
		$CenterID = $data['CenterID'];
		$ItemID = $data['ItemID'];
		$BookingType = $data['BookingType'];
		$IsApprove = $data['IsApprove'];
		
		$this->db->select('tbllead_master.*,tblpcsoft_gic_number_referance.pcsoft_doc_ref,tblpcsoft_gic_number_referance.GIC_Reference,tblCenterMaster.CenterName,tblitems.ItemName,
		tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,
		tblloan_history.BookingID as LoanID');
		$this->db->join('tblpcsoft_gic_number_referance','tblpcsoft_gic_number_referance.GIC_Reference = tbllead_master.BookingID','LEFT');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID','left');
		$this->db->join('tblloan_history','tblloan_history.BookingID = tbllead_master.BookingID','left');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
		
		/*if($data["WHID"] && $data["WHID"] !== ""){
			$this->db->where('tbllead_master.WHID', $WHID);
		}*/
		if($data["CenterID"] && $data["CenterID"] !== ""){
			// $this->db->where('tbllead_master.CenterID', $CenterID);
			$this->db->where_in('tbllead_master.CenterID', $CenterID);
		}
		if($data["from_date"] !== "" && $data["to_date"] !== ""){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data["ItemID"] && $data["ItemID"] !== ""){
			$this->db->where('tbllead_master.ItemID', $ItemID);
		}
		if($data["AccountID"] && $data["AccountID"] !== ""){
			$this->db->where('tbllead_master.AccountID', $data['AccountID']);
		}
		if($data["IsApprove"] && $data["IsApprove"] !== ""){
			$this->db->where('tbllead_master.IsApprove', $data['IsApprove']);
		}
		if($data["BookingType"] && $data["BookingType"] !== ""){
			$this->db->where('tbllead_master.TType', $BookingType);
		}
		$this->db->order_by('tbllead_master.id','DESC');
		return $this->db->get('tbllead_master')->result_array();
	}
	
	public function GetBookingListDetailsDB($BookingID){
		$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname,tblwarehouse.center,tblwarehouse.w_name');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','left');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
		$this->db->where('tbllead_master.BookingID',$BookingID);
		$this->db->order_by('tbllead_master.id','DESC');
		return $this->db->get('tbllead_master')->row();
	}
	
	public function GetBookingsFromBookingIDDB($BookingID){
		$this->db->select('tblGateMaster.*');
		$this->db->where('tblGateMaster.BookingID',$BookingID);
		return $this->db->get('tblGateMaster')->result_array();
	}
	
	public function CheckAsnLockDB($BookingID){
		$this->db->where('BookingID',$BookingID);
		$this->db->where('status <',2);
	    $this->db->order_by('id','DESC');
		$result = $this->db->get('tblGateMaster')->result_array();
		return $result;
	}
}