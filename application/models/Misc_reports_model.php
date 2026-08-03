<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Misc_reports_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_data($tbl,$where)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->row_array();
	}
		
    /* Get main item group */
    public function get_main_item_group($id = '')
    {
        $this->db->select('*');
        $this->db->order_by('id','DESC');
        $this->db->where('IsStock',"Y");
        $this->db->from(db_prefix() . 'items_main_groups');
        return $this->db->get()->result_array();
    }
    // Gell All Active Center List
    public function GetCenterList($CenterIDs = "")
    {
        $this->db->select('*');
        $this->db->order_by('CenterName','ASC');
        if($CenterIDs){
            $this->db->where_in('CenterID',$CenterIDs);
        }
        $this->db->where('status',"Y");
        $this->db->from(db_prefix() . 'CenterMaster');
        return $this->db->get()->result_array();
    }
    // Get All Party List 
    public function GetPartyList()
    {
        $this->db->select('*');
        $this->db->order_by('PlantName','ASC');
        $this->db->from(db_prefix() . 'PlantMaster');
        return $this->db->get()->result_array();
    }
    // Get Warehouse By Center List
    public function GetWHListByCenterID($CenterID,$GodownID="")
    {
        $this->db->select('tblwarehouse.AccountID,tblwarehouse.w_name,tblwarehouse.center');
        if($GodownID){
            $this->db->where_in('AccountID',$GodownID);
        }
        $this->db->where_in('center',$CenterID);
        return $this->db->get(db_prefix().'warehouse')->result_array();
    }
    
    
    // All Warehouse List
    public function GetGodownData()
    {
        $PlantID = $this->session->userdata('root_company');
        return $this->db->get(db_prefix().'warehouse')->result_array();
    }
    
    public function GetItemList($filterdata,$item_group)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $GodownID = $filterdata["GodownID"]; 
        $item_group_array = explode(",",$item_group);
        
        $this->db->select('tblitems.ItemID,tblitems.ItemName');
        $this->db->where_in('tblitems.subgroup_id', $item_group_array);
        $this->db->where('tblitems.isactive', "Y");
        $ItemList = $this->db->get(db_prefix() . 'items')->result_array();
        
        return $ItemList;
        /*if($selected_company == "1"){
            $CustType = '1';
        }else if($selected_company == "2"){
            $CustType = '13';
        }else if($selected_company == "3"){
            $CustType = '21';
        }
        if($GodownID !==''){
        $sql = 'SELECT tblitems.PlantID,tblitems.ItemID,tblitems.ItemName,tblitems.unit,
        (SELECT SUM(tblstockmaster.OQty) AS OQty FROM tblstockmaster WHERE tblstockmaster.ItemID=tblitems.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.GodownID = "'.$GodownID.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty
        FROM `tblitems` 
        WHERE tblitems.PlantID = '.$selected_company.'   
         AND tblitems.subgroup_id IN('.$item_group.')';
        
        }else{
            $sql = 'SELECT  tblitems.PlantID,tblitems.ItemID,tblitems.ItemName,tblitems.unit,
            (SELECT SUM(tblstockmaster.OQty) AS OQty FROM tblstockmaster WHERE tblstockmaster.ItemID=tblitems.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty
         FROM `tblitems` 
        WHERE tblitems.PlantID = '.$selected_company.'   
         AND tblitems.subgroup_id IN('.$item_group.') ';
       
        }
        
        $sql .= ' ORDER BY tblitems.subgroup_id ASC';
        $result = $this->db->query($sql)->result_array();
        return $result;*/
    }
    // Get Root Company details
    public function get_company_detail()
    {  
        $selected_company = $this->session->userdata('root_company');
        $sql ='SELECT '.db_prefix().'rootcompany.*
        FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
        $result = $this->db->query($sql)->row();
        return $result;
    }
    
    public function get_ledger_detail()
     {  
        
        $this->db->select('SUM(' .db_prefix() .'accountledger.Amount) as AMT');
        $this->db->where(db_prefix() .'accountledger.PassedFrom', "RECEIPTS");
        $this->db->where(db_prefix() .'accountledger.TType', "C");
        $result = $this->db->get(db_prefix() . 'accountledger')->row() ;
        return $result;
     }
    public function get_ledger_details()
     {  
        
        $this->db->select('SUM(' .db_prefix() .'accountledger.Amount) as AMTS');
        $this->db->where(db_prefix() .'accountledger.PassedFrom', "PAYMENTS");
        $this->db->where(db_prefix() .'accountledger.TType', "C");
        $result = $this->db->get(db_prefix() . 'accountledger')->row() ;
        return $result;
     }
    public function get_taxc_details()
     {  
         $this->db->select('SUM(Amount) as AMTSC');
    $this->db->from('tblaccountledger');
    $this->db->where_in('AccountID', array("CGST", "SGST", "IGST"));
    $this->db->where('TType', 'C');
    $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->AMTSC;
        } else {     
            return 0; 
        }
            $result = $this->db->get( db_prefix() . 'tblaccountledger')->row() ;
            return $result;
    }
    public function get_taxd_details()
     {  
        $this->db->select('SUM(Amount) as AMTSD');
        $this->db->from('tblaccountledger');
        $this->db->where_in('AccountID', array("CGST", "SGST", "IGST"));
        $this->db->where('TType', 'D');
        $query = $this->db->get();
    
            if ($query->num_rows() > 0) {
                return $query->row()->AMTSD;
            } else {     
                return 0; 
            }
                $result = $this->db->get( db_prefix() . 'tblaccountledger')->row() ;
                return $result;
     }
    
    public function get_item_open_qty($filterdata,$item_group)
    {
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $GodownID = $filterdata["GodownID"]; 
        $PartyID = $filterdata["PartyID"]; 
        $Service_type = $filterdata["Service_type"]; 
        $CenterID = $filterdata["CenterID"]; 
        $item_group_array = explode(",",$item_group);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select('tblstockmaster.ItemID,tblstockmaster.PartyID,tblstockmaster.CenterID,tblstockmaster.GodownID,SUM(tblstockmaster.OQty) AS OQtySum');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID=' . db_prefix() . 'stockmaster.ItemID');
        $this->db->where_in('tblitems.subgroup_id', $item_group_array);
        if($CenterID){
            $this->db->where_in('tblstockmaster.CenterID', $CenterID);
        }
        if($GodownID){
            $this->db->where_in('tblstockmaster.GodownID', $GodownID);
        }
        if($PartyID){
            $this->db->where_in('tblstockmaster.PartyID', $PartyID);
        }
        if($Service_type){
            $this->db->where('tblstockmaster.TypeID', $Service_type);
        }
        $this->db->where('tblstockmaster.PlantID', $selected_company);
        $this->db->where('tblstockmaster.FY', $fy);
        $this->db->group_by('tblstockmaster.ItemID,tblstockmaster.PartyID,tblstockmaster.CenterID,tblstockmaster.GodownID');
        $OpnQtyItemList = $this->db->get(db_prefix() . 'stockmaster')->result_array();
        return $OpnQtyItemList;
        /*if($from_date == "2022-04-01"){
            $day_before = '2022-04-01';
        }else{
            $day_before = date( 'Y-m-d', strtotime( $from_date . ' -1 day' ) );
        }
        $first_date = '2022-04-01';
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
         if($GodownID !==''){
        $sql = 'SELECT tblhistory.TType,tblhistory.TType2,tblhistory.ItemID,tblstockmaster.OQty,
         SUM(tblhistory.BilledQty)as billsum FROM `tblhistory` 
        INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
        LEFT JOIN tblstockmaster ON tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.cnfid = "1"
        WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$first_date.' 00:00:00" AND "'.$day_before.' 23:59:59" AND tblitems.subgroup_id IN('.$item_group.') AND tblhistory.BillID IS NOT NULL ';
        if($GodownID !==''){
            $sql .= ' AND tblhistory.GodownID = "'.$GodownID.'" AND tblstockmaster.GodownID = "'.$GodownID.'"';
        }
        
        }else{
            $sql = 'SELECT tblhistory.TType,tblhistory.TType2,tblhistory.ItemID,
            (SELECT SUM(tblstockmaster.OQty) FROM tblstockmaster WHERE tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty,
         SUM(tblhistory.BilledQty)as billsum FROM `tblhistory` 
        INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
        WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$first_date.' 00:00:00" AND "'.$day_before.' 23:59:59" AND tblitems.subgroup_id IN('.$item_group.') AND tblhistory.BillID IS NOT NULL';
           
        }
        $sql .= ' GROUP BY tblhistory.ItemID,tblhistory.TType,tblhistory.TType2 ';
        $result = $this->db->query($sql)->result_array();
        return $result;*/
    }
    
    public function GetPreStockData($filterdata,$item_group,$day_before)
    {
        $from_date = '20'.$fy.'-04-01 00:00:00';
        $to_date = $day_before.' 23:59:59'; 
        $GodownID = $filterdata["GodownID"]; 
        $PartyID = $filterdata["PartyID"];
        $Service_type = $filterdata["Service_type"];
        $CenterID = $filterdata["CenterID"]; 
        $item_group_array = explode(",",$item_group);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select('tblhistory.CenterID,tblhistory.GodownID,tblhistory.PartyID,tblhistory.TType,tblhistory.TType2,tblhistory.TypeID,
        tblhistory.ItemID,SUM(tblhistory.BilledQty) AS TotalQty');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID =' . db_prefix() . 'history.ItemID');
        $this->db->where_in('tblitems.subgroup_id', $item_group_array);
        if($CenterID){
            $this->db->where_in('tblhistory.CenterID', $CenterID);
        }
        if($GodownID){
            $this->db->where_in('tblhistory.GodownID', $GodownID);
        }
        if($PartyID){
            $this->db->where_in('tblhistory.PartyID', $PartyID);
        }
        if($Service_type){
            $this->db->where('tblhistory.TypeID', $Service_type);
        }
        $this->db->where('tblhistory.PlantID', $selected_company);
        $this->db->where('tblhistory.FY', $fy);
        $this->db->where('tblhistory.OrderID IS NOT NULL');
        $this->db->where('tblhistory.BillID IS NOT NULL');
        $this->db->where('tblhistory.TransID IS NOT NULL');
        $this->db->where('tblhistory.TransDate2 BETWEEN "'. $from_date. '" AND "'. $to_date. '" ');
        $this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CenterID,tblhistory.GodownID');
        $PreStockItemList = $this->db->get(db_prefix() . 'history')->result_array();
        return $PreStockItemList;
    }
    
    public function GetStockData($filterdata,$item_group)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
        $to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
        $item_group_array = explode(",",$item_group);
        $GodownID = $filterdata["GodownID"]; 
        $PartyID = $filterdata["PartyID"]; 
        $Service_type = $filterdata["Service_type"]; 
        $CenterID = $filterdata["CenterID"]; 
        
        $this->db->select('tblhistory.CenterID,tblhistory.GodownID,tblhistory.PartyID,tblhistory.TType,tblhistory.TType2,tblhistory.TypeID,
        tblhistory.ItemID,SUM(tblhistory.BilledQty) AS TotalQty');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID =' . db_prefix() . 'history.ItemID');
        $this->db->where_in('tblitems.subgroup_id', $item_group_array);
        if($CenterID){
            $this->db->where_in('tblhistory.CenterID', $CenterID);
        }
        if($GodownID){
            $this->db->where_in('tblhistory.GodownID', $GodownID);
        }
        if($PartyID){
            $this->db->where_in('tblhistory.PartyID', $PartyID);
        }
        if($Service_type){
            $this->db->where('tblhistory.TypeID', $Service_type);
        }
        $this->db->where('tblhistory.PlantID', $selected_company);
        $this->db->where('tblhistory.FY', $fy);
        $this->db->where('tblhistory.OrderID IS NOT NULL');
        $this->db->where('tblhistory.BillID IS NOT NULL');
        $this->db->where('tblhistory.TransID IS NOT NULL');
        $this->db->where('tblhistory.TransDate2 BETWEEN "'. $from_date. '" AND "'. $to_date. '" ');
        $this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CenterID,tblhistory.GodownID');
        $StockItemList = $this->db->get(db_prefix() . 'history')->result_array();
        return $StockItemList;
        
        
    }
	
	public function GetStockData_Chart($filterdata,$item_group)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
        $to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
        $item_group_array = explode(",",$item_group);
        $GodownID = $filterdata["GodownID"]; 
        $PartyID = $filterdata["PartyID"]; 
        $Service_type = $filterdata["Service_type"]; 
        $CenterID = $filterdata["CenterID"]; 
        
		
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
		
        $this->db->select('Count(tblhistory.id) AS TotalCount, tblhistory.CenterID,tblhistory.GodownID,tblhistory.PartyID,tblhistory.TType,tblhistory.TType2,tblhistory.TypeID,
        tblhistory.ItemID,SUM(tblhistory.BilledQty) AS TotalQty,tblitems.ItemName as NameItems');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID =' . db_prefix() . 'history.ItemID');
        $this->db->where_in('tblitems.subgroup_id', $item_group_array);
        // if($CenterID){
            // $this->db->where_in('tblhistory.CenterID', $CenterID);
        // }
        // if($GodownID){
            // $this->db->where_in('tblhistory.GodownID', $GodownID);
        // }
        // if($PartyID){
            // $this->db->where_in('tblhistory.PartyID', $PartyID);
        // }
        // if($Service_type){
            // $this->db->where('tblhistory.TypeID', $Service_type);
        // }
        $this->db->where('tblhistory.PlantID', $selected_company);
        $this->db->where('tblhistory.FY', $fy);
        $this->db->where('tblhistory.OrderID IS NOT NULL');
        $this->db->where('tblhistory.BillID IS NOT NULL');
        $this->db->where('tblhistory.TransID IS NOT NULL');
        $this->db->where('tblhistory.TransDate2 BETWEEN "'. $from_date. '" AND "'. $to_date. '" ');
        $this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CenterID,tblhistory.GodownID');
		// $this->db->group_by("tblvillagedetails.UserID");
        $StockItemList = $this->db->get(db_prefix() . 'history')->result_array();
		// echo"<pre>";
		// print_r($StockItemList);
		// die;
		
		
			$i = 0;
			// foreach ($StockItemList as $row) {
				// echo "ItemID: " . $row['ItemID'] . "<br>";
				// echo "TotalQty: " . $row['TotalQty'] . "<br>";
				// echo "TotalCount: " . $row['TotalCount'] . "<br>";
				// echo "GodownID: " . $row['GodownID'] . "<br>";
				// echo "CenterID: " . $row['CenterID'] . "<br><hr>";
			// $name = isset($row['ItemName']) ? $row['ItemName'] : 'Unknown';
			
			
			
			 foreach ($StockItemList as $key => $value) {
				// // Determine name based on ReportFor
				// if ($ReportFor == "1"  && empty($Staff_Id)) {
				
					$name = isset($value['NameItems']) ? $value['NameItems'] : 'Unknown';
					 $allcount = isset($value['TotalQty']) ? (int)$value['TotalQty'] : 0;
					// } elseif ($ReportFor == "2"  && empty($Staff_Id)) {
					// $name = isset($value['assignee_firstname']) ? $value['assignee_firstname'] . " " . $value['assignee_lastname'] : 'Unknown';
					// }else {
					// $name = 'Unknown';
				// }
				// if (!empty($Staff_Id)) {
					// if (!empty($District) && empty($Taluka)) {
						// $name = isset($value['TalukaName']) ? $value['TalukaName'] : 'Unknown';
						// }elseif(!empty($Taluka)){
						// $name = isset($value['VillageName']) ? $value['VillageName'] : 'Unknown';
						// }else {
						// $name = isset($value['city_name']) ? $value['city_name'] : 'Unknown';
					// }
				// }
				// if ($ChartType !== "Pie") {
					// $allcount = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
					// } else {
					// $count3_raw = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
					// $count = ($totalCount > 0) ? round(($count3_raw / $totalCount) * 100, 2) : 0;
					// $allcount = $count; // Keep decimal part
				// }
		
		// Now build the chart array
				$chart[] = array(
				'name'  => $name,
				'y'     => $allcount,
				'color' => $color_data[$i % count($color_data)],
				'z'     => 100,
				'label' => "Qty"
				);
				
				
				$i++;
			}
			$data = [
			'ChartData' => $chart,
			];
			// Step 7: Return chart data
			return $data;
        return $StockItemList;
        
        
    }
	
	
	
	
     
    /* Start Code for  Stock position reports */
    
