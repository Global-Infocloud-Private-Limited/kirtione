<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Year_transfer_model extends App_Model
{
    public function __construct()
    { 
        parent::__construct();
    }
    public function get_vendor_data($id = '', $where = [])
    {
      
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select( db_prefix() . 'clients.company,'.db_prefix() . 'clients.userid,'.db_prefix() . 'clients.AccountID,');
       
        $this->db->where_in(db_prefix() . 'clients.SubActGroupID', ['50003002','60001004']);
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $this->db->order_by('company', 'asc');
        return $this->db->get(db_prefix() . 'clients')->result_array();
    }
    public function get_firm_data($id = '', $where = [])
    {
      
        $selected_company = $this->session->userdata('root_company');
        $this->db->select( db_prefix() . 'setup.*');
        $this->db->where(db_prefix() . 'setup.PlantID', $selected_company);
        $this->db->order_by('FY', 'asc');
        return $this->db->get(db_prefix() . 'setup')->result_array();
    }
    
    public function transfer_year($data = '')
    {
        
        // Load the DB utility class
        $this->load->dbutil();
        
        $selected_company = $this->session->userdata('root_company');
        if($selected_company == "1"){
            $GodownID = 'CSPL';
        }else if($selected_company == "2"){
            $GodownID = 'CFF';
        }else if($selected_company == "3"){
            $GodownID = 'CBUPL';
        }
        
        $fy = $this->session->userdata('finacial_year');
        if($selected_company == 1){
                $com_name = 'CSPL';
            }elseif($selected_company == 2){
                $com_name = 'CFF';
            }elseif($selected_company == 3){
                $com_name = 'CBU';
            }
        $prefs = array(
            'tables'        => array('tblaccountbalances','tblstockmaster','tblaccountcrates','tblaccountledger'),   // Array of tables to backup.
            'ignore'        => array(),                     // List of tables to omit from the backup
            'format'        => 'zip',                       // gzip, zip, txt
            'filename'      => '"'.$com_name.'"_bal_ledger_stock_crates.sql',              // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
            'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
            'newline'       => "\n"                         // Newline character used in backup file
        );
        // Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup($prefs);
        
        // Load the file helper and write the file to your server
        $this->load->helper('file');
        $this->load->library('zip');
        $UserID = $this->session->userdata('username');
        $file_name = $com_name.'_bal_ledger_stock_crates_'.$UserID.'_'.date('d-m-Y');
        write_file('uploads/backup/'.$file_name.'.zip', $backup);
        
        // Load the download helper and send the file to your desktop
        $this->load->helper('download');
        //force_download($file_name.'.zip', $backup);
        //die;
        
        
        $trf_from = $data["trf_from"];
        $trf_to = $data["trf_to"];
        $trf_accounts = $data["trf_accounts"];
        $trf_stock = $data["trf_stock"];
        $trf_crates = $data["trf_crates"];
        
        $this->db->select( db_prefix() . 'accountgroups.*,'.db_prefix() . 'accountgroupssub.*,'.db_prefix() . 'clients.AccountID');
        $this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroups.ActGroupID = '.db_prefix() . 'accountgroupssub.ActGroupID ');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'accountgroupssub.SubActGroupID = '.db_prefix() . 'clients.SubActGroupID AND '.db_prefix() . 'clients.PlantID = '.$selected_company);
        $this->db->where(db_prefix() . 'accountgroups.ActGroupMovementID !=', 'B');
        $this->db->order_by('ActGroupName', 'asc');
        $TransferGroup = $this->db->get(db_prefix() . 'accountgroups')->result_array();
        $NBal_trf_AccountID = array();
        foreach ($TransferGroup as $key11 => $value11) {
            array_push($NBal_trf_AccountID, trim(strtoupper($value11['AccountID'])));
        }
        
        // From Account List Balances
        $this->db->select( db_prefix() . 'accountbalances.*');
        $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'accountbalances.FY', $trf_from);
        $this->db->order_by('AccountID', 'asc');
        $AccountList_From = $this->db->get(db_prefix() . 'accountbalances')->result_array();
        
        // To Account List Balances
        $this->db->select( db_prefix() . 'accountbalances.*');
        $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'accountbalances.FY', $trf_to);
        $this->db->order_by('AccountID', 'asc');
        $AccountList_To = $this->db->get(db_prefix() . 'accountbalances')->result_array();
        
        // FROM Accounts Credit Balance 
            $trf_from_new = $trf_from + 1;
            $from_date = '20'.$trf_from.'-04-01';
            $to_date = '20'.$trf_from_new.'-03-31';
            
            // credit balance SUM
                $this->db->select('sum(Amount) as credit_bal,AccountID');
                $this->db->where('tblaccountledger.PlantID', $selected_company);
                $this->db->LIKE('tblaccountledger.TType', 'C');
                $this->db->LIKE('tblaccountledger.FY', $trf_from);
                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
                $this->db->group_by('AccountID');
                $credit_bal = $this->db->get('tblaccountledger')->result_array();
        
        // FROM Accounts Debit Balance 
            
            // Debit balance SUM
                $this->db->select('sum(Amount) as debit_bal,AccountID');
                $this->db->where('tblaccountledger.PlantID', $selected_company);
                $this->db->LIKE('tblaccountledger.TType', 'D');
                $this->db->LIKE('tblaccountledger.FY', $trf_from);
                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
                $this->db->group_by('AccountID');
                $debit_bal = $this->db->get('tblaccountledger')->result_array();
        
            
        foreach ($AccountList_From as $key1 => $value1) {
            $find = 0;
            foreach ($AccountList_To as $key2 => $value2) {
                if(trim(strtoupper($value1['AccountID'])) == trim(strtoupper($value2['AccountID'])) && $value1['PlantID']==$value2['PlantID']){
                    //echo "update".$value1['AccountID'];
                    $debitAmt = 0;
                    $balance = 0;
                    $creditAmt = 0;
                    if($trf_accounts== "1"){
                        foreach($credit_bal as $value3){
                            if(trim(strtoupper($value1["AccountID"])) == trim(strtoupper($value3["AccountID"]))){
                                $creditAmt = $value3["credit_bal"];
                            }
                        }
                        foreach($debit_bal as $value4){
                            if(trim(strtoupper($value1["AccountID"])) == trim(strtoupper($value4["AccountID"]))){
                                $debitAmt = $value4["debit_bal"];
                            }
                        }
                        $balance = $value1['BAL1'] + $debitAmt - $creditAmt;
                        $update_array = array(
                            'BAL1' =>$balance,
                        );
                        if (in_array(trim(strtoupper($value1["AccountID"])), $NBal_trf_AccountID)){
                            
                        }else{
                            $this->db->where('PlantID', $selected_company);
                            $this->db->LIKE('FY', $trf_to);
                            $this->db->where('AccountID',$value1["AccountID"]);
                            $this->db->update(db_prefix() . 'accountbalances',$update_array);
                        }
                        
                    }
                    $find = 1;
                }
            }
            if($find == "0"){
                $debitAmt = 0;
                $balance = 0;
                $creditAmt = 0;
                if($trf_accounts== "1"){
                    foreach($credit_bal as $value3){
                        if(trim(strtoupper($value1["AccountID"])) == trim(strtoupper($value3["AccountID"]))){
                            $creditAmt = $value3["credit_bal"];
                        }
                    }
                    foreach($debit_bal as $value4){
                        if(trim(strtoupper($value1["AccountID"])) == trim(strtoupper($value4["AccountID"]))){
                            $debitAmt = $value4["debit_bal"];
                        }
                    }
                    $balance = $value1['BAL1'] + $debitAmt - $creditAmt;
                        if (in_array(trim(strtoupper($value1["AccountID"])), $NBal_trf_AccountID)){
                            $insert_array = array(
                                'PlantID'=>$selected_company,
                                'FY'=>$trf_to,
                                'AccountID' =>$value1['AccountID'],
                            );
                        }else{
                            $insert_array = array(
                                'PlantID'=>$selected_company,
                                'FY'=>$trf_to,
                                'AccountID' =>$value1['AccountID'],
                                'BAL1' =>$balance,
                            );
                        }
                    
                }else{
                    $insert_array = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$trf_to,
                        'AccountID' =>$value1['AccountID'],
                    );
                }
                $this->db->insert(db_prefix() . 'accountbalances',$insert_array);
                //echo $value1['AccountID'] . " Insert Accounts";
            }
        }
        
        // From Item Stock 
            $this->db->select( db_prefix() . 'stockmaster.*');
            $this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'stockmaster.FY', $trf_from);
            $this->db->where('GodownID',$GodownID);
            $this->db->order_by('ItemID', 'asc');
            $ItemIDList_From = $this->db->get(db_prefix() . 'stockmaster')->result_array();
            
        // To Item Stock 
            $this->db->select( db_prefix() . 'stockmaster.*');
            $this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'stockmaster.FY', $trf_to);
            $this->db->where('GodownID',$GodownID);
            $this->db->order_by('ItemID', 'asc');
            $ItemIDList_to = $this->db->get(db_prefix() . 'stockmaster')->result_array();
        
        foreach ($ItemIDList_From as $key5 => $value5) {
            $find1 = 0;
            foreach ($ItemIDList_to as $key6 => $value6) {
                if(trim(strtoupper($value5['ItemID'])) == trim(strtoupper($value6['ItemID'])) && $value5['PlantID']==$value6['PlantID']){
                    //echo "update".$value1['AccountID'];
                    
                    $balance1 = 0;
                    if($trf_stock== "Y"){
                      
                        $balance1 = $value5['OQty'] + $value5['PQty'] - $value5['PRQty'] - $value5['IQty'] + $value5['PRDQty'] + $value5['gtiqty'] - $value5['gtoqty'] - $value5['SQty'] + $value5['SRQty'] - $value5['DQTY'] - $value5['ADJQTY'];
                        $update_array2 = array(
                            'OQty' =>$balance1,
                        );
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $trf_to);
                        $this->db->where('ItemID',$value5['ItemID']);
                        $this->db->where('GodownID',$GodownID);
                        $this->db->update(db_prefix() . 'stockmaster',$update_array2);
                    }
                    $find1 = 1;
                }
            }
            if($find1 == "0"){
               
                $balance = 0;
                if($trf_stock== "Y"){
                    
                    $balance = $value5['OQty'] + $value5['PQty'] - $value5['PRQty'] - $value5['IQty'] + $value5['PRDQty'] + $value5['gtiqty'] - $value5['gtoqty'] - $value5['SQty'] + $value5['SRQty'] - $value5['DQTY'] - $value5['ADJQTY'];
                    $insert_array2 = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$trf_to,
                        'ItemID' =>$value5['ItemID'],
                        'OQty' =>$balance,
                        'GodownID' =>$GodownID,
                    );
                }else{
                    $insert_array2 = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$trf_to,
                        'ItemID' =>$value5['ItemID'],
                        'GodownID' =>$GodownID,
                    );
                }
            $this->db->insert(db_prefix() . 'stockmaster',$insert_array2);
                //echo $value1['AccountID'] . " Insert Accounts";
            }
        }    
        
        // From Account For Crates 
            $this->db->select( db_prefix() . 'accountcrates.*');
            $this->db->where(db_prefix() . 'accountcrates.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'accountcrates.FY', $trf_from);
            $this->db->group_by('AccountID');
            $this->db->order_by('AccountID', 'asc');
            $AccountList_From2 = $this->db->get(db_prefix() . 'accountcrates')->result_array();
            
        // TO Account For Crates 
            $this->db->select( db_prefix() . 'accountcrates.*');
            $this->db->where(db_prefix() . 'accountcrates.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'accountcrates.FY', $trf_to);
            $this->db->where(db_prefix() . 'accountcrates.PassedFrom', 'OPENCRATES');
            $this->db->order_by('AccountID', 'asc');
            $AccountList_To2 = $this->db->get(db_prefix() . 'accountcrates')->result_array();
            
        // FROM Accounts Credit Crates 
            $trf_from_new = $trf_from + 1;
            $from_date = '20'.$trf_from.'-04-01';
            $to_date = '20'.$trf_from_new.'-03-31';
            
            // credit Crates SUM
                $this->db->select('sum(Qty) as credit_crates,AccountID');
                $this->db->where('tblaccountcrates.PlantID', $selected_company);
                $this->db->where('tblaccountcrates.TType LIKE', 'C');
                $this->db->where('tblaccountcrates.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
                $this->db->group_by('AccountID');
                $credit_crates = $this->db->get('tblaccountcrates')->result_array();
        
        // FROM Accounts Debit Crates 
            
            // Debit Crates SUM
                $this->db->select('sum(Qty) as debit_crates,AccountID');
                $this->db->where('tblaccountcrates.PlantID', $selected_company);
                $this->db->where('tblaccountcrates.TType LIKE', 'D');
                $this->db->where('tblaccountcrates.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
                $this->db->group_by('AccountID');
                $debit_crates = $this->db->get('tblaccountcrates')->result_array();
        
        foreach ($AccountList_From2 as $key7 => $value7) {
            $find2 = 0;
            foreach ($AccountList_To2 as $key8 => $value8) {
                if(trim(strtoupper($value7['AccountID'])) == trim(strtoupper($value8['AccountID'])) && $value7['PlantID']==$value8['PlantID']){
                    //echo "update".$value1['AccountID'];
                    $debitcrates = 0;
                    $balance_crates = 0;
                    $creditcrates = 0;
                    if($trf_crates== "Y"){
                        foreach($credit_crates as $value9){
                            if(trim(strtoupper($value7["AccountID"])) == trim(strtoupper($value9["AccountID"]))){
                                $creditcrates = $value9["credit_crates"];
                            }
                        }
                        foreach($debit_crates as $value10){
                            if(trim(strtoupper($value7["AccountID"])) == trim(strtoupper($value10["AccountID"]))){
                                $debitcrates = $value10["debit_crates"];
                            }
                        }
                        $balance_crates =  $debitcrates - $creditcrates;
                        $update_array = array(
                            'Qty' =>$balance_crates,
                        );
                        $this->db->where('PlantID', $selected_company);
                        $this->db->LIKE('FY', $trf_to);
                        $this->db->where('AccountID',$value7["AccountID"]);
                        $this->db->where('PassedFrom',"OPENCRATES");
                        $this->db->update(db_prefix() . 'accountcrates',$update_array);
                    }
                    $find2 = 1;
                }
            }
            if($find2 == "0"){
                $debitcrates = 0;
                $balance_crates = 0;
                $creditcrates = 0;
                if($selected_company == 1){
                    $next_opencrates_number = get_option('next_opencrates_number_for_cspl');
                }elseif($selected_company == 2){
                    $next_opencrates_number = get_option('next_opencrates_number_for_cff');
                }elseif($selected_company == 3){
                    $next_opencrates_number = get_option('next_opencrates_number_for_cbu');
                }
                
                $voucherID = "OPCRT".$trf_to.$next_opencrates_number;
                $narration = "OpenCrates 20".$trf_to;
                if($trf_crates== "Y"){
                    foreach($credit_crates as $value11){
                        if($value7["AccountID"]==$value11["AccountID"]){
                            $creditcrates = $value11["credit_crates"];
                        }
                    }
                    foreach($debit_crates as $value12){
                        if($value7["AccountID"]==$value12["AccountID"]){
                            $debitcrates = $value12["debit_crates"];
                        }
                    }
                    $balance_crates = $debitcrates - $creditcrates;
                    if($balance_crates <= 0){
                        $ttype= "C";
                    }else{
                        $ttype= "D";
                    }
                    
                    $insert_array = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$trf_to,
                        'VoucherID'=>$voucherID,
                        'Transdate'=>date('Y-m-d H:i:s'),
                        'TransDate2'=>date('Y-m-d H:i:s'),
                        'TType'=>$ttype,
                        'Narration'=>$narration,
                        'Ordinalno'=>1,
                        'UserID'=>$this->session->userdata('username'),
                        'AccountID' =>$value7['AccountID'],
                        'PassedFrom' =>'OPENCRATES',
                        'Qty' =>$balance_crates,
                    );
                }else{
                    $ttype = "C";
                    $insert_array = array(
                        'PlantID'=>$selected_company,
                        'FY'=>$trf_to,
                        'VoucherID'=>$voucherID,
                        'Transdate'=>date('Y-m-d H:i:s'),
                        'TransDate2'=>date('Y-m-d H:i:s'),
                        'TType'=>$ttype,
                        'Narration'=>$narration,
                        'Ordinalno'=>1,
                        'UserID'=>$this->session->userdata('username'),
                        'AccountID' =>$value7['AccountID'],
                        'PassedFrom' =>'OPENCRATES',
                        'Qty' =>$balance_crates,
                    );
                }
            $this->db->insert(db_prefix() . 'accountcrates',$insert_array);
            $this->increment_next_number();
                //echo $value1['AccountID'] . " Insert Accounts";
            }
        }
        //force_download($file_name.'.zip', $backup);
        return true;
    }
//====================== Kirti ONe Year Transfer ===============================
    
    public function KirtiOne_transfer_year($data = '')
    {
        $selected_company = $this->session->userdata('root_company');
        $username = $this->session->userdata('username');
        $timestamp = date("Y-m-d H:i:s");
        
        $trf_from = $data["trf_from"];
        $trf_to = $data["trf_to"];
        $trf_accounts = $data["trf_accounts"];
        $trf_stock = $data["trf_stock"];
        
        // Item List
        $this->db->select('tblproduct.*');
        $this->db->where('tblproduct.ItemFor', 'KASPL');
        $this->db->order_by('tblproduct.ProductID', 'ASC');
        $AllItemList = $this->db->get(db_prefix() . 'product')->result_array();
        
        // All Center List
        $this->db->select('tblCenterMaster.*');
        $this->db->order_by('tblCenterMaster.CenterID', 'ASC');
        $AllCenterList = $this->db->get(db_prefix() . 'CenterMaster')->result_array();
        
        // Opening Stock for From year
        $this->db->select('tblK1stockmaster.*');
        $this->db->where('tblK1stockmaster.PartyID', 'KASPL');
        $this->db->where(db_prefix() . 'K1stockmaster.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'K1stockmaster.FY', $trf_from);
        $this->db->group_by('tblK1stockmaster.ItemID,CenterID,BatchNo');
        $this->db->order_by('tblK1stockmaster.ItemID,CenterID', 'ASC');
        $AllItemOpnQty = $this->db->get(db_prefix() . 'K1stockmaster')->result_array();
        
        $this->db->select('CenterID,ItemID,BatchNo');
        $this->db->where('tblK1history.FY', $trf_from);
        $this->db->where('tblK1history.TransID IS NOT NULL');
        $this->db->where('tblK1history.BillID IS NOT NULL');
        $this->db->where('tblK1history.BatchNo IS NOT NULL');
        $this->db->group_by('CenterID,ItemID,BatchNo');
        $this->db->order_by('tblK1history.CenterID,tblK1history.ItemID', 'asc');
        $UnqueCombination = $this->db->get(db_prefix() . 'K1history')->result_array();
        
        //============== All Purchase List
        $this->db->select('TransID,TransDate,CenterID,ItemID,BatchNo,ExpDate,PurchRate');
        $this->db->where('tblK1history.TransID IS NOT NULL');
        $this->db->where('tblK1history.BillID IS NOT NULL');
        $this->db->where('tblK1history.BatchNo IS NOT NULL');
        $this->db->where('tblK1history.TType',"P");
        $this->db->group_by('TransID,CenterID,ItemID,BatchNo');
        $this->db->order_by('tblK1history.CenterID,tblK1history.ItemID', 'asc');
        $AllPurchase = $this->db->get(db_prefix() . 'K1history')->result_array();
        
        $temp = [];

        foreach (array_merge($AllItemOpnQty, $UnqueCombination) as $row) {
            $key = $row['CenterID'].'_'.$row['ItemID'].'_'.$row['BatchNo'];
            $temp[$key] = $row; // overwrite duplicates
        }
        
        $FinalUnqueComb = array_values($temp);
        
        
        $PurchIDMap = [];
        $TransDateMap = [];
        $ExpMap = [];
        $PurchRate = [];
        foreach ($AllPurchase as $row) {
            $key = $row['CenterID'] . '_' . $row['ItemID'] . '_' . $row['BatchNo'];
            $PurchIDMap[$key] = $row['TransID'];
            $TransDateMap[$key] = $row['TransDate'];
            $ExpMap[$key] = $row['ExpDate'];
            $PurchRate[$key] = $row['PurchRate'];
            
        }
        
        foreach ($FinalUnqueComb as $key => $val) {
            $mapKey = $val['CenterID'] . '_' . $val['ItemID'] . '_' . $val['BatchNo'];
        
            $FinalUnqueComb[$key]['PurchID'] = isset($PurchIDMap[$mapKey]) ? $PurchIDMap[$mapKey] : NULL;
            $FinalUnqueComb[$key]['PurchDate'] = isset($TransDateMap[$mapKey]) ? $TransDateMap[$mapKey] : NULL;
            $FinalUnqueComb[$key]['ExpDate'] = isset($ExpMap[$mapKey]) ? $ExpMap[$mapKey] : NULL;
            $FinalUnqueComb[$key]['PurchRate'] = isset($PurchRate[$mapKey]) ? $PurchRate[$mapKey] : NULL;
        }
        
        
        $opnQtyMap = [];
        foreach ($AllItemOpnQty as $row) {
            $key = $row['CenterID'] . '_' . $row['ItemID'] . '_' . $row['BatchNo'];
            $opnQtyMap[$key] = $row['OQty'];
        }
        
        foreach ($FinalUnqueComb as $key => $val) {
            $mapKey = $val['CenterID'] . '_' . $val['ItemID'] . '_' . $val['BatchNo'];
            $FinalUnqueComb[$key]['OQty'] = isset($opnQtyMap[$mapKey]) ? $opnQtyMap[$mapKey] : 0;
            $FinalUnqueComb[$key]['BalQty'] = isset($opnQtyMap[$mapKey]) ? $opnQtyMap[$mapKey] : 0;
        }
        
        
        $this->db->select("
            h.BatchNo, h.ItemID,
            MAX(h.CenterID) AS CenterID,
            (
                SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END)
                + COALESCE(MAX(sm.OQty),0)
                - SUM(CASE WHEN h.TType='P' AND h.TType2='PURCHASE RETURN' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='O' AND h.TType2='SALE' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='SR' AND h.TType2='FRESH RETURN' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='T' AND h.TType2='IN' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='T' AND h.TType2='OUT' THEN h.BilledQty ELSE 0 END)
                + SUM(CASE WHEN h.TType='I' AND h.TType2='INWARD' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='L' AND h.TType2='LIENMARK' THEN h.BilledQty ELSE 0 END)
                - SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END)
            ) AS AvailableQty", false);
        
        
        $this->db->from('tblK1history h');
        $this->db->join('tblK1stockmaster sm', 'sm.BatchNo = h.BatchNo AND sm.ItemID = h.ItemID AND sm.CenterID = h.CenterID AND sm.FY='.$trf_from, 'left');
        $this->db->where(['h.BatchNo !=' => '', 'h.FY' => $trf_from]);
        $this->db->group_by(['h.CenterID', 'h.BatchNo', 'h.ItemID']);
        $this->db->having('AvailableQty >', 0);
        
        $query = $this->db->get();
        $TransactionBal = $query->result_array();
        
        $BalQtyMap = [];
        foreach ($TransactionBal as $row) {
            $key = $row['CenterID'] . '_' . $row['ItemID'] . '_' . $row['BatchNo'];
            $BalQtyMap[$key] = $row['AvailableQty'];
        }
        
        foreach ($FinalUnqueComb as $key => $val) {
            $mapKey = $val['CenterID'] . '_' . $val['ItemID'] . '_' . $val['BatchNo'];
            $FinalUnqueComb[$key]['BalQty'] = isset($BalQtyMap[$mapKey]) ? $BalQtyMap[$mapKey] : 0;
        }
        foreach ($FinalUnqueComb as &$row) {
            if (
                isset($row['PlantID']) && 
                isset($row['BalQty'], $row['OQty']) &&
                (float)$row['BalQty'] == 0 &&
                (float)$row['OQty'] > 0
            ) {
                $row['BalQty'] = $row['OQty'];
            }
        }
        unset($row); // important after reference foreach
        
        $FinalUnqueComb = array_filter($FinalUnqueComb, function ($row) {
            return isset($row['BalQty']) && (float)$row['BalQty'] != 0;
        });
        
        // batch insert update
        
        $sql = "
            INSERT INTO tblK1stockmaster
            (
                PlantID, FY, cnfid, ItemID, PurchID, PurchDate,
                PartyID, CenterID, BatchNo, ExpDate, GodownID,
                PurchRate, OQty, TransDate, UserID, Lupdate,
                UserID2
            ) VALUES
        ";
        $values = [];
        foreach ($FinalUnqueComb as $row) {
            $values[] = "(" .
                $selected_company . "," .
                "'".$trf_to . "'," .
                "1," .
                $this->db->escape($row['ItemID']) . "," .
                $this->db->escape($row['PurchID']) . "," .
                $this->db->escape($row['PurchDate']) . "," .
                "'KASPL'," .
                $this->db->escape($row['CenterID']) . "," .
                $this->db->escape($row['BatchNo']) . "," .
                $this->db->escape($row['ExpDate']) . "," .
                $this->db->escape($row['GodownID'] ?? null) . "," .
                $this->db->escape($row['PurchRate'] ?? null) . "," .
                $this->db->escape($row['BalQty']) . "," .
                "'".$timestamp . "'," .
                "'".$username . "'," .
                "'".$timestamp . "'," .
                "'".$username ."'
                )";
        }
        $sql .= implode(",", $values);
        $sql .= "
            ON DUPLICATE KEY UPDATE
                PurchID = VALUES(PurchID),
                PurchDate = VALUES(PurchDate),
                ExpDate = VALUES(ExpDate),
                GodownID = VALUES(GodownID),
                PurchRate = VALUES(PurchRate),
                OQty = VALUES(OQty),
                TransDate = VALUES(TransDate),
                UserID = VALUES(UserID),
                Lupdate = VALUES(Lupdate),
                UserID2 = VALUES(UserID2)
        ";
        // echo $sql;
        // die;
        // $this->db->query($sql);
        // batch insert update
        
        echo "<pre>";
        print_r($FinalUnqueComb);
        die;
        
        
        // All Plant List
        $this->db->select( db_prefix() . 'PlantMaster.*');
        $this->db->order_by('PlantID', 'ASC');
        $AllPlantList = $this->db->get(db_prefix() . 'PlantMaster')->result_array();
        
        $this->db->select( db_prefix() . 'accountgroups.*,'.db_prefix() . 'accountgroupssub1.*,'.db_prefix() . 'clients.AccountID');
        $this->db->join(db_prefix() . 'accountgroupssub1', db_prefix() . 'accountgroupssub1.ActGroupID = '.db_prefix() . 'accountgroups.ActGroupID ');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'accountgroupssub1.SubActGroupID1 = '.db_prefix() . 'clients.SubActGroupID1 AND '.db_prefix() . 'clients.PlantID = '.$selected_company);
        $this->db->where(db_prefix() . 'accountgroups.ActGroupMovementID !=', 'B');
        $this->db->order_by('ActGroupName', 'asc');
        $TransferGroup = $this->db->get(db_prefix() . 'accountgroups')->result_array();
        $NBal_trf_AccountID = array();
        foreach ($TransferGroup as $key11 => $value11) {
            array_push($NBal_trf_AccountID, trim(strtoupper($value11['AccountID'])));
        }
        
        // From Account List Balances
        $this->db->select( db_prefix() . 'accountbalances.*');
        $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'accountbalances.FY', $trf_from);
        $this->db->group_by('AccountID,PartyID');
        $this->db->order_by('AccountID', 'asc');
        $AccountList_From = $this->db->get(db_prefix() . 'accountbalances')->result_array();
        
        // To Account List Balances
        $this->db->select( db_prefix() . 'accountbalances.*');
        $this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'accountbalances.FY', $trf_to);
        $this->db->group_by('AccountID,PartyID');
        $this->db->order_by('AccountID', 'asc');
        $AccountList_To = $this->db->get(db_prefix() . 'accountbalances')->result_array();
        
        // Fetch Company and Account Wise Credit debit amount
        // FROM Accounts Credit Balance 
        $trf_from_new = $trf_from + 1;
        $from_date = '20'.$trf_from.'-04-01';
        $to_date = '20'.$trf_from_new.'-03-31';
        
        // credit balance SUM
        $this->db->select('sum(Amount) as TotalAmt,AccountID,PartyID');
        $this->db->where('tblaccountledger.PlantID', $selected_company);
        $this->db->LIKE('tblaccountledger.TType', 'C');
        $this->db->LIKE('tblaccountledger.FY', $trf_from);
        $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
        $this->db->group_by('AccountID');
        $credit_bal = $this->db->get('tblaccountledger')->result_array();
        
        // Debit balance SUM
        $this->db->select('sum(Amount) as TotalAmt,AccountID,PartyID');
        $this->db->where('tblaccountledger.PlantID', $selected_company);
        $this->db->LIKE('tblaccountledger.TType', 'D');
        $this->db->LIKE('tblaccountledger.FY', $trf_from);
        $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
        $this->db->group_by('AccountID,PartyID');
        $debit_bal = $this->db->get('tblaccountledger')->result_array();
        
        // Fetch distinct accounts that have recorded transactions in the selected FROM financial year.
        $AccountList = array();
        foreach ($AccountList_From as $key1 => $value1) {
            if (!in_array($value1["AccountID"], $AccountList)) {
                $AccountList[] = $value1["AccountID"];
            }
        }
        foreach ($credit_bal as $key1 => $value1) {
            if (!in_array($value1["AccountID"], $AccountList)) {
                $AccountList[] = $value1["AccountID"];
            }
        }
        foreach ($debit_bal as $key1 => $value1) {
            if (!in_array($value1["AccountID"], $AccountList)) {
                $AccountList[] = $value1["AccountID"];
            }
        }
        
        $opnBalArr = array();
        foreach ($AccountList_From as $row) {
            $key = $row['AccountID'] . '_' . $row['PartyID'];
            $opnBalArr[$key] = $row['BAL1'];
        }
        
        $crArr = array();
        foreach ($credit_bal as $row) {
            $key = $row['AccountID'] . '_' . $row['PartyID'];
            $crArr[$key] = $row['TotalAmt'];
        }
        
        $drArr = array();
        foreach ($debit_bal as $row) {
            $key = $row['AccountID'] . '_' . $row['PartyID'];
            $drArr[$key] = $row['TotalAmt'];
        }
        $FinalArray = array();
        foreach ($AccountList as $AccountID) {
            foreach ($AllPlantList as $PlantVal) {
                $key = $AccountID . '_' . $PlantVal['PlantID'];
        
                $OpnBal = isset($opnBalArr[$key]) ? $opnBalArr[$key] : 0;
                $CRAmt  = isset($crArr[$key]) ? $crArr[$key] : 0;
                $DRAmt  = isset($drArr[$key]) ? $drArr[$key] : 0;
        
                $BalAmt = $OpnBal + $DRAmt - $CRAmt;
        
                if ($BalAmt != 0) {
                    $FinalArray[] = array(
                        "AccountID" => $AccountID,
                        "PartyID"   => $PlantVal["PlantID"],
                        "Balance"   => $BalAmt
                    );
                }
            }
        }
        
        
        
        
        // Prepare Account wise party wise balance 
        
        foreach($AccountList as $AccountID){
            foreach($AllPlantList as $PlantKey=>$PlantVal){
                $OpnBal = 0;$CRAmt = 0;$DRAmt = 0;$BalAmt = 0;
                foreach($AccountList_From as $ActOpnKey=>$ActOpnVal){
                    if($AccountID == $ActOpnVal["AccountID"] && $PlantVal["PlantID"] == $ActOpnVal["PartyID"]){
                        $OpnBal = $ActOpnVal["BAL1"];
                    }
                }
                
                foreach($credit_bal as $ActCRAmtKey=>$ActCRAmtVal){
                    if($AccountID == $ActCRAmtVal["AccountID"] && $PlantVal["PlantID"] == $ActCRAmtVal["PartyID"]){
                        $CRAmt = $ActCRAmtVal["TotalAmt"];
                    }
                }
                foreach($debit_bal as $ActDRAmtKey=>$ActDRAmtVal){
                    if($AccountID == $ActDRAmtVal["AccountID"] && $PlantVal["PlantID"] == $ActDRAmtVal["PartyID"]){
                        $DRAmt = $ActDRAmtVal["TotalAmt"];
                    }
                }
                $BalAmt = $OpnBal + $DRAmt - $CRAmt;
                if($BalAmt >0 || $BalAmt < 0){
                    $newArray = array(
                        "AccountID"=>$AccountID,
                        "PartyID"=>$PlantVal["PlantID"],
                        "Balance"=>$BalAmt
                    );
                    array_push($FinalArray,$newArray);
                }
            }
        }
        
        
        
        
    }

    public function increment_next_number()
    {
        // Update next OpenCrates number in settings
        
       $selected_company = $this->session->userdata('root_company');
            if($selected_company == 1){
                $this->db->where('name', 'next_opencrates_number_for_cspl');
                
            }elseif($selected_company == 2){
                $this->db->where('name', 'next_opencrates_number_for_cff');
               
            }elseif($selected_company == 3){
                $this->db->where('name', 'next_opencrates_number_for_cbu');
                
            }
        $this->db->set('value', 'value+1', false);
        $this->db->update(db_prefix() . 'options');
    }
    
}?>