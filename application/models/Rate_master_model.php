<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rate_master_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Order_model');
    }

//===================== deduction matrix =======================================

    public function getCommodity(){
        return $this->db->get('tblitems')->result_array();
    }
    // Get All QC Parameter
    public function getParameter(){
        return $this->db->get('tblItemParameter')->result_array();
    }

    public function GetQcParameterByItemID($ItemID)
    {
        $this->db->select('tblItemQCParameter.*,tblItemParameter.ItemParameterName');
        $this->db->from(db_prefix() . 'ItemQCParameter');
        $this->db->join(db_prefix() . 'ItemParameter', '' . db_prefix() . 'ItemParameter.ItemParameterID = ' . db_prefix() . 'ItemQCParameter.ItemParameterID');
        $this->db->order_by('tblItemQCParameter.ItemParameterID', 'ASC');
        $this->db->where('tblItemQCParameter.ItemID',$ItemID);
        return $this->db->get()->result_array();
    }

    public function GetQcParameterDetailsByItemID($ItemID,$QCparameterID)
    {
        $response = array();
        $this->db->select('tblItemQCParameter.*');
        $this->db->from(db_prefix() . 'ItemQCParameter');
        $this->db->where('tblItemQCParameter.ItemID',$ItemID);
        $this->db->where('tblItemQCParameter.ItemParameterID',$QCparameterID);
        $QCParameterValue = $this->db->get()->row();
        $response["QcParameterDetails"] = $QCParameterValue;
        if($QCParameterValue){
            $this->db->select('tbldeduction_matrix.*');
            $this->db->from(db_prefix() . 'deduction_matrix');
            $this->db->where('tbldeduction_matrix.ItemID',$ItemID);
            $this->db->where('tbldeduction_matrix.ItemParameterID',$QCparameterID);
            $deduction_matrix = $this->db->get()->result_array();
            $response["deduction_matrix"] = $deduction_matrix;
        }
        return $response;
    }
//================ Competitor Code =============================================

    public function GetAllCompetitor()
    {
        $this->db->select('tblCompetitorMaster.*');
        $this->db->where_not_in('tblCompetitorMaster.CompetitorID', 'C01');
        $this->db->where('tblCompetitorMaster.Type', 'C');
        return $this->db->get('tblCompetitorMaster')->result_array();
    }

    public function GetAllMandi()
    {
        $this->db->select('tblCompetitorMaster.*');
        $this->db->where_not_in('tblCompetitorMaster.CompetitorID', 'C01');
        $this->db->where_not_in('tblCompetitorMaster.CompetitorID', 'C02');
        $this->db->where('tblCompetitorMaster.Type', 'M');
        return $this->db->get('tblCompetitorMaster')->result_array();
    }
