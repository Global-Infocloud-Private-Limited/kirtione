<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class AppUser extends ClientsController {

    public function __construct() 
    {
        parent::__construct();
        hooks()->do_action('clients_authentication_constructor', $this);
        $this->load->helper(array('form', 'url', 'file'));
        $this->load->library('upload');
        $this->load->model('GateControl_model');
    }
//=================== CHeck Mobile number is Exist or not ======================
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

    public function CheckUserExist($params=FALSE) 
    {
        $mobile_no = $params['mobile_no'];
        $this->db->where('AccountID', $mobile_no);
        $UserDetails = $this->db->get(db_prefix().'clients')->row();
        return $UserDetails;
    }

    public function CheckBankAccountNoExist($params=FALSE)
    {
        $AccountNumber = $params['AccountNumber'];
        $this->db->where('accountNumber', $AccountNumber);
        $AccountNoDetails = $this->db->get(db_prefix().'BankDetails')->row();
        return $AccountNoDetails;
    }  

    public function CheckAadharExist($params=FALSE)
    {
        $AadharNumber = $params['AadharNumber'];
        $this->db->where('aadhaar_number', $AadharNumber);
        $AadharDetails = $this->db->get(db_prefix().'contacts')->row();
        return $AadharDetails;
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
         
           $this->db->where('AccountID', $Accountid);
           $AadharDetails = $this->db->get(db_prefix().'AadharDetails')->row();  
           return $AadharDetails;
        }
        return null;
    }
