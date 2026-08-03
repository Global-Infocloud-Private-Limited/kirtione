<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class K1InventoryModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		//============================ Get Root Company ================================
		public function GetRootCompany()
		{
			$this->db->select('tblrootcompany.*');
			$RootCompanyList = $this->db->get('tblrootcompany')->result_array();
			return $RootCompanyList;
		}
		//===================== Gell All Active Center List ============================
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
		//============================ Get Item Group List =============================
		public function GetItemAllGroupList()
		{
			$this->db->select('tblK1ItemSubCategory.*');
			$ItemGroupList = $this->db->get('tblK1ItemSubCategory')->result_array();
			return $ItemGroupList;
		}
		
		public function GetSubcategoryData($categoryID)
		{
		    $this->db->select('tblK1ItemSubCategory.*');
			$this->db->where('tblK1ItemSubCategory.CategoryID',$categoryID);
			$SubcategoryList = $this->db->get('tblK1ItemSubCategory')->result_array();
			return $SubcategoryList;
		}
		
		
		public function GetCategoryList()
		{
		    $this->db->select('tblK1ItemCategory.*');
			$ItemCategoryList = $this->db->get('tblK1ItemCategory')->result_array();
			return $ItemCategoryList;
		}
		
		public function GetBrandList()
		{
		    $this->db->select('tblbrands.*');
			$BrandList = $this->db->get('tblbrands')->result_array();
			return $BrandList;
		}
		
		public function GetItemGroupList($ItemGroup = "")
		{
			$this->db->select('tblK1ItemSubCategory.*');
			$this->db->where_in('tblK1ItemSubCategory.id',$ItemGroup);
			$ItemGroupList = $this->db->get('tblK1ItemSubCategory')->result_array();
			return $ItemGroupList;
		}
		
		public function GetCategoryGroupList($CategoryID = "")
		{
			$this->db->select('tblK1ItemCategory.*');
			$this->db->where_in('tblK1ItemCategory.id',$CategoryID);
			$CategoryList = $this->db->get('tblK1ItemCategory')->result_array();
			return $CategoryList;
		}
		
		public function GetBrandListdata($Brand = "")
		{
		    $this->db->select('tblbrands.*');
			$this->db->where_in('tblbrands.id',$Brand);
			$BrandList = $this->db->get('tblbrands')->result_array();
			return $BrandList;
		}
		
		public function GetItemselectedata($ItemGroup)
		{
			$this->db->select('tblproduct.*');
			$this->db->where('tblproduct.ProductID',$ItemGroup);
			$ItemList = $this->db->get('tblproduct')->row();
			return $ItemList;
		}
		
		//================== Get Godown List By CenterID ===============================
		public function GetGodownListByCenterID($CenterID)
		{
			$this->db->select('tblwarehouse.*');
			$this->db->where_in('tblwarehouse.center',$CenterID);
			$GodownList = $this->db->get('tblwarehouse')->result_array();
			return $GodownList;
		}
		//======================= Get Item List ========================================
		public function GetItemList($filterdata)
		{
			$ItemGroupArray = explode(",",$filterdata["ItemGroup"]);
			$PartyGroupArray = explode(",",$filterdata["PartyID"]);
			$this->db->select('tblproduct.*');
			$this->db->where_in('tblproduct.Subcategory',$ItemGroupArray);
			// print_r($filterdata["PartyID"]);die;
			if(!empty($filterdata["PartyID"])){
				$this->db->where_in('tblproduct.ItemFor', $filterdata["PartyID"]);
			}
			// if($filterdata["ItemGroup"]){
				// $this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
			// }
			$ProductList = $this->db->get('tblproduct')->result_array();
			return $ProductList;
		}
