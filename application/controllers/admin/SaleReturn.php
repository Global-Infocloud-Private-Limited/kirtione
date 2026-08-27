<?php
	
defined('BASEPATH') or exit('No direct script access allowed');

class SaleReturn extends AdminController
{
	private $not_importable_fields = ['id'];
	public function __construct()
	{
		parent::__construct();
		$this->load->model('SaleReturnModel');        
		$this->load->model('PurchaseModel');
	}
		
	public function AddEditSaleReturnInvoice($SINumber = '')
	{
		if (!has_permission_new('SaleReturnInvoice', '', 'view')) {
			access_denied('SaleReturnInvoice');
		}   		
		if ($this->input->post()) {
			$pur_order_data = $this->input->post();				
			
			$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
			if ($SINumber == '') {
				if (!has_permission_new('SaleReturnInvoice', '', 'create')) {
					access_denied('SaleReturnInvoice');
				}
				$id = $this->SaleReturnModel->AddKirtiOneReturnSaleOrderNew($pur_order_data);

				if ($id) {
					set_alert('success', _l('added_successfully', _l('pur_order')));
					redirect(admin_url('SaleReturn/AddEditSaleReturnInvoice'));
				}
			}else{
				if (!has_permission_new('SaleReturnInvoice', '', 'edit')) {
					access_denied('SaleReturnInvoice');
				}				
				$id = $this->SaleReturnModel->UpdateKirtiOneReturnSaleInvoice($pur_order_data,$SINumber);
				if ($id) {
					set_alert('success', _l('updated_successfully', _l('pur_order')));
					redirect(admin_url('SaleReturn/AddEditSaleReturnInvoice'));
				}
			}
		}
		
		if ($SINumber == '') {
			$title = "Create Sale Return Invoice";
		}else{
			$SaleDetails = $this->SaleReturnModel->GetSaleReturnInvoiceDetails($SINumber);			
			$data['Sale_details'] = $SaleDetails;	
			$PurchaseItemList = $this->SaleReturnModel->GetSaleReturnInvoiceItemList($SINumber,$SaleDetails->SalesRtnTypeID);
			$data['pur_order_detail'] = json_encode($PurchaseItemList);
			$title = "Edit Sale Invoice";
			  //echo "<pre>";print_r($PurchaseItemList);die;
		}
		/*echo "<pre>";
		print_r($PurchaseItemList);
		print_r($data);
		die;*/
		$ActGroupID = 10010;
		$wh_effect = '(ActGroupID="'.$ActGroupID.'")'; 
		$DirectExp = $this->SaleReturnModel->get_all_data($tablename="tblclients",$wh_effect);
		$data['DirectExp'] = $DirectExp; 
		$data['SaleCenterList'] = $this->SaleReturnModel->GetSaleCenterList(); 
		
		// $trader_list = $this->SaleReturnModel->GetAccountList();
		// $trader_list = $this->SaleReturnModel->PendingInvoiceVendors();
		//$data['trader_list'] = $trader_list;
		$data['item_code'] = $this->SaleReturnModel->get_items_code();	
		$data['statelist'] = $this->SaleReturnModel->getstatelist();
		$data['company_detail'] = $this->SaleReturnModel->get_company_detail();
		
		$this->load->view('admin/SaleReturnMaster/AddEditSaleReturnInvoice',$data);
	}
	
