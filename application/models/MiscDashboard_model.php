<?php 
defined('BASEPATH') or exit('No direct script access allowed');
class MiscDashboard_model extends App_Model
{
		
	public function __construct()
	{
		parent::__construct();
	}
	public function GetAllStaffList()
	{ 
		$this->db->select('tblstaff.*');
		$this->db->where('tblstaff.admin', '0');
		$this->db->order_by('tblstaff.firstname');
		return $this->db->get('tblstaff')->result_array();
	}
//============ Get Center Wise Staff Wise Purchase =============================
	public function GetCenterWiseStaffWisePurchase($filter_data) 
	{
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
    
        $from_date = to_sql_date($filter_data['from_date']);
        $to_date = to_sql_date($filter_data['to_date']);

        $this->db->select('
            tblGateMaster.FeildOfficer,
            tblstaff.firstname,
            tblstaff.lastname,
            tblGateMaster.CenterID,
            tblCenterMaster.CenterName,
            SUM((tblGateMaster.LoadedWeight - tblGateMaster.TareWeight)/10) AS QtyMt
        ', false);
    
        $this->db->from('tblGateMaster');
        $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblGateMaster.CenterID');
        $this->db->join('tblstaff', 'tblstaff.AccountID = tblGateMaster.FeildOfficer', 'left');

        $this->db->where('tblGateMaster.PlantID', $selected_company);
        $this->db->where('tblGateMaster.FY', $fy);
    
        // Important: Force TType = 'P' as per your SQL
        $this->db->where('tblGateMaster.TType', 'P');
    
        if (!empty($filter_data['ItemID'])) {
            $this->db->where('tblGateMaster.ItemID', $filter_data['ItemID']);
        }

        if (!empty($filter_data['CenterID'])) {
            $this->db->where('tblGateMaster.CenterID', $filter_data['CenterID']);
        }
    
        if (!empty($filter_data['FeildOfficer'])) {
            $this->db->where('tblGateMaster.FeildOfficer', $filter_data['FeildOfficer']);
        }

        $this->db->where('tblGateMaster.Gate_in_ID IS NOT NULL', null, false);
        $this->db->where('tblGateMaster.LoadedWeight IS NOT NULL', null, false);
        $this->db->where('tblGateMaster.TareWeight IS NOT NULL', null, false);
    
        $this->db->where('tblGateMaster.gate_in_date >=', $from_date . ' 00:00:00');
        $this->db->where('tblGateMaster.gate_in_date <=', $to_date . ' 23:59:59');
    
        $this->db->group_by(['tblGateMaster.CenterID', 'tblGateMaster.FeildOfficer']);
        $this->db->order_by('tblCenterMaster.CenterName', 'ASC');
    
        $query = $this->db->get();
        return $query->result();
    }
    
//==================== Get Center Wise Purchase Quantity =======================
    public function GetCenterWisePurchase($filter_data)
	{	
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$from_date = to_sql_date($filter_data['from_date']);
		$to_date = to_sql_date($filter_data['to_date']);
		
		$this->db->select('tblGateMaster.CenterID, tblCenterMaster.CenterName, SUM((tblGateMaster.LoadedWeight - tblGateMaster.TareWeight)/10) AS QtyMt');
		$this->db->from('tblGateMaster');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->where('tblGateMaster.PlantID', $selected_company);
		$this->db->where('tblGateMaster.FY', $fy);
		$this->db->where('tblGateMaster.TType', 'P');
		if($filter_data['ItemID'] != ''){
		$this->db->where('tblGateMaster.ItemID',$filter_data['ItemID']);
    	} 
		if($filter_data['CenterID'] != ''){
		$this->db->where('tblGateMaster.CenterID',$filter_data['CenterID']);
    	} 
		if($filter_data['TType'] != ''){
		$this->db->where('tblGateMaster.TType',$filter_data['TType']);
    	} 
		if($filter_data['FeildOfficer'] != ''){
		$this->db->where('tblGateMaster.FeildOfficer',$filter_data['FeildOfficer']);
    	}        
		  
		$this->db->where('tblGateMaster.Gate_in_ID IS NOT NULL', null, false);
		$this->db->where('tblGateMaster.LoadedWeight IS NOT NULL', null, false);
		$this->db->where('tblGateMaster.TareWeight IS NOT NULL', null, false);
			
	    $this->db->where('tblGateMaster.gate_in_date >=', $from_date.' 00:00:00');
		$this->db->where('tblGateMaster.gate_in_date <=', $to_date.' 23:59:59');
		$this->db->group_by('tblGateMaster.CenterID');
		$this->db->order_by('tblCenterMaster.CenterName', 'ASC');
		$query = $this->db->get();
		return $result = $query->result();
	}
//===================== Center Wise Purchase Chart =============================
    public function GetCenterWisePurchaseChart($filter_data) 
    {     
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
		];
		
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$from_date = to_sql_date($filter_data['from_date']);
		$to_date = to_sql_date($filter_data['to_date']);
		
		// Fetch Top 5 Products by Sales Amount
		$this->db->select('tblGateMaster.CenterID, tblCenterMaster.CenterName, SUM((tblGateMaster.LoadedWeight - tblGateMaster.TareWeight)/10) AS QtyMt');
		$this->db->from('tblGateMaster');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->where('tblGateMaster.PlantID', $selected_company);
		$this->db->where('tblGateMaster.FY', $fy);
		$this->db->where('tblGateMaster.TType', 'P');
		if($filter_data['ItemID'] != ''){
		$this->db->where('tblGateMaster.ItemID',$filter_data['ItemID']);
    	} 
		if($filter_data['CenterID'] != ''){
		$this->db->where('tblGateMaster.CenterID',$filter_data['CenterID']);
    	}
		if($filter_data['TType'] != ''){
		$this->db->where('tblGateMaster.TType',$filter_data['TType']);
    	} 
		if($filter_data['FeildOfficer'] != ''){
		$this->db->where('tblGateMaster.FeildOfficer',$filter_data['FeildOfficer']);
    	}         
		  
		$this->db->where('tblGateMaster.Gate_in_ID IS NOT NULL', null, false);
		$this->db->where('tblGateMaster.LoadedWeight IS NOT NULL', null, false);
		$this->db->where('tblGateMaster.TareWeight IS NOT NULL', null, false);
	
	    $this->db->where('tblGateMaster.gate_in_date >=', $from_date.' 00:00:00');
		$this->db->where('tblGateMaster.gate_in_date <=', $to_date.' 23:59:59');
		$this->db->group_by('tblGateMaster.CenterID');
		$query = $this->db->get();
		$result = $query->result();
		
		$chart = [];
		$totalAmount = 0;
		$i = 0;
		
		if ($filter_data['ChartType'] === "Pie") {
			foreach ($result as $row) {
				$totalAmount += (float)$row->QtyMt;
			}
		}
		
		// 2. Prepare chart data
		foreach ($result as $row) {
			$amount = (float)$row->QtyMt;
			
			$value = $filter_data['ChartType'] === "Pie"
			? ($totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0)
			: $amount;
			
			
			$chart[] = [
			'CenterID' => $row->CenterID,
			'name'      => $row->CenterName,
			'y'         => $value,
			'color'     => $color_data[$i % count($color_data)],
			'z'         => 100
			];
			$i++;
		}
		
		return ['ChartData' => $chart];
	}
	
	public function getAllCenters(){
		return $this->db->get('tblCenterMaster')->result_array();
	}
	
	public function getItems(){
		$this->db->order_by('tblitems.ItemID', 'ASC');
		return $this->db->get('tblitems')->result_array();
	}
		
}	