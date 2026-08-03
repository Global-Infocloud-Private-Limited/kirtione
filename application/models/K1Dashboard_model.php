<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class K1Dashboard_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
//=========== New Code =========================================================

//======================= Get All Sale Brand List ==============================
    public function GetSaleBrandList($Type)
	{
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblbrands.id,tblbrands.BrandName');
		$this->db->from('tblK1history');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId');
		if($Type == "Sale"){
		    $this->db->where_in('tblK1history.TType', ["O","S"]);
		    $this->db->where_in('tblK1history.TType2', ['SALE']);
		}else{
		    $this->db->where_in('tblK1history.TType', ["P"]);
		    $this->db->where_in('tblK1history.TType2', ['Purchase']);
		}
		$this->db->where('tblK1history.FY', $fy);
		$this->db->group_by('tblproduct.BrandId');
		$query = $this->db->get(); // ADD table name here!
		$result = $query->result_array();
		return $result;
	}
//======================= Get All Sale Category List ==============================
    public function GetCategoryList($Type)
	{
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1ItemCategory.id,tblK1ItemCategory.SubcategoryName');
		$this->db->from('tblK1history');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
		if($Type == "Sale"){
		    $this->db->where_in('tblK1history.TType', ["O","S"]);
		    $this->db->where_in('tblK1history.TType2', ['SALE']);
		}else{
		    $this->db->where_in('tblK1history.TType', ["P"]);
		    $this->db->where_in('tblK1history.TType2', ['Purchase']);
		}
		
		$this->db->where('tblK1history.FY', $fy);
		$this->db->group_by('tblproduct.Category');
		$query = $this->db->get(); // ADD table name here!
		$result = $query->result_array();
		return $result;
	}
	
	//================================  Sale chart Start ===========================	
	public function GetSaleChartData($FilterData) 
	{
		// Define chart colors
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
		
		$chart = [];
		
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName,
		tblK1history.ItemID,
		tblK1ordermaster.VillageName,
		tblproduct.ProductName,
		SUM(tblK1history.ChallanAmt) AS TotalAmt
		');
		$this->db->from('tblK1history');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		$this->db->join('tblK1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where_in('tblK1history.TType', ['S','O']);
		$this->db->where('tblK1history.TType2', "SALE");
		if($FilterData["SalesBrandList"]){
		    $this->db->where('tblproduct.BrandId', $FilterData["SalesBrandList"]);
		}
		if($FilterData["SalesCategoryList"]){
		    $this->db->where('tblproduct.Category', $FilterData["SalesCategoryList"]);
		}
		if($FilterData["FilterType"] == "CENTERWISE"){
		    $this->db->group_by('tblK1history.CenterID');
		    $this->db->order_by('tblCenterMaster.CenterName');
		}elseif($FilterData["FilterType"] == "ITEMWISE"){
		    $this->db->group_by('tblK1history.ItemID');
		    $this->db->order_by('tblproduct.ProductName');
		}elseif($FilterData["FilterType"] == "VILLAGEWISE"){
		    $this->db->group_by('tblK1ordermaster.VillageName');
		    $this->db->order_by('tblK1ordermaster.VillageName');
		}
		$query = $this->db->get();
		$result = $query->result_array();
		if($FilterData["SalesResultCount"] !="All"){
		    usort($result, function($a, $b) {
                return $b['TotalAmt'] <=> $a['TotalAmt']; // descending
            });
            $topresult = array_slice($result, 0, $FilterData["SalesResultCount"]);
		}else{
		    $topresult = $result;
		}
        
		$TotalAmount = 0;
		foreach ($topresult as $value) {
			$TotalAmount += isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
		}
		
		$i = 0;
		foreach ($topresult as $key => $value) {
			if ($FilterData["SalesChartType"] !== "pie") {
				$allcount = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
			} else {
				$count3_raw = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
				$count = ($TotalAmount > 0) ? round(($count3_raw / $TotalAmount) * 100, 2) : 0;
				$allcount = $count; // Keep decimal part
			}
			if($FilterData["FilterType"] == "CENTERWISE"){
			    $Name = $value['CenterName'];
			    $NameID = $value['CenterID'];
			}elseif($FilterData["FilterType"] == "ITEMWISE"){
			    $Name = $value['ProductName'];
			    $NameID = $value['ItemID'];
			}elseif($FilterData["FilterType"] == "VILLAGEWISE"){
			    $Name = $value['VillageName'];
			    $NameID = $value['VillageName'];
			}
			$chart[] = array(
			'NameID' => $NameID,
			'name'  => $Name,
			'y'     => $allcount,
			'Amt'     => $value['TotalAmt'],
			'color' => $color_data[$i % count($color_data)],
			'z'     => 100,
			'label' => "Qty"
			);
			$i++;
		}
		
		$chart_data = [
		    'ChartData' => $chart,
		];
		return $chart_data;
	}
	
	public function SalesdataCenterAndVillageWise($filter_data) 
	{
	    $fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName,
		tblK1history.ItemID,
		tblproduct.ProductName,
		tblK1ordermaster.VillageName,
		SUM(tblK1history.ChallanAmt) AS TotalAmt
		');
		$this->db->from('tblK1history');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		$this->db->join('tblK1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where_in('tblK1history.TType', ['S','O']);
		$this->db->where('tblK1history.TType2', "SALE");
		if($filter_data["SalesBrandList"]){
		    $this->db->where('tblproduct.BrandId', $filter_data["SalesBrandList"]);
		}
		if($filter_data["SalesCategoryList"]){
		    $this->db->where('tblproduct.Category', $filter_data["SalesCategoryList"]);
		}
		if($filter_data["FilterType"] == "CENTERWISE"){
		    $this->db->group_by('tblK1history.CenterID,tblK1ordermaster.VillageName');
		}elseif($filter_data["FilterType"] == "ITEMWISE"){
		    $this->db->group_by('tblK1history.ItemID,tblK1history.CenterID');
		}elseif($filter_data["FilterType"] == "VILLAGEWISE"){
		    $this->db->group_by('tblK1ordermaster.VillageName,tblK1history.ItemID');
		}   
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
// ===========================  Purchase chart Data load =======================	
	public function GetPurchaseChartData($FilterData) 
	{
		// Define chart colors
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
		
		$chart = [];
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName,
		tblK1history.ItemID,
		tblproduct.ProductName,
		SUM(tblK1history.ChallanAmt) AS TotalAmt
		');
		$this->db->from('tblK1history');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where_in('tblK1history.TType', ['P']);
		$this->db->where('tblK1history.TType2', "Purchase");
		if($FilterData["PurchBrandList"]){
		    $this->db->where('tblproduct.BrandId', $FilterData["PurchBrandList"]);
		}
		if($FilterData["PurchCategoryList"]){
		    $this->db->where('tblproduct.Category', $FilterData["PurchCategoryList"]);
		}
		if($FilterData["FilterType"] == "CENTERWISE"){
		    $this->db->group_by('tblK1history.CenterID');
		    $this->db->order_by('tblCenterMaster.CenterName');
		}elseif($FilterData["FilterType"] == "ITEMWISE"){
		    $this->db->group_by('tblK1history.ItemID');
		    $this->db->order_by('tblproduct.ProductName');
		}
		$query = $this->db->get();
		$result = $query->result_array();
		if($FilterData["PurchResultCount"] !="All"){
		    usort($result, function($a, $b) {
                return $b['TotalAmt'] <=> $a['TotalAmt']; // descending
            });
            $topresult = array_slice($result, 0, $FilterData["PurchResultCount"]);
		}else{
		    $topresult = $result;
		}
        
		$TotalAmount = 0;
		foreach ($topresult as $value) {
			$TotalAmount += isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
		}
		
		$i = 0;
		foreach ($topresult as $key => $value) {
			if ($FilterData["PurchChartType"] !== "pie") {
				$allcount = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
			} else {
				$count3_raw = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
				$count = ($TotalAmount > 0) ? round(($count3_raw / $TotalAmount) * 100, 2) : 0;
				$allcount = $count; // Keep decimal part
			}
			if($FilterData["FilterType"] == "CENTERWISE"){
			    $Name = $value['CenterName'];
			    $NameID = $value['CenterID'];
			}elseif($FilterData["FilterType"] == "ITEMWISE"){
			    $Name = $value['ProductName'];
			    $NameID = $value['ItemID'];
			}
			$chart[] = array(
    			'NameID' => $NameID,
    			'name'  => $Name,
    			'y'     => $allcount,
    			'Amt'     => $value['TotalAmt'],
    			'color' => $color_data[$i % count($color_data)],
    			'z'     => 100,
    			'label' => "Qty"
			);
			$i++;
		}
		
		$chart_data = [
		    'ChartData' => $chart,
		];
		return $chart_data;
	}
	
	public function PurchdataCenterAndItemIDWise($filter_data) 
	{
	    $fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName,
		tblK1history.ItemID,
		tblproduct.ProductName,
		SUM(tblK1history.ChallanAmt) AS TotalAmt
		');
		$this->db->from('tblK1history');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where_in('tblK1history.TType', ['P']);
		$this->db->where('tblK1history.TType2', "Purchase");
		if($filter_data["PurchBrandList"]){
		    $this->db->where('tblproduct.BrandId', $filter_data["PurchBrandList"]);
		}
		if($filter_data["PurchCategoryList"]){
		    $this->db->where('tblproduct.Category', $filter_data["PurchCategoryList"]);
		}
		if($filter_data["FilterType"] == "CENTERWISE"){
		    $this->db->group_by('tblK1history.CenterID,tblK1history.ItemID');
		}elseif($filter_data["FilterType"] == "ITEMWISE"){
		    $this->db->group_by('tblK1history.ItemID,tblK1history.CenterID');
		} 
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
//======================== High Stock Chart Data Load ==========================
	public function GetHighStockChartDataLoad($filter_data) 
	{
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
		
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('tblK1history.CenterID,tblK1history.TType,tblK1history.TType2,tblK1history.ItemID,tblproduct.ProductName,
		SUM(tblK1history.BilledQty) AS TotalQty,tblCenterMaster.CenterName');
		$this->db->from('tblK1history');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		
		if(!empty($filter_data['HighStockBrandList']))
		{
		    $this->db->where('tblproduct.BrandId', $filter_data['HighStockBrandList']);
		} 
		
		if(!empty($filter_data['HighStockCategoryList']))
		{
		    $this->db->where('tblproduct.Category', $filter_data['HighStockCategoryList']);
		}
		
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.PlantID', $selected_company);
		//$this->db->where('tblK1history.ItemID', $ProductID);
		$this->db->group_by(['tblK1history.ItemID','tblK1history.CenterID','tblK1history.TType', 'tblK1history.TType2']);
		$query = $this->db->get();
		
		$rows = $query->result_array();
		
		$UniqueItems = [];
		foreach ($rows as $row) {
			$productID = $row['ItemID'];
			if (!isset($UniqueItems[$productID])) {
				$UniqueItems[$productID] = [];
			}
			$UniqueItems[$productID][] = $row;
		}
		$UniqueCenterID = [];
		foreach ($rows as $row) {
			$CenterID = $row['CenterID'];
			if (!isset($UniqueCenterID[$CenterID])) {
				$UniqueCenterID[$CenterID] = [];
			}
			$UniqueCenterID[$CenterID] = $CenterID;
		}
		
		$result = [];
		foreach ($UniqueItems as $productID => $transactions) {
			$opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
			$SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
			$AdjQty = 0; $InQty = 0; $OutQty = 0;
			
			foreach ($transactions as $row) {
				if ($row["TType"] == "O" && $row["TType2"] == "SALE") {
					$SaleQty += $row["TotalQty"];
				} elseif ($row["TType"] == "P" && $row["TType2"] == "Purchase") {
					$PurchQty += $row["TotalQty"];
				} elseif ($row["TType"] == "T" && $row["TType2"] == "IN") {
					$InQty += $row["TotalQty"];
				} elseif ($row["TType"] == "T" && $row["TType2"] == "OUT") {
					$OutQty += $row["TotalQty"];
				} elseif ($row["TType"] == "I" && $row["TType2"] == "INWARD") {
					$InwardQty += $row["TotalQty"];
				}
			}
			
			$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			
			$result[] = [
			    'ProductID' => $productID,
			    'ProductName' => $row['ProductName'],
			    'BalanceQty' => (float) number_format($BalQty, 2, '.', '')
			];
		}
		if($FilterData["HighStockResultCount"] !="All"){
		    // Sort and slice top 5
    		usort($result, function($a, $b) {
                return $b['BalanceQty'] <=> $a['BalanceQty']; // descending
            });
            $ItemWiseStock =  array_slice($result, 0, $filter_data["HighStockResultCount"]);
		}else{
		    $ItemWiseStock = $result;
		}
		
		$TotalQty = 0;
		foreach ($ItemWiseStock as $value) {
			$TotalQty += isset($value['BalanceQty']) ? (int)$value['BalanceQty'] : 0;
		}
		
		$i = 0;
		foreach ($ItemWiseStock as $key => $value) {
			if ($filter_data["HighStockChartType"] != "pie") {
				$allcount = isset($value['BalanceQty']) ? (int)$value['BalanceQty'] : 0;
			} else {
				$count3_raw = isset($value['BalanceQty']) ? (int)$value['BalanceQty'] : 0;
				$count = ($TotalQty > 0) ? round(($count3_raw / $TotalQty) * 100, 2) : 0;
				$allcount = $count; // Keep decimal part
			}
			$Name = $value['ProductName'];
			$NameID = $value['ProductID'];
			$chart[] = array(
    			'NameID' => $NameID,
    			'name'  => $Name,
    			'y'     => $allcount,
    			'Qty'     => $value['BalanceQty'],
    			'color' => $color_data[$i % count($color_data)],
    			'z'     => 100,
    			'label' => "Qty"
			);
			$i++;
		}
		
		$chart_data = [
		    'ChartData' => $chart,
		    'TransactionData' => $rows,
		    'UniqueCenterID'=>$UniqueCenterID
		];
		return $chart_data;
		
	
		// Total for Pie %
		$totalCount = 0;
		foreach ($top5 as $value) {
			$totalCount += $value['BalanceQty'];
			
		}
		
		$chart = [];
		$i = 0;
		foreach ($top5 as $item) {
			$value = $filter_data['ChartType'] !== "Pie" 
			? $item['BalanceQty'] 
			: ($totalCount > 0 ? round(($item['BalanceQty'] / $totalCount) * 100, 2) : 0);
			
			// Make sure CenterID is defined; if not available in `$item`, you must add it earlier in your logic
			//$CenterID = isset($item['CenterID']) ? $item['CenterID'] : null;
			
			$chart[] = [
			'ProductID' => $item['ProductID'],
			'name'      => $item['ProductName'],
			'y'         => $value,
			'color'     => $color_data[$i % count($color_data)],
			'z'         => 100
			];
			$i++;
		}
		
		return ['ChartData' => $chart];
	}
//==================== Sale Vs Purchase Report Chart ===========================
	public function SaleVsPurchaseChartReport($FilterData) 
	{
	    if (isset($FilterData['SalesPurchFilterType']) && $FilterData['SalesPurchFilterType'] == "ITEMWISE") 
        {
            return $this->SaleVsPurchaseChartReport_ItemWise($FilterData);
        }
        
		$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
		'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
		'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
		
		$chart = [];
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select('
		tblCenterMaster.CenterID,
		tblCenterMaster.CenterName,
		tblK1history.TType,
		tblK1history.TType2,
		SUM(tblK1history.ChallanAmt) AS TotalAmt
		');
		$this->db->from('tblK1history');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		
		if(!empty($FilterData['SalesPurchBrandList']))
		{
		    $this->db->where('tblproduct.BrandId', $FilterData['SalesPurchBrandList']);
		} 
		
		if(!empty($FilterData['SalesPurchCategoryList']))
		{
		    $this->db->where('tblproduct.Category', $FilterData['SalesPurchCategoryList']);
		}
		
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where_in('tblK1history.TType', ['S', 'O', 'P']);
		$this->db->where_in('tblK1history.TType2', ['SALE', 'Purchase']);
		$this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
		$this->db->order_by('tblCenterMaster.CenterName');
		
		$query = $this->db->get();
		$result = $query->result();
		
		$centerData = [];
		foreach ($result as $row) {
			$centerID = $row->CenterID;
			$centerName = $row->CenterName;
			$type2 = $row->TType2;
			$amount = (float) $row->TotalAmt;
			
			if (!isset($centerData[$centerID])) {
				$centerData[$centerID] = [
				'CenterID' => $centerID,
				'CenterName' => $centerName,
				'SALE' => 0,
				'Purchase' => 0
				];
			}
			
			$centerData[$centerID][$type2] += $amount;
		}
		
		$centerList = array_values($centerData);
        usort($centerList, function($a, $b) {
            return $b['Purchase'] <=> $a['Purchase']; 
        });
    
        
        if ($FilterData["SalesPurchResultCount"] != "All") {
            $centerList = array_slice($centerList, 0, $FilterData["SalesPurchResultCount"]);
        }
		
		$i = 0;
		foreach ($centerList as $data) {
			$chart[] = [ 
			'name' => $data['CenterName'],
			'data' => [
            [
			'name' => 'Sale',
			'y' => $data['SALE'],
			'color'=>'#00e272'
			    //'color' => $color_data[$i % count($color_data)],
            ],
            [
			'name' => 'Purchase',
			'y' => $data['Purchase'],
			'color'=>'#fe6a35'
			//'color' => $color_data[($i + 1) % count($color_data)],
            ]
			]
			];
			$i++;
		}
		
		$chart_data = [
		'ChartData' => $chart,
		];
		return $chart_data; // for Highcharts or AJAX use
	}
	
	public function SaleVsPurchaseChartReport_ItemWise($FilterData)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
    
        $this->db->select('
            tblproduct.ProductID,
            tblproduct.ProductName,
            tblK1history.TType2,
            SUM(tblK1history.ChallanAmt) AS TotalAmt
        ');
        $this->db->from('tblK1history');
        $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
    
        if (!empty($FilterData['SalesPurchBrandList'])) {
            $this->db->where('tblproduct.BrandId', $FilterData['SalesPurchBrandList']);
        }
    
        if (!empty($FilterData['SalesPurchCategoryList'])) {
            $this->db->where('tblproduct.Category', $FilterData['SalesPurchCategoryList']);
        }

        $this->db->where('tblK1history.PlantID', $selected_company);
        $this->db->where('tblK1history.FY', $fy);
        $this->db->where_in('tblK1history.TType', ['S','O','P']);
        $this->db->where_in('tblK1history.TType2', ['SALE','Purchase']);
        $this->db->group_by(['tblproduct.ProductID','tblK1history.TType2']);
        $this->db->order_by('tblproduct.ProductName');
    
        $rows = $this->db->get()->result();
    
        $itemData = [];
        foreach ($rows as $row) {
    
            if (!isset($itemData[$row->ProductID])) {
                $itemData[$row->ProductID] = [
                    'ProductID' => $row->ProductID,
                    'ProductName' => $row->ProductName,
                    'SALE' => 0,
                    'Purchase' => 0,
                    'Total' => 0
                ];
            }
    
            if ($row->TType2 == "SALE")
                $itemData[$row->ProductID]['SALE'] += $row->TotalAmt;
            else
                $itemData[$row->ProductID]['Purchase'] += $row->TotalAmt;
    
            $itemData[$row->ProductID]['Total'] =
                $itemData[$row->ProductID]['SALE'] + $itemData[$row->ProductID]['Purchase'];
        }

        if ($FilterData["SalesPurchResultCount"] != "All") {
            $itemList = array_values($itemData);
            
            usort($itemList, function($a, $b) {
                return $b['Purchase'] <=> $a['Purchase'];
            });
        
            $itemList = array_slice($itemList, 0, $FilterData["SalesPurchResultCount"]);
        } else {
            $itemList = array_values($itemData);
        }
    
        $chart = [];
        foreach ($itemList as $item) {
            $chart[] = [
                'name' => $item['ProductName'],
                'data' => [
                    ['name'=>'Sale','y'=>$item['SALE'],'color'=>'#00e272'],
                    ['name'=>'Purchase','y'=>$item['Purchase'],'color'=>'#fe6a35']
                ]
            ];
        }
    
        return ['ChartData' => $chart];
    }
	
//==================== New Code End ============================================
		public function getAllvillageCount(){
			return $this->db->count_all('tblvillagedetails');	
		}
		
		public function getAllProductItemCount(){
			$this->db->where('isactive', 'Y');
			$this->db->from('tblproduct');
			return $this->db->count_all_results();	
		}
		
		public function getPurchaseAmountCount(){
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->where_in('tblK1history.TType', ['P']);
			$this->db->where_in('tblK1history.TType2', ['Purchase']);
			// $this->db->select('SUM(ChallanAmt) AS total_amount');
			// $this->db->from('tblhistory');
			// $this->db->where('TType', 'P');
			// $this->db->where('TType2', 'Purchase');
			$this->db->where('FY', $fy);
			
			$query = $this->db->get('tblK1history'); // ADD table name here!
			
			if ($query && $query->num_rows() > 0) {
				$result = $query->row();
				$formatted_total = number_format($result->TotalAmt, 2);
				return $formatted_total;
				} else {
				return number_format(0, 2); // default fallback value
			}
		}
		
		public function getSaleAmountCount() {
			$fy = $this->session->userdata('finacial_year');
			
			$this->db->select('
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->where_in('tblK1history.TType', ['S','O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->where('FY', $fy);
			
			$query = $this->db->get('tblK1history'); // ADD table name here!
			
			if ($query && $query->num_rows() > 0) {
				$result = $query->row();
				$formatted_total = number_format($result->TotalAmt, 2);
				return $formatted_total;
				} else {
				return number_format(0, 2); // default fallback value
			}
		}
		
		public function get_center_wise_summary() {
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where_in('tblK1history.TType', ['S','O','P']);
			$this->db->where_in('tblK1history.TType2', ['SALE', 'Purchase']);
			$this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
			$this->db->order_by('tblCenterMaster.CenterName');
			
			$query = $this->db->get();
			return $result = $query->result();
			
		}
		
		
		
		
		
		public function get_top5Selling_item(){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where_in('tblK1history.TType', ['S','O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->group_by('tblproduct.ProductID');
			$this->db->order_by('TotalAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			return $result = $query->result();
			
		}
		public function get_top5Purchase_item(){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName as Pu_name,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalPuAmt');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where_in('tblK1history.TType', ['P']);
			$this->db->where_in('tblK1history.TType2', ['Purchase']);
			$this->db->group_by('tblproduct.ProductID');
			$this->db->order_by('TotalPuAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			return $result = $query->result();
			
		} 
		// ===========================  Sale chart Start    ========================	
		public function Sale_wise_chart($filter_data) {
			// Define chart colors
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where_in('tblK1history.TType', ['S','O']);
			$this->db->where('tblK1history.TType2', "SALE");
			$this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
			$this->db->order_by('tblCenterMaster.CenterName');
			
			$query = $this->db->get();
			
			$result = $query->result_array();
			
			$TotalAmount = 0;
			foreach ($result as $value) {
				$TotalAmount += isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
			}
			
			$i = 0;
			foreach ($result as $key => $value) {
				if ($filter_data["ChartType"] !== "Pie") {
					$allcount = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					} else {
					$count3_raw = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					$count = ($TotalAmount > 0) ? round(($count3_raw / $TotalAmount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				
				$name = $value['CenterName'];
				$CenterID = $value['CenterID'];
				// $allcount = (float)$value['TotalAmt'];
				
				$chart[] = array(
				'CenterID' => $CenterID,
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
			return $chart_data;
		}
		
		public function VillageWisedatabyCenterID($CenterID, $filter_data)
		{
			
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblK1ordermaster.VillageName,
			SUM(tblK1history.ChallanAmt) AS TotalSaleAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->join('tblK1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where_in('tblK1history.TType', ['S','O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->where('tblK1history.CenterID', $CenterID);
			$this->db->group_by([
			'tblK1ordermaster.VillageName'
			]);
			
			$query = $this->db->get();
			$result = $query->result_array();
			$i = 0;
			$TotalAmount = 0;
			foreach ($result as $value) {
				$TotalAmount += isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
			}
			
			foreach ($result as $key => $value) {
				if ($filter_data !== "Pie") {
					$allcount = isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
					} else {
					$count3_raw = isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
					$count = ($TotalAmount > 0) ? round(($count3_raw / $TotalAmount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				
				$name = $value['VillageName'];
				$VillageName = $value['VillageName'];
				$CenterID = $value['CenterID'];
				// $allcount = (float)$value['TotalSaleAmt'];
				
				$chart[] = array(
				'CenterID' => $CenterID,
				'VillageName' => $VillageName,
				'name'      => $name,
				'y'         => $allcount,
				'color'     => $color_data[$i % count($color_data)],
				'z'         => 100,
				'label'     => "Qty"
				);
				$i++;
			}
			
			$chart_data = [
			'ChartData' => $chart,
			];
			return $chart_data;
		}
		
		public function ItemWiseSaleByVillage($VillageName, $CenterID, $filter_data)
		{
			
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblK1ordermaster.VillageName,
			SUM(tblK1history.ChallanAmt) AS TotalSaleAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->join('tblK1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where_in('tblK1history.TType', ['S','O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->where('tblK1history.CenterID', $CenterID);
			$this->db->where('tblK1ordermaster.VillageName', $VillageName);
			$this->db->group_by([
			'tblK1history.ItemID'
			]);
			
			$query = $this->db->get();
			$result = $query->result_array();
			$i = 0;
			$TotalAmount = 0;
			foreach ($result as $value) {
				$TotalAmount += isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
			}
			
			foreach ($result as $key => $value) {
				if ($filter_data !== "Pie") {
					$allcount = isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
				} else {
					$count3_raw = isset($value['TotalSaleAmt']) ? (int)$value['TotalSaleAmt'] : 0;
					$count = ($TotalAmount > 0) ? round(($count3_raw / $TotalAmount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				
				$name = $value['ProductName'];
				$VillageName = $value['VillageName'];
				// $allcount = (float)$value['TotalSaleAmt'];
				
				$chart[] = array(
				'name'      => $name,
				'y'         => $allcount,
				'color'     => $color_data[$i % count($color_data)],
				'z'         => 100,
				'label'     => "Qty"
				);
				$i++;
			}
			
			$chart_data = [
			'ChartData' => $chart,
			];
			return $chart_data;
		}
		
		
		// ===========================  End chart Start    ========================	
		
		// ===========================  Purchase chart Start    ========================	
		public function Purchase_wise_chart($filter_data) {
			// Define chart colors
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.TType', "P");
			$this->db->where('tblK1history.TType2', "Purchase");
			$this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
			$this->db->order_by('tblCenterMaster.CenterName');
			
			$query = $this->db->get();
			
			$result = $query->result_array();
			
			$i = 0;
			$totalCount = 0;
			foreach ($result as $value) {
				$totalCount += isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
			}
			
			foreach ($result as $key => $value) {
				if ($filter_data["ChartType"] !== "Pie") {
					$allcount = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					} else {
					$count3_raw = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					$count = ($totalCount > 0) ? round(($count3_raw / $totalCount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				
				$name = $value['CenterName'];
				$CenterID = $value['CenterID'];
				// $allcount = (float)$value['TotalAmt'];
				
				$chart[] = array(
				'CenterID' => $CenterID,
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
			return $chart_data;
		}
		
		public function get_item_purchases_by_center($CenterID, $filter_data)
		{
			
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblK1history.TType,
			tblK1history.TType2,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.TType', "P");
			$this->db->where('tblK1history.TType2', "Purchase");
			$this->db->where('tblK1history.CenterID', $CenterID);
			$this->db->group_by([
			'tblK1history.ItemID', 
			'tblK1history.TType', 
			'tblK1history.TType2',
			'tblCenterMaster.CenterID',
			'tblCenterMaster.CenterName',
			'tblproduct.ProductID',
			'tblproduct.ProductName'
			]);
			
			$query = $this->db->get();
			$result = $query->result_array();
			$i = 0;
			$totalCount = 0;
			foreach ($result as $value) {
				$totalCount += isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
			}
			
			foreach ($result as $key => $value) {
				if ($filter_data !== "Pie") {
					$allcount = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					} else {
					$count3_raw = isset($value['TotalAmt']) ? (int)$value['TotalAmt'] : 0;
					$count = ($totalCount > 0) ? round(($count3_raw / $totalCount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				
				$name = $value['ProductName'];
				$ProductID = $value['ProductID'];
				// $allcount = (float)$value['TotalAmt'];
				
				$chart[] = array(
				'ProductID' => $ProductID,
				'name'      => $name,
				'y'         => $allcount,
				'color'     => $color_data[$i % count($color_data)],
				'z'         => 100,
				'label'     => "Qty"
				);
				$i++;
			}
			
			$chart_data = [
			'ChartData' => $chart,
			];
			return $chart_data;
		}
		// ===========================  Purchase chart End    ========================		
		// ============================ Top	5 stock Get ==============================
		public function get_Top5HighStockItem() {
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Get all transactions grouped by ItemID, TType, TType2
			$this->db->select('tblK1history.TType, tblK1history.TType2, tblK1history.ItemID, tblproduct.ProductName, SUM(tblK1history.BilledQty) AS TotalQty');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.FY', $fy);
			$this->db->group_by('tblK1history.ItemID, tblproduct.ProductName, tblK1history.TType, tblK1history.TType2');
			$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
			
			
			
			// Group data by ItemID
			$grouped = [];
			foreach ($StockItemList as $row) {
				$itemID = $row['ItemID'];
				if (!isset($grouped[$itemID])) {
					$grouped[$itemID] = [
					'ProductName' => $row['ProductName'],
					'ItemID' => $itemID,
					'Qty' => []
					];
				}
				$grouped[$itemID]['Qty'][] = $row;
			}
			
			// Calculate BalQty per item
			$result = [];
			foreach ($grouped as $itemID => $data) {
				$opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
				$SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
				$AdjQty = 0; $InQty = 0; $OutQty = 0;
				
				foreach ($data['Qty'] as $row) {
					$qty = (float) $row['TotalQty'];
					$TType = $row['TType'];
					$TType2 = strtoupper($row['TType2']);
					
					if ($TType == "O" && $TType2 == "SALE") {
						$SaleQty += $qty;
						} elseif ($TType == "P" && $TType2 == "PURCHASE") {
						$PurchQty += $qty;
						} elseif ($TType == "T" && $TType2 == "IN") {
						$InQty += $qty;
						} elseif ($TType == "T" && $TType2 == "OUT") {
						$OutQty += $qty;
						} elseif ($TType == "I" && $TType2 == "INWARD") {
						$InwardQty += $qty;
						} elseif ($TType == "P" && $TType2 == "PURCHASERTN") {
						$PurchRtnQty += $qty;
						} elseif ($TType == "O" && $TType2 == "SALERTN") {
						$SaleRtnQty += $qty;
						} elseif ($TType == "I" && $TType2 == "ISSUE") {
						$IssueQty += $qty;
						} elseif ($TType == "A") {
						$AdjQty += $qty;
						} elseif ($TType == "M") {
						$PrdQty += $qty;
					}
				}
				
				$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				
				$result[] = [
				'ItemID' => $itemID,
				'ProductName' => $data['ProductName'],
				'BalanceQty' => round($BalQty, 2)
				];
			}
			
			// Sort by BalanceQty descending and return top 5
			usort($result, function($a, $b) {
				return $b['BalanceQty'] <=> $a['BalanceQty'];
			});
			
			return array_slice($result, 0, 5);
		}
		
		
		
		
		// public function CenterWiseStockChart($ChartType, $ProductID) {
		// // Define colors for chart
		// $color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
		// '#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a',
		// '#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70',
		// '#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
		// ];
		
		// $fy = $this->session->userdata('finacial_year');
		// $selected_company = $this->session->userdata('root_company');
		
		// // Fetch relevant transactions for the item and company
		// $this->db->select('
		// tblCenterMaster.CenterID,
		// tblCenterMaster.CenterName,
		// tblK1history.TType,
		// tblK1history.TType2,
		// tblK1history.ItemID,
		// SUM(tblK1history.BilledQty) AS TotalQty
		// ');
		// $this->db->from('tblK1history');
		// $this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
		// $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
		// $this->db->where('tblK1history.FY', $fy);
		// $this->db->where('tblK1history.BillID IS NOT NULL');
		// $this->db->where('tblK1history.TransID IS NOT NULL');
		// $this->db->where('tblK1history.PlantID', $selected_company);
		// $this->db->where('tblK1history.ItemID', $ProductID);
		// $this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
		// $query = $this->db->get();
		// // echo $this->db->last_query();
		// $rows = $query->result_array();
		
		// // Create unique center list
		// $centerList = [];
		// foreach ($rows as $row) {
		// $centerList[] = [
		// "CenterID" => $row['CenterID'],
		// "CenterName" => $row['CenterName'],
		// "TotalQty" => 0
		// ];
		// }
		
		// $centerList = array_map("unserialize", array_unique(array_map("serialize", $centerList)));
		
		// // Calculate stock quantity per center
		// foreach ($centerList as $key => $val) {
		// $opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
		// $SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
		// $AdjQty = 0; $InQty = 0; $OutQty = 0; $BalQty = 0;
		
		// foreach ($rows as $row) {
		
		// if ($val["CenterID"] == $row['CenterID'] && $row["TType"] == "O" && $row["TType2"] == "SALE") {
		// $SaleQty += $row["TotalQty"];
		// } elseif ($val["CenterID"] == $row['CenterID'] && $row["TType"] == "P" && $row["TType2"] == "Purchase") {
		// $PurchQty += $row["TotalQty"];
		// } elseif ($val["CenterID"] == $row['CenterID'] && $row["TType"] == "T" && $row["TType2"] == "IN") {
		// $InQty += $row["TotalQty"];
		// } elseif ($val["CenterID"] == $row['CenterID'] && $row["TType"] == "T" && $row["TType2"] == "OUT") {
		// $OutQty += $row["TotalQty"];
		// } elseif ($val["CenterID"] == $row['CenterID'] && $row["TType"] == "I" && $row["TType2"] == "INWARD") {
		// $InwardQty += $row["TotalQty"];
		// }
		
		
		// }
		
		// $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
		// $centerList[$key]["StockQty"] = $BalQty;
		// // echo "<pre>";
		// // print_r($centerList[$key]["StockQty"] = $BalQty);
		// }
		
		// $chart = [];
		// $i = 0;
		// $totalQty = 0;
		
		
		// if ($ChartType === "Pie") {
		// foreach ($centerList as $value) {
		// $totalQty += $value['StockQty']; 
		
		
		// }
		
		// }
		
		// foreach ($centerList as $item) {
		// $Qty = (float)$item['StockQty'];
		
		
		
		
		// $value = ($ChartType !== "Pie")
		// ? $Qty
		// : ($totalQty > 0 ? round(($Qty / $totalQty) * 100, 2) : 0);
		
		
		
		// $chart[] = [
		// 'name'  => $item['CenterName'],
		// 'y'     => $value,
		// 'color' => $color_data[$i % count($color_data)],
		// 'z'     => 100
		// ];
		// $i++;
		 
		// }
		// return ['ChartData' => $chart];
		// }
		
		public function CenterWiseStockChart($ChartType, $ProductID)
		{
			// Define chart colors
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a',
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70',
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
			];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Step 1: Fetch raw transaction data
			$this->db->select('
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			tblK1history.TType,
			tblK1history.TType2,
			tblK1history.ItemID,
			SUM(tblK1history.BilledQty) AS TotalQty
			');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.ItemID', $ProductID);
			$this->db->group_by(['tblK1history.CenterID', 'tblK1history.TType', 'tblK1history.TType2']);
			$query = $this->db->get();
			$rows = $query->result_array();
			
			// echo "<h3>--- Raw Transaction Rows ---</h3><pre>";
			// print_r($rows);
			
			// Step 2: Build unique center list
			$centerMap = [];
			foreach ($rows as $row) {
				$cid = $row['CenterID'];
				if (!isset($centerMap[$cid])) {
					$centerMap[$cid] = [
					"CenterID" => $cid,
					"CenterName" => $row['CenterName'],
					"StockQty" => 0
					];
				}
			}
			
			$centerList = array_values($centerMap); // Reindex
			// echo "<h3>--- Unique Center List ---</h3><pre>";
			// print_r($centerList);
			
			// Step 3: Calculate stock per center
			$totalStock = 0;
			foreach ($centerList as $key => $val) {
				$cid = $val["CenterID"];
				$opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
				$SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
				$AdjQty = 0; $InQty = 0; $OutQty = 0;
				
				foreach ($rows as $row) {
					if ($row['CenterID'] != $cid) continue;
					
					$ttype = $row["TType"];
					$ttype2 = $row["TType2"];
					$qty = $row["TotalQty"];
					
					if ($ttype == "O" && $ttype2 == "SALE") {
						$SaleQty += $qty;
						} elseif ($ttype == "P" && $ttype2 == "Purchase") {
						$PurchQty += $qty;
						} elseif ($ttype == "T" && $ttype2 == "IN") {
						$InQty += $qty;
						} elseif ($ttype == "T" && $ttype2 == "OUT") {
						$OutQty += $qty;
						} elseif ($ttype == "I" && $ttype2 == "INWARD") {
						$InwardQty += $qty;
					}
				}
				
				$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				$centerList[$key]["StockQty"] = $BalQty;
				$totalStock += $BalQty;
			//echo "<br>";	
				// echo "<h4>Center: {$val['CenterName']} (ID: $cid)</h4>";
				// echo "<pre>";
				// print_r([
				// 'Purchase' => $PurchQty,
				// 'Inward' => $InwardQty,
				// 'Sale' => $SaleQty,
				// 'In' => $InQty,
				// 'Out' => $OutQty,
				// 'Balance' => $BalQty
				// ]);
			}
			
			$chart = [];
			$i = 0;
			$runningPercentage = 0;
			$lastIndex = count($centerList) - 1;
			
			foreach ($centerList as $index => $item) {
				$Qty = (float)$item['StockQty'];
				$percentage = ($totalStock > 0) ? round(($Qty / $totalStock) * 100,0) : 0;
				
				if ($ChartType === "Pie") {
					// Use rounded percentage but fix last item to make 100%
					if ($index < $lastIndex) {
						$rounded = round($percentage, 2);
						$runningPercentage += $rounded;
						} else {
						// Adjust last item
						$rounded = round(100 - $runningPercentage, 2);
					}
					$value = $rounded;
					} else {
					$value = $Qty;
					$rounded = round($percentage, 2); // for display purposes
				}
				
				$chart[] = [
				'name' => $item['CenterName'],
				'y' => $value,
				'color' => $color_data[$i % count($color_data)],
				'z' => 100,
				'actual_qty' => $Qty,
				'percentage' => round($percentage, 2)  // Store original % too
				];
				$i++;
			}
			// echo "<h3>--- Final Chart Data ---</h3><pre>";
			// print_r($chart);
			
			// echo "<h3>Total Stock: $totalStock</h3>";
			
			return [
			'ChartData' => $chart,
			'TotalStock' => $totalStock
			];
		}
		
		
		// ===========================================================================
		// ========================== Top 5 Selling Item  Chart Report =============== 
		
		public function Top5HighSellingItemChartReport($filter_data) {
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
			];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Fetch Top 5 Products by Sales Amount
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where_in('tblK1history.TType', ['S', 'O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->group_by('tblproduct.ProductID');
			$this->db->order_by('TotalAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			$result = $query->result();
			
			$chart = [];
			$totalAmount = 0;
			$i = 0;
			
			if ($filter_data['ChartType'] === "Pie") {
				foreach ($result as $row) {
					$totalAmount += (float)$row->TotalAmt;
				}
			}
			
			// 2. Prepare chart data
			foreach ($result as $row) {
				$amount = (float)$row->TotalAmt;
				
				$value = $filter_data['ChartType'] === "Pie"
				? ($totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0)
				: $amount;
				
				
				$chart[] = [
				'ProductID' => $row->ProductID,
				'name'      => $row->ProductName,
				'y'         => $value,
				'color'     => $color_data[$i % count($color_data)],
				'z'         => 100
				];
				$i++;
			}
			
			return ['ChartData' => $chart];
		}
		
		public function ItemWiseSaleCenterChart($ChartType, $ProductID)
		{
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a',
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70',
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
			];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where_in('tblK1history.TType', ['S', 'O']);
			$this->db->where_in('tblK1history.TType2', ['SALE']);
			$this->db->where('tblK1history.ItemID', $ProductID);
			$this->db->group_by('tblK1history.CenterID');
			$this->db->order_by('TotalAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			$result = $query->result_array();
			
			$chart = [];
			$i = 0;
			$totalAmount = 0;
			
			if (strtolower($ChartType) === "pie") {
				foreach ($result as $row) {
					$totalAmount += $row['TotalAmt'];
				}
			}
			
			foreach ($result as $row) {
				$value = (strtolower($ChartType) === "pie")
				? (($totalAmount > 0) ? round(($row['TotalAmt'] / $totalAmount) * 100, 2) : 0)
				: floatval($row['TotalAmt']);
				
				$chart[] = [
				'name'  => $row['CenterName'],
				'y'     => $value,
				'color' => $color_data[$i % count($color_data)],
				'ProductID' => $row['ProductID'] // for drilldown if needed
				];
				$i++;
			}
			
			return ['ChartData' => $chart];
		}
		
		// ========================== Top 5 Purchase Item  Chart Report =============== 
		
		public function Top5PurchaseItemChartReport($filter_data) {
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
			];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Fetch Top 5 Products by Sales Amount
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.TType', "P");
			$this->db->where('tblK1history.TType2', "Purchase");
			$this->db->group_by('tblproduct.ProductID');
			$this->db->order_by('TotalAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			$result = $query->result();
			
			$totalAmount = 0;
			$i = 0;
			
			if ($filter_data['ChartType'] === "Pie") {
				foreach ($result as $row) {
					$totalAmount += (float)$row->TotalAmt;
				}
			}
			
			// 2. Prepare chart data
			foreach ($result as $row) {
				$amount = (float)$row->TotalAmt;
				
				$value = $filter_data['ChartType'] === "Pie"
				? ($totalAmount > 0 ? round(($amount / $totalAmount) * 100, 2) : 0)
				: $amount;
				
				
				$chart[] = [
				'ProductID' => $row->ProductID,
				'name'      => $row->ProductName,
				'y'         => $value,
				'color'     => $color_data[$i % count($color_data)],
				'z'         => 100
				];
				$i++;
			}
			
			return ['ChartData' => $chart];
		}
		
		public function ItemWisePurchaseCenterChart($ChartType, $ProductID)
		{
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a',
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70',
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'
			];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('
			tblproduct.ProductID,
			tblproduct.ProductName,
			tblCenterMaster.CenterID,
			tblCenterMaster.CenterName,
			SUM(tblK1history.ChallanAmt) AS TotalAmt
			');
			$this->db->from('tblK1history');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.TType', "P");
			$this->db->where('tblK1history.TType2', "Purchase");
			$this->db->where('tblK1history.ItemID', $ProductID);
			$this->db->group_by('tblK1history.CenterID');
			$this->db->order_by('TotalAmt', 'DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			$result = $query->result_array();
			
			$chart = [];
			$i = 0;
			$totalAmount = 0;
			
			if (strtolower($ChartType) === "pie") {
				foreach ($result as $row) {
					$totalAmount += $row['TotalAmt'];
				}
			}
			
			foreach ($result as $row) {
				$value = (strtolower($ChartType) === "pie")
				? (($totalAmount > 0) ? round(($row['TotalAmt'] / $totalAmount) * 100, 2) : 0)
				: floatval($row['TotalAmt']);
				
				$chart[] = [
				'name'  => $row['CenterName'],
				'y'     => $value,
				'color' => $color_data[$i % count($color_data)],
				'ProductID' => $row['ProductID'] // for drilldown if needed
				];
				$i++;
			}
			
			return ['ChartData' => $chart];
		}
		
		
	}																																	