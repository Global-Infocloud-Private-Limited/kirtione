<?php

defined('BASEPATH') or exit('No direct script access allowed');

class BrandMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BrandModel');      
        
    }

    public function AddEditBrand()
    {
        if (!has_permission_new('BrandMaster', '', 'view')) {
            access_denied('BrandMaster');
        }
        $Brands =  $this->BrandModel->get_all_table_data($tablename="tblbrands");
        $data['Brands'] = $Brands;

        $maxBrandId = $this->BrandModel->get_max_brand_id();
        $data['maxBrandId'] = $maxBrandId;
        $this->load->view('admin/BrandMaster/AddEditBrand',$data);
    }

    public function insertBrandDetails()
    {
        if (!has_permission_new('BrandMaster', '', 'create')) {
            access_denied('BrandMaster');
        }
        $BrandName = $this->input->post('BrandName');

        $insert_brand = array(
            'BrandName'=>$BrandName,      
        );
        $createnewbrand = $this->BrandModel->insert_data($tablename="tblbrands",$insert_brand);
        if ($createnewbrand) {    
            $newBrandId = $this->db->insert_id();       
            echo json_encode(['success' => true,'message' => 'Data inserted successfully', 'newMaxBrandId' => $newBrandId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetBrandDetailsbyID()
    {
        $BrandId = $this->input->post('BrandId');
        $where = '(id="'.$BrandId.'")'; 
        $Branddetails = $this->BrandModel->get_data($tablename="tblbrands",$where);
        echo json_encode($Branddetails);
    }

    public function UpdateBrandDetails()
    {
        if (!has_permission_new('BrandMaster', '', 'edit')) {
            access_denied('BrandMaster');
        }
        $brandid = $this->input->post('brandid');
        $brandname = $this->input->post('brandname');

        $update_brand = array(
            'BrandName'=>$brandname,      
        );
        $where = '(id="'.$brandid.'")'; 
        $updatebrand = $this->BrandModel->edit_data($tablename="tblbrands",$where,$update_brand);
        if($updatebrand)
        {
            $Brands =  $this->BrandModel->get_all_table_data($tablename="tblbrands");            

            echo json_encode(['success' => true,'message' => 'Data updated successfully','brands' => $Brands]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function Brand_table_data()
    {
        $Brands =  $this->BrandModel->get_all_table_data($tablename="tblbrands");
        echo json_encode($Brands);
    }
}