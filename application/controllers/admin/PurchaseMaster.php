<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class PurchaseMaster extends AdminController
	{
		private $not_importable_fields = ['id'];
		public function __construct()
		{
			parent::__construct();
			$this->load->model('PurchaseModel');
			$this->load->model('KirtiOneOrderModel');
		}
		public function AddEditPurchaseRequest($PRNumber = '')
		{
			if (!has_permission_new('PurchaseRequest', '', 'view')) {
				access_denied('purchase order');
			}
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($PRNumber == '') {
					if (!has_permission_new('PurchaseRequest', '', 'create')) {
						access_denied('PurchaseRequest');
					}
					$id = $this->PurchaseModel->AddKirtiOnePurchaseRequest($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseRequest'));
					}
					}else{
					if (!has_permission_new('PurchaseRequest', '', 'edit')) {
						access_denied('PurchaseRequest');
					}
					$id = $this->PurchaseModel->UpdateKirtiOnePurchaseRequest($pur_order_data,$PRNumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseRequest'));
					}
				}
			}
			// $data['item_code'] = $this->PurchaseModel->get_items_code();
			$data['item_code'] = array();
			if ($PRNumber == '') {
				$title = _l('create_new_pur_order');
			}else{
				$PurchaseDetails = $this->PurchaseModel->GetPurchaseRequestDetails($PRNumber);
				$data['purchase_details'] = $PurchaseDetails;
				$data['item_code'] = $this->PurchaseModel->GetVendorWiseItems($PurchaseDetails->AccountID);
				$PurchaseItemList = $this->PurchaseModel->GetPurchaseRequestItemList($PRNumber);
				$data['pur_order_detail'] = json_encode($PurchaseItemList);
				$title = "Edit Purchase Order";
			}
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")';
			$EffectOn = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn;
			$wh_effect = '(status="Y")';
			$centermaster = $this->PurchaseModel->get_all_data($tablename="tblCenterMaster",$wh_effect);
			$CenterList = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $CenterList;
			$trader_list = $this->PurchaseModel->GetAccountList();
			$data['trader_list'] = $trader_list;
			$data['statelist'] = $this->PurchaseModel->getstatelist();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditPurchaseRequest',$data);
		}
		public function AddEditDemandList($Demandid = '')
		{
		    if (!has_permission_new('DemandList', '', 'view')) {
				access_denied('DemandList');
			}
			if ($this->input->post())
			{
			    $pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($Demandid == '')
				{
					if (!has_permission_new('DemandList', '', 'create'))
					{
						access_denied('DemandList');
					}
					$id = $this->PurchaseModel->AddDemandList($pur_order_data);
					if ($id)
					{
						set_alert('success', _l('added_successfully', _l('pur_order_detail')));
						redirect(admin_url('PurchaseMaster/AddEditDemandList'));
					}
					}else
					{
    					if (!has_permission_new('DemandList', '', 'edit')) {
    						access_denied('DemandList');
    					}
					    $id = $this->PurchaseModel->UpdateDemandList($pur_order_data,$Demandid);
    					if ($id) {
    						set_alert('success', _l('added_successfully', _l('pur_order')));
    						redirect(admin_url('PurchaseMaster/AddEditDemandList'));
    					}
				    }
			    }
			if ($Demandid == '') {
				$title = _l('create_new_demand_list');
				}else{
				$DemandList = $this->PurchaseModel->GetDemandList($Demandid);
				$data['demandlist'] = $DemandList;
				$data['pur_order_detail'] = json_encode([$DemandList]);
				$title = "Edit Demand List";
			}
			$CenterList = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $CenterList;
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditDemandList',$data);
		}
		public function DemandListReport()
		{
		     if (!has_permission_new('DemandListReport', '', 'view')) {
				access_denied('DemandListReport');
			}
			$CenterList = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['centermaster'] = $CenterList;
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
		    $this->load->view('admin/PurchaseMaster/DemandListReport',$data);
		}
		public function AddEditPurchaseOrderNew($PONumber = '')
		{
			if (!has_permission_new('PurchaseOrder', '', 'view')) {
				access_denied('purchase order');
			}
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if (!empty($pur_order_data['reminder_date'])) {
					$reminderDate = to_sql_date($pur_order_data['reminder_date']);
					if (strtotime($reminderDate) < strtotime(date('Y-m-d'))) {
						set_alert('warning', 'Reminder Date must be today or a future date.');
						redirect($PONumber == '' ? admin_url('PurchaseMaster/AddEditPurchaseOrderNew') : admin_url('PurchaseMaster/AddEditPurchaseOrderNew/' . $PONumber));
					}
				}
				if ($PONumber == '') {
					if (!has_permission_new('PurchaseOrder', '', 'create')) {
						access_denied('PurchaseOrder');
					}
					$id = $this->PurchaseModel->AddKirtiOnePurchaseOrderNew($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseOrderNew'));
					}
					}else{
					if (!has_permission_new('PurchaseOrder', '', 'edit')) {
						access_denied('PurchaseOrder');
					}
					$id = $this->PurchaseModel->UpdateKirtiOnePurchaseOrder($pur_order_data,$PONumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseOrderNew'));
					}
				}
			}
			if ($PONumber == '') {
				$title = _l('create_new_pur_order');
			}else{
				$PurchaseDetails = $this->PurchaseModel->GetPurchaseOrderDetails($PONumber);
				$data['purchase_details'] = $PurchaseDetails;
				$PurchaseItemList = $this->PurchaseModel->GetPurchaseOrderItemList($PONumber);
				$data['pur_order_detail'] = json_encode($PurchaseItemList);
				$title = "Edit Purchase Order";
				// echo "<pre>";print_r($PurchaseItemList);die;
			}
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")';
			$EffectOn = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn;
			$centermaster = $this->PurchaseModel->GetCenterList();
			$data['centermaster'] = $centermaster;
			$trader_list = $this->PurchaseModel->GetAccountList();
			//$trader_list = $this->PurchaseModel->PendingInwardVendors();
			$data['trader_list'] = $trader_list;
			$data['item_code'] = $this->PurchaseModel->get_items_code();
			$data['statelist'] = $this->PurchaseModel->getstatelist();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditPurchaseOrderNew',$data);
		}
		public function AddEditPurchaseInvoice($PINumber = '')
		{
			if (!has_permission_new('PurchaseInvoice', '', 'view')) {
				access_denied('purchase order');
			}
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($PINumber == '') {
					if (!has_permission_new('PurchaseInvoice', '', 'create')) {
						access_denied('PurchaseInvoice');
					}
					$id = $this->PurchaseModel->AddKirtiOnePurchaseInvoice($pur_order_data);
					if ($id) {
					   // is array condition added 21apr2026
					    if(is_array($id)){
					        set_alert('warning', $id['message'].$id['data'], $id['data']);
					    }else{
					        set_alert('success', _l('added_successfully', _l('pur_order')));
					    }
					   // end
					    redirect(admin_url('PurchaseMaster/AddEditPurchaseInvoice'));
					}
				}else{
					if (!has_permission_new('PurchaseInvoice', '', 'edit')) {
						access_denied('PurchaseInvoice');
					}
					$id = $this->PurchaseModel->UpdateKirtiOnePurchaseInvoice($pur_order_data,$PINumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseInvoice'));
					}
				}
			}
			if ($PINumber == '') {
				$title = "Create Purchase Invoice";
			}else{
				$PurchaseDetails = $this->PurchaseModel->GetPurchaseInvoiceDetails($PINumber);
				$PurchaseItemList = $this->PurchaseModel->GetPurchaseInvoiceItemList($PINumber);
				$IsSale = 0;
				foreach($PurchaseItemList as $val){
				    if($val["SaleQty"] > 0){
				        $IsSale++;
				    }
				}
				$PurchaseDetails->IsSale = $IsSale;
				$data['pur_order_detail'] = json_encode($PurchaseItemList);
				$data['purchase_details'] = $PurchaseDetails;
				$title = "Edit Purchase Invoice";
				 //echo "<pre>";print_r($PurchaseDetails);die;
			}
			$ActGroupID = 10010;
			$wh_effect = '(ActGroupID="'.$ActGroupID.'")';
			$DirectExp = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['DirectExp'] = $DirectExp;
			$ActGroupID = 10011;
			$wh_effect = '(ActGroupID="'.$ActGroupID.'")';
			$DirectInc = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['DirectInc'] = $DirectInc;
			$centermaster = $this->PurchaseModel->get_all_table_data($tablename="tblCenterMaster");
			$data['centermaster'] = $centermaster;
			// $trader_list = $this->PurchaseModel->GetAccountList();
			$trader_list = $this->PurchaseModel->PendingInvoiceVendors();
			$data['trader_list'] = $trader_list;
			$data['item_code'] = $this->PurchaseModel->get_items_code();
			$data['statelist'] = $this->PurchaseModel->getstatelist();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditPurchaseInvoice',$data);
		}
		public function generateQR(){
			$purchase_inward_no = $this->input->post('purchase_inward_no');
			$PurchaseItemList = $this->PurchaseModel->GetPurchaseInvoiceItemListForQR($purchase_inward_no);
			$temp_qr = [];
			// Load QR library
			$this->load->library('ciqrcode');
			foreach ($PurchaseItemList as $item) {
					$id        = $item['id'];
					$item_id   = $item['ItemID'];
					$batch_no  = $item['BatchNo'];
					$center_id = $item['CenterID'];
					$category  = $item['Category'];
					$purchase_id = $purchase_inward_no;
					// QR save directory
					$dir = 'assets/purchase_qr/'.$purchase_id.'/';
					if(!file_exists($dir)) mkdir($dir, 0775, true);
					// Load CI QR library
					$this->load->library('ciqrcode');
					$qr_data = json_encode([
							'stock_id'  => $id,
							'item_id'   => $item_id,
							'batch_no'  => $batch_no,
							'center_id' => $center_id,
							'glossary'  => in_array($category, [6, 8])
					]);
					$qr_file = $dir."QR_{$id}.png";
					// Generate QR
					$params['data']     = $qr_data;
					$params['level']    = 'H';
					$params['size']     = 10;
					$params['savename'] = FCPATH . $qr_file;
					$this->ciqrcode->generate($params);
					// Load QR image
					$qr_img = imagecreatefrompng(FCPATH . $qr_file);
					// Text below QR
					$text = "Item ID: $item_id | Center: $center_id\nBatch No: $batch_no";
					$font_size = 5; // imagestring font size 1-5
					// Measure text
					$lines = explode("\n", $text);
					$line_height = imagefontheight($font_size);
					$text_height = count($lines) * $line_height + 10; // extra padding
					$qr_width = imagesx($qr_img);
					$qr_height = imagesy($qr_img);
					// Create new image with extra space for text below
					$new_img = imagecreatetruecolor($qr_width, $qr_height + $text_height);
					$white = imagecolorallocate($new_img, 255, 255, 255);
					$black = imagecolorallocate($new_img, 0, 0, 0);
					imagefill($new_img, 0, 0, $white);
					// Copy QR into new image
					imagecopy($new_img, $qr_img, 0, 0, 0, 0, $qr_width, $qr_height);
					// Draw text below QR, center each line
					$y = $qr_height + 5; // start 5px below QR
					foreach ($lines as $line) {
							$text_width = imagefontwidth($font_size) * strlen($line);
							$x = ($qr_width - $text_width) / 2;
							imagestring($new_img, $font_size, $x, $y, $line, $black);
							$y += $line_height;
					}
					// Save final image
					imagepng($new_img, FCPATH . $qr_file);
					// Cleanup
					imagedestroy($qr_img);
					imagedestroy($new_img);
					// Save QR file name/path in K1history table
					$this->db->where('id', $id);
					$this->db->update('tblK1history', [
							'QRData' => $qr_data,
							'QRPath' => $dir."QR_{$id}.png",
					]);
					$temp_qr[] = [
							'id'			=> $id,
							'QRCode' 	=> "QR_{$id}.png",
							'QRPath' 	=> $dir."QR_{$id}.png",
							'QRData' 	=> $qr_data
					];
			}
			echo json_encode([
					'success' => true,
					'message' => 'QR codes generated successfully',
					'item' 		=> $PurchaseItemList,
					'data'    => $temp_qr
			]);
		}
		public function downloadQRPDF($purchase_inward_no)
		{
				$items = $this->PurchaseModel->GetPurchaseInvoiceItemListForQR($purchase_inward_no);
				$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
				$pdf->SetTitle("Purchase QR Codes");
				$pdf->SetMargins(10, 10, 10);
				$pdf->SetAutoPageBreak(TRUE, 10);
				$page_width = 210;
				$page_height = 297;
				$margin = 10;
				$gap = 5;
				$qr_per_row = 3;
				// QR width/height to fit 3 per row
				$cell_width = ($page_width - 2*$margin - ($qr_per_row-1)*$gap) / $qr_per_row;
				$cell_height = $cell_width;
				foreach ($items as $item) {
						// ALWAYS start a new page per item
						$pdf->AddPage();
						$id        = $item['id'];
						$item_id   = $item['ItemID'];
						$batch_no  = $item['BatchNo'];
						$center_id = $item['CenterID'];
						$qty       = $item['BilledQty'];
						$heading_text = "Item ID: $item_id | Center ID: $center_id | Batch No: $batch_no";
						// Heading
						$pdf->SetFont('helvetica','B',12);
						$pdf->Cell(0, 6, $heading_text, 0, 1, 'L');
						$pdf->Ln(2);
						$pdf->Line($margin, $pdf->GetY(), $page_width - $margin, $pdf->GetY());
						$pdf->Ln(5);
						$x = $pdf->GetX();
						$y = $pdf->GetY();
						$count = 0;
						for ($i=1; $i <= $qty; $i++) {
								$qr_file = FCPATH.'assets/purchase_qr/'.$purchase_inward_no."/QR_{$id}.png";
								if (file_exists($qr_file)) {
										// Check page bottom
										if ($y + $cell_height > $pdf->getPageHeight() - 20) {
												$pdf->AddPage();
												$y = $pdf->GetY();
												$x = $pdf->GetX();
												// Repeat heading on new page
												$pdf->SetFont('helvetica','B',12);
												$pdf->Cell(0, 6, $heading_text, 0, 1, 'L');
												$pdf->Ln(2);
												$pdf->Line($margin, $pdf->GetY(), $page_width - $margin, $pdf->GetY());
												$pdf->Ln(5);
												$y = $pdf->GetY();
										}
										$pdf->Image($qr_file, $x, $y, $cell_width, $cell_height);
								}
								$x += $cell_width + $gap;
								$count++;
								if ($count % $qr_per_row == 0) {
										$y += $cell_height + $gap;
										$x = $pdf->GetX();
								}
						}
				}
				$pdf->Output("Purchase_QR_$purchase_inward_no.pdf", 'D');
		}
		public function AddEditPurchaseInvoiceLedger($PINumber = '')
		{
			if (!has_permission_new('PurchaseInvoiceLedger', '', 'view')) {
				access_denied('purchase order');
			}
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($PINumber == '') {
					if (!has_permission_new('PurchaseInvoiceLedger', '', 'create')) {
						access_denied('PurchaseInvoiceLedger');
					}
					$id = $this->PurchaseModel->AddKirtiOnePurchaseInvoiceLedger($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseInvoiceLedger'));
					}
				}else{
					if (!has_permission_new('PurchaseInvoiceLedger', '', 'edit')) {
						access_denied('PurchaseInvoiceLedger');
					}
					$id = $this->PurchaseModel->UpdateKirtiOnePurchaseInvoiceLedger($pur_order_data,$PINumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditPurchaseInvoiceLedger'));
					}
				}
			}
			if ($PINumber == '') {
				$title = "Create Purchase Invoice";
			}else{
				$PurchaseDetails = $this->PurchaseModel->GetPurchaseInvoiceDetails($PINumber);
				$data['purchase_details'] = $PurchaseDetails;
				$PurchaseItemList = $this->PurchaseModel->GetPurchaseInvoiceItemList($PINumber);
				$data['pur_order_detail'] = json_encode($PurchaseItemList);
				$title = "Edit Purchase Invoice";
				// echo "<pre>";print_r($PurchaseItemList);die;
				$selected_company = $this->session->userdata("root_company");
        $fy = $this->session->userdata("finacial_year");
				$data['expense_ledger'] = $this->PurchaseModel->get_all_data("tblK1PurchaseMasterExpenses", ["PlantID" => $selected_company, "FY" => $fy, "Inv_No" => $PINumber, "LedgerCategory" => 'Direct Expense']);
				$data['income_ledger'] = $this->PurchaseModel->get_all_data("tblK1PurchaseMasterExpenses", ["PlantID" => $selected_company, "FY" => $fy, "Inv_No" => $PINumber, "LedgerCategory" => 'Direct Income']);
			}
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")';
			$EffectOn = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn;
			$ActGroupID = 10010;
			$wh_effect = '(ActGroupID="'.$ActGroupID.'")';
			$DirectExp = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['DirectExp'] = $DirectExp;
			$ActGroupID = 10011;
			$wh_effect = '(ActGroupID="'.$ActGroupID.'")';
			$DirectInc = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['DirectInc'] = $DirectInc;
			$centermaster = $this->PurchaseModel->get_all_table_data($tablename="tblCenterMaster");
			$data['centermaster'] = $centermaster;
			// $trader_list = $this->PurchaseModel->GetAccountList();
			$trader_list = $this->PurchaseModel->PendingInvoiceLedgerVendors();
			$data['trader_list'] = $trader_list;
			$data['item_code'] = $this->PurchaseModel->get_items_code();
			$data['statelist'] = $this->PurchaseModel->getstatelist();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditPurchaseInvoiceLedger',$data);
		}
