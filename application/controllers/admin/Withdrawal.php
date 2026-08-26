<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Withdrawal extends AdminController
{
    public function __construct(){
		parent::__construct();
		$this->load->Model('Withdrawal_model');
        $this->load->model('sale_reports_model');
	}
	
	public function index()
	{
		if (!has_permission_new('Withdrawal_Booking', '', 'view')) {
			access_denied('customers');
		}
		$data['title'] = "Withdrawal Master";
		$data['warehouses'] = $this->Withdrawal_model->getWarehouseData();
		$data['CenterList'] = $this->Withdrawal_model->GetCenterList();
		$data['items'] = $this->Withdrawal_model->getItemsData();
		$data['customers'] = $this->Withdrawal_model->getCustomersData();
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
		$this->load->view('admin/withdrawal/withdrawal',$data);
	}
	
	public function GetWithdrawalBooking()
	{
	    if (!has_permission_new('Withdrawal_Booking', '', 'view')) {
			access_denied('customers');
		}
	    $data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'CenterID'  => $this->input->post('CenterID'),
			'CustomerType'  => $this->input->post('CustomerType'),
			'ItemID'  => $this->input->post('ItemID'),
			'IsApprove'  => $this->input->post('IsApprove'),
		);
		
		$WarehouseList = $this->Withdrawal_model->GetWithdrawalBookingDB($data);
		
		$html ='';
		$sr = 1;
		foreach($WarehouseList as $value){
			
			if(($value['IsApprove'] == 'N') && ($value['ClientApprove'] == 'Y')){
				$status = 'Rejected';
			}
			if(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y')){
				$status = 'Accepted';
			}
			if(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'N')){
				$status = 'Awaiting Client Approval';
			}
			if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'N')){
				$status = 'Awaiting Client Approval';
			}
			if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y')){
				$status = '--';
			}
			if($value['CustomerType'] == '1'){
				$CustomerType = 'Farmer';
			}
			if($value['CustomerType'] == '2'){
				$CustomerType = 'Broker';
			}
			if($value['CustomerType'] == '3'){
				$CustomerType = 'Trader';
			}
			if($value['CustomerType'] == '4'){
				$CustomerType = 'Corporate/Processor';
			}
			if($value["company"] == "" || $value["company"] == null){
				$AccountName = $value["firstname"]." ".$value["lastname"];
				}else{
				$AccountName = $value["company"];
			}
			$html.= '<tr class="GetDetails" data-id="'.$value["id"].'" >';
			$html.= '<td style="text-align:left;">'.$sr.'</td>';
			$html.= '<td style="text-align:left;">'.$value["BookingID"].'</td>';
			$html.= '<td style="text-align:left;">'._d(substr($value['TransDate'],0,10)).'</td>';
			$html.= '<td style="text-align:left;">'.$CustomerType.'</td>';
			$html.= '<td style="text-align:left;">'.$value["AccountID"].'</td>';
			$html.= '<td style="text-align:left;">'.$AccountName.'</td>';
			/*$html.= '<td style="text-align:left;">'.$value["WHID"].'</td>';
			$html.= '<td style="text-align:left;">'.$value["w_name"].'</td>';*/
			$html.= '<td style="text-align:left;">'.$value["CenterName"].'</td>';
			$html.= '<td style="text-align:left;">'.$value["ItemID"].'</td>';
			$html.= '<td style="text-align:left;">'.$value["ItemName"].'</td>';
			$html.= '<td style="text-align:left;">'.$value["quantity"].'</td>';
			$html.= '<td style="text-align:left;">'.$value["unit"].'</td>';
			$html.= '<td style="text-align:left;">'.$status.'</td>';
			if(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'Y')){
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptTrade("'.$value["BookingID"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Modify" onclick=modifyTrade("'.$value["BookingID"].'") style="padding:3px 6px;" class="btn btn-info"><i class="fa fa-pencil"></i></button></td>';
            }
            elseif(($value['IsApprove'] == 'NA') && ($value['ClientApprove'] == 'N')){
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=awaiting() style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
            }
            elseif(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'Y')){
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-check"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
            }
            elseif(($value['IsApprove'] == 'Y') && ($value['ClientApprove'] == 'N')){
                $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" style="margin-right:12px;padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-check"></i></button>
                <button title="Modify" style="padding:3px 6px;" class="btn btn-default" disabled><i class="fa fa-pencil"></i></button></td>';
            }
			$html.= '</tr>';
			$sr++;
		}
		
		echo $html;
	}
	
	public function getModalData()
	{
        $BookingID = $this->input->post('BookingID');
        $ModalData = $this->Withdrawal_model->getModalDataDb($BookingID);
        echo json_encode($ModalData);
    }
	
	public function AcceptTrade()
	{
	    if (!has_permission_new('WithdrawalTradePunch', '', 'create')) {
			access_denied('customers');
		}
        $data = array(
            'BookingID' => $this->input->post('BookingID'),
            'ApproveUserID' => $this->session->userdata('username'),
            'ApproveTime' => date('Y-m-d H:i:s'),
        );
        $result = $this->Withdrawal_model->AcceptTradeDb($data);
        echo json_encode($result);
    }
    
    public function ModifyTrade()
    {
        if (!has_permission_new('WithdrawalTradePunch', '', 'create')) {
			access_denied('customers');
		}
        $data = array(
            'BookingID' => $this->input->post('modal_BookingID'),
            'PaymentRemark' => $this->input->post('modal_payment'),
            'ClientApprove' => 'N',
            'IsApprove' => $this->input->post('modal_status'),
            'modify_date' => $this->input->post('modify_date'),
            'UserID2' => $this->session->userdata('username'),
            'Lupdate' => date('Y-m-d H:i:s'),
        );
        $result = $this->Withdrawal_model->ModifyTradeDb($data);
        echo json_encode($result);
    }
    
    public function export_dailywithdrawaltrader()
    {
        if (!has_permission_new('Withdrawal_Booking', '', 'export')) {
			access_denied('customers');
		}
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->sale_reports_model->get_company_detail();
            $ $data = array(
    			'from_date' => $this->input->post('from_date'),
    			'to_date'  => $this->input->post('to_date'),
    			'WHID'  => $this->input->post('w_id'),
    			'CustomerType'  => $this->input->post('CustomerType'),
    			'ItemID'  => $this->input->post('ItemID'),
    			'IsApprove'  => $this->input->post('IsApprove'),
			);
			
			$result = $this->Withdrawal_model->GetWithdrawalBookingDB($data);
			
            
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 13);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 13);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["BookingID"] =  'BookingID';
            $set_col_tk["Booking Date"] = 'BookingID';
            $set_col_tk["Party Type"] = 'Party Type';
            $set_col_tk["AccountID"] = 'AccountID';
            $set_col_tk["Party Name"] = 'Party Name';
            $set_col_tk["CenetrName"] =  'Center Name';
            $set_col_tk["ItemID"] = 'ItemID';
            $set_col_tk["Item Name"] = 'Item Name';
            $set_col_tk["Quantity"] = 'Quantity';
            $set_col_tk["Unit"] = 'Unit';
            $set_col_tk["Status"] = 'Status';
            $set_col_tk["Action"] = 'Action';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["BookingID"];
                $list_add[] = _d(substr($value['TransDate'],0,10));
                $list_add[] = $value["firstname"]." ".$value["lastname"];
                $list_add[] = $value["AccountID"];
                $list_add[] =$value["firstname"]." ".$value["lastname"];
                $list_add[] = $value["CenterName"];
                $list_add[] = $value["ItemID"];
                $list_add[] = $value["ItemName"];
                $list_add[] = $value["quantity"];
                $list_add[] = $value["unit"];
                $list_add[] = $value["IsApprove"];
                $list_add[] = $value["ClientApprove"];
    
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'WithdrawalTradeList.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }
        
