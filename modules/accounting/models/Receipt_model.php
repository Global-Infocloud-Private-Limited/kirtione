<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Receipt_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function generateNextVoucherIDNew($selected_date = '', $plant_id = '', $passage_from = ''){
    if(empty($selected_date)){
      $selected_date = date('Y-m-d');
    }
    
    if(empty($plant_id)){
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

	public function add_receipts_entry($data)
	{
		$receipts_entry = json_decode($data['receipts_entry']);
		unset($data['receipts_entry']);
		/*echo "<pre>";
		print_r($receipts_entry);
		die;*/
		$data['receipts_entry'] = to_sql_date($data['receipts_entry']);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$receipt_date = to_sql_date($data['receipt_date'])." ".date('H:i:s');
		$month = substr($receipt_date,5,2);
		$date = to_sql_date($data['receipt_date']);
		$LastUniqueID = $this->generateNextVoucherIDNew($date, $selected_company, 'RECEIPTS');
		// $get_result_to_cur_date = $this->get_result_to_cur_date_receipts($date);
		// if(empty($get_result_to_cur_date)){
		// 		if($selected_company == 1){
		// 				$new_tax_transactionNumber = get_option('next_receipts_number_for_kirti');
		// 		}
		// 		$new_voucher_number = $new_tax_transactionNumber;
		// }else{
				
		// 		$count = count($get_result_to_cur_date);
		// 		$last_index = $count - 1;
		// 		$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				
		// 		$incNo = (int) $new_voucher_number - 1;
		// 		$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "RECEIPTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
		// 		$this->db->query($sql);
		// 		if ($this->db->affected_rows() > 0) {
		// 				$this->increment_next_receipts_number();
		// 		}
		// }
			
			// cheque no duplication check added 22apr26
		foreach ($receipts_entry as $key => $value) {
				if($value[0] != '' && $value[7] != ''){
						$where = [
								"AccountID" =>$value[0],
								"ref_no" =>$value[7]
						];
						$find = $this->db->select('*')->from(db_prefix().'accountledger')->where($where)->get()->row();
						if(!empty($find)){
								return ['status' => false, 'message' => 'Cheque No Present', 'data' => $value[7]];
								die;
						}
				}
		}
		// end
			
		$i = 1;
		foreach ($receipts_entry as $key => $value) {
				if($value[0] != ''){
						
						if($value[5] != '' || $value[5] != null){
								$partyID = $value[5];     
						}else{
								$partyID = 'KASPL';
						}
						
						// Ledger Entry
						$credit_data = array(
								"PlantID" =>$selected_company,
								"Transdate" =>$receipt_date,
								"TransDate2" =>date('Y-m-d H:i:s'),
								"VoucherID" =>$LastUniqueID,
								"AccountID" =>$value[0],
								"CounterAccount" =>$data['ganeral_account'],
								"TType" =>"C",
								"CenterID" =>$value[2],
								"CommodityID" =>$value[3],
								"EntryFor" =>$value[4],
								"PartyID" =>$partyID,
								"Amount" =>$value[6],
								"ref_no" =>$value[7],
								"Narration" =>$value[8],
								"PassedFrom" =>"RECEIPTS",
								"OrdinalNo" =>$i,
								"UserID" =>$this->session->userdata('username'),
								"FY" =>$fy,
						);
						$this->db->insert(db_prefix().'accountledger', $credit_data);
						
						
						$debit_data = array(
								"PlantID" =>$selected_company,
								"Transdate" =>$receipt_date,
								"TransDate2" =>date('Y-m-d H:i:s'),
								"VoucherID" =>$LastUniqueID,
								"AccountID" =>$data['ganeral_account'],
								"CounterAccount" =>$value[0],
								"TType" =>"D",
								"CenterID" =>$value[2],
								"CommodityID" =>$value[3],
								"EntryFor" =>$value[4],
								"PartyID" =>$partyID,
								"Amount" =>$value[6],
								"ref_no" =>$value[7],
								"Narration" =>$value[8],
								"PassedFrom" =>"RECEIPTS",
								"OrdinalNo" =>$i,
								"UserID" =>$this->session->userdata('username'),
								"FY" =>$fy,
						);
						$this->db->insert(db_prefix().'accountledger', $debit_data);
						$i++;
				}
		}
// 		if(empty($get_result_to_cur_date)){
// 				$this->increment_next_receipts_number();
// 		}
		return true;
	}

	//Update receipts entry
    public function update_receipts_entry($data,$id)
    {
        $receipts_entry = json_decode($data['receipts_entry']);
        unset($data['receipts_entry']);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $receipt_date = to_sql_date($data['receipt_date1'])." ".date('H:i:s');
        $receipts_details = $this->get_receipt_entry_details($id);
        
        // Delete previous ledger details
        
        foreach ($receipts_details as $key => $value) 
        {
            $ledger_audit = array(
                "PlantID"=>$value["PlantID"],
                "FY"=>$value["FY"],
                "Transdate"=>$value["Transdate"],
                "TransDate2"=>$value["TransDate2"],
                "VoucherID"=>$value["VoucherID"],
                "AccountID"=>$value["AccountID"],
                "TType"=>$value["TType"],
                "Amount"=>$value["Amount"],
                "ref_no"=>$value["ref_no"],
                "Narration"=>$value["Narration"],
                "PassedFrom"=>$value["PassedFrom"],
                "OrdinalNo"=>$value["OrdinalNo"],
                "UserID"=>$value["UserID"],
                "Lupdate"=>date('Y-m-d H:i:s'),
                "UserID2"=>$this->session->userdata('username')
            );
            $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
        }
        
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->LIKE('PassedFrom', "RECEIPTS");
        $this->db->where('VoucherID', $id);
        $this->db->delete(db_prefix() . 'accountledger');
        // END Delete previous ledger details
        
        
        $i = 1;
        foreach ($receipts_entry as $key => $value) {
            if($value[0] != ''){
                
                if($value[5] != '' || $value[5] != null){
                    $partyID = $value[5];     
                }else{
                    $partyID = 'KASPL';
                }
                
                $credit_data = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>$receipt_date,
                    "TransDate2"=>$value["TransDate2"],
                    "VoucherID" =>$id,
                    "AccountID" =>$value[0],
                    "CounterAccount" =>$data['ganeral_account'],
                    "TType" =>"C",
                    "CenterID" =>$value[2],
                    "CommodityID" =>$value[3],
                    "EntryFor" =>$value[4],
                    "PartyID" =>$partyID,
                    "Amount" =>$value[6],
                    "ref_no" =>$value[7],
                    "Narration" =>$value[8],
                    "PassedFrom" =>"RECEIPTS",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                );
                
                $this->db->insert(db_prefix().'accountledger', $credit_data);
                
                $debit_data = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>$receipt_date,
                    "TransDate2"=>$value["TransDate2"],
                    "VoucherID" =>$id,
                    "AccountID" =>$data['ganeral_account'],
                    "CounterAccount" =>$value[0],
                    "TType" =>"D",
                    "CenterID" =>$value[2],
                    "CommodityID" =>$value[3],
                    "EntryFor" =>$value[4],
                    "PartyID" =>$partyID,
                    "Amount" =>$value[6],
                    "ref_no" =>$value[7],
                    "Narration" =>$value[8],
                    "PassedFrom" =>"RECEIPTS",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                );
                $this->db->insert(db_prefix().'accountledger', $debit_data);
                $i++;
            }
        }
        return true;
    }
    
    public function get_receipt_entry_details($id)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "RECEIPTS");
        $journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
        return $journal_data;
    }

		// Get Receipts Entry details
    public function get_receipts_entry($id)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "RECEIPTS");
        $receipts_entry = $this->db->get(db_prefix() . 'accountledger')->row();
        
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "RECEIPTS");
        $this->db->order_by('OrdinalNo', "ASC");
        $receipts_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
        
        $data_details =[];
        $total_amt = 0;
        $debamt = 0;
        foreach ($receipts_data as $key => $value) 
        {
            $amt = '';
            if($value['TType']=="C"){
                $amt = $value['Amount'];
                $amt = floatval($amt);
                $dr_cr = "C";
                $total_amt = $total_amt + $amt;
                $data_details[] = [
                    "AccountID" => strtoupper($value['AccountID']),
                    "company" => strtoupper($value['AccountID']),
                    "center" => strtoupper($value['CenterID']),
                    "commodity" => strtoupper($value['CommodityID']),
                    "entryfor" => strtoupper($value['EntryFor']),
                    "party" => strtoupper($value['PartyID']),
                    "debit" => $amt,
                    "description" => $value['Narration']
                ];
            }else{
                $amt = $value['Amount'];
                $deb_act = strtoupper($value['AccountID']);
                $debamt = $debamt + $amt;
            }
            $debamt = floatval($debamt);
        }
        if(count($data_details) < 10){

        }
        $receipts_entry->details = $data_details;
        $receipts_entry->damt = $debamt;
        $receipts_entry->d_act = $deb_act;

        return $receipts_entry;
    }

		//Get the data account to choose from.
    public function get_data_account_to_select_for_receipts() 
    {
        $accounts = $this->get_accounts_for_receipts();
        $staff_list = $this->get_staff_for_payment();
        $acc_enable_account_numbers = get_option('acc_enable_account_numbers');
        $acc_show_account_numbers = get_option('acc_show_account_numbers');
        $list_accounts = [];

        foreach ($accounts as $key => $account) {
            $note = [];
            $note['id'] = strtoupper($account['AccountID']);
            $note['label'] = $account['company'].'-'.$account['StationName'];

            $list_accounts[] = $note;
        }
        foreach ($staff_list as $key1 => $account1) {
            $note = [];
            $note['id'] = strtoupper($account1['AccountID']);
            $note['label'] = $account1['firstname']." ".$account1['lastname'].'-'.$account1["stationName"];

            $list_accounts[] = $note;
        }
        return $list_accounts;
        
    }

		public function get_accounts_for_receipts($id = '', $where = [])
    {
        if ($id) {
            
            $selected_company = $this->session->userdata('root_company');
            $this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
            $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID','left');
            $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'clients.AccountID', $id);
            return $this->db->get(db_prefix() . 'clients')->row();
        }

        $acc_show_account_numbers = get_option('acc_show_account_numbers');

        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
        $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID', 'left');
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $accounts = $this->db->get(db_prefix() . 'clients')->result_array();

        foreach ($accounts as $key => $value) {
            if($acc_show_account_numbers == 1 && $value['number'] != ''){
                $accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
            }else{
                $accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
            }
        }
        return $accounts;
    }

		// Get Account From Staff table
    public function get_staff_for_payment($id = '', $where = [])
    {
        $acc_show_account_numbers = get_option('acc_show_account_numbers');

        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'staff.*');
        $this->db->where('tblstaff.PlantID ',$selected_company);
        $accounts = $this->db->get(db_prefix() . 'staff')->result_array();

        foreach ($accounts as $key => $value) {
            if($acc_show_account_numbers == 1 && $value['number'] != ''){
                $accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
            }else{
                $accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
            }
        }
        return $accounts;
    }

		public function get_data_ganeral_account_to_select() 
    {
        $selected_company = $this->session->userdata('root_company');
        $subgroup = array('1000017');
        $this->db->where('PlantID', $selected_company);
        $this->db->where_in('SubActGroupID',$subgroup);
        $this->db->order_by('company', 'ASC');
        $accounts = $this->db->get(db_prefix() . 'clients')->result_array();
        return $accounts;
    }

		// Get All Center
    public function all_centers()
    {
        $this->db->from(db_prefix() . 'CenterMaster');
        $resultData = $this->db->get()->result_array();
        $allCenter = [];
        foreach ($resultData as $key => $account) {
            $note = [];
            $note['id'] = $account['CenterID'];
            $note['label'] = $account['CenterName'];
            $allCenter[] = $note;
        }
        return $allCenter;
    }
    
    public function all_commodities()
    {   
        $this->db->select('tblitems.ItemID,tblitems.ItemName');
        $this->db->from('tblitems');
        $this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.ShortCode =' . db_prefix() . 'items.GroupCode');
        $this->db->where(db_prefix() . 'items_sub_groups.main_group_id', 3);
        $resultData = $this->db->get()->result_array();
        $allCommodities = [];
        foreach ($resultData as $key => $account) {
            $note = [];
            $note['id'] = $account['ItemID'];
            $note['label'] = $account['ItemName'];
            $allCommodities[] = $note;
        }
        return $allCommodities;
    }
    
    // Get All Center
    public function all_parties()
    {
        $this->db->from(db_prefix() . 'PlantMaster');
        $resultData = $this->db->get()->result_array();
        $allParties = [];
        foreach ($resultData as $key => $account) {
            $note = [];
            $note['id'] = $account['PlantID'];
            $note['label'] = $account['PlantName'];
            $allParties[] = $note;
        }
        return $allParties;
    }
	
}
