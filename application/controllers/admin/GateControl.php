<?php
defined('BASEPATH') or exit('No direct script access allowed');
class GateControl extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GateControl_model');
		$this->load->model('sale_reports_model');
		$this->load->model('order_model');
		$this->load->model('MiscDashboard_model');
		$this->load->helper('url', 'form');
	}
	//===================== Gate In List Page ======================================
	public function index()
	{
		if (!has_permission_new('Ganerate_gatein', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data['trades'] = $this->GateControl_model->getTrades();
		$data['party'] = $this->GateControl_model->getParty();
		$data['items'] = $this->GateControl_model->getItems();
		$data['title'] = "Gate Control";
		$this->load->view('admin/gateControl/gateControl', $data);
	}
	//=========================== Create ASN Page View =============================
	public function ASNGenerate()
	{
		if (!has_permission_new('Ganerate_asn', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data['party'] = $this->GateControl_model->getParty();
		$data['items'] = $this->GateControl_model->getItems();
		$data['title'] = "ASN Generate";
		$this->load->view('admin/gateControl/ASNGenerate', $data);
	}
	//========================== Trade List to Create ASN ==========================
	public function Booking_for_ASNGenerate()
	{
		$BookingList = $this->GateControl_model->GetTrades_for_asn();
		$html = "";
		foreach ($BookingList as $key => $value) {
			if ($value['CustomerType'] == '1') {
				$PartyType = 'Farmer';
			}
			if ($value['CustomerType'] == '2') {
				$PartyType = 'Broker';
			}
			if ($value['CustomerType'] == '3') {
				$PartyType = 'Trader';
			}
			if ($value['CustomerType'] == '4') {
				$PartyType = 'Corporate/Processor';
			}
			if ($value['company'] == "") {
				$PartyName = $value['firstname'] . " " . $value['lastname'];
			} else {
				$PartyName = $value['company'];
			}
			if ($value['e_quantity'] == "" || $value['e_quantity'] == null) {
				$qty = $value['quantity'];
			} else {
				$qty = $value['e_quantity'];
			}
			$html .= '<tr class="get_AccountID" data-id="' . $value["BookingID"] . '">';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $qty . ' ' . $value["unit"] . '</td>';
			$html .= '<td>' . number_format($value["TotalAsnQty"], 2) . '</td>';
			$html .= '<td>' . number_format($value["TotalInOut"], 2) . '</td>';
			$html .= '<td>' . $value['TType2'] . '</td>';
			$html .= '<td>' . $value["CenterID"] . '</td>';
			$html .= '<td>' . $PartyType . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['ItemID'] . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}

	public function Booking_for_ASNGenerate_ByAccountID()
	{
		$AccountID = $this->input->post('AccountID');
		$BookingList = $this->GateControl_model->GetTrades_for_asn_ByAccountID($AccountID);
		$html = "";
		foreach ($BookingList as $key => $value) {
			if ($value['CustomerType'] == '1') {
				$PartyType = 'Farmer';
			}
			if ($value['CustomerType'] == '2') {
				$PartyType = 'Broker';
			}
			if ($value['CustomerType'] == '3') {
				$PartyType = 'Trader';
			}
			if ($value['CustomerType'] == '4') {
				$PartyType = 'Corporate/Processor';
			}
			if ($value['company'] == "") {
				$PartyName = $value['firstname'] . " " . $value['lastname'];
			} else {
				$PartyName = $value['company'];
			}
			if ($value['e_quantity'] == "" || $value['e_quantity'] == null) {
				$qty = $value['quantity'];
			} else {
				$qty = $value['e_quantity'];
			}
			$html .= '<tr class="get_AccountID" data-id="' . $value["BookingID"] . '">';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $qty . ' ' . $value["unit"] . '</td>';
			$html .= '<td>' . number_format($value["TotalAsnQty"], 2) . '</td>';
			$html .= '<td>' . number_format($value["TotalInOut"], 2) . '</td>';
			$html .= '<td>' . $value['TType2'] . '</td>';
			$html .= '<td>' . $value["CenterID"] . '</td>';
			$html .= '<td>' . $PartyType . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['ItemID'] . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}

	public function GetAsnListPopUp()
	{
		$BookingList = $this->GateControl_model->GetAsnGeneratedList();
		$html = "";
		foreach ($BookingList as $key => $value) {
			if ($value['CustomerType'] == '1') {
				$PartyType = 'Farmer';
			}
			if ($value['CustomerType'] == '2') {
				$PartyType = 'Broker';
			}
			if ($value['CustomerType'] == '3') {
				$PartyType = 'Trader';
			}
			if ($value['CustomerType'] == '4') {
				$PartyType = 'Corporate/Processor';
			}
			if ($value['company'] == null) {
				$PartyName = $value['firstname'] . ' ' . $value['lastname'];
			} else {
				$PartyName = $value['company'];
			}
			$html .= '<tr class="get_AccountID" data-id="' . $value["ASNID"] . '">';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value['ASNID'] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $value['TType2'] . '</td>';
			$html .= '<td>' . $value["CenterID"] . '</td>';
			$html .= '<td>' . $PartyType . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$html .= '<td>' . $value['quantity'] . '</td>';
			$html .= '<td>' . $value['Asn_WT_MT'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}

	public function GetAsnListPopUpByAccountID()
	{
		$AccountID = $this->input->post('AccountID');
		$BookingList = $this->GateControl_model->GetAsnGeneratedListByAccountID($AccountID);
		$html = "";
		foreach ($BookingList as $key => $value) {
			if ($value['CustomerType'] == '1') {
				$PartyType = 'Farmer';
			}
			if ($value['CustomerType'] == '2') {
				$PartyType = 'Broker';
			}
			if ($value['CustomerType'] == '3') {
				$PartyType = 'Trader';
			}
			if ($value['CustomerType'] == '4') {
				$PartyType = 'Corporate/Processor';
			}
			if ($value['company'] == null) {
				$PartyName = $value['firstname'] . ' ' . $value['lastname'];
			} else {
				$PartyName = $value['company'];
			}
			$html .= '<tr class="get_AccountID" data-id="' . $value["ASNID"] . '">';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value['ASNID'] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $value['TType2'] . '</td>';
			$html .= '<td>' . $value["CenterID"] . '</td>';
			$html .= '<td>' . $PartyType . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$html .= '<td>' . $value['quantity'] . '</td>';
			$html .= '<td>' . $value['Asn_WT_MT'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}

	public function GetSingleASN()
	{
		$ASNID = $this->input->post('ASNID');
		$result = $this->GateControl_model->GetSingleASN($ASNID);
		echo json_encode($result);
	}
	public function fetchItemRate()
	{
		$item = $this->input->post('item');
		$center = $this->input->post('center');
		$AccountType = $this->input->post('AccountType');
		if ($AccountType == "1") {
			$AccountType = $AccountType;
		} else {
			$AccountType = 2;
		}
		$Rate = $this->GateControl_model->GetTodaysRate($item, $center, $AccountType);
		echo json_encode($Rate);
	}
	//======================= Create New ASN =======================================
	public function GenerateASN()
	{
		if (!has_permission_new('Ganerate_asn', '', 'create')) {
			access_denied('Generate asn');
		}
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$payload = $this->input->post();
		// echo json_encode($payload); die;
		$BookingID = $this->input->post('BookingID');
		$AsnDate = $this->input->post('AsnDate');
		$dateObj = DateTime::createFromFormat('d/m/Y', $AsnDate);
		$formattedDate = (isset($dateObj) && !empty($dateObj)) ? $dateObj->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
		$dateVenObj = DateTime::createFromFormat('d/m/Y', $payload['ven_inv_date']);
		$payload['ven_inv_date'] = $dateVenObj ? $dateVenObj->format('Y-m-d') : null;
		$accid = $this->input->post('AccountID');
		$this->db->select('tblclients.*');
		$this->db->where('AccountID', $accid);
		$clientdetails = $this->db->get('tblclients')->row();
		$this->db->select('tbllead_master.*');
		$this->db->where('BookingID', $BookingID);
		$details = $this->db->get('tbllead_master')->row();
		if ($details->status == "1") {
			if ($this->input->post('VehicleNo') == '') {
				$vehicleNo = NULL;
			} else {
				$vehicleNo = $this->input->post('VehicleNo');
			}
			if ($this->input->post('Phone') == '') {
				$Phone = NULL;
			} else {
				$Phone = $this->input->post('Phone');
			}
			$CenterID = $details->CenterID;
			$GodownID = $details->WHID;
			$RateMasterRate = $details->Mastercurrentrate;
			$new_Number = get_number($CenterID, 'ASN');
			$number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
			$AsnID = "ASN" . $CenterID . date('d') . date('m') . date('y') . $number;
			$this->increment_number($CenterID, 'ASN');
			$Ttype = $this->input->post('TType');
			if ($Ttype != "P") {
				$rate = $this->input->post('ItemRate');
			} else {
				$rate = $this->input->post('basic_rate');
			}
			if ($Ttype == "A" || $Ttype == "T") {
				$finalrate = $rate;
			} else {
				$finalrate = null;
			}

			$files = ['ven_invoice_doc', 'ven_eway_bill_doc'];
			$this->load->library('upload');
			$path = FCPATH . 'assets/GateInDoc/';
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}
			foreach ($files as $field) {
				if (!empty($_FILES[$field]['name'])) {
					$extension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
					$config = [
						'upload_path'   => FCPATH . 'assets/GateInDoc/',
						'allowed_types' => 'jpg|jpeg|png|pdf',
						'max_size'      => 5120,
						'file_ext_tolower' => TRUE,
						'remove_spaces' => TRUE,
						'overwrite'     => FALSE,
						'file_name'     => $field . '_' . date('YmdHis') . '_' . uniqid()
					];
					$this->upload->initialize($config);
					if ($this->upload->do_upload($field)) {
						$uploadData = $this->upload->data();
						$payload[$field] = 'assets/GateInDoc/' . $uploadData['file_name'];
					} else {
						$payload[$field] = $this->upload->display_errors();
						// Optional:
						// echo $this->upload->display_errors();
						// exit;
					}
				} else {
					$payload[$field] = NULL;
				}
			}

			$data = array(
				'AccountID' => $payload['AccountID'],
				'ASNID' => $AsnID,
				'PlantID' => $selected_company,
				'FY' => $fy,
				'status' => 1,
				'BookingID' => $payload['BookingID'],
				'PartyID' => $details->PartyID,
				'CenterID' => $details->CenterID,
				'GodownID' => $GodownID,
				'basic_rate' => $rate,
				'Mastercurrentrate' => $RateMasterRate,
				'final_rate' => $finalrate,
				'ItemID' => ($payload['ItemID'] == '') ? $payload['Item'] : $payload['ItemID'],
				'quantity' => $payload['asn_qty_bag'] + $payload['asn_qty_ppbag'],
				'jute_quantity' => $payload['asn_qty_bag'],
				'pp_quantity' => $payload['asn_qty_ppbag'],
				'unit' => $payload['Unit'],
				'asn_date' => $formattedDate ?? date('Y-m-d H:i:s'),
				'asn_by' => $this->session->userdata('username'),
				'TType' => $payload['TType'],
				'TType2' => $payload['TType2'],
				'Asn_WT_MT' => $payload['asn_qty_mt'],
				'InvoiceAmt' => $payload['asn_amt'],
				'SalesRepName' => $payload['SalesPerson'],
				'SalesRepMobile' => $payload['Salesmobile'],
				'VehicleNo' => $vehicleNo,
				'Phone' => $Phone,
				'vendor_invoice_number' => $payload['ven_inv_number'] ?? null,
				'vendor_invoice_date' => $payload['ven_inv_date'] ?? null,
				'vendor_invoice_amount' => $payload['ven_inv_amt'] ?? null,
				'vendor_invoice_doc' => $payload['ven_invoice_doc'],
				'vendor_ewaybill_number' => $payload['ven_eway_bill_number'] ?? null,
				'vendor_ewaybill_doc' => $payload['ven_eway_bill_doc'],
				'VillageID' => $clientdetails->VillageID,
			);
			$result = $this->GateControl_model->GenerateASN($data);
			$this->db->select('tbllead_master.*,tblclients.company,tblclients.ShortCode,tblitems.PCItemID,tblitems.ItemID');
			$this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');
			$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
			$this->db->where('tbllead_master.BookingID', $BookingID);
			$leadMasterDetails = $this->db->get('tbllead_master')->row();
			// Send to PC Soft
			$trinvs_array = array([
				"party_no" => $leadMasterDetails->ShortCode,
				"your_ref" => $leadMasterDetails->BookingID,
				"truck_no" => $vehicleNo,
				"doc_ref" => $AsnID,
				"your_date" => date('Y-m-d H:i:s'),
				"doc_flnm" => NULL,
				"lr_no" => NULL,
				"lr_date" => NULL,
				"type_code" => NULL,
			]);
			$sporddtl_array = array([
				"im_code" => $leadMasterDetails->PCItemID,
				"im_qty" => $payload['asn_qty_mt'],
				"im_bag" => $payload['asn_qty_bag'] + $payload['asn_qty_ppbag'],
				"im_ordrate" => $leadMasterDetails->basic_rate
			]);
			$data_asn_array =  array(
				"cocd" => $leadMasterDetails->PartyID,
				"trinvs" => $trinvs_array,
				"sporddtl" => $sporddtl_array
			);
			$ASN_data = json_encode($data_asn_array);
			$curl = curl_init();
			curl_setopt_array(
				$curl,
				array(
					//-> LIVE URL
					CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/ASNinsert", // Live
					//CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/ASNinsert",// -> DEV URL
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $ASN_data,
					CURLOPT_HTTPHEADER => array(
						"content-type: application/json"
					),
				)
			);
			$apiResponse = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			$response_array = json_decode($apiResponse);
			$PcSoft_GIN = $response_array->doc_ref_number;
			$status = $response_array->Status;
			if ($status == true) {
				$insert_referance = array(
					"Type" => $details->TType,
					"Name" => "ASN",
					"GIC_Reference" => $AsnID,
					"pcsoft_doc_ref" => $PcSoft_GIN,
					"status" => $status
				);
				$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
			} else {
				$insert_referance = array(
					"Type" => $details->TType,
					"Name" => "ASN",
					"GIC_Reference" => $AsnID,
					"status" => $status
				);
				$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
			}
			$response = array(
				"result" => true,
				"ASNID" => $AsnID,
				"BookingID" => $leadMasterDetails->BookingID
			);
			echo json_encode($response);
		} else {
			echo json_encode(false);
		}
	}
	//================ Generate ASN QR Code ========================================
	public function GenerateASNQR($BookingID, $ASNID)
	{
		/* Load QR Code Library */
		$this->load->library('ciqrcode');
		$image_name  = 'ASNQR.png';
		/* QR Code File Directory Initialize */
		$dir = 'uploads/' . $BookingID . '/' . $ASNID . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0775, true);
		}
		/* QR Configuration  */
		$config['cacheable']    = true;
		$config['imagedir']     = $dir;
		$config['quality']      = true;
		$config['size']         = '1024';
		$config['black']        = array(255, 255, 255);
		$config['white']        = array(255, 255, 255);
		$this->ciqrcode->initialize($config);
		/* QR Data  */
		$params['data']     = $ASNID . ',' . $BookingID . ',' . "1";
		$params['level']    = 'L';
		$params['size']     = 10;
		$params['savename'] = FCPATH . $config['imagedir'] . $image_name;
		$this->ciqrcode->generate($params);
		/* Return Data */
		$QR = array(
			'content' => $ASNID . ',' . $BookingID,
			'file'    => $dir . $image_name,
			'name'    => $image_name
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('ASNID', $ASNID);
		$this->db->set('ASNQR', $QR['name']);
		$this->db->set('status', 1);
		$this->db->update('tblGateMaster');
		$flag = 1;
		$this->data['AsnDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $ASNID, $flag);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		$this->load->library('asn_pdf');
		$this->load->view('asn/asn_pdf', $this->data);
	}
	//============================ View ASN ========================================
	public function viewAsn($BookingID, $ASNID)
	{
		$flag = 1;
		$this->data['AsnDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $ASNID, $flag);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		$this->load->library('asn_pdf');
		$this->load->view('asn/asn_pdf', $this->data);
	}
	public function centerwise_commoditywise_purchase()
	{
		echo json_encode($this->GateControl_model->centerwise_commoditywise_purchase());
	}
	public function centerwise_commoditywise_deposit()
	{
		echo json_encode($this->GateControl_model->centerwise_commoditywise_deposit());
	}
	public function centerwise_commoditywise_deposit_stock()
	{
		echo json_encode($this->GateControl_model->centerwise_commoditywise_deposit_stock());
	}
	public function centerwise_commoditywise_purchase_stock()
	{
		echo json_encode($this->GateControl_model->centerwise_commoditywise_purchase_stock());
	}
	public function TradeTypeCenterWiseReport()
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data = array(
			'CenterID' => $this->input->post('CenterID'),
			'BookingType' => $this->input->post('BookingType')
		);
		$result = $this->GateControl_model->TradeTypeCenterWiseReport($data);
		$html = '';
		foreach ($result as $key => $value) {
			if ($this->input->post('BookingType') == "P") {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "CLEANING DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				} else if ($value['status'] == 13) {
					$status_val = "PAYMENT ADVICE REQUEST SENT";
				} else if ($value['status'] == 14) {
					$status_val = "PAYMENT ADVICE APROVE";
				} else if ($value['status'] == 14 && $value['IsPayment'] == "Y") {
					$status_val = "PAYMENT DONE";
				} else if ($value['status'] == 14 && $value['IsCD'] == "Y") {
					$status_val = "DEBIT NOTE GENERATED ";
				} else if ($value['status'] == 14 && $value['IsCD'] == "Y" && $value['IsPayment'] == "Y") {
					$status_val = "DEBIT NOTE & PAYMENT GENERATED ";
				}
			}
			if ($this->input->post('BookingType') == 'D') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 9) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 10) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 11) {
					$status_val = "EXIT ";
				}
			}
			$html .= '<tr>';
			$html .= '<td>' . $status_val . '</td>';
			$html .= '<td>' . $value["TotalCount"] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function GetAcceptedBookingListPopUp()
	{
		$BookingList = $this->GateControl_model->getTrades();
		$html = "";
		foreach ($BookingList as $key => $value) {
			if ($value['CustomerType'] == '1') {
				$PartyType = 'Farmer';
			}
			if ($value['CustomerType'] == '2') {
				$PartyType = 'Broker';
			}
			if ($value['CustomerType'] == '3') {
				$PartyType = 'Trader';
			}
			if ($value['CustomerType'] == '4') {
				$PartyType = 'Corporate/Processor';
			}
			if ($value['company'] == null) {
				$PartyName = $value['firstname'] . ' ' . $value['lastname'];
			} else {
				$PartyName = $value['company'];
			}
			if (($value['CenterID'] != '') || ($value['CenterID'] != null)) {
				$center = $value['CenterID'];
			} else {
				$center = $value['center'];
			}
			if ($value['e_quantity'] == "" || $value['e_quantity'] == null) {
				$qty = $value['quantity'];
			} else {
				$qty = $value['e_quantity'];
			}
			$html .= '<tr class="get_AccountID" data-id="' . $value["BookingID"] . '">';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $value['TType2'] . '</td>';
			$html .= '<td>' . $value["center"] . '</td>';
			$html .= '<td>' . $PartyType . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['ItemID'] . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$html .= '<td>' . $qty . '</td>';
			$html .= '<td>' . $value['unit'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function getSingleTrade()
	{
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->getSingleTrade($BookingID);
		echo json_encode($result);
	}
	public function increment_number($CenterID, $TType)
	{
		$this->db->set('Number', 'Number+1', false);
		$this->db->WHERE('CenterID', $CenterID);
		$this->db->WHERE('TType', $TType);
		$this->db->update(db_prefix() . 'numberformat');
	}
	public function checkForAsn()
	{
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->checkForAsnDb($BookingID);
		echo json_encode($result);
	}
	public function getCenterID()
	{
		$WHID = $this->input->post('WHID');
		$result = $this->GateControl_model->getCenterIDDB($WHID);
		echo json_encode($result);
	}
	public function ViewDO($BookingID, $ASNID)
	{
		$flag = 2;
		$this->data['DODetails'] = $this->GateControl_model->GetDODetails($BookingID, $ASNID);
		$this->load->library('payment_pdf');
		$this->load->view('doslip/doslip_pdf', $this->data);
	}
	public function GenerateDO()
	{
		if (!has_permission_new('Ganerate_gatein', '', 'create')) {
			access_denied('Generate gatein');
		}
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$BookingID = $this->input->post('BookingID');
		$AccountID = $this->input->post('AccountID');
		$ASNID = $this->input->post('ASNID');
		$CenterID = $this->input->post('CenterID');
		$ItemID = $this->input->post('ItemID');
		$basic_rate = $this->input->post('basic_rate');
		$asn_qty_mt = $this->input->post('asn_qty_mt');
		$asn_qty_bag = $this->input->post('asn_qty_bag');
		$DeliveryType = $this->input->post('DeliveryType');
		$DoAmount = $this->input->post('DoAmount');
		$PartyID = $this->input->post('PartyID');
		$GodownID = $this->input->post('GodownID');
		// Get DO Number Center Wise
		$new_Number = get_number($CenterID, 'DO');
		$number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
		$DOID = "DO" . $CenterID . date('d') . date('m') . date('y') . $number;
		$GetItemDetails = $this->GateControl_model->GetPartyStateItemTax($BookingID, $ASNID);
		$taxrate = $GetItemDetails->taxrate;
		$GSTAmt = $DoAmount * ($taxrate / 100);
		$SaleRate = $basic_rate + ($basic_rate * ($taxrate / 100));
		if ($GetItemDetails->state == "27") {
			$CGSTPer = $taxrate / 2;
			$SGSTPer = $taxrate / 2;
			$IGSTPer = 0;
			$CGSTAmt = $GSTAmt / 2;
			$SGSTAmt = $GSTAmt / 2;
			$IGSTAmt = 0;
		} else {
			$IGSTPer = $taxrate;
			$CGSTPer = 0;
			$SGSTPer = 0;
			$IGSTAmt = $GSTAmt;
			$CGSTAmt = 0;
			$SGSTAmt = 0;
		}
		$NetOrderAmt = $GSTAmt + $DoAmount;
		// Update Gate Master
		$GateMasterData = array(
			"quantity" => $asn_qty_bag,
			"Asn_WT_MT" => $asn_qty_mt,
			"InvoiceAmt" => $DoAmount,
			"DeliveryType" => $DeliveryType,
			"GodownID" => $GodownID,
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('ASNID', $ASNID);
		if ($this->db->update('tblGateMaster', $GateMasterData)) {
			$Do_array = array(
				"PlantID" => $selected_company,
				"FY" => $fy,
				"DOID" => $DOID,
				"ASNID" => $ASNID,
				"OrderAmt" => $NetOrderAmt,
				"GSTNO" => $GetItemDetails->gstin,
				"Cases" => $asn_qty_mt,
				"OrderType" => "Tax Order",
				"OrderStatus" => "O",
				"Transdate" => date('Y-m-d H:i:s'),
				"AccountID" => $AccountID,
				"UserID" => $this->session->userdata('username'),
				"cnfid" => 1,
			);
			if ($this->db->insert('tblordermaster', $Do_array)) {
				$this->increment_number($CenterID, 'DO');
				$do_history = array(
					"PlantID" => $selected_company,
					"FY" => $fy,
					"OrderID" => $DOID,
					"BillID" => $BookingID,
					"TransID" => $ASNID,
					"TransDate" => date('Y-m-d H:i:s'),
					"TransDate2" => date('Y-m-d H:i:s'),
					"TType" => "DO",
					"TType2" => "Delivery Order",
					"AccountID" => $AccountID,
					"ItemID" => $ItemID,
					"TypeID" => "SP",
					"CenterID" => $CenterID,
					"PartyID" => $PartyID,
					"PurchRate" => $basic_rate,
					"BasicRate" => $basic_rate,
					"final_rate" => $basic_rate,
					"SaleRate" => $SaleRate,
					"OrderQty" => $asn_qty_mt,
					"BilledQty" => $asn_qty_mt,
					"SuppliedIn" => "MT",
					"cgst" => $CGSTPer,
					"cgstamt" => $CGSTAmt,
					"sgst" => $SGSTPer,
					"sgstamt" => $SGSTAmt,
					"igst" => $IGSTPer,
					"igstamt" => $IGSTAmt,
					"Cases" => $asn_qty_mt,
					"OrderAmt" => $DoAmount,
					"ChallanAmt" => $DoAmount,
					"NetOrderAmt" => $NetOrderAmt,
					"NetChallanAmt" => $NetOrderAmt,
					"Ordinalno" => 1,
					"UserID" => $this->session->userdata('username'),
					"cnfid" => 1,
				);
				$this->db->insert('tblhistory', $do_history);
				echo json_encode(true);
			} else {
				echo json_encode(false);
			}
		} else {
			echo json_encode(false);
		}
	}
	public function GenerateGateInPass()
	{
		if (!has_permission_new('Ganerate_gatein', '', 'create')) {
			access_denied('Generate gatein');
		}
		$TType = $this->input->post('TType');
		$BookingID = $this->input->post('BID');
		$ASNID = $this->input->post('ASNID_hidden');
		$CenterID = $this->input->post('CID');
		$ItemID = $this->input->post('ItemID');
		$gateindate = $this->input->post('gateindate');
		if ($TType == "P") {
			$formattedDate = date("Y-m-d H:i:s");
		} else {
			$formattedDate = to_sql_date($this->input->post('gateindate')) . " " . date('H:i:s');
		}
		$VehicleImg = $_FILES['VehicleImg']['name'];
		$VehicleImg_tmp = $_FILES['VehicleImg']['tmp_name'];
		if (!is_dir('uploads/' . $BookingID . '/' . $ASNID)) {
			mkdir('uploads/' . $BookingID . "/" . $ASNID, 0777, TRUE);
			move_uploaded_file($VehicleImg_tmp, "uploads/" . $BookingID . "/" . $ASNID . "/GateInVehicleImage.png");
		} else {
			move_uploaded_file($VehicleImg_tmp, "uploads/" . $BookingID . "/" . $ASNID . "/GateInVehicleImage.png");
		}
		$DriverImg = $_FILES['DriverImg']['name'];
		$DriverImg_tmp = $_FILES['DriverImg']['tmp_name'];
		if (!is_dir('uploads/' . $BookingID . '/' . $ASNID)) {
			mkdir('uploads/' . $BookingID . "/" . $ASNID, 0777, TRUE);
			move_uploaded_file($DriverImg_tmp, "uploads/" . $BookingID . "/" . $ASNID . "/GateINDriverImage.png");
		} else {
			move_uploaded_file($DriverImg_tmp, "uploads/" . $BookingID . "/" . $ASNID . "/GateINDriverImage.png");
		}
		$new_Number = get_number($CenterID, 'GATE');
		$number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
		$GateINID = "G" . $CenterID . date('d') . date('m') . date('y') . $number;
		$this->increment_number($CenterID, 'GATE');
		// send to new erp gatein number
		$this->db->select('PartyID');
		$this->db->where('BookingID', $BookingID);
		$details = $this->db->get(db_prefix() . 'lead_master')->row();
		$httpData = [
			"COCD" => $details->PartyID,
			"TradeID" => $BookingID,
			"GateInID" => $GateINID,
			"VehicleNo" => $this->input->post('VehicleNo'),
			"DriverName" => "",
			"DriverMobileNo" => $this->input->post('Phone')
		];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/GateIn",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($httpData),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));
		$response = curl_exec($curl);
		$response_array = json_decode($response);
		if ($response_array->status) {
			$insert_referance = array(
				"Type" => $TType,
				"Name" => "GateIN",
				"GIC_Reference" => $GateINID,
				"pcsoft_doc_ref" => $response_array->data->GateInID
			);
			$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
		}
		curl_close($curl);
		/* Load QR Code Library */
		$this->load->library('ciqrcode');
		$save_name  = 'GateINQRImage.png';
		/* QR Code File Directory Initialize */
		$dir = 'uploads/' . $BookingID . '/' . $ASNID . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0775, true);
		}
		/* QR Configuration  */
		$config['cacheable']    = true;
		$config['imagedir']     = $dir;
		$config['quality']      = true;
		$config['size']         = '1024';
		$config['black']        = array(255, 255, 255);
		$config['white']        = array(255, 255, 255);
		$this->ciqrcode->initialize($config);
		/* QR Data  */
		$params['data']     = $GateINID . ',' . $BookingID . ',' . "2";
		$params['level']    = 'L';
		$params['size']     = 10;
		$params['savename'] = FCPATH . $config['imagedir'] . $save_name;
		$this->ciqrcode->generate($params);
		/* Return Data */
		$QR = array(
			'content' => $AsnID . ',' . $BookingID,
			'file'    => $dir . $save_name,
			'name'    => $save_name
		);
		$data = array(
			'VehicleNo' => $this->input->post('VehicleNo'),
			"Gate_in_ID" => $GateINID,
			"QR" => $QR['name'],
			'GodownID' => $this->input->post('GodownID'),
			'ChamberID' => $this->input->post('chamber'),
			'Phone' => $this->input->post('Phone'),
			'StackID' => $this->input->post('Stack'),
			'LOTID' => $this->input->post('LOTID'),
			'VehicleImg' => "V-" . $BookingID,
			'DriverImg' => "D-" . $BookingID,
			'VchlArrivalDateTime' => date('Y-m-d H:i:s'),
			'ArrivalDateTimeUserID' => $this->session->userdata('username'),
			'gate_in_date' => $formattedDate,
			'gate_in_by' => $this->session->userdata('username'),
		);
		$result = $this->GateControl_model->UpdateGateControl($data, $BookingID, $ASNID);
		if ($result) {
			$this->db->where('BookingID', $BookingID);
			$this->db->where('ASNID', $ASNID);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->set('status', 2);
			if ($this->db->update('tblGateMaster')) {
				$GetItemWiseQcParameter = $this->GateControl_model->GetItemWiseQCParameter($ItemID);
				foreach ($GetItemWiseQcParameter as $key => $value) {
					$ItemParameterID = $value["ItemParameterID"];
					$ParameterValue = $value["BaseValue"];
					$parameterArray = array(
						"BookingID" => $BookingID,
						"Gate_in_ID" => $GateINID,
						"TType" => "P",
						"ItemID" => $ItemID,
						"ItemParameterID" => $ItemParameterID,
						"ParameterValue" => $ParameterValue,
						"UserID" => $this->session->userdata('username'),
						"TransDate" => date('Y-m-d H:i:s')
					);
					$this->db->insert('tblQCParameterValues', $parameterArray);
				}
				if ($TType == "P") {
					$this->db->where('BookingID', $BookingID);
					$this->db->where('ASNID', $ASNID);
					$this->db->where('Gate_in_ID', $GateINID);
					$this->db->set('status', 3);
					$this->db->update('tblGateMaster');
				}
			}
			$flag = 2;
			$this->data['GetInPassDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateINID, $flag);
			$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
			$this->load->library('getin_pdf');
			$this->load->view('getin/getin_pdf', $this->data);
		} else {
			return false;
		}
	}
	public function viewGetInPass($BookingID, $GateIN)
	{
		$flag = 2;
		$this->data['GetInPassDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateIN, $flag);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		$this->load->library('getin_pdf');
		$this->load->view('getin/getin_pdf', $this->data);
	}
	public function GenerateChallan()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$BookingID = $this->input->post('BookingID');
		$GateINID = $this->input->post('GateINID');
		$BookingType = $this->input->post('BookingType');
		$SalesID = $this->input->post('SalesID');
		$StockWeightCheck = $this->input->post('StockWeightCheck');
		$ChallanAmt = $this->input->post('ChallanAmt');
		$VehicleID = $this->input->post('VehicleID');
		$DriverID = $this->input->post('DriverID');
		// Insert Challan Master Details
		$ChallanID = 'CHL' . $fy . get_option2('next_challan_number_for_kirti', $fy);
		$Challan_data = array(
			"PlantID" => $selected_company,
			"FY" => $fy,
			"ChallanID" => $ChallanID,
			"cnfid" => 1,
			"Transdate" => date('Y-m-d H:i:s'),
			"RouteID" => 1,
			"VehicleID" => $VehicleID,
			"DriverID" => $DriverID,
			"Cases" => $StockWeightCheck,
			"ChallanAmt" => $ChallanAmt,
			"UserID" => $this->session->userdata('username'),
		);
		if ($this->db->insert(db_prefix() . 'challanmaster', $Challan_data)) {
			// Increment Challan number
			$this->increment_next_number('next_challan_number_for_kirti', $fy);
			$this->db->where('OrderID', $GateINID);
			$this->db->where('SalesID', $SalesID);
			$this->db->update('tblsalesmaster', ["ChallanID" => $ChallanID]);
			// History Update
			$this->db->where('OrderID', $GateINID);
			$this->db->update('tblhistory', ["BillID" => $ChallanID, "TransID" => $SalesID]);
			$result = true;
			set_alert('success', "Challan Generated successfully");
		} else {
			$result = false;
			set_alert('warning', "Challan not Generated, please try again");
		}
		echo json_encode($result);
	}
	/*public function GenerateChallan()
			{
			$BookingID = $this->input->post('BookingID');
			$GateINID = $this->input->post('GateINID');
			$BookingType = $this->input->post('BookingType');
			$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID,$GateINID);
			$GetTCS = $this->GateControl_model->GetTCSDetails($BookingID,$GateINID);
			$GetCurrentRate = $this->GateControl_model->GetCurrentRate($GateControlDetails->CenterID,$GateControlDetails->ItemID);
			$tcsPerValue = $GetTCS[0]['tcs'];
			$PCSoftRef = $this->GateControl_model->GetPCSoftDoc($BookingID);
			$selected_company = $GateControlDetails->PlantID;
			$fy = $GateControlDetails->FY;
			$Netweight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
			$purch_amt = $Netweight * $GateControlDetails->basic_rate;
			$TaxRate = $GateControlDetails->taxrate;
			$TradeRate = $GateControlDetails->basic_rate;
			$CurrentRate = $GetCurrentRate->Rate;
			$PartyID = $GateControlDetails->PartyID;
			$ItemID = $GateControlDetails->ItemID;
			$DOWeightMT = $GateControlDetails->Cases;
			$Netweight_MT = $Netweight/10;
			$MinWtMT = $DOWeightMT - ($DOWeightMT * 0.02);
			$MaxWtMT = $DOWeightMT + ($DOWeightMT * 0.02);
			$ItemCount = 1;
			if($DOWeightMT >= $Netweight_MT){
		    $SaleAmt = $Netweight_MT * ($TradeRate * 10);
			}elseif($MaxWtMT >= $Netweight_MT){
		    $SaleAmt = $Netweight * ($TradeRate * 10);
			}else if($MaxWtMT < $Netweight_MT && $TradeRate < $CurrentRate){
	        $SaleAmt = $DOWeightMT * ($TradeRate * 10);
	        $SaleAmt1 = $SaleAmt; // use for DO Weight record store in history
	        $BeyondWtMT = $Netweight_MT - $DOWeightMT;
	        $SaleAmt += $BeyondWtMT * ($CurrentRate * 10);
	        $SaleAmt2 = $BeyondWtMT * ($CurrentRate * 10); // use for DO Weight beyond weight record store in history
	        $ItemCount++;
			}else{
		    $SaleAmt = $Netweight_MT * ($TradeRate * 10);
			}
			$GSTAmt = $SaleAmt * ($TaxRate / 100 );
			$GSTAmt1 = $SaleAmt1 * ($TaxRate / 100 ); // use for DO Weight record store in history
			$GSTAmt2 = $SaleAmt2 * ($TaxRate / 100 ); // use for DO Weight beyond weight record store in history
			$InvAmt = $SaleAmt + $GSTAmt;
			$TradeSaleRate = $TradeRate + ($TradeRate * ($TaxRate / 100));
			if($GateControlDetails->istcs == "1"){
		    $TcsAmt = $InvAmt * ($tcsPerValue / 100);
			}else{
		    $TcsAmt = 0;
			}
			if($PCSoftRef){
		    $pcsoft_doc_ref = $PCSoftRef->pcsoft_doc_ref;
		    // Send Sales Outward Data to PCSoft
		    $this->SendSalesOutwardDataToPcSoft($BookingID,$GateINID,$PartyID,$ItemID,$Netweight_MT,$pcsoft_doc_ref);
			}
			// Insert Challan Master Details
			$ChallanID = 'CHL' . $fy . get_option2('next_challan_number_for_kirti', $fy);
			$Challan_data = array(
		    "PlantID"=>$selected_company,
		    "FY"=>$fy,
		    "ChallanID"=>$ChallanID,
		    "cnfid"=>1,
		    "Transdate"=>date('Y-m-d H:i:s'),
		    "RouteID"=>1,
		    "VehicleID"=>$GateControlDetails->VehicleNo,
		    "DriverID"=>$GateControlDetails->Phone,
		    "Cases"=>$Netweight_MT,
		    "ChallanAmt"=>$InvAmt,
		    "UserID"=>$this->session->userdata('username'),
			);
			if($this->db->insert(db_prefix() . 'challanmaster', $Challan_data)){
		    // Increment Challan number
		    $this->increment_next_number('next_challan_number_for_kirti',$fy);
		    // Insert record in Sale Master
		    if($GateControlDetails->state == "27"){
			$CGSTAmt = $GSTAmt / 2;
			$SGSTAmt = $GSTAmt / 2;
			$IGSTAmt = 0;
			// use for DO Weight record store in history
			$CGSTAmt1 = $GSTAmt1 / 2;
			$SGSTAmt1 = $GSTAmt1 / 2;
			$IGSTAmt1 = 0;
			// use for DO Weight beyond weight record store in history
			$CGSTAmt2 = $GSTAmt2 / 2;
			$SGSTAmt2 = $GSTAmt2 / 2;
			$IGSTAmt2 = 0;
			$CGSTPer = $TaxRate / 2;
			$SGSTPer = $TaxRate / 2;
			$IGSTPer = 0;
		    }else{
			$IGSTAmt = $GSTAmt;
			$CGSTAmt = 0;
			$SGSTAmt = 0;
			// use for DO Weight record store in history
			$IGSTAmt1 = $GSTAmt1;
			$CGSTAmt1 = 0;
			$SGSTAmt1 = 0;
			// use for DO Weight beyond weight record store in history
			$IGSTAmt2 = $GSTAmt2;
			$CGSTAmt2 = 0;
			$SGSTAmt2 = 0;
			$CGSTPer = 0;
			$SGSTPer = 0;
			$IGSTPer = $TaxRate;
		    }
		    $SalesID = 'TAX' . $fy . get_option2('next_tax_number_for_kirti', $fy);
		    $sale_data = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"BT"=>"T",
			"SalesID"=>$SalesID,
			"Transdate"=>date('Y-m-d H:i:s'),
			"OrderID"=>$GateINID,
			"ChallanID"=>$ChallanID,
			"DOID"=>$GateControlDetails->DOID,
			"PartyID"=>$PartyID,
			"AccountID"=>$GateControlDetails->AccountID,
			"ShipTo"=>$fy,
			"CenterID"=>$GateControlDetails->CenterID,
			"WHID"=>$GateControlDetails->GodownID,
			"BrokerID"=>$GateControlDetails->BrokerID,
			"gstno"=>$GateControlDetails->vat,
			"sale_qty"=>$Netweight_MT,
			"SaleAmt"=>$SaleAmt,
			"sgstamt"=>$SGSTAmt,
			"cgstamt"=>$CGSTAmt,
			"igstamt"=>$IGSTAmt,
			"BillAmt"=>$InvAmt,
			"RndAmt"=>$InvAmt,
			"ItCount"=>1,
			"UserID"=>$this->session->userdata('username'),
			"tcs"=>$tcsPerValue,
			"tcsAmt"=>$TcsAmt
		    );
		    if($this->db->insert(db_prefix() . 'salesmaster', $sale_data)){
			// Increment Sale Invoice number
			$this->increment_next_number('next_tax_number_for_kirti',$fy);
			// Add Item data in History table
			$histry = 0;
			if($ItemCount > 1){
			$history_data = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"OrderID"=>$GateINID,
			"BillID"=>$ChallanID,
			"TransID"=>$SalesID,
			"TransDate"=>date('Y-m-d H:i:s'),
			"TransDate2"=>date('Y-m-d H:i:s'),
			"TType"=>"S",
			"TType2"=>"Sale",
			"AccountID"=>$GateControlDetails->AccountID,
			"ItemID"=>$ItemID,
			"TypeID"=>"SP",
			"CenterID"=>$GateControlDetails->CenterID,
			"GodownID"=>$GateControlDetails->GodownID,
			"PartyID"=>$PartyID,
			"PurchRate"=>$TradeRate,
			"SaleRate"=>$TradeSaleRate,
			"BasicRate"=>$TradeRate,
			"final_rate"=>$TradeRate * 10,
			"SuppliedIn"=>"MT",
			"OrderQty"=>$DOWeightMT,
			"BilledQty"=>$DOWeightMT,
			"cgst"=>$CGSTPer,
			"cgstamt"=>$CGSTAmt1,
			"sgst"=>$SGSTPer,
			"sgstamt"=>$SGSTAmt1,
			"igst"=>$IGSTPer,
			"igstamt"=>$IGSTAmt1,
			"CaseQty"=>1,
			"Cases"=>$DOWeightMT,
			"OrderAmt"=>$SaleAmt1,
			"ChallanAmt"=>$SaleAmt1,
			"NetOrderAmt"=>$SaleAmt1 + ($CGSTAmt1 + $SGSTAmt1 + $IGSTAmt1),
			"NetChallanAmt"=>$SaleAmt1 + ($CGSTAmt1 + $SGSTAmt1 + $IGSTAmt1),
			"Ordinalno"=>1,
			"UserID"=>$this->session->userdata('username'),
			"cnfid"=>1,
			);
			if($this->db->insert(db_prefix() . 'history', $history_data)){
			$histry++;
			}
			$history_data = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"OrderID"=>$GateINID,
			"BillID"=>$ChallanID,
			"TransID"=>$SalesID,
			"TransDate"=>date('Y-m-d H:i:s'),
			"TransDate2"=>date('Y-m-d H:i:s'),
			"TType"=>"S",
			"TType2"=>"Sale",
			"AccountID"=>$GateControlDetails->AccountID,
			"ItemID"=>$ItemID,
			"TypeID"=>"SP",
			"CenterID"=>$GateControlDetails->CenterID,
			"GodownID"=>$GateControlDetails->GodownID,
			"PartyID"=>$PartyID,
			"PurchRate"=>$CurrentRate,
			"SaleRate"=>$CurrentRate + ($CurrentRate * ($TaxRate / 100)),
			"BasicRate"=>$CurrentRate,
			"final_rate"=>$CurrentRate * 10,
			"SuppliedIn"=>"MT",
			"OrderQty"=>$BeyondWtMT,
			"BilledQty"=>$BeyondWtMT,
			"cgst"=>$CGSTPer,
			"cgstamt"=>$CGSTAmt2,
			"sgst"=>$SGSTPer,
			"sgstamt"=>$SGSTAmt2,
			"igst"=>$IGSTPer,
			"igstamt"=>$IGSTAmt2,
			"CaseQty"=>1,
			"Cases"=>$BeyondWtMT,
			"OrderAmt"=>$SaleAmt2,
			"ChallanAmt"=>$SaleAmt2,
			"NetOrderAmt"=>$SaleAmt2 + ($CGSTAmt2 + $SGSTAmt2 + $IGSTAmt2),
			"NetChallanAmt"=>$SaleAmt2 + ($CGSTAmt2 + $SGSTAmt2 + $IGSTAmt2),
			"Ordinalno"=>1,
			"UserID"=>$this->session->userdata('username'),
			"cnfid"=>2,
			);
			if($this->db->insert(db_prefix() . 'history', $history_data)){
			$histry++;
			}
			}else{
			$history_data = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"OrderID"=>$GateINID,
			"BillID"=>$ChallanID,
			"TransID"=>$SalesID,
			"TransDate"=>date('Y-m-d H:i:s'),
			"TransDate2"=>date('Y-m-d H:i:s'),
			"TType"=>"S",
			"TType2"=>"Sale",
			"AccountID"=>$GateControlDetails->AccountID,
			"ItemID"=>$ItemID,
			"TypeID"=>"SP",
			"CenterID"=>$GateControlDetails->CenterID,
			"GodownID"=>$GateControlDetails->GodownID,
			"PartyID"=>$PartyID,
			"PurchRate"=>$TradeRate,
			"SaleRate"=>$TradeSaleRate,
			"BasicRate"=>$TradeRate,
			"final_rate"=>$TradeRate * 10,
			"SuppliedIn"=>"MT",
			"OrderQty"=>$Netweight_MT,
			"BilledQty"=>$Netweight_MT,
			"cgst"=>$CGSTPer,
			"cgstamt"=>$CGSTAmt,
			"sgst"=>$SGSTPer,
			"sgstamt"=>$SGSTAmt,
			"igst"=>$IGSTPer,
			"igstamt"=>$IGSTAmt,
			"CaseQty"=>1,
			"Cases"=>$Netweight_MT,
			"OrderAmt"=>$SaleAmt,
			"ChallanAmt"=>$SaleAmt,
			"NetOrderAmt"=>$InvAmt,
			"NetChallanAmt"=>$InvAmt,
			"Ordinalno"=>1,
			"UserID"=>$this->session->userdata('username'),
			"cnfid"=>1,
			);
			if($this->db->insert(db_prefix() . 'history', $history_data)){
			$histry++;
			}
			}
			if($histry > 0){
			// Update Order Master for DO status
			$UpdateOrderMaster = array(
			"OrderID"=>$GateINID,
			"ChallanID"=>$ChallanID,
			"SalesID"=>$SalesID,
			);
			$this->db->where('DOID',$GateControlDetails->DOID);
			$this->db->where('ASNID',$GateControlDetails->ASNID);
			$this->db->update('tblordermaster',$UpdateOrderMaster);
			//Insert Ledger Entry
			$ord = 1;
			// Debit to party Account
			$Nerration = " Against BookingID ".$BookingID."/ GateInID ".$GateINID . " TransID  ".$SalesID;
			$drLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  $GateControlDetails->AccountID,
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'D',
			"Amount" =>  $InvAmt,
			"CounterAccount" =>  "SALE",
			"Narration" => $Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
			$ord++;
			// Credit to SALE Account
			$crLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>"SALE",
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $SaleAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$ord++;
			// GST Ledger Entry
			if($IGSTAmt > 0){
			// Credit IGST Accounts
			$crLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>"IGST",
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $IGSTAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$ord++;
			}else{
			// Credit CGST Ledger
			$crLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>"CGST",
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $CGSTAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$ord++;
			// Credit SGST Ledger
			$crLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>"SGST",
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $SGSTAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" => $Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$ord++;
			}
			// TCS Ledger Entry
			if($TcsAmt > 0){
			// Credit TCS Ledger
			$crLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>"TCS",
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $TcsAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$ord++;
			// Debit Party Ledger
			$drLedger = array(
			"PlantID" =>$selected_company,
			"FY" =>$fy,
			"PartyID" =>$PartyID,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>$GateControlDetails->AccountID,
			"CenterID" =>  $GateControlDetails->CenterID,
			"CommodityID" =>  $GateControlDetails->ItemID,
			"EntryFor" =>  3,
			"TType" =>  'D',
			"Amount" =>  $TcsAmt,
			"CounterAccount" => "TCS",
			"Narration" => $Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$ord,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
			}
			// Update Gate Master table
			$this->db->where('BookingID',$BookingID);
			$this->db->where('Gate_in_ID',$GateINID);
			$this->db->update('tblGateMaster',['status'=>8]);
			$result = true;
			set_alert('success', "Challan Generated successfully");
			}// History Table Insert
			$result = false;
			set_alert('warning', "Challan not Generated, please try again");
		    }// Sales Master Insert
		    $result = false;
		    set_alert('warning', "Challan not Generated, please try again");
			}else{
		    $result = false;
		    set_alert('warning', "Challan not Generated, please try again");
			}// Challan Master Insert
			echo json_encode($result);
		}*/
	// Send Sales Outward Data to PCSoft
	public function SendSalesOutwardDataToPcSoft($BookingID, $GateINID, $PartyID, $ItemID, $Netweight_MT, $pcsoft_doc_ref)
	{
		$outward_array = array(
			"cocd" => $PartyID,
			"doc_no" => $pcsoft_doc_ref,
			"im_code" => $ItemID,
			"net_wt" => $Netweight_MT // Net Weight in MT
		);
		$outward_data = json_encode($outward_array);
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/GICStore/GICUpdateQty4R", //  -> LIVE URL
				//CURLOPT_URL => "https://app.ieverp.com/TRIP/API/GICStore/GICUpdateQty4R", //--> DEV URL
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $outward_data,
				CURLOPT_HTTPHEADER => array(
					"content-type: application/json"
				),
			)
		);
		$response = curl_exec($curl);
		$response_array = json_decode($response);
		$Status = $response_array->Status;
		$einvoice = $response_array->einvoice;
		$ewb = $response_array->ewb;
		$pcSoftRes = array(
			"OutwardAPIDateTime"  => date('Y-m-d H:i:s'),
			"PcSoftStatus"  => $Status,
			"Iseinvoice"  => $einvoice,
			"Isewb"  => $ewb,
			"einvoice_link"  => $response_array->einvoice_link,
			"ewb_link"  => $response_array->ewb_link
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->update('tblGateMaster', $pcSoftRes);
		$err = curl_error($curl);
		curl_close($curl);
	}
	public function increment_next_number($name, $fy)
	{
		// Update next number in settings
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('name', $name);
		$this->db->WHERE('FY', $fy);
		$this->db->update(db_prefix() . 'options');
	}
	public function generateGateOut()
	{
		$BookingID = $this->input->post('BookingID');
		$GateINID = $this->input->post('GateINID');
		$BookingType = $this->input->post('BookingType');
		$updated = 0;
		$update_array = array(
			"gate_out_date" => date('Y-m-d H:i:s'),
			"gate_out_by" => $this->session->userdata('username')
		);
		if ($BookingType == 'S') {
			$update_array['status'] = 9;
		} elseif ($BookingType == 'W' || $BookingType == 'TW' || $BookingType == 'AW') {
			$update_array['status'] = 6;
		} else {
			$update_array['status'] = 11;
		}
		// echo "<pre>";print_r($update_array);die;
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		if ($this->db->update('tblGateMaster', $update_array)) {
			$updated++;
		}
		if ($updated > 0) {
			$result = true;
			set_alert('success', "Gate Out Generated successfully");
		} else {
			$result = false;
			set_alert('warning', "something went wrong ");
		}
		echo json_encode($result);
	}
	public function viewGateOut($BookingID, $GateINID)
	{
		$flag = 2;
		$this->data['GateOutDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateINID, $flag);
		$this->data['StackDetails'] = $this->GateControl_model->GetStackDetails($BookingID, $GateINID);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		$this->load->library('gateout_pdf');
		$this->load->view('gateout/gateout_pdf', $this->data);
	}
	public function getVehicleDetails()
	{
		$BookingID = $this->input->post('BookingID');
		$ASNID = $this->input->post('ASNID');
		$result = $this->GateControl_model->getVehicleDetailsDb($BookingID, $ASNID);
		echo json_encode($result);
	}
	public function GateControl_Reports()
	{
		if (!has_permission_new('GateControl_Reports', '', 'view')) {
			access_denied('GateControl Reports');
		}
		$data['StaffList'] = $this->GateControl_model->GetAllStaffList();
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['items'] = $this->GateControl_model->getItems();
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$data['village_detail'] = $this->GateControl_model->getVillageInfo();
		$data['title'] = "Gate Control Reports";
		$this->load->view('admin/gateControl/gateControl_Reports', $data);
	}
	public function AdvancePaymentList()
	{
		if (!has_permission_new('AdvancePayment_List', '', 'view')) {
			access_denied('GateControl Reports');
		}
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['PartyList'] =  $this->GateControl_model->getAllPartys();
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$this->load->view('admin/gateControl/AdvancePaymentList', $data);
	}
	public function PaymentDetails($GateINID = "")
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data['GateINDetails'] = $this->GateControl_model->GetControlDetailsByGateIN($GateINID);
		$data['PaidAmts'] = $this->GateControl_model->GetPaymentSum($GateINID);
		$data["historyDetails"] = $this->GateControl_model->FetchHistoryDetails($GateINID);
		$data["QCparameter"] = $this->GateControl_model->GetQCParameter();
		$data["QCList"] = $this->GateControl_model->GetQCDetailsByGateIN($GateINID);
		$data['title'] = "Payment Details";
		$this->load->view('admin/gateControl/Pur_PaymentDetails', $data);
	}
	public function PurchasePaymentList()
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_Denied('PurchasePaymentList');
		}
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['AllParty'] = $this->GateControl_model->GetCompanyList();
		$data['QCParameter'] = $this->GateControl_model->GetQCParameter();
		$data['OtherDeductionItems'] = $this->GateControl_model->GetOtherDeductionItems();
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$data['title'] = "Purchase Payment List";
		$this->load->view('admin/gateControl/PendingPaymentList', $data);
	}
	public function InvoiceList()
	{
		if (!has_permission_new('InvoiceList', '', 'view')) {
			access_Denied('InvoiceList');
		}
		$from_date = date('Y') . '-' . date('m') . '-01';
		$to_date = date('Y-m-d');
		$data['company_detail'] = $this->GateControl_model->getRootCompany();
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['AllInvoiceByCompany'] = $this->GateControl_model->GetAllInvoiceByCompany($from_date, $to_date);
		$data['AllInvoiceToParty'] = $this->GateControl_model->GetAllInvoiceToParty($from_date, $to_date);
		$data['title'] = "Invoice List";
		$this->load->view('admin/gateControl/InvoiceList', $data);
	}
	public function GetInvoiceList()
	{
		if (!has_permission_new('InvoiceList', '', 'view')) {
			access_denied('Invoice List');
		}
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'CenterID' => $this->input->post('CenterID'),
			'invoice_by' => $this->input->post('invoice_by'),
			'invoice_to' => $this->input->post('invoice_to'),
			'service_type' => $this->input->post('service_type'),
		);
		$result = $this->GateControl_model->GetInvoiceList($data);
		$CenterName = $result[0]["CenterName"];
		$InvoiceBy = $result[0]["PlantName"];
		$InvoiceTo = $result[0]["company"];
		$ServiceType = $result[0]["TransType"];
		$filter = "<b>From Date</b> " . $this->input->post('from_date') . " <b>To Date</b> " . $this->input->post('to_date');
		if ($this->input->post('CenterID') == "") {
			$filter .= " <b>Center</b> : All";
		} else {
			$filter .= " <b>Center</b> : " . $CenterName;
		}
		if ($this->input->post('invoice_by') == "") {
			$filter .= " <b>Invoice By</b> : All";
		} else {
			$filter .= " <b>Invoice By</b> : " . $InvoiceBy;
		}
		if ($this->input->post('invoice_to') == "") {
			$filter .= " <b>Invoice To</b> : All";
		} else {
			$filter .= " <b>Invoice To</b> : " . $InvoiceTo;
		}
		if ($this->input->post('service_type') == "") {
			$filter .= " <b>Service Type </b>: All";
		} else {
			$filter .= " <b>Service Type</b> : " . $ServiceType;
		}
		$html = '';
		$Total = 0;
		$i = 1;
		$html .= '<tr style="display:none;">';
		$html .= '<td style="text-align:center;" colspan="11"><input type="hidden" name="report_for" id="report_for" value="' . $filter . '"></td>';
		$html .= '</tr>';
		foreach ($result as $key => $value) {
			$url = "window.open('" . admin_url() . "GateControl/InvoiceDetails/" . $value["TransID"] . "', '_blank')";
			$html .= '<tr onclick="' . $url . '">';
			$html .= '<td style="text-align:center;">' . $i . '</td>';
			$html .= '<td style="text-align:center;">' . $value["TransID"] . '</td>';
			if ($value["TransType"] == "T") {
				$Servicename = "Trade Finance";
			} else if ($value["TransType"] == "W") {
				$Servicename = "Warehouse Deposit";
			} else if ($value["TransType"] == "P") {
				$Servicename = "Kirti Purchase";
			} else if ($value["TransType"] == "S") {
				$Servicename = "Kirti Sale";
			} else if ($value["TransType"] == "A") {
				$Servicename = "Anamat";
			} else {
				$Servicename = "";
			}
			$html .= '<td style="text-align:center;">' . $Servicename . '</td>';
			$html .= '<td style="text-align:center;">' . _d($value["TransDate"]) . '</td>';
			$html .= '<td style="text-align:center;">' . _d($value["InvFromDate"]) . '</td>';
			$html .= '<td style="text-align:center;">' . _d($value["InvToDate"]) . '</td>';
			$html .= '<td style="text-align:left;">' . $value["company"] . '</td>';
			$html .= '<td style="text-align:center;">' . $value["BookingID"] . '</td>';
			$html .= '<td style="text-align:left;">' . $value["CenterName"] . '</td>';
			$html .= '<td style="text-align:right">' . number_format($value["InvoiceAmt"], 2, '.', ',') . '</td>';
			$Total += $value["InvoiceAmt"];
			$html .= '<td style="text-align:center;">' . $value["IsPaid"] . '</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '<tr onclick="' . $url . '">';
		$html .= '<td colspan="9" style="text-align:right;font-weight:700;">Total</td>';
		$html .= '<td style="text-align:right;font-weight:700;">' . number_format($Total, 2, '.', ',') . '</td>';
		$html .= '<td></td>';
		$html .= '</tr>';
		echo $html;
	}
	// Get All Invoice By Company and  All Invoice To Party between to date
	public function GetAllInvoiceBy_To()
	{
		$from_date = to_sql_date($this->input->post('from_date'));
		$to_date = to_sql_date($this->input->post('to_date'));
		$AllInvoiceByCompany = $this->GateControl_model->GetAllInvoiceByCompany($from_date, $to_date);
		$AllInvoiceToParty = $this->GateControl_model->GetAllInvoiceToParty($from_date, $to_date);
		$data->AllInvoiceByCompany = $AllInvoiceByCompany;
		$data->AllInvoiceToParty = $AllInvoiceToParty;
		echo json_encode($data);
	}
	public function InvoiceDetails($TransID = "")
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data['InvoiceDetails'] = $this->GateControl_model->GetInvoiceDetails($TransID);
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$data['title'] = "Invoice Details";
		$this->load->view('admin/gateControl/InvoiceDetails', $data);
	}
	public function UpdateInvoiceStatus()
	{
		$Invoice_number = $this->input->post('Invoice_number');
		$result = $this->GateControl_model->UpdateInvoiceStatus($Invoice_number);
		if ($result == true) {
			set_alert('success', "Payment received successfully");
			echo json_encode(true);
		} else {
			set_alert('danger', "Payment not received");
			echo json_encode(false);
		}
	}
	public function OutStandingCalculation()
	{
		if (!has_permission_new('outstandingcalculation', '', 'view')) {
			access_Denied('outstandingcalculation');
		}
		$from_date = date('Y') . '-' . date('m') . '-01';
		$to_date = date('Y-m-d');
		$data['company_detail'] = $this->GateControl_model->getRootCompany();
		$data['centers'] = $this->GateControl_model->getAllCenters();
		//$data['AllInvoiceByCompany'] = $this->GateControl_model->GetAllInvoiceByCompany($from_date,$to_date);
		$data['AllStockToParty'] = $this->GateControl_model->GetAllStockToParty($from_date, $to_date);
		/*echo "<pre>";
				print_r($data['AllStockToParty']);
			die;*/
		$data['title'] = "Outstanding List";
		$this->load->view('admin/gateControl/OutstandingList', $data);
	}
	public function GetOutStandingList()
	{
		if (!has_permission_new('outstandingcalculation', '', 'view')) {
			access_denied('Invoice List');
		}
		$data = array(
			'CenterID' => $this->input->post('CenterID'),
			'outstanding_to' => $this->input->post('outstanding_to'),
			'service_type' => $this->input->post('service_type'),
		);
		$result = $this->GateControl_model->GetOutstandingList($data);
		$service_type = $this->input->post('service_type');
		$CenterName = $result[0]["CenterName"];
		$InvoiceTo = $result[0]["company"];
		if ($service_type == "T") {
			$GateFinanceInwardList = $this->GateControl_model->GetFinanceInwardList($data);
			$Servicename = "Trade Finance";
		} else if ($service_type == "D") {
			$GateMaster = $this->GateControl_model->GetMasterList($data);
			$Servicename = "Warehouse Deposit";
		} else if ($service_type == "A") {
			$GateFinanceInwardList = $this->GateControl_model->GetFinanceInwardList($data);
			$Servicename = "Anamat";
		}
		$filter = "";
		if ($this->input->post('CenterID') == "") {
			$filter .= " <b>Center</b> : All ";
		} else {
			$filter .= " <b>Center</b> : " . $CenterName . " ";
		}
		if ($this->input->post('outstanding_to') == "") {
			$filter .= " <b>OutStanding To</b> : All ";
		} else {
			$filter .= " <b>OutStanding To</b> : " . $InvoiceTo . " ";
		}
		if ($service_type == "") {
			$filter .= " <b>Service Type </b>: All ";
		} else {
			$filter .= " <b>Service Type</b> : " . $Servicename . " ";
		}
		$html = '';
		$html .= '<thead>';
		$html .= '<tr style="display:none;">';
		$html .= '<td style="text-align:center;" colspan="11"><input type="hidden" name="report_for" id="report_for" value="' . $filter . '"></td>';
		$html .= '</tr>';
		$i = 1;
		$html .= '<tr>';
		$html .= '<th style="text-align:center;">Sr. No.</th>';
		$html .= '<th style="text-align:center;">Party Name</th>';
		$html .= '<th style="text-align:center;">Booking ID</th>';
		$html .= '<th style="text-align:center;">Date of Deposit</th>';
		$html .= '<th style="text-align:center;">Commodity</th>';
		$html .= '<th style="text-align:center;">Weight(MT)</th>';
		$html .= '<th style="text-align:center;">Mkt Rate(MT)</th>';
		$html .= '<th style="text-align:center;">WR No.</th>';
		$html .= '<th style="text-align:center;">WR Weight(MT)</th>';
		$html .= '<th style="text-align:center;">WR Value</th>';
		if ($service_type == "A" || $service_type == "T") {
			$html .= '<th style="text-align:center;">Disbursement %</th>';
			$html .= '<th style="text-align:center;">Amount of Disbursement</th>';
			$html .= '<th style="text-align:center;">Date of Disbursement</th>';
			$html .= '<th style="text-align:center;">Rate of Interest</th>';
			$html .= '<th style="text-align:center;">No of Days post Disb</th>';
			$html .= '<th style="text-align:center;">Interest Amount</th>';
		}
		$html .= '<th style="text-align:center;">Storage Charge/MT/Month</th>';
		$html .= '<th style="text-align:center;">Total Storage Charges</th>';
		$html .= '<th style="text-align:center;">Material Handling Cost</th>';
		$html .= '<th style="text-align:center;">Current Mkt Rate/MT</th>';
		$html .= '<th style="text-align:center;">Current Mkt value</th>';
		$html .= '<th style="text-align:center;">Total O/S</th>';
		$html .= '<th style="text-align:center;">O/S Amt Vs Current Mkt Value</th>';
		$html .= '<th style="text-align:center;">Action</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody id="filter_data_table">';
		$CurrentDate = date('Y-m-d');
		foreach ($result as $key => $value) {
			$WR_no = '';
			$Total_os = 0;
			$Booking_rate_mt = $value["basic_rate"] * 10;
			$net_weight = 0;
			$WR_value = 0;
			$Booking_date = substr($value["TransDate"], 0, 10);
			$Total_loan_amt = 0;
			$total_int = 0;
			if ($value['CycleDays']) {
				$PaymentCycleDays = $value['CycleDays'];
			} else {
				$PaymentCycleDays = 7;
			}
			if ($value['TType'] == "D") {
				foreach ($GateMaster as $gkey => $gval) {
					if ($value["BookingID"] == $gval["BookingID"]) {
						$loaded_weight = $gval["LoadedWeight"];
						$tare_weight = $gval["TareWeight"];
						$weight = ($loaded_weight - $tare_weight) / 10;
						$net_weight += $weight;
						$WR_no .= $gval["Gate_in_ID"] . " ";
					}
				}
				$WR_value = $net_weight * $Booking_rate_mt;
			} else {
				$one_day_interest = 0;
				$net_weight = $value["WRWeight"];
				$WR_value = $value["WRValue"];
				$Total_loan_amt = $value["Amount"];
				$ROC = $value["ROC"];
				$loan_per = $value["loan_per"];
				$loan_date = _d(substr($value["dis_date"], 0, 10));
				// calculate interest amount from date of disbrusment for Ananmat and Trade Finance
				$dis_date = substr($value["dis_date"], 0, 10);
				$date11 = new DateTime($CurrentDate);
				$date22 = new DateTime($dis_date);
				$dis_days  = $date22->diff($date11)->format('%a') + 1;
				$one_day_interest = ($Total_loan_amt * ($ROC / 100)) / 365;
				$total_int = $one_day_interest * ($dis_days);
				foreach ($GateFinanceInwardList as $Fkey => $Fval) {
					if ($Fval['BookingID'] == $value["BookingID"]) {
						$WR_no .= $Fval["Gate_in_ID"] . " ";
					}
				}
			}
			// calculate days and storage charges from booking date comman for all services
			$date1 = new DateTime($CurrentDate);
			$date2 = new DateTime($Booking_date);
			$days  = $date2->diff($date1)->format('%a') + 1;
			$totalCycleApply = $days / $PaymentCycleDays;
			$invoice_payment_cycle = ceil($totalCycleApply);
			$per_dya_chr_mt = $value["StorageCharge"] / 30;
			$OneDayCharg = $value["quantity"] * $per_dya_chr_mt;
			$invoice_days = $invoice_payment_cycle * $PaymentCycleDays;
			$totalChr = $OneDayCharg * $invoice_days;
			$HandChr = 150 * $value["quantity"];
			// Calculate quality deduction as provide by kailash sir
			if ($value['TType'] == "T") {
				$net_weight = 0;
				foreach ($GateFinanceInwardList as $Fkey => $Fval) {
					if ($Fval['BookingID'] == $value["BookingID"]) {
						$Inward_date = substr($Fval["gate_in_date"], 0, 10);
						$loaded_weight = $Fval["LoadedWeight"];
						$tare_weight = $Fval["TareWeight"];
						$weight = ($loaded_weight - $tare_weight) / 10;
						$date111 = new DateTime($CurrentDate);
						$date222 = new DateTime($Inward_date);
						$days111  = $date2->diff($date1)->format('%a') + 1;
						if ($days111 <= 30) {
							// MOISTURE weight deduction for first month is 1%
							$weight = $weight - ($weight * 0.01);
						}
						if ($days111 <= 60 && $days111 > 30) {
							// MOISTURE weight deduction for first month is 0.25%
							$weight = $weight - ($weight * 0.0025);
						}
						if ($days111 <= 90 && $days111 > 60) {
							// MOISTURE weight deduction for first month is 0.25%
							$weight = $weight - ($weight * 0.0025);
						}
						// FM weight deduction  is 1%
						$weight = $weight - ($weight * 0.01);
						// DAMAGE weight deduction  is 1%
						$weight = $weight - ($weight * 0.01);
						$net_weight += $weight;
					}
				}
			}
			$cur_rate_mt = $value["Current_rate"] * 10;
			$Current_val = $net_weight * $cur_rate_mt;
			$Total_os = $totalChr + $HandChr + $Total_loan_amt + $total_int;
			if ($Current_val <= 0) {
				$os_per = 100;
			} else {
				$os_per = ($Total_os / $Current_val) * 100;
			}
			if ($os_per >= 90) {
				$status = "Close";
			} else {
				$status = "Continue";
			}
			//$url = "window.open('".admin_url()."GateControl/InvoiceDetails/".$value["TransID"]."', '_blank')";
			//$html .= '<tr onclick="'.$url.'">';
			$html .= '<tr >';
			$html .= '<td style="text-align:center;">' . $i . '</td>';
			$html .= '<td style="text-align:left;">' . $value["company"] . '</td>';
			$html .= '<td style="text-align:center;">' . $value["BookingID"] . '</td>';
			$html .= '<td style="text-align:center;">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';
			$html .= '<td style="text-align:left;">' . $value["ItemName"] . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($value["quantity"], 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($Booking_rate_mt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:left;">' . $WR_no . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($net_weight, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($WR_value, 2, '.', '') . '</td>';
			if ($service_type == "A" || $service_type == "T") {
				$html .= '<td style="text-align:right;">' . number_format($loan_per, 2, '.', '') . ' %</td>';
				$html .= '<td style="text-align:right;">' . number_format($Total_loan_amt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:center;">' . $loan_date . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($ROC, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:center;">' . number_format($dis_days, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:center;">' . number_format($total_int, 2, '.', '') . '</td>';
			}
			$html .= '<td style="text-align:right;">' . number_format($value["StorageCharge"], 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($totalChr, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($HandChr, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($cur_rate_mt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($Current_val, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($Total_os, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($os_per, 2, '.', '') . '%</td>';
			$html .= '<td style="text-align:center;">' . $status . '</td>';
			$i++;
		}
		$html .= '</body>';
		echo $html;
	}
	public function ChargesCalculation()
	{
		if (!has_permission_new('outstandingcalculation', '', 'view')) {
			access_Denied('outstandingcalculation');
		}
		$from_date = date('Y') . '-' . date('m') . '-01';
		$to_date = date('Y-m-d');
		$data['company_detail'] = $this->GateControl_model->getRootCompany();
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['AllStockToParty'] = $this->GateControl_model->GetAllStockToParty($from_date, $to_date);
		$data['title'] = "Charges Calculation";
		$this->load->view('admin/gateControl/ChargesCalculation', $data);
	}
	public function GetChargesList()
	{
		if (!has_permission_new('outstandingcalculation', '', 'view')) {
			access_denied('Invoice List');
		}
		$data = array(
			'CenterID' => $this->input->post('CenterID'),
			'outstanding_to' => $this->input->post('outstanding_to'),
			'service_type' => $this->input->post('service_type'),
		);
		$GetChargesList = $this->GateControl_model->GetChargesList($data);
		$AllStockToParty = $this->GateControl_model->GetAllStockToParty();
		$service_type = $this->input->post('service_type');
		$CenterName = $result[0]["CenterName"];
		$InvoiceTo = $result[0]["company"];
		if ($service_type == "T") {
			$GateFinanceInwardList = $this->GateControl_model->GetFinanceInwardList($data);
			$Servicename = "Trade Finance";
		} else if ($service_type == "D") {
			$GateMaster = $this->GateControl_model->GetMasterList($data);
			$Servicename = "Warehouse Deposit";
		} else if ($service_type == "A") {
			$GateFinanceInwardList = $this->GateControl_model->GetFinanceInwardList($data);
			$Servicename = "Anamat";
		}
		$filter = "";
		if ($this->input->post('CenterID') == "") {
			$filter .= " <b>Center</b> : All ";
		} else {
			$filter .= " <b>Center</b> : " . $CenterName . " ";
		}
		if ($this->input->post('outstanding_to') == "") {
			$filter .= " <b>OutStanding To</b> : All ";
		} else {
			$filter .= " <b>OutStanding To</b> : " . $InvoiceTo . " ";
		}
		if ($service_type == "") {
			$filter .= " <b>Service Type </b>: All ";
		} else {
			$filter .= " <b>Service Type</b> : " . $Servicename . " ";
		}
		$html = '';
		$html .= '<thead>';
		$html .= '<tr style="display:none;">';
		$html .= '<td style="text-align:center;" colspan="16"><input type="hidden" name="report_for" id="report_for" value="' . $filter . '"></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th style="text-align:center;">Sr. No.</th>';
		$html .= '<th style="text-align:center;">State</th>';
		$html .= '<th style="text-align:center;">WH location</th>';
		$html .= '<th style="text-align:center;">WH Name</th>';
		$html .= '<th style="text-align:center;">Bill month</th>';
		$html .= '<th style="text-align:center;">From</th>';
		$html .= '<th style="text-align:center;">To</th>';
		$html .= '<th style="text-align:center;">Commodity</th>';
		$html .= '<th style="text-align:center;">Booked Quantity</th>';
		$html .= '<th style="text-align:center;">Lock-in period starts</th>';
		$html .= '<th style="text-align:center;">Lock-in period ends</th>';
		$html .= '<th style="text-align:center;">Actual Quantity</th>';
		$html .= '<th style="text-align:center;">Billing on</th>';
		$html .= '<th style="text-align:center;">Rate</th>';
		$html .= '<th style="text-align:center;">Days/Week</th>';
		$html .= '<th style="text-align:center;">Rent Amt</th>';
		$html .= '</tr>';
		$CurrentDate = date('Y-m-d');
		$day = date('d');
		$MonthName = date('F', strtotime($CurrentDate));
		$firstDateofMonth = date('Y') . '-' . date('m') . '-01';
		foreach ($AllStockToParty as $PKey => $pVal) {
			$html .= '<tr>';
			$html .= '<td colspan="16" style="font-size:14px;"><b>' . $pVal["company"] . '</b></td>';
			$html .= '</tr>';
			$i = 1;
			$TotalBookedQty = 0;
			$TotalWR = 0;
			foreach ($GetChargesList as $ChrKey => $chrVal) {
				$WR_no = '';
				$WR_value = 0;
				$Booking_date = substr($chrVal["TransDate"], 0, 10);
				if (strtotime($Booking_date) < strtotime($firstDateofMonth)) {
					$status = "before current month";
					$billingStartDate = $firstDateofMonth;
				} else {
					$status = "Current month";
					$billingStartDate = $Booking_date;
				}
				$Total_loan_amt = 0;
				$total_int = 0;
				if ($chrVal['CycleDays']) {
					$PaymentCycleDays = $chrVal['CycleDays'];
				} else {
					$PaymentCycleDays = 7;
				}
				// calculate days and storage charges from booking date comman for all services
				$date1 = new DateTime($CurrentDate);
				$date2 = new DateTime($billingStartDate);
				$days  = $date2->diff($date1)->format('%a') + 1;
				$totalCycleApply = $days / $PaymentCycleDays;
				$invoice_payment_cycle = ceil($totalCycleApply);
				$per_dya_chr_mt = $chrVal["StorageCharge"] / 30;
				$OneDayCharg = $chrVal["quantity"] * $per_dya_chr_mt;
				$invoice_days = $invoice_payment_cycle * $PaymentCycleDays;
				$totalChr = $OneDayCharg * $invoice_days;
				$TradeWiseWRWeight = 0;
				if ($pVal["AccountID"] == $chrVal["AccountID"]) {
					if ($chrVal['TType'] == "D") {
						foreach ($GateMaster as $gkey => $gval) {
							$loaded_weight = 0;
							$tare_weight = 0;
							if ($chrVal["BookingID"] == $gval["BookingID"]) {
								$loaded_weight = $gval["LoadedWeight"];
								$tare_weight = $gval["TareWeight"];
								$weight_in_mt = ($loaded_weight - $tare_weight) / 10;
								$TradeWiseWRWeight += $weight_in_mt;
								$WR_no .= $gval["Gate_in_ID"] . " ";
							}
						}
						$TotalWR += $TradeWiseWRWeight;
						$WR_value = $weight_in_mt * $Booking_rate_mt;
					}
					$html .= '<tr>';
					$html .= '<td>' . $i . '</td>';
					$html .= '<td>' . $chrVal["CentState"] . '</td>';
					$html .= '<td>' . $chrVal["WhAddrs"] . '</td>';
					$html .= '<td>' . $chrVal["CenterName"] . '</td>';
					$html .= '<td>' . $MonthName . "-" . date('Y') . '</td>';
					$BillingStartDate = substr($billingStartDate, 8, 2) . '-' . date('F', strtotime($billingStartDate)) . "-" . substr($billingStartDate, 0, 4);
					$html .= '<td>' . $BillingStartDate . '</td>';
					$html .= '<td>' . $day . "-" . $MonthName . '</td>';
					$html .= '<td>' . $chrVal["ItemName"] . '</td>';
					$html .= '<td style="text-align:right;">' . $chrVal["quantity"] . '</td>';
					$locStartDate = substr($chrVal["TransDate"], 0, 10);
					$lockStartDate = substr($locStartDate, 8, 2) . '-' . date('F', strtotime($locStartDate)) . "-" . substr($locStartDate, 0, 4);
					$html .= '<td>' . $lockStartDate . '</td>';
					$EndDate = date("Y-m-d", strtotime($locStartDate . " +" . $chrVal["LockDays"] . " day"));
					$lockEndDate = substr($EndDate, 8, 2) . '-' . date('F', strtotime($EndDate)) . "-" . substr($EndDate, 0, 4);
					$html .= '<td>' . $lockEndDate . '</td>';
					$html .= '<td style="text-align:right;">' . number_format($TradeWiseWRWeight, 2, '.', '') . '</td>';
					$html .= '<td style="text-align:right;">' . number_format($chrVal["quantity"], 2, '.', '') . '</td>';
					$html .= '<td style="text-align:right;">' . number_format($chrVal["StorageCharge"], 2, '.', '') . '</td>';
					$html .= '<td style="text-align:right;">' . number_format($invoice_days, 2, '.', '') . '</td>';
					$html .= '<td style="text-align:right;">' . number_format($totalChr, 2, '.', '') . '</td>';
					$html .= '</tr>';
					$i++;
					$TotalBookedQty += $chrVal["quantity"];
				}
			}
			$html .= '<tr>';
			$html .= '<td colspan="8" style="font-size:14px;"><b>Total</b></td>';
			$html .= '<td style="font-size:14px;text-align:right;">' . number_format($TotalBookedQty, 2, '.', '') . '</td>';
			$html .= '<td colspan="2" style="font-size:14px;"><b></b></td>';
			$html .= '<td style="font-size:14px;text-align:right;">' . number_format($TotalWR, 2, '.', '') . '</td>';
			$html .= '<td style="font-size:14px;text-align:right;">' . number_format($TotalBookedQty, 2, '.', '') . '</td>';
			$html .= '<td colspan="2" style="font-size:14px;"></td>';
			$html .= '<td style="font-size:14px;"><b></b></td>';
			$html .= '</tr>';
		}
		$html .= '</body>';
		echo $html;
	}
	public function SettledList()
	{
		if (!has_permission_new('SettledList', '', 'view')) {
			access_Denied('Settled List');
		}
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['AllParty'] = $this->GateControl_model->GetCompanyList();
		$data['title'] = "Booking Settled List";
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$this->load->view('admin/booking_settlement/SettlementDone', $data);
	}
	public function CenterWiseTradeQuantity()
	{
		if (!has_permission_new('CenterWiseTradeQty', '', 'view')) {
			access_Denied('Center Wise Trade Quantity');
		}
		$data['centers'] = $this->GateControl_model->getAllCenters();
		$data['items'] = $this->GateControl_model->getItems();
		$data['title'] = "Center Wise Trade Quantity";
		$data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$this->load->view('admin/booking_settlement/CenterWiseTradeQuantity', $data);
	}
	public function GetCenterWiseTradeQuantity()
	{
		if (!has_permission_new('CenterWiseTradeQty', '', 'view')) {
			access_denied('Center Wise Trade Quantity');
		}
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'CenterID' => $this->input->post('CenterID'),
			'ItemID' => $this->input->post('ItemID'),
			'TradeType' => $this->input->post('TradeType'),
			'TradeStatus' => $this->input->post('TradeStatus')
		);
		$result = $this->GateControl_model->GetCenterWiseTradeQuantity($data);
		$html = '';
		$srNo = 1;
		$grandTradeQty = 0;
		$grandInwardQty = 0;
		foreach ($result as $value) {
			$grandTradeQty += $value["TotalTradeQty"];
			$grandInwardQty += $value["TotalInwardQty"];
			$html .= '<tr>';
			$html .= '<td style="text-align:center">' . $srNo . '</td>';
			$html .= '<td>' . $value["CenterName"] . '</td>';
			$html .= '<td style="text-align:center">' . number_format($value["TotalTradeQty"], 3, '.', '') . '</td>';
			$html .= '<td style="text-align:center">' . number_format($value["TotalInwardQty"], 3, '.', '') . '</td>';
			$html .= '</tr>';
			$srNo++;
		}
		if ($html != '') {
			$html .= '<tr style="font-weight:bold;background-color:#e8e8e8;color:#42a5f5;">';
			$html .= '<td style="text-align:center;color:#42a5f5;"></td>';
			$html .= '<td style="color:#42a5f5;">Grand Total</td>';
			$html .= '<td style="text-align:center;color:#42a5f5;">' . number_format($grandTradeQty, 3, '.', '') . '</td>';
			$html .= '<td style="text-align:center;color:#42a5f5;">' . number_format($grandInwardQty, 3, '.', '') . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function GetBookingListDetails()
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'CenterID' => $this->input->post('CenterID'),
			'purchase_for' => $this->input->post('purchase_for'),
			'TradeStatus' => $this->input->post('TradeStatus')
		);
		$result = $this->GateControl_model->GetBookingListDetails($data);
		$InwardDetails = $this->GateControl_model->GetBookingListInwardDetails($data);
		$html = '';
		$grandQuantity = 0;
		$grandInwardWeight = 0;
		$grandShortageAmt = 0;
		$grandNonDeliverdAmt = 0;
		foreach ($result as $key => $value) {
			$InwardWeight = 0;
			foreach ($InwardDetails as $inkey => $inval) {
				if ($value["BookingID"] == $inval["BookingID"]) {
					$InwardWeight = $inval["InwardQty"];
				}
			}
			$grandQuantity += $value["quantity"];
			$grandInwardWeight += $InwardWeight;
			$grandShortageAmt += $value["ShortageAmt"];
			$grandNonDeliverdAmt += $value["NonDeliverdAmt"];
			$html .= '<tr>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td style="text-align:center" title="' . _d($value["TransDate"]) . '">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';
			$html .= '<td style="text-align:center" title="' . _d($value["SettlementDate"]) . '">' . _d(substr($value["SettlementDate"], 0, 10)) . '</td>';
			$date = strtotime(substr($value["TransDate"], 0, 10));
			if ($value["CustomerType"] == "1") {
				$Duedate = _d(substr($value["TransDate"], 0, 10));
			} else {
				$Duedate = strtotime("+7 day", $date);
				$Duedate = date('d/m/Y', $Duedate);
			}
			$html .= '<td style="text-align:center">' . $Duedate . '</td>';
			$html .= '<td title="' . $value["PlantName"] . '">' . $value["PartyID"] . '</td>';
			$html .= '<td>' . $value["CenterName"] . '</td>';
			$html .= '<td>' . $value["company"] . '</td>';
			$html .= '<td>' . $value["BrokerName"] . '</td>';
			$html .= '<td>' . $value["ItemName"] . '</td>';
			$html .= '<td style="text-align:right">' . $value["basic_rate"] . '</td>';
			$html .= '<td style="text-align:right">' . $value["today_rate"] . '</td>';
			$html .= '<td style="text-align:right">' . $value["quantity"] . '</td>';
			$html .= '<td style="text-align:right">' . ($InwardWeight) . '</td>';
			$InwardPer = ($value["quantity"] > 0) ? ($InwardWeight / ($value["quantity"])) * 100 : 0;
			$html .= '<td style="text-align:right">' . number_format($InwardPer, 2, '.', '') . '%</td>';
			$html .= '<td style="text-align:center">' . $value["ShortageAmt"] . '</td>';
			$html .= '<td style="text-align:center">' . $value["NonDeliverdAmt"] . '</td>';
			//$html .= '<td><a class="btn btn-default mright5" data-toggle="tooltip" data-title="click to open invoice" target="_blank" href="'. admin_url().'GateControl/PrintCommissionInvoice/'.$value["BookingID"].'"><i class="fa fa-eye"></i></a></td>';
			if ($value["inw_Weight"] > 0) {
				$html .= '<td><a data-toggle="tooltip" data-title="click to open invoice" target="_blank" href="' . admin_url() . 'GateControl/PrintCommissionInvoice/' . $value["BookingID"] . '"><i class="fa fa-eye" style="font-size:16px;"></i></a></td>';
			} else {
				$html .= '<td></td>';
			}
			$html .= '</tr>';
		}
		if ($html != '') {
			$grandInwardPer = ($grandQuantity > 0) ? ($grandInwardWeight / $grandQuantity) * 100 : 0;
			$html .= '<tr style="font-weight:bold;background-color:#e8e8e8;color:#42a5f5;">';
			$html .= '<td colspan="11" style="color:#42a5f5;">Grand Total</td>';
			$html .= '<td style="text-align:right;color:#42a5f5;">' . number_format($grandQuantity, 3, '.', '') . '</td>';
			$html .= '<td style="text-align:right;color:#42a5f5;">' . number_format($grandInwardWeight, 3, '.', '') . '</td>';
			$html .= '<td style="text-align:right;color:#42a5f5;">' . number_format($grandInwardPer, 2, '.', '') . '%</td>';
			$html .= '<td style="text-align:center;color:#42a5f5;">' . number_format($grandShortageAmt, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:center;color:#42a5f5;">' . number_format($grandNonDeliverdAmt, 2, '.', '') . '</td>';
			$html .= '<td></td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function PrintCommissionInvoice($BookingID)
	{
		$data['RootCompany'] = $this->GateControl_model->LoginCompany();
		$data['BookingDetails'] = $this->GateControl_model->GetSingleBookingDataDB($BookingID);
		$data['CompDetails'] = $this->GateControl_model->CompanyDetails($data['BookingDetails']->PartyID);
		$data['InvoiceDetails'] = $this->GateControl_model->GetInvoiceDetails($BookingID);
		/*echo "<pre>";
				print_r($data->RootCompany);
				print_r($data);
			die;*/
		if (!$BookingID) {
			redirect(admin_url('GateControl/BookingSettlement'));
		}
		if (!has_permission_new('challan_list', '', 'view')) {
			access_denied('Invoices');
		}
		$data        = hooks()->apply_filters('before_admin_view_invoice_pdf', $data);
		try {
			$pdf = Commission_pdf($data);
		} catch (Exception $e) {
			$message = $e->getMessage();
			echo $message;
			if (strpos($message, 'Unable to get the size of the image') !== false) {
				show_pdf_unable_to_get_image_size_error();
			}
			die;
		}
		$type = 'I';
		$pdf->Output(mb_strtoupper(slug_it($BookingID)) . '-Commission.pdf', $type);
	}
	public function GetPendingPaymentList()
	{
		if (!has_permission_new('PurchasePaymentList', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'CenterID' => $this->input->post('CenterID'),
			'purchase_for' => $this->input->post('purchase_for')
		);
		$result = $this->GateControl_model->GetPendingPaymentList($data);
		$QCparameter = $this->GateControl_model->GetQCParameter();
		$resultFarmer = $this->GateControl_model->GetPendingPaymentListForFarmer($data);
		/*echo "<pre>";
				print_r($resultFarmer);
			die;*/
		$mergedArrays = array_merge($result, $resultFarmer);
		usort($mergedArrays, function ($a, $b) {
			return strcmp($b['Gate_in_ID'], $a['Gate_in_ID']);
		});
		$result = $mergedArrays;
		$All_gate_in = array();
		foreach ($result as $key => $value) {
			array_push($All_gate_in, $value["Gate_in_ID"]);
		}
		if ($result) {
			$PaymentList = $this->GateControl_model->GetPaymentListByGateIN($All_gate_in);
			$QCList = $this->GateControl_model->GetQCDetailsByGateIN($All_gate_in);
			$MaxLayerNumber = $this->GateControl_model->GetMaxQCLayer($All_gate_in);
			$MaxQCLayer = $MaxLayerNumber->MaxLayer;
			$OtherDeductionItemList = $this->GateControl_model->GetOtherDeductionItems();
			$OtherDeductionItems = $this->GateControl_model->GetOtherDeductionGateINWise($All_gate_in);
			$BagDetails = $this->GateControl_model->GetBagQtyGateInWise($All_gate_in);
		}
		$html = '';
		/*echo $MaxQCLayer;
			die;*/
		// Design Header
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="text-align:left;">Sr.No.</th>';
		$html .= '<th style="text-align:left;">Trade ID</th>';
		$html .= '<th style="text-align:left;">Trade Date</th>';
		$html .= '<th style="text-align:left;">Vehicle Arrival DateTime</th>';
		$html .= '<th style="text-align:left;">GateIN ID</th>';
		$html .= '<th style="text-align:left;">Inward Date</th>';
		$html .= '<th style="text-align:left;">Purchase For</th>';
		$html .= '<th style="text-align:left;">Party Mobile</th>';
		$html .= '<th style="text-align:left;">Party Name</th>';
		$html .= '<th style="text-align:left;">Party Type</th>';
		$html .= '<th style="text-align:left;">PAN/Aadhaar</th>';
		$html .= '<th style="text-align:left;">State</th>';
		$html .= '<th style="text-align:left;">City</th>';
		$html .= '<th style="text-align:left;">Taluka</th>';
		$html .= '<th style="text-align:left;">Post</th>';
		$html .= '<th style="text-align:left;">Town</th>';
		$html .= '<th style="text-align:left;">Locality</th>';
		$html .= '<th style="text-align:left;">Street</th>';
		$html .= '<th style="text-align:left;">House</th>';
		$html .= '<th style="text-align:left;">Pincode</th>';
		$html .= '<th style="text-align:left;">Business Address</th>';
		$html .= '<th style="text-align:left;">Center Name</th>';
		$html .= '<th style="text-align:left;">Vehicle No</th>';
		$html .= '<th style="text-align:left;">Item Name</th>';
		$html .= '<th style="text-align:left;">Bag Qty</th>';
		$html .= '<th style="text-align:left;">Net Weight(Qtl)</th>';
		$html .= '<th style="text-align:left;">Trade Rate</th>';
		$html .= '<th style="text-align:left;">Total Amt</th>';
		$html .= '<th style="text-align:left;">Applicable Rate</th>';
		for ($i = 1; $i <= $MaxQCLayer; $i++) {
			foreach ($QCparameter as $key => $value) {
				$html .= '<th style="text-align:left;">' . $value["ItemParameterName"] . " " . $i . '</th>';
			}
			$html .= '<th style="text-align:left;">Layer ' . $i . ' Weight(Qtl)</th>';
			$html .= '<th style="text-align:left;">Layer ' . $i . ' Bag Qty</th>';
			$html .= '<th style="text-align:left;">Layer ' . $i . ' Amt</th>';
		}
		$html .= '<th style="text-align:left;">TDS</th>';
		$html .= '<th style="text-align:left;">PO Taxable Amt</th>';
		$html .= '<th style="text-align:left;">PO GST Amt</th>';
		$html .= '<th style="text-align:left;">PO Net Amt</th>';
		$html .= '<th style="text-align:left;">QC Deduction</th>';
		foreach ($OtherDeductionItemList as $Okey => $Ovalue) {
			$html .= '<th style="text-align:left;">' . $Ovalue["ItemName"] . '</th>';
		}
		$html .= '<th style="text-align:left;">GST On Deduction</th>';
		$html .= '<th style="text-align:left;">Net Deduction</th>';
		$html .= '<th style="text-align:left;">Payable Amt</th>';
		$html .= '<th style="text-align:left;">Paid Amt</th>';
		$html .= '<th style="text-align:left;">Bal Amt</th>';
		$html .= '<th style="text-align:left;">Status</th>';
		$html .= '<th style="text-align:left;">IFSC</th>';
		$html .= '<th style="text-align:left;">Bank Name</th>';
		$html .= '<th style="text-align:left;">Acct Number</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		$SrNo = 1;
		foreach ($result as $key => $value) {
			$net_weight = $value['LoadedWeight'] - $value['TareWeight'];
			$PaidAmt = 0;
			foreach ($PaymentList as $key1 => $value1) {
				if ($value["BookingID"] == $value1["BookingID"] && $value["Gate_in_ID"] == $value1["GateINID"] && $value["AccountID"] == $value1["AccountID"]) {
					$PaidAmt += $value1["Amount"];
				}
			}
			$url = "window.open('" . admin_url() . "GateControl/PaymentDetails/" . $value["Gate_in_ID"] . "', '_blank')";
			$html .= '<tr onclick="' . $url . '">';
			$html .= '<td>' . $SrNo . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . _d($value["BookingDate"]) . '</td>';
			$html .= '<td>' . _d($value["VchlArrivalDateTime"]) . '</td>';
			$html .= '<td>' . $value["Gate_in_ID"] . '</td>';
			$html .= '<td>' . _d($value["gate_in_date"]) . '</td>';
			$html .= '<td>' . $value["PlantName"] . '</td>';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . preg_replace('!\s+!', ' ', $value["company"]) . '</td>';
			$AadharPAN = "";
			$state = "";
			if ($value["CustomerType"] == 1) {
				$html .= '<td>Farmer</td>';
				$AadharPAN = $value["aadhaar_number"];
				$state = $value["AState"];
			} else if ($value["CustomerType"] == 2) {
				$html .= '<td>Broker</td>';
				$AadharPAN = $value["Pan"];
				$state = $value["GSTState"];
			} else if ($value["CustomerType"] == 3) {
				$html .= '<td>Trader</td>';
				$AadharPAN = $value["Pan"];
				$state = $value["GSTState"];
			}
			$html .= '<td>' . $AadharPAN . '</td>';
			$html .= '<td>' . $state . '</td>';
			$html .= '<td>' . $value["Adist"] . '</td>';
			$html .= '<td>' . $value["Asubdist"] . '</td>';
			$html .= '<td title="' . $value["Apo"] . '">' . substr($value["Apo"], 0, 20) . '</td>';
			$html .= '<td title="' . $value["Avtc"] . '">' . substr($value["Avtc"], 0, 20) . '</td>';
			$html .= '<td title="' . $value["Aloc"] . '">' . substr($value["Aloc"], 0, 20) . '</td>';
			$html .= '<td title="' . $value["Astreet"] . '">' . substr($value["Astreet"], 0, 20) . '</td>';
			$html .= '<td title="' . $value["Ahouse"] . '">' . substr($value["Ahouse"], 0, 20) . '</td>';
			$html .= '<td>' . $value["Apincode"] . '</td>';
			$html .= '<td>' . $value["GSTAddress"] . '</td>';
			$html .= '<td>' . $value["CenterName"] . '</td>';
			$html .= '<td>' . $value["VehicleNo"] . '</td>';
			$html .= '<td>' . $value["ItemName"] . '</td>';
			$BagQty = 0;
			$TotalWeightMT = 0;
			foreach ($BagDetails as $kbag => $vbag) {
				if ($value["Gate_in_ID"] == $vbag["GateINID"]) {
					$BagQty = $vbag["TotalBagQty"];
					$TotalWeightMT = $vbag["TotalWeightMT"];
				}
			}
			$html .= '<td style="text-align:right">' . number_format($BagQty, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . (string) number_format(($TotalWeightMT * 10), 3, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format(($value['basic_rate']) * 10, 2, '.', ',') . '</td>';
			$TotalAmt = ($TotalWeightMT * 10) * $value['basic_rate'];
			$html .= '<td style="text-align:right">' . number_format($TotalAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($value['final_rate'], 2, '.', ',') . '</td>';
			$TotalDeduction = 0;
			$netDeduction = 0;
			for ($i = 1; $i <= $MaxQCLayer; $i++) {
				$LayerWiseAmt = 0;
				$LayerWiseWt = 0;
				$LayerWiseBag = 0;
				foreach ($QCparameter as $Qkey => $Qval) {
					$QCValue = "";
					foreach ($QCList as $QVKey => $QVVal) {
						if ($QVVal["ItemParameterID"] == $Qval["ItemParameterID"] && $value["Gate_in_ID"] == $QVVal["Gate_in_ID"] && $i == $QVVal["layer_number"]) {
							$QCValue = $QVVal["HParameterValue"];
							$TotalDeduction += $QVVal["deductionAmt"];
							$LayerWiseAmt += $QVVal["deductionAmt"];
							$LayerWiseWt = $QVVal["MTWeight"];
							$LayerWiseBag = $QVVal["BagQty"];
						}
					}
					$html .= '<td style="text-align:right">' . $QCValue . '</td>';
				}
				$html .= '<td style="text-align:right;">' . number_format($LayerWiseWt * 10, 2, '.', ',') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($LayerWiseBag, 2, '.', ',') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($LayerWiseAmt, 2, '.', ',') . '</td>';
			}
			$html .= '<td style="text-align:right">0.00</td>';
			if ($value["CustomerType"] == 1) {
				$NetAmt = $TotalWeightMT  * $value["final_rate"];
				$GrossAmt = $TotalWeightMT * $value["final_rate"];
				$GstAmt = 0.00;
			} else {
				$GrossAmt = (($TotalWeightMT * 10) * $value["basic_rate"]);
				$GstAmt = ($GrossAmt * $value["taxrate"]) / 100;
				$NetAmt = $GrossAmt + $GstAmt;
			}
			$netAmt = $GrossAmt + $GstAmt;
			$html .= '<td style="text-align:right">' . number_format($GrossAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($GstAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($netAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($TotalDeduction, 2, '.', ',') . '</td>';
			foreach ($OtherDeductionItemList as $OItemKey => $OItemVal) {
				$DedAmt = 0;
				foreach ($OtherDeductionItems as $OKey => $Oval) {
					if ($value["Gate_in_ID"] == $Oval["GateINID"] && $OItemVal["ItemID"] == $Oval["ItemID"]) {
						$DedAmt += $Oval["Amount"];
					}
				}
				$html .= '<td style="text-align:right">' . (string) number_format($DedAmt, 2, '.', ',') . '</td>';
				$TotalDeduction += $DedAmt;
			}
			if ($value["CustomerType"] == 1) {
				$NetAmt = $TotalWeightMT  * $value["final_rate"];
				$GrossAmt = $TotalWeightMT * $value["final_rate"];
				$GstAmt = 0.00;
				$DBGstAmt = 0.00;
				$netDeduction = $TotalDeduction;
				$PayableAmt = $NetAmt;
			} else {
				$GrossAmt = (($TotalWeightMT * 10) * $value["basic_rate"]);
				$GstAmt = ($GrossAmt * $value["taxrate"]) / 100;
				$NetAmt = $GrossAmt + $GstAmt;
				$DBGstAmt = ($TotalDeduction * $value["taxrate"]) / 100;
				$netDeduction = $DBGstAmt + $TotalDeduction;
				$PayableAmt = $NetAmt - $netDeduction;
			}
			$html .= '<td style="text-align:right">' . number_format($DBGstAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($netDeduction, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($PayableAmt, 2, '.', ',') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($PaidAmt, 2, '.', ',') . '</td>';
			$balAmt = $PayableAmt - $PaidAmt;
			$html .= '<td style="text-align:right">' . number_format($balAmt, 2, '.', ',') . '</td>';
			if ($PayableAmt <= $PaidAmt) {
				$msg = "PAID";
			} else if ($PaidAmt > 0) {
				$msg = "PARTIALLY PAID";
			} else {
				$msg = "UNPAID";
			}
			$html .= '<td>' . $msg . '</td>';
			$html .= '<td>' . $value["ifsc"] . '</td>';
			$html .= '<td>' . $value["bankName"] . '</td>';
			$html .= '<td>' . $value["accountNumber"] . '</td>';
			$html .= '</tr>';
			$SrNo++;
		}
		$html .= '</tbody>';
		echo $html;
	}
	public function GateControl_Reports_Details($id)
	{
		$data['id'] = $id;
		$data['title'] = "Gate Control Reports Details";
		$data['details'] = $this->GateControl_model->getSingleTradeById($id);
		$data['StaffList'] = $this->GateControl_model->GetAllStaffList();
		$BookingID = $data['details']->BookingID;
		$GateINID = $data['details']->Gate_in_ID;
		$ASNID = $data['details']->ASNID;
		$PurchID = $data['details']->PurchID;
		$TransType = $data['details']->TType;
		$CenterID = $data['details']->CenterID;
		$data['layers'] = $this->GateControl_model->getLayerDetails($BookingID, $GateINID);
		$data['DebitNoteItem'] = $this->GateControl_model->GetDebitNoteItemList();
		$data['OtherDeductionMasterList'] = $this->GateControl_model->GetOtherDeductionMasterList();
		$data['ActualOtherDeductionList'] = $this->GateControl_model->GetActualOtherDeductionList($BookingID, $GateINID);
		$data['StackList'] = $this->GateControl_model->GetStackListAgainstInward($BookingID, $GateINID);
		$data['peripheral'] = $this->GateControl_model->getPeripheralDetails($BookingID, $GateINID);
		// StockInventory Data
		$data['StockInvData'] = $this->GateControl_model->StockInventoryData($BookingID, $GateINID);
		// Trade Center Wise Godown list
		$data['GodownList'] = $this->GateControl_model->GetGodownListByCenter($CenterID);
		$data['cleaningBagDetails'] = $this->GateControl_model->getCleaningBagDetails($GateINID);
		/*echo "<pre>";
				print_r($data['StackList']);
			die;*/
		$data['withdrawalQc'] = $this->GateControl_model->getWithdrawalQCDetails($BookingID, $GateINID);
		$data['finalQC'] = $this->GateControl_model->getFinalQCDetails($BookingID, $GateINID);
		$data['staffName'] = array(
			'LWUserID' => $this->GateControl_model->getStaffNameFromId($data['details']->LWUserID),
			'FMUserID' => $this->GateControl_model->getStaffNameFromId($data['details']->FMUserID),
			'TWUserID' => $this->GateControl_model->getStaffNameFromId($data['details']->TWUserID),
		);
		$data['SName'] = array(
			'asn_by' => $this->GateControl_model->getStaffNameFromAccountID($data['details']->asn_by),
			'gate_in_by' => $this->GateControl_model->getStaffNameFromAccountID($data['details']->gate_in_by),
			'gate_out_by' => $this->GateControl_model->getStaffNameFromAccountID($data['details']->gate_out_by),
			'exit_by' => $this->GateControl_model->getStaffNameFromAccountID($data['details']->exit_by),
			'payment_approved_by' => $this->GateControl_model->getStaffNameFromAccountID($data['details']->payment_approved_by),
		);

		// echo "<pre>";
		// print_r($data);
		// die;
		// echo $TransType; die;

		if ($TransType == "P") {
			$data['PaymentMode'] = $this->GateControl_model->GetPaymentMode();
			$data['SendInwardToPcSoftCheck'] = $this->GateControl_model->SendInwardToPcSoftCheck($PurchID);
			$data['PcSoftASNStatus'] = $this->GateControl_model->SendInwardToPcSoftCheck($ASNID);
			$this->load->view('admin/gateControl/GateControl/PurchaseEntryDetails', $data);
		} else if ($TransType == "D") {
			$this->load->view('admin/gateControl/GateControl/DepositEntryDetails', $data);
		} else if ($TransType == "AW") {
			$this->load->view('admin/gateControl/GateControl/WithdrawEntryDetails', $data);
		} else if ($TransType == "TW") {
			$this->load->view('admin/gateControl/GateControl/WithdrawEntryDetails', $data);
		} else if ($TransType == "W") {
			$this->load->view('admin/gateControl/GateControl/WithdrawEntryDetails', $data);
		} else if ($TransType == "A") {
			$this->load->view('admin/gateControl/GateControl/AnamatEntryDetails', $data);
		} else if ($TransType == "S") {
			$this->load->view('admin/gateControl/GateControl/KirtiSellEntryDetails', $data);
		} else if ($TransType == "T") {
			$this->load->view('admin/gateControl/GateControl/TradeFinanceDetails', $data);
		}
	}
	public function GetVillageList()
	{
		$zip = $this->input->post('zip');
		$Villagedata = $this->GateControl_model->VillageDetails($zip);
		echo json_encode($Villagedata);
	}
	public function GetControldata()
	{
		$id = $this->input->post('id');
		$GateInID = $this->input->post('GateInID');
		$GateControlData = $this->GateControl_model->GateControlDetails($id, $GateInID);
		echo json_encode($GateControlData);
	}
	public function Ganerate_wr_details()
	{
		$wr_list = $this->input->post('wr_list');
		$result = $this->GateControl_model->Ganerate_wr_details($wr_list);
		$total_weight = 0;
		$total_amount = 0;
		foreach ($result as $key => $val) {
			$net_weight = $val["LoadedWeight"] - $val["TareWeight"];
			$final_rate = $val["final_rate"];
			$total_weight += $net_weight / 10;
			$total_amount += $net_weight * $final_rate;
		}
		$details->total_weight = $total_weight;
		$details->total_amount = $total_amount;
		echo json_encode($details);
	}
	public function GetFilterDataGateControl()
	{
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'TType' => $this->input->post('TType'),
			'ItemID' => $this->input->post('ItemID'),
			'CenterID' => $this->input->post('CenterID'),
			'FeildOfficer' => $this->input->post('FeildOfficer'),
			'villagename' => $this->input->post('villagename'),
		);
		$result = $this->GateControl_model->getFilterDataGateControlDB($data);
		$TotalMt  = 0;
		$html = '';
		foreach ($result as $key => $value) {
			if ($value['TType'] == 'P') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "CLEANING DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				} else if ($value['status'] == 13) {
					$status_val = "PAYMENT ADVICE REQUEST SENT";
				} else if ($value['status'] == 14) {
					$status_val = "RO OFFICE QC DONE";
				} else if ($value['status'] == 15) {
					$status_val = "HO OFFICE QC DONE";
				} else if ($value['status'] == 16) {
					$status_val = "PAYMENT ADVICE APROVE";
				} else if ($value['status'] == 17) {
					$status_val = "DATA SEND TO PCSOFT";
				} else if ($value['status'] == 18) {
					$status_val = "INWARD REJECTED";
				}
			}
			if ($value['TType'] == 'D') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				}
			}
			if ($value['TType'] == 'W') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 4) {
					$status_val = "LOADING IN PROGRESS ";
				} else if ($value['status'] == 5) {
					$status_val = "LOADING FINISHED ";
				} else if ($value['status'] == 6) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 7) {
					$status_val = "FINAL QC DONE";
				} else if ($value['status'] == 8) {
					$status_val = "GROSS WEIGHT CAPTURED";
				} else if ($value['status'] == 9) {
					$status_val = "MARK AS EXIT";
				} else if ($value['status'] == 10) {
					$status_val = "EXIT";
				}
			}
			if ($value['TType'] == 'S') {
				if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "EMPTY VEHICLE WEIGHT DONE";
				} else if ($value['status'] == 4) {
					$status_val = "LOADING IN PROGRESS ";
				} else if ($value['status'] == 5) {
					$status_val = "LOADING FINISHED ";
				} else if ($value['status'] == 6) {
					$status_val = "LOADING QC DONE";
				} else if ($value['status'] == 7) {
					$status_val = "GROSS WEIGHT CAPTURED";
				} else if ($value['status'] == 8) {
					$status_val = "PAYMENT DONE";
				} else if ($value['status'] == 9) {
					$status_val = "GATE OUT GANERATED";
				} else if ($value['status'] == 10) {
					$status_val = "MARK AS EXIT";
				}
			}
			if ($value['TType'] == 'A') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "CLEANING DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				} else if ($value['status'] == 13) {
					//$status_val = "PAYMENT ADVICE REQUEST SENT";
					$status_val = "Loan Provided";
				} else if ($value['status'] == 14) {
					$status_val = "RO OFFICE QC DONE";
				} else if ($value['status'] == 15) {
					$status_val = "HO OFFICE QC DONE";
				} else if ($value['status'] == 16) {
					$status_val = "PAYMENT ADVICE APROVE";
				}
			}
			if ($value['TType'] == 'T') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "CLEANING DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				} else if ($value['status'] == 13) {
					//$status_val = "PAYMENT ADVICE REQUEST SENT";
					$status_val = "Loan Provided";
				} else if ($value['status'] == 14) {
					$status_val = "RO OFFICE QC DONE";
				} else if ($value['status'] == 15) {
					$status_val = "HO OFFICE QC DONE";
				} else if ($value['status'] == 16) {
					$status_val = "PAYMENT ADVICE APROVE";
				}
			}
			if (($value['LoadedWeight'] != '') || ($value['LoadedWeight'] != null)) {
				if (($value['TareWeight'] != '') || ($value['TareWeight'] != null)) {
					if ($value['TType'] == "S") {
						$net_weight = ($value['TareWeight'] / 10) - ($value['LoadedWeight'] / 10);
					} else {
						$net_weight = ($value['LoadedWeight'] / 10) - ($value['TareWeight'] / 10);
					}
				} else {
					$net_weight = '0';
				}
			} else {
				$net_weight = '0';
			}
			$html .= '<tr onclick="fill_data(' . $value["id"] . ')">';
			$html .= '<td>' . ($key + 1) . '</td>';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["company"] . '</td>';
			$html .= '<td>' . $value["VillageName"] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . _d($value["VchlArrivalDateTime"]) . '</td>';
			$html .= '<td>' . $value["ASNID"] . '</td>';
			$html .= '<td>' . $value["Gate_in_ID"] . '</td>';
			$html .= '<td>' . _d($value["gate_in_date"]) . '</td>';
			$html .= '<td>' . $value["VehicleNo"] . '</td>';
			$html .= '<td>' . $value["TType2"] . '</td>';
			$html .= '<td>' . $value["ItemID"] . '</td>';
			$html .= '<td>' . $value["ItemName"] . '</td>';
			$html .= '<td style="text-align:right;">' . number_format($net_weight, 2, '.', '') . '</td>';
			$html .= '<td>' . $value["firstname"] . " " . $value["lastname"] . '</td>';
			$html .= '<td>' . $status_val . '</td>';
			$html .= '</tr>';
			$TotalMt += $net_weight;
		}
		$html .= '<tr >';
		$html .= '<td colspan="11" style="text-align:right;font-size:14px;font-weight:700;">Total</td>';
		$html .= '<td style="text-align:right;font-size:14px;font-weight:700;">' . number_format($TotalMt, 2, '.', '') . '</td>';
		$html .= '<td colspan="2"></td>';
		$html .= '</tr>';
		echo $html;
	}
	public function sendGateinToNewErp()
	{
		$Gate_in_ID = $this->input->post('gate_in_id');
		$this->db->select('*');
		$this->db->from(db_prefix() . 'GateMaster');
		$this->db->where('Gate_in_ID', $Gate_in_ID);
		$query = $this->db->get()->row();
		$httpData = [
			"COCD" => $query->PartyID,
			"TradeID" => $query->BookingID,
			"GateInID" => $Gate_in_ID,
			"VehicleNo" => $query->VehicleNo,
			"DriverName" => "",
			"DriverMobileNo" => $query->Phone ?? ""
		];
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/GateIn",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($httpData),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));
		$response = curl_exec($curl);
		$response_array = json_decode($response);
		if ($response_array->status) {
			$GateInID = $response_array->data->GateInID;
			// echo json_encode($id);
			$insert_referance = array(
				"Type" => $query->TType,
				"Name" => "GateIN",
				"GIC_Reference" => $Gate_in_ID,
				"pcsoft_doc_ref" => $GateInID
			);
			$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
		}
		curl_close($curl);
		echo $response;
	}
	public function export_gateControllist()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'TType' => $this->input->post('TType'),
				'ItemID' => $this->input->post('ItemID'),
				'CenterID' => $this->input->post('CenterID'),
				'FeildOfficer' => $this->input->post('FeildOfficer'),
				'villagename' => $this->input->post('villagename'),
				'Bookingidtext' => $this->input->post('Bookingidtext'),
				'ItemNametext' => $this->input->post('ItemNametext'),
				'centertext' => $this->input->post('centertext'),
				'fieldofficertext' => $this->input->post('fieldofficertext'),
				'villagenametext' => $this->input->post('villagenametext'),
			);
			$result = $this->GateControl_model->getFilterDataGateControlDB($data);
			$filters = [];
			if (!empty($data['from_date'])) {
				$filters[] = 'From Date: ' . $data['from_date'];
			}
			if (!empty($data['to_date'])) {
				$filters[] = 'To Date: ' . $data['to_date'];
			}
			if (!empty($data['TType'])) {
				$filters[] = 'Booking Type: ' . $data['Bookingidtext'];
			}
			if (!empty($data['ItemID'])) {
				$filters[] = 'Item Name: ' . $data['ItemNametext'];
			}
			if (!empty($data['CenterID'])) {
				$filters[] = 'Center: ' . $data['centertext'];
			}
			if (!empty($data['FeildOfficer'])) {
				$filters[] = 'FeildOfficer: ' . $data['fieldofficertext'];
			}
			if (!empty($data['villagename'])) {
				$filters[] = 'Village Name: ' . $data['villagenametext'];
			}
			$filter_text = 'Filters: ' . implode(', ', $filters);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			$writer->markMergedCell('Sheet1', 2, 0, 2, 12);
			$writer->writeSheetRow('Sheet1', array($filter_text));
			$set_col_tk = [];
			$set_col_tk["AccountID"] =  'AccountID';
			$set_col_tk["Party Name"] = 'Party Name';
			$set_col_tk["Village Name"] = 'Village Name';
			$set_col_tk["BookingID"] = 'BookingID';
			$set_col_tk["Vehicle Arrival Date"] = 'Vehicle Arrival Date';
			$set_col_tk["ASNID"] = 'ASNID';
			$set_col_tk["Gate Pass No."] = 'Gate Pass No.';
			$set_col_tk["Gate Pass Date"] = 'Gate Pass Date';
			$set_col_tk["Truck No."] = 'Truck No.';
			$set_col_tk["TType"] =  'TType';
			$set_col_tk["ItemID"] =  'ItemID';
			$set_col_tk["Item Name"] =  'Item Name';
			$set_col_tk["Net Weight (Qtl)"] = 'Net Weight (Qtl)';
			$set_col_tk["Field Officer"] = 'Field Officer';
			$set_col_tk["Status"] = 'Status';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$TotalMt = 0;
			foreach ($result as $k => $value) {
				if ($value['TType'] == 'P') {
					if ($value['status'] == 0) {
						$status_val = "NO ACTION";
					} else if ($value['status'] == 1) {
						$status_val = "ASN GENERATED";
					} else if ($value['status'] == 2) {
						$status_val = "GATE IN GENERATED";
					} else if ($value['status'] == 3) {
						$status_val = "PERIPHERAL DONE";
					} else if ($value['status'] == 4) {
						$status_val = "GROSS WEIGHT CAPTURED ";
					} else if ($value['status'] == 5) {
						$status_val = "UNLOADING IN PROGRESS ";
					} else if ($value['status'] == 6) {
						$status_val = "UNLOADING FINISHED ";
					} else if ($value['status'] == 7) {
						$status_val = "QC DONE ";
					} else if ($value['status'] == 8) {
						$status_val = "CLEANING DONE ";
					} else if ($value['status'] == 9) {
						$status_val = "TARE WEIGHT CAPTURED ";
					} else if ($value['status'] == 10) {
						$status_val = "FINAL QC DONE ";
					} else if ($value['status'] == 11) {
						$status_val = "GATE OUT GENERATED";
					} else if ($value['status'] == 12) {
						$status_val = "EXIT ";
					} else if ($value['status'] == 13) {
						$status_val = "PAYMENT ADVICE REQUEST SENT";
					} else if ($value['status'] == 14) {
						$status_val = "RO OFFICE QC DONE";
					} else if ($value['status'] == 15) {
						$status_val = "HO OFFICE QC DONE";
					} else if ($value['status'] == 16) {
						$status_val = "PAYMENT ADVICE APROVE";
					} else if ($value['status'] == 17) {
						$status_val = "DATA SEND TO PCSOFT";
					} else if ($value['status'] == 18) {
						$status_val = "INWARD REJECTED";
					}
				}
				if ($value['TType'] == 'D') {
					if ($value['status'] == 0) {
						$status_val = "NO ACTION";
					} else if ($value['status'] == 1) {
						$status_val = "ASN GENERATED";
					} else if ($value['status'] == 2) {
						$status_val = "GATE IN GENERATED";
					} else if ($value['status'] == 3) {
						$status_val = "PERIPHERAL DONE";
					} else if ($value['status'] == 4) {
						$status_val = "GROSS WEIGHT CAPTURED ";
					} else if ($value['status'] == 5) {
						$status_val = "UNLOADING IN PROGRESS ";
					} else if ($value['status'] == 6) {
						$status_val = "UNLOADING FINISHED ";
					} else if ($value['status'] == 7) {
						$status_val = "QC DONE ";
					} else if ($value['status'] == 9) {
						$status_val = "TARE WEIGHT CAPTURED ";
					} else if ($value['status'] == 10) {
						$status_val = "FINAL QC DONE ";
					} else if ($value['status'] == 11) {
						$status_val = "GATE OUT GENERATED";
					} else if ($value['status'] == 12) {
						$status_val = "EXIT ";
					}
				}
				if ($value['TType'] == 'W') {
					if ($value['status'] == 0) {
						$status_val = "NO ACTION";
					} else if ($value['status'] == 1) {
						$status_val = "ASN GENERATED";
					} else if ($value['status'] == 2) {
						$status_val = "GATE IN GENERATED";
					} else if ($value['status'] == 3) {
						$status_val = "TARE WEIGHT CAPTURED ";
					} else if ($value['status'] == 4) {
						$status_val = "LOADING IN PROGRESS ";
					} else if ($value['status'] == 5) {
						$status_val = "LOADING FINISHED ";
					} else if ($value['status'] == 6) {
						$status_val = "QC DONE ";
					} else if ($value['status'] == 7) {
						$status_val = "FINAL QC DONE";
					} else if ($value['status'] == 8) {
						$status_val = "GROSS WEIGHT CAPTURED";
					} else if ($value['status'] == 9) {
						$status_val = "MARK AS EXIT";
					} else if ($value['status'] == 10) {
						$status_val = "EXIT";
					}
				}
				if ($value['TType'] == 'A') {
					if ($value['status'] == 0) {
						$status_val = "NO ACTION";
					} else if ($value['status'] == 1) {
						$status_val = "ASN GENERATED";
					} else if ($value['status'] == 2) {
						$status_val = "GATE IN GENERATED";
					} else if ($value['status'] == 3) {
						$status_val = "PERIPHERAL DONE";
					} else if ($value['status'] == 4) {
						$status_val = "GROSS WEIGHT CAPTURED ";
					} else if ($value['status'] == 5) {
						$status_val = "UNLOADING IN PROGRESS ";
					} else if ($value['status'] == 6) {
						$status_val = "UNLOADING FINISHED ";
					} else if ($value['status'] == 7) {
						$status_val = "QC DONE ";
					} else if ($value['status'] == 8) {
						$status_val = "CLEANING DONE ";
					} else if ($value['status'] == 9) {
						$status_val = "TARE WEIGHT CAPTURED ";
					} else if ($value['status'] == 10) {
						$status_val = "FINAL QC DONE ";
					} else if ($value['status'] == 11) {
						$status_val = "GATE OUT GENERATED";
					} else if ($value['status'] == 12) {
						$status_val = "EXIT ";
					} else if ($value['status'] == 13) {
						$status_val = "PAYMENT ADVICE REQUEST SENT";
					} else if ($value['status'] == 14) {
						$status_val = "RO OFFICE QC DONE";
					} else if ($value['status'] == 15) {
						$status_val = "HO OFFICE QC DONE";
					} else if ($value['status'] == 16) {
						$status_val = "PAYMENT ADVICE APROVE";
					}
				}
				if (($value['LoadedWeight'] != '') || ($value['LoadedWeight'] != null)) {
					if (($value['TareWeight'] != '') || ($value['TareWeight'] != null)) {
						if ($value['TType'] == "S") {
							$net_weight = ($value['TareWeight'] / 10) - ($value['LoadedWeight'] / 10);
						} else {
							$net_weight = ($value['LoadedWeight'] / 10) - ($value['TareWeight'] / 10);
						}
					} else {
						$net_weight = '0';
					}
				} else {
					$net_weight = '0';
				}
				$list_add = [];
				$list_add[] = $value["AccountID"];
				$list_add[] = $value["company"];
				$list_add[] = $value["VillageName"];
				$list_add[] = $value["BookingID"];
				$list_add[] = _d($value["VchlArrivalDateTime"]);
				$list_add[] = $value['ASNID'];
				$list_add[] = $value["Gate_in_ID"];
				$list_add[] = _d($value["gate_in_date"]);
				$list_add[] = $value["VehicleNo"];
				$list_add[] = $value["TType2"];
				$list_add[] = $value["ItemID"];
				$list_add[] = $value["ItemName"];
				$list_add[] = $net_weight;
				$list_add[] = $value["firstname"] . " " . $value["lastname"];
				$list_add[] = $status_val;
				$TotalMt += $net_weight;
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$list_add = [];
			$list_add[] = "Total";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = $TotalMt;
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = $row_a;
			$writer->writeSheetRow('Sheet1', $list_add);
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'InwardList.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	public function loan_Receipt_submit()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$username = $this->session->userdata('username');
		$data = $this->input->post();
		$AccountID = $data['AccountID'];
		$BookingID = $data['BookingID'];
		$TType = $data['TType'];
		$PassedFrom = $data['PassedFrom'];
		$rceipt_wr_list = $data['rceipt_wr_list'];
		$receipt_wr_amount = $data['receipt_wr_amount'];
		$receipt_wr_weight = $data['receipt_wr_weight'];
		$receipt_per = $data['receipt_per'];
		$receipt_amount = $data['receipt_amount'];
		$receiptdate = $data['receiptdate'];
		$all_success = true;
		foreach ($rceipt_wr_list as $wr_id) {
			$loan_data = array(
				'PlantID'    => $selected_company,
				'FY'         => $fy,
				'TransDate'  => to_sql_date($receiptdate) . " " . date('H:i:s'),
				'TransType'  => $TType,
				'TType' => "C",
				'PassedFrom' => $PassedFrom,
				'BookingID'  => $BookingID,
				'GateINID'  => $wr_id,
				'WRWeight'   => $receipt_wr_weight,
				'WRValue'    => $receipt_wr_amount,
				'AccountID'  => $AccountID,
				'loan_per'   => $receipt_per,
				'Amount'     => $receipt_amount,
				'status'     => 'O',
				'UserID'     => $username,
			);
			$res = $this->GateControl_model->addLoanDetails($loan_data);
			if ($res) {
				// update inward status table
				$inwarddata = array(
					'BookingID'  => $BookingID,
					'GateINID'   => $wr_id,
					'TransDate'  => date('Y-m-d H:i:s'),
					'Type'       => $TType,
					'status'     => 'Y',
					'UserID'     => $username,
				);
				$this->db->insert('tblinward_status', $inwarddata);
				// update GateMaster status
				$this->db->where('BookingID', $BookingID);
				$this->db->where('Gate_in_ID', $wr_id);
				$this->db->set('status', 13);
				$this->db->update('tblGateMaster');
			} else {
				$all_success = false;
			}
		}
		if ($all_success) {
			set_alert('success', "Receipt Amount updated successfully");
		} else {
			set_alert('warning', "Some loan entries failed to insert");
		}
		return $all_success;
	}
	public function GetAdvancePaymentData()
	{
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'CenterID' => $this->input->post('CenterID'),
			'PartyID' => $this->input->post('PartyID'),
		);
		$result = $this->GateControl_model->getFilterDataAdvancePayment($data);
		$html = '';
		foreach ($result as $key => $value) {
			if ($value['Is_PcsoftData'] == 'Y') {
				$PCSoftStatus = "--";
			} else if ($value['Is_PcsoftData'] == 'N') {
				$PCSoftStatus = '<button type="button" onclick="ReSendTradeToPcSoft(' . $value["id"] . ')" class="btn btn-info">Send To PcSoft</button>';
			}
			$html .= '<tr onclick="fill_data(' . $value["id"] . ')">';
			$html .= '<td>' . ($key + 1) . '</td>';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["company"] . '</td>';
			$html .= '<td>' . $value["BookingID"] . '</td>';
			$html .= '<td>' . $value["GateINID"] . '</td>';
			$html .= '<td>' . $value["PartyID"] . '</td>';
			$html .= '<td>' . $value["CenterName"] . '</td>';
			$html .= '<td>' . $value["EffectOn"] . '</td>';
			$html .= '<td>' . $value["VoucherNo"] . '</td>';
			$html .= '<td>' . $value["Amount"] . '</td>';
			$html .= '<td>' . _d($value["TransDate"]) . '</td>';
			$html .= '<td>' . $value["UtrNo"] . '</td>';
			$html .= '<td>' . $value["Narration"] . '</td>';
			$html .= '<td>' . $PCSoftStatus . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function SendAdvancePaymentData()
	{
		$id = $this->input->post('ID');
		$PaymentData = $this->GateControl_model->SendAdvancePaymentData($id);
		if ($PaymentData) {
			// Send Data to PcSoft
			$data_pc_soft_array =  array(
				"PartyID" => $PaymentData->PartyID,
				"VoucherNo" => $PaymentData->VoucherNo,
				"payment_amt" => $PaymentData->Amount,
				"narration" => $PaymentData->Narration,
				"FromAccount" => $PaymentData->EffectOn,
				"ToAccount" => $PaymentData->ShortCode,
				"utr_no" => $PaymentData->UtrNo,
				"PaymentDateTime" => $PaymentData->TransDate,
				"access_tokan" => "fe3fd1f94239c467727c5cae504d4fdd",
			);
			$pc_soft_data = json_encode($data_pc_soft_array);
			$curl = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SendAdvPayment",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $pc_soft_data,
					CURLOPT_HTTPHEADER => array(
						"content-type: application/json"
					),
				)
			);
			$response = curl_exec($curl);
			$response_array = json_decode($response);
			$PcSoft_po = $response_array->doc_ref_number;
			$status = $response_array->Status;
			if ($status == true) {
				$updatestatus = array(
					'Is_PcsoftData' => 'Y',
				);
				$this->db->where('id', $id);
				$this->db->update('tblAdvancePayment', $updatestatus);
				echo json_encode(true);
				return;
			}
			$err = curl_error($curl);
			curl_close($curl);
		}
		echo json_encode(false);
		return;
	}
	public function export_AdvancePaymentList()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'CenterID' => $this->input->post('CenterID'),
				'PartyID' => $this->input->post('PartyID'),
				'centertext' => $this->input->post('centertext'),
				'partytext' => $this->input->post('partytext'),
			);
			$result = $this->GateControl_model->getFilterDataAdvancePayment($data);
			$filters = [];
			if (!empty($data['from_date'])) {
				$filters[] = 'From Date: ' . $data['from_date'];
			}
			if (!empty($data['to_date'])) {
				$filters[] = 'To Date: ' . $data['to_date'];
			}
			if (!empty($data['CenterID'])) {
				$filters[] = 'Center Name: ' . $data['centertext'];
			} else {
				$filters[] = 'Center Name: ALL';
			}
			if (!empty($data['PartyID'])) {
				$filters[] = 'Party Name: ' . $data['partytext'];
			} else {
				$filters[] = 'Party Name: ALL';
			}
			$filter_text = 'Filters: ' . implode(', ', $filters);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			$writer->markMergedCell('Sheet1', 2, 0, 2, 12);
			$writer->writeSheetRow('Sheet1', array($filter_text));
			$set_col_tk = [];
			$set_col_tk["AccountID"] =  'AccountID';
			$set_col_tk["Party Name"] = 'Party Name';
			$set_col_tk["BookingID"] = 'BookingID';
			$set_col_tk["Gate Pass No."] = 'Gate Pass No.';
			$set_col_tk["Party ID"] = 'Party ID';
			$set_col_tk["Center Name"] = 'Center Name';
			$set_col_tk["Payment Mode"] =  'Payment Mode';
			$set_col_tk["Voucher No"] =  'Voucher No';
			$set_col_tk["Payment Amt"] =  'Payment Amt';
			$set_col_tk["Payment Date"] = 'Payment Date';
			$set_col_tk["Utr No."] = 'Utr No.';
			$set_col_tk["Narration"] = 'Narration';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$TotalMt = 0;
			foreach ($result as $k => $value) {
				$list_add = [];
				$list_add[] = $value["AccountID"];
				$list_add[] = $value["company"];
				$list_add[] = $value["BookingID"];
				$list_add[] = $value['GateINID'];
				$list_add[] = $value["PartyID"];
				$list_add[] = $value["CenterName"];
				$list_add[] = $value["EffectOn"];
				$list_add[] = $value["VoucherNo"];
				$list_add[] = $value["Amount"];
				$list_add[] = _d($value["TransDate"]);
				$list_add[] = $value["UtrNo"];
				$list_add[] = $value["Narration"];
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'Advance Payment List.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	public function saveFinalQCWithdrawal()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$data = $this->input->post();
		$BookingID = $data['BookingID'];
		$Gate_in_ID = $data['Gate_in_ID'];
		$ItemID = $data['ItemID'];
		$id = $data['id'];
		$AccountID = $data['AccountID'];
		$BookingType = $data['BookingType'];
		unset($data['BookingType']);
		unset($data['Gate_in_ID']);
		unset($data['BookingID']);
		unset($data['ItemID']);
		unset($data['id']);
		unset($data['AccountID']);
		$AccountDetails = $this->db->select('AccountID,vat')->get_where(db_prefix() . 'clients', array('AccountID' => $AccountID))->row();
		$BookingDetailsfrom_lead = $this->db->select('*')->get_where(db_prefix() . 'lead_master', array('BookingID' => $BookingID))->row();
		$GateControlDetails = $this->db->select('*')->get_where(db_prefix() . 'GateMaster', array('BookingID' => $BookingID))->row();
		$Netweight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
		$purch_amt = $Netweight * $GateControlDetails->basic_rate;
		if ($AccountDetails->vat == '') {
			$bt = 'N';
		} else {
			$bt = 'Y';
		}
		foreach ($data as $key => $value) {
			$data2 = array(
				'BookingID' => $BookingID,
				'Gate_in_ID' => $Gate_in_ID,
				'TType' => 'F',
				'ItemID' => $ItemID,
				'ItemParameterID' => $key,
				'ParameterValue' => $value,
				'UserID' => $this->session->userdata('username'),
				'TransDate' => date('Y-m-d H:i:s'),
			);
			$res =  $this->GateControl_model->addFinalQCWithdrawalDB($data2);
		}
		if ($res == true) {
			$new_poNumber = get_option('next_purchase_number_for_kirti');
			$Billno = "PO" . $fy . $new_poNumber;
			$data_array = array(
				'PlantID' => $selected_company,
				'FY' => $fy,
				'BT' => $bt,
				'PurchID' => $Billno,
				'TransID' => $BookingID,
				'Transdate' => date('Y-m-d H:i:s'),
				'FrtAccountID' => NULL,
				'AccountID' => $AccountID,
				'Invoiceno' => NULL,
				'Invoicedate' => date('Y-m-d H:i:s'),
				'Purchamt' => $purch_amt,
				'Discamt' => 0,
				'Frtamt' => 0,
				'Othamt' => 0,
				'Invamt' => $purch_amt,
				'ItCount' => 1,
				'RoundOffAmt' => NULL,
				'OthAccountID' => NULL,
				'cgstamt' => 0,
				'sgstamt' => 0,
				'igstamt' => 0,
				'tcs' => NULL,
				'tcsAmt' => NULL,
				"Userid" => $_SESSION['username'],
			);
			$this->db->insert(db_prefix() . 'purchasemaster', $data_array);
			if ($this->db->affected_rows() > 0) {
				$this->GateControl_model->increment_next_ponumber();
				$data_array_result = array(
					'PlantID' => $selected_company,
					'FY' => $fy,
					'cnfid' => 1,
					'OrderID' => $Billno,
					"TransID" => $BookingID,
					'TransDate' => date('Y-m-d H:i:s'),
					'BillID' => $Billno,
					'GodownID' => $BookingDetailsfrom_lead->CenterID,
					'CenterID' => $GateControlDetails->CenterID,
					'TransDate2' => date('Y-m-d H:i:s'),
					'TType' => 'P',
					'TType2' => 'Purchase',
					'AccountID' => $AccountID,
					'ItemID' => $ItemID,
					'CaseQty' => 1,
					'PurchRate' => $GateControlDetails->basic_rate,
					'SaleRate' => $GateControlDetails->basic_rate,
					'BasicRate' => $GateControlDetails->basic_rate,
					'SuppliedIn' => $GateControlDetails->unit,
					'Cases' => $Netweight,
					'OrderQty' => $Netweight,
					'BilledQty' => $Netweight,
					'OrderAmt' => $purch_amt,
					'DiscAmt' => 0,
					'gst' => NULL,
					'cgst' => 0,
					'sgst' => 0,
					'igst' => NULL,
					'cgstamt' => 0,
					'sgstamt' => 0,
					'igstamt' => NULL,
					'OrderAmt' => $purch_amt,
					'ChallanAmt' => $purch_amt,
					'NetOrderAmt' => $purch_amt,
					'NetChallanAmt' => $purch_amt,
					'Ordinalno' => 1,
					'UserID' => $_SESSION['username'],
				);
				$this->db->insert(db_prefix() . 'history', $data_array_result);
			}
			header('Location:' . admin_url() . 'GateControl/GateControl_Reports_Details/' . $id);
		}
	}
	public function SaveKirtiSellPayment()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$username = $this->session->userdata('username');
		$data = $this->input->post();
		$narration = 'Being Payment received / ' . $data['BookingID'] . ' against ' . $data['GateINID'] . ' TransID ' . ["SalesID"];
		$next_receipt_number = get_option2('next_receipts_number_for_kirti', $fy);
		$credit_data = array(
			"FY" => $fy,
			"PlantID" => $selected_company,
			"VoucherID" => $next_receipt_number,
			"Transdate" => date('Y-m-d H:i:s'),
			"TransDate2" => date('Y-m-d H:i:s'),
			"AccountID" => $data['AccountID'],
			"CenterID" => $data['CenterID'],
			"CommodityID" => $data['ItemID'],
			"EntryFor" => 3,
			"TType" => "C",
			"Amount" => $data['payment_amount'],
			"Narration" => $narration,
			"PassedFrom" => "RECEIPTS",
			"OrdinalNo" => "1",
			"UserID" => $username
		);
		if ($this->db->insert(db_prefix() . 'accountledger', $credit_data)) {
			$insert++;
		}
		$debit_data = array(
			"FY" => $fy,
			"PlantID" => $selected_company,
			"VoucherID" => $next_receipt_number,
			"Transdate" => date('Y-m-d H:i:s'),
			"TransDate2" => date('Y-m-d H:i:s'),
			"AccountID" => "CASH",
			"CenterID" => $data['CenterID'],
			"CommodityID" => $data['ItemID'],
			"EntryFor" => 3,
			"TType" => "D",
			"Amount" => $data['payment_amount'],
			"Narration" => $narration,
			"PassedFrom" => "RECEIPTS",
			"OrdinalNo" => "2",
			"UserID" => $username
		);
		if ($this->db->insert(db_prefix() . 'accountledger', $debit_data)) {
			$insert++;
		}
		if ($insert > 0) {
			$this->db->where('BookingID', $data['BookingID']);
			$this->db->where('Gate_in_ID', $data['GateINID']);
			$this->db->set('status', 8);
			$this->db->update('tblGateMaster');
			$this->increment_next_receipts_number();
			set_alert('success', "Payment received successfully");
		} else {
			set_alert('warning', "something went wrong, please try again");
		}
		header('Location:' . admin_url() . 'GateControl/GateControl_Reports_Details/' . $data['id']);
	}
	public function increment_next_receipts_number()
	{
		// Update next receipt number in settings
		$fy = $this->session->userdata('finacial_year');
		$this->db->where('name', 'next_receipts_number_for_kirti');
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $fy);
		$this->db->update(db_prefix() . 'options');
	}
	public function loan_dis_submit()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$username = $this->session->userdata('username');
		$data = $this->input->post();
		$AccountID = $data['AccountID'];
		$BookingID = $data['BookingID'];
		$TType = $data['TType'];
		$wr_list = $data['wr_list'];
		$wr_amount = $data['wr_amount'];
		$wr_weight = $data['wr_weight'];
		$dis_per = $data['dis_per'];
		$dis_amount = $data['dis_amount'];
		$ROI = $data['ROI'];
		$DisbrusmentDate = $data['disbrusmentdate'];
		$all_success = true;
		foreach ($wr_list as $wr_id) {
			$total_weight = 0;
			$total_amount = 0;
			$result = $this->GateControl_model->Ganerate_wr_details($wr_id);
			foreach ($result as $key => $val) {
				$net_weight = $val["LoadedWeight"] - $val["TareWeight"];
				$final_rate = $val["final_rate"];
				$total_weight += $net_weight / 10;
				$total_amount += $net_weight * $final_rate;
			}
			$disbursed_amount = ($total_amount * floatval($dis_per)) / 100;
			$loan_data = array(
				'PlantID'    => $selected_company,
				'FY'         => $fy,
				'TransDate'  =>  to_sql_date($DisbrusmentDate) . " " . date('H:i:s'),
				'TransType'  => $TType,
				'TType' => "D",
				'PassedFrom' => "Disbursment",
				'BookingID'  => $BookingID,
				'GateINID'  => $wr_id,
				'WRWeight'   => $total_weight,
				'WRValue'    => $total_amount,
				'AccountID'  => $AccountID,
				'loan_per'   => $dis_per,
				'Amount'     => $disbursed_amount,
				'ROC'        => $ROI,
				'status'     => 'O',
				'UserID'     => $username,
			);
			$res = $this->GateControl_model->addLoanDetails($loan_data);
			if ($res) {
				// update inward status table
				$inwarddata = array(
					'BookingID'  => $BookingID,
					'GateINID'   => $wr_id,
					'TransDate'  => date('Y-m-d H:i:s'),
					'Type'       => $TType,
					'status'     => 'Y',
					'UserID'     => $username,
				);
				$this->db->insert('tblinward_status', $inwarddata);
				// update GateMaster status
				$this->db->where('BookingID', $BookingID);
				$this->db->where('Gate_in_ID', $wr_id);
				$this->db->set('status', 13);
				$this->db->update('tblGateMaster');
			} else {
				$all_success = false;
			}
		}
		if ($all_success) {
			set_alert('success', "Loan Amount updated successfully");
		} else {
			set_alert('warning', "Some loan entries failed to insert");
		}
		return $all_success;
	}
	public function saveFinalQC()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$data = $this->input->post();
		$BookingID = $data['BookingID'];
		$GateINID = $data['GateINID'];
		$ItemID = $data['ItemID'];
		$id = $data['id'];
		$AccountID = $data['AccountID'];
		$BookingType = $data['BookingType'];
		$QCApproval = $data['QCApproval'];
		unset($data['BookingType']);
		unset($data['BookingID']);
		unset($data['ItemID']);
		unset($data['id']);
		unset($data['AccountID']);
		unset($data['GateINID']);
		unset($data['GrossWeight']);
		unset($data['TareWeight']);
		unset($data['QCApproval']);
		unset($data['QC_for']);
		unset($data['QCApprovalstatus']);
		foreach ($data as $key => $value) {
			$data2 = array(
				'BookingID' => $BookingID,
				'Gate_in_ID' => $GateINID,
				'TType' => 'F',
				'ItemID' => $ItemID,
				'ItemParameterID' => $key,
				'ParameterValue' => $value,
				'EParameterValue' => $value,
				'HParameterValue' => $value,
				'UserID' => $this->session->userdata('username'),
				'TransDate' => date('Y-m-d H:i:s'),
			);
			$res =  $this->GateControl_model->addFinalQCDB($data2);
		}
		if ($res == true) {
			$fQCSlip = $_FILES['fQCSlip']['name'];
			$fQCSlip_tmp = $_FILES['fQCSlip']['tmp_name'];
			if (!is_dir('uploads/QC/' . $GateINID)) {
				mkdir('uploads/QC/' . $GateINID, 0777, TRUE);
				move_uploaded_file($fQCSlip_tmp, "uploads/QC/" . $GateINID . "/CQC-" . $GateINID . '.png');
			} else {
				move_uploaded_file($fQCSlip_tmp, "uploads/QC/" . $GateINID . "/CQC-" . $GateINID . '.png');
			}
			if ($BookingType == 'P') {
				// Calculate Deduction Amount and store in database - use for only Kirti Purchase
				$AccountDetails = $this->db->select('AccountID,vat,CustomerType')->get_where(db_prefix() . 'clients', array('AccountID' => $AccountID))->row();
				$AccountType = $AccountDetails->CustomerType;
				$GateControlDetails = $this->db->select('*')->get_where(db_prefix() . 'GateMaster', array('BookingID' => $BookingID, 'Gate_in_ID' => $GateINID))->row();
				$ActualWeight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
				$AsnWeight = $GateControlDetails->Asn_WT_MT;
				if ($ActualWeight <= $AsnWeight) {
					$Netweight = $ActualWeight;
				} else {
					$Netweight = $AsnWeight * 10;
				}
				//$Netweight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
				$booking_rate = $GateControlDetails->basic_rate;
				$purch_amt = $Netweight * $GateControlDetails->basic_rate;
				$FQC = $this->GateControl_model->GetFinalQC($BookingID, $GateINID);
				$DeductionMatrix = $this->GateControl_model->GetDeductionMatrix($GateControlDetails->ItemID);
				$total_deduction = 0;
				/*echo $Netweight;
					die;*/
				foreach ($FQC as $value) {
					$parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($GateControlDetails->ItemID, $value["ItemParameterID"]);
					$parameterValueToCheck = $value['ParameterValue'];
					if ($value["ItemParameterID"] == "2") {
						//Calculate by amount
						$damageAmtPer_qtls = 0;
						foreach ($parameterDeductionMatrix as $innerValue) {
							if ($parameterValueToCheck == $innerValue['Value']) {
								$damageAmtPer_qtls = $innerValue['Deduction'];
							}
						}
						$deductionAmt = $Netweight * $damageAmtPer_qtls;
					} else {
						//Calculate by percent
						// min value
						$minVal = floor($value['ParameterValue']);
						$minPer = 0;
						// Max Value
						$maxVal = ceil($value['ParameterValue']);
						$maxPer = 0;
						foreach ($parameterDeductionMatrix as $innerValue) {
							if ($minVal == $innerValue['Value']) {
								$minPer = $innerValue['Deduction'];
							} elseif ($maxVal == $innerValue['Value']) {
								$maxPer = $innerValue['Deduction'];
							}
						}
						$diff = $maxPer - $minPer;
						$valDeff = $parameterValueToCheck - $minVal;
						$finalPer = $minPer + ($valDeff * $diff);
						$deductionAmt = $purch_amt * ($finalPer / 100);
					}
					$this->db->where('BookingID', $BookingID);
					$this->db->where('Gate_in_ID', $GateINID);
					$this->db->where('TType', "F");
					$this->db->where('ItemID', $GateControlDetails->ItemID);
					$this->db->where('ItemParameterID', $value["ItemParameterID"]);
					$this->db->set('deductionAmt', $deductionAmt);
					$this->db->update('tblQCParameterValues');
				}
				if ($BookingType == 'P' && $AccountType == '1' && $QCApproval == "2") {
					$GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);
					$title = "QC Approve";
					$screen = "1";
					$body = " Please approve QC against BookingID : " . $BookingID . " and GateInID : " . $GateINID . "";
					$booking_id = $BookingID;
					$to = $GetBookingDetails->fcm_token;
					$this->send_notification($title, $screen, $body, $booking_id, $to);
				}
			} // end kirti purchase quality parameter deduction code
			$this->db->where('BookingID', $BookingID);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->set('FinalQCSlip', "CQC-" . $GateINID . '.png');
			if ($AccountType == "1") {
				if ($QCApproval == "1") {
					$this->db->set('QCApprove', "Y");
				}
			} else {
				$this->db->set('QCApprove', "Y");
			}
			if ($this->db->update('tblGateMaster')) {
				set_alert('success', "Final QC update successfully");
			} else {
				set_alert('warning', "something went wrong");
			}
		} else {
			set_alert('warning', "something went wrong");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	//================== Inward Rejected ===========================================
	public function RejecteInward()
	{
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		$rejection_reason = $data['rejection_reason'];
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->set('status', '18');
		$this->db->set('rejection_reason', $rejection_reason);
		$result = $this->db->update('tblGateMaster');
		if ($result == true) {
			set_alert('success', "Inward Rejected Successfully");
		} else {
			set_alert('warning', "Something Went Wrong, Please try Again");
		}
		echo json_encode($result);
	}
	//================== Edit Rate ===========================================
	public function EditGateControlRate()
	{
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		$ID = $data['id'];
		$Rate = $data['Rate'];
		$TotalRate = $Rate / 10;
		$Ratedata = array(
			'basic_rate' => $TotalRate,
			'final_rate' => $TotalRate,
		);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->where('id', $ID);
		$result = $this->db->update('tblGateMaster', $Ratedata);
		if ($result == true) {
			set_alert('success', "Rate Updated Successfully");
		} else {
			set_alert('warning', "Something Went Wrong, Please try Again");
		}
		echo json_encode($result);
	}
	//====================== Trade Rate Change =====================================
	public function TradeRateChange()
	{
		$data = $this->input->post();
		$GateINID = $data['RChangeGateINID'];
		$NewTradeRate = ($data['NewTradeRate'] / 10); // Rate Convert to per quintal
		$BookingID = $data['RChangeBookingID'];
		$rate_change_reason = $data['rate_change_reason'];
		$OldRate = $data['OldRate'];
		$CustType = $data['CustType'];
		$ItemID = $data['RateChangeItemID'];
		$taxrate = $data['taxrate'];
		$RCGrossWeight = $data['RCGrossWeight'];
		$RCTareWeight = $data['RCTareWeight'];
		$RCASNWeight = $data['RCASNWeight'];
		//Update Lead Master table
		$this->db->where('BookingID', $BookingID);
		$this->db->set('basic_rate', $NewTradeRate);
		$result = $this->db->update('tbllead_master');
		if ($result == true) {
			// Create Rate history
			$rate_history = array(
				"TransDate" => date('Y-m-d H:i:s'),
				"BookingID" => $BookingID,
				"OldRate" => $OldRate,
				"NewRate" => $NewTradeRate,
				"Reason" => $rate_change_reason,
				"UserID" => $this->session->userdata('username')
			);
			$this->db->insert('tblRateChangeHistory', $rate_history);
			// Update Rate in Gate Master
			$this->db->where('BookingID', $BookingID);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->set('basic_rate', $NewTradeRate);
			$result = $this->db->update('tblGateMaster');
			// Update Qc Deduction As per new rate
			//echo "<pre>";
			// Gate Stack List
			$StackList = $this->GateControl_model->GetGateINIDStackList($BookingID, $GateINID);
			$ItemWiseQCParameterList = $this->GateControl_model->GetItemWiseQCParameterList($ItemID);
			$FQC = $this->GateControl_model->GetFinalQC($BookingID, $GateINID);
			$TotalDeduction = 0;
			//print_r($StackList);
			foreach ($StackList as $key => $val) {
				$purch_amt = $val['Weight'] * $data['NewTradeRate'];
				$Netweight = ($val['Weight'] * 10); // Weight in quintals
				$GetQcMinMax = $this->GateControl_model->GetQcMinMax($ItemID);
				foreach ($FQC as $QcKey => $QcVal) {
					if ($QcVal["layer_number"] == $val["QCID"]) {
						$parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($ItemID, $QcVal["ItemParameterID"]);
						$parameterValueToCheck = $QcVal["HParameterValue"];
						// min value
						$minVal = floor($QcVal["HParameterValue"]);
						// Max Value
						$maxVal = ceil($QcVal["HParameterValue"]);
						$BaseValue = 2;
						//echo $QcVal["ItemParameterID"];
						//print_r($GetQcMinMax);
						foreach ($GetQcMinMax as $k => $v) {
							if ($v["ItemParameterID"] == $QcVal["ItemParameterID"] && $QcVal["layer_number"] == $val["QCID"]) {
								$BaseValue = $v["BaseValue"];
							}
						}
						$deductionAmt = 0;
						if ($QcVal["ItemParameterID"] == "2") {
							//Calculate by amount
							if ($parameterValueToCheck <= $BaseValue) {
								$deductionAmt = 0;
							} else {
								$deductionAmt = 0;
								$minPer = 0;
								$maxPer = 0;
								foreach ($parameterDeductionMatrix as $innerValue) {
									if ($minVal == $innerValue['Value']) {
										$minPer = $innerValue['Deduction'];
									} elseif ($maxVal == $innerValue['Value']) {
										$maxPer = $innerValue['Deduction'];
									}
								}
								$diff = $parameterValueToCheck - $minVal;
								$point_deductionAmtPer_qtls = 12 * $diff;
								$deductionAmt = $Netweight * $minPer;
								$deductionAmt2 = $Netweight * $point_deductionAmtPer_qtls;
								$deductionAmt += $deductionAmt2;
							}
						} else {
							//Calculate by percent
							$minPer = 0;
							$maxPer = 0;
							foreach ($parameterDeductionMatrix as $innerValue) {
								if ($minVal == $innerValue['Value']) {
									$minPer = $innerValue['Deduction'];
								} elseif ($maxVal == $innerValue['Value']) {
									$maxPer = $innerValue['Deduction'];
								}
							}
							//print_r($parameterDeductionMatrix);
							$diff = $maxPer - $minPer;
							if ($parameterValueToCheck <= $BaseValue) {
								$valDeff = 0;
								$deductionAmt = 0;
							} else {
								$valDeff = $parameterValueToCheck - $minVal;
								$finalPer = $minPer + ($valDeff * $diff);
								$deductionAmt = $purch_amt * ($finalPer / 100);
							}
							//echo "PurchAmt : ".$purch_amt." Amt :".$deductionAmt." QCID : ".$val["QCID"]." ItemPARA : ".$QcVal["ItemParameterID"]." BaseValue : ".$finalPer;
							//echo "<br>";
							//echo $purch_amt;
							//echo "<br>";
						}
						$data2 = array(
							'deductionAmt' => $deductionAmt,
						);
						$TotalDeduction += $deductionAmt;
						$this->db->where('tblQCParameterValues.BookingID', $BookingID);
						$this->db->where('tblQCParameterValues.Gate_in_ID', $GateINID);
						$this->db->where('tblQCParameterValues.layer_number', $val["QCID"]);
						$this->db->where('tblQCParameterValues.ItemParameterID', $QcVal["ItemParameterID"]);
						$this->db->where('tblQCParameterValues.TType', "F");
						$this->db->update(db_prefix() . 'QCParameterValues', $data2);
					}
				}
			}
			//die;
			$ActualOtherDeductionList = $this->GateControl_model->GetActualOtherDeductionList($BookingID, $GateINID);
			foreach ($ActualOtherDeductionList as $ADKey => $ADVal) {
				$TotalDeduction += $ADVal["Amount"];
			}
			if ($CustType == "1") {
				$taxrate = 0;
				$PurchaseWeight = ($RCGrossWeight - $RCTareWeight) / 10;
				$WeightShortInKg = 0;
			} else {
				$PurchaseWeight = $RCASNWeight;
				$actWt = ($RCGrossWeight - $RCTareWeight) / 10;
				if ($PurchaseWeight > $actWt) {
					$WeightShortInKg = ($PurchaseWeight - (($RCGrossWeight - $RCTareWeight) / 10)) * 1000;
				} else {
					$WeightShortInKg = 0;
				}
			}
			$PurchaseValue = $PurchaseWeight * $data['NewTradeRate'];
			$GstAmt = $PurchaseValue * ($taxrate / 100);
			$NetPurchaseAmt = $PurchaseValue + $GstAmt;
			$NetWeight_MT = $PurchaseWeight - ($BagWeight / 1000) - ($WeightShortInKg / 1000);
			if ($PurchaseWeight <= $NetWeight_MT) {
				$ActualInwardWeightMT = $PurchaseWeight;
			} else {
				$ActualInwardWeightMT = $NetWeight_MT;
			}
			$Finalrate = ($PurchaseValue - $TotalDeduction) / $ActualInwardWeightMT;
			$NetValue = $Finalrate * $ActualInwardWeightMT;
			//update finala rate in gate Master
			$GateControl = array(
				"PaymentAmt"  => $NetValue,
				"final_rate"  => $Finalrate,
			);
			$this->db->where('tblGateMaster.Gate_in_ID', $GateINID);
			$this->db->update(db_prefix() . 'GateMaster', $GateControl);
			// Add Account Ledger Entry
			$ledger_result = $this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID, $GateINID, $NetValue);
			//Update history table
			$history_update = array(
				"final_rate" => $Finalrate,
				'Cases' => $ActualInwardWeightMT,
				'OrderQty' => $ActualInwardWeightMT,
				'BilledQty' => $ActualInwardWeightMT,
			);
			if ($CustType == "1") {
				$history_update["PurchRate"] = $Finalrate;
				$history_update["BasicRate"] = $Finalrate;
				$history_update["SaleRate"] = ($Finalrate + ($Finalrate * $data['taxrate']) / 100);
				$history_update["OrderAmt"] = $NetValue;
				$history_update["ChallanAmt"] = $NetValue;
				$history_update["NetOrderAmt"] = $NetValue;
				$history_update["NetChallanAmt"] = $NetValue;
				// update PurchaseMaster Table
				$PO_Array = array(
					"Purchamt" => $NetValue,
					"Invamt" => $NetValue
				);
				$this->db->where('TransID', $GateINID);
				$this->db->update('tblpurchasemaster', $PO_Array);
			}
			// Update final rate in history table
			$this->db->where('OrderID', $GateINID);
			$this->db->where('BillID', $BookingID);
			$this->db->update('tblhistory', $history_update);
			set_alert('success', "Rate Updated Successfully");
		} else {
			set_alert('warning', "Something Went Wrong, Please try Again");
		}
		echo json_encode($result);
	}
	//================== Move Inward to Gross Weight ===============================
	public function MoveInwardToGrossWeight()
	{
		$data = $this->input->post();
		$GateINID = $data['RGateINID'];
		$RBookingID = $data['RBookingID'];
		$Rid = $data['Rid'];
		$update_array = array(
			"status" => 3,
			"weigh_bridge_slip_no" => NULL,
			"LoadedWeight" => NULL,
			"VhlTopImage" => NULL,
			"VhlFrontImage" => NULL,
			"VHLSideImage" => NULL,
			"LWUserID" => NULL,
			"LWTransDate" => NULL,
			"no_of_layers" => 0,
			"TareWeight" => NULL,
			"TWVhlTopImage" => NULL,
			"TWVhlFrontImage" => NULL,
			"TWVHLSideImage" => NULL,
			"TWUserID" => NULL,
			"TWTransDate" => NULL,
			"QCApprove" => "NA",
			"IsQcUpdate" => "NA",
			"IsHoUpdate" => "NA",
			"payment_done" => 0,
			"payment_approved_date" => NULL,
			"payment_approved_by" => NULL,
			"gate_out_date" => NULL,
			"gate_out_by" => NULL,
			"exit_date" => NULL,
			"exit_by" => NULL,
		);
		$this->db->where('BookingID', $RBookingID);
		$this->db->where('id', $Rid);
		$this->db->where('Gate_in_ID', $GateINID);
		$result = $this->db->update('tblGateMaster', $update_array);
		if ($result == true) {
			//Delete QC Records against GateIn ID
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->where('BookingID', $RBookingID);
			$this->db->where('TType', "F");
			$this->db->delete('tblQCParameterValues');
			// Delete Inventory record
			$this->db->where('GateINID', $GateINID);
			$this->db->where('BookingID', $RBookingID);
			$this->db->delete('tblstockInventory');
			// Delete Item History table
			$this->db->where('OrderID', $GateINID);
			$this->db->where('BillID', $RBookingID);
			$this->db->delete('tblhistory');
			// Get Purchase Order Number
			$this->db->select('tblpurchasemaster.*');
			$this->db->where('tblpurchasemaster.TransID', $GateINID);
			$PurchaseMaster =  $this->db->get('tblpurchasemaster')->row();
			$PurchID = $PurchaseMaster->PurchID;
			// Delete tblpurchasemaster Order
			$this->db->where('TransID', $GateINID);
			$this->db->delete('tblpurchasemaster');
			// Delete Ledger Record
			$this->db->where('VoucherID', $PurchID);
			$this->db->delete('tblaccountledger');
			// Delete PcSoft reference number
			$this->db->where('GIC_Reference', $GateINID);
			$this->db->delete('tblpcsoft_gic_number_referance');
			set_alert('success', "Inward Moved to Gross Weight stage Successfully");
		} else {
			set_alert('warning', "Something Went Wrong, Please try Again");
		}
		echo json_encode($result);
	}
	//======================= Generete Payment Voucher =============================
	public function GeneratePaymentVoucher()
	{
		if (!has_permission_new('AdvancePayment', '', 'create')) {
			access_denied('Booking settlement');
		}
		$data = $this->input->post();
		$PVpayment_mode = $data['PVpayment_mode'];
		$PVpayment_date = $data['PVpayment_date'];
		$PVpayment_amt = $data['PVpayment_amt'];
		$PVutr_no = $data['PVutr_no'];
		$PVnarration = $data['PVnarration'];
		$PVShortCode = $data['PVShortCode'];
		$PVAccountID = $data['PVAccountID'];
		$PVPartyID = $data['PVPartyID'];
		$PVCenterID = $data['PVCenterID'];
		$PVItemID = $data['PVItemID'];
		$BookingID = $data['BookingID'];
		$GateINID = $data['GateINID'];
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$payment_date = to_sql_date($data['PVpayment_date']) . " " . date('H:i:s');
		$date = to_sql_date($data['PVpayment_date']);
		$get_result_to_cur_date = $this->get_result_to_cur_date_payments($date);
		$GetLastUniqueNo = $this->GetLastUniqueNo($date);
		$LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
		if (empty($get_result_to_cur_date)) {
			if ($selected_company == 1) {
				$new_tax_transactionNumber = get_option('next_payment_number_for_kirti');
			}
			$new_voucher_number = $new_tax_transactionNumber;
		} else {
			$count = count($get_result_to_cur_date);
			$last_index = $count - 1;
			$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
			$incNo = (int) $new_voucher_number - 1;
			$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "' . $incNo . '" AND PassedFrom = "PAYMENTS" AND FY = "' . $fy . '" AND PlantID = ' . $selected_company;
			$this->db->query($sql);
			if ($this->db->affected_rows() > 0) {
				$this->increment_next_payment_number();
			}
		}
		if (empty($get_result_to_cur_date)) {
			$this->increment_next_payment_number();
		}
		// Insert Ledger Entry
		$credit_data = array(
			"PlantID" => $selected_company,
			"Transdate" => $payment_date,
			"TransDate2" => date('Y-m-d H:i:s'),
			"VoucherID" => $new_voucher_number,
			"AccountID" => $PVAccountID,
			"TType" => "D",
			"CenterID" => $PVCenterID,
			"CommodityID" => $PVItemID,
			"EntryFor" => 2,
			"PartyID" => $PVPartyID,
			"Amount" => $PVpayment_amt,
			"Narration" => $PVnarration,
			"ref_no" => $PVutr_no,
			"ref_no" => $PVutr_no,
			"CounterAccount" => $PVpayment_mode,
			"PassedFrom" => "PAYMENTS",
			"OrdinalNo" => $i,
			"UserID" => $this->session->userdata('username'),
			"FY" => $fy,
			"UniquID" => $LastUniqueID,
		);
		if ($this->db->insert(db_prefix() . 'accountledger', $credit_data)) {
			$debit_data = array(
				"PlantID" => $selected_company,
				"Transdate" => $payment_date,
				"TransDate2" => date('Y-m-d H:i:s'),
				"VoucherID" => $new_voucher_number,
				"AccountID" => $PVpayment_mode,
				"CounterAccount" => $PVAccountID,
				"TType" => "C",
				"CenterID" => $PVCenterID,
				"CommodityID" => $PVItemID,
				"EntryFor" => 2,
				"PartyID" => $PVPartyID,
				"Amount" => $PVpayment_amt,
				"Narration" => $PVnarration,
				"ref_no" => $PVutr_no,
				"PassedFrom" => "PAYMENTS",
				"OrdinalNo" => $i,
				"UserID" => $this->session->userdata('username'),
				"FY" => $fy,
				"UniquID" => $LastUniqueID,
			);
			if ($this->db->insert(db_prefix() . 'accountledger', $debit_data)) {
				//Add Payment Voucher
				$advcancepayment = array(
					'BookingID' => $BookingID,
					'GateINID' => $GateINID,
					'PartyID' => $PVPartyID,
					'AccountID' => $PVAccountID,
					'CenterID' => $PVCenterID,
					'EffectOn' => $PVpayment_mode,
					'VoucherNo' => $new_voucher_number,
					'Amount' => $PVpayment_amt,
					'TransDate' => $date,
					'UtrNo' => $PVutr_no,
					'Narration' => $PVnarration,
					'UserID' => $this->session->userdata('username')
				);
				if ($this->db->insert(db_prefix() . 'AdvancePayment', $advcancepayment)) {
					$inserted_id = $this->db->insert_id();
					// Send Data to PcSoft
					$data_pc_soft_array =  array(
						"PartyID" => $PVPartyID,
						"VoucherNo" => $new_voucher_number,
						"payment_amt" => $PVpayment_amt,
						"Narration" => $PVnarration,
						"FromAccount" => $PVpayment_mode,
						"ToAccount" => $PVShortCode,
						"utr_no" => $PVutr_no,
						"PaymentDateTime" => $date,
						"access_tokan" => "fe3fd1f94239c467727c5cae504d4fdd",
					);
					$pc_soft_data = json_encode($data_pc_soft_array);
					$url = "https://app.ieverp.com/TRIP/API/bank/CashVchrFGIC"; // Test Url
					$curl = curl_init();
					curl_setopt_array(
						$curl,
						array(
							CURLOPT_URL => $url,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 30,
							CURLOPT_CUSTOMREQUEST => "POST",
							CURLOPT_POSTFIELDS => $pc_soft_data,
							CURLOPT_HTTPHEADER => array(
								"content-type: application/json"
							),
						)
					);
					$response = curl_exec($curl);
					$response_array = json_decode($response);
					echo "<pre>";
					print_r($response_array);
					die;
					$PcSoft_po = $response_array->doc_ref_number;
					$status = $response_array->Status;
					if ($status == true) {
						$updatestatus = array(
							'Is_PcsoftData' => 'Y',
						);
						$this->db->where('id', $inserted_id);
						$this->db->update('tblAdvancePayment', $updatestatus);
					}
					$err = curl_error($curl);
					curl_close($curl);
					set_alert('success', "Advance Payment Voucher Generate Successfully");
					echo json_encode(true);
				}
			}
		} else {
			set_alert('warning', "Error to generate Advance Payment Voucher");
			return false;
		}
	}
	public function increment_next_payment_number()
	{
		// Update next CHALLAN number in settings
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if ($selected_company == 1) {
			$this->db->where('name', 'next_payment_number_for_kirti');
		}
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	public function get_result_to_cur_date_payments($payment_date)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$fy_ne = $fy + 1;
		$las_date_fy = '20' . $fy_ne . '-03-31 23:59:59';
		$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = ' . $selected_company . ' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "' . $fy . '" AND Transdate BETWEEN "' . $payment_date . ' H:i:s" AND "' . $las_date_fy . '" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
		$staff_data = $this->db->query($sql)->result_array();
		return $staff_data;
	}
	public function GetLastUniqueNo()
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = ' . $selected_company . ' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "' . $fy . '"  GROUP BY UniquID ORDER BY abs(tblaccountledger.UniquID) DESC ';
		$UniqueID = $this->db->query($sql)->result_array();
		return $UniqueID;
	}
	// Continue Center QC same as RO QC
	public function continue_same_Qc()
	{
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		unset($data['GateINID']);
		unset($data['BookingID']);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->set('IsQcUpdate', 'Y');
		$this->db->set('status', '14');
		$result = $this->db->update('tblGateMaster');
		if ($result == true) {
			set_alert('success', "RO QC update successfully");
		} else {
			set_alert('warning', "something went wrong");
		}
		echo json_encode($result);
	}
	// Continue RO QC same as HO QC
	public function continue_same_ROQc()
	{
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		$HOQcApproval = $data['HOQcApproval'];
		unset($data['GateINID']);
		unset($data['BookingID']);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->set('status', '15');
		if ($HOQcApproval == "Y") {
			$this->db->set('IsHoUpdate', $HOQcApproval);
		}
		$result = $this->db->update('tblGateMaster');
		if ($result == true) {
			set_alert('success', "HO QC update successfully");
			$UpdateStatusCreditLimit = array(
				'Status' => 'N',
			);
			$this->db->where('GateINID', $GateINID);
			$this->db->update('tblCreditLimitMaster', $UpdateStatusCreditLimit);
			if ($GateControlDetails->TType == "S") {
				$this->GateControl_model->GenerateLedgerEntryForSale($BookingID, $GateINID);
			} else if ($GateControlDetails->TType == "P") {
				$purchasedetails = $this->GateControl_model->fetch_purchase_details($GateINID);
				$NetAmt = $purchasedetails->Invamt ?? 0;
				$this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID, $GateINID, $NetAmt);
			}
		} else {
			set_alert('warning', "something went wrong");
		}
		echo json_encode($result);
	}
	// Add other deduction for kirti Purchase
	public function Add_Other_Deduction()
	{
		$data = $this->input->post();
		$id = $data["id"];
		$res =  $this->GateControl_model->Add_Other_Deduction($data, $id);
		if ($res > 0) {
			set_alert('success', "Other Deduction Added Successfully");
		} else {
			set_alert('warning', "Other Deduction Not Added, please try again later");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	// Update Center QC and deduction amount
	public function updateFinalQC()
	{
		$data = $this->input->post();
		$id = $data['id'];
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		$ItemID = $data['ItemID'];
		$AccountID = $data['AccountID'];
		$BookingType = $data['BookingType'];
		$GrossWeight = $data['GrossWeight'];
		$TareWeight = $data['TareWeight'];
		$QCApproval = $data['QCApproval'];
		$QC_for = $data['QC_for'];
		unset($data['BookingType']);
		unset($data['BookingID']);
		unset($data['ItemID']);
		unset($data['id']);
		unset($data['AccountID']);
		unset($data['GateINID']);
		unset($data['GrossWeight']);
		unset($data['TareWeight']);
		unset($data['QCApproval']);
		$res =  $this->GateControl_model->updateFinalQCDB($data, $GateINID, $BookingID);
		if ($res == true) {
			$fQCSlip = $_FILES['fQCSlip']['name'];
			$fQCSlip_tmp = $_FILES['fQCSlip']['tmp_name'];
			if (!is_dir('uploads/QC/' . $GateINID)) {
				mkdir('uploads/QC/' . $GateINID, 0777, TRUE);
				move_uploaded_file($fQCSlip_tmp, "uploads/QC/" . $GateINID . "/CQC-" . $GateINID . '.png');
			} else {
				move_uploaded_file($fQCSlip_tmp, "uploads/QC/" . $GateINID . "/CQC-" . $GateINID . '.png');
			}
			// QC Parameter wise deduction amount store in database i.e. only for Kirti Purchase
			if ($BookingType == 'P') {
				$AccountDetails = $this->db->select('AccountID,vat,CustomerType')->get_where(db_prefix() . 'clients', array('AccountID' => $AccountID))->row();
				$AccountType = $AccountDetails->CustomerType;
				$GateControlDetails = $this->db->select('*')->get_where(db_prefix() . 'GateMaster', array('BookingID' => $BookingID, 'Gate_in_ID' => $GateINID))->row();
				$ActualWeight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
				$AsnWeight = $GateControlDetails->Asn_WT_MT;
				if ($ActualWeight <= $AsnWeight) {
					$Netweight = $ActualWeight;
				} else {
					$Netweight = $AsnWeight * 10;
				}
				$purch_amt = $Netweight * $GateControlDetails->basic_rate;
				$booking_rate = $GateControlDetails->basic_rate;
				$FQC = $this->GateControl_model->GetFinalQC($BookingID, $GateINID);
				$DeductionMatrix = $this->GateControl_model->GetDeductionMatrix($GateControlDetails->ItemID);
				$GetQcMinMax = $this->GateControl_model->GetQcMinMax($GateControlDetails->ItemID);
				$deductionAmt = 0;
				foreach ($FQC as $value) {
					$parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($GateControlDetails->ItemID, $value["ItemParameterID"]);
					if ($QC_for == "Center") {
						$parameterValueToCheck = $value['ParameterValue'];
						// min value
						$minVal = floor($value['ParameterValue']);
						// Max Value
						$maxVal = ceil($value['ParameterValue']);
					} elseif ($QC_for == "RO") {
						$parameterValueToCheck = $value['EParameterValue'];
						// min value
						$minVal = floor($value['EParameterValue']);
						// Max Value
						$maxVal = ceil($value['EParameterValue']);
					} elseif ($QC_for == "HO") {
						$parameterValueToCheck = $value['HParameterValue'];
						// min value
						$minVal = floor($value['HParameterValue']);
						// Max Value
						$maxVal = ceil($value['HParameterValue']);
					}
					$BaseValue = 2;
					foreach ($GetQcMinMax as $k => $v) {
						if ($v["ItemParameterID"] == $value["ItemParameterID"]) {
							$BaseValue = $v["BaseValue"];
						}
					}
					if ($value["ItemParameterID"] == "2") {
						//Calculate by amount
						if ($parameterValueToCheck <= $BaseValue) {
							$deductionAmt = 0;
						} else {
							$damageAmtPer_qtls = 0;
							foreach ($parameterDeductionMatrix as $innerValue) {
								if ($parameterValueToCheck == $innerValue['Value']) {
									$damageAmtPer_qtls = $innerValue['Deduction'];
								}
							}
							$deductionAmt = $Netweight * $damageAmtPer_qtls;
						}
					} else {
						//Calculate by percent
						$minPer = 0;
						$maxPer = 0;
						foreach ($parameterDeductionMatrix as $innerValue) {
							if ($minVal == $innerValue['Value']) {
								$minPer = $innerValue['Deduction'];
							} elseif ($maxVal == $innerValue['Value']) {
								$maxPer = $innerValue['Deduction'];
							}
						}
						$diff = $maxPer - $minPer;
						if ($parameterValueToCheck <= $BaseValue) {
							$valDeff = 0;
							$deductionAmt = 0;
						} else {
							$valDeff = $parameterValueToCheck - $minVal;
							$finalPer = $minPer + ($valDeff * $diff);
							$deductionAmt = $purch_amt * ($finalPer / 100);
						}
					}
					$this->db->where('BookingID', $BookingID);
					$this->db->where('Gate_in_ID', $GateINID);
					$this->db->where('TType', "F");
					$this->db->where('ItemID', $GateControlDetails->ItemID);
					$this->db->where('ItemParameterID', $value["ItemParameterID"]);
					$this->db->set('deductionAmt', $deductionAmt);
					$this->db->update('tblQCParameterValues');
				}
				if ($BookingType == 'P' && $QCApproval == "2") {
					$GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);
					$title = "QC Updated";
					$screen = "1";
					$body = " Please approve QC against BookingID : " . $BookingID . " and GateInID : " . $GateINID . "";
					$booking_id = $BookingID;
					$to = $GetBookingDetails->fcm_token;
					$this->send_notification($title, $screen, $body, $booking_id, $to);
				}
			}
			$this->db->where('BookingID', $BookingID);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->set('FinalQCSlip', "CQC-" . $GateINID . '.png');
			if ($QCApproval == "1") {
				$QcStatas = "Y";
			} else {
				$QcStatas = "NA";
			}
			if ($QC_for == "Center") {
				$this->db->set('QCApprove', $QcStatas);
				set_alert('success', "Center QC update successfully");
			} elseif ($QC_for == "RO") {
				$this->db->set('IsQcUpdate', $QcStatas);
				$this->db->set('status', "14");
				set_alert('success', "RO QC update successfully");
			} elseif ($QC_for == "HO") {
				$this->db->set('IsHoUpdate', $QcStatas);
				$this->db->set('status', "15");
				set_alert('success', "HO QC update successfully");
			}
			if ($this->db->update('tblGateMaster')) {
			} else {
				set_alert('warning', "something went wrong");
			}
		} else {
			set_alert('warning', "something went wrong");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	public function updateFinalQCWithdrawal()
	{
		$data = $this->input->post();
		$id = $data['id'];
		unset($data['id']);
		foreach ($data as $key => $value) {
			$data2 = array(
				'id' => $key,
				'ParameterValue' => $value,
				'UserID2' => $this->session->userdata('username'),
				'Lupdate' => date('Y-m-d H:i:s'),
			);
			$res =  $this->GateControl_model->updateFinalQCWithdrawalDB($data2);
		}
		if ($res == true) {
			header('Location:' . admin_url() . 'GateControl/GateControl_Reports_Details/' . $id);
		}
	}
	public function viewQc($BookingID, $GateINID)
	{
		$flag = 2;
		$this->data['GateDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateINID, $flag);
		$this->data['QcDetails'] = $this->GateControl_model->getSingleFinalQc($BookingID, $GateINID);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		$this->load->library('qc_pdf');
		$this->load->view('qcslip/qcslip_pdf', $this->data);
	}
	public function UpdatePaymentAdvice()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		unset($data['GateINID']);
		$data['modifyFlag'] = 'Y';
		$data['status'] = '13';
		$result = $this->GateControl_model->UpdatePaymentAdvice($data, $GateINID);
		if ($result == true) {
			set_alert('success', "Payment Advice send successfully for approval ");
		} else {
			set_alert('warning', "something went wrong ");
		}
		echo json_encode($result);
	}
	public function ApprovePaymentAdvice()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$data = $this->input->post();
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		$CustomerType = $data['CustomerType'];
		$NetAmt = $data['NetAmt'];
		$GateControl = array(
			"PaymentAmt"  => $data['PaymentAmt'],
			"PaymentPer"  => $data['PaymentPer'],
			"final_rate"  => $data['final_rate'],
			"status"  => '16',
		);
		unset($data['GateINID']);
		$data['modifyFlag'] = 'Y';
		$data['status'] = '16';
		$result = $this->GateControl_model->ApprovePaymentAdvice($GateINID, $GateControl);
		if ($result == true) {
			$history_update = array(
				"final_rate" => $data['final_rate'],
				'Cases' => $data['ActualWeight'],
				'OrderQty' => $data['ActualWeight'],
				'BilledQty' => $data['ActualWeight'],
			);
			// Add Account Ledger Entry
			$ledger_result = $this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID, $GateINID, $NetAmt);
			if ($CustomerType != '1') {
				// ganerate debit note for other than farmer
				$this->GateControl_model->GenerateDebitNote($BookingID, $GateINID);
			} else {
				$history_update["PurchRate"] = $data['final_rate'];
				$history_update["BasicRate"] = $data['final_rate'];
				$history_update["SaleRate"] = ($data['final_rate'] + ($data['final_rate'] * $data['taxrate']) / 100);
				$history_update["OrderAmt"] = $data['NetAmt'];
				$history_update["ChallanAmt"] = $data['NetAmt'];
				$history_update["NetOrderAmt"] = $data['NetAmt'];
				$history_update["NetChallanAmt"] = $data['NetAmt'];
				// update PurchaseMaster Table
				$PO_Array = array(
					"Purchamt" => $data['NetAmt'],
					"Invamt" => $data['NetAmt']
				);
				$this->db->where('TransID', $GateINID);
				$this->db->update('tblpurchasemaster', $PO_Array);
			}
			// Update final rate in history table
			$this->db->where('OrderID', $GateINID);
			$this->db->where('BillID', $BookingID);
			$this->db->update('tblhistory', $history_update);
			set_alert('success', "Payment Advice Approve Successfully ");
		} else {
			set_alert('warning', "something went wrong ");
		}
		echo json_encode($result);
	}
	public function viewPayment($BookingID, $GateIN)
	{
		$this->load->library('payment_pdf');
		$flag = 2;
		$this->data['ActualOtherDeductionList'] = $this->GateControl_model->GetActualOtherDeductionList($BookingID, $GateIN);
		$this->data['DebitNoteItem'] = $this->GateControl_model->GetDebitNoteItemList();
		$this->data['PaymentDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateIN, $flag);
		$this->data['UnloadingDetails'] = $this->GateControl_model->GetUnloadingDetails($BookingID, $GateIN);
		$this->data['PlantDetails'] = $this->GateControl_model->CompanyDetails($this->data['PaymentDetails'][0]['PartyID']);
		$this->data['QCStackDetails'] = $this->GateControl_model->GetStackListAgainstInward($BookingID, $GateIN);
		$this->data['PurchaseDetails'] = $this->GateControl_model->getPurchaseMasterDetails($GateIN);
		$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
		/*echo "<pre>";
				print_r($this->data);
			die;*/
		$this->load->view('paymentslip/paymentslip_pdf', $this->data);
	}
	public function viewSellPayment($BookingID, $GateIN)
	{
		$flag = 2;
		$this->load->library('app_number_to_word', [
			'clientid' => 1,
		], 'numberword');
		$this->data['PaymentDetails'] = $this->GateControl_model->getSingleGateControl($BookingID, $GateIN, $flag);
		$this->data['PartyDetails'] = $this->GateControl_model->GetPartyDetails($BookingID, $GateIN);
		$this->data['GetInvoiceItemDetails'] = $this->GateControl_model->GetInvoiceItemDetails($BookingID, $GateIN);
		$this->data['PlantDetails'] = $this->GateControl_model->CompanyDetails($this->data['PaymentDetails'][0]['PartyID']);
		$this->load->library('payment_pdf');
		$this->load->view('paymentslip/invoiceslip_pdf', $this->data);
	}
	// Not in USE
	public function approvePayment()
	{
		$id = $this->input->post('id');
		$data = array(
			'id' => $id,
			'payment_done' => $this->input->post('payment_approve'),
			'payment_approved_date' => date('Y-m-d H:i:s'),
			'payment_approved_by' => $this->session->userdata('username')
		);
		$result = $this->GateControl_model->approvePaymentDB($data);
		header("Location:" . admin_url() . "GateControl/GateControl_Reports_Details/" . $id);
	}
	public function markExit()
	{
		$id = $this->input->post('id');
		$BookingID = $this->input->post('BookingID');
		$BookingType = $this->input->post('BookingType');
		$result = $this->GateControl_model->markExitDB($BookingID, $id, $BookingType);
		if ($result == true) {
			set_alert('success', "Vehicle Exit Successfully");
		} else {
			set_alert('warning', "somthing went wrong");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	//====================== Re Send ASN Data To PcSoft ============================
	public function SendASNDataToPcSoft()
	{
		$id = $this->input->post('id');
		$GateINID = $this->input->post('GateINID');
		$BookingID = $this->input->post('BookingID');
		$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID, $GateINID);
		// Send to PC Soft
		$trinvs_array = array([
			"party_no" => $GateControlDetails->ShortCode,
			"your_ref" => $BookingID,
			"truck_no" => $GateControlDetails->VehicleNo,
			"doc_ref" => $GateControlDetails->ASNID,
			"your_date" => $GateControlDetails->asn_date,
			"doc_flnm" => NULL,
			"lr_no" => NULL,
			"lr_date" => NULL,
			"type_code" => NULL,
		]);
		$sporddtl_array = array([
			"im_code" => $GateControlDetails->PCItemID,
			"im_qty" => $GateControlDetails->Asn_WT_MT,
			"im_bag" => $GateControlDetails->quantity,
			"im_ordrate" => $GateControlDetails->basic_rate
		]);
		$data_asn_array =  array(
			"cocd" => $GateControlDetails->PartyID,
			"trinvs" => $trinvs_array,
			"sporddtl" => $sporddtl_array
		);
		$ASN_data = json_encode($data_asn_array);
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				//-> LIVE URL
				CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/ASNinsert", // Live
				//CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/ASNinsert",// -> DEV URL
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $ASN_data,
				CURLOPT_HTTPHEADER => array(
					"content-type: application/json"
				),
			)
		);
		$apiResponse = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		$response_array = json_decode($apiResponse);
		$PcSoft_GIN = $response_array->doc_ref_number;
		$status = $response_array->Status;
		if ($response_array) {
			$insert_referance = array(
				"Type" => "P",
				"Name" => "ASN",
				"GIC_Reference" => $GateControlDetails->ASNID,
				"pcsoft_doc_ref" => $PcSoft_GIN,
				"status" => $status
			);
			$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
			$msg = "ASN Data Send Successfully";
			set_alert('success', $msg);
		} else {
			$msg = $response_array->ErrorMessage;
			set_alert('warning', $msg);
		}
		/*	echo "<pre>";
		print_r($insert_referance);
		print_r($data_asn_array);
		print_r($apiResponse);
		print_r($response_array);
		die;*/
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	//===================== Send Inward Data to PCSOFT =============================
	// public function SendDataToPcSoft(){
	// 	$id = $this->input->post('id');
	// 	$GateINID = $this->input->post('GateINID');
	// 	$BookingID = $this->input->post('BookingID');
	// 	$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID,$GateINID);
	// 	$QcDetailsLotWise = $this->GateControl_model->getSingleFinalQc($BookingID,$GateINID);
	/// 	$OtherDeduction = $this->GateControl_model->GetOtherDeduction($BookingID,$GateINID);
	/// 	$StckWiseBagList = $this->GateControl_model->GetStckWiseBagList($BookingID,$GateINID);
	// 	$basicValue = $GateControlDetails->basic_rate * ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight);
	// 	$ItemWeight = ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight) / 10;
	// 	$FinalRate = ($basicValue - $Total_deduction) / ($ItemWeight * 10);
	// 	$totalBag = 0;
	// 	foreach($StckWiseBagList as $Bkey=>$Bval){
	// 		$totalBag += $Bval["BagQty"];
	// 	}
	// 	if($totalBag <= 0){
	// 	    $totalBag = 1;
	// 	}
	// 	$ASNBag = $GateControlDetails->quantity;
	// 	$ASNWT_MT = $GateControlDetails->Asn_WT_MT;
	// 	$PerBagWt = ($ItemWeight/$totalBag)*1000; // Per Bag Weight as per inward weight and inward bag
	// 	if($GateControlDetails->CustomerType == "1"){
	// 	    $vChlPerBagkWt = $PerBagWt; // Per Bag Weight as per ASN weight and ASN bag
	// 	}else{
	// 	    $vChlPerBagkWt = ($ASNWT_MT/$ASNBag)*1000; // Per Bag Weight as per ASN weight and ASN bag
	// 	}
	// 	$QCDetails = array();
	// 	$Total_deduction = 0;
	// 	foreach($QcDetailsLotWise as $key=>$val){
	// 	    if($val["BagQty"] > 0){
	// 	        $bagqty = $val["BagQty"];
	// 	    }else{
	// 	        $bagqty = 1;
	// 	    }
	// 		$Total_deduction += $val["deductionAmt"];
	// 		$AllPack = array();
	// 		$_packdtl = array(
	//         "REMARK"=>"",
	//         "PK_CD"=>"X04",
	//         "PK_FCT"=>(int)$bagqty
	// 		);
	// 		array_push($AllPack,$_packdtl);
	// 		$IM_RXDQTY = (($bagqty * $PerBagWt)/1000); // Net Weight in MT as per inward details
	// 		$IM_CHLQTY = (($bagqty * $vChlPerBagkWt)/1000); // Net Weight in MT as per ASN details
	// 		$lotDetails = array(
	//             "LotNo"=>$val["QCID"],
	//             "BagQty"=>$bagqty,
	//             "IM_RXDQTY"=>$IM_RXDQTY,
	//             "IM_CHLQTY"=>$IM_CHLQTY,
	//             "AUX_RXDQT"=>($IM_RXDQTY * 10),
	//             "AUX_CHLQT"=>($IM_CHLQTY * 10),
	//             "_packdtl"=>$AllPack,
	//             "LotWeight"=>$val["Weight"], // MT Weight
	// 		);
	// 		$QcArray = array();
	// 		foreach($val["QCDetails"] as $Qkey=>$Qval){
	// 			$Array = array(
	//                 "TESTNO"=>$Qval['pc_soft_parameter'],
	//                 "READING"=>number_format($Qval['HParameterValue'], 2),
	//                 "deduction_amt"=>$Qval['deductionAmt'],
	// 			);
	// 			array_push($QcArray,$Array);
	// 		}
	// 		$lotDetails['QCDetails'] = $QcArray;
	// 		//$QCDetails[$val['pc_soft_parameter']] = $val['HParameterValue'];
	// 		//array_push($QCDetails , array("TESTNO" => $val['pc_soft_parameter'] , "READING" => number_format($val['HParameterValue'], 2),"deduction_amt"=>$val['deductionAmt']));
	// 		array_push($QCDetails,$lotDetails);
	// 	}
	// 	$Oth_deduction = array();
	// 	foreach($OtherDeduction as $key=>$val){
	// 		$Total_deduction += $val["Amount"];
	// 		array_push($Oth_deduction , array("ItemID" => $val['PCItemID'] , "deduction_amt" => number_format($val['Amount'], 2)));
	// 	}
	// 	$inward_array = array(
	//         "COCD" =>$GateControlDetails->PartyID,
	//         "doc_ref" =>$GateControlDetails->ASNID,
	//         "GateInID" =>$GateINID,
	//         "chl_bag" =>$GateControlDetails->total_bags,
	//         "chl_katta" =>$GateControlDetails->total_katta,
	//         "TotalBag" =>$totalBag,
	//         "gross_wt" =>$GateControlDetails->LoadedWeight /10,
	//         "tare_wt" =>$GateControlDetails->TareWeight / 10,
	//         "no_of_layer" =>$GateControlDetails->total_layers,
	//         "final_rate" =>number_format(($GateControlDetails->basic_rate * 10), 2, '.', ''),
	//         "QCparameters"=>$QCDetails,
	//         "Other_deduction"=>$Oth_deduction
	// 	);
	// 	$inward_data = json_encode($inward_array);
	// 	$curl = curl_init();
	// 	curl_setopt_array($curl, array(
	//         CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/GATEENTRY/GRRSUBMIT", // --> LIVE
	//         //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/GATEENTRY/GRRSUBMIT", //--> DEV URL
	//         CURLOPT_RETURNTRANSFER => true,
	//         CURLOPT_MAXREDIRS => 10,
	//         CURLOPT_TIMEOUT => 30,
	//         CURLOPT_CUSTOMREQUEST => "POST",
	//         CURLOPT_POSTFIELDS => $inward_data,
	//         CURLOPT_HTTPHEADER => array(
	// 		    "content-type: application/json"
	// 		),
	//     )
	// 	);
	// 	$response = curl_exec($curl);
	// 	$err = curl_error($curl);
	// 	curl_close($curl);
	// 	$response_array = json_decode($response);
	// 	$PcSoft_GRN = $response_array->doc_ref_number;
	// 	$status = $response_array->Status;
	// 	if($status == true){
	// 	    $msg = $response_array->SuccessMessage;
	// 	}else{
	// 	    $msg = $response_array->ErrorMessage;
	// 	}
	// 	if($status == true){
	// 		$insert_referance = array(
	//         "Type"=>$GateControlDetails->TType,
	//         "Name"=>"GateIN",
	//         "GIC_Reference"=>$GateINID,
	//         "pcsoft_doc_ref"=>$PcSoft_GRN,
	//         "status"=>$status
	// 		);
	// 		$this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
	// 		// update status
	// 		$this->db->where('Gate_in_ID',$GateINID);
	// 		$this->db->set('status','17');
	// 		$result = $this->db->update('tblGateMaster');
	// 		set_alert('success', $msg);
	// 	}else{
	// 		/*$insert_referance = array(
	// 			"Type"=>$GateControlDetails->TType,
	// 			"Name"=>"GateIN",
	// 			"GIC_Reference"=>$GateINID,
	// 			"status"=>$status
	// 		);
	// 		$this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);*/
	// 		set_alert('warning', $msg);
	// 	}
	// 		/*echo "<pre>";
	// 		echo $inward_data;
	// 		print_r($inward_array);
	// 		echo "<br>";
	// 		echo "<br>";
	// 		print_r($response);
	// 		print_r($err);
	// 	die;*/
	// 	$redUrl = admin_url('GateControl/GateControl_Reports_Details/'.$id);
	// 	redirect($redUrl);
	// }
	public function SendDataToPcSoft()
	{
		$id         = $this->input->post('id');
		$GateINID   = $this->input->post('GateINID');
		$BookingID  = $this->input->post('BookingID');
		$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID, $GateINID);
		$QcDetailsLotWise   = $this->GateControl_model->getSingleFinalQc($BookingID, $GateINID);
		$ActualOtherDeductionList = $this->GateControl_model->GetActualOtherDeductionList($BookingID, $GateINID);
		// echo '<pre>'; print_r($GateControlDetails); echo '</pre>';
		// echo '<hr>';
		// echo '<pre>'; print_r($QcDetailsLotWise); echo '</pre>';
		// Stack Details
		$StackDetails = [];
		$warehouseID = "";
		foreach ($QcDetailsLotWise as $key => $val) {
			$bagqty = (!empty($val["BagQty"]) && $val["BagQty"] > 0) ? $val["BagQty"] : 1;
			$QCDetails = [];
			if (!empty($val["QCDetails"])) {
				foreach ($val["QCDetails"] as $Qval) {
					$QCDetails[] = [
						"QCParameterID"    => $Qval['ItemParameterID'],
						"QCParameterValue" => number_format($Qval['HParameterValue'], 2, '.', ''),
						"DeductionAmt"     => number_format($Qval['deductionAmt'], 2, '.', '')
					];
				}
			}
			$StackDetails[] = [
				"ItemID"   => $val["ItemID"] ?? "",
				"Weight"   => $val["Weight"],
				"BagQty"   => $bagqty,
				"WarehouseID" => $val["WHID"],
				"ChamberID" => $val["CHID"],
				"Stack"    => $val["StackID"],
				"LotID"    => $val["LOTID"],
				"QCDetails" => $QCDetails
			];
			$warehouseID = $val["WHID"];
		}
		$extra_charges = [];
		if (!empty($ActualOtherDeductionList)) {
			foreach ($ActualOtherDeductionList as $ADKey => $ADVal) {
				$extra_charges[] = [
					"name" => $ADVal['ItemName'],
					"amount" => $ADVal['Amount'],
				];
			}
		}
		// Main Payload
		$payload = [
			"COCD"            => $GateControlDetails->PartyID,
			"TradeID"         => $GateControlDetails->BookingID,
			"OrderID"         => $GateControlDetails->PurchID,
			"GateInID"        => $GateINID,
			'WarehouseID'     => $warehouseID,
			"gross_wt"        => $GateControlDetails->LoadedWeight / 10,
			"tare_wt"         => $GateControlDetails->TareWeight / 10,
			"gate_out_time"   => $GateControlDetails->gate_out_date ?? "",
			"gate_exit_time"  => $GateControlDetails->exit_date ?? "",
			"extra_charges"   => $extra_charges,
			"StackDetails"    => $StackDetails
		];
		// echo '<hr>';
		// echo '<pre>'; print_r($payload); echo '</pre>';
		$jsonData = json_encode($payload);
		// CURL Call
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL            => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/Inward", // update if external URL
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => $jsonData,
			CURLOPT_HTTPHEADER     => [
				"Content-Type: application/json"
			],
		]);
		$response = curl_exec($curl);
		$err      = curl_error($curl);
		curl_close($curl);
		$response_array = json_decode($response);
		// echo '<hr>';
		// echo '<pre>'; print_r($response); echo '</pre>';
		// Response Handling
		if ($response_array->status) {
			$insert_referance = [
				"Type"            => $GateControlDetails->TType,
				"Name"            => "Order",
				"GIC_Reference"   => $GateControlDetails->PurchID,
				"pcsoft_doc_ref"  => $response_array->data->OrderID ?? "",
				"status"          => 1
			];
			$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
			$insert_referance = [
				"Type"            => $GateControlDetails->TType,
				"Name"            => "Inward",
				"GIC_Reference"   => $GateControlDetails->PurchID,
				"pcsoft_doc_ref"  => $response_array->data->InwardID ?? "",
				"status"          => 1
			];
			$this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
			// update status
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->set('status', '17');
			$this->db->update('tblGateMaster');
			set_alert('success', $response_array->message ?? 'Success');
		} else {
			set_alert('warning', $response_array->message ?? 'API Error');
		}
		redirect(admin_url('GateControl/GateControl_Reports_Details/' . $id));
	}
	public function SentToStarAgri()
	{
		$id = $this->input->post('id');
		$BookingID = $this->input->post('BookingID');
		$TransID = $this->input->post('TransID');
		$Result = $this->GateControl_model->GetInwardDetail($BookingID, $id, $TransID);
		if ($Result->ChamberID != '') {
			$chamber_id = $Result->ChamberID;
		} else {
			$chamber_id = '0';
		}
		$QcDetails = $this->GateControl_model->GetQCDetailByGateINID($BookingID, $TransID);
		$avg_bag = ($Result->LoadedWeight - $Result->TareWeight) * 100 / $Result->total_bags;
		$Qc_date = $QcDetails[0]['TransDate'];
		$i = 0;
		foreach ($QcDetails as $qd) {
			unset($QcDetails[$i]['TransDate']);
			$i++;
		}
		$cis_date = new DateTime($Result->cis_date);
		$cis_date_parsed = $cis_date->format('Y-m-d');
		$qc_date = new DateTime($Qc_date);
		$qc_date_parsed = $qc_date->format('Y-m-d');
		$gate_out_file = base_url() . "uploads/gateout_print/" . $BookingID . "/" . $TransID . ".pdf";
		$data = array(
			"wh_pid" => $Result->wh_pid, // warehouse ID Provided by star agri
			//  "warehouse_type_id"=>"NULL", // defualt null
			"fk_cm_billing_id" => "0", // defualt null
			//  "reservation_id"=>$Result->reservation_id, // Booking ID
			"reservation_id" => "0", // Bookink ID
			"cis_date" => $cis_date_parsed, // Gate IN Date
			"warehouse_address" => $Result->warehouse_address, // warehouse address
			"branch_name" => "NULL", // defualt NULL
			"location_name" => $Result->location_name, // Center Name
			"market_rate" => $Result->basic_rate * 10, // Market rate in MT (now only put Current rate in MT)
			//  "godown_id"=>$Result->godown_id, // Chamber ID Provided by star agri
			"godown_id" => $chamber_id, // Chamber ID Provided by star agri
			"godown_number" => $Result->godown_number, // Chamber name
			"nwr_issued" => "0", // defualt 0
			"nwr_no" => "NULL", // defualt NULL
			"depositor_address" => NULL, // defualt NULL it's Optional
			"depositor_contact_no" => $Result->depositor_mobile_no, // AccountID
			"depositor_name" => $Result->depositor_Name, // Account name
			"depositor_pan" => $Result->depositor_PAN, // Account PAN other wise it's null
			"depositor_aadhaar_no" => $Result->depositor_Aadhaar, // Account aadhaar number other wise null
			"depositor_type" => $Result->depositor_Type, // acctount type
			"com_id" => $Result->com_id, // Item GroupID provided by start agri
			"variety_id" => $Result->variety_id, // ItemID provided by star agri
			"commodity_value" => ($Result->LoadedWeight - $Result->TareWeight) * $Result->basic_rate, // Item Value (qty*rate)
			"insurance_by" => "3", // defualt value provided by star agri
			"fire_policy_no" => "123", // defualt value provided by star agri
			"insurance_company_name" => NULL, // defualt name provided by star agri
			"sum_insured_amount" => "", // defualt value provided by star agri
			"weight_in_mt" => ($Result->LoadedWeight - $Result->TareWeight) / 10, // Item weight in MT
			"avg_bag_size" => $avg_bag, // calculate from total weight devided by number of bag
			"quality_of_stock" => "1", // defualt value
			"date_of_testing" => $qc_date_parsed, // Final QC date
			"final_grade" => NULL, // defualt NULL
			"is_reservation" => "2", // defualt value provided by star agri
			"fk_rate_card_id" => "0", // defualt value provided by star agri
			"billing_cycle" => "1", // defualt value provided by star agri *
			"billing_unit" => "1", // defualt value provided by star agri *
			"billing_rate" => "0", // Get from rate master ( conform to parag and kailash sir) *
			"fk_supplier_id" => "0", // *
			"remarks" => NULL,  // defualt null
			"vehicle_stack_details" => array(array(
				"vehicle_no" => $Result->VehicleNo, // Vehicle Number
				"vehicle_doc" => $gate_out_file, // gate out url
				"gate_pass_no" => $Result->Gate_in_ID, // Gate Pass Number (GATEINID)
				"weighbridge_name" => "kirti group", // defualt name
				"weighbridge_slip_no" => $Result->weigh_bridge_slip_no, // collect number when enter weighbridge details
				"gross_weight" => ($Result->LoadedWeight) / 10, // gross weight in MT
				"tare_weight" => ($Result->TareWeight) / 10, // tare weight in MT
				"net_weight" => ($Result->LoadedWeight - $Result->TareWeight) / 10, // Net weight in MT
				"no_of_bags" => $Result->total_bags, // number of bag
				//  "awsLink"=>"https://franchisedocument.s3.amazonaws.com/4211698404343.png/", // *
				"stack_details" => array(array(
					"stack_no" => "1", // optional StackID
					"no_of_bags" => $Result->total_bags, // count bag per stack
					"spillage_bag" => "0" // defualt 0
				))
			)),
			"qualityReports" => $QcDetails,
			/*"qualityReports"=> array(
		        "variety_id"=>"", // ItemCode provided by star agri
		        "variety_name"=>"", // Item name
		        "fk_com_id"=>"", // Item GrooupID provided by start agri
		        "min_market_rate"=>"", // min market rate not collected **
		        "max_market_rate"=>"", // Max market rate not collected **
		        "qty_params_id"=>"", // QC param ID provided by star agri
		        "grade"=>NULL, // defualt null
		        "premium1"=>NULL, // defualt null
		        "activation_status"=>NULL, // defualt null
		        "premium2"=>NULL, // defualt null
		        "premium3"=>NULL, // defualt null
		        "premium4"=>NULL, // defualt null
		        "is_imported"=>NULL, // defualt null
		        "from1"=>NULL, // defualt null
		        "to1"=>NULL, // defualt null
		        "from2"=>NULL, // defualt null
		        "to2"=>NULL, // defualt null
		        "from3"=>NULL, // defualt null
		        "to3"=>NULL, // defualt null
		        "from4"=>NULL, // defualt null
		        "to4"=>NULL, // defualt null
		        "qty_params_name"=>"", // QC parameter name
		        "min_length"=>NULL, // defualt null
		        "max_length"=>NULL, // defualt null
		        "qrcode_link"=>NULL, // defualt null
		        "min"=>"", // min QC parameter value
		        "max"=>"", // Max QC parameter value
		        "result"=>"" // Actual result value
			),*/
			"physical_cis_no" => $Result->Gate_in_ID, // GATEIN ID
			"number_of_bags" => $Result->total_bags, // count of bag
			"base_receipt_no" => $Result->reservation_id, // New field added
			"is_licenced" => 1   // New Field added
			//  "jsonPayload"=>"yes", // defualt
			//  "access_tokan"=>"fe3fd1f94239c467727c5cae55d88fff",// defualt
		);
		// Changes done
		// commented access token , vehicle_doc
		// Quality reports -> renamed com_id to fk_com_id
		// Added $result[$i]['from4'] = 0;
		// Removed $result[$i]['TransDate']
		// Renamed $result[$i]['HParameterValue'] to ['result']
		// Renamed $result[$i]['ParameterName'] to ['qty_params_name']
		// 		echo "<pre>";
		// // 		print_r($QcDetails);
		// 		echo json_encode($data);
		// // 		print_r($data);
		// 		die;
		//Updated URL
		// https://novademo.agribazaar.com/kirtiGroupFranchiseGTSEntryMaster.php
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://novademo.agribazaar.com/kirtiGroupFranchiseGTSEntryMaster.php',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json; charset=UTF-8',
				'Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlBhcmFnIFByYWZ1bGxhIFJhaWJhZ2thcnxYfFh8MnxYfFh8NTMzOXxYfFh8MXxYfFh8U1RBUiBBR1JJV0FSRUhPVVNJTkcgJiBDT0xMQVRFUkFMIE1BTkFHRU1FTlQgTFREIg.RwyH0qQIwG8_DAGt0R44FyUc9d5Zy_Go4AWi796oYTo',
				'Connection: Keep-Alive',
				'User-Agent: okhttp/3.14.7',
				'User-Type: 0'
			),
		));
		$response = curl_exec($curl);
		$response = json_decode($response, true);
		curl_close($curl);
		if ($response['error'] == false) {
			//  $wr_id = $response['data'];
			$dataArray = array(
				"WR_ID" => $response['data'],
			);
			$this->db->where('TransID', $TransID);
			$this->db->update(db_prefix() . 'depositemaster', $dataArray);
			// 		if($this->db->affected_rows() > 0){
			// 		}else{
			// 		}
			set_alert('success', $response['message']);
		} else {
			set_alert('warning', $response['message']);
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	public function freeze()
	{
		$id = $this->input->post('id');
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->freezerecord_in_GateMaster($BookingID, $id);
		$TransID = $this->input->post('TransID');
		$result = $this->GateControl_model->freezrecord_in_History($TransID);
		$result = $this->GateControl_model->freezrecord_in_purchasemaster($TransID);
		if ($result == true) {
			set_alert('success', "Freez Successfully");
		} else {
			set_alert('warning', "somthing went wrong");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	public function markExitKirtiSell()
	{
		$id = $this->input->post('id');
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->markExitDBKirtiSell($BookingID, $id);
		if ($result == true) {
			set_alert('success', "Vehicle Exit Successfully");
		} else {
			set_alert('warning', "somthing went wrong");
		}
		$redUrl = admin_url('GateControl/GateControl_Reports_Details/' . $id);
		redirect($redUrl);
	}
	public function markExitWithdrawal()
	{
		$id = $this->input->post('id');
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->markExitWithdrawalDB($BookingID);
		header("Location:" . admin_url() . "GateControl/GateControl_Reports_Details/" . $id);
	}
	////////////////////////////////////////////////Deduction Matrix///////////////////////////////////////
	public function getAllMandi()
	{
		$result = $this->Cluster_model->getAllMandiDb();
		$html = '';
		foreach ($result as $key => $val) {
			$html .= '<tr onclick=fill_data("' . $val['CenterID'] . '")>';
			$html .= '<td>' . $val['CenterID'] . '</td>';
			$html .= '<td>' . $val['CenterName'] . '</td>';
			$html .= '<td>' . $val['commodity'] . '</td>';
			$html .= '<td>' . $val['CompetitorID'] . '</td>';
			$html .= '<td>' . $val['state_name'] . '</td>';
			$html .= '<td>' . $val['city'] . '</td>';
			$html .= '</tr>';
		}
		echo $html;
	}
	public function getSingleMandi()
	{
		$center_id = $this->input->post('center_id');
		$result = $this->Cluster_model->getSingleMandiDb($center_id);
		echo json_encode($result);
	}
	public function saveMandi()
	{
		$today = date('Y-m-d');
		$commodity = $this->input->post('commodity');
		$commodity = implode(",", $commodity);
		$competitor = $this->input->post('competitor');
		$competitor = implode(",", $competitor);
		$competitor = $competitor . ",C01,C02";
		$ItemdataSerializedArr = $this->input->post('ItemdataSerializedArr');
		$ItemsArray = json_decode($ItemdataSerializedArr, true);
		$ItemsArraylen = count($ItemsArray);
		$data = array(
			'CenterID' => $this->input->post('mandiID'),
			'CenterName' => $this->input->post('mandi'),
			'commodity' => $commodity,
			'CompetitorID' => $competitor,
			'state' => $this->input->post('state'),
			'city' => $this->input->post('city'),
			'taluka' => $this->input->post('taluka'),
			'TransDate' => $today,
			'UserID' => $this->session->userdata('username'),
		);
		$NF = array(
			array(
				'CenterID' => $this->input->post('mandiID'),
				'TType' => "S",
				'Number' => "1",
				'autoload' => "0",
			),
			array(
				'CenterID' => $this->input->post('mandiID'),
				'TType' => "D",
				'Number' => "1",
				'autoload' => "0",
			),
			array(
				'CenterID' => $this->input->post('mandiID'),
				'TType' => "A",
				'Number' => "1",
				'autoload' => "0",
			),
			array(
				'CenterID' => $this->input->post('mandiID'),
				'TType' => "W",
				'Number' => "1",
				'autoload' => "0",
			),
			array(
				'CenterID' => $this->input->post('mandiID'),
				'TType' => "G",
				'Number' => "1",
				'autoload' => "0",
			)
		);
		//$this->Cluster_model->saveNumberFormat($NF);
		$result =  $this->Cluster_model->saveMandi($data);
		if ($result == true) {
			foreach ($ItemsArray as $value) {
				$insertArray = array(
					"CenterID" => $this->input->post('mandiID'),
					"ItemID" => $value["0"],
					"DMGAmt" => $value["1"],
					"UserID" => $this->session->userdata('username'),
					"TransDate" => date('Y-m-d H:i:s'),
				);
				$this->db->insert(db_prefix() . 'DMGAmtCenterWise', $insertArray);
			}
		}
		echo json_encode($result);
	}
	public function updateMandi()
	{
		$today = date('Y-m-d H:m:s');
		$commodity = $this->input->post('commodity');
		$commodity = implode(",", $commodity);
		$competitor = $this->input->post('competitor');
		$competitor = implode(",", $competitor);
		$competitor = $competitor . ",C01,C02";
		//$paradataArraylength = $this->input->post('paradataArraylength');
		//$paradataSerializedArr = $this->input->post('paradataSerializedArr');
		$data = array(
			'CenterID' => $this->input->post('mandiID'),
			'CenterName' => $this->input->post('mandi'),
			'commodity' => $commodity,
			'CompetitorID' => $competitor,
			'state' => $this->input->post('state'),
			'city' => $this->input->post('city'),
			'taluka' => $this->input->post('taluka'),
			'TransDate' => $today,
			'UserID' => $this->session->userdata('username'),
			'paradataSerializedArr' => $this->input->post('paradataSerializedArr')
		);
		$result =  $this->Cluster_model->updateMandi($data);
		echo json_encode($result);
	}
	public function get_account_group_details()
	{
		$accountID = $this->input->post('act_id');
		$account_data = $this->accounts_master_model->get_account_group_details($accountID);
		echo json_encode($account_data);
	}
	public function GetChamberList()
	{
		$whid = $this->input->post('WHID');
		$result = $this->GateControl_model->GetChamberList($whid);
		echo json_encode($result);
	}
	public function GetWarehouseStackList()
	{
		$whid = $this->input->post('CHID');
		$result = $this->GateControl_model->GetWarehouseStackList($whid);
		echo json_encode($result);
	}
	public function GetStackList()
	{
		$whid = $this->input->post('WHID');
		$result = $this->GateControl_model->GetStackList($whid);
		echo json_encode($result);
	}
	public function GetStackLotList()
	{
		$StackID = $this->input->post('StackID');
		$result = $this->GateControl_model->GetStackLotList($StackID);
		echo json_encode($result);
	}
	public function Settlement()
	{
		if (!has_permission_new('Booking_settlement', '', 'view')) {
			access_denied('Booking settlement');
		}
		$data['title'] = "Booking Settlement";
		$this->load->view('admin/booking_settlement/booking_settlement', $data);
	}
	public function GetAllClientsName()
	{
		$BookingType = $this->input->post('BookingType');
		$result = $this->GateControl_model->GetAllClientsNameDB($BookingType);
		$html = '';
		$html = '<option value="">Not Selected</option>';
		foreach ($result as $key => $value) {
			$AccountID = $value["AccountID"];
			if ($value["company"] != '') {
				$name = $value["company"];
			} else {
				$name = $value["firstname"] . ' ' . $value["lastname"];
			}
			$html .= '<option value="' . $AccountID . '">' . $name . '</option>';
		}
		echo $html;
	}
	public function GetAllBookingID()
	{
		$BookingType = $this->input->post('BookingType');
		$PartyName = $this->input->post('PartyName');
		$result = $this->GateControl_model->GetAllBookingIDDB($BookingType, $PartyName);
		$html = '';
		$html = '<option value="">Not Selected</option>';
		foreach ($result as $key => $value) {
			$BookingID = $value["BookingID"];
			$html .= '<option value="' . $BookingID . '">' . $BookingID . '</option>';
		}
		echo $html;
	}
	public function GetBookingDetails()
	{
		$PartyName = $this->input->post('Name');
		$BookingType = $this->input->post('BookingType');
		$BookingID = $this->input->post('BookingID');
		$InwardDetails = $this->GateControl_model->GetTableDataDB($PartyName, $BookingType, $BookingID);
		$BookingDetails = $this->GateControl_model->GetSingleBookingDataDB($BookingID);
		if ($BookingType == "S") {
			$TodayRate = $this->GateControl_model->GetTodaysSaleRate($BookingDetails->ItemID, $BookingDetails->CenterID, $BookingType);
		} else {
			$TodayRate = $this->GateControl_model->GetTodaysRate($BookingDetails->ItemID, $BookingDetails->CenterID, $BookingDetails->CustomerType);
		}
		$total_net_weight = 0;
		foreach ($InwardDetails as $key => $value) {
			$NetWeight = $value['LoadedWeight'] - $value['TareWeight'];
			$total_net_weight += $NetWeight;
		}
		if ($BookingDetails->unit == "Bags") {
			$BookingWeight = $BookingDetails->quantity;
		} else if ($BookingDetails->unit == "Quintal") {
			$BookingWeight = $BookingDetails->quantity;
		} else if ($BookingDetails->unit == "MT") {
			$BookingWeight = $BookingDetails->quantity * 10;
		} else {
			$BookingWeight = $BookingDetails->quantity;
		}
		$result->NetWeight = number_format($total_net_weight, 2, '.', '');
		$result->BookingWeight =  number_format($BookingWeight, 2, '.', '');
		$result->BookingDate = $BookingDetails->TransDate;
		$result->BookingRate = $BookingDetails->basic_rate;
		$diff_qty = $BookingWeight - $total_net_weight;
		$result->diff_qty = number_format($diff_qty, 2, '.', '');
		$result->TodayRate = number_format($TodayRate->Rate, 2, '.', '');
		if ($BookingType == "S") {
			$DiffRate =  $BookingDetails->basic_rate - $result->TodayRate;
			/*if($DiffRate < 0){
        	        $ChargesAmt = $diff_qty * $DiffRate;
					}else{
        	        $ChargesAmt = 0;
				}*/
		} else {
			$DiffRate = $result->TodayRate - $BookingDetails->basic_rate;
		}
		if ($DiffRate > 0) {
			$ChargesAmt = 0;
		} else {
			$ChargesAmt = $diff_qty * $DiffRate;
		}
		$result->ChargesAmt = number_format($ChargesAmt, 2, '.', '');
		$result->PartyID = $BookingDetails->PartyID;
		$result->ItemID = $BookingDetails->ItemID;
		$result->CenterID = $BookingDetails->CenterID;
		$result->BrokerID = $BookingDetails->BrokerID;
		echo json_encode($result);
	}
	public function SaveSettlement()
	{
		$data_update = array(
			"inw_Weight" => $this->input->post('inw_Weight'),
			"today_rate" => $this->input->post('today_rate'),
			"status" => $this->input->post('Status'),
			"settlement_remark" => $this->input->post('Remark'),
			"is_invoice" => $this->input->post('is_invoice'),
			"SettlementID" => $this->session->userdata('username'),
			"SettlementDate" => date('Y-m-d H:i:s'),
			"BookingType" => $this->input->post('BookingType'),
			"PartyName" => $this->input->post('PartyName'),
			"BookingID" => $this->input->post('BookingID'),
			"shortageAmt" => $this->input->post('shortageAmt'),
			"CompID" => $this->input->post('CompID'),
			"BookingQty" => $this->input->post('BookingQty'),
			"CenterID" => $this->input->post('CenterID'),
			"ItemID" => $this->input->post('ItemID'),
			"BrokerID" => $this->input->post('BrokerID'),
			"is_not_delivered" => $this->input->post('is_not_delivered'),
			"NotDelAmt" => $this->input->post('NotDelAmt'),
		);
		$result =  $this->GateControl_model->save_settlement($data_update);
		echo json_encode($result);
	}
	public function GetTableData()
	{
		$Name = $this->input->post('Name');
		$BookingType = $this->input->post('BookingType');
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->GetTableDataDB($Name, $BookingType, $BookingID);
		$sr = 1;
		$total_bag = 0;
		$total_katta = 0;
		$total_net_weight = 0;
		foreach ($result as $key => $value) {
			if ($value['company'] != '') {
				$PartyName = $value['company'];
			} else {
				$PartyName = $value['firstname'] . ' ' . $value['lastname'];
			}
			if ($value['TType'] == 'P') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "CLEANING DONE ";
				} else if ($value['status'] == 9) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 10) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 11) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 12) {
					$status_val = "EXIT ";
				} else if ($value['status'] == 13) {
					$status_val = "PAYMENT ADVICE REQUEST SENT";
				} else if ($value['status'] == 14) {
					$status_val = "PAYMENT ADVICE APROVE";
				} else if ($value['status'] == 14 && $value['IsPayment'] == "Y") {
					$status_val = "PAYMENT DONE";
				} else if ($value['status'] == 14 && $value['IsCD'] == "Y") {
					$status_val = "DEBIT NOTE GENERATED ";
				} else if ($value['status'] == 14 && $value['IsCD'] == "Y" && $value['IsPayment'] == "Y") {
					$status_val = "DEBIT NOTE & PAYMENT GENERATED ";
				}
			}
			if ($value['TType'] == 'D') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "PERIPHERAL DONE";
				} else if ($value['status'] == 4) {
					$status_val = "GROSS WEIGHT CAPTURED ";
				} else if ($value['status'] == 5) {
					$status_val = "UNLOADING IN PROGRESS ";
				} else if ($value['status'] == 6) {
					$status_val = "UNLOADING FINISHED ";
				} else if ($value['status'] == 7) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 8) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 9) {
					$status_val = "FINAL QC DONE ";
				} else if ($value['status'] == 10) {
					$status_val = "GATE OUT GENERATED";
				} else if ($value['status'] == 11) {
					$status_val = "EXIT ";
				}
			}
			if ($value['TType'] == 'W') {
				if ($value['status'] == 0) {
					$status_val = "NO ACTION";
				} else if ($value['status'] == 1) {
					$status_val = "ASN GENERATED";
				} else if ($value['status'] == 2) {
					$status_val = "GATE IN GENERATED";
				} else if ($value['status'] == 3) {
					$status_val = "TARE WEIGHT CAPTURED ";
				} else if ($value['status'] == 4) {
					$status_val = "LOADING IN PROGRESS ";
				} else if ($value['status'] == 5) {
					$status_val = "LOADING FINISHED ";
				} else if ($value['status'] == 6) {
					$status_val = "QC DONE ";
				} else if ($value['status'] == 7) {
					$status_val = "FINAL QC DONE";
				} else if ($value['status'] == 8) {
					$status_val = "GROSS WEIGHT CAPTURED";
				} else if ($value['status'] == 9) {
					$status_val = "MARK AS EXIT";
				} else if ($value['status'] == 10) {
					$status_val = "EXIT";
				}
			}
			$html .= '<tr>';
			$html .= '<td>' . $value["ASNID"] . '</td>';
			$html .= '<td>' . $value["Gate_in_ID"] . '</td>';
			$html .= '<td>' . _d($value['gate_in_date']) . '</td>';
			$html .= '<td>' . $value['AccountID'] . '</td>';
			$html .= '<td>' . $PartyName . '</td>';
			$html .= '<td>' . $value['BookingID'] . '</td>';
			$html .= '<td>' . $value['ItemID'] . '</td>';
			$html .= '<td>' . $value['ItemName'] . '</td>';
			$NetWeight = $value['LoadedWeight'] - $value['TareWeight'];
			$total_net_weight += $NetWeight;
			$html .= '<td style="text-align:right">' . number_format($NetWeight, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right">' . number_format($value['total_bags'], 2, '.', '') . '</td>';
			$total_bag += $value['total_bags'];
			$html .= '<td style="text-align:right">' . number_format($value['total_katta'], 2, '.', '') . '</td>';
			$total_katta += $value['total_katta'];
			$html .= '<td style="text-align:right">' . number_format($value['total_layers'], 2, '.', '') . '</td>';
			$html .= '<td>' . $status_val . '</td>';
			$html .= '</tr>';
			$sr++;
		}
		$html .= '<tr>';
		$html .= '<td colspan="8">Total</td>';
		$html .= '<td style="text-align:right;font-weight:700">' . number_format($total_net_weight, 2, '.', '') . '</td>';
		$html .= '<td style="text-align:right;font-weight:700">' . number_format($total_bag, 2, '.', '') . '</td>';
		$html .= '<td style="text-align:right;font-weight:700">' . number_format($total_katta, 2, '.', '') . '</td>';
		$html .= '<td colspan="2"></td>';
		$html .= '</tr>';
		echo $html;
	}
	public function GetSingleBookingData()
	{
		$BookingID = $this->input->post('BookingID');
		$result = $this->GateControl_model->GetSingleBookingDataDB($BookingID);
		echo json_encode($result);
	}
	public function fetchAllStageData($id)
	{
		$data['title'] = "Gate Control Reports Details";
		//Fetch Gate Control Data
		$GateControlDetails = $this->GateControl_model->getSingleTradeById($id);
		$SName = array(
			'asn_by' => $this->GateControl_model->getStaffNameFromAccountID($GateControlDetails->asn_by),
			'gate_in_by' => $this->GateControl_model->getStaffNameFromAccountID($GateControlDetails->gate_in_by),
			'gate_out_by' => $this->GateControl_model->getStaffNameFromAccountID($GateControlDetails->gate_out_by),
			'exit_by' => $this->GateControl_model->getStaffNameFromAccountID($GateControlDetails->exit_by),
			'payment_approved_by' => $this->GateControl_model->getStaffNameFromAccountID($GateControlDetails->payment_approved_by),
		);
		$status = 'NA';
		if ($GateControlDetails->TType == 'P') {
			switch ($GateControlDetails->status) {
				case 0:
					$status = "NO ACTION";
					break;
				case 1:
					$status = "ASN GENERATED";
					break;
				case 2:
					$status = "GATE IN GENERATED";
					break;
				case 3:
					$status = "PERIPHERAL DONE";
					break;
				case 4:
					$status = "GROSS WEIGHT CAPTURED";
					break;
				case 5:
					$status = "UNLOADING IN PROGRESS";
					break;
				case 6:
					$status = "UNLOADING FINISHED";
					break;
				case 7:
					$status = "QC DONE";
					break;
				case 8:
					$status = "CLEANING DONE";
					break;
				case 9:
					$status = "TARE WEIGHT CAPTURED";
					break;
				case 10:
					$status = "FINAL QC DONE";
					break;
				case 11:
					$status = "GATE OUT GENERATED";
					break;
				case 12:
					$status = "MARK AS EXIT";
					break;
				case 13:
					$status = "PAYMENT ADVICE GANERATED";
					break;
				case 14:
					$status = "RO OFFICE QC DONE";
					break;
				case 15:
					$status = "HO OFFICE QC DONE";
					break;
				default:
					$status = "PAYMENT ADVICE APPROVED";
			}
		} else if ($GateControlDetails->TType == 'D') {
			switch ($GateControlDetails->status) {
				case 0:
					$status = "NO ACTION";
					break;
				case 1:
					$status = "ASN GENERATED";
					break;
				case 2:
					$status = "GATE IN GENERATED";
					break;
				case 3:
					$status = "PERIPHERAL DONE";
					break;
				case 4:
					$status = "GROSS WEIGHT CAPTURED";
					break;
				case 5:
					$status = "UNLOADING IN PROGRESS";
					break;
				case 6:
					$status = "UNLOADING FINISHED";
					break;
				case 7:
					$status = "QC DONE";
					break;
				case 8:
					$status = "TARE WEIGHT CAPTURED";
					break;
				case 9:
					$status = "FINAL QC DONE";
					break;
				case 10:
					$status = "GATE OUT GENERATED";
					break;
				default:
					$status = "EXIT";
			}
		}
		if ($GateControlDetails->CustomerType == 1) {
			$partyType = 'Farmer';
		} else if ($GateControlDetails->CustomerType == 2) {
			$partyType = 'Broker';
		} else if ($GateControlDetails->CustomerType == 3) {
			$partyType = 'Trader';
		} else if ($GateControlDetails->CustomerType == 4) {
			$partyType = 'Corporate/Processor';
		}
		$GateControlArray = array(
			'AccountID' => $GateControlDetails->AccountID,
			'BookingID' => $GateControlDetails->BookingID,
			'Status' => $status,
			'PartyType' => $partyType,
			'PartyName' => $GateControlDetails->company,
			'ItemID' => $GateControlDetails->ItemID,
			'ItemName' => $GateControlDetails->ItemName,
			'ASNBy' => $SName["asn_by"]->firstname . ' ' . $SName["asn_by"]->lastname,
			'ASNDate' => $GateControlDetails->asn_date,
			'ASNQuantity(MT)' => $GateControlDetails->Asn_WT_MT,
			'ASNQuantity(Bag)' => $GateControlDetails->quantity,
			'GateInBy' => $SName["gate_in_by"]->firstname . ' ' . $SName["gate_in_by"]->lastname,
			'GateInDate' => $GateControlDetails->gate_in_date,
		);
		//Fetch peripheral QC Data
		$BookingID = $GateControlDetails->BookingID;
		$GateINID = $GateControlDetails->Gate_in_ID;
		$peripheralData = $this->GateControl_model->getPeripheralDetails($BookingID, $GateINID);
		$PeripheralQCDetails = array();
		foreach ($peripheralData as $value) {
			$PeripheralQCDetails[$value['ItemParameterName']] = $value['ParameterValue'];
		}
		$PeripheralQCDetails["UserID"] = $peripheralData[0]['firstname'] . ' ' . $peripheralData[0]['lastname'];
		$PeripheralQCDetails["TransDate"] = $peripheralData[0]['TransDate'];
		//Fetch Gross Weight Data
		$staffDetails = array(
			'LWUserID' => $this->GateControl_model->getStaffNameFromId($GateControlDetails->LWUserID),
			'FMUserID' => $this->GateControl_model->getStaffNameFromId($GateControlDetails->FMUserID),
			'TWUserID' => $this->GateControl_model->getStaffNameFromId($GateControlDetails->TWUserID),
		);
		$GrossWeightDetails = array(
			'TotalWeight' => $GateControlDetails->LoadedWeight,
			'LoadedBy' => $staffDetails['LWUserID']->firstname . ' ' . $staffDetails['LWUserID']->lastname,
			'LoadedDatetime' => $GateControlDetails->LWTransDate,
		);
		//Fetch Unloading Details
		$layers = $this->GateControl_model->getLayerDetails($BookingID, $GateINID);
		$layersArray = array();
		$i = 0;
		foreach ($layers as $l) {
			$layersArray[$i]['layer_number'] = $l['layer_number'];
			$layersArray[$i]['quantity'] = $l['qty'];
			$layersArray[$i]['unit'] = $l['unit'];
			$layersArray[$i]['done_by'] = $l['firstname'] . ' ' . $l['lastname'];
			$layersArray[$i]['done_date'] = $l['Transdate'];
			foreach ($l['parameter_detail'] as $paramDetail) {
				$layersArray[$i][$paramDetail['ItemParameterName']] = $paramDetail['ParameterValue'];
			}
			$layersArray[$i]['qc_done_by'] = $l['parameter_detail'][0]['firstname'] . ' ' . $l['parameter_detail'][0]['lastname'];
			$layersArray[$i]['qc_done_time'] = $l['parameter_detail'][0]['TransDate'];
			$i++;
		}
		$UnloadingDetails = array(
			'TotalLayers' => $GateControlDetails->no_of_layers,
			'Layers' =>  $layersArray
		);
		//Fetch Cleaning Details
		$CleaningDetails = array(
			'FM(Kg)' => $GateControlDetails->FMQty,
			'CleaningBy' => $staffDetails["FMUserID"]->firstname . ' ' . $staffDetails["FMUserID"]->lastname,
			'CleaningDate' => $GateControlDetails->FMTransDate,
		);
		//Fetch Tare Weight Details
		$TareWeightDetails = array(
			'TareWeight' => $GateControlDetails->TareWeight,
			'UnloadedBy' => $staffDetails["TWUserID"]->firstname . ' ' . $staffDetails["TWUserID"]->lastname,
			'UnloadedDate' => $GateControlDetails->TWTransDate,
		);
		//Fetch Final QC Details
		$finalQC = $this->GateControl_model->getFinalQCDetails($BookingID, $GateINID);
		$FinalQCDetails = array();
		foreach ($peripheralData as $value) {
			$FinalQCDetails[$value['ItemParameterName']] = $value['ParameterValue'];
		}
		//Fetch Gate Out Pass details
		$GateOutPassDetails = array(
			'GateOutBy' => $SName['gate_out_by']->firstname . ' ' . $SName['gate_out_by']->lastname,
			'GateOutDate' => $GateControlDetails->gate_out_date
		);
		//Fetch ExitDetails
		$ExitDetails = array(
			'ExitBy' => $SName['exit_by']->firstname . ' ' . $SName['exit_by']->lastname,
			'ExitDate' => $GateControlDetails->exit_date
		);
		$result = array(
			'GateControlDetails' => $GateControlArray,
			'PeripheralQCDetails' => $PeripheralQCDetails,
			'GrossWeightDetails' => $GrossWeightDetails,
			'UnloadingDetails' => $UnloadingDetails,
			'CleaningDetails' => $CleaningDetails,
			'TareWeightDetails' => $TareWeightDetails,
			'FinalQCDetails' => $FinalQCDetails,
			'GateOutPassDetails' => $GateOutPassDetails,
			'ExitDetails' => $ExitDetails,
		);
		$data['result'] = $result;
		$this->load->view('admin/gateControl/fetchAllStageData', $data);
	}
	public function export_centerwise_trade_quantity()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'CenterID' => $this->input->post('CenterID'),
				'ItemID' => $this->input->post('ItemID'),
				'TradeType' => $this->input->post('TradeType'),
				'TradeStatus' => $this->input->post('TradeStatus')
			);
			$result = $this->GateControl_model->GetCenterWiseTradeQuantity($data);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 3);
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = array($company_detail->address);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 3);
			$writer->writeSheetRow('Sheet1', $address);
			$report_title = array('Center Wise Trade Quantity Report');
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 3);
			$writer->writeSheetRow('Sheet1', $report_title);
			$centerLabel = ($data['CenterID'] != '') ? $this->input->post('CenterText') : 'All';
			$itemLabel = ($data['ItemID'] != '') ? $this->input->post('ItemText') : 'All';
			$tradeTypeLabel = ($data['TradeType'] != '') ? $this->input->post('TradeTypeText') : 'All';
			$tradeStatusLabel = ($data['TradeStatus'] != '') ? $this->input->post('TradeStatusText') : 'All';
			$filter_row1 = 'From Date: ' . $data['from_date'] . ' | To Date: ' . $data['to_date'] . ' | Center: ' . $centerLabel . ' | Commodity: ' . $itemLabel;
			$filter_row2 = 'Trade Type: ' . $tradeTypeLabel . ' | Status: ' . $tradeStatusLabel;
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 3);
			$writer->writeSheetRow('Sheet1', array($filter_row1));
			$writer->markMergedCell('Sheet1', $start_row = 4, $start_col = 0, $end_row = 4, $end_col = 3);
			$writer->writeSheetRow('Sheet1', array($filter_row2));
			$set_col_tk = [];
			$set_col_tk["Sr. No."] = 'Sr. No.';
			$set_col_tk["Center Name"] = 'Center Name';
			$set_col_tk["Total Trade Quantity"] = 'Total Trade Quantity';
			$set_col_tk["Total Inward Qty"] = 'Total Inward Qty';
			$writer->writeSheetRow('Sheet1', $set_col_tk);
			$srNo = 1;
			$grandTradeQty = 0;
			$grandInwardQty = 0;
			foreach ($result as $value) {
				$grandTradeQty += $value["TotalTradeQty"];
				$grandInwardQty += $value["TotalInwardQty"];
				$list_add = [];
				$list_add[] = $srNo;
				$list_add[] = $value["CenterName"];
				$list_add[] = $value["TotalTradeQty"];
				$list_add[] = $value["TotalInwardQty"];
				$writer->writeSheetRow('Sheet1', $list_add);
				$srNo++;
			}
			if ($srNo > 1) {
				$list_add = [];
				$list_add[] = '';
				$list_add[] = 'Grand Total';
				$list_add[] = $grandTradeQty;
				$list_add[] = $grandInwardQty;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'CenterWiseTradeQuantity.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	public function export_tradsettledlist()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'CenterID' => $this->input->post('CenterID'),
				'purchase_for' => $this->input->post('purchase_for'),
				'TradeStatus' => $this->input->post('TradeStatus')
			);
			$result = $this->GateControl_model->GetBookingListDetails($data);
			$InwardDetails = $this->GateControl_model->GetBookingListInwardDetails($data);
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 14);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 14);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			$set_col_tk = [];
			$set_col_tk["Booking ID"] =  'Booking ID';
			$set_col_tk["Booking Date"] = 'Booking Date';
			$set_col_tk["Settlement Date"] = 'Settlement Date';
			$set_col_tk["DueDate"] = 'Due Date';
			$set_col_tk["Purchase For"] = 'Purchase For';
			$set_col_tk["Center Name"] = 'Center Name';
			$set_col_tk["Party Name"] =  'Party Name';
			$set_col_tk["Item Name"] =  'Item Name';
			$set_col_tk["Broker  Name"] =  'Broker Name';
			$set_col_tk["Booking Rate"] = 'Booking Rate';
			$set_col_tk["Rate(at Setlled)"] = 'Rate(at Setlled)';
			$set_col_tk["Booking WT(MT)"] = 'Booking WT(MT)';
			$set_col_tk["Inward WT(MT)"] = 'Inward WT(MT)';
			$set_col_tk["Inward WT(%)"] = 'Inward WT(%)';
			$set_col_tk["Shortage Charge "] = 'Shortage Charge 	';
			$set_col_tk["Non Delivery Charge"] = 'Non Delivery Charge';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$grandQuantity = 0;
			$grandInwardWeight = 0;
			$grandShortageAmt = 0;
			$grandNonDeliverdAmt = 0;
			foreach ($result as $k => $value) {
				$InwardWeight = 0;
				foreach ($InwardDetails as $inkey => $inval) {
					if ($value["BookingID"] == $inval["BookingID"]) {
						$InwardWeight = $inval["InwardQty"];
					}
				}
				$grandQuantity += $value["quantity"];
				$grandInwardWeight += $InwardWeight;
				$grandShortageAmt += $value["ShortageAmt"];
				$grandNonDeliverdAmt += $value["NonDeliverdAmt"];
				$list_add = [];
				$list_add[] = $value["BookingID"];
				$list_add[] = $value['TransDate'];
				$date = strtotime(substr($value["TransDate"], 0, 10));
				$Duedate = strtotime("+7 day", $date);
				$Duedate = date('d/m/Y', $Duedate);
				$list_add[] = $value["SettlementDate"];
				$list_add[] = $Duedate;
				$list_add[] = $value["PlantName"];
				$list_add[] = $value["CenterName"];
				$list_add[] = $value["company"];
				$list_add[] = $value["BrokerName"];
				$list_add[] = $value["ItemName"];
				$list_add[] = $value["basic_rate"];
				$list_add[] = $value["today_rate"];
				$list_add[] = $value["quantity"];
				$list_add[] = $InwardWeight;
				$InwardPer = ($value["quantity"] > 0) ? ($InwardWeight / ($value["quantity"])) * 100 : 0;
				$list_add[] = $InwardPer;
				$list_add[] = $value["ShortageAmt"];
				$list_add[] = $value["NonDeliverdAmt"];
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			if (count($result) > 0) {
				$grandInwardPer = ($grandQuantity > 0) ? ($grandInwardWeight / $grandQuantity) * 100 : 0;
				$list_add = [];
				$list_add[] = 'Grand Total';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = '';
				$list_add[] = $grandQuantity;
				$list_add[] = $grandInwardWeight;
				$list_add[] = $grandInwardPer;
				$list_add[] = $grandShortageAmt;
				$list_add[] = $grandNonDeliverdAmt;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'TradeList.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	public function export_Purchasepaymentlist()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
		if ($this->input->post()) {
			$company_detail = $this->sale_reports_model->get_company_detail();
			$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date' => $this->input->post('to_date'),
				'CenterID' => $this->input->post('CenterID'),
				'purchase_for' => $this->input->post('purchase_for')
			);
			$result = $this->GateControl_model->GetPendingPaymentList($data);
			$QCparameter = $this->GateControl_model->GetQCParameter();
			$resultFarmer = $this->GateControl_model->GetPendingPaymentListForFarmer($data);
			$mergedArrays = array_merge($result, $resultFarmer);
			usort($mergedArrays, function ($a, $b) {
				return strcmp($b['Gate_in_ID'], $a['Gate_in_ID']);
			});
			$result = $mergedArrays;
			$All_gate_in = array();
			foreach ($result as $key => $value) {
				array_push($All_gate_in, $value["Gate_in_ID"]);
			}
			if ($result) {
				$PaymentList = $this->GateControl_model->GetPaymentListByGateIN($All_gate_in);
				$QCList = $this->GateControl_model->GetQCDetailsByGateIN($All_gate_in);
				$MaxLayerNumber = $this->GateControl_model->GetMaxQCLayer($All_gate_in);
				$MaxQCLayer = $MaxLayerNumber->MaxLayer;
				$OtherDeductionItemList = $this->GateControl_model->GetOtherDeductionItems();
				$OtherDeductionItems = $this->GateControl_model->GetOtherDeductionGateINWise($All_gate_in);
				$BagDetails = $this->GateControl_model->GetBagQtyGateInWise($All_gate_in);
			}
			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 16);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			$address = $company_detail->address;
			$center_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 16);  //merge cells
			$writer->writeSheetRow('Sheet1', $center_addr);
			$set_col_tk = [];
			$set_col_tk["Sr. No."] =  'Sr. No.';
			$set_col_tk["TradeID"] =  'TradeID';
			$set_col_tk["Trade Date"] =  'Trade Date';
			$set_col_tk["Arrival DateTime"] =  'Arrival DateTime';
			$set_col_tk["GateIN ID"] = 'GateIN ID';
			$set_col_tk["Inward Date"] = 'Inward Date';
			$set_col_tk["Purchase For"] = 'Purchase For';
			$set_col_tk["Party Mobile"] = 'Party Mobile';
			$set_col_tk["Party Name"] = 'Party Name';
			$set_col_tk["Party Type"] = 'Party Type';
			$set_col_tk["PAN/Aadhaar"] = 'PAN/Aadhaar';
			$set_col_tk["State"] = 'State';
			$set_col_tk["District"] = 'District';
			$set_col_tk["Taluka"] = 'Taluka';
			$set_col_tk["Post Office"] = 'Post Office';
			$set_col_tk["Town"] = 'Town';
			$set_col_tk["Locality"] = 'Localaty';
			$set_col_tk["Street"] = 'Street';
			$set_col_tk["House"] = 'House';
			$set_col_tk["Pincode"] = 'Pincode';
			$set_col_tk["Business Address"] = 'Business Address';
			$set_col_tk["Center Name"] =  'Center Name';
			$set_col_tk["Vehicle No"] =  'Vehicle No';
			$set_col_tk["Item Name"] =  'Item Name';
			$set_col_tk["Bag Qty"] =  'Bag Qty';
			$set_col_tk["Net Weight(Qtl)"] =  'Net Weight(Qtl)';
			$set_col_tk["Trade Rate"] =  'Trade Rate';
			$set_col_tk["Total Amt"] =  'Total Amt';
			$set_col_tk["Applicable Rate"] =  'Applicable Rate';
			for ($i = 1; $i <= $MaxQCLayer; $i++) {
				foreach ($QCparameter as $key => $value) {
					$set_col_tk[$value['ItemParameterName'] . ' ' . $i] =  $value['ItemParameterName'] . " " . $i;
				}
				$set_col_tk['Layer ' . $i . ' Weight(Qtl)'] =  'Layer ' . $i . ' Weight(Qtl)';
				$set_col_tk['Layer ' . $i . ' Bag Qty'] =  'Layer ' . $i . ' Bag Qty';
				$set_col_tk['Layer ' . $i . ' Amt'] =  'Layer ' . $i . ' Amt';
			}
			foreach ($QCparameter as $key => $value) {
			}
			$set_col_tk["TDS"] =  'TDS';
			$set_col_tk["PO Taxable Amt"] = 'PO Taxable Amt';
			$set_col_tk["PO GST Amt"] = 'PO GST Amt';
			$set_col_tk["PO Net Amt"] = 'PO Net Amt';
			$set_col_tk["QC Deduction"] = 'QC Deduction';
			foreach ($OtherDeductionItemList as $Okey => $Ovalue) {
				$set_col_tk[$Ovalue['ItemName']] = $Ovalue["ItemName"];
			}
			$set_col_tk["GST On Deduction"] = 'GST On Deduction';
			$set_col_tk["Net Deduction"] = 'Net Deduction';
			$set_col_tk["Payable Amt"] = 'Payable Amt';
			$set_col_tk["Paid Amt"] = 'Paid Amt';
			$set_col_tk["Bal Amt"] = 'Bal Amt';
			$set_col_tk["Status"] = 'Status';
			$set_col_tk["IFSC"] =  'IFSC';
			$set_col_tk["Bank Name"] =  'Bank Name';
			$set_col_tk["Act Number"] =  'Acct Number';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$j = 1;
			foreach ($result as $k => $value) {
				$net_weight = $value['LoadedWeight'] - $value['TareWeight'];
				$PaidAmt = 0;
				foreach ($PaymentList as $key1 => $value1) {
					if ($value["BookingID"] == $value1["BookingID"] && $value["Gate_in_ID"] == $value1["GateINID"] && $value["AccountID"] == $value1["AccountID"]) {
						$PaidAmt += $value1["Amount"];
					}
				}
				$list_add = [];
				$list_add[] = $j;
				$list_add[] = $value["BookingID"];
				$list_add[] = _d($value["BookingDate"]);
				$list_add[] = _d($value["VchlArrivalDateTime"]);
				$list_add[] = $value['Gate_in_ID'];
				$list_add[] = _d($value["gate_in_date"]);
				$list_add[] = $value["PlantName"];
				$list_add[] = $value["AccountID"];
				$list_add[] = preg_replace('!\s+!', ' ', $value["company"]);
				$AadharPAN = "";
				$state = "";
				if ($value["CustomerType"] == 1) {
					$list_add[] = 'Farmer';
					$AadharPAN = $value["aadhaar_number"];
					$state = $value["AState"];
				} else if ($value["CustomerType"] == 2) {
					$list_add[] = 'Broker';
					$AadharPAN = $value["Pan"];
					$state = $value["GSTState"];
				} else if ($value["CustomerType"] == 3) {
					$list_add[] = "Trader";
					$AadharPAN = $value["Pan"];
					$state = $value["GSTState"];
				}
				$list_add[] = $AadharPAN;
				$list_add[] = $state;
				$list_add[] = $value["Adist"];
				$list_add[] = $value["Asubdist"];
				$list_add[] = $value["Apo"];
				$list_add[] = $value["Avtc"];
				$list_add[] = $value["Aloc"];
				$list_add[] = $value["Astreet"];
				$list_add[] = $value["Ahouse"];
				$list_add[] = $value["Apincode"];
				$list_add[] = $value["GSTAddress"];
				$list_add[] = $value["CenterName"];
				$list_add[] = $value["VehicleNo"];
				$list_add[] = $value["ItemName"];
				$BagQty = 0;
				$TotalWeightMT = 0;
				foreach ($BagDetails as $kbag => $vbag) {
					if ($value["Gate_in_ID"] == $vbag["GateINID"]) {
						$BagQty = $vbag["TotalBagQty"];
						$TotalWeightMT = $vbag["TotalWeightMT"];
					}
				}
				$list_add[] = number_format($BagQty, 2, '.', ',');
				$list_add[] = number_format($TotalWeightMT, 2, '.', ',');
				$TotalAmt = ($TotalWeightMT * 10) * $value['basic_rate'];
				$list_add[] = (string)number_format(($value['basic_rate'] * 10), 2, '.', ',');
				$list_add[] = (string)number_format($TotalAmt, 2, '.', ',');
				$list_add[] = (string)number_format($value['final_rate'], 2, '.', ',');
				$TotalDeduction = 0;
				$netDeduction = 0;
				for ($i = 1; $i <= $MaxQCLayer; $i++) {
					$LayerWiseAmt = 0;
					$LayerWiseWt = 0;
					$LayerWiseBag = 0;
					foreach ($QCparameter as $Qkey => $Qval) {
						$QCValue = "";
						foreach ($QCList as $QVKey => $QVVal) {
							if ($QVVal["ItemParameterID"] == $Qval["ItemParameterID"] && $value["Gate_in_ID"] == $QVVal["Gate_in_ID"] && $i == $QVVal["layer_number"]) {
								$QCValue = $QVVal["HParameterValue"];
								$TotalDeduction += $QVVal["deductionAmt"];
								$LayerWiseAmt += $QVVal["deductionAmt"];
								$LayerWiseWt = $QVVal["MTWeight"];
								$LayerWiseBag = $QVVal["BagQty"];
							}
						}
						$list_add[] = $QCValue;
					}
					$list_add[] = (string)number_format($LayerWiseWt * 10, 2, '.', ',');
					$list_add[] = (string)number_format($LayerWiseBag, 2, '.', ',');
					$list_add[] = (string)number_format($LayerWiseAmt, 2, '.', ',');
				}
				$list_add[] = 0.00;
				if ($value["CustomerType"] == 1) {
					$NetAmt = $TotalWeightMT  * $value["final_rate"];
					$GrossAmt = $TotalWeightMT * $value["final_rate"];
					$GstAmt = 0.00;
				} else {
					$GrossAmt = (($TotalWeightMT * 10) * $value["basic_rate"]);
					$GstAmt = ($GrossAmt * $value["taxrate"]) / 100;
					$NetAmt = $GrossAmt + $GstAmt;
				}
				$netAmt = $GrossAmt + $GstAmt;
				$list_add[] = (string)number_format($GrossAmt, 2, '.', ',');
				$list_add[] = (string)number_format($GstAmt, 2, '.', ',');
				$list_add[] = (string)number_format($NetAmt, 2, '.', ',');
				$list_add[] = (string)number_format($TotalDeduction, 2, '.', ',');
				foreach ($OtherDeductionItemList as $OItemKey => $OItemVal) {
					$DedAmt = 0;
					foreach ($OtherDeductionItems as $OKey => $Oval) {
						if ($value["Gate_in_ID"] == $Oval["GateINID"] && $OItemVal["ItemID"] == $Oval["ItemID"]) {
							$DedAmt += $Oval["Amount"];
						}
					}
					$list_add[] = (string)number_format($DedAmt, 2, '.', ',');
					$TotalDeduction += $DedAmt;
				}
				if ($value["CustomerType"] == 1) {
					$NetAmt = $TotalWeightMT  * $value["final_rate"];
					$GrossAmt = $TotalWeightMT * $value["final_rate"];
					$GstAmt = 0.00;
					$DBGstAmt = 0.00;
					$netDeduction = $TotalDeduction;
					$PayableAmt = $NetAmt;
				} else {
					$GrossAmt = (($TotalWeightMT * 10) * $value["basic_rate"]);
					$GstAmt = ($GrossAmt * $value["taxrate"]) / 100;
					$NetAmt = $GrossAmt + $GstAmt;
					$DBGstAmt = ($TotalDeduction * $value["taxrate"]) / 100;
					$netDeduction = $DBGstAmt + $TotalDeduction;
					$PayableAmt = $NetAmt - $netDeduction;
				}
				$list_add[] = (string)number_format($DBGstAmt, 2, '.', ',');
				$list_add[] = (string)number_format($netDeduction, 2, '.', ',');
				$list_add[] = (string)number_format($PayableAmt, 2, '.', ',');
				$list_add[] = (string)number_format($PaidAmt, 2, '.', ',');
				$balAmt = $PayableAmt - $PaidAmt;
				$list_add[] = (string)number_format($balAmt, 2, '.', ',');
				if ($PayableAmt <= $PaidAmt) {
					$msg = "PAID";
				} else if ($PaidAmt > 0) {
					$msg = "PARTIALLY PAID";
				} else {
					$msg = "UNPAID";
				}
				$list_add[] = $msg;
				$list_add[] = $value["ifsc"];
				$list_add[] = $value["bankName"];
				$list_add[] = $value["accountNumber"] . " ";
				$list_add[] = $row_a;
				$writer->writeSheetRow('Sheet1', $list_add);
				$j++;
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'purchasePaymentList.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}
	function send_notification($title, $screen, $body, $booking_id, $to)
	{
		$data_arrary = array(
			"title" => $title,
			"screen" => $screen,
			"body" => $body,
			"booking_id" => $booking_id
		);
		$post_data = array(
			"priority" => "HIGH",
			"data" => $data_arrary,
			"to" => $to
		);
		$finel_data = json_encode($post_data);
		$curl = curl_init();
		curl_setopt_array(
			$curl,
			array(
				CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $finel_data,
				CURLOPT_HTTPHEADER => array(
					"authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",
					"content-type: application/json"
				),
			)
		);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		// return $response;
	}
	public function updatePeripheralQC()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('BookingID');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$number_of_para = $this->input->post('count');
		$result = $this->GateControl_model->updatePeripheralQcParameterDetails($requestData, $BookingID, $GateInID, $number_of_para);
		//return $result;
		if ($result == true) {
			set_alert('success', 'Peripharal QC Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	//============================ Update Godown ===================================
	public function UpdateGodown()
	{
		$GodownID = $this->input->post('GodownID');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->UpdateGodown($GodownID, $GateInID);
		//return $result;
		if ($result == true) {
			set_alert('success', 'Godown Updated successfully');
		} else {
			set_alert('warning', 'Somthing went wrong, Please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	//========================== Add Field Officer =================================
	public function AddFieldOfficer()
	{
		$FeildOfficer = $this->input->post('FeildOfficer');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->AddFieldOfficer($FeildOfficer, $GateInID);
		//return $result;
		if ($result == true) {
			set_alert('success', 'Field Officer Updated successfully');
		} else {
			set_alert('warning', 'Somthing went wrong, Please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	//======================= Add Arrival Date Time ================================
	public function AddArrivalDateTime()
	{
		$ArrivalDateTime = $this->input->post('ArrivalDateTime');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->AddArrivalDateTime($ArrivalDateTime, $GateInID);
		//return $result;
		if ($result == true) {
			set_alert('success', 'Arrival DateTime Updated successfully');
		} else {
			set_alert('warning', 'Somthing went wrong, Please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function AddVillageDetails()
	{
		$accid = $this->input->post('accid');
		$GateINID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$stateid = $this->input->post('stateid');
		$talukaid = $this->input->post('talukaid');
		$districtid = $this->input->post('districtid');
		$pinid = $this->input->post('pinid');
		$villageID =  $this->input->post('village');
		$villageName = $this->input->post('villagename');
		$State = $this->input->post('state');
		$District = $this->input->post('district');
		$Taluka = $this->input->post('taluka');
		if ($villageID == "new") {
			$result = $this->GateControl_model->AddNewVillage($pinid, $villageName, $stateid, $talukaid, $districtid, $GateINID, $id, $accid);
			if ($result) {
				set_alert('success', 'New village added successfully');
			} else {
				set_alert('danger', 'Failed to add new village');
			}
		} else {
			$result = $this->GateControl_model->UpdateVillage($GateINID, $id, $villageID, $accid);
			if ($result) {
				set_alert('success', 'data update successfully');
			} else {
				set_alert('danger', 'Failed to add new village');
			}
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateGrossWeightDetails()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('BookingID');
		$gross_weight = $this->input->post('total_weight');
		$GateINID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->updateGrossWeightDetails($BookingID, $GateINID, $gross_weight);
		if ($result == true) {
			set_alert('success', 'Gross Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateCleaningDetails()
	{
		$BookingID = $this->input->post('BookingID');
		$fm_cleaning = $this->input->post('fm_cleaning');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->updateCleaningDetails($BookingID, $GateInID, $fm_cleaning);
		if ($result == true) {
			set_alert('success', 'FM Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateBagWeightDetails()
	{
		$data = $this->input->post();
		$id = $this->input->post('id');
		$result = $this->GateControl_model->updateBagWeightDetails($data);
		if ($result == true) {
			set_alert('success', 'Bag Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function AddEditEmptyWeightForWithdraw()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('BookingID');
		$tare_weight = $this->input->post('tare_weight');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		// echo $tare_weight;die;
		$result = $this->GateControl_model->AddEditEmptyWeightForWithdraw($BookingID, $GateInID, $tare_weight);
		if ($result == true) {
			set_alert('success', 'Tare Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function AddEditLoadedWeightForWithdraw()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('BookingID');
		$total_weight = $this->input->post('total_weight');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		// echo $total_weight;die;
		$result = $this->GateControl_model->AddEditLoadedWeightForWithdraw($BookingID, $GateInID, $total_weight);
		if ($result == true) {
			set_alert('success', 'Tare Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateTareWeightDetails()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('BookingID');
		$tare_weight = $this->input->post('tare_weight');
		$GateInID = $this->input->post('GateINID');
		$id = $this->input->post('id');
		$result = $this->GateControl_model->updateTareWeightDetails($BookingID, $GateInID, $tare_weight);
		if ($result == true) {
			set_alert('success', 'Tare Weight Updated successfully');
		} else {
			set_alert('warning', 'no changes');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	// Add Update Stack,LOT wise QC & Stack Details
	public function updateStackDetails()
	{
		$requestData = $this->input->post();
		$id = $this->input->post('id');
		$TotalLot = $this->input->post('TotalLot');
		$result = $this->GateControl_model->UpdateStackDetails($requestData);
		if ($result == $TotalLot) {
			set_alert('success', 'Stack List Updated successfully');
		} else if ($result > 0) {
			set_alert('success', 'Stack List Updated successfully');
		} else {
			set_alert('warning', 'Stack List not updated please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateWithdrawDetails()
	{
		$requestData = $this->input->post();
		$id = $this->input->post('id');
		// echo "<pre>";print_r($this->input->post());die;
		$result = $this->GateControl_model->updateWithdrawDetails($requestData);
		if ($result == true) {
			set_alert('success', 'Withdraw List Updated successfully');
		} else {
			set_alert('warning', 'Withdraw List not updated please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	// Update Lot Wise QC Details
	public function UpdateLotWiseQc()
	{
		$requestData = $this->input->post();
		$id = $this->input->post('id');
		$TotalLot = $this->input->post('TotalLot');
		$result = $this->GateControl_model->UpdateLotWiseQc($requestData);
		if ($result == $TotalLot) {
			set_alert('success', 'Stack List Updated successfully');
			$UpdateStatusCreditLimit = array(
				'Status' => 'N',
			);
			$this->db->where('GateINID', $GateINID);
			$this->db->update('tblCreditLimitMaster', $UpdateStatusCreditLimit);
			if ($GateControlDetails->TType == "S") {
				$this->GateControl_model->GenerateLedgerEntryForSale($BookingID, $GateINID);
			} else if ($GateControlDetails->TType == "P") {
				$purchasedetails = $this->GateControl_model->fetch_purchase_details($GateINID);
				$NetAmt = $purchasedetails->Invamt ?? 0;
				$this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID, $GateINID, $NetAmt);
			}
		} else if ($result > 0) {
			set_alert('success', 'Partial Stack List Updated successfully');
			$UpdateStatusCreditLimit = array(
				'Status' => 'N',
			);
			$this->db->where('GateINID', $GateINID);
			$this->db->update('tblCreditLimitMaster', $UpdateStatusCreditLimit);
			if ($GateControlDetails->TType == "S") {
				$this->GateControl_model->GenerateLedgerEntryForSale($BookingID, $GateINID);
			} else if ($GateControlDetails->TType == "P") {
				$purchasedetails = $this->GateControl_model->fetch_purchase_details($GateINID);
				$NetAmt = $purchasedetails->Invamt ?? 0;
				$this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID, $GateINID, $NetAmt);
			}
		} else {
			set_alert('warning', 'Stack List not updated please try again');
		}
		redirect('admin/GateControl/GateControl_Reports_Details/' . $id);
	}
	public function updateLayerDetails()
	{
		$requestData = $this->input->post();
		$BookingID = $this->input->post('Booking_ID');
		$GateInID = $this->input->post('Gate_IN_ID');
		$id = $this->input->post('id');
		$ItemID = $this->input->post('ItemID');
		$item_array = $this->input->post('unloading_array');
		$user_Details = $this->GateControl_model->getStaffNameFromAccountID($this->session->userdata('username'));
		$layer_details = $this->GateControl_model->fetch_layer_details($BookingID, $GateInID);
		$item_unit = $layer_details[0]['unit'];
		foreach ($layer_details as $layer) {
			$insert_layer_history = array(
				'Booking_ID' => $BookingID,
				'Gate_IN_ID' => $GateInID,
				'name' => 'layer',
				'stage' => $layer['layer_number'],
				'value' => $layer['qty'],
				'UserID' => $layer['UserID'],
				'TransDate' => $layer['Transdate'],
			);
			$this->db->insert('tblGateMaster_history', $insert_layer_history);
		}
		$this->db->where('tblLayerMaster.BookingID', $BookingID);
		$this->db->where('tblLayerMaster.Gate_in_ID', $GateInID);
		$this->db->delete('tblLayerMaster');
		$qc_details = $this->GateControl_model->fetch_layer_qc_details($BookingID, $GateInID, 'U');
		foreach ($qc_details as $qc) {
			$insert_qc_history = array(
				'BookingID' => $BookingID,
				'Gate_in_ID' => $GateInID,
				'TType' => 'U',
				'ItemID' => $qc['ItemID'],
				'layer_number' => $qc['layer_number'],
				'ItemParameterID' => $qc['ItemParameterID'],
				'ParameterValue' => $qc['ParameterValue'],
				'UserID' => $qc['UserID'],
				'TransDate' => $qc['TransDate'],
			);
			$this->db->insert('tblQCParameterValues_history', $insert_qc_history);
		}
		$this->db->where('tblQCParameterValues.BookingID', $BookingID);
		$this->db->where('tblQCParameterValues.Gate_in_ID', $GateInID);
		$this->db->where('tblQCParameterValues.TType', 'U');
		$this->db->delete('tblQCParameterValues');
		$number_of_layer = 0;
		$total_bag = 0;
		$count = 1;
		for ($i = 0; $i < sizeof($item_array); $i++) {
			$insert_layer = array(
				'BookingID' => $BookingID,
				'Gate_in_ID' => $GateInID,
				'layer_number' => $count,
				'qty' => $item_array[$i]['layer_quantity'],
				'unit' => "BAG",
				'UserID' => $user_Details->staffid,
				'Transdate' => date('Y-m-d H:i:s'),
			);
			$this->db->insert('tblLayerMaster', $insert_layer);
			$number_of_layer++;
			$total_bag += $item_array[$i]['layer_quantity'];
			$inner_item_details = $item_array[$i]['layer_details'];
			for ($j = 0; $j < sizeof($inner_item_details); $j++) {
				if ($inner_item_details[$j]['qc_done_by'] == '') {
					$qc_by = $user_Details->staffid;
					$qc_date = date('Y-m-d H:i:s');
				} else {
					$qc_by = $inner_item_details[$j]['qc_done_by'];
					$qc_date = DateTime::createFromFormat('d/m/Y H:i:s', $inner_item_details[$j]['qc_done_date'])->format('Y-m-d H:i:s');
				}
				$insert_qc_details = array(
					'BookingID' => $BookingID,
					'Gate_in_ID' => $GateInID,
					'TType' => 'U',
					'ItemID' => $ItemID,
					'layer_number' => $count,
					'ItemParameterID' => $inner_item_details[$j]['item_id'],
					'ParameterValue' => $inner_item_details[$j]['item_value'],
					'UserID' => $user_Details->staffid,
					'TransDate' => date('Y-m-d H:i:s')
				);
				$this->db->insert('tblQCParameterValues', $insert_qc_details);
			}
			$count++;
		}
		$this->db->where('tblUnloadingMaster.BookingID', $BookingID);
		$this->db->where('tblUnloadingMaster.Gate_in_ID', $GateInID);
		$this->db->delete('tblUnloadingMaster');
		$insert_unloading_details = array(
			'BookingID' => $BookingID,
			'Gate_in_ID' => $GateInID,
			'total_bags' => $total_bag,
			'total_katta' => 0,
			'total_layers' => $number_of_layer
		);
		$this->db->insert('tblUnloadingMaster', $insert_unloading_details);
		$this->db->where('tblGateMaster.id', $id);
		$this->db->set('tblGateMaster.no_of_layers', ($count - 1));
		$this->db->update('tblGateMaster');
		set_alert('success', 'Layer Details Updated successfully');
		echo json_encode('success');
	}
}
