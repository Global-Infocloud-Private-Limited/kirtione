<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PincodeMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();   
        $this->load->model('PincodeModel');                        
    }

    public function AddEditPincode()
    {         
        $AllPincode = $this->PincodeModel->get_all_table_data($tablename="tblpin");
        foreach($AllPincode as &$pin)
        {
            $wh_state = '(short_name="'.$pin['State'].'")'; 
            $stateDetails = $this->PincodeModel->get_data($tablename="tblxx_statelist",$wh_state);
            $pin['statename'] = $stateDetails['state_name'];

            $wh_taluka = '(id="'.$pin['Taluka'].'")'; 
            $talukadetails = $this->PincodeModel->get_data($tablename="tblTalukaMaster",$wh_taluka);
            $pin['talukaname'] = $talukadetails['TalukaName'];

            $wh_city = '(id="'.$pin['District'].'")'; 
            $citydetails = $this->PincodeModel->get_data($tablename="tblxx_citylist",$wh_city);
            $pin['cityname'] = $citydetails['city_name'];
        }
        $data['AllPincode'] = $AllPincode;
        $this->load->view('admin/PincodeMaster/AddEditPincode',$data);
    }

    public function savePincode()
    {
        $Pincodename = $this->input->post('Pincodename');
        $Statename = $this->input->post('Statename');
        $Districtname = $this->input->post('Districtname');
        if($Districtname == "Osmanabad"){
            $Districtname = "DHARASHIV";
        }
        $Talukaname = $this->input->post('Talukaname');      

        $wh_state = '(state_name="'.$Statename.'")'; 
        $StateList = $this->PincodeModel->get_data($tablename="tblxx_statelist",$wh_state);

        $wh_city = '(state_id="'.$StateList['short_name'].'" AND city_name="'.$Districtname.'")'; 
        $CityList = $this->PincodeModel->get_data($tablename="tblxx_citylist",$wh_city);

        $wh_taluka = '(DistrictID="'.$CityList['id'].'" AND TalukaName="'.$Talukaname.'")'; 
        $TalukaList = $this->PincodeModel->get_data($tablename="tblTalukaMaster",$wh_taluka);

        if($TalukaList == "")
        {
            $insert_taluka = array(
                'DistrictID'=>$CityList['id'],
                'TalukaName'=>$Talukaname
            );
            $addTaluka = $this->PincodeModel->insert_data($tablename="tblTalukaMaster",$insert_taluka);
            $TalukaID = $addTaluka;
        }
        else
        {
            $TalukaID = $TalukaList['id'];
        }        
       
        $insert_pincode = array(           
            'Pincode'=>$Pincodename,   
            'Taluka'=>$TalukaID,   
            'District'=>$CityList['id'],   
            'State'=>$StateList['short_name']        
        );   
        $createpincode = $this->PincodeModel->insert_data($tablename="tblpin",$insert_pincode);
        if($createpincode) 
        { 
            echo json_encode(['success' => true,'message' => 'Data inserted successfully']);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to insert details']);
        }       
    }

    public function GetPincodeDetailsbyID()
    {
        $Id = $this->input->post('Id');
        $where = '(id="'.$Id.'")';
        $Pindetails = $this->PincodeModel->get_data($tablename="tblpin",$where);   

        $wh_state = '(short_name="'.$Pindetails['State'].'")'; 
        $stateDetails = $this->PincodeModel->get_data($tablename="tblxx_statelist",$wh_state);
        $Pindetails['statename'] = $stateDetails['state_name'];
        $Pindetails['state_id'] = $stateDetails['short_name'];

        $wh_taluka = '(id="'.$Pindetails['Taluka'].'")'; 
        $talukadetails = $this->PincodeModel->get_data($tablename="tblTalukaMaster",$wh_taluka);
        $Pindetails['talukaname'] = $talukadetails['TalukaName'];
        $Pindetails['taluka_id'] = $talukadetails['id'];

        $wh_city = '(id="'.$Pindetails['District'].'")'; 
        $citydetails = $this->PincodeModel->get_data($tablename="tblxx_citylist",$wh_city);
        $Pindetails['cityname'] = $citydetails['city_name'];
        $Pindetails['city_id'] = $citydetails['id'];
        
        echo json_encode($Pindetails);
    }

    public function UpdatePincodeDetails()
    {
        $Id = $this->input->post('Id');
        $Pincode = $this->input->post('Pincode');
        $Statename = $this->input->post('Statename');
        $Districtname = $this->input->post('Districtname');
        $Talukaname = $this->input->post('Talukaname');

        $update_details = array(           
            'Pincode'=>$Pincode,   
            'Taluka'=>$Talukaname,   
            'District'=>$Districtname,   
            'State'=>$Statename           
        );      
        $where = '(id="'.$Id.'")';          
        $updateDetails = $this->PincodeModel->edit_data($tablename="tblpin",$where,$update_details);  
        if($updateDetails) 
        { 
            echo json_encode(['success' => true]);        
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update details']);
        }
    }

    public function Pincode_table_data()
    {
        $table_data = $this->PincodeModel->get_all_table_data($tablename="tblpin");      
        foreach($table_data as &$pin)
        {
            $wh_state = '(short_name="'.$pin['State'].'")'; 
            $stateDetails = $this->PincodeModel->get_data($tablename="tblxx_statelist",$wh_state);
            $pin['statename'] = $stateDetails['state_name'];

            $wh_taluka = '(id="'.$pin['Taluka'].'")'; 
            $talukadetails = $this->PincodeModel->get_data($tablename="tblTalukaMaster",$wh_taluka);
            $pin['talukaname'] = $talukadetails['TalukaName'];

            $wh_city = '(id="'.$pin['District'].'")'; 
            $citydetails = $this->PincodeModel->get_data($tablename="tblxx_citylist",$wh_city);
            $pin['cityname'] = $citydetails['city_name'];
        }  
        echo json_encode($table_data);
    }

    public function FetchAddressDetailsByPincode()
	{
		$zip = $this->input->post('zip');
		$curl = curl_init();
		curl_setopt_array(
        $curl,
        array(
		CURLOPT_URL => 'https://api.postalpincode.in/pincode/' . $zip . '',
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
		$response_array = json_decode($response);
		echo  $response;
	}

    public function CheckPincodeExistence()
    {
        $zip = $this->input->post('zip');        
        $exist = $this->PincodeModel->GetDataPincode($zip);        
        echo json_encode($exist);
    }
}