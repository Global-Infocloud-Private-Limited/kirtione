<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class FpoOrderModel extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function GetCenterList()
        {
            $UserID = $this->session->userdata('username');
            $this->db->select('tblCenterMaster.*');
    		$this->db->from(db_prefix() . 'CenterMaster');
    		if(!is_admin()){
    		    $this->db->join('tblstaff_wise_center', 'tblstaff_wise_center.CenterID = tblCenterMaster.CenterID');
    	        $this->db->where('tblstaff_wise_center.AccountID', $UserID);
    		}
    		$this->db->order_by( db_prefix() .'CenterMaster.id','ASC');
    		return $this->db->get()->result_array();
        }
        
        public function GetTraderList()
        {
            $CustomerType = 3;
            $this->db->select('tblclients.*');
    		$this->db->from(db_prefix() . 'clients');
    		$this->db->where('tblclients.CustomerType', $CustomerType);
    		$this->db->order_by( db_prefix() .'clients.AccountID','ASC');
    		return $this->db->get()->result_array();
        }
        
        public function GetIsFPOStaffList()
        {
            $StaffRole = 1;
            $AccountID = $this->session->userdata('username');
            $role = $this->session->userdata('role');
            $this->db->select('tblstaff.*');
    		$this->db->from(db_prefix() . 'staff');
    		$this->db->where('tblstaff.role', $StaffRole);
    		if($role == $StaffRole){
    		    $this->db->where('tblstaff.AccountID', $AccountID);
    		}
    		$this->db->order_by('tblstaff.firstname,tblstaff.lastname','ASC');
    		return $this->db->get()->result_array();
        }
        
        public function GetItemList()
        {
            $UserID = $this->session->userdata('username');
            $selected_company = $this->session->userdata('root_company');
            $this->db->select('tblitems.*, tblitems_sub_groups.main_group_id');
            $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = tblitems.subgroup_id');
            if(!is_admin()){
    		    $this->db->join('tblstaff_wise_items', 'tblstaff_wise_items.ItemID = tblitems.ItemID');
    	        $this->db->where('tblstaff_wise_items.AccountID', $UserID);
    		}
            $this->db->where(db_prefix() . 'items.isactive', 'Y');
            $this->db->where('tblitems_sub_groups.main_group_id', 3); 
            $this->db->order_by('ItemName', 'ASC');
            return $this->db->get(db_prefix() . 'items')->result_array();
        }
        
        public function GetFarmerList()
        {
            $CustomerType = 1;
            $this->db->select('tblclients.AccountID, tblclients.company');
            $this->db->from(db_prefix() . 'clients');
            $this->db->where('tblclients.CustomerType', $CustomerType);
            $this->db->order_by(db_prefix() . 'clients.AccountID', 'ASC');
            $result = $this->db->get()->result_array();
            
            return array_map(function($row) {
                return [
                    'id' => $row['AccountID'],
                    'label' => $row['company']
                ];
            }, $result);
        }
        
        public function GetItemDetails($itemID)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
        
            $this->db->select('tblitems.*');
            $this->db->where('tblitems.PlantID', $selected_company);
            $this->db->where('tblitems.ItemID', $itemID);
            $data = $this->db->get('tblitems')->row();
        
            if ($data) {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'ItemQCParameter');
                $this->db->where('ItemID', $itemID);
                $parameters = $this->db->get()->result_array();
        
                $finalParameters = [];
        
                foreach ($parameters as $param) {
                    if (!empty($param['ItemParameterID'])) {
                        $this->db->select('ItemParameterName,ItemParameterID');
                        $this->db->from(db_prefix() . 'ItemParameter');
                        $this->db->where('ItemParameterID', $param['ItemParameterID']);
                        $parameterDetail = $this->db->get()->row();
        
                        if ($parameterDetail) {
                            $param['ItemParameterName'] = $parameterDetail->ItemParameterName;
                            $finalParameters[] = $param;
                        }
                    }
                }
        
                $data->Parameter = $finalParameters;
            }
        
            return $data;
        }
        
        public function add_fpo_order($data)
        {
            if (isset($data['pur_order_detail'])) {
                $pur_order_detail = json_decode($data['pur_order_detail']);
                unset($data['pur_order_detail']);
                
                $es_detail = [];
                
                $header_before_dynamic = ['id', 'NetWeight', 'UOM', 'Bag', 'Rate'];
                $header_after_dynamic = ['Deduction', 'NetRate', 'tenweight', 'NetAmt'];
        
                foreach ($pur_order_detail as $row) {
                    if (!empty($row[0])) {
                        $before_dynamic = array_slice($row, 0, count($header_before_dynamic));
                        $after_dynamic = array_slice($row, -count($header_after_dynamic));
        
                        $combined = [];
                        foreach ($header_before_dynamic as $index => $header_name) {
                            $combined[$header_name] = $before_dynamic[$index] ?? null;
                        }
                        foreach ($header_after_dynamic as $index => $header_name) {
                            $combined[$header_name] = $after_dynamic[$index] ?? null;
                        }
                        $es_detail[] = $combined;
                    }
                }
            }
           
            $PlantID = $this->session->userdata('root_company'); 
            $FY = $this->session->userdata('finacial_year');
            $fpo_orderNumbar = get_option('next_FPO_number_for_kirti');
            $new_fpo_orderNumbar = 'FPO'.$FY.$fpo_orderNumbar;   
            $ItCount = count($es_detail);
            $Transdate =  to_sql_date($data['FPO_Date'])." ".date('H:i:s');
            $ItemID =  $data['ItemID'];
            $CenterID = $data['CenterID'];
            $ParameterList = $this->GetItemDetails($ItemID);
            $fpolist = $data['fpolist'];
            $Rate = $data['rate'];
            
            $total_net_wgt =  $data['total_net_wgt'];
            $Total_bag =  $data['Total_bag'];
            $total_tent_wgt =  $data['total_tent_wgt'];
            $total_amt =  $data['total_amt'];
            $PartyDetails = $this->GetPurchaseForParty($CenterID,$ItemID);
            if($PartyDetails){
                $PartyID = $PartyDetails->PartyID;
            }else{
                $PartyID = "KOIL";
            }
            if(empty($ItemID)){
                return false;
            }
            else if(empty($fpolist)){
                return false;
            }
            else if(empty($Rate)) {
                return false;
            }

            $FPOOrderMaster = array(
                'PlantID'=>$PlantID,
                'FY'=>$FY,
                'OrderID' =>$new_fpo_orderNumbar,
                'Transdate' =>$Transdate,
                'Transdate2' =>date('Y-m-d H:i:s'),
                'FPOID'=>$fpolist,
                'CenterID'=>$CenterID,
                'ItemID'=>$ItemID,
                'FpoRate'=>$Rate,
                'PartyID'=>$PartyID,
                "UserID" => $_SESSION['username'],
            );
            $this->db->insert(db_prefix() . 'FpoOrderMaster',$FPOOrderMaster);
            if($this->db->affected_rows() > 0)
            {
                $this->increment_next_Fpo_Order_number();
                
                foreach($es_detail as $value)
                {
                     if (empty($value['id'])) {
                        log_message('error', 'Skipping insert: AccountID is empty');
                        continue;
                    }
                    $FpoOrderdetails = array(
                        'PlantID'=>$PlantID,
                        'FY'=>$FY,
                        'OrderID'=>$new_fpo_orderNumbar,
                        'Transdate'=>$Transdate,
                        'Transdate2'=>date('Y-m-d H:i:s'),
                        'CenterID'=>$CenterID,
                        'TType'=>'O',
                        'TType2'=>'ORDER',
                        'AccountID'=>$value['id'],
                        'NetWgt'=>$value['NetWeight'],
                        'Bag'=>$value['Bag'],
                        'Rate'=>$value['Rate'],
                        'Deduction'=>$value['Deduction'],
                        'NetRate'=>$value['NetRate'],
                        'TentativeWgt'=>$value['tenweight'],
                        'Amount'=>$value['NetAmt']
                    );
                    $this->db->insert(db_prefix() . 'FpoOrderDetails',$FpoOrderdetails);
                }
                if($this->db->affected_rows() > 0)
                {
                    if ($this->db->affected_rows() > 0 && !empty($data['dynamic_param_data'])) 
                    {
                        $ParameterList = $this->GetItemDetails($ItemID);
                        $parameters = $ParameterList->Parameter;
                        $dynamicParamData = $data['dynamic_param_data'];
                        
                        foreach ($dynamicParamData as $accountID => $paramRows) {
                            foreach ($paramRows as $row) {
                                foreach ($parameters as $param) {
                                    $paramName = $param['ItemParameterName'];
                                    $valueKey = $paramName;
                                    $amtKey = $paramName . ' Amt';
                    
                                    if (array_key_exists($valueKey, $row) && array_key_exists($amtKey, $row)) {
                                        $qcValue = $row[$valueKey];
                                        $qcAmt = $row[$amtKey];
                    
                                        // Skip empty values
                                        if ($qcValue === '' && $qcAmt === '') {
                                            continue;
                                        }
                    
                                        $insertData = [
                                            'OrderID' => $new_fpo_orderNumbar,
                                            'AccountID' => $accountID,
                                            'Parameter_ID' => $param['ItemParameterID'],
                                            'Qc_Value' => $qcValue,
                                            'Qc_Amt' => $qcAmt,
                                        ];
                    
                                        $this->db->insert('tblFpoQcDetail', $insertData);
                                       
                                    }
                                }
                            }
                        }
                    }
                }
                return true;
            }
        }
        
        public function edit_fpo_order($data,$id)
        {
            if (isset($data['pur_order_detail'])) {
                $pur_order_detail = json_decode($data['pur_order_detail']);
                unset($data['pur_order_detail']);
                
                $es_detail = [];
                
                $header_before_dynamic = ['id', 'NetWeight', 'UOM', 'Bag', 'Rate'];
                $header_after_dynamic = ['Deduction', 'NetRate', 'tenweight', 'NetAmt'];
        
                foreach ($pur_order_detail as $row) {
                    if (!empty($row[0])) {
                        $before_dynamic = array_slice($row, 0, count($header_before_dynamic));
                        $after_dynamic = array_slice($row, -count($header_after_dynamic));
        
                        $combined = [];
                        foreach ($header_before_dynamic as $index => $header_name) {
                            $combined[$header_name] = $before_dynamic[$index] ?? null;
                        }
                        foreach ($header_after_dynamic as $index => $header_name) {
                            $combined[$header_name] = $after_dynamic[$index] ?? null;
                        }
                        $es_detail[] = $combined;
                    }
                }
            }
            
            $PlantID = $this->session->userdata('root_company'); 
            $FY = $this->session->userdata('finacial_year');
            $Po_Order = $data['po_order'];
            $ItCount = count($es_detail);
            $Transdate =  to_sql_date($data['FPO_Date'])." ".date('H:i:s');
            $ItemID =  $data['itemid'];
            $ParameterList = $this->GetItemDetails($ItemID);
            $fpolist = $data['fpo_list'];
            $Rate = $data['rate'];
            
            $total_net_wgt =  $data['total_net_wgt'];
            $Total_bag =  $data['Total_bag'];
            $total_tent_wgt =  $data['total_tent_wgt'];
            $total_amt =  $data['total_amt'];
            $PartyDetails = $this->GetPurchaseForParty($CenterID,$ItemID);
            if($PartyDetails){
                $PartyID = $PartyDetails->PartyID;
            }else{
                $PartyID = "KOIL";
            }
            if(empty($ItemID)){
                return false;
            }
            else if(empty($fpolist)){
                return false;
            }
            else if(empty($Rate)) {
                return false;
            }
            
            $FPOOrderMaster = array(
                "UserID2" => $_SESSION['username'],
                "Lupdate"=>date('Y-m-d H:i:s'),
            );
            $this->db->where('OrderID', $Po_Order);
            $this->db->update(db_prefix() . 'FpoOrderMaster',$FPOOrderMaster);
            if($this->db->affected_rows() > 0){
                foreach($es_detail as $value)
                {
                    if (empty($value['id'])) {
                        log_message('error', 'Skipping insert: AccountID is empty');
                        continue;
                    }
                    $FpoOrderdetails = array(
                        'NetWgt'=>$value['NetWeight'],
                        'Bag'=>$value['Bag'],
                        'Rate'=>$value['Rate'],
                        'Deduction'=>$value['Deduction'],
                        'NetRate'=>$value['NetRate'],
                        'TentativeWgt'=>$value['tenweight'],
                        'Amount'=>$value['NetAmt']
                    );
                    $this->db->where('OrderID', $Po_Order);
                    $this->db->where('AccountID', $value['id']);
                    $this->db->update(db_prefix() . 'FpoOrderDetails',$FpoOrderdetails);
                }
                
                if (!empty($data['dynamic_param_data'])) 
                {
                        $ParameterList = $this->GetItemDetails($ItemID);
                        $parameters = $ParameterList->Parameter;
                        $dynamicParamData = $data['dynamic_param_data'];
                        
                        foreach ($dynamicParamData as $accountID => $paramRows) {
                            foreach ($paramRows as $row) {
                                foreach ($parameters as $param) {
                                    $paramName = $param['ItemParameterName'];
                                    $valueKey = $paramName;
                                    $amtKey = $paramName . ' Amt';
                    
                                    if (array_key_exists($valueKey, $row) && array_key_exists($amtKey, $row)) {
                                        $qcValue = $row[$valueKey];
                                        $qcAmt = $row[$amtKey];
                    
                                        // Skip empty values
                                        if ($qcValue === '' && $qcAmt === '') {
                                            continue;
                                        }
                                        
                                        $updateQcDetail = [
                                            'Qc_Value' => $qcValue,
                                            'Qc_Amt' => $qcAmt,
                                        ];
                    
                                        $this->db->where('OrderID', $Po_Order);
                                        $this->db->where('AccountID', $accountID);
                                        $this->db->where('Parameter_ID', $param['ItemParameterID']);
                                        $this->db->update(db_prefix() . 'FpoQcDetail',$updateQcDetail);
                                    }
                                }
                            }
                        }
                    }
                return true;
            }
        }
        
        public function add_dispatch_order($data)
        {
            if (isset($data['pur_order_detail'])) {
                $pur_order_detail = json_decode($data['pur_order_detail']);
                unset($data['pur_order_detail']);
                
                $es_detail = [];
                
                $header_before_dynamic = ['id', 'NetWeight','PendingQty','DispatchQty','DispatchBag','UOM', 'Bag', 'Rate'];
                $header_after_dynamic = ['Deduction', 'NetRate', 'tenweight', 'NetAmt'];
        
                foreach ($pur_order_detail as $row) {
                    if (!empty($row[0])) {
                        $before_dynamic = array_slice($row, 0, count($header_before_dynamic));
                        $after_dynamic = array_slice($row, -count($header_after_dynamic));
        
                        $combined = [];
                        foreach ($header_before_dynamic as $index => $header_name) {
                            $combined[$header_name] = $before_dynamic[$index] ?? null;
                        }
                        foreach ($header_after_dynamic as $index => $header_name) {
                            $combined[$header_name] = $after_dynamic[$index] ?? null;
                        }
                        $es_detail[] = $combined;
                    }
                }
                
                $allDispatched = true;
                foreach ($pur_order_detail as $row) {
                    if (!is_array($row) || empty($row[0])) continue;
        
                   $pendingQty = floatval($row[2]);     
                    $dispatchQty = floatval($row[3]);     
                
                    if ($pendingQty != $dispatchQty) {
                        $allDispatched = false;
                        break;
                    }
                }
        
                $status = $allDispatched ? 3 : 2;
            }
            
            $PlantID = $this->session->userdata('root_company'); 
            $FY = $this->session->userdata('finacial_year');
            $FpoOrderNo = $data['disorder'];
            $CenterID = $data['CenterID'];
            $vehicle_no = $data['vehicle_no'];
            $fpo_disorderNumbar = get_option('next_DIS_number_for_kirti');
            $new_fpo_DisorderNumbar = 'DIS'.$FY.$fpo_disorderNumbar;   
            $ItCount = count($es_detail);
            $Transdate =  to_sql_date($data['FPO_Date'])." ".date('H:i:s');
            $ItemID =  $data['ItemID'];
            $ParameterList = $this->GetItemDetails($ItemID);
            $fpolist = $data['fpoidname'];
            $Rate = $data['rate'];
            
            $total_net_wgt =  $data['total_net_wgt'];
            $Total_bag =  $data['Total_bag'];
            $total_tent_wgt =  $data['total_tent_wgt'];
            $total_amt =  $data['total_amt'];
            $PartyDetails = $this->GetPurchaseForParty($CenterID,$ItemID);
            if($PartyDetails){
                $PartyID = $PartyDetails->PartyID;
            }else{
                $PartyID = "KOIL";
            }
            
            $DispatchOrderMaster = array(
                'PlantID'=>$PlantID,
                'FY'=>$FY,
                'DispatchID'=>$new_fpo_DisorderNumbar,
                'OrderID' =>$FpoOrderNo,
                'Transdate' =>$Transdate,
                'Transdate2' =>date('Y-m-d H:i:s'),
                'CenterID'=>$CenterID,
                'VehicleNo'=>$vehicle_no,
                'FPOID'=>$fpolist,
                'ItemID'=>$ItemID,
                'FpoRate'=>$Rate,
                'PartyID'=>$PartyID,
                "UserID" => $_SESSION['username'],
            );
            $this->db->insert(db_prefix() . 'FpoDispatchMaster',$DispatchOrderMaster);
            if($this->db->affected_rows() > 0)
            {
                $totalDispatchBags = 0;
                foreach ($es_detail as $value) {
                    if (!empty($value['id']) && $value['DispatchQty'] > 0) {
                        $totalDispatchBags += $value['DispatchBag'];
                    }
                }
                
                foreach($es_detail as $value)
                {
                    if (empty($value['id'])) {
                        log_message('error', 'Skipping insert: AccountID is empty');
                        continue;
                    }
                    if($value['DispatchQty'] > 0)
                    {
                        $DispatchDetails = array(
                            'PlantID'=>$PlantID,
                            'FY'=>$FY,
                            'OrderID'=>$FpoOrderNo,
                            'DispatchID'=>$new_fpo_DisorderNumbar,
                            'Transdate'=>$Transdate,
                            'Transdate2'=>date('Y-m-d H:i:s'),
                            'CenterID'=>$CenterID,
                            'TType'=>'D',
                            'TType2'=>'DISPATCH',
                            'AccountID'=>$value['id'],
                            'NetWgt'=>$value['DispatchQty'],
                            'Bag'=>$value['DispatchBag'],
                            'Rate'=>$value['Rate'],
                            'Deduction'=>$value['Deduction'],
                            'NetRate'=>$value['NetRate'],
                            'TentativeWgt'=>$value['tenweight'],
                            'Amount'=>$value['NetAmt']
                        );
                        $this->db->insert(db_prefix() . 'FpoOrderDetails',$DispatchDetails);
                    }
                }
                $this->increment_next_Dispatch_Order_number();
                
                $UpdateFpoDisatchStatus = array(
                        'Status'=>$status,
                    );
                $this->db->WHERE('OrderID', $FpoOrderNo);
                $this->db->update(db_prefix() . 'FpoOrderMaster',$UpdateFpoDisatchStatus);
                
                //Insert Bag Ledger
                $AddBagLedger = array(
                        'OrderID'=>$FpoOrderNo,
                        'DispatchID'=>$new_fpo_DisorderNumbar,
                        'AccountID'=>$fpolist,
                        'Transdate'=>$Transdate,
                        'Type'=>'D',
                        'Qty'=>$totalDispatchBags,
                        'PassedFrom'=>'PURCHASE',
                    );
                $this->db->insert(db_prefix() . 'BagLedger',$AddBagLedger);
                
                return true;
            }
        }
        
        public function increment_next_Fpo_Order_number()
        {
            $FY = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            
            $this->db->where('name', 'next_FPO_number_for_kirti');
            $this->db->set('value', 'value+1', false);
            $this->db->WHERE('FY', $FY);
            $this->db->update(db_prefix() . 'options');
        }
        
        public function increment_next_Dispatch_Order_number()
        {
            $FY = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            
            $this->db->where('name', 'next_DIS_number_for_kirti');
            $this->db->set('value', 'value+1', false);
            $this->db->WHERE('FY', $FY);
            $this->db->update(db_prefix() . 'options');
        }
        
        public function GetDeductionMatrixData($parameterName, $ItemID, $value)
        {
            $this->db->select('ItemParameterID');
            $this->db->where('ItemParameterName', $parameterName);
            $parameter = $this->db->get(db_prefix() . 'ItemParameter')->row();
        
            if (!$parameter) {
                return null;  
            }
            $paramID = $parameter->ItemParameterID;
    
            $this->db->select('*');
            $this->db->where('ItemID', $ItemID);
            $this->db->where('ItemParameterID', $paramID);
            $this->db->order_by('Value', 'ASC');
            $matrix = $this->db->get(db_prefix() . 'deduction_matrix')->result_array();
        
            if (empty($matrix)) {
                return null; 
            }
            
            foreach ($matrix as $entry) {
                if ((float)$entry['Value'] === (float)$value) {
                    return (object)$entry;  
                }
            }
            
            $floorEntry = null;
            $ceilEntry = null;
        
            foreach ($matrix as $entry) {
                if ($entry['Value'] <= $value) {
                    $floorEntry = $entry;
                }
                if ($entry['Value'] > $value) {
                    $ceilEntry = $entry;
                    break;
                }
            }
            
            if (!$floorEntry && $ceilEntry) {
                return (object)$ceilEntry;
            }
            
            if ($floorEntry && !$ceilEntry) {
                return (object)$floorEntry;
            }
        
        if ($floorEntry && $ceilEntry) {
            $val1 = $floorEntry['Value'];
            $val2 = $ceilEntry['Value'];
            $ded1 = $floorEntry['Deduction'];
            $ded2 = $ceilEntry['Deduction'];
    
            $interpolatedDeduction = $ded1 + (($value - $val1) * ($ded2 - $ded1)) / ($val2 - $val1);
            $result = $floorEntry; 
            $result['Deduction'] = round($interpolatedDeduction, 2); 
    
            return (object)$result;
        }
        return null;
    }

        
        public function GetPurchaseForParty($CenterID,$ItemID)
        {
            $this->db->select('tblCommisionMatrix.*');
            $this->db->where('CenterID', $CenterID);
            $this->db->where('ItemID', $ItemID);
            $this->db->where('IsOn', "Y");
            $this->db->where('IsActive', "Y");
            $Partydetails = $this->db->get(db_prefix().'CommisionMatrix')->row();
            return $Partydetails;
        }
        
        public function GetFpoDetails($OrderID)//change
        {  
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $TType= 'O';
          
            $sql1 = ' tblFpoOrderMaster.OrderID = "'.$OrderID.'"
            AND '.db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
            AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
            
            $sql1 .= ' ORDER BY tblFpoOrderMaster.Transdate ASC';
            $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,CONCAT(tblstaff.firstname,"",tblstaff.lastname) AS FPOName,tblitems.ItemName
            FROM '.db_prefix().'FpoOrderMaster 
            LEFT JOIN tblstaff ON tblstaff.AccountID = '.db_prefix().'FpoOrderMaster.FPOID
            LEFT JOIN tblitems ON tblitems.ItemID = '.db_prefix().'FpoOrderMaster.ItemID
            WHERE '.$sql1;
            $result = $this->db->query($sql)->row();
            if($result){
                $sql1 = ' tblFpoOrderDetails.OrderID = "'.$OrderID.'"
                AND '.db_prefix().'FpoOrderDetails.TType = "'.$TType.'" 
                AND '.db_prefix().'FpoOrderDetails.FY = "'.$fy.'" 
                AND '.db_prefix().'FpoOrderDetails.PlantID = "'.$selected_company.'" ';
                
                $sql1 .= ' ORDER BY tblFpoOrderDetails.OrderID ASC';
                $sql ='SELECT '.db_prefix().'FpoOrderDetails.*,'. db_prefix() . 'FpoOrderDetails.id AS incrementid, '. db_prefix() . 'FpoOrderDetails.AccountID AS id,'
                . db_prefix() . 'FpoOrderDetails.NetWgt AS NetWeight,' . db_prefix() . 'FpoOrderDetails.TentativeWgt AS tenweight,'. db_prefix() . 'FpoOrderDetails.Amount AS NetAmt
                FROM '.db_prefix().'FpoOrderDetails 
                WHERE '.$sql1;
                $details = $this->db->query($sql)->result_array();
                
                $dispatch_sql = 'SELECT AccountID, SUM(NetWgt) AS DispatchQty
                 FROM '.db_prefix().'FpoOrderDetails
                 WHERE OrderID = "'.$OrderID.'"
                   AND TType = "D"
                   AND TType2 = "DISPATCH"
                   AND FY = "'.$fy.'"
                   AND PlantID = "'.$selected_company.'"
                    GROUP BY AccountID';
        
                $dispatchQtyResult = $this->db->query($dispatch_sql)->result_array();
                
                $dispatchQtyMap = [];
                foreach ($dispatchQtyResult as $drow) {
                    $dispatchQtyMap[$drow['AccountID']] = $drow['DispatchQty'];
                }
                
                foreach ($details as &$detail) {
                    $accountId = $detail['AccountID'];
                    $netWeight = $detail['NetWgt'];
                    $dispatchQty = isset($dispatchQtyMap[$accountId]) ? $dispatchQtyMap[$accountId] : 0;
                    $detail['Dispatch_qty'] = $dispatchQty;
                    $detail['PendingQty'] = $netWeight - $dispatchQty;
                }
                unset($detail); 

                $result->details = $details;
                
                if($details)
                {
                    $sql2 = ' tblFpoQcDetail.OrderID = "'.$OrderID.'"';
                    $sql2 .= ' ORDER BY tblFpoQcDetail.OrderID ASC';
                    $sql ='SELECT '.db_prefix().'FpoQcDetail.*,'.db_prefix().'ItemParameter.ItemParameterName
                    FROM '.db_prefix().'FpoQcDetail 
                    LEFT JOIN '.db_prefix().'ItemParameter 
                        ON '.db_prefix().'FpoQcDetail.Parameter_ID = '.db_prefix().'ItemParameter.ItemParameterID 
                    WHERE '.$sql2;
                    $qcdetails = $this->db->query($sql)->result_array();
                    $result->qcdetails = $qcdetails;
                }
                else
                {
                    $result->qcdetails = array();
                }
                
            }
            return $result;
        }
        
        public function GetDispatchOrderDetails($DispatchID)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $this->db->select('tblFpoDispatchMaster.*,tblclients.company,tblitems.ItemName,tblitems.hsn_code');
            $this->db->from(db_prefix() . 'FpoDispatchMaster');
            $this->db->join('tblclients', 'tblclients.AccountID = tblFpoDispatchMaster.FPOID', 'left');
            $this->db->join('tblitems', 'tblitems.ItemID = tblFpoDispatchMaster.ItemID', 'left');
            $this->db->where('DispatchID', $DispatchID);
            $FPOdetails = $this->db->get()->row();
            
            if ($FPOdetails) {
                $this->db->select('tblFpoOrderDetails.*');
                $this->db->from(db_prefix() . 'FpoOrderDetails');
                $this->db->where('OrderID', $FPOdetails->OrderID);
                $this->db->where('DispatchID', $DispatchID);
                $OrderDetails = $this->db->get()->result();
                
                $FPOdetails->OrderDetails = $OrderDetails;
            }
            
             return $FPOdetails;
        }
        public function GetPlantDetails($DispatchID)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $this->db->select('tblFpoDispatchMaster.DispatchID,tblPlantMaster.PlantName,tblPlantMaster.GstNo,
            tblPlantMaster.address,tblPlantMaster.pincode,tblxx_statelist.id,tblxx_citylist.city_name');
            $this->db->from(db_prefix() . 'FpoDispatchMaster');
            $this->db->join('tblPlantMaster', 'tblPlantMaster.PlantID = tblFpoDispatchMaster.PartyID');
            $this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblPlantMaster.state');
            $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblPlantMaster.city');
            $this->db->where('tblFpoDispatchMaster.DispatchID', $DispatchID);
            $FPOdetails = $this->db->get()->row();
            return $FPOdetails;
        }
        public function GetDispatchFpoDetails($OrderID,$id)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $TType= 'O';
          
            $sql1 = ' tblFpoOrderMaster.OrderID = "'.$OrderID.'"
            AND '.db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
            AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
            
            $sql1 .= ' ORDER BY tblFpoOrderMaster.Transdate ASC';
            $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS FPOName,tblitems.ItemName
            FROM '.db_prefix().'FpoOrderMaster 
            LEFT JOIN tblstaff ON tblstaff.AccountID = '.db_prefix().'FpoOrderMaster.FPOID
            LEFT JOIN tblitems ON tblitems.ItemID = '.db_prefix().'FpoOrderMaster.ItemID
            WHERE '.$sql1;
            $result = $this->db->query($sql)->row();
            if($result){
                $sql1 = ' tblFpoOrderDetails.OrderID = "'.$OrderID.'"
                AND '.db_prefix().'FpoOrderDetails.TType = "'.$TType.'" 
                AND '.db_prefix().'FpoOrderDetails.FY = "'.$fy.'" 
                AND '.db_prefix().'FpoOrderDetails.PlantID = "'.$selected_company.'" ';
                
                $sql1 .= ' ORDER BY tblFpoOrderDetails.OrderID ASC';
                $sql ='SELECT '.db_prefix().'FpoOrderDetails.*,tblclients.company AS FarmerName,'. db_prefix() . 'FpoOrderDetails.id AS incrementid, '. db_prefix() . 'FpoOrderDetails.AccountID AS id,'
                . db_prefix() . 'FpoOrderDetails.NetWgt AS NetWeight,' . db_prefix() . 'FpoOrderDetails.TentativeWgt AS tenweight,' . db_prefix() . 'FpoDispatchMaster.CenterID,'. db_prefix() . 'FpoOrderDetails.Amount AS NetAmt,'. db_prefix() .'FpoDispatchMaster.VehicleNo
                FROM '.db_prefix().'FpoOrderDetails 
                LEFT JOIN tblFpoDispatchMaster ON tblFpoDispatchMaster.DispatchID = tblFpoOrderDetails.DispatchID
                LEFT JOIN tblclients ON tblclients.AccountID = tblFpoOrderDetails.AccountID
                WHERE '.$sql1;
                $details = $this->db->query($sql)->result_array();
                
                $vehicleSql = 'SELECT VehicleNo,Transdate,CenterID,GrossWt,TareWt,FpoStatus FROM '.db_prefix().'FpoDispatchMaster WHERE DispatchID = "'.$id.'" LIMIT 1';
                $vehicleRow = $this->db->query($vehicleSql)->row();
                
                $dispatchDate = $vehicleRow ? $vehicleRow->Transdate : null;
                $centerID = $vehicleRow ? $vehicleRow->CenterID : null;
                $vehicleNo = $vehicleRow ? $vehicleRow->VehicleNo : null;
                $GrossWt = $vehicleRow ? $vehicleRow->GrossWt : null;
                $TareWt = $vehicleRow ? $vehicleRow->TareWt : null;
                $Status = $vehicleRow ? $vehicleRow->FpoStatus : null;
                
                $CeternameSql = 'SELECT CenterName FROM '.db_prefix().'CenterMaster WHERE CenterID = "'.$centerID.'" LIMIT 1';
                $centerRow = $this->db->query($CeternameSql)->row();
                
                $CenterName = $centerRow ? $centerRow->CenterName : null;

                foreach ($details as &$detail) {
                    $detail['dispatchdate'] = $dispatchDate;
                    $detail['CenterID'] = $centerID;
                    $detail['VehicleNo'] = $vehicleNo;
                    $detail['CenterName'] = $CenterName;
                    $detail['GrossWeight'] = $GrossWt;
                    $detail['TareWeight'] = $TareWt;
                    $detail['FpoStatus'] = $Status;
                }
                unset($detail);
                
                $dispatch_sql = 'SELECT AccountID, NetWgt AS DispatchQty,Bag AS DispatchBag
                 FROM '.db_prefix().'FpoOrderDetails
                 WHERE OrderID = "'.$OrderID.'" AND 
                 DispatchID = "'.$id.'" 
                   AND TType = "D"
                   AND TType2 = "DISPATCH"
                   AND FY = "'.$fy.'"
                   AND PlantID = "'.$selected_company.'"
                    GROUP BY AccountID';
        
                $dispatchQtyResult = $this->db->query($dispatch_sql)->result_array();
                
                $pending_sql = 'SELECT AccountID, SUM(NetWgt) AS TotalDispatch
                 FROM '.db_prefix().'FpoOrderDetails
                 WHERE OrderID = "'.$OrderID.'" 
                   AND TType = "D"
                   AND TType2 = "DISPATCH"
                   AND FY = "'.$fy.'"
                   AND PlantID = "'.$selected_company.'"
                    GROUP BY AccountID';
        
                $PendingqtyResult = $this->db->query($pending_sql)->result_array();
                
                $dispatchQtyMap = [];
                $dispatchBagMap = [];
                foreach ($dispatchQtyResult as $drow) {
                    $dispatchQtyMap[$drow['AccountID']] = $drow['DispatchQty'];
                    $dispatchBagMap[$drow['AccountID']] = $drow['DispatchBag'];
                }
                
                $pendingQtyMap = [];
                foreach($PendingqtyResult as $val)
                {
                    $pendingQtyMap[$val['AccountID']] = $val['TotalDispatch'];
                }
                foreach ($details as &$detail) {
                    $accountId = $detail['AccountID'];
                    $netWeight = $detail['NetWgt'];
                    $dispatchQty = isset($dispatchQtyMap[$accountId]) ? $dispatchQtyMap[$accountId] : 0;
                    $dispatchBag = isset($dispatchBagMap[$accountId]) ? $dispatchBagMap[$accountId] : 0;
                    $TotalDispatch = isset($pendingQtyMap[$accountId]) ? $pendingQtyMap[$accountId] : 0;
                    
                    $detail['DispatchQty'] = $dispatchQty;
                    
                    $PendingQty = ($netWeight - $TotalDispatch) + $dispatchQty;
                    $detail['PendingQty'] = $PendingQty;
                    $detail['DispatchBag'] = $dispatchBag;
                }
                
                $result->dispatchID = $id;
                $result->details = $details;
                
                if($details)
                {
                    $sql2 = ' tblFpoQcDetail.OrderID = "'.$OrderID.'"';
                    $sql2 .= ' ORDER BY tblFpoQcDetail.OrderID ASC';
                    $sql ='SELECT '.db_prefix().'FpoQcDetail.*,'.db_prefix().'ItemParameter.ItemParameterName
                    FROM '.db_prefix().'FpoQcDetail 
                    LEFT JOIN '.db_prefix().'ItemParameter 
                        ON '.db_prefix().'FpoQcDetail.Parameter_ID = '.db_prefix().'ItemParameter.ItemParameterID 
                    WHERE '.$sql2;
                    $qcdetails = $this->db->query($sql)->result_array();
                    $result->qcdetails = $qcdetails;
                }
                else
                {
                    $result->qcdetails = array();
                }
                
            }
            return $result;
        }
        
        public function load_data_fpo_rate($data)
        {
            $CenterID = $data["CenterID"];
            $ItemID = $data["ItemID"];
            $Fpolist = $data["Fpolist"];
            $Status = $data["Status"];
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $UserID = $this->session->userdata('username');
            $role = $this->session->userdata('role');
            $this->db->select(db_prefix().'FpoRateMaster.*, tblitems.ItemName,tblstaff.firstname,tblstaff.lastname,tblCenterMaster.CenterName');
            $this->db->from(db_prefix().'FpoRateMaster');
            $this->db->join('tblstaff', 'tblstaff.AccountID = '.db_prefix().'FpoRateMaster.FPOID');
            $this->db->join('tblitems', 'tblitems.ItemID = '.db_prefix().'FpoRateMaster.ItemID AND tblitems.PlantID = '.db_prefix().'FpoRateMaster.PlantID');
            $this->db->join('tblCenterMaster', 'tblCenterMaster.CenterID = '.db_prefix().'FpoRateMaster.CenterID','left');
            if($role == "1"){
    	        $this->db->where('tblFpoRateMaster.FPOID', $UserID);
    		}
            $this->db->where(db_prefix().'FpoRateMaster.FY', $fy);
            $this->db->where(db_prefix().'FpoRateMaster.PlantID', $selected_company);
            
            if (!empty($CenterID)) {
                $this->db->where(db_prefix().'FpoRateMaster.CenterID', $CenterID);
            }
            
            if (!empty($ItemID)) {
                $this->db->where(db_prefix().'FpoRateMaster.ItemID', $ItemID);
            }
            if (!empty($Fpolist)) {
                $this->db->where(db_prefix().'FpoRateMaster.FPOID', $Fpolist);
            }
            
            if (!empty($Status)) {
                $this->db->where(db_prefix().'FpoRateMaster.Status', $Status);
            }

            $this->db->order_by(db_prefix().'FpoRateMaster.id', 'ASC');
        
            $query = $this->db->get();
            $result = $query->result_array();
        
            return $result;
        }
        
        public function AddRate($data)
    	{
    		return $this->db->insert('tblFpoRateMaster', $data);
    	}
    	
    	public function GetRateMasterDetails($fpolist,$ItemID,$CenterID)
    	{
    	    $selected_company = $this->session->userdata('root_company');
		    $fy = $this->session->userdata('finacial_year');
		    
    	    $this->db->select('tblFpoRateMaster.*');
            $this->db->where('ItemID', $ItemID);
            $this->db->where('FPOID',$fpolist);
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $fy);
            
            $this->db->group_start();
                $this->db->where('CenterID', $CenterID);
                $this->db->or_where('CenterID IS NULL', null, false);
            $this->db->group_end();
    
            $RateDetails = $this->db->get(db_prefix().'FpoRateMaster')->result();
            return $RateDetails;
    	}
    	
    	public function get_company_detail()
        {  
            $selected_company = $this->session->userdata('root_company');
            $sql ='SELECT '.db_prefix().'rootcompany.*
            FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
            $result = $this->db->query($sql)->row();
            return $result;
        }
