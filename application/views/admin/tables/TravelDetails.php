<?php

defined('BASEPATH') or exit('No direct script access allowed');
$selected_company = $this->ci->session->userdata('root_company');
$hasPermissionDelete = has_permission('customers', '', 'delete');

$custom_fields = get_table_custom_fields('customers');
$this->ci->db->query("SET sql_mode = ''");

$aColumns = [
    'id',
    'staffid',
    'TransDate',
    'type_check','latitude','longitude','address','battery_level','device_information','GPS_Status'
];

$sIndexColumn = 'id';
$sTable       = db_prefix().'LocationTracking';
$where        = [];
// Add blank where all filter can be stored
$filter = [];


$join = [];


$join = hooks()->apply_filters('customers_table_sql_join', $join);



if ($this->ci->input->post('staff')) {
    array_push($filter, 'AND staffid ='. $this->ci->input->post('staff'));
}


if ($this->ci->input->post('report_date')) {
    $dd= _d($this->ci->input->post('report_date'));
    $date = to_sql_date($this->ci->input->post('report_date'));
    array_push($filter, 'AND TransDate BETWEEN "'.$date.' 00:00:00" AND "'.$date.' 23:59:59"');
}


if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}


$aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $full_name = get_staff_name($aRow['staffid']);
    // $name = $full_name->firstname .' '.$full_name->lastname;
    // $row[] = $name;
    $row[] = $aRow['address'];
    $row[] = $aRow['battery_level'];
    $row[] = $aRow['device_information'];
    $row[] = $aRow['GPS_Status'];
    $row[] = substr($aRow['TransDate'],10,9);
    // $row[] = _d(substr($aRow['TransDate'],0,10));
    
   
    $row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