//==================== Get Pre stock ItemWise Center Wise data ======================================
	    public function GetPreItemWiseCenterWiseStockData($filterdata,$item_group,$day_before,$panel)
		{	
			$fy = $this->session->userdata('finacial_year'); // Fixed typo
			$selected_company = $this->session->userdata('root_company');
			$from_date = '20'.$fy.'-04-01 00:00:00';
			$to_date = $day_before.' 23:59:59';
			$this->db->select('tblK1history.TType,tblK1history.TType2,tblK1history.ItemID,tblK1history.CenterID,tblK1history.ExpDate,SUM(tblK1history.BilledQty) AS TotalQty');
			$this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where_in('tblproduct.Subcategory', $item_group_array);
			// Apply filters if they exist
			if ($filterdata["PartyID"] = "") {
				$this->db->where_in('tblK1history.PartyID', $filterdata["PartyID"]);
			}
			if (!empty($filterdata["CenterID"])) {
				$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
			}
			
			//$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			
			if($panel == "kirti"){
			    $this->db->where('tblK1history.FY', $fy);
			}
			$this->db->where('tblK1history.TransDate >=', $from_date);
			$this->db->where('tblK1history.TransDate <=', $to_date);
			 
			$this->db->group_by('tblK1history.TType,tblK1history.TType2,tblK1history.CenterID,tblK1history.ItemID');
			$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
			return $StockItemList;
		}
		
		public function GetHistoryDetilsList($CenterID=[],$PartyID=[],$ItemGroup=[],$DaysFilter)
		{
            $this->db->select('tblK1history.*,tblproduct.ProductName');
            $this->db->from('tblK1history');
            $this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID');
            
            if($CenterID != "") {
			    $this->db->where_in('tblK1history.CenterID', $CenterID);
			}
			
            if($PartyID != "") {
				$this->db->where_in('tblK1history.PartyID', $PartyID);
			}
			
			if($ItemGroup){
				$this->db->where_in('tblproduct.Subcategory', $ItemGroup);
			}
			
			if (!empty($DaysFilter)) {
                $currentDate = date('Y-m-d');
                
                $endDate = date('Y-m-d', strtotime($currentDate . ' + ' . $DaysFilter . ' days'));
               
                $this->db->where("
                    CASE
                        WHEN tblK1history.ExpDate LIKE '%/%' 
                            THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')
                        WHEN tblK1history.ExpDate LIKE '%:%' 
                            THEN DATE(tblK1history.ExpDate)
                        ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
                    END BETWEEN '$currentDate' AND '$endDate'
                ");
            }
            
            $this->db->order_by("
                CASE 
                    WHEN tblK1history.ExpDate IS NULL OR tblK1history.ExpDate = '' THEN 1 
                    ELSE 0 
                END,
                CASE
                    WHEN tblK1history.ExpDate LIKE '%/%' 
                        THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')
                    WHEN tblK1history.ExpDate LIKE '%:%' 
                        THEN DATE(tblK1history.ExpDate)
                    ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
                END
            ", "", false);

            $query = $this->db->get();
            return $query->result_array();
		}
		
		public function GetItemWiseCenterWiseStockData($filterdata,$ItemGroup,$panel)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Convert and format dates
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$item_group_array = explode(",", $ItemGroup);
			$this->db->select('tblK1history.TType,tblK1history.TType2,tblK1history.ItemID,tblK1history.CenterID,SUM(tblK1history.BilledQty) AS TotalQty');
			$this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where_in('tblproduct.Subcategory', $item_group_array);
			// Apply filters if they exist
			if ($filterdata["PartyID"] != "") {
				$this->db->where_in('tblK1history.PartyID', $filterdata["PartyID"]);
			}
			if (!empty($filterdata["CenterID"])) {
				$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
			}
			
			//$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			if($panel == "kirti"){
			    $this->db->where('tblK1history.FY', $fy);
			}
			$this->db->where('tblK1history.TransDate >=', $from_date);
			$this->db->where('tblK1history.TransDate <=', $to_date);
			 
			$this->db->group_by('tblK1history.TType,tblK1history.TType2,tblK1history.CenterID,tblK1history.ItemID');
			$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
			return $StockItemList;
		}
		
		//===================== Gell All Active Item List ============================
		public function GetItemGroupsList($ItemGroups = "")
		{
			$this->db->select('*');
			$this->db->order_by('ProductName','ASC');
			if($ItemGroups){
				$this->db->where('tblproduct.id',$ItemGroups);
			}
			// $this->db->where('status',"Y");
			$this->db->from(db_prefix() . 'product');
			return $this->db->get()->result_array();
		}
		public function GetItemGroupListbyproduct()
		{
			$this->db->select('tblproduct.*');
			// $this->db->where_in('tblproduct.Subcategory',$ItemGroupArray);
			$ProductList = $this->db->get('tblproduct')->result_array();
			return $ProductList;
		}
		public function GetsingalItemList($ItemGroup)
		{
			
			$this->db->select('tblproduct.*');
			$this->db->where('tblproduct.Subcategory',$ItemGroup);
			$ProductList = $this->db->get('tblproduct')->result_array();
			return $ProductList;
		}
