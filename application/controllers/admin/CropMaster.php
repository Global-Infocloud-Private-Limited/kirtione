<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CropMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CropModel');             
    }

    public function AddEditCrop()
    {        
        if (!has_permission_new('CropMaster', '', 'view')) {
            access_denied('BrandMaster');
        }
        $Crops =  $this->CropModel->get_all_table_data($tablename="tblcrops");
        $data['Crops'] = $Crops;

        $maxCropId = $this->CropModel->get_max_crop_id();
        $data['maxCropId'] = $maxCropId;
        $this->load->view('admin/CropMaster/AddEditCrop',$data);
    }

    public function insertCropDetails()
    {
        if (!has_permission_new('CropMaster', '', 'create')) {
            access_denied('BrandMaster');
        }
        $CropName = $this->input->post('CropName');

        $insert_crop = array(
            'CropName'=>$CropName,      
        );
        $createnewcrop = $this->CropModel->insert_data($tablename="tblcrops",$insert_crop);
        if ($createnewcrop) {    
            $newCropId = $this->db->insert_id();       
            echo json_encode(['success' => true,'message' => 'Data inserted successfully', 'newMaxCropId' => $newCropId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetCropDetailsbyID()
    {
        $CropId = $this->input->post('CropId');
        $where = '(id="'.$CropId.'")'; 
        $Cropsdetails = $this->CropModel->get_data($tablename="tblcrops",$where);
        echo json_encode($Cropsdetails);
    }

    public function UpdateCropDetails()
    {
        if (!has_permission_new('CropMaster', '', 'edit')) {
            access_denied('BrandMaster');
        }
        $cropid = $this->input->post('cropid');
        $cropname = $this->input->post('cropname');

        $update_crop = array(
            'CropName'=>$cropname,      
        );
        $where = '(id="'.$cropid.'")'; 
        $updatecrop = $this->CropModel->edit_data($tablename="tblcrops",$where,$update_crop);
        if($updatecrop)
        {
            $Crops =  $this->CropModel->get_all_table_data($tablename="tblcrops");          

            echo json_encode(['success' => true,'message' => 'Data updated successfully','crops' => $Crops]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function Crop_table_data()
    {
        $Crops =  $this->CropModel->get_all_table_data($tablename="tblcrops");
        echo json_encode($Crops);
    }
}