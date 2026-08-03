<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SaleDashboard_model extends App_Model
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    public function GetAllCenter()
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
        //$this->db->where_in(db_prefix() . 'Center_wise_item.ItemID', $ItemID);
        $this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
        if(!is_admin()){
            $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
            $this->db->where(db_prefix() . 'staff_wise_center.AccountID', $UserID);
        }
        
        $this->db->join(db_prefix() . 'Centerwise_staff_priority', '' . db_prefix() . 'Centerwise_staff_priority.CenterID = ' . db_prefix() . 'CenterMaster.CenterID AND tblCenterwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
        $this->db->group_by('tblCenter_wise_item.CenterID');
        $this->db->where('tblCenterMaster.status',"Y");
        $this->db->order_by('tblCenterwise_staff_priority.priority,CenterMaster.CenterName','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }
    public function GetCenterWiseItem()
    {
        $UserID = $this->session->userdata('username');
        $this->db->select('tblCenter_wise_item.*');
        $this->db->join(db_prefix() . 'Itemwise_staff_priority', '' . db_prefix() . 'Itemwise_staff_priority.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"','LEFT');
        //$this->db->where(db_prefix() . 'Itemwise_staff_priority.staff_id', $UserID);
        $this->db->order_by('tblItemwise_staff_priority.priority','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }
    
    public function GetItemWiseCenterWisePurchase()
    {
        $UserID = $this->session->userdata('username');
        $this->db->select('SUM(IFNULL(tblGateMaster.LoadedWeight,0) - IFNULL(tblGateMaster.TareWeight,0)) AS NetWeight,tblGateMaster.ItemID,tbllead_master.CenterID');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.LoadedWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->order_by('tblGateMaster.ItemID','ASC');
        $this->db->group_by('tblGateMaster.ItemID,tbllead_master.CenterID');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function GetItemWiseCenterWiseTodayPurchase($GroupID = '',$CenterID = '',$Type ="")
    {
        if($Type == "T"){
            $UserType = "3";
        }else{
            $UserType = "1";
        }
        $minvalue = date('Y-m-d').' 00:00:00';
        $maxvalue = date('Y-m-d').' 23:59:59';
        $UserID = $this->session->userdata('username');
        $this->db->select('SUM(IFNULL(tblGateMaster.LoadedWeight,0) - IFNULL(tblGateMaster.TareWeight,0)) AS NetWeight,tblGateMaster.ItemID,tbllead_master.CenterID');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'GateMaster.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'GateMaster.AccountID');
        $this->db->where(db_prefix() . 'lead_master.CenterID', $CenterID);
        $this->db->where(db_prefix() . 'items.subgroup_id', $GroupID);
        $this->db->where(db_prefix() . 'clients.CustomerType', $UserType);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.LoadedWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where("gate_in_date BETWEEN '$minvalue' AND '$maxvalue'");
        $this->db->order_by('tblGateMaster.ItemID','ASC');
        $this->db->group_by('tblGateMaster.ItemID,tbllead_master.CenterID');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function GetItemWiseCenterWiseCurrentRate()
    {
        $this->db->select('tblRateMaster.*,Center_wise_item.TradeOnOff');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where('tblRateMaster.KeyID', 'C01');
        $this->db->where('tblRateMaster.Type', 'T');
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'RateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'RateMaster.CenterID', 'left');
        return $this->db->get('tblRateMaster')->result_array();
    }
    
    public function GetItemWiseCenterWiseAvgRate()
    {
        $this->db->select('tblGateMaster.ItemID,SUM(tblGateMaster.basic_rate) AS AVGRAte, COUNT(tblGateMaster.basic_rate) AS TotalRate,tbllead_master.CenterID');
        $this->db->group_by('tblGateMaster.ItemID,tbllead_master.CenterID');
        //$this->db->where('tbllead_master.CenterID', 'NA');
        $this->db->where('tblGateMaster.TType', 'P');
        $this->db->where('tblGateMaster.LoadedWeight IS NOT NULL');
        $this->db->where('tblGateMaster.TareWeight IS NOT NULL');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function GetItemWiseCenterWiseCurrentAvgRate()
    {
        $minvalue = date('Y-m-d').' 00:00:00';
        $maxvalue = date('Y-m-d').' 23:59:59';
        $this->db->select('tblGateMaster.ItemID,SUM(tblGateMaster.basic_rate) AS AVGRAte, COUNT(tblGateMaster.basic_rate) AS TotalRate,tbllead_master.CenterID');
        $this->db->group_by('tblGateMaster.ItemID,tbllead_master.CenterID');
        //$this->db->where('tbllead_master.CenterID', 'NA');
        $this->db->where('tblGateMaster.TType', 'P');
        $this->db->where('tblGateMaster.LoadedWeight IS NOT NULL');
        $this->db->where('tblGateMaster.TareWeight IS NOT NULL');
        $this->db->where("tblGateMaster.gate_in_date BETWEEN '$minvalue' AND '$maxvalue'");
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    // New Sell Dashboard
    
    public function GetAllItemWiseCenter($yourCommodityArray,$yourCenterIDsArray)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName,tblitems_sub_groups.ShortCode AS SubGroupID,tblitems_sub_groups.name');
        $this->db->where_in(db_prefix() . 'Center_wise_item.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID');
        $this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id');
        $this->db->where_in(db_prefix() . 'items_sub_groups.ShortCode', $yourCommodityArray);
        if(!is_admin()){
            $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
            $this->db->where(db_prefix() . 'staff_wise_center.AccountID', $UserID);
            
            $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID');
            $this->db->where(db_prefix() . 'staff_wise_items.AccountID', $UserID);
        }
        
        $this->db->join(db_prefix() . 'Centerwise_staff_priority', '' . db_prefix() . 'Centerwise_staff_priority.CenterID = ' . db_prefix() . 'CenterMaster.CenterID AND tblCenterwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
        $this->db->join(db_prefix() . 'Itemwise_staff_priority', '' . db_prefix() . 'Itemwise_staff_priority.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
        $this->db->group_by('tblCenter_wise_item.CenterID,tblitems.GroupCode');
        $this->db->order_by('tblCenterwise_staff_priority.priority,tblItemwise_staff_priority.priority','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }
    
    public function GetAllItemWiseCenterTradingStatus($yourCommodityArray,$yourCenterIDsArray)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblCenter_wise_item.CenterID,tblCenter_wise_item.TradeOnOff,tblCenter_wise_item.TradeOnOffFarmer,tblitems.GroupCode');
        $this->db->where_in(db_prefix() . 'Center_wise_item.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $yourCommodityArray);
        $this->db->order_by('tblCenter_wise_item.CenterID,tblCenter_wise_item.ItemID','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }
    
    public function AvgRateGroupWise($yourCommodityArray,$yourCenterIDsArray,$Type)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblitems.subgroup_id,tblRateMaster.CenterID,(SUM(tblRateMaster.Rate) / COUNT(tblRateMaster.Rate)) AS AvgRate');
        $this->db->where_in(db_prefix() . 'RateMaster.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'RateMaster.ItemID');
        $this->db->where_in(db_prefix() . 'items.subgroup_id', $yourCommodityArray);
        
        $this->db->where(db_prefix() . 'RateMaster.Type', $Type);
        if($Type == "N"){
            $this->db->where(db_prefix() . 'RateMaster.KeyID', "C02");
        }else if($Type == "C"){
            $array = array("C02","C01");
            $this->db->where_not_in(db_prefix() . 'RateMaster.KeyID', 'C02');
            $this->db->where_not_in(db_prefix() . 'RateMaster.KeyID', 'C01');
        }else{
            $this->db->where(db_prefix() . 'RateMaster.KeyID', "C01");
        }
        $this->db->where(db_prefix() . 'RateMaster.IsActive', "Y");
        $this->db->where(db_prefix() . 'RateMaster.Rate > 0');
        
        $this->db->group_by('tblRateMaster.CenterID,tblitems.subgroup_id');
        $this->db->order_by('tblRateMaster.CenterID,tblitems.subgroup_id','ASC');
        return $this->db->get('tblRateMaster')->result_array();
    }
    
    public function TodaysCnfTradeQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,$Type)
    {
        $UserID = $this->session->userdata('username');
        $minvalue = date('Y-m-d').' 00:00:00';
        $maxvalue = date('Y-m-d').' 23:59:59';
        $this->db->select('tbllead_master.CenterID,tblitems.GroupCode,SUM(tbllead_master.quantity) AS TotalQty');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $yourCommodityArray);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $Type);
        $this->db->where(db_prefix() . 'lead_master.IsApprove', "Y");
        $this->db->where("tbllead_master.TransDate BETWEEN '$minvalue' AND '$maxvalue'");
        
        $this->db->group_by('tbllead_master.CenterID,tblitems.GroupCode');
        $this->db->order_by('tbllead_master.CenterID,tblitems.GroupCode','ASC');
        return $this->db->get('tbllead_master')->result_array();
    }
    
    public function clsQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,$Type)
    {
        $UserID = $this->session->userdata('username');
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        $this->db->select('tblitems.GroupCode,tbllead_master.CenterID,(SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight)) AS NetWeight');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $yourCommodityArray);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $Type);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'lead_master.FY', $fy);
        
        $this->db->group_by('tbllead_master.CenterID,tblitems.GroupCode');
        $this->db->order_by('tbllead_master.CenterID,tblitems.GroupCode','ASC');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function weightedAvgRateGroupWise($yourCommodityArray,$yourCenterIDsArray,$Type)
    {
        $UserID = $this->session->userdata('username');
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year'); 
        
        $this->db->select('tblitems.GroupCode,tbllead_master.CenterID,(SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight)) AS NetWeight,(tblGateMaster.final_rate * (SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight))) AS Amount');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $yourCommodityArray);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $Type);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'lead_master.FY', $fy);
        
        $this->db->group_by('tbllead_master.CenterID,tblitems.GroupCode,tblGateMaster.final_rate');
        $this->db->order_by('tbllead_master.CenterID,tblitems.GroupCode','ASC');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    // New Code 
    
    public function BaseItemRate($yourCommodityArray,$yourCenterIDsArray,$Type)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblRateMaster.ItemID,tblRateMaster.CenterID,tblRateMaster.Rate');
        $this->db->where_in(db_prefix() . 'RateMaster.CenterID', $yourCenterIDsArray);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'RateMaster.ItemID');
        $this->db->where_in(db_prefix() . 'RateMaster.ItemID', $yourCommodityArray);
        $this->db->where(db_prefix() . 'RateMaster.Type', $Type);
        $this->db->where(db_prefix() . 'RateMaster.IsActive', "Y");
        if($Type == "N"){
            $this->db->where(db_prefix() . 'RateMaster.KeyID', "C02");
            $this->db->order_by('tblRateMaster.CenterID,tblRateMaster.ItemID','ASC');
        }else if($Type == "C"){
            $this->db->order_by('tblRateMaster.CenterID,tblRateMaster.ItemID,tblRateMaster.Rate','DESC');
        }else if($Type == "M"){
            $this->db->order_by('tblRateMaster.CenterID,tblRateMaster.ItemID,tblRateMaster.Rate','DESC');
        }else{
            $this->db->where(db_prefix() . 'RateMaster.KeyID', "C01");
            $this->db->order_by('tblRateMaster.CenterID,tblRateMaster.ItemID','ASC');
        }
        return $this->db->get('tblRateMaster')->result_array();
        
    }
    
    // Sell Dashboard details pop up
    
    public function CurrentRateItemWise($GroupID,$CenterID,$Type)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblRateMaster.ItemID,tblRateMaster.CenterID,tblRateMaster.Rate');
        $this->db->where_in(db_prefix() . 'RateMaster.CenterID', $CenterID);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'RateMaster.ItemID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $GroupID);
        
        $this->db->where(db_prefix() . 'RateMaster.Type', $Type);
        $this->db->where(db_prefix() . 'RateMaster.KeyID', "C01");
        $this->db->where(db_prefix() . 'RateMaster.IsActive', "Y");
        $this->db->where(db_prefix() . 'RateMaster.Rate > 0');
        
        $this->db->group_by('tblRateMaster.CenterID,tblRateMaster.ItemID');
        $this->db->order_by('tblRateMaster.CenterID,tblRateMaster.ItemID','ASC');
        return $this->db->get('tblRateMaster')->result_array();
    }
    
    public function AvgCurrentRateItemWise($GroupID,$CenterID,$Type)
    {
        $UserID = $this->session->userdata('username');
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        if($Type == "T"){
            $UserType = "3";
        }else{
            $UserType = "1";
        }
        $minvalue = date('Y-m-d').' 00:00:00';
        $maxvalue = date('Y-m-d').' 23:59:59';
        
        $this->db->select('tblitems.ItemID,tbllead_master.CenterID,(SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight)) AS NetWeight,(tblGateMaster.final_rate * (SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight))) AS Amount');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $CenterID);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $GroupID);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $UserType);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'lead_master.FY', $fy);
        $this->db->where("tblGateMaster.gate_in_date BETWEEN '$minvalue' AND '$maxvalue'");
        $this->db->group_by('tbllead_master.CenterID,tblGateMaster.ItemID,tblGateMaster.final_rate');
        $this->db->order_by('tbllead_master.CenterID,tblGateMaster.ItemID','ASC');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function clsQtyItemWise($GroupID,$CenterID,$Type)
    {
        $UserID = $this->session->userdata('username');
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        
        if($Type == "T"){
            $UserType = "3";
        }else{
            $UserType = "1";
        }
        
        $this->db->select('tblitems.ItemID,tbllead_master.CenterID,(SUM(tblGateMaster.LoadedWeight) - SUM(tblGateMaster.TareWeight)) AS NetWeight');
        $this->db->join(db_prefix() . 'lead_master', '' . db_prefix() . 'lead_master.BookingID = ' . db_prefix() . 'GateMaster.BookingID');
        $this->db->where_in(db_prefix() . 'lead_master.CenterID', $CenterID);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'lead_master.ItemID');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'lead_master.AccountID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $GroupID);
        
        $this->db->where(db_prefix() . 'clients.CustomerType', $UserType);
        $this->db->where(db_prefix() . 'GateMaster.TType', "P");
        $this->db->where(db_prefix() . 'GateMaster.TareWeight IS NOT NULL');
        $this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'lead_master.FY', $fy);
        
        $this->db->group_by('tbllead_master.CenterID,tblGateMaster.ItemID');
        $this->db->order_by('tbllead_master.CenterID,tblGateMaster.ItemID','ASC');
        return $this->db->get('tblGateMaster')->result_array();
    }
    
    public function GroupWiseCenterWiseItem($GroupID,$CenterID)
    {
        $UserID = $this->session->userdata('username');
        
        $this->db->select('tblCenter_wise_item.ItemID,tblitems.ItemName,tblCenter_wise_item.CenterID');
        $this->db->where_in(db_prefix() . 'Center_wise_item.CenterID', $CenterID);
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID');
        $this->db->where_in(db_prefix() . 'items.GroupCode', $GroupID);
        $this->db->group_by('tblCenter_wise_item.CenterID,tblCenter_wise_item.ItemID');
        $this->db->order_by('tblCenter_wise_item.CenterID,tblCenter_wise_item.ItemID','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }
    
    public function statusCheck(){
        $this->db->select('tblCenter_wise_item.*');
        $result = $this->db->get('tblCenter_wise_item')->result_array();
        $tradeOnOffCheck = false;
        foreach($result as $r){
            if($r['TradeOnOff'] == 'Y'){
                $tradeOnOffCheck = true;
            }
        }
        return $tradeOnOffCheck;
    }
    public function statusCheck1(){
        $this->db->select('tblCenter_wise_item.*');
        $result = $this->db->get('tblCenter_wise_item')->result_array();
        $farmerOnOffCheck = false;
        foreach($result as $r){
            if($r['TradeOnOffFarmer'] == 'Y'){
                $farmerOnOffCheck = true;
            }
        }
        return $farmerOnOffCheck;
    }
    public function statusCheck2(){
        $this->db->select('tblCenter_wise_item.*');
        $result = $this->db->get('tblCenter_wise_item')->result_array();
        $saleOnOffCheck = false;
        foreach($result as $r){
            if($r['SaleTradeOnOff'] == 'Y'){
                $saleOnOffCheck = true;
            }
        }
        return $saleOnOffCheck;
    }
}
?>