<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class KirtiOneOrder extends AdminController
	{
		private $not_importable_fields = ['id'];
		public function __construct()
		{
			parent::__construct();
			$this->load->model('KirtiOneOrderModel'); 
			$this->load->model('PurchaseModel'); 
			$this->load->model('ItemModel'); 
			$this->load->model('Challan_model'); 
		}
		public function index()
		{
			$this->AddEditSaleOrder();
		}
		public function AutoReceiptsGenerate()
		{
		  // die;
			$this->KirtiOneOrderModel->AutoReceiptsGenerate();
		} 
		public function DataCorrection()
		{
			$this->KirtiOneOrderModel->GetDataCorrection();
		} 
		public function AddEditSaleOrder($ORDNumber = '')
		{
			if (!has_permission_new('OrderMaster', '', 'view')) {
				access_denied('DirectSale');
			}
			if ($this->input->post()) {
				$sale_order_data = $this->input->post();
				$sale_order_data['terms'] = nl2br($sale_order_data['terms']);
				if ($ORDNumber == '') {
					if (!has_permission_new('OrderMaster', '', 'create')) {
						access_denied('KirtiOneInward');
					}
					$response = $this->KirtiOneOrderModel->AddKirtiOneSaleOrder($sale_order_data);
					if ($response['status'] == true) {
						set_alert('success', $response['message']);
						redirect(admin_url('KirtiOneOrder/AddEditSaleOrder/').$id);
					}else{
						set_alert('warning', $response['message']);
						redirect(admin_url('KirtiOneOrder/AddEditSaleOrder/'));
					}
				}else{
					if (!has_permission_new('OrderMaster', '', 'edit')) {
						access_denied('DirectSale');
					}	
					$response = $this->KirtiOneOrderModel->UpdateKirtiOneSaleOrder($sale_order_data,$ORDNumber);
					if ($response['status'] == true) {
						set_alert('success', $response['message']);
						redirect(admin_url('KirtiOneOrder/AddEditSaleOrder/').$ORDNumber);
					}else{
					    set_alert('warning', $response['message']);
					    redirect(admin_url('KirtiOneOrder/AddEditSaleOrder/').$ORDNumber);
					}
				}
			}
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$pincodeDetails = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblpin");
			$data['pincodeDetails'] = $pincodeDetails;
			$nextK1OrderNumber = get_option('next_K1Order_number_for_kirti'); 
			$data['NextOrderId'] = $nextK1OrderNumber;
			//$SubActGroupID  = 1000006;
			//$where = '(SubActGroupID="'.$SubActGroupID.'")'; 
			// $clients = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblclients");
			// $data['clients'] = $clients;
			$states = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblxx_statelist");
			$data['states'] = $states;
			$citylist = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblxx_citylist");
			$data['citylist'] = $citylist;
			$talukalist = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblTalukaMaster");
			$data['talukalist'] = $talukalist;
			$products = $this->KirtiOneOrderModel->get_items_code();
			$data['products'] = $products;      
			$centermaster = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $centermaster; 
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")'; 
			$EffectOn = $this->KirtiOneOrderModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn; 
			$ActGroupID = 10011;
			$wh_effect = '(ActGroupID="'.$ActGroupID.'")'; 
			$DirectIncome = $this->KirtiOneOrderModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['DirectIncome'] = $DirectIncome; 
			/*$where = ['FY' => $fy];  
			$orderBy = 'Transdate ASC';
			$OrderDetails = $this->KirtiOneOrderModel->get_all_data_orderby($tablename="tblK1ordermaster",$orderBy,$where);*/
			if ($ORDNumber == '') {
				$title = _l('Sale Order');
			}else{
				$OrderDetails = $this->KirtiOneOrderModel->GetDirectSaleOrderDetails($ORDNumber);			
				$data['OrderDetails'] = $OrderDetails;			
				$SaleItemList = $this->KirtiOneOrderModel->GetSaleOrderItemList_New($ORDNumber);
				$data['item_order_detail'] = json_encode($SaleItemList); 
				$products = $this->KirtiOneOrderModel->get_items_code_by_categorytype($OrderDetails->CategoryType);
				$data['products'] = $products;
			  /*echo "<pre>";print_r($SaleItemList);die;
			 print_r($data['item_order_detail']);
			  die;*/
				$title = "Edit Sale Order";
			}
			$data['company_detail'] = $this->KirtiOneOrderModel->get_company_detail();
			$data['title'] = $title;
			$this->load->view('admin/KirtiOneOrder/AddEditDirectSaleOrder',$data);
		} 
		public function get_party(){
			$keyword = $this->input->get('keyword');
			$result = $this->db
					->select('AccountID, company')
					->like('AccountID', $keyword, 'after')
					->or_like('company', $keyword)
					->limit(20)
					->get(db_prefix().'clients')
					->result_array();
			return $this->output
					->set_content_type('application/json')
					->set_output(json_encode($result));
		}
		public function GetVendorDetails()
		{		
			$VendorID = $this->input->post('vendor_id');
			$trader_list = $this->KirtiOneOrderModel->GetAccountListVendorwise($VendorID);		
			echo json_encode($trader_list);
		}
		public function GetAccountWiseFarmerDetails()
		{
			$AccountID = $this->input->post('AccountID');       
			$where = '(AccountID="'.$AccountID.'")'; 
			$clientDetails = $this->KirtiOneOrderModel->get_data($tablename="tblclients",$where);
			if(!empty($clientDetails)){
				$this->db->select("tblShippingDetails.*,tblxx_statelist.state_name,tblxx_citylist.city_name,CONCAT(IFNULL(House, ''), ', ',IFNULL(Street, ''), ', ',IFNULL(Locality, ''), ', ',IFNULL(Block, ''), ', (',IFNULL(state_name, ''), ' - ',IFNULL(city_name, ''), ', ',IFNULL(Pincode, ''), ')') AS shipping_label");
				$this->db->from(db_prefix() . 'ShippingDetails');	
				$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblShippingDetails.State', 'LEFT');	
				$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblShippingDetails.District', 'LEFT');	
				$this->db->where(db_prefix() . 'ShippingDetails.AccountID', $AccountID);
				$ShippingDetails = $this->db->get()->result_array();
				 $clientDetails['ShippingData'] = $ShippingDetails;
			}
			$historyDetails = $this->KirtiOneOrderModel->get_all_data($tablename="tblK1history",$where);
			foreach($historyDetails as &$val)
			{
				$whs = '(ProductID="'.$val['ItemID'].'")'; 
				$itemdetails = $this->KirtiOneOrderModel->get_data($tablename="tblproduct",$whs);
				$val['ProductName'] = $itemdetails['ProductName'];
			}
			$orderDetails = $this->KirtiOneOrderModel->get_data($tablename="tblK1ordermaster",$where);
			$salesDetails = $this->KirtiOneOrderModel->get_data($tablename="tblK1salesmaster",$where);
			$total_bal = $this->KirtiOneOrderModel->get_data_for_account_bal($AccountID);
			$data_report = $this->KirtiOneOrderModel->get_data_general_ledger2($AccountID);
			$new_acc_bal = $total_bal->BAL1;
			$opening_bal = $total_bal->BAL1;  
			$CRSum = 0;
			$DRSum = 0;
			$finacial_year = $this->session->userdata('finacial_year');
			$total_debit = 0;
			$total_credit = 0;
			if (empty($data_report)) 
			{
				$OCR = 0.00;
				$ODR = 0.00;
				if ($new_acc_bal <= 0) {
					$OCR = abs($new_acc_bal);
					$OB = $OCR . 'Cr';
					} else {
					$ODR = abs($new_acc_bal);
					$OB = $ODR . 'Dr';
				}
			} 
			else 
			{
				$OCR = 0.00;
				$ODR = 0.00;
				if($new_acc_bal <=0){
					$OCR = abs($new_acc_bal);
					$OB = $OCR.'Cr';
					}else{
					$ODR = abs($new_acc_bal);
					$OB = $ODR.'Dr';
				}
				$total_credit = $total_credit + $OCR;
				$total_debit = $total_debit + $ODR;
				foreach ($data_report as $key => $value) 
				{
					if ($value["Amount"] !== "0.00") 
					{                    
						// Update the balance based on transaction type (Debit or Credit)
						if ($value["TType"] == "D") {
							$new_acc_bal = $new_acc_bal + $value["Amount"];
							$dvalue = $value["Amount"];
							$total_debit = $total_debit + $dvalue;                       
							$dvalue = number_format($dvalue,2);                                     
						}
						if ($value["TType"] == "C") {
							$new_acc_bal = $new_acc_bal - $value["Amount"];
							$cvalue = $value["Amount"];
							$total_credit = $total_credit + $cvalue;
							$cvalue = number_format($cvalue,2);                        
						}                    
						// Calculate the new balance (new_acc_bal2)
						$new_acc_bal2 = $new_acc_bal;
						if ($new_acc_bal > 0) {
							$nab_dr_cr = "Dr";
							} else {
							$nab_dr_cr = "Cr";
						}      
						// Round off the final balance to 2 decimal places
						$new_acc_bal2 = number_format($new_acc_bal2, 2) . " " . $nab_dr_cr;                         
						// At this point, you can use $new_acc_bal2 for further calculations or logging if needed
					}
				}            
			}
			$data = array(
            'clientDetails' => $clientDetails, 
            'historyDetails' => $historyDetails,
            'orderDetails'=> $orderDetails,
            'salesDetails'=> $salesDetails,
            'ClosingBalance'=>$new_acc_bal2,
			);      
			echo json_encode($data);        
		}
		public function GetItemDetails($ItemID)
		{			
			$ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($ItemID);			
			echo json_encode($ItemDetails);
		}
		public function GetItemDetailsForDirectSaleOld($ItemID,$CenterID,$PartyID)
		{			
			$ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($ItemID,$CenterID);
			if(!empty($ItemDetails)){
			    $filterdata = [
    				'ItemID'=>$ItemID,
    				'CenterID'=>$CenterID,
    				'BatchID'=>"",
    			];
    			$ItemBatch = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
				$ItemDetails->BatchList = $ItemBatch;
			}
			echo json_encode($ItemDetails);
		}
		public function GetItemDetailsForDirectSale($ItemID,$CenterID,$PartyID)
		{			
			$ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($ItemID,$CenterID);
			if(!empty($ItemDetails)){
			    $filterdata = [
    				'ItemID'=>$ItemID,
    				'CenterID'=>$CenterID,
    				'BatchID'=>"",
    			];
    			$ItemBatch = $this->KirtiOneOrderModel->GetItemBatchListWithStockDSO($filterdata);
				$ItemDetails->BatchList = $ItemBatch;
			}
			echo json_encode($ItemDetails);
		}
		public function GetItemDetailsStock($ItemID,$CenterID,$BatchNo)
		{		
		    $ItemIDs = $this->input->post('ItemID');
			$ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($ItemID);
			if(!empty($ItemDetails)){
				$filterdata = [
				'ItemID'=>$ItemID,
				'CenterID'=>$CenterID,
				'BatchNo'=>$BatchNo,
				];
				$StockData = $this->KirtiOneOrderModel->GetItemWiseStockData($filterdata);
				$OpnQtyData = $this->KirtiOneOrderModel->GetItemWiseOpnQty($filterdata);
				$OQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach($StockData as $stockkey=>$stockval){
					if($stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
						$SaleQty += $stockval["TotalQty"];
					}else if($stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
						$PurchQty += $stockval["TotalQty"];
					}else if($stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
						$InQty += $stockval["TotalQty"];
					}else if($stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
						$OutQty += $stockval["TotalQty"];
					}else if($stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
						$InwardQty += $stockval["TotalQty"];
					}
				}
				if($OpnQtyData){
				    $OQty = $OpnQtyData->TotalOQty;
				}
				$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				$ItemDetails->StockQty = ($BalQty);
			}
			echo json_encode($ItemIDs);
		}
		public function GetItemBatchStock()
		{		
		    $ItemID = $this->input->post('ItemID');
		    $BatchID = $this->input->post('BatchID');
		    $CenterID = $this->input->post('CenterID');
		    $filterdata = [
				'ItemID'=>$ItemID,
				'CenterID'=>$CenterID,
				'BatchID'=>$BatchID,
			];
			// echo "<pre>";print_r($filterdata);die;
			$BatchStock = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
			echo json_encode($BatchStock);
		}
		public function GetItemBatchStockDSO(){		
		    $ItemID = $this->input->post('ItemID');
		    $BatchID = $this->input->post('BatchID');
		    $CenterID = $this->input->post('CenterID');
		    $filterdata = [
				'ItemID'=>$ItemID,
				'CenterID'=>$CenterID,
				'BatchID'=>$BatchID,
			];
			// echo "<pre>";print_r($filterdata);die;
			$BatchStock = $this->KirtiOneOrderModel->GetItemBatchListWithStockDSO($filterdata);
			echo json_encode($BatchStock);
		}
//============================== Diresct Sale ORder List =======================
		public function Order_table_data()
		{
			$data = array(
    			'from_date' => $this->input->post('from_date'),
    			'to_date'  => $this->input->post('to_date'),
			    'CategoryType'  => $this->input->post('CategoryTypeFilter'),
			    'Report_type'=>1
			);
			$result = $this->ItemModel->getItemOrderDetailsDB($data); 
			$ItemData = $this->ItemModel->getItemOrderDetailsDB($data);
			$html = '';
			/*$totalQtySum = 0;$TotalItemAmt = 0;$TotalDiscAmt = 0;$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;
    		$CashTotal = 0;$OnlineTotal = 0;$TotalOtherAmt = 0;
    		*/foreach($result as $key=>$value)
    		{
    			if ($value['OrderStatus'] == "O") {
    				$OrderStat = "Pending";
    			} elseif ($value['OrderStatus'] == "F") {
    				$OrderStat = "Completed";
    			} elseif ($value['OrderStatus'] == "C") {
    				$OrderStat = "Cancelled";
    			}
				$OrdItemTotal = 0;
				$OrdItemDiscAmt = 0;
				$OrdTaxableAmt = 0;
				$OrdCGSTAmt = 0;$OrdSGSTAmt = 0;$OrdIGSTAmt = 0;
				$OrdNetTotal = 0;
				$PartyGST = "";
				foreach($ItemData as $key1=>$val2){
					if($value["OrderID"] == $val2["OrderID"]){
						$PartyGST = $val2["PartyGST"];
						$TaxableAmt = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$GSTAmt = 0;$NetAmt = 0;
						$OrderAmt = $val2["OrderAmt"] - $val2["DiscAmt"];
						//$GSTPer = $val2['cgst'] + $val2['sgst'] + $val2['igst'];
    					$GSTPer = $val2['taxrate'];
    					$ExGSTAmt = $val2['sgstamt'] + $val2['cgstamt'] + $val2['igstamt'];
    					if($ExGSTAmt > 0){
    					    $TaxableAmt = $OrderAmt;
    					    $GSTAmt = $ExGSTAmt;
    					}else{
    					    $TaxableAmt = $OrderAmt / (1+($GSTPer/100));
    					    $GSTAmt = $OrderAmt - $TaxableAmt;
    					}
    					if($val2['state'] == $val2['CenterState'] || $val2['state'] == ""){
    					    $CGSTAmt = $GSTAmt / 2;
    					    $SGSTAmt = $GSTAmt / 2;
    					}else{
    					    $IGSTAmt = $GSTAmt;
    					}
						$OrdItemTotal += $val2["OrderAmt"];
						$OrdItemDiscAmt += $val2["DiscAmt"];
						$OrdCGSTAmt += $CGSTAmt;
						$OrdSGSTAmt += $SGSTAmt;
						$OrdIGSTAmt += $IGSTAmt;
						$GSTAmt = $CGSTAmt + $SGSTAmt + $IGSTAmt;
						$OrdTaxableAmt += $TaxableAmt;
						$NetAmt = $TaxableAmt + $GSTAmt;
						$OrdNetTotal += $NetAmt;
					}
				}
				$redirectUrl = admin_url('KirtiOneOrder/AddEditSaleOrder/');
				$html .= '<tr onclick="window.open(\''.$redirectUrl.$value["OrderID"].'\');">';           
				$html .= '<td>'.($key+1).'</td>';   
				$html .= '<td>'.$value["OrderID"].'</td>';	
				$html .= '<td>'._d(substr($value["Transdate"],0,10)).'</td>';
				$html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
				$html .= '<td>'.$value['CenterName'].'</td>'; 
				$html .= '<td>'.$value['BIllNo'].'</td>'; 
				$html .= '<td style="text-align:right;">' . number_format($OrdNetTotal , 2, '.', '') . '</td>'; 
				$html .= '<td>'.$OrderStat.'</td>';	  
				$html .= '<td>'.$value['order_type'].'</td>'; 
				$html .= '</tr>'; 
    		}
			echo $html;
		}
		public function FetchAddressDetailsByPincode()
		{
			$zip = $this->input->post('zip');
			$curl = curl_init();
			curl_setopt_array(
			$curl,
			array(
			CURLOPT_URL => 'https://api.postalpincode.in/pincode/' . $zip . '',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			)
			);
			$response = curl_exec($curl);
			curl_close($curl);
			$response_array = json_decode($response);
			echo  $response;
		}
		/* List all Gatepass datatables */
		public function view_gatepass($id = '')
		{
			if (!has_permission_new('gatepass', '', 'view')) {
				access_denied('gatepass');
			}
			close_setup_menu();
			$data['title']                = "View Gatepass";
			$data['bodyclass']            = 'invoices-total-manual';
			$this->load->view('admin/KirtiOneOrder/gatepass', $data);
		}
		public function gatepass_list()
		{
			if (!has_permission_new('gatepass', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('ordergatepass');
				}
			}
		}
		public function Challangatepass_list()
		{
			if (!has_permission_new('gatepass', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('Challangatepass');
				}
			}
		}
		public function gatepass($ChallanID)
		{
			if (!$ChallanID) {
				redirect(admin_url('KirtiOneOrder/view_gatepass'));
			}
			if (!has_permission_new('gatepass', '', 'view')) {
				access_denied('Invoices');
			}
			$invoice        = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($ChallanID);
			if(is_null($invoice->Gatepassuserid)){
				$selected_company = $this->session->userdata('root_company');
				$fy = $this->session->userdata('finacial_year');
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $fy);  
				$this->db->where('ChallanID', $ChallanID);
				$this->db->update(db_prefix() . 'K1challanmaster', [
				'Gatepassuserid' => $this->session->userdata('username'),
				'GetPassTime' => date('Y-m-d H:i:s'),
				]);
			}
			try {
				$pdf = k1gatepass_pdf($invoice);
				} catch (Exception $e) {
				$message = $e->getMessage();
				echo $message;
				if (strpos($message, 'Unable to get the size of the image') !== false) {
					show_pdf_unable_to_get_image_size_error();
				}
				die;
			}
			$type = 'D';
			if ($this->input->get('output_type')) {
				$type = $this->input->get('output_type');
			}
			if ($this->input->get('print')) {
				$type = 'I';
			}
			$pdf->Output(mb_strtoupper(slug_it($OrderID)) . '-Gatepass.pdf', $type);
		}
		public function generateEInvoice(){
		    $postData = $this->input->post();
		    // Get data
			$Salesdata = $this->KirtiOneOrderModel->GetTaxableTransaction($postData);
 			$company_details = $this->KirtiOneOrderModel->get_company_detail($selected_company);
			//E Invoice API
            $date = date("d/m/Y");
            $headersAuth = array(
			'email' => $company_details->einvoice_email,
			'username' => $company_details->einvoice_username,
			'password' => $company_details->einvoice_password,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'client_id' => $company_details->einvoice_client_id,
			'client_secret' => $company_details->einvoice_client_secret,
			'gstin' => $company_details->einvoice_gstin,
            );  
            $base_url = 'https://api.mastergst.com/einvoice/authenticate';
            $query_params = http_build_query(array(
			'email' => $headersAuth['email'],
            ));
            $curl = curl_init();
            curl_setopt_array($curl, array(
			CURLOPT_URL => $base_url . '?' . $query_params,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
			'username: ' . $headersAuth['username'],
			'password: ' . $headersAuth['password'],
			'ip_address: ' . $headersAuth['ip_address'],
			'client_id: ' . $headersAuth['client_id'],
			'client_secret: ' . $headersAuth['client_secret'],
			'gstin: ' . $headersAuth['gstin'],
			),
            ));
            $api_response = curl_exec($curl);
            if ($api_response === false) {
                return $api_response;
			}
            curl_close($curl);
            // // Decode the JSON response
            $response_data = json_decode($api_response, true);
            // // Return the AuthToken
            $authKey = $response_data['data']['AuthToken'];
            $headersGenerateInvoice = array(
			'email' => $company_details->einvoice_email,
			'username' => $company_details->einvoice_username,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'client_id' => $company_details->einvoice_client_id,
			'client_secret' => $company_details->einvoice_client_secret,
			'authToken' => $authKey,
			'gstin' => $company_details->einvoice_gstin
            );
            //Fetch Item Details
            $itemDetails = $this->KirtiOneOrderModel->fetchItemDetails($Salesdata[0]['SalesID']);
            $i = 0;
			$SlNo = 1;
            $newItemList = array();
			foreach ($itemDetails as $value) {
				$newItemList[$i]['SlNo'] = (string)$SlNo;
				$newItemList[$i]['PrdDesc'] = $value["hsn_code"];;
				$newItemList[$i]['IsServc'] = 'N';
				$newItemList[$i]['HsnCd'] = $value["hsn_code"];
				$newItemList[$i]['Barcde'] = null;
				$newItemList[$i]['Qty'] = floatval($value["BilledQty"]);
				$newItemList[$i]['FreeQty'] = 0;
				$newItemList[$i]['Unit'] = $value["unit"];
				$newItemList[$i]['UnitPrice'] = floatval($value["BasicRate"]);
				$newItemList[$i]['TotAmt'] = floatval($value["ChallanAmt"]);
				$newItemList[$i]['Discount'] = floatval($value["DiscAmt"]);
				$newItemList[$i]['PreTaxVal'] = 0.00;
				$newItemList[$i]['AssAmt'] = floatval($value["ChallanAmt"]);
				if($value["igst"] == NULL || $value["igst"] == '0.00'){
					$gst = $value["sgst"] + $value["cgst"];
					$igstAmt = 0.00;
					$cgstAmt = floatval($value["cgstamt"]);
					$sgstAmt = floatval($value["sgstamt"]);
					$IgstOnIntra = "N";
					}else{
					$gst = $value["igst"];
					$igstAmt = floatval($value["igstamt"]);
					$cgstAmt = 0.00;
					$sgstAmt = 0.00;
					$IgstOnIntra = "N";
				}
				$newItemList[$i]['GstRt'] = floatval($gst);
				$newItemList[$i]['IgstAmt'] = $igstAmt;
				$newItemList[$i]['CgstAmt'] = $cgstAmt;
				$newItemList[$i]['SgstAmt'] = $sgstAmt;
				$newItemList[$i]['CesRt'] = 0.00;
				$newItemList[$i]['CesAmt'] = 0.00;
				$newItemList[$i]['CesNonAdvlAmt'] = 0;
				$newItemList[$i]['StateCesRt'] = 0;
				$newItemList[$i]['StateCesAmt'] = 0;
				$newItemList[$i]['StateCesNonAdvlAmt'] = 0;
				$newItemList[$i]['OthChrg'] = 0;
				$newItemList[$i]['TotItemVal'] = floatval($value["NetChallanAmt"]);
				$newItemList[$i]['BchDtls'] = null;
				$i++;
				$SlNo++;
			}
            $fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$TrandId = $Salesdata[0]['SalesID'];
			$this->db->select(db_prefix() . 'K1salesmaster.*,'.db_prefix() . 'clients.vat,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.zip,'.db_prefix() . 'contacts.email,'.db_prefix() . 'clients.dist,'.db_prefix() .'xx_citylist.city_name,'.db_prefix() .'xx_statelist.id As StateId');
			$this->db->from(db_prefix() . 'K1salesmaster');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'K1salesmaster.AccountID = '.db_prefix() . 'clients.AccountID AND '.db_prefix() . 'K1salesmaster.PlantID = '.db_prefix() . 'clients.PlantID');
			$this->db->join(db_prefix() . 'contacts', db_prefix() . 'clients.AccountID = '.db_prefix() . 'contacts.AccountID AND '.db_prefix() . 'clients.PlantID = '.db_prefix() . 'contacts.PlantID');
			$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'clients.dist = '.db_prefix() . 'xx_citylist.id','LEFT');
			$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'clients.state = '.db_prefix() . 'xx_statelist.short_name');
			$this->db->where(db_prefix() . 'K1salesmaster.SalesID', $TrandId);
			$this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1salesmaster.FY', $fy);
			$Sales_details = $this->db->get()->row();
			$IgstVal = $Sales_details->igstamt;
			$CgstVal = $Sales_details->cgstamt;
			$SgstVal = $Sales_details->sgstamt;
			$CesVal = 0;
			$StCesVal = 0;
			$Discount = $Sales_details->DiscAmt;
			$OthChrg = $Sales_details->tcsAmt;
			$rnd = $Sales_details->RndAmt - $Sales_details->BillAmt;
			$RndOffAmt = number_format($rnd,2);
			$TotInvVal = $Sales_details->RndAmt;
            $ValDtls = array(
			"AssVal"=>floatval($Sales_details->SaleAmt),
			"IgstVal"=>floatval($IgstVal),
			"CgstVal"=>floatval($CgstVal),
			"SgstVal"=>floatval($SgstVal),
			"CesVal"=>$CesVal,
			"StCesVal"=>$StCesVal,
			"Discount"=>floatval($Discount),
			"OthChrg"=>floatval($OthChrg),
			"RndOffAmt"=>floatval($RndOffAmt),
			"TotInvVal"=>floatval($TotInvVal),
            );
			//Buyer Details
			$pgst = $Sales_details->gstno;
			$LglNm = $Sales_details->company;
			$Addr1 = $Sales_details->address;
			$Addr2 = 'Pune';
			$Stcd = $Sales_details->StateId;
			$Pos_c = $Stcd;
			if($Sales_details->city_name == ""){
                $location = $Sales_details->city;
				}else{
                $location = $Sales_details->city_name;
			}
			$Loc = $location;
			$Pin = $Sales_details->zip;
			$Ph = $Sales_details->phonenumber;
			//Seller Details
			$Gstin_c = $company_details->gst;
			$LglNm_c = $company_details->company_name;
			$Addr1_c = $company_details->address;
			$Addr2_c = 'Pune';
			$Loc_c = $company_details->city;
			$Pin_c = $company_details->pincode;
			$Stcd_c = "09";
			$Ph_c = $company_details->mobile1;
			$BuyerDtls = array(
			"Gstin"=>$pgst,
			"LglNm"=>$LglNm,
			"TrdNm"=>$LglNm,
			"Pos"=>$Pos_c,
			"Addr1"=>$Addr1,
			"Addr2"=>$Addr2,
			"Loc"=>$Loc,
			"Pin"=>(int)$Pin,
			"Stcd"=>$Stcd,
			"Ph"=>$Ph,
            );
			$SellerDtls = array(
			"Gstin"=>$Gstin_c,
			"LglNm"=>$LglNm_c,
			"TrdNm"=>$LglNm_c,
			"Addr1"=>$Addr1_c,
			"Addr2"=>$Addr2_c,
			"Loc"=>$Loc_c,
			"Pin"=>(int)$Pin_c,
			"Stcd"=>$Stcd_c,
			"Ph"=>$Ph_c,
            );
            $DispDtls = array(
			"Nm"=>$LglNm,
			"Addr1"=>$Addr1,
			"Addr2"=>$Addr2,
			"Loc"=>$Loc,
			"Pin"=>(int)$Pin,
			"Stcd"=>$Stcd,
            );
			$ShipDtls = array(
			"Gstin"=>$pgst,
			"LglNm"=>$LglNm,
			"TrdNm"=>$LglNm,
			"Addr1"=>$Addr1,
			"Addr2"=>$Addr2,
			"Loc"=>$Loc,
			"Pin"=>(int)$Pin,
			"Stcd"=>$Stcd,
            );
            $InvoiceNo = $Salesdata[0]['SalesID'];
            $body = [];
            array_push($body, array('sellerDetails' => $SellerDtls, 'buyerDetails' => $BuyerDtls, 'dispDtls' => $DispDtls, 'shipDtls' => $ShipDtls ,'itemList' => $newItemList, 'valDtls' => $ValDtls));
            $curl = curl_init();
            curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.mastergst.com/einvoice/type/GENERATE/version/V1_03?email=ajinkya.bhalerao@globalinfocloud.com',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{
			"Version": "1.1",
			"TranDtls": {
			"TaxSch": "GST",
			"SupTyp": "B2B",
			"IgstOnIntra": "N",
			"RegRev": "N",
			"EcmGstin": null
			},
			"DocDtls": {
			"Typ": "INV",
			"No": "'. $InvoiceNo .'",
			"Dt": "'. $date .'"
			},
			"SellerDtls": {
			"Gstin": "29AABCT1332L000",
			"LglNm": "INDMARK PAPER FORM PRIVATE LIMITED",
			"TrdNm": "INDMARK PAPER FORM PRIVATE LIMITED",
			"Addr1": "Pune",
			"Addr2": null,
			"Loc": "Gorakhpur",
			"Pin": 560001,
			"Stcd": "29",
			"Ph": "7355356548"
			},
			"BuyerDtls": {
			"Gstin": "09AABCB2066P3ZB",
			"LglNm": "BRITANNIA INDUSTRIES LIMITED",
			"TrdNm": "BRITANNIA INDUSTRIES LIMITED",
			"Pos": "9",
			"Addr1": "GATA NO. 1801,1802K,1803 VILLAGE PARA KHADAULI TEHSIL NAWABGANJ BARABANKI ,UTTAR PRADESH",
			"Addr2": null,
			"Loc": "BARABANKI",
			"Pin": 273005,
			"Stcd": "9",
			"Ph": null
			},
			"DispDtls": ' . json_encode($body[0]['dispDtls']) . ',
			"ShipDtls": {
			"Gstin": "09AABCB2066P3ZB",
			"LglNm": "BRITANNIA INDUSTRIES LIMITED",
			"TrdNm": "BRITANNIA INDUSTRIES LIMITED",
			"Addr1": "GATA NO. 1801,1802K,1803 VILLAGE PARA KHADAULI TEHSIL NAWABGANJ BARABANKI ,UTTAR PRADESH",
			"Addr2": null,
			"Loc": "BARABANKI",
			"Pin": 273005,
			"Stcd": "9"
			},
			"ItemList": [
			{
			"SlNo": "1",
			"PrdDesc": "48191010",
			"IsServc": "N",
			"HsnCd": "48191010",
			"Barcde": null,
			"Qty": 500,
			"FreeQty": 0,
			"Unit": "Pcs",
			"UnitPrice": 8,
			"TotAmt": 4000,
			"Discount": 0,
			"PreTaxVal": 0,
			"AssAmt": 4000,
			"GstRt": 18,
			"IgstAmt": 720,
			"CgstAmt": 0,
			"SgstAmt": 0,
			"CesRt": 0,
			"CesAmt": 0,
			"CesNonAdvlAmt": 0,
			"StateCesRt": 0,
			"StateCesAmt": 0,
			"StateCesNonAdvlAmt": 0,
			"OthChrg": 0,
			"TotItemVal": 4720,
			"BchDtls": null
			}
			],
			"ValDtls": {
			"AssVal": 4000,
			"IgstVal": 720,
			"CgstVal": 0,
			"SgstVal": 0,
			"CesVal": 0,
			"StCesVal": 0,
			"Discount": 0,
			"OthChrg": 0,
			"RndOffAmt": 0,
			"TotInvVal": 4720
			}
			}',
			CURLOPT_HTTPHEADER => array(
			'ip_address: ' . $headersGenerateInvoice['ip_address'] . '',
			'client_id: ' . $headersGenerateInvoice['client_id'] . '',
			'client_secret: ' . $headersGenerateInvoice['client_secret'] . '',
			'username: ' . $headersGenerateInvoice['username'] . '',
			'auth-token:' . $headersGenerateInvoice['authToken'] . '',
			'gstin: ' . $headersGenerateInvoice['gstin'] . '',
			'Content-Type: application/json',
			),
            ));
            $apiResponse = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($apiResponse, true);
            $irn = $data['data']['Irn'];
            $signedQRCode = $data['data']['SignedQRCode'];
            $AckNo = $data['data']['AckNo'];
            $AckDt = $data['data']['AckDt'];
            $Status = $data['data']['Status'];
            $status_cd = $data['data']['status_cd'];
            $signedInvoice = $data['data']['SignedInvoice'];
            $status_desc = $data['status_desc'];
            // echo "<pre>";print_r($data);die;
            $response = array(
			'IRN' => $irn,
			'SignedQRCode' => $signedQRCode,
			'AckNo' => $AckNo,
			'AckDate' => $AckDt,
			'Status' => $Status,
			'status_cd' => $status_cd,
			'status_desc' => $status_desc,
			'SignedInvoice' => $signedInvoice
            );
            if($response["Status"] == 'ACT'){
                //Update Table entry
                $updateArray = array(
				'irn' => $response['IRN'],
				'Qrcode' => $response['SignedQRCode'],
				'ackno' => $response['AckNo'],
				'ackdate' => $response['AckDate'],
				'SignedInvoice' => $response['SignedInvoice'],
				'Lupdate' => date('Y-m-d H:i:s')
                );
                $this->db->where('SalesID', $Salesdata[0]['SalesID']);
				$this->db->update(db_prefix() . 'K1salesmaster', $updateArray);
				set_alert('success', 'E-Invoice generated Successfully');
                echo json_encode("success");
				}else{
                set_alert('warning', 'Error Occurred');
                echo json_encode("error");
			}
		}
		public function GetSaleOrderDetails(){
			// POST data
			$OrderID = $this->input->post('OrderID');
			// Get data
			$Salesdata = $this->KirtiOneOrderModel->GetSaleOrderDetails($OrderID);
			echo json_encode($Salesdata);
		}
