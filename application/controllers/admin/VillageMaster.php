<?php
    defined('BASEPATH') or exit('No direct script access allowed');

class VillageMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();   
        $this->load->model('VillageModel');                        
    }

    public function AddEditVillage()
    {       
        if (!has_permission_new('VillageMaster', '', 'view')) {
            access_denied('Invoice Items');
        }
        $talukadetails = $this->VillageModel->get_all_table_data($tablename="tblTalukaMaster");
        $data['talukadetails'] = $talukadetails; 

        $statedetails = $this->VillageModel->get_all_table_data($tablename="tblxx_statelist");
        $data['statedetails'] = $statedetails;

        $citydetails =  $this->VillageModel->get_all_table_data($tablename="tblxx_citylist");
        $data['citydetails'] = $citydetails;

        $crops =  $this->VillageModel->get_all_table_data($tablename="tblcrops");
        $data['crops'] = $crops;

        $fertilizers =  $this->VillageModel->get_all_table_data($tablename="tblfertilizers");
        $data['fertilizers'] = $fertilizers;
        
        $seeds = $this->VillageModel->get_all_table_data($tablename="tblseed");
        $data['seeds'] = $seeds;      

        $pesticides = $this->VillageModel->get_all_table_data($tablename="tblpesticides");
        $data['pesticides'] = $pesticides;        

        $table_data = $this->VillageModel->get_all_table_data($tablename="tblvillagedetails");
        foreach($table_data as &$val)
        {
            $wh_taluka = '(id="'.$val['TalukaId'].'")';                         
            $talukaDetails = $this->VillageModel->get_data($tablename="tblTalukaMaster",$wh_taluka);
            $val['talukaname'] = $talukaDetails['TalukaName'];              
          
            $wh_district = '(id="'.$val['DistrictId'].'")';              
            $Districtdetail = $this->VillageModel->get_data($tablename="tblxx_citylist",$wh_district);
            $val['districtname'] = $Districtdetail['city_name']; 
            
            $wh_state = '(short_name="'.$val['StateId'].'")';                     
            $StateDetail = $this->VillageModel->get_data($tablename="tblxx_statelist",$wh_state);
            $val['statename'] = $StateDetail['state_name'];
        }
        $data['table_data'] = $table_data;
        
        $pincodes = $this->VillageModel->get_all_table_data($tablename="tblpin");
        $data['pincodes'] = $pincodes;   
        
        $vehicletypes = $this->VillageModel->get_all_table_data($tablename="tblvehicletype");
        $data['vehicletypes'] = $vehicletypes; 
        $this->load->view('admin/VillageMaster/AddEditVillage',$data);
    }

    public function addVillageDetails()
    {
        if (!has_permission_new('VillageMaster', '', 'create')) {
            access_denied('Invoice Items');
        }
        $visitdate = $this->input->post('visitdate');
        $villagename = $this->input->post('villagename');
        $pin = $this->input->post('pin');
        $taluka = $this->input->post('taluka');
        $district = $this->input->post('district');
        $state = $this->input->post('state');
        $villagesarpanch = $this->input->post('villagesarpanch');
        $villagepopulation = $this->input->post('villagepopulation');
        $villagearea = $this->input->post('villagearea');
        $villageinfluencername = $this->input->post('villageinfluencername');
        $govtpost = $this->input->post('govtpost');
        $mobileno = $this->input->post('mobileno');
        $rtrsno = $this->input->post('rtrsno');
        $otherinfo = $this->input->post('otherinfo');

        $datetime = DateTime::createFromFormat('d/m/Y', $visitdate);       
        $visitdate_formatted = $datetime->format('Y-m-d') . ' ' . date('H:i:s') . '.000000';   
        
        //hotel details
        $hotelDetails = $this->input->post('HotelDetails'); 

        //aggregator details
        $aggregatorDetails = $this->input->post('aggregatorDetails');    
        
        //ksk details
        $kskDetails = $this->input->post('kskDetails');   
        
        //crop details
        $cropDetails = $this->input->post('cropDetails');              
        
        //village vehicle details
        $vehicleDetails = $this->input->post('vehicleDetails');  

        //save single row 
        $HotelName = $this->input->post('hotelName'); 
        $OwnerName = $this->input->post('OwnerName');  
        $OwnerMobile = $this->input->post('ownerMobile');  
        
        $aggregatorName = $this->input->post('aggregatorName');  
        $aggregatorNumber = $this->input->post('aggregatorNumber');   
        
        $kskname = $this->input->post('kskname'); 
        $kskshopownername = $this->input->post('kskshopownername'); 
        $kskshopownerno = $this->input->post('kskshopownerno'); 

        $cropname = $this->input->post('cropname'); 
        $fertilizername = $this->input->post('fertilizername'); 
        $seedname = $this->input->post('seedname'); 
        $pesticidename = $this->input->post('pesticidename');                

        $vehicle_type = $this->input->post('vehicle_type'); 
        $regno = $this->input->post('regno'); 
        $capacity = $this->input->post('capacity'); 
        $drivername = $this->input->post('drivername'); 
        $driverno = $this->input->post('driverno'); 
        $vehownername = $this->input->post('vehownername'); 
        $vehownerno = $this->input->post('vehownerno');         

        $insert_villagedetails = array(           
            'VisitDate'=>$visitdate_formatted,   
            'VillageName'=>$villagename,   
            'Pincode'=>$pin,   
            'TalukaId'=>$taluka,   
            'DistrictId'=>$district,   
            'StateId'=>$state,   
            'VillageSarpanch'=>$villagesarpanch,   
            'VillagePopulation'=>$villagepopulation,   
            'Area'=>$villagearea,  
            'InfluencerName'=>$villageinfluencername,
            'InfluencerGovtPost'=>$govtpost,
            'Influencer_MobNo'=>$mobileno,
            'NoRtrsFarmers'=>$rtrsno,
            'OtherInformation'=>$otherinfo,
            'UserID'=>$this->session->userdata('username'),
            'datecreated'=>date('Y-m-d h:i:s')
        );        
        $createVillageDetails = $this->VillageModel->insert_data($tablename="tblvillagedetails",$insert_villagedetails);
        if($createVillageDetails) 
        {     
            //insert hotel details
            if($hotelDetails)
            {
                foreach ($hotelDetails as $hotels) 
                {
                   if (!empty($hotels['Hotname']) && !empty($hotels['hotownerName']) && !empty($hotels['hotownerMobile'])) 
                    {
                       $add_hoteldetails = array(
                            'VillageDetailId'=>$createVillageDetails,
                            'HotelName'=>$hotels['Hotname'],
                            'OwnerName'=>$hotels['hotownerName'],
                            'OwnerMobileNo'=>$hotels['hotownerMobile'],
                            'UserID'=>$this->session->userdata('username'),
                            'TransDate'=>date('Y-m-d h:i:s')
                       );
                       $createHotelDetails = $this->VillageModel->insert_data($tablename="tblVillageHotelDetails",$add_hoteldetails);
                    } 
                } 
            }
            else if($HotelName && $OwnerName && $OwnerMobile)
            {
                $add_villagehoteldetails_row = array(
                    'VillageDetailId'=>$createVillageDetails,
                    'HotelName'=>$HotelName,
                    'OwnerName'=>$OwnerName,
                    'OwnerMobileNo'=>$OwnerMobile,
                    'UserID'=>$this->session->userdata('username'),
                    'TransDate'=>date('Y-m-d h:i:s')
               );
               $createHotel = $this->VillageModel->insert_data($tablename="tblVillageHotelDetails",$add_villagehoteldetails_row);
            } 
            
            //insert village aggregator details
            if($aggregatorDetails)
            {
                foreach ($aggregatorDetails as $aggregator) 
                {
                    if (!empty($aggregator['name']) && !empty($aggregator['no'])) 
                    {
                       $add_aggregatordetails = array(
                            'VillageDetailId'=>$createVillageDetails,
                            'VillageAggregatorName'=>$aggregator['name'],
                            'AggregatorMobNo'=>$aggregator['no'],
                            'UserID'=>$this->session->userdata('username'),
                            'datecreated'=>date('Y-m-d h:i:s')
                       );
                       $createAggregatorDetails = $this->VillageModel->insert_data($tablename="tblvillageaggregatordetails",$add_aggregatordetails);
                    } 
                } 
            }   
            else if($aggregatorName && $aggregatorNumber)
            {
                $add_aggregatordetails_row = array(
                    'VillageDetailId'=>$createVillageDetails,
                    'VillageAggregatorName'=>$aggregatorName,
                    'AggregatorMobNo'=>$aggregatorNumber,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s')
               );
               $createAggregatorDetailsrow = $this->VillageModel->insert_data($tablename="tblvillageaggregatordetails",$add_aggregatordetails_row);
            }        

            //insert ksk details
            if($kskDetails)
            {
                foreach ($kskDetails as $ksk) 
                {
                    if (!empty($ksk['kskName']) && !empty($ksk['kskownershopname']) && !empty($ksk['kskshopownerNo'])) 
                    {
                        $add_kskdetails = array(
                            'VillageDetailId'=>$createVillageDetails,
                            'KskName'=>$ksk['kskName'],
                            'KskShopOwnerName'=>$ksk['kskownershopname'],
                            'KskShopOwnerNo'=>$ksk['kskshopownerNo'],
                            'UserID'=>$this->session->userdata('username'),
                            'datecreated'=>date('Y-m-d h:i:s')
                       );
                       $createKskDetails = $this->VillageModel->insert_data($tablename="tblvillagekskdetails",$add_kskdetails);
                    }
                }
            }
            else if($kskname && $kskshopownername && $kskshopownerno)
            {
                $add_kskdetails_row = array(
                    'VillageDetailId'=>$createVillageDetails,
                    'KskName'=>$kskname,
                    'KskShopOwnerName'=>$kskshopownername,
                    'KskShopOwnerNo'=>$kskshopownerno,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s')
               );
               $createKskDetailsrow = $this->VillageModel->insert_data($tablename="tblvillagekskdetails",$add_kskdetails_row);
            } 

          
            //insert village crop details
            if($cropDetails)
            {
                foreach ($cropDetails as $crop) 
                {
                    if (!empty($crop['cropname']) && !empty($crop['fertilizername']) && !empty($crop['seedname']) && !empty($crop['pesticidename'])) 
                    {
                        $cropIds = $crop['cropname'];                        
                        $fertilizerIds = implode(',', $crop['fertilizername']);
                        $seedIds = implode(',', $crop['seedname']);
                        $pesticideIds = implode(',', $crop['pesticidename']);

                        $add_cropdetails = array(
                            'VillageDetailId'=>$createVillageDetails,
                            'CropId'=>$cropIds,
                            'FertilizerId'=>$fertilizerIds,
                            'SeedId'=>$seedIds,
                            'PesticideId'=>$pesticideIds,
                            'UserID'=>$this->session->userdata('username'),
                            'datecreated'=>date('Y-m-d h:i:s')
                        );
                        $createcropDetails = $this->VillageModel->insert_data($tablename="tblvillagecropdetails",$add_cropdetails);
                    }
                }
            }
            else if($cropname && $fertilizername && $seedname && $pesticidename)
            {
                if (count($fertilizername) > 1) {
                    $fertilizerString = implode(',', $fertilizername);
                } else {                   
                    $fertilizerString = $fertilizername[0];
                }

                // if (count($cropname) > 1) {
                //     $cropString = implode(',', $cropname);
                // } else {                   
                //     $cropString = $cropname[0];
                // }

                if (count($seedname) > 1) {
                    $seedString = implode(',', $seedname);
                } else {                   
                    $seedString = $seedname[0];
                }

                if (count($pesticidename) > 1) {
                    $pesticideString = implode(',', $pesticidename);
                } else {                   
                    $pesticideString = $pesticidename[0];
                }
               
                $add_cropdetails_row = array(
                    'VillageDetailId'=>$createVillageDetails,
                    'CropId'=>$cropname,
                    'FertilizerId'=>$fertilizerString,
                    'SeedId'=>$seedString,
                    'PesticideId'=>$pesticideString,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s')
                );
                $createcropDetails_row = $this->VillageModel->insert_data($tablename="tblvillagecropdetails",$add_cropdetails_row);
            }

            //insert vehicle details
            if($vehicleDetails)
            {
                foreach ($vehicleDetails as $vehicle) 
                {
                    if (!empty($vehicle['vehicleType']) && !empty($vehicle['RegNo']) && !empty($vehicle['vehicleCapacity']) && !empty($vehicle['Drivername']) && !empty($vehicle['driverNo']) && !empty($vehicle['ownerName']) && !empty($vehicle['ownerNo'])) 
                    {
                        $add_vehicledetails = array(
                            'VillageDetailId'=>$createVillageDetails,
                            'VehicleType'=>$vehicle['vehicleType'],
                            'RegsiterNo'=>$vehicle['RegNo'],
                            'capacity'=>$vehicle['vehicleCapacity'],
                            'DriverName'=>$vehicle['Drivername'],
                            'MobileNo'=>$vehicle['driverNo'],
                            'OwnerName'=>$vehicle['ownerName'],
                            'OwnerMobNo'=>$vehicle['ownerNo'],
                            'UserID'=>$this->session->userdata('username'),
                            'datecreated'=>date('Y-m-d h:i:s')
                        );
                        $vehicleDetails = $this->VillageModel->insert_data($tablename="tblvillagevehicledetails",$add_vehicledetails);
                    }
                }
            }
            else if($vehicle_type && $regno && $capacity && $drivername && $driverno && $vehownername && $vehownerno)
            {
                $add_vehicledetails_row = array(
                    'VillageDetailId'=>$createVillageDetails,
                    'VehicleType'=>$vehicle_type,
                    'RegsiterNo'=>$regno,
                    'capacity'=>$capacity,
                    'DriverName'=>$drivername,
                    'MobileNo'=>$driverno,
                    'OwnerName'=>$vehownername,
                    'OwnerMobNo'=>$vehownerno,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s')
                );
                $vehicleDetails_row = $this->VillageModel->insert_data($tablename="tblvillagevehicledetails",$add_vehicledetails_row);
            }
            echo json_encode(['success' => true,'message' => 'Data inserted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert details']);
        }        
    }

    public function GetVillageDetailByID()
    {
        $VillageId = $this->input->post('VillageId');
        $where = '(id="'.$VillageId.'")';
        $villageDetails = $this->VillageModel->get_data($tablename="tblvillagedetails",$where);
        $data['villageDetails'] = $villageDetails;     
        
        $active = 0;
        $whs = '(VillageDetailId="'.$VillageId.'" AND isActive="'.$active.'")';
        $villageAggregatorDetails = $this->VillageModel->get_all_data($tablename="tblvillageaggregatordetails",$whs);
        $data['villageAggregatorDetails'] = $villageAggregatorDetails;
        
         $whhotel = '(VillageDetailId="'.$VillageId.'" AND IsActive="'.$active.'")';
        $hoteldetails = $this->VillageModel->get_all_data($tablename="tblVillageHotelDetails",$whhotel);
        $data['HotelDetails'] = $hoteldetails;

        $whksk = '(VillageDetailId="'.$VillageId.'" AND isActive="'.$active.'")';
        $kskdetails = $this->VillageModel->get_all_data($tablename="tblvillagekskdetails",$whksk);
        $data['kskDetails'] = $kskdetails;

        $whcrop = '(VillageDetailId="'.$VillageId.'" AND isActive="'.$active.'")';
        $cropdetails = $this->VillageModel->get_all_data($tablename="tblvillagecropdetails",$whcrop);
        
        $whstate = '(id="'.$villageDetails['StateId'].'")';
        $state = $this->VillageModel->get_all_data($tablename="tblxx_statelist",$whstate);
        $data['Statename'] = $state;
        
        $whcity = '(id="'.$villageDetails['DistrictId'].'")';
        $city = $this->VillageModel->get_all_data($tablename="tblxx_citylist",$whcity);
        $data['Cityname'] = $city;
        
        $whtaluka = '(id="'.$villageDetails['TalukaId'].'")';
        $taluka = $this->VillageModel->get_all_data($tablename="tblTalukaMaster",$whtaluka);
        $data['Talukaname'] = $taluka;
        
        foreach($cropdetails as &$val)
        {
            $cropIds = explode(',', $val['CropId']); 
            $cropIds = array_map('intval', $cropIds);  
            $cropIdsString = implode(',', $cropIds);  
            $whcrop = 'id IN (' . $cropIdsString . ')';
            $cropmajor = $this->VillageModel->get_all_data($tablename="tblcrops",$whcrop); 
            $cropNames = [];                      
            foreach ($cropmajor as $crop) {
                $cropNames[] = $crop['CropName'];            
            }               
            $val['cropname'] = implode(', ', $cropNames);        

            $fertilizerIds = explode(',', $val['FertilizerId']);       
            $fertilizerIds = array_map('intval', $fertilizerIds);           
            $fertilizerIdsString = implode(',', $fertilizerIds);

            $whfer = 'id IN (' . $fertilizerIdsString . ')';
            $ferdetails = $this->VillageModel->get_all_data($tablename="tblfertilizers",$whfer);  
            $fertilizerNames = [];                      
            foreach ($ferdetails as $fertilizer) {
                $fertilizerNames[] = $fertilizer['fertilizerName'];            
            }                   
            $val['fertilizername'] = implode(', ', $fertilizerNames);         

            $seedIds = explode(',', $val['SeedId']);  
            $seedIds = array_map('intval', $seedIds);           
            $seedIdsString = implode(',', $seedIds);

            $whseed = 'id IN (' . $seedIdsString . ')';
            $seeddetails = $this->VillageModel->get_all_data($tablename="tblseed",$whseed);
            $seedNames = [];                      
            foreach ($seeddetails as $seed) {
                $seedNames[] = $seed['SeedName'];            
            }   
            $val['SeedName'] = implode(', ', $seedNames); 

            $pesticideIds = explode(',', $val['PesticideId']);
            $pesticideIds = array_map('intval', $pesticideIds);  
            $pesticideIdsString = implode(',', $pesticideIds);

            $whpesti = 'id IN (' . $pesticideIdsString . ')';
            $pesticidedetails = $this->VillageModel->get_all_data($tablename="tblpesticides",$whpesti);
            $pesticideNames = [];                      
            foreach ($pesticidedetails as $pesti) {
                $pesticideNames[] = $pesti['PesticideName'];            
            }   
            $val['PesticideName'] = implode(', ', $pesticideNames); 
        }          
        $data['cropdetails'] = $cropdetails;

        $whvehicle = '(VillageDetailId="'.$VillageId.'" AND isActive="'.$active.'")';
        $vehicleDetails = $this->VillageModel->get_all_data($tablename="tblvillagevehicledetails",$whvehicle);
        $data['vehicleDetails'] = $vehicleDetails;

        $crops =  $this->VillageModel->get_all_table_data($tablename="tblcrops");
        $data['crops'] = $crops;

        $fertilizers =  $this->VillageModel->get_all_table_data($tablename="tblfertilizers");
        $data['fertilizers'] = $fertilizers;

        $seeds = $this->VillageModel->get_all_table_data($tablename="tblseed");
        $data['seeds'] = $seeds;

        $pesticides = $this->VillageModel->get_all_table_data($tablename="tblpesticides");
        $data['pesticides'] = $pesticides;

        echo json_encode($data);
    }

    public function GetModeltabledata()
    {
        $table_data = $this->VillageModel->get_all_table_data($tablename="tblvillagedetails");        
        echo json_encode($table_data);
    }

    public function UpdateVillageDetails()
    {        
        if (!has_permission_new('VillageMaster', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $VillageId = $this->input->post('VillageId');
        $visitdate = $this->input->post('visitdate');
        $villagename = $this->input->post('villagename');
        $pin = $this->input->post('pin');
        $taluka = $this->input->post('taluka');
        $district = $this->input->post('district');
        $state = $this->input->post('state');
        $villagesarpanch = $this->input->post('villagesarpanch');
        $villagepopulation = $this->input->post('villagepopulation');
        $villagearea = $this->input->post('villagearea');
        $villageinfluencername = $this->input->post('villageinfluencername');
        $govtpost = $this->input->post('govtpost');
        $mobileno = $this->input->post('mobileno');
        $rtrsno = $this->input->post('rtrsno');
        $otherinfo = $this->input->post('otherinfo');

        $datetime = DateTime::createFromFormat('d/m/Y', $visitdate);       
        $visitdate_formatted = $datetime->format('Y-m-d') . ' ' . date('H:i:s') . '.000000';   

        //hotel details
        $hotelDetails = $this->input->post('HotelDetails'); 

        //aggregator details
        $aggregatorDetails = $this->input->post('aggregatorDetails');               
        
        //ksk details
        $kskDetails = $this->input->post('kskDetails');          
        
        //crop details
        $cropDetails = $this->input->post('cropDetails');      

        //vehicle details
        $vehicleDetails = $this->input->post('vehicleDetails');

        //save single row 
        $HotelName = $this->input->post('hotelName');  
        $OwnerName = $this->input->post('OwnerName');  
        $OwnerMobile = $this->input->post('ownerMobile');  
        
        $aggregatorName = $this->input->post('aggregatorName');  
        $aggregatorNumber = $this->input->post('aggregatorNumber');  

        $kskname = $this->input->post('kskname'); 
        $kskshopownername = $this->input->post('kskshopownername'); 
        $kskshopownerno = $this->input->post('kskshopownerno'); 

        $cropname = $this->input->post('cropname');                
        $fertilizername = $this->input->post('fertilizername'); 
        $seedname = $this->input->post('seedname'); 
        $pesticidename = $this->input->post('pesticidename'); 

        $vehicle_type = $this->input->post('vehicle_type'); 
        $regno = $this->input->post('regno'); 
        $capacity = $this->input->post('capacity'); 
        $drivername = $this->input->post('drivername'); 
        $driverno = $this->input->post('driverno'); 
        $vehownername = $this->input->post('vehownername'); 
        $vehownerno = $this->input->post('vehownerno');   

        $update_villagedetails = array(           
            'VisitDate'=>$visitdate_formatted,   
            'VillageName'=>$villagename,   
            'Pincode'=>$pin,   
            'TalukaId'=>$taluka,   
            'DistrictId'=>$district,   
            'StateId'=>$state,   
            'VillageSarpanch'=>$villagesarpanch,   
            'VillagePopulation'=>$villagepopulation,   
            'Area'=>$villagearea,  
            'InfluencerName'=>$villageinfluencername,
            'InfluencerGovtPost'=>$govtpost,
            'Influencer_MobNo'=>$mobileno,
            'NoRtrsFarmers'=>$rtrsno,
            'OtherInformation'=>$otherinfo,              
            'UserID2'=>$this->session->userdata('username'),
            'dateupdatedat'=>date('Y-m-d h:i:s')
        );          
        $where = '(id="'.$VillageId.'")';          
        $updateVillageDetails = $this->VillageModel->edit_data($tablename="tblvillagedetails",$where,$update_villagedetails);
        if($updateVillageDetails) 
        {      
           //update village hotel details
            $whhotel = '(VillageDetailId="'.$VillageId.'")';  
            $allHoteldata = $this->VillageModel->get_all_data($tablename="tblVillageHotelDetails",$whhotel);
            if($hotelDetails)
            {
                $missingHotelData = [];

                foreach ($allHoteldata as $dbhotel) 
                {                    
                    $found = false;

                    foreach ($hotelDetails as $userHotel) {
                        if ($dbhotel['HotelName'] === $userHotel['Hotname'] && $dbhotel['OwnerName'] === $userHotel['hotownerName'] && $dbhotel['OwnerMobileNo'] === $userHotel['hotownerMobile']) {
                            $found = true;
                            break; 
                        }
                    }
                   
                    if (!$found) {
                        $missingHotelData[] = $dbhotel;
                    }
                }
                
                if (!empty($missingHotelData)) {
                    foreach ($missingHotelData as $missingdata) 
                    {
                        $updatestatush = array(
                            'IsActive'=>1,
                        );
                        $whh = '(id="'.$missingdata['id'].'")';     
                        $updatestat = $this->VillageModel->edit_data($tablename="tblVillageHotelDetails",$whh,$updatestatush);
                    }
                }    
                
                foreach ($hotelDetails as $hotel) 
                {
                    if (!empty($hotel['Hotname']) && !empty($hotel['hotownerName']) && !empty($hotel['hotownerMobile'])) 
                    {  
                        $whexists = '(VillageDetailId="'.$VillageId.'" AND HotelName="'.$hotel['Hotname'].'" AND OwnerName="'.$hotel['hotownerName'].'" AND OwnerMobileNo="'.$hotel['hotownerMobile'].'")'; 
                        $isexisthotel = $this->VillageModel->get_data($tablename="tblVillageHotelDetails",$whexists);
                        if(empty($isexisthotel))
                        {
                            $add_hoteldetails = array(
                                'VillageDetailId'=>$VillageId,
                                'HotelName'=>$hotel['Hotname'],
                                'OwnerName'=>$hotel['hotownerName'],
                                'OwnerMobileNo'=>$hotel['hotownerMobile'],
                                'UserID'=>$this->session->userdata('username'),
                                'TransDate'=>date('Y-m-d h:i:s'),
                                'UserID2'=>$this->session->userdata('username'),
                                'Lupdate'=>date('Y-m-d h:i:s')
                            );
                            $createHotelDetails = $this->VillageModel->insert_data($tablename="tblVillageHotelDetails",$add_hoteldetails);
                        }                       
                    } 
                }  
            }
            else
            {
                foreach ($allHoteldata as $missingh) 
                {
                    $updatestatusd = array(
                        'IsActive'=>1,
                    );
                    $whd = '(id="'.$missingh['id'].'")';     
                    $updatestatd = $this->VillageModel->edit_data($tablename="tblVillageHotelDetails",$whd,$updatestatusd);
                }
            }
            
            if($HotelName && $OwnerName && $OwnerMobile)
            {
                $add_hoteldetails_row = array(
                    'VillageDetailId'=>$VillageId,
                    'HotelName'=>$HotelName,
                    'OwnerName'=>$OwnerName,
                    'OwnerMobileNo'=>$OwnerMobile,
                    'UserID'=>$this->session->userdata('username'),
                    'TransDate'=>date('Y-m-d h:i:s'),
                    'UserID2'=>$this->session->userdata('username'),
                    'Lupdate'=>date('Y-m-d h:i:s')
               );
               $createHotelsrow = $this->VillageModel->insert_data($tablename="tblVillageHotelDetails",$add_hoteldetails_row);
            }   
            
            //update village aggregator details
            $whaggregator = '(VillageDetailId="'.$VillageId.'")';  
            $allaggregatordetails = $this->VillageModel->get_all_data($tablename="tblvillageaggregatordetails",$whaggregator);
            if($aggregatorDetails)
            {             
                $missingAggregatorData = [];

                foreach ($allaggregatordetails as $dbAggregator) 
                {                    
                    $found = false;

                    foreach ($aggregatorDetails as $userAggregator) {
                        if ($dbAggregator['VillageAggregatorName'] === $userAggregator['name'] && $dbAggregator['AggregatorMobNo'] === $userAggregator['no']) {
                            $found = true;
                            break; 
                        }
                    }
                   
                    if (!$found) {
                        $missingAggregatorData[] = $dbAggregator;
                    }
                }

                if (!empty($missingAggregatorData)) {
                    foreach ($missingAggregatorData as $missingagg) 
                    {
                        $updatestatus = array(
                            'isActive'=>1,
                        );
                        $wh = '(id="'.$missingagg['id'].'")';     
                        $updatestat = $this->VillageModel->edit_data($tablename="tblvillageaggregatordetails",$wh,$updatestatus);
                    }
                }               
               
                foreach ($aggregatorDetails as $aggregator) 
                {
                    if (!empty($aggregator['name']) && !empty($aggregator['no'])) 
                    {  
                        $whexist = '(VillageDetailId="'.$VillageId.'" AND VillageAggregatorName="'.$aggregator['name'].'" AND AggregatorMobNo="'.$aggregator['no'].'")'; 
                        $isexist = $this->VillageModel->get_data($tablename="tblvillageaggregatordetails",$whexist);
                        if(empty($isexist))
                        {
                            $add_aggregatordetails = array(
                                'VillageDetailId'=>$VillageId,
                                'VillageAggregatorName'=>$aggregator['name'],
                                'AggregatorMobNo'=>$aggregator['no'],
                                'UserID'=>$this->session->userdata('username'),
                                'datecreated'=>date('Y-m-d h:i:s'),
                                'UserID2'=>$this->session->userdata('username'),
                                'dateupdatedat'=>date('Y-m-d h:i:s')
                            );
                            $createAggregatorDetails = $this->VillageModel->insert_data($tablename="tblvillageaggregatordetails",$add_aggregatordetails);
                        }                       
                    } 
                }                 
            }   
            else
            {
                foreach ($allaggregatordetails as $missingu) 
                {
                    $updatestatus = array(
                        'isActive'=>1,
                    );
                    $wh = '(id="'.$missingu['id'].'")';     
                    $updatestat = $this->VillageModel->edit_data($tablename="tblvillageaggregatordetails",$wh,$updatestatus);
                }
            }

            if($aggregatorName && $aggregatorNumber)
            {
                $add_aggregatordetails_row = array(
                    'VillageDetailId'=>$VillageId,
                    'VillageAggregatorName'=>$aggregatorName,
                    'AggregatorMobNo'=>$aggregatorNumber,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s'),
                    'UserID2'=>$this->session->userdata('username'),
                    'dateupdatedat'=>date('Y-m-d h:i:s')
               );
               $createAggregatorDetailsrow = $this->VillageModel->insert_data($tablename="tblvillageaggregatordetails",$add_aggregatordetails_row);
            }     
            
            //update ksk details
            $whksk = '(VillageDetailId="'.$VillageId.'")';  
            $allkskdetails = $this->VillageModel->get_all_data($tablename="tblvillagekskdetails",$whksk);
            if($kskDetails)
            {               
                $missingKskData = [];  
                
                foreach ($allkskdetails as $dbksk) 
                {                    
                    $found = false;

                    foreach ($kskDetails as $userKsk) {
                        if ($dbksk['KskName'] === $userKsk['kskName'] && $dbksk['KskShopOwnerName'] === $userKsk['kskownershopname'] && $dbksk['KskShopOwnerNo'] === $userKsk['kskshopownerNo']) {
                            $found = true;
                            break; 
                        }
                    }
                   
                    if (!$found) {
                        $missingKskData[] = $dbksk;
                    }
                }

                if (!empty($missingKskData)) {
                    foreach ($missingKskData as $missing) 
                    {
                        $updatestatusksk = array(
                            'isActive'=>1,
                        );
                        $whksk = '(id="'.$missing['id'].'")';     
                        $updatestatksk = $this->VillageModel->edit_data($tablename="tblvillagekskdetails",$whksk,$updatestatusksk);
                    }
                }  
                
                //update ksk details
                foreach ($kskDetails as $ksk) 
                {
                    if (!empty($ksk['kskName']) && !empty($ksk['kskownershopname']) && !empty($ksk['kskshopownerNo'])) 
                    {          
                        $whexistksk = '(VillageDetailId="'.$VillageId.'" AND KskName="'.$ksk['kskName'].'" AND KskShopOwnerName="'.$ksk['kskownershopname'].'" AND KskShopOwnerNo="'.$ksk['kskshopownerNo'].'")'; 
                        $isexistksk = $this->VillageModel->get_data($tablename="tblvillagekskdetails",$whexistksk);

                        if(empty($isexistksk))
                        {
                            $add_kskdetails = array(
                                'VillageDetailId'=>$VillageId,
                                'KskName'=>$ksk['kskName'],
                                'KskShopOwnerName'=>$ksk['kskownershopname'],
                                'KskShopOwnerNo'=>$ksk['kskshopownerNo'],
                                'UserID'=>$this->session->userdata('username'),
                                'datecreated'=>date('Y-m-d h:i:s'),
                                'UserID2'=>$this->session->userdata('username'),
                                'dateupdatedat'=>date('Y-m-d h:i:s')
                            );
                            $createKskDetails = $this->VillageModel->insert_data($tablename="tblvillagekskdetails",$add_kskdetails);
                        }
                    }
                }
            }
            else
            {
                foreach ($allkskdetails as $missing) 
                {
                    $updatestatusksk = array(
                        'isActive'=>1,
                    );
                    $whksk = '(id="'.$missing['id'].'")';     
                    $updatestatksk = $this->VillageModel->edit_data($tablename="tblvillagekskdetails",$whksk,$updatestatusksk);
                }
            }

            if($kskname && $kskshopownername && $kskshopownerno)
            {
                $add_kskdetails_row = array(
                    'VillageDetailId'=>$VillageId,
                    'KskName'=>$kskname,
                    'KskShopOwnerName'=>$kskshopownername,
                    'KskShopOwnerNo'=>$kskshopownerno,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s'),
                    'UserID2'=>$this->session->userdata('username'),
                    'dateupdatedat'=>date('Y-m-d h:i:s')
               );
               $createKskDetailsrow = $this->VillageModel->insert_data($tablename="tblvillagekskdetails",$add_kskdetails_row);
            } 

            //update crop details     
            $whcrops = '(VillageDetailId="'.$VillageId.'")';  
            $allcropdetails = $this->VillageModel->get_all_data($tablename="tblvillagecropdetails",$whcrops);        
           
            if(!empty($cropDetails))
            {                    
                $missingcropData = [];  

                foreach ($allcropdetails as $dbcrop) 
                {                                   
                    $found = false;
                   
                    foreach (array_slice($cropDetails, 1) as $usercrop) 
                    {                          
                        $dbcropCrops = explode(',', $dbcrop['CropId']);         
                        $dbcropFertilizers = explode(',', $dbcrop['FertilizerId']);  
                        $dbcropSeeds = explode(',', $dbcrop['SeedId']);         
                        $dbcropPesticides = explode(',', $dbcrop['PesticideId']);


                        $usercropCrops = $usercrop['cropname'];                 
                        $usercropFertilizers = $usercrop['fertilizername'];     
                        $usercropSeeds = $usercrop['seedname'];                
                        $usercropPesticides = $usercrop['pesticidename'];    
                        
                        $usercropCropsArray = (array)$usercropCrops;

                        if(                                              
                            !empty(array_intersect($dbcropCrops, $usercropCropsArray)) &&
                            !empty(array_intersect($dbcropFertilizers, $usercropFertilizers)) &&
                            !empty(array_intersect($dbcropSeeds, $usercropSeeds)) &&
                            !empty(array_intersect($dbcropPesticides, $usercropPesticides))
                        ){                                
                            $found = true;
                            $missingcropData[] = $dbcrop;
                            break; 
                        }
                    }                                     
                }                        
                $missingCropIds = array_column($missingcropData, 'id');              
            
                if(!empty($missingcropData)) 
                {
                    foreach ($missingcropData as $missing) 
                    {
                        foreach ($allcropdetails as $dbcrop) 
                        {
                            if (in_array($dbcrop['id'], $missingCropIds)) 
                            {                              
                                $updatestatuscrop = array(
                                    'isActive'=>1,
                                );
                                $whcrop = '(id="'.$dbcrop['id'].'")';                              
                                $updatestatcrop = $this->VillageModel->edit_data($tablename="tblvillagecropdetails",$whcrop,$updatestatuscrop);
                            }
                            if(!in_array($dbcrop['id'], $missingCropIds))
                            {
                                $updatestatuscrop = array(
                                    'isActive'=>1,
                                );
                                $whcrop = '(id="'.$dbcrop['id'].'")';                              
                                $updatestatcrop = $this->VillageModel->edit_data($tablename="tblvillagecropdetails",$whcrop,$updatestatuscrop);
                            }
                        }
                    }
                }              
                
                foreach ($cropDetails as $crop) 
                {                     
                    if (!empty($crop['cropname']) && !empty($crop['fertilizername']) && !empty($crop['seedname']) && !empty($crop['pesticidename'])) 
                    {                                         
                        $cropIds = $crop['cropname'];
                        $fertilizerIds = implode(',', $crop['fertilizername']);
                        $seedIds = implode(',', $crop['seedname']);
                        $pesticideIds = implode(',', $crop['pesticidename']);                           

                        $cropsIds = $crop['cropname'];                                                                              
                        $fertilizersIds = $crop['fertilizername'];
                        $seedsIds = $crop['seedname'];
                        $pesticidesIds = $crop['pesticidename'];          
                        
                        //sort($cropsIds);
                        sort($fertilizersIds);
                        sort($seedsIds);
                        sort($pesticidesIds);                       

                        $cropConditions = 'CropId = "' . $cropsIds . '"';                       
                        $fertilizerConditions = 'FertilizerId = "' . implode(',', $fertilizersIds) . '"';
                        $seedConditions = 'SeedId = "' . implode(',', $seedsIds) . '"';
                        $pesticideConditions = 'PesticideId = "' . implode(',', $pesticidesIds) . '"';  
                        
                        $whereClause = '(VillageDetailId="' . $VillageId . '" AND ' .
                                        $cropConditions . ' AND ' .
                                        $fertilizerConditions . ' AND ' .
                                        $seedConditions . ' AND ' .
                                        $pesticideConditions . ')';                      
                      
                        $isexistcrop = $this->VillageModel->get_data($tablename="tblvillagecropdetails", $whereClause);
                        
                        if(empty($isexistcrop))
                        {                                                                            
                            $add_cropdetails = array(
                                'VillageDetailId'=>$VillageId,
                                'CropId'=>$cropIds,
                                'FertilizerId'=>$fertilizerIds,
                                'SeedId'=>$seedIds,
                                'PesticideId'=>$pesticideIds,
                                'isActive'=>0,
                                'UserID'=>$this->session->userdata('username'),
                                'datecreated'=>date('Y-m-d h:i:s'),
                                'UserID2'=>$this->session->userdata('username'),
                                'dateupdatedat'=>date('Y-m-d h:i:s')
                            );
                            $createcropDetails = $this->VillageModel->insert_data($tablename="tblvillagecropdetails",$add_cropdetails);
                        }
                        else
                        {                                                                                 
                            $updatedetails = array(                               
                                'CropId'=>$cropIds,
                                'FertilizerId'=>$fertilizerIds,
                                'SeedId'=>$seedIds,
                                'PesticideId'=>$pesticideIds,   
                                'isActive'=>0,                             
                                'UserID2'=>$this->session->userdata('username'),
                                'dateupdatedat'=>date('Y-m-d h:i:s')
                            );
                            $whupdate = '(id="'.$isexistcrop['id'].'")';  
                            $updatecropDetails = $this->VillageModel->edit_data($tablename="tblvillagecropdetails",$whupdate,$updatedetails);
                        }
                    }
                }                                    
            }
            else
            {                 
                foreach ($allcropdetails as $missing) 
                {
                    $updatestatuscrop = array(
                        'isActive'=>1,
                    );
                    $whcrop = '(id="'.$missing['id'].'")';     
                    $updatestatcrop = $this->VillageModel->edit_data($tablename="tblvillagecropdetails",$whcrop,$updatestatuscrop);
                }
            }          
           
            if (!empty($cropname) && 
            !empty($fertilizername) && 
            is_array($fertilizername) && count($fertilizername) > 0 && !in_array('', $fertilizername) &&
            !empty($seedname) && 
            is_array($seedname) && count($seedname) > 0 && !in_array('', $seedname) && 
            !empty($pesticidename) && 
            is_array($pesticidename) && count($pesticidename) > 0 && !in_array('', $pesticidename)) 
            {            
                $cropsIds = $cropname;                                                                           
                $fertilizersIds = $fertilizername;
                $seedsIds = $seedname;
                $pesticidesIds = $pesticidename;          
                
                //sort($cropsIds);
                sort($fertilizersIds);
                sort($seedsIds);
                sort($pesticidesIds);

                $cropConditions = 'CropId = "' . $cropsIds . '"';
                $fertilizerConditions = 'FertilizerId = "' . implode(',', $fertilizersIds) . '"';
                $seedConditions = 'SeedId = "' . implode(',', $seedsIds) . '"';
                $pesticideConditions = 'PesticideId = "' . implode(',', $pesticidesIds) . '"';           

                $whereClauses = '(VillageDetailId="' . $VillageId . '" AND ' .
                                $cropConditions . ' AND ' .
                                $fertilizerConditions . ' AND ' .
                                $seedConditions . ' AND ' .
                                $pesticideConditions . ')';                      
              
                $isexistcropdetails = $this->VillageModel->get_data($tablename="tblvillagecropdetails", $whereClauses);
         
                if(empty($isexistcropdetails))
                {
                    $update_cropdetails_row = array(
                        'VillageDetailId'=>$VillageId,
                        'CropId'=>$cropsIds,
                        'FertilizerId'=>implode(",", $fertilizersIds),
                        'SeedId'=>implode(",", $seedsIds),
                        'PesticideId'=>implode(",", $pesticidesIds),
                        'isActive'=>0,
                        'UserID'=>$this->session->userdata('username'),
                        'datecreated'=>date('Y-m-d h:i:s'),
                        'UserID2'=>$this->session->userdata('username'),
                        'dateupdatedat'=>date('Y-m-d h:i:s')
                    );               
                    $updatecropDetails_row = $this->VillageModel->insert_data($tablename="tblvillagecropdetails",$update_cropdetails_row);
                }
                else
                {                            
                    $updatedetails = array(                               
                        'CropId'=>$cropsIds,
                        'FertilizerId'=>$fertilizerIds,
                        'SeedId'=>$seedIds,
                        'PesticideId'=>$pesticideIds,   
                        'isActive'=>0,                             
                        'UserID2'=>$this->session->userdata('username'),
                        'dateupdatedat'=>date('Y-m-d h:i:s')
                    );
                    $whupdate = '(id="'.$isexistcropdetails['id'].'")';  
                    $updatecropDetails = $this->VillageModel->edit_data($tablename="tblvillagecropdetails",$whupdate,$updatedetails);
                }
            }

            //update vehicle details
            $whvehicle = '(VillageDetailId="'.$VillageId.'")';  
            $allvehicledetails = $this->VillageModel->get_all_data($tablename="tblvillagevehicledetails",$whvehicle);
            if($vehicleDetails)
            {               
                $missingvehicleData = [];  

                foreach ($allvehicledetails as $dbvehicle) 
                {                    
                    $found = false;

                    foreach ($vehicleDetails as $uservehicle) {
                        if ($dbvehicle['VehicleType'] === $uservehicle['vehicleType'] && $dbvehicle['RegsiterNo'] === $uservehicle['RegNo'] && $dbvehicle['capacity'] === $uservehicle['vehicleCapacity'] && $dbvehicle['DriverName'] === $uservehicle['Drivername'] && $dbvehicle['MobileNo'] === $uservehicle['driverNo'] && $dbvehicle['OwnerName'] === $uservehicle['ownerName'] && $dbvehicle['OwnerMobNo'] === $uservehicle['ownerNo']) {
                            $found = true;
                            break; 
                        }
                    }
                   
                    if (!$found) {
                        $missingvehicleData[] = $dbvehicle;
                    }
                }

                if (!empty($missingvehicleData)) {
                    foreach ($missingvehicleData as $missing) 
                    {
                        $updatestatusvehicle = array(
                            'isActive'=>1,
                        );
                        $whvehicle = '(id="'.$missing['id'].'")';     
                        $updatestatvehicle = $this->VillageModel->edit_data($tablename="tblvillagevehicledetails",$whvehicle,$updatestatusvehicle);
                    }
                }    

                foreach ($vehicleDetails as $vehicle) 
                {
                    if (!empty($vehicle['vehicleType']) && !empty($vehicle['RegNo']) && !empty($vehicle['vehicleCapacity']) && !empty($vehicle['Drivername']) && !empty($vehicle['driverNo']) && !empty($vehicle['ownerName']) && !empty($vehicle['ownerNo'])) 
                    {
                        $whexistvehicle = '(VillageDetailId="'.$VillageId.'" AND VehicleType="'.$vehicle['vehicleType'].'" AND RegsiterNo="'.$vehicle['RegNo'].'" AND capacity="'.$vehicle['vehicleCapacity'].'" AND DriverName="'.$vehicle['Drivername'].'" AND MobileNo="'.$vehicle['driverNo'].'" AND OwnerName="'.$vehicle['ownerName'].'" AND OwnerMobNo="'.$vehicle['ownerNo'].'")'; 
                        $isexistvehicle = $this->VillageModel->get_data($tablename="tblvillagevehicledetails",$whexistvehicle);

                        if(empty($isexistvehicle))
                        {
                            $add_vehicledetails = array(
                                'VillageDetailId'=>$VillageId,
                                'VehicleType'=>$vehicle['vehicleType'],
                                'RegsiterNo'=>$vehicle['RegNo'],
                                'capacity'=>$vehicle['vehicleCapacity'],
                                'DriverName'=>$vehicle['Drivername'],
                                'MobileNo'=>$vehicle['driverNo'],
                                'OwnerName'=>$vehicle['ownerName'],
                                'OwnerMobNo'=>$vehicle['ownerNo'],
                                'UserID'=>$this->session->userdata('username'),
                                'datecreated'=>date('Y-m-d h:i:s'),
                                'UserID2'=>$this->session->userdata('username'),
                                'dateupdatedat'=>date('Y-m-d h:i:s')
                            );
                            $vehicleDetails = $this->VillageModel->insert_data($tablename="tblvillagevehicledetails",$add_vehicledetails);
                        }
                    }
                }
            }
            else
            {
                foreach ($allvehicledetails as $missing) 
                {
                    $updatestatusvehicle = array(
                        'isActive'=>1,
                    );
                    $whvehicle = '(id="'.$missing['id'].'")';     
                    $updatestatvehicle = $this->VillageModel->edit_data($tablename="tblvillagevehicledetails",$whvehicle,$updatestatusvehicle);
                }
            }

            if($vehicle_type && $regno && $capacity && $drivername && $driverno && $vehownername && $vehownerno)
            {
                $add_vehicledetails_row = array(
                    'VillageDetailId'=>$VillageId,
                    'VehicleType'=>$vehicle_type,
                    'RegsiterNo'=>$regno,
                    'capacity'=>$capacity,
                    'DriverName'=>$drivername,
                    'MobileNo'=>$driverno,
                    'OwnerName'=>$vehownername,
                    'OwnerMobNo'=>$vehownerno,
                    'UserID'=>$this->session->userdata('username'),
                    'datecreated'=>date('Y-m-d h:i:s'),
                    'UserID2'=>$this->session->userdata('username'),
                    'dateupdatedat'=>date('Y-m-d h:i:s')
                );
                $vehicleDetails_row = $this->VillageModel->insert_data($tablename="tblvillagevehicledetails",$add_vehicledetails_row);
            }                   
            echo json_encode(['success' => true]);            
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update details']);
        }
    }

    public function GetStateTalukaDetailByID()
    {
        $pincodeId = $this->input->post('pincodeId');
        $where = '(id="'.$pincodeId.'")';
        $pincodeDetails = $this->VillageModel->get_data($tablename="tblpin",$where);
        $data['pincodeDetails'] = $pincodeDetails;

        $whstate = '(state_name="'.$pincodeDetails['State'].'")';
        $pincodestate = $this->VillageModel->get_data($tablename="tblxx_statelist",$whstate);

        $whcity = '(state_id="'.$pincodestate['short_name'].'")';
        $pincodecity = $this->VillageModel->get_all_data($tablename="tblxx_citylist",$whcity);
        foreach($pincodecity as &$val)
        {
            $whtaluka = '(DistrictID="'.$val['id'].'")';
            $pincodewisetaluka = $this->VillageModel->get_all_data($tablename="tblTalukaMaster",$whtaluka);
            $val['pincodewisetaluka'] = $pincodewisetaluka;
        }
        $data['pincodecity'] = $pincodecity;       

        $statedetails = $this->VillageModel->get_all_table_data($tablename="tblxx_statelist");
        $data['statedetails'] = $statedetails;

        $citydetails =  $this->VillageModel->get_all_table_data($tablename="tblxx_citylist");
        $data['citydetails'] = $citydetails;

        $talukadetails = $this->VillageModel->get_all_table_data($tablename="tblTalukaMaster");
        $data['talukadetails'] = $talukadetails; 
        echo json_encode($data);   
    }
    
    public function GetDetailsFromPincode()
    {
        $pincodeId = $this->input->post('pincodeId');
        $where = '(Pincode="'.$pincodeId.'")';
        $pincodeDetails = $this->VillageModel->get_data($tablename="tblpin",$where);
        
        $whstate = '(short_name="'.$pincodeDetails['State'].'")';
        $Statename = $this->VillageModel->get_data($tablename="tblxx_statelist",$whstate);        

        $whcity = '(id="'.$pincodeDetails['District'].'")';
        $Cityname = $this->VillageModel->get_data($tablename="tblxx_citylist",$whcity);

        $whtaluka = '(id="'.$pincodeDetails['Taluka'].'")';
        $Talukaname = $this->VillageModel->get_data($tablename="tblTalukaMaster",$whtaluka);

        $data['pincodeDetails'] = $pincodeDetails;
        $data['Statename'] = $Statename;
        $data['Cityname'] = $Cityname;
        $data['Talukaname'] = $Talukaname;
        echo json_encode($data);   
    }
	
// ============== Village Report Start ===================

    public function VillageReport()
    {   
        if (!has_permission_new('VillageReport', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['StaffList'] = $this->VillageModel->GetVillageAddedStaffList();
		$data['ReprStaff'] = $this->VillageModel->GetVillageAddedReprStaffList();
        $data['CityList'] = $this->VillageModel->GetVillageAddedCityList();
        $data['company_detail'] = $this->VillageModel->get_company_detail();
        $this->load->view('admin/VillageMaster/VillageMaster_Report',$data);
    }

    public function load_village_data()
    {
        if (!has_permission_new('VillageReport', '', 'view')) {
            access_denied('Invoice Items');
        }
        $post_data = $this->input->post();        
        $data = $this->VillageModel->get_table_on_load_filter($post_data);      
       
        $where_dis = '(id="'.$post_data['Account_district'].'")';
        $districtlist = $this->VillageModel->get_data($talename="tblxx_citylist",$where_dis);   
        $cityname= $districtlist['city_name'];
        
        $wh_staff = '(AccountID="'.$post_data['Staff_Id'].'")';
        $staff = $this->VillageModel->get_data($talename="tblstaff",$wh_staff); 
        $staffname= $staff['firstname'].$staff['lastname'];      

        $filters_output = '';        
        if (!empty($post_data['Account_district']) && !empty($post_data['Staff_Id'])) {            
            $filters_output .= 'District: ' . htmlspecialchars($cityname). ' | ';
            $filters_output .= 'Staff ID: ' . htmlspecialchars($staffname). ' | ';
            $filters_output .= 'From Date:' . htmlspecialchars($post_data['from_date']). ' | ';
            $filters_output .= 'To Date:' . htmlspecialchars($post_data['to_date']);
        } else {            
            if (!empty($post_data['Account_district'])) {
                $filters_output .= 'District: ' . htmlspecialchars($cityname) . ' | ';
                $filters_output .= 'From Date: ' . htmlspecialchars($post_data['from_date']) . ' | ';
                $filters_output .= 'To Date: ' . htmlspecialchars($post_data['to_date']);
            } elseif (!empty($post_data['Staff_Id'])) {
                $filters_output .= 'Staff ID: ' . htmlspecialchars($staffname). ' | ';
                $filters_output .= 'From Date: ' . htmlspecialchars($post_data['from_date']) . ' | ';
                $filters_output .= 'To Date: ' . htmlspecialchars($post_data['to_date']);
            } else {              
                $filters_output .= 'From Date: ' . htmlspecialchars($post_data['from_date']) . ' | ';
                $filters_output .= 'To Date: ' . htmlspecialchars($post_data['to_date']);
            }
        }          
       
        $html = '';

        $html .= '<div class="col-md-12 ">';
        $html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
        $html .= '<thead>';
        $html .= '<tr style="display:none;">';
        $html .= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">' . $company_detail->company_name . '</span><br><span style="font-size:10px;font-weight:600;">' . $company_detail->address . '</span><br><span style="font-size:10px;font-weight:600;">Village Details Report</span></h5></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th >Sr.No </th>';             
        $html .= '<th>Village Name</th>';
        $html .= '<th>TotalFilled Survey</th>'; 
        $html .= '<th>State</th>';
        $html .= '<th>District</th>';
        $html .= '<th>Taluka</th>';
        $html .= '<th>Pincode</th>';
        $html .= '<th>Village Sarpanch</th>';       
        $html .= '<th>Visit Date</th>'; 
        $html .= '<th>Created By </th>'; 
		$html .= '<th>Staff Representative </th>';
		$html .= '<th>Action </th>';		
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '</tbody>';

        $i = 1;
        foreach ($data as $value) 
        {
            $filledvillagedata = 0;
            $whaggre = '(VillageDetailId="'.$value['id'].'"  AND VillageAggregatorName != "" AND AggregatorMobNo != "")';
            $aggredetail = $this->VillageModel->get_data($tablename="tblvillageaggregatordetails",$whaggre); 
            
            $whksk = '(VillageDetailId="'.$value['id'].'" AND KskName != "" AND KskShopOwnerName != "" AND KskShopOwnerNo != "")';
            $kskdetail = $this->VillageModel->get_data($tablename="tblvillagekskdetails",$whksk); 
            
            $whcrop = '(VillageDetailId="'.$value['id'].'" AND CropId != "" AND FertilizerId != "" AND SeedId != "" AND PesticideId != "")';
            $cropdetail =  $this->VillageModel->get_data($tablename="tblvillagecropdetails",$whcrop); 
            
            $whvehicle = '(VillageDetailId="'.$value['id'].'" AND VehicleType != "" AND RegsiterNo != "" AND capacity != "" AND DriverName != "" AND MobileNo != "" AND OwnerName != "" AND OwnerMobNo != "")';
            $vehicle = $this->VillageModel->get_data($tablename="tblvillagevehicledetails",$whvehicle); 
            
            $whhotel = '(VillageDetailId="'.$value['id'].'" AND HotelName != "" AND OwnerName != "" AND OwnerMobileNo != "")';
            $hotel = $this->VillageModel->get_data($tablename="tblVillageHotelDetails",$whhotel); 
            
            if(!empty($value['VisitDate']))
            {  $filledvillagedata++; }
            if(!empty($value['VillageName']))
            {  $filledvillagedata++; }  
            if(!empty($value['Pincode']))
            {  $filledvillagedata++; }  
            if(!empty($value['TalukaId']))
            {  $filledvillagedata++; } 
            if(!empty($value['DistrictId']))
            {  $filledvillagedata++; } 
            if(!empty($value['StateId']))
            {  $filledvillagedata++; } 
            if(!empty($value['VillageSarpanch']))
            {  $filledvillagedata++; } 
            if(!empty($value['VillagePopulation']))
            {  $filledvillagedata++; } 
            if(!empty($value['Area']))
            {  $filledvillagedata++; } 
            if(!empty($value['InfluencerName']))
            {  $filledvillagedata++; } 
            if(!empty($value['InfluencerGovtPost']))
            {  $filledvillagedata++; } 
            if(!empty($value['Influencer_MobNo']))
            {  $filledvillagedata++; } 
            if(!empty($value['NoRtrsFarmers']))
            {  $filledvillagedata++; } 
            if(!empty($value['OtherInformation']))
            {  $filledvillagedata++; }
            
            //aggregator
            if(!empty($aggredetail['VillageAggregatorName']))
            {  $filledvillagedata++; }
            if(!empty($aggredetail['AggregatorMobNo']))
            {  $filledvillagedata++; }
            
            //ksk
            if(!empty($kskdetail['KskName']))
            {  $filledvillagedata++; }
            if(!empty($kskdetail['KskShopOwnerName']))
            {  $filledvillagedata++; }
            if(!empty($kskdetail['KskShopOwnerNo']))
            {  $filledvillagedata++; }
            
            //crop
            if(!empty($cropdetail['CropId']))
            {  $filledvillagedata++; }
            if(!empty($cropdetail['FertilizerId']))
            {  $filledvillagedata++; }
            if(!empty($cropdetail['SeedId']))
            {  $filledvillagedata++; }
            if(!empty($cropdetail['PesticideId']))
            {  $filledvillagedata++; }
            
            //vehicle
            if(!empty($vehicle['VehicleType']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['RegsiterNo']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['capacity']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['DriverName']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['MobileNo']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['OwnerName']))
            {  $filledvillagedata++; }
            if(!empty($vehicle['OwnerMobNo']))
            {  $filledvillagedata++; }
            
            //hotel
            if(!empty($hotel['HotelName']))
            {  $filledvillagedata++; }
            if(!empty($hotel['OwnerName']))
            {  $filledvillagedata++; }
            if(!empty($hotel['OwnerMobileNo']))
            {  $filledvillagedata++; }
            
            $value['filcount'] = $filledvillagedata;
            $maincount = 33;
            $totalVillagefilledpercent = number_format(($value['filcount'] / $maincount) * 100, 2);
            
            $date = new DateTime($value["VisitDate"]);
            $formattedDate = $date->format('d/m/Y');
            $redirectUrl = admin_url()."VillageMaster/AddEditVillage";

            $staffName = $value['firstname'].$value['lastname']; 
			$assignName = $value['assignee_firstname'].$value['assignee_lastname'];		
			
			$formId = 'villageForm' . $i;
            $id = htmlspecialchars($value["id"]);
			
            $html .= '<tr style="cursor:pointer;" onclick="document.getElementById(\'' . $formId . '\').submit();">';
            $html .= '<td>' . $i . '</td>';            
            $html .= '<td>';
            $html .= '<form action="' . $redirectUrl . '" method="POST" id="' . $formId . '" target="_blank">';
            $html .= '<input type="hidden" name="id" value="' . $id . '">';
            $html .= '<a href="javascript:void(0);" style="color: black; text-decoration: none;">' . htmlspecialchars($value["VillageName"]) . '</a>';
            $html .= '</form>';
            $html .= '</td>';
            $html .= '<td>
                        <div class="progress-bar-container" style="width:200px; height:12px; background:#e0e0e0; border-radius:12px; position:relative; margin:0 auto; overflow:hidden;">
                            <div class="progress-bar-fill" style="width:' . $totalVillagefilledpercent . '%; height: 100%; background-color: lightgreen; border-radius: 12px 0 0 12px; transition: width 0.4s ease-in-out;"></div>
                            <span class="progress-label" style="position:absolute; left:50%; top:50%; transform: translate(-50%, -50%); font-weight:bold; color:black; font-size:12px; pointer-events:none;">
                                ' . $totalVillagefilledpercent . '%
                            </span>
                        </div>
                    </td>';
            $html .= '<td>' . $value['state_name'] . '</td>';            
            $html .= '<td>' . $value['city_name'] . '</td>';
            $html .= '<td>' . $value['TalukaName'] . '</td>';
            $html .= '<td>' . $value['Pincode'] . '</td>';
            $html .= '<td>' . $value["VillageSarpanch"] . '</td>';   
            $html .= '<td>' . $formattedDate . '</td>';   
            $html .= '<td>' . $staffName . '</td>'; 
            $html .= '<td>' . $assignName . '</td>';
			$html .= '<td>
    <button
        class="btn btn-info btn-sm"
        onclick="myCustomFunction(' . $value["id"] . ')"
        data-toggle="modal"
        data-target="#viewVillageModal">
        Assing Staff
    </button>
</td>';			
            $html .= '</tr>';
            $i++;
        }
        $html .= '</tbody>';

        $html .= '</table>';
        $html .= '</div>';
		
        //amother table
        $html2 = '';

        $html2 .= '<div class="col-md-12 report_for">';
        $html2 .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead21" id="table-daily_report2" width="100%">';
        $html2 .= '<thead>';
        $html2 .= '<tr style="display:none;">';
        $html2 .= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">' . $company_detail->company_name . '</span><br><span style="font-size:10px;font-weight:600;">' . $company_detail->address . '</span><br><span style="font-size:10px;font-weight:600;">Village Details Report</span><br><span class="report_for" style="font-size:10px;">'. $filters_output . '</span></h5></td>';
       
        //$html .= '<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">' . $company_detail->company_name . '</span><br><span style="font-size:10px;font-weight:600;">' . $company_detail->address . '</span><br><span style="font-size:10px;font-weight:600;">Customers Master</span><br><span class="report_for" style="font-size:10px;">Filters Distributor Type:' . $client_type . ', Distributor State:' . $distributor_state . ',Division:' . $division . ',  Responsible Person:' . $responsible_admin . ',  Status:' . $status . '</span></h5></td>';

        $html2 .= '</tr>';
        $html2 .= '<tr>';
        $html2 .= '<th >Sr.No </th>';             
        $html2 .= '<th>Village Name</th>';      
        $html2 .= '<th>State</th>';
        $html2 .= '<th>District</th>';
        $html2 .= '<th>Taluka</th>';
        $html2 .= '<th>Pincode</th>';
        $html2 .= '<th>Village Sarpanch</th>';       
        $html2 .= '<th>Visit Date</th>'; 
        $html2 .= '<th>Staff Name </th>';    
        $html2 .= '<th>Village Population </th>';  
        $html2 .= '<th>Area </th>'; 
        $html2 .= '<th>Influencer Name </th>'; 
        $html2 .= '<th>Govt Post </th>';    
        $html2 .= '<th>Mobile No</th>';          
        $html2 .= '</tr>';
        $html2 .= '</thead>';
        $html2 .= '<tbody>';
        $html2 .= '</tbody>';

        $i = 1;
        foreach ($data as $value) 
        {
            $date = new DateTime($value["VisitDate"]);
            $formattedDate = $date->format('d/m/Y');
            $redirectUrl = admin_url()."VillageMaster/AddEditVillage";

            $staffName = $value['firstname'].$value['lastname'];

            $html2 .= '<tr>';
            $html2 .= '<td>' . $i . '</td>';            
            $html2 .= '<td>';
            $html2 .= '<form action="' . $redirectUrl . '" method="POST" id="villageForm' . $i . '" target="_blank">'; 
            $html2 .= '<input type="hidden" name="id" value="' . htmlspecialchars($value["id"]) . '">'; 
            $html2 .= '<a href="javascript:void(0);" onclick="document.getElementById(\'villageForm' . $i . '\').submit();" style="color: black; text-decoration: none;">' . $value["VillageName"] . '</a>';
            $html2 .= '</form>';
            $html2 .= '</td>';
            $html2 .= '<td>' . $value['state_name'] . '</td>';            
            $html2 .= '<td>' . $value['city_name'] . '</td>';
            $html2 .= '<td>' . $value['TalukaName'] . '</td>';
            $html2 .= '<td>' . $value['Pincode'] . '</td>';
            $html2 .= '<td>' . $value["VillageSarpanch"] . '</td>';   
            $html2 .= '<td>' . $formattedDate . '</td>';   
            $html2 .= '<td>' . $staffName . '</td>';   
            $html2 .= '<td>' . $value['VillagePopulation'] . '</td>';  
            $html2 .= '<td>' . $value['Area'] . '</td>'; 
            $html2 .= '<td>' . $value['InfluencerName'] . '</td>';    
            $html2 .= '<td>' . $value['InfluencerGovtPost'] . '</td>';  
            $html2 .= '<td>' . $value['Influencer_MobNo'] . '</td>';                                  
            $html2 .= '</tr>';
            $i++;
        }
        $html2 .= '</tbody>';

        $html2 .= '</table>';
        $html2 .= '</div>';
       

        $response = array('html' => $html,'html2' => $html2);
        echo json_encode($response);
    }  

   public function export_villageDetailslist()
   {
        if (!has_permission_new('VillageReport', '', 'export')) {
            access_denied('Invoice Items');
        }

        if (!class_exists('XLSXReader_fin')) {
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

        if ($this->input->post()) 
        {
            $company_detail = $this->VillageModel->get_company_detail();
            $post_data = $this->input->post();
            $result = $this->VillageModel->get_table_on_load_filter($post_data);
            
            $max_count = 0;
            $maxkskcount= 0;
            $maxcropcount = 0;
            $vehiclecount = 0;
            $maxhotelcount = 0;
            foreach ($result as $row) {
            if ((int)$row['aggregator_count'] > $max_count) {
                $max_count = (int)$row['aggregator_count'];
            }
            if ((int)$row['ksk_count'] > $maxkskcount) {
                $maxkskcount = (int)$row['ksk_count'];
            }
            if ((int)$row['crop_count'] > $maxcropcount) {
                $maxcropcount = (int)$row['crop_count'];
            }
            if ((int)$row['vehicle_count'] > $vehiclecount) {
                $vehiclecount = (int)$row['vehicle_count'];
            }
            if ((int)$row['hotel_count'] > $maxhotelcount) {
                $maxhotelcount = (int)$row['hotel_count'];
            }
        }
    
            $writer = new XLSXWriter();
    
            // Add company name and address headers
            $writer->markMergedCell('Sheet1', 0, 0, 0, 10);
            $writer->writeSheetRow('Sheet1', [$company_detail->company_name]);
    
            $writer->markMergedCell('Sheet1', 1, 0, 1, 10);
            $writer->writeSheetRow('Sheet1', [$company_detail->address]);
            
            $filters = [];
            if (!empty($post_data['from_date']) && !empty($post_data['to_date'])) {
                $filters[] = 'Date Range: ' . $post_data['from_date'] . ' to ' . $post_data['to_date'];
            }
            if (!empty($post_data['Account_district'])) {
                $filters[] = 'District Name: ' . $post_data['Account_district_text']; 
            }
            if (!empty($post_data['Account_taluka'])) {
                $filters[] = 'Taluka Name: ' . $post_data['Account_taluka_text']; // optionally replace ID with name
            }
            if (!empty($post_data['Staff_Id'])) {
                $filters[] = 'Created By Staff Name: ' . $post_data['Staff_Id_text'];
            }
            if (!empty($post_data['Repr_Staff'])) {
                $filters[] = 'Representative Staff Name: ' . $post_data['Repr_Staff_text'];
            }
            
            $writer->markMergedCell('Sheet1', 2, 0, 2, 10);
            $writer->writeSheetRow('Sheet1', [implode(' , ', $filters)]);
    
            // Column headers
            $set_col_tk = [
                "VillageName" => 'Village Name',
                "state_name" => 'State',
                "city_name" => 'District',
                "TalukaName" => 'Taluka',
                "Pincode" => 'Pincode',
                "VillageSarpanch" => 'Village Sarpanch',
                "VisitDate" => 'Visit Date',
                "UserID" => 'Created By',
                "AssignStaff" => 'Staff Representative',
            ];
            
            for ($i = 1; $i <= $max_count; $i++) {
                $set_col_tk["VillageAggregatorName_$i"] = 'Village Aggregator Name' . $i;
                $set_col_tk["AggregatorMobNo_$i"] = 'Village Aggregator Mobile No' . $i;
            }
        
            for ($j = 1; $j <= $maxkskcount; $j++) {
                $set_col_tk["KskName_$j"] = 'Krushi Seva Kendra Name' . $j;
                $set_col_tk["KskShopOwnerName_$j"] = 'KSK Shop Owner Name' . $j;
                $set_col_tk["KskShopOwnerNo_$j"] = 'KSK Shop Owner No' . $j;
            }
        
            for ($k = 1; $k <= $maxcropcount; $k++) {
                $set_col_tk["CropName_$k"] = 'Major Crops' . $k;
                $set_col_tk["fertilizerName_$k"] = 'Fertilizer Brands' . $k;
                $set_col_tk["SeedName_$k"] = 'Seed Brands' . $k;
                $set_col_tk["PesticideName_$k"] = 'Pesticide Brands' . $k;
            }
        
            for ($l = 1; $l <= $vehiclecount; $l++) {
                $set_col_tk["VehicleType_$l"] = 'Vehicle Type' . $l;
                $set_col_tk["RegsiterNo_$l"] = 'Vehicle Registration No' . $l;
                $set_col_tk["capacity_$l"] = 'Vehicle Capacity(Qtls)' . $l;
                $set_col_tk["DriverName_$l"] = 'Vehicle Driver Name' . $l;
                $set_col_tk["MobileNo_$l"] = 'Driver Mobile No' . $l;
                $set_col_tk["OwnerName_$l"] = 'Owner Name' . $l;
                $set_col_tk["OwnerMobNo_$l"] = 'Owner Mobile No' . $l;
            }
        
            for ($m = 1; $m <= $maxhotelcount; $m++) {
                $set_col_tk["HotelName_$m"] = 'Village Hotel Name' . $m;
                $set_col_tk["Hotownername_$m"] = 'Hotel Owner Name' . $m;
                $set_col_tk["hotmobileno_$m"] = 'Hotel Owner Mobile No' . $m;
            }
            $writer->writeSheetRow('Sheet1', array_values($set_col_tk));
    
            // Write data rows
            foreach ($result as $value) {
                $staffName = $value['firstname'] . ' ' . $value['lastname'];
                $assignName = $value['assignee_firstname'] . ' ' . $value['assignee_lastname'];
    
                $list_add = [
                    $value["VillageName"],
                    $value["state_name"],
                    $value["city_name"],
                    $value["TalukaName"],
                    $value["Pincode"],
                    $value["VillageSarpanch"],
                    date('d/m/Y', strtotime($value["VisitDate"])),
                    $staffName,
                    $assignName
                ];
                
                $aggregators = isset($value['aggregators']) ? $value['aggregators'] : [];
                $names = [];
                $mobiles = [];

                foreach ($aggregators as $agg) {
                    $names[] = $agg['VillageAggregatorName'];
                    $mobiles[] = $agg['AggregatorMobNo'];
                }
                
                $ksks = isset($value['ksk']) ? $value['ksk'] : [];
                $kaksnames=[];
                $kskshopownername = [];
                $kskshopownerno = [];
            
                foreach($ksks as $kskdetail)
                {
                    $kaksnames[] = $kskdetail['KskName'];
                    $kskshopownername[] = $kskdetail['KskShopOwnerName'];
                    $kskshopownerno[] = $kskdetail['KskShopOwnerNo'];
                }
            
                $crops = isset($value['crops']) ? $value['crops'] : [];
                $majorcrops=[];
                $fertilizerbrand=[];
                $seedbrand=[];
                $pesticidebrand=[];
            
                foreach($crops as $cropdetail)
                {
                    $majorcrops[] = $cropdetail['CropName'];
                    $fertilizerbrand[] = $cropdetail['fertilizerName'];
                    $seedbrand[] = $cropdetail['SeedName'];
                    $pesticidebrand[] = $cropdetail['PesticideName'];
                }
                
                $vehiclesdetail = isset($value['vehicles']) ? $value['vehicles'] : [];
                $vehicletype=[];
                $vehicleregno=[];
                $vehiclecapacity=[];
                $vehicledrivername=[];
                $drivermobno=[];
                $ownername=[];
                $ownermobileno = [];
            
                foreach($vehiclesdetail as $vehicle)
                {
                    $vehicletype[] = $vehicle['VehicleType'];
                    $vehicleregno[] = $vehicle['RegsiterNo'];
                    $vehiclecapacity[] = $vehicle['capacity'];
                    $vehicledrivername[] = $vehicle['DriverName'];
                    $drivermobno[] = $vehicle['MobileNo'];;
                    $ownername[]= $vehicle['OwnerName'];;
                    $ownermobileno[] = $vehicle['OwnerMobNo'];;
                }
            
                $hoteldetail = isset($value['hoteldetail']) ? $value['hoteldetail'] : [];
                $hotelname=[];
                $hotelownername=[];
                $hotelownermobileno=[];
                
                foreach($hoteldetail as $hotel)
                {
                    $hotelname[] = $hotel['HotelName'];
                    $hotelownername[] = $hotel['Hotownername'];
                    $hotelownermobileno[] = $hotel['hotmobileno'];
                }

                for ($i = 0; $i < $max_count; $i++) {
                    $list_add[] = isset($names[$i]) ? trim($names[$i]) : '';
                    $list_add[] = isset($mobiles[$i]) ? trim($mobiles[$i]) : '';
                }
                
                for ($j = 0; $j < $maxkskcount; $j++) {
                    $list_add[] = isset($kaksnames[$j]) ? trim($kaksnames[$j]) : '';
                    $list_add[] = isset($kskshopownername[$j]) ? trim($kskshopownername[$j]) : '';
                    $list_add[] = isset($kskshopownerno[$j]) ? trim($kskshopownerno[$j]) : '';
                }
                
                for ($k = 0; $k < $maxcropcount; $k++) {
                    $list_add[] = isset($majorcrops[$k]) ? trim($majorcrops[$k]) : '';
                    $list_add[] = isset($fertilizerbrand[$k]) ? trim($fertilizerbrand[$k]) : '';
                    $list_add[] = isset($seedbrand[$k]) ? trim($seedbrand[$k]) : '';
                    $list_add[] = isset($pesticidebrand[$k]) ? trim($pesticidebrand[$k]) : '';
                }
            
                for ($l = 0; $l < $vehiclecount; $l++) {
                      $list_add[] = isset($vehicletype[$l]) ? trim($vehicletype[$l]) : '';
                      $list_add[] = isset($vehicleregno[$l]) ? trim($vehicleregno[$l]) : '';
                      $list_add[] = isset($vehiclecapacity[$l]) ? trim($vehiclecapacity[$l]) : '';
                      $list_add[] = isset($vehicledrivername[$l]) ? trim($vehicledrivername[$l]) : '';
                      $list_add[] = isset($drivermobno[$l]) ? trim($drivermobno[$l]) : '';
                      $list_add[] = isset($ownername[$l]) ? trim($ownername[$l]) : '';
                      $list_add[] = isset($ownermobileno[$l]) ? trim($ownermobileno[$l]) : '';
                }
            
                for ($m = 0; $m < $maxhotelcount; $m++) {
                    $list_add[] = isset($hotelname[$m]) ? trim($hotelname[$m]) : '';
                    $list_add[] = isset($hotelownername[$m]) ? trim($hotelownername[$m]) : '';
                    $list_add[] = isset($hotelownermobileno[$m]) ? trim($hotelownermobileno[$m]) : '';
                }
                $writer->writeSheetRow('Sheet1', $list_add);
            }
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
        		foreach($files as $file){
        			if(is_file($file)) {
        				unlink($file); 
        			}
        		}
        		$filename = 'VillageReportList.xlsx';
        		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
        		echo json_encode([
        			'site_url'          => site_url(),
        			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
        		]);
        		die;
    
            // // Define export path
            // $exportPath = FCPATH . 'uploads/exports/';
            // if (!is_dir($exportPath)) {
                // mkdir($exportPath, 0777, true);
            // }
    
            // // Clean up old files (optional)
            // $files = glob($exportPath . '*');
            // foreach ($files as $file) {
                // if (is_file($file)) {
                    // unlink($file);
                // }
            // }
    
            // // Write to file
            // $filename = 'Customerlist.xlsx';
            // $filePath = $exportPath . $filename;
    
            // $writer->writeToFile($filePath);
    
            // // Respond with download link
            // echo json_encode([
                // 'site_url' => site_url(),
                // 'filename' => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            // ]);
            // die;
        }
    }
public function getTalukas()
{
    $district_id = $this->input->post('district_id');
    $talukas = $this->VillageModel->getTalukasByDistrict($district_id);

    echo json_encode($talukas);
}
	
   public function get_village_details()
    {
        $id = $this->input->post('id');
        $village = $this->VillageModel->get_village_by_id($id);
        if ($village) {
            echo json_encode($village); // Return village data
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Village not found']);
        }
    }
	
	public function get_all_staff()
    {
        $staff = $this->VillageModel->get_all_staff();
        echo json_encode($staff); // Return staff data
    }
	
	public function assign_staff()
    {
    $village_id = $this->input->post('village_id');
    $staff_id = $this->input->post('staff_id');
    $result = $this->VillageModel->assign_staff_to_village($village_id, $staff_id);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    } 

//========================= Village Report End ===================
 
//========================= Village Report Chart Start =================

     public function VillageChart()
	 {
		if (!has_permission_new('VillageChart', '', 'view')) {
		    access_denied('Invoice Items');
		}
		$data['StaffList'] = $this->VillageModel->GetVillageAddedStaffList();
		$data['ReprStaff'] = $this->VillageModel->GetVillageAddedReprStaffList();
        $data['CityList'] = $this->VillageModel->GetVillageAddedCityList();
		$data['title'] = "Village Report Chart";
		$this->load->view('admin/VillageMaster/VillageReportCharts', $data);
	 }
	 
	 public function village_wise_chart()
	 {
	    if (!has_permission_new('VillageChart', '', 'view')) {
		    access_denied('Invoice Items');
		}
		$filter_data = array(
		    "from_date"=>$this->input->post('from_date'),
		    "to_date"=>$this->input->post('to_date'),
		    "District"=>$this->input->post('District'),
		    "Taluka"=>$this->input->post('Taluka'),
		    "ReportFor"=>$this->input->post('ReportFor'),
		    "Staff_Id"=>$this->input->post('Staff_Id'),
		    "GroupBy"=>$this->input->post('GroupBy'),
		    "ChartType"=>$this->input->post('ChartType')
		);
		$result = $this->VillageModel->village_wise_chart($filter_data);
		$data = [
			'ChartData' => $result['ChartData'],
		];
		echo json_encode($data);
	 }

}
