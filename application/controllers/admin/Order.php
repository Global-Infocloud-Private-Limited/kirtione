<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Order extends AdminController

{

  public function __construct()

  {

    parent::__construct();

    $this->load->model('invoices_model');

    $this->load->model('order_model');

    $this->load->model('GateControl_model');

    $this->load->model('sale_reports_model');

    $this->load->model('credit_notes_model');

    $this->load->model('inventory_model');



    $this->pusher_options['app_key'] = get_option('pusher_app_key');

    $this->pusher_options['app_secret'] = get_option('pusher_app_secret');

    $this->pusher_options['app_id'] = get_option('pusher_app_id');



    if (get_option('pusher_cluster') != '') {

      $this->pusher_options['cluster'] = get_option('pusher_cluster');
    }

    $this->pusher = new Pusher\Pusher(

      $this->pusher_options['app_key'],

      $this->pusher_options['app_secret'],

      $this->pusher_options['app_id'],

      array('cluster' => $this->pusher_options['cluster'])

    );
  }

  //====================== New Code ==============================================



  // Direct Sell Order punch page

  public function index($id = '')

  {

    if (!has_permission_new('orders', '', 'view')) {

      access_denied('order');
    }

    //$this->order();

    $this->sale_invoice();
  }

  public function sale_invoice($id = '')

  {

    if (!has_permission_new('orders', '', 'view')) {

      access_denied('order');
    }

    if ($this->input->post()) {

      $order_data = $this->input->post();

      if ($id == '') {

        if (!has_permission_new('orders', '', 'create')) {

          access_denied('order');
        }

        $id = $this->order_model->AddSaleInvoice($order_data);

        if ($id) {

          set_alert('success', _l('added_successfully', 'Order'));

          //$redUrl = admin_url('order/pending_orders2/' . $id);

          $redUrl = admin_url('order');

          redirect($redUrl);
        }
      }
    }

    $data['CenterList'] = $this->order_model->GetCenterList();

    $data['PlantList'] = $this->order_model->GetPlantList();

    $data['AllBrokerList'] = $this->order_model->GetAllBrokerList();

    $data['PaymentCycleList'] = $this->order_model->GetPaymentCycleList();

    $data['TransportList'] = $this->order_model->GetTransportList();

    $data['StateList'] = $this->order_model->GetStateList();

    $data['ItemList'] = $this->inventory_model->GetItemList();

    $data['title']     = $title;

    $data['bodyclass'] = 'invoice';

    $this->load->view('admin/order/DirectSellOrderPunchUpdate', $data);
  }





  // Direct Sell Order punch page

  public function order($id = '')

  {

    if (!has_permission_new('orders', '', 'view')) {

      access_denied('order');
    }

    if ($this->input->post()) {

      $order_data = $this->input->post();

      if ($id == '') {

        if (!has_permission_new('orders', '', 'create')) {

          access_denied('order');
        }



        $id = $this->order_model->neworderplace($order_data);

        if ($id) {

          set_alert('success', _l('added_successfully', 'Order'));

          $redUrl = admin_url('order/pending_orders2/' . $id);



          if (isset($order_data['save_and_record_payment'])) {

            $this->session->set_userdata('record_payment', true);
          } elseif (isset($order_data['save_and_send_later'])) {

            $this->session->set_userdata('send_later', true);
          }

          redirect($redUrl);
        }
      } else {

        if (!has_permission_new('orders', '', 'edit')) {

          access_denied('order');
        }

        $success = $this->order_model->update($order_data, $id);

        if ($success == false) {

          set_alert('warning', "Stock not available...");

          redirect(admin_url('order/order/' . $id));
        } else {

          set_alert('success', _l('updated_successfully', "Order"));

          redirect(admin_url('order/pending_orders2/' . $id));
        }
      }
    }

    if ($id == '') {

      $title                  = _l('create_new_order');

      $data['billable_tasks'] = [];
    } else {

      $order = $this->order_model->get2($id); // for edit order



      if (!$order) {

        blank_page(_l('order_not_found'));
      }

      $data['order']        = $order;

      $data['edit']           = true;

      $title = "Edit Order";
    }







    $data['CenterList'] = $this->order_model->GetCenterList();

    $data['PlantList'] = $this->order_model->GetPlantList();

    $data['AllBrokerList'] = $this->order_model->GetAllBrokerList();

    $data['PaymentCycleList'] = $this->order_model->GetPaymentCycleList();

    $data['TransportList'] = $this->order_model->GetTransportList();

    $data['StateList'] = $this->order_model->GetStateList();

    $data['title']     = $title;

    $data['bodyclass'] = 'invoice';

    $this->load->view('admin/order/DirectSellOrderPunch', $data);
  }



  public function GetItemDetails()

  {

    // POST data

    $ItemID = $this->input->post('ItemID');

    // Get data

    $data = $this->order_model->GetItemDetailsByItemID($ItemID);

    echo json_encode($data);
  }



  public function ItemDetails($ItemID)

  {

    $data = $this->order_model->GetItemDetailsByItemID($ItemID);

    echo json_encode($data);
  }

  public function itemlist()

  {

    // POST data

    $postData = $this->input->post();

    // Get data

    $data = $this->order_model->getitems($postData);

    echo json_encode($data);
  }



  public function GetWHListByCenterID()

  {

    // POST data

    $CenterID = $this->input->post('CenterID');

    // Get data

    $data = $this->order_model->GetWHListByCenterID($CenterID);

    echo json_encode($data);
  }



  public function GetSaleItemCenterWiseStaffWise()

  {

    // POST data

    $postData = $this->input->post();

    $CenterID = $postData["CenterID"];

    // Get data

    $data = $this->order_model->GetSaleItemCenterWiseStaffWise($CenterID);

    echo json_encode($data);
  }



  /* List all Kirti Sale Requst  */

  public function SellRequest($id = '')

  {

    if (!has_permission_new('Sell_Booking', '', 'view')) {

      access_denied('order list');
    }

    $data['title']                = "Sell Request List";

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $this->load->view('admin/order/SellRequest', $data);
  }



  public function AnamatRequest($id = '')

  {

    if (!has_permission_new('Anamat_Booking', '', 'view')) {

      access_denied('order list');
    }

    $data['title']  = "Anamat Request List";

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $data['CenterList'] = $this->order_model->GetCenterList();

    $this->load->view('admin/order/AnamatRequest', $data);
  }



  public function TradeFinanceRequest($id = '')

  {

    if (!has_permission_new('Trade_Finance_Booking', '', 'view')) {

      access_denied('order list');
    }

    $data['title']  = "Trade Finance Request List";

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $data['CenterList'] = $this->order_model->GetCenterList();

    $this->load->view('admin/order/TradeFinanceRequest', $data);
  }



  /* List all Kirti Purchase Requst  */

  public function PurchaseRequest($id = '')

  {

    if (!has_permission_new('Purchase_Booking', '', 'view')) {

      access_denied('order list');
    }

    $data['title']                = "Purchase Request List";

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $data['CenterList'] = $this->order_model->GetCenterList();

    $this->load->view('admin/order/PurchaseRequest', $data);
  }



  public function getModalData()

  {

    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldSaleTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($checkOldTrade) {

      echo json_encode(false);
    } else {

      $ModalData = $this->order_model->getModalDataDb($data);

      echo json_encode($ModalData);
    }
  }





  public function Get_purchase_request()

  {

    $data = array(

      'from_date' => date('d/m/Y'),

      'to_date'  => date('d/m/Y'),

      'account_type'  => '',

      'center'  => '',

      'IsApprove'  => ''

    );

    $reject_by_broker_delay = $this->order_model->reject_by_delay_broker_approval();

    $reject_by_kirti_delay = $this->order_model->reject_by_delay_kirti_approval();

    $OrderList = $this->order_model->GetPurchaseRequest($data);



    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($OrderList as $value) {

      if ($value['company'] == "") {

        $PartyName = $value['firstname'] . " " . $value['lastname'];
      } else {

        $PartyName = $value['company'];
      }

      if ($value['BName'] == "") {

        $BrokerName = $value['Bfirstname'] . " " . $value['Blastname'];
      } else {

        $BrokerName = $value['BName'];
      }

      if ($value['CustomerType'] == "1") {

        $AccountType = "Farmer";
      } elseif ($value['CustomerType'] == "3") {

        $AccountType = "Trader";
      } elseif ($value['CustomerType'] == "2") {

        $AccountType = "Broker";
      } elseif ($value['CustomerType'] == "4") {

        $AccountType = "Corporate/Processor";
      } else {

        $AccountType = "";
      }

      $status = '';

      $html_code = '';

      if ($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y") {

        // Condition 1

        $status = 'Accepted ' . _d($value['ApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Accepted</td>';
      } elseif ($value['IsApprove'] == 'Y' && $value['BrokerApprove'] == "NA" && $value['ClientApprove'] == "NA") {

        // Condition 2

        // Trade Modify by kirti

        $status = 'Modify By Kirti ' . _d($value['modify_date']) . '';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif ($value['IsApprove'] == 'N' && is_null($value['LastActionName'])) {

        // Condition 3 

        // Rejected by Kirti

        $status = 'Rejected ' . _d($value['ApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif ($value['IsApprove'] == 'N' && $value['LastActionName'] != "") {

        // Condition 4 

        // Rejected due to kirti delay

        $status = $value['LastActionName'] . ' ' . _d($value['ApproveTime']);

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "NA" && $value['BrokerID'] != $value['AccountID']) {

        // Condition 5

        // waiting for Broker Approval

        $status = 'Awaiting Broker Approval';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "NA" && $value['BrokerID'] != $value['AccountID']) {

        // Condition 5

        // waiting for Broker Approval

        $status = 'Awaiting Broker Approval';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['ClientApprove'] == 'N')) {

        // Condition 6

        // modify trade reject by trader

        $status = $value['LastActionName'] . ' ' . _d($value['BrokerApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif ($value['BrokerApprove'] == "N") {

        // Condition 7

        // Trade reject by Broker

        $status = $value['LastActionName'] . ' ' . _d($value['BrokerApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "Y") {

        $status = '--';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      }

      if ($value['e_quantity'] == '' || $value['e_quantity'] == null) {

        $Qty = $value['quantity'];
      } else {

        $Qty = $value['e_quantity'];
      }

      if ($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] != "") {

        $PCSoftStatus =  $value["pcsoft_doc_ref"];
      } else if ($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] == "") {

        $PCSoftStatus = '<button type="button" onclick=ReSendTradeToPcSoft("' . $value["BookingID"] . '") id="ReSendTradeToPcSoft" class="btn btn-info">Send To NewERP</button>';
      } else {

        $PCSoftStatus = "--";
      }

      $html .= '<tr class="GetDetails" data-id="' . $value["BookingID"] . '">';

      $html .= '<td style="text-align:left;">' . $sr . '</td>';

      $html .= '<td style="text-align:left;">' . $value['PlantName'] . '</td>';

      $html .= '<td style="text-align:left;">' . _d($value['TransDate']) . '</td>';

      $html .= '<td style="text-align:left;">' . $value['CenterName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['ItemName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['basic_rate'] . '</td>';

      $html .= '<td style="text-align:left;">' . $Qty . ' ' . $value['unit'] . '</td>';



      $html .= $html_code;

      $html .= '<td style="text-align:left;">' . $BrokerName . '</td>';

      $html .= '<td>' . $PartyName . '</td>';

      $html .= '<td>' . $PCSoftStatus . '</td>';

      $html .= '<td style="text-align:left;">' . $status . '</td>';

      $html .= '</tr>';

      $sr++;
    }



    echo $html;
  }



  // Get Kirti Sale request by Ajax

  public function Get_Sale_request()

  {

    $data = array(

      'from_date' => date('d/m/Y'),

      'to_date'  => date('d/m/Y'),

      'account_type'  => '',

      'center'  => '',

      'IsApprove'  => ''

    );

    //$reject_by_broker_delay = $this->order_model->reject_by_delay_broker_approval();

    //$reject_by_kirti_delay = $this->order_model->reject_by_delay_kirti_approval();

    $OrderList = $this->order_model->GetSaleRequest($data);



    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($OrderList as $value) {

      // Party Name

      if ($value['company'] == "") {

        $PartyName = $value['firstname'] . " " . $value['lastname'];
      } else {

        $PartyName = $value['company'];
      }

      // Broker name

      if ($value['BName'] == "") {

        $BrokerName = $value['Bfirstname'] . " " . $value['Blastname'];
      } else {

        $BrokerName = $value['BName'];
      }



      $html_code = '';

      if ($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y") {

        // Condition 1

        $status = 'Accepted ' . _d($value['ApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Accepted</td>';
      } elseif ($value['IsApprove'] == 'Y' && $value['BrokerApprove'] == "NA" && $value['ClientApprove'] == "NA") {

        // Condition 2

        // Trade Modify by kirti

        $status = 'Modify By Kirti ' . _d($value['modify_date']) . '';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectSaleTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif ($value['IsApprove'] == 'N' && is_null($value['LastActionName'])) {

        // Condition 3 

        // Rejected by Kirti

        $status = 'Rejected ' . _d($value['ApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif ($value['IsApprove'] == 'N' && $value['LastActionName'] != "") {

        // Condition 4 

        // Rejected due to kirti delay

        $status = $value['LastActionName'] . ' ' . _d($value['ApproveTime']);

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "NA" && $value['BrokerID'] != $value['AccountID']) {

        // Condition 5

        // waiting for Broker Approval

        $status = 'Awaiting Broker Approval';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectSaleTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "NA" && $value['BrokerID'] != $value['AccountID']) {

        // Condition 5

        // waiting for Broker Approval

        $status = 'Awaiting Broker Approval';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectSaleTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['ClientApprove'] == 'N')) {

        // Condition 6

        // modify trade reject by trader

        $status = $value['LastActionName'] . ' ' . _d($value['BrokerApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif ($value['BrokerApprove'] == "N") {

        // Condition 7

        // Trade reject by Broker

        $status = $value['LastActionName'] . ' ' . _d($value['BrokerApproveTime']) . '';

        $html_code = '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && $value['BrokerApprove'] == "Y") {

        $status = '--';

        $html_code = '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptSaleTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectSaleTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      }

      if ($value['e_quantity'] == '' || $value['e_quantity'] == null) {

        $Qty = $value['quantity'];
      } else {

        $Qty = $value['e_quantity'];
      }



      $html .= '<tr class="GetDetails" data-id="' . $value["BookingID"] . '">';

      $html .= '<td style="text-align:left;">' . $sr . '</td>';

      $html .= '<td style="text-align:left;">' . $value['PlantName'] . '</td>';

      $html .= '<td style="text-align:left;">' . _d($value['TransDate']) . '</td>';

      $html .= '<td style="text-align:left;">' . $value['CenterName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['ItemName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['basic_rate'] . '</td>';

      $html .= '<td style="text-align:left;">' . $Qty . ' ' . $value['unit'] . '</td>';



      $html .= $html_code;

      $html .= '<td style="text-align:left;">' . $BrokerName . '</td>';

      $html .= '<td>' . $PartyName . '</td>';

      $html .= '<td style="text-align:left;">' . $status . '</td>';

      $html .= '</tr>';

      $sr++;
    }



    echo $html;
  }



  public function Get_purchase_request_by_show_button()

  {

    $data = array(

      'from_date' => $this->input->post('from_date'),

      'to_date'  => $this->input->post('to_date'),

      'account_type'  => $this->input->post('account_type'),

      'center'  => $this->input->post('center'),

      'IsApprove'  => $this->input->post('IsApprove')

    );

    $OrderList = $this->order_model->GetPurchaseRequest_by_show_button($data);



    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($OrderList as $value) {



      if ($value['company'] == "") {

        $PartyName = $value['firstname'] . " " . $value['lastname'];
      } else {

        $PartyName = $value['company'];
      }

      if ($value['CustomerType'] == "1") {

        $AccountType = "Farmer";
      } elseif ($value['CustomerType'] == "3") {

        $AccountType = "Trader";
      } elseif ($value['CustomerType'] == "2") {

        $AccountType = "Broker";
      } elseif ($value['CustomerType'] == "4") {

        $AccountType = "Corporate/Processor";
      } else {

        $AccountType = "";
      }

      $status = '';

      if ($value['IsApprove'] == 'Y') {

        $status = 'Accepted';
      } elseif ($value['IsApprove'] == 'N') {

        $status = 'Rejected';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {

        $status = 'Awaiting Client Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {

        $status = 'Awaiting Broker Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {

        $status = '--';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {

        $status = '--';
      }

      if ($value['e_quantity'] == '' || $value['e_quantity'] == null) {

        $Qty = $value['quantity'];
      } else {

        $Qty = $value['e_quantity'];
      }



      $html .= '<tr class="GetDetails" data-id="' . $value["BookingID"] . '">';

      $html .= '<td style="text-align:left;">' . $sr . '</td>';

      $html .= '<td style="text-align:left;">' . $value['CenterName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['ItemName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['basic_rate'] . '</td>';

      $html .= '<td style="text-align:left;">' . $Qty . ' ' . $value['unit'] . '</td>';



      if ($value['IsApprove'] == 'Y') {

        $html .= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
      } elseif ($value['IsApprove'] == 'N') {

        $html .= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      }

      $html .= '<td style="text-align:left;">' . $value['BrokerID'] . '</td>';

      $html .= '<td>' . $PartyName . '</td>';

      $html .= '<td style="text-align:left;">' . $status . '</td>';

      $html .= '</tr>';

      $sr++;
    }



    echo json_encode($html);
  }



  public function AcceptSaleTrade()

  {

    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $update_data = array(

      "ApproveTime" => $date,

      "ApproveUserID" => $UserID,

      "IsApprove" => "Y"

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldSaleTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($checkOldTrade) {

      echo json_encode($checkOldTrade);
    } else {

      //echo json_encode(true);

      $result = $this->order_model->AcceptTradeDb($update_data, $BookingID);

      if ($result == true) {



        // Notification for Trader/ Farmer



        $title = "Trade Accepted by Kisan Kirti";

        $screen = "1";

        $body = "Your BookingID : " . $BookingID . ' accepted by Kisan Kirti';

        $booking_id = $BookingID;

        $to = $GetBookingDetails->fcm_token;

        $this->send_notification($title, $screen, $body, $booking_id, $to);





        // Notification For Broker 

        if ($GetBookingDetails->BrokerID != NULL && $GetBookingDetails->BrokerID != "" && $GetBookingDetails->BrokerID != $GetBookingDetails->AccountID) {

          //$BrokerID = $GetBookingDetails->BrokerID;

          if ($GetBookingDetails->company == "" || $GetBookingDetails->company == null) {

            $AccountName = $GetBookingDetails->firstname . ' ' . $GetBookingDetails->lastname;
          } else {

            $AccountName = $GetBookingDetails->company;
          }



          $title = "Trade Accepted by Kisan Kirti";

          $screen = "1";

          $body = "BookingID accepted by Kisan kirti against " . $BookingID . " for " . $AccountName;

          $booking_id = $BookingID;

          $to = $GetBookingDetails->Bfcm_token;

          $this->send_notification($title, $screen, $body, $booking_id, $to);
        }
      }

      echo json_encode($result);
    }
  }



  public function RejectSaleTrade()

  {

    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldSaleTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($checkOldTrade) {

      echo json_encode($checkOldTrade);
    } else {

      $result = $this->order_model->RejectTradeDb($data, $GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

      if ($result == true) {
      }

      echo json_encode($result);
    }
  }



  public function AcceptTrade()

  {

    $date = date('Y-m-d H:i:s');

    $UserID = $this->session->userdata('username');



    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($GetBookingDetails->CenterType == "W") {

      $CenterType = "1";
    } else {

      $CenterType = "0";
    }

    $update_data = array(

      "ApproveTime" => $date,

      "ApproveUserID" => $UserID,

      "IsApprove" => "Y"

    );

    if ($GetBookingDetails->TType == "D") {

      $update_data['payment_cycle'] = 'P007';
    }

    if ($checkOldTrade) {

      echo json_encode($checkOldTrade);
    } else {

      $result = $this->order_model->AcceptTradeDb($update_data, $BookingID);

      if ($result == true) {



        if ($GetBookingDetails->TType == "P") {

          // Send to PC Soft 

          $trinvs_array = array([

            "doc_type" => "37",

            "party_st" => "C",

            "party_no" => $GetBookingDetails->ShortCode,

            "doc_ref" => $GetBookingDetails->BookingID,

            "im_loc" => $GetBookingDetails->CenterID,
            "CustomerType" => $GetBookingDetails->CustomerType

            // "im_loc"=>$GetBookingDetails->PCCenterID

          ]);

          $sporddtl_array = array([

            // "IM_CODE"=>$GetBookingDetails->PCItemID,

            "IM_CODE" => $GetBookingDetails->ItemID,

            "im_qty" => $GetBookingDetails->e_quantity + 2,

            "case_qty" => $GetBookingDetails->e_quantity,

            "im_ordrate" => $GetBookingDetails->basic_rate

          ]);



          $data_po_array =  array(

            "cocd" => $GetBookingDetails->PartyID,

            "Type" => $CenterType,

            "trinvs" => $trinvs_array,

            "sporddtl" => $sporddtl_array

          );

          $po_data = json_encode($data_po_array);



          $curl = curl_init();

          curl_setopt_array(
            $curl,
            array(

              // CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/posoSubmit", //  -> LIVE URL

              // CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/posoSubmit", // -> DEV URL

              CURLOPT_URL => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/Order", // -> New Kriti erp

              CURLOPT_RETURNTRANSFER => true,

              CURLOPT_MAXREDIRS => 10,

              CURLOPT_TIMEOUT => 30,

              CURLOPT_CUSTOMREQUEST => "POST",

              CURLOPT_POSTFIELDS => $po_data,

              CURLOPT_HTTPHEADER => array(

                "content-type: application/json"

              ),

            )

          );

          $response = curl_exec($curl);

          $response_array = json_decode($response);

          if ($response_array->status) {

            $id = $response_array->data->id;

            // echo json_encode($id);

            $insert_referance = array(

              "Type" => $GetBookingDetails->TType,

              "Name" => "Trade",

              "GIC_Reference" => $GetBookingDetails->BookingID,

              "pcsoft_doc_ref" => $id

            );

            $this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
          }

          // $PcSoft_po = $response_array->doc_ref_number;

          // $status = $response_array->Status;

          // if($status == true){

          //     $insert_referance = array(

          //         "Type"=>$GetBookingDetails->TType,

          //         "Name"=>"Trade",

          //         "GIC_Reference"=>$GetBookingDetails->BookingID,

          //         "pcsoft_doc_ref"=>$PcSoft_po

          //     );

          //     $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);

          // }

          $err = curl_error($curl);

          curl_close($curl);
        }



        // Notification for Trader/ Farmer



        $title = "Trade Accepted by Kisan Kirti";

        $screen = "1";

        $body = "Your BookingID : " . $BookingID . ' accepted by Kisan Kirti';

        $booking_id = $BookingID;

        $to = $GetBookingDetails->fcm_token;

        $this->send_notification($title, $screen, $body, $booking_id, $to);





        // Notification For Broker 

        if ($GetBookingDetails->BrokerID != NULL && $GetBookingDetails->BrokerID != "" && $GetBookingDetails->BrokerID != $GetBookingDetails->AccountID) {

          //$BrokerID = $GetBookingDetails->BrokerID;

          if ($GetBookingDetails->company == "" || $GetBookingDetails->company == null) {

            $AccountName = $GetBookingDetails->firstname . ' ' . $GetBookingDetails->lastname;
          } else {

            $AccountName = $GetBookingDetails->company;
          }



          $title = "Trade Accepted by Kisan Kirti";

          $screen = "1";

          $body = "BookingID accepted by Kisan kirti against " . $BookingID . " for " . $AccountName;

          $booking_id = $BookingID;

          $to = $GetBookingDetails->Bfcm_token;

          $this->send_notification($title, $screen, $body, $booking_id, $to);
        }
      }

      echo json_encode($result);
    }
  }



  public function ReSendTradeToPcSoft()

  {

    $date = date('Y-m-d H:i:s');

    $UserID = $this->session->userdata('username');



    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($GetBookingDetails->CenterType == "W") {

      $CenterType = "1";
    } else {

      $CenterType = "0";
    }

    /*if($checkOldTrade){

            echo json_encode($checkOldTrade);

        }else{*/

    if ($GetBookingDetails->TType == "P") {

      // Send to PC Soft 

      $trinvs_array = array([

        "doc_type" => "37",

        "party_st" => "C",

        "party_no" => $GetBookingDetails->ShortCode,

        "doc_ref" => $GetBookingDetails->BookingID,

        "im_loc" => $GetBookingDetails->CenterID,
        "CustomerType" => $GetBookingDetails->CustomerType

        // "im_loc"=>$GetBookingDetails->PCCenterID

      ]);

      $sporddtl_array = array([

        // "IM_CODE"=>$GetBookingDetails->PCItemID,

        "IM_CODE" => $GetBookingDetails->ItemID,

        "im_qty" => $GetBookingDetails->e_quantity + 2,

        "case_qty" => $GetBookingDetails->e_quantity,

        "im_ordrate" => $GetBookingDetails->basic_rate

      ]);



      $data_po_array =  array(

        "cocd" => $GetBookingDetails->PartyID,

        "Type" => $CenterType,

        "trinvs" => $trinvs_array,

        "sporddtl" => $sporddtl_array

      );

      $po_data = json_encode($data_po_array);



      $curl = curl_init();

      curl_setopt_array(
        $curl,
        array(

          // CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/posoSubmit", //  -> LIVE URL

          // CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/posoSubmit", // -> DEV URL

          CURLOPT_URL => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/Order", // -> New Kriti erp

          CURLOPT_RETURNTRANSFER => true,

          CURLOPT_MAXREDIRS => 10,

          CURLOPT_TIMEOUT => 30,

          CURLOPT_CUSTOMREQUEST => "POST",

          CURLOPT_POSTFIELDS => $po_data,

          CURLOPT_HTTPHEADER => array(

            "content-type: application/json"

          ),

        )

      );

      $response = curl_exec($curl);

      $err = curl_error($curl);

      curl_close($curl);

      $response_array = json_decode($response);

      if ($response_array->status) {

        $id = $response_array->data->id;

        // echo json_encode($id);

        $insert_referance = array(

          "Type" => $GetBookingDetails->TType,

          "Name" => "Trade",

          "GIC_Reference" => $GetBookingDetails->BookingID,

          "pcsoft_doc_ref" => $id

        );

        $this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);

        echo json_encode('Data Send to New ERP Successsfully');
      } else {

        $errorMsg = $response_array->data->error ?? $response_array->message ?? 'Something went wrong';

        echo json_encode($errorMsg);
      }

      // $PcSoft_po = $response_array->doc_ref_number;

      // $status = $response_array->Status;



      // if($status == true){

      //     $insert_referance = array(

      //         "Type"=>$GetBookingDetails->TType,

      //         "Name"=>"Trade",

      //         "GIC_Reference"=>$GetBookingDetails->BookingID,

      //         "pcsoft_doc_ref"=>$PcSoft_po

      //     );

      //     $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);

      //     echo json_encode('Data Send to PcSoft Successsfully');

      // }else{

      //     echo json_encode($response_array->ErrorMessage);

      // }

      /*echo "<pre>";

                //print_r($po_data);

			print_r($response);

			echo "<br>";

			echo "<br>";

			//print_r($insert_referance);

			//print_r($err); 

		die;*/
    }



    //}



  }



  function send_notification($title, $screen, $body, $booking_id, $to)

  {

    $data_arrary = array(

      "title" => $title,

      "screen" => $screen,

      "body" => $body,

      "booking_id" => $booking_id

    );

    $post_data = array(

      "priority" => "HIGH",

      "data" => $data_arrary,

      "to" => $to

    );

    $finel_data = json_encode($post_data);

    $curl = curl_init();

    curl_setopt_array(
      $curl,
      array(

        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_ENCODING => "",

        CURLOPT_MAXREDIRS => 10,

        CURLOPT_TIMEOUT => 30,

        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

        CURLOPT_CUSTOMREQUEST => "POST",

        CURLOPT_POSTFIELDS => $finel_data,

        CURLOPT_HTTPHEADER => array(

          "authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",

          "content-type: application/json"

        ),

      )

    );

    $response = curl_exec($curl);

    $err = curl_error($curl);

    curl_close($curl);
  }



  public function RejectTrade()

  {

    $data = array(

      'BookingID' => $this->input->post('BookingID'),

    );

    $BookingID = $this->input->post('BookingID');

    $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

    $checkOldTrade = $this->order_model->GetOldTrade($GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

    if ($checkOldTrade) {

      echo json_encode($checkOldTrade);
    } else {

      $result = $this->order_model->RejectTradeDb($data, $GetBookingDetails->TransDate, $GetBookingDetails->ItemID, $GetBookingDetails->CenterID, $GetBookingDetails->TType);

      if ($result == true) {
      }

      echo json_encode($result);
    }
  }



  public function ModifyTrades()

  {

    $data = array(

      'e_quantity' => $this->input->post('modal_quantity'),

      'unit' => $this->input->post('modal_unit'),

      'payment_cycle' => $this->input->post('payment_cycle'),

      'locking_period' => $this->input->post('locking_period'),

      'basic_rate' => $this->input->post('chargerate'),

      'modify_date' => date('Y-m-d H:i:s'),

      'Lupdate' => date('Y-m-d H:i:s'),

    );



    $deposittradedata = array(

      'MinQty' => $this->input->post('minqty'),

      'DepositPeriod' => $this->input->post('depositperiod'),

      'RateType' => $this->input->post('Ratetype'),

      'IsFumigation' => $this->input->post('isfumigation'),

      'RateIncFumigation' => $this->input->post('rateincfumigation'),

      'FumigationAmt' => $this->input->post('fumigationamt'),

      'CreditDays' => $this->input->post('CreditDays')

    );



    $BookingID = $this->input->post('modal_BookingID');

    $result = $this->order_model->ModifyTradeDb($data, $BookingID);

    $tradeResult = $this->order_model->ModifyDepositTradeDb($deposittradedata, $BookingID);

    if ($result == true && $tradeResult == true) {

      $GetBookingDetails = $this->order_model->GetBookingDetails($BookingID);

      $AccountID = $GetBookingDetails->AccountID;

      // Notification for Trader/ Farmer

      $title = "Trade modify by Kisan Kirti";

      $screen = "1";

      $body = "Your BookingID : " . $BookingID . ' modify by Kisan Kirti';

      $booking_id = $BookingID;

      $to = $GetBookingDetails->fcm_token;

      $this->send_notification($title, $screen, $body, $booking_id, $to);





      // Notification For Broker 

      if ($GetBookingDetails->BrokerID != NULL && $GetBookingDetails->BrokerID != "" && $GetBookingDetails->BrokerID != $GetBookingDetails->AccountID) {

        //$BrokerID = $GetBookingDetails->BrokerID;

        if ($GetBookingDetails->company == "" || $GetBookingDetails->company == null) {

          $AccountName = $GetBookingDetails->firstname . ' ' . $GetBookingDetails->lastname;
        } else {

          $AccountName = $GetBookingDetails->company;
        }



        $title = "Trade modify by Kisan Kirti";

        $screen = "1";

        $body = "BookingID modify by Kisan kirti against " . $BookingID . " for " . $AccountName;

        $booking_id = $BookingID;

        $to = $GetBookingDetails->Bfcm_token;

        $this->send_notification($title, $screen, $body, $booking_id, $to);
      }
    }

    echo json_encode($result);
  }



  //====================== New Code End ==========================================





  /*public function itemlist(){

        $this->load->model('invoice_items_model');

    // POST data

    $postData = $this->input->post();



    // Get data

    $data = $this->invoice_items_model->getitem($postData);



    echo json_encode($data);

  }*/



  /* Get item by id / ajax */

  public function get_remark_by_orderid($id)

  {

    if ($this->input->is_ajax_request()) {

      $order                   = $this->order_model->get2($id);





      echo json_encode($order);
    }
  }



  /* Edit or update items / ajax request /*/

  public function remark_update()

  {

    if (has_permission_new('orders', '', 'edit')) {

      if ($this->input->post()) {

        $data = $this->input->post();

        if ($data['itemid'] == '') {

          if (!has_permission_new('orders', '', 'create')) {

            header('HTTP/1.0 400 Bad error');

            echo _l('access_denied');

            die;
          }

          $id      = $this->invoice_items_model->add($data);

          $success = false;

          $message = '';

          if ($id) {

            $success = true;

            $message = _l('added_successfully', _l('sales_item'));
          }

          echo json_encode([

            'success' => $success,

            'message' => $message,

            'item'    => $this->invoice_items_model->get($id),

          ]);
        } else {

          if (!has_permission_new('orders', '', 'edit')) {

            header('HTTP/1.0 400 Bad error');

            echo _l('access_denied');

            die;
          }

          $success = $this->order_model->remark_update($data);

          $message = '';

          if ($success) {

            $message = _l('updated_successfully', _l('sales_item'));
          }

          echo json_encode([

            'success' => $success,

            'message' => $message,

          ]);
        }
      }
    }
  }



  public function itemlist_using_itemcode()
  {

    $this->load->model('invoice_items_model');

    // POST data

    $postData = $this->input->post();



    // Get data

    $data = $this->invoice_items_model->getitem_using_itemcode($postData);



    echo json_encode($data);
  }



  public function GetItemDetailByID()
  {

    $this->load->model('invoice_items_model');

    // POST data

    $postData = $this->input->post();

    // Get data

    $data = $this->invoice_items_model->getItemDetailsByID($postData);

    echo json_encode($data);
  }



  /* List all invoices datatables */

  public function SaleList($id = '')

  {

    if (!has_permission_new('sale_list', '', 'view')) {

      access_denied('order list');
    }

    close_setup_menu();

    $this->load->model('payment_modes_model');

    $data['payment_modes']        = $this->payment_modes_model->get('', [], true);

    $data['invoiceid']            = $id;

    $data['title']                = "Sale List";

    $data['invoices_years']       = $this->invoices_model->get_invoices_years();

    $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

    $data['invoices_statuses']    = $this->invoices_model->get_statuses();

    $data['bodyclass']            = 'invoices-total-manual';

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $this->load->view('admin/order/manage', $data);
  }



  /* List all invoices datatables */

  public function pending_orders2($id = '')

  {

    if (!has_permission_new('sale_list', '', 'view')) {

      access_denied('orders');
    }



    close_setup_menu();



    $data['title']                = "Order List";

    $order = $this->order_model->get2($id);



    $data['order'] = $order;

    $this->load->view('admin/order/order_details', $data);
  }



  /* List all invoices datatables */

  public function order_details($id = '')

  {

    if (!has_permission_new('sale_list', '', 'view')) {

      access_denied('orders');
    }



    close_setup_menu();



    $data['title']                = "Order Details";

    $order = $this->order_model->get2($id);

    $data['selected_company_details']    = $this->order_model->get_selected_company_details();

    $data['order'] = $order;

    $this->load->view('admin/order/order_details', $data);
  }



  /* List all pending order datatables */

  public function pending_orders($id = '')

  {

    if (!has_permission_new('pending_orders', '', 'view')) {

      access_denied('orders');
    }



    close_setup_menu();



    $this->load->model('payment_modes_model');

    $data['payment_modes']        = $this->payment_modes_model->get('', [], true);

    $data['invoiceid']            = $id;

    $data['title']                = "Pending Order";

    $data['invoices_years']       = $this->invoices_model->get_invoices_years();

    $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

    $data['invoices_statuses']    = $this->invoices_model->get_statuses();

    $data['states']    = $this->order_model->get_state_list();

    $data['dist_type']    = $this->order_model->get_distributor_type();

    $data['selected_company_details']    = $this->order_model->get_selected_company_details();

    $data['bodyclass']            = 'invoices-total-manual';

    $this->load->view('admin/order/pendig_order_list2', $data);
  }





  public function load_data()

  {

    $data = array(

      'dates' => $this->input->post('dates'),

      'order_type'  => $this->input->post('order_type'),

      'state'  => $this->input->post('state'),

      'dist_type'  => $this->input->post('dist_type')

    );

    $data = $this->order_model->load_data($data);

    echo json_encode($data);
  }



  public function load_data_items()

  {

    $data = array(

      'dates' => $this->input->post('dates'),

      'order_type'  => $this->input->post('order_type'),

      'state'  => $this->input->post('state'),

      'dist_type'  => $this->input->post('dist_type')

    );

    $data = $this->order_model->load_data_items($data);

    echo json_encode($data);
  }



  public function load_data2()

  {

    $data = array(

      'dates' => $this->input->post('dates'),

      'order_type'  => $this->input->post('order_type'),

      'state'  => $this->input->post('state'),

      'dist_type'  => $this->input->post('dist_type'),

      'selected_ids'  => $this->input->post('selected_ids')

    );

    $data = $this->order_model->load_data2($data);

    echo json_encode($data);
  }



  public function load_data_items2()

  {

    $data = array(

      'dates' => $this->input->post('dates'),

      'order_type'  => $this->input->post('order_type'),

      'state'  => $this->input->post('state'),

      'dist_type'  => $this->input->post('dist_type'),

      'selected_ids'  => $this->input->post('selected_ids')

    );

    $data = $this->order_model->load_data_items2($data);

    echo json_encode($data);
  }



  public function update_order_status()

  {

    /*$data = array(

           $this->input->post('table_column') => $this->input->post('value'),

           $this->input->post('table_column') => $this->input->post('value')

          );*/



    $data = $this->order_model->update_order_status($this->input->post('selected_ids'), $this->input->post('selected_ids_remarks'), $this->input->post('unselected_ids'), $this->input->post('unselected_ids_remarks'));

    echo json_encode($data);
  }



  public function reset_order_status()

  {

    /*$data = array(

           $this->input->post('table_column') => $this->input->post('value'),

           $this->input->post('table_column') => $this->input->post('value')

          );*/



    // echo $selected_id=$this->input->post('selected_ids');

    //   $selected_id = $this->input->post('selected_ids_remarks');

    //   $unselected_ids = $this->input->post('unselected_ids');

    //   $unselected_ids_remarks = $this->input->post('unselected_ids_remarks');

    //  exit();



    $data = $this->order_model->reset_order_status($this->input->post('selected_ids'), $this->input->post('selected_ids_remarks'), $this->input->post('unselected_ids'), $this->input->post('unselected_ids_remarks'));

    echo json_encode($data);
  }







  public function edit_order_table()

  {

    if (!has_permission_new('orders', '', 'view')) {

      ajax_access_denied();
    }

    if ($this->input->is_ajax_request()) {

      if ($this->input->post()) {

        $this->app->get_table_data('edit_order');
      }
    }
  }







  public function table($clientid = '')

  {



    $this->load->model('payment_modes_model');

    $data['payment_modes'] = $this->payment_modes_model->get('', [], true);



    $this->app->get_table_data(($this->input->get('recurring') ? 'recurring_invoices' : 'order2'), [

      'clientid' => $clientid,

      'data'     => $data,

    ]);
  }







  public function client_change_data($customer_id, $current_invoice = '')

  {

    if ($this->input->is_ajax_request()) {

      $this->load->model('projects_model');

      $this->load->model('invoice_items_model');

      $data                     = [];

      $fy = $this->session->userdata('finacial_year');

      $selected_company = $this->session->userdata('root_company');



      $data['client_details']  = $this->clients_model->get($customer_id);

      $data['client_actbal']  = $this->order_model->get_accbal($customer_id, $selected_company, $fy);

      $data['client_last_bill']  = $this->order_model->get_last_bill_on($customer_id, $selected_company, $fy);

      $data['client_last_deposit']  = $this->order_model->get_last_deposit_on($customer_id, $selected_company, $fy);

      $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);

      $data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);

      $data['client_route']  = $this->clients_model->getroutebyclient($customer_id);

      $data['client_details']->routes = $data['client_route']->routes;

      $data['location_details'] = $this->clients_model->get_location_type($customer_id);

      $data['client_details']->location_type = $data['location_details']->LocationTypeID;

      $client_item_div  = $this->clients_model->getclientitem_division($customer_id);

      $pending_order  = $this->order_model->check_pending_order($customer_id);

      $data['client_details']->pending_order = $pending_order;

      $item_div_ids = array();

      if (empty($client_item_div)) {
      } else {

        foreach ($client_item_div as $key => $value) {

          # code...

          array_push($item_div_ids, $value["ItemDivID"]);
        }
      }



      $data['client_details']->itemdivision = $item_div_ids;



      $data['customer_groups_name'] = $this->clients_model->get_customer_groups_name($data['client_details']->DistributorType);





      echo json_encode($data);
    }
  }



  public function update_number_settings($id)

  {

    $response = [

      'success' => false,

      'message' => '',

    ];

    if (has_permission_new('orders', '', 'edit')) {

      $affected_rows = 0;



      $this->db->where('id', $id);

      $this->db->update(db_prefix() . 'invoices', [

        'prefix' => $this->input->post('prefix'),

      ]);

      if ($this->db->affected_rows() > 0) {

        $affected_rows++;
      }



      if ($affected_rows > 0) {

        $response['success'] = true;

        $response['message'] = _l('updated_successfully', _l('invoice'));
      }
    }

    echo json_encode($response);

    die;
  }



  public function validate_invoice_number()

  {

    $isedit          = $this->input->post('isedit');

    $number          = $this->input->post('number');

    $date            = $this->input->post('date');

    $original_number = $this->input->post('original_number');

    $number          = trim($number);

    $number          = ltrim($number, '0');



    if ($isedit == 'true') {

      if ($number == $original_number) {

        echo json_encode(true);

        die;
      }
    }



    if (total_rows(db_prefix() . 'invoices', [

      'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),

      'number' => $number,

      'status !=' => Invoices_model::STATUS_DRAFT,

    ]) > 0) {

      echo 'false';
    } else {

      echo 'true';
    }
  }



  public function add_note($rel_id)

  {

    if ($this->input->post() && user_can_view_invoice($rel_id)) {

      $this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);

      echo $rel_id;
    }
  }



  public function get_notes($id)

  {

    if (user_can_view_invoice($id)) {

      $data['notes'] = $this->misc_model->get_notes($id, 'invoice');

      $this->load->view('admin/includes/sales_notes_template', $data);
    }
  }



  public function pause_overdue_reminders($id)

  {

    if (has_permission_new('orders', '', 'edit')) {

      $this->db->where('id', $id);

      $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);
    }

    redirect(admin_url('order/list_orders/' . $id));
  }



  public function resume_overdue_reminders($id)

  {

    if (has_permission_new('orders', '', 'edit')) {

      $this->db->where('id', $id);

      $this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);
    }

    redirect(admin_url('order/list_orders/' . $id));
  }



  public function mark_as_cancelled($id)

  {

    if (!has_permission_new('orders', '', 'edit') && !has_permission_new('invoices', '', 'create')) {

      access_denied('invoices');
    }



    $success = $this->invoices_model->mark_as_cancelled($id);



    if ($success) {

      set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
    }



    redirect(admin_url('order/list_orders/' . $id));
  }



  public function unmark_as_cancelled($id)

  {

    if (!has_permission_new('orders', '', 'edit') && !has_permission_new('invoices', '', 'create')) {

      access_denied('order');
    }

    $success = $this->invoices_model->unmark_as_cancelled($id);

    if ($success) {

      set_alert('success', _l('invoice_unmarked_as_cancelled'));
    }

    redirect(admin_url('order/list_orders/' . $id));
  }











  public function get_bill_expense_data($id)

  {

    $this->load->model('expenses_model');

    $expense = $this->expenses_model->get($id);



    $expense->qty              = 1;

    $expense->long_description = clear_textarea_breaks($expense->description);

    $expense->description      = $expense->name;

    $expense->rate             = $expense->amount;

    if ($expense->tax != 0) {

      $expense->taxname = [];

      array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
    }

    if ($expense->tax2 != 0) {

      array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
    }

    echo json_encode($expense);
  }







  /* Get all invoice data used when user click on invoiec number in a datatable left side*/

  public function get_order_data_ajax($id)

  {



    if (!$id) {

      die(_l('invoice_not_found'));
    }



    $order = $this->order_model->get2($id);





    if (!$order) {

      echo "Order Not Found";

      die;
    }







    $data['order'] = $order;



    $this->load->view('admin/order/invoice_preview_template', $data);
  }



  public function apply_credits($invoice_id)

  {

    $total_credits_applied = 0;

    foreach ($this->input->post('amount') as $credit_id => $amount) {

      $success = $this->credit_notes_model->apply_credits($credit_id, [

        'invoice_id' => $invoice_id,

        'amount'     => $amount,

      ]);

      if ($success) {

        $total_credits_applied++;
      }
    }



    if ($total_credits_applied > 0) {

      update_invoice_status($invoice_id, true);

      set_alert('success', _l('invoice_credits_applied'));
    }

    redirect(admin_url('order/list_orders/' . $invoice_id));
  }



  public function get_invoices_total()

  {

    if ($this->input->post()) {

      load_invoices_total_template();
    }
  }



  /* Record new inoice payment view */

  public function record_invoice_payment_ajax($id)

  {

    $this->load->model('payment_modes_model');

    $this->load->model('payments_model');

    $data['payment_modes'] = $this->payment_modes_model->get('', [

      'expenses_only !=' => 1,

    ]);

    $data['invoice']  = $this->invoices_model->get($id);

    $data['payments'] = $this->payments_model->get_invoice_payments($id);

    $this->load->view('admin/invoices/record_payment_template', $data);
  }



  /* Record new inoice  */

  public function crate_invoice_by_ajax($id)

  {

    $this->load->model('payment_modes_model');

    $this->load->model('payments_model');

    $data['payment_modes'] = $this->payment_modes_model->get('', [

      'expenses_only !=' => 1,

    ]);

    $order  = $this->order_model->get($id);

    //echo "<pre>";

    $addedfrom = !DEFINED('CRON') ? get_staff_user_id() : 0;

    $invoicedata = array(

      "sent" => $order->sent,

      "datesend" => $order->datesend,

      "clientid" => $order->clientid,

      "deleted_customer_name" => $order->deleted_customer_name,

      "order_id" => $order->number,

      "order_type" => $order->order_type,

      "dist_comp" => $order->dist_comp,

      "dist_sale_agent" => $order->dist_sale_agent,

      "prefix" => 'INV-',

      "number_format" => $order->number_format,

      "datecreated" => date('Y-m-d H:i:s'),

      "date" => date('Y-m-d'),

      "currency" => $order->currency,

      "subtotal" => $order->subtotal,

      "total_tax" => $order->total_tax,

      "total" => $order->total,

      "total_cases" => $order->total_cases,

      "adjustment" => $order->adjustment,

      "addedfrom" => $addedfrom,

      "hash" => $order->hash,

      "status" => $order->status,

      "allowed_payment_modes" => $order->allowed_payment_modes,

      "token" => $order->token,

      "discount_percent" => $order->discount_percent,

      "discount_total" => $order->discount_total,

      "discount_type" => $order->discount_type,

      "sale_agent" => $order->sale_agent,

      "billing_street" => $order->billing_street,

      "billing_city" => $order->billing_city,

      "billing_state" => $order->billing_state,

      "billing_zip" => $order->billing_zip,

      "billing_country" => $order->billing_country,

      "shipping_street" => $order->shipping_street,

      "shipping_state" => $order->shipping_state,

      "shipping_city" => $order->shipping_city,

      "shipping_zip" => $order->shipping_zip,

      "shipping_country" => $order->shipping_country,

      "include_shipping" => $order->include_shipping,

      "show_shipping_on_invoice" => $order->show_shipping_on_invoice,

      "show_quantity_as" => $order->show_quantity_as,

      "subscription_id" => $order->subscription_id,

      "short_link" => $order->short_link,

      "project_id" => $order->project_id

    );



    $items = $order->items;

    /*foreach ($items as $key => $value) {

               # code...

               echo $value["description"];

               echo "<br>";

               

            }*/

    //print_r($items);

    //echo $items[0]['rel_type'];

    //die;

    $this->db->insert(db_prefix() . 'invoices', $invoicedata);

    $invoice_id = $this->db->insert_id();

    if ($invoice_id) {



      $this->db->where('id', $invoice_id);

      $this->db->update(db_prefix() . 'invoices', [

        'number' => $invoice_id,

      ]);



      foreach ($items as $key => $item) {

        # code...



        $itemdata = array(

          "rel_id" => $invoice_id,

          "rel_type" => $item['rel_type'],

          "description" => $item['description'],

          "long_description" => $item['long_description'],

          "hsn_code" => $item['hsn_code'],

          "qty" => $item['qty'],

          "pack_qty" => $item['pack_qty'],

          "rate" => $item['rate'],

          "total_amt" => $item['total_amt'],

          "discount_amt" => $item['discount_amt'],

          "taxable_amt" => $item['taxable_amt'],

          "gst" => $item['gst'],

          "gst_amt" => $item['gst_amt'],

          "unit" => $item['unit'],

          "grand_total" => $item['grand_total'],

          "item_order" => $item['item_order']

        );

        $this->db->insert(db_prefix() . 'itemable', $itemdata);
      }
    }



    redirect(admin_url('order/get_order_data_ajax/' . $id));
  }



  /* This is where invoice payment record $_POST data is send */

  public function record_payment()

  {

    if (!has_permission_new('orders', '', 'create')) {

      access_denied('Record Payment');
    }

    if ($this->input->post()) {

      $this->load->model('payments_model');

      $id = $this->payments_model->process_payment($this->input->post(), '');

      if ($id) {

        set_alert('success', _l('invoice_payment_recorded'));

        redirect(admin_url('payments/payment/' . $id));
      } else {

        set_alert('danger', _l('invoice_payment_record_failed'));
      }

      redirect(admin_url('order/list_orders/' . $this->input->post('invoiceid')));
    }
  }



  /* Send invoice to email */

  public function send_to_email($id)

  {

    $canView = user_can_view_invoice($id);

    if (!$canView) {

      access_denied('Invoices');
    } else {

      if (!has_permission_new('orders', '', 'view') && !has_permission_new('orders', '', 'view_own') && $canView == false) {

        access_denied('Order');
      }
    }



    try {

      $statementData = [];

      if ($this->input->post('attach_statement')) {

        $statementData['attach'] = true;

        $statementData['from']   = to_sql_date($this->input->post('statement_from'));

        $statementData['to']     = to_sql_date($this->input->post('statement_to'));
      }



      $success = $this->invoices_model->send_invoice_to_client(

        $id,

        '',

        $this->input->post('attach_pdf'),

        $this->input->post('cc'),

        false,

        $statementData

      );
    } catch (Exception $e) {

      $message = $e->getMessage();

      echo $message;

      if (strpos($message, 'Unable to get the size of the image') !== false) {

        show_pdf_unable_to_get_image_size_error();
      }

      die;
    }



    // In case client use another language

    load_admin_language();

    if ($success) {

      set_alert('success', _l('invoice_sent_to_client_success'));
    } else {

      set_alert('danger', _l('invoice_sent_to_client_fail'));
    }

    redirect(admin_url('order/list_orders/' . $id));
  }



  /* Delete invoice payment*/

  public function delete_payment($id, $invoiceid)

  {

    if (!has_permission_new('payments', '', 'delete')) {

      access_denied('payments');
    }

    $this->load->model('payments_model');

    if (!$id) {

      redirect(admin_url('payments'));
    }

    $response = $this->payments_model->delete($id);

    if ($response == true) {

      set_alert('success', _l('deleted', _l('payment')));
    } else {

      set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
    }

    redirect(admin_url('order/list_orders/' . $invoiceid));
  }



  /* Delete invoice */

  public function delete($id)

  {

    if (!has_permission_new('orders', '', 'delete')) {

      access_denied('order');
    }

    if (!$id) {

      redirect(admin_url('order/list_orders'));
    }

    $success = $this->invoices_model->delete($id);



    if ($success) {

      set_alert('success', _l('deleted', _l('invoice')));
    } else {

      set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
    }

    if (strpos($_SERVER['HTTP_REFERER'], 'list_orders') !== false) {

      redirect(admin_url('order/list_orders'));
    } else {

      redirect($_SERVER['HTTP_REFERER']);
    }
  }



  public function delete_attachment($id)

  {

    $file = $this->misc_model->get_file($id);

    if ($file->staffid == get_staff_user_id() || is_admin()) {

      echo $this->invoices_model->delete_attachment($id);
    } else {

      header('HTTP/1.0 400 Bad error');

      echo _l('access_denied');

      die;
    }
  }



  /* Will send overdue notice to client */

  public function send_overdue_notice($id)

  {

    $canView = user_can_view_invoice($id);

    if (!$canView) {

      access_denied('Order');
    } else {

      if (!has_permission_new('orders', '', 'view') && !has_permission_new('orders', '', 'view_own') && $canView == false) {

        access_denied('Order');
      }
    }



    $send = $this->invoices_model->send_invoice_overdue_notice($id);

    if ($send) {

      set_alert('success', _l('invoice_overdue_reminder_sent'));
    } else {

      set_alert('warning', _l('invoice_reminder_send_problem'));
    }

    redirect(admin_url('order/list_orders/' . $id));
  }



  /* Generates invoice PDF and senting to email of $send_to_email = true is passed */

  public function pdf($id)

  {

    if (!$id) {

      redirect(admin_url('order/list_orders'));
    }



    $canView = user_can_view_invoice($id);

    if (!$canView) {

      access_denied('Order');
    } else {

      if (!has_permission_new('orders', '', 'view') && !has_permission_new('invoices', '', 'view_own') && $canView == false) {

        access_denied('Order');
      }
    }



    $invoice        = $this->invoices_model->get($id);

    $invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);

    $invoice_number = format_invoice_number($invoice->id);



    try {

      $pdf = invoice_pdf($invoice);
    } catch (Exception $e) {

      $message = $e->getMessage();

      echo $message;

      if (strpos($message, 'Unable to get the size of the image') !== false) {

        show_pdf_unable_to_get_image_size_error();
      }

      die;
    }



    $type = 'D';



    if ($this->input->get('output_type')) {

      $type = $this->input->get('output_type');
    }



    if ($this->input->get('print')) {

      $type = 'I';
    }



    $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
  }



  public function mark_as_sent($id)

  {

    if (!$id) {

      redirect(admin_url('order/list_orders'));
    }

    if (!user_can_view_invoice($id)) {

      access_denied('Invoice Mark As Sent');
    }



    $success = $this->invoices_model->set_invoice_sent($id, true);



    if ($success) {

      set_alert('success', _l('invoice_marked_as_sent'));
    } else {

      set_alert('warning', _l('invoice_marked_as_sent_failed'));
    }



    redirect(admin_url('order/list_orders/' . $id));
  }



  public function get_due_date()

  {

    if ($this->input->post()) {

      $date    = $this->input->post('date');

      $duedate = '';

      if (get_option('invoice_due_after') != 0) {

        $date    = to_sql_date($date);

        $d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));

        $duedate = _d($d);

        echo $duedate;
      }
    }
  }

  public function export_order_list()

  {

    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $data = array(

        'from_date' => $this->input->post('from_date'),

        'to_date'  => $this->input->post('to_date')

      );

      $data = $this->order_model->load_data_for_order($data);

      $this->load->model('sale_reports_model');

      $selected_company_details    = $this->sale_reports_model->get_company_detail();



      $writer = new XLSXWriter();

      //$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

      //$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

      //$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

      //$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



      $company_name = array($selected_company_details->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $selected_company_details->address;

      $company_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_addr);



      $msg = "Filtes Date From " . $this->input->post('from_date') . " To " . $this->input->post('to_date');

      $filter = array($msg);

      $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $filter);



      // empty row

      $list_add = [];

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $writer->writeSheetRow('Sheet1', $list_add);





      $set_col_tk = [];

      $set_col_tk["Order No."] =  'Order No.';

      $set_col_tk["Order Date"] = 'Order Date';

      $set_col_tk["SalesID"] = 'SalesID';

      $set_col_tk["SalesDate"] = 'SalesDate';

      $set_col_tk["Challan"] = 'Challan';

      $set_col_tk["Party Name"] = 'Party Name';

      $set_col_tk["OrderAmt"] = 'OrderAmt';

      $set_col_tk["BillAmt"] = 'BillAmt';

      $set_col_tk["Order Type"] = 'Order Type';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);

      $selected_company = $this->session->userdata('root_company');

      $orderSum = 0;

      $saleSum = 0;

      foreach ($data as $k => $value) {



        $list_add = [];

        $list_add[] = $value["OrderID"];

        $orderdate = _d(substr($value["Transdate"], 0, 10));

        $list_add[] = $orderdate;

        $list_add[] = $value["SalesID"];

        $saledate = _d(substr($value["SaleDate"], 0, 10));

        $list_add[] = $saledate;

        $list_add[] = $value["ChallanID"];

        $account_name = get_account_name($value['AccountID'], $selected_company);

        $list_add[] = $account_name->company;

        $list_add[] = $value['OrderAmt'];

        $orderSum += $value['OrderAmt'];

        $list_add[] = $value['BillAmt'];

        $saleSum += $value['BillAmt'];

        $list_add[] = $value['order_type'];



        $writer->writeSheetRow('Sheet1', $list_add);
      }



      $list_add = [];

      $list_add[] = 'Total';

      $list_add[] = '';

      $list_add[] = '';

      $list_add[] = '';

      $list_add[] = '';

      $list_add[] = '';

      $list_add[] = $orderSum;

      $list_add[] = $saleSum;

      $list_add[] = '';



      $writer->writeSheetRow('Sheet1', $list_add);

      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'SaleReport.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }

  public function load_data_for_order()
  {

    $data = array(

      'from_date' => $this->input->post('from_date'),

      'to_date'  => $this->input->post('to_date')

    );

    $selected_company = $this->session->userdata('root_company');

    $data1 = $this->order_model->load_data_for_order($data);

    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($data1 as $value) {

      $html .= '<tr>';

      $numberOutput = '<a href="' . admin_url('order/order_details/' . $value['OrderID']) . '" target="_blank" style="font-size: 11px;">' . $value['OrderID'] . '</a>';

      $numberOutput1 = '<a href="' . admin_url('order/order/' . $value['OrderID']) . '" target="_blank" style="font-size: 11px;">' . $value['OrderID'] . '</a>';

      $html .= '<td style="text-align:center;">' . $sr . '</td>';

      $html .= '<td>' . $numberOutput1 . '</td>';

      $html .= '<td style="text-align:center;">' . date("d/m/Y", strtotime(substr($value['Transdate'], 0, 10))) . '</td>';

      $html .= '<td style="text-align:center;">' . $value['SalesID'] . '</td>';

      $html .= '<td style="text-align:center;">' . date("d/m/Y", strtotime(substr($value['SaleDate'], 0, 10))) . '</td>';





      $html .= '<td style="text-align:center;">' . $value['ChallanID'] . '</td>';

      $account_name = get_account_name($value['AccountID'], $selected_company);

      $html .= '<td>' . $account_name->company . '</td>';

      $html .= '<td style="text-align:right;">' . $value['OrderAmt'] . '</td>';

      $ordersum += $value['OrderAmt'];

      $html .= '<td style="text-align:right;">' . $value['BillAmt'] . '</td>';

      $salesum += $value['BillAmt'];

      $html .= '<td style="text-align:center;">' . $value['order_type'] . '</td>';



      $html .= '</tr>';

      $sr++;
    }

    $html .= '<tr>';

    $html .= '<td colspan="7">Total</td>';

    $html .= '<td style="text-align:right;">' . $ordersum . '</td>';

    $html .= '<td style="text-align:right;">' . $salesum . '</td>';

    $html .= '<td></td>';

    $html .= '</tr>';

    echo json_encode($html);
  }



  public function export_pending_order()

  {

    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $data = array(

        'dates' => $this->input->post('dates'),

        'order_type'  => $this->input->post('order_type'),

        'state'  => $this->input->post('state'),

        'dist_type'  => $this->input->post('dist_type'),

        'selected_ids'  => $this->input->post('selected_ids')

      );

      if (empty($data['selected_ids'])) {

        $data_array = $this->order_model->load_data($data);
      } else {

        $data_array = $this->order_model->load_data2($data);
      }



      $this->load->model('sale_reports_model');

      $selected_company_details    = $this->sale_reports_model->get_company_detail();



      $writer = new XLSXWriter();

      //$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

      //$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

      //$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

      //$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



      $company_name = array($selected_company_details->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 11);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $selected_company_details->address;

      $company_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 11);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_addr);



      $distributor_id = $this->input->post('dist_type');

      $state_id = $this->input->post('state');

      $order_type = $this->input->post('order_type');

      $dates = $this->input->post('dates');



      $data_state_name  = $this->db->get_where('tblxx_statelist', array('short_name' => $state_id))->row_array();

      $data_distributor_name  = $this->db->get_where('tblcustomers_groups', array('id' => $distributor_id))->row_array();



      $msg = "Pending Order For Date : " . $dates . " State " . $data_state_name["state_name"] . " Order type :" . $order_type . ", Distributor Type : " . $data_distributor_name["name"];

      $filter = array($msg);

      $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 11);  //merge cells

      $writer->writeSheetRow('Sheet1', $filter);



      // empty row

      $list_add = [];

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $writer->writeSheetRow('Sheet1', $list_add);





      $set_col_tk = [];

      $set_col_tk["SrNo"] =  'SrNo';

      $set_col_tk["OrderId"] =  'OrderId';

      $set_col_tk["Transdate"] = 'Transdate';

      $set_col_tk["SONAME"] = 'SO Name';

      $set_col_tk["Account_Name"] = 'Account Name';

      $set_col_tk["Station"] = 'Station';

      $set_col_tk["Dist Type"] = 'Dist Type';

      $set_col_tk["State"] = 'State';

      $set_col_tk["Close BalAmt"] = 'Close BalAmt';

      $set_col_tk["orderAmt"] = 'orderAmt';

      $set_col_tk["status"] = 'Status';

      $set_col_tk["Remark (if any)"] = 'Remark (if any)';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);



      $j = 4;

      $i = 1;

      $BalTotal = 0;

      $OrderSum = 0;

      foreach ($data_array as $k => $value) {

        $bal_new = $value["bal1"] + $value["balance"];

        $BalTotal += $bal_new;

        $list_add = [];

        $list_add[] = $i;

        $list_add[] = $value["OrderID"];

        $date = _d(substr($value["Transdate"], 0, 10));

        $list_add[] = $date;

        $list_add[] = $value["SOID"];

        $list_add[] = $value["AccountName"];



        $list_add[] = $value["StationName"];

        $list_add[] = $value["dist_Type"];

        $list_add[] = $value["StateName"];

        //$bal = $value["bal1"] + $value["bal2"] + $value["bal3"] + $value["bal4"] + $value["bal5"] + $value["bal6"] + $value["bal7"] + $value["bal8"] + $value["bal9"] + $value["bal10"] + $value["bal11"] + $value["bal12"] + $value["bal13"];

        $list_add[] = $bal_new;

        $list_add[] = $value["OrderAmt"];

        $OrderSum += $value["OrderAmt"];

        if ($value["OrderStatus"] == "C") {

          $cc = "checked";

          $c = "Yes";

          $status = "Cancel";
        } else {

          $cc = "";

          $c = "";

          $status = "Open";
        }

        $list_add[] = $status;

        $list_add[] = $value["remark"];



        $writer->writeSheetRow('Sheet1', $list_add);

        $j++;

        $i++;
      }



      // Total row

      $list_add = [];

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "Total";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = $BalTotal;

      $list_add[] = $OrderSum;

      $list_add[] = "";

      $list_add[] = "";

      $writer->writeSheetRow('Sheet1', $list_add);



      // empty row

      $list_add = [];

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $writer->writeSheetRow('Sheet1', $list_add);

      $j++;

      $j++;

      $filter = array("Pending Order Item Data");

      $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells

      $writer->writeSheetRow('Sheet1', $filter);



      $set_col_tk1 = [];

      $set_col_tk1["ItemID"] =  'ItemID';

      $set_col_tk1["ItemName"] =  'ItemName';

      $set_col_tk1["Pack"] =  'Pack';

      $set_col_tk1["OrderQty"] =  'OrderQty';

      $set_col_tk1["CurrStock"] =  'CurrStock';

      $set_col_tk1["Production Plan"] =  'Production Plan';

      $set_col_tk1["Remark"] =  'Remark';

      $writer_header1 = $set_col_tk1;

      $writer->writeSheetRow('Sheet1', $writer_header1);



      $data = array(

        'dates' => $this->input->post('dates'),

        'order_type'  => $this->input->post('order_type'),

        'state'  => $this->input->post('state'),

        'dist_type'  => $this->input->post('dist_type'),

        'selected_ids'  => $this->input->post('selected_ids')

      );

      if (empty($data['selected_ids'])) {

        $data_items = $this->order_model->load_data_items($data);
      } else {

        $data_items = $this->order_model->load_data_items2($data);
      }



      $total_cases = 0;

      $total_taxableamt = 0.00;

      $total_netamt = 0.00;

      foreach ($data_items as $k1 => $value1) {

        if ($value1["NetOrderAmt"] == "0.00") {
        } else {



          $list_add = [];

          $list_add[] = $value1["Item_code"];

          $list_add[] = $value1["description"];

          $list_add[] = $value1["CaseQty"];

          $ordqty = $value1["OrderQty"] / $value1["CaseQty"];

          $list_add[] = $ordqty;

          $list_add[] = round($value1["StockBal"], 2);

          $total_taxableamt += $value1["OrderAmt"];

          $total_netamt += $value1["NetOrderAmt"];

          $total_cases += $ordqty;



          $list_add[] = '';

          $list_add[] = '';

          $writer->writeSheetRow('Sheet1', $list_add);
        }
      }

      $total_tax = $total_netamt - $total_taxableamt;

      // Total row

      $list_add = [];

      $list_add[] = "";

      $list_add[] = "Total";

      $list_add[] = "";

      $list_add[] = $total_cases;

      $list_add[] = "";

      $list_add[] = "";

      $list_add[] = "";

      $writer->writeSheetRow('Sheet1', $list_add);

      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'PendingOrder_Report.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }



  public function Get_anamat_request()

  {

    $data = array(

      'from_date' => date('d/m/Y'),

      'to_date'  => date('d/m/Y'),

      'account_type'  => '',

      'center'  => '',

      'IsApprove'  => ''

    );

    $reject_by_broker_delay = $this->order_model->reject_by_delay_broker_approval();

    $reject_by_kirti_delay = $this->order_model->reject_by_delay_kirti_approval();

    $OrderList = $this->order_model->GetAnamatRequest($data);



    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($OrderList as $value) {



      if ($value['company'] == "") {

        $PartyName = $value['firstname'] . " " . $value['lastname'];
      } else {

        $PartyName = $value['company'];
      }

      if ($value['BName'] == "") {

        $BrokerName = $value['Bfirstname'] . " " . $value['Blastname'];
      } else {

        $BrokerName = $value['BName'];
      }

      if ($value['CustomerType'] == "1") {

        $AccountType = "Farmer";
      } elseif ($value['CustomerType'] == "3") {

        $AccountType = "Trader";
      } elseif ($value['CustomerType'] == "2") {

        $AccountType = "Broker";
      } elseif ($value['CustomerType'] == "4") {

        $AccountType = "Corporate/Processor";
      } else {

        $AccountType = "";
      }

      $status = '';

      if ($value['IsApprove'] == 'Y') {

        $status = 'Accepted';
      } elseif ($value['IsApprove'] == 'N') {

        $status = 'Rejected';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {

        $status = 'Awaiting Client Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {

        $status = 'Awaiting Broker Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {

        $status = '--';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {

        $status = '--';
      }

      if ($value['e_quantity'] == '' || $value['e_quantity'] == null) {

        $Qty = $value['quantity'];
      } else {

        $Qty = $value['e_quantity'];
      }



      $html .= '<tr class="GetDetails" data-id="' . $value["BookingID"] . '">';

      $html .= '<td style="text-align:left;">' . $sr . '</td>';

      $html .= '<td style="text-align:left;">' . $value['CenterName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['ItemName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['basic_rate'] . '</td>';

      $html .= '<td style="text-align:left;">' . $Qty . ' ' . $value['unit'] . '</td>';



      if ($value['IsApprove'] == 'Y') {

        $html .= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
      } elseif ($value['IsApprove'] == 'N') {

        $html .= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      }

      $html .= '<td style="text-align:left;">' . $BrokerName . '</td>';

      $html .= '<td>' . $PartyName . '</td>';

      $html .= '<td style="text-align:left;">' . $status . '</td>';

      $html .= '</tr>';

      $sr++;
    }



    echo $html;
  }



  public function Get_trade_finance_request()

  {

    $data = array(

      'from_date' => date('d/m/Y'),

      'to_date'  => date('d/m/Y'),

      'account_type'  => '',

      'center'  => '',

      'IsApprove'  => ''

    );

    $reject_by_broker_delay = $this->order_model->reject_by_delay_broker_approval();

    $reject_by_kirti_delay = $this->order_model->reject_by_delay_kirti_approval();

    $OrderList = $this->order_model->GetTradeFinanceRequest($data);



    $html = '';

    $ordersum = 0;

    $salesum = 0;

    $sr = 1;

    foreach ($OrderList as $value) {



      if ($value['company'] == "") {

        $PartyName = $value['firstname'] . " " . $value['lastname'];
      } else {

        $PartyName = $value['company'];
      }

      if ($value['BName'] == "") {

        $BrokerName = $value['Bfirstname'] . " " . $value['Blastname'];
      } else {

        $BrokerName = $value['BName'];
      }

      if ($value['CustomerType'] == "1") {

        $AccountType = "Farmer";
      } elseif ($value['CustomerType'] == "3") {

        $AccountType = "Trader";
      } elseif ($value['CustomerType'] == "2") {

        $AccountType = "Broker";
      } elseif ($value['CustomerType'] == "4") {

        $AccountType = "Corporate/Processor";
      } else {

        $AccountType = "";
      }

      $status = '';

      if ($value['IsApprove'] == 'Y') {

        $status = 'Accepted';
      } elseif ($value['IsApprove'] == 'N') {

        $status = 'Rejected';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {

        $status = 'Awaiting Client Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {

        $status = 'Awaiting Broker Approval';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {

        $status = '--';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {

        $status = '--';
      }

      if ($value['e_quantity'] == '' || $value['e_quantity'] == null) {

        $Qty = $value['quantity'];
      } else {

        $Qty = $value['e_quantity'];
      }



      $html .= '<tr class="GetDetails" data-id="' . $value["BookingID"] . '">';

      $html .= '<td style="text-align:left;">' . $sr . '</td>';

      $html .= '<td style="text-align:left;">' . $value['CenterName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['ItemName'] . '</td>';

      $html .= '<td style="text-align:left;">' . $value['basic_rate'] . '</td>';

      $html .= '<td style="text-align:left;">' . $Qty . ' ' . $value['unit'] . '</td>';



      if ($value['IsApprove'] == 'Y') {

        $html .= '<td style="text-align:left;width:8%;">Trade Accepted</td>';
      } elseif ($value['IsApprove'] == 'N') {

        $html .= '<td style="text-align:left;width:8%;">Trade Rejected</td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'NA')) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "NA")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=awaiting_for_broker() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify"  style="padding:3px 6px;" class="btn btn-defualt" disabled><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] != NULL && $value['BrokerApprove'] == "Y")) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      } elseif (($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y') && ($value['BrokerID'] == NULL)) {



        $html .= '<td style="text-align:left;width:8%;">

                <button title="Accept" onclick=acceptTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>

                <button title="Reject" onclick=rejectTrade("' . $value["BookingID"] . '") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>

                <button title="Modify" onclick=modifyTrade("' . $value["BookingID"] . '") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
      }

      $html .= '<td style="text-align:left;">' . $BrokerName . '</td>';

      $html .= '<td>' . $PartyName . '</td>';

      $html .= '<td style="text-align:left;">' . $status . '</td>';

      $html .= '</tr>';

      $sr++;
    }



    echo $html;
  }



  public function export_tradeFinance()

  {



    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $this->load->model('sale_reports_model');

      $company_detail = $this->sale_reports_model->get_company_detail();

      $data = array(

        'from_date' => date('d/m/Y'),

        'to_date'  => date('d/m/Y'),

        'account_type'  => '',

        'center'  => '',

        'IsApprove'  => ''

      );

      $result = $this->order_model->GetTradeFinanceRequest($data);



      $writer = new XLSXWriter();



      $company_name = array($company_detail->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $company_detail->address;

      $center_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $center_addr);





      $set_col_tk = [];

      $set_col_tk["Location"] =  'Location';

      $set_col_tk["Item Name"] = 'Item Name';

      $set_col_tk["Rate"] = 'Rate';

      $set_col_tk["Quantity"] = 'Quantity';

      $set_col_tk["Action"] = 'Action';

      $set_col_tk["Broker Name"] =  'Broker Name';

      $set_col_tk["Party Name"] = 'Party Name';

      $set_col_tk["Status"] = 'Status';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);

      foreach ($result as $k => $value) {



        $list_add = [];

        $list_add[] = $value["CenterName"];

        $list_add[] = $value["ItemName"];

        $list_add[] = $value["basic_rate"];

        $list_add[] = $value["e_quantity"];

        $list_add[] = $value["ClientApprove"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["is_invoice"];



        $list_add[] = $row_a;



        $writer->writeSheetRow('Sheet1', $list_add);
      }



      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'TradeFinanceTrader.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }



  public function export_anamattrader()

  {



    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $this->load->model('sale_reports_model');

      $company_detail = $this->sale_reports_model->get_company_detail();

      $data = array(

        'from_date' => date('d/m/Y'),

        'to_date'  => date('d/m/Y'),

        'account_type'  => '',

        'center'  => '',

        'IsApprove'  => ''

      );

      $result = $this->order_model->GetAnamatRequest($data);



      $writer = new XLSXWriter();



      $company_name = array($company_detail->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $company_detail->address;

      $center_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $center_addr);





      $set_col_tk = [];

      $set_col_tk["Location"] =  'Location';

      $set_col_tk["Item Name"] = 'Item Name';

      $set_col_tk["Rate"] = 'Rate';

      $set_col_tk["Quantity"] = 'Quantity';

      $set_col_tk["Action"] = 'Action';

      $set_col_tk["Broker Name"] =  'Broker Name';

      $set_col_tk["Party Name"] = 'Party Name';

      $set_col_tk["Status"] = 'Status';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);

      foreach ($result as $k => $value) {



        $list_add = [];

        $list_add[] = $value["CenterName"];

        $list_add[] = $value["ItemName"];

        $list_add[] = $value["basic_rate"];

        $list_add[] = $value["e_quantity"];

        $list_add[] = $value["ClientApprove"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["is_invoice"];



        $list_add[] = $row_a;



        $writer->writeSheetRow('Sheet1', $list_add);
      }



      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'AnamatTrader.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }



  public function export_selltrader()

  {



    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $company_detail = $this->sale_reports_model->get_company_detail();

      $result = $this->order_model->GetSaleRequest(array());



      $writer = new XLSXWriter();



      $company_name = array($company_detail->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $company_detail->address;

      $center_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $center_addr);





      $set_col_tk = [];

      $set_col_tk["Location"] =  'Location';

      $set_col_tk["Item Name"] = 'Item Name';

      $set_col_tk["Rate"] = 'Rate';

      $set_col_tk["Quantity"] = 'Quantity';

      $set_col_tk["Action"] = 'Action';

      $set_col_tk["Broker Name"] =  'Broker Name';

      $set_col_tk["Party Name"] = 'Party Name';

      $set_col_tk["Status"] = 'Status';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);

      foreach ($result as $k => $value) {



        $list_add = [];

        $list_add[] = $value["CenterName"];

        $list_add[] = $value["ItemName"];

        $list_add[] = $value["basic_rate"];

        $list_add[] = $value["e_quantity"];

        $list_add[] = $value["ClientApprove"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["is_invoice"];



        $list_add[] = $row_a;



        $writer->writeSheetRow('Sheet1', $list_add);
      }



      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'SellTrader.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }



  public function export_purchasetrader()

  {



    if (!class_exists('XLSXReader_fin')) {

      require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
    }

    require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



    if ($this->input->post()) {



      $data = array(

        'from_date' => date('d/m/Y'),

        'to_date'  => date('d/m/Y'),

        'account_type'  => '',

        'center'  => '',

        'IsApprove'  => ''

      );

      $company_detail = $this->sale_reports_model->get_company_detail();

      $result = $this->order_model->GetPurchaseRequest($data);

      $writer = new XLSXWriter();



      $company_name = array($company_detail->company_name);

      $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $company_name);



      $address = $company_detail->address;

      $center_addr = array($address,);

      $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

      $writer->writeSheetRow('Sheet1', $center_addr);





      $set_col_tk = [];

      $set_col_tk["Location"] =  'Location';

      $set_col_tk["Item Name"] = 'Item Name';

      $set_col_tk["Rate"] = 'Rate';

      $set_col_tk["Quantity"] = 'Quantity';

      $set_col_tk["Action"] = 'Action';

      $set_col_tk["Broker Name"] =  'Broker Name';

      $set_col_tk["Party Name"] = 'Party Name';

      $set_col_tk["Status"] = 'Status';

      $writer_header = $set_col_tk;

      $writer->writeSheetRow('Sheet1', $writer_header);

      foreach ($result as $k => $value) {



        $list_add = [];

        $list_add[] = $value["CenterName"];

        $list_add[] = $value["ItemName"];

        $list_add[] = $value["basic_rate"];

        $list_add[] = $value["e_quantity"];

        $list_add[] = $value["ClientApprove"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["BName"];

        $list_add[] = $value["is_invoice"];

        $list_add[] = $row_a;



        $writer->writeSheetRow('Sheet1', $list_add);
      }



      $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

      foreach ($files as $file) {

        if (is_file($file)) {

          unlink($file);
        }
      }

      $filename = 'PurchaseTrader.xlsx';

      $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

      echo json_encode([

        'site_url'          => site_url(),

        'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,

      ]);

      die;
    }
  }



  public function Purchasetradepunch()

  {

    if (!has_permission_new('Purchasetrade_punch', '', 'view')) {

      access_denied('order list');
    }

    $data['title']                = "Purchase Trade Punch";

    $this->load->model('accounts_master_model');

    $data['company_detail'] = $this->accounts_master_model->get_company_detail();

    $data['center'] = $this->order_model->GetAllActiveCenterList();

    $this->load->view('admin/order/purchasetradepunch', $data);
  }



  public function verifyOTP()

  {

    $phoneNumber = $this->input->post('phoneNumber');

    $otp = $this->input->post('otp');

    $authKey = '406120AG34t47jvSiB6513fd24P1';



    $curl = curl_init();

    curl_setopt_array($curl, array(

      CURLOPT_URL => 'https://control.msg91.com/api/v5/otp/verify?mobile=91' . $phoneNumber . '&otp=' . $otp . '',

      CURLOPT_RETURNTRANSFER => true,

      CURLOPT_ENCODING => '',

      CURLOPT_MAXREDIRS => 10,

      CURLOPT_TIMEOUT => 0,

      CURLOPT_FOLLOWLOCATION => true,

      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

      CURLOPT_CUSTOMREQUEST => 'GET',

      CURLOPT_HTTPHEADER => array(

        'authkey: ' . $authKey . ''

      ),

    ));



    $response = curl_exec($curl);

    $response = json_decode($response, true);

    curl_close($curl);

    if ($response["type"] == 'success') {

      echo true;
    } else {

      echo false;
    }
  }



  public function GetCommodity()

  {

    $CenterID = $this->input->post('CenterID');

    $CommodityList = $this->order_model->GetCommodityListForCompanyMaster($CenterID);

    echo json_encode($CommodityList);
  }



  public function GetItemId()

  {

    $CenterID = $this->input->post('center');

    $CommodityID = $this->input->post('CommodityID');

    $ItemIDList = $this->order_model->GetItemListForCompanyMaster($CenterID, $CommodityID);

    echo json_encode($ItemIDList);
  }



  public function fetchItemRate()

  {

    $item = $this->input->post('item');

    $center = $this->input->post('center');

    $AccountType = $this->input->post('AccountType');

    if ($AccountType == "1") {

      $AccountType = $AccountType;
    } else {

      $AccountType = 2;
    }

    $Rate = $this->GateControl_model->GetTodaysRate($item, $center, $AccountType);

    echo json_encode($Rate);
  }



  public function fetchClientData()

  {

    $AccountID = $this->input->post('AccountID');

    $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

    $result = $this->db->get(db_prefix() . 'clients')->row();

    echo json_encode($result);
  }



  public function SaveOrder($params = FALSE)
  {
    $fy = $this->session->userdata('finacial_year');
    $selected_company = $this->session->userdata('root_company');
    $CenterID = $this->input->post('center');
    $ItemID = $this->input->post('item');
    $AccountType = $this->input->post('AccountType');
    $TradeType = $this->input->post('TradeType');
    $FreightTerms = $this->input->post('FreightTerms');
    $PartyDetails = $this->GetPurchaseForParty($CenterID, $ItemID);
    if ($PartyDetails) {
      $PartyID = $PartyDetails->PartyID;
    } else {
      $PartyID = "KASPL";
    }

    if ($TradeType == "P") {
      $new_Number = get_number($CenterID, 'S');
      $Prefix = "S";
      $Type = "P";
      $TType2 = "Purchase";
      $IsApprove = "Y";
      $ApproveTime = date('Y-m-d H:i:s');
      $ApproveUserID = $this->session->userdata('username');
    } else {
      $PartyID = "KASPL";
      $new_Number = get_number($CenterID, 'D');
      $Prefix = "D";
      $Type = "D";
      $TType2 = "Deposite";
      $IsApprove = "NA";
      $ApproveTime = NULL;
      $ApproveUserID = NULL;
    }

    $number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
    $bookingID = $CenterID . $Prefix . date('d') . date('m') . date('y') . $number;
    $Cropsale_data = array(
      "FY" => $fy,
      "PlantID" => $selected_company,
      "BookingID" => $bookingID,
      "PartyID" => $PartyID,
      "TransDate" => date('Y-m-d H:i:s'),
      "TType" => $Type,
      "TType2" => $TType2,
      "AccountID" => $this->input->post('AccountID'),
      "UserID" => $this->session->userdata('username'),
      "BrokerID" => $this->input->post('AccountID'),
      "CenterID" => $CenterID,
      "ItemID" => $ItemID,
      "quantity" => $this->input->post('Quantity'),
      "e_quantity" => $this->input->post('Quantity'),
      "max_quantity" => $this->input->post('MaxQuantity') ?? $this->input->post('Quantity'),
      "unit" => 'MT',
      "basic_rate" => $this->input->post('CurrentRate'),
      "Mastercurrentrate" => $this->input->post('Mastercurrentrate'),
      'FreightTerms' => $FreightTerms,
      "IsApprove" => $IsApprove,
      "ApproveTime" => $ApproveTime,
      "ApproveUserID" => $ApproveUserID,
      "ClientApprove" => "Y",
      "BrokerApproveTime" => date('Y-m-d H:i:s'),
      "BrokerApprove" => "Y"
    );
    $this->db->insert(db_prefix() . 'lead_master', $Cropsale_data);
    $insert_id = $this->db->insert_id();
    if ($insert_id) {
      if ($TradeType == "P") {
        $this->increment_crop_sale_number($CenterID, 'S');
      } else {
        // add Trade details for storage
        $deposittradedata = array(
          'MinQty' => 0,
          'DepositPeriod' => 0,
          'RateType' => 1,
          'IsFumigation' => 1,
          'RateIncFumigation' => 1,
          'FumigationAmt' => 0,
          'CreditDays' => 30
        );
        $tradeResult = $this->order_model->ModifyDepositTradeDb($deposittradedata, $bookingID);
        $this->increment_crop_sale_number($CenterID, 'D');
      }

      $GetBookingDetails = $this->order_model->GetBookingDetails($bookingID);
      if ($GetBookingDetails->CenterType == "W") {
        $CenterType = "1";
      } else {
        $CenterType = "0";
      }
      // Send data to Pcsoft if trade approval after modify by kirti
      if ($GetBookingDetails->TType == "P") {

        $trinvs_array = array([

          "doc_type" => "37",

          "party_st" => "C",

          "party_no" => $GetBookingDetails->ShortCode,

          "doc_ref" => $GetBookingDetails->BookingID,

          "im_loc" => $GetBookingDetails->CenterID,
          "CustomerType" => $GetBookingDetails->CustomerType

          // "im_loc"=>$GetBookingDetails->PCCenterID

        ]);

        $sporddtl_array = array([

          // "IM_CODE"=>$GetBookingDetails->PCItemID,

          "IM_CODE" => $GetBookingDetails->ItemID,

          "im_qty" => $GetBookingDetails->e_quantity,

          "im_ordrate" => $GetBookingDetails->basic_rate

        ]);



        $data_po_array =  array(

          "cocd" => $GetBookingDetails->PartyID,

          "Type" => $CenterType,

          "trinvs" => $trinvs_array,

          "sporddtl" => $sporddtl_array

        );

        $po_data = json_encode($data_po_array);



        $curl = curl_init();

        curl_setopt_array(
          $curl,
          array(



            // CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/posoSubmit", //  -> LIVE URL

            // CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/posoSubmit", // -> DEV URL

            CURLOPT_URL => "https://kirtierp.globalinfocloud.in/api/v1/Purchase/Order", // -> New Kriti erp

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 30,

            CURLOPT_CUSTOMREQUEST => "POST",

            CURLOPT_POSTFIELDS => $po_data,

            CURLOPT_HTTPHEADER => array(

              "content-type: application/json"

            ),

          )

        );

        $response = curl_exec($curl);

        $response_array = json_decode($response);

        if ($response_array->status) {

          $id = $response_array->data->id;

          // echo json_encode($id);

          $insert_referance = array(

            "Type" => $GetBookingDetails->TType,

            "Name" => "Trade",

            "GIC_Reference" => $GetBookingDetails->BookingID,

            "pcsoft_doc_ref" => $id

          );

          $this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
        }

        // $PcSoft_po = $response_array->doc_ref_number;

        // $status = $response_array->Status;

        // if($status == true){

        //     $insert_referance = array(

        //         "Type"=>$GetBookingDetails->TType,

        //         "Name"=>"Trade",

        //         "GIC_Reference"=>$GetBookingDetails->BookingID,

        //         "pcsoft_doc_ref"=>$PcSoft_po

        //     );

        //     $this->db->insert(db_prefix().'pcsoft_gic_number_referance', $insert_referance);

        // }

        $err = curl_error($curl);

        curl_close($curl);
      }

      // Auto ASN Generate only for Farmer

      if ($AccountType == "1" && $TradeType == "P") {

        $vehicleNo = $this->input->post('vehicle_number');

        // Generate ASN

        $ASN_Number = get_number($CenterID, 'ASN');

        $ASNnumber = str_pad($ASN_Number, 4, '0', STR_PAD_LEFT);

        $AsnID = "ASN" . $CenterID . date('d') . date('m') . date('y') . $ASNnumber;

        $asn_data = array(

          'AccountID' => $this->input->post('AccountID'),

          'ASNID' => $AsnID,

          'PlantID' => $selected_company,

          'FY' => $fy,

          'status' => 1,

          'BookingID' => $bookingID,

          'PartyID' => $PartyID,

          'CenterID' => $CenterID,

          'basic_rate' => $this->input->post('CurrentRate'),

          'Mastercurrentrate' => $this->input->post('Mastercurrentrate'),

          'ItemID' => $ItemID,

          'quantity' => $this->input->post('QuantityBag') ?? 0,

          'Asn_WT_MT' => $this->input->post('Quantity'),

          'unit' => "MT",

          'asn_date' => date('Y-m-d H:i:s'),

          'asn_by' => $this->session->userdata('username'),

          "TType" => "P",

          "TType2" => "Purchase",

          'VehicleNo' => $vehicleNo,

          'Phone' => $this->input->post('driver_mobile')

        );

        if ($this->db->insert(db_prefix() . 'GateMaster', $asn_data)) {

          // Generate QR

          /* Load QR Code Library */

          $this->load->library('ciqrcode');



          /* Data */

          $hex_data   = bin2hex($AsnID);

          $save_name  = $hex_data . '.png';



          /* QR Code File Directory Initialize */

          $dir = 'assets/media/qrcode/';

          if (!file_exists($dir)) {

            mkdir($dir, 0775, true);
          }



          /* QR Configuration  */

          $config['cacheable']    = true;

          $config['imagedir']     = $dir;

          $config['quality']      = true;

          $config['size']         = '1024';

          $config['black']        = array(255, 255, 255);

          $config['white']        = array(255, 255, 255);

          $this->ciqrcode->initialize($config);



          /* QR Data  */

          $params['data']     = $AsnID . ',' . $bookingID . ',' . "1";

          $params['level']    = 'L';

          $params['size']     = 10;

          $params['savename'] = FCPATH . $config['imagedir'] . $save_name;



          $this->ciqrcode->generate($params);



          /* Return Data */

          $QR = array(

            'content' => $AsnID . ',' . $bookingID,

            'file'    => $dir . $save_name,

            'name'    => $save_name

          );



          $this->db->where('BookingID', $bookingID);

          $this->db->where('ASNID', $AsnID);

          $this->db->set('ASNQR', $QR['name']);

          $this->db->set('status', 1);

          $this->db->update('tblGateMaster');

          $this->increment_number($CenterID, 'ASN');



          $this->db->select('tbllead_master.*,tblclients.company,tblclients.ShortCode');

          $this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');

          $this->db->where('tbllead_master.BookingID', $bookingID);

          $leadMasterDetails = $this->db->get('tbllead_master')->row();

          // Send to PC Soft  ASN data

          $trinvs_array = array([

            "party_no" => $leadMasterDetails->ShortCode,

            "your_ref" => $leadMasterDetails->BookingID,

            "truck_no" => $vehicleNo,

            "doc_ref" => $AsnID,

            "your_date" => date('Y-m-d H:i:s'),

            "doc_flnm" => NULL,

            "lr_no" => NULL,

            "lr_date" => NULL,

            "type_code" => NULL,

          ]);

          $sporddtl_array = array([

            "im_code" => $GetBookingDetails->PCItemID,

            "im_qty" => $this->input->post('Quantity'),

            "im_bag" => $this->input->post('QuantityBag'),

            "im_ordrate" => $leadMasterDetails->basic_rate

          ]);



          $data_asn_array =  array(

            "cocd" => $leadMasterDetails->PartyID,

            "trinvs" => $trinvs_array,

            "sporddtl" => $sporddtl_array

          );

          $ASN_data = json_encode($data_asn_array);

          $curl = curl_init();

          curl_setopt_array(
            $curl,
            array(

              //-> LIVE URL

              CURLOPT_URL => "http://45.64.85.182:7731/ERP/API/SaleOrder/ASNinsert", // Live

              //CURLOPT_URL => "https://app.ieverp.com/TRIP/API/SaleOrder/ASNinsert",// -> DEV URL

              CURLOPT_RETURNTRANSFER => true,

              CURLOPT_MAXREDIRS => 10,

              CURLOPT_TIMEOUT => 30,

              CURLOPT_CUSTOMREQUEST => "POST",

              CURLOPT_POSTFIELDS => $ASN_data,

              CURLOPT_HTTPHEADER => array(

                "content-type: application/json"

              ),

            )

          );

          $apiResponse = curl_exec($curl);

          $err = curl_error($curl);

          curl_close($curl);



          $response_array = json_decode($apiResponse);

          $PcSoft_GIN = $response_array->doc_ref_number;

          $status = $response_array->Status;

          if ($status == true) {

            $insert_referance = array(

              "Type" => "P",

              "Name" => "ASN",

              "GIC_Reference" => $AsnID,

              "pcsoft_doc_ref" => $PcSoft_GIN

            );

            $this->db->insert(db_prefix() . 'pcsoft_gic_number_referance', $insert_referance);
          }
        }
      }

      echo json_encode(true);

      die;
    }

    echo json_encode(false);

    die;
  }



  public function increment_number($CenterID, $TType)

  {

    $this->db->set('Number', 'Number+1', false);

    $this->db->WHERE('CenterID', $CenterID);

    $this->db->WHERE('TType', $TType);

    $this->db->update(db_prefix() . 'numberformat');
  }



  public function GetPurchaseForParty($CenterID, $ItemID)

  {

    $this->db->select('tblCommisionMatrix.*');

    $this->db->where('CenterID', $CenterID);

    $this->db->where('ItemID', $ItemID);

    //$this->db->where('IsOn', "Y");

    $this->db->where('IsActive', "Y");

    $Partydetails = $this->db->get(db_prefix() . 'CommisionMatrix')->row();

    return $Partydetails;
  }



  public function increment_crop_sale_number($CenterID, $TType)

  {

    $this->db->set('Number', 'Number+1', false);

    $this->db->WHERE('CenterID', $CenterID);

    $this->db->WHERE('TType', $TType);

    $this->db->update(db_prefix() . 'numberformat');
  }
}
