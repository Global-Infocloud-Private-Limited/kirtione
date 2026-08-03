<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class K1InventoryMaster extends ClientsController
{
    use ValidatesContact;
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('after_clients_area_init', $this);
        $this->load->model('taxes_model');
        $this->load->model('hsn_master_model');
        $this->load->model('ItemModel');
		$this->load->model('PurchaseModel');
        $this->load->helper('url', 'form');		
		$this->load->model('misc_reports_model');
		$this->load->model('K1InventoryModel');
    }
	
	public function index()
    {
        $LogInUser = $this->session->userdata('AccountID');
		$data['AccountID'] = $LogInUser;
        $data['company_detail'] = $this->ItemModel->get_company_detail();      
        $data['CenterMaster'] = $this->K1InventoryModel->GetCenterList();
		$data['title'] = "Stock Position";
        $this->data($data);
        $this->view('Inventory/StockPosition');
        $this->layout();
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
        $LogInUser = $this->session->userdata('AccountID');
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
        $panel = "vendor";
        
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
		$html .= '<th align="left" rowspan="3">SrNo</th>';
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
                $colspan = 12;
                $html .= '<th align="center" colspan="'.$colspan.'">'.$val["CenterName"].'</th>';
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
        			$html .= '<th align="center" >Purch Qty</th>';
        			$html .= '<th align="center" >Purch Rtn Qty</th>';
        			$html .= '<th align="center" >Sale Qty</th>';
        			$html .= '<th align="center" >Sale Rtn Qty</th>';
        			$html .= '<th align="center" >Sale Dmg Rtn Qty</th>';
        			$html .= '<th align="center" >Inward Qty</th>';
        			$html .= '<th align="center" >Lean Qty</th>';
        			$html .= '<th align="center" >In Qty</th>';
        			$html .= '<th align="center" >Out Qty</th>';
        			$html .= '<th align="center" >Adj Qty</th>';
        			$html .= '<th align="center" >Cls Qty</th>';
                /*}
            }*/
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $SrNo = 1;
        
        foreach ($AllItemList as $key => $value) {
			$TotalClosing = 0;
			$html .= '<tr>';
			$html .= '<td>'.$SrNo.'</td>';
			$html .= '<td>'.$value["ProductID"].'</td>';
			$html .= '<td>'.$value["ProductName"].'</td>';
			$html .= '<td>'.$value["unit"].'</td>';
			foreach($CenterList as $key=>$val)
			{
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
				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty = 0; $BalQty = 0;
    			foreach($GetPreTransaction as $PTKey=>$PTVal){
    				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
    					$SaleQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
    					$SaleRtnQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
    					$PurchQty += $PTVal["TotalQty"];
    				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "PR" && $PTVal["TType2"] == "PURCHASE RETURN"){
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
				
				$PurchQty = 0;$InwardQty = 0; $LeanQty = 0 ;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
    			foreach($StockData as $stockKey=>$stockVal){
    				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
    					$SaleQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
    					$SaleRtnQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
    					$SaleDmgRtnQty += $stockVal["TotalQty"];
    				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
    					$PurchQty += $stockVal["TotalQty"];
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
				if($PurchQty>0){
				    $html .= '<td align="center">'.number_format($PurchQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($PurchRtnQty>0){
				    $html .= '<td align="center">'.number_format($PurchRtnQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($SaleQty>0){
				    $html .= '<td align="center">'.number_format($SaleQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($SaleRtnQty>0){
				    $html .= '<td align="center">'.number_format($SaleRtnQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($SaleDmgRtnQty>0){
				    $html .= '<td align="center">'.number_format($SaleDmgRtnQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($InwardQty>0){
				    $html .= '<td align="center">'.number_format($InwardQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($LeanQty>0){
				    $html .= '<td align="center">'.number_format($LeanQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($InQty>0){
				    $html .= '<td align="center">'.number_format($InQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}
				if($OutQty>0){
				    $html .= '<td align="center">'.number_format($OutQty, 2, '.', '').'</td>';
				}else{
				    $html .= '<td align="center"></td>';
				}if($AdjQty>0){
				    $html .= '<td align="center">'.number_format($AdjQty, 2, '.', '').'</td>';
				}else{
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
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        echo json_encode($html);
        die;
    }
    
    /*public function export_stock_report()
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
            //$StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$ItemGroup);
            
            $fy = $this->session->userdata('finacial_year');
            $First_date_FY = '20'.$fy.'-04-01';
            $FromDate = to_sql_date($this->input->post('from_date'));
            if($First_date_FY != $FromDate){
                $day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
                $GetPreTransaction = $this->K1InventoryModel->GetPreStockData($filterdata,$ItemGroup,$day_before);
            }
            
            $StockData = $this->K1InventoryModel->GetStockData($filterdata,$ItemGroup);
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
    		$j = 4;
    		$list_add = [];
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    		$ColFrom = 3;
    		$ColTo = 8;
    		foreach($CenterList as $key=>$val){
                $list_add[] = $val["CenterName"];
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = $ColFrom, $end_row = $j, $end_col = $ColTo);  //merge cells
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $ColFrom += 6;
                $ColTo += 6;
            }
            $list_add[] = "";
            $writer->writeSheetRow('Sheet1', $list_add);
            
            $set_col_tk = [];
    		$set_col_tk[] = "ItemID";
    		$set_col_tk[] = "ItemName";
    		$set_col_tk[] = "Unit";
    		foreach($CenterList as $key=>$val){
    		    $set_col_tk[$val["CenterID"]."OPN Qty"] = "OPN Qty";
                $set_col_tk[$val["CenterID"]."PURCH Qty"] = "PURCH Qty";
                $set_col_tk[$val["CenterID"]."SALE Qty"] = "SALE Qty";
                $set_col_tk[$val["CenterID"]."IN Qty"] = "IN Qty";
                $set_col_tk[$val["CenterID"]."OUT Qty"] = "OUT Qty";
                $set_col_tk[$val["CenterID"]."Cls Qty"] = "Cls Qty";
    		}
    		$set_col_tk["Total Closing"] = "Total Closing";
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
    		
    		foreach ($AllItemList as $key => $value) {
                $TotalClosing = 0;
    		    $list_add = [];
                $list_add[] = $value["ProductID"];
                $list_add[] = $value["ProductName"];
                $list_add[] = $value["unit"];
                foreach($CenterList as $key=>$val){
    				$Opn = 0;
    				$Purch = '';
    				$Sale = '';
    				$IN = '';
    				$Out = '';
    				$Bal = 0;
    				// Get Before From date Transaction Qty
    				foreach($GetPreTransaction as $PTKey=>$PTVal){
    					if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
    						$Opn += $PTVal["TotalQty"];
    					}
    					if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
    						$Opn -= $PTVal["TotalQty"];
    					}
    				}
    				// Get In Between date Transaction 
    				foreach($StockData as $stockKey=>$stockVal){
    					if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
    						$Purch = $stockVal["TotalQty"];
    					}
    					if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
    						$Sale = $stockVal["TotalQty"];
    					}
    				}
    				$Bal = $Opn + $Purch - $Sale + $IN - $Out;
    				$list_add[] = number_format($Opn, 2, '.', '');
    				$list_add[] = number_format($Purch, 2, '.', '');
    				$list_add[] = number_format($Sale, 2, '.', '');
    				$list_add[] = number_format($IN, 2, '.', '');
    				$list_add[] = number_format($Out, 2, '.', '');
    				$list_add[] = number_format($Bal, 2, '.', '');
    				$TotalClosing += $Bal;
                }
                $list_add[] = number_format($TotalClosing, 2, '.', '');
                $writer->writeSheetRow('Sheet1', $list_add);
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
    }*/
    
    public function export_stock_report()
    {
        
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
            $panel = "vendor";
            
            $StockOQtyData = $this->K1InventoryModel->GetItemWiseCenterWiseOpnQty($filterdata,$ItemGroup,$panel);
            
            $fy = $this->session->userdata('finacial_year');
            $First_date_FY = '20'.$fy.'-04-01';
            $FromDate = to_sql_date($this->input->post('from_date'));
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
    	
    		$list_add = [];
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    		$ColFrom = 3;
    		$ColTo = 8;
    		foreach($CenterList as $key=>$val){
                $list_add[] = $val["CenterName"];
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = $ColFrom, $end_row = $j, $end_col = $ColTo);  //merge cells
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $ColFrom += 12;
                $ColTo += 12;
            }
            $list_add[] = "";
            $writer->writeSheetRow('Sheet1', $list_add);
            
            $set_col_tk = [];
    		$set_col_tk[] = "ItemID";
    		$set_col_tk[] = "ItemName";
    		$set_col_tk[] = "Unit";
    		foreach($CenterList as $key=>$val){
    		    $set_col_tk[$val["CenterID"]."OPN Qty"] = "OPN Qty";
                $set_col_tk[$val["CenterID"]."PURCH Qty"] = "PURCH Qty";
                $set_col_tk[$val["CenterID"]."Purch Rtn Qty"] = "Purch Rtn Qty";
                $set_col_tk[$val["CenterID"]."SALE Qty"] = "SALE Qty";
                $set_col_tk[$val["CenterID"]."Sale Rtn Qty"] = "Sale Rtn Qty";
                $set_col_tk[$val["CenterID"]."Sale Dmg Rtn Qty"] = "Sale Dmg Rtn Qty";
                $set_col_tk[$val["CenterID"]."Inward Qty"] = "Inward Qty";
                $set_col_tk[$val["CenterID"]."Lean Qty"] = "Lean Qty";
                $set_col_tk[$val["CenterID"]."IN Qty"] = "IN Qty";
                $set_col_tk[$val["CenterID"]."OUT Qty"] = "OUT Qty";
                $set_col_tk[$val["CenterID"]."Adj Qty"] = "Adj Qty";
                $set_col_tk[$val["CenterID"]."Cls Qty"] = "Cls Qty";
    		}
    		$set_col_tk["Total Closing"] = "Total Closing";
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
    		
    		foreach ($AllItemList as $key => $value) {
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
			    
    				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty=0;$BalQty = 0;
        			foreach($GetPreTransaction as $PTKey=>$PTVal){
        				if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
        					$SaleQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
        					$PurchQty += $PTVal["TotalQty"];
        				}else if($value["ProductID"] == $PTVal["ItemID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "PR" && $PTVal["TType2"] == "PURCHASE RETURN"){
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
        			
        			$PurchQty = 0;$InwardQty = 0;$LeanQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
        			foreach($StockData as $stockKey=>$stockVal){
        				if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "O" && $stockVal["TType2"] == "SALE"){
        					$SaleQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "FRESH RETURN"){
        					$SaleRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "SR" && $stockVal["TType2"] == "DAMAGE RETURN"){
        					$SaleDmgRtnQty += $stockVal["TotalQty"];
        				}else if($value["ProductID"] == $stockVal["ItemID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
        					$PurchQty += $stockVal["TotalQty"];
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
    			
    				$list_add[] = number_format($OpnQty, 2, '.', '');
    				$list_add[] = number_format($PurchQty, 2, '.', '');
    				$list_add[] = number_format($PurchRtnQty, 2, '.', '');
    				$list_add[] = number_format($SaleQty, 2, '.', '');
    				$list_add[] = number_format($SaleRtnQty, 2, '.', '');
    				$list_add[] = number_format($SaleDmgRtnQty, 2, '.', '');
    				$list_add[] = number_format($InwardQty, 2, '.', '');
    				$list_add[] = number_format($LeanQty, 2, '.', '');
    			    $list_add[] = number_format($InQty, 2, '.', '');
    			    $list_add[] = number_format($OutQty, 2, '.', '');
    			    $list_add[] = number_format($AdjQty, 2, '.', '');
    			    $list_add[] = number_format($BalQty, 2, '.', '');
    			
    				$TotalClosing += $BalQty;
                }
                $list_add[] = number_format($TotalClosing, 2, '.', '');
                $writer->writeSheetRow('Sheet1', $list_add);
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

//======================== AS On Date Stock Report ==================================
	public function AsOndateStockReport() 
	{
		$LogInUser = $this->session->userdata('AccountID');
    	$data['AccountID'] = $LogInUser;
		$data['Category'] = $this->K1InventoryModel->GetItemAllGroupList();
		
		$data['product'] = $this->K1InventoryModel->GetItemGroupListbyproduct();
		
		$data['CenterMaster'] = $this->K1InventoryModel->GetCenterList();
		
		$data['RootCompany'] = $this->K1InventoryModel->GetRootCompany();
		$data['company_detail'] = $this->ItemModel->get_company_detail();
		$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();
		$data['title'] = "As ON Date Stock Reports";
		$this->data($data);
		$this->view('Inventory/OndateStockReport');
		$this->layout();
	}
	
	public function GetAsondateStockReport()
    {
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
		$panel = 'vendor'; 
		$AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
		
		$ItemWiseOpnQty = $this->K1InventoryModel->GetItemWiseOpningQty($filterdata,$panel);
		$ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata,$panel);
		
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
		    $PackingQty = $Val["PackingQty"];
			$html .= '<tr>';
			$html .= '<td>'.$SrNo.'</td>';
			$html .= '<td>'.$Val["ProductID"].'</td>';
			$html .= '<td>'.$Val["ProductName"].'</td>';
			$html .= '<td align="center">'.$Val["unit"].'</td>';
			$html .= '<td align="center">'.$Val["PackingQty"].'</td>';
			$opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0; $LeanQty = 0;$BalQty = 0;
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
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "PR" && $stockval["TType2"] == "PURCHASE RETURN"){
					$PurchRtnQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
					$InQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
					$OutQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
					$InwardQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "X"){
					$AdjQty += $stockval["TotalQty"];
				}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK"){
                    $LeanQty += $stockval["TotalQty"];
                }
				
			}
			$BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
			$html .= '<td align="right">'.number_format($BalQty, 2, '.', '').'</td>';
			$html .= '</tr>';
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
//======================= AsOn Date Stock in Vendor Dashboard ==================	
	public function GetAsondateStockChartData()
    {
        $filterdata = array(
            'from_date' => $this->input->post('on_date'),
            'ItemGroup'  => $this->input->post('ItemGroup'),
            'CenterID'  => $this->input->post('CenterID'),
            'PartyID'  => $this->input->post('PartyID'),
        );
    
        $panel = 'vendor'; 
        $AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
        
        $ItemWiseOpnQty = $this->K1InventoryModel->GetItemWiseOpningQty($filterdata, $panel);
        $ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata, $panel);
    
        $result = [];
    
        foreach ($AllItemList as $Key => $Val) {
            $opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
            $SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
            $AdjQty = 0; $InQty = 0; $OutQty = 0;$LeanQty = 0;
    
             $centerWiseMap = [];
            foreach ($ItemWiseOpnQty as $vopnQty) {
                if ($vopnQty["ItemID"] == $Val["ProductID"]) {
                    $opnQty = $vopnQty["TotalOpnQty"];
                }
            }
    
            foreach ($ASOndateStockData as $stockval) {
                if ($Val["ProductID"] != $stockval["ItemID"]) continue;
                
                $center = $stockval["CenterName"] ?? 'Unknown';
                if (!isset($centerWiseMap[$center])) {
                    $centerWiseMap[$center] = 0;
                }
                switch ($stockval["TType"]) {
                    case "O":
                        if ($stockval["TType2"] == "SALE") $SaleQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] -= $stockval["TotalQty"];
                        break;
                    case "SR":
                        if ($stockval["TType2"] == "FRESH RETURN") $SaleRtnQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        break;
                    case "P":
                        if ($stockval["TType2"] == "Purchase") $PurchQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        break;
                    case "PR":
                        if ($stockval["TType2"] == "PURCHASE RETURN") $PurchRtnQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] -= $stockval["TotalQty"];
                        break;
                    case "T":
                        if ($stockval["TType2"] == "IN") $InQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        if ($stockval["TType2"] == "OUT") $OutQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] -= $stockval["TotalQty"];
                        break;
                    case "I":
                        if ($stockval["TType2"] == "INWARD") $InwardQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        break;
                    case "X":
                        $AdjQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] -= $stockval["TotalQty"];
                        break;
                    case "L":
                        if ($stockval["TType2"] == "LIENMARK") $LeanQty += $stockval["TotalQty"];
                        $centerWiseMap[$center] -= $stockval["TotalQty"];
                        break;
                }
            }
    
            $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
            if($BalQty > 0){
                $CenterWiseData = [];
                foreach ($centerWiseMap as $centerName => $centerQty) {
                    $CenterWiseData[] = [
                        'CenterName' => $centerName,
                        'Qty' => round($centerQty, 2)
                    ];
                }
    
                $result[] = [
                    'ItemName' => $Val["ProductName"],
                    'Qty' => round($BalQty, 2),
                    'CenterWiseData' => $CenterWiseData
                ];
            }
            
        }
    
        echo json_encode($result);
    }
//==================== Inward Transaction in Vendor Dashboard ==================	
	public function GetInwardTransactionChartData()
    {
        $filterdata = array(
            'from_date' => $this->input->post('on_date'),
            'ItemGroup'  => $this->input->post('ItemGroup'),
            'CenterID'  => $this->input->post('CenterID'),
            'PartyID'  => $this->input->post('PartyID'),
        );
    
        $panel = 'vendor'; 
        $AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
        
        $ItemWiseOpnQty = $this->K1InventoryModel->GetItemWiseOpningQty($filterdata, $panel);
        $ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata, $panel);
    
        $result = [];
    
        foreach ($AllItemList as $Key => $Val) {
            $opnQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0;
            $SaleQty = 0; $SaleRtnQty = 0; $PrdQty = 0; $IssueQty = 0;
            $AdjQty = 0; $InQty = 0; $OutQty = 0;$LeanQty = 0;
    
             $centerWiseMap = [];
            foreach ($ItemWiseOpnQty as $vopnQty) {
                if ($vopnQty["ItemID"] == $Val["ProductID"]) {
                    $opnQty = $vopnQty["TotalOpnQty"];
                }
            }
    
            foreach ($ASOndateStockData as $stockval) {
                if ($Val["ProductID"] != $stockval["ItemID"]) continue;
    
                switch ($stockval["TType"]) {
                    case "I":
                        if ($stockval["TType2"] == "INWARD") $InwardQty += $stockval["TotalQty"];
                        $center = $stockval["CenterName"] ?? 'Unknown';
                        if (!isset($centerWiseMap[$center])) {
                            $centerWiseMap[$center] = 0;
                        }
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        break;
                }
            }        
            if($InwardQty > 0){
                $CenterWiseData = [];
                foreach ($centerWiseMap as $centerName => $centerQty) {
                    $CenterWiseData[] = [
                        'CenterName' => $centerName,
                        'Qty' => round($centerQty, 2)
                    ];
                }
    
                $result[] = [
                    'ItemName' => $Val["ProductName"],
                    'Qty' => round($InwardQty, 2),
                    'CenterWiseData' => $CenterWiseData
                ];
            }
        }
        echo json_encode($result);
    }

//==================== Inward Transaction in Vendor Dashboard ==================	
	public function GetLeanTransactionChartData()
    {
        $filterdata = array(
            'from_date' => $this->input->post('on_date'),
            'ItemGroup'  => $this->input->post('ItemGroup'),
            'CenterID'  => $this->input->post('CenterID'),
            'PartyID'  => $this->input->post('PartyID'),
        );
    
        $panel = 'vendor'; 
        $AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
        $ASOndateStockData = $this->K1InventoryModel->GetASOndateStockData($filterdata, $panel);
    
        $result = [];
    
        foreach ($AllItemList as $Key => $Val) {
            $LeanQty = 0;
            $centerWiseMap = [];
            foreach ($ASOndateStockData as $stockval) {
                if ($Val["ProductID"] != $stockval["ItemID"]) continue;
    
                switch ($stockval["TType"]) {
                    case "L":
                        if ($stockval["TType2"] == "LIENMARK") $LeanQty += $stockval["TotalQty"];
                        $center = $stockval["CenterName"] ?? 'Unknown';
                        if (!isset($centerWiseMap[$center])) {
                            $centerWiseMap[$center] = 0;
                        }
                        $centerWiseMap[$center] += $stockval["TotalQty"];
                        break;
                }
            }        
            if($LeanQty > 0){
                $CenterWiseData = [];
                foreach ($centerWiseMap as $centerName => $centerQty) {
                    $CenterWiseData[] = [
                        'CenterName' => $centerName,
                        'Qty' => round($centerQty, 2)
                    ];
                }
    
                $result[] = [
                    'ItemName' => $Val["ProductName"],
                    'Qty' => round($LeanQty, 2),
                    'CenterWiseData' => $CenterWiseData
                ];
            }
        }
        echo json_encode($result);
    }
		
//======================== Itemwise Stock Report ==================================	
    	public function ItemWiseStock() 
    	{
    	    $data['product'] = $this->K1InventoryModel->GetItemGroupListbyproduct();
    	    $data['CenterMaster'] = $this->K1InventoryModel->GetCenterList();
    	    $this->data($data);
    	    $this->view('Inventory/ItemWiseStock');
			$this->layout();
    	}
    	
    	public function GetItemWiseStockReport()
		{
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
			$panel = "vendor";
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
			$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty =0;$BalQty = 0;
			foreach($GetPreTransaction as $PTKey=>$PTVal){
				if($PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
					$SaleQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
					$SaleRtnQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
					$PurchQty += $PTVal["TotalQty"];
				}else if($PTVal["TType"] == "PR" && $PTVal["TType2"] == "PURCHASE RETURN"){
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
				
				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$SaleDmgRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty =0; $BalQty = 0;
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
					}else if($row_date == $stockval["Date"] && $stockval["TType"] == "PR" && $stockval["TType2"] == "PURCHASE RETURN"){
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
				foreach($PartyList as $val){
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
				
				$panel = 'kirti';
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
				if (!is_array($PartyIDs)) {
                    $PartyIDs = $PartyIDs ? array($PartyIDs) : [];
                }
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
				$PurchQty = 0;$InwardQty = 0;$LeanQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach($GetPreTransaction as $PTKey=>$PTVal){
					if($PTVal["TType"] == "O" && $PTVal["TType2"] == "SALE"){
						$SaleQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "SR" && $PTVal["TType2"] == "FRESH RETURN"){
						$SaleRtnQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
						$PurchQty += $PTVal["TotalQty"];
					}else if($PTVal["TType"] == "PR" && $PTVal["TType2"] == "PURCHASE RETURN"){
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
						}else if($row_date == $stockval["Date"] && $stockval["TType"] == "PR" && $stockval["TType2"] == "PURCHASE RETURN"){
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
				$list_add[] = "Total"; 
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
				
				$ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
				if (!empty($ItemGroup)) {
                    $ItemGroupList = $this->K1InventoryModel->GetItemGroupList($ItemGroup);
                    $GroupList = array();
                    foreach ($ItemGroupList as $val) {
                        $GroupList[] = $val["SubcategoryName"];
                    }
                    $item_group_name = !empty($GroupList) ? implode(', ', $GroupList) : 'ALL';
                } else {
                    $item_group_name = 'ALL';
                }
				
				$CenterIDs = $this->input->post('CenterID');
				if (!empty($CenterIDs)) {
                    $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
                    $Center = array();
                    foreach ($CenterList as $val) {
                        $Center[] = $val["CenterName"];
                    }
                    $Center_name = !empty($Center) ? implode(', ', $Center) : 'ALL';
                } else {
                    $Center_name = 'ALL';
                }
				$panel = "vendor";
				
				$AllItemList = $this->K1InventoryModel->GetAllItemList($filterdata);
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
				
				$all_filters = "Filters - Item Group: $item_group_name , Center: $Center_name , Party: $Party_name";
                // Write combined filter row
                $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);
                $writer->writeSheetRow('Sheet1', array($all_filters));
                $j = 4;
				
                $list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$set_col_tk = [];
				$set_col_tk[] = "SrNo";
				$set_col_tk[] = "Item ID";
				$set_col_tk[] = "Item Name";
				$set_col_tk[] = "UOM";
				$set_col_tk[] = "Packing Qty";
				$set_col_tk[] = "Qty(Loose)";				
                $writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$SrNo = 1;
                foreach($AllItemList as $Key=>$Val)
				{
                    $list_add = [];
					$list_add[] = $SrNo;
                    $list_add[] = $Val["ProductID"];
                    $list_add[] = $Val["ProductName"];
                    $list_add[] = $Val["unit"];
                    $list_add[] = $Val["PackingQty"];
                    $opnQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$LeanQty = 0;$BalQty = 0;
                    foreach($ASOndateStockData as $stockkey=>$stockval){
                        if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
							$SaleQty += $stockval["TotalQty"];
							}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
							$PurchQty += $stockval["TotalQty"];
							}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
							$InQty += $stockval["TotalQty"];
							}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
							$OutQty += $stockval["TotalQty"];
							}else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
							$InwardQty += $stockval["TotalQty"];
						    }else if($Val["ProductID"] == $stockval["ItemID"] && $stockval["TType"] == "L" && $stockval["TType2"] == "LIENMARK"){
							$LeanQty += $stockval["TotalQty"];
						}
					}
                    $BalQty = $opnQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty - $LeanQty;
                    $list_add[] = number_format($BalQty, 2, '.', '');
					
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
		
		public function GetCommisionData()
		{
			$filterdata = array(
			'centername' => $this->input->post('centername'),
			'filtervendor'  => $this->input->post('filtervendor'),
			'filterItemCode'  => $this->input->post('filterItemCode')
			);
			
		    $Commisiondata = $this->K1InventoryModel->GetFilterwiseCommisionData($filterdata);
		    
		    $html = '<table class="table table-bordered" id="filtertable">';
            $html .= '<thead><tr>
                        <th>ItemID</th>
                        <th>Item Name</th>
                        <th>Center Name</th>
                        <th>Item Rate</th>
                        <th>Commision(%)</th>
                        <th>Commision Amount</th>
                      </tr></thead><tbody>';

            if (!empty($Commisiondata)) {
                foreach ($Commisiondata as $row) {
                    $CommissionAmt = ($row['rate'] * $row['Percent']) / 100;
                    $html .= '<tr>
                                <td>' . htmlspecialchars($row['ItemID']) . '</td>
                                <td>' . htmlspecialchars($row['ProductName']) . '</td>
                                <td>' . htmlspecialchars($row['CenterName']) . '</td>
                                <td>' . htmlspecialchars($row['rate']) . '</td>
                                <td>' . htmlspecialchars($row['Percent']) . '</td>
                                <td>' . number_format($CommissionAmt, 2) . '</td>
                              </tr>';
                }
            } else {
                $html .= '<tr><td colspan="6" class="text-center">No data found</td></tr>';
            }

            $html .= '</tbody></table>';
        
            echo $html;
            exit;
		}
    
}