<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Warehouse_model extends App_Model
	{
		public function getState()
		{
			$this->db->order_by('state_name', 'ASC');
			return $this->db->get('tblxx_statelist')->result_array();
		}
		
		public function getCenter()
		{
			$this->db->order_by('CenterName', 'ASC');
			return $this->db->get('tblCenterMaster')->result_array();
		}
		
		public function getStaffList(){
			return $this->db->get('tblstaff')->result_array();
		}
		
		public function getCity($state)
		{
			$this->db->where('state',$state);
			return $this->db->get('tbl_xx_city')->result_array();
		}
		
		public function getTalukaFromCity($city_id){
			$this->db->where('DistrictID', $city_id);
			return $this->db->get('tblTalukaMaster')->result_array();
		}
		
		public function get_warehouse_table_on_load_filter($postdata){
			
			if(($postdata['district'] != '') && ($postdata['commodity'] != '')){
				$this->db->where('commodity_name',$postdata['commodity']);
				$this->db->where('district',$postdata['district']);
			}
			if(($postdata['district'] == '') && ($postdata['commodity'] != '')){
				$this->db->where('commodity_name',$postdata['commodity']);
			}
			if(($postdata['commodity'] == '') && ($postdata['district'] != '')){
				$this->db->where('district',$postdata['district']);
			}
			
			return $this->db->get('tblwarehouse')->result_array();
		}
		
		public function getWarehouseData(){
			$this->db->order_by('tblwarehouse.w_id', 'ASC');
			return $this->db->get('tblwarehouse')->result_array();
		}
		
		public function getWarehouseSizeData(){
			$this->db->select('tblWHSizeMaster.*,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblWHSizeMaster.WHID');
			return $this->db->get('tblWHSizeMaster')->result_array();
		}
		
		public function fetchAllWarehouseChambers($whid){
			$this->db->select('tblWHSizeMaster.*,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblWHSizeMaster.WHID');
			$this->db->where('tblWHSizeMaster.WHID',$whid);
			return $this->db->get('tblWHSizeMaster')->result_array();
		}
		public function fetchChamberDetails($chid){
			$this->db->select('tblWHSizeMaster.*');
			$this->db->where('tblWHSizeMaster.CHID',$chid);
			return $this->db->get('tblWHSizeMaster')->row();
		}

        public function fetchstackListByChamberID($chid){
			$this->db->select('tblwhstackmaster.*');
			$this->db->where('tblwhstackmaster.CHID',$chid);
			return $this->db->get('tblwhstackmaster')->result_array();
		}
		
		public function getSingleWarehouseSizeData($AccountID){
			$this->db->select('tblWHSizeMaster.*,tblwarehouse.w_name');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.WHID = tblwarehouse.AccountID','LEFT');
			$this->db->where('tblWHSizeMaster.CHID',$AccountID);
			return $this->db->get('tblwarehouse')->row();
		}
		public function getSingleWarehouseSizeDatablur($AccountID){
			$this->db->select('tblWHSizeMaster.*,tblwarehouse.w_name');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.WHID = tblwarehouse.AccountID','LEFT');
			$this->db->where('AccountID',$AccountID);
			return $this->db->get('tblwarehouse')->row();
		}
		
		public function GetWarehouseBookingDb($data){
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$CustomerType = $data['CustomerType'];
			$WHID = $data['WHID'];
			$ItemID = $data['ItemID'];
			$IsApprove = $data['IsApprove'];
			
			$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblitems.PlantID,tblclients.CustomerType,tblclients.AccountID,tblclients.company,tblcontacts.AccountID,tblcontacts.firstname,tblcontacts.firstname,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
			$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.TType', 'D');
			if($data["from_date"] !== "" && $data["to_date"] !== ""){
				$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}
			if($data["CustomerType"] && $data["CustomerType"] !== ""){
				$this->db->where('tblclients.CustomerType', $CustomerType);
			}
			if($data["WHID"] && $data["WHID"] !== ""){
				$this->db->where('tbllead_master.WHID', $WHID);
			}
			if($data["ItemID"] && $data["ItemID"] !== ""){
				$this->db->where('tbllead_master.ItemID', $ItemID);
			}
			if($data["IsApprove"] && $data["IsApprove"] !== ""){
				if($data["IsApprove"] == 'NA'){
					$this->db->where('tbllead_master.IsApprove ','NA');
				}
				elseif($data["IsApprove"] == 'Y'){
			        $this->db->where('tbllead_master.IsApprove ','Y');
				}
				elseif($data["IsApprove"] == 'N'){
					$this->db->where('tbllead_master.IsApprove ','N');
				}
			}
			$this->db->order_by('tbllead_master.id','DESC');
			return $this->db->get('tbllead_master')->result_array();
		}
		
		public function CheckAsnLockDB($BookingID,$TType){
		    $this->db->where('BookingID',$BookingID);
    		if($TType == 'P'){
    		    $this->db->where('tblGateMaster.status <',4);
    		}
    		else if($TType == 'D'){
    		    $this->db->where('tblGateMaster.status <',4);
    		}
    		else if($TType == 'W'){
    		    $this->db->where('tblGateMaster.status <',8);
    		}
    	    $this->db->order_by('id','DESC');
    		$result = $this->db->get('tblGateMaster')->result_array();
    		return $result;
		}
		
		public function GetWarehouseBookingCustDB($data){
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$WHID = $data['WHID'];
			$ItemID = $data['ItemID'];

			$this->db->select('tblGateMaster.*,tblwarehouse.w_name,tblwhstackmaster.WHID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblGateMaster.StackID','left');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID','left');
			$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->where('tblGateMaster.TType', 'D');
			if($data["from_date"] !== "" && $data["to_date"] !== ""){
				$this->db->where('tblGateMaster.asn_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}
			
			if($data["WHID"] && $data["WHID"] !== ""){
				$this->db->where('tblwarehouse.AccountID', $WHID);
			}
			if($data["ItemID"] && $data["ItemID"] !== ""){
				$this->db->where('tblGateMaster.ItemID', $ItemID);
			}
			if($data["AccountID"] && $data["AccountID"] !== ""){
				$this->db->where('tblGateMaster.AccountID', $data['AccountID']);
			}
			$this->db->order_by('tblGateMaster.id','DESC');
			return $this->db->get('tblGateMaster')->result_array();
		}
		
		public function getCenterNameFromID($CenterID){
		    $this->db->where('CenterID',$CenterID);
		    return $this->db->get('tblCenterMaster')->row();
		}
		
		public function check_asn_lock($BookingID){
            $this->db->where('BookingID',$BookingID);
            $this->db->get('tblGateMaster')->result_array();
        }
		
		public function GetTraderequest($data)
        {
            $AccountID = $data["AccountID"];
            $from_date = to_sql_date($data["from_date"]);
            $to_date = to_sql_date($data["to_date"]);
            $center = $data["center"];
            $item = $data["item"];
			$Type = $data["Type"];
			$CustType = $this->session->userdata('AccountType');
            $this->db->select('tblGateMaster.*,tblCenterMaster.CenterName,tblCenterMaster.CenterID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname');
			if($CustType == "2"){
				$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.BrokerID');
				$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.BrokerID');
			}else{
				$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
				$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			}
			$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
			$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->where('tblGateMaster.TType', $Type);
			
    			if($data["from_date"] !== "" && $data["to_date"] !== ""){
    			    $this->db->where('tblGateMaster.asn_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
    			}
    			if($data["center"] && $data["center"] !== ""){
    			    $this->db->like('tblGateMaster.BookingID',$data["center"]);
    			}
    			if($data["item"] && $data["item"] !== ""){
    			    $this->db->where('tblGateMaster.ItemID',$data["item"]);
    			}
    			if($data["AccountID"] && $data["AccountID"] !== ""){
    			    $this->db->where('tblGateMaster.AccountID ',$data["AccountID"]);
    			}
    			$this->db->order_by('tblGateMaster.id','DESC');
    			return $this->db->get('tblGateMaster')->result_array();
        }
		
		public function GetBookingListCustDB($data){
		    $AccountID = $data["AccountID"];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$WHID = $data['WHID'];
			$CenterID = $data['CenterID'];
			$ItemID = $data['ItemID'];
			$BookingType = $data['BookingType'];
			$TType = $data['TType'];
			
			$this->db->select('tbllead_master.*,tblCenterMaster.CenterName,tblwarehouse.w_name,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','left');
			$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID','left');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
			
			if($data["WHID"] && $data["WHID"] !== ""){
				$this->db->where('tbllead_master.WHID', $WHID);
			}
			if($data["CenterID"] && $data["CenterID"] !== ""){
				$this->db->where('tbllead_master.CenterID', $CenterID);
			}
			if($data["from_date"] !== "" && $data["to_date"] !== ""){
				$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}
			if($data["ItemID"] && $data["ItemID"] !== ""){
				$this->db->where('tbllead_master.ItemID', $ItemID);
			}
			if($data["AccountID"] && $data["AccountID"] !== ""){
				$this->db->where('tbllead_master.AccountID', $data['AccountID']);
			}
			if($data["TType"] && $data["TType"] !== ""){
				$this->db->where('tbllead_master.TType', $TType);
			}
			if($data["BookingType"] && $data["BookingType"] !== ""){
				$this->db->where('tbllead_master.TType', $BookingType);
			}
			$this->db->order_by('tbllead_master.id','DESC');
			return $this->db->get('tbllead_master')->result_array();
		}
		
		public function GetWithdrawalBookingCustDB($data){
		    $AccountID = $data['AccountID'];
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$WHID = $data['WHID'];
			$ItemID = $data['ItemID'];
			
			$this->db->select('tblGateMaster.*,tblwarehouse.w_name,tblwhstackmaster.WHID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblGateMaster.StackID','left');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID','left');
			$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			
			if($data["from_date"] !== "" && $data["to_date"] !== ""){
				$this->db->where('tblGateMaster.asn_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}
			
			if($data["WHID"] && $data["WHID"] !== ""){
				$this->db->where('tblwarehouse.AccountID', $WHID);
			}
			if($data["ItemID"] && $data["ItemID"] !== ""){
				$this->db->where('tblGateMaster.ItemID', $ItemID);
			}
	
			if($data["AccountID"] && $data["AccountID"] !== ""){
				$this->db->where('tblGateMaster.AccountID', $AccountID);
			}
			$this->db->where('tblGateMaster.TType', 'W');
			$this->db->order_by('tblGateMaster.id','DESC');
			return $this->db->get('tblGateMaster')->result_array();
		}
		
		public function getWarehouseBookingDetails($id){
			$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname,tblclients.CustomerType,tblwarehouse.center,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
			$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.id',$id);
			$this->db->order_by('tbllead_master.id','DESC');
			return $this->db->get('tbllead_master')->row();
		}
		
		public function GetWarehouseBookingDetailsCustDB($BookingID){
			$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname,tblclients.CustomerType,tblwarehouse.center,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
			$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.BookingID',$BookingID);
			$this->db->order_by('tbllead_master.id','DESC');
			return $this->db->get('tbllead_master')->row();
		}
		
		public function GetBookingListDetailsDB($BookingID){
			$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.AccountID,tblclients.company,tblcontacts.firstname,tblcontacts.firstname,tblwarehouse.center,tblwarehouse.w_name');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','left');
			$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID AND tblclients.PlantID = tbllead_master.PlantID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID AND tblcontacts.PlantID = tbllead_master.PlantID');
			$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID AND tblitems.PlantID = tbllead_master.PlantID');
			$this->db->where('tbllead_master.BookingID',$BookingID);
			$this->db->order_by('tbllead_master.id','DESC');
			return $this->db->get('tbllead_master')->row();
		}
		
		public function GetAllBookingsDB($BookingID){
			$this->db->select('tblGateMaster.*');
			$this->db->where('tblGateMaster.BookingID',$BookingID);
			return $this->db->get('tblGateMaster')->result_array();
		}
		
		public function GateControlDetailsDB($Gate_in_ID){
		    $this->db->select('tblGateMaster.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
		    $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->where('tblGateMaster.Gate_in_ID',$Gate_in_ID);
			return $this->db->get('tblGateMaster')->row();
		}
		
		public function CropSellDetailsByASNDB($ASNID){
		    $this->db->select('tblGateMaster.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
		    $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID','left');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID','left');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->where('tblGateMaster.ASNID',$ASNID);
			return $this->db->get('tblGateMaster')->row();
		}
		
		public function getStaffNameFromId($UserID){
		    $this->db->select('tblstaff.*');
			$this->db->where('tblstaff.staffid',$UserID);
			return $this->db->get('tblstaff')->row();
		}
		
		public function getStaffNameFromAccountID($AccountID){
		    $this->db->select('tblstaff.*');
			$this->db->where('tblstaff.AccountID',$AccountID);
			return $this->db->get('tblstaff')->row();
		}
		
		public function WithdrawalOrderDetailsDB($Gate_in_ID){
		    $this->db->select('tblGateMaster.*,tblwarehouse.w_name,tblwhstackmaster.WHID,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
		    $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblGateMaster.StackID','left');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID','left');
			$this->db->where('tblGateMaster.Gate_in_ID',$Gate_in_ID);
			return $this->db->get('tblGateMaster')->row();
		}
		
		public function getWithdrawalQCDetails($Gate_in_ID){
			$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID','left');
			$this->db->where('Gate_in_ID', $Gate_in_ID);
			$this->db->where('TType', 'U');
			$this->db->where('layer_number', 1);
			return $this->db->get('tblQCParameterValues')->result_array();
		}
		
		public function DepositOrderDetailsDB($Gate_in_ID){
		    $this->db->select('tblGateMaster.*,tblwarehouse.w_name,tblwhstackmaster.WHID,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
		    $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblGateMaster.StackID');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID');
			$this->db->where('tblGateMaster.Gate_in_ID',$Gate_in_ID);
			return $this->db->get('tblGateMaster')->row();
		}
		
		public function GetWarehouseBookingDetailsByASNDB($ASNID){
		    $this->db->select('tblGateMaster.*,tblwarehouse.w_name,tblwhstackmaster.WHID,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
		    $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
			$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblGateMaster.StackID');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID');
			$this->db->where('tblGateMaster.ASNID',$ASNID);
			return $this->db->get('tblGateMaster')->row();
		}
		
		public function getFinalQCDetails($Gate_in_ID){
			$this->db->where('Gate_in_ID', $Gate_in_ID);
			$this->db->where('TType', 'F');
			return $this->db->get('tblQCParameterValues')->result_array();
		}
		
		public function getLayerDetails($Gate_in_ID){
		    $this->db->select('tblLayerMaster.*,tblstaff.firstname,tblstaff.lastname');
		    $this->db->join('tblstaff','tblstaff.staffid = tblLayerMaster.UserID','left');
			$this->db->where('tblLayerMaster.Gate_in_ID', $Gate_in_ID);
			$result = $this->db->get('tblLayerMaster')->result_array();
			$i = 0;
			foreach($result as $key=>$value){
				$this->db->select('tblQCParameterValues.ParameterValue,tblstaff.firstname,tblstaff.lastname,tblItemParameter.ItemParameterID,tblItemParameter.ItemParameterName,tblQCParameterValues.UserID,tblQCParameterValues.TransDate');
				$this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
				$this->db->join('tblstaff','tblstaff.staffid = tblQCParameterValues.UserID','left');
				$this->db->where('tblQCParameterValues.Gate_in_ID', $Gate_in_ID);
				$this->db->where('tblQCParameterValues.layer_number', $value['layer_number']);
				$parameter_detail = $this->db->get('tblQCParameterValues')->result_array();
				$result[$i]['parameter_detail'] = $parameter_detail;
				$i++;
			}
			return $result;
		}
		
		public function getPeripheralDetails($Gate_in_ID){
		    $this->db->select('tblQCParameterValues.*,tblstaff.firstname,tblstaff.lastname,tblItemParameter.ItemParameterName');
			$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID','left');
			$this->db->join('tblstaff','tblstaff.staffid = tblQCParameterValues.UserID','left');
			$this->db->where('Gate_in_ID', $Gate_in_ID);
			$this->db->where('TType', 'P');
			return $this->db->get('tblQCParameterValues')->result_array();
		}
		
		public function getPaymentCycle(){
			return $this->db->get('tblPaymentCycle')->result_array();
		}
		
		public function getLockingPeriod(){
			return $this->db->get('tblLocking')->result_array();
		}
		
		public function getModalDataDb($BookingID){
            $this->db->select('tbllead_master.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemName');
            $this->db->where('tbllead_master.BookingID',$BookingID);
            $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
            $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
            $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
            return $this->db->get('tbllead_master')->row();
        }
    
        public function AcceptTradeDb($data){
            $this->db->where('BookingID',$data['BookingID']);
            $this->db->set('IsApprove','Y');
            $this->db->set('BrokerApprove','Y');
            return $this->db->update('tbllead_master');
        }
        
        public function RejectTradeDb($data){
            $this->db->where('BookingID',$data['BookingID']);
            $this->db->set('IsApprove','N');
            return $this->db->update('tbllead_master');
        }
        
        public function ModifyTradeDb($data){
            $this->db->where('BookingID',$data['BookingID']);
            return $this->db->update('tbllead_master',$data);
        }
    
		public function getItemsData(){
			$this->db->select('tblitems.ItemID,tblitems.ItemName');
			return $this->db->get('tblitems')->result_array();
		}
		
		public function getCustomersData(){
			$this->db->select('tblCustomerType.id,tblCustomerType.Name');
			return $this->db->get('tblCustomerType')->result_array();
		}
		
		public function getSingleWarehouseData($AccountID){
			$this->db->where('AccountID',$AccountID);
			return $this->db->get(db_prefix() . 'warehouse')->row();
		}
		
		public function SaveWarehouseDetails($data){
			return $this->db->insert('tblwarehouse', $data);
		}
		
		public function UpdateWarehouseDetails($data){
			$this->db->where('AccountID',$data['AccountID']);
			return $this->db->update('tblwarehouse',$data);
		}
		
		
		
		public function filter_table_data($data){
			if(($data['city'] != null) && ($data['structure_type'] == null)){
				$this->db->where('tblwarehouse.city',$data['city']);
			}
			if(($data['city'] == null) && ($data['structure_type'] != null)){
				$this->db->where('tblwarehouse.structure',$data['structure_type']);
			}
			if(($data['city'] != null) && ($data['structure_type'] != null)){
				$this->db->where('tblwarehouse.city',$data['city']);
				$this->db->where('tblwarehouse.structure',$data['structure_type']);
			}
			
			$this->db->join('tblxx_statelist','tblxx_statelist.id = tblwarehouse.state');
			$this->db->join('tbl_xx_city','tbl_xx_city.id = tblwarehouse.city');
			return $this->db->get('tblwarehouse')->result_array();
		}
		
		public function getStateName($state_id){
			$this->db->where('id',$state_id);
			return $this->db->get('tblxx_statelist')->row();
		}
		
		public function getCityName($city_id){
			$this->db->where('id',$city_id);
			return $this->db->get('tbl_xx_city')->row();
		}
		
		public function UpdateWarehouseSizeDetails($data){
			$this->db->where('CHID',$data['CHID']);
			return $this->db->update('tblWHSizeMaster',$data);
		}
		
		public function SaveWarehouseSizeDetails($data){
			return $this->db->insert('tblWHSizeMaster', $data);
		}
		
		//===================================== Stack Plan Master ======================
		public function GetAllStack()
		{
			$this->db->select('tblwhstackmaster.*,tblwarehouse.w_name,tblWHSizeMaster.ChaumberName');
			$this->db->order_by(db_prefix() . 'whstackmaster.WHID', 'ASC');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tblwhstackmaster.CHID');
			return $this->db->get(db_prefix().'whstackmaster')->result_array();
		}
		
		public function fetchAllStackDetails($chid){
		    $this->db->where('tblwhstackmaster.CHID',$chid);
		    return $this->db->get(db_prefix().'whstackmaster')->result_array();
		}
        public function fetchAllStackDetails1($whid ,$chid){
			$this->db->select('tblwhstackmaster.*,tblwarehouse.w_name,tblWHSizeMaster.ChaumberName');
		    $this->db->where('tblwhstackmaster.WHID',$whid);
            $this->db->where('tblwhstackmaster.CHID',$chid);
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblwhstackmaster.WHID');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tblwhstackmaster.CHID');
		    return $this->db->get(db_prefix().'whstackmaster')->result_array();
		}
		
		public function fetchAllStackDetails2($whid ,$chid, $stckid){
			$this->db->select('tbllot_master.*,tblwhstackmaster.StackName,tblwarehouse.w_name,tblWHSizeMaster.ChaumberName');
		    $this->db->where('tbllot_master.WHID',$whid);
            $this->db->where('tbllot_master.CHID',$chid);
            $this->db->where('tbllot_master.StackID',$stckid);
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllot_master.WHID');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tbllot_master.CHID');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tbllot_master.StackID');
		    return $this->db->get(db_prefix().'lot_master')->result_array();
		}
		
		public function fetchStackDetails($stckID){
		    $this->db->where('tblwhstackmaster.StackID',$stckID);
		    return $this->db->get(db_prefix().'whstackmaster')->row();
		}
		
		public function GetAllWH()
		{
			$this->db->select('tblWHSizeMaster.*,tblwarehouse.w_name');
			$this->db->from(db_prefix() .'WHSizeMaster');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblWHSizeMaster.WHID');
			$WHList = $this->db->get()->result_array();
			return $WHList;
		}
		
		public function GetSingleStackPlan($StackID)
		{
			$this->db->select('tblwhstackmaster.*');
			$this->db->from(db_prefix() .'whstackmaster');
			$this->db->where('StackID', $StackID);
			$StackPlanDetails = $this->db->get()->row();
			if($StackPlanDetails){
				$this->db->select('SUM(tblwhstackmaster.total_area) AS TotalAllocated');
				$this->db->from(db_prefix() .'whstackmaster');
				$this->db->where('WHID', $StackPlanDetails->WHID);
				$AllocatedSpace = $this->db->get()->row();
				$StackPlanDetails->AllocatedSpace = $AllocatedSpace->TotalAllocated - $StackPlanDetails->total_area;
				
				$this->db->select('SUM(tblWHSizeMaster.utilize_area) AS TotalUtilizeArea');
				$this->db->from(db_prefix() .'WHSizeMaster');
				$this->db->where('WHID', $StackPlanDetails->WHID);
				$WHUtilizeSpace = $this->db->get()->row();
				$StackPlanDetails->WHUtilizeSpace = $WHUtilizeSpace->TotalUtilizeArea;
			}
			return $StackPlanDetails;
		}
		
		public function SaveWarehouseSizeStackDetails($data)
		{
			return $this->db->insert('tblwhstackmaster', $data);
		}	
		
		
		public function GetWarehouseStackSpace($WHID)
		{
			
			$this->db->select('IFNULL(SUM(tblwhstackmaster.total_area), 0) AS TotalAllocated');
			$this->db->from(db_prefix() .'whstackmaster');
			$this->db->where('WHID', $WHID);
			$AllocatedSpace = $this->db->get()->row();
			$StackPlanDetails->AllocatedSpace = $AllocatedSpace->TotalAllocated;
			
			$this->db->select('SUM(tblWHSizeMaster.utilize_area) AS TotalUtilizeArea');
			$this->db->from(db_prefix() .'WHSizeMaster');
			$this->db->where('WHID', $WHID);
			$WHUtilizeSpace = $this->db->get()->row();
			$StackPlanDetails->WHUtilizeSpace = $WHUtilizeSpace->TotalUtilizeArea;
			
			return $StackPlanDetails;
		}
		
		public function UpdateWarehouseSizeStackDetails($data)
		{
			$this->db->where('StackID',$data['StackID']);
			return $this->db->update('tblwhstackmaster',$data);
		}	
		
		public function GetWarehouseStackList($WHID)
		{
			
			$this->db->select('*');
			$this->db->from(db_prefix() .'whstackmaster');
			$this->db->where('WHID', $WHID);
			return $this->db->get()->result();
			
		}
		
		public function GetStackLotSpace($StackID)
		{
			
			$this->db->select('IFNULL(SUM(tbllot_master.total_area), 0) AS TotalAllocated');
			$this->db->from(db_prefix() .'lot_master');
			$this->db->where('StackID', $StackID);
			$AllocatedSpace = $this->db->get()->row();
			$LotPlanDetails->AllocatedSpace = $AllocatedSpace->TotalAllocated;
			
			$this->db->select('SUM(tblwhstackmaster.utilize_area) AS TotalUtilizeArea');
			$this->db->from(db_prefix() .'whstackmaster');
			$this->db->where('StackID', $StackID);
			$WHUtilizeSpace = $this->db->get()->row();
			$LotPlanDetails->LotUtilizeSpace = $WHUtilizeSpace->TotalUtilizeArea;
			
			return $LotPlanDetails;
		}
		
		public function GetSingleLotPlan($LotID)
		{
			$this->db->select('tbllot_master.*,tblwhstackmaster.StackName');
			$this->db->from(db_prefix() .'lot_master');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tbllot_master.StackID');
			$this->db->where('LOTID', $LotID);
			$LotPlanDetails = $this->db->get()->row();
			if($LotPlanDetails){
				$this->db->select('SUM(tbllot_master.total_area) AS TotalAllocated');
				$this->db->from(db_prefix() .'lot_master');
				$this->db->where('StackID', $LotPlanDetails->StackID);
				$AllocatedSpace = $this->db->get()->row();
				$LotPlanDetails->AllocatedSpace = $AllocatedSpace->TotalAllocated - $LotPlanDetails->total_area;
				
				$this->db->select('SUM(tblwhstackmaster.utilize_area) AS TotalUtilizeArea');
				$this->db->from(db_prefix() .'whstackmaster');
				$this->db->where('StackID', $LotPlanDetails->StackID);
				$WHUtilizeSpace = $this->db->get()->row();
				$LotPlanDetails->WHUtilizeSpace = $WHUtilizeSpace->TotalUtilizeArea;
			}
			return $LotPlanDetails;
		}
		
		
		public function GetAllLot()
		{
			$this->db->select('lot_master.*,tblwhstackmaster.StackName,tblwarehouse.w_name,tblWHSizeMaster.ChaumberName');
			$this->db->from(db_prefix() .'lot_master');
			$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tbllot_master.StackID');
			$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tbllot_master.CHID');
			$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllot_master.WHID');
			return $this->db->get()->result_array();
			
			// $this->db->order_by(db_prefix() . 'lot_master.WHID', 'ASC');
			// return $this->db->get(db_prefix().'lot_master')->result_array();
		}
		
		public function UpdateStackLotPlan($data)
		{
			$this->db->where('LOTID',$data['LOTID']);
			return $this->db->update('tbllot_master',$data);
		}
		
		public function SaveLotDetails($data)
		{
			return $this->db->insert('tbllot_master', $data);
		}
	
	// Daily Deposit Trade
	
	public function GetCenterList()
    {
        $this->db->select('tblCenterMaster.*');
        $this->db->from(db_prefix() . 'CenterMaster');
        $this->db->order_by( db_prefix() .'CenterMaster.id','ASC');
        return $this->db->get()->result_array();
    
    }
    
    public function GetPaymentCycleList()
    {
        $this->db->select('tblPaymentCycle.CycleID,tblPaymentCycle.CycleName');
        $this->db->from(db_prefix() . 'PaymentCycle');
        $this->db->order_by( db_prefix() .'PaymentCycle.CycleName','ASC');
        return $this->db->get()->result_array();
    }
    public function GetDailyRequest_by_show_button($data)
      {

        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $account_type = $data["account_type"];
        $center = $data["center"];
        $IsApprove = $data["IsApprove"];
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $UserID = $this->session->userdata('username');
        //return $UserID;

        $GetAllCenter = $this->GetAllcenter_staff_wise($UserID);
        $GetAllItems = $this->GetAllItems_staff_wise($UserID);
        $centerIDs = array();
        foreach($GetAllCenter as $val){
            array_push($centerIDs,$val["CenterID"]);
        }
        $ItemIDs = array();
        foreach($GetAllItems as $val){
            array_push($ItemIDs,$val["ItemID"]);
        }
        $kirti_status = array('NA','Y');
        //return $centerIDs;
        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID');
			$this->db->from(db_prefix() . 'lead_master');
			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'lead_master.FY', $fy);
			$this->db->where('tbllead_master.TType', 'D');
			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');
			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');

			if($data["from_date"] !== "" && $data["to_date"] !== ""){
			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

			}

			if($data["POID"] && $data["POID"] !== ""){
			    $this->db->where(db_prefix() . 'lead_master.id ',$data["POID"]);
			}

			if($data["center"] && $data["center"] !== ""){
			    $this->db->where(db_prefix() . 'lead_master.CenterID ',$data["center"]);
			}else{
			    if(!is_admin()){
			        $this->db->where_in(db_prefix() . 'lead_master.CenterID ',$centerIDs);
			    }

			}

			if(!is_admin()){

			    $this->db->where_in(db_prefix() . 'lead_master.ItemID ',$ItemIDs);

			}
			if($data["IsApprove"] && $data["IsApprove"] !== ""){
			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);
			}else{
			    $this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);
			}
			$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');
			$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');

			if($account_type !== ""){
			    $this->db->where(db_prefix() . 'clients.CustomerType ',$account_type);

			}
			$this->db->order_by( db_prefix() .'lead_master.id','ASC');
		    return $this->db->get()->result_array();

    }

    public function reject_by_delay_broker_approval()
    {
        $Day_time = date('Y-m-d')." 00:00:00";
        $date_time = date('Y-m-d H:i:s');
        $new_date = date("Y-m-d H:i:s", strtotime($date_time) - 180);

        // Broker delay rejection

        $this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,
        tblclients.company,tblclients.fcm_token,tblcontacts.firstname,tblcontacts.lastname,
        BClients.fcm_token AS Bfcm_token');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');
		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');
		$this->db->where('IsApprove','NA');
        $this->db->where('ClientApprove','Y');
        $this->db->where('BrokerApprove','NA');
        $this->db->where(db_prefix() . 'lead_master.TransDate BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');
        $rejectAll =  $this->db->get('tbllead_master')->result_array();

        $this->db->where('IsApprove','NA');
        $this->db->where('ClientApprove','Y');
        $this->db->where('BrokerApprove','NA');
        $this->db->where(db_prefix() . 'lead_master.TransDate BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');
        $this->db->set('BrokerApprove','N');
        $this->db->set('BrokerApproveTime',$date_time);
        $this->db->set('LastActionName','Rejected , Broker Approval Delay..');
        if($this->db->update('tbllead_master')){
            $screen = "1";
            foreach($rejectAll as $key=>$val){
                if($val["BrokerID"] == $val["AccountID"]){
                    $title = "Trade Rejected by your approval delay";
                    $body = "Your BookingID : ".$val["BookingID"].' rejected by your delay in Approval';
                    $booking_id = $val["BookingID"];
                    $to = $val["fcm_token"];
                    $this->send_notification($title,$screen,$body,$booking_id,$to);

                }else{
                    if($val["company"]){
                        $partyName = $val["company"];
                    }else{
                        $partyName = $val["firstname"].' '.$val["lastname"];
                    }

                    // Send notification to broker 

                    $title = "Trade Rejected by your approval delay";
                    $body = "Your BookingID : ".$val["BookingID"].' for '.$partyName.' rejected by your delay in Approval ';
                    $booking_id = $val["BookingID"];
                    $to = $val["Bfcm_token"];
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                    // Send Notification to trader

                    $title = "Trade Rejected by broker approval delay";
                    $body = "Your BookingID : ".$val["BookingID"].' rejected by delay in Broker Approval';
                    $booking_id = $val["BookingID"];
                    $to = $val["fcm_token"];
                    $this->send_notification($title,$screen,$body,$booking_id,$to);

                }
            }
            return true;
        }
    }

    public function reject_by_delay_kirti_approval()
    {
        $Day_time = date('Y-m-d')." 00:00:00";
        $date_time = date('Y-m-d H:i:s');
        $new_date = date("Y-m-d H:i:s", strtotime($date_time) - 180);
        // Reject by kirti approval delay

        $this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,
        tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblclients.fcm_token,BClients.fcm_token AS Bfcm_token');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');
		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');

        $this->db->where('IsApprove','NA');
        $this->db->where('ClientApprove','Y');
        $this->db->where('BrokerApprove','Y');
        $this->db->where(db_prefix() . 'lead_master.BrokerApproveTime BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');
        $rejectAll =  $this->db->get('tbllead_master')->result_array();

        $this->db->where('IsApprove','NA');
        $this->db->where('ClientApprove','Y');
        $this->db->where('BrokerApprove','Y');
        $this->db->where(db_prefix() . 'lead_master.BrokerApproveTime BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');
        $this->db->set('IsApprove','N');
        $this->db->set('ApproveTime',$date_time);
        $this->db->set('LastActionName','Rejected , Kirti Approval Delay..');
        if($this->db->update('tbllead_master')){

            foreach($rejectAll as $key=>$val){
                // Notification for Trader/ Farmer

                $title = "Trade has been Rejected by admin";
                $screen = "1";
                $body = "Your BookingID : ".$val["BookingID"].' has been rejected by admin';
                $booking_id = $val["BookingID"];
                $to = $val["fcm_token"];
                $this->send_notification($title,$screen,$body,$booking_id,$to);
            // Notification For Broker 
                if($val["BrokerID"] != NULL && $val["BrokerID"] !="" && $val["BrokerID"] != $val["AccountID"]){
                    if($val["company"] == "" || $val["company"] == null){
                        $AccountName = $val["firstname"].' '.$val["lastname"];
                    }else{
                        $AccountName = $val["company"];
                    }
                    $title = "Trade has been Rejected by admin";
                    $screen = "1";
                    $body = "Trade has been rejected by admin against ".$val["BookingID"] ." for ".$AccountName;
                    $booking_id = $val["BookingID"];
                    $to = $val["Bfcm_token"];
                    $this->send_notification($title,$screen,$body,$booking_id,$to);
                }
            }
            return true;
        }

    }
    
    function send_notification($title,$screen,$body,$booking_id,$to)
    {
        $data_arrary = array(
            "title"=>$title,
            "screen"=>$screen,
            "body"=>$body,
            "booking_id"=>$booking_id
        );
        $post_data = array(
            "priority"=>"HIGH",
            "data"=>$data_arrary,
            "to"=>$to
        );
        $finel_data = json_encode($post_data);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $finel_data,
            CURLOPT_HTTPHEADER => array(
                    "authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",
                    "content-type: application/json"
                ),
            )
        );
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
       // return $response;
        
    }
    public function GetDailyDepositRequest($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        //$from_date = '2023-11-01';
        $to_date = to_sql_date($data["to_date"]);
        $IsApprove = $data["IsApprove"];
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $UserID = $this->session->userdata('username');
        //return $UserID;
        $kirti_status = array('NA','Y');
        //return $centerIDs;
        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,
            tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID,BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname');
			$this->db->from(db_prefix() . 'lead_master');
			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'lead_master.FY', $fy);
			$this->db->where('tbllead_master.TType', 'D');
			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');
			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');
			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');
			if($data["from_date"] !== "" && $data["to_date"] !== ""){
			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			}
			if(!is_admin()){

			    $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = tbllead_master.CenterID AND tblstaff_wise_center.AccountID = "'.$UserID.'"');
			    $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = tbllead_master.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');
			    //$this->db->where_in(db_prefix() . 'lead_master.CenterID ',$centerIDs);
            }
			if($data["IsApprove"] && $data["IsApprove"] !== ""){
			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);
			}else{
			    $this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);
			}
			$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');
			$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');
			$this->db->order_by( db_prefix() .'lead_master.id','ASC');
		    return $this->db->get()->result_array();

    }
}			