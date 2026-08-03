<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class CategoryModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

        public function get_max_cat_id()
        {         
            $this->db->select_max('id'); 
            $query = $this->db->get('tblsubcategory');        
          
            $result = $query->row(); 
            return $result ? $result->id : 0;
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
		
		//=========== Sub Category add && Edit ================================
		
		public function get_max_Subcat_id()
        {         
            $this->db->select_max('id'); 
            $query = $this->db->get('tblK1ItemSubCategory');        
            $result = $query->row(); 
            return $result ? $result->id : 0;
        }
		
		public function get_Subcatdata($tbl, $where)
		{
			$this->db->select("$tbl.*");
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->row_array();
		}
		
		public function get_all_table_Subcatdata($tbl)
		{
			$this->db->select("$tbl.*, tblK1ItemCategory.SubcategoryName as SubCat");
			$this->db->from($tbl);
			$this->db->join('tblK1ItemCategory', "tblK1ItemCategory.id = $tbl.CategoryID", 'left');
			$query = $this->db->get();
			return $query->result_array();
		}
		public function getCategory()
    {
        $this->db->order_by('SubcategoryName', 'ASC');
        return $this->db->get('tblK1ItemCategory')->result_array();
    }
	
		public function GetCategoryFromSubCategoryCode($Category_ID)
    {
        $this->db->where('CategoryID',$Category_ID);
        return $this->db->get('tblK1ItemSubCategory')->result_array();
    }
		
		
		 public function insert_subcategory_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
		}
		public function edit_SubCategotydata($tbl,$where,$arr) 
		{
			$this->db->where($where);
			if ($this->db->update($tbl, $arr)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		

    }