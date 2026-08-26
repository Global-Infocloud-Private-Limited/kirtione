<?php 
   defined('BASEPATH') or exit('No direct script access allowed');
   
   class Misc_Dashboard extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->load->model('MiscDashboard_model');
			$this->load->model('sale_reports_model');
			$this->load->model('order_model');
			$this->load->helper('url', 'form');
		}
		public function index()
		{
			if(!has_permission_new('MISDashboard', '', 'view')) {
				access_denied('GateControl Reports');  
			}
			$data['StaffList'] = $this->MiscDashboard_model->GetAllStaffList();
			$data['centers'] = $this->MiscDashboard_model->getAllCenters();
			$data['items'] = $this->MiscDashboard_model->getItems();
			$data['company_detail'] = $this->sale_reports_model->get_company_detail();
			//$data['Top5SellingItem'] = $this->MiscDashboard_model->get_top5Selling_item();
			//$data['Top5PurchaseItem'] = $this->MiscDashboard_model->get_topPurchase_item();
			$data['title'] = "Misc Dashboard";
			$this->load->view('admin/Misc_Dashboard/misc_Dashboard',$data);
		}
//======================= Get Center Wise Staff Wise Purchase =============================
	public function CenterWiseStaffWisePurchase()
	{
	    if(!has_permission_new('MISDashboard', '', 'view')) {
				access_denied('GateControl Reports');  
		}
		$filter_data = array(
	    "from_date"=>$this->input->post('from_date'),
		"to_date"=>$this->input->post('to_date'),
		"ItemID"=>$this->input->post('ItemID'),
		"CenterID"=>$this->input->post('CenterID'),
		"TType" => $this->input->post('TType'),
		'FeildOfficer'=>$this->input->post('FeildOfficer')
		);
		$result = $this->MiscDashboard_model->GetCenterWiseStaffWisePurchase($filter_data);
		echo json_encode($result);
	}
//=================== Center Wise Purchase =====================================
	public function CenterWisePurchase()
	{
	    if(!has_permission_new('MISDashboard', '', 'view')) {
			access_denied('GateControl Reports');  
		}
		$filter_data = array(
	    "from_date"=>$this->input->post('from_date'),
		"to_date"=>$this->input->post('to_date'),
		"ItemID"=>$this->input->post('ItemID'),
		"CenterID"=>$this->input->post('CenterID'),
		"TType" => $this->input->post('TType'),
		'FeildOfficer'=>$this->input->post('FeildOfficer')
		);
		$result = $this->MiscDashboard_model->GetCenterWisePurchase($filter_data);
		
		echo json_encode($result);
	}

//===================== Center Wise Purchase Chart =============================
	public function CenterWisePurchaseChart()
	{
	    if(!has_permission_new('MISDashboard', '', 'view')) {
			access_denied('GateControl Reports');  
		}
		$filter_data = array(
	    "ChartType"=>$this->input->post('ChartType'),
		"from_date"=>$this->input->post('from_date'),
		"to_date"=>$this->input->post('to_date'),
		"ItemID"=>$this->input->post('ItemID'),
		"CenterID"=>$this->input->post('CenterID'),
		"TType" => $this->input->post('TType'),
		"FeildOfficer"=>$this->input->post('FeildOfficer')
		);
		$result = $this->MiscDashboard_model->GetCenterWisePurchaseChart($filter_data); 
		$data = [
		'ChartData' => $result['ChartData'],
		];
		echo json_encode($data);
	}
	public function ExportCenterWisePurchase()
	{
	    if(!has_permission_new('MISDashboard', '', 'export')) {
			access_denied('GateControl Reports');  
		}
	    if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		
		if($this->input->post()){
			
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'TType' => $this->input->post('TType'),
            'ItemID' => $this->input->post('ItemID'),
            'CenterID' => $this->input->post('CenterID'),
            'FeildOfficer'=>$this->input->post('FeildOfficer')
			);
			$result = $this->MiscDashboard_model->GetCenterWisePurchase($data);
			
			$writer = new XLSXWriter();
			
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			
			
			$set_col_tk = [];
			$set_col_tk["Sr.no"] =  'Sr.no';
			$set_col_tk["Center Name"] = 'Center Name';
			$set_col_tk["QtyMt"] = 'QtyMt';
			
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$i=1;
			$Total = 0;
			foreach ($result as $k => $value) 
			{
				$list_add = [];
				$list_add[] = $i;
				$list_add[] = $value->CenterName;
				$list_add[] = $value->QtyMt;
				
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
				$i++;
				$Total += $value->QtyMt;
			}
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "Total";
			$list_add[] = $Total;
			
			$list_add[] = $row_a;
			$writer->writeSheetRow('Sheet1', $list_add);
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Center Wise Purchase List.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
            'site_url'          => site_url(),
            'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;
		}
	}
	
	public function ExportCenterWiseStaffWisePurchase()
	{
	    if(!has_permission_new('MISDashboard', '', 'export')) {
			access_denied('GateControl Reports');  
		}
	    if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		
		if($this->input->post()){
			
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'TType' => $this->input->post('TType'),
            'ItemID' => $this->input->post('ItemID'),
            'CenterID' => $this->input->post('CenterID'),
            'FeildOfficer'=>$this->input->post('FeildOfficer')
			);
			$result = $this->MiscDashboard_model->GetCenterWiseStaffWisePurchase($data);
			
			$writer = new XLSXWriter();
			
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			
			
			$set_col_tk = [];
			$set_col_tk["Sr.no"] =  'Sr. No.';
			$set_col_tk["Center Name"] = 'Center Name';
			$set_col_tk["Staff Name"] = 'Staff Name';
			$set_col_tk["QtyMt"] = 'QtyMt';
			
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$i=1;
			$Total = 0;
			foreach ($result as $k => $value) 
			{
				$list_add = [];
				$list_add[] = $i;
				$list_add[] = $value->CenterName;
				$list_add[] = $value->firstname . ' ' . $value->lastname;
				
				$list_add[] = $value->QtyMt;
				
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
				$i++;
				$Total += $value->QtyMt;
			}
			
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "Total";
			$list_add[] = "";
			
			$list_add[] = $Total;
			
			$list_add[] = $row_a;
			$writer->writeSheetRow('Sheet1', $list_add);
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'Center Wise Staff Wise Purchase List.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
            'site_url'          => site_url(),
            'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;
		}
	}

}