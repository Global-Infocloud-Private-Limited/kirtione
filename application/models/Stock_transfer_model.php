<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stock_transfer_model extends App_Model
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    public function GetGodownData()
    {
        $PlantID = $this->session->userdata('root_company');
        return $this->db->get(db_prefix().'warehouse')->result_array();
    }
    
    public function get_table_data(){
        $selected_company = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'items.id as itemid,'.db_prefix() . 'items.UserId as useriditem,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            ItemName,ItemID,DivisionID,subgroup_id,hsn_code,isactive,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items_sub_groups.name as subgroup_name,unit');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.DivisionID', 'left');
        $this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.subgroup_id', 'left');
        $this->db->order_by(db_prefix() . 'items.ItemID', 'ASC');
        
        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);
        return $this->db->get()->result_array();
    }
    
    public function GetItemDetailsbyID($ItemID)
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
    
    public function increment_next_number()
    {
        // Update next Stock Transfer number in settings
        $FY = $this->session->userdata('finacial_year'); 
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('name', 'next_stock_trf_number_for_kirti');
       
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
    }
    
    public function GetItemStock($selectedArray,$FromID)
    {
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $this->db->select('tblstockmaster.gtoqty,tblstockmaster.gtiqty,tblstockmaster.ItemID');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);
        $this->db->where('GodownID', $FromID);
        $this->db->where_in('ItemID', $selectedArray);
        return $this->db->get(db_prefix() . 'stockmaster')->result_array();
    }
    
    public function GetItemStock2($selectedArray,$TrnsTo)
    {
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $this->db->select('tblstockmaster.gtoqty,tblstockmaster.gtiqty,tblstockmaster.ItemID');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);
        $this->db->where('GodownID', $TrnsTo);
        $this->db->where_in('ItemID', $selectedArray);
        return $this->db->get(db_prefix() . 'stockmaster')->result_array();
    }
    
    public function CheckStockRecord($ItemID,$TrnsTo)
    {
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $this->db->select('tblstockmaster.ItemID');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);
        $this->db->where('GodownID', $TrnsTo);
        $this->db->where('ItemID', $ItemID);
        return $this->db->get(db_prefix() . 'stockmaster')->result_array();
    }
    
    // Get Trans List
    public function GetTransData()
    {
        $PlantID = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $PlantID);
        $this->db->where('FY', $FY);
        $this->db->order_by(db_prefix() . 'TransferMaster.Transdate', 'DESC');
       return $this->db->get(db_prefix().'TransferMaster')->result_array();
    }
    
    public function GetTransDetails($TransID){
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $html = "";
       $this->db->select('tblTransferMaster.TransID,tblTransferMaster.Transdate2,tblTransferMaster.TransFrom,tblTransferMaster.TransTo,tblwarehouse.w_name AS FromName,G2.w_name AS ToName');
       $this->db->join(db_prefix() . 'warehouse', 'tblwarehouse.AccountID = ' . db_prefix() . 'TransferMaster.TransFrom');
       $this->db->join(db_prefix() . 'warehouse AS G2', 'G2.AccountID = ' . db_prefix() . 'TransferMaster.TransTo');
       $this->db->where('tblTransferMaster.PlantID', $selected_company);
	   $this->db->where('tblTransferMaster.TransID', $TransID);
       $records = $this->db->get(db_prefix() . 'TransferMaster')->row();
    //   print_r( $records);
    //   die;
	  if($records){
	      $html .= '<tr><td colspan="5" style="text-align:center;">Transfer Details</td></tr>';
	      $html .= '<tr><td><b>TransferID</b></td><td>'.$TransID.'</td><td colspan="2"><b>TransferDate</b></td><td>'._d(substr($records->Transdate2,0,10)).'</td></tr>';
	      $html .= '<tr><td><b>TransferFrom</b></td><td>'.$records->FromName.'</td><td colspan="2"><b>TransferTo</b></td><td>'.$records->ToName.'</td></tr>';
	      $html .= '<tr><td><b>ItemID</b></td><td><b>ItemName</b></td><td style="text-align:center;"><b>Stock</b></td><td style="text-align:center;"><b>Qty</b></td></tr>'; 
	   $ItemDetails = $this->TransItemDetails($TransID,$records->TransFrom);
	   //print_r($ItemDetails);
	   // die;
	    $Items = array();
	    foreach($ItemDetails as $value){
	        array_push($Items,$value['ItemID']);
	    }
	   //echo $Items;
	   //die;
	   
	    $Stocks = $this->GetItemSStockDetails($Items,$records->TransFrom);
	   // print_r($Stocks);
	   // die;
	    $i= 0;
	    foreach($ItemDetails as $value){
	        foreach($Stocks as $value1){
	            if($value['ItemID']== $value1['ItemID']){
	                $PQty = 0;
                    $PRQty = 0;
                    $IQty = 0;
                    $PRDQty = 0;
                    $SQty = 0;
                    $SRTQty = 0;
                    $AQty = 0;
                    $GIQty = 0;
                    $GOQty = 0;
                    $OQty = $value["OQty"];
                    foreach ($Stocks as $stock) {
                        if($stock['TType'] == 'P'){
                            $PQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'N'){
                            $PRQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'A'){
                            $IQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'B'){
                            $PRDQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){
                            $SQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){
                            $SRTQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'X'){
                            $AQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'T'  && $stock['TType2'] == 'In'){
                            $GIQty = $stock['BilledQty'];
                        }elseif($stock['TType'] == 'T'   && $stock['TType2'] == 'Out'){
                            $GOQty = $stock['BilledQty'];
                        }
                    }
                    $stockQty = $OQty + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty + $GIQty - $GOQty;
                    
	            }
	        }
	        $ItemDetails[$i]['Stock'] = $stockQty;
                    $i++;
            $html .= '<tr>';
            $html .= '<td>'.$value['ItemID'].'</td>';
            $html .= '<td>'.$value['ItemName'].'</td>';
           // $html .= '<td style="text-align:center;">'.$value['CaseQty'].'</td>';
            // $html .= '<td style="text-align:center;">'.$value['unit'].'</td>';
            $html .= '<td style="text-align:right;">'.$stockQty.'</td>';
            $html .= '<td style="text-align:right;">'.$value['BilledQty'].'</td>';
            $html .= '</tr>';
	    }
	    $html .= '<tr><td colspan="2" style="text-align:center;border-bottom:0px solid;border-right:0px solid;border-left:0px solid;padding-top:50px;widht:50%"><b>Transfer By</b></td><td colspan="3" style="text-align:center;border-bottom:none;border-right:0px solid;border-left:0px solid;padding-top:50px;width:50%"><b>Received By</b></td></tr>';
	    
	    $records->ItemS = $ItemDetails;
	    $records->Print = $html;
	  }
     return $records;
    }
    
    
    public function GetItemSStockDetails($ItemID,$FromID)
    {
        $MainG = 0;
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        // echo $ItemID;
        // die;
        $this->db->select('tblhistory.ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');
        $this->db->from(db_prefix() .'history');
        $this->db->where_in(db_prefix() .'history.ItemID', $ItemID);
        $this->db->where(db_prefix() .'history.PlantID', $selected_company);
        $this->db->where(db_prefix() .'history.GodownID',$FromID);
        $this->db->where(db_prefix() . 'history.BillID IS NOT NULL', NULL, FALSE);
        $this->db->where(db_prefix() .'history.FY', $fy);
        $this->db->group_by('ItemID,TType,TType2');
        return $this->db->get()->result_array();
    }
    
    public function TransItemDetails($TransID,$AccountID)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblhistory.OrderID,tblhistory.ItemID,CaseQty,tblitems.unit,tblitems.ItemName,tblhistory.BilledQty,tblstockmaster.OQty');
        $this->db->from(db_prefix() .'history');
        $this->db->join('tblstockmaster ', 'tblstockmaster.ItemID = tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.GodownID= tblhistory.AccountID','LEFT');
        $this->db->join('tblitems ', 'tblitems.ItemID  = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID ');
        $this->db->where(db_prefix() .'history.OrderID', $TransID);
        $this->db->where(db_prefix() .'history.AccountID', $AccountID);
        $this->db->where(db_prefix() .'history.TType', 'T');
        $this->db->where(db_prefix() .'history.TType2', 'Out');
        $this->db->where(db_prefix() .'history.PlantID', $selected_company);
        $this->db->where(db_prefix() .'history.FY', $fy);
        return $this->db->get()->result_array();
    }
    
    function GetOLDTransDetails($TransID){
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
       $this->db->select('TransID,Transdate2,TransFrom,TransTo');
       $this->db->where('PlantID', $selected_company);
	   $this->db->where('TransID', $TransID);
       $records = $this->db->get(db_prefix() . 'TransferMaster')->row();
	  if($records){
	    $ItemDetails = $this->OLDTransItemDetails($TransID);
	    $records->ItemS = $ItemDetails;
	  }
// 	  print_r($records);
// 	  die;
	 return $records;
    }
    
    public function OLDTransItemDetails($TransID)
    {
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $this->db->select('tblhistory.OrderID,tblhistory.ItemID,tblhistory.TType2,tblhistory.AccountID,tblhistory.BilledQty,IFNULL(tblstockmaster.gtoqty,0) AS gtoqty,IFNULL(tblstockmaster.gtiqty,0) AS gtiqty');
        $this->db->from(db_prefix() .'history');
        $this->db->join('tblstockmaster ', 'tblstockmaster.ItemID = tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.GodownID = tblhistory.AccountID');
        $this->db->where(db_prefix() .'history.OrderID', $TransID);
        $this->db->where(db_prefix() .'history.PlantID', $selected_company);
        $this->db->where(db_prefix() .'history.FY', $fy);
        return $this->db->get()->result_array();
        
    }
    
}