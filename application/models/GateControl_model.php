<?php
defined('BASEPATH') or exit('No direct script access allowed');
class GateControl_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	//======================== Generate ASN ========================================
	public function GenerateASN($data)
	{
		return $this->db->insert('tblGateMaster', $data);
	}
	//====================== Ganerate Gate IN Pass =================================
	public function UpdateGateControl($data,$BookingID,$ASNID)
	{
		$this->db->where('ASNID', $ASNID);
		$this->db->where('BookingID', $BookingID);
		return $this->db->update('tblGateMaster',$data);
	}
	//======================== Get Payment Mode ============================
	public function GetPaymentMode()
	{
		$selected_company = $this->session->userdata('root_company');
		$subgroup = array('1000017');
		$this->db->where('PlantID', $selected_company);
		$this->db->where_in('SubActGroupID',$subgroup);
		$this->db->order_by('company', 'ASC');
		$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
		return $accounts;
	}
	public function VillageDetails($zip)
	{
		$this->db->select('tblvillagedetails.*,tblxx_statelist.state_name,tblxx_statelist.short_name,tblTalukaMaster.TalukaName,tblTalukaMaster.id AS talukaID,tblxx_citylist.city_name,tblxx_citylist.id AS DistrictID');
		$this->db->where('tblvillagedetails.Pincode', $zip);
		$this->db->join('tblpin', 'tblpin.Pincode = tblvillagedetails.Pincode', 'left');
		$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblpin.State', 'left');
		$this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblpin.Taluka', 'left');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblpin.District', 'left');
		return $this->db->get('tblvillagedetails')->result_array();
	}
	public function getVillageInfo()
	{
		$this->db->order_by('tblvillagedetails.VillageName', 'ASC');
		return $this->db->get('tblvillagedetails')->result_array();
	}
	public function GateControlDetails($id,$GateInID)
	{
	$this->db->select('tblGateMaster.*,tblvillagedetails.Pincode,tblxx_statelist.state_name,tblxx_statelist.short_name,tblxx_citylist.id AS DistrictID,tblTalukaMaster.id AS talukaID,tblTalukaMaster.TalukaName,tblxx_citylist.city_name');
	$this->db->where('tblGateMaster.id', $id);
	$this->db->where('tblGateMaster.Gate_in_ID', $GateInID);
	$this->db->join('tblvillagedetails', 'tblvillagedetails.id = tblGateMaster.VillageID', 'left');
	$this->db->join('tblpin', 'tblpin.Pincode = tblvillagedetails.Pincode', 'left');
	$this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblpin.State', 'left');
	$this->db->join('tblTalukaMaster', 'tblTalukaMaster.id = tblpin.Taluka', 'left');
	$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblpin.District', 'left');
	$gateData = $this->db->get('tblGateMaster')->row_array();
	if (!empty($gateData['Pincode'])) {
		$this->db->select('*');
		$this->db->where('Pincode', $gateData['Pincode']);
		$villages = $this->db->get('tblvillagedetails')->result_array();
		$gateData['villages_with_same_pincode'] = $villages;
	} else {
		$gateData['villages_with_same_pincode'] = [];
	}
	return $gateData;
}
	public function AddNewVillage($pinid,$villageName,$stateid,$talukaid,$districtid,$GateINID,$id,$accid)
	{
	$username = $this->session->userdata('username');
	$insertvillage = array(
			'VisitDate'=>date('Y-m-d H:i:s'),
			'VillageName'=>$villageName,
			'Pincode'=>$pinid,
			'TalukaId'=>$talukaid,
			'DistrictId'=>$districtid,
			'StateId'=>$stateid,
			'AssignStaff'=>$username,
			'UserID'=>$username,
			'datecreated'=>date('Y-m-d H:i:s'),
		);
	$result = $this->db->insert('tblvillagedetails', $insertvillage);
	if($result)
	{
		$insertedVillageID = $this->db->insert_id();
		$updategatemaster = array(
				'VillageID'=>$insertedVillageID,
			);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->where('id', $id);
			$updateResult  = $this->db->update('tblGateMaster',$updategatemaster);
			$updateclient = array(
				'VillageID'=>$insertedVillageID,
				);
			$this->db->where('AccountID', $accid);
			$updateClient  = $this->db->update('tblclients',$updateclient);
			return $updateResult;
	}
		return false;
}
	public function UpdateVillage($GateINID,$id,$villageID,$accid)
	{
		$updategatecontrol = array(
				'VillageID'=>$villageID,
			);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->where('id', $id);
		$result = $this->db->update('tblGateMaster',$updategatecontrol);
		if($result)
		{
			$updateclient = array(
				'VillageID'=>$villageID,
			);
			$this->db->where('AccountID', $accid);
			$updateClient  = $this->db->update('tblclients',$updateclient);
			return $result;
		}
	}
	public function GetTrades_for_asn()
	{
		$this->db->select('tbllead_master.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemID,tblitems.ItemName,GateSummary.TotalAsnQty,InvSummary.TotalInOut');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
		$subQuery = "(SELECT BookingID, TType, SUM(Asn_WT_MT) AS TotalAsnQty FROM tblGateMaster GROUP BY BookingID, TType) AS GateSummary";
		$this->db->join($subQuery, 'GateSummary.BookingID = tbllead_master.BookingID AND GateSummary.TType = tbllead_master.TType', 'left');
		$subQuery1 = "(SELECT BookingID, TType, SUM(Weight) AS TotalInOut FROM tblstockInventory GROUP BY BookingID, TType) AS InvSummary";
		$this->db->join($subQuery1, 'InvSummary.BookingID = tbllead_master.BookingID AND InvSummary.TType = tbllead_master.TType', 'left');
		$this->db->where('tbllead_master.IsApprove', 'Y');
		$this->db->where('tbllead_master.ClientApprove', 'Y');
		$this->db->where('tbllead_master.BrokerApprove', 'Y');
		$this->db->where('tbllead_master.status', '1');
		$this->db->order_by('tbllead_master.id', 'DESC');
		return $this->db->get('tbllead_master')->result_array();
	}
	
	public function GetTrades_for_asn_ByAccountID($AccountID)
	{
		$this->db->select('tbllead_master.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblitems.ItemID,tblitems.ItemName,GateSummary.TotalAsnQty,InvSummary.TotalInOut');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
		$subQuery = "(SELECT BookingID, TType, SUM(Asn_WT_MT) AS TotalAsnQty FROM tblGateMaster GROUP BY BookingID, TType) AS GateSummary";
		$this->db->join($subQuery, 'GateSummary.BookingID = tbllead_master.BookingID AND GateSummary.TType = tbllead_master.TType', 'left');
		$subQuery1 = "(SELECT BookingID, TType, SUM(Weight) AS TotalInOut FROM tblstockInventory GROUP BY BookingID, TType) AS InvSummary";
		$this->db->join($subQuery1, 'InvSummary.BookingID = tbllead_master.BookingID AND InvSummary.TType = tbllead_master.TType', 'left');
		$this->db->where('tbllead_master.IsApprove', 'Y');
		$this->db->where('tbllead_master.AccountID', $AccountID);
		$this->db->where('tbllead_master.ClientApprove', 'Y');
		$this->db->where('tbllead_master.BrokerApprove', 'Y');
		$this->db->where('tbllead_master.status', '1');
		$this->db->order_by('tbllead_master.id', 'DESC');
		return $this->db->get('tbllead_master')->result_array();
	}

	public function GetAsnGeneratedList()
	{
		$this->db->select('tblGateMaster.*,tbllead_master.CenterID,tblitems.ItemID,tblitems.ItemName,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
		$this->db->where('tblGateMaster.status', '1');
		$this->db->where('tblGateMaster.Gate_in_ID IS NULL');
		$this->db->where('tblGateMaster.gate_in_date IS NULL');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->order_by('tblGateMaster.id', 'DESC');
		return $this->db->get('tblGateMaster')->result_array();
	}

	public function GetAsnGeneratedListByAccountID($AccountID)
	{
		$this->db->select('tblGateMaster.*,tbllead_master.CenterID,tblitems.ItemID,tblitems.ItemName,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
		$this->db->where('tblGateMaster.status', '1');
		$this->db->where('tblGateMaster.Gate_in_ID IS NULL');
		$this->db->where('tblGateMaster.gate_in_date IS NULL');
		$this->db->where('tblGateMaster.AccountID', $AccountID);
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->order_by('tblGateMaster.id', 'DESC');
		return $this->db->get('tblGateMaster')->result_array();
	}

	public function GetSingleASN($ASNID)
	{
		$this->db->select('tblGateMaster.*,tblordermaster.DOID,tbllead_master.CenterID,tbllead_master.quantity AS TradeQty,tbllead_master.e_quantity AS TradeEQty,tblitems.ItemID,tblitems.ItemName,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
		$this->db->where('tblGateMaster.ASNID', $ASNID);
		$this->db->join('tblordermaster', 'tblordermaster.ASNID = tblGateMaster.ASNID',"LEFT");
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
		$data = $this->db->get('tblGateMaster')->row();
		if($data){
			$this->db->select('tblwarehouse.*');
			$this->db->where('center', $data->CenterID);
			$WHList = $this->db->get('tblwarehouse')->result_array();
			$data->WHList = $WHList;
		}
		return $data;
	}
	public function getTrades()
	{
		$this->db->select('tbllead_master.*,tblwarehouse.center,tblitems.ItemID,tblitems.ItemName,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
		$this->db->where('tbllead_master.IsApprove', 'Y');
		$this->db->where('tbllead_master.ClientApprove', 'Y');
		$this->db->where('tbllead_master.BrokerApprove', 'Y');
		$this->db->where('tbllead_master.status', '1');
		//$this->db->join('tblGateMaster', 'tblGateMaster.BookingID = tbllead_master.BookingID',"LEFT");
		$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','left');
		$this->db->order_by('tbllead_master.id', 'DESC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function getParty(){
		return $this->db->get('tblCustomerType')->result_array();
	}
	public function centerwise_commoditywise_purchase()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$CenterName = array();
		$CenterID = array();
		$ddd = [];
		$GetPurchaseCenter = $this->GetPurchaseCenter();
		$GetPurchaseItem = $this->GetPurchaseItem();
		foreach($GetPurchaseCenter as $value){
			array_push($CenterName,$value['CenterName']);
			array_push($CenterID,$value['CenterID']);
		}
		foreach($GetPurchaseItem as $value){
			$CenterWiseData = $this->GetPurchaseWeightCenterWise($value['ItemID']);
			$chart_data1['name'] = $value['ItemID'];
			$datas = array();
			foreach($CenterID as $vv){
				$WT = 0;
				foreach($CenterWiseData as $val){
					if($vv == $val['CenterID']){
						$WT = (int) $val['NetWeight'];
					}
				}
				array_push($datas,$WT);
			}
			$chart_data1['data'] = $datas;
			array_push($ddd,$chart_data1);
		}
		$chart['categories'] = $CenterName;
		$chart['data'] = $ddd;
		return $chart;
	}
	public function centerwise_commoditywise_deposit()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$ddd = [];
		$CenterName = array('Akola','Latur','Nanded','Parbhani','Malegaon','Khamgaon','Dharmabad');
		$chart_data1['name'] = 'SO';
		$chart_data1['data'] = array(2200,2220,2190,2180,2100,2199,2140);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'CH';
		$chart_data1['data'] = array(1800,1720,1690,1580,1900,1299,1440);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'TO';
		$chart_data1['data'] = array(1150,1250,1320,1190,1410,1520,1603);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'BAJ';
		$chart_data1['data'] = array(1120,1100,950,890,910,1022,1150);
		array_push($ddd,$chart_data1);
		/*$CenterID = array();
			$GetPurchaseCenter = $this->GetPurchaseCenter();
			$GetPurchaseItem = $this->GetPurchaseItem();
			foreach($GetPurchaseCenter as $value){
			array_push($CenterName,$value['CenterName']);
			array_push($CenterID,$value['CenterID']);
			}
			foreach($GetPurchaseItem as $value){
			$CenterWiseData = $this->GetPurchaseWeightCenterWise($value['ItemID']);
			$chart_data1['name'] = $value['ItemID'];
			$datas = array();
			foreach($CenterID as $vv){
			$WT = 0;
			foreach($CenterWiseData as $val){
			if($vv == $val['CenterID']){
			$WT = (int) $val['NetWeight'];
			}
			}
			array_push($datas,$WT);
			}
			$chart_data1['data'] = $datas;
			array_push($ddd,$chart_data1);
		}*/
		$chart['categories'] = $CenterName;
		$chart['data'] = $ddd;
		return $chart;
	}
	public function centerwise_commoditywise_deposit_stock()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$ddd = [];
		$CenterName = array('Akola','Latur','Nanded','Parbhani','Malegaon','Khamgaon','Dharmabad');
		$chart_data1['name'] = 'SO';
		$chart_data1['data'] = array(2500,2420,2490,2380,2200,2180,2150);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'CH';
		$chart_data1['data'] = array(1810,1700,1690,1680,1850,1799,1640);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'TO';
		$chart_data1['data'] = array(1450,1510,1420,1390,1510,1520,1603);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'BAJ';
		$chart_data1['data'] = array(1420,1500,1250,1290,1210,1122,1150);
		array_push($ddd,$chart_data1);
		/*$CenterID = array();
			$GetPurchaseCenter = $this->GetPurchaseCenter();
			$GetPurchaseItem = $this->GetPurchaseItem();
			foreach($GetPurchaseCenter as $value){
			array_push($CenterName,$value['CenterName']);
			array_push($CenterID,$value['CenterID']);
			}
			foreach($GetPurchaseItem as $value){
			$CenterWiseData = $this->GetPurchaseWeightCenterWise($value['ItemID']);
			$chart_data1['name'] = $value['ItemID'];
			$datas = array();
			foreach($CenterID as $vv){
			$WT = 0;
			foreach($CenterWiseData as $val){
			if($vv == $val['CenterID']){
			$WT = (int) $val['NetWeight'];
			}
			}
			array_push($datas,$WT);
			}
			$chart_data1['data'] = $datas;
			array_push($ddd,$chart_data1);
		}*/
		$chart['categories'] = $CenterName;
		$chart['data'] = $ddd;
		return $chart;
	}
	public function centerwise_commoditywise_purchase_stock()
	{
		$months_report = $this->input->post('months_report');
		$custom_date_select = '';
		$ddd = [];
		$CenterName = array('Akola','Latur','Nanded','Parbhani','Malegaon','Khamgaon','Dharmabad');
		$chart_data1['name'] = 'SO';
		$chart_data1['data'] = array(1800,1720,1890,1780,1600,1580,1650);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'CH';
		$chart_data1['data'] = array(1110,1100,1190,1180,1150,1199,1140);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'TO';
		$chart_data1['data'] = array(1050,1010,1020,1090,1010,1020,1003);
		array_push($ddd,$chart_data1);
		$chart_data1['name'] = 'BAJ';
		$chart_data1['data'] = array(1020,1000,1050,1090,1010,1022,1050);
		array_push($ddd,$chart_data1);
		/*$CenterID = array();
			$GetPurchaseCenter = $this->GetPurchaseCenter();
			$GetPurchaseItem = $this->GetPurchaseItem();
			foreach($GetPurchaseCenter as $value){
			array_push($CenterName,$value['CenterName']);
			array_push($CenterID,$value['CenterID']);
			}
			foreach($GetPurchaseItem as $value){
			$CenterWiseData = $this->GetPurchaseWeightCenterWise($value['ItemID']);
			$chart_data1['name'] = $value['ItemID'];
			$datas = array();
			foreach($CenterID as $vv){
			$WT = 0;
			foreach($CenterWiseData as $val){
			if($vv == $val['CenterID']){
			$WT = (int) $val['NetWeight'];
			}
			}
			array_push($datas,$WT);
			}
			$chart_data1['data'] = $datas;
			array_push($ddd,$chart_data1);
		}*/
		$chart['categories'] = $CenterName;
		$chart['data'] = $ddd;
		return $chart;
	}
	public function TradeTypeCenterWiseReport($data)
	{
		$CenterID = $data['CenterID'];
		$BookingType = $data['BookingType'];
		$this->db->select('tblGateMaster.status,COUNT(tblGateMaster.id) TotalCount');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->where('tblGateMaster.TType',$BookingType);
		$this->db->where('tbllead_master.CenterID',$CenterID);
		$this->db->group_by('tblGateMaster.status');
		$this->db->order_by('tblGateMaster.status','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetPurchaseCenter(){
		$this->db->select('tblGateMaster.ItemID,tblCenterMaster.CenterName,tblCenterMaster.CenterID');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->where('tblGateMaster.status >= 9');
		$this->db->where('tblGateMaster.TType','P');
		$this->db->group_by('tbllead_master.CenterID');
		$this->db->order_by('tbllead_master.CenterID','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetPurchaseItem(){
		$this->db->select('tblGateMaster.ItemID');
		$this->db->where('tblGateMaster.status >= 9');
		$this->db->where('tblGateMaster.TType','P');
		$this->db->group_by('tblGateMaster.ItemID');
		$this->db->order_by('tblGateMaster.ItemID','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetPurchaseWeightCenterWise($ItemID)
	{
		$this->db->select('tbllead_master.CenterID,SUM(ROUND(tblGateMaster.LoadedWeight - tblGateMaster.TareWeight)) AS NetWeight');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->where('tblGateMaster.status >= 9');
		$this->db->where('tblGateMaster.TType','P');
		$this->db->where('tblGateMaster.ItemID',$ItemID);
		$this->db->group_by('tbllead_master.CenterID');
		$this->db->order_by('tbllead_master.CenterID','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function getItems(){
		$this->db->order_by('tblitems.ItemID', 'ASC');
		return $this->db->get('tblitems')->result_array();
	}
	public function getAllCenters(){
		return $this->db->get('tblCenterMaster')->result_array();
	}
	public function getAllPartys(){
		return $this->db->get('tblPlantMaster')->result_array();
	}
	public function GetCompanyList(){
		return $this->db->get('tblPlantMaster')->result_array();
	}
	//================= Get QC Parameter List ======================================
	public function GetQCParameter()
	{
		$Ids = array("1","2","3");
		$this->db->where_in('ItemParameterID',$Ids);
		return $this->db->get('tblItemParameter')->result_array();
	}
	//================= Get Other Deduction Item List ==============================
	public function GetOtherDeductionItems()
	{
		$Ids = array("12"); // DEDUCTION
		$this->db->select('tblitems.ItemID,tblitems.ItemName');
		$this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
		$this->db->where_in('tblitems_sub_groups.id',$Ids);
		return $this->db->get('tblitems')->result_array();
	}
	public function Update_GateControl_DetailsDB($data){
		$this->db->where('BookingID',$data['BookingID']);
		$this->db->set('quantity',$data['quantity']);
		$this->db->set('unit',$data['unit']);
		$result = $this->db->update('tblGateMaster');
		return $result;
	}
	public function getSingleTradeById($id)
	{
		$this->db->select('tblGateMaster.*, tbllead_master.CenterID, tblitems.ItemID, tblitems.ItemName, tblitems.cd_applicable, tblitems.cd_percentage, tblpurchasemaster.PurchID, tblCenterMaster.CenterName, tblclients.AccountID, tblclients.CustomerType, tblclients.company, tblclients.ShortCode, tblclients.KYCStatus, tblclients.ShortCode, tbltaxes.taxrate, tblsalesmaster.SalesID, tblsalesmaster.ChallanID, tblsalesmaster.sale_qty, tblsalesmaster.BillAmt, T1.pcsoft_doc_ref AS PcSoftTradeID, T2.pcsoft_doc_ref AS PcSoftASNID, T3.pcsoft_doc_ref AS PcSoftGateINID, T4.pcsoft_doc_ref AS PcSoftOrderID, tblcontacts.AccountID, tblcontacts.firstname, tblcontacts.lastname');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblGateMaster.CenterID',"LEFT");
		$this->db->join('tblsalesmaster', 'tblsalesmaster.OrderID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblpurchasemaster', 'tblpurchasemaster.TransID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblpcsoft_gic_number_referance AS T1', 'T1.GIC_Reference = tblGateMaster.BookingID',"LEFT");
		$this->db->join('tblpcsoft_gic_number_referance AS T2', 'T2.GIC_Reference = tblGateMaster.ASNID',"LEFT");
		$this->db->join('tblpcsoft_gic_number_referance AS T3', 'T3.GIC_Reference = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblpcsoft_gic_number_referance AS T4', 'T4.GIC_Reference = tblpurchasemaster.PurchID',"LEFT");
		//$this->db->join('tblloan_history', 'tblloan_history.BookingID = tblGateMaster.BookingID AND tblloan_history.GateINID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
		$this->db->where('tblGateMaster.id', $id);
		return $this->db->get('tblGateMaster')->row();
	}
	//==================== Get Staff List ==========================================
	public function GetAllStaffList()
	{
		$this->db->select('tblstaff.*');
		$this->db->where('tblstaff.admin', '0');
		$this->db->order_by('tblstaff.firstname');
		return $this->db->get('tblstaff')->result_array();
	}
	public function SendInwardToPcSoftCheck($GateINID)
	{
		$this->db->select('tblpcsoft_gic_number_referance.*');
		$this->db->where('tblpcsoft_gic_number_referance.GIC_Reference', $GateINID);
		return $this->db->get('tblpcsoft_gic_number_referance')->row();
	}
	public function GetControlDetailsByGateIN($GateINID)
	{
		$this->db->select('tblGateMaster.*,tblclients.AccountID,tblclients.company,tbltaxes.taxrate,tblcontacts.AccountID,tblcontacts.firstname,tblcontacts.lastname,tblclients.CustomerType');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID','left');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax','left');
		$this->db->where('tblGateMaster.Gate_in_ID', $GateINID);
		return $this->db->get('tblGateMaster')->row();
	}
	public function GetWithdrawalMasterByGateIN($GateINID)
	{
		$this->db->select('*');
		$this->db->where('tblwithdrawalmaster.TransID', $GateINID);
		return $this->db->get('tblwithdrawalmaster')->row();
	}
	public function FetchHistoryDetails($GateINID)
	{
		$this->db->select('tblhistory.*');
		$this->db->where('tblhistory.OrderID', $GateINID);
		return $this->db->get('tblhistory')->row();
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
	public function getFilterDataGateControlDB($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblGateMaster.*,tblitems.ItemName,tblclients.company,tblstaff.firstname,tblstaff.lastname,tblvillagedetails.VillageName');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblstaff','tblstaff.AccountID = tblGateMaster.FeildOfficer',"LEFT");
		$this->db->join('tblvillagedetails','tblvillagedetails.id = tblGateMaster.VillageID',"LEFT");
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tblGateMaster.gate_in_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['FeildOfficer'] != ''){
			$this->db->where('tblGateMaster.FeildOfficer',$data['FeildOfficer']);
		}
		if($data['TType'] != ''){
			$this->db->where('tblGateMaster.TType',$data['TType']);
		}
		if($data['ItemID'] != ''){
			$this->db->where('tblGateMaster.ItemID',$data['ItemID']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tblGateMaster.CenterID',$data['CenterID']);
		}
		if($data['villagename'] != ''){
			$this->db->where('tblGateMaster.VillageID',$data['villagename']);
		}
		$this->db->where('tblGateMaster.Gate_in_ID IS NOT NULL');
		$this->db->where('tblGateMaster.PlantID',$selected_company);
		$this->db->where('tblGateMaster.FY',$fy);
		$this->db->order_by('tblGateMaster.gate_in_date','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function getFilterDataAdvancePayment($data)
	{
	$from_date = to_sql_date($data['from_date']);
	$to_date = to_sql_date($data['to_date']);
	$this->db->select('tblAdvancePayment.*,tblclients.company,tblCenterMaster.CenterName');
	$this->db->join('tblclients','tblclients.AccountID = tblAdvancePayment.AccountID');
	$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblAdvancePayment.CenterID');
	if(($data['from_date'] != '') || ($data['to_date'] != '')){
		$this->db->where('tblAdvancePayment.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
	}
	if($data['CenterID'] != ''){
		$this->db->where('tblAdvancePayment.CenterID',$data['CenterID']);
	}
	if($data['PartyID'] != ''){
		$this->db->where('tblAdvancePayment.PartyID',$data['PartyID']);
	}
	$this->db->order_by('tblAdvancePayment.id','DESC');
	return $this->db->get('tblAdvancePayment')->result_array();
}
public function SendAdvancePaymentData($id)
{
	$this->db->select('tblAdvancePayment.*,tblclients.ShortCode');
	$this->db->join('tblclients','tblclients.AccountID = tblAdvancePayment.AccountID');
	$this->db->where('tblAdvancePayment.id',$id);
	return $this->db->get('tblAdvancePayment')->row();
}
	public function Ganerate_wr_details($wr_list)
	{
		//$gate_in_list = explode(",",$wr_list);
		if($wr_list){
			$this->db->select('tblGateMaster.*');
			$this->db->where_in('tblGateMaster.Gate_in_ID',$wr_list);
			$this->db->where('tblGateMaster.LoadedWeight IS NOT NULL');
			$this->db->where('tblGateMaster.TareWeight IS NOT NULL');
			$GateControlList = $this->db->get('tblGateMaster')->result_array();
		}
		return $GateControlList;
	}
	//=============== Get Kirti Purchase Payment List ==============================
	public function GetPendingPaymentList($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tblGateMaster.*,tbllead_master.TransDate AS BookingDate,tblitems.ItemName,tblCenterMaster.CenterName,tbltaxes.taxrate,
		tblPlantMaster.PlantName,tblclients.CustomerType,tblclients.company,tblcontacts.aadhaar_number,tblcontacts.Pan,tblBankDetails.ifsc,tblBankDetails.bankName,tblBankDetails.accountNumber,
		Aadhaar.state AS AState,Aadhaar.dist AS Adist,Aadhaar.subdist AS Asubdist,Aadhaar.po AS Apo,Aadhaar.vtc AS Avtc,
		Aadhaar.loc AS Aloc,Aadhaar.street AS Astreet,Aadhaar.house AS Ahouse,Aadhaar.pincode AS Apincode,GST.state AS GSTState,GST.address AS GSTAddress');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID','left');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID',"LEFT");
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID',"LEFT");
		$this->db->join('tblAadharDetails AS Aadhaar','Aadhaar.AccountID = tblGateMaster.AccountID AND Aadhaar.Type = "1"',"LEFT");
		$this->db->join('tblBankDetails','tblBankDetails.AccountID = tblGateMaster.AccountID AND tblBankDetails.IsPrimary = "1"',"LEFT");
		$this->db->join('tblGstRecord AS GST','GST.AccountID = tblGateMaster.AccountID AND GST.IsPrimary = "1"',"LEFT");
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax',"LEFT");
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->join('tblPlantMaster','tblPlantMaster.PlantID = tblGateMaster.PartyID','LEFT');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tblGateMaster.gate_in_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tblGateMaster.CenterID',$data['CenterID']);
		}
		if($data['purchase_for'] != ''){
			$this->db->where('tblGateMaster.PartyID',$data['purchase_for']);
		}
		$this->db->where('tblGateMaster.status >= 16');
		$this->db->where('tblGateMaster.status <= 17');
		//$this->db->where('tblclients.CustomerType',3);
		$this->db->where('tblGateMaster.TType','P');
		$this->db->order_by('id','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetPendingPaymentListForFarmer($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tblGateMaster.*,tblitems.ItemName,tblCenterMaster.CenterName,tbltaxes.taxrate,tblUnloadingMaster.total_bags,tblUnloadingMaster.total_katta,
		tblPlantMaster.PlantName,tblclients.CustomerType,tblclients.company,tblBankDetails.ifsc,tblBankDetails.bankName,tblBankDetails.accountNumber,tblAadharDetails.dist');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID','left');
		$this->db->join('tblUnloadingMaster','tblUnloadingMaster.BookingID = tblGateMaster.BookingID AND tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblBankDetails','tblBankDetails.AccountID = tblGateMaster.AccountID AND tblBankDetails.IsPrimary = "1"','LEFT');
		$this->db->join('tblAadharDetails','tblAadharDetails.AccountID = tblGateMaster.AccountID AND tblAadharDetails.Type = "1"','LEFT');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->join('tblPlantMaster','tblPlantMaster.PlantID = tblGateMaster.PartyID','LEFT');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tblGateMaster.gate_in_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tblGateMaster.CenterID',$data['CenterID']);
		}
		if($data['purchase_for'] != ''){
			$this->db->where('tblGateMaster.PartyID',$data['purchase_for']);
		}
		$this->db->where('tblGateMaster.status >= 10');
		$this->db->where('tblGateMaster.TType','P');
		$this->db->where('tblclients.CustomerType',1);
		$this->db->order_by('id','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetInvoiceList($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tblSettlement_invoice.*,tblclients.company,tblCenterMaster.CenterName,tblPlantMaster.PlantName');
		$this->db->join('tblclients','tblclients.AccountID = tblSettlement_invoice.AccountTo');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblSettlement_invoice.BookingID');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->join('tblPlantMaster', 'tblPlantMaster.PlantID = tblSettlement_invoice.AccountBy');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tblSettlement_invoice.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['invoice_by'] != ''){
			$this->db->where('tblSettlement_invoice.AccountBy',$data['invoice_by']);
		}
		if($data['invoice_to'] != ''){
			$this->db->where('tblSettlement_invoice.AccountTo',$data['invoice_to']);
		}
		if($data['service_type'] != ''){
			$this->db->where('tblSettlement_invoice.TransType',$data['service_type']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		$this->db->order_by('id','ASC');
		return $this->db->get('tblSettlement_invoice')->result_array();
	}
	public function GetAllInvoiceByCompany($from_date,$to_date)
	{
		$this->db->select('tblSettlement_invoice.AccountBy,tblPlantMaster.PlantName');
		$this->db->join('tblPlantMaster', 'tblPlantMaster.PlantID = tblSettlement_invoice.AccountBy');
		$this->db->where('tblSettlement_invoice.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		$this->db->order_by('tblSettlement_invoice.TransDate','ASC');
		$this->db->group_by('tblSettlement_invoice.AccountBy');
		return $this->db->get('tblSettlement_invoice')->result_array();
	}
	public function GetAllInvoiceToParty($from_date,$to_date)
	{
		$this->db->select('tblSettlement_invoice.AccountTo,tblclients.company');
		$this->db->join('tblclients', 'tblclients.AccountID = tblSettlement_invoice.AccountTo');
		$this->db->where('tblSettlement_invoice.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		$this->db->order_by('tblSettlement_invoice.TransDate','ASC');
		$this->db->group_by('tblSettlement_invoice.AccountTo');
		return $this->db->get('tblSettlement_invoice')->result_array();
	}
	public function GetInvoiceDetails($TransID)
	{
		$this->db->select('tblSettlement_invoice.*,tblclients.AccountID,tblclients.company,tblclients.CustomerType,tbllead_master.TransDate AS b_date,tblwarehouse.w_name,
		tblGstRecord.gstin,tblGstRecord.address as GstAddress,tblxx_statelist.state_name as GstState,
		tblAadharDetails.state as AState,tblAadharDetails.dist,tblAadharDetails.subdist,tblAadharDetails.po,tblAadharDetails.loc,tblAadharDetails.street,tblAadharDetails.house,tblAadharDetails.pincode,
		tblPlantMaster.PlantName,tblPlantMaster.address,tblPlantMaster.GstNo,tblPlantMaster.fssai_no');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblSettlement_invoice.BookingID');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tbllead_master.WHID','LEFT');
		$this->db->join('tblclients','tblclients.AccountID = tblSettlement_invoice.AccountTo');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblSettlement_invoice.AccountTo AND tblGstRecord.IsPrimary = "1"',"LEFT");
		$this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblGstRecord.state',"LEFT");
		$this->db->join('tblAadharDetails','tblAadharDetails.AccountID = tblSettlement_invoice.AccountTo AND tblAadharDetails.Type = "1"',"LEFT");
		$this->db->join('tblPlantMaster','tblPlantMaster.PlantID = tblSettlement_invoice.AccountBy');
		$this->db->where('tblSettlement_invoice.TransID', $TransID);
		$Invoice = $this->db->get('tblSettlement_invoice')->row();
		if($Invoice){
			$this->db->select('tblSettlement_invoice_details.*,tblitems.ItemID,tblitems.ItemName,tblitems.hsn_code,tbltaxes.taxrate');
			$this->db->join('tblitems','tblitems.ItemID = tblSettlement_invoice_details.ItemID');
			$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax','left');
			$this->db->where('tblSettlement_invoice_details.TransID', $TransID);
			$InvoiceDetails = $this->db->get('tblSettlement_invoice_details')->result_array();
			$Invoice->Details = $InvoiceDetails;
		}
		return $Invoice;
	}
	public function UpdateInvoiceStatus($Invoice_number)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$username = $this->session->userdata('username');
		$this->db->where('TransID',$Invoice_number);
		$this->db->set('IsPaid','Y');
		$this->db->update('tblSettlement_invoice');
		if($this->db->affected_rows() > 0){
			// Get Invoice details
			$this->db->select('tblSettlement_invoice.*,tbllead_master.ItemID,tbllead_master.CenterID');
			$this->db->join('tbllead_master','tbllead_master.BookingID = tblSettlement_invoice.BookingID');
			$this->db->where('tblSettlement_invoice.TransID', $Invoice_number);
			$Invoice = $this->db->get('tblSettlement_invoice')->row();
			if($Invoice){
				$narration = 'Being Payment received / '.$Invoice->BookingID.' against '.$Invoice_number;
				$next_receipt_number = get_option2('next_receipts_number_for_kirti',$fy);
				$credit_data = array(
				"FY"=>$fy,
				"PlantID" => $selected_company,
				"VoucherID" => $next_receipt_number,
				"Transdate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				"AccountID"=>$Invoice->AccountTo,
				"CenterID"=>$Invoice->CenterID,
				"CommodityID"=>$Invoice->ItemID,
				"EntryFor"=>3,
				"TType"=>"C",
				"Amount"=>$Invoice->InvoiceAmt,
				"Narration"=>$narration,
				"PassedFrom"=>"RECEIPTS",
				"OrdinalNo"=>"1",
				"UserID"=>$username
				);
				$this->db->insert(db_prefix().'accountledger', $credit_data);
				$debit_data = array(
				"FY"=>$fy,
				"PlantID" => $selected_company,
				"VoucherID" => $next_receipt_number,
				"Transdate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				"AccountID"=>"CASH",
				"CenterID"=>$Invoice->CenterID,
				"CommodityID"=>$Invoice->ItemID,
				"EntryFor"=>3,
				"TType"=>"D",
				"Amount"=>$Invoice->InvoiceAmt,
				"Narration"=>$narration,
				"PassedFrom"=>"RECEIPTS",
				"OrdinalNo"=>"2",
				"UserID"=>$username
				);
				$this->db->insert(db_prefix().'accountledger', $debit_data);
				$this->increment_next_receipts_number();
			}
			return true;
		}
		return false;
	}
	public function increment_next_receipts_number()
	{
		// Update next receipt number in settings
		$fy = $this->session->userdata('finacial_year');
		$this->db->where('name', 'next_receipts_number_for_kirti');
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $fy);
		$this->db->update(db_prefix() . 'options');
	}
	public function GetAllStockToParty()
	{
		$ttype = array("D","A","T");
		//$ttype = array("D");
		$this->db->select('tbllead_master.AccountID,tblclients.company');
		$this->db->join('tblclients', 'tblclients.AccountID = tbllead_master.AccountID');
		$this->db->where_in('tbllead_master.TType',$ttype);
		$this->db->where('tbllead_master.IsApprove',"Y");
		$this->db->order_by('tbllead_master.id','ASC');
		$this->db->group_by('tbllead_master.AccountID');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetOutstandingList($data)
	{
		$this->db->select('tbllead_master.*,tblclients.company,tblCenterMaster.CenterName,tblitems.ItemName,tblitems.subgroup_id,
		IFNULL(tblCharges.Rate,100) as StorageCharge,tblRateMaster.Rate as Current_rate,tblPaymentCycle.CycleDays,
		tblloan_history.Amount,tblloan_history.ROC,tblloan_history.loan_per,tblloan_history.TransDate as dis_date,tblloan_history.WRWeight,tblloan_history.WRValue');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
		$this->db->join('tblPaymentCycle','tblPaymentCycle.CycleID = tbllead_master.payment_cycle',"LEFT");
		$this->db->join('tblloan_history','tblloan_history.BookingID = tbllead_master.BookingID AND tblloan_history.status = "O"',"LEFT");
		$this->db->join('tblCharges','tblCharges.ItemID = tbllead_master.ItemID AND tblCharges.CenterID = tbllead_master.CenterID',"LEFT");
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->join('tblRateMaster','tblRateMaster.CenterID = tbllead_master.CenterID AND tblRateMaster.ItemID = tbllead_master.ItemID AND tblRateMaster.KeyID = "C01" AND tblRateMaster.Type = "T" AND tblRateMaster.IsActive = "Y"',"LEFT");
		if($data['outstanding_to'] != ''){
			$this->db->where('tbllead_master.AccountID',$data['outstanding_to']);
		}
		if($data['service_type'] != ''){
			$this->db->where('tbllead_master.TType',$data['service_type']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		$this->db->where('tbllead_master.IsApprove',"Y");
		$this->db->order_by('id','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetChargesList($data)
	{
		$this->db->select('tbllead_master.*,tblCenterMaster.CenterName,tblCenterMaster.state as CentState,tblCenterMaster.address as WhAddrs,tblitems.ItemName,tblitems.subgroup_id,
		IFNULL(tblCharges.Rate,100) as StorageCharge,tblRateMaster.Rate as Current_rate,tblPaymentCycle.CycleDays,tblLocking.LockDays');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
		$this->db->join('tblPaymentCycle','tblPaymentCycle.CycleID = tbllead_master.payment_cycle',"LEFT");
		$this->db->join('tblLocking','tblLocking.LockID = tbllead_master.locking_period',"LEFT");
		$this->db->join('tblCharges','tblCharges.ItemID = tbllead_master.ItemID AND tblCharges.CenterID = tbllead_master.CenterID',"LEFT");
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID',"LEFT");
		$this->db->join('tblRateMaster','tblRateMaster.CenterID = tbllead_master.CenterID AND tblRateMaster.ItemID = tbllead_master.ItemID AND tblRateMaster.KeyID = "C01" AND tblRateMaster.Type = "T" AND tblRateMaster.IsActive = "Y"',"LEFT");
		if($data['outstanding_to'] != ''){
			$this->db->where('tbllead_master.AccountID',$data['outstanding_to']);
		}
		if($data['service_type'] != ''){
			$this->db->where('tbllead_master.TType',$data['service_type']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		$this->db->where('tbllead_master.IsApprove',"Y");
		$this->db->order_by('id','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetMasterList($data)
	{
		$this->db->select('tblGateMaster.LoadedWeight,tblGateMaster.TareWeight,tblGateMaster.BookingID,tblGateMaster.Gate_in_ID,tblGateMaster.TType,tblGateMaster.AccountID');
		if($data['outstanding_to'] != ''){
			$this->db->where('tblGateMaster.AccountID',$data['outstanding_to']);
		}
		if($data['service_type'] != ''){
			$this->db->where('tblGateMaster.TType',$data['service_type']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tblGateMaster.CenterID',$data['CenterID']);
		}
		$this->db->where('tblGateMaster.LoadedWeight IS NOT NULL');
		$this->db->where('tblGateMaster.TareWeight IS NOT NULL');
		$this->db->order_by('tblGateMaster.id','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetFinanceInwardList($data)
	{
		$this->db->select('tblGateMaster.gate_in_date,tblGateMaster.LoadedWeight,tblGateMaster.TareWeight,tblGateMaster.BookingID,tblGateMaster.Gate_in_ID');
		$this->db->join('tblinward_status','tblinward_status.BookingID = tblGateMaster.BookingID AND tblinward_status.GateINID = tblGateMaster.Gate_in_ID');
		if($data['outstanding_to'] != ''){
			$this->db->where('tblGateMaster.AccountID',$data['outstanding_to']);
		}
		if($data['service_type'] != ''){
			$this->db->where('tblGateMaster.TType',$data['service_type']);
		}
		if($data['CenterID'] != ''){
			$this->db->where('tblGateMaster.CenterID',$data['CenterID']);
		}
		$this->db->where('tblGateMaster.LoadedWeight IS NOT NULL');
		$this->db->where('tblGateMaster.TareWeight IS NOT NULL');
		$this->db->order_by('tblGateMaster.id','ASC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetBookingListDetails($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tbllead_master.*,tblCenterMaster.CenterName,tblPlantMaster.PlantName,
		tblSettlement_invoice.InvoiceAmt,tblclients.company,Bdetails.company AS BrokerName,tblclients.CustomerType,
		tblitems.ItemName');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');
		$this->db->join('tblclients AS Bdetails','Bdetails.AccountID = tbllead_master.BrokerID');
		$this->db->join('tblPlantMaster','tblPlantMaster.PlantID = tbllead_master.PartyID','LEFT');
		$this->db->join('tblSettlement_invoice','tblSettlement_invoice.BookingID = tbllead_master.BookingID AND tblSettlement_invoice.TransType = "S"','LEFT');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		if($data['purchase_for'] != ''){
			$this->db->where('tbllead_master.PartyID',$data['purchase_for']);
		}
		if($data['TradeStatus'] == "1"){
			$status = array('1');
			}else if($data['TradeStatus'] == "2"){
			$status = array('2','3');
			}else{
			$status = array('2','3','1');
		}
		$this->db->where_in('tbllead_master.status',$status);
		$this->db->where_in('tbllead_master.IsApprove',"Y");
		$this->db->where('tbllead_master.TType','P');
		$this->db->order_by('TransDate','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetBookingListInwardDetails($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		$this->db->select('tbllead_master.BookingID,SUM(tblhistory.BilledQty) AS InwardQty');
		$this->db->join('tblhistory','tblhistory.BillID = tbllead_master.BookingID','LEFT');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		if($data['purchase_for'] != ''){
			$this->db->where('tbllead_master.PartyID',$data['purchase_for']);
		}
		if($data['TradeStatus'] == "1"){
			$status = array('1');
			}else if($data['TradeStatus'] == "2"){
			$status = array('2','3');
			}else{
			$status = array('2','3','1');
		}
		$this->db->where_in('tbllead_master.status',$status);
		$this->db->where_in('tbllead_master.IsApprove',"Y");
		$this->db->where('tbllead_master.TType','P');
		$this->db->group_by('tblhistory.BillID');
		$this->db->order_by('tbllead_master.TransDate','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetCenterWiseTradeQuantity($data)
	{
		$from_date = to_sql_date($data['from_date']);
		$to_date = to_sql_date($data['to_date']);
		if($data['TradeStatus'] == "1"){
			$status = array('1');
		}else if($data['TradeStatus'] == "2"){
			$status = array('2','3');
		}else{
			$status = array('2','3','1');
		}
		$this->db->select('tblCenterMaster.CenterID,tblCenterMaster.CenterName,SUM(tbllead_master.quantity) AS TotalTradeQty');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		if($data['ItemID'] != ''){
			$this->db->where('tbllead_master.ItemID',$data['ItemID']);
		}
		if($data['TradeType'] != ''){
			$this->db->where('tbllead_master.TType',$data['TradeType']);
		}
		$this->db->where_in('tbllead_master.status',$status);
		$this->db->where_in('tbllead_master.IsApprove',"Y");
		$this->db->group_by('tbllead_master.CenterID');
		$this->db->order_by('tblCenterMaster.CenterName','ASC');
		$result = $this->db->get('tbllead_master')->result_array();
		$this->db->select('tbllead_master.CenterID,SUM(tblhistory.BilledQty) AS TotalInwardQty');
		$this->db->join('tblhistory','tblhistory.BillID = tbllead_master.BookingID','LEFT');
		if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tbllead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		if($data['CenterID'] != ''){
			$this->db->where('tbllead_master.CenterID',$data['CenterID']);
		}
		if($data['ItemID'] != ''){
			$this->db->where('tbllead_master.ItemID',$data['ItemID']);
		}
		if($data['TradeType'] != ''){
			$this->db->where('tbllead_master.TType',$data['TradeType']);
		}
		$this->db->where_in('tbllead_master.status',$status);
		$this->db->where_in('tbllead_master.IsApprove',"Y");
		$this->db->group_by('tbllead_master.CenterID');
		$inwardRows = $this->db->get('tbllead_master')->result_array();
		$inwardMap = array();
		foreach($inwardRows as $row){
			$inwardMap[$row['CenterID']] = $row['TotalInwardQty'];
		}
		foreach($result as $key => $row){
			$result[$key]['TotalInwardQty'] = isset($inwardMap[$row['CenterID']]) ? $inwardMap[$row['CenterID']] : 0;
		}
		return $result;
	}
	//=================== Get Final QC Value and QC Deduction Amt ==================
	public function GetQCDetailsByGateIN($All_gate_in)
	{
		$this->db->select('tblQCParameterValues.*,tblstockInventory.Weight AS MTWeight,tblstockInventory.BagQty');
		$this->db->join('tblstockInventory', 'tblstockInventory.GateINID = tblQCParameterValues.Gate_in_ID AND tblstockInventory.QCID = tblQCParameterValues.layer_number');
		$this->db->where_in('tblQCParameterValues.Gate_in_ID',$All_gate_in);
		$this->db->where_in('tblQCParameterValues.TType','F');
		$this->db->order_by('Gate_in_ID','ASC');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	//=================== Get Max QC Layer for selected GateIN =====================
	public function GetMaxQCLayer($All_gate_in)
	{
		$this->db->select('MAX(tblQCParameterValues.layer_number) AS MaxLayer');
		$this->db->where_in('tblQCParameterValues.Gate_in_ID',$All_gate_in);
		$this->db->where_in('tblQCParameterValues.TType','F');
		$this->db->order_by('Gate_in_ID','ASC');
		return $this->db->get('tblQCParameterValues')->row();
	}
	//=================== Gate IN wise Other Deduction Amt ==================
	public function GetOtherDeductionGateINWise($All_gate_in)
	{
		$this->db->select('tblotherdeduction.*');
		$this->db->where_in('tblotherdeduction.GateINID',$All_gate_in);
		$this->db->order_by('GateINID','ASC');
		return $this->db->get('tblotherdeduction')->result_array();
	}
	//================== Get Bag Qty Gate IN Wise ==================================
	public function GetBagQtyGateInWise($All_gate_in)
	{
		$this->db->select('SUM(tblstockInventory.BagQty) AS TotalBagQty,tblstockInventory.GateINID,SUM(tblstockInventory.Weight) AS TotalWeightMT');
		$this->db->where_in('tblstockInventory.GateINID',$All_gate_in);
		$this->db->group_by('tblstockInventory.GateINID');
		$this->db->order_by('GateINID','ASC');
		return $this->db->get('tblstockInventory')->result_array();
	}
	public function GetPaymentListByGateIN($All_gate_in)
	{
		$this->db->select('tblpur_payments.*');
		$this->db->where_in('tblpur_payments.GateINID',$All_gate_in);
		$this->db->order_by('GateINID','ASC');
		return $this->db->get('tblpur_payments')->result_array();
	}
	public function GetPaymentSum($GateINID)
	{
		$this->db->select('SUM(tblpur_payments.Amount) AS PaidAmt');
		$this->db->where('tblpur_payments.GateINID',$GateINID);
		return $this->db->get('tblpur_payments')->row();
	}
	// public function getSingleTrade($BookingID)
	// {
	// 	$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblRateMaster.Rate AS CurrentRate');
	// 	$this->db->where('tbllead_master.BookingID', $BookingID);
	// 	$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
	// 	$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
	// 	$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
	// 	$this->db->join('tblRateMaster','tblRateMaster.ItemID = tblitems.ItemID AND tblRateMaster.CenterID = tbllead_master.CenterID AND tblRateMaster.KeyID = "C01" AND tblRateMaster.IsActive = "Y" AND tblRateMaster.Type = "F"',"LEFT");
	// 	$this->db->order_by('tbllead_master.id', 'ASC');
	// 	return $this->db->get('tbllead_master')->row();
	// }

	public function getSingleTrade($BookingID)
	{
    $sql = "
        SELECT
            lm.*,
            i.ItemID,
            i.ItemName,
            c.CustomerType,
            c.company,

            (
                SELECT ct.firstname
                FROM tblcontacts ct
                WHERE ct.AccountID = lm.AccountID
                ORDER BY ct.id ASC
                LIMIT 1
            ) AS firstname,

            (
                SELECT ct.lastname
                FROM tblcontacts ct
                WHERE ct.AccountID = lm.AccountID
                ORDER BY ct.id ASC
                LIMIT 1
            ) AS lastname,

            (
                SELECT rm.Rate
                FROM tblRateMaster rm
                WHERE rm.ItemID = lm.ItemID
                  AND rm.CenterID = lm.CenterID
                  AND rm.KeyID = 'C01'
                  AND rm.IsActive = 'Y'
                  AND rm.Type = 'F'
                LIMIT 1
            ) AS CurrentRate

        FROM tbllead_master lm

        JOIN tblitems i
            ON i.ItemID = lm.ItemID

        JOIN tblclients c
            ON c.AccountID = lm.AccountID

        WHERE lm.BookingID = ?

        LIMIT 1
    ";

    return $this->db->query($sql, [$BookingID])->row();
	}

	public function getPurchaseMasterDetails($GateInID)
	{
		$this->db->select('tblpurchasemaster.*,tblGstRecord.gstin');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblpurchasemaster.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');
		$this->db->where('tblpurchasemaster.TransID', $GateInID);
		return $this->db->get('tblpurchasemaster')->row();
	}
	public function getSingleGateControl($BookingID,$ID,$flag)
	{
		$this->db->select('tblGateMaster.*,tblitems.ItemID,tblitems.ItemName,tblitems.base_value,tblcontacts.firstname,tblcontacts.lastname,tblclients.company,tblclients.CustomerType,
		tbllead_master.TransDate AS BookingDate,tbllead_master.unit AS BUnit,tblwarehouse.w_name,tblxx_statelist.state_name,
		tblCenterMaster.CenterName,tblCenterMaster.address,tblCenterMaster.state,tblxx_citylist.city_name,tblTalukaMaster.TalukaName,
		tblUnloadingMaster.total_bags,tblUnloadingMaster.total_katta,tblGstRecord.gstin AS BuyerGSTIN');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID','left');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblGateMaster.AccountID AND tblGstRecord.IsPrimary = "1"','left');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID','left');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID','left');
		$this->db->join('tblUnloadingMaster','tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID','LEFT');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID AND tbllead_master.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblCenterMaster.state',"LEFT");
		$this->db->join('tblxx_citylist','tblxx_citylist.id = tblCenterMaster.city',"LEFT");
		$this->db->join('tblTalukaMaster','tblTalukaMaster.id = tblCenterMaster.taluka',"LEFT");
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblGateMaster.GodownID','LEFT');
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		if($flag == "1"){
			$this->db->where('tblGateMaster.ASNID', $ID);
			}else{
			$this->db->where('tblGateMaster.Gate_in_ID', $ID);
		}
		$result = $this->db->get('tblGateMaster')->result_array();
		return $result;
	}
	public function GetPartyDetails($BookingID,$GateIN)
	{
		$this->db->select('tblGateMaster.AccountID,tblclients.AccountID,tblcontacts.istcs,
		tblGstRecord.gstin AS BuyerGSTIN,tblGstRecord.state AS PartyState,tblGstRecord.state_code,tblGstRecord.business_name,tblGstRecord.address,
		tblxx_statelist.state_name,tblCenterMaster.CenterName,tblCenterMaster.address AS CenterAddress,tblCenterMaster.state,tblxx_citylist.city_name,tblTalukaMaster.TalukaName');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID',"LEFT");
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID',"LEFT");
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblGateMaster.AccountID AND tblGstRecord.IsPrimary = "1"',"LEFT");
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID',"LEFT");
		$this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblCenterMaster.state',"LEFT");
		$this->db->join('tblxx_citylist','tblxx_citylist.id = tblCenterMaster.city',"LEFT");
		$this->db->join('tblTalukaMaster','tblTalukaMaster.id = tblCenterMaster.taluka',"LEFT");
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where('tblGateMaster.Gate_in_ID', $GateIN);
		$result = $this->db->get('tblGateMaster')->row();
		return $result;
	}
	public function GetInvoiceItemDetails($BookingID,$GateIN)
	{
		$this->db->select('tblsalesmaster.*,tblchallanmaster.VehicleID,tblchallanmaster.DriverID,tblGateMaster.gate_in_date,tblGateMaster.BookingID');
		$this->db->join('tblchallanmaster','tblchallanmaster.ChallanID = tblsalesmaster.ChallanID',"LEFT");
		$this->db->join('tblGateMaster','tblGateMaster.Gate_in_ID = tblsalesmaster.OrderID',"LEFT");
		$this->db->where('tblsalesmaster.OrderID', $GateIN);
		$result = $this->db->get('tblsalesmaster')->row();
		if($result){
			$this->db->select('tblhistory.*,tblitems.ItemName,tblitems.hsn_code');
			$this->db->join('tblitems','tblitems.ItemID = tblhistory.ItemID',"LEFT");
			$this->db->where('tblhistory.OrderID', $GateIN);
			$ItemDetails = $this->db->get('tblhistory')->result_array();
			$result->ItemDetails = $ItemDetails;
		}
		return $result;
	}
	public function GetStackDetails($BookingID,$GateIN)
	{
		$this->db->select('tblstockInventory.Weight,tblstockInventory.BagQty,tblWHSizeMaster.CHID,tblWHSizeMaster.ChaumberName,
		tblwhstackmaster.StackID,tblwhstackmaster.StackName,tbllot_master.LOTID,tbllot_master.LotName');
		$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tblstockInventory.CHID');
		$this->db->join('tblwhstackmaster','tblwhstackmaster.StackID = tblstockInventory.StackID');
		$this->db->join('tbllot_master','tbllot_master.LOTID = tblstockInventory.LOTID');
		$this->db->where('tblstockInventory.BookingID', $BookingID);
		$this->db->where('tblstockInventory.GateINID', $GateIN);
		$result = $this->db->get('tblstockInventory')->result_array();
		$Chamber = array();
		$Stack = array();
		$Lot = array();
		$bags = 0;
		$wt_in_mt = 0;
		foreach($result as $key=>$val){
			array_push($Chamber,$val["ChaumberName"]);
			array_push($Stack,$val["StackName"]);
			array_push($Lot,$val["LotName"]);
			$bags += $val["BagQty"];
			$wt_in_mt += $val["Weight"];
		}
		$ChamberName = implode(',', array_unique($Chamber));
		$StackName = implode(',', array_unique($Stack));
		$LotName = implode(',', array_unique($Lot));
		$response->ChamberName = $ChamberName;
		$response->StackName = $StackName;
		$response->LotName = $LotName;
		$response->bags = $bags;
		$response->wt_in_mt = $wt_in_mt;
		return $response;
	}
	public function GetUnloadingDetails($BookingID,$GateIN)
	{
		$this->db->select('SUM(tblUnloadingMaster.total_bags) AS TotalBag,SUM(tblUnloadingMaster.total_katta) AS TotalKatta');
		$this->db->where('tblUnloadingMaster.BookingID', $BookingID);
		$this->db->where('tblUnloadingMaster.Gate_in_ID	', $GateIN);
		$result = $this->db->get('tblUnloadingMaster')->row();
		return $result;
	}
	public function GetItemWiseQCParameter($ItemID)
	{
		$this->db->select('tblItemQCParameter.*');
		$this->db->where('ItemID', $ItemID);
		$result = $this->db->get('tblItemQCParameter')->result_array();
		return $result;
	}
	public function getSingleFinalQc($BookingID,$GateINID)
	{
		$this->db->select('si.*, wh.w_id as warehouse_id, ch.id as chamber_id, lm.id as lot_id');
		$this->db->from('tblstockInventory si');
		$this->db->join('tblwarehouse wh','wh.AccountID = si.WHID');
		$this->db->join('tblWHSizeMaster ch','ch.CHID = si.CHID');
		$this->db->join('tbllot_master lm','lm.LOTID = si.LOTID');
		$this->db->where('si.BookingID', $BookingID);
		$this->db->where('si.GateINID', $GateINID);
		$QcDetailsLotWise = $this->db->get()->result_array();
		$i = 0;
		foreach($QcDetailsLotWise as $key=>$val){
			$this->db->select('tblItemParameter.ItemParameterID, tblItemParameter.ItemParameterName,tblItemParameter.pc_soft_parameter,tblQCParameterValues.ParameterValue,tblQCParameterValues.EParameterValue,
			tblQCParameterValues.HParameterValue,tblQCParameterValues.deductionAmt,tblQCParameterValues.ItemParameterID,tblItemQCParameter.BaseValue');
			$this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
			$this->db->join('tblItemQCParameter','tblItemQCParameter.ItemParameterID = tblQCParameterValues.ItemParameterID AND tblItemQCParameter.ItemID = tblQCParameterValues.ItemID');
			$this->db->where('BookingID', $BookingID);
			$this->db->where('Gate_in_ID', $GateINID);
			$this->db->where('layer_number', $val['QCID']);
			$this->db->where('TType', 'F');
			$this->db->order_by('layer_number', 'ASC');
			$QCresult = $this->db->get('tblQCParameterValues')->result_array();
			$QcDetailsLotWise[$i]["QCDetails"] = $QCresult;
			$i++;
		}
		return $QcDetailsLotWise;
	}
	public function GetOtherDeduction($BookingID,$GateINID)
	{
		$this->db->select('tblotherdeduction.*,tblitems.PCItemID');
		$this->db->join('tblitems','tblitems.ItemID = tblotherdeduction.ItemID');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('GateINID', $GateINID);
		$result = $this->db->get('tblotherdeduction')->result_array();
		return $result;
	}
	public function GetStckWiseBagList($BookingID,$GateINID)
	{
		$this->db->select('tblstockInventory.*');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('GateINID', $GateINID);
		$result = $this->db->get('tblstockInventory')->result_array();
		return $result;
	}
	public function getRootCompany()
	{
		$this->db->select('tblrootcompany.*');
		$result = $this->db->get('tblrootcompany')->row();
		return $result;
	}
	public function LoginCompany(){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->where('PlantID',$selected_company);
		$this->db->where('FY',$fy);
		$result = $this->db->get('tblsetup')->row();
		return $result;
	}
	/*public function GetInvoiceDetails($BookingID){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblSettlement_invoice.*');
		$this->db->where('BookingID',$BookingID);
		$this->db->where('TransType','C');
		$result = $this->db->get('tblSettlement_invoice')->row();
		return $result;
	}*/
	public function checkForAsnDb($BookingID){
		$status = array('1','2');
		$this->db->select('tblGateMaster.*,tbllead_master.CenterID');
		$this->db->join('tbllead_master','tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where_in('tblGateMaster.status', $status);
		$result = $this->db->get('tblGateMaster')->row();
		if($result){
			$this->db->select('tblwarehouse.*');
			$this->db->where('center', $result->CenterID);
			$WHList = $this->db->get('tblwarehouse')->result_array();
			$result->WHList = $WHList;
			if($result->status == "2"){
				$result->StackList = $this->GetStackList($result->GodownID);
				$result->LotList = $this->GetStackLotList($result->StackID);
			}
		}
		return $result;
	}
	public function getCenterIDDB($WHID){
		$this->db->where('AccountID', $WHID);
		return $this->db->get('tblwarehouse')->row();
	}
	public function UpdateBookingLot($data,$LOTID){
		if(!empty($LOTID))
		{
			$this->db->where('LOTID', $LOTID);
			return $this->db->update('tbllot_master',$data);
		}
		else
		{
			return;
		}
	}
	public function getVehicleDetailsDb($BookingID,$ASNID){
		$this->db->where('BookingID', $BookingID);
		$this->db->where('ASNID', $ASNID);
		return $this->db->get('tblGateMaster')->row();
	}
	public function SendVehicleDb($data){
		$this->db->where('BookingID', $data['BookingID']);
		$this->db->set('GodownID', $data['GodownID']);
		return $this->db->update('tblGateMaster');
	}
	public function markExitDB($BookingID,$id,$BookingType){
		$exit_date = date('Y-m-d H:i:s');
		$username = $this->session->userdata('username');
		$this->db->where('id', $id);
		$this->db->where('BookingID', $BookingID);
		$this->db->set('exit_date', $exit_date);
		$this->db->set('exit_by', $username);
		if($BookingType == "S"){
			$this->db->set('status', 10);
			}else{
			$this->db->set('status', 12);
		}
		return $this->db->update('tblGateMaster');
	}
	public function GetInwardDetail($BookingID,$ID,$GateINID)
	{
		$this->db->select('tblGateMaster.Gate_in_ID,tblwarehouse.SA_AccountID AS wh_pid,tblGateMaster.BookingID AS reservation_id,tblGateMaster.gate_in_date AS cis_date,
		tblwarehouse.address AS warehouse_address,tblCenterMaster.CenterName AS location_name,tblGateMaster.weigh_bridge_slip_no,tblWHSizeMaster.SA_ChamberID AS godown_id,
		tblWHSizeMaster.ChaumberName AS godown_number,tblclients.AccountID AS depositor_mobile_no,tblclients.company AS depositor_Name,tblcontacts.Pan AS depositor_PAN,
		tblcontacts.aadhaar_number AS depositor_Aadhaar,tblclients.CustomerType AS depositor_Type,tblitems_sub_groups.SA_GroupID AS com_id,tblitems.SA_ItemID AS variety_id,
		tblGateMaster.VehicleNo,tblGateMaster.LoadedWeight,tblGateMaster.TareWeight,tblGateMaster.basic_rate,tblUnloadingMaster.total_bags,tblGateMaster.ChamberID');
		$this->db->join('tblwarehouse','tblwarehouse.AccountID = tblGateMaster.GodownID');
		$this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->join('tblWHSizeMaster','tblWHSizeMaster.CHID = tblGateMaster.ChamberID',"LEFT");
		$this->db->join('tblUnloadingMaster','tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID AND tblclients.PlantID = tblGateMaster.PlantID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID AND tblcontacts.PlantID = tblGateMaster.PlantID');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID AND tblitems.PlantID = tblGateMaster.PlantID');
		$this->db->join('tblitems_sub_groups','tblitems_sub_groups.id = tblitems.subgroup_id');
		$this->db->where('tblGateMaster.id', $ID);
		$this->db->where('tblGateMaster.Gate_in_ID', $GateINID);
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$result = $this->db->get('tblGateMaster')->row();
		return $result;
	}
	public function GetQCDetailByGateINID($BookingID,$GateINID)
	{
		// $this->db->select('tblitems.SA_ItemID AS variety_id,tblitems.ItemName AS variety_name,tblitems_sub_groups.SA_GroupID AS fk_com_id,
		// tblItemParameter.SA_ParameterID AS qty_params_id,tblItemParameter.ItemParameterName as qty_params_name,tblQCParameterValues.HParameterValue as result,
		// tblItemQCParameter.MinValue as min,tblItemQCParameter.MaxValue as max,tblQCParameterValues.TransDate');
		$this->db->select('tblitems.SA_ItemID AS variety_id,tblitems.ItemName AS variety_name,tblItemParameter.SA_ParameterID AS qty_params_id,
		tblItemParameter.ItemParameterName as qty_params_name,tblQCParameterValues.HParameterValue as result,
		tblQCParameterValues.TransDate');
		$this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
		$this->db->join('tblItemQCParameter','tblItemQCParameter.ItemParameterID = tblQCParameterValues.ItemParameterID AND tblItemQCParameter.ItemID = tblQCParameterValues.ItemID');
		$this->db->join('tblitems','tblitems.ItemID = tblQCParameterValues.ItemID');
		$this->db->join('tblitems_sub_groups','tblitems_sub_groups.id = tblitems.subgroup_id');
		$this->db->where('tblQCParameterValues.Gate_in_ID', $GateINID);
		$this->db->where('tblQCParameterValues.BookingID', $BookingID);
		$this->db->where('tblQCParameterValues.TType', 'F');
		$result = $this->db->get('tblQCParameterValues')->result_array();
		$i = 0;
		foreach($result as $key=>$val){
			//  $result[$i]['min_market_rate'] = 0;
			//  $result[$i]['max_market_rate'] = 0;
			//  $result[$i]['grade'] = 0;
			//  $result[$i]['premium1'] = 0;
			//  $result[$i]['activation_status'] = 0;
			//  $result[$i]['premium2'] = 0;
			//  $result[$i]['premium3'] = 0;
			//  $result[$i]['premium4'] = 0;
			$result[$i]['is_imported'] = 1;
			$result[$i]['from1'] = 0;
			//  $result[$i]['to1'] = 0;
			//  $result[$i]['from2'] = 0;
			//  $result[$i]['to2'] = 0;
			//  $result[$i]['from3'] = 0;
			//  $result[$i]['to3'] = 0;
			//  $result[$i]['from4'] = 0;
			$result[$i]['to4'] = 0;
			//  $result[$i]['min_length'] = 0;
			//  $result[$i]['max_length'] = 0;
			//  $result[$i]['qrcode_link'] = 0;
			$i++;
		}
		return $result;
	}
	public function freezerecord_in_GateMaster($BookingID,$id)
	{
		$this->db->where('id', $id);
		$this->db->where('BookingID', $BookingID);
		$this->db->set('SAFreeze', "Y");
		$this->db->set('status', 13);
		return $this->db->update('tblGateMaster');
	}
	public function freezrecord_in_History($OrderID)
	{
		$this->db->where('OrderID', $OrderID);
		$this->db->set('SAFreeze', "Y");
		return $this->db->update('tblhistory');
	}
	public function freezrecord_in_purchasemaster($TransID)
	{
		$this->db->where('TransID', $TransID);
		$this->db->set('SAFreeze', "Y");
		return $this->db->update('tblpurchasemaster');
	}
	public function markExitDBKirtiSell($BookingID,$id){
		$exit_date = date('Y-m-d H:i:s');
		$username = $this->session->userdata('username');
		$this->db->where('id', $id);
		$this->db->where('BookingID', $BookingID);
		$this->db->set('exit_date', $exit_date);
		$this->db->set('exit_by', $username);
		$this->db->set('status', 10);
		return $this->db->update('tblGateMaster');
	}
	public function markExitWithdrawalDB($BookingID){
		$exit_date = date('Y-m-d H:i:s');
		$username = $this->session->userdata('username');
		$this->db->where('BookingID', $BookingID);
		$this->db->set('exit_date', $exit_date);
		$this->db->set('exit_by', $username);
		$this->db->set('status', 7);
		return $this->db->update('tblGateMaster');
	}
	public function GetOtherDeductionMasterList()
	{
		// Inward Other Deduction Group Code = 12
		$this->db->select('tblitems.ItemID,tblitems.ItemName');
		$this->db->where('tblitems.subgroup_id', '12');
		$this->db->where('tblitems.isactive', 'Y');
		$result = $this->db->get('tblitems')->result_array();
		return $result;
	}
	public function GetDebitNoteItemList()
	{
		// Inward Debit Note Deduction Group Code = 11
		$this->db->select('tblitems.ItemID,tblitems.ItemName');
		$this->db->where('tblitems.subgroup_id', '11');
		$this->db->where('tblitems.isactive', 'Y');
		$result = $this->db->get('tblitems')->result_array();
		return $result;
	}
	public function GetActualOtherDeductionList($BookingID,$GateINID)
	{
		$this->db->select('tblotherdeduction.ItemID,tblotherdeduction.Amount,tblotherdeduction.quantity,tblotherdeduction.ParticularItemID,
		tblitems.ItemName,tblstaff.firstname,tblstaff.lastname,tblotherdeduction.TransDate');
		$this->db->join('tblitems','tblitems.ItemID = tblotherdeduction.ItemID');
		$this->db->join('tblstaff','tblstaff.AccountID = tblotherdeduction.UserID',"LEFT");
		$this->db->where('tblotherdeduction.BookingID', $BookingID);
		$this->db->where('tblotherdeduction.GateINID', $GateINID);
		$result = $this->db->get('tblotherdeduction')->result_array();
		return $result;
	}
	public function getLayerDetails($BookingID,$GateINID)
	{
		$this->db->select('tblLayerMaster.*,tblstaff.firstname,tblstaff.lastname');
		$this->db->join('tblstaff','tblstaff.staffid = tblLayerMaster.UserID','left');
		$this->db->where('tblLayerMaster.BookingID', $BookingID);
		$this->db->where('tblLayerMaster.Gate_in_ID	', $GateINID);
		$result = $this->db->get('tblLayerMaster')->result_array();
		$i = 0;
		foreach($result as $key=>$value){
			$this->db->select('tblQCParameterValues.ParameterValue,tblstaff.firstname,tblstaff.lastname,tblItemParameter.ItemParameterID,tblItemParameter.ItemParameterName,tblQCParameterValues.UserID,tblQCParameterValues.TransDate');
			$this->db->join('tblItemParameter','tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
			$this->db->join('tblstaff','tblstaff.staffid = tblQCParameterValues.UserID','left');
			$this->db->where('tblQCParameterValues.BookingID', $BookingID);
			$this->db->where('tblQCParameterValues.Gate_in_ID	', $GateINID);
			$this->db->where('tblQCParameterValues.layer_number', $value['layer_number']);
			$parameter_detail = $this->db->get('tblQCParameterValues')->result_array();
			$result[$i]['parameter_detail'] = $parameter_detail;
			$i++;
		}
		return $result;
	}
	// Get Stack List with QC Details Against GateIN old
	/*public function GetStackListAgainstInward($BookingID,$GateINID)
		{
		$this->db->select('tblstockInventory.*');
		$this->db->where('tblstockInventory.BookingID', $BookingID);
		$this->db->where('tblstockInventory.GateINID	', $GateINID);
		$result = $this->db->get('tblstockInventory')->result_array();
		return $result;
	}*/
	// Get Stack List with QC Details Against GateIN New
	public function GetStackListAgainstInward($BookingID,$GateINID)
	{
		$this->db->select('tblstockInventory.*');
		$this->db->where('tblstockInventory.BookingID', $BookingID);
		$this->db->where('tblstockInventory.GateINID', $GateINID);
		$result = $this->db->get('tblstockInventory')->result_array();
		$i = 0;
		foreach($result as $key=>$val){
			$this->db->select('tblQCParameterValues.*,tblItemParameter.*,tblItemQCParameter.BaseValue');
			$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
			$this->db->join('tblItemQCParameter', 'tblItemQCParameter.ItemParameterID = tblQCParameterValues.ItemParameterID AND tblItemQCParameter.ItemID = tblQCParameterValues.ItemID');
			$this->db->where('tblQCParameterValues.BookingID', $BookingID);
			$this->db->where('tblQCParameterValues.Gate_in_ID', $GateINID);
			$this->db->where('tblQCParameterValues.layer_number	', $val["QCID"]);
			$this->db->where('tblQCParameterValues.TType', "F");
			$QCValues = $this->db->get('tblQCParameterValues')->result_array();
			$result[$i]['QcDetails'] = $QCValues;
			$i++;
		}
		return $result;
	}
	public function getWithdrawalQCDetails($BookingID,$GateINID){
		$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->where('TType', 'U');
		$this->db->where('layer_number', 1);
		$this->db->order_by('id','ASC');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	public function getPeripheralDetails($BookingID,$GateINID)
	{
		$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
		$this->db->join('tblItemQCParameter', 'tblItemQCParameter.ItemParameterID = tblQCParameterValues.ItemParameterID AND tblItemQCParameter.ItemID = tblQCParameterValues.ItemID');
		$this->db->join('tblstaff','tblstaff.staffid = tblQCParameterValues.UserID','left');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->where('tblItemQCParameter.Status', 'Y');
		$this->db->where('TType', 'P');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	/*public function StockInventoryData($BookingID,$GateINID)
	{
		$this->db->select('tblstockInventory.*, IFNULL(W.WithdrawQty, 0) as WithdrawQty');
		$this->db->from('tblWithdrawalDetail');
		// Join with tblstockInventory
		$this->db->join('tblstockInventory','tblstockInventory.GateINID = tblWithdrawalDetail.GateINID AND tblstockInventory.BookingID = tblWithdrawalDetail.TradeID','INNER');
		// Subquery to get SUM(Weight) per GateINID, ItemID, QCID (WithdrawQty)
		$subquery = "(SELECT GateINID, ItemID, QCID, SUM(Weight) as WithdrawQty
		FROM " . db_prefix() . "stockInventory
		WHERE TType = 'W'
		GROUP BY GateINID, ItemID, QCID) as W";
		// Join with subquery on matching keys
		$this->db->join($subquery,
		"W.GateINID = tblstockInventory.GateINID
		AND W.ItemID = tblstockInventory.ItemID
		AND W.QCID = tblstockInventory.QCID",
		'LEFT');
		// Filter by BookingID
		$this->db->where('tblWithdrawalDetail.BookingID', $BookingID);
		$this->db->where('tblstockInventory.TType', 'D');
		// Execute query
		$result = $this->db->get()->result_array();
		if($result){
			$i = 0;
			foreach($result as $each){
				$this->db->select('SUM(tblstockInventory.Weight) as TotalWeight,tblstockInventory.TransID as EditTransID');
				$this->db->from('tblwithdrawalmaster');
				// Join with tblstockInventory
				//$this->db->join('tblwithdrawalmaster','tblwithdrawalmaster.TransID = tblWithdrawalDetail.GateINID','INNER');
				$this->db->join('tblstockInventory','tblstockInventory.TransID = tblwithdrawalmaster.PurchID','INNER');
				// Filter by BookingID
				$this->db->where('tblwithdrawalmaster.TransID', $GateINID);
				$this->db->where('tblstockInventory.QCID', $each['QCID']);
				$this->db->where('tblstockInventory.GateINID', $each['GateINID']);
				$this->db->where('tblstockInventory.TType', 'W');
				$this->db->group_by('tblstockInventory.GateINID,tblstockInventory.QCID');
				// Execute query
				$data = $this->db->get()->row();
				$result[$i]["WhQty"] = $data->TotalWeight;
				$result[$i]["EditTransID"] = $data->EditTransID;
				$i++;
			}
		}
		return $result;
	}*/
	public function StockInventoryData($BookingID,$GateINID){
		//Get Ttype from Gate Master
		$this->db->select('tblGateMaster.*');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		$GetMasterList =  $this->db->get('tblGateMaster')->row_array();
		if($GetMasterList['TType'] == "TW")
		{
			$Type = 'T';
			$invTType = 'TW';
		}else if($GetMasterList['TType'] == "W"){
			$Type = 'D';
			$invTType = 'W';
		}else if($GetMasterList['TType'] == "AW"){
			$Type = 'A';
			$invTType = 'AW';
		}
		//Get Deposit GetInList against Withdrawl BookingId
		$this->db->select('tblWithdrawalDetail.*');
		$this->db->where('BookingID', $BookingID);
		$DepositGateInList =  $this->db->get('tblWithdrawalDetail')->result_array();
		$DepositGateInIDs = array();
		foreach($DepositGateInList as $value)
		{
			array_push($DepositGateInIDs,$value['GateINID']);
		}
		$DepositList = array(); // default empty
		if (!empty($DepositGateInIDs)) {
			$this->db->select('tblstockInventory.*');
			$this->db->where_in('GateINID', $DepositGateInIDs);
			$this->db->where('TType', $Type);
			$query = $this->db->get('tblstockInventory');
			if ($query !== false) {
				$DepositList = $query->result_array();
			}
		}
		//Current Withdrawl qty
		$this->db->select('tblwithdrawalmaster.*,tblstockInventory.Weight,tblstockInventory.TType,tblstockInventory.GateINID,tblstockInventory.QCID,tblstockInventory.TransID as EditTransID ');
		$this->db->join('tblstockInventory', 'tblstockInventory.TransID = tblwithdrawalmaster.PurchID');
		$this->db->where('tblwithdrawalmaster.TransID', $GateINID);
		//$this->db->where('tblstockInventory.TType', "W");
		$this->db->where('tblstockInventory.TType', $invTType);
		$this->db->group_by('tblstockInventory.GateINID,tblstockInventory.QCID');
		$WithdrawlQty =  $this->db->get('tblwithdrawalmaster')->result_array();
		//Gat all withdrawl quantity
		$AllWithdrawList = array();
		if (!empty($DepositGateInIDs)) {
			$this->db->select('tblstockInventory.*');
			$this->db->where_in('GateINID', $DepositGateInIDs);
			//$this->db->where('TType', "W");
			$this->db->where('TType', $invTType);
			//$this->db->group_by('tblstockInventory.GateINID,tblstockInventory.QCID');
			$query = $this->db->get('tblstockInventory');
			if ($query !== false) {
				$AllWithdrawList = $query->result_array();
			}
		}
		$reponse->DepositList = $DepositList;
		$reponse->WithdrawlQty = $WithdrawlQty;
		$reponse->AllWithdrawList = $AllWithdrawList;
		return $reponse;
	}
	public function GetGodownListByCenter($CenterID)
	{
		$this->db->select('tblwarehouse.*');
		$this->db->where('center', $CenterID);
		return $this->db->get('tblwarehouse')->result_array();
	}
	public function getParameterValueDetails($BookingID){
		$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('TType', 'U');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	public function getFinalQCDetails($BookingID,$GateINID)
	{
		$this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblQCParameterValues.ItemParameterID');
		$this->db->where('BookingID', $BookingID);
		$this->db->where('TType', 'F');
		$this->db->where('Gate_in_ID', $GateINID);
		$this->db->order_by('id','ASC');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	public function addLoanDetails($data)
	{
		return $this->db->insert('tblloan_history',$data);
	}
	public function addFinalQCWithdrawalDB($data){
		$this->db->where('BookingID',$data['BookingID']);
		$this->db->set('status',7);
		$this->db->update('tblGateMaster');
		return $this->db->insert('tblQCParameterValues',$data);
	}
	public function ApprovePaymentAdvice($GateINID,$GateControl)
	{
		$this->db->where('Gate_in_ID',$GateINID);
		return $this->db->update('tblGateMaster',$GateControl);
	}
	public function addFinalQCDB($data)
	{
		return $this->db->insert('tblQCParameterValues',$data);
	}
	public function Add_Other_Deduction($data,$id)
	{
		$update = 0;
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		$TransID = $data['TransID'];
		$WeightShortInKg = $data['WeightShortInKg'];
		$Amt = $WeightShortInKg * $data['RatePerKg'];
		$OthDeduction = $data['OthDeduction'];
		$reasonamt = $data['remarkmiscamt'];
		$Pre_other_deduction = $this->GetActualOtherDeductionList($BookingID,$GateINID);
		foreach($Pre_other_deduction as $pKey=>$pval){
			if($pval["ParticularItemID"] == "QOD" || $pval["ParticularItemID"] == "SDC"){
				$pre_data = array(
				"BookingID"=>$pval["BookingID"],
				"GateINID"=>$pval["GateINID"],
				"TransID"=>$pval["TransID"],
				"ItemID"=>$pval["ItemID"],
				"Amount"=>$pval["Amount"],
				"UserID"=>$pval["UserID"],
				"TransDate"=>$pval["TransDate"],
				"TransDate2"=>date('Y-m-d H:i:s')
				);
				$this->db->insert('tblotherdeduction_history',$pre_data);
			}
		}
		$item_array = array("QOD","SDC");
		$this->db->where('BookingID',$BookingID);
		$this->db->where('GateINID',$GateINID);
		$this->db->where('TransID',$TransID);
		$this->db->where_in('ParticularItemID',$item_array);
		$this->db->delete('tblotherdeduction');
		foreach($OthDeduction as $key=>$val){
			$data_array = array(
			"BookingID"=>$BookingID,
			"GateINID"=>$GateINID,
			"TransID"=>$TransID,
			"ParticularItemID"=>"QOD",
			"ItemID"=>$key,
			"Amount"=>$val,
			"UserID"=>$this->session->userdata('username'),
			"TransDate"=>date('Y-m-d H:i:s')
			);
			if($this->db->insert('tblotherdeduction',$data_array)){
				$update++;
			}
		}
		// Add Weight Shortage Charges
		$data_array = array(
		"BookingID"=>$BookingID,
		"GateINID"=>$GateINID,
		"TransID"=>$TransID,
		"ParticularItemID"=>"SDC",
		"ItemID"=>"SDC",
		"quantity"=>$WeightShortInKg,
		"Amount"=>$Amt,
		"UserID"=>$this->session->userdata('username'),
		"TransDate"=>date('Y-m-d H:i:s')
		);
		$this->db->insert('tblotherdeduction',$data_array);
		$updatereasonamt = array(
			"MiscRemark"=>$reasonamt,
		);
		$this->db->where('id',$id);
		$this->db->where('Gate_in_ID',$GateINID);
		$this->db->update('tblGateMaster',$updatereasonamt);
		return $update;
	}
	public function updateFinalQCDB($data,$GateINID,$BookingID)
	{
		$update = 0;
		$QC_for = $data['QC_for'];
		unset($data['QC_for']);
		// Move data to QC audit log table
		$FQC = $this->GetFinalQC($BookingID,$GateINID);
		foreach($FQC as $pKey=>$pval){
			$pre_data = array(
			"BookingID"=>$pval["BookingID"],
			"Gate_in_ID"=>$pval["Gate_in_ID"],
			"TType"=>$pval["TType"],
			"ItemID"=>$pval["ItemID"],
			"layer_number"=>$pval["layer_number"],
			"ItemParameterID"=>$pval["ItemParameterID"],
			"ParameterValue"=>$pval["ParameterValue"],
			"EParameterValue"=>$pval["EParameterValue"],
			"HParameterValue"=>$pval["HParameterValue"],
			"deductionAmt"=>$pval["deductionAmt"],
			"UserID"=>$pval["UserID"],
			"TransDate"=>$pval["TransDate"]
			);
			$this->db->insert('tblQCParameterValues_history',$pre_data);
		}
		foreach($data as $key=>$value){
			if($QC_for == "Center"){
				$data2 = array(
				'ParameterValue' => $value,
				'EParameterValue' => $value,
				'HParameterValue' => $value,
				'UserID' => $this->session->userdata('username'),
				'TransDate' => date('Y-m-d H:i:s'),
				);
				}else if($QC_for == "RO"){
				$data2 = array(
				'EParameterValue' => $value,
				'HParameterValue' => $value,
				'UserID' => $this->session->userdata('username'),
				'TransDate' => date('Y-m-d H:i:s'),
				);
				}else if($QC_for == "HO"){
				$data2 = array(
				'HParameterValue' => $value,
				'UserID' => $this->session->userdata('username'),
				'TransDate' => date('Y-m-d H:i:s'),
				);
			}
			$this->db->where('ItemParameterID',$key);
			$this->db->where('BookingID',$BookingID);
			$this->db->where('Gate_in_ID',$GateINID);
			$this->db->where('TType','F');
			if($this->db->update('tblQCParameterValues',$data2)){
				$update++;
			}
		}
		if($update > 0){
			return true;
			}else{
			return false;
		}
	}
	public function updateHOQCDB($data){
		$this->db->where('id',$data['id']);
		$this->db->set('HParameterValue',$data['HParameterValue']);
		$this->db->set('UserID2',$data['UserID2']);
		$this->db->set('Lupdate',$data['Lupdate']);
		return $this->db->update('tblQCParameterValues');
	}
	public function updateFinalQCWithdrawalDB($data){
		$this->db->where('id',$data['id']);
		$this->db->set('ParameterValue',$data['ParameterValue']);
		$this->db->set('UserID2',$data['UserID2']);
		$this->db->set('Lupdate',$data['Lupdate']);
		return $this->db->update('tblQCParameterValues');
	}
	public function UpdatePaymentAdvice($data,$GateINID)
	{
		$this->db->where('Gate_in_ID',$GateINID);
		return $this->db->update('GateMaster',$data);
	}
	public function GetPreLedgerEntry($PurchID)
	{
		$this->db->select('tblaccountledger.*,');
		//$this->db->where('tblaccountledger.AccountID', $AccountID);
		$this->db->where('tblaccountledger.VoucherID', $PurchID);
		return $this->db->get('tblaccountledger')->result_array();
	}
	public function GetTCSDetails()
	{
		$c_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->where('EffDate <=',date('Y-m-d'));
		$this->db->from(db_prefix() . 'tcsmaster');
		$this->db->order_by('id',"desc");
		return $this->db->get()->result_array();
	}
	public function GetCurrentRate($CenterID,$ItemID)
	{
		$this->db->select('tblSaleRateMaster.*');
		$this->db->where('ItemID',$ItemID);
		$this->db->where('CenterID',$CenterID);
		$this->db->where('KeyID',"C01");
		$this->db->where('IsActive',"Y");
		$this->db->from(db_prefix() . 'SaleRateMaster');
		return $this->db->get()->row();
	}
	public function GetControlDetails($BookingID,$GateINID)
	{
		$this->db->select('tblGateMaster.AccountID, tblGateMaster.BookingID, tblGateMaster.PlantID,tblGateMaster.FY,tblGateMaster.LoadedWeight,tblGateMaster.TareWeight,tblGateMaster.PlantID,tblGateMaster.FY,
		tblGateMaster.basic_rate,tblGateMaster.VehicleNo,tblGateMaster.Phone,tblGateMaster.CenterID,tblGateMaster.GodownID,tblGateMaster.unit,
		tblGateMaster.quantity,tblGateMaster.Asn_WT_MT,tblGateMaster.asn_date,tblitems.PCItemID,
		tblGateMaster.TType,tblGateMaster.TType2,tblGateMaster.PartyID,tblGateMaster.ASNID,tblGateMaster.gate_out_date,tblGateMaster.exit_date,tbllead_master.BrokerID,
		tblGstRecord.business_name AS company,tblGstRecord.state_code AS state,tblGstRecord.gstin AS vat,tblclients.CustomerType,tblclients.ShortCode,tblclients.state AS ClientState,tbltaxes.taxrate,tblpurchasemaster.PurchID,tblitems.ItemID,
		tblitems.hsn_code,tblUnloadingMaster.total_bags,tblUnloadingMaster.total_katta,tblUnloadingMaster.total_layers,tblordermaster.DOID,tblordermaster.Cases,
		tblcontacts.TdsPercentage,tblcontacts.TdsSection,
		tblsalesmaster.SalesID,tblsalesmaster.SaleAmt,tblsalesmaster.sgstamt,tblsalesmaster.cgstamt,tblsalesmaster.igstamt,tblsalesmaster.BillAmt');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblclients', 'tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblGateMaster.AccountID AND tblGstRecord.IsPrimary = "1"',"LEFT");
		$this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID AND tblcontacts.PlantID = tblclients.PlantID');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax','LEFT');
		$this->db->join('tblsalesmaster', 'tblsalesmaster.AccountID = tblGateMaster.AccountID AND tblsalesmaster.OrderID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblordermaster', 'tblordermaster.AccountID = tblGateMaster.AccountID AND tblordermaster.ASNID = tblGateMaster.ASNID',"LEFT");
		$this->db->join('tblpurchasemaster', 'tblpurchasemaster.AccountID = tblGateMaster.AccountID AND tblpurchasemaster.TransID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->join('tblUnloadingMaster', 'tblUnloadingMaster.BookingID = tblGateMaster.BookingID AND tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID',"LEFT");
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where('tblGateMaster.Gate_in_ID', $GateINID);
		return $this->db->get('tblGateMaster')->row();
	}
	public function GetPartyStateItemTax($BookingID,$ASNID)
	{
		$this->db->select('tblGateMaster.AccountID,tblGstRecord.state_code AS state,tbltaxes.taxrate,tblitems.ItemID,tblGstRecord.gstin');
		$this->db->join('tblclients', 'tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax','LEFT');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblGateMaster.AccountID AND tblGstRecord.IsPrimary = "1"',"LEFT");
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where('tblGateMaster.ASNID', $ASNID);
		return $this->db->get('tblGateMaster')->row();
	}
	public function GetDODetails($BookingID,$ASNID)
	{
		$this->db->select('tblGateMaster.AccountID,tblGateMaster.BookingID,tblGateMaster.ASNID,tblGateMaster.asn_date AS ASNDate,
		tblGateMaster.Asn_WT_MT,tblGateMaster.basic_rate,tblGateMaster.SalesRepName,tblGateMaster.SalesRepMobile,
		tbllead_master.TransDate AS BookingDate,tblGstRecord.business_name AS company,tblGstRecord.gstin AS vat,
		tblordermaster.DOID,tblordermaster.Transdate AS DODate,
		tblitems.hsn_code,tblitems.ItemName,tbltaxes.taxrate,tblstaff.firstname,tblstaff.lastname,
		tblPlantMaster.PlantName,tblPlantMaster.pincode,tblPlantMaster.address,tblPlantMaster.GstNo,tblPlantMaster.fssai_no,tblPlantMaster.state,tblxx_citylist.city_name,tblTalukaMaster.TalukaName,
		tblCenterMaster.CenterName,tblCenterMaster.address AS CenterAddress,tblCenterMaster.state AS CState,Ccity.city_name AS CCityName,Ctaluka.TalukaName AS CTalukaName');
		$this->db->join('tbllead_master', 'tbllead_master.BookingID = tblGateMaster.BookingID');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax','LEFT');
		$this->db->join('tblordermaster', 'tblordermaster.ASNID = tblGateMaster.ASNID');
		$this->db->join('tblstaff', 'tblstaff.AccountID = tblordermaster.UserID');
		$this->db->join('tblclients', 'tblclients.AccountID = tblGateMaster.AccountID');
		$this->db->join('tblGstRecord','tblGstRecord.AccountID = tblGateMaster.AccountID AND tblGstRecord.IsPrimary = "1"',"LEFT");
		$this->db->join('tblPlantMaster', 'tblPlantMaster.PlantID = tblGateMaster.PartyID');
		$this->db->join('tblxx_citylist','tblxx_citylist.id = tblPlantMaster.city','left');
		$this->db->join('tblTalukaMaster','tblTalukaMaster.id = tblPlantMaster.taluka','left');
		$this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = tblGateMaster.CenterID');
		$this->db->join('tblxx_citylist Ccity','Ccity.id = tblCenterMaster.city','left');
		$this->db->join('tblTalukaMaster Ctaluka','Ctaluka.id = tblPlantMaster.taluka','left');
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where('tblGateMaster.ASNID', $ASNID);
		return $this->db->get('tblGateMaster')->row();
	}
	public function GetPCSoftDoc($BookingID)
	{
		$this->db->select('tblpcsoft_gic_number_referance.*');
		$this->db->where('tblpcsoft_gic_number_referance.GIC_Reference', $BookingID);
		$this->db->where('tblpcsoft_gic_number_referance.Type', "S");
		$this->db->where('tblpcsoft_gic_number_referance.Name', "Trade");
		return $this->db->get('tblpcsoft_gic_number_referance')->row();
	}
	public function GetdeductionDetails($BookingID,$GateINID){
		$this->db->select('tblQCParameterValues.*');
		$this->db->where('tblQCParameterValues.BookingID', $BookingID);
		$this->db->where('tblQCParameterValues.Gate_in_ID', $GateINID);
		$this->db->where('tblQCParameterValues.TType', 'F');
		return $this->db->get('tblQCParameterValues')->result_array();
	}
	public function GetPurchaseDetails($GateINID)
	{
		$this->db->select('tblpurchasemaster.*');
		$this->db->where('tblpurchasemaster.TransID', $GateINID);
		return $this->db->get('tblpurchasemaster')->row();
	}
	public function GenerateLedgerEntryForSale($BookingID,$GateINID)
	{
		$GateControlDetails = $this->GetControlDetails($BookingID,$GateINID);
		$selected_company = $GateControlDetails->PlantID;
		$fy = $GateControlDetails->FY;
		$AccountID = $GateControlDetails->AccountID;
		$ItemID = $GateControlDetails->ItemID;
		$CenterID = $GateControlDetails->CenterID;
		$PartyID = $GateControlDetails->PartyID;
		$SalesID = $GateControlDetails->SalesID;
		$BillAmt = $GateControlDetails->BillAmt;
		$SaleAmt = $GateControlDetails->SaleAmt;
		$igstamt = $GateControlDetails->igstamt;
		$cgstamt = $GateControlDetails->cgstamt;
		$sgstamt = $GateControlDetails->sgstamt;
		$Nerration = " Sale Against BookingID ".$BookingID."/ GateInID ".$GateINID;
		// Delete Previous ledger entry
		$this->db->where('VoucherID', $SalesID);
		$this->db->delete(db_prefix() . 'accountledger');
		$SrNo = 1;
		// Debit to Party Accounts
		$drLedger = array(
		"PlantID" =>  $selected_company,
		"FY" =>  $fy,
		"Transdate" =>date('Y-m-d H:i:s'),
		"VoucherID" =>  $SalesID,
		"TransDate2" =>  date('Y-m-d H:i:s'),
		"AccountID" =>  $AccountID,
		"CenterID" =>  $CenterID,
		"CommodityID" =>  $ItemID,
		"PartyID"=>$PartyID,
		"EntryFor" =>  3,
		"TType" =>  'D',
		"Amount" =>  $BillAmt,
		"CounterAccount" => "SALE",
		"Narration" =>$Nerration,
		"PassedFrom" =>  "SALE",
		"OrdinalNo" =>$SrNo,
		"UserID" =>  $this->session->userdata('username'),
		);
		$this->db->insert('tblaccountledger',$drLedger);
		$SrNo++;
		// Credit to Sale Accounts
		$crLedger = array(
		"PlantID" =>  $selected_company,
		"FY" =>  $fy,
		"Transdate" =>date('Y-m-d H:i:s'),
		"VoucherID" =>  $SalesID,
		"TransDate2" =>  date('Y-m-d H:i:s'),
		"AccountID" =>  "SALE",
		"CenterID" =>  $CenterID,
		"CommodityID" =>  $ItemID,
		"PartyID"=>$PartyID,
		"EntryFor" =>  3,
		"TType" =>  'C',
		"Amount" =>  $SaleAmt,
		"CounterAccount" => $AccountID,
		"Narration" =>$Nerration,
		"PassedFrom" =>  "SALE",
		"OrdinalNo" =>$SrNo,
		"UserID" =>  $this->session->userdata('username'),
		);
		$this->db->insert('tblaccountledger',$crLedger);
		$SrNo++;
		if($igstamt > 0){
			// Credit to IGST Accounts
			$crLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "IGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $igstamt,
			"CounterAccount" => $AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$SrNo,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$SrNo++;
			}else{
			// Credit to CGST Accounts
			$crLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "CGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $cgstamt,
			"CounterAccount" => $AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "SALE",
			"OrdinalNo" =>$SrNo,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$SrNo++;
			// Credit to SGST Accounts
			$crLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $SalesID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "SGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  3,
			"TType" =>  'C',
			"Amount" =>  $sgstamt,
			"CounterAccount" => "SALE",
			"Narration" =>$Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>$SrNo,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$crLedger);
			$SrNo++;
		}
	}
	// add purchase ledger entry
	public function GenerateLedgerEntryForPurchase($BookingID,$GateINID,$NetAmt)
	{
		$GateControlDetails = $this->GetControlDetails($BookingID,$GateINID);
		$selected_company = $GateControlDetails->PlantID;
		$fy = $GateControlDetails->FY;
		$Gst_per = $GateControlDetails->taxrate;
		$PurchID = $GateControlDetails->PurchID;
		$AccountID = $GateControlDetails->AccountID;
		$ItemID = $GateControlDetails->ItemID;
		$CenterID = $GateControlDetails->CenterID;
		$CustomerType = $GateControlDetails->CustomerType;
		$tdsSection = $GateControlDetails->TdsSection;
		$PartyID = $GateControlDetails->PartyID;
		if($CustomerType == "1"){
			$tdsAmt = 0;
			$DeductionDetails = $this->GetdeductionDetails($BookingID,$GateINID);
			$rate_per_qtls = $GateControlDetails->basic_rate;
			//$ItemWeight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
			$ActualWeight = ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight)/10;
			$AsnWeight = $GateControlDetails->Asn_WT_MT;
			if($ActualWeight <= $AsnWeight){
				$ItemWeight = $ActualWeight;
				}else{
				$ItemWeight = $AsnWeight * 10;
			}
			$PurchAmt = ($ItemWeight * 10) * $rate_per_qtls;
			$Total_deduction = 0;
			foreach($DeductionDetails as $Key=>$val){
				$Total_deduction += $val["deductionAmt"];
			}
			$OtherDeduction = $this->GetActualOtherDeductionList($BookingID,$GateINID,$PurchID);
			foreach($OtherDeduction as $okey=>$oval){
				$Total_deduction += $oval["Amount"];
			}
			$PurchAmt -= $Total_deduction;
			$Gst_per = 0;
			}else{
			$tdsAmt = ($NetAmt * ($GateControlDetails->TdsPercentage / 100));
			$PurchAmt = ($GateControlDetails->Asn_WT_MT * 10) * $GateControlDetails->basic_rate;
		}
		$GSTAmt = $PurchAmt * ($Gst_per / 100);
		$invAmt = $PurchAmt + $GSTAmt;
		$Nerration = " Against BookingID ".$BookingID."/ GateInID ".$GateINID;
		if($GateControlDetails->state == "MH"){
			$cgst = $GSTAmt / 2;
			$sgst = $GSTAmt / 2;
			$igst = 0;
			}else{
			$igst = $GSTAmt;
			$cgst = 0;
			$sgst = 0;
		}
		// Delete Previous ledger entry
		$this->db->where('VoucherID', $PurchID);
		$this->db->delete(db_prefix() . 'accountledger');
		$crLedger = array(
		"PlantID" =>  $selected_company,
		"FY" =>  $fy,
		"Transdate" =>date('Y-m-d H:i:s'),
		"VoucherID" =>  $PurchID,
		"TransDate2" =>  date('Y-m-d H:i:s'),
		"AccountID" =>  $AccountID,
		"CenterID" =>  $CenterID,
		"CommodityID" =>  $ItemID,
		"PartyID"=>$PartyID,
		"EntryFor" =>  2,
		"TType" =>  'C',
		"Amount" =>  $invAmt,
		"CounterAccount" => "PURCH",
		"Narration" =>$Nerration,
		"PassedFrom" =>  "PURCHASE",
		"OrdinalNo" =>  1,
		"UserID" =>  $this->session->userdata('username'),
		);
		$this->db->insert('tblaccountledger',$crLedger);
		$drLedger = array(
		"PlantID" =>  $selected_company,
		"FY" =>  $fy,
		"Transdate" =>date('Y-m-d H:i:s'),
		"VoucherID" =>  $PurchID,
		"TransDate2" =>  date('Y-m-d H:i:s'),
		"AccountID" =>  "PURCH",
		"CenterID" =>  $CenterID,
		"CommodityID" =>  $ItemID,
		"PartyID"=>$PartyID,
		"EntryFor" =>  2,
		"TType" =>  'D',
		"Amount" =>  $PurchAmt,
		"CounterAccount" => $GateControlDetails->AccountID,
		"Narration" =>$Nerration,
		"PassedFrom" =>  "PURCHASE",
		"OrdinalNo" =>  2,
		"UserID" =>  $this->session->userdata('username'),
		);
		$this->db->insert('tblaccountledger',$drLedger);
		if($igst > 0){
			$drLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $PurchID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "IIGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  2,
			"TType" =>  'D',
			"Amount" =>  $igst,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>  3,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
			}else{
			$drLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $PurchID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "ICGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  2,
			"TType" =>  'D',
			"Amount" =>  $cgst,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" => $Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>  3,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
			$drLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $PurchID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" =>  "ISGST",
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  2,
			"TType" =>  'D',
			"Amount" =>  $sgst,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" => $Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>  4,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
		}
		// TDS Ledger Entry
		// Update PurchaseMaster for tds amount
		$purchase_update = array(
		"TDS"=>$GateControlDetails->TdsSection,
		"tds_per"=>$GateControlDetails->TdsPercentage,
		"tdsAmt"=>$tdsAmt
		);
		$this->db->where('TransID',$GateINID);
		$this->db->where('PurchID',$PurchID);
		$this->db->update('tblpurchasemaster',$purchase_update);
		if($tdsAmt > 0){
			$drLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $PurchID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" => $AccountID,
			"CenterID" =>  $CenterID,
			"CommodityID" =>  $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  2,
			"TType" =>  'D',
			"Amount" =>  $tdsAmt,
			"CounterAccount" => $tdsSection,
			"Narration" =>$Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>  5,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
			$drLedger = array(
			"PlantID" =>  $selected_company,
			"FY" =>  $fy,
			"Transdate" =>date('Y-m-d H:i:s'),
			"VoucherID" =>  $PurchID,
			"TransDate2" =>  date('Y-m-d H:i:s'),
			"AccountID" => $tdsSection,
			"CenterID" =>  $CenterID,
			"CommodityID" => $ItemID,
			"PartyID"=>$PartyID,
			"EntryFor" =>  2,
			"TType" =>  'C',
			"Amount" =>  $tdsAmt,
			"CounterAccount" => $GateControlDetails->AccountID,
			"Narration" => $Nerration,
			"PassedFrom" =>  "PURCHASE",
			"OrdinalNo" =>  6,
			"UserID" =>  $this->session->userdata('username'),
			);
			$this->db->insert('tblaccountledger',$drLedger);
		}
	}
	// Generate debit note for other than farmer account
	public function GenerateDebitNote($BookingID,$GateINID)
	{
		$DeductionDetails = $this->GetdeductionDetails($BookingID,$GateINID);
		$GateControlDetails = $this->GetControlDetails($BookingID,$GateINID);
		$selected_company = $GateControlDetails->PlantID;
		$fy = $GateControlDetails->FY;
		$Gst_per = $GateControlDetails->taxrate;
		$PurchID = $GateControlDetails->PurchID;
		$AccountID = $GateControlDetails->AccountID;
		$PartyID = $GateControlDetails->PartyID;
		$ItemID = $GateControlDetails->ItemID;
		$CenterID = $GateControlDetails->CenterID;
		$rate_per_kg = ($GateControlDetails->basic_rate / 100);
		//$ItemWeight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
		$ActualWeight = $GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight;
		$AsnWeight = $GateControlDetails->Asn_WT_MT;
		if($ActualWeight <= $AsnWeight){
			$ItemWeight = $ActualWeight;
			}else{
			$ItemWeight = $AsnWeight * 10;
		}
		$NetWt_in_kg = $ItemWeight * 100;
		$quantity = 0;
		$Total_deduction = 0;
		$QualityDeduction = 0;
		foreach($DeductionDetails as $Key=>$val){
			$Total_deduction += $val["deductionAmt"];
			$QualityDeduction += $val["deductionAmt"];
		}
		$OtherDeduction = $this->GetActualOtherDeductionList($BookingID,$GateINID,$PurchID);
		$DebitNoteItem = $this->GetDebitNoteItemList();
		foreach($OtherDeduction as $okey=>$oval){
			$Total_deduction += $oval["Amount"];
		}
		$GstAmt = ($Total_deduction * $Gst_per) /100;
		if($GateControlDetails->state == "MH"){
			$cgst = $GstAmt / 2;
			$sgst = $GstAmt / 2;
			$igst = 0.00;
			$cgst_per = $Gst_per / 2;
			$sgst_per = $Gst_per / 2;
			$igst_per = 00;
			}else{
			$igst = $GstAmt;
			$cgst = 0.00;
			$sgst = 0.00;
			$cgst_per = 0;
			$sgst_per = 0;
			$igst_per = $Gst_per;
		}
		$BillAmt = $Total_deduction + $GstAmt;
		$new_creditNumber = get_option('next_debit_number_for_kirti');
		$Billno = "DR".$fy.$new_creditNumber;
		$cd_notes = array(
		"FY"=>$fy,
		"plantid"=>$selected_company,
		"BT"=>"D",
		"TransID"=>$GateINID,
		"IsAutopost"=>"Y",
		"Billno"=>$Billno,
		"Transdate"=>date('Y-m-d H:i:s'),
		"AccountID"=>$AccountID,
		"SaleAmt"=>$Total_deduction,
		"cgstamt"=>$cgst,
		"sgstamt"=>$sgst,
		"igstamt"=>$igst,
		"BillAmt"=>$BillAmt,
		"RndAmt"=>round($BillAmt),
		"passedfrom"=>"SALESRECEIPT",
		"Userid"=>$this->session->userdata('username'),
		"narration"=>"Quality and other deductions",
		);
		if($this->db->insert(db_prefix() . 'cdnote', $cd_notes)){
			foreach($DebitNoteItem as $keyItem => $valItem){
				$particularAmt = 0;
				foreach($OtherDeduction as $othkey=>$othval){
					if($valItem["ItemID"] == $othval["ParticularItemID"]){
						$particularAmt += $othval["Amount"];
						$quantity = $ADVal["quantity"];
					}
				}
				if($valItem["ItemID"] == "QOD"){
					$particularAmt += $QualityDeduction;
					$rate_per_kg = $particularAmt / $NetWt_in_kg;
					$quantity = $NetWt_in_kg;
				}
				$ItemGstAmt = ($particularAmt * $Gst_per) /100;
				if($GateControlDetails->state == "MH"){
					$Itemcgst = $ItemGstAmt / 2;
					$Itemsgst = $ItemGstAmt / 2;
					$Itemigst = 0.00;
					}else{
					$Itemigst = $ItemGstAmt;
					$Itemcgst = 0.00;
					$Itemsgst = 0.00;
				}
				$particularBillAmt = $particularAmt + $ItemGstAmt;
				$cd_notes_details = array(
				"fy"=>$fy,
				"plantid"=>$selected_company,
				"billno"=>$Billno,
				"transdate"=>date('Y-m-d H:i:s'),
				"ttype"=>"D",
				"AccountID"=>$AccountID,
				"itemid"=>$valItem["ItemID"],
				"hsncode"=>$GateControlDetails->hsn_code,
				"rate"=>$rate_per_kg,
				"qty"=>$quantity,
				"cgst"=>$cgst_per,
				"cgstamt"=>$Itemcgst,
				"sgst"=>$sgst_per,
				"sgstamt"=>$Itemsgst,
				"igst"=>$igst_per,
				"igstamt"=>$Itemigst,
				"amount"=>$particularBillAmt,
				"ordinalno"=>1,
				"TransID"=>$PurchID,
				);
				$this->db->insert(db_prefix() . 'cdnotehistory', $cd_notes_details);
			}
			$this->increment_next_number('next_debit_number_for_kirti');
			$narration_debit = "By CDNote ".$Billno."/Quality and other deductions Against PO ".$PurchID." BookingID ".$BookingID." / GateINID ".$GateINID;
			$CR_debit_note = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"Transdate"=>date('Y-m-d H:i:s'),
			"TransDate2"=>date('Y-m-d H:i:s'),
			"VoucherID"=>$Billno,
			"PartyID"=>$PartyID,
			"CenterID"=>$CenterID,
			"CommodityID"=>$ItemID,
			"EntryFor"=>2,
			"AccountID"=>"CLAIM",
			"TType"=>"C",
			"Amount"=>$Total_deduction,
			"CounterAccount" =>  $GateControlDetails->AccountID,
			"Narration"=>$narration_debit,
			"PassedFrom"=>"CDNOTE",
			"OrdinalNo"=>1,
			"UserID"=>$this->session->userdata('username')
			);
			$this->db->insert(db_prefix() . 'accountledger', $CR_debit_note);
			$DR_debit_note = array(
			"PlantID"=>$selected_company,
			"FY"=>$fy,
			"Transdate"=>date('Y-m-d H:i:s'),
			"TransDate2"=>date('Y-m-d H:i:s'),
			"VoucherID"=>$Billno,
			"PartyID"=>$PartyID,
			"CenterID"=>$CenterID,
			"CommodityID"=>$ItemID,
			"EntryFor"=>2,
			"AccountID"=>$GateControlDetails->AccountID,
			"TType"=>"D",
			"Amount"=>$BillAmt,
			"CounterAccount" =>"CLAIM",
			"Narration"=>$narration_debit,
			"PassedFrom"=>"CDNOTE",
			"OrdinalNo"=>2,
			"UserID"=>$this->session->userdata('username')
			);
			$this->db->insert(db_prefix() . 'accountledger', $DR_debit_note);
			// GST Ledger
			if($igst > 0){
				$CR_debit_note = array(
				"PlantID"=>$selected_company,
				"FY"=>$fy,
				"Transdate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				"VoucherID"=>$Billno,
				"PartyID"=>$PartyID,
				"CenterID"=>$CenterID,
				"CommodityID"=>$ItemID,
				"EntryFor"=>2,
				"AccountID"=>"IGST",
				"TType"=>"C",
				"Amount"=>$igst,
				"CounterAccount" =>  $GateControlDetails->AccountID,
				"Narration"=>$narration_debit,
				"PassedFrom"=>"CDNOTE",
				"OrdinalNo"=>3,
				"UserID"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix() . 'accountledger', $CR_debit_note);
				}else{
				$CR_debit_note = array(
				"PlantID"=>$selected_company,
				"FY"=>$fy,
				"Transdate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				"VoucherID"=>$Billno,
				"PartyID"=>$PartyID,
				"CenterID"=>$CenterID,
				"CommodityID"=>$ItemID,
				"EntryFor"=>2,
				"AccountID"=>"CGST",
				"TType"=>"C",
				"Amount"=>$cgst,
				"CounterAccount" =>  $GateControlDetails->AccountID,
				"Narration"=>$narration_debit,
				"PassedFrom"=>"CDNOTE",
				"OrdinalNo"=>3,
				"UserID"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix() . 'accountledger', $CR_debit_note);
				$CR_debit_note = array(
				"PlantID"=>$selected_company,
				"FY"=>$fy,
				"Transdate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				"VoucherID"=>$Billno,
				"AccountID"=>"SGST",
				"PartyID"=>$PartyID,
				"CenterID"=>$CenterID,
				"CommodityID"=>$ItemID,
				"EntryFor"=>2,
				"TType"=>"C",
				"Amount"=>$sgst,
				"CounterAccount" =>  $GateControlDetails->AccountID,
				"Narration"=>$narration_debit,
				"PassedFrom"=>"CDNOTE",
				"OrdinalNo"=>4,
				"UserID"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix() . 'accountledger', $CR_debit_note);
			}
			$this->db->where('BookingID',$BookingID);
			$this->db->where('Gate_in_ID',$GateINID);
			$this->db->set('IsCD','Y');
			$this->db->update('tblGateMaster');
			return true;
		}
		return false;
	}
	// Not in USE
	public function approvePaymentDB($data)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$Booking_details = $this->GetBooking_details($data['id']);
		$PurchID = $Booking_details->PurchID;
		$BookingID = $Booking_details->BookingID;
		$Netweight = $Booking_details->LoadedWeight - $Booking_details->TareWeight;
		$purch_amt = $Netweight * $Booking_details->basic_rate;
		$FQC = $this->GetFinalQC($BookingID);
		//print_r($FQC);
		$DeductionMatrix = $this->GetDeductionMatrix($Booking_details->ItemID);
		//echo "<br>";
		$total_deduction = 0;
		foreach($FQC as $key=>$value)
		{
			foreach($DeductionMatrix as $key1=>$value1)
			{
				if($value["ItemID"] == $value1["ItemID"] && $value["ItemParameterID"] == $value1["ItemParameterID"] && $value["ParameterValue"] == $value1["Value"])
				{
					if($value1['Deduction'] > 0){
						if($value["ItemParameterID"] == "2"){
							$deductionAmt = $value1['Deduction'] * $Netweight;
							}else{
							$deductionAmt = $purch_amt * ($value1['Deduction'] / 100);
						}
						$total_deduction += $deductionAmt;
						$this->db->where('BookingID',$BookingID);
						$this->db->where('TType',"F");
						$this->db->where('ItemID',$Booking_details->ItemID);
						$this->db->where('ItemParameterID',$value["ItemParameterID"]);
						$this->db->set('deductionAmt',$deductionAmt);
						$this->db->update('tblQCParameterValues');
					}
				}
			}
		}
		if($total_deduction > 0){
		}
		$narration = "Purchase Against BookingID ".$BookingID."/ PurchaseID ".$PurchID;
		$credit_ledger = array(
		"PlantID"=>$selected_company,
		"FY"=>$fy,
		"Transdate"=>date('Y-m-d H:i:s'),
		"TransDate2"=>date('Y-m-d H:i:s'),
		"VoucherID"=>$BookingID,
		"AccountID"=>$Booking_details->AccountID,
		"TType"=>"C",
		"Amount"=>$purch_amt,
		"Narration"=>$narration,
		"PassedFrom"=>"PURCHASE",
		"OrdinalNo"=>1,
		"UserID"=>$this->session->userdata('username')
		);
		$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
		$debit_ledger = array(
		"PlantID"=>$selected_company,
		"FY"=>$fy,
		"Transdate"=>date('Y-m-d H:i:s'),
		"TransDate2"=>date('Y-m-d H:i:s'),
		"VoucherID"=>$BookingID,
		"AccountID"=>'PURCH',
		"TType"=>"D",
		"Amount"=>$purch_amt,
		"Narration"=>$narration,
		"PassedFrom"=>"PURCHASE",
		"OrdinalNo"=>2,
		"UserID"=>$this->session->userdata('username')
		);
		$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
		//die;
		$this->db->where('id',$data['id']);
		$this->db->set('payment_done',$data['payment_done']);
		$this->db->set('payment_approved_by',$data['payment_approved_by']);
		$this->db->set('payment_approved_date',$data['payment_approved_date']);
		$this->db->set('status',13);
		if($this->db->update('tblGateMaster')){
			return true;
			}else{
			return false;
		}
	}
	public function increment_next_number($name)
	{
		$FY = $this->session->userdata('finacial_year');
		$this->db->where('name',$name);
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	public function GetBooking_details($id)
	{
		$this->db->select('tblGateMaster.*,tbltaxes.taxrate,tblitems.hsn_code,tblpurchasemaster.PurchID');
		$this->db->join('tblpurchasemaster', 'tblpurchasemaster.TransID = tblGateMaster.BookingID','LEFT');
		$this->db->join('tblitems', 'tblitems.ItemID = tblGateMaster.ItemID');
		$this->db->join('tbltaxes', 'tbltaxes.id = tblitems.tax');
		$this->db->where('tblGateMaster.id',$id);
		$data = $this->db->get('tblGateMaster')->row();
		return $data;
	}
	public function GetDMGAmt($BookingID)
	{
		$this->db->select('tbllead_master.*,tblDMGAmtCenterWise.DMGAmt');
		$this->db->join('tblDMGAmtCenterWise', 'tblDMGAmtCenterWise.CenterID = tbllead_master.CenterID','LEFT');
		$this->db->where('tbllead_master.BookingID',$BookingID);
		$data = $this->db->get('tbllead_master')->row();
		return $data;
	}
	public function GetFinalQC($BookingID,$GateINID)
	{
		$this->db->select('tblQCParameterValues.*');
		$this->db->where('tblQCParameterValues.BookingID',$BookingID);
		$this->db->where('tblQCParameterValues.Gate_in_ID',$GateINID);
		$this->db->where('tblQCParameterValues.TType','F');
		$data = $this->db->get('tblQCParameterValues')->result_array();
		return $data;
	}
	public function GetQcMinMax($ItemID)
	{
		$this->db->select('tblItemQCParameter.*');
		$this->db->where('tblItemQCParameter.ItemID',$ItemID);
		$data = $this->db->get('tblItemQCParameter')->result_array();
		return $data;
	}
	public function GetDeductionMatrix($ItemID)
	{
		$this->db->select('tbldeduction_matrix.*');
		$this->db->where('tbldeduction_matrix.ItemID',$ItemID);
		$data = $this->db->get('tbldeduction_matrix')->result_array();
		return $data;
	}
	public function GetParameterDeductionMatrix($ItemID, $parameterID)
	{
		$this->db->select('tbldeduction_matrix.*');
		$this->db->where('tbldeduction_matrix.ItemID',$ItemID);
		$this->db->where('tbldeduction_matrix.ItemParameterID',$parameterID);
		$data = $this->db->get('tbldeduction_matrix')->result_array();
		return $data;
	}
	////////////////////////////////////////////////////Deduction Matrix/////////////////////////////////////////
	public function getMandiData(){
		$this->db->join('tblxx_statelist','tblxx_statelist.id = tblCenterMaster.state');
		$this->db->join('tbl_xx_city','tbl_xx_city.id = tblCenterMaster.city');
		return $this->db->get('tblCenterMaster')->result_array();
	}
	public function getAllMandiDb(){
		$this->db->join('tblxx_statelist','tblxx_statelist.id = tblCenterMaster.state');
		$this->db->join('tbl_xx_city','tbl_xx_city.id = tblCenterMaster.city');
		return $this->db->get('tblCenterMaster')->result_array();
	}
	public function getSingleMandiDb($center_id){
		$this->db->where('CenterID',$center_id);
		$data = $this->db->get('tblCenterMaster')->row();
		if($data){
			$this->db->select('tblDMGAmtCenterWise.*');
			$this->db->from(db_prefix() .'DMGAmtCenterWise');
			$this->db->where('tblDMGAmtCenterWise.CenterID', $center_id);
			$Parameter = $this->db->get()->result_array();
			$data->Parameter = $Parameter;
		}
		return $data;
	}
	public function saveMandi($data){
		return $this->db->insert('tblCenterMaster',$data);
	}
	public function saveNumberFormat($data){
		return $this->db->insert_batch('tblnumberformat',$data);
	}
	public function updateMandi($data)
	{
		$ParameterAssign = $data["paradataSerializedArr"];
		$ParameterAssignArray = json_decode($ParameterAssign, true);
		$ParameterAssignArraylen = count($ParameterAssignArray);
		unset($data["paradataSerializedArr"]);
		$UserID = $this->session->userdata('username');
		$this->db->where('CenterID',$data['CenterID']);
		if($this->db->update('tblCenterMaster',$data)){
			// Insert / Update Parameter
			for($k=0; $k<$ParameterAssignArraylen; $k++) {
				$ItemID = $ParameterAssignArray[$k][0];
				$DMGAmt = $ParameterAssignArray[$k][1];
				$addtblid = $ParameterAssignArray[$k][2];
				if(!empty($addtblid))
				{
					$UpdateDMGParameter = array(
					'ItemID' =>$ItemID,
					'DMGAmt' =>$DMGAmt,
					'Lupdate' =>date('Y-m-d H:i:s'),
					'UserID2' =>$UserID
					);
					$this->db->where('id', $addtblid);
					$this->db->update(db_prefix() . 'DMGAmtCenterWise', $UpdateDMGParameter);
					}else{
					$InsDMGParameter = array(
					'CenterID' =>$data['CenterID'],
					'ItemID' =>$ItemID,
					'DMGAmt' =>$DMGAmt,
					'Transdate' =>date('Y-m-d H:i:s'),
					'UserID' =>$UserID
					);
					$this->db->insert(db_prefix() . 'DMGAmtCenterWise',$InsDMGParameter);
				}
			}
			return true;
			}else{
			return false;
		}
	}
	public function GetChamberList($WHID)
	{
		$this->db->select('*');
		$this->db->from(db_prefix() .'WHSizeMaster');
		$this->db->where('WHID', $WHID);
		return $this->db->get()->result();
	}
	public function GetWarehouseStackList($CHID)
	{
		$this->db->select('*');
		$this->db->from(db_prefix() .'whstackmaster');
		$this->db->where('CHID', $CHID);
		return $this->db->get()->result();
	}
	public function GetStackList($WHID)
	{
		$this->db->select('*');
		$this->db->from(db_prefix() .'whstackmaster');
		$this->db->where('WHID', $WHID);
		return $this->db->get()->result();
	}
	public function GetStackLotList($StackID)
	{
		$this->db->select('*');
		$this->db->from(db_prefix() .'lot_master');
		$this->db->where('StackID', $StackID);
		return $this->db->get()->result();
	}
	public function GetAllClientsNameDB($BookingType)
	{
		$this->db->select('tbllead_master.*,tblclients.company,tblcontacts.firstname,tblcontacts.lastname');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID','left');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID','left');
		if($BookingType != ''){
			$this->db->where('tbllead_master.TType',$BookingType);
		}
		$this->db->where('tbllead_master.status','1');
		$this->db->where('tbllead_master.ClientApprove','Y');
		$this->db->where('tbllead_master.IsApprove','Y');
		$this->db->group_by('tbllead_master.AccountID');
		$this->db->order_by('id','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetAllBookingIDDB($BookingType,$PartyName)
	{
		$this->db->select('tbllead_master.*');
		if($BookingType != ''){
			$this->db->where('TType',$BookingType);
		}
		if($PartyName != ''){
			$this->db->where('AccountID',$PartyName);
		}
		$this->db->where('tbllead_master.status','1');
		$this->db->where('tbllead_master.ClientApprove','Y');
		$this->db->where('tbllead_master.IsApprove','Y');
		$this->db->order_by('id','ASC');
		return $this->db->get('tbllead_master')->result_array();
	}
	public function GetTableDataDB($Name,$BookingType,$BookingID)
	{
		$this->db->select('tblGateMaster.*,tblitems.ItemName,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblUnloadingMaster.total_bags,tblUnloadingMaster.total_katta,tblUnloadingMaster.total_layers');
		$this->db->join('tblUnloadingMaster','tblUnloadingMaster.Gate_in_ID = tblGateMaster.Gate_in_ID','LEFT');
		$this->db->join('tblitems','tblitems.ItemID = tblGateMaster.ItemID','left');
		$this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID','left');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tblGateMaster.AccountID','left');
		if($BookingType != ''){
			$this->db->where('tblGateMaster.TType',$BookingType);
		}
		if($Name != ''){
			$this->db->where('tblGateMaster.AccountID',$Name);
		}
		if($BookingID != ''){
			$this->db->where('tblGateMaster.BookingID',$BookingID);
		}
		$this->db->order_by('tblGateMaster.asn_date','DESC');
		return $this->db->get('tblGateMaster')->result_array();
	}
	public function GetSingleBookingDataDB($BookingID)
	{
		$this->db->select('tbllead_master.*,tblclients.CustomerType');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->where('tbllead_master.BookingID',$BookingID);
		$this->db->order_by('tbllead_master.id','ASC');
		return $this->db->get('tbllead_master')->row();
	}
	public function CompanyDetails($PartyID)
	{
		$this->db->select('tblPlantMaster.*,tblxx_statelist.state_name,tblxx_citylist.city_name,tblTalukaMaster.TalukaName');
		$this->db->join('tblxx_statelist','tblxx_statelist.short_name = tblPlantMaster.state','left');
		$this->db->join('tblxx_citylist','tblxx_citylist.id = tblPlantMaster.city','left');
		$this->db->join('tblTalukaMaster','tblTalukaMaster.id = tblPlantMaster.taluka','left');
		$this->db->where('tblPlantMaster.PlantID',$PartyID);
		return $this->db->get('tblPlantMaster')->row();
	}
	public function GetTodaysRate($ItemID,$CenterID,$CustomerType = "")
	{
		$this->db->where('ItemID',$ItemID);
		$this->db->where('CenterID',$CenterID);
		$this->db->where('IsActive','Y');
		if($CustomerType !=""){
			if($CustomerType == "1"){
				$type = "F";
			}else{
				$type = "T";
			}
			$this->db->where('Type',$type);
		}
		$this->db->where('KeyID','C01');
		return $this->db->get('tblRateMaster')->row();
	}
	public function GetTodaysSaleRate($ItemID,$CenterID)
	{
		$this->db->where('ItemID',$ItemID);
		$this->db->where('CenterID',$CenterID);
		$this->db->where('IsActive','Y');
		$this->db->where('KeyID','C01');
		return $this->db->get('tblSaleRateMaster')->row();
	}
	public function GetCommision($ItemID,$CenterID,$CompID)
	{
		$this->db->where('ItemID',$ItemID);
		$this->db->where('PartyID',$CompID);
		$this->db->where('IsActive','Y');
		$this->db->where('IsOn','Y');
		$this->db->where('CenterID',$CenterID);
		return $this->db->get('tblCommisionMatrix')->row();
	}
	public function save_settlement($data_update)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$BookingID = $data_update['BookingID'];
		$PartyName = $data_update['PartyName'];
		$BookingType = $data_update['BookingType'];
		$shortageAmt = $data_update['shortageAmt'];
		$CompID = $data_update['CompID'];
		$BookingWT = $data_update['BookingQty'];
		$ItemID = $data_update['ItemID'];
		$CenterID = $data_update['CenterID'];
		$BrokerID = $data_update['BrokerID'];
		$is_not_delivered = $data_update['is_not_delivered'];
		$NotDelAmt = $data_update['NotDelAmt'];
		$lead_master_update = array(
		"inw_Weight"=>$data_update['inw_Weight'],
		"status"=>$data_update['status'],
		"today_rate"=>$data_update['today_rate'],
		"is_invoice"=>$data_update['is_invoice'],
		"NonDeliverd"=>$data_update['is_not_delivered'],
		"NonDeliverdAmt"=>$data_update['NotDelAmt'],
		"ShortageAmt"=>$data_update['shortageAmt'],
		"SettlementID"=>$data_update['SettlementID'],
		"SettlementDate"=>$data_update['SettlementDate'],
		"settlement_remark"=>$data_update['settlement_remark'],
		);
		unset($data_update['BookingID']);
		unset($data_update['BookingType']);
		unset($data_update['CompID']);
		unset($data_update['BookingQty']);
		unset($data_update['shortageAmt']);
		unset($data_update['ItemID']);
		unset($data_update['CenterID']);
		unset($data_update['BrokerID']);
		unset($data_update['PartyName']);
		unset($data_update['is_not_delivered']);
		unset($data_update['NotDelAmt']);
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$ttype = "D"; // Debit Note
		$this->db->where('BookingID',$BookingID);
		$this->db->where('AccountID',$PartyName);
		$this->db->where('TType',$BookingType);
		if($this->db->update('tbllead_master',$lead_master_update)){
			// Brokerage Charges Calculation
			if($BrokerID != $PartyName){
				$chargeAmt = 5;
				$TotalChargeAmt = $chargeAmt * $data_update['inw_Weight'];
				$GstChargeAmt = $TotalChargeAmt * (18/100);
				$cgst = $GstChargeAmt /2;
				$InvCommAmt = $TotalChargeAmt + $GstChargeAmt;
				$RecNumber = get_option('next_BrokerReceipt_number_for_kirti');
				$BrokerCharges = array(
				"TransID"=>'REC'.$fy.$RecNumber,
				"BookingID"=>$BookingID,
				"TransDate"=>date('Y-m-d H:i:s'),
				"TransType"=>'B',
				"AccountTo"=>$BrokerID,
				"AccountBy"=>$CompID,
				"BookingWT"=>$BookingWT,
				"InwardWT"=>$data_update['inw_Weight'],
				"Rate"=>$TotalChargeAmt,
				"Amount"=>$TotalChargeAmt,
				"cgstAmt"=>$cgst,
				"sgstAmt"=>$cgst,
				"igstAmt"=>0.00,
				"InvoiceAmt"=>$InvCommAmt,
				"UserID"=>$this->session->userdata('username'),
				);
				if($this->db->insert('tblSettlement_invoice',$BrokerCharges)){
					$this->increment_next_number('next_BrokerReceipt_number_for_kirti');
					$BrokerCharges = array(
					"TransID"=>'REC'.$fy.$RecNumber,
					"BookingID"=>$BookingID,
					"TransDate"=>date('Y-m-d H:i:s'),
					"TransType"=>'B',
					"AccountTo"=>$BrokerID,
					"AccountBy"=>$CompID,
					"ItemID"=>$ItemID,
					"basic_rate"=>$TotalChargeAmt,
					"amount"=>$TotalChargeAmt,
					"cgst"=>9.00,
					"cgstAmt"=>$cgst,
					"sgst"=>9.00,
					"sgstAmt"=>$cgst,
					"igst"=>0.00,
					"igstAmt"=>0.00,
					"InvAmt"=>$InvCommAmt,
					"UserID"=>$this->session->userdata('username'),
					);
					$this->db->insert('tblSettlement_invoice_details',$BrokerCharges);
					// Ledger Entry
					$Nerration = "Brokerage Charges Against BookingID ".$BookingID;
					$crLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>$CompID,
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  $BrokerID,
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'C',
					"Amount" =>  $InvCommAmt,
					"CounterAccount" => "BROK",
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "BROKERAGE",
					"OrdinalNo" =>  1,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$crLedger);
					$drLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>$CompID,
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  'BROK',
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'D',
					"Amount" =>  $TotalChargeAmt,
					"CounterAccount" => $BrokerID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "BROKERAGE",
					"OrdinalNo" =>  2,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$drLedger);
					$drLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>$CompID,
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  'CGST',
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'D',
					"Amount" =>  $cgst,
					"CounterAccount" => $BrokerID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "BROKERAGE",
					"OrdinalNo" =>  3,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$drLedger);
					$drLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>$CompID,
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  'SGST',
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'D',
					"Amount" =>  $cgst,
					"CounterAccount" => $BrokerID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "BROKERAGE",
					"OrdinalNo" =>  4,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$drLedger);
				}
			}
			// Platform Commision Calculation From Company
			if($CompID != "KASPL"){
				$GetCommisionAmt = $this->GetCommision($ItemID,$CenterID,$CompID);
				if($GetCommisionAmt){
					$CommAmt = $GetCommisionAmt->CommisionAmt;
					}else{
					$CommAmt = 5.00;
				}
				$InvoiceNumber = get_option('next_invoice_number_for_kirti');
				$TotalAmtComm = $CommAmt * $data_update['inw_Weight'];
				$TotalGSTAmtComm = $TotalAmtComm * (18/100);
				$cgst = $TotalGSTAmtComm / 2;
				$InvAmt = $TotalAmtComm + $TotalGSTAmtComm;
				$CommisionInvoice = array(
				"TransID"=>'INV'.$fy.$InvoiceNumber,
				"BookingID"=>$BookingID,
				"TransDate"=>date('Y-m-d H:i:s'),
				"TransType"=>'C',
				"AccountTo"=>$CompID,
				"AccountBy"=>"KASPL",
				"BookingWT"=>$BookingWT,
				"InwardWT"=>$data_update['inw_Weight'],
				"Rate"=>$CommAmt,
				"Amount"=>$TotalAmtComm,
				"cgstAmt"=>$cgst,
				"sgstAmt"=>$cgst,
				"igstAmt"=>0.00,
				"InvoiceAmt"=>$InvAmt,
				"UserID"=>$this->session->userdata('username'),
				);
				if($this->db->insert('tblSettlement_invoice',$CommisionInvoice)){
					$this->increment_next_number('next_invoice_number_for_kirti');
					$CommDetails = array(
					"TransID"=>'INV'.$fy.$InvoiceNumber,
					"BookingID"=>$BookingID,
					"TransDate"=>date('Y-m-d H:i:s'),
					"TransType"=>'C',
					"AccountTo"=>$CompID,
					"AccountBy"=>"KASPL",
					"ItemID"=>$ItemID,
					"basic_rate"=>$CommAmt,
					"amount"=>$TotalAmtComm,
					"cgst"=>9.00,
					"cgstAmt"=>$cgst,
					"sgst"=>9.00,
					"sgstAmt"=>$cgst,
					"igst"=>0.00,
					"igstAmt"=>0.00,
					"InvAmt"=>$InvAmt,
					"UserID"=>$this->session->userdata('username'),
					);
					$this->db->insert('tblSettlement_invoice_details',$CommDetails);
					// Ledger Entry
					$Nerration = "Commision Amount Against BookingID ".$BookingID;
					$crLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>"KASPL",
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  "COMMR",
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'C',
					"Amount" =>  $TotalAmtComm,
					"CounterAccount" => $CompID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "COMMISION",
					"OrdinalNo" =>  1,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$crLedger);
					$crLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>"KASPL",
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  "CGST",
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'C',
					"Amount" =>  $cgst,
					"CounterAccount" => $CompID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "COMMISION",
					"OrdinalNo" =>  2,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$crLedger);
					$crLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>"KASPL",
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  "SGST",
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'C',
					"Amount" =>  $cgst,
					"CounterAccount" => $CompID,
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "COMMISION",
					"OrdinalNo" =>  3,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$crLedger);
					$drLedger = array(
					"PlantID" =>  $selected_company,
					"FY" =>  $fy,
					"PartyID" =>"KASPL",
					"Transdate" =>date('Y-m-d H:i:s'),
					"VoucherID" =>  $BookingID,
					"TransDate2" =>  date('Y-m-d H:i:s'),
					"AccountID" =>  $CompID,
					"CenterID" => $CenterID,
					"CommodityID" =>  $ItemID,
					"EntryFor" =>  3,
					"TType" =>  'D',
					"Amount" =>  $InvAmt,
					"CounterAccount" => "COMMR",
					"Narration" =>  $Nerration,
					"PassedFrom" =>  "COMMISION",
					"OrdinalNo" =>  4,
					"UserID" =>  $this->session->userdata('username'),
					);
					$this->db->insert('tblaccountledger',$drLedger);
				}
			}
			// Short Delivery Charges Calculate
			if($data_update['is_invoice']=="Y"){
				$new_debitNumber = get_option('next_debit_number_for_kirti');
				$Billno = "DR".$fy.$new_debitNumber;
				$ShortageGstAmt = $shortageAmt * (5/100);
				$cgst = $ShortageGstAmt / 2;
				$igst = 0;
				$billAmt = $shortageAmt + $ShortageGstAmt;
				$narration = "Shortage Delivered Charges Against ".$BookingID;
				$cd_notes = array(
				"FY"=>$fy,
				"plantid"=>$selected_company,
				"BT"=>$ttype,
				"Billno"=>$Billno,
				"Transdate"=>date('Y-m-d H:i:s'),
				"AccountID"=>$PartyName,
				"SaleAmt"=>$shortageAmt,
				"cgstamt"=>$cgst,
				"sgstamt"=>$cgst,
				"igstamt"=>$igst,
				"BillAmt"=>$billAmt,
				"RndAmt"=>round($billAmt),
				"passedfrom"=>"PURCHASESRECEIPT",
				"Userid"=>$this->session->userdata('username'),
				"narration"=>$data["narration"],
				);
				if($this->db->insert(db_prefix() . 'cdnote', $cd_notes)){
					$this->increment_next_number_for_Debit_note();
					$cd_notes_details = array(
					"fy"=>$fy,
					"plantid"=>$selected_company,
					"billno"=>$Billno,
					"transdate"=>date('Y-m-d H:i:s'),
					"ttype"=>$ttype,
					"AccountID"=>$PartyName,
					"itemid"=>"SDC",
					"hsncode"=>'48043100',
					"rate"=>$shortageAmt,
					"qty"=>"1",
					"cgst"=>'2.50',
					"cgstamt"=>$cgst,
					"sgst"=>'2.50',
					"sgstamt"=>$cgst,
					"igst"=>'0.00',
					"igstamt"=>'0.00',
					"amount"=>$billAmt,
					"ordinalno"=>'1',
					"TransID"=>$BookingID,
					);
					if($this->db->insert(db_prefix() . 'cdnotehistory', $cd_notes_details)){
						$narretion = "By CDNote ".$Billno."/".$narration;
						$debit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"D",
						"AccountID"=>$PartyName,
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$billAmt,
						"CounterAccount" => "SDC",// Ledger Account
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'1',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"SDC", // Ledger Account
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$shortageAmt,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'2',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"CGST",
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$cgst,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'3',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"SGST",
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$cgst,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'4',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
					}
				}
			}
			// Calculate Not Delivered Charges & Generate Debit Note
			if($is_not_delivered == "Y"){
				$new_debitNumber = get_option('next_debit_number_for_kirti');
				$Billno = "DR".$fy.$new_debitNumber;
				$NotDelGstAmt = $NotDelAmt * (18/100);
				$cgst = $NotDelGstAmt / 2;
				$igst = 0;
				$billAmt = $NotDelAmt + $NotDelGstAmt;
				$narration = "Not Delivered Charges Against ".$BookingID;
				$cd_notes = array(
				"FY"=>$fy,
				"plantid"=>$selected_company,
				"BT"=>$ttype,
				"Billno"=>$Billno,
				"Transdate"=>date('Y-m-d H:i:s'),
				"AccountID"=>$PartyName,
				"SaleAmt"=>$NotDelAmt,
				"cgstamt"=>$cgst,
				"sgstamt"=>$cgst,
				"igstamt"=>$igst,
				"BillAmt"=>$billAmt,
				"RndAmt"=>round($billAmt),
				"passedfrom"=>"PURCHASESRECEIPT",
				"Userid"=>$this->session->userdata('username'),
				"narration"=>$data["narration"],
				);
				if($this->db->insert(db_prefix() . 'cdnote', $cd_notes)){
					$this->increment_next_number_for_Debit_note();
					$cd_notes_details = array(
					"fy"=>$fy,
					"plantid"=>$selected_company,
					"billno"=>$Billno,
					"transdate"=>date('Y-m-d H:i:s'),
					"ttype"=>$ttype,
					"AccountID"=>$PartyName,
					"itemid"=>"NDC",
					"hsncode"=>'48043100',
					"rate"=>$NotDelAmt,
					"qty"=>"1",
					"cgst"=>'9.00',
					"cgstamt"=>$cgst,
					"sgst"=>'9.00',
					"sgstamt"=>$cgst,
					"igst"=>'0.00',
					"igstamt"=>'0.00',
					"amount"=>$billAmt,
					"ordinalno"=>'1',
					"TransID"=>$BookingID,
					);
					if($this->db->insert(db_prefix() . 'cdnotehistory', $cd_notes_details)){
						$narretion = "By CDNote ".$Billno."/".$narration;
						$debit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"D",
						"AccountID"=>$PartyName,
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$billAmt,
						"CounterAccount" => "NDC",
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'1',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"NDC",
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$NotDelAmt,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'2',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"CGST",
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$cgst,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'3',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
						$credit_ledger = array(
						"FY"=>$fy,
						"PlantID"=>$selected_company,
						"PartyID" =>$CompID,
						"VoucherID"=>$Billno,
						"Transdate"=>date('Y-m-d H:i:s'),
						"TransDate2"=>date('Y-m-d H:i:s'),
						"TType"=>"C",
						"AccountID"=>"SGST",
						"CenterID" => $CenterID,
						"CommodityID" =>  $ItemID,
						"EntryFor" =>  3,
						"Amount"=>$cgst,
						"CounterAccount" => $PartyName,
						"Narration"=>$narretion,
						"PassedFrom"=>"CDNOTE",
						"OrdinalNo"=>'4',
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
					}
				}
			}
			return true;
			}else{
			return false;
		}
	}
	public function increment_next_number_for_Debit_note()
	{
		// Update next Receipts number in settings
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		$this->db->where('name', 'next_debit_number_for_kirti');
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	public function getTradeDetails($BookingID)
	{
		$this->db->select('tbllead_master.*,tblitems.ItemID,tblitems.ItemName,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblRateMaster.Rate AS CurrentRate');
		$this->db->where('tbllead_master.BookingID', $BookingID);
		$this->db->join('tblitems', 'tblitems.ItemID = tbllead_master.ItemID');
		$this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');
		$this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');
		$this->db->join('tblRateMaster','tblRateMaster.ItemID = tblitems.ItemID AND tblRateMaster.CenterID = tbllead_master.CenterID AND tblRateMaster.KeyID = "C01" AND tblRateMaster.IsActive = "Y"');
		$this->db->order_by('tbllead_master.id', 'ASC');
		return $this->db->get('tbllead_master')->row();
	}
	public function getCleaningBagDetails($GateInID){
		$this->db->select('tblcleaning_details.*,tblstaff.firstname,tblstaff.lastname');
		// 			$this->db->where('tblcleaning_details.Type', 'I');
		$this->db->where('tblcleaning_details.GateINID', $GateInID);
		$this->db->join('tblstaff','tblstaff.phonenumber = tblcleaning_details.UserID');
		$this->db->order_by('tblcleaning_details.id', 'ASC');
		return $this->db->get('tblcleaning_details')->result_array();
	}
	public function GetItemDetails($ItemID)
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$this->db->select('tblitems.*,tblstockmaster.OQty');
		$this->db->join('tblstockmaster','tblstockmaster.ItemID = tblitems.ItemID AND tblstockmaster.PlantID = tblitems.PlantID AND tblstockmaster.FY = "'.$fy.'"','LEFT');
		$this->db->where('tblitems.PlantID', $selected_company);
		$this->db->where('tblitems.ItemID', $ItemID);
		$data = $this->db->get('tblitems')->row();
		if($data){
			$this->db->select('tblItemQCParameter.*');
			$this->db->from(db_prefix() .'ItemQCParameter');
			$this->db->where('tblItemQCParameter.ItemID', $ItemID);
			$Parameter = $this->db->get()->result_array();
			$data->Parameter = $Parameter;
		}
		return $data;
	}
	public function fetch_gate_master_details($BookingID, $GateInID)
	{
		$this->db->select('tblGateMaster.*');
		$this->db->where('tblGateMaster.BookingID', $BookingID);
		$this->db->where('tblGateMaster.Gate_in_ID', $GateInID);
		$data = $this->db->get('tblGateMaster')->row();
		return $data;
	}
	public function fetch_purchase_details($GateInID)
	{
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblpurchasemaster.*');
		$this->db->where('tblpurchasemaster.FY', $fy);
		$this->db->where('tblpurchasemaster.TransID', $GateInID);
		$data = $this->db->get('tblpurchasemaster')->row();
		return $data;
	}
	public function fetch_creditlimit_data($GateInID)
	{
		$this->db->select('tblCreditLimitMaster.*');
		$this->db->where('tblCreditLimitMaster.GateINID', $GateInID);
		$data = $this->db->get('tblCreditLimitMaster')->row();
		return $data;
	}
	//============================ Update Godown ===================================
	public function UpdateGodown($GodownID,$GateInID)
	{
		$this->db->where('Gate_in_ID', $GateInID);
		if($this->db->update(db_prefix() . 'GateMaster',["GodownID"=>$GodownID])){
			return true;
			}else{
			return false;
		}
	}
	//========================== Add Field Officer =================================
	public function AddFieldOfficer($FeildOfficer,$GateInID)
	{
		$this->db->where('Gate_in_ID', $GateInID);
		if($this->db->update(db_prefix() . 'GateMaster',["FeildOfficer"=>$FeildOfficer])){
			return true;
			}else{
			return false;
		}
	}
	//========================== Add Arrival Date Time =============================
	public function AddArrivalDateTime($ArrivalDateTime,$GateInID)
	{
		$UserID = $this->session->userdata('username');
		$DateTime = to_sql_date(substr($ArrivalDateTime,0,10)).substr($ArrivalDateTime,10,6).":00";
		$this->db->where('Gate_in_ID', $GateInID);
		if($this->db->update(db_prefix() . 'GateMaster',["VchlArrivalDateTime"=>$DateTime,"ArrivalDateTimeUserID"=>$UserID])){
			return true;
			}else{
			return false;
		}
	}
	public function updatePeripheralQcParameterDetails($requestData,$BookingID,$GateInID,$number_of_para)
	{
		// Move to Audit log table
		$qc_details = $this->GateControl_model->fetch_layer_qc_details($BookingID,$GateInID, 'P');
		foreach($qc_details as $qc)
		{
			$insert_qc_history = array(
			'BookingID' => $BookingID,
			'Gate_in_ID' => $GateInID,
			'TType' => 'P',
			'ItemID' => $qc['ItemID'],
			'layer_number' => $qc['layer_number'],
			'ItemParameterID' => $qc['ItemParameterID'],
			'ParameterValue' => $qc['ParameterValue'],
			'UserID' => $qc['UserID2'],
			'TransDate' => $layer['Lupdate'],
			);
			$this->db->insert('tblQCParameterValues_history' , $insert_qc_history);
		}
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		// Updated Qc Parameter
		$updated = 0;
		for($i = 1 ; $i <=$number_of_para; $i++){
			$parameterId = $requestData['parameterId' . $i];
			$parameterValue = $requestData['parameterValue' . $i];
			$update_qc_layer = array(
			'ParameterValue' =>$parameterValue
			);
			$this->db->where('BookingID', $BookingID);
			$this->db->where('Gate_in_ID', $GateInID);
			$this->db->where('TType', 'P');
			$this->db->where('ItemParameterID', $parameterId);
			if($this->db->update(db_prefix() . 'QCParameterValues',$update_qc_layer)){
				$updated++;
			}
		}
		if($updated>0){
			return true;
		}
		return false;
	}
	/*public function updateStackDetails($requestData)
		{
		$StackDetails = $requestData['StackList'];
		// Check Existing Stock details available
		$ExStockDetails = $this->GetStackListAgainstInward($requestData['BookingID'],$requestData['GateINID']);
		if($ExStockDetails){
		$this->db->where('BookingID',$requestData['BookingID']);
		$this->db->where('GateINID',$requestData['GateINID']);
		$this->db->delete('tblstockInventory');
		}
		$insert = 0;
		foreach($StackDetails as $key=>$val){
		$insert_array = array(
		"BookingID"=>$requestData['BookingID'],
		"GateINID"=>$requestData['GateINID'],
		"TransID"=>$requestData['TransID'],
		"TransDate"=>date('Y-m-d H:i:s'),
		"TType"=>$requestData['BookingType'],
		"ItemID"=>$requestData['ItemID'],
		"AccountID"=>$requestData['AccountID'],
		"PartyID"=>$requestData['PartyID'],
		"WHID"=>$requestData['WHID'],
		"CHID"=>$val['Chamber'],
		"StackID"=>$val['Stack'],
		"LOTID"=>$val['Lot'],
		"Weight"=>$val['WeightMT'],
		"BagQty"=>$val['BagQty'],
		"UserID"=>$this->session->userdata('username')
		);
		//print_r($update_array);
		if($this->db->insert(db_prefix() . 'stockInventory',$insert_array)){
		$insert++;
		}
		}
		return $insert;
	}*/
	// Add / Update Stack and QC Details
	public function UpdateStackDetails($requestData)
	{
		$SrNo = 1;
		// Check Existing Stock details available
		$ExStockDetails = $this->GetStackListAgainstInward($requestData['BookingID'],$requestData['GateINID']);
		if($ExStockDetails){
			$this->db->where('BookingID',$requestData['BookingID']);
			$this->db->where('GateINID',$requestData['GateINID']);
			$this->db->delete('tblstockInventory');
		}
		// Delete QC Details
		$this->db->where('BookingID',$requestData['BookingID']);
		$this->db->where('Gate_in_ID',$requestData['GateINID']);
		$this->db->where('TType',"F");
		$this->db->delete('tblQCParameterValues');
		$StackDetails = $requestData['StackList'];
		$ItemWiseQCParameterList = $this->GetItemWiseQCParameterList($requestData['ItemID']);
		$QC_for = $requestData['QC_for'];
		$transdate =  $requestData['GateINDate'];
		//print_r($ItemWiseQCParameterList);
		foreach($StackDetails as $key=>$val){
			$insert_array = array(
			"BookingID"=>$requestData['BookingID'],
			"GateINID"=>$requestData['GateINID'],
			"TransID"=>$requestData['TransID'],
			"TransDate"=>$transdate,
			"TType"=>$requestData['BookingType'],
			"ItemID"=>$requestData['ItemID'],
			"AccountID"=>$requestData['AccountID'],
			"PartyID"=>$requestData['PartyID'],
			"WHID"=>$requestData['WHID'],
			"CHID"=>$val['Chamber'],
			"StackID"=>$val['Stack'],
			"LOTID"=>$val['Lot'],
			"Weight"=>$val['WeightMT'],
			"BagQty"=>$val['BagQty'] + $val['PPBagQty'],
			"JuteBagQty"=>$val['BagQty'],
			"PPBagQty"=>$val['PPBagQty'],
			"QCID"=>$SrNo,
			"UserID"=>$this->session->userdata('username')
			);
			if($requestData["BookingType"] != "S"){
				if($QC_for == "Center" && $val["QcApproval"] == "Y"){
					$insert_array["CenterQCApprove"] = $val['QcApproval'];
					}elseif($QC_for == "RO" && $val["QcApproval"] == "Y"){
					$insert_array["ROQCApprove"] = $val['QcApproval'];
					}elseif($QC_for == "HO" && $val["QcApproval"] == "Y"){
					$insert_array["HOQCApprove"] = $val['QcApproval'];
				}
				}else{
				$insert_array["CenterQCApprove"] = "Y";
				$insert_array["ROQCApprove"] = "Y";
				$insert_array["HOQCApprove"] = "Y";
			}
			//print_r($insert_array);
			if($this->db->insert(db_prefix() . 'stockInventory',$insert_array)){
				if($requestData["BookingType"] != "S"){
					$purch_amt = $val['WeightMT'] * ($requestData['basic_rate'] * 10);
					$Netweight = ($val['WeightMT'] * 10);
					$GetQcMinMax = $this->GetQcMinMax($requestData['ItemID']);
					foreach($ItemWiseQCParameterList as $QcKey=>$QcVal){
						$parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($requestData['ItemID'] , $QcVal["ItemParameterID"]);
						if($QC_for == "Center"){
							$parameterValueToCheck = $val[$QcVal["ItemParameterID"]];
							// min value
							$minVal = floor($val[$QcVal["ItemParameterID"]]);
							// Max Value
							$maxVal = ceil($val[$QcVal["ItemParameterID"]]);
						}elseif($QC_for == "RO"){
							$parameterValueToCheck = $val[$QcVal["ItemParameterID"]];
							// min value
							$minVal = floor($val[$QcVal["ItemParameterID"]]);
							// Max Value
							$maxVal = ceil($val[$QcVal["ItemParameterID"]]);
						}elseif($QC_for == "HO"){
							$parameterValueToCheck = $val[$QcVal["ItemParameterID"]];
							// min value
							$minVal = floor($val[$QcVal["ItemParameterID"]]);
							// Max Value
							$maxVal = ceil($val[$QcVal["ItemParameterID"]]);
						}
						$BaseValue = 2;
						foreach($GetQcMinMax as $k=>$v){
							if($v["ItemParameterID"] == $QcVal["ItemParameterID"]){
								$BaseValue = $v["BaseValue"];
							}
						}
						if($QcVal["CalculationBy"] == "2"){
							//Calculate by amount
							if($parameterValueToCheck <= $BaseValue){
								$deductionAmt = 0;
							}else{
								$deductionAmt = 0;
								$minPer = 0;
								$maxPer = 0;
								foreach($parameterDeductionMatrix as $innerValue)
								{
									if($minVal == $innerValue['Value']){
										$minPer = $innerValue['Deduction'];
									}elseif($maxVal == $innerValue['Value']){
										$maxPer = $innerValue['Deduction'];
									}
								}
								$diff = $parameterValueToCheck - $minVal;
								$DiffDeductiomAmt = $maxPer - $minPer;
								$deductionAmt2 = ($DiffDeductiomAmt * $diff) * $Netweight;
								//$point_deductionAmtPer_qtls = 12 * $diff;
								$deductionAmt = $Netweight * $minPer;
								//$deductionAmt2 = $Netweight * $point_deductionAmtPer_qtls;
								$deductionAmt += $deductionAmt2;
							}
						}else{
							//Calculate by percent
							$minPer = 0;
							$maxPer = 0;
							foreach($parameterDeductionMatrix as $innerValue){
								if($minVal == $innerValue['Value']){
									$minPer = $innerValue['Deduction'];
								}elseif($maxVal == $innerValue['Value']){
									$maxPer = $innerValue['Deduction'];
								}
							}
							$diff = $maxPer - $minPer;
							if($parameterValueToCheck <= $BaseValue){
								$valDeff = 0;
								$deductionAmt = 0;
							}else{
								$valDeff = $parameterValueToCheck - $minVal;
								$finalPer = $minPer + ($valDeff * $diff);
								$deductionAmt = $purch_amt * ($finalPer / 100);
							}
						}
						$data2 = array(
						'BookingID' => $requestData['BookingID'],
						'Gate_in_ID' => $requestData['GateINID'],
						'TType' => 'F',
						'layer_number' => $SrNo,
						'ItemID' => $requestData['ItemID'],
						'ItemParameterID' => $QcVal["ItemParameterID"],
						'ParameterValue' => $val[$QcVal["ItemParameterID"]],
						'EParameterValue' => $val[$QcVal["ItemParameterID"]],
						'HParameterValue' => $val[$QcVal["ItemParameterID"]],
						'deductionAmt'=>$deductionAmt,
						'UserID' => $this->session->userdata('username'),
						'TransDate' => date('Y-m-d H:i:s'),
						);
						$this->db->insert(db_prefix() . 'QCParameterValues',$data2);
					}
				}
				$SrNo++;
			}
		}
		return $SrNo;
	}
	public function updateWithdrawDetails($requestData)
	{
		$Rtn = false;
		$BookingID = $requestData['BookingID'];
		$GateINID = $requestData['GateINID'];
		$WithdrawList = $requestData['WithdrawList'];
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		if(empty($user_Details)){
			$UserID = "Auto";
			}else{
			$UserID = $user_Details->staffid;
		}
		// Check Existing Stock details available
		$WithdrawData = $this->GateControl_model->GetWithdrawalMasterByGateIN($GateINID);
		$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID,$GateINID);
		$BrokerID = $GateControlDetails->BrokerID;
		$DOID = $GateControlDetails->DOID;
		$FY = $GateControlDetails->FY;
		$PlantID = $GateControlDetails->PlantID;
		if(empty($WithdrawData))
		{
			$new_poNumber = get_option2('next_withdrawal_number_for_kirti',$FY);
			$Billno = "DW".$FY.$new_poNumber;
			// print_r($GateControlDetails);
			$data_array = array(
			'PlantID'=>$PlantID,
			'FY'=>$FY,
			'BT'=>'Y',
			"Transdate"=>date('Y-m-d H:i:s'),
			'AccountID'=>$GateControlDetails->AccountID,
			'CenterID' =>$GateControlDetails->CenterID,
			'PurchID' =>$Billno,
			'TransID' =>$GateINID,
			"ItCount"=>1,
			);
			$this->db->insert(db_prefix() . 'withdrawalmaster',$data_array);
			if($this->db->affected_rows() > 0){
				$this->increment_next_number('next_withdrawal_number_for_kirti');
				$new_data = array(
				"status"=>'4',
				);
				$this->db->where('BookingID', $BookingID);
				$this->db->where('Gate_in_ID', $GateINID);
				$this->db->update(db_prefix() . 'GateMaster',$new_data);
				$Rtn= true;
				$Qty = 0;
				foreach($WithdrawList as $key=>$val){
					if(empty($val['WithdrawQty'])){
						$val['WithdrawQty'] = 0;
					}
					if($val['TType'] == "T")
					{
						$TType = "TW"; 	$TypeID = "TW";
					}
					else if($val['TType'] == "D")
					{
						$TType = "W"; 	$TypeID = "DW";
					}
					else if($val['TType'] == "A")
					{
						$TType = "AW"; $TypeID = "AW";
					}
					$date = $GateControlDetails->gate_in_date;
					$Qty += $val['WithdrawQty'];
					$insert_array = array(
					"BookingID"=>$val['BookingID'],
					"GateINID"=>$val['GateINID'],
					"QCID"=>$val['QCID'],
					"TransID"=>$Billno,
					"TransDate"=>$date,
					"TType"=>$TType,
					'PartyID' =>$GateControlDetails->PartyID,
					"ItemID"=>$requestData['ItemID'],
					"AccountID"=>$requestData['AccountID'],
					"WHID"=>$val['WHID'],
					"CHID"=>$val['CHID'],
					"StackID"=>$val['StackID'],
					"LOTID"=>$val['LOTID'],
					"Weight"=>$val['WithdrawQty'],
					"UserID"=>$this->session->userdata('username')
					);
					//print_r($insert_array);
					$this->db->insert(db_prefix() . 'stockInventory',$insert_array);
				}
				$Item_array = array(
				'PlantID'=>$PlantID,
				'FY'=>$FY,
				'cnfid' =>1,
				'OrderID' =>$Billno,
				"BillID"=>$BookingID,
				"TransID"=>$GateINID,
				"TransDate"=>date('Y-m-d H:i:s'),
				"TransDate2"=>date('Y-m-d H:i:s'),
				'GodownID' =>$GateControlDetails->GodownID,
				'CenterID' =>$GateControlDetails->CenterID,
				'TypeID' =>$TypeID,
				'PartyID' =>$GateControlDetails->PartyID,
				'TType'=>$GateControlDetails->TType,
				'TType2'=> $GateControlDetails->TType2,
				'AccountID'=>$GateControlDetails->AccountID,
				'ItemID'=>$GateControlDetails->ItemID,
				'OrderQty'=>$Qty,
				'BilledQty'=>$Qty,
				"PurchRate"=>0,
				"SaleRate"=>0,
				"BasicRate"=>0,
				'CaseQty'=>1,
				'final_rate'=>0,
				'SuppliedIn'=>$GateControlDetails->unit,
				'cgst'=>0,
				'sgst'=>0,
				'igst'=>0,
				'Ordinalno'=>1,
				'UserID'=>$UserID,
				'DiscAmt'=>0,
				);
				$this->db->insert(db_prefix() . 'history',$Item_array);
			}
		}else{
			$Qty = 0;
			$Billno = null;
			//echo "<PRE>";
			//print_r($WithdrawList);die;
			foreach($WithdrawList as $key=>$val){
				if(empty($val['WithdrawQty'])){
					$val['WithdrawQty'] = 0;
				}
				$Qty += $val['WithdrawQty'];
				$Billno = $val['EditTransID'];
				$update_array = array(
				"Weight"=>$val['WithdrawQty'],
				);
				$this->db->where('tblstockInventory.BookingID',$val['BookingID']);
				$this->db->where('tblstockInventory.GateINID',$val['GateINID']);
				$this->db->where('tblstockInventory.QCID',$val['QCID']);
				$this->db->where('tblstockInventory.TransID',$Billno);
				//$this->db->where('tblstockInventory.TType','DW');
				$this->db->where('tblstockInventory.ItemID',$requestData['ItemID']);
				$this->db->update(db_prefix() . 'stockInventory',$update_array);
			}
			$Item_array = array(
			'OrderQty'=>$Qty,
			'BilledQty'=>$Qty,
			);
			$this->db->where('tblhistory.OrderID',$Billno);
			$this->db->update(db_prefix() . 'history',$Item_array);
			$Rtn= true;
		}
		return $Rtn;
	}
	//===================== Get Stack list against GateIN ID =======================
	public function GetGateINIDStackList($BookingID,$GateINID)
	{
		$this->db->select('tblstockInventory.*');
		$this->db->from(db_prefix() .'stockInventory');
		$this->db->where('tblstockInventory.BookingID', $BookingID);
		$this->db->where('tblstockInventory.GateINID', $GateINID);
		$StockList = $this->db->get()->result_array();
		return $StockList;
	}
	// Update RO QC parameter Lot Wise
	public function UpdateLotWiseQc($requestData)
	{
		$SrNo = 1;
		$StackDetails = $requestData['StackList'];
		$ItemWiseQCParameterList = $this->GetItemWiseQCParameterList($requestData['ItemID']);
		$QC_for = $requestData['QC_for'];
		$BookingID = $requestData['BookingID'];
		$GateINID = $requestData['GateINID'];
		//print_r($ItemWiseQCParameterList);
		/*echo "<pre>";
		print_r($StackDetails);*/
		$HOQCStatus = "NA";
		foreach($StackDetails as $key=>$val){
			$update_array = array(
			"Weight"=>$val['WeightMT'],
			"BagQty"=>$val['BagQty']
			);
			if($QC_for == "RO"){
				$update_array["ROQCApprove"] = "Y";
				}elseif($QC_for == "HO" & $val['QcApproval'] == "Y"){
				$HOQCStatus = "Y";
				$update_array["HOQCApprove"] = "Y";
			}
			//print_r($update_array);
			$this->db->where('tblstockInventory.BookingID',$BookingID);
			$this->db->where('tblstockInventory.GateINID',$GateINID);
			$this->db->where('tblstockInventory.QCID',$val["QCID"]);
			if($this->db->update(db_prefix() . 'stockInventory',$update_array)){
				$purch_amt = $val['WeightMT'] * ($requestData['basic_rate'] * 10);
				$Netweight = ($val['WeightMT'] * 10);
				$GetQcMinMax = $this->GetQcMinMax($requestData['ItemID']);
				foreach($ItemWiseQCParameterList as $QcKey=>$QcVal){
					$parameterDeductionMatrix = $this->GateControl_model->GetParameterDeductionMatrix($requestData['ItemID'] , $QcVal["ItemParameterID"]);
					$parameterValueToCheck = $val[$QcVal["ItemParameterID"]];
					// min value
					$minVal = floor($val[$QcVal["ItemParameterID"]]);
					// Max Value
					$maxVal = ceil($val[$QcVal["ItemParameterID"]]);
					$BaseValue = 2;
					foreach($GetQcMinMax as $k=>$v){
						if($v["ItemParameterID"] == $QcVal["ItemParameterID"]){
							$BaseValue = $v["BaseValue"];
						}
					}
					if($QcVal["CalculationBy"] == "2"){
						//Calculate by amount
						if($parameterValueToCheck <= $BaseValue){
							$deductionAmt = 0;
						}else{
							$deductionAmt = 0;
							$minPer = 0;
							$maxPer = 0;
							foreach($parameterDeductionMatrix as $innerValue)
							{
								if($minVal == $innerValue['Value']){
									$minPer = $innerValue['Deduction'];
								}elseif($maxVal == $innerValue['Value']){
									$maxPer = $innerValue['Deduction'];
								}
							}
							$diff = $parameterValueToCheck - $minVal;
							$point_deductionAmtPer_qtls = 12 * $diff;
							$deductionAmt = $Netweight * $minPer;
							$deductionAmt2 = $Netweight * $point_deductionAmtPer_qtls;
							$deductionAmt += $deductionAmt2;
						}
						}else{
						//Calculate by percent
						$minPer = 0;
						$maxPer = 0;
						foreach($parameterDeductionMatrix as $innerValue){
							if($minVal == $innerValue['Value']){
								$minPer = $innerValue['Deduction'];
							}elseif($maxVal == $innerValue['Value']){
								$maxPer = $innerValue['Deduction'];
							}
						}
						$diff = $maxPer - $minPer;
						if($parameterValueToCheck <= $BaseValue){
							$valDeff = 0;
							$deductionAmt = 0;
							}else{
							$valDeff = $parameterValueToCheck - $minVal;
							$finalPer = $minPer + ($valDeff * $diff);
							$deductionAmt = $purch_amt * ($finalPer / 100);
						}
					}
					$data2 = array(
					'deductionAmt'=>$deductionAmt,
					);
					if($QC_for == "RO"){
						$data2["EParameterValue"] = $val[$QcVal["ItemParameterID"]];
						$data2["HParameterValue"] = $val[$QcVal["ItemParameterID"]];
						}elseif($QC_for == "HO"){
						$data2["HParameterValue"] = $val[$QcVal["ItemParameterID"]];
					}
					/*echo "<br>";
					print_r($data2);*/
					$this->db->where('tblQCParameterValues.BookingID',$BookingID);
					$this->db->where('tblQCParameterValues.Gate_in_ID',$GateINID);
					$this->db->where('tblQCParameterValues.layer_number',$val["QCID"]);
					$this->db->where('tblQCParameterValues.ItemParameterID',$QcVal["ItemParameterID"]);
					$this->db->where('tblQCParameterValues.TType',"F");
					$this->db->update(db_prefix() . 'QCParameterValues',$data2);
				}
				$SrNo++;
			}
		}
		// Update Gate Control status
		$this->db->where('tblGateMaster.BookingID',$BookingID);
		$this->db->where('tblGateMaster.Gate_in_ID',$GateINID);
		if($QC_for == "RO"){
			$gate_update = array(
			"status"=>14,
			"IsQcUpdate"=>"Y"
			);
			}elseif($QC_for == "HO"){
			$gate_update = array(
			"status"=>15,
			"IsHoUpdate"=>$HOQCStatus
			);
		}
		$this->db->update(db_prefix() . 'GateMaster',$gate_update);
		//die;
		return $SrNo;
	}
	public function GetItemWiseQCParameterList($ItemID)
	{
		$this->db->select('tblItemQCParameter.*');
		$this->db->from(db_prefix() .'ItemQCParameter');
		$this->db->where('tblItemQCParameter.ItemID', $ItemID);
		$this->db->where('tblItemQCParameter.Status', "Y");
		$Parameter = $this->db->get()->result_array();
		return $Parameter;
	}
	public function updateGrossWeightDetails($BookingID, $GateInID , $gross_weight)
	{
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		//Insert previous record in history table before updating
		$gate_control_details = $this->fetch_gate_master_details($BookingID, $GateInID);
		$TType = $gate_control_details->TType;
		if($gate_control_details->LWUserID != NULL){
			$insert_array = array(
			'Booking_ID' => $BookingID,
			'Gate_IN_ID' => $GateInID,
			'name' => 'Gross Weight',
			'value' => $gate_control_details->LoadedWeight,
			'UserID' => $gate_control_details->LWUserID,
			'TransDate' => $gate_control_details->LWTransDate,
			);
			$this->db->insert('tblGateMaster_history', $insert_array);
		}
		$new_data = array(
		"LoadedWeight"=> $gross_weight * 10,
		"LWUserID"=>$user_Details->staffid,
		"LWTransDate"=>date('Y-m-d H:i:s')
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateInID);
		if($this->db->update(db_prefix() . 'GateMaster',$new_data)){
			if($TType == "P" || $TType == "D" || $TType == "A" || $TType == "T"){
				if($gate_control_details->TWUserID != NULL){
					$Netweight = (($gross_weight * 10) - $gate_control_details->TareWeight) / 10;
					$Netweight = number_format($Netweight, 2, '.', '');
					$data_array_result = array(
					'Cases'=>$Netweight,
					'OrderQty'=>$Netweight,
					'BilledQty'=>$Netweight,
					);
					$this->db->where('BillID', $BookingID);
					$this->db->where('OrderID', $GateInID);
					$this->db->update(db_prefix() . 'history',$data_array_result);
				}
			}
			return true;
		}
		return false;
	}
	public function updateCleaningDetails($BookingID, $GateInID , $fm_cleaning)
	{
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		//Insert previous record in history table before updating
		$gate_control_details = $this->fetch_gate_master_details($BookingID, $GateInID);
		if($gate_control_details->FMQty != NULL){
			$insert_array = array(
			'Booking_ID' => $BookingID,
			'Gate_IN_ID' => $GateInID,
			'name' => 'FM Cleaning',
			'value' => $gate_control_details->FMQty,
			'UserID' => $gate_control_details->FMUserID,
			'TransDate' => $gate_control_details->FMTransDate,
			);
			$this->db->insert('tblGateMaster_history', $insert_array);
		}
		// update new record
		$updated_array = array(
		"FMQty"=>$fm_cleaning,
		"FMUserID"=>$user_Details->staffid,
		"FMTransDate"=>date('Y-m-d H:i:s')
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateInID);
		if($this->db->update(db_prefix() . 'GateMaster',$updated_array)){
			return true;
		}
		return false;
	}
	public function updateBagWeightDetails($data)
	{
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		$GateINID = $data['GateINID'];
		$BookingID = $data['BookingID'];
		$TransID = $data['TransID'];
		$bag_weight = $data['bag_weight'];
		$basic_rate_per_kg = ($data['basic_rate'] / 100);
		$amount = $basic_rate_per_kg * $bag_weight;
		// Move data to Other Deduction audit log table
		$Pre_other_deduction = $this->GetActualOtherDeductionList($BookingID,$GateINID);
		foreach($Pre_other_deduction as $pKey=>$pval){
			if($pval["ItemID"] == "BG"){
				$pre_data = array(
				"BookingID"=>$pval["BookingID"],
				"GateINID"=>$pval["GateINID"],
				"TransID"=>$pval["TransID"],
				"ItemID"=>$pval["ItemID"],
				"Amount"=>$pval["Amount"],
				"UserID"=>$pval["UserID"],
				"TransDate"=>$pval["TransDate"],
				"TransDate2"=>date('Y-m-d H:i:s')
				);
				$this->db->insert('tblotherdeduction_history',$pre_data);
			}
		}
		$this->db->where('BookingID',$BookingID);
		$this->db->where('GateINID',$GateINID);
		//$this->db->where('TransID',$TransID);
		$this->db->where('ItemID',"BG");
		$this->db->delete('tblotherdeduction');
		$data_array = array(
		"BookingID"=>$BookingID,
		"GateINID"=>$GateINID,
		"TransID"=>$TransID,
		"ParticularItemID"=>"BG",
		"ItemID"=>"BG",
		"quantity"=>$bag_weight,
		"Amount"=>$amount,
		"UserID"=>$this->session->userdata('username'),
		"TransDate"=>date('Y-m-d H:i:s')
		);
		if($this->db->insert('tblotherdeduction',$data_array)){
			return true;
		}
		return false;
	}
	public function AddEditEmptyWeightForWithdraw($BookingID, $GateINID,$tare_weight)
	{
		$new_data = array(
		"TareWeight"=> $tare_weight * 10,
		"TWUserID"=>$UserID,
		"TWTransDate"=>date('Y-m-d H:i:s')
		);
		if($gate_control_details->TWUserID == NULL){
			$new_data['status'] = '3';
		}
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		if($this->db->update(db_prefix() . 'GateMaster',$new_data)){
			return true;
		}
	}
	public function AddEditLoadedWeightForWithdraw($BookingID, $GateINID,$LoadedWeight)
	{
		$new_data = array(
		"LoadedWeight"=> $LoadedWeight * 10,
		"LWUserID"=>$UserID,
		"LWTransDate"=>date('Y-m-d H:i:s')
		);
		if($gate_control_details->LWUserID == NULL){
			$new_data['status'] = '5';
		}
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		if($this->db->update(db_prefix() . 'GateMaster',$new_data)){
			return true;
		}
	}
	public function updateTareWeightDetails($BookingID, $GateINID,$tare_weight)
	{
		$user_Details = $this->getStaffNameFromAccountID($this->session->userdata('username'));
		if(empty($user_Details)){
			$UserID = "Auto";
			}else{
			$UserID = $user_Details->staffid;
		}
		//Insert previous record in history table before updating
		$gate_control_details = $this->fetch_gate_master_details($BookingID, $GateINID);
		$status = "";
		if($gate_control_details->TWUserID != NULL){
			$status = 'edit';
			$insert_array = array(
			'Booking_ID' => $BookingID,
			'Gate_IN_ID' => $GateINID,
			'name' => 'Tare Weight',
			'value' => $gate_control_details->TareWeight,
			'UserID' => $gate_control_details->TWUserID,
			'TransDate' => $gate_control_details->TWTransDate,
			);
			$this->db->insert('tblGateMaster_history', $insert_array);
			}else{
			$status = "add";
		}
		$new_data = array(
		"TareWeight"=> $tare_weight * 10,
		"TWUserID"=>$UserID,
		"TWTransDate"=>date('Y-m-d H:i:s')
		);
		$this->db->where('BookingID', $BookingID);
		$this->db->where('Gate_in_ID', $GateINID);
		if($this->db->update(db_prefix() . 'GateMaster',$new_data)){
			$GateControlDetails = $this->GateControl_model->GetControlDetails($BookingID,$GateINID);
			if($GateControlDetails->state)
			{
				$state = $GateControlDetails->state;
			}
			else{
				$state = $GateControlDetails->ClientState;
			}
			$GetTCS = $this->GetTCSDetails();
			$tcsPerValue = $GetTCS[0]['tcs'];
			//if($GateControlDetails->TType == "P" || $GateControlDetails->TType == "A" || $GateControlDetails->TType == "T" || $GateControlDetails->TType == "D"){
			$BrokerID = $GateControlDetails->BrokerID;
			$DOID = $GateControlDetails->DOID;
			$FY = $GateControlDetails->FY;
			$PlantID = $GateControlDetails->PlantID;
			if($GateControlDetails->TType == "S"){
				$Netweight = ($GateControlDetails->TareWeight - $GateControlDetails->LoadedWeight) / 10;
				}else{
				$Netweight = ($GateControlDetails->LoadedWeight - $GateControlDetails->TareWeight) / 10;
			}
			$Netweight = number_format($Netweight, 2, '.', ''); // MT Weight
			$BasicRate = ($GateControlDetails->basic_rate * 10); // Rate Per MT
			$saleRate = ($BasicRate + ($BasicRate * $GateControlDetails->taxrate) / 100);
			$ItemCount = 1;
			$BasicAmt2 = 0;
			$NetAmt2 = 0;
			$GstAmt2 = 0;
			if($GateControlDetails->CustomerType == 1){
				$BasicAmt = $Netweight * $BasicRate;
				$GstAmt = 0;
				$NetAmt = $BasicAmt;
				$cgst = 0.00;
				$sgst = 0.00;
				$igst = 0.00;
				$cgstAmt = 0;
				$sgstAmt = 0;
				$igstAmt = 0;
				}else{
				if($GateControlDetails->TType == "S"){
					$GetCurrentRate = $this->GetCurrentRate($GateControlDetails->CenterID,$GateControlDetails->ItemID);
					$CurrentRate = $GetCurrentRate->Rate;
					$DOWeightMT = $GateControlDetails->Cases;
					$MinWtMT = $DOWeightMT - ($DOWeightMT * 0.02);
					$MaxWtMT = $DOWeightMT + ($DOWeightMT * 0.02);
					if($MaxWtMT < $Netweight && $BasicRate < $CurrentRate){
						$BasicAmt = $DOWeightMT * $BasicRate;
						$BeyondWtMT = $Netweight - $DOWeightMT;
						$BasicAmt += $BeyondWtMT * ($CurrentRate * 10);
						$BasicAmt2 = $BeyondWtMT * ($CurrentRate * 10); // use for DO Weight beyond weight record store in history
						$ItemCount++;
						$GstAmt = $BasicAmt * ($GateControlDetails->taxrate / 100);
						$NetAmt = $BasicAmt + $GstAmt;
						$GstAmt2 = $BasicAmt2 * ($GateControlDetails->taxrate / 100);
						$NetAmt2 = $BasicAmt2 + $GstAmt2;
						}else{
						$BasicAmt = $Netweight * $BasicRate;
						$GstAmt = $BasicAmt * ($GateControlDetails->taxrate / 100);
						$NetAmt = $BasicAmt + $GstAmt;
					}
					}else{
					$BasicAmt = $Netweight * $BasicRate;
					$GstAmt = $BasicAmt * ($GateControlDetails->taxrate / 100);
					$NetAmt = $BasicAmt + $GstAmt;
				}
				if($state == "MH"){
					$cgst = $GateControlDetails->taxrate/2;
					$sgst = $GateControlDetails->taxrate/2;
					$igst = 0.00;
					$cgstAmt = $GstAmt/2;
					$sgstAmt = $GstAmt/2;
					$cgstAmt2 = $GstAmt2/2;
					$sgstAmt2 = $GstAmt2/2;
					$igstAmt = 0;
					$igstAmt2 = 0;
					}else{
					$cgst = 0.00;
					$sgst = 0.00;
					$igst = $GateControlDetails->taxrate;
					$cgstAmt = 0;
					$sgstAmt = 0;
					$igstAmt = $GstAmt;
					$cgstAmt2 = 0;
					$sgstAmt2 = 0;
					$igstAmt2 = $GstAmt2;
				}
			}
			if($GateControlDetails->istcs == "1"){
				$TcsAmt = ($NetAmt + $NetAmt2) * ($tcsPerValue / 100);
				}else{
				$TcsAmt = 0;
			}
			if($status == "add"){
				if($GateControlDetails->vat == ''){
					$bt = 'N';
					}else{
					$bt = 'Y';
				}
				//next_deposit_number_for_kirti
				if($GateControlDetails->TType == "P"){
					$TypeID = "SP";
					$new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
					$Billno = "PO".$FY.$new_poNumber;
					}else if($GateControlDetails->TType == "A"){
					$TypeID = "A";
					$new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
					$Billno = "PO".$FY.$new_poNumber;
					}else if($GateControlDetails->TType == "T"){
					$TypeID = "TF";
					$new_poNumber = get_option2('next_purchase_number_for_kirti',$FY);
					$Billno = "PO".$FY.$new_poNumber;
					}else if($GateControlDetails->TType == "D") {
					$TypeID = "DW";
					$new_poNumber = get_option2('next_deposit_number_for_kirti',$FY);
					$Billno = "DO".$FY.$new_poNumber;
					}else if($GateControlDetails->TType == "S") {
					$TypeID = "SP";
					$new_TaxNumber = get_option2('next_tax_number_for_kirti',$FY);
					$Billno = "TAX".$FY.$new_TaxNumber;
				}
				$data_array = array(
				'PlantID'=>$PlantID,
				'FY'=>$FY,
				'BT'=>$bt,
				"Transdate"=>date('Y-m-d H:i:s'),
				'PartyID' =>$GateControlDetails->PartyID,
				'AccountID'=>$GateControlDetails->AccountID,
				'CenterID' =>$GateControlDetails->CenterID,
				'WHID' =>$GateControlDetails->GodownID,
				'cgstamt'=>($cgstAmt + $cgstAmt2),
				'sgstamt'=>($sgstAmt + $sgstAmt2),
				'igstamt'=>($igstAmt + $igstAmt2),
				"ItCount"=>1,
				);
				if($GateControlDetails->TType == "S"){
					$data_array["SalesID"] = $Billno;
					$data_array["OrderID"] = $GateINID;
					$data_array["DOID"] = $DOID;
					$data_array["ShipTo"] = $FY;
					$data_array["BrokerID"] = $BrokerID;
					$data_array["gstno"] = $GateControlDetails->vat;
					$data_array["sale_qty"] = $Netweight;
					$data_array["SaleAmt"] = ($BasicAmt + $BasicAmt2);
					$data_array["BillAmt"] = ($NetAmt + $NetAmt2);
					$data_array["RndAmt"] = ($NetAmt + $NetAmt2);
					$data_array["UserID"] = $this->session->userdata('username');
					$data_array["tcs"] = $tcsPerValue;
					$data_array["tcsAmt"] = $TcsAmt;
					}else{
					$data_array["PurchID"] = $Billno;
					$data_array["TransID"] = $GateINID;
					$data_array["Transdate"] = date('Y-m-d H:i:s');
					$data_array["Invoiceno"] = NULL;
					$data_array["Invoicedate"] = NULL;
					$data_array["Purchamt"] = ($BasicAmt + $BasicAmt2);
					$data_array["Invamt"] = ($NetAmt + $NetAmt2);
					$data_array["RoundOffAmt"] = NULL;
					$data_array["Userid"] = $UserID;
				}
				if($GateControlDetails->TType == "D"){
					$this->db->insert(db_prefix() . 'depositemaster',$data_array);
					}elseif($GateControlDetails->TType == "S"){
					$this->db->insert(db_prefix() . 'salesmaster',$data_array);
					} else {
					$data_array["Discamt"] = 0;
					$data_array["Frtamt"] = 0;
					$data_array["Othamt"] = 0;
					$data_array["OthAccountID"] = NULL;
					$data_array["TDS"] = NULL;
					$data_array["tds_per"] = NULL;
					$data_array["tdsAmt"] = NULL;
					$this->db->insert(db_prefix() . 'purchasemaster',$data_array);
				}
				if($this->db->affected_rows() > 0){
					if($GateControlDetails->TType == "D"){
						$this->increment_next_number('next_deposit_number_for_kirti');
						}elseif($GateControlDetails->TType == "S"){
						// Increment Sale Invoice number
						$this->increment_next_number('next_tax_number_for_kirti');
						}else {
						$this->increment_next_number('next_purchase_number_for_kirti');
					}
					// Comman column
					$Item_array = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'cnfid' =>1,
					'OrderID' =>$GateINID,
					"TransID"=>$Billno,
					"TransDate"=>date('Y-m-d H:i:s'),
					"TransDate2"=>date('Y-m-d H:i:s'),
					'GodownID' =>$GateControlDetails->GodownID,
					'CenterID' =>$GateControlDetails->CenterID,
					'TypeID' =>$TypeID,
					'PartyID' =>$GateControlDetails->PartyID,
					'TType'=>$GateControlDetails->TType,
					'TType2'=> $GateControlDetails->TType2,
					'AccountID'=>$GateControlDetails->AccountID,
					'ItemID'=>$GateControlDetails->ItemID,
					"PurchRate"=>$TradeRate,
					"SaleRate"=>$TradeSaleRate,
					"BasicRate"=>$TradeRate,
					'CaseQty'=>1,
					'final_rate'=>$BasicRate,
					'SuppliedIn'=>$GateControlDetails->unit,
					'cgst'=>$cgst,
					'sgst'=>$sgst,
					'igst'=>$igst,
					'Ordinalno'=>1,
					'UserID'=>$UserID,
					'DiscAmt'=>0,
					);
					if($ItemCount > 1){
						$Item_array["Cases"] = $DOWeightMT;
						$Item_array["OrderQty"] = $DOWeightMT;
						$Item_array["BilledQty"] = $DOWeightMT;
						$Item_array["cgstamt"] = $cgstAmt;
						$Item_array["sgstamt"] = $sgstAmt;
						$Item_array["igstamt"] = $igstAmt;
						$Item_array["OrderAmt"] = $BasicAmt;
						$Item_array["ChallanAmt"] = $BasicAmt;
						$Item_array["NetOrderAmt"] = $NetAmt;
						$Item_array["NetChallanAmt"] = $NetAmt;
						}else{
						$Item_array["Cases"] = $Netweight;
						$Item_array["OrderQty"] = $Netweight;
						$Item_array["BilledQty"] = $Netweight;
						$Item_array["cgstamt"] = $cgstAmt;
						$Item_array["sgstamt"] = $sgstAmt;
						$Item_array["igstamt"] = $igstAmt;
						$Item_array["OrderAmt"] = $BasicAmt;
						$Item_array["ChallanAmt"] = $BasicAmt;
						$Item_array["NetOrderAmt"] = $NetAmt;
						$Item_array["NetChallanAmt"] = $NetAmt;
					}
					if($GateControlDetails->TType != "S"){
						$Item_array["BillID"] = $BookingID;
						$this->db->insert(db_prefix() . 'history',$Item_array);
						}else{
						// As per DO  entry add in History
						$this->db->insert(db_prefix() . 'history',$Item_array);
						// Outward weight beyond qty entry
						if($BasicAmt2 > 0){
							$Item_array["Cases"] = $BeyondWtMT;
							$Item_array["OrderQty"] = $BeyondWtMT;
							$Item_array["BilledQty"] = $BeyondWtMT;
							$Item_array["cgstamt"] = $cgstAmt2;
							$Item_array["sgstamt"] = $sgstAmt2;
							$Item_array["igstamt"] = $igstAmt2;
							$Item_array["OrderAmt"] = $BasicAmt2;
							$Item_array["ChallanAmt"] = $BasicAmt2;
							$Item_array["NetOrderAmt"] = $NetAmt2;
							$Item_array["NetChallanAmt"] = $NetAmt2;
							$this->db->insert(db_prefix() . 'history',$Item_array);
						}
					}
					// Add Account Ledger Entry
					/*if($GateControlDetails->TType == "S"){
						$this->GenerateLedgerEntryForSale($BookingID,$GateINID);
						}else if($GateControlDetails->TType == "P"){
						$this->GenerateLedgerEntryForPurchase($BookingID,$GateINID,$NetAmt);
					}*/
					//Add Account Entry in Credit Limit Master
					$purchasedetails = $this->fetch_purchase_details($GateINID);
					$purchamt = $purchasedetails->Purchamt ?? 0;
					$CreditAmtRaw = $purchamt * 0.60;
					$CreditAmt = round($CreditAmtRaw, 2);
					$LimitEntry = array(
						'AccountID'=>$GateControlDetails->AccountID,
						'PartyID'=>$GateControlDetails->PartyID,
						'GateINID'=>$GateINID,
						'PurchAmt'=>$purchasedetails->Purchamt,
						'CreditAmt'=>$CreditAmt,
						'UserID'=>$UserID,
						'TransDate'=>date('Y-m-d H:i:s'),
						'Type'=>'CR',
						'Status'=>'Y',
					);
					$this->db->insert(db_prefix() . 'CreditLimitMaster',$LimitEntry);
				}
				}elseif($status == "edit"){
				$data_array = array(
				'cgstamt'=>($cgstAmt + $cgstAmt2),
				'sgstamt'=>($sgstAmt + $sgstAmt2),
				'igstamt'=>($igstAmt + $igstAmt2),
				);
				if($GateControlDetails->TType != "S"){
					$data_array["Purchamt"] = ($BasicAmt + $BasicAmt2);
					$data_array["Invamt"] = ($NetAmt + $NetAmt2);
					}else{
					$data_array["sale_qty"] = $Netweight;
					$data_array["SaleAmt"] = ($BasicAmt + $BasicAmt2);
					$data_array["BillAmt"] = ($NetAmt + $NetAmt2);
					$data_array["RndAmt"] = ($NetAmt + $NetAmt2);
				}
				if($GateControlDetails->TType == "D"){
					$this->db->where('TransID', $GateINID);
					$this->db->update(db_prefix() . 'depositemaster',$data_array);
					}if($GateControlDetails->TType == "S"){
					$this->db->where('OrderID', $GateINID);
					$this->db->update(db_prefix() . 'salesmaster',$data_array);
					} else {
					$this->db->where('TransID', $GateINID);
					$this->db->update(db_prefix() . 'purchasemaster',$data_array);
				}
				if($this->db->affected_rows() > 0)
				{
					$this->db->where('OrderID', $GateINID);
					$this->db->delete(db_prefix() . 'history');
					// Comman column
					$Item_array = array(
					'PlantID'=>$PlantID,
					'FY'=>$FY,
					'cnfid' =>1,
					'OrderID' =>$GateINID,
					"TransID"=>$Billno,
					"TransDate"=>date('Y-m-d H:i:s'),
					"TransDate2"=>date('Y-m-d H:i:s'),
					'GodownID' =>$GateControlDetails->GodownID,
					'CenterID' =>$GateControlDetails->CenterID,
					'TypeID' =>$TypeID,
					'PartyID' =>$GateControlDetails->PartyID,
					'TType'=>$GateControlDetails->TType,
					'TType2'=> $GateControlDetails->TType2,
					'AccountID'=>$GateControlDetails->AccountID,
					'ItemID'=>$GateControlDetails->ItemID,
					"PurchRate"=>$TradeRate,
					"SaleRate"=>$TradeSaleRate,
					"BasicRate"=>$TradeRate,
					'CaseQty'=>1,
					'final_rate'=>$BasicRate,
					'SuppliedIn'=>$GateControlDetails->unit,
					'cgst'=>$cgst,
					'sgst'=>$sgst,
					'igst'=>$igst,
					'Ordinalno'=>1,
					'UserID'=>$UserID,
					'DiscAmt'=>0,
					);
					if($ItemCount > 1){
						$Item_array["Cases"] = $DOWeightMT;
						$Item_array["OrderQty"] = $DOWeightMT;
						$Item_array["BilledQty"] = $DOWeightMT;
						$Item_array["cgstamt"] = $cgstAmt;
						$Item_array["sgstamt"] = $sgstAmt;
						$Item_array["igstamt"] = $igstAmt;
						$Item_array["OrderAmt"] = $BasicAmt;
						$Item_array["ChallanAmt"] = $BasicAmt;
						$Item_array["NetOrderAmt"] = $NetAmt;
						$Item_array["NetChallanAmt"] = $NetAmt;
						}else{
						$Item_array["Cases"] = $Netweight;
						$Item_array["OrderQty"] = $Netweight;
						$Item_array["BilledQty"] = $Netweight;
						$Item_array["cgstamt"] = $cgstAmt;
						$Item_array["sgstamt"] = $sgstAmt;
						$Item_array["igstamt"] = $igstAmt;
						$Item_array["OrderAmt"] = $BasicAmt;
						$Item_array["ChallanAmt"] = $BasicAmt;
						$Item_array["NetOrderAmt"] = $NetAmt;
						$Item_array["NetChallanAmt"] = $NetAmt;
					}
					if($GateControlDetails->TType != "S"){
						$Item_array["BillID"] = $BookingID;
						$this->db->insert(db_prefix() . 'history',$Item_array);
						}else{
						// As per DO  entry add in History
						$this->db->insert(db_prefix() . 'history',$Item_array);
						// Outward weight beyond qty entry
						if($BasicAmt2 > 0){
							$Item_array["Cases"] = $BeyondWtMT;
							$Item_array["OrderQty"] = $BeyondWtMT;
							$Item_array["BilledQty"] = $BeyondWtMT;
							$Item_array["cgstamt"] = $cgstAmt2;
							$Item_array["sgstamt"] = $sgstAmt2;
							$Item_array["igstamt"] = $igstAmt2;
							$Item_array["OrderAmt"] = $BasicAmt2;
							$Item_array["ChallanAmt"] = $BasicAmt2;
							$Item_array["NetOrderAmt"] = $NetAmt2;
							$Item_array["NetChallanAmt"] = $NetAmt2;
							$this->db->insert(db_prefix() . 'history',$Item_array);
						}
					}
					// Add Account Ledger Entry
					/*if($GateControlDetails->TType == "S"){
						$this->GenerateLedgerEntryForSale($BookingID,$GateINID);
						}else if($GateControlDetails->TType == "P"){
						$this->GenerateLedgerEntryForPurchase($BookingID,$GateINID,$NetAmt);
					}*/
					//Add Account Entry in Credit Limit Master
					$purchasedetails = $this->fetch_purchase_details($GateINID);
					$creditlimitData = $this->fetch_creditlimit_data($GateINID);
					$purchamt = $purchasedetails->Purchamt ?? 0;
					$CreditAmtRaw = $purchamt * 0.60;
					$CreditAmt = round($CreditAmtRaw, 2);
					if (empty($creditlimitData))
					{
						$LimitEntry = array(
							'AccountID'=>$GateControlDetails->AccountID,
							'PartyID'=>$GateControlDetails->PartyID,
							'GateINID'=>$GateINID,
							'PurchAmt'=>$purchasedetails->Purchamt,
							'CreditAmt'=>$CreditAmt,
							'UserID'=>$UserID,
							'TransDate'=>date('Y-m-d H:i:s'),
							'Type'=>'CR',
							'Status'=>'Y',
						);
						$this->db->insert(db_prefix() . 'CreditLimitMaster',$LimitEntry);
					}
					else
					{
						$updateentry = array(
								'PurchAmt'=>$purchasedetails->Purchamt,
								'CreditAmt'=>$CreditAmt,
							);
						$this->db->where('tblCreditLimitMaster.GateINID',$GateINID);
						$this->db->update(db_prefix() . 'CreditLimitMaster',$updateentry);
					}
				}
			}
			//}
			return true;
		}
		return false;
	}
	public function fetch_layer_details($BookingID, $GateInID)
	{
		$this->db->select('tblLayerMaster.*');
		$this->db->where('tblLayerMaster.BookingID', $BookingID);
		$this->db->where('tblLayerMaster.Gate_in_ID', $GateInID);
		$data = $this->db->get('tblLayerMaster')->result_array();
		return $data;
	}
	public function fetch_layer_qc_details($BookingID, $GateInID, $Type){
		$this->db->select('tblQCParameterValues.*');
		$this->db->where('tblQCParameterValues.BookingID', $BookingID);
		$this->db->where('tblQCParameterValues.Gate_in_ID', $GateInID);
		$this->db->where('tblQCParameterValues.TType', $Type);
		$data = $this->db->get('tblQCParameterValues')->result_array();
		return $data;
	}
}