	public function GetPIByCenterWiseVendor()
	{
		$CenterID = $this->input->post('CenterID');
		$data = $this->SaleReturnModel->PendingInvoiceCenterwiseClients($CenterID);
		echo json_encode($data);
	}
	public function GetPIByVendorAndCenter()
	{
		$VenId = $this->input->post('VenId');
		$CenterID = $this->input->post('CenterID');
		$data = $this->SaleReturnModel->get_order_PI_ven_center_details($VenId,$CenterID);
		echo json_encode($data);
	}
	public function GetSIretuenItemData(){
		// POST data
		$PINo = $this->input->post('PINo');
		$Details = $this->SaleReturnModel->GetReturnSaleOrderItemListForInv($PINo);
		$response["ItemDetails"] = $Details["ItemDetails"];
		$response["SaleType"] = $Details["SaleType"];
		// echo "<pre>";
		// print_r($InwardData['historytbl']);
		// die;
		echo json_encode($response);
	}
	public function GetSIBillqtyItemData(){
		// POST data
		$PINo = $this->input->post('PINo');
		// Get data
		$InwardData['historytbl'] = $this->SaleReturnModel->GetBillqty($PINo);
		
		echo json_encode($InwardData);
	}
	public function load_data_for_sale_return_invoice()
	{
		$data = array(
		'from_date' => $this->input->post('from_date'),
		'to_date'  => $this->input->post('to_date')           
		);
		$SaleReturnList = $this->SaleReturnModel->load_data_for_sale_return_invoice_kirtione($data);
		$html = "";
		$TotalPurchAmt = 0;
		$TotalDiscAmt = 0;       
		$TotalCgstAmt = 0;
		$TotalSgstAmt = 0;
		$TotalIgstAmt = 0;        
		$TotalInvAmt = 0;
		$url2 = "";
		// echo "<pre>";
		// print_r($SaleReturnList);
		// die;
		foreach($SaleReturnList as $key=>$val)
		{
			// if($val['OrderStatus'] == "C")
			// { $OrderStatus = "Cancelled";	}		
			// else if($val['OrderStatus'] == "F"){
				// $OrderStatus = "Completed";
				// }else if($val['OrderStatus'] == "P"){
				// $OrderStatus = "Pending";
			// }
			
			
			$url = admin_url()."SaleReturn/AddEditSaleReturnInvoice/".$val["SalesRtnID"];
			//$html .= '<tr onclick="window.open('."'".$url."'".')">';  
			$html .= '<tr onclick="window.location.href=\''.$url.'\'">';			
			$html .= '<td style="text-align:center;">'.$val["SalesRtnID"].'</td>';
			$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
			$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
									
			$html .= '<td style="text-align:right;">'.$val["SaleAmt"].'</td>';
			$html .= '<td style="text-align:right;">'.$val["DiscAmt"].'</td>';          
			$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
			$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
			$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';           
			$html .= '<td style="text-align:right;">'.$val["BillAmt"].'</td>';
			$html .= '</tr>';
			$TotalPurchAmt += $val["SaleAmt"];
			$TotalDiscAmt += $val["DiscAmt"];            
			$TotalCgstAmt += $val["cgstamt"];
			$TotalSgstAmt += $val["sgstamt"];
			$TotalIgstAmt += $val["igstamt"];            
			$TotalInvAmt += $val["BillAmt"];
		}
		$html .= '<tr>';
		$html .= '<td colspan="3" style="text-align:right;"><b>Total</b></td>';
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalPurchAmt, 2, '.', ',').'</b></td>';
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';       
		$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
		$html .= '</tr>';
		echo $html;
	}
	
	//========================== Kirti One Sales Return Report page ==========================
	public function Report(){
		if (!has_permission_new('SalesReturnReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['FY'] = $this->session->userdata('finacial_year');
		$data['centermaster'] = $this->SaleReturnModel->GetSalesReturnCenterList();
		$data['products'] = $this->SaleReturnModel->GetSalesReturnItemList();
		$data['clients'] = $this->SaleReturnModel->GetSalesReturnPartyList();
		$data['company_detail'] = $this->SaleReturnModel->get_company_detail();
		$this->load->view('admin/SaleReturnMaster/SalesReturnReport',$data);
	}
	
	//========================== Filter result for Kirti One Sales Return Report ==========================
	public function GetSRReportFilterData(){
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'AccountID'=>$this->input->post('AccountID'),
			'CenterID'=>$this->input->post('CenterID'),
			'ItemID'=>$this->input->post('ItemID'),
			'ReportType'=>$this->input->post('Report_type'),
		);
		$result = $this->SaleReturnModel->getSRReportFilter($data); 	
		// echo json_encode($result); die;
		$html = '';
		
		if($data['ReportType'] == '1'){ // Report type bill
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">Return No</th>';
			$html .= '<th style="text-align:left;">Invoice No</th>';
			$html .= '<th style="text-align:left;">PO Date</th>';  
			$html .= '<th style="text-align:left;">Vendor</th>';  
			$html .= '<th style="text-align:left;">Center</th>';  
			$html .= '<th style="text-align:left;">Sale Amt</th>';
			$html .= '<th style="text-align:left;">Disc Amt</th>';   
			$html .= '<th style="text-align:left;">CGST Amt</th>';    
			$html .= '<th style="text-align:left;">SGST Amt</th>';    
			$html .= '<th style="text-align:left;">IGST Amt</th>';    
			$html .= '<th style="text-align:left;">Net Amt</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="filter_data_table">';

			$SaleAmt = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $BillAmt = 0;
			foreach($result as $key=>$value){
				$html .= '<tr>';
				$html .= '<td style="text-align:center;">'.($key+1).'</td>';   
				$html .= '<td style="text-align:center;">'.$value['SalesRtnID'].'</td>';	
				$html .= '<td style="text-align:center;">'.$value['SaleID'].'</td>';	
				$html .= '<td style="text-align:center;">'._d(substr($value["Transdate"],0,10)).'</td>'; 
				$html .= '<td>'.$value['AccountName'].'</td>';
				$html .= '<td>'.$value['CenterName'].'</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['SaleAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['BillAmt'], 2, '.', '') . '</td>';
				$html .= '</tr>';
				$SaleAmt += $value['SaleAmt'];
				$DiscAmt += $value['DiscAmt'];
				$cgstamt += $value['cgstamt'];
				$sgstamt += $value['sgstamt'];
				$igstamt += $value['igstamt'];
				$BillAmt += $value['BillAmt'];
			}
			$html .= '</tbody>';
			$html .= '<tfoot>';
			$html .= '<tr>';
			$html .= '<td colspan="6" style="text-align:right;">Total</td>';
			$html .= '<td style="text-align:right;">' . number_format($SaleAmt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($DiscAmt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($cgstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($sgstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($igstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($BillAmt, 2, '.', '') . '</td>';
			$html .= '</tr>';
			$html .= '</tfoot>';
		}else{ // Report type item
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">Return No</th>';
			$html .= '<th style="text-align:left;">Invoice No</th>';
			$html .= '<th style="text-align:left;">PO Date</th>';  
			$html .= '<th style="text-align:left;">Vendor</th>';  
			$html .= '<th style="text-align:left;">Center</th>';  
			$html .= '<th style="text-align:left;">Item</th>';  
			$html .= '<th style="text-align:left;">HSN</th>';  
			$html .= '<th style="text-align:left;">Brand</th>';
			$html .= '<th style="text-align:left;">Qty</th>';
			$html .= '<th style="text-align:left;">Sale Amt</th>';
			$html .= '<th style="text-align:left;">Disc Amt</th>';   
			$html .= '<th style="text-align:left;">CGST Amt</th>';    
			$html .= '<th style="text-align:left;">SGST Amt</th>';    
			$html .= '<th style="text-align:left;">IGST Amt</th>';    
			$html .= '<th style="text-align:left;">Net Amt</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="filter_data_table">';

			$OrderQty = $SaleRate = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $NetOrderAmt = 0;
			foreach($result as $key=>$value){
				$html .= '<tr>';
				$html .= '<td style="text-align:center;">'.($key+1).'</td>';   
				$html .= '<td style="text-align:center;">'.$value['OrderID'].'</td>';	
				$html .= '<td style="text-align:center;">'.$value['BillID'].'</td>';	
				$html .= '<td style="text-align:center;">'._d(substr($value["TransDate"],0,10)).'</td>'; 
				$html .= '<td>'.$value['AccountName'].'</td>';
				$html .= '<td>'.$value['CenterName'].'</td>';
				$html .= '<td>'.$value['ProductName'].'</td>';
				$html .= '<td>'.$value['hsn_code'].'</td>';
				$html .= '<td>'.$value['BrandName'].'</td>';
				$html .= '<td>'.$value['OrderQty'].'</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['SaleRate'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['NetOrderAmt'], 2, '.', '') . '</td>';
				$html .= '</tr>';
				$OrderQty += $value['OrderQty'];
				$SaleRate += $value['SaleRate'];
				$DiscAmt += $value['DiscAmt'];
				$cgstamt += $value['cgstamt'];
				$sgstamt += $value['sgstamt'];
				$igstamt += $value['igstamt'];
				$NetOrderAmt += $value['NetOrderAmt'];
			}
			$html .= '</tbody>';
			$html .= '<tfoot>';
			$html .= '<tr>';
			$html .= '<td colspan="9" style="text-align:right;">Total</td>';
			$html .= '<td style="text-align:right;">' . number_format($OrderQty, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($SaleRate, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($DiscAmt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($cgstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($sgstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($igstamt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($NetOrderAmt, 2, '.', '') . '</td>';
			$html .= '</tr>';
			$html .= '</tfoot>';
		}

		echo $html;
	}
		
	//============= Export Kirti One Sales Return Report List ====================================
	public function export_GetSRReportFilterData(){
		if (!has_permission_new('SalesReturnReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		
		if ($this->input->post()) 
		{
			$company_detail = $this->SaleReturnModel->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'AccountID'=>$this->input->post('AccountID'),
				'CenterID'=>$this->input->post('CenterID'),
				'CenterName'=>$this->input->post('Centertext'),
				'ItemID'=>$this->input->post('ItemID'),
				'ItemName'=>$this->input->post('ItemName'),
				'ReportType'=>$this->input->post('Report_type'),
				'ReportTypeName'=>$this->input->post('ReportTypetext')
			);
			$result = $this->SaleReturnModel->getSRReportFilter($data); 	
			// echo json_encode($result); die;
			
			$writer = new XLSXWriter();
			
			$company_name = array($company_detail->company_name);
			
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  
			
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_detail->address;
			
			$center_addr = array($address, );	  
			
			$filters = "From date: " . $data['from_date'] . ", To date: " . $data['to_date'] . ", Center: " . $data['CenterName'] . ", Report Type: " . $data['ReportTypeName'] .
			", Item: " . $data['ItemName'] . ", Party: " . $data['AccountName'];
			
			$filter_row = array($filters);
			
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells
			
			$writer->writeSheetRow('Sheet1', $center_addr);
			
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 14);  //merge cells	   
			
			$writer->writeSheetRow('Sheet1', $filter_row);
			
			$set_col_tk = [];
			
			if ($data['ReportType'] == "1") {
				$set_col_tk["ReturnNo"] = 'Return No';
				$set_col_tk["InvoiceNo"] = 'Invoice No';
				$set_col_tk["Transdate"] = 'PO Date';
				$set_col_tk["Vendor"] = 'Vendor';
				$set_col_tk["CenterName"] = 'Center Name';
				$set_col_tk["PurchAmt"] = 'Purch Amt';
				$set_col_tk["DiscAmt"] = 'Disc Amt';
				$set_col_tk["CGSTAmt"] = 'CGST Amt';
				$set_col_tk["SGSTAmt"] = 'SGST Amt';
				$set_col_tk["IGSTAmt"] = 'IGST Amt';
				$set_col_tk['ItemNetTotal'] = 'Net Amt';
			}else {
				$set_col_tk["ReturnNo"] = 'Return No';
				$set_col_tk["InvoiceNo"] = 'Invoice No';
				$set_col_tk["Transdate"] = 'PO Date';
				$set_col_tk["Vendor"] = 'Vendor';
				$set_col_tk["CenterName"] = 'Center Name';
				$set_col_tk["Item"] = 'Item Name';
				$set_col_tk["HSN"] = 'HSN Code';
				$set_col_tk["Brand"] = 'Brand Name';
				$set_col_tk["Qty"] = 'Qty';
				$set_col_tk["PurchRate"] = 'Purch Rate';
				$set_col_tk["DiscAmt"] = 'Disc Amt';
				$set_col_tk["CGSTAmt"] = 'CGST Amt';
				$set_col_tk["SGSTAmt"] = 'SGST Amt';
				$set_col_tk["IGSTAmt"] = 'IGST Amt';
				$set_col_tk['ItemNetTotal'] = 'Net Amt';
			}  		 
			
			$writer_header = $set_col_tk;
			
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			if ($data['ReportType'] == "1") {
				$SaleAmt = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $BillAmt = 0;
				foreach($result as $key=>$value){
					$list_add = [];  
					$list_add[] = $value['SalesRtnID'];	
					$list_add[] = $value['SaleID'];	
					$list_add[] = _d(substr($value["Transdate"],0,10)); 
					$list_add[] = $value['AccountName'];
					$list_add[] = $value['CenterName'];
					$list_add[] = number_format($value['SaleAmt'], 2, '.', '');
					$list_add[] = number_format($value['DiscAmt'], 2, '.', '');
					$list_add[] = number_format($value['cgstamt'], 2, '.', '');
					$list_add[] = number_format($value['sgstamt'], 2, '.', '');
					$list_add[] = number_format($value['igstamt'], 2, '.', '');
					$list_add[] = number_format($value['BillAmt'], 2, '.', '');
					
					$writer->writeSheetRow('Sheet1', $list_add); 		

					$SaleAmt += $value['SaleAmt'];
					$DiscAmt += $value['DiscAmt'];
					$cgstamt += $value['cgstamt'];
					$sgstamt += $value['sgstamt'];
					$igstamt += $value['igstamt'];
					$BillAmt += $value['BillAmt'];
				}
			}else{
				$OrderQty = $SaleRate = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $NetOrderAmt = 0;
				foreach($result as $key=>$value){
					$list_add = [];  
					$list_add[] = $value['OrderID'];	
					$list_add[] = $value['BillID'];	
					$list_add[] = _d(substr($value["TransDate"],0,10)); 
					$list_add[] = $value['AccountName'];
					$list_add[] = $value['CenterName'];
					$list_add[] = $value['ProductName'];
					$list_add[] = $value['hsn_code'];
					$list_add[] = $value['BrandName'];
					$list_add[] = number_format($value['OrderQty'], 2, '.', '');
					$list_add[] = number_format($value['SaleRate'], 2, '.', '');
					$list_add[] = number_format($value['DiscAmt'], 2, '.', '');
					$list_add[] = number_format($value['cgstamt'], 2, '.', '');
					$list_add[] = number_format($value['sgstamt'], 2, '.', '');
					$list_add[] = number_format($value['igstamt'], 2, '.', '');
					$list_add[] = number_format($value['NetOrderAmt'], 2, '.', '');
					
					$writer->writeSheetRow('Sheet1', $list_add); 		

					$OrderQty += $value['OrderQty'];
					$SaleRate += $value['SaleRate'];
					$DiscAmt += $value['DiscAmt'];
					$cgstamt += $value['cgstamt'];
					$sgstamt += $value['sgstamt'];
					$igstamt += $value['igstamt'];
					$NetOrderAmt += $value['NetOrderAmt'];
				}
			}
				   
			
			if ($data['ReportType'] == "1") {
				$sum_row = [];
				$sum_row[] = ''; 
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = 'Total'; 
				$sum_row[] = number_format($SaleAmt, 2, '.', '');
				$sum_row[] = number_format($DiscAmt, 2, '.', '');
				$sum_row[] = number_format($cgstamt, 2, '.', '');
				$sum_row[] = number_format($sgstamt, 2, '.', '');
				$sum_row[] = number_format($igstamt, 2, '.', '');
				$sum_row[] = number_format($BillAmt, 2, '.', '');
				$writer->writeSheetRow('Sheet1', $sum_row);
			}else{
				$sum_row = [];
				$sum_row[] = ''; 
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = ''; 
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = 'Total'; 
				$sum_row[] = number_format($OrderQty, 2, '.', '');
				$sum_row[] = number_format($SaleRate, 2, '.', '');
				$sum_row[] = number_format($DiscAmt, 2, '.', '');
				$sum_row[] = number_format($cgstamt, 2, '.', '');
				$sum_row[] = number_format($sgstamt, 2, '.', '');
				$sum_row[] = number_format($igstamt, 2, '.', '');
				$sum_row[] = number_format($NetOrderAmt, 2, '.', '');
				$writer->writeSheetRow('Sheet1', $sum_row);
			}
			
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'SalesReturnReport.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
            'site_url' => site_url(),
            'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
}
