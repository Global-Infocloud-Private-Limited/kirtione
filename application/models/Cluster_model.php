<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cluster_model extends App_Model
{
    public function getState()
    {
        $this->db->order_by('state_name', 'ASC');
        return $this->db->get('tblxx_statelist')->result_array();
    }
    
    public function getCompetitor()
    {
        $this->db->where('isedit','0');
        $this->db->where('Type','C');
        return $this->db->get('tblCompetitorMaster')->result_array();
    }
    public function getMandi()
    {
        $this->db->where('isedit','0');
        $this->db->where('Type','M');
        return $this->db->get('tblCompetitorMaster')->result_array();
    }
    
    public function getCommodity()
    {
        $UserID = $this->session->userdata('username');
        $this->db->select('tblitems.*,tblitems_sub_groups.name');
        $this->db->join('tblitems_sub_groups','tblitems_sub_groups.id = tblitems.subgroup_id');
        $this->db->join('tblItemwise_staff_priority','tblItemwise_staff_priority.ItemID = tblitems.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"','LEFT');
        $this->db->where('tblitems_sub_groups.main_group_id','3');
        $this->db->where('tblitems.isactive','Y');
        $this->db->order_by('tblItemwise_staff_priority.priority', 'ASC');
        return $this->db->get('tblitems')->result_array();
    }
    public function GetPlant()
    {
        return $this->db->get('tblPlantMaster')->result_array();
    }
    public function GetRegionList()
    {
        return $this->db->get('tblRegion')->result_array();
    }
    
    public function getCity($state)
    {
        $this->db->where('state',$state);
        return $this->db->get('tbl_xx_city')->result_array();
    }
    
    public function getCityBYStateCode($state)
    {
        $this->db->where('state_id',$state);
        return $this->db->get('tblxx_citylist')->result_array();
    }
    
    public function getCityById($city_id){
        $this->db->where('id',$city_id);
        return $this->db->get('tbl_xx_city')->row();
    }
    
    public function getClusterData(){
        return $this->db->get('tblCluster')->result_array();
    }
    
    public function getClusterDb($AccountID){
        $this->db->where('AccountID',$AccountID);
        return $this->db->get('tblCluster')->row();
    }
    
    public function getRegionDb($AccountID){
        $this->db->where('AccountID',$AccountID);
        return $this->db->get('tblRegion')->row();
    }
    
    public function saveCluster($data){
        return $this->db->insert('tblCluster',$data);
    }
    
    public function getStateNames($state_id){
        $this->db->where('id',$state_id);
        return $this->db->get('tblxx_statelist')->row();
    }
    
    public function getRegionData(){
        return $this->db->get('tblRegion')->result_array();
    }
    
    public function saveRegion($data){
        return $this->db->insert('tblRegion',$data);
    }
    
    public function updateRegion($data){
        $this->db->where('AccountID',$data['AccountID']);
        return $this->db->update('tblRegion',$data);
    }
    
    public function updateCluster($data){
        $this->db->where('AccountID',$data['AccountID']);
        return $this->db->update('tblCluster',$data);
    }
    
    public function getSingleRegionDb($AccountID){
        $this->db->where('AccountID',$AccountID);
        return $this->db->get('tblRegion')->row();
    }
    
    public function getSingleClusterDb($AccountID){
        $this->db->where('AccountID',$AccountID);
        return $this->db->get('tblCluster')->row();
    }
    
    public function getMandiData(){
        $this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblCenterMaster.state');
        $this->db->join('tblxx_citylist','tblxx_citylist.id = tblCenterMaster.city');
        return $this->db->get('tblCenterMaster')->result_array();
    }
    
    public function getAllMandiDb()
    {
        $this->db->select('tblCenterMaster.*,tblxx_statelist.state_name,tblxx_citylist.city_name,tblRegion.region');
        $this->db->join('tblRegion','tblRegion.AccountID = tblCenterMaster.RegionID',"LEFT");
        $this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblCenterMaster.state',"LEFT");
        $this->db->join('tblxx_citylist','tblxx_citylist.id = tblCenterMaster.city',"LEFT");
        return $this->db->get('tblCenterMaster')->result_array();
    }
    
    public function getSingleMandiDb($center_id)
    {
        $this->db->where('CenterID',$center_id);
        $data = $this->db->get('tblCenterMaster')->row();
        if($data){
            
            // Commision Parametr
            $this->db->select('tblCommisionMatrix.*');
            $this->db->from(db_prefix() .'CommisionMatrix');
            $this->db->join('tblitems','tblitems.ItemID = tblCommisionMatrix.ItemID');
            $this->db->where('tblCommisionMatrix.CenterID', $center_id);
            $this->db->where('tblCommisionMatrix.IsActive', 'Y');
            $this->db->where('tblitems.isactive', 'Y');
            $CommisionList = $this->db->get()->result_array();
            $data->Commision = $CommisionList;
            
            // Center wise Item
            $this->db->select('tblCenter_wise_item.*');
            $this->db->from(db_prefix() .'Center_wise_item');
            $this->db->where('tblCenter_wise_item.CenterID', $center_id);
            $ItemList = $this->db->get()->result_array();
            $data->Items = $ItemList;
        }
        return $data;
    }
    
    public function saveMandi($data){
        return $this->db->insert('tblCenterMaster',$data);
    }
    
    public function saveNumberFormat($data){
        return $this->db->insert_batch('tblnumberformat',$data);
    }
    
    public function updateCenter($data)
    {
        
		$CommisionAssign = $data["CommisiondataSerializedArr"];
		$CommisionArray = json_decode($CommisionAssign, true);
		$CommisionArraylen = count($CommisionArray);
        unset($data["CommisiondataSerializedArr"]);
        
        $UserID = $this->session->userdata('username');
        $this->db->where('CenterID',$data['CenterID']);
        if($this->db->update('tblCenterMaster',$data)){
            
            // Move data to Audit table
            $GetCommisionMatrix = $this->GetCommisionMatrix($data['CenterID']);
            foreach($GetCommisionMatrix as $key=>$val){
                $Move_array = array(
                    "CenterID"=>$val["CenterID"],
                    "ItemID"=>$val["ItemID"],
                    "PartyID"=>$val["PartyID"],
                    "CommisionAmt"=>$val["CommisionAmt"],
                    "UserID"=>$val["UserID"],
                    "TransDate"=>$val["TransDate"],
                    "IsActive"=>$val["IsActive"],
                    "IsOn"=>$val["IsOn"],
                    "created_by"=>$this->session->userdata('username'),
                    "created_at"=>date('Y-m-d H:i:s'),
                );
                $this->db->insert(db_prefix() . 'CommisionMatrixAudit', $Move_array);
            }
            $this->db->where('CenterID',$data['CenterID']);
            $this->db->delete('tblCommisionMatrix');
            foreach ($CommisionArray as $value) {
                $insertComm = array(
                    "CenterID" =>$this->input->post('CenterID'),
                    "ItemID" =>$value["0"],
                    "PartyID" =>$value["1"],
                    "CommisionAmt" =>$value["2"],
                    "IsOn" =>"Y",
                    "UserID" =>$this->session->userdata('username'),
                    "TransDate" =>date('Y-m-d H:i:s'),
                );
                $this->db->insert(db_prefix() . 'CommisionMatrix', $insertComm);
            }
			return true;
        }else{
            return false;
        }
    }
    
    public function GetCommisionMatrix($CenterID)
	{
	    $this->db->select('tblCommisionMatrix.*');
		$this->db->where('CenterID', $CenterID);
		return $this->db->get('tblCommisionMatrix')->result_array();
	}
    public function getCenterList()
	{
		$this->db->order_by('CenterName', 'ASC');
		return $this->db->get('tblCenterMaster')->result_array();
	}
		
}