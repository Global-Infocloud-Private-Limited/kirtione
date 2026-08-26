<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CategoryMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CategoryModel');         
    }

  //===================  Add Category & Edit Category ==================================

    public function AddEditCategory()
    {           
        if (!has_permission_new('ItemCategoryMaster', '', 'view')) {
            access_denied('Invoice Items');
        }
        $maxCatId = $this->CategoryModel->get_max_cat_id();
        $data['maxCatId'] = $maxCatId;

        $Categories = $this->CategoryModel->get_all_table_data($tablename="tblK1ItemCategory");
        $data['Categories'] = $Categories;
        $this->load->view('admin/CategoryMaster/AddEditCategory',$data);
    }

    public function insertSubCategory()
    {
        if (!has_permission_new('ItemCategoryMaster', '', 'create')) {
            access_denied('Invoice Items');
        }
        $SubcatName = $this->input->post('SubcatName');

        $insert_subcategory = array(           
            'SubcategoryName'=>$SubcatName,
            'UserID'=>$this->session->userdata('username'),
            'datecreated'=>date('Y-m-d h:i:s'),
            'UserID2'=>$this->session->userdata('username'),
            'dateupdatedat'=>date('Y-m-d h:i:s')                
        );
        $createnewsubactegory =  $this->CategoryModel->insert_data($tablename="tblK1ItemCategory",$insert_subcategory);
        if ($createnewsubactegory) {   
            $newCatId = $this->db->insert_id();            
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','newCatId' => $newCatId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }

    public function Category_table_data()
    {
        $Categories =  $this->CategoryModel->get_all_table_data($tablename="tblK1ItemCategory");        
        echo json_encode($Categories);
    }

    public function GetSubcategoryDetailsbyID()
    {
        $SubCatId = $this->input->post('SubCatId');
        $where = '(id="'.$SubCatId.'")'; 
        $Categorydetails = $this->CategoryModel->get_data($tablename="tblK1ItemCategory",$where);
        echo json_encode($Categorydetails);
    }

    public function UpdateCategoryDetails()
    {
        if (!has_permission_new('ItemCategoryMaster', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $SubcatId = $this->input->post('SubcatId');
        $SubcatName = $this->input->post('SubcatName');

        $update_category = array(
            'SubcategoryName'=>$SubcatName,
            'UserID2'=>$this->session->userdata('username'),
            'dateupdatedat'=>date('Y-m-d h:i:s')   
        );
        $where = '(id="'.$SubcatId.'")'; 
        $updateCategory = $this->CategoryModel->edit_data($tablename="tblK1ItemCategory",$where,$update_category);
        if($updateCategory)
        {
            $Categories =  $this->CategoryModel->get_all_table_data($tablename="tblsubcategory");      
            echo json_encode(['success' => true,'message' => 'Data updated successfully','Categories' => $Categories]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }
	
	//====================== Sub Categories Add && Edit ====================
	
	public function AddEditSubCategory()
    {           
        if (!has_permission_new('ItemSubCategoryMaster', '', 'view')) {
            access_denied('Invoice Items');
        }
        $maxSubCatId = $this->CategoryModel->get_max_Subcat_id();
        $data['maxSubCatId'] = $maxSubCatId;
		 $Categories = $this->CategoryModel->get_all_table_data($tablename="tblK1ItemCategory");
        $data['Categories'] = $Categories;
         $SubCategories = $this->CategoryModel->get_all_table_Subcatdata($tablename="tblK1ItemSubCategory");
         $data['SubCategories'] = $SubCategories;
        $this->load->view('admin/CategoryMaster/AddEditSubCategory',$data);
    }
	
	public function GetSubcateDetailsbyID()
    {
        $SubCatId = $this->input->post('SubCatId');
        $where = '(id="'.$SubCatId.'")'; 
        $SubCategorydetails = $this->CategoryModel->get_Subcatdata($tablename="tblK1ItemSubCategory",$where);
        echo json_encode($SubCategorydetails);
    }
	public function SubCategory_table_data()
    {
        $SubCategories =  $this->CategoryModel->get_all_table_Subcatdata($tablename="tblK1ItemSubCategory");        
        echo json_encode($SubCategories);
    }
	
	public function SubCategory_Save()
    {
        if (!has_permission_new('ItemSubCategoryMaster', '', 'create')) {
            access_denied('Invoice Items');
        }
		 $CategoryID = $this->input->post('CategoryID');
        $SubcatName = $this->input->post('SubcatName');

        $insertsubcategory = array(           
            'SubcategoryName'=>$SubcatName,
			'CategoryID'=>$CategoryID,
            'UserID'=>$this->session->userdata('username'),
            'TransDate'=>date('Y-m-d h:i:s')
                           
        );
        $createsubactegory =  $this->CategoryModel->insert_subcategory_data($tablename="tblK1ItemSubCategory",$insertsubcategory);
        if ($createsubactegory) {   
            $newCatId = $this->db->insert_id();            
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','newCatId' => $newCatId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }
    }
	
	public function UpdateSubCategoryDetails()
    {
        if (!has_permission_new('ItemSubCategoryMaster', '', 'edit')) {
            access_denied('Invoice Items');
        }
        $SubcatId = $this->input->post('SubcatId');
		$CategoryID = $this->input->post('CategoryID');
        $SubcatName = $this->input->post('SubcatName');

        $update_category = array(
			'SubcategoryName'=>$SubcatName,
			'CategoryID'=>$CategoryID,
            'UserID2'=>$this->session->userdata('username'),
            'Lupdate'=>date('Y-m-d h:i:s')
			
               
        );
        $where = '(id="'.$SubcatId.'")'; 
        $updateCategory = $this->CategoryModel->edit_SubCategotydata($tablename="tblK1ItemSubCategory",$where,$update_category);
        if($updateCategory)
        {
            $SubCategories =  $this->CategoryModel->get_all_table_data($tablename="tblK1ItemSubCategory");      
            echo json_encode(['success' => true,'message' => 'Data updated successfully','SubCategories' => $SubCategories]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }
	 public function GetCategory(){
        $data = $this->$this->CategoryModel->getCategory();
        $html = '<option value="">Non Selected</option>';
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'" >'.$value['SubcategoryName'].'</option>'; 
        }
        echo $html;
    }
	
	public function GetCategoryFromSubCategory(){
        $Category_ID = $this->input->post('Category_ID');
        $html = '';
        $data = $this->CategoryModel->getCategory($Category_ID);
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'" >'.$value['CategoryID'].'</option>'; 
        }
        echo $html;
    }
	
	
	
	
	
}