//=========================== Verify code end ==================================
    public function get_item_group_name($item_group)
    {
        $item_group_array = explode(",",$item_group);
        $this->db->select('name');
        $this->db->where_in('id', $item_group_array);
        $item_group_names = $this->db->get(db_prefix() . 'items_sub_groups')->result_array();
        $item_group_name = array();
        foreach ($item_group_names as $key => $value) {
            array_push($item_group_name, $value["name"]);
        }
        $item_group_name_s = implode(", ", $item_group_name);
        return $item_group_name_s;
    }
    /* Get item group */
    public function get_item_group($id = '')
    {
       
        $this->db->select('*');
        if($id !== '0'){
            $this->db->where('main_group_id',$id);
        }
        $this->db->order_by('name','ASC');
        $this->db->from(db_prefix() . 'items_sub_groups');
        return $this->db->get()->result_array();
    }
   
   /* New Module*/
    public function get_report_data($data){

        $from_date = to_sql_date($data['from_date']).' '.date('00:00:00');
        $to_date = to_sql_date($data['to_date']).' '.date('23:59:59');
        

        $this->db->order_by(db_prefix() . 'RateMaster.id', 'desc');
        $this->db->order_by(db_prefix() . 'RateMaster.IsActive', 'desc');



        if(isset($data['center'])) {
            $centerIDArray = $data['center'];
            foreach($centerIDArray as $value){
                $this->db->or_where(db_prefix() . 'RateMaster.CenterID', $value);
            }
        }
        
         $this->db->where(db_prefix() . 'RateMaster.TransDate >=', $from_date); 
         $this->db->where(db_prefix() . 'RateMaster.TransDate <=', $to_date);
        

         $this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID=' . db_prefix() . 'RateMaster.CenterID', 'left');
         $this->db->select(db_prefix() . 'RateMaster.*,' . db_prefix() . 'CenterMaster.CenterName');
        
         $this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID=' . db_prefix() . 'RateMaster.ItemID', 'left');
         $this->db->select(db_prefix() . 'RateMaster.*,' . db_prefix() . 'items.ItemName');
         
         
         
         if (isset($data['rate'])) {
         $reportType = $data['rate'];

         if ($reportType == 'Kirti purchase farmer') {
           $this->db->select(db_prefix() . 'RateMaster.*,' . db_prefix() . 'items.ItemName');
            $this->db->where(db_prefix() . 'RateMaster.Type', 'F');
         } else if ($reportType == 'Kirti purchase trader') {
            $this->db->select(db_prefix() . 'RateMaster.*,' . db_prefix() . 'items.ItemName');
            $this->db->where(db_prefix() . 'RateMaster.Type', 'T');
         } elseif ($reportType == 'Competitor rate') {
            $this->db->select(db_prefix() . 'RateMaster.*,' . db_prefix() . 'items.ItemName');
            $this->db->where(db_prefix() . 'RateMaster.Type', 'C');
         }
         else {
            $this->db->join(db_prefix() . 'SalerateMaster', db_prefix() . ' SalerateMaster.CenterID = ' . db_prefix() . 'RateMaster.CenterID', 'left');
           // $this->db->select(db_prefix() . 'SalerateMaster.*,' . db_prefix() . 'items.ItemName');

         } 
         
        

      }
        
        return $this->db->get(db_prefix() . 'RateMaster')->result_array();
       
    }
    
    public function getAllMandItemname(){
        return $this->db->get(db_prefix() .'items')->result_array();
    }
    
    
    
    public function getAllMandiDb(){
        return $this->db->get(db_prefix() .'CenterMaster')->result_array();
    }
   
   
    public function get_sale_item_group2($data)
     {  
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $main_item_group_id = $data["main_item_group_id"];
         
         
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
      
        $sql1 = '(Transdate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")';
        
        $sql1 .= ' AND PlantID = "'.$selected_company.'" AND FY = "'.$fy.'"';
        //$sql1 .= ' AND PlantID = "'.$selected_company.'" AND FY = "'.$fy.'"';
        
        $sql ='SELECT '.db_prefix().'history.* FROM '.db_prefix().'history WHERE '.$sql1;
        
        $result = $this->db->query($sql)->result_array();
        if(empty($result)){
            return $result;
        }
        
        $order_ids = array();
        $item_ids = array();
        foreach ($result as $key => $value) {
               # code...
               array_push($item_ids, $value["ItemID"]);
            }
        
        if(empty($item_ids)){
            
        }else{
        
        
        $item_ids_uniqu = array_unique($item_ids);
        
        $this->db->select('*');
      $this->db->from(db_prefix() . 'items');
      $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
      //$this->db->where(db_prefix() . 'items.isactive', "Y");
      $this->db->where_in('ItemID',$item_ids_uniqu);
      $result3 = $this->db->get()->result_array();
        
        $item_group_ids = array();
        foreach ($result3 as $key3 => $value3) {
               # code...
               array_push($item_group_ids, $value3["subgroup_id"]);
            }
        $item_group_ids_uniqu = array_unique($item_group_ids);
        
        $this->db->select('*');
      $this->db->from(db_prefix() . 'items_sub_groups');
      $this->db->where(db_prefix() . 'items_sub_groups.main_group_id', $main_item_group_id);
      
      $this->db->where_in('id',$item_group_ids_uniqu);
      $result4 = $this->db->get()->result_array();
        
        return $result4;
        
        }
     }
    
    /* Get Main item group */
    public function get_mainitem_group($id = '')
    {
       
        $this->db->select('*');
        $this->db->where('id',$id);
        $this->db->from(db_prefix() . 'items_main_groups');
        return $this->db->get()->row();
    }
    
     
    public function GetItemListCommulative($filterdata,$item_group)
     {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $sql = 'SELECT tblitems.PlantID,tblitems.ItemID,tblitems.ItemName,tblitems.unit
        FROM `tblitems` 
        WHERE tblitems.PlantID = '.$selected_company.' AND tblitems.subgroup_id IN('.$item_group.')';
        $sql .= ' ORDER BY tblitems.subgroup_id ASC';
        $result = $this->db->query($sql)->result_array();
        return $result;
     }
    
    public function getCommulativeStockData($filterdata,$item_group)
     {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $to_date = to_sql_date($filterdata["to_date"]);
        $from_date = '20'.$fy.'-04-01';
        
        $sql = 'SELECT tblhistory.TType,tblhistory.TType2,tblhistory.ItemID,tblhistory.GodownID,tblstockmaster.OQty,
         SUM(tblhistory.BilledQty)as billsum FROM `tblhistory` 
        INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
        INNER JOIN tblstockmaster ON tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.GodownID = tblhistory.GodownID
        WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblitems.subgroup_id IN('.$item_group.') AND tblhistory.BillID IS NOT NULL ';
        
        $sql .= ' GROUP BY tblhistory.GodownID,tblhistory.ItemID,tblhistory.TType,tblhistory.TType2 ';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    
    
    public function get_stock_itemlist($filterdata,$item_group)
     {
        /*$from_date = to_sql_date($filterdata["from_date"]);*/
        $to_date = to_sql_date($filterdata["to_date"]); 
        $from_date = "2021-04-01";
        //$to_date = date('Y-m-d'); 
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        if($selected_company == "1"){
            $CustType = '1';
        }else if($selected_company == "2"){
            $CustType = '13';
        }else if($selected_company == "3"){
            $CustType = '21';
        }
       
        $sql = 'SELECT tblitems.*,tblrate_master.assigned_rate,tblstockmaster.OQty,( SELECT SaleRate FROM tblhistory WHERE tblhistory.ItemID=tblitems.ItemID AND tblhistory.PlantID = tblitems.PlantID AND tblhistory.TType = "P" AND tblhistory.TType2 = "PURCHASE" AND tblhistory.FY = "'.$fy.'" ORDER BY id DESC LIMIT 1) AS rate FROM `tblitems` 
        LEFT JOIN tblrate_master ON tblrate_master.item_id=tblitems.ItemID AND tblrate_master.PlantID = tblitems.PlantID AND tblrate_master.state_id = "UP" AND tblrate_master.distributor_id= "'.$CustType.'"
        LEFT JOIN tblstockmaster ON tblstockmaster.ItemID=tblitems.ItemID AND tblstockmaster.PlantID = tblitems.PlantID AND tblstockmaster.FY = "'.$fy.'"
        WHERE tblitems.PlantID = '.$selected_company.'  AND tblitems.subgroup_id IN('.$item_group.')';
        $sql .= 'ORDER BY tblitems.subgroup_id ASC';
        
        $result = $this->db->query($sql)->result_array();
        return $result;
         
     }
     
    public function get_stock_itemdetails_for_body_data($item_group,$filterdata)
     { 
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $report_type = $filterdata["report_type"];
        
        $sql = 'SELECT SUM(tblhistory.BilledQty) as qty_sum,tblhistory.ItemID,tblhistory.TType,tblhistory.TType2  FROM `tblhistory` 
        INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID
        WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblitems.subgroup_id IN('.$item_group.') AND tblhistory.BillID IS NOT NULL';
        
        $sql .= ' GROUP BY tblhistory.ItemID,tblhistory.TType,tblhistory.TType2';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function get_stock_itemlist_new($filterdata,$item_group)
     {
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
   
$sql = 'SELECT tblhistory.OrderID, tblhistory.ItemID,tblitems.ItemName,tblhistory.AccountID,tblclients.StationName FROM `tblhistory`

INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID';
if($states || $client_type){
     $sql .= ' INNER JOIN tblclients ON tblclients.AccountID=tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID';
}else{
    $sql .= ' INNER JOIN tblclients ON tblclients.AccountID=tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID';
}
if($loc_type){
     $sql .= ' INNER JOIN tblaccountlocations ON tblhistory.AccountID=tblaccountlocations.AccountID AND tblhistory.PlantID = tblaccountlocations.PlantID';
}
if($staff_id){
    $sql .= ' INNER JOIN tblcustomer_admins ON tblcustomer_admins.customer_id=tblclients.AccountID AND tblcustomer_admins.company_id = tblclients.PlantID';
}
$sql .= ' WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblitems.subgroup_id IN('.$item_group.') AND tblhistory.BillID IS NOT NULL AND tblhistory.NetChallanAmt !=0.00';

if($loc_type == "3"){
    $sql .= ' AND tblaccountlocations.LocationTypeID IN(1,2,3)';
}else{
    $sql .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type;
}
if($states){
    $sql .= ' AND tblclients.state ="'.$states.'"';
}
if($staff_id){
    $sql .= ' AND tblcustomer_admins.staff_id IN('.$staff_ids_uniqu_s.')';
}
if($client_type){
    $sql .= ' AND tblclients.DistributorType ="'.$client_type.'"';
}

if($report_type == "freshrtn"){
            $sql .= ' AND tblhistory.TType ="R" AND TType2="Fresh"';
        }elseif($report_type == "damage"){
            $sql .= ' AND tblhistory.TType ="R" AND TType2="Damage"';
        }elseif($report_type == "netsales"){
            //$sql .= ' AND tblhistory.TType ="O" AND TType2="Order"';
            
        }elseif($report_type == "sales"){
            $sql .= ' AND tblhistory.TType ="O" AND TType2="Order"';
        }else{
            
        }
 
$sql .= ' GROUP BY tblhistory.ItemID,tblhistory.AccountID ORDER BY tblclients.StationName ASC';

        $result = $this->db->query($sql)->result_array();
        return $result;
         
     }
    
    // Production Report code
    
    public function item_division_group(){
        
        $this->db->order_by('name', 'asc');
        return $this->db->get(db_prefix() . 'items_groups')->result_array();
    }
    
    function getaccounts($postData){

     $response = array();
    $selected_company = $this->session->userdata('root_company');
    $where_clients = '';
    $where_staff = '';
     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       
       $this->db->select(db_prefix() . 'clients.*');
       $where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR AccountID LIKE "%' . $q . '%" ESCAPE \'!\') ';
       $this->db->where($where_clients);
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
       $records = $this->db->get(db_prefix() . 'clients')->result();
       foreach($records as $row ){
          $response[] = array("label"=>$row->company,"value"=>$row->AccountID,"source"=>'con');
       }
       
       $q = $postData['search'];
       
       $this->db->select(db_prefix() . 'staff.*');
       $where_staff .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q. '%" ESCAPE \'!\' OR stationName LIKE "%' . $q . '%" ESCAPE \'!\') ';
       $this->db->where($where_staff);
       $this->db->where(db_prefix() . 'staff.SubActGroupID', '10022004');
       $records = $this->db->get(db_prefix() . 'staff')->result();
       foreach($records as $row ){
           $full_name = $row->firstname." ".$row->lastname;
          $response[] = array("label"=>$full_name,"value"=>$row->AccountID,"source"=>'staff');
       }

     }

     return $response;
  }
   function itemlist($postData){

     $response = array();
    $selected_company = $this->session->userdata('root_company');
    $where_items = '';
     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       
       $this->db->select(db_prefix() . 'items.*');
       $where_items .= '(   ItemID LIKE "%' . $q . '%" ESCAPE \'!\' OR ItemName LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'items.isactive = "Y" AND '.db_prefix() . 'items.subgroup_id NOT IN(9,20,36)';
       
       $this->db->where($where_items);
       
        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
        
       $records = $this->db->get(db_prefix() . 'items')->result();

       foreach($records as $row ){
          $response[] = array("label"=>$row->ItemName,"value"=>$row->ItemID);
       }

     }

     return $response;
  }
  
    public function get_account_details($AccountID)
     {  
        $selected_company = $this->session->userdata('root_company');
        $sql ='SELECT '.db_prefix().'clients.*
        FROM '.db_prefix().'clients WHERE AccountID = "'.$AccountID.'" AND PlantID = '.$selected_company;
        
        $result = $this->db->query($sql)->row();
        return $result;
        
     }
    public function get_staff_details($AccountID)
     {  
        
        $sql ='SELECT '.db_prefix().'staff.*
        FROM '.db_prefix().'staff WHERE AccountID = "'.$AccountID.'"';
        
        $result = $this->db->query($sql)->row();
        return $result;
        
     }
    public function get_item_details($ItemID)
     {  
        $selected_company = $this->session->userdata('root_company');
        $sql ='SELECT '.db_prefix().'items.*
        FROM '.db_prefix().'items WHERE ItemID = "'.$ItemID.'" AND PlantID = '.$selected_company;
        
        $result = $this->db->query($sql)->row();
        return $result;
        
     }
  public function get_production_for_body_data($filterdata)
     { 

        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]);
        $item_division = $filterdata["item_division"];
        $accountID = $filterdata["accountID"];
        $report_type = $filterdata["report_type"];
        $ItemID = $filterdata["ItemID"];
        $source = $filterdata["source"];
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        if($report_type == 1 && empty($ItemID) && empty($accountID)){
            $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'clients.*,tblitems.ItemName,tblstaff.firstname, tblrecipe.qty FROM '.db_prefix() . 'production 
            LEFT JOIN tblclients ON tblclients.AccountID=tblproduction.contractor_name AND tblclients.PlantID = tblproduction.PlantID 
            LEFT JOIN tblstaff ON tblstaff.AccountID=tblproduction.manager_name 
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            INNER JOIN tblitems ON tblitems.ItemID=tblproduction.recipeID AND tblitems.PlantID = tblproduction.PlantID 
            WHERE '.db_prefix() . 'production.FY = '.$fy.'  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ORDER BY tblclients.AccountID';
            
        }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
            $sql = ' SELECT SUM('.db_prefix() . 'production.Finish_good_qty_new) as fgqty,'.db_prefix() . 'clients.*,tblstaff.firstname,tblstaff.AccountID AS AccountID_staff,tblclients.AccountID AS AccountID_con FROM '.db_prefix() . 'production 
            LEFT JOIN tblclients ON tblclients.AccountID=tblproduction.contractor_name AND tblclients.PlantID = tblproduction.PlantID 
            LEFT JOIN tblstaff ON tblstaff.AccountID=tblproduction.manager_name 
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.production_status = "Completed" AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
            GROUP BY tblproduction.manager_name ORDER BY tblclients.AccountID';
        }else if(!empty($ItemID) && empty($accountID)){
            $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'clients.*,tblstaff.firstname, tblrecipe.qty FROM '.db_prefix() . 'production 
            LEFT JOIN tblclients ON tblclients.AccountID=tblproduction.contractor_name AND tblclients.PlantID = tblproduction.PlantID 
            LEFT JOIN tblstaff ON tblstaff.AccountID=tblproduction.manager_name
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND  tblproduction.recipeID = "'.$ItemID.'"  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
             ORDER BY tblclients.AccountID';
        }else if(empty($ItemID) && !empty($accountID)){
            if($source == "con"){
                $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'clients.*,tblitems.ItemName, tblrecipe.qty FROM '.db_prefix() . 'production 
            INNER JOIN tblclients ON tblclients.AccountID=tblproduction.contractor_name AND tblclients.PlantID = tblproduction.PlantID 
            INNER JOIN tblitems ON tblitems.ItemID=tblproduction.recipeID AND tblitems.PlantID = tblproduction.PlantID 
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND  tblproduction.contractor_name = "'.$accountID.'"  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
             ORDER BY tblclients.AccountID';
            }else{
                $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'staff.*,tblitems.ItemName, tblrecipe.qty FROM '.db_prefix() . 'production 
            INNER JOIN tblstaff ON tblstaff.AccountID=tblproduction.manager_name 
            INNER JOIN tblitems ON tblitems.ItemID=tblproduction.recipeID AND tblitems.PlantID = tblproduction.PlantID 
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND  tblproduction.manager_name = "'.$accountID.'"  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
             ORDER BY tblstaff.AccountID';
            }
            
        }else if(!empty($ItemID) && !empty($accountID)){
            if($source == "con"){
                $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'clients.*,tblitems.ItemName, tblrecipe.qty FROM '.db_prefix() . 'production 
            INNER JOIN tblclients ON tblclients.AccountID=tblproduction.contractor_name AND tblclients.PlantID = tblproduction.PlantID 
            INNER JOIN tblitems ON tblitems.ItemID=tblproduction.recipeID AND tblitems.PlantID = tblproduction.PlantID 
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND  tblproduction.recipeID = "'.$ItemID.'" AND  tblproduction.contractor_name = "'.$accountID.'"  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
             ORDER BY tblclients.AccountID';
            }else{
                $sql = ' SELECT '.db_prefix() . 'production.*,'.db_prefix() . 'staff.*,tblitems.ItemName, tblrecipe.qty FROM '.db_prefix() . 'production 
            INNER JOIN tblstaff ON tblstaff.AccountID=tblproduction.manager_name 
            INNER JOIN tblitems ON tblitems.ItemID=tblproduction.recipeID AND tblitems.PlantID = tblproduction.PlantID 
            LEFT JOIN tblrecipe ON tblrecipe.item_code=tblproduction.recipeID AND tblrecipe.status="Y" AND tblrecipe.PlantID = tblproduction.PlantID AND tblrecipe.FY='.$fy.'
            WHERE '.db_prefix() . 'production.FY = '.$fy.' AND  tblproduction.recipeID = "'.$ItemID.'" AND  tblproduction.manager_name = "'.$accountID.'"  AND tblproduction.production_status = "Completed" AND tblproduction.PlantID = '.$selected_company.' AND tblproduction.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
             ORDER BY tblstaff.AccountID';
            }
            
        }
        
       $result = $this->db->query($sql)->result_array();
        return $result;
        
     }
      public function item_division_group_data(){
        
        // $this->db->order_by('name', 'asc');
        return $this->db->get(db_prefix() . 'items_groups')->result_array();
    }
    
    // END production reports code
    public function get_rate_table_data($data)
     {  
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        $item_group = explode(",",$data['item_group']);
        $item_type = $data['item_data'];
        $this->db->select(db_prefix() .'rate_master.item_id,'.db_prefix() .'rate_master.assigned_rate,'.db_prefix() .'rate_master.effective_date,'.db_prefix() . 'items.ItemName,'.db_prefix() .'taxes.taxrate');
        $this->db->from(db_prefix() .'rate_master');
        if($data['item_data'] == '1'){
            $this->db->join(db_prefix() .'items', db_prefix() .'items.ItemID = '.db_prefix() .'rate_master.item_id AND '.db_prefix() .'items.PlantID = '.db_prefix() .'rate_master.PlantID AND '.db_prefix() .'items.isactive = "Y"');
        }else{
            $this->db->join(db_prefix() .'items', db_prefix() .'items.ItemID = '.db_prefix() .'rate_master.item_id AND '.db_prefix() .'items.PlantID = '.db_prefix() .'rate_master.PlantID');
        }
        
        $this->db->join(db_prefix() .'taxes', db_prefix() .'taxes.id = '.db_prefix() .'items.tax ');
        $this->db->where(db_prefix() .'rate_master.PlantID', $selected_company);
        
        if($data['states'] !=''){
            $this->db->where(db_prefix() .'rate_master.state_id', $data['states']);
        }
        if($data['distributor_id'] !=''){
            $this->db->where(db_prefix() .'rate_master.distributor_id', $data['distributor_id']);
        }
        if($data['item_group'] !=''){
            $this->db->where_in(db_prefix() .'items.subgroup_id', $item_group);
        }
        $this->db->order_by(db_prefix() .'items.subgroup_id', 'ASC');
        return $this->db->get()->result_array();/*
        echo $this->db->last_query();die;*/
        
     }
     
    // start target entry andtarget Vs acivements
     public function get_salesstaff(){
        $selected_company = $this->session->userdata('root_company');
         $data_a = array('30001002');
         $this->db->select('firstname,lastname,staffid,AccountID');
         $this->db->from('tblstaff');
         $this->db->where_in('SubActGroupID',$data_a);
         $this->db->where('active',1);
         $this->db->where('PlantID',$selected_company);
         $this->db->order_by('firstname','ASC');
      return  $data = $this->db->get()->result_array();
         
     }
     public function get_salesstaff2(){
         $selected_company = $this->session->userdata('root_company');
         $data_a = array('30001002');
         $this->db->select('firstname,lastname,staffid,AccountID');
         $this->db->from('tblstaff');
         $this->db->where_in('SubActGroupID',$data_a);
         $this->db->where('PlantID',$selected_company);
         $this->db->order_by('firstname','ASC');
      return  $data = $this->db->get()->result_array();
         
     }
     public function get_targetList($data){
         
          $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
      
        $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StationName,'.db_prefix() . 'customers_groups.name');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
        $this->db->join(db_prefix() . 'customers_groups', db_prefix() . 'customers_groups.id = ' . db_prefix() . 'clients.DistributorType', 'left');
        // $this->db->join(db_prefix() . 'accountitemdiv', db_prefix() . 'accountitemdiv.AccountID = ' . db_prefix() . 'clients.AccountID', 'left');
       
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
       if($data['staff_d'] != ''){
            $this->db->where(db_prefix() . 'customer_admins.staff_id', $data['staff_d']);
       }
    //   $this->db->where(db_prefix() . 'customer_admins.staff_id', $data['staff_d']);
       $this->db->order_by(db_prefix() . 'clients.company');
       return $this->db->get()->result_array();
    //   echo $this->db->last_query();
     }
      public function get_coutomer_division($data){
         
          $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
     $month = substr($data['month_data'], -2);
     
      $this->db->select(db_prefix() . 'staff_target.Targate,'.db_prefix() . 'clients.AccountID,'.db_prefix() . 'customers_groups.name,'.db_prefix() . 'accountitemdiv.ItemDivID');
 
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'customer_admins.staff_id', 'left');
        $this->db->join(db_prefix() . 'customers_groups', db_prefix() . 'customers_groups.id = ' . db_prefix() . 'clients.DistributorType', 'left');
        $this->db->join(db_prefix() . 'accountitemdiv', db_prefix() . 'accountitemdiv.AccountID = ' . db_prefix() . 'clients.AccountID AND  '.db_prefix() . 'accountitemdiv.PlantID = '.$selected_company, 'left');
        $this->db->join(db_prefix() . 'staff_target', db_prefix() . 'staff_target.Staff_AccountID = ' . db_prefix() . 'staff.AccountID AND '.db_prefix() . 'staff_target.ItemDivID = ' . db_prefix() . 'accountitemdiv.ItemDivID AND '.db_prefix() . 'staff_target.AccountID = ' . db_prefix() . 'clients.AccountID AND '.db_prefix() . 'staff_target.MonthID = ' . $month, 'left');
       
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        if($data['staff_d'] != ''){
            $this->db->where(db_prefix() . 'customer_admins.staff_id', $data['staff_d']);
       }
       return $this->db->get()->result_array();
        echo $this->db->last_query();
     }
     public function get_staff_business_division($data){
         
          $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
     $month = substr($data['month_data'], -2); 
    
      $this->db->select(db_prefix() . 'new_business_target.*');
        $this->db->from(db_prefix() . 'new_business_target');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.AccountID = ' . db_prefix() . 'new_business_target.Staff_AccountID', 'left');
       
       $this->db->like(db_prefix() . 'new_business_target.FY', $year);
       $this->db->where(db_prefix() . 'new_business_target.MonthID', $month);
       $this->db->where(db_prefix() . 'new_business_target.PlantID', $selected_company);
       
           $this->db->where(db_prefix() . 'staff.staffid', $data['staff_d']);
    
       
       return $this->db->get()->result_array();
        echo $this->db->last_query();
     }
      public function sum_get_coutomer_division($data){ 
         
          $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
     $month = substr($data['month_data'], -2);
     $this->db->select_sum(db_prefix() . 'staff_target.Targate');
      $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'accountitemdiv.ItemDivID');
 
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'customer_admins.staff_id', 'left');
        $this->db->join(db_prefix() . 'accountitemdiv', db_prefix() . 'accountitemdiv.AccountID = ' . db_prefix() . 'clients.AccountID AND  '.db_prefix() . 'accountitemdiv.PlantID = '.$selected_company, 'left');
        $this->db->join(db_prefix() . 'staff_target', db_prefix() . 'staff_target.Staff_AccountID = ' . db_prefix() . 'staff.AccountID AND '.db_prefix() . 'staff_target.ItemDivID = ' . db_prefix() . 'accountitemdiv.ItemDivID AND '.db_prefix() . 'staff_target.AccountID = ' . db_prefix() . 'clients.AccountID AND '.db_prefix() . 'staff_target.MonthID = ' . $month, 'left');
       
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        if($data['staff_d'] != ''){
            $this->db->where(db_prefix() . 'customer_admins.staff_id', $data['staff_d']);
       }
       $this->db->group_by(db_prefix() . 'accountitemdiv.ItemDivID');
        return $this->db->get()->result_array();
        // echo $this->db->last_query();die;
     }
     
      public function sum_get_achievement_division($data){
         
          $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
     $month = substr($data['month_data'], -2);
     /*$start_date = '2022-'.$month.'-01';
     $end_date = '2022-'.$month.'-31';*/
     
     if ( $month <= 03 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
     $start_date = '20'.$FY.'-'.$month.'-01';
    // Converting string to date
    $date = strtotime($start_date);
    // Last date of current month.
    $end_date = date("Y-m-t", $date );
    /* echo $start_date;
     echo "<br>";
     echo $end_date;
     die;*/

         $this->db->select_sum(db_prefix() . 'history.NetChallanAmt');
     $this->db->select(db_prefix() . 'history.AccountID,'.db_prefix() . 'staff_target.ItemDivID');
    $this->db->from(db_prefix() . 'staff_target');
    
   $this->db->join(db_prefix() . 'items', db_prefix() . 'items.group_id = ' . db_prefix() . 'staff_target.ItemDivID AND '. db_prefix() . 'items.PlantID = '.$selected_company , 'left');
    $this->db->join(db_prefix() . 'history', db_prefix() . 'history.AccountID = ' . db_prefix() . 'staff_target.AccountID AND '.db_prefix() . 'history.ItemID = ' . db_prefix() . 'items.ItemID AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND '.db_prefix() . 'history.FY = '.$year, 'left');
    $this->db->like(db_prefix() . 'staff_target.AccountID', $data['accountId']);
    $this->db->like(db_prefix() . 'staff_target.MonthID', $month);
    $this->db->where(db_prefix() . 'staff_target.PlantID', $selected_company);
    $this->db->like(db_prefix() . 'staff_target.FY', $year);
      $this->db->like(db_prefix() . 'history.TType', 'O');
      $this->db->like(db_prefix() . 'history.TType2', 'Order');
      $this->db->where(db_prefix() . 'history.OrderID !=', NULL);
      $this->db->where(db_prefix() . 'history.TransID !=', NULL);
        $this->db->where(db_prefix() . 'history.TransDate2 >=', $start_date.' 00:00:00');
        $this->db->where(db_prefix() . 'history.TransDate2 <=',$end_date.' 23:59:59');
         $this->db->group_by(db_prefix() . 'staff_target.ItemDivID');
       return $this->db->get()->result_array();
        //  echo $this->db->last_query();die;
     }
      public function create_targetSale($data){
       
          $selected_company = $this->session->userdata('root_company');
          $username = $this->session->userdata('username');
      $year = $_SESSION['finacial_year'];
      $month = substr($data['month_data'], -2);
      $staff_d = $this->db->get_where('tblstaff',array('staffid'=>$data['Staff_AccountID']))->row_array();
    
        $i = 0; foreach($data['AccountID'] as $key=>$AccountID){
           $item_id = $AccountID.'_item_id';
           $target = $AccountID.'_target';
          $j = 0;   
        foreach($data[$item_id] as $key=>$ItemDivID){
            
            if($AccountID == 'New_Business'){
                 $data_array = array(
                 'PlantID' => $selected_company,
                 'FY' => $year,
                 'Staff_AccountID' => $staff_d['AccountID'],
                 'MonthID' => $month,
                 'ItemDivID' => $ItemDivID,
                 'Targate' => $data[$target][$j],
                 'UserID' => $username,
                 'TransDate' => date('Y-m-d H:i:s')
                 );
             
                $effected =  $this->db->insert('tblnew_business_target',$data_array);
                }else{
                $data_array = array(
                 'PlantID' => $selected_company,
                 'FY' => $year,
                 'Staff_AccountID' => $staff_d['AccountID'],
                 'MonthID' => $month,
                 'AccountID' => $AccountID,
                 'ItemDivID' => $ItemDivID,
                 'Targate' => $data[$target][$j],
                 'UserID' => $username,
                 'TransDate' => date('Y-m-d H:i:s')
                 );
                $effected =  $this->db->insert('tblstaff_target',$data_array);
            }
                
                
            $j++;
              
          }
                 
         $i++;}
         return $effected;
     }
     public function create_targetSale_bkp_bussiness($data){
       
          $selected_company = $this->session->userdata('root_company');
          $username = $this->session->userdata('username');
      $year = $_SESSION['finacial_year'];
      $month = substr($data['month_data'], -2);
      $staff_d = $this->db->get_where('tblstaff',array('staffid'=>$data['Staff_AccountID']))->row_array();
    
        $i = 0; foreach($data['AccountID'] as $key=>$AccountID){
           $item_id = $AccountID.'_item_id';
           $target = $AccountID.'_target';
          $j = 0;   
        foreach($data[$item_id] as $key=>$ItemDivID){
            /*if($data[$target][$j] == "" || $data[$target][$j] == null){
                
            }else{*/
                $data_array = array(
                 'PlantID' => $selected_company,
                 'FY' => $year,
                 'Staff_AccountID' => $staff_d['AccountID'],
                 'MonthID' => $month,
                 'AccountID' => $AccountID,
                 'ItemDivID' => $ItemDivID,
                 'Targate' => $data[$target][$j],
                 'UserID' => $username,
                 'TransDate' => date('Y-m-d H:i:s')
                 );
                $effected =  $this->db->insert('tblstaff_target',$data_array);
            //}
                
            $j++;
              
          }
                 
         $i++;}
         return $effected;
     }
      public function update_targetSale($data){
       
          $selected_company = $this->session->userdata('root_company');
          $username = $this->session->userdata('username');
      $year = $_SESSION['finacial_year'];
      $month = substr($data['month_data'], -2);
      $staff_d = $this->db->get_where('tblstaff',array('staffid'=>$data['Staff_AccountID']))->row_array();
    
        $i = 0; foreach($data['AccountID'] as $key=>$AccountID){
           $item_id = $AccountID.'_item_id';
           $target = $AccountID.'_target';
          $j = 0;   foreach($data[$item_id] as $key=>$ItemDivID){
              if($AccountID == 'New_Business'){
                   $data_array = array(
              
                 'Targate' => $data[$target][$j],
                 'UserID2' => $username,
                 'Lupdate' => date('Y-m-d H:i:s')
                 );
                
                 $this->db->where('FY' ,$year);
                 $this->db->where('PlantID' ,$selected_company);
                 $this->db->where('MonthID' ,$month);
                 $this->db->where('Staff_AccountID' ,$staff_d['AccountID']);
                 $this->db->where('ItemDivID' ,$ItemDivID);
                $effected =  $this->db->update('tblnew_business_target',$data_array);
              }else{
                   $data_array = array(
              
                 'Targate' => $data[$target][$j],
                 'UserID2' => $username,
                 'Lupdate' => date('Y-m-d H:i:s')
                 );
                 $this->db->where('FY' ,$year);
                 $this->db->where('PlantID' ,$selected_company);
                 $this->db->where('MonthID' ,$month);
                 $this->db->where('Staff_AccountID' ,$staff_d['AccountID']);
                 $this->db->where('AccountID' ,$AccountID);
                 $this->db->where('ItemDivID' ,$ItemDivID);
                $effected =  $this->db->update('tblstaff_target',$data_array);
                 if($this->db->affected_rows() == 0){
                  $data_array = array(
                 'PlantID' => $selected_company,
                 'FY' => $year,
                 'Staff_AccountID' => $staff_d['AccountID'],
                 'MonthID' => $month,
                 'AccountID' => $AccountID,
                 'ItemDivID' => $ItemDivID,
                 'Targate' => $data[$target][$j],
                 'UserID' => $username,
                 'TransDate' => date('Y-m-d H:i:s')
                 );
               
                $effected =  $this->db->insert('tblstaff_target',$data_array);
                
             }
              }
              
          $j++;}
                 
         $i++;}
         return $effected;
     } 
    public function update_targetSale_bkp_bussiness($data){
       
        $selected_company = $this->session->userdata('root_company');
        $username = $this->session->userdata('username');
      $year = $_SESSION['finacial_year'];
      $month = substr($data['month_data'], -2);
      $staff_d = $this->db->get_where('tblstaff',array('staffid'=>$data['Staff_AccountID']))->row_array();
    
        $i = 0; foreach($data['AccountID'] as $key=>$AccountID){
           $item_id = $AccountID.'_item_id';
           $target = $AccountID.'_target';
          $j = 0;   foreach($data[$item_id] as $key=>$ItemDivID){
               $data_array = array(
              
                 'Targate' => $data[$target][$j],
                 'UserID2' => $username,
                 'Lupdate' => date('Y-m-d H:i:s')
                 );
                 $this->db->where('FY' ,$year);
                 $this->db->where('PlantID' ,$selected_company);
                 $this->db->where('MonthID' ,$month);
                 $this->db->where('Staff_AccountID' ,$staff_d['AccountID']);
                 $this->db->where('AccountID' ,$AccountID);
                 $this->db->where('ItemDivID' ,$ItemDivID);
                $effected =  $this->db->update('tblstaff_target',$data_array); 
          $j++;}
                 
         $i++;}
         return $effected;
     }
    // end target entery and targate vs achivements
    
    
    // market outstanding 
    
    //Get All route
    public function get_all_route(){
        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'route.*');
        $this->db->from(db_prefix() . 'route');
        $this->db->where(db_prefix() . 'route.PlantID', $selected_company);
        return $this->db->get()->result_array();
    }
    
    // Get All state
    public function get_all_states(){
        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'xx_statelist.*');
        $this->db->from(db_prefix() . 'xx_statelist');
        $this->db->where(db_prefix() . 'xx_statelist.country_id', 1);
        $this->db->order_by(db_prefix() . 'xx_statelist.state_name', "ASC");
        return $this->db->get()->result_array();
    }
    
    // Get All Distributor Type
    public function get_all_dist_type(){
        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'customers_groups.*');
        $this->db->from(db_prefix() . 'customers_groups');
        $this->db->where(db_prefix() . 'customers_groups.PlantID', $selected_company);
        $this->db->order_by(db_prefix() . 'customers_groups.name', "DESC");
        return $this->db->get()->result_array();
    }
    
    // Get All Item Division
    public function get_all_item_division(){
        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'items_groups.*');
        $this->db->from(db_prefix() . 'items_groups');
        //$this->db->where(db_prefix() . 'items_groups.PlantID', $selected_company);
        $this->db->order_by(db_prefix() . 'items_groups.name', "ASC");
        return $this->db->get()->result_array();
    }
    
    // Get All Item Division
    public function market_outstanding_data($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $routID = $filterdata["routID"];
        $states = $filterdata["states"];
        $loc_type = $filterdata["loc_type"];
        $dist_type = $filterdata["dist_type"];
        $staff_id = $filterdata["staff_id"];
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        
        $staff_ids = array();
        array_push($staff_ids, $staff_id);
        if($staff_id){
            $get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
            $get_result1 = $this->db->query($get_sql1)->result_array();
            foreach ($get_result1 as $key1 => $value1) {
                array_push($staff_ids, $value1["staffid"]);
                $get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
                $get_result2 = $this->db->query($get_sql2)->result_array();
                foreach ($get_result2 as $key2 => $value2) {
                    array_push($staff_ids, $value2["staffid"]);
                    $get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
                    $get_result3 = $this->db->query($get_sql3)->result_array();
                    foreach ($get_result3 as $key3 => $value3) {
                        array_push($staff_ids, $value3["staffid"]);
                        $get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
                        $get_result4 = $this->db->query($get_sql4)->result_array();
                        foreach ($get_result4 as $key4 => $value4) {
                            array_push($staff_ids, $value4["staffid"]);
                            $get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
                            $get_result5 = $this->db->query($get_sql5)->result_array();
                            foreach ($get_result5 as $key5 => $value5) {
                                array_push($staff_ids, $value5["staffid"]);
                            }
                        }
                    }
                }
            }
        }
    $staff_ids_uniqu = array_unique($staff_ids);  
    $staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
        $sql = '';
        
        
        $sql .= 'SELECT tblclients.StationName,tblclients.CtrlAccountID,tblclients.AccountID,tblclients.company,tblclients.state,tblaccountbalances.BAL1
        FROM tblclients'; 
        $sql .= ' INNER JOIN tblaccountbalances ON tblaccountbalances.AccountID=tblclients.AccountID AND tblaccountbalances.PlantID = tblclients.PlantID AND tblaccountbalances.FY = '.$fy;
        //$sql .= ' INNER JOIN tblaccountledger ON tblaccountledger.AccountID=tblclients.AccountID AND tblaccountledger.PlantID = tblclients.PlantID ';
        if($routID !==""){
           $sql .= ' INNER JOIN tblaccountroutes ON tblaccountroutes.AccountID = tblclients.AccountID AND tblaccountroutes.PlantID = tblclients.PlantID '; 
        }
        if($loc_type){
            $sql .= ' INNER JOIN tblaccountlocations ON tblclients.AccountID=tblaccountlocations.AccountID AND tblclients.PlantID = tblaccountlocations.PlantID';
        }
        if($staff_id){
            $sql .= ' INNER JOIN tblcustomer_admins ON tblcustomer_admins.customer_id=tblclients.AccountID AND tblcustomer_admins.company_id = tblclients.PlantID';
        }
        $sql .= ' WHERE tblclients.PlantID = '.$selected_company. ' AND tblclients.SubActGroupID = "60001004" ';
        if($states !==""){
            $sql .= ' AND tblclients.state = "'.$states.'"';
        }
        
        if($dist_type !==""){
            $sql .= ' AND tblclients.DistributorType = "'.$dist_type.'"';
        }
        if($routID !==""){
            $sql .= ' AND tblaccountroutes.RouteID = "'.$routID.'" AND tblaccountroutes.PlantID = '.$selected_company;
        }
        if($loc_type == "3"){
            $sql .= ' AND tblaccountlocations.LocationTypeID IN(1,2,3) AND tblaccountlocations.PlantID = '.$selected_company;
        }else{
            $sql .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type.' AND tblaccountlocations.PlantID = '.$selected_company;
        }
        if($staff_id){
            $sql .= ' AND tblcustomer_admins.staff_id IN('.$staff_ids_uniqu_s.')';
        }
        //$sql .= ' GROUP BY tblaccountledger.AccountID';
        $sql .= ' Order BY tblclients.AccountID ASC';
        
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function market_outstanding_credit_data($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $sql = '';
        
        $sql .= 'SELECT SUM(tblaccountledger.Amount) AS Credit_Amt,AccountID
        FROM tblaccountledger 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "C" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY tblaccountledger.AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function market_outstanding_debit_data($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $sql = '';
        
        $sql .= 'SELECT SUM(tblaccountledger.Amount) AS Debit_Amt,AccountID
        FROM tblaccountledger 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "D" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY tblaccountledger.AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function market_outstanding_trans_data($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $sql = '';
        
        $sql .= 'SELECT SUM(tblaccountledger.Amount) AS total_Amt,tblaccountledger.AccountID
        FROM tblaccountledger 
        INNER JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
        WHERE tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$fy.'" AND tblclients.SubActGroupID= "60001004" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY tblaccountledger.AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function market_outstanding_last_billDate($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $sql = '';
        
        $sql .= 'SELECT max(TransDate2) AS TransDate2,AccountID FROM tblhistory 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "O" AND TType2 = "Order" AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    
    public function market_outstanding_currDaySale($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $sql = '';
        
        $sql .= 'SELECT SUM(NetChallanAmt) AS NetChallanAmt,AccountID FROM tblhistory 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "O" AND TType2 = "Order" AND tblhistory.TransDate2 BETWEEN "'.$to_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    public function market_outstanding_preDaySale($filterdata){
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $from_date = "2021-04-01";
        $to_date = to_sql_date($filterdata["as_on"]); 
        $to_date = date('Y-m-d', strtotime('-1 day', strtotime($to_date)));
        $sql = '';
        
        $sql .= 'SELECT SUM(NetChallanAmt) AS NetChallanAmt,AccountID FROM tblhistory 
        WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "O" AND TType2 = "Order" AND tblhistory.TransDate2 BETWEEN "'.$to_date.' 00:00:00" AND "'.$to_date.' 23:59:59"  
        GROUP BY AccountID';
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
    // end market outstanding
   
   // Start Create ledger 
   
   public function get_vendor_data($id = '', $where = [])
    {
      
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select( db_prefix() . 'clients.company,'.db_prefix() . 'clients.userid,'.db_prefix() . 'clients.AccountID,');
       
        $this->db->where_in(db_prefix() . 'clients.SubActGroupID', ['50003002','60001004']);
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $this->db->order_by('company', 'asc');
        return $this->db->get(db_prefix() . 'clients')->result_array();
    }
   public function get_state_list(){
        // tblxx_statelist
       return $this->db->order_by('state_name')->get('tblxx_statelist')->result_array();
    }
     public function get_data_vendor($id = '')
    {
     $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
       $this->db->select();
        $this->db->from(db_prefix() . 'clients');
        $this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');
        $this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
        $this->db->where(db_prefix() . 'clients.AccountID', $id);
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
       return $this->db->get()->row();
 
      
    }
    public function getCratesRcvdVehicle($filterdata){
        $from_date = to_sql_date($filterdata);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
        $regExp1 ="'.*;s:[0-9]+:";
        $regExp2 =".*'";
        
        $sql = 'SELECT tblvehiclereturn.*,tblroute.name,tblchallanmaster.VehicleID,tblstaff.firstname,tblstaff.lastname FROM tblvehiclereturn 
                INNER JOIN tblchallanmaster ON tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = tblvehiclereturn.PlantID AND tblchallanmaster.FY = tblvehiclereturn.FY
                INNER JOIN tblroute ON tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID
                LEFT JOIN tblstaff ON tblstaff.AccountID = tblchallanmaster.DriverID AND tblstaff.staff_comp REGEXP '.$regExp1.'"'.$selected_company.'"'.$regExp2.' 
                WHERE tblvehiclereturn.FY = '.$fy.'  AND tblvehiclereturn.PlantID = '.$selected_company.' AND  tblvehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59" 
                Order BY tblvehiclereturn.ChallanID ASC';
        $result_all = $this->db->query($sql)->result_array();
        $i = 0;
        foreach ($result_all as $key => $value) {
            $sql2 = 'SELECT tblaccountcrates.Qty,tblclients.company FROM tblaccountcrates 
            INNER JOIN tblclients ON tblclients.AccountID = tblaccountcrates.AccountID AND tblclients.PlantID = tblaccountcrates.PlantID
            WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.VoucherID = "'.$value['ReturnID'].'" AND TType = "C" 
            Order BY tblclients.company ASC';
            $PartyDetails = $this->db->query($sql2)->result_array();
            $result_all[$i]['PartyDetails'] = $PartyDetails;
            $i++;
        }
        return $result_all;
    }
    public function get_Crates_for_body_data($filterdata){
        
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]);
        $accountId = $filterdata["accountId"];
        $state_type = $filterdata["state_type"];
        $loc_type = $filterdata["loc_type"];
        $order_by = $filterdata["order_by"];
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        if ( date('m') <= 3 ) {
            $year = date('y') - 1;
        }
        else {
            $year = date('y');
        }
         if($accountId != ''){
            
            $sql = 'SELECT tblaccountcrates.* FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND  tblaccountcrates.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblaccountcrates.AccountID = "'.$accountId.'" ';
            $result_all = $this->db->query($sql)->result_array();
        
            $sql2 = 'SELECT * FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom = "OPENCRATES" AND tblaccountcrates.AccountID = "'.$accountId.'" ';
            $result_open_cr_debit = $this->db->query($sql2)->row();
            
            $result = array(
                'all' => $result_all,
                'opn_caret' => $result_open_cr_debit,
                );
                // print_r($result);die;
            return $result;
         }else{
             
             
            $Billing_end_Date = to_sql_date($filterdata["from_date"]);
            $state_type = $filterdata["state_type"];
            $loc_type = $filterdata["loc_type"];
            $order_by = $filterdata["order_by"];
            $currDate = date('Y-m-d');
            $preDay = date('Y-m-d', strtotime('-1 day', strtotime($currDate)));
            $Billing_start_Date =  '20'.$year.'-04-01';
            
            $Vehicle_end_Rtn_Date = to_sql_date($filterdata["to_date"]);
            $Vehicle_start_Rtn_Date = '20'.$year.'-04-01';
            if($Billing_end_Date > $Vehicle_end_Rtn_Date){
                $max_date = $Billing_end_Date;
            }else{
                 $max_date = $Vehicle_end_Rtn_Date;
            }
       
            $sql = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID,tblclients.company,tblclients.address,tblclients.StationName FROM tblaccountcrates 
                    LEFT JOIN tblclients ON tblclients.AccountID=tblaccountcrates.AccountID AND tblclients.PlantID = '.$selected_company; 
                    if($loc_type){
                    $sql .= ' INNER JOIN tblaccountlocations ON tblaccountcrates.AccountID=tblaccountlocations.AccountID AND tblaccountcrates.PlantID = tblaccountlocations.PlantID';
                    }
                    $sql .= ' WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND  tblaccountcrates.Transdate BETWEEN "'.$Billing_start_Date.' 00:00:00" AND "'.$max_date.' 23:59:59"';
            if($state_type){
                $sql .= ' AND tblclients.state = "'.$state_type.'"';
            }
            if($loc_type == "3"){
                $sql .= ' AND tblaccountlocations.LocationTypeID IN(1,2,3) AND tblaccountlocations.PlantID = '.$selected_company;
            }else{
                $sql .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type.' AND tblaccountlocations.PlantID = '.$selected_company;
            }
            
            $sql .= ' Group BY tblaccountcrates.AccountID';
            if($order_by == 1){
                $sql .= ' ORDER BY tblclients.StationName ASC';
            }else{
                $sql .= ' ORDER BY tblclients.company ASC';
            }
            $result_all = $this->db->query($sql)->result_array();
        
            $sql1 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.TType = "D" AND tblaccountcrates.PassedFrom != "OPENCRATES"  AND tblaccountcrates.Transdate BETWEEN "'.$Billing_start_Date.' 00:00:00" AND "'.$Billing_end_Date.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_debit = $this->db->query($sql1)->result_array();
            
            $sql2 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.TType = "C" AND tblaccountcrates.PassedFrom != "OPENCRATES" AND tblaccountcrates.Transdate BETWEEN "'.$Vehicle_start_Rtn_Date.' 00:00:00" AND "'.$Vehicle_end_Rtn_Date.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_credit = $this->db->query($sql2)->result_array();
            
            $sql3 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.TType = "D" AND tblaccountcrates.PassedFrom = "OPENCRATES" AND tblaccountcrates.Transdate BETWEEN "'.$Vehicle_start_Rtn_Date.' 00:00:00" AND "'.$Vehicle_end_Rtn_Date.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_open_cr_debit = $this->db->query($sql3)->result_array();
            
            $sql33 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.TType = "C" AND tblaccountcrates.PassedFrom = "OPENCRATES" AND tblaccountcrates.Transdate BETWEEN "'.$Vehicle_start_Rtn_Date.' 00:00:00" AND "'.$Vehicle_end_Rtn_Date.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_open_cr_credit = $this->db->query($sql33)->result_array();
            
            /*$sql4 .= 'SELECT max(TransDate2) AS lastBill,AccountID FROM tblhistory 
                    WHERE PlantID = '.$selected_company.' AND FY = "'.$fy.'" AND TType = "O" AND TType2 = "Order" AND tblhistory.TransDate2 BETWEEN "'.$Billing_start_Date.' 00:00:00" AND "'.$Billing_end_Date.' 23:59:59"  
                    GROUP BY AccountID';
            $result_lastBill = $this->db->query($sql4)->result_array();*/
            
            /*$sql5 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND tblaccountcrates.Transdate BETWEEN "'.$preDay.' 00:00:00" AND "'.$preDay.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_preDay = $this->db->query($sql5)->result_array();
            
            $sql6 = 'SELECT SUM(tblaccountcrates.Qty)as sum_total , tblaccountcrates.AccountID FROM tblaccountcrates 
                    WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND tblaccountcrates.Transdate BETWEEN "'.$currDate.' 00:00:00" AND "'.$currDate.' 23:59:59"  Group BY tblaccountcrates.AccountID';
            $result_currDay = $this->db->query($sql6)->result_array();*/
            
            
            $result = array(
                'all' => $result_all,
                'debit' => $result_debit,
                'credit' => $result_credit,
                'opn_debit' => $result_open_cr_debit,
                'opn_credit' => $result_open_cr_credit,
                /*'lastBill' => $result_lastBill,
                'preDay' => $result_preDay,
                'currDay' => $result_currDay,*/
                );
                // print_r($result);die;
            return $result;
           
        }
    }
    
    public function GetCrateLedger($filterdata)
    {
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]);
        $accountId = $filterdata["accountId"];
        $state_type = $filterdata["state_type"];
        $loc_type = $filterdata["loc_type"];
        $order_by = $filterdata["order_by"];
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        if ( date('m') <= 3 ) {
            $year = date('y') - 1;
        }else {
            $year = date('y');
        }
        $FirstDate = '20'.$year.'-04-01';
        if($accountId != ''){
            $sql = 'SELECT SUM(Qty) AS OQty FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom = "OPENCRATES"  AND tblaccountcrates.AccountID = "'.$accountId.'"';
                $OpenCrate = $this->db->query($sql)->result_array();
                $OQty =  $OpenCrate['0']['OQty'];
                
            if($from_date == $FirstDate){
                $FromDate = $FirstDate;
                $ToDate = to_sql_date($filterdata["to_date"]);
                $sql1 = 'SELECT * FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND  tblaccountcrates.Transdate BETWEEN "'.$FromDate.' 00:00:00" AND "'.$ToDate.' 23:59:59" 
                AND tblaccountcrates.AccountID = "'.$accountId.'" ORDER BY tblaccountcrates.Transdate ASC';
                $Trans = $this->db->query($sql1)->result_array();
                $result = array(
                    'OpenCrate' => $OQty,
                    'Trans' => $Trans,
                );
                return $result;
            
            }else{
                $FromDate = $FirstDate;
                $ToDate = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
                $sql = 'SELECT TType,SUM(Qty) AS Qty FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND  tblaccountcrates.Transdate BETWEEN "'.$FromDate.' 00:00:00" AND "'.$ToDate.' 23:59:59" AND tblaccountcrates.AccountID = "'.$accountId.'" GROUP BY TType';
                $OpenCrate = $this->db->query($sql)->result_array();
                foreach ($OpenCrate as $key => $value) {
                    if($value['TType']== 'C'){
                        $OPNBal -= $value['Qty'];
                    }else{
                        $OPNBal += $value['Qty'];
                    }
                }
                $OQtyNew = $OPNBal + $OQty;
                
                $FromDate = $from_date;
                $ToDate = $to_date;
                $sql = 'SELECT * FROM tblaccountcrates 
                WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND  tblaccountcrates.Transdate BETWEEN "'.$FromDate.' 00:00:00" AND "'.$ToDate.' 23:59:59" 
                AND tblaccountcrates.AccountID = "'.$accountId.'" ORDER BY tblaccountcrates.Transdate ASC';
                $Trans = $this->db->query($sql)->result_array();
                $result = array(
                    'OpenCrate' => $OQtyNew,
                    'Trans' => $Trans,
                );
                return $result;
            }
        }else{
            
            $sql = 'SELECT tblaccountcrates.AccountID,SUM(tblaccountcrates.Qty) AS OQty FROM tblaccountcrates 
            WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom = "OPENCRATES"  GROUP BY AccountID';
            $OpenCrate = $this->db->query($sql)->result_array();
            
            $FromDate = $FirstDate;
            $ToDate = $from_date;
            $sql1 = 'SELECT tblaccountcrates.AccountID,SUM(tblaccountcrates.Qty) AS OQty FROM tblaccountcrates 
            WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND tblaccountcrates.TType = "D" AND  tblaccountcrates.Transdate BETWEEN "'.$FromDate.' 00:00:00" AND "'.$ToDate.' 23:59:59"  GROUP BY AccountID';
            $DebitCrate = $this->db->query($sql1)->result_array();
            
            $ToDate = $to_date;
            $sql11 = 'SELECT tblaccountcrates.AccountID,SUM(tblaccountcrates.Qty) AS OQty FROM tblaccountcrates 
            WHERE tblaccountcrates.FY = '.$fy.'  AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.PassedFrom != "OPENCRATES" AND tblaccountcrates.TType = "C" AND  tblaccountcrates.Transdate BETWEEN "'.$FromDate.' 00:00:00" AND "'.$ToDate.' 23:59:59"  GROUP BY AccountID';
            $CreditCrate = $this->db->query($sql11)->result_array();
            
            $sql111 = 'SELECT tblclients.AccountID,tblclients.company,tblclients.address,tblclients.StationName FROM tblclients ';
                if($loc_type){
                    $sql111 .= ' INNER JOIN tblaccountlocations ON tblclients.AccountID=tblaccountlocations.AccountID AND tblclients.PlantID = tblaccountlocations.PlantID';
                }
            $sql111 .= ' WHERE tblclients.PlantID = '.$selected_company.' ';
            if($loc_type == "3"){
                $sql111 .= ' AND tblaccountlocations.LocationTypeID IN(1,2,3) AND tblaccountlocations.PlantID = '.$selected_company;
            }else{
                $sql111 .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type.' AND tblaccountlocations.PlantID = '.$selected_company;
            }
            if($state_type !==''){
                $sql111 .= ' AND tblclients.state = "'.$state_type.'"';
            }
            if($order_by == 1){
                $sql111 .= ' ORDER BY tblclients.AccountID ASC';
            }else{
                $sql111 .= ' ORDER BY tblclients.company ASC';
            }
            
            $AllAccount = $this->db->query($sql111)->result_array();
            
            $result = array(
                'OpenCrate' => $OpenCrate,
                'Debit' => $DebitCrate,
                'Credit' => $CreditCrate,
                'AllAccount' => $AllAccount,
            );
            return $result;
        }
    }
	
	public function All_staff()
	{
		 return $this->db->get(db_prefix().'staff')->result_array();
	}
	
	public function GetdepDataBySureyID($survey_ids)
	{
	    $this->db->select('tblsurveyDependants.*');
                
        $this->db->where_in(db_prefix() . 'surveyDependants.SurveyID', $survey_ids);
                
        $PurchExp = $this->db->get(db_prefix() . 'surveyDependants')->result_array();
        return $PurchExp;
	}
	public function get_survey_data($filterdata)
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.* , tblstaff.firstname ,tblstaff.lastname,tblxx_statelist.state_name,tblxx_citylist.city_name,tblTalukaMaster.TalukaName
		FROM `tblsurvey` 
        INNER JOIN tblstaff ON tblstaff.staffid = tblsurvey.UserID 
        INNER JOIN tblxx_statelist ON tblxx_statelist.short_name = tblsurvey.state 
        INNER JOIN tblxx_citylist ON tblxx_citylist.id = tblsurvey.district 
        INNER JOIN tblTalukaMaster ON tblTalukaMaster.id = tblsurvey.taluka 
        WHERE tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		if($filterdata["staff_id"]!=="")
		{
			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
		}
	    $body_data = $this->db->query($sql)->result_array();
        //$result = $this->db->query($sql)->result_array();
        //return $result;
        
        	$dep_fields = ['name', 'number', 'gut_number', 'Irrigated_land', 'UnIrrigated_land', 'total_land'];
            $equip_fields = ['name', 'number', 'company'];
            $livestock_fields = ['name', 'number', 'milk_per_day', 'breed'];
            $crop_fields = ['Year', 'name', 'kharif', 'rabi'];
            $prod_fields = ['CostType', 'name', 'value'];
            $main_fields = ['name', 'mobile_number', 'state', 'district', 'taluka', 'village', 'well', 'borewell', 'canal', 'river_nala',
                'farm_pond', 'fisheries', 'fisheries_revenue', 'Feed_per_day', 'Feed_purchase', 'FeedAvgCostPerKG', 'FeedCompany', 'OtherRate', 'DairyRate',
                'labour_in_village', 'labour_in_nearby_village', 'male_labour_cost', 'female_labour_cost', 'solar_pump', 'solar_capacity', 'crop_insurance',
                'insurance_company', 'compensations_received', 'PMKSN', 'AgriEquipmentByPanchayat', 'smart_phone_user', 'WhatsAppUser', 'youtube_referred', 'WhatsAppAgriService',
                'ServiceIsPaid', 'ServicePaidAmt', 'PaymentFrquancy', 'mob_used_for_forcasting'];
            $maincount = 58;
        
            $survey_ids = array_column($body_data, 'id');
            $survey_ids_str = implode(',', $survey_ids);
            //$depdata = $this->GetdepDataBySureyID($survey_ids);
            
            // Helper function (can move outside if preferred)
            function getFilledColumnCountPerSurvey($table, $fields, $survey_ids_str, $db) {
                $survey_ids = explode(',', $survey_ids_str);
                $result_map = [];
        
                foreach ($survey_ids as $survey_id) {
                    $survey_id = intval($survey_id);
        
                    $nonEmptyConditions = array_map(fn($f) => "($f IS NOT NULL AND $f != '')", $fields);
                    $condition_str = implode(' OR ', $nonEmptyConditions);
        
                    $sql = "SELECT * FROM $table WHERE SurveyID = $survey_id AND ($condition_str) LIMIT 1";
                    $row = $db->query($sql)->row_array();
        
                    if ($row) {
                        $filled_count = 0;
                        foreach ($fields as $field) {
                            if (isset($row[$field])) {
                                $val = trim((string)$row[$field]);
                                if ($val !== '') {
                                    $filled_count++;
                                }
                            }
                        }
                        $result_map[$survey_id] = $filled_count;
                    } else {
                        $result_map[$survey_id] = 0;
                    }
                }
        
                return $result_map;
            }

            $dependants_map = getFilledColumnCountPerSurvey('tblsurveyDependants', $dep_fields, $survey_ids_str, $this->db);
            $equipment_map = getFilledColumnCountPerSurvey('tblsurveyEquipment', $equip_fields, $survey_ids_str, $this->db);
            $livestock_map = getFilledColumnCountPerSurvey('tblSurveyLivestock', $livestock_fields, $survey_ids_str, $this->db);
            $croppattern_map = getFilledColumnCountPerSurvey('tblSurveyCropPattern', $crop_fields, $survey_ids_str, $this->db);
            $costprod_map = getFilledColumnCountPerSurvey('tblSurveyProductionCost', $prod_fields, $survey_ids_str, $this->db);

            foreach ($body_data as &$value) {
            $filledvillagesurvey = 0;
            $survey_id = $value['id'];
    
            foreach ($main_fields as $field) {
                if (isset($value[$field])) {
                    $val = trim((string)$value[$field]);
                    if ($val !== '') {
                        $filledvillagesurvey++;
                    }
                }
            }
    
            if (!empty($depdata)) {
                if($depdata[''])
                $filledvillagesurvey += $dependants_map[$survey_id];
            }
            if (!empty($equipment_map[$survey_id])) {
                $filledvillagesurvey += $equipment_map[$survey_id];
            }
            if (!empty($livestock_map[$survey_id])) {
                $filledvillagesurvey += $livestock_map[$survey_id];
            }
            if (!empty($croppattern_map[$survey_id])) {
                $filledvillagesurvey += $croppattern_map[$survey_id];
            }
            if (!empty($costprod_map[$survey_id])) {
                $filledvillagesurvey += $costprod_map[$survey_id];
            }
    
            $value['filcount'] = $filledvillagesurvey;
            $value['totalpercent'] = number_format(($filledvillagesurvey / $maincount) * 100, 2);
        }
    
            return $body_data;
	}
	
	public function Get_dependants($filterdata,$SurveyID = "")
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.id,tblsurveyDependants.name ,tblsurveyDependants.number,
        tblsurveyDependants.gut_number,tblsurveyDependants.Irrigated_land,tblsurveyDependants.UnIrrigated_land,tblsurveyDependants.total_land
		FROM `tblsurvey` 
        INNER JOIN tblsurveyDependants ON tblsurveyDependants.SurveyID = tblsurvey.id 
        WHERE ';
		if($SurveyID){
		    $sql .=' tblsurvey.id = "'.$SurveyID.'"';
		}else{
		    $sql .=' tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		    if($filterdata["staff_id"]!=="")
    		{
    			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
    		}
		}
		
		$sql .= ' ORDER BY tblsurveyDependants.id ASC';
        $result = $this->db->query($sql)->result_array();
        return $result;
	}
	
	public function Get_equipment($filterdata,$SurveyID = "")
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.id,tblsurveyEquipment.name,tblsurveyEquipment.number,tblsurveyEquipment.company
		FROM `tblsurvey` 
        INNER JOIN tblsurveyEquipment ON tblsurveyEquipment.SurveyID = tblsurvey.id 
        WHERE';
        
        if($SurveyID){
		    $sql .=' tblsurvey.id = "'.$SurveyID.'"';
		}else{
		    $sql .=' tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		    if($filterdata["staff_id"]!=="")
    		{
    			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
    		}
		}
		$sql .= ' ORDER BY tblsurveyEquipment.id ASC';
        $result = $this->db->query($sql)->result_array();
        return $result;
	}
	
	public function Get_livestock($filterdata,$SurveyID = "")
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.id,tblSurveyLivestock.name,tblSurveyLivestock.number,tblSurveyLivestock.milk_per_day,tblSurveyLivestock.breed
		FROM `tblsurvey` 
        INNER JOIN tblSurveyLivestock ON tblSurveyLivestock.SurveyID = tblsurvey.id 
        WHERE ';
        if($SurveyID){
		    $sql .=' tblsurvey.id = "'.$SurveyID.'"';
		}else{
		    $sql .=' tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		    if($filterdata["staff_id"]!=="")
    		{
    			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
    		}
		}
		$sql .= ' ORDER BY tblSurveyLivestock.id ASC';
        $result = $this->db->query($sql)->result_array();
        return $result;
	}
	
	
	
	public function Get_crop_pattern($filterdata,$SurveyID = "")
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.id,tblSurveyCropPattern.Year,tblSurveyCropPattern.name,tblSurveyCropPattern.kharif,tblSurveyCropPattern.rabi
		FROM `tblsurvey` 
        INNER JOIN tblSurveyCropPattern ON tblSurveyCropPattern.SurveyID = tblsurvey.id 
        WHERE ';
        if($SurveyID){
		    $sql .=' tblsurvey.id = "'.$SurveyID.'"';
		}else{
		    $sql .=' tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		    if($filterdata["staff_id"]!=="")
    		{
    			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
    		}
		}
        $result = $this->db->query($sql)->result_array();
        return $result;
	}
	
	public function Get_production_cost($filterdata,$SurveyID = "")
	{
        $from_date = to_sql_date($filterdata["from_date"]);
        $to_date = to_sql_date($filterdata["to_date"]); 
        $sql = '';
        
        $sql .= 'SELECT tblsurvey.id,tblSurveyProductionCost.CostType,tblSurveyProductionCost.name,tblSurveyProductionCost.value
		FROM `tblsurvey` 
        INNER JOIN tblSurveyProductionCost ON tblSurveyProductionCost.SurveyID = tblsurvey.id 
        WHERE ';
        
        if($SurveyID){
		    $sql .=' tblsurvey.id = "'.$SurveyID.'"';
		}else{
		    $sql .=' tblsurvey.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
		    if($filterdata["staff_id"]!=="")
    		{
    			$sql .=' AND tblsurvey.UserID = "'.$filterdata["staff_id"].'"';
    		}
		}
        $result = $this->db->query($sql)->result_array();
        return $result;
	}
	
	public function get_state($state)
	{
		$sql = 'SELECT state_name FROM tblxx_statelist WHERE id = "'.$state.'"';
		$result = $this->db->query($sql)->row();
        return $result;
	}
	
	public function get_city($city)
	{
		$sql = 'SELECT city_name FROM tblxx_citylist WHERE id = "'.$city.'"';
		$result = $this->db->query($sql)->row();
        return $result;
	}
	
	public function get_taluka($taluka)
	{
		$sql = 'SELECT TalukaName FROM tblTalukaMaster WHERE id = "'.$taluka.'"';
		$result = $this->db->query($sql)->row();
        return $result;
	}
	
	
	public function get_survey_details($id)
	{
		$sql = 'SELECT tblsurvey.*, tblstaff.firstname ,tblstaff.lastname,tblxx_statelist.state_name,tblxx_citylist.city_name,tblTalukaMaster.TalukaName
		FROM tblsurvey
		INNER JOIN tblstaff ON tblstaff.staffid = tblsurvey.UserID 
        INNER JOIN tblxx_statelist ON tblxx_statelist.short_name = tblsurvey.state 
        INNER JOIN tblxx_citylist ON tblxx_citylist.id = tblsurvey.district 
        INNER JOIN tblTalukaMaster ON tblTalukaMaster.id = tblsurvey.taluka 
		WHERE tblsurvey.id  = "'.$id.'"';
		$result = $this->db->query($sql)->row();
        return $result;
	}
	
	
	
	
	
   // END Create ledger
   
