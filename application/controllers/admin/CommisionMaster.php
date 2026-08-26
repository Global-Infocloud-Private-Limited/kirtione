<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class CommisionMaster extends AdminController
	{
		private $not_importable_fields = ['id'];
		public function __construct()
		{
			parent::__construct();
			$this->load->model('CommsionModel');      
			
		}
		
		public function AddEditCommsion()
		{
			if (!has_permission_new('CommsionMaster', '', 'view')) {
				access_denied('CommsionMaster');
			}
			$CommisionMaster =  $this->CommsionModel->GetCommisionMasterData();
			$data['CommisionMaster'] = $CommisionMaster;
			$centermaster =  $this->CommsionModel->get_all_table_data($tablename="tblCenterMaster");
			$data['centermaster'] = $centermaster;
			$trader_list = $this->CommsionModel->GetAccountList();
			$data['trader_list'] = $trader_list;
			$data['item_code'] = $this->CommsionModel->get_items_code();
			$data['company_detail'] = $this->CommsionModel->get_company_detail();
			$this->load->view('admin/CommsionMaster/AddEditCommsion',$data);
		}
		
		public function GetCommisionData()
		{
		    if (!has_permission_new('CommsionMaster', '', 'view')) {
				access_denied('CommsionMaster');
			}
			$filterdata = array(
			'centername' => $this->input->post('centername'),
			'filtervendor'  => $this->input->post('filtervendor'),
			'filterItemCode'  => $this->input->post('filterItemCode')
			);
			
		    $Commisiondata = $this->CommsionModel->GetFilterwiseCommisionData($filterdata);
		    $html = '<table class="table table-bordered" id="filtertable">';
            $html .= '<thead><tr>
                        <th>ItemID</th>
                        <th>Item Name</th>
                        <th>Vendor Name</th>
                        <th>Center Name</th>
                        <th>Commision Amount</th>
                        <th>Commision(%)</th>
                      </tr></thead><tbody>';

            if (!empty($Commisiondata)) {
                foreach ($Commisiondata as $row) {
                    $html .= '<tr>
                                <td>' . htmlspecialchars($row['ItemID']) . '</td>
                                <td>' . htmlspecialchars($row['ProductName']) . '</td>
                                <td>' . htmlspecialchars($row['company']) . '</td>
                                <td>' . htmlspecialchars($row['CenterName']) . '</td>
                                <td>' . htmlspecialchars($row['Amount']) . '</td>
                                <td>' . htmlspecialchars($row['Percent']) . '</td>
                              </tr>';
                }
            } else {
                $html .= '<tr><td colspan="6" class="text-center">No data found</td></tr>';
            }

            $html .= '</tbody></table>';
        
            echo $html;
            exit;
		}
		
		public function CommisionMaster_table_data()
		{
			$Commision =  $this->CommsionModel->GetCommisionMasterData();
			echo json_encode($Commision);
		}
		
		public function insertCommisionDetails()
		{
			if (!has_permission_new('CommsionMaster', '', 'create')) {
				access_denied('CommsionMaster');
			}
			
			$centername = $this->input->post('centername');
			$vendor = $this->input->post('vendor');
			$ItemCode = $this->input->post('ItemCode');
			$CommisionAmt = $this->input->post('CommisionAmt');
			$CommisionPercent = $this->input->post('CommisionPercent');
			
			if(empty($CommisionAmt)){
				$CommisionAmt = null;
			}
			if(empty($CommisionPercent)){
				$CommisionPercent = null;
			}
			
			$insertData = []; // Empty array to hold data for batch insert
			
			foreach ($centername as $center) {
				foreach ($ItemCode as $item) {
				    $where = '(ItemFor="'.$vendor.'" AND ProductID="'.$item.'")';
				    $VendorWiseItem = $this->CommsionModel->get_data($tablename="tblproduct",$where);
				    if($VendorWiseItem){
				        $where = '(CenterID="'.$center.'" AND AccountID="'.$vendor.'" AND ItemID="'.$item.'")';
						$OldDetails = $this->CommsionModel->get_data($tablename="tblCommisionMaster",$where);
						
						if(!empty($OldDetails)){
							$old_data = array(
							'CenterID'=>$OldDetails["CenterID"],
							'AccountID'=>$OldDetails["AccountID"],
							'ItemID'=>$OldDetails["ItemID"],
							'Amount'=>$OldDetails["Amount"],
							'Percent'=>$OldDetails["Percent"],
							'UserID'=>$OldDetails["UserID"],
							'TransDate'=>$OldDetails["TransDate"],
							'UserID2'=>$OldDetails["UserID2"],
							'Lupdate'=>$OldDetails["Lupdate"],
							);				
							if($this->db->insert(db_prefix() . 'CommisionMaster_audit',$old_data)){
								$this->db->where('CenterID', $center);
								$this->db->where('AccountID', $vendor);
								$this->db->where('ItemID', $item);
								$this->db->delete(db_prefix().'CommisionMaster');
							}
						}
						
						$insertData[] = [
						'CenterID' => $center,
						'AccountID' => $vendor,
						'ItemID' => $item,
						'Amount' => $CommisionAmt,
						'Percent' => $CommisionPercent,
						'UserID' => $_SESSION['username'], 
						'TransDate' => date('Y-m-d H:i:s'), 
						];
				    }
				}
			}
			
			$createnewbrand = $this->CommsionModel->insert_batch_data($tablename="tblCommisionMaster",$insertData);
			if ($createnewbrand) {    
				$newBrandId = $this->db->insert_id();       
				echo json_encode(['success' => true,'message' => 'Data inserted successfully']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Failed to insert']);
			}
		}
		
		
		public function GetCommisionDetailsbyID()
		{
			$id = $this->input->post('id');
			$where = '(id="'.$id.'")'; 
			$CommisionDetails = $this->CommsionModel->get_data($tablename="tblCommisionMaster",$where);
			echo json_encode($CommisionDetails);
		}
		
		public function ItemListByVendorID()
		{
			$AccountIDs = $this->input->post('AccountIDs');
			$CommisionDetails = $this->CommsionModel->GetItemListByVendorID($AccountIDs);
			echo json_encode($CommisionDetails);
		}
		
		public function UpdateCommisionDetails()
		{
			if (!has_permission_new('BrandMaster', '', 'edit')) {
				access_denied('BrandMaster');
			}
			$EditId = $this->input->post('EditId');
			$CommisionAmt = $this->input->post('CommisionAmt');
			$CommisionPercent = $this->input->post('CommisionPercent');
			
			if(empty($CommisionAmt)){
				$CommisionAmt = null;
			}
			if(empty($CommisionPercent)){
				$CommisionPercent = null;
			}
			
			$where = '(id="'.$EditId.'")'; 
			$OldDetails = $this->CommsionModel->get_data($tablename="tblCommisionMaster",$where);
			
			if(!empty($OldDetails)){
				$center = $OldDetails["CenterID"];
				$vend = $OldDetails["AccountID"];
				$item = $OldDetails["ItemID"];
				$old_data = array(
				'CenterID'=>$OldDetails["CenterID"],
				'AccountID'=>$OldDetails["AccountID"],
				'ItemID'=>$OldDetails["ItemID"],
				'Amount'=>$OldDetails["Amount"],
				'Percent'=>$OldDetails["Percent"],
				'UserID'=>$OldDetails["UserID"],
				'TransDate'=>$OldDetails["TransDate"],
				'UserID2'=>$OldDetails["UserID2"],
				'Lupdate'=>$OldDetails["Lupdate"],
				);				
				if($this->db->insert(db_prefix() . 'CommisionMaster_audit',$old_data)){
					$this->db->where('id', $EditId);
					if($this->db->delete(db_prefix().'CommisionMaster')){
						$insert = [
						'CenterID' => $center,
						'AccountID' => $vend,
						'ItemID' => $item,
						'Amount' => $CommisionAmt,
						'Percent' => $CommisionPercent,
						'UserID' => $_SESSION['username'], 
						'TransDate' => date('Y-m-d H:i:s'), 
						];
						
						$createnew = $this->CommsionModel->insert_data($tablename="CommisionMaster",$insert);
						if ($createnew) {          
							echo json_encode(['success' => true,'message' => 'Data inserted successfully']);
							} else {
							echo json_encode(['success' => false, 'message' => 'Failed to insert card']);
						}
					}
				}
			}
			
			
		}
		
		public function export_filterwiseCommisionList()
		{
		    if (!has_permission_new('CommsionMaster', '', 'view')) {
				access_denied('CommsionMaster');
			}
			
			if (!class_exists('XLSXReader_fin')) {
                require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
            }

            require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            
            if ($this->input->post()) 
            {
                $company_detail = $this->CommsionModel->get_company_detail();
    
                $post_data = $this->input->post();
    			
    			$centerid = $post_data['centerid'];
                $vendor = $post_data['vendor'];
                $ItemCode = $post_data['ItemCode'];
                
    			$CenterName = $post_data['CenterName'];
                $VendorName =  $post_data['VendorName'];
                $ItemName = $post_data['ItemName'];
                $result = $this->CommsionModel->GetFilterwiseCommisionData($post_data);
                
                $center='';
                if($centerid !=="")
                { $center = $CenterName;
                }else { $center = "All" ; }
        		
        		$vendors = '';
                if($vendor !=="")
                { $vendors = $VendorName;
                }else { $vendors = "All" ; }
        
        		$items = '';
                if($ItemCode !=="")
                { $items = $ItemName; }
                else
                { $items = "All"; }   
    
                $writer = new XLSXWriter();
    
                $company_name = array($company_detail->company_name);
    
                $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 10);  
    
                $writer->writeSheetRow('Sheet1', $company_name);
    
                $address = $company_detail->address;
    
                $center_addr = array($address, );	  
    	    
    			$filters = "Center Name: " . $center . ", Vendor Name: " . $vendors . ", Items: " . $items;
    	    
    			$filter_row = array($filters);
                
                $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells
    
                $writer->writeSheetRow('Sheet1', $center_addr);
    
    			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 14);  //merge cells	   
    
                $writer->writeSheetRow('Sheet1', $filter_row);
    
                $set_col_tk = [];
     	         
                $set_col_tk["ItemID"] = 'ItemID';
                $set_col_tk["ProductName"] = 'Item Name';
                $set_col_tk["company"] = 'Vendor Name';
				$set_col_tk["CenterName"] = 'Center Name';
                $set_col_tk["Amount"] = 'Commision Amount';
                $set_col_tk["Percent"] = 'Commision(%)';
    
                $writer_header = $set_col_tk;
    
                $writer->writeSheetRow('Sheet1', $writer_header);
    
                foreach ($result as $k => $value) 
                {  
                    $list_add = [];  

                    $list_add[] = $value["ItemID"];
                    $list_add[] = $value["ProductName"];
					$list_add[] = $value["company"];
                    $list_add[] = $value["CenterName"];
                    $list_add[] = $value["Amount"];
                    $list_add[] = $value["Percent"];

                    $writer->writeSheetRow('Sheet1', $list_add); 
                }	   
    			
                $sum_row = [];
                $sum_row[] = ''; 
                $sum_row[] = '';
                $sum_row[] = ''; 
                $sum_row[] = ''; 
  		        $sum_row[] = ''; 
				$sum_row[] = '';
    
    			$writer->writeSheetRow('Sheet1', $sum_row);
                $files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
    
                foreach ($files as $file) {
    
                    if (is_file($file)) {
    
                        unlink($file);
                    }
                }
    
                $filename = 'CommisionReport.xlsx';
    
                $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
    
                echo json_encode([
    
                    'site_url' => site_url(),
    
                    'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,
    
                ]);
                die;
            }
		}
		
	}														