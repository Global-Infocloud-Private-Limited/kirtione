<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class K1Stock_transfer extends AdminController
	{
		private $not_importable_fields = ['id'];
		public function __construct()
		{
			parent::__construct();
			$this->load->model('K1Stock_transfer_model');
			$this->load->model('PurchaseModel');
			$this->load->model('KirtiOneOrderModel');
			$this->load->model('Challan_model');
		}
		public function AddEditStockTransfer($TrfNumber = '')
		{
			if (!has_permission_new('KirtiOneStockTransfer', '', 'view')) {
				access_denied('PurchaseModel');
			}
			if ($this->input->post())
			{
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($TrfNumber == '') {
					if (!has_permission_new('KirtiOneStockTransfer', '', 'create')) {
						access_denied('KirtiOneStockTransfer');
					}
					$id = $this->K1Stock_transfer_model->AddKirtiOneStockTransfer($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('K1Stock_transfer/AddEditStockTransfer'));
					}
					}else{
					if (!has_permission_new('KirtiOneStockTransfer', '', 'edit')) {
						access_denied('KirtiOneStockTransfer');
					}
					$id = $this->K1Stock_transfer_model->UpdateKirtiOneStockTransfer($pur_order_data,$TrfNumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('K1Stock_transfer/AddEditStockTransfer'));
					}
				}
			}
			if ($TrfNumber == '') {
				$title = _l('create_stock_transfer_order');
				$data['item_code'] = array();
			}else{
				$StockDetails = $this->K1Stock_transfer_model->GetStockDetails($TrfNumber);
				$data['stock_details'] = $StockDetails;
				$StockItemList = $this->K1Stock_transfer_model->GetStockItemList($TrfNumber);
				$data['pur_order_detail'] = json_encode($StockItemList);
				$data['item_code'] = $this->K1Stock_transfer_model->GetCenterWiseItems($StockDetails->TransferFrom,$TrfNumber);
				/*echo "<pre>";
				print_r($StockItemList);
				print_r($data['item_code']);
				die;*/
				$title = "Edit Stock Transfer Order";
			}
			// $data['item_code'] = $this->K1Stock_transfer_model->get_items_code();
			$centermaster = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $centermaster;
			$SubActGroupID  = 1000006;
			$where = '(SubActGroupID="'.$SubActGroupID.'")';
			$clients = $this->K1Stock_transfer_model->get_all_data($tablename="tblclients",$where);
			$data['clients'] = $clients;
			$this->load->view('admin/K1Stock_transfer/AddEditStockTransfer',$data);
		}
		public function GetItemDetails($ItemID)
		{
			$ItemDetails = $this->K1Stock_transfer_model->GetItemDetails($ItemID);
			echo json_encode($ItemDetails);
		}
		public function load_data_for_stocktransfer()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$StockList = $this->K1Stock_transfer_model->load_data_for_stockkirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($StockList as $key=>$val)
			{
				if($val['OrderStatus'] == "F")
				{ $orderstat = "Completed"; }
				else if($val['OrderStatus'] == "C")
				{ $orderstat = "Cancelled"; }
				$url = admin_url()."K1Stock_transfer/AddEditStockTransfer/".$val["TransferID"];
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["TransferID"].'</td>';
				$html .= '<td style="text-align:center;">'.$val["company"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["TransferDate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["FromCenter"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["ToCenter"].'</td>';
				$html .= '<td style="text-align:left;">'.$orderstat.'</td>';
				$html .= '<td style="text-align:right;">'.$val["Purchamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Discamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Invamt"].'</td>';
				$html .= '</tr>';
				$TotalPurchAmt += $val["Purchamt"];
				$TotalDiscAmt += $val["Discamt"];
				$TotalCgstAmt += $val["cgstamt"];
				$TotalSgstAmt += $val["sgstamt"];
				$TotalIgstAmt += $val["igstamt"];
				$TotalInvAmt += $val["Invamt"];
			}
			$html .= '<tr>';
			$html .= '<td colspan="5" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalPurchAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function StockTransferList()
		{
			if (!has_permission_new('KirtiOneStockTransferList', '', 'view')) {
				access_denied('Invoice Items');
			}
			$data['fromcentermaster'] = $this->K1Stock_transfer_model->GetFromCenterList();
			$data['tocentermaster'] = $this->K1Stock_transfer_model->GetToCenterList();
			$data['products'] = $this->K1Stock_transfer_model->GetPurchOrderItemList();
			$data['clients'] = $this->K1Stock_transfer_model->GetAccountList();
			$this->load->view('admin/K1Stock_transfer/StockTransferList',$data);
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
		public function CancelItemsStockOrderWise()
		{
			$TransferId = $this->input->post('TransferId');
			if($TransferId !="")
			{
				$where = '(TransferID="'.$TransferId.'")';
				$stockDetails = $this->K1Stock_transfer_model->get_data($tablename="tblK1stocktransfermaster",$where);
				$updateOrderData = array(
				'OrderStatus'=>"C",
                'Purchamt'=>'0.00',
                'Discamt'=>'0.00',
				'cgstamt'=>'0.00',
				'sgstamt'=>'0.00',
                'igstamt'=>'0.00',
				'RoundOffAmt'=>'0.00',
				'Invamt'=>'0.00',
				'ItCount'=>'0'
				);
				$cancelOrder = $this->K1Stock_transfer_model->edit_data($tablename="tblK1stocktransfermaster",$where,$updateOrderData);
				$wh = '(OrderID="'.$TransferId.'")';
				$updateItemData = array(
                'TransDate2'=>date('Y-m-d h:i:s'),
                // 'TType2'=>"CANCEL",
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
				$cancelItemdata = $this->K1Stock_transfer_model->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);
			}
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
		public function CancelItemsStockAdjOrderWise()
		{
			$AdjId = $this->input->post('AdjId');
			if($AdjId !="")
			{
				$where = '(AdjustmentID="'.$AdjId.'")';
				$stockDetails = $this->K1Stock_transfer_model->get_data($tablename="tblK1stockadjustmentmaster",$where);
				$updateOrderData = array(
				'OrderStatus'=>"C",
                'Purchamt'=>'0.00',
                'Discamt'=>'0.00',
				'cgstamt'=>'0.00',
				'sgstamt'=>'0.00',
                'igstamt'=>'0.00',
				'RoundOffAmt'=>'0.00',
				'Invamt'=>'0.00',
				'ItCount'=>'0'
				);
				$cancelOrder = $this->K1Stock_transfer_model->edit_data($tablename="tblK1stockadjustmentmaster",$where,$updateOrderData);
				$wh = '(OrderID="'.$AdjId.'")';
				$updateItemData = array(
                'TransDate2'=>date('Y-m-d h:i:s'),
                // 'TType2'=>"CANCEL",
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
				$cancelItemdata = $this->K1Stock_transfer_model->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Adjustment cancel successfully']);
			}
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
		public function export_StockTransferlist()
		{
			if (!has_permission_new('KirtiOneStockTransferList', '', 'export')) {
				access_denied('KirtiOneStockTransfer');
			}
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
				$AccountName = $post_data['Partyname'];
				$Report_type =  $post_data['Report_type'];
				$result = $this->K1Stock_transfer_model->getItemOrderDetailsDB($post_data);
				$writer = new XLSXWriter();
				$company_name = array($company_detail->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);
				$writer->writeSheetRow('Sheet1', $company_name);
				$address = $company_detail->address;
				$center_addr = array($address, );
				$filters = "From date: " . $from_date . ", To date: " . $to_date . ", From Center: " . $Fromcenter_name . ",To Center: " . $tocenter_name . ", Report Type: " . $report_type .
				", Item: " . $item . ", Party: " . $AccountName . ", Order Status: " . $status;
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
		public function GetItemListData()
		{
			$CenterID = $this->input->post('CenterID');
			$item_list = $this->K1Stock_transfer_model->GetCenterWiseItems($CenterID);
			echo json_encode($item_list);
		}
		public function AddEditStockAdjustment($AdjNumber = '')
		{
			if (!has_permission_new('KirtiOneStockAdjustment', '', 'view')) {
				access_denied('KirtiOneStockAdjustment');
			}
			if ($this->input->post())
			{
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($AdjNumber == '') {
					if (!has_permission_new('KirtiOneStockAdjustment', '', 'create')) {
						access_denied('KirtiOneStockAdjustment');
					}
					$id = $this->K1Stock_transfer_model->AddKirtiOneStockAdjustment($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('K1Stock_transfer/AddEditStockAdjustment'));
					}
					}else{
					if (!has_permission_new('KirtiOneStockAdjustment', '', 'edit')) {
						access_denied('KirtiOneStockAdjustment');
					}
					$id = $this->K1Stock_transfer_model->UpdateKirtiOneStockAdjustment($pur_order_data,$AdjNumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('K1Stock_transfer/AddEditStockAdjustment'));
					}
				}
			}
			if ($AdjNumber == '') {
				$title = "Create Stock Ajustment";
				$data['item_code'] = array();
				}else{
				$StockDetails = $this->K1Stock_transfer_model->GetStockAdjDetails($AdjNumber);
				$data['stock_details'] = $StockDetails;
				$StockItemList = $this->K1Stock_transfer_model->GetStockAdjItemList($AdjNumber);
				$data['pur_order_detail'] = json_encode($StockItemList);
				$data['item_code'] = $this->K1Stock_transfer_model->GetCenterWiseItems($StockDetails->CenterID,$AdjNumber);
				$title = "Edit Stock Ajustment";
			}
			// $data['item_code'] = $this->K1Stock_transfer_model->get_items_code();
			$centermaster = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $centermaster;
			$SubActGroupID  = 1000006;
			$where = '(SubActGroupID="'.$SubActGroupID.'")';
			$clients = $this->K1Stock_transfer_model->get_all_data($tablename="tblclients",$where);
			$data['clients'] = $clients;
			$this->load->view('admin/K1Stock_transfer/AddEditStockAdjustment',$data);
		}
		public function load_data_for_stockadjustment()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$StockList = $this->K1Stock_transfer_model->load_data_for_stockadjkirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($StockList as $key=>$val)
			{
				if($val['OrderStatus'] == "F")
				{ $orderstat = "Completed"; }
				else if($val['OrderStatus'] == "C")
				{ $orderstat = "Cancelled"; }
				$url = admin_url()."K1Stock_transfer/AddEditStockAdjustment/".$val["AdjustmentID"];
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["AdjustmentID"].'</td>';
				$html .= '<td style="text-align:center;">'.$val["company"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["AdjustmentDate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$orderstat.'</td>';
				$html .= '<td style="text-align:right;">'.$val["Purchamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Discamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["cgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["sgstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["igstamt"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Invamt"].'</td>';
				$html .= '</tr>';
				$TotalPurchAmt += $val["Purchamt"];
				$TotalDiscAmt += $val["Discamt"];
				$TotalCgstAmt += $val["cgstamt"];
				$TotalSgstAmt += $val["sgstamt"];
				$TotalIgstAmt += $val["igstamt"];
				$TotalInvAmt += $val["Invamt"];
			}
			$html .= '<tr>';
			$html .= '<td colspan="5" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalPurchAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function generateEwayBill()
		{
		    try{
    		    $postData = $this->input->post();
    		    $TrfNumber = $postData['TransferID'];
    			$fy = $this->session->userdata('finacial_year');
    			$selected_company = $this->session->userdata('root_company');
    			$company_details = $this->Challan_model->get_company_detail($selected_company);
    			$StockData = $this->K1Stock_transfer_model->GetStockDetails($TrfNumber);
				$StockItemList = $this->K1Stock_transfer_model->GetStockItemList($TrfNumber);
				$item_code = $this->K1Stock_transfer_model->GetCenterWiseItems($StockData->TransferFrom,$TrfNumber);
    			// echo '<pre>';print_r($StockItemList); die;
    			// Step 1: Authentication - Get AuthToken
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
    			$CenterDetails = $this->Challan_model->fetchCenterDetails($StockData->TransferFrom);
    			$ToCenterDetails = $this->Challan_model->fetchCenterDetails($StockData->TransferTo);
    			if(empty($StockData)){
    			    $return = false;
    			    $Message = "Details not found. please reload page and try again";
    			}elseif(!empty($StockData->EwayBillNo)){
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
    			    $Message = "Center Details not available please connect to admin";
    			}elseif(empty($ToCenterDetails->GSTNo)){
    			    $return = false;
    			    $Message = "Center GST No not available please update center details";
    			}elseif(empty($ToCenterDetails->CenterName)){
    			    $return = false;
    			    $Message = "Center Name not available please update center details";
    			}elseif(empty($ToCenterDetails->address)){
    			    $return = false;
    			    $Message = "Center address not available please update center details";
    			}elseif(empty($ToCenterDetails->city_name)){
    			    $return = false;
    			    $Message = "Center city name not available please update center details";
    			}elseif(empty($ToCenterDetails->statecode)){
    			    $return = false;
    			    $Message = "Center state name not available please update center details";
    			}elseif(empty($ToCenterDetails->pincode)){
    			    $return = false;
    			    $Message = "Center pincode not available please update center details";
    			}elseif(empty($StockItemList)){
    			    $return = false;
    			    $Message = "Items Details not available please connect to admin";
    			}else{
    			    $SalesID = $StockData->SalesID;
    				$Ph = $StockData->phonenumber;
    				// Outward supply: fromGstin must match auth header GSTIN (NIC error 359)
    				$authGstin = strtoupper(preg_replace('/\s+/', '', trim($authHeaders['gstin'])));
    				$ewayData = [
    					"supplyType"        => "O",
    					"subSupplyType"     => "8",
    					"subSupplyDesc"     => "Stock Transfer",
    					"docType"           => "CHL",
    					"docNo"             => $StockData->TransferID,
    					"docDate"           => date("d/m/Y"),
    					"fromGstin"         => $authGstin,
    					"fromTrdName"       => $CenterDetails->CenterName,
    					"fromAddr1"         => $CenterDetails->address,
    					"fromAddr2"         => " ",
    					"fromPlace"         => $CenterDetails->city_name,
    					"fromPincode"       => (int) $CenterDetails->pincode,
    					"actFromStateCode"  => (int) sprintf('%02d', $CenterDetails->statecode),
    					"fromStateCode"     => (int) sprintf('%02d', $CenterDetails->statecode),
    					"toGstin"           => $authGstin,
    					"toTrdName"         => $ToCenterDetails->CenterName,
    					"toAddr1"           => $ToCenterDetails->address,
    					"toAddr2"           => " ",
    					"toPlace"           => $ToCenterDetails->city_name,
    					"toPincode"         => (int) $ToCenterDetails->pincode,
    					"actToStateCode"    => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"toStateCode"       => (int) sprintf('%02d', $ToCenterDetails->statecode),
    					"transactionType"   => 1,
    					"otherValue"        => 0,
    					"totalValue"        => floatval($StockData->Purchamt - $StockData->Discamt),
    					"cgstValue"         => floatval($StockData->cgstamt),
    					"sgstValue"         => floatval($StockData->sgstamt),
    					"igstValue"         => floatval($StockData->igstamt),
    					"cessValue"         => 0,
    					"cessNonAdvolValue" => 0,
    					"totInvValue"       => floatval($StockData->Invamt),
    					"transporterId"     => "05AAACG0904A1ZL",
    					"transporterName"   => "",
    					"transDocNo"        => "12",
    					"transMode"         => "1",
    					"transDistance"     => "0",// hard code value
    					"transDocDate"      => date("d/m/Y"),
    					"vehicleNo"         => $StockData->VehicleNo,
    					"vehicleType"       => "R",
    					"itemList"          => []
    				];
    				$sl = 1;
    				foreach ($StockItemList as $item) {
    					$ewayData['itemList'][] = [
    					"productName"   => $item['ProductName'],
    					"productDesc"   => $item['ProductName'],
    					"hsnCode"       => $item['hsn_code'],
    					"quantity"      => floatval($item['PackingQty']),
    					"qtyUnit"       => substr($item['PurchUnit'], 0, 3),
    					"cgstRate"      => floatval($item['cgst']),
    					"sgstRate"      => floatval($item['sgst']),
    					"igstRate"      => floatval($item['igst']),
    					"cessRate"      => 0.00,
    					"taxableAmount"=> floatval($item['Netamt'])
    					];
    					$sl++;
    				}
        // 			echo '<pre>'; print_r($ewayData); die;
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
    					$this->db->where('TransferID', $TrfNumber);
    					$this->db->update(db_prefix().'K1stocktransfermaster', [
    				// 	'ewaybill_cancelled' => null,
    				// 	'EwayCancelRemark' => null,
    					'EwayBillNo' => $ewayResData['data']['ewayBillNo'],
    				// 	'ewaybill_date' => $ewayResData['data']['ewayBillDate'],
    				// 	'ewaybill_valid_upto' => $ewayResData['data']['validUpto']
    					]);
    					$return = true;
    					$Message .= "E-Way Bill Is Generated Successfully OrderID ".$StockData->TransferID." . ";
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
    	//========================== Kirti One Sales Return Report page ==========================
    	public function AdjustmentReport(){
    		if (!has_permission_new('StockAdjustmentReport', '', 'view')) {
    			access_denied('Invoice Items');
    		}
    		$data['FY'] = $this->session->userdata('finacial_year');
    		$data['centermaster'] = $this->K1Stock_transfer_model->GetStockAdjustmentCenterList();
    		$data['products'] = $this->K1Stock_transfer_model->GetStockAdjustmentItemList();
    		$data['clients'] = $this->K1Stock_transfer_model->GetStockAdjustmentPartyList();
    		$data['company_detail'] = $this->K1Stock_transfer_model->get_company_detail();
    		$this->load->view('admin/K1Stock_transfer/StockAdjustmentReport',$data);
    	}
    	//========================== Filter result for Kirti One Sales Return Report ==========================
    	public function GetAdjustmentReportFilterData(){
    		$data = array(
    			'from_date' => $this->input->post('from_date'),
    			'to_date' => $this->input->post('to_date'),
    			'AccountID'=>$this->input->post('AccountID'),
    			'CenterID'=>$this->input->post('CenterID'),
    			'ItemID'=>$this->input->post('ItemID'),
    			'ReportType'=>$this->input->post('Report_type'),
    			'AdjustmentType'=>$this->input->post('adjustmenttype'),
    		);
    		$result = $this->K1Stock_transfer_model->getAdjustmentReportFilter($data);
    		// echo json_encode($result); die;
    		$html = '';
    		if($data['ReportType'] == '1'){ // Report type bill
    			$html .= '<thead>';
    			$html .= '<tr>';
    			$html .= '<th style="text-align:left;">Sr No.</th>';
    			$html .= '<th style="text-align:left;">Adjustment No</th>';
    			$html .= '<th style="text-align:left;">Adjustment Date</th>';
    			$html .= '<th style="text-align:left;">Party</th>';
    			$html .= '<th style="text-align:left;">Center</th>';
    			$html .= '<th style="text-align:left;">Type</th>';
    			$html .= '<th style="text-align:left;">Purch Amt</th>';
    			$html .= '<th style="text-align:left;">Disc Amt</th>';
    			$html .= '<th style="text-align:left;">CGST Amt</th>';
    			$html .= '<th style="text-align:left;">SGST Amt</th>';
    			$html .= '<th style="text-align:left;">IGST Amt</th>';
    			$html .= '<th style="text-align:left;">Round Off</th>';
    			$html .= '<th style="text-align:left;">Net Amt</th>';
    			$html .= '</tr>';
    			$html .= '</thead>';
    			$html .= '<tbody id="filter_data_table">';
    			$Purchamt = $Discamt = $cgstamt = $sgstamt = $igstamt = $RoundOff = $Invamt = 0;
    			foreach($result as $key=>$value){
    				$html .= '<tr>';
    				$html .= '<td style="text-align:center;">'.($key+1).'</td>';
    				$html .= '<td style="text-align:center;">'.$value['AdjustmentID'].'</td>';
    				$html .= '<td style="text-align:center;">'._d(substr($value["AdjustmentDate"],0,10)).'</td>';
    				$html .= '<td>'.$value['AccountName'].'</td>';
    				$html .= '<td>'.$value['CenterName'].'</td>';
    				$html .= '<td>'.$value['Type'].'</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['Purchamt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['Discamt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['RoundOffAmt'], 2, '.', '') . '</td>';
    				$html .= '<td style="text-align:right;">' . number_format($value['Invamt'], 2, '.', '') . '</td>';
    				$html .= '</tr>';
    				$Purchamt += $value['Purchamt'];
    				$Discamt += $value['Discamt'];
    				$cgstamt += $value['cgstamt'];
    				$sgstamt += $value['sgstamt'];
    				$igstamt += $value['igstamt'];
    				$RoundOff += $value['RoundOffAmt'];
    				$Invamt += $value['Invamt'];
    			}
    			$html .= '</tbody>';
    			$html .= '<tfoot>';
    			$html .= '<tr>';
    			$html .= '<td colspan="6" style="text-align:right;">Total</td>';
    			$html .= '<td style="text-align:right;">' . number_format($Purchamt, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($Discamt, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($cgstamt, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($sgstamt, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($igstamt, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($RoundOff, 2, '.', '') . '</td>';
    			$html .= '<td style="text-align:right;">' . number_format($Invamt, 2, '.', '') . '</td>';
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
    			$html .= '<th style="text-align:left;">Type</th>';
    			$html .= '<th style="text-align:left;">Item</th>';
    			$html .= '<th style="text-align:left;">HSN</th>';
    			$html .= '<th style="text-align:left;">Brand</th>';
    			$html .= '<th style="text-align:left;">Qty</th>';
    			$html .= '<th style="text-align:left;">Purch Amt</th>';
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
    				$html .= '<td>'.$value['AdjustmentType'].'</td>';
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
    			$html .= '<td colspan="10" style="text-align:right;">Total</td>';
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
    	public function export_GetAdjustmentReportFilterData(){
    		if (!has_permission_new('SalesReturnReport', '', 'view')) {
    			access_denied('Invoice Items');
    		}
    		if (!class_exists('XLSXReader_fin')) {
    			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    		}
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    		if ($this->input->post())
    		{
    			$company_detail = $this->K1Stock_transfer_model->get_company_detail();
    			$data = array(
    				'from_date' => $this->input->post('from_date'),
    				'to_date' => $this->input->post('to_date'),
    				'AccountID'=>$this->input->post('AccountID'),
    				'CenterID'=>$this->input->post('CenterID'),
    				'CenterName'=>$this->input->post('Centertext'),
    				'ItemID'=>$this->input->post('ItemID'),
    				'ItemName'=>$this->input->post('ItemName'),
    				'ReportType'=>$this->input->post('Report_type'),
    				'ReportTypeName'=>$this->input->post('ReportTypetext'),
    				'AdjustmentType'=>$this->input->post('AdjustmentType'),
    				'AdjustmentTypeName'=>$this->input->post('AdjustmentTypetext'),
    			);
    			$result = $this->K1Stock_transfer_model->getAdjustmentReportFilter($data);
    			// echo json_encode($result); die;
    			$writer = new XLSXWriter();
    			$company_name = array($company_detail->company_name);
    			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);
    			$writer->writeSheetRow('Sheet1', $company_name);
    			$address = $company_detail->address;
    			$center_addr = array($address, );
    			$filters = "From date: " . $data['from_date'] . ", To date: " . $data['to_date'] . ", Center: " . $data['CenterName'] . ", Report Type: " . $data['ReportTypeName'] .
    			", Item: " . $data['ItemName'] . ", Party: " . $data['AccountName'] . ", Adjustment Type: " . $data['AdjustmentTypeName'];
    			$filter_row = array($filters);
    			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells
    			$writer->writeSheetRow('Sheet1', $center_addr);
    			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 14);  //merge cells
    			$writer->writeSheetRow('Sheet1', $filter_row);
    			$set_col_tk = [];
    			if ($data['ReportType'] == "1") {
    				$set_col_tk["AjustmentNo"] = 'Adjustment No';
    				$set_col_tk["Transdate"] = 'Adjustment Date';
    				$set_col_tk["Vendor"] = 'Vendor';
    				$set_col_tk["CenterName"] = 'Center Name';
    				$set_col_tk["Type"] = 'Type';
    				$set_col_tk["PurchAmt"] = 'Purch Amt';
    				$set_col_tk["DiscAmt"] = 'Disc Amt';
    				$set_col_tk["CGSTAmt"] = 'CGST Amt';
    				$set_col_tk["SGSTAmt"] = 'SGST Amt';
    				$set_col_tk["IGSTAmt"] = 'IGST Amt';
    				$set_col_tk['ItemNetTotal'] = 'Net Amt';
    			}else {
    				$set_col_tk["AjustmentNo"] = 'Adjustment No';
    				$set_col_tk["Transdate"] = 'Adjustment Date';
    				$set_col_tk["Vendor"] = 'Vendor';
    				$set_col_tk["CenterName"] = 'Center Name';
    				$set_col_tk["Type"] = 'Type';
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
    					$list_add[] = $value['AdjustmentID'];
    					$list_add[] = _d(substr($value["AdjustmentDate"],0,10));
    					$list_add[] = $value['AccountName'];
    					$list_add[] = $value['CenterName'];
    					$list_add[] = $value['Type'];
    					$list_add[] = number_format($value['Purchamt'], 2, '.', '');
    					$list_add[] = number_format($value['Discamt'], 2, '.', '');
    					$list_add[] = number_format($value['cgstamt'], 2, '.', '');
    					$list_add[] = number_format($value['sgstamt'], 2, '.', '');
    					$list_add[] = number_format($value['igstamt'], 2, '.', '');
    					$list_add[] = number_format($value['Invamt'], 2, '.', '');
    					$writer->writeSheetRow('Sheet1', $list_add);
    					$SaleAmt += $value['Purchamt'];
    					$DiscAmt += $value['Discamt'];
    					$cgstamt += $value['cgstamt'];
    					$sgstamt += $value['sgstamt'];
    					$igstamt += $value['igstamt'];
    					$BillAmt += $value['Invamt'];
    				}
    			}else{
    				$OrderQty = $SaleRate = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $NetOrderAmt = 0;
    				foreach($result as $key=>$value){
    					$list_add = [];
    					$list_add[] = $value['OrderID'];
    					$list_add[] = _d(substr($value["TransDate"],0,10));
    					$list_add[] = $value['AccountName'];
    					$list_add[] = $value['CenterName'];
    					$list_add[] = $value['AdjustmentType'];
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
    			$filename = 'StockAdjustmentReport.xlsx';
    			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
    			echo json_encode([
                'site_url' => site_url(),
                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
    			]);
    			die;
    		}
    	}

		// =================== Godown Stock Transfer (within single center) ===================
		public function AddEditGodownStockTransfer($TrfNumber = '')
		{
			if (!has_permission_new('KirtiOneGodownStockTransfer', '', 'view')) {
				access_denied('KirtiOneGodownStockTransfer');
			}
			$data['item_code'] = [];
			$data['pur_order_detail'] = json_encode([]);
			$data['godown_transfer_number'] = $this->K1Stock_transfer_model->getNextGodownTransferDisplayNumber();
			$data['can_create'] = has_permission_new('KirtiOneGodownStockTransfer', '', 'create');
			$data['can_edit'] = has_permission_new('KirtiOneGodownStockTransfer', '', 'edit');
			$data['centermaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
			$this->load->view('admin/K1Stock_transfer/AddEditGodownStockTransfer', $data);
		}

		public function GetGodownStockTransferData()
		{
			$action = $this->input->post('action');
			switch ($action) {
				case 'item_list':
					$CenterID = $this->input->post('CenterID');
					echo json_encode($this->K1Stock_transfer_model->GetCenterWiseGodownItems($CenterID));
					break;
				case 'item_details':
					$ItemID = $this->input->post('ItemID');
					$CenterID = $this->input->post('CenterID');
					$TransferID = $this->input->post('TransferID');
					$ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($ItemID, $CenterID);
					if (!empty($ItemDetails)) {
						$ItemDetails->BatchList = $this->K1Stock_transfer_model->GetGodownItemBatchListWithStock(
							$this->godown_batch_filter($ItemID, $CenterID, '', $TransferID)
						);
					}
					echo json_encode($ItemDetails);
					break;
				case 'batch_stock':
					$BatchStock = $this->K1Stock_transfer_model->GetGodownItemBatchListWithStock(
						$this->godown_batch_filter(
							$this->input->post('ItemID'),
							$this->input->post('CenterID'),
							$this->input->post('BatchID'),
							$this->input->post('TransferID')
						)
					);
					echo json_encode($BatchStock);
					break;
				case 'cancel':
					echo json_encode($this->K1Stock_transfer_model->CancelGodownStockTransfer($this->input->post('TransferID')));
					break;
				case 'approve':
					echo json_encode($this->K1Stock_transfer_model->ApproveGodownStockTransfer($this->input->post('TransferID')));
					break;
				case 'save':
					if (!has_permission_new('KirtiOneGodownStockTransfer', '', 'create')) {
						echo json_encode(['success' => false, 'message' => 'Access denied']);
						break;
					}
					$id = $this->K1Stock_transfer_model->AddGodownStockTransfer($this->input->post());
					if ($id) {
						echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Godown Stock Transfer'), 'TransferID' => $id]);
					} else {
						echo json_encode(['success' => false, 'message' => 'Failed to save godown stock transfer']);
					}
					break;
				case 'update':
					if (!has_permission_new('KirtiOneGodownStockTransfer', '', 'edit')) {
						echo json_encode(['success' => false, 'message' => 'Access denied']);
						break;
					}
					$TransferID = $this->input->post('TransferID');
					$existing = $this->K1Stock_transfer_model->GetGodownStockDetails($TransferID);
					if (empty($existing) || $existing->OrderStatus !== 'D') {
						echo json_encode(['success' => false, 'message' => 'Only draft orders can be updated']);
						break;
					}
					if ($this->K1Stock_transfer_model->UpdateGodownStockTransfer($this->input->post(), $TransferID)) {
						echo json_encode(['success' => true, 'message' => _l('updated_successfully', 'Godown Stock Transfer'), 'TransferID' => $TransferID]);
					} else {
						echo json_encode(['success' => false, 'message' => 'Failed to update godown stock transfer']);
					}
					break;
				case 'load_transfer':
					$TransferID = $this->input->post('TransferID');
					$header = $this->K1Stock_transfer_model->GetGodownStockDetails($TransferID);
					if (empty($header)) {
						echo json_encode(['success' => false, 'message' => 'Transfer not found']);
						break;
					}
					echo json_encode([
						'success' => true,
						'header' => [
							'TransferID' => $header->TransferID,
							'transfer_suffix' => substr($header->TransferID, 5),
							'TransferDate' => _d(substr($header->TransferDate, 0, 10)),
							'CenterID' => $header->CenterID,
							'OrderStatus' => $header->OrderStatus,
							'total_qty_in_mt' => $header->TotalOrderQty,
							'total_amt_in_mt' => $header->Purchamt,
							'total_disc_in_mt' => $header->Discamt,
							'Total_value' => $header->taxable_amt,
							'total_cgst_amt' => $header->cgstamt,
							'total_sgst_amt' => $header->sgstamt,
							'total_igst_amt' => $header->igstamt,
							'total_roundoff_amt' => $header->RoundOffAmt,
							'netpayableamt' => $header->Invamt,
						],
						'items' => $this->K1Stock_transfer_model->GetGodownStockItemList($TransferID),
						'item_code' => $this->K1Stock_transfer_model->GetCenterWiseGodownItems($header->CenterID, $TransferID),
					]);
					break;
				case 'new_transfer':
					echo json_encode([
						'success' => true,
						'transfer_number' => $this->K1Stock_transfer_model->getNextGodownTransferDisplayNumber(),
					]);
					break;
				default:
					echo json_encode([]);
			}
		}

		public function load_data_for_godownstocktransfer()
		{
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date')
			);
			$StockList = $this->K1Stock_transfer_model->load_data_for_godownstocktransfer($data);
			$html = "";
			foreach ($StockList as $val) {
				if ($val['OrderStatus'] == 'F') {
					$orderstat = 'Completed';
				} else if ($val['OrderStatus'] == 'C') {
					$orderstat = 'Cancelled';
				} else if ($val['OrderStatus'] == 'D') {
					$orderstat = 'Draft';
				} else {
					$orderstat = $val['OrderStatus'];
				}
				$html .= '<tr class="godown-list-row" data-transfer-id="' . $val["TransferID"] . '" style="cursor:pointer;">';
				$html .= '<td style="text-align:center;">' . $val["TransferID"] . '</td>';
				$html .= '<td style="text-align:center;">' . _d(substr($val["TransferDate"], 0, 10)) . '</td>';
				$html .= '<td style="text-align:left;">' . $val["CenterName"] . '</td>';
				$html .= '<td style="text-align:left;">' . $orderstat . '</td>';
				$html .= '<td style="text-align:right;">' . $val["Invamt"] . '</td>';
				$html .= '</tr>';
			}
			if (empty($StockList)) {
				$html .= '<tr><td colspan="5" style="text-align:center;">No records found</td></tr>';
			}
			echo $html;
		}

		/** Batch/stock lookup filter for godown transfer AJAX (WHO wholesale stock). */
		private function godown_batch_filter($itemId, $centerId, $batchId = '', $transferId = '')
		{
			$filter = [
				'ItemID' => $itemId,
				'CenterID' => $centerId,
				'BatchID' => $batchId ?: '',
			];
			if (!empty($transferId)) {
				$filter['TransferID'] = $transferId;
			}
			return $filter;
		}
	}