//===================== Withdrawal Trade Punch =================================
	public function TradePunch()
	{ 
	    if (!has_permission_new('WithdrawalTradePunch', '', 'view')) {
			access_denied('WithdrawalTradePunch');
		}
		$data['title']  = "Withdrawal Trade Punch";
		$this->load->view('admin/withdrawal/TradePunch',$data);    
	}
//================== Get Client data and Stock Deposit Center List =============
	public function fetchClientData()
	{
		$AccountID = $this->input->post('AccountID');
		$TradeType = $this->input->post('TradeType');
		$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
		$result = $this->db->get(db_prefix() . 'clients')->row();
		
		if(!empty($result))
		{
			$this->db->select('tblCenterMaster.CenterName,tblGateMaster.*');
			$this->db->where(db_prefix() . 'GateMaster.AccountID', $AccountID);
			$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID','INNER');
			$CenterList = $this->db->get(db_prefix() . 'GateMaster')->result_array();
			
			$this->db->select('tblGateMaster.CenterID,tblstockInventory.ItemID,tblstockInventory.TType,SUM(tblstockInventory.Weight) As Weight');
			$this->db->join('tblGateMaster','tblGateMaster.BookingID = tblstockInventory.BookingID','INNER');
			$this->db->where(db_prefix() . 'stockInventory.AccountID', $AccountID);
			$this->db->group_by('tblGateMaster.CenterID,tblstockInventory.TType');
			$InventoryData = $this->db->get(db_prefix() . 'stockInventory')->result_array();
			$CenterArr = [];
			foreach($CenterList as $Center){
				$Deposit = 0;
				$Withdraw = 0;
				foreach($InventoryData as $each){
					if($Center['CenterID'] == $each['CenterID']){
						if($each['TType'] == $TradeType){
							$Deposit += $each['Weight'];
						}
						
						if($each['TType'] == 'W'){
							$Withdraw += $each['Weight'];
						}
					}
				}
				$stock = $Deposit - $Withdraw;
				if($stock > 0){
					$CenterArr[$Center['CenterID']] = $Center['CenterName'];
				}
			}
			$result->CenterList = $CenterArr;
		}
		echo json_encode($result);
	}
