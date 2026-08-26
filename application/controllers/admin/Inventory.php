<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a purchase.
 */
class Inventory extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
        $this->load->model('order_model');
        //$this->load->model('purchase_model');
    }
    public function index(){
        ItemIssue();
    }
    public function ItemIssue()
    {
        if (!has_permission_new('ItemIssue', '', 'view')) {
            access_denied('purchase_order');
        }
                
        if ($this->input->post()) {
            $pur_order_data = $this->input->post();
            $pur_order_data['terms'] = nl2br($pur_order_data['terms']);
            if ($id == '') {
                if (!has_permission_new('ItemIssue', '', 'create')) {
                    access_denied('purchase_order');
                }
                $id = $this->inventory_model->add_issue_order($pur_order_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Issue Items'));
                    redirect(admin_url('inventory/ItemIssue'));
                }
            }
        }

        $title = "Goods Issue";
        
        // New Function 
        $data['CenterList'] = $this->order_model->GetCenterList();
        $data['ItemList'] = $this->inventory_model->GetItemList();
        $data['PlantList'] = $this->order_model->GetPlantList();
        $data['title'] = $title;
        $this->load->view('admin/Inventory/ItemIssue', $data);
    }
}