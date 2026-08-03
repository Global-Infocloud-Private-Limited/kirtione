<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class VillageModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
        public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}
		
        public function insert_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
		}
		
        public function get_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->row_array();
		}
		
        public function get_all_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
		}
		
        public function edit_data($tbl,$where,$arr) 
		{
			$this->db->where($where);
			if ($this->db->update($tbl, $arr)) {
				return TRUE;
				} else {
				return FALSE;
			}
		}
		
		public function delete_data($tbl, $where) 
		{
			$this->db->where($where);
			if ($this->db->delete($tbl)) {
				return TRUE;
				} else {
				return FALSE;
			}
		}  
		
		public function get_table_on_load_filter($data)
        {
            $from_date = to_sql_date($data['from_date']);
            $to_date = to_sql_date($data['to_date']);   
			
            $this->db->select('tblvillagedetails.id,tblvillagedetails.UserID,tblvillagedetails.AssignStaff,tblvillagedetails.VisitDate,tblvillagedetails.VillageName,
            tblvillagedetails.VillageSarpanch,tblvillagedetails.Pincode,tblxx_statelist.state_name,tblxx_citylist.city_name,
            tblTalukaMaster.TalukaName,tblstaff.firstname,tblstaff.lastname,tblstaff.staffid,assignee.firstname AS assignee_firstname,assignee.lastname AS assignee_lastname,
            tblvillagedetails.VillagePopulation,tblvillagedetails.Area,tblvillagedetails.InfluencerName,tblvillagedetails.InfluencerGovtPost,tblvillagedetails.Influencer_MobNo,tblvillagedetails.NoRtrsFarmers,tblvillagedetails.OtherInformation');                   
            $this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblvillagedetails.StateId');
            $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblvillagedetails.DistrictId');
            $this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblvillagedetails.TalukaId');
            $this->db->join('tblstaff', 'tblstaff.AccountID = tblvillagedetails.UserID');
            $this->db->join('tblstaff AS assignee', 'assignee.AccountID = tblvillagedetails.AssignStaff', 'left');                   
            $this->db->where("DATE(tblvillagedetails.datecreated) BETWEEN '$from_date' AND '$to_date'");
            if ($data['Account_district'] != "") {
                $this->db->where('tblvillagedetails.DistrictId', $data['Account_district']);
			}
			if ($data['Account_taluka'] != "") {
                $this->db->where('tblvillagedetails.TalukaId', $data['Account_taluka']);
			}
            if($data['Staff_Id'] != "")
            {
                $this->db->where('tblvillagedetails.UserID', $data['Staff_Id']);
			}
			if($data['Repr_Staff'] != "")
            {
                $this->db->where('tblvillagedetails.AssignStaff', $data['Repr_Staff']);
			}
            $villages = $this->db->get(db_prefix() . 'villagedetails')->result_array();
            foreach ($villages as &$village) 
            {
                $this->db->select('VillageAggregatorName, AggregatorMobNo');
                $this->db->from('tblvillageaggregatordetails');
                $this->db->where('VillageDetailId', $village['id']);
                $aggregators = $this->db->get()->result_array();
                $village['aggregators'] = $aggregators;
                $village['aggregator_count'] = count($aggregators);
                
                $this->db->select('KskName, KskShopOwnerName,KskShopOwnerNo');
                $this->db->from('tblvillagekskdetails');
                $this->db->where('VillageDetailId', $village['id']);
                $ksk = $this->db->get()->result_array();
                $village['ksk'] = $ksk;
                $village['ksk_count'] = count($ksk);
                
                $this->db->select('tblcrops.CropName,tblfertilizers.fertilizerName,tblseed.SeedName,tblpesticides.PesticideName');
                $this->db->from('tblvillagecropdetails');
                $this->db->join('tblcrops', 'tblcrops.id = tblvillagecropdetails.CropId','left');
                $this->db->join('tblfertilizers', 'tblfertilizers.id = tblvillagecropdetails.FertilizerId','left');
                $this->db->join('tblseed', 'tblseed.id = tblvillagecropdetails.SeedId','left');
                $this->db->join('tblpesticides', 'tblpesticides.id = tblvillagecropdetails.PesticideId','left');
                $this->db->where('VillageDetailId', $village['id']);
                $crop = $this->db->get()->result_array();
                $village['crops'] = $crop;
                $village['crop_count'] = count($crop);
                
                $this->db->select('VehicleType,RegsiterNo,capacity,DriverName,MobileNo,OwnerName,OwnerMobNo');
                $this->db->from('tblvillagevehicledetails');
                $this->db->where('VillageDetailId', $village['id']);
                $vehicles = $this->db->get()->result_array();
                $village['vehicles'] = $vehicles;
                $village['vehicle_count'] = count($vehicles);
                
                $this->db->select('HotelName,OwnerName As Hotownername,OwnerMobileNo AS hotmobileno');
                $this->db->from('tblVillageHotelDetails');
                $this->db->where('VillageDetailId', $village['id']);
                $hotelDetails = $this->db->get()->result_array();
                $village['hoteldetail'] = $hotelDetails;
                $village['hotel_count'] = count($hotelDetails);
            }
            unset($village);
            return $villages;
		}
		
		//================= = =  Village wise chart Report = = ====================== 
		
		public function village_wise_chart($filter_data)
		{
			log_message('debug', 'ReportFor: ' . $filter_data["ReportFor"] . ', Staff_Id: ' . $filter_data["Staff_Id"]);
			// Step 1: Convert to SQL-compatible date format
			$from_date = to_sql_date($filter_data["from_date"])." 00:00:00";
			$to_date = to_sql_date($filter_data["to_date"])." 23:59:59";
			
			// Step 2: Validate dates — return empty if invalid
			if (empty($filter_data["from_date"]) || empty($filter_data["to_date"])) {
				log_message('error', 'village_wise_chart: Empty from_date or to_date');
				return []; // Return empty result to prevent SQL error
			}
			
			// Step 3: Define chart color array
			$color_data = ['#a48a9e', '#c6e1e8', '#648177', '#0d5ac1', '#00FF7F', '#0cffe95c',
			'#80da22', '#f37b15', '#da1818', '#176cea', '#5be4f0', '#57c4d8', '#a4d17a', 
			'#225b8', '#be608b', '#96b00c', '#088baf', '#63b598', '#ce7d78', '#ea9e70', 
			'#d2737d', '#c0a43c', '#f2510e', '#651be6', '#79806e', '#61da5e', '#cd2f00'];
			
			$chart = [];
			
			// Step 4: Build query
			$this->db->select('Count(tblvillagedetails.id) AS TotalCount, tblvillagedetails.id, tblstaff.firstname, tblstaff.lastname, tblstaff.staffid, 
			assignee.firstname AS assignee_firstname, assignee.lastname AS assignee_lastname, tblxx_citylist.city_name, 
			tblTalukaMaster.TalukaName, tblvillagedetails.VillageName');
			$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblvillagedetails.StateId',"LEFT");
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblvillagedetails.DistrictId',"LEFT");
			$this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblvillagedetails.TalukaId',"LEFT");
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblvillagedetails.UserID',"LEFT");
			$this->db->join('tblstaff AS assignee', 'assignee.AccountID = tblvillagedetails.AssignStaff',"LEFT");
			$this->db->where("DATE(tblvillagedetails.datecreated) BETWEEN '$from_date' AND '$to_date'");
			if ($filter_data["District"] != "") {
                $this->db->where('tblvillagedetails.DistrictId', $filter_data["District"]);
			}
			if ($filter_data["Taluka"] != "") {
                $this->db->where('tblvillagedetails.TalukaId', $filter_data["Taluka"]);
			}
			if ($filter_data["ReportFor"] == "2" && !empty($filter_data["Staff_Id"])) {
				$this->db->where('tblvillagedetails.AssignStaff', $filter_data["Staff_Id"]);
			} elseif($filter_data["ReportFor"] == "1" && !empty($filter_data["Staff_Id"])) {
				$this->db->where('tblvillagedetails.UserID', $filter_data["Staff_Id"]);
			}
			// Group By Condition
			if($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "1"){ // staff wise
			    $this->db->group_by("tblvillagedetails.UserID");
			}if($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "2"){ // staff wise
			    $this->db->group_by("tblvillagedetails.AssignStaff");
			}else if($filter_data["GroupBy"] == "2" && empty($filter_data["District"])){ // District wise
			    $this->db->group_by("tblvillagedetails.DistrictId");
			}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["District"]) && empty($filter_data["Taluka"])){ // Taluka wise
			    $this->db->group_by("tblvillagedetails.TalukaId");
			}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["Taluka"])){ // Village wise
			    $this->db->group_by("tblvillagedetails.id");
			}
			
			/*if (!empty($filter_data["Staff_Id"])) {
				if ($filter_data["ReportFor"] == "2") {
					$this->db->where('tblvillagedetails.AssignStaff', $filter_data["Staff_Id"]);
				} elseif($filter_data["ReportFor"] == "1" ) {
					$this->db->where('tblvillagedetails.UserID', $filter_data["Staff_Id"]);
				}
				if (!empty($filter_data["District"]) && empty($filter_data["Taluka"])){
					$this->db->group_by("tblvillagedetails.TalukaId");
				}elseif(!empty($filter_data["Taluka"])){
					$this->db->group_by("tblvillagedetails.id");
				}else{
					$this->db->group_by("tblvillagedetails.DistrictId");
				}
			}else if ($filter_data["ReportFor"] == "2" && empty($filter_data["Staff_Id"])) {
				$this->db->group_by("tblvillagedetails.AssignStaff");
			} else if($filter_data["ReportFor"] == "1" && empty($filter_data["Staff_Id"])) {
				$this->db->group_by("tblvillagedetails.UserID");
			}*/
			
			$this->db->order_by("TotalCount","DESC");
			// Step 5: Execute query and check for errors
			$query = $this->db->get(db_prefix() . 'villagedetails');
			
			if (!$query) {
				log_message('error', 'village_wise_chart: Query failed: ' . $this->db->last_query());
				return [];
			}
			
			$result = $query->result_array();
			log_message('debug', 'Executed SQL: ' . $this->db->last_query());
			$totalCount = 0;
			foreach ($result as $value) {
				$totalCount += isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
			}
			
			$i = 0;
			foreach ($result as $key => $value) {
				// Determine name based on ReportFor
				if($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "1"){ // staff wise
    			    $name = isset($value['firstname']) ? $value['firstname'] . "" . $value['lastname'] : 'Unknown';
    			}if($filter_data["GroupBy"] == "1" && $filter_data["ReportFor"] == "2"){ // staff wise
    			    $name = isset($value['assignee_firstname']) ? $value['assignee_firstname'] . " " . $value['assignee_lastname'] : 'Unknown';
    			}else if($filter_data["GroupBy"] == "2" && empty($filter_data["District"])){ // District wise
    			    $name = isset($value['city_name']) ? $value['city_name'] : 'Unknown';
    			}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["District"]) && empty($filter_data["Taluka"])){ // Taluka wise
    			    $name = isset($value['TalukaName']) ? $value['TalukaName'] : 'Unknown';
    			}else if($filter_data["GroupBy"] == "2" && !empty($filter_data["Taluka"])){ // Village wise
    			    $name = isset($value['VillageName']) ? $value['VillageName'] : 'Unknown';
    			}else{
    			    //$name = 'Unknown last'.$filter_data["GroupBy"]." = ".$filter_data["ReportFor"];
    			}
			
				/*if ($filter_data["ReportFor"] == "1"  && empty($filter_data["Staff_Id"])) {
					$name = isset($value['firstname']) ? $value['firstname'] . "" . $value['lastname'] : 'Unknown';
				} elseif ($filter_data["ReportFor"] == "2"  && empty($filter_data["Staff_Id"])) {
					$name = isset($value['assignee_firstname']) ? $value['assignee_firstname'] . " " . $value['assignee_lastname'] : 'Unknown';
				}else {
					$name = 'Unknown';
				}
				if (!empty($filter_data["Staff_Id"])) {
					if (!empty($filter_data["District"]) && empty($filter_data["Taluka"])) {
						$name = isset($value['TalukaName']) ? $value['TalukaName'] : 'Unknown';
					}elseif(!empty($Taluka)){
						$name = isset($value['VillageName']) ? $value['VillageName'] : 'Unknown';
					}else {
						$name = isset($value['city_name']) ? $value['city_name'] : 'Unknown';
					}
				}*/
				if ($filter_data["ChartType"] !== "Pie") {
					$allcount = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
				} else {
					$count3_raw = isset($value['TotalCount']) ? (int)$value['TotalCount'] : 0;
					$count = ($totalCount > 0) ? round(($count3_raw / $totalCount) * 100, 2) : 0;
					$allcount = $count; // Keep decimal part
				}
				// Now build the chart array
				$chart[] = array(
    				'name'  => $name,
    				'y'     => $allcount,
    				'color' => $color_data[$i % count($color_data)],
    				'z'     => 100,
    				'label' => "Qty"
				);
				$i++;
			}
			$chart_data = [
			    'ChartData' => $chart,
			];
			// Step 7: Return chart data
			return $chart_data;
		}
		
		
		
		
		
		//================= Get Unique Staff List Which is add Pincode Details =========
        public function GetVillageAddedStaffList()
        {   
            $this->db->select('tblvillagedetails.id,tblvillagedetails.UserID,tblstaff.firstname,tblstaff.lastname,tblstaff.staffid'); 
			
            $this->db->join('tblstaff', 'tblstaff.AccountID = tblvillagedetails.UserID');   
            $this->db->group_by('tblvillagedetails.UserID');
            $result = $this->db->get(db_prefix() . 'villagedetails')->result_array();
            return $result;
		}
		//================= Get Unique City List Which is add Pincode Details ==========
        public function GetVillageAddedCityList()
        {   
            $this->db->select('tblvillagedetails.id,tblvillagedetails.DistrictId,tblxx_citylist.city_name');   
            $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblvillagedetails.DistrictId');   
            $this->db->group_by('tblvillagedetails.DistrictId');
            $result = $this->db->get(db_prefix() . 'villagedetails')->result_array();
            return $result;
		} 
		//================= Get Unique Staff List Which is add Pincode Details =========		
		public function GetVillageAddedReprStaffList()
        {   
            $this->db->select('tblvillagedetails.id,tblvillagedetails.AssignStaff,assignee.firstname AS assignee_firstname,assignee.lastname AS assignee_lastname');   
			$this->db->join('tblstaff AS assignee', 'assignee.AccountID = tblvillagedetails.AssignStaff', 'left');
            $this->db->group_by('tblvillagedetails.AssignStaff');
            $result = $this->db->get(db_prefix() . 'villagedetails')->result_array();
            return $result;
		}		
		
		public function get_company_detail()
		{
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
			$result = $this->db->query($sql)->row();
			
			return $result;
		}
		public function getTalukasByDistrict($district_id) 
		{
			$this->db->where('DistrictID', $district_id);
			$query = $this->db->get('tblTalukaMaster'); 
			return $query->result_array();
		}
		public function get_village_by_id($id) {
			$this->db->select('tblvillagedetails.*, assignee.firstname AS assignee_firstname,assignee.lastname AS assignee_lastname,tblxx_statelist.state_name,tblxx_citylist.city_name,
            tblTalukaMaster.TalukaName');
			$this->db->from('tblvillagedetails');
			$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblvillagedetails.StateId');
            $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblvillagedetails.DistrictId');
            $this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblvillagedetails.TalukaId');
			$this->db->join('tblstaff AS assignee', 'assignee.AccountID = tblvillagedetails.AssignStaff', 'left');   
			$this->db->where('tblvillagedetails.id', $id);
			return $this->db->get()->row_array();
		}
		
		public function get_all_staff() {
			$query = $this->db->get('tblstaff');
			return $query->result_array(); // Return as array
		}
		public function assign_staff_to_village($village_id, $staff_id)
		{
			$data = [
			'AssignStaff' => $staff_id
			];
			
			$this->db->where('id', $village_id);
			return $this->db->update('tblvillagedetails', $data);  // Replace 'villages' with your actual table name
		}
		
	}						