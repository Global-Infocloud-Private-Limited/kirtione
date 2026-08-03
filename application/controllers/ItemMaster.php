<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class ItemMaster extends ClientsController
{
    use ValidatesContact;
    public function __construct()
    {
        parent::__construct();

        hooks()->do_action('after_clients_area_init', $this);
        $this->load->model('taxes_model');
        $this->load->model('hsn_master_model');
        $this->load->model('ItemModel');
        $this->load->model('PurchaseModel');
        $this->load->helper('url', 'form');
    }
    
//========================== Item Wise Rate Add ================================
	public function AddItemWiseRate()
	{   
		$data['ItemSubCategory'] = $this->ItemModel->GetItemSubCategory();
		$data['CenterList'] = $this->ItemModel->GetActiveCenterList();
		$filter = array(
		    "PartyID"=> "KASPL",
		);
		$data['RateAvlCenterList'] = $this->ItemModel->GetRateAvailableCenter($filter);
		$data['title'] = "Add Item Wise Rate";
		$this->data($data);
        $this->view('ItemMaster/AddItemWiseRate');
        $this->layout();
		//$this->load->view('admin/ItemMaster/AddItemWiseRate',$data);
	}
//================== Get Item List By Item Sub Category ========================
	public function GetItemListByCategory()
	{
		$ItemSubCat = $this->input->post('ItemSubCat');
		$PartyID = $this->input->post('PartyID');
		$html = '';
		$data = $this->ItemModel->GetItemListByCategory($ItemSubCat,$PartyID);
		foreach($data as $key=>$value){
			$html .= '<option value="'.$value['ProductID'].'">'.$value['ProductName'].'</option>'; 
		}
		echo $html;
	}
//===================== Add New Rate ItemWise ==================================
	public function AddNewItemWiseRate()
	{
		
		$data = array(
		    "ItemID"=> $this->input->post('ItemID'),
		    "CenterID"=> $this->input->post('CenterID'),   
		    "new_salerate"=> $this->input->post('new_salerate'),   
		    "new_basicrate"=> $this->input->post('new_basicrate'),   
		    "new_discAmt"=> $this->input->post('new_discAmt'),   
		    "taxrate"=> $this->input->post('taxrate'),  
		    "PartyID"=> $this->input->post('PartyID'),
		    "UserID"=> $this->input->post('PartyID'),
		);
		$result =  $this->ItemModel->AddItemWiseSaleRate($data); 
		if($result){
		    echo json_encode(['success' => true,'message' => 'rate updated successfully','count' => $result]);
		}else{
		    echo json_encode(['success' => false,'message' => 'rate update error, please try again','count' => 0]);
		}
	}
	//=================  Get Item Details By ProductID =============================
    public function GetProductDetailsbyProductID()
	{
		$ItemID = $this->input->post('ItemID');
		$ProductDetails = $this->ItemModel->GetProductDetailsbyProductID($ItemID);
		echo json_encode($ProductDetails);
	}
//===================== Get Rate List ==========================================
	public function GetAllItemRateList()
	{	
		$data = array(
		    "ItemID"=> $this->input->post('ItemID'),
		    "PartyID"=> $this->input->post('PartyID'),
		    "CenterID"=> $this->input->post('CenterID'),   
		    "ItemSubCat"=> $this->input->post('ItemSubCat')
		);
		$Item_data =  $this->ItemModel->GetAllItemListBySubGroup($data); 
		$CenterList = $this->ItemModel->GetRateAvailableCenter($data);
		$Rate_data =  $this->ItemModel->GetAllItemRateList($data); 
		$html = "";
		$html .= '<table class="tree table table-striped table-bordered table-RateList tableFixHead2" id="RateList" width="100%">';
		if(!empty($Rate_data)){
		    $html .= "<thead>";
    		$html .= "<tr>";
    		$html .= "<th class='for-item-idth'>ItemID</th>";
    		$html .= "<th class='for-item-nameth'>Item Name</th>";
    		foreach($CenterList as $key=>$val){
    		    $html .= "<th colspan='3'>".$val["CenterName"]."</th>";
    		}
    		$html .= "</tr>";
    		
    		$html .= "<tr>";
    		$html .= "<th class='for-item-idth'></th>";
    		$html .= "<th class='for-item-nameth'></th>";
    		foreach($CenterList as $key=>$val){
    		    $html .= "<th>Sale Rate</th>";
    		    $html .= "<th>Basic Rate</th>";
    		    $html .= "<th>Disc Amt</th>";
    		}
    		$html .= "</tr>";
    		
    		$html .= "</thead>";
    		$html .= "<tbody>";
    		foreach($Item_data as $Ikey=>$Ival){
    		    $html .= "<tr>";
        		$html .= "<td class='for-item-idtd'>".$Ival["ProductID"]."</td>";
        		$html .= "<td class='for-item-nametd'>".$Ival["ProductName"]."</td>";
        		foreach($CenterList as $key=>$val){
        		    $SaleRate = "";
        		    $BasicRate = "";
        		    $DiscAmt = "";
        		    $css = "background-color: #ada6a0;";
        		    foreach($Rate_data as $Rkey=>$rval){
        		        if($rval["CenterID"] == $val["CenterID"] && $rval["ItemID"] == $Ival["ProductID"]){
        		            $SaleRate = $rval["sale_rate"];
        		            $BasicRate = $rval["basic_rate"];
        		            $DiscAmt = $rval["disc_amt"];
        		            $css = "";
        		        }
        		    }
        		    $html .= "<td style='".$css."text-align:center;'>".$SaleRate."</td>";
        		    $html .= "<td style='".$css."text-align:center;'>".$BasicRate."</td>";
        		    $html .= "<td style='".$css."text-align:center;'>".$DiscAmt."</td>";
        		}
        		$html .= "<tr>";
    		}
    		
    		$html .= "</tbody>";
		}else{
		    $html .= "<tr>";
		    $html .= "<td colspan='2'>Rate not found</td>";
		    $html .= "<tr>";
		}
		
		$html .= "</table>";
		echo $html;
		//print_r($Rate_data);
	}
    public function index()
    {
        $Brands =  $this->ItemModel->get_all_table_data($tablename="tblbrands");        
        $data['Brands'] = $Brands;

        $data['hsn'] = $this->hsn_master_model->get();
        $data['taxes'] = $this->taxes_model->get();

        $Subcategory = $this->ItemModel->get_all_table_data($tablename="tblK1ItemCategory");   
        $data['Categories'] = $Subcategory;
        $data['NextNumber'] = $this->ItemModel->GetNextItemID();
        $data['title']            = "Item Master";
        $this->data($data);
        $this->view('ItemMaster/AddEditItem');
        $this->layout();
    }
//========================== Get Kirti One Item List ===========================
    public function GetItemList()
    {
        $LoginAccountID = $this->input->post('LoginID');
		$isactive = $this->input->post('status');
		
        $products =  $this->ItemModel->GetItemList($LoginAccountID, $isactive);
        echo json_encode($products);
    }
//============================ Add Kirti One New Item ==========================
    public function AddItem()
    {
        /*if (!has_permission_new('ItemMaster', '', 'create')) {
            access_denied('Invoice Items');
        }*/
        $NextNumber = $this->ItemModel->GetNextItemID();
        $selected_company = 1;
        $nextproductnumber = $NextNumber->value;   
	    $LoginAccountID = $this->input->post('ItemFor');
        $item_image = $_FILES['item_image'];        
      
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) 
        {
            $item_image = $_FILES['item_image'];    
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($item_image['type'], $allowed_types)) 
            {               
                $target_directory = './uploads/productimage/';
                $target_file = $target_directory . basename($item_image['name']);  
               
                if (!file_exists($target_file)) 
                {                   
                    if (move_uploaded_file($item_image['tmp_name'], $target_file)) {                        
                    } 
                } 
            } 
        } else {           
            $target_file = '';
        }      
        $file_name = basename($target_file);

        $insert_product = array(    
            'PlantID'=>$selected_company,
            'ProductID'=>$nextproductnumber,   
            'ItemFor'=>$this->input->post('ItemFor'),
            'ProductName'=>$this->input->post('ProductName'),   
            'Category'=>$this->input->post('Category'), 
            'Subcategory'=>$this->input->post('Subcategory'),			
            'BrandId'=>$this->input->post('Brandname'),
            'rate'=>$this->input->post('BasicRate'),
			'SaleRate'=>$this->input->post('SaleRate'),
            'gst'=>$this->input->post('Gst'),
            'unit'=>$this->input->post('unit'),
            'hsn_code'=>$this->input->post('HsnCode'),          
            'PackingQty'=>$this->input->post('Quantity'),
            'PackingWeight'=>$this->input->post('Weight'),
            'MonitorStock'=>$this->input->post('MonitorStock'),
            'ProductDescription	'=>$this->input->post('description'),
            'Productimg'=>$file_name,
            'isactive'=>"N",
            'UserId'=>$this->input->post('ItemFor'),
            'TransDate'=>date('Y-m-d h:i:s'), 
        );
		$ItemID = $this->input->post('Itemid');
		
		
        $createnewproduct =  $this->ItemModel->insert_data($tablename="tblproduct",$insert_product);      
        if ($createnewproduct) 
        {   
			$insert_multivandor_product = array(
				'ItemID' => $ItemID,
				'VendorID' => $LoginAccountID,
				'UserID' => $LoginAccountID,
				'TransDate'=>date('Y-m-d h:i:s')
			);
			
			$createnew_multivandor_product = $this->ItemModel->insert_multivandor_data($tablename="tblk1ItemVendor",$insert_multivandor_product);
            $this->increment_next_number('next_product_id');                        
            echo json_encode(['success' => true,'message' => 'Data inserted successfully','nextproductnumber' => $nextproductnumber]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
        }  
    }
    
    public function UpdateProductDetails()
    {
        /*if (!has_permission_new('ItemMaster', '', 'edit')) {
            access_denied('Invoice Items');
        }*/
        $NextNumber = $this->ItemModel->GetNextItemID();
        $selected_company = 1;
        $nextproductnumber = $NextNumber->value;         
        $Id = $this->input->post('Id');
        $ProductName = $this->input->post('ProductName');
        $ItemFor = $this->input->post('ItemFor');
        $Category = $this->input->post('Category'); 
		$Subcategory = $this->input->post('Subcategory');
		$Brandname = $this->input->post('Brandname');
		$BasicRate = $this->input->post('BasicRate');
		$SaleRate = $this->input->post('SaleRate');
        $Gst = $this->input->post('Gst');       
        $unit = $this->input->post('unit');
        $HsnCode = $this->input->post('HsnCode');
        $Quantity = $this->input->post('Quantity');
        $Weight = $this->input->post('Weight');
        $MonitorStock = $this->input->post('MonitorStock');
        $isactive = "N";
        $description = $this->input->post('description');   

        $item_image = $_FILES['item_image']; 
        
        $wh = '(id="'.$Id.'")'; 
        $imageexist = $this->ItemModel->get_data($tablename="tblproduct",$wh);        
      
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) 
        {         
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($item_image['type'], $allowed_types)) 
            {               
                $target_directory = './uploads/productimage/';
                $target_file = $target_directory . basename($item_image['name']);  
               
                if (!file_exists($target_file)) 
                {                   
                    if (move_uploaded_file($item_image['tmp_name'], $target_file)) {                        
                    } 
                } 
            } 
        } else { 
            if ($imageexist && isset($imageexist['Productimg']) && !empty($imageexist['Productimg'])) {
                $target_file = $imageexist['Productimg']; 
            } else {
                $target_file = ''; 
            }       
        }    
        $file_name = basename($target_file);
        $update_Details = array(
            'ProductName'=>$ProductName,
            'ItemFor'=>$ItemFor,
            'Category'=>$Category,
            'Subcategory'=>$Subcategory,  
            'BrandId'=>$Brandname,
            'rate'=>$BasicRate,
			'SaleRate'=>$SaleRate,
            'gst'=>$Gst,
            'unit'=>$unit,
            'hsn_code'=>$HsnCode,           
            'PackingQty'=>$Quantity,
            'PackingWeight'=>$Weight,
            'MonitorStock'=>$MonitorStock,
            'ProductDescription	'=>$description,
            'Productimg'=>$file_name,
            'isactive'=>$isactive,           
            'UserID2'=>$ItemFor,
            'Lupdate'=>date('Y-m-d h:i:s')
        );
        $where = '(id="'.$Id.'")'; 
        $updateProduct  = $this->ItemModel->edit_data($tablename="tblproduct",$where,$update_Details);
        if($updateProduct)
        {    
            echo json_encode(['success' => true,'message' => 'Data updated successfully','productnumber' => $nextproductnumber]);
        }
        else
        {
            echo json_encode(['success' => false, 'message' => 'Failed to update brand']);
        }
    }
    
    public function GetProductDetailsbyID()
    {
        $Id = $this->input->post('Id');
        $where = '(id="'.$Id.'")'; 
        $ProductDetails = $this->ItemModel->get_data($tablename="tblproduct",$where);
        echo json_encode($ProductDetails);
    }
    
    public function increment_next_number($name)
    {            
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('name', $name);
        $this->db->update(db_prefix() . 'options');
    }
	
	public function GetCategoryFromSubCategory()
	{
        $category_id = $this->input->post('category_id');
        $html = '';
        $data = $this->ItemModel->GetCategoryFromSubCategoryCode($category_id);
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'">'.$value['SubCategoryName'].'</option>'; 
        }
        echo $html;
    }
	public function GetCategoryBySubCategory(){ 
        $category_id = $this->input->post('category_id');
        $html = '';
        $data = $this->ItemModel->getCategoryBySubCategoryCode($category_id);
        foreach($data as $key=>$value){
            $html .= '<option value="'.$value['id'].'" >'.$value['SubCategoryName'].'</option>'; 
        }
        echo $html;
    }
}