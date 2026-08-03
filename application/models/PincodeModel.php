<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class PincodeModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function insert_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
		}

		public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function get_column_data($tbl, $column)
		{			
			$this->db->select($column);
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function get_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->row_array();
		}

		public function edit_data($tbl,$where,$arr) 
		{
			$this->db->where($where);
			if ($this->db->update($tbl, $arr)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		public function GetDataPincode($data)
		{			
			$pin = $data;
			
			$this->db->select('tblpin.id,tblpin.Pincode,tblxx_statelist.state_name,tblxx_statelist.short_name,tblxx_citylist.city_name,tblxx_citylist.id,
			tblTalukaMaster.TalukaName,tblTalukaMaster.id');		
			$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblpin.State');
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblpin.District');
			$this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblpin.Taluka');
			$this->db->where("tblpin.Pincode", $pin); 
			$this->db->limit(1); 		
			$result = $this->db->get(db_prefix() . 'pin')->row(); 
			return $result;
		}
    }