<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CompanyMaster_Model extends App_Model
{
    public function getState()
    {
        $this->db->order_by('state_name', 'ASC');
        return $this->db->get('tblxx_statelist')->result_array();
    }
    public function GetCompanyList()
    {
        $this->db->select('PlantMaster.*');
        $Data = $this->db->get('PlantMaster')->result_array();
        return $Data;
    } 
    
    public function GetCompanyDetails($PlantID)
    {
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblPlantMaster.*');
        $this->db->where(db_prefix() . 'PlantMaster.PlantID', $PlantID);
        $result = $this->db->get('tblPlantMaster')->row();
        if($result){
            $CityList = $this->getCityList($result->state);
            $TalukaList = $this->GetTaluka($result->city);
            $result->CityList = $CityList;
            $result->TalukaList = $TalukaList;
        }
        return $result;
       
    }
    
    public function GetCityList($StateID)
    {
        $this->db->where(db_prefix() . 'xx_citylist.state_id', $StateID);
        $this->db->order_by(db_prefix() . 'xx_citylist.city_name', 'ASC');
        return $this->db->get('tblxx_citylist')->result_array();
    }
    
    public function GetTaluka($CityID)
    {
        $this->db->select(db_prefix() . 'TalukaMaster.*');
        $this->db->where(db_prefix() . 'TalukaMaster.DistrictID', $CityID);
        $this->db->order_by(db_prefix() . 'TalukaMaster.TalukaName', 'ASC');
        return $this->db->get('tblTalukaMaster')->result_array();
    }
    
    public function SaveCompany($data)
	{
	    $LogID = $this->session->userdata('username');
        $CompArray = array(
            'PlantID'=>$data["comp_code"],
            'PlantName'=>$data["comp_name"],
            'state'=>$data["state"],
            'city'=>$data["city"],
            'taluka'=>$data["taluka"],
            'pincode'=>$data["pincode"],
            'address'=>$data["address"],
            'GstNo'=>$data["GstNo"],
            'fssai_no'=>$data["fssai_no"],
            'UserID' =>$LogID,
            'Transdate' =>date('Y-m-d H:i:s'),
        );
        $this->db->insert(db_prefix() . 'PlantMaster',$CompArray);
        $LastId = $this->db->insert_id();
        if($LastId){
            return true;
        }else{
            return false;
        }		
	}
	
	public function UpdateCompany($data)
	{
	    $LogID = $this->session->userdata('username');
        $CompArray = array(
            'PlantName'=>$data["comp_name"],
            'state'=>$data["state"],
            'city'=>$data["city"],
            'taluka'=>$data["taluka"],
            'pincode'=>$data["pincode"],
            'address'=>$data["address"],
            'GstNo'=>$data["GstNo"],
            'fssai_no'=>$data["fssai_no"],
            'UserID2' =>$LogID,
            'Lupdate' =>date('Y-m-d H:i:s'),
        );
        $this->db->where('PlantID', $data["comp_code"]);
        $this->db->update(db_prefix() . 'PlantMaster', $CompArray);
        if($this->db->affected_rows() > 0){
           return true;
        }else{
            return false;
        }	
	}
}