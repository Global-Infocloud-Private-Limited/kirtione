<?php

defined('BASEPATH') or exit('No direct script access allowed');

class VehicleTypeMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('VehicleTypeModel');         
    }

    public function index()
    {
        if (!has_permission_new('VehicleTypeMaster', '', 'view')) {
            access_denied('Access Denied');
        }
        $vehicletype = $this->VehicleTypeModel->get_all_table_data($tablename="tblvehicletype");     
        $data['vehicletype'] = $vehicletype;

        $maxtypeId = $this->VehicleTypeModel->get_max_type_id();
        $data['maxtypeId'] = $maxtypeId + 1;
        $this->load->view('admin/VehicleTypeMaster/AddEditVehicleType',$data);
    }

    public function insertVehicleType()
    {
        if (!has_permission_new('VehicleTypeMaster', '', 'create')) {
            access_denied('Access Denied');
        }
        $TypeName = $this->input->post('TypeName');
        $insert_type = array(
            'VehicleType'=>$TypeName,      
        );
        $createnewvehicletype = $this->VehicleTypeModel->insert_data($tablename="tblvehicletype",$insert_type);
        if ($createnewvehicletype) {    
            $newMaxTypeId = $this->db->insert_id();       
            echo json_encode(['success' => true,'message' => 'Data inserted successfully', 'newMaxTypeId' => $newMaxTypeId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function GetVehcileTypeDetailsbyID()
    {
        $TypeId = $this->input->post('TypeId');
        $where = '(id="'.$TypeId.'")'; 
        $typedetails = $this->VehicleTypeModel->get_data($tablename="tblvehicletype",$where);
        echo json_encode($typedetails);
    }

    public function UpdateVehicleDetails()
    {
        if (!has_permission_new('VehicleTypeMaster', '', 'edit')) {
            access_denied('Access Denied');
        }
        $typeid = $this->input->post('typeid');
        $typename = $this->input->post('typename');

        $update_type = array(
            'VehicleType'=>$typename,      
        );
        $where = '(id="'.$typeid.'")'; 
        $updatetype = $this->VehicleTypeModel->edit_data($tablename="tblvehicletype",$where,$update_type);
        if($updatetype)
        {
            $types =  $this->VehicleTypeModel->get_all_table_data($tablename="tblvehicletype");            

            echo json_encode(['success' => true,'message' => 'Data updated successfully','types' => $types]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }

    public function vehicletype_table_data()
    {
        $vehicleTypes =  $this->VehicleTypeModel->get_all_table_data($tablename="tblvehicletype");
        echo json_encode($vehicleTypes);
    }
}