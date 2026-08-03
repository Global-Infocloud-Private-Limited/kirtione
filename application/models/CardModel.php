<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class CardModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

        //common functions
		public function insert_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
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

		public function delete_data($tbl, $where) 
		{
			$this->db->where($where);
			if ($this->db->delete($tbl)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}    

		public function delete_all_data($tbl) 
		{			
			$this->db->empty_table($tbl); 			
			if ($this->db->affected_rows() >= 0) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		public function delete_entries_by_ids($ids) 
		{			
			if (is_array($ids) && !empty($ids)) 
			{
				$this->db->where_in('id', $ids);
				$this->db->delete('tblcarddetails'); 
			}
		}

		
	

    public function get_all_data($tbl,$where)
    {
        $this->db->select('*');
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function GetCardList()
    {   
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblAccountWiseCardMaster.AccountID,tblclients.company');
        $this->db->join('tblclients', 'tblclients.AccountID = tblAccountWiseCardMaster.AccountID');
        $this->db->order_by('id', 'asc');
        $CardList = $this->db->get(db_prefix() . 'AccountWiseCardMaster')->result_array();

        return $CardList;
    }
    
    public function GetDataFromDateToDate($data_filter)
    {
        $from_date = date('Y-04-01');
        $to_date = date('Y-m-d');
        
        if(isset($data_filter['from_date'])){
            $from_date = to_sql_date($data_filter['from_date']);
        }

        if(isset($data_filter['to_date'])){
            $to_date = to_sql_date($data_filter['to_date']);
        }
        $selected_company = $this->session->userdata('root_company');
        $finacial_year = $this->session->userdata('finacial_year');
        $username = $this->session->userdata('username');
        $accounting_method = $data_filter['accounting_method'];
        
        $this->db->select(db_prefix().'CardPointsledger.*');
        $this->db->join('tblAccountWiseCardMaster', 'tblAccountWiseCardMaster.CardNumber = tblCardPointsledger.AccountID');
        $this->db->where(db_prefix().'CardPointsledger.PlantID', $selected_company);
        if(isset($data_filter['accounting_method'])){
            $this->db->where(db_prefix().'AccountWiseCardMaster.AccountID', $accounting_method);
        }
        $this->db->LIKE(db_prefix().'CardPointsledger.FY', $finacial_year);
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate>=',$from_date.' 00:00:00');
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate<=',$to_date.' 23:59:59');
        $this->db->order_by(db_prefix().'CardPointsledger.Transdate', "asc");
        $LedgerList = $this->db->get(db_prefix().'CardPointsledger')->result_array();
        return $LedgerList;		
    }
    
    public function GetCrSumBeforeFromDate($data_filter)
    {
        $newfrom_date = date('20'.$finacial_year.'-04-01');
        $to_date = to_sql_date($data_filter['from_date']);
        $to_date = date('Y-m-d', strtotime('-1 day', strtotime($to_date)));
        
        $selected_company = $this->session->userdata('root_company');
        $finacial_year = $this->session->userdata('finacial_year');
        $username = $this->session->userdata('username');
        $accounting_method = $data_filter['accounting_method'];
        
        $this->db->select_sum('tblCardPointsledger.Amount');
        $this->db->join('tblAccountWiseCardMaster', 'tblAccountWiseCardMaster.CardNumber = tblCardPointsledger.AccountID');
        $this->db->where(db_prefix().'CardPointsledger.PlantID', $selected_company);
        if(isset($data_filter['accounting_method'])){
            $this->db->where(db_prefix().'AccountWiseCardMaster.AccountID', $accounting_method);
        }
        $this->db->WHERE(db_prefix().'CardPointsledger.TType','C');
        $this->db->LIKE(db_prefix().'CardPointsledger.FY', $finacial_year);
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate>=',$from_date.' 00:00:00');
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate<=',$to_date.' 23:59:59');
        $this->db->order_by(db_prefix().'CardPointsledger.Transdate', "asc");
        $crAmt = $this->db->get(db_prefix().'CardPointsledger')->result_array();
		return $crAmt;
    }
    
    public function GetDrSumBeforeFromDate($data_filter)
    {
        $newfrom_date = date('20'.$finacial_year.'-04-01');
        $to_date = to_sql_date($data_filter['from_date']);
        $to_date = date('Y-m-d', strtotime('-1 day', strtotime($to_date)));
        
        $selected_company = $this->session->userdata('root_company');
        $finacial_year = $this->session->userdata('finacial_year');
        $username = $this->session->userdata('username');
        $accounting_method = $data_filter['accounting_method'];
        
        $this->db->select_sum('tblCardPointsledger.Amount');
        $this->db->join('tblAccountWiseCardMaster', 'tblAccountWiseCardMaster.CardNumber = tblCardPointsledger.AccountID');
        $this->db->where(db_prefix().'CardPointsledger.PlantID', $selected_company);
        if(isset($data_filter['accounting_method'])){
            $this->db->where(db_prefix().'AccountWiseCardMaster.AccountID', $accounting_method);
        }
        $this->db->WHERE(db_prefix().'CardPointsledger.TType','D');
        $this->db->LIKE(db_prefix().'CardPointsledger.FY', $finacial_year);
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate>=',$from_date.' 00:00:00');
        $this->db->WHERE(db_prefix().'CardPointsledger.Transdate<=',$to_date.' 23:59:59');
        $this->db->order_by(db_prefix().'CardPointsledger.Transdate', "asc");
        $crAmt = $this->db->get(db_prefix().'accountledger')->result_array();
		return $crAmt;
    }
    
    public function get_name_account($data_filter)
    {
        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get_base_currency();
        $acc_show_account_numbers = get_option('acc_show_account_numbers');

       $selected_company = $this->session->userdata('root_company');
        $finacial_year = $this->session->userdata('finacial_year');
        
        $this->db->where('PlantID', $selected_company);
        
        if(isset($data_filter['accounting_method'])){
            $accounting_method = $data_filter['accounting_method'];
            $this->db->where('AccountID', $accounting_method);
        }
        $accounts = $this->db->get(db_prefix().'clients')->row();
        if(empty($accounts)){
            if(isset($data_filter['accounting_method'])){
                $accounting_method = $data_filter['accounting_method'];
                $this->db->where('AccountID', $accounting_method);
            }
            $accounts = $this->db->get(db_prefix().'staff')->row();
        }
		return $accounts;		
    }
    
    public function GetAccountWiseCardList($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tblAccountWiseCardMaster.*,tblCardMaster.CardName,tblclients.company,
		tblclients.house,tblclients.street,tblclients.loc,tblclients.vtc,tblclients.po,tblclients.zip,tblxx_citylist.city_name,tblxx_statelist.state_name');
		$this->db->join('tblclients','tblclients.AccountID = tblAccountWiseCardMaster.AccountID');
		$this->db->join('tblCardMaster','tblCardMaster.Prefix = tblAccountWiseCardMaster.Prefix');
		$this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblclients.state',"LEFT");
		$this->db->join('tblxx_citylist','tblxx_citylist.id = tblclients.dist',"LEFT");
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tblAccountWiseCardMaster.IssueDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['paymentstatus'] != ''){
			$this->db->where('tblAccountWiseCardMaster.PaymentStatus',$data['paymentstatus']);
		}
		if($data['cardtype'] != ''){
			$this->db->where('tblAccountWiseCardMaster.Prefix',$data['cardtype']);
		}
		$this->db->order_by('tblAccountWiseCardMaster.IssueDate','ASC');
		return $this->db->get('tblAccountWiseCardMaster')->result_array();
	}
	
	public function GetAccountwiseCardRequestList($data)
    {
        $from_date = to_sql_date($data['from_date']);
        $to_date = to_sql_date($data['to_date']);
        $this->db->select('tblCardRequest.*,tblCardMaster.CardName,tblclients.company');
        $this->db->join('tblclients','tblclients.AccountID = tblCardRequest.AccountID');
        $this->db->join('tblCardMaster','tblCardMaster.Prefix = tblCardRequest.Prefix');
        if(($data['from_date'] != '') || ($data['to_date'] != '')){
            $this->db->where('tblCardRequest.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
        }
        if($data['status'] != ''){
            $this->db->where('tblCardRequest.status',$data['status']);
        }
        $this->db->order_by('tblCardRequest.TransDate','ASC');
        return $this->db->get('tblCardRequest')->result_array();
    }
    
    public function GetAccountwiseSoiltestRequestList($data)
    {
        $from_date = to_sql_date($data['from_date']);
        $to_date = to_sql_date($data['to_date']);
        $this->db->select('tblsoiltestrequest.*,tblCardMaster.CardName,tblclients.company');
        $this->db->join('tblclients','tblclients.AccountID = tblsoiltestrequest.AccountID');
        $this->db->join('tblCardMaster','tblCardMaster.Prefix = tblsoiltestrequest.Prefix');

        if(($data['from_date'] != '') || ($data['to_date'] != '')){
            $this->db->where('tblsoiltestrequest.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
        }
        if($data['status'] != ''){
            $this->db->where('tblsoiltestrequest.status',$data['status']);
        }
        $this->db->order_by('tblsoiltestrequest.TransDate','ASC');
        return $this->db->get('tblsoiltestrequest')->result_array();
    }
		//end here
}