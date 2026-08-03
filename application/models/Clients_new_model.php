<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Clients_new_model extends App_Model
{
    public function getMandi(){
        return $this->db->get('tblmandi')->result_array();
    }
    
    public function getRemainingCommodity($comm){
        $this->db->where('commodity !=',$comm);
        return $this->db->get('tblcommodityMaster')->result_array();
    }
    
    public function get_table_on_load_filter($data){
        if(($data['client_type'] != '') && ($data['distributor_state'] != '') && ($data['status'] != '')){
            $this->db->where('CustomerType',$data['client_type']);
            $this->db->where('state',$data['distributor_state']);
            $this->db->where('active',$data['status']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] != '') && ($data['distributor_state'] != '') && ($data['status'] == '')){
            $this->db->where('CustomerType',$data['client_type']);
            $this->db->where('state',$data['distributor_state']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] != '') && ($data['distributor_state'] == '') && ($data['status'] != '')){
            $this->db->where('CustomerType',$data['client_type']);
            $this->db->where('active',$data['status']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] == '') && ($data['distributor_state'] != '') && ($data['status'] != '')){
            $this->db->where('state',$data['distributor_state']);
            $this->db->where('active',$data['status']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] == '') && ($data['distributor_state'] == '') && ($data['status'] == '')){
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] != '') && ($data['distributor_state'] == '') && ($data['status'] == '')){
            $this->db->where('CustomerType',$data['client_type']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] == '') && ($data['distributor_state'] != '') && ($data['status'] == '')){
            $this->db->where('state',$data['distributor_state']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
        if(($data['client_type'] == '') && ($data['distributor_state'] == '') && ($data['status'] != '')){
            $this->db->where('active',$data['status']);
            $this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
            $result= $this->db->get(db_prefix() . 'clients')->result_array();
        }
		
	    return $result;
    }
    
    public function getRateData(){
        return $this->db->get('tbldailyCommodityRates')->result_array();
    }
    
    public function getSingleRateData($rate_id){
        $this->db->where('rate_id',$rate_id);
        return $this->db->get('tbldailyCommodityRates')->row();
    }
    
    public function getMandiRateData($mandi){
        $this->db->where('mandi',$mandi);
        return $this->db->get('tbldailyCommodityRates')->result_array();
    }
    
    public function UpdateKirtiRateDetails($data){
        $this->db->where('rate_id',$data['rate_id']);
        return $this->db->update('tbldailyCommodityRates',$data);
    }
    
    public function UpdateApmcRateDetails($data){
        $this->db->where('rate_id',$data['rate_id']);
        return $this->db->update('tbldailyCommodityRates',$data);
    }
    
    public function UpdateCompetitorRateDetails($data){
        $this->db->where('rate_id',$data['rate_id']);
        return $this->db->update('tbldailyCommodityRates',$data);
    }

}