<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	require_once(APPPATH . 'libraries/import/App_import.php');
	
	class Import_rate_master extends App_import
	{ 
		protected $notImportableFields = ['id'];
		
		protected $requiredFields = ['state_id', 'distributor_id'];
		
		public function __construct()
		{
			$this->addItemsGuidelines();
			parent::__construct();
		}
		
		public function RateImport($selected_company='', $cuurent_user='',$RateType)
		{
			$this->initialize();
			
			$databaseFields      = $this->getImportableDatabaseFields();
			
			array_push($databaseFields,"Type","KeyID","UserID","TransDate","IsActive");
			//print_r($databaseFields);
			if($RateType == "TS"){
				unset($databaseFields[5]);
			}
			unset($databaseFields[1]);
			unset($databaseFields[3]);
			/*echo "<pre>";
				print_r($databaseFields);
			die;*/
			$totalDatabaseFields = count($databaseFields);
			foreach ($this->getRows() as $rowNumber => $row) {
				$insert = [];
				$insert2 = [];
				// Check Item is assigned to respective Center
				$CenterID = $row[0];
				$ItemID = $row[2];
				$CheckItemID = $this->CheckCenterWiseItemID($CenterID,$ItemID,$RateType);
				if($CheckItemID){
					$NewRate = 0; 
					foreach($databaseFields as $i=>$val){
						$row[$i] = $this->checkNullValueAddedByUser($row[$i]);
						if ($databaseFields[$i] == 'Type' && $RateType != "TS") {
							$row[$i] = $RateType; // For Kirti Purchase Rate Type
							}elseif ($databaseFields[$i] == 'ItemID') {
							$row[$i] = $ItemID;
							}elseif ($databaseFields[$i] == 'CenterID') {
							$row[$i] = $CenterID;
							}elseif ($databaseFields[$i] == 'KeyID') {
							$row[$i] = "C01"; // Key for kirti rate 
							}elseif ($databaseFields[$i] == 'Rate') {
							$row[$i] = $row[$i];
							$NewRate = number_format($row[$i], 2, '.', '');
							}elseif ($databaseFields[$i] == 'UserID') {
							$row[$i] = $cuurent_user;
							}elseif ($databaseFields[$i] == 'TransDate') {
							$row[$i] = date('Y-m-d H:i:s');
							}elseif ($databaseFields[$i] == 'IsActive') {
							$row[$i] = "Y";
						}
						$insert[$databaseFields[$i]] = $row[$i];
					}
					if($CheckItemID->Rate == "" || $CheckItemID->Rate == null){
						$ExRate = 0.00;
						}else{
						$ExRate = number_format($CheckItemID->Rate, 2, '.', '');
					}
					if($NewRate == $ExRate){
						
						}else{
						$insert = $this->trimInsertValues($insert);
						if (count($insert) > 0) {
							$this->incrementImported();
							$id = null;
							if (!$this->isSimulation()) {
								// InActive previous rate before insert new rate
								$this->ci->db->where('KeyID', "C01");
								$this->ci->db->where('CenterID', $CenterID);
								$this->ci->db->LIKE('ItemID', $ItemID);
								if($RateType =="TS"){
									$this->ci->db->update(db_prefix() . 'SaleRateMaster', [
									'IsActive' => "N"
									]);
									// Insert New rate
									$this->ci->db->insert(db_prefix().'SaleRateMaster', $insert);
									}else{
									$this->ci->db->LIKE('Type', $RateType);
									$this->ci->db->update(db_prefix() . 'RateMaster', [
									'IsActive' => "N"
									]);
									// Insert New rate
									$this->ci->db->insert(db_prefix().'RateMaster', $insert);
								}
								} else {
								$this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
							}
						}
					}
					
					
					if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
						break;
					}
					
				}
			}
		}
		public function perform($states='', $distributor_id='', $effective_date='',$selected_company='', $cuurent_user='')
		{
			$this->initialize();
			
			$databaseFields      = $this->getImportableDatabaseFields();
			array_push($databaseFields,"PlantID","state_id","distributor_id","effective_date","UserId","UserID2","gst","SaleRate");
			
			$totalDatabaseFields = count($databaseFields);
			
			foreach ($this->getRows() as $rowNumber => $row) {
				$insert = [];
				$insert2 = [];
				for ($i = 0; $i < $totalDatabaseFields; $i++) {
					$row[$i] = $this->checkNullValueAddedByUser($row[$i]);
					if ($databaseFields[$i] == 'PlantID') {
						$row[$i] = $selected_company;
						} elseif ($databaseFields[$i] == 'state_id') {
						$row[$i] = $states;
						} elseif ($databaseFields[$i] == 'distributor_id') {
						$row[$i] = $distributor_id;
						} elseif ($databaseFields[$i] == 'item_id') {
						$row[$i] = $row[$i];
						$item_code = $row[$i];
						} elseif ($databaseFields[$i] == 'assigned_rate') {
						$row[$i] = $row[$i];
						$new_rate = $row[$i];
						}  elseif ($databaseFields[$i] == 'effective_date') {
						$row[$i] = to_sql_date($effective_date)." 00:00:01";
						} elseif ($databaseFields[$i] == 'UserId') {
						$row[$i] = $cuurent_user;
						} elseif ($databaseFields[$i] == 'UserID2') {
						$row[$i] = null;
						}elseif ($databaseFields[$i] == 'gst') {
						$item_code_exit = $this->itemcode_exit($item_code,$selected_company);
						if($item_code_exit){
							$taxID = $item_code_exit->tax;
							$tax = $this->getTaxBy('id', $taxID);
							$row[$i] = $tax->taxrate;
							$gst = $tax->taxrate;
							}else{
							$row[$i] = $row[$i];
						}
						} elseif ($databaseFields[$i] == 'SaleRate') {
						
						$taxAmt = ($new_rate/100) * $gst;
						$saleRate = $taxAmt + $new_rate;
						$row[$i] = $saleRate;
					} 
					$item_code_exit = $this->itemcode_exit($item_code,$selected_company);
					
					if($item_code_exit){
						$insert[$databaseFields[$i]] = $row[$i];
					}else{ }
				}
				
				$insert = $this->trimInsertValues($insert);
				
				if (count($insert) > 0) {
					$this->incrementImported();
					$id = null;
					
					if (!$this->isSimulation()) {
						$check_item_code = $this->check_itemcode($item_code,$states,$distributor_id,$selected_company);
						if ($check_item_code == "0") {
							$this->ci->db->insert(db_prefix().'rate_master', $insert);
							}else {
							// inser rate History2
							$insert_history2 = array(
                            'PlantID' =>$selected_company,
                            'DistributorType' =>$distributor_id,
                            'ItemID' =>$item_code,
                            'BasicRate' =>$check_item_code->assigned_rate,
                            'SaleRate' =>$check_item_code->SaleRate,
                            'EffDate' =>$check_item_code->effective_date,
                            'UserId' =>$check_item_code->UserId,
                            'StateID' =>$states,
                            'gst' =>$check_item_code->gst,
                            'UserID2' =>$check_item_code->UserId,
                            'Lupdate' =>to_sql_date($effective_date)." ".date('H:i:s')
							);
							$this->ci->db->insert(db_prefix().'ratehistory2', $insert_history2);
							//Update Rate Master
							$newGstRate = ($new_rate / 100) * $check_item_code->gst;
							$newSaleRate = $new_rate + $newGstRate;
							$this->ci->db->where('PlantID', $selected_company);
							$this->ci->db->where('item_id', $item_code);
							$this->ci->db->LIKE('state_id', $states);
							$this->ci->db->LIKE('distributor_id', $distributor_id);
							$this->ci->db->update(db_prefix() . 'rate_master', [
							'assigned_rate' => $new_rate,
							'SaleRate' => $newSaleRate,
							'UserID2' => $cuurent_user,
							'effective_date' =>to_sql_date($effective_date)." 00:00:01",
							'Lupdate' =>$effective_date." ".date('H:i:s'),
							]);
						}
						} else {
						$this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
					}
					//$this->handleCustomFieldsInsert($id, $row, $i, $rowNumber, 'items_pr');
				}
				if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
					break;
				}
			}
		}
		
		public function performStatement($BankAccount, $selected_company='', $cuurent_user='')
		{
			$this->initialize();
			
			$databaseFields      = $this->getImportableStatementDatabaseFields();
			array_push($databaseFields,"AccountID","UserID","UserID2","TransDate","Lupdate");
			$totalDatabaseFields = count($databaseFields);
			$rec = 1;
			// echo "<pre>";print_r($this->getRows());die;
			foreach ($this->getRows() as $rowNumber => $row) {
				$insert = [];
				$insert2 = [];
				// echo "<pre>";print_r($row);die;
				$debit = 0;
				$credit = 0;
				for ($i = 0; $i < $totalDatabaseFields; $i++) {
					$row[$i] = $this->checkNullValueAddedByUser($row[$i]);
					if ($databaseFields[$i] == 'post_date') {
						$inputDate = $row[$i];
						$inputDate = to_sql_date($inputDate)." 00:00:00";
						$row[$i] = $inputDate;
						$transaction_date = $inputDate;
						
					} elseif ($databaseFields[$i] == 'account_description') {
						$description = $row[$i];
					} elseif ($databaseFields[$i] == 'branch_code') {
						$branch_code = $row[$i];
					} elseif ($databaseFields[$i] == 'credit') {
						$credit = str_replace(',', '', $row[$i]);
					} elseif ($databaseFields[$i] == 'debit') {
						$debit = str_replace(',', '', $row[$i]);
					} elseif ($databaseFields[$i] == 'cheque_number') {
						$chq_ref_no = $row[$i];
					}elseif ($databaseFields[$i] == 'value_date') {
						$ValinputDate = $row[$i];
						$ValinputDate = to_sql_date($ValinputDate)." 00:00:00";
						$value_date = $ValinputDate;
					} 
				}
				
				if(!empty($transaction_date) && !empty($description) && !empty($value_date)){
					if ( ($credit > 0) xor ($debit > 0) ) {
						$insert['AccountID'] = $BankAccount;
						$insert['transaction_date'] = $transaction_date;
						$insert['value_date'] = $value_date;
						$insert['branch_code'] = $branch_code;
						$insert['description'] = $description;
						$insert['chq_ref_no'] = $chq_ref_no;
						$insert['debit'] = $debit;
						$insert['credit'] = $credit;
						$insert['TransDate'] = date('Y-m-d H:i:s');
						$insert['UserID'] = $cuurent_user;
					}
				}
				$insert = $this->trimInsertValues($insert);
				if (count($insert) > 0) {
					$id = null;
					if (!$this->isSimulation()) {
						// if($rec == 3){
						// print_r($insert);die;
						// }
						// $rec++;
						//$checkstatement = $this->check_statements($insert);
						//if(empty($checkstatement)){
							if($this->ci->db->insert(db_prefix().'import_statement', $insert)){
								$this->incrementImported();
							}
						//}
					} else {
						$this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
					}
				}
				
				if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
					break;
				}
			}
			//die;
		}
		
		public function formatFieldNameForHeading($field)
		{
			$this->ci->load->model('currencies_model');
			
			if (strtolower($field) == 'group_id') {
				return 'Group';
				} elseif(strtolower($field) == 'Item Code'){
				return 'item_id';
				} elseif(strtolower($field) == 'Basic rate'){
				return 'assigned_rate';
				} elseif (startsWith($field, 'rate')) {
				$str = 'Rate - ';
				// Base currency
				if ($field == 'rate') {
					$str .= $this->ci->currencies_model->get_base_currency()->name;
					} else {
					$str .= $this->ci->currencies_model->get(strafter($field, 'rate_currency_'))->name;
				}
				
				return $str;
			}
			
			return parent::formatFieldNameForHeading($field);
		}
		
		protected function failureRedirectURL()
		{
			return admin_url('invoice_items/import');
		}
		
		private function addItemsGuidelines()
		{
			//$this->addImportGuidelinesInfo('In the column <b>Tax</b> and <b>Tax2</b>, you <b>must</b> add either the <b>TAX NAME or the TAX ID</b>, which you can get them by navigating to <a href="' . admin_url('taxes') . '" target="_blank">Setup->Finance->Taxes</a>.');
			//$this->addImportGuidelinesInfo('In the column <b>Group</b>, you <b>must</b> add either the <b>GROUP NAME or the GROUP ID</b>, which you can get them by clicking <a href="' . admin_url('invoice_items?groups_modal=true') . '" target="_blank">here</a>.');
		}
		
		private function formatValuesForSimulation($values)
		{
			foreach ($values as $column => $val) {
				if ($column == 'group_id' && !empty($val) && is_numeric($val)) {
					$group = $this->getGroupBy('id', $val);
					if ($group) {
						$values[$column] = $group->name;
					}
					} elseif (($column == 'tax' || $column == 'tax2') && !empty($val) && is_numeric($val)) {
					$tax = $this->getTaxBy('id', $val);
					if ($tax) {
						$values[$column] = $tax->name . ' (' . $tax->taxrate . '%)';
					}
				}
			}
			
			return $values;
		}
		
		private function getTaxBy($field, $idOrName)
		{
			$this->ci->db->where($field, $idOrName);
			
			return $this->ci->db->get(db_prefix().'taxes')->row();
		}
		
		private function getGroupBy($field, $idOrName)
		{
			$this->ci->db->where($field, $idOrName);
			
			return $this->ci->db->get(db_prefix().'items_groups')->row();
		}
		private function getitemcodeby($field, $code)
		{
			$this->ci->db->where($field, $code);
			
			return $this->ci->db->get(db_prefix().'items')->row();
		}
		
		private function checkitemcode($item_code,$states,$distributor_id,$selected_company)
		{
			$this->ci->db->where('item_id', $item_code);
			$this->ci->db->where('state_id', $states);
			$this->ci->db->where('distributor_id', $distributor_id);
			$this->ci->db->where('PlantID', $selected_company);
			
			return $this->ci->db->get(db_prefix().'rate_master')->row();
		}
		private function checkitemcode_history($item_code,$states,$distributor_id,$selected_company)
		{
			$this->ci->db->where('ItemID', $item_code);
			$this->ci->db->where('StateID', $states);
			$this->ci->db->where('DistributorType', $distributor_id);
			$this->ci->db->where('PlantID', $selected_company);
			
			return $this->ci->db->get(db_prefix().'ratehistory2')->row();
		}
		
		private function checkstatements($insert)
		{
			$this->ci->db->where('transaction_date', $insert["transaction_date"]);
			$this->ci->db->where('value_date', $insert["value_date"]);
			$this->ci->db->where('description', $insert["description"]);
			$this->ci->db->where('AccountID', $insert["AccountID"]);
			$this->ci->db->where('chq_ref_no', $insert["chq_ref_no"]);
			$this->ci->db->where('debit', $insert["debit"]);
			$this->ci->db->where('credit', $insert["credit"]);
			return $this->ci->db->get(db_prefix().'import_statement')->row();
		}
		
		private function itemcode_exit_or_not($item_code,$selected_company)
		{
			
			$this->ci->db->where('item_code', $item_code);
			$this->ci->db->where('PlantID', $selected_company);
			return $this->ci->db->get(db_prefix().'items')->row();
		}
		private function CheckCenterWiseItemID($CenterID,$ItemID,$RateType)
		{
			if($RateType == "TS"){
				// Kirti Sale Rate Master For Trader
				$this->ci->db->select('tblCenter_wise_item.CenterID,tblCenter_wise_item.ItemID,tblSaleRateMaster.Rate');
				$this->ci->db->where('tblCenter_wise_item.CenterID', $CenterID);
				$this->ci->db->where('tblCenter_wise_item.ItemID', $ItemID);
				//$this->ci->db->where('tblRateMaster.Type', $RateType);
				$this->ci->db->join('tblSaleRateMaster', 'tblSaleRateMaster.CenterID = tblCenter_wise_item.CenterID AND tblSaleRateMaster.ItemID = tblCenter_wise_item.ItemID',"LEFT");
				return $this->ci->db->get(db_prefix().'Center_wise_item')->row();
				}else{
				// Kirti Purchase rate master for Trader/Farmer
				$this->ci->db->select('tblCenter_wise_item.CenterID,tblCenter_wise_item.ItemID,tblRateMaster.Rate');
				$this->ci->db->where('tblCenter_wise_item.CenterID', $CenterID);
				$this->ci->db->where('tblCenter_wise_item.ItemID', $ItemID);
				//$this->ci->db->where('tblRateMaster.Type', $RateType);
				$this->ci->db->join('tblRateMaster', 'tblRateMaster.CenterID = tblCenter_wise_item.CenterID AND
				tblRateMaster.ItemID = tblCenter_wise_item.ItemID AND tblRateMaster.Type = "'.$RateType.'"',"LEFT");
				return $this->ci->db->get(db_prefix().'Center_wise_item')->row();
			}
		}
		
		private function taxValue($value)
		{
			if ($value != '') {
				if (!is_numeric($value)) {
					$tax   = $this->getTaxBy('name', $value);
					$value = $tax ? $tax->id : 0;
				}
				} else {
				$value = 0;
			}
			
			return $value;
		}
		
		private function groupValue($value)
		{
			if ($value != '') {
				if (!is_numeric($value)) {
					$group = $this->getGroupBy('name', $value);
					$value = $group ? $group->id : 0;
				}
				} else {
				$value = 0;
			}
			
			return $value;
		}
		private function itemcode($value)
		{
			if ($value != '') {
				
                $group = $this->getitemcodeby('item_code', $value);
                $value = $group ? $group->id : 0;
				
				} else {
				$value = 0;
			}
			
			return $value;
		}
		private function check_itemcode($item_code,$states,$distributor_id,$selected_company)
		{
			
			$rate_master_detail = $this->checkitemcode($item_code,$states,$distributor_id,$selected_company);
			$item_code = $rate_master_detail ? $rate_master_detail : 0;
			
			return $item_code;
			
		}
		
		private function check_statements($insert)
		{
			
			$checkrecord = $this->checkstatements($insert);
			// $checkrecord = $checkrecord ? $checkrecord : '';
			// print_r($checkrecord);die;
			return $checkrecord;
			
		}
		private function check_itemcode_history($item_code,$states,$distributor_id,$selected_company)
		{
			
			$rate_master_detail = $this->checkitemcode_history($item_code,$states,$distributor_id,$selected_company);
			$item_code = $rate_master_detail ? $rate_master_detail : 0;
			
			return $item_code;
			
		}
		private function itemcode_exit($item_code,$selected_company)
		{
			
			$item_detail = $this->itemcode_exit_or_not($item_code,$selected_company);
			$item_code = $item_detail ? $item_detail : '';
			
			return $item_code;
			
		}
	}
