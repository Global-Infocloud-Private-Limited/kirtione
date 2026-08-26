<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CardMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CardModel');
        $this->load->model('GateControl_model');
        
    }

    /* add,edit card details */   
    // public function create_card()//not in use
    // {
    //     $all_card_detail = $this->CardModel->get_all_table_data($tablename="tblcardmaster");
    //     $data['all_card_detail'] = $all_card_detail;
        
    //     $all_feature_details = $this->CardModel->get_all_table_data($tablename="tblcardfeaturemaster");
    //     $data['all_feature_details'] = $all_feature_details;  
        
    //     $allcardservices = $this->CardModel->get_all_table_data($tablename="tblcarddetails");
    //     $data['allcardservices'] = $allcardservices;       
    //     $this->load->view('admin/CardMaster/create_card',$data);			
    // }
    
    public function NotificationApi()
    {
         $this->load->view('admin/CardMaster/NotificationApi');
    }
    
    public function AddEditCard()
    {
        if (!has_permission_new('AddEditCard', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['tiltle'] = "Add Edit Card";       
        $this->load->view('admin/CardMaster/AddEditCard',$data);
    }
 
    public function create_features()
    {
        $this->load->view('admin/CardMaster/create_features');	
    }
 
    public function insert_card()
    {			
        $prefix = $this->input->post('prefix');
        $card_name = $this->input->post('card_name');
        $validity = $this->input->post('validity');
        $cardfees = $this->input->post('cardfees');
        $renewalfees = $this->input->post('renewalfees');
        $welcomebonus = $this->input->post('welcomebonus');
        $ptconversion = $this->input->post('ptconversion');
        $interestrate = $this->input->post('interestrate');
        $ratebenefitmin = $this->input->post('ratebenefitmin');
        $ratebenefitmax = $this->input->post('ratebenefitmax');
        $reedemption = $this->input->post('reedemption');
        $soiltest = $this->input->post('soiltest');
        $soiltestdisc = $this->input->post('soiltestdisc');
        $status = $this->input->post('status');	
        if (empty($card_name)) {
            echo json_encode(['success' => false, 'message' => 'Card Name is required']);
            return;
        }		
        $createcard = array(        
            'Prefix'=>$prefix,                   
            'CardName'=>$card_name,
            'Validity'=>$validity,
            'CardFees'=>$cardfees,
            'RenewalFees'=>$renewalfees,
            'WelcomeBonus'=>$welcomebonus,
            'PointConversion'=>$ptconversion,
            'InterestRate'=>$interestrate,
            'RateBenefits'=>$ratebenefitmin,
            'RateBenefitUpto'=>$ratebenefitmax,
            'Redmption'=>$reedemption,
            'SoilTest'=>$soiltest,
            'SoilTestDisc'=>$soiltestdisc,
            'Status'=>$status,   
            'UserID'=>$this->session->userdata('username'),    
            'TransDate'=>date('Y-m-d h:i:s'),
            'UserID2'=>$this->session->userdata('username'),   
            'Lupdate'=>date('Y-m-d h:i:s'),                  
        );								  						   
        $createnewcard =   $this->CardModel->insert_data($tablename="tblCardMaster",$createcard);
        if ($createnewcard) {           
            echo json_encode(['success' => true,'message' => 'Data inserted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }   
 
    // public function update_card_details()//use in create_card page not in use
    // {       
    //     $dataToPost = $this->input->post('dataToPost');         
    //     $prefix = $this->input->post('prefix');
    //     $cardName = $this->input->post('cardName');
    //     $validity = $this->input->post('validity');
    //     $cardfees = $this->input->post('cardfees');
    //     $renewalfees = $this->input->post('renewalfees');
    //     $status = $this->input->post('status');              
    
    //     $update_card = array(
    //                             'CardName'=>$cardName,
    //                             'Validity'=>$validity,
    //                             'CardFees'=>$cardfees,
    //                             'RenewalFees'=>$renewalfees,
    //                             'Status'=>$status,
    //                             'UserID2'=>$this->session->userdata('username'),   
    //                             'Lupdate'=>date('Y-m-d h:i:s'),  
    //                         );
    //     $where = '(Prefix="' . $prefix . '")';
    //     $edit = $this->CardModel->edit_data($tablename="tblcardmaster",$where,$update_card);
    
    //     if ($edit) 
    //     {	 
    //         if(!empty($dataToPost))
    //         {
    //             foreach ($dataToPost as $item) 
    //             {                   
    //                 $featureId = htmlspecialchars($item['id']);                       
    //                 $value =  htmlspecialchars($item['fieldOption']);
    //                 $Status =  htmlspecialchars($item['status']);                   
    //                 $benefittype = htmlspecialchars($item['benefittype']);       

    //                 $insert_carddetials = array(
    //                                             'PrefixID'=>$prefix,
    //                                             'FeatureID'=>$featureId,
    //                                             'BenefitType'=>$benefittype,
    //                                             'Value'=>$value,
    //                                             'Status'=>$Status,
    //                                             'UserID'=>$this->session->userdata('username'), 
    //                                             'TransDate'=>date('Y-m-d h:i:s'),   
    //                                             'UserID2'=>$this->session->userdata('username'), 
    //                                             'Lupdate'=>date('Y-m-d h:i:s'),   
    //                                         );
    //                 $insertfeaturewise_carddetails = $this->CardModel->insert_data($tablename="tblcarddetails",$insert_carddetials);
    //             }
                
    //             $wh = '(PrefixID="' . $prefix . '")';
    //             $getcarddetails = $this->CardModel->get_data($tablename="tblcarddetails",$wh);
    //             if(!empty($getcarddetails))                        
    //             {
    //                 $remove_Data = $this->CardModel->delete_all_data($tablename="tblcarddetails");
    //                 if($remove_Data)
    //                 {
    //                     foreach ($dataToPost as $items) 
    //                     {                            
    //                         $featureId = htmlspecialchars($items['id']);                       
    //                         $value =  htmlspecialchars($items['fieldOption']);
    //                         $Status =  htmlspecialchars($items['status']);      
    //                         $benefittype = htmlspecialchars($items['benefittype']);                              

    //                         $update_carddetails = array(
    //                                                         'PrefixID'=>$prefix,
    //                                                         'FeatureID'=>$featureId,
    //                                                         'BenefitType'=>$benefittype,
    //                                                         'Value'=>$value,
    //                                                         'Status'=>$Status,
    //                                                         'UserID'=>$this->session->userdata('username'), 
    //                                                         'TransDate'=>date('Y-m-d h:i:s'),   
    //                                                         'UserID2'=>$this->session->userdata('username'), 
    //                                                         'Lupdate'=>date('Y-m-d h:i:s'),  
    //                                                     );                        
    //                         $updatefeaturewise_carddetails = $this->CardModel->insert_data($tablename="tblcarddetails",$update_carddetails);
    //                     }
    //                 }                       
    //             }                
    //         }     
    //         else
    //         {
    //             $remove_Data = $this->CardModel->delete_all_data($tablename="tblcarddetails");
    //         }      
    //         echo json_encode(['success' => true]);               
    //     } else {
    //         echo json_encode(['success' => false]);
    //     }
    // }

    public function EditCard_details()//use in AddEditCard page
    {
        $prefix = $this->input->post('prefix');
        $cardName = $this->input->post('cardName');
        $validity = $this->input->post('validity');
        $cardfees = $this->input->post('cardfees');
        $renewalfees = $this->input->post('renewalfees');
        $welcomebonus = $this->input->post('welcomebonus');
        $ptconversion = $this->input->post('ptconversion');
        $interestrate = $this->input->post('interestrate');
        $ratebenefitmin = $this->input->post('ratebenefitmin');
        $ratebenefitmax = $this->input->post('ratebenefitmax');
        $reedemption = $this->input->post('reedemption');
        $soiltest = $this->input->post('soiltest');
        $soiltestdisc = $this->input->post('soiltestdisc');
        $status = $this->input->post('status');  
        
        $update_card = array(
                                'CardName'=>$cardName,
                                'Validity'=>$validity,
                                'CardFees'=>$cardfees,
                                'RenewalFees'=>$renewalfees,
                                'WelcomeBonus'=>$welcomebonus,
                                'PointConversion'=>$ptconversion,
                                'InterestRate'=>$interestrate,
                                'RateBenefits'=>$ratebenefitmin,
                                'RateBenefitUpto'=>$ratebenefitmax,
                                'Redmption'=>$reedemption,
                                'SoilTest'=>$soiltest,
                                'SoilTestDisc'=>$soiltestdisc,
                                'Status'=>$status,
                                'UserID2'=>$this->session->userdata('username'),   
                                'Lupdate'=>date('Y-m-d h:i:s'),  
                            );
        $where = '(Prefix="' . $prefix . '")';
        $edit = $this->CardModel->edit_data($tablename="tblCardMaster",$where,$update_card);
        if ($edit) 
        {
            echo json_encode(['success' => true]);    
        }
        else {
            echo json_encode(['success' => false]);
        }
    }
 
    public function delete_cardservice() 
    {        
        $ids = $this->input->post('ids');     
        $where = '(id = "'.$ids.'")';
        $deleteentry = $this->CardModel->delete_data($tablename="tblCardDetails",$where);        
        if($deleteentry)
        {
            echo json_encode(['success' => true]);
        }              
        else {           
            echo json_encode(['success' => false]);
        }
    }
 
    public function get_card_name()
    {
        $prefix = $this->input->post('prefix');
        $where = array('Prefix' => $prefix); 	
        $cardinfo = $this->CardModel->get_data($tablename="tblCardMaster",$where);
        
        $wh_prefix = array('PrefixID' => $prefix); 
        $cardfeatures = $this->CardModel->get_all_data($tablename="tblCardDetails",$wh_prefix);
        foreach($cardfeatures as &$val)
        {
            $wh_featurmaster = '(id="' . $val['FeatureID'] . '")';
            $featuremaster = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$wh_featurmaster);
            $val['featuremaster'] = $featuremaster;
        }

        $allfeatures = $this->CardModel->get_all_table_data($tablename="tblCardFeatureMaster");
        $response = array(
                                'cardinfo' => $cardinfo,
                                'cardfeatures' => $cardfeatures,
                                'allfeatures'=>$allfeatures,                                
                            );
        header('Content-Type: application/json');
        echo json_encode($response);
    }   
 
    public function get_all_card_details()
    {
        $cardinfo = $this->CardModel->get_all_table_data($tablename="tblCardMaster");	
        header('Content-Type: application/json');
        echo json_encode($cardinfo);
    }
 
    public function card_services()
    {				
        $this->load->view('admin/accounts_master/card_services');			
    }

    public function insert_card_features()
    {
        $featurename = $this->input->post('featurename');
        $featuredesc = $this->input->post('featuredesc');
        $featurebenefit_type = $this->input->post('featurebenefit_type');
        $fieldtypes = $this->input->post('fieldtypes');
        $status = $this->input->post('status');
        $list = $this->input->post('list');

        if (empty($featurename)) {
            echo json_encode(['success' => false, 'message' => 'Feature Name is required']);
            return;
        }	

        $insertfeature = array(
                                    'FeatureName'=>$featurename,
                                    'Description'=>$featuredesc,
                                    'BenefitType'=>$featurebenefit_type,
                                    'FieldType'=>$fieldtypes,
                                    'FieldOption'=>$list,
                                    'Status'=>$status,
                                    'UserID'=>$this->session->userdata('username'),
                                    'TransDate'=>date('Y-m-d h:i:s'),
                                    'UserID2'=>$this->session->userdata('username'),
                                    'Lupdate'=>date('Y-m-d h:i:s'),
                            );
        $newfeature =   $this->CardModel->insert_data($tablename="tblCardFeatureMaster",$insertfeature);
        if ($newfeature) {           
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
 
    public function getcardDetailsbyId()
    {
        $currentcardId = $this->input->post('currentcardId');
        $where = array('Prefix' => $currentcardId); 	
        $cardbyid = $this->CardModel->get_data($tablename="tblCardMaster",$where);	

        $wh_prefix = array('PrefixID' => $currentcardId); 
        $cardfeatures = $this->CardModel->get_all_data($tablename="tblCardDetails",$wh_prefix);
        foreach($cardfeatures as &$val)
        {
            $wh_featurmaster = '(id="' . $val['FeatureID'] . '")';
            $featuremaster = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$wh_featurmaster);
            $val['featuremaster'] = $featuremaster;
        }

        $allfeatures = $this->CardModel->get_all_table_data($tablename="tblCardFeatureMaster");
        $response = array(
                                'cardbyid' => $cardbyid,
                                'cardfeatures' => $cardfeatures,
                                'allfeatures'=>$allfeatures,
                            );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function update_card_features()
    {
        $currentFeatureId = $this->input->post('currentFeatureId');       
        $featurename = $this->input->post('featurename');
        $featuredesc = $this->input->post('featuredesc');
        $featurebenefit_type = $this->input->post('featurebenefit_type');
        $fieldtypes = $this->input->post('fieldtypes');
        $status = $this->input->post('status');
        $list = $this->input->post('list');        

        $updatefeatures = array(
                                        'FeatureName'=>$featurename,
                                        'Description'=>$featuredesc,
                                        'BenefitType'=>$featurebenefit_type,
                                        'FieldType'=>$fieldtypes,
                                        'FieldOption'=>$list,
                                        'Status'=>$status,
                                        'UserID2'=>$this->session->userdata('username'), 
                                        'Lupdate'=>date('Y-m-d h:i:s'),  
                                );
        $where = '(id="' . $currentFeatureId . '")';
        $update =   $this->CardModel->edit_data($tablename="tblCardFeatureMaster",$where,$updatefeatures);
        if ($update) {           
        echo json_encode(['success' => true]);
        } else {
        echo json_encode(['success' => false]);
        }
    }
 
    public function update_cardyfeaturename()
    {
        $featurename = $this->input->post('featurename');
        $featuredesc = $this->input->post('featuredesc');
        $fieldtypes = $this->input->post('fieldtypes');
        $status = $this->input->post('status');
        $list = $this->input->post('list');

        if($fieldtypes==1)
        {
            $list = "";
        }

        $updatefeatures = array(
                                    'FeatureName'=>$featurename,
                                    'Description'=>$featuredesc,
                                    'FieldType'=>$fieldtypes,
                                    'FieldOption'=>$list,
                                    'Status'=>$status,
                                );
        $where = '(FeatureName="' . $featurename . '")';
        $update =   $this->CardModel->edit_data($tablename="tblCardFeatureMaster",$where,$updatefeatures);
        if ($update) {           
        echo json_encode(['success' => true]);
        } else {
        echo json_encode(['success' => false]);
        }
    }
 
    public function get_card_feature_details()
    {
        $featurename = $this->input->post('featurename');
        $where = array('FeatureName' => $featurename); 	
        $featuredetails = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$where);	
        header('Content-Type: application/json');
        echo json_encode($featuredetails);
    }
 
    public function getfeaturedetailsbyid()
    {
        $featureId = $this->input->post('featureId');
        $where = array('id' => $featureId); 	
        $featuredetailsbyid = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$where);	
        header('Content-Type: application/json');
        echo json_encode($featuredetailsbyid);
    }
 
    public function get_allcard_feature_details()
    {       
        $allfeaturedetails = $this->CardModel->get_all_table_data($tablename="tblCardFeatureMaster");	
        header('Content-Type: application/json');
        echo json_encode($allfeaturedetails);
    }

    public function card_report()
    {
        $Statelist = $this->CardModel->get_all_table_data($tablename="tblxx_statelist");	
        $citylist = $this->CardModel->get_all_table_data($tablename="tblxx_citylist");	 
        $data['Statelist'] = $Statelist;
        $data['citylist'] = $citylist;
        $this->load->view('admin/CardMaster/card_report',$data);
    }

    public function GateCard_Report_Details()
    {
       $from_date = $this->input->post('from_date');
       $to_date = $this->input->post('to_date');
       $State = $this->input->post('State');
       $City = $this->input->post('City'); 
        
       $where = array('IssueDate >=' => $from_date,'ExpiryDate <=' => $to_date); 	
       $Getfiltered_data = $this->CardModel->get_all_data($tablename="tblAccountWiseCardMaster",$where);
       foreach($Getfiltered_data as &$row)
       {
            $whclient = '(AccountID="' . $row['AccountID'] . '")';
            $clientdetails = $this->CardModel->get_data($tablename="tblclients",$whclient); 
            
            $wh_state = '(short_name="' . $clientdetails['state'] . '")';
            $statedetails = $this->CardModel->get_data($tablename="tblxx_statelist",$wh_state); 
            $row['state_name'] = $statedetails['state_name'];
       }
       header('Content-Type: application/json');
       echo json_encode($Getfiltered_data);
    }
    
    public function FarmerwiseCardAllocation()
    {       
        if (!has_permission_new('CardAllotment', '', 'view')) {
            access_denied('Invoice Items');
        }
        $CustomerType = 1;
        $KYCStatus = 6;
        $active = 1;
        $where = '(CustomerType="' . $CustomerType . '" AND KYCStatus="' . $KYCStatus . '" AND active="' . $active . '")';
        $allfarmers = $this->CardModel->get_all_data($tablename="tblclients",$where);
        $data['allfarmers'] = $allfarmers;

        $cardnames = $this->CardModel->get_all_table_data($tablename="tblCardMaster");
        $data['cardnames'] = $cardnames;
        $currentDate = date('Y-m-d');
        $data['currentDate'] = $currentDate;
        $subActGroupID = 1000017;
        $wh_paymentmethod =  '(SubActGroupID="' . $subActGroupID . '")';
        $payment_methods = $this->CardModel->get_all_data($tablename="tblclients",$wh_paymentmethod);
        $data['payment_methods'] = $payment_methods;
        $this->load->view('admin/CardMaster/Farmerwise_CardAllocation',$data);
    }
    public function update_farmerwise_carddetails()
    {             
        $selected_company = $this->session->userdata('root_company');
        $AccountID = $this->input->post('AccountID');       
        $prefix = $this->input->post('prefix');
        $validity = $this->input->post('validity');       
        $issuedate = to_sql_date($this->input->post('issuedate'))." ".date("H:i:s");
        $expirydate = to_sql_date($this->input->post('expirydate'))." ".date("H:i:s");
        $cardfees = $this->input->post('cardfees');
        $status = $this->input->post('status');
        $cardfeesreceived = $this->input->post('cardfeesreceived');
        $paymentmethod = $this->input->post('paymentmethod');
        $paymentmode = $this->input->post('paymentmode');
        $refno = $this->input->post('refno');
        $paymentdate = to_sql_date($this->input->post('paymentdate'))." ".date('H:i:s');
        $fy = $this->session->userdata('finacial_year');
        $where = '(AccountID="' . $AccountID . '")';   
        $accountWiseCard = $this->CardModel->get_data($tablename="tblAccountWiseCardMaster",$where);
        if(empty($accountWiseCard))
        {
            if (!has_permission_new('CardAllotment', '', 'create')) {
                access_denied('Invoice Items');
            }
            $nextCardNumber = get_option('next_card_number');  
            $formattedCardNumber = str_pad($nextCardNumber, 4, '0', STR_PAD_LEFT);          
            
            $last8Digits = substr($AccountID, -8);                          
            $formatted_AccountID = substr($last8Digits, 0, 4) . '-' . substr($last8Digits, 4);          
           
            //$card_number = $prefix.$fy .'-'. $formatted_AccountID .'-'. $formattedCardNumber;
            $card_number = $prefix.$fy.$last8Digits.$formattedCardNumber; 
            $insert_farmerwise_card = array(
                'AccountID'=>$AccountID,
                'Prefix '=>$prefix,
                'CardNumber'=>$card_number,
                'IssueDate'=>$issuedate,
                'ExpiryDate'=>$expirydate,
                'PaymentStatus'=>$cardfeesreceived,
                'PaymentMethod'=>$paymentmethod,
                'Amount'=>$cardfees,
                'PaymentMode'=>$paymentmode,
                'ReferanceNo'=>$refno,
                'PaymentDate'=>$paymentdate,
                'Status'=>$status,
                'UserID'=>$this->session->userdata('username'),
                'TransDate'=>date('Y-m-d h:i:s'), 
                'UserID2'=>$this->session->userdata('username'),   
                'Lupdate'=>date('Y-m-d h:i:s'),  
            );
            $cardallocation = $this->CardModel->insert_data($tablename="tblAccountWiseCardMaster",$insert_farmerwise_card);
            if($cardallocation)
            {   
                $this->increment_next_number('next_card_number'); 
                $wh_request = array('Prefix' => $prefix,'AccountID' => $AccountID);  
                $cardrequests = $this->CardModel->get_data($tablename="tblCardRequest",$wh_request); 
                
                if(!empty($cardrequests))
                {
                    // Update REquest Status
                    $update_farmer_request = array(
                        'status'=>1,
                    );                   
                    $cardgenerate = $this->CardModel->edit_data($tablename="tblCardRequest",$wh_request,$update_farmer_request); 
                }
                // Get Selected Card Feature
                $wh_cardwisefeatures = array('Prefix' => $prefix); 
                $staticfeatures = $this->CardModel->get_data($tablename="tblCardMaster",$wh_cardwisefeatures);
                
                // Welcome Bonus Credit Entry
                $nextpointsreceiptsnumber = get_option('next_points_receipts_number_for_kirti'); 
                $pointsledger = array(
                    'PlantID'=>$selected_company,
                    'FY'=>$fy,
                    'Transdate' =>date('Y-m-d h:i:s'), 
                    'VoucherID'=>$nextpointsreceiptsnumber,
                    'TransDate2'=>date('Y-m-d h:i:s'), 
                    'AccountID'=>$card_number,
                    'EntryFor'=>1,
                    'TType'=>"C",
                    'Amount'=>$staticfeatures['WelcomeBonus'],
                    'Narration'=>"WelcomeBonus",
                    'PassedFrom'=>"RECEIPTS",
                    'UserID'=>$this->session->userdata('username')
                );
                $Insertcardpoints_ledger = $this->CardModel->insert_data($tablename="tblCardPointsledger",$pointsledger);
                $this->increment_next_number('next_points_receipts_number_for_kirti');                

                $keysToDisplay = [
                    'WelcomeBonus',
                    'PointConversion',
                    'InterestRate',
                    'RateBenefits',
                    'RateBenefitUpto',
                    'Redmption',
                    'SoilTest',
                    'SoilTestDisc'
                ];
                // Add Card Features to Farmer
                foreach ($keysToDisplay as $key) 
                {               
                    $value = isset($staticfeatures[$key]) ? $staticfeatures[$key] : 'N/A';
                    $insert_accountwise_carddetail = array(
                        'AccountID'=>$AccountID,
                        'Prefix'=>$prefix,
                        'FeatureID'=>$key,
                        'Value'=>$value,
                    );
                    $insertfarmerAccountID_carddetails = $this->CardModel->insert_data($tablename="tblAccountWiseCardDetails",$insert_accountwise_carddetail);                    
                }
                
                // Add Receipt Voucher 
                $nextreceiptsnumber = get_option('next_receipts_number_for_kirti'); 
                if($cardfeesreceived == "Y")
                {
                    //Write Account ledger Entry
                    $accountledgercredit = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$fy,
                        'Transdate'=>date('Y-m-d h:i:s'), 
                        'VoucherID'=>$nextreceiptsnumber,
                        'TransDate2'=>date('Y-m-d h:i:s'), 
                        'AccountID'=>$AccountID,
                        'CounterAccount'=>$paymentmethod,
                        'EntryFor'=>1,
                        'TType'=>"C",
                        'Amount'=>$cardfees,
                        'Narration'=>"Card Fees",
                        'PassedFrom'=>"RECEIPTS",
                        'OrdinalNo'=>1,
                        'UserID'=>$this->session->userdata('username'),
                        'UserID2'=>$this->session->userdata('username'),
                        'lupdate'=>date('Y-m-d h:i:s'),
                    );   
                    $InsertcardAccount_ledger = $this->CardModel->insert_data($tablename="tblaccountledger",$accountledgercredit);                 
                    
                    $accountledgerdebit = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$fy,
                        'Transdate'=>date('Y-m-d h:i:s'), 
                        'VoucherID'=>$nextreceiptsnumber,
                        'TransDate2'=>date('Y-m-d h:i:s'), 
                        'AccountID'=>$paymentmethod,
                        'CounterAccount'=>$AccountID,
                        'EntryFor'=>1,
                        'TType'=>"D",
                        'Amount'=>$cardfees,
                        'Narration'=>"Card Fees",
                        'PassedFrom'=>"RECEIPTS",
                        'OrdinalNo'=>1,
                        'UserID'=>$this->session->userdata('username'),
                        'UserID2'=>$this->session->userdata('username'),
                        'lupdate'=>date('Y-m-d h:i:s'),
                    );
                    $InsertCarddebit_ledger = $this->CardModel->insert_data($tablename="tblaccountledger",$accountledgerdebit);                 
                    $this->increment_next_number('next_receipts_number_for_kirti'); 
                }
            }
        }
        else
        {
            if (!has_permission_new('CardAllotment', '', 'edit')) {
                access_denied('Invoice Items');
            }
            $updatefarmer_carddetails = array(                                       
                'Status'=>$status,
                'PaymentStatus'=>$cardfeesreceived,
                'UserID2'=>$this->session->userdata('username'),  
                'Lupdate'=>date('Y-m-d h:i:s'),  
            );            
            if($cardfeesreceived == "Y"){
                $updatefarmer_carddetails["PaymentMethod"] = $paymentmethod;
                $updatefarmer_carddetails["PaymentMode"] = $paymentmode;
                $updatefarmer_carddetails["ReferanceNo"] = $refno;
                $updatefarmer_carddetails["PaymentDate"] = $paymentdate;
            }
            $updatecardallocation = $this->CardModel->edit_data($tablename="tblAccountWiseCardMaster",$where,$updatefarmer_carddetails);
           
            // Add Receipt Voucher 
            $nextreceiptsnumber = get_option('next_receipts_number_for_kirti'); 
            if($accountWiseCard['PaymentStatus'] == "N" && $cardfeesreceived == "Y")
            {
                $accountledgercredit = array(
                    'PlantID'=>$selected_company,
                    'FY'=>$fy,
                    'Transdate'=>date('Y-m-d h:i:s'), 
                    'VoucherID'=>$nextreceiptsnumber,
                    'TransDate2'=>date('Y-m-d h:i:s'), 
                    'AccountID'=>$AccountID,
                    'CounterAccount'=>$paymentmethod,
                    'EntryFor'=>1,
                    'TType'=>"C",
                    'Amount'=>$cardfees,
                    'Narration'=>"Card Fees",
                    'PassedFrom'=>"RECEIPTS",
                    'OrdinalNo'=>1,
                    'UserID'=>$this->session->userdata('username'),
                    'UserID2'=>$this->session->userdata('username'),
                    'lupdate'=>date('Y-m-d h:i:s'),
                );   
                $InsertcardAccount_ledger = $this->CardModel->insert_data($tablename="tblaccountledger",$accountledgercredit);                 
                
                $accountledgerdebit = array(
                    'PlantID'=>$selected_company,
                    'FY'=>$fy,
                    'Transdate'=>date('Y-m-d h:i:s'), 
                    'VoucherID'=>$nextreceiptsnumber,
                    'TransDate2'=>date('Y-m-d h:i:s'), 
                    'AccountID'=>$paymentmethod,
                    'CounterAccount'=>$AccountID,
                    'EntryFor'=>1,
                    'TType'=>"D",
                    'Amount'=>$cardfees,
                    'Narration'=>"Card Fees",
                    'PassedFrom'=>"RECEIPTS",
                    'OrdinalNo'=>1,
                    'UserID'=>$this->session->userdata('username'),
                    'UserID2'=>$this->session->userdata('username'),
                    'lupdate'=>date('Y-m-d h:i:s'),
                );
                $InsertCarddebit_ledger = $this->CardModel->insert_data($tablename="tblaccountledger",$accountledgerdebit);                 
                $this->increment_next_number('next_receipts_number_for_kirti'); 
            }           
            echo json_encode(['success' => true]);     
        }                            
    }
    
    public function GetCardRequestList()
    {
        if (!has_permission_new('CardRequestList', '', 'export')) {
            access_denied('Invoice Items');
        }
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->GateControl_model->getRootCompany();
            
            $data = array(
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'status' => $this->input->post('status'),
                'cardtype' => $this->input->post('cardtype')               
            );           
            $result = $this->CardModel->GetAccountwiseCardRequestList($data);            
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
            $center_addr = array($address,);
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $center_addr);            
            
            $set_col_tk = [];
            $set_col_tk["Requested Date"] =  'Requested Date';
            $set_col_tk["AccountID"] = 'AccountID';
            $set_col_tk["Farmer Name"] = 'Farmer Name';
            $set_col_tk["Card Name"] = 'Card Name';
            $set_col_tk["Payment Status"] = 'Payment Status';
            $set_col_tk["Status"] = 'Status';    

            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                if($value["CardName"] == "Y"){
                    $PaymentStatus = " Payment Received";
                }else{
                    $PaymentStatus = " Payment Pending";
                }
                if($value["status"] == "0"){
                    $RequestStatus = "Pending";
                }elseif($value["status"] == "1"){
                    $RequestStatus = "Card Generated";
                }elseif($value["status"] == "2"){
                    $RequestStatus = "Request rejected	";
                }else{
                    $RequestStatus = "";
                }
                $list_add = [];
                $list_add[] = _d(substr($value["TransDate"],0,10));
                $list_add[] = $value["AccountID"];
                $list_add[] = $value["company"];
                $list_add[] = $value["CardName"];
                $list_add[] =$PaymentStatus;        
                $list_add[] =$RequestStatus;        
                
                $writer->writeSheetRow('Sheet1', $list_add);
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'FarmerWiseCardRequest.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }

    public function increment_next_number($name)
    {
        // Update next number in settings        
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('name', $name);
        $this->db->update(db_prefix() . 'options');
    }

    public function get_accountwise_carddetails_byId()
    {
        $AccountID = $this->input->post('AccountID');
        $where = array('AccountID' => $AccountID); 	
        $accountwise_data = $this->CardModel->get_data($tablename="tblAccountWiseCardMaster",$where);	
        $accountwise_data["IssueDate"] = _d(substr($accountwise_data["IssueDate"],0,10));
        $accountwise_data["ExpiryDate"] = _d(substr($accountwise_data["ExpiryDate"],0,10));
        $wh_card = array('Prefix' => $accountwise_data['Prefix']); 	
        $mastercard = $this->CardModel->get_data($tablename="tblCardMaster",$wh_card);	

        $wh_features = array('PrefixID' => $accountwise_data['Prefix']); 
        $cardfeatures = $this->CardModel->get_all_data($tablename="tblCardDetails",$wh_features);	
        foreach($cardfeatures as &$row)
        {
            $wh_feature_details = '(id="' . $row['FeatureID'] . '")';
            $features = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$wh_feature_details);
            $row['features'] = $features;
        }
        
        $wh_cardwisefeatures = array('Prefix' => $accountwise_data['Prefix']); 
        $staticfeatures = $this->CardModel->get_data($tablename="tblCardMaster",$wh_cardwisefeatures);

        $response = array(
                            'accountwise_data' => $accountwise_data,  
                            'mastercard'=>$mastercard,        
                            'cardfeatures'=>$cardfeatures,    
                            'staticfeatures'=>$staticfeatures             
                        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_card_details_byprefix()
    {
        $prefix = $this->input->post('prefix');
        $where = array('Prefix' => $prefix); 	
        $carddetails = $this->CardModel->get_data($tablename="tblCardMaster",$where);	

        $wh_features = array('PrefixID' => $prefix); 
        $cardfeatures = $this->CardModel->get_all_data($tablename="tblCardDetails",$wh_features);	
        foreach($cardfeatures as &$row)
        {
            $wh_feature_details = '(id="' . $row['FeatureID'] . '")';
            $features = $this->CardModel->get_data($tablename="tblCardFeatureMaster",$wh_feature_details);
            $row['features'] = $features;
        }

        $wh_cardwisefeatures = array('Prefix' => $prefix); 
        $staticfeatures = $this->CardModel->get_data($tablename="tblCardMaster",$wh_cardwisefeatures);

        $response = array(
                            'carddetails' => $carddetails,
                            'cardfeatures'=>$cardfeatures,    
                            'staticfeatures'=>$staticfeatures                                     
                        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    public function FarmerWiseCardList()
    {
        if (!has_permission_new('CardIssueList', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['company_detail'] = $this->GateControl_model->getRootCompany();
        $Statelist = $this->CardModel->get_all_table_data($tablename="tblxx_statelist");	
        $citylist = $this->CardModel->get_all_table_data($tablename="tblxx_citylist");	 
        $data['Statelist'] = $Statelist;
        $data['citylist'] = $citylist;
        $Status ="Y";
        $where = array('Status' => $Status);    
        $Allcards = $this->CardModel->get_all_data($tablename="tblCardMaster",$where);  
        $data['Allcards'] = $Allcards;
        $this->load->view('admin/CardMaster/FarmerWiseCardList',$data);
    }

    public function GetFarmerwiseCardList()
    {
        if (!has_permission_new('CardIssueList', '', 'view')) {
            access_denied('Invoice Items');
        }
       $from = $this->input->post('from_date');
       $to = $this->input->post('to_date');
       $from_dates = to_sql_date($from)." 00:00:00";
       $to_dates = to_sql_date($to)." 23:59:59";
       $State = $this->input->post('State');
       $City = $this->input->post('City'); 
       $paymentstatus = $this->input->post('paymentstatus');    
        $cardtype = $this->input->post('cardtype');
        $accountIds = [];
        $citywiseAccountID = [];
        if (!empty($State)) 
        {
            $whereserach_bystate = '(state="' . $State . '")';
            $statesreach = $this->CardModel->get_all_data($tablename = "tblclients", $whereserach_bystate);
            
            foreach ($statesreach as $client) {
                if (isset($client['AccountID'])) {
                    $accountIds[] = $client['AccountID'];
                }
            }
        } 
            
        if (!empty($City)) 
        {
            $whereserach_bycity = '(dist="' . $City . '")';
            $citysearch = $this->CardModel->get_all_data($tablename = "tblclients", $whereserach_bycity);
            
            foreach ($citysearch as $clients) {
                if (isset($clients['AccountID'])) {
                    $citywiseAccountID[] = $clients['AccountID'];
                }
            }
        }

        if(!empty($State) && !empty($City) && !empty($cardtype) && !empty($paymentstatus))
        {
            if(!empty($accountIds) && !empty($citywiseAccountID))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'Prefix'=>$cardtype,'PaymentStatus'=>$paymentstatus);               
                $this->db->where_in('AccountID', $accountIds);    
                $this->db->where_in('AccountID', $citywiseAccountID);       
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }
            else
            {
                $Getfiltered_data = []; 
            }
        }else if (!empty($State) && empty($City) && empty($cardtype) && empty($paymentstatus)) 
        {
            if(!empty($accountIds))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);               
                $this->db->where_in('AccountID', $accountIds);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }
            else
            {
                $Getfiltered_data = []; 
            }            
        }else if (!empty($City) && empty($State) && empty($cardtype) && empty($paymentstatus)) 
        {   
            if(!empty($citywiseAccountID))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);
                $this->db->where_in('AccountID', $citywiseAccountID);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }else if (!empty($cardtype) && empty($State) && empty($City) && empty($paymentstatus)) 
        {
            $where_Cardtype = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'Prefix'=>$cardtype);
            $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where_Cardtype);
        }else if (!empty($paymentstatus) && empty($State) && empty($City) && empty($cardtype)) 
        {
            $where_Cardtype = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'PaymentStatus'=>$paymentstatus);
            $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where_Cardtype);
        }
        else if (!empty($cardtype) && !empty($State) && empty($City) && empty($paymentstatus)) 
        {
            if(!empty($accountIds))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'Prefix'=>$cardtype);
                $this->db->where_in('AccountID', $accountIds);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }
        else if (!empty($cardtype) && !empty($City) && empty($State) && empty($paymentstatus)) 
        {
            if(!empty($citywiseAccountID))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'Prefix'=>$cardtype);
                $this->db->where_in('AccountID', $citywiseAccountID);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }
        else if (!empty($State) && !empty($City) && empty($cardtype) && empty($paymentstatus)) 
        {
            if(!empty($citywiseAccountID) && !empty($accountIds))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);
                $this->db->where_in('AccountID', $accountIds);   
                $this->db->where_in('AccountID', $citywiseAccountID);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }else if (!empty($paymentstatus) && !empty($cardtype) && empty($State) && empty($City)) 
        {           
            $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'PaymentStatus'=>$paymentstatus,'Prefix'=>$cardtype);
            $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
        }else if (!empty($paymentstatus) && !empty($State) && empty($cardtype) && empty($City)) 
        {           
            if(!empty($accountIds))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'PaymentStatus'=>$paymentstatus);
                $this->db->where_in('AccountID', $accountIds);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }else if (!empty($paymentstatus) && !empty($City) && empty($cardtype) && empty($State)) 
        {           
            if(!empty($citywiseAccountID))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'PaymentStatus'=>$paymentstatus);
                $this->db->where_in('AccountID', $citywiseAccountID);         
                $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
            }         
            else
            {
                $Getfiltered_data = []; 
            }
        }
        else if(empty($State) && empty($City) && empty($cardtype) && empty($paymentstatus)) {            
            $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);          
            $Getfiltered_data = $this->CardModel->get_all_data($tablename = "tblAccountWiseCardMaster", $where);
        }    
        else{  $Getfiltered_data = [];  }   

        foreach ($Getfiltered_data as &$row) 
        {
            $whclient = '(AccountID="' . $row['AccountID'] . '")';
            $clientdetails = $this->CardModel->get_data($tablename = "tblclients", $whclient); 
            $row['zip'] = $clientdetails['zip'];
            $row['company'] = $clientdetails['company'];
            $wh_state = '(short_name="' . $clientdetails['state'] . '")';
            $statedetails = $this->CardModel->get_data($tablename = "tblxx_statelist", $wh_state); 
            $row['state_name'] = $statedetails['state_name'];
        
            $wh_city = '(id="' . $clientdetails['dist'] . '")';
            $citylist = $this->CardModel->get_data($tablename = "tblxx_citylist", $wh_city); 
            $row['city_name'] = $citylist['city_name'];
        
            $wh_subdist = '(id="' . $clientdetails['subdist'] . '")';
            $taluka = $this->CardModel->get_data($tablename = "tblTalukaMaster", $wh_subdist);            
            
            $wh_cardname ='(Prefix="' . $row['Prefix'] . '")';
            $carddetails = $this->CardModel->get_data($tablename = "tblCardMaster", $wh_cardname);
            $row['card_name'] = $carddetails['CardName'];
            
            $row['address'] = implode(', ', [
                $clientdetails['house'],
                $clientdetails['street'],
                $clientdetails['loc'],
                $clientdetails['vtc'],
                $clientdetails['po'], 
                $taluka['TalukaName'],              
                $citylist['city_name'],
                $clientdetails['zip']
            ]);
        }        
        
        header('Content-Type: application/json');
        echo json_encode($Getfiltered_data);
    }
    
    public function GetAccountWiseCardList()
    {
        if (!has_permission_new('CardIssueList', '', 'export')) {
            access_denied('Invoice Items');
        }
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->GateControl_model->getRootCompany();
            
            $data = array(
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'State' => $this->input->post('State'),
                'City' => $this->input->post('City'),
                'paymentstatus' => $this->input->post('paymentstatus'),
                'cardtype'=>$this->input->post('cardtype')
            );
            $result = $this->CardModel->GetAccountWiseCardList($data);
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
            $center_addr = array($address,);
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["AccountID"] =  'AccountID';
            $set_col_tk["Farmer Name"] = 'Farmer Name';
            $set_col_tk["Card Name"] = 'Card Name';
            $set_col_tk["Payment Method"] = 'Payment Method';
            $set_col_tk["Payment Amount"] = 'Payment Amount';
            $set_col_tk["State"] = 'State';
            $set_col_tk["City"] = 'City';
            $set_col_tk["Pincode"] =  'Pincode';
            $set_col_tk["Address"] =  'Address';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["AccountID"];
                $list_add[] = $value["company"];
                $list_add[] = $value["CardName"];
                $list_add[] =$value['PaymentMethod'];
                $list_add[] =$value["Amount"];
                $list_add[] = $value["state_name"];
                $list_add[] = $value["city_name"];
                $list_add[] = $value["zip"];
                $Address = "";
                if($value["house"]){
                    $Address .= $value["house"].", ";
                }
                if($value["street"]){
                    $Address .= $value["street"].", ";
                }
                if($value["loc"]){
                    $Address .= $value["loc"].", ";
                }
                if($value["vtc"]){
                    $Address .= $value["vtc"].", ";
                }
                if($value["po"]){
                    $Address .= $value["po"].", ";
                }
                $list_add[] = $Address;
               
                $list_add[] = $row_a;
                $writer->writeSheetRow('Sheet1', $list_add);
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'FarmerWiseCardList.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }

    public function FarmerWiseCardRequest()
    {
        if (!has_permission_new('CardRequestList', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['company_detail'] = $this->GateControl_model->getRootCompany();
        $Status ="Y";
        $where = array('Status' => $Status); 	
        $Allcards = $this->CardModel->get_all_data($tablename="tblCardMaster",$where);	
        $data['Allcards'] = $Allcards; 
        $this->load->view('admin/CardMaster/FarmerWiseCardRequest',$data);
    }

    public function GetFarmerWiseCardRequest()
    {
        if (!has_permission_new('CardRequestList', '', 'view')) {
            access_denied('Invoice Items');
        }
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $status = $this->input->post('status');
        $cardtype = $this->input->post('cardtype');
        $paymentstatus = $this->input->post('paymentstatus');
        if (!empty($from_date) && !empty($to_date)) 
        {
            $from_dates = to_sql_date($from_date)." 00:00:00";
            $to_dates = to_sql_date($to_date)." 23:59:59";

            if(!empty($status) && empty($cardtype) && empty($paymentstatus))
            {
                $wh = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates , 'status'=>$status);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $wh);            
            }
            else if(empty($status) && !empty($cardtype) && empty($paymentstatus))
            {
                $whs = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates , 'Prefix'=>$cardtype);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs);   
            }
            else if(empty($status) && !empty($paymentstatus) && empty($cardtype))
            {
                $whs = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates , 'PaymentStatus'=>$paymentstatus);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs);   
            }
            else if(!empty($cardtype) && !empty($status) && empty($paymentstatus))
            {
                $whs = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates ,'Prefix'=>$cardtype,'status'=>$status);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs);   
            }
            else if(!empty($cardtype) && !empty($paymentstatus) && empty($status))
            {
                $whs = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates ,'Prefix'=>$cardtype,'PaymentStatus'=>$paymentstatus);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs);   
            }
            else if(!empty($status) && !empty($paymentstatus) && empty($cardtype))
            {
                $whs = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates ,'status'=>$status,'PaymentStatus'=>$paymentstatus);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs);   
            }
            else if(!empty($status) && !empty($paymentstatus) && !empty($cardtype))
            {
                $whs_All = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates ,'status'=>$status ,'Prefix'=>$cardtype,'PaymentStatus'=>$paymentstatus);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $whs_All); 
            }
            else if(empty($cardtype) && empty($status) && empty($paymentstatus))
            {
                $where = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);
                $Getfiltered_request = $this->CardModel->get_all_data($tablename = "tblCardRequest", $where);            
            }           
        }
        else {            
            $Getfiltered_request = [];
        }

        foreach($Getfiltered_request as &$val)
        {
            $whfarmer = '(AccountID="' . $val['AccountID'] . '")';
            $farmerdetails = $this->CardModel->get_data($tablename = "tblclients", $whfarmer); 
            $val['company'] = $farmerdetails['company'];

            $whprefix = '(Prefix="' . $val['Prefix'] . '")';
            $carddetails = $this->CardModel->get_data($tablename = "tblCardMaster", $whprefix);
            $val['CardName'] = $carddetails['CardName']; 
        }
        header('Content-Type: application/json');
        echo json_encode($Getfiltered_request);
    }
    
    public function PointsLedger()
    {
        if (!has_permission_new('CardLedger', '', 'view')) {
            access_denied('CardLedger');
        }
        $this->load->model('currencies_model');
        $data['title'] = "Points Ledger";
        $fy = $this->session->userdata('finacial_year');
        $fy1 = $fy."-04-01";
        $fy_new  = $fy + 1;
        $lastdate_date = '20'.$fy_new.'-03-31';
        $curr_date = date('Y-m-d');
        $curr_date_new    = new DateTime($curr_date);
        $last_date_yr = new DateTime($lastdate_date);
        if($last_date_yr < $curr_date_new){
            $date = $lastdate_date;
        }else{
                $date = date('Y-m-d');
        }
        $data['from_date'] = $fy1;
        $data['to_date'] = $date;
        $data['CardList'] = $this->CardModel->GetCardList();
        $data['company_detail'] = $this->GateControl_model->getRootCompany();
        
        $this->load->view('admin/CardMaster/CardLedger', $data);
    }
    public function GetLedgerData()
    {
        if (!has_permission_new('CardLedger', '', 'view')) {
            access_denied('CardLedger');
        }
        $data_filter = $this->input->post();
        $data_report = $this->CardModel->GetDataFromDateToDate($data_filter);
        $account_name = $this->CardModel->get_name_account($data_filter);
        $data = array();
        if($account_name->company){
            $name = $account_name->company;
            $actDetail = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
        }else{
            $name = $account_name->firstname." ". $account_name->lastname;
            $actDetail = $name." (".$account_name->AccountID.")";
        }
        
        $data["account_name"] = $actDetail;
        
        $new_acc_bal = 0;
        $opening_bal = 0;
        $i = 1;
        $CRSum = 0;
        $DRSum = 0;
        $finacial_year = $this->session->userdata('finacial_year');
        $from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
        $from_date = date('Y-m-d',strtotime($from_date));
        $to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
        $to_date = date('Y-m-d',strtotime($to_date));
        if($from_date > date('20'.$finacial_year.'-04-01')){
            $getuptofromdatebal = $this->CardModel->GetCrSumBeforeFromDate($data_filter);
            $CRSum = $getuptofromdatebal[0]['Amount'];
            $getuptofromdatebal = $this->CardModel->GetDrSumBeforeFromDate($data_filter);
            $DRSum = $getuptofromdatebal[0]['Amount'];
            $opening_bal = 0 + $DRSum - $CRSum;
            $new_acc_bal = 0 + $DRSum - $CRSum;
        }
        $total_debit = 0;
        $total_credit = 0;
        $html = '';
        if(empty($data_report)){
            $OCR = 0.00;
            $ODR = 0.00;
            if($new_acc_bal <=0){
                $OCR = abs($new_acc_bal);
                $OB = $OCR.'Cr';
            }else{
                $ODR = abs($new_acc_bal);
                $OB = $ODR.'Dr';
            }
            $html .= '<tr style="color:red;">';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td>'. _d($from_date).'</td>';
            $html .= '<td>Opening Balance</td>';
            $html .= '<td align="right">'.number_format($ODR,2).'</td>';
            $html .= '<td align="right">'.number_format($OCR,2).'</td>';
            $html .= '<td align="right">'.number_format($OB,2).'</td>';
            $html .= '</tr>';
            
            $html .= '<tr style="color:red;">';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td>'. _d($to_date).'</td>';
            $html .= '<td>Closing Balance</td>';
            $html .= '<td align="right">'.number_format($ODR,2).'</td>';
            $html .= '<td align="right">'.number_format($OCR,2).'</td>';
            $html .= '<td align="right">'.number_format($OB,2).'</td>';
            $html .= '</tr>';
            
        }else{
            $OCR = 0.00;
            $ODR = 0.00;
            if($new_acc_bal <=0){
                $OCR = abs($new_acc_bal);
                $OB = $OCR.'Cr';
            }else{
                $ODR = abs($new_acc_bal);
                $OB = $ODR.'Dr';
            }
            $html .= '<tr style="color:red;">';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td>'. _d($from_date).'</td>';
            $html .= '<td>Opening Balance</td>';
            $html .= '<td align="right">'.number_format($ODR,2).'</td>';
            $html .= '<td align="right">'.number_format($OCR,2).'</td>';
            $html .= '<td align="right">'.number_format($OB,2).'</td>';
            $html .= '</tr>';
            $total_credit = $total_credit + $OCR;
            $total_debit = $total_debit + $ODR;
            foreach ($data_report as $key => $value) {
                if($value["Amount"] !== "0.00"){
                    $html .= '<tr>';    
                        $html .= '<td>'. $value["PassedFrom"].'</td>';
                        $html .= '<td>'. $value["VoucherID"].'</td>';
                        $html .= '<td>'. _d(substr($value["Transdate"],0,10)).'</td>';
                        $html .= '<td title="'.$value["Narration"].'">'. substr($value["Narration"],0,67).''.$str.'</td>';
                        $dvalue = "";
                        if($value["TType"]=="D"){
                            $new_acc_bal = $new_acc_bal + $value["Amount"];
                            $dvalue = $value["Amount"];
                            $total_debit = $total_debit + $dvalue;
                            $dvalue = number_format($dvalue,2);
                        }
                        $html .= '<td align="right">'. $dvalue .'</td>';
                        $cvalue = "";
                        if($value["TType"]=="C"){
                            $new_acc_bal = $new_acc_bal - $value["Amount"];
                            $cvalue = $value["Amount"];
                            $total_credit = $total_credit + $cvalue;
                            $cvalue = number_format($cvalue,2);
                        }
                        $html .= '<td align="right">'.$cvalue.'</td>';
                        $new_acc_bal2 = $new_acc_bal;
                        if($new_acc_bal>0){
                            $nab_dr_cr = "Dr";
                        }else{
                            $nab_dr_cr = "Cr";
                        }
                        $new_acc_bal2 = round($new_acc_bal2,2)." ".$nab_dr_cr;
                        $html .= '<td align="right">'.number_format(abs($new_acc_bal),2)." ".$nab_dr_cr.'</td>';
                    $html .= '</tr>';
                    $i++;
                }
            }
            if($data_report){
                $html .= '<tr style="color:red;">';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td>Closing Balance</td>';
                    $html .= '<td align="right">'. number_format($total_debit,2).'</td>';
                    $html .= '<td align="right">'. number_format($total_credit,2).'</td>';
                    $html .= '<td align="right">'. number_format($new_acc_bal2,2)." ".$nab_dr_cr.'</td>';
                $html .= '</tr>';
            }
        }
        $data["table"] = $html;
        echo json_encode($data);
    }
    
    public function SoilTestRequest()
    {
        if (!has_permission_new('SoilTestRequest', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['tiltle'] = "Soil Test Request";  
        $data['company_detail'] = $this->GateControl_model->getRootCompany();
        $this->load->view('admin/CardMaster/SoilTestRequest',$data);
    }

    public function GateSoilTestRquest_Details()
    {        
        if (!has_permission_new('SoilTestRequest', '', 'view')) {
            access_denied('Invoice Items');
        }
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $Status = $this->input->post('Status');      
        
        $from_dates = to_sql_date($from_date).' 00:00:00';
        $to_dates = to_sql_date($to_date).' 23:59:59';     

        if(!empty($Status))
        {
            $wh = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates,'status'=>$Status);
            $Getfiltered_soiltest_request = $this->CardModel->get_all_data($tablename = "tblsoiltestrequest", $wh);            
        } else if($Status == "")
        {
            $wh = array('TransDate >=' => $from_dates, 'TransDate <=' => $to_dates);
            $Getfiltered_soiltest_request = $this->CardModel->get_all_data($tablename = "tblsoiltestrequest", $wh);            
        }else{  $Getfiltered_soiltest_request = [];  }          
        
        foreach($Getfiltered_soiltest_request as &$val)
        {
            $dateTime = new DateTime($val['TransDate']);             
            $formattedDate = $dateTime->format('d/m/Y');
            $val['formattedDate'] = $formattedDate;

            $whfarmer = '(AccountID="' . $val['AccountID'] . '")';
            $farmerdetails = $this->CardModel->get_data($tablename = "tblclients", $whfarmer); 
            $val['company'] = $farmerdetails['company'];

            $whprefix = '(Prefix="' . $val['Prefix'] . '")';
            $carddetails = $this->CardModel->get_data($tablename = "tblCardMaster", $whprefix);
            $val['CardName'] = $carddetails['CardName']; 
        }
        header('Content-Type: application/json');
        echo json_encode($Getfiltered_soiltest_request);
    }

    public function Update_Soilreq_Status()
    {
        if (!has_permission_new('SoilTestRequest', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $accountId = $this->input->post('accountId');
        $newStatus = $this->input->post('newStatus');
        $id = $this->input->post('id'); 
        $prefix = $this->input->post('prefix'); 

        $update_status = array(
            'status'=>$newStatus,
        );
        $where = '(id="' . $id . '" AND AccountID="' . $accountId . '")';
        $editstatus =$this->CardModel->edit_data($tablename = "tblsoiltestrequest", $where,$update_status);            
        if ($editstatus) 
        {  
            if($newStatus == 1)
            {
                $wh_accountwisedata =  '(Prefix="' . $prefix . '" AND AccountID="' . $accountId . '")';
                $accountwise_cardmaster_Details = $this->CardModel->get_data($tablename = "tblAccountWiseCardMaster",$wh_accountwisedata);            
                $soiltest_count = $accountwise_cardmaster_Details['SoilTest'] -1;
    
                $editsoiltest_count =array(
                    'SoilTest'=>$soiltest_count,
                  );       
                $updatecount =$this->CardModel->edit_data($tablename = "tblAccountWiseCardMaster",$wh_accountwisedata,$editsoiltest_count);            
            }
            echo json_encode(['success' => true]); 
        } else {
        echo json_encode(['success' => false]);
        }
    }  
    
    public function GetSoiltestReqList()
    {
        if (!has_permission_new('SoilTestRequest', '', 'export')) {
            access_denied('Invoice Items');
        }
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->GateControl_model->getRootCompany();
            
            $data = array(
                'from_date' => $this->input->post('from_date'),
                'to_date' => $this->input->post('to_date'),
                'status' => $this->input->post('status')                             
            );           
            $result = $this->CardModel->GetAccountwiseSoiltestRequestList($data);            
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
            $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
            $center_addr = array($address,);
            $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells
            $writer->writeSheetRow('Sheet1', $center_addr);            
            
            $set_col_tk = [];
            $set_col_tk["Requested Date"] =  'Requested Date';
            $set_col_tk["AccountID"] = 'AccountID';
            $set_col_tk["Farmer Name"] = 'Farmer Name';
            $set_col_tk["Card Name"] = 'Card Name';
            $set_col_tk["Status"] = 'Status';    

            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                if($value['status'] = "0"){
                    $status = "Pending";
                }else if($value['status'] = "1"){
                    $status = "Approved";
                }else if($value['status'] = "2"){
                    $status = "Rejected";
                }
                $list_add = [];
                $list_add[] = _d(substr($value["TransDate"],0,10));
                $list_add[] = $value["AccountID"];
                $list_add[] = $value["company"];
                $list_add[] = $value["CardName"];
                $list_add[] = $status;        
                
                $writer->writeSheetRow('Sheet1', $list_add);
            }
            //print_r($result);
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'SoilTestRequest.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }
    
    
}