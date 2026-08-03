<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Misc_reports extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('misc_reports_model');
        $this->load->model('sale_reports_model');
		$this->load->model('accounts_master_model');
    }

    /* Start Stock Position report code */
    
    public function stock_position()
    {
        if (!has_permission_new('stock_position', '', 'view')) {
            access_denied('access_denied');
        }
        $data['main_item_group'] = $this->misc_reports_model->get_main_item_group();
        $data['CenterMaster'] = $this->misc_reports_model->GetCenterList();
        $data['PartyMaster'] = $this->misc_reports_model->GetPartyList();
        $data['title'] = "Stock Reports";
        $this->load->view('admin/misc_reports/stock_reports', $data);
    }
	public function stock_position_reportchart()
    {
        if (!has_permission_new('stock_position', '', 'view')) {
            access_denied('access_denied');
        }
        $data['main_item_group'] = $this->misc_reports_model->get_main_item_group();
        $data['CenterMaster'] = $this->misc_reports_model->GetCenterList();
        $data['PartyMaster'] = $this->misc_reports_model->GetPartyList();
        $data['title'] = "Stock Reports";
        $this->load->view('admin/misc_reports/stock_reports_charts', $data);
    }
    
    public function get_stock_data()
    {
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'ItemGroup'  => $this->input->post('ItemGroup'),
           'CenterID'  => $this->input->post('CenterID'),
           'GodownID'  => $this->input->post('GodownID'),
           'PartyID'  => $this->input->post('PartyID'),
           'Service_type'  => $this->input->post('Service_type')
        );
        $ItemGroup = $this->input->post('ItemGroup');
        $CenterIDs = $this->input->post('CenterID');
        $GodownID = $this->input->post('GodownID');
        $ItemMainGroup = $this->input->post('ItemMainGroup');
        $AllItemList = $this->misc_reports_model->GetItemList($filterdata,$ItemGroup);
        
        $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
        $CenterWiseGodownList = $this->misc_reports_model->GetWHListByCenterID($CenterIDs,$GodownID);
        /**/
        $company_data = $this->misc_reports_model->get_company_detail();
        $StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$ItemGroup);
        
        $fy = $this->session->userdata('finacial_year');
        $First_date_FY = '20'.$fy.'-04-01';
        $FromDate = to_sql_date($this->input->post('from_date'));
        if($First_date_FY != $FromDate){
            $day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
            $GetPreTransaction = $this->misc_reports_model->GetPreStockData($filterdata,$ItemGroup,$day_before);
        }
        /*echo json_encode($GetPreTransaction);
        die;*/
        $StockData = $this->misc_reports_model->GetStockData($filterdata,$ItemGroup);
        
        
        $html = '<span style="color:red;">Note : All quantities are in MT</span>';
        $html .= '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
        $html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
        $html .= '<input type="hidden" name="filterdate" id="filterdate" value="Stock Position of '.$item_maingroup_name->name.'(Stock Value with GST) '.$this->input->post('from_date').' to '.$this->input->post('to_date').' - Stock in Cases">';
        $html .= '<input type="hidden" name="rate_base" id="rate_base" value="Rates based on : State - UP & Dist.Type - SS ">';
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
        $html .= '<th align="left" rowspan="3">ItemID</th>';
        $html .= '<th align="left" rowspan="3">ItemName</th>';
        foreach($CenterList as $key=>$val){
            $i = 0;
            foreach($CenterWiseGodownList as $whKey=>$whVal){
                if($val["CenterID"] == $whVal["center"]){
                    $i++;
                }
            }
            if($i>0){ // check warehouse is available or not i.e. if available then show center name as column 
                $colspan = $i * 6;
                $html .= '<th align="center" colspan="'.$colspan.'">'.$val["CenterName"].'</th>';
            }
        }
        $html .= '</tr>';
        
        $html .= '<tr>';
        foreach($CenterList as $key=>$val){
            foreach($CenterWiseGodownList as $whKey=>$whVal){
                if($val["CenterID"] == $whVal["center"]){
                    $html .= '<th align="center" colspan="6">'.$whVal["w_name"].'</th>';
                }
            }
        }
        $html .= '</tr>';
        
        $html .= '<tr>';
        foreach($CenterList as $key=>$val){
            foreach($CenterWiseGodownList as $whKey=>$whVal){
                if($val["CenterID"] == $whVal["center"]){
                    
                    $html .= '<th align="center" >OPN Qty</th>';
                    $html .= '<th align="center" >PURCH Qty</th>';
                    $html .= '<th align="center" >SALE Qty</th>';
                    $html .= '<th align="center" >IN Qty</th>';
                    $html .= '<th align="center" >OUT Qty</th>';
                    $html .= '<th align="center" >CLS Qty</th>';
                }
            }
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $SrNo = 1;
        
        foreach ($AllItemList as $key => $value) {
            $html .= '<tr>';
            $html .= '<td>'.$SrNo.'</td>';
            $html .= '<td>'.$value["ItemID"].'</td>';
            $html .= '<td>'.$value["ItemName"].'</td>';
            foreach($CenterList as $key=>$val){
                foreach($CenterWiseGodownList as $whKey=>$whVal){
                    if($val["CenterID"] == $whVal["center"]){
                        $Opn = 0;
                        $Purch = '';
                        $Sale = '';
                        $IN = '';
                        $Out = '';
                        $Bal = 0;
                        // Get Opening Balance Qty
                        foreach($StockOQtyData as $OKey=>$OVal){
                            if($value["ItemID"] == $OVal["ItemID"] && $whVal["AccountID"]==$OVal["GodownID"] && $val["CenterID"]==$OVal["CenterID"] ){
                                $Opn += $stockVal["TotalQty"];
                            }
                        }
                        // Get Before From date Transaction Qty
                        foreach($GetPreTransaction as $PTKey=>$PTVal){
                            if($value["ItemID"] == $PTVal["ItemID"] && $whVal["AccountID"]==$PTVal["GodownID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "P" && $PTVal["TType2"] == "Purchase"){
                                $Opn += $PTVal["TotalQty"];
                            }
                            if($value["ItemID"] == $PTVal["ItemID"] && $whVal["AccountID"]==$PTVal["GodownID"] && $val["CenterID"]==$PTVal["CenterID"] && $PTVal["TType"] == "S" && $PTVal["TType2"] == "Sale"){
                                $Opn -= $PTVal["TotalQty"];
                            }
                        }
                        // Get In Between date Transaction 
                        foreach($StockData as $stockKey=>$stockVal){
                            if($value["ItemID"] == $stockVal["ItemID"] && $whVal["AccountID"]==$stockVal["GodownID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "P" && $stockVal["TType2"] == "Purchase"){
                                $Purch = $stockVal["TotalQty"];
                            }
                            if($value["ItemID"] == $stockVal["ItemID"] && $whVal["AccountID"]==$stockVal["GodownID"] && $val["CenterID"]==$stockVal["CenterID"] && $stockVal["TType"] == "S" && $stockVal["TType2"] == "Sale"){
                                $Sale = $stockVal["TotalQty"];
                            }
                        }
                        $Bal = $Opn + $Purch - $Sale + $IN - $Out;
                        $html .= '<td align="center">'.number_format($Opn, 2, '.', '').'</td>';
                        $html .= '<td align="center">'.number_format($Purch, 2, '.', '').'</td>';
                        $html .= '<td align="center">'.number_format($Sale, 2, '.', '').'</td>';
                        $html .= '<td align="center">'.number_format($IN, 2, '.', '').'</td>';
                        $html .= '<td align="center">'.number_format($Out, 2, '.', '').'</td>';
                        $html .= '<td align="center">'.number_format($Bal, 2, '.', '').'</td>';
                    }
                }
            }
            $html .= '</tr>';
            $SrNo++;
        }
        $html .= '</tbody>';
        $html .= '</table>';
        echo json_encode($html);
        die;
    }
	
	public function get_stock_data_chart()
	 {		
		$filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'ItemGroup'  => $this->input->post('ItemGroup'),
           'CenterID'  => $this->input->post('CenterID'),
           'GodownID'  => $this->input->post('GodownID'),
           'PartyID'  => $this->input->post('PartyID'),
           'Service_type'  => $this->input->post('Service_type'),
		   'ChartType' => $this->input->post('ChartType')
        );
        $ItemGroup = $this->input->post('ItemGroup');
        $CenterIDs = $this->input->post('CenterID');
        $GodownID = $this->input->post('GodownID');
        $ItemMainGroup = $this->input->post('ItemMainGroup');
        $AllItemList = $this->misc_reports_model->GetItemList($filterdata,$ItemGroup);
        
        $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
        $CenterWiseGodownList = $this->misc_reports_model->GetWHListByCenterID($CenterIDs,$GodownID);
        /**/
        $company_data = $this->misc_reports_model->get_company_detail();
        $StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$ItemGroup);
        
        $fy = $this->session->userdata('finacial_year');
        $First_date_FY = '20'.$fy.'-04-01';
        $FromDate = to_sql_date($this->input->post('from_date'));
        if($First_date_FY != $FromDate){
            $day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
            $GetPreTransaction = $this->misc_reports_model->GetPreStockData($filterdata,$ItemGroup,$day_before);
        }
        /*echo json_encode($GetPreTransaction);
        die;*/
       $result = $this->misc_reports_model->GetStockData_Chart($filterdata,$ItemGroup);
	     	
			 
				// $result = $this->VillageModel->village_wise_chart($from_date,$to_date,$District,$Taluka,$ReportFor,$Staff_Id,$ChartType);
				
			    $data = [
				'ChartData' => $result['ChartData'],
				];
			
			echo json_encode($data);
			
			// echo json_encode($this->VillageModel->village_wise_chart($from_date,$to_date,$District,$Taluka,$ReportFor,$Staff_Id));
	 }
    // Get Warehouse By CenterID
    public function GetWHListByCenterID()
    {
        $CenterID = $this->input->post('CenterID');
        $WhList = $this->misc_reports_model->GetWHListByCenterID($CenterID);
        echo json_encode($WhList);
    }
    
    public function StockCummulative()
    {
        if (!has_permission_new('stockCummulative', '', 'view')) {
            access_denied('access_denied');
        }
        $data['main_item_group'] = $this->misc_reports_model->get_main_item_group();
        $data['GodownData'] = $this->misc_reports_model->GetGodownData();
        $data['title'] = "Stock Reports";
        $this->load->view('admin/misc_reports/stockCommulative', $data);
    }
    
    
    
    /* Get Item Group */
    public function get_item_group()
    {
        $main_item_group_id = $this->input->post('main_item_group_id');
        $item_group = $this->misc_reports_model->get_item_group($main_item_group_id);
        
        echo json_encode($item_group);
    }
    
    public function get_item_groupFR_StkP()
     {
        $data = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'main_item_group_id'  => $this->input->post('main_item_group_id')
          );
        $mainGroupID = $this->input->post('main_item_group_id');
      $data = $this->misc_reports_model->get_item_group($mainGroupID);
      echo json_encode($data);
    }
    
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
               'Service_type'  => $this->input->post('Service_type')
            );
            $ItemGroup = $this->input->post('ItemGroup');
            $CenterIDs = $this->input->post('CenterID');
            $GodownID = $this->input->post('GodownID');
            $ItemMainGroup = $this->input->post('ItemMainGroup');
            $AllItemList = $this->misc_reports_model->GetItemList($filterdata,$ItemGroup);
            
            $CenterList = $this->misc_reports_model->GetCenterList($CenterIDs);
            $CenterWiseGodownList = $this->misc_reports_model->GetWHListByCenterID($CenterIDs,$GodownID);
            /**/
            $company_data = $this->misc_reports_model->get_company_detail();
            $StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$ItemGroup);
            
            $fy = $this->session->userdata('finacial_year');
            $First_date_FY = '20'.$fy.'-04-01';
            $FromDate = to_sql_date($this->input->post('from_date'));
            if($First_date_FY != $FromDate){
                $day_before = date( 'Y-m-d', strtotime($FromDate . ' -1 day' ));
                $GetPreTransaction = $this->misc_reports_model->GetPreStockData($filterdata,$ItemGroup,$day_before);
            }
            /*echo json_encode($GetPreTransaction);
            die;*/
            $StockData = $this->misc_reports_model->GetStockData($filterdata,$ItemGroup);
            
    		
    		$writer = new XLSXWriter();
    		
    		$company_name = array($company_data->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		
    		$address = $company_data->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		
    		$msg = "Stock Report of : ".$item_maingroup_name->name." FROM " .$this->input->post('from_date')." To ".$this->input->post('to_date')." ";
    		$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		$j++;
    		
    		 
    		$msg2 = "Item Group: ".$item_group_name;
    		$filter2 = array($msg2);
    		$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter2);
    		$j++;
    		
    		$list_add = [];
    		$list_add[] = "SrNo";
    		$list_add[] = "ItemID";
    		$list_add[] = "ItemName";
    	    
    		foreach($CenterList as $key=>$val){
                $i = 0;
                foreach($CenterWiseGodownList as $whKey=>$whVal){
                    if($val["CenterID"] == $whVal["center"]){
                        $i++;
                    }
                }
                if($i>0){ // check warehouse is available or not i.e. if available then show center name as column 
                    $colspan = $i * 6;
                    //$html .= '<th align="center" colspan="'.$colspan.'">'.$val["CenterName"].'</th>';
                    $list_add[] = $val["CenterName"];
                }
            }
    		$writer->writeSheetRow('Sheet1', $list_add);
    		
    		
    		$list_add = [];
    		$list_add[] = "SrNo";
    		$list_add[] = "ItemID";
    		$list_add[] = "ItemName";
    		foreach($CenterList as $key=>$val){
                foreach($CenterWiseGodownList as $whKey=>$whVal){
                    if($val["CenterID"] == $whVal["center"]){
                        $html .= '<th align="center" colspan="6">'.$whVal["w_name"].'</th>';
                        $list_add[] = $whVal["w_name"];
                    }
                }
            }
    		
    		$writer->writeSheetRow('Sheet1', $list_add);
    		
    		$set_col_tk = [];
    		$set_col_tk[] = "SrNo";
    		$set_col_tk[] = "ItemID";
    		$set_col_tk[] = "ItemName";
    		foreach($CenterList as $key=>$val){
                foreach($CenterWiseGodownList as $whKey=>$whVal){
                    if($val["CenterID"] == $whVal["center"]){
                        $set_col_tk["OPN Qty"] = "OPN Qty";
                        $set_col_tk["PURCH Qty"] = "PURCH Qty";
                        $set_col_tk["SALE Qty"] = "SALE Qty";
                        $set_col_tk["IN Qty"] = "IN Qty";
                        $set_col_tk["OUT Qty"] = "OUT Qty";
                        $set_col_tk["Cls Qty"] = "Cls Qty";
                    }
                }
            }
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
    		
            
    	    $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
        foreach ($AllItemList as $key => $value) {
            $rate = 0;
            
            if($value["case_qty"] == "0"){
                $CaseQty = 1;
            }else{
                $CaseQty = $value["case_qty"];
            }
            
            $OQTY = 0;
            $PurchQty = 0;
            $PurchQtyCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
                    $PurchQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PurchQty !== '0'){
                $PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
                $PurchQtyCasesSum += $PurchQtyCases;
            }
            
            $PurchRtnQty = 0;
            $PurchRtnQtyCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
                    $PurchRtnQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PurchRtnQty !== '0'){
                $PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
                $PurchRtnQtyCasesSum += $PurchRtnQtyCases;
            }
            
            $IssueQty = 0;
            $IssueQtyCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
                    $IssueQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($IssueQty !== '0'){
                $IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
                $IssueQtyCasesSum += $IssueQtyCases;
            }
            
            $PRDQty = 0;
            $PRDCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
                    $PRDQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PRDQty !== '0'){
                $PRDCases = floatval($PRDQty) / floatval($CaseQty);
                $PRDCasesSum += $PRDCases;
            }
        
            
            $SalesQty = 0;
            $SalesCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
                    $SalesQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($SalesQty !== '0'){
                $SalesCases = floatval($SalesQty) / floatval($CaseQty);
                $SalesCasesSum += $SalesCases;
            }
            
            $SalesRtnQty = 0;
            $SalesRtnCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh" )){
                    $SalesRtnQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($SalesRtnQty !== '0'){
                $SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
                $SalesRtnCasesSum += $SalesRtnCases;
            }
            
            $AdjQty = 0;
            $AdjCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment")){
                    $AdjQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($AdjQty !== '0'){
                $AdjCases = floatval($AdjQty) / floatval($CaseQty);
                $AdjCasesSum += $AdjCases;
            }
            
            $GOQty = 0;
            $GOCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
                    $GOQty += $value1['BilledQty'];
                    $GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($GOQty >0){
                $GOCases = floatval($GOQty) / floatval($CaseQty);
                $GOCasesSum += $GOCases;
            }
            
            $GIQty = 0;
            $GICases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["item_code"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
                    $GIQty += $value1['BilledQty'];
                    $GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($GIQty >0){
                $GICases = floatval($GIQty) / floatval($CaseQty);
                $GICasesSum += $GICases;
            }
            
            if($from_date == '2022-04-01'){
                $OQTYCases = floatval($value["OQty"]) / floatval($CaseQty);
            }else{
                $OQtySum = 0;
                $OQtySum += floatval($value["OQty"]);
                foreach ($StockOQtyData as $keyOQty => $valueOQty) {
                    
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "P"){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "N"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "A"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "B"){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"]) && $valueOQty['TType'] == "X"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
                        $OQtySum += $valueOQty['billsum'];
                    }
                }
                $OQTYCases = floatval($OQtySum) / floatval($CaseQty);
            }
            
            $OQTYCasesSum += $OQTYCases;
            $BQty =    $OQTYCases +   $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases - $GOCases + $GICases;
            $BQtySum += $BQty;    
            if(floatval($OQTYCases) == '0.00' && floatval($PurchQtyCases) == "0.00" && floatval($PurchRtnQtyCases) == "0.00" && floatval($IssueQtyCases) == "0.00" && floatval($PRDCases) == "0.00" && floatval($SalesCases) == "0.00" && floatval($SalesRtnCases) == "0.00" && floatval($AdjCases) == "0.00" && floatval($GOCases) == "0.00" && floatval($GICases) == "0.00"){
                
            }else{
            $list_add = [];
            $list_add[] = $value["item_code"];
            $list_add[] = $value["description"];
            $list_add[] = $value["case_qty"];
            $list_add[] = $value["unit"];
            $list_add[] = round($OQTYCases,2);
            if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                $list_add[] = round((float)($PurchQtyCases), 2);
            }
            
            if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                $list_add[] = round((float)($PurchRtnQtyCases), 2);
            }
            
            if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                $list_add[] = round((float)($IssueQtyCases), 2);
            }
            
            if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                $list_add[] = round((float)($PRDCases), 2);
            }
            
            if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                $list_add[] = round((float)($SalesCases), 2);
            }
            
            if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                $list_add[] = round((float)($SalesRtnCases), 2);
            }
            
            if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                $list_add[] = round((float)($AdjCases), 2);
            }
            if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                $list_add[] = round((float)($GOCases), 2);
            }
            if($GICasesSumC > 0 || $GICasesSumC < 0){
                $list_add[] = round((float)($GICases), 2);
            }
            
            if($value["assigned_rate"] == null || $value["assigned_rate"] == "" || $value["assigned_rate"] == "0.00"){
                    //$rate = 0;
            }else{
                $rate = $value["assigned_rate"];
            }
            if($value["case_qty"] == '0' || $value["case_qty"] == ''){
                    $stockqty = round($BQty) * 1;
                }else{
                    $stockqty = round($BQty) * $value["case_qty"];
                }
                
                $stockValue = $stockqty * $rate;
                
                $list_add[] = round((float)($BQty), 2); 
                $list_add[] = round((float)($rate), 2);
                $list_add[] = round((float)($stockValue), 2);
                $stockValue_sum = $stockValue_sum + $stockValue;
                $writer->writeSheetRow('Sheet1', $list_add);
            }  
        }
    	   
                $list_add = [];
                $list_add[] = "";
    			$list_add[] = "Total";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = round((float)($OQTYCasesSum), 2);
                
            if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                $list_add[] = round((float)($PurchQtyCasesSum), 2); 
            }
                
            if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                $list_add[] = round((float)($PurchRtnQtyCasesSum), 2); 
            }    
                
            if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                $list_add[] = round((float)($IssueQtyCasesSum), 2); 
            }    
                
            if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                $list_add[] = round((float)($PRDCasesSum), 2); 
            }    
                
            if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                $list_add[] = round((float)($SalesCasesSum), 2); 
            }    
                
            if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                $list_add[] = round((float)($SalesRtnCasesSum), 2); 
            }    
                
            if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                $list_add[] = round((float)($AdjCasesSum), 2); 
            }
            
            if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                $list_add[] = round((float)($GOCasesSum), 2); 
            }
            if($GICasesSumC > 0 || $GICasesSumC < 0){
                $list_add[] = round((float)($GICasesSum), 2); 
            }
            
            $list_add[] = round((int) $BQtySum, 2); 
            $list_add[] = ""; 
            $list_add[] = round((float)($stockValue_sum), 2);; 
            $writer->writeSheetRow('Sheet1', $list_add);
          
    	
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
    
     
    public function getCummulativeStock()
     {
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
          );
          
        $from_date = to_sql_date($this->input->post('from_date'));
        $item_group = $this->input->post('item_group');
        $item_main_group = $this->input->post('item_main_group');
        $item_group_name = $this->misc_reports_model->get_item_group_name($item_group);
        $item_maingroup_name = $this->misc_reports_model->get_mainitem_group($item_main_group);
        $selected_company = $this->session->userdata('root_company');
        $company_data = $this->misc_reports_model->get_company_detail();
        $GodownData = $this->misc_reports_model->GetGodownData();
        
        $AllItemList = $this->misc_reports_model->GetItemListCommulative($filterdata,$item_group);
        $CommulativeData = $this->misc_reports_model->getCommulativeStockData($filterdata,$item_group);
       
        $html = '';
        $html .= '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
        $html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
        $html .= '<input type="hidden" name="filterdate" id="filterdate" value="Stock Position of '.$item_maingroup_name->name.'(Stock Value with GST) '.$this->input->post('from_date').' to '.$this->input->post('to_date').' - Stock in Cases">';
        $html .= '<input type="hidden" name="rate_base" id="rate_base" value="Rates based on : State - UP & Dist.Type - SS ">';
        $html .= '<input type="hidden" name="filter_group" id="filter_group" value="Item Group : </b>'.$item_group_name.' ">';
            
            
        $html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
        $html .= '<thead style="font-size:11px;">';
        $html .= '<tr style="display:none;">';
        $html .= '<th colspan="10"><b>'.$company_data->company_name.'</b></th>';
        $html .= '</tr>';
        
        $html .= '<tr style="display:none;">';
        $html .= '<th colspan="10"><b>'.$company_data->address.'</b></th>';
        $html .= '</tr>';
        
        $html .= '<tr style="display:none;">';
        $html .= '<th colspan="10"><b>Stock Position of '.$item_maingroup_name->name.'(Stock Value with GST) '.$this->input->post('from_date').' to '.$this->input->post('to_date').' - Stock in Cases</b> </th>';
        $html .= '</tr>';
        
        $html .= '<tr style="display:none;">';
        $html .= '<th colspan="10"><b>Item Group : </b>'.$item_group_name.'</th>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<th align="left">SrNo</th>';
        $html .= '<th align="left">ItemID</th>';
        $html .= '<th align="left">ItemName</th>';
        $html .= '<th align="center">Pkg</th>';
        $html .= '<th align="center">U</th>';
        foreach ($GodownData as $key => $value) {
            $html .= '<th align="center">'.$value["AccountID"].'</th>';
        }
        $html .= '<th align="center">Total.Qty</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $SrNo = 1;
        foreach ($AllItemList as $key => $value) {
            $SumCases = 0;
            $html .= '<tr>';
            $html .= '<td>'.$SrNo.'</td>';
            $html .= '<td>'.$value["item_code"].'</td>';
            $html .= '<td>'.$value["description"].'</td>';
            $html .= '<td align="center">'.$value["case_qty"].'</td>';
            $html .= '<td align="center">'.$value["unit"].'</td>';
            foreach ($GodownData as $key1 => $value1) {
                $OQty = 0;
                $QTYCases = 0;
                
                $PurchQtyC = 0;
                $PurchRtnQtyC = 0;
                $IssueQtyC = 0;
                $PRDQtyC = 0;
                $SalesQtyC = 0;
                $SalesRtnQtyC = 0;
                $AdjQtyC = 0;
                $GOQtyC = 0;
                $GIQtyC = 0;
                foreach ($CommulativeData as $keydata => $valuedata) {
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"]){
                        $OQty = $valuedata['OQty'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "P" && $valuedata["TType2"] == "Purchase"){
                        $PurchQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "N" && $valuedata["TType2"] == "PurchaseReturn"){
                        $PurchRtnQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "A" && $valuedata["TType2"] == "Issue"){
                        $IssueQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "B" && $valuedata["TType2"] == "Production"){
                        $PRDQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "O" && $valuedata["TType2"] == "Order"){
                        $SalesQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "R" && $valuedata["TType2"] == "Fresh"){
                        $SalesRtnQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && ($valuedata["TType"] == "X" && $valuedata["TType2"] == "Free Distribution" || $valuedata["TType"] == "X" && $valuedata["TType2"] == "Promotional Activity" || $valuedata["TType"] == "X" && $valuedata["TType2"] == "Stock Adjustment")){
                        $AdjQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "T" && $valuedata["TType2"] == "Out"){
                        $GOQtyC += $valuedata['billsum'];
                    }
                    if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "T" && $valuedata["TType2"] == "In"){
                        $GIQtyC += $valuedata['billsum'];
                    }
                    $BQty =    $OQty +   $PurchQtyC - $PurchRtnQtyC - $IssueQtyC + $PRDQtyC - $SalesQtyC + $SalesRtnQtyC - $AdjQtyC  - $GOQtyC + $GIQtyC;
                }
                if($BQty == '0'){
                    $QTYCases = '';
                }else{
                    $QTYCases = floatval($BQty) / floatval($value["case_qty"]);
                    $QTYCases = number_format($QTYCases, 2, '.', '');
                    $SumCases += $QTYCases;
                }
                
                $html .= '<td align="center">'.$QTYCases.'</td>';
            }
            $html .= '<td>'.number_format($SumCases, 2, '.', '').'</td>';
            $SrNo++;
        }
        $html .= '</tbody>';
        $html .= '<table>';
        echo json_encode($html);
        die;
    }
    
    public function exportCummulativeStock()
     {
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
          );
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        if($this->input->post()){
            $from_date = to_sql_date($this->input->post('from_date'));
            $item_group = $this->input->post('item_group');
            $item_main_group = $this->input->post('item_main_group');
            $item_group_name = $this->misc_reports_model->get_item_group_name($item_group);
            $item_maingroup_name = $this->misc_reports_model->get_mainitem_group($item_main_group);
            $selected_company = $this->session->userdata('root_company');
            $company_data = $this->misc_reports_model->get_company_detail();
            $GodownData = $this->misc_reports_model->GetGodownData();
            
            $AllItemList = $this->misc_reports_model->GetItemListCommulative($filterdata,$item_group);
            $CommulativeData = $this->misc_reports_model->getCommulativeStockData($filterdata,$item_group);
           
            $writer = new XLSXWriter();
            $company_name = array($company_data->company_name);
        	$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
        	$writer->writeSheetRow('Sheet1', $company_name);
        	
        	$address = $company_data->address;
        	$company_addr = array($address,);
        	$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
        	$writer->writeSheetRow('Sheet1', $company_addr);
        		
        	$msg = "Stock Report of : ".$item_maingroup_name->name."(Stock Value with GST): " .$this->input->post('from_date')." to ".$this->input->post('to_date')." -  Stock in Cases ";
        	$filter = array($msg);
        	$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
        	$writer->writeSheetRow('Sheet1', $filter);
            
            $msg2 = "Item Group: ".$item_group_name;
        	$filter2 = array($msg2);
        	$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
        	$writer->writeSheetRow('Sheet1', $filter2);
        
            $set_col_tk = [];
            $set_col_tk["ItemID"] =  'ItemID';
            $set_col_tk["ItemName"] =  'ItemName';
            $set_col_tk["Pkg"] =  'Pkg';
            $set_col_tk["Unit"] =  'Unit';
            foreach ($GodownData as $key => $value) {
                $set_col_tk[$value["AccountID"]] =  $value["AccountID"];
            }
            $set_col_tk["Total"] =  'Total';
            $writer_header = $set_col_tk;
        	$writer->writeSheetRow('Sheet1', $writer_header);
        	
            foreach ($AllItemList as $key => $value) {
                $SumCases = 0;
                $list_add = [];
                $list_add[] = $value["item_code"];
                $list_add[] = $value["description"];
                $list_add[] = $value["case_qty"];
                $list_add[] = $value["unit"];
                foreach ($GodownData as $key1 => $value1) {
                    $OQty = 0;
                    $QTYCases = 0;
                    
                    $PurchQtyC = 0;
                    $PurchRtnQtyC = 0;
                    $IssueQtyC = 0;
                    $PRDQtyC = 0;
                    $SalesQtyC = 0;
                    $SalesRtnQtyC = 0;
                    $AdjQtyC = 0;
                    $GOQtyC = 0;
                    $GIQtyC = 0;
                    foreach ($CommulativeData as $keydata => $valuedata) {
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"]){
                            $OQty = $valuedata['OQty'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "P" && $valuedata["TType2"] == "Purchase"){
                            $PurchQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "N" && $valuedata["TType2"] == "PurchaseReturn"){
                            $PurchRtnQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "A" && $valuedata["TType2"] == "Issue"){
                            $IssueQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "B" && $valuedata["TType2"] == "Production"){
                            $PRDQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "O" && $valuedata["TType2"] == "Order"){
                            $SalesQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "R" && $valuedata["TType2"] == "Fresh"){
                            $SalesRtnQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && ($valuedata["TType"] == "X" && $valuedata["TType2"] == "Free Distribution" || $valuedata["TType"] == "X" && $valuedata["TType2"] == "Promotional Activity" || $valuedata["TType"] == "X" && $valuedata["TType2"] == "Stock Adjustment")){
                            $AdjQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "T" && $valuedata["TType2"] == "Out"){
                            $GOQtyC += $valuedata['billsum'];
                        }
                        if($valuedata['GodownID'] == $value1['AccountID'] && $valuedata['ItemID'] == $value["item_code"] && $valuedata["TType"] == "T" && $valuedata["TType2"] == "In"){
                            $GIQtyC += $valuedata['billsum'];
                        }
                        $BQty =    $OQty +   $PurchQtyC - $PurchRtnQtyC - $IssueQtyC + $PRDQtyC - $SalesQtyC + $SalesRtnQtyC - $AdjQtyC  - $GOQtyC + $GIQtyC;
                    }
                    if($BQty == '0'){
                        $QTYCases = '';
                    }else{
                        $QTYCases = floatval($BQty) / floatval($value["case_qty"]);
                        $QTYCases = number_format($QTYCases, 2, '.', '');
                        $SumCases += $QTYCases;
                    }
                    $list_add[] = $QTYCases;
                }
                $list_add[] = number_format($SumCases, 2, '.', '');
                 $writer->writeSheetRow('Sheet1', $list_add);
            }
            
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
        		foreach($files as $file){
        			if(is_file($file)) {
        				unlink($file); 
        			}
        		}
        		$filename = 'StockCommulativeReport.xlsx';
        		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
        		echo json_encode([
        			'site_url'          => site_url(),
        			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
        		]);
        	die;
        }	
        
    }
    
    /*public function get_stock_data()
    {
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'GodownID'  => $this->input->post('GodownID')
          );
          
        $from_date = to_sql_date($this->input->post('from_date'));
        $item_group = $this->input->post('item_group');
        $item_main_group = $this->input->post('item_main_group');
        $item_group_name = $this->misc_reports_model->get_item_group_name($item_group);
        $item_maingroup_name = $this->misc_reports_model->get_mainitem_group($item_main_group);
        $selected_company = $this->session->userdata('root_company');
        $company_data = $this->misc_reports_model->get_company_detail();
        $AllItemList = $this->misc_reports_model->GetItemList($filterdata,$item_group);
        $StockData = $this->misc_reports_model->GetStockData($filterdata,$item_group);
        $StockOQtyData = $this->misc_reports_model->get_item_open_qty($filterdata,$item_group);
        
            $PurchQtyCasesSumC = 0;
            $PurchRtnQtyCasesSumC = 0;
            $IssueQtyCasesSumC = 0;
            $PRDCasesSumC = 0;
            $SalesCasesSumC = 0;
            $SalesRtnCasesSumC = 0;
            $AdjCasesSumC = 0;
            $GOCasesSumC = 0;
            $GICasesSumC = 0;
        foreach ($AllItemList as $key => $value) {
            
            if($value["case_qty"] == "0" || $value["case_qty"] == ""){
                $CaseQty = 1;
            }else{
                $CaseQty = $value["case_qty"];
            }
            $OQTY = 0;
            $PurchQtyC = 0;
            $PurchQtyCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["ItemID"] == $value1["ItemID"] && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
                    $PurchQtyC += $value1['BilledQty'];
                }
            }
            if($PurchQtyC !== '0'){
                $PurchQtyCasesC = floatval($PurchQtyC) / floatval($CaseQty);
                $PurchQtyCasesSumC += $PurchQtyCasesC;
            }
            
            $PurchRtnQtyC = 0;
            $PurchRtnQtyCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
                    $PurchRtnQtyC += $value1['BilledQty'];
                }
            }
            if($PurchRtnQtyC !== '0'){
                $PurchRtnQtyCasesC = floatval($PurchRtnQtyC) / floatval($CaseQty);
                $PurchRtnQtyCasesSumC += $PurchRtnQtyCasesC;
            }
            
            $IssueQtyC = 0;
            $IssueQtyCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
                    $IssueQtyC += $value1['BilledQty'];
                }
            }
            if($IssueQtyC !== '0'){
                $IssueQtyCasesC = floatval($IssueQtyC) / floatval($CaseQty);
                $IssueQtyCasesSumC += $IssueQtyCasesC;
            }
            
            $PRDQtyC = 0;
            $PRDCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
                    $PRDQtyC += $value1['BilledQty'];
                }
            }
            if($PRDQtyC !== '0'){
                $PRDCasesC = floatval($PRDQtyC) / floatval($CaseQty);
                $PRDCasesSumC += $PRDCasesC;
            }
        
            
            $SalesQtyC = 0;
            $SalesCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
                    $SalesQtyC += $value1['BilledQty'];
                }
            }
            if($SalesQtyC !== '0'){
                $SalesCasesC = floatval($SalesQtyC) / floatval($CaseQty);
                $SalesCasesSumC += $SalesCasesC;
            }
            
            $SalesRtnQtyC = 0;
            $SalesRtnCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
                    $SalesRtnQtyC += $value1['BilledQty'];
                }
            }
            if($SalesRtnQtyC !== '0'){
                $SalesRtnCasesC = floatval($SalesRtnQtyC) / floatval($CaseQty);
                $SalesRtnCasesSumC += $SalesRtnCasesC;
            }
            
            $AdjQtyC = 0;
            $AdjCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment")){
                    $AdjQtyC += $value1['BilledQty'];
                }
            }
            if($AdjQtyC !== '0'){
                $AdjCasesC = floatval($AdjQtyC) / floatval($CaseQty);
                $AdjCasesSumC += $AdjCasesC;
            }
            $GOQtyC = 0;
            $GOCasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["ItemID"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
                    $GOQtyC += $value1['BilledQty'];
                }
            }
            if($GOQtyC >0){
                $GOCasesC = floatval($GOQtyC) / floatval($CaseQty);
                $GOCasesSumC += $GOCasesC;
            }
            
            $GIQtyC = 0;
            $GICasesC = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["ItemID"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
                    $GIQtyC += $value1['BilledQty'];
                }
            }
            if($GIQtyC >0){
                $GICasesC = floatval($GIQtyC) / floatval($CaseQty);
                $GICasesSumC += $GICasesC;
            }
        }
        
            $html = '<span style="color:red;">Note : All quantities are in Quintals</span>';
            $html .= '<input type="hidden" name="comp_name" id="comp_name" value="'.$company_data->company_name.'">';
            $html .= '<input type="hidden" name="comp_addr" id="comp_addr" value="'.$company_data->address.'">';
            $html .= '<input type="hidden" name="filterdate" id="filterdate" value="Stock Position of '.$item_maingroup_name->name.'(Stock Value with GST) '.$this->input->post('from_date').' to '.$this->input->post('to_date').' - Stock in Cases">';
            $html .= '<input type="hidden" name="rate_base" id="rate_base" value="Rates based on : State - UP & Dist.Type - SS ">';
            $html .= '<input type="hidden" name="filter_group" id="filter_group" value="Item Group : </b>'.$item_group_name.' ">';
            
            $html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
            $html .= '<thead style="font-size:11px;">';
            $html .= '<tr style="display:none;">';
            $html .= '<th colspan="9"><b>'.$company_data->company_name.'</b></th>';
            
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<th colspan="9"><b>'.$company_data->address.'</b></th>';
            
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<th colspan="9"><b>Stock Position of '.$item_maingroup_name->name.'(Stock Value with GST) '.$this->input->post('from_date').' to '.$this->input->post('to_date').' - Stock in Cases</b> </th>';
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<th colspan="9"><b>Rates based on : State - UP & Dist.Type - SS </b> </th>';
            
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<th colspan="9"><b>Item Group : </b>'.$item_group_name.'</th>';
            
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<th></th>';
            $html .= '<th></th>';
            $html .= '<th></th>';
            $html .= '<th></th>';
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center"></th>';
                }
            
            $html .= '<th></th>';
            $html .= '<th></th>';
            $html .= '<th></th>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<th align="left">SrNo</th>';
                $html .= '<th align="left">ItemID</th>';
                $html .= '<th align="left">ItemName</th>';
                //$html .= '<th align="center">Pkg</th>';
                $html .= '<th align="center">Unit</th>';
                $html .= '<th align="center">OpenQty</th>';
                if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                    $html .= '<th align="center">PurchQty</th>';
                }
                
                if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                    $html .= '<th align="center">PurchRtn</th>';
                }
                
                if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                    $html .= '<th align="center">IssueQty</th>';
                }
                
                if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                    $html .= '<th align="center">Production</th>';
                }
                
                if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                    $html .= '<th align="center">SalesQty</th>';
                }
                
                if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                    $html .= '<th align="center">SalesRtn</th>';
                }
                
                if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                    $html .= '<th align="center">AdjQty</th>';
                }
                if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                    $html .= '<th align="center">GTOQty</th>';
                }
                if($GICasesSumC > 0 || $GICasesSumC < 0){
                    $html .= '<th align="center">GTIQty</th>';
                }
                $html .= '<th align="center">Bal.Qty</th>';
                $html .= '<th align="center">Rate</th>';
                $html .= '<th align="center">StkValue</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
            $stockValue_sum = 0;
            $SrNo = 1;
        foreach ($AllItemList as $key => $value) {
            $rate = 0;
            $OQTY = 0;
            $PurchQty = 0;
            $PurchQtyCases = 0;
            
            $CaseQty = 1;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
                    $PurchQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PurchQty !== '0'){
                $PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
                $PurchQtyCasesSum += $PurchQtyCases;
            }
            
            $PurchRtnQty = 0;
            $PurchRtnQtyCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
                    $PurchRtnQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PurchRtnQty !== '0'){
                $PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
                $PurchRtnQtyCasesSum += $PurchRtnQtyCases;
            }
            
            $IssueQty = 0;
            $IssueQtyCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
                    $IssueQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($IssueQty !== '0'){
                $IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
                $IssueQtyCasesSum += $IssueQtyCases;
            }
            
            $PRDQty = 0;
            $PRDCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
                    $PRDQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($PRDQty !== '0'){
                $PRDCases = floatval($PRDQty) / floatval($CaseQty);
                $PRDCasesSum += $PRDCases;
            }
            $SalesQty = 0;
            $SalesCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
                    $SalesQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($SalesQty !== '0'){
                $SalesCases = floatval($SalesQty) / floatval($CaseQty);
                $SalesCasesSum += $SalesCases;
            }
            
            $SalesRtnQty = 0;
            $SalesRtnCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
                    $SalesRtnQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($SalesRtnQty !== '0'){
                $SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
                $SalesRtnCasesSum += $SalesRtnCases;
            }
            
            $AdjQty = 0;
            $AdjCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if(strtoupper($value["ItemID"]) == strtoupper($value1["ItemID"]) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment")){
                    $AdjQty += $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($AdjQty !== '0'){
                $AdjCases = floatval($AdjQty) / floatval($CaseQty);
                $AdjCasesSum += $AdjCases;
            }
            
            $GOQty = 0;
            $GOCases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["ItemID"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
                    $GOQty += $value1['BilledQty'];
                    $GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($GOQty >0){
                $GOCases = floatval($GOQty) / floatval($CaseQty);
                $GOCasesSum += $GOCases;
            }
            
            $GIQty = 0;
            $GICases = 0;
            foreach ($StockData as $key1 => $value1) {
                if($value["ItemID"] == $value1["ItemID"] && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
                    $GIQty += $value1['BilledQty'];
                    $GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
                    if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
                        $rate = $value1["SaleRate"];
                    }
                }
            }
            if($GIQty >0){
                $GICases = floatval($GIQty) / floatval($CaseQty);
                $GICasesSum += $GICases;
            }
            
            if($from_date == '2022-04-01'){
                $OQTYCases = floatval($value["OQty"]) / floatval($CaseQty);
            }else{
                $OQtySum = 0;
                $OQtySum += floatval($value["OQty"]);
                foreach ($StockOQtyData as $keyOQty => $valueOQty) {
                    
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "P"){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "N"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "A"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "B"){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
                        $OQtySum += $valueOQty['billsum'];
                    }
                    if(strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"]) && $valueOQty['TType'] == "X"){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"])) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
                        $OQtySum -= $valueOQty['billsum'];
                    }
                    if((strtoupper($valueOQty['ItemID']) == strtoupper($value["ItemID"])) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
                        $OQtySum += $valueOQty['billsum'];
                    }
                }
                $OQTYCases = floatval($OQtySum) / floatval($CaseQty);
            }
            
            $OQTYCasesSum += $OQTYCases;
            $BQty =    $OQTYCases +  $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases  - $GOCases + $GICases;
            $BQtySum += $BQty;    
            if(floatval($OQTYCases) == '0.00' && floatval($PurchQtyCases) == "0.00" && floatval($PurchRtnQtyCases) == "0.00" && floatval($IssueQtyCases) == "0.00" && floatval($PRDCases) == "0.00" && floatval($SalesCases) == "0.00" && floatval($SalesRtnCases) == "0.00" && floatval($AdjCases) == "0.00" && floatval($GOCases) == "0.00" && floatval($GICases) == "0.00"){
                
            }else{
            $html .= '<tr>';
            $html .= '<td>'.$SrNo.'</td>';
            $html .= '<td>'.$value["ItemID"].'</td>';
            $html .= '<td>'.$value["ItemName"].'</td>';
            $html .= '<td align="center">'.$value["unit"].'</td>';
            
            $html .= '<td align="right">'.number_format((float)($OQTYCases), 2, '.', ',') .'</td>';
            if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($PurchQtyCases), 2, '.', ',').'</td>';
            }
            
            if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($PurchRtnQtyCases), 2, '.', ',').'</td>';
            }
            
            if($IssueQtyCasesSumC > 0 || $IssueQtyCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($IssueQtyCases), 2, '.', ',').'</td>';
            }
            
            if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($PRDCases), 2, '.', ',').'</td>';
            }
            
            if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($SalesCases), 2, '.', ',').'</td>';
            }
            
            if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($SalesRtnCases), 2, '.', ',').'</td>';
            }
            
            if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($AdjCases), 2, '.', ',').'</td>';
            }
            if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($GOCases), 2, '.', ',').'</td>';
            }
            if($GICasesSumC > 0 || $GICasesSumC < 0){
                $html .= '<td align="right">'.number_format((float)($GICases), 2, '.', ',').'</td>';
            }
            
            
                if($value["assigned_rate"] == null || $value["assigned_rate"] == "" || $value["assigned_rate"] == "0.00"){
                    //$rate = 0;
                }else{
                    $rate = $value["assigned_rate"];
                }
                
                if($value["case_qty"] == '0' || $value["case_qty"] == ''){
                    $stockqty = round($BQty) * 1;
                }else{
                    $stockqty = round($BQty) * $value["case_qty"];
                }
                
                $stockValue = $stockqty * $rate;
            
            
            $html .= '<td align="right">'.number_format((float)($BQty), 2, '.', ',').'</td>';
            $html .= '<td align="right">'.$rate.'</td>';
            $html .= '<td align="right">'.number_format((float)$stockValue, 2, '.', '').'</td>';
            
            $stockValue_sum = $stockValue_sum + $stockValue;
            $html .= '</tr>';
            $SrNo++;
        }
        }
            $html .= '<tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td ><b>Total</b></td>';
            //$html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td align="right"><b>'.number_format((float)($OQTYCasesSum), 2, '.', ',').'</b></td>';
            if($PurchQtyCasesSumC > 0 || $PurchQtyCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($PurchQtyCasesSum), 2, '.', ',').'</b></td>';
            }
                
            if($PurchRtnQtyCasesSumC > 0 || $PurchRtnQtyCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($PurchRtnQtyCasesSum), 2, '.', ',').'</b></td>';
            }    
                
            if($IssueQtyCasesSumC >0 || $IssueQtyCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($IssueQtyCasesSum), 2, '.', ',').'</b></td>';
            }    
                
            if($PRDCasesSumC > 0 || $PRDCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($PRDCasesSum), 2, '.', ',').'</b></td>';
            }    
                
            if($SalesCasesSumC > 0 || $SalesCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($SalesCasesSum), 2, '.', ',').'</b></td>';
            }    
                
            if($SalesRtnCasesSumC > 0 || $SalesRtnCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($SalesRtnCasesSum), 2, '.', ',').'</b></td>';
            }    
                
            if($AdjCasesSumC > 0 || $AdjCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($AdjCasesSum), 2, '.', ',').'</b></td>';
            }
            if($GOCasesSumC > 0 || $GOCasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($GOCasesSum), 2, '.', ',').'</b></td>';
            }
            if($GICasesSumC > 0 || $GICasesSumC < 0){
                $html .= '<td align="right"><b>'.number_format((float)($GICasesSum), 2, '.', ',').'</b></td>';
            }
             
                $html .= '<td align="right"><b>'.number_format((float)($BQtySum), 2, '.', ',').'</b></td>';
                $html .= '<td align="right"></td>';
                $html .= '<td align="right"><b>'.number_format((float)($stockValue_sum), 2, '.', ',').'</b></td>';
            
            $html .= '</tr>';
            
        // Show Value 
          
            $html .= '</tfoot>';
            $html .= '<table>';
        echo json_encode($html);
        die;
    }*/
     
    /* End Stock Position report code */
     
    // Production reports Code
    public function production_reports(){
    	
    	if (!has_permission_new('production_reports', '', 'view')) {
            access_denied('Access denied ');
        }
        $data['title']          = "Production Reports";
        $data['item_group'] = $this->misc_reports_model->item_division_group();
        //  print_r($data);die;
        $this->load->view('admin/misc_reports/production_reports', $data);
    }
    public function accountlist(){
        // POST data
        $postData = $this->input->post();
        // Get data
        $data = $this->misc_reports_model->getaccounts($postData);
        echo json_encode($data);
    }
    
  public function itemlist(){
        
    // POST data
    $postData = $this->input->post();

    // Get data
    $data = $this->misc_reports_model->itemlist($postData);

    echo json_encode($data);
  }
  
  public function get_account_details()
     {
       $data = array();
        $accountID = $this->input->post('act_id');
        $account_data = $this->misc_reports_model->get_account_details($accountID);
        $staff_data = $this->misc_reports_model->get_staff_details($accountID);
        $data['account_data'] = $account_data;
        $data['staff_data'] = $staff_data;
        echo json_encode($data);
     }
    public function get_item_details()
     {
       
        $ItemID = $this->input->post('ItemID');
        $account_data = $this->misc_reports_model->get_item_details($ItemID);
        echo json_encode($account_data);
     }
    public function export_production_report(){
        	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
     $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'report_type'  => $this->input->post('report_type'),
           'accountID'  => $this->input->post('accountID'),
           'ItemID'  => $this->input->post('ItemID'),
           'source'  => $this->input->post('source')
          );
            $accountID = $this->input->post('accountID');
            $ItemID = $this->input->post('ItemID');
            $accountname = $this->input->post('accountName');
            $Itemname = $this->input->post('Itemname');
          $report_type = $this->input->post('report_type');
        $body_data = $this->misc_reports_model->get_production_for_body_data($filterdata);
    		$selected_company_details    = $this->misc_reports_model->get_company_detail();
    		
    		$writer = new XLSXWriter();
    		
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		
    		$msg = "Production Details Date : ".$this->input->post('from_date')." to " .$this->input->post('to_date');
    			$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		$j++;
    		
               if(!empty($accountID)){
    		$msg1 = "AccountName : ".$accountname;
    		$filter1 = array($msg1);
    		$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter1);
               }
            if(!empty($ItemID)){
            $msg2 = "ItemName: ".Itemname;
    		$filter2 = array($msg2);
    		$writer->markMergedCell('Sheet1', $start_row = 4, $start_col = 0, $end_row = 4, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter2);
    
             }
   
    		
    		
    		$list_add = [];
    		 if($report_type == 1 && empty($ItemID) && empty($accountID)){
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";                
            }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                 $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = ""; 
            }else if(!empty($ItemID) && empty($accountID)){
                 $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";  
            }else if(empty($ItemID) && !empty($accountID)){
                 $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";  
                
            }else if(!empty($ItemID) && !empty($accountID)){
                 $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";               
                $list_add[] = "";    
               
            }
            
            $writer->writeSheetRow('Sheet1', $list_add);
            
        
            $set_col_tk = [];
             if($report_type == 1 && empty($ItemID) && empty($accountID)){
               $set_col_tk["PRDID"] =  'PRDID';
               $set_col_tk["PRDDate"] =  'PRDDate';
               $set_col_tk["AccountName"] =  'AccountName';
               $set_col_tk["ReceipeName"] =  'ReceipeName';
               $set_col_tk["StdQty"] =  'StdQty';
               $set_col_tk["BatchCount"] =  'BatchCount';
               $set_col_tk["Std.F.G.Qty"] =  'Std.F.G.Qty';
               $set_col_tk["F.G.Qty"] =  'ActualF.G.Qty';
               $set_col_tk["Diff.InQty"] =  'Diff.InQty';
               $set_col_tk["Req.Time(min)"] =  'Req.Time(min)';
               $set_col_tk["ActualTime(min)"] =  'ActualTime(min)';
               
            }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
               $set_col_tk["AccountID"] =  'AccountID';
               $set_col_tk["AccountName"] =  'AccountName';
               $set_col_tk["F.G.Qty"] =  'F.G.Qty';
               
            }else if(!empty($ItemID) && empty($accountID)){
                 $set_col_tk["PRDID"] =  'PRDID';
               $set_col_tk["PRDDate"] =  'PRDDate';
               $set_col_tk["AccountName"] =  'AccountName';
               $set_col_tk["StdQty"] =  'StdQty';
               $set_col_tk["BatchCount"] =  'BatchCount';
               $set_col_tk["Std.F.G.Qty"] =  'Std.F.G.Qty';
               $set_col_tk["F.G.Qty"] =  'ActualF.G.Qty';
               $set_col_tk["Diff.InQty"] =  'Diff.InQty';
               $set_col_tk["Req.Time(min)"] =  'Req.Time(min)';
               $set_col_tk["ActualTime(min)"] =  'ActualTime(min)';
                
              
            }else if(empty($ItemID) && !empty($accountID)){
                $set_col_tk["PRDID"] =  'PRDID';
                $set_col_tk["PRDDate"] =  'PRDDate';
                $set_col_tk["RecipeName"] =  'RecipeName';
                $set_col_tk["StdQty"] =  'StdQty';
                $set_col_tk["BatchCount"] =  'BatchCount';
                $set_col_tk["Std.F.G.Qty"] =  'Std.F.G.Qty';
                $set_col_tk["F.G.Qty"] =  'ActualF.G.Qty';
                $set_col_tk["Diff.InQty"] =  'Diff.InQty';
                $set_col_tk["Req.Time(min)"] =  'Req.Time(min)';
                $set_col_tk["ActualTime(min)"] =  'ActualTime(min)';

                
                
            }else if(!empty($ItemID) && !empty($accountID)){
                 $set_col_tk["PRDID"] =  'PRDID';
               $set_col_tk["PRDDate"] =  'PRDDate';
               $set_col_tk["StdQty"] =  'StdQty';
                $set_col_tk["BatchCount"] =  'BatchCount';
                $set_col_tk["Std.F.G.Qty"] =  'Std.F.G.Qty';
                $set_col_tk["F.G.Qty"] =  'ActualF.G.Qty';
                $set_col_tk["Diff.InQty"] =  'Diff.InQty';
               $set_col_tk["Req.Time(min)"] =  'Req.Time(min)';
               $set_col_tk["ActualTime(min)"] =  'ActualTime(min)';
               
            }
         
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	        $i = 1;
            $Finish_good_qty_new = 0;
            $required_time = 0;
            $actul_time1 = 0;
            $Finish_good_qty_new2 = 0;
            
            $Finish_good_qty_new3 = 0;
            $required_time3 = 0;
            $actul_time3 = 0;
            $Finish_good_qty_new4 = 0;
            $required_time4 = 0;
            $actul_time4 = 0;
            $Finish_good_qty_new5 = 0;
            $required_time5 = 0;
            $actul_time5 = 0;
            $totalBatchQty = 0;
            $stdprodqty_total =0;
            $diffqty_total =0;
            $recstdQty_total =0;
            $totalBatchQty = 0;
            $stdprodqty_total =0;
            $diffqty_total =0;
            $recstdQty_total =0;
            foreach ($body_data as $key => $value) {
                
                 $list_add = [];
                if($report_type == 1 && empty($ItemID) && empty($accountID)){
                    $list_add[] = $value["pro_order_id"];
                    $list_add[] = _d(substr($value["TransDate2"],0,10));
                    if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $list_add[] = $con_st_name;
                    $list_add[] = $value["description"];
                    $list_add[] = $value["qty"];
                    $list_add[] = $value["batch_qty"];
                    $totalBatchQty += $value["batch_qty"];
                    $list_add[] = $stdprodqty;
                    $recstdQty_total +=$value["qty"];
                    $list_add[] = $value["Finish_good_qty_new"];
                    $list_add[] = $diffqty;
                    $Finish_good_qty_new = $Finish_good_qty_new + $value["Finish_good_qty_new"];
                    $list_add[] = $value["required_time"];
                    $required_time = $required_time + $value["required_time"];
                    
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                     $list_add[] = $minutes;
                    $actul_time1 = $actul_time1 + $minutes;
                    
                }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                    
                    if($value["AccountID_staff"] == null){
                       $con_st_AccountID =  $value["AccountID_con"];
                    }else{
                        $con_st_AccountID =  $value["AccountID_staff"];
                    }
                     $list_add[] = $con_st_AccountID;
                    if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $list_add[] = $con_st_name;
                    $list_add[] = $value["fgqty"];
                    $Finish_good_qty_new2 = $Finish_good_qty_new2 + $value["fgqty"];
                    
                }else if(!empty($ItemID) && empty($accountID)){
                    $list_add[] = $value["pro_order_id"];
                    $list_add[] = _d(substr($value["TransDate2"],0,10));
                     if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $list_add[] = $con_st_name;
                    $list_add[] = $value["qty"];
                    $list_add[] = $value["batch_qty"];
                    
                    $totalBatchQty += $value["batch_qty"];
                    $list_add[] = $stdprodqty;
                    $recstdQty_total +=$value["qty"];
                    
                    $list_add[] = $value["Finish_good_qty_new"];
                    $list_add[] = $diffqty;
                    
                    $Finish_good_qty_new3 = $Finish_good_qty_new3 + $value["Finish_good_qty_new"];
                    $list_add[] = $value["required_time"];
                    $required_time3 = $required_time3 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    $list_add[] = $minutes;
                    
                    $actul_time3 = $actul_time3 + $minutes;
                    
                }else if(empty($ItemID) && !empty($accountID)){
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $list_add[] = $value["pro_order_id"];
                    $list_add[] = _d(substr($value["TransDate2"],0,10));
                    $list_add[] = $value["description"];
                    $list_add[] = $value["qty"];
                    $list_add[] = $value["batch_qty"];
                    $totalBatchQty += $value["batch_qty"];
                    $list_add[] = $stdprodqty;
                    $recstdQty_total +=$value["qty"];
                    $list_add[] = $value["Finish_good_qty_new"];
                    $list_add[] = $diffqty;
                    $Finish_good_qty_new4 = $Finish_good_qty_new4 + $value["Finish_good_qty_new"];
                    $list_add[] = $value["required_time"];
                    $required_time4 = $required_time4 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time'].':00'); 
                    $dateTimeObject2 = date_create($value['p_end_time'].':00'); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                      $list_add[] = $minutes;
                    $actul_time4 = $actul_time4 + $minutes;
                }else if(!empty($ItemID) && !empty($accountID)){
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $list_add[] = $value["pro_order_id"];
                    $list_add[] = _d(substr($value["TransDate2"],0,10));
                    $list_add[] = $value["qty"];
                    $list_add[] = $value["batch_qty"];
                    $totalBatchQty += $value["batch_qty"];
                    $list_add[] = $stdprodqty;
                    $recstdQty_total +=$value["qty"];
                    $list_add[] = $value["Finish_good_qty_new"];
                    $list_add[] = $diffqty;
                   
                      $Finish_good_qty_new5 = $Finish_good_qty_new5 + $value["Finish_good_qty_new"];
                       $list_add[] = $value["required_time"];
                    $required_time5 = $required_time5 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                     $list_add[] = $minutes;
                    $actul_time5 = $actul_time5 + $minutes;
                    
                }
                
                  $writer->writeSheetRow('Sheet1', $list_add);
                $i++;
            }
                 $list_add = [];
    		
            if($report_type == 1 && empty($ItemID) && empty($accountID)){
                	$list_add[] = "Total";
                	$list_add[] = "";
                	$list_add[] = "";
                	$list_add[] = "";
                	$list_add[] = $recstdQty_total;
                	$list_add[] = $totalBatchQty;
                	$list_add[] = $stdprodqty_total;
                	$list_add[] = $Finish_good_qty_new;
                	$list_add[] = $diffqty_total;
                	$list_add[] = $required_time;
                	$list_add[] = $actul_time1;
                    
                    
                }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                    	$list_add[] = "Total";
                	$list_add[] = "";
                	$list_add[] = $Finish_good_qty_new2;
                     
                }else if(!empty($ItemID) && empty($accountID)){
                    $list_add[] = "Total";
                	$list_add[] = "";
                	$list_add[] = "";
                    $list_add[] = $recstdQty_total;
                	$list_add[] = $totalBatchQty;
                	$list_add[] = $stdprodqty_total;
                	$list_add[] = $Finish_good_qty_new3;
                	$list_add[] = $diffqty_total;
                	$list_add[] = $required_time3;
                	$list_add[] = $actul_time3;
                    
                   
                    
                }else if(empty($ItemID) && !empty($accountID)){
                    $list_add[] = "Total";
                	$list_add[] = "";
                	$list_add[] = "";
                	$list_add[] = $recstdQty_total;
                	$list_add[] = $totalBatchQty;
                	$list_add[] = $stdprodqty_total;
                	$list_add[] = $Finish_good_qty_new4;
                	$list_add[] = $diffqty_total;
                	$list_add[] = $required_time4;
                	$list_add[] = $actul_time4;
                	
                  
                }else if(!empty($ItemID) && !empty($accountID)){
                     $list_add[] = "Total";
                	$list_add[] = "";
                	$list_add[] = $recstdQty_total;
                	$list_add[] = $totalBatchQty;
                	$list_add[] = $stdprodqty_total;
                	$list_add[] = $Finish_good_qty_new5;
                	$list_add[] = $diffqty_total;
                	$list_add[] = $required_time5;
                	$list_add[] = $actul_time5;
                    
                }
           
                $writer->writeSheetRow('Sheet1', $list_add);
          
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'Production_Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    public function get_production_data()
     {
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'report_type'  => $this->input->post('report_type'),
           'accountID'  => $this->input->post('accountID'),
           'ItemID'  => $this->input->post('ItemID'),
           'source'  => $this->input->post('source')
          );
            $accountID = $this->input->post('accountID');
            $ItemID = $this->input->post('ItemID');
            $accountname = $this->input->post('accountName');
            $Itemname = $this->input->post('Itemname');
          $report_type = $this->input->post('report_type');
        $body_data = $this->misc_reports_model->get_production_for_body_data($filterdata);
        $company_details = $this->misc_reports_model->get_company_detail();
        $table_width = '100%';
        if($report_type == 1 && empty($ItemID) && empty($accountID)){
            $colspan = 8;
        }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
            $colspan = 3;
            
        }else if(empty($ItemID) && !empty($accountID)){
            $colspan = 7;
            
        }else if(!empty($ItemID) && empty($accountID)){
            $colspan = 7;
            
        }
        else if(!empty($ItemID) && !empty($accountID)){
            $colspan = 6;
            
        }
        $html = '';
            $html .= '<table class="table-striped table-bordered production_report" id="production_report" >';
            $html .= '<thead style="font-size:11px;">';
            
            $html .= '<tr style="display:none;">';
            $html .= '<td colspan="'.$colspan.'" style="font-size:18px;font-weight:700;text-align:center;"><b>'.$company_details->company_name.'</b></td>';
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;">';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><b>'.$company_details->address.'</b></td>';
            $html .= '</tr>';
            if($report_type == 1 && empty($ItemID) && empty($accountID)){
            $html .= '<tr style="display:none;">';
            $html .= '<td colspan="'.$colspan.'" style="text-align:center;"><b>Production Details : </b> Date : '.$this->input->post('from_date').' To '.$this->input->post('to_date').'</td>';
            $html .= '</tr>';
            }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                $html .= '<tr style="display:none;">';
                $html .= '<td colspan="'.$colspan.'" style="text-align:center;"><b>Production Summary </b> Date :  '.$this->input->post('from_date').' To '.$this->input->post('to_date').'</td>';
                $html .= '</tr>';
            }else if(empty($ItemID) && !empty($accountID)){
                $html .= '<tr style="display:none;">';
                $html .= '<td colspan="'.$colspan.'" style="text-align:center;"><b>AccountName : '.$accountname.' </b>  Date : '.$this->input->post('from_date').' To '.$this->input->post('to_date').'</td>';
                $html .= '</tr>';
            }else if(!empty($ItemID) && empty($accountID)){
                $html .= '<tr style="display:none;">';
                $html .= '<td colspan="'.$colspan.'" style="text-align:center;"><b>ItemName : '.$Itemname.' </b>  Date: '.$this->input->post('from_date').' To '.$this->input->post('to_date').'</td>';
                $html .= '</tr>';
            }else if(!empty($ItemID) && !empty($accountID)){
                $html .= '<tr style="display:none;">';
                $html .= '<td colspan="'.$colspan.'" style="text-align:center;"><b>ItemName : '.$Itemname.' AND AccountName : '.$accountname.'</b>  Date :  '.$this->input->post('from_date').' To '.$this->input->post('to_date').'</td>';
                $html .= '</tr>';
            }
            
            $html .= '<tr>';
            if($report_type == 1 && empty($ItemID) && empty($accountID)){
               
                $html .= '<th align="center">PRDID</th>';
                $html .= '<th align="center">PRDDate</th>';
                $html .= '<th align="left">AccountName</th>';
                $html .= '<th align="left">ReceipeName</th>';
                $html .= '<th align="center">Std Qty</th>';
                $html .= '<th align="center">Batch Count</th>';
                $html .= '<th align="center">Std F.G. Qty</th>';
                $html .= '<th align="center">Actual F.G. Qty</th>';
                $html .= '<th align="center">Diff. in Qty</th>';
                $html .= '<th align="center">Req.Time(min)</th>';
                $html .= '<th align="center">ActualTime(min)</th>';
            }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                $html .= '<th align="left" style="width:10%;">AccountID</th>';
                $html .= '<th align="left"style="width:30%;">AccountName</th>';
                $html .= '<th align="left" style="width:10%;">F.G.Qty</th>';
            }else if(!empty($ItemID) && empty($accountID)){
                $html .= '<th align="left">PRDID</th>';
                $html .= '<th align="left">PRDDate</th>';
                $html .= '<th align="left">AccountName</th>';
                $html .= '<th align="center">Std Qty</th>';
                $html .= '<th align="center">Batch Count</th>';
                $html .= '<th align="center">Std F.G. Qty</th>';
                $html .= '<th align="center">Actual F.G. Qty</th>';
                $html .= '<th align="center">Diff. in Qty</th>';
                $html .= '<th align="center">Req.Time(min)</th>';
                $html .= '<th align="center">ActualTime(min)</th>';
            }else if(empty($ItemID) && !empty($accountID)){
                
                $html .= '<th align="left">PRDID</th>';
                $html .= '<th align="center">PRDDate</th>';
                $html .= '<th align="left">RecipeName</th>';
                $html .= '<th align="center">Std Qty</th>';
                $html .= '<th align="center">Batch Count</th>';
                $html .= '<th align="center">Std F.G. Qty</th>';
                $html .= '<th align="center">Actual F.G. Qty</th>';
                $html .= '<th align="center">Diff. in Qty</th>';
                $html .= '<th align="center">Req.Time(min)</th>';
                $html .= '<th align="center">Actual Time(min)</th>';
                
            }else if(!empty($ItemID) && !empty($accountID)){
                $html .= '<th align="left">PRDID</th>';
                $html .= '<th align="left">PRDDate</th>';
                $html .= '<th align="center">Std Qty</th>';
                $html .= '<th align="center">Batch Count</th>';
                $html .= '<th align="center">Std F.G. Qty</th>';
                $html .= '<th align="center">Actual F.G. Qty</th>';
                $html .= '<th align="center">Diff. in Qty</th>';
                $html .= '<th align="center">Req.Time(min)</th>';
                $html .= '<th align="center">ActualTime(min)</th>';
            }
            
            
            $html .= '</tr>';
            
            $html .= '</thead>';
            $html .= '<tbody>';
            $i = 1;
            $Finish_good_qty_new = 0;
            $required_time = 0;
            $actul_time1 = 0;
            $Finish_good_qty_new2 = 0;
            
            $Finish_good_qty_new3 = 0;
            $required_time3 = 0;
            $actul_time3 = 0;
            $Finish_good_qty_new4 = 0;
            $required_time4 = 0;
            $actul_time4 = 0;
            $Finish_good_qty_new5 = 0;
            $required_time5 = 0;
            $actul_time5 = 0;
            $totalBatchQty = 0;
            $stdprodqty_total =0;
            $diffqty_total =0;
            $recstdQty_total =0;
            foreach ($body_data as $key => $value) {
                
                $html .= '<tr>';
                if($report_type == 1 && empty($ItemID) && empty($accountID)){
                    
                    $html .= '<td align="center">'.$value["pro_order_id"].'</td>';
                    $html .= '<td align="center">'._d(substr($value["TransDate"],0,10)).'</td>';
                    if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $html .= '<td align="left">'.$con_st_name.'</td>';
                    $html .= '<td align="left">'.$value["description"].'</td>';
                    $html .= '<td align="right">'.$value["qty"].'</td>';
                    $html .= '<td align="right">'.$value["batch_qty"].'</td>';
                    $html .= '<td align="right">'.$stdprodqty.'</td>';
                    $recstdQty_total +=$value["qty"];
                    $html .= '<td align="right">'.$value["Finish_good_qty_new"].'</td>';
                    $html .= '<td align="right">'.$diffqty.'</td>';
                    $Finish_good_qty_new = $Finish_good_qty_new + $value["Finish_good_qty_new"];
                    $html .= '<td align="right">'.$value["required_time"].'</td>';
                    $required_time = $required_time + $value["required_time"];
                    
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    $html .= '<td align="right">'.$minutes.'</td>';
                    $actul_time1 = $actul_time1 + $minutes;
                    $totalBatchQty += $value["batch_qty"];
                    
                }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                    
                    if($value["AccountID_staff"] == null){
                       $con_st_AccountID =  $value["AccountID_con"];
                    }else{
                        $con_st_AccountID =  $value["AccountID_staff"];
                    }
                    $html .= '<td align="left" style="width:10%;">'.$con_st_AccountID.'</td>'; 
                    if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $html .= '<td align="left" style="width:30%;">'.$con_st_name.'</td>';
                    $html .= '<td align="right" style="width:10%;">'.$value["fgqty"].'</td>';
                    $Finish_good_qty_new2 = $Finish_good_qty_new2 + $value["fgqty"];
                    
                }else if(!empty($ItemID) && empty($accountID)){
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $html .= '<td align="left">'.$value["pro_order_id"].'</td>';
                    $html .= '<td align="center">'._d(substr($value["TransDate"],0,10)).'</td>';
                     if($value["firstname"] == null){
                       $con_st_name =  $value["company"];
                    }else{
                        $con_st_name =  $value["firstname"];
                    }
                    $html .= '<td align="left">'.$con_st_name.'</td>';
                    $html .= '<td align="right">'.$value["qty"].'</td>';
                    $html .= '<td align="right">'.$value["batch_qty"].'</td>';
                    $totalBatchQty += $value["batch_qty"];
                    $html .= '<td align="right">'.$stdprodqty.'</td>';
                    $recstdQty_total +=$value["qty"];
                    $html .= '<td align="right">'.$value["Finish_good_qty_new"].'</td>';
                    $html .= '<td align="right">'.$diffqty.'</td>';
                    
                    $Finish_good_qty_new3 = $Finish_good_qty_new3 + $value["Finish_good_qty_new"];
                    $html .= '<td align="right">'.$value["required_time"].'</td>';
                    $required_time3 = $required_time3 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    $html .= '<td align="right">'.$minutes.'</td>';
                    $actul_time3 = $actul_time3 + $minutes;
                    
                    
                }else if(empty($ItemID) && !empty($accountID)){
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $html .= '<td align="left">'.$value["pro_order_id"].'</td>';
                    $html .= '<td align="center">'._d(substr($value["TransDate"],0,10)).'</td>';
                    $html .= '<td align="left">'.$value["description"].'</td>';
                    $html .= '<td align="right">'.$value["qty"].'</td>';
                    $recstdQty_total +=$value["qty"];
                    $html .= '<td align="right">'.$value["batch_qty"].'</td>';
                    $totalBatchQty += $value["batch_qty"];
                    $html .= '<td align="right">'.$stdprodqty.'</td>';
                    $html .= '<td align="right">'.$value["Finish_good_qty_new"].'</td>';
                    $html .= '<td align="right">'.$diffqty.'</td>';
                    $Finish_good_qty_new4 = $Finish_good_qty_new4 + $value["Finish_good_qty_new"];
                    $html .= '<td align="right">'.$value["required_time"].'</td>';
                    $required_time4 = $required_time4 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time'].':00'); 
                    $dateTimeObject2 = date_create($value['p_end_time'].':00'); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    $html .= '<td align="right">'.$minutes.'</td>';
                    $actul_time4 = $actul_time4 + $minutes;
                }else if(!empty($ItemID) && !empty($accountID)){
                    $stdprodqty = $value["batch_qty"]*$value["qty"];
                    $stdprodqty_total += $stdprodqty;
                    $diffqty = $stdprodqty - $value["Finish_good_qty_new"];
                    $diffqty_total += $diffqty;
                    $html .= '<td align="left">'.$value["pro_order_id"].'</td>';
                    $html .= '<td align="center">'._d(substr($value["TransDate"],0,10)).'</td>';
                    $html .= '<td align="right">'.$value["qty"].'</td>';
                    $recstdQty_total +=$value["qty"];
                    $html .= '<td align="right">'.$value["batch_qty"].'</td>';
                    $totalBatchQty += $value["batch_qty"];
                    $html .= '<td align="right">'.$stdprodqty.'</td>';
                    $html .= '<td align="right">'.$value["Finish_good_qty_new"].'</td>';
                    $html .= '<td align="right">'.$diffqty.'</td>';
                    $Finish_good_qty_new5 = $Finish_good_qty_new5 + $value["Finish_good_qty_new"];
                    $html .= '<td align="right">'.$value["required_time"].'</td>';
                    $required_time5 = $required_time5 + $value["required_time"];
                    $dateTimeObject1 = date_create($value['p_start_time']); 
                    $dateTimeObject2 = date_create($value['p_end_time']); 
                    $difference = date_diff($dateTimeObject1, $dateTimeObject2);
                    $minutes = $difference->days * 24 * 60;
                    $minutes += $difference->h * 60;
                    $minutes += $difference->i;
                    $html .= '<td align="right">'.$minutes.'</td>';
                    $actul_time5 = $actul_time5 + $minutes;
                    
                }
                
                $html .= '</tr>';
                $i++;
            }
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            if($report_type == 1 && empty($ItemID) && empty($accountID)){
                    $html .= '<td align="center"><b>Total</b></td>';
                    $html .= '<td align="center"></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="right"><b>'.$recstdQty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$totalBatchQty.'</b></td>';
                    $html .= '<td align="right"><b>'.$stdprodqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$Finish_good_qty_new.'</b></td>';
                    $html .= '<td align="right"><b>'.$diffqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$required_time.'</b></td>';
                    $html .= '<td align="right"><b>'.$actul_time1.'</b></td>';
                    
                }else if($report_type == 2 && empty($ItemID) && empty($accountID)){
                    
                    $html .= '<td align="left" style="width:10%;"><b>Total</b></td>';
                    $html .= '<td align="left" style="width:30%;"></td>';
                    $html .= '<td align="right" style="width:10%;"><b>'.$Finish_good_qty_new2.'</b></td>';
                    
                }else if(!empty($ItemID) && empty($accountID)){
                    $html .= '<td align="left"><b>Total</b></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="right"><b>'.$recstdQty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$totalBatchQty.'</b></td>';
                    $html .= '<td align="right"><b>'.$stdprodqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$Finish_good_qty_new3.'</b></td>';
                    $html .= '<td align="right"><b>'.$diffqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$required_time3.'</b></td>';
                    $html .= '<td align="right"><b>'.$actul_time3.'</b></td>';
                   
                    
                }else if(empty($ItemID) && !empty($accountID)){
                    $html .= '<td align="left"><b>Total</b></td>';
                    $html .= '<td align="center"></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="right"><b>'.$recstdQty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$totalBatchQty.'</b></td>';
                    $html .= '<td align="right"><b>'.$stdprodqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$Finish_good_qty_new4.'</b></td>';
                    $html .= '<td align="right"><b>'.$diffqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$required_time4.'</b></td>';
                    $html .= '<td align="right"><b>'.$actul_time4.'</b></td>';
                    
                }else if(!empty($ItemID) && !empty($accountID)){
                    $html .= '<td align="left"><b>Total</b></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="right"><b>'.$recstdQty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$totalBatchQty.'</b></td>';
                    $html .= '<td align="right"><b>'.$stdprodqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$Finish_good_qty_new5.'</b></td>';
                    $html .= '<td align="right"><b>'.$diffqty_total.'</b></td>';
                    $html .= '<td align="right"><b>'.$required_time5.'</b></td>';
                    $html .= '<td align="right"><b>'.$actul_time5.'</b></td>';
                   
                    
                }
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        echo json_encode($html);
        die;
     }
    // End Production code
    //rate list report start here
     public function rate_list_report()
    {
        if (!has_permission_new('rate_report', '', 'view')) {
            access_denied('access_denied');
        }
        $this->load->model('clients_model');
        $this->load->model('rate_master_model');
        $data['main_item_group'] = $this->misc_reports_model->get_main_item_group();
        $data['states'] = $this->rate_master_model->get_state();
        $data['groups'] = $this->clients_model->get_groups();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['title'] = "Rate list report";
		$data['center'] = $this->misc_reports_model->getAllMandiDb();
        
        $this->load->view('admin/misc_reports/rate_list_report', $data);
    }


    /*  New Function */ 
    public function reportgenerate()
    {
        if (!has_permission_new('rate_report', '', 'view')) {
            access_denied('access_denied');
        }
        $post_data = $this->input->post();
        $data = $this->misc_reports_model->get_report_data($post_data);
        
        $html ='';
        $html .='<div class="col-md-6">';
        $html .='</div>';
        $html .='<div class="col-md-6">';
        $html .='<id="myInput1" onkeyup="myFunction2()">';
        $html .='</div>';
        $html .='<div class="col-md-12">';
        $html.='<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
        $html.='<thead>';
        $html.= '<tr>'; 
        $html.= '<th >Sr.No </th>';
        $html.= '<th >Commodity Name </th>';
        $html.= '<th >Center Name </th>';
        $html.= '<th >Current Rate</th>'; 
        $html.= '<th>Past Rates</th>'; 
        $html.= '<th>Updated By</th>'; 
        $html.= '<th>Date & Time</th>'; 
        $html.= '</tr>'; 
        $html.= '</thead>';
        $html.= '<tbody>';
        if(count($data) > 0){
                $i = 1;
                
                foreach($data as $value)
                {
                    $html.= '<tr>'; 
                    $html.= '<td>'. $i.'</td>';   
                    $html.= '<td>'. $value["ItemName"].'</td>'; 
                    $html.= '<td>'. $value["CenterName"].'</td>';
                    if ($value['IsActive'] == 'Y') {
                        $html .= '<td>' . $value["Rate"] . '</td>';
                        $html .= '<td></td>'; 
                    } elseif ($value['IsActive'] == 'N') {
                        $html .= '<td></td>'; 
                        $html .= '<td>' . $value["Rate"] . '</td>';
                    }
                    $html.= '<td>'. $value["UserID"].'</td>';
                    $dateOnly = date("Y-m-d:h:m:s", strtotime($value["TransDate"]));
                    $html.= '<td>'.$dateOnly.'</td>'; 
                    $html.= '</tr>'; 
                    $i++;
                    
                }
                
                $html.='</tbody>'; 
            }else{
                // $html.= '<tr>';
                $html.='</tbody>'; 
                
                $html.= '</table>';
                $html.= '<span style="color:red;">No record Found..</span>'; 
            // $html.= '</tr>';
        }
       
        echo json_encode($html);
    }
    
    public function GetBreakenensheet()
    {
        $post_data = $this->input->post();
        $SalePurchaseData = $this->misc_reports_model->GetSalePurchaseData($post_data);
        // Get Direct Expense Account SubGroup2
        // 10010
        $DirectExp = "10010";
        $GetAllActSubGroup2 = $this->misc_reports_model->GetActSubGroup2ByMainID($DirectExp,$post_data);
        /*echo "<pre>";
        echo json_encode($SalePurchaseData);
        die;*/
        $TotalPurchAmt = 0;
        $TotalPurchQty = 0;
        $TotalSaleAmt = 0;
        $TotalSaleQty = 0;
        foreach($SalePurchaseData as $key=>$val){
            $NetPurchAmt = 0;
            if($val["TType"]=="P" && $val["TType2"]=="Purchase"){
                $GstPer = $val["cgst"] + $val["sgst"] + $val["igst"];
                //$NetPurchAmt = $val["TotalAmt"] + ($val["TotalAmt"] * ($GstPer / 100));
                $NetPurchAmt = $val["TotalAmt"];
                $TotalPurchAmt += $NetPurchAmt;
                $TotalPurchQty += $val["QTYMT"];
            }
            
            $NetSaleAmt = 0;
            if($val["TType"]=="S" && $val["TType2"]=="Sale"){
                $GstPer = $val["cgst"] + $val["sgst"] + $val["igst"];
                //$NetSaleAmt = $val["TotalAmt"] + ($val["TotalAmt"] * ($GstPer / 100));
                $NetSaleAmt = $val["TotalAmt"];
                $TotalSaleAmt += $NetSaleAmt;
                $TotalSaleQty += $val["QTYMT"];
            }
        }
        
        $html = '';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="width: 40%;">Particulars</th>';
        $html .= '<th style="width: 20%;"></th>';
        $html .= '<th style="width: 20%;">Amount</th>';
        $html .= '<th style="width: 20%;">Quantity(MT)</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td><b></b>Purchase Cost</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align:right;">'.number_format($TotalPurchAmt, 2, '.', '').'</td>';
        $html .= '<td style="text-align:right;">'.number_format($TotalPurchQty, 2, '.', '').'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Direct Cost / Purchase Exp</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $PurchExpCost = 0;
        foreach($GetAllActSubGroup2 as $key=>$value){
            $html .= '<tr>';
            $html .= '<td> '.$value["SubActGroupName2"].'</td>';
            $html .= '<td style="text-align:right;">'.number_format($value["PurchExp"], 2, '.', '').'</td>';
            $PurchExpCost += $value["PurchExp"];
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr> ';
        }
        $PurchAmt_ExpAmt = $PurchExpCost + $TotalPurchAmt;
        $html .= '<tr>';
        $html .= '<td><b>Purchase Cost</b></td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($PurchExpCost, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Total Purchase Cost</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($PurchAmt_ExpAmt, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<td><b>Sales Price</b></td>';
        $html .= '<td></td>';
        $html .= '<td>'.number_format($TotalSaleAmt, 2, '.', '').'</td>';
        $html .= '<td>'.number_format($TotalSaleQty, 2, '.', '').'</td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<td><b>Selling Expesnes</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $SaleExpCost = 0;
        foreach($GetAllActSubGroup2 as $key=>$value){
            $html .= '<tr>';
            $html .= '<td> '.$value["SubActGroupName2"].'</td>';
            $html .= '<td style="text-align:right;">'.number_format($value["SaleExp"], 2, '.', '').'</td>';
            $SaleExpCost += $value["SaleExp"];
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr> ';
        }
        $html .= '<tr>';
        $html .= '<td><b>Selling Expenses</b></td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($SaleExpCost, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $NetSell = $TotalSaleAmt - $SaleExpCost;
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Net Sales Prices</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($NetSell, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        /*if($TotalSaleQty >0){
            
        }else{
            $Sold = $PurchAmt_ExpAmt / $TotalPurchQty;
        }*/
        $Sold = $PurchAmt_ExpAmt / $TotalPurchQty * $TotalSaleQty;
        
        $html .= '<tr>';
        $html .= '<td><b>Purchase price of Material Sold</b></td>';
        $html .= '<td style="text-align:right;">'.number_format($Sold, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $GSold = $SaleExpCost + $Sold;
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Cost of Good Sold (2/3)</td>';
        $html .= '<td style="text-align:right;">'.number_format($GSold, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $NetPL = $TotalSaleAmt - $GSold;
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Net Profit / (Loss)</td>';
        $html .= '<td></td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($NetPL, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $PCostBalC = ($PurchAmt_ExpAmt / $TotalPurchQty) * ($TotalPurchQty - $TotalSaleQty);
        $html .= '<tr>';
        $html .= '<td><b>Purchase Cost for Balance Commodity</b></td>';
        $html .= '<td style="text-align:right;">'.number_format($PCostBalC, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '<tr>';
        $html .= '<td><b>Sales Cost</b></td>';
        $html .= '<td style="text-align:right;">'.number_format($NetPL, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $bal = $PCostBalC - $NetPL;
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">Balance</td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($bal, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $BalQty = $TotalPurchQty - $TotalSaleQty;
        $html .= '<tr>';
        $html .= '<td><b>Balance Qty (Pur Qty - Sale Qty)</b></td>';
        $html .= '<td style="text-align:right;">'.number_format($BalQty, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $BEP = $bal/$BalQty;
        $html .= '<tr>';
        $html .= '<td style="font-weight:700;">BEP per Unit</td>';
        $html .= '<td style="text-align:right;font-weight:700;">'.number_format($BEP, 2, '.', '').'</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '</tbody>';
        echo json_encode($html);
        
    }
    
        
    public function viewBreakenensheet()
    {
        if (!has_permission_new('breakenen_report', '', 'view')) {
            access_denied('breakenen_report');
        }
        
        $data['center'] = $this->misc_reports_model->getAllMandiDb();
        $data['commodity'] = $this->misc_reports_model->getAllMandItemname();
        
        /*echo "<pre>";
        print_r($data['GetAllActSubGroup2']);
        die;*/
        $data['allData'] = $this->misc_reports_model->get_breakenensheet_report_data();
        $totalOrderAmt = $data['allData'][0]['totalOrderAmt'];
        $purchaseCost = $data['allData'][0]['totalOrderAmt'] + $data['allData'][0]['totalOrderAmt'] + $data['allData'][0]['totalOrderAmt'] + $data['allData'][0]['totalOrderAmt'] + $data['allData'][0]['totalOrderAmt'] + $data['allData'][0]['totalOrderAmt'];
        $totalPurchaseCost = $purchaseCost + $totalOrderAmt;
        
        $this->load->view('admin/misc_reports/breakenensheet_report', $data);
    }

    
    public function export_ratereport_list(){
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post())
        {
                $post_data = $this->input->post();
                $result = $this->misc_reports_model->get_report_data($post_data);
                $selected_company_details = $this->misc_reports_model->get_company_detail();
                
                $writer = new XLSXWriter();

                $company_name = array($selected_company_details->company_name);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 6);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_name);
                $j++;
                $address = $selected_company_details->address;
                $company_addr = array($address,);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 6);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_addr);

                $set_col_tk = [];
                $set_col_tk["Commodity Name"] =  'Commodity Name';
                $set_col_tk["Center Name"] = 'Center Name';
                $set_col_tk["Current Rate"] = 'Current Rate';
                $set_col_tk["Past Rates"] = 'Past Rates';
                $set_col_tk["Updated By"] = 'Updated By';
                $set_col_tk["Date & Time"] = 'Date & Time';
                $writer_header = $set_col_tk;
                $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["ItemName"];
                $list_add[] = $value["CenterName"];
                if ($value['IsActive'] == 'Y') {
                    $list_add[] = $value["Rate"] ;
                } elseif ($value['IsActive'] == 'N') { 
                    $list_add[] = $value["Rate"] ;
                }            
                $list_add[] = $value["UserID"];
                $list_add[] = $value["TransDate"];
                
                $writer->writeSheetRow('Sheet1', $list_add);
        
            }
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
                        foreach($files as $file){
                            if(is_file($file)) {
                                unlink($file); 
                            }
                        }
                        $filename = 'Ratelistreport.xlsx';
                        $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
                        echo json_encode([
                            'site_url'          => site_url(),
                            'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
                        ]);
                        die;
        }
    }

    public function export_rate_list(){
         	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
            $item_group = $this->input->post('item_group');
            $item_data = $this->input->post('item_data');
            $states = $this->input->post('states');
            $distributor_id = $this->input->post('distributor_id');
            $data = $this->misc_reports_model->get_rate_table_data($this->input->post());
            $selected_company_details = $this->misc_reports_model->get_company_detail();
            $item_group_name = $this->misc_reports_model->get_item_group_name($item_group);
            
    		$writer = new XLSXWriter();
    		$j=0;
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		$j++;
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		$j++;
    	
    		$msg = "Rate List Report  State: ".$states;
    		$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 16);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		$j++;
    		 if($distributor_id !=''){
              $distributor_d = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();
    	
    		$msg1 = "Distributor: ".$distributor_d['name'];
    		$filter1 = array($msg1);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 16);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter1);
    		$j++;
    		 }
    		  if($item_group !=''){
    		$msg2 = " Item Group: ".$item_group_name;
    		$filter2 = array($msg2);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 16);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter2);
    		  }
    		// empty row
    		$list_add = [];
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            
            $writer->writeSheetRow('Sheet1', $list_add);
            
            
            $set_col_tk = [];
    		$set_col_tk["Item_Id"] =  'Item Id';
    		$set_col_tk["Item_Name"] =  'Item Name';
    		$set_col_tk["CreateQty"] =  'CreateQty';
    		$set_col_tk["CaseQty"] =  'CaseQty';
    		$set_col_tk["BasicRate"] =  'BasicRate';
    		$set_col_tk["GST%"] =  'GST%';
    		$set_col_tk["SaleRate"] =  'SaleRate';
    		$set_col_tk["CaseRate"] =  'CaseRate';
    		$set_col_tk["Effective_Date"] =  'Effective Date';
    	
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    		
    		foreach ($data as $k => $value) {
    	
    			$list_add = [];
    			$list_add[] = strtoupper($value["item_id"]);
    		
    			$list_add[] = $value["description"];
    			$list_add[] = $value["crate_qty"];
    			$list_add[] = $value["case_qty"];
    			$list_add[] = $value["assigned_rate"];
    			$list_add[] = $value["taxrate"];
    			$tax_amt = ($value['assigned_rate']*$value['taxrate'])/100;
                $total_amt = $tax_amt + $value['assigned_rate'];
    			$list_add[] = number_format($total_amt, 2, '.', '');
    			$caseRate = $value['assigned_rate'] * $value['case_qty'];
                $tax_amt2 = ($caseRate*$value['taxrate'])/100;
                $total_amt2 = $tax_amt2 + $caseRate;
    			$list_add[] = number_format($total_amt2, 2, '.', '');
    			$list_add[] = date("d/m/Y", strtotime(substr($value['effective_date'],0,10)));
    			$list_add[] = $value["BillAmt"];

    			$writer->writeSheetRow('Sheet1', $list_add);
    	}
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'Rate_list_Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
        
    }
    public function get_rate_report(){
        if (!has_permission_new('item_rate_list', '', 'view')) {
            access_denied('access_denied');
        }
        $item_group = $this->input->post('item_group');
        $item_data = $this->input->post('item_data');
        $states = $this->input->post('states');
        $distributor_id = $this->input->post('distributor_id');
        $data = $this->misc_reports_model->get_rate_table_data($this->input->post());
        $company_data = $this->misc_reports_model->get_company_detail();
        $item_group_name = $this->misc_reports_model->get_item_group_name($item_group);
        
            $html =''; 
            $html .= '<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">';
            $html .= '<thead style="font-size:11px;">';
             $html .= '<tr style="display:none;">';
             $html .= '<th colspan="10"><b class="co_name">'.$company_data->company_name.'</b></th>';
             $html.= '</tr>';
             $html .= '<tr style="display:none;">';
             $html .= '<th colspan="10"><b class="co_add">'.$company_data->address.'</b></th>';
             $html.= '</tr>';
              $html .= '<tr style="display:none;">';
            $html .= '<th colspan="10"><b class="state_dist">';
            if($states !=''){
            $html .= 'State : '.$states.','; 
            }
            if($distributor_id !=''){
              $distributor_d = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();
            $html .= ' Distributor : '.$distributor_d['name']; 
            }
            $html.= '</b> </th>';
            $html.= '</tr>';
             if($item_group !=''){
             $html .= '<tr style="display:none;">';
             
             $html .= '<th colspan="10"><b class="item_grp">Item Group :'.$item_group_name.' </b ></th>';
             $html.= '</tr>';
             }
             $html.= '<tr>';
             $html.= '<th align="center">Sr.</th>';
             $html.= '<th align="center">Item Id</th>';
             $html.= '<th>Item Name</th>';
             $html.= '<th align="center">CreateQty</th>';
             $html.= '<th align="center">CaseQty</th>';
             $html.= '<th align="center">BasicRate</th>';
             $html.= '<th align="center">GST%</th>';
             $html.= '<th align="center">SaleRate</th>';
             $html.= '<th align="center">CaseRate</th>';
             $html.= '<th align="center">Effective Date</th>';
             $html.= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
        $i = 1; foreach($data as $value){
            $html.= '<tr>';
               
             $html.= '<td align="center">'.$i.'</td>';
             $html.= '<td align="center">'.strtoupper($value['item_id']).'</td>';
             $html.= '<td>'.$value['description'].'</td>';
             $html.= '<td align="right">'.$value['crate_qty'].'</td>';
             $html.= '<td align="right">'.$value['case_qty'].'</td>';
             $html.= '<td align="right">'.$value['assigned_rate'].'</td>';
             $html.= '<td align="right">'.$value['taxrate'].'</td>';
            $tax_amt = ($value['assigned_rate']*$value['taxrate'])/100;
            $total_amt = $tax_amt + $value['assigned_rate'];
            $html.= '<td align="right">'.number_format($total_amt, 2, '.', '').'</td>';
            $caseRate = $value['assigned_rate'] * $value['case_qty'];
            $tax_amt2 = ($caseRate*$value['taxrate'])/100;
            $total_amt2 = $tax_amt2 + $caseRate;
            $html.= '<td align="right">'.number_format($total_amt2,2).'</td>';
            $html.= '<td align="center">'.date("d/m/Y", strtotime(substr($value['effective_date'],0,10))).'</td>';
           
        
            
              
            $html.= '</tr>';
       $i++; }
        $html .= '</tbody>';
        $html .= '<table>';
        // echo $html;
         echo json_encode($html);
    }
    //end here
    
    // target entry and target Vs achivements
    public function target_sale(){
        if (!has_permission_new('staff_target', '', 'view')) {
            access_denied('access_denied');
        }
        $data['staff'] = $this->misc_reports_model->get_salesstaff();
        $data['item_division_group'] = $this->misc_reports_model->item_division_group_data();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['title'] = "Target Sale";
        $this->load->view('admin/misc_reports/target_sale', $data);
    }
    public function get_targetList(){
            $company_detail = $this->misc_reports_model->get_company_detail();
           $data = $this->misc_reports_model->get_targetList($this->input->post());
           $data_division = $this->misc_reports_model->get_coutomer_division($this->input->post());
           $data_staff_business_division = $this->misc_reports_model->get_staff_business_division($this->input->post());
           $sum_get_coutomer_division = $this->misc_reports_model->sum_get_coutomer_division($this->input->post());
           $item_division_group = $this->misc_reports_model->item_division_group();
    
         $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
      $month_data = $this->input->post('month_data');
       $month = substr($month_data, -2);
      $staff_target_data =  $this->db->get_where('tblstaff_target',array('Staff_AccountID'=>$this->input->post('staff_account_name'),'PlantID'=>$selected_company,'FY'=>$year,'MonthID'=>$month))->result_array();
 
           if(count($staff_target_data) > 0){
               $hidden_data = 1;
           }else{
              $hidden_data = 0; 
           }
            $html =''; 
            $html.= '<thead>';
                 
            $html.= '<tr style="display:none;">';
            $html.= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Staff Target</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>';
            $html.= '<tr>';
            $html.= '<th id="sl" style="text-align:left; text-transform: uppercase;">Party Name <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Dist. Type</th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Station</th>';
                  foreach($item_division_group as $value){
                    if($value['id'] != 99){
            $html.= '<th style="text-align:left; text-transform: uppercase;" >'.$value['name'].'</th>';
                   
                    }
                    }
            $html.= '<th style="text-align:left; text-transform: uppercase;">Total</th>';
                  
                   
            $html.= '</tr>';
            $html.= '</thead>';
            $html.= '<tbody>';
            $total_data_total = 0;
        $i = 1; foreach($data as $value){
            $html.= '<tr><input type="hidden" name="hidden" value="'.$hidden_data.'">';
               $total = 0;
              
          
             $html.= '<td data-id="'.$value['AccountID'].'"><input type="hidden" id="AccountID" name="AccountID[]" value="'.$value['AccountID'].'" >'.$value['company'].'</td>';
             $html.= '<td style="text-align:center;">'.$value['name'].'</td>';
             $html.= '<td>'.$value['StationName'].'</td>';
             
            foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                    foreach($data_division as $data_division_data){
                        if($value['AccountID'] == $data_division_data['AccountID'] && $item_division_group_data['id'] == $data_division_data['ItemDivID']){
                            $actId = ''.$value['AccountID'].'';
                            $html.= '<td style="padding: 0px 0px !important;"><input type="hidden" id="ItemDivID" name="'.$value['AccountID'].'_item_id[]" value="'.$data_division_data['ItemDivID'].'" ><input type="text" class="target_data_value target_data_account_'.$value['AccountID'].' target_count_'.$data_division_data['ItemDivID'].'" onkeyup="myFunction_data('.$data_division_data['ItemDivID'].','."'".$actId."'".')" name="'.$value['AccountID'].'_target[]" value="'.$data_division_data['Targate'].'" style=" background-color: #e1e1e17d; text-align: right; height: 30px;width: 100%;font-size: 12px;padding: 5px;" placeholder="0"></td>'; 
                            $mm = 1;
                            $total+=$data_division_data['Targate'];
                        }
                    }
                if($mm == "0"){
                    $html.= '<td></td>';
                }
                }
            }
              $html.= '<td style="text-align:right;font-size: 13px;" class=" target_count_total_left_'.$value['AccountID'].'"><b  class="left_lower_total">'.$total.'</b></td>';
           $total_data_total+=$total;
        
            
              
            $html.= '</tr>';
            
       $i++; }
       $html.= '<tr>';
       $html.= '<td data-id="New_Business"><input type="hidden" id="AccountID" name="AccountID[]" value="New_Business" >NEW BUSINESS</td>';
             $html.= '<td style="text-align:center;"></td>';
             $html.= '<td></td>';
             $total_bussiness_target =0;
               if(count($data_staff_business_division) > 0){
                      foreach($item_division_group as $item_division_group_data){
                foreach($data_staff_business_division as $data_staff_business_division_data){
                    
                    if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                           $mm_data = 0;
                    $actId = 'New_Business';
                     $html.= '<td style="padding: 0px 0px !important;"><input type="hidden" id="ItemDivID" name="New_Business_item_id[]" value="'.$item_division_group_data['id'].'" ><input type="text" class="target_data_value target_data_account_New_Business target_count_'.$item_division_group_data['id'].'" onkeyup="myFunction_data('.$item_division_group_data['id'].','."'".$actId."'".')" name="New_Business_target[]" value="'.$data_staff_business_division_data['Targate'].'" style=" background-color: #e1e1e17d; text-align: right; height: 30px;width: 100%;font-size: 12px;padding: 5px;" placeholder="0"></td>'; 
                            $total_bussiness_target+=$data_staff_business_division_data['Targate'];
                    }
                
                }
                
              }
               $html.= '<td style="text-align:right;font-size: 13px;" class=" target_count_total_left_New_Business"><b  class="left_lower_total">'.$total_bussiness_target.'</b></td>';
          $total_data_total+=$total_bussiness_target;
                  }else{
                      foreach($item_division_group as $item_division_group_data){
                
                $mm_data = 0;
                if($item_division_group_data['id'] != 99){
                    $actId = 'New_Business';
                     $html.= '<td style="padding: 0px 0px !important;"><input type="hidden" id="ItemDivID" name="New_Business_item_id[]" value="'.$item_division_group_data['id'].'" ><input type="text" class="target_data_value target_data_account_New_Business target_count_'.$item_division_group_data['id'].'" onkeyup="myFunction_data('.$item_division_group_data['id'].','."'".$actId."'".')" name="New_Business_target[]" value="'.$item_division_group_data['Targate'].'" style=" background-color: #e1e1e17d; text-align: right; height: 30px;width: 100%;font-size: 12px;padding: 5px;" placeholder="0"></td>'; 
                      $total_data_total+=$total_bussiness_target;     
                }
              }
               $html.= '<td style="text-align:right;font-size: 13px;" class=" target_count_total_left_New_Business"><b  class="left_lower_total">'.$total_bussiness_target.'</b></td>';
          
                  }
              
             $html.= '</tr>';
       
        $html.= '</tbody>';
        $html.= '<tfoot>';
        $html.= '<tr>';
             $html.= '<td style="text-transform: uppercase;"><b>Total</b></td>';
             $html.= '<td></td>';
             $html.= '<td></td>';
                foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                    $total_data = 0;
                     $target_total =0;
                     
                    foreach($sum_get_coutomer_division as $sum_get_coutomer_division_data){
                        if($item_division_group_data['id'] == $sum_get_coutomer_division_data['ItemDivID']){
                            if(count($data_staff_business_division) > 0){
                                foreach($data_staff_business_division as $data_staff_business_division_data){ 
                                     if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                                         $target_total =0;
                                        $target_total+=$data_staff_business_division_data['Targate'];
                                     }
                            }
                            }
                            $mm = 1;
                            $target_total+=$sum_get_coutomer_division_data['Targate'];
                             $html.= '<td style="font-size: 13px; text-align:right" class="target_count_total_lower_'.$item_division_group_data['id'].'"><b>'. $target_total.'</b></td>';
                           
                        }
                    }
                if($mm == "0"){
                    $m_data = 0;
                    // $html.= '<td class="1"></td>';
                    foreach($data_staff_business_division as $data_staff_business_division_data){
                          if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                             $html.= '<td style="font-size: 13px; text-align:right" class="target_count_total_lower_'.$item_division_group_data['id'].'"><b>'.$data_staff_business_division_data['Targate'].'</b></td>';
                       $m_data++;
                    }
                    }
                    if($m_data  == "0"){
                         $html.= '<td style="font-size: 13px; text-align:right" class="target_count_total_lower_'.$item_division_group_data['id'].'"><b>0.00</b></td>';
                    
                    }
                        
                       
                   
                           
                }
                }
               
            }
                $html.= '<td style="font-size: 13px; text-align:right" ><b class=" left_lower_total_count">'.$total_data_total.'</b></td>';
             $html.= '</tr>';
             $html.= '</tfoot>';
         echo json_encode($html);
    }
    
    public function export_target_report()
    {
    	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
    	$data = array(
               'from_date' => $this->input->post('from_date'),
               'to_date'  => $this->input->post('to_date')
              );
            $data = $this->misc_reports_model->get_targetList($this->input->post());
            $data_division = $this->misc_reports_model->get_coutomer_division($this->input->post());
            $data_staff_business_division = $this->misc_reports_model->get_staff_business_division($this->input->post());
            $sum_get_coutomer_division = $this->misc_reports_model->sum_get_coutomer_division($this->input->post());
            $item_division_group = $this->misc_reports_model->item_division_group();
            $selected_company = $this->session->userdata('root_company');
            $year = $_SESSION['finacial_year'];
            $month_data = $this->input->post('month_data');
            $month = substr($month_data, -2);
            $staff_target_data =  $this->db->get_where('tblstaff_target',array('Staff_AccountID'=>$this->input->post('staff_account_name'),'PlantID'=>$selected_company,'FY'=>$year,'MonthID'=>$month))->result_array();
 
           if(count($staff_target_data) > 0){
               $hidden_data = 1;
           }else{
              $hidden_data = 0; 
           }
            
    		$selected_company_details    = $this->misc_reports_model->get_company_detail();
    		
    		$writer = new XLSXWriter();
    		//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
    		//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
    		//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
    		//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
    		
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		
    		$msg = "Target Report For Month: ".$month." ,  StaffID: " .$this->input->post('staff_account_name');
    		$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		
    		// empty row
    		$list_add = [];
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $writer->writeSheetRow('Sheet1', $list_add);
            
            
            $set_col_tk = [];
    		$set_col_tk["PARTY NAME"] =  'PARTY NAME';
    		$set_col_tk["DIST. TYPE"] =  'DIST. TYPE';
    		$set_col_tk["STATION"] =  'STATION';
    		foreach($item_division_group as $value1){
    		    if($value1['id'] != 99){
    		        $key = $value1['name'];
    		        $set_col_tk[$key] =  $value1['name'];
    		    }
    		}
    		
    		$set_col_tk["Total"] =  'Total';
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	    $total_data_total = 0;
            $i = 1;
    		foreach ($data as $k => $value) {
    		    $total = 0;
    			$list_add = [];
    			$list_add[] = $value['company'];
    			$list_add[] = $value['name'];
    			$list_add[] = $value["StationName"];
    			foreach($item_division_group as $item_division_group_data){
    			    $mm = 0;
    			    if($item_division_group_data['id'] != 99){
    			        foreach($data_division as $data_division_data){
    			             if($value['AccountID'] == $data_division_data['AccountID'] && $item_division_group_data['id'] == $data_division_data['ItemDivID']){
    			                 $actId = ''.$value['AccountID'].'';
    			                 $list_add[] = $data_division_data['Targate'];
    			                 $mm = 1;
                                 $total+=$data_division_data['Targate'];
    			             }
    			        }
    			        if($mm == "0"){
                           $list_add[] = "";
                        }
    			    }
    			}
    			$list_add[] = $total;
    			$total_data_total+=$total;
    			$i++;
    			$writer->writeSheetRow('Sheet1', $list_add);
    	    }
    	
    		$list_add = [];
    		$list_add[] = "NEW BUSINESS";
    		$list_add[] = "";
    		$list_add[] = "";
    		$total_bussiness_target =0;
               if(count($data_staff_business_division) > 0){
                   foreach($item_division_group as $item_division_group_data){
                       foreach($data_staff_business_division as $data_staff_business_division_data){
                           if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                               $mm_data = 0;
                               $actId = 'New_Business';
                               $list_add[] = $data_staff_business_division_data['Targate'];
                               $total_bussiness_target+=$data_staff_business_division_data['Targate'];
                           }
                       }
                   }
                   $list_add[] = $total_bussiness_target;
                   $total_data_total+=$total_bussiness_target;
               }else{
                   foreach($item_division_group as $item_division_group_data){
                       $mm_data = 0;
                       if($item_division_group_data['id'] != 99){
                           $actId = 'New_Business';
                           $list_add[] = $item_division_group_data['Targate'];
                           $total_data_total+=$total_bussiness_target; 
                       }
                   }
                   $list_add[] = $total_bussiness_target;
               }
    		$writer->writeSheetRow('Sheet1', $list_add);
    		
    		// footer Data
    		$list_add = [];
    		$list_add[] = "Total";
    		$list_add[] = "";
    		$list_add[] = "";
    		 foreach($item_division_group as $item_division_group_data){
    		      $mm = 0;
    		       if($item_division_group_data['id'] != 99){
    		           $total_data = 0;
                       $target_total =0;
                       foreach($sum_get_coutomer_division as $sum_get_coutomer_division_data){
                           if($item_division_group_data['id'] == $sum_get_coutomer_division_data['ItemDivID']){
                               if(count($data_staff_business_division) > 0){
                                   foreach($data_staff_business_division as $data_staff_business_division_data){
                                       if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                                            $target_total =0;
                                            $target_total+=$data_staff_business_division_data['Targate'];
                                       }
                                   }
                               }
                            $mm = 1;
                            $target_total+=$sum_get_coutomer_division_data['Targate'];
                            $list_add[] = $target_total;
                           }
                       }
                        if($mm == "0"){
                            $m_data = 0;
                            foreach($data_staff_business_division as $data_staff_business_division_data){
                                if($item_division_group_data['id'] == $data_staff_business_division_data['ItemDivID']){
                                    $list_add[] = $data_staff_business_division_data['Targate'];
                                    $m_data++;
                                }
                            }
                            if($m_data  == "0"){
                                $list_add[] = "0.00";
                            }
                        }
    		       }
    		 }
    		$list_add[] = $total_data_total;
    		$writer->writeSheetRow('Sheet1', $list_add);
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'Target_Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    public function get_targetList_bkp_bussiness(){
        $company_detail = $this->misc_reports_model->get_company_detail();
           $data = $this->misc_reports_model->get_targetList($this->input->post());
           $data_division = $this->misc_reports_model->get_coutomer_division($this->input->post());
           $sum_get_coutomer_division = $this->misc_reports_model->sum_get_coutomer_division($this->input->post());
           $item_division_group = $this->misc_reports_model->item_division_group_data();
       
         $selected_company = $this->session->userdata('root_company');
      $year = $_SESSION['finacial_year'];
      $month_data = $this->input->post('month_data');
       $month = substr($month_data, -2);
      $staff_target_data =  $this->db->get_where('tblstaff_target',array('Staff_AccountID'=>$this->input->post('staff_account_name'),'PlantID'=>$selected_company,'FY'=>$year,'MonthID'=>$month))->result_array();
 
           if(count($staff_target_data) > 0){
               $hidden_data = 1;
           }else{
              $hidden_data = 0; 
           }
            $html =''; 
            $html.= '<thead>';
                 
            $html.= '<tr style="display:none;">';
            $html.= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Staff Target</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>';
            $html.= '<tr>';
            $html.= '<th id="sl" style="text-align:left; text-transform: uppercase;">Party Name <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Dist. Type</th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Station</th>';
                  foreach($item_division_group as $value){
                    if($value['id'] != 99){
            $html.= '<th style="text-align:left; text-transform: uppercase;" >'.$value['name'].'</th>';
                   
                    }
                    }
            $html.= '<th style="text-align:left; text-transform: uppercase;">Total</th>';
                  
                   
            $html.= '</tr>';
            $html.= '</thead>';
            $html.= '<tbody>';
            $total_data_total = 0;
        $i = 1; foreach($data as $value){
            $html.= '<tr><input type="hidden" name="hidden" value="'.$hidden_data.'">';
               $total = 0;
              
          
             $html.= '<td data-id="'.$value['AccountID'].'"><input type="hidden" id="AccountID" name="AccountID[]" value="'.$value['AccountID'].'" >'.$value['company'].'</td>';
             $html.= '<td style="text-align:center;">'.$value['name'].'</td>';
             $html.= '<td>'.$value['StationName'].'</td>';
             
            foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                    foreach($data_division as $data_division_data){
                        if($value['AccountID'] == $data_division_data['AccountID'] && $item_division_group_data['id'] == $data_division_data['ItemDivID']){
                             $actId = ''.$value['AccountID'].'';
                            $html.= '<td style="padding: 0px 0px !important;"><input type="hidden" id="ItemDivID" name="'.$value['AccountID'].'_item_id[]" value="'.$data_division_data['ItemDivID'].'" ><input type="text" class="target_data_value target_data_account_'.$value['AccountID'].' target_count_'.$data_division_data['ItemDivID'].'" onkeyup="myFunction_data('.$data_division_data['ItemDivID'].','."'".$actId."'".')" name="'.$value['AccountID'].'_target[]" value="'.$data_division_data['Targate'].'" style=" background-color: #e1e1e17d; text-align: right; height: 30px;width: 100%;font-size: 12px;padding: 5px;" placeholder="0"></td>'; 
                            $mm = 1;
                            $total+=$data_division_data['Targate'];
                        }
                    }
                if($mm == "0"){
                    $html.= '<td></td>';
                }
                }
            }
              $html.= '<td style="text-align:right;font-size: 13px;" class="target_count_total_left_'.$value['AccountID'].'"><b class="left_lower_total">'.$total.'</b></td>';
           $total_data_total+=$total;
        
            
              
            $html.= '</tr>';
            
       $i++; }
        $html.= '</tbody>';
        $html.= '<tfoot>';
        $html.= '<tr>';
             $html.= '<td style="text-transform: uppercase;"><b>Total</b></td>';
             $html.= '<td></td>';
             $html.= '<td></td>';
                foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                    $total_data = 0;
                    
                    foreach($sum_get_coutomer_division as $sum_get_coutomer_division_data){
                        if($item_division_group_data['id'] == $sum_get_coutomer_division_data['ItemDivID']){
                            $mm = 1;
                             $html.= '<td style="font-size: 13px; text-align:right" class="target_count_total_lower_'.$item_division_group_data['id'].'"><b>'. $sum_get_coutomer_division_data['Targate'].'</b></td>';
                           
                        }
                    }
                if($mm == "0"){
                    $html.= '<td class="1"></td>';
                }
                }
               
            }
                $html.= '<td style="font-size: 13px; text-align:right"><b class=" left_lower_total_count">'.$total_data_total.'</b></td>';
             $html.= '</tr>';
             $html.= '</tfoot>';
         echo json_encode($html);
    }
    public function submit_targetSale(){
        if($this->input->post()){
            if (!has_permission_new('staff_target', '', 'edit')) {
                access_denied('access_denied');
            }
            $data_array = $this->input->post();
            if($data_array['hidden'] == 0){
              $data = $this->misc_reports_model->create_targetSale($data_array);
             if($data){
                 set_alert('success', _l('added_successfully'));
                        $redUrl = admin_url('misc_reports/target_sale');
                        redirect($redUrl);
             }else{
                  $redUrl = admin_url('misc_reports/target_sale');
                        redirect($redUrl);
             }   
            }else{
              $data = $this->misc_reports_model->update_targetSale($data_array);
             if($data){
                 set_alert('success', _l('Update Successfully'));
                        $redUrl = admin_url('misc_reports/target_sale');
                        redirect($redUrl);
             }else{
                  $redUrl = admin_url('misc_reports/target_sale');
                        redirect($redUrl);
             }    
            }
            
        }
    }
    public function target_vs_achievement(){
        if (!has_permission_new('target_vs_achivements', '', 'view')) {
            access_denied('access_denied');
        }
        $data['staff'] = $this->misc_reports_model->get_salesstaff2();
        $data['item_division_group'] = $this->misc_reports_model->item_division_group_data();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['title'] = "Target vs Achievement";
        $this->load->view('admin/misc_reports/target_vs_achievement', $data);
    }
    public function get_target_achivement(){
        $company_detail = $this->misc_reports_model->get_company_detail();
         $data = $this->misc_reports_model->get_targetList($this->input->post());
           $data_division = $this->misc_reports_model->get_coutomer_division($this->input->post());
           $sum_get_coutomer_division = $this->misc_reports_model->sum_get_coutomer_division($this->input->post());
           $data_array = $this->input->post();
           $item_division_group = $this->misc_reports_model->item_division_group_data();
       
        
     
            $html =''; 
            $html.= '<thead>';
                 
            $html.= '<tr style="display:none;">';
            $html.= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Target vs Achievement</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>';
            $html.= '<tr>';
            $html.= '<th id="sl" style="text-align:left;text-transform: uppercase;">Party Name <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Dist. Type</th>';
            $html.= '<th style="text-align:left;text-transform: uppercase;">Station</th>';
            $a = array();
                  foreach($item_division_group as $value){
                    if($value['id'] != 99){
                         foreach($data_division as $data_division_data){
                        // if($count != 1){
                        
                        
                                if( $value['id'] == $data_division_data['ItemDivID']){
                                    
                                    if (in_array($value['id'], $a)){
                                        
                                    }else{
                                        array_push($a,$value['id']);
                                $html.= '<th style="text-align:left; text-transform: uppercase;">'.$value['name'].'</th>';
                                    }
                          
                           
                            $count = 1;
                            
                        }
                    // }
                        }
           
                   
                    }
                    }
            $html.= '<th style="text-align:left; text-transform: uppercase;">Total</th>';
                  
                   
            $html.= '</tr>';
            $html.= '</thead>';
            $html.= '<tbody>';
            $total_data_total = 0;
            $total_data_total_count_achievment = 0;
            $array_achievement = array();
        $i = 1; 
        foreach($data as $value){
            $data_array['accountId']  = $value['AccountID'];
            $sum_get_achievement_division = $this->misc_reports_model->sum_get_achievement_division($data_array);
            
            array_push($array_achievement,$sum_get_achievement_division);
           
            $html.= '<tr>';
               $total = 0;
               $total_NetChallanAmt = 0;
              
          
             $html.= '<td data-id="'.$value['AccountID'].'" >'.$value['company'].'</td>';
             $html.= '<td style="text-align:center;">'.$value['name'].'</td>';
             $html.= '<td style="">'.$value['StationName'].'</td>';
             
            foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                     if (in_array($item_division_group_data['id'], $a)){
                      foreach($data_division as $data_division_data){
                        if($mm != 1){
                                if($value['AccountID'] == $data_division_data['AccountID'] && $item_division_group_data['id'] == $data_division_data['ItemDivID']){
                            $html.= '<td style="text-align:right; class="1">'.$data_division_data['Targate']; 
                           
                            $mm = 1;
                            $total+=$data_division_data['Targate'];
                        }
                    }
                        }
                    
                      foreach($sum_get_achievement_division as $sum_get_achievement_division_data){
                        if($value['AccountID'] == $sum_get_achievement_division_data['AccountID'] && $item_division_group_data['id'] == $sum_get_achievement_division_data['ItemDivID']){
                            $html.= ' / '.round($sum_get_achievement_division_data['NetChallanAmt']).'</td>'; 
                         
                            $mm = 1;
                            $total_NetChallanAmt+=$sum_get_achievement_division_data['NetChallanAmt'];
                        } 
                    }  
                    if($mm == "0"){
                    $html.= '<td></td>';
                }
                     }
                    
               
                }
            }
              $html.= '<td  style="text-align:right; font-size:13px;"><b>'.$total.' / '.round($total_NetChallanAmt).'<b></td>';
           $total_data_total+=$total;
           $total_data_total_count_achievment+=$total_NetChallanAmt;
        
            
              
            $html.= '</tr>';
            
       $i++; }
       $html.= '</tbody>';
       $html.= '<tfoot>';
        $html.= '<tr>';
             $html.= '<td style="text-transform: uppercase;">Total</td>';
             $html.= '<td></td>';
             $html.= '<td></td>';
            $total_data_achive_data = 0;
                foreach($item_division_group as $key=>$item_division_group_data){
                     $ii = 0;
                $mm = 0;
                $mmm = 0;
                if($item_division_group_data['id'] != 99){
                    $total_data = 0;
                    $total_data_achive = 0;
                    
                    
                    foreach($sum_get_coutomer_division as $sum_get_coutomer_division_data){
                        if($item_division_group_data['id'] == $sum_get_coutomer_division_data['ItemDivID']){
                            $mm = 1;
                            if($sum_get_coutomer_division_data['Targate'] == ''){
                                $html.= '<td  style="text-align:right;  font-size:13px;"><b>0';
                            }else{
                                $html.= '<td  style="text-align:right;  font-size:13px;"><b>'. $sum_get_coutomer_division_data['Targate'];
                            }
                             
                           
                        }
                    }
              
                    foreach($array_achievement as $array_achievement_data){
                         foreach($array_achievement_data as $array_achievement_data_array){
                        
                        if($item_division_group_data['id'] == $array_achievement_data_array['ItemDivID']){
                            $mmm = 1;
                           
                             $total_data_achive+=$array_achievement_data_array['NetChallanAmt'];
                             
                           $total_data_achive_data+=$total_data_achive; 
                        }
              }}
                 
                        $html.= ' / '.round($total_data_achive).'</b></td >';
                 
              
                }
               
            }
                $html.= '<td  style="text-align:right; font-size:13px;"><b>'.$total_data_total.' / '.round($total_data_total_count_achievment).'</b></td>';
             $html.= '</tr>';
             $html.= '</tfoot>';
         echo json_encode($html);
    }
    
    public function get_target_achivement_data_bkp(){
        $company_detail = $this->misc_reports_model->get_company_detail();
         $data = $this->misc_reports_model->get_targetList($this->input->post());
           $data_division = $this->misc_reports_model->get_coutomer_division($this->input->post());
           $sum_get_coutomer_division = $this->misc_reports_model->sum_get_coutomer_division($this->input->post());
           $data_array = $this->input->post();
           $item_division_group = $this->misc_reports_model->item_division_group_data();
       
        
     
            $html =''; 
            $html.= '<thead>';
                 
            $html.= '<tr style="display:none;">';
            $html.= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Target vs Achievement</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>';
            $html.= '<tr>';
            $html.= '<th id="sl" style="text-align:left;text-transform: uppercase;">Party Name <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>';
            $html.= '<th style="text-align:left; text-transform: uppercase;">Dist. Type</th>';
            $html.= '<th style="text-align:left;text-transform: uppercase;">Station</th>';
                  foreach($item_division_group as $value){
                    if($value['id'] != 99){
            $html.= '<th style="text-align:left; text-transform: uppercase;">'.$value['name'].'</th>';
                   
                    }
                    }
            $html.= '<th style="text-align:left; text-transform: uppercase;">Total</th>';
                  
                   
            $html.= '</tr>';
            $html.= '</thead>';
            $html.= '<tbody>';
            $total_data_total = 0;
            $array_achievement = array();
            // $array_achievement = [];
        $i = 1; foreach($data as $value){
            $data_array['accountId']  = $value['AccountID'];
            $sum_get_achievement_division = $this->misc_reports_model->sum_get_achievement_division($data_array);
            
            array_push($array_achievement,$sum_get_achievement_division);
            // print_r($array_achievement);
            $html.= '<tr>';
               $total = 0;
               $total_NetChallanAmt = 0;
              
          
             $html.= '<td data-id="'.$value['AccountID'].'" >'.$value['company'].'</td>';
             $html.= '<td style="text-align:center;">'.$value['name'].'</td>';
             $html.= '<td style="">'.$value['StationName'].'</td>';
             
            foreach($item_division_group as $item_division_group_data){
                $mm = 0;
                if($item_division_group_data['id'] != 99){
                    foreach($data_division as $data_division_data){
                        if($value['AccountID'] == $data_division_data['AccountID'] && $item_division_group_data['id'] == $data_division_data['ItemDivID']){
                            $html.= '<td style="text-align:right;">'.$data_division_data['Targate']; 
                           
                            $mm = 1;
                            $total+=$data_division_data['Targate'];
                        }
                    }
                      foreach($sum_get_achievement_division as $sum_get_achievement_division_data){
                        if($value['AccountID'] == $sum_get_achievement_division_data['AccountID'] && $item_division_group_data['id'] == $sum_get_achievement_division_data['ItemDivID']){
                            $html.= ' / '.round($sum_get_achievement_division_data['NetChallanAmt']).'</td>'; 
                         
                            $mm = 1;
                            $total_NetChallanAmt+=$sum_get_achievement_division_data['NetChallanAmt'];
                        }
                    }
                if($mm == "0"){
                    $html.= '<td></td>';
                }
                }
            }
              $html.= '<td  style="text-align:right; font-size:13px;"><b>'.$total.' / '.round($total_NetChallanAmt).'<b></td>';
           $total_data_total+=$total;
        
            
              
            $html.= '</tr>';
            
       $i++; }
       $html.= '</tbody>';
       $html.= '<tfoot>';
        $html.= '<tr>';
             $html.= '<td style="text-transform: uppercase;">Total</td>';
             $html.= '<td></td>';
             $html.= '<td></td>';
            $total_data_achive_data = 0;
                foreach($item_division_group as $key=>$item_division_group_data){
                     $ii = 0;
                $mm = 0;
                $mmm = 0;
                if($item_division_group_data['id'] != 99){
                    $total_data = 0;
                    $total_data_achive = 0;
                    
                    
                    foreach($sum_get_coutomer_division as $sum_get_coutomer_division_data){
                        if($item_division_group_data['id'] == $sum_get_coutomer_division_data['ItemDivID']){
                            $mm = 1;
                            if($sum_get_coutomer_division_data['Targate'] == ''){
                                $html.= '<td  style="text-align:right;  font-size:13px;"><b>0';
                            }else{
                                $html.= '<td  style="text-align:right;  font-size:13px;"><b>'. $sum_get_coutomer_division_data['Targate'];
                            }
                             
                           
                        }
                    }
                //   print_r($array_achievement);
              
                    foreach($array_achievement as $array_achievement_data){
                         foreach($array_achievement_data as $array_achievement_data_array){
                        // print_r($array_achievement_data[$ii]['ItemDivID']);
                        // print_r($item_division_group_data['id']);
                        if($item_division_group_data['id'] == $array_achievement_data_array['ItemDivID']){
                            $mmm = 1;
                           
                             $total_data_achive+=$array_achievement_data_array['NetChallanAmt'];
                             
                           $total_data_achive_data+=$total_data_achive; 
                        }
              }}
                 
                        $html.= ' / '.round($total_data_achive).'</b></td >';
                 
                if($mm == "0"){
                    $html.= '<td ></td>';
                }
                }
               
            }
                $html.= '<td  style="text-align:right; font-size:13px;"><b>'.$total_data_total.' / '.round($total_data_achive_data).'</b></td>';
             $html.= '</tr>';
             $html.= '</tfoot>';
         echo json_encode($html);
    }
    // end target entery and target vs achivememnt
    
    public function market_outstanding(){
        
        if (!has_permission_new('market_outstanding', '', 'view')) {
            access_denied('access_denied');
        }
        $title = _l('Market Outstanding');
        $data['title'] = $title;
        $data['route'] = $this->misc_reports_model->get_all_route();
        $data['states'] = $this->misc_reports_model->get_all_states();
        $data['dist_type'] = $this->misc_reports_model->get_all_dist_type();
        $data['item_division'] = $this->misc_reports_model->get_all_item_division();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $this->load->view('admin/misc_reports/market_outstanding', $data);
    }
     public function export_market_outstanding(){
        	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
             $data = $this->misc_reports_model->market_outstanding_data($this->input->post());
        $credit_data = $this->misc_reports_model->market_outstanding_credit_data($this->input->post());
        $debit_data = $this->misc_reports_model->market_outstanding_debit_data($this->input->post());
        //$trans_data = $this->misc_reports_model->market_outstanding_trans_data($this->input->post());
        $last_billDate = $this->misc_reports_model->market_outstanding_last_billDate($this->input->post());
        $currDaySale = $this->misc_reports_model->market_outstanding_currDaySale($this->input->post());
        $preDaySale = $this->misc_reports_model->market_outstanding_preDaySale($this->input->post());
        $selected_company_details = $this->misc_reports_model->get_company_detail();
        $as_on = $this->input->post("as_on");
        $routName = $this->input->post("routName");
        
         
        $states = $this->input->post("states");
        $state_name = $this->sale_reports_model->get_state_name($states);
        
        $loc_type = $this->input->post("loc_type");
        $loc_type = $this->input->post('loc_type');
          if($loc_type == 1){
              $loc_type_name = "Local";
          }elseif($loc_type == 2){
              $loc_type_name = "OutStation";
          }elseif($loc_type == 3){
              $loc_type_name = "NotDefined";
          }
        
        $dist_type = $this->input->post("dist_type");
        $client_type_name = $this->sale_reports_model->get_client_type_name($client_type);
        
        $staff_name = $this->input->post("staff_name");
            
    		$writer = new XLSXWriter();
    		$j=0;
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		$j++;
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		$j++;
    	
    		$msg = "market outstanding Report  Date: ".$as_on;
    		$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		$j++;
    		 if($routName !=''){
              
    		$msg1 = "routName: ".$routName;
    		$filter1 = array($msg1);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter1);
    		$j++;
    		 }
    		  if($loc_type_name !=''){
              
    		$msg2 = "Loc Type: ".$loc_type_name;
    		$filter2 = array($msg2);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter2);
    		$j++;
    		 }
    		  if($client_type_name !=''){
              
    		$msg3 = "Distributor Type: ".$client_type_name->name;
    		$filter3 = array($msg3);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter3);
    		$j++;
    		 }
    		if($staff_name !=''){
              
    		$msg4 = "StaffName: ".$staff_name;
    		$filter4 = array($msg4);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter4);
    		$j++;
    		 }
    		  if($States !=''){
    		$msg5 = " States: ".$state_name->state_name;
    		$filter5 = array($msg5);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter5);
    		  }
    		// empty row
    		$list_add = [];
    		$list_add[] = "";
    		$list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
    		$list_add[] = "";
    	    $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            
            $writer->writeSheetRow('Sheet1', $list_add);
            
            
            $set_col_tk = [];
    		$set_col_tk["SOID"] =  'SOID';
    		$set_col_tk["StationName"] =  'StationName';
    		$set_col_tk["CtrlActID"] =  'CtrlActID';
    		$set_col_tk["AccountID"] =  'AccountID';
    		$set_col_tk["AccountName"] =  'AccountName';
    		$set_col_tk["StateID"] =  'StateID';
    		$set_col_tk["DebitAmt"] =  'DebitAmt';
    		$set_col_tk["CreditAmt"] =  'CreditAmt';
    		$set_col_tk["LastBillDate"] =  'LastBillDate';
    		$set_col_tk["PreDaySale"] =  'PreDaySale';
    		$set_col_tk["CurrDaySale"] =  'CurrDaySale';
    	
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
             /*$_transID = array();
        foreach($data as $key=>$value){
            foreach($trans_data as $key1=>$value1){
                if(strtoupper($value["AccountID"]) == strtoupper($value1["AccountID"])){
                    array_push($_transID, $value["AccountID"]);
                }
            }
        }*/
    		$totalDebit = 0; 
            $totalCredit = 0;
            $totalCurrSale = 0;
            $totalPreSale = 0;
    		foreach($data as $key=>$value){
            /*if (!in_array($value["AccountID"], $_transID)){
                
            }else{*/
            
            $crAmt = 0;
            $drAmt = 0;
            $bal = 0;
            foreach($credit_data as $key1=>$value1){
                if(strtoupper($value["AccountID"]) == strtoupper($value1["AccountID"])){
                    //$html .= '<td align="right">'.$value1["Credit_Amt"].'</td>';
                   $crAmt = $value1["Credit_Amt"];
                   
                }
            }
            
            foreach($debit_data as $key2=>$value2){
                if(strtoupper($value["AccountID"]) == strtoupper($value2["AccountID"])){
                    //$html .= '<td align="right">'.$value1["Credit_Amt"].'</td>';
                   $drAmt = $value2["Debit_Amt"];
                   
                }
            }
            
            $bal = $crAmt - $drAmt;
            $bal_new = $value["BAL1"] - $bal;
                if($bal_new == 0 || $bal_new == 0.00){
                    
                }else{
                    $list_add = [];
    			    $list_add[] = "";
    			    $list_add[] = $value["StationName"];
    			    $list_add[] = $value["CtrlAccountID"];
    			    $list_add[] = $value["AccountID"];
    			    $list_add[] = $value["company"];
    			    $list_add[] = $value["state"];
    			    
                    
                    if($bal_new <= 0){
                          $list_add[] = "";
                          $list_add[] = abs($bal_new);
                        
                        $totalCredit = $totalCredit + $bal_new;
                    }else{
                        
                          $list_add[] = abs($bal_new);
                           $list_add[] = "";
                       
                        $totalDebit = $totalDebit + $bal_new;
                    }
                    
                    $mm = 0;
                    foreach($last_billDate as $key3=>$value3){
                        if(strtoupper($value["AccountID"]) == strtoupper($value3["AccountID"])){
                            $list_add[] = _d(substr($value3["TransDate2"],0,10));
                            $mm++;
                        }
                    }
                    if($mm == 0){
                        $list_add[] = "";
                    }
                    
                    $mm2 = 0;
                    foreach($preDaySale as $key5=>$value5){
                        if(strtoupper($value["AccountID"]) == strtoupper($value5["AccountID"])){
                             $list_add[] = $value5["NetChallanAmt"];
                            $totalPreSale = $totalPreSale + $value5["NetChallanAmt"];
                            $mm2++;
                        }
                    }
                    if($mm2 == 0){
                         $list_add[] = "";
                    }
                    $mm1 = 0;
                    foreach($currDaySale as $key4=>$value4){
                        if(strtoupper($value["AccountID"]) == strtoupper($value4["AccountID"])){
                             $list_add[] = $value4["NetChallanAmt"];
                            $totalCurrSale = $totalCurrSale + $value4["NetChallanAmt"];
                            $mm1++;
                        }
                    }
                    if($mm1 == 0){
                        $list_add[] = "";
                    }
                    
                    $writer->writeSheetRow('Sheet1', $list_add);
                    $i++;
                }
            //}
            	  
    	}
    	        $list_add = [];
    			$list_add[] = "";
    			$list_add[] = "Total";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = abs($totalDebit);
    			$list_add[] = abs($totalCredit);
    			$list_add[] = "";
    			$list_add[] = $totalPreSale;
    			$list_add[] = $totalCurrSale;
    		
    			$writer->writeSheetRow('Sheet1', $list_add);
    			
    			$list_add = [];
    			$list_add[] = "";
    			$list_add[] = "Balance CR";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$balance_cr = abs($totalCredit) - abs($totalDebit);
    			$list_add[] = abs($balance_cr);
    			$list_add[] = "";
    			$list_add[] = "";
    			$list_add[] = "";
    			$writer->writeSheetRow('Sheet1', $list_add);
    	
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'market outstanding Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    public function market_outstanding_report(){
        
        if (!has_permission_new('market_outstanding', '', 'view')) {
            access_denied('access_denied');
        }
        $data = $this->misc_reports_model->market_outstanding_data($this->input->post());
        $credit_data = $this->misc_reports_model->market_outstanding_credit_data($this->input->post());
        $debit_data = $this->misc_reports_model->market_outstanding_debit_data($this->input->post());
        //$trans_data = $this->misc_reports_model->market_outstanding_trans_data($this->input->post());
        $last_billDate = $this->misc_reports_model->market_outstanding_last_billDate($this->input->post());
        $currDaySale = $this->misc_reports_model->market_outstanding_currDaySale($this->input->post());
        $preDaySale = $this->misc_reports_model->market_outstanding_preDaySale($this->input->post());
        $company_detail = $this->misc_reports_model->get_company_detail();
        $as_on = $this->input->post("as_on");
        $routName = $this->input->post("routName");
        
        $states = $this->input->post("states");
        $state_name = $this->sale_reports_model->get_state_name($states);
        
        $loc_type = $this->input->post("loc_type");
          if($loc_type == 1){
              $loc_type_name = "Local";
          }elseif($loc_type == 2){
              $loc_type_name = "OutStation";
          }elseif($loc_type == 3){
              $loc_type_name = "NotDefined";
          }
        
        $dist_type = $this->input->post("dist_type");
        $client_type_name = $this->sale_reports_model->get_client_type_name($client_type);
        
        $staff_name = $this->input->post("staff_name");
        
        
        /*$_transID = array();
        foreach($data as $key=>$value){
            foreach($trans_data as $key1=>$value1){
                if(strtoupper($value["AccountID"]) == strtoupper($value1["AccountID"])){
                    array_push($_transID, $value["AccountID"]);
                }
            }
        }*/
        
        $html = '';
        $html .= '<table class="tree table table-striped table-bordered table-market_outstanding fixTableHead" id="table-market_outstanding">';
        $html .= '<thead>';
        $html.= '<tr style="display:none;">';
        $html.= '<td colspan="11" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Market Outstanding</span></h5></td>';
        $html.= '</tr>';
        $html.= '<tr style="display:none;">';
        $html.= '<td colspan="11" style="font-size:10px;font-weight:600;text-align:center;">Date : '.$as_on.', RoutName : '.$routName.', States : '.$state_name->state_name.', Loc Type : '.$loc_type_name.', Distributor Type : '.$client_type_name->name.', StaffName : '.$staff_name.'</td>';
        $html.= '</tr>';
        $html .= '<tr>';
        $html .= '<th>Sr.No</th>';
        $html .= '<th>SOID</th>';
        $html .= '<th>StationName</th>';
        $html .= '<th>CtrlActID</th>';
        $html .= '<th>AccountID</th>';
        $html .= '<th>AccountName</th>';
        $html .= '<th>StateID</th>';
        $html .= '<th>DebitAmt</th>';
        $html .= '<th>CreditAmt</th>';
        $html .= '<th>LastBillDate</th>';
        $html .= '<th>PreDaySale</th>';
        $html .= '<th>CurrDaySale</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $totalDebit = 0; 
        $totalCredit = 0;
        $totalCurrSale = 0;
        $totalPreSale = 0;
        $i = 1;
        foreach($data as $key=>$value){
            /*if (!in_array($value["AccountID"], $_transID)){
                
            }else{*/
            
            $crAmt = 0;
            $drAmt = 0;
            $bal = 0;
            foreach($credit_data as $key1=>$value1){
                if(strtoupper($value["AccountID"]) == strtoupper($value1["AccountID"])){
                   $crAmt = $value1["Credit_Amt"];
                }
            }
            
            foreach($debit_data as $key2=>$value2){
                if(strtoupper($value["AccountID"]) == strtoupper($value2["AccountID"])){
                   $drAmt = $value2["Debit_Amt"];
                }
            }
            
            $bal = $crAmt - $drAmt;
            $bal_new = $value["BAL1"] - $bal;
                if($bal_new == 0 || $bal_new == 0.00){
                    
                }else{
                    $html .= '<tr>';
                    $html .= '<td>'.$i.'</td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.$value["StationName"].'</td>';
                    $html .= '<td align="center">'.$value["CtrlAccountID"].'</td>';
                    $html .= '<td align="center">'.$value["AccountID"].'</td>';
                    $html .= '<td>'.$value["company"].'</td>';
                    $html .= '<td align="center">'.$value["state"].'</td>';
                    
                    if($bal_new <= 0){
                        $html .= '<td align="right"></td>';
                        $html .= '<td align="right">'.number_format(abs($bal_new),2).'</td>';
                        $totalCredit = $totalCredit + $bal_new;
                    }else{
                        $html .= '<td align="right">'.number_format(abs($bal_new),2).'</td>';
                        $html .= '<td align="right"></td>';
                        $totalDebit = $totalDebit + $bal_new;
                    }
                    
                    $mm = 0;
                    foreach($last_billDate as $key3=>$value3){
                        if(strtoupper($value["AccountID"]) == strtoupper($value3["AccountID"])){
                            $html .= '<td align="right">'._d(substr($value3["TransDate2"],0,10)).'</td>';
                            $mm++;
                        }
                    }
                    if($mm == 0){
                        $html .= '<td></td>';
                    }
                    
                    $mm2 = 0;
                    foreach($preDaySale as $key5=>$value5){
                        if(strtoupper($value["AccountID"]) == strtoupper($value5["AccountID"])){
                            $html .= '<td align="right">'.number_format($value5["NetChallanAmt"],2).'</td>';
                            $totalPreSale = $totalPreSale + $value5["NetChallanAmt"];
                            $mm2++;
                        }
                    }
                    if($mm2 == 0){
                        $html .= '<td></td>';
                    }
                    $mm1 = 0;
                    foreach($currDaySale as $key4=>$value4){
                        if(strtoupper($value["AccountID"]) == strtoupper($value4["AccountID"])){
                            $html .= '<td align="right">'.number_format($value4["NetChallanAmt"],2).'</td>';
                            $totalCurrSale = $totalCurrSale + $value4["NetChallanAmt"];
                            $mm1++;
                        }
                    }
                    if($mm1 == 0){
                        $html .= '<td></td>';
                    }
                    
                    $html .= '</tr>';
                    $i++;
                }
            //}
            
        }
        $html .= '<tr >';
        $html .= '<td>'.$i.'</td>';
        $html .= '<td></td>';
        $html .= '<td><b style="color:red;">Total</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td align="right"> <b style="color:red;">'.number_format(abs($totalDebit),2).'</b></td>';
        $html .= '<td align="right"><b style="color:red;">'.number_format(abs($totalCredit),2).'</b></td>';
        $html .= '<td></td>';
        $html .= '<td align="right"><b style="color:red;">'.number_format($totalPreSale,2).'</b></td>';
        $html .= '<td align="right"><b style="color:red;">'.number_format($totalCurrSale,2).'</b></td>';
        $html .= '</tr>';
        $i++;
        $html .= '<tr >';
        $html .= '<td>'.$i.'</td>';
        $html .= '<td></td>';
        $html .= '<td ><b>Balance CR</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $balance_cr = abs($totalCredit) - abs($totalDebit);
        $html .= '<td align="right"><b>'.number_format(abs($balance_cr),2).'</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td align="right"><b></b></td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        echo json_encode($html);
    }
    
    // Start Crate ledger 
    public function crate_legder(){
        if (!has_permission_new('crate_ledger', '', 'view')) {
            access_denied('access_denied');
        }
        $title = _l('Crate Legder');
        $data['title'] = $title;
        $data['vendors'] = $this->misc_reports_model->get_vendor_data();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['state_list'] = $this->misc_reports_model->get_state_list();
        $this->load->view('admin/misc_reports/crate_legder', $data);
    }
    
    
    //  Crate Received vie vehicle return 
    public function crateRcvdVehicle(){
        if (!has_permission_new('Crates_received_via_vehicle_return', '', 'view')) {
            access_denied('access_denied');
        }
        $title = _l('Crate Received via Vehicle ');
        $data['title'] = $title;
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $this->load->view('admin/misc_reports/CrateRcvdVehicle', $data);
    }
    
    public function get_vendor_data($id =""){
        $vendor = $this->misc_reports_model->get_data_vendor($id);
         echo json_encode([
            'vendor' => $vendor,
        ]);
         
     }
    public function export_crate_legder()
    {
    	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'state_type'  => $this->input->post('state_type'),
           'loc_type'  => $this->input->post('loc_type'),
           'order_by'  => $this->input->post('order_by')
        );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $accountId = $this->input->post('accountId');
            $state_type = $this->input->post('state_type');
            $loc_type = $this->input->post('loc_type');
            $order_by = $this->input->post('order_by');
            $account_full_name = $this->input->post('account_full_name');
          
        $body_data = $this->misc_reports_model->GetCrateLedger($filterdata);
        /*echo json_encode($body_data['OpenCrate']);
        die;*/
        $selected_company_details = $this->misc_reports_model->get_company_detail();
    		
    	$writer = new XLSXWriter();
    	
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		
    		$msg = "Crate legder Report For Month: ".$month." ,  StaffID: " .$this->input->post('staff_account_name');
    		
    		 if($accountId != ''){
    		     	$msg = "Crate legder Report For Account: ".$account_full_name." ,  form date: " .$from_date." to date ".$to_date;
                 }else{
                     	$msg = "Crate legder Report For Billing Date: ".$from_date." ,  Vehicle Rtn Date: " .$to_date." State ".$state_type;
            }
            $filter = array($msg);
            $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		
    		// empty row
    		$list_add = [];
    		if($accountId !== ''){
        		$list_add[] = "";
        		$list_add[] = "";
        		$list_add[] = "";
        	    $list_add[] = "";
        		$list_add[] = "";
        	    $list_add[] = "";
        	    $list_add[] = "";
    		}else{
                $list_add[] = "";
        		$list_add[] = "";
        		$list_add[] = "";
        	    $list_add[] = "";
        		$list_add[] = "";
        		$list_add[] = "";
    		}
            $writer->writeSheetRow('Sheet1', $list_add);
            
            
            $set_col_tk = [];
            if($accountId !== ''){
    		$set_col_tk["VoucherID"] =  'VoucherID';
    		$set_col_tk["Date"] =  'Date';
    		$set_col_tk["Narration"] =  'Narration';
    		$set_col_tk["Debit"] =  'Debit';
    		$set_col_tk["Credit"] =  'Credit';
    		$set_col_tk["Balance"] =  'Balance';
    		$set_col_tk["DrCr"] =  'DrCr';
            }else{
    		
    		$set_col_tk["AccountId"] =  'AccountId';
    		$set_col_tk["Account Name"] =  'Account Name';
    		$set_col_tk["Address"] =  'Address';
    		$set_col_tk["OpCrates"] =  'OpCrates';
    		$set_col_tk["Bal"] =  'Bal';
    		$set_col_tk["DrCr"] =  'DrCr';
            }
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
            $TotalDebit = 0;
            $TotalCredit = 0;
            
            if($accountId !== ''){
                $list_add = [];
    			$list_add[] = "";
    			$list_add[] = to_sql_date($from_date);
    			$list_add[] = "Opening Crates";
    			$OPNBal = 0;
    			$DrCr = '';
    			if($body_data['OpenCrate'] > 0){
    			    $list_add[] = $body_data['OpenCrate'];
    			    $list_add[] = "";
                    $DrCr = 'Dr';
                    $OPNBal += $body_data['OpenCrate'];
                }else{
                    $list_add[] = "";
                    $list_add[] = $body_data['OpenCrate'];
                    $DrCr = 'Cr';
                    $OPNBal += $body_data['OpenCrate'];
                }
    			
    			$list_add[] = abs($body_data['OpenCrate']);
    			$list_add[] = $DrCr;
    			
                $writer->writeSheetRow('Sheet1', $list_add);
                
                foreach ($body_data['Trans'] as $key1 => $value1) {
                    $list_add = [];
                    $list_add[] = $value1['VoucherID'];
                    $list_add[] = _d($value1['Transdate']);
                    $list_add[] = $value1['Narration'];
                    if($value1['TType']== 'D'){
                        $OPNBal += $value1['Qty'];
                        $TotalDebit += $value1['Qty'];
                        $list_add[] = $value1['Qty'];
                        $list_add[] = "";
                    }else{
                        $OPNBal -= $value1['Qty'];
                        $TotalCredit += $value1['Qty'];
                        $list_add[] = "";
                        $list_add[] = $value1['Qty'];
                    }
                    
                    if($OPNBal > 0){
                        $DrCr = 'Dr';
                    }else{
                        $DrCr = 'Cr';
                    }
                    $list_add[] = abs($OPNBal);
                    $list_add[] = $DrCr;
                    $writer->writeSheetRow('Sheet1', $list_add);
                }
                
                $list_add = [];
                $list_add[] = '';
                $list_add[] = '';
                $list_add[] = 'Closing Crates';
                $list_add[] = $TotalDebit;
                $list_add[] = $TotalCredit;
                $list_add[] = abs($OPNBal);
                $list_add[] = $DrCr;
                $writer->writeSheetRow('Sheet1', $list_add);
            }else{
                $sr = 1;
                $OCratesSum = 0;
                $DCratesSum = 0;
                $CCratesSum = 0;
                foreach ($body_data['AllAccount'] as $key => $value) {
                    $OCrates = 0;
                    $DCrates = 0;
                    $CCrates = 0;
                    // Open Crates
                    foreach ($body_data['OpenCrate'] as $key1 => $value1) {
                        if(strtoupper($value['AccountID'])== strtoupper($value1['AccountID'])){
                            $OCrates = $value1['OQty'];
                        }
                    }
                    // Debit Crates
                    foreach ($body_data['Debit'] as $key11 => $value11) {
                        if(strtoupper($value['AccountID'])== strtoupper($value11['AccountID'])){
                            $DCrates = $value11['OQty'];
                        }
                    }
                    
                    // Credit Crates
                    foreach ($body_data['Credit'] as $key111 => $value111) {
                        if(strtoupper($value['AccountID'])== strtoupper($value111['AccountID'])){
                            $CCrates = $value111['OQty'];
                        }
                    }
                    
                        $OCratesSum += $OCrates;
                        $DCratesSum += $DCrates;
                        $CCratesSum += $CCrates;
                        $BalCrate = $OCrates - $CCrates + $DCrates;
                        
                    if($BalCrate == '0'){
                        
                    }else{
                        $list_add = [];
                        $list_add[] = $value['AccountID'];
                        $list_add[] = $value['company'];
                        $list_add[] = $value['address'];
                        $list_add[] = $OCrates;
                        $DrCr = '';
                        if($BalCrate >0){
                            $DrCr = 'Dr';
                        }else{
                            $DrCr = 'Cr';
                        }
                        $list_add[] = abs($BalCrate);
                        
                        $DrCr = '';
                        if($BalCrate >0){
                            $DrCr = 'Dr';
                        }else{
                            $DrCr = 'Cr';
                        }
                        $list_add[] = $DrCr;
                        $writer->writeSheetRow('Sheet1', $list_add);
                    }
                }
                
                $list_add = [];
                $list_add[] = '';
                
                        
                $html .= '<tr>';
                $html .= '<td align="center"></td>';
                $Total = $OCratesSum - $CCratesSum + $DCratesSum;
                $DrCr1 = '';
                if($Total >0){
                    $DrCr1 = 'Dr';
                }else{
                    $DrCr1 = 'Cr';
                }
                $DrCr11 = '';
                if($OCratesSum >0){
                    $DrCr11 = 'Dr';
                }else{
                    $DrCr11 = 'Cr';
                }
                $list_add[] = '';
                $list_add[] = 'Total';
                $list_add[] = abs($OCratesSum).' '.$DrCr11;
                $list_add[] = abs($Total).' '.$DrCr1;
                $writer->writeSheetRow('Sheet1', $list_add);
            }
            
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'Crate_legder_Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    
    public function GetCratesRcvdVehicle()
     {
        $to_date = $this->input->post('to_date');
          
        $body_data = $this->misc_reports_model->getCratesRcvdVehicle($to_date);
        
        $company_details = $this->misc_reports_model->get_company_detail();
        $table_width = '100%';
        $colspan = 6;
        $html = '';
        $SRCount = 0;
        $VRtnWiseTotal = 0;
        $PartyWiseTotal = 0;
        $html .= '<table class="table-striped table-bordered production_report" id="production_report" width="'.$table_width.'">';
        $html .= '<thead style="font-size:11px;">';
        $html .= '<tr>';
        $html .= '<th>ChallanID</th>';
        $html .= '<th>Return ID</th>';
        $html .= '<th>VehicleID</th>';
        $html .= '<th>Route Name</th>';
        $html .= '<th>Driver Name</th>';
        $html .= '<th>Sr.</th>';
        $html .= '<th>Account Name</th>';
        $html .= '<th>Crates</th>';
        $html .= '<th>Total</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($body_data as $key => $value) {
            $partyCount = count($value["PartyDetails"]);
            if($partyCount >=2){
               $row = 'rowspan ="'.$partyCount.'"';
            }else{
                $row = '';
            }
            if($partyCount >=1){
                $SRCount++;
                $html .= '<tr>';
                $html .= '<td '.$row.'>'.$value["ChallanID"].'</td>';
                $html .= '<td '.$row.'>'.$value["ReturnID"].'</td>';
                $html .= '<td '.$row.'>'.$value["VehicleID"].'</td>';
                $html .= '<td '.$row.'>'.$value["name"].'</td>';
                $html .= '<td '.$row.'>'.$value["firstname"].' '.$value["lastname"].'</td>';
                $html .= '<td align = "center">1</td>';
                $html .= '<td>'.$value["PartyDetails"]['0']['company'].'</td>';
                $html .= '<td align = "right">'.$value["PartyDetails"]['0']['Qty'].'</td>';
                $PartyWiseTotal += $value["PartyDetails"]['0']['Qty'];
                $html .= '<td '.$row.' align = "right">'.$value["Crates"].'</td>';
                $VRtnWiseTotal += $value["Crates"];
                $html .= '</tr>';
                if($partyCount >= 2){
                    $j = 1;
                    foreach ($value["PartyDetails"] as $key1 => $value1) {
                        if($j > 1){
                            $SRCount++;
                            $html .= '<tr>';
                            $html .= '<td align = "center">'.$j.'</td>';
                            $html .= '<td>'.$value1["company"].'</td>';
                            $html .= '<td align = "right">'.$value1["Qty"].'</td>';
                            $PartyWiseTotal += $value1["Qty"];
                            $html .= '</tr>';
                        }
                        $j++;
                    }
                }
            }
        }
        
        // Footer
        $html .= '</tr>';
        $html .= '<td></td>';
        $html .= '<td>Total Count</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td align = "center">'.$SRCount.'</td>';
        $html .= '<td>Grand Total</td>';
        $html .= '<td align = "right">'.$PartyWiseTotal.'</td>';
        $html .= '<td align = "right">'.$VRtnWiseTotal.'</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        
        echo json_encode($html);
        die;
     }
    public function get_cretes_dataNew()
     { 
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'state_type'  => $this->input->post('state_type'),
           'loc_type'  => $this->input->post('loc_type'),
           'order_by'  => $this->input->post('order_by')
        );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $accountId = $this->input->post('accountId');
            $state_type = $this->input->post('state_type');
            $loc_type = $this->input->post('loc_type');
            $order_by = $this->input->post('order_by');
            $account_full_name = $this->input->post('account_full_name');
          
        $body_data = $this->misc_reports_model->GetCrateLedger($filterdata);
        /*echo json_encode($body_data['OpenCrate']);
        die;*/
        $company_details = $this->misc_reports_model->get_company_detail();
        $table_width = '100%';
       $colspan = 6;
        $html = '';
        $html .= '<table class="table-striped table-bordered CrateLedger" id="CrateLedger" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
            
            $html .= '<tr style="display:none;" class="print_hide">';
            $html .= '<td colspan="'.$colspan.'" style="font-size:18px;font-weight:700;text-align:center;"><b>'.$company_details->company_name.'</b></td>';
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><b>'.$company_details->address.'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><b>Crate Legder</b></td>';
          $html .= '</tr>';
           $html .= '<tr style="display:none;" >';
            if($accountId != ''){
                
                 $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;"><b>Account:</b>'.$account_full_name.', form date:'.$from_date.', to date:'.$to_date.'</span></td>';
           
            }else{
                $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;"> Billing Date:'.$from_date.', Vehicle Rtn Date:'.$to_date.', State:'.$state_type.'</span></td>';
            }
            if($accountId !== ''){
                $html .= '<tr>';
                $html .= '<th align="center">VoucherID</th>';
                $html .= '<th align="center">Date</th>';
                $html .= '<th align="center">Narration</th>';
                $html .= '<th align="center">Debit</th>';
                $html .= '<th align="center">Credit</th>';
                $html .= '<th align="center">Balance</th>';
                $html .= '</tr>';
                
            }else{
                $html .= '<tr>';
                $html .= '<th align="center">S.No.</th>';
                $html .= '<th align="center">AccountId</th>';
                $html .= '<th align="center">Account Name</th>';
                $html .= '<th align="center">Address</th>';
                $html .= '<th align="center">OpCrates</th>';/*
                $html .= '<th align="center">Debit Crates</th>';
                $html .= '<th align="center">Credit Crates</th>';*/
                $html .= '<th align="center">Balance</th>';
                $html .= '</tr>';
            }
            $html .= '</thead>';
            $html .= '<tbody>';
            $TotalDebit = 0;
            $TotalCredit = 0;
            if($accountId !== ''){
                $html .= '<tr>';
                $html .= '<td align="center"></td>';
                $html .= '<td align="center" style="color:#e93232;font-weight:700;">'.to_sql_date($from_date).'</td>';
                $html .= '<td align="left" style="color:#e93232;font-weight:700;">Opening Crates</td>';
                $OPNBal = 0;
               
                $DrCr = '';
                if($body_data['OpenCrate'] > 0){
                    $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.$body_data['OpenCrate'].'</td>';
                    $html .= '<td align="center"></td>';
                    $DrCr = 'Dr';
                    $OPNBal += $body_data['OpenCrate'];
                }else{
                    $html .= '<td align="center"></td>';
                    $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.$body_data['OpenCrate'].'</td>';
                    $DrCr = 'Cr';
                    $OPNBal += $body_data['OpenCrate'];
                }
                
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.abs($body_data['OpenCrate']).' '.$DrCr.'</td>';
                $html .= '</tr>';
                
                foreach ($body_data['Trans'] as $key1 => $value1) {
                    $html .= '<tr>';
                    $html .= '<td align="center">'.$value1['VoucherID'].'</td>';
                    $html .= '<td align="center">'._d($value1['Transdate']).'</td>';
                    $html .= '<td align="left">'.$value1['Narration'].'</td>';
                    if($value1['TType']== 'D'){
                        $OPNBal += $value1['Qty'];
                        $TotalDebit += $value1['Qty'];
                        $html .= '<td align="right">'.$value1['Qty'].'</td>';
                        $html .= '<td align="center"></td>';
                    }else{
                        $OPNBal -= $value1['Qty'];
                        $TotalCredit += $value1['Qty'];
                        $html .= '<td align="center"></td>';
                        $html .= '<td align="right">'.$value1['Qty'].'</td>';
                    }
                    if($OPNBal > 0){
                        $DrCr = 'Dr';
                    }else{
                        $DrCr = 'Cr';
                    }
                    $html .= '<td align="right">'.abs($OPNBal).' '.$DrCr.'</td>';
                    $html .= '</tr>';
                }
                
                $html .= '<tr>';
                $html .= '<td align="center"></td>';
                $html .= '<td align="center"></td>';
                $html .= '<td align="left" style="color:#e93232;font-weight:700;">Closing Crates</td>';
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.$TotalDebit.'</td>';
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.$TotalCredit.'</td>';
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.abs($OPNBal).' '.$DrCr.'</td>';
                $html .= '</tr>';
            }else{
                $sr = 1;
                $OCratesSum = 0;
                $DCratesSum = 0;
                $CCratesSum = 0;
                foreach ($body_data['AllAccount'] as $key => $value) {
                    $OCrates = 0;
                    $DCrates = 0;
                    $CCrates = 0;
                    // Open Crates
                    foreach ($body_data['OpenCrate'] as $key1 => $value1) {
                        if(strtoupper($value['AccountID'])== strtoupper($value1['AccountID'])){
                            $OCrates = $value1['OQty'];
                        }
                    }
                    // Debit Crates
                    foreach ($body_data['Debit'] as $key11 => $value11) {
                        if(strtoupper($value['AccountID'])== strtoupper($value11['AccountID'])){
                            $DCrates = $value11['OQty'];
                        }
                    }
                    
                    // Credit Crates
                    foreach ($body_data['Credit'] as $key111 => $value111) {
                        if(strtoupper($value['AccountID'])== strtoupper($value111['AccountID'])){
                            $CCrates = $value111['OQty'];
                        }
                    }
                    
                    $OCratesSum += $OCrates;
                        $DCratesSum += $DCrates;
                        $CCratesSum += $CCrates;
                        $BalCrate = $OCrates - $CCrates + $DCrates;
                        
                    if($BalCrate == '0'){
                        
                    }else{
                        $html .= '<tr>';
                        $html .= '<td align="right">'.$sr.'</td>';
                        $html .= '<td align="center">'.$value['AccountID'].'</td>';
                        $html .= '<td align="left">'.substr($value['company'],0,45).'</td>';
                        $html .= '<td align="left">'.substr($value['address'],0,30).'</td>';
                        $html .= '<td align="right">'.$OCrates.'</td>';
                        
                        $DrCr = '';
                        if($BalCrate >0){
                            $DrCr = 'Dr';
                        }else{
                            $DrCr = 'Cr';
                        }
                        $html .= '<td align="right">'.abs($BalCrate).' '.$DrCr.'</td>';
                        $html .= '</tr>';
                        $sr++;
                    }
                }
                
                $html .= '<tr>';
                $html .= '<td align="center"></td>';
                $Total = $OCratesSum - $CCratesSum + $DCratesSum;
                $DrCr1 = '';
                if($Total >0){
                    $DrCr1 = 'Dr';
                }else{
                    $DrCr1 = 'Cr';
                }
                $DrCr11 = '';
                if($OCratesSum >0){
                    $DrCr11 = 'Dr';
                }else{
                    $DrCr11 = 'Cr';
                }
                $html .= '<td align="left" style="color:#e93232;font-weight:700;"></td>';
                $html .= '<td align="left" style="color:#e93232;font-weight:700;">Total</td>';
                $html .= '<td align="left" style="color:#e93232;font-weight:700;"></td>';
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.abs($OCratesSum).' '.$DrCr11.'</td>';
                $html .= '<td align="right" style="color:#e93232;font-weight:700;">'.abs($Total).' '.$DrCr1.'</td>';
                $html .= '</tr>';
            }
            
            
            $html .= '</tbody>';
            $html .= '</table>';
        echo json_encode($html);
        die;
    }
    public function get_cretes_data()
     {
         
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'state_type'  => $this->input->post('state_type'),
           'loc_type'  => $this->input->post('loc_type'),
           'order_by'  => $this->input->post('order_by')
          );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $accountId = $this->input->post('accountId');
            $state_type = $this->input->post('state_type');
            $loc_type = $this->input->post('loc_type');
            $order_by = $this->input->post('order_by');
            $account_full_name = $this->input->post('account_full_name');
          
        $body_data = $this->misc_reports_model->get_Crates_for_body_data($filterdata);
        $company_details = $this->misc_reports_model->get_company_detail();
        $table_width = '100%';
       $colspan = 6;
        $html = '';
            $html .= '<table class="table-striped table-bordered CrateLedger" id="CrateLedger" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
            
            $html .= '<tr style="display:none;" class="print_hide">';
            $html .= '<td colspan="'.$colspan.'" style="font-size:18px;font-weight:700;text-align:center;"><b>'.$company_details->company_name.'</b></td>';
            $html .= '</tr>';
            
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><b>'.$company_details->address.'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><b>Crate Legder</b></td>';
          $html .= '</tr>';
           $html .= '<tr style="display:none;" >';
            if($accountId != ''){
                
                 $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;"><b>Account:</b>'.$account_full_name.', form date:'.$from_date.', to date:'.$to_date.'</span></td>';
           
            }else{
                $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;"> Billing Date:'.$from_date.', Vehicle Rtn Date:'.$to_date.', State:'.$state_type.'</span></td>';
            }
            if($accountId !== ''){
                $html .= '<tr>';
                $html .= '<th align="center">PassedFrom</th>';
                $html .= '<th align="center">VoucherID</th>';
                $html .= '<th align="center">Date</th>';
                $html .= '<th align="center">Narration</th>';
                $html .= '<th align="center">Debit</th>';
                $html .= '<th align="center">Credit</th>';
                $html .= '<th align="center">Balance</th>';
                $html .= '</tr>';
                
            }else{
                $html .= '<tr>';
                $html .= '<th align="center">S.No.</th>';
                $html .= '<th align="center">AccountId</th>';
                $html .= '<th align="center">Account Name</th>';
                $html .= '<th align="center">Address</th>';
                $html .= '<th align="center">OpCrates</th>';
                $html .= '<th align="center">Debit Crates</th>';
                $html .= '<th align="center">Credit Crates</th>';
                $html .= '<th align="center">Balance</th>';
                /*$html .= '<th align="center">Last Bill Date</th>';
                $html .= '<th align="center">PrevDay Crates</th>';
                $html .= '<th align="center">CurrDay Crates</th>';*/
                $html .= '</tr>';
            }
            $html .= '</thead>';
            if($accountId !== ''){
               $html .= '<tbody>';
               $totalDr = 0;
               $totalCr = 0;
               $opncreates = 0;
               //foreach ($body_data['opn_caret'] as $key1 => $value1) {
                   $html .= '<tr>';
                   $html .= '<td align="center"></td>';
                   $html .= '<td align="center"></td>';
                   $html .= '<td align="center"><span style="color:#e93232;font-weight:700;text-align:right;">'.substr(_d($body_data['opn_caret']->Transdate),0,10).'</span></td>';
                   $html .= '<td align="left" ><span style="color:#e93232;font-weight:700;text-align:right;">Opening Crates</span></td>';
                   if($body_data['opn_caret']->TType == "C"){
                       $html .= '<td align="center"></td>';
                       $opncreates1 = $body_data['opn_caret']->Qty.'Cr';
                       $totalCr -= $body_data['opn_caret']->Qty;
                       $opncreates +=$body_data['opn_caret']->Qty;
                       $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.$body_data['opn_caret']->Qty.'</span></td>';
                   }else if($body_data['opn_caret']->TType == "D"){
                       $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.$body_data['opn_caret']->Qty.'</span></td>';
                       $totalDr += $body_data['opn_caret']->Qty;
                       $opncreates1 = $body_data['opn_caret']->Qty.'Dr';
                       $opncreates +=$body_data['opn_caret']->Qty;
                       $html .= '<td align="center"></td>';
                   }else{
                       $opncreates1 = '0Dr';
                       $html .= '<td align="center">0</td>';
                       $html .= '<td align="center"></td>';
                   }
                   $html .= '<td align="right" style="color:#e93232;font-weight:700;text-align:right;">'.$opncreates1.'</td>';
                   $html .= '</tr>';
               //}
               foreach ($body_data['all'] as $key => $value) {
                   $html .= '<tr>';
                   $html .= '<td align="center">'.$value["PassedFrom"].'</td>';
                   $html .= '<td align="center">'.$value["VoucherID"].'</td>';
                   $html .= '<td align="center">'.substr(_d($value["Transdate"]),0,10).'</td>';
                   $html .= '<td align="left">'.$value["Narration"].'</td>';
                   if($value["TType"] == "C"){
                       $html .= '<td align="center"></td>';
                       $html .= '<td align="right">'.$value["Qty"].'</td>';
                       $totalCr += $value["Qty"];
                       $opncreates -= $value["Qty"];
                   }else if($value["TType"] == "D"){
                       $html .= '<td align="right">'.$value["Qty"].'</td>';
                       $totalDr += $value["Qty"];
                       $opncreates += $value["Qty"];
                       $html .= '<td align="center"></td>';
                   }
                   if($opncreates >0){
                       $crdr = 'Dr';
                   }else{
                       $crdr = 'Cr';
                   }
                   $html .= '<td align="right">'.$opncreates.$crdr.'</td>';
                   $html .= '</tr>';
               }
               $html .= '</tbody>';
               
               $html .= '<tfoot>';
               $html .= '<tr>';
               $html .= '<td></td>';
               $html .= '<td></td>';
               $html .= '<td></td>';
               $html .= '<td ><span style="color:#e93232;font-weight:700;text-align:right;">Closing Balance</span></td>';
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.$totalDr.'</span></td>';
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.$totalCr.'</span></td>';
               if($opncreates >0){
                       $crdr = 'Dr';
                   }else{
                       $crdr = 'Cr';
                   }
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.$opncreates.$crdr.'</span></td>';
               $html .= '</tr>';
               $html .= '</tfoot>';
           }else{
            $html .= '<tbody>';
           $i = 1;
           
           $total_c = 0;
           $total_d = 0;
           $total_opn = 0;
           $preDayTT = 0;
           $currDayTT = 0;
           
               foreach ($body_data['all'] as $key => $value) {
                
                $opn_dr = 0;
                $opn_cr = 0;
                $final_opn_in_dr = 0;
                $final_opn_in_cr = 0;
                if(!is_null($value["AccountID"])){
                    $html .= '<tr>';
                    $html .= '<td align="center">'.$i.'</td>';
                    $html .= '<td align="left">'.$value["AccountID"].'</td>';
                    $html .= '<td align="left">'.$value["company"].'</td>';
                    $html .= '<td align="left">'.$value["address"].'</td>';
                    
                    foreach ($body_data['opn_debit'] as $key1 => $value_OPNdebit) {
                        if($value_OPNdebit['AccountID'] == $value["AccountID"]){
                            $opn_dr = $value_OPNdebit["sum_total"];
                        }
                    }
                    foreach ($body_data['opn_credit'] as $key1 => $value_OPNcredit) {
                        if($value_OPNcredit['AccountID'] == $value["AccountID"]){
                            $opn_cr = $value_OPNcredit["sum_total"];
                        }
                    }
                    $final_opn = $opn_dr - $opn_cr;
                    $total_opn+= $final_opn;
                    if($final_opn > 0){
                        $final_opn_in_dr = $final_opn;
                    }else{
                        $final_opn_in_cr = $final_opn;
                    }
                    $html .= '<td align="right">'.$final_opn.'</td>';
                    $BalCrate = 0;
                    $m = 0;
                    foreach ($body_data['debit'] as $key => $value_debit) {
                        
                        if($value_debit['AccountID'] == $value["AccountID"]){
                            $m =1; 
                            $new_sum = $value_debit["sum_total"] + $final_opn_in_dr;
                            $BalCrate += number_format($new_sum, 2, '.', '');
                            $html .= '<td align="right">'.number_format($new_sum, 2, '.', '').'</td>';
                            $total_d+= number_format($new_sum, 2, '.', '');
                        }
                    
                    }
                    if($m == 0){
                        if($final_opn_in_dr == "0"){
                            $html .= '<td align="center"></td>';
                        }else{
                            $total_d+=  number_format($final_opn_in_dr, 2, '.', '');
                            $BalCrate += number_format($final_opn_in_dr, 2, '.', '');
                            $html .= '<td align="right">'.number_format($final_opn_in_dr, 2, '.', '').'</td>';
                        }
                    }
                    $n = 0;
                      foreach ($body_data['credit'] as $key => $value_credit) {
                        if($value_credit['AccountID'] == $value["AccountID"]){
                            $n = 1;
                            $new_sum2 = $value_credit["sum_total"] + $final_opn_in_cr;
                            $BalCrate -= number_format($new_sum2, 2, '.', '');
                            $html .= '<td align="right">'.number_format($new_sum2, 2, '.', '').'</td>';
                             $total_c+= number_format($new_sum2, 2, '.', '');
                        }
                    }
                    if($n == 0){
                        if($final_opn_in_cr == "0"){
                            $html .= '<td align="center"></td>';
                        }else{
                            $total_c+=  number_format($final_opn_in_cr, 2, '.', '');
                            $html .= '<td align="right">'.number_format($final_opn_in_cr, 2, '.', '').'</td>';
                            $BalCrate -= number_format($final_opn_in_cr, 2, '.', '');
                        }
                    }
                    if($BalCrate >0){
                       $crdr = 'Dr';
                    }else{
                       $crdr = 'Cr';
                    }
                    $html .= '<td align="center">'.number_format($BalCrate, 2, '.', '').$crdr.'</td>';
                    // last bill
                    /*$n1 = 0;
                      foreach ($body_data['lastBill'] as $key2 => $value_lastbill) {
                        if($value_lastbill['AccountID'] == $value["AccountID"]){
                            $n1 = 1;
                            $html .= '<td align="center">'.substr(_d($value_lastbill["lastBill"]),0,10).'</td>';
                        }
                    }
                    if($n1 == 0){
                        $html .= '<td align="center"></td>';
                    }*/
                    
                    // PreDay Carets
                    /*$n2 = 0;
                      foreach ($body_data['preDay'] as $key3 => $value_preDay) {
                        if(strtoupper($value_preDay['AccountID']) == strtoupper($value["AccountID"])){
                            $n2 = 1;
                            $html .= '<td align="right">'.$value_preDay["sum_total"].'</td>';
                            $preDayTT+= $value_preDay["sum_total"];
                        }
                    }
                    if($n2 == 0){
                        $html .= '<td align="center"></td>';
                    }*/
                    
                    // CurrDay Carets
                    /*$n3 = 0;
                      foreach ($body_data['currDay'] as $key3 => $value_currDay) {
                        if(strtoupper($value_currDay['AccountID']) == strtoupper($value["AccountID"])){
                            $n3 = 1;
                            $html .= '<td align="right">'.$value_currDay["sum_total"].'</td>';
                            $currDayTT+= $value_currDay["sum_total"];
                        }
                    }
                    if($n3 == 0){
                        $html .= '<td align="center"></td>';
                    }*/
                    
                $html .= '</tr>';
                $i++;
                }
                
            }
          
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
                    $html .= '<td align="center"></td>';
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="center"><b>Total</b></td>';
                   
                    $html .= '<td align="left"></td>';
                    $html .= '<td align="right"><b>'.$total_opn.'</b></td>';
                    $html .= '<td align="right"><b>'.$total_d.'</b></td>';
                    $html .= '<td align="right" ><b>'.$total_c.'</b></td>';
                    $html .= '<td align="right"></td>';
                    /*$html .= '<td align="right"><b>'.$preDayTT.'</b></td>';
                    $html .= '<td align="right"><b>'.$currDayTT.'</b></td>';*/
            $html .= '</tr>';
            $html .= '</tfoot>';
           }
            $html .= '</table>';
        echo json_encode($html);
        die;
     }
    // End Crate ledger
     public function load_data()
     {
        
        $data =$this->purchase_model->table_data($this->input->post());
        $states = $this->input->post('states');
        $status = $this->input->post('status');
        $data_state_name  = $this->db->get_where('tblxx_statelist',array('short_name'=>$states))->row_array(); 
        // echo $this->db->last_query();
       if($data_state_name ==''){
          $data_state_name['state_name']  ='';  
       }
       if($status == ''){
          $status  ='';  
       }
        $html ='';
        foreach($data as $value){
            $html.= '<tr>';
            $html.= '<td>'.$value['AccountID'].'</td>';
              $companyy  = $value['company'];
                $isPerson = false;
            
                if ($companyy == '') {
                    $companyy  = _l('no_company_view_profile');
                    $isPerson = true;
                }
            
                $url = admin_url('purchase/vendor/' . $value['AccountID']);
            
                if ($isPerson && $value['contact_id']) {
                    $url .= '?contactid=' . $value['contact_id'];
                }
                $companyy = '<a href="' . $url . '">' . $companyy . '</a>';

                $company .= '<div class="row-options">';
                $company .= '<a href="' . $url . '">' . _l('view') . '</a>';
            
                if ($aRow['registration_confirmed'] == 0 && is_admin()) {
                    $company .= ' | <a href="' . admin_url('purchase/confirm_registration/' . $aRow['AccountID']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
                }
                if (!$isPerson) {
                    $company .= ' | <a href="' . admin_url('purchase/vendor/' . $aRow['AccountID'] . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';
                }
                if ($hasPermissionDelete) {
                    $company .= ' | <a href="' . admin_url('purchase/delete_vendor/' . $aRow['AccountID']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }
            
                $company .= '</div>';
            
                $row_c = $companyy;
            if (has_permission('vendors','','edit')) {
                $vendor_name = '<a href="' . $url . '">' . $value['company'] . '</a>';
            }else{
                $vendor_name = $value['company'];
            }    
            $html.= '<td>'.$vendor_name.'</td>';
            $html.= '<td>'.$value['StationName'].'</td>';
             $city_name = get_city_name($value['city']);
                if($city_name->city_name){
                    $city = $city_name->city_name;
                }else{
                    $city = $value['city'];
                }
                $row = $city;
            $html.= '<td>'.$row.'</td>';
             
            $html.= '<td>'.$value['state'].'</td>';
            $html.= '<td>'.nl2br($value['address']).'</td>';
            
               if($value['actstatus'] == 1){
                    $status = "Active";
                }else{
                    $status = "DeActive";
                }
           
            $html.= '<td>'.$status.'</td>';
            $html.= '</tr>';
        }
        // echo $html;
        $data_array =array('html'=>$html,'state'=>$data_state_name,'status'=>$status);
      echo json_encode($data_array);
    }
	
	public function survey_report()
    {
        if (!has_permission_new('survey_report', '', 'view')) {
            access_denied('access_denied');
        }
        $data['title'] = "Survey Reports";
        $data['staff'] = $this->misc_reports_model->All_staff();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        // print_r($data);die();
        $this->load->view('admin/misc_reports/survey', $data);
    }
	
	public function get_survey_data()
    {
        $filterdata = array(
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'staff_id' => $this->input->post('staff_id'),
        );

        $staff_id = $this->input->post('staff_id');
        $body_data = $this->misc_reports_model->get_survey_data($filterdata);
        
        $company_details = $this->accounts_master_model->get_company_detail();
        $colspan = 8;
        $html = '';
        $html .= '<table class="table-striped table-bordered daily_report fixTableHead " id="daily_report" width="100%">';
        $html .= '<thead style="font-size:11px;">';

        $html .= '<tr style="display:none;">';
        $html .= '<td colspan="' . $colspan . '" style="font-size:18px;font-weight:700;text-align:center;"><b>' . $company_details->company_name . '</b></td>';
        $html .= '</tr>';

        $html .= '<tr style="display:none;">';
        $html .= '<td colspan="' . $colspan . '" style="font-size:16px;font-weight:600;" align="center"><b>' . $company_details->address . '</b></td>';
        $html .= '</tr>';

        $html .= '<tr style="display:none;">';
        $html .= '<td colspan="' . $colspan . '" style="text-align:center;"><span class="report_for"><b>Survey List  - From </b>  - ' . $this->input->post('from_date') . ' To ' . $this->input->post('to_date') . '</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th align="left">Sr.No</th>';
        $html .= '<th align="left">Name</th>';
        $html .= '<th align="left">Total Survey</th>';
        $html .= '<th align="center">Date</th>';
        $html .= '<th align="left">State</th>';
        $html .= '<th align="left">City</th>';
        $html .= '<th align="left">Taluka</th>';
        $html .= '<th align="left">Village</th>';
        $html .= '<th align="left">Generated By</th>';
        $html .= '<th align="left">Action</th>';
        $html .= '</tr>';

        $html .= '</thead>';
        $html .= '<tbody>';

        $i=1;
        foreach ($body_data as $key => $value) {
            $detail = '<a href="' . admin_url('misc_reports/survey_detail/' . $value['id']) . '" target="_blank">View Details</a>';
            
            $html .= '<tr>';
            $html .= '<td align="left">' . $i . '</td>';
            $html .= '<td align="left">' . $value["name"] . '</td>';
            $html .= '<td>
                        <div class="progress-bar-container" style="width:200px; height:12px; background:#e0e0e0; border-radius:12px; position:relative; margin:0 auto; overflow:hidden;">
                            <div class="progress-bar-fill" style="width:' . $value['totalpercent'] . '%; height: 100%; background-color: lightgreen; border-radius: 12px 0 0 12px; transition: width 0.4s ease-in-out;"></div>
                            <span class="progress-label" style="position:absolute; left:50%; top:50%; transform: translate(-50%, -50%); font-weight:bold; color:black; font-size:12px; pointer-events:none;">
                                ' . $value['totalpercent'] . '%
                            </span>
                        </div>
                    </td>';
            $html .= '<td align="center">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';
            $state = $this->misc_reports_model->get_state($value["state"]);
            $html .= '<td align="left">' . $state->state_name . '</td>';
            $city = $this->misc_reports_model->get_city($value["district"]);
            $html .= '<td align="left">' . $city->city_name . '</td>';
            $taluka = $this->misc_reports_model->get_taluka($value["taluka"]);
            $html .= '<td align="left">' . $taluka->TalukaName . '</td>';
            $html .= '<td align="left">' . $value["village"] . '</td>';
            $html .= '<td align="left">' . $value["firstname"] . ' ' . $value["lastname"] . '</td>';
            $html .= '<td align="left">' . $detail . '</td>';
            $html .= '</tr>';
            $i++;
        }
        $html .= '</tbody>';
        $html .= '</table>';
        echo json_encode($html);
        die;
    }
	 
	public function survey_detail($SurveyID)
    {
        $data['title'] = "Survey Report Details";
        $filterdata = array();
        $data['dependants'] = $this->misc_reports_model->Get_dependants($filterdata,$SurveyID);
        $data['details'] = $this->misc_reports_model->get_survey_details($SurveyID);
        $data['equipment'] = $this->misc_reports_model->Get_equipment($filterdata,$SurveyID);
        $data['livestock'] = $this->misc_reports_model->Get_livestock($filterdata,$SurveyID);
        $data['crop'] = $this->misc_reports_model->Get_crop_pattern($filterdata,$SurveyID);
        $data['production'] = $this->misc_reports_model->Get_production_cost($filterdata,$SurveyID);
        /*echo "<pre>";
        print_r($data['crop']);
        die;*/
        $this->load->view('admin/misc_reports/survey-tab', $data);
    }

    public function export_survey_details()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        if ($this->input->post()) {

            $filterdata = array(
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'staff_id' => $this->input->post('staff_id')
            );
            $From_year = substr(to_sql_date($filterdata["from_date"]),2,2); 
            $To_year = substr(to_sql_date($filterdata["to_date"]),2,2); 
            $Crop_pattern_start = $From_year - 2;
            $Crop_pattern_end = $To_year;
            $years = array();
            for($i = $Crop_pattern_start;$i<=$Crop_pattern_end;$i++){
                array_push($years,$i);
            }
            
            $staff_id = $this->input->post('staff_id');
            $body_data = $this->misc_reports_model->get_survey_data($filterdata);
            $Get_dependants = $this->misc_reports_model->Get_dependants($filterdata);
            $Get_equipment = $this->misc_reports_model->Get_equipment($filterdata);
            $Get_livestock = $this->misc_reports_model->Get_livestock($filterdata);
            $Get_crop_pattern = $this->misc_reports_model->Get_crop_pattern($filterdata);
            $Get_production_cost = $this->misc_reports_model->Get_production_cost($filterdata);

            $selected_company_details = $this->misc_reports_model->get_company_detail();

            $writer = new XLSXWriter();

            $company_name = array($selected_company_details->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $selected_company_details->address;
            $company_addr = array($address, );
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_addr);

            $msg = "Survey List  - From - " . $this->input->post('from_date') . " To " . $this->input->post('to_date');

            $filter = array($msg);
            $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $filter);

            if ($staff_id !== '') {
                $msg1 = "Filter  - Staff ID - " . $staff_id;
            } else {
                $msg1 = "Filter  - Staff ID - All";
            }

            $filter1 = array($msg1);
            $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $filter1);

            // empty row
            $list_add = [];
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Self"] = "Self";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Spouse"] = "Spouse";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Son"] = "Son";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Doughter"] = "Doughter";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Water Resources"] = "Water Resources";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Equipment Tractors"] = "Equipment Tractors";
            $list_add[] = "";
            $list_add["Equipment Trolly"] = "Equipment Trolly";
            $list_add[] = "";
            $list_add["Equipment Cultivator"] = "Equipment Cultivator";
            $list_add[] = "";
            $list_add["Equipment Sowing Mashine"] = "Equipment Sowing Mashine";
            $list_add[] = "";
            $list_add["Equipment Rotavator"] = "Equipment Rotavator";
            $list_add[] = "";
            $list_add["Equipment Harvester"] = "Equipment Harvester";
            $list_add[] = "";
            $list_add[" Water Pump"] = " Water Pump";
            $list_add[] = "";
            $list_add["Type Buffelo LiveStock"] = " Type Buffelo LiveStock";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Type Cow LiveStock"] = " Type Cow LiveStock";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Cattle Feed Cost"] = " Cattle Feed Cost";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            foreach($years as $val){
                $list_add["Crop Pattern Kharif (20".$val.")"] = " Crop Pattern Kharif (20".$val.")";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add["Crop Pattern Rabi (20".$val.")"] = " Crop Pattern Rabi (20".$val.")";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
            }
            
            $list_add["Production Sowing Cost"] = " Production Sowing Cost";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Production Maintenance Cost"] = " Production Maintenance Cost";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Production Harvesting Cost"] = "Production Harvesting Cost";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Production Produce Selling Cost"] = "Production Produce Selling Cost";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Labour Availability"] = "Labour Availability";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Government Schemes"] = "Government Schemes";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add["Smartphone Usage"] = "Smartphone Usage";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";

            $writer->writeSheetRow('Sheet1', $list_add);
            
        // Set Header
            $col1 = 1;
            $col2 = 7;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            // Master Data
            $set_col_tk = [];
            $set_col_tk["Full Name"] = 'Full Name';
            $set_col_tk["Date"] = 'Date';
            $set_col_tk["Mobile"] = 'Mobile';
            $set_col_tk["State"] = 'State';
            $set_col_tk["City"] = 'City';
            $set_col_tk["Taluka"] = 'Taluka';
            $set_col_tk["Village"] = 'Village';
            $set_col_tk["Generated By"] = 'Generated By';
            $col1 = 8;
            $col2 = 12;
            // DEPENDENTS COLUMNS
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count"] = 'Total Count';
            $set_col_tk["Survey/GUT No."] = 'Survey/GUT No.';
            $set_col_tk["Irrigated Land"] = 'Irrigated Land';
            $set_col_tk["Un-Irrigated Land"] = 'Un-Irrigated Land';
            $set_col_tk["Total Land Holding"] = 'TOTAL LAND HOLDING';
            $col1 = 13;
            $col2 = 17;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count Spouse"] = 'Total Count Spouse';
            $set_col_tk["Survey/GUT No. Spouse"] = 'Survey/GUT No. Spouse';
            $set_col_tk["Irrigated Land Spouse"] = 'Irrigated Land Spouse';
            $set_col_tk["Un-Irrigated Land Spouse"] = 'Un-Irrigated Land Spouse';
            $set_col_tk["Total Land Holding Spouse"] = 'TOTAL LAND HOLDING Spouse';
            
            $col1 = 18;
            $col2 = 22;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count Son"] = 'Total Count Son';
            $set_col_tk["Survey/GUT No. Son"] = 'Survey/GUT No. Son';
            $set_col_tk["Irrigated Land Son"] = 'Irrigated Land Son';
            $set_col_tk["Un-Irrigated Land Son"] = 'Un-Irrigated Land Son';
            $set_col_tk["Total Land Holding Son"] = 'TOTAL LAND HOLDING Son';

            $col1 = 23;
            $col2 = 27;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count Doughter"] = 'Total Count Doughter';
            $set_col_tk["Survey/GUT No. Doughter"] = 'Survey/GUT No. Doughter';
            $set_col_tk["Irrigated Land Doughter"] = 'Irrigated Land Doughter';
            $set_col_tk["Un-Irrigated Land Doughter"] = 'Un-Irrigated Land Doughter';
            $set_col_tk["Total Land Holding Doughter"] = 'TOTAL LAND HOLDING Doughter';

            // // WATER RESOURCES
            $col1 = 28;
            $col2 = 34;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Well (FT)"] = 'Well (FT)';
            $set_col_tk["Borewell (FT)"] = 'Borewell (FT)';
            $set_col_tk["Canel (Water Available During Cultivation - Days In Week)"] = 'Canel (Water Available During Cultivation - Days In Week)';
            $set_col_tk["River/Nala(Months) "] = 'River/Nala (Months)';
            $set_col_tk["Farm Pond (Area & Storage Capacity)"] = 'Farm Pond (Area & Storage Capacity)';
            $set_col_tk["Have Fisheries Business"] = 'Have Fisheries Business';
            $set_col_tk["Fisheries Revenue(If Any)"] = 'Fisheries Revenue(If Any)';

            // EQUIPMENT COLUMNS
            $col1 = 35;
            $col2 = 36;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count "] = 'Total Count EQUIPMENT ';
            $set_col_tk["Company"] = 'Company';

            $col1 = 37;
            $col2 = 38;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count  "] = 'Total Count EQUIPMENT  ';
            $set_col_tk["Company "] = 'Company ';
            
            $col1 = 39;
            $col2 = 40;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count   "] = 'Total Count EQUIPMENT   ';
            $set_col_tk["Company  "] = 'Company  ';
            
            $col1 = 41;
            $col2 = 42;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count    "] = 'Total Count EQUIPMENT    ';
            $set_col_tk["Company   "] = 'Company   ';
            
            $col1 = 43;
            $col2 = 44;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count      "] = 'Total Count EQUIPMENT     ';
            $set_col_tk["Company    "] = 'Company   ';
            
            $col1 = 45;
            $col2 = 46;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count       "] = 'Total Count EQUIPMENT      ';
            $set_col_tk["Company     "] = 'Company    ';
            
            $col1 = 47;
            $col2 = 48;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Total Count         "] = 'Total Count EQUIPMENT      ';
            $set_col_tk["Company        "] = 'Company   ';



            // LIVESTOCK COLUMNS
            $col1 = 49;
            $col2 = 51;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk[" Total Count"] = ' Total Count';
            $set_col_tk["Milk/Day"] = 'Milk/Day';
            $set_col_tk["Breed"] = 'Breed';
    
            $col1 = 52;
            $col2 = 54;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk[" Total Count "] = ' Total Count ';
            $set_col_tk[" Milk/Day "] = ' Milk/Day ';
            $set_col_tk[" Breed "] = ' Breed ';



            // CATTLE FEED COST COLUMNS
            $col1 = 55;
            $col2 = 60;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
            $set_col_tk["Per Day Requirement (KG)"] = 'Per Day Requirement (KG)';
            $set_col_tk["General Purchase"] = 'General Purchase (KG)';
            $set_col_tk["Avg.Cost Per (KG)"] = 'Avg.Cost Per (KG)';
            $set_col_tk["Preferred Feed Manufacturing Company"] = 'Preferred Feed Manufacturing Company';
            $set_col_tk["Sales in Nearby Town"] = 'Sales in Nearby Town';
            $set_col_tk["In Sold the Milk Collection Center(Dairy)"] = 'In Sold the Milk Collection Center (Dairy)';
            
            foreach($years as $val1){
                $col1 = $col2 + 1;
                $col2 = $col2 + 11;
                // // CROP PATTERN COLUMNS
                $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
                $set_col_tk["Type(Oil Seed/Pulses)".$val1] = 'Type(Oil Seed/Pulses)';
                $set_col_tk["Sown Seed".$val1] = 'Sown Seed';
                $set_col_tk["Variety".$val1] = 'Variety';
                $set_col_tk["Seed Comapany".$val1] = 'Seed Comapany';
                $set_col_tk["Fertilizers Used".$val1] = 'Fertilizers Used';
                $set_col_tk["Fertilizers Company".$val1] = 'Fertilizers Company';
                $set_col_tk["Proportion".$val1] = 'Proportion';
                $set_col_tk["Pesticides Used".$val1] = 'Pesticides Used';
                $set_col_tk["Pesticides Company".$val1] = 'Pesticides Company';
                $set_col_tk["Total Yield in Quintal".$val1] = 'Total Yield in Quintal';
                $set_col_tk["Avg Rate Got Produce".$val1] = 'Avg Rate Got Produce';
                $col1 = $col1 + 11;
                $col2 = $col2 + 11;
                $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cells
                $set_col_tk["Type(Oil Seed/Pulses) ".$val1] = 'Type(Oil Seed/Pulses) ';
                $set_col_tk["Sown Seed ".$val1] = 'Sown Seed ';
                $set_col_tk["Variety ".$val1] = 'Variety ';
                $set_col_tk["Seed Comapany ".$val1] = 'Seed Comapany ';
                $set_col_tk["Fertilizers Used ".$val1] = 'Fertilizers Used ';
                $set_col_tk["Fertilizers Company ".$val1] = 'Fertilizers Company ';
                $set_col_tk["Proportion ".$val1] = 'Proportion ';
                $set_col_tk["Pesticides Used ".$val1] = 'Pesticides Used ';
                $set_col_tk["Pesticides Company ".$val1] = 'Pesticides Company ';
                $set_col_tk["Total Yield in Quintal ".$val1] = 'Total Yield in Quintal ';
                $set_col_tk["Avg Rate Got Produce ".$val1] = 'Avg Rate Got Produce ';
                
            }
            $col1 = $col2 + 1;
            $col2 = $col2 + 3;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Manual Sowing Cost"] = 'Manual Sowing Cost';
            $set_col_tk["Machine Sowing Cost"] = 'Machine Sowing Cost';
            $set_col_tk["Total Sowing Cost"] = 'Total Sowing Cost';
            
            $col1 = $col2 + 1;
            $col2 = $col2 + 9;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Fertilizer Cost"] = 'Fertilizer Cost';
            $set_col_tk["Fertilizer Labour Cost"] = 'Fertilizer Labour Cost';
            $set_col_tk["Spraying Cost"] = 'Spraying Cost';
            $set_col_tk["Spraying Labour Cost"] = 'Spraying Labour Labour Cost';
            $set_col_tk["Weedicide Control Cost"] = 'Weedicide Control Cost';
            $set_col_tk["Weedicide Control Labour Cost"] = 'Weedicide Control Labour Cost';
            $set_col_tk["Pesticides Spraying Cost"] = 'Pesticides Spraying Cost';
            $set_col_tk["Pesticides Spraying Labour Cost"] = 'Pesticides Spraying Labour Cost';
            $set_col_tk["Total Maintenence Cost"] = 'Total Maintenence Cost';
            
            $col1 = $col2 + 1;
            $col2 = $col2 + 4;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Manual Harvesting Cost"] = 'Manual Harvesting Cost';
            $set_col_tk["Machine Harvesting Cost"] = 'Machine Harvesting Cost';
            $set_col_tk["Hired Harvesting Cost"] = 'Hired Harvesting Cost';
            $set_col_tk["Total Harvesting Cost"] = 'Total Harvesting Cost';

            $col1 = $col2 + 1;
            $col2 = $col2 + 10;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Distance From Village"] = 'Distance From Village';
            $set_col_tk["Transportation Available"] = 'Transportation Available';
            $set_col_tk["Transportation Type"] = 'Transportation Type';
            $set_col_tk["Transportation Cost Per Quintal"] = 'Transportation Cost Per Quintal';
            $set_col_tk["Challenges in APMC"] = 'Challenges in APMC';
            $set_col_tk["Hamali Per Quintal"] = 'Hamali Per Quintal';
            $set_col_tk["Kata"] = 'Kata';
            $set_col_tk["Avg Waiting Time for Auction"] = 'Avg Waiting Time for Auction';
            $set_col_tk["Avg Cutting Per Quintal"] = 'Avg Cutting Per Quintal';
            $set_col_tk["Total Selling Cost"] = 'Total Selling Cost';


            // // LABOUR AVAILABILITY COLUMNS
            $col1 = $col2 + 1;
            $col2 = $col2 + 4;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Available in Same Village"] = 'Available in Same Village';
            $set_col_tk["To be Made Available Form Nearby Village"] = 'To be Made Available Form Nearby Village';
            $set_col_tk["Male Labour Daily Wages"] = 'Male Labour Daily Wages';
            $set_col_tk["Female Labour Daily Wages"] = 'Female Labour Daily Wages';


            // // GOVERNMENT SCHEMES COLUMNS
            $col1 = $col2 + 1;
            $col2 = $col2 + 5;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Solar Pump Capacity"] = 'Solar Pump Capacity';
            $set_col_tk["Crop Insurance Company"] = 'Crop Insurance Company';
            $set_col_tk["Compensations Recieved"] = 'Compensations Recieved';
            $set_col_tk["PM Kisan Samman Nidhi Recieve"] = 'PM Kisan Samman Nidhi Recieve';
            $set_col_tk["Equipment Supply"] = 'AGRI Equipment Supply From Panchayat Committee';


            // // SMARTPHONE USAGE COLUMNS
            $col1 = $col2 + 1;
            $col2 = $col2 + 8;
            $writer->markMergedCell('Sheet1', $start_row = 4, $start_col = $col1, $end_row = 4, $end_col = $col2);  //merge cell
            $set_col_tk["Smartphone Holders in Family"] = 'Smartphone Holders in Family';
            $set_col_tk["Whatsapp Users"] = 'Whatsapp Users';
            $set_col_tk["Whether Youtube Videos Referred for Better Cultivation"] = 'Whether Youtube Videos Referred for Better Cultivation';
            $set_col_tk["Whatsapp Users Subscribed For AGRI Services"] = 'Whatsapp Users Subscribed For AGRI Services';
            $set_col_tk["Service Paid For Free"] = 'Service Paid For Free';
            $set_col_tk["Subscription Charges"] = 'Subscription Charges';
            $set_col_tk["Charges Payment Frequency"] = 'Charges Payment Frequency';
            $set_col_tk["Whether Forecast"] = 'Explores Whether Forecast / Market Forecast / Govt. Scheme';


            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            
            foreach ($body_data as $key => $value) {
                $list_add = [];
                $list_add[] = $value["name"];
                $list_add[] = _d(substr($value["TransDate"], 0, 10));
                $list_add[] = $value["mobile_number"];
                $list_add[] = $value["state_name"];
                $list_add[] = $value["city_name"];
                $list_add[] = $value["TalukaName"];
                $list_add[] = $value["village"];
                $list_add[] = $value["firstname"] . " " . $value["lastname"];
                // Dependants
                foreach($Get_dependants as $dKey=>$dVal){
                    if($value['id']==$dVal['id']){
                        $list_add[] = $dVal["number"];
                        $list_add[] = $dVal["gut_number"];
                        $list_add[] = $dVal["Irrigated_land"];
                        $list_add[] = $dVal["UnIrrigated_land"];
                        $list_add[] = $dVal["total_land"];
                    }
                }
                //Water Resources
                $list_add[] = $value["well"];
                $list_add[] = $value["borewell"];
                $list_add[] = $value["canal"];
                $list_add[] = $value["river_nala"];
                $list_add[] = $value["farm_pond"];
                $list_add[] = $value["fisheries"];
                $list_add[] = $value["fisheries_revenue"];
                
                // Equipment
                foreach($Get_equipment as $eKey=>$eVal){
                    if($value['id']==$eVal['id']){
                        $list_add[] = $eVal["number"];
                        $list_add[] = $eVal["company"];
                    }
                }
                // Livestock
                foreach($Get_livestock as $lKey=>$lVal){
                    if($value['id']==$lVal['id']){
                        $list_add[] = $lVal["number"];
                        $list_add[] = $lVal["milk_per_day"];
                        $list_add[] = $lVal["breed"];
                    }
                }
                
                $list_add[] = $value["Feed_per_day"];
                $list_add[] = $value["Feed_purchase"];
                $list_add[] = $value["FeedAvgCostPerKG"];
                $list_add[] = $value["FeedCompany"];
                $list_add[] = $value["milk_can"];
                $list_add[] = $value["milk_col_company"];
                
                foreach($years as $val1){
                    $year = '20'.$val1;
                    foreach($Get_crop_pattern as $ckey=>$cval){
                        if($value['id']==$cval['id']){
                            if($year == $cval['Year']){
                                $list_add[] = $cval["kharif"];
                            }
                        }
                    }
                    foreach($Get_crop_pattern as $ckey=>$cval){
                        if($value['id']==$cval['id']){
                            if($year == $cval['Year']){
                                $list_add[] = $cval["rabi"];
                            }
                        }
                    }
                }
                
                foreach($Get_production_cost as $pkey=>$pval){
                    if($value['id']==$pval['id']){
                        $list_add[] = $pval["value"];
                    }
                }
                $list_add[] = $value["labour_in_village"];
                $list_add[] = $value["labour_in_nearby_village"];
                $list_add[] = $value["male_labour_cost"];
                $list_add[] = $value["female_labour_cost"];
                $list_add[] = $value["solar_pump"];
                // solar capacity missing
                $list_add[] = $value["crop_insurance"];
                // Insurance company name missing
                $list_add[] = $value["compensations_received"];
                $list_add[] = $value["PMKSN"];
                $list_add[] = $value["AgriEquipmentByPanchayat"];
                $list_add[] = $value["smart_phone_user"];
                $list_add[] = $value["WhatsAppUser"];
                $list_add[] = $value["youtube_referred"];
                $list_add[] = $value["WhatsAppAgriService"];
                $list_add[] = $value["ServiceIsPaid"];
                $list_add[] = $value["ServicePaidAmt"];
                $list_add[] = $value["PaymentFrquancy"];
                $list_add[] = $value["mob_used_for_forcasting"];
                
                
                $writer->writeSheetRow('Sheet1', $list_add);
                
            }
            
           
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            $filename = 'SurveyList.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
            echo json_encode([
                'site_url' => site_url(),
                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
            ]);
            die;
        }
    }
    
    /*New Function in Trader $ Broker Reprts */

      public function traderbrokerreport()
      {
        
        if (!has_permission_new('traderbroker_report', '', 'view')) {
            access_denied('traderbroker_report');
        }
        $data['trader'] = $this->misc_reports_model->gettraderlist();
        $data['broker'] = $this->misc_reports_model->getbrokerlist();
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['title'] = "Trader Broker Report";
        $this->load->view('admin/misc_reports/traderbroker_report', $data);
      }
      
      public function generateReporttraderbroker()
    {
      if (!has_permission_new('rate_report', '', 'view')) {
          access_denied('access_denied');
      }
      
        $post_data = $this->input->post();
        $data = $this->misc_reports_model->get_reporttraderbroker_data($post_data); 
    
        if ($post_data['reportType'] == 'broker') {
            $html .= '<div class="col-md-12">';
            $html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Sr.No</th>';
            $html .= '<th>Broker ID</th>';
            $html .= '<th>Broker Name</th>';
            $html .= '<th>Trader ID</th>';
            $html .= '<th>Trader Name</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
        } elseif ($post_data['reportType'] == 'trader') {
            $html .= '<div class="col-md-12">';
            $html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Sr.No</th>';
            $html .= '<th>Trader ID</th>';
            $html .= '<th>Trader Name</th>';
            $html .= '<th>Broker ID</th>';
            $html .= '<th>Broker Name</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
        }
        $i = 1;
        foreach ($data as $value) {
            $html .= '<tr>';
            $html .= '<td>' . $i . '</td>';
            $TraderName = "";
            $TraderID = "";
            $BrokerID = "";
            $BrokerName = "";
            if($value["SendFromAccountsType"] == "2"){
                $BrokerID = $value["send_from"];
                $BrokerName = $value["SendFromAccountName"];
                $TraderID = $value["send_to"];
                $TraderName = $value["SendToAccountName"];
            }else{
                $BrokerID = $value["send_to"];
                $BrokerName = $value["SendToAccountName"];
                $TraderID = $value["send_from"];
                $TraderName = $value["SendFromAccountName"];
            }
            if($post_data['reportType'] == 'broker'){
                
                $html .= '<td>' . $BrokerID . '</td>';
                $html .= '<td>' . $BrokerName . '</td>';
                $html .= '<td>' . $TraderID . '</td>';
                $html .= '<td>' . $TraderName . '</td>';
            }else if($post_data['reportType'] == 'trader'){
                $html .= '<td>' . $TraderID . '</td>';
                $html .= '<td>' . $TraderName . '</td>';
                $html .= '<td>' . $BrokerID . '</td>';
                $html .= '<td>' . $BrokerName . '</td>';
            }
            $i++;
        }
        echo json_encode($html);
    }

    public function export_traderbrokerreport(){
        
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post())
        {
                $post_data = $this->input->post();
                $result = $this->misc_reports_model->get_reporttraderbroker_data($post_data); 
                $selected_company_details = $this->misc_reports_model->get_company_detail();
                
                $writer = new XLSXWriter();

                $company_name = array($selected_company_details->company_name);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 4);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_name);
                $j++;
                $address = $selected_company_details->address;
                $company_addr = array($address,);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 4);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_addr);
                
                $set_col_tk = [];
                if($post_data['reportType'] == 'broker'){
                    $set_col_tk["BrokerID"] = 'BrokerID';
                    $set_col_tk["BrokerName"] = 'BrokerName';
                    $set_col_tk["TraderId"] =  'TraderId';
                    $set_col_tk["TraderName"] = 'TraderName';
                }else if($post_data['reportType'] == 'trader'){
                    $set_col_tk["TraderId"] =  'TraderId';
                    $set_col_tk["TraderName"] = 'TraderName';
                    $set_col_tk["BrokerID"] = 'BrokerID';
                    $set_col_tk["BrokerName"] = 'BrokerName';
                }
                
                $writer_header = $set_col_tk;
                $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                $TraderName = "";
                $TraderID = "";
                $BrokerID = "";
                $BrokerName = "";
                if($value["SendFromAccountsType"] == "2"){
                    $BrokerID = $value["send_from"];
                    $BrokerName = $value["SendFromAccountName"];
                    $TraderID = $value["send_to"];
                    $TraderName = $value["SendToAccountName"];
                }else{
                    $BrokerID = $value["send_to"];
                    $BrokerName = $value["SendToAccountName"];
                    $TraderID = $value["send_from"];
                    $TraderName = $value["SendFromAccountName"];
                }
                if($post_data['reportType'] == 'broker'){
                    $list_add = [];
                    $list_add[] = $BrokerID;
                    $list_add[] = $BrokerName;
                    $list_add[] = $TraderID;
                    $list_add[] = $TraderName;
                }else if($post_data['reportType'] == 'trader'){
                    $list_add = [];
                    $list_add[] = $TraderID;
                    $list_add[] = $TraderName;
                    $list_add[] = $BrokerID;
                    $list_add[] =$BrokerName;
                }
                $writer->writeSheetRow('Sheet1', $list_add);
        
            }
                $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
                foreach($files as $file){
                    if(is_file($file)) {
                        unlink($file); 
                    }
                }
                $filename = 'TraderBrokerreport.xlsx';
                $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
                echo json_encode([
                    'site_url'          => site_url(),
                    'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
                ]);
                die;
            }
    }
    
    public function balancesheet(){

        if (!has_permission_new('rate_report', '', 'view')) {
            access_denied('access_denied');
        }
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['title'] = "Balance Sheet"; 
        // $data['BalanceSheetHead'] = $this->misc_reports_model->BalanceSheetHead();
        $finalArray = [];
        $BalanceSheet_head = array("10000","10035");
        $result = $this->misc_reports_model->fetchAccountsData($BalanceSheet_head);
        $nestedData = [];
        foreach ($result as $mainGroup) {
            $subGroups = $this->misc_reports_model->fetchAccountsDataSubGroup($mainGroup['ActGroupID']);
            $mainGroupData = [
                'MainGroup' => $mainGroup['ActGroupName'],
                'SubGroups' => [],
            ];
            foreach ($subGroups as $subGroup) {
                $subGroups1 = $this->misc_reports_model->fetchAccountsDataSubGroup1($subGroup['SubActGroupID1']);
                $subGroupData = [
                    'SubGroup' => $subGroup['SubActGroupName'],
                    'SubGroup1' => $subGroups1,
                ];
                $mainGroupData['SubGroups'][] = $subGroupData;
            }
            $nestedData[] = $mainGroupData;
        }
        $ledger_data = $this->misc_reports_model->GetLedgerData($BalanceSheet_head);
        $opn_data = $this->misc_reports_model->GetOpnBalData($BalanceSheet_head);
        $data['nestedData'] = $nestedData; 
        $data['ledger_data'] = $ledger_data; 
        $data['OpnBal'] = $opn_data; 
        // echo "<pre>";
        // print_r($data['ledger_data']);
        // die;
        //   return print json_encode($nestedData);
        $this->load->view('admin/misc_reports/balancesheet', $data);
        
    }
    
    public function export_balsheetreport(){
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post())
        {
                $BalanceSheet_head = array("10000","10035");
                $result = $this->misc_reports_model->fetchAccountsData($BalanceSheet_head);
                $selected_company_details = $this->misc_reports_model->get_company_detail();
                
                $writer = new XLSXWriter();

                $company_name = array($selected_company_details->company_name);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 3);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_name);
                $j++;
                $address = $selected_company_details->address;
                $company_addr = array($address,);
                $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 3);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_addr);
                 

                $fy = $this->session->userdata('finacial_year');
                $lastFy = $fy - 1;
                $fy_ = $fy + 1;
                $lastFy_ = $lastFy + 1;
                $CurrYrLastDate = '31/03/20' . $fy_;
                $LastYrLastDate = '31/03/20' . $lastFy_;
                
                $set_col_tk = [];
                $set_col_tk["Particulars"] =  'Particulars';
                $set_col_tk = $CurrYrLastDate;
                $set_col_tk= $LastYrLastDate;

                $nestedData = [];
                foreach ($result as $mainGroup) {
                    $subGroups = $this->misc_reports_model->fetchAccountsDataSubGroup($mainGroup['ActGroupID']);
                    $mainGroupData = [
                        'MainGroup' => $mainGroup['ActGroupName'],
                        'SubGroups' => [],
                    ];
                    foreach ($subGroups as $subGroup) {
                        $subGroups1 = $this->misc_reports_model->fetchAccountsDataSubGroup1($subGroup['SubActGroupID1']);
                        $subGroupData = [
                            'SubGroup' => $subGroup['SubActGroupName'],
                            'SubGroup1' => $subGroups1,
                        ];
                        $mainGroupData['SubGroups'][] = $subGroupData;
                    }
                    $nestedData[] = $mainGroupData;
                
                }
                $ledger_data = $this->misc_reports_model->GetLedgerData($BalanceSheet_head);
                $opn_data = $this->misc_reports_model->GetOpnBalData($BalanceSheet_head);
                $data['nestedData'] = $nestedData; 
                $data['ledger_data'] = $ledger_data; 
                $data['OpnBal'] = $opn_data; 
                
                $list_add = [];
    			$list_add[] = $value["MainGroup"];
                $list_add[] = $value["SubGroup"];
                $list_add[] = $value["SubActGroupName"];

                $writer_header = $set_col_tk;
                $writer->writeSheetRow('Sheet1', $writer_header);
                    $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
                        foreach($files as $file){
                            if(is_file($file)) {
                                unlink($file); 
                            }
                        }
                        $filename = 'Balancesheet.xlsx';
                        $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
                        echo json_encode([
                            'site_url'          => site_url(),
                            'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
                        ]);
                        die;
        }
    }
