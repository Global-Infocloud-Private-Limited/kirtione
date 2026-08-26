<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Booking_settlement extends AdminController
{
    public function __construct()
	{
		parent::__construct();
			
		$this->load->model('Booking_settlement_model');
	}
	
	public function index(){
	    $data['title'] = "Booking Settlement";
	    $this->load->view('admin/booking_settlement/booking_settlement',$data);
	}
	
	public function GetAllClientsName(){
	    $BookingType = $this->input->post('BookingType');
	    $Name = $this->input->post('Name');
	    $BookingID = $this->input->post('BookingID');
	    $result = $this->Booking_settlement_model->GetAllClientsNameDB($BookingType,$Name,$BookingID);
	    $html = '';
	    $html = '<option value="">Not Selected</option>';
	    foreach($result as $key=>$value){
	        $AccountID = $value["AccountID"];
	        if($value["company"] != ''){
	            $name = $value["company"];
	        }
	        else{
	            $name = $value["firstname"].' '.$value["lastname"];
	        }
	        
	        $html .= '<option value="'.$AccountID.'">'.$name.'</option>';
	    }
	    echo $html;
	}
	
	public function GetAllBookingID(){
	    $BookingType = $this->input->post('BookingType');
	    $Name = $this->input->post('Name');
	    $BookingID = $this->input->post('BookingID');
	    $result = $this->Booking_settlement_model->GetAllBookingIDDB($BookingType,$Name,$BookingID);
	    $html = '';
	    $html = '<option value="">Not Selected</option>';
	    foreach($result as $key=>$value){
	        $BookingID = $value["BookingID"];
	        $html .= '<option value="'.$BookingID.'">'.$BookingID.'</option>';
	    }
	    echo $html;
	}
	
	public function GetAllStates(){
	    $result = $this->Booking_settlement_model->GetAllStatesDB();
	    $html = '';
	    $html = '<option value="">Not Selected</option>';
	    foreach($result as $key=>$value){
	        $id = $value["id"];
	        $name = $value["state_name"];
	        $html .= '<option value="'.$id.'">'.$name.'</option>';
	    }
	    echo $html;
	}
	
	public function GetCities(){
	    $State = $this->input->post('State');
	    $result = $this->Booking_settlement_model->GetCitiesDB($State);
	    $html = '';
	    $html = '<option value="">Not Selected</option>';
	    foreach($result as $key=>$value){
	        $id = $value["id"];
	        $name = $value["city"];
	        $html .= '<option value="'.$id.'">'.$name.'</option>';
	    }
	    echo $html;
	}
	
	public function GetTableData(){
	    $Name = $this->input->post('Name');
	    $BookingType = $this->input->post('BookingType');
	    $BookingID = $this->input->post('BookingID');
	    $result = $this->Booking_settlement_model->GetTableDataDB($Name,$BookingType,$BookingID);
	    $sr = 1;
	    foreach($result as $key=>$value){
	        if($value['company'] != ''){
	            $PartyName = $value['company'];
	        }
	        else{
	            $PartyName = $value['firstname'].' '.$value['lastname'];
	        }
	        
	        $html .= '<tr onclick=fill_data("'.$value["BookingID"].'")>';
	        $html .= '<td>'.$sr.'</td>';
	        $html .= '<td>'.$value['AccountID'].'</td>';
	        $html .= '<td>'.$PartyName.'</td>';
	        $html .= '<td>'.$value['BookingID'].'</td>';
	        $html .= '<td>'._d($value['TransDate']).'</td>';
	        $html .= '<td>'.$value['CenterID'].'</td>';
	        $html .= '<td>'.$value['ItemID'].'</td>';
	        $html .= '<td>'.$value['ItemName'].'</td>';
	        $html .= '<td>'.$value['quantity'].' '.$value['unit'].'</td>';
	        $html .= '</tr>';
	        $sr++;
	    }
	    
	    echo $html;
	}
	
	public function GetSingleBookingData(){
	    $BookingID = $this->input->post('BookingID');
	    $result = $this->Booking_settlement_model->GetSingleBookingDataDB($BookingID);
	    echo json_encode($result);
	}
}