//================ Competitor Code End =========================================
//===================== deduction matrix end ===================================

    public function GetItemWiseCenter($ItemID)
    {
        $UserID = $this->session->userdata('username');

        $this->db->select('tblCenter_wise_item.CenterID,tblCenterMaster.CenterName');
        $this->db->where_in(db_prefix() . 'Center_wise_item.ItemID', $ItemID);
        $this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
        if(!is_admin()){
            $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
            $this->db->where(db_prefix() . 'staff_wise_center.AccountID', $UserID);
        }
        $this->db->join(db_prefix() . 'Centerwise_staff_priority', '' . db_prefix() . 'Centerwise_staff_priority.CenterID = ' . db_prefix() . 'CenterMaster.CenterID AND tblCenterwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
        $this->db->where(db_prefix() . 'CenterMaster.status', 'Y');
        $this->db->group_by('tblCenter_wise_item.CenterID');
        $this->db->order_by('tblCenterwise_staff_priority.priority','ASC');
        return $this->db->get('tblCenter_wise_item')->result_array();
    }

    public function GetItemWiseCity($ItemID, $rateType = 'T')
    {
        $UserID = $this->session->userdata('username');

        $this->db->select(
            'tblCenterMaster.city AS CityID, tblxx_citylist.city_name AS CityName, COUNT(DISTINCT tblCenter_wise_item.CenterID) AS CenterCount, GROUP_CONCAT(DISTINCT tblCenter_wise_item.CenterID ORDER BY tblCenterMaster.CenterName SEPARATOR ",") AS CenterIDs, GROUP_CONCAT(DISTINCT tblCenterMaster.CenterName ORDER BY tblCenterMaster.CenterName SEPARATOR ", ") AS CenterNames',
            false
        );
        $this->db->from(db_prefix() . 'Center_wise_item');
        $this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
        $this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'CenterMaster.city', 'LEFT');
        $this->db->where(db_prefix() . 'Center_wise_item.ItemID', $ItemID);
        $this->db->where(db_prefix() . 'CenterMaster.status', 'Y');
        $this->db->where(db_prefix() . 'CenterMaster.city IS NOT NULL', null, false);
        $this->db->where(db_prefix() . 'CenterMaster.city !=', '');

        if (!is_admin()) {
            $this->db->join(db_prefix() . 'staff_wise_center', db_prefix() . 'staff_wise_center.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
            $this->db->where(db_prefix() . 'staff_wise_center.AccountID', $UserID);
        }

        $this->db->group_by('tblCenterMaster.city');
        $this->db->order_by('tblxx_citylist.city_name', 'ASC');
        $cities = $this->db->get()->result_array();

        foreach ($cities as $key => $city) {
            $centerIds = array_filter(explode(',', $city['CenterIDs']));
            $cities[$key]['CurrentRate'] = '';
            if (!empty($centerIds)) {
                $this->db->select('Rate');
                $this->db->from(db_prefix() . 'RateMaster');
                $this->db->where('ItemID', $ItemID);
                $this->db->where_in('CenterID', $centerIds);
                $this->db->where('KeyID', 'C01');
                $this->db->where('Type', $rateType);
                $this->db->where('IsActive', 'Y');
                $rates = $this->db->get()->result_array();
                $uniqueRates = array_unique(array_column($rates, 'Rate'));
                if (count($uniqueRates) === 1 && $uniqueRates[0] !== '') {
                    $cities[$key]['CurrentRate'] = $uniqueRates[0];
                }
            }
        }

        return $cities;
    }

    public function GetCenterIDsByCityAndItem($ItemID, $CityID)
    {
        $UserID = $this->session->userdata('username');

        $this->db->select('tblCenter_wise_item.CenterID');
        $this->db->from(db_prefix() . 'Center_wise_item');
        $this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
        $this->db->where(db_prefix() . 'Center_wise_item.ItemID', $ItemID);
        $this->db->where(db_prefix() . 'CenterMaster.city', $CityID);
        $this->db->where(db_prefix() . 'CenterMaster.status', 'Y');

        if (!is_admin()) {
            $this->db->join(db_prefix() . 'staff_wise_center', db_prefix() . 'staff_wise_center.CenterID = ' . db_prefix() . 'Center_wise_item.CenterID');
            $this->db->where(db_prefix() . 'staff_wise_center.AccountID', $UserID);
        }

        $this->db->group_by('tblCenter_wise_item.CenterID');
        $result = $this->db->get()->result_array();

        return array_column($result, 'CenterID');
    }

    public function GetAllcenter_staff_wise($AccountID)
    {
        $this->db->select('tblstaff_wise_center.*');
		$this->db->from(db_prefix() . 'staff_wise_center');
		$this->db->where(db_prefix() . 'staff_wise_center.AccountID ',$AccountID);
		$this->db->order_by( db_prefix() .'staff_wise_center.CenterID','ASC');
		return $this->db->get()->result_array();
    }

    public function getCenter()
    {
        $UserID = $this->session->userdata('username');
        $GetAllCenter = $this->GetAllcenter_staff_wise($UserID);
        $centerIDs = array();
        foreach($GetAllCenter as $val){
            array_push($centerIDs,$val["CenterID"]);
        }
        $this->db->select('tblCenterMaster.*');
        if(!is_admin() && !empty($centerIDs)){
            $this->db->where_in(db_prefix() . 'CenterMaster.CenterID ',$centerIDs);
        }
        $this->db->where(db_prefix() . 'CenterMaster.status ','Y');
        $this->db->join(db_prefix() . 'Centerwise_staff_priority', '' . db_prefix() . 'Centerwise_staff_priority.CenterID = ' . db_prefix() . 'CenterMaster.CenterID AND tblCenterwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
        $this->db->order_by('tblCenterwise_staff_priority.priority','ASC');
        return $this->db->get('tblCenterMaster')->result_array();
    }
    public function GetItem_Staff_wise()
    {
        $UserID = $this->session->userdata('username');
        if(!is_admin()){

            $this->db->select('tblstaff_wise_items.ItemID,tblitems.ItemName,tblitems.unit');
    		$this->db->from(db_prefix() . 'staff_wise_items');
    		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'staff_wise_items.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');
    		$this->db->join(db_prefix() . 'Itemwise_staff_priority', '' . db_prefix() . 'Itemwise_staff_priority.ItemID = ' . db_prefix() . 'staff_wise_items.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
    		$this->db->where(db_prefix() . 'staff_wise_items.AccountID ',$UserID);
    		$this->db->where(db_prefix() . 'items.isactive ','Y');
    		$this->db->order_by( db_prefix() .'Itemwise_staff_priority.priority','ASC');
    		return $this->db->get()->result_array();
        }else{
            $this->db->select('tblitems.*');
            $this->db->from(db_prefix() . 'items');
            $this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id');
            $this->db->join(db_prefix() . 'Itemwise_staff_priority', '' . db_prefix() . 'Itemwise_staff_priority.ItemID = ' . db_prefix() . 'items.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"',"LEFT");
            $this->db->where(db_prefix() . 'items_sub_groups.main_group_id','3');
            $this->db->where(db_prefix() . 'items.isactive ','Y');
            $this->db->order_by( db_prefix() .'Itemwise_staff_priority.priority','ASC');
            return $this->db->get()->result_array();
        }
    }

    public function GetItem_Staff_wisePriority()
    {
        $UserID = $this->session->userdata('username');
        $this->db->select('tblItemwise_staff_priority.*');
        $this->db->from(db_prefix() . 'Itemwise_staff_priority');
        $this->db->where(db_prefix() . 'Itemwise_staff_priority.staff_id',$UserID);
        $this->db->order_by('id');
        return $this->db->get()->result_array();
    }

    public function GetCenter_Staff_wisePriority()
    {
        $UserID = $this->session->userdata('username');
        $this->db->select('tblCenterwise_staff_priority.*');
        $this->db->from(db_prefix() . 'Centerwise_staff_priority');
        $this->db->where(db_prefix() . 'Centerwise_staff_priority.staff_id',$UserID);
        $this->db->order_by('id');
        return $this->db->get()->result_array();
    }

    public function GetItemGroup_Staff_wise()
    {
        if(is_admin()){
            $this->db->select('tblitems_sub_groups.name,tblitems_sub_groups.id,tblitems_sub_groups.ShortCode');
            $this->db->distinct();
            $this->db->from(db_prefix() . 'items_sub_groups');
            $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.subgroup_id = ' . db_prefix() . 'items_sub_groups.id');
            $this->db->where(db_prefix() . 'items_sub_groups.main_group_id','3');
            $this->db->where(db_prefix() . 'items.isactive ','Y');
            $this->db->order_by('id');
            return $this->db->get()->result_array();
        }else{
            $UserID = $this->session->userdata('username');
            $this->db->select('tblitems_sub_groups.name,tblitems_sub_groups.id,tblitems_sub_groups.ShortCode');
            $this->db->distinct();
    		$this->db->from(db_prefix() . 'staff_wise_items');
    		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = ' . db_prefix() . 'staff_wise_items.ItemID');
    		$this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id');
    		$this->db->where(db_prefix() . 'staff_wise_items.AccountID ',$UserID);
    		$this->db->where(db_prefix() . 'items.isactive ','Y');
    		$this->db->order_by( db_prefix() .'items.id','ASC');
    		return $this->db->get()->result_array();
        }
    }



    public function GetCenterWiseCommodity()
    {
        $this->db->select('tblCenter_wise_item.*');
		$this->db->from(db_prefix() . 'Center_wise_item');
		$this->db->order_by( db_prefix() .'Center_wise_item.CenterID','ASC');
		return $this->db->get()->result_array();
    }


    public function GetCompetitor()
    {
        $this->db->select('tblCompetitorMaster.*');
        $this->db->where('tblCompetitorMaster.CompetitorID', 'C01');
        return $this->db->get('tblCompetitorMaster')->result_array();
    }

    public function GetCharges(){
        $this->db->select('tblCharges.*');
        $this->db->order_by('tblCharges.TransDate', 'DESC');
        $this->db->where('tblCharges.IsActive', 'Y');
        return $this->db->get('tblCharges')->result_array();
    }

    public function GetRate()
    {
        $this->db->select('tblRateMaster.*,Center_wise_item.TradeOnOff,Center_wise_item.TradeOnOffFarmer');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where('tblRateMaster.KeyID', 'C01');
        $this->db->where('tblRateMaster.Type', 'T');
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'RateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'RateMaster.CenterID');
        return $this->db->get('tblRateMaster')->result_array();
    }
    public function GetUpdatedRateMSP()
    {

        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblRateMaster.*,tblstaff.firstname');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where('tblRateMaster.KeyID', 'C01');
        $this->db->where('tblRateMaster.Type', 'MSP');
        $this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.AccountID = ' . db_prefix() . 'RateMaster.UserID AND ' . db_prefix() . 'staff.PlantID = ' . $selected_company .'', 'left');
        return $this->db->get('tblRateMaster')->result_array();
    }

    public function GetFarmerRate()
    {
        $this->db->select('tblRateMaster.*,Center_wise_item.TradeOnOff,Center_wise_item.TradeOnOffFarmer');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where('tblRateMaster.KeyID', 'C01');
        $this->db->where('tblRateMaster.Type', 'F');
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'RateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'RateMaster.CenterID', 'left');
        return $this->db->get('tblRateMaster')->result_array();
    }
    public function GetCompetitorRate()
    {
        $Type_array = array("C","N");
        $this->db->select('tblRateMaster.*,Center_wise_item.TradeOnOff,Center_wise_item.TradeOnOffFarmer');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where_not_in('tblRateMaster.KeyID', 'C01');
        $this->db->where_in('tblRateMaster.Type', $Type_array);
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'RateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'RateMaster.CenterID', 'left');
        return $this->db->get('tblRateMaster')->result_array();
    }
    public function GetMandiRate()
    {
        $Type_array = array("M");
        $this->db->select('tblRateMaster.*,Center_wise_item.TradeOnOff,Center_wise_item.TradeOnOffFarmer');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where_not_in('tblRateMaster.KeyID', 'C01');
        $this->db->where_in('tblRateMaster.Type', $Type_array);
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'RateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'RateMaster.CenterID', 'left');
        return $this->db->get('tblRateMaster')->result_array();
    }

    public function GetSaleRate()
    {
        $this->db->select('tblSaleRateMaster.*,Center_wise_item.SaleTradeOnOff');
        $this->db->order_by('tblSaleRateMaster.TransDate', 'DESC');
        $this->db->where('tblSaleRateMaster.IsActive', 'Y');
        $this->db->where('tblSaleRateMaster.KeyID', 'C01');
        $this->db->join(db_prefix() . 'Center_wise_item', '' . db_prefix() . 'Center_wise_item.ItemID = ' . db_prefix() . 'SaleRateMaster.ItemID AND ' . db_prefix() . 'Center_wise_item.CenterID = ' . db_prefix() . 'SaleRateMaster.CenterID', 'left');
        return $this->db->get('tblSaleRateMaster')->result_array();
    }

	public function GetMSPRate($ItemID)
    {
        $this->db->select('tblRateMaster.*');
        $this->db->order_by('tblRateMaster.TransDate', 'DESC');
        $this->db->where('tblRateMaster.IsActive', 'Y');
        $this->db->where('tblRateMaster.KeyID', 'C01');
        $this->db->where('tblRateMaster.Type', 'MSP');
        $this->db->where('tblRateMaster.ItemID', $ItemID);
        return $this->db->get('tblRateMaster')->row();
    }

    public function get($id = '',$state_id = '',$distributor_id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        $this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid, rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,r1.assigned_rate as new_rate,r1.id as rate_master_id,
            description,long_description,group_id,item_code,subgroup_id,' . db_prefix() . 'items_groups.name as group_name, '. db_prefix() . 'items_sub_groups.name as subgroup_name,unit');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'rate_master r1', 'r1.item_id = ' . db_prefix() . 'items.item_code', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id', 'left');
        $this->db->order_by('description', 'asc');
        //if (is_numeric($id)) {
            $this->db->where('item_code', $id);
            $selected_company = $this->session->userdata('root_company');
            $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
            $this->db->where('r1.PlantID', $selected_company);
            $this->db->where('r1.state_id', $state_id);
            $this->db->where('r1.distributor_id', $distributor_id);

            return $this->db->get()->row();
        //}

        //return $this->db->get()->result_array();
    }

    public function table_data($data){

        $state_id = $data['state_id'];
        $distributor_id = $data['distributor_id'];
        $this->db->select(db_prefix() . 'items.id as itemid, rate,
            t1.name as taxname_1,t1.id as tax_id_1,t1.taxrate as taxrate, t3.assigned_rate as assigned_2,t3.item_id as rate_id, t3.item_id as item_id_2,t3.id as rate_master_id,  t3.state_id as state_id_2, t3.distributor_id as distributor_id_2, t3.groups_id as groups_id_2,
            description,long_description,group_id,item_code,subgroup_id,' . db_prefix() . 'items_groups.name as group_name, '. db_prefix() . 'items_sub_groups.name as subgroup_name,unit,t3.effective_date as effective_date_2');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'rate_master t3', 't3.item_id = ' . db_prefix() . 'items.item_code', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id', 'left');
        $this->db->order_by('description', 'asc');
        //if (is_numeric($id)) {
            // $this->db->where('item_code', $id);
            $selected_company = $this->session->userdata('root_company');
            $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
            $this->db->where('t3.PlantID', $selected_company);
            $this->db->where('t3.state_id', $state_id);
            $this->db->where('t3.distributor_id', $distributor_id);
            $this->db->order_by(db_prefix() . 'items.item_code', "ASC");

            return $this->db->get()->result_array();

    }

    public function get_grouped()
    {
        $items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);

        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get(db_prefix() . 'items')->result_array();
            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

     public function get_state()
    {
        $this->db->select('*');
        $this->db->where('country_id', '1');
        $this->db->from(db_prefix() . 'xx_statelist');
        $this->db->order_by('state_name', 'ASC');

        return $this->db->get()->result_array();
    }

     public function get_rate_master_data_by_id($state_id,$distributor_id)
    {
        $this->db->select('*');
        $this->db->where('state_id', $state_id);
        $this->db->where('distributor_id', $distributor_id);
        $this->db->from(db_prefix() . 'rate_master');
        //$this->db->order_by('name', 'ASC');

        return $this->db->get()->row_array();
    }

     /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add_rate_master($data)
    {

        $this->db->insert(db_prefix() . 'rate_master', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {


            hooks()->do_action('item_created', $insert_id);

            log_activity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit_rate_master($data)
    {
        $state_id = $data['state_id'];
        $distributor_id = $data['distributor_id'];
        $id = $data['rate_master_id'];
        $item_code = $data['item_code'];
        unset($data["item_code"]);
        unset($data["state_id"]);
        unset($data["distributor_id"]);
        unset($data['rate_master_id']);

        $data = hooks()->apply_filters('before_update_item', $data, $itemid);
        $user_id = $this->session->userdata('username');
        $selected_company = $this->session->userdata('root_company');
        $data["UserID2"] = $user_id;
        $data["Lupdate"] = date('Y-m-d H:i:s');
        $this->db->select('*');
        $this->db->where('state_id', $state_id);
        $this->db->where('distributor_id', $distributor_id);
        $this->db->where('id', $id);
        $this->db->from(db_prefix() . 'rate_master');
        $data_rate_master =  $this->db->get()->row_array();

        $data_insert = array(
            'PlantID' => $data_rate_master['PlantID'],
            'DistributorType' => $data_rate_master['distributor_id'],
            'ItemID' => $data_rate_master['item_id'],
            'BasicRate' => $data_rate_master['assigned_rate'],
            'SaleRate' => $data_rate_master['SaleRate'],
            'EffDate' => $data_rate_master['effective_date'],
            'UserId' => $data_rate_master['UserId'],
            'StateID' => $data_rate_master['state_id'],
            'gst' => $data_rate_master['gst'],
            'UserID2' => $user_id,
            'Lupdate' => date('Y-m-d H:i:s'),
            );

            $gst_amt = ($data['assigned_rate'] /100) * $data_rate_master['gst'];
           $data["SaleRate"] = $gst_amt + $data['assigned_rate'];
           $data["Lupdate"] = $data["effective_date"];
           $data["UserID2"] = $this->session->userdata('username');

        $this->db->where('state_id', $state_id);
        $this->db->where('distributor_id', $distributor_id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'rate_master', $data);

        if ($this->db->affected_rows() > 0) {

              $this->db->insert(db_prefix() . 'ratehistory2', $data_insert);

            log_activity('Invoice Item Updated [ID: ' . $rate_master_id . ', ' . $data['description'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['itemid']);
        if ($data['tax'] == '') {
            unset($data['tax']);
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
            unset($data['tax2']);
        }

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields(db_prefix() . 'items');
        $this->load->dbforge();
        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
                $field = [
                        $column => [
                            'type' => 'decimal(15,' . get_decimal_places() . ')',
                            'null' => true,
                        ],
                ];
                $this->dbforge->add_column('items', $field);
            }
        }

        $this->db->insert(db_prefix() . 'items', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields, true);
            }

            hooks()->do_action('item_created', $insert_id);

            log_activity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
        unset($data['itemid']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['tax']) && $data['tax'] == '') {
            $data['tax'] = null;
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
            $data['tax2'] = null;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $columns = $this->db->list_fields(db_prefix() . 'items');
        $this->load->dbforge();

        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
                $field = [
                        $column => [
                            'type' => 'decimal(15,' . get_decimal_places() . ')',
                            'null' => true,
                        ],
                ];
                $this->dbforge->add_column('items', $field);
            }
        }

        $affectedRows = 0;

        $data = hooks()->apply_filters('before_update_item', $data, $itemid);

        $this->db->where('id', $itemid);
        $this->db->update(db_prefix() . 'items', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
            $affectedRows++;
        }

        if (isset($custom_fields)) {
            if (handle_custom_fields_post($itemid, $custom_fields, true)) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            hooks()->do_action('item_updated', $itemid);
        }

        return $affectedRows > 0 ? true : false;
    }

    public function search($q)
    {
        $this->db->select('rate, id, description as name, long_description as subtext');
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
            $items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' . $item['name'];
        }

        return $items;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'rate_master');
        if ($this->db->affected_rows() > 0) {
            /*$this->db->where('relid', $id);
            $this->db->where('fieldto', 'items_pr');
            $this->db->delete(db_prefix() . 'customfieldsvalues');*/

            log_activity('Rate Item Deleted [ID: ' . $id . ']');

            hooks()->do_action('item_deleted', $id);

            return true;
        }

        return false;
    }

    public function get_groups()
    {
        //$selected_company = $this->session->userdata('root_company');
        //$this->db->where('PlantID', $selected_company);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_groups')->result_array();
    }

    public function add_group($data)
    {
        $this->db->insert(db_prefix() . 'items_groups', $data);
        log_activity('Items Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function edit_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Items Group Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'items_groups')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update(db_prefix() . 'items', [
                'group_id' => 0,
            ]);

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'items_groups');

            log_activity('Item Group Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
    }



    public function get_main_groups()
    {
        //$selected_company = $this->session->userdata('root_company');
         //$this->db->where('PlantID', $selected_company);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_main_groups')->result_array();
    }

    public function add_main_group($data)
    {
        $this->db->insert(db_prefix() . 'items_main_groups', $data);
        log_activity('Items Main Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function edit_main_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'items_main_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Items Main Group Updated [Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

     public function delete_main_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'items_main_groups')->row();

        if ($group) {
            /*$this->db->where('group_id', $id);
            $this->db->update(db_prefix() . 'items', [
                'group_id' => 0,
            ]);
*/
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'items_main_groups');

            log_activity('Item Main Group Deleted [Name: ' . $group->name . ']');

            return true;
        }

        return false;
    }

    public function get_sub_groups()
    {
        //$selected_company = $this->session->userdata('root_company');
        //$this->db->where('PlantID', $selected_company);
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'items_sub_groups')->result_array();
    }

    public function add_sub_group($data)
    {
        $this->db->insert(db_prefix() . 'items_sub_groups', $data);
        log_activity('Items Sub Group Created [Name: ' . $data['name'] . ']');

        return $this->db->insert_id();
    }

    public function addDeductionMatrix($data){
        $this->db->insert(db_prefix() . 'deduction_matrix', $data);
    }

    public function deleteDeductionMatrixEntry($ItemID, $ItemParameterID){
        $this->db->where('ItemID', $ItemID);
        $this->db->where('ItemParameterID', $ItemParameterID);
        $this->db->delete(db_prefix() . 'deduction_matrix');
    }

		public function GetBankAccounts()
		{
			$subgroup = array('1000017');
			$NotAccounts = array('CASH');

			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
			$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID',$subgroup);
			$this->db->where_not_in(db_prefix() . 'clients.AccountID',$NotAccounts);
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			$list_accounts = [];

			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].' - '.$account['AccountID'];

				$list_accounts[] = $note;
			}
			return $list_accounts;
		}
	public function GetPendingStatement($BankAccount)
	{
        $fy = $this->session->userdata('finacial_year');
        $start_date = $fy.'-04-01 00:00:00';
        $end_date = ($fy+1).'-03-31 23:59:59';

		$selected_company = $this->session->userdata('root_company');
		$this->db->select('tblimport_statement.*');
		$this->db->where('tblimport_statement.AccountID', $BankAccount);
		$this->db->where('tblimport_statement.Status', 'N');
		$this->db->where('tblimport_statement.value_date >=', $start_date);
		$this->db->where('tblimport_statement.value_date <=', $end_date);
		return $this->db->get(db_prefix() . 'import_statement')->result_array();
	}
	public function GetPendingPaymentVoucher()
	{
		$selected_company = $this->session->userdata('root_company');
		$this->db->select('tblaccountledger.*');
		$this->db->where('tblaccountledger.PassedFrom', "PAYMENTS");
		$this->db->where('tblaccountledger.reconcile_status', 'N');
		$this->db->where('tblaccountledger.TType', 'D');
		$this->db->where('tblaccountledger.ref_no IS NOT NULL');
		return $this->db->get(db_prefix() . 'accountledger')->result_array();
	}

		public function get_data_ganeral_account_to_select()
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $fy = $this->session->userdata('finacial_year');
			$subgroup = array('1000017');
			$this->db->where('PlantID', $selected_company);
			$this->db->where_not_in('SubActGroupID',$subgroup);
			$this->db->order_by('company', 'ASC');
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			$list_accounts = [];

			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].' - '.$account['AccountID'];

				$list_accounts[] = $note;
			}
			return $list_accounts;
		}

		public function GetLedgerAccountListAll(){
            $selected_company = $this->session->userdata('root_company');
            $FY = $fy = $this->session->userdata('finacial_year');
            $this->db->select('AccountID as id, company as label');
            $this->db->where('PlantID', $selected_company);
            $this->db->order_by('company', 'ASC');
            $accounts = $this->db->get(db_prefix() . 'clients')->result_array();
            return $accounts;
        }

