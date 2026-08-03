<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class K1Stock_transfer_model extends App_Model
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
		public function get_data($tbl,$where)
		{
			$this->db->select('*');
			$this->db->from($tbl);
			$this->db->where($where);
			$query = $this->db->get();
			return $query->row_array();
		}
		public function edit_data($tbl,$where,$arr)
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
		public function get_items_code()
		{
			$selected_company = $this->session->userdata('root_company');
			return $this->db->query('SELECT ProductID as id, CONCAT(ProductID," - ",ProductName) as label,ProductName ,ProductID FROM '.db_prefix().'product WHERE PlantID = '.$selected_company)->result_array();
		}
		public function GetItemDetails($ItemID)
		{
			$this->db->select('tblproduct.*,tblproduct.ProductName,tblbrands.BrandName,tbltaxes.taxrate');
			$this->db->from(db_prefix() . 'product');
			$this->db->join(db_prefix() . 'brands', db_prefix() . 'brands.id = ' . db_prefix() . 'product.BrandId');
			$this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'product.gst');
			$this->db->where(db_prefix() . 'product.ProductID', $ItemID);
			$rs = $this->db->get()->row();
			return $rs;
		}
		public function increment_next_number($name)
		{
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('name', $name);
			$this->db->update(db_prefix() . 'options');
		}
		//=================== Add Kirti One Stock Transfer Order =============================
		public function AddKirtiOneStockTransfer($data)
		{
			if(isset($data['pur_order_detail']))
			{
				$pur_order_detail = json_decode($data['pur_order_detail']);
				unset($data['pur_order_detail']);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PackingWeight';
				$header[] = 'BatchNo';
				$header[] = 'Stock';
				$header[] = 'ExpDate';
				$header[] = 'Qty';
				$header[] = 'PurchRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				foreach ($pur_order_detail as $key => $value) {
					if($value[0] != ''){
						$ItemID = $value[0];
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$TransferFrom = $data['fromcentername'];
			$TransferTo = $data['tocentername'];
			$AccountId =  $data['AccountID'];
			$prefix = "TRF";
			$TransferNumbar = get_option('next_K1Stocktransfer_number_for_kirti');
			$new_Transfer_orderNumbar = $prefix.$FY."1".$TransferNumbar;
			$Transdate =  to_sql_date($data['trf_date'])." ".date('H:i:s');
			$PurchAmt =  $data['total_amt_in_mt'];
			$discountAMT = $data['total_disc_in_mt'];
			$cgstamt = $data['total_cgst_amt'];
			$sgstamt = $data['total_sgst_amt'];
			$igstamt = $data['total_igst_amt'];
			$roundoffamt = $data['total_roundoff_amt'];
			$invoiceamt = $data['netpayableamt'];
			$ItCount = count($es_detail);
			if($PurchAmt !=0)
			{
				$KirtiOneStockTransfer = array(
				'PlantID'=>$PlantID,
				'FY'=>$FY,
				'TransferID' =>$new_Transfer_orderNumbar,
				'AccountID'=>$AccountId,
				'TransferDate'=>$Transdate,
				'TransferFrom'=>$TransferFrom,
				'TransferTo'=>$TransferTo,
				'VehicleNo'=>$data['VehicleNo'],
				'DriverName'=>$data['DriverName'],
				'DriverMobile'=>$data['DriverMobile'],
				'EwayBillNo'=>$data['EwayBillNo'],
				'Purchamt'=>$PurchAmt,
				'Discamt'=>$discountAMT,
				'cgstamt'=>$cgstamt,
				'sgstamt'=>$sgstamt,
				'igstamt'=>$igstamt,
				'RoundOffAmt'=>$roundoffamt,
				'Invamt'=>$invoiceamt,
				'ItCount'=>$ItCount,
				'UserID'=>$_SESSION['username']
				);
				$this->db->insert(db_prefix() . 'K1stocktransfermaster',$KirtiOneStockTransfer);
				if($this->db->affected_rows() > 0)
				{
					$this->increment_next_number('next_K1Stocktransfer_number_for_kirti');
					$i =1;
					foreach($es_detail as $value)
					{
						$productId = $value['ItemID'];
						$brand = $value['Brand'];
						$unit = $value['MeasuredIn'];
						$packing_qty = $value['PackingQty'];
						$packing_weight = $value['PackingWeight'];
						$saleunit = $value['MeasuredIn'];
						$qty = $value['Qty'];
						$PurchRate = $value['PurchRate'];
						$discount = $value['Discount'];
						$gst = $value['GST'];
						$cgstamt = $value['CGSTAMT'];
						$sgstamt = $value['SGSTAMT'];
						$igstamt = $value['IGSTAMT'];
						$netAmount = $value['total_money'];
						$orderquantity = $qty;
						$amountval = ($PurchRate* $qty);
						$totalAmount = $amountval;
						$discountAmount = ($discount / 100) * $totalAmount;
						$finalOrderAmt = $totalAmount - $discountAmount;
						$CGST = 0;$SGST = 0;$IGST = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;
						if ($gst != "")
						{
							if($cgstamt > 0 && $sgstamt > 0)
							{
								$SGSTAmt = $cgstamt;
								$CGSTAmt = $sgstamt;
								$SGST = $gst / 2;
								$CGST = $gst / 2;
								$salerate = $PurchRate * (1 + $gst / 100);
							}else if($igstamt > 0)
							{
								$IGSTAmt = $igstamt;
								$IGST = $gst;
								$salerate = $PurchRate * (1 + $IGST / 100);
							}
						}
						$caseqty = 1;
						$stockoutentry_result = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'OrderID' =>$new_Transfer_orderNumbar,
						'BillID' =>$new_Transfer_orderNumbar,
						'TransID' =>$new_Transfer_orderNumbar,
						'TransDate' =>$Transdate,
						'TransDate2'=>date('Y-m-d H:i:s'),
						'TType'=>'T',
						'TType2'=> 'OUT',
						'AccountID'=> $AccountId,
						'ItemID'=>$productId,
						'CenterID'=>$TransferFrom,
						'PartyID'=>"KASPL",
						'BatchNo'=>$value['BatchNo'],
						'ExpDate'=>to_sql_date($value['ExpDate']),
						'PurchRate'=>$PurchRate,
						'SaleRate'=>$salerate,
						'BasicRate'=>$PurchRate,
						'SuppliedIn'=>$saleunit,
						'OrderQty'=>$orderquantity,
						'BilledQty'=>$orderquantity,
						'DiscPerc'=>$discount,
						'DiscAmt'=>$discountAmount,
						'cgst'=>$CGST,
						'cgstamt'=>$CGSTAmt,
						'sgst'=>$SGST,
						'sgstamt'=>$SGSTAmt,
						'igst'=>$IGST,
						'igstamt'=>$IGSTAmt,
						'CaseQty'=>$caseqty,
						'Cases'=>0.00,
						'OrderAmt'=>$totalAmount,
						'ChallanAmt'=>$totalAmount,
						'NetOrderAmt'=>$netAmount,
						'NetChallanAmt'=>$netAmount,
						'Ordinalno'=>$i,
                        'UserID'=>$_SESSION['username']
						);
						$this->db->insert(db_prefix() . 'K1history',$stockoutentry_result);
						$i++;
						$stockInentry_result = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'OrderID' =>$new_Transfer_orderNumbar,
						'BillID' =>$new_Transfer_orderNumbar,
						'TransID' =>$new_Transfer_orderNumbar,
						'TransDate' =>$Transdate,
						'TransDate2'=>date('Y-m-d H:i:s'),
						'TType'=>'T',
						'TType2'=> 'IN',
						'AccountID'=> $AccountId,
						'ItemID'=>$productId,
						'CenterID'=>$TransferTo,
						'PartyID'=>"KASPL",
						'BatchNo'=>$value['BatchNo'],
						'ExpDate'=>to_sql_date($value['ExpDate']),
						'PurchRate'=>$PurchRate,
						'SaleRate'=>$salerate,
						'BasicRate'=>$PurchRate,
						'SuppliedIn'=>$saleunit,
						'OrderQty'=>$orderquantity,
						'BilledQty'=>$orderquantity,
						'DiscPerc'=>$discount,
						'DiscAmt'=>$discountAmount,
						'cgst'=>$CGST,
						'cgstamt'=>$CGSTAmt,
						'sgst'=>$SGST,
						'sgstamt'=>$SGSTAmt,
						'igst'=>$IGST,
						'igstamt'=>$IGSTAmt,
						'CaseQty'=>$caseqty,
						'Cases'=>0.00,
						'OrderAmt'=>$totalAmount,
						'ChallanAmt'=>$totalAmount,
						'NetOrderAmt'=>$netAmount,
						'NetChallanAmt'=>$netAmount,
						'Ordinalno'=>$i,
                        'UserID'=>$_SESSION['username']
						);
						$this->db->insert(db_prefix() . 'K1history',$stockInentry_result);
						$i++;
					}
					return true;
				}
			}
		}
		public function UpdateKirtiOneStockTransfer($data,$id)
		{
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			if(isset($data['pur_order_detail']))
			{
				$pur_order_detail = json_decode($data['pur_order_detail']);
				unset($data['pur_order_detail']);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PackingWeight';
				$header[] = 'BatchNo';
				$header[] = 'Stock';
				$header[] = 'ExpDate';
				$header[] = 'Qty';
				$header[] = 'PurchRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				foreach ($pur_order_detail as $key => $value) {
					if($value[0] != ''){
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			$Transferid =  $id;
			$AccountId =  $data['AccountID'];
			$TransferFrom = $data['fromcentername'];
			$TransferTo = $data['tocentername'];
			$new_date =  to_sql_date($data['trf_date'])." ".date('H:i:s');
			$purchAmt = $data['total_amt_in_mt'];
			$Discamt =  $data['total_disc_in_mt'];
			$cgstamt =  $data['total_cgst_amt'];
			$sgstamt =  $data['total_sgst_amt'];
			$igstamt =  $data['total_igst_amt'];
			$RoundOffAmt =  $data['total_roundoff_amt'];
			$Invamt =  $data['netpayableamt'];
			$ItCount = count($es_detail);
			$data_array = array(
			'AccountID'=>$AccountId,
            'TransferDate' =>$new_date,
            'TransferFrom'=>$TransferFrom,
            'TransferTo' =>$TransferTo,
			'VehicleNo'=>$data['VehicleNo'],
			'DriverName'=>$data['DriverName'],
			'DriverMobile'=>$data['DriverMobile'],
			'EwayBillNo'=>$data['EwayBillNo'],
			'Purchamt'=>$purchAmt,
			'Discamt'=>$Discamt,
			'cgstamt'=>$cgstamt,
			'sgstamt'=>$sgstamt,
			'igstamt'=>$igstamt,
			'RoundOffAmt'=>$RoundOffAmt,
			'Invamt'=>$Invamt,
			'ItCount'=>$ItCount,
			'OrderStatus'=>'F',
            'UserID2'=>$_SESSION['username'],
			'Lupdate'=>date('Y-m-d H:i:s')
			);
			$this->db->where('PlantID', $PlantID);
			$this->db->LIKE('FY', $FY);
			$this->db->where('TransferID',$Transferid);
			$this->db->update(db_prefix() . 'K1stocktransfermaster',$data_array);
			if($this->db->affected_rows() > 0)
			{
				$old_pur_details = $this->get_stock_detail($Transferid);
				// Move record from tblK1history to tblK1history_audit
				foreach ($old_pur_details as $key => $value)
				{
					if($value["igst"] == null)
					{
						$value["igst"] = "";
						$value["igstamt"] = "";
					}
					else if($value["cgst"] == null)
					{
						$value["cgst"] = "";
						$value["cgstamt"] = "";
						$value["sgst"] = "";
						$value["sgstamt"] = "";
					}
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
                    //'TypeID'=>$value["TypeID"],
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
				// Delete Live history table record
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('OrderID', $Transferid);
				$this->db->delete(db_prefix().'K1history');
				// Add New history detail record
				$i =1;
				foreach($es_detail as $value)
				{
					$productId = $value['ItemID'];
					$brand = $value['Brand'];
					$unit = $value['MeasuredIn'];
					$packing_qty = $value['PackingQty'];
					$packing_weight = $value['PackingWeight'];
					$saleunit = $value['MeasuredIn'];
					$qty = $value['Qty'];
					$PurchRate = $value['PurchRate'];
					$discount = $value['Discount'];
					$gst = $value['GST'];
					$cgstamts = $value['CGSTAMT'];
					$sgstamts = $value['SGSTAMT'];
					$igstamts = $value['IGSTAMT'];
					$netAmount = $value['total_money'];
					$orderquantity = $qty;
					$amountval = ($PurchRate * $qty);
					$totalAmount = $amountval;
					$discountAmount = ($discount / 100) * $totalAmount;
					$finalOrderAmt = $totalAmount - $discountAmount;
					$CGST = 0;$SGST = 0;$IGST = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;
					if ($gst != "")
					{
						if($cgstamts > 0 && $sgstamts > 0)
						{
							$CGSTAmt = $cgstamts;
							$SGSTAmt = $sgstamts;
							$CGST = $gst / 2;
							$SGST = $gst / 2;
							$salerate = $PurchRate * (1 + $gst / 100);
						}else if($igstamts > 0)
						{
							$IGSTAmt = $igstamts;
							$IGST = $gst;
							$salerate = $PurchRate * (1 + $IGST / 100);
						}
					}
					$caseqty = 1;
					$stcokupdate_Outresult = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'OrderID' =>$Transferid,
					'BillID' =>$Transferid,
					'TransID' =>$Transferid,
					'TransDate' =>$new_date,
					'TransDate2'=>date('Y-m-d H:i:s'),
					'TType'=>'T',
					'TType2'=> 'OUT',
					'AccountID'=> $AccountId,
					'ItemID'=>$productId,
					'CenterID'=>$TransferFrom,
					'PartyID'=>"KASPL",
					'BatchNo'=>$value['BatchNo'],
					'ExpDate'=>$value['ExpDate'],
					'PurchRate'=>$PurchRate,
					'SaleRate'=>$salerate,
					'BasicRate'=>$PurchRate,
					'SuppliedIn'=>$saleunit,
					'OrderQty'=>$orderquantity,
					'BilledQty'=>$orderquantity,
					'DiscPerc'=>$discount,
					'DiscAmt'=>$discountAmount,
					'cgst'=>$CGST,
					'cgstamt'=>$CGSTAmt,
					'sgst'=>$SGST,
					'sgstamt'=>$SGSTAmt,
					'igst'=>$IGST,
					'igstamt'=>$IGSTAmt,
					'CaseQty'=>$caseqty,
					'Cases'=>0.00,
					'OrderAmt'=>$totalAmount,
					'ChallanAmt'=>$totalAmount,
					'NetOrderAmt'=>$netAmount,
					'NetChallanAmt'=>$netAmount,
					'Ordinalno'=>$i,
					'rowid'=>"",
					'UserID'=>$_SESSION['username'],
					'cnfid'=>"",
					'UserID2'=>$_SESSION['username'],
					'Lupdate'=>date('Y-m-d H:i:s')
					);
					$this->db->insert(db_prefix() . 'K1history',$stcokupdate_Outresult);
					$i++;
					$stcokupdate_Inresult = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'OrderID' =>$Transferid,
					'BillID' =>$Transferid,
					'TransID' =>$Transferid,
					'TransDate' =>$new_date,
					'TransDate2'=>date('Y-m-d H:i:s'),
					'TType'=>'T',
					'TType2'=> 'IN',
					'AccountID'=> $AccountId,
					'ItemID'=>$productId,
					'CenterID'=>$TransferTo,
					'PartyID'=>"KASPL",
					'BatchNo'=>$value['BatchNo'],
					'ExpDate'=>$value['ExpDate'],
					'PurchRate'=>$PurchRate,
					'SaleRate'=>$salerate,
					'BasicRate'=>$PurchRate,
					'SuppliedIn'=>$saleunit,
					'OrderQty'=>$orderquantity,
					'BilledQty'=>$orderquantity,
					'DiscPerc'=>$discount,
					'DiscAmt'=>$discountAmount,
					'cgst'=>$CGST,
					'cgstamt'=>$CGSTAmt,
					'sgst'=>$SGST,
					'sgstamt'=>$SGSTAmt,
					'igst'=>$IGST,
					'igstamt'=>$IGSTAmt,
					'CaseQty'=>$caseqty,
					'Cases'=>0.00,
					'OrderAmt'=>$totalAmount,
					'ChallanAmt'=>$totalAmount,
					'NetOrderAmt'=>$netAmount,
					'NetChallanAmt'=>$netAmount,
					'Ordinalno'=>$i,
					'rowid'=>"",
					'UserID'=>$_SESSION['username'],
					'cnfid'=>"",
					'UserID2'=>$_SESSION['username'],
					'Lupdate'=>date('Y-m-d H:i:s')
					);
					$this->db->insert(db_prefix() . 'K1history',$stcokupdate_Inresult);
					$i++;
				}
				return true;
			}
		}
		public function get_stock_detail($id){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select();
			$this->db->from(db_prefix() . 'K1history');
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $year);
			$this->db->where(db_prefix() . 'K1history.OrderID', $id);
			return $this->db->get()->result_array();
		}
		public function load_data_for_stockkirtione($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql1 = '('.db_prefix().'K1stocktransfermaster.TransferDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")
			AND tblK1stocktransfermaster.FY = "'.$fy.'"
			AND tblK1stocktransfermaster.PlantID = "'.$selected_company.'"
			ORDER BY TransferID DESC';
			$sql = 'SELECT '.db_prefix().'K1stocktransfermaster.*,tblclients.company,
            (SELECT GROUP_CONCAT(CenterName SEPARATOR ",")
			FROM '.db_prefix().'CenterMaster
			WHERE '.db_prefix().'CenterMaster.CenterID = '.db_prefix().'K1stocktransfermaster.TransferFrom) as FromCenter,
            (SELECT GROUP_CONCAT(CenterName SEPARATOR ",")
			FROM '.db_prefix().'CenterMaster
			WHERE '.db_prefix().'CenterMaster.CenterID = '.db_prefix().'K1stocktransfermaster.TransferTo) as ToCenter
            FROM '.db_prefix().'K1stocktransfermaster
			JOIN ' . db_prefix() . 'clients
            ON ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1stocktransfermaster.AccountID
            WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function load_data_for_stockadjkirtione($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql1 = '('.db_prefix().'K1stockadjustmentmaster.AdjustmentDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")
			AND tblK1stockadjustmentmaster.FY = "'.$fy.'"
			AND tblK1stockadjustmentmaster.PlantID = "'.$selected_company.'"
			ORDER BY AdjustmentID DESC';
			$sql = 'SELECT '.db_prefix().'K1stockadjustmentmaster.*,tblclients.company,
            (SELECT GROUP_CONCAT(CenterName SEPARATOR ",")
			FROM '.db_prefix().'CenterMaster
			WHERE '.db_prefix().'CenterMaster.CenterID = '.db_prefix().'K1stockadjustmentmaster.CenterID) as CenterName
            FROM '.db_prefix().'K1stockadjustmentmaster
			JOIN ' . db_prefix() . 'clients
            ON ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'K1stockadjustmentmaster.AccountID
            WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetStockDetails($TrfNumber)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select('tblK1stocktransfermaster.*,tblCenterMaster.CenterName AS Fromcenter,tblCenterMaster2.CenterName AS ToCenter,SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1stocktransfermaster.Purchamt - tblK1stocktransfermaster.Discamt) AS taxable_amt');
			$this->db->from(db_prefix() . 'K1stocktransfermaster');
			$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = K1stocktransfermaster.TransferFrom');
			$this->db->join(db_prefix() . 'CenterMaster AS tblCenterMaster2', 'tblCenterMaster2.CenterID = tblK1stocktransfermaster.TransferTo');
			$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1stocktransfermaster.TransferID', 'left');
			$this->db->where(db_prefix() . 'K1stocktransfermaster.TransferID', $TrfNumber);
			$this->db->where(db_prefix() . 'K1stocktransfermaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1stocktransfermaster.FY', $year);
			return $this->db->get()->row();
		}
		public function GetStockAdjDetails($AdjNumber)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select('tblK1stockadjustmentmaster.*,tblCenterMaster.CenterName AS Fromcenter,SUM(tblK1history.OrderQty) AS TotalOrderQty,(tblK1stockadjustmentmaster.Purchamt - tblK1stockadjustmentmaster.Discamt) AS taxable_amt');
			$this->db->from(db_prefix() . 'K1stockadjustmentmaster');
			$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = K1stockadjustmentmaster.CenterID');
			$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1stockadjustmentmaster.AdjustmentID', 'left');
			$this->db->where(db_prefix() . 'K1stockadjustmentmaster.AdjustmentID', $AdjNumber);
			$this->db->where(db_prefix() . 'K1stockadjustmentmaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1stockadjustmentmaster.FY', $year);
			return $this->db->get()->row();
		}
		public function GetStockItemList($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('tblK1history.ItemID AS id,tblK1history.BilledQty AS OrderQty,tblK1history.BatchNo,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.BasicRate,
			tblK1history.SuppliedIn,tblK1history.DiscAmt,tblK1history.cgst,tblK1history.sgst,tblK1history.igst,tblK1history.cgstamt,tblK1history.sgstamt,tblK1history.igstamt,
			tblK1history.CaseQty,
			tblproduct.ProductName, tblproduct.hsn_code, tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscPerc AS Discount,tblK1history.NetOrderAmt AS Netamt,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,tblK1stocktransfermaster.TransferFrom');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'K1stocktransfermaster', 'tblK1stocktransfermaster.TransferID = tblK1history.OrderID AND tblK1stocktransfermaster.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
			$this->db->where(db_prefix() . 'K1history.OrderID', $id);
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $fy);
			$this->db->where(db_prefix() . 'K1history.TType2', "IN");
			$results = $this->db->get()->result_array();
			foreach ($results as &$row) {
				$filterdata = [
				'ItemID'=>$row['ItemID'],
				'CenterID'=>$row['TransferFrom'],
				'BatchID'=>$row['BatchNo'],
				];
				// echo "<pre>";print_r($results);die;
				$ItemWiseBatchList = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
				//$row['BatchNo'] = $ItemWiseBatchList;
				$row['StockQty'] = number_format($ItemWiseBatchList[0]["Stock"] + $row['OrderQty'], 2, '.', '');
			}
			return $results;
		}
		public function GetStockAdjItemList($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('tblK1history.*,tblproduct.ProductName,tblproduct.unit AS Measuredin,tblproduct.PackingQty,
			tblproduct.PackingWeight AS Packingwgt,tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscPerc AS Discount,tblK1history.NetOrderAmt AS Netamt,
			tblK1history.ItemID AS id,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
			$this->db->where(db_prefix() . 'K1history.OrderID', $id);
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $fy);
			$results = $this->db->get()->result_array();
			foreach ($results as &$row) {
				$filterdata = [
				'ItemID'=>$row['ItemID'],
				'CenterID'=>$row['CenterID'],
				'BatchID'=>$row['BatchNo'],
				];
				// echo "<pre>";print_r($results);die;
				$ItemWiseBatchList = $this->KirtiOneOrderModel->GetItemBatchListWithStock($filterdata);
				//$row['BAtch'] = $ItemWiseBatchList;
				$row['StockQty'] = number_format($ItemWiseBatchList[0]["Stock"] + $row['BilledQty'], 2, '.', '');
				$row['ExpDate'] = _d(substr($row['ExpDate'],0,10));
			}
			return $results;
		}
		public function GetFromCenterList($LogInUser = "")
		{
			$this->db->select('tblK1stocktransfermaster.TransferID,tblCenterMaster.CenterName,tblCenterMaster.CenterID');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1stocktransfermaster.TransferFrom');
			$this->db->where('tblK1stocktransfermaster.TransferID IS NOT NULL');
			if($LogInUser){
				$this->db->where('tblK1stocktransfermaster.AccountID',$LogInUser);
			}
			$this->db->group_by('tblCenterMaster.CenterID');
			return $this->db->get('tblK1stocktransfermaster')->result_array();
		}
		public function GetToCenterList($LogInUser = "")
		{
			$this->db->select('tblK1stocktransfermaster.TransferID,tblCenterMaster.CenterName,tblCenterMaster.CenterID');
			$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblK1stocktransfermaster.TransferTo');
			$this->db->where('tblK1stocktransfermaster.TransferID IS NOT NULL');
			if($LogInUser){
				$this->db->where('tblK1stocktransfermaster.AccountID',$LogInUser);
			}
			$this->db->group_by('tblCenterMaster.CenterID');
			return $this->db->get('tblK1stocktransfermaster')->result_array();
		}
		public function GetPurchOrderItemList($LogInUser = "")
		{
			$this->db->select('tblK1history.ItemID,tblproduct.ProductName,tblproduct.ProductID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			if($LogInUser){
				$this->db->where('tblK1history.AccountID',$LogInUser);
			}
			$this->db->group_by('tblK1history.ItemID');
			return $this->db->get('tblK1history')->result_array();
		}
		public function GetAccountList()
		{
			$this->db->select('tblK1stocktransfermaster.TransferID,tblclients.company,tblclients.AccountID');
			$this->db->join('tblclients', 'tblclients.AccountID = tblK1stocktransfermaster.AccountID');
			$this->db->where('tblK1stocktransfermaster.TransferID IS NOT NULL');
			$this->db->group_by('tblclients.AccountID');
			return $this->db->get('tblK1stocktransfermaster')->result_array();
		}
		public function getItemOrderDetailsDB($data)
		{
			$from_date = to_sql_date($data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			if($data["Report_type"] == "1")
			{
				$this->db->select('tblK1stocktransfermaster.*,Fromcenter.CenterName AS fromcentername,tocenter.CenterName AS tocentername,tblclients.company,tblK1stocktransfermaster.OrderStatus');
				$this->db->where('tblK1stocktransfermaster.TransferDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if(!empty($data['order_status'])){
					$this->db->where('tblK1stocktransfermaster.OrderStatus',$data['order_status']);
				}
				if(!empty($data['AccountID'])){
					$this->db->where('tblK1stocktransfermaster.AccountID',$data['AccountID']);
				}
				if(!empty($data['FromCenterID'])){
					$this->db->where('tblK1stocktransfermaster.TransferFrom',$data['FromCenterID']);
				}
				if(!empty($data['ToCenterID'])){
					$this->db->where('tblK1stocktransfermaster.TransferTo',$data['ToCenterID']);
				}
				$this->db->join('tblCenterMaster AS Fromcenter', 'Fromcenter.CenterID = tblK1stocktransfermaster.TransferFrom');
				$this->db->join('tblCenterMaster AS tocenter', 'tocenter.CenterID = tblK1stocktransfermaster.TransferTo');
				$this->db->join('tblclients', 'tblclients.AccountID = tblK1stocktransfermaster.AccountID');
				$this->db->where('tblK1stocktransfermaster.TransferID IS NOT NULL');
				$this->db->order_by('tblK1stocktransfermaster.TransferID','DESC');
				return $this->db->get('tblK1stocktransfermaster')->result_array();
			}
			else
			{
				$this->db->select('tblK1history.*,Fromcenter.CenterName AS fromcentername,tocenter.CenterName AS tocentername,tblproduct.ProductName,tblclients.company,tblK1stocktransfermaster.TransferID,tblK1stocktransfermaster.OrderStatus');
				$this->db->where('tblK1history.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if(!empty($data['order_status'])){
					$this->db->where('tblK1stocktransfermaster.OrderStatus',$data['order_status']);
				}
				if(!empty($data['AccountID'])){
					$this->db->where('tblK1stocktransfermaster.AccountID',$data['AccountID']);
				}
				if(!empty($data['FromCenterID'])){
					$this->db->where('tblK1stocktransfermaster.TransferFrom',$data['FromCenterID']);
				}
				if(!empty($data['ToCenterID'])){
					$this->db->where('tblK1stocktransfermaster.TransferTo',$data['ToCenterID']);
				}
				if(!empty($data['ItemID'])){
					$this->db->where('tblK1history.ItemID',$data['ItemID']);
				}
				$this->db->where('tblK1history.TType2',"IN");
				$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
				$this->db->join('tblK1stocktransfermaster', 'tblK1stocktransfermaster.TransferID = tblK1history.OrderID');
				$this->db->join('tblclients', 'tblclients.AccountID = tblK1stocktransfermaster.AccountID');
				$this->db->join('tblCenterMaster AS Fromcenter', 'Fromcenter.CenterID = tblK1stocktransfermaster.TransferFrom');
				$this->db->join('tblCenterMaster AS tocenter', 'tocenter.CenterID = tblK1stocktransfermaster.TransferTo');
				$this->db->where('tblK1stocktransfermaster.TransferID IS NOT NULL');
				$this->db->order_by('tblK1stocktransfermaster.TransferID','DESC');
				return $this->db->get('tblK1history')->result_array();
			}
		}
		public function get_company_detail()
		{
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT ' . db_prefix() . 'rootcompany.*
			FROM ' . db_prefix() . 'rootcompany WHERE id = "' . $selected_company . '"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		public function GetTrfdetailsNumberwise($trfNumber)
		{
			$this->db->select('tblK1stocktransfermaster.*,tblCenterMaster.CenterName AS Fromcenter,tblCenterMaster2.CenterName AS ToCenter,tblclients.company,SUM(tblK1history.OrderQty) AS TotalOrderQty,tblK1history.ItemID');
			$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = tblK1stocktransfermaster.TransferFrom');
			$this->db->join(db_prefix() . 'CenterMaster AS tblCenterMaster2', 'tblCenterMaster2.CenterID = tblK1stocktransfermaster.TransferTo');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1stocktransfermaster.AccountID');
			$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1stocktransfermaster.TransferID');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
			$this->db->where('TransferID', $trfNumber);
			return $this->db->get('tblK1stocktransfermaster')->row();
		}
		public function GetTrfdetailsItemwise($trfNumber)
		{
			$this->db->select('tblK1history.*,tblK1stocktransfermaster.TransferDate,tblK1stocktransfermaster.OrderStatus,tblCenterMaster.CenterName AS Fromcenter,tblCenterMaster2.CenterName AS ToCenter,tblclients.company,tblproduct.ProductName');
			$this->db->join(db_prefix() . 'K1stocktransfermaster', 'tblK1stocktransfermaster.TransferID = tblK1history.OrderID');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID', 'left');
			$this->db->join(db_prefix() . 'CenterMaster','tblCenterMaster.CenterID = tblK1stocktransfermaster.TransferFrom');
			$this->db->join(db_prefix() . 'CenterMaster AS tblCenterMaster2', 'tblCenterMaster2.CenterID = tblK1stocktransfermaster.TransferTo');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblK1stocktransfermaster.AccountID');
			$this->db->where('OrderID', $trfNumber);
			$this->db->where('TType2', "IN");
			return $this->db->get('tblK1history')->result_array();
		}
		public function GetCenterWiseItems($CenterID,$TrfNumber = "")
		{
		    $fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			// Calculate Stock Available Items
		    // Get Opening Qty
			$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalOQty, tblK1stockmaster.ItemID');
			$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1stockmaster.ItemID');
			$this->db->where('tblK1stockmaster.CenterID', $CenterID);
			$this->db->where('tblK1stockmaster.FY', $fy);
			$this->db->where('tblK1stockmaster.PlantID', $selected_company);
			$this->db->group_by('tblK1stockmaster.ItemID');
			$OpnQtyItemWise = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
			// Get Transaction itemwise
			$this->db->select('tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,tblK1history.TType2');
			$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID');
			$this->db->where('tblK1history.CenterID', $CenterID);
			if($TrfNumber){
			    $this->db->where_not_in('tblK1history.OrderID', $TrfNumber);
			}
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->group_by('tblK1history.ItemID,tblK1history.TType,tblK1history.TType2');
			$this->db->order_by('tblK1history.ItemID','ASC');
			$ItemWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();
			$this->db->select('tblproduct.ProductID as id, CONCAT(tblproduct.ProductID," - ",tblproduct.ProductName) as label,tblproduct.ProductName ,ProductID');
			$this->db->from(db_prefix() . 'product');
			$ProductList = $this->db->get()->result_array();
			$FinalItemList = array();
			foreach($ProductList as $key=>$val){
			    $OQty = 0;$PurchQty = 0;$InwardQty = 0;$PurchRtnQty = 0;$SaleQty = 0;$SaleRtnQty = 0;$PrdQty = 0;$IssueQty = 0;$AdjQty = 0;$InQty = 0; $OutQty = 0;$BalQty = 0;
			    foreach($ItemWiseTransaction as $stockkey=>$stockval){
					if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE"){
						$SaleQty += $stockval["TotalQty"];
						}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase"){
						$PurchQty += $stockval["TotalQty"];
						}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN"){
						$InQty += $stockval["TotalQty"];
						}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT"){
						$OutQty += $stockval["TotalQty"];
						}else if($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD"){
						$InwardQty += $stockval["TotalQty"];
					}
				}
				// Opening Qty
				foreach($OpnQtyItemWise as $BatchOpnQty){
				    if($BatchOpnQty["ItemID"] == $val["ProductID"]){
				        $OQty = $BatchOpnQty["TotalOQty"];
					}
				}
				$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				if($BalQty > 0){
				    $new11 = array("id"=>$val["ProductID"],"label"=>$val["label"],"ProductName"=>$val["ProductName"],"ProductID"=>$val["ProductID"]);
			        array_push($FinalItemList,$new11);
				}
			}
			return $FinalItemList;
		}
		//=================== Add Kirti One Stock Transfer Order =============================
		public function AddKirtiOneStockAdjustment($data)
		{
			if(isset($data['pur_order_detail']))
			{
				$pur_order_detail = json_decode($data['pur_order_detail']);
				unset($data['pur_order_detail']);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PackingWeight';
				$header[] = 'BatchNo';
				$header[] = 'Stock';
				$header[] = 'ExpDate';
				$header[] = 'Qty';
				$header[] = 'PurchRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				foreach ($pur_order_detail as $key => $value) {
					if($value[0] != ''){
						$ItemID = $value[0];
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$CenterID = $data['CenterID'];
			$AccountId =  $data['AccountID'];
			$prefix = "ADJ";
			$TransferNumbar = get_option('next_K1Stockadjustment_number_for_kirti');
			$new_Transfer_orderNumbar = $prefix.$FY."1".$TransferNumbar;
			$Transdate =  to_sql_date($data['trf_date'])." ".date('H:i:s');
			$PurchAmt =  $data['total_amt_in_mt'];
			$discountAMT = $data['total_disc_in_mt'];
			$cgstamt = $data['total_cgst_amt'];
			$sgstamt = $data['total_sgst_amt'];
			$igstamt = $data['total_igst_amt'];
			$roundoffamt = $data['total_roundoff_amt'];
			$invoiceamt = $data['netpayableamt'];
			$Type = $data['AdjType'];
			$ItCount = count($es_detail);
			if($PurchAmt !=0)
			{
				$KirtiOneStockAdjustment = array(
				'PlantID'=>$PlantID,
				'FY'=>$FY,
				'AdjustmentID' =>$new_Transfer_orderNumbar,
				'AccountID'=>$AccountId,
				'AdjustmentDate'=>$Transdate,
				'CenterID'=>$CenterID,
				'Type'=>$Type,
				'Purchamt'=>$PurchAmt,
				'Discamt'=>$discountAMT,
				'cgstamt'=>$cgstamt,
				'sgstamt'=>$sgstamt,
				'igstamt'=>$igstamt,
				'RoundOffAmt'=>$roundoffamt,
				'OrderStatus'=>'F',
				'Invamt'=>$invoiceamt,
				'ItCount'=>$ItCount,
				'UserID'=>$_SESSION['username']
				);
				$this->db->insert(db_prefix() . 'K1stockadjustmentmaster',$KirtiOneStockAdjustment);
				if($this->db->affected_rows() > 0)
				{
					$this->increment_next_number('next_K1Stockadjustment_number_for_kirti');
					$i =1;
					foreach($es_detail as $value)
					{
						$productId = $value['ItemID'];
						$brand = $value['Brand'];
						$unit = $value['MeasuredIn'];
						$packing_qty = $value['PackingQty'];
						$packing_weight = $value['PackingWeight'];
						$saleunit = $value['MeasuredIn'];
						$qty = $value['Qty'];
						$PurchRate = $value['PurchRate'];
						$discount = $value['Discount'];
						$gst = $value['GST'];
						$cgstamt = $value['CGSTAMT'];
						$sgstamt = $value['SGSTAMT'];
						$igstamt = $value['IGSTAMT'];
						$netAmount = $value['total_money'];
						$orderquantity = $qty;
						$amountval = ($PurchRate* $qty);
						$totalAmount = $amountval;
						$discountAmount = ($discount / 100) * $totalAmount;
						$finalOrderAmt = $totalAmount - $discountAmount;
						$CGST = 0;$SGST = 0;$IGST = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;
						if ($gst != "")
						{
							if($cgstamt > 0 && $sgstamt > 0)
							{
								$CGSTAmt = $cgstamt;
								$SGSTAmt = $sgstamt;
								$CGST = $gst/2;
								$SGST = $gst/2;
								$salerate = $PurchRate * (1 + $gst / 100);
							}
							else if($igstamt > 0)
							{
								$IGSTAmt = $igstamt;
								$IGST = $gst;
								$salerate = $PurchRate * (1 + $IGST / 100);
							}
						}
						$caseqty = 1;
						$stockadjentry_result = array(
						'PlantID'=>$PlantID,
						'FY'=>$FY,
						'OrderID' =>$new_Transfer_orderNumbar,
						'BillID' =>$new_Transfer_orderNumbar,
						'TransID' =>$new_Transfer_orderNumbar,
						'TransDate' =>$Transdate,
						'TransDate2'=>date('Y-m-d H:i:s'),
						'TType'=>'X',
						'TType2'=> $Type,
						'AccountID'=> $AccountId,
						'ItemID'=>$productId,
						'CenterID'=>$CenterID,
						'PartyID'=>"KASPL",
						'BatchNo'=>$value['BatchNo'],
						'ExpDate'=>to_sql_date($value['ExpDate']),
						'PurchRate'=>$PurchRate,
						'SaleRate'=>$salerate,
						'BasicRate'=>$PurchRate,
						'SuppliedIn'=>$saleunit,
						'OrderQty'=>$orderquantity,
						'BilledQty'=>$orderquantity,
						'DiscPerc'=>$discount,
						'DiscAmt'=>$discountAmount,
						'cgst'=>$CGST,
						'cgstamt'=>$CGSTAmt,
						'sgst'=>$SGST,
						'sgstamt'=>$SGSTAmt,
						'igst'=>$IGST,
						'igstamt'=>$IGSTAmt,
						'CaseQty'=>$caseqty,
						'Cases'=>0.00,
						'OrderAmt'=>$totalAmount,
						'ChallanAmt'=>$totalAmount,
						'NetOrderAmt'=>$netAmount,
						'NetChallanAmt'=>$netAmount,
						'Ordinalno'=>$i,
                        'UserID'=>$_SESSION['username']
						);
						$this->db->insert(db_prefix() . 'K1history',$stockadjentry_result);
						$i++;
					}
					return true;
				}
			}
		}
		public function UpdateKirtiOneStockAdjustment($data,$id)
		{
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			if(isset($data['pur_order_detail']))
			{
				$pur_order_detail = json_decode($data['pur_order_detail']);
				unset($data['pur_order_detail']);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'ItemID';
				$header[] = 'Brand';
				$header[] = 'MeasuredIn';
				$header[] = 'PackingQty';
				$header[] = 'PackingWeight';
				$header[] = 'BatchNo';
				$header[] = 'Stock';
				$header[] = 'ExpDate';
				$header[] = 'Qty';
				$header[] = 'PurchRate';
				$header[] = 'Discount';
				$header[] = 'GST';
				$header[] = 'CGSTAMT';
				$header[] = 'SGSTAMT';
				$header[] = 'IGSTAMT';
				$header[] = 'total_money';
				foreach ($pur_order_detail as $key => $value) {
					if($value[0] != ''){
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			$AdjustmentID =  $id;
			$AccountId =  $data['AccountID'];
			$CenterID = $data['CenterID'];
			$new_date =  to_sql_date($data['trf_date'])." ".date('H:i:s');
			$purchAmt = $data['total_amt_in_mt'];
			$Discamt =  $data['total_disc_in_mt'];
			$cgstamt =  $data['total_cgst_amt'];
			$sgstamt =  $data['total_sgst_amt'];
			$igstamt =  $data['total_igst_amt'];
			$RoundOffAmt =  $data['total_roundoff_amt'];
			$Invamt =  $data['netpayableamt'];
			$Type = $data['AdjType'];
			$ItCount = count($es_detail);
			$data_array = array(
			'AccountID'=>$AccountId,
            'AdjustmentDate' =>$new_date,
			'Type'=>$Type,
            'CenterID'=>$CenterID,
			'Purchamt'=>$purchAmt,
			'Discamt'=>$Discamt,
			'cgstamt'=>$cgstamt,
			'sgstamt'=>$sgstamt,
			'igstamt'=>$igstamt,
			'RoundOffAmt'=>$RoundOffAmt,
			'Invamt'=>$Invamt,
			'ItCount'=>$ItCount,
			'OrderStatus'=>'F',
            'UserID2'=>$_SESSION['username'],
			'Lupdate'=>date('Y-m-d H:i:s')
			);
			$this->db->where('PlantID', $PlantID);
			$this->db->LIKE('FY', $FY);
			$this->db->where('AdjustmentID',$AdjustmentID);
			$this->db->update(db_prefix() . 'K1stockadjustmentmaster',$data_array);
			if($this->db->affected_rows() > 0)
			{
				$old_pur_details = $this->get_stock_detail($AdjustmentID);
				// Move record from tblK1history to tblK1history_audit
				foreach ($old_pur_details as $key => $value)
				{
					if($value["igst"] == null)
					{
						$value["igst"] = "";
						$value["igstamt"] = "";
					}
					else if($value["cgst"] == null)
					{
						$value["cgst"] = "";
						$value["cgstamt"] = "";
						$value["sgst"] = "";
						$value["sgstamt"] = "";
					}
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
                    //'TypeID'=>$value["TypeID"],
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
				// Delete Live history table record
				$this->db->where('PlantID', $PlantID);
				$this->db->where('FY', $FY);
				$this->db->where('OrderID', $AdjustmentID);
				$this->db->delete(db_prefix().'K1history');
				// Add New history detail record
				$i =1;
				foreach($es_detail as $value)
				{
					$productId = $value['ItemID'];
					$brand = $value['Brand'];
					$unit = $value['MeasuredIn'];
					$packing_qty = $value['PackingQty'];
					$packing_weight = $value['PackingWeight'];
					$saleunit = $value['MeasuredIn'];
					$qty = $value['Qty'];
					$PurchRate = $value['PurchRate'];
					$discount = $value['Discount'];
					$gst = $value['GST'];
					$cgstamts = $value['CGSTAMT'];
					$sgstamts = $value['SGSTAMT'];
					$igstamts = $value['IGSTAMT'];
					$netAmount = $value['total_money'];
					$orderquantity = $qty;
					$amountval = ($PurchRate * $qty);
					$totalAmount = $amountval;
					$discountAmount = ($discount / 100) * $totalAmount;
					$finalOrderAmt = $totalAmount - $discountAmount;
					$CGST = 0;$SGST = 0;$IGST = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;
					if ($gst != "")
					{
						if($cgstamts > 0 && $sgstamts > 0)
						{
							$CGSTAmt = $cgstamts;
							$SGSTAmt = $sgstamts;
							$CGST = $gst/2;
							$SGST = $gst/2;
							$salerate = $PurchRate * (1 + $gst / 100);
						}
						else if($igstamts > 0)
						{
							$IGSTAmt = $igstamts;
							$IGST = $gst;
							$salerate = $PurchRate * (1 + $IGST / 100);
						}
					}
					$caseqty = 1;
					$stcokupdate_Outresult = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'OrderID' =>$AdjustmentID,
					'BillID' =>$AdjustmentID,
					'TransID' =>$AdjustmentID,
					'TransDate' =>$new_date,
					'TransDate2'=>date('Y-m-d H:i:s'),
					'TType'=>'X',
					'TType2'=> $Type,
					'AccountID'=> $AccountId,
					'ItemID'=>$productId,
					'CenterID'=>$CenterID,
					'PartyID'=>"KASPL",
					'BatchNo'=>$value['BatchNo'],
					'ExpDate'=>to_sql_date($value['ExpDate']),
					'PurchRate'=>$PurchRate,
					'SaleRate'=>$salerate,
					'BasicRate'=>$PurchRate,
					'SuppliedIn'=>$saleunit,
					'OrderQty'=>$orderquantity,
					'BilledQty'=>$orderquantity,
					'DiscPerc'=>$discount,
					'DiscAmt'=>$discountAmount,
					'cgst'=>$CGST,
					'cgstamt'=>$CGSTAmt,
					'sgst'=>$SGST,
					'sgstamt'=>$SGSTAmt,
					'igst'=>$IGST,
					'igstamt'=>$IGSTAmt,
					'CaseQty'=>$caseqty,
					'Cases'=>0.00,
					'OrderAmt'=>$totalAmount,
					'ChallanAmt'=>$totalAmount,
					'NetOrderAmt'=>$netAmount,
					'NetChallanAmt'=>$netAmount,
					'Ordinalno'=>$i,
					'rowid'=>"",
					'UserID'=>$_SESSION['username'],
					'cnfid'=>"",
					'UserID2'=>$_SESSION['username'],
					'Lupdate'=>date('Y-m-d H:i:s')
					);
					$this->db->insert(db_prefix() . 'K1history',$stcokupdate_Outresult);
					$i++;
				}
				return true;
			}
		}
		public function GetStockAdjustmentCenterList($LogInUser = "")
		{
			$this->db->select('sam.CenterID, cm.CenterName');
			$this->db->from('K1stockadjustmentmaster sam');
			$this->db->join('tblCenterMaster cm', 'cm.CenterID = sam.CenterID');
			if($LogInUser){
				$this->db->where('sam.AccountID',$LogInUser);
			}
			$this->db->group_by('sam.CenterID');
			return $this->db->get()->result_array();
		}
		public function GetStockAdjustmentItemList($LogInUser = "")
		{
			$this->db->select('tblK1history.ItemID,tblproduct.ProductName,tblproduct.ProductID');
			$this->db->join('tblproduct', 'tblproduct.ProductID = tblK1history.ItemID');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.TType', 'X');
			if($LogInUser){
				$this->db->where('tblK1history.AccountID',$LogInUser);
			}
			$this->db->group_by('tblK1history.ItemID');
			return $this->db->get('tblK1history')->result_array();
		}
		public function GetStockAdjustmentPartyList()
		{
			$this->db->select('c.AccountID, c.company');
			$this->db->from('K1stockadjustmentmaster sam');
			$this->db->join('tblclients c', 'c.AccountID  = sam.AccountID');
			$this->db->group_by('sam.AccountID');
			return $this->db->get()->result_array();
		}
		public function getAdjustmentReportFilter($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date   = to_sql_date($data["to_date"]);
			$fy        = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($data['ReportType'] == '1'){ // Report type bill
				$this->db->select('pr.*, cm.CenterName, c.company AS AccountName');
				$this->db->from(db_prefix() . 'K1stockadjustmentmaster pr');
				$this->db->join(db_prefix() . 'CenterMaster cm', 'cm.CenterID = pr.CenterID', 'left');
				$this->db->join(db_prefix() . 'clients c', 'c.AccountID = pr.AccountID', 'left');
				$this->db->where("pr.AdjustmentDate >=", $from_date.' 00:00:00');
				$this->db->where("pr.AdjustmentDate <=", $to_date.' 23:59:59');
				$this->db->where("pr.FY", $fy);
				$this->db->where("pr.AdjustmentID IS NOT NULL", null, false);
				$this->db->where("pr.PlantID", $selected_company);
				if(!empty($data['CenterID'])) $this->db->where("pr.CenterID", $data['CenterID']);
				if(!empty($data['AccountID'])) $this->db->where("pr.AccountID", $data['AccountID']);
				if(!empty($data['AdjustmentType'])) $this->db->where("pr.Type", $data['AdjustmentType']);
				$this->db->order_by("pr.AdjustmentID", "DESC");
			}else{ // Report type item
				$this->db->select('pr.*, cm.CenterName, p.ProductName, p.hsn_code, b.BrandName, c.company AS AccountName, s.Type AS AdjustmentType');
				$this->db->from(db_prefix() . 'K1history pr');
				$this->db->join(db_prefix() . 'CenterMaster cm', 'cm.CenterID = pr.CenterID', 'left');
				$this->db->join(db_prefix() . 'clients c', 'c.AccountID = pr.AccountID', 'left');
				$this->db->join(db_prefix() . 'product p', 'p.ProductID = pr.ItemID', 'left');
				$this->db->join(db_prefix() . 'brands b', 'b.id = p.BrandId', 'left');
				$this->db->join(db_prefix() . 'K1stockadjustmentmaster s', 's.AdjustmentID = pr.OrderID', 'left');
				$this->db->where("pr.TransDate >=", $from_date.' 00:00:00');
				$this->db->where("pr.TransDate <=", $to_date.' 23:59:59');
				$this->db->where("pr.FY", $fy);
				$this->db->where("pr.TType", "X");
				$this->db->where("pr.PlantID", $selected_company);
				if(!empty($data['CenterID'])) $this->db->where("pr.CenterID", $data['CenterID']);
				if(!empty($data['AccountID'])) $this->db->where("pr.AccountID", $data['AccountID']);
				if(!empty($data['ItemID'])) $this->db->where("pr.ItemID", $data['ItemID']);
				if(!empty($data['AdjustmentType'])) $this->db->where("s.Type", $data['AdjustmentType']);
				$this->db->order_by("pr.TransDate", "DESC");
			}
			return $this->db->get()->result_array();
		}

		public function GetCenterWiseGodownItems($CenterID, $TrfNumber = '')
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');

			$this->db->select('SUM(tblK1stockmaster.OQty) AS TotalOQty, tblK1stockmaster.ItemID');
			$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1stockmaster.ItemID');
			$this->db->where('tblK1stockmaster.CenterID', $CenterID);
			$this->db->where('tblK1stockmaster.FY', $fy);
			$this->db->where('tblK1stockmaster.PlantID', $selected_company);
			$this->db->group_by('tblK1stockmaster.ItemID');
			$OpnQtyItemWise = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();

			$this->db->select('tblK1history.ItemID,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,tblK1history.TType2');
			$this->db->join(db_prefix() . 'product', db_prefix() . 'product.ProductID = ' . db_prefix() . 'K1history.ItemID');
			$this->db->where('tblK1history.CenterID', $CenterID);
			$this->db->where('tblK1history.GodownID', 'WHO');
			if ($TrfNumber) {
				$this->db->where_not_in('tblK1history.OrderID', $TrfNumber);
			}
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.FY', $fy);
			$this->db->where('tblK1history.PlantID', $selected_company);
			$this->db->group_by('tblK1history.ItemID,tblK1history.TType,tblK1history.TType2');
			$this->db->order_by('tblK1history.ItemID', 'ASC');
			$ItemWiseTransaction = $this->db->get(db_prefix() . 'K1history')->result_array();

			$this->db->select('tblproduct.ProductID as id, CONCAT(tblproduct.ProductID," - ",tblproduct.ProductName) as label,tblproduct.ProductName ,ProductID');
			$this->db->from(db_prefix() . 'product');
			$ProductList = $this->db->get()->result_array();

			$FinalItemList = array();
			foreach ($ProductList as $val) {
				$OQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0; $SaleQty = 0; $SaleRtnQty = 0;
				$PrdQty = 0; $IssueQty = 0; $AdjQty = 0; $InQty = 0; $OutQty = 0; $BalQty = 0;
				foreach ($ItemWiseTransaction as $stockval) {
					if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "O" && $stockval["TType2"] == "SALE") {
						$SaleQty += $stockval["TotalQty"];
					} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "P" && $stockval["TType2"] == "Purchase") {
						$PurchQty += $stockval["TotalQty"];
					} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "IN") {
						$InQty += $stockval["TotalQty"];
					} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "T" && $stockval["TType2"] == "OUT") {
						$OutQty += $stockval["TotalQty"];
					} else if ($stockval["ItemID"] == $val["ProductID"] && $stockval["TType"] == "I" && $stockval["TType2"] == "INWARD") {
						$InwardQty += $stockval["TotalQty"];
					}
				}
				foreach ($OpnQtyItemWise as $BatchOpnQty) {
					if ($BatchOpnQty["ItemID"] == $val["ProductID"]) {
						$OQty = $BatchOpnQty["TotalOQty"];
					}
				}
				$BalQty = $OQty + $InwardQty + $PurchQty - $PurchRtnQty - $SaleQty + $SaleRtnQty + $PrdQty - $IssueQty - $AdjQty + $InQty - $OutQty;
				if ($BalQty > 0) {
					$FinalItemList[] = array(
						"id" => $val["ProductID"],
						"label" => $val["label"],
						"ProductName" => $val["ProductName"],
						"ProductID" => $val["ProductID"]
					);
				}
			}
			return $FinalItemList;
		}

		public function GetGodownItemBatchListWithStock($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');

			$this->db->select('tblK1stockmaster.*');
			$this->db->where('tblK1stockmaster.ItemID', $filterdata["ItemID"]);
			$this->db->where('tblK1stockmaster.CenterID', $filterdata["CenterID"]);
			if (!empty($filterdata["BatchID"])) {
				$this->db->where('tblK1stockmaster.BatchNo', $filterdata["BatchID"]);
			}
			$this->db->where('tblK1stockmaster.FY', $fy);
			$this->db->group_by('tblK1stockmaster.BatchNo');
			$this->db->order_by('tblK1stockmaster.ExpDate', 'ASC');
			$OpnQtyBatchList = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();

			$this->db->select('tblK1history.BatchNo,SUM(tblK1history.BilledQty) AS TotalQty, tblK1history.TType,
			tblK1history.TType2,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.CaseQty');
			$this->db->where('tblK1history.ItemID', $filterdata["ItemID"]);
			$this->db->where('tblK1history.CenterID', $filterdata["CenterID"]);
			$this->db->where('tblK1history.GodownID', 'WHO');
			$this->db->where('tblK1history.OrderID IS NOT NULL');
			$this->db->where('tblK1history.BillID IS NOT NULL');
			$this->db->where('tblK1history.TransID IS NOT NULL');
			$this->db->where('tblK1history.FY', $fy);
			if (!empty($filterdata['TransferID'])) {
				$this->db->where_not_in('tblK1history.OrderID', $filterdata['TransferID']);
			}
			if (!empty($filterdata["BatchID"])) {
				$this->db->where('tblK1history.BatchNo', $filterdata["BatchID"]);
			}
			$this->db->group_by('tblK1history.BatchNo,TType,TType2');
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
			foreach ($UniqueBatchList as $batchval) {
				$ExpDate = "";
				$PurchRate = 0;
				$OQty = 0; $PurchQty = 0; $InwardQty = 0; $PurchRtnQty = 0; $SaleQty = 0; $SaleRtnQty = 0;
				$PrdQty = 0; $IssueQty = 0; $AdjQty = 0; $InQty = 0; $OutQty = 0; $BalQty = 0;
				$isPurch = false;
				foreach ($BatchWiseTransaction as $stockval) {
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
				if ($BalQty > 0) {
					$response[] = array("BatchNo" => $batchval, "Stock" => $BalQty, "ExpDate" => $ExpDate, "PurchRate" => $PurchRate);
				}
			}
			return $response;
		}

		/**
		 * Next display number for Godown Stock Transfer (separate series from Stock Transfer).
		 * Series digit is 2 (Stock Transfer uses 1). Sequence starts at 1.
		 * Example first no: GTR26 + 0000021  (2 + sequence 1, padded)
		 */
		public function getNextGodownTransferDisplayNumber()
		{
			$next_num = get_option('next_K1GodownStocktransfer_number_for_kirti');
			if ($next_num === '' || $next_num === false || !is_numeric($next_num) || (int) $next_num < 1) {
				$next_num = 1;
			} else {
				$next_num = (int) $next_num;
			}
			$number = $next_num;
			return $number;
			// return str_pad($number, get_option('number_padding_prefixes'), STR_PAD_LEFT);
		}

		public function get_godown_stock_detail($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select();
			$this->db->from(db_prefix() . 'K1history');
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $year);
			$this->db->where(db_prefix() . 'K1history.OrderID', $id);
			$this->db->where(db_prefix() . 'K1history.TType', 'T');
			return $this->db->get()->result_array();
		}

		/** Move live K1history rows to K1history_audit (same fields as stock transfer update). */
		public function archive_k1history_rows_to_audit($rows)
		{
			foreach ($rows as $value) {
				if ($value['igst'] === null) {
					$value['igst'] = '';
					$value['igstamt'] = '';
				} elseif ($value['cgst'] === null) {
					$value['cgst'] = '';
					$value['cgstamt'] = '';
					$value['sgst'] = '';
					$value['sgstamt'] = '';
				}
				$old_data = [
					'PlantID' => $value['PlantID'],
					'FY' => $value['FY'],
					'OrderID' => $value['OrderID'],
					'BillID' => $value['BillID'],
					'TransID' => $value['TransID'],
					'TransDate' => $value['TransDate'],
					'TransDate2' => $value['TransDate2'],
					'TType' => $value['TType'],
					'TType2' => $value['TType2'],
					'AccountID' => $value['AccountID'],
					'ItemID' => $value['ItemID'],
					'CenterID' => $value['CenterID'],
					'GodownID' => isset($value['GodownID']) ? $value['GodownID'] : '',
					'PartyID' => $value['PartyID'],
					'PurchRate' => $value['PurchRate'],
					'SaleRate' => $value['SaleRate'],
					'BasicRate' => $value['BasicRate'],
					'SuppliedIn' => $value['SuppliedIn'],
					'OrderQty' => $value['OrderQty'],
					'eOrderQty' => isset($value['eOrderQty']) ? $value['eOrderQty'] : '',
					'BilledQty' => $value['BilledQty'],
					'DiscPerc' => $value['DiscPerc'],
					'DiscAmt' => $value['DiscAmt'],
					'cgst' => $value['cgst'],
					'cgstamt' => $value['cgstamt'],
					'sgst' => $value['sgst'],
					'sgstamt' => $value['sgstamt'],
					'igst' => $value['igst'],
					'igstamt' => $value['igstamt'],
					'CaseQty' => $value['CaseQty'],
					'Cases' => $value['Cases'],
					'OrderAmt' => $value['OrderAmt'],
					'ChallanAmt' => $value['ChallanAmt'],
					'NetOrderAmt' => $value['NetOrderAmt'],
					'NetChallanAmt' => $value['NetChallanAmt'],
					'Ordinalno' => $value['Ordinalno'],
					'UserID' => $value['UserID'],
					'Lupdate' => date('Y-m-d H:i:s'),
					'UserID2' => $_SESSION['username'],
				];
				if (!$this->db->insert(db_prefix() . 'K1history_audit', $old_data)) {
					return false;
				}
			}
			return true;
		}

		public function GetGodownStockDetails($TrfNumber)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select('tblK1GodownStockTransferMaster.*, tblCenterMaster.CenterName, SUM(tblK1history.OrderQty) AS TotalOrderQty, (tblK1GodownStockTransferMaster.Purchamt - tblK1GodownStockTransferMaster.Discamt) AS taxable_amt');
			$this->db->from(db_prefix() . 'K1GodownStockTransferMaster');
			$this->db->join(db_prefix() . 'CenterMaster', 'tblCenterMaster.CenterID = tblK1GodownStockTransferMaster.CenterID', 'left');
			$this->db->join(db_prefix() . 'K1history', 'tblK1history.OrderID = tblK1GodownStockTransferMaster.TransferID AND tblK1history.PlantID = tblK1GodownStockTransferMaster.PlantID AND tblK1history.TType2 = "OUT"', 'left');
			$this->db->where(db_prefix() . 'K1GodownStockTransferMaster.TransferID', $TrfNumber);
			$this->db->where(db_prefix() . 'K1GodownStockTransferMaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1GodownStockTransferMaster.FY', $year);
			$this->db->group_by(db_prefix() . 'K1GodownStockTransferMaster.id');
			return $this->db->get()->row();
		}

		public function GetGodownStockItemList($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->select('tblK1history.ItemID AS id,tblK1history.BilledQty AS OrderQty,tblK1history.BatchNo,tblK1history.ExpDate,tblK1history.PurchRate,tblK1history.BasicRate,
			tblK1history.SuppliedIn,tblK1history.DiscAmt,tblK1history.cgst,tblK1history.sgst,tblK1history.igst,tblK1history.cgstamt,tblK1history.sgstamt,tblK1history.igstamt,
			tblK1history.CaseQty,tblproduct.ProductName,tblproduct.hsn_code,tblproduct.unit AS Measuredin,tblproduct.PackingQty,tblproduct.PackingWeight AS Packingwgt,
			tblK1history.SuppliedIn AS PurchUnit,tblK1history.DiscPerc AS Discount,tblK1history.NetOrderAmt AS Netamt,tbltaxes.taxrate AS gst,tblbrands.BrandName AS Brand,tblK1GodownStockTransferMaster.CenterID');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->join(db_prefix() . 'K1GodownStockTransferMaster', 'tblK1GodownStockTransferMaster.TransferID = tblK1history.OrderID AND tblK1GodownStockTransferMaster.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'product', 'tblproduct.ProductID = tblK1history.ItemID AND tblproduct.PlantID = tblK1history.PlantID');
			$this->db->join(db_prefix() . 'taxes', 'tbltaxes.id = tblproduct.gst');
			$this->db->join(db_prefix() . 'brands', 'tblbrands.id = tblproduct.BrandId');
			$this->db->where(db_prefix() . 'K1history.OrderID', $id);
			$this->db->where(db_prefix() . 'K1history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'K1history.FY', $fy);
			$this->db->where(db_prefix() . 'K1history.TType', 'T');
			$this->db->where(db_prefix() . 'K1history.TType2', 'OUT');
			$this->db->where(db_prefix() . 'K1history.GodownID', 'WHO');
			$results = $this->db->get()->result_array();
			foreach ($results as &$row) {
				$filterdata = [
					'ItemID' => $row['id'],
					'CenterID' => $row['CenterID'],
					'BatchID' => $row['BatchNo'],
					'TransferID' => $id,
				];
				$ItemWiseBatchList = $this->GetGodownItemBatchListWithStock($filterdata);
				$row['StockQty'] = !empty($ItemWiseBatchList[0]['Stock'])
					? number_format($ItemWiseBatchList[0]['Stock'] - $row['OrderQty'], 2, '.', '')
					: number_format($row['OrderQty'], 2, '.', '');
				$row['ExpDate'] = _d(substr($row['ExpDate'], 0, 10));
			}
			return $results;
		}

		// ----- Godown Stock Transfer helpers -----

		/** Convert Handsontable grid JSON to line items */
		public function parse_godown_transfer_grid_lines($pur_order_detail)
		{
			$columns = ['ItemID', 'Brand', 'MeasuredIn', 'PackingQty', 'PackingWeight', 'BatchNo', 'Stock', 'ExpDate', 'Qty', 'PurchRate', 'Discount', 'GST', 'CGSTAMT', 'SGSTAMT', 'IGSTAMT', 'total_money'];
			$grid_rows = json_decode($pur_order_detail);
			$lines = [];
			if (!is_array($grid_rows)) {
				return $lines;
			}
			foreach ($grid_rows as $row) {
				if (!is_array($row)) {
					continue;
				}
				$row = array_values($row);
				if (empty($row[0])) {
					continue;
				}
				$row = array_pad($row, count($columns), '');
				$lines[] = array_combine($columns, array_slice($row, 0, count($columns)));
			}
			return $lines;
		}

		/**
		 * Build tblK1history OUT rows only (WHO wholesale) — saved on create/update while Draft.
		 */
		public function build_godown_transfer_out_history_rows($lines, $orderId, $centerId, $transdate, $plantId, $fy)
		{
			$history = [];
			$ordinal = 1;
			foreach ($lines as $line) {
				$qty = $line['Qty'];
				$purchRate = $line['PurchRate'];
				$discount = $line['Discount'];
				$gst = $line['GST'];
				$cgstamt = $line['CGSTAMT'];
				$sgstamt = $line['SGSTAMT'];
				$igstamt = $line['IGSTAMT'];
				$netAmount = $line['total_money'];
				$totalAmount = $purchRate * $qty;
				$discountAmount = ($discount / 100) * $totalAmount;
				$CGST = 0;
				$SGST = 0;
				$IGST = 0;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				$saleRate = $purchRate;
				if ($gst != '') {
					if ($cgstamt > 0 && $sgstamt > 0) {
						$SGSTAmt = $cgstamt;
						$CGSTAmt = $sgstamt;
						$SGST = $gst / 2;
						$CGST = $gst / 2;
						$saleRate = $purchRate * (1 + $gst / 100);
					} elseif ($igstamt > 0) {
						$IGSTAmt = $igstamt;
						$IGST = $gst;
						$saleRate = $purchRate * (1 + $IGST / 100);
					}
				}
				$history[] = [
					'PlantID' => $plantId,
					'FY' => $fy,
					'OrderID' => $orderId,
					'BillID' => $orderId,
					'TransID' => $orderId,
					'TransDate' => $transdate,
					'TransDate2' => date('Y-m-d H:i:s'),
					'TType' => 'T',
					'TType2' => 'OUT',
					'GodownID' => 'WHO',
					'Ordinalno' => $ordinal++,
					'AccountID' => '',
					'ItemID' => $line['ItemID'],
					'CenterID' => $centerId,
					'PartyID' => 'KASPL',
					'BatchNo' => $line['BatchNo'],
					'ExpDate' => !empty($line['ExpDate']) ? to_sql_date($line['ExpDate']) : null,
					'PurchRate' => $purchRate,
					'SaleRate' => $saleRate,
					'BasicRate' => $purchRate,
					'SuppliedIn' => $line['MeasuredIn'],
					'OrderQty' => $qty,
					'BilledQty' => $qty,
					'DiscPerc' => $discount,
					'DiscAmt' => $discountAmount,
					'cgst' => $CGST,
					'cgstamt' => $CGSTAmt,
					'sgst' => $SGST,
					'sgstamt' => $SGSTAmt,
					'igst' => $IGST,
					'igstamt' => $IGSTAmt,
					'CaseQty' => 1,
					'Cases' => 0.00,
					'OrderAmt' => $totalAmount,
					'ChallanAmt' => $totalAmount,
					'NetOrderAmt' => $netAmount,
					'NetChallanAmt' => $netAmount,
					'UserID' => $_SESSION['username'],
				];
			}
			return $history;
		}

		/** Get draft OUT lines for a godown transfer (WHO). */
		public function get_godown_transfer_out_lines($transferId)
		{
			$plantId = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->from(db_prefix() . 'K1history');
			$this->db->where('PlantID', $plantId);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $transferId);
			$this->db->where('TType', 'T');
			$this->db->where('TType2', 'OUT');
			$this->db->where('GodownID', 'WHO');
			$this->db->order_by('Ordinalno', 'ASC');
			return $this->db->get()->result_array();
		}

		/** Build RET/IN rows from existing WHO/OUT rows — saved on approve only. */
		public function build_godown_transfer_in_history_rows_from_out($outRows)
		{
			$history = [];
			$ordinal = 1;
			foreach ($outRows as $out) {
				if ((float) $out['OrderQty'] <= 0 && (float) $out['BilledQty'] <= 0) {
					continue;
				}
				$entry = $out;
				unset($entry['id']);
				$entry['TType2'] = 'IN';
				$entry['GodownID'] = 'RET';
				$entry['Ordinalno'] = $ordinal++;
				$entry['TransDate2'] = date('Y-m-d H:i:s');
				$entry['UserID'] = $_SESSION['username'];
				$history[] = $entry;
			}
			return $history;
		}

		public function AddGodownStockTransfer($data)
		{
			if (empty($data['centername']) || empty($data['pur_order_detail'])) {
				return false;
			}
			$es_detail = $this->parse_godown_transfer_grid_lines($data['pur_order_detail']);
			if (empty($es_detail)) {
				return false;
			}

			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$CenterID = $data['centername'];
			$new_Transfer_orderNumbar = 'GTR' . $FY . $this->getNextGodownTransferDisplayNumber();
			$Transdate = to_sql_date($data['trf_date']) . ' ' . date('H:i:s');

			$master = [
				'PlantID' => $PlantID,
				'FY' => $FY,
				'TransferID' => $new_Transfer_orderNumbar,
				'TransferDate' => $Transdate,
				'CenterID' => $CenterID,
				'Purchamt' => !empty($data['total_amt_in_mt']) ? $data['total_amt_in_mt'] : 0,
				'Discamt' => !empty($data['total_disc_in_mt']) ? $data['total_disc_in_mt'] : 0,
				'cgstamt' => !empty($data['total_cgst_amt']) ? $data['total_cgst_amt'] : 0,
				'sgstamt' => !empty($data['total_sgst_amt']) ? $data['total_sgst_amt'] : 0,
				'igstamt' => !empty($data['total_igst_amt']) ? $data['total_igst_amt'] : 0,
				'RoundOffAmt' => !empty($data['total_roundoff_amt']) ? $data['total_roundoff_amt'] : 0,
				'Invamt' => !empty($data['netpayableamt']) ? $data['netpayableamt'] : 0,
				'ItCount' => count($es_detail),
				'UserID' => $_SESSION['username'],
				'OrderStatus' => 'D',
			];

			$history = $this->build_godown_transfer_out_history_rows($es_detail, $new_Transfer_orderNumbar, $CenterID, $Transdate, $PlantID, $FY);

			$this->db->trans_start();
			$this->db->insert(db_prefix() . 'K1GodownStockTransferMaster', $master);
			if (!empty($history)) {
				$this->db->insert_batch(db_prefix() . 'K1history', $history);
			}
			$this->increment_next_number('next_K1GodownStocktransfer_number_for_kirti');
			$this->db->trans_complete();

			return $this->db->trans_status() === false ? false : $new_Transfer_orderNumbar;
		}

		public function UpdateGodownStockTransfer($data, $id)
		{
			if (empty($id) || empty($data['centername']) || empty($data['pur_order_detail'])) {
				return false;
			}
			$existing = $this->GetGodownStockDetails($id);
			if (empty($existing) || $existing->OrderStatus !== 'D') {
				return false;
			}
			$es_detail = $this->parse_godown_transfer_grid_lines($data['pur_order_detail']);
			if (empty($es_detail)) {
				return false;
			}

			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$CenterID = $data['centername'];
			$Transdate = to_sql_date($data['trf_date']) . ' ' . date('H:i:s');

			$master = [
				'TransferDate' => $Transdate,
				'CenterID' => $CenterID,
				'Purchamt' => !empty($data['total_amt_in_mt']) ? $data['total_amt_in_mt'] : 0,
				'Discamt' => !empty($data['total_disc_in_mt']) ? $data['total_disc_in_mt'] : 0,
				'cgstamt' => !empty($data['total_cgst_amt']) ? $data['total_cgst_amt'] : 0,
				'sgstamt' => !empty($data['total_sgst_amt']) ? $data['total_sgst_amt'] : 0,
				'igstamt' => !empty($data['total_igst_amt']) ? $data['total_igst_amt'] : 0,
				'RoundOffAmt' => !empty($data['total_roundoff_amt']) ? $data['total_roundoff_amt'] : 0,
				'Invamt' => !empty($data['netpayableamt']) ? $data['netpayableamt'] : 0,
				'ItCount' => count($es_detail),
				'OrderStatus' => 'D',
				'UserID2' => $_SESSION['username'],
				'Lupdate' => date('Y-m-d H:i:s'),
			];

			$history = $this->build_godown_transfer_out_history_rows($es_detail, $id, $CenterID, $Transdate, $PlantID, $FY);

			$this->db->trans_start();
			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $FY);
			$this->db->where('TransferID', $id);
			$this->db->update(db_prefix() . 'K1GodownStockTransferMaster', $master);

			$old_pur_details = $this->get_godown_transfer_out_lines($id);
			if (!empty($old_pur_details) && !$this->archive_k1history_rows_to_audit($old_pur_details)) {
				$this->db->trans_complete();
				return false;
			}

			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $FY);
			$this->db->where('OrderID', $id);
			$this->db->where('TType', 'T');
			$this->db->delete(db_prefix() . 'K1history');

			if (!empty($history)) {
				$this->db->insert_batch(db_prefix() . 'K1history', $history);
			}
			$this->db->trans_complete();

			return $this->db->trans_status() !== false;
		}

		public function ApproveGodownStockTransfer($TransferId)
		{
			if (empty($TransferId)) {
				return ['success' => false, 'message' => 'Invalid transfer id'];
			}
			$details = $this->GetGodownStockDetails($TransferId);
			if (empty($details) || $details->OrderStatus !== 'D') {
				return ['success' => false, 'message' => 'Only draft orders can be approved'];
			}

			$plantId = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');

			$this->db->from(db_prefix() . 'K1history');
			$this->db->where('PlantID', $plantId);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $TransferId);
			$this->db->where('TType', 'T');
			$this->db->where('TType2', 'IN');
			$this->db->where('GodownID', 'RET');
			if ($this->db->count_all_results() > 0) {
				return ['success' => false, 'message' => 'Retail IN entries already exist for this transfer'];
			}

			$outRows = $this->get_godown_transfer_out_lines($TransferId);
			if (empty($outRows)) {
				return ['success' => false, 'message' => 'No wholesale OUT lines found to approve'];
			}

			$inHistory = $this->build_godown_transfer_in_history_rows_from_out($outRows);
			if (empty($inHistory)) {
				return ['success' => false, 'message' => 'No valid lines to approve'];
			}

			$this->db->trans_start();
			$this->db->insert_batch(db_prefix() . 'K1history', $inHistory);
			$this->edit_data(db_prefix() . 'K1GodownStockTransferMaster', '(TransferID="' . $TransferId . '")', [
				'OrderStatus' => 'F',
				'UserID2' => $_SESSION['username'],
				'Lupdate' => date('Y-m-d H:i:s'),
			]);
			$this->db->trans_complete();

			if ($this->db->trans_status() === false) {
				return ['success' => false, 'message' => 'Failed to approve transfer'];
			}
			return ['success' => true, 'message' => 'Order approved successfully'];
		}

		public function CancelGodownStockTransfer($TransferId)
		{
			if (empty($TransferId)) {
				return ['success' => false, 'message' => 'Invalid transfer id'];
			}
			$details = $this->GetGodownStockDetails($TransferId);
			if (empty($details) || $details->OrderStatus !== 'D') {
				return ['success' => false, 'message' => 'Only draft orders can be cancelled'];
			}
			$where = '(TransferID="' . $TransferId . '")';
			$updateOrderData = [
				'OrderStatus' => 'C',
				'Purchamt' => '0.00',
				'Discamt' => '0.00',
				'cgstamt' => '0.00',
				'sgstamt' => '0.00',
				'igstamt' => '0.00',
				'RoundOffAmt' => '0.00',
				'Invamt' => '0.00',
				'ItCount' => '0',
				'UserID2' => $_SESSION['username'],
				'Lupdate' => date('Y-m-d H:i:s'),
			];
			$this->edit_data(db_prefix() . 'K1GodownStockTransferMaster', $where, $updateOrderData);

			$wh = '(OrderID="' . $TransferId . '" AND TType="T")';
			$updateItemData = [
				'TransDate2' => date('Y-m-d H:i:s'),
				'OrderQty' => '0.00',
				'BilledQty' => '0.00',
				'DiscPerc' => '0.00',
				'DiscAmt' => '0.00',
				'cgst' => '0.00',
				'cgstamt' => '0.00',
				'sgst' => '0.00',
				'sgstamt' => '0.00',
				'igst' => '0.00',
				'igstamt' => '0.00',
				'OrderAmt' => '0.00',
				'ChallanAmt' => '0.00',
				'NetOrderAmt' => '0.00',
				'NetChallanAmt' => '0.00',
			];
			$this->edit_data(db_prefix() . 'K1history', $wh, $updateItemData);

			return ['success' => true, 'message' => 'Order cancelled successfully'];
		}

		public function load_data_for_godownstocktransfer($data)
		{
			$from_date = to_sql_date($data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql1 = '(' . db_prefix() . 'K1GodownStockTransferMaster.TransferDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59")
			AND tblK1GodownStockTransferMaster.FY = "' . $fy . '"
			AND tblK1GodownStockTransferMaster.PlantID = "' . $selected_company . '"
			ORDER BY TransferID DESC';
			$sql = 'SELECT ' . db_prefix() . 'K1GodownStockTransferMaster.*,
			(SELECT GROUP_CONCAT(CenterName SEPARATOR ",")
			FROM ' . db_prefix() . 'CenterMaster
			WHERE ' . db_prefix() . 'CenterMaster.CenterID = ' . db_prefix() . 'K1GodownStockTransferMaster.CenterID) as CenterName
			FROM ' . db_prefix() . 'K1GodownStockTransferMaster
			WHERE ' . $sql1;
			return $this->db->query($sql)->result_array();
		}
	}