//================ Customer Enquiry ============================================

    public function CustomersEnquiry()
    {
		if (!has_permission_new('customer_enquiry', '', 'view')) {
			access_denied('customers');
		}
		$data['title']                = "Customers Enquiry List";
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$this->load->view('admin/misc_reports/customersenquiry_list', $data);
	}
		
		
	public function CustomersEnquiryList()
	{
		$result = $this->misc_reports_model->getcustomerenquirylist();
		$html ='';
		$i = 1;
		foreach ($result as $key => $value) 
		{
			$html .= '<tr class="get_AccountID" data-id = "'.$value['AccountID'].'">';
			$html .= '<td>'.$i.'</td>';
			$html .= '<td>'.$value["AccountID"].'</td>';
			$html .= '<td>'.$value["full_name"].'</td>';
			$html .= '<td>'.$value["CustomerType"].'</td>';
			$html .= '<td>'.$value["mobile_no"].'</td>';
			$html .= '<td>'.$value["email_id"].'</td>';
			$html .= '<td>'.$value["message"].'</td>';
			$html .= '</tr>';
			
			$i++;
		}
		echo json_encode($html);
	}
		
		
    public function export_enquirylist()
	{
	  
    if(!class_exists('XLSXReader_fin')){
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    }
    require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    
    if($this->input->post()){
        
        $company_detail = $this->sale_reports_model->get_company_detail();
       $result = $this->misc_reports_model->getcustomerenquirylist();
        $writer = new XLSXWriter();
        
        $company_name = array($company_detail->company_name);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 7);  //merge cells
		$writer->writeSheetRow('Sheet1', $company_name);

        $address = $company_detail->address;
		$center_addr = array($address,);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col =7 );  //merge cells
		$writer->writeSheetRow('Sheet1', $center_addr);
        
        
        $set_col_tk = [];
       // $set_col_tk["Sr.No"] =  'Sr.No';
        $set_col_tk["AccountID"] =  'AccountID';
        $set_col_tk["Full Name"] =  'Full Name';
        $set_col_tk["Customer Type"] =  'Customer Type';
        $set_col_tk["Mobile No"] =  'Mobile No';
        $set_col_tk["Email ID"] = ' 	Email ID';
        $set_col_tk["Message"] = 'Message';
        $writer_header = $set_col_tk;
        $writer->writeSheetRow('Sheet1', $writer_header);
		$i = 1;
        foreach ($result as $k => $value) {
            
            $list_add = [];
			//$list_add[] = '.$i.';
            $list_add[] = $value["AccountID"];
            $list_add[] = $value["full_name"];
            $list_add[] = $value["CustomerType"];
            $list_add[] = $value["mobile_no"];
            $list_add[] = $value["email_id"];
            $list_add[] = $value["message"];
            $list_add[] = $row_a;
            
			
			//$i++;
            $writer->writeSheetRow('Sheet1', $list_add);
        
        }

        $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file); 
            }
        }
        $filename = 'CustomerEnquirylist.xlsx';
        $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
        echo json_encode([
            'site_url'          => site_url(),
            'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
        ]);
        die;
    }
    }
    
    public function cashFlowReports()
    {
        if (!has_permission_new('rate_report', '', 'view')) {
            access_denied('access_denied');
        }
        $this->load->model('misc_reports_model');
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['ledger_detail'] = $this->misc_reports_model->get_ledger_detail();
        $data['ledger_details'] = $this->misc_reports_model->get_ledger_details();
        $data['taxc_details'] = $this->misc_reports_model->get_taxc_details();
        $data['taxd_details'] = $this->misc_reports_model->get_taxd_details();
        // return print json_encode($data);
        $data['title'] = "Cash Flow reports";
        $this->load->view('admin/misc_reports/cashFlow_reports', $data);

    }

    