//================ Breakenen Sheet Start =======================================
    
    public function GetActSubGroup2ByMainID($DirectExp = '',$post_data)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year'); 
        $FirstDateFY = "20".$fy.'-04-01 00:00:00';
        $ToDate = to_sql_date($post_data['to_date']).' 23:59:59';
        $commodity = $post_data['commodity'];
        $center = $post_data['center'];
        
        $this->db->select('tblaccountgroupssub1.ActGroupID,tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1,
        tblaccountgroupssub.SubActGroupID AS SubActGroupID2,tblaccountgroupssub.SubActGroupName AS SubActGroupName2');
        $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID1=' . db_prefix() . 'accountgroupssub1.SubActGroupID1');
        $this->db->or_where(db_prefix() . 'accountgroupssub1.ActGroupID', $DirectExp);
        $result = $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();
        if($result){
            
            $ItemID = $post_data["commodity"];
            $CenterID = $post_data["commodity"];
            $Todate = to_sql_date($post_data["to_date"]);
            $i = 0;
            foreach($result as $Key=>$val){
                // For Purchase
                $this->db->select('SUM(tblaccountledger.Amount) AS PurchExpAmt');
                $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID =' . db_prefix() . 'accountledger.AccountID');
                $this->db->where(db_prefix() . 'clients.SubActGroupID', $val["SubActGroupID2"]);
                $this->db->where(db_prefix() . 'accountledger.EntryFor', "2");
                $this->db->where(db_prefix() . 'accountledger.TType', "D");
                $this->db->where("tblaccountledger.Transdate BETWEEN '$FirstDateFY' AND '$ToDate'");
                if($commodity){
                    $this->db->where(db_prefix() . 'accountledger.CommodityID', $commodity);
                }
                if($center){
                    $this->db->where(db_prefix() . 'accountledger.CenterID', $center);
                }
                $PurchExp = $this->db->get(db_prefix() . 'accountledger')->row();
                $result[$i]['PurchExp'] = $PurchExp->PurchExpAmt;
                
                // For Sale
                $this->db->select('SUM(tblaccountledger.Amount) AS SaleExpAmt');
                $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID =' . db_prefix() . 'accountledger.AccountID');
                $this->db->where(db_prefix() . 'clients.SubActGroupID', $val["SubActGroupID2"]);
                $this->db->where(db_prefix() . 'accountledger.EntryFor', "3");
                $this->db->where(db_prefix() . 'accountledger.TType', "D");
                $this->db->where("tblaccountledger.Transdate BETWEEN '$FirstDateFY' AND '$ToDate'");
                if($commodity){
                    $this->db->where(db_prefix() . 'accountledger.CommodityID', $commodity);
                }
                if($center){
                    $this->db->where(db_prefix() . 'accountledger.CenterID', $center);
                }
                $SaleExp = $this->db->get(db_prefix() . 'accountledger')->row();
                $result[$i]['SaleExp'] = $SaleExp->SaleExpAmt;
                
                $i++;
            }
            
        
            return $result;
            
        }
    }
    
    public function GetSalePurchaseData($post_data)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year'); 
        $FirstDateFY = '20'.$fy.'-04-01 00:00:00';
        $ToDate = to_sql_date($post_data['to_date']).' 23:59:59';
        $commodity = $post_data['commodity'];
        $center = $post_data['center'];
        $this->db->select('tblhistory.TType,tblhistory.TType2,tblhistory.cgst,tblhistory.sgst,tblhistory.igst,SUM(tblhistory.BilledQty) AS QTYMT,SUM(tblhistory.final_rate * tblhistory.BilledQty) AS TotalAmt');
        $this->db->where("tblhistory.TransDate BETWEEN '$FirstDateFY' AND '$ToDate'");
        if($commodity){
            $this->db->where(db_prefix() . 'history.ItemID', $commodity);
        }
        if($center){
            $this->db->where(db_prefix() . 'history.CenterID', $center);
        }
        $this->db->where(db_prefix() . 'history.PartyID', "KASPL");
        $this->db->group_by('tblhistory.OrderID,tblhistory.TType,tblhistory.TType2');
        $this->db->order_by('tblhistory.TType','ASC');
        return $this->db->get('tblhistory')->result_array();
        
        /*$this->db->select('tblitems.GroupCode,tbllead_master.CenterID,(SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight)) AS NetWeight,(tblGateMaster.final_rate * (SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight))) AS Amount');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $yourCommodityArray);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $Type);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'lead_master.FY', $fy);
        
        $this->db->group_by('tbllead_master.CenterID,tblitems.GroupCode,tblGateMaster.final_rate');
        $this->db->order_by('tbllead_master.CenterID,tblitems.GroupCode','ASC');
        return $this->db->get('tblGateMaster')->result_array();
        
        $this->db->select('tblaccountgroupssub1.ActGroupID,tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName1,
        tblaccountgroupssub.SubActGroupID AS SubActGroupID2,tblaccountgroupssub.SubActGroupName AS SubActGroupName2');
        $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID1=' . db_prefix() . 'accountgroupssub1.SubActGroupID1');
        $this->db->or_where(db_prefix() . 'accountgroupssub1.ActGroupID', $DirectExp);
        return $this->db->get(db_prefix() . 'accountgroupssub1')->result_array();*/
    }
    
    public function get_breakenensheet_report_data($data = '')
    {
        // $from_date = to_sql_date($data['from_date']).' '.date('00:00:00');
        // $to_date = to_sql_date($data['to_date']).' '.date('23:59:59');
        
        if(isset($data['center'])) {
            $centerIDArray = $data['center'];
            foreach($centerIDArray as $value){
                $this->db->or_where(db_prefix() . 'lead_master.CenterID', $value);
            }
        }
        if(isset($data['commodity'])) {
            $commodityArray = $data['commodity'];
            foreach($commodityArray as $value){
                $this->db->or_where(db_prefix() . 'history.ItemID', $value);
            }
        }
        if(isset($data['to_date'])) {
            $to_date = to_sql_date($data['to_date']).' '.date('23:59:59');
            $this->db->where(db_prefix() . 'lead_master.TransDate < ', $to_date);
        }
        $this->db->join(db_prefix() . 'lead_master', db_prefix() . 'lead_master.BookingID=' . db_prefix() . 'history.BillID', 'left');
        $this->db->select('SUM(tblhistory.OrderQty) as totalOrderQty , SUM(tblhistory.OrderAmt) as totalOrderAmt');
        return $this->db->get(db_prefix() . 'history')->result_array();
    }
