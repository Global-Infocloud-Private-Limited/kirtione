<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class FpoOrder extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->load->model('FpoOrderModel');
		}
		
		public function Fpo_Order($id = '')
        {
            if (!has_permission_new('Fpo_Order_Form', '', 'view')) {
                access_denied('invoices');
            }
            
            if ($this->input->post()) 
            {
                $pur_order_data = $this->input->post();
                $pur_order_data['terms'] = nl2br($pur_order_data['terms']);
                
                $dynamic_param_json = $pur_order_data['dynamic_param_data'] ?? '[]';
                $dynamic_param_data = json_decode($dynamic_param_json, true); 
                $pur_order_data['dynamic_param_data'] = $dynamic_param_data; 
                
                if ($id == '') {
                    if (!has_permission_new('Fpo_Order_Form', '', 'create')) {
                        access_denied('invoices');
                    }
                    $ids = $this->FpoOrderModel->add_fpo_order($pur_order_data);
                    if ($ids) {
                        set_alert('success', _l('added_successfully', _l('pur_order')));
                        redirect(admin_url('FpoOrder/Fpo_Order'));
                    }
                }else{
                    if (!has_permission_new('Fpo_Order_Form', '', 'edit')) {
                        access_denied('invoices');
                    }
                    $idupdate = $this->FpoOrderModel->edit_fpo_order($pur_order_data,$id);
                    if ($idupdate) {
                        set_alert('success', _l('updated_successfully', _l('pur_order')));
                        redirect(admin_url('FpoOrder/Fpo_Order'));
                    }
                }
            }
            
            if($id){
                $OrderDetails = $this->FpoOrderModel->GetFpoDetails($id);
                $data['OrderDetails'] = $OrderDetails;
                $data['pur_Details'] = json_encode($OrderDetails->details);
                $data['QcDetails'] = json_encode($OrderDetails->qcdetails);
                $IsExistDispatch = $this->FpoOrderModel->CheckDispatch($id);
                $data['IsExistDispatch'] = $IsExistDispatch;
            }
            else {
                $data['pur_Details'] = json_encode([]);
                $data['QcDetails'] = json_encode([]);
                $data['isEdit'] = false;
                 $data['IsExistDispatch'] = array();
            }
            
            $data['title'] = "Fpo Order";
            $data['CenterList'] = $this->FpoOrderModel->GetCenterList();
            $data['FPOStaffList'] = $this->FpoOrderModel->GetIsFPOStaffList();
            $data['ItemList'] = $this->FpoOrderModel->GetItemList();
            $data['FarmerList']= $this->FpoOrderModel->GetFarmerList();
            $this->load->view('admin/FpoOrder/Fpo_Order', $data);
        }
        
        public function GetItemParameters()
        {
            $itemID = $this->input->post('itemID');
            $item = $this->FpoOrderModel->GetItemDetails($itemID);
           
            echo json_encode($item);
        }
        
        public function GetDeductionMatrix()
        {
            $parameterName = $this->input->post('parameterName');
            $ItemID = $this->input->post('ItemID');
            $value= $this->input->post('newValue');
            $DeductionMatrix = $this->FpoOrderModel->GetDeductionMatrixData($parameterName,$ItemID,$value);
            echo json_encode($DeductionMatrix);
        }
        
        public function load_fpo_order_data()
        {
            $data = array(
               'from_date' => $this->input->post('from_date'),
               'to_date'  => $this->input->post('to_date'),
            );
            $data = $this->FpoOrderModel->load_fpo_order_list($data);
            echo json_encode($data);
        }
        
        public function FpoRate()
        {
            if (!has_permission_new('Fpo_Rate', '', 'view')) {
                access_denied('invoices');
            }
            $data['title'] = "Fpo Rate";
            $data['CenterList'] = $this->FpoOrderModel->GetCenterList();
            $data['ItemList'] = $this->FpoOrderModel->GetItemList();
            $data['FPOStaffList'] = $this->FpoOrderModel->GetIsFPOStaffList();
            $this->load->view('admin/FpoOrder/FpoRate', $data);
        }
        
        public function GetFilterWiseFpoRateDetails()
		{
			$data = array(
			   'CenterID' =>$this->input->post('CenterID'),
               'ItemID'  => $this->input->post('ItemID'),
    		   'Fpolist'  => $this->input->post('Fpolist'),
    		   'Status' =>$this->input->post('Status'),
            );
            $data = $this->FpoOrderModel->load_data_fpo_rate($data);
            echo json_encode($data);
		}
		
		public function InsertRateMaster()
		{
		    $fpolist = $this->input->post('fpolist');
		    $CenterID =  $this->input->post('CenterID');
		    $ItemID = $this->input->post('ItemID');
		    $rate = $this->input->post('rate');
		    $selected_company = $this->session->userdata('root_company');
		    $fy = $this->session->userdata('finacial_year');
            
            $response = [];

            foreach ($fpolist as $fpoid) {
                $rateMasterDetails = $this->FpoOrderModel->GetRateMasterDetails($fpoid, $ItemID,$CenterID);
        
                if (!empty($rateMasterDetails)) {
                    foreach ($rateMasterDetails as $rateRow) {
                        $updateStatus = [
                            'Status' => 'N',
                        ];
                        $this->db->where('id', $rateRow->id);
                        $this->db->update(db_prefix() . 'FpoRateMaster', $updateStatus);
                    }
                }
                
                $insertRate = [
                    'PlantID'   => $selected_company,
                    'FY'        => $fy,
                    'CenterID'  => $CenterID,
                    'ItemID'    => $ItemID,
                    'FPOID'     => $fpoid,
                    'Rate'      => $rate,
                    'Status'    => "Y",
                    'UserID'    => $this->session->userdata('username'),    
                    'Transdate' => date('Y-m-d h:i:s'),
                ];
        
                $createrate = $this->FpoOrderModel->AddRate($insertRate);
        
                if ($createrate) {
                    $response[] = ['FPOID' => $fpoid, 'success' => true, 'message' => 'Inserted successfully'];
                } else {
                    $response[] = ['FPOID' => $fpoid, 'success' => false, 'message' => 'Failed to insert'];
                }
            }
        
            echo json_encode($response);
		}
		
		public function export_RateMaster_Report()
		{
		    if(!class_exists('XLSXReader_fin')){
    			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    		}
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            if($this->input->post())
            {
    			$company_data = $this->FpoOrderModel->get_company_detail();
    			$fy = $this->session->userdata('finacial_year');
    			
    			$filterdata = array(
                    'Fpolist'   => $this->input->post('Fpolist'),
                    'CenterID' =>$this->input->post('CenterID'),
                    'ItemID'=>$this->input->post('ItemID'),
                    'Status'=>$this->input->post('Status'),
                );
                
                $RateDetails = $this->FpoOrderModel->load_data_fpo_rate($filterdata);
               
                $FpolistText = $this->input->post('FpolistText');
                $CenterText = $this->input->post('CenterText');
                $ItemText = $this->input->post('ItemText');
                $Statustext = $this->input->post('Statustext');
                
                $fpoLabel    = !empty($FpolistText) ? $FpolistText : 'ALL';
                $CenetrLabel    = !empty($CenterText) ? $CenterText : 'ALL';
                $ItemLabel = !empty($ItemText) ? $ItemText : 'ALL';
                $StatusLabel = !empty($Statustext) ? $Statustext : 'ALL';
    		
    			$writer = new XLSXWriter();
    			
                $company_name = array($company_data->company_name);
    			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_name);
    			
    			$address = $company_data->address;
    			$company_addr = array($address,);
    			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_addr);
    			
    			$msg3 = "FPO: " . $fpoLabel . " , Center Name: " . $CenetrLabel . " ,Item Name: " . $ItemLabel . " , Status: " . $StatusLabel;
                $filterRow = array($msg3);
                $writer->markMergedCell('Sheet1', 3, 0, 3, 12);  
                $writer->writeSheetRow('Sheet1', $filterRow);
    			$j = 5;
    			
                $list_add[] = "";
    			$writer->writeSheetRow('Sheet1', $list_add);
    			
    			$set_col_tk = [];
    			$set_col_tk[] = "Sr.No";
    			$set_col_tk[] = "FPO Name";
    			$set_col_tk[] = "Item Name";
    			$set_col_tk[] = "Rate/Quintal";
    			$set_col_tk[] = "Date";		
    			$set_col_tk[] = "Status";	
                $writer_header = $set_col_tk;
    			$writer->writeSheetRow('Sheet1', $writer_header);
    			
    			$SrNo = 1;
                foreach ($RateDetails as $value) {
                    if($value['Status'] == "Y")
                    { $Status = "Active";}
                    else{
                      $Status = "Inactive";
                    }
                    $list_add = [];
    				$list_add[] = $SrNo;
                    $list_add[] = $value['firstname'].' '.$value['lastname'];
                    $list_add[] = $value['ItemName'];
                    $list_add[] = number_format($value['Rate'], 2, '.', '');
                    $list_add[] = _d($value['Transdate']);
                    $list_add[] = $Status;
                    
                    $SrNo++;
    				$writer->writeSheetRow('Sheet1', $list_add);
                }
    			
    			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    			foreach($files as $file){
    				if(is_file($file)) {
    					unlink($file); 
    				}
    			}
    			$filename = 'Fpo Rate List .xlsx';
    			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    			echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    			]);
    			die;  
    		}
		}
		
		public function FpoOrderReport()
		{
		    if (!has_permission_new('FpoOrder_Report', '', 'view')) {
                access_denied('invoices');
            }
            $data['title'] = "Fpo Order Report";
            $data['CenterList'] = $this->FpoOrderModel->GetCenterList();
            $data['ItemList'] = $this->FpoOrderModel->GetItemList();
            $data['TraderList'] = $this->FpoOrderModel->GetIsFPOStaffList();
            $data['company_detail'] = $this->FpoOrderModel->get_company_detail();
            $this->load->view('admin/FpoOrder/FpoOrderReport', $data);
		}
		
		public function GetFilterFpoOrderData()
		{
		    $data = array(
               'from_date' => $this->input->post('from_date'),
               'to_date'  => $this->input->post('to_date'),
               'Fpolist' => $this->input->post('Fpolist'),
               'Item' => $this->input->post('Item'),
               'status' => $this->input->post('status'),
               'payment_status'=> $this->input->post('payment_status'),
            );
            $data = $this->FpoOrderModel->load_filterwise_fpo_order_list($data);
            echo json_encode($data);
		}
		
		public function GetFpoOrderDetailsPayment()
		{
		    $OrderID = $this->input->post('OrderID');
		    $data = $this->FpoOrderModel->load_paymentfpolist($OrderID);
		   
            echo json_encode($data);
		}
		
		public function GetFpoOrderInward()
		{
		    $OrderID = $this->input->post('OrderID');
		    $id = $this->input->post('id');
		    $data = $this->FpoOrderModel->load_inwardfpolists($OrderID,$id);
            echo json_encode($data);
		}
		
		public function GetFilterDispatchData()
		{
		     $data = array(
               'from_date' => $this->input->post('from_date'),
               'to_date'  => $this->input->post('to_date'),
               'Fpolist' => $this->input->post('Fpolist'),
               'Center' => $this->input->post('Center'),
               'Item' => $this->input->post('Item'),
               'statusdispatch' => $this->input->post('statusdispatch'),
            );
            $data = $this->FpoOrderModel->load_filterwise_dispatch_list($data);
            echo json_encode($data);
		}
		
		public function export_FpoOrder_Report()
		{
		    if(!class_exists('XLSXReader_fin')){
    			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    		}
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            if($this->input->post())
            {
    			$company_data = $this->FpoOrderModel->get_company_detail();
    			$fy = $this->session->userdata('finacial_year');
    			
    			$filterdata = array(
                   'from_date' => $this->input->post('from_date'),
                   'to_date'  => $this->input->post('to_date'),
                   'Fpolist' => $this->input->post('Fpolist'),
                   'Item' => $this->input->post('Item'),
                   'status'=>$this->input->post('status'),
                   'payment_status'=>$this->input->post('payment_status'),
                );
                
                $ReportData = $this->FpoOrderModel->load_filterwise_fpo_order_list($filterdata);
                
                $FromDate = $filterdata['from_date'];
                $Todate = $filterdata['to_date'];
                $FpoListText = $filterdata['FpoListText'];
                $ItemText = $this->input->post('ItemText');
                $statustext = $this->input->post('statusText');
                $paymentText = $this->input->post('paymentText');
                
                $fpoLabel    = !empty($FpoListText) ? $FpoListText : 'ALL';
                $ItemLabel = !empty($ItemText) ? $ItemText : 'ALL';
                $StatusLabel = !empty($statustext) ? $statustext : 'ALL';
                $PaymentLabel = !empty($paymentText) ? $paymentText : 'ALL';
                
    			$writer = new XLSXWriter();
    			
                $company_name = array($company_data->company_name);
    			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_name);
    			
    			$address = $company_data->address;
    			$company_addr = array($address,);
    			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_addr);
    			
    			$msg3 = "From Date: " . $FromDate ." , To Date: " . $Todate ." , FPO List: " . $fpoLabel . " , Item Name: " . $ItemLabel . " ,  Status: " . $StatusLabel. " ,  Payment Status: " . $PaymentLabel;
                $filterRow = array($msg3);
                $writer->markMergedCell('Sheet1', 3, 0, 3, 12);  
                $writer->writeSheetRow('Sheet1', $filterRow);
    			$j = 5;
    			
                $list_add[] = "";
    			$writer->writeSheetRow('Sheet1', $list_add);
    			
    			$set_col_tk = [];
    			$set_col_tk[] = "Sr.No";
    			$set_col_tk[] = "PO.No";
    			$set_col_tk[] = "PO Date";
    			$set_col_tk[] = "FPO Name";
    			$set_col_tk[] = "Farmer Name";
    			$set_col_tk[] = "FPO Rate";		
    			$set_col_tk[] = "Item Name";	
    			$set_col_tk[] = "Status";
    			$set_col_tk[] = "Farmer Rate";	
    			$set_col_tk[] = "Order Wt(In Qtl)";	
    			$set_col_tk[] = "Dispatch Wt(In Qtl)";	
    			$set_col_tk[] = "Pending Wt(In Qtl)";	
    			$set_col_tk[] = "Net Rate";
    			$set_col_tk[] = "Amount";
    			$set_col_tk[] = "Payment Status";
                $writer_header = $set_col_tk;
    			$writer->writeSheetRow('Sheet1', $writer_header);
    			
    			$SrNo = 1;
                foreach ($ReportData as $value) {
                    
                    $PendingWt = $value['weight'] - $value['DispatchWt'];
                     
                    if($value['Status'] == 1)
                    { $Status = "Pending"; }
                    else if($value['Status'] == 2)
                    { $Status = "In Progress"; }
                    else if($value['Status'] == 3)
                    { $Status = "Completed"; }
                    
                    if($value['PaymentStatus'] == 1)
                    { $PayStatus = "UNPAID"; }
                    else if($value['PaymentStatus'] == 2)
                    { $PayStatus = "PAYMENT DONE"; }
                    
                    $list_add = [];
                    $list_add[] = $SrNo;
    				$list_add[] = $value['OrderID'];
    				$list_add[] = _d($value['Transdate']);
                    $list_add[] = $value['company'];
                    $list_add[] = $value['farmer_name'];
                    $list_add[] = $value['FpoRate'];
                    $list_add[] = $value['ItemName'];
                    $list_add[] = $Status;
                    $list_add[] = $value['farmer_rate'];
                    $list_add[] = $value['weight'];
                    $list_add[] = $value['DispatchWt'];
                    $list_add[] = $PendingWt;
                    $list_add[] = $value['NetRate'];
                    $list_add[] = $value['NetAmt'];
                    $list_add[] = $PayStatus;
                    $SrNo++;
    				$writer->writeSheetRow('Sheet1', $list_add);
                }
    			
    			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    			foreach($files as $file){
    				if(is_file($file)) {
    					unlink($file); 
    				}
    			}
    			$filename = 'Fpo Order Report .xlsx';
    			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    			echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    			]);
    			die;  
    		}
		}
		
		public function FpoOrderDispatch()
		{
		    if (!has_permission_new('FpoOrder_Dispatch', '', 'view') || !has_permission_new('FpoDispatch_Report', '', 'view')) {
                access_denied('invoices');
            }
            
            $id = $this->uri->segment(5);
		    $OrderID = $this->uri->segment(4);
		    
            if ($this->input->post()) 
            {
                $pur_order_data = $this->input->post();
                $pur_order_data['terms'] = nl2br($pur_order_data['terms']);
                
                $dynamic_param_json = $pur_order_data['dynamic_param_data'] ?? '[]';
                $dynamic_param_data = json_decode($dynamic_param_json, true); 
                $pur_order_data['dynamic_param_data'] = $dynamic_param_data; 
                
                if ($id == '') {
                    if (!has_permission_new('FpoOrder_Dispatch', '', 'create')) {
                        access_denied('invoices');
                    }
                    $idd = $this->FpoOrderModel->add_dispatch_order($pur_order_data);
                    if ($idd) {
                        set_alert('success', _l('added_successfully', _l('pur_order')));
                        redirect(admin_url('FpoOrder/FpoOrderDispatch/' . $OrderID));
                    }
                }else{
                    if (!has_permission_new('FpoOrder_Dispatch', '', 'edit')) {
                        access_denied('invoices');
                    }
                    $idd = $this->FpoOrderModel->edit_dispatch_order($pur_order_data,$id);
                }
            }
                
            if($id){
                $OrderDetails = $this->FpoOrderModel->GetDispatchFpoDetails($OrderID,$id);
                $data['OrderDetails'] = $OrderDetails;
                /*echo "<pre>";
                print_r($OrderDetails);
                die;*/
                $data['pur_Details'] = json_encode($OrderDetails->details);
                $data['QcDetails'] = json_encode($OrderDetails->qcdetails);
            }
            else {
                $OrderDetails = $this->FpoOrderModel->GetFpoDetails($OrderID);
                $data['OrderDetails'] = $OrderDetails;
                $data['pur_Details'] = json_encode($OrderDetails->details);
                $data['QcDetails'] = json_encode($OrderDetails->qcdetails);
                $data['isEdit'] = false;
            }
            
		    $data['title'] = "Fpo Dispatch Order";
            $data['CenterList'] = $this->FpoOrderModel->GetCenterList();
            $data['TraderList'] = $this->FpoOrderModel->GetTraderList();
            $data['ItemList'] = $this->FpoOrderModel->GetItemList();
            $data['FarmerList']= $this->FpoOrderModel->GetFarmerList();
            $data['company_detail'] = $this->FpoOrderModel->get_company_detail();
            $this->load->view('admin/FpoOrder/FpoOrderDispatch', $data);
		}
		
		public function generateEwayBill()
		{
			$postData = $this->input->post();
			// Get data
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$DispatchNo = $this->input->post('DispatchNo');
			// Get Company Details
			$company_details = $this->FpoOrderModel->get_company_detail($selected_company);
			
			// Step 1: Authentication - Get AuthToken
			/*$authHeaders = [
    			'email'         => $company_details->eway_email,
    			'username'      => $company_details->eway_username,
    			'password'      => $company_details->eway_password,
    			'ip_address'    => $_SERVER['REMOTE_ADDR'],
    			'client_id'     => $company_details->eway_client_id,
    			'client_secret' => $company_details->eway_client_secret,
    			'gstin'         => $company_details->eway_gstin,
			];*/
			$authHeaders = [
    			'email'         => "ajinkya.bhalerao@globalinfocloud.com",
    			'username'      => "BVMGSP",
    			'password'      => "Wbooks@0142",
    			'ip_address'    => $_SERVER['REMOTE_ADDR'],
    			'client_id'     => "EWBS9b6a21f2-c644-48aa-99c9-0233e73de7ae",
    			'client_secret' => "EWBS2d477cc9-a452-4044-9a45-5cfd93e5f88b",
    			'gstin'         => "29AAGCB1286Q000",
			];
			
			/*$queryParams = http_build_query([
    			'email'    => $authHeaders['email'],
    			'username' => $authHeaders['username'],
    			'password' => $authHeaders['password']
			]);*/
			$queryParams = http_build_query([
    			'email'    => "ajinkya.bhalerao@globalinfocloud.com",
    			'username' => "BVMGSP",
    			'password' => "Wbooks@0142"
			]);
			
			$authURL = "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/authenticate?" . $queryParams;
			//$authURL = "https://api.whitebooks.in/ewaybillapi/v1.03/authenticate?" . $queryParams;
			
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
			
			$FPODetails = $this->FpoOrderModel->GetDispatchOrderDetails($DispatchNo);
			$PlantDetails = $this->FpoOrderModel->GetPlantDetails($DispatchNo);
			$totalNetWgt = 0;
			$toatlAmt = 0;
            if (!empty($FPODetails->OrderDetails) && is_array($FPODetails->OrderDetails)) {
                foreach ($FPODetails->OrderDetails as $detail) {
                    $totalNetWgt += floatval($detail->NetWgt);
                    $toatlAmt += floatval($detail->Amount);
                }
            }
			$isUnregistered = ($toGstin == '' || $toGstin == 'URP');
			$toGstin = "URP";
			//$toGstin = "27AAECK0739R1ZZ";
			/*$ewayData = [
			"supplyType"        => "O",
			"subSupplyType"     => "1",
			"subSupplyDesc"     => " ",
			"docType"           => "INV",
			"docNo"             => $DispatchNo,
			"docDate"           => date("d/m/Y"),
			"fromGstin"         => $company_details->eway_gstin,
			"fromTrdName"       => $company_details->company_name,
			"fromAddr1"         => $company_details->address,
			"fromAddr2"         => " ",
			"fromPlace"         => $company_details->city,
			"actFromStateCode"  => (int) sprintf('%02d', $company_details->eway_statecode),
			"fromPincode"       => (int) $company_details->pincode,
			"fromStateCode"     => (int) sprintf('%02d', $company_details->eway_statecode),
			"toGstin"           => $toGstin,
			"toTrdName"         => $FPODetails->company,
			"toAddr1"           => "78C, MARKET YARD, KAVA ROAD, LATUR",
			"toAddr2"           => " ",
			"toPlace"           => "MARKET YARD",
			"toPincode"         => 413512,
			"actToStateCode"    => 27,
			"toStateCode"       => 27,
			"transactionType"   => 4,
			"dispatchFromGSTIN" => $company_details->eway_gstin,
			"dispatchFromTradeName" => $company_details->company_name,
			"shipToTradeName"   => $FPODetails->company,
			"totalValue"        => 2000.00,
			"cgstValue"         => 120.00,
			"sgstValue"         => 120.00,
			"igstValue"         => 0.00,
			"cessValue"         => 0,
			"cessNonAdvolValue" => 0,
			"totInvValue"       => 2440.00,
			"transMode"         => "1",
			"transDistance"     => "25",
			"transporterName"   => "",
			"transporterId"     => "05AAACG0904A1ZL",
			"transDocNo"        => "12",
			"transDocDate"      => date("d/m/Y"),
			"vehicleNo"         => "MH748484",
			"vehicleType"       => "R",
			"itemList"          => []
			];*/
			
			$ewayData = [
			"supplyType"        => "O",
			"subSupplyType"     => "1",
			"subSupplyDesc"     => " ",
			"docType"           => "INV",
			"docNo"             => $DispatchNo,
			"docDate"           => _d(substr($FPODetails->Transdate,0,10)),
			"fromGstin"         => $PlantDetails->GstNo,
			"fromTrdName"       => $PlantDetails->PlantName,
			"fromAddr1"         => $PlantDetails->address,
			"fromAddr2"         => "",
			"fromPlace"         => $PlantDetails->city,
			"actFromStateCode"  => (int) sprintf('%02d', $PlantDetails->id),
			"fromPincode"       => (int) $PlantDetails->pincode,
			"fromStateCode"     => (int) sprintf('%02d', $PlantDetails->id),
			"toGstin"           => $toGstin,
			"toTrdName"         => $PlantDetails->PlantName,
			"toAddr1"           => $PlantDetails->address,
			"toAddr2"           => " ",
			"toPlace"           => $PlantDetails->city_name,
			"toPincode"         => (int) $PlantDetails->pincode,
			"actToStateCode"    => (int) sprintf('%02d', $PlantDetails->id),
			"toStateCode"       => (int) sprintf('%02d', $PlantDetails->id),
			"transactionType"   => 4,
			"dispatchFromGSTIN" => $PlantDetails->GstNo,
			"dispatchFromTradeName" => $PlantDetails->PlantName,
			"shipToTradeName"   => $PlantDetails->PlantName,
			"totalValue"        => $toatlAmt,
			"cgstValue"         => 0.00,
			"sgstValue"         => 0.00,
			"igstValue"         => 0.00,
			"cessValue"         => 0,
			"cessNonAdvolValue" => 0,
			"totInvValue"       => $toatlAmt,
			"transMode"         => "1",
			"transDistance"     => "25",
			"transporterName"   => "",
			"transporterId"     => "05AAACG0904A1ZL",
			"transDocNo"        => $DispatchNo,
			"transDocDate"      => date("d/m/Y"),
			"vehicleNo"         => $FPODetails->VehicleNo,
			"vehicleType"       => "R",
			"itemList"          => []
			];
			
			/*if (!$isUnregistered) {
				$ewayData["shipToGSTIN"] = $toGstin;
			}*/
			
			$sl = 1;
			/*foreach ($items as $item) {
				$ewayData['itemList'][] = [
					"productName"   => $item['description'],
					"productDesc"   => $item['description'],
					"hsnCode"       => $item['hsn_code'],
					"quantity"      => floatval($item['BilledQty']),
					"qtyUnit"       => 'PCS',// $item['unit']
					"cgstRate"      => floatval($item['cgst']),
					"sgstRate"      => floatval($item['sgst']),
					"igstRate"      => floatval($item['igst']),
					"cessRate"      => 0,
					"taxableAmount"=> floatval($item['ChallanAmt'])
				];
				$sl++;
			}*/
			
			$ewayData['itemList'][] = [
				"productName"   =>  $FPODetails->ItemName,
				"productDesc"   => $FPODetails->ItemName,
				"hsnCode"       => $FPODetails->hsn_code,
				"quantity"      => $totalNetWgt,
				"qtyUnit"       => 'Kg',
				"cgstRate"      => 0.00,
				"sgstRate"      => 0.00,
				"igstRate"      => 0.00,
				"cessRate"      => 0,
				"taxableAmount"=> $toatlAmt
			];
			
			
			// Step 3: Send E-Way Bill request
			$Url = "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']);
			//$Url = "https://api.whitebooks.in/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']);
			$ch = curl_init();
			curl_setopt_array($ch, [
			CURLOPT_URL            => $Url,
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
			 echo "<pre>";print_r($ewayResData);
			 die;
			if (isset($ewayResData['data']['ewayBillNo'])) {
				// Save to DB
				$this->db->where('DispatchID', $SalesID);
				$this->db->update(db_prefix().'FpoDispatchMaster', [
					'ewaybill_cancelled' => null,
					'EwayCancelRemark' => null,
					'ewaybill_no' => $ewayResData['data']['ewayBillNo'],
					'ewaybill_date' => date('Y-m-d H:i:s'),
					'ewaybill_valid_upto' => $ewayResData['data']['validUpto']
				]);
				$return = true;
				$SuccessMsg .= "E-Way Bill Is Generated Successfully OrderID ".$DispatchNo." . ";
			} else {
				$ErrorMsg .= "E-Way Bill Is Not Generate OrderID ".$DispatchNo.". ";
			}
			$Result['Status'] = $return;
			$Result['ErrorMsg'] = $ErrorMsg;
			$Result['SuccessMsg'] = $SuccessMsg;
			echo json_encode($Result);
		}
		
		public function FpoDispatchReport()
		{
		    if (!has_permission_new('FpoDispatch_Report', '', 'view')) {
                access_denied('invoices');
            }
            $data['title'] = "Fpo Dispatch Report";
            $data['CenterList'] = $this->FpoOrderModel->GetCenterList();
            $data['ItemList'] = $this->FpoOrderModel->GetItemList();
            $data['TraderList'] = $this->FpoOrderModel->GetIsFPOStaffList();
            $data['company_detail'] = $this->FpoOrderModel->get_company_detail();
            $this->load->view('admin/FpoOrder/FpoDispatchReport', $data);
		}
		
		public function export_FpoDispatch_Report()
		{
		    if(!class_exists('XLSXReader_fin')){
    			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    		}
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            if($this->input->post())
            {
    			$company_data = $this->FpoOrderModel->get_company_detail();
    			$fy = $this->session->userdata('finacial_year');
    			
    			$filterdata = array(
                   'from_date' => $this->input->post('from_date'),
                   'to_date'  => $this->input->post('to_date'),
                   'Fpolist' => $this->input->post('Fpolist'),
                   'Item' => $this->input->post('Item'),
                   'Center'=>$this->input->post('Center'),
                );
                
                $ReportData = $this->FpoOrderModel->load_filterwise_dispatch_list($filterdata);
                
                $FromDate = $filterdata['from_date'];
                $Todate = $filterdata['to_date'];
                $FpoListText = $filterdata['FpoListText'];
                $ItemText = $this->input->post('ItemText');
                $CenterText = $this->input->post('CenterText');
                
                $fpoLabel   = !empty($FpoListText) ? $FpoListText : 'ALL';
                $ItemLabel = !empty($ItemText) ? $ItemText : 'ALL';
                $CenterLabel = !empty($CenterText) ? $CenterText : 'ALL';
                
    			$writer = new XLSXWriter();
    			
                $company_name = array($company_data->company_name);
    			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_name);
    			
    			$address = $company_data->address;
    			$company_addr = array($address,);
    			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  
    			$writer->writeSheetRow('Sheet1', $company_addr);
    			
    			$msg3 = "From Date: " . $FromDate ." , To Date: " . $Todate ." , FPO List: " . $fpoLabel . " , Item Name: " . $ItemLabel . " , Center Name: " . $CenterLabel;
                $filterRow = array($msg3);
                $writer->markMergedCell('Sheet1', 3, 0, 3, 12);  
                $writer->writeSheetRow('Sheet1', $filterRow);
    			$j = 5;
    			
                $list_add[] = "";
    			$writer->writeSheetRow('Sheet1', $list_add);
    			
    			$set_col_tk = [];
    			$set_col_tk[] = "Sr.No";
    			$set_col_tk[] = "DIS.No";
    			$set_col_tk[] = "PO.No";
    			$set_col_tk[] = "Dispatch Date";
    			$set_col_tk[] = "FPO Name";
    			$set_col_tk[] = "Farmer Name";
    			$set_col_tk[] = "Center Name";
    			$set_col_tk[] = "Vehicle No";
    			$set_col_tk[] = "FPO Rate";		
    			$set_col_tk[] = "Item Name";	
    			$set_col_tk[] = "Farmer Rate";	
    			$set_col_tk[] = "Weight(Qtl)";	
    			/*$set_col_tk[] = "Commission Amt";	*/
    			$set_col_tk[] = "Net Rate";
    			$set_col_tk[] = "Amount";
                $writer_header = $set_col_tk;
    			$writer->writeSheetRow('Sheet1', $writer_header);
    			
    			$SrNo = 1;
    			$totalWeight = 0;
                $totalCommissionAmt = 0;
                $totalNetAmt = 0;
                foreach ($ReportData as $value) {
                    
                    $CommissionAmt = ((float)$value['FpoRate'] - (float)$value['farmer_rate']) * (float)$value['weight'];
                    
                    $totalWeight += (float)$value['weight'];
                    $totalCommissionAmt += $CommissionAmt;
                    $totalNetAmt += (float)$value['NetAmt'];
    
                    $list_add = [];
                    $list_add[] = $SrNo;
                    $list_add[] = $value['DispatchID'];
    				$list_add[] = $value['OrderID'];
    				$list_add[] = _d($value['Dispatch_Date']);
                    $list_add[] = $value['company'];
                    $list_add[] = $value['farmer_name'];
                    $list_add[] = $value['CenterName'];
                    $list_add[] = $value['VehicleNo'];
                    $list_add[] = $value['FpoRate'];
                    $list_add[] = $value['ItemName'];
                    $list_add[] = $value['farmer_rate'];
                    $list_add[] = $value['weight'];
                    /*$list_add[] = $CommissionAmt;*/
                    $list_add[] = $value['NetRate'];
                    $list_add[] = $value['NetAmt'];
                    $SrNo++;
    				$writer->writeSheetRow('Sheet1', $list_add);
                }
                
                $totalRow = [];
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = 'TOTAL'; 
                $totalRow[] = number_format($totalWeight, 2, '.', '');
                /*$totalRow[] = number_format($totalCommissionAmt, 2, '.', '');*/
                $totalRow[] = ''; 
                $totalRow[] = ''; 
                $totalRow[] = number_format($totalNetAmt, 2, '.', '');
                
                $writer->writeSheetRow('Sheet1', $totalRow);
    			
    			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    			foreach($files as $file){
    				if(is_file($file)) {
    					unlink($file); 
    				}
    			}
    			$filename = 'Fpo Dispatch Report .xlsx';
    			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    			echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    			]);
    			die;  
    		}
		}
		
		public function FpoPayment($id)
		{
		    if (!has_permission_new('FpoOrder_Payment', '', 'view') || !has_permission_new('FpoOrder_Report', '', 'view')) {
                access_denied('invoices');
            }
           
            $data['title'] = "Make FPO Payment";
            
            $OrderDetails = $this->FpoOrderModel->GetFpoDetails($id);
            $totalWeight = 0;
            $totalAmt = 0;
            if (!empty($OrderDetails->details) && is_array($OrderDetails->details)) {
                foreach ($OrderDetails->details as $detail) {
                    $totalWeight += floatval($detail['NetWeight']);
                    $totalAmt += floatval($detail['Amount']);
                }
            }
            $OrderDetails->TotalWeight = $totalWeight;
            $OrderDetails->TotalAmt = $totalAmt;
            $data['OrderDetails'] = $OrderDetails;
            $data['Qcdetails'] = $OrderDetails->qcdetails;
            $data['genral_account_to_select'] = $this->FpoOrderModel->get_data_ganeral_account_to_select();
            $this->load->view('admin/FpoOrder/FpoPayment', $data);
		}
