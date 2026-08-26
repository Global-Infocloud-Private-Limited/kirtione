<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FertilizerMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FertilizerModel');              
    }

    public function AddEditFertilizer()
    {   
        if (!has_permission_new('FertilizerMaster', '', 'view')) {
            access_denied('FertilizerMaster');
        }
        $Brands =  $this->FertilizerModel->get_all_table_data($tablename="tblbrands");        
        $data['Brands'] = $Brands;

        $fertilizers =  $this->FertilizerModel->get_all_table_data($tablename="tblfertilizers");
        foreach($fertilizers as &$fer)
        {
            $where = '(id="'.$fer['BrandId'].'")';
            $brandname = $this->FertilizerModel->get_data($tablename="tblbrands",$where);
            $fer['brandname'] = $brandname['BrandName'];
        }
        $data['fertilizers'] = $fertilizers;

        $maxFerId = $this->FertilizerModel->get_max_fer_id();
        $data['maxFerId'] = $maxFerId;
        $this->load->view('admin/FertilizerMaster/AddEditFertilizer',$data);
    }

    public function insertFerDetails()
    {
        if (!has_permission_new('FertilizerMaster', '', 'create')) {
            access_denied('FertilizerMaster');
        }
        $FertilizerName = $this->input->post('FertilizerName');
        $Brandname = $this->input->post('Brandname');

        $insert_fertilizer = array(           
            'fertilizerName'=>$FertilizerName,   
            'BrandId'=>$Brandname,      
        );
        $createnewfertilizer =  $this->FertilizerModel->insert_data($tablename="tblfertilizers",$insert_fertilizer);
        if ($createnewfertilizer) {   
            $newFerId = $this->db->insert_id();            
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','newMaxFerId' => $newFerId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetFertilizerDetailsbyID()
    {
        $FertilizerId = $this->input->post('FertilizerId');
        $where = '(id="'.$FertilizerId.'")'; 
        $Fertilizerdetails = $this->FertilizerModel->get_data($tablename="tblfertilizers",$where);
        echo json_encode($Fertilizerdetails);
    }

    public function UpdateFertilizerDetails()
    {
        if (!has_permission_new('FertilizerMaster', '', 'edit')) {
            access_denied('FertilizerMaster');
        }
        $FertilizerId = $this->input->post('FertilizerId');
        $FertilizerName = $this->input->post('FertilizerName');
        $BrandId = $this->input->post('BrandId');

        $update_fertilizer = array(
            'fertilizerName'=>$FertilizerName,   
            'BrandId'=>$BrandId
        );
        $where = '(id="'.$FertilizerId.'")'; 
        $updatefertilizer = $this->FertilizerModel->edit_data($tablename="tblfertilizers",$where,$update_fertilizer);
        if($updatefertilizer)
        {
            $Fertilizers =  $this->FertilizerModel->get_all_table_data($tablename="tblfertilizers");      
            echo json_encode(['success' => true,'message' => 'Data updated successfully','Fertilizers' => $Fertilizers]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function Fertilizers_table_data()
    {
        $fertilizers =  $this->FertilizerModel->get_all_table_data($tablename="tblfertilizers");
        foreach($fertilizers as &$fer)
        {
            $where = '(id="'.$fer['BrandId'].'")';
            $brandname = $this->FertilizerModel->get_data($tablename="tblbrands",$where);
            $fer['brandname'] = $brandname['BrandName'];
        }
        echo json_encode($fertilizers);
    }
}