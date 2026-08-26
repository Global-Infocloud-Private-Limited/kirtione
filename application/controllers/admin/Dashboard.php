<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
    }

    /* This is admin dashboard view */
    public function index()
    {
        close_setup_menu();
        $this->load->model('departments_model');
        $this->load->model('todo_model');
        $data['departments'] = $this->departments_model->get();

        $data['todos'] = $this->todo_model->get_todo_items(0);
        // Only show last 5 finished todo items
        $this->todo_model->setTodosLimit(5);
        $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        $data['upcoming_events_next_week'] = $this->dashboard_model->get_upcoming_events_next_week();
        $data['upcoming_events']           = $this->dashboard_model->get_upcoming_events();
        $data['title']                     = _l('dashboard_string');

        $this->load->model('contracts_model');
        $data['expiringContracts'] = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());

        $this->load->model('currencies_model');
        $data['currencies']    = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['activity_log']  = $this->misc_model->get_activity_log();
        // Tickets charts
        $tickets_awaiting_reply_by_status     = $this->dashboard_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->dashboard_model->tickets_awaiting_reply_by_department();

        $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);

        $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;

        $data['projects_status_stats'] = json_encode($this->dashboard_model->projects_status_stats());
        $data['leads_status_stats']    = json_encode($this->dashboard_model->leads_status_stats());
        $data['google_ids_calendars']  = $this->misc_model->get_google_calendar_ids();
        $data['bodyclass']             = 'dashboard invoices-total-manual';
        $this->load->model('announcements_model');
        $data['staff_announcements']             = $this->announcements_model->get();
        $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        $this->load->model('projects_model');
        $data['projects_activity'] = $this->projects_model->get_activity('', hooks()->apply_filters('projects_activity_dashboard_limit', 20));
        add_calendar_assets();
        $this->load->model('utilities_model');
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $wps_currency = 'undefined';
        if (is_using_multiple_currencies()) {
            $wps_currency = $data['base_currency']->id;
        }
        $data['weekly_payment_stats'] = json_encode($this->dashboard_model->get_weekly_payments_statistics($wps_currency));

        $data['dashboard'] = true;

        $data['user_dashboard_visibility'] = get_staff_meta(get_staff_user_id(), 'dashboard_widgets_visibility');

        if (!$data['user_dashboard_visibility']) {
            $data['user_dashboard_visibility'] = [];
        } else {
            $data['user_dashboard_visibility'] = unserialize($data['user_dashboard_visibility']);
        }
        $data['user_dashboard_visibility'] = json_encode($data['user_dashboard_visibility']);
        $data['rootcompany'] = $this->clients_model->get_rootcompany();
        
        $this->load->model('SaleDashboard_model');
        $this->load->model('rate_master_model');
        $data['AllCenter'] = $this->SaleDashboard_model->GetAllCenter();
        $data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();
        $data['CenterWiseItem'] = $this->SaleDashboard_model->GetCenterWiseItem();
        $data['ItemWiseCenterWisePurchase'] = $this->SaleDashboard_model->GetItemWiseCenterWisePurchase();
        $data['ItemWiseCenterWiseTodayPurchase'] = $this->SaleDashboard_model->GetItemWiseCenterWiseTodayPurchase();
        $data['ItemWiseCenterWiseCurrentRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseCurrentRate();
        $data['ItemWiseCenterWiseAvgRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseAvgRate();
        $data['ItemWiseCenterWiseCurrentAvgRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseCurrentAvgRate();
        
        $data['kirtiPurchasetraderCheck'] = $this->SaleDashboard_model->statusCheck();
        $data['kirtiPurchasefarmerCheck'] = $this->SaleDashboard_model->statusCheck1();
        $data['kirtiPurchasesaleCheck'] = $this->SaleDashboard_model->statusCheck2();
        
        $data = hooks()->apply_filters('before_dashboard_render', $data);
        $this->load->view('admin/dashboard/dashboard', $data);
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->dashboard_model->get_weekly_payments_statistics($currency));
            die();
        }
    }
   
    
    public function SetCompanySession($plant_fy)
    {
        $aa = explode("-",$plant_fy);
        
        $plant_id= $aa[1];
        $year = $aa[0];
        $compdata = array(
            'root_company'  => $plant_id,
            'finacial_year'  => $year
        );
        $this->session->set_userdata($compdata);
        redirect(admin_url());
    }
    
    /* Company ID add in Session array */
    public function change_company($company_id)
    {
        //array_push_assoc($user_data, 'root_company', $id);
        //$data = $this->input->post();
        
        $aa = explode("-",$company_id);
        
        $plant_id= $aa[1];
        $year = $aa[0];
        //die;
        $compdata = array(
                   'root_company'  => $plant_id,
                   'finacial_year'  => $year
               );
        $this->session->set_userdata($compdata);
        //return true;
        //echo json_encode($data["id"]);
        redirect(admin_url());
       // header('Location: '.$_SERVER['REQUEST_URI']);

    }
}
