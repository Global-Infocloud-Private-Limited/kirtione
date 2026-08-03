<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payment_cycle_model extends App_Model
{
    public function getAllCycles(){
        return $this->db->get('tblPaymentCycle')->result_array();
    }
    
    public function getSingleCycleDB($CycleID){
        $this->db->where('CycleID',$CycleID);
        return $this->db->get('tblPaymentCycle')->row();
    }
    
    public function saveCycleDB($data){
        return $this->db->insert('tblPaymentCycle',$data);
    }
    
    public function updateCycleDB($data){
        $this->db->where('CycleID',$data['CycleID']);
        return $this->db->update('tblPaymentCycle',$data);
    }
    
    public function getAllLockingDB(){
        return $this->db->get('tblLocking')->result_array();
    }
    
    public function getSingleLockDB($LockID){
        $this->db->where('LockID',$LockID);
        return $this->db->get('tblLocking')->row();
    }
    
    public function saveLockDB($data){
        return $this->db->insert('tblLocking',$data);
    }
    
    public function updateLockDB($data){
        $this->db->where('LockID',$data['LockID']);
        return $this->db->update('tblLocking',$data);
    }
}