//====================== Cancel Purchase Ledger Entry ==========================
		public function CancelPILedgerEntry()
		{
			$PurchInvoiceID = $this->input->post('PurchInvoiceID');
			$data = $this->PurchaseModel->CancelPILedgerEntryByPIID($PurchInvoiceID);
			echo json_encode($data);
		}
		public function GetPRByVendor()
		{
			$VenId = $this->input->post('VenId');
			$data = $this->PurchaseModel->get_order_PR_ven_details($VenId);
			echo json_encode($data);
		}
		public function GetPOByVendor()
		{
			$VenId = $this->input->post('VenId');
			$data = $this->PurchaseModel->get_order_PO_ven_details($VenId);
			echo json_encode($data);
		}
		public function GetPIByVendorAndCenter()
		{
			$VenId = $this->input->post('VenId');
			$CenterID = $this->input->post('CenterID');
			$data = $this->PurchaseModel->get_order_PI_ven_center_details($VenId,$CenterID);
			echo json_encode($data);
		}
		public function GetPIByCenterWiseVendor()
		{
			$CenterID = $this->input->post('CenterID');
			$data = $this->PurchaseModel->PendingInvoiceCenterwiseVendors($CenterID);
			echo json_encode($data);
		}
		public function GetPIByVendor()
		{
			$VenId = $this->input->post('VenId');
			$data = $this->PurchaseModel->get_order_PI_ven_details($VenId);
			echo json_encode($data);
		}
		public function GetPRItemData(){
			// POST data
			$PrNo = $this->input->post('PrNo');
			// Get data
			$InwardData['historytbl'] = $this->PurchaseModel->GetPurchaseRequestItemListForPO($PrNo);
			$InwardData['RequestData'] = $this->PurchaseModel->GetPurchaseRequestDetails($PrNo);
			// print_r($InwardData['historytbl']);die;
			echo json_encode($InwardData);
		}
		public function GetPOItemData(){
			// POST data
			$PoNo = $this->input->post('PoNo');
			// Get data
			$InwardData['historytbl'] = $this->PurchaseModel->GetPurchaseOrderItemListForInv($PoNo);
			$InwardData['OrderData'] = $this->PurchaseModel->GetPurchaseOrderDetails($PoNo);
			echo json_encode($InwardData);
		}
		public function GetPIItemData(){
			// POST data
			$PINo = $this->input->post('PINo');
			// Get data
			$InwardData['historytbl'] = $this->PurchaseModel->GetPurchaseOrderItemListForInvLedger($PINo);
			$InwardData['InvoiceData'] = $this->PurchaseModel->GetPurchaseInvoiceDetails($PINo);
			echo json_encode($InwardData);
		}
		public function AddEditInward($PONumber = '')
		{
			if (!has_permission_new('KirtiOneInward', '', 'view')) {
				access_denied('purchase order');
			}
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();
				$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				if ($PONumber == '') {
					if (!has_permission_new('KirtiOneInward', '', 'create')) {
						access_denied('KirtiOneInward');
					}
					$id = $this->PurchaseModel->AddKirtiOneInward($pur_order_data);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditInward'));
					}
					}else{
					if (!has_permission_new('KirtiOneInward', '', 'edit')) {
						access_denied('KirtiOneInward');
					}
					$id = $this->PurchaseModel->UpdateKirtiOneInward($pur_order_data,$PONumber);
					if ($id) {
						set_alert('success', _l('added_successfully', _l('pur_order')));
						redirect(admin_url('PurchaseMaster/AddEditInward'));
					}
				}
			}
			$data['item_code'] = array();
			if ($PONumber == '') {
				$title = _l('create_new_pur_order');
				}else{
				$PurchaseDetails = $this->PurchaseModel->GetInwardDetails($PONumber);
				$data['purchase_details'] = $PurchaseDetails;
				$data['item_code'] = $this->PurchaseModel->GetVendorWiseItems($PurchaseDetails->AccountID);
				$PurchaseItemList = $this->PurchaseModel->GetPurchaseItemList($PONumber);
				$data['pur_order_detail'] = json_encode($PurchaseItemList);
				$title = "Edit Purchase Order";
			}
			$SubactgropuId = 1000017;
			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")';
			$EffectOn = $this->PurchaseModel->get_all_data($tablename="tblclients",$wh_effect);
			$data['EffectOn'] = $EffectOn;
			$centermaster = $this->PurchaseModel->get_all_table_data($tablename="tblCenterMaster");
			$data['centermaster'] = $centermaster;
			$trader_list = $this->PurchaseModel->GetAccountList();
			$data['trader_list'] = $trader_list;
			// $data['item_code'] = $this->PurchaseModel->get_items_code();
			$data['statelist'] = $this->PurchaseModel->getstatelist();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/AddEditPurchaseOrder',$data);
		}
		public function GetItemDetails($ItemID)
		{
			$ItemDetails = $this->PurchaseModel->GetItemDetails($ItemID);
			echo json_encode($ItemDetails);
		}
		public function GetVendorDetails()
		{
			$VendorID = $this->input->post('vendor_id');
			$trader_list = $this->PurchaseModel->GetAccountListVendorwise($VendorID);
			echo json_encode($trader_list);
		}
		public function CheckVendorDocNo()
		{
			$InvoiceID = $this->input->post('VendorDocNo');
			$PurchID = $this->input->post('isedit');
			$PurchInvoiceID = $this->input->post('PurchInvoiceID');
			$purchase_list = $this->PurchaseModel->CheckVendorDocNo($InvoiceID,$PurchID,$PurchInvoiceID);
			echo json_encode($purchase_list);
		}
		public function load_data_for_purchase()
		{
		    $data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_inwardkirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
				}
				$url = admin_url()."PurchaseMaster/AddEditInward/".$val["PurchID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["PurchID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
				$html .= '<td style="text-align:left;">'.$val["EwayBillNo"].'</td>';
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
		public function load_data_for_purchase_request()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_requestkirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
				}
				$url = admin_url()."PurchaseMaster/AddEditPurchaseRequest/".$val["PurchID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["PurchID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
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
		public function load_data_for_demandlist()
		{
		    $data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'centername'=>$this->input->post('centername'),
			);
			$demandlist = $this->PurchaseModel->load_data_for_demandlists($data);
			$html = "";
			$i=1;
			foreach($demandlist as $key=>$val)
			{
				$url = admin_url()."PurchaseMaster/AddEditDemandList/".$val["id"];
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$i.'</td>';
				$html .= '<td style="text-align:left;">'._d(substr($val["TransDate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["ItemID"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Qty"].'</td>';
				$html .= '</tr>';
				$i++;
			}
			echo $html;
		}
		public function load_data_for_demandReport()
		{
		    $data = array(
    			'from_date' => $this->input->post('from_date'),
    			'to_date'  => $this->input->post('to_date'),
    			'centername'=> $this->input->post('centername'),
			);
			$centerlist = $this->PurchaseModel->load_data_for_demandReport_list($data);
			$html = "";
			$i=1;
			foreach($centerlist as $key=>$val)
			{
				$url = admin_url()."PurchaseMaster/AddEditDemandList/".$val["id"];
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$i.'</td>';
				$html .= '<td style="text-align:left;">'._d(substr($val["TransDate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["ItemID"].'</td>';
				$html .= '<td style="text-align:right;">'.$val["Qty"].'</td>';
				$html .= '</tr>';
				$i++;
			}
			echo $html;
		}
	    public function export_DemandList()
        {
            if (!has_permission_new('VillageReport', '', 'export')) {
                access_denied('Invoice Items');
            }
            if (!class_exists('XLSXReader_fin')) {
                require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
            }
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            if ($this->input->post())
            {
                $company_detail = $this->PurchaseModel->get_company_detail();
                $post_data = $this->input->post();
                $result = $this->PurchaseModel->load_data_for_demandReport_list($post_data);
                $writer = new XLSXWriter();
                $writer->markMergedCell('Sheet1', 0, 0, 0, 10);
                $writer->writeSheetRow('Sheet1', [$company_detail->company_name]);
                $writer->markMergedCell('Sheet1', 1, 0, 1, 10);
                $writer->writeSheetRow('Sheet1', [$company_detail->address]);
                $filters = [];
                if (!empty($post_data['from_date']) && !empty($post_data['to_date'])) {
                    $filters[] = 'Date Range: ' . $post_data['from_date'] . ' to ' . $post_data['to_date'];
                }
                if (!empty($post_data['centername'])) {
                    $filters[] = 'Center Name: ' . $post_data['centername_text'];
                }
                $writer->markMergedCell('Sheet1', 2, 0, 2, 10);
                $writer->writeSheetRow('Sheet1', [implode(' , ', $filters)]);
                $set_col_tk = [
                    "Id" => 'Sr.No.',
                     "Date" => 'Date',
                    "CenterID" => 'Center Name',
                    "ItemID" => 'Item Name',
                    "Qty" => 'Qty',
                ];
                $writer->writeSheetRow('Sheet1', array_values($set_col_tk));
                $i=1;
                foreach ($result as $value)
                {
                    $list_add = [
                        $i,
                        $value["CenterName"],
                        $value["ItemID"],
                        $value["Qty"],
                        date('d/m/Y', strtotime($value["TransDate"]))
                    ];
                    $writer->writeSheetRow('Sheet1', $list_add);
                    $i++;
                }
        		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            		foreach($files as $file){
            			if(is_file($file)) {
            				unlink($file);
            			}
            		}
            		$filename = 'DemandReportList.xlsx';
            		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            		echo json_encode([
            			'site_url'          => site_url(),
            			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            		]);
            		die;
            }
         }
		public function load_data_for_purchase_order()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_order_kirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
				}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
				}else if($val['OrderStatus'] == "A"){
					$OrderStatus = "Approved";
				}
				$url = admin_url()."PurchaseMaster/AddEditPurchaseOrderNew/".$val["PurchID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["PurchID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
				$html .= '<td style="text-align:center;">'.$val["Pr_no"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
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
		public function load_data_for_purchase_invoice()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_invoice_kirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
				}
				$url = admin_url()."PurchaseMaster/AddEditPurchaseInvoice/".$val["Inv_No"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["Inv_No"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Inv_date"],0,10)).'</td>';
				$html .= '<td style="text-align:center;">'.$val["PurchID"].'</td>';
				$html .= '<td style="text-align:center;">'.$val["Pr_no"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
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
			$html .= '<td colspan="7" style="text-align:right;"><b>Total</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalPurchAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalDiscAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalCgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalSgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalIgstAmt, 2, '.', ',').'</b></td>';
			$html .= '<td style="text-align:right;"><b>'.number_format($TotalInvAmt, 2, '.', ',').'</b></td>';
			$html .= '</tr>';
			echo $html;
		}
		public function load_data_for_purchase_return_invoice()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_return_invoice_kirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			// echo "<pre>";
			// print_r($PurchaseList);
			// die;
			foreach($PurchaseList as $key=>$val)
			{
				// if($val['OrderStatus'] == "C")
				// { $OrderStatus = "Cancelled";	}
				// else if($val['OrderStatus'] == "F"){
					// $OrderStatus = "Completed";
					// }else if($val['OrderStatus'] == "P"){
					// $OrderStatus = "Pending";
				// }
				$url = admin_url()."PurchaseMaster/AddEditPurchaseReturnInvoice/".$val["PurchRtnID"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["PurchRtnID"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Transdate"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
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
		public function load_data_for_purchase_invoice_ledger()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_invoice_ledger_kirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
				}
				$url = admin_url()."PurchaseMaster/AddEditPurchaseInvoiceLedger/".$val["Inv_No"];
				//$html .= '<tr onclick="window.open('."'".$url."'".')">';
				$html .= '<tr onclick="window.location.href=\''.$url.'\'">';
				$html .= '<td style="text-align:center;">'.$val["Inv_No"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Inv_date"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
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
		public function load_data_for_purchase_invoice_pending_ledger()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$PurchaseList = $this->PurchaseModel->load_data_for_purchase_pending_invoice_ledger_kirtione($data);
			$html = "";
			$TotalPurchAmt = 0;
			$TotalDiscAmt = 0;
			$TotalCgstAmt = 0;
			$TotalSgstAmt = 0;
			$TotalIgstAmt = 0;
			$TotalInvAmt = 0;
			$url2 = "";
			foreach($PurchaseList as $key=>$val)
			{
				if($val['OrderStatus'] == "C")
				{ $OrderStatus = "Cancelled";	}
				else if($val['OrderStatus'] == "F"){
					$OrderStatus = "Completed";
					}else if($val['OrderStatus'] == "P"){
					$OrderStatus = "Pending";
				}
				$html .= '<tr class="GetPendingLedger" data-id="'.$val["AccountID"].'" data-invoice="'.$val["Inv_No"].'">';
				$html .= '<td style="text-align:center;">'.$val["Inv_No"].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($val["Inv_date"],0,10)).'</td>';
				$html .= '<td style="text-align:left;">'.$val["AccountName"].'</td>';
				$html .= '<td style="text-align:left;">'.$val["CenterName"].'</td>';
				$html .= '<td style="text-align:left;">'.$OrderStatus.'</td>';
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
		public function PurchaseOrderList()
		{
			if (!has_permission_new('PurchOrderList', '', 'view')) {
				access_denied('Invoice Items');
			}
			$data['centermaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
			$data['products'] = $this->PurchaseModel->GetPurchOrderItemList();
			$data['clients'] = $this->PurchaseModel->GetPurchOrderPartyList();
			$data['company_detail'] = $this->PurchaseModel->get_company_detail();
			$this->load->view('admin/PurchaseMaster/PurchaseOrderList',$data);
		}
    //========================== Get Kirti One Purchase Order List ==========================
	public function GetFilterDataOrderDetails()
	{
		$data = array(
        'from_date' => $this->input->post('from_date'),
        'to_date' => $this->input->post('to_date'),
        'order_status'=> $this->input->post('order_status'),
        'AccountID'=>$this->input->post('AccountID'),
        'CenterID'=>$this->input->post('CenterID'),
        'ItemID'=>$this->input->post('ItemID'),
        'Report_type'=>$this->input->post('Report_type'),
		'Entry_type'=>$this->input->post('Entry_type')
		);
		$result = $this->PurchaseModel->getItemOrderDetailsDB($data);
		$redirectUrl = admin_url('PurchaseMaster/AddEditPurchaseOrderNew');
		$Report_type = $this->input->post('Report_type');
		$html = '';
		$html .= '<thead>';
		if($Report_type == "1"){
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">PO.No</th>';
			$html .= '<th style="text-align:left;">PO Date</th>';
			$html .= '<th style="text-align:left;">Vendor Doc. No.</th>';
			$html .= '<th style="text-align:left;">Center Name</th> ';
			$html .= '<th style="text-align:left;">Center GSTIN</th> ';
			$html .= '<th style="text-align:left;">Party Name</th> ';
			$html .= '<th style="text-align:left;">GSTIN</th> ';
			$html .= '<th style="text-align:left;">Order Amt</th> ';
			$html .= '<th style="text-align:left;">Disc Amt</th> ';
			$html .= '<th style="text-align:left;">Taxable Amt</th>';
			$html .= '<th style="text-align:left;">CGST Amt</th>';
			$html .= '<th style="text-align:left;">SGST Amt</th>';
			$html .= '<th style="text-align:left;">IGST Amt</th>';
			$html .= '<th style="text-align:left;">Net Amt</th>';
			$html .= '<th style="text-align:left;">Order Status</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="filter_data_table">';
			$data["Report_type"] = '2';
			$ItemData = $this->PurchaseModel->getItemOrderDetailsDB($data);
		}else if($Report_type == "2"){
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">PO.No</th>';
			$html .= '<th style="text-align:left;">PO Date</th>';
			$html .= '<th style="text-align:left;">Vendor Doc. No.</th>';
			$html .= '<th style="text-align:left;">Center Name</th>';
			$html .= '<th style="text-align:left;">Center GSTIN</th> ';
			$html .= '<th style="text-align:left;">Party Name</th>';
			$html .= '<th style="text-align:left;">GSTIN</th> ';
			$html .= '<th style="text-align:left;">Item Name</th>';
			$html .= '<th style="text-align:left;">HSN Code</th>';
			$html .= '<th style="text-align:left;">Unit</th>';
			$html .= '<th style="text-align:left;">Quantity</th>';
			$html .= '<th style="text-align:left;">Item Amt</th>';
			$html .= '<th style="text-align:left;">Disc Amt</th>';
			$html .= '<th style="text-align:left;">GST%</th>';
			$html .= '<th style="text-align:left;">Taxable Amt</th>';
			$html .= '<th style="text-align:left;">CGST Amt</th>';
			$html .= '<th style="text-align:left;">SGST Amt</th>';
			$html .= '<th style="text-align:left;">IGST Amt</th>';
			$html .= '<th style="text-align:left;">Net Amt</th>';
			$html .= '<th style="text-align:left;">Order Status</th>';
			$html .= '</tr>';
		}else if($Report_type == "3"){
		    $ItemData = $this->PurchaseModel->getItemOrderDetailsDB($data);
		    $html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr. No.</th>';
			$html .= '<th style="text-align:left;">HSN Code</th>';
			$html .= '<th style="text-align:left;">Description</th>';
			$html .= '<th style="text-align:left;">UQC</th>';
			$html .= '<th style="text-align:left;">Total Quantity</th>';
			$html .= '<th style="text-align:left;">Total Value</th>';
			$html .= '<th style="text-align:left;">GST Rate</th>';
			$html .= '<th style="text-align:left;">Taxable Amt</th>';
			$html .= '<th style="text-align:left;">IGST Amt</th>';
			$html .= '<th style="text-align:left;">CGST Amt</th>';
			$html .= '<th style="text-align:left;">SGST Amt</th>';
			$html .= '<th style="text-align:left;">CESS Amt</th>';
			$html .= '</tr>';
		}
		$totalQtySum = 0;
		$TotalOrderAmt = 0;
		$TotalDiscAmt = 0;
		$TotalTaxableAmt = 0;
		$TotalCGSTAmt = 0;
		$TotalSGSTAmt = 0;
		$TotalIGSTAmt = 0;
		$TotalNetAmt = 0;
		foreach($result as $key=>$value)
		{
			if($value['OrderStatus'] == "F") {
				$OrderStat = "Completed";
			} elseif ($value['OrderStatus'] == "C") {
				$OrderStat = "Cancelled";
			}
			if($Report_type == "1"){
				$ItemTotal = 0;
				$ItemDiscAmt = 0;
				$ItemGstAmt = 0;
				$ItemNetTotal = 0;
				$GSTIN = "";
				$GSTPer = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$OrdTaxableAmt = 0;
				foreach($ItemData as $key1=>$val2){
					if($value["PurchID"] == $val2["OrderID"]){
					    $TaxableAmt = $val2["OrderAmt"] - $val2["DiscAmt"];
					    $GSTIN = $val2['gstin'];
					    $GSTPer = $val2['cgst'] + $val2['sgst'] + $val2['igst'];
					    $CGSTAmt += $val2['cgstamt'];
					    $SGSTAmt += $val2['sgstamt'];
					    $IGSTAmt += $val2['igstamt'];
					    $OrdTaxableAmt += $TaxableAmt;
						$gstamt = $val2['cgstamt'] + $val2['sgstamt'] + $val2['igstamt'];
						$ItemTotal += $val2["OrderAmt"];
						$ItemDiscAmt += $val2["DiscAmt"];
						$ItemGstAmt += $gstamt;
						$ItemNetTotal += $val2["NetOrderAmt"];
					}
				}
				$html .= '<tr onclick="window.open(\'' . $redirectUrl . '/' . $value['PurchID'] . '\', \'_blank\');">';
				//$html .= '<tr onclick="window.open(\''.$redirectUrl.'?OrderId='.$value["PurchID"].'\', \'_blank\');">';
				$html .= '<td>'.($key+1).'</td>';
				$html .= '<td>'.$value["PurchID"].'</td>';
				$html .= '<td>'._d(substr($value["Transdate"],0,10)).'</td>';
				$html .= '<td>'.$value['InvoiceNo'].'</td>';
				$html .= '<td>'.$value['CenterName'].'</td>';
				$html .= '<td>'.$value['GSTNo'].'</td>';
				$html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
				$html .= '<td>'.$GSTIN.'</td>';
				$html .= '<td style="text-align:right;">' . number_format($ItemTotal, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($ItemDiscAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdTaxableAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($CGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($SGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($IGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($ItemNetTotal, 2, '.', '') . '</td>';
				$html .= '<td>'.$OrderStat.'</td>';
				$html .= '</tr>';
				$TotalOrderAmt += $ItemTotal;
				$TotalDiscAmt += $ItemDiscAmt;
				$TotalTaxableAmt += $OrdTaxableAmt;
				$TotalCGSTAmt += $CGSTAmt;
				$TotalSGSTAmt += $SGSTAmt;
				$TotalIGSTAmt += $IGSTAmt;
				$TotalNetAmt += $ItemNetTotal;
			}elseif($Report_type =="2"){
			    $GSTIN = $value['gstin'];
				//$html .= '<tr onclick="window.open(\''.$redirectUrl.'?OrderId='.$value["PurchID"].'\', \'_blank\');">';
				$html .= '<tr onclick="window.open(\'' . $redirectUrl . '/' . $value['PurchID'] . '\', \'_blank\');">';
				$html .= '<td>'.($key+1).'</td>';
				$html .= '<td>'.$value["PurchID"].'</td>';
				$html .= '<td>'._d(substr($value["TransDate"],0,10)).'</td>';
				$html .= '<td>'.$value['InvoiceNo'].'</td>';
				$html .= '<td>'.$value['CenterName'].'</td>';
				$html .= '<td>'.$value['GSTNo'].'</td>';
				$html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
				$html .= '<td>'.$GSTIN.'</td>';
				$html .= '<td>'.$value['ProductName'].'</td>';
				$html .= '<td>'.$value['hsn_code'].'</td>';
				$html .= '<td>'.$value['unit'].'</td>';
				$gstamt = $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'];
				$GSTPer = ($value['cgst'] != 0.00) ? ($value['cgst'] + $value['sgst']) : $value['igst'];
				$TaxableAmt = $value['OrderAmt'] - $value['DiscAmt'];
				$html .= '<td style="text-align:right;">' . number_format($value['OrderQty'], 2, '.', '') . '</td>';
				$totalQtySum += $value['OrderQty'];
				$html .= '<td style="text-align:right;">' . number_format($value['OrderAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($GSTPer, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($TaxableAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['NetOrderAmt'], 2, '.', '') . '</td>';
				$html .= '<td>'.$OrderStat.'</td>';
				$html .= '</tr>';
				$TotalOrderAmt += $value['OrderAmt'];
				$TotalDiscAmt += $value['DiscAmt'];
				$TotalTaxableAmt += $TaxableAmt;
				$TotalCGSTAmt += $value['cgstamt'];
				$TotalSGSTAmt += $value['sgstamt'];
				$TotalIGSTAmt += $value['igstamt'];
				$TotalNetAmt += $value['NetOrderAmt'];
			}elseif($Report_type == "3"){
			    $html .= '<tr>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<td style="text-align:right;"></td>';
			    $html .= '<tr>';
			}
		}
		if($Report_type == "1"){
			$html .= '<tr>';
			$html .= '<td colspan="8" style="text-align:right;"><strong>Total</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalOrderAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalDiscAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalTaxableAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalCGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalSGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalIGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalNetAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td></td>';
			$html .= '</tr>';
		}elseif($Report_type == "2"){
			$html .= '<tr>';
			$html .= '<td colspan="11" style="text-align:right;"><strong>Total</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($totalQtySum, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalOrderAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalDiscAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalTaxableAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalCGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalSGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalIGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalNetAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td></td>';
			$html .= '</tr>';
		}elseif($Report_type == "3"){
		    $html .= '<tr>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<td style="text-align:right;"></td>';
		    $html .= '<tr>';
		}
		$html .= '</body>';
		echo $html;
	}
//============= Export Kirti One Purchase Order List ====================================
	public function export_PurchaseOrderlist()
	{
		if (!has_permission_new('PurchOrderList', '', 'view')) {
			access_denied('Invoice Items');
		}
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post())
		{
			$company_detail = $this->PurchaseModel->get_company_detail();
			$post_data = $this->input->post();
			$from_date = $post_data['from_date'];
			$to_date = $post_data['to_date'];
			$center_name = $post_data['Centertext'];
			$report_type =  $post_data['ReportTypetext'];
			$item = $post_data['ItemName'];
			$status = $post_data['order_statusText'];
			$AccountName = $post_data['Partyname'];
			$Report_type =  $post_data['Report_type'];
			$Entry_type =  $post_data['Entry_type'];
			$EntryName = $post_data['EntryName'];
			$result = $this->PurchaseModel->getItemOrderDetailsDB($post_data);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address, );
			$filters = "From date: " . $from_date . ", To date: " . $to_date . ", Entry Type: " . $EntryName .
			", Center: " . $center_name . ", Report Type: " . $report_type .
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
				$ItemData = $this->PurchaseModel->getItemOrderDetailsDB($post_data2);
				$set_col_tk["OrderID"] = 'PO.No';
				$set_col_tk["Transdate"] = 'PO Date';
				$set_col_tk["Vendor Doc. No."] = 'Vendor Doc. No.';
				$set_col_tk["CenterName"] = 'Center Name';
				$set_col_tk["CenterGST"] = 'Center GSTIN';
				$set_col_tk["company"] = 'Party Name';
				$set_col_tk["GSTIN"] = 'GSTIN';
				$set_col_tk["ItemTotal"] = 'Order Amt';
				$set_col_tk["ItemDiscAmt"] = 'Disc Amt';
				$set_col_tk["TaxableAmt"] = 'Taxable Amt';
				$set_col_tk["CGSTAmt"] = 'CGST Amt';
				$set_col_tk["SGSTAmt"] = 'SGST Amt';
				$set_col_tk["IGSTAmt"] = 'IGST Amt';
				$set_col_tk['ItemNetTotal'] = 'Net Amt';
				$set_col_tk['status'] = 'Order Status';
			}else {
				$set_col_tk["OrderID"] = 'PO.No';
				$set_col_tk["Transdate"] = 'PO Date';
				$set_col_tk["Vendor Doc. No."] = 'Vendor Doc. No.';
				$set_col_tk["CenterName"] = 'Center Name';
				$set_col_tk["CenterGST"] = 'Center GSTIN';
				$set_col_tk["company"] = 'Party Name';
				$set_col_tk["GSTIN"] = 'GSTIN';
				$set_col_tk["ProductName"] = 'Item Name';
				$set_col_tk["HSNCode"] = 'HSN Code';
				$set_col_tk["Unit"] = 'Unit';
				$set_col_tk["itemqty"] = 'Quantity';
				$set_col_tk["OrderAmt"] = 'Item Amt';
				$set_col_tk["discountamt"] = 'Disc Amt';
				$set_col_tk["GST"] = 'GST%';
				$set_col_tk["TaxableAmt"] = 'Taxable Amt';
				$set_col_tk["CGSTAmt"] = 'CGST Amt';
				$set_col_tk["SGSTAmt"] = 'SGST Amt';
				$set_col_tk["IGSTAmt"] = 'IGST Amt';
				$set_col_tk["NetOrdAmt"] = 'Net Amt';
				$set_col_tk['status'] = 'Order Status';
			}
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$totalQtySum = 0;
    		$TotalOrderAmt = 0;
    		$TotalDiscAmt = 0;
    		$TotalTaxableAmt = 0;
    		$TotalCGSTAmt = 0;
    		$TotalSGSTAmt = 0;
    		$TotalIGSTAmt = 0;
    		$TotalNetAmt = 0;
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
    				$GSTIN = "";
    				$GSTPer = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$OrdTaxableAmt = 0;
					foreach($ItemData as $key1=>$val2){
    					if($value["PurchID"] == $val2["OrderID"]){
    					    $TaxableAmt = $val2["OrderAmt"] - $val2["DiscAmt"];
    					    $GSTIN = $val2['gstin'];
    					    $GSTPer = $val2['cgst'] + $val2['sgst'] + $val2['igst'];
    					    $CGSTAmt += $val2['cgstamt'];
    					    $SGSTAmt += $val2['sgstamt'];
    					    $IGSTAmt += $val2['igstamt'];
    					    $OrdTaxableAmt += $TaxableAmt;
    						$gstamt = $val2['cgstamt'] + $val2['sgstamt'] + $val2['igstamt'];
    						$ItemTotal += $val2["OrderAmt"];
    						$ItemDiscAmt += $val2["DiscAmt"];
    						$ItemGstAmt += $gstamt;
    						$ItemNetTotal += $val2["NetOrderAmt"];
    					}
    				}
					// For Bill Wise Report
					$list_add = [];
					$list_add[] = $value["PurchID"];
					$list_add[] = _d(substr($value["Transdate"],0,10));
					$list_add[] = $value["InvoiceNo"];
					$list_add[] = $value["CenterName"];
					$list_add[] = $value["GSTNo"];
					$list_add[] = $value["company"];
					$list_add[] = $GSTIN;
					$list_add[] = number_format($ItemTotal, 2, '.', '');
					$list_add[] = number_format($ItemDiscAmt, 2, '.', '');
					$list_add[] = number_format($OrdTaxableAmt, 2, '.', '');
					$list_add[] = number_format($CGSTAmt, 2, '.', '');
					$list_add[] = number_format($SGSTAmt, 2, '.', '');
					$list_add[] = number_format($IGSTAmt, 2, '.', '');
					$list_add[] = number_format($ItemNetTotal, 2, '.', '');
					$list_add[] = $OrderStat;
					$writer->writeSheetRow('Sheet1', $list_add);
					$TotalOrderAmt += $ItemTotal;
    				$TotalDiscAmt += $ItemDiscAmt;
    				$TotalTaxableAmt += $OrdTaxableAmt;
    				$TotalCGSTAmt += $CGSTAmt;
    				$TotalSGSTAmt += $SGSTAmt;
    				$TotalIGSTAmt += $IGSTAmt;
    				$TotalNetAmt += $ItemNetTotal;
				}else
				{
				    $GSTIN = $value['gstin'];
					$list_add = [];
					$list_add[] = $value["PurchID"];
					$list_add[] = _d(substr($value["TransDate"],0,10));
					$list_add[] = $value["InvoiceNo"];
					$list_add[] = $value["CenterName"];
					$list_add[] = $value["GSTNo"];
					$list_add[] = $value["company"];
					$list_add[] = $GSTIN;
					$list_add[] = $value['ProductName'];
					$list_add[] = $value['hsn_code'];
					$list_add[] = $value['unit'];
					$GSTPer = ($value['cgst'] != 0.00) ? ($value['cgst'] + $value['sgst']) : $value['igst'];
					$TaxableAmt = $value['OrderAmt'] - $value['DiscAmt'];
					$list_add[] = number_format($value["OrderQty"], 2, '.', '');
					$totalQtySum += $value['OrderQty'];
					$list_add[] = number_format($value["OrderAmt"], 2, '.', '');
					$list_add[] = number_format($value["DiscAmt"], 2, '.', '');
					$list_add[] = number_format($GSTPer, 2, '.', '');
					$list_add[] = number_format($TaxableAmt, 2, '.', '');
					$list_add[] = number_format($value["cgstamt"], 2, '.', '');
					$list_add[] = number_format($value["sgstamt"], 2, '.', '');
					$list_add[] = number_format($value["igstamt"], 2, '.', '');
					$list_add[] = number_format($value["NetOrderAmt"], 2, '.', '');
					$list_add[] = $OrderStat;
					$writer->writeSheetRow('Sheet1', $list_add);
					$TotalOrderAmt += $value['OrderAmt'];
    				$TotalDiscAmt += $value['DiscAmt'];
    				$TotalTaxableAmt += $TaxableAmt;
    				$TotalCGSTAmt += $value['cgstamt'];
    				$TotalSGSTAmt += $value['sgstamt'];
    				$TotalIGSTAmt += $value['igstamt'];
    				$TotalNetAmt += $value['NetOrderAmt'];
				}
			}
			if ($post_data['Report_type'] == "1") {
				$sum_row = [];
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = 'Total';
				$sum_row[] = number_format($TotalOrderAmt, 2, '.', '');
				$sum_row[] = number_format($TotalDiscAmt, 2, '.', '');
				$sum_row[] = number_format($TotalTaxableAmt, 2, '.', '');
				$sum_row[] = number_format($TotalCGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalSGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalIGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalNetAmt, 2, '.', '');
				$sum_row[] = '';
			}else{
				$sum_row = [];
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = '';
				$sum_row[] = 'Total';
				$sum_row[] = number_format($totalQtySum, 2, '.', '');
				$sum_row[] = number_format($TotalOrderAmt, 2, '.', '');
				$sum_row[] = number_format($TotalDiscAmt, 2, '.', '');
				$sum_row[] = '';
				$sum_row[] = number_format($TotalTaxableAmt, 2, '.', '');
				$sum_row[] = number_format($TotalCGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalSGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalIGSTAmt, 2, '.', '');
				$sum_row[] = number_format($TotalNetAmt, 2, '.', '');
				$sum_row[] = '';
			}
			$writer->writeSheetRow('Sheet1', $sum_row);
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'PurchaseOrderList.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
            'site_url' => site_url(),
            'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
		public function CancelOrderWiseItems()
		{
			$poId = $this->input->post('poId');
			if($poId !="")
			{
				$where = '(PurchID="'.$poId.'")';
				$orderDetails = $this->PurchaseModel->get_data($tablename="tblK1purchasemaster",$where);
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
				$cancelOrder = $this->PurchaseModel->edit_data($tablename="tblK1purchasemaster",$where,$updateOrderData);
				$wh = '(OrderID="'.$poId.'")';
				$updateItemData = array(
                'TransDate2'=>date('Y-m-d h:i:s'),
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
				$cancelItemdata = $this->PurchaseModel->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);
			}
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
		public function CancelOrderWiseItemsInward()
		{
		    $poId = $this->input->post('poId');
			if($poId !="")
			{
				$where = '(PurchID="'.$poId.'")';
				$orderDetails = $this->PurchaseModel->get_data($tablename="tblK1Inwardmaster",$where);
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
				$cancelOrder = $this->PurchaseModel->edit_data($tablename="tblK1Inwardmaster",$where,$updateOrderData);
				$wh = '(OrderID="'.$poId.'")';
				$updateItemData = array(
                'TransDate2'=>date('Y-m-d h:i:s'),
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
				$cancelItemdata = $this->PurchaseModel->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);
			}
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
		public function CancelOrderWiseRequestItems()
		{
			$poId = $this->input->post('poId');
			if($poId !="")
			{
				$where = '(PurchID="'.$poId.'")';
				$orderDetails = $this->PurchaseModel->get_data($tablename="tblK1purchase_request_master",$where);
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
				$cancelOrder = $this->PurchaseModel->edit_data($tablename="tblK1purchase_request_master",$where,$updateOrderData);
				$wh = '(OrderID="'.$poId.'")';
				$updateItemData = array(
                'TransDate2'=>date('Y-m-d h:i:s'),
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
				$cancelItemdata = $this->PurchaseModel->edit_data($tablename="tblK1history",$wh,$updateItemData);
				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);
			}
			else
			{
				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
			}
		}
//====================== Cancel Purchase Order =================================
	public function CancelOrderWisePOItems()
	{
		$poId = $this->input->post('poId');
		if($poId !="")
		{
			$where = '(PurchID="'.$poId.'")';
			$orderDetails = $this->PurchaseModel->get_data($tablename="tblK1purchasemaster",$where);
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
			$cancelOrder = $this->PurchaseModel->edit_data($tablename="tblK1purchasemaster",$where,$updateOrderData);
			$wh = '(OrderID="'.$poId.'")';
			$updateItemData = array(
            'TransDate2'=>date('Y-m-d h:i:s'),
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
			$cancelItemdata = $this->PurchaseModel->edit_data($tablename="tblK1history",$wh,$updateItemData);
			echo json_encode(['success' => true,'message' => 'Order cancel successfully']);
		}
		else
		{
			echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);
		}
	}
//====================== Approve Purchase Order ================================
	public function ApprovePurchaseOrder()
	{
		$poId = $this->input->post('poId');
		if($poId !="")
		{
			$where = '(PurchID="'.$poId.'")';
			$updateOrderData = array(
    			'OrderStatus'=>"A",
                'ApproveUserID'=>$_SESSION['username'],
                'ApproveTransDate'=>date('Y-m-d h:i:s')
			);
			$ApproveOrder = $this->PurchaseModel->edit_data($tablename="tblK1purchasemaster",$where,$updateOrderData);
            if($ApproveOrder){
                echo json_encode(['success' => true,'message' => 'Order Approve successfully']);
            }else{
                echo json_encode(['success' => false,'message' => 'Something Went Wrong, PLease try again']);
            }
		}else
		{
			echo json_encode(['success' => false, 'message' => 'Something Went Wrong, Please try again']);
		}
	}
//====================== Page Load Purchase return =============================
    public function AddEditPurchaseReturnInvoice($PINumber = '')
	{
		if (!has_permission_new('PurchaseReturnInvoice', '', 'view')) {
			access_denied('PurchaseReturnInvoice');
		}
		if ($this->input->post()) {
			$pur_order_data = $this->input->post();
			$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
			if ($PINumber == '') {
				if (!has_permission_new('PurchaseReturnInvoice', '', 'create')) {
					access_denied('PurchaseReturnInvoice');
				}
				$id = $this->PurchaseModel->AddKirtiOneReturnPurchaseOrderNew($pur_order_data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('pur_order')));
					redirect(admin_url('PurchaseMaster/AddEditPurchaseReturnInvoice'));
				}
			}else{
				if (!has_permission_new('PurchaseReturnInvoice', '', 'edit')) {
					access_denied('PurchaseReturnInvoice');
				}
				$id = $this->PurchaseModel->UpdateKirtiOneReturnPurchaseInvoice($pur_order_data,$PINumber);
				if ($id) {
					set_alert('success', _l('updated_successfully', _l('pur_order')));
					redirect(admin_url('PurchaseMaster/AddEditPurchaseReturnInvoice'));
				}
			}
		}
		if ($PINumber == '') {
			$title = "Create Purchase Return Invoice";
		}else{
			$PurchaseDetails = $this->PurchaseModel->GetPurchaseReturnInvoiceDetails($PINumber);
			$data['purchase_details'] = $PurchaseDetails;
			/*echo "<pre>";
			print_r($PurchaseDetails);
			die;*/
			$PurchaseItemList = $this->PurchaseModel->GetPurchaseReturnInvoiceItemList($PurchaseDetails->PurchID,$PurchaseDetails->PurchRtnID);
			$data['pur_order_detail'] = json_encode($PurchaseItemList);
			$title = "Edit Purchase Invoice";
		}
		$centermaster = $this->PurchaseModel->GetAllAssignedAndPurchaseCenterList();
		$data['centermaster'] = $centermaster;
		$data['item_code'] = $this->PurchaseModel->get_items_code();
		$data['statelist'] = $this->PurchaseModel->getstatelist();
		$data['company_detail'] = $this->PurchaseModel->get_company_detail();
		$this->load->view('admin/PurchaseMaster/AddEditPurchaseReturnInvoice',$data);
	}
//====================== Get PI Vendor List By CenterID ========================
	public function PIVendorListByCenterID()
	{
		$CenterID = $this->input->post('CenterID');
		$data = $this->PurchaseModel->GetPIVendorListByCenterID($CenterID);
		echo json_encode($data);
	}
//================== Get PI List By CenterID And Vendor ========================
	public function PIListByCenterIDAndVendorID()
	{
		$CenterID = $this->input->post('CenterID');
		$AccountID = $this->input->post('VendorID');
		$data = $this->PurchaseModel->GetPIListByCenterIDAndVendorID($CenterID,$AccountID);
		echo json_encode($data);
	}
//=============== Get PI Item data for purchase return =========================
	public function PIItemDetailsForReturn()
	{
		// POST data
		$PINo = $this->input->post('PINo');
		// Get data
		$InwardData['historytbl'] = $this->PurchaseModel->GetPIItemDetailsForReturn($PINo);
		echo json_encode($InwardData);
	}
	public function ReturnValidityStockReport()
	{
		if (!has_permission_new('ReturnValidityStockReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['centermaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		// $data['products'] = $this->PurchaseModel->GetPurchOrderItemList();
		// $data['clients'] = $this->PurchaseModel->GetPurchOrderPartyList();
		$data['company_detail'] = $this->PurchaseModel->get_company_detail();
		$this->load->view('admin/PurchaseMaster/ReturnValidityStockReport',$data);
	}
	public function RVSRFilterDropdown(){
	    $data = $this->input->post();
	    $value = $data['value'];
	    $type = $data['type'];
	    $toType = $data['toType'];
	    $response = $this->PurchaseModel->getPOFilterDropdown($value, $type, $toType, $data);
	    echo json_encode($response);
	}
	public function getFilterReturnValidityStockReport()
	{
	    $data = $this->input->post();
	    $response['thead'] = '<thead>
	        <tr>
	            <th>Order ID</th>
	            <th>Account ID</th>
	            <th>Center</th>
	            <th>Item ID</th>
	            <th>Brand</th>
	            <th>MeasuredIN</th>
	            <th>PurchaseDate</th>
	            <th>Order Qty</th>
	            <th>Unit</th>
	            <th>Available Qty</th>
	            <th>Return Validity</th>
							<th>Days</th>
	            <th>Batch No</th>
	            <th>Exp Date</th>
	        </tr>
	    </thead>';
	    $AccountID = $data['AccountID'];
	    $ItemID = $data['ItemID'];
	    $rType = $data['rType'];
	    $filterData = $this->PurchaseModel->getFilterReturnValidityStockReport($data);
	    $tbody = '';
	    foreach($filterData as $list){
			$today = new DateTime(date('Y-m-d'));
			$returnDate = new DateTime($list->LastReturnDate);
			$dayGap = (int)$today->diff($returnDate)->format('%r%a');
			$showRow = false;
            switch ($rType) {
                case 'InDate':
                    $showRow = ($dayGap >= 0);
                    break;
                case '10Days':
                    $showRow = ($dayGap >= 0 && $dayGap <= 10);
                    break;
                case '5Days':
                    $showRow = ($dayGap >= 0 && $dayGap <= 5);
                    break;
                case 'OutDate':
                    $showRow = ($dayGap < 0);
                    break;
                case 'All':
                    $showRow = true;
                    break;
            }
            if (!$showRow) {
                continue;
            }
	        $tbody .= '<tr>
	            <td>'.$list->OrderID.'</td>
	            <td>'.$list->company.' - '.$list->AccountID.'</td>
	            <td>'.$list->CenterID.'</td>
	            <td>'.$list->ProductName.'</td>
	            <td>'.$list->BrandName.'</td>
	            <td>'.$list->unit.'</td>
	            <td>'._d(substr($list->TransDate,0,10)).'</td>
	            <td>'.$list->OrderQty.'</td>
	            <td>'.$list->unit.'</td>
	            <td>'.$list->AvailableQty.'</td>
	            <td>'._d(substr($list->LastReturnDate,0,10)).'</td>
				<td class="text-center"><b style="color : '.($dayGap >= 0 ? 'green' : 'red').';">'.$dayGap.'</b></td>
	            <td>'.$list->BatchNo.'</td>
	            <td>'._d(substr($list->ExpDate,0,10)).'</td>
	        </tr>';
	    }
	    $response['tbody'] = $tbody;
	    echo json_encode($response);
	}
	public function export_ReturnValidityStockReport()
	{
		if (!has_permission_new('ReturnValidityStockReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post())
		{
			$company_detail = $this->PurchaseModel->get_company_detail();
			$post_data = $this->input->post();
			$CenterID = empty($post_data['CenterID']) ? 'All' : ($this->PurchaseModel->getRowData('tblCenterMaster', 'CenterName', ['CenterID' => $post_data['CenterID']])->CenterName ?? 'All');
			$AccountID = empty($post_data['AccountID']) ? 'All' : ($this->PurchaseModel->getRowData('tblclients', 'company', ['AccountID' => $post_data['AccountID']])->company ?? 'All');
			$ItemID = empty($post_data['ItemID']) ? 'All' : ($this->PurchaseModel->getRowData('tblproduct', 'ProductName', ['ProductID' => $post_data['ItemID']])->ProductName ?? 'All');
			$filterData = $this->PurchaseModel->getFilterReturnValidityStockReport($post_data);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address, );
			$filters = "Center: " . $CenterID . ", Party: " . $AccountID.", Item: " . $ItemID;
			$filter_row = array($filters);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter_row);
			$set_col_tk = [];
    		$set_col_tk["OrderID"] = 'OrderID';
    		$set_col_tk["AccountID"] = 'Account ID';
    		$set_col_tk["CenterID"] = 'Center';
    		$set_col_tk["ItemID"] = 'Item Name';
    		$set_col_tk["Brand"] = 'Brand';
    		$set_col_tk["MeasuredIN"] = 'Measured IN';
    		$set_col_tk["PurchaseDate"] = 'Purchase Date';
    		$set_col_tk["OrderQty"] = 'Order Qty';
    		$set_col_tk["Unit"] = 'Unit';
    		$set_col_tk["AvailableQty"] = 'Available Qty';
    		$set_col_tk["LastValidity"] = 'Last Validity';
    		$set_col_tk["BatchNo"] = 'BatchNo';
    		$set_col_tk["ExpDate"] = 'Exp Date';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			foreach($filterData as $list){
			    $list_add = [];
	            $list_add[] = $list->OrderID;
	            $list_add[] = $list->company.' - '.$list->AccountID;
	            $list_add[] = $list->CenterID;
	            $list_add[] = $list->ProductName;
	            $list_add[] = $list->BrandName;
	            $list_add[] = $list->unit;
	            $list_add[] = _d(substr($list->TransDate,0,10));
	            $list_add[] = $list->OrderQty;
	            $list_add[] = $list->unit;
	            $list_add[] = $list->AvailableQty;
	            $list_add[] = _d(substr($list->LastReturnDate,0,10));
	            $list_add[] = $list->BatchNo;
	            $list_add[] = _d(substr($list->ExpDate,0,10));
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'ReturnValidityStockReport.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
            'site_url' => site_url(),
            'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	//========================== Kirti One Purchase Return Report page ==========================
	public function PurchaseReturnReport(){
		if (!has_permission_new('PurchaseReturnReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['FY'] = $this->session->userdata('finacial_year');
		$data['centermaster'] = $this->PurchaseModel->GetAllAssignedCenterList();
		$data['products'] = $this->PurchaseModel->GetPurchOrderItemList();
		$data['clients'] = $this->PurchaseModel->GetPurchOrderPartyList();
		$data['company_detail'] = $this->PurchaseModel->get_company_detail();
		$this->load->view('admin/PurchaseMaster/PurchaseReturnReport',$data);
	}
	//========================== Filter result for Kirti One Purchase Return Report ==========================
	public function GetPurchaseReturnReportFilterData(){
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'AccountID'=>$this->input->post('AccountID'),
			'CenterID'=>$this->input->post('CenterID'),
			'ItemID'=>$this->input->post('ItemID'),
			'ReportType'=>$this->input->post('Report_type'),
		);
		$result = $this->PurchaseModel->getPurchaseReturnReportFilter($data);
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
			$html .= '<th style="text-align:left;">Purch Amt</th>';
			$html .= '<th style="text-align:left;">Disc Amt</th>';
			$html .= '<th style="text-align:left;">CGST Amt</th>';
			$html .= '<th style="text-align:left;">SGST Amt</th>';
			$html .= '<th style="text-align:left;">IGST Amt</th>';
			$html .= '<th style="text-align:left;">Net Amt</th>';
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="filter_data_table">';
			$Purchamt = $Discamt = $cgstamt = $sgstamt = $igstamt = $Invamt = 0;
			foreach($result as $key=>$value){
				$html .= '<tr>';
				$html .= '<td style="text-align:center;">'.($key+1).'</td>';
				$html .= '<td style="text-align:center;">'.$value['PurchRtnID'].'</td>';
				$html .= '<td style="text-align:center;">'.$value['PurchID'].'</td>';
				$html .= '<td style="text-align:center;">'._d(substr($value["Transdate"],0,10)).'</td>';
				$html .= '<td>'.$value['AccountName'].'</td>';
				$html .= '<td>'.$value['CenterName'].'</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['Purchamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['Discamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['Invamt'], 2, '.', '') . '</td>';
				$html .= '</tr>';
				$Purchamt += $value['Purchamt'];
				$Discamt += $value['Discamt'];
				$cgstamt += $value['cgstamt'];
				$sgstamt += $value['sgstamt'];
				$igstamt += $value['igstamt'];
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
			$OrderQty = $PurchRate = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $NetOrderAmt = 0;
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
				$html .= '<td style="text-align:right;">' . number_format($value['PurchRate'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['cgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['sgstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['igstamt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['NetOrderAmt'], 2, '.', '') . '</td>';
				$html .= '</tr>';
				$OrderQty += $value['OrderQty'];
				$PurchRate += $value['PurchRate'];
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
			$html .= '<td style="text-align:right;">' . number_format($PurchRate, 2, '.', '') . '</td>';
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
	//============= Export Kirti One Purchase Return Report List ====================================
    public function export_GetPurchaseReturnReportFilterData(){
		if (!has_permission_new('PurchaseReturnReport', '', 'view')) {
			access_denied('Invoice Items');
		}
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post())
		{
			$company_detail = $this->PurchaseModel->get_company_detail();
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
			$result = $this->PurchaseModel->getPurchaseReturnReportFilter($data);
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
				$Purchamt = $Discamt = $cgstamt = $sgstamt = $igstamt = $Invamt = 0;
				foreach($result as $key=>$value){
					$list_add = [];
					$list_add[] = $value['PurchRtnID'];
					$list_add[] = $value['PurchID'];
					$list_add[] = _d(substr($value["Transdate"],0,10));
					$list_add[] = $value['AccountName'];
					$list_add[] = $value['CenterName'];
					$list_add[] = number_format($value['Purchamt'], 2, '.', '');
					$list_add[] = number_format($value['Discamt'], 2, '.', '');
					$list_add[] = number_format($value['cgstamt'], 2, '.', '');
					$list_add[] = number_format($value['sgstamt'], 2, '.', '');
					$list_add[] = number_format($value['igstamt'], 2, '.', '');
					$list_add[] = number_format($value['Invamt'], 2, '.', '');
					$writer->writeSheetRow('Sheet1', $list_add);
					$Purchamt += $value['Purchamt'];
					$Discamt += $value['Discamt'];
					$cgstamt += $value['cgstamt'];
					$sgstamt += $value['sgstamt'];
					$igstamt += $value['igstamt'];
					$Invamt += $value['Invamt'];
				}
			}else{
				$OrderQty = $PurchRate = $DiscAmt = $cgstamt = $sgstamt = $igstamt = $NetOrderAmt = 0;
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
					$list_add[] = number_format($value['PurchRate'], 2, '.', '');
					$list_add[] = number_format($value['DiscAmt'], 2, '.', '');
					$list_add[] = number_format($value['cgstamt'], 2, '.', '');
					$list_add[] = number_format($value['sgstamt'], 2, '.', '');
					$list_add[] = number_format($value['igstamt'], 2, '.', '');
					$list_add[] = number_format($value['NetOrderAmt'], 2, '.', '');
					$writer->writeSheetRow('Sheet1', $list_add);
					$OrderQty += $value['OrderQty'];
					$PurchRate += $value['PurchRate'];
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
				$sum_row[] = number_format($Purchamt, 2, '.', '');
				$sum_row[] = number_format($Discamt, 2, '.', '');
				$sum_row[] = number_format($cgstamt, 2, '.', '');
				$sum_row[] = number_format($sgstamt, 2, '.', '');
				$sum_row[] = number_format($igstamt, 2, '.', '');
				$sum_row[] = number_format($Invamt, 2, '.', '');
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
				$sum_row[] = number_format($PurchRate, 2, '.', '');
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
			$filename = 'PurchaseReturnReport.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
            'site_url' => site_url(),
            'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}

	public function PurchaseOrderReminderReport()
	{
		if (
			!has_permission_new('PurchaseOrderReminderReport', '', 'view')
			&& !has_permission_new('PurchaseOrderReminderReport', '', 'print')
			&& !has_permission_new('PurchaseOrderReminderReport', '', 'export')
		) {
			access_denied('Purchase Order Reminder Report');
		}
		$data['company_detail'] = $this->PurchaseModel->get_company_detail();
		$data['can_view_reminder_report'] = has_permission_new('PurchaseOrderReminderReport', '', 'view');
		$data['can_print_reminder_report'] = has_permission_new('PurchaseOrderReminderReport', '', 'print');
		$data['can_export_reminder_report'] = has_permission_new('PurchaseOrderReminderReport', '', 'export');
		$this->load->view('admin/PurchaseMaster/PurchaseOrderReminderReport', $data);
	}

	public function load_data_for_purchase_order_reminder_report()
	{
		if (
			!has_permission_new('PurchaseOrderReminderReport', '', 'view')
			&& !has_permission_new('PurchaseOrderReminderReport', '', 'print')
		) {
			access_denied('Purchase Order Reminder Report');
		}
		$data = [
			'from_date' => $this->input->post('from_date'),
			'to_date'   => $this->input->post('to_date'),
		];
		$result = $this->PurchaseModel->get_purchase_order_reminder_report_list($data);
		$html = '';
		$i = 1;
		foreach ($result as $row) {
			$url = admin_url('PurchaseMaster/AddEditPurchaseOrderNew/' . $row['PurchID']);
			$status = !empty($row['ReminderSent'])
				? '<span class="label label-success">Sent</span>'
				: '<span class="label label-warning">Pending</span>';
			$remark = !empty($row['ReminderRemark']) ? html_escape($row['ReminderRemark']) : '-';
			$html .= '<tr>';
			$html .= '<td style="text-align:center;">' . $i . '</td>';
			$html .= '<td style="text-align:left;"><a href="' . $url . '" target="_blank">' . html_escape($row['PurchID']) . '</a></td>';
			$html .= '<td style="text-align:left;">' . _d($row['ReminderDate']) . '</td>';
			$html .= '<td style="text-align:left;">' . $status . '</td>';
			$html .= '<td style="text-align:left;">' . $remark . '</td>';
			$html .= '</tr>';
			$i++;
		}
		if ($html === '') {
			$html = '<tr><td colspan="5" style="text-align:center;">No records found.</td></tr>';
		}
		echo $html;
	}

	public function export_PurchaseOrderReminderReport()
	{
		if (!has_permission_new('PurchaseOrderReminderReport', '', 'export')) {
			access_denied('Purchase Order Reminder Report');
		}
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->PurchaseModel->get_company_detail();
			$post_data = $this->input->post();
			$result = $this->PurchaseModel->get_purchase_order_reminder_report_list($post_data);
			$writer = new XLSXWriter();
			$writer->markMergedCell('Sheet1', 0, 0, 0, 4);
			$writer->writeSheetRow('Sheet1', [$company_detail->company_name]);
			$writer->markMergedCell('Sheet1', 1, 0, 1, 4);
			$writer->writeSheetRow('Sheet1', [$company_detail->address]);
			$filters = [];
			if (!empty($post_data['from_date']) && !empty($post_data['to_date'])) {
				$filters[] = 'Date Range: ' . $post_data['from_date'] . ' to ' . $post_data['to_date'];
			}
			$writer->markMergedCell('Sheet1', 2, 0, 2, 4);
			$writer->writeSheetRow('Sheet1', [implode(' , ', $filters)]);
			$writer->writeSheetRow('Sheet1', [
				'Sr.No',
				'Purchase Order ID',
				'Reminder Date',
				'Reminder Status',
				'Reminder Notes',
			]);
			$i = 1;
			foreach ($result as $value) {
				$writer->writeSheetRow('Sheet1', [
					$i,
					$value['PurchID'],
					_d($value['ReminderDate']),
					!empty($value['ReminderSent']) ? 'Sent' : 'Pending',
					!empty($value['ReminderRemark']) ? $value['ReminderRemark'] : '-',
				]);
				$i++;
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'PurchaseOrderReminderReport.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url' => site_url(),
				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
}