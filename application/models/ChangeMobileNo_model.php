<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChangeMobileNo_model extends App_Model
{
    public function GetAllTableList($tbl)
    {
        $this->db->select('*');
        $this->db->from($tbl);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function GetRecordList($tbl,$where)
    {
        $this->db->select('*');
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function GetRecorDetails($tbl,$where)
    {
        $this->db->select('*');
        $this->db->from($tbl);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_data($tbl,$where,$arr) 
    {
        $this->db->where($where);
        if ($this->db->update($tbl, $arr)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function insert_data($tbl,$data) 
    {      
        $this->db->insert($tbl, $data);
        return $this->db->insert_id();
    }
}