//================ Breakenen Sheet End =========================================

//================ Balance Sheet Start =======================================
    
    public function BalanceSheetHead($data = '')
    {
        
        $this->db->select('tblaccountgroupssub.SubActGroupID AS SubActGroupID2,tblaccountgroupssub.SubActGroupName AS SubActGroupName2,
        tblaccountgroupssub1.SubActGroupID1 AS SubActGroupID1,tblaccountgroupssub1.SubActGroupName AS SubActGroupName2,
        tblaccountgroups.ActGroupID,tblaccountgroups.ActGroupName');
        $this->db->join(db_prefix() . 'accountgroupssub1', db_prefix() . 'accountgroupssub1.SubActGroupID1=' . db_prefix() . 'accountgroupssub.SubActGroupID1');
        $this->db->join(db_prefix() . 'accountgroups', db_prefix() . 'accountgroups.ActGroupID=' . db_prefix() . 'accountgroupssub1.ActGroupID');
        return $this->db->get(db_prefix() . 'history')->result_array();
    }
    
    public function fetchAccountsData($BalanceSheet_head)
    {
        
        $this->db->select('tblaccountgroups.ActGroupName,tblaccountgroups.ActGroupID');
        $this->db->where_in('tblaccountgroups.ActGroupID', $BalanceSheet_head);
        return $this->db->get('tblaccountgroups')->result_array();
    }
    
    public function fetchAccountsDataSubGroup($mainGroupID)
    {
        $this->db->select('tblaccountgroupssub1.SubActGroupName,tblaccountgroupssub1.SubActGroupID1');
        $this->db->where('ActGroupID', $mainGroupID);
        return $this->db->get('tblaccountgroupssub1')->result_array();
    }
        
    public function fetchAccountsDataSubGroup1($subGroupID)
    {
        $this->db->select('tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID');
        $this->db->where('SubActGroupID1', $subGroupID);
        return $this->db->get('tblaccountgroupssub')->result_array();
        
    }
    
    public function GetLedgerData($BalanceSheet_head)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        //$Ledger_data = array();
        $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountledger.FY');
        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head);
        $this->db->where('tblaccountledger.FY', $fy);
        $this->db->where('tblaccountledger.PlantID', $selected_company);
        $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');
        $CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();
        $Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;
        // Privius year ledger
        $last_fy = $fy - 1;
        $this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountledger.FY');
        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head);
        $this->db->where('tblaccountledger.FY', $last_fy);
        $this->db->where('tblaccountledger.PlantID', $selected_company);
        $this->db->group_by('tblaccountledger.TType,tblclients.SubActGroupID');
        $lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();
        $Ledger_data->Last_yr_ledger = $lastYrLedger_data;
        return $Ledger_data;
    }
    
    public function GetOpnBalData($BalanceSheet_head)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        
        //$Ledger_data = array();
        $this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountbalances.FY');
        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head);
        $this->db->where('tblaccountbalances.FY', $fy);
        $this->db->where('tblaccountbalances.PlantID', $selected_company);
        $this->db->group_by('tblclients.SubActGroupID');
        $CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
        $OpnBal_data->Cur_yr_OpnBal = $CurrentYrOpnBal;
        // Privius year ledger
        $last_fy = $fy - 1;
        $this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName,tblaccountbalances.FY');
        $this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
        $this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
        $this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head);
        $this->db->where('tblaccountbalances.FY', $last_fy);
        $this->db->where('tblaccountbalances.PlantID', $selected_company);
        $this->db->group_by('tblclients.SubActGroupID');
        $CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
        $OpnBal_data->Last_yr_OpnBal = $CurrentYrOpnBal;
        return $OpnBal_data;
    }
