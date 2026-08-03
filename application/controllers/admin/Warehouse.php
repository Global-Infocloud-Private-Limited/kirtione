<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Warehouse extends AdminController
	{
		public function __construct(){
			parent::__construct();
			$this->load->Model('Warehouse_model');
			$this->load->Model('accounts_master_model');
			$this->load->Model('sale_reports_model');
		}
		
		public function index()
		{
			if (!has_permission_new('WarehouseMaster', '', 'view')) {
				access_denied('customers');
			}
			$data['title'] = 'Add/Edit Warehouse';
			$data['table_data'] = $this->Warehouse_model->getWarehouseData();
			$data['managers'] = $this->Warehouse_model->getStaffList();
			$data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$this->load->view('admin/clients/warehouse', $data);
		}
		
		public function getSingleWarehouse(){
			$AccountID = $this->input->post('AccountID');
			$result = $this->Warehouse_model->getSingleWarehouseData($AccountID);
			echo json_encode($result);
		}
	
	/*New Export to Excel*/ 
    public function export_warehouseMaster()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $result = $this->Warehouse_model->getWarehouseData();
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["WHID"] =  'WHID';
            $set_col_tk["WH Name"] = 'WH Name';
            $set_col_tk["Address"] = 'Address';
            $set_col_tk["Center"] = 'Center';
            $set_col_tk["Pincode"] = 'Pincode';
            $set_col_tk["Assignment"] =  'Assignment';
            $set_col_tk["Capacity (MT)"] = 'Capacity (MT)';
            $set_col_tk["Structure"] = 'Structure';
            $set_col_tk["Latitude"] = 'Latitude';
            $set_col_tk["Longitude"] = 'Longitude';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["AccountID"];
                $list_add[] = $value["w_name"];
                $list_add[] = $value["address"];
                $list_add[] = $value["center"];
                $list_add[] = $value["pincode"];
                $list_add[] = $value["type_of_assignment"];
                $list_add[] = $value["w_capacity"];
                $list_add[] = $value["structure"];
                $list_add[] = $value["latitude"];
                $list_add[] = $value["longitude"];
               
    
    
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'warehouseMaster.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }	
		
		
		public function GetAllWarehouse(){
			$result = $this->Warehouse_model->getWarehouseData();
			// echo '<pre>';
			// print_r($result);
			// die;
			$html = '';
			foreach($result as $key=>$value){
				
				$html .= '<tr class="get_AccountID" data-id = "'.$value['AccountID'].'">';
				$html .= '<td>'.$value['AccountID'].'</td>'; 
				$html .= '<td>'.$value['w_name'].'</td>'; 
				$html .= '<td>'. substr($value['address'], 0, 30) .'</td>'; 
				$html .= '<td>'.$value['center'].'</td>'; 
				$html .= '<td>'.$value['pincode'].'</td>'; 
				$html .= '<td>'.$value['type_of_assignment'].'</td>'; 
				$html .= '<td>'.$value['w_capacity'].'</td>'; 
				$html .= '<td>'.$value['structure'].'</td>'; 
				$html .= '<td>'.$value['latitude'].'</td>'; 
				$html .= '<td>'.$value['longitude'].'</td>'; 
				$html .= '</tr>';
			}
			echo $html;
		}
		
		/* add new warehouse*/
		public function SaveWarehouse()
		{
		    if (!has_permission_new('WarehouseMaster', '', 'create')) {
				access_denied('customers');
			}
			$warehouseDetails = array(
                'AccountID' => $this->input->post("AccountID"),
                'w_name' => $this->input->post("w_name"),
                'address' => $this->input->post("address"),
                'center' => $this->input->post("center"),
                'pincode' => $this->input->post("pincode"),
                'latitude' => $this->input->post("latitude"),
                'longitude' => $this->input->post("longitude"),
                'type_of_assignment' => $this->input->post("type_of_assignment"),
                'structure' => $this->input->post("structure"),
                'length' => $this->input->post("length"),
                'breadth' => $this->input->post("breadth"),
                'height' => $this->input->post("height"),
                'diameter' => $this->input->post("diameter"),
                'w_capacity' => $this->input->post("w_capacity"),
                'flooring' => $this->input->post("flooring"),
                'no_of_chambers' => $this->input->post("no_of_chambers"),
                'no_of_floors' => $this->input->post("no_of_floors"),
                'shutter_door' => $this->input->post("shutter_door"),
                'no_of_shutter_door' => $this->input->post("no_of_shutter_door"),
                'no_of_lock' => $this->input->post("no_of_lock"),
                'lock_functional' => $this->input->post("lock_functional"),
                'windows' => $this->input->post("windows"),
                'no_of_window' => $this->input->post("no_of_window"),
                'ventilator' => $this->input->post("ventilator"),
                'no_of_ventilator' => $this->input->post("no_of_ventilator"),
                'wall' => $this->input->post("wall"),
                'roof' => $this->input->post("roof"),
                'leakage' => $this->input->post("leakage"),
                'plinth_height' => $this->input->post("plinth_height"),
                'drainage_channel' => $this->input->post("drainage_channel"),
                'wire_inside' => $this->input->post("electric_wire"),
                'compound_wall' => $this->input->post("compound_wall"),
                'compound_gate' => $this->input->post("compound_gate"),
                'is_w_clean' => $this->input->post("is_w_clean"),
                'cooling_system' => $this->input->post("cooling_system"),
                'insulation' => $this->input->post("insulation"),
                'temprature' => $this->input->post("temprature"),
                'insurance' => $this->input->post("insurance"),
                'insurance_taken_by' => $this->input->post("insurance_taken_by"),
                'insurance_compound' => $this->input->post("insurance_compound"),
                'policy_no' => $this->input->post("policy_no"),
                'assured_sum' => $this->input->post("assured_sum"),
                'validity' => $this->input->post("validity"),
                'security' => $this->input->post("security"),
                'watchman_name' => $this->input->post("watchman_name"),
                'security_type' => $this->input->post("security_type"),
                'weigh_bridge' => $this->input->post("weigh_bridge"),
                'weighbridge_type' => $this->input->post("weighbridge_type"),
                'weighbridge_distance' => $this->input->post("weighbridge_distance"),
                'no_of_weighbridge' => $this->input->post("no_of_weighbridge"),
                'police_station' => $this->input->post("police_station"),
                'fire_station' => $this->input->post("fire_station"),
                'pest_control' => $this->input->post("pest_control"),
                'amenities' => $this->input->post("amenities"),
                'note' => $this->input->post("note"),
                'w_manager' => $this->input->post("w_manager"),
                'remark' => $this->input->post("remark"),
			);
			
			$result = $this->Warehouse_model->SaveWarehouseDetails($warehouseDetails);
			echo json_encode($result);
		}
		
		public function UpdateWarehouse()
		{
		    if (!has_permission_new('WarehouseMaster', '', 'edit')) {
				access_denied('customers');
			}
			$warehouseDetails = array(
                'AccountID' => $this->input->post("AccountID"),
                'w_name' => $this->input->post("w_name"),
                'address' => $this->input->post("address"),
                'center' => $this->input->post("center"),
                'pincode' => $this->input->post("pincode"),
                'latitude' => $this->input->post("latitude"),
                'longitude' => $this->input->post("longitude"),
                'type_of_assignment' => $this->input->post("type_of_assignment"),
                'structure' => $this->input->post("structure"),
                'length' => $this->input->post("length"),
                'breadth' => $this->input->post("breadth"),
                'height' => $this->input->post("height"),
                'diameter' => $this->input->post("diameter"),
                'w_capacity' => $this->input->post("w_capacity"),
                'flooring' => $this->input->post("flooring"),
                'no_of_chambers' => $this->input->post("no_of_chambers"),
                'no_of_floors' => $this->input->post("no_of_floors"),
                'shutter_door' => $this->input->post("shutter_door"),
                'no_of_shutter_door' => $this->input->post("no_of_shutter_door"),
                'no_of_lock' => $this->input->post("no_of_lock"),
                'lock_functional' => $this->input->post("lock_functional"),
                'windows' => $this->input->post("windows"),
                'no_of_window' => $this->input->post("no_of_window"),
                'ventilator' => $this->input->post("ventilator"),
                'no_of_ventilator' => $this->input->post("no_of_ventilator"),
                'wall' => $this->input->post("wall"),
                'roof' => $this->input->post("roof"),
                'leakage' => $this->input->post("leakage"),
                'plinth_height' => $this->input->post("plinth_height"),
                'drainage_channel' => $this->input->post("drainage_channel"),
                'wire_inside' => $this->input->post("electric_wire"),
                'compound_wall' => $this->input->post("compound_wall"),
                'compound_gate' => $this->input->post("compound_gate"),
                'is_w_clean' => $this->input->post("is_w_clean"),
                'cooling_system' => $this->input->post("cooling_system"),
                'insulation' => $this->input->post("insulation"),
                'temprature' => $this->input->post("temprature"),
                'insurance' => $this->input->post("insurance"),
                'insurance_taken_by' => $this->input->post("insurance_taken_by"),
                'insurance_compound' => $this->input->post("insurance_compound"),
                'policy_no' => $this->input->post("policy_no"),
                'assured_sum' => $this->input->post("assured_sum"),
                'validity' => $this->input->post("validity"),
                'security' => $this->input->post("security"),
                'watchman_name' => $this->input->post("watchman_name"),
                'security_type' => $this->input->post("security_type"),
                'weigh_bridge' => $this->input->post("weigh_bridge"),
                'weighbridge_type' => $this->input->post("weighbridge_type"),
                'weighbridge_distance' => $this->input->post("weighbridge_distance"),
                'no_of_weighbridge' => $this->input->post("no_of_weighbridge"),
                'police_station' => $this->input->post("police_station"),
                'fire_station' => $this->input->post("fire_station"),
                'pest_control' => $this->input->post("pest_control"),
                'amenities' => $this->input->post("amenities"),
                'note' => $this->input->post("note"),
                'w_manager' => $this->input->post("w_manager"),
                'remark' => $this->input->post("remark"),
			);
			$result = $this->Warehouse_model->UpdateWarehouseDetails($warehouseDetails);
			echo json_encode($result);
		}
		
		public function Reports(){
		    if (!has_permission_new('WarehouseReports', '', 'view')) {
            access_denied('WarehouseReports');
        }
			$data['title'] = 'Warehouse Reports';
			$this->load->view('admin/clients/warehouse_reports', $data);
		}
		
		public function GetCityFromState(){
			$state_id = $this->input->post('state_id');
			$html = '<option value="">Non Selected</option>';
			$data = $this->Warehouse_model->getCity($state_id);
			foreach($data as $key=>$value){
				$html .= '<option value="'.$value['id'].'" >'.$value['city'].'</option>'; 
			}
			echo $html;
		}
		
		public function GetTalukaFromCity(){
			$city_id = $this ->input->post('city_id');
			$html = '<option value="">Non Selected</option>';
			$data = $this->Warehouse_model->getTalukaFromCity($city_id);
			foreach($data as $key=>$value){
				$html .= '<option value="'.$value['id'].'" >'.$value['TalukaName'].'</option>'; 
			}
			echo $html;
		}
		
		public function GetCenter(){
			$data = $this->Warehouse_model->getCenter();
			$html = '<option value="">Non Selected</option>';
			foreach($data as $key=>$value){
				$html .= '<option value="'.$value['CenterID'].'" >'.$value['CenterName'].'</option>'; 
			}
			echo $html;
		}
		
		public function load_filter_data_warehouse(){
			$data = array(
            'city' => $this->input->post("city"),
            'structure_type' => $this->input->post("structure_type")
			);
			
			$result = $this->Warehouse_model->filter_table_data($data);
			$html = '';
			foreach($result as $key=>$value){
				
				$html .= '<tr>';
				$html .= '<td>'.$value['AccountID'].'</td>'; 
				$html .= '<td>'.$value['w_name'].'</td>'; 
				$html .= '<td>'.$value['address'].'</td>'; 
				$html .= '<td>'.$value['state_name'].'</td>'; 
				$html .= '<td>'.$value['city'].'</td>'; 
				$html .= '<td>'.$value['taluka'].'</td>'; 
				$html .= '<td>'.$value['w_capacity'].'</td>'; 
				$html .= '<td>'.$value['structure'].'</td>'; 
				$html .= '</tr>';
			}
			echo json_encode($html);
		}
		
		public function BookWarehouse()
		{
			if (!has_permission_new('Deposit_Booking', '', 'view')) {
				access_denied('WH Booking');
			}
			$data['title'] = 'Book Warehouse';
			$data['warehouses'] = $this->Warehouse_model->getWarehouseData();
			$data['items'] = $this->Warehouse_model->getItemsData();
			$data['customers'] = $this->Warehouse_model->getCustomersData();
			$data['payment_cycle'] = $this->Warehouse_model->getPaymentCycle();
			$data['locking_period'] = $this->Warehouse_model->getLockingPeriod();
            $data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$this->load->view('admin/clients/bookWarehouse', $data);
		}
		
		
        
		public function GetWarehouseBooking()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'WHID'  => $this->input->post('w_id'),
			'CustomerType'  => $this->input->post('CustomerType'),
			'ItemID'  => $this->input->post('ItemID'),
			'IsApprove'  => $this->input->post('IsApprove'),
			);
			
			$WarehouseList = $this->Warehouse_model->GetWarehouseBookingDb($data);
			
			$html ='';
			$sr = 1;
			foreach($WarehouseList as $value){
				
				/*if(($value['IsApprove'] == 'N') && ($value['ClientApprove'] == 'Y')){
					$status = 'Rejected';
				}
				if(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y')){
					$status = 'Accepted';
				}
				if(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'N')){
					$status = 'Awaiting Client Approval';
				}
				if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'N')){
					$status = 'Awaiting Client Approval';
				}
				if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y')){
					$status = '--';
				}*/
				$status = '';
                if($value['IsApprove'] == 'Y'){
                    $status = 'Accepted';
                }elseif($value['IsApprove'] == 'N'){
                    $status = 'Rejected';
                }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                    $status = 'Awaiting Client Approval';
                }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                    $status = 'Awaiting Broker Approval';
                }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                    $status = '--';
                }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                    $status = '--';
                }
            
				if($value['CustomerType'] == '1'){
					$CustomerType = 'Farmer';
				}
				if($value['CustomerType'] == '2'){
					$CustomerType = 'Broker';
				}
				if($value['CustomerType'] == '3'){
					$CustomerType = 'Trader';
				}
				if($value['CustomerType'] == '4'){
					$CustomerType = 'Corporate/Processor';
				}
				if($value["company"] == "" || $value["company"] == null){
					$AccountName = $value["firstname"]." ".$value["lastname"];
					}else{
					$AccountName = $value["company"];
				}
				$html.= '<tr class="GetDetails" data-id="'.$value["id"].'" >';
				$html.= '<td style="text-align:left;">'.$sr.'</td>';
				$html.= '<td style="text-align:left;">'.$value["BookingID"].'</td>';
				$html.= '<td style="text-align:left;">'._d(substr($value['TransDate'],0,10)).'</td>';
				$html.= '<td style="text-align:left;">'.$CustomerType.'</td>';
				$html.= '<td style="text-align:left;">'.$value["AccountID"].'</td>';
				$html.= '<td style="text-align:left;">'.$AccountName.'</td>';
				$html.= '<td style="text-align:left;">'.$value["WHID"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["w_name"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["ItemID"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["ItemName"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["quantity"].'</td>';
				$html.= '<td style="text-align:left;">'.$value["unit"].'</td>';
				$html.= '<td style="text-align:left;">'.$status.'</td>';
				/*if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y')){
                    $html.= '<td style="text-align:left;width:8%;">
                    <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                    <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                }
                elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'N')){
                    $html.= '<td style="text-align:left;width:8%;">
                    <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                    <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
                }
                elseif(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y')){
                    $html.= '<td style="text-align:left;width:8%;">
                    <button title="Accept" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-check"></i></button>
                    <button title="Reject" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-times"></i></button>
                    <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
                }
                elseif(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'N')){
                    $html.= '<td style="text-align:left;width:8%;">
                    <button title="Accept" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-check"></i></button>
                    <button title="Reject" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-times"></i></button>
                    <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
                }*/
                if($value['IsApprove'] == 'Y'){
                
                $html.= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
                
            }elseif($value['IsApprove'] == 'N'){
                
                $html.= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
            
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }
				$html.= '</tr>';
				$sr++;
			}
			
			echo $html;
		}
		
		public function WarehouseSize()
		{
			if (!has_permission_new('WHspacemgmt', '', 'view')) {
				access_denied('customers');
			}
			$data['title'] = 'Add/Edit Chamber';
			$data['table_data'] = $this->Warehouse_model->getWarehouseSizeData();
			$data['managers'] = $this->Warehouse_model->getStaffList();
			$data["warehouses"] = $this->Warehouse_model->getWarehouseData();
            $data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$this->load->view('admin/warehouse/warehousesize', $data);
		}
		
	public function getWarehouseSizeData()
	{
		$result = $this->Warehouse_model->getWarehouseSizeData();
		$html = '';
		foreach($result as $key=>$value){
			
			$html .= '<tr class="get_AccountID" data-id = "'.$value['CHID'].'">';
			$html .= '<td>'.$value['CHID'].'</td>'; 
			$html .= '<td>'.$value['ChaumberName'].'</td>'; 
			$html .= '<td>'.$value['WHID'].'</td>';
			$html .= '<td>'.$value['w_name'].'</td>';
			$html .= '<td>'.$value['length'].'</td>'; 
			$html .= '<td>'.$value['width'].'</td>'; 
			$html .= '<td>'.$value['height'].'</td>'; 
			$html .= '<td>'.$value['margin'].'</td>'; 
			$html .= '<td>'.$value['total_area'].'</td>'; 
			$html .= '<td>'.$value['utilize_area'].'</td>'; 
			$html .= '<td>'.$value['volume'].'</td>'; 
			$html .= '<td>'.$value['capacity'].'</td>'; 
			$html .= '</tr>';
		}
		echo $html;
	}
		
		public function getSingleWarehouseSize(){
			$AccountID = $this->input->post('AccountID');
			$result = $this->Warehouse_model->getSingleWarehouseSizeData($AccountID);
			echo json_encode($result);
		}
		
		
		public function UpdateWarehouseSize()
		{
			$warehouseSizeDetails = array(
            'WHID' => strtoupper($this->input->post("warehouse")),
            'CHID' => strtoupper($this->input->post("chid")),
            'ChaumberName' => strtoupper($this->input->post("chambername")),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("Width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("area"),
            'utilize_area' => $this->input->post("utilizearea"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
            'UserID2' => get_staff_user_id(),
            'Lupdate' => date('Y-m-d H:i:s'),
			);
			$result = $this->Warehouse_model->UpdateWarehouseSizeDetails($warehouseSizeDetails);
			echo json_encode($result);
		}
		
		/* add new warehouse*/
		public function SaveWarehouseSize()
		{
			$warehouseSizeDetails = array(
            'WHID' => strtoupper($this->input->post("warehouse")),
            'CHID' => strtoupper($this->input->post("chid")),
            'ChaumberName' => strtoupper($this->input->post("chambername")),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("Width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("area"),
            'utilize_area' => $this->input->post("utilizearea"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
			'UserID' => get_staff_user_id(),
            'TransDate' => date('Y-m-d H:i:s'),
			);
			
			$result = $this->Warehouse_model->SaveWarehouseSizeDetails($warehouseSizeDetails);
			echo json_encode($result);
		}
		
		//===================== ADD EDIT STACK PLAN ===================================   
		
		public function AddEditStackPlan()
		{
			if (!has_permission_new('WHStackMgmt', '', 'view')) {
				access_denied('Invoice Items');
			}
			
			$data['title'] = "Add/Edit Stack Plan";
			$data['StackList'] = $this->Warehouse_model->GetAllStack();
			$data['WHList'] = $this->Warehouse_model->GetAllWH();
            $data['company_detail'] = $this->sale_reports_model->get_company_detail();
			$data["warehouses"] = $this->Warehouse_model->getWarehouseData();
			/*echo "<pre>";
				print_r($data['WHList']);
			die;*/
			//$data['company_detail'] = $this->accounts_master_model->get_company_detail();
			$this->load->view('admin/warehouse/AddEditStack', $data);
		}
        // public function warehousename()
		// {
		// 	$accountID = $this->input->post('id');
		// 	$account_data = $this->Warehouse_model->getWarehousename->($accountID);
		// 	echo json_encode($account_data);
		// }
		public function get_account_group_details()
		{
			$accountID = $this->input->post('act_id');
			$account_data = $this->accounts_master_model->get_account_group_details($accountID);
			echo json_encode($account_data);
		}

		public function GetStackPlanList()
		{
			$StackList = $this->Warehouse_model->GetAllStack();
			$html ='';
			foreach ($StackList as $key => $value) 
			{
				$html .= '<tr class="get_AccountID" data-id = "'.$value['StackID'].'">';
				$html .= '<td>'.$value["StackID"].'</td>';
				$html .= '<td>'.$value["StackName"].'</td>';
				$html .= '<td>'.$value["CHID"].'</td>';
				$html .= '<td>'.$value["ChaumberName"].'</td>';
				$html .= '<td>'.$value["WHID"].'</td>';
				$html .= '<td>'.$value["w_name"].'</td>';
				$html .= '<td>'.$value["length"].'</td>';
				$html .= '<td>'.$value["width"].'</td>';
				$html .= '<td>'.$value["height"].'</td>';
				$html .= '<td>'.$value["margin"].'</td>';
				$html .= '<td>'.$value["total_area"].'</td>';
				$html .= '<td>'.$value["capacity"].'</td>';
				
				$html .= '</tr>';
			}
			echo $html;
		}
		
		public function fetchChambers(){
		    $whid = $this->input->post('whid');
		    $result = $this->Warehouse_model->fetchAllWarehouseChambers($whid);
		    $html = '<option value=""></option>';
		    foreach($result as $r){
		        $html .= '<option value="' . $r['CHID'] . '">' . $r['ChaumberName'] . '</option>';
		    }
		    echo json_encode($html);
		}
        
		
		public function fetchChambersDetails(){
		    $chid = $this->input->post('chid');
		    $result = $this->Warehouse_model->fetchChamberDetails($chid);
		    echo json_encode($result);
		}
        // Get Stack list by chamber ID
        public function fetchstackListByChamberID(){
		    $chid = $this->input->post('chid');
		    $result = $this->Warehouse_model->fetchstackListByChamberID($chid);
            $html = '<option value=""></option>';
		    foreach($result as $r){
		        $html .= '<option value="' . $r['StackID'] . '">' . $r['StackName'] . '</option>';
		    }
		    echo json_encode($html);
		}
		
		public function GetSingleStackPlan()
		{
			$StackID = $this->input->post('StackID');
			$result = $this->Warehouse_model->GetSingleStackPlan($StackID);
			echo json_encode($result);
		}
		
		public function SaveWHStackPlan()
		{
			$WHStackPlanDetails = array(
            'StackID' => strtoupper($this->input->post("StackID")),
            'StackName' => $this->input->post("StackName"),
            'WHID' => $this->input->post("WHName"),
            'CHID' => $this->input->post("CHID"),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("total_area"),
            'utilize_area' => $this->input->post("utilize_area"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
			'UserID' => get_staff_user_id(),
            'TransDate' => date('Y-m-d H:i:s'),
			);
			
			$result = $this->Warehouse_model->SaveWarehouseSizeStackDetails($WHStackPlanDetails);
			echo json_encode($result);
		}
		
		public function GetWarehouseStackSpace()
		{
			$whid = $this->input->post('wh');
			$result = $this->Warehouse_model->GetWarehouseStackSpace($whid);
			echo json_encode($result);
		}
		
		public function UpdateWHStackPlan()
		{
		    if (!has_permission_new('WHStackMgmt', '', 'edit')) {
				access_denied('Wh Stack Mgmt access denied..');
			}
			$WHStackPlanDetails = array(
            'StackID' => strtoupper($this->input->post("StackID")),
            'StackName' => $this->input->post("StackName"),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("total_area"),
            'utilize_area' => $this->input->post("utilize_area"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
			'UserID2' => get_staff_user_id(),
            'Lupdate' => date('Y-m-d H:i:s'),
			);
			
			$result = $this->Warehouse_model->UpdateWarehouseSizeStackDetails($WHStackPlanDetails);
			echo json_encode($result);
		}
		
		
		// ====================== Add Edit Lot =====================
		
		public function AddEditLotPlan()
		{
			if (!has_permission_new('WHLotMgmt', '', 'view')) {
				access_denied('Invoice Items');
			}
			$data['title'] = "Add/Edit Lot Plan";
			$data['WHList'] = $this->Warehouse_model->GetAllWH();
			$data["warehouses"] = $this->Warehouse_model->getWarehouseData();
			$this->load->view('admin/warehouse/AddEditLot', $data);
		}
		
		public function fetchAllStackDetails(){
		    $chid = $this->input->post('chid');
		    $result = $this->Warehouse_model->fetchAllStackDetails($chid);
		    $html = '<option value=""></option>';
		    foreach($result as $r){
		        $html .= '<option value="' . $r['StackID'] . '">' . $r['StackName'] . '</option>';
		    }
		    echo json_encode($html);
		}
        public function fetchAllStackDetails1(){
		    $chid = $this->input->post('chid');
		    $result = $this->Warehouse_model->fetchAllStackDetails1($chid);
		    echo json_encode($result);
		}
		
		public function fetchStackDetails(){
		    $stckID = $this->input->post('stckid');
		    $result = $this->Warehouse_model->fetchStackDetails($stckID);
		    echo json_encode($result);
		}
		
		
		public function GetWarehouseStackList()
		{
			$whid = $this->input->post('wh');
			$result = $this->Warehouse_model->GetWarehouseStackList($whid);
			echo json_encode($result);
		}
		
		public function GetStackLotSpace()
		{
			$StackID = $this->input->post('StackID');
			$result = $this->Warehouse_model->GetStackLotSpace($StackID);
			echo json_encode($result);
		}
		
		public function GetSingleLotPlan()
		{
			$LotID = $this->input->post('LotID');
			$result = $this->Warehouse_model->GetSingleLotPlan($LotID);
			echo json_encode($result);
		}
		
		public function GetLotPlanList()
		{
			$StackList = $this->Warehouse_model->GetAllLot();
			$html ='';
			foreach ($StackList as $key => $value) 
			{
				$html .= '<tr class="get_AccountID" data-id = "'.$value['LOTID'].'">';
				$html .= '<td>'.$value["LOTID"].'</td>';
				$html .= '<td>'.$value["LotName"].'</td>';
				$html .= '<td>'.$value["StackID"].'</td>';
				$html .= '<td>'.$value["StackName"].'</td>';
				$html .= '<td>'.$value["CHID"].'</td>';
				$html .= '<td>'.$value["ChaumberName"].'</td>';
				$html .= '<td>'.$value["WHID"].'</td>';
				$html .= '<td>'.$value["w_name"].'</td>';
				$html .= '<td>'.$value["length"].'</td>';
				$html .= '<td>'.$value["width"].'</td>';
				$html .= '<td>'.$value["height"].'</td>';
				$html .= '<td>'.$value["margin"].'</td>';
				$html .= '<td>'.$value["total_area"].'</td>';
				$html .= '<td>'.$value["capacity"].'</td>';
				
				$html .= '</tr>';
			}
			echo $html;
		}
		
		public function UpdateStackLotPlan()
		{
			$LotPlanDetails = array(
            'LOTID' => strtoupper($this->input->post("LotID")),
            'LotName' => $this->input->post("LotName"),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("total_area"),
            'utilize_area' => $this->input->post("utilize_area"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
			'UserID2' => get_staff_user_id(),
            'Lupdate' => date('Y-m-d H:i:s'),
			);
			
			$result = $this->Warehouse_model->UpdateStackLotPlan($LotPlanDetails);
			echo json_encode($result);
		}
		
		public function SaveStackLotPlan()
		{
			$LotPlanDetails = array(
            'LOTID' => strtoupper($this->input->post("LotID")),
            'LotName' => $this->input->post("LotName"),
            'WHID' => $this->input->post("warehouse"),
            'CHID' => $this->input->post("chamber"),
            'StackID' => $this->input->post("stack"),
            'length' => $this->input->post("length"),
            'width' => $this->input->post("width"),
            'height' => $this->input->post("height"),
            'margin' => $this->input->post("margin"),
            'total_area' => $this->input->post("total_area"),
            'utilize_area' => $this->input->post("utilize_area"),
            'volume' => $this->input->post("volume"),
            'capacity' => $this->input->post("capacity"),
			'UserID' => get_staff_user_id(),
            'TransDate' => date('Y-m-d H:i:s'),
			);
			
			$result = $this->Warehouse_model->SaveLotDetails($LotPlanDetails);
			echo json_encode($result);
		}

        public function export_dailydeposittrader()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $data = array(
                'from_date' => $this->input->post('from_date'),
                'to_date'  => $this->input->post('to_date'),
                'WHID'  => $this->input->post('w_id'),
                'CustomerType'  => $this->input->post('CustomerType'),
                'ItemID'  => $this->input->post('ItemID'),
                'IsApprove'  => $this->input->post('IsApprove'),
                );
            $result = $this->Warehouse_model->GetWarehouseBookingDb($data);
      
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 13);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 13);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["BookingID"] =  'BookingID';
            $set_col_tk["Booking Date"] = 'BookingID';
            $set_col_tk["Party Type"] = 'Party Type';
            $set_col_tk["AccountID"] = 'AccountID';
            $set_col_tk["Party Name"] = 'Party Name';
            $set_col_tk["WHID"] =  'WHID';
            $set_col_tk["WH Name"] = 'WH Name';
            $set_col_tk["ItemID"] = 'ItemID';
            $set_col_tk["Item Name"] = 'Item Name';
            $set_col_tk["Quantity"] = 'Quantity';
            $set_col_tk["Unit"] = 'Unit';
            $set_col_tk["Status"] = 'Status';
            $set_col_tk["Action"] = 'Action';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["BookingID"];
                $list_add[] = _d(substr($value['TransDate'],0,10));
                $list_add[] = $value["firstname"]." ".$value["lastname"];
                $list_add[] = $value["AccountID"];
                $list_add[] = $value["firstname"]." ".$value["lastname"];
                $list_add[] = $value["WHID"];
                $list_add[] = $value["w_name"];
                $list_add[] = $value["ItemID"];
                $list_add[] = $value["ItemName"];
                $list_add[] = $value["quantity"];
                $list_add[] = $value["unit"];
                $list_add[] = $value["IsApprove"];
                $list_add[] = $value["ClientApprove"];
    
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'WarehouseMaster.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }	

    public function export_Chamberlist  ()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $result = $this->Warehouse_model->getWarehouseSizeData();
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["CHID"] =  'CHID';
            $set_col_tk["Chamber Name"] = 'Chamber Name';
            $set_col_tk["WHID"] = 'WHID';
            $set_col_tk["Warehouse Name"] = 'Warehouse Name';
            $set_col_tk["Length"] = 'Length';
            $set_col_tk["Width"] =  'Width';
            $set_col_tk["Height"] = 'Height';
            $set_col_tk["Margin"] = 'Margin';
            $set_col_tk["Total Area"] = 'Total Area';
            $set_col_tk["Utilize Area"] = 'Utilize Area';
            $set_col_tk["Volume"] = 'Volume';
            $set_col_tk["Capacity"] = 'Capacity';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["CHID"];
                $list_add[] = $value["ChaumberName"];
                $list_add[] = $value["WHID"];
                $list_add[] = $value["w_name"];
                $list_add[] = $value["length"];
                $list_add[] = $value["width"];
                $list_add[] = $value["height"];
                $list_add[] = $value["margin"];
                $list_add[] = $value["total_area"];
                $list_add[] = $value["utilize_area"];
                $list_add[] = $value["volume"];
                $list_add[] = $value["capacity"];
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'ChamberList.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }	

    public function export_Stackplanlist()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $result = $this->Warehouse_model->GetAllStack();
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["StackID"] =  'StackID';
            $set_col_tk["Stack Name"] =  'Stack Name';
            $set_col_tk["CHID"] =  'CHID';
            $set_col_tk["Chamber Name"] = 'Chamber Name';
            $set_col_tk["WHID"] = 'WHID';
            $set_col_tk["Warehouse Name"] = 'Warehouse Name';
            $set_col_tk["Length"] = 'Length';
            $set_col_tk["Width"] =  'Width';
            $set_col_tk["Height"] = 'Height';
            $set_col_tk["Margin"] = 'Margin';
            $set_col_tk["Total Area"] = 'Total Area';
            $set_col_tk["Volume"] = 'Volume';
            $set_col_tk["Capacity"] = 'Capacity';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["StackID"];
                $list_add[] = $value["CHID"];
                $list_add[] = $value["StackName"];
                $list_add[] = $value["ChaumberName"];
                $list_add[] = $value["WHID"];
                $list_add[] = $value["w_name"];
                $list_add[] = $value["length"];
                $list_add[] = $value["width"];
                $list_add[] = $value["height"];
                $list_add[] = $value["margin"];
                $list_add[] = $value["total_area"];
                $list_add[] = $value["volume"];
                $list_add[] = $value["capacity"];
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'stackPlan.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }	

    public function export_lotplanlist()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $result =$this->Warehouse_model->GetAllLot();
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 14);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 14);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["Lot ID"] =  'Lot ID';
            $set_col_tk["Lot Name"] =  'Lot Name';
            $set_col_tk["StackID"] =  'StackID';
            $set_col_tk["Stack Name"] =  'Stack Name';
            $set_col_tk["CHID"] =  'CHID';
            $set_col_tk["Chamber Name"] = 'Chamber Name';
            $set_col_tk["WHID"] = 'WHID';
            $set_col_tk["Warehouse Name"] = 'Warehouse Name';
            $set_col_tk["Length"] = 'Length';
            $set_col_tk["Width"] =  'Width';
            $set_col_tk["Height"] = 'Height';
            $set_col_tk["Margin"] = 'Margin';
            $set_col_tk["Total Area"] = 'Total Area';
            $set_col_tk["Volume"] = 'Volume';
            $set_col_tk["Capacity"] = 'Capacity';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["LOTID"];
                $list_add[] = $value["LotName"];
                $list_add[] = $value["StackID"];
                $list_add[] = $value["CHID"];
                $list_add[] = $value["StackName"];
                $list_add[] = $value["ChaumberName"];
                $list_add[] = $value["WHID"];
                $list_add[] = $value["w_name"];
                $list_add[] = $value["length"];
                $list_add[] = $value["width"];
                $list_add[] = $value["height"];
                $list_add[] = $value["margin"];
                $list_add[] = $value["total_area"];
                $list_add[] = $value["volume"];
                $list_add[] = $value["capacity"];
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'LotPlan.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }	

    public function chamberlist()
		{
			if (!has_permission_new('WHspacemgmt', '', 'view')) {
				access_denied('customers');
			}
            $whid = $this->input->post('warehouse1');
		    $result = $this->Warehouse_model->fetchAllWarehouseChambers($whid);
         

            foreach($result as $key=>$value){
				
				$html .= '<tr class="get_AccountID" data-id = "'.$value['CHID'].'">';
				$html .= '<td>'.$value['CHID'].'</td>'; 
				$html .= '<td>'.$value['ChaumberName'].'</td>'; 
				$html .= '<td>'.$value['WHID'].'</td>';
				$html .= '<td>'.$value['w_name'].'</td>';
				$html .= '<td>'.$value['length'].'</td>'; 
				$html .= '<td>'.$value['width'].'</td>'; 
				$html .= '<td>'.$value['height'].'</td>'; 
				$html .= '<td>'.$value['margin'].'</td>'; 
				$html .= '<td>'.$value['total_area'].'</td>'; 
				$html .= '<td>'.$value['utilize_area'].'</td>'; 
				$html .= '<td>'.$value['volume'].'</td>'; 
				$html .= '<td>'.$value['capacity'].'</td>'; 
				$html .= '</tr>';
			}
			echo json_encode($html);
	}

      public function stackplanlist()
      {
            if (!has_permission_new('WHspacemgmt', '', 'view')) {
                access_denied('customers');
            }
            $whid= $this->input->post('warehouse1');
            $chid = $this->input->post('chamber1');
            $result = $this->Warehouse_model->fetchAllStackDetails1($whid ,$chid);
		   

            foreach ($result as $key => $value) 
			{
				$html .= '<tr class="get_AccountID" data-id = "'.$value['StackID'].'">';
				$html .= '<td>'.$value["StackID"].'</td>';
				$html .= '<td>'.$value["StackName"].'</td>';
				$html .= '<td>'.$value["CHID"].'</td>';
				$html .= '<td>'.$value["ChaumberName"].'</td>';
				$html .= '<td>'.$value["WHID"].'</td>';
				$html .= '<td>'.$value["w_name"].'</td>';
				$html .= '<td>'.$value["length"].'</td>';
				$html .= '<td>'.$value["width"].'</td>';
				$html .= '<td>'.$value["height"].'</td>';
				$html .= '<td>'.$value["margin"].'</td>';
				$html .= '<td>'.$value["total_area"].'</td>';
				$html .= '<td>'.$value["capacity"].'</td>';
				
				$html .= '</tr>';
			}
			echo json_encode($html);
            
           
      }



      public function Lotplanlist(){
        if (!has_permission_new('WHspacemgmt', '', 'view')) {
            access_denied('customers');
        }
            
        $whid= $this->input->post('warehouse1');
        $chid = $this->input->post('chamber1');
        $stckid = $this->input->post('stack1');

        $result = $this->Warehouse_model->fetchAllStackDetails2($whid ,$chid, $stckid);
         
        foreach ($result as $key => $value) 
			{
				$html .= '<tr class="get_AccountID" data-id = "'.$value['LOTID'].'">';
				$html .= '<td>'.$value["LOTID"].'</td>';
				$html .= '<td>'.$value["LotName"].'</td>';
				$html .= '<td>'.$value["StackID"].'</td>';
				$html .= '<td>'.$value["StackName"].'</td>';
				$html .= '<td>'.$value["CHID"].'</td>';
				$html .= '<td>'.$value["ChaumberName"].'</td>';
				$html .= '<td>'.$value["WHID"].'</td>';
				$html .= '<td>'.$value["w_name"].'</td>';
				$html .= '<td>'.$value["length"].'</td>';
				$html .= '<td>'.$value["width"].'</td>';
				$html .= '<td>'.$value["height"].'</td>';
				$html .= '<td>'.$value["margin"].'</td>';
				$html .= '<td>'.$value["total_area"].'</td>';
				$html .= '<td>'.$value["capacity"].'</td>';
				
				$html .= '</tr>';
			}
			echo json_encode($html);


    }


    // Dailly Deposite  Trade
    
    public function dailydeposit($id = '')
    {
        if (!has_permission_new('Deposit_Booking', '', 'view')) {
            access_denied('customers');
        }
        $data['title']                = "Daily Deposit List";
        $this->load->model('Warehouse_model');
         $this->load->model('PurchaseTradeModel');
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        $data['CenterList'] = $this->Warehouse_model->GetCenterList();
        $data['PaymentCycleList'] = $this->Warehouse_model->GetPaymentCycleList();
        $data['lockingPeriod'] = $this->PurchaseTradeModel->getAllLockingDB();
        $this->load->view('admin/clients/dailydeposit', $data);
    }
    
    public function Get_Daily_request()
    {
        $data = array(
           'from_date' => date('d/m/Y'),
           'to_date'  => date('d/m/Y'),
           'account_type'  => '',
           'center'  => '',
           'IsApprove'  => ''
        );
       //$reject_by_broker_delay = $this->Warehouse_model->reject_by_delay_broker_approval();
       //$reject_by_kirti_delay = $this->Warehouse_model->reject_by_delay_kirti_approval();
        $OrderList = $this->Warehouse_model->GetDailyDepositRequest($data);
        
        $html ='';
        $ordersum = 0;
        $salesum = 0;
        $sr = 1;
        foreach($OrderList as $value){
            
            if($value['company'] == ""){
                $PartyName = $value['firstname']." ".$value['lastname'];
            }else{
                $PartyName = $value['company'];
            }
            if($value['BName'] == ""){
                $BrokerName = $value['Bfirstname']." ".$value['Blastname'];
            }else{
                $BrokerName = $value['BName'];
            }
            if($value['CustomerType'] =="1"){
                $AccountType = "Farmer";
            }elseif($value['CustomerType'] == "3"){
                $AccountType = "Trader";
            }elseif($value['CustomerType'] == "2"){
                $AccountType = "Broker";
            }elseif($value['CustomerType'] == "4"){
                $AccountType = "Corporate/Processor";
            }else{
                $AccountType = "";
            }
            $status = '';
            if($value['IsApprove'] == 'Y'){
                $status = 'Accepted';
            }elseif($value['IsApprove'] == 'N'){
                $status = 'Rejected';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                $status = 'Awaiting Client Approval';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                $status = 'Awaiting Broker Approval';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                $status = '--';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                $status = '--';
            }
            if($value['e_quantity'] == '' || $value['e_quantity'] == null){
                $Qty = $value['quantity'];
            }else{
                $Qty = $value['e_quantity'];
            }
    
            $html.= '<tr class="GetDetails" data-id="'.$value["BookingID"].'">';
            $html.= '<td style="text-align:left;">'.$sr.'</td>';
            $html.= '<td style="text-align:left;">'.$value['CenterName'].'</td>';
            $html.= '<td style="text-align:left;">'.$value['ItemName'].'</td>';
            $html.= '<td style="text-align:left;">'.$value['basic_rate'].'</td>';
            $html.= '<td style="text-align:left;">'.$Qty.' '.$value['unit'].'</td>';
            
            if($value['IsApprove'] == 'Y'){
                $html.= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
            }elseif($value['IsApprove'] == 'N'){
                $html.= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }
            $html.= '<td style="text-align:left;">'.$BrokerName.'</td>';
            $html.= '<td>'.$PartyName.'</td>';
            $html.= '<td style="text-align:left;">'.$status.'</td>';
            $html.= '</tr>';
            $sr++;
        }
        
        echo $html;
    }


    public function Get_Daily_request_by_show_button()
    {
        $data = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'account_type'  => $this->input->post('account_type'),
           'center'  => $this->input->post('center'),
           'IsApprove'  => $this->input->post('IsApprove')
        );
        $OrderList = $this->Warehouse_model->GetDailyRequest_by_show_button($data);
        
        $html ='';
        $ordersum = 0;
        $salesum = 0;
        $sr = 1;
        foreach($OrderList as $value){
            
            if($value['company'] == ""){
                $PartyName = $value['firstname']." ".$value['lastname'];
            }else{
                $PartyName = $value['company'];
            }
            if($value['CustomerType'] =="1"){
                $AccountType = "Farmer";
            }elseif($value['CustomerType'] == "3"){
                $AccountType = "Trader";
            }elseif($value['CustomerType'] == "2"){
                $AccountType = "Broker";
            }elseif($value['CustomerType'] == "4"){
                $AccountType = "Corporate/Processor";
            }else{
                $AccountType = "";
            }
            $status = '';
            if($value['IsApprove'] == 'Y'){
                $status = 'Accepted';
            }elseif($value['IsApprove'] == 'N'){
                $status = 'Rejected';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                $status = 'Awaiting Client Approval';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                $status = 'Awaiting Broker Approval';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                $status = '--';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                $status = '--';
            }
            if($value['e_quantity'] == '' || $value['e_quantity'] == null){
                $Qty = $value['quantity'];
            }else{
                $Qty = $value['e_quantity'];
            }
    
            $html.= '<tr class="GetDetails" data-id="'.$value["BookingID"].'">';
            $html.= '<td style="text-align:left;">'.$sr.'</td>';
            $html.= '<td style="text-align:left;">'.$value['CenterName'].'</td>';
            $html.= '<td style="text-align:left;">'.$value['ItemName'].'</td>';
            $html.= '<td style="text-align:left;">'.$value['basic_rate'].'</td>';
            $html.= '<td style="text-align:left;">'.$Qty.' '.$value['unit'].'</td>';
            
            if($value['IsApprove'] == 'Y'){
                $html.= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
            }elseif($value['IsApprove'] == 'N'){
                $html.= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)){
                
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
                
            }
            $html.= '<td style="text-align:left;">'.$value['BrokerID'].'</td>';
            $html.= '<td>'.$PartyName.'</td>';
            $html.= '<td style="text-align:left;">'.$status.'</td>';
            $html.= '</tr>';
            $sr++;
        }
        
        echo json_encode($html);
    }

    public function export_DailyDeposit()
    {
        
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $data = array(
               'from_date' => date('d/m/Y'),
               'to_date'  => date('d/m/Y'),
               'account_type'  => '',
               'center'  => '',
               'IsApprove'  => ''
            );
            $company_detail = $this->sale_reports_model->get_company_detail();
            $result = $this->Warehouse_model->GetDailyDepositRequest($data);
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
            $center_addr = array($address,);
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
            $writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["Location"] =  'Location';
            $set_col_tk["Item Name"] = 'Item Name';
            $set_col_tk["Rate"] = 'Rate';
            $set_col_tk["Quantity"] = 'Quantity';
            $set_col_tk["Action"] = 'Action';
            $set_col_tk["Broker Name"] =  'Broker Name';
            $set_col_tk["Party Name"] = 'Party Name';
            $set_col_tk["Status"] = 'Status';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["CenterName"];
                $list_add[] = $value["ItemName"];
                $list_add[] = $value["basic_rate"];
                $list_add[] = $value["e_quantity"];
                $list_add[] = $value["ClientApprove"];
                $list_add[] = $value["BName"];
                $list_add[] = $value["BName"];
                $list_add[] = $value["is_invoice"];
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'DailyDeposit.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }
}	