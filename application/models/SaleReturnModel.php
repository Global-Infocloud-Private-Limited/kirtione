<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class SaleReturnModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		public function get_all_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
		}
		public function get_all_table_data($tbl)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$query = $this->db->get();
			return $query->result_array();
		}	
		public function get_items_code()
		{
			$selected_company = $this->session->userdata('root_company');   
			return $this->db->query('SELECT ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID FROM '.db_prefix().'product WHERE PlantID = '.$selected_company)->result_array();
		}
		public function getstatelist()
		{
			$Data = $this->db->get('tblxx_statelist')->result_array();
			return $Data;
		}
		public function get_company_detail()
		{
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		
//==================== Get Sale Center List ====================================
    public function GetSaleCenterList()
	{
	    $UserID = $this->session->userdata('username');
		$this->db->select('tblK1salesmaster.CenterID,tblCenterMaster.CenterName');
		$this->db->from('tblK1salesmaster');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1salesmaster.CenterID');
		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblCenterMaster.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
		$this->db->group_by('tblK1salesmaster.CenterID');
		$this->db->order_by('tblCenterMaster.CenterName');
		$query = $this->db->get();
		return $query->result_array();
	}
		public function PendingInvoiceCenterwiseClients($CenterID)
		{
			$this->db->select('
			tblclients.AccountID,
			tblclients.company,
			tblCustomerType.name AS customer_type,
			tblcontacts.phonenumber,
			tblxx_statelist.state_name,
			tblxx_citylist.city_name
			');
			
			$this->db->from('tblK1salesmaster');
			$this->db->join('tblclients', 'tblclients.AccountID = tblK1salesmaster.AccountID');
			$this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
			$this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID', 'LEFT');
			$this->db->join('tblxx_statelist', 'tblxx_statelist.id = tblclients.state', 'LEFT');
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
			
			//$this->db->where('tblclients.CustomerType', '3');
			//$this->db->where('tblclients.IsKirtiOneAccess', 'Y');
			//$this->db->where('tblK1salesmaster.SalesID', null);
			$this->db->where('tblK1salesmaster.CenterID', $CenterID);
			
			$this->db->group_by('tblK1salesmaster.AccountID');
			
			$query = $this->db->get();
			
			// Debug: Remove in production
			//echo $this->db->last_query(); 
			
			return $query->result_array();
		}
		
		public function get_order_PI_ven_center_details($id, $CenterID)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblK1salesmaster.*');
			$this->db->from(db_prefix() . 'K1salesmaster');
			$this->db->where('tblK1salesmaster.CenterID',$CenterID);
			$this->db->where('tblK1salesmaster.AccountID',$id);
			$this->db->where('tblK1salesmaster.SalesID IS NOT NULL');
			// $this->db->where('tblK1salesmaster.Is_Ledger','N');
			$this->db->where('tblK1salesmaster.PlantID', $selected_company);
			$this->db->where('tblK1salesmaster.FY', $fy);
			$result = $this->db->get()->result();
			// echo $this->db->last_query();
			return $result;
		}
		
		public function GetBillqty($id)
		{
		
		
			$PlantID = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			
			$this->db->select("
			tblK1history.BilledQty as ReturnOrderQty,
			tblproduct.PackingQty,
			tblproduct.PackingWeight AS Packingwgt,
			
			(
            SELECT SUM(RcvHistory.OrderQty / tblproduct.PackingQty)
            FROM tblK1history AS RcvHistory
            WHERE RcvHistory.ItemID = tblK1history.ItemID
			AND RcvHistory.OrderID = tblK1history.OrderID
			AND RcvHistory.TType = 'O'
			AND RcvHistory.TType2 = 'SALE'
			) AS RcvQty,
			(
            SELECT SUM(RqstHistory.OrderQty / tblproduct.PackingQty)
            FROM tblK1history AS RqstHistory
            WHERE RqstHistory.ItemID = tblK1history.ItemID
			AND RqstHistory.BillID = tblK1history.BillID
			AND RqstHistory.TType = 'O'
			AND RqstHistory.TType2 = 'SALE'
			) AS SOQty
			", false);  // `false` to prevent escaping your subqueries
			
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
			$this->db->where('tblK1history.TransID', $id);
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->where('tblK1history.FY', $fy);
			
			$results = $this->db->get()->result_array();
			
			return $results;
		}
		
		public function GetReturnSaleOrderItemListForInv($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('tblK1history.*,
			tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,
			tblproduct.PackingQty,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.ItemID AS id,
			tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,tblK1history.BilledQty,
			(Select SUM(PRHistory.BilledQty) from tblK1history as PRHistory where PRHistory.ItemID = tblK1history.ItemID  AND PRHistory.BillID = tblK1history.TransID AND PRHistory.TType ="SR") As EXSRQty');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst'); 		
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');   		
			$this->db->where(db_prefix() . 'K1history.TransID', $id);
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $fy);
			$results = $this->db->get()->result_array();
			$CGStAmt = 0;$IGSTAmt = 0;
			foreach ($results as &$row) {
			    $CGStAmt += $row['cgstamt'];
			    $IGStAmt += $row['igstamt'];
				if ($row['PackingQty'] == 1) {		
					$row['OrderQty'] = $row['BilledQty'];
				} else {				
					$row['OrderQty'] = $row['BilledQty'] / $row['PackingQty'];
				}
				$row['SOQty'] = $row['BilledQty'] - $row['EXSRQty'];
				$row['Discount'] = $row['Discount'] / $row['BilledQty'];
			}
			$SaleType = "B";
			if($CGStAmt > 0 || $IGSTAmt > 0){
			    $SaleType = "T";
			}
			$response["ItemDetails"] = $results;
			$response["SaleType"] = $SaleType;
			return $response;
		}
		
		public function load_data_for_sale_return_invoice_kirtione($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);        
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'K1salesreturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
			AND tblK1salesreturn.FY = "'.$fy.'" AND tblK1salesreturn.SalesRtnID IS NOT NULL 
			AND tblK1salesreturn.PlantID = "'.$selected_company.'"         
			ORDER BY SalesRtnID DESC';
			
			$sql ='SELECT '.db_prefix().'K1salesreturn.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'K1salesreturn.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName
			FROM '.db_prefix().'K1salesreturn WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function increment_next_number($name)
		{   
		    $year = $this->session->userdata('finacial_year');
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('name', $name);
			$this->db->WHERE('FY', $year);
			$this->db->update(db_prefix() . 'options');
		}
		
		//=================== Add Kirti One Return Sale Order =============================
		public function AddKirtiOneReturnSaleOrderNew($data)
		{	
			if(isset($data['pur_order_detail'])){
				$pur_order_detail = json_decode($data['pur_order_detail']);
				
				unset($data['pur_order_detail']); // Just remove the key
				
				
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'HSN';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PIQty';
				$header[] = 'PReturnQty';
				$header[] = 'SaleRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				$header[] = 'BatchNo';
				$header[] = 'ExpDate';
				
				foreach ($pur_order_detail as $key => $value) {
					if($value[0] != ''){
						$ItemID = $value[0];
						$es_detail[] = array_combine($header, $value);
						
					}
				}
			}
			$PlantID = $this->session->userdata('root_company'); 
			$FY = $this->session->userdata('finacial_year');
			/*echo "<pre>";
			print_r($data);
			print_r($es_detail);
			die;*/
			
			$prefix = 'SR';
			$sale_orderNumbar = get_option('next_Sale_rtn_number_for_kirtione');
			$new_sale_ReturnorderNumbar = $prefix.$FY.$sale_orderNumbar;  
			
			$Transdate =  to_sql_date($data['prd_date'])." ".date('H:i:s'); 
			
			$SalesID = $data['SaleID'];
			$AccountID = $data['vendor'];	 
			$CenterID = $data['centername']; 
			$State = $data['state'];
			$SaleAmt =  $data['total_amt_in_mt'];
			$discountAMT = $data['total_disc_in_mt'];		
			$cgstamt = $data['total_cgst_amt'];		
			$sgstamt = $data['total_sgst_amt'];
			$igstamt = $data['total_igst_amt'];
			$roundoffamt = $data['total_roundoff_amt'];	  
			$invoiceamt = $data['netpayableamt'];
			$SalesRtnTypeID = $data['SalesRtnType'];
			$SaleType = $data['SaleType'];
			if($SalesRtnTypeID == "1"){
			    $TType2 = "FRESH RETURN";
			}else{
			    $TType2 = "DAMAGE RETURN";
			}
			$ItCount = count($es_detail); 	
			//$SalesID = $data["SalesID"];
			$KirtiOneSaleMaster = array(
			'PlantID'=>$PlantID,
			'FY'=>$FY,
			'BT'=>'T',
			'SalesRtnID' =>$new_sale_ReturnorderNumbar,
			'SaleID' =>$SalesID,
			'Transdate' =>$Transdate, 
			'CenterID' =>$CenterID,            
			'AccountID' =>$AccountID,
			'SaleAmt'=>$SaleAmt,
			'Discamt'=>$discountAMT,
			'cgstamt'=>$cgstamt,
			'sgstamt'=>$sgstamt,
			'igstamt'=>$igstamt,
			'RndAmt'=>$roundoffamt,
			'BillAmt'=>$invoiceamt,
			'ItCount'=>$ItCount,
			"UserID" => $_SESSION['username'],
			"SalesRtnTypeID" =>$SalesRtnTypeID,
			);
			
			$this->db->insert(db_prefix() . 'K1salesreturn',$KirtiOneSaleMaster);  			
			if($this->db->affected_rows() > 0)
			{
				$this->increment_next_number('next_Sale_rtn_number_for_kirtione');		
				$i =1;
				$TotalItemAmt = 0;
				$TotalDiscAmt = 0;
				$TotalTaxableAmt = 0;
				$TotaCGSTAmt = 0;
				$TotaSGSTAmt = 0;
				$TotaIGSTAmt = 0;
				$TotalNetAmt = 0;
				foreach($es_detail as $value)
				{
					$ItemID = $value['ItemID'];
					$SaleRate = $value['SaleRate'];
					$saleunit = $value['SaleUnit'];
					$unit = $value['MeasuredIn'];  
					$packing_qty = $value['PackingQty'];      
					$packing_weight = $value['PackingWeight']; 
					$Orderqty = $value['PIQty'];
					$qty = $value['PReturnQty'];  					
					$TotalDisc = $value['Discount'] * $qty;  
					if($SaleType == "T"){
					    $gst = $value['GST'];  
					}else{
					    $gst = 0;
					}
					
					$cgstamt = $value['CGSTAMT'];
					$sgstamt = $value['SGSTAMT'];
					$igstamt = $value['IGSTAMT'];
					$netAmount = $value['total_money']; 
					$BatchNo = $value['BatchNo'];
					$ExpDate = to_sql_date($value['ExpDate']);
					$BilledQty = $qty; 			
						$caseqty = 1;
				
					$ItemTotal = $qty * $SaleRate;
					$TotalItemAmt += $ItemTotal;
					$TotalDiscAmt += $TotalDisc;
					$TaxableAmt = $ItemTotal - $TotalDisc;
					$TotalTaxableAmt += $TaxableAmt;
					$Discountperc = ($value['Discount'] / 100) * $qty * $value['SaleRate'];
					$CGST = 0;
					$CGSTAmt = 0;
					$SGST = 0;
					$SGSTAmt = 0;
					$IGST = 0;
					$IGSTAmt = 0;
					
					if($State == "MH"){
						$CGST = $gst/2;$SGST = $gst/2;$IGST = 0;
						if($SaleType == "T"){
						    $CGSTAmt = $TaxableAmt * ($CGST/100);
    						$SGSTAmt = $TaxableAmt * ($SGST/100);
    						$IGSTAmt = 0;
						}
					}else{
						$CGST = 0;$SGST = 0;$IGST = $gst;
						if($SaleType == "T"){
						    $CGSTAmt = 0;
						    $SGSTAmt = 0;
						    $IGSTAmt = $TaxableAmt * ($IGST/100);   
						}
					}
					$TotaCGSTAmt += $CGSTAmt;
					$TotaSGSTAmt += $SGSTAmt;
					$TotaIGSTAmt += $IGSTAmt;
					$NetAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt + $IGSTAmt;
					$TotalNetAmt += $NetAmt;
					$data_array_result = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'OrderID' =>$new_sale_ReturnorderNumbar,
					'TransID' =>$new_sale_ReturnorderNumbar,
					'BillID' =>$SalesID,
					'TransDate' =>$Transdate,
					'TransDate2'=>date('Y-m-d H:i:s'),						
					'TType'=>'SR',
					'TType2'=> $TType2,
					'AccountID'=> $AccountID,
					'ItemID'=>$ItemID,
					'CenterID'=>$CenterID,
					'PartyID'=>"KASPL",							
					'PurchRate'=>"0",							
					'SaleRate'=>$SaleRate, 
					'BasicRate'=>$SaleRate,						
					'SuppliedIn'=>$caseqty,
					'OrderQty'=>$qty,						
					'BilledQty'=>$qty,
					'DiscPerc'=>$Discountperc,
					'DiscAmt'=>$TotalDisc,
					'cgst'=>$CGST,
					'cgstamt'=>$CGSTAmt,
					'sgst'=>$SGST,
					'sgstamt'=>$SGSTAmt,
					'igst'=>$IGST,
					'igstamt'=>$IGSTAmt,						
					'CaseQty'=>$caseqty,
					'Cases'=>0.00,
					'OrderAmt'=>$TaxableAmt,
					'ChallanAmt'=>$TaxableAmt,
					'NetOrderAmt'=>$NetAmt,
					'NetChallanAmt'=>$NetAmt,
					'Ordinalno'=>$i,	
					'rowid'=>"0",
					'UserID'=>$_SESSION['username'],
					'BatchNo'=>$BatchNo,
					'ExpDate'=>$ExpDate,
					'cnfid'=>""
					);		
					
					$this->db->insert(db_prefix() . 'K1history', $data_array_result);
					$i++;					
				}
				// Update Masster
				$KirtiOneSaleMaster = array(
        			'SaleAmt'=>$TotalItemAmt,
        			'Discamt'=>$TotalDiscAmt,
        			'cgstamt'=>$TotaCGSTAmt,
        			'sgstamt'=>$TotaSGSTAmt,
        			'igstamt'=>$TotaIGSTAmt,
        			'RndAmt'=>round($TotalNetAmt),
        			'BillAmt'=>$TotalNetAmt
        		);
        		
        		$this->db->where(db_prefix() . 'K1salesreturn.SalesRtnID', $new_sale_ReturnorderNumbar);
        		$this->db->update(db_prefix() . 'K1salesreturn', $KirtiOneSaleMaster);
				$UserID = $_SESSION['username'];
				$narration = "Sale Return Against ".$SalesID;
				$ord = 1;
				// Add Ledger Entry
				// Credit to Party
				$sale_ledger_entry = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'Transdate'=>date('Y-m-d h:i:s'),
					'VoucherID'=>$new_sale_ReturnorderNumbar,  
					'Transdate2'=>date('Y-m-d h:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>$AccountID,
					'CounterAccount'=>"SALER",
					'CenterID'=>$CenterId,
					'EntryFor'=>3,
					'TType'=>"C",
					'Amount'=>$TotalNetAmt,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES RETURN",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);
				$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
				$ord++;
				if($TotalDiscAmt > 0){
					// Credit to Discount Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"DISCR",
						'CounterAccount'=>"SALER",
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"C",
						'Amount'=>$TotalDiscAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				}
				// Debit  to Sale Return Ledger
				$sale_ledger_entry = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'Transdate'=>date('Y-m-d h:i:s'),
					'VoucherID'=>$new_sale_ReturnorderNumbar,  
					'Transdate2'=>date('Y-m-d h:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>"SALER",
					'CounterAccount'=>$AccountID,
					'CenterID'=>$CenterId,
					'EntryFor'=>3,
					'TType'=>"D",
					'Amount'=>$ItemTotal,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES RETURN",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);
				$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
				$ord++;
				if($TotaIGSTAmt > 0){
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"IGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaIGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				}else{
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"CGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaCGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"SGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaSGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				}
				return $new_sale_ReturnorderNumbar;
			}			
		}
		
		//================== update Kirti One Return Purchase Invoice ===================
		public function UpdateKirtiOneReturnSaleInvoice($data,$id)
		{			
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');  
			
			if(isset($data['pur_order_detail'])){
				$pur_order_detail = json_decode($data['pur_order_detail']);
				unset($data['pur_order_detail']);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'HSN';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PIQty';
				$header[] = 'PReturnQty';
				$header[] = 'SaleRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				$header[] = 'BatchNo';
				$header[] = 'ExpDate';
				
				foreach ($pur_order_detail as $key => $value) {
					
					if($value[0] != ''){
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
		    $SaleRetID =  $id;
			$Transdate =  to_sql_date($data['prd_date'])." ".date('H:i:s'); 
			$new_date =  to_sql_date($data['prd_date'])." ".date('H:i:s');
			$SalesID = $data['SaleID'];
			$AccountID = $data['vendor'];	 
			$CenterID = $data['centername']; 
			$State = $data['state'];
			$SaleAmt =  $data['total_amt_in_mt'];
			$discountAMT = $data['total_disc_in_mt'];		
			$cgstamt = $data['total_cgst_amt'];		
			$sgstamt = $data['total_sgst_amt'];
			$igstamt = $data['total_igst_amt'];
			$roundoffamt = $data['total_roundoff_amt'];	  
			$invoiceamt = $data['netpayableamt'];
			$SalesRtnTypeID = $data['SalesRtnType'];
			$SaleType = $data['SaleType'];
			if($SalesRtnTypeID == "1"){
			    $TType2 = "FRESH RETURN";
			}else{
			    $TType2 = "DAMAGE RETURN";
			}
			$ItCount = count($es_detail);   			
			
			$this->db->select('tblK1salesreturn.*');
			$this->db->from(db_prefix() . 'K1salesreturn'); 		
			$this->db->where(db_prefix() . 'K1salesreturn.SalesRtnID', $SaleRetID);
			$purchaselist = $this->db->get()->row();
			
			$data_array = array(   
    			'Transdate' =>$new_date,
    			'ItCount'=>$ItCount,
    			'SalesRtnTypeID' =>$SalesRtnTypeID,
    			'Lupdate'=>date('Y-m-d H:i:s'),
    			'UserID2'=>$this->session->userdata('username')
			);
			
			$this->db->where('PlantID', $PlantID);
			$this->db->LIKE('FY', $Fy);
			$this->db->where('SalesRtnID',$SaleRetID);
			$this->db->update(db_prefix() . 'K1salesreturn',$data_array);
			
			if($this->db->affected_rows() > 0)
			{
				$old_pur_details = $this->get_sale_returninvoice_detail($SalesID, $SaleRetID);
				//Move record from tblK1history to tblK1history_audit
				foreach ($old_pur_details as $key => $value) 
				{	
					$old_data = array(
					'PlantID'=>$value["PlantID"],
					'FY'=>$value["FY"],
					'OrderID' =>$value["OrderID"],
					'BillID' =>$value["BillID"],
					'TransID' =>$value["TransID"],
					'TransDate'=>$value["TransDate"],
					'TransDate2'=>$value["TransDate2"],
					'TType'=>$value["TType"],
					'TType2'=> $value["TType2"],
					'AccountID'=> $value["AccountID"],
					'ItemID'=>$value["ItemID"],
					'CenterID'=>$value["CenterID"],
					'GodownID' =>$value["GodownID"],
					'PartyID'=>$value["PartyID"],					
					'PurchRate'=>$value["PurchRate"],
					'SaleRate'=>$value['SaleRate'],
					'BasicRate'=>$value['BasicRate'],                    
					'SuppliedIn'=>$value["SuppliedIn"],
					'OrderQty'=>$value['OrderQty'],
					'eOrderQty'=>$value['eOrderQty'],
					'BilledQty'=>$value['BilledQty'],
					'DiscPerc'=>$value["DiscPerc"],
					'DiscAmt'=>$value['DiscAmt'],
					'cgst'=>$value["cgst"],
					'cgstamt'=>$value['cgstamt'],
					'sgst'=>$value["sgst"],
					'sgstamt'=>$value['sgstamt'],
					'igst'=>$value["igst"],
					'igstamt'=>$value['igstamt'],
					'CaseQty'=>$value['CaseQty'],
					'Cases'=>$value['Cases'],
					'OrderAmt'=>$value['OrderAmt'],
					'ChallanAmt'=>$value['ChallanAmt'],
					'NetOrderAmt'=>$value['NetOrderAmt'],
					'NetChallanAmt'=>$value['NetChallanAmt'],
					'Ordinalno'=>$value["Ordinalno"],
					'UserID'=>$value["UserID"],
					'Lupdate'=>date('Y-m-d H:i:s'),
					'UserID2'=>$_SESSION['username']
					);	
					$this->db->insert(db_prefix() . 'K1history_audit',$old_data);							
				}
				
				//Delete Live history table record 
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('OrderID', $SaleRetID);
				$this->db->delete(db_prefix().'K1history');	
				
				//Add New history detail record		
				//$Pr_item_data = $this->GetPurchaseRequestItemListInvoiceAdd($Pr_no);
				
				$i =1;
				$TotalItemAmt = 0;
				$TotalDiscAmt = 0;
				$TotalTaxableAmt = 0;
				$TotaCGSTAmt = 0;
				$TotaSGSTAmt = 0;
				$TotaIGSTAmt = 0;
				$TotalNetAmt = 0;
				foreach($es_detail as $value)
				{
					$ItemID = $value['ItemID'];
					$SaleRate = $value['SaleRate'];
					$saleunit = $value['SaleUnit'];
					$unit = $value['MeasuredIn'];  
					$packing_qty = $value['PackingQty'];      
					$packing_weight = $value['PackingWeight']; 
					$Orderqty = $value['PIQty'];
					$qty = $value['PReturnQty'];  					
					$TotalDisc = $value['Discount'] * $qty;  
					if($SaleType == "T"){
					    $gst = $value['GST'];  
					}else{
					    $gst = 0;
					}
					
					$cgstamt = $value['CGSTAMT'];
					$sgstamt = $value['SGSTAMT'];
					$igstamt = $value['IGSTAMT'];
					$netAmount = $value['total_money']; 
					$BatchNo = $value['BatchNo'];
					$ExpDate = $value['ExpDate'];
					$BilledQty = $qty; 			
					$caseqty = 1;
					
					$ItemTotal = $qty * $SaleRate;
					$TotalItemAmt += $ItemTotal;
					$TotalDiscAmt += $TotalDisc;
					$TaxableAmt = $ItemTotal - $TotalDisc;
					$TotalTaxableAmt += $TaxableAmt;
					$Discountperc = ($value['Discount'] / 100) * $qty * $value['SaleRate'];
					$CGST = 0;
					$CGSTAmt = 0;
					$SGST = 0;
					$SGSTAmt = 0;
					$IGST = 0;
					$IGSTAmt = 0;
					
					if($State == "MH"){
						$CGST = $gst/2;$SGST = $gst/2;$IGST = 0;
						if($SaleType == "T"){
						    $CGSTAmt = $TaxableAmt * ($CGST/100);
    						$SGSTAmt = $TaxableAmt * ($SGST/100);
    						$IGSTAmt = 0;
						}
					}else{
						$CGST = 0;$SGST = 0;$IGST = $gst;
						if($SaleType == "T"){
						    $CGSTAmt = 0;
						    $SGSTAmt = 0;
						    $IGSTAmt = $TaxableAmt * ($IGST/100);   
						}
					}
					$TotaCGSTAmt += $CGSTAmt;
					$TotaSGSTAmt += $SGSTAmt;
					$TotaIGSTAmt += $IGSTAmt;
					$NetAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt + $IGSTAmt;
					$TotalNetAmt += $NetAmt;
					$data_array_result = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'OrderID' =>$SaleRetID,
					'TransID' =>$SaleRetID,
					'BillID' =>$SalesID,
					'TransDate' =>$Transdate,
					'TransDate2'=>date('Y-m-d H:i:s'),						
					'TType'=>'SR',
					'TType2'=> $TType2,
					'AccountID'=> $AccountID,
					'ItemID'=>$ItemID,
					'CenterID'=>$CenterID,
					'PartyID'=>"KASPL",							
					'PurchRate'=>"0",							
					'SaleRate'=>$SaleRate, 
					'BasicRate'=>$SaleRate,						
					'SuppliedIn'=>$caseqty,
					'OrderQty'=>$qty,						
					'BilledQty'=>$qty,
					'DiscPerc'=>$Discountperc,
					'DiscAmt'=>$TotalDisc,
					'cgst'=>$CGST,
					'cgstamt'=>$CGSTAmt,
					'sgst'=>$SGST,
					'sgstamt'=>$SGSTAmt,
					'igst'=>$IGST,
					'igstamt'=>$IGSTAmt,						
					'CaseQty'=>$caseqty,
					'Cases'=>0.00,
					'OrderAmt'=>$TaxableAmt,
					'ChallanAmt'=>$TaxableAmt,
					'NetOrderAmt'=>$NetAmt,
					'NetChallanAmt'=>$NetAmt,
					'Ordinalno'=>$i,	
					'rowid'=>"0",
					'UserID'=>$_SESSION['username'],
					'BatchNo'=>$BatchNo,
					'ExpDate'=>$ExpDate,
					'cnfid'=>""
					);
					$this->db->insert(db_prefix() . 'K1history', $data_array_result);
					$i++;					
				}
				// Update Masster
				$KirtiOneSaleMaster = array(
        			'SaleAmt'=>$TotalItemAmt,
        			'Discamt'=>$TotalDiscAmt,
        			'cgstamt'=>$TotaCGSTAmt,
        			'sgstamt'=>$TotaSGSTAmt,
        			'igstamt'=>$TotaIGSTAmt,
        			'RndAmt'=>round($TotalNetAmt),
        			'BillAmt'=>$TotalNetAmt
        		);
        		
        		$this->db->where(db_prefix() . 'K1salesreturn.SalesRtnID', $SaleRetID);
        		$this->db->update(db_prefix() . 'K1salesreturn', $KirtiOneSaleMaster);
				//Delete Live history table record 
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('VoucherID', $SaleRetID);
				$this->db->delete(db_prefix().'accountledger');
				
				$UserID = $_SESSION['username'];
				$new_sale_ReturnorderNumbar	= $SaleRetID;
				$narration = "Sale Return Against ".$SaleRetID;
				$ord = 1;
				// Add Ledger Entry
				// Credit to Party
				$sale_ledger_entry = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'Transdate'=>date('Y-m-d h:i:s'),
					'VoucherID'=>$new_sale_ReturnorderNumbar,  
					'Transdate2'=>date('Y-m-d h:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>$AccountID,
					'CounterAccount'=>"SALER",
					'CenterID'=>$CenterId,
					'EntryFor'=>3,
					'TType'=>"C",
					'Amount'=>$TotalNetAmt,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES RETURN",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);
				$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
				$ord++;
				if($$TotalDiscAmt > 0){
					// Credit to Discount Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"DISCR",
						'CounterAccount'=>"SALER",
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"C",
						'Amount'=>$TotalDiscAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				}
				// Debit  to Sale Return Ledger
				$sale_ledger_entry = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'Transdate'=>date('Y-m-d h:i:s'),
					'VoucherID'=>$new_sale_ReturnorderNumbar,  
					'Transdate2'=>date('Y-m-d h:i:s'), 
					'PartyID'=>"KASPL",
					'AccountID'=>"SALER",
					'CounterAccount'=>$AccountID,
					'CenterID'=>$CenterId,
					'EntryFor'=>3,
					'TType'=>"D",
					'Amount'=>$ItemTotal,
					'Narration'=>$narration,
					'PassedFrom'=>"SALES RETURN",
					'OrdinalNo'=>$ord,
					'UserID'=>$UserID     
					);
				$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
				$ord++;
				if($TotaIGSTAmt > 0){
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"IGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaIGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				}else{
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"CGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaCGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
					// Debit  to CGST Ledger
					$sale_ledger_entry = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'Transdate'=>date('Y-m-d h:i:s'),
						'VoucherID'=>$new_sale_ReturnorderNumbar,  
						'Transdate2'=>date('Y-m-d h:i:s'), 
						'PartyID'=>"KASPL",
						'AccountID'=>"SGST",
						'CounterAccount'=>$AccountID,
						'CenterID'=>$CenterId,
						'EntryFor'=>3,
						'TType'=>"D",
						'Amount'=>$TotaSGSTAmt,
						'Narration'=>$narration,
						'PassedFrom'=>"SALES RETURN",
						'OrdinalNo'=>$ord,
						'UserID'=>$UserID     
						);
					$this->db->insert(db_prefix() . 'accountledger',$sale_ledger_entry);
					$ord++;
				
				
			}     
			return true;		
		}
		
	}
		
		
		
		public function GetSaleReturnInvoiceDetails($SINumber)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select('tblK1salesreturn.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1salesreturn.SaleAmt - tblK1salesreturn.DiscAmt) AS taxable_amt,tblCenterMaster.CenterName,tblGstRecord.gstin AS gst, CenterState.state_name AS StateCenter, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress');
			$this->db->from(db_prefix() . 'K1salesreturn');
			$this->db->join(db_prefix() . 'clients','tblclients.AccountID = tblK1salesreturn.AccountID AND tblclients.PlantID = tblK1salesreturn.PlantID');
			$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
			$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
			$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'K1salesreturn.CenterID', 'left');
			$this->db->join(db_prefix() . 'xx_statelist as CenterState', 'CenterState.short_name = tblCenterMaster.state', 'left');
			
			$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1salesreturn.SalesRtnID', 'left');
			
			$this->db->where(db_prefix() . 'K1salesreturn.SalesRtnID', $SINumber);
			$this->db->where(db_prefix() . 'K1salesreturn.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1salesreturn.FY', $year);
			return $this->db->get()->row();
		}
		
		
		public function GetSaleReturnInvoiceItemList($id,$SalesRtnTypeID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('tblK1history.*,tblK1history.ItemID AS id,tblK1history.DiscAmt,tblK1history.OrderQty,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,
			tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,
			tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,
			tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,tblK1history.BilledQty,
			(Select SUM(SQtyhistory.BilledQty) from tblK1history as SQtyhistory where SQtyhistory.ItemID = tblK1history.ItemID AND SQtyhistory.TransID = tblK1history.BillID AND SQtyhistory.TType ="O" AND SQtyhistory.TType2="SALE") As SOQty,
			(Select SUM(ExRtn.BilledQty) from tblK1history as ExRtn where ExRtn.ItemID = tblK1history.ItemID AND ExRtn.BillID = tblK1history.BillID AND ExRtn.TType ="SR") As ExSRQty');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst'); 		
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId'); 
			$this->db->where(db_prefix() . 'K1history.TransID', $id);
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $fy);
			$this->db->where(db_prefix() . 'K1history.TType', "SR");
			if($SalesRtnTypeID == "1"){
			    $TType2 = "FRESH RETURN";
			}else{
			    $TType2 = "DAMAGE RETURN";
			}
			$this->db->where(db_prefix() . 'K1history.TType2', $TType2);
			$results = $this->db->get()->result_array();
			
			//echo $this->db->last_query();
			foreach ($results as &$row) {
				$row['ExpDate'] = _d($row['ExpDate']);
				$row['ReturnOrderQty'] = $row['BilledQty'];
				$row['SOQty'] = $row['SOQty'] - $row['ExSRQty'] + $row['BilledQty'];
				if($row['BilledQty'] > 0){
				    $row['Discount'] = $row['DiscAmt']/$row['BilledQty'];
				}else{
				    $row['Discount'] = 0;
				}
				
			}
			return $results;
		}
		
		
		public function get_sale_returninvoice_detail($id, $SalesRtnID){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select();
			$this->db->from(db_prefix() . 'K1history');
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $year);
			$this->db->where(db_prefix() . 'K1history.BillID', $id);
			$this->db->where(db_prefix() . 'K1history.OrderID', $SalesRtnID);
			return $this->db->get()->result_array();
		}
		
		public function GetSalesReturnCenterList($LogInUser = "")
		{
			$this->db->select('tblK1history.CenterID, tblCenterMaster.CenterName');	
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1history.CenterID');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.TType', 'SR');
			if($LogInUser){
				$this->db->where('tblK1history.AccountID',$LogInUser);
			}
			$this->db->group_by('tblK1history.CenterID');
			return $this->db->get('tblK1history')->result_array();
		}

		public function GetSalesReturnItemList($LogInUser = "")
		{
			$this->db->select('tblK1history.ItemID,tblproduct.ProductName,tblproduct.ProductID');	
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.TType', 'SR');
			if($LogInUser){
				$this->db->where('tblK1history.AccountID',$LogInUser);
			}
			$this->db->group_by('tblK1history.ItemID');
			return $this->db->get('tblK1history')->result_array();
		}
		
		public function GetSalesReturnPartyList()
		{
			$this->db->select('tblK1salesreturn.SaleID,tblclients.company,tblclients.AccountID');	
			$this->db->join('tblclients', 'tblclients.AccountID = tblK1salesreturn.AccountID');
			$this->db->where('tblK1salesreturn.SaleID IS NOT NULL');
			$this->db->group_by('tblclients.AccountID');
			return $this->db->get('tblK1salesreturn')->result_array();
		}
		
		public function getSRReportFilter($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date   = to_sql_date($data["to_date"]);
			$fy        = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');

			if($data['ReportType'] == '1'){ // Report type bill
				$this->db->select('pr.*, cm.CenterName');
				$this->db->select("(SELECT GROUP_CONCAT(company SEPARATOR ',')
														FROM " . db_prefix() . "clients 
														WHERE " . db_prefix() . "clients.AccountID = pr.AccountID
														AND " . db_prefix() . "clients.PlantID = '$selected_company'
													) AS AccountName", false);
				$this->db->from(db_prefix() . 'K1salesreturn pr');
				$this->db->join(db_prefix() . 'CenterMaster cm', 'cm.CenterID = pr.CenterID', 'left');
				$this->db->where("pr.Transdate >=", $from_date.' 00:00:00');
				$this->db->where("pr.Transdate <=", $to_date.' 23:59:59');
				$this->db->where("pr.FY", $fy);
				$this->db->where("pr.SalesRtnID IS NOT NULL", null, false);
				$this->db->where("pr.PlantID", $selected_company);
				if(!empty($data['CenterID'])) $this->db->where("pr.CenterID", $data['CenterID']);
				if(!empty($data['AccountID'])) $this->db->where("pr.AccountID", $data['AccountID']);

				$this->db->order_by("pr.SalesRtnID", "DESC");
			}else{ // Report type item
				$this->db->select('pr.*, cm.CenterName, p.ProductName, p.hsn_code, b.BrandName');
				$this->db->select("(SELECT GROUP_CONCAT(company SEPARATOR ',')
														FROM " . db_prefix() . "clients 
														WHERE " . db_prefix() . "clients.AccountID = pr.AccountID
														AND " . db_prefix() . "clients.PlantID = '$selected_company'
													) AS AccountName", false);
				$this->db->from(db_prefix() . 'K1history pr');
				$this->db->join(db_prefix() . 'CenterMaster cm', 'cm.CenterID = pr.CenterID', 'left');
				$this->db->join(db_prefix() . 'product p', 'p.ProductID = pr.ItemID', 'left');
				$this->db->join(db_prefix() . 'brands b', 'b.id = p.BrandId', 'left');
				$this->db->where("pr.TransDate >=", $from_date.' 00:00:00');
				$this->db->where("pr.TransDate <=", $to_date.' 23:59:59');
				$this->db->where("pr.FY", $fy);
				$this->db->where("pr.TType", "SR");
				$this->db->where("pr.PlantID", $selected_company);
				if(!empty($data['CenterID'])) $this->db->where("pr.CenterID", $data['CenterID']);
				if(!empty($data['AccountID'])) $this->db->where("pr.AccountID", $data['AccountID']);
				if(!empty($data['ItemID'])) $this->db->where("pr.ItemID", $data['ItemID']);

				$this->db->order_by("pr.TransDate", "DESC");

			}
			return $this->db->get()->result_array();
		}
		
	}		