//================ Balance Sheet End =========================================

    public function gettraderlist()
    {
        $this->db->where('CustomerType', 3);
        return $this->db->get(db_prefix() .'clients')->result_array();
    }

    public function getbrokerlist()
    {
        $this->db->where('CustomerType', 2);
        return $this->db->get(db_prefix() .'clients')->result_array();
    }
    
    public function get_reporttraderbroker_data($postData)
    {
        $this->db->select('tbltrader_broker_assigned.send_from,SendFromAccounts.company AS SendFromAccountName,SendFromAccounts.CustomerType AS SendFromAccountsType,tbltrader_broker_assigned.send_to,SendToAccounts.company AS SendToAccountName,SendToAccounts.CustomerType AS SendToAccountsType');
        $this->db->join('tblclients AS SendFromAccounts', 'SendFromAccounts.AccountID = tbltrader_broker_assigned.send_from');
        $this->db->join('tblclients AS SendToAccounts', 'SendToAccounts.AccountID = tbltrader_broker_assigned.send_to');
        if($postData['reportType'] == 'broker'){
            $this->db->or_where_in('tbltrader_broker_assigned.send_from', $postData['broker']);
            $this->db->or_where_in('tbltrader_broker_assigned.send_to', $postData['broker']);
        }else if($postData['reportType'] == 'trader'){
            $this->db->or_where_in('tbltrader_broker_assigned.send_from', $postData['trader']);
            $this->db->or_where_in('tbltrader_broker_assigned.send_to', $postData['trader']);
        }
        
        return $this->db->get('tbltrader_broker_assigned')->result_array();
    }