//====================== Sign IN or Log In API =================================
    public function SignInAPI($param=FALSE) 
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
            $data = array(
                "mobile_no"=>$params['mobile_no'],
                "DeviceID"=>$params['DeviceID']
            );
            $UserDetails = $this->CheckUserExist($data);
			$response = array("status"=>true,"message"=>"Record Inserted Successfully","UserDetails"=>$UserDetails);
		}else{
		    $response = array("status"=>false,"message"=>"Something Went Wrong");
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
//==================== Get Nesxt Master Number =================================
    public function get_next_code($name)
    {
        $this->db->select('tbloptions.*');
        $this->db->where('name', $name);
        $number_details = $this->db->get(db_prefix().'options')->row();
        return $number_details;
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
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
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
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function latlongUpdate($params=FALSE)
    {
        $latitude = $params['reg_latitude'];
        $longitude = $params['reg_longitude'];
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
    
//==================== Check Aadhar Vaidation and Exist ======================================
    public function AadharCheckAndValidateAPI($param=FALSE) 
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
                $response = $this->AadharCheckAndValidate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function AadharCheckAndValidate($params=FALSE)
    {
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            
            // check Aadhaar validation 
           $aadhaarNumber = $params['Aadhar_number'];
            $curl = curl_init();
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
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
            $response_array = json_decode($response);
            if($response_array->success == true){
                $this->db->select('tblcontacts.*');
                $this->db->where('tblcontacts.aadhaar_number',$params['Aadhar_number']);
                $AadharDetails = $this->db->get(db_prefix().'contacts')->result_array();
                if($AadharDetails){
                    $response = array("status"=>false,"message"=>"This Aadhaar number is already registered.");
                }else{
                    $response = array("status"=>true,"message"=>"New Aadhaar","AadhaarDetails"=>$response_array);
                }
            }else{
                $response = array("status"=>false,"message"=>"Aadhaar number is not valid");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
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
                $GetState_short_code = $this->Getstate_short_code($value['state_code']);
                $Gstdata =array(
                    "AccountID"=>$params['phonenumber'],
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
//======================== Get Short Code Against State id =====================

    public function Getstate_short_code($state) 
    {
        $State = strtoupper($state);
        $this->db->select('*');
        $this->db->where('state_name', $State);
        $StateDetails = $this->db->get(db_prefix().'xx_statelist')->row();
        $StateID = $StateDetails->short_name;
        
        $IdDetails = array(
            "StateID" => $StateID
        );
        
        return $IdDetails;
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
    
//============================= Check PAN Validate and check Exist ================================
    public function PANCheckAndValidateAPI($param=FALSE) 
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
                $response = $this->PANCheckAndValidate($data);
            }
        }
        echo json_encode($response);    
    }
    
    public function PANCheckAndValidate($params=FALSE)
    {   
        $checkLoginTokan = $this->CheckTokan($params['login_tokan'],$params['phonenumber']);
        if($checkLoginTokan){
            // PAN Validation 
            $PAN = $params['Pan'];
            $curl = curl_init();
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
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
                    	"id_number":  "' . $PAN . '"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token . ''
                    ),
                )
            );

            $response = curl_exec($curl);
            curl_close($curl);
            $response_array = json_decode($response);
            if($response_array->success == true){
                $this->db->select('tblcontacts.*');
                //$this->db->where('tblcontacts.AccountID',$params['phonenumber']);
                $this->db->where('tblcontacts.Pan',$params['Pan']);
                $PanDetails = $this->db->get(db_prefix().'contacts')->result_array();
                if($PanDetails){
                    $response = array("status"=>false,"message"=>"This Pan number is already registered.");
                }else{
                    $response = array("status"=>true,"message"=>"New Pan","PANDetails"=>$response_array);
                }
            }else{
                $response = array("status"=>false,"message"=>"Please Enter Valid PAN");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
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
                    "WHID"=>$decode['WHID'],
                    "ItemID"=>$decode['ItemID'],
                    "Quantity"=>$decode['Quantity'],
                    "Unit"=>$decode['Unit'],
                    "TransDate"=>$decode['TransDate'],
                    "OtherID"=>$decode['OtherID'],
                    "UserType"=>$decode['UserType'],
                    "CenterID"=>$decode['CenterID']
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
//===================== Get Booking Details Against BookingID ==================
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
            $this->db->select('tbllead_master.BookingID,tbllead_master.CenterID,tbllead_master.e_quantity,tbllead_master.basic_rate AS TradeRate,
            tblitems.ItemName,tblCenterMaster.CenterName,tblCenterMaster.address,tblCenterMaster.longitude,tblCenterMaster.latitude,
            tblclients.company AS PartyName,TBLBROKER.company AS BrokerName');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
            $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');
            $this->db->join('tblclients AS TBLBROKER', 'TBLBROKER.AccountID = tbllead_master.BrokerID','LEFT');
            $this->db->where('tbllead_master.BookingID',$params['BookingID']);
            $BookingDetails = $this->db->get('tbllead_master')->row();
            $BookingDetails->Link = 'https://kirtidev.globalinfocloud.com/uploads/cs/CS-Buy-Trade-Soyabean.pdf';
            if($BookingDetails){
                $this->db->select('BookingID,ASNID,asn_date,Gate_in_ID,gate_in_date,Asn_WT_MT,VehicleNo,Phone,LoadedWeight,TareWeight');
                $this->db->where('tblGateMaster.BookingID',$params['BookingID']);
                $InwardList = $this->db->get('tblGateMaster')->result_array();
                $j = 0;
                foreach($InwardList as $key=>$Val)
                {
                    $this->db->select('TransID,QCID AS LotNumber,WHID,CHID,StackID,LOTID,Weight,BagQty,CenterQCApprove,ROQCApprove,HOQCApprove');
                    $this->db->where('tblstockInventory.BookingID',$Val['BookingID']);
                    $this->db->where('tblstockInventory.GateINID',$Val['Gate_in_ID']);
                    $StackDetails = $this->db->get('tblstockInventory')->result_array();
                    $i = 0;
                    foreach($StackDetails as $Key1=>$Val1)
                    {
                        $this->db->select('tblQCParameterValues.BookingID,tblQCParameterValues.Gate_in_ID,tblItemParameter.ItemParameterName,tblQCParameterValues.ParameterValue,tblQCParameterValues.EParameterValue,tblQCParameterValues.HParameterValue');
                        $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
                        $this->db->where('tblQCParameterValues.BookingID',$Val['BookingID']);
                        $this->db->where('tblQCParameterValues.TType',"F");
                        $this->db->where('tblQCParameterValues.layer_number',$Val1["LotNumber"]);
                        $this->db->where('tblQCParameterValues.Gate_in_ID',$Val['Gate_in_ID']);
                        $QCList = $this->db->get('tblQCParameterValues')->result_array();
                        $StackDetails[$i]["QCDetails"] = $QCList;
                        $i++;
                    }
                    $InwardList[$j]["StackWiseDetails"] = $StackDetails;
                    $j++;
                }
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
                $i = 0;
                foreach ($newsdata as $newsList) {
                    if($newsList["category"] = "1"){
                        $newsList["CategoryName"] = "Agriculture";
                    }else if($newsList["category"] = "2"){
                        $newsList["CategoryName"] = "Weather";
                    }else if($newsList["category"] = "3"){
                        $newsList["CategoryName"] = "Government Schemes";
                    }else if($newsList["category"] = "4"){
                        $newsList["CategoryName"] = "Inventions";
                    }else if($newsList["category"] = "5"){
                        $newsList["CategoryName"] = "Other";
                    }else{
                        $newsList["CategoryName"] = "";
                    }
                    $languages = $newsList['language'];
                    $languageArray = explode(",", $languages);
                    
                    if (in_array($checkLoginTokan->default_language, $languageArray)) {
                        if($newsList['newsphoto'] == "" || $newsList['newsphoto'] == NULL){
                            $newsList['newsphoto'] = NULL;
                        }else{
                            $newsList['newsphoto'] = base_url().'uploads/staff_profile_images/news/'.$newsList['id'].'/'.$newsList['newsphoto'];
                        }
                        $filteredNewsData[] = $newsList;
                    }
                    $i++;
                }
                $newsdata = $filteredNewsData;
            }
            $response = array("status"=>true,"message"=>"News List","newsList"=>$newsdata);
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
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
//======================== Get Booking Details =================================
    public function GetBookingDetails($BookingID) 
    {
        
        $this->db->select('tbllead_master.*,tblCenterMaster.PCCenterID,
        tblclients.company,tblclients.ShortCode,tblclients.fcm_token,tblcontacts.firstname,tblcontacts.lastname,Bdetails.fcm_token AS Bfcm_token');
        $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
        $this->db->join('tblclients AS Bdetails','Bdetails.AccountID = tbllead_master.BrokerID',"");
        $this->db->where('tbllead_master.BookingID', $BookingID);
        return $this->db->get('tbllead_master')->row();
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
            "login_tokan"=>0,
            "fcm_token"=>NULL
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
//================= Get Account Details By Short Code ==========================
    public function getDetails_by_shortcode($ShortCode)
    {
        $this->db->select('tblclients.*');
        $this->db->where('tblclients.ShortCode',$ShortCode);
        $Para_list = $this->db->get('tblclients')->row();
        return $Para_list;
    }
//============ Check Request Exist =============================================
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
            $status = NULL;
            if($checkLoginTokan->CustomerType == "2" && $params['block_status'] == "B"){
                $msg = "Trader Blocked Successfully"; 
                $status = 13;
            }else if($checkLoginTokan->CustomerType == "2" && $params['block_status'] == "U"){
                $msg = "Trader UnBlocked Successfully";  
                $status = 12;
            }else if($checkLoginTokan->CustomerType == "3" && $params['block_status'] == "B"){
                $msg = "Broker Blocked Successfully";  
                $status = 11;
            }else if($checkLoginTokan->CustomerType == "3" && $params['block_status'] == "U"){
                $msg = "Broker UnBlocked Successfully";  
                $status = 10;
            }
            $updateArray = array(
                "block_status"=>$status,
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$params['phonenumber']
            );
            $this->db->where('id',$params['request_id']);
            $this->db->update('tbltrader_broker_assigned',$updateArray);
            if($this->db->affected_rows() > 0){
                $response = array("status"=>true,"message"=>$msg);
            }else{
                $response = array("status"=>false,"message"=>"Status update failed");
            }
        }else{
            $response = array("status"=>false,"message"=>"Please login with registered mobile number");
        }
        return $response; 
    }
//======================== Get Company Name which is Purchase Commodity ========
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
//================= Get Account Details BY AccountID ===========================
    public function GetSingleAccountDetails($AccountID)
    {
        $this->db->select('tblclients.*');
        $this->db->where('AccountID', $AccountID);
        $Account_details = $this->db->get(db_prefix().'clients')->row();
        return $Account_details;
    }

//================== Increment Center Wise Trade Number ========================   
    public function increment_center_wise_booking_number($CenterID,$TType)
    {
        $this->db->set('Number', 'Number+1', false);
        $this->db->WHERE('CenterID', $CenterID);
        $this->db->WHERE('TType', $TType);
        $this->db->update(db_prefix() . 'numberformat');
    }
    
//================== Send Notification to party ================================
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
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan']
                    );
                    $response = $this->GetStateList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
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
                $checkLoginTokan = $this->CheckTokan($decode['login_tokan'],$decode['phonenumber']);
                if($checkLoginTokan){
                    $data = array(
                        "phonenumber"=>$decode['phonenumber'],
                        "login_tokan"=>$decode['login_tokan'],
                        "state_id"=>$decode['state_id']
                    );
                    $response = $this->GetCityList($data);
                }else{
                    $response = array("status"=>false,"message"=>"Please login with registered mobile number");
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function GetCityList($params=FALSE)
    {
        $this->db->where('state', $params['state_id']);
        $this->db->order_by('city',"ASC");
        $CityList = $this->db->get(db_prefix().'_xx_city')->result_array();
        $response = array("status"=>true,"message"=>"City List","City"=>$CityList);
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
    
//========================== Check Login Token =================================
    public function CheckTokan($login_tokan,$AccountID) 
    {
        $this->db->where('AccountID', $AccountID);
        $this->db->where('login_tokan', $login_tokan);
        $UserDetails = $this->db->get(db_prefix().'clients')->row();
        return $UserDetails;
    }
//============== Increment Farmer/Trader/Broker and corporate number ===========
    public function increment_next_number($name)
    {
        // Update next number in settings
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('name', $name);
        $this->db->update(db_prefix() . 'options');
    }

//======================= Get Inward Details Against Vehicl ====================
    public function GetInwardDetailsAgainstVehicle($VehicleNo) 
    {
        $this->db->where('VehicleNo', $VehicleNo);
        $this->db->where('gate_out_by IS NULL');
        $GateDetails = $this->db->get(db_prefix().'GateMaster')->row();
        return $GateDetails;
    }
    
//================= Gross / Tare Weight Update  ================================
    public function AddWeightAPI($param=FALSE) 
    {
        $response = array();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $content_type=$_SERVER['CONTENT_TYPE'];
            $content = trim(file_get_contents("php://input"));
            $decode = json_decode($content,true);
            $TypeOfWeight = array("1", "2");

            if ($content_type!="application/json") {
                $response = array("status" => false,"message" => "Invalid content type.","VehicleNo"=>$decode['VehicleNo']);  
            }elseif($decode['VhlSideImage'] == ""){
                $response = array("status" => false,"message" => "Please Provide Vehicle Side Image.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['VhlTopImage'] == ""){
                $response = array("status" => false,"message" => "Please Provide Vehicle Top Image.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['VhlFrontImage'] == ""){
                $response = array("status" => false,"message" => "Please Provide Vehicle Front Image.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['SlipNo'] == ""){
                $response = array("status" => false,"message" => "Please Provide Slip No.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['VehicleNo'] == ""){
                $response = array("status" => false,"message" => "Please Provide Vehicle Number.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['Weight'] == ""){
                $response = array("status" => true,"message" => "Please Provide Vehicle Weight.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif($decode['WeightType'] == ""){
                $response = array("status" => false,"message" => "Please Provide Vehicle Weight Type.","VehicleNo"=>$decode['VehicleNo']); 
            }elseif(!in_array($decode['WeightType'], $TypeOfWeight)){
                $response = array("status" => false,"message" => "Please Provide Weight Type 1 Or 2","VehicleNo"=>$decode['VehicleNo']); 
            }else{
                
                $InwardDetails = $this->GetInwardDetailsAgainstVehicle($decode['VehicleNo']);
                if(empty($InwardDetails)){
                    $response = array("status"=>false,"message"=>"Vehicle Details Not Found.","VehicleNo"=>$decode['VehicleNo']);
                }else if($InwardDetails->LoadedWeight == NULL && $decode['WeightType'] == "2"){
                    $response = array("status"=>false,"message"=>"Please Send Gross Weight First.","VehicleNo"=>$decode['VehicleNo']);
                }else{
                    $GateINID = $InwardDetails->Gate_in_ID;
                    $BookingID = $InwardDetails->BookingID;
                    // Vehicle Side image
                    if($decode['VhlSideImage'])
                    {
                        $image1 = base64_decode($decode['VhlSideImage']);
                        $image_name = "VhlSideImage";
                        $filename = $image_name . '.' . $decode['VhlSideImage_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/WeightBridgeVhlImages/'.$GateINID)) {
                            mkdir('assets/WeightBridgeVhlImages/'.$GateINID, 0777, true);
                        }
                        $VhlSideImage = "assets/WeightBridgeVhlImages/".$GateINID."/".$filename;
                        file_put_contents($VhlSideImage , $image1);
                    }else{
                        $VhlSideImage = '';  
                    }
                    // Vehicle Top image
                    if($decode['VhlTopImage'])
                    {
                        $image2 = base64_decode($decode['VhlTopImage']);
                        $image_name = "VhlTopImage";
                        $filename = $image_name . '.' . $decode['VhlTopImage_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/WeightBridgeVhlImages/'.$GateINID)) {
                            mkdir('assets/WeightBridgeVhlImages/'.$GateINID, 0777, true);
                        }
                        $VhlTopImage = "assets/WeightBridgeVhlImages/".$GateINID."/".$filename;
                        file_put_contents($VhlTopImage , $image2);
                    }else{
                        $VhlTopImage = '';  
                    }
                    // Vehicle Front image
                    if($decode['VhlFrontImage'])
                    {
                        $image3 = base64_decode($decode['VhlFrontImage']);
                        $image_name = "VhlFrontImage";
                        $filename = $image_name . '.' . $decode['VhlFrontImage_ext'];
                    //rename file name with random number
                        if (!file_exists('assets/WeightBridgeVhlImages/'.$GateINID)) {
                            mkdir('assets/WeightBridgeVhlImages/'.$GateINID, 0777, true);
                        }
                        $VhlFrontImage = "assets/WeightBridgeVhlImages/".$GateINID."/".$filename;
                        file_put_contents($VhlFrontImage , $image3);
                    }else{
                        $VhlFrontImage = '';  
                    }
                    $data = array(
                        "GateINID"=>$decode['GateINID'],
                        "BookingID"=>$decode['BookingID'],
                        "VehicleNo"=>$decode['VehicleNo'],
                        "SlipNo"=>$decode['SlipNo'],
                        "TransportName"=>$decode['TransportName'],
                        "DriverName"=>$decode['DriverName'],
                        "DriverMobile"=>$decode['DriverMobile'],
                        "Weight"=>$decode['Weight'],
                        "WeightType"=>$decode['WeightType'],
                        "VhlTopImage"=>$VhlTopImage,
                        "VhlFrontImage"=>$VhlFrontImage,
                        "VhlSideImage"=>$VhlSideImage,
                    );
                    $response = $this->UpdateWeight($data);
                }
            }
        }
        echo json_encode($response);    
    }
    
    public function UpdateWeight($params=FALSE)
    {
        $BookingID = $params['BookingID'];
        $GateInID = $params['GateINID'];
        $tare_weight = ($params['Weight'] / 1000); // Weight in MT
        $update_array = array(
            "weigh_bridge_slip_no" =>$params['SlipNo'],
            "Phone" =>$params['DriverMobile'],
        );
        if($params['VhlTopImage'] !== ""){
            if($params['WeightType'] == "1"){
                $update_array["VhlTopImage"] = $params['VhlTopImage'];
            }else{
                $update_array["TWVhlTopImage"] = $params['VhlTopImage'];
            }
        }
        
        if($params['VhlFrontImage'] !== ""){
            if($params['WeightType'] == "1"){
                $update_array["VhlFrontImage"] = $params['VhlFrontImage'];
            }else{
                $update_array["TWVhlFrontImage"] = $params['VhlFrontImage'];
            }
        }
        
        if($params['VhlSideImage'] !== ""){
            if($params['WeightType'] == "1"){
                $update_array["VHLSideImage"] = $params['VhlSideImage'];
            }else{
                $update_array["TWVHLSideImage"] = $params['VhlSideImage'];
            }
        }
        if($params['WeightType'] == "1"){
            $update_array["LoadedWeight"] = $params['Weight'] / 100;
            $update_array["LWUserID"] = "Auto";
            $update_array["LWTransDate"] = date("Y-m-d H:i:s");
        }else{
            $update_array["TareWeight"] = $params['Weight'] / 100;
            $update_array["TWUserID"] = "Auto";
            $update_array["TWTransDate"] = date("Y-m-d H:i:s");
        }
        
        $this->db->where('VehicleNo', $params['VehicleNo']);
        $this->db->update(db_prefix().'GateMaster', $update_array);
        
        if($this->db->affected_rows() > 0){
            if($params['WeightType'] == "2"){
                //Update Inventory and vendor ledger
                $leadMasterDetails = $this->GateControl_model->updateTareWeightDetails($BookingID,$GateInID,$tare_weight);
            }
            $response = array("status"=>true,"message"=>"Weight Updated Successfully","VehicleNo"=>$params['VehicleNo']);
        }else{
            $response = array("status"=>false,"message"=>"Something Went Wrong","VehicleNo"=>$params['VehicleNo']);
        }
        return $response; 
    }
    
    
}