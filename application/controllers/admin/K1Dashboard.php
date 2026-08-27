<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class K1Dashboard extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('K1Dashboard_model');
		}
		
//========================= Dashboard PAge Load ================================
	public function index()
	{	
	    if (!has_permission_new('K1Dashboard', '', 'view')) {
			access_denied('purchase order');
		}   
		$Type = "Sale";
		$data['BrandList'] = $this->K1Dashboard_model->GetSaleBrandList($Type);
		$data['CategoryList'] = $this->K1Dashboard_model->GetCategoryList($Type);
		$Type = "Purchase";
		$data['PurchBrandList'] = $this->K1Dashboard_model->GetSaleBrandList($Type);
		$data['PurchCategoryList'] = $this->K1Dashboard_model->GetCategoryList($Type);
		$data['Total_village'] = $this->K1Dashboard_model->getAllvillageCount(); 
		$data['Total_productItem'] = $this->K1Dashboard_model->getAllProductItemCount();
		$data['Total_purchaseAmount'] = $this->K1Dashboard_model->getPurchaseAmountCount();
		$data['Total_saleAmount'] = $this->K1Dashboard_model->getSaleAmountCount();
		$data['summary'] = $this->K1Dashboard_model->get_center_wise_summary();
		$data['Top5SellingItem'] = $this->K1Dashboard_model->get_top5Selling_item();
		$data['Top5PurchaseItem'] = $this->K1Dashboard_model->get_top5Purchase_item(); 
		$data['Top5HighStockItem'] = $this->K1Dashboard_model->get_Top5HighStockItem();
		$this->load->view('admin/K1Dashboard/K1DashboardNew',$data);
	}
	
