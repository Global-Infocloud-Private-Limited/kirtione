<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ChangeMobileNo extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ChangeMobileNo_model');
         $this->load->database(); 
    }
    
    public function index()
    {
        if (!has_permission_new('ChangeMobileNo', '', 'view')) {
            access_denied('Invoice Items');
        }
        $AllClients = $this->ChangeMobileNo_model->GetAllTableList($tablename="tblclients");	
        $data['AllClients'] = $AllClients;
        $this->load->view('admin/ChangeMobileNo/ChangeMobileNo',$data);
    }

    public function GetAccountDetailsByID()
    {
        $AccountID =  $this->input->post('AccountID');
        $newaccountID = $this->input->post('newaccountID');
        $where = '(AccountID="' . $AccountID . '")';
        $clients_data = $this->ChangeMobileNo_model->GetRecorDetails($tablename="tblclients",$where);	

        if($clients_data['state'] !==null)
        {
            $wh_state =  '(short_name="' . $clients_data['state'] . '")';
            $state_list = $this->ChangeMobileNo_model->GetRecorDetails($tablename="tblxx_statelist",$wh_state);	
        }
        else
        {
            $state_list = null;
        }

        if($clients_data['dist'] !==null)
        {
            $wh_city =  '(id="' . $clients_data['dist'] . '")';
            $city_list = $this->ChangeMobileNo_model->GetRecorDetails($tablename="tblxx_citylist",$wh_city);	
        }  
        else
        {
            $city_list = null;
        }   
        
        if($clients_data['subdist'] !==null)
        {
            $wh_taluka =  '(id="' . $clients_data['subdist'] . '")';
            $taluka_list = $this->ChangeMobileNo_model->GetRecorDetails($tablename="tblTalukaMaster",$wh_taluka);	
        }  
        else
        {
            $taluka_list = null;
        }   

        $wh ='(AccountID="' . $newaccountID . '")';
        $all_accountdetails = $this->ChangeMobileNo_model->GetRecordList($tablename="tblclients",$wh);	

        $response = array(
                            'clients_data'=>$clients_data,       
                            'state_list'=>$state_list,      
                            'city_list'=>$city_list,  
                            'taluka_list'=>$taluka_list,     
                            'all_accountdetails'=>$all_accountdetails,        
                          );
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function CheckAccountExist()
    {
        $newaccountID = $this->input->post('newaccountID');             
        $where = '(AccountID="' . $newaccountID . '")';
        $clients_data = $this->ChangeMobileNo_model->GetRecorDetails($tablename="tblclients",$where);	       
        
        if($clients_data)
        {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        }
        else
        {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
        }        
    }    

    public function ChangeMobileNumber()
    {       
        if (!has_permission_new('ChangeMobileNo', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $newaccountID = $this->input->post('newaccountID');
        $AccountID = $this->input->post('AccountID');
        
        $tables = $this->db->list_tables();     
        $results = [];
      
        foreach ($tables as $table) 
        {      
            $query = $this->db->query("SHOW COLUMNS FROM {$table}");
            $columns = $query->result_array();             

            $field_types = [];
            foreach ($columns as $column) {
                $field_types[$column['Field']] = $column['Type'];
            }
            
            $fields = $this->db->list_fields($table);                
            foreach ($fields as $field) 
            {               
                $field_type = $field_types[$field];              
              
                if (strpos($field_type, 'int') === 0 || strpos($field_type, 'varchar') === 0) 
                {   
                    $query = $this->db->get_where($table, [
                        $field => $AccountID,/*
                        $field . ' IS NOT NULL' => null,
                        $field . ' !=' => '0000-00-00 00:00:00'*/
                    ]);              
            
                    if ($query->num_rows() > 0) {                    
                        
                        $results[] = [
                            'tablename' => $table,
                            'columnname' => $field
                        ];
                    }
                }
            }
        }

        $updateSuccess = false;
        if (!empty($results)) 
        {
            foreach ($results as $table1) 
            {
                $tablename = $table1['tablename'];
                $columnname = $table1['columnname'];   
                
                $this->db->where($columnname, $AccountID);
                $this->db->update($tablename, [$columnname => $newaccountID]); 
               
                if ($this->db->affected_rows() > 0) {
                    $updateSuccess = true; 
                }
            }
        } 
        header('Content-Type: application/json');
        echo json_encode(['success' => $updateSuccess]);  
    }  
}