<?php

defined('BASEPATH') or exit('No direct script access allowed');

class News extends AdminController
{
   
    public function __construct()
    {
        parent::__construct();
         
        $this->load->model('news_model');
        $this->load->model('clients_model');
    }
    
    public function index()
    {
        if (!has_permission_new('news', '', 'view')) {
            access_denied('Invoice Items');
        }
        $data['allNews'] = $this->news_model->fetchAllNews();
        $this->load->view('admin/News/news',$data);
    }
    
    public function SaveNews()
    {
        if (!has_permission_new('news', '', 'create')) {
            access_denied('Invoice Items');
        }
        $newsUpdateID = $this->input->post('newsID');
        $newsTitle = $this->input->post('news_title');
        $newsDescription = $this->input->post('news_description');
        $newsCategory = $this->input->post('news_category');
        // $newsphoto = $this->input->post('news_image');
        $status = $this->input->post('status');
        $language = $this->input->post('language');
        $message = '';
        if($newsUpdateID == 0){
            if (!has_permission_new('news', '', 'create')) {
                access_denied('News');
            }
            //Add new news
            $InsertDetails = array(
                "title"=>$newsTitle,
                "category"=>$newsCategory,
                "description"=>$newsDescription,
                "status"=>$status,
                "language"=>$language,
                "UserID"=>$this->session->userdata('username'),
                "createddate"=>date('Y-m-d H:i:s')
            );
            $this->db->insert(db_prefix() . 'news', $InsertDetails);
            $message = 'News Created Successfully!!';
        }else{
            if (!has_permission_new('news', '', 'edit')) {
                access_denied('News');
            }
            //Update news
            $updateDetails = array(
                "title"=>$newsTitle,
                "category"=>$newsCategory,
                "description"=>$newsDescription,
                "status"=>$status,
                "language"=>$language,
                "UserID"=>$this->session->userdata('username')
            );
            $this->db->where(db_prefix() . 'news.id', $newsUpdateID);
            $this->db->update(db_prefix() . 'news', $updateDetails);
            $message = 'News Updated Successfully!!';
        }
        
        if($this->db->affected_rows()>0){
            if($newsUpdateID == 0){
                $newsID = $this->db->insert_id();    
            }else{
                $newsID = $newsUpdateID;
            }
            if ($_FILES['news_image']['name'] != '') {
                hooks()->do_action('before_upload_staff_profile_image');
                $path = get_upload_path_by_type('staff') . 'news/'.$newsID . '/';
                // Get the temp file path
                //echo $path;
                $tmpFilePath = $_FILES['news_image']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Getting file extension
                    $extension          = strtolower(pathinfo($_FILES['news_image']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = [
                        'jpg',
                        'jpeg',
                        'png',
                    ];
    
                    $allowed_extensions = hooks()->apply_filters('staff_profile_image_upload_allowed_extensions', $allowed_extensions);
    
                    if (!in_array($extension, $allowed_extensions)) {
                        set_alert('warning', _l('file_php_extension_blocked'));
                        return false;
                    }
                    _maybe_create_upload_path($path);
                    $filename    = unique_filename($path, $_FILES['news_image']['name']);
                    $newFilePath = $path . '/' . $filename;
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $CI                       = & get_instance();
                        $config                   = [];
                        $config['image_library']  = 'gd2';
                        $config['source_image']   = $newFilePath;
                        $config['new_image']      =  'thumb_'.$filename;
                        $config['maintain_ratio'] = true;
                        //$config['width']          = hooks()->apply_filters('staff_profile_image_thumb_width', 820);
                        //$config['height']         = hooks()->apply_filters('staff_profile_image_thumb_height', 820);
                        $CI->image_lib->initialize($config);
                        $CI->image_lib->resize();
                        $CI->image_lib->clear();
                        $config['image_library']  = 'gd2';
                        $config['source_image']   = $newFilePath;
                        $config['new_image']      =  $filename;
                        $config['maintain_ratio'] = true;
                        $config['width']          = hooks()->apply_filters('staff_profile_image_small_width', 150);
                        $config['height']         = hooks()->apply_filters('staff_profile_image_small_height', 150);
                        $CI->image_lib->initialize($config);
                        $CI->image_lib->resize();
                        $CI->db->where('id', $newsID);
                        $CI->db->update(db_prefix().'news', [
                            'newsphoto' => 'thumb_'.$filename,
                        ]);
                        // Remove original image
                        unlink($newFilePath);
                        $link = site_url().'uploads/staff_profile_images/news/'.$newsID.'/'.$filename;
                        
                    }
                }
                echo json_encode($message);
            }
            echo json_encode($message);
        }else{
            echo json_encode(false);
        }
    }
    
    public function fetchNewsDetails()
    {
       
        $newsID = $this->input->post('news_id');
        $news_data = $this->news_model->fetchNewsDetails($newsID);
        echo json_encode($news_data);
    }
    
}