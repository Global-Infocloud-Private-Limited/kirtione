<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class Clients extends AdminController

	{

		

		//====================== New Code ==============================================

		// Get All Accounts

		public function index()

		{

			if (!has_permission_new('CustomerList', '', 'view')) {

				access_denied('customers');

			}

			$data['title'] = "Account List";

			$data['state'] = $this->clients_model->getallstate();

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			$data['itemdivision'] = $this->clients_model->get_itemDivision();

			// Customer groups

			$data['groups'] = $this->clients_model->get_groups();

			

			$this->load->view('admin/clients/manage', $data);

		}

		

		public function load_data_filter()

		{

			$post_data = $this->input->post();

			$data = $this->clients_model->get_table_on_load_filter($post_data);

			$html = '';

			

			

			$html .= '<div class="col-md-12 ">';

			if ($post_data['report_type'] == "1") {

				$html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';

				$html .= '<thead>';

				$html .= '<tr style="display:none;">';

				$html .= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">' . $company_detail->company_name . '</span><br><span style="font-size:10px;font-weight:600;">' . $company_detail->address . '</span><br><span style="font-size:10px;font-weight:600;">Customers Master</span><br><span class="report_for" style="font-size:10px;">Filters Distributor Type:' . $client_type . ', Distributor State:' . $distributor_state . ',Division:' . $division . ',  Responsible Person:' . $responsible_admin . ',  Status:' . $status . '</span></h5></td>';

				$html .= '</tr>';

				$html .= '<tr>';

				$html .= '<th >Sr.No </th>';

				$html .= '<th >Date </th>';

				$html .= '<th >AccountID </th>';

				$html .= '<th >Account Name</th>';

				$html .= '<th>User Type</th>';

				$html .= '<th>State</th>';

				$html .= '<th>City</th>';

				$html .= '<th>Address</th>';

				$html .= '<th>Aadhaar / PAN</th>';

				$html .= '<th>GST Number</th>';

				$html .= '<th>Registration Location</th>';

				$html .= '<th>Refer By</th>';

				$html .= '</tr>';

				$html .= '</thead>';

				$html .= '<tbody>';

				// print_r($data); die();

				$i = 1;

				foreach ($data as $value) {

					

					$html .= '<tr>';

					$html .= '<td>' . $i . '</td>';

					$html .= '<td>' . _d(substr($value["datecreated"], 0, 10)) . '</td>';

					$html .= '<td>' . $value["AccountID"] . '</td>';

					$html .= '<td>' . $value["company"] . '</td>';

					$html .= '<td>' . $value["CustomerTypeName"] . '</td>';

					if ($value['CustomerType'] == "1") {

						$State = $value["state"];

						$City = $value["dist"];

						$Address = $value["house"] . " " . $value["street"] . " " . $value["loc"] . " " . $value["po"] . ' ' . $value["subdist"] . " - " . $value["pincode"];

						$Aadhaar_pan = $value["adharno"];

						$Gst_no = "";

						} else {

						$State = $value["GSTState"];

						$City = "";

						$Address = $value["address"];

						$Aadhaar_pan = $value["panno"];

						$Gst_no = $value["gstin"];

					}

					$dots = "";

					if(strlen($Address) > 50){

						$dots = '...';

					}

					$html .= '<td>' . $value["state_name"] . '</td>';

					$html .= '<td>' . $City . '</td>';

					$html .= '<td title="'.$Address.'">' . substr($Address,0,50) .$dots. '</td>';

					$html .= '<td>' . $Aadhaar_pan . '</td>';

					$html .= '<td>' . $Gst_no . '</td>';

					$html .= '<td>' . $value["RegAddress"] . '</td>';

					$html .= '<td>' . $value["firstname"]." ". $value["lastname"]. '</td>';

					$html .= '</tr>';

					$i++;

				}

				$html .= '</tbody>';

				} else {

				$html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="30%">';

				$html .= '<thead>';

				$html .= '<tr style="display:none;">';

				$html .= '<td colspan="3" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">' . $company_detail->company_name . '</span><br><span style="font-size:10px;font-weight:600;">' . $company_detail->address . '</span><br><span style="font-size:10px;font-weight:600;">Customers Master</span><br><span class="report_for" style="font-size:10px;">Filters Distributor Type:' . $client_type . ', Distributor State:' . $distributor_state . ',Division:' . $division . ',  Responsible Person:' . $responsible_admin . ',  Status:' . $status . '</span></h5></td>';

				$html .= '</tr>';

				$html .= '<tr>';

				$html .= '<th >Sr.No </th>';

				$html .= '<th >Date </th>';

				$html .= '<th >Total Count</th>';

				$html .= '</tr>';

				$html .= '</thead>';

				$html .= '<tbody>';

				$i = 1;

				$TotalUser = 0;

				foreach ($data as $value) {

					$html .= '<tr>';

					$html .= '<td>' . $i . '</td>';

					$html .= '<td>' . _d(substr($value["datecreated"], 0, 10)) . '</td>';

					$html .= '<td>' . $value["TotalCount"] . '</td>';

					$TotalUser += $value["TotalCount"];

					$html .= '</tr>';

					$i++;

				}

				$html .= '<tr>';

				$html .= '<td colspan="2">Total</td>';

				$html .= '<td>' . $TotalUser . '</td>';

				

				$html .= '</tr>';

				$html .= '</tbody>';

			}

			

			$html .= '</table>';

			$html .= '</div>';

			$response = array('html' => $html);

			echo json_encode($response);

		}

		

		public function GetVillageList()

		{

		   $zip = $this->input->post('zip');

		   $Villagedata = $this->clients_model->VillageDetails($zip);

		   echo json_encode($Villagedata);

		}

		

		private function getAddressFromCoordinates($latitude, $longitude)

		{

			

			$apiKey = 'sQIqNbAj0nkWUYCtXDr9qmGQm9h6GduI';

			$apiUrl = "http://www.mapquestapi.com/geocoding/v1/address?key=" . $apiKey . "&location=" . $latitude . "," . $longitude . "&includeRoadMetadata=true&includeNearestIntersection=true";

			

			$json = file_get_contents($apiUrl);

			$data = json_decode($json);

			return $data;

		}

		/*  Add Edit Account */

		public function AddEditAccount($id = '')

		{

			if (!has_permission_new('TraderMaster', '', 'view')) {

				access_denied('customers');

			}

			if ($id !== "") {

				$AccountIDSetData = array(

                'AccountIDSet' => $id

				);

				$this->session->set_userdata($AccountIDSetData);

				} else {

				$this->session->unset_userdata('AccountIDSet');

			}

			$post_data = array();

			$this->load->model('currencies_model');

			$data['currencies'] = $this->currencies_model->get();

			$data['staff'] = $this->staff_model->get('', ['active' => 1]);

			$data['state'] = $this->clients_model->getallstate();

			$data['AccountType'] = $this->clients_model->GetAccountType();

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			$data['company_detail'] = $this->clients_model->get_company_detail();

			$data['itemdivision'] = $this->clients_model->get_itemDivision();

			$data['title'] = 'Add/Edit Accounts';

			$data['clusters'] = $this->clients_model->getClusters();

			

			$data['tds_sections'] = $this->clients_model->GetTDSSections();

			

			$this->load->view('admin/clients/AddEditTraderMaster', $data);

		}

		

		public function GetTDSSectionRate()

		{

			echo json_encode($this->clients_model->GetTDSSectionRate($this->input->post('tds_section_code')));

		}

		

		/* add new client*/

		public function SaveAccountID()

		{

			$AccountDetails = $this->input->post();

			$AccountDetails = $this->clients_model->SaveAccountDetails($AccountDetails);

			echo json_encode($AccountDetails);

		}

		

		/* Edit client*/

		public function UpdateAccountID($id = '')

		{

			$AccountDetails = $this->input->post();

			$AccountDetails = $this->clients_model->UpdateAccountDetails($AccountDetails);

			echo json_encode($AccountDetails);

		}

		

		

//================== Add Edit Farmer Page Load =================================

	public function AddEditFarmar($id = '')

	{

		if (!has_permission_new('FarmarMaster', '', 'view')) {

			access_denied('customers');

		}

		if ($id !== "") {

			$AccountIDSetData = array(

            'AccountIDSet' => $id

			);

			$this->session->set_userdata($AccountIDSetData);

			} else {

			$this->session->unset_userdata('AccountIDSet');

		}

		$post_data = array();

		$this->load->model('currencies_model');

		$data['currencies'] = $this->currencies_model->get();

		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['state'] = $this->clients_model->getallstate();

		$data['rootcompany'] = $this->clients_model->get_rootcompany();

		$data['GetRegion'] = $this->clients_model->Getregion();

		$data['title'] = 'Add/Edit Accounts';

		

		$data['company_detail'] = $this->clients_model->get_company_detail();

		$this->load->view('admin/clients/AddEditFarmar', $data);

	}

//========================= Save New Farmer ====================================

	public function SaveFarmar($id = '')

	{

		$AccountDetails = $this->input->post();

		$AccountDetails = $this->clients_model->SaveFarmarDetails($AccountDetails);

		echo json_encode($AccountDetails);

	}

		

		/* Edit Farmar*/

		public function UpdateFarmar($id = '')

		{

			$AccountDetails = $this->input->post();

			$AccountDetails = $this->clients_model->UpdateFarmarDetails($AccountDetails);

			echo json_encode($AccountDetails);

		}

		

		/* Get City List by State ID / ajax */

		public function GetState()

		{

			$data = $this->clients_model->getallstate();

			$html = '<option value="">Non Selected</option>';

			foreach ($data as $key => $value) {

				$html .= '<option value="' . $value['id'] . '" >' . $value['state_name'] . '</option>';

			}

			echo $html;

		}

		

		public function GetCity()

		{

			$StateID = $this->input->post('StateID');

			//Fetch State Shortcode

			$this->db->where('tblxx_statelist.short_name', $StateID);

			$stateID = $this->db->get('tblxx_statelist')->row();

			$CityList = $this->clients_model->GetCityListForCompanyMaster($stateID->short_name);

			echo json_encode($CityList);

		}

		

		public function GetCityFromState()

		{

			$StateID = $this->input->post('StateID');

			$CityList = $this->clients_model->GetCityList($StateID);

			$html = '<option value="">Non Selected</option>';

			foreach ($CityList as $key => $value) {

				$html .= '<option value="' . $value['id'] . '" >' . $value['city'] . '</option>';

			}

			echo $html;

		}

		

		/* Get Taluka List by City ID / ajax */

		public function GetTaluka()

		{

			$CityID = $this->input->post('CityID');

			$TalukaList = $this->clients_model->GetTaluka($CityID);

			echo json_encode($TalukaList);

		}

		

		public function AccountListPopUp()

		{

			$account_subgroup_table = $this->clients_model->GetAccountList();

			

			$html = "";

			foreach ($account_subgroup_table as $key => $value) {

				$html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

				$html .= '<td>' . $value["AccountID"] . '</td>';

				$html .= '<td>' . $value["company"] . '</td>';

				$html .= '<td>' . $value['Name'] . '</td>';

				$html .= '<td>' . $value["firstname"] . " " . $value["lastname"] . '</td>';

				$html .= '<td>' . $value["phonenumber"] . '</td>';

				$html .= '<td>' . $value["Pan"] . '</td>';

				$html .= '<td>' . $value["state_name"] . '</td>';

				$html .= '<td>' . $value["city_name"] . '</td>';

				$html .= '</tr>';

			}

			echo $html;

			//echo json_encode($account_data);

		}

		

		public function FarmarListPopUp()

		{

			$search = $this->input->post('search');

			$account_subgroup_table = $this->clients_model->GetFarmarList($search);

			// $html = "";

			// $i = 1;

			// foreach ($account_subgroup_table as $key => $value) {

			//     $html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

			//     $html .= '<td style="text-align:center;">' . $i . '</td>';

			//     $html .= '<td>' . $value["AccountID"] . '</td>';

			//     $html .= '<td>' . $value["firstname"] . " " . $value["lastname"] . '</td>';

			//     $html .= '<td>' . $value["phonenumber"] . '</td>';

			//     $html .= '<td>' . $value['adharno'] . '</td>';

			//     $html .= '<td>' . $value["state_name"] . '</td>';

			//     $html .= '<td>' . $value["city_name"] . '</td>';

			//     $html .= '</tr>';

			//     $i++;

			// }

			// echo $html;

			echo json_encode($account_subgroup_table);

		}

		

		public function GetAadhar()

		{

			$acc_id = $this->input->post('AccountID');

			$result = $this->clients_model->GetAadhar($acc_id);

			$front = base_url() . $result->aadhaar_front;

			$back = base_url() . $result->aadhaar_back;

			$html = "";

			if ($result->aadhaar_front = "" || $result->aadhaar_front == NULL) {

				

				} else {

				$html .= "<div class='col-md-6'>";

				$html .= "<label for='aadhar_front' class='form-label'>Aadhaar Front</label><br>";

				$html .= "<a href='" . $front . "' target='_blank'><img src='" . $front . "' width='120px' height='120px' style='border: 1px solid;'></a>";

				$html .= "</div>";

			}

			

			if ($result->aadhaar_back = "" || $result->aadhaar_back == NULL) {

				

				} else {

				$html .= "<div class='col-md-6'>";

				$html .= "<label for='aadhar_back' class='form-label'>Aadhaar Back</label><br>";

				$html .= "<a href='" . $back . "' target='_blank'><img src='" . $back . "' width='120px' height='120px' style='border: 1px solid;'></a>";

				$html .= "</div>";

			}

			

			echo $html;

		}

		

		public function GetAadharProfile()

		{

			$acc_id = $this->input->post('AccountID');

			$result = $this->clients_model->GetAadhar($acc_id);

			

			$html = "";

			if ($result->aadhar_image) {

				

				$url = base_url() . 'admin/clients/GetImage?AccountID=' . $result->AccountID . '&Type=Aadhaar_profile';

				$img_file = 'raju.png';

				$src = 'data: ' . mime_content_type($img_file) . ';base64,' . $result->aadhar_image;

				

				$html .= "<div class='col-md-6'>";

				$html .= "<label for='aadhar_back' class='form-label'>User Profile</label><br>";

				$html .= '<a href="' . $url . '" target="_blank"><img src="' . $src . '" width="120px" height="120px"></a></a>';

				$html .= "</div>";

			}

			echo $html;

			

		}

		

		public function UpdateGStAddress()

		{

			//$acc_id = $this->input->post('AccountID');

			$AccountID = '9823333495';

			$result = $this->clients_model->GetAccountGstDetails($AccountID);

			echo "<pre>";

			//print_r($result);

			$sr = 1;

			$curl = curl_init();

			// $tokan = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
			$tokan = 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';

			//foreach($result as $key => $value){

			//if($value["IsPrimary"] == "0"){

			echo $sr . " - " . $value["gstin"];

			echo "<br>";

			$sr++;

			$url = 'https://kyc-api.aadhaarkyc.io/api/v1/corporate-otp/gstin/init';

			

			$fields = array(

            'id_number' => "27AACCK6480D1ZK",

            'hsn_info_get' => 'false'

			);

			

			$json_string = json_encode($fields);

			curl_setopt($curl, CURLOPT_URL, $url);

			curl_setopt($curl, CURLOPT_POST, TRUE);

			curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);

			// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo'));
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo'));

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$data = curl_exec($curl);

			$jsondecode = json_decode($data, true);

			print_r($jsondecode);

			echo "<br>";

			//primary address

			//print_r($jsondecode['data']['details']['contact_details']['principal']['address']);

			$Pr_address = $jsondecode['data']['details']['contact_details']['principal']['address'];

			$email = $jsondecode['data']['details']['contact_details']['principal']['email'];

			$mobile = $jsondecode['data']['details']['contact_details']['principal']['mobile'];

			$nature_of_business = $jsondecode['data']['details']['contact_details']['principal']['nature_of_business'];

			$business_name = $jsondecode['data']['details']['contact_details']['business_name'];

			$constitution_of_business = $jsondecode['data']['details']['contact_details']['constitution_of_business'];

			$taxpayer_type = $jsondecode['data']['details']['contact_details']['taxpayer_type'];

			$date_of_registration = $jsondecode['data']['details']['contact_details']['date_of_registration'];

			$gstin_status = $jsondecode['data']['details']['contact_details']['gstin_status'];

			

			// secondary address

			$sec_add = $jsondecode['data']['details']['contact_details']['additional'];

			print_r($sec_add);

			//}

			

			//}

			curl_close($curl);

			die;

			$html = "";

			if ($result->aadhar_image) {

				

				$url = base_url() . 'admin/clients/GetImage?AccountID=' . $result->AccountID . '&Type=Aadhaar_profile';

				$img_file = 'raju.png';

				$src = 'data: ' . mime_content_type($img_file) . ';base64,' . $result->aadhar_image;

				

				$html .= "<div class='col-md-6'>";

				$html .= "<label for='aadhar_back' class='form-label'>User Profile</label><br>";

				$html .= '<a href="' . $url . '" target="_blank"><img src="' . $src . '" width="120px" height="120px"></a></a>';

				$html .= "</div>";

			}

			echo $html;

		}

		

		

		

		//==============================================================================

		/* List all clients */

		

		public function export_party_list()

		{

			

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			$post_data = $this->input->post();

			$data = $this->clients_model->get_table_on_load_filter($post_data);

			$this->load->model('hsn_master_model');

			$selected_company_details = $this->hsn_master_model->get_company_detail();

			

			$writer = new XLSXWriter();

			

			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);

			

			$address = $selected_company_details->address;

			$company_addr = array($address, );

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 5);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);

			

			if ($post_data['client_type'] == "1") {

				$client_type = "Farmer";

				} else if ($post_data['client_type'] == "2") {

				$client_type = "Broker";

				} else if ($post_data['client_type'] == "3") {

				$client_type = "Trader";

				} else if ($post_data['client_type'] == "4") {

				$client_type = "Corporate/Processor";

				} else {

				$client_type = "";

			}

			

			$filtermsg = 'Filters : User Type:' . $client_type;

			$filter = array($filtermsg);

			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 5);  //merge cells

			$writer->writeSheetRow('Sheet1', $filter);

			

			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);

			

			$set_col_tk = [];

			$set_col_tk["Ac Code"] = 'AccountID';

			$set_col_tk["Firm Name"] = 'Account Name';

			$set_col_tk["State"] = 'State';

			$set_col_tk["City"] = 'City';

			$set_col_tk["Town"] = 'Address';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);

			

			foreach ($data as $k => $value) {

				$list_add = [];

				$list_add[] = $value["AccountID"];

				$list_add[] = $value["company"];

				

				$list_add[] = $value["state"];

				$city_name = get_city_name($value['dist']);

				if ($city_name->city_name) {

					$city = $city_name->city_name;

					} else {

					$city = $value['dist'];

				}

				$list_add[] = $city;

				$list_add[] = nl2br($value["loc"] . " " . $value["street"] . " " . $value["house"]);

				

				

				$writer->writeSheetRow('Sheet1', $list_add);

			}

			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);

				}

			}

			$filename = 'Party_list.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

            'site_url' => site_url(),

            'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;

		}

		

		

		public function table()

		{

			if (!has_permission_new('customers', '', 'view')) {

				if (!has_permission_new('customers', '', 'create')) {

					ajax_access_denied();

				}

			}

			

			$this->app->get_table_data('clients');

		}

		

		public function no_show()

		{

			

			if (!has_permission_new('no_show', '', 'view')) {

				ajax_access_denied();

			}

			if ($this->input->post()) {

				

				if (!has_permission_new('no_show', '', 'edit')) {

					ajax_access_denied();

				}

				$data = $this->input->post();

				/*echo "<pre>";

					print_r($data["user_id"]);

				die;*/

				

				$selected_company = $this->session->userdata('root_company');

				//$this->db->where('AccountID', $value);

				$this->db->where('PlantID', $selected_company);

				$this->db->update(db_prefix() . 'clients', [

                'no_show' => NULL,

				]);

				

				$regExp = '.*;s:[0-9]+:"' . $selected_company . '".*';

				$this->db->where('tblstaff.staff_comp REGEXP', $regExp);

				$this->db->update(db_prefix() . 'staff', [

                'no_show' => NULL,

				]);

				

				foreach ($data["user_id"] as $value) {

					

					$this->db->where('AccountID', $value);

					$this->db->where('PlantID', $selected_company);

					$this->db->update(db_prefix() . 'clients', [

                    'no_show' => 1,

					]);

					if ($this->db->affected_rows() > 0) {

						

						} else {

						$this->db->where('AccountID', $value);

						// $this->db->where('PlantID', $selected_company);

						$this->db->update(db_prefix() . 'staff', [

                        'no_show' => 1,

						]);

					}

				}

				

			}

			$data['title'] = "No show Accounts";

			$data['NoShowclients'] = $this->clients_model->GetNoShowclients();

			$data['NonNoShowclients'] = $this->clients_model->GetNonNoShowclients();

			$data['NoShowStaff'] = $this->clients_model->GetNoShowstaff();

			$data['NonNoShowStaff'] = $this->clients_model->GetNonNoShowstaff();

			

			/*echo "<pre>";

				print_r($data['all_staff']);

				print_r($data['all_clients']);

			die;*/

			$this->load->view('admin/clients/no_show', $data);

		}

		

		public function all_contacts()

		{

			if ($this->input->is_ajax_request()) {

				$this->app->get_table_data('all_contacts');

			}

			

			if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {

				$this->load->model('gdpr_model');

				$data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();

			}

			

			$data['title'] = _l('customer_contacts');

			$this->load->view('admin/clients/all_contacts', $data);

		}

		

		public function all_beats()

		{

			if ($this->input->is_ajax_request()) {

				$this->app->get_table_data('tblbeat');

			}

			

			if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {

				$this->load->model('gdpr_model');

				$data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();

			}

			

			$data['title'] = _l('customer_contacts');

			$data['allbeat'] = $this->clients_model->get_all_beat();

			$this->load->view('admin/clients/all_beats', $data);

		}

		

		/* Only add new client*/

		

		public function client_add()

		{

			if (!has_permission_new('customers', '', 'view')) {

				if ($id != '' && !is_customer_admin($id)) {

					access_denied('customers');

				}

			}

			

			if ($this->input->post() && !$this->input->is_ajax_request()) {

				

				if (!has_permission_new('customers', '', 'create')) {

					access_denied('customers');

				}

				$data = $this->input->post();

				$AccountID = trim($data["AccountID"]);

				

				$Account_details_code = $this->clients_model->check_AccountID($AccountID);

				if ($Account_details_code == true) {

					set_alert('warning', "AccountID already exists");

					redirect(admin_url('clients/client'));

				}

				

				$rootcompany = $this->clients_model->get_rootcompany();

				$itemdivision = $this->clients_model->get_itemDivision();

				

				

				$itemDivision = array();

				foreach ($itemdivision as $item_Division) {

					

					$item_division_id = "itemdiv" . $item_Division["id"];

					$Selected_item_division_ids = $data[$item_division_id];

					if ($Selected_item_division_ids == $item_Division["id"]) {

						

						array_push($itemDivision, $item_Division["id"]);

						unset($data[$item_division_id]);

					}

				}

				

				

				

				$itemDivision_comp = array();

				foreach ($itemdivision as $item_Division) {

					

					$item_division_comp_id = "itemdivisioncomp" . $item_Division["id"];

					$Selected_item_division_comp_ids = $data[$item_division_comp_id];

					

					if ($Selected_item_division_comp_ids !== "") {

						

						$itemDivision_comp[$item_Division["id"]] = $data[$item_division_comp_id];

						unset($data[$item_division_comp_id]);

						} else {

						unset($data[$item_division_comp_id]);

					}

				}

				

				/*echo "<pre>";

					

					print_r($data['customer_admins']);

				die;*/

				$company_assigned = array();

				foreach ($rootcompany as $r_company) {

					

					$company_assigned_id = "company_assigned" . $r_company["id"];

					$Selected_company_assigned_ids = $data[$company_assigned_id];

					if ($Selected_company_assigned_ids == $r_company["id"]) {

						

						array_push($company_assigned, $r_company["id"]);

						unset($data[$company_assigned_id]);

					}

				}

				

				$company_assigned_sales_p = array();

				$company_sales_person_assign_data = array();

				foreach ($rootcompany as $r_company) {

					

					$company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];

					$Selected_company_assigned_sales_p_ids = $data[$company_assigned_sales_p_id];

					//print_r($Selected_company_assigned_sales_p_ids);

					

					if ($Selected_company_assigned_sales_p_ids !== "") {

						

						foreach ($Selected_company_assigned_sales_p_ids as $assigned_ids) {

							$company_assigned_sales_p[$r_company["id"]] = $assigned_ids;

							$company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];

							

							$company_sales_person_assign_data[$r_company["id"]] = $data[$company_assigned_sales_p_id];

						}

						

						unset($data[$company_assigned_sales_p_id]);

						} else {

						unset($data[$company_assigned_sales_p_id]);

					}

				}

				

				

				

				

				$company_assigned_opn_bal = array();

				foreach ($rootcompany as $r_company) {

					

					$company_assigned_opn_bal_id = "opening_bal" . $r_company["id"];

					$Selected_company_assigned_opn_bal_ids = $data[$company_assigned_opn_bal_id];

					

					if ($Selected_company_assigned_opn_bal_ids !== "") {

						

						$company_assigned_opn_bal[$r_company["id"]] = $data[$company_assigned_opn_bal_id];

						unset($data[$company_assigned_opn_bal_id]);

						} else {

						unset($data[$company_assigned_opn_bal_id]);

					}

				}

				

				$company_assigned_drcr = array();

				foreach ($rootcompany as $r_company) {

					

					$company_assigned_drcr_id = "drcr" . $r_company["id"];

					//$Selected_company_assigned_drcr_ids = $data[$company_assigned_drcr_id];

					

					if ($data[$company_assigned_drcr_id] !== "") {

						

						$company_assigned_drcr[$r_company["id"]] = $data[$company_assigned_drcr_id];

						unset($data[$company_assigned_drcr_id]);

						} else {

						unset($data[$company_assigned_drcr_id]);

					}

				}

				

				$newcompany_assigned_sales_p = array();

				$newcompany_assigned_sales_p = array_filter($company_assigned_sales_p, 'strlen');

				

				/*print_r($company_assigned_sales_p);

					//print_r($company_assigned);

				die;*/

				

				$data["itemdivision"] = serialize($itemDivision);

				$data["itemdivision_comp"] = serialize($itemDivision_comp);

				

				$data["company_assigned"] = serialize($company_assigned);

				$data["company_assigned_staff"] = serialize($company_assigned_sales_p);

				$data["opening_bal"] = serialize($company_assigned_opn_bal);

				$data["drcr"] = serialize($company_assigned_drcr);

				/*echo "<pre>";

					print_r($data["company_assigned_staff"]);

					print_r($data);

				die;*/

				$contacts_fields["kms"] = $data['kms'];

				unset($data['kms']);

				

				$contacts_fields["FLNO1"] = $data['FLNO1'];

				unset($data['FLNO1']);

				

				$contacts_fields["Pan"] = $data['Pan'];

				unset($data['Pan']);

				

				$contacts_fields["Aadhaarno"] = $data['Aadhaarno'];

				unset($data['Aadhaarno']);

				

				$contacts_fields["istcs"] = $data['istcs'];

				unset($data['istcs']);

				

				$contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

				unset($data['TcsStartDate']);

				

				/*$contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

				unset($data['TcsStartDate']);*/

				

				$contacts_fields["BalancesYN"] = $data['BalancesYN'];

				unset($data['BalancesYN']);

				

				$contacts_fields["BalancelYN"] = $data['BalancelYN'];

				unset($data['BalancelYN']);

				

				if (isset($data['profile_image'])) {

					$contacts_fields["profile_image"] = $data['profile_image'];

					unset($data['profile_image']);

				}

				

				if (isset($data['title'])) {

					$contacts_fields["title"] = $data['title'];

					unset($data['title']);

				}

				if (isset($data['firstname'])) {

					$contacts_fields["firstname"] = $data['firstname'];

					unset($data['firstname']);

				}

				if (isset($data['lastname'])) {

					$contacts_fields["lastname"] = $data['lastname'];

					unset($data['lastname']);

				}

				

				if (isset($data['email'])) {

					$contacts_fields["email"] = $data['email'];

					unset($data['email']);

				}

				

				if (isset($data['save_and_add_contact'])) {

					unset($data['save_and_add_contact']);

					$save_and_add_contact = true;

				}

				if (isset($data['profile_image'])) {

					$contacts_fields["profile_image"] = $data['profile_image'];

					unset($data['profile_image']);

				}

				

				if (isset($data['title'])) {

					$contacts_fields["title"] = $data['title'];

					unset($data['title']);

				}

				if (isset($data['firstname'])) {

					$contacts_fields["firstname"] = $data['firstname'];

					unset($data['firstname']);

				}

				if (isset($data['lastname'])) {

					$contacts_fields["lastname"] = $data['lastname'];

					unset($data['lastname']);

				}

				

				if (isset($data['email'])) {

					$contacts_fields["email"] = $data['email'];

					unset($data['email']);

				}

				if (isset($data['phonenumber'])) {

					$contacts_fields["phonenumber"] = $data['phonenumber'];

					//unset($data['phonenumber']);

				}

				

				

				

				//client table array

				

				if (isset($data['AccountID'])) {

					$client_fields["AccountID"] = $data['AccountID'];

					$account_id = $data['AccountID'];

					unset($data['AccountID']);

				}

				if (isset($data['CtrlAccountID'])) {

					$client_fields["CtrlAccountID"] = $data['CtrlAccountID'];

					unset($data['CtrlAccountID']);

				}

				

				if (isset($data['company'])) {

					$client_fields["company"] = $data['company'];

					unset($data['company']);

				}

				

				if (isset($data['city'])) {

					$client_fields["city"] = $data['city'];

					unset($data['city']);

				}

				

				if (isset($data['city'])) {

					$client_fields["city"] = $data['city'];

					unset($data['city']);

				}

				

				if (isset($data['address'])) {

					$client_fields["address"] = $data['address'];

					unset($data['address']);

				}

				

				if (isset($data['Address3'])) {

					$client_fields["Address3"] = $data['Address3'];

					unset($data['Address3']);

				}

				

				if (isset($data['state'])) {

					$client_fields["state"] = $data['state'];

					unset($data['state']);

				}

				

				if (isset($data['zip'])) {

					$client_fields["zip"] = $data['zip'];

					unset($data['zip']);

				}

				

				if (isset($data['groups_in'])) {

					$client_fields["DistributorType"] = $data['groups_in'];

					unset($data['groups_in']);

				}

				

				if (isset($data['MaxCrdAmt'])) {

					$client_fields["MaxCrdAmt"] = $data['MaxCrdAmt'];

					unset($data['MaxCrdAmt']);

				}

				

				if (isset($data['MaxDays'])) {

					$client_fields["MaxDays"] = $data['MaxDays'];

					unset($data['MaxDays']);

				}

				

				if (isset($data['ActSalestype'])) {

					$client_fields["ActSalestype"] = $data['ActSalestype'];

					unset($data['ActSalestype']);

				}

				

				if (isset($data['SalesFrequency'])) {

					$client_fields["SalesFrequency"] = $data['SalesFrequency'];

					unset($data['SalesFrequency']);

				}

				

				if (isset($data['Blockyn'])) {

					$client_fields["Blockyn"] = $data['Blockyn'];

					unset($data['Blockyn']);

				}

				

				if (isset($data['phonenumber'])) {

					$client_fields["phonenumber"] = $data['phonenumber'];

					//unset($data['Blockyn']);

				}

				

				if (isset($data['altphonenumber'])) {

					$client_fields["altphonenumber"] = $data['altphonenumber'];

					//unset($data['Blockyn']);

				}

				if (isset($data['website'])) {

					$client_fields["website"] = $data['website'];

					unset($data['website']);

				}

				if (isset($data['bill_till_bal'])) {

					$client_fields["bill_till_bal"] = $data['bill_till_bal'];

					unset($data['bill_till_bal']);

				}

				

				if (isset($data['billing_street'])) {

					$client_fields["billing_street"] = $data['billing_street'];

					unset($data['billing_street']);

				}

				

				if (isset($data['billing_city'])) {

					$client_fields["billing_city"] = $data['billing_city'];

					unset($data['billing_city']);

				}

				if (isset($data['billing_state'])) {

					$client_fields["billing_state"] = $data['billing_state'];

					unset($data['billing_state']);

				}

				

				if (isset($data['billing_zip'])) {

					$client_fields["billing_zip"] = $data['billing_zip'];

					unset($data['billing_zip']);

				}

				if (isset($data['billing_country'])) {

					$client_fields["billing_country"] = $data['billing_country'];

					unset($data['billing_country']);

				}

				if (isset($data['shipping_street'])) {

					$client_fields["shipping_street"] = $data['shipping_street'];

					unset($data['shipping_street']);

				}

				if (isset($data['shipping_city'])) {

					$client_fields["shipping_city"] = $data['shipping_city'];

					unset($data['shipping_city']);

				}

				if (isset($data['shipping_state'])) {

					$client_fields["shipping_state"] = $data['shipping_state'];

					unset($data['shipping_state']);

				}

				if (isset($data['shipping_zip'])) {

					$client_fields["shipping_zip"] = $data['shipping_zip'];

					unset($data['shipping_zip']);

				}

				

				if (isset($data['shipping_country'])) {

					$client_fields["shipping_country"] = $data['shipping_country'];

					unset($data['shipping_country']);

				}

				

				if (isset($data['vat'])) {

					$client_fields["vat"] = $data['vat'];

					unset($data['vat']);

				}

				

				if (isset($data['company_assigned'])) {

					$client_fields["company_assigned"] = $data['company_assigned'];

					unset($data['company_assigned']);

				}

				if (isset($data['company_assigned_staff'])) {

					$client_fields["company_assigned_staff"] = $data['company_assigned_staff'];

					unset($data['company_assigned_staff']);

				}

				if (isset($data['country'])) {

					$client_fields["country"] = $data['country'];

					unset($data['country']);

				}

				

				if (isset($data['country'])) {

					$client_fields["country"] = $data['country'];

					unset($data['country']);

				}

				if (isset($data['default_currency'])) {

					$client_fields["default_currency"] = $data['default_currency'];

					unset($data['default_currency']);

				}

				

				if (isset($data['StationName'])) {

					$client_fields["StationName"] = $data['StationName'];

					unset($data['StationName']);

				}

				if (isset($data['location_type'])) {

					$LocationTypeID = $data['location_type'];

					unset($data['location_type']);

				}

				

				

				// echo "<pre>";

				

				//print_r($data);

				//print_r($client_fields);

				//print_r($contacts_fields);

				//print_r($itemDivision_comp);

				/* print_r($company_assigned_opn_bal);

					print_r($company_assigned_drcr);

					print_r($company_assigned_sales_p);

					echo $company_assigned_drcr[1];

					foreach($company_assigned_drcr as $key1 => $value1){

					

					echo $company_assigned_drcr[$key1];

					echo "<br>";

				}*/

				$routes = $data["route"];

				unset($data["route"]);

				

				

				// die;

				$selected_company = $this->session->userdata('root_company');

				$fy = $this->session->userdata('finacial_year');

				foreach ($rootcompany as $r_company) {

					$client_fields['addedfrom'] = get_staff_user_id();

					$client_fields['UserID2'] = get_staff_user_id();

					$client_fields["PlantID"] = $r_company["id"];

					$client_fields['StartDate'] = date('Y-m-d H:i:s');

					$client_fields['SubActGroupID'] = "60001004";

					$customer_id = $this->clients_model->add($client_fields);

					

					$this->db->insert(db_prefix() . 'accountlocations', [

                    'PlantID' => $r_company["id"],

                    'LocationTypeID' => $LocationTypeID,

                    'AccountID' => $account_id,

					]);

				}

				if ($customer_id) {

					

					

					

					foreach ($itemDivision_comp as $key => $value) {

						$this->db->insert(db_prefix() . 'accountitemdiv', [

                        'ItemDivID' => $key,

                        'PlantID' => $selected_company,

                        'plant_assign' => $value,

                        'AccountID' => $account_id,

						]);

						

					}

					

					foreach ($routes as $value) {

						# code...

						$route_data = array(

                        "PlantID" => $selected_company,

                        "AccountID" => $account_id,

                        "RouteID" => $value

						);

						$this->db->insert(db_prefix() . 'accountroutes', $route_data);

					}

					foreach ($rootcompany as $r_company) {

						$contacts_fields["PlantID"] = $r_company["id"];

						$contacts_fields["AccountID"] = $account_id;

						$this->db->insert(db_prefix() . 'contacts', $contacts_fields);

					}

					

					foreach ($newcompany_assigned_sales_p as $key => $value) {

						

						

						/*if($value){

						foreach($value as $val){*/

						//echo $key . " = >" .$val;

						$this->db->insert(db_prefix() . 'customer_admins', [

                        'customer_id' => $account_id,

                        'staff_id' => $value,

                        'company_id' => $key,

                        'date_assigned' => date('d-m-Y H:i:s'),

						]);

						/* }

						}*/

						

					}

					$bal_array = array();

					$non_bal_array = array();

					foreach ($company_assigned_opn_bal as $key1 => $value1) {

						

						$value_type = $company_assigned_drcr[$key1];

						array_push($bal_array, $key1);

						

						if ($value_type == "DR") {

							$value1 = "-" . $value1;

						}

						

						$this->db->insert(db_prefix() . 'accountbalances', [

                        'PlantID' => $key1,

                        'FY' => $fy,

                        'AccountID' => $account_id,

                        'BAL1' => $value1,

                        'UserID2' => get_staff_user_id(),

						]);

					}

					

					foreach ($company_assigned_drcr as $key2 => $value2) {

						

						if (in_array($key2, $bal_array)) {

							

							} else {

							array_push($non_bal_array, $key2);

						}

						

					}

					

					foreach ($non_bal_array as $value3) {

						# code...

						$this->db->insert(db_prefix() . 'accountbalances', [

                        'PlantID' => $value3,

                        'FY' => $fy,

                        'AccountID' => $account_id,

                        'BAL1' => "0.00",

                        'UserID2' => get_staff_user_id(),

						]);

					}

					set_alert('success', _l('added_successfully', _l('client')));

					redirect(admin_url('clients/client/' . $account_id));

				}

			}

		}

		

		

		/* Get Account Details by AccountID / ajax */

		public function GetAccountDetailByID()

		{

			$AccountID = $this->input->post('AccountID');

			$AccountDetails = $this->clients_model->get_AccountDetails($AccountID);

			if ($AccountDetails) {

				$BankDetails = $this->clients_model->GetAccountBankDetails($AccountID);

				$html = '';

				foreach ($BankDetails as $key => $value) {

					$html .= '<tr>';

					if ($value["IsPrimary"] == '1') {

						$status = 'Yes';

					}

					if ($value["IsPrimary"] == '0') {

						$status = 'No';

					}

					$html .= '<td>' . $value["bankName"] . '</td>';

					$html .= '<td>' . $value["branchName"] . '</td>';

					$html .= '<td>' . $value["AaccountName"] . '</td>';

					$html .= '<td>' . $value["accountNumber"] . '</td>';

					$html .= '<td>' . $value["ifsc"] . '</td>';

					$html .= '<td>' . $status . '</td>';

					$url = base_url() . 'admin/clients/GetImage?AccountID=' . $value["id"] . '&Type=bank_cheque';

					$img_file = 'raju.png';

					$src = 'data: ' . mime_content_type($img_file) . ';base64,' . $value["cheque_image"];

					$html .= '<td><a href="' . $url . '" target="_blank"><img src="' . $src . '" width="120px" height="120px"></a></td>';

					$html .= '</tr>';

				}

				$AccountDetails->BankData = $html;

				

				$GSTList = $this->clients_model->GetAccountGstDetails($AccountID);

				$html = '';

				foreach ($GSTList as $key => $value) {

					

					$html .= '<tr>';

					if ($value["IsPrimary"] == '1') {

						$status = 'Yes';

					}

					if ($value["IsPrimary"] == '0') {

						$status = 'No';

					}

					$html .= '<td>' . $value["gstin"] . '</td>';

					$html .= '<td>' . $value["state"] . '</td>';

					$html .= '<td>' . $value["business_name"] . '</td>';

					$html .= '<td>' . $value["address"] . '</td>';

					$html .= '<td><input type="radio" name="IsPrimary" value="' . $value["id"] . '" '. ($value["IsPrimary"] == '1' ? 'checked' : '') . '></td>';

					$html .= '</tr>';

				}

				$AccountDetails->GSTList = $html;

			}

			echo json_encode($AccountDetails);

		}

		

		public function VerifyAadhar()

		{

			$curr_date = date('Y-m-d');

			$data = array(

            'aadhaar_number' => $this->input->post('Aadhaarno'),

            'AccountID' => $this->input->post('AccountID'),

            'aadhaar_verified_date' => $curr_date,

            'veryfiedUserID' => $this->session->userdata('username'),

			);

			

			$result = $this->clients_model->VerifyAadharDb($data);

			echo json_encode($result);

		}

		

		public function sendAadhaarOtp()

		{

			$aadhaarNumber = $this->input->post('Aadhaarno');

			$result = $this->clients_model->checkAadhar($aadhaarNumber);

			if (empty($result)) {

				$curl = curl_init();

				// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
				$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';

				curl_setopt_array(

                $curl,

                array(

				CURLOPT_URL => 'https://kyc-api.aadhaarkyc.io/api/v1/aadhaar-validation/aadhaar-validation',

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => '',

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 0,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => 'POST',

				CURLOPT_POSTFIELDS => '{

            	"id_number":  "' . $aadhaarNumber . '"

				}',

				CURLOPT_HTTPHEADER => array(

				'Content-Type: application/json',

				'Authorization: Bearer ' . $token . ''

				),

                )

				);

				

				$response = curl_exec($curl);

				curl_close($curl);

				echo ($response);

				} else {

				echo json_encode(false);

			}

		}

		public function sendAadhaarOtp2()

		{

			$aadhaarNumber = $this->input->post('Aadhaarno');

			$result = $this->clients_model->checkAadhar($aadhaarNumber);

			if (empty($result)) {

				$curl = curl_init();

				// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
				$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';


				curl_setopt_array(

                $curl,

                array(

				CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/aadhaar-v2/generate-otp',

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => '',

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 0,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => 'POST',

				CURLOPT_POSTFIELDS => '{

            	"id_number":  "' . $aadhaarNumber . '"

				}',

				CURLOPT_HTTPHEADER => array(

				'Content-Type: application/json',

				'Authorization: Bearer ' . $token . ''

				),

                )

				);

				

				$response = curl_exec($curl);

				curl_close($curl);

				echo ($response);

				} else {

				echo json_encode(false);

			}

		}

		

		public function verifyAadhaarOtp()

		{

			$aadharClientID = $this->input->post('aadharClientID');

			$aadharOtp = $this->input->post('aadharOtp');

			// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
			$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';
			

			$curl = curl_init();

			

			curl_setopt_array(

            $curl,

            array(

			CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/aadhaar-v2/submit-otp',

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => '',

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 0,

			CURLOPT_FOLLOWLOCATION => true,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => 'POST',

			CURLOPT_POSTFIELDS => '{

        	"client_id": "' . $aadharClientID . '",

        	"otp": "' . $aadharOtp . '"

			}',

			CURLOPT_HTTPHEADER => array(

			'Content-Type: application/json',

			'Authorization: Bearer ' . $token . ''

			),

            )

			);

			

			$response = curl_exec($curl);

			

			curl_close($curl);

			echo $response;

		}

		

		public function fetchBankDetailsFromIFSC()

		{

			$ifsc_code = $this->input->post('ifsc_code');

			$curl = curl_init();

			curl_setopt_array(

            $curl,

            array(

			CURLOPT_URL => 'https://ifsc.razorpay.com/' . $ifsc_code . '',

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

			echo $response;

		}

		

		public function verifyBankAccount()

		{

			

			$reaccount_number = $this->input->post('reaccount_number');

			$ifsc_code = $this->input->post('ifsc_code');

			// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
			$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';
			

			$curl = curl_init();

			curl_setopt_array(

            $curl,

            array(

			CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/bank-verification/',

			//CURLOPT_URL => 'https://kyc-api.aadhaarkyc.io/api/v1/bank-verification/',

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => '',

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 0,

			CURLOPT_FOLLOWLOCATION => true,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => 'POST',

			CURLOPT_POSTFIELDS => '{

        	"id_number": "' . $reaccount_number . '",

        	"ifsc": "' . $ifsc_code . '",

        	"ifsc_details": true

			}',

			CURLOPT_HTTPHEADER => array(

			'Content-Type: application/json',

			'Authorization: Bearer ' . $token . ''

			),

            )

			);

			

			$response = curl_exec($curl);

			

			curl_close($curl);

			echo $response;

		}

		public function verifyGSTNNumber()

		{

			

			$gst_no = $this->input->post('gst_no');

			// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
			$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';
			

			$curl = curl_init();

			curl_setopt_array(

            $curl,

            array(

			CURLOPT_URL => 'https://kyc-api.aadhaarkyc.io/api/v1/corporate-otp/gstin/init',

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => '',

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 0,

			CURLOPT_FOLLOWLOCATION => true,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => 'POST',

			CURLOPT_POSTFIELDS => '{

        	"id_number": "' . $gst_no . '",

        	"hsn_info_get": true

			}',

			CURLOPT_HTTPHEADER => array(

			'Content-Type: application/json',

			'Authorization: Bearer ' . $token . ''

			),

            )

			);

			

			$response = curl_exec($curl);

			

			curl_close($curl);

			echo $response;

		}

		

		public function addBankDetails()

		{

			$isDefault = 0;

			if($this->input->post('is_primary') == 'Y'){

				$isDefault = 1;

			}

			$cheque_image = $this->input->post('cheque_image');

			

			if ($cheque_image && $cheque_image != 'NA') {

				$image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $cheque_image));

				$image_name = md5(uniqid(rand(), true));

				$filename = $image_name . '.png';

				

				$directory = 'assets/Upload_doc/' . $AccountID;

				if (!file_exists($directory)) {

					mkdir($directory, 0777, true);

				}

				

				$path1 = $directory . '/' . $filename;

				file_put_contents($path1, $image_data);

				$resultString = substr($cheque_image, 9);

				} else {

				$path1 = '';

				$resultString = 'NA';

			}

			

			$Bankdata = array(

                "ifsc" => $this->input->post('ifsc_code'),

                "bankName" => $this->input->post('bank_name'),

                "branchName" => $this->input->post('bank_branch'),

                "AaccountName" => $this->input->post('account_name'),

                "accountNumber" => $this->input->post('account_number'),

                "AccountID" => $this->input->post('AccountID'),

                "IsPrimary" => $isDefault,

                "IsManualVerified" => $this->input->post('is_act_validate'),

                "TransDate" => date('Y-m-d H:i:s'),

                "cheque_image" => $resultString,

			);

			$Clientdata = array(

            "AccountFor" => $this->input->post('accountFor'),

            "KYCStatus" => 6,

			);

			if ($this->db->insert(db_prefix() . 'BankDetails', $Bankdata)) {

				$insert_id = $this->db->insert_id();

				

				$this->db->where('AccountID', $this->input->post('AccountID'));

				$this->db->update(db_prefix() . 'clients', $Clientdata);

				

				$Updatedata = array(

            "IsPrimary" => 0,

			);

				if($isDefault == 1){

					$this->db->where('AccountID', $this->input->post('AccountID'));

					$this->db->where('id != ', $insert_id);

					$this->db->update(db_prefix() . 'BankDetails', $Updatedata);

				}

				echo json_encode(true);

				} else {

				echo json_encode(false);

			}

		}

		public function addGSTDetails()

		{

			$isDefault = 0;

			if($this->input->post('is_primary') == 'Y'){

				$isDefault = 1;

			}

			

			$statecode = substr($this->input->post('gst_no'), 0, 2);

			if ($statecode[0] == '0') {

				$stateid = $statecode[1];

				} else {

				$stateid = $statecode;

			}

			

			// Get State Details

			$this->db->where('id', $stateid);

			$state = $this->db->get(db_prefix() . 'xx_statelist')->row();

			

			$GSTData = array(

            "AccountID" => $this->input->post('AccountID'),

            "gstin" => $this->input->post('gst_no'),

            "state_code" => $statecode,

            "state" => $state->state_name,

            "business_name" => $this->input->post('business_name'),

            "constitution_of_business" => $this->input->post('constitution_business'),

            "taxpayer_type" => $this->input->post('taxpayer_type'),

            "date_of_registration" => $this->input->post('date_of_reg'),

            "active_status" => $this->input->post('status_gst'),

            "address" => $this->input->post('address_gst'),

            "IsPrimary" => $isDefault,

			);

			$Updatedata = array(

            "IsPrimary" => 0,

			);

			if ($this->db->insert(db_prefix() . 'GstRecord', $GSTData)) {

				$insert_id = $this->db->insert_id();

				if($isDefault == 1){

					$this->db->where('AccountID', $this->input->post('AccountID'));

					$this->db->where('id != ', $insert_id);

					$this->db->update(db_prefix() . 'GstRecord', $Updatedata);

				}

				

				echo json_encode(true);

				} else {

				echo json_encode(false);

			}

		}

		

		public function VerifyPan()

		{

			$curr_date = date('Y-m-d');

			$data = array(

            'Pan' => $this->input->post('Pan'),

            'AccountID' => $this->input->post('AccountID'),

            'pan_verified_date' => $curr_date,

            'panVerifiedID' => $this->session->userdata('username'),

			);

			

			$Pan = $this->input->post('Pan');

			$result = $this->clients_model->checkPan($Pan);

			// if (empty($result)) {

				$curl = curl_init();

				// $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
				$token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTc4MjExNzE5NSwianRpIjoiN2ZiZWUyMzAtNTVhNy00ZDQzLWEyOTYtODVkMjM4Mzk3N2JlIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTc4MjExNzE5NSwiZXhwIjoyMDk3NDc3MTk1LCJlbWFpbCI6Imdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.Q4hrORYTQ6xifLe8UbojVxdvbk9eW-EIj9mJTxl3-xo';

				curl_setopt_array(

                $curl,

                array(

				CURLOPT_URL => 'https://kyc-api.aadhaarkyc.io/api/v1/pan/pan',

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => '',

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 0,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => 'POST',

				CURLOPT_POSTFIELDS => '{

            	"id_number":  "' . $Pan . '"

				}',

				CURLOPT_HTTPHEADER => array(

				'Content-Type: application/json',

				'Authorization: Bearer ' . $token . ''

				),

                )

				);

				

				$response = curl_exec($curl);

				curl_close($curl);

				echo ($response);

				// } else {

				// echo json_encode(false);

			// }

			// echo json_encode($result);

		}

		

		public function CheckBankAccount()

		{

			$curr_date = date('Y-m-d');

			

			$account_number = $this->input->post('account_number');

			$result = $this->clients_model->checkAccount($account_number);

			if (empty($result)) {

				

				echo json_encode(true);

				} else {

				echo json_encode(false);

			}

		}

		

		public function CheckPanExist()

		{

		    $Pan = $this->input->post('Pan');

		    $result = $this->clients_model->CheckPanExist($Pan);

			if (empty($result)) {

				

				echo json_encode(true);

				} else {

				echo json_encode(false);

			}

		}

		

		public function GetPanWiseDetails()

		{

		    $Pan = $this->input->post('Pan');

			$PanDetails = $this->clients_model->GetPanWiseDetails($Pan);

			

			$html = '';

			$key = 0;

			foreach ($PanDetails as $key => $value) {

			    if($value['active']==0)

			    {

			        $status = "Block";

			    }else if($value['active']==1){

			        $status = "Unblock";

			    }

			    

			    if($value['CustomerType']==1)

			    { $customertype = "Farmer";  }

			    else if($value['CustomerType']==2)

			    {  $customertype = "Broker";  }

			    else if($value['CustomerType']==3)

			    { $customertype = "Trader";}

			    

				$html .= '<tr>';

				$html .= '<td>' . ($key+1) . '</td>';

				$html .= '<td>' . $value["AccountID"] . '</td>';

				$html .= '<td>' . $value["company"] . '</td>';

				$html .= '<td>' . $value["firstname"] . '</td>';

				$html .= '<td>' . $value["lastname"] . '</td>';

				$html .= '<td>' . $customertype . '</td>';

				$html .= '<td>' . $value["gstin"] . '</td>';

				$html .= '<td>' . $status . '</td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		public function UpdateStatusPan()

		{

		    $Pan = $this->input->post('Pan');

		    $Status = $this->input->post('Status');

		    $result = $this->clients_model->UpdateStatusPan($Pan,$Status);

			if (empty($result)) {

				

				echo json_encode(false);

				} else {

				echo json_encode(true);

			}

		}

		

		public function UpdateDefaultBank() {

			$ID = $this->input->post('ID');

			$AccountID = $this->input->post('AccountID');

			$result = $this->clients_model->UpdateDefaultBankAccount($ID,$AccountID);

			echo json_encode($result);

		}

		

		public function GetBankDetailById()

		{

			$AccountID = $this->input->post('AccountID');

			$AccountDetails = $this->clients_model->GetAccountBankDetails($AccountID);

			$html = '';

			foreach ($AccountDetails as $key => $value) {

				$html .= '<tr>';

				if ($value["IsPrimary"] == '1') {

					$status = 'Default';

				}

				if ($value["IsPrimary"] == '0') {

					$status = '--';

				}

				

				$html .= '<td>';

				if($value["IsPrimary"] =='1'){

					$html .= '<input id="'.$value["id"].'" type="checkbox" value="'.$value["AccountID"].'" onchange="changeDefaultBank(this.id,this.value)" checked>';

					}else{

					$html .= '<input id="'.$value["id"].'" type="checkbox" value="'.$value["AccountID"].'" onchange="changeDefaultBank(this.id,this.value)">';

				}

				$html .= '</td>';

				

				$html .= '<td>' . $value["bankName"] . '</td>';

				$html .= '<td>' . $value["branchName"] . '</td>';

				$html .= '<td>' . $value["AaccountName"] . '</td>';

				$html .= '<td>' . $value["accountNumber"] . '</td>';

				$html .= '<td>' . $value["ifsc"] . '</td>';

				// $html .= '<td>' . $status . '</td>';

				$url = base_url() . 'admin/clients/GetImage?AccountID=' . $value["id"] . '&Type=bank_cheque';

				$img_file = 'raju.png';

				$src = 'data: ' . mime_content_type($img_file) . ';base64,' . $value["cheque_image"];

				// $html .= '<td><a href="' . $url . '" target="_blank"><img src="' . $src . '" width="120px" height="120px"></a></td>';

				$html .= '<td><a href="' . $url . '" target="_blank">View Image</a></td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		public function GetImage()

		{

			$AccountID = $_GET["AccountID"];

			$Type = $_GET["Type"];

			

			$image_details = $this->clients_model->GetImageDetails($AccountID, $Type);

			$data['image_details'] = $image_details;

			$data['title'] = "Image Show";

			$data['Type'] = $Type;

			/*echo "<pre>";

				print_r($image_details);

			die;*/

			$this->load->view('admin/clients/image_show', $data);

		}

		/* Get Farm List By AccountID */

		public function GetFirmDetailByID()

		{

			

			$AccountID = $this->input->post('AccountID');

			$AccountDetails = $this->clients_model->GetAccountFirmDetails($AccountID);

			$html = '';

			foreach ($AccountDetails as $key => $value) {

				

				$html .= '<tr>';

				

				$html .= '<td>' . $value["survey_number"] . '</td>';

				$html .= '<td>' . $value["latitude"] . '</td>';

				$html .= '<td>' . $value["longitude"] . '</td>';

				$html .= '<td>' . $value["farm_area"] . " " . $value["farm_unit"] . '</td>';

				$html .= '<td>' . $value["state_name"] . '</td>';

				$html .= '<td>' . $value["city"] . '</td>';

				$html .= '<td>' . $value["TalukaName"] . '</td>';

				$html .= '<td>' . $value["village"] . '</td>';

				$html .= '<td>' . $value["pincode"] . '</td>';

				$html .= '<td>' . $value["Address"] . '</td>';

				$url = base_url() . 'admin/clients/GetImage?AccountID=' . $value["id"] . '&Type=farm_image';

				$img_file = 'raju.png';

				$src = 'data: ' . mime_content_type($img_file) . ';base64,' . $value["saatbara_image"];

				$html .= '<td><a href="' . $url . '" target="_blank"><img src="' . $src . '" width="120px" height="120px"></a></td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		/* Get Crop List By AccountID */

		public function GetCropDetailByID()

		{

			$AccountID = $this->input->post('AccountID');

			$CropList = $this->clients_model->GetAccountCropList($AccountID);

			$html = '';

			foreach ($CropList as $key => $value) {

				

				$html .= '<tr>';

				$html .= '<td>' . $value["farm_name"] . '</td>';

				$html .= '<td>' . $value["ItemID"] . '</td>';

				$html .= '<td>' . $value["ItemName"] . '</td>';

				$html .= '<td>' . $value["CropArea"] . " " . $value["AreaUnit"] . '</td>';

				$html .= '<td>' . $value["season"] . '</td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		public function GetGstDetailByID()

		{

			

			$AccountID = $this->input->post('AccountID');

			$AccountDetails = $this->clients_model->GetAccountGstDetails($AccountID);

			$html = '';

			foreach ($AccountDetails as $key => $value) {

				

				$html .= '<tr>';

				if ($value["IsPrimary"] == '1') {

					$status = 'Default';

				}

				if ($value["IsPrimary"] == '0') {

					$status = '--';

				}

				$html .= '<td>' . $value["gstin"] . '</td>';

				$html .= '<td>' . $value["state"] . '</td>';

				$html .= '<td>' . $value["business_name"] . '</td>';

				$html .= '<td>' . $value["address"] . '</td>';

				$html .= '<td>' . $status . '</td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		public function GetDefaultGst()

		{

			$AccountID = $this->input->post('AccountID');

			$AccountDetails = $this->clients_model->GetDeafultGst($AccountID);

			echo json_encode($AccountDetails);

		}

		

		public function getCityID()

		{

			$city = $this->input->post('city');

			$city_id = $this->clients_model->getCityIDdb($city);

			echo json_encode($city_id);

		}

		

		public function getTalukaID()

		{

			$taluka = $this->input->post('taluka');

			$taluka_id = $this->clients_model->getTalukaIDdb($taluka);

			echo json_encode($taluka_id);

		}

		

		/* Edit client or add new client*/

		public function client($id = '')

		{

			if (!has_permission_new('customers', '', 'view')) {

				if ($id != '' && !is_customer_admin($id)) {

					access_denied('customers');

				}

			}

			

			if ($this->input->post() && !$this->input->is_ajax_request()) {

				if ($id == '') {

					if (!has_permission_new('customers', '', 'create')) {

						access_denied('customers');

					}

					

					$data = $this->input->post();

					

					$save_and_add_contact = false;

					if (isset($data['save_and_add_contact'])) {

						unset($data['save_and_add_contact']);

						$save_and_add_contact = true;

					}

					

					$id = $this->clients_model->add($data);

					if (!has_permission_new('customers', '', 'view')) {

						$assign['customer_admins'] = [];

						$assign['customer_admins'][] = get_staff_user_id();

						$this->clients_model->assign_admins($assign, $id);

					}

					if ($id) {

						set_alert('success', _l('added_successfully', _l('client')));

						if ($save_and_add_contact == false) {

							redirect(admin_url('clients/client/' . $id));

							} else {

							redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));

						}

					}

					} else {

					if (!has_permission_new('customers', '', 'edit')) {

						if (!is_customer_admin($id)) {

							access_denied('customers');

						}

					}

					$data = $this->input->post();

					$pan = strtoupper($data["Pan"]);

					

					$data["vat"] = strtoupper($data["vat"]);

					$data["Pan"] = strtoupper($data["Pan"]);

					

					$pattern_pan = '[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}';

					if ($pan !== "") {

						if (!preg_match("/^[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}?$/", $pan)) {

							set_alert('warning', 'Enter valid PAN number');

							redirect(admin_url('clients/client/' . $id));

						}

					}

					

					$vat = $data["vat"];

					if ($vat !== "") {

						if (!preg_match("/^([0-9]){2}([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}([0-9]{1})([0-9A-Za-z]){2}?$/", $vat)) {

							set_alert('warning', 'Enter valid GST number');

							redirect(admin_url('clients/client/' . $id));

							} else {

							$data["gsttype"] = 1;

						}

						} else {

						$data["vat"] = NULL;

						$data["gsttype"] = 2;

					}

					

					$rootcompany = $this->clients_model->get_rootcompany();

					$itemdivision = $this->clients_model->get_itemDivision();

					

					

					

					$data["routes"] = serialize($data["route"]);

					$routes = $data["route"];

					unset($data["route"]);

					$itemDivision = array();

					foreach ($itemdivision as $item_Division) {

						

						$item_division_id = "itemdiv" . $item_Division["id"];

						$Selected_item_division_ids = $data[$item_division_id];

						if ($Selected_item_division_ids == $item_Division["id"]) {

							

							array_push($itemDivision, $item_Division["id"]);

							unset($data[$item_division_id]);

						}

					}

					

					

					

					$itemDivision_comp = array();

					foreach ($itemdivision as $item_Division) {

						

						$item_division_comp_id = "itemdivisioncomp" . $item_Division["id"];

						$Selected_item_division_comp_ids = $data[$item_division_comp_id];

						

						if ($Selected_item_division_comp_ids !== "") {

							//array_push($itemDivision_comp,$Selected_item_division_comp_ids);

							$itemDivision_comp[$item_Division["id"]] = $data[$item_division_comp_id];

							unset($data[$item_division_comp_id]);

							} else {

							unset($data[$item_division_comp_id]);

						}

					}

					

					

					$company_assigned = array();

					foreach ($rootcompany as $r_company) {

						

						$company_assigned_id = "company_assigned" . $r_company["id"];

						$Selected_company_assigned_ids = $data[$company_assigned_id];

						if ($Selected_company_assigned_ids == $r_company["id"]) {

							

							array_push($company_assigned, $r_company["id"]);

							unset($data[$company_assigned_id]);

						}

					}

					

					$company_assigned_sales_p = array();

					$company_sales_person_assign_data = array();

					foreach ($rootcompany as $r_company) {

						

						$company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];

						$Selected_company_assigned_sales_p_ids = $data[$company_assigned_sales_p_id];

						//print_r($Selected_company_assigned_sales_p_ids);

						

						if ($Selected_company_assigned_sales_p_ids !== "") {

							

							foreach ($Selected_company_assigned_sales_p_ids as $assigned_ids) {

								$company_assigned_sales_p[$r_company["id"]] = $assigned_ids;

								$company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];

								

								$company_sales_person_assign_data[$r_company["id"]] = $data[$company_assigned_sales_p_id];

							}

							

							unset($data[$company_assigned_sales_p_id]);

							} else {

							unset($data[$company_assigned_sales_p_id]);

						}

					}

					

					

					

					

					$company_assigned_opn_bal = array();

					foreach ($rootcompany as $r_company) {

						

						$company_assigned_opn_bal_id = "opening_bal" . $r_company["id"];

						$Selected_company_assigned_opn_bal_ids = $data[$company_assigned_opn_bal_id];

						

						if ($Selected_company_assigned_opn_bal_ids !== "") {

							

							$company_assigned_opn_bal[$r_company["id"]] = $data[$company_assigned_opn_bal_id];

							unset($data[$company_assigned_opn_bal_id]);

							} else {

							unset($data[$company_assigned_opn_bal_id]);

						}

					}

					

					$company_assigned_drcr = array();

					foreach ($rootcompany as $r_company) {

						

						$company_assigned_drcr_id = "drcr" . $r_company["id"];

						//$Selected_company_assigned_drcr_ids = $data[$company_assigned_drcr_id];

						

						if ($data[$company_assigned_drcr_id] !== "") {

							

							$company_assigned_drcr[$r_company["id"]] = $data[$company_assigned_drcr_id];

							unset($data[$company_assigned_drcr_id]);

							} else {

							unset($data[$company_assigned_drcr_id]);

						}

					}

					

					/*print_r($company_assigned_sales_p);

						//print_r($company_assigned);

					die;*/

					

					$data["itemdivision"] = serialize($itemDivision);

					$data["itemdivision_comp"] = serialize($itemDivision_comp);

					

					$data["company_assigned"] = serialize($company_assigned);

					$data["company_assigned_staff"] = serialize($company_assigned_sales_p);

					$data["opening_bal"] = serialize($company_assigned_opn_bal);

					$data["drcr"] = serialize($company_assigned_drcr);

					/*echo "<pre>";

						print_r($data["company_assigned_staff"]);

						print_r($data);

					die;*/

					$contacts_fields["kms"] = $data['kms'];

					unset($data['kms']);

					

					$contacts_fields["FLNO1"] = $data['FLNO1'];

					unset($data['FLNO1']);

					

					$contacts_fields["Pan"] = $data['Pan'];

					unset($data['Pan']);

					

					$contacts_fields["Aadhaarno"] = $data['Aadhaarno'];

					unset($data['Aadhaarno']);

					

					$contacts_fields["istcs"] = $data['istcs'];

					unset($data['istcs']);

					

					$contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

					unset($data['TcsStartDate']);

					

					/*$contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

					unset($data['TcsStartDate']);*/

					

					$contacts_fields["BalancesYN"] = $data['BalancesYN'];

					unset($data['BalancesYN']);

					

					$contacts_fields["BalancelYN"] = $data['BalancelYN'];

					unset($data['BalancelYN']);

					

					$contacts_fields["phonenumber"] = $data['altphonenumber'];

					unset($data['altphonenumber']);

					

					

					

					if (isset($data['profile_image'])) {

						$contacts_fields["profile_image"] = $data['profile_image'];

						unset($data['profile_image']);

					}

					

					if (isset($data['title'])) {

						$contacts_fields["title"] = $data['title'];

						unset($data['title']);

					}

					if (isset($data['firstname'])) {

						$contacts_fields["firstname"] = $data['firstname'];

						unset($data['firstname']);

					}

					if (isset($data['lastname'])) {

						$contacts_fields["lastname"] = $data['lastname'];

						unset($data['lastname']);

					}

					

					if (isset($data['email'])) {

						$contacts_fields["email"] = $data['email'];

						unset($data['email']);

					}

					

					if (isset($data['location_type'])) {

						$LocationTypeID = $data['location_type'];

						unset($data['location_type']);

					}

					

					

					

					if (isset($data['DataTables_Table_0_length'])) {

						//$contacts_fields["donotsendwelcomeemail"] = $data['donotsendwelcomeemail'];

						unset($data['DataTables_Table_0_length']);

					}

					

					if (isset($data['groups_in'])) {

						$data["DistributorType"] = $data['groups_in'];

						unset($data['groups_in']);

					}

					

					if (isset($data['StartDate'])) {

						$data["StartDate"] = to_sql_date($data['StartDate']) . ' H:i:s';

						//unset($data['groups_in']);

					}

					

					$selected_company = $this->session->userdata('root_company');

					$FY = $this->session->userdata('finacial_year');

					$UserID = $this->session->userdata('username');

					$data["UserID2"] = $UserID;

					$data["Lupdate"] = date('Y-m-d H:i:s');

					$successcontacts = $this->clients_model->update_contact_new($contacts_fields, $id);

					

					$update_route = $this->clients_model->update_route($routes, $id);

					$current_item_div = $this->get_itemdiv($id, $selected_company);

					if ($this->session->userdata('staff_user_id') == "3") {

						foreach ($company_assigned_opn_bal as $key => $value) {

							foreach ($company_assigned_drcr as $key1 => $value1) {

								if ($key == $key1) {

									$dr_cr = $value1;

								}

							}

							if ($dr_cr == "CR") {

								$new_value = 0 - $value;

								} else if ($dr_cr == "DR") {

								$new_value = $value;

							}

							

							$this->db->where('PlantID', $key);

							$this->db->LIKE('FY', $FY);

							$this->db->where('AccountID', $id);

							$this->db->update(db_prefix() . 'accountbalances', [

                            'BAL1' => $new_value,

                            'UserID2' => $UserID,

                            'Lupdate' => date('Y-m-d H:i:s'),

							]);

							if ($this->db->affected_rows() > 0) {

								

								} else {

								$this->db->insert(db_prefix() . 'accountbalances', [

                                'BAL1' => $new_value,

                                'PlantID' => $key,

                                'FY' => $FY,

                                'AccountID' => $id,

								]);

							}

							

						}

						

					}

					

					foreach ($current_item_div as $key => $value) {

						# code...

						$this->db->where('ItemDivID', $value["ItemDivID"]);

						//$this->db->where('PlantID', $selected_company);

						$this->db->like('AccountID', $id);

						$this->db->delete(db_prefix() . 'accountitemdiv');

						/*echo $a_itemdiv ."  ".$selected_company."  ".$id;

						echo "<br>";*/

						//$this->delete_itemdiv($selected_company,$id,$a_itemdiv);

					}

					

					

					

					foreach ($itemDivision_comp as $key => $value) {

						$this->db->insert(db_prefix() . 'accountitemdiv', [

                        'ItemDivID' => $key,

                        'PlantID' => $selected_company,

                        'plant_assign' => $value,

                        'AccountID' => $id,

						]);

						

					}

					$success = $this->clients_model->update($data, $id);

					/*if ($success) {*/

					

					$successlocation = $this->clients_model->location_update($selected_company, $id, $LocationTypeID);

					

					$current_admins = $this->get_admins($id);

					$current_admins_ids = [];

					foreach ($current_admins as $c_admin) {

						//array_push($current_admins_ids, $c_admin['staff_id']);

						$this->delete_admin($c_admin['customer_id']);

					}

					

					/* foreach ($current_admins as $key => $c_admin_id) {

						if (!in_array($c_admin_id, $data['customer_admins'])) {

						$this->db->where('staff_id', $c_admin_id['staff_id']);

						$this->db->where('customer_id', $id);

						$this->db->where('company_id', $c_admin_id['company_id']);

						$this->db->delete(db_prefix() . 'customer_admins');

						

						}

					}*/

					

					foreach ($company_sales_person_assign_data as $key => $value) {

						

						

						if ($value) {

							

							foreach ($value as $val) {

								//echo $key . " = >" .$val;

								if ($val == 0) {

									

									} else {

									

									$this->db->insert(db_prefix() . 'customer_admins', [

                                    'customer_id' => $id,

                                    'staff_id' => $val,

                                    'company_id' => $key,

                                    'date_assigned' => date('Y-m-d H:i:s'),

									]);

								}

								

							}

						}

						

					}

					

					

					//}

					set_alert('success', _l('updated_successfully', _l('client')));

					redirect(admin_url('clients/client/' . $id));

				}

			}

			

			$group = !$this->input->get('group') ? 'profile' : $this->input->get('group');

			$data['group'] = $group;

			

			if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {

				redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));

			}

			

			// Customer groups

			$data['groups'] = $this->clients_model->get_groups();

			

			if ($id == '') {

				$title = _l('add_new', _l('client_lowercase'));

				} else {

				

				$client = $this->clients_model->get($id);

				$client_location = $this->clients_model->get_location_type($id);

				//print_r($client);

				//die;

				$data['customer_tabs'] = get_customer_profile_tabs();

				

				if (!$client) {

					show_404();

				}

				$data['dist_route'] = $this->clients_model->getroutebyclient($id);

				$data['dist_item_div'] = $this->clients_model->getclientitem_division($id);

				/*echo "<pre>";

					echo $id;

					print_r($data['dist_route']);

				die;*/

				$data['contacts'] = $this->clients_model->get_contacts($id);

				$data['tab'] = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

				

				if (!$data['tab']) {

					show_404();

				}

				

				// Fetch data based on groups

				if ($group == 'profile') {

					//$data['customer_groups'] = $this->clients_model->get_customer_groups($id);

					$data['customer_admins'] = $this->clients_model->get_admins($id);

					$data['acc_bal1'] = $this->clients_model->get_acc_bal1($id);

					$data['acc_bal2'] = $this->clients_model->get_acc_bal2($id);

					$data['acc_bal3'] = $this->clients_model->get_acc_bal3($id);

					} elseif ($group == 'attachments') {

					$data['attachments'] = get_all_customer_attachments($id);

					} elseif ($group == 'vault') {

					$data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

					

					if ($data['vault_entries'] === -1) {

						$data['vault_entries'] = [];

					}

					} elseif ($group == 'estimates') {

					$this->load->model('estimates_model');

					$data['estimate_statuses'] = $this->estimates_model->get_statuses();

					} elseif ($group == 'invoices') {

					$this->load->model('invoices_model');

					$data['invoice_statuses'] = $this->invoices_model->get_statuses();

					} elseif ($group == 'credit_notes') {

					$this->load->model('credit_notes_model');

					$data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();

					$data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);

					} elseif ($group == 'payments') {

					$this->load->model('payment_modes_model');

					$data['payment_modes'] = $this->payment_modes_model->get();

					} elseif ($group == 'notes') {

					$data['user_notes'] = $this->misc_model->get_notes($id, 'customer');

					} elseif ($group == 'projects') {

					$this->load->model('projects_model');

					$data['project_statuses'] = $this->projects_model->get_project_statuses();

					} elseif ($group == 'statement') {

					if (!has_permission_new('invoices', '', 'view') && !has_permission_new('payments', '', 'view')) {

						set_alert('danger', _l('access_denied'));

						redirect(admin_url('clients/client/' . $id));

					}

					

					$data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));

					} elseif ($group == 'map') {

					if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {

						$this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());

						

						$this->app_scripts->add('google-maps-api-js', [

                        'path' => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',

                        'attributes' => [

						'async',

						'defer',

						'latitude' => "$client->latitude",

						'longitude' => "$client->longitude",

						'mapMarkerTitle' => "$client->company",

                        ],

						]);

					}

				}

				

				$data['staff'] = $this->staff_model->getAssignStaff('', ['active' => 1]);

				

				$data['client'] = $client;

				$data['client_location'] = $client_location;

				$data['client_contacts'] = $this->clients_model->get_contacts($id);

				$title = $client->company;

				/* echo "<pre>";

					print_r($data['client_contacts']);

				die;*/

				// Get all active staff members (used to add reminder)

				$data['members'] = $data['staff'];

				

				if (!empty($data['client']->company)) {

					// Check if is realy empty client company so we can set this field to empty

					// The query where fetch the client auto populate firstname and lastname if company is empty

					if (is_empty_customer_company($data['client']->userid)) {

						$data['client']->company = '';

					}

				}

			}

			

			$this->load->model('currencies_model');

			$data['currencies'] = $this->currencies_model->get();

			

			if ($id != '') {

				$customer_currency = $data['client']->default_currency;

				

				foreach ($data['currencies'] as $currency) {

					if ($customer_currency != 0) {

						if ($currency['id'] == $customer_currency) {

							$customer_currency = $currency;

							

							break;

						}

						} else {

						if ($currency['isdefault'] == 1) {

							$customer_currency = $currency;

							

							break;

						}

					}

				}

				

				if (is_array($customer_currency)) {

					$customer_currency = (object) $customer_currency;

				}

				

				$data['customer_currency'] = $customer_currency;

				

				$slug_zip_folder = (

                $client->company != ''

                ? $client->company

                : get_contact_full_name(get_primary_contact_user_id($client->userid))

				);

				

				$data['zip_in_folder'] = slug_it($slug_zip_folder);

			}

			$data['staff'] = $this->staff_model->get('', ['active' => 1]);

			// Get All route list

			$data['routes'] = $this->clients_model->getroute();

			$data['state'] = $this->clients_model->getallstate();

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			$data['itemdivision'] = $this->clients_model->get_itemDivision();

			$data['bodyclass'] = 'customer-profile dynamic-create-groups';

			$data['title'] = $title;

			/*echo "<pre>";

				print_r($data['client_contacts']);

			die;*/

			$this->load->view('admin/clients/client', $data);

		}

		

		

		public function get_admins($id)

		{

			$this->db->where('customer_id', $id);

			

			return $this->db->get(db_prefix() . 'customer_admins')->result_array();

		}

		public function get_itemdiv($id, $selected_company)

		{

			$this->db->where('AccountID', $id);

			//$this->db->where('PlantID', $selected_company);

			return $this->db->get(db_prefix() . 'accountitemdiv')->result_array();

		}

		

		public function get_current_division($id)

		{

			$this->db->where('client_id', $id);

			

			return $this->db->get(db_prefix() . 'customers_item_division')->result_array();

		}

		public function delete_admin($id)

		{

			$this->db->where('customer_id', $id);

			

			$this->db->delete(db_prefix() . 'customer_admins');

		}

		

		public function export($contact_id)

		{

			if (is_admin()) {

				$this->load->library('gdpr/gdpr_contact');

				$this->gdpr_contact->export($contact_id);

			}

		}

		

		// Used to give a tip to the user if the company exists when new company is created

		public function check_duplicate_customer_name()

		{

			if (has_permission_new('customers', '', 'create')) {

				$companyName = trim($this->input->post('company'));

				$Account_details = $this->clients_model->check_company($companyName);

				$response = [

                'exists' => $Account_details,

                'message' => _l('company_exists_info1', '<b>' . $companyName . '</b>'),

				];

				echo json_encode($response);

			}

		}

		

		// Used to give a tip to the user if the company exists when new company is created

		public function check_duplicate_customer_code()

		{

			if (has_permission_new('customers', '', 'create')) {

				$AccountID = trim($this->input->post('AccountID'));

				$Account_details = $this->clients_model->check_AccountID($AccountID);

				$response = [

                'exists' => $Account_details,

                'message' => _l('account_code_exists_info', '<b>' . $AccountID . '</b>'),

				];

				echo json_encode($response);

			}

		}

		

		public function save_longitude_and_latitude($client_id)

		{

			if (!has_permission_new('customers', '', 'edit')) {

				if (!is_customer_admin($client_id)) {

					ajax_access_denied();

				}

			}

			

			$this->db->where('userid', $client_id);

			$this->db->update(db_prefix() . 'clients', [

            'longitude' => $this->input->post('longitude'),

            'latitude' => $this->input->post('latitude'),

			]);

			if ($this->db->affected_rows() > 0) {

				echo 'success';

				} else {

				echo 'false';

			}

		}

		

		public function form_contact($customer_id, $contact_id = '')

		{

			if (!has_permission_new('customers', '', 'view')) {

				if (!is_customer_admin($customer_id)) {

					echo _l('access_denied');

					die;

				}

			}

			$data['customer_id'] = $customer_id;

			$data['contactid'] = $contact_id;

			if ($this->input->post()) {

				$data = $this->input->post();

				$data['password'] = $this->input->post('password', false);

				

				unset($data['contactid']);

				if ($contact_id == '') {

					if (!has_permission_new('customers', '', 'create')) {

						if (!is_customer_admin($customer_id)) {

							header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

							echo json_encode([

                            'success' => false,

                            'message' => _l('access_denied'),

							]);

							die;

						}

					}

					$id = $this->clients_model->add_contact($data, $customer_id);

					$message = '';

					$success = false;

					if ($id) {

						handle_contact_profile_image_upload($id);

						$success = true;

						$message = _l('added_successfully', _l('contact'));

					}

					echo json_encode([

                    'success' => $success,

                    'message' => $message,

                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

                    'is_individual' => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'contacts', ['userid' => $customer_id]) == 1,

					]);

					die;

				}

				if (!has_permission_new('customers', '', 'edit')) {

					if (!is_customer_admin($customer_id)) {

						header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

						echo json_encode([

                        'success' => false,

                        'message' => _l('access_denied'),

						]);

						die;

					}

				}

				$original_contact = $this->clients_model->get_contact($contact_id);

				$success = $this->clients_model->update_contact($data, $contact_id);

				$message = '';

				$proposal_warning = false;

				$original_email = '';

				$updated = false;

				if (is_array($success)) {

					if (isset($success['set_password_email_sent'])) {

						$message = _l('set_password_email_sent_to_client');

						} elseif (isset($success['set_password_email_sent_and_profile_updated'])) {

						$updated = true;

						$message = _l('set_password_email_sent_to_client_and_profile_updated');

					}

					} else {

					if ($success == true) {

						$updated = true;

						$message = _l('updated_successfully', _l('contact'));

					}

				}

				if (handle_contact_profile_image_upload($contact_id) && !$updated) {

					$message = _l('updated_successfully', _l('contact'));

					$success = true;

				}

				if ($updated == true) {

					$contact = $this->clients_model->get_contact($contact_id);

					if (

                    total_rows(db_prefix() . 'proposals', [

					'rel_type' => 'customer',

					'rel_id' => $contact->userid,

					'email' => $original_contact->email,

                    ]) > 0 && ($original_contact->email != $contact->email)

					) {

						$proposal_warning = true;

						$original_email = $original_contact->email;

					}

				}

				echo json_encode([

                'success' => $success,

                'proposal_warning' => $proposal_warning,

                'message' => $message,

                'original_email' => $original_email,

                'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

				]);

				die;

			}

			if ($contact_id == '') {

				$title = _l('add_new', _l('contact_lowercase'));

				} else {

				$data['contact'] = $this->clients_model->get_contact($contact_id);

				

				if (!$data['contact']) {

					header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

					echo json_encode([

                    'success' => false,

                    'message' => 'Contact Not Found',

					]);

					die;

				}

				$title = $data['contact']->firstname . ' ' . $data['contact']->lastname;

			}

			

			$data['customer_permissions'] = get_contact_permissions();

			$data['title'] = $title;

			$this->load->view('admin/clients/modals/contact', $data);

		}

		

		public function form_beat($contact_id = '')

		{

			/* if (!has_permission_new('customers', '', 'view')) {

				if (!is_customer_admin($customer_id)) {

				echo _l('access_denied');

				die;

				}

			}*/

			/*$data['customer_id'] = $customer_id;

			$data['contactid']   = $contact_id;*/

			if ($this->input->post()) {

				$data = $this->input->post();

				

				

				unset($data['contactid']);

				unset($data['distcity']);

				if ($contact_id == '') {

					/*if (!has_permission_new('customers', '', 'create')) {

						if (!is_customer_admin($customer_id)) {

                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

                        echo json_encode([

						'success' => false,

						'message' => _l('access_denied'),

                        ]);

                        die;

						}

					}*/

					$id = $this->clients_model->add_beat($data);

					$message = '';

					$success = false;

					if ($id) {

						//handle_contact_profile_image_upload($id);

						$success = true;

						$message = _l('added_successfully', 'Beat');

					}

					echo json_encode([

                    'success' => $data,

                    'message' => $message,

                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

                    'is_individual' => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'contacts', ['userid' => $customer_id]) == 1,

					]);

					die;

				}

				/*if (!has_permission_new('customers', '', 'edit')) {

					if (!is_customer_admin($customer_id)) {

                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

                    echo json_encode([

					'success' => false,

					'message' => _l('access_denied'),

					]);

                    die;

					}

				}*/

				$original_contact = $this->clients_model->get_contact($contact_id);

				$success = $this->clients_model->update_contact($data, $contact_id);

				$message = '';

				$proposal_warning = false;

				$original_email = '';

				$updated = false;

				

				

				

				echo json_encode([

                'success' => $success,

                'proposal_warning' => $proposal_warning,

                'message' => $message,

                'original_email' => $original_email,

                'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

				]);

				die;

			}

			if ($contact_id == '') {

				$title = "Add New Beat";

				} else {

				$data['contact'] = $this->clients_model->get_contact($contact_id);

				

				if (!$data['contact']) {

					header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

					echo json_encode([

                    'success' => false,

                    'message' => 'Contact Not Found',

					]);

					die;

				}

				$title = $data['contact']->firstname . ' ' . $data['contact']->lastname;

			}

			$data['customer'] = $this->clients_model->get();

			$data['customer_permissions'] = get_contact_permissions();

			$data['title'] = $title;

			$this->load->view('admin/clients/modals/beat', $data);

		}

		

		public function confirm_registration($client_id)

		{

			if (!is_admin()) {

				access_denied('Customer Confirm Registration, ID: ' . $client_id);

			}

			$this->clients_model->confirm_registration($client_id);

			set_alert('success', _l('customer_registration_successfully_confirmed'));

			redirect($_SERVER['HTTP_REFERER']);

		}

		

		public function dist_fetch_detail()

		{

			$dist_id = $this->input->post('id');

			$customer_data = $this->clients_model->get($dist_id);

			echo json_encode($customer_data);

			

		}

		

		public function update_file_share_visibility()

		{

			if ($this->input->post()) {

				$file_id = $this->input->post('file_id');

				$share_contacts_id = [];

				

				if ($this->input->post('share_contacts_id')) {

					$share_contacts_id = $this->input->post('share_contacts_id');

				}

				

				$this->db->where('file_id', $file_id);

				$this->db->delete(db_prefix() . 'shared_customer_files');

				

				foreach ($share_contacts_id as $share_contact_id) {

					$this->db->insert(db_prefix() . 'shared_customer_files', [

                    'file_id' => $file_id,

                    'contact_id' => $share_contact_id,

					]);

				}

			}

		}

		

		public function delete_contact_profile_image($contact_id)

		{

			$this->clients_model->delete_contact_profile_image($contact_id);

		}

		

		public function mark_as_active($id)

		{

			$this->db->where('userid', $id);

			$this->db->update(db_prefix() . 'clients', [

            'active' => 1,

			]);

			redirect(admin_url('clients/client/' . $id));

		}

		

		public function consents($id)

		{

			if (!has_permission_new('customers', '', 'view')) {

				if (!is_customer_admin(get_user_id_by_contact_id($id))) {

					echo _l('access_denied');

					die;

				}

			}

			

			$this->load->model('gdpr_model');

			$data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'contact');

			$data['consents'] = $this->gdpr_model->get_consents(['contact_id' => $id]);

			$data['contact_id'] = $id;

			$this->load->view('admin/gdpr/contact_consent', $data);

		}

		

		public function update_all_proposal_emails_linked_to_customer($contact_id)

		{

			$success = false;

			$email = '';

			if ($this->input->post('update')) {

				$this->load->model('proposals_model');

				

				$this->db->select('email,userid');

				$this->db->where('id', $contact_id);

				$contact = $this->db->get(db_prefix() . 'contacts')->row();

				

				$proposals = $this->proposals_model->get('', [

                'rel_type' => 'customer',

                'rel_id' => $contact->userid,

                'email' => $this->input->post('original_email'),

				]);

				$affected_rows = 0;

				

				foreach ($proposals as $proposal) {

					$this->db->where('id', $proposal['id']);

					$this->db->update(db_prefix() . 'proposals', [

                    'email' => $contact->email,

					]);

					if ($this->db->affected_rows() > 0) {

						$affected_rows++;

					}

				}

				

				if ($affected_rows > 0) {

					$success = true;

				}

			}

			echo json_encode([

            'success' => $success,

            'message' => _l('proposals_emails_updated', [

			_l('contact_lowercase'),

			$contact->email,

            ]),

			]);

		}

		

		public function assign_admins($id)

		{

			if (!has_permission_new('customers', '', 'create') && !has_permission_new('customers', '', 'edit')) {

				access_denied('customers');

			}

			$success = $this->clients_model->assign_admins($this->input->post(), $id);

			if ($success == true) {

				set_alert('success', _l('updated_successfully', _l('client')));

			}

			

			redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));

		}

		

		public function delete_customer_admin($customer_id, $staff_id)

		{

			if (!has_permission_new('customers', '', 'create') && !has_permission_new('customers', '', 'edit')) {

				access_denied('customers');

			}

			

			$this->db->where('customer_id', $customer_id);

			$this->db->where('staff_id', $staff_id);

			$this->db->delete(db_prefix() . 'customer_admins');

			redirect(admin_url('clients/client/' . $customer_id) . '?tab=customer_admins');

		}

		

		public function delete_contact($customer_id, $id)

		{

			if (!has_permission_new('customers', '', 'delete')) {

				if (!is_customer_admin($customer_id)) {

					access_denied('customers');

				}

			}

			$contact = $this->clients_model->get_contact($id);

			$hasProposals = false;

			if ($contact && is_gdpr()) {

				if (total_rows(db_prefix() . 'proposals', ['email' => $contact->email]) > 0) {

					$hasProposals = true;

				}

			}

			

			$this->clients_model->delete_contact($id);

			if ($hasProposals) {

				$this->session->set_flashdata('gdpr_delete_warning', true);

			}

			redirect(admin_url('clients/client/' . $customer_id . '?group=contacts'));

		}

		

		public function contacts($client_id)

		{

			$this->app->get_table_data('contacts', [

            'client_id' => $client_id,

			]);

		}

		

		public function upload_attachment($id)

		{

			handle_client_attachments_upload($id);

		}

		

		public function add_external_attachment()

		{

			if ($this->input->post()) {

				$this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));

			}

		}

		

		public function delete_attachment($customer_id, $id)

		{

			if (has_permission_new('customers', '', 'delete') || is_customer_admin($customer_id)) {

				$this->clients_model->delete_attachment($id);

			}

			redirect($_SERVER['HTTP_REFERER']);

		}

		

		/* Delete client */

		public function delete($id)

		{

			if (!has_permission_new('customers', '', 'delete')) {

				access_denied('customers');

			}

			if (!$id) {

				redirect(admin_url('clients'));

			}

			$response = $this->clients_model->delete($id);

			if (is_array($response) && isset($response['referenced'])) {

				set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));

				} elseif ($response == true) {

				set_alert('success', _l('deleted', _l('client')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('client_lowercase')));

			}

			redirect(admin_url('clients'));

		}

		

		/* Staff can login as client */

		public function login_as_client($id)

		{

			if (is_admin()) {

				login_as_client($id);

			}

			hooks()->do_action('after_contact_login');

			redirect(site_url());

		}

		

		public function get_customer_billing_and_shipping_details($id)

		{

			echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));

		}

		

		/* Change client status / active / inactive */

		public function change_contact_status($id, $status)

		{

			if (has_permission_new('customers', '', 'edit') || is_customer_admin(get_user_id_by_contact_id($id))) {

				if ($this->input->is_ajax_request()) {

					$this->clients_model->change_contact_status($id, $status);

				}

			}

		}

		

		/* Change client status / active / inactive */

		public function change_client_status($id, $status)

		{

			if ($this->input->is_ajax_request()) {

				$this->clients_model->change_client_status($id, $status);

			}

		}

		

		/* Zip function for credit notes */

		public function zip_credit_notes($id)

		{

			$has_permission_view = has_permission_new('credit_notes', '', 'view');

			

			if (!$has_permission_view && !has_permission_new('credit_notes', '', 'view_own')) {

				access_denied('Zip Customer Credit Notes');

			}

			

			if ($this->input->post()) {

				$this->load->library('app_bulk_pdf_export', [

                'export_type' => 'credit_notes',

                'status' => $this->input->post('credit_note_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=credit_notes'),

				]);

				

				$this->app_bulk_pdf_export->set_client_id($id);

				$this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

				$this->app_bulk_pdf_export->export();

			}

		}

		

		public function zip_invoices($id)

		{

			$has_permission_view = has_permission_new('invoices', '', 'view');

			if (

            !$has_permission_view && !has_permission_new('invoices', '', 'view_own')

            && get_option('allow_staff_view_invoices_assigned') == '0'

			) {

				access_denied('Zip Customer Invoices');

			}

			

			if ($this->input->post()) {

				$this->load->library('app_bulk_pdf_export', [

                'export_type' => 'invoices',

                'status' => $this->input->post('invoice_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=invoices'),

				]);

				

				$this->app_bulk_pdf_export->set_client_id($id);

				$this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

				$this->app_bulk_pdf_export->export();

			}

		}

		

		/* Since version 1.0.2 zip client estimates */

		public function zip_estimates($id)

		{

			$has_permission_view = has_permission_new('estimates', '', 'view');

			if (

            !$has_permission_view && !has_permission_new('estimates', '', 'view_own')

            && get_option('allow_staff_view_estimates_assigned') == '0'

			) {

				access_denied('Zip Customer Estimates');

			}

			

			if ($this->input->post()) {

				$this->load->library('app_bulk_pdf_export', [

                'export_type' => 'estimates',

                'status' => $this->input->post('estimate_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=estimates'),

				]);

				

				$this->app_bulk_pdf_export->set_client_id($id);

				$this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

				$this->app_bulk_pdf_export->export();

			}

		}

		

		public function zip_payments($id)

		{

			$has_permission_view = has_permission_new('payments', '', 'view');

			

			if (

            !$has_permission_view && !has_permission_new('invoices', '', 'view_own')

            && get_option('allow_staff_view_invoices_assigned') == '0'

			) {

				access_denied('Zip Customer Payments');

			}

			

			$this->load->library('app_bulk_pdf_export', [

            'export_type' => 'payments',

            'payment_mode' => $this->input->post('paymentmode'),

            'date_from' => $this->input->post('zip-from'),

            'date_to' => $this->input->post('zip-to'),

            'redirect_on_error' => admin_url('clients/client/' . $id . '?group=payments'),

			]);

			

			$this->app_bulk_pdf_export->set_client_id($id);

			$this->app_bulk_pdf_export->set_client_id_column(db_prefix() . 'clients.userid');

			$this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

			$this->app_bulk_pdf_export->export();

		}

		

		public function import()

		{

			if (!has_permission_new('customers', '', 'create')) {

				access_denied('customers');

			}

			

			$dbFields = $this->db->list_fields(db_prefix() . 'contacts');

			foreach ($dbFields as $key => $contactField) {

				if ($contactField == 'phonenumber') {

					$dbFields[$key] = 'contact_phonenumber';

				}

			}

			

			$dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix() . 'clients'));

			

			$this->load->library('import/import_customers', [], 'import');

			

			$this->import->setDatabaseFields($dbFields)

            ->setCustomFields(get_custom_fields('customers'));

			

			if ($this->input->post('download_sample') === 'true') {

				$this->import->downloadSample();

			}

			

			if (

            $this->input->post()

            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''

			) {

				$this->import->setSimulation($this->input->post('simulate'))

                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])

                ->setFilename($_FILES['file_csv']['name'])

                ->perform();

				

				

				$data['total_rows_post'] = $this->import->totalRows();

				

				if (!$this->import->isSimulation()) {

					set_alert('success', _l('import_total_imported', $this->import->totalImported()));

				}

			}

			

			$data['groups'] = $this->clients_model->get_groups();

			$data['title'] = _l('import');

			$data['bodyclass'] = 'dynamic-create-groups';

			$this->load->view('admin/clients/import', $data);

		}

		

		public function groups()

		{

			if (!has_permission_new('distributor_type', '', 'view')) {

				access_denied('access denied');

			}

			if ($this->input->is_ajax_request()) {

				$this->app->get_table_data('distributor_type');

			}

			$data['title'] = _l('customer_groups');

			$this->load->view('admin/clients/groups_manage', $data);

		}

		

		public function group()

		{

			if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {

				access_denied('Customer Groups');

			}

			

			if ($this->input->is_ajax_request()) {

				$data = $this->input->post();

				$data['PlantID'] = $this->session->userdata('root_company');

				if ($data['id'] == '') {

					$id = $this->clients_model->add_group($data);

					$message = $id ? _l('added_successfully', _l('customer_group')) : '';

					echo json_encode([

                    'success' => $id ? true : false,

                    'message' => $message,

                    'id' => $id,

                    'name' => $data['name'],

					]);

					} else {

					$success = $this->clients_model->edit_group($data);

					$message = '';

					if ($success == true) {

						$message = _l('updated_successfully', _l('customer_group'));

					}

					echo json_encode([

                    'success' => $success,

                    'message' => $message,

					]);

				}

			}

		}

		

		public function delete_group($id)

		{

			if (!is_admin()) {

				access_denied('Delete Customer Group');

			}

			if (!$id) {

				redirect(admin_url('clients/groups'));

			}

			$response = $this->clients_model->delete_group($id);

			if ($response == true) {

				set_alert('success', _l('deleted', _l('customer_group')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));

			}

			redirect(admin_url('clients/groups'));

		}

		

		public function bulk_action()

		{

			hooks()->do_action('before_do_bulk_action_for_customers');

			$total_deleted = 0;

			if ($this->input->post()) {

				$ids = $this->input->post('ids');

				$groups = $this->input->post('groups');

				

				if (is_array($ids)) {

					foreach ($ids as $id) {

						if ($this->input->post('mass_delete')) {

							if ($this->clients_model->delete($id)) {

								$total_deleted++;

							}

							} else {

							if (!is_array($groups)) {

								$groups = false;

							}

							$this->client_groups_model->sync_customer_groups($id, $groups);

						}

					}

				}

			}

			

			if ($this->input->post('mass_delete')) {

				set_alert('success', _l('total_clients_deleted', $total_deleted));

			}

		}

		

		public function vault_entry_create($customer_id)

		{

			$data = $this->input->post();

			

			if (isset($data['fakeusernameremembered'])) {

				unset($data['fakeusernameremembered']);

			}

			

			if (isset($data['fakepasswordremembered'])) {

				unset($data['fakepasswordremembered']);

			}

			

			unset($data['id']);

			$data['creator'] = get_staff_user_id();

			$data['creator_name'] = get_staff_full_name($data['creator']);

			$data['description'] = nl2br($data['description']);

			$data['password'] = $this->encryption->encrypt($this->input->post('password', false));

			

			if (empty($data['port'])) {

				unset($data['port']);

			}

			

			$this->clients_model->vault_entry_create($data, $customer_id);

			set_alert('success', _l('added_successfully', _l('vault_entry')));

			redirect($_SERVER['HTTP_REFERER']);

		}

		

		public function vault_entry_update($entry_id)

		{

			$entry = $this->clients_model->get_vault_entry($entry_id);

			

			if ($entry->creator == get_staff_user_id() || is_admin()) {

				$data = $this->input->post();

				

				if (isset($data['fakeusernameremembered'])) {

					unset($data['fakeusernameremembered']);

				}

				if (isset($data['fakepasswordremembered'])) {

					unset($data['fakepasswordremembered']);

				}

				

				$data['last_updated_from'] = get_staff_full_name(get_staff_user_id());

				$data['description'] = nl2br($data['description']);

				

				if (!empty($data['password'])) {

					$data['password'] = $this->encryption->encrypt($this->input->post('password', false));

					} else {

					unset($data['password']);

				}

				

				if (empty($data['port'])) {

					unset($data['port']);

				}

				

				$this->clients_model->vault_entry_update($entry_id, $data);

				set_alert('success', _l('updated_successfully', _l('vault_entry')));

			}

			redirect($_SERVER['HTTP_REFERER']);

		}

		

		public function vault_entry_delete($id)

		{

			$entry = $this->clients_model->get_vault_entry($id);

			if ($entry->creator == get_staff_user_id() || is_admin()) {

				$this->clients_model->vault_entry_delete($id);

			}

			redirect($_SERVER['HTTP_REFERER']);

		}

		

		public function vault_encrypt_password()

		{

			$id = $this->input->post('id');

			$user_password = $this->input->post('user_password', false);

			$user = $this->staff_model->get(get_staff_user_id());

			

			if (!app_hasher()->CheckPassword($user_password, $user->password)) {

				header('HTTP/1.1 401 Unauthorized');

				echo json_encode(['error_msg' => _l('vault_password_user_not_correct')]);

				die;

			}

			

			$vault = $this->clients_model->get_vault_entry($id);

			$password = $this->encryption->decrypt($vault->password);

			

			$password = html_escape($password);

			

			// Failed to decrypt

			if (!$password) {

				header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

				echo json_encode(['error_msg' => _l('failed_to_decrypt_password')]);

				die;

			}

			

			echo json_encode(['password' => $password]);

		}

		

		public function get_vault_entry($id)

		{

			$entry = $this->clients_model->get_vault_entry($id);

			unset($entry->password);

			$entry->description = clear_textarea_breaks($entry->description);

			echo json_encode($entry);

		}

		

		public function statement_pdf()

		{

			$customer_id = $this->input->get('customer_id');

			

			if (!has_permission_new('invoices', '', 'view') && !has_permission_new('payments', '', 'view')) {

				set_alert('danger', _l('access_denied'));

				redirect(admin_url('clients/client/' . $customer_id));

			}

			

			$from = $this->input->get('from');

			$to = $this->input->get('to');

			

			$data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

			

			try {

				$pdf = statement_pdf($data['statement']);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(slug_it(_l('customer_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);

		}

		

		public function send_statement()

		{

			$customer_id = $this->input->get('customer_id');

			

			if (!has_permission_new('invoices', '', 'view') && !has_permission_new('payments', '', 'view')) {

				set_alert('danger', _l('access_denied'));

				redirect(admin_url('clients/client/' . $customer_id));

			}

			

			$from = $this->input->get('from');

			$to = $this->input->get('to');

			

			$send_to = $this->input->post('send_to');

			$cc = $this->input->post('cc');

			

			$success = $this->clients_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);

			// In case client use another language

			load_admin_language();

			if ($success) {

				set_alert('success', _l('statement_sent_to_client_success'));

				} else {

				set_alert('danger', _l('statement_sent_to_client_fail'));

			}

			

			redirect(admin_url('clients/client/' . $customer_id . '?group=statement'));

		}

		

		public function statement()

		{

			if (!has_permission_new('invoices', '', 'view') && !has_permission_new('payments', '', 'view')) {

				header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

				echo _l('access_denied');

				die;

			}

			

			$customer_id = $this->input->get('customer_id');

			$from = $this->input->get('from');

			$to = $this->input->get('to');

			

			$data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

			

			$data['from'] = $from;

			$data['to'] = $to;

			

			$viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);

			

			echo json_encode($viewData);

		}

		

		public function AddEditBroker($id = '')

		{

			

			if (!has_permission_new('BrokerMaster', '', 'view')) {

				access_denied('access denied');

			}

			if ($id !== "") {

				$AccountIDSetData = array(

                'AccountIDSet' => $id

				);

				$this->session->set_userdata($AccountIDSetData);

				} else {

				$this->session->unset_userdata('AccountIDSet');

			}

			$post_data = array();

			$this->load->model('currencies_model');

			$data['currencies'] = $this->currencies_model->get();

			$data['staff'] = $this->staff_model->get('', ['active' => 1]);

			$data['state'] = $this->clients_model->getallstate();

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			$data['itemdivision'] = $this->clients_model->get_itemDivision();

			$data['title'] = 'Add/Edit Broker';

			$data['clusters'] = $this->clients_model->getClusters();

			$data['company_detail'] = $this->clients_model->get_company_detail();

			$data['tds_sections'] = $this->clients_model->GetTDSSections();

			

			$this->load->view('admin/clients/AddEditBrokerMaster', $data);

		}

		

		public function BlockUnblockLedgerPan()

		{

		    if (!has_permission_new('BlockUnblock_ledger', '', 'view')) {

				access_denied('access denied');

			}

			$this->load->view('admin/clients/BlockUnblockLedgerPan');

		}

		

		/* add new client*/

		public function SaveBroker()

		{

			if (!has_permission_new('BrokerMaster', '', 'create')) {

				set_alert('danger', _l('access_denied'));

			}

			$BrokerDetails = $this->input->post();

			$BrokerDetails = $this->clients_model->SaveBrokerDetails($BrokerDetails);

			echo json_encode($BrokerDetails);

		}

		

		/* Edit client*/

		public function UpdateBroker($id = '')

		{

			if (!has_permission_new('BrokerMaster', '', 'edit')) {

				set_alert('danger', _l('access_denied'));

			}

			$BrokerDetails = $this->input->post();

			$BrokerDetails = $this->clients_model->UpdateBrokerDetails($BrokerDetails);

			echo json_encode($BrokerDetails);

		}

		

		public function BrokerListPopUp()

		{

			$account_subgroup_table = $this->clients_model->GetBrokerList();

			

			$html = "";

			foreach ($account_subgroup_table as $key => $value) {

				$html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

				$html .= '<td>' . $value["AccountID"] . '</td>';

				$html .= '<td>' . $value["company"] . '</td>';

				$html .= '<td>' . $value['Name'] . '</td>';

				$html .= '<td>' . $value["firstname"] . " " . $value["lastname"] . '</td>';

				$html .= '<td>' . $value["phonenumber"] . '</td>';

				$html .= '<td>' . $value["Pan"] . '</td>';

				$state = $this->clients_model->get_state($value["state"]);

				$html .= '<td>' . $state->state_name . '</td>';

				$city = $this->clients_model->get_city($value["dist"]);

				$html .= '<td>' . $city->city_name . '</td>';

				$html .= '</tr>';

			}

			echo $html;

		}

		

		public function export_TraderMaster()

		{

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) {

				

				$company_detail = $this->clients_model->get_company_detail();

				$result = $this->clients_model->GetAccountList();

				

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				$center_addr = array($address, );

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				

				$set_col_tk = [];

				$set_col_tk["AccountID "] = 'AccountID ';

				$set_col_tk["Business Name"] = 'Business Name';

				$set_col_tk["User Type"] = 'User Type';

				$set_col_tk["Contact Person"] = 'Contact Person';

				$set_col_tk["Mobile No"] = 'Mobile No ';

				$set_col_tk["Pan No"] = 'Pan No';

				$set_col_tk["State"] = 'State';

				$set_col_tk["City"] = 'City';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				foreach ($result as $k => $value) {

					

					$list_add = [];

					$list_add[] = $value["AccountID"];

					$list_add[] = $value["company"];

					$list_add[] = $value["Name"];

					$list_add[] = $value["firstname"] . " " . $value["lastname"];

					$list_add[] = $value["phonenumber"];

					$list_add[] = $value["Pan"];

					$list_add[] = $value["state_name"];

					$list_add[] = $value["city_name"];

					

					$list_add[] = $row_a;

					

					$writer->writeSheetRow('Sheet1', $list_add);

					

				}

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'TraderMaster.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

                'site_url' => site_url(),

                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

			

		}

		

		public function export_BrokerMaster()

		{

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) {

				

				$company_detail = $this->clients_model->get_company_detail();

				

				$data = $this->clients_model->get_state($value["state"]);

				$result = $this->clients_model->GetBrokerList($data);

				// $result = $this->clients_model->get_city($value["dist"]);

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				$center_addr = array($address, );

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				

				$set_col_tk = [];

				$set_col_tk["AccountID "] = 'AccountID ';

				$set_col_tk["Business Name"] = 'Business Name';

				$set_col_tk["User Type"] = 'User Type';

				$set_col_tk["Contact Person"] = 'Contact Person';

				$set_col_tk["Mobile No"] = 'Mobile No ';

				$set_col_tk["Pan No"] = 'Pan No';

				$set_col_tk["State"] = 'State';

				$set_col_tk["City"] = 'City';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				foreach ($result as $k => $value) {

					

					$list_add = [];

					$list_add[] = $value["AccountID"];

					$list_add[] = $value["company"];

					$list_add[] = $value["Name"];

					$list_add[] = $value["firstname"] . " " . $value["lastname"];

					$list_add[] = $value["phonenumber"];

					$list_add[] = $value["Pan"];

					$list_add[] = $value["state"];

					$list_add[] = $value["dist"];

					

					$list_add[] = $row_a;

					

					$writer->writeSheetRow('Sheet1', $list_add);

					

				}

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'BrokerMaster.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

                'site_url' => site_url(),

                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

		}

		

		public function export_FarmerMaster()

		{

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) {

				

				$company_detail = $this->clients_model->get_company_detail();

				$search = $this->input->post('search');

				$result = $this->clients_model->GetFarmarList($search);

				

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				$center_addr = array($address, );

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				

				$set_col_tk = [];

				$set_col_tk["AccountID "] = 'AccountID ';

				$set_col_tk["Farmer Name"] = 'Farmer Name';

				$set_col_tk["Mobile No"] = 'Mobile No ';

				$set_col_tk["Aadhaar Number"] = 'Aadhaar Number';

				$set_col_tk["State"] = 'State';

				$set_col_tk["City"] = 'City';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				foreach ($result as $k => $value) {

					

					$list_add = [];

					$list_add[] = $value["AccountID"];

					$list_add[] = $value["firstname"] . " " . $value["lastname"];

					$list_add[] = $value["phonenumber"];

					$list_add[] = $value["adharno"];

					$list_add[] = $value["state_name"];

					$list_add[] = $value["city_name"];

					$list_add[] = $row_a;

					

					$writer->writeSheetRow('Sheet1', $list_add);

					

				}

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'FarmerMaster.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

                'site_url' => site_url(),

                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

		}

		

		public function export_customerlist()

		{

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) {

				

				$company_detail = $this->clients_model->get_company_detail();

				$post_data = $this->input->post();

				$result = $this->clients_model->get_table_on_load_filter($post_data);

				

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				$center_addr = array($address, );

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				

				$set_col_tk = [];

				$set_col_tk["Date "] = 'Date ';

				$set_col_tk["AccountID "] = 'Account ID ';

				$set_col_tk["company"] = 'Account Name';

				$set_col_tk["User Type"] = 'User Type ';

				$set_col_tk["State"] = 'State';

				$set_col_tk["City"] = 'City';

				$set_col_tk['Address'] = 'Address';

				$set_col_tk['Aadhaar / PAN'] = 'Aadhaar / PAN';

				$set_col_tk['GSTIN'] = 'GSTIN';

				$set_col_tk['Registration Location'] = 'Registration Location';

				$set_col_tk['Referance By'] = 'Referance By';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				foreach ($result as $k => $value) {

					

					if ($value['CustomerType'] == "1") {

						$State = $value["state"];

						$City = $value["dist"];

						$Address = $value["house"] . " " . $value["street"] . " " . $value["loc"] . " " . $value["po"] . ' ' . $value["subdist"] . " - " . $value["pincode"];

						$Aadhaar_pan = $value["adharno"];

						$Gst_no = "";

						} else {

						$State = $value["GSTState"];

						$City = "";

						$Address = $value["address"];

						$Aadhaar_pan = $value["panno"];

						$Gst_no = $value["gstin"];

					}

					$dots = "";

					if(strlen($Address) > 50){

						$dots = '...';

					}

					

					

					$list_add = [];

					$list_add[] = _d(substr($value["datecreated"],0,10));

					$list_add[] = $value["AccountID"];

					$list_add[] = $value["company"];

					$list_add[] = $value["CustomerTypeName"];

					$list_add[] = $value["state_name"];

					$list_add[] = $City;

					$list_add[] = $Address;

					$list_add[] = $Aadhaar_pan;

					$list_add[] = $Gst_no;

					$list_add[] = $value["RegAddress"];

					$list_add[] = $value["firstname"]." ". $value["lastname"];

					$list_add[] = $row_a;

					

					$writer->writeSheetRow('Sheet1', $list_add);

					

				}

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'Customerlist.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

                'site_url' => site_url(),

                'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

		}

	}