//===================== Get Item Wise Transaction data =========================
	public function GetItemWiseStockData($filterdata,$panel)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		// Convert and format dates
		$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
		$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
		
		$this->db->select('DATE(tblK1history.TransDate) AS Date, 
		SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType, 
		tblK1history.TType2');
		if($panel == "kirti"){
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		}
		// Apply filters if they exist
		if ($filterdata["PartyID"]) {
			 $this->db->where_in('tblK1history.PartyID', $filterdata["PartyID"]);
		}
		
		if (!empty($filterdata["ItemID"])) {
			$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
		}
		
		if (!empty($filterdata["CenterID"])) {
			$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
		}
		
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
	    if($panel == "kirti"){
		   if(!is_admin()){
		        $this->db->where('tblK1history.FY', $fy);
		   }
		}
		$this->db->where('tblK1history.TransDate >=', $from_date);
		$this->db->where('tblK1history.TransDate <=', $to_date);
		
		$this->db->group_by('DATE(tblK1history.TransDate),tblK1history.TType,tblK1history.TType2');
		$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
		return $StockItemList;
	}
	
//===================== Get Item Wise Opening Qty data =========================
	public function GetItemOpnQty($filterdata,$panel)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalQty,tblK1stockmaster.ItemID');
		if($panel == "kirti"){
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1stockmaster.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		}
		// Apply filters if they exist
		if ($filterdata["PartyID"] = "") {
			$this->db->where_in('tblK1stockmaster.PartyID', $filterdata["PartyID"]);
		}
		if (!empty($filterdata["ItemID"])) {
			$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
		}
		if (!empty($filterdata["CenterID"])) {
			$this->db->where_in('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		}
		$this->db->where('tblK1stockmaster.FY', $fy);
		// if($panel == "kirti"){
		//     if(!is_admin()){
		//         $this->db->where('tblK1stockmaster.FY', $fy);
		//     }
		// }
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID');
		$StockItemList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		return $StockItemList;
	}
//===================== Get Item Wise Center wise Opening Qty data =============
	public function GetItemWiseCenterWiseOpnQty($filterdata,$panel)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalQty,tblK1stockmaster.ItemID,tblK1stockmaster.CenterID');
		// Apply filters if they exist
		if (!empty($filterdata["PartyID"])) {
            $this->db->where_in('tblK1stockmaster.PartyID', $filterdata["PartyID"]);
        }
        
		if (!empty($filterdata["ItemID"])) {
			$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
		}
		if (!empty($filterdata["CenterID"])) {
			$this->db->where_in('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		}
		// if($panel == "kirti"){
		        $this->db->where('tblK1stockmaster.FY', $fy);
		// }
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID,tblK1stockmaster.CenterID');
		$StockItemList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		// print_r($StockItemList); exit;
		return $StockItemList;
	}
	
	
	    
		//======================== 	All Item List ==========================================	
		public function GetAllItemList($filterdata)
		{ 
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblproduct.*');
			/*if(!empty($filterdata["PartyID"])){
				$this->db->where_in('tblproduct.ItemFor', $filterdata["PartyID"]);
			}*/
			
			if (!empty($filterdata["PartyID"]) && !in_array('KASPL', $filterdata["PartyID"])) {
                $this->db->where_in('tblproduct.ItemFor', $filterdata["PartyID"]);
            }
            
			if($filterdata["ItemGroup"]){
				$this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
			}
			$ItemList = $this->db->get(db_prefix() . 'product')->result_array();
			return $ItemList;
		}
//====================== ItemWise Opening Qty ==================================
	
	public function GetItemWiseOpningQty($filterdata,$panel)
	{ 
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		
		$PartyID = $filterdata["PartyID"]; 
		$CenterID = $filterdata["CenterID"]; 
		$ItemGroup = $filterdata["ItemGroup"];
		
		$this->db->select('tblK1stockmaster.ItemID,SUM(tblK1stockmaster.OQty) AS TotalOpnQty');
		
		$this->db->join('tblproduct','tblproduct.ProductID = tblK1stockmaster.ItemID');
		if($panel == "kirti"){
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1stockmaster.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		}
		if($filterdata["PartyID"]){
			$this->db->where_in('tblproduct.ItemFor', $filterdata["PartyID"]);
		}
		if($filterdata["ItemGroup"]){
			$this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
		}
		if($filterdata["CenterID"]){
			$this->db->where_in('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		}
		if($panel=="kirti"){
		    if(!is_admin()){
		        $this->db->where('tblK1stockmaster.FY', $fy);
		    }else{
				$this->db->where('tblK1stockmaster.FY', $fy);
			}
		}
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		
		$this->db->group_by('tblK1stockmaster.ItemID');
		$ItemWiseOpnQtyList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		return $ItemWiseOpnQtyList;
	}
	//================= As On Date stock Data ==================================
	
	public function GetASOndateStockData($filterdata,$panel)
	{ 
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		//$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00'; 
		// Convert and format dates
		$from_date = '20'.$fy.'-04-01 00:00:00';
		$to_date = to_sql_date($filterdata["from_date"]).' 23:59:59';
		$PartyID = $filterdata["PartyID"]; 
		$CenterID = $filterdata["CenterID"]; 
		$ItemGroup = $filterdata["ItemGroup"];
		
		$this->db->select('tblK1history.TType,tblK1history.TType2,tblK1history.CenterID,tblCenterMaster.CenterName,
		tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty');
		
		$this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblK1history.CenterID','left');
		if($panel == "kirti"){
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		}
		/*if($filterdata["PartyID"]){
			$this->db->where_in('tblproduct.ItemFor', $filterdata["PartyID"]);
		}*/
		if($filterdata["ItemGroup"]){
			$this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
		}
		if($filterdata["CenterID"]){
			$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
		}
		if($filterdata["PartyID"]){
			$this->db->where_in('tblK1history.PartyID', $filterdata["PartyID"]);
		}
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		if($panel == "kirti"){
		   if(!is_admin()){
		      $this->db->where('tblK1history.FY', $fy);
		   }
		}
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.TransDate >=', $from_date);
		$this->db->where('tblK1history.TransDate <=', $to_date);
		
		$this->db->group_by('tblK1history.ItemID,tblK1history.CenterID,tblK1history.TType,tblK1history.TType2');
		$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
		return $StockItemList;
	}

	public function FilterAsondateStockReport($filterdata, $panel = 'kirti')
	{
			$fy               = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$UserID           = $this->session->userdata('username');

			$from_date = '20' . $fy . '-04-01 00:00:00';
			$to_date   = to_sql_date($filterdata["from_date"]) . ' 23:59:59';

			// Opening stock subquery
			$openingQuery = "
				SELECT 
						ItemID,
						CenterID,
						SUM(OQty) AS OpeningQty
				FROM tblK1stockmaster
				WHERE PlantID = " . $this->db->escape($selected_company) . "
				AND FY = " . $this->db->escape($fy);
			if (!empty($filterdata["CenterID"])) {
				$centerIDs = array_map([$this->db, 'escape'], $filterdata["CenterID"]);

				$openingQuery .= "
						AND CenterID IN (" . implode(',', $centerIDs) . ") 
						GROUP BY ItemID, CenterID
					";
			}else{
				$openingQuery .= " GROUP BY ItemID";
			}

			$this->db->select("
					p.ProductID AS ItemID,
					p.ProductName AS ItemName,
					p.unit AS UOM,
					p.PackingQty,

					(
							COALESCE(op.OpeningQty, 0)
							+ SUM(CASE WHEN h.TType = 'I' AND h.TType2 = 'INWARD' THEN h.BilledQty ELSE 0 END)
							+ SUM(CASE WHEN h.TType = 'P' AND h.TType2 = 'Purchase' THEN h.BilledQty ELSE 0 END)
							- SUM(CASE WHEN h.TType = 'P' AND h.TType2 = 'PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
							- SUM(CASE WHEN h.TType = 'O' AND h.TType2 = 'SALE' THEN h.BilledQty ELSE 0 END)
							+ SUM(CASE WHEN h.TType = 'SR' AND h.TType2 = 'FRESH RETURN' THEN h.BilledQty ELSE 0 END)
							+ SUM(CASE WHEN h.TType = 'T' AND h.TType2 = 'IN' THEN h.BilledQty ELSE 0 END)
							- SUM(CASE WHEN h.TType = 'T' AND h.TType2 = 'OUT' THEN h.BilledQty ELSE 0 END)
							- SUM(CASE WHEN h.TType = 'L' AND h.TType2 = 'LIENMARK' THEN h.BilledQty ELSE 0 END)
							- SUM(CASE WHEN h.TType = 'X' THEN h.BilledQty ELSE 0 END)
					) AS Qty
			");

			$this->db->from('tblproduct p');

			// Opening stock
			$this->db->join(
					"($openingQuery) op",
					'op.ItemID = p.ProductID',
					'left'
			);

			// Transaction history
			$this->db->join(
					'tblK1history h',
					"h.ItemID = p.ProductID
					AND h.PlantID = " . $this->db->escape($selected_company) . "
					AND h.FY = " . $this->db->escape($fy) . "
					AND h.TransDate >= " . $this->db->escape($from_date) . "
					AND h.TransDate <= " . $this->db->escape($to_date) . "
					AND h.BillID IS NOT NULL
					AND h.TransID IS NOT NULL",
					'left'
			);

			// Staff center restriction
			if ($panel == "kirti" && !is_admin()) {

					$this->db->join(
							'tblstaff_wise_center swc',
							'swc.CenterID = h.CenterID',
							'inner'
					);

					$this->db->where('swc.AccountID', $UserID);
					$this->db->where('h.FY', $fy);
			}

			// Filters
			if (!empty($filterdata["PartyID"])) {
					$this->db->where_in('h.PartyID', $filterdata["PartyID"]);
			}

			if (!empty($filterdata["ItemGroup"])) {
					$this->db->where_in('p.Subcategory', $filterdata["ItemGroup"]);
			}

			if (!empty($filterdata["CenterID"])) {
					$this->db->where_in('h.CenterID', $filterdata["CenterID"]);
			}

			$this->db->group_by('p.ProductID');

			// Only available stock
			$this->db->having('Qty !=', 0);

			$this->db->order_by('p.id', 'ASC');

			return $this->db->get()->result_array();
	
			// $query = $this->db->get_compiled_select();

			// echo '<pre>';
			// print_r($query);
			// exit;
	}
	
	public function GetPreItemWiseStockData($filterdata, $day_before,$panel)
	{
	    $UserID = $this->session->userdata('username');
		$fy = $this->session->userdata('finacial_year'); // Fixed typo
		$selected_company = $this->session->userdata('root_company');
		$from_date = '20'.$fy.'-04-01 00:00:00';
		$to_date = $day_before.' 23:59:59';
		// Ensure date is in correct format
		$query_date = date('Y-m-d', strtotime($day_before));
		
		$this->db->select('
		DATE(tblK1history.TransDate) AS Date, 
		SUM(tblK1history.BilledQty) AS TotalQty, 
		tblK1history.TType, 
		tblK1history.TType2
		');
		$this->db->from(db_prefix() . 'K1history');
		if($panel== "kirti"){
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		}
		// Conditional filters
		if ($filterdata["PartyID"]) {
			 $this->db->where_in('tblK1history.PartyID', $filterdata["PartyID"]);
		}
		
		if (!empty($filterdata["ItemID"])) {
			$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
		}
		
		if (!empty($filterdata["CenterID"])) {
			$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
		}
		
		// Fixed conditions
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
	    $this->db->where('tblK1history.FY', $fy);
	    //   if($panel== "kirti"){
		//     if(!is_admin()){
		//         $this->db->where('tblK1history.FY', $fy);
		//     }
		// }
		$this->db->where('tblK1history.TransDate >=', $from_date);
		$this->db->where('tblK1history.TransDate <=', $to_date);
		
		// Group by
		$this->db->group_by(array('DATE(tblK1history.TransDate)', 'tblK1history.TType', 'tblK1history.TType2'));
		
		$query = $this->db->get();
		return $query->result_array();
	}
		
		public function GetPartyList($PartyIDs)
		{
			$this->db->select('tblclients.*');
			 $this->db->where('tblclients.IsKirtiOneAccess',"Y");
			//$this->db->where_in('tblclients.AccountID',$PartyIDs);
			$this->db->where_in('tblclients.AccountID',$PartyIDs);
			// $this->db->order_by('tblclients.active','1');
			return $this->db->get('tblclients')->result_array();
		}
		
	//===============================  VENDOR AS ON STOCK REPORT 	==========================
	//======================== 	All Item List ================================================
	
		public function GetAllItemList_vendor($filterdata, $LogInUser)
		{ 
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblproduct.*');
			if($LogInUser){
				$this->db->where('tblproduct.UserId', $LogInUser);
			}
			if($filterdata["ItemGroup"]){
				$this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
			}
			$ItemList = $this->db->get(db_prefix() . 'product')->result_array();
			return $ItemList;
		}
		//================= As On Date stock Data ==================================
		
		public function GetASOndateStockData_vendor($filterdata, $LogInUser)
		{ 
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			//$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00'; 
			// Convert and format dates
			$from_date = '2025-04-01 00:00:00';
			$to_date = to_sql_date($filterdata["from_date"]).' 23:59:59';
			$PartyID = $filterdata["PartyID"]; 
			$CenterID = $filterdata["CenterID"]; 
			$ItemGroup = $filterdata["ItemGroup"];
			
			$this->db->select('tblK1history.TType,tblK1history.TType2,
			tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty');
			
			$this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID');
			if($LogInUser){
				$this->db->where('tblproduct.UserId', $LogInUser);
				$this->db->where('tblK1history.UserId', $LogInUser);
			}
			if($filterdata["ItemGroup"]){
				$this->db->where_in('tblproduct.Subcategory', $filterdata["ItemGroup"]);
			}
			if($filterdata["CenterID"]){
				$this->db->where_in('tblK1history.CenterID', $filterdata["CenterID"]);
			}
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.TransDate >=', $from_date);
			$this->db->where('tblK1history.TransDate <=', $to_date);
			
			$this->db->group_by('tblK1history.ItemID,tblK1history.TType,tblK1history.TType2');
			$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
			return $StockItemList;
		}
	
        public function GetFilterwiseCommisionData($filterdata)
		{
		    $this->db->select('tblCommisionMaster.*, tblCenterMaster.CenterName, tblproduct.ProductName,tblproduct.rate, tblclients.company');
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
		
		public function GetSalableList($CenterID, $ItemID, $ReportBy, $FromDate, $ToDate)
        {
            if (!empty($FromDate)) {
                $FromDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $FromDate)));
            }
            if (!empty($ToDate)) {
                $ToDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $ToDate)));
            }
        
            $this->db->select('
                tblK1history.ItemID,
                tblproduct.ProductName,
                tblproduct.PackingQty,
                SUM(tblK1history.BilledQty) AS TotalBilledQty,
                SUM(tblK1history.NetOrderAmt) AS TotalNetOrderAmt
            ');
            $this->db->from('tblK1history');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
           
            $this->db->where('tblK1history.TType', 'O');
            $this->db->where('tblK1history.TType2', 'SALE');
           
            if (!empty($FromDate) && !empty($ToDate)) {
                $this->db->where('tblK1history.TransDate >=', $FromDate);
                $this->db->where('tblK1history.TransDate <=', $ToDate);
            }
            
            if (!empty($CenterID)) {
                $this->db->where('tblK1history.CenterID', $CenterID);
            }
            
            if (!empty($ItemID)) {
                $this->db->where('tblproduct.Subcategory', $ItemID);
            }
        
            $this->db->group_by('tblK1history.ItemID');
        
            if ($ReportBy == 'Qty') {
                $this->db->order_by('TotalBilledQty', 'DESC');
            } else {
                $this->db->order_by('TotalNetOrderAmt', 'DESC');
            }
        
            return $this->db->get()->result_array();
        }
        
        public function GetProfitableList($CenterID,$Brand,$CategoryID,$ItemID,$FromDate,$ToDate)
        {
            if (!empty($FromDate)) {
                $FromDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $FromDate)));
            }
            if (!empty($ToDate)) {
                $ToDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $ToDate)));
            }
            
            /* ----------------------------------------------------
               QUERY 1 : UNIQUE ITEM LIST (GROUP BY ItemID)
            ---------------------------------------------------- */
        
            $this->db->select('
                tblK1history.ItemID,
                tblproduct.ProductName,
                tblbrands.BrandName,
                tblK1ItemSubCategory.SubCategoryName,
                tblK1ItemCategory.SubcategoryName AS CategoryName,
            ');
            $this->db->from('tblK1history');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
            $this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId', 'left');
            $this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory', 'left');
             $this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category', 'left');
           
            $this->db->where('tblK1history.TType', 'O');
            $this->db->where('tblK1history.TType2', 'SALE');
           
            if (!empty($FromDate) && !empty($ToDate)) {
                $this->db->where('tblK1history.TransDate >=', $FromDate);
                $this->db->where('tblK1history.TransDate <=', $ToDate);
            }
            
            if (!empty($CenterID)) {
                $this->db->where('tblK1history.CenterID', $CenterID);
            }
            
            if (!empty($Brand)) {
                $this->db->where('tblproduct.BrandId', $Brand);
            }
            
            if (!empty($CategoryID)) {
                $this->db->where('tblproduct.Category', $CategoryID);
            }
            
            if (!empty($ItemID)) {
                $this->db->where('tblproduct.Subcategory', $ItemID);
            }
        
            $this->db->group_by('tblK1history.ItemID');
            $UniqueItemList = $this->db->get()->result_array();
            
            /* ----------------------------------------------------
               QUERY 2 : GROUP BY ItemID + BatchNo
            ---------------------------------------------------- */
            
            $this->db->select('
                tblK1history.ItemID,
                tblK1history.BatchNo,
                tblproduct.ProductName
            ');
            $this->db->from('tblK1history');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
            
            $this->db->where('tblK1history.TType', 'O');
            $this->db->where('tblK1history.TType2', 'SALE');
            
            if (!empty($FromDate) && !empty($ToDate)) {
                $this->db->where('tblK1history.TransDate >=', $FromDate);
                $this->db->where('tblK1history.TransDate <=', $ToDate);
            }
            
            if (!empty($CenterID)) {
                $this->db->where('tblK1history.CenterID', $CenterID);
            }
            
            if (!empty($ItemID)) {
                $this->db->where('tblproduct.Subcategory', $ItemID);
            }
            
            $this->db->group_by(['tblK1history.ItemID', 'tblK1history.BatchNo']);
            $UniqueItem_BatchList = $this->db->get()->result_array();
           
            /* ----------------------------------------------------
               QUERY 3 Sale List : GROUP BY ItemID + BatchNo + PurchRate
            ---------------------------------------------------- */
            
            $this->db->select('
                tblK1history.ItemID,
                tblK1history.BatchNo,
                tblK1history.PurchRate,
                SUM(tblK1history.BilledQty) AS TotalQty,
                tblproduct.ProductName
            ');
            $this->db->from('tblK1history');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
            
            $this->db->where('tblK1history.TType', 'O');
            $this->db->where('tblK1history.TType2', 'SALE');
            
            if (!empty($FromDate) && !empty($ToDate)) {
                $this->db->where('tblK1history.TransDate >=', $FromDate);
                $this->db->where('tblK1history.TransDate <=', $ToDate);
            }
            
            if (!empty($CenterID)) {
                $this->db->where('tblK1history.CenterID', $CenterID);
            }
            
            if (!empty($ItemID)) {
                $this->db->where('tblproduct.Subcategory', $ItemID);
            }
            
            $this->db->group_by(['tblK1history.ItemID', 'tblK1history.BatchNo', 'tblK1history.PurchRate']);
            $SaleListItem_Batch_PurchRateList = $this->db->get()->result_array();
           
            /* ----------------------------------------------------
               QUERY 4 Purchase List : GROUP BY ItemID + BatchNo + PurchRate
            ---------------------------------------------------- */
            
           $this->db->select('
                tblK1history.ItemID,
                SUM(tblK1history.BilledQty) AS TotalQty,
                SUM(tblK1history.NetChallanAmt) AS TotalAmt,
                tblproduct.ProductName
            ');
                        
            $this->db->from('tblK1history');
            $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
            
            $this->db->where('tblK1history.TType', 'P');
            $this->db->where('tblK1history.TType2', 'Purchase');
            
            $this->db->group_by([
                'tblK1history.ItemID',
            ]);
            $PurchaseListItem_Batch_PurchRateList = $this->db->get()->result_array();
           
            foreach($UniqueItemList as &$Item)
            {
                $TotalSaleAmt = 0;
                $TotalPurchAmt = 0;
                $TotalSaleQty = 0;
                
                $TotalPurchaseValue = 0;   
                $TotalPurchaseQty   = 0;   
                
                foreach($UniqueItem_BatchList as $ItemBatch)
                {
                    if ($ItemBatch['ItemID'] != $Item['ItemID']) {
                        continue;
                    }
                    $SaleAmt = 0;
                    $PurchAmt = 0;
                    $SaleQty = 0;
                    $AveragePurchRate = 0;
                    
                    //sale array
                    foreach($SaleListItem_Batch_PurchRateList as $ItemBatchPurchRate)
                    {
                        if($ItemBatchPurchRate['ItemID'] == $ItemBatch['ItemID'] && $ItemBatchPurchRate['BatchNo'] == $ItemBatch['BatchNo'])
                        {
                            $SaleAmt += $ItemBatchPurchRate['TotalQty'] * $ItemBatchPurchRate['PurchRate'];
                            
                            $SaleQty += $ItemBatchPurchRate['TotalQty'];
                        }
                    }
                    $TotalSaleAmt += $SaleAmt;
                    $TotalSaleQty += $SaleQty;
                    
                    //purch array
                    foreach ($PurchaseListItem_Batch_PurchRateList as $purch)
                    {
                        if ($purch['ItemID'] == $ItemBatch['ItemID'] )
                        {
                            if ($purch['TotalQty'] > 0)
                            {
                                $TotalPurchaseValue += $purch['TotalAmt']; 
                                $TotalPurchaseQty   += $purch['TotalQty']; 
                                
                                $AveragePurchRate = $purch['TotalAmt'] / $purch['TotalQty'];
                            }
                        }
                    }
                    
                    $PurchAmt = $SaleQty * $AveragePurchRate;
                    $TotalPurchAmt += $PurchAmt;
                }
                
                $FinalAvgRate = ($TotalPurchaseQty > 0)
                    ? ($TotalPurchaseValue / $TotalPurchaseQty)
                    : 0;
                
                $Item['SaleAmt'] = $TotalSaleAmt;
                $Item['PurchAmt'] = $TotalPurchAmt;
                $Item['SaleQty'] = $TotalSaleQty;
                $Item['AvgRate']  = $FinalAvgRate;
            }
            
            return $UniqueItemList;
        }
		
		
	}				