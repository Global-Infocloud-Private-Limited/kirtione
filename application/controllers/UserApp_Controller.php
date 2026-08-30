<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class UserApp_Controller extends ClientsController {

    public function __construct() {
        parent::__construct();
        hooks()->do_action('clients_authentication_constructor', $this);
        $this->load->helper(array('form', 'url', 'file'));
        //$this->load->model('BuisnessModel');
        $this->load->library('upload');
        $this->load->model('GateControl_model');
        $this->load->model('CardModel');
        $this->load->model('FpoOrderModel');
        $this->load->model('KirtiOneOrderModel');
    }
//============== Kirti API Code for Staff Application Start ====================

    public function GetStaffDataAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                $staffID = $decode['staff_id'];
                if($staffID) {
                    $this->db->where('staffid', $staffID);
                    $staffData = $this->db->get(db_prefix().'staff')->row();
                    if($staffData){
                        $response = array("status"=>true,"message"=>"Data found","staff_data"=>$staffData);
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }
                } else {
                    $response = array("status"=>false,"message"=>"staff id is required");
                }
            }
        }
        echo json_encode($response);    
    }

    public function GetPurchOrderAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                    $BookingID = $decode['BookingID'];
                    $BookingDetails = $this->GetBookingDetails($BookingID);
                    if($BookingDetails){
                        $response = array("status"=>true,"message"=>"Booking Details","Details"=>$BookingDetails);
                    }else{
                        $response = array("status"=>false,"message"=>"somthing went wrong");
                    }
                }
            }
        echo json_encode($response);    
    }
    
    public function GetBookingDetails($BookingID) 
    {   
        $this->db->select('tbllead_master.*,tblCenterMaster.PCCenterID,tblclients.company,tblclients.ShortCode,tblclients.fcm_token,tblcontacts.firstname,tblcontacts.lastname,Bdetails.fcm_token AS Bfcm_token');
        $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
        $this->db->join('tblclients AS Bdetails','Bdetails.AccountID = tbllead_master.BrokerID',"");
        $this->db->where('tbllead_master.BookingID', $BookingID);
        return $this->db->get('tbllead_master')->row();
    }
    
//============== Kirti Customer API Code Start ==========================================
    
    public function ChkMobileAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "mobile_no"=>$decode['mobile_no']
                );
                $CheckUser = $this->CheckUserExist($data);
                if($CheckUser){
                    $response = array("status"=>false,"message"=>"User already exit.","UserDetails"=>$CheckUser);
                }else{
                    $response = array("status"=>true,"message"=>"New user");
                }
            }
        }
        echo json_encode($response);    
    }

    public function ChkAadharNoAPI($param=FALSE) 
    { 
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "AadharNumber"=>$decode['AadharNumber']
                );
                $CheckAadhar = $this->CheckAadharExist($data);
                if($CheckAadhar){
                    $response = array("status"=>false,"message"=>"Aadhar number already exist.","AadharDetails"=>$CheckAadhar);
                }else{
                    $response = array("status"=>true,"message"=>"New Aadhar");
                }
            }
        }
        echo json_encode($response); 
    }
    
    public function ChkAccountNoAPI($param=FALSE) 
    { 
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "AccountNumber"=>$decode['AccountNumber']
                );
                $CheckAccountNo = $this->CheckBankAccountNoExist($data);
                if($CheckAccountNo){
                    $response = array("status"=>false,"message"=>"Account number already exist.","AccountDetails"=>$CheckAccountNo);
                }else{
                    $response = array("status"=>true,"message"=>"New AccountNo");
                }
            }
        }
        echo json_encode($response); 
    }

    public function ChkPanNoAPI($param=FALSE) 
    { 
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "Pan"=>$decode['Pan']
                );
                $CheckPan = $this->CheckPanNoExist($data);
                if($CheckPan){
                    $response = array("status"=>false,"message"=>"Pan number already exist.","PanNoDetails"=>$CheckPan);
                }else{
                    $response = array("status"=>true,"message"=>"New PanNo");
                }
            }
        }
        echo json_encode($response); 
    }

    public function GetAadharDetailsAPI($param=FALSE) 
    { 
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "LoginToken"=>$decode['LoginToken'],
                    "mobile_no"  => $decode['mobile_no']
                );
                $Details = $this->AadhardetailsByLoginToken($data);
                if($Details){ 
                    $response = array("status"=>true,"message"=>"Details Retrived.","AadharDetails"=>$Details);
                }else{
                    $response = array("status"=>false,"message"=>"No Data Found");
                }
            }
        }
        echo json_encode($response); 
    }
//========================= Sign IN New User ===================================
    public function SignInAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                if($decode['fcm_token']){
                    $fcm_token = $decode['fcm_token'];
                }else{
                    $fcm_token = NULL;
                }
                
                if($decode['ref_by']){
                    $ref_by = $decode['ref_by'];
                }else{
                    $ref_by = NULL;
                }
                $data = array(
                    "mobile_no"=>$decode['mobile_no'],
                    "DeviceID"=>$decode['DeviceID'],
                    "name"=>$decode['name'],
                    "fcm_token"=>$fcm_token,
                    "ref_by"=>$ref_by,
                );
                if($decode['Pincode']){
                    $data['Pincode'] = $decode['Pincode'];
                }else{
                    $data['Pincode'] = "Old";
                }
                if($decode['VillageID']){
                    $data['VillageID'] = $decode['VillageID'];
                }
                if($decode['VillageName']){
                    $data['VillageName'] = $decode['VillageName'];
                }
                if($decode['State']){
                    $data['State'] = $decode['State'];
                }
                if($decode['District']){
                    $data['District'] = $decode['District'];
                }
                if($decode['Taluka']){
                    $data['Taluka'] = $decode['Taluka'];
                }
                $CheckUser = $this->CheckUserExist($data);
                if($CheckUser){
                    $response = $this->LogedIn($data);
                }else{
                    $response = $this->SignIn($data);
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function SignIn($params=FALSE)
    {
        $token = bin2hex(random_bytes(16));
        if($params['mobile_no'] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Mobile Number");
        }elseif($params['name'] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Mobile Number");
        }elseif($params['Pincode'] == "" && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Enter Pincode");
        }elseif($params['VillageID'] == ""  && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Select Village Name Or Add Your Village");
        }elseif($params['VillageID'] == "Add New Village" && $params['VillageName'] == "" && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Enter Village Name");
        }elseif($params['VillageID'] == "Add New Village" && $params['State'] == "" && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Select State");
        }elseif($params['VillageID'] == "Add New Village" && $params['District'] == "" && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Select District");
        }elseif($params['VillageID'] == "Add New Village" && $params['Taluka'] == "" && $params['Pincode'] != "Old"){
            $response = array("status"=>false,"message"=>"Please Select Taluka");
        }else{
            $Clientdata =array(
                "PlantID"=>1,
                "AccountID"=>$params['mobile_no'],
                "ref_by"=>$params['ref_by'],
                "company"=>$params['name'],
                "phonenumber"=>$params['mobile_no'],
                "DeviceID"=>$params['DeviceID'],
                "StartDate"=>date('Y-m-d H:i:s'),
                "datecreated"=>date('Y-m-d H:i:s'),
                "last_login"=>date('Y-m-d H:i:s'),
                "login_tokan"=>$token,
                "fcm_token"=>$params['fcm_token']
            );
            $VillageID = NULL;
            if($params['VillageID'] == "Add New Village"){
                $VillageData = array(
                    "VisitDate"=>date("Y-m-d H:i:s"),
                    "VillageName"=>$params['VillageName'],
                    "Pincode"=>$params['Pincode'],
                    "TalukaId"=>$params['Taluka'],
                    "DistrictId"=>$params['District'],
                    "StateId"=>$params['State'],
                    "UserID"=>$params['mobile_no'],
                    "datecreated"=>date("Y-m-d H:i:s"),
                );
                $this->db->insert(db_prefix().'villagedetails', $VillageData);
                $VillageID = $this->db->insert_id();
                $Clientdata["state"] = $params['State'];
                $Clientdata["dist"] = $params['District'];
                $Clientdata["subdist"] = $params['Taluka'];
                $Clientdata["po"] = $params['VillageName'];
                $Clientdata["zip"] = $params['Pincode'];
            }elseif($params['Pincode'] != "Old"){
                // Get Village Details By ID
                $VillageID = $params['VillageID'];
                $VillageDetails = $this->GetVillageDetails($VillageID);
                $Clientdata["state"] = $VillageDetails->StateId;
                $Clientdata["dist"] = $VillageDetails->DistrictId;
                $Clientdata["subdist"] = $VillageDetails->TalukaId;
                $Clientdata["po"] = $VillageDetails->VillageName;
                $Clientdata["zip"] = $VillageDetails->Pincode;
            }
            $Clientdata["VillageID"] = $VillageID;
            $this->db->insert(db_prefix().'clients', $Clientdata);
    		if($this->db->affected_rows() > 0){
    		    $Contactdata =array(
                    "PlantID"=>1,
                    "ref_by"=>$params['ref_by'],
                    "AccountID"=>$params['mobile_no'],
                    "firstname"=>$params['name'],
                    "phonenumber"=>$params['mobile_no'],
                    "datecreated"=>date('Y-m-d H:i:s'),
                );
                $this->db->insert(db_prefix().'contacts', $Contactdata);
    			$response = array("status"=>true,"message"=>"Record Inserted Successfully","login_tokan"=>$token);
    		}else{
    		    $response = array("status"=>false,"message"=>"Something Went Wrong");
    		}
        }
        return $response; 
    }
    
    public function LogedIn($params=FALSE)
    {
        $token = bin2hex(random_bytes(16));
        $Clientdata =array(
            "last_login"=>date('Y-m-d H:i:s'),
            "login_tokan"=>$token,
            "DeviceID"=>$params['DeviceID'],
            "fcm_token"=>$params['fcm_token'],
        );
        
        $this->db->where('AccountID', $params['mobile_no']);
        $this->db->update(db_prefix().'clients', $Clientdata);
        $data = array(
            "mobile_no"=>$params['mobile_no'],
            "DeviceID"=>$params['DeviceID']
        );
                    
        if($this->db->affected_rows() > 0){
            $UserDetails = $this->CheckUserExist($data);
            $response = array("status"=>true,"message"=>"Logged in Successfully","UserDetails"=>$UserDetails);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }
    
    public function GetVillageDetails($VillageID) 
    {
        $this->db->select('tblvillagedetails.*');
        $this->db->where('tblvillagedetails.id', $VillageID);
        $VillageDetails = $this->db->get(db_prefix().'villagedetails')->row();
        return $VillageDetails;
    }
    
    public function CheckUserExist($params=FALSE) 
    {
        $mobile_no = $params['mobile_no'];
        $this->db->where('AccountID', $mobile_no);
        $UserDetails = $this->db->get(db_prefix().'clients')->row();
        return $UserDetails;
    }

    public function CheckAadharExist($params=FALSE)
    {
        $AadharNumber = $params['AadharNumber'];
        $this->db->where('aadhaar_number', $AadharNumber);
        $AadharDetails = $this->db->get(db_prefix().'contacts')->row();
        return $AadharDetails;
    }

    public function CheckBankAccountNoExist($params=FALSE)
    {
        $AccountNumber = $params['AccountNumber'];
        $this->db->where('accountNumber', $AccountNumber);
        $AccountNoDetails = $this->db->get(db_prefix().'BankDetails')->row();
        return $AccountNoDetails;
    }  

    public function CheckPanNoExist($params=FALSE)
    {
        $Pan = $params['Pan'];
        $this->db->where('Pan', $Pan);
        $PanNoDetails = $this->db->get(db_prefix().'contacts')->row();
        return $PanNoDetails;
    }  

   public function AadhardetailsByLoginToken($params=FALSE)
    {
        $LoginToken = $params['LoginToken'];
        $mobile_no = $params['mobile_no'];
        $this->db->where('login_tokan', $LoginToken);
        $this->db->where('phonenumber', $mobile_no);
        $ClientDetails = $this->db->get(db_prefix().'clients')->row();
        if ($ClientDetails) {
         $Accountid = $ClientDetails->AccountID; 
         
 	   $this->db->select('AccountID,firstname, middlename, lastname');
           $this->db->where('AccountID', $Accountid);
           $AadharDetails = $this->db->get(db_prefix().'contacts')->row();  
           return $AadharDetails;
        }
        return null;
    }
    
//================== Set Account Type ==========================================    
    public function SetAccountTypeAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "CustomerType"=>$decode['CustomerType'],
                    );
                    $response = $this->SetAccountType($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function SetAccountType($params=FALSE)
    {
        $AccountType =array(
            "CustomerType"=>$params['CustomerType']
        );
        if($params['CustomerType'] == "2"){
            $next_code = $this->get_next_code('next_broker_code');
            $number = 'KB'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "3"){
            $next_code = $this->get_next_code('next_trader_code');
            $number = 'KT'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "1"){
            $next_code = $this->get_next_code('next_farmer_code');
            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "4"){
            $next_code = $this->get_next_code('next_corporate_code');
            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }
        
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $AccountType);
        
        if($this->db->affected_rows() > 0){
            if($params['CustomerType'] == "2"){
                $this->increment_next_number('next_broker_code');
            }elseif($params['CustomerType'] == "3"){
                $this->increment_next_number('next_trader_code');
            }elseif($params['CustomerType'] == "1"){
                $this->increment_next_number('next_farmer_code');
            }elseif($params['CustomerType'] == "1"){
                $this->increment_next_number('next_corporate_code');
            }
            $response = array("status"=>true,"message"=>"Account Type Updated Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","Account Type"=>$AccountType);
        }
        return $response; 
    }
    
//========================== Set Defualt language ==============================    
    public function langUpdateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "default_language"=>$decode['default_language'],
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan']
                    );
                    $response = $this->langUpdate($data,$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function langUpdate($params=FALSE)
    {
        $lang_data =array(
            "default_language"=>$params['default_language'],
            "UserID2"=>$params['phonenumber'],
            "Lupdate"=>date('Y-m-d H:i:s')
        );
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $lang_data);
        if($this->db->affected_rows() > 0){
            $response = array("status"=>true,"message"=>"Language Update Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }

//========================== Add Register address Update =======================
    public function latlongUpdateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "reg_latitude"=>$decode['reg_latitude'],
                        "reg_longitude"=>$decode['reg_longitude'],
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan']
                    );
                    if($decode['address']){
                        $data["address"] = $decode['address'];
                    }
                    $response = $this->latlongUpdate($data,$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function latlongUpdate($params=FALSE)
    {
        $latitude = $params['reg_latitude'];
        $longitude = $params['reg_longitude'];
       /* $state = NULL;
        $address = '';*/
        /*if($latitude != null || $longitude != null){
            $apiKey = 'sQIqNbAj0nkWUYCtXDr9qmGQm9h6GduI';
            $apiUrl = "http://www.mapquestapi.com/geocoding/v1/address?key=".$apiKey."&location=".$latitude.",".$longitude."&includeRoadMetadata=true&includeNearestIntersection=true";
            $json = file_get_contents($apiUrl);
            $data = json_decode($json);
            $state = $data->results[0]->locations[0]->adminArea3;
            
            if($data->results[0]->locations[0]->adminArea6 != "" || $data->results[0]->locations[0]->adminArea6 != NULL){
                $address .= $data->results[0]->locations[0]->adminArea6.', ';
            }
            if($data->results[0]->locations[0]->adminArea5 != "" || $data->results[0]->locations[0]->adminArea5 != NULL){
                $address .= $data->results[0]->locations[0]->adminArea5.', ';
            }
            if($data->results[0]->locations[0]->adminArea4 != "" || $data->results[0]->locations[0]->adminArea4 != NULL){
                $address .= $data->results[0]->locations[0]->adminArea4.', ';
            }
            if($data->results[0]->locations[0]->adminArea3 != "" || $data->results[0]->locations[0]->adminArea3 != NULL){
                $address .= $data->results[0]->locations[0]->adminArea3.', ';
            }
            if($data->results[0]->locations[0]->postalCode != "" || $data->results[0]->locations[0]->postalCode != NULL){
                $address .= $data->results[0]->locations[0]->postalCode;
            }
        }*/
        
        
        $lang_data =array(
            "reg_latitude"=>$latitude,
            "reg_longitude"=>$longitude,
            "address"=>$params['address'],
            "UserID2"=>$params['phonenumber'],
            "Lupdate"=>date('Y-m-d H:i:s')
        );
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $lang_data);
        if($this->db->affected_rows() > 0){
            $response = array("status"=>true,"message"=>"Address Update Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }
    public function AddTraderByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['mobile_no']);
                if($checkLoginTokan){
                    $CkeckTraderdata = array(
                        "mobile_no"=>$decode['TraderID'],
                    );
                    $CheckUser = $this->CheckUserExist($CkeckTraderdata);
                    if($CheckUser){
                        $response = array("status"=>false,"message"=>"This Trader Account is Already exit");
                    }else{
                        $Traderdata = array(
                            "TraderID"=>$decode['TraderID'],
                            "name"=>$decode['name'],
                            "DeviceID"=>$decode['DeviceID'],
                        );
                        $response = $this->AddNewTraderByBroker($Traderdata);
                    }
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function AddNewTraderByBroker($params=FALSE)
    {
        //$token = bin2hex(random_bytes(16));
        $Clientdata =array(
            "PlantID"=>1,
            "AccountID"=>$params['TraderID'],
            "company"=>$params['name'],
            "phonenumber"=>$params['TraderID'],
            "UserID"=>$params['mobile_no'],
            "DeviceID"=>$params['DeviceID'],
            "StartDate"=>date('Y-m-d H:i:s'),
            "datecreated"=>date('Y-m-d H:i:s'),
            "last_login"=>NULL,
            "login_tokan"=>NULL,
        );
        $this->db->insert(db_prefix().'clients', $Clientdata);
		if($this->db->affected_rows() > 0){
		    $Contactdata =array(
                "PlantID"=>1,
                "AccountID"=>$params['TraderID'],
                "firstname"=>$params['name'],
                "phonenumber"=>$params['TraderID'],
                "datecreated"=>date('Y-m-d H:i:s'),
            );
            $this->db->insert(db_prefix().'contacts', $Contactdata);
			$response = array("status"=>true,"message"=>"Trader Record Inserted Successfully","login_tokan"=>$token);
		}else{
		    $response = array("status"=>false,"message"=>"Something Went Wrong");
		}
        
        return $response; 
    }
    
    //======================= Set Account Type From Broker Login API ===============    
    public function SetAccountTypeByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "CustomerType"=>$decode['CustomerType'],
                        "TraderID"=>$decode['TraderID'],
                    );
                    $response = $this->SetAccountTypeByBroker($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
                
            }
        }
        echo json_encode($response);    
    }
    
    public function SetAccountTypeByBroker($params=FALSE)
    {
        $AccountType =array(
            "CustomerType"=>$params['CustomerType']
        );
        if($params['CustomerType'] == "2"){
            $next_code = $this->get_next_code('next_broker_code');
            $number = 'KB'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "3"){
            $next_code = $this->get_next_code('next_trader_code');
            $number = 'KT'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "1"){
            $next_code = $this->get_next_code('next_farmer_code');
            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }elseif($params['CustomerType'] == "4"){
            $next_code = $this->get_next_code('next_corporate_code');
            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $AccountType['ShortCode'] = $number;
        }
        
        $this->db->where('AccountID', $params['TraderID']);
        //$this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $AccountType);
        
        if($this->db->affected_rows() > 0){
            if($params['CustomerType'] == "2"){
                $this->increment_next_number('next_broker_code');
            }elseif($params['CustomerType'] == "3"){
                $this->increment_next_number('next_trader_code');
            }elseif($params['CustomerType'] == "1"){
                $this->increment_next_number('next_farmer_code');
            }elseif($params['CustomerType'] == "1"){
                $this->increment_next_number('next_corporate_code');
            }
            $response = array("status"=>true,"message"=>"Account Type Updated Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","Account Type"=>$AccountType);
        }
        return $response; 
    }
    
    public function AddTraderPANByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $Traderdata = array(
                        "TraderID"=>$decode['TraderID'],
                        "TraderName"=>$decode['TraderName'],
                        "PAN"=>$decode['PAN'],
                        "kyc_status"=>$decode['kyc_status'],
                        "phonenumber"=>$decode['phonenumber'],
                    );
                    $response = $this->AddTraderPANByBroker($Traderdata);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function AddTraderPANByBroker($params=FALSE)
    {
        //$token = bin2hex(random_bytes(16));
        $Clientdata =array(
            "KYCStatus"=>$params['kyc_status'],
            "company"=>$params['TraderName'],
            "Lupdate"=>date('Y-m-d H:i:s'),
            "UserID2"=>$params['phonenumber'],
        );
        $this->db->where('AccountID', $params['TraderID']);
        $this->db->update(db_prefix().'clients', $Clientdata);
		if($this->db->affected_rows() > 0){
		    $Contactdata =array(
                "firstname"=>$params['TraderName'],
                "Pan"=>$params['PAN'],
                "pan_verified_date"=>date('Y-m-d H:i:s'),
                "panVerifiedID"=>$params['phonenumber'],
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$params['phonenumber'],
            );
            $this->db->where('AccountID', $params['TraderID']);
            $this->db->update(db_prefix().'contacts', $Contactdata);
			$response = array("status"=>true,"message"=>"Pan Details added Successfully","login_tokan"=>$token);
		}else{
		    $response = array("status"=>false,"message"=>"Something Went Wrong");
		}
        
        return $response; 
    }
//=================== Add PAN Details After Submit PAN OTP =====================     
    public function AddPanAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $Traderdata = array(
                        "TraderName"=>$decode['TraderName'],
                        "PAN"=>$decode['PAN'],
                        "phonenumber"=>$decode['phonenumber'],
                        "kyc_status"=>$decode['kyc_status'],
                    );
                    $response = $this->AddPan($Traderdata);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }

    public function AddPan($params=FALSE)
    {
        //$token = bin2hex(random_bytes(16));
        $Clientdata =array(
            "KYCStatus"=>$params['kyc_status'],
            "company"=>$params['TraderName'],
            "UserID2"=>$params['phonenumber'],
            "Lupdate"=>date('Y-m-d H:i:s')
        );
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->update(db_prefix().'clients', $Clientdata);
		if($this->db->affected_rows() > 0){
		    $Contactdata =array(
                "firstname"=>$params['TraderName'],
                "Pan"=>$params['PAN'],
                "pan_verified_date"=>date('Y-m-d H:i:s'),
                "panVerifiedID"=>$params['phonenumber'],
            );
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'contacts', $Contactdata);
			$response = array("status"=>true,"message"=>"Pan Details added Successfully","login_tokan"=>$token);
		}else{
		    $response = array("status"=>false,"message"=>"Something Went Wrong");
		}
        
        return $response; 
    }
    
    
//======================== Aadhaar Details Update As Per Aadhaar data ==========
    public function AadharUpdateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                // Aadhar image
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "gender"=>$decode['gender'],
                    "house"=>$decode['house'],
                    "street"=>$decode['street'],
                    "loc"=>$decode['loc'],
                    "vtc"=>$decode['vtc'],
                    "po"=>$decode['po'],
                    "subdist"=>$decode['subdist'],
                    "dist"=>$decode['dist'],
                    "state"=>$decode['state'],
                    "aadhaar_number"=>$decode['aadhaar_number'],
                    "aadhar_image"=>$decode['aadhar_image'],
                    "dob"=>$decode['dob'],
                    "zip"=>$decode['zip'],
                    "full_name"=>$decode['full_name'],
                    "login_tokan"=>$decode['login_tokan'],
                    "street2"=>$decode['street2'],
                    "dist2"=>$decode['dist2'],
                    "state2"=>$decode['state2'],
                    "zip2"=>$decode['zip2'],
                    "Addrs_flag"=>$decode['Addrs_flag'],
                    "kyc_status"=>$decode['kyc_status'],
                );
                $response = $this->AadharUpdate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AadharUpdate($params=FALSE)
    {
        $firtname_pos = strpos($params['full_name'],' ');
        $firstname = substr($params['full_name'],0,$firtname_pos);
        $lastname = substr($params['full_name'],$firtname_pos);
        $GetAddressIDs = $this->GetAddresIDs($params['state'],$params['dist'],$params['subdist']);
        $AadharAddr = array(
            "AccountID" => $params['phonenumber'],
            "Type" => 1,
            "TransDate" => date('Y-m-d H:i:s'),
            "state" => $params['state'],
            "dist" => $params['dist'],
            "subdist" => $params['subdist'],
            "po" => $params['po'],
            "vtc" => $params['vtc'],
            "loc" => $params['loc'],
            "street" => $params['street'],
            "house" => $params['house'],
            "pincode" => $params['zip'],
        );
        
        $AadharAddr_ID = array(
            "AccountID" => $params['phonenumber'],
            "Type" => 2,
            "TransDate" => date('Y-m-d H:i:s'),
            "state" => $GetAddressIDs['StateID'],
            "dist" => $GetAddressIDs['CityID'],
            "subdist" => $GetAddressIDs['TalukaID'],
            "po" => $params['po'],
            "vtc" => $params['vtc'],
            "loc" => $params['loc'],
            "street" => $params['street'],
            "house" => $params['house'],
            "pincode" => $params['zip'],
        );
        $TempAddr = array(
            "AccountID" => $params['phonenumber'],
            "Type" => 3,
            "TransDate" => date('Y-m-d H:i:s'),
            "state" => $params['state2'],
            "dist" => $params['dist2'],
            "street" => $params['street2'],
            "pincode" => $params['zip2'],
        );
        //return $AadharAddr_ID;
        $Clientdata =array(
            "company"=>$params['full_name'],
            "house"=>$params['house'],
            "street"=>$params['street'],
            "loc"=>$params['loc'],
            "vtc"=>$params['vtc'],
            "po"=>$params['po'],
            "subdist"=>$GetAddressIDs['TalukaID'],
            "dist"=>$GetAddressIDs['CityID'],
            "state"=>$GetAddressIDs['StateID'],
            "aadhar_image"=>$params['aadhar_image'],
            "zip"=>$params['zip'],
            "KYCStatus"=>$params['kyc_status']
        );
        $Contactdata =array(
            "firstname"=>$firstname,
            "lastname"=>$lastname,
            "gender"=>$params['gender'],
            "dob"=>$params['dob'],
            "aadhaar_number"=>$params['aadhaar_number'],
            "aadhaar_verified_date"=>date('Y-m-d H:i:s'),
            "veryfiedUserID"=> $params['phonenumber']
        );
        
        // Add Aadhar Address 
        /* Add Address As per Aadhar Address */
        $this->db->insert(db_prefix().'AadharDetails', $AadharAddr);
        /* Add Address with IDS */
        $this->db->insert(db_prefix().'AadharDetails', $AadharAddr_ID);
        /* Add Temparary Address  */
        if($params['Addrs_flag'] == "Y"){
            $AadharAddr['Type'] = '3';
            $this->db->insert(db_prefix().'AadharDetails', $AadharAddr);
        }else{
            $this->db->insert(db_prefix().'AadharDetails', $TempAddr);
        }
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $Clientdata);
        if($this->db->affected_rows() > 0){
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'contacts', $Contactdata);
            $response = array("status"=>true,"message"=>"Aadhaar Update Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }


    
    public function GetAddresIDs($state,$dist,$subdist) 
    {
        $State = strtoupper($state);
        $dist = strtoupper($dist);
        $subdist = strtoupper($subdist);
        $this->db->select('*');
        $this->db->where('state_name', $State);
        $StateDetails = $this->db->get(db_prefix().'xx_statelist')->row();
        $StateID = $StateDetails->short_name;
        
        // Get CityID 
        $this->db->select('*');
        $this->db->where('city', $dist);
        $CityDetails = $this->db->get(db_prefix().'_xx_city')->row();
        $CityID = $CityDetails->id;
        
        // Get Taluka 
        $this->db->select('*');
        $this->db->where('TalukaName', $subdist);
        $TalukaDetails = $this->db->get(db_prefix().'TalukaMaster')->row();
        $TalukaID = $TalukaDetails->id;
        
        $IdDetails = array(
            "StateID" => $StateID,
            "CityID" => $CityID,
            "TalukaID" => $TalukaID,
        );
        
        return $IdDetails;
    }
    
//================== Bank Account Detail Update ================================
    public function BankAccountUpdateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    // Cheque / passbook image
                    /*if($decode['cheque_image'])
                    {
                        $image1 = base64_decode($decode['cheque_image']);
                        $image_name = md5(uniqid(rand(), true));
                        $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                            mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                        }
                        $path1 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                        file_put_contents($path1 , $image1);
                    }else{
                        $path1 = '';  
                    }*/
                    
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "AccountFor"=>$decode['AccountFor'],
                        "ifsc"=>$decode['ifsc'],
                        "bankName"=>$decode['bankName'],
                        "branchName"=>$decode['branchName'],
                        "AaccountName"=>$decode['AaccountName'],
                        "cheque_image"=>$decode['cheque_image'],
                        "accountNumber"=>$decode['accountNumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "kyc_status"=>$decode['kyc_status'],
                    );
                    $response = $this->BankDetailUpdate($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function BankDetailUpdate($params=FALSE)
    {
        $query = $this->db->query('SELECT id FROM tblBankDetails WHERE accountNumber = "' . $params['accountNumber'] . '"');
        if($query->num_rows() > 0) {
            $response = array("status"=>false,"message"=>"Account number already exists");
        } else {
            $query = $this->db->query('SELECT id FROM tblBankDetails WHERE AccountID = "' . $params['phonenumber'] .'" AND IsPrimary = "1"');
            $isPrimary = 1;
            if($query->num_rows() > 0) {
                $isPrimary = 0;
            }
            $Bankdata =array(
                "ifsc"=>$params['ifsc'],
                "bankName"=>$params['bankName'],
                "branchName"=>$params['branchName'],
                "AaccountName"=>$params['AaccountName'],
                "accountNumber"=>$params['accountNumber'],
                "cheque_image"=>$params['cheque_image'],
                "AccountID"=>$params['phonenumber'],
                "IsPrimary"=>$isPrimary,
                "TransDate"=>date('Y-m-d H:i:s')
            );
            $Clientdata =array(
                "AccountFor"=>$params['AccountFor'],
                "KYCStatus"=>$params['kyc_status'],
            );
            if($this->db->insert(db_prefix().'BankDetails', $Bankdata)){
                $this->db->where('AccountID', $params['phonenumber']);
                $this->db->where('login_tokan', $params['login_tokan']);
                $this->db->update(db_prefix().'clients', $Clientdata);
                $response = array("status"=>true,"message"=>"Bank Detail Update Successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Bank details update failed");
            }
        }
        return $response; 
    }
    
    // Bank Account Detail Update
    public function BankDetailUpdateByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    // Cheque / passbook image
                    /*if($decode['cheque_image'])
                    {
                        $image1 = base64_decode($decode['cheque_image']);
                        $image_name = md5(uniqid(rand(), true));
                        $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                            mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                        }
                        $path1 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                        file_put_contents($path1 , $image1);
                    }else{
                        $path1 = '';  
                    }*/
                    
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "TraderID"=>$decode['TraderID'],
                        "AccountFor"=>$decode['AccountFor'],
                        "ifsc"=>$decode['ifsc'],
                        "bankName"=>$decode['bankName'],
                        "branchName"=>$decode['branchName'],
                        "AaccountName"=>$decode['AaccountName'],
                        "cheque_image"=>$decode['cheque_image'],
                        "accountNumber"=>$decode['accountNumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "kyc_status"=>$decode['kyc_status'],
                    );
                    $response = $this->BankDetailUpdateByBroker($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function BankDetailUpdateByBroker($params=FALSE)
    {
        $query = $this->db->query('SELECT id FROM tblBankDetails WHERE accountNumber = "' . $params['accountNumber'] . '"');
        if($query->num_rows() > 0) {
            $response = array("status"=>false,"message"=>"Account number already exists");
        } else {
            $query = $this->db->query('SELECT id FROM tblBankDetails WHERE AccountID = "' . $params['phonenumber'] .'" AND IsPrimary = "1"');
            $isPrimary = 1;
            if($query->num_rows() > 0) {
                $isPrimary = 0;
            }
            
            $Bankdata = array(
                "ifsc"=>$params['ifsc'],
                "bankName"=>$params['bankName'],
                "branchName"=>$params['branchName'],
                "AaccountName"=>$params['AaccountName'],
                "accountNumber"=>$params['accountNumber'],
                "cheque_image"=>$params['cheque_image'],
                "AccountID"=>$params['TraderID'],
                "IsPrimary"=>$isPrimary,
                "TransDate"=>date('Y-m-d H:i:s')
            );
            $Clientdata =array(
                "AccountFor"=>$params['AccountFor'],
                "KYCStatus"=>$params['kyc_status'],
                "UserID2"=>$params['phonenumber'],
                "Lupdate"=>date('Y-m-d H:i:s'),
            );
            if($this->db->insert(db_prefix().'BankDetails', $Bankdata)){
                $this->db->where('AccountID', $params['TraderID']);
                $this->db->update(db_prefix().'clients', $Clientdata);
                $response = array("status"=>true,"message"=>"Bank Detail Update Successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong");
            }
        }
        return $response; 
    }
//========================= Get Login User Profile =============================
    public function UserProfileAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "CustomerType"=>$decode['CustomerType'],
                    );
                    $response = $this->UserProfile($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function UserProfile($params=FALSE)
    {
        $AccountID = $params['phonenumber'];
        $CustomerType = $params['CustomerType'];
        
        $this->db->select('tblclients.AccountID,tblclients.company,tblcontacts.aadhaar_number,tblcontacts.Pan');
        $this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID AND tblcontacts.PlantID = tblclients.PlantID');
        $this->db->where('tblclients.AccountID', $AccountID);
        $this->db->where('tblclients.CustomerType', $CustomerType);
        $AadharPan = $this->db->get(db_prefix().'clients')->row();
        $date['PanAadhaar'] = $AadharPan;
        if($CustomerType == "1"){
            $type_array = array('1','3');
            $this->db->select('tblAadharDetails.house,tblAadharDetails.street,tblAadharDetails.loc,tblAadharDetails.po,tblAadharDetails.subdist,tblAadharDetails.dist,tblAadharDetails.state,tblAadharDetails.pincode,tblAadharDetails.Type');
            $this->db->where('tblAadharDetails.AccountID', $AccountID);
            $this->db->where_in('tblAadharDetails.Type', $type_array);
            $AadharDetails = $this->db->get(db_prefix().'AadharDetails')->result_array();
            $date['AadhaarDetails'] = $AadharDetails;
        }else{
            $this->db->select('tblGstRecord.business_name,tblGstRecord.address AS GSTAddress,tblGstRecord.date_of_registration,tblGstRecord.active_status,tblGstRecord.IsPrimary,
            tblGstRecord.gstin,tblGstRecord.state');
            $this->db->where('tblGstRecord.AccountID', $AccountID);
            $GSTDetails = $this->db->get(db_prefix().'GstRecord')->result_array();
            $date['GSTDetails'] = $GSTDetails;
        }
        
        $this->db->select('tblBankDetails.ifsc,tblBankDetails.bankName,tblBankDetails.branchName,tblBankDetails.AaccountName,tblBankDetails.accountNumber,tblBankDetails.IsPrimary');
        $this->db->where('tblBankDetails.AccountID', $AccountID);
        $BankDetails = $this->db->get(db_prefix().'BankDetails')->result_array();
        $date['BankDetails'] = $BankDetails;
        
        if($date){
            $response = array("status"=>true,"message"=>"Profile Data","login_tokan"=>$params['login_tokan'],"data"=>$date);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }
    
    public function GetBankAccountsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                    );
                    $response = $this->GetBankAccounts($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function GetBankAccounts($params=FALSE)
    {
        $AccountID = $params['phonenumber'];
        $this->db->where('tblBankDetails.AccountID', $AccountID);
        $BankDetails = $this->db->get(db_prefix().'BankDetails')->result_array();
        if($BankDetails){
            $response = array("status"=>true,"message"=>"Bank Accounts List","data"=>$BankDetails);
        }else{
            $response = array("status"=>false,"message"=>"Bank Accounts List","data"=>$BankDetails);
        }
        return $response; 
    }
//=================== Set Primary Bank Account From Bank List ==================
    public function SetPrimaryBankAccountAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "account_number"=>$decode['account_number'],
                    );
                    $response = $this->SetPrimaryBankAccount($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function SetPrimaryBankAccount($params=FALSE)
    {
        $BankDetailsArray = array('IsPrimary'=>0);
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->update(db_prefix() . 'BankDetails', $BankDetailsArray);
        
        $RequestData = array("IsPrimary"=>1);
        $AccountNumber = $params['account_number'];
        $this->db->where('tblBankDetails.accountNumber', $AccountNumber);
        $this->db->update(db_prefix().'BankDetails', $RequestData);
        
        if($this->db->affected_rows() > 0){
            $response = array("status"=>true,"message"=>"Primary account updated","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Primary account update failed");
        }
        return $response; 
    }
    
//================== Account User Logout =======================================
    public function logoutAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->logoutAccount($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function logoutAccount($params=FALSE)
    {
        $Clientdata =array(
            "login_tokan"=>NULL,
        );
        
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $Clientdata);
        if($this->db->affected_rows() > 0){
            $response = array("status"=>true,"message"=>"Logout Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong");
        }
        return $response; 
    }
//==================== Add GST List Against PAN Number =========================
    public function GstListAddAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "pan_number"=>$decode['pan_number'],
                        "gstin_list"=>$decode['gstin_list'],
                        "kyc_status"=>$decode['kyc_status'],
                    );
                    $response = $this->GstListAdd($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function GstListAdd($params=FALSE)
    {
        $Contactdata =array(
            "Pan"=>$params['pan_number'],
            "pan_verified_date"=>date('Y-m-d H:i:s'),
            "panVerifiedID"=>$params['phonenumber'],
            "Lupdate"=>date('Y-m-d H:i:s'),
            "UserID2"=>$params['phonenumber']
        );
        $kycupdate =array(
            "KYCStatus"=>$params['kyc_status'],
            "Lupdate"=>date('Y-m-d H:i:s'),
            "UserID2"=>$params['phonenumber']
        );
        
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->update(db_prefix().'contacts', $Contactdata);
        if($this->db->affected_rows() > 0){
            $inert = 0;
            $gstin_list = $params['gstin_list'];
            foreach ($gstin_list as $key => $value) {
                $GetState_short_code = $this->Getstate_short_code($value['state']);
                $Gstdata =array(
                    "AccountID"=>$params['phonenumber'],
                    "gstin"=>$value['gstin'],
                    "state_code"=>$GetState_short_code->StateID,
                    "state"=>$value['state'],
                    "active_status"=>$value['active_status']
                );
                if($this->db->insert(db_prefix().'GstRecord', $Gstdata)){
                    $inert++;
                }
            }
            if($inert > 0){
                $this->db->where('AccountID', $params['phonenumber']);
                $this->db->update(db_prefix().'clients', $kycupdate);
                $response = array("status"=>true,"message"=>"GST Record added Successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>true,"message"=>"GST Record Not Available","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"PAN Not Updated","dd"=>$Contactdata);
        }
        return $response; 
    }
    
    public function Getstate_short_code($state) 
    {
        $State = strtoupper($state);
        $this->db->select('*');
        $this->db->where('id', $State);
        $StateDetails = $this->db->get(db_prefix().'xx_statelist')->row();
        $StateID = $StateDetails->short_name;
        
        $IdDetails = array(
            "StateID" => $StateID
        );
        
        return $IdDetails;
    }
    
    public function GstListAddByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "pan_number"=>$decode['pan_number'],
                        "gstin_list"=>$decode['gstin_list'],
                        "kyc_status"=>$decode['kyc_status'],
                        "TraderID"=>$decode['TraderID'],
                    );
                    $response = $this->GstListAddByBroker($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
                
            }
        }
        echo json_encode($response);    
    }
    
    public function GstListAddByBroker($params=FALSE)
    {
        $Contactdata =array(
            "Pan"=>$params['pan_number'],
            "pan_verified_date"=>date('Y-m-d H:i:s'),
            "panVerifiedID"=>$params['TraderID'],
            "Lupdate"=>date('Y-m-d H:i:s'),
            "UserID2"=>$params['phonenumber']
        );
        $kycupdate =array(
            "KYCStatus"=>$params['kyc_status'],
            "Lupdate"=>date('Y-m-d H:i:s'),
            "UserID2"=>$params['phonenumber']
        );
        
        $this->db->where('AccountID', $params['TraderID']);
        $this->db->update(db_prefix().'contacts', $Contactdata);
        if($this->db->affected_rows() > 0){
            $inert = 0;
            $gstin_list = $params['gstin_list'];
            foreach ($gstin_list as $key => $value) {
                $Gstdata =array(
                    "AccountID"=>$params['TraderID'],
                    "gstin"=>$value['gstin'],
                    "state_code"=>$value['state_code'],
                    "state"=>$value['state'],
                    "active_status"=>$value['active_status']
                );
                if($this->db->insert(db_prefix().'GstRecord', $Gstdata)){
                    $inert++;
                }
            }
            if($inert > 0){
                $this->db->where('AccountID', $params['TraderID']);
                $this->db->update(db_prefix().'clients', $kycupdate);
                $response = array("status"=>true,"message"=>"GST Record added Successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>true,"message"=>"GST Record Not Available","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"PAN Not Updated","dd"=>$Contactdata);
        }
        return $response; 
    }
    
//===================== Set Primary GST Number =================================    
    public function SetPrimaryGstAPI($param=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "gstin"=>$decode['gstin'],
                        "business_name"=>$decode['business_name'],
                        "constitution_of_business"=>$decode['constitution_of_business'],
                        "taxpayer_type"=>$decode['taxpayer_type'],
                        "date_of_registration"=>$decode['date_of_registration'],
                        "address"=>$decode['address'],
                        "IsPrimary"=>$decode['IsPrimary'],
                        "kyc_status"=>$decode['kyc_status']
                    );
                    $response = $this->SetPrimaryGst($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function SetPrimaryGst($params=FALSE)
    {
        $resetdata = array(
            "IsPrimary"=>0
        );
        
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->update(db_prefix().'GstRecord', $resetdata);
        
        $setdata =array(
            "business_name"=>$params['business_name'],
            "constitution_of_business"=>$params['constitution_of_business'],
            "taxpayer_type"=>$params['taxpayer_type'],
            "date_of_registration"=>$params['date_of_registration'],
            "address"=>$params['address'],
            "IsPrimary"=>$params['IsPrimary']
        );
        
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('gstin', $params['gstin']);
        $this->db->update(db_prefix().'GstRecord', $setdata);
        if($this->db->affected_rows() > 0){
            $kycupdate = array(
                "KYCStatus"=>$params['kyc_status'],
                "company"=>$params['business_name'],
                "UserID2"=>$params['phonenumber'],
                "Lupdate"=>date('Y-m-d H:i:s')
            );
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'clients', $kycupdate);
            $response = array("status"=>true,"message"=>"GST Record Updated Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","dd"=>$Contactdata);
        }
        return $response; 
    }
    
    public function SetPrimaryGstByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "gstin"=>$decode['gstin'],
                        "business_name"=>$decode['business_name'],
                        "constitution_of_business"=>$decode['constitution_of_business'],
                        "taxpayer_type"=>$decode['taxpayer_type'],
                        "date_of_registration"=>$decode['date_of_registration'],
                        "address"=>$decode['address'],
                        "IsPrimary"=>$decode['IsPrimary'],
                        "kyc_status"=>$decode['kyc_status'],
                        "TraderID"=>$decode['TraderID']
                    );
                    $response = $this->SetPrimaryGstByBroker($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
                
            }
        }
        echo json_encode($response);    
    }
    
    public function SetPrimaryGstByBroker($params=FALSE)
    {
        $resetdata = array(
            "IsPrimary"=>0
        );
        
        $this->db->where('AccountID', $params['TraderID']);
        $this->db->update(db_prefix().'GstRecord', $resetdata);
        
        $setdata =array(
            "business_name"=>$params['business_name'],
            "constitution_of_business"=>$params['constitution_of_business'],
            "taxpayer_type"=>$params['taxpayer_type'],
            "date_of_registration"=>$params['date_of_registration'],
            "address"=>$params['address'],
            "IsPrimary"=>$params['IsPrimary']
        );
        
        $this->db->where('AccountID', $params['TraderID']);
        $this->db->where('gstin', $params['gstin']);
        $this->db->update(db_prefix().'GstRecord', $setdata);
        if($this->db->affected_rows() > 0){
            $kycupdate = array(
                "KYCStatus"=>$params['kyc_status'],
                "company"=>$params['business_name'],
                "UserID2"=>$params['phonenumber'],
                "Lupdate"=>date('Y-m-d H:i:s')
            );
            $this->db->where('AccountID', $params['TraderID']);
            $this->db->update(db_prefix().'clients', $kycupdate);
            $response = array("status"=>true,"message"=>"GST Record Updated Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","dd"=>$Contactdata);
        }
        return $response; 
    }
//======================= Add DIN Details ======================================    
    public function AddDinDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "din_number"=>$decode['din_number'],
                    "dob"=>$decode['dob'],
                    "full_name"=>$decode['full_name'],
                    "permanent_address"=>$decode['permanent_address'],
                    "email"=>$decode['email'],
                    "father_name"=>$decode['father_name'],
                    "client_id"=>$decode['client_id'],
                    "nationality"=>$decode['nationality'],
                    "present_address"=>$decode['present_address'],
                    "kyc_status"=>$decode['kyc_status'],
                );
                $response = $this->AddDinDetails($data); 
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function AddDinDetails($params=FALSE)
    {
        
        $DIN_Data =array(
            "TransDate"=>date('Y-m-d H:i:s'),
            "AccountID"=>$params['phonenumber'],
            "present_address"=>$params['present_address'],
            "nationality"=>$params['nationality'],
            "client_id"=>$params['client_id'],
            "father_name"=>$params['father_name'],
            "email"=>$params['email'],
            "permanent_address"=>$params['permanent_address'],
            "full_name"=>$params['full_name'],
            "dob"=>$params['dob'],
            "din_number"=>$params['din_number']
        );
        $this->db->insert(db_prefix().'DIN_details', $DIN_Data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            $kycupdate = array(
                "KYCStatus"=>$params['kyc_status']
            );
        
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'clients', $kycupdate);
            $response = array("status"=>true,"message"=>"DIN Record Updated Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Already Record Added","DINRecord"=>$DIN_Data);
        }
        return $response; 
    }


    
    
//================= Document Upload ============================================
    public function DocUploadAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                // GST Certificate
                if($decode['gst_reg_cert'])
                {
                    $image1 = base64_decode($decode['gst_reg_cert']);
                    $image_name = 'gst_cert';
                    $filename = $image_name . '.' . $decode['gst_reg_cert_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // PAN Image
                if($decode['pan'])
                {
                    $image2 = base64_decode($decode['pan']);
                    $image_name = "pan";
                    $filename = $image_name . '.' . $decode['pan_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // board resolution certificate
                if($decode['board_resolution_cert'])
                {
                    $image3 = base64_decode($decode['board_resolution_cert']);
                    $image_name = "board_resolution_cert_ext";
                    $filename = $image_name . '.' . $decode['board_resolution_cert_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
                
                // company encorporation certificate
                if($decode['comp_encorporation_cert'])
                {
                    $image4 = base64_decode($decode['comp_encorporation_cert']);
                    $image_name = "comp_encorporation_cert";
                    $filename = $image_name . '.' . $decode['comp_encorporation_cert_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path4 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path4 , $image4);
                }else{
                    $path4 = '';  
                }
                
                // Farmer Aadhar Front
                if($decode['aadhaar_front'])
                {
                    $image5 = base64_decode($decode['aadhaar_front']);
                    $image_name = "aadhaar_front";
                    $filename = $image_name . '.' . $decode['aadhaar_front_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path5 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path5 , $image5);
                }else{
                    $path5 = '';  
                }
                
                // Farmer Aadhar Back
                if($decode['aadhaar_back'])
                {
                    $image6 = base64_decode($decode['aadhaar_back']);
                    $image_name = "aadhaar_back";
                    $filename = $image_name . '.' . $decode['aadhaar_back_ext'];
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path6 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path6 , $image6);
                }else{
                    $path6 = '';  
                }
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "pan"=>$path2,
                    "gst_reg_cert"=>$path1,
                    "board_resolution_cert"=>$path3,
                    "comp_encorporation_cert"=>$path4,
                    "aadhaar_front"=>$path5,
                    "aadhaar_back"=>$path6,
                    "kyc_status"=>$decode['kyc_status'],
                );
                $response = $this->DocUpload($data);
            }else{
                $response = array("status"=>false,"message"=>"Please login with registered mobile number");
            }
          }
        }
        echo json_encode($response);    
    }
    
    public function DocUpload($params=FALSE)
    {
        $Doc_upload = array();
        
        if($params['pan'] !== ""){
            $Doc_upload["PANImage"] = $params['pan'];
        }
        if($params['gst_reg_cert'] !== ""){
            $Doc_upload["gst_certificate"] = $params['gst_reg_cert'];
        }
        if($params['board_resolution_cert'] !== ""){
            $Doc_upload["board_res_cert"] = $params['board_resolution_cert'];
        }
        if($params['comp_encorporation_cert'] !== ""){
            $Doc_upload["comp_encorp_cert"] = $params['comp_encorporation_cert'];
        }
        if($params['aadhaar_front'] !== ""){
            $Doc_upload["aadhaar_front"] = $params['aadhaar_front'];
        }
        if($params['aadhaar_back'] !== ""){
            $Doc_upload["aadhaar_back"] = $params['aadhaar_back'];
        }
        $kycupdate = array(
            'KYCStatus'=>$params['kyc_status']  
        );
        $this->db->where('AccountID', $params['phonenumber']);
        $this->db->where('login_tokan', $params['login_tokan']);
        $this->db->update(db_prefix().'clients', $Doc_upload);
        
        if($this->db->affected_rows() > 0){
            
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'clients', $kycupdate);
            $response = array("status"=>true,"message"=>"Document Uploaded Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","Account Type"=>$AccountType);
        }
        return $response; 
    }
    
    
    // Document Upload
    public function DocUploadByBrokerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    // GST Certificate
                    if($decode['gst_reg_cert'])
                    {
                        $image1 = base64_decode($decode['gst_reg_cert']);
                        $image_name = 'gst_cert';
                        $filename = $image_name . '.' . $decode['gst_reg_cert_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path1 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path1 , $image1);
                    }else{
                        $path1 = '';  
                    }
                    
                    // PAN Image
                    if($decode['pan'])
                    {
                        $image2 = base64_decode($decode['pan']);
                        $image_name = "pan";
                        $filename = $image_name . '.' . $decode['pan_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path2 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path2 , $image2);
                    }else{
                        $path2 = '';  
                    }
                    
                    // board resolution certificate
                    if($decode['board_resolution_cert'])
                    {
                        $image3 = base64_decode($decode['board_resolution_cert']);
                        $image_name = "board_resolution_cert_ext";
                        $filename = $image_name . '.' . $decode['board_resolution_cert_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path3 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path3 , $image3);
                    }else{
                        $path3 = '';  
                    }
                    
                    // company encorporation certificate
                    if($decode['comp_encorporation_cert'])
                    {
                        $image4 = base64_decode($decode['comp_encorporation_cert']);
                        $image_name = "comp_encorporation_cert";
                        $filename = $image_name . '.' . $decode['comp_encorporation_cert_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path4 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path4 , $image4);
                    }else{
                        $path4 = '';  
                    }
                    
                    // Farmer Aadhar Front
                    if($decode['aadhaar_front'])
                    {
                        $image5 = base64_decode($decode['aadhaar_front']);
                        $image_name = "aadhaar_front";
                        $filename = $image_name . '.' . $decode['aadhaar_front_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path5 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path5 , $image5);
                    }else{
                        $path5 = '';  
                    }
                    
                    // Farmer Aadhar Back
                    if($decode['aadhaar_back'])
                    {
                        $image6 = base64_decode($decode['aadhaar_back']);
                        $image_name = "aadhaar_back";
                        $filename = $image_name . '.' . $decode['aadhaar_back_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/Upload_doc/'.$decode['TraderID'])) {
                            mkdir('assets/Upload_doc/'.$decode['TraderID'], 0777, true);
                        }
                        $path6 = "assets/Upload_doc/".$decode['TraderID']."/".$filename;
                        file_put_contents($path6 , $image6);
                    }else{
                        $path6 = '';  
                    }
                    
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "TraderID"=>$decode['TraderID'],
                        "pan"=>$path2,
                        "gst_reg_cert"=>$path1,
                        "board_resolution_cert"=>$path3,
                        "comp_encorporation_cert"=>$path4,
                        "aadhaar_front"=>$path5,
                        "aadhaar_back"=>$path6,
                        "kyc_status"=>$decode['kyc_status'],
                    );
                    $response = $this->DocUploadByBroker($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function DocUploadByBroker($params=FALSE)
    {
        $Doc_upload = array();
        
        if($params['pan'] !== ""){
            $Doc_upload["PANImage"] = $params['pan'];
        }
        if($params['gst_reg_cert'] !== ""){
            $Doc_upload["gst_certificate"] = $params['gst_reg_cert'];
        }
        if($params['board_resolution_cert'] !== ""){
            $Doc_upload["board_res_cert"] = $params['board_resolution_cert'];
        }
        if($params['comp_encorporation_cert'] !== ""){
            $Doc_upload["comp_encorp_cert"] = $params['comp_encorporation_cert'];
        }
        if($params['aadhaar_front'] !== ""){
            $Doc_upload["aadhaar_front"] = $params['aadhaar_front'];
        }
        if($params['aadhaar_back'] !== ""){
            $Doc_upload["aadhaar_back"] = $params['aadhaar_back'];
        }
        $Doc_upload["UserID2"] = $params['phonenumber'];
        $Doc_upload["Lupdate"] = date('Y-m-d H:i:s');
        $kycupdate = array(
            'KYCStatus'=>$params['kyc_status'],
            'UserID2'=>$params['phonenumber'],
            'Lupdate'=>date('Y-m-d H:i:s')  
        );
        $this->db->where('AccountID', $params['TraderID']);
        $this->db->update(db_prefix().'clients', $Doc_upload);
        
        if($this->db->affected_rows() > 0){
            
            $this->db->where('AccountID', $params['TraderID']);
            $this->db->update(db_prefix().'clients', $kycupdate);
            $response = array("status"=>true,"message"=>"Document Uploaded Successfully","login_tokan"=>$params['login_tokan']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","Account Type"=>$AccountType);
        }
        return $response; 
    }
    
    // Document Upload
    public function testPDFAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                echo $decode;
                die;
                // GST Certificate
                if($decode['gst_reg_cert'])
                {
                    $image1 = base64_decode($decode['gst_reg_cert']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // PAN Image
                if($decode['pan'])
                {
                    $image2 = base64_decode($decode['pan']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // board resolution certificate
                if($decode['board_resolution_cert'])
                {
                    $image3 = base64_decode($decode['board_resolution_cert']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
                
                // company encorporation certificate
                if($decode['board_resolution_cert'])
                {
                    $image4 = base64_decode($decode['board_resolution_cert']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path4 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path4 , $image4);
                }else{
                    $path4 = '';  
                }
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "pan"=>$path2,
                    "gst_reg_cert"=>$path1,
                    "board_resolution_cert"=>$path3,
                    "comp_encorporation_cert"=>$path4
                );
                $response = $this->DocUpload($data);
            }
        }
        echo json_encode($response);    
    }
    
    // Add Firm Details
    public function AddFarmAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                // 7 / 12 Certificate
                /*if($decode['saatbara_image'])
                {
                    $image1 = base64_decode($decode['saatbara_image']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['phonenumber'])) {
                        mkdir('assets/Upload_doc/'.$decode['phonenumber'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['phonenumber']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }*/
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "farm_name"=>$decode['farm_name'],
                    "survey_number"=>$decode['survey_number'],
                    "farm_area"=>$decode['farm_area'],
                    "farm_unit"=>$decode['farm_unit'],
                    "state"=>$decode['state'],
                    "district"=>$decode['district'],
                    "taluka"=>$decode['taluka'],
                    "village"=>$decode['village'],
                    "pincode"=>$decode['pincode'],
                    "saatbara_image"=>$decode['saatbara_image'],
                );
                $response = $this->firmAdd($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function firmAdd($params=FALSE)
    {
        $Firm_data = array(
            "AccountID"=>$params['phonenumber'],
            "farm_name"=>$params['farm_name'],
            "survey_number"=>$params['survey_number'],
            "farm_area"=>$params['farm_area'],
            "farm_unit"=>$params['farm_unit'],
            "state"=>$params['state'],
            "district"=>$params['district'],
            "taluka"=>$params['taluka'],
            "village"=>$params['village'],
            "pincode"=>$params['pincode'],
            "saatbara_image"=>$params['saatbara_image'],
            "TransDate"=> date('Y-m-d H:i:s')
        );
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $this->db->insert(db_prefix().'farm_details', $Firm_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $response = array("status"=>true,"message"=>"Farm Added Successfully","userDetails"=>$checkLoginTokan);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    
    // Get Farm By FarmerID
    public function GetFarmAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetfarmList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetfarmList($params=FALSE)
    {
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblfarm_details.id,AccountID,TransDate,farm_name,survey_number,farm_area,farm_unit,state,district,taluka,village,pincode');
            $this->db->where('AccountID', $params['phonenumber']);
            $FarmList = $this->db->get(db_prefix().'farm_details')->result_array();
            $response = array("status"=>true,"message"=>"Farm List","FarmList"=>$FarmList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    public function CheckTokan($login_tokan,$AccountID) 
    {
        $this->db->where('AccountID', $AccountID);
        $this->db->where('login_tokan', $login_tokan);
        $UserDetails = $this->db->get(db_prefix().'clients')->row();
        return $UserDetails;
    }
    
    public function CheckTokanStaff($login_tokan,$phonenumber) 
    {
        $this->db->where('phonenumber', $phonenumber);
        $this->db->where('login_tokan', $login_tokan);
        $UserDetails = $this->db->get('tblstaff')->row_array();
        return $UserDetails;
    }
    
//==================== Get State List ==========================================
    public function GetStateListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                /*$checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){*/
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan']
                    );
                    $response = $this->GetStateList($data);
                /*}else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }*/
            }
        }
        echo json_encode($response);    
    }
    
    public function GetStateList($params=FALSE)
    {
        $array = array('MH','KR');
        $this->db->where_in('short_name',$array);
        $this->db->order_by('state_name',"ASC");
        $StateList = $this->db->get(db_prefix().'xx_statelist')->result_array();
        $response = array("status"=>true,"message"=>"State List","StateList"=>$StateList);
        return $response; 
    }
    
    
//================== Get City List Against State ID ============================
    public function GetCityListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                /*$checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){*/
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "state_id"=>$decode['state_id']
                    );
                    $response = $this->GetCityList($data);
                /*}else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }*/
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCityList($params=FALSE)
    {
        $this->db->select('tblxx_citylist.id,tblxx_citylist.city_name AS city');
        $this->db->where('state_id', $params['state_id']);
        $this->db->order_by('city',"ASC");
        $CityList = $this->db->get(db_prefix().'xx_citylist')->result_array();
        $response = array("status"=>true,"message"=>"City List","City"=>$CityList);
        return $response; 
    }
    
    // Get Taluka List
    public function GetTalukaAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
               
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "districtID"=>$decode['districtID']
                );
                $response = $this->GetTalukaList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetTalukaList($params=FALSE)
    {
        $this->db->order_by('TalukaName', "ASC");
        $this->db->where('DistrictID', $params['districtID']);
        $TalukaList = $this->db->get(db_prefix().'TalukaMaster')->result_array();
        $response = array("status"=>true,"message"=>"Taluka List","Taluka"=>$TalukaList);
        return $response; 
    }
    
    // Add Crop Details
    public function AddCropAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID'],
                    "CropArea"=>$decode['CropArea'],
                    "AreaUnit"=>$decode['AreaUnit'],
                    "season"=>$decode['season'],
                    "FarmID"=>$decode['FarmID']
                );
                $response = $this->CropAdd($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CropAdd($params=FALSE)
    {
        
        $Crop_data = array(
            "AccountID"=>$params['phonenumber'],
            "FarmID"=>$params['FarmID'],
            "ItemID"=>$params['ItemID'],
            "CropArea"=>$params['CropArea'],
            "AreaUnit"=>$params['AreaUnit'],
            "season"=>$params['season'],
            "TransDate"=> date('Y-m-d H:i:s')
        );
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $this->db->insert(db_prefix().'CropMaster', $Crop_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $response = array("status"=>true,"message"=>"Crop Added Successfully","userDetails"=>$checkLoginTokan);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Crop By FarmerID
    public function GetCropAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetCropList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCropList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblCropMaster.AccountID,tblCropMaster.FarmID,tblCropMaster.ItemID,CropArea,AreaUnit,season,tblitems.ItemName AS CropName,tblfarm_details.farm_name');
            $this->db->where('tblCropMaster.AccountID', $params['phonenumber']);
            $this->db->join('tblitems', 'tblitems.ItemID = tblCropMaster.ItemID');
            $this->db->join('tblfarm_details', 'tblfarm_details.id = tblCropMaster.FarmID');
            $CropList = $this->db->get(db_prefix().'CropMaster')->result_array();
            $response = array("status"=>true,"message"=>"Crop List","CropList"=>$CropList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//===================== Add Trade for Kirti Purchase API =======================
    public function AddCropSellAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID'],
                    "quantity"=>$decode['quantity'],
                    "equantity"=>$decode['equantity'],
                    "basic_rate"=>$decode['basic_rate'],
                    "unit"=>$decode['unit'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType']
                );
                $response = $this->CropsaleAdd($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CropsaleAdd($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        if($params['unit'] == 'Quintal'){
            $qty = $params['quantity'] / 10;
        }else{
            $qty = $params['quantity'];
        }
        
        $Cropsale_data = array(
            "FY"=>$FY,
            "PlantID" => 1,
            "CenterID"=>$params['CenterID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$qty,
            "e_quantity"=>$qty,
            "basic_rate"=>$params['basic_rate'],
            "unit"=>'MT',
            "UserID"=>$params['phonenumber'],
            "TransDate"=> date('Y-m-d H:i:s'),
            "TType"=> "P",
            "TType2"=> "Purchase"
        );
        if($params['UserType'] == "2"){
            $Cropsale_data['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $Cropsale_data['BrokerID'] = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $Cropsale_data['BrokerApprove'] = 'Y';
                $Cropsale_data['BrokerID'] = $params['phonenumber'];
                $Cropsale_data['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $Cropsale_data['BrokerApprove'] = 'NA';
                $Cropsale_data['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            $Cropsale_data['BrokerApprove'] = 'NA';
            $Cropsale_data['BrokerID'] = $params['OtherID'];
        }
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'lead_master', $Cropsale_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                // Get Company Purchase for 
                $PartyDetails = $this->GetPurchaseForParty($params['CenterID'],$params['ItemID']);
                if($PartyDetails){
                    $PartyID = $PartyDetails->PartyID;
                }else{
                    $PartyID = "KASPL";
                }
                
                $new_Number = get_number($params['CenterID'],'S');
                 
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $params['CenterID'].'S'.date('d').date('m').date('y').$number;
            
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID,'PartyID'=>$PartyID]);
                $this->increment_center_wise_booking_number($params['CenterID'],'S');
                
                $title = "Trade Created";
                $screen = "1";
                $body = "Your BookingID : ".$bookingID.' Created';
                $booking_id = $bookingID;
                $to = $checkLoginTokan->fcm_token;
            
                if($checkLoginTokan->CustomerType == "1"){
                    // Farmer 
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    //$ids = array($AccountID);
                }else if($checkLoginTokan->CustomerType  == "3"){
                    // Send Notification to Trader
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Broker
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }else if($checkLoginTokan->CustomerType ){
                    // Send Notification to Broker
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Trader
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }
                $response = array("status"=>true,"message"=>"Crop sell request submitted successfully, we will contact you shortly.","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    
    // QC Approval API
    public function UpdateQcApprovalAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "OtherID"=>$decode['OtherID'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GateINID"=>$decode['GateINID'],
                    "BookingID"=>$decode['BookingID'],
                    "CustomerType"=>$decode['CustType'],
                    "TType"=>$decode['TType'],
                    "Status"=>$decode['Status'],
                );
                $response = $this->UpdateQcApproval($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateQcApproval($params=FALSE)
    {
        $GateINID = $params['GateINID'];
        $BookingID = $params['BookingID'];
        $AccountID = $params['phonenumber'];
        $OtherID = $params['OtherID'];
        $CustomerType = $params['CustomerType']; 
        $Status = $params['Status']; 
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){ 
            
            $this->db->where('Gate_in_ID', $GateINID);
            $this->db->where('BookingID', $BookingID);
            //$this->db->where('AccountID', $AccountID);
            if($CustomerType == "1"){
                $this->db->set('QCApprove', $Status);
                $this->db->set('IsQcUpdate', $Status);
                $this->db->set('IsHoUpdate', $Status);
            }else{
                $this->db->set('IsHoUpdate', $Status);
            }
            if($this->db->update(db_prefix().'GateMaster')){
                // Check inward data is GIC data or Pc Soft data 
                $CheckInward = $this->ChecknumberMapping($GateINID);
                if($Status == "Y"){
                    $label = "Approved";
                }else{
                    $label = "Rejected";
                }
                $title = "QC ".$label;
                $screen = "1";
                if($CustomerType == "2"){
                    // generate debit note
                    if($params['TType'] == 'P'){
                        if($CheckInward){
                            // send qc approve status to PCsoft
                            $this->send_qc_status_to_pcsoft($CheckInward->pcsoft_doc_ref, $GateINID,$Status);
                        }
                    }
                    // Send Notification to Broker
                    $to = $checkLoginTokan->fcm_token;
                    $AccountDetails = $this->GetSingleAccountDetails($OtherID);
                    $body = "QC ".$label." against BookingID : ".$BookingID .' / '.$AccountDetails->company;
                    $this->send_notification($title,$screen,$body,$BookingID,$to);
                    
                    // send notification to trader
                    $to = $AccountDetails->fcm_token;
                    $body = "QC ".$label." against BookingID : ".$BookingID .' by '.$checkLoginTokan->company;
                    $this->send_notification($title,$screen,$body,$BookingID,$to);
                }else if($CustomerType == "3"){
                    // generate debit note
                    if($params['TType'] == 'P'){
                        if($CheckInward){
                            // send qc approve status to PCsoft
                            $this->send_qc_status_to_pcsoft($CheckInward->pcsoft_doc_ref, $GateINID,$Status);
                        }
                    }
                    // send notification to Trader
                    $to = $checkLoginTokan->fcm_token;
                    $body = "QC ".$label." against BookingID : ".$BookingID;
                    $this->send_notification($title,$screen,$body,$BookingID,$to);
                    
                    // Send Notification to Broker
                    
                    $AccountDetails = $this->GetSingleAccountDetails($OtherID);
                    $to = $AccountDetails->fcm_token;
                    $body = "QC ".$label." against BookingID : ".$BookingID .' / '.$checkLoginTokan->company;
                    $this->send_notification($title,$screen,$body,$BookingID,$to);
                    
                }else{
                    //Update final Rate in history table after qc approve
                    if($params['TType'] == 'P'){
                        if($CheckInward){
                            // send qc approve status to PCsoft
                            $this->send_qc_status_to_pcsoft($CheckInward->pcsoft_doc_ref, $GateINID,$Status);
                        }else{
                            //$this->updateFinalQuantity($BookingID, $GateINID);
                        }
                    }
                    // send notification to farmer or other than trader and broker
                    $to = $checkLoginTokan->fcm_token;
                    $body = "QC ".$label." against BookingID : ".$BookingID;
                    $this->send_notification($title,$screen,$body,$BookingID,$to);
                }
                $response = array("status"=>true,"message"=>"Qc ".$label." successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>" QC not approved please try again","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    public function send_qc_status_to_pcsoft($pcsoft_doc_ref,$GateINID,$Status)
    {
        $Gatemaster_details = $this->GetMasterDetails($GateINID);
        $data_qc_array =  array(
            "cocd" => $Gatemaster_details->PartyID,
            "GRN" => $pcsoft_doc_ref,
            "GateINID"=>$GateINID,
            "status"=>$Status
        );
        $qc_data = json_encode($data_qc_array);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/send_qc_status_to_pcsoft", //  -> LIVE URL
            //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/send_qc_status_to_pcsoft", // -> DEV URL
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $qc_data,
            CURLOPT_HTTPHEADER => array(
                    "content-type: application/json",
                ),
            )
        );
        $response = curl_exec($curl);
    }
    
    // Add Cleaning details API
    public function AddCleaningDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Type"=>$decode['Type'],
                    "GateINID"=>$decode['GateINID'],
                    "number"=>$decode['number'],
                    "weight"=>$decode['weight'],
                );
                $response = $this->AddCleaningDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AddCleaningDetails($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        $Cleaning_data = array(
            "FY"=>$FY,
            "PlantID" => 1,
            "Type"=>$params['Type'],
            "GateINID"=>$params['GateINID'],
            "number"=>$params['number'],
            "weight"=>$params['weight'],
            "UserID"=>$params['phonenumber'],
            "TransDate"=> date('Y-m-d H:i:s'),
        );
        
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'cleaning_details', $Cleaning_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $response = array("status"=>true,"message"=>"Bag Added successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetSingleAccountDetails($AccountID)
    {
        $this->db->select('tblclients.*');
        $this->db->where('AccountID', $AccountID);
        $Account_details = $this->db->get(db_prefix().'clients')->row();
        return $Account_details;
    }
    
    public function GetMasterDetails($GateINID)
    {
        $this->db->select('tblGateMaster.*');
        $this->db->where('Gate_in_ID', $GateINID);
        $gate_details = $this->db->get(db_prefix().'GateMaster')->row();
        return $gate_details;
    }
    
    public function ChecknumberMapping($GateID)
    {
        $this->db->select('tblpcsoft_gic_number_referance.*');
        $this->db->where('GIC_Reference', $GateID);
        $details = $this->db->get(db_prefix().'pcsoft_gic_number_referance')->row();
        return $details;
    }
    
    public function GetSingleAccountDetailsByPan($PAN)
    {
        $this->db->select('tblclients.AccountID,tblclients.ShortCode,tblclients.company,tblclients.phonenumber,tblcontacts.Pan');
        $this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID AND tblcontacts.PlantID = tblclients.PlantID');
        $this->db->where('tblcontacts.Pan', $PAN);
        $Account_details = $this->db->get(db_prefix().'clients')->row();
        return $Account_details;
    }
    
    public function GetItemDetailsByItemID($ItemID)
    {
        $this->db->select('tblitems.ItemID,tblitems.ItemName');
        $this->db->where('tblitems.ItemID', $ItemID);
        $Item_details = $this->db->get(db_prefix().'items')->row();
        return $Item_details;
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
       // return $response;
        
    }
    
    // Add Kirti Sale Request from PCSOFT 
    public function PunchSaleOrderAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode = json_decode($content,true);
                $trinvs = $decode['trinvs'];
                $data = array(
                    "PartyID"=>$decode['cocd'],
                    "party_pan"=>$trinvs['party_pan'],
                    "broker_pan"=>$trinvs['broker_pan'],
                    "booking_date"=>$trinvs['booking_date'],
                    "ShortCode"=>$trinvs['party_no'],
                    "doc_ref"=>$trinvs['doc_ref'],
                    "CenterID"=>$trinvs["im_loc"],
                    "ItemID"=>$decode['sporddtl']['IM_CODE'],
                    "quantity"=>$decode['sporddtl']['im_qty'],
                    "equantity"=>$decode['sporddtl']['im_qty'],
                    "basic_rate"=>$decode['sporddtl']['im_ordrate'],
                    "unit"=>"MT",
                    "access_tokan"=>$decode['access_tokan'],
                );
                $response = $this->PunchSaleOrder($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PunchSaleOrder($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        if($params['access_tokan'] != "fe3fd1f94239c467727c5cae504d4fdd"){
            $response = array("status"=>false,"message"=>"access token not matched please send valid access token");
        }elseif(empty($params['party_pan']) || is_null($params['party_pan'])){
            $response = array("status"=>false,"message"=>"please send party pan number and try again");
        }elseif(empty($params['doc_ref']) || is_null($params['doc_ref'])){
            $response = array("status"=>false,"message"=>"please send doc reference number and try again");
        }elseif(empty($params['CenterID']) || is_null($params['CenterID'])){
            $response = array("status"=>false,"message"=>"please send location ID and try again");
        }elseif(empty($params['ItemID']) || is_null($params['ItemID'])){
            $response = array("status"=>false,"message"=>"please send commodity code and try again");
        }elseif(!is_float($params['quantity'])){
            $response = array("status"=>false,"message"=>"please send atleast 1 quantity and try again");
        }elseif(!is_float($params['basic_rate'])){
            $response = array("status"=>false,"message"=>"please send basic rate and try again");
        }else{
            $ItemDetails = $this->GetItemDetailsByItemID($params['ItemID']);
            if($ItemDetails){
                $AccountDetails = $this->GetSingleAccountDetailsByPan($params['party_pan']);
                if($AccountDetails){
                    if($params['broker_pan']){
                        $BrokerDetails = $this->GetSingleAccountDetailsByPan($params['broker_pan']);
                    }
                    if($BrokerDetails){
                        $BrokerID = $BrokerDetails->AccountID;
                    }else{
                        $BrokerID = $AccountDetails->AccountID;
                    }
                
                    $pcsoft_doc_ref = $params['doc_ref'];
                    $new_Number = get_number($params['CenterID'],'P');
                    $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                    $bookingID = $params['CenterID'].'P'.date('d').date('m').date('y').$number;
                    $Cropsale_data = array(
                        "PlantID" => 1,
                        "FY"=>$FY,
                        "PartyID"=>trim($params['PartyID']),
                        "BookingID"=>$bookingID,
                        "TransDate"=>$params['booking_date'].' '.date("H:i:s"),
                        "TType"=> "S",
                        "TType2"=> "Sale",
                        "AccountID"=>$AccountDetails->AccountID,
                        "UserID"=>$AccountDetails->AccountID,
                        "BrokerID"=>$BrokerID,
                        "CenterID"=>$params['CenterID'],
                        "ItemID"=>$params['ItemID'],
                        "quantity"=>$params['quantity'],
                        "e_quantity"=>$params['quantity'],
                        "unit"=>$params['unit'],
                        "basic_rate"=>($params['basic_rate'])/10,
                        "IsApprove"=>"Y",
                        "ApproveTime"=>date('Y-m-d H:i:s'),
                        "ApproveUserID"=>$AccountDetails->AccountID,
                        "ClientApprove"=>"Y",
                        "BrokerApproveTime"=>date('Y-m-d H:i:s'),
                        "BrokerApprove"=>"Y",
                    );
                    
                    $this->db->insert(db_prefix().'lead_master', $Cropsale_data);
                    $insert_id = $this->db->insert_id();
                    if($insert_id){
                        $insert_referance = array(
                            "Type"=>"P",
                            "Name"=>"Trade",
                            "GIC_Reference"=>$bookingID,
                            "pcsoft_doc_ref"=>$pcsoft_doc_ref
                        );
                        $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                        $this->increment_center_wise_booking_number($params['CenterID'],'P');
                        $response = array("Status"=>true,"message"=>"Crop Buy request submitted successfully","referance_no"=>$bookingID,'party_shortcode'=>$AccountDetails->ShortCode);
                    }else{
                        $response = array("Status"=>false,"message"=>"Something Went Wrong please try again");
                    }
                }else{
                    $response = array("Status"=>false,"message"=>"Party not registered, please register on GIC Portal");
                }
            }else{
                
                $response = array("Status"=>false,"message"=>"Item not Mapped with GIC Portal Item Master please contact to GIC Team");
            }
        }
        return $response;
    }
    
//================ Add Kirti Sale Trade API ====================================
    public function AddKirtiSaleAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID'],
                    "quantity"=>$decode['quantity'],
                    "equantity"=>$decode['equantity'],
                    "basic_rate"=>$decode['basic_rate'],
                    "unit"=>$decode['unit'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType']
                );
                $response = $this->AddKirtiSale($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AddKirtiSale($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        
        $Cropsale_data = array(
            "FY"=>$FY,
            "PlantID" => 1,
            "CenterID"=>$params['CenterID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$params['quantity'],
            "e_quantity"=>$params['quantity'],
            "basic_rate"=>$params['basic_rate'],
            "unit"=>$params['unit'],
            "UserID"=>$params['phonenumber'],
            "TransDate"=> date('Y-m-d H:i:s'),
            "TType"=> "S",
            "TType2"=> "Sale"
        );
        // 1 - Farmer
        // 2 - Broker
        // 3 - Trader
        // 4 - Corporate
        if($params['UserType'] == "2"){
            $Cropsale_data['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $Cropsale_data['BrokerID'] = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $Cropsale_data['BrokerApprove'] = 'Y';
                $Cropsale_data['BrokerID'] = $params['phonenumber'];
                $Cropsale_data['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $Cropsale_data['BrokerApprove'] = 'NA';
                $Cropsale_data['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            $Cropsale_data['BrokerApprove'] = 'NA';
            $Cropsale_data['BrokerID'] = $params['OtherID'];
        }
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'lead_master', $Cropsale_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                // Get Company Purchase for 
                $PartyDetails = $this->GetPurchaseForParty($params['CenterID'],$params['ItemID']);
                if($PartyDetails){
                    $PartyID = $PartyDetails->PartyID;
                }else{
                    $PartyID = "KASPL";
                }
                
                $new_Number = get_number($params['CenterID'],'P');
                
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $params['CenterID'].'P'.date('d').date('m').date('y').$number;
            
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID,'PartyID'=>$PartyID]);
                $this->increment_center_wise_booking_number($params['CenterID'],'P');
                if($params['UserType'] == "1"){
                    $ids = array($AccountID);
                }else if($params['UserType'] == "3"){
                    $ids = array($AccountID,$params['OtherID']);
                }
                
                
                $title = "Trade Created";
                $screen = "1";
                $body = "Your BookingID : ".$bookingID.' Created';
                $booking_id = $bookingID;
                $to = $checkLoginTokan->fcm_token;
                
                if($checkLoginTokan->CustomerType == "1"){
                    // Farmer 
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    //$ids = array($AccountID);
                }else if($checkLoginTokan->CustomerType  == "3"){
                    // Send Notification to Trader
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Broker
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }else if($checkLoginTokan->CustomerType ){
                    // Send Notification to Broker
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Trader
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }
                $response = array("status"=>true,"message"=>"Crop Buy request submitted successfully, we will contact you shortly.","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    // Add EMD againest TradeID
    public function SubmitEMDAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID'],
                    "Amount"=>$decode['Amount']
                );
                $response = $this->SubmitEMD($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SubmitEMD($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        $insert = 0;
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            if($params['Amount'] == "0"){
                $this->db->set('EMDPaid', 'Y');
                $this->db->WHERE('BookingID', $params['BookingID']);
                $this->db->update(db_prefix() . 'lead_master');
                $response = array("status"=>true,"message"=>"EMD submitted successfully","login_tokan"=>$params['login_tokan']);
            }else{
                $leadMasterDetails = $this->GateControl_model->GetSingleBookingDataDB($params['BookingID']);
                $narration = 'Being Payment received / '.$checkLoginTokan->company.' against '.$params['BookingID'];
                $next_receipt_number = get_option2('next_receipts_number_for_kirti',$FY);
                $credit_data = array(
                    "FY"=>$FY,
                    "PlantID" => 1,
                    "VoucherID" => $next_receipt_number,
                    "Transdate"=>date('Y-m-d H:i:s'),
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "AccountID"=>$params['phonenumber'],
                    "CenterID"=>$params['CenterID'],
                    "CommodityID"=>$params['ItemID'],
                    "EntryFor"=>3,
                    "TType"=>"C",
                    "Amount"=>$params['Amount'],
                    "Narration"=>$narration,
                    "PassedFrom"=>"RECEIPTS",
                    "OrdinalNo"=>"1",
                    "UserID"=>$params['phonenumber']
                );
                if($this->db->insert(db_prefix().'accountledger', $credit_data)){
                    $insert++;
                }
                $debit_data = array(
                    "FY"=>$FY,
                    "PlantID" => 1,
                    "VoucherID" => $next_receipt_number,
                    "Transdate"=>date('Y-m-d H:i:s'),
                    "TransDate2"=>date('Y-m-d H:i:s'),
                    "AccountID"=>"CASH",
                    "CenterID"=>$params['CenterID'],
                    "CommodityID"=>$params['ItemID'],
                    "EntryFor"=> 3,
                    "TType"=>"D",
                    "Amount"=>$params['Amount'],
                    "Narration"=>$narration,
                    "PassedFrom"=>"RECEIPTS",
                    "OrdinalNo"=>"2",
                    "UserID"=>$params['phonenumber']
                );
                if($this->db->insert(db_prefix().'accountledger', $debit_data)){
                    $insert++;
                }
                if($insert > 0){
                    $this->increment_next_receipts_number();
                    $this->db->set('EMDPaid', 'Y');
                    $this->db->WHERE('BookingID', $params['BookingID']);
                    $this->db->update(db_prefix() . 'lead_master');
                    $response = array("status"=>true,"message"=>"EMD submitted successfully","login_tokan"=>$params['login_tokan']);
                }else{
                    $response = array("status"=>false,"message"=>"EMD not submitted please try again","login_tokan"=>$params['login_tokan']);
                }
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    // Get Trade details with account balance
    public function EMDDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID']
                );
                $response = $this->EMDDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function EMDDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $ledger = $this->GetLadgerByAccountID($params['phonenumber']);
            $AccountOpnBal = $this->GetAccountIDOpnBal($params['phonenumber']);
            $CR = 0;
            $DR = 0;
            foreach($ledger as $val){
                if($val["TType"] == "C"){
                    $CR += $val["TotalSum"];
                }else if($val["TType"] == "D"){
                    $DR += $val["TotalSum"];
                }
            }
            $balance = $AccountOpnBal->Opnbal - $CR + $DR;
            $this->db->select('tbllead_master.BookingID,tbllead_master.e_quantity AS TradeQtyMT,tbllead_master.basic_rate AS RateQtl,tblitems.ItemName,tbllead_master.ItemID,tblCenterMaster.CenterName,tblCenterMaster.CenterID,
            tblclients.company AS BrokerName');
            $this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tbllead_master.CenterID');
            $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.BrokerID');
            $this->db->where('tbllead_master.IsApprove',"Y");
            $this->db->where('tbllead_master.ClientApprove', 'Y');
            $this->db->where('tbllead_master.BrokerApprove', 'Y');
            $this->db->where('tbllead_master.BookingID', $params["BookingID"]);
            $TradeDetails = $this->db->get(db_prefix().'lead_master')->row();
            $TradeDetails->EMDPer = $checkLoginTokan->EMDP;
            $TradeDetails->Balance = number_format($balance, 2, '.', '');
            
            $response = array("status"=>true,"message"=>"Trade Details","TradeDetails"=>$TradeDetails);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    public function GetLadgerByAccountID($AccountID)
    {
        $this->db->select('SUM(tblaccountledger.Amount) AS TotalSum,tblaccountledger.TType');
        $this->db->where('tblaccountledger.AccountID', $AccountID);
        $this->db->group_by('tblaccountledger.TType');
        $TradeDetails = $this->db->get(db_prefix().'accountledger')->result_array();
        return $TradeDetails;
    }
    public function GetAccountIDOpnBal($AccountID)
    {
        $this->db->select('tblaccountbalances.BAL1 AS Opnbal');
        $this->db->where('tblaccountbalances.AccountID', $AccountID);
        $OpnBal = $this->db->get(db_prefix().'accountbalances')->row();
        return $OpnBal;
    }
    public function increment_next_receipts_number()
    {
        // Update next receipt number in settings
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        $this->db->where('name', 'next_receipts_number_for_kirti');
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
        
    }
    
    
    public function get_next_code($name)
    {
        $this->db->select('tbloptions.*');
        $this->db->where('name', $name);
        $number_details = $this->db->get(db_prefix().'options')->row();
        return $number_details;
    }
    
    public function increment_next_number($name)
    {
        // Update next number in settings
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('name', $name);
        $this->db->update(db_prefix() . 'options');
    }
    
    public function increment_next_number_sale($name,$fy)
    {
        // Update next number in settings
        
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('name', $name);
        $this->db->WHERE('FY', $fy);
        $this->db->update(db_prefix() . 'options');
    }
    public function GetRateFromRateMaster($CenterID,$ItemID)
    {
        $this->db->select('tblRateMaster.*');
        $this->db->where('CenterID', $CenterID);
        $this->db->where('ItemID', $ItemID);
        $this->db->where('KeyID', "C01");
        $this->db->where('IsActive', "Y");
        $Rate_details = $this->db->get(db_prefix().'RateMaster')->row();
        return $Rate_details;
    }
    public function GetPurchaseForParty($CenterID,$ItemID)
    {
        $this->db->select('tblCommisionMatrix.*');
        $this->db->where('CenterID', $CenterID);
        $this->db->where('ItemID', $ItemID);
        $this->db->where('IsOn', "Y");
        $this->db->where('IsActive', "Y");
        $Partydetails = $this->db->get(db_prefix().'CommisionMatrix')->row();
        return $Partydetails;
    }
//================== Increment Center Wise Trade Number ========================   
    public function increment_center_wise_booking_number($CenterID,$TType)
    {
        $this->db->set('Number', 'Number+1', false);
        $this->db->WHERE('CenterID', $CenterID);
        $this->db->WHERE('TType', $TType);
        $this->db->update(db_prefix() . 'numberformat');
    }
    
    
    // Get My Center List 
    public function GetMyCenterAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetMyCenterList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetMyCenterList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $AccountType = $checkLoginTokan["CustomerType"];
            //$CityIDs = array();
            if($AccountType == "1"){
                $regionID = $checkLoginTokan["regionID"];
                $GetRegionDetails = $this->GetRegionDetails($regionID);
                $City = $GetRegionDetails['city'];
                $CityIDs = explode(',', $City);
            }else if($AccountType == "2" || $AccountType == "3" || $AccountType == "4"){
                $ClusterID = $checkLoginTokan["ClusterID"];
                $GetClusterDetails = $this->GetClusterDetails($ClusterID);
                $City = $GetClusterDetails['city'];
                $CityIDs = explode(',', $City);
            }
            $this->db->select('tblCenterMaster.*');
            if (count($CityIDs) > 2) {
                $this->db->where_in('city',$CityIDs);
            }
            $this->db->order_by('tblCenterMaster.CenterName','ASC');
            $CenterList = $this->db->get(db_prefix().'CenterMaster')->result_array();
            $response = array("status"=>true,"message"=>"Center List","CenterList"=>$CenterList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Region Details
    public function GetRegionDetails($RegionID)
    {
        $this->db->select('tblRegion.*');
        $this->db->where('id',$RegionID);
        $regionDetails = $this->db->get(db_prefix().'Region')->row_array();
        return $regionDetails;
    }
    // Get Cluster Details
    public function GetClusterDetails($ClusterID)
    {
        $this->db->select('tblCluster.*');
        $this->db->where('id',$ClusterID);
        $ClusterDetails = $this->db->get(db_prefix().'Cluster')->row_array();
        return $ClusterDetails;
    }
//===================== Get Dashboard ItemList with rate =======================
    public function ItemListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetItemList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetItemList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            if($checkLoginTokan->CustomerType == "1"){
                $type = 'F';
            }else{
                $type = 'T';
            }
            $lang = load_client_language($params['phonenumber']);
            $CenterID = array("HO","AKOLA","BJP001","BID001","BAIL001","GUL001");
            $this->db->select('tblRateMaster.id,tblRateMaster.Rate,tblRateMaster.TransDate,tblRateMaster.CenterID,tblCenterMaster.CenterName,tblitems.ItemID,tblitems.ItemName,tblitems.subgroup_id,tblitems.hsn_code,
            tblitems_sub_groups.name AS GroupName,tblitems_sub_groups.item_image');
            $this->db->where('tblRateMaster.IsActive', 'Y');
            $this->db->where('tblitems.isactive', 'Y');
            $this->db->where('tblRateMaster.KeyID', 'C01');
            $this->db->where('tblRateMaster.Type', $type);
            //$this->db->where_in('tblRateMaster.CenterID', "HO");
            $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
            $this->db->join('tblRateMaster', 'tblRateMaster.ItemID = tblitems.ItemID');
            $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.CenterID = tblRateMaster.CenterID AND tblCenter_wise_item.ItemID = tblRateMaster.ItemID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
            
            if($checkLoginTokan->CustomerType == "1"){
                //$this->db->where('tblCenterMaster.CenterType', "M");
                $this->db->where('tblCenter_wise_item.TradeOnOffFarmer', "Y");
            }else{
                //$this->db->where('tblCenterMaster.CenterType', "F");
                $this->db->where('tblCenter_wise_item.TradeOnOff', "Y");
            }
            $this->db->order_by('tblRateMaster.Rate,tblRateMaster.ItemID',"DESC");
            $ItemList = $this->db->get(db_prefix().'items')->result_array();
            $i = 0;
            foreach($ItemList as $Key=>$val){
                $ItemName = _l(strtoupper($val["ItemName"]));
                $ItemList[$i]['ItemName'] = $ItemName;
                $i++;
            }
            $response = array("status"=>true,"message"=>"Item List","ItemList"=>$ItemList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    
//===================== Get Dashboard ItemList with rate =======================
    public function SalePurchaseItemListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ListType"=>$decode['ListType']
                );
                $response = $this->SalePurchaseItemList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SalePurchaseItemList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            if($params['ListType'] == "P"){
                if($checkLoginTokan->CustomerType == "1"){
                    $type = 'F';
                }else{
                    $type = 'T';
                }
                $this->db->select('tblRateMaster.id,tblRateMaster.Rate,tblRateMaster.TransDate,tblRateMaster.CenterID,tblCenterMaster.CenterName,tblitems.ItemID,tblitems.ItemName,tblitems.subgroup_id,tblitems.base_value AS hsn_code,
                tblitems_sub_groups.name AS GroupName,tblitems_sub_groups.item_image');
                $this->db->where('tblRateMaster.IsActive', 'Y');
                $this->db->where('tblitems.isactive', 'Y');
                $this->db->where('tblRateMaster.KeyID', 'C01');
                $this->db->where('tblRateMaster.Type', $type);
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                $this->db->join('tblRateMaster', 'tblRateMaster.ItemID = tblitems.ItemID');
                $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.CenterID = tblRateMaster.CenterID AND tblCenter_wise_item.ItemID = tblRateMaster.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer', "Y");
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff', "Y");
                }
                $this->db->order_by('tblRateMaster.Rate,tblRateMaster.ItemID',"DESC");
                $ItemList = $this->db->get(db_prefix().'items')->result_array();
            }elseif($params['ListType'] == "S"){
                $this->db->select('tblSaleRateMaster.id,tblSaleRateMaster.Rate,tblSaleRateMaster.TransDate,tblSaleRateMaster.CenterID,tblCenterMaster.CenterName,tblitems.ItemID,tblitems.ItemName,tblitems.subgroup_id,tblitems.base_value AS hsn_code,
                tblitems_sub_groups.name AS GroupName,tblitems_sub_groups.item_image');
                $this->db->where('tblSaleRateMaster.IsActive', 'Y');
                $this->db->where('tblitems.isactive', 'Y');
                $this->db->where('tblSaleRateMaster.KeyID', 'C01');
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                $this->db->join('tblSaleRateMaster', 'tblSaleRateMaster.ItemID = tblitems.ItemID');
                $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.CenterID = tblSaleRateMaster.CenterID AND tblCenter_wise_item.ItemID = tblSaleRateMaster.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff', "Y");
                $this->db->order_by('tblSaleRateMaster.Rate,tblSaleRateMaster.ItemID',"DESC");
                $ItemList = $this->db->get(db_prefix().'items')->result_array();
            }else{
                $ItemList = array();
            }
            $i = 0;
            foreach($ItemList as $Key=>$val){
                $ItemName = _l(strtoupper($val["ItemName"]));
                $ItemList[$i]['ItemName'] = $ItemName;
                $i++;
            }
            $response = array("status"=>true,"message"=>"Item List","ItemList"=>$ItemList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
//==================== Check Aadhar Exist ======================================
    public function AadharCheckAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Aadhar_number"=>$decode['Aadhar_number'],
                );
                $response = $this->AadharCheck($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AadharCheck($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblcontacts.*');
            $this->db->where('tblcontacts.aadhaar_number',$params['Aadhar_number']);
            $AadharDetails = $this->db->get(db_prefix().'contacts')->result_array();
            if($AadharDetails){
                $response = array("status"=>false,"message"=>"This Aadhaar number is already registered.");
            }else{
                $response = array("status"=>true,"message"=>"New Aadhaar","AccountDetails"=>$AadharDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//============================= Check PAN Exist ================================
    public function PANCheckAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Pan"=>$decode['Pan'],
                );
                $response = $this->PanCheck($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PanCheck($params=FALSE)
    {   
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblcontacts.*');
            //$this->db->where('tblcontacts.AccountID',$params['phonenumber']);
            $this->db->where('tblcontacts.Pan',$params['Pan']);
            $PanDetails = $this->db->get(db_prefix().'contacts')->result_array();
            if($PanDetails){
                $response = array("status"=>false,"message"=>"This Pan number is already registered.");
            }else{
                $response = array("status"=>true,"message"=>"New Pan","AccountDetails"=>$PanDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========================== Set KYC Status update =============================
    public function KYCStatusAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "KYCStatus"=>$decode['KYCStatus'],
                );
                $response = $this->KYCStatus_update($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function KYCStatus_update($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'clients', ["KYCStatus"=>$params['KYCStatus']]);
            if($this->db->affected_rows() > 0){ 
                $response = array("status"=>true,"message"=>"KYC Status Updated.",'login_token'=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something went wrong","login_token"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//=================== Dashboard Details ========================================
    public function DashboardAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->DashboardDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function DashboardDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            // KYC Status
            $this->db->select('tblclients.KYCStatus,tblclients.ShortCode');
            $this->db->where('tblclients.AccountID',$params['phonenumber']);
            $KYCDetails = $this->db->get(db_prefix().'clients')->result_array();
            
            
            // All Trade Count
            $this->db->select('COUNT(tbllead_master.id) As AllTradeCount');
            if($checkLoginTokan->CustomerType == "2"){
                $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
            }else{
                $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
            }
            
            $TradeCount = $this->db->get(db_prefix().'lead_master')->result_array();
            
            // All Accepted Trade Count
            $this->db->select('COUNT(tbllead_master.id) As TotalTransactionCount');
            if($checkLoginTokan->CustomerType == "2"){
                $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
            }else{
                $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
            }
            $this->db->where('tbllead_master.IsApprove','Y');
            $TransactionCount = $this->db->get(db_prefix().'lead_master')->result_array();
            
            $data = array(
                "KYCStatus" =>$KYCDetails[0]['KYCStatus'],
                "ShortCode" =>$KYCDetails[0]['ShortCode'],
                "CropSellCount" =>0, // temp delete to next update
                "CropDepositCount" =>0, // temp delete to next update
                "CropWithdrawCount" =>0, // temp delete to next update
                "TotalTransactionCount" =>$TransactionCount[0]['TotalTransactionCount'],
                "AllTradeCount" =>$TradeCount[0]['AllTradeCount']
            );
            $response = array("status"=>true,"message"=>"Dashboard Details",'login_token'=>$params['login_tokan'],"Details"=>$data);
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//===================== Get Active Center List =================================
    public function AllActiveCenterListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->AllActiveCenterList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AllActiveCenterList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            $this->db->select('tblCenterMaster.*');
            $this->db->where('tblCenterMaster.status','Y');
            $ActiveCenterList = $this->db->get(db_prefix().'CenterMaster')->result_array();
            $i = 0;
            foreach($ActiveCenterList as $key =>$val){
            	$ActiveCenterList[$i]['CenterName'] = _l(strtoupper($val["CenterName"]));
            	$i++;
            }
            $response = array("status"=>true,"message"=>"Active Center List","ActiveCenterList"=>$ActiveCenterList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//=============== Get Active Warehouse List =======================================
    public function WHListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->GetWHList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetWHList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            $type = array('Silo','Tank');
            $this->db->select('tblwarehouse.*');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblwarehouse.center');
            $this->db->where('tblCenterMaster.status','Y');
            $this->db->where('tblCenterMaster.CenterID',$params['CenterID']);
            //$this->db->where_not_in('structure',$type);
            $WHList = $this->db->get(db_prefix().'warehouse')->result_array();
            $i = 0;
            foreach($WHList as $key=>$val){
                $WHList[$i]["w_name"] = _l(strtoupper($val["w_name"]));
                $i++;
            }
            $response = array("status"=>true,"message"=>"WH List","WHList"=>$WHList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//===================== Warehouse wise Item List ===============================
    public function WHWiseItemsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->WHWiseItems($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function WHWiseItems($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            $this->db->select('tblCenter_wise_item.ItemID,tblitems.ItemName');
            $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
            $this->db->where('tblCenter_wise_item.CenterID',$params['CenterID']);
            $CenterWiseItem = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            $i = 0;
            foreach($CenterWiseItem as $key=>$val){
                $CenterWiseItem[$i]['ItemName'] = _l(strtoupper($val["ItemName"]));
                $i++;
            }
            $response = array("status"=>true,"message"=>"Center Wise Item","CenterWiseItem"=>$CenterWiseItem);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//=================== Add Book WH Request ======================================
    public function BookWHAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "WHID"=>$decode['WHID'],
                    "ItemID"=>$decode['ItemID'],
                    "Quantity"=>$decode['Quantity'],
                    "Unit"=>$decode['Unit'],
                    "TransDate"=>$decode['TransDate'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType'],
                    
                );
                $response = $this->BookWh($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function BookWh($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        $PlantID = 1;
        $WHBook_data = array(
            "AccountID"=>$params['phonenumber'],
            "BrokerID"=>$params['phonenumber'],
            "FY"=>$FY,
            "PlantID"=>$PlantID,
            "WHID"=>$params['WHID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$params['Quantity'],
            "unit"=>$params['Unit'],
            "UserID"=>$params['phonenumber'],
            "PartyID"=>"KASPL",
            "TransDate"=>$params['TransDate'].' '.date('H:i:s'),
            "TType"=> "D",
            "TType2"=> "Deposit",
            "BrokerApprove"=>"Y"
        );
        
        if($params['UserType'] == "2"){
            $WHBook_data['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $WHBook_data['BrokerID'] = $params['phonenumber'];
            $WHBook_data['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $WHBook_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $WHBook_data['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $WHBook_data['BrokerApprove'] = 'Y';
                $WHBook_data['BrokerID'] = $params['phonenumber'];
                $WHBook_data['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $WHBook_data['BrokerApprove'] = 'NA';
                $WHBook_data['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $WHBook_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $WHBook_data['AccountID'] = $AccountID;
            if($params['OtherID']){
                $WHBook_data['BrokerApprove'] = 'NA';
                $WHBook_data['BrokerID'] = $params['OtherID'];
            }else{
                $WHBook_data['BrokerApprove'] = 'Y';
                $WHBook_data['BrokerID'] = $AccountID;
            }
            
        }
        
        $this->db->where('AccountID',$params['WHID']);
        $WhDetails = $this->db->get('tblwarehouse')->row();
        
        $CenterID = $WhDetails->center;
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan->CustomerType == "1"){
            $this->db->where('tblRateMaster.Type',"F");
        }else{
            $this->db->where('tblRateMaster.Type',"T");
        }
        $this->db->where('tblRateMaster.KeyID',"C01");
        $this->db->where('tblRateMaster.IsActive','Y');
        $this->db->where('tblRateMaster.ItemID',$params['ItemID']);
        $this->db->where('tblRateMaster.CenterID',$CenterID);
        $basicRateDetails = $this->db->get(db_prefix().'RateMaster')->row();
        
        if($checkLoginTokan){
            $WHBook_data['CenterID'] = $CenterID;
            $WHBook_data['basic_rate'] = $basicRateDetails->Rate;
            $this->db->insert(db_prefix().'lead_master', $WHBook_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $new_Number = get_number($CenterID,'D');
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $CenterID.'D'.date('d').date('m').date('y').$number;
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID]);
                $this->increment_center_wise_booking_number($CenterID,'D');
                
                $response = array("status"=>true,"message"=>"WH booking request submitted successfully, we will contact you shortly.","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
//===================== Get All Trade List by Type =============================
    public function BookingListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "TType"=>$decode['TType'],
                    "CustType"=>$decode['CustType'],
                    "status"=>$decode['status'],
                );
                $response = $this->GetBookingList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetBookingList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            $this->db->select('tbllead_master.*,tblwarehouse.w_name,tblitems.ItemName,tblclients.company AS PartyName,TBLBROKER.company AS BrokerName');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tbllead_master.WHID','LEFT');
            $this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');
            $this->db->join('tblclients AS TBLBROKER', 'TBLBROKER.AccountID = tbllead_master.BrokerID','LEFT');
            if($params['CustType'] == '2'){
                $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
            }else{
                $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
            }
            $this->db->where('tbllead_master.TType',$params['TType']);
            if(isset($params['status']) && !empty($params['status'])) {
                if($params['status'] == 'Y') {
                    $status_condition = '(tbllead_master.IsApprove = "' . $params['status'] . '" and tbllead_master.ClientApprove = "' . $params['status'] . '" and tbllead_master.BrokerApprove = "' . $params['status'] . '")';
                } else {
                    $status_condition = '(tbllead_master.IsApprove = "' . $params['status'] . '" or tbllead_master.ClientApprove = "' . $params['status'] . '" or tbllead_master.BrokerApprove = "' . $params['status'] . '")';
                }
                $this->db->where($status_condition);
            }
            $this->db->order_by('tbllead_master.TransDate',"DESC");
            $BookList = $this->db->get(db_prefix().'lead_master')->result_array();
            $i = 0;
            foreach($BookList as $key => $val){
                $BookList[$i]['LastActionName'] = _l($val["LastActionName"]);
                $BookList[$i]['ItemName'] = _l($val["ItemName"]);
                $i++;
            }
            $response = array("status"=>true,"message"=>"Booking List ","BookList"=>$BookList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Trader List Created By Broker i.e. Broker wise
    public function TraderCRTByBrokerListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->TraderCRTByBrokerList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function TraderCRTByBrokerList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblclients.AccountID,tblclients.company,tblclients.datecreated');
            $this->db->where('tblclients.UserID',$params['phonenumber']);
            $this->db->order_by('tblclients.datecreated',"DESC");
            $TraderList = $this->db->get(db_prefix().'clients')->result_array();
            $response = array("status"=>true,"message"=>"Trader List","TraderList"=>$TraderList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    // Get WH Booking List
    public function WHBookingListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetWHBookingList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetWHBookingList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.BookingID,tbllead_master.TransDate,tbllead_master.WHID,tblwarehouse.w_name,tbllead_master.ItemID,tblitems.ItemName,tbllead_master.quantity
            ,tbllead_master.unit,tbllead_master.IsApprove,tblGateMaster.Gate_in_ID,tblGateMaster.status,tblGateMaster.VehicleNo,tblGateMaster.Phone');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tbllead_master.WHID');
            //$this->db->join('tblfarm_details', 'tblfarm_details.id = tblBookWH.FarmID');
            $this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblGateMaster','tblGateMaster.BookingID = tbllead_master.BookingID AND tblGateMaster.AccountID = tbllead_master.AccountID');
            $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
            $WHBookList = $this->db->get(db_prefix().'lead_master')->result_array();
            $response = array("status"=>true,"message"=>"WH Booking List","WHBookList"=>$WHBookList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Center Wise Commodity
    public function CenterWiseCommodityAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->GetCenterWiseCommodity($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCenterWiseCommodity($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblRateMaster.*,tblitems.ItemName');
            $this->db->join('tblitems', 'tblitems.ItemID = tblRateMaster.ItemID');
            $this->db->where('tblRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblRateMaster.KeyID','C01');
            $this->db->where('tblRateMaster.IsActive', 'Y');
            $CommodityList = $this->db->get(db_prefix().'RateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Centerwise Commodity List","CommodutyList"=>$CommodityList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//================ Get Center Wise Commodity Group for Kirti Purchase ==========
    public function CenterWiseItemGroupAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->CenterWiseItemGroup($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CenterWiseItemGroup($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            if($params['CenterID'] == ""){
                $this->db->select('tblitems.subgroup_id AS id,tblitems_sub_groups.name,tblitems_sub_groups.item_image');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                $this->db->where('tblitems_sub_groups.main_group_id','3');
                $this->db->where('tblitems.isactive','Y');
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                $CommodityGroupList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }else{
                
                $this->db->select('tblitems.subgroup_id AS id,tblitems_sub_groups.name,tblitems_sub_groups.item_image');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                $this->db->where('tblitems.isactive','Y');
                $this->db->where('tblCenter_wise_item.CenterID',$params['CenterID']);
                $this->db->where('tblitems_sub_groups.main_group_id','3');
                $CommodityGroupList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }
            $i = 0;
            foreach($CommodityGroupList as $key =>$val){
                $CommodityGroupList[$i]['name'] = _l(strtoupper($val['name']));
                $i++;
            }
            
            $response = array("status"=>true,"message"=>"Centerwise Commodity  Group List","CommodutyGroupList"=>$CommodityGroupList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//============== Get Center Wise OR All Commodity Group Sale ===================
    public function CenterWiseItemGroupSaleAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->CenterWiseItemGroupSale($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CenterWiseItemGroupSale($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            if($params['CenterID'] == ""){
                $this->db->select('tblitems.subgroup_id AS id,tblitems_sub_groups.name,tblitems_sub_groups.item_image');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblitems_sub_groups.main_group_id','3');
                $this->db->where('tblitems.isactive','Y');
                $CommodityGroupList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }else{
                $this->db->select('tblitems.subgroup_id AS id,tblitems_sub_groups.name,tblitems_sub_groups.item_image');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblCenter_wise_item.CenterID',$params['CenterID']);
                $this->db->where('tblitems_sub_groups.main_group_id','3');
                $this->db->where('tblitems.isactive','Y');
                $CommodityGroupList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }
            $i = 0;
            foreach($CommodityGroupList as $key =>$val){
                $CommodityGroupList[$i]['name'] = _l(strtoupper($val["name"]));
                $i++;
            }
            $response = array("status"=>true,"message"=>"Centerwise Commodity  Group List","CommodutyGroupList"=>$CommodityGroupList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Commodity Group wise Center Purchase
    public function ItemGroupWiseCenterAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID']
                );
                $response = $this->ItemGroupWiseCenter($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ItemGroupWiseCenter($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            if($params['GroupID'] == ""){
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
                $this->db->distinct();
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
                
            }else{
                
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                $this->db->where('tblitems.subgroup_id',$params['GroupID']);
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();   
            }
            $new_array = array();
            $i = 0;
            foreach($CenterList as $val){
                $centerName = _l(strtoupper($val["CenterName"]));
                $new_array[$i]['CenterID'] = $val["CenterID"];
                $new_array[$i]['CenterName'] = $centerName;
                $i++;
            }
            
            $response = array("status"=>true,"message"=>"Commodity Group wise Center List","CenterList"=>$new_array);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//====== Get Center List Against GroupID and CenetrType Region Wise ============
    public function ItemGroupWiseCenterUpdatedAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID'],
                    "CenterType"=>$decode['CenterType']
                );
                $response = $this->ItemGroupWiseCenterUpdated($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ItemGroupWiseCenterUpdated($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblRegion.AccountID AS RegionID,tblRegion.region AS RegionName');
            $RegionList = $this->db->get(db_prefix().'Region')->result_array();
            
            $lang = load_client_language($params['phonenumber']);
            if($params['GroupID'] == ""){
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName,tblCenterMaster.RegionID,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude');
                $this->db->distinct();
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                if($params['CenterType'] !=""){
                    $this->db->where('tblCenterMaster.CenterType',$params['CenterType']);
                }
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
                
            }else{
                
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName,tblCenterMaster.RegionID,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                $this->db->where('tblitems.subgroup_id',$params['GroupID']);
                if($checkLoginTokan->CustomerType == "1"){
                    $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
                }else{
                    $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
                }
                if($params['CenterType'] !=""){
                    $this->db->where('tblCenterMaster.CenterType',$params['CenterType']);
                }
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();   
            }
            
            $rr = 0;
            foreach($RegionList as $Rkey=>$Rval){
                $new_array = array();
                $i = 0;
                foreach($CenterList as $val){
                    if($val["RegionID"] == $Rval["RegionID"]){
                        $centerName = _l(strtoupper($val["CenterName"]));
                        $new_array[$i]['CenterID'] = $val["CenterID"];
                        $new_array[$i]['CenterName'] = $centerName;
                        $new_array[$i]['Address'] = $val["address"];
                        $new_array[$i]['longitude'] = $val["longitude"];
                        $new_array[$i]['latitude'] = $val["latitude"];
                        $i++;
                    }
                }
                
                $RegionList[$rr]["CenterList"] = $new_array;
                $RegionList[$rr]["RegionName"] = _l(strtoupper($Rval["RegionName"]));
                $rr++;
            }
            
            $response = array("status"=>true,"message"=>"Commodity Group wise Center List","RegionWiseCenterList"=>$RegionList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Commodity Group wise Center Purchase
    public function ItemGroupWiseCenterSaleAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID']
                );
                $response = $this->ItemGroupWiseCenterSale($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ItemGroupWiseCenterSale($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            if($params['GroupID'] == ""){
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
                $this->db->distinct();
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
                
            }else{
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblCenterMaster.status','Y');
                $this->db->where('tblitems.subgroup_id',$params['GroupID']);
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }
            $i = 0;
            foreach($CenterList as $key =>$val){
                $CenterList[$i]['CenterName'] = _l(strtoupper($val["CenterName"]));
                $i++;
            }
            
            $response = array("status"=>true,"message"=>"Commodity Group wise Center List","CenterList"=>$CenterList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//========== Get Commodity Group wise Center For Sale ==========================
    public function ItemGroupWiseCenterSaleUpdatedAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID'],
                    "CenterType"=>$decode['CenterType']
                );
                $response = $this->ItemGroupWiseCenterSaleUpdated($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ItemGroupWiseCenterSaleUpdated($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblRegion.AccountID AS RegionID,tblRegion.region AS RegionName');
            $RegionList = $this->db->get(db_prefix().'Region')->result_array();
            $lang = load_client_language($params['phonenumber']);
            if($params['GroupID'] == ""){
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName,tblCenterMaster.RegionID,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude');
                $this->db->distinct();
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                if($params['CenterType'] !=""){
                    $this->db->where('tblCenterMaster.CenterType',$params['CenterType']);
                }
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblCenterMaster.status','Y');
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }else{
                $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName,tblCenterMaster.RegionID,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude');
                $this->db->distinct();
                $this->db->join('tblitems', 'tblitems.ItemID = tblCenter_wise_item.ItemID');
                $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblCenter_wise_item.CenterID');
                if($params['CenterType'] !=""){
                    $this->db->where('tblCenterMaster.CenterType',$params['CenterType']);
                }
                $this->db->where('tblCenter_wise_item.SaleTradeOnOff','Y');
                $this->db->where('tblCenterMaster.status','Y');
                $this->db->where('tblitems.subgroup_id',$params['GroupID']);
                $CenterList = $this->db->get(db_prefix().'Center_wise_item')->result_array();
            }
            $i = 0;
            foreach($CenterList as $key =>$val){
                $CenterList[$i]['CenterName'] = _l(strtoupper($val["CenterName"]));
                $i++;
            }
            $rr = 0;
            foreach($RegionList as $Rkey=>$Rval){
                $new_array = array();
                $i = 0;
                foreach($CenterList as $val){
                    if($val["RegionID"] == $Rval["RegionID"]){
                        $centerName = _l(strtoupper($val["CenterName"]));
                        $new_array[$i]['CenterID'] = $val["CenterID"];
                        $new_array[$i]['CenterName'] = $centerName;
                        $new_array[$i]['Address'] = $val["address"];
                        $new_array[$i]['longitude'] = $val["longitude"];
                        $new_array[$i]['latitude'] = $val["latitude"];
                        $i++;
                    }
                }
                $RegionList[$rr]["CenterList"] = $new_array;
                $RegionList[$rr]["RegionName"] = _l(strtoupper($Rval["RegionName"]));
                $rr++;
            }
            
            $response = array("status"=>true,"message"=>"Commodity Group wise Center List","RegionWiseCenterList"=>$RegionList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========= Get Commodity Group and Center wise Rate For Purchase ==============
    public function CenterItemGroupWiserateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID'],
                    "CenterID"=>$decode['CenterID'],
                    "Type"=>$decode['Type']
                );
                $response = $this->CenterItemGroupWiserate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CenterItemGroupWiserate($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            if(isset($params['Type']) || $params['Type'] != '' || $params['Type'] != NULL){
                $where = 'AND tblwishlist.Type = "' . $params['Type'] . '"';
            }else{
                $where = 'AND tblwishlist.Type = "P"';
            }
            
            $this->db->select('tblRateMaster.*,tblitems.base_value,tblCenter_wise_item.TradeOnOff,tblCenterMaster.trade_condition,
            tblwishlist.Type AS wishlist');
            $this->db->where('tblRateMaster.IsActive','Y');
            $this->db->where('tblRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblRateMaster.KeyID',"C01");
            if($checkLoginTokan->CustomerType == "1"){
                $this->db->where('tblRateMaster.Type',"F");
                $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
            }else{
                $this->db->where('tblRateMaster.Type',"T");
                $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
            }
            $this->db->where('tblitems.subgroup_id',$params['GroupID']);
            $this->db->where('tblitems.isactive',"Y");
            $this->db->join('tblwishlist', 'tblwishlist.ItemID = tblRateMaster.ItemID AND tblwishlist.CenterID = tblRateMaster.CenterID AND tblwishlist.AccountID = "'.$params['phonenumber'].'" '. $where . ' ',"LEFT");
            $this->db->join('tblitems', 'tblitems.ItemID = tblRateMaster.ItemID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblRateMaster.CenterID');
            $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.ItemID = tblRateMaster.ItemID AND tblCenter_wise_item.CenterID = tblRateMaster.CenterID');
            
            $RateList = $this->db->get(db_prefix().'RateMaster')->result_array();
            
            $response = array("status"=>true,"message"=>"Commodity Group wise Center wise rate List","RateList"=>$RateList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========== Get Commodity Group and Center wise Rate For Sale =================
    public function CenterItemGroupWiseSalerateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "GroupID"=>$decode['GroupID'],
                    "CenterID"=>$decode['CenterID']
                );
                $response = $this->CenterItemGroupWiseSalerate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CenterItemGroupWiseSalerate($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $this->db->select('tblSaleRateMaster.*,tblitems.base_value,tblCenter_wise_item.TradeOnOff,tblCenterMaster.trade_condition');
            $this->db->where('tblSaleRateMaster.IsActive','Y');
            $this->db->where('tblSaleRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblSaleRateMaster.KeyID',"C01");
            $this->db->where('tblitems.subgroup_id',$params['GroupID']);
            $this->db->where('tblitems.isactive',"Y");
            $this->db->join('tblitems', 'tblitems.ItemID = tblSaleRateMaster.ItemID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblSaleRateMaster.CenterID');
            $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.ItemID = tblSaleRateMaster.ItemID AND tblCenter_wise_item.CenterID = tblSaleRateMaster.CenterID');
            $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
            $RateList = $this->db->get(db_prefix().'SaleRateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Commodity Group wise Center wise rate List","RateList"=>$RateList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//========== Get Rate Center Wise Item Wise For Kirti Purchase =================
    public function GetRateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID']
                );
                $response = $this->GetRateCenterWiseCommodatywise($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetRateCenterWiseCommodatywise($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblRateMaster.*,tblitems.ItemName,tblCompetitorMaster.Competitor,tblCenter_wise_item.TradeOnOff');
            $this->db->join('tblitems', 'tblitems.ItemID = tblRateMaster.ItemID');
            $this->db->join('tblCompetitorMaster', 'tblCompetitorMaster.CompetitorID = tblRateMaster.KeyID');
            $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.CenterID = tblRateMaster.CenterID AND tblCenter_wise_item.ItemID = tblRateMaster.ItemID');
            $this->db->where('tblRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblRateMaster.ItemID',$params['ItemID']);
            $this->db->where('tblRateMaster.KeyID',"C01");
            $this->db->where('tblRateMaster.IsActive', 'Y');
            
            if($checkLoginTokan->CustomerType == "1"){
                $this->db->where('tblRateMaster.Type', 'F');
                $this->db->where('tblCenter_wise_item.TradeOnOffFarmer','Y');
            }else{
                $this->db->where('tblRateMaster.Type', 'T');
                $this->db->where('tblCenter_wise_item.TradeOnOff','Y');
            }
            $RateList = $this->db->get(db_prefix().'RateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Rate List","RateList"=>$RateList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Current Rate Center Wise Item Wise For Purchase  i.e. PcSoft
    public function GetCurrentRateAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "CustType"=>$decode['CustType'],
                    "access_tokan"=>$decode['access_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID']
                );
                $response = $this->GetCurrentRate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCurrentRate($params=FALSE)
    {
        $checkLoginTokan = $params['access_tokan'];
        $CustomerType = $params['CustType'];
        if($checkLoginTokan == "fe3fd1f94239c467727c5cae504d4fdd"){
            $this->db->select('tblRateMaster.*,tblitems.ItemName');
            $this->db->join('tblitems', 'tblitems.ItemID = tblRateMaster.ItemID');
            $this->db->where('tblRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblRateMaster.ItemID',$params['ItemID']);
            $this->db->where('tblRateMaster.KeyID',"C01");
            $this->db->where('tblRateMaster.IsActive', 'Y');
            if($CustomerType == "1"){
                $this->db->where('tblRateMaster.Type', 'F');
            }else{
                $this->db->where('tblRateMaster.Type', 'T');
            }
            $RateList = $this->db->get(db_prefix().'RateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Rate List","RateList"=>$RateList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//====== Get Current Rate Center Wise Item Wise For Sale =======================
    public function GetSaleRateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID']
                );
                $response = $this->GetSaleRateCenterWiseCommodatywise($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetSaleRateCenterWiseCommodatywise($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblSaleRateMaster.*,tblitems.ItemName,tblCompetitorMaster.Competitor,tblCenter_wise_item.TradeOnOff');
            $this->db->join('tblitems', 'tblitems.ItemID = tblSaleRateMaster.ItemID');
            $this->db->join('tblCompetitorMaster', 'tblCompetitorMaster.CompetitorID = tblSaleRateMaster.KeyID');
            $this->db->join('tblCenter_wise_item', 'tblCenter_wise_item.CenterID = tblSaleRateMaster.CenterID AND tblCenter_wise_item.ItemID = tblSaleRateMaster.ItemID');
            $this->db->where('tblSaleRateMaster.CenterID',$params['CenterID']);
            $this->db->where('tblSaleRateMaster.ItemID',$params['ItemID']);
            $this->db->where('tblSaleRateMaster.KeyID',"C01");
            $this->db->where('tblSaleRateMaster.IsActive', 'Y');
            $RateList = $this->db->get(db_prefix().'SaleRateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Rate List","RateList"=>$RateList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//=============== Get Trader List By Broker name ===============================
    public function GetTraderListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetTraderList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetTraderList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbltrader_broker_assigned.*,SendFrom.company AS SendFromName,SendTo.company AS SendToName,SendFrom.CustomerType AS SendFromType,SendTo.CustomerType AS SentToType');
            $this->db->join('tblclients AS SendFrom','SendFrom.AccountID = tbltrader_broker_assigned.send_from');
            $this->db->join('tblclients AS SendTo','SendTo.AccountID = tbltrader_broker_assigned.send_to');
            $block_condition = '(tbltrader_broker_assigned.block_status IS NULL or tbltrader_broker_assigned.block_status = "10" or tbltrader_broker_assigned.block_status = "12")';
            $this->db->where($block_condition);
            $condition = '(tbltrader_broker_assigned.send_from = "' . $params['phonenumber'] . '" or tbltrader_broker_assigned.send_to = "' . $params['phonenumber'] . '")';
            $this->db->where($condition);
            $TraderList = $this->db->get('tbltrader_broker_assigned')->result_array();
            $Traders = array();
            $i = 0;
            foreach($TraderList as $key=>$value){
                if($value["status"]=="Y" && $value["send_from"] == $params['phonenumber']){
                    $Traders[$i]['AccountID'] = $value["send_to"];
                    $Traders[$i]['company'] = $value["SendToName"];
                    $Traders[$i]['phonenumber'] = $value["send_to"];
                }elseif($value["status"]=="Y" && $value["send_to"] == $params['phonenumber']){
                    $Traders[$i]['AccountID'] = $value["send_from"];
                    $Traders[$i]['company'] = $value["SendFromName"];
                    $Traders[$i]['phonenumber'] = $value["send_from"];
                }
                $i++;
            }
            $response = array("status"=>true,"message"=>"Trader List","TraderList"=>$Traders);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//================= Get Broker List By Trader Name =============================
    public function GetBrokerListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetBrokerList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetBrokerList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbltrader_broker_assigned.*,SendFrom.company AS SendFromName,SendTo.company AS SendToName,SendFrom.CustomerType AS SendFromType,SendTo.CustomerType AS SentToType');
            $this->db->join('tblclients AS SendFrom','SendFrom.AccountID = tbltrader_broker_assigned.send_from');
            $this->db->join('tblclients AS SendTo','SendTo.AccountID = tbltrader_broker_assigned.send_to');
            $block_condition = '(tbltrader_broker_assigned.block_status IS NULL or tbltrader_broker_assigned.block_status = "10" or tbltrader_broker_assigned.block_status = "12")';
            $this->db->where($block_condition);
            $condition = '(tbltrader_broker_assigned.send_from = "' . $params['phonenumber'] . '" or tbltrader_broker_assigned.send_to = "' . $params['phonenumber'] . '")';
            $this->db->where($condition);
            // $this->db->where('tbltrader_broker_assigned.send_from',$params['phonenumber']);
            // $this->db->or_where('tbltrader_broker_assigned.send_to',$params['phonenumber']);
            $BrokerList = $this->db->get('tbltrader_broker_assigned')->result_array();
            $Brokers = array();
            $i = 0;
            foreach($BrokerList as $key=>$value){
                if($value["status"]=="Y" && $value["send_from"] == $params['phonenumber']){
                    $Brokers[$i]['AccountID'] = $value["send_to"];
                    $Brokers[$i]['company'] = $value["SendToName"];
                    $Brokers[$i]['phonenumber'] = $value["send_to"];
                }elseif($value["status"]=="Y" && $value["send_to"] == $params['phonenumber']){
                    $Brokers[$i]['AccountID'] = $value["send_from"];
                    $Brokers[$i]['company'] = $value["SendFromName"];
                    $Brokers[$i]['phonenumber'] = $value["send_from"];
                }
                $i++;
            }
            $response = array("status"=>true,"message"=>"Broker List","BrokerList"=>$Brokers,'');
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Deduction Matrix By ItemID
    public function GetDeductionMatrixAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID']
                );
                $response = $this->GetDeductionMatrix($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetDeductionMatrix($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblItemQCParameter.*,tblItemParameter.ItemParameterName');
            $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblItemQCParameter.ItemParameterID');
            $this->db->where('tblItemQCParameter.ItemID',$params['ItemID']);
            $QCParameterList = $this->db->get(db_prefix().'ItemQCParameter')->result_array();
            $i = 0;
            if($QCParameterList){
                foreach($QCParameterList as $key=>$value){
                    $this->db->select('tbldeduction_matrix.*');
                    $this->db->where('tbldeduction_matrix.ItemID',$value['ItemID']);
                    $this->db->where('tbldeduction_matrix.ItemParameterID',$value['ItemParameterID']);
                    $DeductioMatrix = $this->db->get(db_prefix().'deduction_matrix')->result_array();
                    $QCParameterList[$i]['DeductionMatrix'] = $DeductioMatrix;
                    $i++;
                }
                
            }
            $response = array("status"=>true,"message"=>"Deduction List","DeductionList"=>$QCParameterList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get MAC Address By CenterID
    public function GetMACAddressAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetMACAddress($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetMACAddress($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $StaffCenter = $checkLoginTokan['CenterID'];
            $this->db->select('tblCenterMaster.*');
            $this->db->where('tblCenterMaster.CenterID',$StaffCenter);
            $CenterDetails = $this->db->get(db_prefix().'CenterMaster')->row();
            if($CenterDetails){
                $MACAddress = $CenterDetails->mac_address;
                $MACAddress_array = explode(',', $MACAddress);
            }else{
                $MACAddress_array = array();
            }
            $response = array("status"=>true,"message"=>"Mac Address List","MACAddress"=>$MACAddress_array);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateWeighBridgeDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                // VhlTop Image 
                if($decode['VhlTopImage'])
                {
                    $image1 = base64_decode($decode['VhlTopImage']);
                    $image_name = 'VhlTopImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // VhlFront Image 
                if($decode['VhlFrontImage'])
                {
                    $image2 = base64_decode($decode['VhlFrontImage']);
                    $image_name = 'VhlFrontImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // VHLSide Image 
                if($decode['VHLSideImage'])
                {
                    $image3 = base64_decode($decode['VHLSideImage']);
                    $image_name = 'VHLSideImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
              
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "LoadedWeight"=>$decode['LoadedWeight'],
                    "SlipNo"=>$decode['SlipNo'],
                    "UserID"=>$decode['UserID'],
                    "VhlTopImage"=>$path1,
                    "VhlFrontImage"=>$path2,
                    "VHLSideImage"=>$path3
                );
                $response = $this->UpdateWeighBridgeDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateWeighBridgeDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $updateArray = array(
                "LoadedWeight"=>($params['LoadedWeight']) / 100,
                "VhlTopImage"=>$params['VhlTopImage'],
                "VhlFrontImage"=>$params['VhlFrontImage'],
                "VHLSideImage"=>$params['VHLSideImage'],
                "LWUserID"=>$params['UserID'],
                "LWTransDate"=>date('Y-m-d H:i:s'),
                "status"=>4,
            );
            if($params['SlipNo']){
                $updateArray["weigh_bridge_slip_no"] = $params['SlipNo'];
            }
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->update('tblGateMaster',$updateArray);
            if($this->db->affected_rows() > 0){
                $response = array("status"=>true,"message"=>"Weigh Bridge Details Updated","Details"=>$updateArray);
            }else{
                $response = array("status"=>false,"message"=>"something went wrong","Details"=>$updateArray);
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateSingleLayerAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                if($decode['id']){
                    $layer_id = $decode['id'];
                }else{
                    $layer_id = '';
                }
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "layer_number"=>$decode['layer_number'],
                    "qty"=>$decode['qty'],
                    "layer_id"=>$layer_id,
                    "unit"=>$decode['unit'],
                    "UserID" =>$decode['UserID'],
                );
                $response = $this->UpdateSingleLayerDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateSingleLayerDB($params=FALSE)
    {   
        
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            if($params['layer_id'] == ""){
                $data = array(
                    "BookingID"=>$params['BookingID'],
                    "Gate_in_ID"=>$params['Gate_in_ID'],
                    "layer_number"=>$params['layer_number'],
                    "qty"=>$params['qty'],
                    "unit"=>$params['unit'],
                    "UserID" =>$params['UserID'],
                    "TransDate" =>date('Y-m-d H:i:s')
                );
                $Layer = $this->db->insert('tblLayerMaster',$data);
                $inserted_id = $this->db->insert_id();
                $data['id'] = $inserted_id;
                $response = array("status"=>true,"message"=>"Layer Details Inserted","Layer"=>$data);
            }else{
                $data = array(
                    "qty"=>$params['qty'],
                    "unit"=>$params['unit'],
                );
                $this->db->where('id',$params['layer_id']);
                $this->db->update('tblLayerMaster',$data);
                $data['id'] = $params['layer_id'];
                $response = array("status"=>true,"message"=>"Layer Details Updated","Layer"=>$data);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateUnloadingDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "total_bags"=>$decode['total_bags'],
                    "total_katta"=>$decode['total_katta'],
                    "total_layers"=>$decode['total_layers'],
                    "bags_collection_by_customer"=>$decode['bags_collection_by_customer']
                );
                $response = $this->UpdateUnloadingDetailsDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateUnloadingDetailsDB($params=FALSE)
    {
        $data = array(
                    "BookingID"=>$params['BookingID'],
                    "Gate_in_ID"=>$params['Gate_in_ID'],
                    "total_bags"=>$params['total_bags'],
                    "total_katta"=>$params['total_katta'],
                    "total_layers"=>$params['total_layers'],
                    "bags_collection_by_customer"=>$params['bags_collection_by_customer']
                );
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->set('status',6);
            $this->db->update('tblGateMaster');
            $Details = $this->db->insert('tblUnloadingMaster',$data);
            $response = array("status"=>true,"message"=>"Unloading Details Inserted","Details"=>$Details);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateQualitySingleLayerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "layer_number"=>$decode['layer_number'],
                    "ItemID"=>$decode['ItemID'],
                    "UserID"=>$decode['UserID'],
                    "QCDetails"=>$decode['QCDetails']
                );
                $response = $this->UpdateQualitySingleLayerDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateQualitySingleLayerDB($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $QCDetails = $params['QCDetails'];
            if(count($QCDetails)>0){
                foreach ($QCDetails as $key => $value) {
                    $ItemParameterID = $value["ItemParameterID"];
                    $ParameterValue = $value["ParameterValue"];
                    $GetQCRecord = $this->GetQCRecord($ItemParameterID,$params['layer_number'],$params['Gate_in_ID'],$params['BookingID']);
                    if($GetQCRecord){
                        //$response = array("status"=>true,"message"=>"Quality Details Already Inserted","BookingID"=>$params['BookingID']);
                    }else{
                        $parameterArray = array(
                            "BookingID" =>$params['BookingID'],
                            "Gate_in_ID" =>$params['Gate_in_ID'],
                            "TType" =>"U",
                            "ItemID" =>$params['ItemID'],
                            "layer_number" =>$params['layer_number'],
                            "ItemParameterID" =>$ItemParameterID,
                            "ParameterValue" =>$ParameterValue,
                            "UserID" =>$params['UserID'],
                            "TransDate" =>date('Y-m-d H:i:s')
                        );
                        $this->db->insert('tblQCParameterValues',$parameterArray);
                    }
                }
                $response = array("status"=>true,"message"=>"Quality Details Inserted","BookingID"=>$params['BookingID']);
            }else{
                $response = array("status"=>true,"message"=>"Quality Details Inserted As Blank","BookingID"=>$params['BookingID']);
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    public function GetQCRecord($ItemParameterID,$layer_no,$gateINID,$BookingID)
    {
        $this->db->select('tblQCParameterValues.*');
        $this->db->where('tblQCParameterValues.ItemParameterID', $ItemParameterID);
        $this->db->where('tblQCParameterValues.layer_number', $layer_no);
        $this->db->where('tblQCParameterValues.Gate_in_ID', $gateINID);
        $this->db->where('tblQCParameterValues.TType', 'U');
        return $this->db->get('tblQCParameterValues')->row();
    }
    
    public function UpdateQualityAllLayerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "layers_list"=>$decode['layers_list'],
                );
                $response = $this->UpdateQualityAllLayer($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateQualityAllLayer($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $BookingID = $params['BookingID'];
            $GateINID = $params['Gate_in_ID'];
            $layers_list = $params['layers_list'];
            $count = 0;
            $date = date("Y-m-d H:i:s");
            foreach($layers_list as $key=>$val){
                foreach($val["QCparameter"] as $QCKey=>$QCVal){
                    $this->db->where('BookingID',$BookingID);
                    $this->db->where('Gate_in_ID',$GateINID);
                    $this->db->where('TType',"U");
                    $this->db->where('layer_number',$QCVal["layer_number"]);
                    $this->db->where('ItemParameterID',$QCVal["ItemParameterID"]);
                    $this->db->set('ParameterValue',$QCVal["ParameterValue"]);
                    $this->db->set('UserID2',$params["phonenumber"]);
                    $this->db->set('Lupdate',$date);
                    if($this->db->update('tblQCParameterValues')){
                        $count++;
                    }
                }
            }
            
            if($count>0){
                $response = array("status"=>true,"message"=>"Quality Details Updated","BookingID"=>$params['BookingID']);
            }else{
                $response = array("status"=>true,"message"=>"Quality Details same as previous","BookingID"=>$params['BookingID']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function PeripheralQCAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "ItemID"=>$decode['ItemID'],
                    "UserID"=>$decode['UserID'],
                    "QCDetails"=>$decode['QCDetails']
                );
                $response = $this->PeripheralQC($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PeripheralQC($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $QCDetails = $params['QCDetails'];
            if(count($QCDetails)>0){
                foreach ($QCDetails as $key => $value) {
                    $ItemParameterID = $value["ItemParameterID"];
                    $ParameterValue = $value["ParameterValue"];
                    $parameterArray = array(
                        "BookingID" =>$params['BookingID'],
                        "Gate_in_ID" =>$params['Gate_in_ID'],
                        "TType" =>"P",
                        "ItemID" =>$params['ItemID'],
                        "ItemParameterID" =>$ItemParameterID,
                        "ParameterValue" =>$ParameterValue,
                        "UserID" =>$params['UserID'],
                        "TransDate" =>date('Y-m-d H:i:s')
                    );
                    $this->db->insert('tblQCParameterValues',$parameterArray);
                }
                
                $this->db->where('BookingID',$params['BookingID']);
                $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
                $this->db->set('status',3);
                $this->db->update('tblGateMaster');
                $response = array("status"=>true,"message"=>"Peripheral Quality Inserted","BookingID"=>$params['BookingID']);
            }else{
                $response = array("status"=>true,"message"=>"Peripheral Quality Inserted As Blank","BookingID"=>$params['BookingID']);
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateQualityAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID']
                );
                $response = $this->UpdateQualityDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateQualityDB($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.status');
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $result = $this->db->get('tblGateMaster')->row();
            
            if($result->status == 6){
                $this->db->where('BookingID',$params['BookingID']);
                $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
                $this->db->set('status',7);
                $Quality = $this->db->update('tblGateMaster');
                $response = array("status"=>true,"message"=>"Quality Status Updates","Quality"=>$Quality);
            }else if($result->status == 7){
                $response = array("status"=>true,"message"=>"Quality Status Updates","Quality"=>$Quality);
            }else{
                $response = array("status"=>false,"message"=>"Quality Not Finished");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//==== Use this API for Trade Accept/reject by broker or Kirti modified trade accept/reject by broker/ trader/ farmer 
    public function BookingAcceptAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "CustType"=>$decode['CustType'],
                    "Status"=>$decode['Status'],
                );
                $response = $this->BookingAccept($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function BookingAccept($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $GetBookingDetails = $this->GetBookingDetails($params['BookingID']);
            $AccountID = $GetBookingDetails->AccountID;
            if($params['Status'] == "NA" || $params['Status'] == "N"){
                $status = "N";
            }else{
                $status = "Y";
            }
            $traderids = array($AccountID);
            $date_time = date('Y-m-d H:i:s');
            $BookingID = $params['BookingID'];
            $this->db->where('BookingID',$params['BookingID']);
            
            /*if($GetBookingDetails->modify_date == null){
                if($params['CustType'] == "2"){
                    $this->db->set('BrokerApprove',$status);
                    $this->db->set('BrokerApproveTime',$date_time);
                }else{
                    $this->db->set('ClientApprove',$status);
                    $this->db->set('BrokerApproveTime',$date_time);
                }
            }*/
            
            /*if($params['CustType'] == "2"){
                $this->db->set('BrokerApprove',$status);
                $this->db->set('BrokerApproveTime',$date_time);
            }else{
                $this->db->set('ClientApprove',$status);
                $this->db->set('BrokerApproveTime',$date_time);
            }*/
            
            $this->db->set('BrokerApprove',$status);
            $this->db->set('BrokerApproveTime',$date_time);
            $this->db->set('ClientApprove',$status);
            $this->db->set('BrokerApproveTime',$date_time);
                
            if($status == "N"){
                if($params['CustType'] == "2"){
                    $last_action = "Rejected By Broker";
                    $this->db->set('LastActionName',$last_action);
                    $bodyTrader = "Your BookingID : ".$BookingID.' Rejected by Broker';
                    $bodyBroker = "Your BookingID : ".$BookingID.' Rejected';
                    $title_broker = "Trade Rejected by you";
                    $title_trader = "Trade Rejected by Broker";
                }else{
                    $last_action = "Rejected By Trader";
                    $this->db->set('LastActionName',$last_action);
                    $bodyTrader = "Your BookingID : ".$BookingID.' Rejected';
                    $bodyBroker = "Your BookingID : ".$BookingID.' Rejected by Trader';
                    $title_broker = "Trade Rejected by Trader";
                    $title_trader = "Trade Rejected by you";
                }
                //$this->db->set('IsApprove','N');
            }else{
                
                if($params['CustType'] == "2"){
                    $bodyTrader = "Your BookingID : ".$BookingID.' accepted by Broker';
                    $bodyBroker = "Your BookingID : ".$BookingID.' accepted';
                    $title_broker = "Trade accepted by you";
                    $title_trader = "Trade accepted by Broker";
                }else{
                    $bodyTrader = "Your BookingID : ".$BookingID.' accepted';
                    $bodyBroker = "Your BookingID : ".$BookingID.' accepted by Trader';
                    $title_broker = "Trade accepted by Trader";
                    $title_trader = "Trade accepted by you";
                }
            }
            if($this->db->update('tbllead_master')){
                // Send data to Pcsoft if trade approval after modify by kirti
                if($GetBookingDetails->TType == "P" && $GetBookingDetails->modify_date != NULL){
                    $trinvs_array = array([
                        "doc_type"=>"37",
                        "party_st"=>"C",
                        "party_no"=>$GetBookingDetails->ShortCode,
                        "doc_ref"=>$GetBookingDetails->BookingID,
                        "im_loc"=>$GetBookingDetails->PCCenterID
                    ]);
                    $sporddtl_array = array([
                        "IM_CODE"=>$GetBookingDetails->ItemID,
                        "im_qty"=>$GetBookingDetails->e_quantity,
                        "im_ordrate"=>$GetBookingDetails->basic_rate
                    ]);
            
                    $data_po_array =  array(
                        "cocd" => $GetBookingDetails->PartyID,
                        "trinvs"=>$trinvs_array,
                        "sporddtl"=>$sporddtl_array
                    );
                    $po_data = json_encode($data_po_array);
                    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        
                        CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/posoSubmit", //  -> LIVE URL
                        //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/posoSubmit", // -> DEV URL
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $po_data,
                        CURLOPT_HTTPHEADER => array(
                                "content-type: application/json"
                            ),
                        )
                    );
                    $response = curl_exec($curl);
                    $response_array = json_decode($response);
                    $PcSoft_po = $response_array->doc_ref_number;
                    $status = $response_array->Status;
                    if($status == true){
                        $insert_referance = array(
                            "Type"=>$GetBookingDetails->TType,
                            "Name"=>"Trade",
                            "GIC_Reference"=>$GetBookingDetails->BookingID,
                            "pcsoft_doc_ref"=>$PcSoft_po
                        );
                        $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                    }
                    $err = curl_error($curl);
                    curl_close($curl);
                }
                $screen = 1;
                // Notification for Trader
                $AccountDetails = $this->GetSingleAccountDetails($AccountID);
                $Trader_fcm = $AccountDetails->fcm_token;
                $this->send_notification($title_trader,$screen,$bodyTrader,$BookingID,$Trader_fcm);
                
                if($AccountID != $GetBookingDetails->BrokerID){
                    $AccountDetails = $this->GetSingleAccountDetails($GetBookingDetails->BrokerID);
                    $Broker_fcm = $AccountDetails->fcm_token;
                    $this->send_notification($title_broker,$screen,$bodyBroker,$BookingID,$Broker_fcm);
                }
                $response = array("status"=>true,"message"=>" Status Updates");
            }else{
                $response = array("status"=>false,"message"=>"something went wrong");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function BookingDetails($BookingID)
    {
        $this->db->select('tbllead_master.*');
        $this->db->where('tbllead_master.BookingID', $BookingID);
        return $this->db->get('tbllead_master')->row();
    }
    
    public function CleaningAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "FMQty"=>$decode['FMQty'],
                    "FMUserID"=>$decode['UserID']
                );
                $response = $this->Cleaningupdate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function Cleaningupdate($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $Update_data = array(
                "status"=>8,
                "FMQty"=>$params['FMQty'],
                "FMUserID"=>$params['FMUserID'],
                "FMTransDate"=>date('Y-m-d H:i:s')
            );
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->set('status',8);
            $this->db->update('tblGateMaster',$Update_data);
            $response = array("status"=>true,"message"=>"Update cleaning data successfully","Updated_data"=>$Update_data);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function UpdateTareweightAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                // Tare weight VhlTop Image 
                if($decode['TWVhlTopImage'])
                {
                    $image1 = base64_decode($decode['TWVhlTopImage']);
                    $image_name = 'TWVhlTopImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // Tare weight VhlFront Image 
                if($decode['TWVhlFrontImage'])
                {
                    $image2 = base64_decode($decode['TWVhlFrontImage']);
                    $image_name = 'TWVhlFrontImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // Tare weight VHLSide Image 
                if($decode['TWVHLSideImage'])
                {
                    $image3 = base64_decode($decode['TWVHLSideImage']);
                    $image_name = 'TWVHLSideImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "TareWeight"=>($decode['TareWeight']) / 100,
                    "TWVhlTopImage"=>$path1,
                    "TWVhlFrontImage"=>$path2,
                    "TWVHLSideImage"=>$path3,
                    "UserID"=>$decode['UserID'],
                    "SlipNo"=>$decode['SlipNo']
                );
                $response = $this->UpdateTareweight($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateTareweight($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $BookingID = $params['BookingID'];
			$GateINID = $params['Gate_in_ID'];
            $UpdateArray = array(
                "TareWeight"=>$params['TareWeight'],
                "TWVhlTopImage"=>$params['TWVhlTopImage'],
                "TWVhlFrontImage"=>$params['TWVhlFrontImage'],
                "TWVHLSideImage"=>$params['TWVHLSideImage'],
                "status"=>'9',
                "TWUserID"=>$params['UserID'],
                "TWTransDate"=>date('Y-m-d H:i:s'),
            );
            if($params['SlipNo']){
                $UpdateArray["weigh_bridge_slip_no"] = $params['SlipNo'];
            }
            $this->db->where('BookingID',$BookingID);
            $this->db->where('Gate_in_ID',$GateINID);
            $this->db->update('tblGateMaster',$UpdateArray);
            if($this->db->affected_rows() > 0){
                $GateControlDetails = $this->db->select('*')->get_where(db_prefix().'GateMaster',array('BookingID'=>$BookingID,'Gate_in_ID'=>$GateINID))->row();
                $AccountDetails = $this->db->select('AccountID,vat,CustomerType')->get_where(db_prefix().'clients',array('AccountID'=>$GateControlDetails->AccountID))->row();
                $BookingDetailsfrom_lead = $this->db->select('*')->get_where(db_prefix().'lead_master',array('BookingID'=>$BookingID))->row();
                
                $gateControlTaxDetails = $this->GateControl_model->GetControlDetails2($BookingID,$GateINID);
                
                if($AccountDetails->vat == ''){
    				$bt = 'N';
    				}else{
    				$bt = 'Y';
    			}
    			$FY = $GateControlDetails->FY;
    			$PlantID = $GateControlDetails->PlantID;
                $Netweight = ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight) / 10;
                $Netweight = number_format($Netweight, 2, '.', '');
                
                if($AccountDetails->CustomerType == 1){
                    $saleRate = $GateControlDetails->basic_rate;    
                }else{
                    $saleRate = ($GateControlDetails->basic_rate + ($GateControlDetails->basic_rate * $gateControlTaxDetails->taxrate) / 100);
                }
                
                $GodownID = $GateControlDetails->GodownID;
                //next_deposit_number_for_kirti
                
                if($GateControlDetails->TType == "S") {
			        $TypeID = "SP";
			        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
				    $Billno = "PO".$FY.$new_poNumber;
			    }else if($GateControlDetails->TType == "P"){
			        $TypeID = "SP";
			        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
				    $Billno = "PO".$FY.$new_poNumber;
			    }else if($GateControlDetails->TType == "A"){
			        $TypeID = "A";
			        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
				    $Billno = "PO".$FY.$new_poNumber;
			    }else if($GateControlDetails->TType == "T"){
			        $TypeID = "TF";
			        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
				    $Billno = "PO".$FY.$new_poNumber;
			    }else if($GateControlDetails->TType == "D") {
			        $TypeID = "DW";
			        $new_poNumber = get_option2('next_deposit_number_for_kirti',$FY);
				    $Billno = "DO".$FY.$new_poNumber;
			    } else if ($GateControlDetails->TType == "W"){
			        $TypeID = "DW";
			        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
				    $Billno = "PO".$FY.$new_poNumber;
			    }
                
				$data_array = array(
                    'PlantID'=>$PlantID,
                    'FY'=>$FY,
                    'BT'=>$bt,
                    'PurchID' =>$Billno,
                    'TransID' =>$GateINID,
                    'Transdate' =>date('Y-m-d H:i:s'),
                    'AccountID'=>$GateControlDetails->AccountID,
                    'Invoiceno'=>NULL,
                    'Invoicedate'=>NULL,
                    'Purchamt'=> 0,
                    // 'Discamt'=>0,
                    // 'Frtamt'=>0,
                    // 'Othamt'=>0,
                    'Invamt'=>0,
                    'ItCount'=>1,
                    'RoundOffAmt'=>NULL,
                    // 'OthAccountID'=>NULL,
                    'cgstamt'=>0,
                    'sgstamt'=>0,
                    'igstamt'=>0,
                    // 'tcs'=>NULL,
                    // 'tcsAmt'=>NULL,
                    "Userid" => $params['UserID'],
				);
				if($GateControlDetails->TType == "D"){
			        $this->db->insert(db_prefix() . 'depositemaster',$data_array);
			    } else {
			     //   $data_array["FrtAccountID"] = NULL;
			        $data_array["Discamt"] = 0;
			        $data_array["Frtamt"] = 0;
			        $data_array["Othamt"] = 0;
			        $data_array["OthAccountID"] = NULL;
			        $data_array["tcs"] = NULL;
			        $data_array["tcsAmt"] = NULL;
			        $this->db->insert(db_prefix() . 'purchasemaster',$data_array);
			    }
				// $this->db->insert(db_prefix() . 'purchasemaster',$data_array);
				if($this->db->affected_rows() > 0){
				    if($GateControlDetails->TType == "D"){
    			        $this->increment_next_donumber($FY,$PlantID);
    			    } else {
				        $this->increment_next_ponumber($FY,$PlantID);
    			    }
				    $data_array_result = array(
                        'PlantID'=>$PlantID,
                        'FY'=>$FY,
                        'cnfid' =>1,
                        'OrderID' =>$GateINID,
                        "TransID"=>$Billno,
                        'TransDate' =>date('Y-m-d H:i:s'),
                        'BillID' =>$BookingID,
                        'GodownID' =>$GodownID,
                        'CenterID' =>$GateControlDetails->CenterID,
                        'TypeID' =>$TypeID,
                        'PartyID' =>$GateControlDetails->PartyID,
                        'ChamberID' =>$GateControlDetails->ChamberID,
                        'StackID' =>$GateControlDetails->StackID,
                        'LOTID' =>$GateControlDetails->LOTID,
                        'TransDate2'=>date('Y-m-d H:i:s'),
                        'TType'=>$GateControlDetails->TType,
                        'TType2'=> $GateControlDetails->TType2,
                        'AccountID'=>$GateControlDetails->AccountID,
                        'ItemID'=>$GateControlDetails->ItemID,
                        'CaseQty'=>1,
                        'PurchRate'=>$GateControlDetails->basic_rate,
                        'SaleRate'=>$saleRate,
                        'BasicRate'=>$GateControlDetails->basic_rate,
                        'final_rate'=>$GateControlDetails->basic_rate,
                        'SuppliedIn'=>$GateControlDetails->unit,
                        'Cases'=>$Netweight,
                        'OrderQty'=>$Netweight,
                        'BilledQty'=>$Netweight,
                        'OrderAmt'=>0,
                        'DiscAmt'=>0,
                        'cgst'=>0,
                        'sgst'=>0,
                        'igst'=>NULL,
                        'cgstamt'=>0,
                        'sgstamt'=>0,
                        'igstamt'=>NULL,
                        'OrderAmt'=>0,
                        'ChallanAmt'=>0,
                        'NetOrderAmt'=>0,
                        'NetChallanAmt'=>0,
                        'Ordinalno'=>1,
                        'UserID'=>$params['UserID']
					);
					$this->db->insert(db_prefix() . 'history',$data_array_result);
				}
                $response = array("status"=>true,"message"=>"Tare Weight Update Successfully","Details"=>$UpdateArray);
            }else{
                $response = array("status"=>false,"message"=>"something went wrong","Details"=>$UpdateArray);
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function increment_next_ponumber($FY,$selected_company)
		{
			// Update next PO number in settings
			
			$this->db->where('name', 'next_purchase_number_for_kirti');
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
	public function increment_next_donumber($FY,$selected_company)
		{
			// Update next PO number in settings
			
			$this->db->where('name', 'next_deposit_number_for_kirti');
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
    
    
    public function GenerateAsnAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                // Party Invoice image
                if($decode['PartyInvoice'])
                {
                    $image3 = base64_decode($decode['PartyInvoice']);
                    $image_name = 'PartyInvoice';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
                // select Warehouse 
                if($decode['WHID'])
                {
                    $WHID = $decode['WHID'];
                }else{
                    $WHID = NULL;
                }
                
                // select chamber 
                if($decode['CHID'])
                {
                    $CHID = $decode['WHID'];
                }else{
                    $CHID = NULL;
                }
                
                // select stack 
                if($decode['StackID'])
                {
                    $StackID = $decode['StackID'];
                }else{
                    $StackID = NULL;
                }
                
                // Select Lot 
                if($decode['LotID'])
                {
                    $LotID = $decode['LotID'];
                }else{
                    $LotID = NULL;
                }
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "GodownID"=>$WHID,
                    "ChamberID"=>$CHID,
                    "StackID"=>$StackID,
                    "LOTID"=>$LotID,
                    "VehicleNo"=>$decode['VehicleNo'],
                    "Phone"=>$decode['Phone'],
                    "quantity"=>$decode['quantity'],
                    "Asn_WT_MT"=>$decode['Asn_WT_MT'],
                    "PartyInvoice"=>$path3,
                    "asn_date"=>$decode['asn_date']
                );
                $response = $this->GenerateASN($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GenerateASN($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
			$this->db->select('tbllead_master.*,tblclients.company,tblclients.ShortCode,tblitems.PCItemID');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            $this->db->where('tbllead_master.BookingID', $params["BookingID"]);
			$details = $this->db->get('tbllead_master')->row();
			
			$CenterID = $details->CenterID;
			$new_Number = get_number($CenterID,'ASN');
            $number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
            $AsnID = "ASN".$CenterID.date('d').date('m').date('y').$number;
            if($details->basic_rate == NULL || $details->basic_rate == ""){
                $basic_rate = 0;
            }else{
                $basic_rate = $details->basic_rate;
            }
            $this->increment_number($CenterID,'ASN');
            if ( date('m') <= 3 ) {
                $FY = date('y') - 1;
            }else {
                $FY = date('y');
            }
            $data = array(
                'AccountID' => $details->AccountID,
                'ASNID' => $AsnID,
                'FY'=>$FY,
                'BookingID' => $params["BookingID"],
                'PartyID' => $details->PartyID,
                'basic_rate' => $basic_rate,
                'ItemID' => $details->ItemID,
                'CenterID' => $CenterID,
                'quantity' => $params["quantity"],
                'Asn_WT_MT' => $params["Asn_WT_MT"],
                'unit' => $details->unit,
                'asn_date' => $params["asn_date"],
                'PartyInvoice' => $params["PartyInvoice"],
                'asn_by' => $params['phonenumber'],
                'TType' => $details->TType,
                'TType2' => $details->TType2,
                "VehicleNo"=>$params['VehicleNo'],
                "Phone"=>$params['Phone'],
			);
            $this->db->insert('tblGateMaster',$data);
            $inserted_id = $this->db->insert_id();
            if($inserted_id){
                /* Load QR Code Library */
    			$this->load->library('ciqrcode');
    			/* Data */
    			$hex_data   = bin2hex($AsnID);
    			$save_name  = $hex_data.'.png';
    			
    			/* QR Code File Directory Initialize */
    			$dir = 'assets/media/qrcode/';
    			if (!file_exists($dir)) {
    				mkdir($dir, 0775, true);
    			}
    			
    			/* QR Configuration  */
    			$config['cacheable']    = true;
    			$config['imagedir']     = $dir;
    			$config['quality']      = true;
    			$config['size']         = '1024';
    			$config['black']        = array(255,255,255);
    			$config['white']        = array(255,255,255);
    			$this->ciqrcode->initialize($config);
    			
    			/* QR Data  */
    			$params['data']     = $AsnID.','.$params["BookingID"].','."1";
    			$params['level']    = 'L';
    			$params['size']     = 10;
    			$params['savename'] = FCPATH.$config['imagedir']. $save_name;
    			
    			$this->ciqrcode->generate($params);
    			
    			/* Return Data */
    			$QR = array(
                    'content' => $AsnID.','.$params["BookingID"],
                    'file'    => $dir. $save_name,
                    'name'    => $save_name
    			);
    			$this->db->where('BookingID',$params["BookingID"]);
    			$this->db->where('ASNID',$AsnID);
    			$this->db->set('ASNQR',$QR['name']);
    			$this->db->set('status',1);
    			$this->db->update('tblGateMaster');
    			if($details->TType == "P"){
    			    // Send to PC Soft 
                    $trinvs_array = array([
                        "party_no"=>$details->ShortCode,
                        "your_ref"=>$details->BookingID,
                        "truck_no"=>$params['VehicleNo'],
                        "doc_ref"=>$AsnID,
                        "your_date"=>$params["asn_date"],
                        "doc_flnm"=>NULL,
                        "lr_no"=>NULL,
                        "lr_date"=>NULL,
                        "type_code"=>NULL,
                    ]);
                    $sporddtl_array = array([
                        "im_code"=>$details->PCItemID,
                        "im_qty"=>$params["Asn_WT_MT"],
                        "im_bag"=>$params["quantity"],
                        "im_ordrate"=>$details->basic_rate
                    ]);
            
                    $data_asn_array =  array(
                        "cocd" => $details->PartyID,
                        "trinvs"=>$trinvs_array,
                        "sporddtl"=>$sporddtl_array
                    );
                    
                    $ASN_data = json_encode($data_asn_array);
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/ASNinsert", // live
                        //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/ASNinsert", // demo
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
                    $response = curl_exec($curl);
                    $response_array = json_decode($response);
                    $PcSoft_GIN = $response_array->doc_ref_number;
                    $status = $response_array->Status;
                    if($status == true){
                        $insert_referance = array(
                            "Type"=>$details->TType,
                            "Name"=>"ASN",
                            "GIC_Reference"=>$AsnID,
                            "pcsoft_doc_ref"=>$PcSoft_GIN,
                            "status"=>$status
                        );
                        $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                    }else{
                        $insert_referance = array(
                            "Type"=>$details->TType,
                            "Name"=>"ASN",
                            "GIC_Reference"=>$AsnID,
                            "status"=>$status
                        );
                        $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                    }
                    $err = curl_error($curl);
                    curl_close($curl);
    			}
                $response = array("status"=>true,"message"=>"ASN Generated Successfully","Details"=>$data);
            }else{
                $response = array("status"=>false,"message"=>"something went wrong","Details"=>$data);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//============= Get Transaction Summary List ===================================    
    public function TransactionSummeryAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CustType"=>$decode['CustType'],
                    "TType"=>$decode['TType'],
                    "status"=>$decode['status'],
                );
                $response = $this->TransactionSummery($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function TransactionSummery($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.BookingID,tbllead_master.TransDate,tbllead_master.basic_rate,tbllead_master.quantity,tbllead_master.e_quantity,tbllead_master.EMDPaid,tblitems.ItemName,tblCenterMaster.CenterName,tblCenterMaster.CenterID,tblclients.company AS PartyName,TBLBROKER.company AS BrokerName');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
            $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID',"LEFT");
            $this->db->join('tblclients AS TBLBROKER', 'TBLBROKER.AccountID = tbllead_master.BrokerID','LEFT');
            //$this->db->where('tbllead_master.AccountID',$decode['phonenumber']);
            //$this->db->or_where('tbllead_master.BrokerID',$decode['phonenumber']);
            if($params['CustType'] == '2'){
                $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
            }else{
                $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
            }
            $this->db->where('tbllead_master.TType',$params['TType']);
            $this->db->where('tbllead_master.IsApprove',"Y");
            $this->db->where('tbllead_master.ClientApprove',"Y");
            $this->db->where('tbllead_master.BrokerApprove',"Y");
            $this->db->order_by('tbllead_master.TransDate',"DESC");
            $TransactionList = $this->db->get('tbllead_master')->result_array();
            if($TransactionList){
                // Get Actual Inward List
                $this->db->select('SUM(tblGateMaster.LoadedWeight - tblGateMaster.TareWeight) AS InwardWeight,tblGateMaster.BookingID');
                $this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
                $this->db->where('tblGateMaster.TareWeight IS NOT NULL');
                if($params['CustType'] == '2'){
                    $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
                }else{
                    $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
                }
                $this->db->where('tbllead_master.TType',$params['TType']);
                $this->db->where('tbllead_master.IsApprove',"Y");
                $this->db->where('tbllead_master.ClientApprove',"Y");
                $this->db->where('tbllead_master.BrokerApprove',"Y");
                $this->db->group_by('tblGateMaster.BookingID');
                $this->db->order_by('tbllead_master.TransDate',"DESC");
                $InwardList = $this->db->get('tblGateMaster')->result_array();
                
                // Get ASN List
                $this->db->select('SUM(tblGateMaster.Asn_WT_MT) AS ASNWeight,tblGateMaster.BookingID');
                $this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
                
                if($params['CustType'] == '2'){
                    $this->db->where('tbllead_master.BrokerID',$params['phonenumber']);
                }else{
                    $this->db->where('tbllead_master.AccountID',$params['phonenumber']);
                }
                $this->db->where('tbllead_master.TType',$params['TType']);
                $this->db->where('tbllead_master.IsApprove',"Y");
                $this->db->where('tbllead_master.ClientApprove',"Y");
                $this->db->where('tbllead_master.BrokerApprove',"Y");
                $this->db->group_by('tblGateMaster.BookingID');
                $this->db->order_by('tbllead_master.TransDate',"DESC");
                $ASNList = $this->db->get('tblGateMaster')->result_array();
                
                $i = 0;
                foreach($TransactionList as $key=>$value){
                    $InwardQty = 0;
                    $AsnQty = 0;
                    foreach($InwardList as $Ikey=>$Ival){
                        if($value["BookingID"] == $Ival["BookingID"]){
                            $InwardQty += ($Ival["BookingID"]/10);
                        }
                    }
                    $TransactionList[$i]['inward_wt'] = number_format($InwardQty, 3, '.','');
                    foreach($ASNList as $AKey=>$AVal){
                        if($value["BookingID"] == $AVal["BookingID"]){
                            $AsnQty += $AVal["ASNWeight"];
                        }
                    }
                    $TransactionList[$i]['asn_wt'] = number_format($AsnQty, 3, '.','');
                    /*$this->db->select('SUM(tblGateMaster.LoadedWeight) AS Loading_wt,SUM(tblGateMaster.TareWeight) AS Tare_wt,Sum(Asn_WT_MT) AS ASNQty');
                    $this->db->where('tblGateMaster.BookingID',$value['BookingID']);
                    $this->db->where('tblGateMaster.TareWeight IS NOT NULL');
                    $this->db->group_by('tblGateMaster.BookingID');
                    $InwardList = $this->db->get('tblGateMaster')->row();
                    $InwardWt = $InwardList->Loading_wt - $InwardList->Tare_wt;
                    $WtInMT = $InwardWt / 10;
                    $TransactionList[$i]['inward_wt'] = number_format($WtInMT, 3, '.','');*/
                    $i++;
                }
                $response = array("status"=>true,"message"=>"Transaction Summery","TransactionList"=>$TransactionList);
            }else{
                $response = array("status"=>false,"message"=>"No Records Found");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function increment_number($CenterID,$TType)
    {
        $this->db->set('Number', 'Number+1', false);
        $this->db->WHERE('CenterID', $CenterID);
        $this->db->WHERE('TType', $TType);
        $this->db->update(db_prefix() . 'numberformat');
    }
    
    public function AddNoOfLayersAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "no_of_layers"=>$decode['no_of_layers']
                );
                $response = $this->AddNoOfLayersDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AddNoOfLayersDB($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->set('status',5);
            $this->db->set('no_of_layers',$params['no_of_layers']);
            $Layers = $this->db->update('tblGateMaster');
            $response = array("status"=>true,"message"=>"No of Layers Inserted","Layers"=>$Layers);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    
    public function InwardDetailsByBookingIDAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                );
                $response = $this->InwardDetailsByBookingID($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function InwardDetailsByBookingID($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.*,tblitems.ItemName,tblCenterMaster.CenterName,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude,
            tblclients.company AS PartyName,TBLBROKER.company AS BrokerName');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
            $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');
            $this->db->join('tblclients AS TBLBROKER', 'TBLBROKER.AccountID = tbllead_master.BrokerID','LEFT');
            $this->db->where('tbllead_master.BookingID',$params['BookingID']);
            $BookingDetails = $this->db->get('tbllead_master')->row();
            $BookingDetails->Link = 'https://kirtidev.globalinfocloud.com/uploads/cs/CS-Buy-Trade-Soyabean.pdf';
            if($BookingDetails){
                $this->db->select('tblGateMaster.*');
                $this->db->where('tblGateMaster.BookingID',$params['BookingID']);
                $InwardList = $this->db->get('tblGateMaster')->result_array();
                $i = 0;
                foreach($InwardList as $key=>$Val){
                    $this->db->select('tblQCParameterValues.BookingID,tblQCParameterValues.Gate_in_ID,tblItemParameter.ItemParameterName,tblQCParameterValues.ParameterValue,tblQCParameterValues.EParameterValue,tblQCParameterValues.HParameterValue');
                    $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
                    $this->db->where('tblQCParameterValues.BookingID',$Val['BookingID']);
                    $this->db->where('tblQCParameterValues.TType',"F");
                    $this->db->where('tblQCParameterValues.Gate_in_ID',$Val['Gate_in_ID']);
                    $QCList = $this->db->get('tblQCParameterValues')->result_array();
                    $InwardList[$i]["QCDetails"] = $QCList;
                    
                    // $this->db->select('tblPaymentStatus.*');
                    // $this->db->join('tblPaymentStatus', 'tblPaymentStatus.ASNID = tblGateMaster.ASNID');
                    // $this->db->where('tblGateMaster.Gate_in_ID = ', $Val['Gate_in_ID']);
                    // $paymentList = $this->db->get('tblQCParameterValues')->result_array();
                    // $InwardList[$i]["QCDetails"] = $QCList;
                    
                    $i++;
                }
                
                //$BookingDetails->QCList = $QCList;
                $BookingDetails->inwardList = $InwardList;
                $response = array("status"=>true,"message"=>"Inward Details","BookingDetails"=>$BookingDetails);
            }else{
                $response = array("status"=>false,"message"=>"");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetPaymentStatusByInwardAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "ASNID"=>$decode['ASNID'],
                );
                $response = $this->GetPaymentStatusByInward($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetPaymentStatusByInward($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblPaymentStatus.*');
            $this->db->where('tblPaymentStatus.BookingID',$params['BookingID']);
            $this->db->where('tblPaymentStatus.ASNID',$params['ASNID']);
            $paymentData = $this->db->get('tblPaymentStatus')->result_array();
            if($paymentData){
                $response = array("status"=>true,"message"=>"Data found","PaymentData"=>$paymentData);
            }else{
                $response = array("status"=>false,"message"=>"No data found");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetBookingDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "BookingID"=>$decode['BookingID'],
                    "QType"=>$decode['QType']
                );
                $response = $this->GetBookingDetailsDB($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetBookingDetailsDB($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.*,tblitems.ItemID,tblitems.ItemName,tblUnloadingMaster.total_bags,tblUnloadingMaster.total_katta,tblUnloadingMaster.bags_collection_by_customer');
            $this->db->join('tblUnloadingMaster','tblUnloadingMaster.BookingID = tblGateMaster.BookingID AND tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID','LEFT');
            $this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
            if($params['QType'] == "1"){
                $this->db->where('tblGateMaster.ASNID',$params['Gate_in_ID']);
            }else{
                $this->db->where('tblGateMaster.Gate_in_ID',$params['Gate_in_ID']);
            }
            
            $this->db->where('tblGateMaster.BookingID',$params['BookingID']);
            $Booking = $this->db->get('tblGateMaster')->row();
            if($Booking){
                $GetParameter = $this->GetQCParameter($Booking->ItemID);
                $Booking->Qcparameterlist = $GetParameter;
                // layer details
                $this->db->select('tblLayerMaster.*');
                $this->db->where('tblLayerMaster.BookingID',$params['BookingID']);
                $this->db->where('tblLayerMaster.Gate_in_ID',$params['Gate_in_ID']);
                $layers_list = $this->db->get('tblLayerMaster')->result_array();
                $i = 0;
                foreach($layers_list as $value){
                    $this->db->select('tblQCParameterValues.layer_number,tblQCParameterValues.ItemParameterID,tblQCParameterValues.ParameterValue,tblItemParameter.ItemParameterName');
                    $this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
                    $this->db->where('tblQCParameterValues.BookingID',$params['BookingID']);
                    $this->db->where('tblQCParameterValues.layer_number',$value['layer_number']);
                    $this->db->where('tblQCParameterValues.TType','U');
                    $this->db->where('tblQCParameterValues.Gate_in_ID',$params['Gate_in_ID']);
                    $QCList = $this->db->get('tblQCParameterValues')->result_array();
                
                    $layers_list[$i]['QCparameter'] = $QCList;
                    $i++;
                }
                $response = array("status"=>true,"message"=>"Booking Details Updated","Booking"=>$Booking,"layers_list"=>$layers_list);
            }else{
                $response = array("status"=>false,"message"=>"BookingID not found");
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetContractNoteDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                );
                $response = $this->GetContractNoteDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetContractNoteDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.BookingID,tbllead_master.TransDate,tbllead_master.AccountID,tblitems.ItemID,tblitems.ItemName,tblCenterMaster.CenterID,tblCenterMaster.CenterName,tblCenterMaster.address As CenterAddress');
            $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID','LEFT');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            $this->db->where('tbllead_master.BookingID',$params['BookingID']);
            $BookingDetails = $this->db->get('tbllead_master')->row();
            if($BookingDetails){
                // QC Parameter Details
                $GetParameter = $this->GetQCParameter($BookingDetails->ItemID);
                $BookingDetails->Qcparameterlist = $GetParameter;
                // Bank Account Details 
                $GetBankDetails = $this->GetBankDetailsByAccountID($BookingDetails->AccountID);
                $BookingDetails->BankDetails = $GetBankDetails;
                $response = array("status"=>true,"message"=>"Contract Note Details","Details"=>$BookingDetails);
            }else{
                $response = array("status"=>false,"message"=>"Details not found");
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    public function GetSurveyListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetSurveyList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetSurveyList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblsurvey.*');
            $this->db->where('tblsurvey.UserID',$checkLoginTokan['staffid']);
            $SurveyList = $this->db->get('tblsurvey')->result_array();
            $response = array("status"=>true,"message"=>"Survey List","SurveyList"=>$SurveyList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetSurveyDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "surveyID"=>$decode['surveyID']
                );
                $response = $this->GetSurveyDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetSurveyDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblsurvey.*');
            $this->db->where('tblsurvey.id',$params['surveyID']);
            $SurveyDetails = $this->db->get('tblsurvey')->row();
            if($SurveyDetails){
                // Dependants
                $this->db->select('tblsurveyDependants.*');
                $this->db->where('tblsurveyDependants.SurveyID',$params['surveyID']);
                $DependantsList = $this->db->get('tblsurveyDependants')->result_array();
                $SurveyDetails->DependantsList = $DependantsList;
                
                // Equipment
                $this->db->select('tblsurveyEquipment.*');
                $this->db->where('tblsurveyEquipment.SurveyID',$params['surveyID']);
                $Equipment = $this->db->get('tblsurveyEquipment')->result_array();
                $SurveyDetails->Equipment = $Equipment;
                
                // LiveStockList
                $this->db->select('tblSurveyLivestock.*');
                $this->db->where('tblSurveyLivestock.SurveyID',$params['surveyID']);
                $LiveStockList = $this->db->get('tblSurveyLivestock')->result_array();
                $SurveyDetails->LiveStockList = $LiveStockList;
                
                // CropPattern
                $this->db->select('tblSurveyCropPattern.*');
                $this->db->where('tblSurveyCropPattern.SurveyID',$params['surveyID']);
                $CropPattern = $this->db->get('tblSurveyCropPattern')->result_array();
                $SurveyDetails->CropPattern = $CropPattern;
                
                // ProductionCost
                $this->db->select('tblSurveyProductionCost.*');
                $this->db->where('tblSurveyProductionCost.SurveyID',$params['surveyID']);
                $ProductionCost = $this->db->get('tblSurveyProductionCost')->result_array();
                $SurveyDetails->ProductionCost = $ProductionCost;
            }
            $response = array("status"=>true,"message"=>"Survey Details","SurveyDetails"=>$SurveyDetails);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//================= Send Request to initiate Trader/ Broker ====================    
    public function Send_requestAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ShortCode"=>$decode['ShortCode']
                );
                $response = $this->Send_request($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function Send_request($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $GetRequestToDetails = $this->getDetails_by_shortcode($params['ShortCode']);
            if($GetRequestToDetails->KYCStatus == "6"){
                $checkRequstExists = $this->get_request_exists($params['phonenumber'],$GetRequestToDetails->AccountID);
                if($checkRequstExists){
                    if($checkRequstExists->status == "NA"){
                        $status_message = "Sent";
                    }else{
                        $status_message = "Approved";
                    }
                    $message = "Request already ".$status_message;
                    $response = array("status"=>false,"message"=>$message);
                }else{
                    if($GetRequestToDetails->AccountID == $params['phonenumber']){
                        $message = "you cant't send request to yourself";
                        $response = array("status"=>false,"message"=>$message);
                    }else if($GetRequestToDetails->CustomerType == $checkLoginTokan->CustomerType){
                        if($checkLoginTokan->CustomerType == "2"){
                            $message = "you cant't send request to another broker";
                        }else if($checkLoginTokan->CustomerType == "3"){
                            $message = "you cant't send request to another trader";
                        }
                        $response = array("status"=>false,"message"=>$message);
                    }else{
                        $inser_array = array(
                            "send_from"=>$params['phonenumber'],
                            "send_to"=>$GetRequestToDetails->AccountID,
                            "TransDate"=>date('Y-m-d H:i:s'),
                            "status"=>"NA",
                            "UserID"=>$params['phonenumber']
                        );
                        if($this->db->insert('tbltrader_broker_assigned',$inser_array)){
                            $response = array("status"=>true,"message"=>"Request sent successfully");
                        }else{
                            $response = array("status"=>false,"message"=>"something went wrong, please try again later");
                        }
                    }
                }
            }else{
                $response = array("status"=>false,"message"=>"Requested short code is not veryfied");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//======================= Request Accept OR Reject =============================
    public function ApproveRequestAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "RequestFrom"=>$decode['RequestFrom'],
                    "request_id"=>$decode['request_id'],
                    "status"=>$decode['status']
                );
                $response = $this->ApproveRequest($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ApproveRequest($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $updateArray = array(
                "status"=>$params['status'],
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$params['phonenumber']
            );
            $this->db->where('send_from',$params['RequestFrom']);
            $this->db->where('send_to',$params['phonenumber']);
            $this->db->where('id',$params['request_id']);
            $this->db->update('tbltrader_broker_assigned',$updateArray);
            if($this->db->affected_rows() > 0){
                if($params['status'] == "Y"){
                    $response = array("status"=>true,"message"=>"requst Approved successfully");
                }else{
                    $response = array("status"=>true,"message"=>"requst Rejected successfully");
                }
            }else{
                $response = array("status"=>false,"message"=>"something went wrong, please try again later");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//============ Block Unblock Trader/Broker =====================================
    public function BlockUnblockPartyAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "request_id"=>$decode['request_id'],
                    "block_status"=>$decode['block_status']
                );
                $response = $this->BlockUnblockParty($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function BlockUnblockParty($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $updateArray = array(
                "block_status"=>$params['block_status'],
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$params['phonenumber']
            );
            $this->db->where('id',$params['request_id']);
            $this->db->update('tbltrader_broker_assigned',$updateArray);
            if($this->db->affected_rows() > 0){
                $response = array("status"=>true,"message"=>"Status updated");
            }else{
                $response = array("status"=>false,"message"=>"Status update failed");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//============= Get Broker/Trader Associate List ===============================
    public function RequestListAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->RequestList($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function RequestList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbltrader_broker_assigned.*,SendFrom.company AS SendFromName,SendTo.company AS SendToName');
            $this->db->join('tblclients AS SendFrom','SendFrom.AccountID = tbltrader_broker_assigned.send_from');
            $this->db->join('tblclients AS SendTo','SendTo.AccountID = tbltrader_broker_assigned.send_to');
            $this->db->where('tbltrader_broker_assigned.send_from',$params['phonenumber']);
            $this->db->or_where('tbltrader_broker_assigned.send_to',$params['phonenumber']);
            $request = $this->db->get('tbltrader_broker_assigned')->result_array();
            $response = array("status"=>true,"message"=>"requst List","data"=>$request);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function getDetails_by_shortcode($ShortCode)
    {
        $this->db->select('tblclients.*');
        $this->db->where('tblclients.ShortCode',$ShortCode);
        $Para_list = $this->db->get('tblclients')->row();
        return $Para_list;
    }
    public function get_request_exists($From,$To)
    {
        $status = array('NA','Y');
        $this->db->select('tbltrader_broker_assigned.*');
        $this->db->where('tbltrader_broker_assigned.send_from',$From);
        $this->db->where('tbltrader_broker_assigned.send_to',$To);
        $this->db->where_in('tbltrader_broker_assigned.status',$status);
        $request = $this->db->get('tbltrader_broker_assigned')->row();
        if(!$request){
            $this->db->select('tbltrader_broker_assigned.*');
            $this->db->where('tbltrader_broker_assigned.send_from',$To);
            $this->db->where('tbltrader_broker_assigned.send_to',$From);
            $this->db->where_in('tbltrader_broker_assigned.status',$status);
            $request = $this->db->get('tbltrader_broker_assigned')->row();
        }
        return $request;
    }
    
    public function GetQCParameter($ItemID)
    {
        $this->db->select('tblItemQCParameter.*,tblItemParameter.ItemParameterName');
        $this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblItemQCParameter.ItemParameterID');
        $this->db->where('tblItemQCParameter.ItemID',$ItemID);
        $Para_list = $this->db->get('tblItemQCParameter')->result_array();
        return $Para_list;
    }
    
    public function GetBankDetailsByAccountID($AccountID)
    {
        $this->db->select('tblBankDetails.*');
        $this->db->where('tblBankDetails.AccountID',$AccountID);
        $this->db->where('tblBankDetails.IsPrimary','1');
        $BankDetails = $this->db->get('tblBankDetails')->row();
        return $BankDetails;
    }
    
    //Update Loading Details Withdrawal
    public function UpdateLoadedWeightWithdrawalAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                // VhlTop Image 
                if($decode['VhlTopImage'])
                {
                    $image1 = base64_decode($decode['VhlTopImage']);
                    $image_name = 'VhlTopImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // VhlFront Image 
                if($decode['VhlFrontImage'])
                {
                    $image2 = base64_decode($decode['VhlFrontImage']);
                    $image_name = 'VhlFrontImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // VHLSide Image 
                if($decode['VHLSideImage'])
                {
                    $image3 = base64_decode($decode['VHLSideImage']);
                    $image_name = 'VHLSideImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
              
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "LoadedWeight"=>$decode['LoadedWeight'],
                    "UserID"=>$decode['UserID'],
                    "SlipNo"=>$decode['SlipNo'],
                    "VhlTopImage"=>$path1,
                    "VhlFrontImage"=>$path2,
                    "VHLSideImage"=>$path3
                );
                $response = $this->UpdateLoadedWeightWithdrawal($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateLoadedWeightWithdrawal($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $fetchBookingDetails =  $this->db->get('tblGateMaster')->row();
            if($fetchBookingDetails->TType == 'S'){
                $status = 7;
            }else{
                $status = 8;
            }
            $updateArray = array(
                "LoadedWeight"=>($params['LoadedWeight']) / 100,
                "VhlTopImage"=>$params['VhlTopImage'],
                "VhlFrontImage"=>$params['VhlFrontImage'],
                "VHLSideImage"=>$params['VHLSideImage'],
                "LWUserID"=>$params['UserID'],
                "LWTransDate"=>date('Y-m-d H:i:s'),
                "status"=>$status,
            );
            if($params['SlipNo']){
                $updateArray["weigh_bridge_slip_no"] = $params['SlipNo'];
            }
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->update('tblGateMaster',$updateArray);
            
            if($fetchBookingDetails->TType == 'S'){
                // Insert into salesmaster and history and ledger entry for sell
                $result = $this->insertSalesMaster($params['BookingID'],$params['Gate_in_ID'],$params['UserID']);
                
            }
            if($this->db->affected_rows() > 0){
                $response = array("status"=>true,"message"=>"Loaded Weight Details Updated","Details"=>$updateArray);
            }else{
                $response = array("status"=>false,"message"=>"something went wrong","Details"=>$updateArray);
            }
            
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Update Quality Withdrawl
    public function UpdateQualityWithdrawalAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID']
                );
                $response = $this->UpdateQualityWithdrawal($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateQualityWithdrawal($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.status');
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $result = $this->db->get('tblGateMaster')->row();
            
            if($result->status == 5){
                $this->db->where('BookingID',$params['BookingID']);
                $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
                $this->db->set('status',6);
                $Quality = $this->db->update('tblGateMaster');
                $response = array("status"=>true,"message"=>"Quality Status Updated","Quality"=>$Quality);
            }
            else{
                $response = array("status"=>false,"message"=>"Status is Not 5");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Update Unloading Details for Withdrawal
    public function UpdateUnloadingDetailsWithdrawalAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "total_bags"=>$decode['total_bags'],
                    "total_katta"=>$decode['total_katta'],
                    "total_layers"=>$decode['total_layers'],
                    "bags_collection_by_customer"=>$decode['bags_collection_by_customer']
                );
                $response = $this->UpdateUnloadingDetailsWithdrawal($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateUnloadingDetailsWithdrawal($params=FALSE)
    {
        $data = array(
                    "BookingID"=>$params['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "total_bags"=>$params['total_bags'],
                    "total_katta"=>$params['total_katta'],
                    "total_layers"=>$params['total_layers'],
                    "bags_collection_by_customer"=>$params['bags_collection_by_customer']
                );
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('BookingID',$params['BookingID']);
            $this->db->where('Gate_in_ID',$params['Gate_in_ID']);
            $this->db->set('status',5);
            $this->db->update('tblGateMaster');
            $Details = $this->db->insert('tblUnloadingMaster',$data);
            $response = array("status"=>true,"message"=>"Unloading Details Inserted","Details"=>$data);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Unloading in Progress for Withdrawal
    public function UnloadingInProgressWithdrawlAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID']
                );
                $response = $this->UnloadingInProgressWithdrawl($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UnloadingInProgressWithdrawl($data)
    {
        $checkLoginTokan = $this->CheckTokanStaff($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('tblGateMaster.BookingID',$data['BookingID']);
            $this->db->where('tblGateMaster.Gate_in_ID',$data['Gate_in_ID']);
            $this->db->set('tblGateMaster.status',4);
            $UnloadingInProgress = $this->db->update('tblGateMaster');
            $response = array("status"=>true,"message"=>"Withdraw Unloading In Progress","UnloadingInProgress"=>$UnloadingInProgress);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Update Tare Weight for Withdrawal
    public function UpdateTareWeightWithdrawlAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                // TWVhlTopImage Image 
                if($decode['TWVhlTopImage'])
                {
                    $image1 = base64_decode($decode['TWVhlTopImage']);
                    $image_name = 'TWVhlTopImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path1 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path1 , $image1);
                }else{
                    $path1 = '';  
                }
                
                // TWVhlFrontImage Image 
                if($decode['TWVhlFrontImage'])
                {
                    $image2 = base64_decode($decode['TWVhlFrontImage']);
                    $image_name = 'TWVhlFrontImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path2 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path2 , $image2);
                }else{
                    $path2 = '';  
                }
                
                // TWVHLSideImage Image 
                if($decode['TWVHLSideImage'])
                {
                    $image3 = base64_decode($decode['TWVHLSideImage']);
                    $image_name = 'TWVHLSideImage';
                    $filename = $image_name . '.' . 'png';
                //rename file name with random number
                    if (!file_exists('assets/Upload_doc/'.$decode['BookingID'])) {
                        mkdir('assets/Upload_doc/'.$decode['BookingID'], 0777, true);
                    }
                    $path3 = "assets/Upload_doc/".$decode['BookingID']."/".$filename;
                    file_put_contents($path3 , $image3);
                }else{
                    $path3 = '';  
                }
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "BookingID"=>$decode['BookingID'],
                    "Gate_in_ID"=>$decode['Gate_in_ID'],
                    "TareWeight"=>$decode['TareWeight'],
                    "UserID"=>$decode['UserID'],
                    "SlipNo"=>$decode['SlipNo'],
                    "TWVhlTopImage"=>$path1,
                    "TWVhlFrontImage"=>$path2,
                    "TWVHLSideImage"=>$path3
                );
                $response = $this->UpdateTareWeightWithdrawl($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateTareWeightWithdrawl($data)
    {
        $checkLoginTokan = $this->CheckTokanStaff($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('tblGateMaster.BookingID',$data['BookingID']);
            $this->db->where('tblGateMaster.Gate_in_ID',$data['Gate_in_ID']);
            $this->db->set('tblGateMaster.TareWeight',($data['TareWeight'])/100);
            $this->db->set('tblGateMaster.TWUserID',$data['UserID']);
            $this->db->set('tblGateMaster.TWTransDate',date('Y-m-d H:i:s'));
            $this->db->set('tblGateMaster.TWVhlTopImage',$data['TWVhlTopImage']);
            $this->db->set('tblGateMaster.TWVhlFrontImage',$data['TWVhlFrontImage']);
            $this->db->set('tblGateMaster.TWVHLSideImage',$data['TWVHLSideImage']);
            if($data['SlipNo']){
                $this->db->set('tblGateMaster.weigh_bridge_slip_no',$data['SlipNo']);
            }
            $this->db->set('tblGateMaster.status',3);
            $TareWeightDetails = $this->db->update('tblGateMaster');
            $response = array("status"=>true,"message"=>"Withdraw Tare Weight Updated","TareWeightDetails"=>$TareWeightDetails);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========== Get Available Deposited Stock Item ================================
    public function GetItemForWithdrawAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->GetItemForWithdraw($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetItemForWithdraw($data)
    {
        $checkLoginTokan = $this->CheckTokan($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.ItemID,tblitems.ItemName');
            $this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
            $this->db->where('tblGateMaster.AccountID',$data['phonenumber']);
            $this->db->where('tblGateMaster.TType',"D");
            $this->db->where('tblGateMaster.status',"12");
            $this->db->group_by('tblGateMaster.ItemID');
            $WItemList = $this->db->get(db_prefix().'GateMaster')->result_array();
            $response = array("status"=>true,"message"=>"Withdraw Item List","ItemList"=>$WItemList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//=============== Get WH List for Withdraw request =============================
    public function GetWHforWithdrawItemWiseAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "ItemID"=>$decode['ItemID'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->GetWHforWithdrawItemWise($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetWHforWithdrawItemWise($data)
    {
        $checkLoginTokan = $this->CheckTokan($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.ItemID,tblwarehouse.w_name,tblwarehouse.AccountID AS WHID,SUM(tblGateMaster.LoadedWeight - tblGateMaster.TareWeight) AS TotalQty_in_qtl');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tblGateMaster.GodownID');
            $this->db->where('tblGateMaster.AccountID',$data['phonenumber']);
            $this->db->where('tblGateMaster.ItemID',$data['ItemID']);
            $this->db->where('tblGateMaster.TType',"D");
            $this->db->where('tblGateMaster.status',"12");
            $this->db->group_by('tblGateMaster.GodownID');
            $W_WHList = $this->db->get(db_prefix().'GateMaster')->result_array();
            
            $this->db->select('tblGateMaster.ItemID,tblwarehouse.w_name,tblwarehouse.AccountID AS WHID,SUM(tblGateMaster.LoadedWeight - tblGateMaster.TareWeight) AS TotalQty_in_qtl');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tblGateMaster.GodownID');
            $this->db->where('tblGateMaster.AccountID',$data['phonenumber']);
            $this->db->where('tblGateMaster.ItemID',$data['ItemID']);
            $this->db->where('tblGateMaster.TType',"W");
            $this->db->where('tblGateMaster.status',"9");
            $this->db->group_by('tblGateMaster.GodownID');
            $WithList = $this->db->get(db_prefix().'GateMaster')->result_array();
            $i = 0;
            foreach($W_WHList as $key=>$val){
                $W_WHList[$i]["WithdrawQty"] = 0;
                foreach($WithList as $KeyW=>$valW){
                    if($val["GodownID"] == $valW["GodownID"] && $val["ItemID"] == $valW["ItemID"]){
                        $W_WHList[$i]["WithdrawQty"] = $valW["TotalQty_in_qtl"];
                    }
                }
                $i++;
            }
            
            $response = array("status"=>true,"message"=>"Withdraw WH List","WHList"=>$W_WHList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//================= Get Stack List for Withdraw request ========================
    public function GetStackforWithdrawWHWiseAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "ItemID"=>$decode['ItemID'],
                    "WHID"=>$decode['WHID'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->GetStackforWithdrawWHWise($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetStackforWithdrawWHWise($data)
    {
        $checkLoginTokan = $this->CheckTokan($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.ItemID,tblwarehouse.w_name,tblwhstackmaster.StackName,tblGateMaster.StackID');
            $this->db->join('tblGateMaster', 'tblGateMaster.AccountID = tbllead_master.AccountID AND tblGateMaster.TType = tbllead_master.TType');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tbllead_master.WHID');
            $this->db->join('tblwhstackmaster', 'tblwhstackmaster.StackID = tblGateMaster.StackID');
            $this->db->where('tbllead_master.AccountID',$data['phonenumber']);
            $this->db->where('tbllead_master.ItemID',$data['ItemID']);
            $this->db->where('tbllead_master.TType',"D");
            $this->db->where('tbllead_master.WHID',$data['WHID']);
            $this->db->where('tblwhstackmaster.WHID',$data['WHID']);
            $this->db->group_by('tblGateMaster.StackID');
            $W_StackList = $this->db->get(db_prefix().'lead_master')->result_array();
            $response = array("status"=>true,"message"=>"Withdraw Stack List","StackList"=>$W_StackList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//=============== Get Lot List for Withdraw request ============================
    public function GetLotforWithdrawStackWiseAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "ItemID"=>$decode['ItemID'],
                    "WHID"=>$decode['WHID'],
                    "StackID"=>$decode['StackID'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->GetLotforWithdrawStackWise($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetLotforWithdrawStackWise($data)
    {
        $checkLoginTokan = $this->CheckTokan($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tbllead_master.ItemID,tbllot_master.LotName,tblGateMaster.LOTID');
            $this->db->join('tblGateMaster', 'tblGateMaster.AccountID = tbllead_master.AccountID AND tblGateMaster.TType = tbllead_master.TType');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tbllead_master.WHID');
            $this->db->join('tbllot_master', 'tbllot_master.LOTID = tblGateMaster.LOTID');
            $this->db->where('tbllead_master.AccountID',$data['phonenumber']);
            $this->db->where('tbllead_master.ItemID',$data['ItemID']);
            $this->db->where('tbllead_master.TType',"D");
            $this->db->where('tbllead_master.WHID',$data['WHID']);
            $this->db->where('tbllot_master.WHID',$data['WHID']);
            $this->db->where('tbllot_master.StackID',$data['StackID']);
            $this->db->group_by('tblGateMaster.LOTID');
            $W_LotList = $this->db->get(db_prefix().'lead_master')->result_array();
            $response = array("status"=>true,"message"=>"Withdraw Lot List","LotList"=>$W_LotList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//======================== Get Lot List for Withdraw request ===================
    public function GetLotDetailsByLotIDAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "ItemID"=>$decode['ItemID'],
                    "LOTID"=>$decode['LOTID'],
                    "StackID"=>$decode['StackID'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->GetLotDetailsByLotID($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetLotDetailsByLotID($data)
    {
        $checkLoginTokan = $this->CheckTokan($data['login_tokan'],$data['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblGateMaster.ItemID,SUM(tblGateMaster.quantity) AS SumQty,tblGateMaster.unit');
            
            $this->db->where('tblGateMaster.AccountID',$data['phonenumber']);
            $this->db->where('tblGateMaster.ItemID',$data['ItemID']);
            $this->db->where('tblGateMaster.TType',"D");
            $this->db->where('tblGateMaster.StackID',$data['StackID']);
            $this->db->where('tblGateMaster.LOTID',$data['LOTID']);
            $this->db->group_by('tblGateMaster.LOTID');
            $LotDetails = $this->db->get(db_prefix().'GateMaster')->row();
            $response = array("status"=>true,"message"=>"Lot Details","LotDetails"=>$LotDetails);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//================= Add Withdrow Request against deposited stock ===============
    public function SubmitWithdrawRequestAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "WHID"=>$decode['WHID'],
                    "ItemID"=>$decode['ItemID'],
                    "quantity"=>$decode['quantity'],
                    "UserType"=>$decode['UserType'],
                    "OtherID"=>$decode['OtherID'],
                    "unit"=>$decode['unit'],
                    "TransDate"=>$decode['TransDate'],
                );
                $response = $this->SubmitWithdrawRequest($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SubmitWithdrawRequest($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }

        $PlantID = 1;
        $WH_Withdraw = array(
            "FY"=>$FY,
            "PlantID"=>$PlantID,
            "WHID"=>$params['WHID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$params['quantity'],
            "unit"=>$params['unit'],
            "UserID"=>$params['phonenumber'],
            "TransDate"=>$params['TransDate'].' '.date('H:i:s'),
            "TType"=> "W",
            "TType2"=> "Withdrow"
        );
        
        if($params['UserType'] == "2"){
            $WH_Withdraw['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $WH_Withdraw['BrokerID'] = $params['phonenumber'];
            $WH_Withdraw['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $WH_Withdraw['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $WH_Withdraw['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $WH_Withdraw['BrokerApprove'] = 'Y';
                $WH_Withdraw['BrokerID'] = $params['phonenumber'];
                $WH_Withdraw['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $WH_Withdraw['BrokerApprove'] = 'NA';
                $WH_Withdraw['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $WH_Withdraw['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $WH_Withdraw['AccountID'] = $AccountID;
            if($params['OtherID']){
                $WH_Withdraw['BrokerApprove'] = 'NA';
                $WH_Withdraw['BrokerID'] = $params['OtherID'];
            }else{
                $WH_Withdraw['BrokerApprove'] = 'Y';
                $WH_Withdraw['BrokerID'] = $AccountID;
            }
            
        }
        
        $this->db->where('AccountID',$params['WHID']);
        $WhDetails = $this->db->get('tblwarehouse')->row();
        $WH_Withdraw["CenterID"] = $WhDetails->center;
        $CenterID = $WhDetails->center;
        
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            // Get Current rate
            if($checkLoginTokan->CustomerType == "1"){
                $this->db->where('tblRateMaster.Type',"F");
            }else{
                $this->db->where('tblRateMaster.Type',"T");
            }
            $this->db->where('tblRateMaster.KeyID',"C01");
            $this->db->where('tblRateMaster.IsActive','Y');
            $this->db->where('tblRateMaster.ItemID',$params['ItemID']);
            $this->db->where('tblRateMaster.CenterID',$CenterID);
            $basicRateDetails = $this->db->get(db_prefix().'RateMaster')->row();
            $WH_Withdraw['basic_rate'] = $basicRateDetails->Rate;
            
            $this->db->insert(db_prefix().'lead_master', $WH_Withdraw);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $new_Number = get_number($CenterID,'W');
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $CenterID.'W'.date('d').date('m').date('y').$number;
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID]);
                $this->increment_center_wise_booking_number($CenterID,'W');
                
                // get Outstanding Balance
                
                $this->db->select('tblaccountledger.TType,SUM(tblaccountledger.Amount) AS TotalAMt');
                $this->db->where('tblaccountledger.AccountID',$params['phonenumber']);
                $this->db->where('tblaccountledger.FY',$FY);
                $this->db->where('tblaccountledger.PlantID',$PlantID);
                $this->db->group_by('tblaccountledger.TType');
                $ledger_data = $this->db->get(db_prefix().'accountledger')->result_array();
                
                // Opning balance
                
                $this->db->select('SUM(tblaccountbalances.BAL1) AS TotalopnAmt');
                $this->db->where('tblaccountbalances.AccountID',$params['phonenumber']);
                $this->db->where('tblaccountbalances.FY',$FY);
                $this->db->where('tblaccountbalances.PlantID',$PlantID);
                $opn_data = $this->db->get(db_prefix().'accountbalances')->row();
                $Cr = 0;
                $Dr = 0;
                $OpnBal = $opn_data->TotalopnAmt;
                foreach($ledger_data as $key=>$val){
                    if($val["TType"]=="C"){
                        $Cr += $val["TotalAMt"];
                    }else if($val["TType"]=="D"){
                        $Dr += $val["TotalAMt"];
                    }
                }
                $Bal = $OpnBal + $Dr - $Cr;
                
                $response = array("status"=>true,"message"=>"WH Withdrow request submitted successfully, we will contact you shortly.","BookingID"=>$bookingID,"Outstanding_balance"=>$Bal);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    // Add Withdrow commodity from WH Request
    public function PaymentStatusWithdrawAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "PaymentDate"=>$decode['PaymentDate'],
                    "PaymentRemark"=>$decode['PaymentRemark'],
                    "BookingID"=>$decode['BookingID'],
                );
                $response = $this->PaymentStatusWithdraw($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PaymentStatusWithdraw($params=FALSE)
    {
        
        $PaymentStatus = array(
            "PaymentDate"=>$params['PaymentDate'],
            "PaymentRemark"=>$params['PaymentRemark'],
        );
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->where('BookingID', $params['BookingID']);
            $this->db->where('AccountID', $params['phonenumber']);
            $this->db->update(db_prefix().'lead_master',$PaymentStatus);
            if($this->db->affected_rows () > 0){
                $response = array("status"=>true,"message"=>"Payment status update successfully, we will contact you shortly.","BookingID"=>$params['BookingID']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","BookingID"=>$params['BookingID']);
            }
        }
        
        return $response; 
    }
    
    // Add Survey Details
    public function AddSurweyAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "UserID"=>$decode['UserID'],
                    "name"=>$decode['name'],
                    "state"=>$decode['state'],
                    "district"=>$decode['district'],
                    "taluka"=>$decode['taluka'],
                    "village"=>$decode['village'],
                    "pin_code"=>$decode['pin_code'],
                    "mobile_number"=>$decode['mobile_number'],
                    "latitude"=>$decode['latitude'],
                    "longitude"=>$decode['longitude'],
                );
                $response = $this->AddSurvey($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AddSurvey($params=FALSE)
    {
        
        $Surveydata = array(
            "UserID"=>$params['UserID'],
            "name"=>$params['name'],
            "state"=>$params['state'],
            "district"=>$params['district'],
            "taluka"=>$params['taluka'],
            "village"=>$params['village'],
            "pin_code"=>$params['pin_code'],
            "mobile_number"=>$params['mobile_number'],
            "latitude"=>$params['latitude'],
            "longitude"=>$params['longitude'],
            "TransDate"=> date('Y-m-d H:i:s')
        );
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'survey', $Surveydata);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $data = array(
                    "mobile_no"=>$params['mobile_number']
                );
                $CheckUser = $this->CheckUserExist($data);
                if($CheckUser){
                    if($CheckUser->state == NULL || $CheckUser->state == ""){
                        // add details 
                        $addr_array = array(
                            "state"=>$params['state'],
                            "dist"=>$params['district'],
                            "subdist"=>$params['taluka'],
                            "po"=>$params['village'],
                            "vtc"=>$params['village'],
                            "zip"=>$params['pin_code'],
                        );
                        $this->db->where('tblclients.AccountID',$params['mobile_number']);
                        $this->db->update(db_prefix().'clients', $addr_array);
                    }
                }
                $SurveyDetails = $this->GetSurveyByID($insert_id);
                $response = array("status"=>true,"message"=>"Survey Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetSurveyByID($surveyID)
    {
        $this->db->select('tblsurvey.*');
        $this->db->where('tblsurvey.id',$surveyID);
        $SurveyDetails = $this->db->get(db_prefix().'survey')->row();
        if($SurveyDetails){
           $SurveyDetails->dependance = $this->GetDependance($surveyID); 
           $SurveyDetails->Equipment = $this->GetEquipment($surveyID);
           $SurveyDetails->Livestock = $this->GetLivestock($surveyID);
           $SurveyDetails->CropPattern = $this->GetCropPattern($surveyID);
           $SurveyDetails->ProductionCost = $this->GetProductionCost($surveyID);
        }
        return $SurveyDetails; 
    }
    
    public function GetDependance($surveyID)
    {
        $this->db->select('tblsurveyDependants.*');
        $this->db->where('tblsurveyDependants.SurveyID',$surveyID);
        $dependanceDetails = $this->db->get(db_prefix().'surveyDependants')->result_array();
        return $dependanceDetails; 
    }
    public function GetEquipment($surveyID)
    {
        $this->db->select('tblsurveyEquipment.*');
        $this->db->where('tblsurveyEquipment.SurveyID',$surveyID);
        $EquipmentDetails = $this->db->get(db_prefix().'surveyEquipment')->result_array();
        return $EquipmentDetails; 
    }
    
    public function GetLivestock($surveyID)
    {
        $this->db->select('tblSurveyLivestock.*');
        $this->db->where('tblSurveyLivestock.SurveyID',$surveyID);
        $LivestockDetails = $this->db->get(db_prefix().'SurveyLivestock')->result_array();
        return $LivestockDetails; 
    }
    
    public function GetProductionCost($surveyID)
    {
        $this->db->select('tblSurveyProductionCost.*');
        $this->db->where('tblSurveyProductionCost.SurveyID',$surveyID);
        $ProductionCostDetails = $this->db->get(db_prefix().'SurveyProductionCost')->result_array();
        return $ProductionCostDetails; 
    }
    
    public function GetCropPattern($surveyID)
    {
        $this->db->select('tblSurveyCropPattern.*');
        $this->db->where('tblSurveyCropPattern.SurveyID',$surveyID);
        $CropPatternDetails = $this->db->get(db_prefix().'SurveyCropPattern')->result_array();
        return $CropPatternDetails; 
    }
    
    // Add Survey Dependants Details
    public function AddSurwayDependantAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "Fields"=>$decode['Fields']
                );
                $response = $this->AddSurwayDependant($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AddSurwayDependant($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        $SurveyDetails = $this->GetSurveyByID($SurveyID);
        $fields = $params['Fields'];
        $del = 0;
        $ins = 0;
        if($checkLoginTokan){
            $this->db->where('SurveyID', $SurveyID);
            if($this->db->delete(db_prefix() . 'surveyDependants')){
                $del++;
            };
            foreach($fields as $val){
                $dep_array = array(
                    "SurveyID" => $SurveyID,
                    "name" => $val['name'],
                    "number" => $val['number'],
                    "gut_number" => $val['gut_number'],
                    "Irrigated_land" => $val['Irrigated_land'],
                    "UnIrrigated_land" => $val['UnIrrigated_land'],
                    "total_land" => $val['total_land']
                );
                if($this->db->insert(db_prefix().'surveyDependants', $dep_array)){
                    $ins++;
                }
            }
            
            if($del > 0 && $ins > 0){
                $response = array("status"=>true,"message"=>"Exiting Dependance Deleted and New Dependance Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del == '0' && $ins > 0){
                $response = array("status"=>true,"message"=>"Dependance Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del > 0 && $ins =='0'){
                $response = array("status"=>true,"message"=>"Exiting Dependance Deleted","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    // Add Survey Water Source Details
    public function WaterSourceAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "well"=>$decode['well'],
                    "borewell"=>$decode['borewell'],
                    "canal"=>$decode['canal'],
                    "pond_days"=>$decode['pond_days'],
                    "river_nala"=>$decode['river_nala'],
                    "farm_pond"=>$decode['farm_pond'],
                    "fisheries"=>$decode['fisheries'],
                    "fisheries_revenue"=>$decode['fisheries_revenue']
                );
                $response = $this->WaterSource($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function WaterSource($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        $SurveyDetails = $this->GetSurveyByID($SurveyID);
        
        if($checkLoginTokan){
            $update_survey = array(
                "well"=>$params['well'],
                "borewell"=>$params['borewell'],
                "canal"=>$params['canal'],
                "pond_days"=>$params['pond_days'],
                "river_nala"=>$params['river_nala'],
                "farm_pond"=>$params['farm_pond'],
                "fisheries"=>$params['fisheries'],
                "fisheries_revenue"=>$params['fisheries_revenue']
            );
            $this->db->where('id', $SurveyID);
            if($this->db->update(db_prefix() . 'survey',$update_survey)){
                $response = array("status"=>true,"message"=>"Water Source Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>false,"message"=>"data not update","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$SurveyDetails);
        }
        return $response;
    }
    
    
    // Add Agri Infrastructure/Equipment Owned Details
    public function EquipmentOwnedAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "Fields"=>$decode['Fields']
                );
                $response = $this->EquipmentOwned($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function EquipmentOwned($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        
        $fields = $params['Fields'];
        $del = 0;
        $ins = 0;
        if($checkLoginTokan){
            $this->db->where('SurveyID', $SurveyID);
            if($this->db->delete(db_prefix() . 'surveyEquipment')){
                $del++;
            };
            foreach($fields as $val){
                $dep_array = array(
                    "SurveyID" => $SurveyID,
                    "name" => $val['name'],
                    "number" => $val['number'],
                    "company" => $val['company']
                );
                if($this->db->insert(db_prefix().'surveyEquipment', $dep_array)){
                    $ins++;
                }
            }
            $SurveyDetails = $this->GetSurveyByID($SurveyID);
            if($del > 0 && $ins > 0){
                $response = array("status"=>true,"message"=>"Exiting Equipment Deleted and New Equipment Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del == '0' && $ins > 0){
                $response = array("status"=>true,"message"=>"Equipment Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del > 0 && $ins =='0'){
                $response = array("status"=>true,"message"=>"Exiting Equipment Deleted","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    // Add Livestock Details
    public function LivestockAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "Feed_per_day"=>$decode['Feed_per_day'],
                    "Feed_purchase"=>$decode['Feed_purchase'],
                    "FeedAvgCostPerKG"=>$decode['FeedAvgCostPerKG'],
                    "FeedCompany"=>$decode['FeedCompany'],
                    "Fields"=>$decode['Fields'],
                    "DairyRate"=>$decode['DairyRate'],
                    "OtherRate"=>$decode['OtherRate'],
                    "feed_type"=>$decode['feed_type'],
                    "feed_remark"=>$decode['feed_remark'],
                    "milk_can"=>$decode['milk_can'],
                    "milk_mh_company"=>$decode['milk_mh_company'],
                    "milk_col_company"=>$decode['milk_col_company'],
                    "feed_cutter_company"=>$decode['feed_cutter_company']
                );
                $response = $this->Livestock($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function Livestock($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        
        $fields = $params['Fields'];
        $del = 0;
        $ins = 0;
        if($checkLoginTokan){
            $MasterUpdate = array(
                "Feed_per_day"=>$params['Feed_per_day'],
                "Feed_purchase"=>$params['Feed_purchase'],
                "FeedAvgCostPerKG"=>$params['FeedAvgCostPerKG'],
                "FeedCompany"=>$params['FeedCompany'],
                "DairyRate"=>$params['DairyRate'],
                "OtherRate"=>$params['OtherRate'],
                "feed_type"=>$params['feed_type'],
                "feed_remark"=>$params['feed_remark'],
                "milk_can"=>$params['milk_can'],
                "milk_mh_company"=>$params['milk_mh_company'],
                "milk_col_company"=>$params['milk_col_company'],
                "feed_cutter_company"=>$params['feed_cutter_company']
            );
            $this->db->where('id', $SurveyID);
            $this->db->update(db_prefix() . 'survey',$MasterUpdate);
            
            
            $this->db->where('SurveyID', $SurveyID);
            if($this->db->delete(db_prefix() . 'SurveyLivestock')){
                $del++;
            };
            foreach($fields as $val){
                $dep_array = array(
                    "SurveyID" => $SurveyID,
                    "name" => $val['name'],
                    "number" => $val['number'],
                    "milk_per_day" => $val['milk_per_day'],
                    "breed" => $val['breed']
                );
                if($this->db->insert(db_prefix().'SurveyLivestock', $dep_array)){
                    $ins++;
                }
            }
            $SurveyDetails = $this->GetSurveyByID($SurveyID);
            if($del > 0 && $ins > 0){
                $response = array("status"=>true,"message"=>"Exiting Livestock Deleted and New Livestock Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del == '0' && $ins > 0){
                $response = array("status"=>true,"message"=>"Livestock Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del > 0 && $ins =='0'){
                $response = array("status"=>true,"message"=>"Exiting Livestock Deleted","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    // Add Cropping Pattern Details
    public function CropPatternAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "Year"=>$decode['Year'],
                    "Fields"=>$decode['Fields']
                );
                $response = $this->CropPattern($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CropPattern($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        $Year = $params['Year'];
        $fields = $params['Fields'];
        $del = 0;
        $ins = 0;
        if($checkLoginTokan){
            $this->db->where('SurveyID', $SurveyID);
            $this->db->where('Year', $Year);
            if($this->db->delete(db_prefix() . 'SurveyCropPattern')){
                $del++;
            };
            foreach($fields as $val){
                $dep_array = array(
                    "SurveyID" => $SurveyID,
                    "Year" => $Year,
                    "name" => $val['name'],
                    "kharif" => $val['kharif'],
                    "rabi" => $val['rabi']
                );
                if($this->db->insert(db_prefix().'SurveyCropPattern', $dep_array)){
                    $ins++;
                }
            }
            $SurveyDetails = $this->GetSurveyByID($SurveyID);
            if($del > 0 && $ins > 0){
                $response = array("status"=>true,"message"=>"Exiting Crop Pattern Deleted and New Crop Pattern Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del == '0' && $ins > 0){
                $response = array("status"=>true,"message"=>"Crop Pattern Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del > 0 && $ins =='0'){
                $response = array("status"=>true,"message"=>"Exiting Crop Pattern Deleted","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    // Add Production Cost Details
    public function ProductionCostAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "Fields"=>$decode['Fields']
                );
                $response = $this->ProductionCost($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function ProductionCost($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        $fields = $params['Fields'];
        $del = 0;
        $ins = 0;
        if($checkLoginTokan){
            $this->db->where('SurveyID', $SurveyID);
            if($this->db->delete(db_prefix() . 'SurveyProductionCost')){
                $del++;
            };
            foreach($fields as $val){
                $dep_array = array(
                    "SurveyID" => $SurveyID,
                    "CostType" => $val['CostType'],
                    "name" => $val['name'],
                    "value" => $val['value']
                );
                if($this->db->insert(db_prefix().'SurveyProductionCost', $dep_array)){
                    $ins++;
                }
            }
            $SurveyDetails = $this->GetSurveyByID($SurveyID);
            if($del > 0 && $ins > 0){
                $response = array("status"=>true,"message"=>"Exiting Production Cost Deleted and New Crop Pattern Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del == '0' && $ins > 0){
                $response = array("status"=>true,"message"=>"Production Cost Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else if($del > 0 && $ins =='0'){
                $response = array("status"=>true,"message"=>"Exiting Production Cost Deleted","SurveyDetails"=>$SurveyDetails);
            }else{
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    // Add Labour Availability Details
    public function LabourAvailabilityAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "labour_in_village"=>$decode['labour_in_village'],
                    "labour_in_nearby_village"=>$decode['labour_in_nearby_village'],
                    "male_labour_cost"=>$decode['male_labour_cost'],
                    "female_labour_cost"=>$decode['female_labour_cost']
                );
                $response = $this->LabourAvailability($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function LabourAvailability($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        if($checkLoginTokan){
            
            $MasterUpdate = array(
                "labour_in_village"=>$params['labour_in_village'],
                "labour_in_nearby_village"=>$params['labour_in_nearby_village'],
                "male_labour_cost"=>$params['male_labour_cost'],
                "female_labour_cost"=>$params['female_labour_cost']
            );
            $this->db->where('id', $SurveyID);
            if($this->db->update(db_prefix() . 'survey',$MasterUpdate)){
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"Labour Availability Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else{
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    
    // Add Govt Schemes Details
    public function GovtSchemesAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "solar_pump"=>$decode['solar_pump'],
                    "solar_capacity"=>$decode['solar_capacity'],
                    "crop_insurance"=>$decode['crop_insurance'],
                    "insurance_company"=>$decode['insurance_company'],
                    "compensations_received"=>$decode['compensations_received'],
                    "PMKSN"=>$decode['PMKSN'],
                    "AgriEquipmentByPanchayat"=>$decode['AgriEquipmentByPanchayat']
                );
                $response = $this->GovtSchemes($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GovtSchemes($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        if($checkLoginTokan){
            
            $MasterUpdate = array(
                "solar_pump"=>$params['solar_pump'],
                "solar_capacity"=>$params['solar_capacity'],
                "crop_insurance"=>$params['crop_insurance'],
                "insurance_company"=>$params['insurance_company'],
                "compensations_received"=>$params['compensations_received'],
                "PMKSN"=>$params['PMKSN'],
                "AgriEquipmentByPanchayat"=>$params['AgriEquipmentByPanchayat'],
            );
            $this->db->where('id', $SurveyID);
            if($this->db->update(db_prefix() . 'survey',$MasterUpdate)){
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"Govt Schemes Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else{
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
    
    
    // Add Smart Phone Usage Details
    public function SmartPhoneUsageAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "SurveyID"=>$decode['SurveyID'],
                    "smart_phone_user"=>$decode['smart_phone_user'],
                    "WhatsAppUser"=>$decode['WhatsAppUser'],
                    "youtube_referred"=>$decode['youtube_referred'],
                    "WhatsAppAgriService"=>$decode['WhatsAppAgriService'],
                    "ServiceIsPaid"=>$decode['ServiceIsPaid'],
                    "ServicePaidAmt"=>$decode['ServicePaidAmt'],
                    "PaymentFrquancy"=>$decode['PaymentFrquancy'],
                    "mob_used_for_forcasting"=>$decode['mob_used_for_forcasting']
                );
                $response = $this->SmartPhoneUsage($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SmartPhoneUsage($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        $SurveyID = $params['SurveyID'];
        if($checkLoginTokan){
            
            $MasterUpdate = array(
                "smart_phone_user"=>$params['smart_phone_user'],
                "WhatsAppUser"=>$params['WhatsAppUser'],
                "youtube_referred"=>$params['youtube_referred'],
                "WhatsAppAgriService"=>$params['WhatsAppAgriService'],
                "ServiceIsPaid"=>$params['ServiceIsPaid'],
                "ServicePaidAmt"=>$params['ServicePaidAmt'],
                "PaymentFrquancy"=>$params['PaymentFrquancy'],
                "mob_used_for_forcasting"=>$params['mob_used_for_forcasting']
            );
            $this->db->where('id', $SurveyID);
            if($this->db->update(db_prefix() . 'survey',$MasterUpdate)){
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"Smart Phone Usage Added Successfully","SurveyDetails"=>$SurveyDetails);
            }else{
                $SurveyDetails = $this->GetSurveyByID($SurveyID);
                $response = array("status"=>true,"message"=>"No Changes","SurveyDetails"=>$SurveyDetails);
            }
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","SurveyDetails"=>$SurveyDetails);
        }
        return $response; 
    }
//==============================================================================    
    
/* Staff Application APi */

    public function loginAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "mobile"=>$decode['mobile'],
                    "password"=>$decode['password'],
                    "staff"=>$decode['staff'],
                    "DeviceID"=>$decode['DeviceID']
                );
                $response=$this->login($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function login($params=FALSE){
        
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->login(
                $params['mobile'],
                $params['password'],
                $params['staff'],
                $params['DeviceID']
            );
        return $success; 
    } 
    
    public function LogOutStaffAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                    );
                    $response=$this->LogOutStaff($data);    
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    
    public function LogOutStaff($data) 
    {
        $affected_row = 0;
        $this->db->where('phonenumber', $data["phonenumber"]);
        $this->db->set('login_tokan',NULL);
        $this->db->update('tblstaff');
        if($this->db->affected_rows() > 0){
            $affected_row++;
        }
        if($affected_row >0){
            $response=array("status"=>true,"message"=>"You have logged Out successfully");
            return $response;
        }else{
            $response=array("status"=>false,"message"=>"Semething went wrong");
            return $response;
        }
    }
    
    public function check_in_out_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->helper('timesheets');
			    $this->load->helper('email_templates');
			    $this->load->model('departments_model');
			    $this->load->model('staff_model');
			    $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $payload = array(
        				'staff_id' => $decode['staff_id'],
        				'type_check' => $decode['type_check'],
        				'edit_date' => $decode['edit_date'],
        				'point_id' => $decode['point_id'],
        				'location_user' => $decode['location_user'],
        				'ip_address' => $decode['ip_address']
        			);
        			
        			$Tracking = array(
        			    "staffid"=>$decode['staff_id'],
        			    "TransDate"=>date('Y-m-d H:i:s'),
        			    "type_check"=>$decode['type_check'],
        			    "latitude"=>$decode['latitude'],
        			    "longitude"=>$decode['longitude'],
        			    "address"=>$decode['address'],
        			);
        			$re = $this->UserApp_Model->check_in($payload,$Tracking);
                    if (is_numeric($re)) {
				        // Error
				        if ($re == 2) {
				            $response = array("status"=>false,"message"=>"Your current location is not allowed to take attendance");
				        }
				        if ($re == 3) {
				            $response = array("status"=>false,"message"=>"location information is unknown");
				        }
				        if ($re == 4) {
				            $response = array("status"=>false,"message"=>"route point is unknown");
				        }
        				if ($re == 5) {
        				    $response = array("status"=>false,"message"=>"timesheet access denie");
        				}
        				if ($re == 6) {
        				    $response = array("status"=>false,"message"=>"timesheet cannot get client ip address");
        				}
        			} else {
        				if ($re == true) {
        					if ($decode['type_check'] == 1) {
        					    $response = array("status"=>TRUE,"message"=>"check in successfull");
        					} else {
        					    $response = array("status"=>TRUE,"message"=>"check out successfull");
        					}
        				} else {
        					if ($decode['type_check'] == 1) {
        					    $response = array("status"=>TRUE,"message"=>"check in not successfull");
        					} else {
        					    $response = array("status"=>TRUE,"message"=>"check out not successfull");
        					}
        				}
        			} 
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
                
            }
        }
        echo json_encode($response);    
    }
    
    public function StaffDashboardAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "staff_id"=>$decode['staff_id'],
                        "login_tokan"=>$decode['login_tokan'],
                    );
                    $response=$this->StaffDashboard($data);    
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    
    public function StaffDashboard($data) 
    {
        $minvalue = date('Y-m-d').' 00:00:00';
        $maxvalue = date('Y-m-d').' 23:59:59';
        // Check In Details
        $this->db->where('staff_id', $data['staff_id']);
        $this->db->where("date BETWEEN '$minvalue' AND '$maxvalue'");
        $UserDetails = $this->db->get(db_prefix().'check_in_out')->result_array();
        
        // Staff and Center Details
        $this->db->select('tblstaff.staffid,tblstaff.active,tblCenterMaster.*');
        $this->db->where('staffid', $data['staff_id']);
        $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblstaff.CenterID');
        $StaffDetails = $this->db->get(db_prefix().'staff')->row();
        
        if($UserDetails){
            $response=array("status"=>true,"message"=>"check in check out details", "data"=>$UserDetails,"StaffStatus"=>$StaffDetails->active,"StaffCenterDetails"=>$StaffDetails);
            return $response;
        }else{
            $response=array("status"=>true,"message"=>"Semething went wrong","data"=>$UserDetails,"StaffStatus"=>$StaffDetails->active,"StaffCenterDetails"=>$StaffDetails);
            return $response;
        }
    }
    
    public function GetLocationAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
        			$Tracking = array(
        			    "staffid"=>$decode['staff_id'],
        			    "TransDate"=>date('Y-m-d H:i:s'),
        			    "latitude"=>$decode['latitude'],
        			    "longitude"=>$decode['longitude'],
        			    "address"=>$decode['address'],
        			    "battery_level"=>$decode['battery_level'],
        			    "device_information"=>$decode['device_information'],
        			    "GPS_Status"=>$decode['GPS_Status'],
        			);
        			$re = $this->GetLocation($Tracking);
        			if($re){
        			    $response = array("status"=>true,"message"=>"location get successfull");
        			}else{
        			    $response = array("status"=>false,"message"=>"location not received");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","ss"=>$decode['longitude']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function GetLocation($Tracking)
    {
        $this->db->insert(db_prefix().'LocationTracking', $Tracking);
        $insert_id = $this->db->insert_id();
		if ($insert_id) {
		    return true;
		} 
		return false;
    }
    
    public function Getstaff_permissionAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
        			$staffid = $decode['staff_id'];
        			$staff_permission = $this->Getstaff_permission($staffid);
        			if($staff_permission){
        			    $response = array("status"=>true,"message"=>"Staff Wise Permission List","data"=>$staff_permission);
        			}else{
        			    $response = array("status"=>false,"message"=>"Permission Not found");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function Getstaff_permission($staffid)
    {
        $this->db->select('*');
        $this->db->where('staff_id',$staffid);
        $StaffPermission = $this->db->get(db_prefix().'staff_permissions')->result_array();
        return $StaffPermission;
    }
//========================= Get FPO Rate By Center, Item =======================
    public function FPORateAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "CenterID"=>$decode['CenterID'],
                        "ItemID"=>$decode['ItemID'],
                        "AccountID"=>$checkLoginTokan["AccountID"]
                    );
        			$FPORateDetails = $this->FPORate($data);
        			if($FPORateDetails){
        			    $response = array("status"=>true,"message"=>"Rate Details","data"=>$FPORateDetails);
        			}else{
        			    $response = array("status"=>false,"message"=>"Permission Not found");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function FPORate($data)
    {
        $this->db->select('tblFpoRateMaster.*');
        $this->db->where('CenterID',$data["CenterID"]);
        $this->db->where('ItemID',$data["ItemID"]);
        $this->db->where('FPOID',$data["AccountID"]);
        $this->db->where('Status',"Y");
        $FPORateDetails = $this->db->get(db_prefix().'FpoRateMaster')->row();
        return $FPORateDetails;
    }
//========================= Get Farmer Details By Mobile =======================
    public function FarmerDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$decode['MobileNumber']
                    );
        			$FarmerDetails = $this->FarmerDetails($data);
        			if($FarmerDetails){
        			    $response = array("status"=>true,"message"=>"Farmer Details","data"=>$FarmerDetails);
        			}else{
        			    $response = array("status"=>false,"message"=>"Permission Not found");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function FarmerDetails($data)
    {
        $this->db->select('tblclients.company,AccountID,ShortCode');
        $this->db->where('AccountID',$data["AccountID"]);
        $FarmerDetails = $this->db->get(db_prefix().'clients')->row();
        return $FarmerDetails;
    }
    
//========================= FPO Center List ====================================
    public function FPOCenterListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"]
                    );
        			$CentreList = $this->FPOCenterList($data);
        			if($CentreList){
        			    $response = array("status"=>true,"message"=>"CenterList","CenterList"=>$CentreList);
        			}else{
        			    $response = array("status"=>false,"message"=>"Center Not found. PLease connect to kirti admin");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function FPOCenterList($data)
    {
        $UserID = $data["AccountID"];
        $this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName');
		if(!is_admin()){
		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblCenterMaster.CenterID',"INNER");
	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
		}
		$this->db->order_by( db_prefix() .'CenterMaster.id','ASC');
		$CenterList = $this->db->get(db_prefix().'CenterMaster')->result_array();
		return $CenterList;
    }
    
//========================= FPO Item List ====================================
    public function FPOItemsListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"]
                    );
        			$FPOItemsList = $this->FPOItemsList($data);
        			if($FPOItemsList){
        			    $response = array("status"=>true,"message"=>"FPO Items List","ItemList"=>$FPOItemsList);
        			}else{
        			    $response = array("status"=>false,"message"=>"FPO Item not found. Please connect to kirti admin");
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function FPOItemsList($data)
    {
        $UserID = $data["AccountID"];
        $this->db->select('tblitems.ItemID,tblitems.ItemName');
        if(!is_admin()){
		    $this->db->join('tblstaff_wise_items', 'tblstaff_wise_items.ItemID = tblitems.ItemID');
	        $this->db->where('tblstaff_wise_items.AccountID', $UserID);
		}
        $this->db->where(db_prefix() . 'items.isactive', 'Y');
        $this->db->order_by('ItemName', 'ASC');
        return $this->db->get(db_prefix() . 'items')->result_array();
    }
    
//========================== Save FPO Order ====================================
    public function SaveFPOOrderAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "ItemID"=>$decode['ItemID'],
                        "CenterID"=>$decode['CenterID'],
                        "FPORate"=>$decode['FPORate'],
                        "OrderDetails"=>$decode['OrderDetails']
                    );
        			$response = $this->SaveFPOOrder($data);
        			/*if($FPOItemsList){
        			    $response = array("status"=>true,"message"=>"FPO Items List","ItemList"=>$FPOItemsList);
        			}else{
        			    $response = array("status"=>false,"message"=>"Permission Not found");
        			}*/
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function SaveFPOOrder($data)
    {
        $OrderDetails = $data["OrderDetails"];
        if($data["ItemID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Purchase Commodity");
        }else if($data["CenterID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Center");
        }else if($data["FPORate"] == ""){
            $response = array("status"=>false,"message"=>"FPO Rate is not assigned.Please connect to Kirti admin");
        }else if(empty($OrderDetails)){
            $response = array("status"=>false,"message"=>"Please add atlest one farmer details");
        }else{
            // Check Farmer Details
            $FarmerError = 0;
            $WeightError = 0;
            $BagError = 0;
            $RateError = 0;
            $QCDetailsError = 0;
            $QCParameterIDError = 0;
            $QCValueError = 0;
            $QCAmtError = 0;
            foreach($OrderDetails as $key=>$val){
                $QCDetails = $val["QcDetails"];
                if($val["FarmerID"] == ""){
                    $FarmerError++;
                }elseif($val["Weight"] == "" || $val["Weight"] <= 0){
                    $WeightError++;
                }elseif($val["Bag"] == "" || $val["Bag"] <= 0){
                    $BagError++;
                }elseif($val["FarmerRate"] == "" || $val["FarmerRate"] <= 0){
                    $RateError++;
                }else if(empty($QCDetails)){
                    $QCDetailsError++;
                    break;
                }
                foreach($QCDetails as $Qkey=>$Qval){
                    if($Qval["QCParameter_ID"] == ""){
                        $QCParameterIDError++;
                    }elseif($Qval["QCValue"] == ""){
                        $QCValueError++;
                    }elseif($Qval["QCAmt"] == ""){
                        $QCAmtError++;
                    }
                }
            }
            if($FarmerError > 0){
                $response = array("status"=>false,"message"=>"Please select farmer.");
            }elseif($WeightError > 0){
                $response = array("status"=>false,"message"=>"Please add Weight is greater than zero for each farmer.");
            }elseif($BagError > 0){
                $response = array("status"=>false,"message"=>"Please add Bag qty is greater than zero for each farmer.");
            }elseif($RateError > 0){
                $response = array("status"=>false,"message"=>"Please add rate for each farmer.");
            }elseif($QCDetailsError > 0){
                $response = array("status"=>false,"message"=>"Please add QC Parameter for each farmer.");
            }elseif($QCParameterIDError > 0){
                $response = array("status"=>false,"message"=>"Please add QC Parameter for each farmer.");
            }elseif($QCValueError > 0){
                $response = array("status"=>false,"message"=>"Please add QC Parameter Value for each farmer.");
            }elseif($QCAmtError > 0){
                $response = array("status"=>false,"message"=>"Please add QC Parameter Amt calculation Error. Please Connect to Admin");
            }else{
                $PartyDetails = $this->FpoOrderModel->GetPurchaseForParty($data["CenterID"],$data["ItemID"]);
                if($PartyDetails){
                    $PartyID = $PartyDetails->PartyID;
                }else{
                    $PartyID = "KOIL";
                }
                $PlantID = 1; 
                if ( date('m') <= 3 ) {
                    $FY = date('y') - 1;
                }else {
                    $FY = date('y');
                }
                $fpo_orderNumbar = get_option2('next_FPO_number_for_kirti',$FY);
                $new_fpo_orderNumbar = 'FPO'.$FY.$fpo_orderNumbar;   
                $FPOOrderMaster = array(
                    'PlantID'=>$PlantID,
                    'FY'=>$FY,
                    'OrderID' =>$new_fpo_orderNumbar,
                    'Transdate' =>date('Y-m-d H:i:s'),
                    'Transdate2' =>date('Y-m-d H:i:s'),
                    'FPOID'=>$data["AccountID"],
                    'CenterID'=>$data["CenterID"],
                    'ItemID'=>$data["ItemID"],
                    'FpoRate'=>$data["FPORate"],
                    'PartyID'=>$PartyID,
                    "UserID" =>$data["AccountID"],
                );
                $this->db->insert(db_prefix() . 'FpoOrderMaster',$FPOOrderMaster);
                if($this->db->affected_rows() > 0)
                {
                    $this->FpoOrderModel->increment_next_Fpo_Order_number();
                    foreach($OrderDetails as $key=>$val){
                        $QCDetails = $val["QcDetails"];
                        $TentativeWgt = $val['Weight'] + ($val['Bag']* 0.007);
                        $FpoOrderdetails = array(
                            'PlantID'=>$PlantID,
                            'FY'=>$FY,
                            'OrderID'=>$new_fpo_orderNumbar,
                            'Transdate'=>date('Y-m-d H:i:s'),
                            'Transdate2'=>date('Y-m-d H:i:s'),
                            'CenterID'=>$data["CenterID"],
                            'TType'=>'O',
                            'TType2'=>'ORDER',
                            'AccountID'=>$val['FarmerID'],
                            'NetWgt'=>$val['Weight'],
                            'Bag'=>$val['Bag'],
                            'Rate'=>$val['FarmerRate'],
                            'Deduction'=>$value['Deduction'],//
                            'NetRate'=>$value['NetRate'],//
                            'TentativeWgt'=>$TentativeWgt,
                            'Amount'=>$value['NetAmt']//
                        );
                        $this->db->insert(db_prefix() . 'FpoOrderDetails',$FpoOrderdetails);
                        $TotalDedAmt = 0;
                        foreach($QCDetails as $Qkey=>$QVal){
                            $insertData = [
                                'OrderID' => $new_fpo_orderNumbar,
                                'AccountID' => $val['FarmerID'],
                                'Parameter_ID' => $QVal['QCParameter_ID'],
                                'Qc_Value' => $QVal["QCValue"],
                                'Qc_Amt' => $QVal["QCAmt"],
                            ];
                            $this->db->insert('tblFpoQcDetail', $insertData);
                            $TotalDedAmt += $QVal["QCAmt"];
                        }
                        $DedPerQtl = $TotalDedAmt/$val['Weight'];
                        $NetRate = $val['FarmerRate'] - $DedPerQtl;
                        $NetAmt = $NetRate * $val['Weight'];
                        $OrderUpdate = array(
                            "Deduction"=>$TotalDedAmt, 
                            "NetRate"=>$NetRate,
                            "Amount"=>$NetAmt
                        );
                        $this->db->where('OrderID', $new_fpo_orderNumbar);
                        $this->db->where('AccountID', $val['FarmerID']);
                        $this->db->update(db_prefix() . 'FpoOrderDetails',$OrderUpdate);
                    }
                    $response = array("status"=>true,"message"=>"Order Generated Successfully.");
                }else{
                    $response = array("status"=>false,"message"=>"Order Place Error. Please Connect to Admin");
                }
            }
        }
        return $response;
    }
    
//========================== FPO Order List ====================================
    public function FPOOrderListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                    );
        			$response = $this->FPOOrderList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function FPOOrderList($data)
    {
        $selected_company = 1;
        $this->db->select('tblFpoOrderMaster.*,tblitems.ItemName,tblCenterMaster.CenterName');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = tblFpoOrderMaster.ItemID');	
		$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = tblFpoOrderMaster.CenterID');	
		//$this->db->where('tblFpoOrderMaster.FPOID', $data["AccountID"]);
		/*$this->db->where('tblK1stockmaster.CenterID', $CenterID);
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID');*/
		$FPOOrderList = $this->db->get(db_prefix() . 'FpoOrderMaster')->result_array();
		$response = array("status"=>true,"message"=>"FPO Order List","FPOOrderList"=>$FPOOrderList);
		return $response;
    }
    
//========================== Save New Farmer ===================================
    public function AddNewFarmerAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "FarmerMobile"=>$decode['FarmerMobile'],
                        "FarmerName"=>$decode['FarmerName'],
                        "AadhaarNo"=>$decode['AadhaarNo'],
                        "Pincode"=>$decode['Pincode'],
                        "VillageID"=>$decode['VillageID'],
                        "VillageName"=>$decode['VillageName'],
                        "Address"=>$decode['Address']
                    );
        			$response = $this->AddNewFarmer($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function AddNewFarmer($data)
    {
        if($data["FarmerMobile"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Farmer Mobile Number");
        }else if($data["FarmerName"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Farmer Name");
        }else if($data["Pincode"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Pincode");
        }else if($data["VillageID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Village");
        }else if($data["VillageID"] == "Add New Village" && $data["VillageName"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Village Name");
        }/*else if($data["State"] == ""){
            $response = array("status"=>false,"message"=>"State Name is required");
        }else if($data["District"] == ""){
            $response = array("status"=>false,"message"=>"District Name is required");
        }else if($data["Taluka"] == ""){
            $response = array("status"=>false,"message"=>"Taluka Name is required");
        }*/else{
            // Get Pincode Details
            $PincodeDetails = $this->GetPincodeDetails($data["Pincode"]);
            $StateCode = $PincodeDetails->State;
            $CityID = $PincodeDetails->District;
            $TalukaID = $PincodeDetails->Taluka;
            $next_code = $this->get_next_code('next_farmer_code');
            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
            $ShortCode = $number;
            if($data['VillageID'] == "Add New Village"){
                $VillageData = array(
                    "VisitDate"=>date("Y-m-d H:i:s"),
                    "VillageName"=>strtoupper($data['VillageName']),
                    "Pincode"=>$data['Pincode'],
                    "TalukaId"=>$TalukaID,
                    "DistrictId"=>$CityID,
                    "StateId"=>$StateCode,
                    "UserID"=>$data["AccountID"],
                    "datecreated"=>date("Y-m-d H:i:s"),
                );
                $this->db->insert(db_prefix().'villagedetails', $VillageData);
                $VillageID = $this->db->insert_id();
            }else{
                $VillageID = $data["VillageID"];
            }
            $Clients = array(
                "PlantID"=>1,
                "AccountID"=>$data["FarmerMobile"],
                "IsKirtiOneAccess"=>"N",
                "ShortCode"=>$ShortCode,
                "company"=>$data["FarmerName"],
                "CustomerType"=>1,
                "ActGroupID"=>"10000",
                "SubActGroupID1"=>"100002",
                "SubActGroupID"=>"1000006",
                "AccountFor"=>"Self",
                "phonenumber"=>$data["FarmerMobile"],
                "house"=>$data["Address"],
                "subdist"=>$TalukaID,
                "dist"=>$CityID,
                "state"=>$StateCode,
                "country"=>1,
                "zip"=>$data["Pincode"],
                "VillageID"=>$VillageID,
                "StartDate"=>date("Y-m-d H:i:s"),
                "datecreated"=>date("Y-m-d H:i:s"),
                "UserID"=>$data["AccountID"],
                "active"=>1,
                "ref_by"=>$data["AccountID"],
            );
            
            $this->db->insert(db_prefix().'clients', $Clients);
    		if($this->db->affected_rows() > 0){
    		    $Contactdata =array(
                    "PlantID"=>1,
                    "ref_by"=>$data["AccountID"],
                    "AccountID"=>$data['FarmerMobile'],
                    "firstname"=>$data["FarmerName"],
                    "phonenumber"=>$data["FarmerMobile"],
                    "aadhaar_number"=>$data["AadhaarNo"],
                    "datecreated"=>date('Y-m-d H:i:s'),
                );
                $this->db->insert(db_prefix().'contacts', $Contactdata);
    			$response = array("status"=>true,"message"=>"Record Inserted Successfully");
    		}else{
    		    $response = array("status"=>false,"message"=>"Something Went Wrong");
    		}
        }
        return $response;
    }
    
//========================== K1 Sale Item List =================================
    public function K1SaleItemListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "CenterID"=>$decode['CenterID'],
                        "CategoryType"=>$decode['CategoryType']
                    );
        			$response = $this->K1SaleItemList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1SaleItemList($data)
    {
        if($data["CenterID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Center");
        }else if($data["CategoryType"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Category Type");
        }else{
            $Itemist = $this->GetCategoryWiseItems($data);
            $response = array("status"=>true,"message"=>"Item List","ItemList"=>$Itemist);
        }
        return $response;
    }
    public function GetCategoryWiseItems($data)
	{
	    if( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }else {
            $fy = date('y');
        }
		$selected_company = 1;
		if($data["CategoryType"] == "Grocery"){
			$Category = array('6','8');
		}elseif($data["CategoryType"] == "Non Grocery"){
			$Category = array('1','2','3','7');
		}else{
			$Category = array();
		}
		$CenterID = $data["CenterID"];
		// Calculate Stock Available Items
	    // Get Opening Qty	
		$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalOQty, tblK1stockmaster.ItemID');
		$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1stockmaster.ItemID');	
		$this->db->where_in('tblproduct.Category', $Category);
		$this->db->where('tblK1stockmaster.CenterID', $CenterID);
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID');
		$OpnQtyItemWise = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		// Get Transaction itemwise
		$this->db->select('tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,tblK1history.TType2');
		$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID');	
		$this->db->where_in('tblproduct.Category', $Category);
		$this->db->where('tblK1history.CenterID', $CenterID);
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->group_by('tblK1history.ItemID,tblK1history.TType,tblK1history.TType2');
		$this->db->order_by('tblK1history.ItemID','ASC');
		$ItemWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
		
		
		
		$this->db->select('tblproduct.ProductID as id, CONCAT(tblproduct.ProductID," - ",tblproduct.ProductName) as label,tblproduct.ProductName ,ProductID');
		$this->db->from(db_prefix() . 'product'); 		
		$this->db->where_in(db_prefix() . 'product.Category', $Category);
		$ProductList = $this->db->get()->result_array();
		$FinalItemList = array();
		foreach($ProductList as $key=>$val){
		    $OQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
		    foreach($ItemWiseTransaction as $stockkey=>$stockval){
				if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
					$SaleQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
					$SaleRtnQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
					$PurchQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "PR" && $stockval["TType2"] == "PURCHASE RETURN"){
					$PurchRtnQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
					$InQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
					$OutQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
					$InwardQty += $stockval["TotalQty"];
				}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "X"){
					$AdjQty += $stockval["TotalQty"];
				}
			}
			// Opening Qty
			foreach($OpnQtyItemWise as $BatchOpnQty){
			    if($BatchOpnQty["ItemID"] == $val["ProductID"]){
			        $OQty = $BatchOpnQty["TotalOQty"];
				}
			}
			$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			if($BalQty > 0){
			    $new11 = array("id"=>$val["ProductID"],"label"=>$val["label"],"ProductName"=>$val["ProductName"],"ProductID"=>$val["ProductID"]);
		        array_push($FinalItemList,$new11);
			}
		}
		return $FinalItemList;
	}
//================== K1 Sale Item Details And Batch List =======================
    public function K1ItemDetailsAndBatchListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "CenterID"=>$decode['CenterID'],
                        "ItemID"=>$decode['ItemID'],
                        "PartyID"=>$decode['PartyID']
                    );
        			$response = $this->K1ItemDetailsAndBatchList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1ItemDetailsAndBatchList($data)
    {
        if($data["CenterID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Center");
        }else if($data["ItemID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Item");
        }else if($data["PartyID"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Party Mobile No");
        }else{
            $ItemDetails = $this->KirtiOneOrderModel->GetItemDetails($data["ItemID"],$data["CenterID"]);
			if(!empty($ItemDetails)){
			    $filterdata = [
    				'ItemID'=>$data["ItemID"],
    				'CenterID'=>$data["CenterID"],
    				'BatchID'=>"",
    			];
    			$ItemBatch = $this->GetItemBatchListWithStock($filterdata);
				$ItemDetails->BatchList = $ItemBatch;

                $currentRate = $this->ItemCurrentRate($filterdata);
                $ItemDetails->CurrentRate = ($currentRate == 0) ? $ItemDetails->rate : $currentRate;
			}
            $response = array("status"=>true,"message"=>"Item Details","ItemDetails"=>$ItemDetails);
        }
        return $response;
    }

    public function ItemCurrentRate($data){
        $ItemID = $data['ItemID'];
        $CenterID = $data['CenterID'];
        
        if(!empty($ItemID) && !empty($CenterID)){
            $this->db->select('rm.sale_rate');
            $this->db->from('tblK1RateMaster rm');
            $this->db->where('rm.ItemID', $ItemID);
            $this->db->where('rm.CenterID', $CenterID);
            $this->db->order_by('rm.TransDate', 'DESC');
            $this->db->limit(1);
            $rate = $this->db->get()->row_array();
            if(!empty($rate)){
                return $rate['sale_rate'];
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

//================== K1 Sale Item Batch Details ================================
    public function K1ItemBatchDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "CenterID"=>$decode['CenterID'],
                        "ItemID"=>$decode['ItemID'],
                        "BatchID"=>$decode['BatchID']
                    );
        			$response = $this->K1ItemBatchDetails($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1ItemBatchDetails($data)
    {
        if($data["CenterID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Center");
        }else if($data["ItemID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Item");
        }else if($data["BatchID"] == ""){
            $response = array("status"=>false,"message"=>"Please Please Select Batch");
        }else{
            $filterdata = [
				'ItemID'=>$data["ItemID"],
				'CenterID'=>$data["CenterID"],
				'BatchID'=>$data["BatchID"],
			];
			$ItemBatch = $this->GetItemBatchListWithStock($filterdata);
            $response = array("status"=>true,"message"=>"Item Batch Details","ItemBatchDetails"=>$ItemBatch);
        }
        return $response;
    }
    public function GetItemBatchListWithStock($filterdata)
	{
		if( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }else {
            $fy = date('y');
        }
		$selected_company = 1;
		
		// Batch List From Opening Stock
		$this->db->select('tblK1stockmaster.*');
		$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		if($filterdata["BatchID"]){
		    $this->db->where('tblK1stockmaster.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->group_by('tblK1stockmaster.BatchNo');
		$this->db->order_by('tblK1stockmaster.ExpDate','ASC');
		$OpnQtyBatchList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		
		// Batch List From History
		$this->db->select('tblK1history.BatchNo,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType, 
		tblK1history.TType2,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.CaseQty');
		$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1history.CenterID', $filterdata["CenterID"]);
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.FY', $fy);
		if($filterdata["BatchID"]){
		    $this->db->where('tblK1history.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->group_by('tblK1history.BatchNo,TType,TType2');
		$this->db->order_by('tblK1history.ExpDate','ASC');
		$BatchWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
		$response = array();
		$batch = array();
		foreach($OpnQtyBatchList as $val){
		    array_push($batch,$val["BatchNo"]);
		}
		foreach($BatchWiseTransaction as $val1){
		    if($val1["BatchNo"] != "" && $val1["BatchNo"] != NULL){
		        array_push($batch,$val1["BatchNo"]);
			}
		}
		$UniqueBatchList = array_unique($batch);
		foreach($UniqueBatchList as $key=>$batchval){
		    $ExpDate = "";
		    $PurchRate = 0;
		    $OQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
			
			$isPurch = false;
		    foreach($BatchWiseTransaction as $stockkey=>$stockval){
				if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
					$SaleQty += ($stockval["TotalQty"]);
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN"){
					$SaleRtnQty += ($stockval["TotalQty"]);
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
					$PurchQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"],0,10));
					$PurchRate = $stockval["PurchRate"];
					$isPurch = true;
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN"){
					$PurchRtnQty += ($stockval["TotalQty"]);
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
					$InQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"],0,10));
					$PurchRate = $stockval["PurchRate"];
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
					$OutQty += $stockval["TotalQty"];
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
					$InwardQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"],0,10));
					$PurchRate = $stockval["PurchRate"];
				}else if($stockval["BatchNo"] == $batchval && $stockval["TType"] == "X"){
					$AdjQty += ($stockval["TotalQty"]);
				}
			}
			// Opening Qty
			foreach($OpnQtyBatchList as $BatchOpnQty){
			    if($BatchOpnQty["BatchNo"] == $batchval){
			        $OQty = $BatchOpnQty["OQty"];
			        $ExpDate = _d(substr($BatchOpnQty["ExpDate"],0,10));
					if(!$isPurch){
						$PurchRate = $BatchOpnQty["PurchRate"];
					}
				}
			}
			$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			//return $batchval."=".$OQty."=".$PurchQty."=".$SaleQty;
			if($BalQty > 0){
			    $new11 = array("BatchNo"=>$batchval,"Stock"=>$BalQty,"ExpDate"=>$ExpDate,"PurchRate"=>$PurchRate);
		        array_push($response,$new11);
			}
		}
		return $response;
	}
//================== K1 Sale Online Ledger List ================================
    public function K1SaleOnlineLedgerAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {   
                    $SubActGroupID = "1000017";
        			$response = $this->K1SaleLedgerOnlineList($SubActGroupID);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    public function K1SaleLedgerOnlineList($SubActGroupID)
    {
        $this->db->select('tblclients.company,tblclients.AccountID');
		$this->db->where('tblclients.SubActGroupID', $SubActGroupID);
		$this->db->order_by('tblclients.company','ASC');
		$OnlineLedgerList = $this->db->get(db_prefix() . 'clients')->result_array();
		
        $response = array("status"=>true,"message"=>"Online Ledger List","OnlineLedgerList"=>$OnlineLedgerList);
        return $response;
    }
//================== K1 Sale Other Ledger List ================================
    public function K1SaleOtherLedgerAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {   
                    $ActGroupID = "10011";
        			$response = $this->K1SaleOtherLedgerList($ActGroupID);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    public function K1SaleOtherLedgerList($ActGroupID)
    {
        $this->db->select('tblclients.company,tblclients.AccountID');
		$this->db->where('tblclients.ActGroupID', $ActGroupID);
		$this->db->order_by('tblclients.company','ASC');
		$OtherLedgerList = $this->db->get(db_prefix() . 'clients')->result_array();
		
        $response = array("status"=>true,"message"=>"Other Ledger List","OtherLedgerList"=>$OtherLedgerList);
        return $response;
    }
//================ Save K1 Sale Order Lisr =====================================
    public function K1SaleOrderListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"]
                    );
        			$response = $this->K1SaleOrderList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1SaleOrderList($data)
    {
        $this->db->select('tblK1ordermaster.OrderID,tblK1ordermaster.Transdate,tblK1ordermaster.VillageName,tblK1ordermaster.OrderAmt,tblK1ordermaster.BIllNo,
        tblCenterMaster.CenterName,tblclients.company,tblK1ordermaster.AccountID');
        $this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1ordermaster.AccountID');
        $this->db->join(db_prefix() . 'CenterMaster', 'tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
		$this->db->where('tblK1ordermaster.UserID', $data["AccountID"]);
		$this->db->order_by('tblK1ordermaster.Transdate','DESC');
		$OrderList = $this->db->get(db_prefix() . 'K1ordermaster')->result_array();
        $response = array("status"=>true,"message"=>"Order List","OrderList"=>$OrderList);
        return $response;
    }

    // ================ Save K1 Sale Order Details =====================================
    
    public function K1SaleOrderDetailsAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        'OrderID'=>$decode['OrderID']
                    );
        			$response = $this->K1SaleOrderDetails($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1SaleOrderDetails($data){
        $sql = "SELECT 
            -- Order Details
            kom.OrderID,
            kom.ChallanID,
            kom.SalesID,
            kom.Transdate,
            kom.AccountID,
            kom.CenterID,
            kom.GSTNO,
            kom.VillageName,
            kom.saleamt,
            kom.Discamt,
            kom.cgstamt,
            kom.sgstamt,
            kom.igstamt,
            kom.RoundOffAmt,
            kom.Invamt,

            -- Center Details
            cm.CenterName,
            cms.state_name AS CenterState,
            cmc.city_name AS CenterCity,
            cm.address AS CenterAddress,
            cm.pincode AS CenterPincode,
            cm.GSTNo AS CenterGST,

            -- Customer Details
            c.company AS CustomerName,
            c.house AS CustomerAddress,
            cs.state_name AS CustomerState,
            cc.city_name AS CustomerCity,
            c.zip AS CustomerPincode,

            -- Order Items (grouped)
            (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'ItemID', h.ItemID,
                        'BilledQty', h.BilledQty,
                        'BatchNo', h.BatchNo,
                        'ExpDate', h.ExpDate,
                        'PurchRate', h.PurchRate,
                        'SaleRate', h.SaleRate,
                        'BasicRate', h.BasicRate,
                        'SuppliedIn', h.SuppliedIn,
                        'OrderQty', h.OrderQty,
                        'DiscPerc', h.DiscPerc,
                        'DiscAmt', h.DiscAmt,
                        'cgst', h.cgst,
                        'cgstamt', h.cgstamt,
                        'sgst', h.sgst,
                        'sgstamt', h.sgstamt,
                        'igst', h.igst,
                        'igstamt', h.igstamt,
                        'CaseQty', h.CaseQty,
                        'Cases', h.Cases,
                        'OrderAmt', h.OrderAmt,
                        'ChallanAmt', h.ChallanAmt,
                        'NetOrderAmt', h.NetOrderAmt,
                        'NetChallanAmt', h.NetChallanAmt,
                        'ProductName', p.ProductName,
                        'hsn_code', p.hsn_code,
                        'Measuredin', p.unit,
                        'PackingQty', p.PackingQty,
                        'Packingwgt', p.PackingWeight,
                        'SaleUnit', h.SuppliedIn,
                        'Discount', h.DiscAmt,
                        'Netamt', h.NetOrderAmt,
                        'id', h.ItemID,
                        'gst', t.taxrate,
                        'Brand', b.BrandName
                    )
                )
                FROM ".db_prefix()."K1history h
                LEFT JOIN ".db_prefix()."product p ON p.ProductID = h.ItemID AND p.PlantID = h.PlantID
                LEFT JOIN ".db_prefix()."taxes t ON t.id = p.gst
                LEFT JOIN ".db_prefix()."brands b ON b.id = p.BrandId
                WHERE h.OrderID = kom.OrderID
                AND h.BillID IS NULL
                AND h.TransID IS NULL
            ) AS OrderHistory

        FROM ".db_prefix()."K1ordermaster kom
        LEFT JOIN ".db_prefix()."clients c ON c.AccountID = kom.AccountID
        LEFT JOIN ".db_prefix()."xx_citylist cc ON cc.id = c.dist
        LEFT JOIN ".db_prefix()."xx_statelist cs ON cs.short_name = c.state
        LEFT JOIN ".db_prefix()."CenterMaster cm ON cm.CenterID = kom.CenterID
        LEFT JOIN ".db_prefix()."xx_citylist cmc ON cmc.id = cm.city
        LEFT JOIN ".db_prefix()."xx_statelist cms ON cms.short_name = cm.state

        WHERE kom.OrderID = '".$data["OrderID"]."'
        LIMIT 1";

        $query = $this->db->query($sql);
        $result = $query->row_array();
        $orderHistory = json_decode($result['OrderHistory'], true);
        unset($result['OrderHistory']);

        return [
            "status"        => true,
            "message"       => "Order Details",
            "OrderDetails"  => $result,
            "OrderHistory"  => $orderHistory
        ];
    }

// ================ Save K1 Reminders Details =====================================
    public function K1SaveRemindersAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        'CenterID'=>$decode['CenterID'],
                        'ReminderDate'=>$decode['ReminderDate'],
                        'ReminderNote'=>$decode['ReminderNote'],
                        'Priority'=>$decode['Priority']
                    );
        			$response = $this->K1SaveReminders($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }

    public function K1SaveReminders($data){
        if(empty($data['ReminderNote']) || count($data['ReminderNote']) <= 0){
            return ["status" => false, "message" => "Reminder Note Required"];
        }
        if(empty($data['ReminderDate'])){
            return ["status" => false, "message" => "Reminder Date Required"];
        }
        if(empty($data['CenterID'])){
            return ["status" => false, "message" => "Center ID Required"];
        }

        $retrunData = [];
        foreach($data['ReminderNote'] as $key => $value){
            $saveData = [
                'CenterID' => $data['CenterID'],
                'ReminderDate' => $data['ReminderDate'],
                'ReminderName' => $value,
                'Priority' => $data['Priority'],
                'TransDate' => date('Y-m-d H:i:s'),
                'UserID' => $data["AccountID"],
                'Lupdate' => date('Y-m-d H:i:s'),
                'UserID2' => $data["AccountID"]
            ];
            // check first if already exists
            $this->db->where('ReminderName', $value);
            $this->db->where('CenterID', $data['CenterID']);
            $this->db->where('ReminderDate', $data['ReminderDate']);
            $checkReminder = $this->db->get(db_prefix().'ReminderMaster')->row();
            if($checkReminder){
                continue;
            }

            $this->db->insert(db_prefix().'ReminderMaster', $saveData);
            $reminderID = $this->db->insert_id();
            $saveData['ReminderID'] = $reminderID;
            $retrunData[] = $saveData;
        }
        if(count($retrunData) > 0){
            return ["status" => true, "message" => "Reminder Saved Successfully", "ReminderID" => $retrunData];
        }else{
            return ["status" => false, "message" => "Reminder Not Saved"];
        }
    }

    // ================ Save K1 Reminders list self =====================================
    public function K1ListRemindersAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"]
                    );
        			$response = $this->K1ListReminders($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }

    public function K1ListReminders($data){
        $this->db->where('UserID', $data['AccountID']);
        $this->db->where('ReminderDate >=', date('Y-m-d'));
        $this->db->order_by('ReminderDate', 'ASC');
        $list = $this->db->get(db_prefix().'ReminderMaster')->result_array();
        if(empty($list)){
            return ["status" => false, "message" => "Reminder list not found"];
        }else{
            return ["status" => true, "message" => "Reminder list found", "data" => $list];
        }
    }

    // ================== Expired stock list ========================
    public function ExpiredStockListAPI($param=FALSE) 
    {
        $response = array();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                // $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                // if($checkLoginTokan)
                // {
                    $data = array(
                        // "AccountID"=>$checkLoginTokan["AccountID"],
                        "CenterID" => $decode['CenterID'] ?? '',
                        "PartyID" => $decode['PartyID'] ?? '',
                        "ItemGroup" => $decode['ItemGroup'] ?? '',
                        "DaysFilter" => $decode['Days'] ?? 10
                    );
        			$response = $this->ExpiredStockList($data);
                // }else{
                //     $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                // }
            }
        }
        echo json_encode($response);   
    }

    public function ExpiredStockList($data){
        $CenterID   = $data['CenterID'] ?? '';
        $PartyID    = $data['PartyID'] ?? '';
        $ItemGroup  = $data['ItemGroup'] ?? '';
        $DaysFilter = $data['Days'] ?? 10;

        $this->db->select('tblK1history.*,tblproduct.ProductName');
        $this->db->from('tblK1history');
        $this->db->join('tblproduct','tblproduct.ProductID = tblK1history.ItemID','inner');

        // Center filter
        if (!empty($CenterID)) {
            $CenterID = is_array($CenterID) ? $CenterID : [$CenterID];
            $this->db->where_in('tblK1history.CenterID', $CenterID);
        }

        // Party filter
        if (!empty($PartyID)) {
            $PartyID = is_array($PartyID) ? $PartyID : [$PartyID];
            $this->db->where_in('tblK1history.PartyID', $PartyID);
        }

        // Item Group filter
        if (!empty($ItemGroup)) {
            $ItemGroup = is_array($ItemGroup) ? $ItemGroup : [$ItemGroup];
            $this->db->where_in('tblproduct.Subcategory', $ItemGroup);
        }

        if ($DaysFilter !== '' && $DaysFilter !== null) {
            $DaysFilter = (int)$DaysFilter;
            $currentDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime($currentDate . ' +' . $DaysFilter . ' days'));

            $this->db->where("
                CASE
                    WHEN tblK1history.ExpDate LIKE '%/%'
                        THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')

                    WHEN tblK1history.ExpDate LIKE '%:%'
                        THEN DATE(tblK1history.ExpDate)

                    ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
                END BETWEEN " . $this->db->escape($currentDate) . "
                AND " . $this->db->escape($endDate) . "
            ", null, false);
        }

        $this->db->order_by("
            CASE
                WHEN tblK1history.ExpDate IS NULL
                    OR tblK1history.ExpDate = ''
                THEN 1
                ELSE 0
            END
        ", '', false);

        $this->db->order_by("
            CASE
                WHEN tblK1history.ExpDate LIKE '%/%'
                    THEN STR_TO_DATE(tblK1history.ExpDate, '%d/%m/%Y')

                WHEN tblK1history.ExpDate LIKE '%:%'
                    THEN DATE(tblK1history.ExpDate)

                ELSE STR_TO_DATE(tblK1history.ExpDate, '%Y-%m-%d')
            END
        ", '', false);

        $query = $this->db->get();
        $GetHistoryData = $query->result_array();

        $BatchList = [];
        foreach ($GetHistoryData as $row) {
            if (empty($row['BatchNo'])) {
                continue;
            }

            $ItemID   = $row['ItemID'];
            $BatchNo  = $row['BatchNo'];
            $Center   = $row['CenterID'];
            $ExpDate  = $row['ExpDate'];

            if (!empty($ExpDate)) {
                if (strpos($ExpDate, '/') !== false) {
                    $timestamp = strtotime(str_replace('/', '-', $ExpDate));
                } else {
                    $timestamp = strtotime($ExpDate);
                }

                $NormalizedExpDate = $timestamp ? date('Y-m-d', $timestamp) : '';
            } else {
                $NormalizedExpDate = '';
            }

            $BatchKey = $ItemID . '_' . $BatchNo . '_' . $Center;
            if (!isset($BatchList[$BatchKey])) {
                $BatchList[$BatchKey] = [
                    'ItemID'      => $ItemID,
                    'BatchNo'     => $BatchNo,
                    'ExpDate'     => $NormalizedExpDate,
                    'ProductName' => $row['ProductName'],
                    'CenterID'    => $Center,

                    'OpeningQty'  => 0,
                    'InwardQty'   => 0,
                    'PurchQty'    => 0,
                    'PurchRtnQty' => 0,
                    'SaleQty'     => 0,
                    'SaleRtnQty'  => 0,
                    'PrdQty'      => 0,
                    'IssueQty'    => 0,
                    'AdjQty'      => 0,
                    'InQty'       => 0,
                    'OutQty'      => 0
                ];
            }

            $Qty = (float)$row['BilledQty'];
            // Sale
            if ( $row['TType'] == 'O' && $row['TType2'] == 'SALE' ) {
                $BatchList[$BatchKey]['SaleQty'] += $Qty;
            }

            // Fresh Sale Return
            elseif ( $row['TType'] == 'SR' && $row['TType2'] == 'FRESH RETURN' ) {
                $BatchList[$BatchKey]['SaleRtnQty'] += $Qty;
            }

            // Purchase
            elseif ( $row['TType'] == 'P' && $row['TType2'] == 'Purchase' ) {
                $BatchList[$BatchKey]['PurchQty'] += $Qty;
            }

            // Purchase Return
            elseif ( $row['TType'] == 'PR' && $row['TType2'] == 'PURCHASE RETURN' ) {
                $BatchList[$BatchKey]['PurchRtnQty'] += $Qty;
            }

            // Transfer IN
            elseif ( $row['TType'] == 'T' && $row['TType2'] == 'IN' ) {
                $BatchList[$BatchKey]['InQty'] += $Qty;
            }

            // Transfer OUT
            elseif ( $row['TType'] == 'T' && $row['TType2'] == 'OUT' ) {
                $BatchList[$BatchKey]['OutQty'] += $Qty;
            }

            // Purchase Inward
            elseif ( $row['TType'] == 'I' && $row['TType2'] == 'INWARD' ) {
                $BatchList[$BatchKey]['InwardQty'] += $Qty;
            }

            // Adjustment
            elseif ($row['TType'] == 'X') {
                $BatchList[$BatchKey]['AdjQty'] += $Qty;
            }
        }

        $FinalData = [];
        foreach ($BatchList as $Batch) {

            $StockQty =
                $Batch['OpeningQty']
                + $Batch['InwardQty']
                + $Batch['PurchQty']
                - $Batch['PurchRtnQty']
                - $Batch['SaleQty']
                + $Batch['SaleRtnQty']
                + $Batch['PrdQty']
                - $Batch['IssueQty']
                - $Batch['AdjQty']
                + $Batch['InQty']
                - $Batch['OutQty'];

            if ((float)$StockQty == 0) {
                continue;
            }

            $FinalData[] = [
                'ItemID'      => $Batch['ItemID'],
                'ProductName' => $Batch['ProductName'],
                'CenterID'    => $Batch['CenterID'],
                'BatchNo'     => $Batch['BatchNo'],
                'ExpDate'     => $Batch['ExpDate'],
                'StockQty'    => round($StockQty, 2)
            ];
        }

        if(empty($FinalData)){
            return ["status" => false, "message" => "List not found"];
        }else{
            return ["status" => true, "message" => "List found", "data" => $FinalData];
        }
    }
    
//================ Save K1 Sale Order Order ====================================
    public function K1SaleOrderSaveAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan)
                {
                    $data = array(
                        "AccountID"=>$checkLoginTokan["AccountID"],
                        "FarmerID"=>$decode["FarmerID"],
                        "CenterID"=>$decode['CenterID'],
                        "CategoryType"=>$decode['CategoryType'],
                        "OrderType"=>$decode['OrderType'],
                        "VillageName"=>$decode['VillageName'],
                        "BillNo"=>$decode['BillNo'],
                        "OtherAmt"=>$decode['OtherAmt'],
                        "OtherAmtLedger"=>$decode['OtherAmtLedger'],
                        "CashAmt"=>$decode['CashAmt'],
                        "OnlineAmt"=>$decode['OnlineAmt'],
                        "OnlineAmtLedger"=>$decode['OnlineAmtLedger'],
                        "Ref_no"=>$decode['Ref_no'],
                        "ItemDetails"=>$decode['ItemDetails']
                    );
        			$response = $this->K1SaleOrderSave($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number","phonenumber"=>$decode['phonenumber']);
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function K1SaleOrderSave($data)
    {   
        $OrderItemList = $data["ItemDetails"];
        $OrderType = $data["OrderType"];
        if($data["FarmerID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Farmer");
        }else if($data["CenterID"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Center");
        }else if($data["CategoryType"] == ""){
            $response = array("status"=>false,"message"=>"Please Select to Category Type");
        }else if($data["OrderType"] == ""){
            $response = array("status"=>false,"message"=>"Please Select to Order Type");
        }else if($data["VillageName"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Village Name");
        }else if($data["OtherAmt"] > 0 && $data["OtherAmtLedger"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Other Amt Ledger");
        }else if($data["OnlineAmt"] > 0 && $data["OnlineAmtLedger"] == ""){
            $response = array("status"=>false,"message"=>"Please Select Online Amt Ledger");
        }else if($data["OnlineAmt"] > 0 && $data["Ref_no"] == ""){
            $response = array("status"=>false,"message"=>"Please Enter Ref_no");
        }else if(empty($OrderItemList)){
            $response = array("status"=>false,"message"=>"Please add atlest one item");
        }else{
            if($OrderType == '') {
				$InvoiceType = "CREDIT";              
			} else {
				$InvoiceType = "CASH";   
			}
            $OtherAmt = $data["OtherAmt"];
            $OthEffectOn = $data["OtherAmtLedger"];
            $UserID = $data["AccountID"];
            $ReceiptAmt = $data["CashAmt"] + $data["OnlineAmt"];
            $clients = $this->KirtiOneOrderModel->GetAccountDetails($data["FarmerID"]);
    		$GSTIN = NULL;
    		if($clients['gstin']){
    		    $GSTIN = $clients['gstin'];
    		}
    		$PartyState = "MH";
    		if($clients['state']){
    		    $PartyState = $clients['state'];
    		}
    		$CenterData = $this->KirtiOneOrderModel->GetCenterByCenterID($data["CenterID"]);
    		$selected_company = 1; 
            if ( date('m') <= 3 ) {
                $fy = date('y') - 1;
            }else {
                $fy = date('y');
            }
    		$nextK1OrderNumber = get_option2('next_K1Order_number_for_kirti',$fy); 
			$OrderID = "ORD".$fy.$nextK1OrderNumber;
			$nextChallannumber = get_option2('next_K1Challan_number_for_kirti',$fy); 
			$prefixchallan = "CHL";
			$kirtione= 1;
			$ConcatenatedChallanNumber = $prefixchallan . $fy . $selected_company . $kirtione . $nextChallannumber;
			
			$nextTaxNumber = get_option2('next_K1Tax_number_for_kirti',$fy); 
			$prefixTaxNo = "TAX";
			$ConcatenatedTaxNumber = $prefixTaxNo . $fy . $selected_company . $kirtione . $nextTaxNumber;
			$SalesId = $ConcatenatedTaxNumber;  
			
            $ItemListArray = array();
            $OrdSaleAmt = 0;$OrdDiscAmt = 0;$OrdCgstAmt = 0;$OrdSgstAmt = 0;$OrdIgstAmt = 0;$OrdNetAmt = 0;$OrdRoundOffAmt = 0;
    		$ItemCount = 0;$ordno = 1;
    		foreach ($OrderItemList as $index => $row) 
    		{           
    			if (!empty($row['ItemID'])) 
    			{ 
    			    $ItemCount++;
    			    $ItemID = $row['ItemID'];
    			    $ItemDetails = $this->CheckItemFor($ItemID);
    			    
    				$Qty = $row['SaleQty'];
    				$Unit = $ItemDetails->unit;
    				$SaleUnit = $row['SaleUnit']; 
    				$PackQty = $row['PackQty']; 
    				$DiscAmt = $row['DiscAmt'];
    				$BasicRate = $row['SaleRate'];
    				//$NewBasicRate = $row['Amount'];
                    $isGlossary = in_array($row['Category'], [6, 8]);
                    $isGlocery  = ($item['Category'] === 'Glocery');

                    if ($isGlocery xor $isGlossary) {
                        $response = array("status"=>false,"message"=>"Item Category Should be same");
                        goto end;
                    }

    				$BatchNo = $row['BatchID'];
    				$ExpDate = to_sql_date($row['BatchExpDate']);
    				$ItemTotal = 0;$TotalDisc = 0;$TaxableAmt = 0;$DiscPer = 0;$CgstPer = 0;$CgstAmt = 0;$SgstPer = 0;$SgstAmt = 0;$IgstPer = 0;$IgstAmt = 0;
    				if($DiscAmt >0){
    					$DiscPer = ($DiscAmt/$BasicRate)*100;
    				}
    				if($Unit == $SaleUnit){
    				    $NewBasicRate = $BasicRate / $PackQty;
    					$BilledQty = $Qty * $PackQty;
    					$CaseQty = $PackQty;
    				}else{
    					$BilledQty = $Qty;
    					$NewBasicRate = $BasicRate;
    					$CaseQty = 1;
    				}
    				if($row['GSTType'] == "Including"){
    				    $SaleRate = $NewBasicRate;
    				}else{
    				    $SaleRate = $NewBasicRate+($NewBasicRate*($row['GSTPer']/100));
    				}
    				$TotalDisc = $DiscAmt * $Qty;
    				$ItemTotal = $NewBasicRate * $BilledQty;
    				
    				$TaxableAmt = $ItemTotal - $TotalDisc;
    				if($row['GSTType'] == "Excluding"){
    				    if($CenterData->state == $PartyState){
    						$CgstPer = $row['GSTPer']/2;
    						$SgstPer = $row['GSTPer']/2;
    						$CgstAmt = $TaxableAmt * ($CgstPer / 100); 
    						$SgstAmt = $TaxableAmt * ($SgstPer / 100); 
    					}else{
    						$IgstPer = $row['GSTPer'];
    						$IgstAmt = $TaxableAmt * ($IgstPer / 100); 
    					}
    				}else{
    				    if($CenterData->state == $PartyState){
    						$CgstPer = $row['GSTPer']/2;
    						$SgstPer = $row['GSTPer']/2;
    					}else{
    						$IgstPer = $row['GSTPer'];
    					}
    				}
    				$ItemNetAmt = $TaxableAmt + $CgstAmt+ $SgstAmt+ $IgstAmt;
    				$OrdSaleAmt += $ItemTotal;
    				$OrdDiscAmt += $TotalDisc;
    				$OrdCgstAmt += $CgstAmt;
    				$OrdSgstAmt += $SgstAmt;
    				$OrdIgstAmt += $IgstAmt;
    				$OrdNetAmt  += $ItemNetAmt;
    				$itemDetails = array(
    				'PlantID'=>$selected_company,
    				'FY'=>$fy,
    				'OrderID'=>$OrderID,
    				'BillID'=>$ConcatenatedChallanNumber,
    				'TransID'=>$SalesId,
    				'TransDate'=>date('Y-m-d H:i:s'), 
    				'TransDate2'=>date('Y-m-d H:i:s'),
    				'TType'=>"O",
    				'TType2'=>"SALE",
    				'AccountID'=>$data["FarmerID"],
    				'ItemID'=>$ItemID,
    				'CenterID'=>$data["CenterID"],
    				'GodownID'=>'',
    				'PartyID'=>"KASPL",
    				'ChamberID'=>'',
    				'StackID'=>'',
    				'LOTID'=>'',
    				'PurchRate'=>$NewBasicRate,
    				'SaleRate'=>$SaleRate,
    				'BasicRate'=>$NewBasicRate,
    				'SuppliedIn'=>$Unit,
    				'OrderQty'=>$BilledQty,
    				'eOrderQty'=>'',
    				'BilledQty'=>$BilledQty,
    				'DiscPerc'=>$DiscPer,
    				'DiscAmt'=>$TotalDisc,
    				'cgst'=>$CgstPer,
    				'cgstamt'=>$CgstAmt,
    				'sgst'=>$SgstPer,
    				'sgstamt'=>$SgstAmt,
    				'igst'=>$IgstPer,
    				'igstamt'=>$IgstAmt,
    				'CaseQty'=>$CaseQty,
    				'Cases'=>0.00,
    				'OrderAmt'=>$ItemTotal,
    				'ChallanAmt'=>$ItemTotal,
    				'NetOrderAmt'=>$ItemNetAmt,
    				'NetChallanAmt'=>$ItemNetAmt,
    				'BatchNo'=>$BatchNo,
    				'ExpDate'=>$ExpDate,
    				'Ordinalno'=>$ordno,
    				'rowid'=>0,
    				'UserID'=>$UserID,
    				'cnfid'=>'',                          
    				'reason'=>''
    				);
    				
    				array_push($ItemListArray,$itemDetails);
    				//$this->db->insert(db_prefix() . 'K1history', $itemDetails);
    				$ordno++;
    				
    				//LeanMark Entry
    				if($ItemDetails->ItemFor !='KASPL')
    				{
    				    $ItemEntry = array(
        					'PlantID'=>$selected_company,
        					'FY'=>$fy,
        					'OrderID'=>$OrderID,
        					'BillID'=>$ConcatenatedChallanNumber,
        					'TransID'=>$SalesId,
        					'TransDate'=>date('Y-m-d H:i:s'), 
        					'TransDate2'=>date('Y-m-d H:i:s'),
        					'TType'=>"L",
        					'TType2'=>"LIENMARK",
        					'AccountID'=>$data["FarmerID"],
        					'ItemID'=>$ItemID,
        					'CenterID'=>$data["CenterID"],
        					'GodownID'=>'',
        					'PartyID'=>$ItemDetails->ItemFor,
        					'ChamberID'=>'',
        					'StackID'=>'',
        					'LOTID'=>'',
        					'PurchRate'=>$NewBasicRate,
        					'SaleRate'=>$SaleRate,
        					'BasicRate'=>$NewBasicRate,
        					'SuppliedIn'=>$Unit,
        					'OrderQty'=>$BilledQty,
        					'eOrderQty'=>'',
        					'BilledQty'=>$BilledQty,
        					'DiscPerc'=>$DiscPer,
        					'DiscAmt'=>$TotalDisc,
        					'cgst'=>$CgstPer,
        					'cgstamt'=>$CgstAmt,
        					'sgst'=>$SgstPer,
        					'sgstamt'=>$SgstAmt,
        					'igst'=>$IgstPer,
        					'igstamt'=>$IgstAmt,
        					'CaseQty'=>$CaseQty,
        					'Cases'=>0.00,
        					'OrderAmt'=>$ItemTotal,
        					'ChallanAmt'=>$ItemTotal,
        					'NetOrderAmt'=>$ItemNetAmt,
        					'NetChallanAmt'=>$ItemNetAmt,
        					'BatchNo'=>$BatchNo,
        					'ExpDate'=>$ExpDate,
        					'Ordinalno'=>$ordno,
        					'rowid'=>0,
        					'UserID'=>$UserID,
        					'cnfid'=>'',                          
        					'reason'=>''
    					);
    					array_push($ItemListArray,$ItemEntry);
    					//$this->db->insert(db_prefix() . 'K1history', $ItemEntry);
    					$ordno++;
    				}
    			}
    		}
    		if($OrdNetAmt > $ReceiptAmt && $OrderType == "1"){
    		    $response = array("status"=>false,"message"=>"Order Amt is greter than Cash and Online Amt");
    		}elseif($OrdNetAmt < $ReceiptAmt && $OrderType == "1"){
    		    $response = array("status"=>false,"message"=>"Order Amt is less than Cash and Online Amt");
    		}elseif($OrdNetAmt == "0"){
    		    $response = array("status"=>false,"message"=>"Order Amt is zero, Please add Atleast one item");
    		}else{
    		    if($OtherAmt > 0){
				    $OrdNetAmt += $OtherAmt;
				}
				$BillAmt = round($OrdNetAmt);
				$OrdRoundOffAmt = $BillAmt - $OrdNetAmt;
				$SaleLedgerAmount = $OrdSaleAmt; 
    		    $insert_order = array(    
    				'PlantID'=>$selected_company,
    				'FY'=>$fy,
    				'OrderID'=>$OrderID,  
    				'IsDirectSale'=>'Y',
    				'ChallanID'=>$ConcatenatedChallanNumber,
    				'SalesID'=>$SalesId,   
    				'Transdate'=>date('Y-m-d H:i:s'), 
    				'AccountID'=>$data["FarmerID"],
    				'CenterID'=>$data["CenterID"],
    				'GSTNO'=>$GSTIN,
    				'VillageName'=>$data["VillageName"],
    				'OrderWeight'=>'0.00',
    				'OrderStatus'=>"F",           
    				'OrderType'=>"TAXITEMS",
    				'UserID'=>$UserID,
    				'OrderPaymentType'=>$OrderType,
    				'BIllNo'=>$data["BillNo"],
    				'billstate'=>$PartyState,
    				'CategoryType'=>$data["CategoryType"],
    				'DeliveryType'=>1,
    				'order_type'=>"APP",  
    				'OrderAmt'=>$BillAmt
                );
				if($this->db->insert(db_prefix() . 'K1ordermaster', $insert_order))
				{     
				    $this->KirtiOneOrderModel->increment_next_number('next_K1Order_number_for_kirti'); 
				     $this->db->insert_batch('tblK1history', $ItemListArray);
					//insert in sales table
					$add_entry_sales = array(
    					'PlantID'=>$selected_company,
    					'FY'=>$fy,
    					'BT'=>'T',
    					'InvoiceType'=>$InvoiceType,
    					'SalesID'=>$SalesId,
    					'Transdate'=>date('Y-m-d H:i:s'), 
    					'OrderID'=>$OrderID,
    					'ChallanID'=>$ConcatenatedChallanNumber,
    					'PartyID'=>"KASPL",
    					'AccountID'=>$data["FarmerID"],
    					'ShipTo'=>'',
    					'CenterID'=>$data["CenterID"],
    					'WHID'=>'',
    					'BrokerID'=>'',
    					'GSTIN'=>$GSTIN,
    					'DeliveryType'=>1,
    					'ShippingID'=>NULL,
    					'SaleAmt'=>$OrdSaleAmt,
    					'DiscAmt'=>$OrdDiscAmt,
    					'sgstamt'=>$OrdCgstAmt,
    					'cgstamt'=>$OrdSgstAmt,
    					'igstamt'=>$OrdIgstAmt,
    					'OtherAmt'=>$OtherAmt,
    					'EffectOnOtherAmt'=>$OthEffectOn,
        				'RefNo'=>$data["Ref_no"],
        				'CashAmt'=>$data["CashAmt"],
        				'OnlineAmt'=>$data["OnlineAmt"],
        				'Effecton'=>$data["OnlineAmtLedger"],
    					'BillAmt'=>$OrdNetAmt,
    					'RndAmt'=>$BillAmt,
    					'ItCount'=>$ItemCount,
    					'UserID'=>$UserID,
					);
					
					if($this->db->insert(db_prefix() . 'K1salesmaster',$add_entry_sales)){
					    $this->KirtiOneOrderModel->increment_next_number('next_K1Tax_number_for_kirti');
					}
					//insert challan details
					$insert_challanDetails = array(
    					'PlantID'=>$selected_company,
    					'FY'=>$fy,
    					'ChallanID'=>$ConcatenatedChallanNumber,
    					'cnfid'=>'',
    					'Transdate'=>date('Y-m-d H:i:s'), 
    					'RouteID'=>0,
    					'VehicleID'=>'',
    					'DriverID'=>'',
    					'LoaderID'=>'',
    					'SalesmanID'=>'',
    					'ChallanWeight'=>0,
    					'ChallanAmt'=>$BillAmt,                    
    					'Gatepassuserid'=>'',
    					'OrderStatus'=>'F',
    					'UserID'=>$UserID                    
					);
					if($this->db->insert(db_prefix() . 'K1challanmaster',$insert_challanDetails)){
					    $this->KirtiOneOrderModel->increment_next_number('next_K1Challan_number_for_kirti'); 
					}
					
					//Add Customer ledger Entries 
					$ord = 1;
					$narration = "By SalesID ".$SalesId."/".$ConcatenatedChallanNumber;
					$insert_customer_ledger = array(
					'PlantID'=>$selected_company,
					'FY'=>$fy,
					'Transdate'=>date('Y-m-d H:i:s'),
					'VoucherID'=>$SalesId,       
					'Transdate2'=>date('Y-m-d H:i:s'),        
					'PartyID'=>"KASPL",
					'AccountID'=>$data["FarmerID"],
					'CounterAccount'=>"SALE",
					'CenterID'=>$data["CenterID"],                  
					'EntryFor'=>3,
					'TType'=>"D",
					'Amount'=>$BillAmt,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID                
					);
					$this->db->insert(db_prefix() . 'accountledger',$insert_customer_ledger);
					$ord ++ ;
					if($OtherAmt>0){
					    //Add Sale Ledger Entry
    					$sale_ledger_entry = array(
    					'PlantID'=>$selected_company,
    					'FY'=>$fy,
    					'Transdate'=>date('Y-m-d H:i:s'),
    					'VoucherID'=>$SalesId,  
    					'Transdate2'=>date('Y-m-d H:i:s'), 
    					'PartyID'=>"KASPL",
    					'AccountID'=>$OthEffectOn,
    					'CounterAccount'=>$data["FarmerID"],
    					'CenterID'=>$data["CenterID"],
    					'EntryFor'=>3,
    					'TType'=>"C",
    					'Amount'=>$OtherAmt,
    					'Narration'=>$narration,
    					'PassedFrom'=>"SALES",
    					'OrdinalNo'=>$ord,
    					'UserID'=>$UserID     
    					);
						$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
    					$ord ++ ;  
					}
					//Add Sale Ledger Entry
					$sale_ledger_entry = array(
					'PlantID'=>$selected_company,
					'FY'=>$fy,
					'Transdate'=>date('Y-m-d H:i:s'),
					'VoucherID'=>$SalesId,  
					'Transdate2'=>date('Y-m-d H:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>"SALE",
					'CounterAccount'=>$data["FarmerID"],
					'CenterID'=>$data["CenterID"],
					'EntryFor'=>3,
					'TType'=>"C",
					'Amount'=>$SaleLedgerAmount,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord ++ ;  
					
					if($OrdCgstAmt != 0 && $OrdSgstAmt != 0)
					{
						//CGST Tax Ledger Entry
						$Cgst_Ledger_entry = array(
						'PlantID'=>$selected_company,
						'FY'=>$fy,
						'Transdate'=>date('Y-m-d H:i:s'),
						'VoucherID'=>$SalesId,  
						'Transdate2'=>date('Y-m-d H:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"CGST",
						'CounterAccount'=>$data["FarmerID"],
						'CenterID'=>$data["CenterID"],
						'EntryFor'=>3,
						'TType'=>"C",
						'Amount'=>$OrdCgstAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						); 
						$this->db->insert(db_prefix() . 'accountledger',$Cgst_Ledger_entry);
						$ord ++ ;     
						
						//SGST Tax Ledger Entry
						$Sgst_Ledger_entry = array(
						'PlantID'=>$selected_company,
						'FY'=>$fy,
						'Transdate'=>date('Y-m-d H:i:s'),
						'VoucherID'=>$SalesId,  
						'Transdate2'=>date('Y-m-d H:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"SGST",
						'CounterAccount'=>$data["FarmerID"],
						'CenterID'=>$data["CenterID"],
						'EntryFor'=>3,
						'TType'=>"C",
						'Amount'=>$OrdSgstAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
						$this->db->insert(db_prefix() . 'accountledger',$Sgst_Ledger_entry);
						$ord ++ ;     
					}
					else if($OrdIgstAmt != 0)
					{
						//Igst Ledger Entry
						$Igst_Ledger_Entry = array(
						'PlantID'=>$selected_company,
						'FY'=>$fy,
						'Transdate'=>date('Y-m-d H:i:s'),
						'VoucherID'=>$SalesId,  
						'Transdate2'=>date('Y-m-d H:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"IGST",
						'CounterAccount'=>$data["FarmerID"],
						'CenterID'=>$data["CenterID"],
						'EntryFor'=>3,
						'TType'=>"C",
						'Amount'=>$OrdIgstAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						); 
						$this->db->insert(db_prefix() . 'accountledger',$Igst_Ledger_Entry);
						$ord ++ ;     
					}
					
					//Discount Ledger Entry
					if($OrdDiscAmt > 0)
					{                   
						$disc_ledger_entry = array(
						'PlantID'=>$selected_company,
						'FY'=>$fy,
						'Transdate'=>date('Y-m-d H:i:s'),
						'VoucherID'=>$SalesId,  
						'Transdate2'=>date('Y-m-d H:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"DISC",
						'CounterAccount'=>$data["FarmerID"],
						'CenterID'=>$data["CenterID"],
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$OrdDiscAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						); 
						$this->db->insert(db_prefix() . 'accountledger',$disc_ledger_entry);
						$ord ++ ; 
					}
					
					
					$roundledgerentry_debit = array(
					'PlantID'=>$selected_company,
					'FY'=>$fy,
					'Transdate'=>date('Y-m-d H:i:s'),
					'VoucherID'=>$SalesId,  
					'Transdate2'=>date('Y-m-d H:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>"ROUNDOFF",
					'CounterAccount'=>$data["FarmerID"],
					'CenterID'=>$data["CenterID"],
					'EntryFor'=>3,
					'TType'=>"D",
					'Amount'=>$OrdRoundOffAmt,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);  
					$this->db->insert(db_prefix() . 'accountledger',$roundledgerentry_debit);
					$ord ++ ; 
					
					
					if($OrderType == 1)
					{
						$nextReceiptnumber = get_option2('next_receipts_number_for_kirti',$fy);  
						$ordinalno = 1;
						/*if($data["OnlineAmt"] > 0){
							//Receipt Voucher credit Entry to party
							$receiptentry_credit_toParty = array(
							'PlantID'=>$selected_company,
							'FY'=>$fy,
							'Transdate'=>date('Y-m-d H:i:s'),
							'VoucherID'=>$nextReceiptnumber, 
							'Transdate2'=>date('Y-m-d H:i:s'),  
							'PartyID'=>"KASPL",
							'AccountID'=>$data["FarmerID"],
							'CounterAccount'=>$data["OnlineAmtLedger"],
							'CenterID'=>$data["CenterID"],
							'EntryFor'=>3,
							'TType'=>"C",
							'Amount'=>$data["OnlineAmt"],
							'Narration'=>$narration,
							'PassedFrom'=>"RECEIPTS",
							'OrdinalNo'=>$ordinalno,
							'UserID'=>$UserID     
							); 
							$this->db->insert(db_prefix() . 'accountledger',$receiptentry_credit_toParty);
							$ordinalno ++ ; 
							
							//Receipt Voucher Debit Entry to Company
							$receiptentry_debitto_company = array(
							'PlantID'=>$selected_company,
							'FY'=>$fy,
							'Transdate'=>date('Y-m-d H:i:s'),
							'VoucherID'=>$nextReceiptnumber, 
							'Transdate2'=>date('Y-m-d H:i:s'),  
							'PartyID'=>"KASPL",
							'AccountID'=>$data["OnlineAmtLedger"],
							'CounterAccount'=>$data["FarmerID"],
							'CenterID'=>$data["CenterID"],
							'EntryFor'=>3,
							'TType'=>"D",
							'Amount'=>$data["OnlineAmt"],
							'Narration'=>$narration,
							'PassedFrom'=>"RECEIPTS",
							'OrdinalNo'=>$ordinalno,
							'UserID'=>$UserID     
							);
							$this->db->insert(db_prefix() . 'accountledger',$receiptentry_debitto_company); 
							$ordinalno ++ ; 
						}*/
						
						if($data["CashAmt"] > 0){
							$receiptentry_credit_toParty = array(
							'PlantID'=>$selected_company,
							'FY'=>$fy,
							'Transdate'=>date('Y-m-d H:i:s'),
							'VoucherID'=>$nextReceiptnumber, 
							'Transdate2'=>date('Y-m-d H:i:s'),  
							'PartyID'=>"KASPL",
							'AccountID'=>$data["FarmerID"],
							'CounterAccount'=>'CASH',
							'CenterID'=>$data["CenterID"],
							'EntryFor'=>3,
							'TType'=>"C",
							'Amount'=>$data["CashAmt"],
							'Narration'=>$narration,
							'PassedFrom'=>"RECEIPTS",
							'OrdinalNo'=>$ordinalno,
							'UserID'=>$UserID     
							); 
							$this->db->insert(db_prefix() . 'accountledger',$receiptentry_credit_toParty);
							$ordinalno ++ ; 
							
							//Receipt Voucher Debit Entry to Company
							$receiptentry_debitto_company = array(
							'PlantID'=>$selected_company,
							'FY'=>$fy,
							'Transdate'=>date('Y-m-d H:i:s'),
							'VoucherID'=>$nextReceiptnumber, 
							'Transdate2'=>date('Y-m-d H:i:s'),  
							'PartyID'=>"KASPL",
							'AccountID'=>'CASH',
							'CounterAccount'=>$data["FarmerID"],
							'CenterID'=>$data["CenterID"],
							'EntryFor'=>3,
							'TType'=>"D",
							'Amount'=>$data["CashAmt"],
							'Narration'=>$narration,
							'PassedFrom'=>"RECEIPTS",
							'OrdinalNo'=>$ordinalno,
							'UserID'=>$UserID     
							);
							$this->db->insert(db_prefix() . 'accountledger',$receiptentry_debitto_company); 
						}
						if($ordinalno > 1){
							$this->increment_next_number('next_receipts_number_for_kirti');
							$this->db->where(db_prefix() . 'K1salesmaster.OrderID',$OrderID);
							$this->db->update(db_prefix() . 'K1salesmaster',["ReceiptVoucherID"=>$nextReceiptnumber]);
						}
						
					}
					$response = array("status"=>true,'data'=>$ItemListArray,"message"=>"Order Generated Successfully.");
				}
    		    
    		}
		    
        }
        end:
        return $response;
    }
    
    public function CheckItemFor($ProductID)
	{
		$this->db->select(db_prefix() . 'product.*');
		$this->db->where(db_prefix() . 'product.ProductID',$ProductID);
		return $this->db->get(db_prefix() . 'product')->row();
	}
//======================= Get All Kirti Staff List =============================
    public function GetAllStaffAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
    			$result = $this->GetAllStaff();
    			if($result){
    			    $response = array("status"=>true,"message"=>"Staff List","StaffList"=>$result);
    			}else{
    			    $response = array("status"=>false,"message"=>"Staff List not found");
    			}
            }
        }
        echo json_encode($response);   
    }
    public function GetAllStaff()
    {
        $this->db->select('AccountID,firstname,lastname');
        $StaffList = $this->db->get(db_prefix().'staff')->result_array();
        return $StaffList;
    }
//======================== Get Pincode Details =================================
    public function GetPincodeDetails()
    {
        $this->db->select('tblpin.*');
        $PinDetails = $this->db->get(db_prefix().'pin')->row();
        return $PinDetails;
    }
    public function GetChamberListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $WhID = $decode['WHID'];
        			$re = $this->GetChamberList($WhID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Chamber List" ,"ChamberList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Chamber List","ChamberList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function GetChamberList($WhID) 
    {
        
        $this->db->where('WHID', $WhID);
        $chamberList = $this->db->get(db_prefix().'WHSizeMaster')->result_array();
        return $chamberList;
    }
    // For trader App
    public function GetChamberListForTraderAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $WhID = $decode['WHID'];
        			$re = $this->GetChamberList($WhID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Chamber List" ,"ChamberList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Chamber List","ChamberList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    
    // Use this for Staff Application
    public function GetStackListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $ChamberID = $decode['ChamberID'];
        			$re = $this->GetStackList($ChamberID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Stack List" ,"StackList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Stack List","StackList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    // Use this for Customer Application
    public function GetStackListForTraderAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $ChamberID = $decode['ChamberID'];
        			$re = $this->GetStackList($ChamberID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Stack List" ,"StackList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Stack List","StackList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function GetStackList($ChamberID) 
    {
        
        $this->db->where('CHID', $ChamberID);
        $StackList = $this->db->get(db_prefix().'whstackmaster')->result_array();
        return $StackList;
    }
    
    // Use this for Staff application
    public function GetLotListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $StackID = $decode['StackID'];
        			$re = $this->GetLotList($StackID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Lot List" ,"LotList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Lot List","LotList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    // Use this for Customer application
    public function GetLotListForTraderAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $this->load->model('UserApp_Model');
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $StackID = $decode['StackID'];
        			$re = $this->GetLotList($StackID);
        			if($re){
        			    $response = array("status"=>true,"message"=>"Lot List" ,"LotList"=>$re);
        			}else{
        			    $response = array("status"=>false,"message"=>"Lot List","LotList"=>$re);
        			}
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);   
    }
    
    public function GetLotList($StackID) 
    {
        
        $this->db->where('StackID', $StackID);
        $LotList = $this->db->get(db_prefix().'lot_master')->result_array();
        return $LotList;
    }
//============== Kirti API Code End ============================================
 
    
    
    public function Asigned_company($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id']
                                      );
                            $response=$this->Get_assigned_company($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    
    
    public function get_targetAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                      );
                            $response=$this->Get_target($data);
                }
            }
        
        echo json_encode($response);    
    }
    
    	public function get_achievementAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                               "staff_id"=>$decode['staff_id'],
                               "PlantID"=>$decode['PlantID'],
                               
                            );
                            $response=$this->Get_achievement($data);
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_CityAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "state_id"=>$decode['state_id']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_Citylist($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_order_list_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "dist_id"=>$decode['dist_id'],
                                        "PlantID"=>$decode['PlantID'],
                                        "start_date"=>$decode['start_date'],
                                        "end_date"=>$decode['end_date'],
                                        "order_status"=>$decode['order_status']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_order_list($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    
    
    public function Get_pending_order_list_API_new($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "dist_id"=>$decode['dist_id'],
                                        "PlantID"=>$decode['PlantID'],
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_pending_order_list_new($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_pending_order_list_API_new2($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                            $data=array(
                                "dist_id"=>$decode['dist_ids'],
                                "PlantID"=>$decode['PlantID'],
                                "staff_id"=>$decode['staff_id'],
                            );
                        $response=$this->Get_pending_order_list_new2($data);
                }
            }
        
        echo json_encode($response);    
    }
    
    public function GetPendingOrderAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                            $data=array(
                                "PlantID"=>$decode['PlantID'],
                                "staff_id"=>$decode['staff_id'],
                            );
                        $response=$this->GetPendingOrder($data);
                }
            }
        
        echo json_encode($response);    
    }
    
    
    public function Get_my_team_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "PlantID"=>$decode['PlantID']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_my_team_list($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_staff_detail_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "PlantID"=>$decode['PlantID']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_staff_detail($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_sale_reports_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "UserID"=>$decode['UserID'],
                                        "PlantID"=>$decode['PlantID'],
                                        "AccountID"=>$decode['AccountID'],
                                        "from_date"=>$decode['from_date'],
                                        "to_date"=>$decode['to_date']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_sale_reports($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function parties_not_billedAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "UserID"=>$decode['UserID'],
                                        "PlantID"=>$decode['PlantID'],
                                        "from_date"=>$decode['from_date'],
                                        "to_date"=>$decode['to_date']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_parties_not_billed($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function item_not_billedAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "AccountID"=>$decode['AccountID'],
                                        "PlantID"=>$decode['PlantID'],
                                        "from_date"=>$decode['from_date'],
                                        "to_date"=>$decode['to_date']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_item_not_billed($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_account_ledger_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "UserID"=>$decode['UserID'],
                                        "PlantID"=>$decode['PlantID'],
                                        "AccountID"=>$decode['AccountID'],
                                        "from_date"=>$decode['from_date'],
                                        "to_date"=>$decode['to_date']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_account_ledger($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function update_tour_plan_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "PlantID"=>$decode['PlantID'],
                                        "id"=>$decode['id'],
                                        "status"=>$decode['status'],
                                        "reason"=>$decode['reason']
                                      );
                            $response=$this->update_tour_plan($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function SubmitTPlanAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "staff_id"=>$decode['staff_id'],
                    "PlantID"=>$decode['PlantID'],
                    "id"=>$decode['id'],
                    "DistAvl"=>$decode['DistAvl'],
                    "Retailing"=>$decode['Retailing'],
                    "TotalCounterCall"=>$decode['TotalCounterCall'],
                    "TotalProductiveCall"=>$decode['TotalProductiveCall'],
                    "TotalValue"=>$decode['TotalValue'],
                    "PrimaryValue"=>$decode['PrimaryValue'],
                    "ClosingRemark"=>$decode['ClosingRemark']
                );
                $response=$this->SubmitTPlan($data);
                }
            }
        echo json_encode($response);    
    }
    
    public function detail_tour_planAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "id"=>$decode['id']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->detail_tour_plan($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_order_detail_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "order_id"=>$decode['order_id']
                                      );
                        //$state_id = $decode['state_id'];
                            $response=$this->Get_order_details($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    
    
    
    public function In_OutAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            /*$data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "type_check"=>$decode['type_check'],
                                        "location_user"=>$decode['location_user'],
                                        "location_name"=>$decode['location_name']
                                      );*/
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "type_check"=>$decode['type_check'],
                                        "location_user"=>$decode['location_user']
                                      );
                            $response=$this->In_Out($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    
    
    
    public function In_Out_statusAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id']
                                      );
                           
                            $response=$this->In_Out_status($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function VisLocationsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                           /* $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "location_list"=>$decode['location_list'],
                                        "total_distance"=>$decode['total_distance'],
                                        "location_name_list"=>$decode['location_name_list']
                                      );*/
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "location_list"=>$decode['location_list'],
                                        "total_distance"=>$decode['total_distance']
                                      );
                            $response=$this->VisLocations($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function VisLocationsAPI_new($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "location_list"=>$decode['location_list'],
                                        "total_distance"=>$decode['total_distance'],
                                        "location_name_list"=>$decode['location_name_list'],
                                        "battery_level"=>$decode['battery_level'],
                                        "device_information"=>$decode['device_information'],
                                        "GPS_Status"=>$decode['GPS_Status']
                                      );
                           
                            $response=$this->VisLocations_new($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_CustomerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "plant_id"=>$decode['plant_id'],
                                        "staff_id"=>$decode['staff_id']
                                      );
                            $response=$this->Get_Customer($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_CustomerAPI_new($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "plant_id"=>$decode['plant_id'],
                                        "staff_id"=>$decode['staff_id']
                                      );
                            $response=$this->Get_Customer_new($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_EnquiryAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id']
                                      );
                            $response=$this->Get_enquiry($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_EnqDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                    $data=array(
                        "enqID"=>$decode['enqID']
                    );
                $response=$this->Get_enquiryDetails($data);
                }
            }
        echo json_encode($response);    
    }
    
    public function Update_EnqAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                    $data=array(
                        "enqID"=>$decode['enqID'],
                        "GSTIN"=>$decode['GSTIN'],
                        "PAN"=>$decode['PAN'],
                        "AdharNo"=>$decode['AdharNo'],
                        "FLIC"=>$decode['FLIC'],
                        "PIN"=>$decode['PIN'],
                        "GROUPTYPE"=>$decode['GROUPTYPE'],
                        "DISTTYPE"=>$decode['DISTTYPE'],
                        "EmailID"=>$decode['EmailID']
                    );
                $response=$this->Update_enquiryDetails($data);
                }
            }
        echo json_encode($response);    
    }
    
    public function Get_TourAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "staff_id"=>$decode['staff_id'],
                    "from_date"=>$decode['from_date'],
                    "to_date"=>$decode['to_date'],
                    "PlantID"=>$decode['PlantID']
                );
                $response=$this->Get_tour($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetTeamTourAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "staff_id"=>$decode['staff_id'],
                    "from_date"=>$decode['from_date'],
                    "to_date"=>$decode['to_date'],
                    "PlantID"=>$decode['PlantID']
                );
                $response=$this->GetTeamTour($data);
            }
        }
        echo json_encode($response);    
    }
    
    
    public function Get_ItemDevisionAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                    $response=$this->Get_ItemDivision();
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_CustDivAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "dist_id"=>$decode['dist_id'],
                                        "plant_id"=>$decode['plant_id']
                                      );
                            $response=$this->Get_ItemDivision_by_dist($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_ItemDevwise_listAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "group_id"=>$decode['division_id']
                                      );
                            $response=$this->Get_ItemDivwise_list($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_itemlistAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                $data=array(
                    "plant_id"=>$decode['plant_id'],
                    "dist_type"=>$decode['dist_type'],
                    "dist_state_id"=>$decode['dist_state_id'],
                    "item_division"=>$decode['item_division']
                );
                $response=$this->Get_itemlist($data);
            }
        }
        
        echo json_encode($response);    
    }
    
    public function All_dist_type($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "plant_id"=>$decode['plant_id']
                                      );
                            $response=$this->Get_all_dist_type($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function All_Item_price_List($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "dist_type"=>$decode['dist_type'],
                                        "dist_state_id"=>$decode['dist_state_id'],
                                        "plant_id"=>$decode['plant_id']
                                      );
                            $response=$this->Get_allitemlist($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_order_numberAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            
                            $response=$this->Get_next_order_number();
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    
    public function Ganerate_hashAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $hash = app_generate_hash();
                            
                            //$response=$this->Get_next_order_number();
                $response=array("status"=>true,"message"=>"Hash Code","hash"=>$hash);        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function oder_placeAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "number"=>$decode['number'],
                                        "date"=>$decode['date'],
                                        "clientid"=>$decode['clientid'],
                                        "dist_comp"=>$decode['dist_comp'],
                                        "dist_sale_agent"=>$decode['dist_sale_agent'],
                                        "dist_route"=>$decode['dist_route'],
                                        "dist_tcs"=>$decode['dist_tcs'],
                                        "order_type"=>$decode['order_type'],
                                        "taxes"=>$decode['taxes'],
                                        "project_id"=>$decode['project_id'],
                                        "billing_street"=>$decode['billing_street'],
                                        "billing_city"=>$decode['billing_city'],
                                        "billing_state"=>$decode['billing_state'],
                                        "billing_zip"=>$decode['billing_zip'],
                                        "billing_country"=>$decode['billing_country'],
                                        "include_shipping"=>$decode['include_shipping'],
                                        "show_shipping_on_invoice"=>$decode['show_shipping_on_invoice'],
                                        "shipping_street"=>$decode['shipping_street'],
                                        "shipping_city"=>$decode['shipping_city'],
                                        "shipping_state"=>$decode['shipping_state'],
                                        "shipping_zip"=>$decode['shipping_zip'],
                                        "shipping_country"=>$decode['shipping_country'],
                                        "currency"=>$decode['currency'],
                                        "sale_agent"=>$decode['sale_agent'],
                                        "total_cases"=>$decode['total_cases'],
                                        "total_crates"=>$decode['total_crates'],
                                        "total_tax"=>$decode['total_tax'],
                                        "subtotal"=>$decode['subtotal'],
                                        "total"=>$decode['total'],
                                        "prefix"=>$decode['prefix'],
                                        "number_format"=>$decode['number_format'],
                                        "datecreated"=>$decode['datecreated'],
                                        "addedfrom"=>$decode['addedfrom'],
                                        "cancel_overdue_reminders"=>$decode['cancel_overdue_reminders'],
                                        "allowed_payment_modes"=>$decode['allowed_payment_modes'],
                                        "custom_recurring"=>$decode['custom_recurring'],
                                        "recurring"=>$decode['recurring'],
                                        "hash"=>$decode['hash'],
                                        "adjustment"=>$decode['adjustment'],
                                        "company_id"=>$decode['company_id'],
                                        "financial_year"=>$decode['financial_year'],
                                        "Item"=>$decode['Item']
                                      );
                            $response=$this->order_place($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function oder_placeAPI2($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                /*if($decode['PlantID'] == "3"){
                    $response=array("status"=>true,"message"=>"Unable to place order because application is under the maintance...");
                }else{*/
                    $data=array(
                        "PlantID"=>$decode['PlantID'],
                        "FY"=>$decode['FY'],
                        "OrderID"=>$decode['OrderID'],
                        "AccountID"=>$decode['AccountID'],
                        "subtotal"=>$decode['subtotal'],
                        "total_tax"=>$decode['total_tax'],
                        "OrderAmt"=>$decode['OrderAmt'],
                        "Crates"=>$decode['Crates'],
                        "Cases"=>$decode['Cases'],
                        "OrderStatus"=>$decode['OrderStatus'],
                        "OrderType"=>$decode['OrderType'],
                        "order_type"=>$decode['order_type'],
                        "UserID"=>$decode['UserID'],
                        "hash"=>$decode['hash'],
                        "Item"=>$decode['Item']
                    );
                    $response=$this->order_place($data);
                //}   
                }
            }
        //$response=array("status"=>true,"message"=>"Unable to place order, please try again later...");
                                //return $response;
        echo json_encode($response);    
    }
    public function searchCustomerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "customer_name"=>$decode['customer_name'],
                                        "plant_id"=>$decode['plant_id']
                                      );
                            $response=$this->search_Customer($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function singleCustomerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "customer_id"=>$decode['customer_id'],
                                        "plant_id"=>$decode['plant_id']
                                      );
                                      //$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$decode['plant_id']);
                            $response = $this->single_Customer_detail($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_CustomerGroupAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            /*$data=array(
                                        "customer_type"=>$decode['customer_type']
                                      );*/
                            $response=$this->Get_CustomerGroup();
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function addCustomerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "company"=>$decode['company'],
                                        "vat"=>$decode['gst'],
                                        "phonenumber"=>$decode['companyphone'],
                                        "country"=>$decode['country'],
                                        "city"=>$decode['city'],
                                        "zip"=>$decode['zipcode'],
                                        "state"=>$decode['state'],
                                        "address"=>$decode['address'],
                                        "groups_in"=>$decode['groups_in'],
                                        "addedfrom"=>$decode['addedfrom'],
                                        "is_primary"=>$decode['is_primary'],
                                        "firstname"=>$decode['firstName'],
                                        "lastname"=>$decode['lastname'],
                                        "title"=>$decode['title'],
                                        "email"=>$decode['email'],
                                        "password"=>$decode['password']
                                      );
                            
                            $response=$this->addCustomer($data);
                        
                }
            }
            
          
        
        echo json_encode($response);    
    }
    
    public function AddEnquiryAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "farm_name"=>$decode['farm_name'],
                                        "contact_person"=>$decode['contact_person'],
                                        "cp_mobile_no"=>$decode['cp_mobile_no'],
                                        "address"=>$decode['address'],
                                        "remark"=>$decode['remark'],
                                        "state"=>$decode['state'],
                                        "district"=>$decode['district'],
                                        "area"=>$decode['area'],
                                        "revisit"=>$decode['revisit'],
                                        "status"=>$decode['status']
                                      );
                            
                            $response=$this->AddEnquiry($data);
                        
                }
            }
            
          
        
        echo json_encode($response);    
    }
    
    public function AddTourAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "staff_id"=>$decode['staff_id'],
                                        "PlantID"=>$decode['PlantID'],
                                        "cust_ID"=>$decode['cust_ID'],
                                        "purpose"=>$decode['purpose'],
                                        "start_date"=>$decode['start_date'],
                                        "end_date"=>$decode['end_date'],
                                        "state"=>$decode['state'],
                                        "city"=>$decode['city'],
                                        "area"=>$decode['area'],
                                        "remark"=>$decode['remark'],
                                        "status"=>$decode['status'],
                                        "reason"=>$decode['reason']
                                      );
                            
                            $response=$this->addtour($data);
                        
                }
            }
            
          
        
        echo json_encode($response);    
    }
    
    public function Add_versonAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                                        "verson"=>$decode['verson'],
                                        "app_url"=>$decode['app_url']
                                      );
                            $response=$this->Add_verson($data);
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function Get_versonAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            /*$data=array(
                                        "customer_type"=>$decode['customer_type']
                                      );*/
                            $response=$this->Get_App_Version();
                        
                }
            }
        
        echo json_encode($response);    
    }
    
    public function getAPI_old($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'PUT')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                switch ($decode['api_name']) {
                    case "get_subCategory":
                            $response=$this->getSubCategory($decode['parent_category_id']);
                         break;
                    case "signup":
                           $data=array(
                                        "mobile_number"=>$decode['mobile_number'],
                                        "fname"=>$decode['fname'],
                                        "lname"=>$decode['lname'],
                                        "email"=>$decode['email'],
                                        "password"=>$decode['password'],
                                        "reference_code"=>$decode['reference_code']
                                      );
                            $response=$this->signup($data);
                        break;
                    case "firebase_token":
                           $data=array(
                                        "android_id"=>$decode['android_id'],
                                        "user_id"=>$decode['user_id'],
                                        "firebase_token"=>$decode['firebase_token']
                                      );
                            $response=$this->firebase_token($data);
                        break;
                    case "get_childCategory":
                            $response=$this->getChildCategory($decode['sub_category_id']);
                        break;
                        case "login":
                            $data=array(
                                        "email"=>$decode['email'],
                                        "password"=>$decode['password'],
                                        "staff"=>$decode['staff']
                                      );
                            $response=$this->login($data);
                        break;
                    case "all_subCategory":
                            $response=$this->getAllSubCategory();
                        break;
                    case "update_profile":
                            $data=array( 
                                        "user_id"=>$decode['user_id'],                                       
                                        "fname"=>$decode['fname'],
                                        "lname"=>$decode['lname'],
                                        "email"=>$decode['email'],
                                        "dob"=>$decode['dob'],
                                        "gender"=>$decode['gender'],
                                        "address"=>$decode['address'],
                                        "aboutme"=>$decode['aboutme']
                                      );
                            $response=$this->updateUserProfile($data);
                        break;
                    case "vendor_registration":
                            $data=array( 
                                        "user_id"=>$decode['user_id'],
                                        "exp1"=>$decode['exp1'],                                       
                                        "exp2"=>$decode['exp2'],
                                        "exp3"=>$decode['exp3'],
                                        "professional_type"=>$decode['professional_type'],
                                        "qualification"=>$decode['qualification'],
                                        "month"=>$decode['month'],
                                        "year"=>$decode['year']
                                      );
                            $response=$this->vendorRegistration($data);
                        break;
                    case "contact_us":
                            $data=array( 
                                      "message"=>$decode['message'],                                       
                                      "contact_number"=>$decode['contact_number']
                                      );
                            $response=$this->contactUs($data);
                        break;
                        
                        default:
                        $response = array("error" => true,"message" => "Invalid API");  
                }
            }
        }
        else
        {
           $response = array("error" => true,"message" => "Invalid madhav request");  
        }
        echo json_encode($response);    
    }
    
    
    
    
    public function getSubCategory($parent_id) {
        
        $sql="SELECT * FROM `tbl_sub_category` WHERE `parent_category_id`=".$parent_id;
        $result=$this->BuisnessModel->getQuery($sql);
        $response=array("error"=>false,"message"=>"Sub-category list.","sub_categories"=>$result);            
        return $response;
    }

    public function signup($params=FALSE) {
        
        
        $sql="SELECT * FROM `tbl_user` WHERE `user_mobile_number`='".$params['mobile_number']."'";
        $num=$this->BuisnessModel->numRowsQuery($sql);
        if($num>=1)
        {
            $mobile = $params['mobile_number'];
           $response=array("error"=>true,"message"=>"$mobile has already registered.");
           return $response;   
        }
        else
        {
           $curdate=$this->curDate();
           $sql_insert="INSERT INTO `tbl_user`(`user_fname`, `user_lname`,`user_email`, `user_mobile_number`, `user_password`, `user_account_status`, `user_insertedDate`) VALUES ('".$params['fname']."','".$params['lname']."','".$params['email']."','".$params['mobile_number']."','".md5($params['password'])."','activate','".$curdate."')"; 
           $result=$this->BuisnessModel->insertQueryGetLastId($sql_insert);

           if($result['affected_rows']>=1)
           {
              
           	    if(trim($params['reference_code'])!="")
           	    {

           	    	$sql_ven_num="SELECT * FROM `tbl_user`,vendor_info WHERE tbl_user.user_id=vendor_info.user_id and tbl_user.reference_code='".trim($params['reference_code'])."'";
           	    	$ven_num=$this->BuisnessModel->numRowsQuery($sql_ven_num);
           	    	$ven_data=$this->BuisnessModel->getQuery($sql_ven_num);

           	    	if($ven_num==1)
           	    	{
           	    		$bonus_remark="You have got 200 bonus credits for giving reference to other user";
           	    		$sql_bonus="INSERT INTO `tbl_user_credit_details`(`user_id`, `get_credits`, `credits_type`, `credits_remark`, `payment_status`, `credit_insertedDate`) VALUES (".$ven_data[0]['user_id'].",200,'Bonus','".$bonus_remark."','Free','".$this->curDate()."')";
           	    		$result_bonus=$this->BuisnessModel->insertQuery($sql_bonus);

           	    		$sql_update_credit="UPDATE `vendor_info` SET  user_credits=user_credits+200 WHERE `user_id`=".$ven_data[0]['user_id']; 
                        $affected_row_credit=$this->BuisnessModel->updateQuery($sql_update_credit);


                        $notification_remark="You have got 200 bonus credits for giving reference to other user";
           	    		$sql_notification="INSERT INTO `tbl_notification`(`user_id`, `notification_title`, `notification_text`, `notification_status`, `notification_inserted_date`) VALUES (".$ven_data[0]['user_id'].",'Bonus Credit','".$notification_remark."','unssen','".$this->curDate()."')";
           	    		$result_notification=$this->BuisnessModel->insertQuery($sql_notification);

           	    	}
           	    }

           	   /*  generate reference code*/
           	    $reference_code="F2F".$result['last_id'].substr($params['fname'],0,1).substr($params['lname'],strlen($params['lname'])-1,1).substr($params['lname'],0,1).substr($params['fname'],strlen($params['fname'])-1,1).$this->generateRandomNumber(2);

           	    $sql_update="UPDATE `tbl_user` SET  reference_code='".strtoupper($reference_code)."' WHERE `user_id`=".$result['last_id']; 
                $affected_row=$this->BuisnessModel->updateQuery($sql_update);

                /*  generate reference code end */ 

               $sql_get="SELECT * FROM `tbl_user` WHERE `user_mobile_number`='".$params['mobile_number']."' and `user_password`='".md5($params['password'])."'";
               $result=$this->BuisnessModel->getQuery($sql_get);
               if(empty($result[0]['user_photo']))
               {
                 $file_path="https://".$_SERVER['SERVER_NAME']."/foot2feet-live/admin_assets/uploads/user/profile/default.jpg";
               }
               else{
                 $file_path="https://".$_SERVER['SERVER_NAME']."/foot2feet-live/admin_assets/uploads/user/profile/".$result[0]['user_photo'];
               }
               $data=array("user_id"=>$result[0]['user_id']);
               $vendor_info=$this->vendor_info($data);
               $user_data=array(
                        "user_id"=> $result[0]['user_id'],
                        "user_fname"=> $result[0]['user_fname'],
                        "user_lname"=> $result[0]['user_lname'],
                        "user_email"=> $result[0]['user_email'],
                        "user_mobile_number"=>$result[0]['user_mobile_number'],
                        "user_dob"=> $result[0]['user_dob'],
                        "user_gender"=> $result[0]['user_gender'],
                        "user_address"=> $result[0]['user_address'],
                        "user_about_me"=> $result[0]['user_about_me'],
                        "reference_code"=> $result[0]['reference_code'],
                        "user_photo"=> $file_path,
                        "user_insertedDate"=> $result[0]['user_insertedDate'],
                        "vendor_id"=> $vendor_info['vendor_id'],
                        "vendor_flag"=> $vendor_info['vendor_flag']
               );

               $response=array("error"=>false,"message"=>"You have registered successfully.","user_data"=>$user_data);
               return $response;  
           }
           else{

                $response=array("error"=>true,"message"=>"During signup some error occure.");
                return $response;
           }

        }
    }

    public function firebase_token($params=FALSE) {
        
        
        $sql="SELECT * FROM `tbl_firebase_notification` WHERE `android_id`='".$params['android_id']."' ";
        $num=$this->BuisnessModel->numRowsQuery($sql);
        if($num>=1)
        {
            $sql_update="UPDATE `tbl_firebase_notification` SET 
            user_id = '".$params['user_id']."',`firebase_token`= 
            '".$params['firebase_token']."' WHERE `android_id`='".$params['android_id']."' "; 
           
           $affected_row=$this->BuisnessModel->updateQuery($sql_update);
           if($affected_row>=1){
                $response=array("error"=>false,"message"=>"firebase token updated.");
                return $response;   
           }else {
            $response=array("error"=>true,"message"=>"Firebase token is upto date.");
                return $response;   
           }
        }
        else
        {
           $curdate=$this->curDate();
           $sql_insert="INSERT INTO `tbl_firebase_notification`(`android_id`, `user_id`,`firebase_token`, `created`) VALUES ('".$params['android_id']."','".$params['user_id']."','".$params['firebase_token']."', '".$curdate."')"; 
           $result=$this->BuisnessModel->insertQueryGetLastId($sql_insert);

           if($result['affected_rows']>=1)
           {
               $response=array("error"=>false,"message"=>"You have registered firebase token successfully.");
               return $response;  
           }
           else{
                $response=array("error"=>true,"message"=>"During insert firebase token some error occured.");
                return $response;
           }

        }
    }

    public function getChildCategory($subcategory_id) {
        
        $sql="SELECT * FROM `tbl_child_category` WHERE `sub_category_id`=".$subcategory_id;
        $result=$this->BuisnessModel->getQuery($sql);
        $response=array("error"=>false,"message"=>"Child-category list.","child_category"=>$result);            
        return $response;
    }
    
    
    public function In_Out($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        /*$data=array(
                    "staff_id"=>$params['staff_id'],
                    "type_check"=>$params['type_check'],
                    "edit_date"=>'',
                    "point_id"=>'',
                    "location_user"=>$params['location_user'],
                    "location_name"=>$params['location_name']
                                      );*/
        $data=array(
                    "staff_id"=>$params['staff_id'],
                    "type_check"=>$params['type_check'],
                    "edit_date"=>'',
                    "point_id"=>'',
                    "location_user"=>$params['location_user']
                                      );
            $type = $data['type_check'];
        $re = $this->UserApp_Model->check_in($data);
      
       if(is_numeric($re)){
				if($re == 2){
				    $response=array("status"=>false,"message"=>"Your Current Location is not allowed to take attendance");
					//set_alert('warning',_l('your_current_location_is_not_allowed_to_take_attendance'));            
				}
				if($re == 3){
				    $response=array("status"=>false,"message"=>"Your location information is unknown");
					//set_alert('warning',_l('location_information_is_unknown'));            
				}
				if($re == 4){
				    $response=array("status"=>false,"message"=>"Your Route point is unknown");
					//set_alert('warning',_l('route_point_is_unknown'));            
				}
			}
			else{
				if($re == true){
					if($type == 1){
					    $response=array("status"=>true,"message"=>"Start Day Successfully");
						//set_alert('success',_l('check_in_successfull'));            
					}
					else{
					    $response=array("status"=>true,"message"=>"End Day Successfully");
						//set_alert('success',_l('check_out_successfull'));            
					}
				}
				else{
					if($type == 1){
					    $response=array("status"=>false,"message"=>"Day Not Started Successfully");
						//set_alert('warning',_l('check_in_not_successfull'));            
					}
					else{
					    $response=array("status"=>false,"message"=>"Day out not Successfully");
						//set_alert('warning',_l('check_out_not_successfull'));            
					}
				}                
			}
               return $response; 
    } 
    
    public function In_Out_status($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        
        $staff_id= $params['staff_id'];
        $cur_date = date('Y-m-d');
        $get_data = $this->UserApp_Model->get_in_out_data($staff_id,$cur_date);
        
        
       
        if($get_data){
            
                        $response=array("status"=>true,"message"=>"Below detail for login user", "data"=>$get_data);
            
                    } else {
                        
                            $response=array("status"=>false,"message"=>"no data for today", "data"=>null);
                    }
        
        
               return $response; 
    }
    
    public function VisLocations($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        /*$data=array(
                    "staff_id"=>$params['staff_id'],
                    "location_list"=>$params['location_list'],
                    "location_trav"=>$params['total_distance'],
                    "location_name_list"=>$params['location_name_list']
                                      );*/
        $data=array(
                    "staff_id"=>$params['staff_id'],
                    "location_list"=>$params['location_list'],
                    "location_trav"=>$params['total_distance']
                                      );
                $data['travDate'] = date('Y-m-d');
            $this->db->insert(db_prefix() . 'staffVisitLoc', $data);
            $insert_id = $this->db->insert_id();
        
        if (isset($insert_id)) {
                $response=array("status"=>true,"message"=>"You have Added Visiting list successfully");
            } else {
                $response=array("status"=>false,"message"=>"Something Went Wrong...");
            }
        
       
                
            return $response; 
    } 
    
    public function VisLocations_new($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        $staff_id= $params['staff_id'];
        $cur_date = date('Y-m-d');
        
        
                                      
        $get_data = $this->UserApp_Model->get_in_out_data($staff_id,$cur_date);
        
        if($get_data && !empty($get_data["location_list"])){
            
            $location_list = unserialize($get_data["location_list"]);
            $location_list = $location_list."|".$params['location_list'];
            $location_list = serialize($location_list);
        
            $total_distance = unserialize($get_data["location_trav"]);
            $total_distance = $total_distance."|".$params['total_distance'];
            $total_distance = serialize($total_distance);
            
            $location_name_list = unserialize($get_data["location_name_list"]);
            $location_name_list = $location_name_list."|".$params['location_name_list'];
            $location_name_list = serialize($location_name_list);
            
            
        }else{
            
            $location_list= serialize($params['location_list']);
            $total_distance= serialize($params['total_distance']);
            $location_name_list= serialize($params['location_name_list']);
            
        }
        
        
            if($get_data['type_check'] == "1"){    
                $data=array(
                    "staff_id"=>$params['staff_id'],
                    "location_list"=>$location_list,
                    "location_trav"=>$total_distance,
                    "location_name_list"=>$location_name_list
                                      );
                $result = $this->UserApp_Model->location_update($data);
            }    
        
            if($get_data['type_check'] == "1"){
                $single_record = array(
                        "staff_id"=>$params['staff_id'],
                        "location_list"=>$params['location_list'],
                        "location_trav"=>$params['total_distance'],
                        "location_name_list"=>$params['location_name_list'],
                        "battery_level"=>$params['battery_level'],
                        "device_information"=>$params['device_information'],
                        "GPS_Status"=>$params['GPS_Status'],
                        "date"=>$cur_date
                    );
                $this->db->insert(db_prefix().'travel_report', $single_record);
            }
            
        
                
        if ($result==true) {
                $response=array("status"=>true,"message"=>"You have Added Visiting list successfully");
            } else {
                $response=array("status"=>false,"message"=>"Something Went Wrong...");
            }
        
       
                
            return $response; 
    }
    
    public function Get_dashboard_status($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_dashboard_status($staff_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$staff_id);
                                return $response;*/
               return $success; 
    }
    
    public function Get_assigned_company($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_assigned_company($staff_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$staff_id);
                                return $response;*/
               return $success; 
    }
    
     public function Get_target($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_target($staff_id);
        return $success; 
    }
    
    public function Get_achievement($params=FALSE){
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_achievement($staff_id,$PlantID);
        return $success; 
    }
     
    public function Get_Citylist($params=FALSE){
        
        $state_id = $params['state_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_Citylist($state_id);
       return $success; 
    }
    
    public function Get_order_list($params=FALSE){
        
        $dist_id = $params['dist_id'];
        $PlantID = $params['PlantID'];
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $order_status = $params['order_status'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_order_list_detail($dist_id,$PlantID,$start_date,$end_date,$order_status);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$staff_id);
                                return $response;*/
               return $success; 
    }
    
    
    
    public function Get_pending_order_list_new($params=FALSE){
        
        $dist_id = $params['dist_id'];
        $PlantID = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_pending_order_list_detail_new($dist_id,$PlantID);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$staff_id);
                                return $response;*/
               return $success; 
    }
    
    public function Get_pending_order_list_new2($params=FALSE)
    {
        $dist_id = $params['dist_id'];
        $PlantID = $params['PlantID'];
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_pending_order_list_detail_new2($dist_id,$PlantID,$staff_id);
        return $success; 
    }
    
    public function GetPendingOrder($params=FALSE){
        
        $PlantID = $params['PlantID'];
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->GetPendingOrder($PlantID,$staff_id);
        return $success; 
    }
    
    public function Get_my_team_list($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_my_team_list_detail($staff_id,$PlantID);
        return $success; 
    }
    
    public function Get_staff_detail($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_staff_detail($staff_id,$PlantID);
        return $success; 
    }
    
    public function Get_sale_reports($params=FALSE){
        
        $UserID = $params['UserID'];
        $PlantID = $params['PlantID'];
        $AccountID = $params['AccountID'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_sale_reports($UserID,$PlantID,$AccountID,$from_date,$to_date);
        return $success; 
    }
    
    public function Get_parties_not_billed($params=FALSE){
        
        $UserID = $params['UserID'];
        $PlantID = $params['PlantID'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_parties_not_billed($UserID,$PlantID,$from_date,$to_date);
       return $success; 
    }
    
    public function Get_item_not_billed($params=FALSE){
        
        $AccountID = $params['AccountID'];
        $PlantID = $params['PlantID'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_item_not_billed($AccountID,$PlantID,$from_date,$to_date);
       
                
               return $success; 
    }
    
    public function Get_account_ledger($params=FALSE){
        
        $UserID = $params['UserID'];
        $PlantID = $params['PlantID'];
        $AccountID = $params['AccountID'];
        $from_date = $params['from_date'];
        $to_date = $params['to_date'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_account_ledger($UserID,$PlantID,$AccountID,$from_date,$to_date);
       
               return $success; 
    }
    
    public function update_tour_plan($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $id = $params['id'];
        $status = $params['status'];
        $reason = $params['reason'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->update_tour_plan($staff_id,$PlantID,$id,$status,$reason);
               return $success; 
    }
    
    public function SubmitTPlan($params=FALSE){
        
        
        $data = array(
            "staff_id" => $params['staff_id'],
            "PlantID" => $params['PlantID'],
            "id" => $params['id'],
            "DistAvl" => $params['DistAvl'],
            "Retailing" => $params['Retailing'],
            "TotalCounterCall" => $params['TotalCounterCall'],
            "TotalProductiveCall" => $params['TotalProductiveCall'],
            "TotalValue" => $params['TotalValue'],
            "PrimaryValue" => $params['PrimaryValue'],
        );
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Submit_TPlan($data);
        return $success; 
    }
    
    public function detail_tour_plan($params=FALSE){
        
        
        $id = $params['id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->detail_tour_plan($id);
       
               
               return $success; 
    }
    
    public function Get_order_details($params=FALSE){
        
        $order_id = $params['order_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_order_details($order_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function Get_Customer($params=FALSE){
        
        $plant_id = $params['plant_id'];
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_Customer($plant_id,$staff_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function Get_Customer_new($params=FALSE){
        
        $plant_id = $params['plant_id'];
        $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_Customer_new($plant_id,$staff_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function Get_enquiry($params=FALSE){
        
        
         $staff_id = $params['staff_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_enquiry($staff_id);
       
               return $success; 
    }
    
    public function Get_enquiryDetails($params=FALSE){
        $enqID = $params['enqID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_enquiryDetails($enqID);
        return $success; 
    }
    public function Update_enquiryDetails($params=FALSE){
        $enqID = $params['enqID'];
        $data =array(
            "enqID"=>$params['enqID'],
            "GSTIN"=>$params['GSTIN'],
            "PAN"=>$params['PAN'],
            "AdharNo"=>$params['AdharNo'],
            "FLIC"=>$params['FLIC'],
            "PIN"=>$params['PIN'],
            "GROUPTYPE"=>$params['GROUPTYPE'],
            "DISTTYPE"=>$params['DISTTYPE'],
            "EmailID"=>$params['EmailID']
        );
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Update_enquiryDetails($data);
        return $success; 
    }
    
    public function Get_tour($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $from_date = $params['from_date'];
        $to_date  = $params['to_date'];
        $PlantID  = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_tour($staff_id,$from_date,$to_date,$PlantID);
        return $success; 
    }
    public function GetTeamTour($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $from_date = $params['from_date'];
        $to_date  = $params['to_date'];
        $PlantID  = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->GetTeamTour($staff_id,$from_date,$to_date,$PlantID);
        return $success; 
    }
    
    public function Get_ItemDivision($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_ItemDivision();
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function Get_ItemDivision_by_dist($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        $dist_id = $params['dist_id'];
        $plant_id = $params['plant_id'];
        $success = $this->UserApp_Model->Get_ItemDivision_by_dist($dist_id,$plant_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function Get_ItemDivwise_list($params=FALSE){
        
        
        $group_id = $params['group_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_ItemDivwise_list($group_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$dd);
                                return $response;*/
               return $success; 
    }
    public function Get_itemlist($params=FALSE){
        
        $plant_id = $params['plant_id'];
        $dist_type = $params['dist_type'];
        $dist_state_id = $params['dist_state_id'];
        $item_division = $params['item_division'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_itemlist($dist_type,$dist_state_id,$item_division,$plant_id);
       
               
               return $success; 
    }
    
    public function Get_all_dist_type($params=FALSE){
        
        
        $plant_id = $params['plant_id'];
        
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_all_dist_type($plant_id);
       
               
               return $success; 
    }
    
    public function Get_Allitemlist($params=FALSE){
        
        
        $dist_type = $params['dist_type'];
        $dist_state_id = $params['dist_state_id'];
        $plant_id = $params['plant_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_Allitemlist($dist_type,$dist_state_id,$plant_id);
       
               
               return $success; 
    }
    
    public function Get_next_order_number($params=FALSE){
        
        
        
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_next_order_number();
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$dd);
                                return $response;*/
               return $success; 
    }
    
    public function search_Customer($params=FALSE){
        
        
        $search_key = $params['customer_name'];
        $plant_id= $params['plant_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->search_Customer($search_key,$plant_id);
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$dd);
                                return $response;*/
               return $success; 
    }
    
    public function single_Customer_detail($params=FALSE){
        
        
        $customer_id = $params['customer_id'];
        $plant_id = $params['plant_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->single_Customer_detail($customer_id,$plant_id);
       
               /* $response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$customer_id);
                                return $response;*/
               return $success; 
    }
    
    public function Get_CustomerGroup($params=FALSE){
        
        
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->Get_CustomerGroup();
       
                /*$response=array("status"=>true,"message"=>"You have logined successfully","user_data"=>$user_data);
                                return $response;*/
               return $success; 
    }
    
    public function addCustomer($params=FALSE){
        
        $company = $params['company'];
        $vat = $params['gst'];
        $phonenumber = $params['phonenumber'];
        $country = $params['country'];
        $city = $params['city'];
        $zip = $params['zip'];
        $state = $params['state'];
        $address = $params['address'];
        $addedfrom = $params['addedfrom'];
        
        $groups_in = $params['groups_in'];
        
        
        $is_primary = $params['is_primary'];
        $firstname = $params['firstname'];
        $lastname = $params['lastname'];
        $title = $params['title'];
        $email = $params['email'];
        $password = $params['password'];
        
            $companydata=array(
                                "company"=>$company,
                                "vat"=>$vat,
                                "phonenumber"=>$phonenumber,
                                "country"=>$country,
                                "city"=>$city,
                                "zip"=>$zip,
                                "state"=>$state,
                                "address"=>$address,
                                "addedfrom"=>$addedfrom        
                            );  
            $companydata['datecreated'] = date('Y-m-d H:i:s');
            
            if (isset($companydata['groups_in'])) {
            $groups_in = $companydata['groups_in'];
            unset($companydata['groups_in']);
        }
            
            $this->db->insert(db_prefix() . 'clients', $companydata);
            $userid = $this->db->insert_id();
            
            if ($userid) {
                                    
            $contactdata=array(
                        "userid"=>$userid,             
                        "is_primary"=>$is_primary,
                        "firstname"=>$firstname,
                        "lastname"=>$lastname,
                        "title"=>$title,
                        "email"=>$email,
                        "password"=>$password
                    );
            
            if (isset($contactdata['password'])) {
            $password_before_hash = $contactdata['password'];
            $contactdata['password']     = app_hash_password($contactdata['password']);
        }
        
        $this->db->insert(db_prefix() . 'contacts', $contactdata);
        $contact_id = $this->db->insert_id();
        
       /* if (isset($groups_in)) {
                foreach ($groups_in as $group) {*/
                    $this->db->insert(db_prefix() . 'customer_groups', [
                        'customer_id' => $userid,
                        'groupid'     => $groups_in,
                    ]);
               /* }
            }*/
        
       
                $response=array("status"=>true,"message"=>"You have Create New Customer successfully");
                                return $response;
               //return $success; 
        }
    }
    
    public function increment_next_order_number()
    {
        // Update next invoice number in settings
        $this->db->where('name', 'next_order_number');
        $this->db->set('value', 'value+1', false);
        $this->db->update(db_prefix() . 'options');
    }
    
    public function addenquiry($params=FALSE){
        
        $staff_id = $params['staff_id'];
        $farm_name = $params['farm_name'];
        $contact_person = $params['contact_person'];
        $cp_mobile_no = $params['cp_mobile_no'];
        $address = $params['address'];
        $remark = $params['remark'];
        $state = $params['state'];
        $district = $params['district'];
        $area = $params['area'];
        $revisit = $params['revisit'];
        $status = $params['status'];
        $date = date('Y-m-d');
            $enquirydata=array(
                                "staff_id"=>$staff_id,
                                "farm_name"=>$farm_name,
                                "contact_person"=>$contact_person,
                                "cp_mobile_no"=>$cp_mobile_no,
                                "address"=>$address,
                                "remark"=>$remark,
                                "state"=>$state,
                                "district"=>$district,
                                "area"=>$area,
                                "revisit"=>$revisit,
                                "status"=>$status,
                                "Enq_date"=>$date
                            );  
           
            
            $this->db->insert(db_prefix() . 'so_enquiry', $enquirydata);
            $enquiryid = $this->db->insert_id();
            
            if ($enquiryid) {
                                    
            
       
                $response=array("status"=>true,"message"=>"You have Create New Enquiry successfully");
                                return $response;
               //return $success; 
        }
    }
    
    public function addtour($params=FALSE){
        
        
        $staff_id = $params['staff_id'];
        $cust_ID = $params['cust_ID'];
        $purpose = $params['purpose'];
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $state = $params['state'];
        $city = $params['city'];
        $area = $params['area'];
        $remark = $params['remark'];
        $status = $params['status'];
        $reason = $params['reason'];
        $PlantID = $params['PlantID'];
        
            $tourdata=array(
                "staff_id"=>$staff_id,
                "PlantID"=>$PlantID,
                "cust_ID"=>$cust_ID,
                "purpose"=>$purpose,
                "start_date"=>$start_date,
                "end_date"=>$end_date,
                "remark"=>$remark,
                "state"=>$state,
                "city"=>$city,
                "area"=>$area,
                "status"=>$status,
                "reason"=>$reason,
            );  
           
            
            $this->db->insert(db_prefix() . 'tour', $tourdata);
            $tourid = $this->db->insert_id();
            
            if ($tourid) {
                                    
            
       
                $response=array("status"=>true,"message"=>"You have Create New Tour successfully");
                                return $response;
               //return $success; 
        }
    }
    
    public function Add_verson($params=FALSE){
        
        $verson = $params['verson'];
        $app_url = $params['app_url'];
        $status = 1;
        
            $appdata=array(
                                "verson"=>$verson,
                                "app_url"=>$app_url,
                                "status"=>$status      
                            );  
            $appdata['created_date'] = date('Y-m-d H:i:s');
            
            $this->db->insert(db_prefix() . 'app_version', $appdata);
            $insertid = $this->db->insert_id();
            
            if ($insertid) {
                                    
                $this->db->where("id !=",$insertid); 
                $this->db->set('status',0);
                $this->db->update(db_prefix() . 'app_version');
       
                $response=array("status"=>true,"message"=>"You have Create New Verson successfully");
                                return $response;
               //return $success; 
        }
    }
//======================= Add/ Remove Wishlist Items ===========================    
    public function AddWishlistAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "AccountID"=>$decode['phonenumber'],
                        "CenterID"=>$decode['CenterID'],
                        "ItemID"=>$decode['ItemID'],
                        "Type"=>$decode['Type'],
                        "flag"=>$decode['flag'],
                        "Transdate"=>date('Y-m-d H:i:s')
                    );        
                    $response=$this->AddWishlist($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }     
        echo json_encode($response);    
    }
    
    public function AddWishlist($params=FALSE)
    {
        $AccountID = $params['AccountID'];
        $CenterID = $params['CenterID'];
        $ItemID = $params['ItemID'];
        $Transdate = date('Y-m-d H:i:s');
        if($params['flag'] == "Y"){
            $data=array(
                "AccountID"=>$AccountID,
                "CenterID"=>$CenterID,
                "ItemID"=>$ItemID,
                "Type"=>$params['Type'],
                "Transdate"=>$Transdate,
            );  
            $this->db->insert(db_prefix() . 'wishlist', $data);
            $inserted_id = $this->db->insert_id();
            if ($inserted_id) {
                $response=array("status"=>true,"data"=>$data,"message"=>"Item added in wishlist successfully");
                return $response;                     
            }else{
                $response=array("status"=>true,"data"=>$data,"message"=>"Item not added in wishlist");
                return $response;
            }
        }else{
            $this->db->where('AccountID', $AccountID);
            $this->db->where('CenterID', $CenterID);
            $this->db->where('ItemID', $ItemID);
            $this->db->where('Type', $params['Type']);
            if($this->db->delete(db_prefix() . 'wishlist')){
                $response=array("status"=>true,"data"=>$data,"message"=>"Item deleted from wishlist successfully");
                return $response;
            }else{
                $response=array("status"=>true,"data"=>$data,"message"=>"Item not deleted from wishlist");
                return $response;
            }
        }
    }
    
//========================== Get Wishlist ======================================
    public function GetWishlistAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "Type"=>$decode['Type'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetWishlist($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetWishlist($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $lang = load_client_language($params['phonenumber']);
            $this->db->select('tblwishlist.*,,tblitems.ItemName,tblCenterMaster.CenterName,tblRateMaster.Rate');
            $this->db->join('tblitems', 'tblitems.ItemID = tblwishlist.ItemID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblwishlist.CenterID');
            $this->db->join('tblRateMaster', 'tblRateMaster.CenterID = tblwishlist.CenterID AND tblRateMaster.ItemID = tblwishlist.ItemID');
            $this->db->where('tblRateMaster.IsActive','Y'); 
            if($checkLoginTokan->CustomerType == "1"){
                $this->db->where('tblRateMaster.Type','F'); 
            }else{
                $this->db->where('tblRateMaster.Type','T'); 
            }
            $this->db->where('tblwishlist.Type',$params['Type']); 
            $this->db->where('tblwishlist.AccountID',$params['phonenumber']); 
            $WishList = $this->db->get(db_prefix().'wishlist')->result_array();
            $i = 0;
            foreach($WishList as $Key=>$val){
            	$ItemName = 
            	$WishList[$i]['CenterName'] = _l(strtoupper($val["CenterName"]));
            	$WishList[$i]['ItemName'] = _l(strtoupper($val["ItemName"]));
            	$i++;
            }
            $response = array("status"=>true,"message"=>" Wish List","WishList"=>$WishList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function Get_App_Version($params=FALSE)
    {
        $this->load->model('UserApp_Model');
        $status = 1;
        $success = $this->UserApp_Model->Get_App_Version($status);
        return $success; 
    }
   
      public function AddExpense_API($param=FALSE) {
            $response = array();
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $content_type=$_SERVER['CONTENT_TYPE'];
                if ($content_type!="application/json") {
                    $response = array("error" => true,"message" => "Invalid content type.");  
                }
                else
                {
                    $content=trim(file_get_contents("php://input"));
                    $decode=json_decode($content,true);
                    
                    if($decode['date'] == ''){
                         $response = array("error" => true,"message" => "Date is requird field."); 
                    }else{
                    $this->load->model('UserApp_Model');
                     $UserAccount = $this->UserApp_Model->Get_user_details_by_userID($decode['UserID']);
            $AccountID = $UserAccount->AccountID;
                    if($decode['image1']){
                        
                          $image1 = base64_decode($decode['image1']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                    //   $staff_d = $this->db->get_where('tblstaff',array('staffid',$decode['UserID']))->row();
                    //   mkdir($staff_d->AccountID);
                       if (!file_exists('assets/expense_file/'.$AccountID)) {
                        mkdir('assets/expense_file/'.$AccountID, 0777, true);
                    }
                    $path1 = "assets/expense_file/".$AccountID."/".$filename;
                   
                    file_put_contents($path1 , $image1);
                    // array_push($all_img_url,$path1);
                    }else{
                      $path1 = '';  
                    }
                    
                    if($decode['image2']){
                        
                          $image2 = base64_decode($decode['image2']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                    //   $staff_d = $this->db->get_where('tblstaff',array('staffid',$decode['UserID']))->row();
                    //   mkdir($staff_d->AccountID);
                      if (!file_exists('assets/expense_file/'.$AccountID)) {
                        mkdir('assets/expense_file/'.$AccountID, 0777, true);
                    }
                    $path2 = "assets/expense_file/".$AccountID."/".$filename;
                   
                    file_put_contents($path2 , $image2);
                    // array_push($all_img_url,$path2);
                    }else{
                      $path2 = '';  
                    }
                    if($decode['image3']){
                        
                          $image3 = base64_decode($decode['image3']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                    //   $staff_d = $this->db->get_where('tblstaff',array('staffid',$decode['UserID']))->row();
                    //   mkdir($staff_d->AccountID);
                      if (!file_exists('assets/expense_file/'.$AccountID)) {
                        mkdir('assets/expense_file/'.$AccountID, 0777, true);
                    }
                    $path3 = "assets/expense_file/".$AccountID."/".$filename;
                   
                    file_put_contents($path3 , $image3);
                    // array_push($all_img_url,$path3);
                    }else{
                      $path3 = '';  
                    }
                    if($decode['image4']){
                        
                          $image4 = base64_decode($decode['image4']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                    //   $staff_d = $this->db->get_where('tblstaff',array('staffid',$decode['UserID']))->row();
                    //   mkdir($staff_d->AccountID);
                      if (!file_exists('assets/expense_file/'.$AccountID)) {
                        mkdir('assets/expense_file/'.$AccountID, 0777, true);
                    }
                    $path4 = "assets/expense_file/".$AccountID."/".$filename;
                   
                    file_put_contents($path4 , $image4);
                    // array_push($all_img_url,$path4);
                    }else{
                      $path4 = '';  
                    }
                    if($decode['image5']){
                        
                          $image5 = base64_decode($decode['image5']);
                    $image_name = md5(uniqid(rand(), true));
                    $filename = $image_name . '.' . 'png';
                    //rename file name with random number
                    //   $staff_d = $this->db->get_where('tblstaff',array('staffid',$decode['UserID']))->row();
                    //   mkdir($staff_d->AccountID);
                      if (!file_exists('assets/expense_file/'.$AccountID)) {
                        mkdir('assets/expense_file/'.$AccountID, 0777, true);
                    }
                    $path5 = "assets/expense_file/".$AccountID."/".$filename;
                   
                    file_put_contents($path5 , $image5);
                    // array_push($all_img_url,$path5);
                    }else{
                      $path5 = '';  
                    }
                  
                                $data=array(
                                     "image_path1"=>$path1,
                                             "image_path2"=>$path2,
                                             "image_path3"=>$path3,
                                             "image_path4"=>$path4,
                                             "image_path5"=>$path5,
                                            // "image_path"=>$path,
                                            "PlantID"=>$decode['PlantID'],
                                            "UserID"=>$decode['UserID'],
                                            "date"=>$decode['date'],
                                            "da_type"=>$decode['da_type'],
                                            "market"=>$decode['market'],
                                            "travel_mode"=>$decode['travel_mode'],
                                            "travel_expenses"=>$decode['travel_expenses'],
                                            "kilometer"=>$decode['kilometer'],
                                            "misc_expenses"=>$decode['misc_expenses'],
                                            "reason"=>$decode['reason'],
                                            "previous_file"=>$decode['previous_file']
                                          );
                                
                                $response=$this->addexpenses_data($data);
                    }       
                    }
                }
                
            echo json_encode($response);    
        }
    
    public function addexpense($params=FALSE){
        
        $this->load->model('UserApp_Model');
        $image_path = $params['image_path'];
        $date = $params['date'];
        $PlantID = $params['PlantID'];
        $UserID = $params['UserID'];
        $UserAccount = $this->UserApp_Model->Get_user_details_by_userID($UserID);
        $AccountID = $UserAccount->AccountID;
        $da_type = $params['da_type'];
        $market = $params['market'];
        $travel_mode = $params['travel_mode'];
        $travel_expenses = $params['travel_expenses'];
        $kilometer = $params['kilometer'];
        $misc_expenses = $params['misc_expenses'];
        $reason = $params['reason'];
        $previous_file = $params['previous_file'];
        
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        
        if($previous_file == 1){
            $last_row=$this->db->select('image_path,id')->order_by('id',"desc")->limit(1)->get_where(db_prefix() . 'claimexpense',array('UserID'=>$AccountID))->row();
            $image_path = $last_row->image_path;
        }
            $expanse_data=array(
                                "image_path"=>$image_path,
                                "PlantID"=>$PlantID,
                                "FY"=>$FY,
                                "UserID"=>$AccountID,
                                "date"=>$date,
                                "da_type"=>$da_type,
                                "market"=>$market,
                                "travel_mode"=>$travel_mode,
                                "travel_expenses"=>$travel_expenses,
                                "kilometer"=>$kilometer,
                                "misc_expenses"=>$misc_expenses,
                                "reason"=>$reason,
                                "previous_file"=>$previous_file,
                            );  
           
            
            $this->db->insert(db_prefix() . 'claimexpense', $expanse_data);
            $expense_id = $this->db->insert_id();
            
            if ($expense_id) {
                                    
            if($image_path != '' || $previous_file != ''){
                $response=array("file_uploaded"=>"1","status"=>true,"data"=>$expanse_data,"message"=>"You have Create New Expense successfully");
                                return $response;
               return $success;  
            }else{
                 $response=array("file_uploaded"=>"0","status"=>true,"data"=>$expanse_data,"message"=>"You have Create New Expense successfully");
                                return $response;
               return $success; 
            }
       
               
        }
    }
    public function addexpenses_data($params=FALSE){
         $this->load->model('UserApp_Model');
         
        $UserID = $params['UserID'];
        
        $UserAccount = $this->UserApp_Model->Get_user_details_by_userID($UserID);
        $ACTID = $UserAccount->AccountID;
        
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
        
        $all_img_url = array();
        $image_path1 = $params['image_path1'];
        $image_path2 = $params['image_path2'];
        $image_path3 = $params['image_path3'];
        $image_path4 = $params['image_path4'];
        $image_path5 = $params['image_path5'];
        if($image_path1 != ''){
             array_push($all_img_url,$image_path1);
        }
         if($image_path2 != '' ){
             array_push($all_img_url,$image_path2);
        }
         if($image_path3 != '' ){
             array_push($all_img_url,$image_path3);
        }
         if($image_path4 != '' ){
             array_push($all_img_url,$image_path4);
        }
         if($image_path5 != '' ){
             array_push($all_img_url,$image_path5);
        }
            $all_img  = implode(",",$all_img_url);
            
        $date = $params['date'];
        $PlantID = $params['PlantID'];
        $UserID = $params['UserID'];
        $da_type = $params['da_type'];
        $market = $params['market'];
        $travel_mode = $params['travel_mode'];
        $travel_expenses = $params['travel_expenses'];
        $kilometer = $params['kilometer'];
        $misc_expenses = $params['misc_expenses'];
        $reason = $params['reason'];
        $previous_file = $params['previous_file'];
            
        $this->db->select();
        $this->db->from(db_prefix() . 'claimexpense');
        $this->db->where(db_prefix() . 'claimexpense.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'claimexpense.UserID', $ACTID);
        $this->db->where(db_prefix() . 'claimexpense.FY', $FY);
        $this->db->where(db_prefix() . 'claimexpense.date', $date);
        $this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
     $array_data = $this->db->get()->row_array();
    //  echo $this->db->last_query();die;
    //  print_r($array_data);die;
        if(count($array_data) > 0){
            
            
        $images = explode(",",$array_data['image_path']);
         
        foreach($images as $val){
            
             array_push($all_img_url,$val);
        }
        $all_img_url1  = implode(",",$all_img_url);
    
            $expanse_data=array(
                                "image_path"=>$all_img_url1,
                                "PlantID"=>$PlantID,
                                "FY"=>$FY,
                                "UserID"=>$ACTID,
                                "date"=>$date,
                                "da_type"=>$da_type,
                                "market"=>$market,
                                "travel_mode"=>$travel_mode,
                                "travel_expenses"=>$travel_expenses,
                                "kilometer"=>$kilometer,
                                "misc_expenses"=>$misc_expenses,
                                "reason"=>$reason,
                                "previous_file"=>$previous_file,
                                "Lupdate" =>date('Y-m-d H:i:s'),
                            );   
                   $expanse_data1=array(
                                "image_path"=>$all_img_url,
                                "PlantID"=>$PlantID,
                                "FY"=>$FY,
                                "UserID"=>$ACTID,
                                "date"=>$date,
                                "da_type"=>$da_type,
                                "market"=>$market,
                                "travel_mode"=>$travel_mode,
                                "travel_expenses"=>$travel_expenses,
                                "kilometer"=>$kilometer,
                                "misc_expenses"=>$misc_expenses,
                                "reason"=>$reason,
                                "previous_file"=>$previous_file,
                                "image_path1" => $params['image_path1'],
                                "image_path2" => $params['image_path2'],
                                "image_path3" => $params['image_path3'],
                                "image_path4" => $params['image_path4'],
                                "image_path5" => $params['image_path5'],
                                
                            ); 
                            // print_r($expanse_data);die;
                    $this->db->where(db_prefix() . 'claimexpense.PlantID', $PlantID);
                    $this->db->where(db_prefix() . 'claimexpense.UserID', $ACTID);
                    $this->db->where(db_prefix() . 'claimexpense.FY', $FY);
                    $this->db->where(db_prefix() . 'claimexpense.date', $date);
                    $expense_id= $this->db->update(db_prefix() . 'claimexpense', $expanse_data);
        // echo $this->db->last_query();die;
            // $this->db->insert(db_prefix() . 'claimexpense', $expanse_data);
            // $expense_id = $this->db->insert_id();
            
            if ($expense_id) {
                                    
            if($image_path != '' || $previous_file != ''){
                $response=array("file_uploaded"=>"1","status"=>true,"data"=>$expanse_data1,"message"=>"You have Update Expense successfully");
                                return $response;
               return $success;  
            }else{
                 $response=array("file_uploaded"=>"0","status"=>true,"data"=>$expanse_data1,"message"=>"You have Update Expense successfully");
                                return $response;
               return $success; 
            }   
        }
        }else{
           $date = $params['date'];
        $PlantID = $params['PlantID'];
        $UserID = $params['UserID'];
        $da_type = $params['da_type'];
        $market = $params['market'];
        $travel_mode = $params['travel_mode'];
        $travel_expenses = $params['travel_expenses'];
        $kilometer = $params['kilometer'];
        $misc_expenses = $params['misc_expenses'];
        $reason = $params['reason'];
        $previous_file = $params['previous_file'];
      
        if($previous_file == 1){
            $last_row=$this->db->select('image_path,id')->order_by('id',"desc")->limit(1)->get_where(db_prefix() . 'claimexpense',array('UserID'=>$ACTID))->row();
            $image_path = $last_row->image_path;
             $all_img_url  = implode(",",$last_row->image_path);
        }
            $expanse_data=array(
                                "image_path"=>$all_img,
                                "PlantID"=>$PlantID,
                                "FY"=>$FY,
                                "UserID"=>$ACTID,
                                "date"=>$date,
                                "da_type"=>$da_type,
                                "market"=>$market,
                                "travel_mode"=>$travel_mode,
                                "travel_expenses"=>$travel_expenses,
                                "kilometer"=>$kilometer,
                                "misc_expenses"=>$misc_expenses,
                                "reason"=>$reason,
                                "previous_file"=>$previous_file,
                            );   
                   $expanse_data1=array(
                                "image_path"=>$all_img_url,
                                "PlantID"=>$PlantID,
                                "FY"=>$FY,
                                "UserID"=>$ACTID,
                                "date"=>$date,
                                "da_type"=>$da_type,
                                "market"=>$market,
                                "travel_mode"=>$travel_mode,
                                "travel_expenses"=>$travel_expenses,
                                "kilometer"=>$kilometer,
                                "misc_expenses"=>$misc_expenses,
                                "reason"=>$reason,
                                "previous_file"=>$previous_file,
                            ); 
         
            $this->db->insert(db_prefix() . 'claimexpense', $expanse_data);
            $expense_id = $this->db->insert_id();
            
            if ($expense_id) {
                                    
            if($image_path != '' || $previous_file != ''){
                $response=array("file_uploaded"=>"1","status"=>true,"data"=>$expanse_data1,"message"=>"You have Create New Expense successfully");
                                return $response;
               return $success;  
            }else{
                 $response=array("file_uploaded"=>"0","status"=>true,"data"=>$expanse_data1,"message"=>"You have Create New Expense successfully");
                                return $response;
               return $success; 
            }
       
               
        } 
        }
    }
    
    public function GetExpense_API($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                // return $decode;
                  $data=array(
                                         
                                        "PlantID"=>$decode['PlantID'],
                                        "UserID"=>$decode['UserID'],
                                        "date"=>$decode['date']
                                       
                                      );
                   $response=$this->getexpense($data);
                            
                        
                }
            }
            
          
        
        echo json_encode($response);    
    }
     public function getexpense($params=FALSE){
        
         $this->load->model('UserApp_Model');
        
        $date = $params['date'];
        $PlantID = $params['PlantID'];
        $UserID = $params['UserID'];
        
        $UserAccount = $this->UserApp_Model->Get_user_details_by_userID($UserID);
        $AccountID = $UserAccount->AccountID;
        
        $this->db->select();
        $this->db->from(db_prefix() . 'claimexpense');
        $this->db->where(db_prefix() . 'claimexpense.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'claimexpense.UserID', $AccountID);
        $this->db->where(db_prefix() . 'claimexpense.date', $date);
        $this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
     $array_data = $this->db->get()->row_array();
      
            
            if (count($array_data) > 0) {
                      if($array_data['image_path'] !=""){
                          $images = explode(",",$array_data['image_path']); 
                      }else{
                           $images = [];
                      }              
               
                $response=array("status"=>true,"data"=>$array_data,"images"=>$images);
                                return $response;
               
            }else{
                 $response=array("status"=>false,"message"=>"No Data Found");
                                return $response;
               
            }
       
               
        }
        	public function get_targetandAchievementAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                               "staff_id"=>$decode['staff_id'],
                               "PlantID"=>$decode['PlantID'],
                               
                            );
                            $response=$this->Get_targetAchievementAPI($data);
                }
            }
        
        echo json_encode($response);    
    } 
      public function Get_targetAchievementAPI($params=FALSE){
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->targetAchievementAPI($staff_id,$PlantID);
        return $success; 
    }
    public function Get_division_targetandAchievementAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                               "staff_id"=>$decode['staff_id'],
                               "PlantID"=>$decode['PlantID'],
                               "month"=>$decode['month'],
                               "party_id"=>$decode['DistId'],
                               
                            );
                            $response=$this->Get_division_targetAchievementAPI($data);
                }
            }
        
        echo json_encode($response);    
    }
       public function Get_division_targetAchievementAPI($params=FALSE){
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $month = $params['month'];
        $party_id = $params['party_id'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->division_targetAchievementAPI($staff_id,$PlantID,$month,$party_id);
        return $success; 
    }
    
    public function GetSaleReportAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                
                            $data=array(
                               "staff_id"=>$decode['staff_id'],
                               "PlantID"=>$decode['PlantID'],
                               "AsOn"=>$decode['AsOn'],
                               "admin"=>$decode['admin'],
                            );
                            $response=$this->GetSaleReport($data);
                }
            }
        
        echo json_encode($response);    
    }
       public function GetSaleReport($params=FALSE){
        $staff_id = $params['staff_id'];
        $PlantID = $params['PlantID'];
        $AsOn = $params['AsOn'];
        $admin = $params['admin'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->GetSaleReport($staff_id,$PlantID,$AsOn,$admin);
        return $success; 
    }
    
    public function GetSaleDetailAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                            $data=array(
                               "SaleID"=>$decode['SaleID']
                            );
                            $response=$this->GetSaleDetail($data);
                }
            }
        echo json_encode($response);    
    }
    
    public function CheckAccountCodeAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                            $data=array(
                               "AccountID"=>$decode['AccountID']
                            );
                            $response=$this->CheckAccountID($data);
                }
            }
        echo json_encode($response);    
    }
    public function CheckAccountID($params=FALSE){
        $AccountID = $params['AccountID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->CheckAccountID($AccountID);
        return $success; 
    }
    
    
    
    public function GetSaleDetail($params=FALSE){
        $SaleID = $params['SaleID'];
        $this->load->model('UserApp_Model');
        $success = $this->UserApp_Model->GetSaleDetails($SaleID);
        return $success; 
    }
    
    public function GetOfficeAddressAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                    $data=array(
                        "staff_id"=>$decode['staff_id']
                    );  
                $response=$this->GetOfficeAddress($data);
                }
            }
        echo json_encode($response);    
    }
    
    public function GetOfficeAddress($params=FALSE){
        
        $this->load->model('UserApp_Model');
        $staff_id= $params['staff_id'];
        $get_data = $this->UserApp_Model->GetOfficeAddress($staff_id);
        
        return $get_data;
    }
    
    
//=============== Get All News =================================================
    public function GetNewsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetnewsList($data);
            }
        }
        echo json_encode($response,JSON_UNESCAPED_SLASHES);    
    }
    
    public function GetnewsList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        // $checkLoginTokan=1;
        if($checkLoginTokan){
            $this->db->select('tblnews.*');
            $this->db->where('tblnews.status', 1);
            $this->db->order_by('tblnews.id',"DESC");
            $newsdata = $this->db->get(db_prefix().'news')->result_array();
            if (count($newsdata) > 0) {
                $filteredNewsData = [];
                foreach ($newsdata as $newsList) {
                    $languages = $newsList['language'];
                    $languageArray = explode(",", $languages);
                    
                    if (in_array($checkLoginTokan->default_language, $languageArray)) {
                        $newsList['newsphoto'] = stripslashes(base_url().'uploads/staff_profile_images/news/'.$newsList['id'].'/'.$newsList['newsphoto']);
                        $filteredNewsData[] = $newsList;
                    }
                }
                $newsdata = $filteredNewsData;
            }
            $response = array("status"=>true,"message"=>"News List","newsList"=>$newsdata);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    // Get Account Details By Short Code 
    public function GetAccountDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "AccountCode"=>$decode['AccountCode'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetAccountDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetAccountDetails($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            $this->db->select('tblclients.AccountID,tblclients.company AS FirmName,tblCustomerType.Name AS AccountTypeName,tblclients.CustomerType,
                IFNULL(tblAadharDetails.state, "") AS state,IFNULL(tblAadharDetails.dist,"") AS dist,IFNULL(tblAadharDetails.subdist,"") AS subdist,
                IFNULL(tblAadharDetails.po,"") AS po,IFNULL(tblAadharDetails.loc,"") AS loc,IFNULL(tblAadharDetails.street,"") AS street,
            IFNULL(tblAadharDetails.house,"") AS house,IFNULL(tblAadharDetails.pincode,"") AS pincode,IFNULL(tblcontacts.aadhaar_number,"") AS aadhaar_number,IFNULL(tblcontacts.Pan,"") AS Pan,
            IFNULL(tblGstRecord.gstin,"") AS gstin,IFNULL(tblGstRecord.state_code,"") AS state_code,IFNULL(tblGstRecord.state,"") AS GSTState,IFNULL(tblGstRecord.address,"") AS GSTAddress,
            IFNULL(tblGstRecord.taxpayer_type,"") AS taxpayer_type');
            $this->db->join(db_prefix() . 'CustomerType', '' . db_prefix() . 'CustomerType.id = ' . db_prefix() . 'clients.CustomerType');
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID');
            $this->db->join(db_prefix() . 'AadharDetails', '' . db_prefix() . 'AadharDetails.AccountID = ' . db_prefix() . 'clients.AccountID AND tblAadharDetails.Type = "1"','LEFT');
            $this->db->join(db_prefix() . 'GstRecord', '' . db_prefix() . 'GstRecord.AccountID = ' . db_prefix() . 'clients.AccountID AND tblGstRecord.IsPrimary = "1"','LEFT');
            $this->db->where('tblclients.ShortCode',$params['AccountCode']);
            $AccountDetails = $this->db->get(db_prefix().'clients')->row();
            if($AccountDetails){
                $this->db->select('tblBankDetails.ifsc,bankName,branchName,AaccountName,accountNumber,IsPrimary,cheque_image');
                $this->db->where('tblBankDetails.AccountID',$AccountDetails->AccountID);
                $BankList = $this->db->get(db_prefix().'BankDetails')->result_array();
                $AccountDetails->BankList = $BankList;
            }
            $response = array("status"=>true,"message"=>"Account Details","AccountDetails"=>$AccountDetails);
        }else{
            $response = array("status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }
    
    // Get Account Details By Short Code 
    public function GetAccountDetailsNewAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "AccountCode"=>$decode['AccountCode'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetAccountDetailsNew($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetAccountDetailsNew($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            $this->db->select('tblclients.AccountID,tblclients.company,CustomerType,
            house,street,loc,vtc,po,subdist,dist,state,zip,IFNULL(tblcontacts.aadhaar_number,"") AS aadhaar_number,IFNULL(tblcontacts.Pan,"") AS Pan');
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID');
            $this->db->where('tblclients.ShortCode',$params['AccountCode']);
            $AccountDetails = $this->db->get(db_prefix().'clients')->row();
            
            if($AccountDetails){
                // Aadhaar Details
                $this->db->select('tblAadharDetails.*');
                $this->db->where('tblAadharDetails.AccountID',$AccountDetails->AccountID);
                $AadharDetails = $this->db->get(db_prefix().'AadharDetails')->result_array();
                $AccountDetails->AadharDetails = $AadharDetails;
                
                // GST Details
                $this->db->select('tblGstRecord.*');
                $this->db->where('tblGstRecord.AccountID',$AccountDetails->AccountID);
                $GstRecord = $this->db->get(db_prefix().'GstRecord')->result_array();
                $AccountDetails->GstRecord = $GstRecord;
                
                // Bank Details
                $this->db->select('tblBankDetails.ifsc,bankName,branchName,AaccountName,accountNumber,IsPrimary,cheque_image');
                $this->db->where('tblBankDetails.AccountID',$AccountDetails->AccountID);
                $BankList = $this->db->get(db_prefix().'BankDetails')->result_array();
                $AccountDetails->BankList = $BankList;
            }
            $response = array("status"=>true,"message"=>"Account Details","AccountDetails"=>$AccountDetails);
        }else{
            $response = array("status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }
//================== Comapy Details ============================================
    public function CompanyDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "PlantID"=>$decode['PlantID'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetCompanyDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCompanyDetails($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            $this->db->select('tblPlantMaster.*');
            $this->db->where('tblPlantMaster.PlantID',$params['PlantID']);
            $PlantDetails = $this->db->get(db_prefix().'PlantMaster')->row();
            
            $response = array("status"=>true,"message"=>"Plant Details","PlantDetails"=>$PlantDetails);
        }else{
            $response = array("status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }
    
//================== Center Details ============================================
    public function CenterDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "CenterID"=>$decode['CenterID'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetCenterDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCenterDetails($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            $this->db->select('tblCenterMaster.*');
            $this->db->where('tblCenterMaster.CenterID',$params['CenterID']);
            $CenterDetails = $this->db->get(db_prefix().'CenterMaster')->row();
            
            $response = array("status"=>true,"message"=>"Cenetr Details","CenterDetails"=>$CenterDetails);
        }else{
            $response = array("status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }
    
    // Generate Gate IN and inward data send from PC SOFT
    public function GetInwardDataFromPcSoftAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "cocd"=>$decode['COCD'],
                    "doc_ref"=>$decode['doc_ref'],
                    "AsnID"=>$decode['AsnID'],
                    "GRN"=>$decode['GRN'],
                    "chl_bag"=>$decode['chl_bag'],
                    "chl_katta"=>$decode['chl_katta'],
                    "gross_wt"=>$decode['gross_wt'],
                    "tare_wt"=>$decode['tare_wt'],
                    "no_of_layer"=>$decode['no_of_layer'],
                    "final_rate"=>$decode['final_rate'],
                    "afterCleaningWt"=>$decode['afterCleaningWt'],
                    "WHID"=>$decode['WHID'],
                    "CHID"=>$decode['CHID'],
                    "StackID"=>$decode['StackID'],
                    "LotID"=>$decode['LotID'],
                    "driver_mobile"=>$decode['driver_mobile'],
                    "vehicle_number"=>$decode['vehicle_number'],
                    "weigh_bridge_slip_no"=>$decode['weigh_bridge_slip_no'],
                    "Quality_parameter"=>$decode['Quality_parameter'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetInwardDataFromPcSoft($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetInwardDataFromPcSoft($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            if(empty($params['AsnID'])){
                $response = array("status"=>false,"message"=>"GIC ASNID is required");
            }elseif(empty($params['doc_ref'])){
                $response = array("status"=>false,"message"=>"GIC BookingID is required",);
            }elseif(empty($params['gross_wt'])){
                $response = array("status"=>false,"message"=>"Gross Weight is required");
            }elseif(empty($params['tare_wt'])){
                $response = array("status"=>false,"message"=>"Tare Weight is required",);
            }else{
                $this->db->select('tblGateMaster.TType,tblGateMaster.TType2,tblGateMaster.ItemID,tblGateMaster.PartyID,
                tblGateMaster.FY,tblGateMaster.PlantID,tblGateMaster.AccountID,tblGateMaster.basic_rate,
                tblGateMaster.CenterID,tblclients.CustomerType,tblclients.state');
                $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'GateMaster.AccountID AND tblclients.PlantID = tblGateMaster.PlantID');
                $this->db->where('tblGateMaster.ASNID',$params['AsnID']);
                $this->db->where('tblGateMaster.BookingID',$params['doc_ref']);
                $AccountDetails = $this->db->get(db_prefix().'GateMaster')->row();
                $pcSoft_GRN = $params['GRN'];
                if(empty($AccountDetails)){
                    $response = array("status"=>false,"message"=>"GIC ASNID is not found");
                }else{
                    $NetWT_MT = $params['gross_wt'] - $params['tare_wt'];
                    $rate = $AccountDetails->basic_rate * 10;
                    $PurchAmt = $NetWT_MT * $rate;
                    if($AccountDetails->CustomerType == "1"){
                        $cgst_per = 0;
                        $sgst_per = 0;
                        $igst_per = 0;
                    }else{
                        if($AccountDetails->state == "MH"){
                            $cgst_per = 2.5;
                            $sgst_per = 2.5;
                            $igst_per = 0;
                        }else{
                            $sgst_per = 0;
                            $cgst_per = 0;
                            $igst_per = 5.0;
                        }
                    }
                    $saleRate = ($rate * 0.05) + $rate;
                    $CenterID = $AccountDetails->CenterID;
                    $PlantID = $AccountDetails->PlantID;
                    $FY = $AccountDetails->FY;
                    $qc_details = $params['Quality_parameter'];
                    $BookingID = $params['doc_ref'];
                    $new_Number = get_number($CenterID,'GATE');
                    $number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
                    $GateINID = "G".$CenterID.date('d').date('m').date('y').$number;
                    $gate_in_array = array(
                        "GodownID"=>$params['WHID'],
                        "ChamberID"=>$params['CHID'],
                        "StackID"=>$params['StackID'],
                        "LOTID"=>$params['LotID'],
                        "Gate_in_ID"=>$GateINID,
                        "gate_in_by"=>'PCSOFT',
                        "gate_in_date"=>date('Y-m-d H:i:s'),
                        "final_rate"=>$rate/10,
                        "VehicleNo"=>$params['vehicle_number'],
                        "Phone"=>$params['driver_mobile'],
                        "weigh_bridge_slip_no"=>$params['weigh_bridge_slip_no'],
                        "LoadedWeight"=>$params['gross_wt'] * 10,
                        "LWUserID"=>'PCSOFT',
                        "LWTransDate"=>date('Y-m-d H:i:s'),
                        "TareWeight"=>$params['tare_wt'] * 10,
                        "TWUserID"=>'PCSOFT',
                        "TWTransDate"=>date('Y-m-d H:i:s'),
                        "no_of_layers"=>$params['no_of_layer'],
                        "gate_out_by"=>'PCSOFT',
                        "gate_out_date"=>date('Y-m-d H:i:s'),
                        "exit_by"=>'PCSOFT',
                        "exit_date"=>date('Y-m-d H:i:s'),
                        "status"=>15, // pending
                    );
                    $this->db->where('BookingID', $params['doc_ref']);
                    $this->db->where('ASNID', $params['AsnID']);
                    $this->db->update(db_prefix() . 'GateMaster',$gate_in_array);
                    if($this->db->affected_rows() > 0){
                        
                        // Number mapping PcSoft and GIC
                        $insert_referance = array(
                            "Type"=>$AccountDetails->TType,
                            "Name"=>"GateIN",
                            "GIC_Reference"=>$GateINID,
                            "pcsoft_doc_ref"=>$pcSoft_GRN
                        );
                        $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                        
                        $this->increment_number($CenterID,'GATE');
                        
                        // qc Parameter details insert
                        // Get Warehouse, Chamber,Stack and lot details
                        $this->db->select('tblwarehouse.AccountID');
                        $this->db->where('tblwarehouse.center',$CenterID);
                        $WHDetails = $this->db->get(db_prefix().'warehouse')->row();
                        $WHID = $WHDetails->AccountID;
                        
                        $this->db->select('tblWHSizeMaster.CHID');
                        $this->db->where('tblWHSizeMaster.WHID',$WHID);
                        $CHDetails = $this->db->get(db_prefix().'WHSizeMaster')->row();
                        $CHID = $CHDetails->CHID;
                        
                        $this->db->select('tblwhstackmaster.StackID');
                        $this->db->where('tblwhstackmaster.CHID',$CHID);
                        $STDetails = $this->db->get(db_prefix().'whstackmaster')->row();
                        $StackID = $STDetails->StackID;
                        
                        $this->db->select('tbllot_master.LOTID');
                        $this->db->where('tbllot_master.StackID',$StackID);
                        $LOTDetails = $this->db->get(db_prefix().'lot_master')->row();
                        $LOTID = $LOTDetails->LOTID;
                        
                        $i = 1;
                        foreach($qc_details as $Key=>$value){
                            $stack_data = array(
                                "BookingID"=>$BookingID,
                                "GateINID"=>$GateINID,
                                "QCID"=>$value["LotNo"],
                                "TransDate"=>date('Y-m-d H:i:s'),
                                "TType"=>"P",
                                "ItemID"=>$AccountDetails->ItemID,
                                "AccountID"=>$AccountDetails->AccountID,
                                "PartyID"=>$AccountDetails->PartyID,
                                "WHID"=>$WHID,
                                "CHID"=>$CHID,
                                "StackID"=>$StackID,
                                "LOTID"=>$LOTID,
                                "Weight"=>$value["LotWeight"],
                                "BagQty"=>$value["BagQty"],
                                "UserID"=>'PCSOFT',
                            );
                            $this->db->insert(db_prefix() . 'stockInventory',$stack_data);
                            foreach($value["QCDetails"] as $key=>$val){
                                $GetMyQCID = $this->GetMyQCID($val['TESTNO']);
                                if($i == "1"){
                                    // Peripharal QC
                                    $qc_data = array(
                                        "BookingID"=>$BookingID,
                                        "Gate_in_ID"=>$GateINID,
                                        "layer_number"=>$value["LotNo"],
                                        "TType"=>"P",
                                        "ItemID"=>$AccountDetails->ItemID,
                                        "ItemParameterID"=>$GetMyQCID->ItemParameterID,
                                        "ParameterValue"=>$val['READING'],
                                        "EParameterValue"=>$val['READING'],
                                        "HParameterValue"=>$val['READING'],
                                        "deductionAmt"=>0,
                                        "TransDate"=>date('Y-m-d H:i:s'),
                                        "UserID"=>'PCSOFT',
                                    );
                                    $this->db->insert(db_prefix() . 'QCParameterValues',$qc_data);
                                }
                                // Final QC
                                $qc_data = array(
                                    "BookingID"=>$BookingID,
                                    "Gate_in_ID"=>$GateINID,
                                    "layer_number"=>$value["LotNo"],
                                    "TType"=>"F",
                                    "ItemID"=>$AccountDetails->ItemID,
                                    "ItemParameterID"=>$GetMyQCID->ItemParameterID,
                                    "ParameterValue"=>$val['READING'],
                                    "EParameterValue"=>$val['READING'],
                                    "HParameterValue"=>$val['READING'],
                                    "deductionAmt"=>0,
                                    "TransDate"=>date('Y-m-d H:i:s'),
                                    "UserID"=>'PCSOFT',
                                );
                                $this->db->insert(db_prefix() . 'QCParameterValues',$qc_data);
                            }
                        $i++;
                        }
                        // unloading details insert
                        $unloading_details = array(
                            "BookingID"=>$BookingID,
                            "Gate_in_ID"=>$GateINID,
                            "total_bags"=>$params['chl_bag'],
                            "total_katta"=>$params['chl_katta'],
                            "total_layers"=>$params['no_of_layer'],
                        );
                        $this->db->insert(db_prefix() . 'UnloadingMaster',$unloading_details);
                        
                        // Layer master insert
                        $layer_master = array(
                            "BookingID"=>$BookingID,
                            "Gate_in_ID"=>$GateINID,
                            "layer_number"=>$params['no_of_layer'],
                            "qty"=>$params['chl_bag'],
                            "unit"=>"Bag",
                            "TransDate"=>date('Y-m-d H:i:s'),
                            "UserID"=>'PCSOFT',
                        );
                        $this->db->insert(db_prefix() . 'LayerMaster',$layer_master);
                        
                        $data_array_result = array(
                            'PlantID'=>$PlantID,
                            'FY'=>$FY,
                            'cnfid' =>1,
                            'OrderID' =>$GateINID,
                            'TransDate' =>date('Y-m-d H:i:s'),
                            'BillID' =>$BookingID,
                            'GodownID' =>$params['WHID'],
                            'CenterID' =>$CenterID,
                            'TypeID' =>"SP",
                            'PartyID' =>$params['COCD'],
                            'ChamberID' =>$params['CHID'],
                            'StackID' =>$params['StackID'],
                            'LOTID' =>$params['LotID'],
                            'TransDate2'=>date('Y-m-d H:i:s'),
                            'TType'=>$AccountDetails->TType,
                            'TType2'=> $AccountDetails->TType2,
                            'AccountID'=>$AccountDetails->AccountID,
                            'ItemID'=>$AccountDetails->ItemID,
                            'CaseQty'=>1,
                            'PurchRate'=>$rate,
                            'SaleRate'=>$saleRate,
                            'BasicRate'=>$rate,
                            'SuppliedIn'=>"MT",
                            'Cases'=>$NetWT_MT,
                            'OrderQty'=>$NetWT_MT,
                            'BilledQty'=>$NetWT_MT,
                            'cgst'=>$cgst_per,
                            'sgst'=>$sgst_per,
                            'igst'=>$igst_per,
                            'Ordinalno'=>1,
                            'UserID'=>"PCSOFT"
                        );
                        $this->db->insert(db_prefix() . 'history',$data_array_result);
                        $response = array("Status"=>true,"SuccessMessage"=>"Inward Data insert successfully","doc_ref_number"=>$GateINID);
                    }else{
                        $response = array("Status"=>false,"SuccessMessage"=>"inward data not inserted please try again","doc_ref_number"=>"");
                    }// Gate Master Entry End
                }// ASN Data Not found
            }// Data validation end
        }else{
            $response = array("Status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }
    
    public function GetMyQCID($pcsoft_Qc_id)
    {
        $this->db->select('tblItemParameter.*,');
        $this->db->where('tblItemParameter.pc_soft_parameter',$pcsoft_Qc_id);
        $ParaDetails = $this->db->get(db_prefix().'ItemParameter')->row();
           
        return $ParaDetails; 
    }
    
    public function GetMyOtherDeductionItemID($PcSoftOtherDedID)
    {
        $this->db->select('tblitems.*');
        $this->db->where_in('tblitems.PCItemID',$PcSoftOtherDedID);
        $OtherDeductionItems = $this->db->get(db_prefix().'items')->result_array();
        return $OtherDeductionItems; 
    }
    
    public function GetPIDataFromPcSoftAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "access_tokan"=>$decode['access_tokan'],
                    "GateINID"=>$decode['GateINID'],
                    "GRN"=>$decode['GRN'],
                    "PI"=>$decode['PI'],
                    "Quality_parameter"=>$decode['QCparameters'],
                    "OtherDeduction"=>$decode['Other_deduction'],
                    "purchase_amt"=>$decode['purchase_amt'],
                    "debit_amt"=>$decode['debit_amt'],
                    "purchase_gst"=>$decode['purchase_gst'],
                    "debit_gst"=>$decode['debit_gst'],
                );
                $response = $this->GetPIDataFromPcSoft($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetPIDataFromPcSoft($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            /*if(empty($params['BookingID'])){
                $response = array("status"=>false,"message"=>"GIC Booking ID not found");
            }elseif(empty($params['POID'])){
                $response = array("status"=>false,"message"=>"Your PO Number not found");
            }elseif(empty($params['AsnID'])){
                $response = array("status"=>false,"message"=>"GIC ASN not found");
            }elseif(empty($params['GIN'])){
                $response = array("status"=>false,"message"=>"Your GIN Number not found");
            }else*/if(empty($params['GateINID'])){
                $response = array("status"=>false,"message"=>"GIC GateIN ID not found");
            }elseif(empty($params['GRN'])){
                $response = array("status"=>false,"message"=>"Your GRN Number not found");
            }elseif(empty($params['PI'])){
                $response = array("status"=>false,"message"=>"Your PI Number not found");
            }elseif(empty($params['Quality_parameter'])){
                $response = array("status"=>false,"message"=>"Quality parameter not found");
            }elseif(is_null($params['purchase_amt'])){
                $response = array("status"=>false,"message"=>"purchase amt not found");
            }elseif(is_null($params['debit_amt'])){
                $response = array("status"=>false,"message"=>"debit amt not found");
            }elseif(is_null($params['purchase_gst'])){
                $response = array("status"=>false,"message"=>"purchase gst not found");
            }elseif(is_null($params['debit_gst'])){
                $response = array("status"=>false,"message"=>"debit gst not found");
            }else{
                $this->db->select('tblGateMaster.BookingID,tblGateMaster.TType,tblGateMaster.TType2,tblGateMaster.ItemID,
                tblGateMaster.FY,tblGateMaster.PlantID,tblGateMaster.AccountID,tbltaxes.taxrate,tblGateMaster.PartyID,tblGateMaster.GodownID,
                tblGateMaster.basic_rate,tblGateMaster.CenterID,tblGateMaster.LoadedWeight,tblGateMaster.TareWeight,clients.CustomerType,tblclients.state');
                $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'GateMaster.AccountID AND tblclients.PlantID = tblGateMaster.PlantID');
                $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'GateMaster.ItemID AND tblitems.PlantID = tblGateMaster.PlantID');
                $this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax');
                $this->db->where('tblGateMaster.Gate_in_ID',$params['GateINID']);
                //$this->db->where('tblGateMaster.ASNID',$params['AsnID']);
                //$this->db->where('tblGateMaster.BookingID',$params['BookingID']);
                $AccountDetails = $this->db->get(db_prefix().'GateMaster')->row();
                /*return $AccountDetails;
                die;*/
                if(empty($AccountDetails)){
                    $response = array("status"=>false,"message"=>"record not found");
                }else{
                    // Check PI Generate or not
                    $this->db->select('tblpurchasemaster.PurchID,');
                    $this->db->where('tblpurchasemaster.TransID',$params['GateINID']);
                    $CheckPIGenerate = $this->db->get(db_prefix().'purchasemaster')->row();
                    if($CheckPIGenerate){
                        // mapping data for PI
                        $insert_referance = array(
                            "Type"=>$AccountDetails->TType,
                            "Name"=>"PI",
                            "GIC_Reference"=>$CheckPIGenerate->PurchID,
                            "pcsoft_doc_ref"=>$params['PI']
                        );
                        if($this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance)){
                           $response = array("Status"=>true,"message"=>"PI Data insert successfully","doc_ref_number"=>$CheckPIGenerate->PurchID); 
                        }
                    }else{
                        $PlantID = $AccountDetails->PlantID;
                        $FY = $AccountDetails->FY;
                        $GateINID = $params['GateINID'];
                        $BookingID = $AccountDetails->BookingID;
                        $purchase_amt = $params['purchase_amt'];
                        $purchase_gst = $params['purchase_gst'];
                        $debit_amt = $params['debit_amt'];
                        $debit_gst = $params['debit_gst'];
                        $basic_rate = $AccountDetails->basic_rate;
                        $Gst_per = $AccountDetails->taxrate;
                        $NetWeight = $AccountDetails->LoadedWeight - $AccountDetails->TareWeight;
                        $POAmount = $NetWeight * $basic_rate;
                        $CenterID = $AccountDetails->CenterID;
                        $state = $AccountDetails->state;
                        if($AccountDetails->CustomerType == "1"){
                            $bt = "B";
                            $GstAmt = 0;
                            $cgst_amt = 0;
                            $sgst_amt = 0;
                            $igst_amt = 0;
                        }else{
                            $bt = "T";
                            $GstAmt = ($POAmount * $Gst_per) /100;
                            if($state = "27"){
                            $cgst_amt = $GstAmt / 2;
                            $sgst_amt = $GstAmt / 2;
                            }else{
                                $igst_amt = $GstAmt;
                            }
                        }
                        
                        $NetPOAmount = $POAmount + $GstAmt;
                        // Get Next Purchase Number
                        $new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
                        $Billno = "PO".$FY.$new_poNumber;
                        
                        $data_array = array(
                            'PlantID'=>$PlantID,
                            'FY'=>$FY,
                            'BT'=>$bt,
                            'PurchID' =>$Billno,
                            'TransID' =>$GateINID,
                            'Transdate' =>date('Y-m-d H:i:s'),
                            'AccountID'=>$AccountDetails->AccountID,
                            'CenterID'=>$AccountDetails->CenterID,
                            'PurchType'=>"A",
                            'PartyID'=>$AccountDetails->PartyID,
                            'WHID'=>$AccountDetails->GodownID,
                            'Invoiceno'=>NULL,
                            'Invoicedate'=>NULL,
                            'Purchamt'=> $POAmount,
                            'Discamt'=>0,
                            'Frtamt'=>0,
                            'Othamt'=>0,
                            'Invamt'=>$NetPOAmount,
                            'ItCount'=>1,
                            'RoundOffAmt'=>NULL,
                            'OthAccountID'=>NULL,
                            'cgstamt'=>$cgst_amt,
                            'sgstamt'=>$sgst_amt,
                            'igstamt'=>$igst_amt,
                            'tcs'=>NULL,
                            'tcsAmt'=>NULL
                        );
                        
                        $this->db->insert(db_prefix() . 'purchasemaster',$data_array);
                        if($this->db->affected_rows() > 0){
                            // mapping data for PI
                            $insert_referance = array(
                                "Type"=>$AccountDetails->TType,
                                "Name"=>"PI",
                                "GIC_Reference"=>$Billno,
                                "pcsoft_doc_ref"=>$params['PI']
                            );
                            $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                            
                            // Increment PO number
                            $this->increment_next_ponumber($FY,$PlantID);
                            // update deduction as per parameter
                            $Quality_parameter = $params['Quality_parameter'];
                            foreach($Quality_parameter as $key=>$value){
                                foreach($value["QCDetails"] as $key=>$val){
                                    $GetMyQCID = $this->GetMyQCID($val['TESTNO']);
                                    $qc_data = array(
                                        "deductionAmt"=>$val['deduction_amt'],
                                    );
                                    $this->db->where('BookingID', $BookingID);
                                    $this->db->where('Gate_in_ID', $GateINID);
                                    $this->db->where('layer_number', $value["LotNo"]);
                                    $this->db->where('TType', "F");
                                    $this->db->where('ItemParameterID', $GetMyQCID->ItemParameterID);
                                    $this->db->update(db_prefix() . 'QCParameterValues',$qc_data);
                                }
                            }
                            
                            // Update Other deduction amount
                            $OtherDeduction = $params['OtherDeduction'];
                            $deductionCode = array();
                            foreach($OtherDeduction as $key=>$val){
                                array_push($deductionCode,$val["ItemID"]);
                            }
                            $GetMyOtherDeductionItemID = $this->GetMyOtherDeductionItemID($deductionCode);
                            foreach($OtherDeduction as $key=>$val){
                                foreach($GetMyOtherDeductionItemID as $pcKey=>$pcVal){
                                    if($val["ItemID"]==$pcVal["PCItemID"]){
                                        $GICItemID = $pcVal["ItemID"];
                                    }
                                }
                                if($GICItemID == "SDC"){
                                    $ParticularItemID = "SDC";
                                }elseif($GICItemID == "BG"){
                                    $ParticularItemID = "BG";
                                }else{
                                    $ParticularItemID = "QOD";
                                }
                                $addDeduction = array(
                                    "BookingID"=>$BookingID,
                                    "GateINID"=>$GateINID,
                                    "TransID"=>$Billno,
                                    "ParticularItemID"=>$ParticularItemID,
                                    "ItemID"=>$GICItemID,
                                    "quantity"=>0,
                                    "Amount"=>$val["deduction_amt"],
                                    "UserID"=>"PCSOFT",
                                    "TransDate"=>date('Y-m-d H:i:s')
                                );
                                $this->db->insert('tblotherdeduction',$addDeduction);
                            }
                            
                            // Add Account Ledger Entry 
    			            $ledger_result = $this->GateControl_model->GenerateLedgerEntryForPurchase($BookingID,$GateINID,$POAmount);
                            if($AccountDetails->CustomerType == "1"){
                                
                            }else{
    			                // ganerate debit note for other than farmer
        			            $this->GateControl_model->GenerateDebitNote($BookingID,$GateINID);
                            }
                            $DeductionDetails = $this->GateControl_model->GetdeductionDetails($BookingID,$GateINID);
                            $Total_deduction = 0;
                    		foreach($DeductionDetails as $Key=>$val){
                    		    $Total_deduction += $val["deductionAmt"];
                    		}
                    		$OtherDeduction = $this->GateControl_model->GetActualOtherDeductionList($BookingID,$GateINID);
                    		foreach($OtherDeduction as $okey=>$oval){
                    		    $Total_deduction += $oval["Amount"];
                    		}
                    		$final_rate = ($POAmount - $Total_deduction) / $NetWeight; // Calculate Final rate per quintal
                    		if($AccountDetails->CustomerType == "1"){
                    		    $POAmount = $final_rate * $NetWeight;
                    		    $NetPOAmount = $POAmount;
                    		    $updatePurchase = array(
                    		        'Purchamt'=> $POAmount,
                                    'Invamt'=>$POAmount,
                    		    );
                    		    $this->db->where('PurchID', $Billno);
                                $this->db->where('TransID', $GateINID);
                                $this->db->update(db_prefix() . 'purchasemaster',$updatePurchase);
                    		}
                            $data_array_result = array(
                                'final_rate'=>$final_rate * 10,
                                'PartyID'=>$AccountDetails->PartyID,
                                'TransID'=>$Billno,
                                'cgstamt'=>$cgst_amt,
                                'sgstamt'=>$sgst_amt,
                                'igstamt'=>$igst_amt,
                                'OrderAmt'=>$POAmount,
                                'ChallanAmt'=>$POAmount,
                                'NetOrderAmt'=>$NetPOAmount,
                                'NetChallanAmt'=>$NetPOAmount,
                            );
                            $this->db->where('BillID', $BookingID);
                            $this->db->where('OrderID', $GateINID);
                            $this->db->update(db_prefix() . 'history',$data_array_result);
                            
                            $gate_control = array(
                                'final_rate'=>$final_rate * 10,
                                "status"=>16,
                            );
                            $this->db->where('BookingID', $BookingID);
                            $this->db->where('Gate_in_ID', $GateINID);
                            $this->db->update(db_prefix() . 'GateMaster',$gate_control);
                            $response = array("Status"=>true,"message"=>"PI Data insert successfully","doc_ref_number"=>$Billno);
                        }else{
                            $response = array("Status"=>false,"message"=>"PI not generate","doc_ref_number"=>"");
                        }
                    }
                }// Transaction detail found
            }// All validation check
        }else{
            $response = array("status"=>false,"message"=>"access token not matched");
        }
        return $response; 
    }

    public function GetInvoiceDataAPI($param=FALSE) {
        $response = array('status' => false, 'message' => 'Invalid content type.', 'code' => 400);
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $content_type = $_SERVER['CONTENT_TYPE'];
            if ($content_type != "application/json") {
                $response = array("status" => false, "message" => "Invalid content type.", "code" => 400);  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "access_tokan" => $decode['access_tokan'],
                    "PurchID" => $decode['PurchID'],
                    "InvoiceID" => $decode['InvoiceID'],
                );

                if($data['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
                    $validate = ['PurchID','InvoiceID'];
                    $isValidate = true;
                    foreach ($validate as $value) {
                        if (empty($data[$value])) {
                            $response = array("status" => false, "message" => $value." is required", "code" => 404);
                            $isValidate = false;
                            break;
                        }
                    }

                    if($isValidate){
                        $insert_referance = array(
                            "Type"=>'P',
                            "Name"=>"PI",
                            "GIC_Reference"=>$data['PurchID'],
                            "pcsoft_doc_ref"=>$data['InvoiceID']
                        );
                        $check = $this->db->select('*')->where($insert_referance)->get(db_prefix().'pcsoft_gic_number_referance')->row();
                        if($check){
                            $response = array("status" => true, "message" => "PI Data already exist", "code" => 302);
                        }else{
                            $save = $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
                            if($save){
                                $response = array("status" => true, "message" => "PI Data insert successfully", "code" => 201); 
                            }else{
                                $response = array("status" => false, "message" => "Failed to Mapping Invoice", "code" => 500);
                            }
                        }
                    }
                }else{
                    $response = array("status" => false, "message" => "access token not matched", "code" => 400);
                }
            }
        }else{
            $response = array("status" => false, "message" => "Request method not allowed", "code" => 405);
        }
        echo json_encode($response);
    }
    
    public function UpdatePaymentStatusAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "access_tokan"=>$decode['access_tokan'],
                    "GateINID"=>$decode['GateINID'],
                    "payment_amt"=>$decode['payment_amt'],
                    "narration"=>$decode['narration'],
                    "FromAccount"=>$decode['FromAccount'],
                    "utr_no"=>$decode['utr_no'],
                    "PaymentDateTime"=>$decode['PaymentDateTime']
                );
                $response = $this->UpdatePaymentStatus($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdatePaymentStatus($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fdd"){
            // Ganerate Payment Voucher
            $this->db->select('tblGateMaster.*');
            $this->db->where('tblGateMaster.Gate_in_ID',$params['GateINID']);
            $AccountDetails = $this->db->get(db_prefix().'GateMaster')->row();
            $new_voucher_number = get_option2('next_payment_number_for_kirti',$AccountDetails->FY);
            $i = 1;
            $debit_data = array(
                "PlantID" =>$AccountDetails->PlantID,
                "Transdate" =>$params['PaymentDateTime'],
                "TransDate2" =>date('Y-m-d H:i:s'),
                "VoucherID" =>$new_voucher_number,
                "AccountID" =>$AccountDetails->AccountID,
                "TType" =>"D",
                "CenterID" =>$AccountDetails->CenterID,
                "CommodityID" =>$AccountDetails->ItemID,
                "EntryFor" =>"3",
                "PartyID" =>$AccountDetails->PartyID,
                "Amount" =>$params['payment_amt'],
                "Narration" =>$params['narration'],
                "CounterAccount" =>$params['FromAccount'],
                "PassedFrom" =>"PAYMENTS",
                "OrdinalNo" =>$i,
                "UserID" =>$this->session->userdata('username'),
                "FY" =>$AccountDetails->FY,
                "UniquID" =>'',
            );
            if($this->db->insert(db_prefix().'accountledger', $debit_data)){
                $i++;
            }
                
            $credit_data = array(
                "PlantID" =>$AccountDetails->PlantID,
                "Transdate" =>$params['PaymentDateTime'],
                "TransDate2" =>date('Y-m-d H:i:s'),
                "VoucherID" =>$new_voucher_number,
                "AccountID" =>$params['FromAccount'],
                "TType" =>"C",
                "CenterID" =>$AccountDetails->CenterID,
                "CommodityID" =>$AccountDetails->ItemID,
                "EntryFor" =>"3",
                "PartyID" =>$AccountDetails->PartyID,
                "Amount" =>$params['payment_amt'],
                "Narration" =>$params['narration'],
                "CounterAccount" =>$AccountDetails->AccountID,
                "PassedFrom" =>"PAYMENTS",
                "OrdinalNo" =>$i,
                "UserID" =>"PCSOFT",
                "FY" =>$AccountDetails->FY,
                "UniquID" =>'',
            );
            if($this->db->insert(db_prefix().'accountledger', $credit_data)){
                $i++;
            }
            if($i>1){
                $payment_status = array(
                    "payment_done"=>1,
                    "payment_approved_date"=>$params['PaymentDateTime'],
                    "payment_approved_by"=>"PCSOFT",
                    "IsPayment"=>'Y',
                    "PaymentVoucherID"=>$new_voucher_number,
    			);
    			$this->db->where('Gate_in_ID', $params['GateINID']);
                $this->db->update(db_prefix() . 'GateMaster',$payment_status);
                $this->increment_next_payment_number($AccountDetails->FY,$AccountDetails->PlantID);
                $response = array("status"=>true,"message"=>"Payment status updated successfully");
            }else{
                $response = array("status"=>false,"message"=>"Payment status update failed");
            }
        }else{
            $response = array("status"=>false,"message"=>"access token not matched");
        }
        return $response; 
    }
    
    public function increment_next_payment_number($FY,$PlantID)
    {
        // Update next PAYMENT number in settings
        if($PlantID == 1){
            $this->db->where('name', 'next_payment_number_for_kirti');
        }
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }
    
//=================== Star Agri API Start ======================================
    
    // Get Account Details By mobile and pan 
    public function GetPartyDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "depositor_mobile_no"=>$decode['depositor_mobile_no'],
                    "depositor_PAN"=>$decode['depositor_PAN'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->GetPartyDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetPartyDetails($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae55d88fff"){
            $this->db->select('tblclients.AccountID,tblclients.company AS FirmName,tblCustomerType.Name AS AccountTypeName,tblclients.CustomerType,
            tblAadharDetails.state,tblAadharDetails.dist,tblAadharDetails.subdist,tblAadharDetails.po,tblAadharDetails.loc,tblAadharDetails.street,
            tblAadharDetails.house,tblAadharDetails.pincode,tblcontacts.aadhaar_number,tblcontacts.Pan,tblGstRecord.gstin,tblGstRecord.state_code,tblGstRecord.state AS GSTState,tblGstRecord.address AS GSTAddress,tblGstRecord.taxpayer_type');
            $this->db->join(db_prefix() . 'CustomerType', '' . db_prefix() . 'CustomerType.id = ' . db_prefix() . 'clients.CustomerType');
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID');
            $this->db->join(db_prefix() . 'AadharDetails', '' . db_prefix() . 'AadharDetails.AccountID = ' . db_prefix() . 'clients.AccountID AND tblAadharDetails.Type = "1"','LEFT');
            $this->db->join(db_prefix() . 'GstRecord', '' . db_prefix() . 'GstRecord.AccountID = ' . db_prefix() . 'clients.AccountID AND tblGstRecord.IsPrimary = "1"','LEFT');
            $this->db->where('tblclients.AccountID',$params['depositor_mobile_no']);
            $this->db->where('tblcontacts.Pan',$params['depositor_PAN']);
            $AccountDetails = $this->db->get(db_prefix().'clients')->row();
            
            $response = array("status"=>true,"message"=>"Depositor Details","DepositorDetails"=>$AccountDetails);
        }else{
            $response = array("status"=>false,"message"=>"login token not matched");
        }
        return $response; 
    }

//=================== Star Agri API END ========================================

//===================== Agri Bazaar API ========================================

    // Check PAN Exist
    public function PANCheckAgriBazaarAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "pan_number"=>$decode['pan_number'],
                    "access_tokan"=>$decode['access_tokan']
                );
                $response = $this->PANCheckAgriBazaar($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PANCheckAgriBazaar($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fddd"){
            $this->db->select('tblcontacts.*');
            $this->db->where('tblcontacts.Pan',$params['pan_number']);
            $PanDetails = $this->db->get(db_prefix().'contacts')->result_array();
            if($PanDetails){
                $response = array("status"=>true,"message"=>"This Pan number is registered with Kisan Kirti.");
            }else{
                $response = array("status"=>false,"message"=>"This Pan number is not registered with Kisan Kirti.");
            }
        }else{
            $response = array("status"=>false,"message"=>"access token not matched");
        }
        return $response; 
    }
    
    
    public function SignINAgriBazaarAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "mobile_no"=>$decode['mobile_no'],
                    "pan_number"=>$decode['pan_number'],
                    "name"=>$decode['name'],
                    "DeviceID"=>$params['DeviceID'],
                    "access_tokan"=>$decode['access_tokan'],
                );
                $response = $this->SignINAgriBazaar($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SignINAgriBazaar($params=FALSE)
    {
        if($params['access_tokan'] == "fe3fd1f94239c467727c5cae504d4fddd"){
            $checkPan = $this->PANCheckfunction($params['pan_number']);
            if($checkPan){
                $response = array("status"=>false,"message"=>"This Pan number is registered with Kisan Kirti.");
            }else{
                $Clientdata =array(
                    "PlantID"=>1,
                    "AccountID"=>$params['mobile_no'],
                    "company"=>$params['name'],
                    "phonenumber"=>$params['mobile_no'],
                    "DeviceID"=>$params['DeviceID'],
                    "StartDate"=>date('Y-m-d H:i:s'),
                    "datecreated"=>date('Y-m-d H:i:s'),
                    "last_login"=>date('Y-m-d H:i:s'),
                );
                $this->db->insert(db_prefix().'clients', $Clientdata);
        		if($this->db->affected_rows() > 0){
        		    $Contactdata =array(
                        "PlantID"=>1,
                        "AccountID"=>$params['mobile_no'],
                        "Pan"=>$params['pan_number'],
                        "firstname"=>$params['name'],
                        "phonenumber"=>$params['mobile_no'],
                        "datecreated"=>date('Y-m-d H:i:s'),
                    );
                    $this->db->insert(db_prefix().'contacts', $Contactdata);
        			$response = array("status"=>true,"message"=>"Record Inserted Successfully");
        		}else{
        		    $response = array("status"=>false,"message"=>"Something Went Wrong");
        		}
            }
        }else{
            $response = array("status"=>false,"message"=>"access token not matched");
        }
        return $response; 
    }
    
    public function PANCheckfunction($pan)
    {
        $this->db->select('tblcontacts.*');
        $this->db->where('tblcontacts.Pan',$pan);
        $PanDetails = $this->db->get(db_prefix().'contacts')->result_array();
        return $PanDetails; 
    }
    
//================ Add Kirti Anamat Trade API ==================================
    public function AddAnamatTradeAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID'],
                    "quantity"=>$decode['quantity'],
                    "equantity"=>$decode['equantity'],
                    "basic_rate"=>$decode['basic_rate'],
                    "unit"=>$decode['unit'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType']
                );
                $response = $this->AnamatTradeAdd($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AnamatTradeAdd($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        
        $Cropsale_data = array(
            "FY"=>$FY,
            "PlantID" => 1,
            "CenterID"=>$params['CenterID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$params['quantity'],
            "e_quantity"=>$params['quantity'],
            "basic_rate"=>$params['basic_rate'],
            "unit"=>$params['unit'],
            "UserID"=>$params['phonenumber'],
            "TransDate"=> date('Y-m-d H:i:s'),
            "TType"=> "A",
            "TType2"=> "Anamat"
        );
        if($params['UserType'] == "2"){
            $Cropsale_data['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $Cropsale_data['BrokerID'] = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $Cropsale_data['BrokerApprove'] = 'Y';
                $Cropsale_data['BrokerID'] = $params['phonenumber'];
                $Cropsale_data['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $Cropsale_data['BrokerApprove'] = 'NA';
                $Cropsale_data['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            $Cropsale_data['BrokerApprove'] = 'NA';
            $Cropsale_data['BrokerID'] = $params['OtherID'];
        }
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'lead_master', $Cropsale_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                // Get Company Purchase for 
                $PartyDetails = $this->GetPurchaseForParty($params['CenterID'],$params['ItemID']);
                if($PartyDetails){
                    $PartyID = $PartyDetails->PartyID;
                }else{
                    $PartyID = "KASPL";
                }
                
                $new_Number = get_number($params['CenterID'],'A');
                 
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $params['CenterID'].'A'.date('d').date('m').date('y').$number;
            
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID,'PartyID'=>$PartyID]);
                $this->increment_center_wise_booking_number($params['CenterID'],'A');
                
                $title = "Trade Created";
                $screen = "1";
                $body = "Your BookingID : ".$bookingID.' Created';
                $booking_id = $bookingID;
                $to = $checkLoginTokan->fcm_token;
            
                if($checkLoginTokan->CustomerType == "1"){
                    // Farmer 
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    //$ids = array($AccountID);
                }else if($checkLoginTokan->CustomerType  == "3"){
                    // Send Notification to Trader
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Broker
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }else if($checkLoginTokan->CustomerType ){
                    // Send Notification to Broker
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Trader
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }
                $response = array("status"=>true,"message"=>"Anamat Trade submitted successfully, we will contact you shortly.","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
	
	public function increment_next_number_debit_note($FY)
	{
		$this->db->where('name', 'next_debit_number_for_kirti');
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
    
    // Add Trade Finance API
    public function AddTradeFinanceAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CenterID"=>$decode['CenterID'],
                    "ItemID"=>$decode['ItemID'],
                    "quantity"=>$decode['quantity'],
                    "equantity"=>$decode['equantity'],
                    "basic_rate"=>$decode['basic_rate'],
                    "unit"=>$decode['unit'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType']
                );
                $response = $this->TradeFinanceAdd($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function TradeFinanceAdd($params=FALSE)
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }else {
            $FY = date('y');
        }
        
        $Cropsale_data = array(
            "FY"=>$FY,
            "PlantID" => 1,
            "CenterID"=>$params['CenterID'],
            "ItemID"=>$params['ItemID'],
            "quantity"=>$params['quantity'],
            "e_quantity"=>$params['quantity'],
            "basic_rate"=>$params['basic_rate'],
            "unit"=>$params['unit'],
            "UserID"=>$params['phonenumber'],
            "TransDate"=> date('Y-m-d H:i:s'),
            "TType"=> "T",
            "TType2"=> "Trade Finance"
        );
        if($params['UserType'] == "2"){
            $Cropsale_data['BrokerApprove'] = 'Y';
            $AccountID = $params['OtherID'];
            $Cropsale_data['BrokerID'] = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            
        }else if($params['UserType'] == "1"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            if($params['OtherID'] == null || $params['OtherID'] == ""){
                $Cropsale_data['BrokerApprove'] = 'Y';
                $Cropsale_data['BrokerID'] = $params['phonenumber'];
                $Cropsale_data['BrokerApproveTime'] = date('Y-m-d H:i:s');
            }else{
                $Cropsale_data['BrokerApprove'] = 'NA';
                $Cropsale_data['BrokerID'] = $params['OtherID'];
            }
        }else if($params['UserType'] == "3"){
            $Cropsale_data['ClientApprove'] = 'Y';
            $AccountID = $params['phonenumber'];
            $Cropsale_data['AccountID'] = $AccountID;
            $Cropsale_data['BrokerApprove'] = 'NA';
            $Cropsale_data['BrokerID'] = $params['OtherID'];
        }
        
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->insert(db_prefix().'lead_master', $Cropsale_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                // Get Company Purchase for 
                $PartyDetails = $this->GetPurchaseForParty($params['CenterID'],$params['ItemID']);
                if($PartyDetails){
                    $PartyID = $PartyDetails->PartyID;
                }else{
                    $PartyID = "KASPL";
                }
                
                $new_Number = get_number($params['CenterID'],'T');
                 
                $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
                $bookingID = $params['CenterID'].'T'.date('d').date('m').date('y').$number;
            
                $this->db->where('id', $insert_id);
                $this->db->update(db_prefix().'lead_master', ["BookingID"=>$bookingID,'PartyID'=>$PartyID]);
                $this->increment_center_wise_booking_number($params['CenterID'],'T');
                
                $title = "Trade Created";
                $screen = "1";
                $body = "Your BookingID : ".$bookingID.' Created';
                $booking_id = $bookingID;
                $to = $checkLoginTokan->fcm_token;
            
                if($checkLoginTokan->CustomerType == "1"){
                    // Farmer 
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    //$ids = array($AccountID);
                }else if($checkLoginTokan->CustomerType  == "3"){
                    // Send Notification to Trader
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Broker
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }else if($checkLoginTokan->CustomerType == "2"){
                    // Send Notification to Broker
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    
                    // Send Notification to Trader
                    $AccountDetails = $this->GetSingleAccountDetails($params['OtherID']);
                    $title = "Trade Created By ".$checkLoginTokan->company;
                    $body = " BookingID : ".$bookingID.' Created';
                    $to = $AccountDetails->fcm_token;
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }
                $response = array("status"=>true,"message"=>"Trade Finance trade submitted successfully, we will contact you shortly.","login_tokan"=>$params['login_tokan']);
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong","login_tokan"=>$params['login_tokan']);
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
    }
    
    public function updateFinalQuantity($BookingID, $GateInID)
    {
        $getControl_details = get_control_details($GateInID);
        $QcDetails = $this->GateControl_model->getSingleFinalQc($BookingID,$GateInID);
        
        $GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID,$GateInID);
        $AccountID = $GateControlDetails->AccountID;
		$PurchID = $GateControlDetails->PurchID;
		$QCDetails = array();
        foreach($QcDetails as $key=>$val){
            $deductionAmt = $val['deductionAmt'];
            $totalDeduction += $deductionAmt;
            array_push($QCDetails , array("TESTNO" => $val['pc_soft_parameter'] , "READING" => number_format($val['HParameterValue'], 2)));
            // $QCDetails[$val['pc_soft_parameter']] = $val['HParameterValue'];
        }
        
        
        $basicValue = $getControl_details->basic_rate * ($getControl_details->LoadedWeight - $getControl_details->TareWeight);
        $ItemWeight = ($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10;
        $FinalRate = ($basicValue - $totalDeduction) / ($ItemWeight * 10);
        
        $this->db->where('BookingID',$BookingID);
		$this->db->where('Gate_in_ID',$GateInID);
		$this->db->update('tblGateMaster',array('final_rate' => $FinalRate));
		    
		$selected_company = $getControl_details->PlantID;
		$fy = $getControl_details->FY;
		
		$ItemWt = ($ItemWeight * 10) * $FinalRate;
		
		// Send Inward data to PCSoft
        //if($GateControlDetails->PartyID == "KDML"){
            $inward_array = array(
                "COCD" =>$GateControlDetails->PartyID,
                "doc_ref" =>$GateControlDetails->ASNID,
                "chl_bag" =>$GateControlDetails->total_bags,
                "chl_katta" =>$GateControlDetails->total_katta,
                "gross_wt" =>$getControl_details->LoadedWeight /10,
                "tare_wt" =>$getControl_details->TareWeight / 10,
                "no_of_lot" =>$GateControlDetails->total_layers,
                "final_rate" =>number_format(($FinalRate * 10), 2, '.', ''),
                "QCparameters"=>$QCDetails
            );
            
            $inward_data = json_encode($inward_array);
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/GATEENTRY/GRRSUBMIT", // --> LIVE
                //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/GATEENTRY/GRRSUBMIT", //--> DEV URL
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $inward_data,
                CURLOPT_HTTPHEADER => array(
                        "content-type: application/json"
                    ),
                )
            );
            $apiResponse = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
        //}
        
        $response_array = json_decode($apiResponse);
        $PcSoft_GIN = $response_array->doc_ref_number;
        $status = $response_array->Status;
        if($status == true){
            $insert_referance = array(
                "Type"=>$details->TType,
                "Name"=>"GateIN",
                "GIC_Reference"=>$GateInID,
                "pcsoft_doc_ref"=>$PcSoft_GIN
            );
            $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);
        }
		$data_array = array(
            'Purchamt'=> $ItemWt,
            'OthAccountID'=>NULL,
            'tcs'=>NULL,
            'tcsAmt'=>NULL,
		);
		$data_array['cgstamt'] = NULL;
	    $data_array['sgstamt'] = NULL;
	    $data_array['igstamt'] = NULL;
	    $data_array['Invamt'] = $ItemWt;
	    $data_array['RoundOffAmt'] = round($ItemWt,2);
		$this->db->where('TransID',$GateInID);
        $this->db->update('tblpurchasemaster',$data_array);
		
        
        $Item_array = array(
            "OrderAmt" => $ItemWt,
            "ChallanAmt" => $ItemWt,
            "NetOrderAmt" => $ItemWt,
            "NetChallanAmt" => $ItemWt,
            "cgst" => NULL,
            "cgstamt" => NULL,
            "sgst" => NULL,
            "sgstamt" => NULL,
            "igst" => NULL,
            "igstamt" => NULL,
            'final_rate' => $FinalRate
        );
		
		$this->db->where('BillID',$BookingID);
		$this->db->where('OrderID',$GateInID);
		if($this->db->update('tblhistory',$Item_array)){
		    // Ledger Entry 
    		$Nerration = "Purchase Against BookingID ".$BookingID."/ GateInID ".$GateInID;
    	    $crLedger = array(
    	        "PlantID" =>  $selected_company,
    	        "FY" =>  $fy,
    	        "Transdate" =>date('Y-m-d H:i:s'),
    	        "VoucherID" =>  $PurchID,
    	        "TransDate2" =>  date('Y-m-d H:i:s'),
    	        "AccountID" =>  $AccountID,
    	        "CenterID" =>  $GateControlDetails->CenterID,
    	        "CommodityID" =>  $GateControlDetails->ItemID,
    	        "EntryFor" =>  2,
    	        "TType" =>  'C',
    	        "Amount" =>  $ItemWt,
    	        "Narration" =>  $Nerration,
    	        "PassedFrom" =>  "PURCHASE",
    	        "OrdinalNo" =>  1,
    	        "UserID" =>  $AccountID,
    	    );
    		$this->db->insert('tblaccountledger',$crLedger);
    		
    		$drLedger = array(
    	        "PlantID" =>  $selected_company,
    	        "FY" =>  $fy,
    	        "Transdate" =>date('Y-m-d H:i:s'),
    	        "VoucherID" =>  $PurchID,
    	        "TransDate2" =>  date('Y-m-d H:i:s'),
    	        "AccountID" =>  "PURCH",
    	        "CenterID" =>  $GateControlDetails->CenterID,
    	        "CommodityID" =>  $GateControlDetails->ItemID,
    	        "EntryFor" =>  2,
    	        "TType" =>  'D',
    	        "Amount" =>  $ItemWt,
    	        "Narration" =>  $Nerration,
    	        "PassedFrom" =>  "PURCHASE",
    	        "OrdinalNo" =>  2,
    	        "UserID" =>  $AccountID,
    	    );
    		$this->db->insert('tblaccountledger',$drLedger);
		}
    }
    
    public function insertSalesMaster($BookingID, $GateINID, $UserID)
    {
        
        $GateControlDetails = $this->GateControl_model->GetControlDetails2($BookingID,$GateINID);
        $PCSoftRef = $this->GateControl_model->GetPCSoftDoc($BookingID);
        $leadMasterDetails = $this->GateControl_model->GetSingleBookingDataDB($BookingID);
        
		$Netweight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
		$purch_amt = $Netweight * $GateControlDetails->basic_rate;
		$TaxRate = $GateControlDetails->taxrate;
		if($PCSoftRef){
		    // Send Data to PCSoft
		    $outward_array = array(
            	"cocd" =>$GateControlDetails->PartyID,
            	"doc_no" =>$PCSoftRef->pcsoft_doc_ref,
            	"im_code" =>$GateControlDetails->ItemID,
            	"net_wt" =>$Netweight/10 // Net Weight in MT
            );
            
            $outward_data = json_encode($outward_array);

            $curl = curl_init();
            curl_setopt_array($curl, array(
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
                "OutwardAPIDateTime"  =>date('Y-m-d H:i:s'),
                "PcSoftStatus"  =>$Status,
                "Iseinvoice"  =>$einvoice,
                "Isewb"  =>$ewb,
                "einvoice_link"  =>$response_array->einvoice_link,
                "ewb_link"  =>$response_array->ewb_link
            );
            $this->db->where('BookingID',$BookingID);
            $this->db->where('Gate_in_ID',$GateINID);
            $this->db->update('tblGateMaster',$pcSoftRes);
            
            $err = curl_error($curl);
            curl_close($curl);
		}
		
		
		$data_array = array(
            'Purchamt'=> $purch_amt,
            'OthAccountID'=>NULL,
            'tcs'=>NULL,
            'tcsAmt'=>NULL,
		);
		
		if($GateControlDetails->CustomerType == '1'){
		    $GstAmt = 0;
		}else{
	        $GstAmt = ($purch_amt * $TaxRate) / 100;
		}
		$invAmt = $purch_amt + $GstAmt;
	    if($TaxRate == "0.00"){
		    $cgst = NULL;
		    $sgst = NULL;
		    $igst = NULL;
		    $cgst_per = NULL;
		    $sgst_per = NULL;
		    $igst_per = NULL;
		}else{
		    if($GateControlDetails->CustomerType == '1'){
		        $cgst = NULL;
    		    $sgst = NULL;
    		    $igst = NULL;
		    }else{
		        $cgst = $GstAmt / 2;
    		    $sgst = $GstAmt / 2;
    		    $igst = $GstAmt;
		    }
		}
		if($GateControlDetails->state = "MH"){
		    if($GateControlDetails->CustomerType == '1'){
		        $igst = NULL;
    		    $cgst_per = NULL;
    		    $sgst_per = NULL;
    		    $igst_per = NULL;
		    }else{
		        $igst = NULL;
    		    $cgst_per = $TaxRate / 2;
    		    $sgst_per = $TaxRate / 2;
    		    $igst_per = NULL;
		    }
		}else{
		    if($GateControlDetails->CustomerType == '1'){
		        $cgst = NULL;
    		    $sgst = NULL;
    		    $igst_per = NULL;
    		    $cgst_per = NULL;
    		    $sgst_per = NULL;
		    }else{
		        $cgst = NULL;
    		    $sgst = NULL;
    		    $igst_per = $TaxRate;
    		    $cgst_per = NULL;
    		    $sgst_per = NULL;
		    }
		}
		$invoiceAmt = $invAmt;
	    $roundAmt = round($invAmt,2);
	    
        $this->db->where('AccountID',$GateControlDetails->AccountID);
        $fetchGstDetails =  $this->db->get('tblGstRecord')->row();
        
        $SalesID = 'TAX' . $GateControlDetails->FY . get_option2('next_tax_number_for_kirti', $GateControlDetails->FY);
        $salesArray = array(
            'PlantID' => $GateControlDetails->PlantID,
            'FY' => $GateControlDetails->FY,
            'BT' => 'T',
            'SalesID' => $SalesID,
            'Transdate' => date('Y-m-d H:i:s'),
            'OrderID' => $GateINID,
            'ChallanID' => $BookingID,
            'AccountID' => $GateControlDetails->AccountID,
            'gstno' => $fetchGstDetails->gstin,
            'PayType' => 'S',
            'SaleAmt' => $purch_amt,
            'sgstamt' => $sgst,
            'cgstamt' => $cgst,
            'igstamt' => $igst,
            'BillAmt' => $invoiceAmt,
            'RndAmt' => $roundAmt,
            'ItCount' => 1,
            'UserID' => $UserID,
        );
        $this->db->insert('tblsalesmaster',$salesArray);
        
        $this->increment_next_number_sale('next_tax_number_for_kirti',$GateControlDetails->FY);
        //Insert into history
        
        $Netweight = ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight) / 10;
        $Netweight = number_format($Netweight, 2, '.', '');
                
        $insertHistoryArray = array(
            'PlantID'=> $GateControlDetails->PlantID,
            'FY'=> $GateControlDetails->FY,
            'cnfid' =>1,
            'OrderID' =>$GateINID,
            "TransID"=>$SalesID,
            'TransDate' =>date('Y-m-d H:i:s'),
            'BillID' =>$BookingID,
            'GodownID' => $GateControlDetails->GodownID,
            'CenterID' => $GateControlDetails->CenterID,
            'PartyID' =>$GateControlDetails->PartyID,
            'ChamberID' =>$GateControlDetails->ChamberID,
            'StackID' =>$GateControlDetails->StackID,
            'LOTID' =>$GateControlDetails->LOTID,
            'TransDate2'=>date('Y-m-d H:i:s'),
            'TypeID'=>'SP',
            'TType'=>'S',
            'TType2'=> 'Sale',
            'AccountID'=>$GateControlDetails->AccountID,
            'ItemID'=>$GateControlDetails->ItemID,
            'CaseQty'=>1,
            'PurchRate'=>$GateControlDetails->basic_rate,
            'SaleRate'=>($GateControlDetails->basic_rate + ($GateControlDetails->basic_rate * $TaxRate) / 100),
            'BasicRate'=>$GateControlDetails->basic_rate,
            'final_rate'=>$GateControlDetails->basic_rate,
            'SuppliedIn'=>$GateControlDetails->unit,
            'Cases'=>$Netweight,
            'OrderQty'=>$Netweight,
            'BilledQty'=>$Netweight,
            'cgst'=>$cgst_per,
            'sgst'=>$sgst_per,
            'igst'=>$igst_per,
            'cgstamt'=>$cgst,
            'sgstamt'=>$sgst,
            'igstamt'=>$igst,
            'OrderAmt'=>$purch_amt,
            'ChallanAmt'=>$purch_amt,
            'NetOrderAmt'=>$invoiceAmt,
            'NetChallanAmt'=>$invoiceAmt,
            'Ordinalno'=>1,
            'UserID'=>$UserID
		);
		$this->db->insert(db_prefix() . 'history',$insertHistoryArray);
        
        //Insert into ledger 
        // Ledger Entry 
		$Nerration = "Sale Against BookingID ".$BookingID."/ GateInID ".$GateINID . " TransID  ".$SalesID;
	    $crLedger = array(
	        "PlantID" =>  $GateControlDetails->PlantID,
	        "FY" =>  $GateControlDetails->FY,
	        "Transdate" =>date('Y-m-d H:i:s'),
	        "VoucherID" =>  $SalesID,
	        "TransDate2" =>  date('Y-m-d H:i:s'),
	        "AccountID" =>  $GateControlDetails->AccountID,
	        "CenterID" =>  $leadMasterDetails->CenterID,
	        "CommodityID" =>  $leadMasterDetails->ItemID,
	        "EntryFor" =>  3,
	        "TType" =>  'D',
	        "Amount" =>  $invoiceAmt,
	        "Narration" =>  $Nerration,
	        "PassedFrom" =>  "SALE",
	        "OrdinalNo" =>  1,
	        "UserID" =>  $this->session->userdata('username'),
	    );
		$this->db->insert('tblaccountledger',$crLedger);
		
		$drLedger = array(
	        "PlantID" =>  $GateControlDetails->PlantID,
	        "FY" =>  $GateControlDetails->FY,
	        "Transdate" =>date('Y-m-d H:i:s'),
	        "VoucherID" =>  $SalesID,
	        "TransDate2" =>  date('Y-m-d H:i:s'),
	        "AccountID" =>  "SALE",
	        "CenterID" =>  $leadMasterDetails->CenterID,
	        "CommodityID" =>  $leadMasterDetails->ItemID,
	        "EntryFor" =>  3,
	        "TType" =>  'C',
	        "Amount" =>  $purch_amt,
	        "Narration" =>  $Nerration,
	        "PassedFrom" =>  "SALE",
	        "OrdinalNo" =>  2,
	        "UserID" =>  $this->session->userdata('username'),
	    );
		$this->db->insert('tblaccountledger',$drLedger);
		
		if($TaxRate == "0.00"){
		    
		}else{
		    if($GateControlDetails->state = "MH"){
			    $cgstLedger = array(
    		        "PlantID" =>  $GateControlDetails->PlantID,
    		        "FY" =>  $GateControlDetails->FY,
    		        "Transdate" =>date('Y-m-d H:i:s'),
    		        "VoucherID" =>  $SalesID,
    		        "TransDate2" =>  date('Y-m-d H:i:s'),
    		        "AccountID" =>  "CGST",
    		        "CenterID" =>  $leadMasterDetails->CenterID,
        	        "CommodityID" =>  $leadMasterDetails->ItemID,
        	        "EntryFor" =>  3,
    		        "TType" =>  'C',
    		        "Amount" =>  $cgst,
    		        "Narration" =>  $Nerration,
    		        "PassedFrom" =>  "SALE",
    		        "OrdinalNo" =>  3,
    		        "UserID" =>  $this->session->userdata('username'),
    		    );
    		    $this->db->insert('tblaccountledger',$cgstLedger);
    		    
    		    $sgstLedger = array(
    		        "PlantID" =>  $GateControlDetails->PlantID,
    		        "FY" =>  $GateControlDetails->FY,
    		        "Transdate" =>date('Y-m-d H:i:s'),
    		        "VoucherID" =>  $SalesID,
    		        "TransDate2" =>  date('Y-m-d H:i:s'),
    		        "AccountID" =>  "SGST",
    		        "CenterID" =>  $leadMasterDetails->CenterID,
        	        "CommodityID" =>  $leadMasterDetails->ItemID,
        	        "EntryFor" =>  3,
    		        "TType" =>  'C',
    		        "Amount" =>  $sgst,
    		        "Narration" =>  $Nerration,
    		        "PassedFrom" =>  "SALE",
    		        "OrdinalNo" =>  4,
    		        "UserID" =>  $this->session->userdata('username'),
    		    );
    		    $this->db->insert('tblaccountledger',$sgstLedger);
    		    
			}else{
			    $igstLedger = array(
    		        "PlantID" =>  $GateControlDetails->PlantID,
    		        "FY" =>  $GateControlDetails->FY,
    		        "Transdate" =>date('Y-m-d H:i:s'),
    		        "VoucherID" =>  $SalesID,
    		        "TransDate2" =>  date('Y-m-d H:i:s'),
    		        "AccountID" =>  "IGST",
    		        "CenterID" =>  $leadMasterDetails->CenterID,
        	        "CommodityID" =>  $leadMasterDetails->ItemID,
        	        "EntryFor" =>  3,
    		        "TType" =>  'C',
    		        "Amount" =>  $igst,
    		        "Narration" =>  $Nerration,
    		        "PassedFrom" =>  "SALE",
    		        "OrdinalNo" =>  3,
    		        "UserID" =>  $this->session->userdata('username'),
    		    );
    		    $this->db->insert('tblaccountledger',$igstLedger);
			}
		}
    }
    
    //=================Customer Enquiry API====================//
 
 
     public function CustomerEnquiry($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }
            else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
               
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
		            "login_tokan"=>$decode['login_tokan'],
					"CustomerType"=>$decode['CustomerType'],
					"full_name"=>$decode['full_name'],
					"mobile_no"=>$decode['mobile_no'],
					"email_id"=>$decode['email_id'],
					"message"=>$decode['message'],
			
                   
                );
                $response = $this->CustomEnquiry($data);
            }
        }
        echo json_encode($response);    
    } 
 
    public function CustomEnquiry($params=FALSE)
    {
	  $enquiry_data = array(
            "AccountID"=>$params['phonenumber'],
            "CustomerType"=>$params['CustomerType'],
            "full_name"=>$params['full_name'],
			"mobile_no"=>$params['mobile_no'],
			"email_id"=>$params['email_id'],
			"message"=>$params['message'],
			"Created_at"=> date('Y-m-d H:i:s')
        );
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            $this->db->insert(db_prefix().'ContactsEnquiry', $enquiry_data);
            $insert_id = $this->db->insert_id();
            if($insert_id){
                $response = array("status"=>true,"message"=>"Enquiry Added Successfully");
            }else{
                $response = array("status"=>false,"message"=>"Something Went Wrong");
            }
        
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        
        return $response; 
   }
   
   // Get Expense Category List
    public function GetExpenseCategoryAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
               
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->GetExpenseCategoryList($data);
            }
        }
        echo json_encode($response);    
    }
    //  Get Expense Category List
    public function GetExpenseCategoryList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            $this->db->select('tblexpenseCategory.id,tblexpenseCategory.CategoryName');
            $expenseList = $this->db->get(db_prefix().'expenseCategory')->result_array();
            $response = array("status"=>true,"message"=>"Expense Category List fetched successfully","Expense Category List"=>$expenseList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
 
	// Add New Expense
	public function AddExpenseAPI($param = FALSE) 
	{
		$response = array();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$content_type = $_SERVER['CONTENT_TYPE'];
			$decode=$_POST;
			$checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);

			if ($checkLoginTokan) {
				if (isset($_FILES['item_image']) && !empty($_FILES['item_image'])) {
					$randdom = round(microtime(time() * 1000)) . rand(000, 999);
					$file_extension1 = pathinfo($_FILES['item_image']["name"], PATHINFO_EXTENSION);
					$file_name1 = $randdom . '.' . $file_extension1;
					$directory = 'uploads/expenseImages';

					if ($_FILES['item_image']["error"] <= 0) {
						move_uploaded_file($_FILES['item_image']['tmp_name'], $directory . '/' . $file_name1);
						$resultString = $file_name1;
					}
				} else {
					$resultString = 'NA';
				}
				$data = array(
					"phonenumber" => $decode['phonenumber'],
					"login_tokan" => $decode['login_tokan'],
					"Category" => $decode['Cat_id'],
					"expense_date" => $decode['expense_date'],
					"travel_distance" => $decode['travel_distance'],
					"Amount" => $decode['Amount'],
					"address" => $decode['address'],
					"remark" => $decode['remark'],
					"file_upload" => $resultString,
				);

				$response = $this->AddExpenseUpdated($data);
			} else {
				$response = array("status" => false, "message" => "Please login with a registered mobile number");
			}
        }
      echo json_encode($response);
    }
	public function AddExpenseUpdated($params=FALSE)
	{
		
		$Exp_data = array(
			"Category" => $params['Category'],
			"expense_date" => date('Y-m-d H:i:s', strtotime($params['expense_date'])),
			"travel_distance" => $params['travel_distance'],
			"Amount" => $params['Amount'],
			"address" => $params['address'],
			"remark" => $params['remark'],
			"file_upload" => $params['file_upload'],
			"created_date" => date('Y-m-d H:i:s'),
			"Lupdate" => date('Y-m-d H:i:s'),
		);
		$this->db->insert(db_prefix().'expenseUpdated', $Exp_data);
		$insert_id = $this->db->insert_id();
		if ($insert_id) {
			$response = array("status" => true, "message" => "Category Added Successfully");
		} else {
			$response = array("status" => false, "message" => "Something Went Wrong");
		}
		return $response;
	}
	
	// Get Expense List Staff wise
	public function GetExpenseAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "phonenumber" => $decode['phonenumber'],
					    "login_tokan" => $decode['login_tokan'],
                    );
                    $response=$this->GetExpenseUpdated($data);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function GetExpenseUpdated($params=FALSE)
    {
        $UserID = $params['phonenumber'];
        $this->db->select('tblexpenseUpdated.*');
        $this->db->from(db_prefix() . 'expenseUpdated');
        //$this->db->where(db_prefix() . 'expenseUpdated.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'expenseUpdated.UserID', $UserID);
        $this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
        $array_data = $this->db->get()->result_array();
        $response = array("status" => true, "message" => "Expenses List","data"=>$array_data);
        return $response;
    }
    
    // Update Expenses
    
	public function UpdateExpenseAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            /*if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{*/
                /*$content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);*/
                $decode=$_POST;
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    
                    if (isset($_FILES['item_image']) && !empty($_FILES['item_image'])) 
                    {
    					$randdom = round(microtime(time() * 1000)) . rand(000, 999);
    					$file_extension1 = pathinfo($_FILES['item_image']["name"], PATHINFO_EXTENSION);
    					$file_name1 = $randdom . '.' . $file_extension1;
    					$directory = 'uploads/expenseImages';
    
    					if ($_FILES['item_image']["error"] <= 0) {
    						move_uploaded_file($_FILES['item_image']['tmp_name'], $directory . '/' . $file_name1);
    						$resultString = $file_name1;
    					}
    				}else {
    					$resultString = 'NA';
    				}
                    
		
                    $data=array(
                        "phonenumber" => $decode['phonenumber'],
					    "id" => $decode['id'],
					    "Category" => $decode['Category'],
    					"expense_date" => $decode['expense_date'],
    					"travel_distance" => $decode['travel_distance'],
    					"Amount" => $decode['Amount'],
    					"address" => $decode['address'],
    					"remark" => $decode['remark']
                    );
                    if($resultString != "NA"){
                        $data['file_upload'] = $resultString;
                    }
                    $response=$this->UpdateExpense($data);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            //}
        }
        echo json_encode($response);    
    }
    
    public function UpdateExpense($params=FALSE)
    {
        $UserID = $params['phonenumber'];
        $id = $params['id'];
        
        $Exp_data = array(
			"Category" => $params['Category'],
			"expense_date" => date('Y-m-d H:i:s', strtotime($params['expense_date'])),
			"travel_distance" => $params['travel_distance'],
			"Amount" => $params['Amount'],
			"address" => $params['address'],
			"remark" => $params['remark'],
			"Lupdate" => date('Y-m-d H:i:s'),
			"UserID2" =>$UserID
		);
		if($params['file_upload']){
		    $Exp_data['file_upload'] = $params['file_upload'];
		}
        $this->db->where(db_prefix() . 'expenseUpdated.UserID', $UserID);
        $this->db->where(db_prefix() . 'expenseUpdated.id', $id);
        if($this->db->update(db_prefix().'expenseUpdated', $Exp_data)){
            $response = array("status" => true, "message" => "Expenses Update Successfully","data"=>$Exp_data);
        }else{
            $response = array("status" => true, "message" => "No change","data"=>$Exp_data);
        }
        
        return $response;
    }
	
//======================= Processed Orders For Kata ============================

	 public function GetProccesDataAPI($param=FALSE)
	{
	   $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "phonenumber" => $decode['phonenumber'],
					    "login_tokan" => $decode['login_tokan'],
						"UserID" => $decode['UserID'],
						"UserType" => $decode['UserType'],
                    );
                    $response=$this->GetProccesData($data);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    public function GetProccesData($params=FALSE)
	{
	  $UserID = $params['UserID'];
	  $UserType = $params['UserType'];
	  if($UserType == "16"){
	      $this->db->select('COUNT(tblGateMaster.id) as TWCount,tblGateMaster.TType,tblGateMaster.TType2');
    	  $this->db->where('tblGateMaster.TWUserID', $UserID);
    	  // add data condition on TransDate
    	  $this->db->group_by('tblGateMaster.TType,tblGateMaster.TType2');
    	  $this->db->order_by('tblGateMaster.id',"ASC");
    	  $array_dataTW = $this->db->get(db_prefix().'GateMaster')->result_array();
     
          $this->db->select('COUNT(tblGateMaster.id) as LWCount,tblGateMaster.TType,tblGateMaster.TType2');
          $this->db->where('tblGateMaster.LWUserID', $UserID);
          // add data condition on TransDate
    	  $this->db->group_by('tblGateMaster.TType,tblGateMaster.TType2');
    	  $this->db->order_by('tblGateMaster.id',"ASC");
    	  $array_dataLW = $this->db->get(db_prefix().'GateMaster')->result_array();
    	  $data["tw_data"] = $array_dataTW;
    	  $data["lw_data"] = $array_dataLW;
	  }else if($UserType == "17"){
	  
	      $this->db->select('Gate_in_ID');
    	  $this->db->where('tblLayerMaster.UserID', $UserID);
    	  // add data condition on TransDate
    	  $this->db->group_by('tblLayerMaster.Gate_in_ID');
		  $array_unloading = $this->db->get(db_prefix().'LayerMaster')->result_array();
		  $gatinList = array();
			foreach($array_unloading as $key=>$val){
				array_push($gatinList,$val["Gate_in_ID"]);
			}
				$this->db->select('COUNT(tblGateMaster.id) as UCount,tblGateMaster.TType,tblGateMaster.TType2');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $gatinList);
        	  $this->db->group_by('tblGateMaster.TType');
        	  $this->db->group_by('tblGateMaster.TType2');
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_unloadingdata = $this->db->get(db_prefix().'GateMaster')->result_array();
	      $data["unloadingdata"] = $array_unloadingdata;
		  
	  }else if($UserType == "18"){
	      $Type = array("P","U");
	      $this->db->select('Gate_in_ID,TType');
    	  $this->db->where('tblQCParameterValues.UserID', $UserID);
    	  $this->db->where_in('tblQCParameterValues.TType', $Type);
    	  // add data condition on TransDate
    	  $this->db->group_by('tblQCParameterValues.TType,tblQCParameterValues.Gate_in_ID');
    	  $AllGateInList = $this->db->get(db_prefix().'QCParameterValues')->result_array();
	      $periparalGatein = array();
	      $unloadingGatein = array();
	      foreach($AllGateInList as $Key=>$val){
	          if($val["TType"] == "P"){
	              array_push($periparalGatein,$val["Gate_in_ID"]);
	          }else{
	              array_push($unloadingGatein,$val["Gate_in_ID"]);
	          }
	      }
	      // Get periparal QC data
	      if($periparalGatein){
	          $this->db->select('COUNT(tblGateMaster.id) as PCount,tblGateMaster.TType,tblGateMaster.TType2');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $periparalGatein);
        	  $this->db->group_by('tblGateMaster.TType');
        	  $this->db->group_by('tblGateMaster.TType2');
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_periQc = $this->db->get(db_prefix().'GateMaster')->result_array();
	      }else{
	          $array_periQc = array();
	      }
	      
	      // Get periparal QC data
	      if($unloadingGatein){
	          $this->db->select('COUNT(tblGateMaster.id) as UCount,tblGateMaster.TType,tblGateMaster.TType2');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $unloadingGatein);
        	  $this->db->group_by('tblGateMaster.TType');
        	  $this->db->group_by('tblGateMaster.TType2');
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_unlQc = $this->db->get(db_prefix().'GateMaster')->result_array();
	      }else{
	          $array_unlQc = array();
	      }
	      
	      $data["periparalQc"] = $array_periQc;
    	  $data["unloadingQc"] = $array_unlQc;
	  }else{
	      $data = array();
	  }
	  
	  $response = array("status" => true, "message" => "Processed data","data"=>$data);
	  return $response;
	}
	
	
	
	public function GetProccesOrdersDataAPI($param=FALSE)
	{
	   $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    $data=array(
                        "phonenumber" => $decode['phonenumber'],
					    "login_tokan" => $decode['login_tokan'],
						"UserID" => $decode['UserID'],
						"UserType" => $decode['UserType'],
						"TType" => $decode['TType'],
						"TType2" => $decode['TType2'],
                    );
                    $response=$this->GetProccesOrderData($data);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            }
        }
        echo json_encode($response);    
	}
	
	
	public function GetProccesOrderData($params=FALSE)
    {
	  $UserID = $params['UserID'];
	  $UserType = $params['UserType'];
	  $TType = $params['TType'];
	  $TType2 = $params['TType2'];
	   if($UserType == "16"){
		   $this->db->select('tblGateMaster.VehicleNo,tblGateMaster.Gate_in_ID,tblGateMaster.Phone,tblGateMaster.TareWeight,tblGateMaster.ASNID');
    	  $this->db->where('tblGateMaster.TWUserID', $UserID);
    	  $this->db->where('tblGateMaster.TType', $TType);
            $this->db->where('tblGateMaster.TType2', $TType2);
    	  // add data condition on TransDate
    	  $this->db->order_by('tblGateMaster.id',"ASC");
    	  $array_dataTW = $this->db->get(db_prefix().'GateMaster')->result_array();
		  
          $this->db->select('tblGateMaster.VehicleNo,tblGateMaster.Gate_in_ID,tblGateMaster.Phone,tblGateMaster.LoadedWeight,tblGateMaster.ASNID');
          $this->db->where('tblGateMaster.LWUserID', $UserID);
          $this->db->where('tblGateMaster.TType', $TType);
            $this->db->where('tblGateMaster.TType2', $TType2);
          // add data condition on TransDate
    	  $this->db->order_by('tblGateMaster.id',"ASC");
    	  $array_dataLW = $this->db->get(db_prefix().'GateMaster')->result_array();
    	  $data["tw_data"] = $array_dataTW;
    	  $data["lw_data"] = $array_dataLW;
	    }else if($UserType == "17"){
	  
	      $this->db->select('Gate_in_ID');
    	  $this->db->where('tblLayerMaster.UserID', $UserID);
    	  // add data condition on TransDate
    	  $this->db->group_by('tblLayerMaster.Gate_in_ID');
		  $array_unloading = $this->db->get(db_prefix().'LayerMaster')->result_array();
		  $gatinList = array();
			foreach($array_unloading as $key=>$val){
				array_push($gatinList,$val["Gate_in_ID"]);
			}
			  $this->db->select('tblGateMaster.VehicleNo,tblGateMaster.Gate_in_ID,tblGateMaster.Phone,tblGateMaster.TareWeight,tblGateMaster.ASNID');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $gatinList);
              $this->db->where('tblGateMaster.TType', $TType);
              $this->db->where('tblGateMaster.TType2', $TType2);
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_unloadingdata = $this->db->get(db_prefix().'GateMaster')->result_array();
	            $data["unloadingdata"] = $array_unloadingdata;
		  
	    }
		else if($UserType == "18"){
	      $Type = array("P","U");
	      $this->db->select('Gate_in_ID,TType');
    	  $this->db->where('tblQCParameterValues.UserID', $UserID);
    	  $this->db->where_in('tblQCParameterValues.TType', $Type);
    	  // add data condition on TransDate
    	  $this->db->group_by('tblQCParameterValues.TType,tblQCParameterValues.Gate_in_ID');
    	  $AllGateInList = $this->db->get(db_prefix().'QCParameterValues')->result_array();
	      $periparalGatein = array();
	      $unloadingGatein = array();
	      foreach($AllGateInList as $Key=>$val){
	          if($val["TType"] == "P"){
	              array_push($periparalGatein,$val["Gate_in_ID"]);
	          }else{
	              array_push($unloadingGatein,$val["Gate_in_ID"]);
	          }
	      }
	      // Get periparal QC data
	      if($periparalGatein){
			  $this->db->select('tblGateMaster.VehicleNo,tblGateMaster.Gate_in_ID,tblGateMaster.Phone,tblGateMaster.TareWeight,tblGateMaster.ASNID');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $periparalGatein);
              $this->db->where('tblGateMaster.TType', $TType);
              $this->db->where('tblGateMaster.TType2', $TType2);
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_periQc = $this->db->get(db_prefix().'GateMaster')->result_array();
	      }else{
	          $array_periQc = array();
	      }
	      // Get periparal QC data
	      if($unloadingGatein){
	          $this->db->select('tblGateMaster.VehicleNo,tblGateMaster.Gate_in_ID,tblGateMaster.Phone,tblGateMaster.TareWeight,tblGateMaster.ASNID');
              $this->db->where_in('tblGateMaster.Gate_in_ID', $unloadingGatein);
              $this->db->where('tblGateMaster.TType', $TType);
              $this->db->where('tblGateMaster.TType2', $TType2);
        	  $this->db->order_by('tblGateMaster.id',"ASC");
        	  $array_unlQc = $this->db->get(db_prefix().'GateMaster')->result_array();
	      }else{
	          $array_unlQc = array();
	      }
	      
	      $data["periparalQc"] = $array_periQc;
    	  $data["unloadingQc"] = $array_unlQc;
	  }else{
	      $data = array();
	  }
	  
	  $response = array("status" => true, "message" => "Processed data","data"=>$data);
	  return $response;
	  
	}
	
	public function GetTravelData($param=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    
                    $staff_id = $decode['staff_id'];
                    $date = substr($decode['date'],0,10);
                    
                    $from_date = substr($date,0,10).' 00:00:00';
                    $to_date = substr($date,0,10).' 23:59:59';
                    
                    $this->db->select('tblLocationTracking.latitude, tblLocationTracking.longitude, tblLocationTracking.address, CAST(tblLocationTracking.TransDate as datetime) as TransDate');
                    $this->db->from(db_prefix() . 'LocationTracking');
                    if ($staff_id) {
                        $this->db->where(db_prefix() . 'LocationTracking.staffid', $staff_id);
                    }
                    $this->db->where('TransDate >=', $from_date);
                    $this->db->where('TransDate <=', $to_date);
                    $this->db->order_by('TransDate','ASC');
                    
                    $travel_detail = $this->db->get()->result_array();
                    
                    $j = 1;
                    $dist_cal_new = 0.00;
                    $pi80 = M_PI / 180; 
                    $r = 6372.797; // mean radius of Earth in km
                    foreach ($travel_detail as $TRkey => $TRvalue) {
                        if($j == 1){
                            $lat1 = $TRvalue['latitude'];
                            $lon1 = $TRvalue['longitude'];
                            $lat1 *= $pi80; 
                            $lon1 *= $pi80;
                        }
                        if($j>1){
                            $lat2 = $TRvalue['latitude'];
                            $lon2 = $TRvalue['longitude'];
                            $lat2 *= $pi80; 
                            $lon2 *= $pi80;
                            if($lat1 == $lat2 && $lon1 == $lon2){
                                                
                            }else{
                                $dlat = $lat2 - $lat1; 
                                $dlon = $lon2 - $lon1; 
                                $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                                $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                                $km = $r * $c; 
                                                
                                $lat1 = $lat2;
                                $lon1 = $lon2;
                                $dist_cal_new =  $dist_cal_new  + $km;
                            }              
                        }
                        $j++;
                    }
                    $data['total_distance'] = number_format($dist_cal_new,2);
                    $data['lat_lon_list'] = $travel_detail;
                    $response = array("status" => true, "message" => "", "data" => $data);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            }
        } else {
            $response = array("error" => false,"message" => "Invalid Request.");
        }
        echo json_encode($response);
    }

    public function GetTimesheetReport($param=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
                if($checkLoginTokan){
                    
                    $staff_id = $decode['staff_id'];
                    
                    $from_date = substr($decode['from_date'],0,10);//.' 00:00:00';
                    $to_date = substr($decode['to_date'],0,10);//.' 23:59:59';
                    
                    $this->db->select('tblcheck_in_out.*,tblstaff.firstname,tblstaff.lastname');
                    $this->db->from(db_prefix() . 'check_in_out');
                    $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'check_in_out.staff_id ');
                    $this->db->where('date >=', $from_date);
                    $this->db->where('date <=', $to_date);
                    $this->db->where('staff.staffid', $staff_id);
                    $this->db->where(db_prefix() . 'check_in_out.type_check', '1');
                    $this->db->order_by('date','DESC');
                    $Check_in_data = $this->db->get()->result_array();
                    
     
                    $this->db->select('tblcheck_in_out.*,tblstaff.firstname,tblstaff.lastname');
                    $this->db->from(db_prefix() . 'check_in_out');
                    $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'check_in_out.staff_id ');
                    $this->db->where('date >=', $from_date);
                    $this->db->where('date <=', $to_date);
                    $this->db->where('staff.staffid', $staff_id);
                    $this->db->where(db_prefix() . 'check_in_out.type_check', '2');
                    $this->db->order_by('date','DESC');
                    $Check_out_data = $this->db->get()->result_array();
                    
                    $travelData = array();
                    foreach($Check_in_data as $value){
                        $in_date = substr($value['date'],0,10);
                        $OutTime = '';
                        foreach($Check_out_data as $value1){
                            if($in_date == substr($value1['date'],0,10) && $value['staff_id'] == $value1['staff_id']){
                                $OutTime = $value1['date'];
                            }
                        }
                        $travel_detail = $this->get_travel_detail_by_staff_id($value['staff_id'],$value['date']);
                        $j = 1;
                        $dist_cal_new = 0.00;
                        $pi80 = M_PI / 180; 
                        $r = 6372.797; // mean radius of Earth in km
                        foreach ($travel_detail as $TRkey => $TRvalue) {
                              # code...
                            if($j == 1){
                                $lat1 = $TRvalue['latitude'];
                                $lon1 = $TRvalue['longitude'];
                                $lat1 *= $pi80; 
                                $lon1 *= $pi80;
                            }
                            if($j>1){
                                $lat2 = $TRvalue['latitude'];
                                $lon2 = $TRvalue['longitude'];
                                $lat2 *= $pi80; 
                                $lon2 *= $pi80;
                                if($lat1 == $lat2 && $lon1 == $lon2){
                                                    
                                }else{
                                    $dlat = $lat2 - $lat1; 
                                    $dlon = $lon2 - $lon1; 
                                    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                                    $km = $r * $c; 
                                                    
                                    $lat1 = $lat2;
                                    $lon1 = $lon2;
                                    $dist_cal_new =  $dist_cal_new  + $km;
                                }              
                            }
                            $j++;
                        }
                        
                        $outTimeStr = '';
                        if($OutTime) {
                            $outTime = substr($OutTime,11,18);
                        }
                        array_push(
                            $travelData, 
                            array (
                                'date' => substr($value['date'],0,10),
                                'in_time' => substr($value['date'],11,18),
                                'out_time' => $outTimeStr,
                                'total_distance' => number_format($dist_cal_new, 2)
                            )
                        );
                    }
                    
                    $response = array("status" => true, "message" => "", "data"=> $travelData);
                }else{
                    $response = array("status" => false, "message" => "Please login with a registered mobile number");
                }
            }
        } else {
            $response = array("error" => false,"message" => "Invalid Request.");
        }
        echo json_encode($response);
    }
    
    public function get_travel_detail_by_staff_id($staff_id,$date)
    {
        $from_date = substr($date,0,10).' 00:00:00';
        $to_date = substr($date,0,10).' 23:59:59';
        
        $this->db->select('tblLocationTracking.*');
        $this->db->from(db_prefix() . 'LocationTracking');
        if ($staff_id) {
            $this->db->where(db_prefix() . 'LocationTracking.staffid', $staff_id);
        }
        $this->db->where('TransDate >=', $from_date);
        $this->db->where('TransDate <=', $to_date);
        $this->db->order_by('TransDate','ASC');
        return $this->db->get()->result_array();
    }
    
    public function AddFarmData($param = FALSE) 
	{
		$response = array();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$content=trim(file_get_contents("php://input"));
            $decode=json_decode($content,true);
			$checkLoginTokan = $this->CheckTokanStaff($decode['login_tokan'], $decode['phonenumber']);
			if ($checkLoginTokan) {
				$farm_data = array(
        			"SurveyID" => $decode['survey_id'],
        			"latitude" => $decode['latitude'],
        			"longitude" => $decode['longitude'],
        			"TransDate	" => date('Y-m-d H:i:s'),
        		);
        		$this->db->insert(db_prefix().'landDetails', $farm_data);
        		$insert_id = $this->db->insert_id();
        		if ($insert_id) {
        			$response = array("status" => true, "message" => "Data added successfully");
        		} else {
        			$response = array("status" => false, "message" => "Something Went Wrong");
        		}
			} else {
				$response = array("status" => false, "message" => "Please login with a registered mobile number");
			}
        }
        echo json_encode($response);
    }
    
    
//============================ Card List and Card details ======================
    public function CardListDetailsAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->CardListDetails($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CardListDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            // Card Request Details 
            $this->db->select('tblCardRequest.TransDate,tblCardMaster.CardName,tblCardRequest.PaymentStatus,tblCardRequest.status');
            $this->db->join('tblCardMaster', 'tblCardMaster.Prefix = tblCardRequest.Prefix');
            $this->db->where('tblCardRequest.AccountID',$params['phonenumber']);
            $CardRequestDetails = $this->db->get(db_prefix().'CardRequest')->row();
            
            
            // Get Card Number For Ledger
            $this->db->select('tblAccountWiseCardMaster.CardNumber,tblCardMaster.CardName,tblCardMaster.Prefix,
            tblAccountWiseCardMaster.IssueDate,tblAccountWiseCardMaster.ExpiryDate,tblAccountWiseCardMaster.Status');
            $this->db->join('tblCardMaster', 'tblCardMaster.Prefix = tblAccountWiseCardMaster.Prefix');
            $this->db->where('tblAccountWiseCardMaster.AccountID',$params['phonenumber']);
            $CardDetails = $this->db->get(db_prefix().'AccountWiseCardMaster')->row();
            $CardNumber = $CardDetails->CardNumber;
            // Get Reward Point Closing Balance
            $this->db->select('SUM(tblCardPointsledger.Amount) AS Points,tblCardPointsledger.TType');
            $this->db->where('tblCardPointsledger.AccountID',$CardNumber);
            $this->db->group_by('tblCardPointsledger.TType');
            $Cardledger = $this->db->get(db_prefix().'CardPointsledger')->result_array();
            $cr = 0;
            $dr = 0;
            foreach($Cardledger as $key=>$val){
                if($val["TType"] == "C"){
                    $cr += $val["Points"];
                }elseif($val["TType"] == "D"){
                    $dr += $val["Points"];
                }
            }
            $PointBal = $cr - $dr;
            
            
            // Card List
            $this->db->select('Prefix,tblCardMaster.CardName,Validity,CardFees,RenewalFees,
            WelcomeBonus,PointConversion,InterestRate,RateBenefits,RateBenefitUpto,Redmption,SoilTest,SoilTestDisc,Status');
            $CardList = $this->db->get(db_prefix().'CardMaster')->result_array();
            
            $response = array("status"=>true,"message"=>"Card List","CardRequest"=>$CardRequestDetails,"CardDetail"=>$CardDetails,"PointBalance"=>$PointBal,"CardList"=>$CardList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
    //============================ Card Ledger =================================
    public function CardLedgerAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->CardLedger($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function CardLedger($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            // Get Card Number
            $this->db->select('tblAccountWiseCardMaster.CardNumber,tblCardMaster.CardName,
            tblAccountWiseCardMaster.IssueDate,tblAccountWiseCardMaster.ExpiryDate,tblAccountWiseCardMaster.Status');
            $this->db->join('tblCardMaster', 'tblCardMaster.Prefix = tblAccountWiseCardMaster.Prefix');
            $this->db->where('tblAccountWiseCardMaster.AccountID',$params['phonenumber']);
            $CardDetails = $this->db->get(db_prefix().'AccountWiseCardMaster')->row();
            $CardNumber = $CardDetails->CardNumber;
            
            $this->db->select('CardPointsledger.*');
            $this->db->where('tblCardPointsledger.AccountID',$CardNumber);
            $CardLedger = $this->db->get(db_prefix().'CardPointsledger')->result_array();
            $response = array("status"=>true,"message"=>"Card List","CardLedger"=>$CardLedger);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //============================ Save New Card Request =======================
    public function SubmitCardRequestAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Prefix"=>$decode['Prefix']
                );
                $response = $this->SubmitCardRequest($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SubmitCardRequest($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            // Check Existing Pending Request
            $where = '(AccountID="' . $params['phonenumber'] . '" AND Prefix = "'.$params['Prefix'].'")';
            $CheckExistingRequest = $this->CardModel->get_data($tablename = "tblCardRequest", $where);
            if($CheckExistingRequest){
                $response = array("status"=>false,"message"=>"Request already submitted..");
            }else{
                $insert_array = array(
                    "TransDate" =>date("Y-m-d H:i:s"),
                    "AccountID"=>$params['phonenumber'],
                    "Prefix"=>$params['Prefix'],
                );
                if($this->db->insert('tblCardRequest',$insert_array)){
                    $response = array("status"=>true,"message"=>"Request Submitted Successfully");
                }else{
                    $response = array("status"=>false,"message"=>"Something went wrong, Please try again");
                }
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //============================ Save New Soil Test Request =======================
    public function SubmitSoilTestRequestAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Prefix"=>$decode['Prefix']
                );
                $response = $this->SubmitSoilTestRequest($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function SubmitSoilTestRequest($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $AccountID = $params['phonenumber'];
            $where = '(AccountID="' . $AccountID . '")';
            $requestexist = $this->CardModel->get_data($tablename = "tblsoiltestrequest", $where);
            if($requestexist == null || $requestexist == "" || $requestexist["status"] != 0)
            {
                $insert_array = array(
                    "TransDate" =>date("Y-m-d H:i:s"),
                    "AccountID"=>$params['phonenumber'],
                    "Prefix"=>$params['Prefix'],
                );
                if($this->db->insert('tblsoiltestrequest',$insert_array)){
                    $response = array("status"=>true,"message"=>"Request Submitted Successfully");
                }else{
                    $response = array("status"=>false,"message"=>"Something went wrong, Please try again");
                }
            }else
            {
                $response = array("status"=>false,"message"=>"Request already submitted");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetSoilTestRequestAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],                   
                );
                $response = $this->GetSoilTestRequest($data);
            }
        }
        echo json_encode($response);    
    }

    public function GetSoilTestRequest($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
           $AccountID = $params['phonenumber'];
           $where = '(AccountID="' . $AccountID . '")';
           $Accountwise_Details = $this->CardModel->get_all_data($tablename = "tblsoiltestrequest", $where);        
           if($Accountwise_Details)
           {
                $filteredData = [];            
                foreach ($Accountwise_Details as $detail) {
                    $filteredData[] = [
                        'TransDate' => $detail['TransDate'],
                        'status' => $detail['status']
                    ];
                }
                $response = array("status"=>true,"message"=>"Request List","data"=>$filteredData);
           }else{
               $response = array("status"=>false,"message"=>"Request List Not Found","data"=>"");
           }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }      
        return $response;    
    }
    
//============================ Get Village List By Pincode =====================
    public function VillageListByPincodeAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Pincode"=>$decode['Pincode']
                );
                $response = $this->VillageListByPincode($data);
            }
        }
        echo json_encode($response);    
    }

    public function VillageListByPincode($params=FALSE)
    {
        $this->db->select('tblvillagedetails.Pincode,VillageName,id');  
        $this->db->where('tblvillagedetails.Pincode',$params["Pincode"]);  
        $VillageList = $this->db->get(db_prefix().'villagedetails')->result_array();
        $response = array("status"=>true,"message"=>"Village List","VillageList"=>$VillageList); 
        return $response; 
    }
    
//============================ Save New Village Details =======================
    public function VillageMastersAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->VillageMasters($data);
            }
        }
        echo json_encode($response);    
    }

    public function VillageMasters($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get crops details            
            $this->db->select('tblcrops.*');          
            $Crops = $this->db->get(db_prefix().'crops')->result_array();

            //Get brand details
            $this->db->select('tblbrands.*');          
            $Brands = $this->db->get(db_prefix().'brands')->result_array();

            //Get Fertilizers details
            $this->db->select('tblfertilizers.*');          
            $Fertilizers = $this->db->get(db_prefix().'fertilizers')->result_array();

            //Get Seed details
            $this->db->select('tblseed.*');          
            $Seeds = $this->db->get(db_prefix().'seed')->result_array();

            //Get Pesticide details
            $this->db->select('tblpesticides.*');          
            $Pesticides = $this->db->get(db_prefix().'pesticides')->result_array();

            $response = array("status"=>true,"message"=>"Msters List","CropsDetail"=>$Crops,"BrandDetail"=>$Brands,"FertilizerDetail"=>$Fertilizers,
                              "SeedDetail"=>$Seeds,"PesticideDetail"=>$Pesticides); 
               
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
   
    public function PincodeDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->PincodeDetails($data);
            }
        }
        echo json_encode($response);    
    }

    public function PincodeDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get pincode details            
            $this->db->select('tblpin.*');          
            $Pincode = $this->db->get(db_prefix().'pin')->result_array();
            // Village List
            $this->db->select('tblvillagedetails.*');          
            $VillageList = $this->db->get(db_prefix().'villagedetails')->result_array();
            foreach($Pincode as &$val)
            {
                $villageArray = array();
                foreach($VillageList as $Vkey=>$vVal){
                    if($val["Pincode"] == $vVal["Pincode"]){
                        $new = array(
                            "id"=>$vVal["id"],
                            "VillageName"=>$vVal["VillageName"]
                        );
                        array_push($villageArray,$new);
                    }
                }
                
                $this->db->where('id', $val['Taluka']);               
                $talukaDetails = $this->db->get(db_prefix().'TalukaMaster')->row(); 
                $val['talukaname'] = $talukaDetails->TalukaName;  
                
                $this->db->where('id', $val['District']);               
                $Districtdetail = $this->db->get(db_prefix().'xx_citylist')->row(); 
                $val['districtname'] = $Districtdetail->city_name; 

                $this->db->where('short_name', $val['State']);               
                $StateDetail = $this->db->get(db_prefix().'xx_statelist')->row(); 
                $val['statename'] = $StateDetail->state_name; 
                $val['VillageList'] = $villageArray;  
            }
            $response = array("status"=>true,"message"=>"Pincode List","PincodeDetail"=>$Pincode);          
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }  

    public function CheckVillageDetailsExistAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Pincode"=>$decode['Pincode'],
                    "VillageName"=>$decode['VillageName']
                );
                $response = $this->CheckVillageDetailsExist($data);
            }
        }
        echo json_encode($response);    
    }

    public function CheckVillageDetailsExist($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $Pincode = $params['Pincode'];
            $VillageName = $params['VillageName'];
           
            if(!empty($Pincode) && !empty($VillageName))
            {
                $this->db->where('Pincode', $Pincode);
                $this->db->where('VillageName', $VillageName);
                $pincodeData = $this->db->get(db_prefix().'villagedetails')->row();               
                if($pincodeData){
                    $response = array("status"=>true,"message"=>"Data found","VillageDetailId"=>$pincodeData->id,"pincode_data"=>$pincodeData);
                }else{
                    $response = array("status"=>false,"message"=>"No data found");
                }
            } 
            else
            {
                $response = array("status"=>false,"message"=>"PincodeId && Village Name is required");
            }          
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//=================== Get FG Item List =========================================
    public function GetFGItemListAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode = json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
    			$response = $this->GetFGItemList($data);
            }
        }
        echo json_encode($response);   
    }
    
    public function GetFGItemList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $this->db->select('tblitems.ItemID,ItemName');
            $this->db->join('tblitems_sub_groups','tblitems_sub_groups.id = tblitems.subgroup_id');
            $this->db->where('tblitems_sub_groups.main_group_id',3);
            $this->db->where('tblitems.isactive',"Y");
            $ItemList = $this->db->get(db_prefix().'items')->result_array();
            $response = array("status"=>true,"message"=>"Item List","data"=>$ItemList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response;
    }
//=================== Get Item Wise Qc Parameter List ==========================
    public function GetItemWiseQCParameterAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode = json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID'],
                );
    			$response = $this->GetItemWiseQCParameter($data);
            }
        }
        echo json_encode($response);   
    }
    
    public function GetItemWiseQCParameter($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $ItemID = $params['ItemID'];
            $this->db->select('tblItemQCParameter.*,tblItemParameter.ItemParameterName');
            $this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblItemQCParameter.ItemParameterID');
            $this->db->where('tblItemQCParameter.ItemID',$ItemID);
            $ParaList = $this->db->get(db_prefix().'ItemQCParameter')->result_array();
            $response = array("status"=>true,"message"=>"Item Wise QC Paramter List","data"=>$ParaList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response;
    }
    
//=================== Get Calculation Details ==================================
    public function GetCalculationAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else{
                $content=trim(file_get_contents("php://input"));
                $decode = json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID'],
                    "Weight"=>$decode['Weight'],
                    "Rate"=>$decode['Rate'],
                    "Parameter"=>$decode['Parameter'],
                );
    			$response = $this->GetCalculation($data);
            }
        }
        echo json_encode($response);   
    }
    
    public function GetCalculation($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $ItemID = $params['ItemID'];
            $Weight = $params['Weight'];
            $Rate = $params['Rate'];
            $Parameter = $params['Parameter'];
            $purch_amt = $Weight * $Rate;
            $GetQcMinMax = $this->GetQcMinMax($ItemID);
            $response = array(
                "FinalRate"=>0,
                "TotalAmt"=>$purch_amt,
                "TotalDeduction"=>0,
                "NetTotal"=>0,
                "DeductionDetails"=>array()
            );
            $TotalDeduction = 0;
            foreach($Parameter as $Key =>$QcVal){
                $ItemParameterID = substr($Key,4);
                $ParameterName = $this->GetParaName($ItemParameterID);
                $parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($ItemID , $ItemParameterID);
	                $parameterValueToCheck = $QcVal;
	                // min value
    		        $minVal = floor($QcVal);
    		        // Max Value
    		        $maxVal = ceil($QcVal);
	            $BaseValue = 2;
		        foreach($GetQcMinMax as $k=>$v){
		            if($v["ItemParameterID"] == $ItemParameterID){
		                $BaseValue = $v["BaseValue"];
		            }
		        }
		        if($ItemParameterID == "2"){
		            //Calculate by amount
		            if($parameterValueToCheck <= $BaseValue){
		                $deductionAmt = 0;
		            }else{
		                $deductionAmt = 0;
		                $minPer = 0;
    		            $maxPer = 0;
    		            foreach($parameterDeductionMatrix as $innerValue)
    		            {
    		                if($minVal == $innerValue['Value']){
        		                $minPer = $innerValue['Deduction'];
        		            }elseif($maxVal == $innerValue['Value']){
        		                $maxPer = $innerValue['Deduction'];
        		            }
    		            }
    		            $diff = $parameterValueToCheck - $minVal;
		                $point_deductionAmtPer_qtls = 12 * $diff;
        		        $deductionAmt = $Weight * $minPer;
        		        $deductionAmt2 = $Weight * $point_deductionAmtPer_qtls;
        		        $deductionAmt += $deductionAmt2;
		            }
		        }else{
		            //Calculate by percent
    		        $minPer = 0;
    		        $maxPer = 0;
    		        foreach($parameterDeductionMatrix as $innerValue){
    		            if($minVal == $innerValue['Value']){
    		                $minPer = $innerValue['Deduction'];
    		            }elseif($maxVal == $innerValue['Value']){
    		                $maxPer = $innerValue['Deduction'];
    		            }
    		        }
    		        $diff = $maxPer - $minPer;
    		        if($parameterValueToCheck <= $BaseValue){
    		            $valDeff = 0;
    		            $deductionAmt = 0;
    		        }else{
    		            $valDeff = $parameterValueToCheck - $minVal;
    		            $finalPer = $minPer + ($valDeff * $diff);
    		            $deductionAmt = $purch_amt * ($finalPer / 100);
    		        }
		        }
		        $TotalDeduction += $deductionAmt;
                $data = array(
                    "ItemParameterName" =>$ParameterName->ItemParameterName,
    				'ItemParameterID' => $ItemParameterID,
    				'HParameterValue' => $QcVal,
    				'deductionAmt'=>number_format($deductionAmt, 2, '.', '')
                );
                array_push($response["DeductionDetails"],$data);
            }
            $response["TotalDeduction"] = $TotalDeduction;
            $NetAmt = $purch_amt - $TotalDeduction;
            $FinalRate = $NetAmt /$Weight; 
            $response["FinalRate"] = $FinalRate;
            $response["NetTotal"] = $NetAmt;
                    
            /*$this->db->select('tblItemQCParameter.*,tblItemParameter.ItemParameterName');
            $this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblItemQCParameter.ItemParameterID');
            $this->db->where('tblItemQCParameter.ItemID',$ItemID);
            $ParaList = $this->db->get(db_prefix().'ItemQCParameter')->result_array();*/
            $response = array("status"=>true,"message"=>"Item Wise QC Paramter List","data"=>$response);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response;
    }
    
    public function GetQcMinMax($ItemID)
	{
	    $this->db->select('tblItemQCParameter.*');
		$this->db->where('tblItemQCParameter.ItemID',$ItemID);
		$data = $this->db->get('tblItemQCParameter')->result_array();
		return $data;
	}
	public function GetParaName($ItemParameterID)
	{
	    $this->db->select('tblItemParameter.*');
		$this->db->where('tblItemParameter.ItemParameterID',$ItemParameterID);
		$data = $this->db->get('tblItemParameter')->row();
		return $data;
	}
    public function AddVillageDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type = $_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VisitDate"=>$decode['VisitDate'],
                    "VillageName"=>$decode['VillageName'],
                    "Pincode"=>$decode['Pincode'],
                    "TalukaId"=>$decode['TalukaId'],
                    "DistrictId"=>$decode['DistrictId'],
                    "StateId"=>$decode['StateId'],
                    "VillageSarpanch"=>$decode['VillageSarpanch'],
                    "VillagePopulation"=>$decode['VillagePopulation'],
                    "Area"=>$decode['Area'],
                    "InfluencerName"=>$decode['InfluencerName'],
                    "InfluencerGovtPost"=>$decode['InfluencerGovtPost'],
                    "Influencer_MobNo"=>$decode['Influencer_MobNo'],
                    "NoRtrsFarmers"=>$decode['NoRtrsFarmers'],
                    "OtherInformation"=>$decode['OtherInformation'],
                    "datecreated"=>date("Y-m-d H:i:s"),
                );
                if($decode['SarpanchMobile']){
                    $data["SarpanchMobile"] = $decode['SarpanchMobile'];
                }else{
                    $data["SarpanchMobile"] = NULL;
                }
                $response = $this->AddVillageDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function AddVillageDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $Pincode = $params['Pincode'];
            $VillageName = $params['VillageName'];

            if(!empty($Pincode) && !empty($VillageName))
            {
                $this->db->where('Pincode', $Pincode);
                $this->db->where('VillageName', $VillageName);
                $pincodeData = $this->db->get(db_prefix().'villagedetails')->row();
                if(empty($pincodeData))
                {
                    if(!empty($params['VisitDate']) && !empty($params['TalukaId']) && !empty($params['DistrictId']) && !empty($params['StateId']) && !empty($params['VillageSarpanch']))
                    {
                        $insert_village_details = array(
                            "VisitDate" =>$params['VisitDate'],
                            "VillageName"=>$params['VillageName'],
                            "Pincode"=>$params['Pincode'],
                            "TalukaId"=>$params['TalukaId'],
                            "DistrictId"=>$params['DistrictId'],
                            "StateId"=>$params['StateId'],
                            "VillageSarpanch"=>$params['VillageSarpanch'],
                            "SarpanchMobile"=>$params['SarpanchMobile'],
                            "VillagePopulation"=>$params['VillagePopulation'],
                            "Area"=>$params['Area'],
                            "InfluencerName"=>$params['InfluencerName'],
                            "InfluencerGovtPost"=>$params['InfluencerGovtPost'],
                            "Influencer_MobNo"=>$params['Influencer_MobNo'],
                            "NoRtrsFarmers"=>$params['NoRtrsFarmers'],
                            "OtherInformation"=>$params['OtherInformation'],
                            "AssignStaff"=>$checkLoginTokan["AccountID"],
                            "UserID"=>$checkLoginTokan["AccountID"],
                            "datecreated"=>date("Y-m-d H:i:s"),
                        );                              
                        if($this->db->insert('tblvillagedetails',$insert_village_details))
                        {      
                            $insert_id = $this->db->insert_id();                                       
                            $response = array("status"=>true,"message"=>"Village Details Inserted Successfully","VillageDetailId"=>$insert_id,"Details"=>$insert_village_details);
                        }else{
                            $response = array("status"=>false,"message"=>"Something went wrong, Please try again");
                        }
                    }    
                    else
                    {
                        $response = array("status"=>false,"message"=>"Please fill the required fields.");
                    }   
                }  
                else
                {
                    if(!empty($params['VisitDate']) && !empty($params['TalukaId']) && !empty($params['DistrictId']) && !empty($params['StateId']) && !empty($params['VillageSarpanch']))
                    {
                        $update_village_details = array(
                            "VisitDate" =>$params['VisitDate'],
                            "VillageName"=>$params['VillageName'],
                            "Pincode"=>$params['Pincode'],
                            "TalukaId"=>$params['TalukaId'],
                            "DistrictId"=>$params['DistrictId'],
                            "StateId"=>$params['StateId'],
                            "VillageSarpanch"=>$params['VillageSarpanch'],
                            "SarpanchMobile"=>$params['SarpanchMobile'],
                            "VillagePopulation"=>$params['VillagePopulation'],
                            "Area"=>$params['Area'],
                            "InfluencerName"=>$params['InfluencerName'],
                            "InfluencerGovtPost"=>$params['InfluencerGovtPost'],
                            "Influencer_MobNo"=>$params['Influencer_MobNo'],
                            "NoRtrsFarmers"=>$params['NoRtrsFarmers'],
                            "OtherInformation"=>$params['OtherInformation'],                                
                            "UserID2"=>$params['UserID'],
                            "dateupdatedat"=>date("Y-m-d H:i:s")
                        );    

                        $this->db->where('Pincode', $Pincode);
                        $this->db->where('VillageName', $VillageName);
                        if($this->db->update(db_prefix().'villagedetails', $update_village_details)){
                            $villageDetailId = $pincodeData->id;    
                            $response = array("status" => true, "message" => "Village Details Updated Successfully","VillageDetailId"=>$villageDetailId,"data"=>$update_village_details);
                        }else{
                            $response = array("status" => true, "message" => "No change","data"=>$update_village_details);
                        }                  
                    }
                    else
                    {
                        $response = array("status"=>false,"message"=>"Please fill the required fields.");
                    }  
                }   
            }
            else
            {
                $response = array("status"=>false,"message"=>"PincodeId && Village Name is required");
            }               
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }   
//========================= Get Staff Wise Village List ========================
    public function VillageLIstStaffWiseAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type = $_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->VillageLIstStaffWise($data);
            }
        }
        echo json_encode($response);  
    }

    public function VillageLIstStaffWise($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $this->db->select('tblvillagedetails.*,tblxx_statelist.state_name,tblxx_citylist.city_name,tblTalukaMaster.TalukaName');
            $this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = '.db_prefix() . 'villagedetails.StateId',"Left");
            $this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = '.db_prefix() . 'villagedetails.DistrictId',"Left");
            $this->db->join(db_prefix() . 'TalukaMaster', db_prefix() . 'TalukaMaster.id = '.db_prefix() . 'villagedetails.TalukaId',"Left");
            $this->db->where('tblvillagedetails.UserID', $checkLoginTokan["AccountID"]);
            $VillageList = $this->db->get(db_prefix().'villagedetails')->result_array();
            $response = array("status"=>true,"message"=>"Village List","VillageList"=>$VillageList);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }   
    
    //Aggregator Details
    public function CheckAggregatorDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],                    
                );
                $response = $this->CheckAggregatorDetails($data);
            }
        }
        echo json_encode($response);    
    }

    public function CheckAggregatorDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {           
            $VillageDetailId = $params['VillageDetailId'];     
            
            if(!empty($VillageDetailId))
            {
                $this->db->where('id', $VillageDetailId);               
                $details = $this->db->get(db_prefix().'villagedetails')->row();   
                if($details)
                {                    
                    $this->db->where('VillageDetailId', $VillageDetailId);                                
                    $isexistAggregator = $this->db->get(db_prefix().'villageaggregatordetails')->result_array();
                        
                    if($isexistAggregator){
                        $response = array("status"=>true,"message"=>"Data found","data"=>$isexistAggregator);
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }                     
                }
                else{
                    $response = array("status"=>false,"message"=>"No data found");
                }  
            }
            else
            {
                $response = array("status"=>false,"message"=>"Detail Id is required");
            }              
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function AddAggregatorDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],   
                    "AggregatorDetails"=>$decode['AggregatorDetails'],    
                    "UserID"=>$decode['UserID'],                   
                    "datecreated"=>date("Y-m-d H:i:s")
                );               
                $response = $this->AddAggregatorDetails($data);
            }
        }
        echo json_encode($response);    
    }

    public function AddAggregatorDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $AggregatorDetails = json_encode($params['AggregatorDetails']);
            $VillageDetailId = $params['VillageDetailId'];

            if (!empty($AggregatorDetails) && $AggregatorDetails !== null) 
            {
                if(!empty($VillageDetailId))
                {                      
                    $this->db->where('id', $VillageDetailId);               
                    $details = $this->db->get(db_prefix().'villagedetails')->row();                              
                    if($details)
                    {                    
                        $aggregatorDetails = json_decode($AggregatorDetails, true);                       
                   
                        foreach($aggregatorDetails as $val)
                        {       
                            $this->db->where('VillageDetailId', $VillageDetailId);                              
                            $this->db->where('AggregatorMobNo', $val['AggregatorMobNo']);               
                            $isexistAggregator = $this->db->get(db_prefix().'villageaggregatordetails')->row();

                            if(empty($isexistAggregator))
                            {
                                $insert_aggregatorDetails = array(
                                    "VillageDetailId" =>$VillageDetailId,   
                                    "VillageAggregatorName"=>$val['VillageAggregatorName'],
                                    "AggregatorMobNo"=>$val['AggregatorMobNo'],
                                    "isnew"=>1,
                                    "UserID"=>$checkLoginTokan['AccountID'],
                                    "datecreated"=>date("Y-m-d H:i:s"),
                                    "isActive"=>1
                                );
    
                                if($this->db->insert('tblvillageaggregatordetails',$insert_aggregatorDetails))
                                {      
                                    $insert_id = $this->db->insert_id();     
                                    $aggregator_details_info = array(
                                        "AggregatorId" => $insert_id,
                                        "AggregatorDetails" => array(
                                            "VillageAggregatorName" => $val['VillageAggregatorName'],
                                            "AggregatorMobNo" => $val['AggregatorMobNo']
                                        )
                                    );                                  
                                    $response[] = array("status"=>true,"message"=>"Aggregator Details Inserted Successfully","data"=>$aggregator_details_info);
                                }else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }      
                            }
                            else
                            {
                                $update_details = array(
                                    "VillageAggregatorName"=>$val['VillageAggregatorName'],
                                    "AggregatorMobNo"=>$val['AggregatorMobNo'],
                                    "UserID2"=>$checkLoginTokan['AccountID'],
                                    "dateupdatedat"=>date("Y-m-d H:i:s")
                                );

                                $this->db->where('VillageDetailId', $VillageDetailId);                                 
                                $this->db->where('AggregatorMobNo', $val['AggregatorMobNo']);                                  
                                if($this->db->update(db_prefix().'villageaggregatordetails', $update_details)){
                                    $aggregator_details_info = array(                                       
                                        "AggregatorDetails" => array(
                                            "VillageAggregatorName" => $val['VillageAggregatorName'],
                                            "AggregatorMobNo" => $val['AggregatorMobNo']
                                        )
                                    ); 
                                    $response[] = array("status" => true, "message" => "Aggregator Details Updated Successfully","data"=>$aggregator_details_info);
                                }  
                                else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }        
                            }                                                  
                        }                     
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }    
                }
                else
                {
                    $response = array("status"=>false,"message"=>"Detail Id is required");
                }                        
            }
            else
            {
                $response = array("status"=>false,"message"=>"Please fill the required fields.");
            }   
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Ksk Details
    public function CheckKskDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],                    
                );
                $response = $this->CheckKskDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function CheckKskDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {           
            $VillageDetailId = $params['VillageDetailId'];     
            
            if(!empty($VillageDetailId))
            {
                $this->db->where('id', $VillageDetailId);               
                $details = $this->db->get(db_prefix().'villagedetails')->row();   
                if($details)
                {                    
                    $this->db->where('VillageDetailId', $VillageDetailId);                                
                    $isexistKskDetail = $this->db->get(db_prefix().'villagekskdetails')->result_array();
                        
                    if($isexistKskDetail){
                        $response = array("status"=>true,"message"=>"Data found","data"=>$isexistKskDetail);
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }                     
                }
                else{
                    $response = array("status"=>false,"message"=>"No data found");
                }  
            }
            else
            {
                $response = array("status"=>false,"message"=>"Detail Id is required");
            }              
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }

    public function AddKskDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],
                    "KskDetails"=>$decode['KskDetails'],    
                    "UserID"=>$decode['UserID'],                   
                    "datecreated"=>date("Y-m-d H:i:s"),
                    "UserID2"=>$decode['UserID'],
                    "dateupdatedat"=>date("Y-m-d H:i:s")             
                );               
                $response = $this->AddKskDetails($data);
            }
        }
        echo json_encode($response);   
    }
    
    public function AddKskDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $KskDetails = json_encode($params['KskDetails']);
            $VillageDetailId = $params['VillageDetailId'];

            if (!empty($KskDetails) && $KskDetails !== null) 
            {
                if(!empty($VillageDetailId))
                {
                    $this->db->where('id', $VillageDetailId);               
                    $details = $this->db->get(db_prefix().'villagedetails')->row(); 
                    if($details)
                    { 
                        $kskdetailinfo = json_decode($KskDetails, true);   
                        foreach($kskdetailinfo as $val)
                        {
                            $this->db->where('VillageDetailId', $VillageDetailId);                              
                            $this->db->where('KskShopOwnerNo', $val['KskShopOwnerNo']);               
                            $isexistksk = $this->db->get(db_prefix().'villagekskdetails')->row();

                            if(empty($isexistksk))
                            {
                                $insert_kskDetails = array(
                                    "VillageDetailId" =>$VillageDetailId,   
                                    "KskName"=>$val['KskName'],
                                    "KskShopOwnerName"=>$val['KskShopOwnerName'],
                                    "KskShopOwnerNo"=>$val['KskShopOwnerNo'],
                                    "isActive"=>1,
                                    "UserID"=>$checkLoginTokan['AccountID'],
                                    "datecreated"=>date("Y-m-d H:i:s")                           
                                );
    
                                if($this->db->insert('tblvillagekskdetails',$insert_kskDetails))
                                {      
                                    $insert_id = $this->db->insert_id();     
                                    $ksk_details_info = array(
                                        "KskId" => $insert_id,
                                        "KskDetails" => array(
                                            "KskName" => $val['KskName'],
                                            "KskShopOwnerName" => $val['KskShopOwnerName'],
                                            "KskShopOwnerNo"=>$val['KskShopOwnerNo']
                                        )
                                    );                                  
                                    $response[] = array("status"=>true,"message"=>"KSk Details Inserted Successfully","data"=>$ksk_details_info);
                                }else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }   
                            }
                            else
                            {
                                $update_ksk = array(
                                    "KskName"=>$val['KskName'],
                                    "KskShopOwnerName"=>$val['KskShopOwnerName'],
                                    "KskShopOwnerNo"=>$val['KskShopOwnerNo'],
                                    "UserID2"=>$checkLoginTokan['AccountID'],
                                    "dateupdatedat"=>date("Y-m-d H:i:s")         
                                );

                                $this->db->where('VillageDetailId', $VillageDetailId);                              
                                $this->db->where('KskShopOwnerNo', $val['KskShopOwnerNo']);                                 
                                if($this->db->update(db_prefix().'villagekskdetails', $update_ksk)){
                                    $ksk_details_info = array(                                       
                                        "KskDetails" => array(
                                            "KskName" => $val['KskName'],
                                            "KskShopOwnerName" => $val['KskShopOwnerName'],
                                            "KskShopOwnerNo" => $val['KskShopOwnerNo']
                                        )
                                    ); 
                                    $response[] = array("status" => true, "message" => "Ksk Details Updated Successfully","data"=>$ksk_details_info);
                                }  
                                else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }        
                            }                          
                        }
                    }
                    else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }  
                }
                else
                {
                    $response = array("status"=>false,"message"=>"Detail Id is required");
                }   
            }
            else
            {
                $response = array("status"=>false,"message"=>"Please fill the required fields.");
            }   
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//====================== Check Existing Hotel details ==========================
    public function CheckHotelDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],                    
                );
                $response = $this->CheckHotelDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function CheckHotelDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {           
            $VillageDetailId = $params['VillageDetailId'];   
            if(!empty($VillageDetailId))
            {
                $this->db->where('id', $VillageDetailId);               
                $details = $this->db->get(db_prefix().'villagedetails')->row();   
                if($details)
                {                    
                    $this->db->where('VillageDetailId', $VillageDetailId);                                
                    $isexistHotelDetail = $this->db->get(db_prefix().'VillageHotelDetails')->result_array();
                    if($isexistHotelDetail){
                        $response = array("status"=>true,"message"=>"Hotel details List","data"=>$isexistHotelDetail);
                    }else{
                        $response = array("status"=>false,"message"=>"Hotel Data Not Found");
                    }                     
                }else{
                    $response = array("status"=>false,"message"=>"Village Data Not Found");
                }  
            }else
            {
                $response = array("status"=>false,"message"=>"Village ID is Required");
            }              
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//================== Add Hotel Details =========================================
    public function AddHotelDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],
                    "HotelDetails"=>$decode['HotelDetails'],    
                    "UserID"=>$decode['UserID']        
                );               
                $response = $this->AddHotelDetails($data);
            }
        }
        echo json_encode($response);   
    }
    
    public function AddHotelDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $HotelDetails = json_encode($params['HotelDetails']);
            $VillageDetailId = $params['VillageDetailId'];
            $Hoteldetailinfo = json_decode($HotelDetails, true); 
            $this->db->where('id', $VillageDetailId);               
            $Villagedetails = $this->db->get(db_prefix().'villagedetails')->row(); 
            if(empty($Hoteldetailinfo) || $Hoteldetailinfo == NULL){
                $response = array("status"=>false,"message"=>"Please fill the required fields.");
            }else if(empty($VillageDetailId) || $VillageDetailId == "" || $VillageDetailId == NULL){
                $response = array("status"=>false,"message"=>"Village ID is Required");
            }else if(empty($Villagedetails) || $Villagedetails == "" || $Villagedetails == NULL){
                $response = array("status"=>false,"message"=>"Village Data Not Found");
            }else{
                $Affected_row = 0;
                $Inserted_row = 0;
                foreach($Hoteldetailinfo as $val)
                {
                    // Check Existing Hotel
                    if($val["id"]){
                        $updateArray = array(
                            "HotelName"=>$val['HotelName'],
                            "OwnerName"=>$val['OwnerName'],
                            "OwnerMobileNo"=>$val['OwnerMobileNo'],
                            "UserID2"=>$checkLoginTokan['AccountID'],
                            "Lupdate"=>date("Y-m-d H:i:s")
                        );
                        $this->db->where('VillageDetailId', $VillageDetailId);                              
                        $this->db->where('id', $val["id"]);                                 
                        if($this->db->update(db_prefix().'VillageHotelDetails', $updateArray)){
                            $Affected_row++;
                        }
                    }else{
                        $insertArray = array(
                            "VillageDetailId"=>$VillageDetailId,
                            "HotelName"=>$val['HotelName'],
                            "OwnerName"=>$val['OwnerName'],
                            "OwnerMobileNo"=>$val['OwnerMobileNo'],
                            "IsActive"=>"Y",
                            "UserID"=>$checkLoginTokan['AccountID'],
                            "TransDate"=>date("Y-m-d H:i:s")
                        );
                        if($this->db->insert('VillageHotelDetails',$insertArray))
                        { 
                            $Inserted_row++;
                        }
                    }
                }
                if($Inserted_row > 0 || $Affected_row > 0){
                    $response = array("status"=>true,"message"=>"Hotel Details Updated");
                }else{
                    $response = array("status"=>false,"message"=>"Hotel Details Not Updated Please Change Some data");
                }
            }   
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    //crop details
    public function CheckCropDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],                    
                );
                $response = $this->CheckCropDetails($data);
            }
        }
        echo json_encode($response); 
    }

    public function CheckCropDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        { 
            $VillageDetailId = $params['VillageDetailId'];     
            if(!empty($VillageDetailId))
            {
                $this->db->where('id', $VillageDetailId);               
                $details = $this->db->get(db_prefix().'villagedetails')->row(); 
                if($details)
                {
                    $this->db->where('VillageDetailId', $VillageDetailId);                                
                    $isexistCropDetail = $this->db->get(db_prefix().'villagecropdetails')->result_array();
                    foreach($isexistCropDetail as &$val)
                    {
                        $this->db->where_in('id', $val['CropId']);
                        $crops = $this->db->get(db_prefix().'crops')->row();       
                        $val['crop_name'] = $crops->CropName;

                        $fertilizerIds = explode(',', $val['FertilizerId']);

                        $this->db->where_in('id', $fertilizerIds);
                        $fertilizers = $this->db->get(db_prefix().'fertilizers')->result();                        
                        $fertilizerNames = [];
                        foreach ($fertilizers as $fername) {
                            $fertilizerNames[] = $fername->fertilizerName;
                        }                        
                        $val['fertilizer_name'] = implode(', ', $fertilizerNames);

                        $pesticideIds = explode(',', $val['PesticideId']);                       
                        $this->db->where_in('id', $pesticideIds);
                        $pesticides = $this->db->get(db_prefix().'pesticides')->result();                       
                        $pesticideNames = [];
                        foreach ($pesticides as $pesticide) {
                            $pesticideNames[] = $pesticide->PesticideName;
                        }                        
                        $val['pesticide_name'] = implode(', ', $pesticideNames);

                        $seedIds = explode(',', $val['SeedId']);                        
                        $this->db->where_in('id', $seedIds);
                        $seeds = $this->db->get(db_prefix().'seed')->result();                       
                        $seedNames = [];
                        foreach ($seeds as $seed) {
                            $seedNames[] = $seed->SeedName;
                        }                       
                        $val['seed_name'] = implode(', ', $seedNames);
                    }
                    if($isexistCropDetail){
                        $response = array("status"=>true,"message"=>"Data found","data"=>$isexistCropDetail);
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }        
                }  
                else{
                    $response = array("status"=>false,"message"=>"No data found");
                }  
            }
            else
            {
                $response = array("status"=>false,"message"=>"Detail Id is required");
            }    
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }

    public function AddCropDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],
                    "CropDetails"=>$decode['CropDetails'],
                    "UserID"=>$decode['UserID'],                   
                    "datecreated"=>date("Y-m-d H:i:s")                                         
                );               
                $response = $this->AddCropDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function AddCropDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $CropDetails = json_encode($params['CropDetails']);
            $VillageDetailId = $params['VillageDetailId'];

            if (!empty($CropDetails) && $CropDetails !== null) 
            {
                if(!empty($VillageDetailId))
                {
                    $this->db->where('id', $VillageDetailId);               
                    $details = $this->db->get(db_prefix().'villagedetails')->row(); 
                    if($details)
                    {
                        $cropdetailsinfo = json_decode($CropDetails, true); 
                        foreach($cropdetailsinfo as $val)
                        {
                            $this->db->where('VillageDetailId', $VillageDetailId);                              
                            $this->db->where('CropId', $val['CropId']);               
                            $isexistCropinfo = $this->db->get(db_prefix().'villagecropdetails')->row();

                            if(empty($isexistCropinfo))
                            {
                                $insert_cropdetails = array(
                                    "VillageDetailId" =>$VillageDetailId,   
                                    "CropId"=>$val['CropId'],
                                    "FertilizerId"=>$val['FertilizerId'],
                                    "SeedId"=>$val['SeedId'],
                                    "PesticideId"=>$val['PesticideId'],
                                    "isActive"=>0,                               
                                    "UserID"=>$checkLoginTokan['AccountID'],
                                    "datecreated"=>date("Y-m-d H:i:s")    
                                );
                            
                                if($this->db->insert('tblvillagecropdetails',$insert_cropdetails))
                                {      
                                    $insert_id = $this->db->insert_id();     
                                    $crop_details_info = array(
                                        "CropdetailId" => $insert_id,
                                        "CropDetails" => array(
                                            "CropId"=>$val['CropId'],
                                            "FertilizerId"=>$val['FertilizerId'],
                                            "SeedId"=>$val['SeedId'],
                                            "PesticideId"=>$val['PesticideId']                                      
                                        )
                                    );                                  
                                    $response[] = array("status"=>true,"message"=>"Crop Details Inserted Successfully","data"=>$crop_details_info);
                                }else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }  
                            } 
                            else
                            {
                                $update_details = array(
                                    "CropId"=>$val['CropId'],
                                    "FertilizerId"=>$val['FertilizerId'],
                                    "SeedId"=>$val['SeedId'],
                                    "PesticideId"=>$val['PesticideId'],
                                    "UserID2"=>$checkLoginTokan['AccountID'],
                                    "dateupdatedat"=>date("Y-m-d H:i:s")     
                                );

                                $this->db->where('VillageDetailId', $VillageDetailId);                              
                                $this->db->where('CropId', $val['CropId']);                                 
                                if($this->db->update(db_prefix().'villagecropdetails', $update_details)){
                                    $crop_details_info = array(                                       
                                        "CropDetails" => array(
                                            "CropId"=>$val['CropId'],
                                            "FertilizerId"=>$val['FertilizerId'],
                                            "SeedId"=>$val['SeedId'],
                                            "PesticideId"=>$val['PesticideId']                                      
                                        )
                                    ); 
                                    $response[] = array("status" => true, "message" => "Crop Details Updated Successfully","data"=>$crop_details_info);
                                }  
                                else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }     
                            }
                        }
                    }
                    else{
                        $response = array("status"=>false,"message"=>"No data found");
                    } 
                }
                else
                {
                    $response = array("status"=>false,"message"=>"Detail Id is required");
                } 
            }
            else
            {
                $response = array("status"=>false,"message"=>"Please fill the required fields.");
            }  
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Vehicle Details
    public function CheckVehicleDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],                    
                );
                $response = $this->CheckVehicleDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function CheckVehicleDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        { 
            $VillageDetailId = $params['VillageDetailId'];     
            if(!empty($VillageDetailId))
            {
                $this->db->where('id', $VillageDetailId);               
                $details = $this->db->get(db_prefix().'villagedetails')->row(); 
                if($details)
                {
                    $this->db->where('VillageDetailId', $VillageDetailId);                                
                    $isexistVehicleDetail = $this->db->get(db_prefix().'villagevehicledetails')->result_array();
                        
                    if($isexistVehicleDetail){
                        $response = array("status"=>true,"message"=>"Data found","data"=>$isexistVehicleDetail);
                    }else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }        
                }  
                else{
                    $response = array("status"=>false,"message"=>"No data found");
                }  
            }
            else
            {
                $response = array("status"=>false,"message"=>"Detail Id is required");
            }    
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }

    public function AddVehicleDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "VillageDetailId"=>$decode['VillageDetailId'],
                    "VehicleDetails"=>$decode['VehicleDetails'],
                    "UserID"=>$decode['UserID'],                   
                    "datecreated"=>date("Y-m-d H:i:s"),                      
                );               
                $response = $this->AddVehicleDetails($data);
            }
        }
        echo json_encode($response);  
    }

    public function AddVehicleDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $VehicleDetails = json_encode($params['VehicleDetails']);
            $VillageDetailId = $params['VillageDetailId'];

            if (!empty($VehicleDetails) && $VehicleDetails !== null) 
            {
                if(!empty($VillageDetailId))
                {
                    $this->db->where('id', $VillageDetailId);               
                    $details = $this->db->get(db_prefix().'villagedetails')->row(); 
                    if($details)
                    {
                        $VehicleDetailsinfo = json_decode($VehicleDetails, true);   
                        foreach($VehicleDetailsinfo as $val)
                        {
                            $this->db->where('VillageDetailId', $VillageDetailId);                              
                            $this->db->where('RegsiterNo', $val['RegsiterNo']);               
                            $isexistvehicleinfo = $this->db->get(db_prefix().'villagevehicledetails')->row();

                            if(empty($isexistvehicleinfo))
                            {
                                $insert_vehicleDetails = array(
                                    "VillageDetailId" =>$VillageDetailId,   
                                    "VehicleType"=>$val['VehicleType'],
                                    "RegsiterNo"=>$val['RegsiterNo'],
                                    "capacity"=>$val['capacity'],
                                    "DriverName"=>$val['DriverName'],
                                    "MobileNo"=>$val['MobileNo'],
                                    "OwnerName"=>$val['OwnerName'],
                                    "OwnerMobNo"=>$val['OwnerMobNo'],
                                    "isActive"=>0,
                                    "UserID"=>$checkLoginTokan['AccountID'],
                                    "datecreated"=>date("Y-m-d H:i:s")                             
                                );

                                if($this->db->insert('tblvillagevehicledetails',$insert_vehicleDetails))
                                {      
                                    $insert_id = $this->db->insert_id();     
                                    $vehicle_details_info = array(
                                        "vehicleId" => $insert_id,
                                        "VehicleDetails" => array(
                                            "VehicleType"=>$val['VehicleType'],
                                            "RegsiterNo"=>$val['RegsiterNo'],
                                            "capacity"=>$val['capacity'],
                                            "DriverName"=>$val['DriverName'],
                                            "MobileNo"=>$val['MobileNo'],
                                            "OwnerName"=>$val['OwnerName'],
                                            "OwnerMobNo"=>$val['OwnerMobNo']
                                        )
                                    );                                  
                                    $response[] = array("status"=>true,"message"=>"Vehicle Details Inserted Successfully","data"=>$vehicle_details_info);
                                }else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }   
                            }
                            else
                            {
                                $update_vehicleDetails = array(
                                    "VehicleType"=>$val['VehicleType'],
                                    "RegsiterNo"=>$val['RegsiterNo'],
                                    "capacity"=>$val['capacity'],
                                    "DriverName"=>$val['DriverName'],
                                    "MobileNo"=>$val['MobileNo'],
                                    "OwnerName"=>$val['OwnerName'],
                                    "OwnerMobNo"=>$val['OwnerMobNo'],
                                    "UserID2"=>$checkLoginTokan['AccountID'],
                                    "dateupdatedat"=>date("Y-m-d H:i:s")    
                                );

                                $this->db->where('VillageDetailId', $VillageDetailId);                              
                                $this->db->where('RegsiterNo', $val['RegsiterNo']);                                 
                                if($this->db->update(db_prefix().'villagevehicledetails', $update_vehicleDetails)){
                                    $vehicle_details_info = array(                                       
                                        "VehicleDetails" => array(
                                            "VehicleType" => $val['VehicleType'],
                                            "RegsiterNo" => $val['RegsiterNo'],
                                            "capacity" => $val['capacity'],
                                            "DriverName"=>$val['DriverName'],
                                            "MobileNo"=>$val['MobileNo'],
                                            "OwnerName"=>$val['OwnerName'],
                                            "OwnerMobNo"=>$val['OwnerMobNo']                                            
                                        )
                                    ); 
                                    $response[] = array("status" => true, "message" => "Vehicle Details Updated Successfully","data"=>$vehicle_details_info);
                                }  
                                else{
                                    $response[] = array("status"=>false,"message"=>"Something went wrong, Please try again");
                                }     
                            }
                        }
                    }
                    else{
                        $response = array("status"=>false,"message"=>"No data found");
                    }  
                }
                else
                {
                    $response = array("status"=>false,"message"=>"Detail Id is required");
                } 
            }
            else
            {
                $response = array("status"=>false,"message"=>"Please fill the required fields.");
            }  
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function VehicleTypeAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->VehicleType($data);
            }
        }
        echo json_encode($response);   
    }

    public function VehicleType($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {           
            $this->db->select('tblvehicletype.*');          
            $Vehicletype = $this->db->get(db_prefix().'vehicletype')->result_array();
            if($Vehicletype)
            {
                $response = array("status"=>true,"message"=>"Vehicle Type List","Vehicletype"=>$Vehicletype); 
            }
            else
            {
                $response = array("status"=>false,"message"=>"No Data Found");
            }            
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    //Farmer Product Details
    public function ItemDetailsAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']                                    
                );
                $response = $this->ItemDetails($data);
            }
        }
        echo json_encode($response); 
    }

    public function ItemDetails($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get product details            
            $this->db->select('tblproduct.*');          
            $Items = $this->db->get(db_prefix().'product')->result_array();   
            if(!empty($Items))
            {
                foreach($Items as &$val)
                {                                         
                    $this->db->where('id', $val['Subcategory']);               
                    $subcategory = $this->db->get(db_prefix().'subcategory')->row();                 
                    $val['subcatname'] = $subcategory->SubcategoryName;   
                    
                    $this->db->where('id', $val['BrandId']);               
                    $brand = $this->db->get(db_prefix().'brands')->row();                
                    $val['brandname'] = $brand->BrandName;   
                }      
                $response = array("status"=>true,"message"=>"Product Details","Items"=>$Items);  
            }
            else
            {
                $response = array("status"=>false,"message"=>"No Data Found.");
            }          
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }

    public function ItemCategoryBrandDetailAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']                                    
                );
                $response = $this->ItemCategoryBrandDetail($data);
            }
        }
        echo json_encode($response); 
    }

    public function ItemCategoryBrandDetail($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokanStaff($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get category details            
            $this->db->select('tblsubcategory.*');          
            $subcategories = $this->db->get(db_prefix().'subcategory')->result_array();   

            //get Brand details
            $this->db->select('tblbrands.*');          
            $brands = $this->db->get(db_prefix().'brands')->result_array();   
         
            $response = array("status"=>true,"message"=>"Details Retrived","Categories"=>$subcategories,"brands"=>$brands);                 
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//========================= Kirti One Item Category List =======================
    public function KirtiOneItemCategoryAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']                                    
                );
                $response = $this->KirtiOneItemCategory($data);
            }
        }
        echo json_encode($response); 
    }

    public function KirtiOneItemCategory($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get category details            
            $this->db->select('tblsubcategory.*');          
            $CategoryList = $this->db->get(db_prefix().'subcategory')->result_array();  
            $response = array("status"=>true,"message"=>"Item Category List","CategoryList"=>$CategoryList);                 
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========================= Kirti One Item List ================================
    public function KirtiOneItemListAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "CategoryID"=>$decode['CategoryID'] 
                );
                $response = $this->KirtiOneItemList($data);
            }
        }
        echo json_encode($response); 
    }

    public function KirtiOneItemList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get category details            
            $this->db->select('tblproduct.*,tblbrands.BrandName');   
			$this->db->join('tblbrands','tblbrands.id = tblproduct.BrandId','LEFT');
            if($params['CategoryID']){
                $this->db->WHERE('tblproduct.Subcategory',$params['CategoryID']);    
            }
            $ItemList = $this->db->get(db_prefix().'product')->result_array();  
            $response = array("status"=>true,"message"=>"Item List","ItemList"=>$ItemList);                 
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//========================= Add / Edit Item In Cart ============================
    public function AddEditItemInCartAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID'],
                    "Qty"=>$decode['Qty'] 
                );
                $response = $this->AddEditItemInCart($data);
            }
        }
        echo json_encode($response); 
    }

    public function AddEditItemInCart($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $data_array = array(
                "AccountID"=>$params['phonenumber'],
                "ItemID"=>$params['ItemID'],
                "quantity"=>$params['Qty'],
                "UserID"=>$params['phonenumber'],
                "TransDate"=>date('Y-m-d H:i:s'),
            );      
            // Check Item Is Exist or not
            $CheckCartItem = $this->CheckCartItem($params['phonenumber'],$params['ItemID']);
            if($CheckCartItem){
                unset($data_array["AccountID"],$data_array["ItemID"]);
                $this->db->where('tblCartMaster.ItemID',$params['ItemID']);
                $this->db->where('tblCartMaster.AccountID',$params['phonenumber']);
                if($this->db->update('tblCartMaster',$data_array)){
                    $response = array("status"=>true,"message"=>"Item Updated Successfully"); 
                }else{
                    $response = array("status"=>false,"message"=>"Something went wrong please try some time"); 
                }
            }else{
                if($this->db->insert('tblCartMaster',$data_array)){
                    $response = array("status"=>true,"message"=>"Item Added Successfully"); 
                }else{
                    $response = array("status"=>false,"message"=>"Something went wrong please try some time"); 
                }  
            }           
        }
        else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function CheckCartItem($AccountID,$ItemID)
    {         
        $this->db->select('tblCartMaster.*'); 
        $this->db->WHERE('tblCartMaster.AccountID',$AccountID);
        $this->db->WHERE('tblCartMaster.ItemID',$ItemID);
        $CartItemList = $this->db->get(db_prefix().'CartMaster')->row();
        return $CartItemList;
    }
    
//========================= Cart Item List =====================================
    public function CartItemListAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->CartItemList($data);
            }
        }
        echo json_encode($response); 
    }

    public function CartItemList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get category details            
            $this->db->select('tblCartMaster.*,tblproduct.ProductName,tblproduct.rate,tblproduct.Productimg,tbltaxes.taxrate,tblproduct.unit'); 
            $this->db->join('tblproduct','tblproduct.ProductID = tblCartMaster.ItemID');
            $this->db->join('tbltaxes','tbltaxes.id = tblproduct.gst');
            $this->db->WHERE('tblCartMaster.AccountID',$params['phonenumber']);  
            $CartItemList = $this->db->get(db_prefix().'CartMaster')->result_array();  
            $response = array("status"=>true,"message"=>"Cart Item List","CartItemList"=>$CartItemList);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========================= Delete Item From Cart ==============================
    public function RemoveItemFromCartAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "ItemID"=>$decode['ItemID'],
                );
                $response = $this->RemoveItemFromCart($data);
            }
        }
        echo json_encode($response); 
    }

    public function RemoveItemFromCart($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $this->db->where('tblCartMaster.ItemID',$params['ItemID']);
            $this->db->where('tblCartMaster.AccountID',$params['phonenumber']);
            if($this->db->delete('tblCartMaster')){
                $response = array("status"=>true,"message"=>"Item Deleted Successfully"); 
            }else{
                $response = array("status"=>false,"message"=>"Something went wrong please try some time"); 
            }           
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========================= Kirti One Order Place ==============================
    public function KirtiOnePlaceOrderAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "DeliveryType"=>$decode['DeliveryType'],
                    "ShippingID"=>$decode['ShippingID'],
                    "Pincode"=>$decode['Pincode'],
                    "State"=>$decode['State'],
                    "District"=>$decode['District'],
                    "Block"=>$decode['Block'],
                    "Locality"=>$decode['Locality'],
                    "Street"=>$decode['Street'],
                    "House"=>$decode['House'],
                    "remark"=>$decode['remark'],
                    "Items"=>$decode['Items'],
                );
                $response = $this->KirtiOnePlaceOrder($data);
            }
        }
        echo json_encode($response); 
    }

    public function KirtiOnePlaceOrder($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $fy = "25";
            $selected_company = 1;
            $nextK1OrderNumber = get_option2('next_K1Order_number_for_kirti',$fy); 
            $OrderId = "ORD".$fy.$nextK1OrderNumber;
            $Items = $params['Items'];
            // Order Master Table array
            $insert_order = array(    
                'PlantID'=>$selected_company,
                'FY'=>$fy,
                'OrderID'=>$OrderId,  
                'Transdate'=>date('Y-m-d H:i:s'), 
                'AccountID'=>$params['phonenumber'],
                'OrderAmt'=>0,
                'OrderWeight'=>'0.00',
                'OrderStatus'=>"O",           
                'OrderType'=>"TAXITEMS",
                'UserID'=>$params['phonenumber'],
                'order_type'=>"APP", 
                'DeliveryType'=>$params['DeliveryType'], 
                'remark'=>$params["remark"]
            );
            if($this->db->insert('tblK1ordermaster',$insert_order)){
                $ShippingID = NULL;
                if($params['ShippingID']){
                    $ShippingID = $params['ShippingID'];
                }else{
                    $ShippingArray = array(
                        "AccountID"=>$params['phonenumber'],
                        "Pincode"=>$params['Pincode'],
                        "State"=>$params['State'],
                        "District"=>$params['District'],
                        "Block"=>$params['Block'],
                        "Locality"=>$params['Locality'],
                        "Street"=>$params['Street'],
                        "House"=>$params['House'],
                        "UserID"=>$params['phonenumber'],
                        "TransDate"=>date('Y-m-d H:i:s'),
                    );
                    if($this->db->insert('tblShippingDetails',$ShippingArray)){
                        $ShippingID = $this->db->insert_id();
                    }
                }
                
                $this->increment_next_number('next_K1Order_number_for_kirti'); 
                $ordno = 1;
                $TotalNetAmt = 0;
                foreach($Items as $key=>$val){
                    $GetItemDetails = $this->GetItemdetails($val["ItemID"]);
                    $taxrate = $GetItemDetails->taxrate;
                    $gstAmt = ($GetItemDetails->rate * ($GetItemDetails->taxrate / 100));
                    $salerate = $GetItemDetails->rate + $gstAmt;
                    $CGST = $taxrate / 2;
                    $SGST = $taxrate / 2;
                    $IGST = 0;
                    $TaxableAmt = ($GetItemDetails->rate * $val["Qty"]);
                    $CGSTAmt = ($TaxableAmt * ($CGST/100));
                    $SGSTAmt = ($TaxableAmt * ($SGST/100));
                    $IGSTAmt = 0;
                    $caseqty = $val["Qty"] /$GetItemDetails->PackingQty;
                    $NetAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
                    $TotalNetAmt += $NetAmt;
                    $insert_product_detail = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$fy,
                        'OrderID'=>$OrderId,
                        'TransDate'=>date('Y-m-d h:i:s'), 
                        'TransDate2'=>date('Y-m-d h:i:s'),
                        'TType'=>"O",
                        'TType2'=>"ORDER",
                        'AccountID'=>$params['phonenumber'],
                        'ItemID'=>$val["ItemID"],
                        'PurchRate'=>$GetItemDetails->rate,
                        'SaleRate'=>$salerate,
                        'BasicRate'=>$GetItemDetails->rate,
                        'SuppliedIn'=>$GetItemDetails->unit,
                        'OrderQty'=>$val["Qty"],
                        'BilledQty'=>$val["Qty"],
                        'cgst'=>$CGST,
                        'cgstamt'=>$CGSTAmt,
                        'sgst'=>$SGST,
                        'sgstamt'=>$SGSTAmt,
                        'igst'=>$IGST,
                        'igstamt'=>$IGSTAmt,
                        'CaseQty'=>$caseqty,
                        'OrderAmt'=>$TaxableAmt,
                        'NetOrderAmt'=>$NetAmt,
                        'Ordinalno'=>$ordno,
                        'rowid'=>0,
                        'UserID'=>$params['phonenumber']
                    );
                    $this->db->insert('tblK1history',$insert_product_detail);
                    $ordno++;
                }
                $this->db->where('tblK1ordermaster.OrderID',$OrderId);
                $this->db->update('tblK1ordermaster',["OrderAmt"=>$TotalNetAmt,"ShippingID"=>$ShippingID]);
                $response = array("status"=>true,"message"=>"Order Place Successfully","data"=>$insert_product_detail);
            }else{
                $response = array("status"=>false,"message"=>"Something went wrong please try some time"); 
            }      
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function GetItemdetails($ItemID)
    {
        $this->db->select('tblproduct.ProductName,tblproduct.rate,tblproduct.Productimg,tbltaxes.taxrate,tblproduct.unit,tblproduct.PackingQty'); 
        $this->db->join('tbltaxes','tbltaxes.id = tblproduct.gst');
        $this->db->WHERE('tblproduct.ProductID',$ItemID);  
        $Itemdetails = $this->db->get(db_prefix().'product')->row(); 
        return $Itemdetails;
    }
    
//========================= Kirti One Item List List ===========================
    public function KirtiOneOrderListAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->KirtiOneOrderList($data);
            }
        }
        echo json_encode($response); 
    }

    public function KirtiOneOrderList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            //Get category details            
            $this->db->select('tblK1ordermaster.*,tblclients.company'); 
            $this->db->join('tblclients','tblclients.AccountID = tblK1ordermaster.AccountID');
            $this->db->WHERE('tblK1ordermaster.AccountID',$params['phonenumber']);  
            $this->db->order_by('tblK1ordermaster.OrderID',"DESC");  
            $OrderList = $this->db->get(db_prefix().'K1ordermaster')->result_array();  
            $i = 0;
            foreach($OrderList as $key=>$val){
                $this->db->select('tblK1history.*'); 
                $this->db->WHERE('tblK1history.OrderID',$val['OrderID']);  
                $ItemList = $this->db->get(db_prefix().'K1history')->result_array(); 
                $OrderList[$i]["ItemList"] = $ItemList;
                $i++;
            }
            $response = array("status"=>true,"message"=>"Order List","OrderList"=>$OrderList);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
//========================= Account Wise Shipping List =========================
    public function ShippingAddressAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                );
                $response = $this->ShippingAddress($data);
            }
        }
        echo json_encode($response); 
    }

    public function ShippingAddress($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            
            $this->db->select('tblShippingDetails.*'); 
            $this->db->WHERE('tblShippingDetails.AccountID',$params['phonenumber']);  
            $this->db->order_by('tblShippingDetails.id',"ASC");  
            $ShippingList = $this->db->get(db_prefix().'ShippingDetails')->result_array();  
            
            $response = array("status"=>true,"message"=>"Account Shipping List","ShippingList"=>$ShippingList);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//========================= Kirti One Item List List ===========================
    public function AccountLedgerAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "form_date"=>$decode['form_date'],
                    "to_date"=>$decode['to_date']
                );
                $response = $this->AccountLedger($data);
            }
        }
        echo json_encode($response); 
    }

    public function AccountLedger($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $form_date = $params['form_date'].' 00:00:00';
            $to_date = $params['to_date'].' 23:59:59';
            $to_date_new = date('Y-m-d', strtotime('-1 day', strtotime($form_date)))." 23:59:59";
            if ( date('m') <= 3 ) {
                $FY = date('y') - 1;
            }else {
                $FY = date('y');
            }
            $FirstDateFY = "20".$FY."-04-01 00:00:00";
            $OpnBal = 0;
            //Get category details            
            $this->db->select('tblaccountledger.VoucherID ,tblaccountledger.Transdate,tblaccountledger.TType,tblaccountledger.Amount,
            tblaccountledger.Narration,PassedFrom,tblclients.company AS CounterAccountName,tblCenterMaster.CenterName,tblitems_sub_groups.name AS CommodityName'); 
            $this->db->join('tblclients','tblclients.AccountID = tblaccountledger.CounterAccount',"LEFT");
            $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblaccountledger.CenterID',"LEFT");
            $this->db->join('tblitems_sub_groups','tblitems_sub_groups.ShortCode = tblaccountledger.CommodityID',"LEFT");
            $this->db->WHERE('tblaccountledger.AccountID',$params['phonenumber']);  
            $this->db->where("tblaccountledger.Transdate BETWEEN '$form_date' AND '$to_date'");
            $this->db->WHERE('tblaccountledger.FY',$FY); 
            $this->db->order_by('tblaccountledger.Transdate,tblaccountledger.PassedFrom',"asc");  
            $AccountLedger = $this->db->get(db_prefix().'accountledger')->result_array();  
            
            
            // Calculate Opening Balance
            // Get Current Year Opening balance
            $this->db->select('tblaccountbalances.*'); 
            $this->db->WHERE('tblaccountbalances.AccountID',$params['phonenumber']);  
            $this->db->WHERE('tblaccountbalances.FY',$FY);  
            $AccountOpeningBalance = $this->db->get(db_prefix().'accountbalances')->row(); 
            if($AccountOpeningBalance){
                $OpnBal = $AccountOpeningBalance->BAL1;
            }
            
            // Previous day opening bal calculation
            $this->db->select('tblaccountledger.*'); 
            $this->db->WHERE('tblaccountledger.AccountID',$params['phonenumber']);  
            $this->db->where("tblaccountledger.Transdate BETWEEN '$FirstDateFY' AND '$to_date_new'");
            $this->db->WHERE('tblaccountledger.FY',$FY); 
            $this->db->order_by('tblaccountledger.Transdate',"ASC");  
            $PreAccountLedger = $this->db->get(db_prefix().'accountledger')->result_array(); 
            foreach($PreAccountLedger as $key=>$val){
                if($val["TType"] == "C"){
                    $OpnBal -= $val["Amount"];
                }else if($val["TType"] == "D"){
                    $OpnBal += $val["Amount"];
                }
            }
            
            if($OpnBal > 0){
                $Type = "D";
            }else{
                $Type = "C";
            }
            $response = array("status"=>true,"message"=>"Account Ledger","OpeningBalance"=>$OpnBal,"OpnBalType"=>$Type,"AccountLedger"=>$AccountLedger);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    
//========================= Account Balance Details ============================
    public function AccountClosingBalanceAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->AccountClosingBalance($data);
            }
        }
        echo json_encode($response); 
    }

    public function AccountClosingBalance($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            if ( date('m') <= 3 ) {
                $FY = date('y') - 1;
            }else {
                $FY = date('y');
            }
            $AccountBal = 0;
            //Get category details            
            $this->db->select('tblaccountledger.VoucherID ,tblaccountledger.Transdate,tblaccountledger.TType,tblaccountledger.Amount'); 
            $this->db->WHERE('tblaccountledger.AccountID',$params['phonenumber']);  
            $this->db->WHERE('tblaccountledger.FY',$FY); 
            $this->db->order_by('tblaccountledger.Transdate,tblaccountledger.PassedFrom',"asc");  
            $AccountLedger = $this->db->get(db_prefix().'accountledger')->result_array();  
            
            
            // Calculate Opening Balance
            // Get Current Year Opening balance
            $this->db->select('tblaccountbalances.*'); 
            $this->db->WHERE('tblaccountbalances.AccountID',$params['phonenumber']);  
            $this->db->WHERE('tblaccountbalances.FY',$FY);  
            $AccountOpeningBalance = $this->db->get(db_prefix().'accountbalances')->row(); 
            if($AccountOpeningBalance){
                $AccountBal = $AccountOpeningBalance->BAL1;
            }
            foreach($AccountLedger as $key=>$val){
                if($val["TType"] == "C"){
                    $AccountBal -= $val["Amount"];
                }else if($val["TType"] == "D"){
                    $AccountBal += $val["Amount"];
                }
            }
            
            if($AccountBal > 0){
                $Type = "D";
            }else{
                $Type = "C";
            }
            $response = array("status"=>true,"message"=>"Account Ledger Details","LedgerBalance"=>$AccountBal,"OpnBalType"=>$Type,"CreditBal"=>0,"RewardPoints"=>0);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//========================= Payment Request ====================================
    public function PaymentRequestAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan'],
                    "Amount"=>$decode['Amount']
                );
                $response = $this->PaymentRequest($data);
            }
        }
        echo json_encode($response); 
    }

    public function PaymentRequest($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {
            $insert_array = array(
                "TransDate"=>date("Y-m-d H:i:s"),
                "AccountID"=>$params['phonenumber'],
                "Amount"=>$params['Amount'],
                "Status"=>1
            );
            $CheckPendinRequest = $this->CheckPendingPaymentRequest($params['phonenumber']);
            if($CheckPendinRequest){
                $response = array("status"=>false,"message"=>"Request Already Submitted.."); 
            }else{
                if($this->db->insert('tblPaymentRequest',$insert_array)){
                    $response = array("status"=>true,"message"=>"Payment Request Summitted Successfully");                 
                }else{
                    $response = array("status"=>false,"message"=>"Something went wrong, please try again");  
                }   
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//======================= Account Payment Request List =========================
    public function PaymentRequestListAPI($params=FALSE)
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data = array(
                    "phonenumber"=>$decode['phonenumber'],
                    "login_tokan"=>$decode['login_tokan']
                );
                $response = $this->PaymentRequestList($data);
            }
        }
        echo json_encode($response); 
    }

    public function PaymentRequestList($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan)
        {               
            $this->db->select('tblPaymentRequest.*'); 
            $this->db->WHERE('tblPaymentRequest.AccountID',$params['phonenumber']);  
            $this->db->order_by('tblPaymentRequest.id',"DESC");  
            $AccountPaymentRequest = $this->db->get(db_prefix().'PaymentRequest')->result_array();  
            $response = array("status"=>true,"message"=>"Account Payment Request List","PaymentRequest"=>$AccountPaymentRequest);                 
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
    
    public function CheckPendingPaymentRequest($AccountID)
    {
        $status = array('1','2');           
        $this->db->select('tblPaymentRequest.*'); 
        $this->db->WHERE('tblPaymentRequest.AccountID',$AccountID);  
        $this->db->where_in('tblPaymentRequest.Status',$status);  
        $this->db->order_by('tblPaymentRequest.id',"DESC");  
        $AccountPaymentRequest = $this->db->get(db_prefix().'PaymentRequest')->result_array(); 
        return $AccountPaymentRequest; 
    }
    
//========================== Add New Version ===================================
    public function AddVersionAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "VersionNo"=>$decode['VersionNo'],
                    "app_url"=>$decode['app_url']
                );
                if($decode['VersionNo'] == "" || $decode['VersionNo'] == NULL || empty($decode['VersionNo'])){
                    $response = array("status"=>false,"message"=>"Please add Version number");
                }elseif($decode['app_url'] == "" || $decode['app_url'] == NULL || empty($decode['app_url'])){
                    $response = array("status"=>false,"message"=>"Please add App URL");
                }else{
                    $response = $this->AddVersion($data);
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function AddVersion($params=FALSE)
    {
        $VersionNo = $params['VersionNo'];
        $app_url = $params['app_url'];
        $status = 1;
        $appdata=array(
            "VersionNo"=>$VersionNo,
            "app_url"=>$app_url,
            "status"=>$status      
        );  
        $appdata['created_date'] = date('Y-m-d H:i:s');
        
        $this->db->insert('tblStaffapp_version', $appdata);
        $insertid = $this->db->insert_id();
        if ($insertid) {        
            $this->db->where("id !=",$insertid); 
            $this->db->set('status',0);
            $this->db->update('tblStaffapp_version');
            $response=array("status"=>true,"message"=>"You have Create New Version successfully");
            return $response; 
        }else{
            $response=array("status"=>false,"message"=>"something went wrong, Please try again");
        }
    }
    
//========================= Get Letas App Version ==============================
    public function GetAppVersionAPI($param=FALSE) {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            if ($content_type!="application/json") {
                $response = array("error" => true,"message" => "Invalid content type.");  
            }else
            {
                $content=trim(file_get_contents("php://input"));
                $decode=json_decode($content,true);
                $data=array(
                    "mobile"=>$decode['mobile'],
                    "login_token"=>$decode['login_token']
                );
                $response=$this->GetAppVersion($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function GetAppVersion($params=FALSE)
    {
        $this->load->model('UserApp_Model');
        $CheckLoginToken = $this->CheckTokanStaff($params['login_token'],$params['mobile']);
        if($CheckLoginToken)
        {
            $status = 1;
            $response = $this->UserApp_Model->GetAppVersion($status);
        }else{
            $response = array("status"=>false,"message"=>"Login token is invalid please login","user_data"=>null);
        }
        return $response; 
    }
    
}
?>