//====================== Get Deposit Items Against Account and Center ==========
	public function GetTradeAvailableItems()
	{
		$CenterID = $this->input->post('CenterID');
		$AccountID = $this->input->post('AccountID');
		$TradeType = $this->input->post('TradeType');
		
		$this->db->select('tblGateMaster.CenterID, tblstockInventory.ItemID, tblstockInventory.TType, SUM(tblstockInventory.Weight) as Weight');
		$this->db->join('tblGateMaster', 'tblGateMaster.BookingID = tblstockInventory.BookingID', 'INNER');
		$this->db->where(db_prefix() . 'GateMaster.CenterID', $CenterID);
		$this->db->where(db_prefix() . 'stockInventory.AccountID', $AccountID);
		$this->db->group_by('tblGateMaster.CenterID, tblstockInventory.ItemID, tblstockInventory.TType');
		$InventoryData = $this->db->get(db_prefix() . 'stockInventory')->result_array();
		
		$ItemStock = [];
		
		foreach ($InventoryData as $each) {
			$ItemID = $each['ItemID'];
			$Weight = floatval($each['Weight']);
			
			if (!isset($ItemStock[$ItemID])) {
				$ItemStock[$ItemID] = 0;
			}
			
			if ($each['TType'] == $TradeType) {
				$ItemStock[$ItemID] += $Weight;
				} elseif ($each['TType'] == 'W') {
				$ItemStock[$ItemID] -= $Weight;
			}
		}
		
		// Fetch item names for items having stock > 0
		$ItemIDsWithStock = [];
		foreach ($ItemStock as $ItemID => $stock) {
			if ($stock > 0) {
				$ItemIDsWithStock[] = $ItemID;
			}
		}
		// print_r($ItemIDsWithStock);die;
		$Item_Arr = [];
		
		if (!empty($ItemIDsWithStock)) {
			$this->db->select('ItemID, ItemName');
			$this->db->from(db_prefix() . 'items');
			$this->db->where_in('ItemID', $ItemIDsWithStock);
			$ItemsData = $this->db->get()->result_array();
			
			foreach ($ItemsData as $item) {
				$Item_Arr[$item['ItemID']] = $item['ItemName'];
			}
		}
		echo json_encode($Item_Arr);
	}
