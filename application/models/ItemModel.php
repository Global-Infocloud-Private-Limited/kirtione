<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class ItemModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		//================== Get Next ItemID ===========================================
		public function GetNextItemID()
		{
			$this->db->select('tbloptions.*');
			$this->db->where('tbloptions.name',"next_product_id");
			return $this->db->get('tbloptions')->row();
		}
		//================== Get Kirti One Access Account List =========================
		public function GetKirtiOneAccessList()
		{
			$this->db->select('tblclients.*');
			$this->db->where('tblclients.IsKirtiOneAccess',"Y");
			$this->db->order_by('tblclients.active','1');
			return $this->db->get('tblclients')->result_array();
		}
		//================== Get Item Sub Category List ================================
		public function GetItemSubCategory()
		{
			$this->db->select('tblK1ItemSubCategory.*');
			//$this->db->order_by('tblclients.active','1');
			return $this->db->get('tblK1ItemSubCategory')->result_array();
		}
		
		//================== Add New Rate ItemWise =====================================
		public function AddItemWiseSaleRate($data)
		{
			$CenterList = $data["CenterID"];
			unset($data["CenterID"]);
			$ItemID = $data["ItemID"];
			$new_salerate = $data["new_salerate"];
			$new_basicrate = $data["new_basicrate"];
			$new_discAmt = $data["new_discAmt"];
			$taxrate = $data["taxrate"];
			$PartyID = $data["PartyID"];
			$UserID = $data["UserID"];
			$count = 0;
			$GetAllRateByItem = $this->GetAllRateByItemID($ItemID);
			foreach($CenterList as $CenterID){
				$Exist = 0;
				$DisAmt = "";
				foreach($GetAllRateByItem as $key=>$val){
					if($val["itemID"] = $ItemID && $val["CenterID"] == $CenterID){
						$Exist++;
						$DisAmt = $val["disc_amt"];
						// Move Record to History table
						$ex_data = array(
	                    "PartyID"=>$val["PartyID"],
	                    "ItemID"=>$val["ItemID"],
	                    "CenterID"=>$val["CenterID"],
	                    "taxrate"=>$val["taxrate"],
	                    "sale_rate"=>$val["sale_rate"],
	                    "basic_rate"=>$val["basic_rate"],
	                    "disc_amt"=>$val["disc_amt"],
	                    "UserID"=>$val["UserID"],
	                    "TransDate"=>$val["TransDate"],
						);
						$this->db->insert('tblK1RateMasterHistory',$ex_data);
					}
				}
				if($Exist > 0){
					// Delete Existing record
					$this->db->where('tblK1RateMaster.ItemID',$ItemID);
					$this->db->where('tblK1RateMaster.CenterID',$CenterID);
					$this->db->delete('tblK1RateMaster');
				}
				$insert_array = array(
	            "ItemID"=>$ItemID,
	            "PartyID"=>$PartyID,
	            "CenterID"=>$CenterID,
	            "taxrate"=>$taxrate,
	            "sale_rate"=>$new_salerate,
	            "basic_rate"=>$new_basicrate,
	            "UserID"=>$UserID,
	            "TransDate"=>date('Y-m-d H:i:s')
				);
				if($new_discAmt == ""){
					$insert_array["disc_amt"] = $DisAmt;
					}else{
					$insert_array["disc_amt"] = $new_discAmt;
				}
				if($this->db->insert('tblK1RateMaster',$insert_array)){
					$count++;
				}
			}
			return $count;
		}
		//========================= Get All rate list By ItemID ========================
		public function GetAllRateByItemID($ItemID)
		{
			$this->db->select('tblK1RateMaster.*');
			$this->db->where('tblK1RateMaster.ItemID',$ItemID);
			return $this->db->get('tblK1RateMaster')->result_array();
		}
		//========================= Get Rate Assigned Center List ========================
		public function GetRateAvailableCenter($data)
		{
			$this->db->select('tblK1RateMaster.*,tblCenterMaster.CenterName');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1RateMaster.CenterID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1RateMaster.ItemID');
			if($data["ItemSubCat"]){
				$this->db->where('tblproduct.Subcategory',$data["ItemSubCat"]);
			}
			if($data["ItemID"]){
				$this->db->where('tblproduct.ProductID',$data["ItemID"]);
			}
			$this->db->where('tblproduct.ItemFor',$data["PartyID"]);
			$this->db->group_by('tblK1RateMaster.CenterID');
			return $this->db->get('tblK1RateMaster')->result_array();
		}
		//========================= Get Active Center List =============================
		public function GetActiveCenterList()
		{
			$this->db->select('tblCenterMaster.*');
			$this->db->where('tblCenterMaster.status',"Y");
			return $this->db->get('tblCenterMaster')->result_array();
		}
		//================== Get All Item Rate List ====================================
		public function GetAllItemRateList($data)
		{
			$this->db->select('tblK1RateMaster.*');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1RateMaster.ItemID');
			if($data["ItemSubCat"]){
				$this->db->where('tblproduct.Subcategory',$data["ItemSubCat"]);
			}
			if($data["ItemID"]){
				$this->db->where('tblproduct.ProductID',$data["ItemID"]);
			}
			if($data["CenterID"]){
				$this->db->where_in('tblK1RateMaster.CenterID',$data["CenterID"]);
			}
			$this->db->where('tblproduct.ItemFor',$data["PartyID"]);
			return $this->db->get('tblK1RateMaster')->result_array();
		}
		//================== Get All Item List By SubGroup And ItemID ==================
		public function GetAllItemListBySubGroup($data)
		{
			$this->db->select('tblproduct.*');
			if($data["ItemSubCat"]){
				$this->db->where('tblproduct.Subcategory',$data["ItemSubCat"]);
			}
			if($data["ItemID"]){
				$this->db->where('tblproduct.ProductID',$data["ItemID"]);
			}
			$this->db->where('tblproduct.ItemFor',$data["PartyID"]);
			return $this->db->get('tblproduct')->result_array();
		}
		//================== Get Item List By Item Sub Category ========================
		public function GetItemListByCategory($ItemSubCat,$PartyID)
		{
			$this->db->select('tblproduct.*');
			$this->db->where('tblproduct.ItemFor',$PartyID);
			$this->db->where('tblproduct.Subcategory',$ItemSubCat);
			return $this->db->get('tblproduct')->result_array();
		}
		//================ Get Item List By Item Main Category Type ====================
		public function GetCategoryWiseItemList($CategoryType)
		{
			$this->db->select('tblproduct.*');
			$this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
			$this->db->where('tblK1ItemCategory.IsGrocery',$CategoryType);
			return $this->db->get('tblproduct')->result_array();
		}
		//==================== Get Kirti One Item List =================================
		public function GetItemList($LoginAccountID = "", $isactive)
		{ 
			$this->db->select('tblproduct.*,tblK1ItemCategory.SubcategoryName,tblK1ItemSubCategory.SubCategoryName as SubcateName,tblbrands.BrandName,tbltaxes.taxrate');	
	   		
			if($LoginAccountID){				
				$this->db->where('tblproduct.ItemFor',$LoginAccountID);			
			}
			$this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory',"LEFT");
			$this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category',"LEFT");
			$this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId');
			$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
			if (!empty($isactive)) {
				$this->db->where('tblproduct.isactive', $isactive);
			}
			$this->db->order_by('tblproduct.id','DESC');
			return $this->db->get('tblproduct')->result_array();
		}
		public function GetItemListadminproduct($isactive = null, $ItemFor = null, $LoginAccountID = "")
		{ 
			$this->db->select('
			tblproduct.*, 
			tblK1ItemCategory.SubcategoryName,
			tblK1ItemSubCategory.SubCategoryName as SubcateName,
			tblbrands.BrandName,
			tbltaxes.taxrate
			');	
			
			$this->db->from('tblproduct');
			$this->db->join('tblK1ItemSubCategory', 'tblK1ItemSubCategory.id = tblproduct.Subcategory', "LEFT");
			$this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category', "LEFT");
			$this->db->join('tblbrands', 'tblbrands.id = tblproduct.BrandId', "LEFT");
			$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst', "LEFT");
			
			// Apply filters
			if (!empty($isactive)) {
				$this->db->where('tblproduct.isactive', $isactive);
			}
			
			if (!empty($LoginAccountID)) {
				$this->db->where('tblproduct.ItemFor', $LoginAccountID);
				} elseif (!empty($ItemFor)) {
				$this->db->where('tblproduct.ItemFor', $ItemFor);
			}
			
			$this->db->order_by('tblproduct.id', 'DESC');
			
			return $this->db->get()->result_array();
		}
		
		public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function insert_data($tbl,$data) 
		{      
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
		}
		
		public function insert_multivandor_data($tbl,$data)
		{
			$this->db->insert($tbl, $data);
			return $this->db->insert_id();
		}
		
		public function get_company_detail()
		{
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		//=================  Get Item Details By ProductID =============================
		public function GetProductDetailsbyProductID($ItemID)
		{
			$this->db->select('tblproduct.*,tbltaxes.taxrate');
			$this->db->from('tblproduct');
			$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst', "LEFT");
			$this->db->where("tblproduct.ProductID", $ItemID);
			$query = $this->db->get();
			$result =  $query->row_array();
			return $result;
		}
		public function get_data_ItemMaster($id)
		{
			
			$this->db->select('tblproduct.*,tbltaxes.taxrate');
			$this->db->from('tblproduct');
			$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst', "LEFT");
			$this->db->where("tblproduct.id", $id);
			$query = $this->db->get();
			$result =  $query->row_array();
			if($result){
				$vendorList = array();
				$this->db->select('tblk1ItemVendor.*');
				$this->db->from('tblk1ItemVendor');
				$this->db->where("tblk1ItemVendor.ItemID", $result["ProductID"]);
				$query = $this->db->get();
				$vendordata =  $query->result_array();
				foreach($vendordata as $val){
					array_push($vendorList,$val["VendorID"]);
				}
				$result["VendorFor"] = $vendorList;
			}
			return $result;
		}
		
		public function get_data($tbl,$where)
    	{
    		$this->db->select('*');
    		$this->db->from($tbl);
    		$this->db->where($where);
    		$query = $this->db->get();
    		return $query->row_array();
		}
		// public function get_data($id)
		// {
		// $this->db->select('*');
		// $this->db->from($tbl);	
		// $this->db->where($where);
		// $this->db->join($tbl, "tblk1ItemVendor.ItemID = {$tbl}.ProductID", 'left');
		// $query = $this->db->get();
		// return $query->row_array();
		// }
		
		public function edit_data($tbl,$where,$arr) 
		{
			$this->db->where($where);
			if ($this->db->update($tbl, $arr)) {
				return TRUE;
				} else {
				return FALSE;
			}
		}
		public function edit_multivandor_data($ItemID, $arr) 
		{
			$this->db->where('ItemID', $ItemID);
			$result = $this->db->update('tblk1ItemVendor', $arr);
			
			return $result;
		}
		//===================== Get All Order Punch Party List =========================
		public function GetOrderPartyList()
		{
		    $UserID = $this->session->userdata('username');
			$this->db->select('tblK1ordermaster.OrderID,tblclients.company,tblclients.AccountID');	
			$this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID');
			if(!is_admin()){
			    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1ordermaster.CenterID');
		        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
			}
			$this->db->where('tblK1ordermaster.OrderID IS NOT NULL');
			$this->db->group_by('tblclients.AccountID');
			return $this->db->get('tblK1ordermaster')->result_array();
		}
		//===================== Get All Order Punch Center List =========================
		public function GetOrderPunchCenterList($LogInUser = "")
		{
			$this->db->select('tblK1ordermaster.OrderID,tblCenterMaster.CenterName,tblCenterMaster.CenterID');	
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
			$this->db->where('tblK1ordermaster.OrderID IS NOT NULL');
			if($LogInUser){
				$this->db->where('tblK1ordermaster.AccountID',$LogInUser);
			}
			$this->db->group_by('tblCenterMaster.CenterID');
			return $this->db->get('tblK1ordermaster')->result_array();
		}
		//===================== Get All Order Punch Item List =========================
		public function GetOrderPunchItemList($LogInUser = "")
		{
			$UserID = $this->session->userdata('username');
			$this->db->select('tblK1history.ItemID,tblproduct.ProductName,tblproduct.ProductID');	
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			if(!is_admin()){
				$this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
				$this->db->where('tblstaff_wise_center.AccountID', $UserID);
			}
			$this->db->group_by('tblK1history.ItemID');
			return $this->db->get('tblK1history')->result_array();
		}
		//========================== Get Sale Order List ===============================
		public function getItemOrderDetailsDB($data)
		{
		    $fy = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$from_date = to_sql_date($data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			if($data["Report_type"] == "1")
			{   
				$this->db->select('tblK1ordermaster.*,tblK1salesmaster.ChallanID,tblK1salesmaster.OtherAmt,tblK1salesmaster.GSTIN AS PartyGST,
				tblK1salesmaster.CashAmt,tblK1salesmaster.OnlineAmt,tblK1salesmaster.PartyBillNo,
				tblCenterMaster.CenterName,tblCenterMaster.GSTNo,tblclients.company, tblK1salesmaster.Transdate AS InvoiceDate, tblK1salesmaster.SalesID AS InvoiceNo');
				$this->db->join('tblK1salesmaster', 'tblK1salesmaster.OrderID = tblK1ordermaster.OrderID');
				if(!is_admin()){
					$this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1ordermaster.CenterID');
					$this->db->where('tblstaff_wise_center.AccountID', $UserID);
				}
				$this->db->where('tblK1ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if(!empty($data['order_status'])){
				    
					$this->db->where('tblK1ordermaster.OrderStatus',$data['order_status']);
				}	
				if(!empty($data['AccountID'])){
					$this->db->where('tblK1ordermaster.AccountID',$data['AccountID']);
				}			
				if(!empty($data['CenterID'])){				
					$this->db->where('tblK1ordermaster.CenterID',$data['CenterID']);			
				}
				if(!empty($data['CategoryType'])){	
					if($data['CategoryType'] == "Y"){
						$CategoryType = "Grocery";
					}else{
						$CategoryType = "Non Grocery";
					}
					$this->db->where('tblK1ordermaster.CategoryType',$CategoryType);			
				}
				if(!empty($data['SaleType'])){	
					$this->db->where('tblK1ordermaster.IsDirectSale',$data['SaleType']);			
				}
				if(!empty($data['OrderType'])){	
					$this->db->where('tblK1ordermaster.OrderPaymentType',$data['OrderType']);			
				}
				$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
				$this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID');
				$this->db->where('tblK1ordermaster.OrderID IS NOT NULL');
				$this->db->order_by('tblK1ordermaster.OrderID','DESC');
				$this->db->where('tblK1ordermaster.FY',$fy);
				return $this->db->get('tblK1ordermaster')->result_array();
				//echo $this->db->last_query();
				//die;
			}else
			{
				// echo "ok";die;
				$this->db->select('tblK1history.*,(tblK1history.BasicRate * tblK1history.BilledQty) AS ItemTotalAmt,
				tblK1salesmaster.ChallanID,tblK1ordermaster.OrderStatus,tblK1ordermaster.BIllNo,
				tblK1ordermaster.IsDirectSale,tblK1ordermaster.OrderPaymentType,tblK1salesmaster.GSTIN AS PartyGST,tblK1salesmaster.PartyBillNo,
				tblK1ordermaster.SalesID,tblCenterMaster.CenterName,tblCenterMaster.GSTNo,
				tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit,tblproduct.PackingQty,tbltaxes.taxrate,
				tblclients.company,tblclients.state,tblCenterMaster.state As CenterState');	
				if(!is_admin()){
					$this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1history.CenterID');
					$this->db->where('tblstaff_wise_center.AccountID', $UserID);
				}
				if(!empty($data['OrderIDs'])){
					$this->db->where_in('tblK1ordermaster.OrderID', $data['OrderIDs']);
				}else{
					$this->db->where('tblK1history.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				}
				// $this->db->where('tblK1history.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if(!empty($data['order_status'])){
					$this->db->where('tblK1ordermaster.OrderStatus',$data['order_status']);
				}	
				if(!empty($data['AccountID'])){
					$this->db->where('tblK1ordermaster.AccountID',$data['AccountID']);
				}			
				if(!empty($data['CenterID'])){				
					$this->db->where('tblK1ordermaster.CenterID',$data['CenterID']);			
				}
				if(!empty($data['ItemID'])){				
					$this->db->where('tblK1history.ItemID',$data['ItemID']);			
				}
				if(!empty($data['CategoryType'])){				
					$this->db->where('tblK1ItemCategory.IsGrocery',$data['CategoryType']);			
				}
				if(!empty($data['SaleType'])){	
					$this->db->where('tblK1ordermaster.IsDirectSale',$data['SaleType']);			
				}
				if(!empty($data['OrderType'])){	
					$this->db->where('tblK1ordermaster.OrderPaymentType',$data['OrderType']);			
				}
				
				$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
				$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst');
				$this->db->join('tblK1ItemCategory', 'tblK1ItemCategory.id = tblproduct.Category');
				$this->db->join('tblK1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
				$this->db->join('tblK1salesmaster', 'tblK1salesmaster.OrderID = tblK1ordermaster.OrderID');
				$this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID');
				$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
				$this->db->where('tblK1ordermaster.OrderID IS NOT NULL');
				$this->db->where('tblK1history.BillID IS NOT NULL');
				$this->db->where('tblK1history.TransID IS NOT NULL');
				$this->db->where('tblK1history.FY',$fy);
				$this->db->where('tblK1history.PartyID',"KASPL");
				$this->db->order_by('tblK1ordermaster.OrderID','DESC');
				return $this->db->get('tblK1history')->result_array();
			}
		}
		
		public function get_max_pay_id()
        {         
            $this->db->select_max('id'); 
            $query = $this->db->get('tblpaymentmethod');        
			
            $result = $query->row(); 
            return $result ? $result->id : 0;
		}
		
		public function get_all_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function get_data_for_account_bal($AccountID){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $finacial_year);    
			if(isset($AccountID)){				
				$this->db->where('AccountID', $AccountID);
			}			
			$accounts = $this->db->get(db_prefix().'accountbalances')->row();
			return $accounts;		
		}
		
		public function get_data_general_ledger2($AccountID)
		{
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$finacial_year = $this->session->userdata('finacial_year');
			
			$from_date = date('20'.$finacial_year.'-04-01');
			$to_date = date('Y-m-d');			
			
			$selected_company = $this->session->userdata('root_company');			
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $AccountID);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();        
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $AccountID);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				
				$this->db->select(db_prefix().'accountledger.*,tblclients.company');
				$this->db->join(db_prefix().'clients', db_prefix().'clients.AccountID = '.db_prefix().'accountledger.CounterAccount AND '.db_prefix().'clients.PlantID = '.db_prefix().'accountledger.PlantID ','LEFT');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);				
				$this->db->where(db_prefix().'accountledger.AccountID', $AccountID);				
				
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$from_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');				
				$this->db->order_by(db_prefix().'accountledger.Transdate', "asc");
				$query = $this->db->get(db_prefix().'accountledger')->result_array();
				
				return $query;
			}					
		}
		
		public function get_all_data_orderby($tablename, $orderBy = '',$where) 
		{
    		$this->db->select('*');
    		$this->db->from($tablename);      		
    		
			$this->db->where($where);
    		if ($orderBy != '') {
        		$this->db->order_by($orderBy);  
			}    
    		
    		$query = $this->db->get();
			
    		return $query->result_array();  
		}
		
		public function get_table_on_load_filter($data)
		{
            $from_date = to_sql_date($data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			
            $this->db->select('tblK1ordermaster.AccountID,tblK1ordermaster.OrderID,tblK1ordermaster.Transdate,tblCenterMaster.CenterName,tblclients.company,tblK1salesmaster.DiscAmt,tblK1salesmaster.SaleAmt,
			tblK1salesmaster.cgstamt,tblK1salesmaster.sgstamt,tblK1salesmaster.igstamt,tblK1salesmaster.BillAmt,tblK1ordermaster.OrderStatus');        
			
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
            
            $this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID');   
            
            $this->db->join('tblK1salesmaster', 'tblK1salesmaster.OrderID = tblK1ordermaster.OrderID');
			
            if(($data['from_date'] != '') || ($data['to_date'] != '')){
				$this->db->where('tblK1ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}		
			if(!empty($data['order_status'])){
				$this->db->where('tblK1ordermaster.OrderStatus',$data['order_status']);
			}	
			if(!empty($data['AccountID'])){
				$this->db->where('tblK1ordermaster.AccountID',$data['AccountID']);
			}			
			if(!empty($data['CenterID'])){				
				$this->db->where('tblK1ordermaster.CenterID',$data['CenterID']);			
			}
            $this->db->where('tblK1ordermaster.OrderID IS NOT NULL');
			$this->db->order_by('tblK1ordermaster.Transdate','ASC');
            $result = $this->db->get(db_prefix() . 'K1ordermaster')->result_array();
            foreach ($result as &$row) {
                if ($row['cgstamt'] != 0) {                  
                    $row['total_gst'] = $row['cgstamt'] + $row['sgstamt'];
					} else {                   
                    $row['total_gst'] = $row['igstamt'];
				}
				
				$date = $row["Transdate"];
                $datetime = new DateTime($date); 
                $formattedDate = $datetime->format('d/m/Y');  
                $row['orderdate'] = $formattedDate;
				
				if($row['OrderStatus'] == "O")
                {
                    $row['status'] = "Pending";
				}
                else if($row['OrderStatus'] == "C")
                {
                    $row['status'] = "Cancelled";
				}
                else if($row['OrderStatus'] == "F")
                {
                    $row['status'] = "Completed";
				}
				
				$row['customername'] = $row['company'] . ' (' . $row['AccountID'] . ')';
			}
            return $result;
			
		}
        public function GetPodetailsNumberwise($PoNumber)
    	{
    		$this->db->select('tblK1ordermaster.*,tblCenterMaster.CenterName,tblclients.company,SUM(tblK1history.OrderQty) AS TotalOrderQty,tblK1history.ItemID');
    		$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = tblK1ordermaster.CenterID');		
    		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1ordermaster.AccountID');		
    		$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1ordermaster.OrderID');				
    		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
    		$this->db->where('tblK1ordermaster.OrderID', $PoNumber);
			
    		return $this->db->get('tblK1ordermaster')->row();
		}
    	
    	public function GetPodetailsItemwise($PoNumber)
    	{
    		$this->db->select('tblK1history.*,tblK1ordermaster.Transdate,tblK1ordermaster.OrderStatus,tblCenterMaster.CenterName AS CenterName,tblclients.company,tblproduct.ProductName');	
    		$this->db->join(db_prefix() . 'K1ordermaster', 'tblK1ordermaster.OrderID = tblK1history.OrderID');
    		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
    		$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = tblK1ordermaster.CenterID');
    	   	$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1ordermaster.AccountID');				
    		$this->db->where('tblK1history.OrderID', $PoNumber);		
    		return $this->db->get('tblK1history')->result_array();		
		}
		
		public function GetCategoryFromSubCategoryCode($category_id)
		{
			$this->db->where('CategoryID',$category_id);
			return $this->db->get('tblK1ItemSubCategory')->result_array();
		}
		public function getCategoryBySubCategoryCode($category_id)
        {
			$this->db->where('CategoryID',$category_id);
			return $this->db->get('tblK1ItemSubCategory')->result_array();
		}
		
		
		public function GetAllDirectIncomeLedger($maingroup)
		{
		    $this->db->select('tblclients.*');
		    $this->db->where('tblclients.ActGroupID',$maingroup);
			$this->db->order_by(db_prefix() . 'clients.company', 'ASC');
			return $this->db->get(db_prefix().'clients')->result_array();
		}
		
		public function load_data_for_direct_sale_orderkirtione($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);        
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'K1ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
			AND tblK1ordermaster.FY = "'.$fy.'" 
			AND tblK1ordermaster.PlantID = "'.$selected_company.'" AND IsDirectSale = "Y" ';
			if($data["CategoryTypeFilter"]){
				$sql1 .= ' AND tblK1ordermaster.CategoryType	 = "'.$data["CategoryTypeFilter"].'"';
			}
			$sql1 .= 'ORDER BY OrderID DESC';
			
			$sql ='SELECT '.db_prefix().'K1ordermaster.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients 
			WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'K1ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName
			FROM '.db_prefix().'K1ordermaster WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		// HSN Wise Report
		public function get_data_for_HSN($data)
		{   
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$CenterID = $data["CenterID"];
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			
			$sql2 = '';
			if(!empty($CenterID)){
				$sql2 .= ' AND tblK1salesmaster.CenterID = "'.$CenterID.'" ';
			}
			
			$sql1 = 'SELECT tblK1salesmaster.SalesID
			FROM `tblK1salesmaster` 
			WHERE tblK1salesmaster.PlantID = '.$selected_company.' AND tblK1salesmaster.FY = "'.$year.'" 
			AND tblK1salesmaster.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" '.$sql2.'';
			$SaleIDS = $this->db->query($sql1)->result_array();
			
			$TransIDs = array();
			foreach($SaleIDS as $value){
				array_push($TransIDs,$value["SalesID"]);
			}
			$this->db->select('tblproduct.hsn_code,
			tblK1history.cgst,sum(tblK1history.cgstamt) AS CGSTSUM, tblK1history.sgst, SUM(tblK1history.sgstamt) SGSTSUM,tblK1history.igst, 
			SUM(tblK1history.igstamt) AS IGSTSUM, SUM(tblK1history.ChallanAmt - tblK1history.DiscAmt) AS TaxableAmt ,SUM(tblK1history.DiscAmt) AS TotalDiscAmt, 
			SUM(tblK1history.NetChallanAmt) AS BillAmt,SUM(tblK1history.BilledQty) AS BilledQtySum');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID AND  '.db_prefix() . 'product.PlantID = ' . db_prefix() . 'K1history.PlantID');
			$this->db->where_in(db_prefix() . 'K1history.TransID', $TransIDs);
			if(!empty($CenterID)){
				$this->db->where_in(db_prefix() . 'K1history.CenterID', $CenterID);
			}
			$this->db->where(db_prefix() . 'K1history.TType', 'O');
			$this->db->where(db_prefix() . 'K1history.TType2', 'SALE');
			$this->db->group_by('tblproduct.hsn_code,tblK1history.cgst,tblK1history.sgst,tblK1history.igst');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		//================== Get All HSN Master ========================================
		public function getHsnMaster($data)
		{
			$sql1 = 'SELECT tblhsn.* FROM `tblhsn`';
			$HsnMaster = $this->db->query($sql1)->result_array();
			return $HsnMaster;
		}
		
		public function GetSRT_HSN($data)
		{   
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$CenterID = $data["CenterID"];
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			$sql2 = '';
			if(!empty($CenterID)){
				$sql2 .= ' AND tblK1salesreturn.CenterID = "'.$CenterID.'" ';
			}
			$sql1 = 'SELECT tblK1salesreturn.SalesRtnID
			FROM `tblK1salesreturn` 
			WHERE tblK1salesreturn.PlantID = '.$selected_company.' AND tblK1salesreturn.FY = "'.$year.'"  AND 
			tblK1salesreturn.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"  '.$sql2.'
			';
			$SaleIDS = $this->db->query($sql1)->result_array();
			
			$TransIDs = array();
			foreach($SaleIDS as $value){
				array_push($TransIDs,$value["SalesRtnID"]);
			}
			if (!$TransIDs) {
				$result = [];
				} else {
				$this->db->select('tblproduct.hsn_code,tblK1history.cgst, tblK1history.sgst, tblK1history.igst');
				$this->db->from(db_prefix() . 'K1history');
				$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID AND  '.db_prefix() . 'product.PlantID = ' . db_prefix() . 'K1history.PlantID');
				$this->db->where_in(db_prefix() . 'K1history.OrderID', $TransIDs);
				if(!empty($CenterID)){
					$this->db->where_in(db_prefix() . 'K1history.CenterID', $CenterID);
				}
				$this->db->where(db_prefix() . 'K1history.TType', 'R');
				$this->db->group_by('tblproduct.hsn_code,tblK1history.cgst,tblK1history.sgst,tblK1history.igst');
				$result = $this->db->get()->result_array();
			}
			return $result;
		}
		
		public function GetCD_HSN($data)
		{
			
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			
			$sql = 'SELECT DISTINCT tblcdnotehistory.hsncode,tblcdnotehistory.cgst,tblcdnotehistory.sgst,tblcdnotehistory.igst 
			FROM `tblcdnotehistory`
			WHERE tblcdnotehistory.plantid = '.$selected_company.' AND tblcdnotehistory.fy LIKE "'.$year.'" 
			AND tblcdnotehistory.transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" 
			GROUP BY tblcdnotehistory.hsncode,tblcdnotehistory.cgst,tblcdnotehistory.sgst,tblcdnotehistory.igst';
			$result = $this->db->query($sql)->result_array();
			return $result;
			
		}
		
		public function get_data_for_HSNSRT($data)
		{   
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$CenterID = $data["CenterID"];
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			
			$sql1 = "";
			if(!empty($CenterID)){
				$sql1 .= ' AND tblK1history.CenterID = "'.$CenterID.'" ';
			}
			$sql = 'SELECT tblproduct.hsn_code,
			tblK1history.cgst,sum(tblK1history.cgstamt) AS CGSTSUM, tblK1history.sgst, SUM(tblK1history.sgstamt) SGSTSUM,tblK1history.igst, 
			SUM(tblK1history.igstamt) AS IGSTSUM, SUM(tblK1history.ChallanAmt - tblK1history.DiscAmt) AS TaxableAmt ,SUM(tblK1history.DiscAmt) AS TotalDiscAmt , 
			SUM(tblK1history.NetChallanAmt) AS BillAmt,SUM(tblK1history.BilledQty) AS BilledQtySum
			FROM `tblK1history` 
			INNER JOIN tblproduct ON tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID
			WHERE tblK1history.PlantID = '.$selected_company.' AND tblK1history.FY LIKE "'.$year.'" AND tblK1history.TType = "R"
			AND tblK1history.TransDate BETWEEN "'.$from_date.'" AND "'.$to_date.'" '.$sql1.' 
			GROUP BY tblproduct.hsn_code,tblK1history.cgst,tblK1history.sgst,tblK1history.igst';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function get_data_for_HSNCD($data)
		{   
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			
			$sql = 'SELECT tblcdnotehistory.hsncode,tblcdnotehistory.cgst,SUM(tblcdnotehistory.cgstamt) AS CGSTSUM, tblcdnotehistory.sgst, SUM(tblcdnotehistory.sgstamt) SGSTSUM,tblcdnotehistory.igst,  tblK1purchasemaster.PurchID, tblK1salesmaster.SalesID,
			SUM(tblcdnotehistory.igstamt) AS IGSTSUM, SUM(tblcdnotehistory.rate) AS TaxableAmt , SUM(tblcdnotehistory.amount) AS BillAmt,SUM(tblcdnotehistory.qty) AS BilledQtySum  
			FROM `tblcdnotehistory` 
			LEFT JOIN tblK1purchasemaster ON tblK1purchasemaster.PurchID =  tblcdnotehistory.TransID
			LEFT JOIN 	tblK1salesmaster ON 	tblK1salesmaster.SalesID =  tblcdnotehistory.TransID
			WHERE tblcdnotehistory.plantid = '.$selected_company.' AND tblcdnotehistory.fy = "'.$year.'" AND tblcdnotehistory.transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblcdnotehistory.ttype = "C"
			GROUP BY tblcdnotehistory.hsncode,tblcdnotehistory.cgst,tblcdnotehistory.sgst,tblcdnotehistory.igst';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		//=============================== Get HSN Wise Ddebit note Amount ==============
		public function get_data_for_HSNDD($data)
		{   
			$selected_company = $this->session->userdata('root_company');
			$year = $_SESSION['finacial_year'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$from_date = $from_date.' 00:00:00';
			$to_date = $to_date.' 23:59:59';
			
			$sql = 'SELECT tblcdnotehistory.hsncode,tblcdnotehistory.cgst,SUM(tblcdnotehistory.cgstamt) AS CGSTSUM, tblcdnotehistory.sgst, SUM(tblcdnotehistory.sgstamt) SGSTSUM,tblcdnotehistory.igst,  tblK1purchasemaster.PurchID, tblK1salesmaster.SalesID,
			SUM(tblcdnotehistory.igstamt) AS IGSTSUM, SUM(tblcdnotehistory.rate) AS TaxableAmt , SUM(tblcdnotehistory.amount) AS BillAmt,SUM(tblcdnotehistory.qty) AS BilledQtySum  
			FROM `tblcdnotehistory`
			LEFT JOIN tblK1purchasemaster ON tblK1purchasemaster.PurchID =  tblcdnotehistory.TransID
			LEFT JOIN tblK1salesmaster ON 	tblK1salesmaster.SalesID =  tblcdnotehistory.TransID
			WHERE tblcdnotehistory.plantid = '.$selected_company.' AND tblcdnotehistory.fy = "'.$year.'" AND tblcdnotehistory.transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblcdnotehistory.ttype = "D"
			GROUP BY tblcdnotehistory.hsncode,tblcdnotehistory.cgst,tblcdnotehistory.sgst,tblcdnotehistory.igst';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
	}				