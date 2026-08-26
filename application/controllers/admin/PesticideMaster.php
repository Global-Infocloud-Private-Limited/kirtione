<?php

defined('BASEPATH') or exit('No direct script access allowed');

class PesticideMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PesticideModel');              
    }

    public function AddEditPesticide()
    { 
        if (!has_permission_new('PesticideMaster', '', 'view')) {
            access_denied('BrandMaster');
        }
        $Brands =  $this->PesticideModel->get_all_table_data($tablename="tblbrands");        
        $data['Brands'] = $Brands;

        $Pesticides =  $this->PesticideModel->get_all_table_data($tablename="tblpesticides");
        foreach($Pesticides as &$pesti)
        {
            $where = '(id="'.$pesti['BrandId'].'")';
            $brandname = $this->PesticideModel->get_data($tablename="tblbrands",$where);
            $pesti['brandname'] = $brandname['BrandName'];
        }
        $data['Pesticides'] = $Pesticides;

        $maxpestiId = $this->PesticideModel->get_max_pesti_id();
        $data['maxpestiId'] = $maxpestiId;
        $this->load->view('admin/PesticideMaster/AddEditPesticide',$data);
    }

    public function insertPesticideDetails()
    {
        if (!has_permission_new('PesticideMaster', '', 'create')) {
            access_denied('BrandMaster');
        }
        $PesticideName = $this->input->post('PesticideName');
        $Brandname = $this->input->post('Brandname');

        $insert_pesticidedetails = array(           
            'PesticideName'=>$PesticideName,   
            'BrandId'=>$Brandname,      
        );
        $createnewPesticide =   $this->PesticideModel->insert_data($tablename="tblpesticides",$insert_pesticidedetails);
        if ($createnewPesticide) {   
            $newPestiId = $this->db->insert_id();            
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','newMaxPestiId' => $newPestiId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetPesticideDetailsbyID()
    {
        $PesticideId = $this->input->post('PesticideId');
        $where = '(id="'.$PesticideId.'")'; 
        $Pesticidedetails = $this->PesticideModel->get_data($tablename="tblpesticides",$where);
        echo json_encode($Pesticidedetails);
    }

    public function UpdateSeedDetails()
    {
        if (!has_permission_new('PesticideMaster', '', 'edit')) {
            access_denied('BrandMaster');
        }
        $PesticideId = $this->input->post('PesticideId');
        $PesticideName = $this->input->post('PesticideName');
        $BrandId = $this->input->post('BrandId');

        $update_pesti = array(
            'PesticideName'=>$PesticideName,   
            'BrandId'=>$BrandId
        );
        $where = '(id="'.$PesticideId.'")'; 
        $updatepesticide = $this->PesticideModel->edit_data($tablename="tblpesticides",$where,$update_pesti);
        if($updatepesticide)
        {
            $Pesticides =  $this->PesticideModel->get_all_table_data($tablename="tblpesticides");      
            echo json_encode(['success' => true,'message' => 'Data updated successfully','Pesticides' => $Pesticides]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function Pesticides_table_data()
    {
        $Pesticides =  $this->PesticideModel->get_all_table_data($tablename="tblpesticides");
        foreach($Pesticides as &$pesti)
        {
            $where = '(id="'.$pesti['BrandId'].'")';
            $brandname = $this->PesticideModel->get_data($tablename="tblbrands",$where);
            $pesti['brandname'] = $brandname['BrandName'];
        }
        echo json_encode($Pesticides);
    }
}