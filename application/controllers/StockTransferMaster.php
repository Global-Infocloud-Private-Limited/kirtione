<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class StockTransferMaster extends ClientsController
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
		$this->load->model('K1Stock_transfer_model');
        $this->load->helper('url', 'form');
    }
	
	public function index()
    {
        $LogInUser = $this->session->userdata('AccountID');
		$data['AccountID'] = $LogInUser;
        $data['company_detail'] = $this->ItemModel->get_company_detail();      
        
		$data['fromcentermaster'] = $this->K1Stock_transfer_model->GetFromCenterList();
		$data['tocentermaster'] = $this->K1Stock_transfer_model->GetToCenterList();
		
		$data['products'] = $this->K1Stock_transfer_model->GetPurchOrderItemList();		
		
		$data['title'] = "Stock Transfer Master";
        $this->data($data);
        $this->view('StockTransferMaster/StockTransferlist');
        $this->layout();
    }
	
	public function GetFilterDataStockTransferDetails()
    {
        $data = array(
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
			'FromCenterID'=>$this->input->post('FromCenterID'),
			'ToCenterID'=>$this->input->post('ToCenterID'),
			'Report_type'=>$this->input->post('Report_type'),
			'ItemID'=>$this->input->post('ItemID'),
			'AccountID'=>$this->input->post('AccountID'),
            'order_status'=> $this->input->post('order_status')                              
			
        );		
        $result = $this->K1Stock_transfer_model->getItemOrderDetailsDB($data); 		
        $redirectUrl = admin_url('K1Stock_transfer/AddEditStockTransfer');
		 
        $html = '';
        $html .= '<thead>';
        if($this->input->post('Report_type') =="1"){
            $html .= '<tr>';
            $html .= '<th style="text-align:left;">Sr No.</th>';
            $html .= '<th style="text-align:left;">TransferID</th>';			
            $html .= '<th style="text-align:left;">Transfer Date</th>';            
            $html .= '<th style="text-align:left;">Transfer From</th> ';  
			$html .= '<th style="text-align:left;">Transfer To</th> '; 			
            $html .= '<th style="text-align:left;">Party Name</th> ';
            $html .= '<th style="text-align:left;">Order Amt</th> ';
            $html .= '<th style="text-align:left;">Disc Amt</th> ';
            $html .= '<th style="text-align:left;">GST Amt</th>';
            $html .= '<th style="text-align:left;">Net Amt</th>';                                           
            $html .= '<th style="text-align:left;">Order Status</th>';                                                     
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody id="filter_data_table">';
            $data["Report_type"] = '2';
            $ItemData = $this->K1Stock_transfer_model->getItemOrderDetailsDB($data); 
        }else{
            $html .= '<tr>';
            $html .= '<th style="text-align:left;">Sr No.</th>';
            $html .= '<th style="text-align:left;">TransferID</th>';			
            $html .= '<th style="text-align:left;">Transfer Date</th>';    
			$html .= '<th style="text-align:left;">Transfer From</th>';			
            $html .= '<th style="text-align:left;">Transfer To</th>';           
            $html .= '<th style="text-align:left;">Party Name</th>'; 
            $html .= '<th style="text-align:left;">Item Name</th>'; 
            $html .= '<th style="text-align:left;">Quantity</th>'; 
            $html .= '<th style="text-align:left;">Item Amt</th>'; 
            $html .= '<th style="text-align:left;">Disc Amt</th>'; 
            $html .= '<th style="text-align:left;">GST Amt</th>';                      
            $html .= '<th style="text-align:left;">Net Amt</th>';   
            $html .= '<th style="text-align:left;">Order Status</th>'; 
            $html .= '</tr>';
        }
        $totalQtySum = 0;
        $totalValueAmtSum = 0;
        $totalDiscountAmtSum = 0;
        $totalTaxAmtSum = 0;
        $totalNetAmtSum = 0;
    	foreach($result as $key=>$value)
    	{
            if($value['OrderStatus'] == "F") {
                $OrderStat = "Completed";
            } elseif ($value['OrderStatus'] == "C") {
                $OrderStat = "Cancelled";
            }	
						
            if($this->input->post('Report_type') == "1"){
                $ItemTotal = 0;
                $ItemDiscAmt = 0;
                $ItemGstAmt = 0;
                $ItemNetTotal = 0;
				$redirectUrl = base_url()."StockTransferMaster/StockTransferDetails";
                foreach($ItemData as $key1=>$val2){
                    if($value["TransferID"] == $val2["OrderID"]){
                        $gstamt = $val2['cgstamt'] + $val2['sgstamt'] + $val2['igstamt'];
                        $ItemTotal += $val2["OrderAmt"];
                        $ItemDiscAmt += $val2["DiscAmt"];
                        $ItemGstAmt += $gstamt;
                        $ItemNetTotal += $val2["NetOrderAmt"];
                    }
                }
				$html .= '<tr onclick="window.open(\'' . $redirectUrl . '/' . $value['TransferID'] . '\', \'_blank\');">';
                //$html .= '<tr onclick="window.open(\''.$redirectUrl.'?OrderId='.$value["PurchID"].'\', \'_blank\');">';           
                $html .= '<td>'.($key+1).'</td>';   
       	        $html .= '<td>'.$value["TransferID"].'</td>';					
                $html .= '<td>'._d(substr($value["TransferDate"],0,10)).'</td>';           	
      	        $html .= '<td>'.$value['fromcentername'].'</td>';  
				$html .= '<td>'.$value['tocentername'].'</td>';   				
                $html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
     	        $html .= '<td style="text-align:right;">' . number_format($ItemTotal, 2, '.', '') . '</td>';
                $html .= '<td style="text-align:right;">' . number_format($ItemDiscAmt, 2, '.', '') . '</td>';
                $html .= '<td style="text-align:right;">' . number_format($ItemGstAmt, 2, '.', '') . '</td>';
                $html .= '<td style="text-align:right;">' . number_format($ItemNetTotal, 2, '.', '') . '</td>'; 	    
                $html .= '<td>'.$OrderStat.'</td>';	        
    	        $html .= '</tr>'; 
    	        $totalValueAmtSum += $ItemTotal;
    	        $totalDiscountAmtSum += $ItemDiscAmt;
    	        $totalTaxAmtSum += $ItemGstAmt;
    	        $totalNetAmtSum += $ItemNetTotal;
            }else{
                //$html .= '<tr onclick="window.open(\''.$redirectUrl.'?OrderId='.$value["PurchID"].'\', \'_blank\');">';           
                $html .= '<tr onclick="window.open(\'' . $redirectUrl . '/' . $value['TransferID'] . '\', \'_blank\');">';
				$html .= '<td>'.($key+1).'</td>';   
                $html .= '<td>'.$value["TransferID"].'</td>';	
                $html .= '<td>'._d(substr($value["TransDate"],0,10)).'</td>';      
                $html .= '<td>'.$value['fromcentername'].'</td>';  
				$html .= '<td>'.$value['tocentername'].'</td>';        	       
                $html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
                $html .= '<td>'.$value['ProductName'].'</td>';
                $gstamt = $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'];
                $html .= '<td style="text-align:right;">' . number_format($value['OrderQty'], 2, '.', '') . '</td>';
                $totalQtySum += $value['OrderQty'];
                $html .= '<td style="text-align:right;">' . number_format($value['OrderAmt'], 2, '.', '') . '</td>';
                $totalValueAmtSum += $value['OrderAmt'];
                $html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
                $totalDiscountAmtSum += $value['DiscAmt'];
                $html .= '<td style="text-align:right;">' . number_format($gstamt, 2, '.', '') . '</td>';
                $totalTaxAmtSum += $gstamt;
                $html .= '<td style="text-align:right;">' . number_format($value['NetOrderAmt'], 2, '.', '') . '</td>';
                $totalNetAmtSum += $value['NetOrderAmt'];
                $html .= '<td>'.$OrderStat.'</td>';                     
                $html .= '</tr>';
            }
    	}
    	if($this->input->post('Report_type') == "1"){
    	    $html .= '<tr>';
            $html .= '<td colspan="6" style="text-align:right;"><strong>Total</strong></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalValueAmtSum, 2, '.', '') . '</strong></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalDiscountAmtSum, 2, '.', '') . '</strong></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalTaxAmtSum, 2, '.', '') . '</strong></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalNetAmtSum, 2, '.', '') . '</strong></td>';
            $html .= '<td></td>'; 
            $html .= '</tr>';
    	}else{
    	    $html .= '<tr>';
	        $html .= '<td colspan="7" style="text-align:right;"><strong>Total</strong></td>';        
           	$html .= '<td style="text-align:right;"><strong>' . number_format($totalQtySum, 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalValueAmtSum, 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalDiscountAmtSum, 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalTaxAmtSum, 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($totalNetAmtSum, 2, '.', '') . '</td>';
     	    $html .= '<td></td>'; 
            $html .= '</tr>';
    	}
    	$html .= '</body>';
        echo $html;
    }
	
	public function export_StockTransferlist()
	{		
        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }

        require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        if ($this->input->post()) 
        {
            $company_detail = $this->K1Stock_transfer_model->get_company_detail();

            $post_data = $this->input->post();				
			
			$from_date = $post_data['from_date'];
            $to_date = $post_data['to_date'];
            $Fromcenter_name = $post_data['FromCenterName'];
			$tocenter_name = $post_data['ToCenterName'];
            $report_type =  $post_data['ReportTypetext'];
            $item = $post_data['ItemName'];
            $status = $post_data['order_statusText'];            
			$Report_type =  $post_data['Report_type'];	
				
            $result = $this->K1Stock_transfer_model->getItemOrderDetailsDB($post_data);

            $writer = new XLSXWriter();

            $company_name = array($company_detail->company_name);

            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  

            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;

            $center_addr = array($address, );	  
	    
			$filters = "From date: " . $from_date . ", To date: " . $to_date . ", From Center: " . $Fromcenter_name . ",To Center: " . $tocenter_name . ", Report Type: " . $report_type .
                        ", Item: " . $item . ", Order Status: " . $status;
	    
			$filter_row = array($filters);
            
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells

            $writer->writeSheetRow('Sheet1', $center_addr);

			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 14);  //merge cells	   

            $writer->writeSheetRow('Sheet1', $filter_row);

            $set_col_tk = [];
 	        $post_data2 = $post_data;
            if ($post_data['Report_type'] == "1") {    
                $post_data2["Report_type"] = '2';            
                $ItemData = $this->K1Stock_transfer_model->getItemOrderDetailsDB($post_data2);
                $set_col_tk["TransferID"] = 'PO.No';
                $set_col_tk["TransferDate"] = 'PO Date';
                $set_col_tk["TransferFrom"] = 'From Center';
				$set_col_tk["TransferTo"] = 'To Center';
                $set_col_tk["company"] = 'Party Name';
                $set_col_tk["ItemTotal"] = 'Order Amt';
                $set_col_tk["ItemDiscAmt"] = 'Disc Amt';
                $set_col_tk["ItemGstAmt"] = 'GST Amt';
                $set_col_tk['ItemNetTotal'] = 'Net Amt';               
                $set_col_tk['status'] = 'Order Status';
            }else {  
                $set_col_tk["TransferID"] = 'PO.No';
                $set_col_tk["TransferDate"] = 'PO Date';
                $set_col_tk["TransferFrom"] = 'From Center';
				$set_col_tk["TransferTo"] = 'To Center';
                $set_col_tk["company"] = 'Party Name';
                $set_col_tk["ProductName"] = 'Item Name';
                $set_col_tk["itemqty"] = 'Quantity';
                $set_col_tk["OrderAmt"] = 'Item Amt';
                $set_col_tk["discountamt"] = 'Disc Amt';
                $set_col_tk["gstamt"] = 'GST Amt';
                $set_col_tk["NetOrdAmt"] = 'Net Amt';
                $set_col_tk['status'] = 'Order Status';
            }  		 

            $writer_header = $set_col_tk;

            $writer->writeSheetRow('Sheet1', $writer_header);

			$sum_totalorderamt =0;
            $sum_DiscAmt = 0;
            $sum_total_gst = 0;           
            $sum_total_netamt = 0;

			$sum_itemqty = 0;
            $sum_itemrate=0;
            $sum_itemdiscamt = 0;
            $sum_itemgstamt=0;
            $sum_itemnetOrderamt=0;

            foreach ($result as $k => $value) 
            {  
				if($value['OrderStatus'] == "F") {
                    $OrderStat = "Completed";
                } elseif ($value['OrderStatus'] == "C") {
                    $OrderStat = "Cancelled";
                }

                if ($post_data['Report_type'] == "1") 
                {
                    $ItemTotal = 0;
                    $ItemDiscAmt = 0;
                    $ItemGstAmt = 0;
                    $ItemNetTotal = 0;
                    foreach($ItemData as $key1=>$val2){
                        if($value["TransferID"] == $val2["OrderID"]){
                            $gstamt = $val2['cgstamt'] + $val2['sgstamt'] + $val2['igstamt'];
                            $ItemTotal += $val2["OrderAmt"];
                            $ItemDiscAmt += $val2["DiscAmt"];
                            $ItemGstAmt += $gstamt;
                            $ItemNetTotal += $val2["NetOrderAmt"];
                        }
                    }
                    // For Bill Wise Report
                    $list_add = [];  

                    $list_add[] = $value["TransferID"];
                    $list_add[] = _d(substr($value["TransferDate"],0,10));
                    $list_add[] = $value["fromcentername"];
					$list_add[] = $value["tocentername"];
                    $list_add[] = $value["company"];
                    $list_add[] = number_format($ItemTotal, 2, '.', '');        
                    $list_add[] = number_format($ItemDiscAmt, 2, '.', '');         
                    $list_add[] = number_format($ItemGstAmt, 2, '.', '');       
                    $list_add[] = number_format($ItemNetTotal, 2, '.', ''); 
                    $list_add[] = $OrderStat;   

                    $sum_totalorderamt += $ItemTotal;
                    $sum_DiscAmt += $ItemDiscAmt;
                    $sum_total_gst += $ItemGstAmt;
                    $sum_total_netamt += $ItemNetTotal;   

                    $writer->writeSheetRow('Sheet1', $list_add); 			
			
                }else  
                {                   
                   
                    $gstamt = $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'];
                    $list_add = [];   

                    $list_add[] = $value["TransferID"];
                    $list_add[] = _d(substr($value["TransferDate"],0,10));
                    $list_add[] = $value["fromcentername"];
					$list_add[] = $value["tocentername"];
                    $list_add[] = $value["company"];
                    $list_add[] = $value['ProductName'];
                    $list_add[] = number_format($value["OrderQty"], 2, '.', '');              
                    $list_add[] = number_format($value["OrderAmt"], 2, '.', '');              
                    $list_add[] = number_format($value["DiscAmt"], 2, '.', '');                
                    $list_add[] = number_format($gstamt, 2, '.', '');  
                    $list_add[] = number_format($value["NetOrderAmt"], 2, '.', ''); 
                    $list_add[] = $OrderStat;   

                    $sum_itemqty += $value["OrderQty"];
                    $sum_itemrate += $value["OrderAmt"];
                    $sum_itemdiscamt += $value["DiscAmt"];
                    $sum_itemgstamt +=  $gstamt;
                    $sum_itemnetOrderamt += $value["NetOrderAmt"];
                    $writer->writeSheetRow('Sheet1', $list_add);   	
                }                   
            }	   
			
			if ($post_data['Report_type'] == "1") {
				$sum_row = [];
				$sum_row[] = ''; 
				$sum_row[] = '';
				$sum_row[] = ''; 
				$sum_row[] = ''; 
				$sum_row[] = ''; 
				$sum_row[] = number_format($sum_totalorderamt, 2, '.', '');       
				$sum_row[] = number_format($sum_DiscAmt, 2, '.', '');       
				$sum_row[] = number_format($sum_total_gst, 2, '.', '');    
				$sum_row[] = number_format($sum_total_netamt, 2, '.', '');                    
				$sum_row[] = ''; 
			}else{
                $sum_row = [];
                $sum_row[] = ''; 
                $sum_row[] = '';
                $sum_row[] = ''; 
                $sum_row[] = ''; 
  		        $sum_row[] = ''; 
				$sum_row[] = ''; 
                $sum_row[] = number_format($sum_itemqty, 2, '.', '');       
                $sum_row[] = number_format($sum_itemrate, 2, '.', '');       
                $sum_row[] = number_format($sum_itemdiscamt, 2, '.', '');    
                $sum_row[] = number_format($sum_itemgstamt, 2, '.', '');     
                $sum_row[] = number_format($sum_itemnetOrderamt, 2, '.', '');        
                $sum_row[] = ''; 
			}

			$writer->writeSheetRow('Sheet1', $sum_row);
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

            foreach ($files as $file) {

                if (is_file($file)) {

                    unlink($file);
                }
            }

            $filename = 'StockTransferReport.xlsx';

            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

            echo json_encode([

                'site_url' => site_url(),

                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

            ]);
            die;
        }
    } 
	
	public function StockTransferDetails()
	{
		$LogInUser = $this->session->userdata('AccountID');
		$data['AccountID'] = $LogInUser;
        $data['company_detail'] = $this->ItemModel->get_company_detail(); 

		$trfNumber = $this->uri->segment(3); 
		$data['TransferDetails'] = $this->K1Stock_transfer_model->GetTrfdetailsNumberwise($trfNumber);	
		$data['TransferHistoryDetails'] = $this->K1Stock_transfer_model->GetTrfdetailsItemwise($trfNumber);	
		
		$data['fromcentermaster'] = $this->K1Stock_transfer_model->GetFromCenterList();
		$data['tocentermaster'] = $this->K1Stock_transfer_model->GetToCenterList();
		
		$data['products'] = $this->K1Stock_transfer_model->GetPurchOrderItemList();		
		
		$data['title'] = "Stock Transfer Details";
        $this->data($data);
        $this->view('StockTransferMaster/StockTransferDetails');
        $this->layout();	
	}
}