//======================= Get FPO Order List in Pop Up =========================
    public function load_fpo_order_list($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $UserID = $this->session->userdata('username');
        $Role = $this->session->userdata('role');
        
        $sql1 = '('.db_prefix().'FpoOrderMaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
        AND '.db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
        AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
        if($Role == "1"){
            $sql1 .= ' AND tblFpoOrderMaster.FPOID = "'.$UserID.'"';
        }
        $sql1 .= ' ORDER BY tblFpoOrderMaster.OrderID ASC';
        $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,tblitems.ItemName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS FPOName,tblPlantMaster.PlantName
        FROM '.db_prefix().'FpoOrderMaster 
        INNER JOIN  tblstaff ON tblstaff.AccountID = tblFpoOrderMaster.FPOID';
        if(!is_admin()){
            $sql .= ' INNER JOIN tblstaff_wise_items ON tblstaff_wise_items.ItemID = tblFpoOrderMaster.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'" ';
        }
        $sql .= ' LEFT JOIN  tblPlantMaster ON tblPlantMaster.PlantID = tblFpoOrderMaster.PartyID 
        INNER JOIN  tblitems ON tblitems.ItemID = tblFpoOrderMaster.ItemID AND tblitems.PlantID = tblFpoOrderMaster.PlantID
        WHERE '.$sql1;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
//========================= Get FPO Order Report List ==========================
    public function load_filterwise_fpo_order_list($data)
    {
        $from_date = to_sql_date($data["from_date"]);
        $to_date = to_sql_date($data["to_date"]);
        $Fpolist = $data['Fpolist'];
        $Item = $data['Item'];
        $Status = $data['status'];
        $PaymentStatus = $data['payment_status'];
        
        $fy = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
        $UserID = $this->session->userdata('username');
        $Role = $this->session->userdata('role');
        
        $sql1 = '('.db_prefix().'FpoOrderMaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
        AND '.db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
        AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
        
        if (!empty($Fpolist)) {
            $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.FPOID = "'.$Fpolist.'"';
        }
        
        if (!empty($Item)) {
            $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.ItemID = "'.$Item.'"';
        }
        
        if (!empty($Status)) {
            $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.Status = "'.$Status.'"';
        }
        
        if (!empty($PaymentStatus)) {
            $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.PaymentStatus = "'.$PaymentStatus.'"';
        }
        
        if($Role == "1"){
            $sql1 .= ' AND tblFpoOrderMaster.FPOID = "'.$UserID.'"';
        }
        
        $sql1 .= ' ORDER BY tblFpoOrderMaster.OrderID ASC';
        $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,tblitems.ItemName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS company,tblPlantMaster.PlantName,farmer_clients.company AS farmer_name,farmer_clients.AccountID AS farmer_id,
        tblFpoOrderDetails.Rate AS farmer_rate,tblFpoOrderDetails.NetWgt AS weight,tblFpoOrderDetails.NetRate,tblFpoOrderDetails.Amount AS NetAmt,tblFpoOrderDetails.Deduction
        FROM '.db_prefix().'FpoOrderMaster 
        INNER JOIN  tblstaff ON tblstaff.AccountID = tblFpoOrderMaster.FPOID'; 
        if(!is_admin()){
            $sql .= ' INNER JOIN tblstaff_wise_items ON tblstaff_wise_items.ItemID = tblFpoOrderMaster.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'" ';
        }
        $sql .= ' LEFT JOIN  tblPlantMaster ON tblPlantMaster.PlantID = tblFpoOrderMaster.PartyID 
        INNER JOIN  tblitems ON tblitems.ItemID = tblFpoOrderMaster.ItemID AND tblitems.PlantID = tblFpoOrderMaster.PlantID
        INNER JOIN  tblFpoOrderDetails ON tblFpoOrderDetails.OrderID = tblFpoOrderMaster.OrderID AND tblFpoOrderDetails.TType = "O" AND tblFpoOrderDetails.PlantID = tblFpoOrderMaster.PlantID AND tblFpoOrderDetails.FY = tblFpoOrderMaster.FY
        INNER JOIN tblclients AS farmer_clients ON farmer_clients.AccountID = tblFpoOrderDetails.AccountID 
        WHERE '.$sql1;
        $result = $this->db->query($sql)->result_array();
        
        foreach ($result as &$val) {
            $this->db->select('SUM(NetWgt) AS TotalDispatch');
            $this->db->where('OrderID', $val['OrderID']);
            $this->db->where('AccountID', $val['farmer_id']);
            $this->db->where('TType', 'D');
            $this->db->where('PlantID', $selected_company);
            $this->db->where('FY', $fy);
        
            $TotalDispatch = $this->db->get(db_prefix().'FpoOrderDetails')->row();
            $val['DispatchWt'] = $TotalDispatch && $TotalDispatch->TotalDispatch ? $TotalDispatch->TotalDispatch : 0;
            
            $this->db->select('tblFpoQcDetail.*,tblItemParameter.ItemParameterName');
            $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblFpoQcDetail.Parameter_ID', 'left');
            $this->db->where('OrderID', $val['OrderID']);
            $this->db->where('AccountID', $val['farmer_id']);
            $QcDetails = $this->db->get(db_prefix().'FpoQcDetail')->result_array();
            $val['QcDetails'] = $QcDetails;
        }
        unset($val); 

        return $result;
    }
        
        public function load_paymentfpolist($OrderID)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
          
            $sql1 = db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
            AND '.db_prefix().'FpoOrderMaster.OrderID = "'.$OrderID.'" 
            AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
            
            $sql1 .= ' ORDER BY tblFpoOrderMaster.OrderID ASC';
            $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,tblitems.ItemName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS company,tblPlantMaster.PlantName,farmer_clients.company AS farmer_name,farmer_clients.AccountID AS farmer_id,
            tblFpoOrderDetails.Rate AS farmer_rate,tblFpoOrderDetails.NetWgt AS weight,tblFpoOrderDetails.NetRate,tblFpoOrderDetails.Amount AS NetAmt,tblFpoOrderDetails.Deduction,
            tblFpoOrderDetails.Bag
            FROM '.db_prefix().'FpoOrderMaster 
            INNER JOIN  tblstaff ON tblstaff.AccountID = tblFpoOrderMaster.FPOID 
            LEFT JOIN  tblPlantMaster ON tblPlantMaster.PlantID = tblFpoOrderMaster.PartyID 
            INNER JOIN  tblitems ON tblitems.ItemID = tblFpoOrderMaster.ItemID AND tblitems.PlantID = tblFpoOrderMaster.PlantID
            INNER JOIN  tblFpoOrderDetails ON tblFpoOrderDetails.OrderID = tblFpoOrderMaster.OrderID AND tblFpoOrderDetails.TType = "O" AND tblFpoOrderDetails.PlantID = tblFpoOrderMaster.PlantID AND tblFpoOrderDetails.FY = tblFpoOrderMaster.FY
            INNER JOIN tblclients AS farmer_clients ON farmer_clients.AccountID = tblFpoOrderDetails.AccountID 
            WHERE '.$sql1;
            $result = $this->db->query($sql)->result_array();
            
            foreach ($result as &$val) {
                $this->db->select('SUM(NetWgt) AS TotalDispatch');
                $this->db->where('OrderID', $val['OrderID']);
                $this->db->where('AccountID', $val['farmer_id']);
                $this->db->where('TType', 'D');
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $fy);
            
                $TotalDispatch = $this->db->get(db_prefix().'FpoOrderDetails')->row();
                $val['DispatchWt'] = $TotalDispatch && $TotalDispatch->TotalDispatch ? $TotalDispatch->TotalDispatch : 0;
                
                $this->db->select('FpoQcDetail.*,tblItemParameter.ItemParameterName');
                $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblFpoQcDetail.Parameter_ID', 'left');
                $this->db->where('OrderID', $val['OrderID']);
                $this->db->where('AccountID', $val['farmer_id']);
                $qcdetails = $this->db->get(db_prefix().'FpoQcDetail')->result_array();
                $val['qcdetails'] = $qcdetails;
            }
            unset($val); 

            return $result;
        }
        
        public function load_inwardfpolists($OrderID,$id)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
          
            $sql1 = db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
            AND '.db_prefix().'FpoOrderMaster.OrderID = "'.$OrderID.'" 
            AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
            
            $sql1 .= ' ORDER BY tblFpoOrderMaster.OrderID ASC';
            $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,tblitems.ItemName,CONCAT(tblstaff.firstname,"",tblstaff.lastname) AS company,tblPlantMaster.PlantName,farmer_clients.company AS farmer_name,farmer_clients.AccountID AS farmer_id,
            tblFpoOrderDetails.Rate AS farmer_rate,tblFpoOrderDetails.NetWgt AS weight,tblFpoOrderDetails.NetRate,tblFpoOrderDetails.Amount AS NetAmt,tblFpoOrderDetails.Deduction,
            tblFpoOrderDetails.Bag
            FROM '.db_prefix().'FpoOrderMaster 
            INNER JOIN  tblstaff ON tblstaff.AccountID = tblFpoOrderMaster.FPOID 
            LEFT JOIN  tblPlantMaster ON tblPlantMaster.PlantID = tblFpoOrderMaster.PartyID 
            INNER JOIN  tblitems ON tblitems.ItemID = tblFpoOrderMaster.ItemID AND tblitems.PlantID = tblFpoOrderMaster.PlantID
            INNER JOIN  tblFpoOrderDetails ON tblFpoOrderDetails.OrderID = tblFpoOrderMaster.OrderID AND tblFpoOrderDetails.TType = "O" AND tblFpoOrderDetails.PlantID = tblFpoOrderMaster.PlantID AND tblFpoOrderDetails.FY = tblFpoOrderMaster.FY
            INNER JOIN tblclients AS farmer_clients ON farmer_clients.AccountID = tblFpoOrderDetails.AccountID 
            WHERE '.$sql1;
            $result = $this->db->query($sql)->result_array();
            
            foreach ($result as &$val) {
                $this->db->select('SUM(NetWgt) AS TotalDispatch');
                $this->db->where('OrderID', $val['OrderID']);
                $this->db->where('AccountID', $val['farmer_id']);
                $this->db->where('TType', 'D');
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $fy);
            
                $TotalDispatch = $this->db->get(db_prefix().'FpoOrderDetails')->row();
                $val['DispatchWt'] = $TotalDispatch && $TotalDispatch->TotalDispatch ? $TotalDispatch->TotalDispatch : 0;
                
                $this->db->select('FpoQcDetail.*,tblItemParameter.ItemParameterName');
                $this->db->join('tblItemParameter', 'tblItemParameter.ItemParameterID = tblFpoQcDetail.Parameter_ID', 'left');
                $this->db->where('OrderID', $val['OrderID']);
                $this->db->where('AccountID', $val['farmer_id']);
                $qcdetails = $this->db->get(db_prefix().'FpoQcDetail')->result_array();
                $val['qcdetails'] = $qcdetails;
                
                $dispatch_sql = 'SELECT AccountID, NetWgt AS DispatchQty,Bag AS DispatchBag
                 FROM '.db_prefix().'FpoOrderDetails
                 WHERE OrderID = "'.$OrderID.'" AND 
                 DispatchID = "'.$id.'" 
                   AND TType = "D"
                   AND TType2 = "DISPATCH"
                   AND FY = "'.$fy.'"
                   AND PlantID = "'.$selected_company.'"
                    GROUP BY AccountID';
        
                $dispatchQtyResult = $this->db->query($dispatch_sql)->result_array();
                
                $pending_sql = 'SELECT AccountID, SUM(NetWgt) AS TotalDispatch
                 FROM '.db_prefix().'FpoOrderDetails
                 WHERE OrderID = "'.$OrderID.'" 
                   AND TType = "D"
                   AND TType2 = "DISPATCH"
                   AND FY = "'.$fy.'"
                   AND PlantID = "'.$selected_company.'"
                    GROUP BY AccountID';
        
                $PendingqtyResult = $this->db->query($pending_sql)->result_array();
                
                $dispatchQtyMap = [];
                $dispatchBagMap = [];
                foreach ($dispatchQtyResult as $drow) {
                    $dispatchQtyMap[$drow['AccountID']] = $drow['DispatchQty'];
                    $dispatchBagMap[$drow['AccountID']] = $drow['DispatchBag'];
                }
                
                $pendingQtyMap = [];
                foreach($PendingqtyResult as $pval)
                {
                    $pendingQtyMap[$pval['AccountID']] = $pval['TotalDispatch'];
                }
                
                $accountId = $val['farmer_id'];
                $netWeight = $val['weight'];
                $dispatchQty = isset($dispatchQtyMap[$accountId]) ? $dispatchQtyMap[$accountId] : 0;
                $dispatchBag = isset($dispatchBagMap[$accountId]) ? $dispatchBagMap[$accountId] : 0;
                $TotalDispatch = isset($pendingQtyMap[$accountId]) ? $pendingQtyMap[$accountId] : 0;
                
                $val['DispatchQty'] = $dispatchQty;
                
                $PendingQty = ($netWeight - $TotalDispatch) + $dispatchQty;
                $val['PendingQty'] = $PendingQty;
                $val['DispatchBag'] = $dispatchBag;
            }
            unset($val); 

            return $result;
        }
        
        public function load_filterwise_dispatch_list($data)
        {
            $from_date = to_sql_date($data["from_date"]);
            $to_date = to_sql_date($data["to_date"]);
            $Fpolist = $data['Fpolist'];
            $Center = $data['Center'];
            $Item = $data['Item'];
            $statusdispatch = $data['statusdispatch'];
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $UserID = $this->session->userdata('username');
            $Role = $this->session->userdata('role');
          
            $sql1 = '('.db_prefix().'FpoOrderDetails.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
            AND '.db_prefix().'FpoOrderMaster.FY = "'.$fy.'" 
            AND '.db_prefix().'FpoOrderMaster.PlantID = "'.$selected_company.'" ';
            
            if (!empty($Fpolist)) {
                $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.FPOID = "'.$Fpolist.'"';
            }
            
            if (!empty($Center)) {
                $sql1 .= ' AND '.db_prefix().'FpoDispatchMaster.CenterID = "'.$Center.'"';
            }
            
            if (!empty($Item)) {
                $sql1 .= ' AND '.db_prefix().'FpoOrderMaster.ItemID = "'.$Item.'"';
            }
            
            if (!empty($statusdispatch)) {
                $sql1 .= ' AND '.db_prefix().'FpoDispatchMaster.FpoStatus = "'.$statusdispatch.'"';
            }
            
            if($Role == "1"){
                $sql1 .= ' AND tblFpoOrderMaster.FPOID = "'.$UserID.'"';
            }
        
            $sql1 .= ' ORDER BY tblFpoOrderDetails.DispatchID ASC';
            $sql ='SELECT '.db_prefix().'FpoOrderMaster.*,tblitems.ItemName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS company,tblPlantMaster.PlantName,farmer_clients.company AS farmer_name,
            tblFpoOrderDetails.Rate AS farmer_rate,tblFpoOrderDetails.NetWgt AS weight,tblFpoOrderDetails.NetRate,tblFpoOrderDetails.Amount AS NetAmt,tblFpoDispatchMaster.VehicleNo,tblFpoDispatchMaster.FpoStatus,
            tblFpoDispatchMaster.CenterID,tblFpoOrderDetails.DispatchID,tblFpoOrderDetails.Transdate AS Dispatch_Date,tblCenterMaster.CenterName
            FROM '.db_prefix().'FpoOrderMaster 
            INNER JOIN  tblstaff ON tblstaff.AccountID = tblFpoOrderMaster.FPOID ';
            if(!is_admin()){
                $sql .= ' INNER JOIN tblstaff_wise_items ON tblstaff_wise_items.ItemID = tblFpoOrderMaster.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'" ';
            }
            $sql .= ' LEFT JOIN  tblPlantMaster ON tblPlantMaster.PlantID = tblFpoOrderMaster.PartyID 
            INNER JOIN  tblitems ON tblitems.ItemID = tblFpoOrderMaster.ItemID AND tblitems.PlantID = tblFpoOrderMaster.PlantID
            INNER JOIN  tblFpoOrderDetails ON tblFpoOrderDetails.OrderID = tblFpoOrderMaster.OrderID AND tblFpoOrderDetails.TType = "D" AND tblFpoOrderDetails.PlantID = tblFpoOrderMaster.PlantID AND tblFpoOrderDetails.FY = tblFpoOrderMaster.FY
            INNER JOIN  tblFpoDispatchMaster ON tblFpoDispatchMaster.DispatchID = tblFpoOrderDetails.DispatchID AND tblFpoDispatchMaster.PlantID = tblFpoOrderDetails.PlantID AND tblFpoDispatchMaster.FY = tblFpoOrderDetails.FY
            INNER JOIN tblclients AS farmer_clients ON farmer_clients.AccountID = tblFpoOrderDetails.AccountID 
            LEFT JOIN  tblCenterMaster ON tblCenterMaster.CenterID = tblFpoDispatchMaster.CenterID 
            WHERE '.$sql1;
            $result = $this->db->query($sql)->result_array();
            return $result;
        }
        
        public function edit_dispatch_order($data,$id)
        {
            if (isset($data['pur_order_detail'])) {
                $pur_order_detail = json_decode($data['pur_order_detail']);
                unset($data['pur_order_detail']);
                
                $es_detail = [];
                
                $header_before_dynamic = ['id', 'NetWeight','PendingQty','DispatchQty','DispatchBag','UOM', 'Bag', 'Rate'];
                $header_after_dynamic = ['Deduction', 'NetRate', 'tenweight', 'NetAmt'];
        
                foreach ($pur_order_detail as $row) {
                    if (!empty($row[0])) {
                        $before_dynamic = array_slice($row, 0, count($header_before_dynamic));
                        $after_dynamic = array_slice($row, -count($header_after_dynamic));
        
                        $combined = [];
                        foreach ($header_before_dynamic as $index => $header_name) {
                            $combined[$header_name] = $before_dynamic[$index] ?? null;
                        }
                        foreach ($header_after_dynamic as $index => $header_name) {
                            $combined[$header_name] = $after_dynamic[$index] ?? null;
                        }
                        $es_detail[] = $combined;
                    }
                }
                
                $allDispatched = true;
                foreach ($pur_order_detail as $row) {
                    if (!is_array($row) || empty($row[0])) continue;
        
                   $pendingQty = floatval($row[2]);     
                    $dispatchQty = floatval($row[3]);     
                
                    if ($pendingQty != $dispatchQty) {
                        $allDispatched = false;
                        break;
                    }
                }
        
                $status = $allDispatched ? 3 : 2;
            }
            
            $PlantID = $this->session->userdata('root_company'); 
            $FY = $this->session->userdata('finacial_year');
            $FpoOrderNo = $data['disorder'];
            $CenterID = $data['CenterID'];
            $vehicle_no = $data['vehicle_no'];
            $fpo_disorderNumbar = get_option('next_DIS_number_for_kirti');
            $new_fpo_DisorderNumbar = 'DIS'.$FY.$fpo_disorderNumbar;   
            $ItCount = count($es_detail);
            $Transdate =  to_sql_date($data['FPO_Date'])." ".date('H:i:s');
            $ItemID =  $data['ItemID'];
            $ParameterList = $this->GetItemDetails($ItemID);
            $fpolist = $data['fpoidname'];
            $Rate = $data['rate'];
            
            $total_net_wgt =  $data['total_net_wgt'];
            $Total_bag =  $data['Total_bag'];
            $total_tent_wgt =  $data['total_tent_wgt'];
            $total_amt =  $data['total_amt'];
            $PartyDetails = $this->GetPurchaseForParty($CenterID,$ItemID);
            if($PartyDetails){
                $PartyID = $PartyDetails->PartyID;
            }else{
                $PartyID = "KOIL";
            }
            
            $totalDispatchBags = 0;
            foreach($es_detail as $value)
            {
                if (empty($value['id'])) {
                    log_message('error', 'Skipping insert: AccountID is empty');
                    continue;
                }
                if($value['DispatchQty'] > 0)
                {
                    $totalDispatchBags += $value['DispatchBag'];
                    
                    $DispatchDetails = array(
                        'NetWgt'=>$value['DispatchQty'],
                        'Bag'=>$value['DispatchBag'],
                        'TentativeWgt'=>$value['tenweight'],
                    );
                    $this->db->WHERE('DispatchID', $id);
                    $this->db->WHERE('AccountID', $value['id']);
                    $this->db->WHERE('TType', 'D');
                    $this->db->update(db_prefix() . 'FpoOrderDetails',$DispatchDetails);
                }
            }
            
            $UpdateFpoDisatchStatus = array(
                'Status'=>$status,
            );
            $this->db->WHERE('OrderID', $FpoOrderNo);
            $this->db->update(db_prefix() . 'FpoOrderMaster',$UpdateFpoDisatchStatus);
            
            //update bag ledger qty 
            $UpdateBagLedger = array(
                    'Qty'=>$totalDispatchBags,
                );
            $this->db->where('OrderID', $FpoOrderNo);
            $this->db->where('DispatchID', $id);
            $this->db->where('AccountID', $fpolist); 
            $this->db->update(db_prefix() . 'BagLedger', $UpdateBagLedger);
            return true;
        }
        
        public function GetWarehouseDetails($CenterID)
        {
            $this->db->select('tblwarehouse.AccountID,tblwarehouse.w_name');
			$this->db->where('center', $CenterID);
			$WHList = $this->db->get('tblwarehouse')->result_array();
		    return $WHList;
        }
        
        public function GetChamberList($GodownID)
		{
			$this->db->select('tblWHSizeMaster.CHID,tblWHSizeMaster.ChaumberName,tblWHSizeMaster.WHID');
			$this->db->from(db_prefix() .'WHSizeMaster');
			$this->db->where('WHID', $GodownID);
			return $this->db->get()->result_array();
		}
		
		public function GetWarehouseStackList($CHID)
		{
			$this->db->select('tblwhstackmaster.StackID,tblwhstackmaster.StackName');
			$this->db->from(db_prefix() .'whstackmaster');
			$this->db->where('CHID', $CHID);
			return $this->db->get()->result();
		}
		
		public function GetStackLotList($StackID)
		{
			$this->db->select('tbllot_master.LOTID,tbllot_master.LotName');
			$this->db->from(db_prefix() .'lot_master');
			$this->db->where('StackID', $StackID);
			return $this->db->get()->result();
		}
		
		public function UpdateStackDetails($requestData)
		{
		   
		    $OrderID = $requestData['OrderID'];
		    $DispatchID = $requestData['DispatchID'];
		    $ItemID = $requestData['ItemID'];
		    $PartyID = $requestData['PartyID'];
		    $StackDetails = $requestData['StackList'];
		    $FarmerID = $requestData['AccountID'];
		    
		    $this->db->where('BookingID', $OrderID);
            $this->db->where('Gate_in_ID', $DispatchID);
            $this->db->delete(db_prefix() . 'QCParameterValues');
        
            $this->db->where('BookingID', $OrderID);
            $this->db->where('GateINID', $DispatchID);
            $this->db->delete(db_prefix() . 'stockInventory');
		    
		    $Layer = 1;
		    foreach($StackDetails as $key => $val)
            {
                $has_valid_param = false;
                foreach ($val as $paramID => $paramValue) {
                    if (is_numeric($paramID) && trim($paramValue) !== "") {
                        $has_valid_param = true;
                        break;
                    }
                }
                
                if (!$has_valid_param) {
                    continue;
                }
    
                foreach ($val as $paramID => $paramValue)
                {
                    if (!is_numeric($paramID) || trim($paramValue) === "") continue;
        
                    $insert_Qc_array = array(
                        "BookingID"       => $OrderID,
                        "Gate_in_ID"      => $DispatchID,
                        "TType"           => "F",
                        "ItemID"          => $ItemID,
                        "layer_number"    => $Layer,
                        "ItemParameterID" => $paramID,
                        "ParameterValue" => $paramValue, 
                        "EParameterValue"=>$paramValue,
                        "HParameterValue"=>$paramValue,
                        "deductionAmt"=>0,
                        "UserID"=>$this->session->userdata('username'),    
                        "TransDate"=>date('Y-m-d h:i:s'),
                    );
                    $this->db->insert('tblQCParameterValues', $insert_Qc_array);
                }
               
                $insert_inventory = array(
                    "BookingID"  => $OrderID,
                    "GateINID"   => $DispatchID,
                    "TransID"    => $DispatchID,
                    "QCID"       => $Layer,
                    "CenterQCApprove"=>"Y",
                    "ROQCApprove"=>"Y",
                    "HOQCApprove"=>"Y",
                    "TransDate"  =>date('Y-m-d h:i:s'),
                    "TType"      =>"P",
                    "ItemID"     =>$ItemID,
                    "AccountID"  =>$FarmerID,
                    "PartyID"    =>$PartyID,
                    "WHID"       =>$val['GodownID'],
                    "CHID"       =>$val['CHID'],
                    "StackID"    =>$val['StackID'],
                    "LOTID"      =>$val['LOTID'],
                    "Weight"     =>$val['lot_weight'],
                    "BagQty"     =>$val['bag'],
                    "UserID"     =>$this->session->userdata('username'),    
                );
                $this->db->insert('tblstockInventory', $insert_inventory);
                $Layer++; 
            }
            
            $UpdateStatus = array(
                    'FpoStatus'=>2, //inward in progress
                );
            $this->db->WHERE('OrderID', $OrderID);
	        $this->db->WHERE('DispatchID', $DispatchID);
            $update_success  = $this->db->update(db_prefix() . 'FpoDispatchMaster',$UpdateStatus);
		}
		
		public function GetQcStackDetails($OrderID,$DispatchID)
		{
		    $this->db->select('tblQCParameterValues.*');
			$this->db->from(db_prefix() .'QCParameterValues');
			$this->db->where('BookingID', $OrderID);
			$this->db->where('Gate_in_ID', $DispatchID);
			return $this->db->get()->result();
		}
		
		public function GetInvDetails($OrderID,$DispatchID)
		{
		    $this->db->select('tblstockInventory.*');
			$this->db->from(db_prefix() .'stockInventory');
			$this->db->where('BookingID', $OrderID);
			$this->db->where('GateINID', $DispatchID);
			return $this->db->get()->result();
		}
		
		public function GetInwardQcData($OrderID,$id)
		{
		    $this->db->select('tblQCParameterValues.*');
			$this->db->from(db_prefix() .'QCParameterValues');
			$this->db->where('BookingID', $OrderID);
			$this->db->where('Gate_in_ID', $id);
			$QcDetails = $this->db->get()->result();
			
			$this->db->select('tblstockInventory.*, tblwarehouse.w_name As GodownName, tblWHSizeMaster.ChaumberName AS Chembername, tblwhstackmaster.StackName, tbllot_master.LotName');
            $this->db->from(db_prefix() . 'stockInventory');
            $this->db->join('tblwarehouse', 'tblwarehouse.AccountID = tblstockInventory.WHID', 'left');
            $this->db->join('tblWHSizeMaster', 'tblWHSizeMaster.CHID = tblstockInventory.CHID', 'left');
            $this->db->join('tblwhstackmaster', 'tblwhstackmaster.StackID = tblstockInventory.StackID', 'left');
            $this->db->join('tbllot_master', 'tbllot_master.LOTID = tblstockInventory.LOTID', 'left');
            
            $this->db->where('tblstockInventory.BookingID', $OrderID);
            $this->db->where('tblstockInventory.GateINID', $id);
			$inventoryDetails = $this->db->get()->result();
			
			$inventoryByLayer = [];
            foreach ($inventoryDetails as $inv) {
                $layer = $inv->QCID;
                $inventoryByLayer[$layer] = [
                    'GodownID'   => $inv->WHID,
                    'GodownName' => $inv->GodownName,
                    'CHID'       => $inv->CHID,
                    'ChemberName'=> $inv->Chembername,
                    'StackID'    => $inv->StackID,
                    'StackName'  => $inv->StackName,
                    'LOTID'      => $inv->LOTID,
                    'LotName'    => $inv->LotName,
                    'lot_weight' => $inv->Weight,
                    'bag'        => $inv->BagQty,
                ];
            }
   
            $result = [];
        
            foreach ($QcDetails as $qc) {
                $layer = $qc->layer_number;
                $paramID = $qc->ItemParameterID;
                $value = $qc->ParameterValue;
        
                if (!isset($result[$layer])) {
                    $result[$layer] = [];
                }
        
                $result[$layer][$paramID] = $value;
            }
   
            foreach ($inventoryByLayer as $layer => $invData) {
                if (!isset($result[$layer])) {
                    $result[$layer] = [];
                }
        
                $result[$layer] = array_merge($result[$layer], $invData);
            }
            return array_values($result);
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
        
        public function add_payment_entry($data)
        {
            $payment_entry = $data['payments'];
           
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            $payment_date = to_sql_date($data['payment_date'])." ".date('H:i:s');
            $date= to_sql_date($data['payment_date']);
            $month = substr($payment_date,5,2);
            $get_result_to_cur_date = $this->get_result_to_cur_date_payments($date);
            $GetLastUniqueNo = $this->GetLastUniqueNo($date);
            $LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
           
            $get_result_to_cur_date_journal = $this->get_result_to_cur_date_journal($date);
            
            $i = 1;
            foreach ($payment_entry as $key => $value) 
            {
                if (!empty($key)) 
                {
                    //payment voucher
                    if(empty($get_result_to_cur_date))
                    {
                        if($selected_company == 1){
                            $new_tax_transactionNumber = get_option('next_payment_number_for_kirti');
                        }
                        $new_voucher_number = $new_tax_transactionNumber;
                    }else{
                        
                        $count = count($get_result_to_cur_date);
                        $last_index = $count - 1;
                        $new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
                        
                        $incNo = (int) $new_voucher_number - 1;
                        $sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
                        $this->db->query($sql);
                        if ($this->db->affected_rows() > 0) {
                            $this->increment_next_payment_number();
                        }
                    }
                    
                    //Journal Voucher Entry
                    if(empty($get_result_to_cur_date_journal)){
                        $selected_company = $this->session->userdata('root_company');
                        if($selected_company == 1){
                            $new_journalNumber = get_option('next_journal_number_for_kirti');
                        }
                        $new_voucher_number_journal = $new_journalNumber;
                    }else{ 
                        $count = count($get_result_to_cur_date_journal);
                        $last_index = $count - 1;
                        $new_voucher_number_journal = $get_result_to_cur_date_journal[$last_index]['VoucherID'];
                        
                        $incNo = (int) $new_voucher_number_journal - 1;
                        $sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
                        $this->db->query($sql);
                        if ($this->db->affected_rows() > 0) {
                            $this->increment_next_journal_number();
                        }
                    }
            
                    $FpoRate = isset($data['ratefpo']) ? floatval($data['ratefpo']) : 0;
                    $farmerrate = isset($value['Rate']) ? floatval($value['Rate']) : 0;
                    $Orderwt = isset($value['NetWeight']) ? floatval($value['NetWeight']) : 0;
            
                    $JournalAmt = ($FpoRate - $farmerrate) * $Orderwt;
                   
                    // Insert Ledger Entry
                    $credit_data = array(
                        "PlantID" =>$selected_company,
                        "Transdate" =>$payment_date,
                        "TransDate2" =>date('Y-m-d H:i:s'),
                        "VoucherID" =>$new_voucher_number,
                        "AccountID" =>$key,
                        "TType" =>"C",
                        "CenterID" =>"",
                        "CommodityID" =>$data['Itemid'],
                        "EntryFor" =>"2",
                        "PartyID" => $data['partyid'],
                        "Amount" => $value['netAmt'],
                        "Narration" => "Payment Against OrderID " . $data['FpoOrderID'],
                        "CounterAccount" =>$data['paymode'],
                        "PassedFrom" =>"PAYMENTS",
                        "OrdinalNo" =>$i,
                        "UserID" =>$this->session->userdata('username'),
                        "FY" =>$fy,
                        "UniquID" =>$LastUniqueID,
                    );
                    
                    $this->db->insert(db_prefix().'accountledger', $credit_data);
                    $i++;
                    $debit_data = array(
                            "PlantID" =>$selected_company,
                            "Transdate" =>$payment_date,
                            "TransDate2" =>date('Y-m-d H:i:s'),
                            "VoucherID" =>$new_voucher_number,
                            "AccountID" =>$data['paymode'],
                            "CounterAccount" =>$key,
                            "TType" =>"D",
                            "CenterID" =>"",
                            "CommodityID" =>$data['Itemid'],
                            "EntryFor" =>"2",
                            "PartyID" =>$data['partyid'],
                            "Amount" => $value['netAmt'],
                            "Narration" => "Payment Against OrderID " . $data['FpoOrderID'],
                            "PassedFrom" =>"PAYMENTS",
                            "OrdinalNo" =>$i,
                            "UserID" =>$this->session->userdata('username'),
                            "FY" =>$fy,
                            "UniquID" =>$LastUniqueID,
                        );
                    $this->db->insert(db_prefix().'accountledger', $debit_data);
                    $i++;
                    //journal voucher entry
                    $Fpo_journal_voucher = array(
                        "PlantID" =>$selected_company,
                        "Transdate" =>$payment_date,
                        "TransDate2" =>date('Y-m-d H:i:s'),
                        "VoucherID" =>$new_voucher_number_journal,
                        "AccountID" =>$data['TraderID'],
                        "CounterAccount" =>"FPOCOMM",
                        "TType" =>'C',
                        "Amount" =>$JournalAmt,
                        "CenterID" =>"",
                        "CommodityID" =>$data['Itemid'],
                        "EntryFor" =>"2",
                        "PartyID" =>$data['partyid'],
                        "Narration" =>"Comission Against OrderID ". $data['FpoOrderID'] ."/Purchase From ". $key,
                        "PassedFrom" =>"JOURNAL",
                        "OrdinalNo" =>$i,
                        "UserID" =>$this->session->userdata('username'),
                        "FY" =>$fy,
                        "UniquID" =>$LastUniqueID,
                        );
                    $this->db->insert(db_prefix().'accountledger', $Fpo_journal_voucher);
                    $i++;
                    
                    //journal voucher entry
                    $Fpo_journal_voucher = array(
                        "PlantID" =>$selected_company,
                        "Transdate" =>$payment_date,
                        "TransDate2" =>date('Y-m-d H:i:s'),
                        "VoucherID" =>$new_voucher_number_journal,
                        "AccountID" =>"FPOCOMM",
                        "CounterAccount" =>$data['TraderID'],
                        "TType" =>'D',
                        "Amount" =>$JournalAmt,
                        "CenterID" =>"",
                        "CommodityID" =>$data['Itemid'],
                        "EntryFor" =>"2",
                        "PartyID" =>$data['partyid'],
                        "Narration" =>"Comission Against OrderID ". $data['FpoOrderID'] ."/Purchase From ". $key,
                        "PassedFrom" =>"JOURNAL",
                        "OrdinalNo" =>$i,
                        "UserID" =>$this->session->userdata('username'),
                        "FY" =>$fy,
                        "UniquID" =>$LastUniqueID,
                        );
                    $this->db->insert(db_prefix().'accountledger', $Fpo_journal_voucher);
                
                    $i++;
                    
                    if(empty($get_result_to_cur_date)){
                        $this->increment_next_payment_number();
                    }
                    
                    if(empty($get_result_to_cur_date)){
                        $this->increment_next_journal_number();
                    }
                }
            }
            
            $Update_status = array(
                'PayMode'=>$data['paymode'],
                'PaymentDate'=>to_sql_date($data['payment_date'])." ".date('H:i:s'),
                'PayAmt'=>(float) str_replace(',', '', $data['total_amt']),
                'PaymentStatus'=>2,
                );
            $this->db->WHERE('OrderID', $data['FpoOrderID']);
            $this->db->update(db_prefix() . 'FpoOrderMaster',$Update_status);
            return true;
        }
        
        public function get_result_to_cur_date_payments($payment_date)
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            /*$this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "PAYMENTS");
            $this->db->where('Transdate >', $payment_date);
            $this->db->order_by("VoucherID", "desc");
            $journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
            return $journal_data;*/
            
            $fy_ne = $fy + 1;
            $las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
            $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$payment_date.' H:i:s" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
            $staff_data = $this->db->query($sql)->result_array();
            return $staff_data;
        }
        
        public function get_result_to_cur_date_journal($journal_date){
        
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            /*$this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "JOURNAL");
            $this->db->where('Transdate >', $journal_date);
            $this->db->order_by("VoucherID", "desc");
            $journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
            return $journal_data;*/
            
            $fy_ne = $fy + 1;
            $las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
            $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "JOURNAL" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$journal_date.' H:i:m" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
            $journal_data = $this->db->query($sql)->result_array();
            return $journal_data;
            
        }
        
        public function increment_next_journal_number()
        {
            // Update next Journal number in settings
            $FY = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
                if($selected_company == 1){
                    $this->db->where('name', 'next_journal_number_for_kirti');
                }
            $this->db->set('value', 'value+1', false);
            $this->db->WHERE('FY', $FY);
            $this->db->update(db_prefix() . 'options');
        }
        
        public function GetLastUniqueNo()
        {
            $fy = $this->session->userdata('finacial_year');
            $selected_company = $this->session->userdata('root_company');
            
            $sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'"  GROUP BY UniquID ORDER BY abs(tblaccountledger.UniquID) DESC ';
            $UniqueID = $this->db->query($sql)->result_array();
            return $UniqueID;
        }
        
        public function increment_next_payment_number()
        {
           $FY = $this->session->userdata('finacial_year'); 
           $selected_company = $this->session->userdata('root_company');
                if($selected_company == 1){
                    $this->db->where('name', 'next_payment_number_for_kirti');
                }
            $this->db->set('value', 'value+1', false);
            $this->db->WHERE('FY', $FY);
            $this->db->update(db_prefix() . 'options');
        }
        
        public function FetchRate($Fpolist,$ItemID,$CenterID)
        {
            $FY = $this->session->userdata('finacial_year'); 
            $selected_company = $this->session->userdata('root_company');
            $this->db->select('tblFpoRateMaster.Rate');
			$this->db->from(db_prefix() .'FpoRateMaster');
			$this->db->where('ItemID', $ItemID);
			$this->db->where('FPOID', $Fpolist);
			$this->db->where('CenterID', $CenterID);
			$this->db->WHERE('FY', $FY);
			$this->db->WHERE('PlantID', $selected_company);
			$this->db->WHERE('Status', 'Y');
			return $this->db->get()->row();
        }
        
        public function CheckDispatch($id)
        {
            $FY = $this->session->userdata('finacial_year'); 
            $selected_company = $this->session->userdata('root_company');
            $this->db->select('tblFpoDispatchMaster.*');
			$this->db->from(db_prefix() .'FpoDispatchMaster');
			$this->db->where('OrderID', $id);
			$this->db->WHERE('FY', $FY);
			$this->db->WHERE('PlantID', $selected_company);
			return $this->db->get()->result();
        }
        
        public function AddBagLedger($data)
        {
            $IsOpenEntryExist = $this->CheckFpoOpenExist($data['FpoList']);
            if(!empty($IsOpenEntryExist) && $data['BagType'] == 'Open')
            {
                $updatebagledger = array(
                        'Qty'=>$data['BagQty'],
                    );
                $this->db->where('id', $IsOpenEntryExist->id);
                $this->db->update(db_prefix() . 'BagLedger',$updatebagledger);
                if($this->db->affected_rows() > 0)
                {
                    return true;
                }
            }
            else
            {
                $bagledger = array(
                        'AccountID'=>$data['FpoList'],
                        'Transdate'=>to_sql_date($data['BagDate'])." ".date('H:i:s'),
                        'Type'=>'C',
                        'Qty'=>$data['BagQty'],
                        'PassedFrom'=>$data['BagType'],
                    );
                $this->db->insert(db_prefix() . 'BagLedger',$bagledger);
                if($this->db->affected_rows() > 0)
                {
                    return true;
                }
            }
        }
        
        public function CheckFpoOpenExist($FpoList)
        {
            $this->db->select('tblBagLedger.*');
			$this->db->from(db_prefix() .'BagLedger');
			$this->db->where('AccountID', $FpoList);
			$this->db->WHERE('PassedFrom', 'Open');
			return $this->db->get()->row();
        }
        
        public function GetBagLedgerData($data = [])
        {
            $UserID = $this->session->userdata('username');
            $role = $this->session->userdata('role');
            $this->db->select('tblBagLedger.*');
    		$this->db->from(db_prefix() . 'BagLedger');
    		
    		if($role == 1){
    		    $this->db->where('AccountID', $UserID);
    		}else if (!empty($data['Fpolist'])) {
                $this->db->where('AccountID', $data['Fpolist']);
            }
    		
    		if (!empty($data['from_date']) && !empty($data['to_date'])) {
                $fromDateObj = $data['from_date']." 00:00:00";
                $toDateObj = $data['to_date']." 23:59:59";
        
                if ($fromDateObj && $toDateObj) {
                   
                    $this->db->where('Transdate >=', $fromDateObj);
                    $this->db->where('Transdate <=', $toDateObj);
                }
            }
    		$this->db->order_by( db_prefix() .'BagLedger.Transdate','ASC');
    		return $this->db->get()->result_array();
        }
        
        public function GetBagOpenQtyData()
        {
            $this->db->select('tblBagLedger.Type,Sum(Qty) AS Total_qty');
    		$this->db->from(db_prefix() . 'BagLedger');
    		$this->db->where('PassedFrom', 'Open');
    		
    		$this->db->group_by( db_prefix() .'BagLedger.Type');
    		$this->db->order_by( db_prefix() .'BagLedger.Transdate','ASC');
    		return $this->db->get()->result_array();
        }
        
        public function GetDispatchInfo($OrderID,$DispatchID)
        {
            $this->db->select('tblFpoDispatchMaster.*');
            $this->db->from(db_prefix() . 'FpoDispatchMaster');
            $this->db->where('OrderID', $OrderID);
            $this->db->where('DispatchID', $DispatchID);
            $FPOdetails = $this->db->get()->row();
            if ($FPOdetails) {
                $this->db->select('tblFpoOrderDetails.*,tblclients.company');
                $this->db->from(db_prefix() . 'FpoOrderDetails');
                $this->db->join('tblclients', 'tblclients.AccountID = tblFpoOrderDetails.AccountID', 'left');
                $this->db->where('OrderID', $OrderID);
                $this->db->where('DispatchID', $DispatchID);
                $OrderDetails = $this->db->get()->result();
                
                $FPOdetails->OrderDetails = $OrderDetails;
            }
            return $FPOdetails;
        }
        
        public function GetDispatchData($OrderID,$DispatchID)
        {
            $this->db->select('tblFpoOrderDetails.*');
            $this->db->where('OrderID', $OrderID);
            $this->db->where('DispatchID', $DispatchID);
            $DispatchOrderDetails = $this->db->get(db_prefix().'FpoOrderDetails')->result_array();
            return $DispatchOrderDetails;
        }
        
        public function FpoDispatchOrderDetails($OrderID,$DispatchID)
        {
            $this->db->select('tblFpoDispatchMaster.*,tblstaff.firstname,tblstaff.lastname');
            $this->db->join('tblstaff', 'tblstaff.AccountID = tblFpoDispatchMaster.FPOID', 'left');
            $this->db->where('OrderID', $OrderID);
            $this->db->where('DispatchID', $DispatchID);
            $DispatchDetails = $this->db->get(db_prefix().'FpoDispatchMaster')->row();
            return $DispatchDetails;
        }
        
        public function GetDispatchEntry($OrderID,$id)
        {
            $this->db->select('tblFpoDispatchMaster.FpoDebitEntry');
            $this->db->where('OrderID', $OrderID);
            $this->db->where('DispatchID', $id);
            $Details = $this->db->get(db_prefix().'FpoDispatchMaster')->row();
            return $Details;
        }
	}