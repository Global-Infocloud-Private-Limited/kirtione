<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CompanyMaster extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->Model('CompanyMaster_Model');
     }
    
    public function index(){
        if (!has_permission_new('CompanyMaster', '', 'view')) {
            access_denied('Invoice Items');
        }
        
        $data['title'] = "Company Master";
        $data['StateList'] = $this->CompanyMaster_Model->getState();
        $this->load->view('admin/CompanyMaster/AddEditCompany', $data);   
    }
    
    public function CompanyListPopUp()
    {
        $CompnyList = $this->CompanyMaster_Model->GetCompanyList();
        $html = "";
        foreach ($CompnyList as $key => $value) {
            $html .= '<tr class="get_AccountID" data-id="'.$value["PlantID"].'">';
            $html .= '<td>'.$value["PlantID"].'</td>';
            $html .= '<td>'.$value["PlantName"].'</td>';
            $html .= '<td>'.$value["state"].'</td>';
            $html .= '<td>'.$value["city"].'</td>';
            $html .= '<td>'.$value["GstNo"].'</td>';
            $html .= '<td>'.$value["fssai_no"].'</td>';
            $html .= '</tr>';
        }
        echo $html;
        //echo json_encode($account_data);
    }
     
    /* Get Company Details by PlantID / ajax */
    public function GetCompanyDetailByID()
    {
        $PlantID = $this->input->post('PlantID');
        $CompanyDetails = $this->CompanyMaster_Model->GetCompanyDetails($PlantID);
        echo json_encode($CompanyDetails);
    }
    
    public function SaveCompany()
    {
        if (!has_permission_new('CompanyMaster', '', 'create')) {
            access_denied('Invoice Items');
        }
        $CompanyDetails = $this->input->post();
        $company  = $this->CompanyMaster_Model->SaveCompany($CompanyDetails);
        echo json_encode($company);
    }
    
    public function UpdateCompany()
    {
        if (!has_permission_new('CompanyMaster', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $CompanyDetails = $this->input->post();
        $company = $this->CompanyMaster_Model->UpdateCompany($CompanyDetails);
        echo json_encode($company);
    }
}