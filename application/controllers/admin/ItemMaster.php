<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class ItemMaster extends AdminController

	{

		private $not_importable_fields = ['id'];

		public function __construct()

		{

			parent::__construct();

			$this->load->model('ItemModel');    

			$this->load->model('hsn_master_model');

			$this->load->model('taxes_model');

			$this->load->model('PurchaseModel');  

		}

		//========================== Item Wise Rate Add ================================

		public function AddItemWiseRate()

		{   

			if (!has_permission_new('ItemWiseRateMaster', '', 'view')) {

				access_denied('Invoice Items');

			}

			$data['ItemSubCategory'] = $this->ItemModel->GetItemSubCategory();

			$data['CenterList'] = $this->PurchaseModel->GetAllAssignedCenterList();

			$filter = array(

		    "PartyID"=> "KASPL",

			);

			$data['RateAvlCenterList'] = $this->ItemModel->GetRateAvailableCenter($filter);

			$data['title'] = "Add Item Wise Rate";

			$this->load->view('admin/ItemMaster/AddItemWiseRate',$data);

		}

		//================== Get Item List By Item Sub Category ========================

		public function GetItemListByCategory()

		{

			$ItemSubCat = $this->input->post('ItemSubCat');

			$PartyID = "KASPL";

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

			if (!has_permission_new('ItemWiseRateMaster', '', 'create')) {

				access_denied('Invoice Items');

			}

			$data = array(

		    "ItemID"=> $this->input->post('ItemID'),

		    "CenterID"=> $this->input->post('CenterID'),   

		    "new_salerate"=> $this->input->post('new_salerate'),   

		    "new_basicrate"=> $this->input->post('new_basicrate'),   

		    "new_discAmt"=> $this->input->post('new_discAmt'),   

		    "taxrate"=> $this->input->post('taxrate'),  

		    "PartyID"=> "KASPL",  

		    "UserID"=> $this->session->userdata('username'),  

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

			if (!has_permission_new('ItemWiseRateMaster', '', 'view')) {

				access_denied('Invoice Items');

			}

			$data = array(

		    "PartyID"=> "KASPL",

		    "ItemID"=> $this->input->post('ItemID'),

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

		

		public function AddEditProduct()

		{   

			if (!has_permission_new('ItemMaster', '', 'view')) {

				access_denied('Invoice Items');

			}

			$Brands =  $this->ItemModel->get_all_table_data($tablename="tblbrands");        

			$data['Brands'] = $Brands;

			

			$data['hsn'] = $this->hsn_master_model->get();

			$data['taxes'] = $this->taxes_model->get();

			

			$Subcategory = $this->ItemModel->get_all_table_data($tablename="tblK1ItemCategory");   

            $data['Categories'] = $Subcategory;

			

			$data['company_detail'] = $this->ItemModel->get_company_detail();

			$data['KirtiOneAccessList'] = $this->ItemModel->GetKirtiOneAccessList();

			$data['NextNumber'] = $this->ItemModel->GetNextItemID();

			$Products = $this->ItemModel->get_all_table_data($tablename="tblproduct");   

			foreach($Products as &$pro)

			{

				$where =  '(id="'.$pro['Subcategory'].'")'; 

				$subcategory_details = $this->ItemModel->get_data($tablename="tblsubcategory",$where);   

				$pro['subcatname'] = $subcategory_details['SubcategoryName'];

				

				$wh =  '(id="'.$pro['BrandId'].'")'; 

				$brand_details = $this->ItemModel->get_data($tablename="tblbrands",$wh);   

				$pro['brandname'] = $brand_details['BrandName'];	    

				

				$wh_gst = '(id="'.$pro['gst'].'")';             

				$taxes = $this->ItemModel->get_data($tablename="tbltaxes",$wh_gst);            

				$pro['gstrate'] = $taxes['taxrate'];

			}

			$data['Products'] = $Products;

			$this->load->view('admin/ItemMaster/AddEditProduct',$data);

		}

		

		public function increment_next_number($name)

		{            

			$this->db->set('value', 'value+1', false);

			$this->db->WHERE('name', $name);

			$this->db->update(db_prefix() . 'options');

		}

		//============================ Add Kirti One New Item ==========================

		public function AddItem()

		{

			if (!has_permission_new('ItemMaster', '', 'create')) {

				access_denied('Invoice Items');

			}

			$selected_company = $this->session->userdata('root_company');

			$NextNumber = $this->ItemModel->GetNextItemID();

	        $nextproductnumber = $NextNumber->value;    

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

			'minimum_order_qty'=>$this->input->post('MOLevel'),

			'minimum_stock_qty'=>$this->input->post('MSQty'),

            'ProductDescription	'=>$this->input->post('description'),

            'PurchaseReturnDay' => $this->input->post('PurchaseReturnDay'),

            'Productimg'=>$file_name,

            'isactive'=>$this->input->post('isactive'),

            'UserId'=>$this->session->userdata('username'),

            'TransDate'=>date('Y-m-d h:i:s'), 

            'UserID2'=>$this->session->userdata('username'),

            'Lupdate'=>date('Y-m-d h:i:s')

			);

			

			$createnewproduct =  $this->ItemModel->insert_data($tablename="tblproduct",$insert_product);

			

			if ($createnewproduct) 

			{   

				$VendorList = $this->input->post('VendorFor');

				$VendorListArray = explode(",",$VendorList);

				foreach($VendorListArray as $val){

					$insert_multivandor_product = array(

					'ItemID' => $nextproductnumber,

					'VendorID' => $val,

					'UserID' => $this->session->userdata('username'),

					'TransDate'=>date('Y-m-d h:i:s')

					);

					$createnew_multivandor_product = $this->ItemModel->insert_multivandor_data($tablename="tblk1ItemVendor",$insert_multivandor_product);

				}

				$this->increment_next_number('next_product_id');                        

				echo json_encode(['success' => true,'message' => 'Data inserted successfully','nextproductnumber' => $nextproductnumber]);

				} else {

				echo json_encode(['success' => false, 'message' => 'Failed to insert card']);

			}  

		}

		

		public function GetProductDetailsbyID()

		{

			$Id = $this->input->post('Id');

			$ProductDetails = $this->ItemModel->get_data_ItemMaster($Id);

			echo json_encode($ProductDetails);

		}

		

		public function UpdateProductDetails()

		{

			if (!has_permission_new('ItemMaster', '', 'edit')) {

				access_denied('Invoice Items');

			}

			

	        

			$NextNumber = $this->ItemModel->GetNextItemID();

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

			$minimum_order_qty = $this->input->post('MOLevel');

			$minimum_stock_qty = $this->input->post('MSQty');

			$isactive = $this->input->post('isactive');

			$description = $this->input->post('description');

			$PurchaseReturnDay = $this->input->post('PurchaseReturnDay');

			

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

			'minimum_order_qty'=>$minimum_order_qty,

			'minimum_stock_qty'=>$minimum_stock_qty,

			'PurchaseReturnDay' => $PurchaseReturnDay,

            'isactive'=>$isactive,           

            'UserID2'=>$this->session->userdata('username'),

            'Lupdate'=>date('Y-m-d h:i:s')

			);

			$where = '(id="'.$Id.'")'; 

			$updateProduct  = $this->ItemModel->edit_data($tablename="tblproduct",$where,$update_Details);

			

			$ItemID = $this->input->post('ItemID');

			

            

			

			if($updateProduct)

			{     

				$this->db->where('ItemID', $ItemID);

				$this->db->delete('tblk1ItemVendor');

				

				$VendorList = $this->input->post('VendorFor');

				$VendorListArray = explode(",",$VendorList);

				foreach($VendorListArray as $val){

					$insert_multivandor_product = array(

					'ItemID' => $ItemID,

					'VendorID' => $val,

					'UserID' => $this->session->userdata('username'),

					'TransDate'=>date('Y-m-d h:i:s')

					);

					$createnew_multivandor_product = $this->ItemModel->insert_multivandor_data($tablename="tblk1ItemVendor",$insert_multivandor_product);

				}

				echo json_encode(['success' => true,'message' => 'Data updated successfully','productnumber' => $nextproductnumber]);

			}

			else

			{

				echo json_encode(['success' => false, 'message' => 'Failed to update brand']);

			}

		}

		//========================== Get Kirti One Item List ===========================

		public function GetItemList()

		{

			$products =  $this->ItemModel->GetItemList();

			echo json_encode($products);

		}

		

		public function GetItemListadminproduct()

		{

			

            $isactive = $this->input->post('status');

            $ItemFor = $this->input->post('searchItemFor');

			

			$products =  $this->ItemModel->GetItemListadminproduct($isactive, $ItemFor);

			log_message('debug', print_r($products, true));

			echo json_encode($products);

		}

		//================== Sale Order List Page Load =================================

		public function ItemOrderDetails()

		{

			if (!has_permission_new('OrderList', '', 'view')) {

				access_denied('Invoice Items');

			}

			$data['title'] = "Sale Order List";

			$data['company_detail'] = $this->ItemModel->get_company_detail();

			$paymentmethods = $this->ItemModel->get_all_table_data($tablename="tblpaymentmethod");

			$data['paymentmethods'] = $paymentmethods;

			$data['clients'] = $this->ItemModel->GetOrderPartyList();

			$data['centermaster'] = $this->ItemModel->GetOrderPunchCenterList(); 

			$data['products'] = $this->ItemModel->GetOrderPunchItemList();

			$this->load->view('admin/ItemMaster/ItemOrderDetails',$data);

		}

		//==================== Get Category Type wise Item List ========================	

		public function GetCategoryWiseItemList()

		{	

			$CategoryType = $this->input->post('CategoryType');

			$products =  $this->ItemModel->GetCategoryWiseItemList($CategoryType);

			//log_message('debug', print_r($products, true));

			echo json_encode($products);

		}

//========================== Get Kirti One Order List ==========================

	public function GetFilterDataOrderDetails()
	{
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'order_status'=> $this->input->post('order_status'),
			'AccountID'=>$this->input->post('AccountID'),
			'CenterID'=>$this->input->post('CenterID'),
			'ItemID'=>$this->input->post('ItemID'),
			'Report_type'=>$this->input->post('Report_type'),
			'CategoryType'=>$this->input->post('CategoryType'),
			'SaleType'=>$this->input->post('SaleType'),
			'OrderType'=>$this->input->post('OrderType')
		);

		$result = $this->ItemModel->getItemOrderDetailsDB($data); 
		/*echo "<pre>";
		print_r($result);
		die;*/
		
		$html = '';
		$html .= '<thead>';
		if($this->input->post('Report_type') =="1"){
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">OrderID</th>';
			$html .= '<th style="text-align:left;">Order Date</th>';  
			$html .= '<th style="text-align:left;">InvoiceNo</th>';
			$html .= '<th style="text-align:left;">Invoice Date</th>';
			$html .= '<th style="text-align:left;">Center Name</th> '; 
			$html .= '<th style="text-align:left;">Center GSTIN</th> '; 
			$html .= '<th style="text-align:left;">Party Name</th> ';
			$html .= '<th style="text-align:left;">GSTIN</th> ';
			$html .= '<th style="text-align:left;">Bill No</th> ';
			$html .= '<th style="text-align:left;">Cash Amt</th> ';
			$html .= '<th style="text-align:left;">Online Amt</th> ';
			$html .= '<th style="text-align:left;">Order Amt</th> ';
			$html .= '<th style="text-align:left;">Other Amt</th> ';
			$html .= '<th style="text-align:left;">Disc Amt</th> ';
			$html .= '<th style="text-align:left;">Taxable Amt</th>';
			$html .= '<th style="text-align:left;">CGST Amt</th>';
			$html .= '<th style="text-align:left;">SGST Amt</th>';
			$html .= '<th style="text-align:left;">IGST Amt</th>';
			$html .= '<th style="text-align:left;">Net Amt</th>';                                           
			$html .= '<th style="text-align:left;">Order Status</th>';  
			$html .= '<th style="text-align:left;">Order Type</th>';  
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody id="filter_data_table">';
			$data["Report_type"] = '2';
			$data["OrderIDs"] = array_column($result, 'OrderID');
			$ItemData = $this->ItemModel->getItemOrderDetailsDB($data);
			 //echo "<pre>";print_r($ItemData);die;
		}else{
			$html .= '<tr>';
			$html .= '<th style="text-align:left;">Sr No.</th>';
			$html .= '<th style="text-align:left;">OrderID</th>';
			$html .= '<th style="text-align:left;">Order Date</th>'; 
			$html .= '<th style="text-align:left;">InvoiceNo</th>';
			$html .= '<th style="text-align:left;">Invoice Date</th>';
			$html .= '<th style="text-align:left;">Center Name</th>'; 
			$html .= '<th style="text-align:left;">Center GSTIN</th> '; 
			$html .= '<th style="text-align:left;">Party Name</th>'; 
			$html .= '<th style="text-align:left;">GSTIN</th> ';
			$html .= '<th style="text-align:left;">Bill No</th> ';
			$html .= '<th style="text-align:left;">Item Name</th>'; 
			$html .= '<th style="text-align:left;">HSN Code</th>'; 
			$html .= '<th style="text-align:left;">GST%</th>'; 
			$html .= '<th style="text-align:left;">Unit</th>'; 
			$html .= '<th style="text-align:left;">Quantity</th>'; 
			$html .= '<th style="text-align:left;">Item Amt</th>'; 
			$html .= '<th style="text-align:left;">Disc Amt</th>'; 
			$html .= '<th style="text-align:left;">Taxable Amt</th>'; 
			$html .= '<th style="text-align:left;">CGST Amt</th>'; 
			$html .= '<th style="text-align:left;">SGST Amt</th>'; 
			$html .= '<th style="text-align:left;">IGST Amt</th>';                      
			$html .= '<th style="text-align:left;">Net Amt</th>';   
			$html .= '<th style="text-align:left;">Order Status</th>'; 
			$html .= '<th style="text-align:left;">Order Type</th>';  
			$html .= '</tr>';
		}

		$totalQtySum = 0;$TotalItemAmt = 0;$TotalDiscAmt = 0;$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;
		$CashTotal = 0;$OnlineTotal = 0;$TotalOtherAmt = 0;
		$processed = [];
		foreach($result as $key=>$value)
		{
			if($value["IsDirectSale"] == "Y"){
				$redirectUrl = admin_url('KirtiOneOrder/AddEditSaleOrder/').$value["OrderID"];
			}else{
				$redirectUrl = admin_url('KirtiOneOrder/AddEditDeliveryInvoice/').$value["ChallanID"];
			}

			if ($value['OrderStatus'] == "O") {
				$OrderStat = "Pending";
			} elseif ($value['OrderStatus'] == "F") {
				$OrderStat = "Completed";
			} elseif ($value['OrderStatus'] == "C") {
				$OrderStat = "Cancelled";
			}

			$OrderType = "";
			if($value['OrderPaymentType'] == "1"){
			    $OrderType = "Cash Order";
			}else if($value['OrderPaymentType'] == "2"){
			    $OrderType = "Credit Order";
			}

			$BillNo = $value['BIllNo'];
			if($value['BIllNo'] == "" || $value['BIllNo'] == NULL){
			    $BillNo = $value['PartyBillNo'];
			}

			if($this->input->post('Report_type') == "1"){
				if(isset($processed[$value['OrderID']])){
        	continue;
				}
				$processed[$value['OrderID']] = true;

				$OrdItemTotal = 0;
				$OrdItemDiscAmt = 0;
				$OrdTaxableAmt = 0;
				$OrdCGSTAmt = 0;
				$OrdSGSTAmt = 0;
				$OrdIGSTAmt = 0;
				$OrdNetTotal = 0;
				$PartyGST = "";
				$TransID = $value["InvoiceNo"];
				foreach($ItemData as $key1=>$val2){
					if($value["OrderID"] == $val2["OrderID"]){
						$PartyGST = $val2["PartyGST"];
						$TransID = $val2["TransID"];
						$TaxableAmt = 0;
						$CGSTAmt = 0;
						$SGSTAmt = 0;
						$IGSTAmt = 0;
						$GSTAmt = 0;
						$NetAmt = 0;
						$OrderAmt = $val2["ItemTotalAmt"] - $val2["DiscAmt"];
						$GSTPer = $val2['taxrate'];
						$ExGSTAmt = $val2['sgstamt'] + $val2['cgstamt'] + $val2['igstamt'];
						
						if($ExGSTAmt > 0){
								$TaxableAmt = $OrderAmt;
								$GSTAmt = $ExGSTAmt;
						}else{
								$TaxableAmt = $OrderAmt / (1+($GSTPer/100));
								$GSTAmt = $OrderAmt - $TaxableAmt;
						}

						if($val2['state'] == $val2['CenterState'] || $val2['state'] == ""){
								$CGSTAmt = $GSTAmt / 2;
								$SGSTAmt = $GSTAmt / 2;
						}else{
								$IGSTAmt = $GSTAmt;
						}
						$OrdItemTotal += $val2["OrderAmt"];
						$OrdItemDiscAmt += $val2["DiscAmt"];
						$OrdCGSTAmt += $CGSTAmt;
						$OrdSGSTAmt += $SGSTAmt;
						$OrdIGSTAmt += $IGSTAmt;
						$GSTAmt = $CGSTAmt + $SGSTAmt + $IGSTAmt;
						$OrdTaxableAmt += $TaxableAmt;
						$NetAmt = $TaxableAmt + $GSTAmt;
						$OrdNetTotal += $NetAmt;
					}
				}
				$CashTotal += $value['CashAmt'];
				$OnlineTotal += $value['OnlineAmt'];
				$TotalOtherAmt += $value['OtherAmt'];

				if(($value['CashAmt'] + $value['OnlineAmt']) != $NetAmt){
				    //$Css = "color:red";
				}else{
				    $Css = "";
				}
				
				$html .= '<tr style="'.$Css.'" onclick="window.open(\''.$redirectUrl.'\', \'_blank\');">';           
				$html .= '<td>'.($key+1).'</td>';   
				$html .= '<td>'.$value["OrderID"].'</td>';	
				$html .= '<td>'._d(substr($value["Transdate"],0,10)).'</td>'; 
				$html .= '<td>'.$TransID.'</td>';	
				$html .= '<td>'._d(substr($value["InvoiceDate"],0,10)).'</td>'; 
				$html .= '<td>'.$value['CenterName'].'</td>'; 
				$html .= '<td>'.$value['GSTNo'].'</td>'; 
				$html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
				$html .= '<td>'.$PartyGST.'</td>'; 
				$html .= '<td>'.$BillNo.'</td>'; 
				$html .= '<td style="text-align:right;">' . number_format($value['CashAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['OnlineAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdNetTotal , 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['OtherAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdItemDiscAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdTaxableAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdCGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdSGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($OrdIGSTAmt, 2, '.', '') . '</td>';
				// $OrdNetTotal += $value['OtherAmt'];
				$FinalNetAmt = $OrdNetTotal + $value['OtherAmt'];
				$html .= '<td style="text-align:right;">' . number_format($FinalNetAmt, 2, '.', '') . '</td>'; 	    
				$html .= '<td>'.$OrderStat.'</td>';	       
				$html .= '<td>'.$OrderType.'</td>';	 
				$html .= '</tr>'; 

				$TotalItemAmt += $OrdItemTotal;
				$TotalDiscAmt += $OrdItemDiscAmt;
				$TotalTaxableAmt += $OrdTaxableAmt;
				$TotalCGSTAmt += $OrdCGSTAmt;
				$TotalSGSTAmt += $OrdSGSTAmt;
				$TotalIGSTAmt += $OrdIGSTAmt;
				$TotalNetAmt += $FinalNetAmt;
			}else{
				$OrdItemTotal = 0;
				$OrdItemDiscAmt = 0;
				$OrdTaxableAmt = 0;
				$OrdCGSTAmt = 0;
				$OrdSGSTAmt = 0;
				$OrdIGSTAmt = 0;
				$OrdNetTotal = 0;
				$PartyGST = $value["PartyGST"];
				//$GSTPer = $value['cgst'] + $value['sgst'] + $value['igst'];
				$GSTPer = $value['taxrate'];
				$html .= '<tr onclick="window.open(\''.$redirectUrl.'\', \'_blank\');">';           
				$html .= '<td>'.($key+1).'</td>';   
				$html .= '<td>'.$value["OrderID"].'</td>';	
				$html .= '<td>'._d(substr($value["TransDate"],0,10)).'</td>'; 
				$html .= '<td>'.$value["TransID"].'</td>';	
				$html .= '<td>'._d(substr($value["InvoiceDate"],0,10)).'</td>'; 
				$html .= '<td>'.$value['CenterName'].'</td>';  
				$html .= '<td>'.$value['GSTNo'].'</td>'; 
				$html .= '<td>' . $value['company'] . ' (' . $value["AccountID"] . ')</td>';
				$html .= '<td>'.$PartyGST.'</td>'; 
				$html .= '<td>'.$BillNo.'</td>'; 
				$html .= '<td>'.$value['ProductName'].'</td>';
				$html .= '<td>'.$value['hsn_code'].'</td>';
				$html .= '<td>'.number_format($GSTPer, 2, '.', '').'</td>';
				$Unit = "";
				if($value['PackingQty'] == $value['CaseQty']){
					$Unit = $value['unit'];
				}else{
					$Unit = "Pcs";
				}
				$html .= '<td>'.$Unit.'</td>'; 
				$TaxableAmt = 0;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				$GSTAmt = 0;
				$NetAmt = 0;
				$OrderAmt = $value["ItemTotalAmt"] - $value["DiscAmt"];
				$ExGSTAmt = $value['sgstamt'] + $value['cgstamt'] + $value['igstamt'];

				if($ExGSTAmt > 0){
				    $TaxableAmt = $OrderAmt;
				    $GSTAmt = $ExGSTAmt;
				}else{
				    $TaxableAmt = $OrderAmt / (1+($GSTPer/100));
				    $GSTAmt = $OrderAmt - $TaxableAmt;
				}

				if($value['state'] == $value['CenterState'] || $value['state'] == ""){
				    $CGSTAmt = $GSTAmt / 2;
				    $SGSTAmt = $GSTAmt / 2;
				}else{
				    $IGSTAmt = $GSTAmt;
				}

				$OrdItemTotal = $value["OrderAmt"];
				$OrdItemDiscAmt = $value["DiscAmt"];
				$OrdCGSTAmt = $CGSTAmt;
				$OrdSGSTAmt = $SGSTAmt;
				$OrdIGSTAmt = $IGSTAmt;
				$GSTAmt = $CGSTAmt + $SGSTAmt + $IGSTAmt;
				$OrdTaxableAmt = $TaxableAmt;
				$NetAmt = $OrdTaxableAmt + $GSTAmt;
				$totalQtySum += $value['OrderQty'];
				$html .= '<td style="text-align:right;">' . number_format($value['OrderQty'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($NetAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($value['DiscAmt'], 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($TaxableAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($CGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($SGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($IGSTAmt, 2, '.', '') . '</td>';
				$html .= '<td style="text-align:right;">' . number_format($NetAmt, 2, '.', '') . '</td>';
				$html .= '<td>'.$OrderStat.'</td>';      
				$html .= '<td>'.$OrderType.'</td>';	 
				$html .= '</tr>';
				
				$TotalItemAmt += $OrdItemTotal;
				$TotalDiscAmt += $OrdItemDiscAmt;
				$TotalTaxableAmt += $OrdTaxableAmt;
				$TotalCGSTAmt += $OrdCGSTAmt;
				$TotalSGSTAmt += $OrdSGSTAmt;
				$TotalIGSTAmt += $OrdIGSTAmt;
				$TotalNetAmt += $NetAmt;
			}
		}

		if($this->input->post('Report_type') == "1"){
			$html .= '<tr>';
			$html .= '<td colspan="10" style="text-align:right;"><strong>Total</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($CashTotal, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($OnlineTotal, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalItemAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalOtherAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalDiscAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalTaxableAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalCGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalSGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalIGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalNetAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td colspan="2"></td>'; 
			$html .= '</tr>';
		}else{
			$html .= '<tr>';
			$html .= '<td colspan="14" style="text-align:right;"><strong>Total</strong></td>';        
			$html .= '<td style="text-align:right;"><strong>' . number_format($totalQtySum, 2, '.', '') . '</td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalItemAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalDiscAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalTaxableAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalCGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalSGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalIGSTAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td style="text-align:right;"><strong>' . number_format($TotalNetAmt, 2, '.', '') . '</strong></td>';
			$html .= '<td colspan="2"></td>'; 
			$html .= '</tr>';
		}
		$html .= '</body>';
		echo $html;
	}

		

		//============= Export Kirti One Order List ====================================

		public function export_ItemOrderlist()

		{

			if (!has_permission_new('OrderList', '', 'export')) {

				access_denied('Invoice Items');

			}

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) 

			{

				$company_detail = $this->ItemModel->get_company_detail();

				

				$post_data = $this->input->post();

				

				$from_date = $post_data['from_date'];

				$to_date = $post_data['to_date'];

				$center_name = $post_data['Centertext'];

				$report_type =  $post_data['ReportTypetext'];

				$item = $post_data['ItemName'];

				$status = $post_data['order_statusText'];

				$AccountName = $post_data['Partyname']; 

				$Report_type =  $post_data['Report_type'];

				$CategoryType =  $post_data['CategoryType'];

				$SaleType =  $post_data['SaleType'];

				

				$result = $this->ItemModel->getItemOrderDetailsDB($post_data);

				

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  

				

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				

				$center_addr = array($address, );	  

				

				$filters = "From date: " . $from_date . ", To date: " . $to_date . 

				", Center: " . $center_name . ", Report Type: " . $report_type .

				", Item: " . $item . ", Party: " . $AccountName . ", Order Status: " . $status;

				

				$filter_row = array($filters);

				

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 11);  //merge cells

				

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 14);  //merge cells	   

				

				$writer->writeSheetRow('Sheet1', $filter_row);

				

				$set_col_tk = [];

				$post_data2 = $post_data;

				if ($post_data['Report_type'] == "1") {    

					$post_data2["Report_type"] = '2';            

					$ItemData = $this->ItemModel->getItemOrderDetailsDB($post_data2);

					$set_col_tk["OrderID"] = 'OrderID';

					$set_col_tk["Transdate"] = 'Order Date';

					$set_col_tk["InvoiceNo"] = 'InvoiceNo';

					$set_col_tk["InvoiceDate"] = 'Invoice Date';

					$set_col_tk["CenterName"] = 'Center Name';

					$set_col_tk["CenterGST"] = 'Center GST';

					$set_col_tk["company"] = 'Party Name';

					$set_col_tk["GSTIN"] = 'GSTIN';

					$set_col_tk["BIllNo"] = 'Bill No';

					$set_col_tk["Cash Amt"] = 'Cash Amt';

					$set_col_tk["OnlineAmt"] = 'Online Amt';

					$set_col_tk["OrderTotal"] = 'Order Amt';

					$set_col_tk["OtherTotal"] = 'Other Amt';

					$set_col_tk["ItemDiscAmt"] = 'Disc Amt';

					$set_col_tk["ItemtaxableAmt"] = 'Taxable Amt';

					$set_col_tk["ItemCGSTAmt"] = 'CGST Amt';

					$set_col_tk["ItemSGSTAmt"] = 'SGST Amt';

					$set_col_tk["ItemIGSTAmt"] = 'IGST Amt';

					$set_col_tk['ItemNetTotal'] = 'Net Amt';               

					$set_col_tk['status'] = 'Order Status';

					$set_col_tk['Order Type'] = 'Order Type';

				}else {  

					$set_col_tk["OrderID"] = 'OrderID';

					$set_col_tk["Transdate"] = 'Order Date';

					$set_col_tk["InvoiceNo"] = 'InvoiceNo';

					$set_col_tk["InvoiceDate"] = 'Invoice Date';

					$set_col_tk["CenterName"] = 'Center Name';

					$set_col_tk["CenterGST"] = 'Center GST';

					$set_col_tk["company"] = 'Party Name';

					$set_col_tk["GSTIN"] = 'GSTIN';

					$set_col_tk["BIllNo"] = 'Bill No';

					$set_col_tk["ProductName"] = 'Item Name';

					$set_col_tk["HSNCode"] = 'HSN Code';

					$set_col_tk["GST%"] = 'GST%';

					$set_col_tk["Unit"] = 'Unit';

					$set_col_tk["itemqty"] = 'Quantity';

					$set_col_tk["OrderAmt"] = 'Item Amt';

					$set_col_tk["discountamt"] = 'Disc Amt';

					$set_col_tk["ItemtaxableAmt"] = 'Taxable Amt';

					$set_col_tk["ItemCGSTAmt"] = 'CGST Amt';

					$set_col_tk["ItemSGSTAmt"] = 'SGST Amt';

					$set_col_tk["ItemIGSTAmt"] = 'IGST Amt';

					$set_col_tk["NetOrdAmt"] = 'Net Amt';

					$set_col_tk['status'] = 'Order Status';

					$set_col_tk['Order Type'] = 'Order Type';

				}  

				

				

				$writer_header = $set_col_tk;

				

				$writer->writeSheetRow('Sheet1', $writer_header);

				

				$totalQtySum = 0;$TotalItemAmt = 0;$TotalDiscAmt = 0;$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;

				$CashTotal = 0;$OnlineTotal = 0;$TotalOtherAmt = 0;

				foreach ($result as $k => $value) 

				{  

					if ($value['OrderStatus'] == "O") {

						$OrderStat = "Pending";

					} elseif ($value['OrderStatus'] == "F") {

						$OrderStat = "Completed";

					} elseif ($value['OrderStatus'] == "C") {

						$OrderStat = "Cancelled";

					}

					$OrderType = "";

        			if($value['OrderPaymentType'] == "1"){

        			    $OrderType = "Cash Order";

        			}else if($value['OrderPaymentType'] == "2"){

        			    $OrderType = "Credit Order";

        			}

					$BillNo = $value['BIllNo'];

    				if($value['BIllNo'] == "" || $value['BIllNo'] == NULL){

    				    $BillNo = $value['PartyBillNo'];

    				}

					if ($post_data['Report_type'] == "1") 

					{

						$OrdItemTotal = 0;

						$OrdItemDiscAmt = 0;

						$OrdTaxableAmt = 0;

						$OrdCGSTAmt = 0;$OrdSGSTAmt = 0;$OrdIGSTAmt = 0;

						$OrdNetTotal = 0;

						$GSTNO = "";

						$TransID = $value["InvoiceNo"];

						foreach($ItemData as $key1=>$val2){

							if($value["OrderID"] == $val2["OrderID"]){

								$GSTNO = $val2["PartyGST"];

								$TransID = $val2["TransID"];

    							$TaxableAmt = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$GSTAmt = 0;$NetAmt = 0;

    							$OrderAmt = $val2["OrderAmt"] - $val2["DiscAmt"];

    							//$GSTPer = $val2['cgst'] + $val2['sgst'] + $val2['igst'];

            					$GSTPer = $val2['taxrate'];

            					$ExGSTAmt = $val2['sgstamt'] + $val2['cgstamt'] + $val2['igstamt'];

            					if($ExGSTAmt > 0){

            					    $TaxableAmt = $OrderAmt;

            					    $GSTAmt = $ExGSTAmt;

            					}else{

            					    $TaxableAmt = $OrderAmt / (1+($GSTPer/100));

            					    $GSTAmt = $OrderAmt - $TaxableAmt;

            					}

            					

            					if($val2['state'] == $val2['CenterState'] || $val2['state'] == ""){

            					    $CGSTAmt = $GSTAmt / 2;

            					    $SGSTAmt = $GSTAmt / 2;

            					}else{

            					    $IGSTAmt = $GSTAmt;

            					}

    							$OrdItemTotal += $val2["OrderAmt"];

    							$OrdItemDiscAmt += $val2["DiscAmt"];

    							$OrdCGSTAmt += $CGSTAmt;

    							$OrdSGSTAmt += $SGSTAmt;

    							$OrdIGSTAmt += $IGSTAmt;

    							$GSTAmt = $CGSTAmt + $SGSTAmt + $IGSTAmt;

    							$OrdTaxableAmt += $TaxableAmt;

    							$NetAmt = $TaxableAmt + $GSTAmt;

    							$OrdNetTotal += $NetAmt;

							}

						}

						$CashTotal += $value["CashAmt"];

						$OnlineTotal += $value["OnlineAmt"];

						$TotalItemAmt += $OrdNetTotal;

						$TotalOtherAmt += $value["OtherAmt"];

						

						// For Bill Wise Report

						$list_add = [];  

						$list_add[] = $value["OrderID"];

						$list_add[] = _d(substr($value["Transdate"],0,10));

						$list_add[] = $TransID;

						$list_add[] = _d(substr($value["InvoiceDate"],0,10));

						$list_add[] = $value["CenterName"];

						$list_add[] = $value["GSTNo"];

						$list_add[] = $value["company"];

						$list_add[] = $GSTNO;

						$list_add[] = $BillNo;

						$list_add[] = number_format($value["CashAmt"], 2, '.', '');  

						$list_add[] = number_format($value["OnlineAmt"], 2, '.', '');  

						$list_add[] = number_format($OrdItemTotal, 2, '.', ''); 

						$list_add[] = number_format($value["OtherAmt"], 2, '.', '');

						$list_add[] = number_format($OrdItemDiscAmt, 2, '.', ''); 

						$list_add[] = number_format($OrdTaxableAmt, 2, '.', '');   

						$list_add[] = number_format($OrdCGSTAmt, 2, '.', '');    

						$list_add[] = number_format($OrdSGSTAmt, 2, '.', '');    

						$list_add[] = number_format($OrdIGSTAmt, 2, '.', '');    

						$OrdNetTotal += $value["OtherAmt"];

						$list_add[] = number_format($OrdNetTotal, 2, '.', ''); 

						$list_add[] = $OrderStat;  

						$list_add[] = $OrderType;  

						

						

						$TotalDiscAmt += $OrdItemDiscAmt;

						$TotalTaxableAmt += $OrdTaxableAmt;

						$TotalCGSTAmt += $OrdCGSTAmt;

						$TotalSGSTAmt += $OrdSGSTAmt;

						$TotalIGSTAmt += $OrdIGSTAmt;

						$TotalNetAmt += $OrdNetTotal;  

						

						$writer->writeSheetRow('Sheet1', $list_add); 			

						

					}else  

					{                   

						$OrdItemTotal = 0;

						$OrdItemDiscAmt = 0;

						$OrdTaxableAmt = 0;

						$OrdCGSTAmt = 0;$OrdSGSTAmt = 0;$OrdIGSTAmt = 0;

						$OrdNetTotal = 0;

						$list_add = [];   

						$GSTNO = $value["PartyGST"];

						//$GSTPer = $value['cgst'] + $value['sgst'] + $value['igst'];

						$GSTPer = $value['taxrate'];

						$list_add[] = $value["OrderID"];

						$list_add[] = _d(substr($value["TransDate"],0,10));

						$list_add[] = $value["TransID"];

						$list_add[] = _d(substr($value["InvoiceDate"],0,10));

						$list_add[] = $value["CenterName"];

						$list_add[] = $value["GSTNo"];

						$list_add[] = $value["company"];

						$list_add[] = $GSTNO;

						$list_add[] = $BillNo;

						$list_add[] = $value['ProductName'];

						$list_add[] = $value["hsn_code"];

						$Unit = "";

						if($value['PackingQty'] == $value['CaseQty']){

							$Unit = $value['unit'];

						}else{

							$Unit = "Pcs";

						}

						$list_add[] = $GSTPer;

						$list_add[] = $Unit;

						$TaxableAmt = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$GSTAmt = 0;$NetAmt = 0;

    					$OrderAmt = $value["OrderAmt"] - $value["DiscAmt"];

    					$ExGSTAmt = $value['sgstamt'] + $value['cgstamt'] + $value['igstamt'];

    					

    					if($ExGSTAmt > 0){

    					    $TaxableAmt = $OrderAmt;

    					    $GSTAmt = $ExGSTAmt;

    					}else{

    					    $TaxableAmt = $OrderAmt / (1+($GSTPer/100));

    					    $GSTAmt = $OrderAmt - $TaxableAmt;

    					}

    					if($value['state'] == $value['CenterState'] || $value['state'] == ""){

        				    $CGSTAmt = $GSTAmt / 2;

        				    $SGSTAmt = $GSTAmt / 2;

        				}else{

        				    $IGSTAmt = $GSTAmt;

        				}

    					

    					$OrdItemTotal += $value["NetOrderAmt"];

    					$OrdItemDiscAmt += $value["DiscAmt"];

    					$OrdCGSTAmt += $CGSTAmt;

    					$OrdSGSTAmt += $SGSTAmt;

    					$OrdIGSTAmt += $IGSTAmt;

    					$GSTAmt = $CGSTAmt + $SGSTAmt + $IGSTAmt;

    					$TaxableAmt = $value["NetOrderAmt"] - $GSTAmt;

    					$OrdTaxableAmt += $TaxableAmt;

    					$NetAmt = $TaxableAmt + $GSTAmt;

    					$OrdNetTotal += $NetAmt;

    					$sum_itemqty += $value["OrderQty"];

					

						$list_add[] = number_format($value["OrderQty"], 2, '.', '');              

						$list_add[] = number_format($value["OrderAmt"], 2, '.', '');              

						$list_add[] = number_format($value["DiscAmt"], 2, '.', '');                

						$list_add[] = number_format($TaxableAmt, 2, '.', '');

						$list_add[] = number_format($CGSTAmt, 2, '.', '');

						$list_add[] = number_format($SGSTAmt, 2, '.', '');

						$list_add[] = number_format($IGSTAmt, 2, '.', '');

						$list_add[] = number_format($NetAmt, 2, '.', ''); 

						$list_add[] = $OrderStat; 

						$list_add[] = $OrderType;  

						

						$TotalItemAmt += $OrdItemTotal;

						$TotalDiscAmt += $OrdItemDiscAmt;

						$TotalTaxableAmt += $OrdTaxableAmt;

						$TotalCGSTAmt += $OrdCGSTAmt;

						$TotalSGSTAmt += $OrdSGSTAmt;

						$TotalIGSTAmt += $OrdIGSTAmt;

						$TotalNetAmt += $OrdNetTotal;

						$writer->writeSheetRow('Sheet1', $list_add);   	

					}                   

				}	   

				

				if ($post_data['Report_type'] == "1") {

					$sum_row = [];

					$sum_row[] = ''; 

					$sum_row[] = '';

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = number_format($CashTotal, 2, '.', '');  

					$sum_row[] = number_format($OnlineTotal, 2, '.', '');  

					$sum_row[] = number_format($TotalItemAmt, 2, '.', '');  

					$sum_row[] = number_format($TotalOtherAmt, 2, '.', '');  

					$sum_row[] = number_format($TotalDiscAmt, 2, '.', '');       

					$sum_row[] = number_format($TotalTaxableAmt, 2, '.', '');    

					$sum_row[] = number_format($TotalCGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalSGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalIGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalNetAmt, 2, '.', '');   

					$sum_row[] = ''; 

					$sum_row[] = ''; 

				}else{

					$sum_row = [];

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = '';

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = ''; 

					$sum_row[] = number_format($sum_itemqty, 2, '.', '');       

					$sum_row[] = number_format($TotalItemAmt, 2, '.', '');       

					$sum_row[] = number_format($TotalDiscAmt, 2, '.', '');       

					$sum_row[] = number_format($TotalTaxableAmt, 2, '.', '');    

					$sum_row[] = number_format($TotalCGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalSGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalIGSTAmt, 2, '.', '');   

					$sum_row[] = number_format($TotalNetAmt, 2, '.', '');        

					$sum_row[] = ''; 

					$sum_row[] = ''; 

				}

				$writer->writeSheetRow('Sheet1', $sum_row);

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'SaleOrderList.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

		} 

		

		public function AddEditPaymentMethod()

		{

			$paymentMethod = $this->ItemModel->get_all_table_data($tablename="tblpaymentmethod");

			$data['paymentMethod'] = $paymentMethod;

			

			$maxpayId = $this->ItemModel->get_max_pay_id();

			$data['maxpayId'] = $maxpayId;

			$this->load->view('admin/ItemMaster/AddEditPaymentMethod',$data);

		}

		

		public function insertPaymentDetails()

		{

			$MethodName = $this->input->post('MethodName');

			

			$insert_payment = array(

            'PaymentMethod'=>$MethodName,      

			);

			$createnewpayment = $this->ItemModel->insert_data($tablename="tblpaymentmethod",$insert_payment);

			if ($createnewpayment) {    

				$newPayId = $this->db->insert_id();       

				echo json_encode(['success' => true,'message' => 'Data inserted successfully', 'newMaxPayId' => $newPayId]);

				} else {

				echo json_encode(['success' => false, 'message' => 'Failed to insert card']);

			}

		}

		

		public function payment_table_data()

		{

			$payments =  $this->ItemModel->get_all_table_data($tablename="tblpaymentmethod");

			echo json_encode($payments);

		}

		

		public function GetPaymentDetailsbyID()

		{

			$PayID = $this->input->post('PayID');

			$where = '(id="'.$PayID.'")'; 

			$Paydetails = $this->ItemModel->get_data($tablename="tblpaymentmethod",$where);

			echo json_encode($Paydetails);

		}

		

		public function UpdatePaymentDetails()

		{

			$paymentid = $this->input->post('paymentid');

			$methodname = $this->input->post('methodname');

			

			$update_payment = array(

            'PaymentMethod'=>$methodname,      

			);

			$where = '(id="'.$paymentid.'")'; 

			$updatepayment = $this->ItemModel->edit_data($tablename="tblpaymentmethod",$where,$update_payment);

			if($updatepayment)

			{

				$Payments =  $this->ItemModel->get_all_table_data($tablename="tblpaymentmethod");            

				

				echo json_encode(['success' => true,'message' => 'Data updated successfully','Payment_method' => $Payments]);

			}

			else

			{

				echo json_encode(['success' => false, 'message' => 'Failed to update brand']);

			}

		}

		

		public function AddEditItemOrder()

		{

			if (!has_permission_new('OrderMaster', '', 'view')) {

				access_denied('Invoice Items');

			}

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			

			$pincodeDetails = $this->ItemModel->get_all_table_data($tablename="tblpin");

			$data['pincodeDetails'] = $pincodeDetails;

			

			$nextK1OrderNumber = get_option('next_K1Order_number_for_kirti'); 

			$data['NextOrderId'] = $nextK1OrderNumber;

			

			//$SubActGroupID  = 1000006;

			//$where = '(SubActGroupID="'.$SubActGroupID.'")'; 

			$clients = $this->ItemModel->get_all_table_data($tablename="tblclients");

			$data['clients'] = $clients;

			

			$states = $this->ItemModel->get_all_table_data($tablename="tblxx_statelist");

			$data['states'] = $states;

			

			$citylist = $this->ItemModel->get_all_table_data($tablename="tblxx_citylist");

			$data['citylist'] = $citylist;

			

			$talukalist = $this->ItemModel->get_all_table_data($tablename="tblTalukaMaster");

			$data['talukalist'] = $talukalist;

			

			$products = $this->ItemModel->get_all_table_data($tablename="tblproduct");

			$data['products'] = $products;      

			$centermaster = $this->ItemModel->get_all_table_data($tablename="tblCenterMaster");

			$data['centermaster'] = $centermaster; 

			

			$MainGroup = 10011;

			$data['DirectIncome'] =  $this->ItemModel->GetAllDirectIncomeLedger($MainGroup);

			

			$SubactgropuId = 1000017;

			$wh_effect = '(SubActGroupID="'.$SubactgropuId.'")'; 

			$EffectOn = $this->ItemModel->get_all_data($tablename="tblclients",$wh_effect);

			$data['EffectOn'] = $EffectOn; 

			

			$where = ['FY' => $fy];  

			$orderBy = 'Transdate ASC';

			$OrderDetails = $this->ItemModel->get_all_data_orderby($tablename="tblK1ordermaster",$orderBy,$where);

			$data['OrderDetails'] = $OrderDetails;

			$this->load->view('admin/ItemMaster/AddEditItemOrder',$data);

		}

		

		public function GetPincodeDetailbyId()

		{

			$pincode = $this->input->post('pincode');

			$where = '(id="'.$pincode.'")'; 

			$PincodeDetails = $this->ItemModel->get_data($tablename="tblpin",$where);

			echo json_encode($PincodeDetails);

		}

		

		public function GetAccountWiseFarmerDetails()

		{

			$AccountID = $this->input->post('AccountID');       

			$where = '(AccountID="'.$AccountID.'")'; 

			$clientDetails = $this->ItemModel->get_data($tablename="tblclients",$where);

			

			$historyDetails = $this->ItemModel->get_all_data($tablename="tblK1history",$where);

			foreach($historyDetails as &$val)

			{

				$whs = '(ProductID="'.$val['ItemID'].'")'; 

				$itemdetails = $this->ItemModel->get_data($tablename="tblproduct",$whs);

				$val['ProductName'] = $itemdetails['ProductName'];

			}

			

			$orderDetails = $this->ItemModel->get_data($tablename="tblK1ordermaster",$where);

			

			$salesDetails = $this->ItemModel->get_data($tablename="tblK1salesmaster",$where);

			

			$total_bal = $this->ItemModel->get_data_for_account_bal($AccountID);

			$data_report = $this->ItemModel->get_data_general_ledger2($AccountID);

			

			$new_acc_bal = $total_bal->BAL1;

			$opening_bal = $total_bal->BAL1;  

			$CRSum = 0;

			$DRSum = 0;

			$finacial_year = $this->session->userdata('finacial_year');

			$total_debit = 0;

			$total_credit = 0;

			

			if (empty($data_report)) 

			{

				$OCR = 0.00;

				$ODR = 0.00;

				if ($new_acc_bal <= 0) {

					$OCR = abs($new_acc_bal);

					$OB = $OCR . 'Cr';

					} else {

					$ODR = abs($new_acc_bal);

					$OB = $ODR . 'Dr';

				}

			} 

			else 

			{

				$OCR = 0.00;

				$ODR = 0.00;

				if($new_acc_bal <=0){

					$OCR = abs($new_acc_bal);

					$OB = $OCR.'Cr';

					}else{

					$ODR = abs($new_acc_bal);

					$OB = $ODR.'Dr';

				}

				

				$total_credit = $total_credit + $OCR;

				$total_debit = $total_debit + $ODR;

				

				foreach ($data_report as $key => $value) 

				{

					if ($value["Amount"] !== "0.00") 

					{                    

						// Update the balance based on transaction type (Debit or Credit)

						if ($value["TType"] == "D") {

							$new_acc_bal = $new_acc_bal + $value["Amount"];

							$dvalue = $value["Amount"];

							$total_debit = $total_debit + $dvalue;                       

							$dvalue = number_format($dvalue,2);                                     

						}

						

						if ($value["TType"] == "C") {

							$new_acc_bal = $new_acc_bal - $value["Amount"];

							$cvalue = $value["Amount"];

							$total_credit = $total_credit + $cvalue;

							$cvalue = number_format($cvalue,2);                        

						}                    

						

						// Calculate the new balance (new_acc_bal2)

						$new_acc_bal2 = $new_acc_bal;

						if ($new_acc_bal > 0) {

							$nab_dr_cr = "Dr";

							} else {

							$nab_dr_cr = "Cr";

						}      

						

						// Round off the final balance to 2 decimal places

						$new_acc_bal2 = number_format($new_acc_bal2, 2) . " " . $nab_dr_cr;                         

						// At this point, you can use $new_acc_bal2 for further calculations or logging if needed

					}

				}            

			}

			

			$data = array(

            'clientDetails' => $clientDetails, 

            'historyDetails' => $historyDetails,

            'orderDetails'=> $orderDetails,

            'salesDetails'=> $salesDetails,

            'ClosingBalance'=>$new_acc_bal2,

			);      

			echo json_encode($data);        

		}

		

		public function GetOrderWiseItemDetails()

		{

			$OrderId = $this->input->post('OrderId');

			$where = '(OrderID="'.$OrderId.'")'; 

			$orderDetails = $this->ItemModel->get_data($tablename="tblK1ordermaster",$where);

			$where_order = '(OrderID="'.$OrderId.'")'; 

			$salesmasterDetails = $this->ItemModel->get_data($tablename="K1salesmaster",$where_order);

			

			$wh_center =  '(CenterID="'.$orderDetails['CenterID'].'")'; 

			$centerDetails = $this->ItemModel->get_data($tablename="tblCenterMaster",$wh_center);

			

			$wh_client = '(AccountID="'.$orderDetails['AccountID'].'")'; 

			$clientDetails = $this->ItemModel->get_data($tablename="tblclients",$wh_client);

			

			$historyDetails = $this->ItemModel->get_all_data($tablename="tblK1history",$where);

			foreach($historyDetails as &$val)

			{

				$whs = '(ProductID="'.$val['ItemID'].'")'; 

				$itemdetails = $this->ItemModel->get_data($tablename="tblproduct",$whs);

				

				$whbrand =  '(id="'.$itemdetails['BrandId'].'")'; 

				$brand = $this->ItemModel->get_data($tablename="tblbrands",$whbrand);

				

				$wh_gst =  '(id="'.$itemdetails['gst'].'")'; 

				$taxes = $this->ItemModel->get_data($tablename="tbltaxes",$wh_gst);

				

				$val['brandname'] = $brand['BrandName'];

				$val['ProductName'] = $itemdetails['ProductName'];

				$val['PackingQty'] = $itemdetails['PackingQty'];

				$val['MeasuredIn'] = $itemdetails['unit'];

				$val['PackingQty'] = $itemdetails['PackingQty'];

				$val['PackingWeight'] = $itemdetails['PackingWeight'];

				$val['gst'] = $taxes['taxrate'];

			}

			

			$salesDetails = $this->ItemModel->get_data($tablename="tblK1salesmaster",$wh_client);

			

			$AccountID = $orderDetails['AccountID'];

			$total_bal = $this->ItemModel->get_data_for_account_bal($AccountID);

			$data_report = $this->ItemModel->get_data_general_ledger2($AccountID);

			

			$new_acc_bal = $total_bal->BAL1;

			$opening_bal = $total_bal->BAL1;  

			$CRSum = 0;

			$DRSum = 0;

			$finacial_year = $this->session->userdata('finacial_year');

			$total_debit = 0;

			$total_credit = 0;

			

			if (empty($data_report)) 

			{

				$OCR = 0.00;

				$ODR = 0.00;

				if ($new_acc_bal <= 0) {

					$OCR = abs($new_acc_bal);

					$OB = $OCR . 'Cr';

					} else {

					$ODR = abs($new_acc_bal);

					$OB = $ODR . 'Dr';

				}

			} 

			else 

			{

				$OCR = 0.00;

				$ODR = 0.00;

				if($new_acc_bal <=0){

					$OCR = abs($new_acc_bal);

					$OB = $OCR.'Cr';

					}else{

					$ODR = abs($new_acc_bal);

					$OB = $ODR.'Dr';

				}

				

				$total_credit = $total_credit + $OCR;

				$total_debit = $total_debit + $ODR;

				

				foreach ($data_report as $key => $value) 

				{

					if ($value["Amount"] !== "0.00") 

					{                    

						// Update the balance based on transaction type (Debit or Credit)

						if ($value["TType"] == "D") {

							$new_acc_bal = $new_acc_bal + $value["Amount"];

							$dvalue = $value["Amount"];

							$total_debit = $total_debit + $dvalue;                       

							$dvalue = number_format($dvalue,2);                                     

						}

						

						if ($value["TType"] == "C") {

							$new_acc_bal = $new_acc_bal - $value["Amount"];

							$cvalue = $value["Amount"];

							$total_credit = $total_credit + $cvalue;

							$cvalue = number_format($cvalue,2);                        

						}                    

						

						// Calculate the new balance (new_acc_bal2)

						$new_acc_bal2 = $new_acc_bal;

						if ($new_acc_bal > 0) {

							$nab_dr_cr = "Dr";

							} else {

							$nab_dr_cr = "Cr";

						}      

						

						// Round off the final balance to 2 decimal places

						$new_acc_bal2 = number_format($new_acc_bal2, 2) . " " . $nab_dr_cr;                         

						// At this point, you can use $new_acc_bal2 for further calculations or logging if needed

					}

				}            

			}

			

			$data = array(

            'clientDetails' => $clientDetails, 

            'historyDetails' => $historyDetails,

            'orderDetails'=> $orderDetails,

            'salesDetails'=> $salesDetails,

            'salesMasterDetails'=> $salesmasterDetails,

            'centerDetails'=>$centerDetails,

			'ClosingBalance'=>$new_acc_bal2

			);

			echo json_encode($data);

		}

		

		public function GetProductDetailById()

		{

			$OrderId = $this->input->post('OrderId');

			$ProductID  = $this->input->post('productID');

			$CenterID = $this->input->post('CenterName');

			$accountID = $this->input->post('accountID');

			

			$wh_client = '(AccountID="'.$accountID.'")'; 

			$clientDetails = $this->ItemModel->get_data($tablename="tblclients",$wh_client);

			

			$wh_center =  '(CenterID="'.$CenterID.'")'; 

			$centerDetails = $this->ItemModel->get_data($tablename="tblCenterMaster",$wh_center);

			

			$where = '(ProductID="'.$ProductID.'")'; 

			$ProductDetails = $this->ItemModel->get_data($tablename="tblproduct",$where);

			$wh_subcategory = '(id="'.$ProductDetails['Subcategory'].'")'; 

			$Subcatgeory = $this->ItemModel->get_data($tablename="tblsubcategory",$wh_subcategory);

			$ProductDetails['SubcategoryName'] = $Subcatgeory['SubcategoryName'];

			

			$wh_brand = '(id="'.$ProductDetails['BrandId'].'")'; 

			$brands = $this->ItemModel->get_data($tablename="tblbrands",$wh_brand);

			$ProductDetails['BrandName'] = $brands['BrandName'];

			

			$wh_history = '(ItemID="'.$ProductID.'" AND OrderID="'.$OrderId.'")';

			$historyDetails = $this->ItemModel->get_data($tablename="tblK1history",$wh_history);

			

			$wh_taxes = '(id="'.$ProductDetails['gst'].'")'; 

			$gst = $this->ItemModel->get_data($tablename="tbltaxes",$wh_taxes);

			$ProductDetails['taxrate'] = $gst['taxrate'];

			if ($ProductDetails) {            

				echo json_encode([

                'success' => true,

                'product' => $ProductDetails,

                'clientDetails'=>$clientDetails,

                'centerDetails'=>$centerDetails,

                'historyDetails'=>$historyDetails

				]);

				} else {           

				echo json_encode([

                'success' => false,

                'message' => 'Product details not found'

				]);

			}

		}

		

		public function GetCenterName()

		{

			$CenterID = $this->input->post('CenterName');

			$accountID = $this->input->post('accountID');

			

			$wh_client = '(AccountID="'.$accountID.'")'; 

			$clientDetails = $this->ItemModel->get_data($tablename="tblclients",$wh_client);

			

			$wh_center =  '(CenterID="'.$CenterID.'")'; 

			$centerDetails = $this->ItemModel->get_data($tablename="tblCenterMaster",$wh_center);

			

			echo json_encode([

            'success' => true,           

            'clientDetails'=>$clientDetails,

            'centerDetails'=>$centerDetails

			]);

		}

		

		public function AddEditOrder()

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			$UserID = $this->session->userdata('username'); 	

			

			//$OrderId = $this->input->post('OrderId');

			

			$PostedDate = $this->input->post('PostedDate');

			$date = DateTime::createFromFormat('d/m/Y', $PostedDate);

			$formattedDate = $date->format('Y-m-d h:i:s');

			

			$nextK1OrderNumber = get_option('next_K1Order_number_for_kirti'); 

			$OrderId = "ORD".$fy.$nextK1OrderNumber;

			$AccountId = $this->input->post('AccountId');

			$CenterId = $this->input->post('CenterId');       

			//$OrderAmt = $this->input->post('OrderAmt');       

			$InvoiceType = $this->input->post('InvoiceType');

			$TotalCgstAmt = $this->input->post('TotalCgstAmt');

			$TotalSgstAmt = $this->input->post('TotalSgstAmt');

			$IgstAmt = $this->input->post('IgstAmt');

			$TotalValue = $this->input->post('TotalValue');

			$TotalDiscountAmt = $this->input->post('TotalDiscountAmt');

			$TotalNetPayableAmt =  $this->input->post('TotalNetPayableAmt');      

			$Effecton = $this->input->post('Effecton');

			$OrderType = $this->input->post('OrderType');

			$PaymentMode = $this->input->post('PaymentMode');

			$PaymentMethod = $this->input->post('PaymentMethod');

			$ReferenceNo = $this->input->post('ReferenceNo'); 

			

			$VillageName = $this->input->post('VillageName');	

			

			

			$OtherAmt = $this->input->post('OtherAmt');	

			$EffectOnOtherAmt = $this->input->post('EffectOnOtherAmt');	

			

			if(empty($OtherAmt)){

				$OtherAmt = null;

				$EffectOnOtherAmt = null;

			}

			

			$PartyName = $this->input->post('PartyName');

			$nameParts = explode(' ', $PartyName);

			if(count($nameParts) >= 2) {

				$firstName = $nameParts[0];   

				$lastName = implode(' ', array_slice($nameParts, 1));            

				} else {

				$firstName = $PartyName;

				$lastName= "";

			}    

			

			$MobileNo = $this->input->post('MobileNo');

			$BillingState = $this->input->post('BillingState');

			$BillNo = $this->input->post('BillNo'); 

			

			$RndAmt = $this->input->post('RndAmt');

			$RoundAmt = abs($RndAmt);	

			

			if($OrderType == 2)

			{

				$paymode = "";

				$paymethod = "";

				$refnumber = "";

			}

			else if($OrderType == 1)

			{

				$paymode = $PaymentMode;

				$paymethod = $PaymentMethod;

				$refnumber = $ReferenceNo;

			}              

			

			$SaleLedgerAmount = $TotalValue + $TotalDiscountAmt;      

			

			$wh_client = '(AccountID="'.$AccountId.'")'; 

			$clients = $this->ItemModel->get_data($tablename="tblclients",$wh_client);

			

			$orderData  = json_decode($_POST['orderData'], true);                     

			

			$nextOrdernumber = get_option('next_K1Order_number_for_kirti');        

			

			$nextChallannumber = get_option('next_K1Challan_number_for_kirti'); 

			$nextTaxNumber = get_option('next_K1Tax_number_for_kirti'); 

			$nextNonTaxNumber = get_option('next_K1NonTax_number_for_kirti'); 

			

			$prefixchallan = "CHL";

			$kirtione= 1;

			$ConcatenatedChallanNumber = $prefixchallan . $fy . $selected_company . $kirtione . $nextChallannumber;

			

			$prefixTaxNo = "TAX";

			$ConcatenatedTaxNumber = $prefixTaxNo . $fy . $selected_company . $kirtione . $nextTaxNumber;

			

			$prefixNonTaxNo = "BOS";

			$ConcatenatedNonTaxNumber = $prefixNonTaxNo . $fy . $selected_company . $kirtione . $nextNonTaxNumber;

			

			if (isset($orderData[0]) && isset($orderData[0]['GST']) && $orderData[0]['GST'] == 1) 

			{

				$SalesId = $ConcatenatedNonTaxNumber;           

			}

			else{

				$SalesId = $ConcatenatedTaxNumber;        

			}        

			

			if($AccountId !="" && $AccountId !="new")

			{

				if($TotalNetPayableAmt != 0.00)

				{        

					$insert_order = array(    

                    'PlantID'=>$selected_company,

                    'FY'=>$fy,

                    'OrderID'=>$OrderId,    

                    'IsDirectSale'=>'Y',    

                    'ChallanID'=>'',

                    'SalesID'=>'',   

                    'Transdate'=>$formattedDate, 

                    'AccountID'=>$AccountId,

                    'CenterID'=>$CenterId,

                    'GSTNO'=>'',

					'VillageName'=>$VillageName,

                    'OrderAmt'=>$TotalNetPayableAmt,

                    'OrderWeight'=>'0.00',

                    'OrderStatus'=>"O",           

                    'OrderType'=>"TAXITEMS",

                    'UserID'=>$UserID,

                    'OrderPaymentType'=>$OrderType,

                    'PaymentMode'=>$paymode,

                    'PaymentMethod'=>$paymethod,

                    'RefNo'=>$refnumber,

                    'AccountID2'=>'',

                    'Gstin2'=>'',

                    'cnfid'=>'',                   

                    'reason'=>'',

                    'order_type'=>"WEB",

                    'remark'=>''          

					);

					$createneworder = $this->ItemModel->insert_data($tablename="tblK1ordermaster",$insert_order);     

					

					//insert in history table

					$ordno = 1;

					foreach ($orderData as $index => $row) 

					{           

						if (!empty($row['ProductID'])) 

						{ 

							$productId = $row['ProductID'];  

							$subCategory = $row['SubCategory'];

							$brand = $row['Brand'];

							$unit = $row['MeasuredIn'];  

							$packing_qty = $row['PackingQty'];      

							$packing_weight = $row['PackingWeight'];  

							$saleunit = $row['SaleUnit'];                      

							$qty = $row['Qty'];  

							$amount = $row['Amount'];  

							$discount = $row['Discount'];  

							$gst = $row['GST'];  

							$cgstamt = $row['CgstAmt'];

							$sgstamt = $row['SgstAmt'];

							$igstamt = $row['IgstAmt'];

							$netAmount = $row['NetAmount'];  

							

							if($saleunit == $unit)

							{

								$orderquantity = $packing_qty * $qty;      

								$totalAmount = $qty * $amount;       

								//$basicrate = $amount;                    

							}

							else

							{

								$orderquantity = $qty; 

								// $basicrate = $amount / $packing_qty;

								$amountval = ($amount / $packing_qty) * $qty;     

								$totalAmount = $amountval; 

							}     

							

							$discountAmount = ($discount / 100) * $totalAmount;  

							$finalOrderAmt = $totalAmount - $discountAmount;                   

							

							if ($gst != "") 

							{

								if($cgstamt > 0 && $sgstamt > 0)

								{

									$cgst = $cgstamt;

									$sgst = $sgstamt;

									

									$cgstPercentage = ($cgst / $finalOrderAmt) * 100;  

									$sgstPercentage = $cgstPercentage;

									$totalPercentage = $cgstPercentage + $sgstPercentage;

									$salerate = $amount * (1 + $totalPercentage / 100);                               

								}

								else if($igstamt > 0)

								{

									$igst = $igstamt;

									$igstPercentage = ($igst / $finalOrderAmt) * 100;  

									$salerate = $amount * (1 + $igstPercentage / 100);

								}                            

							}   			

							

							if($saleunit =="Loose")

							{

								$caseqty = 1;

								}else{

								$caseqty = $packing_qty;

							}

							

							$wh_subcat = '(SubcategoryName="'.$subCategory.'")'; 

							$subcategory_name = $this->ItemModel->get_data($tablename="tblsubcategory",$wh_subcat);

							

							$wh_brand = '(BrandName="'.$brand.'")'; 

							$brands_name = $this->ItemModel->get_data($tablename="tblbrands",$wh_brand);

							

							$insert_product_detail = array(

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'OrderID'=>$OrderId,

                            'BillID'=>'',

                            'TransID'=>'',

                            'TransDate'=>$formattedDate, 

                            'TransDate2'=>$formattedDate,

                            'TType'=>"O",

                            'TType2'=>"ORDER",

                            'AccountID'=>$AccountId,

                            'ItemID'=>$productId,

                            'CenterID'=>$CenterId,

                            'GodownID'=>'',

                            'PartyID'=>"KASPL",

                            'ChamberID'=>'',

                            'StackID'=>'',

                            'LOTID'=>'',

                            'PurchRate'=>$amount,

                            'SaleRate'=>$salerate,

                            'BasicRate'=>$amount,

                            'SuppliedIn'=>$saleunit,

                            'OrderQty'=>$orderquantity,

                            'eOrderQty'=>'',

                            'BilledQty'=>$orderquantity,

                            'DiscPerc'=>$discount,

                            'DiscAmt'=>$discountAmount,

                            'cgst'=>$cgstPercentage,

                            'cgstamt'=>$cgst,

                            'sgst'=>$sgstPercentage,

                            'sgstamt'=>$sgst,

                            'igst'=>$igstPercentage,

                            'igstamt'=>$igst,

                            'CaseQty'=>$caseqty,

                            'Cases'=>0.00,

                            'OrderAmt'=>$totalAmount,

                            'ChallanAmt'=>$totalAmount,

                            'NetOrderAmt'=>$netAmount,

                            'NetChallanAmt'=>$netAmount,

                            'Ordinalno'=>$ordno,

                            'rowid'=>0,

                            'UserID'=>$UserID,

                            'cnfid'=>'',                          

                            'reason'=>''

							);

							$productdetails= $this->ItemModel->insert_data($tablename="tblK1history",$insert_product_detail);

							$ordno++;

						}

					}                          

					

					//insert in sales table

					$add_entry_sales = array(

                    'PlantID'=>$selected_company,

                    'FY'=>$fy,

                    'BT'=>'',

                    'InvoiceType'=>$InvoiceType,

                    'SalesID'=>$SalesId,

                    'Transdate'=>$formattedDate, 

                    'OrderID'=>$OrderId,

                    'ChallanID'=>'',

                    'PartyID'=>"KASPL",

                    'AccountID'=>$AccountId,

                    'ShipTo'=>'',

                    'CenterID'=>$CenterId,

                    'WHID'=>'',

                    'BrokerID'=>'',

                    'gstno'=>$clients['vat'],

                    'SaleAmt'=>$SaleLedgerAmount,

                    'DiscAmt'=>$TotalDiscountAmt,

                    'sgstamt'=>$TotalSgstAmt,

                    'cgstamt'=>$TotalCgstAmt,

                    'igstamt'=>$IgstAmt,

                    'BillAmt'=>$TotalNetPayableAmt,

                    'RndAmt'=>$RoundAmt,

                    'OtherAmt'=>$OtherAmt,

                    'EffectOnOtherAmt'=>$EffectOnOtherAmt,

                    'ItCount'=>0,

                    'UserID'=>$UserID,

                    'ewayno'=>'',                   

                    'tcs'=>0.00,

                    'tcsAmt'=>0.00,

                    'irn'=>'',

                    'Qrcode'=>'',

                    'QRImg'=>'',

                    'ackno'=>'',                   

                    'TransportID'=>'',

                    'vehicleno'=>'',                   

					);

					$SaleEntry= $this->ItemModel->insert_data($tablename="tblK1salesmaster",$add_entry_sales);

					

					//insert challan details

					$insert_challanDetails = array(

                    'PlantID'=>$selected_company,

                    'FY'=>$fy,

                    'ChallanID'=>$ConcatenatedChallanNumber,

                    'cnfid'=>'',

                    'Transdate'=>$formattedDate, 

                    'RouteID'=>0,

                    'VehicleID'=>'',

                    'DriverID'=>'',

                    'LoaderID'=>'',

                    'SalesmanID'=>'',

                    'ChallanWeight'=>0,

                    'ChallanAmt'=>$TotalNetPayableAmt,                    

                    'Gatepassuserid'=>'',

                    'UserID'=>$UserID                    

					);

					$ChallanEntry= $this->ItemModel->insert_data($tablename="tblK1challanmaster",$insert_challanDetails);

					

					$wh_order =  '(OrderID="'.$OrderId.'")'; 

					$orderDetails = $this->ItemModel->get_data($tablename="tblK1ordermaster",$wh_order);

					

					if($orderDetails['order_type']=="WEB")

					{ $ordstat = "F";

						$TType2 = "SALE";

					}

					else{

						$ordstat = "O";

						$TType2 = "ORDER";

					}

					

					$update_order = array(                   

                    'OrderStatus'=>$ordstat,

                    'ChallanID'=>$ConcatenatedChallanNumber,

                    'SalesID'=>$SalesId

					);

					$wh_updateorder = '(OrderID="'.$OrderId.'")'; 

					$updateorder = $this->ItemModel->edit_data($tablename="tblK1ordermaster",$wh_updateorder,$update_order);

					

					$update_hisotry = array(                   

                    'TType2'=>$TType2,

                    'BillID'=>$ConcatenatedChallanNumber,

                    'TransID'=>$SalesId

					);

					$wh_updateitem = '(OrderID="'.$OrderId.'")'; 

					$updatehisotry = $this->ItemModel->edit_data($tablename="tblK1history",$wh_updateitem,$update_hisotry);               

					

					$update_sales = array(                    

                    "ChallanID"=>$ConcatenatedChallanNumber

					);

					$wh_updatesales = '(OrderID="'.$OrderId.'")'; 

					$updatesales = $this->ItemModel->edit_data($tablename="tblK1salesmaster",$wh_updatesales,$update_sales);

					

					//Add Customer ledger Entries 

					$ord = 1;

					$narration = "By SalesID ".$SalesId."/".$ConcatenatedChallanNumber;

					$insert_customer_ledger = array(

                    'PlantID'=>$selected_company,

                    'FY'=>$fy,

                    'Transdate'=>$formattedDate,

                    'VoucherID'=>$SalesId,       

					'Transdate2'=>$formattedDate,        

                    'PartyID'=>"KASPL",

                    'AccountID'=>$AccountId,

                    'CounterAccount'=>"SALE",

                    'CenterID'=>$CenterId,                  

                    'EntryFor'=>3,

                    'TType'=>"D",

                    'Amount'=>$TotalNetPayableAmt,

                    'Narration'=>$narration,

                    'PassedFrom'=>"SALES",

                    'OrdinalNo'=>$ord,

                    'UserID'=>$UserID                

					);

					$CustLedgerEntry= $this->ItemModel->insert_data($tablename="tblaccountledger",$insert_customer_ledger); 

					$ord ++ ;  

					

					if(!empty($OtherAmt)){

						$insert_other_ledger = array(

						'PlantID'=>$selected_company,

						'FY'=>$fy,

						'Transdate'=>$formattedDate,

						'VoucherID'=>$SalesId,       

						'Transdate2'=>$formattedDate,        

						'PartyID'=>"KASPL",

						'AccountID'=>$EffectOnOtherAmt,

						'CounterAccount'=>"SALE",

						'CenterID'=>$CenterId,                  

						'EntryFor'=>3,

						'TType'=>"C",

						'Amount'=>$OtherAmt,

						'Narration'=>$narration,

						'PassedFrom'=>"SALES",

						'OrdinalNo'=>$ord,

						'UserID'=>$UserID                

						);

						$CustLedgerEntry= $this->ItemModel->insert_data($tablename="tblaccountledger",$insert_other_ledger); 

						$ord ++ ; 

					}

					

					//Add Sale Ledger Entry

					$sale_ledger_entry = array(

                    'PlantID'=>$selected_company,

                    'FY'=>$fy,

                    'Transdate'=>$formattedDate,

                    'VoucherID'=>$SalesId,  

					'Transdate2'=>$formattedDate, 

                    'PartyID'=>"KASPL",

                    'AccountID'=>"SALE",

                    'CounterAccount'=>$AccountId,

                    'CenterID'=>$CenterId,

                    'EntryFor'=>3,

                    'TType'=>"C",

                    'Amount'=>$SaleLedgerAmount,

                    'Narration'=>$narration,

                    'PassedFrom'=>"SALES",

                    'OrdinalNo'=>$ord,

                    'UserID'=>$UserID     

					);

					$SalesLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$sale_ledger_entry); 

					$ord ++ ;     

					

					if($TotalCgstAmt !=0.00 && $TotalSgstAmt != 0.00)

					{

						//CGST Tax Ledger Entry

						$Cgst_Ledger_entry = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"CGST",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"C",

                        'Amount'=>$TotalCgstAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$CgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Cgst_Ledger_entry); 

						$ord ++ ;     

						

						//SGST Tax Ledger Entry

						$Sgst_Ledger_entry = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"SGST",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"C",

                        'Amount'=>$TotalSgstAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$SgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Sgst_Ledger_entry); 

						$ord ++ ;     

					}

					else if($IgstAmt != 0.00)

					{

						//Igst Ledger Entry

						$Igst_Ledger_Entry = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"IGST",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"C",

                        'Amount'=>$IgstAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$IgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Igst_Ledger_Entry); 

						$ord ++ ;     

					}

					

					//Discount Ledger Entry

					if($TotalDiscountAmt > 0)

					{                   

						$disc_ledger_entry = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"DISC",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"D",

                        'Amount'=>$TotalDiscountAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$DiscountLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$disc_ledger_entry); 

						$ord ++ ; 

					}

					

					//RndAmt Ledger Entry

					if($RndAmt >= 0)

					{

						$roundledgerentry_debit = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"ROUNDOFF",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"D",

                        'Amount'=>$RndAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$Round_Debit_LedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$roundledgerentry_debit); 

						$ord ++ ; 

					}

					else

					{

						$amt =  abs($RndAmt);

						$roundledgerentry_credit = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$SalesId,  

						'Transdate2'=>$formattedDate, 

                        'PartyID'=>"KASPL",

                        'AccountID'=>"ROUNDOFF",

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"C",

                        'Amount'=>$amt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"SALES",

                        'OrdinalNo'=>$ord,

                        'UserID'=>$UserID     

						);

						$Round_credit_LedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$roundledgerentry_credit); 

						$ord ++ ; 

					}

					

					if($OrderType == 1)

					{

						$nextReceiptnumber = get_option('next_receipts_number_for_kirti');  

						$ordinalno = 1;

						//Receipt Voucher credit Entry to party

						$receiptentry_credit_toParty = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$nextReceiptnumber, 

						'Transdate2'=>$formattedDate,  

                        'PartyID'=>"KASPL",

                        'AccountID'=>$AccountId,

                        'CounterAccount'=>$Effecton,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"C",

                        'Amount'=>$TotalNetPayableAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"RECEIPTS",

                        'OrdinalNo'=>$ordinalno,

                        'UserID'=>$UserID     

						);

						$CreditToParty = $this->ItemModel->insert_data($tablename="tblaccountledger",$receiptentry_credit_toParty); 

						$ordinalno ++ ; 

						

						//Receipt Voucher Debit Entry to Company

						$receiptentry_debitto_company = array(

                        'PlantID'=>$selected_company,

                        'FY'=>$fy,

                        'Transdate'=>$formattedDate,

                        'VoucherID'=>$nextReceiptnumber, 

						'Transdate2'=>$formattedDate,  

                        'PartyID'=>"KASPL",

                        'AccountID'=>$Effecton,

                        'CounterAccount'=>$AccountId,

                        'CenterID'=>$CenterId,

                        'EntryFor'=>3,

                        'TType'=>"D",

                        'Amount'=>$TotalNetPayableAmt,

                        'Narration'=>$narration,

                        'PassedFrom'=>"RECEIPTS",

                        'OrdinalNo'=>$ordinalno,

                        'UserID'=>$UserID     

						);

						$DebitToCompany = $this->ItemModel->insert_data($tablename="tblaccountledger",$receiptentry_debitto_company); 

						$this->increment_next_number('next_receipts_number_for_kirti');

						

						$update_sales = array(                    

						"ReceiptVoucherID"=>$nextReceiptnumber

						);

        				$wh_updatereceptno = '(SalesID="'.$SalesId.'")'; 

        				$updatesales = $this->ItemModel->edit_data($tablename="tblK1salesmaster",$wh_updatereceptno,$update_sales);

					}

					

					$this->increment_next_number('next_K1Order_number_for_kirti');     

					$this->increment_next_number('next_K1Challan_number_for_kirti');     

					if (isset($orderData[0]) && isset($orderData[0]['GST']) && $orderData[0]['GST'] == 1) 

					{                   

						$this->increment_next_number('next_K1NonTax_number_for_kirti');       

					}

					else{                  

						$this->increment_next_number('next_K1Tax_number_for_kirti');   

					}                   

					$nextOrdernumber = get_option('next_K1Order_number_for_kirti');   

					echo json_encode(['success' => true,'message' => 'Order inserted successfully','nextOrdernumber' => $nextOrdernumber]);           

				}

				else

				{

					echo json_encode(['success' => false, 'message' => 'Add Products']);

				}

			}  

			else if($AccountId !="" && $AccountId =="new")

			{            

				$insert_client_array = array(

				'PlantID'=>$selected_company,

				'AccountID'=>$MobileNo,

				'ShortCode'=>'',

				'IsKirtiOneAccess'=>"Y",

				'company'=>$PartyName,

				'CustomerType'=>1,

				'ActGroupID'=>'10000',

				'SubActGroupID1'=>'100002',

				'SubActGroupID'=>'1000006',

				'AccountFor'=>"Self",

				'phonenumber'=>$MobileNo,

				'state'=>$BillingState,

				'StartDate'=>date('Y-m-d h:i:s'), 

				'datecreated'=>date('Y-m-d h:i:s'), 

				'UserID'=>$UserID,

				'Aadhaar_ver_man'=>"N",

				'active'=>'1',

				);   

				

				$this->db->insert('tblclients', $insert_client_array);           

				

				if ($this->db->affected_rows() > 0) 

				{                

					$insert_contacts = array(

                    'PlantID'=>$selected_company,

                    'AccountID'=>$MobileNo,

                    'firstname'=>$firstName,

                    'lastname'=>$lastName,

                    'gender'=>"M",

                    'phonenumber'=>$MobileNo,

                    'datecreated'=>date('Y-m-d h:i:s'),

                    'active'=>'1',

					);               

					$this->db->insert('tblcontacts', $insert_contacts);   

					if ($this->db->affected_rows() > 0) 

					{

						if($TotalNetPayableAmt != 0.00)

						{    

							$AccountId = $MobileNo;

							$insert_order = array(    

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'OrderID'=>$OrderId,  

                            'IsDirectSale'=>'Y',

                            'ChallanID'=>'',

                            'SalesID'=>'',   

                            'Transdate'=>$formattedDate, 

                            'AccountID'=>$AccountId,

                            'CenterID'=>$CenterId,

                            'GSTNO'=>'',

							'VillageName'=>$VillageName,

                            'OrderAmt'=>$TotalNetPayableAmt,

                            'OrderWeight'=>'0.00',

                            'OrderStatus'=>"O",           

                            'OrderType'=>"TAXITEMS",

                            'UserID'=>$UserID,

                            'OrderPaymentType'=>$OrderType,

                            'PaymentMode'=>$paymode,

                            'PaymentMethod'=>$paymethod,

                            'RefNo'=>$refnumber,

                            'BIllNo'=>$BillNo,

                            'AccountID2'=>'',

                            'Gstin2'=>'',

                            'cnfid'=>'',                   

                            'reason'=>'',

                            'order_type'=>"WEB",

                            'remark'=>''          

							);

							$createneworder = $this->ItemModel->insert_data($tablename="tblK1ordermaster",$insert_order);     

							

							//insert in history table

							$ordno = 1;

							foreach ($orderData as $index => $row) 

							{           

								if (!empty($row['ProductID'])) 

								{ 

									$productId = $row['ProductID'];  

									$subCategory = $row['SubCategory'];

									$brand = $row['Brand'];

									$unit = $row['MeasuredIn'];  

									$packing_qty = $row['PackingQty'];      

									$packing_weight = $row['PackingWeight'];  

									$saleunit = $row['SaleUnit'];                      

									$qty = $row['Qty'];  

									$amount = $row['Amount'];  

									$discount = $row['Discount'];  

									$gst = $row['GST'];  

									$cgstamt = $row['CgstAmt'];

									$sgstamt = $row['SgstAmt'];

									$igstamt = $row['IgstAmt'];

									$netAmount = $row['NetAmount'];  

									

									if($saleunit == $unit)

									{

										$orderquantity = $packing_qty * $qty;      

										$totalAmount = $qty * $amount;       

										//$basicrate = $amount;                    

									}

									else

									{

										$orderquantity = $qty; 

										// $basicrate = $amount / $packing_qty;

										$amountval = ($amount / $packing_qty) * $qty;     

										$totalAmount = $amountval; 

									}     

									

									$discountAmount = ($discount / 100) * $totalAmount;  

									$finalOrderAmt = $totalAmount - $discountAmount;                   

									

									if ($gst != "") 

									{

										if($cgstamt > 0 && $sgstamt > 0)

										{

											$cgst = $cgstamt;

											$sgst = $sgstamt;

											

											$cgstPercentage = ($cgst / $finalOrderAmt) * 100;  

											$sgstPercentage = $cgstPercentage;

											$totalPercentage = $cgstPercentage + $sgstPercentage;

											$salerate = $amount * (1 + $totalPercentage / 100);                               

										}

										else if($igstamt > 0)

										{

											$igst = $igstamt;

											$igstPercentage = ($igst / $finalOrderAmt) * 100;  

											$salerate = $amount * (1 + $igstPercentage / 100);

										}                            

									}   			

									

									if($saleunit =="Loose")

									{

										$caseqty = 1;

										}else{

										$caseqty = $packing_qty;

									}

									

									$wh_subcat = '(SubcategoryName="'.$subCategory.'")'; 

									$subcategory_name = $this->ItemModel->get_data($tablename="tblsubcategory",$wh_subcat);

									

									$wh_brand = '(BrandName="'.$brand.'")'; 

									$brands_name = $this->ItemModel->get_data($tablename="tblbrands",$wh_brand);

									

									$insert_product_detail = array(

                                    'PlantID'=>$selected_company,

                                    'FY'=>$fy,

                                    'OrderID'=>$OrderId,

                                    'BillID'=>'',

                                    'TransID'=>'',

                                    'TransDate'=>$formattedDate, 

                                    'TransDate2'=>$formattedDate,

                                    'TType'=>"O",

                                    'TType2'=>"ORDER",

                                    'AccountID'=>$AccountId,

                                    'ItemID'=>$productId,

                                    'CenterID'=>$CenterId,

                                    'GodownID'=>'',

                                    'PartyID'=>"KASPL",

                                    'ChamberID'=>'',

                                    'StackID'=>'',

                                    'LOTID'=>'',

                                    'PurchRate'=>$amount,

                                    'SaleRate'=>$salerate,

                                    'BasicRate'=>$amount,

                                    'SuppliedIn'=>$saleunit,

                                    'OrderQty'=>$orderquantity,

                                    'eOrderQty'=>'',

                                    'BilledQty'=>$orderquantity,

                                    'DiscPerc'=>$discount,

                                    'DiscAmt'=>$discountAmount,

                                    'cgst'=>$cgstPercentage,

                                    'cgstamt'=>$cgst,

                                    'sgst'=>$sgstPercentage,

                                    'sgstamt'=>$sgst,

                                    'igst'=>$igstPercentage,

                                    'igstamt'=>$igst,

                                    'CaseQty'=>$caseqty,

                                    'Cases'=>0.00,

                                    'OrderAmt'=>$totalAmount,

                                    'ChallanAmt'=>$totalAmount,

                                    'NetOrderAmt'=>$netAmount,

                                    'NetChallanAmt'=>$netAmount,

                                    'Ordinalno'=>$ordno,

                                    'rowid'=>0,

                                    'UserID'=>$UserID,

                                    'cnfid'=>'',                          

                                    'reason'=>''

									);

									$productdetails= $this->ItemModel->insert_data($tablename="tblK1history",$insert_product_detail);

									$ordno++;

								}

							}                          

							

							//insert in sales table

							$add_entry_sales = array(

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'BT'=>'',

                            'InvoiceType'=>$InvoiceType,

                            'SalesID'=>$SalesId,

                            'Transdate'=>$formattedDate, 

                            'OrderID'=>$OrderId,

                            'ChallanID'=>'',

                            'PartyID'=>"KASPL",

                            'AccountID'=>$AccountId,

                            'ShipTo'=>'',

                            'CenterID'=>$CenterId,

                            'WHID'=>'',

                            'BrokerID'=>'',

                            'gstno'=>$clients['vat'],

                            'SaleAmt'=>$SaleLedgerAmount,

                            'DiscAmt'=>$TotalDiscountAmt,

                            'sgstamt'=>$TotalSgstAmt,

                            'cgstamt'=>$TotalCgstAmt,

                            'igstamt'=>$IgstAmt,

                            'BillAmt'=>$TotalNetPayableAmt,

                            'RndAmt'=>$RoundAmt,

                            'ItCount'=>0,

                            'UserID'=>$UserID,

                            'ewayno'=>'',                   

                            'tcs'=>0.00,

                            'tcsAmt'=>0.00,

                            'irn'=>'',

                            'Qrcode'=>'',

                            'QRImg'=>'',

                            'ackno'=>'',                   

                            'TransportID'=>'',

                            'vehicleno'=>'',                   

							);

							$SaleEntry= $this->ItemModel->insert_data($tablename="tblK1salesmaster",$add_entry_sales);

							

							//insert challan details

							$insert_challanDetails = array(

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'ChallanID'=>$ConcatenatedChallanNumber,

                            'cnfid'=>'',

                            'Transdate'=>$formattedDate, 

                            'RouteID'=>0,

                            'VehicleID'=>'',

                            'DriverID'=>'',

                            'LoaderID'=>'',

                            'SalesmanID'=>'',

                            'ChallanWeight'=>0,

                            'ChallanAmt'=>$TotalNetPayableAmt,                    

                            'Gatepassuserid'=>'',

                            'UserID'=>$UserID                    

							);

							$ChallanEntry= $this->ItemModel->insert_data($tablename="tblK1challanmaster",$insert_challanDetails);

							

							$wh_order =  '(OrderID="'.$OrderId.'")'; 

							$orderDetails = $this->ItemModel->get_data($tablename="tblK1ordermaster",$wh_order);

							

							if($orderDetails['order_type']=="WEB")

							{ $ordstat = "F";

								$TType2 = "SALE";

							}

							else{

								$ordstat = "O";

								$TType2 = "ORDER";

							}

							

							$update_order = array(                   

                            'OrderStatus'=>$ordstat,

                            'ChallanID'=>$ConcatenatedChallanNumber,

                            'SalesID'=>$SalesId

							);

							$wh_updateorder = '(OrderID="'.$OrderId.'")'; 

							$updateorder = $this->ItemModel->edit_data($tablename="tblK1ordermaster",$wh_updateorder,$update_order);

							

							$update_hisotry = array(                   

                            'TType2'=>$TType2,

                            'BillID'=>$ConcatenatedChallanNumber,

                            'TransID'=>$SalesId

							);

							$wh_updateitem = '(OrderID="'.$OrderId.'")'; 

							$updatehisotry = $this->ItemModel->edit_data($tablename="tblK1history",$wh_updateitem,$update_hisotry);               

							

							$update_sales = array(                    

                            "ChallanID"=>$ConcatenatedChallanNumber

							);

							$wh_updatesales = '(OrderID="'.$OrderId.'")'; 

							$updatesales = $this->ItemModel->edit_data($tablename="tblK1salesmaster",$wh_updatesales,$update_sales);

							

							//Add Customer ledger Entries 

							$ord = 1;

							$narration = "By SalesID ".$SalesId."/".$ConcatenatedChallanNumber;

							$insert_customer_ledger = array(

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'Transdate'=>$formattedDate,

                            'VoucherID'=>$SalesId,       

                            'Transdate2'=>$formattedDate,        

                            'PartyID'=>"KASPL",

                            'AccountID'=>$AccountId,

                            'CounterAccount'=>"SALE",

                            'CenterID'=>$CenterId,                  

                            'EntryFor'=>3,

                            'TType'=>"D",

                            'Amount'=>$TotalNetPayableAmt,

                            'Narration'=>$narration,

                            'PassedFrom'=>"SALES",

                            'OrdinalNo'=>$ord,

                            'UserID'=>$UserID                

							);

							$CustLedgerEntry= $this->ItemModel->insert_data($tablename="tblaccountledger",$insert_customer_ledger); 

							$ord ++ ;         

							

							//Add Sale Ledger Entry

							$sale_ledger_entry = array(

                            'PlantID'=>$selected_company,

                            'FY'=>$fy,

                            'Transdate'=>$formattedDate,

                            'VoucherID'=>$SalesId,  

                            'Transdate2'=>$formattedDate, 

                            'PartyID'=>"KASPL",

                            'AccountID'=>"SALE",

                            'CounterAccount'=>$AccountId,

                            'CenterID'=>$CenterId,

                            'EntryFor'=>3,

                            'TType'=>"C",

                            'Amount'=>$SaleLedgerAmount,

                            'Narration'=>$narration,

                            'PassedFrom'=>"SALES",

                            'OrdinalNo'=>$ord,

                            'UserID'=>$UserID     

							);

							$SalesLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$sale_ledger_entry); 

							$ord ++ ;     

							

							if($TotalCgstAmt !=0.00 && $TotalSgstAmt != 0.00)

							{

								//CGST Tax Ledger Entry

								$Cgst_Ledger_entry = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"CGST",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"C",

                                'Amount'=>$TotalCgstAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$CgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Cgst_Ledger_entry); 

								$ord ++ ;     

								

								//SGST Tax Ledger Entry

								$Sgst_Ledger_entry = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"SGST",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"C",

                                'Amount'=>$TotalSgstAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$SgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Sgst_Ledger_entry); 

								$ord ++ ;     

							}

							else if($IgstAmt != 0.00)

							{

								//Igst Ledger Entry

								$Igst_Ledger_Entry = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"IGST",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"C",

                                'Amount'=>$IgstAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$IgstLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$Igst_Ledger_Entry); 

								$ord ++ ;     

							}

							

							//Discount Ledger Entry

							if($TotalDiscountAmt > 0)

							{                   

								$disc_ledger_entry = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"DISC",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"D",

                                'Amount'=>$TotalDiscountAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$DiscountLedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$disc_ledger_entry); 

								$ord ++ ; 

							}

							

							//RndAmt Ledger Entry

							if($RndAmt >= 0)

							{

								$roundledgerentry_debit = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"ROUNDOFF",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"D",

                                'Amount'=>$RndAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$Round_Debit_LedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$roundledgerentry_debit); 

								$ord ++ ; 

							}

							else

							{

								$amt =  abs($RndAmt);

								$roundledgerentry_credit = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$SalesId,  

                                'Transdate2'=>$formattedDate, 

                                'PartyID'=>"KASPL",

                                'AccountID'=>"ROUNDOFF",

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"C",

                                'Amount'=>$amt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"SALES",

                                'OrdinalNo'=>$ord,

                                'UserID'=>$UserID     

								);

								$Round_credit_LedgerEntry = $this->ItemModel->insert_data($tablename="tblaccountledger",$roundledgerentry_credit); 

								$ord ++ ; 

							}

							

							if($OrderType == 1)

							{

								$nextReceiptnumber = get_option('next_receipts_number_for_kirti');  

								$ordinalno = 1;

								//Receipt Voucher credit Entry to party

								$receiptentry_credit_toParty = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$nextReceiptnumber, 

                                'Transdate2'=>$formattedDate,  

                                'PartyID'=>"KASPL",

                                'AccountID'=>$AccountId,

                                'CounterAccount'=>$Effecton,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"C",

                                'Amount'=>$TotalNetPayableAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"RECEIPTS",

                                'OrdinalNo'=>$ordinalno,

                                'UserID'=>$UserID     

								);

								$CreditToParty = $this->ItemModel->insert_data($tablename="tblaccountledger",$receiptentry_credit_toParty); 

								$ordinalno ++ ; 

								

								//Receipt Voucher Debit Entry to Company

								$receiptentry_debitto_company = array(

                                'PlantID'=>$selected_company,

                                'FY'=>$fy,

                                'Transdate'=>$formattedDate,

                                'VoucherID'=>$nextReceiptnumber, 

                                'Transdate2'=>$formattedDate,  

                                'PartyID'=>"KASPL",

                                'AccountID'=>$Effecton,

                                'CounterAccount'=>$AccountId,

                                'CenterID'=>$CenterId,

                                'EntryFor'=>3,

                                'TType'=>"D",

                                'Amount'=>$TotalNetPayableAmt,

                                'Narration'=>$narration,

                                'PassedFrom'=>"RECEIPTS",

                                'OrdinalNo'=>$ordinalno,

                                'UserID'=>$UserID     

								);

								$DebitToCompany = $this->ItemModel->insert_data($tablename="tblaccountledger",$receiptentry_debitto_company); 

								$this->increment_next_number('next_receipts_number_for_kirti');

								

								$update_sales = array(                    

								"ReceiptVoucherID"=>$nextReceiptnumber

								);

                				$wh_updatereceptno = '(SalesID="'.$SalesId.'")'; 

                				$updatesales = $this->ItemModel->edit_data($tablename="tblK1salesmaster",$wh_updatereceptno,$update_sales);

							}

							

							$this->increment_next_number('next_K1Order_number_for_kirti');     

							$this->increment_next_number('next_K1Challan_number_for_kirti');     

							if (isset($orderData[0]) && isset($orderData[0]['GST']) && $orderData[0]['GST'] == 1) 

							{                   

								$this->increment_next_number('next_K1NonTax_number_for_kirti');       

							}

							else{                  

								$this->increment_next_number('next_K1Tax_number_for_kirti');   

							}                   

							$nextOrdernumber = get_option('next_K1Order_number_for_kirti');   

							echo json_encode(['success' => true,'message' => 'Order inserted successfully','nextOrdernumber' => $nextOrdernumber]);           

						}

						else

						{

							echo json_encode(['success' => false, 'message' => 'Add Products']);

						}

					}  

					else 

					{

						echo json_encode(['success' => false, 'message' => 'Something Went Wrong..']);

					}

				}

				else 

				{

					echo json_encode(['success' => false, 'message' => 'Add Client']);

				}

			}           

		} 

		

		public function CancelOrderWiseItems()

		{

			$OrderId = $this->input->post('OrderId');          

			

			if($OrderId !="")

			{           

				$where = '(OrderID="'.$OrderId.'")'; 

				$orderDetails = $this->ItemModel->get_data($tablename="tblK1ordermaster",$where);

				

				$updateOrderData = array(                      

                'OrderAmt'=>'0.00',

                'OrderWeight'=>'0.00',

                'OrderStatus'=>"C",           

                'OrderType'=>"",           

                'OrderPaymentType'=>'',

                'PaymentMode'=>'',

                'PaymentMethod'=>'',

                'RefNo'=>''                 

				);

				$cancelOrder = $this->ItemModel->edit_data($tablename="tblK1ordermaster",$where,$updateOrderData);

				

				$updateItemData = array(                         

                'TransDate2'=>date('Y-m-d h:i:s'),

                'TType'=>"C",

                'TType2'=>"ORDER",              

                'OrderQty'=>'0.00',           

                'BilledQty'=>'0.00',

                'DiscPerc'=>'0.00',

                'DiscAmt'=>'0.00',

                'cgst'=>'0.00',

                'cgstamt'=>'0.00',

                'sgst'=>'0.00',

                'sgstamt'=>'0.00',

                'igst'=>'0.00',

                'igstamt'=>'0.00',           

                'OrderAmt'=>'0.00',

                'ChallanAmt'=>'0.00',

                'NetOrderAmt'=>'0.00',

                'NetChallanAmt'=>'0.00'           

				);

				$cancelItemdata = $this->ItemModel->edit_data($tablename="tblK1history",$where,$updateItemData);

				

				$updateSalesdata = array(                

                'SaleAmt'=>'0.00',

                'DiscAmt'=>'0.00',

                'sgstamt'=>'0.00',

                'cgstamt'=>'0.00',

                'igstamt'=>'0.00',

                'BillAmt'=>'0.00'           

				);

				$cancelSalesdata = $this->ItemModel->edit_data($tablename="tblK1salesmaster",$where,$updateSalesdata);

				

				$updateChallandata = array(           

                'ChallanAmt'=>'0.00'           

				);

				$cancelChallandata = $this->ItemModel->edit_data($tablename="tblK1challanmaster",$where,$updateChallandata);

				

				$where_voucher =  '(VoucherID="'.$orderDetails['SalesID'].'")'; 

				//update ledger

				$updateCustomerLedger = array(              

                'Amount'=>'0.00'                      

				);

				$cancelCustomerLedger = $this->ItemModel->edit_data($tablename="tblaccountledger",$where_voucher,$updateCustomerLedger);

				

				echo json_encode(['success' => true,'message' => 'Order cancel successfully']);      

			}     

			else

			{

				echo json_encode(['success' => false, 'message' => 'Something Went Wrong']);

			}

		}

		

		

		public function Order_table_data()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'CategoryTypeFilter'  => $this->input->post('CategoryTypeFilter')   

			);

			$OrderDetails = $this->ItemModel->load_data_for_direct_sale_orderkirtione($data);

			foreach($OrderDetails as &$value)

			{

				$where = '(AccountID="'.$value['AccountID'].'")'; 

				$companyname = $this->ItemModel->get_data($tablename="tblclients",$where);

				$value['name'] = $companyname['company'];

				

				$date = $value["Transdate"];

				$datetime = new DateTime($date); 

				$formattedDate = $datetime->format('d/m/Y');    

				$value['formattedDate'] = $formattedDate;

				

				$wh_items =  '(OrderID="'.$value['OrderID'].'")'; 

				$historyDetails = $this->ItemModel->get_all_data($tablename="tblK1history",$wh_items);

				$totalQuantity = 0;  

				$totalDiscountAmt = 0;      

				$totalValueAmt = 0;  

				$totalCGSTAmt = 0; 

				$totalSGSTAmt = 0;

				$totalIGSTAmt= 0;

				$totalTaxAmt= 0;

				$totalNetAmt= 0;

				

				foreach ($historyDetails as $item) 

				{                

					if (isset($item['OrderQty'])) {

						$totalQuantity += $item['OrderQty'];

					}

					

					if(isset($item['DiscAmt']))

					{

						$totalDiscountAmt += $item['DiscAmt'];

					}

					

					if(isset($item['OrderAmt'])){

						$totalValueAmt += $item['OrderAmt'];

					}

					

					if (isset($item['cgstamt'])) {

						$totalCGSTAmt += $item['cgstamt'];

					}            

					

					if (isset($item['sgstamt'])) {

						$totalSGSTAmt += $item['sgstamt'];

					}

					

					if (isset($item['igstamt'])) {

						$totalIGSTAmt += $item['igstamt'];

					}        

					

					if (isset($item['NetOrderAmt'])) {

						$totalNetAmt += $item['NetOrderAmt'];

					}  

				}

				

				if ($totalCGSTAmt > 0 && $totalSGSTAmt > 0) {                

					$totalTaxAmt = $totalCGSTAmt + $totalSGSTAmt;

					} else if($totalIGSTAmt > 0) {               

					$totalTaxAmt = $totalIGSTAmt;

				}

				else{

					$totalTaxAmt =0.00;

				}

				

				$value['totalQuantity'] = $totalQuantity;

				$value['totalDiscountAmt'] = $totalDiscountAmt;

				$value['totalValueAmt'] = $totalValueAmt;

				$value['totalTaxAmt'] = $totalTaxAmt;

				$value['totalNetAmt'] = $totalNetAmt;

			}        

			echo json_encode($OrderDetails);

		}

		

		

		public function CheckMobileNumber()

		{

			$fy = $this->session->userdata('finacial_year');

			$phonenumber = $this->input->post('phonenumber');

			$whs = '(phonenumber="'.$phonenumber.'")'; 

			$clientDetails = $this->ItemModel->get_data($tablename="tblclients",$whs);

			

			echo json_encode($clientDetails);

		}

		

		public function get_subcategory()

		{

			$category_id = $this->input->post('category_id');

			$subcategories = $this->ItemModel->getSubcategoriesByCategory($category_id);

			echo json_encode($subcategories);

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

		public function GetCategoryBySubCategory()

		{

			$category_id = $this->input->post('category_id');

			$html = '';

			$data = $this->ItemModel->GetCategoryBySubCategoryCode($category_id);

			foreach($data as $key=>$value){

				$html .= '<option value="'.$value['id'].'">'.$value['SubCategoryName'].'</option>'; 

			}

			echo $html;

		}

		

		public function HsnWiseSale()

		{

			if (!has_permission_new('HsnWiseSale', '', 'view')) {

				access_denied('Invoice Items');

			}

			$data['title'] = "HSN Wise Sales";

			$data['company_detail'] = $this->ItemModel->get_company_detail();

			$data['centermaster'] = $this->ItemModel->GetOrderPunchCenterList(); 

			$this->load->view('admin/ItemMaster/HsnWiseSale',$data);

		}

		

		public function GetHsnWiseReport()

		{

			$filterdata = array(

            'from_date' => $this->input->post('from_date'),

            'to_date' => $this->input->post('to_date'),

            'CenterID'=>$this->input->post('CenterID'),

			);

			$HSN_data = $this->ItemModel->get_data_for_HSN($filterdata);

			$HSNMaster = $this->ItemModel->getHsnMaster($filterdata);

			$SRT_HSN = $this->ItemModel->GetSRT_HSN($filterdata);

			$CD_HSN = $this->ItemModel->GetCD_HSN($filterdata);

			

			$HSN_dataSRT = $this->ItemModel->get_data_for_HSNSRT($filterdata);

			$HSN_dataCD = $this->ItemModel->get_data_for_HSNCD($filterdata);

			$HSN_dataDD = $this->ItemModel->get_data_for_HSNDD($filterdata);

			// echo "<pre>";print_r($HSN_data);die;

			

			$html = '';

			$html .= '<thead>';

			$html .= '<tr>';

			$html .= '<th style="text-align:left;">Sr No.</th>';

			$html .= '<th style="text-align:left;">HSN</th>';

			$html .= '<th style="text-align:left;">Description</th>';

			$html .= '<th style="text-align:left;">UQC</th>';

			$html .= '<th style="text-align:left;">Total Qty</th>';

			$html .= '<th style="text-align:left;">Total Value</th>';

			$html .= '<th style="text-align:left;">Taxable Value</th>';

			$html .= '<th style="text-align:left;">	Integrated Tax</th>';

			$html .= '<th style="text-align:left;">Central Tax</th>';

			$html .= '<th style="text-align:left;">State/UT Tax</th>';

			$html .= '<th style="text-align:left;">Cess Amount</th>';  

			$html .= '<th style="text-align:left;">GST%</th>';                                                     

			$html .= '</tr>';

			$html .= '</thead>';

			$html .= '<tbody id="filter_data_table">';

			$srNo7 = 001;

			$BillQtyTotal = 0.00;

			$billAmtTotal = 0.00;

			$taxAmtTotal = 0.00;

			$ISUMTotal = 0.00;

			$CSUMTotal = 0.00;

			$SSUMTotal = 0.00;

			

			$HSNList = array();

			$HSNTaxrate = array();

			foreach ($HSN_data as $hsnkey => $hsnvalue) {

				if($hsnvalue["hsn_code"] !== ''){

					array_push($HSNList,$hsnvalue["hsn_code"]);

					$tax = $hsnvalue["igst"] + $hsnvalue["sgst"] + $hsnvalue["cgst"];

					array_push($HSNTaxrate,$tax);

				}

			}

			

			foreach ($CD_HSN as $hsnkey1 => $hsnvalue1) {

				if($hsnvalue1["hsncode"] !== ''){

					array_push($HSNList,$hsnvalue1["hsncode"]);

					$tax = $hsnvalue1["igst"] + $hsnvalue1["sgst"] + $hsnvalue1["cgst"];

					array_push($HSNTaxrate,$tax);

				}

			}

			

			foreach ($SRT_HSN as $hsnkey2 => $hsnvalue2) {

				if($hsnvalue2["hsn_code"] !== ''){

					array_push($HSNList,$hsnvalue2["hsn_code"]);

					$tax = $hsnvalue2["igst"] + $hsnvalue2["sgst"] + $hsnvalue2["cgst"];

					array_push($HSNTaxrate,$tax);

				}

			}

			

			$HSNList = array_unique($HSNList);

			$HSNTaxrate = array_unique($HSNTaxrate);

			// echo "<pre>";

			// print_r($HSNTaxrate);

			// die;

			foreach ($HSNList as $hsnCode) {

				$hsnDesc = "";

				foreach ($HSNMaster as $master) {

					if($hsnCode == $master["name"]){

						$hsnDesc = $master["hsndesc"];

					}

				}

				foreach ($HSNTaxrate as $hsnTax) {

					$match = 0;

					$BillQty = 0.00;

					$billAmt = 0.00;

					$taxAmt = 0.00;

					$ISUM = 0.00;

					$CSUM = 0.00;

					$SSUM = 0.00;

					foreach ($HSN_data as $key7 => $value7) {

						$gstPer = $value7["igst"] + $value7["sgst"] + $value7["cgst"];

						if($value7['hsn_code'] == $hsnCode && $hsnTax == $gstPer){

							$BillQty += $value7["BilledQtySum"];

							$billAmt += $value7["BillAmt"];

							

							

							if($value7['cgst'] > 0){

								$CSUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['cgst']/100));

							}

							if($value7['sgst'] > 0){

								$SSUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['sgst']/100));

							}

							if($value7['igst'] > 0){

								$ISUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['igst']/100));

							}

							

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $value7["TaxableAmt"] - $GSTAmt;

							$taxAmt += $TaxableAmt;

							// $CSUM += $value7["CGSTSUM"];

							// $ISUM += $value7["IGSTSUM"];

							// $SSUM += $value7["SGSTSUM"];

							$match = 1;

						}

					}

					// Minus SRT values    

					foreach ($HSN_dataSRT as $keySRT => $valueSRT) {

						$gstPer2 = $valueSRT["igst"] + $valueSRT["sgst"] + $valueSRT["cgst"];

						if($valueSRT['hsn_code'] == $hsnCode && $hsnTax == $gstPer2){

							$BillQty -= $valueSRT["BilledQtySum"];

							$billAmt -= $valueSRT["BillAmt"];

							if($valueSRT['cgst'] > 0){

								$CSUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['cgst']/100));

							}

							if($valueSRT['sgst'] > 0){

								$SSUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['sgst']/100));

							}

							if($valueSRT['igst'] > 0){

								$ISUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueSRT["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

							// $ISUM -= $valueSRT["IGSTSUM"];

							// $CSUM -= $valueSRT["CGSTSUM"];

							// $SSUM -= $valueSRT["SGSTSUM"];

							$match = 1;

						}

					}

					// Minus Credit value values    

					foreach ($HSN_dataCD as $keyCD => $valueCD) {

						$gstPer3 = $valueCD["igst"] + $valueCD["sgst"] + $valueCD["cgst"];

						if($valueCD['hsncode'] == $hsnCode && $hsnTax == $gstPer3 && $valueCD["SalesID"] != NULL){

                            $BillQty -= $valueCD["BilledQtySum"];

                            $billAmt -= $valueCD["BillAmt"];

							if($valueCD['cgst'] > 0){

								$CSUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['cgst']/100));

							}

							if($valueCD['sgst'] > 0){

								$SSUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['sgst']/100));

							}

							if($valueCD['igst'] > 0){

								$ISUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueCD["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

                            // $ISUM -= $valueCD["IGSTSUM"];

                            // $CSUM -= $valueCD["CGSTSUM"];

                            // $SSUM -= $valueCD["SGSTSUM"];

                            $match = 1;

						}

					}

					// ADD Debit value values    

					foreach ($HSN_dataDD as $keyDD => $valueDD) {

						$gstPer3 = $valueDD["igst"] + $valueDD["sgst"] + $valueDD["cgst"];

						if($valueDD['hsncode'] == $hsnCode && $hsnTax == $gstPer3 && $valueDD["SalesID"] != NULL){

                            $BillQty += $valueDD["BilledQtySum"];

                            $billAmt += $valueDD["BillAmt"];

							if($valueDD['cgst'] > 0){

								$CSUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['cgst']/100));

							}

							if($valueDD['sgst'] > 0){

								$SSUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['sgst']/100));

							}

							if($valueDD['igst'] > 0){

								$ISUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueDD["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

                            // $ISUM += $valueDD["IGSTSUM"];

                            // $CSUM += $valueDD["CGSTSUM"];

                            // $SSUM += $valueDD["SGSTSUM"];

                            $match = 1;

						}

					}

					if($match == "1"){

						$html .= '<tr>'; 

						$html .= '<td align="center">'.$srNo7.'</td>'; 

						$html .= '<td align="center">'.$hsnCode.'</td>'; 

						$html .= '<td>'.$hsnDesc.'</td>'; 

						$html .= '<td align="center">PCS-PIECES</td>'; 

						$html .= '<td align="right">'.number_format($BillQty,2).'</td>'; 

						$BillQtyTotal += $BillQty;

						$html .= '<td align="right">'.number_format($billAmt,2).'</td>'; 

						$billAmtTotal += $billAmt;

						$html .= '<td align="right">'.number_format($taxAmt,2).'</td>'; 

						$taxAmtTotal += $taxAmt;

						$html .= '<td align="right">'.number_format($ISUM,2).'</td>'; 

						$ISUMTotal += $ISUM;

						$html .= '<td align="right">'.number_format($CSUM,2).'</td>'; 

						$CSUMTotal += $CSUM;

						$html .= '<td align="right">'.number_format($SSUM,2).'</td>'; 

						$SSUMTotal += $SSUM;

						$html .= '<td></td>'; 

						$html .= '<td align="center">'.number_format($hsnTax,2).'</td>'; 

						$html .= '<tr>'; 

						$srNo7++;

					}

				}

			}

			

			$html .= '<tr>';

			$html .= '<td></td>';

			$html .= '<td>Total</td>';

			$html .= '<td></td>';

			$html .= '<td></td>';

			$html .= '<td align="right">'.number_format($BillQtyTotal,2).'</td>';

			$html .= '<td align="right">'.number_format($billAmtTotal,2).'</td>';

			$html .= '<td align="right">'.number_format($taxAmtTotal,2).'</td>';

			$html .= '<td align="right">'.number_format($ISUMTotal,2).'</td>';

			$html .= '<td align="right">'.number_format($CSUMTotal,2).'</td>';

			$html .= '<td align="right">'.number_format($SSUMTotal,2).'</td>';

			$html .= '<td></td>';

			$html .= '<td></td>';

			$html .= '</tr>';

			$html .= '</tbody>';

			

			echo $html;

		}

		

		public function export_HsnWiseReport()

		{

			if (!has_permission_new('OrderList', '', 'export')) {

				access_denied('Invoice Items');

			}

			if (!class_exists('XLSXReader_fin')) {

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if ($this->input->post()) 

			{

				$company_detail = $this->ItemModel->get_company_detail();

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date' => $this->input->post('to_date'),

				'CenterID'=>$this->input->post('CenterID'),

				);

				$HSN_data = $this->ItemModel->get_data_for_HSN($filterdata);

				$HSNMaster = $this->ItemModel->getHsnMaster($filterdata);

				$SRT_HSN = $this->ItemModel->GetSRT_HSN($filterdata);

				$CD_HSN = $this->ItemModel->GetCD_HSN($filterdata);

				

				$HSN_dataSRT = $this->ItemModel->get_data_for_HSNSRT($filterdata);

				$HSN_dataCD = $this->ItemModel->get_data_for_HSNCD($filterdata);

				$HSN_dataDD = $this->ItemModel->get_data_for_HSNDD($filterdata);

				

				$writer = new XLSXWriter();

				

				$company_name = array($company_detail->company_name);

				

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  

				

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $company_detail->address;

				

				$center_addr = array($address, );	  

				

				$filters = "From date: " . $this->input->post('from_date') . ", To date: " . $this->input->post('to_date');

				

				$filter_row = array($filters);

				

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells

				

				$writer->writeSheetRow('Sheet1', $center_addr);

				

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 13);  //merge cells	   

				

				$writer->writeSheetRow('Sheet1', $filter_row);

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] = 'Sr No.';

				$set_col_tk["HSN"] = 'HSN';

				$set_col_tk["Description"] = 'Description';

				$set_col_tk["UQC"] = 'UQC';

				$set_col_tk["Total Qty"] = 'Total Qty';

				$set_col_tk["Total Value"] = 'Total Value';

				$set_col_tk["Taxable Value"] = 'Taxable Value';

				$set_col_tk["Integrated Tax"] = 'Integrated Tax';

				$set_col_tk["Central Tax"] = 'Central Tax';

				$set_col_tk["State/UT Tax"] = 'State/UT Tax';

				$set_col_tk["Cess Amount"] = 'Cess Amount';

				$set_col_tk["GST%"] = 'GST%';

				

				$writer_header = $set_col_tk;

				

				$writer->writeSheetRow('Sheet1', $writer_header);

				

				

				

				

				

				$srNo7 = 001;

				$BillQtyTotal = 0.00;

				$billAmtTotal = 0.00;

				$taxAmtTotal = 0.00;

				$ISUMTotal = 0.00;

				$CSUMTotal = 0.00;

				$SSUMTotal = 0.00;

				

				$HSNList = array();

				$HSNTaxrate = array();

				foreach ($HSN_data as $hsnkey => $hsnvalue) {

					if($hsnvalue["hsn_code"] !== ''){

						array_push($HSNList,$hsnvalue["hsn_code"]);

						$tax = $hsnvalue["igst"] + $hsnvalue["sgst"] + $hsnvalue["cgst"];

						array_push($HSNTaxrate,$tax);

					}

				}

				

				foreach ($CD_HSN as $hsnkey1 => $hsnvalue1) {

					if($hsnvalue1["hsncode"] !== ''){

						array_push($HSNList,$hsnvalue1["hsncode"]);

						$tax = $hsnvalue1["igst"] + $hsnvalue1["sgst"] + $hsnvalue1["cgst"];

						array_push($HSNTaxrate,$tax);

					}

				}

				

				foreach ($SRT_HSN as $hsnkey2 => $hsnvalue2) {

					if($hsnvalue2["hsn_code"] !== ''){

						array_push($HSNList,$hsnvalue2["hsn_code"]);

						$tax = $hsnvalue2["igst"] + $hsnvalue2["sgst"] + $hsnvalue2["cgst"];

						array_push($HSNTaxrate,$tax);

					}

				}

				

				$HSNList = array_unique($HSNList);

				$HSNTaxrate = array_unique($HSNTaxrate);

				//echo "<pre>";

				//print_r($SRT_HSN);

				//die;

				foreach ($HSNList as $hsnCode) {

					$hsnDesc = "";

					foreach ($HSNMaster as $master) {

						if($hsnCode == $master["name"]){

							$hsnDesc = $master["hsndesc"];

						}

					}

					foreach ($HSNTaxrate as $hsnTax) {

						$match = 0;

						$BillQty = 0.00;

						$billAmt = 0.00;

						$taxAmt = 0.00;

						$ISUM = 0.00;

						$CSUM = 0.00;

						$SSUM = 0.00;

						foreach ($HSN_data as $key7 => $value7) {

						$gstPer = $value7["igst"] + $value7["sgst"] + $value7["cgst"];

						if($value7['hsn_code'] == $hsnCode && $hsnTax == $gstPer){

							$BillQty += $value7["BilledQtySum"];

							$billAmt += $value7["BillAmt"];

							

							

							if($value7['cgst'] > 0){

								$CSUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['cgst']/100));

							}

							if($value7['sgst'] > 0){

								$SSUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['sgst']/100));

							}

							if($value7['igst'] > 0){

								$ISUM += $value7["BillAmt"] - ($value7["BillAmt"] / (1 + $value7['igst']/100));

							}

							

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $value7["TaxableAmt"] - $GSTAmt;

							$taxAmt += $TaxableAmt;

							// $CSUM += $value7["CGSTSUM"];

							// $ISUM += $value7["IGSTSUM"];

							// $SSUM += $value7["SGSTSUM"];

							$match = 1;

						}

					}

					// Minus SRT values    

					foreach ($HSN_dataSRT as $keySRT => $valueSRT) {

						$gstPer2 = $valueSRT["igst"] + $valueSRT["sgst"] + $valueSRT["cgst"];

						if($valueSRT['hsn_code'] == $hsnCode && $hsnTax == $gstPer2){

							$BillQty -= $valueSRT["BilledQtySum"];

							$billAmt -= $valueSRT["BillAmt"];

							if($valueSRT['cgst'] > 0){

								$CSUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['cgst']/100));

							}

							if($valueSRT['sgst'] > 0){

								$SSUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['sgst']/100));

							}

							if($valueSRT['igst'] > 0){

								$ISUM -= $valueSRT["BillAmt"] - ($valueSRT["BillAmt"] / (1 + $valueSRT['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueSRT["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

							// $ISUM -= $valueSRT["IGSTSUM"];

							// $CSUM -= $valueSRT["CGSTSUM"];

							// $SSUM -= $valueSRT["SGSTSUM"];

							$match = 1;

						}

					}

					// Minus Credit value values    

					foreach ($HSN_dataCD as $keyCD => $valueCD) {

						$gstPer3 = $valueCD["igst"] + $valueCD["sgst"] + $valueCD["cgst"];

						if($valueCD['hsncode'] == $hsnCode && $hsnTax == $gstPer3 && $valueCD["SalesID"] != NULL){

                            $BillQty -= $valueCD["BilledQtySum"];

                            $billAmt -= $valueCD["BillAmt"];

							if($valueCD['cgst'] > 0){

								$CSUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['cgst']/100));

							}

							if($valueCD['sgst'] > 0){

								$SSUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['sgst']/100));

							}

							if($valueCD['igst'] > 0){

								$ISUM -= $valueCD["BillAmt"] - ($valueCD["BillAmt"] / (1 + $valueCD['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueCD["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

                            // $ISUM -= $valueCD["IGSTSUM"];

                            // $CSUM -= $valueCD["CGSTSUM"];

                            // $SSUM -= $valueCD["SGSTSUM"];

                            $match = 1;

						}

					}

					// ADD Debit value values    

					foreach ($HSN_dataDD as $keyDD => $valueDD) {

						$gstPer3 = $valueDD["igst"] + $valueDD["sgst"] + $valueDD["cgst"];

						if($valueDD['hsncode'] == $hsnCode && $hsnTax == $gstPer3 && $valueDD["SalesID"] != NULL){

                            $BillQty += $valueDD["BilledQtySum"];

                            $billAmt += $valueDD["BillAmt"];

							if($valueDD['cgst'] > 0){

								$CSUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['cgst']/100));

							}

							if($valueDD['sgst'] > 0){

								$SSUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['sgst']/100));

							}

							if($valueDD['igst'] > 0){

								$ISUM += $valueDD["BillAmt"] - ($valueDD["BillAmt"] / (1 + $valueDD['igst']/100));

							}

							$GSTAmt = $CSUM + $SSUM + $ISUM;

							$TaxableAmt = $valueDD["TaxableAmt"] - $GSTAmt;

							$taxAmt -= $TaxableAmt;

                            // $ISUM += $valueDD["IGSTSUM"];

                            // $CSUM += $valueDD["CGSTSUM"];

                            // $SSUM += $valueDD["SGSTSUM"];

                            $match = 1;

						}

					}

						if($match == "1"){

							$list_add = [];  

							$list_add[] = $srNo7;

							$list_add[] = $hsnCode;

							$list_add[] = $hsnDesc;

							$list_add[] = 'PCS-PIECES';

							$list_add[] = number_format($BillQty,2);

							$list_add[] = number_format($billAmt,2);

							$list_add[] = number_format($taxAmt,2);

							$list_add[] = number_format($ISUM,2);

							$list_add[] = number_format($CSUM,2);

							$list_add[] = number_format($SSUM,2);

							$list_add[] = '';

							$list_add[] = number_format($hsnTax,2);

							

							$BillQtyTotal += $BillQty;

							$billAmtTotal += $billAmt; 

							$taxAmtTotal += $taxAmt;

							$ISUMTotal += $ISUM;

							$CSUMTotal += $CSUM;

							$SSUMTotal += $SSUM;

							$srNo7++;

							

							

							$writer->writeSheetRow('Sheet1', $list_add); 

						}

					}

				}

				

				

				$sum_row = [];

					$sum_row[] = ''; 

					$sum_row[] = '';

					$sum_row[] = ''; 

					$sum_row[] = 'Total'; 

					$sum_row[] = number_format($BillQtyTotal, 2, '.', '');       

					$sum_row[] = number_format($billAmtTotal, 2, '.', '');       

					$sum_row[] = number_format($taxAmtTotal, 2, '.', '');    

					$sum_row[] = number_format($ISUMTotal, 2, '.', '');   

					$sum_row[] = number_format($CSUMTotal, 2, '.', '');   

					$sum_row[] = number_format($SSUMTotal, 2, '.', '');  

					$sum_row[] = '';

					$sum_row[] = '';  

				

				

				$writer->writeSheetRow('Sheet1', $sum_row);

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

				foreach ($files as $file) {

					if (is_file($file)) {

						unlink($file);

					}

				}

				$filename = 'HsnWiseReport.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

				echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

				]);

				die;

			}

		}

	}				