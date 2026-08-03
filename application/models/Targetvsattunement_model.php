<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Targetvsattunement_model extends App_Model
	{
		public function __construct()
		{ 
			parent::__construct();
		}
		public function get_all_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
		}
		public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}	
		public function get_items_code()
		{
			$selected_company = $this->session->userdata('root_company');   
			return $this->db->query('SELECT ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID FROM '.db_prefix().'product WHERE PlantID = '.$selected_company)->result_array();
		}
		public function getstatelist()
		{
			$Data = $this->db->get('tblxx_statelist')->result_array();
			return $Data;
		}
		public function get_company_detail()
		{
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		
		public function GetcompanyStaff()
		{
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			
			$this->db->select("tblstaff.AccountID, 
			CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) AS staff_name,
			tblStaffWiseTarget.APR,
			tblStaffWiseTarget.MAY,
			tblStaffWiseTarget.JUN,
			tblStaffWiseTarget.JUL,
			tblStaffWiseTarget.AUG,
			tblStaffWiseTarget.SEP,
			tblStaffWiseTarget.OCT,
			tblStaffWiseTarget.NOV,
			tblStaffWiseTarget.DESC,
			tblStaffWiseTarget.JAN,
			tblStaffWiseTarget.FEB,
			tblStaffWiseTarget.MAR"); // avoid column name clash
			
			$this->db->from("tblstaff");
			$this->db->join("tblStaffWiseTarget", "tblStaffWiseTarget.AccountID = tblstaff.AccountID AND tblStaffWiseTarget.PlantID = tblstaff.PlantID", "left");
			
			$this->db->where("tblstaff.PlantID", $PlantID);
			$this->db->where("tblstaff.AccountID IS NOT NULL");
			$this->db->where("tblstaff.active", 1);
			$this->db->where("tblstaff.admin", 0);
			
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function get_TargetVsAttunement_detail($id)
		{
			$this->db->select("tblStaffWiseTarget.*");
			$this->db->from("tblStaffWiseTarget");
			$this->db->where("tblStaffWiseTarget.id", $id);
			
			$result = $this->db->get();
			return $result->result_array();
		}
		
		
		public function AddKirtiOneTargetvsattunementNew($data)
		{
            $es_detail = [];
            if (isset($data['pur_order_detail'])) {  
    		$pur_order_detail_json = $data['pur_order_detail'];
    		unset($data['pur_order_detail']); 
		
    		$decoded_data = json_decode($pur_order_detail_json, true); 
    		if (json_last_error() !== JSON_ERROR_NONE) {
    		log_message('error', 'JSON decoding error in AddKirtiOneTargetvsattunementNew: ' . json_last_error_msg());
    		return 0;
    		}
		
    		$row = [];
    		$rq_val = [];
    		$header = [];
    		$header[] = 'AccountID';
    		$header[] = 'staff_name';
    		$header[] = 'APR';
    		$header[] = 'MAY';
    		$header[] = 'JUN';
    		$header[] = 'JUL';
    		$header[] = 'AUG';
    		$header[] = 'SEP';
    		$header[] = 'OCT';
    		$header[] = 'NOV';
    		$header[] = 'DESC';
    		$header[] = 'JAN';
    		$header[] = 'FEB';
    		$header[] = 'MAR';
		
    		foreach ($decoded_data as $value) {
    		if (is_array($value) && count($value) >= count($header) && !empty($value[2])) { 
    		$es_detail[] = array_combine($header, $value);
    		} else {
    		log_message('warning', 'Skipping malformed or incomplete row in pur_order_detail: ' . json_encode($value));
    		}
    		}
            } else {
    		log_message('warning', 'No pur_order_detail found in input data for AddKirtiOneTargetvsattunementNew.');
    		return 0; 
            }
        
            $PlantID    = $this->session->userdata('root_company');
            $FY         = $this->session->userdata('finacial_year');
            $UserID     = $this->session->userdata('username') ? $this->session->userdata('username') : 'Unknown';
            $Transdate  = date('Y-m-d H:i:s'); 
		
            $processed_records_count = 0;
       
            foreach ($es_detail as $value) {
    		$AccountID = trim($value['AccountID']);
	
    		if (empty($AccountID)) {
    		log_message('warning', 'Skipping record due to empty AccountID: ' . json_encode($value));
    		continue;
    		}
		
    		$data_array_for_db = [
    		'PlantID'   => $PlantID,
    		'FY'        => $FY,
    		'AccountID' => $AccountID, 
    		'APR'       => $value['APR'],
    		'MAY'       => $value['MAY'],
    		'JUN'       => $value['JUN'],
    		'JUL'       => $value['JUL'],
    		'AUG'       => $value['AUG'],
    		'SEP'       => $value['SEP'],
    		'OCT'       => $value['OCT'],
    		'NOV'       => $value['NOV'],
    		'DESC'      => $value['DESC'], 
    		'JAN'       => $value['JAN'],
    		'FEB'       => $value['FEB'],
    		'MAR'       => $value['MAR'],
    		'UserID'    => $UserID,    
    		'TransDate' => $Transdate  
    		];
		
    		$this->db->trans_start();
    		if($es_detail > 0)
    		{
        		$this->db->where('AccountID', $AccountID);
        		$this->db->where('PlantID', $PlantID);
        		$this->db->where('FY', $FY);
        		$query = $this->db->get(db_prefix() . 'StaffWiseTarget');
    		}
    		
    		if ($query->num_rows() > 0) 
    		{
    		    $existing_data = $query->row_array();
    		
        		$existing_data_upper = array_change_key_case($existing_data, CASE_UPPER);
        		$new_data_upper = array_change_key_case($data_array_for_db, CASE_UPPER);
        		
        		$has_changes = false;
        		$changed_fields = []; 
    	
        		$fields_to_compare = array_keys($data_array_for_db);
        		foreach ($fields_to_compare as $field) 
        		{
    		
            		if (in_array($field, ['TransDate', 'UserID', 'AccountID', 'PlantID', 'FY'])) {
                        continue;
                    }
        		    
        		    $key = strtoupper($field);
                    $old_val = isset($existing_data_upper[$key]) ? trim((string)$existing_data_upper[$key]) : '';
                    $new_val = isset($new_data_upper[$key]) ? trim((string)$new_data_upper[$key]) : '';
            
                    if ($old_val !== $new_val) {
                        $has_changes = true;
                        $changed_fields[$field] = ['old' => $old_val, 'new' => $new_val];
                    }
        		}
    		
        		if ($has_changes) 
        		{
        		    $history_data = $existing_data; 
        	
            		$history_data['HistoryTransDate'] = date('Y-m-d H:i:s'); 
            		$history_data['HistoryUserID'] = $UserID; 
        		
            		if (!$this->db->insert(db_prefix() . 'StaffWiseTarget_history', $history_data)) {
            		log_message('error', 'Failed to insert into StaffWiseTarget_history for AccountID: ' . $AccountID . ' Error: ' . print_r($this->db->error(), true));
            		$this->db->trans_rollback(); 
            		continue; 
            		}
        		
            		$this->db->where('AccountID', $AccountID);
            		$this->db->where('PlantID', $PlantID);
            		$this->db->where('FY', $FY);
            		if (!$this->db->update(db_prefix() . 'StaffWiseTarget', $data_array_for_db)) 
            		{
                		log_message('error', 'Failed to update StaffWiseTarget for AccountID: ' . $AccountID . ' Error: ' . print_r($this->db->error(), true));
                		$this->db->trans_rollback(); 
                		continue; 
            		}
        		} else {
        		log_message('info', 'No changes detected for AccountID: ' . $AccountID . ' (PlantID: ' . $PlantID . ', FY: ' . $FY . '). Skipping update.');
        		}
    		} else 
    		{
        		if (!$this->db->insert(db_prefix() . 'StaffWiseTarget', $data_array_for_db)) {
        		log_message('error', 'Failed to insert new StaffWiseTarget record for AccountID: ' . $AccountID . ' Error: ' . print_r($this->db->error(), true));
        		$this->db->trans_rollback(); 
        		continue; 
    		    }
    		}
		
    		if ($this->db->trans_status() === FALSE) {
    		} else {
        		$this->db->trans_commit(); 
        		$processed_records_count++;
    		}
        }
        return $processed_records_count;
		}
	}	
?>