//======================== Kirti Inward Page load ==============================	
	public function FpoInward($OrderID,$id)
	{
	    if (!has_permission_new('Fpo_Inward', '', 'view') || !has_permission_new('FpoDispatch_Report', '', 'view')) {
            access_denied('invoices');
        }
        $data['title'] = "FPO Inward";
        $OrderDetails = $this->FpoOrderModel->GetDispatchFpoDetails($OrderID,$id);
        $totalWeight = 0;
        if (!empty($OrderDetails->details) && is_array($OrderDetails->details)) {
            foreach ($OrderDetails->details as $detail) {
                $totalWeight += floatval($detail['DispatchQty']);
            }
        }
        $OrderDetails->TotalWeight = $totalWeight;
        $data['OrderDetails'] = $OrderDetails;
        
        $DebitExist = $this->FpoOrderModel->GetDispatchEntry($OrderID,$id);
        $data['DebitExist'] = $DebitExist;
        
        $data['Qcdetails'] = $OrderDetails->qcdetails;
        $CenterID = $OrderDetails->details[0]['CenterID'];
        $WarehouseDetails =  $this->FpoOrderModel->GetWarehouseDetails($CenterID);
        $existingStackList = $this->FpoOrderModel->GetInwardQcData($OrderID,$id);
        $data['existingStackList'] = $existingStackList;
        $data['WarehouseDetails'] = $WarehouseDetails;
        
        $total_weighted_sum = [];
        $total_lot_weight = 0;
        
        foreach ($existingStackList as $item) {
            $weight = $item['lot_weight'];
            $total_lot_weight += $weight;
            
            foreach ($item as $key => $value) {
                if ($key !== 'lot_weight' && is_numeric($value)) {
                    if (!isset($total_weighted_sum[$key])) {
                        $total_weighted_sum[$key] = 0;
                    }
                    $total_weighted_sum[$key] += $value * $weight;
                }
            }
        }
        $weighted_avg = [];
        foreach ($total_weighted_sum as $key => $weighted_sum) {
            $weighted_avg[$key] = $weighted_sum / $total_lot_weight;
        }
        $data['weighted_avg'] = $weighted_avg;
        $this->load->view('admin/FpoOrder/FpoInward', $data);
	}
		
		public function GetChamberListInward()
		{
		    $GodownID = $this->input->post('GodownID');
		    $data = $this->FpoOrderModel->GetChamberList($GodownID);
            echo json_encode($data);
		}
		
		public function GetStackListInward()
		{
		    $CHID = $this->input->post('CHID');
			$result = $this->FpoOrderModel->GetWarehouseStackList($CHID);
			echo json_encode($result);
		}
		
		public function GetStackLotList()
		{
		    $StackID = $this->input->post('StackID');
			$result = $this->FpoOrderModel->GetStackLotList($StackID);
			echo json_encode($result);
		}
		
		public function updateStackDetails()
		{
		    $requestData = $this->input->post();
		    $OrderID = $requestData['OrderID'];
		    $DispatchID = $requestData['DispatchID'];
		    $result = $this->FpoOrderModel->UpdateStackDetails($requestData);
			if($result == $TotalLot){
				set_alert('success','Stack List Updated successfully');
				}else if($result > 0){
				set_alert('success','Stack List Updated successfully');
				}else{
				set_alert('warning','Stack List not updated please try again');
			}
			redirect('admin/FpoOrder/FpoInward/' . $OrderID . '/' . $DispatchID);
		}
		
		public function AddGrossWeight()
		{
		    $OrderID = $this->input->post('FpoOrderID');
		    $DispatchID = $this->input->post('FpoDispatchID');
		    $grossweight = $this->input->post('grossweight');
		    if($grossweight !="")
		    {
		        $edit_grosswt = array(
		                'GrossWt'=>$grossweight,
		            );
		        $this->db->WHERE('OrderID', $OrderID);
		        $this->db->WHERE('DispatchID', $DispatchID);
                $update_success  = $this->db->update(db_prefix() . 'FpoDispatchMaster',$edit_grosswt);
                if ($update_success) {
                    set_alert('success', 'Gross Weight Updated successfully');
                } else {
                    set_alert('danger', 'Update failed. Please try again.');
                }
		    }
		    redirect('admin/FpoOrder/FpoInward/' . $OrderID . '/' . $DispatchID);
		}
		
		public function AddTareWeight()
		{
		    $OrderID = $this->input->post('orderid');
		    $DispatchID = $this->input->post('dispatchid');
		    $TareWeight = $this->input->post('tareweight');
		    
		    if($TareWeight !="")
		    {
		        $edit_tarewt = array(
		                'TareWt'=>$TareWeight,
		            );
		        $this->db->WHERE('OrderID', $OrderID);
		        $this->db->WHERE('DispatchID', $DispatchID);
                $update_success  = $this->db->update(db_prefix() . 'FpoDispatchMaster',$edit_tarewt);
                if ($update_success) {
                    set_alert('success', 'Tare Weight Updated successfully');
                } else {
                    set_alert('danger', 'Update failed. Please try again.');
                }
		    }
		    redirect('admin/FpoOrder/FpoInward/' . $OrderID . '/' . $DispatchID);
		}
		
		public function ExitVehicleOrder()
		{
		    $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            
		    $OrderID = $this->input->post('OrderID');
		    $DispatchID = $this->input->post('DispatchID');
		    
		    $DispatchDetails = $this->FpoOrderModel->GetDispatchInfo($OrderID,$DispatchID);
		    $Details = $DispatchDetails->OrderDetails;
		    
		    $edit_status = array(
	                'FpoStatus'=>3,
	        );
	        $this->db->WHERE('OrderID', $OrderID);
	        $this->db->WHERE('DispatchID', $DispatchID);
            $update_success  = $this->db->update(db_prefix() . 'FpoDispatchMaster',$edit_status);
            if ($update_success) 
            {
                //Journal Voucher 
                $date= to_sql_date($DispatchDetails->Transdate);
                $GetLastUniqueNo = $this->FpoOrderModel->GetLastUniqueNo($date);
                $LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
                
                $get_result_to_cur_date_journal = $this->FpoOrderModel->get_result_to_cur_date_journal($date);
                
                $i = 1;
                foreach($Details as $Val)
                {
                    //Journal Voucher Entry
                    if(empty($get_result_to_cur_date_journal)){
                        if($selected_company == 1){
                            $new_journalNumber = get_option('next_journal_number_for_kirti');
                        }
                        $new_voucher_number_journal = $new_journalNumber;
                    }else{ 
                        $count = count($get_result_to_cur_date_journal);
                        $last_index = $count - 1;
                        $new_voucher_number_journal = $get_result_to_cur_date_journal[$last_index]['VoucherID'];
                        
                        $incNo = (int) $new_voucher_number_journal - 1;
                        $sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
                        $this->db->query($sql);
                        if ($this->db->affected_rows() > 0) {
                            $this->FpoOrderModel->increment_next_journal_number();
                        }
                    }
                
                    $Fpo_Rate = $DispatchDetails->FpoRate;
                    $DifferenceRate = $Fpo_Rate - $Val->Rate;
                    $ToatlCommissionAmt = $DifferenceRate * $Val->NetWgt;
                    
                    //credit journal voucher
                    $credit_journal_voucher = array(
                            "PlantID" =>$selected_company,
                            "Transdate" =>$DispatchDetails->Transdate,
                            "TransDate2" =>date('Y-m-d H:i:s'),
                            "VoucherID" =>$new_voucher_number_journal,
                            "AccountID" =>$DispatchDetails->FPOID,
                            "TType" =>"C",
                            "CenterID" =>$DispatchDetails->CenterID,
                            "CommodityID" =>$DispatchDetails->ItemID,
                            "EntryFor" =>"2",
                            "PartyID" => $DispatchDetails->PartyID,
                            "Amount" => $ToatlCommissionAmt,
                            "Narration" =>  "Commission Against ". $Val->OrderID ."/ ". $Val->DispatchID . " Purchase From ". $Val->company,
                            "CounterAccount" =>"FPOCOMM",
                            "PassedFrom" =>"JOURNAL",
                            "OrdinalNo" =>$i,
                            "UserID" =>$this->session->userdata('username'),
                            "FY" =>$fy,
                            "UniquID" =>$LastUniqueID,
                    );
                    $this->db->insert(db_prefix().'accountledger', $credit_journal_voucher);
                    $i++;
                    
                    //debit journal voucher
                    $Fpo_journal_voucher = array(
                        "PlantID" =>$selected_company,
                        "Transdate" =>$DispatchDetails->Transdate,
                        "TransDate2" =>date('Y-m-d H:i:s'),
                        "VoucherID" =>$new_voucher_number_journal,
                        "AccountID" =>"FPOCOMM",
                        "CounterAccount" =>$DispatchDetails->FPOID,
                        "TType" =>'D',
                        "Amount" =>$ToatlCommissionAmt,
                        "CenterID" =>$DispatchDetails->CenterID,
                        "CommodityID" =>$DispatchDetails->ItemID,
                        "EntryFor" =>"2",
                        "PartyID" =>$DispatchDetails->PartyID,
                        "Narration" =>  "Commission Against ". $Val->OrderID ."/ ". $Val->DispatchID . " Purchase From ". $Val->company,
                        "PassedFrom" =>"JOURNAL",
                        "OrdinalNo" =>$i,
                        "UserID" =>$this->session->userdata('username'),
                        "FY" =>$fy,
                        "UniquID" =>$LastUniqueID,
                        );
                    $this->db->insert(db_prefix().'accountledger', $Fpo_journal_voucher);
                    $i++;
                    
                    if(empty($get_result_to_cur_date_journal)){
                        $this->FpoOrderModel->increment_next_journal_number();
                    }
                }
                
                echo json_encode(['status' => true]);
            } else {
                echo json_encode(['status' => false]);
            }
		}
		
		public function AddFpoPayment()
		{
		    $data = $this->input->post();
		    $OrderID = $this->input->post('FpoOrderID');
		    $PayEntry = $this->FpoOrderModel->add_payment_entry($data);
		    if($PayEntry){
				set_alert('success','Generate Payment Successfully.');
			}else{
				set_alert('warning','Something went wrong please try again');
			}
			redirect('admin/FpoOrder/FpoPayment/' . $OrderID);
		}
		
		public function FetchRate()
		{
		    $Fpolist = $this->input->post('Fpolist');
		    $ItemID = $this->input->post('ItemID');
		    $CenterID = $this->input->post('CenterID');
		    $FpoRateMaster = $this->FpoOrderModel->FetchRate($Fpolist,$ItemID,$CenterID);
		    echo json_encode($FpoRateMaster);
		}
		
		public function BagEntryForm()
		{
		    if (!has_permission_new('Bag_ledger', '', 'view')) {
                access_denied('invoices');
            }
            $data['title'] = "Bag Entry Form";
            $data['company_detail'] = $this->FpoOrderModel->get_company_detail();
            $data['FPOStaffList'] = $this->FpoOrderModel->GetIsFPOStaffList();
            $this->load->view('admin/FpoOrder/BagEntryForm', $data);
		}
		
		public function AddBagLedger()
		{
		     $data = array(
		            'FpoList'=>$this->input->post('FpoList'),
		            'BagDate'=>$this->input->post('BagDate'),
		            'BagType'=>$this->input->post('BagType'),
		            'BagQty'=>$this->input->post('BagQty'),
		        );
		    $LedgerEntry = $this->FpoOrderModel->AddBagLedger($data);
		    echo json_encode($LedgerEntry);
		}
		
		public function GetFilterBagData()
        {
            $fy = $this->session->userdata('finacial_year');
            $FromDate = $this->input->post('from_date');
            $data = array(
                'from_date'=>to_sql_date($this->input->post('from_date')),
                'to_date'=>to_sql_date($this->input->post('to_date')),
                'Fpolist'=>$this->input->post('Fpolist'),
            );
            $BagOpenQty = $this->FpoOrderModel->GetBagOpenQtyData();
            
            foreach($BagOpenQty as $val)
            {
                if($val['Type'] == 'C')
                {
                    $OpenCr = $val['Total_qty'];
                }else if($val['Type'] == 'D'){
                    $OpenDr = $val['Total_qty'];
                }
            }
            
            $Open = $OpenCr - $OpenDr;
            
            $BagLedgerData = $this->FpoOrderModel->GetBagLedgerData($data);
            $FirstDateFY = "20".$fy."-04-01";
            $FromDateNew = to_sql_date($FromDate);
            
            if($FromDateNew > $FirstDateFY){
                $onedaybefore = (new DateTime())->modify('-1 day')->format($FromDateNew);
                $data1 = array(
                    'from_date'=>$FirstDateFY,
                    'to_date'=>$onedaybefore,
                    'Fpolist'=>$this->input->post('Fpolist'),
                );
                $beforeFromDateBagLedger = $this->FpoOrderModel->GetBagLedgerData($data1);
            }
            
            if (!empty($beforeFromDateBagLedger)) {
                $CR = 0;$DR = 0;
                foreach ($beforeFromDateBagLedger as $index => $row) {
                    if ($row['PassedFrom'] == 'Transfer') {
                        $CR += $row['Qty'];
                    } else if ($row['PassedFrom'] == 'PURCHASE') {
                        $DR += $row['Qty'];
                    }
                }
            }
            
            $opnQty = $Open + $CR - $DR;
            
            $html = '';
            $balance = 0; 

            $html .= '<tr>';
            if($opnQty > 0){
                $CRDR = "DR";
                $color = "red";
            }else{
                 $CRDR = "CR";
                 $color = "green";
            }
            
            $html .= '<td colspan=3 style="font-weight: bold; color: ' . $color . ';border: 1px solid black;text-align: right;">Opening Qty</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td style="font-weight: bold; color:' . $color . ';text-align: right;">' . htmlspecialchars(number_format($opnQty, 2)).' '.$CRDR . '</td>';
            $html .= '</tr>';
                    
            if (!empty($BagLedgerData)) {
                $balqty = 0;$CR =0;$DR =0;$opnqty = 0;
                foreach ($BagLedgerData as $index => $row) {
                    if ($row['PassedFrom'] != 'Open') {
                        if ($row['PassedFrom'] == 'Transfer') {
                            $CR = $row['Qty'];
                        } else if ($row['PassedFrom'] == 'PURCHASE') {
                            $DR = $row['Qty'];
                        }
                        
                        $balqty = $opnqty + $CR - $DR;
                        
                        if($balqty > 0){
                            $CRDR = "DR";
                        }else{
                             $CRDR = "CR";
                        }
                    
                        $html .= '<tr>';
                        $html .= '<td>' . ($index + 1) . '</td>';
                        $html .= '<td style="text-align: center;">' . date('d/m/Y', strtotime($row['Transdate'])) . '</td>';
                        $html .= '<td>' . htmlspecialchars($row['PassedFrom']) . '</td>';
                         
                        $html .= '<td style="text-align: center;">' . htmlspecialchars($DR) . '</td>';
                        $html .= '<td style="text-align: center;">' . htmlspecialchars($CR) . '</td>';
                        $html .= '<td style="font-weight: bold;text-align: right;">' . htmlspecialchars(number_format($balqty, 2)).' '.$CRDR . '</td>';
                        $html .= '</tr>';
                        $opnqty = $balqty;
                    }
                }
            } else {
                $html .= '<tr><td colspan="6" class="text-center">No data found.</td></tr>';
            }
            
            $closingQty = $opnqty;
            $html .= '<tr>';
            if($closingQty > 0){
                $CRDR = "DR";
                $color = "red";
            }else{
                 $CRDR = "CR";
                 $color = "green";
            }
            
            $html .= '<td colspan=3 style="font-weight: bold; color: ' . $color . ';border: 1px solid black;text-align: right;">Closing Qty</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td style="font-weight: bold; color:' . $color . ';text-align: right;">' . htmlspecialchars(number_format($closingQty, 2)).' '.$CRDR . '</td>';
            $html .= '</tr>';
            
            echo $html;
        }
        
        public function export_Bag_Report()
        {
            if (!class_exists('XLSXReader_fin')) {
                require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
            }
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
            if ($this->input->post()) {
                $company_data = $this->FpoOrderModel->get_company_detail();
                $fy = $this->session->userdata('finacial_year');
        
                $filterdata = array(
                    'from_date' => to_sql_date($this->input->post('from_date')),
                    'to_date'   => to_sql_date($this->input->post('to_date')),
                    'Fpolist'   => $this->input->post('Fpolist'),
                );
        
                $FpoListText = $this->input->post('FpoListText');
               
                $BagOpenQty = $this->FpoOrderModel->GetBagOpenQtyData();
        
                $OpenCr = 0; $OpenDr = 0;
                foreach ($BagOpenQty as $val) {
                    if ($val['Type'] == 'C') {
                        $OpenCr = $val['Total_qty'];
                    } elseif ($val['Type'] == 'D') {
                        $OpenDr = $val['Total_qty'];
                    }
                }
        
                $Open = $OpenCr - $OpenDr;
                
                $FromDateNew = to_sql_date($this->input->post('from_date'));
                $FirstDateFY = "20" . $fy . "-04-01";
        
                $CR = 0; $DR = 0;
                if ($FromDateNew > $FirstDateFY) {
                    $onedaybefore = date('Y-m-d', strtotime($FromDateNew . ' -1 day'));
                    $data1 = array(
                        'from_date' => $FirstDateFY,
                        'to_date' => $onedaybefore,
                        'Fpolist' => $this->input->post('Fpolist'),
                    );
                    $beforeFromDateBagLedger = $this->FpoOrderModel->GetBagLedgerData($data1);
        
                    if (!empty($beforeFromDateBagLedger)) {
                        foreach ($beforeFromDateBagLedger as $row) {
                            if ($row['PassedFrom'] == 'Transfer') {
                                $CR += $row['Qty'];
                            } elseif ($row['PassedFrom'] == 'PURCHASE') {
                                $DR += $row['Qty'];
                            }
                        }
                    }
                }
                
                $opnQty = $Open + $CR - $DR;
        
                $BagLedgerData = $this->FpoOrderModel->GetBagLedgerData($filterdata);
        
                $writer = new XLSXWriter();
                
                $writer->markMergedCell('Sheet1', 0, 0, 0, 5);
                $writer->writeSheetRow('Sheet1', [$company_data->company_name]);
        
                $writer->markMergedCell('Sheet1', 1, 0, 1, 5);
                $writer->writeSheetRow('Sheet1', [$company_data->address]);
               
                $msg3 = "From Date: " . _d($this->input->post('from_date')) .
                    ", To Date: " . _d($this->input->post('to_date')) .
                    ", FPO: " . ($FpoListText ?: 'ALL');
                $writer->markMergedCell('Sheet1', 3, 0, 3, 5);
                $writer->writeSheetRow('Sheet1', [$msg3]);
        
                $writer->writeSheetRow('Sheet1', ['']); 
                
                $header = ['Sr.No', 'Date', 'Passed From', 'Debit', 'Credit', 'Balance'];
                $writer->writeSheetRow('Sheet1', $header);
                
                $balanceLabel = ($opnQty >= 0) ? 'DR' : 'CR';
                $writer->writeSheetRow('Sheet1', ['', '', 'Opening Qty', '', '', number_format(abs($opnQty), 2) . ' ' . $balanceLabel]);
                
                $opnqty = 0; 
                $sr = 1;
        
                if (!empty($BagLedgerData)) {
                    foreach ($BagLedgerData as $index => $row) {
                        if ($row['PassedFrom'] != 'Open') {
                            $CR = 0;
                            $DR = 0;
                            if ($row['PassedFrom'] == 'Transfer') {
                                $CR = $row['Qty'];
                            } elseif ($row['PassedFrom'] == 'PURCHASE') {
                                $DR = $row['Qty'];
                            }
        
                            if ($sr == 1) {
                                $balqty = $CR - $DR;
                            } else {
                                $balqty = $opnqty + $CR - $DR;
                            }
        
                            $balanceLabel = ($balqty >= 0) ? 'DR' : 'CR';
        
                            $writer->writeSheetRow('Sheet1', [
                                $sr++,
                                date('d/m/Y', strtotime($row['Transdate'])),
                                $row['PassedFrom'],
                                $DR > 0 ? number_format($DR, 2) : '0.00',
                                $CR > 0 ? number_format($CR, 2) : '0.00',
                                number_format(abs($balqty), 2) . ' ' . $balanceLabel,
                            ]);
        
                            $opnqty = $balqty;
                        }
                    }
                } else {
                    $writer->writeSheetRow('Sheet1', ['', '', 'No data found.', '', '', '']);
                }
                
                $closingQty = $opnqty;
                $balanceLabel = ($closingQty >= 0) ? 'DR' : 'CR';
                $writer->writeSheetRow('Sheet1', ['', '', 'Closing Qty', '', '', number_format(abs($closingQty), 2) . ' ' . $balanceLabel]);
                
                $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
                foreach ($files as $file) {
                    if (is_file($file)) unlink($file);
                }
                
                $filename = 'Bag Ledger Report.xlsx';
                $filepath = TIMESHEETS_PATH_EXPORT_FILE . $filename;
                $writer->writeToFile($filepath);
        
                echo json_encode([
                    'site_url' => site_url(),
                    'filename' => $filepath
                ]);
                die;
            }
        }
        
        public function GenerateDebitEntry()
        {
            $OrderID = $this->input->post('OrderID');
            $DispatchID = $this->input->post('DispatchID');
            $QcParameterList = $this->input->post('QcParameterList');
            $TotalAverage = $this->input->post('TotalAverage');
            $ItemID = $this->input->post('ItemID');
            $TotalWt = $this->input->post('TotalWt');
            
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
           
            $DispatchData = $this->FpoOrderModel->GetDispatchData($OrderID,$DispatchID);
            $FpoDetails = $this->FpoOrderModel->FpoDispatchOrderDetails($OrderID,$DispatchID);
            
            $SumDispatchQty = 0;
            $TotalAvgRate = 0;
            foreach($DispatchData as $value)
            {
                $TotalAvgRate += $value['NetWgt'] * $value['Rate'];
                $SumDispatchQty += $value['NetWgt'];
            }
            
            $AverageRate = $TotalAvgRate / $SumDispatchQty;
            
            $baseAmounts = [
                'Moisture' => 10,
                'Damaged' => 2,
                'Foreign Material' => 2,
            ];
            
            $SumDeductionAmt =0;
            foreach ($QcParameterList as $qc) {
                $paramName  = $qc['name'];
                $normalAvg  = (float) $qc['average'];
            
                foreach ($TotalAverage as $kirti) {
                    if ($kirti['name'] === $paramName) {
                        $kirtiAvg = (float) $kirti['val'];
                        
                        $diff = round($kirtiAvg - $normalAvg, 2);
                        
                        $baseAmt = isset($baseAmounts[$paramName]) ? $baseAmounts[$paramName] : 0;
                        
                        $finalValue = $baseAmt + $diff;
                        
                        $DeductionMatrix = $this->FpoOrderModel->GetDeductionMatrixData($paramName,$ItemID,$finalValue);
                        $deductionPercent = $DeductionMatrix->Deduction;
                        $itemParamID = $DeductionMatrix->ItemParameterID; 
                        
                        $deductionAmount = 0;
                        if ($itemParamID == 2) {
                            $deductionAmount = $TotalWt * $deductionPercent;
                        } else {
                            $deductionAmount = $AverageRate * $TotalWt * ($deductionPercent / 100);
                        }
                        $SumDeductionAmt += $deductionAmount;
                        break;
                    }
                }
            }
           
            
            $date= to_sql_date($FpoDetails->Transdate);
            $GetLastUniqueNo = $this->FpoOrderModel->GetLastUniqueNo($date);
            $LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
            
            $get_result_to_cur_date_journal = $this->FpoOrderModel->get_result_to_cur_date_journal($date);
            
            if(empty($get_result_to_cur_date_journal)){
                if($selected_company == 1){
                    $new_journalNumber = get_option('next_journal_number_for_kirti');
                }
                $new_voucher_number_journal = $new_journalNumber;
            }else{ 
                $count = count($get_result_to_cur_date_journal);
                $last_index = $count - 1;
                $new_voucher_number_journal = $get_result_to_cur_date_journal[$last_index]['VoucherID'];
                
                $incNo = (int) $new_voucher_number_journal - 1;
                $sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
                $this->db->query($sql);
                if ($this->db->affected_rows() > 0) {
                    $this->FpoOrderModel->increment_next_journal_number();
                }
            }
            
            $i=1;
            //Generate Credit Entry
            $credit_journal_voucher = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>date('Y-m-d H:i:s'),
                    "TransDate2" =>date('Y-m-d H:i:s'),
                    "VoucherID" =>$new_voucher_number_journal,
                    "AccountID" =>"FPOCOMM",
                    "TType" =>"C",
                    "CenterID" =>$FpoDetails->CenterID,
                    "CommodityID" =>$FpoDetails->ItemID,
                    "EntryFor" =>"2",
                    "PartyID" => $FpoDetails->PartyID,
                    "Amount" => $SumDeductionAmt,
                    "Narration" =>  "Commission Return Against ". $OrderID ."/ ". $FpoDetails->firstname ."  ".$FpoDetails->lastname,
                    "CounterAccount" =>$FpoDetails->FPOID,
                    "PassedFrom" =>"JOURNAL",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                    "UniquID" =>$LastUniqueID,
            );
            $creditEntry = $this->db->insert(db_prefix().'accountledger', $credit_journal_voucher);
            $i++;
            
            //Debit Entry
            $debit_journal_voucher = array(
                "PlantID" =>$selected_company,
                "Transdate" =>date('Y-m-d H:i:s'),
                "TransDate2" =>date('Y-m-d H:i:s'),
                "VoucherID" =>$new_voucher_number_journal,
                "AccountID" =>$FpoDetails->FPOID,
                "TType" =>"D",
                "CenterID" =>$FpoDetails->CenterID,
                "CommodityID" =>$FpoDetails->ItemID,
                "EntryFor" =>"2",
                "PartyID" => $FpoDetails->PartyID,
                "Amount" => $SumDeductionAmt,
                "Narration" =>  "Commission Return Against ". $OrderID ."/ ". $FpoDetails->firstname ."  ".$FpoDetails->lastname,
                "CounterAccount" =>"FPOCOMM",
                "PassedFrom" =>"JOURNAL",
                "OrdinalNo" =>$i,
                "UserID" =>$this->session->userdata('username'),
                "FY" =>$fy,
                "UniquID" =>$LastUniqueID,
            );
            $debitEntry = $this->db->insert(db_prefix().'accountledger', $debit_journal_voucher);
            
            if($creditEntry && $debitEntry)
            {
                if(empty($get_result_to_cur_date_journal)){
                    $this->FpoOrderModel->increment_next_journal_number();
                }
                
                $update_debitEntryFlag = array(
                        'FpoDebitEntry'=>'Y',
                    );
                $this->db->where('OrderID', $OrderID);
                $this->db->where('DispatchID', $DispatchID);
                $this->db->update(db_prefix() . 'FpoDispatchMaster', $update_debitEntryFlag);
                
                $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => true]));
            }
            else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => false]));
            }
        }
	}