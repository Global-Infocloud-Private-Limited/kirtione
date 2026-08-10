<?php
defined('BASEPATH') or exit('No direct script access allowed');
class KirtiOneOrderModel extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function get_company_detail()
	{
		$selected_company = $this->session->userdata('root_company');
		$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
		$result = $this->db->query($sql)->row();
		return $result;
	}
	
	public function AutoReceiptsGenerate()
	{
	    $PlantID = 1;
	    $FY = "25";
	    $fromDate = '2025-04-01 00:00:00';
        $toDate   = '2026-03-31 23:59:59';
        $TType = array('O');
        $TType2 = array("SALE");
        /*$TType = array('SR');
        $TType2 = array("DAMAGE RETURN","FRESH RETURN");*/
	    $taxNos = ['TAX2511105780', 'TAX2511105781', 'TAX2511105782', 'TAX2511105783', 'TAX2511105784', 'TAX2511106018', 
	    'TAX2511113558', 'TAX2511113560', 'TAX2511110922', 'TAX2511113559', 
	    'TAX2511113561', 'TAX2511113562', 'TAX2511113524', 'TAX2511104667', 'TAX2511104668', 'TAX2511104669'];
	    $this->db->select("SUM(K1h.BasicRate * K1h.BilledQty) AS ItemAmt,SUM(K1h.DiscAmt) AS DiscAmt,
	    SUM(K1h.cgstamt) AS cgstamt,SUM(K1h.sgstamt) AS sgstamt,SUM(K1h.igstamt) AS igstamt,K1h.AccountID,K1h.TransDate,
	    K1h.UserID,K1h.TransID,K1h.OrderID,K1h.CenterID");
	    $this->db->where('K1h.PlantID', $PlantID);
	    $this->db->where('K1h.FY', $FY);
	    $this->db->where_in('K1h.TType', $TType);
	    $this->db->where_in('K1h.TType2', $TType2);
	    $this->db->where_not_in('K1h.TransID', $taxNos);
	    $this->db->where('K1h.TransDate >=', $fromDate);
	    //$this->db->where('K1h.BilledQty >=', 0);
        $this->db->where('K1h.TransDate <=', $toDate);
	    $this->db->group_by('K1h.OrderID');
		$this->db->from('tblK1history K1h');
		$HistoryDetails = $this->db->get()->result_array();
		echo "<pre>";
			
		$Total = 0;
		foreach($HistoryDetails as $key=>$val){
			$TaxableAmt = $val["ItemAmt"] - $val["DiscAmt"];
			$GSTAmt = $val["sgstamt"] + $val["cgstamt"]+ $val["igstamt"];
			$NetAmt = $TaxableAmt + $GSTAmt;
			$Total += $NetAmt;
			$date = substr($val["TransDate"],0,10);
			$PassedForm = "RECEIPTS";
			//$PassedForm = "PAYMENTS";
			$LastUniqueID = $this->generateNextVoucherIDNew($date, $PlantID, $PassedForm);
			//$Narration = "Payment collected against " . $val["TransID"] . " / " . $val["OrderID"];
			$Narration = "Receipt generated for payment received against Invoice ".$val["TransID"]." / Order ".$val["OrderID"].".";
			//$Narration = "Payment generated for payment paid against Invoice ".$val["TransID"]." / Order ".$val["OrderID"].".";
			$srNo = 1;
			// Credit Farmer Account
			$credit_data = array(
				"PlantID"        => $PlantID,
				"FY"             => $FY,
				"Transdate"      => $val["TransDate"],
				"TransDate2"     => $val["TransDate"],
				"VoucherID"      => $LastUniqueID,
				"AccountID"      => $val["AccountID"],
				"CounterAccount" => "RETS",
				"TType"          => "C",
				"CenterID"       => $val["CenterID"],
				"CommodityID"    => NULL,
				"EntryFor"       => 3,
				"PartyID"        => "KASPL",
				"bill_no"        => $val["TransID"],
				"Amount"         => $NetAmt,
				"Narration"      => $Narration,
				"PassedFrom"     => $PassedForm,
				"OrdinalNo"      => $srNo,
				"UserID"         => $val["UserID"],
			);
			//print_r($credit_data);
			//$this->db->insert(db_prefix() . 'accountledger', $credit_data);
			// Debit Amt to Control Account
			$debit_data = array(
				"PlantID"        => $PlantID,
				"FY"             => $FY,
				"Transdate"      => $val["TransDate"],
				"TransDate2"     => $val["TransDate"],
				"VoucherID"      => $LastUniqueID,
				"AccountID"      => "RETS",
				"CounterAccount" => $val["AccountID"],
				"TType"          => "D",
				"CenterID"       => $val["CenterID"],
				"CommodityID"    => NULL,
				"EntryFor"       => 3,
				"PartyID"        => "KASPL",
				"bill_no"        => $val["TransID"],
				"Amount"         => $NetAmt,
				"Narration"      => $Narration,
				"PassedFrom"     => $PassedForm,
				"OrdinalNo"      => $srNo,
				"UserID"         => $val["UserID"],
			);
			//$this->db->insert(db_prefix() . 'accountledger', $debit_data);
			//print_r($debit_data);
			//echo $LastUniqueID;
			//echo "<br>";
			//echo $NetAmt;
		}
		echo $Total;
		die;
	}
	public function GetDataCorrection()
	{
		$this->db->select('tblK1salesmaster.SalesID,tblK1salesmaster.CashAmt,tblK1salesmaster.OnlineAmt,tblK1salesmaster.BillAmt,
			SaleAmt,CenterID,Transdate,OtherAmt,Effecton,tblK1salesmaster.AccountID,FY,PlantID,ChallanID,UserID');
		$this->db->from('tblK1salesmaster');
		$SaleList = $this->db->get()->result_array();
		
		$this->db->select("tblaccountledger.*");
		$this->db->from('tblaccountledger');
		$this->db->where('tblaccountledger.TType', "D");
		$this->db->where('tblaccountledger.PassedFrom', "RECEIPTS");
		$this->db->where('tblaccountledger.PartyID', "KASPL");
		$RECEIPTSList = $this->db->get()->result_array();
		
		$this->db->select("tblaccountledger.*");
		$this->db->from('tblaccountledger');
		$this->db->where('tblaccountledger.TType', "C");
		$this->db->where('tblaccountledger.PassedFrom', "SALES");
		$this->db->where('tblaccountledger.PartyID', "KASPL");
		$SalesVoucherList = $this->db->get()->result_array();
		
		$this->db->select("tblK1history.*");
		$this->db->from('tblK1history');
		$HistoryDetails = $this->db->get()->result_array();
		echo "<pre>";
		$Sr = 1;
		/*// History and Sales Master Correction
			foreach ($SaleList as $key=>$val) {
			    $HAmt = 0;
			    foreach ($HistoryDetails as $Hkey=>$HVal) {
			        if ($HVal["TransID"] == $val["SalesID"]) {
			            $HAmt += $HVal["OrderAmt"];
			        }
			    }
			    if ($HAmt != $val["BillAmt"]) {
			        echo $Sr. " => ".$val["SalesID"]. " => ".$val["BillAmt"]." => ".$HAmt;
			        echo "<br>";
			        $Sr++;
			    }
			}*/
		/*
			// Check Extra Receipts
			foreach ($RECEIPTSList as $Rkey=>$RVal) {
			    foreach ($SaleList as $key=>$val) {
			        if (str_contains($RVal["Narration"], $val["SalesID"])) {
			            $Match++;
			        }
			    }
			    if ($Match <= 0) {
			        echo $Sr. " => ".$RVal["VoucherID"]. " => ".$RVal["Narration"]." => ".$RVal["Amount"];
			        echo "<br>";
			        $Sr++;
			    }
			}*/
		/*
			// Correction Sale Ledger
			foreach ($SaleList as $key=>$val) {
			    $VAmt = 0;
			    foreach ($SalesVoucherList as $Rkey=>$RVal) {
			        if ($RVal["VoucherID"] == $val["SalesID"]) {
			            $VAmt += $RVal["Amount"];
			        }
			    }
			    if ($VAmt != $val["SaleAmt"]) {
			        echo $Sr. " => ".$val["SalesID"]. " => ".$val["SaleAmt"]." => ".$VAmt;
			        echo "<br>";
			        $Sr++;
			    }
			}*/
		// Check Bill Amt and Cash Amt, Online Amt
		/*foreach($SaleList as $key=>$val){
			    $RAmt = 0;
			    foreach ($RECEIPTSList as $RKey=>$RVal) {
			        if (str_contains($RVal["Narration"], $val["SalesID"])) {
			            $RAmt += $RVal["Amount"];
			        }
			    }
			    $TotalAmt = $val["CashAmt"] + $val["OnlineAmt"];
			    if ($TotalAmt == ($val["BillAmt"] + $val["OtherAmt"]) && $val["OnlineAmt"] <=0 && $val["CashAmt"] <=0) {
			        $arr = array(
			            "CashAmt"  =>$val["BillAmt"] + $val["OtherAmt"],
			            // "OnlineAmt"=>0,
			            "Effecton" =>"CASH"
			        );
			        $this->db->where('tblK1salesmaster.SalesID', $val["SalesID"]);
			        $this->db->update('tblK1salesmaster', $arr);
			        echo $Sr. " => ".$val["SalesID"]. " => ".($val["BillAmt"] + $val["OtherAmt"])." => ".$val["Effecton"]." => ".$val["CashAmt"]." => ".$val["OnlineAmt"]." => ".$RAmt;
			        echo "<br>";
			        $Sr++;
			    }
			}*/
		$NextReceiptsNumber = get_option('next_receipts_number_for_kirti');
		// Generate Receipts against Sale
		foreach ($SaleList as $key => $val) {
			$TotalAmt = $val["CashAmt"] + $val["OnlineAmt"];
			$BillTotal = $val["BillAmt"] + $val["OtherAmt"];
			if ($TotalAmt == $BillTotal && $val["BillAmt"] > 0) {
				// if ($val["BillAmt"] >0) {
				// print_r($val);
				$Narration = "By SalesID " . $val["SalesID"] . " / " . $val["ChallanID"];
				$srNo = 1;
				$credit_data = array(
					"PlantID"        => $val["PlantID"],
					"FY"             => $val["FY"],
					"Transdate"      => $val["Transdate"],
					"TransDate2"     => $val["Transdate"],
					"VoucherID"      => $NextReceiptsNumber,
					"AccountID"      => $val["AccountID"],
					"CounterAccount" => $val['Effecton'],
					"TType"          => "C",
					"CenterID"       => $val["CenterID"],
					"CommodityID"    => NULL,
					"EntryFor"       => 3,
					"PartyID"        => "KASPL",
					"bill_no"        => $val["SalesID"],
					"Amount"         => $val["BillAmt"],
					"Narration"      => $Narration,
					"PassedFrom"     => "RECEIPTS",
					"OrdinalNo"      => $srNo,
					"UserID"         => $val["UserID"],
				);
				$this->db->insert(db_prefix() . 'accountledger', $credit_data);
				// print_r($credit_data);
				$srNo++;
				// Cash Debit
				if ($val["CashAmt"] > 0) {
					$debitCash_data = array(
						"PlantID"        => $val["PlantID"],
						"FY"             => $val["FY"],
						"Transdate"      => $val["Transdate"],
						"TransDate2"     => $val["Transdate"],
						"VoucherID"      => $NextReceiptsNumber,
						"AccountID"      => "CASH",
						"CounterAccount" => $val['AccountID'],
						"TType"          => "D",
						"CenterID"       => $val["CenterID"],
						"CommodityID"    => NULL,
						"EntryFor"       => 3,
						"PartyID"        => "KASPL",
						"bill_no"        => $val["SalesID"],
						"Amount"         => $val["CashAmt"],
						"Narration"      => $Narration,
						"PassedFrom"     => "RECEIPTS",
						"OrdinalNo"      => $srNo,
						"UserID"         => $val["UserID"],
					);
					$srNo++;
					$this->db->insert(db_prefix() . 'accountledger', $debitCash_data);
					// print_r($debitCash_data);
				}
				// Online Debit
				if ($val["OnlineAmt"] > 0) {
					$debitOnline_data = array(
						"PlantID"        => $val["PlantID"],
						"FY"             => $val["FY"],
						"Transdate"      => $val["Transdate"],
						"TransDate2"     => $val["Transdate"],
						"VoucherID"      => $NextReceiptsNumber,
						"AccountID"      => "CBI",
						"CounterAccount" => $val['AccountID'],
						"TType"          => "D",
						"CenterID"       => $val["CenterID"],
						"CommodityID"    => NULL,
						"EntryFor"       => 3,
						"PartyID"        => "KASPL",
						"bill_no"        => $val["SalesID"],
						"Amount"         => $val["OnlineAmt"],
						"Narration"      => $Narration,
						"PassedFrom"     => "RECEIPTS",
						"OrdinalNo"      => $srNo,
						"UserID"         => $val["UserID"],
					);
					$this->db->insert(db_prefix() . 'accountledger', $debitOnline_data);
					// print_r($debitOnline_data);
				}
				$NextReceiptsNumber++;
				// $this->increment_next_receipts_number();
				echo $Sr . " => " . $val["SalesID"] . " => " . ($val["BillAmt"] + $val["OtherAmt"]) . " => " . $val["Effecton"] . " => " . $val["CashAmt"] . " => " . $val["OnlineAmt"];
				echo "<br>";
				$Sr++;
			}
		}
		// print_r($RECEIPTSList);
		return $Data;
	}
	public function increment_next_receipts_number()
	{
		// Update next receipts number in settings
		$FY = $this->session->userdata('finacial_year');
		$this->db->where('name', 'next_receipts_number_for_kirti');
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	public function GetAccountListVendorwise($VendorID)
	{
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$this->db->select('tblclients.*, tblxx_statelist.state_name, tblxx_statelist.short_name');
		$this->db->from(db_prefix() . 'clients');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblclients.state', 'LEFT');
		$this->db->where(db_prefix() . 'clients.AccountID', $VendorID);
		$Data = $this->db->get()->row();
		if (!empty($Data)) {
			$this->db->select("tblShippingDetails.*, tblxx_statelist.state_name, tblxx_citylist.city_name, CONCAT(IFNULL(House, ''), ', ',IFNULL(Street, ''), ', ',IFNULL(Locality, ''), ', ',IFNULL(Block, ''), ', (', IFNULL(state_name, ''), ' - ',IFNULL(city_name, ''), ', ',IFNULL(Pincode, ''), ')') AS shipping_label");
			$this->db->from(db_prefix() . 'ShippingDetails');
			$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblShippingDetails.State', 'LEFT');
			$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblShippingDetails.District', 'LEFT');
			$this->db->where(db_prefix() . 'ShippingDetails.AccountID', $VendorID);
			$ShippingDetails = $this->db->get()->result_array();
			$Data->ShippingData = $ShippingDetails;
			// Closing Bal
			// Get Opening balance
			$this->db->select("tblaccountbalances.*");
			$this->db->from(db_prefix() . 'accountbalances');
			$this->db->where(db_prefix() . 'accountbalances.AccountID', $VendorID);
			$this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountbalances.FY', $FY);
			$this->db->where(db_prefix() . 'accountbalances.PartyID', "KASPL");
			$OpnBalDetails = $this->db->get()->row();
			$OpnBal = 0;
			if ($OpnBalDetails) {
				$OpnBal = $OpnBalDetails->BAL1;
			}
			// Get Transaction Entry
			$this->db->select("SUM(tblaccountledger.Amount) AS TotalAmt,tblaccountledger.TType");
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where(db_prefix() . 'accountledger.AccountID', $VendorID);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountledger.FY', $FY);
			$this->db->where(db_prefix() . 'accountledger.PartyID', "KASPL");
			$this->db->group_by(db_prefix() . 'accountledger.TType');
			$LedgerDetails = $this->db->get()->result_array();
			$CreditAmt = 0;
			$DebitAmt = 0;
			foreach ($LedgerDetails as $key => $val) {
				if ($val["TType"] == "C") {
					$CreditAmt = $val["TotalAmt"];
				} else if ($val["TType"] == "D") {
					$DebitAmt = $val["TotalAmt"];
				}
			}
			$ClosingBal = $OpnBal + $CreditAmt - $DebitAmt;
			$Data->Bal = $ClosingBal;
		}
		return $Data;
	}
	public function getShippingDetails($id = '', $accountid = '')
	{
		if (!empty($id)) {
			$this->db->select("tblShippingDetails.*, tblxx_statelist.state_name, tblxx_statelist.id AS statecode, tblxx_citylist.city_name, CONCAT(IFNULL(House, ''), ', ',IFNULL(Street, ''), ', ',IFNULL(Locality, ''), ', ',IFNULL(Block, ''), ', (', IFNULL(state_name, ''), ' - ',IFNULL(city_name, ''), ', ',IFNULL(Pincode, ''), ')') AS shipping_label");
			$this->db->from(db_prefix() . 'ShippingDetails');
			$this->db->join('tblxx_statelist', '(tblxx_statelist.short_name = tblShippingDetails.State) || (tblxx_statelist.state_name = tblShippingDetails.State)', 'LEFT');
			$this->db->join('tblxx_citylist', '(tblxx_citylist.id = tblShippingDetails.District) || (tblxx_citylist.city_name = tblShippingDetails.District)', 'LEFT');
			$this->db->where(db_prefix() . 'ShippingDetails.id', $id);
			$ShippingDetails = $this->db->get()->row();
			return $ShippingDetails;
		}
		if (!empty($accountid)) {
			$this->db->select("tblclients.*, tblclients.zip as Pincode, tblxx_statelist.state_name, tblxx_statelist.id AS statecode, tblxx_citylist.city_name, CONCAT(IFNULL(house, ''), ', ',IFNULL(street, ''), ', ',IFNULL(loc, ''), ', ',IFNULL(po, ''), ', (', IFNULL(state_name, ''), ' - ',IFNULL(city_name, ''), ', ',IFNULL(Pincode, ''), ')') AS shipping_label");
			$this->db->from(db_prefix() . 'clients');
			$this->db->join('tblxx_statelist', '(tblxx_statelist.short_name = tblclients.state) || (tblxx_statelist.state_name = tblclients.state)', 'LEFT');
			$this->db->join('tblxx_citylist', '(tblxx_citylist.id = tblclients.dist) || (tblxx_citylist.city_name = tblclients.dist)', 'LEFT');
			$this->db->where(db_prefix() . 'clients.AccountID', $accountid);
			$ShippingDetails = $this->db->get()->row();
			return $ShippingDetails;
		}
	}
	public function GetShippingListVendorwise($VendorID)
	{
		$this->db->select("tblShippingDetails.*, tblxx_statelist.state_name, tblxx_citylist.city_name, GROUP_CONCAT(CONCAT(IFNULL(House, ''), ', ',IFNULL(Street, ''), ', ',IFNULL(Locality, ''), ', ',IFNULL(Block, ''), ', (', IFNULL(state_name, ''), ' - ',IFNULL(city_name, ''), ', ',IFNULL(Pincode, ''), ')') SEPARATOR '\n') AS shipping_label");
		$this->db->from(db_prefix() . 'ShippingDetails');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblShippingDetails.State', 'LEFT');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblShippingDetails.District', 'LEFT');
		$this->db->where(db_prefix() . 'ShippingDetails.AccountID', $VendorID);
		$this->db->group_by(db_prefix() . 'ShippingDetails.id');
		$ShippingDetails = $this->db->get()->result_array();
		return $ShippingDetails;
	}
	public function get_data($tbl, $where)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->row_array();
	}
	public function GetAccountDetails($AccountID)
	{
		$this->db->select("tblclients.*, tblGstRecord.gstin");
		$this->db->from(db_prefix() . 'clients');
		$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
		$AccountDetails = $this->db->get()->row_array();
		return $AccountDetails;
	}
	public function insert_data($tbl, $data)
	{
		$this->db->insert($tbl, $data);
		return $this->db->insert_id();
	}
	public function edit_data($tbl, $where, $arr)
	{
		$this->db->where($where);
		if ($this->db->update($tbl, $arr)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function get_all_table_data($tbl)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_all_data($tbl, $where)
	{
		$this->db->select('*');
		$this->db->from($tbl);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_all_data_orderby($tablename, $orderBy = '', $where)
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
	public function get_data_for_account_bal($AccountID)
	{
		$this->load->model('currencies_model');
		$currency = $this->currencies_model->get_base_currency();
		$acc_show_account_numbers = get_option('acc_show_account_numbers');
		$selected_company = $this->session->userdata('root_company');
		$finacial_year = $this->session->userdata('finacial_year');
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $finacial_year);
		if (isset($AccountID)) {
			$this->db->where('AccountID', $AccountID);
		}
		$accounts = $this->db->get(db_prefix() . 'accountbalances')->row();
		return $accounts;
	}
	public function get_data_general_ledger2($AccountID)
	{
		$this->load->model('currencies_model');
		$currency = $this->currencies_model->get_base_currency();
		$acc_show_account_numbers = get_option('acc_show_account_numbers');
		$finacial_year = $this->session->userdata('finacial_year');
		$from_date = date('20' . $finacial_year . '-04-01');
		$to_date = date('Y-m-d');
		$selected_company = $this->session->userdata('root_company');
		$username = $this->session->userdata('username');
		$this->db->where('PlantID', $selected_company);
		$this->db->where('AccountID', $AccountID);
		$accounts_details = $this->db->get(db_prefix() . 'clients')->row();
		// get permission
		$this->db->where('PlantID', $selected_company);
		$this->db->where('AccountID', $AccountID);
		$this->db->where('UserID', $username);
		$permission_details = $this->db->get(db_prefix() . 'nsaccountmaster')->row();
		if (isset($accounts_details->no_show) && $accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']) {
			return $accounts_details->no_show;
		} else {
			$this->db->select(db_prefix() . 'accountledger.*,tblclients.company');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'accountledger.CounterAccount AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'accountledger.PlantID ', 'LEFT');
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountledger.AccountID', $AccountID);
			$this->db->LIKE(db_prefix() . 'accountledger.FY', $finacial_year);
			$this->db->WHERE(db_prefix() . 'accountledger.Transdate>=', $from_date . ' 00:00:00');
			$this->db->WHERE(db_prefix() . 'accountledger.Transdate<=', $to_date . ' 23:59:59');
			$this->db->order_by(db_prefix() . 'accountledger.Transdate', "asc");
			$query = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $query;
		}
	}
	public function getstatelist()
	{
		$Data = $this->db->get('tblxx_statelist')->result_array();
		return $Data;
	}
	public function get_items_code()
	{
		$selected_company = $this->session->userdata('root_company');
		return $this->db->query('SELECT ProductID as id, CONCAT(ProductID, " - ", ProductName) as label,ProductName ,ProductID FROM ' . db_prefix() . 'product WHERE PlantID = ' . $selected_company)->result_array();
	}
	public function get_items_code_by_categorytype($CategoryType)
	{
		if ($CategoryType == "Grocery") {
			$Category = array('6', '8', '11');
		} elseif ($CategoryType == "Non Grocery") {
			$Category = array('1', '2', '3', '7','9','10');
		} else {
			$Category = array();
		}
		$selected_company = $this->session->userdata('root_company');
		// Build base query
		$this->db->select('ProductID as id, CONCAT(ProductID, " - ", ProductName) as label, ProductName, ProductID');
		$this->db->from(db_prefix() . 'product');
		$this->db->where('PlantID', $selected_company);
		// Apply category filter if not empty
		if (!empty($Category)) {
			$this->db->where_in('Category', $Category); // Make sure this field exists in your table
		}
		return $this->db->get()->result_array();
	}
	// public function GetItemDetails($ItemID, $CenterID = "")
	// {
	// 	$this->db->select('tblproduct.*, tblproduct.ProductName, tblbrands.BrandName, tbltaxes.taxrate, tblK1RateMaster.sale_rate');
	// 	$this->db->from(db_prefix() . 'product');
	// 	$this->db->join(db_prefix() . 'brands', db_prefix() . 'brands.id = ' . db_prefix() . 'product.BrandId');
	// 	$this->db->join(db_prefix() . 'K1RateMaster', db_prefix() . 'K1RateMaster.ItemID = ' . db_prefix() . 'product.ProductID AND tblK1RateMaster.CenterID = "' . $CenterID . '"', "LEFT");
	// 	$this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'product.gst');
	// 	$this->db->where(db_prefix() . 'product.ProductID', $ItemID);
	// 	$rs = $this->db->get()->row();
	// 	return $rs;
	// }
	public function GetItemDetails($ItemID, $CenterID = "")
	{
		$this->db->select('tblproduct.*, tblproduct.ProductName, tblbrands.BrandName, tbltaxes.taxrate, K1.sale_rate');
		$this->db->from(db_prefix() . 'product');
		$this->db->join(db_prefix() . 'brands', db_prefix() . 'brands.id = ' . db_prefix() . 'product.BrandId');
		$subQuery = '(SELECT * FROM ' . db_prefix() . 'K1RateMaster 
									WHERE ItemID = ' . $ItemID . ' 
									AND CenterID = "' . $CenterID . '" 
									ORDER BY id DESC LIMIT 1) AS K1';
		$this->db->join($subQuery, 'K1.ItemID = ' . db_prefix() . 'product.ProductID', 'LEFT');
		$this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'product.gst');
		$this->db->where(db_prefix() . 'product.ProductID', $ItemID);
		$rs = $this->db->get()->row();
		return $rs;
	}
	public function AddKirtiOneSaleOrder($data)
	{
		if (isset($data['sale_invoice_detail'])) {
			$sale_invoice_detail = json_decode($data['sale_invoice_detail']);
			unset($data['sale_invoice_detail']);
			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];
			$header[] = 'ProductID';
			$header[] = 'HsnCode';
			$header[] = 'Brand';
			$header[] = 'MeasuredIn';
			$header[] = 'PackingQty';
			$header[] = 'GSTApply';
			$header[] = 'SaleUnit';
			$header[] = 'BatchNo';
			$header[] = 'StockQty';
			$header[] = 'Qty';
			$header[] = 'Amount';
			$header[] = 'Discount';
			$header[] = 'GST';
			$header[] = 'CGSTAMT';
			$header[] = 'SGSTAMT';
			$header[] = 'IGSTAMT';
			$header[] = 'NetAmount';
			$header[] = 'ExpDate';
			foreach ($sale_invoice_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		/*echo "<pre>";
			print_r($data);
			print_r($es_detail);
			die;*/
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$UserID = $this->session->userdata('username');
		$PostedDate = $data['posted_date'];
		// $date = DateTime::createFromFormat('d/m/Y', $PostedDate);
		$formattedDate =  to_sql_date($PostedDate) . " " . date('H:i:s');
		$nextK1OrderNumber = get_option('next_K1Order_number_for_kirti');
		$OrderId = "ORD" . $fy . $nextK1OrderNumber;
		$AccountId = $data['AccountID'];
		$CenterId = $data['centername'];
		$TotalCgstAmt = $data['total_cgst_amt'];
		$TotalSgstAmt = $data['total_sgst_amt'];
		$IgstAmt = $data['total_igst_amt'];
		$TotalValue = $data['Total_value'];
		$TotalDiscountAmt = $data['total_disc_in_mt'];
		$TotalNetPayableAmt =  $data['netpayableamt'];
		$Effecton = $data['Effecton'];
		$OrderType = $data['ordtype'];
		$PaymentMode = $data['paymentmode'];
		$PaymentMethod = $data['paymentmethod'];
		$ReferenceNo = $data['referenceno'];
		$CashAmt = $data['CashAmt'];
		$OnlineAmt = $data['OnlineAmt'];
		$OtherAmt = $data['OtherAmt'];
		$OthEffectOn = $data['OthEffectOn'];
		$FarmerID = $data['FarmerID'];
		$FarmerAadhaar = $data['FarmerAadhaar'];
		$VillageName = $data['villagename'];
		$CategoryType = $data['CategoryType'];
		$delivery_type = $data['type'];
		$ordtype = $data['ordtype'];
		if (($CashAmt + $OnlineAmt) != $TotalNetPayableAmt && $ordtype == "1") {
			$response = array("status"=>false,"message"=>"The payment amount does not match the total net payable amount.");
		        return $response;
		}
		// check stock
		//echo "<pre>";
	    $StockNotAvl = 0;
	    foreach ($es_detail as $index => $row) {
			if (!empty($row['ProductID'])) {
				$productId = $row['ProductID'];
				$BatchNo = $row['BatchNo'];
				$Qty = $row['Qty'];
				$Unit = $row['MeasuredIn'];
				$SaleUnit = $row['SaleUnit'];
				if ($Unit == $SaleUnit) {
					$BilledQty = $Qty * $PackQty;
				} else {
					$BilledQty = $Qty;
				}
				$filterdata = [
                	'ItemID'   => $productId,
                	'CenterID' => $CenterId,
                	'BatchID'  => $BatchNo,
                ];
                $ItemWiseBatchList = $this->GetItemBatchListWithStockDSO($filterdata);
                /*if($UserID == "GIC"){
                    echo "<pre>";
                    print_r($ItemWiseBatchList);
                    die;
                }*/
                //die;
                $StockQty = number_format($ItemWiseBatchList[0]["Stock"], 2);
                if($BilledQty > $StockQty){
                    $StockNotAvl++;
                }
			}
	    }
	    if($StockNotAvl > 0){
	        $response = array("status"=>false,"message"=>"Insufficient stock is available for one or more order items. Please review the item quantities and try again.");
	        //return $response;
	    }
		if ($delivery_type == '2') {
			$ShippingID = $data['ShippingID'];
			if ($ShippingID == 'new') {
				$insert_address = array(
					'AccountID' => $AccountId,
					'House'     => $data['ShippingHouse'],
					'Street'    => $data['ShippingStreet'],
					'Locality'  => $data['ShippingLocality'],
					'Block'     => $data['ShippingBlock'],
					'Pincode'   => $data['ShippingPincode'],
					'State'     => $data['ShippingState'],
					'District'  => $data['ShippingCity'],
					"UserID"    => $_SESSION['username'],
					"TransDate" => date('Y-m-d H:i:s'),
				);
				$this->db->insert('tblShippingDetails', $insert_address);
				$ShippingID = $this->db->insert_id();
			}
		} else {
			$ShippingID = null;
		}
		$PartyName = $data['partyname'];
		$nameParts = explode(' ', $PartyName);
		if (count($nameParts) >= 2) {
			$firstName = $nameParts[0];
			$lastName = implode(' ', array_slice($nameParts, 1));
		} else {
			$firstName = $PartyName;
			$lastName = "";
		}
		if ($OrderType == '') {
			$InvoiceType = "CREDIT";
		} else {
			$InvoiceType = "CASH";
		}
		$MobileNo = $data['phonenumber'];
		$BillingState = $data['billstate'];
		$BillNo = $data['billno'];
		$RndAmt = $data['total_roundoff_amt'];
		$RoundAmt = abs($RndAmt);
		if ($OrderType == '2') {
			$paymode = "";
			$paymethod = "";
			$refnumber = "";
		} else if ($OrderType == '1') {
			$paymode = $PaymentMode;
			$paymethod = $PaymentMethod;
			$refnumber = $ReferenceNo;
		}
		if ($AccountId != "" && $AccountId == "new") {
			$next_code = $this->get_next_farmer_code('next_farmer_code');
			$number = 'KF' . str_pad($next_code->value, 4, '0', STR_PAD_LEFT);
			$insert_client_array = array(
				'PlantID'          => $selected_company,
				'AccountID'        => $MobileNo,
				'IsKirtiOneAccess' => "N",
				'company'          => $PartyName,
				'ShortCode'        => $number,
				'CustomerType'     => 1,
				'ActGroupID'       => '10000',
				'SubActGroupID1'   => '100002',
				'SubActGroupID'    => '1000006',
				'AccountFor'       => "Self",
				'phonenumber'      => $MobileNo,
				'state'            => $BillingState,
				'StartDate'        => date('Y-m-d h:i:s'),
				'datecreated'      => date('Y-m-d h:i:s'),
				'UserID'           => $UserID,
				'Aadhaar_ver_man'  => "N",
				'active'           => '1',
			);
			$this->db->insert('tblclients', $insert_client_array);
			if ($this->db->affected_rows() > 0) {
				$this->increment_next_farmer_number();
				$insert_contacts = array(
					'PlantID'     => $selected_company,
					'AccountID'   => $MobileNo,
					'firstname'   => $firstName,
					'lastname'    => $lastName,
					'gender'      => "M",
					'phonenumber' => $MobileNo,
					'datecreated' => date('Y-m-d h:i:s'),
					'active'      => '1',
				);
				$this->db->insert('tblcontacts', $insert_contacts);
				$AccountId = $MobileNo;
			}
		}
		$clients = $this->GetAccountDetails($AccountId);
		$GSTIN = NULL;
		if ($clients['gstin']) {
			$GSTIN = $clients['gstin'];
		}
		$CenterData = $this->GetCenterByCenterID($CenterId);
		$nextOrdernumber = get_option('next_K1Order_number_for_kirti');
		$nextChallannumber = get_option('next_K1Challan_number_for_kirti');
		$nextTaxNumber = get_option('next_K1Tax_number_for_kirti');
		$prefixchallan = "CHL";
		$kirtione = 1;
		$ConcatenatedChallanNumber = $prefixchallan . $fy . $selected_company . $kirtione . $nextChallannumber;
		$prefixTaxNo = "TAX";
		$ConcatenatedTaxNumber = $prefixTaxNo . $fy . $selected_company . $kirtione . $nextTaxNumber;
		$SalesId = $ConcatenatedTaxNumber;
		if ($TotalNetPayableAmt != 0.00) {
			$insert_order = array(
				'PlantID'          => $selected_company,
				'FY'               => $fy,
				'OrderID'          => $OrderId,
				'IsDirectSale'     => 'Y',
				'ChallanID'        => '',
				'SalesID'          => '',
				'Transdate'        => $formattedDate,
				'AccountID'        => $AccountId,
				'CenterID'         => $CenterId,
				'GSTNO'            => $GSTIN,
				'VillageName'      => $VillageName,
				'OrderWeight'      => '0.00',
				'OrderStatus'      => "O",
				'OrderType'        => "TAXITEMS",
				'UserID'           => $UserID,
				'OrderPaymentType' => $OrderType,
				'BIllNo'           => $BillNo,
				'billstate'        => $BillingState,
				'CategoryType'     => $CategoryType,
				'DeliveryType'     => $delivery_type,
				'order_type'       => "WEB",
				'FarmerID'         => $FarmerID,
				'FarmerAadhaar'    => $FarmerAadhaar
			);
			if ($this->db->insert(db_prefix() . 'K1ordermaster', $insert_order)) {
				$this->increment_next_number('next_K1Order_number_for_kirti');
				// insert in history table
				$ordno = 1;
				$OrdSaleAmt = 0;
				$OrdDiscAmt = 0;
				$OrdCgstAmt = 0;
				$OrdSgstAmt = 0;
				$OrdIgstAmt = 0;
				$OrdNetAmt = 0;
				$OrdRoundOffAmt = 0;
				foreach ($es_detail as $index => $row) {
					if (!empty($row['ProductID'])) {
						$productId = $row['ProductID'];
						$ItemDetails = $this->CheckItemFor($productId);
						$Qty = $row['Qty'];
						$Unit = $row['MeasuredIn'];
						$SaleUnit = $row['SaleUnit'];
						$PackQty = $row['PackingQty'];
						$DiscAmt = $row['Discount'];
						$BasicRate = $row['Amount'];
						$NewBasicRate = $row['Amount'];
						$BatchNo = $row['BatchNo'];
						$ItemTotal = 0;
						$TotalDisc = 0;
						$TaxableAmt = 0;
						$DiscPer = 0;
						$CgstPer = 0;
						$CgstAmt = 0;
						$SgstPer = 0;
						$SgstAmt = 0;
						$IgstPer = 0;
						$IgstAmt = 0;
						if ($DiscAmt > 0) {
							$DiscPer = ($DiscAmt / $BasicRate) * 100;
						}
						if ($Unit == $SaleUnit) {
							$NewBasicRate = $BasicRate / $PackQty;
							$BilledQty = $Qty * $PackQty;
							$CaseQty = $PackQty;
						} else {
							$BilledQty = $Qty;
							$NewBasicRate = $BasicRate;
							$CaseQty = 1;
						}
						if ($row['GSTApply'] == "Including") {
							$SaleRate = $NewBasicRate;
						} else {
							$SaleRate = $NewBasicRate + ($NewBasicRate * ($ItemDetails->gst / 100));
						}
						$TotalDisc = $DiscAmt * $Qty;
						$ItemTotal = $NewBasicRate * $BilledQty;
						$TaxableAmt = $ItemTotal - $TotalDisc;
						if ($row['GSTApply'] == "Excluding") {
							if ($CenterData->state == $BillingState) {
								$CgstPer = $ItemDetails->gst / 2;
								$SgstPer = $ItemDetails->gst / 2;
								$CgstAmt = $TaxableAmt * ($CgstPer / 100);
								$SgstAmt = $TaxableAmt * ($SgstPer / 100);
							} else {
								$IgstPer = $ItemDetails->gst;
								$IgstAmt = $TaxableAmt * ($IgstPer / 100);
							}
						} else {
							if ($CenterData->state == $BillingState) {
								$CgstPer = $ItemDetails->gst / 2;
								$SgstPer = $ItemDetails->gst / 2;
							} else {
								$IgstPer = $ItemDetails->gst;
							}
						}
						$ItemNetAmt = $TaxableAmt + $CgstAmt + $SgstAmt + $IgstAmt;
						$OrdSaleAmt += $ItemTotal;
						$OrdDiscAmt += $TotalDisc;
						$OrdCgstAmt += $CgstAmt;
						$OrdSgstAmt += $SgstAmt;
						$OrdIgstAmt += $IgstAmt;
						$OrdNetAmt  += $ItemNetAmt;
						$itemDetails = array(
							'PlantID'       => $selected_company,
							'FY'            => $fy,
							'OrderID'       => $OrderId,
							'BillID'        => '',
							'TransID'       => '',
							'TransDate'     => $formattedDate,
							'TransDate2'    => $formattedDate,
							'TType'         => "O",
							'TType2'        => "SALE",
							'AccountID'     => $AccountId,
							'ItemID'        => $productId,
							'CenterID'      => $CenterId,
							'GodownID'      => 'RET',
							'PartyID'       => "KASPL",
							'ChamberID'     => '',
							'StackID'       => '',
							'LOTID'         => '',
							'PurchRate'     => $NewBasicRate,
							'SaleRate'      => $SaleRate,
							'BasicRate'     => $NewBasicRate,
							'SuppliedIn'    => $Unit,
							'OrderQty'      => $BilledQty,
							'eOrderQty'     => '',
							'BilledQty'     => $BilledQty,
							'DiscPerc'      => $DiscPer,
							'DiscAmt'       => $TotalDisc,
							'cgst'          => $CgstPer,
							'cgstamt'       => $CgstAmt,
							'sgst'          => $SgstPer,
							'sgstamt'       => $SgstAmt,
							'igst'          => $IgstPer,
							'igstamt'       => $IgstAmt,
							'CaseQty'       => $CaseQty,
							'Cases'         => 0.00,
							'OrderAmt'      => $ItemTotal,
							'ChallanAmt'    => $ItemTotal,
							'NetOrderAmt'   => $ItemNetAmt,
							'NetChallanAmt' => $ItemNetAmt,
							'BatchNo'       => $BatchNo,
							'ExpDate'       => to_sql_date($row['ExpDate']),
							'Ordinalno'     => $ordno,
							'rowid'         => 0,
							'UserID'        => $UserID,
							'cnfid'         => '',
							'reason'        => ''
						);
						$this->db->insert(db_prefix() . 'K1history', $itemDetails);
						$ordno++;
						// LeanMark Entry
						if ($ItemDetails->ItemFor != 'KASPL') {
							$ItemEntry = array(
								'PlantID'       => $selected_company,
								'FY'            => $fy,
								'OrderID'       => $OrderId,
								'BillID'        => '',
								'TransID'       => '',
								'TransDate'     => $formattedDate,
								'TransDate2'    => $formattedDate,
								'TType'         => "L",
								'TType2'        => "LIENMARK",
								'AccountID'     => $AccountId,
								'ItemID'        => $productId,
								'CenterID'      => $CenterId,
								'GodownID'      => 'RET',
								'PartyID'       => $ItemDetails->ItemFor,
								'ChamberID'     => '',
								'StackID'       => '',
								'LOTID'         => '',
								'PurchRate'     => $NewBasicRate,
								'SaleRate'      => $SaleRate,
								'BasicRate'     => $NewBasicRate,
								'SuppliedIn'    => $Unit,
								'OrderQty'      => $BilledQty,
								'eOrderQty'     => '',
								'BilledQty'     => $BilledQty,
								'DiscPerc'      => $DiscPer,
								'DiscAmt'       => $TotalDisc,
								'cgst'          => $CgstPer,
								'cgstamt'       => $CgstAmt,
								'sgst'          => $SgstPer,
								'sgstamt'       => $SgstAmt,
								'igst'          => $IgstPer,
								'igstamt'       => $IgstAmt,
								'CaseQty'       => $CaseQty,
								'Cases'         => 0.00,
								'OrderAmt'      => $ItemTotal,
								'ChallanAmt'    => $ItemTotal,
								'NetOrderAmt'   => $ItemNetAmt,
								'NetChallanAmt' => $ItemNetAmt,
								'BatchNo'       => $BatchNo,
								'ExpDate'       => to_sql_date($row['ExpDate']),
								'Ordinalno'     => $ordno,
								'rowid'         => 0,
								'UserID'        => $UserID,
								'cnfid'         => '',
								'reason'        => ''
							);
							$this->db->insert(db_prefix() . 'K1history', $ItemEntry);
							$ordno++;
						}
					}
				}
				if ($OtherAmt > 0) {
					$OrdNetAmt += $OtherAmt;
				}
				$BillAmt = round($OrdNetAmt);
				$OrdRoundOffAmt = $BillAmt - $OrdNetAmt;
				$SaleLedgerAmount = $OrdSaleAmt;
				// insert in sales table
				$add_entry_sales = array(
					'PlantID'          => $selected_company,
					'FY'               => $fy,
					'BT'               => 'T',
					'InvoiceType'      => $InvoiceType,
					'SalesID'          => $SalesId,
					'Transdate'        => $formattedDate,
					'OrderID'          => $OrderId,
					'ChallanID'        => '',
					'PartyID'          => "KASPL",
					'AccountID'        => $AccountId,
					'ShipTo'           => '',
					'CenterID'         => $CenterId,
					'WHID'             => '',
					'BrokerID'         => '',
					'GSTIN'            => $GSTIN,
					'DeliveryType'     => $delivery_type,
					'ShippingID'       => $ShippingID,
					'SaleAmt'          => $OrdSaleAmt,
					'DiscAmt'          => $OrdDiscAmt,
					'sgstamt'          => $OrdCgstAmt,
					'cgstamt'          => $OrdSgstAmt,
					'igstamt'          => $OrdIgstAmt,
					'OtherAmt'         => $OtherAmt,
					'EffectOnOtherAmt' => $OthEffectOn,
					'RefNo'            => $refnumber,
					'CashAmt'          => $CashAmt,
					'OnlineAmt'        => $OnlineAmt,
					'Effecton'         => $Effecton,
					'BillAmt'          => $OrdNetAmt,
					'RndAmt'           => $BillAmt,
					'ItCount'          => 0,
					'UserID'           => $UserID,
					'ewaybill_no'      => '',
					'tcs'              => 0.00,
					'tcsAmt'           => 0.00,
					'irn'              => '',
					'Qrcode'           => '',
					'QRImg'            => '',
					'ackno'            => '',
					'TransportID'      => '',
					'vehicleno'        => '',
				);
				if ($this->db->insert(db_prefix() . 'K1salesmaster', $add_entry_sales)) {
					$this->increment_next_number('next_K1Tax_number_for_kirti');
				}
				// insert challan details
				$insert_challanDetails = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'ChallanID'      => $ConcatenatedChallanNumber,
					'cnfid'          => '',
					'Transdate'      => $formattedDate,
					'RouteID'        => 0,
					'VehicleID'      => '',
					'DriverID'       => '',
					'LoaderID'       => '',
					'SalesmanID'     => '',
					'ChallanWeight'  => 0,
					'ChallanAmt'     => $BillAmt,
					'Gatepassuserid' => '',
					'OrderStatus'    => 'F',
					'UserID'         => $UserID
				);
				if ($this->db->insert(db_prefix() . 'K1challanmaster', $insert_challanDetails)) {
					$this->increment_next_number('next_K1Challan_number_for_kirti');
				}
				$wh_order =  '(OrderID="' . $OrderId . '")';
				$orderDetails = $this->get_data($tablename = "tblK1ordermaster", $wh_order);
				$TType2 = "SALE";
				$ordstat = "F";
				$update_order = array(
					'OrderStatus' => $ordstat,
					'ChallanID'   => $ConcatenatedChallanNumber,
					'SalesID'     => $SalesId,
					'OrderAmt'    => $OrdNetAmt,
				);
				$wh_updateorder = '(OrderID="' . $OrderId . '")';
				$updateorder = $this->edit_data($tablename = "tblK1ordermaster", $wh_updateorder, $update_order);
				$update_hisotry = array(
					// 'TType2'=>$TType2,
					'BillID'  => $ConcatenatedChallanNumber,
					'TransID' => $SalesId
				);
				$wh_updateitem = '(OrderID="' . $OrderId . '")';
				$updatehisotry = $this->edit_data($tablename = "tblK1history", $wh_updateitem, $update_hisotry);
				$update_sales = array(
					"ChallanID" => $ConcatenatedChallanNumber
				);
				$wh_updatesales = '(OrderID="' . $OrderId . '")';
				$updatesales = $this->edit_data($tablename = "tblK1salesmaster", $wh_updatesales, $update_sales);
				// Add Customer ledger Entries 
				$ord = 1;
				$narration = "By SalesID " . $SalesId . "/" . $ConcatenatedChallanNumber;
				$insert_customer_ledger = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => $AccountId,
					'CounterAccount' => "SALE",
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $BillAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $insert_customer_ledger);
				$ord++;
				if ($OtherAmt > 0) {
					// Add Sale Ledger Entry
					$sale_ledger_entry = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $SalesId,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => $OthEffectOn,
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "C",
						'Amount'         => $OtherAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "SALES",
						'OrdinalNo'      => $ord,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $sale_ledger_entry);
					$ord++;
				}
				// Add Sale Ledger Entry
				$sale_ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "SALE",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $SaleLedgerAmount,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $sale_ledger_entry);
				$ord++;
				if ($OrdCgstAmt != 0 && $OrdSgstAmt != 0) {
					// CGST Tax Ledger Entry
					$Cgst_Ledger_entry = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $SalesId,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => "CGST",
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "C",
						'Amount'         => $OrdCgstAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "SALES",
						'OrdinalNo'      => $ord,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $Cgst_Ledger_entry);
					$ord++;
					// SGST Tax Ledger Entry
					$Sgst_Ledger_entry = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $SalesId,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => "SGST",
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "C",
						'Amount'         => $OrdSgstAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "SALES",
						'OrdinalNo'      => $ord,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $Sgst_Ledger_entry);
					$ord++;
				} else if ($OrdIgstAmt != 0) {
					// Igst Ledger Entry
					$Igst_Ledger_Entry = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $SalesId,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => "IGST",
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "C",
						'Amount'         => $OrdIgstAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "SALES",
						'OrdinalNo'      => $ord,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $Igst_Ledger_Entry);
					$ord++;
				}
				// Discount Ledger Entry
				if ($OrdDiscAmt > 0) {
					$disc_ledger_entry = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $SalesId,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => "DISC",
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "D",
						'Amount'         => $OrdDiscAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "SALES",
						'OrdinalNo'      => $ord,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $disc_ledger_entry);
					$ord++;
				}
				$roundledgerentry_debit = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "ROUNDOFF",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $OrdRoundOffAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $roundledgerentry_debit);
				$ord++;
				if ($OrderType == 1) {
					// $nextReceiptnumber = get_option('next_receipts_number_for_kirti');
					$nextReceiptnumber = $this->generateNextVoucherIDNew($formattedDate, $selected_company, 'RECEIPTS');
					$ordinalno = 1;
					// 		if ($OnlineAmt > 0) {
					// 			// Receipt Voucher credit Entry to party
					// 			$receiptentry_credit_toParty = array(
					// 			'PlantID'=>$selected_company,
					// 			'FY'=>$fy,
					// 			'Transdate'=>$formattedDate,
					// 			'VoucherID'=>$nextReceiptnumber, 
					// 			'Transdate2'=>date('Y-m-d h:i:s'),  
					// 			'PartyID'=>"KASPL",
					// 			'AccountID'=>$AccountId,
					// 			'CounterAccount'=>$Effecton,
					// 			'CenterID'=>$CenterId,
					// 			'EntryFor'=>3,
					// 			'TType'=>"C",
					// 			'Amount'=>$OnlineAmt,
					// 			'Narration'=>$narration,
					// 			'PassedFrom'=>"RECEIPTS",
					// 			'OrdinalNo'=>$ordinalno,
					// 			'UserID'=>$UserID     
					//); 
					// 			$this->db->insert(db_prefix() . 'accountledger',$receiptentry_credit_toParty);
					// 			$ordinalno ++ ; 
					// 			// Receipt Voucher Debit Entry to Company
					// 			$receiptentry_debitto_company = array(
					// 			'PlantID'=>$selected_company,
					// 			'FY'=>$fy,
					// 			'Transdate'=>$formattedDate,
					// 			'VoucherID'=>$nextReceiptnumber, 
					// 			'Transdate2'=>date('Y-m-d h:i:s'),  
					// 			'PartyID'=>"KASPL",
					// 			'AccountID'=>$Effecton,
					// 			'CounterAccount'=>$AccountId,
					// 			'CenterID'=>$CenterId,
					// 			'EntryFor'=>3,
					// 			'TType'=>"D",
					// 			'Amount'=>$OnlineAmt,
					// 			'Narration'=>$narration,
					// 			'PassedFrom'=>"RECEIPTS",
					// 			'OrdinalNo'=>$ordinalno,
					// 			'UserID'=>$UserID     
					//);
					// 			$this->db->insert(db_prefix() . 'accountledger',$receiptentry_debitto_company); 
					// 			$ordinalno ++ ; 
					// 		}
					if ($CashAmt > 0) {
						$receiptentry_credit_toParty = array(
							'PlantID'        => $selected_company,
							'FY'             => $fy,
							'Transdate'      => $formattedDate,
							'VoucherID'      => $nextReceiptnumber,
							'Transdate2'     => date('Y-m-d h:i:s'),
							'PartyID'        => "KASPL",
							'AccountID'      => $AccountId,
							'CounterAccount' => 'CASH',
							'CenterID'       => $CenterId,
							'EntryFor'       => 3,
							'TType'          => "C",
							'Amount'         => $CashAmt,
							'Narration'      => $narration,
							'PassedFrom'     => "RECEIPTS",
							'OrdinalNo'      => $ordinalno,
							'UserID'         => $UserID
						);
						$this->db->insert(db_prefix() . 'accountledger', $receiptentry_credit_toParty);
						$ordinalno++;
						// Receipt Voucher Debit Entry to Company
						$receiptentry_debitto_company = array(
							'PlantID'        => $selected_company,
							'FY'             => $fy,
							'Transdate'      => $formattedDate,
							'VoucherID'      => $nextReceiptnumber,
							'Transdate2'     => date('Y-m-d h:i:s'),
							'PartyID'        => "KASPL",
							'AccountID'      => 'CASH',
							'CounterAccount' => $AccountId,
							'CenterID'       => $CenterId,
							'EntryFor'       => 3,
							'TType'          => "D",
							'Amount'         => $CashAmt,
							'Narration'      => $narration,
							'PassedFrom'     => "RECEIPTS",
							'OrdinalNo'      => $ordinalno,
							'UserID'         => $UserID
						);
						$this->db->insert(db_prefix() . 'accountledger', $receiptentry_debitto_company);
					}
					// if ($ordinalno > 1) {
					// 	$this->increment_next_number('next_receipts_number_for_kirti');
					$update_sales = array(
						"ReceiptVoucherID" => $nextReceiptnumber
					);
					$wh_updatesales = '(OrderID="' . $OrderId . '")';
					$updatesales = $this->edit_data($tablename = "tblK1salesmaster", $wh_updatesales, $update_sales);
					// }
				}
				//return $OrderId;
				$response = array("status"=>true,"message"=>"Order has been created successfully.");
			}else{
			    $response = array("status"=>false,"message"=>"An error occurred while creating the order. Please try again.");
			}
		}else{
		    $response = array("status"=>false,"message"=>"Order Net Amount cannot be zero. Please verify the item quantity, rate, discount, and tax details before saving the Order.");
		}
		return $response;
	}
	public function generateNextVoucherIDNew($selected_date = '', $plant_id = '', $passage_from = '')
	{
		if (empty($selected_date)) {
			$selected_date = date('Y-m-d');
		} else {
			$selected_date = date('Y-m-d', strtotime($selected_date));
		}
		if (empty($plant_id)) {
			$plant_id = $this->session->userdata('root_company');
		}
		// Extract date components
		$date_parts = explode('-', $selected_date);
		$year = substr($date_parts[0], 2);
		$month = $date_parts[1];
		$day = $date_parts[2];
		$plant_id_formatted = str_pad($plant_id, 2, '0', STR_PAD_LEFT);
		switch (strtoupper($passage_from)) {
			case 'JOURNAL':
				$prefix = 'J';
				break;
			case 'RECEIPTS':
				$prefix = 'R';
				break;
			case 'PAYMENTS':
				$prefix = 'P';
				break;
			default:
				$prefix = 'C';
				break;
		}
		// Build base: J0126040300001 or C0126040300001
		$voucher_base = $prefix . $plant_id_formatted . $year . $month . $day;
		$sql = "SELECT VoucherID 
							FROM " . db_prefix() . "accountledger 
							WHERE PlantID = " . (int)$plant_id . " 
							AND PassedFrom = '" . $this->db->escape_str(strtoupper($passage_from)) . "' 
							AND DATE(Transdate) = '" . $this->db->escape_str($selected_date) . "' 
							AND VoucherID LIKE '" . $this->db->escape_like_str($voucher_base) . "%'
							ORDER BY CAST(RIGHT(VoucherID, 3) AS UNSIGNED) DESC
							LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (!empty($row['VoucherID'])) {
			$lastNumber = (int) substr($row['VoucherID'], -3);
			$nextNumber = $lastNumber + 1;
		} else {
			$nextNumber = 1;
		}
		$new_voucher_number = $voucher_base . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
		return $new_voucher_number;
	}
	public function CheckItemFor($ProductID)
	{
		$selected_company = $this->session->userdata('root_company');
		$this->db->select(db_prefix() . 'product.*,tbltaxes.taxrate AS gst');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst', 'left');
		$this->db->where(db_prefix() . 'product.ProductID', $ProductID);
		$this->db->where(db_prefix() . 'product.PlantID', $selected_company);
		return $this->db->get(db_prefix() . 'product')->row();
	}
	public function UpdateKirtiOneSaleOrder($data, $OrderId)
	{
		if (isset($data['sale_invoice_detail'])) {
			$sale_invoice_detail = json_decode($data['sale_invoice_detail']);
			unset($data['sale_invoice_detail']);
			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];
			$header[] = 'ProductID';
			$header[] = 'HsnCode';
			$header[] = 'Brand';
			$header[] = 'MeasuredIn';
			$header[] = 'PackingQty';
			$header[] = 'GSTApply';
			$header[] = 'SaleUnit';
			$header[] = 'BatchNo';
			$header[] = 'StockQty';
			$header[] = 'Qty';
			$header[] = 'Amount';
			$header[] = 'Discount';
			$header[] = 'GST';
			$header[] = 'CGSTAMT';
			$header[] = 'SGSTAMT';
			$header[] = 'IGSTAMT';
			$header[] = 'NetAmount';
			$header[] = 'ExpDate';
			foreach ($sale_invoice_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$UserID = $this->session->userdata('username');
		$PostedDate = $data['posted_date'];
		// $date = DateTime::createFromFormat('d/m/Y', $PostedDate);
		$formattedDate =  to_sql_date($PostedDate) . " " . date('H:i:s');
		$AccountId = $data['AccountID'];
		$CenterId = $data['centername'];
		$TotalCgstAmt = $data['total_cgst_amt'];
		$TotalSgstAmt = $data['total_sgst_amt'];
		$IgstAmt = $data['total_igst_amt'];
		$TotalValue = $data['Total_value'];
		$TotalDiscountAmt = $data['total_disc_in_mt'];
		$TotalNetPayableAmt =  $data['netpayableamt'];
		$Effecton = $data['Effecton'];
		$OrderType = $data['ordtype'];
		$PaymentMode = $data['paymentmode'];
		$PaymentMethod = $data['paymentmethod'];
		$CashAmt = $data['CashAmt'];
		$OnlineAmt = $data['OnlineAmt'];
		$ReferenceNo = $data['referenceno'];
		$OtherAmt = $data['OtherAmt'];
		$OthEffectOn = $data['OthEffectOn'];
		$FarmerID = $data['FarmerID'];
		$FarmerAadhaar = $data['FarmerAadhaar'];
		// echo $ReferenceNo;die;
		$VillageName = $data['villagename'];
		$delivery_type = $data['type'];
		$ordtype = $data['ordtype'];
		if (($CashAmt + $OnlineAmt) != $TotalNetPayableAmt && $ordtype == "1") {
			$response = array("status"=>false,"message"=>"The payment amount does not match the total net payable amount.");
	        return $response;
		}
		$SaleItemList = $this->GetSaleOrderItemList_New($OrderId);
		// check stock
	    $StockNotAvl = 0;
	    foreach ($es_detail as $index => $row) {
			if (!empty($row['ProductID'])) {
				$productId = $row['ProductID'];
				$BatchNo = $row['BatchNo'];
				$Qty = $row['Qty'];
				$Unit = $row['MeasuredIn'];
				$SaleUnit = $row['SaleUnit'];
				if ($Unit == $SaleUnit) {
					$BilledQty = $Qty * $PackQty;
				} else {
					$BilledQty = $Qty;
				}
				$ExQty = 0;
				foreach($SaleItemList as $Exkey=>$ExVal){
					if($productId == $ExVal["ItemID"] && $BatchNo == $ExVal["BatchNo"]){
						$ExQty = $ExVal["BilledQty"];
					}
				}
				$filterdata = [
					'ItemID'   => $productId,
					'CenterID' => $CenterId,
					'BatchID'  => $BatchNo,
				];
				$ItemWiseBatchList = $this->GetItemBatchListWithStockDSO($filterdata);
				$StockQty = number_format($ItemWiseBatchList[0]["Stock"]+$ExQty, 2);
				if($BilledQty > $StockQty){
					$StockNotAvl++;
				}
			}
	    }
	    if($StockNotAvl > 0){
				$response = array("status"=>false,"message"=>"Insufficient stock is available for one or more order items. Please review the item quantities and try again.");
				return $response;
	    }
		if ($delivery_type == '2') {
			$ShippingID = $data['ShippingID'];
			if ($ShippingID == 'new') {
				$insert_address = array(
					'AccountID' => $AccountId,
					'House'     => $data['ShippingHouse'],
					'Street'    => $data['ShippingStreet'],
					'Locality'  => $data['ShippingLocality'],
					'Block'     => $data['ShippingBlock'],
					'Pincode'   => $data['ShippingPincode'],
					'State'     => $data['ShippingState'],
					'District'  => $data['ShippingCity'],
					"UserID"    => $_SESSION['username'],
					"TransDate" => date('Y-m-d H:i:s'),
				);
				$createnewaddress = $this->insert_data($tablename = "tblShippingDetails", $insert_address);
				$ShippingID = $this->db->insert_id();
			}
		} else {
			$ShippingID = null;
		}
		if ($OrderType == '') {
			$InvoiceType = "CREDIT";
		} else {
			$InvoiceType = "CASH";
		}
		$MobileNo = $data['phonenumber'];
		$BillingState = $data['billstate'];
		$BillNo = $data['billno'];
		$RndAmt = $data['total_roundoff_amt'];
		$RoundAmt = abs($RndAmt);
		if ($OrderType == '2') {
			$paymode = "";
			$paymethod = "";
			$refnumber = "";
		} else if ($OrderType == '1') {
			$paymode = $PaymentMode;
			$paymethod = $PaymentMethod;
			$refnumber = $ReferenceNo;
		}
		$OrderDetails = $this->GetSaleOrderDetails($OrderId);
		$SaleItemList = $this->GetSaleOrderItemList_New($OrderId);
		$CenterData = $this->GetCenterByCenterID($CenterId);
		$SalesId   = $OrderDetails->SalesID;
		$ChallanID   = $OrderDetails->ChallanID;
		$wh_saleid = '(SalesID="' . $SalesId . '")';
		$SalesMasterData = $this->get_data($tablename = "tblK1salesmaster", $wh_saleid);
		$oldItemsdata = array();
		foreach ($SaleItemList as $olditem) {
			array_push($oldItemsdata, $olditem['ItemID']);
		}
		if ($OrderDetails) {
			// insert in history table
			$ordno = 1;
			$OrdSaleAmt = 0;
			$OrdDiscAmt = 0;
			$OrdCgstAmt = 0;
			$OrdSgstAmt = 0;
			$OrdIgstAmt = 0;
			$OrdNetAmt = 0;
			$OrdRoundOffAmt = 0;
			$ItemCount = 0;
			foreach ($es_detail as $index => $row) {
				if (!empty($row['ProductID'])) {
					$productId = $row['ProductID'];
					$ItemDetails = $this->CheckItemFor($productId);
					$Qty = $row['Qty'];
					$Unit = $row['MeasuredIn'];
					$SaleUnit = $row['SaleUnit'];
					$PackQty = $row['PackingQty'];
					$DiscAmt = $row['Discount'];
					$BasicRate = $row['Amount'];
					$NewBasicRate = $row['Amount'];
					$BatchNo = $row['BatchNo'];
					$ExpDate = $row['ExpDate'];
					$ItemTotal = 0;
					$TotalDisc = 0;
					$TaxableAmt = 0;
					$DiscPer = 0;
					$CgstPer = 0;
					$CgstAmt = 0;
					$SgstPer = 0;
					$SgstAmt = 0;
					$IgstPer = 0;
					$IgstAmt = 0;
					if ($DiscAmt > 0) {
						$DiscPer = ($DiscAmt / $BasicRate) * 100;
					}
					if ($Unit == $SaleUnit) {
						$NewBasicRate = $BasicRate / $PackQty;
						$BilledQty = $Qty * $PackQty;
						$CaseQty = $PackQty;
					} else {
						$BilledQty = $Qty;
						$NewBasicRate = $BasicRate;
						$CaseQty = 1;
					}
					if ($row['GSTApply'] == "Including") {
						$SaleRate = $NewBasicRate;
					} else {
						$SaleRate = $NewBasicRate + ($NewBasicRate * ($ItemDetails->gst / 100));
					}
					$TotalDisc = $DiscAmt * $Qty;
					$ItemTotal = $NewBasicRate * $BilledQty;
					$TaxableAmt = $ItemTotal - $TotalDisc;
					if ($row['GSTApply'] == "Excluding") {
						if ($CenterData->state == $BillingState) {
							$CgstPer = $ItemDetails->gst / 2;
							$SgstPer = $ItemDetails->gst / 2;
							$CgstAmt = $TaxableAmt * ($CgstPer / 100);
							$SgstAmt = $TaxableAmt * ($SgstPer / 100);
						} else {
							$IgstPer = $ItemDetails->gst;
							$IgstAmt = $TaxableAmt * ($IgstPer / 100);
						}
					} else {
						if ($CenterData->state == $BillingState) {
							$CgstPer = $ItemDetails->gst / 2;
							$SgstPer = $ItemDetails->gst / 2;
						} else {
							$IgstPer = $ItemDetails->gst;
						}
					}
					$ItemNetAmt = $TaxableAmt + $CgstAmt + $SgstAmt + $IgstAmt;
					$OrdSaleAmt += $ItemTotal;
					$OrdDiscAmt += $TotalDisc;
					$OrdCgstAmt += $CgstAmt;
					$OrdSgstAmt += $SgstAmt;
					$OrdIgstAmt += $IgstAmt;
					$OrdNetAmt  += $ItemNetAmt;
					if (in_array($row['ProductID'], $oldItemsdata)) {
						$update_product_detail = array(
							'TransDate'     => $formattedDate,
							'CenterID'      => $CenterId,
							'GodownID'      => 'RET',
							'PurchRate'     => $NewBasicRate,
							'SaleRate'      => $SaleRate,
							'BasicRate'     => $NewBasicRate,
							'SuppliedIn'    => $Unit,
							'OrderQty'      => $BilledQty,
							'eOrderQty'     => '',
							'BilledQty'     => $BilledQty,
							'DiscPerc'      => $DiscPer,
							'DiscAmt'       => $TotalDisc,
							'cgst'          => $CgstPer,
							'cgstamt'       => $CgstAmt,
							'sgst'          => $SgstPer,
							'sgstamt'       => $SgstAmt,
							'igst'          => $IgstPer,
							'igstamt'       => $IgstAmt,
							'CaseQty'       => $CaseQty,
							'Cases'         => 0.00,
							'OrderAmt'      => $ItemTotal,
							'ChallanAmt'    => $ItemTotal,
							'NetOrderAmt'   => $ItemNetAmt,
							'NetChallanAmt' => $ItemNetAmt,
							'BatchNo'       => $BatchNo,
							'ExpDate'       => $ExpDate,
							'Ordinalno'     => $ordno,
							'rowid'         => 0,
							'UserID2'       => $UserID,
							'Lupdate'       => date('Y-m-d h:i:s'),
							'cnfid'         => '',
							'reason'        => ''
						);
						$wh_updateorder = array("OrderID" => $OrderId, "ItemID" => $row['ProductID'], 'BatchNo' => $BatchNo,);
						// print_r($update_product_detail);
						$updateorder = $this->edit_data($tablename = "tblK1history", $wh_updateorder, $update_product_detail);
						$ordno++;
					} else {
						$insert_product_detail = array(
							'PlantID'       => $selected_company,
							'FY'            => $fy,
							'OrderID'       => $OrderId,
							'BillID'        => $ChallanID,
							'TransID'       => $SalesId,
							'TransDate'     => $formattedDate,
							'TransDate2'    => date('Y-m-d h:i:s'),
							'TType'         => "O",
							'TType2'        => "SALE",
							'AccountID'     => $AccountId,
							'ItemID'        => $productId,
							'CenterID'      => $CenterId,
							'GodownID'      => 'RET',
							'PartyID'       => "KASPL",
							'ChamberID'     => '',
							'StackID'       => '',
							'LOTID'         => '',
							'PurchRate'     => $NewBasicRate,
							'SaleRate'      => $SaleRate,
							'BasicRate'     => $NewBasicRate,
							'SuppliedIn'    => $Unit,
							'OrderQty'      => $BilledQty,
							'eOrderQty'     => '',
							'BilledQty'     => $BilledQty,
							'DiscPerc'      => $DiscPer,
							'DiscAmt'       => $TotalDisc,
							'cgst'          => $CgstPer,
							'cgstamt'       => $CgstAmt,
							'sgst'          => $SgstPer,
							'sgstamt'       => $SgstAmt,
							'igst'          => $IgstPer,
							'igstamt'       => $IgstAmt,
							'CaseQty'       => $CaseQty,
							'Cases'         => 0.00,
							'OrderAmt'      => $ItemTotal,
							'ChallanAmt'    => $ItemTotal,
							'NetOrderAmt'   => $ItemNetAmt,
							'NetChallanAmt' => $ItemNetAmt,
							'BatchNo'       => $BatchNo,
							'ExpDate'       => $ExpDate,
							'Ordinalno'     => $ordno,
							'rowid'         => 0,
							'UserID'        => $UserID,
							'cnfid'         => '',
							'reason'        => ''
						);
						$this->db->insert(db_prefix() . 'K1history', $insert_product_detail);
						$ordno++;
					}
				}
				$ItemCount++;
			}
			// die;
			$BillAmt = round($OrdNetAmt);
			$OrdRoundOffAmt = $BillAmt - $OrdNetAmt;
			$SaleLedgerAmount = $OrdSaleAmt;
			$update_order = array(
				'Transdate'        => $formattedDate,
				'CenterID'         => $CenterId,
				'VillageName'      => $VillageName,
				'OrderAmt'         => $OrdNetAmt,
				'OrderPaymentType' => $OrderType,
				'DeliveryType'     => $delivery_type,
				'BIllNo'           => $BillNo,
				'billstate'        => $BillingState,
				'FarmerID'         => $FarmerID,
				'FarmerAadhaar'    => $FarmerAadhaar
			);
			$wh_updateorder = '(OrderID="' . $OrderId . '")';
			$updateorder = $this->edit_data($tablename = "tblK1ordermaster", $wh_updateorder, $update_order);
			// insert in sales table
			$update_entry_sales = array(
				'Transdate'        => $formattedDate,
				'CenterID'         => $CenterId,
				'DeliveryType'     => $delivery_type,
				'ShippingID'       => $ShippingID,
				'SaleAmt'          => $OrdSaleAmt,
				'DiscAmt'          => $OrdDiscAmt,
				'sgstamt'          => $OrdCgstAmt,
				'cgstamt'          => $OrdSgstAmt,
				'igstamt'          => $OrdIgstAmt,
				'BillAmt'          => $OrdNetAmt,
				'RndAmt'           => $BillAmt,
				'OtherAmt'         => $OtherAmt,
				'EffectOnOtherAmt' => $OthEffectOn,
				'CashAmt'          => $CashAmt,
				'OnlineAmt'        => $OnlineAmt,
				'RefNo'            => $refnumber,
				'Effecton'         => $Effecton,
				'ItCount'          => $ItemCount,
				'UserID2'          => $UserID,
				'Lupdate'          => date('Y-m-d h:i:s'),
				'tcs'              => 0.00,
				'tcsAmt'           => 0.00,
			);
			$wh_updateorder = '(OrderID="' . $OrderId . '")';
			$SaleEntry = $this->edit_data($tablename = "tblK1salesmaster", $wh_updateorder, $update_entry_sales);
			// insert challan details
			$update_challanDetails = array(
				'Transdate'  => $formattedDate,
				'ChallanAmt' => $BillAmt,
				'UserID2'    => $UserID,
				'Lupdate'    => date('Y-m-d h:i:s'),
			);
			$wh_updatechallan = '(ChallanID="' . $ChallanID . '")';
			$ChallanEntry = $this->edit_data($tablename = "tblK1challanmaster", $wh_updatechallan, $update_challanDetails);
			$wh_order =  '(OrderID="' . $OrderId . '")';
			$orderDetails = $this->get_data($tablename = "tblK1ordermaster", $wh_order);
			$ordstat = "F";
			$TType2 = "SALE";
			$update_hisotry = array(
				'TType2' => $TType2,
			);
			$wh_updateitem = '(OrderID="' . $OrderId . '")';
			$updatehisotry = $this->edit_data($tablename = "tblK1history", $wh_updateitem, $update_hisotry);
			// Delete Previous Ledger Entries
			$this->db->where('VoucherID', $SalesId);
			$this->db->delete(db_prefix() . 'accountledger');
			// Add Customer ledger Entries 
			$ord = 1;
			$narration = "By SalesID " . $SalesId . "/" . $ChallanID;
			$insert_customer_ledger = array(
				'PlantID'        => $selected_company,
				'FY'             => $fy,
				'Transdate'      => $formattedDate,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => $AccountId,
				'CounterAccount' => "SALE",
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "D",
				'Amount'         => $BillAmt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $UserID
			);
			$this->db->insert(db_prefix() . 'accountledger', $insert_customer_ledger);
			$ord++;
			// Add Sale Ledger Entry
			$sale_ledger_entry = array(
				'PlantID'        => $selected_company,
				'FY'             => $fy,
				'Transdate'      => $formattedDate,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => "SALE",
				'CounterAccount' => $AccountId,
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "C",
				'Amount'         => $SaleLedgerAmount,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $UserID
			);
			$this->db->insert(db_prefix() . 'accountledger', $sale_ledger_entry);
			$ord++;
			if ($OtherAmt > 0) {
				// Add Sale Ledger Entry
				$OtherAmt_ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => $OthEffectOn,
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OtherAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $OtherAmt_ledger_entry);
				$ord++;
			}
			if ($OrdCgstAmt != 0.00 && $OrdSgstAmt != 0.00) {
				// CGST Tax Ledger Entry
				$Cgst_Ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "CGST",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrdCgstAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $Cgst_Ledger_entry);
				$ord++;
				// SGST Tax Ledger Entry
				$Sgst_Ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "SGST",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrdSgstAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $Sgst_Ledger_entry);
				$ord++;
			} else if ($OrdIgstAmt != 0.00) {
				// Igst Ledger Entry
				$Igst_Ledger_Entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "IGST",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrdCgstAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $Igst_Ledger_Entry);
				$ord++;
			}
			// Discount Ledger Entry
			if ($OrdDiscAmt > 0) {
				$disc_ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $formattedDate,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "DISC",
					'CounterAccount' => $AccountId,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $OrdDiscAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$this->db->insert(db_prefix() . 'accountledger', $disc_ledger_entry);
				$ord++;
			}
			// RndAmt Ledger Entry
			$roundledgerentry_debit = array(
				'PlantID'        => $selected_company,
				'FY'             => $fy,
				'Transdate'      => $formattedDate,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => "ROUNDOFF",
				'CounterAccount' => $AccountId,
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "D",
				'Amount'         => $OrdRoundOffAmt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $UserID
			);
			$this->db->insert(db_prefix() . 'accountledger', $roundledgerentry_debit);
			$ord++;
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('VoucherID', $SalesMasterData['ReceiptVoucherID']);
			$this->db->where('PassedFrom', 'RECEIPTS');
			$this->db->delete(db_prefix() . 'accountledger');
			if ($OrderType == 1) {
				// $nextReceiptnumber = get_option('next_receipts_number_for_kirti');  
				$nextReceiptnumber = $this->generateNextVoucherIDNew($formattedDate, $selected_company, 'RECEIPTS');
				$ordinalno = 1;
				// 	if ($OnlineAmt > 0) {
				// 		// Receipt Voucher credit Entry to party
				// 		$receiptentry_credit_toParty = array(
				// 		'PlantID'=>$selected_company,
				// 		'FY'=>$fy,
				// 		'Transdate'=>$formattedDate,
				// 		'VoucherID'=>$nextReceiptnumber, 
				// 		'Transdate2'=>date('Y-m-d h:i:s'),  
				// 		'PartyID'=>"KASPL",
				// 		'AccountID'=>$AccountId,
				// 		'CounterAccount'=>$Effecton,
				// 		'CenterID'=>$CenterId,
				// 		'EntryFor'=>3,
				// 		'TType'=>"C",
				// 		'Amount'=>$OnlineAmt,
				// 		'Narration'=>$narration,
				// 		'PassedFrom'=>"RECEIPTS",
				// 		'OrdinalNo'=>$ordinalno,
				// 		'UserID'=>$UserID     
				//); 
				// 		$this->db->insert(db_prefix() . 'accountledger',$receiptentry_credit_toParty);
				// 		$ordinalno ++ ; 
				// 		// Receipt Voucher Debit Entry to Company
				// 		$receiptentry_debitto_company = array(
				// 		'PlantID'=>$selected_company,
				// 		'FY'=>$fy,
				// 		'Transdate'=>$formattedDate,
				// 		'VoucherID'=>$nextReceiptnumber, 
				// 		'Transdate2'=>date('Y-m-d h:i:s'),  
				// 		'PartyID'=>"KASPL",
				// 		'AccountID'=>$Effecton,
				// 		'CounterAccount'=>$AccountId,
				// 		'CenterID'=>$CenterId,
				// 		'EntryFor'=>3,
				// 		'TType'=>"D",
				// 		'Amount'=>$OnlineAmt,
				// 		'Narration'=>$narration,
				// 		'PassedFrom'=>"RECEIPTS",
				// 		'OrdinalNo'=>$ordinalno,
				// 		'UserID'=>$UserID     
				//);
				// 		$this->db->insert(db_prefix() . 'accountledger',$receiptentry_debitto_company); 
				// 		$ordinalno ++ ; 
				// 	}
				if ($CashAmt > 0) {
					$receiptentry_credit_toParty = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $nextReceiptnumber,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => $AccountId,
						'CounterAccount' => 'CASH',
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "C",
						'Amount'         => $CashAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "RECEIPTS",
						'OrdinalNo'      => $ordinalno,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $receiptentry_credit_toParty);
					$ordinalno++;
					// Receipt Voucher Debit Entry to Company
					$receiptentry_debitto_company = array(
						'PlantID'        => $selected_company,
						'FY'             => $fy,
						'Transdate'      => $formattedDate,
						'VoucherID'      => $nextReceiptnumber,
						'Transdate2'     => date('Y-m-d h:i:s'),
						'PartyID'        => "KASPL",
						'AccountID'      => 'CASH',
						'CounterAccount' => $AccountId,
						'CenterID'       => $CenterId,
						'EntryFor'       => 3,
						'TType'          => "D",
						'Amount'         => $CashAmt,
						'Narration'      => $narration,
						'PassedFrom'     => "RECEIPTS",
						'OrdinalNo'      => $ordinalno,
						'UserID'         => $UserID
					);
					$this->db->insert(db_prefix() . 'accountledger', $receiptentry_debitto_company);
				}
				// if ($ordinalno > 1) {
				// 	$this->increment_next_number('next_receipts_number_for_kirti');
				$update_sales = array(
					"ReceiptVoucherID" => $nextReceiptnumber
				);
				$wh_updatesales = '(OrderID="' . $OrderId . '")';
				$updatesales = $this->edit_data($tablename = "tblK1salesmaster", $wh_updatesales, $update_sales);
				// }
			}
			$response = array("status"=>true,"message"=>"Order has been updated successfully.");
		}else{
		    $response = array("status"=>false,"message"=>"Order details could not be loaded. Please refresh the page and try again.");
		}
		return $response;
	}
	public function increment_next_number($name)
	{
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('name', $name);
		$this->db->update(db_prefix() . 'options');
	}
	public function GetSaleOrderItemList_New($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*,tblK1history.ItemID AS id,ExpDate,
			tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			tblK1history.SuppliedIn AS SaleUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst', 'left');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId', 'left');
		$this->db->where(db_prefix() . 'K1history.OrderID', $id);
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$this->db->where(db_prefix() . 'K1history.TType', "O");
		$results = $this->db->get()->result_array();
		foreach ($results as &$row) {
			$GstAmt = $row['cgstamt'] + $row['sgstamt'] + $row['igstamt'];
			if ($GstAmt > 0) {
				$row['GSTApply'] = 'Excluding';
			} else {
				$row['GSTApply'] = 'Including';
			}
			if ($row['PackingQty'] == $row['CaseQty'] && $row['PackingQty'] > 1) {
				$row['SaleUnit'] = $row['Measuredin'];
				$row['OrderQty'] = $row['BilledQty'] / $row['PackingQty'];
			} else {
				$row['SaleUnit'] = 'Loose';
				$row['OrderQty'] = $row['BilledQty'];
			}
			$row['PurchRate'] = $row['PurchRate'] * $row['CaseQty'];
			$row['Discount'] = number_format($row['DiscAmt'] / ($row['BilledQty'] / $row['CaseQty']), 2);
			$row['ExpDate'] = _d(substr($row['ExpDate'], 0, 10));
			$filterdata = [
				'ItemID'   => $row['ItemID'],
				'CenterID' => $row['CenterID'],
				'BatchID'  => $row['BatchNo'],
			];
			$ItemWiseBatchList = $this->GetItemBatchListWithStockDSO($filterdata);
			// $row['BAtch'] = $ItemWiseBatchList;
			$row['StockQty'] = number_format($ItemWiseBatchList[0]["Stock"] + $row['BilledQty'], 2);
		}
		return $results;
	}
	public function GetSaleOrderItemList($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*,(tblK1history.OrderQty/tblK1history.CaseQty) AS OrderQty,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,
			tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS SaleUnit,tblK1history.DiscAmt AS Discount,
			tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst', 'left');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId', 'left');
		$this->db->where(db_prefix() . 'K1history.OrderID', $id);
		$this->db->where(db_prefix() . 'K1history.BillID IS NULL');
		$this->db->where(db_prefix() . 'K1history.TransID IS NULL');
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$results = $this->db->get()->result_array();
		return $results;
	}
	public function GetSaleOrderDetails($PONumber)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $this->session->userdata('finacial_year');
		$this->db->select('tblK1ordermaster.*, tblclients.company, tblclients.phonenumber, tblclients.state, tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1ordermaster.saleamt - tblK1ordermaster.Discamt) AS taxable_amt,tblCenterMaster.CenterName,tblCenterMaster.state as CenterStateShort,tblGstRecord.gstin AS gst, CenterState.state_name AS StateCenter, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress,(SELECT COUNT(*) FROM ' . db_prefix() . 'K1salesmaster WHERE ' . db_prefix() . 'K1salesmaster.OrderID = tblK1ordermaster.OrderID) AS SalesCount');
		$this->db->from(db_prefix() . 'K1ordermaster');
		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1ordermaster.AccountID AND tblclients.PlantID = tblK1ordermaster.PlantID');
		$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
		$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'K1ordermaster.CenterID', 'left');
		$this->db->join(db_prefix() . 'xx_statelist as CenterState', 'CenterState.short_name = tblCenterMaster.state', 'left');
		$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1ordermaster.OrderID', 'left');
		// $this->db->join(db_prefix() . 'accountledger', 'tblaccountledger.AccountID = tblK1ordermaster.OrderID', 'left');
		$this->db->where(db_prefix() . 'K1ordermaster.OrderID', $PONumber);
		$this->db->where(db_prefix() . 'K1ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1ordermaster.FY', $year);
		$data = $this->db->get()->row();
		if (!empty($data)) {
			$data->ClosingBalance = $this->GetClosingBalance($data->AccountID);
			$data->ShippingList = $this->GetShippingListVendorwise($data->AccountID);
		}
		return $data;
	}
	public function GetDirectSaleOrderDetails($PONumber)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $this->session->userdata('finacial_year');
		$this->db->select('tblK1ordermaster.*, tblK1salesmaster.*, tblK1salesmaster.GSTIN AS PartyGSTIN, tblclients.company, tblclients.phonenumber, tblclients.state, tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1ordermaster.saleamt - tblK1ordermaster.Discamt) AS taxable_amt,tblCenterMaster.CenterName,tblGstRecord.gstin AS gst, CenterState.state_name AS StateCenter, GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress,(SELECT COUNT(*) FROM ' . db_prefix() . 'K1salesmaster WHERE ' . db_prefix() . 'K1salesmaster.OrderID = tblK1ordermaster.OrderID) AS SalesCount');
		$this->db->from(db_prefix() . 'K1ordermaster');
		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1ordermaster.AccountID AND tblclients.PlantID = tblK1ordermaster.PlantID');
		$this->db->join(db_prefix() . 'K1salesmaster', 'tblK1salesmaster.OrderID = tblK1ordermaster.OrderID AND tblK1salesmaster.PlantID = tblK1ordermaster.PlantID');
		$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
		$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'K1ordermaster.CenterID', 'left');
		$this->db->join(db_prefix() . 'xx_statelist as CenterState', 'CenterState.short_name = tblCenterMaster.state', 'left');
		$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1ordermaster.OrderID', 'left');
		// $this->db->join(db_prefix() . 'accountledger', 'tblaccountledger.AccountID = tblK1ordermaster.OrderID', 'left');
		$this->db->where(db_prefix() . 'K1ordermaster.OrderID', $PONumber);
		$this->db->where(db_prefix() . 'K1ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1ordermaster.FY', $year);
		$data = $this->db->get()->row();
		if (!empty($data)) {
			$data->ClosingBalance = $this->GetClosingBalance($data->AccountID);
			$data->ShippingList = $this->GetShippingListVendorwise($data->AccountID);
		}
		return $data;
	}
	public function GetClosingBalance($AccountID)
	{
		$total_bal = $this->get_data_for_account_bal($AccountID);
		$data_report = $this->get_data_general_ledger2($AccountID);
		$new_acc_bal = $total_bal->BAL1 ?? 0;
		$opening_bal = $total_bal->BAL1 ?? 0;
		$new_acc_bal2 = 0;
		$CRSum = 0;
		$DRSum = 0;
		$finacial_year = $this->session->userdata('finacial_year');
		$total_debit = 0;
		$total_credit = 0;
		if (empty($data_report)) {
			$OCR = 0.00;
			$ODR = 0.00;
			if ($new_acc_bal <= 0) {
				$OCR = abs($new_acc_bal);
				$OB = $OCR . 'Cr';
			} else {
				$ODR = abs($new_acc_bal);
				$OB = $ODR . 'Dr';
			}
		} else {
			$OCR = 0.00;
			$ODR = 0.00;
			if ($new_acc_bal <= 0) {
				$OCR = abs($new_acc_bal);
				$OB = $OCR . 'Cr';
			} else {
				$ODR = abs($new_acc_bal);
				$OB = $ODR . 'Dr';
			}
			$total_credit = $total_credit + $OCR;
			$total_debit = $total_debit + $ODR;
			foreach ($data_report as $key => $value) {
				if ($value["Amount"] !== "0.00") {
					// Update the balance based on transaction type (Debit or Credit)
					if ($value["TType"] == "D") {
						$new_acc_bal = $new_acc_bal + $value["Amount"];
						$dvalue = $value["Amount"];
						$total_debit = $total_debit + $dvalue;
						$dvalue = number_format($dvalue, 2);
					}
					if ($value["TType"] == "C") {
						$new_acc_bal = $new_acc_bal - $value["Amount"];
						$cvalue = $value["Amount"];
						$total_credit = $total_credit + $cvalue;
						$cvalue = number_format($cvalue, 2);
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
		return $new_acc_bal2;
	}
	public function GetCenterByCenterID($CenterID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'CenterMaster.*');
		$this->db->where(db_prefix() . 'CenterMaster.CenterID', $CenterID);
		return $this->db->get(db_prefix() . 'CenterMaster')->row();
	}
	public function GetTaxableTransaction($postData)
	{
		$ChallanID = $postData['ChallanID'];
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'K1salesmaster.*');
		$this->db->where(db_prefix() . 'K1salesmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1salesmaster.FY', $fy);
		// $this->db->where(db_prefix() . 'K1salesmaster.BT',"T");
		$this->db->where(db_prefix() . 'K1salesmaster.ChallanID', $ChallanID);
		return $this->db->get(db_prefix() . 'K1salesmaster')->result_array();
	}
	public function fetchItemDetails($TransId)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $_SESSION['finacial_year'];
		$this->db->select(db_prefix() . 'K1history.*,' . db_prefix() . 'product.hsn_code,' . db_prefix() . 'product.unit,tblproduct.ProductName');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->where(db_prefix() . 'K1history.TransID', $TransId);
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $year);
		// $this->db->where(db_prefix() . 'K1history.TType', "O");
		$this->db->where(db_prefix() . 'K1history.NetChallanAmt !=', "0.00");
		// $this->db->where(db_prefix() . 'K1history.TType2', "Order");
		return $this->db->get()->result_array();
	}
	public function GetAccountListPartywise($PartyID)
	{
		$this->db->select('tblclients.*, tblxx_statelist.state_name, tblxx_statelist.short_name, tblGstRecord.gstin');
		$this->db->from(db_prefix() . 'clients');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblclients.state', 'LEFT');
		$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->where(db_prefix() . 'clients.AccountID', $PartyID);
		$Data = $this->db->get()->row();
		return $Data;
	}
	// =================== Add Kirti One SaleOrder =============================
	public function AddKirtiOneNewSaleOrder($data)
	{
		if (isset($data['pur_order_detail'])) {
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
			$header[] = 'PackingWeight';
			$header[] = 'SaleUnit';
			$header[] = 'BilledQty';
			$header[] = 'BasicRate';
			$header[] = 'DiscAmt';
			$header[] = 'GSTPer';
			$header[] = 'CGSTAmt';
			$header[] = 'SGSTAmt';
			$header[] = 'IGSTAmt';
			$header[] = 'NetAmt';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$prefix = 'ORD';
		$sale_orderNumbar = get_option('next_K1Order_number_for_kirti');
		$new_sale_orderNumbar = $prefix . $FY . $sale_orderNumbar;
		$Transdate =  to_sql_date($data['prd_date']) . " " . date('H:i:s');
		$VendorID = $data['vendor'];
		$PaymentTerm = $data['PaymentTerm'];
		$GSTIN = $data['gst'];
		$CenterID = $data['centername'];
		$State = $data['state'];
		$ItCount = count($es_detail);
		// Party Details
		$this->db->select('tblclients.*');
		$this->db->from(db_prefix() . 'clients');
		$this->db->where(db_prefix() . 'clients.AccountID', $VendorID);
		$traderlist = $this->db->get()->row();
		// Center Details
		$this->db->select('tblCenterMaster.*');
		$this->db->from(db_prefix() . 'CenterMaster');
		$this->db->where(db_prefix() . 'CenterMaster.CenterID', $CenterID);
		$CenterDetails = $this->db->get()->row();
		$CenterState = $CenterDetails->state;
		$KirtiOneOrdMaster = array(
			'PlantID'     => $PlantID,
			'FY'          => $FY,
			'OrderID'     => $new_sale_orderNumbar,
			'Transdate'   => $Transdate,
			'CenterID'    => $CenterID,
			'AccountID'   => $VendorID,
			'PaymentTerm' => $PaymentTerm,
			'saleamt'     => 0,
			'Discamt'     => 0,
			'cgstamt'     => 0,
			'sgstamt'     => 0,
			'igstamt'     => 0,
			'RoundOffAmt' => 0,
			'Invamt'      => 0,
			'OrderStatus' => 'O',
			'ItCount'     => $ItCount,
			"Userid"      => $_SESSION['username'],
		);
		if ($GSTIN) {
			$KirtiOneOrdMaster["GSTNO"] = $GSTIN;
		}
		$this->db->insert(db_prefix() . 'K1ordermaster', $KirtiOneOrdMaster);
		if ($this->db->affected_rows() > 0) {
			// $insert_id = $this->db->insert_id();
			$this->increment_next_number('next_K1Order_number_for_kirti');
			if ($traderlist->state == "") {
				$state_result = array(
					'state' => $State,
				);
				$this->db->where('AccountID', $VendorID);
				$this->db->update(db_prefix() . 'clients', $state_result);
			}
			$i = 1;
			$TotalItemAmt = 0;
			$TotalDiscAmt = 0;
			$TotalTaxableAmt = 0;
			$TotalCGSTAmt = 0;
			$TotalSGSTAmt = 0;
			$TotalIGSTAmt = 0;
			$TotalNetAmt = 0;
			foreach ($es_detail as $value) {
				$productId = $value['ItemID'];
				$ItemTotal = $value['BilledQty'] * $value['BasicRate'];
				$TotalItemAmt += $ItemTotal;
				$ItemDiscAmt = $value['BilledQty'] * $value['DiscAmt'];
				$TotalDiscAmt += $ItemDiscAmt;
				$DisPer = ($value['DiscAmt'] / $value['BasicRate']) * 100;
				$ItemTaxableAmt = $ItemTotal - $ItemDiscAmt;
				$TotalTaxableAmt += $ItemTaxableAmt;
				$ItemGSTAmt = $ItemTaxableAmt * ($value['GSTPer'] / 100);
				$CGST = 0;
				$SGST = 0;
				$IGST = 0;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				if ($State == $CenterState) {
					$CGST = $value['GSTPer'] / 2;
					$SGST = $value['GSTPer'] / 2;
					$CGSTAmt = $ItemGSTAmt / 2;
					$SGSTAmt = $ItemGSTAmt / 2;
					$TotalCGSTAmt += $CGSTAmt;
					$TotalSGSTAmt += $SGSTAmt;
				} else {
					$IGST = $value['GSTPer'];
					$IGSTAmt = $ItemGSTAmt;
					$TotalIGSTAmt += $IGSTAmt;
				}
				$ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
				$TotalNetAmt += $ItemNetAmt;

				if($value['SaleUnit'] == 'Boxs'){
					$BasicRate = $value['BasicRate'] / $value['PackingQty'];
				}else{
					$BasicRate = $value['BasicRate'];
				}

				$data_array_result = array(
					'PlantID'       => $PlantID,
					'FY'            => $FY,
					'OrderID'       => $new_sale_orderNumbar,
					'TransDate'     => $Transdate,
					'TransDate2'    => date('Y-m-d H:i:s'),
					'TType'         => 'O',
					'TType2'        => 'Order',
					'AccountID'     => $data['vendor'],
					'ItemID'        => $productId,
					'CenterID'      => $CenterID,
					'PartyID'       => "KASPL",
					'GodownID'      => "WHO",
					'PurchRate'     => $BasicRate,
					'SaleRate'      => ($BasicRate + ($BasicRate * ($value['GSTPer'] / 100))),
					'BasicRate'     => $BasicRate,
					'SuppliedIn'    => $value['SaleUnit'],
					'OrderQty'      => ($value['BilledQty'] * $value['PackingQty']),
					'BilledQty'     => ($value['BilledQty'] * $value['PackingQty']),
					'DiscPerc'      => $DisPer,
					'DiscAmt'       => $value['DiscAmt'],
					'cgst'          => $CGST,
					'cgstamt'       => $CGSTAmt,
					'sgst'          => $SGST,
					'sgstamt'       => $SGSTAmt,
					'igst'          => $CGST,
					'igstamt'       => $IGSTAmt,
					'CaseQty'       => $value['PackingQty'],
					'Cases'         => $value['BilledQty'],
					'OrderAmt'      => $ItemTotal,
					'ChallanAmt'    => $ItemTotal,
					'NetOrderAmt'   => $ItemNetAmt,
					'NetChallanAmt' => $ItemNetAmt,
					'Ordinalno'     => $i,
					'rowid'         => "",
					'UserID'        => $_SESSION['username'],
					'cnfid'         => ""
				);
				$this->db->insert(db_prefix() . 'K1history', $data_array_result);
				$i++;
			}
			$roundOffAmt = round($TotalNetAmt) - $TotalNetAmt;
			$KirtiOneOrdMaster = array(
				'saleamt'     => $TotalItemAmt,
				'Discamt'     => $TotalDiscAmt,
				'cgstamt'     => $TotalCGSTAmt,
				'sgstamt'     => $TotalSGSTAmt,
				'igstamt'     => $TotalIGSTAmt,
				'RoundOffAmt' => $roundOffAmt,
				'Invamt'      => round($TotalNetAmt),
			);
			$this->db->where('OrderID', $new_sale_orderNumbar);
			$this->db->update(db_prefix() . 'K1ordermaster', $KirtiOneOrdMaster);
			return $new_sale_orderNumbar;
		}
	}
	public function UpdateKirtiOneNewSaleOrder($data, $id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		if (isset($data['pur_order_detail'])) {
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
			$header[] = 'PackingWeight';
			$header[] = 'PurchaseUnit';
			$header[] = 'Qty';
			$header[] = 'PurchRate';
			$header[] = 'Discount';
			$header[] = 'GST';
			$header[] = 'CGSTAMT';
			$header[] = 'SGSTAMT';
			$header[] = 'IGSTAMT';
			$header[] = 'total_money';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$OrderID =  $id;
		$AccountID = $data['vendor'];
		$PaymentTerm = $data['PaymentTerm'];
		$CenterID = $data['centername'];
		$new_date =  to_sql_date($data['prd_date']) . " " . date('H:i:s');
		$saleAmt = $data['total_amt_in_mt'];
		$Discamt =  $data['total_disc_in_mt'];
		$cgstamt =  $data['total_cgst_amt'];
		$sgstamt =  $data['total_sgst_amt'];
		$igstamt =  $data['total_igst_amt'];
		$RoundOffAmt =  $data['total_roundoff_amt'];
		$Invamt =  $data['netpayableamt'];
		$ItCount = count($es_detail);
		$data_array = array(
			'Transdate'   => $new_date,
			'CenterID'    => $CenterID,
			'PaymentTerm' => $PaymentTerm,
			'AccountID'   => $AccountID,
			'saleamt'     => $saleAmt,
			'Discamt'     => $Discamt,
			'cgstamt'     => $cgstamt,
			'sgstamt'     => $sgstamt,
			'igstamt'     => $igstamt,
			'RoundOffAmt' => $RoundOffAmt,
			'Invamt'      => $Invamt,
			'ItCount'     => $ItCount,
			'OrderStatus' => $data['ordstat'] ?? 'O',
			'Lupdate'     => date('Y-m-d H:i:s'),
			'UserID2'     => $this->session->userdata('username')
		);
		$this->db->where('PlantID', $selected_company);
		$this->db->LIKE('FY', $fy);
		$this->db->where('OrderID', $OrderID);
		$this->db->update(db_prefix() . 'K1ordermaster', $data_array);
		if ($this->db->affected_rows() > 0) {
			$old_pur_details = $this->GetSaleOrderItemList($OrderID);
			// Move record from tblK1history to tblK1history_audit
			$TType = '';
			$TType2 = '';
			foreach ($old_pur_details as $key => $value) {
				$TType = $value["TType"];
				$TType2 = $value["TType2"];
				if ($value["igst"] == null) {
					$value["igst"] = "";
					$value["igstamt"] = "";
				} else if ($value["cgst"] == null) {
					$value["cgst"] = "";
					$value["cgstamt"] = "";
					$value["sgst"] = "";
					$value["sgstamt"] = "";
				}
				$old_data = array(
					'PlantID'       => $value["PlantID"],
					'FY'            => $value["FY"],
					'OrderID'       => $value["OrderID"],
					'BillID'        => $value["BillID"],
					'TransID'       => $value["TransID"],
					'TransDate'     => $value["TransDate"],
					'TransDate2'    => $value["TransDate2"],
					'TType'         => $value["TType"],
					'TType2'        => $value["TType2"],
					'AccountID'     => $value["AccountID"],
					'ItemID'        => $value["ItemID"],
					// 'TypeID'=>$value["TypeID"],
					'CenterID'      => $value["CenterID"],
					'GodownID'      => $value["GodownID"],
					'PartyID'       => $value["PartyID"],
					'PurchRate'     => $value["PurchRate"],
					'SaleRate'      => $value['SaleRate'],
					'BasicRate'     => $value['BasicRate'],
					'SuppliedIn'    => $value["SuppliedIn"],
					'OrderQty'      => $value['OrderQty'],
					'eOrderQty'     => $value['eOrderQty'],
					'BilledQty'     => $value['BilledQty'],
					'DiscPerc'      => $value["DiscPerc"],
					'DiscAmt'       => $value['DiscAmt'],
					'cgst'          => $value["cgst"],
					'cgstamt'       => $value['cgstamt'],
					'sgst'          => $value["sgst"],
					'sgstamt'       => $value['sgstamt'],
					'igst'          => $value["igst"],
					'igstamt'       => $value['igstamt'],
					'CaseQty'       => $value['CaseQty'],
					'Cases'         => $value['Cases'],
					'OrderAmt'      => $value['OrderAmt'],
					'ChallanAmt'    => $value['ChallanAmt'],
					'NetOrderAmt'   => $value['NetOrderAmt'],
					'NetChallanAmt' => $value['NetChallanAmt'],
					'Ordinalno'     => $value["Ordinalno"],
					'UserID'        => $value["UserID"],
					'Lupdate'       => date('Y-m-d H:i:s'),
					'UserID2'       => $_SESSION['username']
				);
				$this->db->insert(db_prefix() . 'K1history_audit', $old_data);
			}
			// Delete Live history table record 
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $OrderID);
			$this->db->delete(db_prefix() . 'K1history');
			// Add New history detail record		
			$i = 1;
			foreach ($es_detail as $value) {
				$productId = $value['ItemID'];
				$brand = $value['Brand'];
				$unit = $value['MeasuredIn'];
				$packing_qty = $value['PackingQty'];
				$packing_weight = $value['PackingWeight'];
				$saleunit = $value['PurchaseUnit'];
				$qty = $value['Qty'];
				$amount = $value['PurchRate'];
				$discountAmount = $value['Discount'] * $qty;
				$gst = $value['GST'];
				$cgstamts = $value['CGSTAMT'];
				$sgstamts = $value['SGSTAMT'];
				$igstamts = $value['IGSTAMT'];
				$netAmount = $value['total_money'];
				if ($saleunit == $unit) {
					$orderquantity = $packing_qty * $qty;
					$totalAmount = $qty * $amount;
				} else {
					$orderquantity = $qty;
					$amountval = ($amount / $packing_qty) * $qty;
					$totalAmount = $amountval;
				}
				// $discountAmount = ($discount / 100) * $totalAmount;  
				$discount = ($discountAmount / $totalAmount) * 100;
				$finalOrderAmt = $totalAmount - $discountAmount;
				$cgst = 0.00;
				$sgst = 0.00;
				$igst = 0.00;
				
				if($value['PurchaseUnit'] == 'Boxs'){
					$BasicRate = $value['PurchRate'] / $value['PackingQty'];
				}else{
					$BasicRate = $value['PurchRate'];
				}

				if ($gst != "") {
					if ($cgstamts > 0 && $sgstamts > 0) {
						$cgst = $cgstamts;
						$sgst = $sgstamts;
						$cgstPercentage = ($cgst / $finalOrderAmt) * 100;
						$sgstPercentage = $cgstPercentage;
						$totalPercentage = $cgstPercentage + $sgstPercentage;
						$salerate = $BasicRate * (1 + $totalPercentage / 100);
					} else if ($igstamts > 0) {
						$igst = $igstamts;
						$igstPercentage = ($igst / $finalOrderAmt) * 100;
						$salerate = $BasicRate * (1 + $igstPercentage / 100);
					}
				}
				if ($saleunit == "Loose") {
					$caseqty = 1;
				} else {
					$caseqty = $packing_qty;
				}
				$data_array_result = array(
					'PlantID'       => $selected_company,
					'FY'            => $fy,
					'OrderID'       => $OrderID,
					'TransDate'     => $new_date,
					'TransDate2'    => date('Y-m-d H:i:s'),
					'TType'         => $TType,
					'TType2'        => $TType2,
					'AccountID'     => $AccountID,
					'ItemID'        => $productId,
					'CenterID'      => $CenterID,
					'PartyID'       => "KASPL",
					'GodownID'      => "WHO",
					'PurchRate'     => $BasicRate,
					'SaleRate'      => $salerate,
					'BasicRate'     => $BasicRate,
					'SuppliedIn'    => $saleunit,
					'OrderQty'      => $orderquantity,
					'BilledQty'     => $orderquantity,
					'DiscPerc'      => $discount,
					'DiscAmt'       => $discountAmount,
					'cgst'          => $cgstPercentage,
					'cgstamt'       => $cgst,
					'sgst'          => $sgstPercentage,
					'sgstamt'       => $sgst,
					'igst'          => $igstPercentage,
					'igstamt'       => $igst,
					'CaseQty'       => $caseqty,
					'Cases'         => 0.00,
					'OrderAmt'      => $totalAmount,
					'ChallanAmt'    => $totalAmount,
					'NetOrderAmt'   => $netAmount,
					'NetChallanAmt' => $netAmount,
					'Ordinalno'     => $i,
					'UserID'        => $_SESSION['username']
				);
				$this->db->insert(db_prefix() . 'K1history', $data_array_result);
				$i++;
			}
		}
		return true;
	}
	public function load_data_for_sale_orderkirtione($data)
	{
		$from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$sql1 = '(' . db_prefix() . 'K1ordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") 
			AND tblK1ordermaster.FY = "' . $fy . '" 
			AND tblK1ordermaster.PlantID = "' . $selected_company . '" AND IsDirectSale = "N"        
			ORDER BY OrderID DESC';
		$sql = 'SELECT ' . db_prefix() . 'K1ordermaster.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ", ") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName
			FROM ' . db_prefix() . 'K1ordermaster WHERE ' . $sql1;
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	public function load_data_for_direct_sale_orderkirtione($data)
	{
		$from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$sql1 = '(' . db_prefix() . 'K1ordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") 
			AND tblK1ordermaster.FY = "' . $fy . '" 
			AND tblK1ordermaster.PlantID = "' . $selected_company . '" AND IsDirectSale = "Y"         
			';
		$join = "";
		if (!is_admin()) {
			$join .= ' INNER JOIN tblstaff_wise_center ON tblstaff_wise_center.CenterID = tblK1ordermaster.CenterID ';
			$sql1 .= ' AND tblstaff_wise_center.AccountID = "' . $UserID . '"';
		}
		if ($data["CategoryTypeFilter"]) {
			$sql1 .= ' AND tblK1ordermaster.CategoryType	 = "' . $data["CategoryTypeFilter"] . '"';
		}
		$sql1 .= ' ORDER BY OrderID DESC';
		$sql = 'SELECT ' . db_prefix() . 'K1ordermaster.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ", ") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName
			FROM ' . db_prefix() . 'K1ordermaster ' . $join . ' WHERE ' . $sql1;
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	public function PendingSaleOrderVendors()
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$this->db->select('tblclients.*, tblCustomerType.*, tblcontacts.*, tblxx_statelist.*, tblxx_citylist.*');
		$this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID');
		$this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType');
		$this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.id = tblclients.state', 'LEFT');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
		if (!is_admin()) {
			$this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1ordermaster.CenterID');
		}
		// $this->db->where('tblclients.IsKirtiOneAccess', 'Y');
		$this->db->where('tblK1ordermaster.OrderStatus', 'O');
		$this->db->where('tblK1ordermaster.FY', $fy);
		$this->db->where('tblK1ordermaster.PlantID', $selected_company);
		$this->db->group_by('tblK1ordermaster.AccountID');
		$Data = $this->db->get('tblK1ordermaster')->result_array();
		return $Data;
	}
	public function PendingSaleInvoiceVendors()
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$this->db->select('tblK1ordermaster.*, tblclients.*');
		// $this->db->join('tblK1ordermaster', 'tblK1ordermaster.ChallanID = tblK1challanmaster.ChallanID');
		$this->db->join('tblclients', 'tblclients.AccountID = tblK1ordermaster.AccountID', "LEFT");
		$this->db->join('tblCustomerType', 'tblCustomerType.id = tblclients.CustomerType', "LEFT");
		$this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID', "LEFT");
		$this->db->join('tblxx_statelist', 'tblxx_statelist.id = tblclients.state', 'LEFT');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
		if (!is_admin()) {
			$this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblK1ordermaster.CenterID');
		}
		$this->db->where('tblclients.IsKirtiOneAccess', 'Y');
		$this->db->where('tblK1ordermaster.OrderStatus', 'O');
		$this->db->where('tblK1ordermaster.FY', $fy);
		$this->db->where('tblK1ordermaster.PlantID', $selected_company);
		$this->db->group_by('tblK1ordermaster.AccountID');
		$Data = $this->db->get('tblK1ordermaster')->result_array();
		return $Data;
	}
	public function GetPartyList()
	{
		$this->db->distinct();
		$this->db->select('tblK1salesmaster.AccountID, tblclients.company');
		$this->db->from('tblK1challanmaster');
		$this->db->join(
			'tblK1salesmaster',
			'tblK1salesmaster.ChallanID = tblK1challanmaster.ChallanID',
			'INNER'
		);
		$this->db->join(
			'tblclients',
			'tblclients.AccountID = tblK1salesmaster.AccountID',
			'INNER'
		);
		$this->db->where('tblK1challanmaster.OrderStatus', 'A');
		$data = $this->db->get()->result_array();
		return $data;
	}
	public function UpdateKirtiOneDeliveryInvoice($data)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		if (isset($data['pur_order_detail'])) {
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
			$header[] = 'PackingWeight';
			$header[] = 'SaleUnit';
			$header[] = 'BatchList';
			$header[] = 'StockQty';
			$header[] = 'Qty';
			$header[] = 'DOQty';
			$header[] = 'PurchRate';
			$header[] = 'Discount';
			$header[] = 'GST';
			$header[] = 'CGSTAMT';
			$header[] = 'SGSTAMT';
			$header[] = 'IGSTAMT';
			$header[] = 'total_money';
			$header[] = 'ExpDate';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$AccountID = $data['vendor'];
		$ChallanID = $data['ChallanID'];
		$SaleAmt = $data['total_amt_in_mt'];
		$Discamt =  $data['total_disc_in_mt'];
		$cgstamt =  $data['total_cgst_amt'];
		$sgstamt =  $data['total_sgst_amt'];
		$igstamt =  $data['total_igst_amt'];
		$RoundOffAmt =  $data['total_roundoff_amt'];
		$Invamt =  $data['netpayableamt'];
		$ItCount = count($es_detail);
		$delivery_type = $data['type'];
		$OrderType = $data['ordtype'];
		$BillNo = $data['billno'];
		$Effecton = $data['Effecton'];
		$PaymentMode = $data['paymentmode'];
		$PaymentMethod = $data['paymentmethod'];
		$vehicleno = $data['vehicleno'];
		$DriverName = $data['DriverName'];
		$DriverMobile = $data['DriverMobile'];
		$TranportName = $data['TranportName'];
		$ReferenceNo = $data['referenceno'];
		$OrderFrom = $data['ordfrom'];
		$RndAmt = $data['total_roundoff_amt'];
		$RoundAmt = abs($RndAmt);
		if ($delivery_type == '2') {
			$ShippingID = $data['ShippingID'];
			if ($ShippingID == 'new') {
				$insert_address = array(
					'AccountID' => $AccountID,
					'House'     => $data['ShippingHouse'],
					'Street'    => $data['ShippingStreet'],
					'Locality'  => $data['ShippingLocality'],
					'Block'     => $data['ShippingBlock'],
					'Pincode'   => $data['ShippingPincode'],
					'State'     => $data['ShippingState'],
					'District'  => $data['ShippingCity'],
					"UserID"    => $_SESSION['username'],
					"TransDate" => date('Y-m-d H:i:s'),
				);
				$createnewaddress = $this->insert_data($tablename = "tblShippingDetails", $insert_address);
				$ShippingID = $this->db->insert_id();
			}
		} else {
			$ShippingID = null;
		}
		$this->db->select(db_prefix() . 'K1salesmaster.*');
		$this->db->where(db_prefix() . 'K1salesmaster.ChallanID', $ChallanID);
		$SalesData = $this->db->get(db_prefix() . 'K1salesmaster')->row();
		$KirtiOneSalesMaster = array(
			'vehicleno'     => $vehicleno,
			'DriverName'    => $DriverName,
			'DriverMobile'  => $DriverMobile,
			'TranportName'  => $TranportName,
			'SaleAmt'       => $SaleAmt,
			'DiscAmt'       => $Discamt,
			'cgstamt'       => $cgstamt,
			'sgstamt'       => $sgstamt,
			'igstamt'       => $igstamt,
			'RndAmt'        => $RoundOffAmt,
			'BillAmt'       => $Invamt,
			'DeliveryType'  => $delivery_type,
			'ShippingID'    => $ShippingID,
			'OrderType'     => $OrderType,
			'OrderFrom'     => $OrderFrom,
			'PartyBillNo'   => $BillNo,
			'PaymentMode'   => $paymode,
			'PaymentMethod' => $paymethod,
			'Effecton'      => $Effecton,
			'RefNo'         => $refnumber,
			'ItCount'       => $ItCount,
			"UserID2"       => $_SESSION['username'],
			"Lupdate"       => date('Y-m-d H:i:s'),
		);
		$this->db->where('PlantID', $selected_company);
		$this->db->LIKE('FY', $fy);
		$this->db->where('ChallanID', $ChallanID);
		$this->db->update(db_prefix() . 'K1salesmaster', $KirtiOneSalesMaster);
		if ($this->db->affected_rows() > 0) {
			// Update Status Challan Master
			$KirtiOneChallanMaster = array(
				'ChallanAmt'  => $Invamt,
				'OrderStatus' => 'F',
			);
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('ChallanID', $ChallanID);
			$this->db->update(db_prefix() . 'K1challanmaster', $KirtiOneChallanMaster);
			// Update history Challan Master
			$KirtiOnehistory = array(
				'TType'  => 'O',
				'TType2' => 'SALE',
			);
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('BillID', $ChallanID);
			$this->db->update(db_prefix() . 'K1history', $KirtiOnehistory);
			$ReceiptVoucherID = $SalesData->ReceiptVoucherID;
			$SalesId = $SalesData->SalesID;
			$CenterId = $SalesData->CenterID;
			$date = substr($SalesData->Transdate, 0, 19);
			// Delete Previous Ledger Entries
			$this->db->where('VoucherID', $SalesId);
			$this->db->delete(db_prefix() . 'accountledger');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('VoucherID', $ReceiptVoucherID);
			$this->db->where('PassedFrom', 'RECEIPTS');
			$this->db->delete(db_prefix() . 'accountledger');
			$ord = 1;
			$narration = "By SalesID " . $SalesId . "/" . $ChallanID;
			$insert_customer_ledger = array(
				'PlantID'        => $selected_company,
				'FY'             => $fy,
				'Transdate'      => $date,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => $AccountID,
				'CounterAccount' => "SALE",
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "D",
				'Amount'         => $Invamt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $UserID
			);
			$CustLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $insert_customer_ledger);
			$ord++;
			// Add Sale Ledger Entry
			$sale_ledger_entry = array(
				'PlantID'        => $selected_company,
				'FY'             => $fy,
				'Transdate'      => $date,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => "SALE",
				'CounterAccount' => $AccountID,
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "C",
				'Amount'         => $SaleAmt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $UserID
			);
			$SalesLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $sale_ledger_entry);
			$ord++;
			if ($cgstamt != 0.00 && $sgstamt != 0.00) {
				// CGST Tax Ledger Entry
				$Cgst_Ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "CGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $cgstamt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$CgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Cgst_Ledger_entry);
				$ord++;
				// SGST Tax Ledger Entry
				$Sgst_Ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "SGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $sgstamt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$SgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Sgst_Ledger_entry);
				$ord++;
			} else if ($igstamt != 0.00) {
				// Igst Ledger Entry
				$Igst_Ledger_Entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "IGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $igstamt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$IgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Igst_Ledger_Entry);
				$ord++;
			}
			// Discount Ledger Entry
			if ($Discamt > 0) {
				$disc_ledger_entry = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "DISC",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $Discamt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$DiscountLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $disc_ledger_entry);
				$ord++;
			}
			// RndAmt Ledger Entry
			if ($RoundOffAmt >= 0) {
				$roundledgerentry_debit = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "ROUNDOFF",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $RoundOffAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$Round_Debit_LedgerEntry = $this->insert_data($tablename = "tblaccountledger", $roundledgerentry_debit);
				$ord++;
			} else {
				$amt =  abs($RoundOffAmt);
				$roundledgerentry_credit = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "ROUNDOFF",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $amt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $UserID
				);
				$Round_credit_LedgerEntry = $this->insert_data($tablename = "tblaccountledger", $roundledgerentry_credit);
				$ord++;
			}
			if ($OrderType == 1) {
				// $nextReceiptnumber = get_option('next_receipts_number_for_kirti');  
				$nextReceiptnumber = $this->generateNextVoucherIDNew($date, $selected_company, 'RECEIPTS');
				$ordinalno = 1;
				// Receipt Voucher credit Entry to party
				$receiptentry_credit_toParty = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $nextReceiptnumber,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => $AccountID,
					'CounterAccount' => $Effecton,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $Invamt,
					'Narration'      => $narration,
					'PassedFrom'     => "RECEIPTS",
					'OrdinalNo'      => $ordinalno,
					'UserID'         => $UserID
				);
				$CreditToParty = $this->insert_data($tablename = "tblaccountledger", $receiptentry_credit_toParty);
				$ordinalno++;
				// Receipt Voucher Debit Entry to Company
				$receiptentry_debitto_company = array(
					'PlantID'        => $selected_company,
					'FY'             => $fy,
					'Transdate'      => $date,
					'VoucherID'      => $nextReceiptnumber,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => $Effecton,
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $Invamt,
					'Narration'      => $narration,
					'PassedFrom'     => "RECEIPTS",
					'OrdinalNo'      => $ordinalno,
					'UserID'         => $UserID
				);
				$DebitToCompany = $this->insert_data($tablename = "tblaccountledger", $receiptentry_debitto_company);
				// $this->increment_next_number('next_receipts_number_for_kirti');
				$update_sales = array(
					"ReceiptVoucherID" => $nextReceiptnumber
				);
				$wh_updatesales = '(SalesID="' . $SalesId . '")';
				$updatesales = $this->edit_data($tablename = "tblK1salesmaster", $wh_updatesales, $update_sales);
			}
		}
		return $ChallanID;
	}
	public function Update_DeliveryInv_KirtiOne($data)
	{
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		if (isset($data['pur_order_detail'])) {
			$pur_order_detail = json_decode($data['pur_order_detail']);
			unset($data['pur_order_detail']);
			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];
			$header[] = 'ItemID';
			$header[] = 'HSN';
			$header[] = 'BrandID';
			$header[] = 'UOM';
			$header[] = 'PackingQty';
			$header[] = 'PackingWeight';
			$header[] = 'SaleUnit';
			$header[] = 'BatchList';
			$header[] = 'StockQty';
			$header[] = 'SOQty';
			$header[] = 'DOQty';
			$header[] = 'BasicRate';
			$header[] = 'DiscAmt';
			$header[] = 'GSTPer';
			$header[] = 'cgstamt';
			$header[] = 'sgstamt';
			$header[] = 'igstamt';
			$header[] = 'Netamt';
			$header[] = 'ExpDate';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$ItCount = count($es_detail);
		$AccountID = $data['vendor'];
		$OrderID = $data['OrderID'];
		$PartyState = $data['state'];
		$CenterState = $data['CenterState'];
		$CenterID = $data['CenterID'];
		// $DeliveryDate =  to_sql_date($data['prd_date'])." ".date('H:i:s');  
		$ChallanID =  $data['ChallanID'];
		$TransID = $data['TransID'];
		$DeliveryType = $data['type'];
		$Transportname = $data['TranportName'];
		$DriverMobile = $data['DriverMobile'];
		$DriverName = $data['DriverName'];
		$VehicleNo = $data['vehicleno'];
		$EwaybillNo = $data['ewayno'];
		$EinvoiceNo = $data['E_Invoice_No'];
		$BillNo = $data['billno'];
		$OrderFrom = $data['ordfrom'];
		$OrderType = $data['ordtype'];
		$OthEffectOn = $data['OthEffectOn'];
		$OtherAmt = $data['OtherAmt'];
		$data_array = array(
			'OrderStatus' => 'F',
			// 'Transdate' =>$DeliveryDate,    
			'Lupdate'     => date('Y-m-d H:i:s'),
			'UserID2'     => $this->session->userdata('username')
		);
		$this->db->where('PlantID', $PlantID);
		$this->db->LIKE('FY', $FY);
		$this->db->where('ChallanID', $ChallanID);
		$this->db->update(db_prefix() . 'K1challanmaster', $data_array);
		if ($this->db->affected_rows() > 0) {
			$KirtiOneSalesMaster = array(
				'Effecton'         => $Effecton,
				'PaymentMode'      => $paymentmode,
				'OrderType'        => $OrderType,
				'OrderFrom'        => $OrderFrom,
				'PartyBillNo'      => $BillNo,
				'E_Invoice_No'     => $EinvoiceNo,
				'vehicleno'        => $VehicleNo,
				'DriverName'       => $DriverName,
				'DriverMobile'     => $DriverMobile,
				'TranportName'     => $Transportname,
				'DeliveryType'     => $DeliveryType,
				'OtherAmt'         => $OtherAmt,
				'EffectOnOtherAmt' => $OthEffectOn,
				'ItCount'          => $ItCount,
				'GSTIN'            => $data['PartyGSTIN'],
				"UserID2"          => $_SESSION['username'],
				"Lupdate"          => date('Y-m-d H:i:s'),
			);
			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $FY);
			$this->db->where('ChallanID', $ChallanID);
			$this->db->update(db_prefix() . 'K1salesmaster', $KirtiOneSalesMaster);
			$i = 1;
			$OrderTotalAmt = 0;
			$OrderDiscTotal = 0;
			$OrderCGSTTotal = 0;
			$OrderSGSTTotal = 0;
			$OrderIGSTTotal = 0;
			$OrderNetTotal = 0;
			$OrderTaxableTotal = 0;
			foreach ($es_detail as $value) {
				if($value['SaleUnit'] == 'Boxs'){
					$BasicRate = $value['BasicRate'] / $value['PackingQty'];
				}else{
					$BasicRate = $value['BasicRate'];
				}
				$SaleRate = $BasicRate + ($BasicRate * ($value["GSTPer"] / 100));
				$ItemTotal = $value["DOQty"] * $value["BasicRate"];
				$OrderTotalAmt += $ItemTotal;
				$DiscAmt = 0;
				if ($value["DiscAmt"] > 0) {
					$DiscAmt = $value["DiscAmt"] * $value["DOQty"];
					$OrderDiscTotal += $DiscAmt;
				}
				$TaxableAmt = $ItemTotal - $DiscAmt;
				$OrderTaxableTotal += $TaxableAmt;
				$GSTAmt = 0;
				if ($value["GSTPer"] > 0) {
					$GSTAmt = $TaxableAmt * ($value["GSTPer"] / 100);
				}
				$NetAmt = $TaxableAmt + $GSTAmt;
				$OrderNetTotal += $NetAmt;
				$CGST = 0;
				$SGST = 0;
				$IGST = 0;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				if ($CenterState == $PartyState && $GSTAmt > 0) {
					$CGST = $value["GSTPer"] / 2;
					$SGST = $value["GSTPer"] / 2;
					$CGSTAmt = $GSTAmt / 2;
					$OrderCGSTTotal += $CGSTAmt;
					$SGSTAmt = $GSTAmt / 2;
					$OrderSGSTTotal += $SGSTAmt;
				} elseif ($CenterState != $PartyState && $GSTAmt > 0) {
					$IGST = $value["GSTPer"];
					$IGSTAmt = $GSTAmt;
					$OrderIGSTTotal += $IGSTAmt;
				}
				$DiscPer = $value["DiscAmt"] / $value["BasicRate"];
				$BilledQty = $value["DOQty"] * $value["PackingQty"];
				$data_array_result = array(
					// 'TransDate' =>$Transdate,
					// 'TransDate2'=>date('Y-m-d H:i:s'),
					// 'BatchNo'       => $value["BatchList"],
					// 'ExpDate'       => to_sql_date($value["ExpDate"]),
					'PurchRate'     => $BasicRate,
					'SaleRate'      => $SaleRate,
					'BasicRate'     => $BasicRate,
					'SuppliedIn'    => $value["SaleUnit"],
					'OrderQty'      => $BilledQty,
					'BilledQty'     => $BilledQty,
					'DiscPerc'      => $DiscPer,
					'DiscAmt'       => $DiscAmt,
					'cgst'          => $CGST,
					'cgstamt'       => $CGSTAmt,
					'sgst'          => $SGST,
					'sgstamt'       => $SGSTAmt,
					'igst'          => $IGST,
					'igstamt'       => $IGSTAmt,
					'CaseQty'       => $value["PackingQty"],
					'Cases'         => $value["DOQty"],
					'OrderAmt'      => $ItemTotal,
					'ChallanAmt'    => $ItemTotal,
					'NetOrderAmt'   => $NetAmt,
					'NetChallanAmt' => $NetAmt,
					'UserID2'       => $_SESSION['username'],
					'Lupdate'       => date('Y-m-d H:i:s'),
				);
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('BillID', $ChallanID);
				$this->db->where('BatchNo', $value["BatchList"]);
				$this->db->where('TType', "O");
				$this->db->where('TType2', "SALE");
				$this->db->where('ItemID', $value["ItemID"]);
				$this->db->update(db_prefix() . 'K1history', $data_array_result);
				$i++;
			}
			$ChallanUpdate = array(
				"ChallanAmt" => $OrderNetTotal
			);
			$this->db->where(db_prefix() . 'K1challanmaster.ChallanID', $ChallanID);
			$this->db->update(db_prefix() . 'K1challanmaster', $ChallanUpdate);
			$roundAmt = $OrderNetTotal - round($OrderNetTotal);
			$SalesUpdate = array(
				"SaleAmt" => $OrderTotalAmt,
				"DiscAmt" => $OrderDiscTotal,
				"sgstamt" => $OrderSGSTTotal,
				"cgstamt" => $OrderCGSTTotal,
				"igstamt" => $OrderIGSTTotal,
				"BillAmt" => $OrderNetTotal,
				"RndAmt"  => round($OrderNetTotal),
				"ItCount" => $i - 1
			);
			$this->db->where(db_prefix() . 'K1salesmaster.SalesID', $TransID);
			$this->db->update(db_prefix() . 'K1salesmaster', $SalesUpdate);
			$this->db->select(db_prefix() . 'K1salesmaster.*');
			$this->db->where(db_prefix() . 'K1salesmaster.ChallanID', $ChallanID);
			$SalesData = $this->db->get(db_prefix() . 'K1salesmaster')->row();
			$ReceiptVoucherID = $SalesData->ReceiptVoucherID;
			$SalesId = $SalesData->SalesID;
			$CenterId = $SalesData->CenterID;
			$date = substr($SalesData->Transdate, 0, 19);
			// Delete Previous Ledger Entries
			$this->db->where('VoucherID', $TransID);
			$this->db->delete(db_prefix() . 'accountledger');
			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $FY);
			$this->db->where('VoucherID', $ReceiptVoucherID);
			$this->db->where('PassedFrom', 'RECEIPTS');
			$this->db->delete(db_prefix() . 'accountledger');
			// Ledger Entries Insert
			$ord = 1;
			$narration = "By SalesID " . $TransID . "/" . $ChallanID;
			$insert_customer_ledger = array(
				'PlantID'        => $PlantID,
				'FY'             => $FY,
				'Transdate'      => $date,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => $AccountID,
				'CounterAccount' => "SALE",
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "D",
				'Amount'         => ($OrderNetTotal + $OtherAmt),
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $_SESSION['username'],
			);
			$CustLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $insert_customer_ledger);
			$ord++;
			if ($OtherAmt > 0) {
				$Otherledger_entry = array(
					'PlantID'        => $PlantID,
					'FY'             => $FY,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => $OthEffectOn,
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OtherAmt,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $_SESSION['username'],
				);
				$SalesLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Otherledger_entry);
				$ord++;
			}
			// Add Sale Ledger Entry
			$sale_ledger_entry = array(
				'PlantID'        => $PlantID,
				'FY'             => $FY,
				'Transdate'      => $date,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => "SALE",
				'CounterAccount' => $AccountID,
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "C",
				'Amount'         => $OrderTotalAmt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $_SESSION['username'],
			);
			$SalesLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $sale_ledger_entry);
			$ord++;
			if ($OrderCGSTTotal != 0.00 && $OrderSGSTTotal != 0.00) {
				// CGST Tax Ledger Entry
				$Cgst_Ledger_entry = array(
					'PlantID'        => $PlantID,
					'FY'             => $FY,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "CGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrderCGSTTotal,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $_SESSION['username'],
				);
				$CgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Cgst_Ledger_entry);
				$ord++;
				// SGST Tax Ledger Entry
				$Sgst_Ledger_entry = array(
					'PlantID'        => $PlantID,
					'FY'             => $FY,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "SGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrderSGSTTotal,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $_SESSION['username'],
				);
				$SgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Sgst_Ledger_entry);
				$ord++;
			} else if ($OrderIGSTTotal != 0.00) {
				// Igst Ledger Entry
				$Igst_Ledger_Entry = array(
					'PlantID'        => $PlantID,
					'FY'             => $FY,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "IGST",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "C",
					'Amount'         => $OrderIGSTTotal,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $_SESSION['username'],
				);
				$IgstLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $Igst_Ledger_Entry);
				$ord++;
			}
			// Discount Ledger Entry
			if ($OrderDiscTotal > 0) {
				$disc_ledger_entry = array(
					'PlantID'        => $PlantID,
					'FY'             => $FY,
					'Transdate'      => $date,
					'VoucherID'      => $SalesId,
					'Transdate2'     => date('Y-m-d h:i:s'),
					'PartyID'        => "KASPL",
					'AccountID'      => "DISC",
					'CounterAccount' => $AccountID,
					'CenterID'       => $CenterId,
					'EntryFor'       => 3,
					'TType'          => "D",
					'Amount'         => $OrderDiscTotal,
					'Narration'      => $narration,
					'PassedFrom'     => "SALES",
					'OrdinalNo'      => $ord,
					'UserID'         => $_SESSION['username'],
				);
				$DiscountLedgerEntry = $this->insert_data($tablename = "tblaccountledger", $disc_ledger_entry);
				$ord++;
			}
			// RndAmt Ledger Entry
			$roundledgerentry_debit = array(
				'PlantID'        => $PlantID,
				'FY'             => $FY,
				'Transdate'      => $date,
				'VoucherID'      => $SalesId,
				'Transdate2'     => date('Y-m-d h:i:s'),
				'PartyID'        => "KASPL",
				'AccountID'      => "ROUNDOFF",
				'CounterAccount' => $AccountID,
				'CenterID'       => $CenterId,
				'EntryFor'       => 3,
				'TType'          => "D",
				'Amount'         => $roundAmt,
				'Narration'      => $narration,
				'PassedFrom'     => "SALES",
				'OrdinalNo'      => $ord,
				'UserID'         => $_SESSION['username'],
			);
			$Round_Debit_LedgerEntry = $this->insert_data($tablename = "tblaccountledger", $roundledgerentry_debit);
			$ord++;
		}
		return true;
	}
	public function GetSaleOrderItemListForDelivery($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*,(tblK1history.OrderQty/tblK1history.CaseQty) AS DOQty,
			tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS UOM,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			tblK1history.SuppliedIn AS SaleUnit,
			tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS GSTPer,tblbrands.BrandName AS BrandID,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID  AND RcvHistory.OrderID = tblK1history.OrderID AND RcvHistory.TType ="O" AND RcvHistory.TType2="Order" AND RcvHistory.BillID IS NOT NULL) As SendQty,
			(Select SUM(RqstHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RqstHistory where RqstHistory.ItemID = tblK1history.ItemID  AND RqstHistory.OrderID = tblK1history.OrderID AND RqstHistory.TType ="O" AND RqstHistory.TType2="Order" AND RqstHistory.BillID IS NULL) As SOQty');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
		$this->db->where(db_prefix() . 'K1history.OrderID', $id);
		$this->db->where(db_prefix() . 'K1history.BillID IS NULL');
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$this->db->order_by(db_prefix() . 'K1history.Ordinalno', "ASC");
		$results = $this->db->get()->result_array();
		foreach ($results as &$row) {
			$row['PendingQty'] = $row['SOQty'] - $row['SendQty'];
			$BatchList = array(
				array(
					'id'    => 1,
					'label' => 'first'
				),
				array(
					'id'    => 2,
					'label' => 'second'
				)
			);
			$row['BatchList'] = $BatchList;
			$filterdata = [
				'ItemID'   => $row['ItemID'],
				'CenterID' => $row['CenterID'],
				'PartyID'  => $row['AccountID'],
				'OrderID'  => $row['OrderID'],
			];
			$StockDataBatchWise = $this->GetItemBatchListWithStock($filterdata);
			$row['BatchList'] = $StockDataBatchWise;
			/*$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach ($StockData as $stockkey=>$stockval) {
					if ($stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
						$SaleQty += $stockval["TotalQty"];
						} else if ($stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
						$PurchQty += $stockval["TotalQty"];
						} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
						$InQty += $stockval["TotalQty"];
						} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
						$OutQty += $stockval["TotalQty"];
						} else if ($stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
						$InwardQty += $stockval["TotalQty"];
					}
				}
				$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				*/
			$row['StockQty'] = 0;
		}
		return $results;
	}
	public function GetChallanItemListForDelivery($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*, (tblK1history.OrderQty/tblK1history.CaseQty) AS DOQty,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscAmt AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,tblK1history.BatchNo AS BatchList');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
		$this->db->where(db_prefix() . 'K1history.BillID', $id);
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$results = $this->db->get()->result_array();
		foreach ($results as &$row) {
			if ($row['PackingQty'] > 1) {
				$row['OrderQty'] = $row['OrderQty'] / $row['PackingQty'];
			}
			$row['Discount'] = $row['Discount'] / $row['OrderQty'];
			$filterdata = [
				'ItemID'   => $row['ItemID'],
				'CenterID' => $row['CenterID'],
				'PartyID'  => $row['AccountID'],
				'OrderID'  => $row['OrderID'],
			];
			$StockDataBatchWise = $this->GetItemBatchListWithStock($filterdata);
			$row['StockQty'] = ($StockDataBatchWise[0]['Stock'] + $row['BilledQty']);
			// $row['ExpDate'] = 
		}
		return $results;
	}
	public function get_order_PO_ven_details($id)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$this->db->select('tblK1ordermaster.*');
		$this->db->from(db_prefix() . 'K1ordermaster');
		$this->db->where('tblK1ordermaster.AccountID', $id);
		$this->db->where('tblK1ordermaster.OrderStatus', 'O');
		$this->db->where('tblK1ordermaster.PlantID', $selected_company);
		$this->db->where('tblK1ordermaster.FY', $fy);
		$result = $this->db->get()->result();
		return $result;
	}
	public function GetApprovedChallanByVendor($id)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$this->db->select('tblK1challanmaster.*');
		$this->db->join('tblK1salesmaster', 'tblK1salesmaster.ChallanID = tblK1challanmaster.ChallanID');
		$this->db->from(db_prefix() . 'K1challanmaster');
		$this->db->where('tblK1salesmaster.AccountID', $id);
		$this->db->where('tblK1challanmaster.OrderStatus', 'A');
		$this->db->where('tblK1challanmaster.PlantID', $selected_company);
		$this->db->where('tblK1challanmaster.FY', $fy);
		$result = $this->db->get()->result();
		return $result;
	}
	// ================= Get Account CLosing Balance ==================================
	public function GetAccountClosingBalance($VendorID)
	{
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		// Closing Bal
		// Get Opening balance
		$this->db->select("tblaccountbalances.*");
		$this->db->from(db_prefix() . 'accountbalances');
		$this->db->where(db_prefix() . 'accountbalances.AccountID', $VendorID);
		$this->db->where(db_prefix() . 'accountbalances.PlantID', $PlantID);
		$this->db->where(db_prefix() . 'accountbalances.FY', $FY);
		$this->db->where(db_prefix() . 'accountbalances.PartyID', "KASPL");
		$OpnBalDetails = $this->db->get()->row();
		$OpnBal = 0;
		if ($OpnBalDetails) {
			$OpnBal = $OpnBalDetails->BAL1;
		}
		// Get Transaction Entry
		$this->db->select("SUM(tblaccountledger.Amount) AS TotalAmt,tblaccountledger.TType");
		$this->db->from(db_prefix() . 'accountledger');
		$this->db->where(db_prefix() . 'accountledger.AccountID', $VendorID);
		$this->db->where(db_prefix() . 'accountledger.PlantID', $PlantID);
		$this->db->where(db_prefix() . 'accountledger.FY', $FY);
		$this->db->where(db_prefix() . 'accountledger.PartyID', "KASPL");
		$this->db->group_by(db_prefix() . 'accountledger.TType');
		$LedgerDetails = $this->db->get()->result_array();
		$CreditAmt = 0;
		$DebitAmt = 0;
		foreach ($LedgerDetails as $key => $val) {
			if ($val["TType"] == "C") {
				$CreditAmt = $val["TotalAmt"];
			} else if ($val["TType"] == "D") {
				$DebitAmt = $val["TotalAmt"];
			}
		}
		$ClosingBal = $OpnBal - $CreditAmt + $DebitAmt;
		return $ClosingBal;
	}
	// =================== Add Kirti One Purchase Invoice =============================
	public function AddKirtiOneDeliveryOrder($data)
	{
		if (isset($data['pur_order_detail'])) {
			$pur_order_detail = json_decode($data['pur_order_detail']);
			unset($data['pur_order_detail']);
			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];
			$header[] = 'ItemID';
			$header[] = 'HSN';
			$header[] = 'BrandID';
			$header[] = 'UOM';
			$header[] = 'PackingQty';
			$header[] = 'SaleUnit';
			$header[] = 'BatchList';
			$header[] = 'StockQty';
			$header[] = 'SOQty';
			$header[] = 'DOQty';
			$header[] = 'BasicRate';
			$header[] = 'DiscAmt';
			$header[] = 'GSTPer';
			$header[] = 'cgstamt';
			$header[] = 'sgstamt';
			$header[] = 'igstamt';
			$header[] = 'Netamt';
			$header[] = 'ExpDate';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$i = 1;
		$OrderTotalAmt = 0;
		$OrderDiscTotal = 0;
		$OrderCGSTTotal = 0;
		$OrderSGSTTotal = 0;
		$OrderIGSTTotal = 0;
		$OrderNetTotal = 0;
		$OrderTaxableTotal = 0;
		foreach ($es_detail as $value) {
			$ItemTotal = $value["DOQty"] * $value["BasicRate"];
			$OrderTotalAmt += $ItemTotal;
			$DiscAmt = 0;
			if ($value["DiscAmt"] > 0) {
				$DiscAmt = $value["DiscAmt"] * $value["DOQty"];
				$OrderDiscTotal += $DiscAmt;
			}
			$TaxableAmt = $ItemTotal - $DiscAmt;
			$OrderTaxableTotal += $TaxableAmt;
			$GSTAmt = 0;
			if ($value["GSTPer"] > 0) {
				$GSTAmt = $TaxableAmt * ($value["GSTPer"] / 100);
			}
			$NetAmt = $TaxableAmt + $GSTAmt;
			$OrderNetTotal += $NetAmt;
		}
		$VendorID = $data['vendor'];
		$PaymentTerm = $data['PaymentTermHidden'];
		$AccountClsBal = $this->GetAccountClosingBalance($VendorID);
		// echo "<pre>";
		// echo $PaymentTerm;
		// print_r($AccountClsBal);
		// die;
		if ($AccountClsBal > 0 && $PaymentTerm == "A") {
			$response = array(
				"status"    => false,
				"message"   => "DO creation not allowed. Party has a Debit Balance of ₹." . $AccountClsBal,
				"ChallanID" => ""
			);
			return $response;
		} else if ($OrderNetTotal > abs($AccountClsBal) && $PaymentTerm == "A") {
			$msg = "DO creation not allowed. Available Credit Balance (₹ " . $AccountClsBal . ") is less than the DO Amount (₹ " . $OrderNetTotal . ").";
			$response = array(
				"status"    => false,
				"message"   => $msg,
				"ChallanID" => ""
			);
			return $response;
		} else {
			foreach ($es_detail as $value) {
				if(empty($value["BatchList"])){
					return array(
						"status"    => false,
						"message"   => "Batch Number and Expiry Date is required",
						"ChallanID" => ""
					);
				}else{
					$filterdata = [
						'ItemID'   => $value['ItemID'],
						'CenterID' => $data['CenterID'],
						'BatchID'  => $value["BatchList"]
					];
					$StockDataBatchWise = $this->GetItemBatchListWithStock($filterdata);
					if(empty($StockDataBatchWise)){
						return array(
							"status"    => false,
							"message"   => "For Batch ".$value["BatchList"]." Stock is not available",
							"ChallanID" => ""
						);
					}else{
						if($StockDataBatchWise[0]['Stock'] < $value["DOQty"]){
							return array(
								"status"    => false,
								"message"   => "For Batch ".$value["BatchList"]." Stock is ".$StockDataBatchWise[0]['Stock'].". DO Quantity must be less than or equal to Stock",
								"ChallanID" => ""
							);
						}
					}
				}
			}

			$prefix = 'CHL';
			$Challan_Inv_Numbar = get_option('next_K1Challan_number_for_kirti');
			$kirtione = 1;
			$new_Challan_Inv_Number = $prefix . $FY . $PlantID . $kirtione . $Challan_Inv_Numbar;
			// $new_Challan_Inv_Number = $prefix.$FY."1".$Challan_Inv_Numbar;          
			$Transdate =  to_sql_date($data['prd_date']) . " " . date('H:i:s');
			$VendorID = $data['vendor'];
			$OrderID = $data['OrderID'];
			$PartyState = $data['state'];
			$CenterState = $data['CenterState'];
			$CenterID = $data['CenterID'];
			$VendorGSTIN = $data['gst'];
			$KirtiOneChallanMaster = array(
				'PlantID'      => $PlantID,
				'FY'           => $FY,
				'ChallanID'    => $new_Challan_Inv_Number,
				'IsDirectSale' => 'N',
				'Transdate'    => $Transdate,
				'OrderStatus'  => 'P',
				"UserID"       => $_SESSION['username']
			);
			$this->db->insert(db_prefix() . 'K1challanmaster', $KirtiOneChallanMaster);
			if ($this->db->affected_rows() > 0) {
				$this->increment_next_number('next_K1Challan_number_for_kirti');
				$nextTaxNumber = get_option('next_K1Tax_number_for_kirti');
				$nextNonTaxNumber = get_option('next_K1NonTax_number_for_kirti');
				$prefixTaxNo = "TAX";
				$ConcatenatedTaxNumber = $prefixTaxNo . $FY . $PlantID . $nextTaxNumber;
				$prefixNonTaxNo = "BOS";
				$ConcatenatedNonTaxNumber = $prefixNonTaxNo . $FY . $PlantID . $nextNonTaxNumber;
				if (isset($es_detail[0]) && isset($es_detail[0]['GST']) && $es_detail[0]['GST'] <= 0) {
					$TransID = $ConcatenatedNonTaxNumber;
				} else {
					$TransID = $ConcatenatedTaxNumber;
				}
				$KirtiOneSalesMaster = array(
					'PlantID'   => $PlantID,
					'FY'        => $FY,
					'SalesID'   => $TransID,
					'GSTIN'     => $VendorGSTIN,
					'OrderID'   => $OrderID,
					'CenterID'  => $CenterID,
					'AccountID' => $VendorID,
					'ChallanID' => $new_Challan_Inv_Number,
					'Transdate' => $Transdate,
				);
				$this->db->insert(db_prefix() . 'K1salesmaster', $KirtiOneSalesMaster);
				if (isset($es_detail[0]) && isset($es_detail[0]['GST']) && $es_detail[0]['GST'] <= 1) {
					$this->increment_next_number('next_K1NonTax_number_for_kirti');
				} else {
					$this->increment_next_number('next_K1Tax_number_for_kirti');
				}
				$i = 1;
				$OrderTotalAmt = 0;
				$OrderDiscTotal = 0;
				$OrderCGSTTotal = 0;
				$OrderSGSTTotal = 0;
				$OrderIGSTTotal = 0;
				$OrderNetTotal = 0;
				$OrderTaxableTotal = 0;
				foreach ($es_detail as $value) {
					if($value['SaleUnit'] == 'Boxs'){
						$BasicRate = $value['BasicRate'] / $value['PackingQty'];
					}else{
						$BasicRate = $value['BasicRate'];
					}
					$SaleRate = $BasicRate + ($BasicRate * ($value["GSTPer"] / 100));
					$ItemTotal = $value["DOQty"] * $value['BasicRate'];
					$OrderTotalAmt += $ItemTotal;
					$DiscAmt = 0;
					if ($value["DiscAmt"] > 0) {
						$DiscAmt = $value["DiscAmt"] * $value["DOQty"];
						$OrderDiscTotal += $DiscAmt;
					}
					$TaxableAmt = $ItemTotal - $DiscAmt;
					$OrderTaxableTotal += $TaxableAmt;
					$GSTAmt = 0;
					if ($value["GSTPer"] > 0) {
						$GSTAmt = $TaxableAmt * ($value["GSTPer"] / 100);
					}
					$NetAmt = $TaxableAmt + $GSTAmt;
					$OrderNetTotal += $NetAmt;
					$CGST = 0;
					$SGST = 0;
					$IGST = 0;
					$CGSTAmt = 0;
					$SGSTAmt = 0;
					$IGSTAmt = 0;
					if ($CenterState == $PartyState && $GSTAmt > 0) {
						$CGST = $value["GSTPer"] / 2;
						$SGST = $value["GSTPer"] / 2;
						$CGSTAmt = $GSTAmt / 2;
						$OrderCGSTTotal += $CGSTAmt;
						$SGSTAmt = $GSTAmt / 2;
						$OrderSGSTTotal += $SGSTAmt;
					} elseif ($CenterState != $PartyState && $GSTAmt > 0) {
						$IGST = $value["GSTPer"];
						$IGSTAmt = $GSTAmt;
						$OrderIGSTTotal += $IGSTAmt;
					}
					$DiscPer = $value["DiscAmt"] / $value['BasicRate'];
					$BilledQty = $value["DOQty"] * $value["PackingQty"];
					$data_array_result = array(
						'PlantID'       => $PlantID,
						'FY'            => $FY,
						'OrderID'       => $OrderID,
						'BillID'        => $new_Challan_Inv_Number,
						'TransID'       => $TransID,
						'TransDate'     => $Transdate,
						'TransDate2'    => $Transdate,
						'TType'         => 'O',
						'TType2'        => 'SALE',
						'AccountID'     => $VendorID,
						'ItemID'        => $value["ItemID"],
						'CenterID'      => $CenterID,
						'PartyID'       => "KASPL",
						'GodownID'      => "WHO",
						'BatchNo'       => $value["BatchList"],
						'ExpDate'       => to_sql_date($value["ExpDate"]),
						'PurchRate'     => $BasicRate,
						'SaleRate'      => $SaleRate,
						'BasicRate'     => $BasicRate,
						'SuppliedIn'    => $value["SaleUnit"],
						'OrderQty'      => $BilledQty,
						'BilledQty'     => $BilledQty,
						'DiscPerc'      => $DiscPer,
						'DiscAmt'       => $DiscAmt,
						'cgst'          => $CGST,
						'cgstamt'       => $CGSTAmt,
						'sgst'          => $SGST,
						'sgstamt'       => $SGSTAmt,
						'igst'          => $IGST,
						'igstamt'       => $IGSTAmt,
						'CaseQty'       => $value["PackingQty"],
						'Cases'         => $value["DOQty"],
						'OrderAmt'      => $ItemTotal,
						'ChallanAmt'    => $ItemTotal,
						'NetOrderAmt'   => $NetAmt,
						'NetChallanAmt' => $NetAmt,
						'Ordinalno'     => $i,
						'UserID'        => $_SESSION['username'],
					);
					$this->db->insert(db_prefix() . 'K1history', $data_array_result);
					$i++;
				}
				$ChallanUpdate = array(
					"ChallanAmt" => $OrderNetTotal
				);
				$this->db->where(db_prefix() . 'K1challanmaster.ChallanID', $new_Challan_Inv_Number);
				$this->db->update(db_prefix() . 'K1challanmaster', $ChallanUpdate);
				$roundAmt = $OrderNetTotal - round($OrderNetTotal);
				$SalesUpdate = array(
					"SaleAmt" => $OrderTotalAmt,
					"DiscAmt" => $OrderDiscTotal,
					"sgstamt" => $OrderSGSTTotal,
					"cgstamt" => $OrderCGSTTotal,
					"igstamt" => $OrderIGSTTotal,
					"BillAmt" => $OrderNetTotal,
					"RndAmt"  => round($OrderNetTotal),
					"ItCount" => $i - 1
				);
				$this->db->where(db_prefix() . 'K1salesmaster.SalesID', $TransID);
				$this->db->update(db_prefix() . 'K1salesmaster', $SalesUpdate);
				// Update Order Status k1OrderMaster
				$OrderMasterUpdate = array(
					"OrderStatus" => 'F'
				);
				$this->db->where(db_prefix() . 'K1ordermaster.OrderID', $OrderID);
				$this->db->update(db_prefix() . 'K1ordermaster', $OrderMasterUpdate);
				$response = array(
					"status"    => true,
					"message"   => "DO generated successfully.",
					"ChallanID" => $new_Challan_Inv_Number
				);
				return $response;
			}
		}
	}
	// ======================== Edit Delivery Order =================================	
	public function UpdateKirtiOneDeliveryOrder($data, $id)
	{
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		if (isset($data['pur_order_detail'])) {
			$pur_order_detail = json_decode($data['pur_order_detail']);
			unset($data['pur_order_detail']);
			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];
			$header[] = 'ItemID';
			$header[] = 'HSN';
			$header[] = 'BrandID';
			$header[] = 'UOM';
			$header[] = 'PackingQty';
			$header[] = 'SaleUnit';
			$header[] = 'BatchList';
			$header[] = 'StockQty';
			$header[] = 'SOQty';
			$header[] = 'DOQty';
			$header[] = 'BasicRate';
			$header[] = 'DiscAmt';
			$header[] = 'GSTPer';
			$header[] = 'cgstamt';
			$header[] = 'sgstamt';
			$header[] = 'igstamt';
			$header[] = 'Netamt';
			$header[] = 'ExpDate';
			foreach ($pur_order_detail as $key => $value) {
				if ($value[0] != '') {
					$ItemID = $value[0];
					$es_detail[] = array_combine($header, $value);
				}
			}
		}
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$i = 1;
		$OrderTotalAmt = 0;
		$OrderDiscTotal = 0;
		$OrderCGSTTotal = 0;
		$OrderSGSTTotal = 0;
		$OrderIGSTTotal = 0;
		$OrderNetTotal = 0;
		$OrderTaxableTotal = 0;
		foreach ($es_detail as $value) {
			$ItemTotal = $value["DOQty"] * $value["BasicRate"];
			$OrderTotalAmt += $ItemTotal;
			$DiscAmt = 0;
			if ($value["DiscAmt"] > 0) {
				$DiscAmt = $value["DiscAmt"] * $value["DOQty"];
				$OrderDiscTotal += $DiscAmt;
			}
			$TaxableAmt = $ItemTotal - $DiscAmt;
			$OrderTaxableTotal += $TaxableAmt;
			$GSTAmt = 0;
			if ($value["GSTPer"] > 0) {
				$GSTAmt = $TaxableAmt * ($value["GSTPer"] / 100);
			}
			$NetAmt = $TaxableAmt + $GSTAmt;
			$OrderNetTotal += $NetAmt;
		}
		$VendorID = $data['vendor'];
		$PaymentTerm = $data['PaymentTerm'];
		$AccountClsBal = $this->GetAccountClosingBalance($VendorID);
		if ($AccountClsBal > 0 && $PaymentTerm == "A") {
			$response = array(
				"status"    => false,
				"message"   => "DO creation not allowed. Party has a Debit Balance of ₹." . $AccountClsBal,
				"ChallanID" => ""
			);
			return $response;
		} else if ($OrderNetTotal > abs($AccountClsBal) && $PaymentTerm == "A") {
			$msg = "DO creation not allowed. Available Credit Balance (₹ " . $AccountClsBal . ") is less than the DO Amount (₹ " . $OrderNetTotal . ").";
			$response = array(
				"status"    => false,
				"message"   => $msg,
				"ChallanID" => ""
			);
			return $response;
		} else {
			$ItCount = count($es_detail);
			$OrderID = $data['OrderID'];
			$PartyState = $data['state'];
			$CenterState = $data['CenterState'];
			$CenterID = $data['CenterID'];
			$DeliveryDate =  to_sql_date($data['prd_date']) . " " . date('H:i:s');
			$ChallanID =  $id;
			$TransID = $data['TransID'];
			$data_array = array(
				'Transdate' => $DeliveryDate,
				'Lupdate'   => date('Y-m-d H:i:s'),
				'UserID2'   => $this->session->userdata('username')
			);
			$this->db->where('PlantID', $PlantID);
			$this->db->LIKE('FY', $FY);
			$this->db->where('ChallanID', $ChallanID);
			$this->db->update(db_prefix() . 'K1challanmaster', $data_array);
			if ($this->db->affected_rows() > 0) {
				$KirtiOneSalesMaster = array(
					'Transdate' => $DeliveryDate,
					'ItCount'   => $ItCount,
					"UserID2"   => $_SESSION['username'],
					"Lupdate"   => date('Y-m-d H:i:s'),
				);
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('ChallanID', $ChallanID);
				$this->db->update(db_prefix() . 'K1salesmaster', $KirtiOneSalesMaster);
				$i = 1;
				$OrderTotalAmt = 0;
				$OrderDiscTotal = 0;
				$OrderCGSTTotal = 0;
				$OrderSGSTTotal = 0;
				$OrderIGSTTotal = 0;
				$OrderNetTotal = 0;
				$OrderTaxableTotal = 0;
				foreach ($es_detail as $value) {
					if($value['SaleUnit'] == 'Boxs'){
						$BasicRate = $value['BasicRate'] / $value['PackingQty'];
					}else{
						$BasicRate = $value['BasicRate'];
					}
					$SaleRate = $BasicRate + ($BasicRate * ($value["GSTPer"] / 100));
					$ItemTotal = $value["DOQty"] * $value['BasicRate'];
					$OrderTotalAmt += $ItemTotal;
					$DiscAmt = 0;
					if ($value["DiscAmt"] > 0) {
						$DiscAmt = $value["DiscAmt"] * $value["DOQty"];
						$OrderDiscTotal += $DiscAmt;
					}
					$TaxableAmt = $ItemTotal - $DiscAmt;
					$OrderTaxableTotal += $TaxableAmt;
					$GSTAmt = 0;
					if ($value["GSTPer"] > 0) {
						$GSTAmt = $TaxableAmt * ($value["GSTPer"] / 100);
					}
					$NetAmt = $TaxableAmt + $GSTAmt;
					$OrderNetTotal += $NetAmt;
					$CGST = 0;
					$SGST = 0;
					$IGST = 0;
					$CGSTAmt = 0;
					$SGSTAmt = 0;
					$IGSTAmt = 0;
					if ($CenterState == $PartyState && $GSTAmt > 0) {
						$CGST = $value["GSTPer"] / 2;
						$SGST = $value["GSTPer"] / 2;
						$CGSTAmt = $GSTAmt / 2;
						$OrderCGSTTotal += $CGSTAmt;
						$SGSTAmt = $GSTAmt / 2;
						$OrderSGSTTotal += $SGSTAmt;
					} elseif ($CenterState != $PartyState && $GSTAmt > 0) {
						$IGST = $value["GSTPer"];
						$IGSTAmt = $GSTAmt;
						$OrderIGSTTotal += $IGSTAmt;
					}
					$DiscPer = $value["DiscAmt"] / $value['BasicRate'];
					$BilledQty = $value["DOQty"] * $value["PackingQty"];
					$data_array_result = array(
						'TransDate'     => $DeliveryDate,
						'TransDate2'    => $DeliveryDate,
						'BatchNo'       => $value["BatchList"],
						'ExpDate'       => to_sql_date($value["ExpDate"]),
						'PurchRate'     => $BasicRate,
						'SaleRate'      => $SaleRate,
						'BasicRate'     => $BasicRate,
						'SuppliedIn'    => $value["SaleUnit"],
						'OrderQty'      => $BilledQty,
						'BilledQty'     => $BilledQty,
						'DiscPerc'      => $DiscPer,
						'DiscAmt'       => $DiscAmt,
						'cgst'          => $CGST,
						'cgstamt'       => $CGSTAmt,
						'sgst'          => $SGST,
						'sgstamt'       => $SGSTAmt,
						'igst'          => $IGST,
						'igstamt'       => $IGSTAmt,
						'CaseQty'       => $value["PackingQty"],
						'Cases'         => $value["DOQty"],
						'OrderAmt'      => $ItemTotal,
						'ChallanAmt'    => $ItemTotal,
						'NetOrderAmt'   => $NetAmt,
						'NetChallanAmt' => $NetAmt,
						'UserID2'       => $_SESSION['username'],
						'Lupdate'       => date('Y-m-d H:i:s'),
					);
					$this->db->where('PlantID', $PlantID);
					$this->db->where('FY', $FY);
					$this->db->where('BillID', $ChallanID);
					$this->db->where('BatchNo', $value["BatchList"]);
					$this->db->where('TType', "O");
					$this->db->where('TType2', "SALE");
					$this->db->where('ItemID', $value["ItemID"]);
					$this->db->update(db_prefix() . 'K1history', $data_array_result);
					$i++;
				}
				$ChallanUpdate = array(
					"ChallanAmt" => $OrderNetTotal
				);
				$this->db->where(db_prefix() . 'K1challanmaster.ChallanID', $new_Challan_Inv_Number);
				$this->db->update(db_prefix() . 'K1challanmaster', $ChallanUpdate);
				$roundAmt = $OrderNetTotal - round($OrderNetTotal);
				$SalesUpdate = array(
					"SaleAmt" => $OrderTotalAmt,
					"DiscAmt" => $OrderDiscTotal,
					"sgstamt" => $OrderSGSTTotal,
					"cgstamt" => $OrderCGSTTotal,
					"igstamt" => $OrderIGSTTotal,
					"BillAmt" => $OrderNetTotal,
					"RndAmt"  => round($OrderNetTotal),
					"ItCount" => $i - 1
				);
				$this->db->where(db_prefix() . 'K1salesmaster.SalesID', $TransID);
				$this->db->update(db_prefix() . 'K1salesmaster', $SalesUpdate);
			}
			$response = array(
				"status"    => true,
				"message"   => "DO updated successfully.",
				"ChallanID" => $ChallanID
			);
			return $response;
		}
	}
	public function GetDeliveryChallanDetails($ChallanID)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $this->session->userdata('finacial_year');
		$this->db->select('tblK1challanmaster.*,tblK1challanmaster.Transdate as ChallanDate,tblK1salesmaster.*,
            tblclients.company,tblclients.phonenumber,
			tblclients.state,tblxx_statelist.state_name, SUM(tblK1history.OrderQty) AS TotalOrderQty,
            tblK1ordermaster.Transdate AS OrderDate,tblK1ordermaster.PaymentTerm,
			(tblK1salesmaster.SaleAmt - tblK1salesmaster.DiscAmt) AS taxable_amt,tblCenterMaster.CenterName,
            tblCenterMaster.state as CenterStateShort,
			tblCenterMaster.state as CenterStateShort,tblGstRecord.gstin AS gst, 
			CenterState.state_name AS StateCenter, tblK1salesmaster.BillAmt,
            GROUP_CONCAT(DISTINCT CONCAT(tblclients.house, ", ", tblclients.street, ", ", tblclients.loc, ", ", tblclients.vtc, ", ", tblxx_statelist.state_name, " - ", tblxx_citylist.city_name)) AS VendorAddress');
		$this->db->from(db_prefix() . 'K1challanmaster');
		$this->db->join(db_prefix() . 'K1salesmaster', 'tblK1salesmaster.ChallanID = tblK1challanmaster.ChallanID AND tblK1salesmaster.PlantID = tblK1challanmaster.PlantID');
		$this->db->join(db_prefix() . 'K1ordermaster', 'tblK1ordermaster.OrderID = tblK1salesmaster.OrderID AND tblK1ordermaster.PlantID = tblK1salesmaster.PlantID', "LEFT");
		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1salesmaster.AccountID AND tblclients.PlantID = tblK1salesmaster.PlantID');
		$this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.dist', 'LEFT');
		$this->db->join(db_prefix() . 'CenterMaster', db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'K1salesmaster.CenterID', 'left');
		$this->db->join(db_prefix() . 'xx_statelist as CenterState', 'CenterState.short_name = tblCenterMaster.state', 'left');
		$this->db->join(db_prefix() . 'K1history', 'tblK1history.TransID = tblK1salesmaster.SalesID', 'left');
		$this->db->where(db_prefix() . 'K1challanmaster.ChallanID', $ChallanID);
		$this->db->where(db_prefix() . 'K1challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1challanmaster.FY', $year);
		$data = $this->db->get()->row();
		if (!empty($data)) {
			$data->ShippingList = $this->GetShippingListVendorwise($data->AccountID);
			// Closing Bal
			// Get Opening balance
			$this->db->select("tblaccountbalances.*");
			$this->db->from(db_prefix() . 'accountbalances');
			$this->db->where(db_prefix() . 'accountbalances.AccountID', $data->AccountID);
			$this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountbalances.FY', $year);
			$this->db->where(db_prefix() . 'accountbalances.PartyID', "KASPL");
			$OpnBalDetails = $this->db->get()->row();
			$OpnBal = 0;
			if ($OpnBalDetails) {
				$OpnBal = $OpnBalDetails->BAL1;
			}
			// Get Transaction Entry
			$this->db->select("SUM(tblaccountledger.Amount) AS TotalAmt,tblaccountledger.TType");
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where(db_prefix() . 'accountledger.AccountID', $data->AccountID);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PartyID', "KASPL");
			$this->db->group_by(db_prefix() . 'accountledger.TType');
			$LedgerDetails = $this->db->get()->result_array();
			$CreditAmt = 0;
			$DebitAmt = 0;
			foreach ($LedgerDetails as $key => $val) {
				if ($val["TType"] == "C") {
					$CreditAmt = $val["TotalAmt"];
				} else if ($val["TType"] == "D") {
					$DebitAmt = $val["TotalAmt"];
				}
			}
			$ClosingBal = $OpnBal - $CreditAmt - $data->BillAmt + $DebitAmt;
			$CRDR = "CR";
			if ($ClosingBal > 0) {
				$CRDR = "DR";
			}
			$data->clsBal = $ClosingBal;
			$ClosingBal = abs($ClosingBal) . " " . $CRDR;
			$data->ClosingBal = $ClosingBal;
		}
		return $data;
	}
	public function GetDeliveryChallanItemList($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			(tblK1history.DiscAmt/(tblK1history.BilledQty/tblK1history.CaseQty)) AS DiscAmt,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,
			tblbrands.BrandName AS BrandID,tblproduct.unit AS UOM,tblK1history.SuppliedIn AS SaleUnit,tbltaxes.taxrate AS GSTPer,
			(tblK1history.BilledQty/tblK1history.CaseQty) AS DOQty,tblK1history.BatchNo AS BatchList,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID AND RcvHistory.OrderID = tblK1history.OrderID AND RcvHistory.TType ="O" AND RcvHistory.TType2="Order" AND RcvHistory.BillID IS NOT NULL AND RcvHistory.OrderID != "' . $id . '") As SendQty,
			(Select SUM(RqstHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RqstHistory where RqstHistory.ItemID = tblK1history.ItemID  AND RqstHistory.OrderID = tblK1history.OrderID AND RqstHistory.TType ="O" AND RqstHistory.TType2="Order" AND RqstHistory.BillID IS NULL) As SOQty');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
		$this->db->where(db_prefix() . 'K1history.BillID', $id);
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$results = $this->db->get()->result_array();
		foreach ($results as &$row) {
			$row['ExpDate'] = _d(substr($row["ExpDate"], 0, 10));
			$BasicRate = $row['BasicRate'] * $row['CaseQty'];
			$row['BasicRate'] = $BasicRate;
			/*$filterdata = [
    				'ItemID'   =>$row['ItemID'],
    				'CenterID' =>$row['CenterID'],
    				'PartyID'  =>$row['AccountID'],
    				'OrderID'  =>$row['OrderID'],
    				'BatchNo'  =>$row['BatchList']
				];*/
			$filterdata = [
				'ItemID'   => $row['ItemID'],
				'CenterID' => $row['CenterID'],
				'BatchID'  => $row['BatchList'],
			];
			// echo "<pre>";print_r($filterdata);die;
			$BatchStock = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
			$row['StockQty'] = ($BatchStock[0]['Stock'] + $row['BilledQty']);
			/*
				$StockData = $this->GetItemWiseStockData($filterdata);
				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach ($StockData as $stockkey=>$stockval) {
					if ($stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
						$SaleQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
						$PurchQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
						$InQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
						$OutQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
						$InwardQty += $stockval["TotalQty"];
					}
				}
				$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				$row['StockQty'] = $BalQty;*/
		}
		return $results;
	}
	public function GetDeliveryInvoiceItemList($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblK1history.*,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			(tblK1history.DiscAmt/(tblK1history.BilledQty/tblK1history.CaseQty)) AS Discount,tblK1history.NetOrderAmt AS Netamt,tblK1history.ItemID AS id,
			tblbrands.BrandName AS Brand,tblproduct.unit AS Measuredin,tblK1history.SuppliedIn AS PurchUnit,tbltaxes.taxrate AS gst,
			(tblK1history.BilledQty/tblK1history.CaseQty) AS DOQty,tblK1history.BatchNo AS BatchList,
			(Select SUM(RcvHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RcvHistory where RcvHistory.ItemID = tblK1history.ItemID AND RcvHistory.OrderID = tblK1history.OrderID AND RcvHistory.TType ="O" AND RcvHistory.TType2="Order" AND RcvHistory.BillID IS NOT NULL AND RcvHistory.OrderID != "' . $id . '") As SendQty,
			(Select SUM(RqstHistory.OrderQty/tblproduct.PackingQty) from tblK1history as RqstHistory where RqstHistory.ItemID = tblK1history.ItemID  AND RqstHistory.OrderID = tblK1history.OrderID AND RqstHistory.TType ="O" AND RqstHistory.TType2="Order" AND RqstHistory.BillID IS NULL) As OrderQty');
		$this->db->from(db_prefix() . 'K1history');
		$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
		$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
		$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
		$this->db->where(db_prefix() . 'K1history.BillID', $id);
		$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'K1history.FY', $fy);
		$results = $this->db->get()->result_array();
		foreach ($results as &$row) {
			$row['ExpDate'] = _d(substr($row["ExpDate"], 0, 10));
			$row['BasicRate'] = $row['BasicRate'] * $row['CaseQty'];
			$row['PurchRate'] = $row['PurchRate'] * $row['CaseQty'];
			$row['SaleRate'] = $row['SaleRate'] * $row['CaseQty'];
			/*$filterdata = [
    				'ItemID'   =>$row['ItemID'],
    				'CenterID' =>$row['CenterID'],
    				'PartyID'  =>$row['AccountID'],
    				'OrderID'  =>$row['OrderID'],
    				'BatchNo'  =>$row['BatchList']
				];*/
			$filterdata = [
				'ItemID'   => $row['ItemID'],
				'CenterID' => $row['CenterID'],
				'BatchID'  => $row['BatchList'],
			];
			// echo "<pre>";print_r($filterdata);die;
			$BatchStock = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
			$row['StockQty'] = ($BatchStock[0]['Stock'] + $row['BilledQty']);
			/*
				$StockData = $this->GetItemWiseStockData($filterdata);
				$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
				foreach ($StockData as $stockkey=>$stockval) {
					if ($stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
						$SaleQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
						$PurchQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
						$InQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
						$OutQty += $stockval["TotalQty"];
					} else if ($stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
						$InwardQty += $stockval["TotalQty"];
					}
				}
				$BalQty = $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				$row['StockQty'] = $BalQty;*/
		}
		return $results;
	}
	public function load_data_for_delivery_order($data)
	{
		$from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$IsDirectSale = 'N';
		$sql1 = '(' . db_prefix() . 'K1challanmaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") 
			AND tblK1challanmaster.FY = "' . $fy . '" 
			AND tblK1challanmaster.PlantID = "' . $selected_company . '"   
			AND tblK1challanmaster.IsDirectSale = "' . $IsDirectSale . '" 
			ORDER BY tblK1challanmaster.Transdate DESC';
		$sql = 'SELECT tblK1challanmaster.*,tblK1challanmaster.Transdate as ChallanDate,tblK1salesmaster.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name,tblCenterMaster.CenterName 
			FROM ' . db_prefix() . 'K1challanmaster
			INNER JOIN tblK1salesmaster ON tblK1salesmaster.ChallanID = tblK1challanmaster.ChallanID AND tblK1salesmaster.PlantID = tblK1challanmaster.PlantID
			INNER JOIN tblclients ON tblclients.AccountID = tblK1salesmaster.AccountID AND tblclients.PlantID = tblK1salesmaster.PlantID
			LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = tblclients.state
			LEFT JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1salesmaster.CenterID
			WHERE ' . $sql1;
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	public function load_data_for_delivery_invoice($data)
	{
		$from_date = to_sql_date($data["from_date"]);
		$to_date = to_sql_date($data["to_date"]);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$sql1 = '(' . db_prefix() . 'K1challanmaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") 
			AND tblK1challanmaster.FY = "' . $fy . '" 
			AND tblK1challanmaster.PlantID = "' . $selected_company . '" AND tblK1challanmaster.OrderStatus = "F"  
			AND tblK1challanmaster.IsDirectSale = "N"  
			ORDER BY tblK1challanmaster.ChallanID DESC';
		$sql = 'SELECT tblK1challanmaster.*,tblK1challanmaster.Transdate as ChallanDate,tblK1salesmaster.*,tblclients.company,tblclients.phonenumber,tblclients.state,tblxx_statelist.state_name,tblCenterMaster.CenterName 
			FROM ' . db_prefix() . 'K1challanmaster
			INNER JOIN tblK1salesmaster ON tblK1salesmaster.ChallanID = tblK1challanmaster.ChallanID AND tblK1salesmaster.PlantID = tblK1challanmaster.PlantID
			INNER JOIN tblclients ON tblclients.AccountID = tblK1salesmaster.AccountID AND tblclients.PlantID = tblK1salesmaster.PlantID
			LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = tblclients.state
			LEFT JOIN tblCenterMaster ON tblCenterMaster.CenterID = tblK1salesmaster.CenterID
			WHERE ' . $sql1;
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	public function GetItemWiseStockData($filterdata)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		// Convert and format dates
		$from_date = '20' . $fy . '-04-01 00:00:00';
		$to_date = date('Y-m-d') . ' 23:59:59';
		$PartyID = $filterdata["PartyID"];
		$ItemID = $filterdata["ItemID"];
		$CenterID = $filterdata["CenterID"];
		$OrderID = $filterdata["OrderID"];
		$BatchNo = $filterdata["BatchNo"];
		// echo $PartyID;die;
		$this->db->select('DATE(tblK1history.TransDate) AS Date, 
			SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType, 
			tblK1history.TType2');
		// Apply filters if they exist
		if (!empty($ItemID)) {
			$this->db->where('tblK1history.ItemID', $ItemID);
		}
		if (!empty($CenterID)) {
			$this->db->where('tblK1history.CenterID', $CenterID);
		}
		if (!empty($OrderID)) {
			$this->db->where('tblK1history.OrderID !=', $OrderID);
		}
		if (!empty($BatchNo)) {
			$this->db->where('tblK1history.BatchNo', $BatchNo);
		}
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.PartyID', "KASPL");
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.TransDate >=', $from_date);
		$this->db->where('tblK1history.TransDate <=', $to_date);
		$this->db->group_by('tblK1history.TType, tblK1history.TType2');
		$StockItemList = $this->db->get(db_prefix() . 'K1history')->result_array();
		return $StockItemList;
	}
	public function GetItemBatchListWithStock($filterdata)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		// Batch List From Opening Stock
		$this->db->select('tblK1stockmaster.*');
		$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		if ($filterdata["BatchID"]) {
			$this->db->where('tblK1stockmaster.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->group_by('tblK1stockmaster.BatchNo');
		$this->db->order_by('tblK1stockmaster.ExpDate', 'ASC');
		$OpnQtyBatchList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		// Batch List From History
		$this->db->select('tblK1history.BatchNo,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType, 
			tblK1history.TType2,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.CaseQty');
		$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1history.CenterID', $filterdata["CenterID"]);
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		//$this->db->where('tblK1history.GodownID', 'WHO');
		$this->db->where('tblK1history.FY', $fy);
		if ($filterdata["BatchID"]) {
			$this->db->where('tblK1history.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->group_by('tblK1history.BatchNo, TType, TType2');
		$this->db->order_by('tblK1history.ExpDate', 'ASC');
		$BatchWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
		$response = array();
		$batch = array();
		foreach ($OpnQtyBatchList as $val) {
			array_push($batch, $val["BatchNo"]);
		}
		foreach ($BatchWiseTransaction as $val1) {
			if ($val1["BatchNo"] != "" && $val1["BatchNo"] != NULL) {
				array_push($batch, $val1["BatchNo"]);
			}
		}
		$UniqueBatchList = array_unique($batch);
		foreach ($UniqueBatchList as $key => $batchval) {
			$ExpDate = "";
			$PurchRate = 0;
			$OQty = 0;
			$PurchQty = 0;
			$InwardQty = 0;
			$PurchRtnQty = 0;
			$SaleQty = 0;
			$SaleRtnQty = 0;
			$PrdQty = 0;
			$IssueQty = 0;
			$AdjQty = 0;
			$InQty = 0;
			$OutQty = 0;
			$BalQty = 0;
			$isPurch = false;
			foreach ($BatchWiseTransaction as $stockkey => $stockval) {
				if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
					$SaleQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN") {
					$SaleRtnQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
					$PurchQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
					$CaseQty = $stockval["CaseQty"];
					$isPurch = true;
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN") {
					$PurchRtnQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
					$InQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
					$OutQty += $stockval["TotalQty"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
					$InwardQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "X") {
					$AdjQty += ($stockval["TotalQty"]);
				}
			}
			// Opening Qty
			foreach ($OpnQtyBatchList as $BatchOpnQty) {
				if ($BatchOpnQty["BatchNo"] == $batchval) {
					$OQty = $BatchOpnQty["OQty"];
					$ExpDate = _d(substr($BatchOpnQty["ExpDate"], 0, 10));
					if (!$isPurch) {
						$PurchRate = $BatchOpnQty["PurchRate"];
					}
				}
			}
			$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			// return $batchval."=".$OQty."=".$PurchQty."=".$SaleQty;
			if ($BalQty > 0) {
				$new11 = array("BatchNo" => $batchval, "Stock" => $BalQty, "ExpDate" => $ExpDate, "PurchRate" => $PurchRate, "CaseQty" => $CaseQty);
				array_push($response, $new11);
			}
		}
		return $response;
	}
	public function GetItemBatchListWithStockDSO($filterdata)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		// Batch List From Opening Stock
		$this->db->select('tblK1stockmaster.*');
		$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
		//$this->db->where('tblK1stockmaster.GodownID', 'RET');
		if ($filterdata["BatchID"]) {
			$this->db->where('tblK1stockmaster.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->group_by('tblK1stockmaster.BatchNo');
		$this->db->order_by('tblK1stockmaster.ExpDate', 'ASC');
		$OpnQtyBatchList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		// Batch List From History
		$this->db->select('tblK1history.BatchNo,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType, 
			tblK1history.TType2,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.CaseQty');
		$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
		$this->db->where('tblK1history.CenterID', $filterdata["CenterID"]);
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.FY', $fy);
		//$this->db->where('tblK1history.GodownID', 'RET');
		if ($filterdata["BatchID"]) {
			$this->db->where('tblK1history.BatchNo', $filterdata["BatchID"]);
		}
		$this->db->group_by('tblK1history.BatchNo, TType, TType2');
		$this->db->order_by('tblK1history.ExpDate', 'ASC');
		$BatchWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
		//print_r($OpnQtyBatchList);
		//print_r($BatchWiseTransaction);
		$response = array();
		$batch = array();
		foreach ($OpnQtyBatchList as $val) {
			array_push($batch, $val["BatchNo"]);
		}
		foreach ($BatchWiseTransaction as $val1) {
			if ($val1["BatchNo"] != "" && $val1["BatchNo"] != NULL) {
				array_push($batch, $val1["BatchNo"]);
			}
		}
		$UniqueBatchList = array_unique($batch);
		foreach ($UniqueBatchList as $key => $batchval) {
			$ExpDate = "";
			$PurchRate = 0;
			$OQty = 0;
			$PurchQty = 0;
			$InwardQty = 0;
			$PurchRtnQty = 0;
			$SaleQty = 0;
			$SaleRtnQty = 0;
			$PrdQty = 0;
			$IssueQty = 0;
			$AdjQty = 0;
			$InQty = 0;
			$OutQty = 0;
			$BalQty = 0;
			$isPurch = false;
			foreach ($BatchWiseTransaction as $stockkey => $stockval) {
				if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
					$SaleQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN") {
					$SaleRtnQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
					$PurchQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
					$isPurch = true;
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "P" && $stockval["TType2"] == "PURCHASE RETURN") {
					$PurchRtnQty += ($stockval["TotalQty"]);
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
					$InQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
					$OutQty += $stockval["TotalQty"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
					$InwardQty += ($stockval["TotalQty"]);
					$ExpDate = _d(substr($stockval["ExpDate"], 0, 10));
					$PurchRate = $stockval["PurchRate"];
				} else if ($stockval["BatchNo"] == $batchval && $stockval["TType"] == "X") {
					$AdjQty += ($stockval["TotalQty"]);
				}
			}
			// Opening Qty
			foreach ($OpnQtyBatchList as $BatchOpnQty) {
				if ($BatchOpnQty["BatchNo"] == $batchval) {
					$OQty = $BatchOpnQty["OQty"];
					$ExpDate = _d(substr($BatchOpnQty["ExpDate"], 0, 10));
					if (!$isPurch) {
						$PurchRate = $BatchOpnQty["PurchRate"];
					}
				}
			}
			$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			// return $batchval."=".$OQty."=".$PurchQty."=".$SaleQty;
			if ($BalQty > 0) {
				$new11 = array("BatchNo" => $batchval, "Stock" => $BalQty, "ExpDate" => $ExpDate, "PurchRate" => $PurchRate);
				array_push($response, $new11);
			}
		}
		//print_r($response);
		return $response;
	}
	public function GetItemWiseOpnQty($filterdata)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$PartyID = $filterdata["PartyID"];
		$ItemID = $filterdata["ItemID"];
		$CenterID = $filterdata["CenterID"];
		$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalOQty, tblK1stockmaster.ItemID');
		if (!empty($ItemID)) {
			$this->db->where('tblK1stockmaster.ItemID', $ItemID);
		}
		if (!empty($CenterID)) {
			$this->db->where('tblK1stockmaster.CenterID', $CenterID);
		}
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID');
		$OpnQty = $this->db->get(db_prefix() . 'K1stockmaster')->row();
		return $OpnQty;
	}
	public function GetCategoryWiseItems($CategoryType, $CenterID)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if ($CategoryType == "Grocery") {
			$Category = array('6', '8', '11');
		} elseif ($CategoryType == "Non Grocery") {
			$Category = array('1', '2', '3', '7','9','10');
		} else {
			$Category = array();
		}
		// Calculate Stock Available Items
		// Get Opening Qty	
		$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalOQty, tblK1stockmaster.ItemID');
		$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1stockmaster.ItemID');
		$this->db->where_in('tblproduct.Category', $Category);
		$this->db->where('tblK1stockmaster.CenterID', $CenterID);
		$this->db->where('tblK1stockmaster.FY', $fy);
		$this->db->where('tblK1stockmaster.PlantID', $selected_company);
		$this->db->group_by('tblK1stockmaster.ItemID');
		$OpnQtyItemWise = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
		// Get Transaction itemwise
		$this->db->select('tblK1history.ItemID, SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,tblK1history.TType2');
		$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID');
		$this->db->where_in('tblproduct.Category', $Category);
		$this->db->where('tblK1history.CenterID', $CenterID);
		$this->db->where('tblK1history.OrderID IS NOT NULL');
		$this->db->where('tblK1history.BillID IS NOT NULL');
		$this->db->where('tblK1history.TransID IS NOT NULL');
		$this->db->where('tblK1history.FY', $fy);
		$this->db->where('tblK1history.PlantID', $selected_company);
		$this->db->group_by('tblK1history.ItemID, tblK1history.TType, tblK1history.TType2');
		$this->db->order_by('tblK1history.ItemID', 'ASC');
		$ItemWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
		$this->db->select('tblproduct.ProductID as id, CONCAT(tblproduct.ProductID, " - ", tblproduct.ProductName) as label,tblproduct.ProductName ,ProductID');
		$this->db->from(db_prefix() . 'product');
		$this->db->where_in(db_prefix() . 'product.Category', $Category);
		$ProductList = $this->db->get()->result_array();
		$FinalItemList = array();
		foreach ($ProductList as $key => $val) {
			$OQty = 0;
			$PurchQty = 0;
			$InwardQty = 0;
			$PurchRtnQty = 0;
			$SaleQty = 0;
			$SaleRtnQty = 0;
			$PrdQty = 0;
			$IssueQty = 0;
			$AdjQty = 0;
			$InQty = 0;
			$OutQty = 0;
			$BalQty = 0;
			foreach ($ItemWiseTransaction as $stockkey => $stockval) {
				if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
					$SaleQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "SR" && $stockval["TType2"] == "FRESH RETURN") {
					$SaleRtnQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
					$PurchQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "PR" && $stockval["TType2"] == "PURCHASE RETURN") {
					$PurchRtnQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
					$InQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
					$OutQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
					$InwardQty += $stockval["TotalQty"];
				} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "X") {
					$AdjQty += $stockval["TotalQty"];
				}
			}
			// Opening Qty
			foreach ($OpnQtyItemWise as $BatchOpnQty) {
				if ($BatchOpnQty["ItemID"] == $val["ProductID"]) {
					$OQty = $BatchOpnQty["TotalOQty"];
				}
			}
			$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
			if ($BalQty > 0) {
				$new11 = array("id" => $val["ProductID"], "label" => $val["label"], "ProductName" => $val["ProductName"], "ProductID" => $val["ProductID"]);
				array_push($FinalItemList, $new11);
			}
		}
		return $FinalItemList;
	}
	public function get_next_farmer_code($name)
	{
		$this->db->select('tbloptions.*');
		$this->db->where('name', $name);
		$number_details = $this->db->get(db_prefix() . 'options')->row();
		return $number_details;
	}
	public function increment_next_farmer_number()
	{
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$this->db->where('name', 'next_farmer_code');
		$this->db->set('value', 'value+1', false);
		// $this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	public function GetLeanMarkEntries()
	{
		$this->db->select('tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty,tblK1history.CenterID,tblK1history.PartyID,tbltaxes.taxrate,
		    tblclients.company,tblclients.state,tblproduct.ProductName,tblproduct.rate,tblproduct.PackingQty,tblproduct.unit,tblCommisionMaster.Percent AS CommissionPercent');
		$this->db->join('tblclients', 'tblclients.AccountID = tblK1history.PartyID', "LEFT");
		$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID', "LEFT");
		$this->db->join('tbltaxes', 'tbltaxes.id = tblproduct.gst', "LEFT");
		$this->db->join('tblCommisionMaster', 'tblCommisionMaster.CenterID = tblK1history.CenterID AND tblCommisionMaster.AccountID = tblK1history.PartyID AND tblCommisionMaster.ItemID = tblK1history.ItemID', "LEFT");
		/*$today_start = date('Y-m-d 00:00:00');
            $today_end = date('Y-m-d 23:59:59');
            $this->db->where('tblK1history.TransDate >=', $today_start);
            $this->db->where('tblK1history.TransDate <=', $today_end);*/
		$this->db->where('tblK1history.TType2', 'LIENMARK');
		$this->db->where('tblK1history.TType', 'L');
		$this->db->group_by('tblK1history.ItemID, tblK1history.CenterID, tblK1history.PartyID');
		$Data = $this->db->get('tblK1history')->result_array();
		return $Data;
	}
}