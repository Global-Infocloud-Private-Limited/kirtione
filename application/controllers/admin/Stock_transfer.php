<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stock_transfer extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('stock_transfer_model');
        $this->load->model('accounts_master_model');
    }
    
    public function StockTransfer()
    {
        if (!has_permission_new('StockTransfer', '', 'view')) {
            access_denied('Stock Transfer');
        }
        $data['title'] = "Stock Tranfer";
        $data['GodownData'] = $this->stock_transfer_model->GetGodownData();
        $data['TransData'] = $this->stock_transfer_model->GetTransData();
        $data['Itemdata'] = $this->stock_transfer_model->get_table_data();
        $data['company_detail'] = $this->accounts_master_model->get_company_detail();
        $this->load->view('admin/Stock_transfer/stock_transfer',$data);
    }
    
    public function GetItemDetails()
    {
        $ItemID = $this->input->post('ItemID');
        $item = $this->stock_transfer_model->GetItemDetailsbyID($ItemID);
        echo json_encode($item);
    }
    
    public function SaveTransfer()
    {
        if (!has_permission_new('StockTransfer', '', 'create')) {
            access_denied('Stock Transfer');
        }
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $new_TransNumber = get_option('next_stock_trf_number_for_kirti');
        $TransNumber = 'TRS'.$FY.$new_TransNumber;
        $Transdate = to_sql_date($this->input->post('Transdate'))." ".date('H:i:s');
        $TrnsFrom = $this->input->post('TrnsFrom');
        $TrnsTo = $this->input->post('TrnsTo');
        $ItemCount = $this->input->post('ItemCount');
        $ItemCountN = $ItemCount - 1;
        $masterData = array(
            'PlantID'=> $selected_company,
            'FY'=> $FY,
            'TransID'=> $TransNumber,
            'Transdate'=> date('Y-m-d H:i:s'),
            'Transdate2'=> $Transdate,
            'TransFrom'=> $TrnsFrom,
            'TransTo'=> $TrnsTo,
            'UserID'=> $_SESSION['username'],
        );
        $this->db->insert(db_prefix() . 'TransferMaster',$masterData);
        if($this->db->affected_rows() > 0){
            
            $this->stock_transfer_model->increment_next_number();
            
            $ItemSerializedArr = $this->input->post('ItemSerializedArr');
            $ItemArray = json_decode($ItemSerializedArr, true);
            $selectedArray = array();
            for($i=0; $i<$ItemCountN; $i++) {
                $ItemID = $ItemArray[$i][0];
                array_push($selectedArray,$ItemID);
            }
            $OrdNo = 1;
            $GetItemStock = $this->stock_transfer_model->GetItemStock($selectedArray,$TrnsFrom);
            $GetItemStock2 = $this->stock_transfer_model->GetItemStock2($selectedArray,$TrnsTo);
            for($k=0; $k<$ItemCountN; $k++) {
                $ItemID = $ItemArray[$k][0];
                $ItemName = $ItemArray[$k][1];
                // $Pack = $ItemArray[$k][2];
                // $Unit = $ItemArray[$k][3];
                 $stock = $ItemArray[$k][2];
                 $Qty = $ItemArray[$k][3];
                // $Qty = $qtyCases * $Pack;
                
                /*echo "<pre>";
                print_r($GetItemStock);*/
                $CheckItemStockRecord = $this->stock_transfer_model->CheckStockRecord($ItemID,$TrnsTo);
                
                if(empty($CheckItemStockRecord)){
                    $insertStock = array(
                            'PlantID' =>$selected_company,
                            'FY' =>$FY,
                            'cnfid' =>1,
                            'ItemID' =>$ItemID,
                            'gtiqty' =>$Qty,
                            'GodownID' =>$TrnsTo,
                            'UserId' =>$_SESSION['username'],
                            //'EffDate' =>date('Y-m-d H:i:s'),
                            'EffDate' =>$Transdate
                        );
                        $this->db->insert(db_prefix() . 'stockmaster',$insertStock);
                }
                foreach($GetItemStock as $value){
                    if(strtoupper($value['ItemID']) == strtoupper($ItemID)){
                             
                        // Qty In to TransTo Godown        
                        $QtyIn = $value['gtoqty'] + $Qty;
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $FY);  
                        $this->db->where('GodownID', $TrnsFrom);  
                        $this->db->where('ItemID', $ItemID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtoqty' => $QtyIn,
                                ]);
                    }
                }
                foreach($GetItemStock2 as $value){
                    if(strtoupper($value['ItemID']) == strtoupper($ItemID)){
                        // Qty Out From TransFrom Godown      
                        $QtyOut = $value['gtiqty'] + $Qty;
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $FY);  
                        $this->db->where('GodownID', $TrnsTo);  
                        $this->db->where('ItemID', $ItemID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtiqty' => $QtyOut,
                                ]);
                    }
                }
                
                    $HistoryArrayOut = array(
                        'PlantID' =>$selected_company,
                        'FY' =>$FY,
                        'cnfid' =>'1',
                        'OrderID' =>$TransNumber,
                        'BillID' =>$TransNumber,
                        'TransID' =>$TransNumber,
                        'TransDate' =>date('Y-m-d H:i:s'),
                        'TransDate2' =>$Transdate,
                        'TType' =>'T',
                        'TType2' =>'Out',
                        'AccountID' =>$TrnsFrom,
                        'GodownID' =>$TrnsFrom,
                        'ItemID' =>$ItemID,
                        'SaleRate' =>null,
                        'BasicRate' =>null,
                        'SuppliedIn' =>'CS',
                        'OrderQty' =>$Qty,
                        'BilledQty' =>$Qty,
                        'CaseQty' =>$Pack,
                        'OrderAmt' =>null,
                        'ChallanAmt' =>null,
                        'NetOrderAmt' =>null,
                        'NetChallanAmt' =>null,
                        'Ordinalno' =>$OrdNo,
                        'UserID' =>$_SESSION['username'],
                    );
                //print_r($HistoryArrayOut);
                $this->db->insert(db_prefix() . 'history',$HistoryArrayOut);
                $OrdNo++;
                    $HistoryArrayIn = array(
                        'PlantID' =>$selected_company,
                        'FY' =>$FY,
                        'cnfid' =>'1',
                        'OrderID' =>$TransNumber,
                        'BillID' =>$TransNumber,
                        'TransID' =>$TransNumber,
                        'TransDate' =>date('Y-m-d H:i:s'),
                        'TransDate2' =>$Transdate,
                        'TType' =>'T',
                        'TType2' =>'In',
                        'AccountID' =>$TrnsTo,
                        'GodownID' =>$TrnsTo,
                        'ItemID' =>$ItemID,
                        'SaleRate' =>null,
                        'BasicRate' =>null,
                        'SuppliedIn' =>'CS',
                        'OrderQty' =>$Qty,
                        'BilledQty' =>$Qty,
                        'CaseQty' =>$Pack,
                        'OrderAmt' =>null,
                        'ChallanAmt' =>null,
                        'NetOrderAmt' =>null,
                        'NetChallanAmt' =>null,
                        'Ordinalno' =>$OrdNo,
                        'UserID' =>$_SESSION['username'],
                    );
                    //print_r($HistoryArrayIn);
                $this->db->insert(db_prefix() . 'history',$HistoryArrayIn);
                $OrdNo++;
            }
            //die;
            $next_TransNumber = get_option('next_stock_trf_number_for_kirti');
                
                $new_TransNumber = 'TRS'.$FY.$next_TransNumber;
                echo json_encode($new_TransNumber);
                die;
        }
    }
    
    public function GetTransDetails()
    {
        // POST data
        $postData = $this->input->post();
        $TransID = $this->input->post('TransID');
        // Get data
        $data = $this->stock_transfer_model->GetTransDetails($TransID);
        echo json_encode($data);
    }
    
    public function UpdateTransfer()
    {
        if (!has_permission_new('StockTransfer', '', 'edit')) {
            access_denied('Stock Transfer');
        }
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');  
        $TransID = $this->input->post('TransID');
        $Transdate = to_sql_date($this->input->post('Transdate'))." ".date('H:i:s');
        $TrnsFrom = $this->input->post('TrnsFrom');
        $TrnsTo = $this->input->post('TrnsTo');
        $ItemCount = $this->input->post('ItemCount');
        $ItemCountN = $ItemCount - 1;
        
        $OldTransDetails = $this->stock_transfer_model->GetOLDTransDetails($TransID);
        
        $masterData = array(
            
            'Transdate'=> date('Y-m-d H:i:s'),
            'Transdate2'=> $Transdate,
            'TransFrom'=> $TrnsFrom,
            'TransTo'=> $TrnsTo,
            'UserID2'=> $_SESSION['username'],
            'Lupdate'=>date('Y-m-d H:i:s'),
        );
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);  
        $this->db->where('TransID', $TransID); 
        $this->db->update(db_prefix() . 'TransferMaster', $masterData);
        
            $OldTransFrom = $OldTransDetails->TransFrom;
            $OldTransTo = $OldTransDetails->TransTo;
            // echo '<pre>';
            // print_r($OldTransDetails);
            // die;
            foreach($OldTransDetails->ItemS as $row ){
                // Qty minus to TransFrom Godown  
                $TrnsFromOLD = $row['AccountID'];
                $ItemIDOLD = $row['ItemID'];
                // echo $TrnsFromOLD;
                // die;
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $FY);  
                $this->db->where('GodownID', $TrnsFromOLD);  
                $this->db->where('ItemID', $ItemIDOLD);
                        
                if($row['TType2']=="Out"){
                    $QtyIn = $row['gtoqty'] - $row['BilledQty'];
                   
                    $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtoqty' => $QtyIn,
                                ]);
                }else{
                    $QtyIn = $row['gtiqty'] - $row['BilledQty'];
                    $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtiqty' => $QtyIn,
                                ]);
                } 
                //  echo $QtyIn;
                //     die;
            }
            
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $FY);
            $this->db->where('TType', "T");
            $this->db->where('TransID', $TransID);
            $this->db->delete(db_prefix() . 'history');
        
            $ItemSerializedArr = $this->input->post('ItemSerializedArr');
            $ItemArray = json_decode($ItemSerializedArr, true);
            // print_r($ItemArray);
            // die;
            $selectedArray = array();
            for($i=0; $i<$ItemCountN; $i++) {
                $ItemID = $ItemArray[$i][0];
                array_push($selectedArray,$ItemID);
            }
            $OrdNo = 1;
            $GetItemStock = $this->stock_transfer_model->GetItemStock($selectedArray,$TrnsFrom);
            // print_r($selectedArray);
            // die;
            $GetItemStock2 = $this->stock_transfer_model->GetItemStock2($selectedArray,$TrnsTo);
            for($k=0; $k<$ItemCountN; $k++) {
                $ItemID = $ItemArray[$k][0];
                $ItemName = $ItemArray[$k][1];
                // $Pack = $ItemArray[$k][2];
                // $Unit = $ItemArray[$k][3];
                // $qtyCases = $ItemArray[$k][4];
                $Qty = $ItemArray[$k][2];
                /*echo "<pre>";
                print_r($GetItemStock);*/
                
                $CheckItemStockRecord = $this->stock_transfer_model->CheckStockRecord($ItemID,$TrnsTo);
                
                if(empty($CheckItemStockRecord)){
                    $insertStock = array(
                            'PlantID' =>$selected_company,
                            'FY' =>$FY,
                            'cnfid' =>1,
                            'ItemID' =>$ItemID,
                            'gtiqty' =>$Qty,
                            'GodownID' =>$TrnsTo,
                            'UserId' =>$_SESSION['username'],
                            //'EffDate' =>date('Y-m-d H:i:s'),
                            'EffDate' =>$Transdate
                        );
                        $this->db->insert(db_prefix() . 'stockmaster',$insertStock);
                }
                
                foreach($GetItemStock as $value){
                    if(strtoupper($value['ItemID']) == strtoupper($ItemID)){
                             
                        // Qty In to TransTo Godown        
                        $QtyIn = $value['gtoqty'] + $Qty;
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $FY);  
                        $this->db->where('GodownID', $TrnsFrom);  
                        $this->db->where('ItemID', $ItemID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtoqty' => $QtyIn,
                                ]);
                    }
                }
                foreach($GetItemStock2 as $value){
                    if(strtoupper($value['ItemID']) == strtoupper($ItemID)){
                        // Qty Out From TransFrom Godown      
                        $QtyOut = $value['gtiqty'] + $Qty;
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $FY);  
                        $this->db->where('GodownID', $TrnsTo);  
                        $this->db->where('ItemID', $ItemID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtiqty' => $QtyOut,
                                ]);
                    }
                }
                //echo "<pre>";
                    $HistoryArrayOut = array(
                        'PlantID' =>$selected_company,
                        'FY' =>$FY,
                        'cnfid' =>'1',
                        'OrderID' =>$TransID,
                        'BillID' =>$TransID,
                        'TransID' =>$TransID,
                        'TransDate' =>date('Y-m-d H:i:s'),
                        'TransDate2' =>$Transdate,
                        'TType' =>'T',
                        'TType2' =>'Out',
                        'AccountID' =>$TrnsFrom,
                        'GodownID' =>$TrnsFrom,
                        'ItemID' =>$ItemID,
                        'SaleRate' =>null,
                        'BasicRate' =>null,
                        'SuppliedIn' =>'CS',
                        'OrderQty' =>$Qty,
                        'BilledQty' =>$Qty,
                        'CaseQty' =>$Pack,
                        'OrderAmt' =>null,
                        'ChallanAmt' =>null,
                        'NetOrderAmt' =>null,
                        'NetChallanAmt' =>null,
                        'Ordinalno' =>$OrdNo,
                        'UserID' =>$_SESSION['username'],
                    );
                //print_r($HistoryArrayOut);
                $this->db->insert(db_prefix() . 'history',$HistoryArrayOut);
                $OrdNo++;
                    $HistoryArrayIn = array(
                        'PlantID' =>$selected_company,
                        'FY' =>$FY,
                        'cnfid' =>'1',
                        'OrderID' =>$TransID,
                        'BillID' =>$TransID,
                        'TransID' =>$TransID,
                        'TransDate' =>date('Y-m-d H:i:s'),
                        'TransDate2' =>$Transdate,
                        'TType' =>'T',
                        'TType2' =>'In',
                        'AccountID' =>$TrnsTo,
                        'GodownID' =>$TrnsTo,
                        'ItemID' =>$ItemID,
                        'SaleRate' =>null,
                        'BasicRate' =>null,
                        'SuppliedIn' =>'CS',
                        'OrderQty' =>$Qty,
                        'BilledQty' =>$Qty,
                        'CaseQty' =>$Pack,
                        'OrderAmt' =>null,
                        'ChallanAmt' =>null,
                        'NetOrderAmt' =>null,
                        'NetChallanAmt' =>null,
                        'Ordinalno' =>$OrdNo,
                        'UserID' =>$_SESSION['username'],
                    );
                    //print_r($HistoryArrayIn);
                $this->db->insert(db_prefix() . 'history',$HistoryArrayIn);
                $OrdNo++;
            }
            //die;
                $next_TransNumber = get_option('next_stock_trf_number_for_kirti');
                $new_TransNumber = 'TRS'.$FY.$next_TransNumber;
                echo json_encode($new_TransNumber);
                die;
    }
    
    public function DeleteTransfer()
    {
        if (!has_permission_new('StockTransfer', '', 'delete')) {
            access_denied('Stock Transfer');
        }
        $TransID = $this->input->post('TransID');
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        
        $OldTransDetails = $this->stock_transfer_model->GetOLDTransDetails($TransID);
        
        $masterData = array(
            'UserID2'=> $_SESSION['username'],
            'Lupdate'=>date('Y-m-d H:i:s'),
        );
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);  
        $this->db->where('TransID', $TransID); 
        $this->db->update(db_prefix() . 'TransferMaster', $masterData);
        
            $OldTransFrom = $OldTransDetails->TransFrom;
            $OldTransTo = $OldTransDetails->TransTo;
            
            foreach($OldTransDetails->ItemS as $row ){
                // Qty minus to TransFrom Godown  
                $TrnsFromOLD = $row['AccountID'];
                $ItemIDOLD = $row['ItemID'];
                
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $FY);  
                $this->db->where('GodownID', $TrnsFromOLD);  
                $this->db->where('ItemID', $ItemIDOLD);
                        
                if($row['TType2']=="Out"){
                    $QtyIn = $row['gtoqty'] - $row['BilledQty'];
                    $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtoqty' => $QtyIn,
                                ]);
                }else{
                    $QtyIn = $row['gtiqty'] - $row['BilledQty'];
                    $this->db->update(db_prefix() . 'stockmaster', [
                                    'gtiqty' => $QtyIn,
                                ]);
                }      
            }
            
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $FY);
            $this->db->where('TType', "T");
            $this->db->where('TransID', $TransID);
            $this->db->delete(db_prefix() . 'history');
            if ($this->db->affected_rows() > 0) {
                $next_TransNumber = get_option('next_stock_trf_number_for_kirti');
                $new_TransNumber = 'TRS'.$FY.$next_TransNumber;
                echo json_encode($new_TransNumber);
                die;
            }else{
                echo json_encode(false);
            }
            
    }
}