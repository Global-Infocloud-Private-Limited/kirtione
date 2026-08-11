<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class Rate_master extends AdminController
	{
		private $not_importable_fields = ['id'];

		public function __construct()
		{
			parent::__construct();
			$this->load->model('rate_master_model');

		}

		//================================= Not in USE =================================
		public function index()
		{
			if (!has_permission_new('ratemaster', '', 'view')) {
				access_denied('rate Master');
			}

			$this->load->model('taxes_model');
			$this->load->model('clients_model');
			$this->load->model('sale_reports_model');
			$data['taxes']        = $this->taxes_model->get();
			$data['items_groups'] = $this->rate_master_model->get_groups();
			$data['states'] = $this->rate_master_model->get_state();
			$data['groups'] = $this->clients_model->get_groups();
			$data['items_main_groups'] = $this->rate_master_model->get_main_groups();
			$data['items_sub_groups'] = $this->rate_master_model->get_sub_groups();
			$data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();

			$data['base_currency'] = $this->currencies_model->get_base_currency();

			$data['title'] = _l('rate_master');
			$this->load->view('admin/rate_master/manage', $data);
		}
		public function Charges()
		{
			if (!has_permission_new('Charges', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Charges Master';
			$data['commodity'] = $this->rate_master_model->getCommodity();
			$data['center'] = $this->rate_master_model->getCenter();
			$data['Charges'] = $this->rate_master_model->GetCharges();
			$this->load->view('admin/rate_master/charges',$data);
		}

		public function getUpdatedCharges(){
			if (!has_permission_new('Charges', '', 'create') && !has_permission_new('Charges', '', 'edit')) {
				access_denied('invoices');
			}
			$this->db->truncate('tblCharges');
			$user_id = $this->session->userdata('username');
			$inputData = $this->input->post();
			$ItemDiv = $inputData["inputData"];

			$data = json_decode($ItemDiv);

			foreach($data as $key => $value){

				$Id_array = explode('-', $value['0']);

				$ItemID = $Id_array['0'];
				$CenterID = $Id_array['1'];

				if($value !== "" ){

					$data_insert = array(
					'ItemID' =>$ItemID,
					'CenterID' =>$CenterID,
					'Rate' =>$value['1'],
					'UserID' =>$user_id,
					'TransDate' =>date('Y-m-d H:i:s'),
					'IsActive' =>'Y'
					);
					$result = $this->db->insert('tblCharges', $data_insert);
					echo ($result);
				}
			}
		}

		public function TraderRateMaster()
		{
			if (!has_permission_new('DailyTraderRate', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Kirti Purchase Rate for Trader';
			$AllRates = $this->rate_master_model->GetRate();
			$this->load->library('import/import_rate_master', [], 'import');
			$data_array = array();
			$mm = array('CenterID','CenterName','ItemID','ItemName','Rate');
			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));
			// Download Sample file
			if ($this->input->post('download_sample') === 'true') {
				$this->import->RateMasterSample("T");
			}
			//============== Import rate ===========================================
			if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $selected_company = $this->session->userdata('root_company');
                $cuurent_user = $this->session->userdata('username');
                $RateType = "T";
				$this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->RateImport($selected_company,$cuurent_user,$RateType);

				$data['total_rows_post'] = $this->import->totalRows();

				if (!$this->import->isSimulation()) {
					set_alert('success', _l('import_total_imported', $this->import->totalImported()));
					redirect(admin_url('Rate_master/TraderRateMaster'));
				}
			}
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();

			$data['center'] = $this->rate_master_model->getCenter();
			$data['CenterWiseCommodity'] = $this->rate_master_model->GetCenterWiseCommodity();
			$data['Competitor'] = $this->rate_master_model->GetCompetitor();
			$data['Rate'] = $AllRates;
			$this->load->view('admin/rate_master/TraderRateMaster',$data);

		}

		public function CityWiseCommodityRateUpdate()
		{
			if (!has_permission_new('CityWiseCommodityRate', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'City Wise Commodity Rate Update';
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$this->load->view('admin/rate_master/CityWiseCommodityRateUpdate', $data);
		}

		public function GetItemWiseCity()
		{
			if (!has_permission_new('CityWiseCommodityRate', '', 'view')) {
				access_denied('invoices');
			}

			$ItemID = $this->input->post('ItemID');
			if ($ItemID == '') {
				echo json_encode([]);
				return;
			}
			$CityList = $this->rate_master_model->GetItemWiseCity($ItemID);
			echo json_encode($CityList);
		}

		public function UpdateCityWiseRate()
		{
			if (!has_permission_new('CityWiseCommodityRate', '', 'edit')) {
				access_denied('invoices');
			}

			$Commodity = $this->input->post('Commodity');
			$city_rates = json_decode($this->input->post('city_rates'), true);

			if ($Commodity == '' || empty($city_rates) || !is_array($city_rates)) {
				echo json_encode(['status' => false, 'message' => 'Please select commodity and enter at least one valid rate.']);
				die;
			}

			// echo json_encode(['status' => false, 'message' => 'Update temporarily disabled.']); die; // TEMP: remove this line to enable update

			$updatedCities = 0;
			$failedCities = 0;

			foreach ($city_rates as $cityRate) {
				$cityId = isset($cityRate['city_id']) ? $cityRate['city_id'] : '';
				$new_rate = isset($cityRate['rate']) ? trim($cityRate['rate']) : '';

				if ($cityId == '' || $new_rate === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $new_rate) || floatval($new_rate) < 0) {
					continue;
				}

				$centerIDs = $this->rate_master_model->GetCenterIDsByCityAndItem($Commodity, $cityId);
				if (empty($centerIDs)) {
					$failedCities++;
					continue;
				}

				if ($this->updateRateForCenters($Commodity, $centerIDs, $new_rate, 'T')) {
					$updatedCities++;
				} else {
					$failedCities++;
				}
			}

			if ($updatedCities > 0) {
				$message = 'Rates updated successfully for ' . $updatedCities . ' city/cities.';
				if ($failedCities > 0) {
					$message .= ' ' . $failedCities . ' city/cities could not be updated.';
				}
				echo json_encode(['status' => true, 'message' => $message]);
				die;
			}

			echo json_encode(['status' => false, 'message' => 'No rates were updated. Please enter valid rates with up to 2 decimal places (zero or greater).']);
			die;
		}

		public function CityWiseFarmerCommodityRateUpdate()
		{
			if (!has_permission_new('CityWiseFarmerCommodityRate', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Kirti Purchase Farmer Rate City Wise';
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$this->load->view('admin/rate_master/CityWiseFarmerCommodityRateUpdate', $data);
		}

		public function GetItemWiseCityFarmer()
		{
			if (!has_permission_new('CityWiseFarmerCommodityRate', '', 'view')) {
				access_denied('invoices');
			}

			$ItemID = $this->input->post('ItemID');
			if ($ItemID == '') {
				echo json_encode([]);
				return;
			}
			$CityList = $this->rate_master_model->GetItemWiseCity($ItemID, 'F');
			echo json_encode($CityList);
		}

		public function UpdateCityWiseFarmerRate()
		{
			if (!has_permission_new('CityWiseFarmerCommodityRate', '', 'edit')) {
				access_denied('invoices');
			}

			$Commodity = $this->input->post('Commodity');
			$city_rates = json_decode($this->input->post('city_rates'), true);

			if ($Commodity == '' || empty($city_rates) || !is_array($city_rates)) {
				echo json_encode(['status' => false, 'message' => 'Please select commodity and enter at least one valid rate.']);
				die;
			}

			$updatedCities = 0;
			$failedCities = 0;

			foreach ($city_rates as $cityRate) {
				$cityId = isset($cityRate['city_id']) ? $cityRate['city_id'] : '';
				$new_rate = isset($cityRate['rate']) ? trim($cityRate['rate']) : '';

				if ($cityId == '' || $new_rate === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $new_rate) || floatval($new_rate) < 0) {
					continue;
				}

				$centerIDs = $this->rate_master_model->GetCenterIDsByCityAndItem($Commodity, $cityId);
				if (empty($centerIDs)) {
					$failedCities++;
					continue;
				}

				if ($this->updateRateForCenters($Commodity, $centerIDs, $new_rate, 'F')) {
					$updatedCities++;
				} else {
					$failedCities++;
				}
			}

			if ($updatedCities > 0) {
				$message = 'Rates updated successfully for ' . $updatedCities . ' city/cities.';
				if ($failedCities > 0) {
					$message .= ' ' . $failedCities . ' city/cities could not be updated.';
				}
				echo json_encode(['status' => true, 'message' => $message]);
				die;
			}

			echo json_encode(['status' => false, 'message' => 'No rates were updated. Please enter valid rates with up to 2 decimal places (zero or greater).']);
			die;
		}

		public function MSPRateUpdate()
		{
			if (!has_permission_new('MSPRate', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Kirti MSP Rate Master';
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();

			$data['Rate'] = $this->rate_master_model->GetUpdatedRateMSP();
			$this->load->view('admin/rate_master/MSPRateUpdate',$data);
		}

		public function FarmerRateUpdate()
		{
			if (!has_permission_new('DailyFarmerRate', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Kirti Purchase Rate for Farmer';
			$AllRates = $this->rate_master_model->GetFarmerRate();
			$this->load->library('import/import_rate_master', [], 'import');
			$data_array = array();
			$mm = array('CenterID','CenterName','ItemID','ItemName','Rate');
			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));
			// Download Sample file
			if ($this->input->post('download_sample') === 'true') {
				$this->import->RateMasterSample("F");
			}
			//============== Import rate ===========================================
			if ($this->input->post()
			&& isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '')
			{
				$selected_company = $this->session->userdata('root_company');
				$cuurent_user = $this->session->userdata('username');
				$RateType = "F";
				$this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->RateImport($selected_company,$cuurent_user,$RateType);

				$data['total_rows_post'] = $this->import->totalRows();

				if (!$this->import->isSimulation()) {
					set_alert('success', _l('import_total_imported', $this->import->totalImported()));
					redirect(admin_url('Rate_master/FarmerRateUpdate'));
				}
			}
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();

			$data['center'] = $this->rate_master_model->getCenter();
			$data['CenterWiseCommodity'] = $this->rate_master_model->GetCenterWiseCommodity();
			$data['Competitor'] = $this->rate_master_model->GetCompetitor();
			$data['Rate'] = $AllRates;
			$this->load->view('admin/rate_master/FarmerRateMaster',$data);
		}

		public function CompRateUpdate()
		{
			if (!has_permission_new('DailyCompetitorRate', '', 'view')) {
				access_denied('invoices');
			}
			$this->load->library('import/import_rate_master', [], 'import');
			$mm = array('CenterID','ItemID','CompID','Rate');

			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));
			if ($this->input->post('download_sample') === 'true') {
				$this->import->downloadSample();
			}
			$data['title'] = 'Competitor Purchase Rate';
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();

			$data['center'] = $this->rate_master_model->getCenter();
			$data['CenterWiseCommodity'] = $this->rate_master_model->GetCenterWiseCommodity();
			$data['Competitor'] = $this->rate_master_model->GetAllCompetitor();
			$data['Rate'] = $this->rate_master_model->GetCompetitorRate();
			$this->load->view('admin/rate_master/CompetitorRateMaster',$data);
		}

		public function MandiRateUpdate()
		{
			if (!has_permission_new('DailyMandiRate', '', 'view')) {
				access_denied('invoices');
			}
			$this->load->library('import/import_rate_master', [], 'import');
			//$mm = array('CenterID','ItemID','CompID','Rate');
			$mm = array('CenterID','CenterName','ItemID','ItemName','MandiID','MandiName','Rate');
			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));
			if ($this->input->post('download_sample') === 'true') {
				$this->import->RateMasterSample('M');
			}
			$data['title'] = 'Mandi Purchase Rate';
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();

			$data['center'] = $this->rate_master_model->getCenter();
			$data['CenterWiseCommodity'] = $this->rate_master_model->GetCenterWiseCommodity();
			$data['Mandi'] = $this->rate_master_model->GetAllMandi();
			$data['Rate'] = $this->rate_master_model->GetMandiRate();
			$this->load->view('admin/rate_master/MandiRateMaster',$data);
		}

		public function GetItemWiseCenter()
		{
			if($this->input->post('ItemID') == ""){
				$ItemID = array();
				echo json_encode($ItemID);
				}else{
				$ItemID = explode(",",$this->input->post('ItemID'));
			}
			//$ItemID = $this->input->post('ItemID');
			$CenterList = $this->rate_master_model->GetItemWiseCenter($ItemID);
			echo json_encode($CenterList);
		}

		public function UpdateRate()
		{
			if (!has_permission_new('DailyTraderRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ID = $this->input->post('id');
			$Id_array = explode("_",$ID);
			$ItemID = $Id_array[0];
			$CenterID = $Id_array[1];
			$KeyID = $Id_array[2];
			$new_rate = $this->input->post('new_rate');


			$this->db->where('ItemID', $ItemID);
			$this->db->where('CenterID', $CenterID);
			$this->db->where('KeyID', $KeyID);
			$this->db->where('Type', 'T');
			if($this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N'])){
				$curl = curl_init();
				// All Booking Reject if rate is grater than booking rate
				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
				$this->db->where('tbllead_master.basic_rate >', $new_rate);
				$this->db->where('tbllead_master.ItemID',$ItemID);
				$this->db->where('tbllead_master.CenterID',$CenterID);
				$this->db->where('tbllead_master.TType',"P");
				$this->db->where('tbllead_master.IsApprove','NA');
				$this->db->where('tblclients.CustomerType !=','1');
				$rejectAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate >', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('TType',"P");
				$this->db->where('IsApprove', 'NA');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'N'])){

					foreach($rejectAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' rejected by kirti';
						$fcm = array(
                        "title"=>"Trade Rejected by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}


				// Accept All booking if rate is less as compare to booking rate

				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID',$ItemID);
				$this->db->where('CenterID',$CenterID);
				$this->db->where('IsApprove','NA');
				$this->db->where('TType',"P");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				$AcceptAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('IsApprove', 'NA');
				$this->db->where('TType',"P");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'Y'])){

					foreach($AcceptAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' Accepted by kirti';
						$fcm = array(
                        "title"=>"Trade Accepted by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}
				curl_close($curl);

				$data_insert = array(
                'ItemID' =>$ItemID,
                'CenterID' =>$CenterID,
                'Type' =>"T",
                'KeyID' =>$KeyID,
                'Rate' =>$new_rate,
                'UserID' =>$user_id,
                'TransDate' =>date('Y-m-d H:i:s'),
                'IsActive' =>'Y'
				);
				if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
					/*$data_rate_array =  array(
						"CustType" => '00002',
						"CenterID"=>$CenterID,
						"ItemID"=>$ItemID,
						"Rate"=>$new_rate
						);
						$rate_data = json_encode($data_rate_array);
						$curl = curl_init();
						curl_setopt_array($curl, array(

						CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/UpdatePriceFromGIC", //  -> LIVE URL
						//CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/UpdatePriceFromGIC", // -> DEV URL
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => $rate_data,
						CURLOPT_HTTPHEADER => array(
						"content-type: application/json"
                        ),
						)
						);
						$response = curl_exec($curl);
						$response_array = json_decode($response);
						$err = curl_error($curl);
					curl_close($curl);*/
					echo json_encode(1);
					die;
				}
				echo json_encode(0);
				die;
			}
			echo json_encode(false);
			die;
		}
		public function UpdateRateMSP()
		{
			if (!has_permission_new('MSPRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$formdata = $this->input->post('formdata');

			$return = false;
			foreach($formdata as $ItemID => $value){
				$GetMSPRate = $this->rate_master_model->GetMSPRate($ItemID);
				// print_r($GetMSPRate);die;
				if(!empty($GetMSPRate)){
					if($value !== '' && $value != $GetMSPRate->Rate)
					{
						$this->db->where('ItemID', $ItemID);
						$this->db->where('KeyID', 'C01');
						$this->db->where('Type', 'MSP');
						if($this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N'])){
							$data_insert = array(
							'ItemID' =>$ItemID,
							'Type' =>"MSP",
							'KeyID' =>'C01',
							'Rate' =>$value,
							'UserID' =>$user_id,
							'TransDate' => date('Y-m-d H:i:s'),
							'IsActive' =>'Y'
							);
							if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
								$return = true;
							}
						}
					}
				}
				else
				{
					if($value !== '')
					{
						$data_insert = array(
						'ItemID' =>$ItemID,
						'Type' =>"MSP",
						'KeyID' =>'C01',
						'Rate' =>$value,
						'UserID' =>$user_id,
						'TransDate' => date('Y-m-d H:i:s'),
						'IsActive' =>'Y'
						);
						if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
							$return = true;
						}
					}
				}
			}
			echo json_encode($return);
			die;
		}
		public function UpdateRateMSPSingle()
		{
			if (!has_permission_new('MSPRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ItemID = $this->input->post('ItemID');
			$value = $this->input->post('new_rate');

			$return = false;
			$GetMSPRate = $this->rate_master_model->GetMSPRate($ItemID);
			// print_r($GetMSPRate);die;
			if(!empty($GetMSPRate)){
				if($value !== '' && $value != $GetMSPRate->Rate)
				{
					$this->db->where('ItemID', $ItemID);
					$this->db->where('KeyID', 'C01');
					$this->db->where('Type', 'MSP');
					if($this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N'])){
						$data_insert = array(
						'ItemID' =>$ItemID,
						'Type' =>"MSP",
						'KeyID' =>'C01',
						'Rate' =>$value,
						'UserID' =>$user_id,
						'TransDate' => date('Y-m-d H:i:s'),
						'IsActive' =>'Y'
						);
						if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
							$return = true;
						}
					}
				}
			}
			else
			{
				if($value !== '')
				{
					$data_insert = array(
					'ItemID' =>$ItemID,
					'Type' =>"MSP",
					'KeyID' =>'C01',
					'Rate' =>$value,
					'UserID' =>$user_id,
					'TransDate' => date('Y-m-d H:i:s'),
					'IsActive' =>'Y'
					);
					if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
						$return = true;
					}
				}
			}

			echo json_encode($return);
			die;
		}

		public function UpdateFarmerRate()
		{
			if (!has_permission_new('DailyFarmerRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ID = $this->input->post('id');
			$Id_array = explode("_",$ID);
			$ItemID = $Id_array[0];
			$CenterID = $Id_array[1];
			$KeyID = $Id_array[2];
			$new_rate = $this->input->post('new_rate');


			$this->db->where('ItemID', $ItemID);
			$this->db->where('CenterID', $CenterID);
			$this->db->where('KeyID', $KeyID);
			$this->db->where('Type', 'F');
			if($this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N'])){
				$curl = curl_init();
				// All Booking Reject if rate is grater than booking rate
				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
				$this->db->where('tbllead_master.basic_rate >', $new_rate);
				$this->db->where('tbllead_master.ItemID',$ItemID);
				$this->db->where('tbllead_master.CenterID',$CenterID);
				$this->db->where('tbllead_master.TType',"P");
				$this->db->where('tbllead_master.IsApprove','NA');
				$this->db->where('tblclients.CustomerType','1');
				$rejectAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate >', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('TType',"P");
				$this->db->where('IsApprove', 'NA');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'N'])){

					foreach($rejectAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' rejected by kirti';
						$fcm = array(
                        "title"=>"Trade Rejected by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}


				// Accept All booking if rate is less as compare to booking rate

				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID',$ItemID);
				$this->db->where('CenterID',$CenterID);
				$this->db->where('IsApprove','NA');
				$this->db->where('TType',"P");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				$AcceptAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('IsApprove', 'NA');
				$this->db->where('TType',"P");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'Y'])){

					foreach($AcceptAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' Accepted by kirti';
						$fcm = array(
                        "title"=>"Trade Accepted by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}
				curl_close($curl);

				$data_insert = array(
                'ItemID' =>$ItemID,
                'CenterID' =>$CenterID,
                'Type' =>"F",
                'KeyID' =>$KeyID,
                'Rate' =>$new_rate,
                'UserID' =>$user_id,
                'TransDate' =>date('Y-m-d H:i:s'),
                'IsActive' =>'Y'
				);
				if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
					/*$data_rate_array =  array(
						"CustType" => '00001',
						"CenterID"=>$CenterID,
						"ItemID"=>$ItemID,
						"Rate"=>$new_rate
						);
						$rate_data = json_encode($data_rate_array);
						$curl = curl_init();
						curl_setopt_array($curl, array(

						CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/UpdatePriceFromGIC", //  -> LIVE URL
						//CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/UpdatePriceFromGIC", // -> DEV URL
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => $rate_data,
						CURLOPT_HTTPHEADER => array(
						"content-type: application/json"
                        ),
						)
						);
						$response = curl_exec($curl);
						$response_array = json_decode($response);
						$err = curl_error($curl);
					curl_close($curl);*/
					echo json_encode(1);
					die;
				}

				echo json_encode(0);
				die;
			}
			echo json_encode(false);
			die;
		}

		public function UpdateCompetitorRateByAjax()
		{
			if (!has_permission_new('DailyCompetitorRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ID = $this->input->post('id');
			$Id_array = explode("-",$ID);
			$ItemID = $Id_array[0];
			$CenterID = $Id_array[1];
			$KeyID = $Id_array[2];
			$new_rate = $this->input->post('new_rate');
			if($KeyID == "C02"){
				$Type = "N";
				}else{
				$Type = "C";
			}
			$Type_array = array("C","N");
			$this->db->where('ItemID', $ItemID);
			$this->db->where('CenterID', $CenterID);
			$this->db->where('KeyID', $KeyID);
			$this->db->where_in('Type', $Type_array);
			$this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N']);

			$data_insert = array(
            'ItemID' =>$ItemID,
            'CenterID' =>$CenterID,
            'Type' =>$Type,
            'KeyID' =>$KeyID,
            'Rate' =>$new_rate,
            'UserID' =>$user_id,
            'TransDate' =>date('Y-m-d H:i:s'),
            'IsActive' =>'Y'
			);
			if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
				echo json_encode(1);
				die;
			}
			echo json_encode(false);
			die;
		}

		public function UpdateMandiRateByAjax()
		{
			if (!has_permission_new('DailyMandiRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ID = $this->input->post('id');
			$Id_array = explode("-",$ID);
			$ItemID = $Id_array[0];
			$CenterID = $Id_array[1];
			$KeyID = $Id_array[2];
			$new_rate = $this->input->post('new_rate');

			$Type_array = array("M");
			$this->db->where('ItemID', $ItemID);
			$this->db->where('CenterID', $CenterID);
			$this->db->where('KeyID', $KeyID);
			$this->db->where_in('Type', $Type_array);
			$this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N']);

			$data_insert = array(
            'ItemID' =>$ItemID,
            'CenterID' =>$CenterID,
            'Type' =>"M",
            'KeyID' =>$KeyID,
            'Rate' =>$new_rate,
            'UserID' =>$user_id,
            'TransDate' =>date('Y-m-d H:i:s'),
            'IsActive' =>'Y'
			);
			if($this->db->insert(db_prefix() . 'RateMaster', $data_insert)){
				echo json_encode(1);
				die;
			}
			echo json_encode(false);
			die;
		}
		public function UpdateRateByForm()
		{
			if (!has_permission_new('DailyTraderRate', '', 'edit')) {
				access_denied('invoices');
			}
			$Commodity = $this->input->post('Commodity');
			$CenterIDs = $this->input->post('CenterIDs');
			$new_rate = $this->input->post('new_rate');
			$CenterID_array = explode(",", $CenterIDs);

			if ($this->updateRateForCenters($Commodity, $CenterID_array, $new_rate, 'T')) {
				echo json_encode(1);
				die;
			}
			echo json_encode(false);
			die;
		}

		public function UpdateFarmerRateByForm()
		{
			if (!has_permission_new('DailyFarmerRate', '', 'edit')) {
				access_denied('invoices');
			}
			$Commodity = $this->input->post('Commodity');
			$CenterIDs = $this->input->post('CenterIDs');
			$new_rate = $this->input->post('new_rate');
			$CenterID_array = explode(",", $CenterIDs);

			if ($this->updateRateForCenters($Commodity, $CenterID_array, $new_rate, 'F')) {
				echo json_encode(1);
				die;
			}
			echo json_encode(false);
			die;
		}

		function send_notification($title,$screen,$body,$booking_id,$to)
		{
			$data_arrary = array(
            "title"=>$title,
            "screen"=>$screen,
            "body"=>$body,
            "booking_id"=>$booking_id
			);
			$post_data = array(
            "priority"=>"HIGH",
            "data"=>$data_arrary,
            "to"=>$to
			);
			$finel_data = json_encode($post_data);
			$curl = curl_init();
			curl_setopt_array($curl, array(
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
		}
		public function UpdateTradingStatus()
		{
			if (!has_permission_new('DailyFarmerRate', '', 'edit') &&  !has_permission_new('DailyTraderRate', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$Commoditys = $this->input->post('Commoditys');
			if($Commoditys == ""){
				$Commoditys_array = array();
				}else{
				$Commoditys_array = explode(",",$Commoditys);
			}
			$CenterIDs = $this->input->post('CenterIDs');
			if($CenterIDs == ""){
				$CenterIDs_array = array();
				}else{
				$CenterIDs_array = explode(",",$CenterIDs);
			}
			$Type = $this->input->post('Type');
			$trading_status = $this->input->post('trading_status');
			if($Type == "F"){
				$update_array = array(
                "TradeOnOffFarmer" =>$trading_status
				);
				}else{
				$update_array = array(
                "TradeOnOff" =>$trading_status
				);
			}
			if(empty($Commoditys_array) && empty($CenterIDs_array)){
				if($this->db->update(db_prefix() . 'Center_wise_item', $update_array)){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(empty($Commoditys_array)){
				$this->db->where_in('CenterID', $CenterIDs_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', $update_array)){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(empty($CenterIDs_array)){
				$this->db->where_in('ItemID', $Commoditys_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', $update_array)){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(!empty($CenterIDs_array) && !empty($Commoditys_array)){
				$this->db->where_in('ItemID', $Commoditys_array);
				$this->db->where_in('CenterID', $CenterIDs_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', $update_array)){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
			}
		}
		//======================= Kirti Sale Rate Master ===============================
		public function SellRateUpdate()
		{
			if (!has_permission_new('SellRateMaster', '', 'view')) {
				access_denied('invoices');
			}
			$data['title'] = 'Rate Master';
			$AllRates = $this->rate_master_model->GetSaleRate();
			$this->load->library('import/import_rate_master', [], 'import');
			$data_array = array();
			$mm = array('CenterID','CenterName','ItemID','ItemName','Rate');
			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));
			// Download Sample file
			if ($this->input->post('download_sample') === 'true') {
				$this->import->RateMasterSample("TS");
			}
			//============== Import rate ===========================================
			if ($this->input->post()
			&& isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '')
			{
				$selected_company = $this->session->userdata('root_company');
				$cuurent_user = $this->session->userdata('username');
				$RateType = "TS";
				$this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->RateImport($selected_company,$cuurent_user,$RateType);

				$data['total_rows_post'] = $this->import->totalRows();

				if (!$this->import->isSimulation()) {
					set_alert('success', _l('import_total_imported', $this->import->totalImported()));
					redirect(admin_url('Rate_master/SellRateUpdate'));
				}
			}
			$data['commodity'] = $this->rate_master_model->GetItem_Staff_wise();
			$data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();

			$data['center'] = $this->rate_master_model->getCenter();
			$data['CenterWiseCommodity'] = $this->rate_master_model->GetCenterWiseCommodity();
			$data['Competitor'] = $this->rate_master_model->GetCompetitor();
			$data['Rate'] = $AllRates;
			$this->load->view('admin/rate_master/SellRateMaster',$data);
		}


		public function UpdateSaleTradingStatus()
		{
			if (!has_permission_new('SaleRateMaster', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$Commoditys = $this->input->post('Commoditys');
			if($Commoditys == ""){
				$Commoditys_array = array();
				}else{
				$Commoditys_array = explode(",",$Commoditys);
			}
			$CenterIDs = $this->input->post('CenterIDs');
			if($CenterIDs == ""){
				$CenterIDs_array = array();
				}else{
				$CenterIDs_array = explode(",",$CenterIDs);
			}
			$trading_status = $this->input->post('trading_status');
			if(empty($Commoditys_array) && empty($CenterIDs_array)){
				if($this->db->update(db_prefix() . 'Center_wise_item', ['SaleTradeOnOff'=>$trading_status])){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(empty($Commoditys_array)){
				$this->db->where_in('CenterID', $CenterIDs_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', ['SaleTradeOnOff'=>$trading_status])){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(empty($CenterIDs_array)){
				$this->db->where_in('ItemID', $Commoditys_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', ['SaleTradeOnOff'=>$trading_status])){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
				}elseif(!empty($CenterIDs_array) && !empty($Commoditys_array)){
				$this->db->where_in('ItemID', $Commoditys_array);
				$this->db->where_in('CenterID', $CenterIDs_array);
				if($this->db->update(db_prefix() . 'Center_wise_item', ['SaleTradeOnOff'=>$trading_status])){
					echo json_encode(true);
					}else{
					echo json_encode(false);
				}
			}
		}


		public function UpdateSaleRateByForm()
		{
			if (!has_permission_new('SaleRateMaster', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$Commodity = $this->input->post('Commodity');
			$CenterIDs = $this->input->post('CenterIDs');
			$new_rate = $this->input->post('new_rate');
			$CenterID_array = explode(",",$CenterIDs);
			$KeyID = 'C01';
			$this->db->where('ItemID', $Commodity);
			$this->db->where_in('CenterID', $CenterID_array);
			$this->db->where('KeyID', $KeyID);
			if($this->db->update(db_prefix() . 'SaleRateMaster', ['IsActive'=>'N'])){
				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID', $Commodity);
				$this->db->where_in('CenterID', $CenterID_array);
				$this->db->where('IsApprove', 'NA');
				$this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'N']);
				$insert = 0;
				foreach($CenterID_array as $CenterID){
					$data_insert = array(
                    'ItemID' =>$Commodity,
                    'CenterID' =>$CenterID,
                    'KeyID' =>$KeyID,
                    'Rate' =>$new_rate,
                    'UserID' =>$user_id,
                    'TransDate' =>date('Y-m-d H:i:s'),
                    'IsActive' =>'Y'
					);
					if($this->db->insert(db_prefix() . 'SaleRateMaster', $data_insert)){
						$insert++;
					}
				}
				if($insert > 0){
					echo json_encode(1);
					die;
					}else{
					echo json_encode(0);
					die;
				}
			}
			echo json_encode(false);
			die;
		}

		public function UpdateSaleRate()
		{
			if (!has_permission_new('SaleRateMaster', '', 'edit')) {
				access_denied('invoices');
			}
			$user_id = $this->session->userdata('username');
			$ID = $this->input->post('id');
			$Id_array = explode("_",$ID);
			$ItemID = $Id_array[0];
			$CenterID = $Id_array[1];
			$KeyID = $Id_array[2];
			$new_rate = $this->input->post('new_rate');


			$this->db->where('ItemID', $ItemID);
			$this->db->where('CenterID', $CenterID);
			$this->db->where('KeyID', $KeyID);
			if($this->db->update(db_prefix() . 'SaleRateMaster', ['IsActive'=>'N'])){
				$curl = curl_init();
				// All Booking Reject if rate is grater than booking rate
				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->where('basic_rate >', $new_rate);
				$this->db->where('ItemID',$ItemID);
				$this->db->where('TType',"S");
				$this->db->where('CenterID',$CenterID);
				$this->db->where('IsApprove','NA');
				$rejectAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate >', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('TType',"S");
				$this->db->where('IsApprove', 'NA');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'N'])){

					foreach($rejectAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' rejected by kirti';
						$fcm = array(
                        "title"=>"Trade Rejected by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}


				// Accept All booking if rate is less as compare to booking rate

				$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID');
				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID',$ItemID);
				$this->db->where('CenterID',$CenterID);
				$this->db->where('IsApprove','NA');
				$this->db->where('TType',"S");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				$AcceptAll =  $this->db->get('tbllead_master')->result_array();

				$this->db->where('basic_rate <', $new_rate);
				$this->db->where('ItemID', $ItemID);
				$this->db->where('CenterID', $CenterID);
				$this->db->where('IsApprove', 'NA');
				$this->db->where('TType',"S");
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
				if($this->db->update(db_prefix() . 'lead_master', ['IsApprove'=>'Y'])){

					foreach($AcceptAll as $key=>$val){
						$ids = array($val["AccountID"],$val["BrokerID"]);
						$body = "Your BookingID : ".$val["BookingID"].' Accepted by kirti';
						$fcm = array(
                        "title"=>"Trade Accepted by kirti",
                        "body"=>$body,
                        "booking_id"=>$val["BookingID"],
                        "screen"=>1
						);
						$FCM_data = array(
                        "data"=>$fcm
						);
						$data_array =  array(
                        "interests" => $ids,
                        "fcm"=>$FCM_data
						);
						$data = json_encode($data_array);
						curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $data,
                        CURLOPT_HTTPHEADER => array(
						"authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
						"content-type: application/json"
						),
                        )
						);
						$response = curl_exec($curl);
						$err = curl_error($curl);
					}
				}
				curl_close($curl);

				$data_insert = array(
                'ItemID' =>$ItemID,
                'CenterID' =>$CenterID,
                'KeyID' =>$KeyID,
                'Rate' =>$new_rate,
                'UserID' =>$user_id,
                'TransDate' =>date('Y-m-d H:i:s'),
                'IsActive' =>'Y'
				);
				if($this->db->insert(db_prefix() . 'SaleRateMaster', $data_insert)){
					echo json_encode(1);
					die;
				}
				echo json_encode(0);
				die;
			}
			echo json_encode(false);
			die;
		}


		public function getUpdatedRate(){
			$user_id = $this->session->userdata('username');
			$inputData = $this->input->post();
			$ItemDiv = $inputData["inputData"];

			$data = json_decode($ItemDiv);

			foreach($data as $key => $value){

				$Id_array = explode('-', $value['0']);

				$ItemID = $Id_array['0'];
				$CenterID = $Id_array['1'];
				$KeyID = $Id_array['2'];

				if($value !== "" ){
					$this->db->where('ItemID', $ItemID);
					$this->db->where('CenterID', $CenterID);
					$this->db->where('KeyID', $KeyID);
					$this->db->update(db_prefix() . 'RateMaster', ['IsActive'=>'N']);
					$data_insert = array(
                    'ItemID' =>$ItemID,
                    'CenterID' =>$CenterID,
                    'KeyID' =>$KeyID,
                    'Rate' =>$value['1'],
                    'UserID' =>$user_id,
                    'TransDate' =>date('Y-m-d H:i:s'),
                    'IsActive' =>'Y'
					);

					$result = $this->db->insert(db_prefix() . 'RateMaster', $data_insert);
					echo ($result);
				}

			}
		}

		public function DailyRate(){

			$data['title'] = 'Add/Edit Commodities';
			$data['commodity'] = $this->rate_master_model->getCommodity();
			$data['center'] = $this->rate_master_model->getCenter();
			$this->load->view('admin/clients/dailyRate',$data);
		}

		public function getAllRates(){
			$data = $this->Clients_new_model->getRateData();
			$html = "";
			foreach($data as $key=>$value){
				$html .= '<tr>';
				$html .= '<td>'.$value["mandi"].'</td>';
				$html .= '<td>'.$value['commodity'].'</td>';
				$html .= '<td><input onchange="updateKirtiRateData('.$value["rate_id"].','.$value["kirti_rate"].')" type="text" value="'.$value["kirti_rate"].'" style="border:none"></td>';
				$html .= '<td><input type="text" value="'.$value["apmc_rate"].'" style="border:none"></td>';
				$html .= '<td><input type="text" value="'.$value["competitor_rate"].'" style="border:none"></td>';
				$html .= '</tr>';
			}
			echo $html;
		}

		public function getRateFromMandi(){
			$mandi = $this->input->post('mandi_name');
			$data = $this->Clients_new_model->getMandiRateData($mandi);
			$html = "";
			foreach($data as $key=>$value){
				$html .= '<tr>';
				$html .= '<td style="display:none">'.$value["rate_id"].'</td>';
				$html .= '<td>'.$value["mandi"].'</td>';
				$html .= '<td>'.$value['commodity'].'</td>';
				$html .= '<td>'.$value["kirti_rate"].'</td>';
				$html .= '<td>'.$value["apmc_rate"].'</td>';
				$html .= '<td>'.$value["competitor_rate"].'</td>';
				$html .= '<td><a style="cursor:pointer" onclick="editRate('.$value["rate_id"].')">Edit</a></td>';
				$html .= '</tr>';
			}
			echo $html;
		}

		public function getSingleRate(){
			$rate_id = $this->input->post('rate_id');
			$result = $this->Clients_new_model->getSingleRateData($rate_id);
			echo json_encode($result);
		}

		public function SaveRate(){
			$data = array(
            'mandi' => $this->input->post('mandi'),
            'commodity' => $this->input->post('commodity'),
            'kirti_rate' => $this->input->post('kirti_rate'),
            'competitor_rate' => $this->input->post('competitor_rate'),
            'ncdex_rate' => $this->input->post('ncdex_rate'),
			);
			$result = $this->Clients_new_model->SaveRateDetails($data);
			echo json_encode($result);
		}

		public function UpdateKirtiRate()
		{
			$data = array(
            'rate_id' => $this->input->post('rate_id'),
            'kirti_rate' => $this->input->post('kirti_rate'),
			);
			$result = $this->Clients_new_model->UpdateKirtiRateDetails($data);
			echo json_encode($result);
		}

		public function UpdateApmcRate()
		{
			$data = array(
            'rate_id' => $this->input->post('rate_id'),
            'apmc_rate' => $this->input->post('apmc_rate'),
			);
			$result = $this->Clients_new_model->UpdateApmcRateDetails($data);
			echo json_encode($result);
		}

		public function UpdateCompetitorRate()
		{
			$data = array(
            'rate_id' => $this->input->post('rate_id'),
            'competitor_rate' => $this->input->post('competitor_rate'),
			);
			$result = $this->Clients_new_model->UpdateCompetitorRateDetails($data);
			echo json_encode($result);
		}
		//============================ deduction matrix ================================
		public function DeductionMatrix(){
			if (!has_permission_new('DeductionMatrix', '', 'view')) {
				access_denied('DeductionMatrix');
			}
			$data['title'] = 'Deduction Matrix';
			$data['commodity'] = $this->rate_master_model->getCommodity();
			$data['AllQcParameter'] = $this->rate_master_model->getParameter();
			$this->load->view('admin/rate_master/deductionMatrix',$data);
		}

		public function GetQcParameterByItemID(){
			$ItemID = $this->input->post('ItemID');
			$result = $this->rate_master_model->GetQcParameterByItemID($ItemID);
			echo json_encode($result);
		}

		public function GetQcParameterDetailsByItemID(){
			$ItemID = $this->input->post('ItemID');
			$QCparameterID = $this->input->post('QCparameterID');
			$result = $this->rate_master_model->GetQcParameterDetailsByItemID($ItemID,$QCparameterID);
			echo json_encode($result);
		}

		//========================== deduction matrix end ==============================

		/*----------------      New Code        -------------------*/

		public function table()
		{
			if (!has_permission_new('ratemaster', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('rate_master');
				}
			}
		}


		/* Edit or update items / ajax request /*/
		public function manage()
		{
			if (has_permission_new('items', '', 'view')) {
				if ($this->input->post()) {
					$data = $this->input->post();
					if ($data['itemid'] == '') {
						if (!has_permission_new('items', '', 'create')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$id      = $this->invoice_items_model->add($data);
						$success = false;
						$message = '';
						if ($id) {
							$success = true;
							$message = _l('added_successfully', _l('sales_item'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->rate_model_model->get($id),
						]);
						} else {
						if (!has_permission_new('items', '', 'edit')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$success = $this->invoice_items_model->edit($data);
						$message = '';
						if ($success) {
							$message = _l('updated_successfully', _l('sales_item'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
						]);
					}
				}
			}
		}

		/* Edit or update items / ajax request /*/
		public function add_edit_rate_master()
		{
			if (has_permission_new('items', '', 'view')) {
				if ($this->input->post()) {
					$data = $this->input->post();

					if ($data['rate_master_id'] == '') {
						if (!has_permission_new('items', '', 'create')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$data["item_id"] = $data["itemid"];
						$data["assigned_rate"] = $data["assignrate"];
						unset($data["itemid"]);
						unset($data["assignrate"]);
						unset($data["itemid"]);
						unset($data["assignrate"]);
						unset($data["description"]);
						unset($data["group_id"]);
						unset($data["item_code"]);
						unset($data["long_description"]);
						unset($data["rate"]);
						unset($data["subgroup_id"]);
						unset($data["tax"]);
						unset($data["tax2"]);
						unset($data["unit"]);
						unset($data["rate_master_id"]);

						$id = $this->rate_master_model->add_rate_master($data);
						$success = false;
						$message = '';
						if ($id) {
							$success = true;
							$message = _l('added_successfully', _l('rate_master'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
						]);
						} else {
						if (!has_permission_new('items', '', 'edit')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}

						//$data["item_id"] = $data["itemid"];
						$data["assigned_rate"] = $data["assignrate"];
						$data["effective_date"] = to_sql_date($data["effective_date"])." 00:00:01";
						/*unset($data["state_id"]);
						unset($data["distributor_id"]);*/
						unset($data["itemid"]);
						unset($data["assignrate"]);
						unset($data["description"]);
						unset($data["group_id"]);
						$data["item_code"] = $data["item_code"];
						unset($data["long_description"]);
						unset($data["rate"]);
						unset($data["subgroup_id"]);
						unset($data["tax"]);
						unset($data["tax2"]);
						unset($data["unit"]);

						$get_data = $this->rate_master_model->get_rate_master_data_by_id($data["state_id"],$data["distributor_id"]);
						if($get_data["distributor_id"] == $data["distributor_id"] && $get_data["state_id"] == $data["state_id"]){

							$success = $this->rate_master_model->edit_rate_master($data);
							}else {
							unset($data["rate_master_id"]);
							$success = $this->rate_master_model->add_rate_master($data);
						}


						$message = '';
						if ($success) {
							$message = _l('updated_successfully', _l('rate_master'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
						]);
					}
				}
			}
		}

		public function import()
		{
			if (!has_permission_new('items', '', 'create')) {
				access_denied('Items Import');
			}

			$this->load->library('import/import_rate_master', [], 'import');
			$mm = array('item_id','assigned_rate');

			$this->import->setDatabaseFields($mm)
			->setCustomFields(get_custom_fields('rate_master'));

			if ($this->input->post('download_sample') === 'true') {
				$this->import->downloadSample();
			}

			if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                $selected_company = $this->session->userdata('root_company');
                $cuurent_user = $this->session->userdata('username');
                $states = $this->input->post('states');
                $distributor_id = $this->input->post('distributor_id');
                $effective_date = $this->input->post('effective_date');

				$this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->perform($states, $distributor_id, $effective_date,$selected_company,$cuurent_user);

				$data['total_rows_post'] = $this->import->totalRows();

				if (!$this->import->isSimulation()) {
					set_alert('success', _l('import_total_imported', $this->import->totalImported()));
					redirect(admin_url('rate_master/import'));
				}
			}
			$data['items_groups'] = $this->rate_master_model->get_groups();
			$data['states'] = $this->rate_master_model->get_state();
			$data['groups'] = $this->clients_model->get_groups();
			$data['items_main_groups'] = $this->rate_master_model->get_main_groups();
			$data['items_sub_groups'] = $this->rate_master_model->get_sub_groups();
			$data['title'] = _l('import');
			$this->load->view('admin/rate_master/import', $data);
		}


		/* Delete item*/
		public function delete($id)
		{
			if (!has_permission_new('items', '', 'delete')) {
				access_denied('Invoice Items');
			}

			/*echo $id;
			die;*/

			if (!$id) {
				redirect(admin_url('invoice_items'));
			}

			$response = $this->rate_master_model->delete($id);
			if (is_array($response) && isset($response['referenced'])) {
				set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
				} elseif ($response == true) {
				set_alert('success', _l('deleted', _l('invoice_item')));
				} else {
				set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
			}
			redirect(admin_url('rate_master'));
		}


		public function search()
		{
			if ($this->input->post() && $this->input->is_ajax_request()) {
				echo json_encode($this->invoice_items_model->search($this->input->post('q')));
			}
		}

		/* Get item by id / ajax */
		public function get_item_by_id($id,$state_id,$distributor_id)
		{
			if ($this->input->is_ajax_request()) {
				$item                     = $this->rate_master_model->get($id,$state_id,$distributor_id);
				$item->long_description   = nl2br($item->long_description);
				$item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
				$item->custom_fields      = [];

				$cf = get_custom_fields('items');

				foreach ($cf as $custom_field) {
					$val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
					if ($custom_field['type'] == 'textarea') {
						$val = clear_textarea_breaks($val);
					}
					$custom_field['value'] = $val;
					$item->custom_fields[] = $custom_field;
				}

				echo json_encode($item);
			}
		}

		public function export_rate_master()
		{
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			if($this->input->post()){

				$data =$this->rate_master_model->table_data($this->input->post());
				$distributor_id = $this->input->post('distributor_id');
				$state_id = $this->input->post('state_id');
				$data_state_name  = $this->db->get_where('tblxx_statelist',array('short_name'=>$state_id))->row_array();
				$data_distributor_name  = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();

				$writer = new XLSXWriter();
				//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
				//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
				//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
				//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 6);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);

				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 6);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				$msg = "Rate Master State: ".$data_state_name["state_name"] ." Distributor: " .$data_distributor_name["name"];
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 6);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);

				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);


				$set_col_tk = [];
				$set_col_tk["ItemID"] =  'ItemID';
				$set_col_tk["Item Name"] = 'Item Name';
				$set_col_tk["Unit"] = 'Unit';
				$set_col_tk["Basic Rate"] = 'Basic Rate';
				$set_col_tk["GST"] = 'GST';
				$set_col_tk["SaleRate"] = 'SaleRate';

				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);


				foreach ($data as $k => $value) {

					$list_add = [];
					$list_add[] = $value["item_code"];
					$list_add[] = $value["description"];
					$list_add[] = $value["unit"];
					if($value['state_id_2'] == !"" && $value['state_id_2'] !== "0" && $value['state_id_2'] == $state_id && $value['distributor_id_2'] == !"" && $value['distributor_id_2'] !== "0" && $value['distributor_id_2'] == $distributor_id ){
						$new_rate = $value['assigned_2'];
						} else {
						$new_rate = 0;
					}
					$rate = app_format_money($new_rate, get_base_currency());

					$list_add[] = $rate;
					$list_add[] = app_format_number($value['taxrate'])."%";
					$p = $value['taxrate'] /100;
					$Y = $p * $new_rate;
					$sale_rate = $new_rate + $Y;
					$sale_field = number_format($sale_rate, 2);
					$list_add[] = $sale_field;

					$writer->writeSheetRow('Sheet1', $list_add);

				}

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file);
					}
				}
				$filename = 'RateMaster.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}

		public function load_data(){
			// print_r($this->input->post());
			$data =$this->rate_master_model->table_data($this->input->post());
			$distributor_id = $this->input->post('distributor_id');
			$state_id = $this->input->post('state_id');
			$data_state_name  = $this->db->get_where('tblxx_statelist',array('short_name'=>$state_id))->row_array();
			$data_distributor_name  = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();

			$html ='';
			foreach($data as $value){
				$html.= '<tr>';
				if (has_permission('ratemaster', '', 'edit')) {
					$item = '<a href="#" data-toggle="modal" data-target="#rate_master_modal" data-id="' . $value['rate_id'] . '">' . $value['item_code'] . '</a>';
					}else {
					$item = $value['item_code'];
				}
				$html.= '<td>'.$item.'</td>';
				if (has_permission('ratemaster', '', 'edit')) {

					$desc = '<a href="#" data-toggle="modal" data-target="#rate_master_modal" data-id="' . $value['rate_id'] . '">' . $value['description'] . '</a>';

					}else {
					$desc = $value['description'];
				}
				$html.= '<td>'.$desc.'</td>';
				$html.= '<td>'.$value['unit'].'</td>';
				if($value['state_id_2'] == !"" && $value['state_id_2'] !== "0" && $value['state_id_2'] == $state_id && $value['distributor_id_2'] == !"" && $value['distributor_id_2'] !== "0" && $value['distributor_id_2'] == $distributor_id ){
                    $new_rate = $value['assigned_2'];
					} else {
                    $new_rate = 0;
				}
                if (has_permission('ratemaster', '', 'edit')) {
					$rate = '<a href="#" data-toggle="modal" data-target="#rate_master_modal" data-id="' . $value['rate_id'] . '">' . app_format_money($new_rate, get_base_currency()) . '</a>';
					}else{
                    $rate = app_format_money($new_rate, get_base_currency());
				}
				$html.= '<td>'.$rate.'</td>';
                $aRow['taxrate'] = $value['taxrate'] ?? 0;
				$tax_field             = '<span data-toggle="tooltip" title="' . $value['taxname_1'] . '" data-taxid="' . $value['tax_id_1'] . '">' . app_format_number($aRow['taxrate']) . '%' . '</span>';
				$html.= '<td>'.$tax_field.'</td>';

                $p = $value['taxrate'] /100;
                $Y = $p * $new_rate;
                $sale_rate = $new_rate + $Y;
                $sale_field = number_format($sale_rate, 2);

				$html.= '<td>'.$sale_field.'</td>';
				$html.= '</tr>';
			}
			// echo $html;
			$data_array =array('html'=>$html,'state'=>$data_state_name,'distributor'=>$data_distributor_name);
			echo json_encode($data_array);
		}

		public function addDeductionMatrix(){
			$itemArray = $this->input->post("itemArray");
			for($i = 0; $i < sizeof($itemArray); $i++){
				$dataToInsert = array(
                'ItemID' => $this->input->post("commodity"),
                'ItemParameterID' => $this->input->post("QCparameterName"),
                'Value' => $itemArray[$i]['value'],
                'Deduction' => $itemArray[$i]['deduction'],
                'UserID' => $this->session->userdata('username'),
                'TransDate' => date('Y-m-d H:i:s'),
				);
				$this->rate_master_model->addDeductionMatrix($dataToInsert);
			}
			echo json_encode(true);
		}

		public function updateDeductionMatrix(){

			//Delete previous entries
			$this->rate_master_model->deleteDeductionMatrixEntry($this->input->post("commodity"),$this->input->post("QCparameterName"));

			$itemArray = $this->input->post("itemArray");
			for($i = 0; $i < sizeof($itemArray); $i++){
				$dataToInsert = array(
                'ItemID' => $this->input->post("commodity"),
                'ItemParameterID' => $this->input->post("QCparameterName"),
                'Value' => $itemArray[$i]['value'],
                'Deduction' => $itemArray[$i]['deduction'],
                'UserID' => $this->session->userdata('username'),
                'TransDate' => date('Y-m-d H:i:s'),
				);
				$this->rate_master_model->addDeductionMatrix($dataToInsert);
			}
			echo json_encode(true);
		}

		public function ImportStatement()
		{
			if (!has_permission_new('ImportStatement', '', 'create')) {
				access_denied('Items Import');
			}

			$this->load->library('import/import_rate_master', [], 'import');
			$mm = array('post_date','value_date','branch_code','cheque_number','account_description','debit','credit','balance');

			// $this->import->setDatabaseFieldsStatementWise($mm)->setCustomFields(get_custom_fields('rate_master'));


			// if ($this->input->post('download_statement_sample') === 'true') {
			// 	$this->import->downloadSampleStatement();
			// }

			if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
				// echo "<pre>";print_r($this->input->post());die;
                $selected_company = $this->session->userdata('root_company');
                $cuurent_user = $this->session->userdata('username');
                $BankAccount = $this->input->post('BankAccount');

				$this->import->setSimulation($this->input->post('simulate'))
				->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
				->setFilename($_FILES['file_csv']['name'])
				->performStatement($BankAccount,$selected_company,$cuurent_user);

				$data['total_rows_post'] = $this->import->totalRows();

				if (!$this->import->isSimulation()) {
					set_alert('success', _l('import_total_imported', $this->import->totalImported()));
					redirect(admin_url('rate_master/ImportStatement'));
				}
			}
			$BankAccount = "CBI";
			// $Accounts = $this->rate_master_model->GetLedgerAccountList();
			$Accounts = $this->rate_master_model->GetLedgerAccountListAll();
			$data['Accounts'] = $Accounts;
			// $result = $this->rate_master_model->GetPendingStatement($BankAccount);
			// $PendingPaymentVoucher = $this->rate_master_model->GetPendingPaymentVoucher();
			// $data['PendingPaymentVoucher'] = $PendingPaymentVoucher;
			// $voucherMap = [];
              //       foreach ($PendingPaymentVoucher as $v) {
              //           $voucherMap[$v["ref_no"]] = $v["AccountID"];
              //       }

              //       foreach ($result as &$val) {
              //           $val["LedgerAccountID"] = $voucherMap[$val["chq_ref_no"]] ?? null;
              //       }
              //       unset($val);
			$data['result'] = [];
			// $data['result'] = $result;
			$data['FY'] = $this->session->userdata('finacial_year');
			$data['BankAccount'] = $this->rate_master_model->GetBankAccounts();
			$data['title'] = _l('import');
			$data['center'] = $this->rate_master_model->getCenter();
			$this->load->view('admin/rate_master/ImportStatement', $data);
			/*echo "<pre>";
			print_r($result);
			die;*/
		}

		public function GetPendingStatement()
		{
			$BankAccount = $this->input->post('BankAccount');
			// $Accounts = $this->rate_master_model->get_data_ganeral_account_to_select();
			$result = $this->rate_master_model->GetPendingStatement($BankAccount);
			$PendingPaymentVoucher = $this->rate_master_model->GetPendingPaymentVoucher();

			$voucherMap = [];
			foreach ($PendingPaymentVoucher as $v) {
					$voucherMap[$v["ref_no"]] = $v["AccountID"];
			}

			foreach ($result as &$val) {
				$val["LedgerAccountID"] = $voucherMap[$val["chq_ref_no"]] ?? null;
			}
			unset($val);
			echo json_encode([
				'result' => $result,
				// 'accounts' => $Accounts
			]);

			//echo json_encode($result);
			//echo $html;
		}

		public function DeleteStatementVoucher()
		{
			$Data = $this->input->post();
			$TotalEntry = 0;
			$UpdatedEntry = 0;
			foreach($Data['entries'] as $key => $value){
			    $TotalEntry++;
			    $id = $value['id'];
			    $this->db->where('id', $id);
				$this->db->delete(db_prefix() . 'import_statement');
				if($this->db->affected_rows() > 0){
				    $UpdatedEntry++;
				}
			}
			$msg = "Total Deleted Entry ".$UpdatedEntry." out of ".$TotalEntry;
			$status = false;
			if($UpdatedEntry > 0){
			    $status = true;
			}
			$result = array(
			    "status"=>$status,
			    "msg"=>$msg
			);
			echo json_encode($result);
		}
		public function GenerateStatementVoucher()
		{
			$Data = $this->input->post();
			// echo json_encode($Data); die;

			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');

			$entryids = [];
			$TotalEntry = 0;
			$UpdatedEntry = 0;
			$Return = false;
			$msg2 = "";
			foreach($Data['entries'] as $key => $value){
			    $TotalEntry++;
				// $entryids[] = $value['id'];
				$statementdata = $this->rate_master_model->GetStatementImportedDataByids($value['id']);
				if($Data['EntryType'] == 'Receipt'){
					$receipt_date = $statementdata['transaction_date'];
					$month = substr($receipt_date,5,2);
					$date = substr($statementdata['transaction_date'],0,10);
					$PassedFrom = "RECEIPTS";
					$LastUniqueID = $this->generateNextVoucherIDNew($date, $selected_company, $PassedFrom);
					
					$ledgerAccount = $value['ledgerAccount'];

					// Ledger Entry
					$credit_data = array(
    					"PlantID" =>$selected_company,
    					"Transdate" =>$receipt_date,
    					"TransDate2" =>date('Y-m-d H:i:s'),
    					"VoucherID" =>$LastUniqueID,
    					"AccountID" =>$ledgerAccount,
    					"CenterID" =>$statementdata['centerID'],
    					"CounterAccount" =>$statementdata['AccountID'],
    					"TType" =>"C",
    					"PartyID" =>'KASPL',
    					"ref_no"=>$statementdata['chq_ref_no'],
    					"Amount" =>$statementdata['credit'],
    					"Narration" =>$statementdata['description'],
    					"PassedFrom" =>"RECEIPTS",
    					"reconcile_status" =>"Y",
    					"OrdinalNo" =>1,
    					"UserID" =>$this->session->userdata('username'),
    					"FY" =>$fy,
					);

					$debit_data = array(
    					"PlantID" =>$selected_company,
    					"Transdate" =>$receipt_date,
    					"TransDate2" =>date('Y-m-d H:i:s'),
    					"VoucherID" =>$LastUniqueID,
    					"AccountID" =>$statementdata['AccountID'],
						"CenterID" =>$statementdata['centerID'],
    					"CounterAccount" =>$ledgerAccount,
    					"TType" =>"D",
    					"PartyID" =>'KASPL',
    					"ref_no"=>$statementdata['chq_ref_no'],
    					"Amount" =>$statementdata['credit'],
    					"Narration" =>$statementdata['description'],
    					"PassedFrom" =>"RECEIPTS",
    					"reconcile_status" =>"Y",
    					"OrdinalNo" =>1,
    					"UserID" =>$this->session->userdata('username'),
    					"FY" =>$fy,
					);
					$i++;
					$this->db->insert(db_prefix().'accountledger', $credit_data);
					if($this->db->affected_rows() > 0){
					    $UpdatedEntry++;
						$this->db->insert(db_prefix().'accountledger', $debit_data);
						$this->db->set('Status', 'Y');
						$this->db->where('id', $statementdata['id']);
						$this->db->update(db_prefix() . 'import_statement');
						
					}else{
					    $msg2 = "Receipt vouchers have not been generated for ";
					}
				}else{
					$payment_date = $statementdata['transaction_date'];
					$chq_ref_no = $value['chqRef'];
					$ledgerAccount = $value['ledgerAccount'];

					if(!empty($chq_ref_no)){
						$this->db->set('Transdate', $payment_date);
						$this->db->set('reconcile_status', "Y");
						$this->db->where('ref_no', $chq_ref_no);
						$this->db->where('reconcile_status', "N");
						$this->db->where('FY', $fy);
						$this->db->update(db_prefix() . 'accountledger');
						if($this->db->affected_rows() > 0){
							$UpdatedEntry++;
							$this->db->set('Status', 'Y');
							$this->db->where_in('id', $statementdata['id']);
							$this->db->update(db_prefix() . 'import_statement');
						}else{
							$msg2 = "Payment vouchers corresponding to the cheque numbers were not found for ";
						}
					}else{
						$msg2 = "Cheque numbers were not found";
					}
				}
			}
			$msg = "Total reconciled entries: ".$UpdatedEntry." out of ".$TotalEntry.". ";
			if($msg2 !=""){
			    $notupdated = $TotalEntry - $UpdatedEntry;
			    $msg .= $msg2.$notupdated." entries.";
			}
			$status = false;
			if($UpdatedEntry > 0){
			    $status = true;
			}
			$result = array(
			    "status"=>$status,
			    "msg"=>$msg
			);
			echo json_encode($result);
		}

		public function GeneratePaymentVoucher()
		{
			$Data = $this->input->post();

			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');

			$insertData = [];
			$updateData = [];
			foreach($Data['entries'] as $key => $value){
				$insertData = [];
				$updateData[] = $value['id'];
				$date = substr($value['transDate'],0,10);
				$dateObj = DateTime::createFromFormat('d/m/Y', $value['transDate']);
				$formattedDate = $dateObj->format('Y-m-d H:i:s');

				switch($value['ledgerAccount']){
					case 'CASH':
						$type = 'CONTRA'; // cbi d cash c
						break;
					case 'PHONEPAY':
						$type = 'CONTRA'; // cbi d phonepay c
						break;
					case 'CASHHAD':
						// $type = 'CONTRA'; // cbi d cashhad c    kadhali d tr 
						$type = 'PAYMENTS'; // changes by madhav sir
						break;
					case 'BCOMM':
						$type = 'PAYMENTS'; // d cbi c
						break;
					case 'GSTCOMM':
						$type = 'PAYMENTS';
						break;
					case 'BANKCHR':
						$type = 'PAYMENTS';
						break;
					default:
						continue 2;
				}

				// if(!isset($voucherCache[$type])){
				// 	$date = date('Y-m-d', strtotime($formattedDate));
				// 	$voucherCache[$type] = $this->rate_master_model->nextVoucherID($type, $date);
				// } else {
				// 	$voucherCache[$type]++;
				// }
				// $new_voucher_number = $voucherCache[$type];
				
				$date = date('Y-m-d', strtotime($formattedDate));
				switch($type){
					case 'CONTRA':
					    $key = $date . '_' . $type; // e.g. 2026-07-02_PAYMENTS
						if (!isset($voucherCache[$key])) {
							// First voucher for this date & type
							$new_voucher_number = $this->rate_master_model->generateNextVoucherIDNew($date, $selected_company, $type);
							$voucherCache[$key] = $new_voucher_number;
						} else {
							// Increment the last generated voucher
							$lastVoucher = $voucherCache[$key];
							$prefix = substr($lastVoucher, 0, -3);
							$number = (int) substr($lastVoucher, -3);
							$new_voucher_number = $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
							$voucherCache[$key] = $new_voucher_number;
						}
						
						$insertData[] = array(
							"FY" => $fy,
							"PlantID" => $selected_company,
							"Transdate" => $formattedDate,
							"TransDate2" => $formattedDate,
							"VoucherID" => $new_voucher_number ?? '',
							"AccountID" => $value['ledgerAccount'],
							"CenterID" => $value['centerID'],
							"CounterAccount" => null,
							"TType" => ($value['credit'] !== "0.00" && $value['credit'] !== "0") ? "C" : "D",
							"PartyID" => 'KASPL',
							"ref_no"=> $value['chqRef'],
							"Amount" => $value['amount'],
							"Narration" => $value['description'],
							"PassedFrom" => $type,
							"reconcile_status" => "Y",
							"OrdinalNo" => 1,
							"UserID" => $this->session->userdata('username'),
						);

						$insertData[] = array(
							"FY" => $fy,
							"PlantID" => $selected_company,
							"Transdate" => $formattedDate,
							"TransDate2" => $formattedDate,
							"VoucherID" => $new_voucher_number ?? '',
							"AccountID" => $Data['BankAccount'],
							"CenterID" => $value['centerID'],
							"CounterAccount" => null,
							"TType" => ($value['credit'] !== "0.00" && $value['credit'] !== "0") ? "D" : "C",
							"PartyID" => 'KASPL',
							"ref_no"=> $value['chqRef'],
							"Amount" => $value['amount'],
							"Narration" => $value['description'],
							"PassedFrom" => $type,
							"reconcile_status" => "Y",
							"OrdinalNo" => 1,
							"UserID" => $this->session->userdata('username'),
						);
						break;
					case 'PAYMENTS':
						// $new_voucher_number = $this->rate_master_model->generateNextVoucherIDNew($date, $selected_company, 'PAYMENTS');
						
						$key = $date . '_' . $type; // e.g. 2026-07-02_PAYMENTS
						if (!isset($voucherCache[$key])) {
							// First voucher for this date & type
							$new_voucher_number = $this->rate_master_model->generateNextVoucherIDNew($date, $selected_company, $type);
							$voucherCache[$key] = $new_voucher_number;
						} else {
							// Increment the last generated voucher
							$lastVoucher = $voucherCache[$key];
							$prefix = substr($lastVoucher, 0, -3);
							$number = (int) substr($lastVoucher, -3);
							$new_voucher_number = $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
							$voucherCache[$key] = $new_voucher_number;
						}

						$insertData[] = array(
							"FY" => $fy,
							"PlantID" => $selected_company,
							"Transdate" => $formattedDate,
							"TransDate2" => $formattedDate,
							"VoucherID" => $new_voucher_number ?? '',
							"AccountID" => $Data['BankAccount'],
							"CenterID" => $value['centerID'],
							"CounterAccount" => $value['ledgerAccount'],
							"TType" => "C",
							"PartyID" => 'KASPL',
							"ref_no"=> $value['chqRef'],
							"Amount" => $value['amount'],
							"Narration" => $value['description'],
							"PassedFrom" => $type,
							"reconcile_status" => "Y",
							"OrdinalNo" => 1,
							"UserID" => $this->session->userdata('username'),
						);

						$insertData[] = array(
							"FY" => $fy,
							"PlantID" => $selected_company,
							"Transdate" => $formattedDate,
							"TransDate2" => $formattedDate,
							"VoucherID" => $new_voucher_number ?? '',
							"AccountID" => $value['ledgerAccount'],
							"CenterID" => $value['centerID'],
							"CounterAccount" => $Data['BankAccount'],
							"TType" => "D",
							"PartyID" => 'KASPL',
							"ref_no"=> $value['chqRef'],
							"Amount" => $value['amount'],
							"Narration" => $value['description'],
							"PassedFrom" => $type,
							"reconcile_status" => "Y",
							"OrdinalNo" => 1,
							"UserID" => $this->session->userdata('username'),
						);
						break;
					default:
						continue 2;
				}

				// echo json_encode($insertData); die;
				// batch insert for vouchers
				if(!empty($insertData)){
					$batch = $this->db->insert_batch(db_prefix().'accountledger', $insertData);
					$rowAffected = $this->db->affected_rows();
					if($rowAffected == 0){
						// 
					}
				}
			}
			
			// update statement status
			if(!empty($updateData)){
				$this->db->where_in('id', $updateData);
				$this->db->update(db_prefix().'import_statement', array('Status' => 'Y'));
			}else{
				echo json_encode([
					"status" => false,
					"msg" => "Failed to generate payment vouchers."
				]);
				die;
			}

			echo json_encode([
			    "status" => true,
			    "msg" => "Payment vouchers generated successfully."
			]);
		}
		
		public function increment_next_contra_number()
    {
        // Update next Contra number in settings
        $FY = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        if($selected_company == 1){
            $this->db->where('name', 'next_contra_number_for_kirti');
        }
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }

		private function updateRateForCenters($Commodity, $CenterID_array, $new_rate, $rateType = 'T')
		{
			if (empty($CenterID_array) || $new_rate === '' || !is_numeric($new_rate) || floatval($new_rate) < 0) {
				return false;
			}

			$user_id = $this->session->userdata('username');
			$KeyID = 'C01';
			$curr_date = date('Y-m-d H:i:s');
			$isFarmer = ($rateType === 'F');

			$this->db->where('ItemID', $Commodity);
			$this->db->where_in('CenterID', $CenterID_array);
			$this->db->where('KeyID', $KeyID);
			$this->db->where('Type', $rateType);
			if (!$this->db->update(db_prefix() . 'RateMaster', ['IsActive' => 'N'])) {
				return false;
			}

			$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,tblclients.fcm_token');
			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.basic_rate >', $new_rate);
			$this->db->where('tbllead_master.ItemID', $Commodity);
			$this->db->where_in('tbllead_master.CenterID', $CenterID_array);
			$this->db->where('tbllead_master.TType', "P");
			$this->db->where('tbllead_master.IsApprove', 'NA');
			if ($isFarmer) {
				$this->db->where('tblclients.CustomerType', '1');
			} else {
				$this->db->where('tblclients.CustomerType != "1"');
			}
			$rejectAll = $this->db->get('tbllead_master')->result_array();

			$this->db->where('basic_rate >', $new_rate);
			$this->db->where('ItemID', $Commodity);
			$this->db->where_in('CenterID', $CenterID_array);
			$this->db->where('IsApprove', 'NA');
			if ($this->db->update(db_prefix() . 'lead_master', ['IsApprove' => 'N', 'ApproveTime' => $curr_date, 'ApproveUserID' => $user_id])) {
				$title = "Trade Rejected";
				$screen = "1";
				foreach ($rejectAll as $val) {
					$body = "Your BookingID : " . $val["BookingID"] . ' rejected by Kisan Kirti';
					$this->send_notification($title, $screen, $body, $val["BookingID"], $val["fcm_token"]);
				}
			}

			$this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,tblclients.fcm_token');
			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.basic_rate <=', $new_rate);
			$this->db->where('tbllead_master.ItemID', $Commodity);
			$this->db->where_in('tbllead_master.CenterID', $CenterID_array);
			$this->db->where('tbllead_master.TType', "P");
			$this->db->where('tbllead_master.IsApprove', 'NA');
			if ($isFarmer) {
				$this->db->where('tblclients.CustomerType', '1');
			} else {
				$this->db->where('tblclients.CustomerType != "1"');
			}
			$acceptAll = $this->db->get('tbllead_master')->result_array();

			$this->db->where('basic_rate <=', $new_rate);
			$this->db->where('ItemID', $Commodity);
			$this->db->where_in('CenterID', $CenterID_array);
			$this->db->where('IsApprove', 'NA');
			$this->db->where('TType', "P");
			if (!$isFarmer) {
				$this->db->where('ClientApprove', 'Y');
				$this->db->where('BrokerApprove', 'Y');
			}
			if ($this->db->update(db_prefix() . 'lead_master', ['IsApprove' => 'Y', 'ApproveTime' => $curr_date, 'ApproveUserID' => $user_id])) {
				$title = "Trade Accepted";
				$screen = "1";
				foreach ($acceptAll as $Aval) {
					$body = "Your BookingID : " . $Aval["BookingID"] . ' Accepted by Kisan Kirti';
					$this->send_notification($title, $screen, $body, $Aval["BookingID"], $Aval["fcm_token"]);
				}
			}

			$insert = 0;
			foreach ($CenterID_array as $CenterID) {
				$data_insert = [
					'ItemID' => $Commodity,
					'CenterID' => $CenterID,
					'Type' => $rateType,
					'KeyID' => $KeyID,
					'Rate' => $new_rate,
					'UserID' => $user_id,
					'TransDate' => date('Y-m-d H:i:s'),
					'IsActive' => 'Y'
				];
				if ($this->db->insert(db_prefix() . 'RateMaster', $data_insert)) {
					$insert++;
				}
			}

			return $insert > 0;
		}

	}
// 	contra
// cbi c
// phonepay d

// payment
// cash deposite