//================================= B2C Sale invoice Print =====================
		public function DirectSalePdf($id)
		{
			if (!$id) {
				redirect(admin_url('ItemMaster/ItemOrderDetails'));
			}
			if (!has_permission_new('OrderMaster', '', 'view')) {
                access_denied('Invoices');
			}
			$invoice        = $this->KirtiOneOrderModel->GetDirectSaleOrderDetails($id);
			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
			try {
				$pdf = DirectSaleinvoice_pdf($invoice);
			} catch (Exception $e) {
				$message = $e->getMessage();
				echo $message;
				if (strpos($message, 'Unable to get the size of the image') !== false) {
					show_pdf_unable_to_get_image_size_error();
				}
				die;
			}
			$type = 'D';
			if ($this->input->get('output_type')) {
				$type = $this->input->get('output_type');
			}
			if ($this->input->get('print')) {
				$type = 'I';
			}
			$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);
		}
//============================== B2B Sale Invoice Print ========================
	public function B2BSaleInvoicePdf($id)
	{
		if (!$id) {
			redirect(admin_url('ItemMaster/ItemOrderDetails'));
		}
		if (!has_permission_new('OrderMaster', '', 'view')) {
            access_denied('Invoices');
		}
		$invoice        = $this->KirtiOneOrderModel->GetDirectSaleOrderDetails($id);
		// echo "<pre>"; print_r($invoice); die;
		$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
		try {
			$pdf = B2BSaleinvoice_pdf($invoice);
		} catch (Exception $e) {
			$message = $e->getMessage();
			echo $message;
			if (strpos($message, 'Unable to get the size of the image') !== false) {
				show_pdf_unable_to_get_image_size_error();
			}
			die;
		}
		$type = 'D';
		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}
		if ($this->input->get('print')) {
			$type = 'I';
		}
		$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);
	}
		public function pdf($id)
		{
			if (!$id) {
				redirect(admin_url('ItemMaster/ItemOrderDetails'));
			}
			if (!has_permission_new('OrderMaster', '', 'view')) {
                access_denied('Invoices');
			}
			$invoice        = $this->KirtiOneOrderModel->GetDirectSaleOrderDetails($id);
			// print_r($invoice);
			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
			//$invoice_number = format_invoice_number($invoice->id);
			try {
				$pdf = Saleinvoice_pdf($invoice);
			} catch (Exception $e) {
				$message = $e->getMessage();
				echo $message;
				if (strpos($message, 'Unable to get the size of the image') !== false) {
					show_pdf_unable_to_get_image_size_error();
				}
				die;
			}
			$type = 'D';
			if ($this->input->get('output_type')) {
				$type = $this->input->get('output_type');
			}
			if ($this->input->get('print')) {
				$type = 'I';
			}
			$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);
		}
		public function AddEditNewSaleOrder($OrdNumber = '')
		{
			if (!has_permission_new('SaleOrder', '', 'view')) {
				access_denied('purchase order');
			}   		
			if ($this->input->post()) {
				$sale_order_data = $this->input->post();				
				if ($OrdNumber == '') {
					if (!has_permission_new('SaleOrder', '', 'create')) {
						access_denied('SaleOrder');
					}
					$id = $this->KirtiOneOrderModel->AddKirtiOneNewSaleOrder($sale_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('KirtiOneOrder/AddEditNewSaleOrder'));
						// redirect(admin_url('KirtiOneOrder/AddEditNewSaleOrder/').$id);
					}
				}else{
					if (!has_permission_new('SaleOrder', '', 'edit')) {
						access_denied('SaleOrder');
					}				
					$id = $this->KirtiOneOrderModel->UpdateKirtiOneNewSaleOrder($sale_order_data,$OrdNumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('KirtiOneOrder/AddEditNewSaleOrder'));
					}
				}
			}
			if ($OrdNumber == '') {
				$title = _l('Sale Order');
			}else{
				$SaleDetails = $this->KirtiOneOrderModel->GetSaleOrderDetails($OrdNumber);			
				$data['sale_details'] = $SaleDetails;			
				$PurchaseItemList = $this->KirtiOneOrderModel->GetSaleOrderItemList($OrdNumber);
				foreach ($PurchaseItemList as $key => $value) {
					if($value['Measuredin'] == 'Boxs'){
						$PurchaseItemList[$key]['BasicRate'] = round(($value['PackingQty'] * $value['BasicRate']), 2);
						$PurchaseItemList[$key]['PurchRate'] = round(($value['PackingQty'] * $value['PurchRate']), 2);
						$PurchaseItemList[$key]['SaleRate'] = round(($value['PackingQty'] * $value['SaleRate']), 2);
					}
				}
				$data['pur_order_detail'] = json_encode($PurchaseItemList);           
				$title = "Edit Sale Order";
			}
			$centermaster = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblCenterMaster");
			$data['centermaster'] = $centermaster; 
			$trader_list = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblclients");
			$data['trader_list'] = $trader_list;
			$data['item_code'] = $this->KirtiOneOrderModel->get_items_code();	
			$data['statelist'] = $this->KirtiOneOrderModel->getstatelist();
			$data['company_detail'] = $this->KirtiOneOrderModel->get_company_detail();
			$this->load->view('admin/KirtiOneOrder/AddEditNewSaleOrder',$data);
		}
		public function GetPartyDetails()
		{		
			$PartyID = $this->input->post('vendor_id');
			$trader_list = $this->KirtiOneOrderModel->GetAccountListPartywise($PartyID);		
			echo json_encode($trader_list);
		}
		public function load_data_for_sale_order()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')           
			);
			$SaleList = $this->KirtiOneOrderModel->load_data_for_sale_orderkirtione($data);
			$html = "";
			$TotalSaleAmt = 0;
			$TotalDiscAmt = 0;       
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;        
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($SaleList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}		
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
					}else if($val['OrderStatus'] == "O"){
					$OrderStatus = "Pending";
				}
				$url = admin_url()."KirtiOneOrder/AddEditNewSaleOrder/".$val["OrderID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';  
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';			
				$html .= '<td style="text-align:center;">'.$val["OrderID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>'; 						
				$html .= '<td style="text-align:right;">'.$val["saleamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Discamt"].'</td>';          
				$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';           
				$html .= '<td style="text-align:right;">'.$val["Invamt"].'</td>';
				$html .= '</tr>';
				$TotalSaleAmt += $val["saleamt"];
				$TotalDiscAmt += $val["Discamt"];            
				$TotalCgstAmt += $val["cgstamt"];
				$TotalSgstAmt += $val["sgstamt"];
				$TotalIgstAmt += $val["igstamt"];            
				$TotalInvAmt += $val["Invamt"];
			}
			$html .= '<tr>';
			$html .= '<td colspan="4" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSaleAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';       
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function CancelOrderWiseRequestItems()
		{
			$poId = $this->input->post('poId');          
			if($poId !="")
			{           
				$where = '(OrderID="'.$poId.'")'; 
				$orderDetails = $this->KirtiOneOrderModel->get_data($tablename="tblK1ordermaster",$where);
				$updateOrderData = array(  
				'OrderStatus'=>"C",  			
                'saleamt'=>'0.00',
                'Discamt'=>'0.00',
				'cgstamt'=>'0.00',
				'sgstamt'=>'0.00',
                'igstamt'=>'0.00',   
				'RoundOffAmt'=>'0.00',
				'Invamt'=>'0.00',
				'ItCount'=>'0'                              
				);
				$cancelOrder = $this->KirtiOneOrderModel->edit_data($tablename="tblK1ordermaster",$where,$updateOrderData);
				$wh = '(OrderID="'.$poId.'")'; 
				$updateItemData = array(               
                'TType2'=>"CANCEL",              
                'OrderQty'=>'0.00',           
                'BilledQty'=>'0.00',
                'DiscPerc'=>'0.00',
                'DiscAmt'=>'0.00',
                'cgst'=>'0.00',
                'cgstamt'=>'0.00',
                'sgst'=>'0.00',
                'sgstamt'=>'0.00',
                'igst'=>'0.00',
                'igstamt'=>'0.00',           
                'OrderAmt'=>'0.00',
                'ChallanAmt'=>'0.00',
                'NetOrderAmt'=>'0.00',
                'NetChallanAmt'=>'0.00'           
				);
				$cancelItemdata = $this->KirtiOneOrderModel->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);      
			}     
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
//================== Add Edit Delivery Order Page load==========================	
	public function AddEditDeliveryOrder($ChlNumber = '')
	{
		if (!has_permission_new('DeliveryOrder', '', 'view')) {
			access_denied('purchase order');
		}   		
		if ($this->input->post()) {
			$pur_order_data = $this->input->post();				
			// echo "<pre>";
            // print_r($pur_order_data);
            // die;
			if ($ChlNumber == '') {
				if (!has_permission_new('DeliveryOrder', '', 'create')) {
					access_denied('DeliveryOrder');
				}
				$response = $this->KirtiOneOrderModel->AddKirtiOneDeliveryOrder($pur_order_data);
				// echo json_encode($response); die;
				if ($response["status"] == true) {
					set_alert('success', $response["message"]);
					redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder/').$response["ChallanID"]);
				}else{
                    set_alert('warning', $response["message"]);
					redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder/'));
                }
			}else{
				if (!has_permission_new('DeliveryOrder', '', 'edit')) {
					access_denied('DeliveryOrder');
				}				
				$id = $this->KirtiOneOrderModel->UpdateKirtiOneDeliveryOrder($pur_order_data,$ChlNumber);
				set_alert('success', $response["message"]);
				redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder/').$response["ChallanID"]);
			}
		}
		if ($ChlNumber == '') {
			$title = "Create Delivery Order";
		}else{
			$challan_details = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($ChlNumber);			
			$data['challan_details'] = $challan_details;	
			$ChlItemList = $this->KirtiOneOrderModel->GetDeliveryChallanItemList($ChlNumber);
			foreach ($ChlItemList as $key => $value) {
				if($value['UOM'] == 'Boxs'){
					$ChlItemList[$key]['BasicRate'] = round(($value['PackingQty'] * $value['BasicRate']), 2);
					$ChlItemList[$key]['PurchRate'] = round(($value['PackingQty'] * $value['PurchRate']), 2);
					$ChlItemList[$key]['SaleRate'] = round(($value['PackingQty'] * $value['SaleRate']), 2);
				}
			}
			$data['chl_item_detail'] = json_encode($ChlItemList);           
			// echo "<pre>";
			// print_r($ChlItemList);
			// print_r($challan_details);
			// die;
			$title = "Edit  Delivery Order";
		}
		$trader_list = $this->KirtiOneOrderModel->PendingSaleOrderVendors();
		$data['trader_list'] = $trader_list;
		$data['item_code'] = $this->KirtiOneOrderModel->get_items_code();	
		$data['statelist'] = $this->KirtiOneOrderModel->getstatelist();
		$data['company_detail'] = $this->KirtiOneOrderModel->get_company_detail();
		$this->load->view('admin/KirtiOneOrder/AddEditDeliveryOrder',$data);
	}
		public function GetSaleOrderItemData()
		{
			// POST data
			$OrderNo = $this->input->post('OrderNo');
			// Get data
			$OrderData['OrderData'] = $this->KirtiOneOrderModel->GetSaleOrderDetails($OrderNo);
			$history = $this->KirtiOneOrderModel->GetSaleOrderItemListForDelivery($OrderNo);
			foreach ($history as $key => $value) {
				if($value['SuppliedIn'] == 'Boxs'){
					$history[$key]['BasicRate'] = round(($value['PackingQty'] * $value['BasicRate']), 2);
					$history[$key]['PurchRate'] = round(($value['PackingQty'] * $value['PurchRate']), 2);
					$history[$key]['SaleRate'] = round(($value['PackingQty'] * $value['SaleRate']), 2);
				}
			}
			$OrderData['historytbl'] = $history;
			echo json_encode($OrderData);
		}
		public function GetChallanItemData(){
			// POST data
			$ChallanID = $this->input->post('ChallanID');
			// Get data
			$SaleData['ChallanData'] = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($ChallanID);
			$ChlItemList = $this->KirtiOneOrderModel->GetChallanItemListForDelivery($ChallanID);
			foreach ($ChlItemList as $key => $value) {
				if($value['Measuredin'] == 'Boxs'){
					$ChlItemList[$key]['BasicRate'] = round(($value['PackingQty'] * $value['BasicRate']), 2);
					$ChlItemList[$key]['PurchRate'] = round(($value['PackingQty'] * $value['PurchRate']), 2);
					$ChlItemList[$key]['SaleRate'] = round(($value['PackingQty'] * $value['SaleRate']), 2);
				}
			}
			$SaleData['historytbl'] = $ChlItemList;
			echo json_encode($SaleData);
		}
		public function GetPOByVendor()
		{
			$VenId = $this->input->post('VenId');
			$data = $this->KirtiOneOrderModel->get_order_PO_ven_details($VenId);
			echo json_encode($data);
		}
		public function GetApprovedChallanByVendor()
		{
			$VenId = $this->input->post('VenId');
			$data = $this->KirtiOneOrderModel->GetApprovedChallanByVendor($VenId);
			echo json_encode($data);
		}
		public function load_data_for_delivery_order()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')           
			);
			$SaleList = $this->KirtiOneOrderModel->load_data_for_delivery_order($data);
			$html = "";
			$TotalSaleAmt = 0;
			$TotalDiscAmt = 0;       
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;        
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($SaleList as $key=>$val)
			{
				if($val['OrderStatus'] == "A")
				{ 
					$OrderStatus = "Approved";	
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
					}else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
				}
				$url = admin_url()."KirtiOneOrder/AddEditDeliveryOrder/".$val["ChallanID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';  
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';			
				$html .= '<td style="text-align:center;">'.$val["ChallanID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["ChallanDate"],0,10)).'</td>';
				$html .= '<td style="text-align:center;">'.$val["OrderID"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["company"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>'; 						
				$html .= '<td style="text-align:right;">'.$val["SaleAmt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["DiscAmt"].'</td>';          
				$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';           
				$html .= '<td style="text-align:right;">'.$val["BillAmt"].'</td>';
				$html .= '</tr>';
				$TotalSaleAmt += $val["SaleAmt"];
				$TotalDiscAmt += $val["DiscAmt"];            
				$TotalCgstAmt += $val["cgstamt"];
				$TotalSgstAmt += $val["sgstamt"];
				$TotalIgstAmt += $val["igstamt"];            
				$TotalInvAmt += $val["BillAmt"];
			}
			$html .= '<tr>';
			$html .= '<td colspan="5" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSaleAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';       
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function load_data_for_delivery_invoice()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')           
			);
			$SaleList = $this->KirtiOneOrderModel->load_data_for_delivery_invoice($data);
			$html = "";
			$TotalSaleAmt = 0;
			$TotalDiscAmt = 0;       
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;        
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($SaleList as $key=>$val)
			{
				if($val['OrderStatus'] == "A")
				{ 
					$OrderStatus = "Approved";	
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
					}else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
				}
				$url = admin_url()."KirtiOneOrder/AddEditDeliveryInvoice/".$val["ChallanID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';  
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';			
				$html .= '<td style="text-align:center;">'.$val["ChallanID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["ChallanDate"],0,10)).'</td>';
				$html .= '<td style="text-align:center;">'.$val["OrderID"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["company"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>'; 						
				$html .= '<td style="text-align:right;">'.$val["SaleAmt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["DiscAmt"].'</td>';          
				$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';           
				$html .= '<td style="text-align:right;">'.$val["BillAmt"].'</td>';
				$html .= '</tr>';
				$TotalSaleAmt += $val["SaleAmt"];
				$TotalDiscAmt += $val["DiscAmt"];            
				$TotalCgstAmt += $val["cgstamt"];
				$TotalSgstAmt += $val["sgstamt"];
				$TotalIgstAmt += $val["igstamt"];            
				$TotalInvAmt += $val["BillAmt"];
			}
			$html .= '<tr>';
			$html .= '<td colspan="5" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSaleAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';       
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function ApproveChallan()
		{
			if (!has_permission_new('DeliveryOrder', '', 'view')) {
				access_denied('purchase order');
			}   		
			if ($this->input->post()) {
				$ChallanID = $this->input->post('ApproveChallanID');				
				if (!empty($ChallanID)) {
					$data_array = array(   
					'OrderStatus' => 'A',       
					'Lupdate'=>date('Y-m-d H:i:s'),
					'UserID2'=>$this->session->userdata('username')
					);
					$this->db->where('ChallanID',$ChallanID);
					if ($this->db->update(db_prefix() . 'K1challanmaster',$data_array)) {
						set_alert('success', _l('Approved Successfully', _l('pur_order')));
						redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder/').$ChallanID);
						}else{
						set_alert('warning', _l('Something Went Wrong', _l('pur_order')));
						redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder'));
					}
					}else{
					set_alert('warning', _l('Something Went Wrong', _l('pur_order')));
					redirect(admin_url('KirtiOneOrder/AddEditDeliveryOrder'));
				}
			}
		}
		public function AddEditDeliveryInvoice($ChlNumber = '')
		{
			if (!has_permission_new('DeliveryInvoice', '', 'view')) {
				access_denied('purchase order');
			}   		
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();	
				if (!has_permission_new('DeliveryInvoice', '', 'create') && !has_permission_new('DeliveryInvoice', '', 'edit')) {
					access_denied('DeliveryInvoice');
				}
				$id = $this->KirtiOneOrderModel->Update_DeliveryInv_KirtiOne($pur_order_data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('pur_order')));
					redirect(admin_url('KirtiOneOrder/AddEditDeliveryInvoice'));
				}
			}
			if ($ChlNumber == '') {
				$title = "Create Delivery Invoice";
			}else{
				$challan_details = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($ChlNumber);		
				$data['challan_details'] = $challan_details;	
				$ChlItemList = $this->KirtiOneOrderModel->GetDeliveryInvoiceItemList($ChlNumber);
				$data['chl_item_detail'] = json_encode($ChlItemList);           
				$title = "Edit  Delivery Invoice";
			}
			$states = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblxx_statelist");
			$data['states'] = $states;
			$citylist = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblxx_citylist");
			$data['citylist'] = $citylist;
			$talukalist = $this->KirtiOneOrderModel->get_all_table_data($tablename="tblTalukaMaster");
			$data['talukalist'] = $talukalist;
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")'; 
			$EffectOn = $this->KirtiOneOrderModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn; 
			$trader_list = $this->KirtiOneOrderModel->PendingSaleInvoiceVendors();
			$PartyList = $this->KirtiOneOrderModel->GetPartyList();
			$data['trader_list'] = $trader_list;
			$data['party_list'] = $PartyList;
			$data['item_code'] = $this->KirtiOneOrderModel->get_items_code();	
			$data['statelist'] = $this->KirtiOneOrderModel->getstatelist();
			$ActGroupID = 10011;
    		$wh_effect = '(ActGroupID="'.$ActGroupID.'")'; 
    		$DirectIncome = $this->KirtiOneOrderModel->get_all_data($tablename="tblclients",$wh_effect);
    		$data['DirectIncome'] = $DirectIncome; 
			$data['company_detail'] = $this->KirtiOneOrderModel->get_company_detail();
			$this->load->view('admin/KirtiOneOrder/AddEditDeliveryInvoice',$data);
		}
		public function generateEwayBill()
		{
		    try{
    		    $postData = $this->input->post();
    		    $ChallanID = $postData['ChallanID'];
    			$fy = $this->session->userdata('finacial_year');
    			$selected_company = $this->session->userdata('root_company');
    			$company_details = $this->Challan_model->get_company_detail($selected_company);
    			// Step 1: Authentication - Get AuthToken
    			/* $authHeaders = [
        			'email'         => $company_details->eway_email,
        			'username'      => $company_details->eway_username,
        			'password'      => $company_details->eway_password,
        			'ip_address'    => $_SERVER['REMOTE_ADDR'],
        			'client_id'     => $company_details->eway_client_id,
        			'client_secret' => $company_details->eway_client_secret,
        			'gstin'         => $company_details->eway_gstin,
    			]; */
    			$authHeaders = [
        			'email'         => 'ajinkya.bhalerao@globalinfocloud.com',
        			'username'      => 'BVMGSP',
        			'password'      => 'Wbooks@0142',
        			'ip_address'    => $_SERVER['REMOTE_ADDR'],
        			'client_id'     => 'EWBS9b6a21f2-c644-48aa-99c9-0233e73de7ae',
        			'client_secret' => 'EWBS2d477cc9-a452-4044-9a45-5cfd93e5f88b',
        			'gstin'         => '29AAGCB1286Q000',
    			];
    			$queryParams = http_build_query([
        			'email'    => $authHeaders['email'],
        			'username' => $authHeaders['username'],
        			'password' => $authHeaders['password']
    			]);
    			// $authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;
    			$authURL = "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/authenticate?" . $queryParams;
    			$ch = curl_init();
    			curl_setopt_array($ch, [
    			CURLOPT_URL            => $authURL,
    			CURLOPT_RETURNTRANSFER => true,
    			CURLOPT_HTTPHEADER     => [
                    "email: {$authHeaders['email']}",
                    "username: {$authHeaders['username']}",
                    "password: {$authHeaders['password']}",
                    "ip_address: {$authHeaders['ip_address']}",
                    "client_id: {$authHeaders['client_id']}",
                    "client_secret: {$authHeaders['client_secret']}",
                    "gstin: {$authHeaders['gstin']}"
    			],
    			]);
    			$response = curl_exec($ch);
    			curl_close($ch);
    			$authRes = json_decode($response, true);
    			if ($authRes['status_cd'] == 0) {
    				echo json_encode(['Status' => 'error', 'ErrorMsg' => 'Auth failedd', 'response' => $authRes]);
    				return;
    			}
    			$AuthToken = $authRes['data']['AuthToken'];
    			$return = false;
    			$ErrorMsg = ''; 
    			$SuccessMsg = '';
    			$Salesdata = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($ChallanID);
    			$items = $this->KirtiOneOrderModel->GetDeliveryInvoiceItemList($ChallanID);
                // $Salesdata = $this->Challan_model->GetTaxableNonTaxableTransaction($postData);
    			$CenterDetails = $this->Challan_model->fetchCenterDetails($Salesdata->CenterID);
    		    $ToCenterDetails = $this->KirtiOneOrderModel->getShippingDetails($Salesdata->ShippingID ?? 3);
    			$CenterDetails->GSTNo = $authHeaders['gstin'];
    			$ToCenterDetails->GSTNo = 'URP';
    // 			echo '<pre>'; print_r($Salesdata); die;
    			if(empty($Salesdata)){
    			    $return = false;
    			    $Message = "Challan Details not found. please reload page and try again";
    			}elseif(!empty($Salesdata->ewaybill_no)){
    			    $return = false;
    			    $Message = "E-way Bill already generated";
    			}elseif(empty($CenterDetails)){
    			    $return = false;
    			    $Message = "Center Details not available please connect to admin";
    			}elseif(empty($CenterDetails->GSTNo)){
    			    $return = false;
    			    $Message = "Center GST No not available please update center details";
    			}elseif(empty($CenterDetails->CenterName)){
    			    $return = false;
    			    $Message = "Center Name not available please update center details";
    			}elseif(empty($CenterDetails->address)){
    			    $return = false;
    			    $Message = "Center address not available please update center details";
    			}elseif(empty($CenterDetails->city_name)){
    			    $return = false;
    			    $Message = "Center city name not available please update center details";
    			}elseif(empty($CenterDetails->statecode)){
    			    $return = false;
    			    $Message = "Center state name not available please update center details";
    			}elseif(empty($CenterDetails->pincode)){
    			    $return = false;
    			    $Message = "Center pincode not available please update center details";
    			}elseif(empty($ToCenterDetails)){
    			    $return = false;
    			    $Message = "Farmer Location not available please connect to admin";
    			}elseif(empty($ToCenterDetails->GSTNo)){
    			    $return = false;
    			    $Message = "Farmer GST No not available please update center details";
    			}elseif(empty($ToCenterDetails->shipping_label)){
    			    $return = false;
    			    $Message = "Farmer Name not available please update center details";
    			}elseif(empty($ToCenterDetails->shipping_label)){
    			    $return = false;
    			    $Message = "Farmer address not available please update center details";
    			}elseif(empty($ToCenterDetails->city_name)){
    			    $return = false;
    			    $Message = "Farmer city name not available please update center details";
    			}elseif(empty($ToCenterDetails->statecode)){
    			    $return = false;
    			    $Message = "Farmer state name not available please update center details";
    			}elseif(empty($ToCenterDetails->Pincode)){
    			    $return = false;
    			    $Message = "Farmer pincode not available please update center details";
    			}elseif(empty($items)){
    			    $return = false;
    			    $Message = "Items Details not available please connect to admin";
    			}else{
    			    $SalesID = $Salesdata->SalesID;
    				$Ph = $Salesdata->phonenumber;
    				// If party is unregistered, GSTIN should be 'URP', shipToGSTIN must be blank
    				$toGstin = (empty($Salesdata->gstno) || strtoupper($Salesdata->gstno) == 'URP') ? 'URP' : $Salesdata->gstno;
    				$isUnregistered = ($toGstin == '' || $toGstin == 'URP');
    				$ewayData = [
    					"supplyType"        => "O",
    					"subSupplyType"     => "1",
    					"subSupplyDesc"     => " ",
    					"docType"           => "INV",
    					"docNo"             => $Salesdata->ChallanID,
    					"docDate"           => date("d/m/Y"),
    					"fromGstin"         => $CenterDetails->GSTNo,
    					"fromTrdName"       => $CenterDetails->CenterName,
    					"fromAddr1"         => $CenterDetails->address,
    					"fromAddr2"         => " ",
    					"fromPlace"         => $CenterDetails->city_name,
    					"fromPincode"       => (int) $CenterDetails->pincode,
    					"actFromStateCode"  => (int) sprintf('%02d', $CenterDetails->statecode),
    					"fromStateCode"     => (int) sprintf('%02d', $CenterDetails->statecode),
    					"toGstin"           => $ToCenterDetails->GSTNo,
    					"toTrdName"         => $ToCenterDetails->shipping_label,
    					"toAddr1"           => $ToCenterDetails->shipping_label,
    					"toAddr2"           => " ",
    					"toPlace"           => $ToCenterDetails->city_name,
    					"toPincode"         => (int) $ToCenterDetails->Pincode,
    					"actToStateCode"    => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"toStateCode"       => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"transactionType"   => 4,
    					"otherValue"        => 0,
    					"totalValue"        => floatval($Salesdata->SaleAmt - $Salesdata->Discamt),
    					"cgstValue"         => floatval($Salesdata->cgstamt),
    					"sgstValue"         => floatval($Salesdata->sgstamt),
    					"igstValue"         => floatval($Salesdata->igstamt),
    					"cessValue"         => 0,
    					"cessNonAdvolValue" => 0,
    					"totInvValue"       => floatval($Salesdata->BillAmt),
    					"transporterId"     => "05AAACG0904A1ZL",
    					"transporterName"   => "",
    					"transDocNo"        => "12",
    					"transMode"         => "1",
    					"transDistance"     => "0",// hard code value
    					"transDocDate"      => date("d/m/Y"),
    					"vehicleNo"         => $Salesdata->vehicleno,
    					"vehicleType"       => "R",
    					"itemList"          => []
    				];
    				$sl = 1;
    				foreach ($items as $item) {
    					$ewayData['itemList'][] = [
    					"productName"   => $item['ProductName'],
    					"productDesc"   => $item['ProductName'],
    					"hsnCode"       => $item['hsn_code'],
    					"quantity"      => floatval($item['BilledQty']),
    					"qtyUnit"       => 'PCS',// $item['unit']
    					"cgstRate"      => ($OrderDetails->cgstamt == 0) ? 0 : floatval($item['cgst']),
    					"sgstRate"      => ($OrderDetails->sgstamt == 0) ? 0 : floatval($item['sgst']),
    					"igstRate"      => ($OrderDetails->igstamt == 0) ? 0 : floatval($item['igst']),
    					"cessRate"      => 0.00,
    					"taxableAmount"=> floatval($item['ChallanAmt'])
    					];
    					$sl++;
    				}
    				// echo '<pre>'; print_r($ewayData); die;
    				// Step 3: Send E-Way Bill request
    				$ch = curl_init();
    				curl_setopt_array($ch, [
    				// CURLOPT_URL            => "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),
    				CURLOPT_URL            => "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),
    				CURLOPT_RETURNTRANSFER => true,
    				CURLOPT_POST           => true,
    				CURLOPT_POSTFIELDS     => json_encode($ewayData),
    				CURLOPT_HTTPHEADER     => [
            				"Content-Type: application/json",
            				"email: {$authHeaders['email']}",
            				"ip_address: {$authHeaders['ip_address']}",
            				"client_id: {$authHeaders['client_id']}",
            				"client_secret: {$authHeaders['client_secret']}",
            				"username: {$authHeaders['username']}",
            				"gstin: {$authHeaders['gstin']}"
    				    ]
    				]);
    				$ewayRes = curl_exec($ch);
    				curl_close($ch);
    				$ewayResData = json_decode($ewayRes, true);
    				if (isset($ewayResData['data']['ewayBillNo'])) {
    				    // Save to DB
    					$this->db->where('ChallanID', $ChallanID);
    					$this->db->update(db_prefix().'K1salesmaster', [
        					'ewaybill_cancelled' => null,
        					'EwayCancelRemark' => null,
        					'ewaybill_no' => $ewayResData['data']['ewayBillNo'],
        					'ewaybill_date' => $ewayResData['data']['ewayBillDate'],
        					'ewaybill_valid_upto' => $ewayResData['data']['validUpto']
    					]);
    					$return = true;
    					$Message .= "E-Way Bill (".$ewayResData['data']['ewayBillNo'].") Is Generated Successfully for ChallanID ".$ChallanID." . ";
    				}else{
    				    $return = false;
    				    $Message = $ewayResData['error']['message'];
    				}
    			}
    			$Result['Status'] = $return;
    			$Result['Message'] = $Message;
    			$Result['ewayResData'] = $ewayResData;
    			echo json_encode($Result);
		    } catch (Exception $e) {
                echo $e->getMessage();
            }
		}
		public function generateOrderEwayBill()
		{
		    try{
    		    $postData = $this->input->post();
    		    $OrderID = $postData['OrderID'];
    			$fy = $this->session->userdata('finacial_year');
    			$selected_company = $this->session->userdata('root_company');
    			$company_details = $this->Challan_model->get_company_detail($selected_company);
    			$OrderDetails = $this->KirtiOneOrderModel->GetDirectSaleOrderDetails($OrderID);
				$SaleItemList = $this->KirtiOneOrderModel->GetSaleOrderItemList_New($OrderID);
				$products = $this->KirtiOneOrderModel->get_items_code_by_categorytype($OrderDetails->CategoryType);
                // echo '<pre>';print_r($OrderDetails); die;
    			// Step 1: Authentication - Get AuthToken
    			/* $authHeaders = [
        			'email'         => $company_details->eway_email,
        			'username'      => $company_details->eway_username,
        			'password'      => $company_details->eway_password,
        			'ip_address'    => $_SERVER['REMOTE_ADDR'],
        			'client_id'     => $company_details->eway_client_id,
        			'client_secret' => $company_details->eway_client_secret,
        			'gstin'         => $company_details->eway_gstin,
    			]; */
    			$authHeaders = [
        			'email'         => 'ajinkya.bhalerao@globalinfocloud.com',
        			'username'      => 'BVMGSP',
        			'password'      => 'Wbooks@0142',
        			'ip_address'    => $_SERVER['REMOTE_ADDR'],
        			'client_id'     => 'EWBS9b6a21f2-c644-48aa-99c9-0233e73de7ae',
        			'client_secret' => 'EWBS2d477cc9-a452-4044-9a45-5cfd93e5f88b',
        			'gstin'         => '29AAGCB1286Q000',
    			];
    			$queryParams = http_build_query([
        			'email'    => $authHeaders['email'],
        			'username' => $authHeaders['username'],
        			'password' => $authHeaders['password']
    			]);
    			// $authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;
    			$authURL = "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/authenticate?" . $queryParams;
    			$ch = curl_init();
    			curl_setopt_array($ch, [
    			CURLOPT_URL            => $authURL,
    			CURLOPT_RETURNTRANSFER => true,
    			CURLOPT_HTTPHEADER     => [
                    "email: {$authHeaders['email']}",
                    "username: {$authHeaders['username']}",
                    "password: {$authHeaders['password']}",
                    "ip_address: {$authHeaders['ip_address']}",
                    "client_id: {$authHeaders['client_id']}",
                    "client_secret: {$authHeaders['client_secret']}",
                    "gstin: {$authHeaders['gstin']}"
    			],
    			]);
    			$response = curl_exec($ch);
    			curl_close($ch);
    			$authRes = json_decode($response, true);
    			if ($authRes['status_cd'] == 0) {
    				echo json_encode(['Status' => 'error', 'ErrorMsg' => 'Auth failedd', 'response' => $authRes]);
    				return;
    			}
    			$AuthToken = $authRes['data']['AuthToken'];
    			$return = false;
    			$ErrorMsg = ''; 
    			$SuccessMsg = '';
    			$CenterDetails = $this->Challan_model->fetchCenterDetails($OrderDetails->CenterID);
    			$ToCenterDetails = $this->KirtiOneOrderModel->getShippingDetails($OrderDetails->ShippingID, $OrderDetails->AccountID);
    			$CenterDetails->GSTNo = $authHeaders['gstin'];
    			$ToCenterDetails->GSTNo = 'URP';
    		    //  echo '<pre>';print_r($ToCenterDetails); die;
    			if(empty($OrderDetails)){
    			    $return = false;
    			    $Message = "Details not found. please reload page and try again";
    			}elseif(!empty($OrderDetails->EwayBillNo)){
    			    $return = false;
    			    $Message = "E-way Bill already generated";
    			}elseif(empty($CenterDetails)){
    			    $return = false;
    			    $Message = "Center Details not available please connect to admin";
    			}elseif(empty($CenterDetails->GSTNo)){
    			    $return = false;
    			    $Message = "Center GST No not available please update center details";
    			}elseif(empty($CenterDetails->CenterName)){
    			    $return = false;
    			    $Message = "Center Name not available please update center details";
    			}elseif(empty($CenterDetails->address)){
    			    $return = false;
    			    $Message = "Center address not available please update center details";
    			}elseif(empty($CenterDetails->city_name)){
    			    $return = false;
    			    $Message = "Center city name not available please update center details";
    			}elseif(empty($CenterDetails->statecode)){
    			    $return = false;
    			    $Message = "Center state name not available please update center details";
    			}elseif(empty($CenterDetails->pincode)){
    			    $return = false;
    			    $Message = "Center pincode not available please update center details";
    			}elseif(empty($ToCenterDetails)){
    			    $return = false;
    			    $Message = "Shipping Location not available please connect to admin";
    			}elseif(empty($ToCenterDetails->GSTNo)){
    			    $return = false;
    			    $Message = "Shipping GST No not available please update details";
    			}elseif(empty($ToCenterDetails->shipping_label)){
    			    $return = false;
    			    $Message = "Shipping Name not available please update details";
    			}elseif(empty($ToCenterDetails->shipping_label)){
    			    $return = false;
    			    $Message = "Shipping address not available please update details";
    			}elseif(empty($ToCenterDetails->city_name)){
    			    $return = false;
    			    $Message = "Shipping city name not available please update details";
    			}elseif(empty($ToCenterDetails->statecode)){
    			    $return = false;
    			    $Message = "Shipping state name not available please update details";
    			}elseif(empty($ToCenterDetails->Pincode)){
    			    $return = false;
    			    $Message = "Shipping pincode not available please update details";
    			}elseif(empty($SaleItemList)){
    			    $return = false;
    			    $Message = "Items Details not available please connect to admin";
    			}else{
    				$ewayData = [
    					"supplyType"        => "O",
    					"subSupplyType"     => "1",
    					"subSupplyDesc"     => "Sales Order Delivery",
    					"docType"           => "INV",
    					"docNo"             => $OrderDetails->OrderID,
    					"docDate"           => date("d/m/Y"),
    					"fromGstin"         => $CenterDetails->GSTNo,
    					"fromTrdName"       => $CenterDetails->CenterName,
    					"fromAddr1"         => $CenterDetails->address,
    					"fromAddr2"         => " ",
    					"fromPlace"         => $CenterDetails->city_name,
    					"fromPincode"       => (int) $CenterDetails->pincode,
    					"actFromStateCode"  => (int) sprintf('%02d', $CenterDetails->statecode),
    					"fromStateCode"     => (int) sprintf('%02d', $CenterDetails->statecode),
    					"toGstin"           => $ToCenterDetails->GSTNo,
    					"toTrdName"         => $ToCenterDetails->shipping_label,
    					"toAddr1"           => $ToCenterDetails->shipping_label,
    					"toAddr2"           => " ",
    					"toPlace"           => $ToCenterDetails->city_name,
    					"toPincode"         => (int) $ToCenterDetails->Pincode,
    					"actToStateCode"    => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"toStateCode"       => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"transactionType"   => 4,
    					"otherValue"        => 0,
    					"totalValue"        => floatval($OrderDetails->SaleAmt - $OrderDetails->Discamt),
    					"cgstValue"         => floatval($OrderDetails->cgstamt),
    					"sgstValue"         => floatval($OrderDetails->sgstamt),
    					"igstValue"         => floatval($OrderDetails->igstamt),
    					"cessValue"         => 0,
    					"cessNonAdvolValue" => 0,
    					"totInvValue"       => floatval($OrderDetails->BillAmt),
    					"transporterId"     => "05AAACG0904A1ZL",
    					"transporterName"   => "",
    					"transDocNo"        => "12",
    					"transMode"         => "1",
    					"transDistance"     => "0",// hard code value
    					"transDocDate"      => date("d/m/Y"),
    					"vehicleNo"         => $OrderDetails->VehicleNo ?? 'MH12TS1234',
    					"vehicleType"       => "R",
    					"itemList"          => []
    				];
    				$sl = 1;
    				foreach ($SaleItemList as $item) {
    					$ewayData['itemList'][] = [
    					"productName"   => $item['ProductName'],
    					"productDesc"   => $item['ProductName'],
    					"hsnCode"       => $item['hsn_code'],
    					"quantity"      => floatval($item['PackingQty']),
    					"qtyUnit"       => substr($item['Measuredin'], 0, 3),
    					"cgstRate"      => ($OrderDetails->cgstamt == 0) ? 0 : floatval($item['cgst']),
    					"sgstRate"      => ($OrderDetails->sgstamt == 0) ? 0 : floatval($item['sgst']),
    					"igstRate"      => ($OrderDetails->igstamt == 0) ? 0 : floatval($item['igst']),
    					"cessRate"      => 0.00,
    					"taxableAmount"=> floatval($item['OrderAmt'])
    					];
    					$sl++;
    				}
                    // echo '<pre>'; print_r($ewayData); die;
    				// Step 3: Send E-Way Bill request
    				$ch = curl_init();
    				curl_setopt_array($ch, [
        				// CURLOPT_URL            => "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),
        				CURLOPT_URL            => "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),
        				CURLOPT_RETURNTRANSFER => true,
        				CURLOPT_POST           => true,
        				CURLOPT_POSTFIELDS     => json_encode($ewayData),
        				CURLOPT_HTTPHEADER     => [
            				"Content-Type: application/json",
            				"email: {$authHeaders['email']}",
            				"ip_address: {$authHeaders['ip_address']}",
            				"client_id: {$authHeaders['client_id']}",
            				"client_secret: {$authHeaders['client_secret']}",
            				"username: {$authHeaders['username']}",
            				"gstin: {$authHeaders['gstin']}"
        				]
    				]);
    				$ewayRes = curl_exec($ch);
    				curl_close($ch);
    				$ewayResData = json_decode($ewayRes, true);
    				if (isset($ewayResData['data']['ewayBillNo'])) {
    				    // Save to DB
    					$this->db->where('OrderID', $OrderDetails->OrderID);
    					$this->db->where('PlantID', $OrderDetails->PlantID);
    					$this->db->update(db_prefix().'K1salesmaster', [
        					'ewaybill_cancelled' => null,
        					'EwayCancelRemark' => null,
        					'ewaybill_no' => $ewayResData['data']['ewayBillNo'],
        					'ewaybill_date' => $ewayResData['data']['ewayBillDate'],
        					'ewaybill_valid_upto' => $ewayResData['data']['validUpto']
    					]);
    					$return = true;
    					$Message .= "E-Way Bill (".$ewayResData['data']['ewayBillNo'].") Is Generated Successfully for OrderID ".$OrderDetails->OrderID." . ";
    				}else{
    				    $return = false;
    				    $Message = $ewayResData['error']['message'];
    				}
    			}
    			$Result['Status'] = $return;
    			$Result['Message'] = $Message;
    			$Result['ewayResData'] = $ewayResData;
    			echo json_encode($Result);
		    } catch (Exception $e) {
                echo $e->getMessage();
            }
		}
		public function GetTaxableTransaction(){
			// POST data
			$postData = $this->input->post();
			// Get data
			$Salesdata = $this->KirtiOneOrderModel->GetTaxableTransaction($postData);
			echo json_encode($Salesdata);
		}
		public function invoicepdf($id)
		{
			if (!$id) {
				redirect(admin_url('ItemMaster/ItemOrderDetails'));
			}
			if (!has_permission_new('challan_list', '', 'view')) {
                access_denied('Invoices');
			}
			$invoice        = $this->KirtiOneOrderModel->GetDeliveryChallanDetails($id);
			// print_r($invoice);
			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);
			//$invoice_number = format_invoice_number($invoice->id);
			try {
				$pdf = Deliveryinvoice_pdf($invoice);
				} catch (Exception $e) {
				$message = $e->getMessage();
				echo $message;
				if (strpos($message, 'Unable to get the size of the image') !== false) {
					show_pdf_unable_to_get_image_size_error();
				}
				die;
			}
			$type = 'D';
			if ($this->input->get('output_type')) {
				$type = $this->input->get('output_type');
			}
			if ($this->input->get('print')) {
				$type = 'I';
			}
			$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);
		}
		public function GetItemListData()
		{		
			$CategoryType = $this->input->post('CategoryType');
			$CenterID = $this->input->post('CenterID');
			$trader_list = $this->KirtiOneOrderModel->GetCategoryWiseItems($CategoryType,$CenterID);		
			echo json_encode($trader_list);
		}

		public function fix_wrong_receipt_voucher_ids()
		{
				$selected_company = $this->session->userdata('selected_company');

				$sql = "
						SELECT
								PlantID,
								FY,
								DATE(Transdate) AS EntryDate,
								Transdate,
								Amount,
								COUNT(*) AS EntryCount,
								GROUP_CONCAT(id ORDER BY id) AS LedgerIDs,
								GROUP_CONCAT(VoucherID ORDER BY id) AS OldVoucherIDs
						FROM tblaccountledger
						WHERE FY = '26'
							AND PassedFrom = 'RECEIPTS'
							AND VoucherID REGEXP '^[0-9]+$'
						GROUP BY
								PlantID,
								DATE(Transdate),
								Transdate,
								Amount
						HAVING COUNT(*) = 2
						ORDER BY
								EntryDate,
								Transdate,
								MIN(id)
				";

				$groups = $this->db->query($sql)->result();

				if (empty($groups)) {

						echo json_encode([
								'status'  => false,
								'message' => 'No wrong receipt records found.'
						]);
						return;
				}

				$this->db->trans_begin();

				$debugData = [];

				$totalGroups  = 0;
				$totalRecords = 0;

				try {

						foreach ($groups as $group) {

								$ledgerIds = explode(',', $group->LedgerIDs);
								$oldVoucherIds = explode(',', $group->OldVoucherIDs);

								// Safety check - every group must contain exactly 2 records
								if (count($ledgerIds) != 2) {
										continue;
								}

								/*
								* Generate new VoucherID
								*/
								$newVoucherID = $this->KirtiOneOrderModel->generateNextVoucherIDNew(
										$group->Transdate,
										$group->PlantID,
										'RECEIPTS'
								);

								if (empty($newVoucherID)) {

										throw new Exception(
												'VoucherID generation failed for Ledger IDs: ' .
												$group->LedgerIDs
										);
								}

								/*
								* Debug information BEFORE update
								*/
								$debugRow = [
										'PlantID'       => $group->PlantID,
										'FY'            => $group->FY,
										'Transdate'     => $group->Transdate,
										'Amount'        => $group->Amount,
										'LedgerIDs'     => $group->LedgerIDs,
										'OldVoucherID1'=> isset($oldVoucherIds[0]) ? $oldVoucherIds[0] : '',
										'OldVoucherID2'=> isset($oldVoucherIds[1]) ? $oldVoucherIds[1] : '',
										'NewVoucherID'  => $newVoucherID,
										'UpdatedRows'   => 0
								];

								/*
								* Update both records
								*/
								$this->db
										->where_in('id', $ledgerIds)
										->where('FY', '26')
										->where('PassedFrom', 'RECEIPTS')
										->where("VoucherID REGEXP '^[0-9]+$'", null, false)
										->update('tblaccountledger', [
												'VoucherID' => $newVoucherID
										]);

								$affectedRows = $this->db->affected_rows();

								$debugRow['UpdatedRows'] = $affectedRows;

								/*
								* Make sure exactly 2 rows were updated
								*/
								if ($affectedRows != 2) {

										throw new Exception(
												'Expected 2 rows but updated ' .
												$affectedRows .
												' rows. Ledger IDs: ' .
												$group->LedgerIDs .
												' | New VoucherID: ' .
												$newVoucherID
										);
								}

								/*
								* Add to debug result
								*/
								$debugData[] = $debugRow;

								$totalGroups++;
								$totalRecords += $affectedRows;
						}

						/*
						* Check transaction
						*/
						if ($this->db->trans_status() === FALSE) {
								throw new Exception('Database transaction failed.');
						}

						$this->db->trans_commit();

						echo json_encode([
								'status'        => true,
								'message'       => 'Receipt VoucherID correction completed.',
								'total_groups'  => $totalGroups,
								'total_records' => $totalRecords,
								'debug_data'    => $debugData
						]);

				} catch (Exception $e) {

						$this->db->trans_rollback();
						echo json_encode([
								'status'     => false,
								'message'    => $e->getMessage(),
								'debug_data' => $debugData
						]);
				}
		}

		public function fix_purchase_order_history()
		{
			$this->db->select('*');
			$this->db->from('tblK1history');
			$this->db->where('TType', 'P');
			$this->db->where('TType2', 'Purchase');
			$this->db->where('TransID IS NOT NULL', null, false);
			$this->db->where('BillID <> TransID', null, false);

			$query = $this->db->get();

			if (!$query) {
				echo '<pre>';
				print_r([
					'status' => false,
					'message' => 'Failed to fetch source records.'
				]);
				echo '</pre>';
				return;
			}

			$records = $query->result_array();

			if (empty($records)) {
				echo '<pre>';
				print_r([
					'status' => false,
					'message' => 'No records found.'
				]);
				echo '</pre>';
				return;
			}

			$totalRows = count($records);
			
			$this->db->trans_begin();
			$insertedRows = 0;
			try {
				foreach ($records as $row) {
					unset($row['id']);
					$row['TType']  = 'P';
					$row['TType2'] = 'Purchase Order';
					$row['TransID'] = $row['BillID'];
					$this->db->insert('tblK1history', $row);

					if ($this->db->affected_rows() != 1) {
						throw new Exception(
							'Insert failed for BillID: ' . $row['BillID']
						);
					}

					$insertedRows++;
				}
				
				if ($this->db->trans_status() === FALSE) {
					throw new Exception(
						'Database transaction failed.'
					);
				}

				$this->db->trans_commit();

				echo '<pre>';
				print_r([
					'status' => true,
					'message' => 'Purchase Order history records copied successfully.',
					'source_rows' => $totalRows,
					'inserted_rows' => $insertedRows,
					'changes' => [
						'TType' => 'P',
						'TType2' => 'Purchase Order',
						'TransID' => 'BillID'
					]
				]);
				echo '</pre>';

			} catch (Exception $e) {
				$this->db->trans_rollback();
				echo '<pre>';
				print_r([
					'status' => false,
					'message' => $e->getMessage(),
					'inserted_before_error' => $insertedRows
				]);
				echo '</pre>';
			}
		}

		public function fix_negative_stock_batch_wise()
		{
				$this->db->trans_begin();

				try {

						$FY = '26';

						/*
						* ------------------------------------------------------------
						* STEP 1:
						* Find all negative stock batches.
						* ------------------------------------------------------------
						*/
						$negative_sql = "
								SELECT
										s.ItemID,
										s.CenterID,
										s.BatchNo,
										s.OpeningQty,

										COALESCE(h.InwardQty, 0) AS InwardQty,
										COALESCE(h.PurchQty, 0) AS PurchQty,
										COALESCE(h.PurchRtnQty, 0) AS PurchRtnQty,
										COALESCE(h.SaleQty, 0) AS SaleQty,
										COALESCE(h.SaleRtnQty, 0) AS SaleRtnQty,
										COALESCE(h.InQty, 0) AS InQty,
										COALESCE(h.OutQty, 0) AS OutQty,
										COALESCE(h.AdjQty, 0) AS AdjQty,
										COALESCE(h.PrdQty, 0) AS PrdQty,
										COALESCE(h.IssueQty, 0) AS IssueQty,

										(
												s.OpeningQty
												+ COALESCE(h.InwardQty, 0)
												+ COALESCE(h.PurchQty, 0)
												- COALESCE(h.PurchRtnQty, 0)
												- COALESCE(h.SaleQty, 0)
												+ COALESCE(h.SaleRtnQty, 0)
												+ COALESCE(h.PrdQty, 0)
												- COALESCE(h.IssueQty, 0)
												- COALESCE(h.AdjQty, 0)
												+ COALESCE(h.InQty, 0)
												- COALESCE(h.OutQty, 0)
										) AS BalanceQty

								FROM
								(
										SELECT
												ItemID,
												CenterID,
												BatchNo,
												SUM(OQty) AS OpeningQty
										FROM tblK1stockmaster
										WHERE FY = ?
										GROUP BY ItemID, CenterID, BatchNo
								) s

								LEFT JOIN
								(
										SELECT
												ItemID,
												CenterID,
												BatchNo,

												SUM(
														CASE
																WHEN TType = 'I'
																AND TType2 = 'INWARD'
																THEN BilledQty ELSE 0
														END
												) AS InwardQty,

												SUM(
														CASE
																WHEN TType = 'P'
																AND TType2 = 'Purchase'
																THEN BilledQty ELSE 0
														END
												) AS PurchQty,

												SUM(
														CASE
																WHEN TType = 'P'
																AND TType2 = 'PURCHASE RETURN'
																THEN BilledQty ELSE 0
														END
												) AS PurchRtnQty,

												SUM(
														CASE
																WHEN TType = 'O'
																AND TType2 = 'SALE'
																THEN BilledQty ELSE 0
														END
												) AS SaleQty,

												SUM(
														CASE
																WHEN TType = 'SR'
																AND TType2 = 'FRESH RETURN'
																THEN BilledQty ELSE 0
														END
												) AS SaleRtnQty,

												SUM(
														CASE
																WHEN TType = 'T'
																AND TType2 = 'IN'
																THEN BilledQty ELSE 0
														END
												) AS InQty,

												SUM(
														CASE
																WHEN TType = 'T'
																AND TType2 = 'OUT'
																THEN BilledQty ELSE 0
														END
												) AS OutQty,

												SUM(
														CASE
																WHEN TType = 'X'
																THEN BilledQty ELSE 0
														END
												) AS AdjQty,

												SUM(
														CASE
																WHEN TType = 'I'
																AND TType2 = 'PRODUCTION'
																THEN BilledQty ELSE 0
														END
												) AS PrdQty,

												SUM(
														CASE
																WHEN TType = 'I'
																AND TType2 = 'ISSUE'
																THEN BilledQty ELSE 0
														END
												) AS IssueQty

										FROM tblK1history
										WHERE FY = ?
											AND OrderID IS NOT NULL
											AND BillID IS NOT NULL
											AND TransID IS NOT NULL

										GROUP BY ItemID, CenterID, BatchNo

								) h

										ON h.ItemID = s.ItemID
									AND h.CenterID = s.CenterID
									AND h.BatchNo = s.BatchNo

								HAVING BalanceQty < 0

								ORDER BY s.ItemID, s.CenterID, s.BatchNo
						";

						$negative_batches = $this->db->query(
								$negative_sql,
								array($FY, $FY)
						)->result_array();


						$processed = array();
						$unresolved = array();


						/*
						* ------------------------------------------------------------
						* STEP 2:
						* Process every negative batch.
						* ------------------------------------------------------------
						*/
						foreach ($negative_batches as $negative) {

								$item_id       = $negative['ItemID'];
								$center_id     = $negative['CenterID'];
								$negative_batch = $negative['BatchNo'];

								$required_qty = abs((float)$negative['BalanceQty']);

								if ($required_qty <= 0) {
										continue;
								}


								/*
								* --------------------------------------------------------
								* STEP 3:
								* Get SALE rows belonging to negative batch.
								*
								* IMPORTANT:
								* Process oldest transaction first.
								* --------------------------------------------------------
								*/
								$sale_rows = $this->db
										->select('id, BilledQty, BatchNo, BillID, TransID, TransDate')
										->from('tblK1history')
										->where('FY', $FY)
										->where('ItemID', $item_id)
										->where('CenterID', $center_id)
										->where('BatchNo', $negative_batch)
										->where('TType', 'O')
										->where('TType2', 'SALE')
										->where('OrderID IS NOT NULL', null, false)
										->where('BillID IS NOT NULL', null, false)
										->where('TransID IS NOT NULL', null, false)
										->order_by('TransDate', 'ASC')
										->order_by('id', 'ASC')
										->get()
										->result_array();


								if (empty($sale_rows)) {

										$unresolved[] = array(
												'ItemID'       => $item_id,
												'CenterID'     => $center_id,
												'BatchNo'      => $negative_batch,
												'RequiredQty'  => $required_qty,
												'Reason'       => 'Negative stock but SALE rows not found'
										);

										continue;
								}


								/*
								* --------------------------------------------------------
								* STEP 4:
								* Find positive stock batches.
								*
								* Stock available from:
								*
								* Opening
								* + Purchase
								* + Inward
								* + Transfer IN
								* + Production
								* - Purchase Return
								* - Sale
								* - Transfer OUT
								* - Issue
								* - Adjustment
								*
								* We use the same stock calculation as the negative
								* stock query.
								* --------------------------------------------------------
								*/
								$positive_sql = "
										SELECT
												s.ItemID,
												s.CenterID,
												s.BatchNo,
												s.OpeningQty,

												COALESCE(h.InwardQty, 0) AS InwardQty,
												COALESCE(h.PurchQty, 0) AS PurchQty,
												COALESCE(h.PurchRtnQty, 0) AS PurchRtnQty,
												COALESCE(h.SaleQty, 0) AS SaleQty,
												COALESCE(h.SaleRtnQty, 0) AS SaleRtnQty,
												COALESCE(h.InQty, 0) AS InQty,
												COALESCE(h.OutQty, 0) AS OutQty,
												COALESCE(h.AdjQty, 0) AS AdjQty,
												COALESCE(h.PrdQty, 0) AS PrdQty,
												COALESCE(h.IssueQty, 0) AS IssueQty,

												(
														s.OpeningQty
														+ COALESCE(h.InwardQty, 0)
														+ COALESCE(h.PurchQty, 0)
														- COALESCE(h.PurchRtnQty, 0)
														- COALESCE(h.SaleQty, 0)
														+ COALESCE(h.SaleRtnQty, 0)
														+ COALESCE(h.PrdQty, 0)
														- COALESCE(h.IssueQty, 0)
														- COALESCE(h.AdjQty, 0)
														+ COALESCE(h.InQty, 0)
														- COALESCE(h.OutQty, 0)
												) AS BalanceQty

										FROM
										(
												SELECT
														ItemID,
														CenterID,
														BatchNo,
														SUM(OQty) AS OpeningQty
												FROM tblK1stockmaster
												WHERE FY = ?
												GROUP BY ItemID, CenterID, BatchNo
										) s

										LEFT JOIN
										(
												SELECT
														ItemID,
														CenterID,
														BatchNo,

														SUM(
																CASE
																		WHEN TType = 'I'
																		AND TType2 = 'INWARD'
																		THEN BilledQty ELSE 0
																END
														) AS InwardQty,

														SUM(
																CASE
																		WHEN TType = 'P'
																		AND TType2 = 'Purchase'
																		THEN BilledQty ELSE 0
																END
														) AS PurchQty,

														SUM(
																CASE
																		WHEN TType = 'P'
																		AND TType2 = 'PURCHASE RETURN'
																		THEN BilledQty ELSE 0
																END
														) AS PurchRtnQty,

														SUM(
																CASE
																		WHEN TType = 'O'
																		AND TType2 = 'SALE'
																		THEN BilledQty ELSE 0
																END
														) AS SaleQty,

														SUM(
																CASE
																		WHEN TType = 'SR'
																		AND TType2 = 'FRESH RETURN'
																		THEN BilledQty ELSE 0
																END
														) AS SaleRtnQty,

														SUM(
																CASE
																		WHEN TType = 'T'
																		AND TType2 = 'IN'
																		THEN BilledQty ELSE 0
																END
														) AS InQty,

														SUM(
																CASE
																		WHEN TType = 'T'
																		AND TType2 = 'OUT'
																		THEN BilledQty ELSE 0
																END
														) AS OutQty,

														SUM(
																CASE
																		WHEN TType = 'X'
																		THEN BilledQty ELSE 0
																END
														) AS AdjQty,

														SUM(
																CASE
																		WHEN TType = 'I'
																		AND TType2 = 'PRODUCTION'
																		THEN BilledQty ELSE 0
																END
														) AS PrdQty,

														SUM(
																CASE
																		WHEN TType = 'I'
																		AND TType2 = 'ISSUE'
																		THEN BilledQty ELSE 0
																END
														) AS IssueQty

												FROM tblK1history
												WHERE FY = ?
													AND OrderID IS NOT NULL
													AND BillID IS NOT NULL
													AND TransID IS NOT NULL
												GROUP BY ItemID, CenterID, BatchNo

										) h

												ON h.ItemID = s.ItemID
											AND h.CenterID = s.CenterID
											AND h.BatchNo = s.BatchNo

										HAVING BalanceQty > 0

										ORDER BY BalanceQty DESC, s.BatchNo ASC
								";


								$positive_batches = $this->db->query(
										$positive_sql,
										array($FY, $FY)
								)->result_array();


								if (empty($positive_batches)) {

										$unresolved[] = array(
												'ItemID'       => $item_id,
												'CenterID'     => $center_id,
												'BatchNo'      => $negative_batch,
												'RequiredQty'  => $required_qty,
												'Reason'       => 'No positive stock batch available'
										);

										continue;
								}


								/*
								* --------------------------------------------------------
								* STEP 5:
								* Consume positive stock.
								* --------------------------------------------------------
								*/
								$remaining_qty = $required_qty;


								foreach ($positive_batches as $positive) {

										if ($remaining_qty <= 0) {
												break;
										}


										$positive_batch = $positive['BatchNo'];
										$available_qty  = (float)$positive['BalanceQty'];


										/*
										* Do not use the same batch.
										*/
										if ($positive_batch == $negative_batch) {
												continue;
										}


										if ($available_qty <= 0) {
												continue;
										}


										/*
										* How much can be moved from this positive batch?
										*/
										$transfer_qty = min(
												$remaining_qty,
												$available_qty
										);


										/*
										* ----------------------------------------------------
										* STEP 6:
										* Change SALE rows from negative batch
										* to positive batch.
										*
										* ONLY the required quantity is changed.
										* ----------------------------------------------------
										*/
										foreach ($sale_rows as $sale_row) {

												if ($transfer_qty <= 0) {
														break;
												}


												$sale_qty = (float)$sale_row['BilledQty'];

												if ($sale_qty <= 0) {
														continue;
												}


												/*
												* Full row can be moved.
												*/
												if ($sale_qty <= $transfer_qty) {

														$this->db
																->where('id', $sale_row['id'])
																->where('ItemID', $item_id)
																->where('CenterID', $center_id)
																->where('BatchNo', $negative_batch)
																->where('TType', 'O')
																->where('TType2', 'SALE')
																->update(
																		'tblK1history',
																		array(
																				'BatchNo' => $positive_batch
																		)
																);


														$transfer_qty -= $sale_qty;
														$remaining_qty -= $sale_qty;

												} else {

														/*
														* ------------------------------------------------
														* IMPORTANT:
														* We cannot simply change the complete row
														* because only part of BilledQty belongs to
														* the positive batch.
														*
														* Example:
														*
														* Sale row = 120
														* Required = 99
														*
														* Result:
														*
														* Original row = 21 KMS02
														* New row      = 99 PositiveBatch
														*
														* Therefore split the row.
														* ------------------------------------------------
														*/

														$move_qty = $transfer_qty;

														$remaining_sale_qty = $sale_qty - $move_qty;


														/*
														* Update original negative-batch row
														* with remaining quantity.
														*/
														$this->db
																->where('id', $sale_row['id'])
																->where('BatchNo', $negative_batch)
																->update(
																		'tblK1history',
																		array(
																				'BilledQty' => $remaining_sale_qty
																		)
																);


														/*
														* Copy complete row and change only:
														*
														* BilledQty
														* BatchNo
														*/
														$new_row = $sale_row;

														unset($new_row['id']);

														$new_row['BatchNo'] = $positive_batch;
														$new_row['BilledQty'] = $move_qty;


														$this->db->insert(
																'tblK1history',
																$new_row
														);


														$transfer_qty = 0;
														$remaining_qty = 0;
												}


												/*
												* If row was completely moved, continue to
												* next sale row.
												*/
										}


										/*
										* Continue with next positive batch if required.
										*/
								}


								/*
								* --------------------------------------------------------
								* STEP 7:
								* Final status of this negative batch.
								* --------------------------------------------------------
								*/
								if ($remaining_qty <= 0) {

										$processed[] = array(
												'ItemID'       => $item_id,
												'CenterID'     => $center_id,
												'OldBatchNo'   => $negative_batch,
												'ProcessedQty' => $required_qty,
												'RemainingQty' => 0,
												'Status'       => 'FIXED'
										);

								} else {

										$processed[] = array(
												'ItemID'       => $item_id,
												'CenterID'     => $center_id,
												'OldBatchNo'   => $negative_batch,
												'ProcessedQty' => $required_qty - $remaining_qty,
												'RemainingQty' => $remaining_qty,
												'Status'       => 'PARTIALLY FIXED'
										);

										$unresolved[] = array(
												'ItemID'       => $item_id,
												'CenterID'     => $center_id,
												'BatchNo'      => $negative_batch,
												'RequiredQty'  => $required_qty,
												'RemainingQty' => $remaining_qty,
												'Reason'       => 'Insufficient positive stock'
										);
								}
						}


						/*
						* ------------------------------------------------------------
						* STEP 8:
						* Check transaction.
						* ------------------------------------------------------------
						*/
						if ($this->db->trans_status() === FALSE) {

								$this->db->trans_rollback();

								echo '<pre>';
								print_r(array(
										'status' => false,
										'message' => 'Transaction failed. Nothing was updated.'
								));
								echo '</pre>';

								return;
						}


						$this->db->trans_commit();


						/*
						* ------------------------------------------------------------
						* FINAL RESULT
						* ------------------------------------------------------------
						*/
						echo '<pre>';

						print_r(array(
								'status' => true,
								'message' => 'Negative batch stock processing completed.',
								'processed' => $processed,
								'unresolved' => $unresolved
						));

						echo '</pre>';


				} catch (Exception $e) {

						$this->db->trans_rollback();

						echo '<pre>';

						print_r(array(
								'status' => false,
								'message' => $e->getMessage()
						));

						echo '</pre>';
				}
		}
	}															