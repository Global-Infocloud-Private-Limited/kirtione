<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transportation extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
        $this->load->model('Transportation_model');
        $this->load->model('departments_model');
        $this->load->model('Transportation_model');

    }
    public function index()
    {
        if (!has_permission_new('TransportMaster', '', 'view')) {
            access_denied('TransportMaster');
        }
        //echo "hello";
        $data['state'] = $this->Transportation_model->getallstate();
        $data['country'] = $this->Transportation_model->getallcountry();
        $data['city'] = $this->Transportation_model->getallcity();
        $data['accounttype'] = $this->Transportation_model->GetAccountType();
        $data['account_types'] = $this->Transportation_model->get_accoun_main_group();
        //$data['accounts'] = $this->Transportation_model->get_accounts_list();
        $data['company_detail'] = $this->Transportation_model->get_company_detail();
        $data['state_list'] = $this->Transportation_model->get_state();


        $this->load->view('admin/transportation/transport', $data);
    }

    public function AccountListPopUp()
    {
        $AccountLedger = $this->Transportation_model->GetAccountList();
        $html = "";
        foreach ($AccountLedger as $key => $value) {
            $html .= '<tr class="get_AccountID" data-id=" ' . $value["id"] . '">';
            $html .= '<td>' . $value["TransportID"] . '</td>';
            $html .= '<td>' . $value["TransportName"] . '</td>';
            $html .= '<td>' . $value["state"] . '</td>';
            $html .= '<td>' . $value["city_name"] . '</td>';
            $html .= '<td>' . $value['PAN'] . '</td>';
            $html .= '</tr>';
        }
        // print_r($value);
        echo $html;
    }

    public function GetCity()
    {
        $StateID = $this->input->post('StateID');
        //Fetch State Shortcode
        $this->db->where('tblxx_statelist.short_name', $StateID);
        $stateID = $this->db->get('tblxx_statelist')->row();
        $CityList = $this->clients_model->GetCityListForCompanyMaster($stateID->short_name);
        echo json_encode($CityList);
    }

    public function GetCityFromState()
    {
        $StateID = $this->input->post('state');

        $CityList = $this->clients_model->GetCityList($StateID);
        $html = '<option value="">Non Selected</option>';
        foreach ($CityList as $key => $value) {
            $html .= '<option value="' . $value['id'] . '" >' . $value['city'] . '</option>';
        }
        echo $html;
        die;
    }

    public function GetAccountDetailByID()
    {
        $AccountID = $this->input->post('id');
        $AccountDetails = $this->Transportation_model->GetAccountDetails($AccountID);
        echo json_encode($AccountDetails);
    }


    public function UpdateAccountID()
    {
        $response = array(); // Initialize an empty response array

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!has_permission_new('TransportMaster', '', 'edit')) {
                access_denied('TransportMaster');
            }

            // All images uploaded successfully, update other form data
            $data = array();

            $upload_dir = "uploads/transport/";
            $image_fields = array();
            if (!empty($_FILES['PAN_Card_image']["name"])) {
                array_push($image_fields, 'PAN_Card_image');
            }
            if (!empty($_FILES['gst_certification_image']["name"])) {
                array_push($image_fields, 'gst_certification_image');
            }
            if (!empty($_FILES['aadhaar_image']["name"])) {
                array_push($image_fields, 'aadhaar_image');
            }
            if (!empty($_FILES['shop_act_image']["name"])) {
                array_push($image_fields, 'shop_act_image');
            }
            if (!empty($_FILES['transport_permit']["name"])) {
                array_push($image_fields, 'transport_permit');
            }
            if (!empty($_FILES['cancel_cheque']["name"])) {
                array_push($image_fields, 'cancel_cheque');
            }
            if (!empty($_FILES['ownership_photo']["name"])) {
                array_push($image_fields, 'ownership_photo');
            }
            if (!empty($_FILES['address_proof']["name"])) {
                array_push($image_fields, 'address_proof');
            }

            foreach ($image_fields as $image_field) {
                if (isset($_FILES[$image_field]) && $_FILES[$image_field]["error"] == 0) {
                    $image_filename = $_FILES[$image_field]["name"];
                    $image_temp_name = $_FILES[$image_field]["tmp_name"];
                    $image_target_path = $upload_dir . $image_filename;

                    if (move_uploaded_file($image_temp_name, $image_target_path)) {
                        // Image uploaded successfully
                        $data[$image_field] = $image_target_path;
                    } else {
                        // Failed to upload the image
                        echo "Failed to upload the $image_field.";
                        // Handle the error (return false, log error, etc.)
                    }
                }
            }

            // Populate other form data into $data array
            $data['TransportName'] = $this->input->post('TransportName');
            $data['UserID'] = $this->input->post('user_id');
            $data['TransportID'] = $this->input->post('AccountID');
            $data['state'] = $this->input->post('state');
            $data['city'] = $this->input->post('city');
            $data['address'] = $this->input->post('address');
            $data['PAN'] = $this->input->post('PAN');
            $data['bank'] = $this->input->post('bank');
            $data['bank_branch'] = $this->input->post('bank_branch');
            $data['account_type'] = $this->input->post('account_type');
            $data['account_number'] = $this->input->post('account_number');
            $data['account_name'] = $this->input->post('account_name');
            $data['ifsc_code'] = $this->input->post('ifsc_code');
            $data['state_list'] = $this->input->post('state_list');
           
            // Update database record with new images and other data
            $AccountDetails = $this->Transportation_model->UpdateAccountID($data);
            $response['message'] = $AccountDetails;
            if ($AccountDetails) {
                $response['success'] = true;
                $response['message'] = "Record updated successfully.";
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to update record.";
            }
        } else {
            // Form not submitted properly
            $response['success'] = false;
            $response['message'] = "Error submitting form.";
        }
        // Return the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }



    public function SaveItemID()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["PAN_Card_image"]) && $_FILES["PAN_Card_image"]["error"] == 0) {
            if (!has_permission_new('TransportMaster', '', 'create')) {
                access_denied('TransportMaster');
            }
            // Define the directory where images will be uploaded
            $upload_dir = "uploads/transport/";

            // Ensure the upload directory exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Process and upload PAN card image
            $pan_filename = $_FILES["PAN_Card_image"]["name"];
            $pan_temp_name = $_FILES["PAN_Card_image"]["tmp_name"];
            $pan_target_path = $upload_dir . $pan_filename;

            if (move_uploaded_file($pan_temp_name, $pan_target_path)) {
                // PAN card image uploaded successfully
                $data['PAN_Card_image'] = $pan_target_path;

                // Loop through other images and upload them
                $image_fields = array('gst_certification_image', 'aadhaar_image', 'shop_act_image', 'transport_permit', 'cancel_cheque', 'ownership_photo', 'address_proof');
                foreach ($image_fields as $image_field) {
                    if (isset($_FILES[$image_field]) && $_FILES[$image_field]["error"] == 0) {
                        $image_filename = $_FILES[$image_field]["name"];
                        $image_temp_name = $_FILES[$image_field]["tmp_name"];
                        $image_target_path = $upload_dir . $image_filename;

                        if (move_uploaded_file($image_temp_name, $image_target_path)) {
                            // Image uploaded successfully
                            $data[$image_field] = $image_target_path;
                        } else {
                            // Failed to upload the image
                            echo "Failed to upload the $image_field.";
                            // Handle the error (return false, log error, etc.)
                        }
                    }
                }

                // Populate other form data into $data array
                // (Assuming these fields are not file uploads)
                $data['TransportName'] = $this->input->post('company');
                $data['UserID'] = $this->input->post('user_id');
                $data['TransportID'] = $this->input->post('AccountID');
                $data['state'] = $this->input->post('state');
                $data['city'] = $this->input->post('city');
                $data['address'] = $this->input->post('address');
                $data['PAN'] = $this->input->post('PAN');
                $data['bank'] = $this->input->post('bank');
                $data['bank_branch'] = $this->input->post('bank_branch');
                $data['account_type'] = $this->input->post('account_type');
                $data['account_number'] = $this->input->post('account_number');
                $data['account_name'] = $this->input->post('account_name');
                $data['ifsc_code'] = $this->input->post('ifsc_code');
                $data['state_list'] = $this->input->post('state_list');

                // Save $data to the database
                $AccountDetails = $this->Transportation_model->SaveAccountID($data);
                echo json_encode($AccountDetails);
            } else {
                // Failed to upload the PAN card image
                echo "Failed to upload the PAN card image.";
                // Handle the error (return false, log error, etc.)
            }
        } else {
            // Error uploading file or form not submitted properly
            echo "Error uploading file.";
        }
    }

}