//===================== Get Deposite Trade Details =============================
	public function fetchTradeData()
	{
		$CenterID = $this->input->post('CenterID');
		$AccountID = $this->input->post('AccountID');
		$ItemID = $this->input->post('ItemID');
		$TradeType = $this->input->post('TradeType');
		if($TradeType == "T")
		{
		    $type="TW";
		}
		else if($TradeType == "D"){
		    $type="W";
		}else if($TradeType == "A"){
		    $type="AW";
		}
		$escaped_type = $this->db->escape($type);
		$this->db->select("tblstockInventory.BookingID,tblstockInventory.GateINID, tblstockInventory.ItemID, SUM(tblstockInventory.Weight) as DepositQty, IFNULL(W.WithdrawQty, 0) as WithdrawQty
		");
		$this->db->from(db_prefix() . 'stockInventory');
		$this->db->join('tblGateMaster', 'tblGateMaster.Gate_in_ID = tblstockInventory.GateINID', 'INNER');
		$this->db->join("(SELECT GateINID, ItemID, SUM(Weight) as WithdrawQty FROM " . db_prefix() . "stockInventory WHERE TType = $escaped_type GROUP BY GateINID, ItemID ) as W", "W.GateINID = tblstockInventory.GateINID AND W.ItemID = tblstockInventory.ItemID", 'LEFT');
		// Filters
		$this->db->where('tblGateMaster.CenterID', $CenterID);
		$this->db->where('tblstockInventory.AccountID', $AccountID);
		$this->db->where('tblstockInventory.ItemID', $ItemID);
		$this->db->where('tblstockInventory.TType', $TradeType);
		
		// Grouping
		$this->db->group_by('tblstockInventory.GateINID, tblstockInventory.BookingID, tblstockInventory.ItemID');
		
		// Get result
		$InventoryData = $this->db->get()->result_array();
		
		//echo "<pre>";print_r($InventoryData);die;
		$html = '';
		$sr= 1;
		foreach($InventoryData as $each){
			$availableQty = $each['DepositQty'] - $each['WithdrawQty'];
			if($availableQty > 0){
				$html .= "<tr>";
				$html .= "<td class='check'><input type='checkbox' name='".$each['GateINID']."'></td>";
				$html .= "<td><input type='hidden' class='form-control BookingID' value='".$each['BookingID']."'>".$each['BookingID']."</td>";
				$html .= "<td class='GateINID'>".$each['GateINID']."</td>";
				$html .= "<td>".$each['DepositQty']."</td>";
				$html .= "<td>".$each['WithdrawQty']."</td>";
				$html .= "<td>".$availableQty."</td>";
				$html .= "<td><input type='number' class='form-control issue_qty' max='" . $availableQty . "' step='0.01' data-avail='".$availableQty."' oninput='restrictQty(this)'></td>";
				$sr++;
			}
		}
		echo json_encode($html);
	}
//================== Save Withdrawal Trade =====================================
	public function SaveTradePunch()
	{
	    if (!has_permission_new('WithdrawalTradePunch', '', 'create')) {
			access_denied('WithdrawalTradePunch');
		}
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$TradeType = $this->input->post('TradeType');
		$AccountID = $this->input->post('AccountID');
		$CenterID = $this->input->post('CenterID');
		$ItemID = $this->input->post('ItemID');
		$selectedItems = json_decode($this->input->post('SelectedItems'), true);
		
		if (empty($AccountID) || empty($CenterID) || empty($ItemID) || empty($selectedItems)) {
			echo json_encode(['status' => false, 'message' => 'Missing required data']);
			return;
		}
		
		$PartyID = "KASPL";
        $new_Number = get_number($CenterID,'W');
        
        if($TradeType == "A")
        {
            $Type = "AW";
            $Prefix = "AW";
        }
        else if($TradeType == "T")
        {
            $Type = "TW";
            $Prefix = "TW";
        }else{
            $Type = "W";
            $Prefix = "W";
        }
        $TType2 = "Withdrawal";
        $IsApprove = "Y";
        $ApproveTime = date('Y-m-d H:i:s');
        $ApproveUserID = $this->session->userdata('username');
		
		$number = str_pad($new_Number, 3, '0', STR_PAD_LEFT);
		$NewbookingID = $CenterID.$Prefix.date('d').date('m').date('y').$number;
		
		$TotalQty = 0;
		foreach ($selectedItems as $item) {
			$TotalQty += $item['Qty'];
		}
		$Cropsale_data = array(
        "FY"=>$fy,
        "PlantID" => $selected_company,
        "BookingID"=>$NewbookingID,
        "PartyID"=>$PartyID,
        "TransDate"=> date('Y-m-d H:i:s'),
        "TType"=> $Type,
        "TType2"=> $TType2,
        "AccountID"=>$AccountID,
        "UserID"=>$this->session->userdata('username'),
        "BrokerID"=>$AccountID,
        "CenterID"=>$CenterID,
        "ItemID"=>$ItemID,
        "quantity"=>$TotalQty,
        "e_quantity"=>$TotalQty,
        "unit"=>'MT',
        "basic_rate"=>0,
        "Mastercurrentrate"=>0,
        "IsApprove"=>$IsApprove,
        "ApproveTime"=>$ApproveTime,
        "ApproveUserID"=>$ApproveUserID,
        "ClientApprove"=>"Y",
        "BrokerApproveTime"=>date('Y-m-d H:i:s'),
        "BrokerApprove"=>"Y"
		);
		$this->db->insert(db_prefix().'lead_master', $Cropsale_data);
		$insert_id = $this->db->insert_id();
		if($insert_id)
		{
			$this->increment_crop_sale_number($CenterID,'W');
			foreach ($selectedItems as $item) {
				$GateINID = $item['GateINID'];
				$Qty = $item['Qty'];
				$BookingID = $item['BookingID'];
				
				// Save trade logic here, for example:
				$insert = [
				'BookingID'  => $NewbookingID,
				'TradeID'  => $BookingID,
				'GateINID'   => $GateINID,
				'Qty'        => $Qty,
				'UserID'  => $this->session->userdata('username'),
				'Transdate'  => date('Y-m-d H:i:s'),
				];
				
				$this->db->insert('tblWithdrawalDetail', $insert);
			}
			
			echo json_encode(['status' => true, 'message' => 'Trade punched successfully']);
		}else{
			echo json_encode(['status' => false, 'message' => 'Something Went Wrong']);
		}
	}
//========================= Increment Withdrawal number ========================
	public function increment_crop_sale_number($CenterID,$TType)
	{
		$this->db->set('Number', 'Number+1', false);
		$this->db->WHERE('CenterID', $CenterID);
		$this->db->WHERE('TType', $TType);
		$this->db->update(db_prefix() . 'numberformat');
	}
}