<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class CommsionModel extends App_Model
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
        public function insert_batch_data($tbl,$data) 
		{      
			$this->db->insert_batch($tbl, $data);
			return $this->db->insert_id();
		}

        public function GetCommisionMasterData()
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			
			$this->db->select('tblCommisionMaster.*,tblclients.company,tblCenterMaster.CenterName,tblproduct.ProductName');
			$this->db->from(db_prefix() . 'CommisionMaster');
			$this->db->join(db_prefix() . 'clients','tblclients.AccountID = tblCommisionMaster.AccountID AND tblclients.PlantID = "'.$selected_company.'"');
			$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'CommisionMaster.CenterID', 'left');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblCommisionMaster.ItemID AND tblproduct.PlantID = "'.$selected_company.'"');
			$this->db->order_by(db_prefix() . 'CommisionMaster.id', 'ASC');
			return $this->db->get()->result_array();
		}
		public function GetItemListByVendorID($AccountIDs)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			
			$this->db->select('tblproduct.*');
			$this->db->from(db_prefix() . 'product');
			$this->db->where_in(db_prefix() . 'product.ItemFor', $AccountIDs);
			$this->db->where(db_prefix() . 'product.isactive', "Y");
			$this->db->order_by(db_prefix() . 'product.ProductName', 'ASC');
			return $this->db->get()->result_array();
		}
        public function get_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->row_array();
		}

        public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
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
		
			
		public function get_items_code()
		{
			$selected_company = $this->session->userdata('root_company');   
			return $this->db->query('SELECT ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID FROM '.db_prefix().'product WHERE PlantID = '.$selected_company)->result_array();
		}
		
		public function GetAccountList()
		{
			$this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
			$this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID');
			$this->db->join('tblxx_statelist', 'tblxx_statelist.id = tblclients.state', 'LEFT');
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
			$this->db->where('tblclients.CustomerType', '3');
			$this->db->where('tblclients.IsKirtiOneAccess', 'Y');
			$Data = $this->db->get('tblclients')->result_array();
			return $Data;
		}
		
		public function GetFilterwiseCommisionData($filterdata)
		{
		    $this->db->select('tblCommisionMaster.*, tblCenterMaster.CenterName, tblproduct.ProductName, tblclients.company');
            $this->db->from(db_prefix() . 'CommisionMaster');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCommisionMaster.CenterID', 'LEFT');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblCommisionMaster.ItemID', 'LEFT');
            $this->db->join('tblclients', 'tblclients.AccountID = tblCommisionMaster.AccountID', 'LEFT');
            
            if (!empty($filterdata['centername'])) 
            {
                $centers = [];
        
                if (is_array($filterdata['centername'])) {
                    $centers = array_map('trim', $filterdata['centername']);
                } elseif (is_string($filterdata['centername'])) {
                    $centers = array_map('trim', explode(',', $filterdata['centername']));
                }
        
                $centers = array_filter($centers, fn($val) => $val !== '');
        
                if (!empty($centers)) {
                    if (count($centers) === 1) {
                        $this->db->where('tblCommisionMaster.CenterID', $centers[0]);
                    } else {
                        $this->db->where_in('tblCommisionMaster.CenterID', $centers);
                    }
                }
            }
            
            if(!empty($filterdata['filtervendor']))
            {
                 $this->db->where('tblCommisionMaster.AccountID', $filterdata['filtervendor']);
            }
            
            if (!empty($filterdata['filterItemCode'])) 
            {
                $items = [];
                if (is_array($filterdata['filterItemCode'])) {
                    $items = array_map('trim', $filterdata['filterItemCode']);
                } elseif (is_string($filterdata['filterItemCode'])) {
                    $items = array_map('trim', explode(',', $filterdata['filterItemCode']));
                }
            
                $items = array_filter($items, fn($val) => $val !== '');
            
                if (!empty($items)) {
                    if (count($items) === 1) {
                        $this->db->where('tblCommisionMaster.ItemID', $items[0]);
                    } else {
                        $this->db->where_in('tblCommisionMaster.ItemID', $items);
                    }
                }
            }
        
            return $this->db->get()->result_array();
		}
		
		public function get_company_detail()
    	{
    		$selected_company = $this->session->userdata('root_company');
    		$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
    		FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
    			$result = $this->db->query($sql)->row();
    			return $result;
    	}
    }