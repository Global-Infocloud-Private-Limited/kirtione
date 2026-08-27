<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Paymententry_model extends App_Model
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

	public function add_payment_entry($data)
    {
        $payment_entry = json_decode($data['payment_entry']);
        unset($data['payment_entry']);
        $data['payment_entry'] = to_sql_date($data['payment_entry']);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $payment_date = to_sql_date($data['payment_date'])." ".date('H:i:s');
        $date= to_sql_date($data['payment_date']);
        $month = substr($payment_date,5,2);
		$LastUniqueID = $this->generateNextVoucherIDNew($date, $selected_company, 'PAYMENTS');
        // $get_result_to_cur_date = $this->get_result_to_cur_date_payments($date);
        // $GetLastUniqueNo = $this->GetLastUniqueNo($date);
        // $LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
        
        // if(empty($get_result_to_cur_date)){
        //     if($selected_company == 1){
        //         $new_tax_transactionNumber = get_option('next_payment_number_for_kirti');
        //     }
        //     $new_voucher_number = $new_tax_transactionNumber;
        // }else{
        //     $count = count($get_result_to_cur_date);
        //     $last_index = $count - 1;
        //     $new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
            
        //     $incNo = (int) $new_voucher_number - 1;
        //     $sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
        //     $this->db->query($sql);
        //     if ($this->db->affected_rows() > 0) {
        //         $this->increment_next_payment_number();
        //     }
        // }

		// cheque no duplication check added 22apr26
		foreach ($payment_entry as $key => $value) {
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
        foreach ($payment_entry as $key => $value) {
            if($value[0] != ''){
                if($value[5] != '' || $value[5] != null){
                    $partyID = $value[5];     
                }else{
                    $partyID = 'KASPL';
                }
                // Insert Ledger Entry
                $credit_data = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>$payment_date,
                    "TransDate2" =>date('Y-m-d H:i:s'),
                    "VoucherID" =>$LastUniqueID,
                    "AccountID" =>$value[0],
                    "TType" =>"D",
                    "CenterID" =>$value[2],
                    "CommodityID" =>$value[3],
                    "EntryFor" =>$value[4],
                    "PartyID" =>$partyID,
                    "Amount" =>$value[6],
                    "ref_no" =>$value[7],
                    "Narration" =>$value[8],
                    "CounterAccount" =>$data['ganeral_account'],
                    "PassedFrom" =>"PAYMENTS",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                    "UniquID" =>$LastUniqueID,
                );
                $this->db->insert(db_prefix().'accountledger', $credit_data);
                
                $debit_data = array(
                        "PlantID" =>$selected_company,
                        "Transdate" =>$payment_date,
                        "TransDate2" =>date('Y-m-d H:i:s'),
                        "VoucherID" =>$LastUniqueID,
                        "AccountID" =>$data['ganeral_account'],
                        "CounterAccount" =>$value[0],
                        "TType" =>"C",
                        "CenterID" =>$value[2],
                        "CommodityID" =>$value[3],
                        "EntryFor" =>$value[4],
                        "PartyID" =>$partyID,
                        "Amount" =>$value[6],
                        "ref_no" =>$value[7],
                        "Narration" =>$value[8],
                        "PassedFrom" =>"PAYMENTS",
                        "OrdinalNo" =>$i,
                        "UserID" =>$this->session->userdata('username'),
                        "FY" =>$fy,
                        "UniquID" =>$LastUniqueID,
                    );
                $this->db->insert(db_prefix().'accountledger', $debit_data);
                $i++;
            }
        }
        // if(empty($get_result_to_cur_date)){
        //     $this->increment_next_payment_number();
        // }
        return true;
    }

	public function update_payments_entry($data,$id)
    {
        $payments_entry = json_decode($data['payment_entry']);
        
        unset($data['payment_entry']);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $payment_date = to_sql_date($data['payment_date1'])." ".date('H:i:s');
        
        $UniqueID = $data['UniqueID'];
        $payments_details = $this->get_payment_entry_detailsNew($UniqueID);
        
        // Delete previous ledger details
        
        foreach ($payments_details as $key => $value) {
            $NewVoucherID = $value["VoucherID"];
           
            $ledger_audit = array(
                "PlantID"=>$value["PlantID"],
                "FY"=>$value["FY"],
                "Transdate"=>$value["Transdate"],
                "TransDate2"=>$value["TransDate2"],
                "VoucherID"=>$value["VoucherID"],
                "AccountID"=>$value["AccountID"],
                "CenterID"=>$value["CenterID"],
                "CommodityID"=>$value["CommodityID"],
                "EntryFor"=>$value["EntryFor"],
                "UniquID"=>$value["UniquID"],
                "bill_no"=>$value["bill_no"],
                "ref_no"=>$value["ref_no"],
                "TType"=>$value["TType"],
                "PartyID"=>$value["PartyID"],
                "CounterAccount"=>$value["CounterAccount"],
                "Amount"=>$value["Amount"],
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
        $this->db->LIKE('PassedFrom', "PAYMENTS");
        $this->db->where('UniquID', $UniqueID);
        $this->db->delete(db_prefix() . 'accountledger');
        // END Delete previous ledger details
        
        
        $i = 1;
        foreach ($payments_entry as $key => $value) {
            if($value[0] != ''){
                    
                if($value[5] != '' || $value[5] != null){
                    $partyID = $value[5];     
                }else{
                    $partyID = 'KASPL';
                }
                $credit_data = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>$payment_date,
                    "TransDate2" =>date('Y-m-d H:i:s'),
                    "VoucherID" =>$NewVoucherID,
                    "AccountID" =>$value[0],
                    "CounterAccount" =>$data['ganeral_account'],
                    "TType" =>"D",
                    "CenterID" =>$value[2],
                    "CommodityID" =>$value[3],
                    "EntryFor" =>$value[4],
                    "PartyID" =>$partyID,
                    "Amount" =>$value[6],
                    "ref_no" =>$value[7],
                    "Narration" =>$value[8],
                    "PassedFrom" =>"PAYMENTS",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                    "UniquID" =>$UniqueID,
                );
                    
                $this->db->insert(db_prefix().'accountledger', $credit_data);
                $debit_data = array(
                    "PlantID" =>$selected_company,
                    "Transdate" =>$payment_date,
                    "TransDate2" =>date('Y-m-d H:i:s'),
                    "VoucherID" =>$NewVoucherID,
                    "AccountID" =>$data['ganeral_account'],
                    "CounterAccount" =>$value[0],
                    "TType" =>"C",
                    "CenterID" =>$value[2],
                    "CommodityID" =>$value[3],
                    "EntryFor" =>$value[4],
                    "PartyID" =>$partyID,
                    "Amount" =>$value[6],
                    "ref_no" =>$value[7],
                    "Narration" =>$value[8],
                    "PassedFrom" =>"PAYMENTS",
                    "OrdinalNo" =>$i,
                    "UserID" =>$this->session->userdata('username'),
                    "FY" =>$fy,
                    "UniquID" =>$UniqueID,
                );
                $this->db->insert(db_prefix().'accountledger', $debit_data);
                $i++;
            }  
        }
        return true;
    }

	public function get_payment_entry_detailsNew($id)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('UniquID', $id);
        $this->db->LIKE('PassedFrom', "PAYMENTS");
        $journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
        return $journal_data;
    }

	// get Payment Details Entry
    public function get_payments_entry($id)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "PAYMENTS");
        $payment_entry = $this->db->get(db_prefix() . 'accountledger')->row();
        
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->where('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "PAYMENTS");
        $this->db->order_by('OrdinalNo', "ASC");
        $payment_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
        
        $data_details =[];
        $total_amt = 0;
        $debamt = 0;
        foreach ($payment_data as $key => $value) {
            $amt = '';
            if($value['TType']=="D"){
                $amt = $value['Amount'];
                $amt = floatval($amt);
                $dr_cr = "D";
                $total_amt = $total_amt + $amt;
                $data_details[] = [
                "AccountID" => strtoupper($value['AccountID']),
                "company" => strtoupper($value['AccountID']),
                "center" => $value['CenterID'],
                "commodity" => $value['CommodityID'],
                "entryfor" => $value['EntryFor'],
                "party" => $value['PartyID'],
                "cheque_no" => $value['ref_no'],
                "debit" => $amt,
                "description" => $value['Narration']];
            }else{
                $amt = $value['Amount'];
                $deb_act = strtoupper($value['AccountID']);
                $debamt = $debamt + $amt;
            }
            $debamt = floatval($debamt);
        }
        if(count($data_details) < 10){

        }
        $payment_entry->details = $data_details;
        $payment_entry->damt = $debamt;
        $payment_entry->d_act = $deb_act;
        return $payment_entry;
    }

	// Get All account To select in payment page
    public function get_data_account_to_select_for_payment() 
    {
        $accounts = $this->get_accounts_for_payment();
        $staff_list = $this->get_staff_for_payment();
        
        $acc_enable_account_numbers = get_option('acc_enable_account_numbers');
        $acc_show_account_numbers = get_option('acc_show_account_numbers');
        $list_accounts = [];
        foreach ($accounts as $key => $account) {
            $note = [];
            $note['id'] = strtoupper($account['AccountID']);
            $note['label'] = $account['company'].' - '.$account['AccountID'];
            $list_accounts[] = $note;
        }
        foreach ($staff_list as $key1 => $account1) {
            $note = [];
            $note['id'] = strtoupper($account1['AccountID']);
            $note['label'] = $account1['firstname']." ".$account1['lastname'].' - '.$account1['AccountID'];
            $list_accounts[] = $note;
        }
        return $list_accounts;
    }

	// Get Account From Client table
    public function get_accounts_for_payment($id = '', $where = [])
    {
        if ($id) {
            
            $selected_company = $this->session->userdata('root_company');
            $this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
            $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID' ,"LEFT");
            $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
            $this->db->where('AccountID', $id);
            return $this->db->get(db_prefix() . 'clients')->row();
        }
        $acc_show_account_numbers = get_option('acc_show_account_numbers');

        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
        $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID', 'LEFT');
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