//======================== Show only Accounts those are availabel in ledger table ======
	public function GetLedgerAccountList()
	{
		$selected_company = $this->session->userdata('root_company');
		$FY = $fy = $this->session->userdata('finacial_year');
		$subgroup = array('1000017');
		$this->db->select('tblaccountledger.AccountID,tblclients.company');
		$this->db->join('tblclients','tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID');
		$this->db->where('tblaccountledger.PlantID', $selected_company);
		$this->db->group_by('tblaccountledger.AccountID');
		$this->db->order_by('tblclients.company', 'ASC');
		$accounts = $this->db->get(db_prefix() . 'accountledger')->result_array();
		$list_accounts = [];

		foreach ($accounts as $key => $account) {
			$note = [];
			$note['id'] = strtoupper($account['AccountID']);
			$note['label'] = $account['company'].' - '.$account['AccountID'];

			$list_accounts[] = $note;
		}
		return $list_accounts;
	}

		public function GetStatementImportedDataByids($entryids)
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblimport_statement.*');
			$this->db->where_in('tblimport_statement.id', $entryids);
			$this->db->where('tblimport_statement.Status', 'N');
			return $this->db->get(db_prefix() . 'import_statement')->row_array();
		}

		 public function GetLastUniqueNo(){

        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');

        $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'"  GROUP BY UniquID ORDER BY abs(tblaccountledger.UniquID) DESC ';
        $UniqueID = $this->db->query($sql)->result_array();
        return $UniqueID;

    }
		public function get_result_to_cur_date_receipts($receipts_date){

        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        /*$this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->LIKE('PassedFrom', "RECEIPTS");
        $this->db->where('Transdate >', $receipts_date);
        $this->db->order_by("VoucherID", "desc");
        $journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
        return $journal_data;*/

        $fy_ne = $fy + 1;
        $las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
        $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "RECEIPTS" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$receipts_date.' H:i:m" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
        $receipts_data = $this->db->query($sql)->result_array();
        return $receipts_data;

    }

    public function nextVoucherID($type, $date){
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');

        $this->db->select_max('VoucherID');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('PassedFrom', $type);
        $this->db->where('FY', $fy);

        $row = $this->db->get(db_prefix() . 'accountledger')->row();

        return ($row && $row->VoucherID) ? $row->VoucherID + 1 : 1;
    }

		 public function increment_next_receipts_number()
    {
        // Update next receipts number in settings
        $FY = $this->session->userdata('finacial_year');
        $this->db->where('name', 'next_receipts_number_for_kirti');
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }

		 public function get_result_to_cur_date_payments($payment_date)
    {

        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');

        $fy_ne = $fy + 1;
        $las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
        $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$payment_date.' H:i:s" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
        $staff_data = $this->db->query($sql)->result_array();
        return $staff_data;

    }

    public function generateNextVoucherIDNew($selected_date = '', $plant_id = '', $passage_from = '')
    {
		if(empty($selected_date)){
		$selected_date = date('Y-m-d');
		}
		
		if(empty($plant_id)){
		$plant_id = $this->session->userdata('root_company');
		}
		
		// Extract date components
		$date_parts = explode('-', $selected_date);
		$year = substr($date_parts[0], 2);
		$month = $date_parts[1];
		$day = $date_parts[2];
		
		$plant_id_formatted = str_pad($plant_id, 2, '0', STR_PAD_LEFT);
		
			switch (strtoupper($passage_from)) {
				case 'JOURNAL':
					$prefix = 'J';
					break;
				case 'RECEIPTS':
					$prefix = 'R';
					break;
				case 'PAYMENTS':
					$prefix = 'P';
					break;
				default:
					$prefix = 'C';
					break;
			}
		
		// Build base: J0126040300001 or C0126040300001
		$voucher_base = $prefix . $plant_id_formatted . $year . $month . $day;
		
		$sql = "SELECT VoucherID 
				FROM " . db_prefix() . "accountledger 
				WHERE PlantID = " . (int)$plant_id . " 
				AND PassedFrom = '" . $this->db->escape_str(strtoupper($passage_from)) . "' 
				AND DATE(Transdate) = '" . $this->db->escape_str($selected_date) . "' 
				AND VoucherID LIKE '" . $this->db->escape_like_str($voucher_base) . "%'
							ORDER BY CAST(RIGHT(VoucherID, 3) AS UNSIGNED) DESC
							LIMIT 1";
		
		$query = $this->db->query($sql);
		$row = $query->row_array();

			if (!empty($row['VoucherID'])) {
				$lastNumber = (int) substr($row['VoucherID'], -3);
				$nextNumber = $lastNumber + 1;
			} else {
				$nextNumber = 1;
			}
		$new_voucher_number = $voucher_base . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
		return $new_voucher_number;
	}
    
	public function increment_next_payment_number()
    {
        // Update next CHALLAN number in settings
       $FY = $this->session->userdata('finacial_year');
       $selected_company = $this->session->userdata('root_company');
            if($selected_company == 1){
                $this->db->where('name', 'next_payment_number_for_kirti');
            }
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }
}
