<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SeedMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SeedModel');              
    }

    public function AddEditSeed()
    {    
        if (!has_permission_new('SeedsMaster', '', 'view')) {
            access_denied('BrandMaster');
        }
        $Brands =  $this->SeedModel->get_all_table_data($tablename="tblbrands");        
        $data['Brands'] = $Brands;

        $seeds =  $this->SeedModel->get_all_table_data($tablename="tblseed");
        foreach($seeds as &$seed)
        {
            $where = '(id="'.$seed['BrandId'].'")';
            $brandname = $this->SeedModel->get_data($tablename="tblbrands",$where);
            $seed['brandname'] = $brandname['BrandName'];
        }        
        $data['seeds'] = $seeds;

        $maxseedId = $this->SeedModel->get_max_seed_id();
        $data['maxseedId'] = $maxseedId;

        $this->load->view('admin/SeedMaster/AddEditSeed',$data);
    }

    public function insertSeedDetails()
    {
        if (!has_permission_new('SeedsMaster', '', 'create')) {
            access_denied('BrandMaster');
        }
        $SeedName = $this->input->post('SeedName');
        $Brandname = $this->input->post('Brandname');

        $insert_seeddetails = array(           
            'SeedName'=>$SeedName,   
            'BrandId'=>$Brandname,      
        );
        $createnewseed =   $this->SeedModel->insert_data($tablename="tblseed",$insert_seeddetails);
        if ($createnewseed) {   
            $newSeedId = $this->db->insert_id();            
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','newMaxSeedId' => $newSeedId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetSeedDetailsbyID()
    {
        $SeedId = $this->input->post('SeedId');
        $where = '(id="'.$SeedId.'")'; 
        $seeddetails = $this->SeedModel->get_data($tablename="tblseed",$where);
        echo json_encode($seeddetails);
    }

    public function UpdateSeedDetails()
    {
        if (!has_permission_new('SeedsMaster', '', 'edit')) {
            access_denied('BrandMaster');
        }
        $SeedId = $this->input->post('SeedId');
        $SeedName = $this->input->post('SeedName');
        $BrandId = $this->input->post('BrandId');

        $update_seed = array(
            'SeedName'=>$SeedName,   
            'BrandId'=>$BrandId
        );
        $where = '(id="'.$SeedId.'")'; 
        $updateseed = $this->SeedModel->edit_data($tablename="tblseed",$where,$update_seed);
        if($updateseed)
        {
            $seeds =  $this->SeedModel->get_all_table_data($tablename="tblseed");    
            echo json_encode(['success' => true,'message' => 'Data updated successfully','Seeds' => $seeds]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function Seed_table_data()
    {
        $seeds =  $this->SeedModel->get_all_table_data($tablename="tblseed");  
        foreach($seeds as &$seed)
        {
            $where = '(id="'.$seed['BrandId'].'")';
            $brandname = $this->SeedModel->get_data($tablename="tblbrands",$where);
            $seed['brandname'] = $brandname['BrandName'];
        }     
        echo json_encode($seeds);
    }
}