<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class SeedModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}

        public function get_max_seed_id()
        {         
            $this->db->select_max('id'); 
            $query = $this->db->get('tblseed');        
          
            $result = $query->row(); 
            return $result ? $result->id : 0;
        }

        public function insert_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
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
    }