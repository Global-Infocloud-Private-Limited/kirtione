<?php

defined('BASEPATH') or exit('No direct script access allowed');

class News_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function fetchAllNews(){
        return $this->db->get(db_prefix() . 'news')->result_array();
    }
    
    public function fetchNewsDetails($newsID)
	{  
		
		$sql ='SELECT '.db_prefix().'news.*
		FROM '.db_prefix().'news WHERE id = '.$newsID;
		
		$result = $this->db->query($sql)->row();
		return $result;
		
	}
}