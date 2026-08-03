<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_cycle extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Payment_cycle_model');
    }
    
    public function index(){
        if (!has_permission_new('PaymentCycle', '', 'view')) {
            access_denied('invoices');
        }
        $data['title'] = "Payment Cycle Master";
        $this->load->view("admin/payments/payment_cycle",$data);
    }
    
    public function getAllCycles(){
        $table_data = $this->Payment_cycle_model->getAllCycles();
        $html = '';
        $sr = 1;
        foreach($table_data as $key=>$value){
            $html .= '<tr onclick=fill_data("'.$value['CycleID'].'")>';
            $html .= '<td>'.$sr.'</td>';
            $html .= '<td>'.$value['CycleID'].'</td>';
            $html .= '<td>'.$value['CycleName'].'</td>';
            $html .= '<td>'.$value['CycleDays'].'</td>';
            $html .= '</tr>';
            $sr++;
        }
        echo $html;
    }
    
    public function getSingleCycle(){
        $CycleID = $this->input->post('CycleID');
        $result = $this->Payment_cycle_model->getSingleCycleDB($CycleID);
        echo json_encode($result);
    }
    
    public function saveCycle()
    {
        if (!has_permission_new('PaymentCycle', '', 'create')) {
            access_denied('invoices');
        }
        $data = array(
            'CycleID' => $this->input->post('CycleID'),
            'CycleName' => $this->input->post('CycleName'),
            'CycleDays' => $this->input->post('CycleDays'),
            'TransDate' => date('Y-m-d H:i:s'),
            'UserID' => $this->session->userdata('username')
        );
        $result = $this->Payment_cycle_model->saveCycleDB($data);
        echo json_encode($result);
    }
    
    public function updateCycle()
    {
        if (!has_permission_new('PaymentCycle', '', 'edit')) {
            access_denied('invoices');
        }
        $data = array(
            'CycleID' => $this->input->post('CycleID'),
            'CycleName' => $this->input->post('CycleName'),
            'CycleDays' => $this->input->post('CycleDays'),
            'TransDate2' => date('Y-m-d H:i:s'),
            'UserID2' => $this->session->userdata('username')
        );
        $result = $this->Payment_cycle_model->updateCycleDB($data);
        echo json_encode($result);
    }
    
    public function locking_period()
    {
        if (!has_permission_new('Locking', '', 'view')) {
            access_denied('invoices');
        }
        $data['title'] = "Locking Period Master";
        $this->load->view("admin/payments/locking_period",$data);
    }
    
    public function getAllLocking(){
        $table_data = $this->Payment_cycle_model->getAllLockingDB();
        $html = '';
        $sr = 1;
        foreach($table_data as $key=>$value){
            $html .= '<tr onclick=fill_data("'.$value['LockID'].'")>';
            $html .= '<td>'.$sr.'</td>';
            $html .= '<td>'.$value['LockID'].'</td>';
            $html .= '<td>'.$value['LockName'].'</td>';
            $html .= '<td>'.$value['LockDays'].'</td>';
            $html .= '</tr>';
            $sr++;
        }
        echo $html;
    }
    
    public function getSingleLock(){
        $LockID = $this->input->post('LockID');
        $result = $this->Payment_cycle_model->getSingleLockDB($LockID);
        echo json_encode($result);
    }
    
    public function saveLock()
    {
        if (!has_permission_new('Locking', '', 'create')) {
            access_denied('invoices');
        }
        $data = array(
            'LockID' => $this->input->post('LockID'),
            'LockName' => $this->input->post('LockName'),
            'LockDays' => $this->input->post('LockDays'),
            'TransDate' => date('Y-m-d H:i:s'),
            'UserID' => $this->session->userdata('username')
        );
        $result = $this->Payment_cycle_model->saveLockDB($data);
        echo json_encode($result);
    }
    
    public function updateLock()
    {
        if (!has_permission_new('Locking', '', 'edit')) {
            access_denied('invoices');
        }
        $data = array(
            'LockID' => $this->input->post('LockID'),
            'LockName' => $this->input->post('LockName'),
            'LockDays' => $this->input->post('LockDays'),
            'TransDate2' => date('Y-m-d H:i:s'),
            'UserID2' => $this->session->userdata('username')
        );
        $result = $this->Payment_cycle_model->updateLockDB($data);
        echo json_encode($result);
    }
}