//========================= Expense Page =======================================

    public function expense()
    {
        if (!has_permission_new('expense_list', '', 'view')) {
            access_denied('expense');
        }
        $data['title']                = "Expense List";
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        $data['Category'] = $this->misc_reports_model->GetCategorylist();
        $data['staff'] = $this->misc_reports_model->Getstafflist();
        $this->load->view('admin/misc_reports/expense_list', $data);
    }


    public function export_expenselist()
    {
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        if ($this->input->post()) {

            $company_detail = $this->sale_reports_model->get_company_detail();
            $result = $this->misc_reports_model->getexpenselist();
            $writer = new XLSXWriter();

            $company_name = array($company_detail->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
            $center_addr = array($address,);
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $center_addr);


            $set_col_tk = [];
            // $set_col_tk["Sr.No"] =  'Sr.No';
            $set_col_tk["CategoryName"] =  'CategoryName';
            $set_col_tk["Staff Name"] =  'Staff Name';
            $set_col_tk["Expense Date"] =  'Expense Date';
            $set_col_tk["Travel Distance"] =  'Travel Distance';
            $set_col_tk["Amount"] =  'Amount';
            $set_col_tk["Address"] = 'Address';
            $set_col_tk["Remark"] = 'Remark';
            $set_col_tk["Item Image"] = 'Item Image';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            $i = 1;
            foreach ($result as $k => $value) {

                $list_add = [];
                //$list_add[] = '.$i.';
                $list_add[] = $value["CategoryName"];
                $list_add[] = $value["expense_date"];
                $list_add[] = $value["travel_distance"];
                $list_add[] = $value["Amount"];
                $list_add[] = $value["address"];
                $list_add[] = $value["remark"];
                $list_add[] = $value["file_upload"];
                $list_add[] = $row_a;


                //$i++;
                $writer->writeSheetRow('Sheet1', $list_add);
            }

            $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $filename = 'expense.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
            ]);
            die;
        }
    }

    public function expensedata()
    {
        if (!has_permission_new('expense_list', '', 'view')) {
            access_denied('expense');
        }
        $post_data = $this->input->post();
        $data = $this->misc_reports_model->getexpenselist($post_data);

        $html = '';
        $html .= '<div class="col-md-6">';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<id="myInput1" onkeyup="myFunction2()">';
        $html .= '</div>';
        $html .= '<div class="col-md-12">';
        $html .= '<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th >Sr.No </th>';
        $html .= '<th >Category Name </th>';
        $html .= '<th >Staff Name </th>';
        $html .= '<th >Expense Date</th>';
        $html .= '<th>Travel Distance</th>';
        $html .= '<th>Amount</th>';
        $html .= '<th>Date</th>';
        $html .= '<th>Remark</th>';
        $html .= '<th>Image</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        if (count($data) > 0) {
            $i = 1;

            foreach ($data as $value) {
                $html .= '<tr class="get_AccountID" data-id = "' . $value['AccountID'] . '">';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td>' . $value["CategoryName"] . '</td>';
                $html .= '<td>' . $value["firstname"] . ' ' . $value["lastname"] . '</td>';
                $html .= '<td>' . $value["expense_date"] = date('d/m/Y') . '</td>';
                $html .= '<td>' . $value["travel_distance"] . '</td>';
                $html .= '<td>' . $value["Amount"] . '</td>';
                $html .= '<td>' . $value["address"] . '</td>';
                $html .= '<td>' . $value["remark"] . '</td>';
                $html .= '<td><img style="height: 25px; width: auto;" src="' . base_url() . 'uploads/expenseImages/' . $value["file_upload"] . '" ></td>';
                // $html .= '<td >'.$d.'</td>'; 
                $html .= '</tr>';
                $i++;
            }

            $html .= '</tbody>';
        } else {
            // $html.= '<tr>';
            $html .= '</tbody>';

            $html .= '</table>';
            $html .= '<span style="color:red;">No record Found..</span>';
            // $html.= '</tr>';
        }

        echo json_encode($html);
    }
    
    
    public function survey_Chartreport()
    {
        if (!has_permission_new('survey_reportChart', '', 'view')) {
            access_denied('access_denied');
        }
        $data['title'] = "Survey Reports";
        $data['staff'] = $this->misc_reports_model->All_staff();
		$data['States'] = $this->misc_reports_model->get_all_states();
		

        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        // print_r($data);die();
        $this->load->view('admin/misc_reports/survey_chart_report', $data);
    }
	
	public function Survey_wise_chart()
	{
	    if (!has_permission_new('survey_reportChart', '', 'view')) {
		    access_denied('Invoice Items');
		}
		$filter_data = array(
		    "from_date"=>$this->input->post('from_date'),
		    "to_date"=>$this->input->post('to_date'),
			"State"=>$this->input->post('State'),
		    "District"=>$this->input->post('District'),
		     "Taluka"=>$this->input->post('Taluka'),
		     "ReportFor"=>$this->input->post('ReportFor'),
		     "Staff_Id"=>$this->input->post('Staff_Id'),
		     "GroupBy"=>$this->input->post('GroupBy'),
		     "ChartType"=>$this->input->post('ChartType')
		);
		
		$result = $this->misc_reports_model->Survey_wise_chart($filter_data);
		
		$data = [
			'ChartData' => $result['ChartData'],
		];
		echo json_encode($data);
	
	}
}