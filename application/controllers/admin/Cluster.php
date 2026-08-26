<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cluster extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->Model('Cluster_model');
        $this->load->Model('Clients_model');
        $this->load->Model('sale_reports_model');
     }
    
    public function index(){
        
        if (!has_permission_new('ClusterMaster', '', 'view')) {
            access_denied('access denied');
        }
        $data['title'] = 'Add/Edit Cluster';
        $data['table_data'] = $this->Cluster_model->getClusterData();
        
        $this->load->view('admin/clients/cluster',$data);      
    }
    
    public function GetCity(){
        $state_id = $this->input->post('state_id');
        $data = $this->Cluster_model->getCity($state_id);
        $html = '';
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['city'].'" >'.$value['city'].'</option>'; 
        }
        echo $html;
    }
    
    public function GetCityFromState(){
        $state_id = $this->input->post('state_id');
        $html = '';
        $data = $this->Cluster_model->getCityBYStateCode($state_id);
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'" >'.$value['city_name'].'</option>'; 
        }
        echo $html;
    }
    
    public function GetTalukaFromCity(){
        $CityID = $this->input->post('CityID');
        $html = '';
        $data = $this->clients_model->GetTaluka($CityID);
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'" >'.$value['TalukaName'].'</option>'; 
        }
        echo $html;
    }
    
    public function GetState(){
        $data = $this->Cluster_model->getState();
        $html = '<option value="">Non Selected</option>';
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['short_name'].'" >'.$value['state_name'].'</option>'; 
        }
        echo $html;
    }
    
    public function saveCluster(){
        $states = $this->input->post('states');
        $states_string = '';
        $cities_string = '';
        $centers_string = '';
        // foreach($states as $value){
        //     $states_string .= $value.',';
        // }
        // $cities = $this->input->post('cities');
        // foreach($cities as $value){
        //     $cities_string .= $value.',';
        // }
        $selectedCenters = $this->input->post('center_id');
        foreach($selectedCenters as $value){
            $centers_string .= $value.',';
        }
        $data = array(
            'AccountID' => $this->input->post('AccountID'),
            'cluster' => $this->input->post('cluster'),
            /*'state' => $states_string,
            'state_name' => $this->input->post('state_name'),
            'city' => $cities_string,
            'city_name' => $this->input->post('city_name'),*/
            'center_id' => $centers_string,
            'center_name' => $this->input->post('centerName'),
        );
        $result =  $this->Cluster_model->saveCluster($data);
        echo json_encode($result);
    }
    
    public function Region(){
        if (!has_permission_new('RegionMaster', '', 'view')) {
            access_denied('access denied');
        }
        $data['title'] = 'Add/Edit Region';
        $data['table_data'] = $this->Cluster_model->getRegionData();
        $this->load->view('admin/clients/region',$data);       
    }
    
    public function saveRegion(){
        $states = $this->input->post('states');
        $states_string = '';
        $cities_string = '';
        foreach($states as $value){
            $states_string .= $value.',';
        }
        $cities = $this->input->post('cities');
        foreach($cities as $value){
            $cities_string .= $value.',';
        }
        $data = array(
            'AccountID' => $this->input->post('AccountID'),
            'region' => $this->input->post('region'),
            'state' => $states_string,
            'state_name' => $this->input->post('state_name'),
            'city' => $cities_string,
            'city_name' => $this->input->post('city_name'),
        );
        $result =  $this->Cluster_model->saveRegion($data);
        echo json_encode($result);
    }
    
    public function updateRegion(){
        $states = $this->input->post('states');
        $states_string = '';
        $cities_string = '';
        foreach($states as $value){
            $states_string .= $value.',';
        }
        $cities = $this->input->post('cities');
        foreach($cities as $value){
            $cities_string .= $value.',';
        }
        $data = array(
            'AccountID' => $this->input->post('AccountID'),
            'region' => $this->input->post('region'),
            'state' => $states_string,
            'city' => $cities_string,
        );
        $result =  $this->Cluster_model->updateRegion($data);
        echo json_encode($result);
    }
    
    public function updateCluster(){
        $states = $this->input->post('states');
        $states_string = '';
        $cities_string = '';
        foreach($states as $value){
            $states_string .= $value.',';
        }
        $cities = $this->input->post('cities');
        foreach($cities as $value){
            $cities_string .= $value.',';
        }
        $data = array(
            'AccountID' => $this->input->post('AccountID'),
            'cluster' => $this->input->post('cluster'),
            'state' => $states_string,
            'state_name' => $this->input->post('state_name'),
            'city' => $cities_string,
            'city_name' => $this->input->post('city_name'),
        );
        $result =  $this->Cluster_model->updateCluster($data);
        echo json_encode($result);
    }
    
    public function getSingleRegion(){
        $AccountID = $this->input->post('AccountID');
        $result = $this->Cluster_model->getSingleRegionDb($AccountID);
        echo json_encode($result);
    }
    
    public function getSingleCluster(){
        $AccountID = $this->input->post('AccountID');
        $result = $this->Cluster_model->getSingleClusterDb($AccountID);
        echo json_encode($result);
    }
    
    public function getClusterDetails(){
        $AccountID = $this->input->post('AccountID');
        $result = $this->Cluster_model->getClusterDb($AccountID);
        echo json_encode($result);
    }
    
    public function getRegionDetails(){
        $AccountID = $this->input->post('AccountID');
        $result = $this->Cluster_model->getRegionDb($AccountID);
        echo json_encode($result);
    }
    
    public function Center()
    {
        if (!has_permission_new('CenterMaster', '', 'view')) {
            access_denied('CenterMaster');
        }
        $data['title'] = 'Add/Edit Center';
        $data['table_data'] = $this->Cluster_model->getMandiData();
        $data['competitor'] = $this->Cluster_model->getCompetitor();
        $data['Mandi'] = $this->Cluster_model->getMandi();
        $data['commodity'] = $this->Cluster_model->getCommodity();
        $data['Parties'] = $this->Cluster_model->GetPlant();
        $data['RegionList'] = $this->Cluster_model->GetRegionList();
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        $this->load->view('admin/CenterMaster/AddEditCenter',$data);       
    }
    
    public function getAllCenter(){
        $result = $this->Cluster_model->getAllMandiDb();
        $html = '';
        foreach($result as $key=>$val){
            if($val['CenterType'] == "F"){
                $CenterTypeName = "Factory Location";
            }else if($val['CenterType'] == "W"){
                $CenterTypeName = "Warehouse Location";
            }else if($val['CenterType'] == "M"){
                $CenterTypeName = "Mandi Location";
            }else{
                $CenterTypeName = "";
            }
            if($val['Premises'] == "O"){
                $PremisesName = "Own Premises";
            }else if($val['Premises'] == "S"){
                $PremisesName = "Third Party WSP";
            }else if($val['Premises'] == "KAS"){
                $PremisesName = "Own KASPL Premises";
            }else if($val['Premises'] == "KWPL"){
                $PremisesName = "KisanMitra";
            }else{
                $PremisesName = "";
            }
            if($val['status'] == "Y"){
                $status = "Active";
            }else if($val['status'] == "N"){
                $status = "DeActive";
            }else{
                $status = "";
            }
            $html .= '<tr onclick=fill_data("'.$val['CenterID'].'")>';
            $html .= '<td>'.$val['CenterID'].'</td>';
            $html .= '<td>'.strtoupper($val['CenterName']).'</td>';
            $html .= '<td>'.$val['region'].'</td>';
            $html .= '<td>'.$CenterTypeName.'</td>';
            $html .= '<td>'.$PremisesName.'</td>';
            $html .= '<td>'.$val['state_name'].'</td>';
            $html .= '<td>'.$val['city_name'].'</td>';
            $html .= '<td>'.$status.'</td>';
            $html .= '</tr>';
        }
        echo $html;
    }
    
    /*New Export to Excel*/ 
    public function export_centerMaster()
    {
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
        if($this->input->post()){
            $result['title'] = 'Center Report List';
            $result = $this->Cluster_model->getAllMandiDb();
            $company_detail = $this->sale_reports_model->get_company_detail();
            $writer = new XLSXWriter();

            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 6);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 6);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            $set_col_tk = [];
    		$set_col_tk["Center ID"] =  'Center ID';
    		$set_col_tk["Center Name"] = 'Center Name';
    		$set_col_tk["Center Type"] = 'Center Type';
    		$set_col_tk["Premises"] = 'Premises';
    		$set_col_tk["State"] = 'State';
    		$set_col_tk["City"] = 'City';
    		$set_col_tk["Status"] = 'Status';
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
    		    
        		if($value['CenterType'] == "F"){
                    $CenterTypeName = "Factory Location";
                }else if($value['CenterType'] == "W"){
                    $CenterTypeName = "Warehouse Location";
                }else{
                    $CenterTypeName = "";
                }
                if($value['Premises'] == "O"){
                    $PremisesName = "Own Premises";
                }else if($value['Premises'] == "S"){
                    $PremisesName = "Start Agri";
                }else{
                    $PremisesName = "";
                }
                if($value['status'] == "Y"){
                    $status = "Active";
                }else if($value['status'] == "N"){
                    $status = "DeActive";
                }else{
                    $status = "";
                }
            
    			$list_add = [];
    			$list_add[] = $value["CenterID"];
                $list_add[] = strtoupper($value["CenterName"]);
                $list_add[] = $CenterTypeName;
                $list_add[] = $PremisesName;
                $list_add[] = $value["state_name"];
                $list_add[] = $value["city_name"];
                $list_add[] = $status;
    			$list_add[] = $row_a;
    			
    			$writer->writeSheetRow('Sheet1', $list_add);
    	    }

            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'CenterMaster.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
        }
    }
    
    public function getSingleCenter()
    {
        $CenterID = $this->input->post('CenterID');
        $result = $this->Cluster_model->getSingleMandiDb($CenterID);
        echo json_encode($result);
    }
    
    public function SaveCenter()
	{
		if (!has_permission_new('CenterMaster', '', 'create')) {
			access_denied('CenterMaster');
		}
		$today = date('Y-m-d');
		$commodity = $this->input->post('commodity');
		$commodity_string = implode(",",$commodity);
		
		$competitor = $this->input->post('competitor');
		$competitor = implode(",",$competitor);
		$competitor = $competitor.",C01,C02";
		
		$CommdataSerializedArr = $this->input->post('CommisiondataSerializedArr');
		$CommisionArray = json_decode($CommdataSerializedArr, true);
		$CommitionArraylen = count($CommisionArray);
		
		
		$data = array(
        'CenterID' => $this->input->post('CenterID'),
        'CenterName' => $this->input->post('CenterName'),
        'mac_address' => $this->input->post('mac_address'),
        'trade_condition' => $this->input->post('trade_condition'),
        'commodity' => $commodity_string,
        'CompetitorID' => $competitor,
        'state' => $this->input->post('state'),
        'city' => $this->input->post('city'),
        'taluka' => $this->input->post('taluka'),
        'pincode' => $this->input->post('Pincode'),
        'CenterType' => $this->input->post('CenterType'),
        'regionID' => $this->input->post('regionID'),
        'Premises' => $this->input->post('Premises'),
        'status' => $this->input->post('CenterStatus'),
        'address' => $this->input->post('address'),
        'latitude' => $this->input->post('latitude'),
        'longitude' => $this->input->post('longitude'),
		'MobileNo' => $this->input->post('MobileNo'),
		'GSTNo' => $this->input->post('Gst_no'),
		'Fertikizers' => $this->input->post('Fertikizers'),
		'Insecticides' => $this->input->post('Insecticides'),
		'Seeds' => $this->input->post('Seeds'),
		'Cotton' => $this->input->post('Cotton'),
		'Fertikizers2' => $this->input->post('Fertikizers2'),
		'Insecticides2' => $this->input->post('Insecticides2'),
		'Seeds2' => $this->input->post('Seeds2'),
		'Cotton2' => $this->input->post('Cotton2'),
        'TransDate' => $today,
        'UserID' => $this->session->userdata('username'),
		);
		$NF = array(
        // Kirti Purchase
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "S",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti Sell
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "P",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti Deposite
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "D",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti Withdrow
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "W",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti Anamat
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "A",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti Trade Finance
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "T",
		'Number' => "1",
		'autoload' => "0",
        ),
        
        // Kirti Gate IN
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "GATE",
		'Number' => "1",
		'autoload' => "0",
        ),
        // Kirti ASN 
        array(
		'CenterID' => $this->input->post('CenterID'),
		'TType' => "ASN",
		'Number' => "1",
		'autoload' => "0",
        )
        
		);
		
		$this->Cluster_model->saveNumberFormat($NF);
		$result =  $this->Cluster_model->saveMandi($data);
		if($result == true){
			foreach ($commodity as $Itemval) {
				$InsertItem = array(
                "CenterID" =>$this->input->post('CenterID'),
                "ItemID" =>$Itemval,
                "UserID" =>$this->session->userdata('username'),
                "TransDate" =>date('Y-m-d H:i:s'),
				);
				$this->db->insert(db_prefix() . 'Center_wise_item', $InsertItem);
			}
			foreach ($CommisionArray as $value) {
				$insertComm = array(
                "CenterID" =>$this->input->post('CenterID'),
                "ItemID" =>$value["0"],
                "PartyID" =>$value["1"],
                "CommisionAmt" =>$value["2"],
                "UserID" =>$this->session->userdata('username'),
                "TransDate" =>date('Y-m-d H:i:s'),
				);
				$this->db->insert(db_prefix() . 'CommisionMatrix', $insertComm);
			}
		}
		echo json_encode($result);
	}

    public function updateCenter()
	{
		if (!has_permission_new('CenterMaster', '', 'edit')) {
			access_denied('CenterMaster');
		}
		$today = date('Y-m-d H:m:s');
		$commodity = $this->input->post('commodity');
		$commodity_string = implode(",",$commodity);
		
		$mac_address = $this->input->post('mac_address');
		$trade_condition = $this->input->post('trade_condition');
		$competitor = $this->input->post('competitor');
		$competitor = implode(",",$competitor);
		$competitor = $competitor.",C01,C02";
		
		$MandiID = $this->input->post('MandiID');
		$MandiIDs = implode(",",$MandiID);
		
		$data = array(
        'CenterID' => $this->input->post('CenterID'),
        'CenterName' => $this->input->post('CenterName'),
        'address' => $this->input->post('address'),
        'mac_address' => $mac_address,
        'trade_condition' => $trade_condition,
        'commodity' => $commodity_string,
        'CompetitorID' => $competitor,
        'MandiIDs' => $MandiIDs,
        'state' => $this->input->post('state'),
        'city' => $this->input->post('city'),
        'taluka' => $this->input->post('taluka'),
        'pincode' => $this->input->post('Pincode'),
        'CenterType' => $this->input->post('CenterType'),
        'Premises' => $this->input->post('Premises'),
        'regionID' => $this->input->post('regionID'),
        'latitude' => $this->input->post('latitude'),
        'longitude' => $this->input->post('longitude'),
		'MobileNo' => $this->input->post('MobileNo'),
		'GSTNo' => $this->input->post('Gst_no'),
		'Fertikizers' => $this->input->post('Fertikizers'),
		'Insecticides' => $this->input->post('Insecticides'),
		'Seeds' => $this->input->post('Seeds'),
		'Cotton' => $this->input->post('Cotton'),
		'Fertikizers2' => $this->input->post('Fertikizers2'),
		'Insecticides2' => $this->input->post('Insecticides2'),
		'Seeds2' => $this->input->post('Seeds2'),
		'Cotton2' => $this->input->post('Cotton2'),
        'status' => $this->input->post('CenterStatus'),
        'TransDate' => $today,
        'UserID' => $this->session->userdata('username'),
        'CommisiondataSerializedArr' =>$this->input->post('CommisiondataSerializedArr')
		);
		
		$result =  $this->Cluster_model->updateCenter($data);
		if($result){
			$this->db->where('CenterID', $this->input->post('CenterID'));
			$this->db->delete(db_prefix() . 'Center_wise_item');
			foreach ($commodity as $Itemval) {
				$InsertItem = array(
                "CenterID" =>$this->input->post('CenterID'),
                "ItemID" =>$Itemval,
                "UserID" =>$this->session->userdata('username'),
                "TransDate" =>date('Y-m-d H:i:s'),
				);
				$this->db->insert(db_prefix() . 'Center_wise_item', $InsertItem);
			}
		}
		echo json_encode($result);
	}
    
    public function GetCenterList()
    {
		$data = $this->Cluster_model->getCenterList();
		$html = '<option value="">Non Selected</option>';
		foreach($data as $key=>$value){
			$html .= '<option value="'.$value['CenterID'].'" >'.$value['CenterName'].'</option>'; 
		}
		echo $html;
	}

}