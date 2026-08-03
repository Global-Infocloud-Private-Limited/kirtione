<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class K1InventoryMaster extends AdminController
	{
		private $not_importable_fields = ['id'];
		
		public function __construct()
		{
			parent::__construct();
			$this->load->model('K1InventoryModel');
			$this->load->model('PurchaseModel');
			$this->load->model('misc_reports_model');
			$this->load->model('ItemModel');
		}
		
		/* Start Stock Position report code */
		
		public function index()
		{
			if (!has_permission_new('K1StockPosition', '', 'view')) {
				access_denied('access_denied');
			}
			$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
			// echo "<pre>";print_r($data['Category']);die;
			$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
			$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
			$data['title'] = "Stock Reports";
			$this->load->view('admin/K1Inventory/StockPositionReport', $data);
		}
		//========================== Get Item Group List ===============================
		public function GetItemGroupList()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$ItemGroupList = $this->K1InventoryModel->GetItemAllGroupList();
			echo json_encode($ItemGroupList);
		}
		
		//================== Get Godown List By CenterID ===============================
		public function GetGodownListByCenterID()
		{
			$CenterID = $this->input->post('CenterID');
			$GodownList = $this->K1InventoryModel->GetGodownListByCenterID($CenterID);
			echo json_encode($GodownList);
		}
