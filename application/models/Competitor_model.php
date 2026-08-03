<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Competitor_model extends App_Model
{
    public function getCompetitor()
    {
        $CompetitorIDs = array("C01","C02");
        $this->db->where_not_in('CompetitorID',$CompetitorIDs);
        return $this->db->get('tblCompetitorMaster')->result_array();
    }
    
    public function getSingleCompetitorDb($CompetitorID){
        $this->db->where('CompetitorID',$CompetitorID);
        return $this->db->get('tblCompetitorMaster')->row();
    }
    
    public function saveCompetitorDb($data){
        return $this->db->insert('tblCompetitorMaster',$data);
    }
    
    public function updateCompetitorDb($data){
        $this->db->where('CompetitorID',$data['CompetitorID']);
        return $this->db->update('tblCompetitorMaster',$data);
    }
}