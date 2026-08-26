<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class WHCharges extends AdminController
	{
		public function __construct(){
			parent::__construct();
			$this->load->Model('WHCharges_model');
			$this->load->model('GateControl_model');
			$this->load->helper('url', 'form');
			$this->load->model('sale_reports_model');
		}
		
		public function index()
		{
			if (!has_permission_new('StorageTradeList', '', 'view')) {
				access_denied('customers');
			}
			$data['title'] = 'Storage Trade List';
			$data['items'] = $this->WHCharges_model->getItemsData();
			$data['warehouses'] = $this->WHCharges_model->getWarehouseData();
			$data['centers'] = $this->WHCharges_model->getCenter();
			$data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$this->load->view('admin/WHCharges/DepositeTradeList',$data);
		}
		//======================== All Deposite Trade list =============================
		public function GetDepositeTrade()
		{
			$CenterID = $this->input->post('CenterID');
			$data = array(
			'CenterID'  => $CenterID,
			'ItemID'  => $this->input->post('ItemID'),
			'IsApprove'  => $this->input->post('IsApprove'),
			'TradeType' => $this->input->post('TradeType'),
			);
			
			$BookingList = $this->WHCharges_model->GetDepositeTrade($data);
			$html ='';
			$sr = 1;
			$TotalWeight = 0;
			foreach($BookingList as $value){
				
				if($value["IsApprove"] == 'Y'){
					$status = 'ACCEPTED';
				}
				if($value["IsApprove"] == 'Y' && $value["ClientApprove"] == 'N'){
					$status = "Waiting for party approval";
				}
				if($value["IsApprove"] == 'N'){
					$status = 'REJECTED';
				}
				if($value["IsApprove"] == 'NA'){
					$status = 'NO ACTION';
				}
				if($value["status"] == '2'){
					$status = "COMPLATED";
				}
				if($value["status"] == '3'){
					$status = "PARTIAL COMPLATED";
				}
				if($value['company'] != ''){
					$name = $value['company'];
				}
				else{
					$name = $value['firstname'].' '.$value['lastname'];
				}
				if (has_permission_new('StorageCharges', '', 'view')) {
    				$html.= '<tr class="GetDetails" data-id="'.$value["BookingID"].'" >';
    			}else{
    			    $html.= '<tr>';
    			}
				
				$html.= '<td style="text-align:center;">'.$sr.'</td>';
				$html.= '<td style="text-align:left;">'.$value["BookingID"].'</td>';
				$html.= '<td style="text-align:left;">'._d(substr($value['TransDate'],0,10)).'</td>';
				$html.= '<td style="text-align:left;">'.$name.'</td>';
				
				$html.= '<td style="text-align:left;">'.$value["CenterName"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["ItemName"].'</td>';
				$html.= '<td style="text-align:center;">'.$value["quantity"].' '.$value["unit"].'</td>';
				$TotalWeight += $value["quantity"];
				$html.= '<td style="text-align:center;">'.$status.'</td>';
				
				$html.= '</tr>';
				$sr++;
			}
			$html.= '<tr>';
			$html.= '<td style="text-align:right;" colspan="6"><b>Total</b></td>';
			$html .= '<td style="text-align:right">'.number_format($TotalWeight, 2, '.', '').'</td>';
			$html.= '<td style="text-align:right;" ></td>';
			$html.= '</tr>';
			echo json_encode($html);
		}
		
		public function StorageTradeDetails($BookingID)
		{ 
		    if (!has_permission_new('StorageCharges', '', 'view')) {
				access_denied('customers');
			}
			$data['title']  = "Storage Trade Details";
			$data['OrderDetails']  = $this->WHCharges_model->GetBookingListDetailsDB($BookingID);
			$data['OrderList']  = $this->WHCharges_model->GetInwardListByTradeID($BookingID);
			$data['LoanHistory'] = $this->WHCharges_model->GetLoanHistoryListByTradeID($BookingID);
			$data['OutwardList']  = $this->WHCharges_model->GetOutwardByTradeID($BookingID);
			$data['StockInventory']  = $this->WHCharges_model->GetLotWiseStockByTradeID($BookingID);
			/*echo "<pre>";
			print_r($data['LoanHistory']);
			die;*/
			if($data['OrderDetails']->TType == "D")
			{  $this->load->view('admin/WHCharges/WHChargesCalculationDetails',$data);   }
			else if($data['OrderDetails']->TType == "A")
			{  $this->load->view('admin/WHCharges/AnamatStorageTradeDetails',$data);   }
			else if($data['OrderDetails']->TType == "T")
			{  $this->load->view('admin/WHCharges/TradeFinanceStorageTradeDetails',$data);   }
		}
		
		public function export_storagetradelist()
        {
            if(!class_exists('XLSXReader_fin')){
                require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
            }
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            
            if($this->input->post()){
                
                $company_detail = $this->WHCharges_model->get_company_detail();
               	$data = array(
        			'CenterID'  => $CenterID,
        			'ItemID'  => $this->input->post('ItemID'),
        			'IsApprove'  => $this->input->post('IsApprove'),
        			'TradeType' => $this->input->post('TradeType'),
        			);
                 
                $result = $this->WHCharges_model->GetDepositeTrade($data);
                
                $writer = new XLSXWriter();
                
                $company_name = array($company_detail->company_name);
                $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 13);  
                $writer->writeSheetRow('Sheet1', $company_name);
    
                $address = $company_detail->address;
                $center_addr = array($address,);
                $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 13);  
                $writer->writeSheetRow('Sheet1', $center_addr);
                
                //filters
                $centerName = $this->input->post('centerText');
                $item_id = $this->input->post('ItemText');
                $is_approve = $this->input->post('statusText');
                $trade_type = $this->input->post('TradetypeText');
                
                if($centerName)
                { $centerfilter = $centerName;}
                else{ $centerfilter = "ALL"; }
                    
                if($item_id)
                { $itemfilter = $item_id;}
                else{ $itemfilter = "ALL"; }
                
                if($is_approve)
                { $statusfilter = $is_approve;}
                else{ $statusfilter = "ALL"; }
                
                if($trade_type)
                { $tradetypefilter = $trade_type;}
                else{ $tradetypefilter = "ALL"; }
                
                $filter_text = "Center Name: $centerfilter , Commodity Name: $itemfilter , Status: $statusfilter , Trade Type: $tradetypefilter";
                
                $writer->markMergedCell('Sheet1', 2, 0, 2, 13);
                $writer->writeSheetRow('Sheet1', ['Filters: ' . $filter_text]); 
                
                $set_col_tk = [];
                $set_col_tk["Sr.No."] = 'Sr. No.';
                $set_col_tk["BookingID"] = 'BookingID';
                $set_col_tk["Booking Date"] = 'Booking Date';
                $set_col_tk["TType"] = 'TType';
                $set_col_tk["Account Name"] =  'Account Name';
                $set_col_tk["Center Name"] =  'Center Name';
                $set_col_tk["Item Name"] = 'Item Name';
                $set_col_tk["Trade Qty"] = 'Trade Qty';
                $set_col_tk["Status"] = 'Status';
                $writer_header = $set_col_tk;
                $writer->writeSheetRow('Sheet1', $writer_header);
                $TotalWeight = 0;
                $i = 1;
                foreach ($result as $k => $value) {
                    if($value["IsApprove"] == 'Y'){
    					$status = 'ACCEPTED';
    				}
    				if($value["IsApprove"] == 'Y' && $value["ClientApprove"] == 'N'){
    					$status = "Waiting for party approval";
    				}
    				if($value["IsApprove"] == 'N'){
    					$status = 'REJECTED';
    				}
    				if($value["IsApprove"] == 'NA'){
    					$status = 'NO ACTION';
    				}
    				if($value["status"] == '2'){
    					$status = "COMPLATED";
    				}
    				if($value["status"] == '3'){
    					$status = "PARTIAL COMPLATED";
    				}
    				
    				if($value['company'] != ''){
    					$name = $value['company'];
    				}
    				else{
    					$name = $value['firstname'].' '.$value['lastname'];
    				}
                    
                    $list_add = [];
                    $list_add[] = $i;
                    $list_add[] = $value["BookingID"];
                    $list_add[] = _d(substr($value['TransDate'],0,10));
                    $list_add[] = $value["TType2"];
                    $list_add[] = $name;
                    $list_add[] = $value["CenterName"];
                    $list_add[] = $value["ItemName"];
                    $list_add[] = $value["quantity"].' '.$value["unit"];
                    $list_add[] = $status;
                    $TotalWeight += (float)$value["quantity"];
        
                    $list_add[] = $row_a;
                    
                    $writer->writeSheetRow('Sheet1', $list_add);
                    $i++;
                }
                
                $total_row = [];
                $total_row[] = ''; 
                $total_row[] = ''; 
                $total_row[] = ''; 
                $total_row[] = '';
                $total_row[] = '';
                $total_row[] = ''; 
                $total_row[] = 'Total'; 
                $total_row[] = number_format($TotalWeight, 2);
                $total_row[] = ''; 
                $total_row[] = ''; 
                
                $writer->writeSheetRow('Sheet1', $total_row);
        
                $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
                foreach($files as $file){
                    if(is_file($file)) {
                        unlink($file); 
                    }
                }
                $filename = 'Storage Trade List.xlsx';
                $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
                echo json_encode([
                    'site_url'          => site_url(),
                    'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
                ]);
                die;
            }
        }

}
?>