//=============================== Sale Chart Load ==============================
    public function SaleChartDataLoad()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data) {
            $data = $this->input->post();
        }
        $SalesResultCount   = isset($data['SalesResultCount']) ? $data['SalesResultCount'] : 'All';
        $FilterType  = isset($data['FilterType']) ? strtoupper($data['FilterType']) : 'CENTERWISE';
        $FilterData = array(
            "SalesResultCount" =>$SalesResultCount,
            "FilterType"=>$FilterType,
            "SalesChartType"=>$data['SalesChartType'],
            "SalesBrandList"=>$data['SalesBrandList'],
            "SalesCategoryList"=>$data['SalesCategoryList']
        );
        
        
        $CenterID    = isset($data['CenterID']) ? $data['CenterID'] : null;
        $VillageName = isset($data['VillageName']) ? $data['VillageName'] : null;
        
        $result = $this->K1Dashboard_model->GetSaleChartData($FilterData);
        $SalesdataCenterAndVillageWise = $this->K1Dashboard_model->SalesdataCenterAndVillageWise($FilterData);
        
        $finalData = [];
        $SecondLayerData = [];
        if (!empty($result['ChartData'])) {
            foreach ($result['ChartData'] as $Val) {
                $NameID     = $Val['NameID'];
                $Name   = $Val['name'] ?? $Val['name'] ?? 'Unknown Center';
                $TotalAmount = (float)($Val['y'] ?? $Val['y'] ?? 0);
                $Name2 = "";
                $SecondArray = [];
                foreach($SalesdataCenterAndVillageWise as $Key2=>$Val2){
                    if($FilterType == "CENTERWISE"){
                        $NameID2 = $Val2['CenterID'];
                    }elseif($FilterType == "ITEMWISE"){
                        $NameID2 = $Val2['ItemID'];
                    }elseif($FilterType == "VILLAGEWISE"){
                        $NameID2 = $Val2['VillageName'];
                    }
                    //$NameID2 = $Val2['CenterID'];
                    if($NameID == $NameID2){
                        if($FilterType == "CENTERWISE"){
                            $Name2   = $Val2['VillageName'] ?? 'Unknown Village';
                        }elseif($FilterType == "ITEMWISE"){
                            $Name2   = $Val2['CenterName'] ?? 'Unknown Village';
                        }elseif($FilterType == "VILLAGEWISE"){
                            $Name2   = $Val2['ProductName'] ?? 'Unknown Village';
                        }
                        if ($data['SalesChartType'] != "pie") {
            				$Amount2 = (float)($Val2['TotalAmt'] ?? 0);
            			} else {
            			    $Amount1 = (float)($Val2['TotalAmt'] ?? 0);
            			    $AmtPer = ($Val['Amt'] > 0) ? round(($Amount1 / $Val['Amt']) * 100, 2) : 0;
            			    $Amount2 = $AmtPer;
            			}
                        //$Amount2 = (float)($Val2['TotalAmt'] ?? 0);
                        $SecondArray[] = [$Name2,$Amount2];
                    }
                }
                if($SalesResultCount !="All"){
                    usort($SecondArray, function($a, $b) {
                        return $b[1] <=> $a[1]; // descending
                    });
                    $topresult = array_slice($SecondArray, 0, $SalesResultCount);
                }else{
                    $topresult = $SecondArray;
                }
                
                $SecondLayerData[] = [
                    "name"=>$Name2,
                    "id"=>$NameID,
                    "data"=>$topresult
                ];
                $finalData[] = [
                    'name'    => $Name,
                    'y'        => $TotalAmount,
                    'drilldown'=>$NameID,
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['ChartData' => $finalData,"SecondLayerData"=>$SecondLayerData,"dd"=>$SalesdataCenterAndVillageWise]);
    }
//======================== Purchase Chart Data Load ============================
    public function PurchaseChartDataLoad()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data) {
            $data = $this->input->post();
        }
        $PurchResultCount   = isset($data['PurchResultCount']) ? $data['PurchResultCount'] : 'All';
        $FilterType  = isset($data['FilterType']) ? strtoupper($data['FilterType']) : 'CENTERWISE';
        $FilterData = array(
            "PurchResultCount" =>$PurchResultCount,
            "FilterType"=>$FilterType,
            "PurchChartType"=>$data['PurchChartType'],
            "PurchBrandList"=>$data['PurchBrandList'],
            "PurchCategoryList"=>$data['PurchCategoryList']
        );
        
        
        $CenterID    = isset($data['CenterID']) ? $data['CenterID'] : null;
        $VillageName = isset($data['VillageName']) ? $data['VillageName'] : null;
        
        $result = $this->K1Dashboard_model->GetPurchaseChartData($FilterData);
        $PurchdataCenterAndItemIDWise = $this->K1Dashboard_model->PurchdataCenterAndItemIDWise($FilterData);
        
        $finalData = [];
        $SecondLayerData = [];
        if (!empty($result['ChartData'])) {
            foreach ($result['ChartData'] as $Val) {
                $NameID     = $Val['NameID'];
                $Name   = $Val['name'] ?? $Val['name'] ?? 'Unknown Center';
                $TotalAmount = (float)($Val['y'] ?? $Val['y'] ?? 0);
                $Name2 = "";
                $SecondArray = [];
                foreach($PurchdataCenterAndItemIDWise as $Key2=>$Val2){
                    if($FilterType == "CENTERWISE"){
                        $NameID2 = $Val2['CenterID'];
                    }elseif($FilterType == "ITEMWISE"){
                        $NameID2 = $Val2['ItemID'];
                    }
                    //$NameID2 = $Val2['CenterID'];
                    if($NameID == $NameID2){
                        if($FilterType == "CENTERWISE"){
                            $Name2   = $Val2['ProductName'] ?? 'Unknown Item';
                        }elseif($FilterType == "ITEMWISE"){
                            $Name2   = $Val2['CenterName'] ?? 'Unknown Center';
                        }
                        if ($data['PurchChartType'] != "pie") {
            				$Amount2 = (float)($Val2['TotalAmt'] ?? 0);
            			} else {
            			    $Amount1 = (float)($Val2['TotalAmt'] ?? 0);
            			    $AmtPer = ($Val['Amt'] > 0) ? round(($Amount1 / $Val['Amt']) * 100, 2) : 0;
            			    $Amount2 = $AmtPer;
            			}
                        $SecondArray[] = [$Name2,$Amount2];
                    }
                }
                if($PurchResultCount !="All"){
                    usort($SecondArray, function($a, $b) {
                        return $b[1] <=> $a[1]; // descending
                    });
                    $topresult = array_slice($SecondArray, 0, $PurchResultCount);
                }else{
                    $topresult = $SecondArray;
                }
                
                $SecondLayerData[] = [
                    "name"=>$Name2,
                    "id"=>$NameID,
                    "data"=>$topresult
                ];
                $finalData[] = [
                    'name'    => $Name,
                    'y'        => $TotalAmount,
                    'drilldown'=>$NameID,
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['ChartData' => $finalData,"SecondLayerData"=>$SecondLayerData,"dd"=>$SalesdataCenterAndVillageWise]);
    }
//======================== High Stock Chart Data Load ==========================
    public function HighStockChartDataLoad()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data) {
            $data = $this->input->post();
        }
        $HighStockResultCount   = isset($data['HighStockResultCount']) ? $data['HighStockResultCount'] : 'All';
        $FilterType  = isset($data['FilterType']) ? strtoupper($data['FilterType']) : 'CENTERWISE';
        $FilterData = array(
            "HighStockResultCount" =>$HighStockResultCount,
            "HighStockChartType"=>$data['HighStockChartType'],
            "HighStockBrandList"=>$data['HighStockBrandList'],
            "HighStockCategoryList"=>$data['HighStockCategoryList']
        );
        $ItemWiseTransaction = $this->K1Dashboard_model->GetHighStockChartDataLoad($FilterData);
        //$ItemAndCenterWiseTransaction = $this->K1Dashboard_model->PurchdataCenterAndItemIDWise($FilterData);
        
        $finalData = [];
        $SecondLayerData = [];
        if (!empty($ItemWiseTransaction['ChartData'])) {
            foreach ($ItemWiseTransaction['ChartData'] as $Val) {
                $NameID     = $Val['NameID'];
                $Name   = $Val['name'] ?? $Val['name'] ?? 'Unknown Center';
                $TotalItemQty = (float)($Val['y'] ?? $Val['y'] ?? 0);
                $Name2 = "";
                $SecondArray = [];
                foreach($ItemWiseTransaction["UniqueCenterID"] as $CenterID){
                    $opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
        			$SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
        			$AdjQty = 0; $InQty = 0; $OutQty = 0;$BalQty = 0;
                    foreach($ItemWiseTransaction["TransactionData"] as $Key2=>$Val2){
                        $NameID2 = $Val2['ItemID'];
                        if($CenterID == $Val2["CenterID"]){
                            $Name2   = $Val2['CenterName'] ?? 'Unknown Center';
                        }
                        if($NameID == $NameID2 && $CenterID == $Val2["CenterID"] && $Val2["TType"] == "O" && $Val2["TType2"] == "SALE"){
                            $SaleQty += $Val2["TotalQty"];
                        }elseif($NameID == $NameID2 && $CenterID == $Val2["CenterID"] && $Val2["TType"] == "P" && $Val2["TType2"] == "Purchase"){
                            $PurchQty += $Val2["TotalQty"];
                        }elseif($NameID == $NameID2 && $CenterID == $Val2["CenterID"] && $Val2["TType"] == "T" && $Val2["TType2"] == "IN"){
                            $InQty += $Val2["TotalQty"];
                        }elseif($NameID == $NameID2 && $CenterID == $Val2["CenterID"] && $Val2["TType"] == "T" && $Val2["TType2"] == "OUT"){
                            $OutQty += $Val2["TotalQty"];
                        }elseif($NameID == $NameID2 && $CenterID == $Val2["CenterID"] && $Val2["TType"] == "I" && $Val2["TType2"] == "INWARD"){
                            $InwardQty += $Val2["TotalQty"];
                        }
                    }
                    $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
                    if($BalQty > 0){
                        if ($FilterData["HighStockChartType"] != "pie") {
            				$CenterQty = (float)($BalQty ?? 0);
            			} else {
            				$CenterQty1 = (float)($BalQty ?? 0);
            				$CenterPer = ($Val['Qty'] > 0) ? round(($CenterQty1 / $Val['Qty']) * 100, 2) : 0;
            				$CenterQty = $CenterPer; // Keep decimal part
            			}
                        $SecondArray[] = [$Name2,$CenterQty];
                    }
                }
                
                if($HighStockResultCount !="All"){
                    usort($SecondArray, function($a, $b) {
                        return $b[1] <=> $a[1]; // descending
                    });
                    $topresult = array_slice($SecondArray, 0, $HighStockResultCount);
                }else{
                    $topresult = $SecondArray;
                }
                
                $SecondLayerData[] = [
                    "name"=>$Name2,
                    "id"=>$NameID,
                    "data"=>$topresult
                ];
                $finalData[] = [
                    'name'    => $Name,
                    'y'        => $TotalItemQty,
                    'drilldown'=>$NameID,
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['ChartData' => $finalData,"SecondLayerData"=>$SecondLayerData,"dd"=>$SalesdataCenterAndVillageWise]);
    }
//==================== Sale Vs Purchase Report Chart ===========================
    public function SaleVsPurchaseChartReport()
	{
	    $FilterData = array(
	        "SalesPurchResultCount" =>$this->input->post('SalesPurchResultCount'),
            "SalesPurchFilterType" =>$this->input->post('SalesPurchFilterType'),
            "SalesPurchBrandList"=>$this->input->post('SalesPurchBrandList'),
            "SalesPurchCategoryList"=>$this->input->post('SalesPurchCategoryList'),
        );
        
		$result = $this->K1Dashboard_model->SaleVsPurchaseChartReport($FilterData);
		$data = [
		    'ChartData' => $result['ChartData'],
		];
		echo json_encode($data);
	}
    
    
	}		