//========================= Customer Enquiry ===================================
    
    public function getcustomerenquirylist()
	{
	    $this->db->select('tblContactsEnquiry.*');
		return $this->db->get(db_prefix().'ContactsEnquiry')->result_array();
	}
	
    
    
    //===================Expense==========================================================

	public function getexpenselist($data)
	{
		$from_date = to_sql_date($data['from_date']) . ' ' . date('00:00:00');
		$to_date = to_sql_date($data['to_date']) . ' ' . date('23:59:59');
		
		$this->db->select('tblexpenseUpdated.*, tblexpenseCategory.CategoryName, tblstaff.firstname, tblstaff.lastname');
		$this->db->from(db_prefix() . 'expenseUpdated');
		$this->db->join('tblexpenseCategory', 'tblexpenseCategory.id = tblexpenseUpdated.Category', 'left');
		$this->db->join('tblstaff', 'tblstaff.phonenumber = tblexpenseUpdated.UserID', 'left');

		// Add conditions for staff member, expense category
		if (!empty($data['Staff'])) {
			$this->db->where('tblexpenseUpdated.UserID', $data['Staff']);
		}

		if (!empty($data['Category'])) {
			$this->db->where('tblexpenseUpdated.Category', $data['Category']);
		}
		return $this->db->get()->result_array();
	}

    public function GetCategorylist()
    {
        return $this->db->get(db_prefix() .'expenseCategory')->result_array();
    }
    public function Getstafflist()
    {
        return $this->db->get(db_prefix() .'staff')->result_array();
    }
    
    public function Survey_wise_chart($filter_data)
	{
		log_message('debug', 'ReportFor: ' . $filter_data["ReportFor"] . ', Staff_Id: ' . $filter_data["Staff_Id"]);
		// Step 1: Convert to SQL-compatible date format
		$from_date = to_sql_date($filter_data['from_date']);
		$to_date = to_sql_date($filter_data['to_date']);
		
		// Step 2: Validate dates — return empty if invalid
		if (empty($filter_data["from_date"]) || empty($filter_data["to_date"])) {
			log_message('error', 'village_wise_chart: Empty from_date or to_date');
			return []; // Return empty result to prevent SQL error
		}
		
		// Step 3: Define chart color array
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
		
		$chart = [];
		
		// Step 4: Build query
		$this->db->select('COUNT(tblsurvey.id) AS TotalCount, tblsurvey.UserID, tblsurvey.state, tblsurvey.district, tblsurvey.taluka,
        tblstaff.firstname, tblstaff.lastname,
		tblxx_statelist.state_name,
		tblxx_citylist.city_name,
		tblsurvey.village,
		tblTalukaMaster.TalukaName');
		$this->db->where('tblsurvey.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblsurvey.state', 'LEFT');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblsurvey.district', 'LEFT');
		$this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblsurvey.taluka', 'LEFT');
		$this->db->join('tblstaff', 'tblstaff.staffid = tblsurvey.UserID', 'LEFT');
		//$this->db->where("DATE(tblsurvey.TransDate) BETWEEN '$from_date' AND '$to_date'");
		
		
		// $this->db->where("tblsurvey.TransDate >=", $from_date);
		// $this->db->where("tblsurvey.TransDate <=", $to_date);
		
		if ($filter_data["State"] != "") {
            $this->db->where('tblsurvey.state', $filter_data["State"]);
		}
		if ($filter_data["District"] != "") {
            $this->db->where('tblsurvey.district', $filter_data["District"]);
		}
		if ($filter_data["Taluka"] != "") {
            $this->db->where('tblsurvey.taluka', $filter_data["Taluka"]);
		}
		else if($filter_data["ReportFor"] == "1" && !empty($filter_data["Staff_Id"])) {
			$this->db->where('tblsurvey.UserID', $filter_data["Staff_Id"]);
		}
		
		if ($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "1") {
			// Group by Staff
			$this->db->group_by("tblsurvey.UserID");
			
			} else if ($filter_data["GroupBy"] == "1" && empty($filter_data["State"])) {
			// Group by State (when State filter is not selected)
			$this->db->group_by("tblsurvey.state");
			
			} else if ($filter_data["GroupBy"] == "1" && !empty($filter_data["State"]) && empty($filter_data["District"])) {
			// Group by District (when State is selected but District is not)
			$this->db->group_by("tblsurvey.district");
			
			}  else if($filter_data["GroupBy"] == "2" && empty($filter_data["District"])){ // District wise
			$this->db->group_by("tblsurvey.district");
			} else if ($filter_data["GroupBy"] == "2" && !empty($filter_data["District"]) && empty($filter_data["Taluka"])) {
			// Group by Taluka (when District selected but Taluka not selected)
			$this->db->group_by("tblsurvey.taluka");
			
			} else if ($filter_data["GroupBy"] == "2" && !empty($filter_data["Taluka"])) {
			// Group by Village (when Taluka selected)
			$this->db->group_by("tblsurvey.village"); // Use 'village' if that’s the column, not `id`
		    }else if ($filter_data["GroupBy"] == "2" && !empty($filter_data["Staff_Id"])) {
			// Group by Village (when Taluka selected)
			$this->db->group_by("tblsurvey.district"); 
			}

		
		$this->db->order_by("TotalCount","DESC");
		// Step 5: Execute query and check for errors
		$query = $this->db->get('tblsurvey');
		
		if (!$query) {
			log_message('error', 'village_wise_chart: Query failed: ' . $this->db->last_query());
			return [];
		}
		//echo $this->db->last_query();
		$result = $query->result_array();
		// echo "<pre>";
		// print_r($result);
		$totalCount = 0;
		foreach ($result as $value) {
			$totalCount += isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
		}
		
		$i = 0;
		foreach ($result as $key => $value) {
			// Determine name based on ReportFor
			if($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "1"){ // staff wise
			    $name = isset($value['firstname']) ? $value['firstname'] . "" . $value['lastname'] : 'Unknown';
			}
			else if($filter_data["GroupBy"] == "1" && empty($filter_data["State"])){ // District wise
			    $name = isset($value['city_name']) ? $value['city_name'] : 'Unknown';
			}
			else if($filter_data["GroupBy"] == "2" && empty($filter_data["District"])){ // District wise
			    $name = isset($value['city_name']) ? $value['city_name'] : 'Unknown';
				}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["District"]) && empty($filter_data["Taluka"])){ // Taluka wise
			    $name = isset($value['TalukaName']) ? $value['TalukaName'] : 'Unknown';
				}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["Taluka"])){ // Village wise
			    $name = isset($value['village']) ? $value['village'] : 'Unknown';
			}else{
			    //$name = 'Unknown last'.$filter_data["GroupBy"]." = ".$filter_data["ReportFor"];
			}

			if ($filter_data["ChartType"] !== "Pie") {
				$allcount = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
				} else {
				$count3_raw = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
				$count = ($totalCount > 0) ? round(($count3_raw / $totalCount) * 100, 2) : 0;
				$allcount = $count; // Keep decimal part
			}
			// Now build the chart array
			$chart[] = array(
			'name'  => $name,
			'y'     => $allcount,
			'color' => $color_data[$i % count($color_data)],
			'z'     => 100,
			'label' => "Qty"
			);
			$i++;
		}
		$chart_data = [
		'ChartData' => $chart,
		];
		// Step 7: Return chart data
		return $chart_data;
	}
   
   
}