//===================== Get Inventory Data =====================================
	public function GetStockReport()
	{
		if (!has_permission_new('K1StockPosition', '', 'view')) {
			access_denied('access_denied');
		}
		$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'ItemGroup'  => $this->input->post('ItemGroup'),
			'CenterID'  => $this->input->post('CenterID'),
			'GodownID'  => $this->input->post('GodownID'),
			'PartyID'  => $this->input->post('PartyID'),
		);
		$ItemGroup = $this->input->post('ItemGroup');
		$CenterIDs = $this->input->post('CenterID');
		$GodownID = $this->input->post('GodownID');
		$AllItemList = $this->K1InventoryModel->GetItemList($filterdata);
		$ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
		$GroupList = array();
		foreach($ItemGroupList as $val){
			array_push($GroupList,$val["SubcategoryName"]);
		}
		
		$panel = "kirti";
		$item_group_name = implode(',', $GroupList);
		$CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
		$CenterWiseGodownList = $this->misc_reports_model->GetWHListByCenterID($CenterIDs,$GodownID);
		$company_data = $this->misc_reports_model->get_company_detail();
		$StockOQtyData = $this->K1InventoryModel->GetItemWiseCenterWiseOpnQty($filterdata,$ItemGroup,$panel);
		
		$fy = $this->session->userdata('finacial_year');
		$First_date_FY = '20'.$fy.'-04-01';
		$FromDate = to_sql_date($this->input->post('from_date'));
		if($First_date_FY != $FromDate){
			$day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
			$GetPreTransaction = $this->K1InventoryModel->GetPreItemWiseCenterWiseStockData($filterdata,$ItemGroup,$day_before,$panel);
		}
		$StockData = $this->K1InventoryModel->GetItemWiseCenterWiseStockData($filterdata,$ItemGroup,$panel);
		
		$i = 0;
		$TotalPurchQty = 0;
		foreach ($AllItemList as $key => $value) {
		    $ItemClosing = 0;
		    $CenterIndex = 0;
		    foreach($CenterList as $key=>$val){
		        $OpnQty = 0;
		        $ItemCenterClosing = 0;
    		    foreach($StockOQtyData as $OKey=>$OVal){
    			    if($value["ProductID"] == $OVal["ItemID"] && $val["CenterID"]==$OVal["CenterID"] ){
    			        $OpnQty += $OVal["TotalQty"];
    			    }
    		    }
    		    // Get Before From date Transaction Qty
    			$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty = 0;$BalQty = 0;
    			foreach($GetPreTransaction as $PTKey=>$PTVal){
    				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
    					$SaleQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
    					$SaleRtnQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
    					$PurchQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
    					$PurchRtnQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
    					$InQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
    					$OutQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
    					$InwardQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
    					$LeanQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "X"){
    					$AdjQty += $PTVal["TotalQty"];
    				}
    			}
    			$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;;
    			$OpnQty += $BalQty;
    			// Get In Between date Transaction 
    				
    			$PurchQty = 0;$InwardQty = 0; $LeanQty = 0; $PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$ItemCenterClosing = 0;
    			foreach($StockData as $stockKey=>$stockVal){
    				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
    					$SaleQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
    					$SaleRtnQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
    					$SaleDmgRtnQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
    					$PurchQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "PURCHASE RETURN"){
    					$PurchRtnQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "IN"){
    					$InQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "OUT"){
    					$OutQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "I" && $stockVal["TType2"] == "INWARD"){
    					$InwardQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "L" && $stockVal["TType2"] == "LIENMARK"){
    					$LeanQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "X"){
    					$AdjQty += $stockVal["TotalQty"];
    				}
    			}
    			if($PurchQty > 0){
    			    $CenterList[$CenterIndex]["PurchQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["PurchQty"])){
    			    $CenterList[$CenterIndex]["PurchQty"] = "N";
    			}
    			if($InwardQty > 0){
    			    $CenterList[$CenterIndex]["InwardQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["InwardQty"])){
    			    $CenterList[$CenterIndex]["InwardQty"] = "N";
    			}
    			
    			if($LeanQty > 0){
    			    $CenterList[$CenterIndex]["LeanQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["LeanQty"])){
    			    $CenterList[$CenterIndex]["$LeanQty"] = "N";
    			}
    			
    			if($PurchRtnQty > 0){
    			    $CenterList[$CenterIndex]["PurchRtnQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["PurchRtnQty"])){
    			    $CenterList[$CenterIndex]["PurchRtnQty"] = "N";
    			}
    			if($SaleQty > 0){
    			    $CenterList[$CenterIndex]["IsSaleQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["IsSaleQty"])){
    			    $CenterList[$CenterIndex]["IsSaleQty"] = "N";
    			}
    			if($SaleRtnQty > 0){
    			    $CenterList[$CenterIndex]["SaleRtnQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["SaleRtnQty"])){
    			    $CenterList[$CenterIndex]["SaleRtnQty"] = "N";
    			}
    			if($SaleDmgRtnQty > 0){
    			    $CenterList[$CenterIndex]["SaleDmgRtnQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["SaleDmgRtnQty"])){
    			    $CenterList[$CenterIndex]["SaleDmgRtnQty"] = "N";
    			}
    			if($AdjQty > 0){
    			    $CenterList[$CenterIndex]["AdjQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["AdjQty"])){
    			    $CenterList[$CenterIndex]["AdjQty"] = "N";
    			}
    			if($InQty > 0){
    			    $CenterList[$CenterIndex]["InQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["InQty"])){
    			    $CenterList[$CenterIndex]["InQty"] = "N";
    			}
    			if($OutQty > 0){
    			    $CenterList[$CenterIndex]["OutQty"] = "Y";
    			}elseif(!isset($CenterList[$CenterIndex]["OutQty"])){
    			    $CenterList[$CenterIndex]["OutQty"] = "N";
    			}
    			$ItemCenterClosing = $OpnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
    			$ItemClosing += $ItemCenterClosing;
    			$CenterIndex++;
    		}
    		if($ItemClosing > 0){
			    $AllItemList[$i]["IsStock"] = "Y";
			}else{
			    $AllItemList[$i]["IsStock"] = "N";
			}
			$i++;
		}
		/**/
		$html = '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
		$html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
		$html .= '<input type="hidden" name="filterdate" id="filterdate" value="Stock Position From  '.$this->input->post('from_date').' to '.$this->input->post('to_date').'">';
		$html .= '<input type="hidden" name="filter_group" id="filter_group" value="Item Group : </b>'.$item_group_name.' ">';
		$html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
		$html .= '<thead style="font-size:11px;">';
		$html .= '<tr style="display:none;">';
		$html .= '<th colspan="9"><b>'.$company_data->company_name.'</b></th>';
		$html .= '</tr>';
		$html .= '<tr style="display:none;">';
		$html .= '<th colspan="9"><b>'.$company_data->address.'</b></th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th align="left" rowspan="2">SrNo</th>';
		$html .= '<th align="left" rowspan="2">ItemID</th>';
		$html .= '<th align="left" rowspan="2">ItemName</th>';
		$html .= '<th align="left" rowspan="2">Unit</th>';
		foreach($CenterList as $key=>$val){
			/*$i = 0;
				foreach($CenterWiseGodownList as $whKey=>$whVal){
				if($val["CenterID"] == $whVal["center"]){
                $i++;
				}
				}
			if($i>0){*/ // check warehouse is available or not i.e. if available then show center name as column 
            $colspan = 11;
            if($val["PurchQty"] == "N"){
                $colspan--;
            }
            if($val["InwardQty"] == "N"){
                $colspan--;
            }
            
            if($val["LeanQty"] == "N"){
                $colspan--;
            }
            
            if($val["PurchRtnQty"] == "N"){
                $colspan--;
            }
            if($val["IsSaleQty"] == "N"){
                $colspan--;
            }
            if($val["SaleRtnQty"] == "N"){
                $colspan--;
            }
            if($val["SaleDmgRtnQty"] == "N"){
                $colspan--;
            }
            if($val["AdjQty"] == "N"){
                $colspan--;
            }
            if($val["InQty"] == "N"){
                $colspan--;
            }
            if($val["OutQty"] == "N"){
                $colspan--;
            }
            $html .= '<th align="center" colspan="'.$colspan.'">'.$val["CenterID"].'</th>';
			//}
		}
		$html .= '<th align="left" rowspan="2">Total Closing</th>';
		$html .= '</tr>';
		
		/*$html .= '<tr>';
			foreach($CenterList as $key=>$val){
			foreach($CenterWiseGodownList as $whKey=>$whVal){
            if($val["CenterID"] == $whVal["center"]){
			$html .= '<th align="center" colspan="6">'.$whVal["w_name"].'</th>';
            }
			}
			}
		$html .= '</tr>';*/
		
		$html .= '<tr>';
		foreach($CenterList as $key=>$val){
			/*foreach($CenterWiseGodownList as $whKey=>$whVal){
			if($val["CenterID"] == $whVal["center"]){*/
			$html .= '<th align="center" >Opn Qty</th>';
				
			if($val["PurchQty"] == "Y"){
			    $html .= '<th align="center" >Purch Qty</th>';
			}
			if($val["PurchRtnQty"] == "Y"){
			    $html .= '<th align="center" >Purch Rtn Qty</th>';
			}
			if($val["IsSaleQty"] == "Y"){
			    $html .= '<th align="center" >Sale Qty</th>';
			}
			if($val["SaleRtnQty"] == "Y"){
			    $html .= '<th align="center" >Sale Rtn Qty</th>';
			}
			if($val["SaleDmgRtnQty"] == "Y"){
			    $html .= '<th align="center" >Sale Dmg Rtn Qty</th>';
			}
			if($val["InwardQty"] == "Y"){
			    $html .= '<th align="center" >Inward Qty</th>';
			}
			
			if($val["LeanQty"] == "Y"){
			    $html .= '<th align="center" >Lean Qty</th>';
			}
			
			if($val["InQty"] == "Y"){
			    $html .= '<th align="center" >In Qty</th>';
			}
			if($val["OutQty"] == "Y"){
			    $html .= '<th align="center" >Out Qty</th>';
			}
			if($val["AdjQty"] == "Y"){
			    $html .= '<th align="center" >Adj Qty</th>';
			}
			$html .= '<th align="center" >Cls Qty</th>';
            /*}
			}*/
		}
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		$SrNo = 1;
		$i = 1;
		foreach ($AllItemList as $key => $value) {
		    if($value["IsStock"] == "Y"){
		        $TotalClosing = 0;
    			$html .= '<tr>';
    			$html .= '<td>'.$SrNo.'</td>';
    			$html .= '<td>'.$value["ProductID"].'</td>';
    			$html .= '<td>'.$value["ProductName"].'</td>';
    			$html .= '<td>'.$value["unit"].'</td>';
    			foreach($CenterList as $key=>$val){
    				/*foreach($CenterWiseGodownList as $whKey=>$whVal){
    				if($val["CenterID"] == $whVal["center"]){*/
    				$OpnQty = 0;
    				// Get Opening Balance Qty
                    foreach($StockOQtyData as $OKey=>$OVal){
    				    if($value["ProductID"] == $OVal["ItemID"] && $val["CenterID"]==$OVal["CenterID"] ){
    				        $OpnQty += $OVal["TotalQty"];
    				    }
    			    }
    				// Get Before From date Transaction Qty
    				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0; $LeanQty = 0;$BalQty = 0;
        			foreach($GetPreTransaction as $PTKey=>$PTVal){
        				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
        					$SaleQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
        					$PurchQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
        					$PurchRtnQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
        					$InQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
        					$OutQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
        					$InwardQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
    					    $LeanQty += $PTVal["TotalQty"];
    				    }else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "X"){
        					$AdjQty += $PTVal["TotalQty"];
        				}
        			}
        			$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
        			$OpnQty += $BalQty;
        			
    				// Get In Between date Transaction 
    				
    				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $LeanQty =0;$OutQty = 0;$BalQty = 0;
        			foreach($StockData as $stockKey=>$stockVal){
        				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
        					$SaleQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
        					$SaleDmgRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
        					$PurchQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "PURCHASE RETURN"){
        					$PurchRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "IN"){
        					$InQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "OUT"){
        					$OutQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "I" && $stockVal["TType2"] == "INWARD"){
        					$InwardQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "L" && $stockVal["TType2"] == "LIENMARK"){
        					$LeanQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "X"){
        					$AdjQty += $stockVal["TotalQty"];
        				}
        			}
        			$BalQty = $OpnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
    				
    				if($OpnQty>0){
    				    $html .= '<td align="center">'.number_format($OpnQty, 2, '.', '').'</td>';
    				}else{
    				    $html .= '<td align="center"></td>';
    				}
    				if($PurchQty>0 && $val["PurchQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($PurchQty, 2, '.', '').'</td>';
    				}elseif($val["PurchQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($PurchRtnQty>0 && $val["PurchRtnQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($PurchRtnQty, 2, '.', '').'</td>';
    				}elseif($val["PurchRtnQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($SaleQty>0 && $val["IsSaleQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($SaleQty, 2, '.', '').'</td>';
    				}elseif($val["IsSaleQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($SaleRtnQty>0 && $val["SaleRtnQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($SaleRtnQty, 2, '.', '').'</td>';
    				}elseif($val["SaleRtnQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($SaleDmgRtnQty>0 && $val["SaleDmgRtnQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($SaleDmgRtnQty, 2, '.', '').'</td>';
    				}elseif($val["SaleDmgRtnQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($InwardQty>0 && $val["InwardQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($InwardQty, 2, '.', '').'</td>';
    				}elseif($val["InwardQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				
    				if($LeanQty>0 && $val["LeanQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($LeanQty, 2, '.', '').'</td>';
    				}elseif($val["LeanQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				
    				if($InQty>0 && $val["InQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($InQty, 2, '.', '').'</td>';
    				}elseif($val["InQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				if($OutQty>0 && $val["OutQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($OutQty, 2, '.', '').'</td>';
    				}elseif($val["OutQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}if($AdjQty>0 && $val["AdjQty"] == "Y"){
    				    $html .= '<td align="center">'.number_format($AdjQty, 2, '.', '').'</td>';
    				}elseif($val["AdjQty"] == "Y"){
    				    $html .= '<td align="center"></td>';
    				}
    				$html .= '<td align="right" style="font-weight:600;">'.number_format($BalQty, 2, '.', '').'</td>';
    				$TotalClosing += $BalQty;
                    /*}
    				}*/
    			}
    			$html .= '<td align="right" style="font-weight:700;">'.number_format($TotalClosing, 2, '.', '').'</td>';
    			//$Opn += $BalQty;
    			$html .= '</tr>';
    			$SrNo++;
		        
		    }
		}
		$html .= '</tbody>';
		$html .= '</table>';
		echo json_encode($html);
		die;
	}
	
	public function GetExpiryReportData()
    {
        if (!has_permission_new('K1ExpairyReport', '', 'view')) {
            access_denied('access_denied');
        }
    
        $filterdata = array(
            'CenterID'  => $this->input->post('CenterID'),
            'PartyID'   => $this->input->post('PartyID'),
            'ItemGroup'=>$this->input->post('ItemGroup'),
            'Days'=>$this->input->post('Days'),
        );
        
        $CenterID = $filterdata['CenterID'];
        $PartyID = $filterdata['PartyID'];
        $ItemGroup = $filterdata['ItemGroup'];
        $DaysFilter = $filterdata['Days'];
        $GetHistoryData = $this->K1InventoryModel->GetHistoryDetilsList($CenterID,$PartyID,$ItemGroup,$DaysFilter);
        
        $BatchList = [];
        $uniqueKeys = [];
        
        if (!empty($GetHistoryData)) {
            foreach ($GetHistoryData as $row) {
                if($row['BatchNo'] !="")
                {
                     $expDateRaw = $row['ExpDate'];
                    $expDateFormatted = !empty($expDateRaw) ? date('d/m/Y', strtotime(str_replace('/', '-', $expDateRaw))) : '-';
                    $normalizedDate = !empty($expDateRaw) ? date('Y-m-d', strtotime(str_replace('/', '-', $expDateRaw))) : '';
    
                    //$dateStr = str_replace('/', '-', $row['ExpDate']);
                    //$normalizedDate = date('Y-m-d', strtotime($dateStr));
                    $compositeKey = $row['ItemID'] . '_' . $row['BatchNo'] . '_' . $normalizedDate;
                    if (!isset($uniqueKeys[$compositeKey])) {
                        $BatchList[] = [
                            'ItemID'      => $row['ItemID'],
                            'BatchNo'     => $row['BatchNo'],
                            'ExpDate'     => $normalizedDate,
                            'ProductName' => $row['ProductName'],
                            'CenterID'    => $row['CenterID'],
                        ];
                        $uniqueKeys[$compositeKey] = true;
                    }
                }
            }
        }
        
        $i=0;
        foreach($BatchList as $key=>$val)
        {
            $opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
            foreach($GetHistoryData as $hkey=>$hval)
            {
                $TransDate =  _d(substr($hval['ExpDate'],0,10));
                if($val['BatchNo'] == $hval['BatchNo'] && $val['ItemID'] == $hval['ItemID'] && $val['CenterID'] == $hval['CenterID'])
                {
                    if($hval["TType"] == "O" && $hval["TType2"] == "SALE"){
					    $SaleQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "SR" && $hval["TType2"] == "FRESH RETURN"){
    					$SaleRtnQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "P" && $hval["TType2"] == "Purchase"){
    					$PurchQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "PR" && $hval["TType2"] == "PURCHASE RETURN"){
    					$PurchRtnQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "T" && $hval["TType2"] == "IN"){
    					$InQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "T" && $hval["TType2"] == "OUT"){
    					$OutQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "I" && $hval["TType2"] == "INWARD"){
    					$InwardQty += $hval["BilledQty"];
    				}else if($hval["TType"] == "X"){
    					$AdjQty += $hval["BilledQty"];
    				}
                }
            }
            $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
            $BatchList[$i]['StockQty']=$BalQty;
            $i++;
        }
        
        $showCenterName = empty($CenterID);
        
        $html = '<table class="table table-bordered">';
        $html .= '<thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Item ID</th>
                        <th>Item Name</th>';
        if ($showCenterName) {
            $html .= '<th>Center Name</th>';
        }
        $html .= '  <th>Qty</th>
                    <th>Expiry Date</th>
                    <th>Batch No</th>
                </tr>
              </thead><tbody>';
    
        $sr = 1;
        foreach ($BatchList as $List) 
        {
            if ((float)$List['StockQty'] == 0) {
                continue; 
            }
            $html .= '<tr>';
            $html .= '<td>' . $sr++ . '</td>';
            $html .= '<td>' . htmlspecialchars($List['ItemID']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['ProductName']) . '</td>';
            if ($showCenterName) {
                $html .= '<td>' . htmlspecialchars($List['CenterID']) . '</td>';
            }
            $html .= '<td align="right">' . number_format($List['StockQty'], 2, '.', '') . '</td>';
            $html .= '<td>' . _d(substr($List['ExpDate'],0,10)) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['BatchNo']) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
    	$html .= '</table>';
    	echo $html;
        exit;
    }
		
	public function export_stock_report()
	{
		if (!has_permission_new('K1StockPosition', '', 'export')) {
			access_denied('access_denied');
		}
		if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		
		if($this->input->post()){
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'ItemGroup'  => $this->input->post('ItemGroup'),
			'CenterID'  => $this->input->post('CenterID'),
			'GodownID'  => $this->input->post('GodownID'),
			'PartyID'  => $this->input->post('PartyID'),
			);
			$ItemGroup = $this->input->post('ItemGroup');
			$CenterIDs = $this->input->post('CenterID');
			$GodownID = $this->input->post('GodownID');
			$AllItemList = $this->K1InventoryModel->GetItemList($filterdata,$ItemGroup);
			$ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
			$GroupList = array();
			foreach($ItemGroupList as $val){
				array_push($GroupList,$val["SubcategoryName"]);
			}
			$item_group_name = implode(',', $GroupList);
			$CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
			$CenterWiseGodownList = $this->misc_reports_model->GetWHListByCenterID($CenterIDs,$GodownID);
			$company_data = $this->misc_reports_model->get_company_detail();
			$StockOQtyData = $this->K1InventoryModel->GetItemWiseCenterWiseOpnQty($filterdata,$ItemGroup);
		
			$fy = $this->session->userdata('finacial_year');
			$First_date_FY = '20'.$fy.'-04-01';
			$FromDate = to_sql_date($this->input->post('from_date'));
			$panel = "kirti";
			if($First_date_FY != $FromDate){
				$day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
				$GetPreTransaction = $this->K1InventoryModel->GetPreItemWiseCenterWiseStockData($filterdata,$ItemGroup,$day_before,$panel);
			}
			$StockData = $this->K1InventoryModel->GetItemWiseCenterWiseStockData($filterdata,$ItemGroup,$panel);
			$writer = new XLSXWriter();
			
			$company_name = array($company_data->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_data->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);
			
			$msg = "Stock Report From " .$this->input->post('from_date')." To ".$this->input->post('to_date')." ";
			$filter = array($msg);
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter);
			$j = 3;
			$msg2 = "Item Group: ".$item_group_name;
			$filter2 = array($msg2);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter2);
			
			$i = 0;
    		$TotalPurchQty = 0;
    		foreach ($AllItemList as $key => $value) {
    		    $ItemClosing = 0;
    		    $CenterIndex = 0;
    		    foreach($CenterList as $key=>$val){
    		        $OpnQty = 0;
    		        $ItemCenterClosing = 0;
        		    foreach($StockOQtyData as $OKey=>$OVal){
        			    if($value["ProductID"] == $OVal["ItemID"] && $val["CenterID"]==$OVal["CenterID"] ){
        			        $OpnQty += $OVal["TotalQty"];
        			    }
        		    }
        		    // Get Before From date Transaction Qty
        			$PurchQty = 0;$InwardQty = 0;$LeanQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
        			foreach($GetPreTransaction as $PTKey=>$PTVal){
        				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
        					$SaleQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
        					$PurchQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
        					$PurchRtnQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
        					$InQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
        					$OutQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
        					$InwardQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
        					$LeanQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "X"){
        					$AdjQty += $PTVal["TotalQty"];
        				}
        			}
        			$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
        			$OpnQty += $BalQty;
        			// Get In Between date Transaction 
        				
        			$PurchQty = 0;$InwardQty = 0; $LeanQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$ItemCenterClosing = 0;
        			foreach($StockData as $stockKey=>$stockVal){
        				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
        					$SaleQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
        					$SaleDmgRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
        					$PurchQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "PURCHASE RETURN"){
        					$PurchRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "IN"){
        					$InQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "OUT"){
        					$OutQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "I" && $stockVal["TType2"] == "INWARD"){
        					$InwardQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "L" && $stockVal["TType2"] == "LIENMARK"){
        					$LeanQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "X"){
        					$AdjQty += $stockVal["TotalQty"];
        				}
        			}
        			if($PurchQty > 0){
        			    $CenterList[$CenterIndex]["PurchQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["PurchQty"])){
        			    $CenterList[$CenterIndex]["PurchQty"] = "N";
        			}
        			if($InwardQty > 0){
        			    $CenterList[$CenterIndex]["InwardQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["InwardQty"])){
        			    $CenterList[$CenterIndex]["InwardQty"] = "N";
        			}
        			
        			if($LeanQty > 0){
        			    $CenterList[$CenterIndex]["LeanQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["LeanQty"])){
        			    $CenterList[$CenterIndex]["LeanQty"] = "N";
        			}
        			
        			if($PurchRtnQty > 0){
        			    $CenterList[$CenterIndex]["PurchRtnQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["PurchRtnQty"])){
        			    $CenterList[$CenterIndex]["PurchRtnQty"] = "N";
        			}
        			if($SaleQty > 0){
        			    $CenterList[$CenterIndex]["IsSaleQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["IsSaleQty"])){
        			    $CenterList[$CenterIndex]["IsSaleQty"] = "N";
        			}
        			if($SaleRtnQty > 0){
        			    $CenterList[$CenterIndex]["SaleRtnQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["SaleRtnQty"])){
        			    $CenterList[$CenterIndex]["SaleRtnQty"] = "N";
        			}
        			if($SaleDmgRtnQty > 0){
        			    $CenterList[$CenterIndex]["SaleDmgRtnQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["SaleDmgRtnQty"])){
        			    $CenterList[$CenterIndex]["SaleDmgRtnQty"] = "N";
        			}
        			if($AdjQty > 0){
        			    $CenterList[$CenterIndex]["AdjQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["AdjQty"])){
        			    $CenterList[$CenterIndex]["AdjQty"] = "N";
        			}
        			if($InQty > 0){
        			    $CenterList[$CenterIndex]["InQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["InQty"])){
        			    $CenterList[$CenterIndex]["InQty"] = "N";
        			}
        			if($OutQty > 0){
        			    $CenterList[$CenterIndex]["OutQty"] = "Y";
        			}elseif(!isset($CenterList[$CenterIndex]["OutQty"])){
        			    $CenterList[$CenterIndex]["OutQty"] = "N";
        			}
        			$ItemCenterClosing = $OpnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
        			$ItemClosing += $ItemCenterClosing;
        			$CenterIndex++;
        		}
        		if($ItemClosing > 0){
    			    $AllItemList[$i]["IsStock"] = "Y";
    			}else{
    			    $AllItemList[$i]["IsStock"] = "N";
    			}
    			$i++;
    		}
			$j = 4;
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$ColFrom = 3;
			$ColTo = 11;
			foreach($CenterList as $key=>$val){
				$list_add[] = $val["CenterName"];
				if($val["PurchQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["InwardQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                
                if($val["LeanQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                
                if($val["PurchRtnQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["IsSaleQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["SaleRtnQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["SaleDmgRtnQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["AdjQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["InQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
                if($val["OutQty"] == "Y"){
                    $ColTo--;
                    $list_add[] = "";
                }
				//$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = $ColFrom, $end_row = $j, $end_col = $ColTo);  //merge cells
				
				$ColFrom += ($ColTo + 1);
				$ColTo += 11;
				$list_add[] = "";
			}
			
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk[] = "ItemID";
			$set_col_tk[] = "ItemName";
			$set_col_tk[] = "Unit";
			foreach($CenterList as $key=>$val){
				$set_col_tk[$val["CenterID"]."OPN Qty"] = "OPN Qty";
				if($val["PurchQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."PURCH Qty"] = "PURCH Qty";
    			}
    			if($val["PurchRtnQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."PURCH Rtn Qty"] = "PURCH Rtn Qty";
    			}
    			if($val["IsSaleQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."SALE Qty"] = "SALE Qty";
    			}
    			if($val["SaleRtnQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."SALE Fresh Rtn Qty"] = "SALE Fresh RtnQty";
    			}
    			if($val["SaleDmgRtnQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."SALE Dmg Rtn Qty"] = "SALE Dmg Rtn Qty";
    			}
    			if($val["InwardQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."Inward Qty"] = "Inward Qty";
    			}
    			
    			if($val["LeanQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."Lean Qty"] = "Lean Qty";
    			}
    			
    			if($val["InQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."IN Qty"] = "IN Qty";
    			}
    			if($val["OutQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."OUT Qty"] = "OUT Qty";
    			}
    			if($val["AdjQty"] == "Y"){
    			    $set_col_tk[$val["CenterID"]."Adj Qty"] = "Adj Qty";
    			}
				$set_col_tk[$val["CenterID"]."Cls Qty"] = "Cls Qty";
			}
			$set_col_tk["Total Closing"] = "Total Closing";
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			foreach ($AllItemList as $key => $value) {
			    if($value["IsStock"] == "Y"){
			        $TotalClosing = 0;
    				$list_add = [];
    				$list_add[] = $value["ProductID"];
    				$list_add[] = $value["ProductName"];
    				$list_add[] = $value["unit"];
    				foreach($CenterList as $key=>$val){
    					$OpnQty = 0;
        				// Get Opening Balance Qty
                        foreach($StockOQtyData as $OKey=>$OVal){
        				    if($value["ProductID"] == $OVal["ItemID"] && $val["CenterID"]==$OVal["CenterID"] ){
        				        $OpnQty += $OVal["TotalQty"];
        				    }
        			    }
    					// Get Before From date Transaction Qty
        				$PurchQty = 0;$InwardQty = 0;$LeanQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
            			foreach($GetPreTransaction as $PTKey=>$PTVal){
            				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
            					$SaleQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
            					$SaleRtnQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
            					$PurchQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
            					$PurchRtnQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
            					$InQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
            					$OutQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
            					$InwardQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
            					$LeanQty += $PTVal["TotalQty"];
            				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "X"){
            					$AdjQty += $PTVal["TotalQty"];
            				}
            			}
            			$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
            			$OpnQty += $BalQty;
    					// Get In Between date Transaction 
    					$PurchQty = 0;$InwardQty = 0; $LeanQty = 0; $PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
            			foreach($StockData as $stockKey=>$stockVal){
            				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
            					$SaleQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
            					$SaleRtnQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
            					$SaleDmgRtnQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
            					$PurchQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "PURCHASE RETURN"){
            					$PurchRtnQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "IN"){
            					$InQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "T" && $stockVal["TType2"] == "OUT"){
            					$OutQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "I" && $stockVal["TType2"] == "INWARD"){
            					$InwardQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "L" && $stockVal["TType2"] == "LIENMARK"){
            					$LeanQty += $stockVal["TotalQty"];
            				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "X"){
            					$AdjQty += $stockVal["TotalQty"];
            				}
            			}
            			$BalQty = $OpnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
        				if($OpnQty>0){
        				    $list_add[] = number_format($OpnQty, 2, '.', '');
        				}else{
        				    $list_add[] = "";
        				}
        				if($PurchQty>0 && $val["PurchQty"] == "Y"){
        				    $list_add[] = number_format($PurchQty, 2, '.', '');
        				}elseif($val["PurchQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($PurchRtnQty>0 && $val["PurchRtnQty"] == "Y"){
        				    $list_add[] = number_format($PurchRtnQty, 2, '.', '');
        				}elseif($val["PurchRtnQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($SaleQty>0 && $val["IsSaleQty"] == "Y"){
        				    $list_add[] = number_format($SaleQty, 2, '.', '');
        				}elseif($val["IsSaleQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($SaleRtnQty>0 && $val["SaleRtnQty"] == "Y"){
        				    $list_add[] = number_format($SaleRtnQty, 2, '.', '');
        				}elseif($val["SaleRtnQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($SaleDmgRtnQty>0 && $val["SaleDmgRtnQty"] == "Y"){
        				    $list_add[] = number_format($SaleDmgRtnQty, 2, '.', '');
        				}elseif($val["SaleDmgRtnQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($InwardQty>0 && $val["InwardQty"] == "Y"){
        				    $list_add[] = number_format($InwardQty, 2, '.', '');
        				}elseif($val["InwardQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				
        				if($LeanQty>0 && $val["LeanQty"] == "Y"){
        				    $list_add[] = number_format($LeanQty, 2, '.', '');
        				}elseif($val["LeanQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				
        				if($InQty>0 && $val["InQty"] == "Y"){
        				    $list_add[] = number_format($InQty, 2, '.', '');
        				}elseif($val["InQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($OutQty>0 && $val["OutQty"] == "Y"){
        				    $list_add[] = number_format($OutQty, 2, '.', '');
        				}elseif($val["OutQty"] == "Y"){
        				    $list_add[] = "";
        				}
        				if($AdjQty>0 && $val["AdjQty"] == "Y"){
        				    $list_add[] = number_format($AdjQty, 2, '.', '');
        				}elseif($val["AdjQty"] == "Y"){
        				    $list_add[] = "";
        				}
    					$list_add[] = number_format($BalQty, 2, '.', '');
    					$TotalClosing += $BalQty;
    				}
    				$list_add[] = number_format($TotalClosing, 2, '.', '');
    				$writer->writeSheetRow('Sheet1', $list_add);
			    }
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Stock_Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;
			
		}
	}
	
	public function export_Expairy_Report()
	{
	    if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        if($this->input->post())
        {
			$company_data = $this->misc_reports_model->get_company_detail();
			$fy = $this->session->userdata('finacial_year');
			
			$filterdata = array(
                'CenterID'  => $this->input->post('CenterID'),
                'PartyID'   => $this->input->post('PartyID'),
                'ItemGroup'=>$this->input->post('ItemGroup'),
                'Days'=>$this->input->post('Days'),
            );
        
            $CenterID = $filterdata['CenterID'];
            $PartyID = $filterdata['PartyID'];
            $ItemGroup = $this->input->post('ItemGroup');
            $DaysFilter = $this->input->post('Days');
            $GetHistoryData = $this->K1InventoryModel->GetHistoryDetilsList($CenterID,$PartyID,$ItemGroup,$DaysFilter);
        
            $BatchList = [];
            $uniqueKeys = [];
            
            if (!empty($GetHistoryData)) {
                foreach ($GetHistoryData as $row) {
                    if($row['BatchNo'] !="")
                    {
                        $dateStr = str_replace('/', '-', $row['ExpDate']);
                        $normalizedDate = date('Y-m-d', strtotime($dateStr));
                        $compositeKey = $row['ItemID'] . '_' . $row['BatchNo'] . '_' . $normalizedDate;
                        if (!isset($uniqueKeys[$compositeKey])) {
                            $BatchList[] = [
                                'ItemID'      => $row['ItemID'],
                                'BatchNo'     => $row['BatchNo'],
                                'ExpDate'     => date('d/m/Y', strtotime($normalizedDate)),//_d(substr($row['ExpDate'],0,10)),
                                'ProductName' => $row['ProductName']
                            ];
                            $uniqueKeys[$compositeKey] = true;
                        }
                    }
                }
            }
            
            $i=0;
            foreach($BatchList as $key=>$val)
            {
                $opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
                foreach($GetHistoryData as $hkey=>$hval)
                {
                    $TransDate =  _d(substr($hval['ExpDate'],0,10));
                    if($val['BatchNo'] == $hval['BatchNo'] && $val['ItemID'] == $hval['ItemID'])
                    {
                        if($hval["TType"] == "O" && $hval["TType2"] == "SALE"){
    					    $SaleQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "SR" && $hval["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "P" && $hval["TType2"] == "Purchase"){
        					$PurchQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "PR" && $hval["TType2"] == "PURCHASE RETURN"){
        					$PurchRtnQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "T" && $hval["TType2"] == "IN"){
        					$InQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "T" && $hval["TType2"] == "OUT"){
        					$OutQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "I" && $hval["TType2"] == "INWARD"){
        					$InwardQty += $hval["BilledQty"];
        				}else if($hval["TType"] == "X"){
        					$AdjQty += $hval["BilledQty"];
        				}
                    }
                }
                $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
                $BatchList[$i]['StockQty']=$BalQty;
                $i++;
            }
            
            $CenterIDs = $this->input->post('CenterID');
			if($CenterIDs){
			    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
    			$Center = array();
    			foreach($CenterList as $val){
    				array_push($Center,$val["CenterName"]);
    			}
    			$Center_name = implode(', ', $Center);
			}else{
			    $Center_name = "All";
			}
			
		if ($ItemGroup) {
            $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
            $GroupList = array();
            foreach ($ItemGroupList as $val) {
                array_push($GroupList, $val["SubCategoryName"]);
            }
            $item_group_name = implode(',', $GroupList);
        } else {
            $item_group_name = "All";
        }
            
            $PartyIDs = $PartyID;
			$PartyList = $this->K1InventoryModel->GetPartyList($PartyIDs);
			$Party = array();
			foreach($PartyList as $val){
				array_push($Party,$val["company"]);
			}
			$Party_name = implode(', ', $Party);
		
			$writer = new XLSXWriter();
			
            $company_name = array($company_data->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_data->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);
			
			$msg3 = "Center Name: ".$Center_name;
			$filter3 = array($msg3);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter3);
			$j = 5;
			if (in_array("KASPL", $PartyIDs)) {
				$msg4 = "Party Name: KIRTI AGRI SOLUTION PVT LTD";
				} else if ($PartyIDs != "") {
				$msg4 = "Party Name: ".$Party_name;
				} else {
				$msg4 = "Party Name: All";
			}
			
			$filter4 = array($msg4);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter4);
			$j = 6;
			
			$msg5 = "Item Category: ".$item_group_name;
			$filter5 = array($msg5);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter5);
			$j = 7;
			
            $list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk[] = "Sr.No";
			$set_col_tk[] = "Item ID";
			$set_col_tk[] = "Item Name";
			$set_col_tk[] = "Qty";
			$set_col_tk[] = "Expiry Date";
			$set_col_tk[] = "Batch No";				
            $writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			$SrNo = 1;
            foreach ($BatchList as $List) {
                if ((float)$List['StockQty'] == 0) {
                    continue; 
                }
                $list_add = [];
				$list_add[] = $SrNo;
                $list_add[] = $List['ItemID'];
                $list_add[] = $List['ProductName'];
                $list_add[] = number_format($List['StockQty'], 2, '.', '');
                $list_add[] = $List['ExpDate'];
                $list_add[] = $List['BatchNo'];
                
                $SrNo++;
				$writer->writeSheetRow('Sheet1', $list_add);
            }
			
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Itemwise Expairy Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;  
			
			
		}
	}
		//========================	Item Wise StockReport ====================
		public function ItemWiseStockReport()
		{
			if (!has_permission_new('K1ItemWiseStockPosition', '', 'view')) {
				access_denied('access_denied');
			}
			$data['Category'] = $this->K1InventoryModel->GetItemGroupList();
			$data['product'] = $this->K1InventoryModel->GetItemGroupListbyproduct();
			// echo "<pre>";print_r($data['Category']);die;
			$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
			$data['company_detail'] = $this->ItemModel->get_company_detail();
			$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
			$data['title'] = "Item Wise Stock Reports";
			$this->load->view('admin/K1Inventory/ItemWiseStockReport', $data);
		}
		
		//===================== Get Inventory Data =====================================
		public function GetItemWiseStockReport()
		{
			if (!has_permission_new('K1ItemWiseStockPosition', '', 'view')) {
				access_denied('access_denied');
			}
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'ItemID'  => $this->input->post('ItemID'),
			'CenterID'  => $this->input->post('CenterID'),
			'PartyID'  => $this->input->post('PartyID'),
			);
			
			$ItemGroup = $this->input->post('ItemID');
			$CenterIDs = $this->input->post('CenterID');
			$GodownID = $this->input->post('GodownID');
			$PartyIDs = $this->input->post('PartyID');
			$AllItemList = $this->K1InventoryModel->GetItemList($filterdata,$ItemGroup);
			$ItemGroupList = $this->K1InventoryModel->GetItemselectedata($ItemGroup);
			$CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
			$PartyList = $this->K1InventoryModel->GetPartyList($PartyIDs);
			$Party = array();
			foreach($PartyList as $val){
				array_push($Party,$val["company"]);
			}
			$Party_name = implode(', ', $Party);
			
			$Center = array();
			foreach($CenterList as $val){
				array_push($Center,$val["CenterName"]);
			}
			$Center_name = implode(', ', $Center);
			
			$company_data = $this->misc_reports_model->get_company_detail();
			//$StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$ItemGroup);
			
			$fy = $this->session->userdata('finacial_year');
			$First_date_FY = '20'.$fy.'-04-01';
			$FromDate = to_sql_date($this->input->post('from_date'));
			$panel = "kirti";
			if($First_date_FY != $FromDate){
				$day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
				
				$GetPreTransaction = $this->K1InventoryModel->GetPreItemWiseStockData($filterdata,$day_before,$panel);
			}
			$StockData = $this->K1InventoryModel->GetItemWiseStockData($filterdata,$panel);
			$ItemOpnQty = $this->K1InventoryModel->GetItemOpnQty($filterdata,$panel);
			// echo"<pre>";
			// print_r($StockData);
			/**/
			$html = '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
			$html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
			$html .= '<input type="hidden" name="filterdate" id="filterdate" value="Stock Position From  '.$this->input->post('from_date').' to '.$this->input->post('to_date').'">';
			if ($CenterIDs != "") {
				$msg3 = $Center_name;
				} else {
				$msg3 = "All";
			}
			$html .= '<input type="hidden" name="Center_Name" id="Center_Name" value="Center Name : </b>'.$msg3.' ">';
			$html .= '<input type="hidden" name="filter_group" id="filter_group" value="Item Name : </b>'.$ItemGroupList->ProductName.' ">';
			if (in_array("KASPL", $PartyIDs)) {
				$msg4 = "KIRTI AGRI SOLUTION PVT LTD";
			} else if ($PartyIDs != "") {
				$msg4 = $Party_name;
			} else {
				$msg4 = "All";
			}
			$html .= '<input type="hidden" name="PartyName" id="PartyName" value="Party Name : </b>'.$msg4.' ">';
			
			$html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
			$html .= '<thead style="font-size:11px;">';
			$html .= '<tr style="display:none;">';
			$html .= '<th colspan="9"><b>'.$company_data->company_name.'</b></th>';
			$html .= '</tr>';
			$html .= '<tr style="display:none;">';
			$html .= '<th colspan="9"><b>'.$company_data->address.'</b></th>';
			$html .= '</tr>';
			$html .= '<tr>';
			$html .= '<th align="left" rowspan="3">SrNo</th>';
			$html .= '<th align="left" rowspan="2">Date</th>';
			$html .= '<th align="center" >Opn Qty</th>';
			$html .= '<th align="center" >Purch Qty</th>';
			$html .= '<th align="center" >Purch Rtn Qty</th>';
			$html .= '<th align="center" >Sale Qty</th>';
			$html .= '<th align="center" >Sale Rtn Qty</th>';
			$html .= '<th align="center" >Sale Dmg Rtn Qty</th>';
			$html .= '<th align="center" >In Qty</th>';
			$html .= '<th align="center" >Out Qty</th>';
			$html .= '<th align="center" >Inward Qty</th>';
			$html .= '<th align="center" >Lean Qty</th>';
			$html .= '<th align="center" >Adj Qty</th>';
			$html .= '<th align="center" >Bal Qty</th>';            
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$SrNo = 1;
			$start_date = to_sql_date($this->input->post('from_date'));
			
			$end_date = to_sql_date($this->input->post('to_date'));
			
			$begin = new DateTime($start_date);
			$end = new DateTime($end_date);
			$end = $end->modify('+1 day'); // Include the end date
			
			$interval = new DateInterval('P1D'); // 1 day interval
			$date_range = new DatePeriod($begin, $interval, $end);
			// echo"<pre>";
			// print_r($GetPreTransaction);
			// die;
			
			$opnQty = 0;
			if($ItemOpnQty){
			    $opnQty = $ItemOpnQty[0]["TotalQty"];
			}
			$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty = 0;$BalQty = 0;
			foreach($GetPreTransaction as $PTKey=>$PTVal){
				if($PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
					$SaleQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
					$SaleRtnQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
					$PurchQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
					$PurchRtnQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
					$InQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
					$OutQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
					$InwardQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
					$LeanQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "X"){
					$AdjQty += $PTVal["TotalQty"];
				}
			}
			$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
			$opnQty = $BalQty;
			foreach ($date_range as $date) { 
				
				$html .= '<tr>';
				$html .= '<td>'.$SrNo.'</td>';
				$dateStr = $date->format('d/m/Y');
				$html .= '<td>' . $dateStr . '</td>';
				$row_date = $date->format('Y-m-d');
				
				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0; $LeanQty =0; $BalQty = 0;
				$currentDate = $date->format('Y-m-d');
				
				$stockDate = new DateTime($stockVal["TransDate"]);
				$stockDateFormatted = $stockDate->format('Y-m-d');
				
				foreach($StockData as $stockkey=>$stockval){
					if($row_date == $stockval["Date"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
						$SaleQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
						$SaleRtnQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "DAMAGE RETURN"){
						$SaleDmgRtnQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
						$PurchQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN"){
						$PurchRtnQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
						$InQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
						$OutQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
						$InwardQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK"){
						$LeanQty += $stockval["TotalQty"];
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "X"){
						$AdjQty += $stockval["TotalQty"];
					}
				}
				$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
				
				$TotalPurchQty += $PurchQty;
                $TotalPurchRtnQty += $PurchRtnQty;
                $TotalInwardQty += $InwardQty;
                $TotalLeanQty += $LeanQty;
                $TotalPRDQty += $PRDQty;
                $TotalAdjQty += $AdjQty;
                $TotalIssueQty += $IssueQty;
                $TotalSaleQty += $SaleQty;
                $TotalSaleRtnQty += $SaleRtnQty;
                $TotalSaleDmgRtnQty += $SaleDmgRtnQty;
                $TotalInQty += $InQty;
                $TotalOutQty += $OutQty;
                $TotalBalQty += $BalQty;
				
				$html .= '<td align="center">'.number_format($opnQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($PurchQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($PurchRtnQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($SaleQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($SaleRtnQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($SaleDmgRtnQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($InQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($OutQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($InwardQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($LeanQty, 2, '.', '').'</td>';
				$html .= '<td align="center">'.number_format($AdjQty, 2, '.', '').'</td>';
				$html .= '<td align="right" style="font-weight:600;">'.number_format($BalQty, 2, '.', '').'</td>';
				$opnQty  = $BalQty;
				$html .= '</tr>';
				$SrNo++;
			}
			$html .= '<tr>';
			$html .= '<td colspan="2"  style="font-weight:900;font-size:15px;" align="center">Total</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format(0, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalPurchQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalPurchRtnQty, 2, '.', ',') .'</td>';
			// $html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalPRDQty, 2, '.', ',') .'</td>';
			// $html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalIssueQty, 2, '.', ',') .'</td>';
			// $html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalMoistQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalSaleQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalSaleRtnQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalSaleDmgRtnQty, 2, '.', ',') .'</td>';
			// $html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalConOutQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalInQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalOutQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalInwardQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalLeanQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right">'.number_format($TotalAdjQty, 2, '.', ',') .'</td>';
			$html .= '<td  style="font-weight:900;font-size:15px;"  align="right"></td>';
			
			$html .= '</tr>';
			
			$html .= '</tbody>';
			$html .= '</table>';
			
			echo json_encode($html);
			die;
		}
		//============================== export Item Wise StockReport =======================
		
		public function export_ItemWiseStockReport()
		{
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			if($this->input->post()){
				$filterdata = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date'),
				'ItemID'  => $this->input->post('ItemID'),
				'CenterID'  => $this->input->post('CenterID'),
				'PartyID'  => $this->input->post('PartyID'),
				);
				$ItemGroup = $this->input->post('ItemID');
				$CenterIDs = $this->input->post('CenterID');
				$PartyIDs = $this->input->post('PartyID');
				$GodownID = $this->input->post('GodownID');
				$AllItemList = $this->K1InventoryModel->GetItemList($filterdata,$ItemGroup);
				$ItemGroupList = $this->K1InventoryModel->GetItemselectedata($ItemGroup);
				$PartyList = $this->K1InventoryModel->GetPartyList($PartyIDs);
				$Party = array();
				foreach($$PartyList as $val){
					array_push($Party,$val["company"]);
				}
				$Party_name = implode(', ', $Party);
				$CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
				$Center = array();
				foreach($CenterList as $val){
					array_push($Center,$val["CenterName"]);
				}
				$Center_name = implode(', ', $Center);
				$company_data = $this->misc_reports_model->get_company_detail();
				$fy = $this->session->userdata('finacial_year');
				$First_date_FY = '20'.$fy.'-04-01';
				$FromDate = to_sql_date($this->input->post('from_date'));
				$panel = "kirti";
				if($First_date_FY != $FromDate){
					$day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
					
					$GetPreTransaction = $this->K1InventoryModel->GetPreItemWiseStockData($filterdata,$day_before,$panel);
				}
				$StockData = $this->K1InventoryModel->GetItemWiseStockData($filterdata,$panel);
				$writer = new XLSXWriter();
				
				$company_name = array($company_data->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $company_data->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				$msg = "Item Wise Stock Report From " .$this->input->post('from_date')." To ".$this->input->post('to_date')." ";
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				$j = 3;
				
				$msg2 = "Item Name: ".$ItemGroupList->ProductName;
				$filter2 = array($msg2);
				$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter2);
				$j = 4;
				if($CenterIDs != ""){
					$msg3 = " Center Name: " . $Center_name;
					} else {
					$msg3 = " Center Name: All";
				}
				
				$filter3 = array($msg3);
				$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter3);
				$j = 5;
				if (in_array("KASPL", $PartyIDs)) {
					$msg4 = "Party Name: KIRTI AGRI SOLUTION PVT LTD";
					} else if ($PartyIDs != "") {
					$msg4 = "Party Name: ".$Party_name;
					} else {
					$msg4 = "Party Name: All";
				}
				$filter4 = array($msg4);
				$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter4);
				$j = 6;
				// $list_add = [];
				// $list_add[] = "";
				// $list_add[] = "";
				// $list_add[] = "";
				// $ColFrom = 3;
				// $ColTo = 8;
				// foreach($CenterList as $key=>$val){
				// $list_add[] = $val["CenterName"];
				// $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = $ColFrom, $end_row = $j, $end_col = $ColTo);  //merge cells
				// $list_add[] = "";
				// $list_add[] = "";
				// $list_add[] = "";
				// $list_add[] = "";
				// $list_add[] = "";
				// $ColFrom += 6;
				// $ColTo += 6;
				// }
				
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$set_col_tk = [];
				$set_col_tk[] = "SrNo";
				$set_col_tk[] = "Date";
				$set_col_tk[] = "Opn Qty";
				$set_col_tk[] = "Purch Qty";
				$set_col_tk[] = "Purch Rtn Qty";
				$set_col_tk[] = "Sale Qty";
				$set_col_tk[] = "Sale Rtn Qty";
				$set_col_tk[] = "Sale Dmg Rtn Qty";
				$set_col_tk[] = "In Qty";
				$set_col_tk[] = "Out Qty";
				$set_col_tk[] = "Inward Qty";
				$set_col_tk[] = "Lean Qty";
				$set_col_tk[] = "Adj Qty";
				$set_col_tk[] = "Bal Qty";
				
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				$SrNo = 1;
				
				$start_date = to_sql_date($this->input->post('from_date'));
				$end_date = to_sql_date($this->input->post('to_date'));
				$begin = new DateTime($start_date);
				$end = new DateTime($end_date);
				$end = $end->modify('+1 day'); // Include the end date
				$interval = new DateInterval('P1D'); // 1 day interval
				$date_range = new DatePeriod($begin, $interval, $end);
				
				$opnQty = 0;
				$PurchQty = 0;$InwardQty = 0;$LeanQty=0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach($GetPreTransaction as $PTKey=>$PTVal){
					if($PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
						$SaleQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
						$SaleRtnQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
						$PurchQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "PURCHASE RETURN"){
						$PurchRtnQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "T" && $PTVal["TType2"] == "IN"){
						$InQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "T" && $PTVal["TType2"] == "OUT"){
						$OutQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "I" && $PTVal["TType2"] == "INWARD"){
						$InwardQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "L" && $PTVal["TType2"] == "LIENMARK"){
						$LeanQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "X"){
						$AdjQty += $PTVal["TotalQty"];
					}
					
				}
				$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
				$opnQty = $BalQty;
				
				foreach ($date_range as $date) 
				{ 
					$list_add = [];
					$list_add[] = $SrNo;
					$dateStr = $date->format('d/m/Y');
					$list_add[] = $dateStr;
					
					$row_date = $date->format('Y-m-d');
					$PurchQty = 0;$InwardQty = 0; $LeanQty = 0; $PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
					
					foreach($StockData as $stockkey=>$stockval){
						if($row_date == $stockval["Date"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
							$SaleQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
							$SaleRtnQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "DAMAGE RETURN"){
							$SaleDmgRtnQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
							$PurchQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN"){
							$PurchRtnQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
							$InQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
							$OutQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
							$InwardQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK"){
							$LeanQty += $stockval["TotalQty"];
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "X"){
							$AdjQty += $stockval["TotalQty"];
						}
					}
					$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
					
					$TotalPurchQty += $PurchQty;
                    $TotalPurchRtnQty += $PurchRtnQty;
                    $TotalInwardQty += $InwardQty;
                    $TotalLeanQty += $LeanQty;
                    $TotalPRDQty += $PRDQty;
                    $TotalAdjQty += $AdjQty;
                    $TotalIssueQty += $IssueQty;
                    $TotalSaleQty += $SaleQty;
                    $TotalSaleRtnQty += $SaleRtnQty;
                    $TotalSaleDmgRtnQty += $SaleDmgRtnQty;
                    $TotalInQty += $InQty;
                    $TotalOutQty += $OutQty;
                
					$list_add[] = number_format($opnQty, 2, '.', '');
					$list_add[] = number_format($PurchQty, 2, '.', '');
					$list_add[] = number_format($PurchRtnQty, 2, '.', '');
					$list_add[] = number_format($SaleQty, 2, '.', '');
					$list_add[] = number_format($SaleRtnQty, 2, '.', '');
					$list_add[] = number_format($SaleDmgRtnQty, 2, '.', '');
					$list_add[] = number_format($InQty, 2, '.', '');
					$list_add[] = number_format($OutQty, 2, '.', '');
					$list_add[] = number_format($InwardQty, 2, '.', '');
					$list_add[] = number_format($LeanQty, 2, '.', '');
					$list_add[] = number_format($AdjQty, 2, '.', '');
					$list_add[] = number_format($BalQty, 2, '.', '');
					$opnQty  = $BalQty;
					$SrNo++;
					$writer->writeSheetRow('Sheet1', $list_add);
					
				}
				
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = number_format($TotalPurchQty, 2, '.', '');
				$list_add[] = number_format($TotalPurchRtnQty, 2, '.', '');
				$list_add[] = number_format($TotalSaleQty, 2, '.', '');
				$list_add[] = number_format($TotalSaleRtnQty, 2, '.', '');
				$list_add[] = number_format($TotalSaleDmgRtnQty, 2, '.', '');
				$list_add[] = number_format($TotalInQty, 2, '.', '');
				$list_add[] = number_format($TotalOutQty, 2, '.', '');
				$list_add[] = number_format($TotalInwardQty, 2, '.', '');
				$list_add[] = number_format($TotalLeanQty, 2, '.', '');
				$list_add[] = number_format($TotalAdjQty, 2, '.', '');
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'Item Wise Stock_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
			
		}
		
	//========================	AS On Date StockReport ====================
	public function AsOndateStockReport()
	{
		if (!has_permission_new('K1AsOnStockPosition', '', 'view')) {
			access_denied('access_denied');
		}
		$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
		$data['product'] = $this->K1InventoryModel->GetItemGroupListbyproduct();
		// echo "<pre>";print_r($data['Category']);die;
		$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
		$data['company_detail'] = $this->ItemModel->get_company_detail();
		$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
		$data['title'] = "As ON Date Stock Reports";
		$this->load->view('admin/K1Inventory/OndateStockReport', $data);
	}
	
	public function ExpairyReport()
	{
	    if (!has_permission_new('K1ExpairyReport', '', 'view')) {
			access_denied('access_denied');
		}
		$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
		$data['company_detail'] = $this->ItemModel->get_company_detail();
		$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
		$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
	    $data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
	    $this->load->view('admin/K1Inventory/ExpairyReport',$data);
	}
	
	public function GetAsondateStockReport()
	{
		if (!has_permission_new('K1AsOnStockPosition', '', 'view')) {
			access_denied('access_denied');
		}
		$filterdata = array(
		'from_date' => $this->input->post('on_date'),
		'ItemGroup'  => $this->input->post('ItemGroup'),
		'CenterID'  => $this->input->post('CenterID'),
		'PartyID'  => $this->input->post('PartyID'),
		);
		$company_data = $this->misc_reports_model->get_company_detail();
		$fy = $this->session->userdata('finacial_year');
		$ItemGroup = $this->input->post('ItemGroup');
		$PartyIDs = $this->input->post('PartyID');
		$ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
		$PartyList = $this->K1InventoryModel->GetPartyList($PartyIDs);
		$Party = array();
		foreach($PartyList as $val){
			array_push($Party,$val["company"]);
		}
		$Party_name = implode(', ', $Party);
		
		if($ItemGroup){
		    $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
		    $GroupList = array();
		    foreach($ItemGroupList as $val){
				array_push($GroupList,$val["SubcategoryName"]);
			}
			$item_group_name = implode(',', $GroupList);
		}else{
		    $item_group_name = "All";
		}
		
		$CenterIDs = $this->input->post('CenterID');
		if($CenterIDs){
		    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
			$Center = array();
			foreach($CenterList as $val){
				array_push($Center,$val["CenterName"]);
			}
			$Center_name = implode(', ', $Center);
		}else{
		    $Center_name = "All";
		}
		$panel = 'kirti';  
		$AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
		
		$ItemWiseOpnQty = $this->K1InventoryModel->GetItemWiseOpningQty($filterdata,$panel);
		$ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata,$panel);
// 		echo '<pre>'; print_r($ASOndateStockData); die;
		
		$html = '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
		$html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
		$html .= '<input type="hidden" name="filterdate" id="filterdate" value="As On date Stock Position  '.$this->input->post('on_date').'">';
		$html .= '<input type="hidden" name="Center_name" id="Center_name" value="Center Name : </b>'.$Center_name.' ">';
		$html .= '<input type="hidden" name="filter_group" id="filter_group" value="Item Category : </b>'.$item_group_name.' ">';
		if (in_array("KASPL", $PartyIDs)) {
			$msg4 = "KIRTI AGRI SOLUTION PVT LTD";
			} else if ($PartyIDs != "") {
			$msg4 = $Party_name;
			} else {
			$msg4 = "All";
		}
		$html .= '<input type="hidden" name="PartyName" id="PartyName" value="Party Name : </b>'.$msg4.' ">';
		$html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
		$html .= '<thead style="font-size:11px;">';
		$html .= '<tr style="display:none;">';
		$html .= '<th colspan="9"><b>'.$company_data->company_name.'</b></th>';
		$html .= '</tr>';
		$html .= '<tr style="display:none;">';
		$html .= '<th colspan="9"><b>'.$company_data->address.'</b></th>';
		$html .= '</tr>';
		$html .= '<tr >';
		$html .= '<th align="left" rowspan="2">SrNo</th>';
		$html .= '<th align="left" rowspan="2">Item ID</th>';
		$html .= '<th align="left" rowspan="2">Item Name</th>';
		$html .= '<th align="center">UOM</th>';
		$html .= '<th align="center">Packing Qty</th>';
		$html .= '<th align="center">Qty (Loose)</th>';
		$html .= '</tr>';
		
		$html .= '</thead>';
		$html .= '<tbody>';
		$SrNo = 1;
		$TotalBalance = 0;
		foreach($AllItemList as $Key=>$Val)
		{
		    $htmlRow = '';
		    $PackingQty = $Val["PackingQty"];
			$htmlRow .= '<tr>';
			$htmlRow .= '<td>'.$SrNo.'</td>';
			$htmlRow .= '<td>'.$Val["ProductID"].'</td>';
			$htmlRow .= '<td>'.$Val["ProductName"].'</td>';
			$htmlRow .= '<td align="center">'.$Val["unit"].'</td>';
			$htmlRow .= '<td align="center">'.$Val["PackingQty"].'</td>';
			$opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$LeanQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
			foreach($ItemWiseOpnQty as $kopnQty=>$vopnQty){
			    if($vopnQty["ItemID"] == $Val["ProductID"]){
			        $opnQty = $vopnQty["TotalOpnQty"];
			    }
			}
			foreach($ASOndateStockData as $stockkey=>$stockval){
				if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
					$SaleQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
					$SaleRtnQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
					$PurchQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN"){
					$PurchRtnQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
					$InQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
					$OutQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
					$InwardQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK"){
					$LeanQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "X"){
					$AdjQty += $stockval["TotalQty"];
				}
				
			}
			$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
			$htmlRow .= '<td align="right">'.number_format($BalQty, 2, '.', '').'</td>';
			$htmlRow .= '</tr>';
			if($BalQty == 0){
			    continue;
			}
			$html .= $htmlRow;
			$SrNo++;
			$TotalBalance += ($BalQty);
		}
		
		$html .= '<tr>';
		$html .= '<td colspan="5"  style="font-weight:900;font-size:15px;" align="center">Total</td>';
		
		$html .= '<td  style="font-weight:900;font-size:15px;" align="right">'.number_format($TotalBalance, 2, '.', '').'</td>';
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		echo json_encode($html);
		die;
	}
		
	public function AsOndateStock()
	{
		if (!has_permission_new('K1AsOnStockPosition', '', 'view')) {
			access_denied('access_denied');
		}
		$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
		$data['product'] = $this->K1InventoryModel->GetItemGroupListbyproduct();
		// echo "<pre>";print_r($data['Category']);die;
		$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
		$data['company_detail'] = $this->ItemModel->get_company_detail();
		$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
		$data['title'] = "As ON Date Stock New";
		$this->load->view('admin/K1Inventory/AsOnDateStock', $data);
	}

	public function FilterAsondateStockReport()
	{
		if (!has_permission_new('K1AsOnStockPosition', '', 'view')) {
			access_denied('access_denied');
		}
		$filterdata = array(
		'from_date' => $this->input->post('on_date'),
		'ItemGroup'  => $this->input->post('ItemGroup'),
		'CenterID'  => $this->input->post('CenterID'),
		'PartyID'  => $this->input->post('PartyID'),
		);
		
		$result = $this->K1InventoryModel->FilterAsondateStockReport($filterdata);
		
		echo json_encode($result);
		die;
	}
		
//========================= As On Date 	Stock Report =========================
	
	public function export_Asondate_stock_report()
	{
		if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        if($this->input->post()){
			
            $filterdata = array(
			'from_date' => $this->input->post('on_date'),
			'ItemGroup'  => $this->input->post('ItemGroup'),
			'CenterID'  => $this->input->post('CenterID'),
			'PartyID'  => $this->input->post('PartyID'),
			);
			$company_data = $this->misc_reports_model->get_company_detail();
			$fy = $this->session->userdata('finacial_year');
			$ItemGroup = $this->input->post('ItemGroup');
			$PartyIDs = $this->input->post('PartyID');
			$PartyList = $this->K1InventoryModel->GetPartyList($PartyIDs);
			$Party = array();
			foreach($PartyList as $val){
				array_push($Party,$val["company"]);
			}
			$Party_name = implode(', ', $Party);
			if($ItemGroup){
			    $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
			    $GroupList = array();
			    foreach($ItemGroupList as $val){
    				array_push($GroupList,$val["SubcategoryName"]);
    			}
    			$item_group_name = implode(',', $GroupList);
			}else{
			    $item_group_name = "All";
			}
			
			$CenterIDs = $this->input->post('CenterID');
			if($CenterIDs){
			    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
    			$Center = array();
    			foreach($CenterList as $val){
    				array_push($Center,$val["CenterName"]);
    			}
    			$Center_name = implode(', ', $Center);
			}else{
			    $Center_name = "All";
			}
			$panel = 'kirti';  
			
			$AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
			$ItemWiseOpnQty = $this->K1InventoryModel->GetItemWiseOpningQty($filterdata,$panel);
			$ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata,$panel);
			$writer = new XLSXWriter();
			
            $company_name = array($company_data->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_data->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);
			
			$msg = "As On Date Stock Report From " .$this->input->post('on_date')." ";
			$filter = array($msg);
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter);
			$j = 3;
			
			
			$msg2 = "Item Group Name: ".$item_group_name;
			$filter2 = array($msg2);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter2);
			$j = 4;
			
			$msg3 = "Center Name: ".$Center_name;
			$filter3 = array($msg3);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter3);
			$j = 5;
			if (in_array("KASPL", $PartyIDs)) {
				$msg4 = "Party Name: KIRTI AGRI SOLUTION PVT LTD";
			} else if ($PartyIDs != "") {
				$msg4 = "Party Name: ".$Party_name;
			} else {
				$msg4 = "Party Name: All";
			}
			$filter4 = array($msg4);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter4);
			$j = 6;
			
            $list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk[] = "SrNo";
			$set_col_tk[] = "Item ID";
			$set_col_tk[] = "Item Name";
			$set_col_tk[] = "UOM";
			$set_col_tk[] = "Packing Qty";
			$set_col_tk[] = "Qty";				
            $writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			$SrNo = 1;
            foreach($AllItemList as $Key=>$Val)
			{
			    $PackingQty = $Val["PackingQty"];
                $list_add = [];
				$list_add[] = $SrNo;
                $list_add[] = $Val["ProductID"];
                $list_add[] = $Val["ProductName"];
                $list_add[] = $Val["unit"];
                $list_add[] = $Val["PackingQty"];
                $opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty = 0;$BalQty = 0;
                foreach($ItemWiseOpnQty as $kopnQty=>$vopnQty){
    			    if($vopnQty["ItemID"] == $Val["ProductID"]){
    			        $opnQty = $vopnQty["TotalOpnQty"];
    			    }
    			}
                foreach($ASOndateStockData as $stockkey=>$stockval){
                    if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
						$SaleQty += $stockval["TotalQty"];
					}if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
						$SaleRtnQty += $stockval["TotalQty"];
					}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
						$PurchQty += $stockval["TotalQty"];
					}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN"){
						$PurchRtnQty += $stockval["TotalQty"];
					}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
						$InQty += $stockval["TotalQty"];
					}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
						$OutQty += $stockval["TotalQty"];
					}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
						$InwardQty += $stockval["TotalQty"];
					}else if ($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK") {
                        $LeanQty += $stockval["TotalQty"];
                    }else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "X"){
						$AdjQty += $stockval["TotalQty"];
					}
				}
                $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
                $list_add[] = number_format($BalQty, 2, '.', '');
				if($BalQty == 0){
    			    continue;
    			}
				$SrNo++;
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'As ON Date Stock_Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;  
			
			
		}
		
	}

//========================= Salable Report =========================
	
	public function SalableReport()
    {
        if (!has_permission_new('K1SalableReport', '', 'view')) {
			access_denied('access_denied');
		}
		$data['company_detail'] = $this->misc_reports_model->get_company_detail();
		$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
		$this->load->view('admin/K1Inventory/SalableReport',$data);
    }
    
    public function GetSalableReportData()
    {
        if (!has_permission_new('K1ExpairyReport', '', 'view')) {
            access_denied('access_denied');
        }
        $filterdata = array(
            'FromDate' =>$this->input->post('FromDate'),
            'ToDate' =>$this->input->post('ToDate'),
            'CenterID'  => $this->input->post('CenterID'),
            'ItemID'=>$this->input->post('ItemID'),
            'ReportBy'=>$this->input->post('ReportBy'),
        );
        
        $FromDate = $filterdata['FromDate'];
        $ToDate = $filterdata['ToDate'];
        $CenterID = $filterdata['CenterID'];
        $ItemID = $filterdata['ItemID'];
        $ReportBy = $filterdata['ReportBy'];
        $GetSalableData = $this->K1InventoryModel->GetSalableList($CenterID,$ItemID,$ReportBy,$FromDate,$ToDate);
        
        $html = '<table class="table table-bordered">';
        $html .= '<thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Packing Qty</th>';
        if ($ReportBy == "Qty") {
            $html .= '<th>Qty</th>';
        }else if($ReportBy == "Amt"){
             $html .= '<th>Amt</th>';
        }
        $html .= ' 
                </tr>
              </thead><tbody>';
    
        $sr = 1;
        $grandTotal = 0;
        foreach ($GetSalableData as $List) 
        {
            $html .= '<tr>';
            $html .= '<td>' . $sr++ . '</td>';
            $html .= '<td>' . htmlspecialchars($List['ItemID']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['ProductName']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['PackingQty']) . '</td>';
            if ($ReportBy == "Qty") {
                $value = (float)$List['TotalBilledQty'];
            } else {
                $value = (float)$List['TotalNetOrderAmt'];
            }
            
            $grandTotal += $value;
            $html .= '<td>' . number_format($value, 2) . '</td>';
            $html .= '</tr>';
        }
         $html .= '<tr style="font-weight:bold; background-color:#f2f2f2;">
                <td colspan="4" class="text-center">Total</td>
                <td>' . number_format($grandTotal, 2) . '</td>
              </tr>';

        $html .= '</tbody></table>';
    	echo $html;
        exit;
        
    }
    
    public function export_Salable_report()
    {
        if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        if($this->input->post())
        {
			$company_data = $this->misc_reports_model->get_company_detail();
			$fy = $this->session->userdata('finacial_year');
			
			$filterdata = array(
                'FromDate' =>$this->input->post('FromDate'),
                'ToDate' =>$this->input->post('ToDate'),
                'CenterID'  => $this->input->post('CenterID'),
                'ItemID'=>$this->input->post('ItemID'),
                'ReportBy'=>$this->input->post('ReportBy'),
            );
        
            $CenterID = $filterdata['CenterID'];
            $ItemID = $filterdata['ItemID'];
            $ReportBy = $filterdata['ReportBy'];
            $FromDate = $filterdata['FromDate'];
            $ToDate = $filterdata['ToDate'];
            $GetSalableData = $this->K1InventoryModel->GetSalableList($CenterID,$ItemID,$ReportBy,$FromDate,$ToDate);
            
            $CenterIDs = $this->input->post('CenterID');
			if($CenterIDs){
			    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
    			$Center = array();
    			foreach($CenterList as $val){
    				array_push($Center,$val["CenterName"]);
    			}
    			$Center_name = implode(', ', $Center);
			}else{
			    $Center_name = "All";
			}
			
    		if ($ItemID) {
                $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemID);
                $GroupList = array();
                foreach ($ItemGroupList as $val) {
                    array_push($GroupList, $val["SubCategoryName"]);
                }
                $item_group_name = implode(',', $GroupList);
            } else {
                $item_group_name = "All";
            }
            
			$writer = new XLSXWriter();
			
            $company_name = array($company_data->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_data->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $company_addr);
			
			$msg2 = "From Date: " . $FromDate . "   To Date: " . $ToDate;
            $filter2 = array($msg2);
            $writer->markMergedCell('Sheet1', 2, 0, 2, 12);  
            $writer->writeSheetRow('Sheet1', $filter2);
			
			$msg3 = "Center Name: ".$Center_name;
			$filter3 = array($msg3);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter3);
			$j = 5;
			
			$msg4 =  "Report By: ".$ReportBy;
			
			$filter4 = array($msg4);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter4);
			$j = 6;
			
			$msg5 = "Item Category: ".$item_group_name;
			$filter5 = array($msg5);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter5);
			$j = 7;
			
            $list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk[] = "Sr.No";
			$set_col_tk[] = "Item ID";
			$set_col_tk[] = "Item Name";
			$set_col_tk[] = "Packing Qty";
			
			if($ReportBy =="Qty")
			{
			    $set_col_tk[] = "Qty";
			}else{
			    $set_col_tk[] = "Amt";		
			}
            $writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			$SrNo = 1;
			$grandTotal = 0;
            foreach ($GetSalableData as $List) {
                
                $list_add = [];
				$list_add[] = $SrNo;
                $list_add[] = $List['ItemID'];
                $list_add[] = $List['ProductName'];
                $list_add[] = number_format($List['PackingQty'], 2, '.', '');
                
                if ($ReportBy == "Qty") {
                    $value = $List['TotalBilledQty'];
                } else {
                    $value = $List['TotalNetOrderAmt'];
                }
                $grandTotal += $value;
                $list_add[] = number_format($value, 2, '.', '');
                $SrNo++;
				$writer->writeSheetRow('Sheet1', $list_add);
            }
            
            $totalLabel = ["", "", "", "Total", number_format($grandTotal, 2, '.', '')];
            $writer->writeSheetRow('Sheet1', []);
            $writer->writeSheetRow('Sheet1', $totalLabel);
			
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Most Salable Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;  
			
			
		}
    }
    
    public function ProfitableReport()
    {
        if (!has_permission_new('k1ProfitableReport', '', 'view')) {
			access_denied('access_denied');
		}
		$data['company_detail'] = $this->misc_reports_model->get_company_detail();
		$data['CenterMaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		$data['SubCategory'] = $this->K1InventoryModel->GetItemAllGroupList();
		$data['Category'] = $this->K1InventoryModel->GetCategoryList();
		$data['Brands'] = $this->K1InventoryModel->GetBrandList();
		$this->load->view('admin/K1Inventory/ProfitableReport',$data);
    }
    
    public function getSubCategoryByCategory()
    {
        $categoryID = $this->input->post('categoryid');
	    $CategoryList = $this->K1InventoryModel->GetSubcategoryData($categoryID);
		echo json_encode($CategoryList);
    }
    
    public function GetProfitableReport()
    {
        if (!has_permission_new('k1ProfitableReport', '', 'view')) {
            access_denied('access_denied');
        }
        $filterdata = array(
            'FromDate' =>$this->input->post('FromDate'),
            'ToDate' =>$this->input->post('ToDate'),
            'CenterID'  => $this->input->post('CenterID'),
            'Brand' => $this->input->post('Brand'),
            'CategoryID' => $this->input->post('CategoryID'),
            'ItemID'=>$this->input->post('ItemID'),
        );
        
        $FromDate = $filterdata['FromDate'];
        $ToDate = $filterdata['ToDate'];
        $CenterID = $filterdata['CenterID'];
        $Brand = $filterdata['Brand'];
        $CategoryID =  $filterdata['CategoryID'];
        $ItemID = $filterdata['ItemID'];
        $GetProfitableListData = $this->K1InventoryModel->GetProfitableList($CenterID,$Brand,$CategoryID,$ItemID,$FromDate,$ToDate);
        
        usort($GetProfitableListData, function($a, $b) {
            $profitA = $a['SaleAmt'] - $a['PurchAmt'];
            $profitB = $b['SaleAmt'] - $b['PurchAmt'];
            return $profitB <=> $profitA;  
        });
        
        $html = '<table class="table table-bordered">';
        $html .= '<thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>SubCategory</th>
                        <th>Sale Amt</th>
                        <th>Sale Qty</th>
                        <th>Average Sale Rate</th>
                        <th>Average Purch Rate</th>
                        <th>Purch Amt</th>
                        <th>Profit Amt</th>
                        <th>Profit(%)</th>';
                       
        $html .= ' 
                </tr>
              </thead><tbody>';
    
        $sr = 1;
        $TotalSaleAmt = 0;
        $TotalSaleQty = 0;
        $TotalPurchaseAmount = 0;
        $ProfitAmt = 0;
        $TotalProfitPercentage = 0;
        foreach ($GetProfitableListData as $List) 
        {
            $AvgsaleRate = ($List['SaleQty'] > 0) 
                ? round($List['SaleAmt'] / $List['SaleQty'], 2)
                : 0;
                
            $Profit = $List['SaleAmt'] - $List['PurchAmt'];
            
            $ProfitPercent = ($List['PurchAmt'] > 0)
            ? round(($Profit / $List['PurchAmt']) * 100, 2)
            : 0;
            
            $html .= '<tr>';
            $html .= '<td style="text-align:center;">' . $sr++ . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($List['ItemID']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['ProductName']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['BrandName']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['CategoryName']) . '</td>';
            $html .= '<td>' . htmlspecialchars($List['SubCategoryName']) . '</td>';
            $html .= '<td style="text-align:right;">' . htmlspecialchars($List['SaleAmt']) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($List['SaleQty']) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($AvgsaleRate) . '</td>';
            $html .= '<td style="text-align:center;">' . number_format($List['AvgRate'], 2, '.', ',') . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($List['PurchAmt'], 2, '.', ',') . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($Profit, 2, '.', ',') . '</td>';
            $html .= '<td style="text-align:center;">' . $ProfitPercent . '</td>';
            $html .= '</tr>';
            
            $TotalSaleAmt += $List['SaleAmt'];
            $TotalSaleQty += $List['SaleQty'];
            $TotalPurchaseAmount += $List['PurchAmt'];
            $ProfitAmt += $Profit;
        }
        
        $TotalProfitPercentage = ($ProfitAmt / $TotalPurchaseAmount)*100;
        
        $html .= '<tr style="font-weight:bold; background:#f2f2f2;">';
        $html .= '<td colspan="6" style="text-align:right;">TOTAL</td>';
        
        $html .= '<td style="text-align:right;">' . number_format($TotalSaleAmt, 2) . '</td>';
        $html .= '<td style="text-align:right;">' . number_format($TotalSaleQty, 2) . '</td>';
        $html .= '<td>'.'</td>';
        $html .= '<td>'.'</td>';
        $html .= '<td style="text-align:right;">' . number_format($TotalPurchaseAmount, 2) . '</td>';
        $html .= '<td style="text-align:right;">' . number_format($ProfitAmt, 2) . '</td>';
        $html .= '<td style="text-align:right;">' . number_format($TotalProfitPercentage, 2) . '</td>';
        $html .= '</tr>';
        
        $html .= '</tbody></table>';
    	echo $html;
        exit;
    }
    
    public function export_profitable_list()
    {
        if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        if($this->input->post())
        {
			$company_data = $this->misc_reports_model->get_company_detail();
			$fy = $this->session->userdata('finacial_year');
			
			$filterdata = array(
                'FromDate' =>$this->input->post('FromDate'),
                'ToDate' =>$this->input->post('ToDate'),
                'CenterID'  => $this->input->post('CenterID'),
                'Brand'=>$this->input->post('Brand'),
                'CategoryID'=> $this->input->post('CategoryID'),
                'ItemID'=>$this->input->post('ItemID'),
            );
        
            $CenterID = $filterdata['CenterID'];
            $Brand = $filterdata['Brand'];
            $CategoryID = $filterdata['CategoryID'];
            $ItemID = $filterdata['ItemID'];
            $FromDate = $filterdata['FromDate'];
            $ToDate = $filterdata['ToDate'];
            $GetProfitableListData = $this->K1InventoryModel->GetProfitableList($CenterID,$Brand,$CategoryID,$ItemID,$FromDate,$ToDate);
            
            usort($GetProfitableListData, function($a, $b) {
                $profitA = $a['SaleAmt'] - $a['PurchAmt'];
                $profitB = $b['SaleAmt'] - $b['PurchAmt'];
                return $profitB <=> $profitA;  
            });
                
            $CenterIDs = $this->input->post('CenterID');
			if($CenterIDs){
			    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
    			$Center = array();
    			foreach($CenterList as $val){
    				array_push($Center,$val["CenterName"]);
    			}
    			$Center_name = implode(', ', $Center);
			}else{
			    $Center_name = "All";
			}
			
			if ($Brand) {
                $BrandList = $this->K1InventoryModel->GetBrandListdata($Brand);
                $barndgrouplist = array();
                foreach ($BrandList as $val) {
                    array_push($barndgrouplist, $val["BrandName"]);
                }
                $Brand_Name = implode(',', $barndgrouplist);
            } else {
                $Brand_Name = "All";
            }
            
			if ($CategoryID) {
                $CategoryList = $this->K1InventoryModel->GetCategoryGroupList($CategoryID);
                $GroupLists = array();
                foreach ($CategoryList as $val) {
                    array_push($GroupLists, $val["SubcategoryName"]);
                }
                $Category_Name = implode(',', $GroupLists);
            } else {
                $Category_Name = "All";
            }
			
    		if ($ItemID) {
                $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemID);
                $GroupList = array();
                foreach ($ItemGroupList as $val) {
                    array_push($GroupList, $val["SubCategoryName"]);
                }
                $item_group_name = implode(',', $GroupList);
            } else {
                $item_group_name = "All";
            }
            
			$writer = new XLSXWriter();
			
            $company_name = array($company_data->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_data->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $company_addr);
			
			$msg2 = "From Date: " . $FromDate . "   To Date: " . $ToDate;
            $filter2 = array($msg2);
            $writer->markMergedCell('Sheet1', 2, 0, 2, 12);  
            $writer->writeSheetRow('Sheet1', $filter2);
			
			$msg3 = "Center Name: ".$Center_name;
			$filter3 = array($msg3);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter3);
			$j = 5;
			
			$msg4 = "Brand Name: ".$Brand_Name;
			$filter4 = array($msg4);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter4);
			$j = 6;
			
			$msg5 = "Category: ".$Category_Name;
			$filter5 = array($msg5);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter5);
			$j = 7;
			
			$msg6 = "SubCategory: ".$item_group_name;
			$filter6 = array($msg6);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  
			$writer->writeSheetRow('Sheet1', $filter6);
			$j = 8;
			
            $list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk[] = "Sr.No";
			$set_col_tk[] = "Item ID";
			$set_col_tk[] = "Item Name";
			$set_col_tk[] = "Brand";
			$set_col_tk[] = "Category";
			$set_col_tk[] = "SubCategory";
			$set_col_tk[] = "Sale Amt";
			$set_col_tk[] = "Sale Qty";
			$set_col_tk[] = "Average Sale Rate";
			$set_col_tk[] = "Average Purch Rate";
			$set_col_tk[] = "Purch Amt";
			$set_col_tk[] = "Profit Amt";
			$set_col_tk[] = "Profit(%)";
			
            $writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			$SrNo = 1;
			$TotalSaleAmt = 0;
            $TotalSaleQty = 0;
            $TotalPurchaseAmount = 0;
            $ProfitAmt = 0;
            $TotalProfitPercentage = 0;
        
            foreach ($GetProfitableListData as $List) {
                $AvgsaleRate = ($List['SaleQty'] > 0) 
                ? round($List['SaleAmt'] / $List['SaleQty'], 2)
                : 0;
                
                $Profit = $List['SaleAmt'] - $List['PurchAmt'];
            
                $ProfitPercent = ($List['PurchAmt'] > 0)
                ? round(($Profit / $List['PurchAmt']) * 100, 2)
                : 0;
                
                $list_add = [];
				$list_add[] = $SrNo;
                $list_add[] = $List['ItemID'];
                $list_add[] = $List['ProductName'];
                $list_add[] = $List['BrandName'];
                $list_add[] = $List['CategoryName'];
                $list_add[] = $List['SubCategoryName'];
                $list_add[] = number_format($List['SaleAmt'], 2, '.', '');
                $list_add[] = number_format($List['SaleQty'], 2, '.', '');
                $list_add[] = number_format($AvgsaleRate, 2, '.', '');
                $list_add[] = number_format($List['AvgRate'], 2, '.', '');
                $list_add[] = number_format($List['PurchAmt'], 2, '.', '');
                $list_add[] = number_format($Profit, 2, '.', '');
                $list_add[] = number_format($ProfitPercent, 2, '.', '');
                $SrNo++;
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$TotalSaleAmt += $List['SaleAmt'];
                $TotalSaleQty += $List['SaleQty'];
                $TotalPurchaseAmount += $List['PurchAmt'];
                $TotalProfitAmt += $Profit;
            }
            
            if ($TotalPurchaseAmount > 0) {
                $TotalProfitPercent = round(($TotalProfitAmt / $TotalPurchaseAmount) * 100, 2);
            }
            
            $total_row = [];
            $total_row[] = ""; 
            $total_row[] = ""; 
            $total_row[] = "TOTAL";
            $total_row[] = ""; 
            $total_row[] = ""; 
            $total_row[] = ""; 
            $total_row[] = number_format($TotalSaleAmt, 2);
            $total_row[] = number_format($TotalSaleQty, 2);
            $total_row[] = ""; 
            $total_row[] = ""; 
            $total_row[] = number_format($TotalPurchaseAmount, 2);
            $total_row[] = number_format($TotalProfitAmt, 2);
            $total_row[] = number_format($TotalProfitPercent, 2);
            
            $writer->writeSheetRow('Sheet1', $total_row);
			
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Most Profitable Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;  
		}
    }
		
		
}												