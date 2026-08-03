<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Booking_settlement_model extends App_Model
{
    public function GetAllStatesDB(){
        $this->db->order_by('state_name','ASC');
        return $this->db->get('tblxx_statelist')->result_array();
    }
    
    public function GetCitiesDB($State){
        $this->db->order_by('id','ASC');
        $this->db->where('state',$State);
        return $this->db->get('tbl_xx_city')->result_array();
    }
    
    public function GetAllClientsNameDB($BookingType,$Name,$BookingID){
        $this->db->select('tbllead_master.*,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID','left');
        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID','left');
        if($BookingType != ''){
            $this->db->where('TType',$BookingType);
        }
        if($Name != ''){
            $this->db->where('AccountID',$Name);
        }
        if($BookingID != ''){
            $this->db->where('BookingID',$BookingID);
        }
        $this->db->order_by('id','ASC');
        return $this->db->get('tbllead_master')->result_array();
    }
    
    public function GetAllBookingIDDB($BookingType,$Name,$BookingID){
        $this->db->select('tbllead_master.*');
        if($BookingType != ''){
            $this->db->where('TType',$BookingType);
        }
        if($Name != ''){
            $this->db->where('AccountID',$Name);
        }
        if($BookingID != ''){
            $this->db->where('BookingID',$BookingID);
        }
        $this->db->order_by('id','ASC');
        return $this->db->get('tbllead_master')->result_array();
    }
    
    public function GetTableDataDB($Name,$BookingType,$BookingID){
        $this->db->select('tbllead_master.*,tblitems.ItemName,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
        $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID','left');
        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID','left');
        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID','left');
        if($BookingType != ''){
            $this->db->where('tbllead_master.TType',$BookingType);
        }
        if($Name != ''){
            $this->db->where('tbllead_master.AccountID',$Name);
        }
        if($BookingID != ''){
            $this->db->where('tbllead_master.BookingID',$BookingID);
        }
        $this->db->order_by('tbllead_master.id','ASC');
        return $this->db->get('tbllead_master')->result_array();
    }
    
    public function GetSingleBookingDataDB($BookingID){
        $this->db->where('BookingID',$BookingID);
        $this->db->order_by('id','ASC');
        return $this->db->get('tbllead_master')->row();
    }
}