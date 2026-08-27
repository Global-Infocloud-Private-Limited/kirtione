<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Booking_list extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->Model('Booking_list_model');
        $this->load->model('GateControl_model');
        $this->load->helper('url', 'form');
        $this->load->model('sale_reports_model');
    }
    
    public function index()
    {
        if (!has_permission_new('Booking_list', '', 'view')) {
				access_denied('customers');
			}
        $data['title'] = 'Booking List';
        $data['items'] = $this->Booking_list_model->getItemsData();
        $data['warehouses'] = $this->Booking_list_model->getWarehouseData();
        $data['centers'] = $this->Booking_list_model->getCenter();
        $data['company_detail'] = $this->sale_reports_model->get_company_detail();
        $this->load->view('admin/booking_list/booking_list',$data);
    }
    
    public function GetAllBookings()
    {
        $CenterID = $this->input->post('CenterID');
        $data = array(
           'AccountID' => $this->input->post('AccountID'),
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'CenterID'  => $CenterID,
           'ItemID'  => $this->input->post('ItemID'),
           'BookingType' => $this->input->post('BookingType'),
           'IsApprove'  => $this->input->post('IsApprove'),
        );
        
        $BookingList = $this->Booking_list_model->GetAllBookingsDB($data);
        
        $html ='';
        $sr = 1;
        $TotalWeight = 0;
        foreach($BookingList as $value){
            
            if($value["IsApprove"] == 'Y'){
                $status = 'ACCEPTED';
            }
            if($value["IsApprove"] == 'Y' && $value["ClientApprove"] == 'N'){
                $status = "Waiting for party approval";
            }
            if($value["IsApprove"] == 'N'){
                $status = 'REJECTED';
            }
            if($value["IsApprove"] == 'NA'){
                $status = 'NO ACTION';
            }
            if($value["status"] == '2'){
                $status = "COMPLATED";
            }
            if($value["status"] == '3'){
                $status = "PARTIAL COMPLATED";
            }
            if($value['company'] != ''){
                $name = $value['company'];
            }
            else{
                $name = $value['firstname'].' '.$value['lastname'];
            }
            $url = admin_url().'Booking_list/GetBookingListDetails/'.$value["BookingID"];
            $html.= '<tr class="GetDetails" data-id="'.$value["BookingID"].'" >';
            $html.= '<td style="text-align:center;">'.$sr.'</td>';
            $html.= '<td style="text-align:left;">'.$name.'</td>';
            $html.= '<td style="text-align:left;">'.$value["TType2"].'</td>';
            $html.= '<td style="text-align:left;">'.$value["BookingID"].'</td>';
            $html.= '<td style="text-align:left;">'._d(substr($value['TransDate'],0,10)).'</td>';
            $html.= '<td style="text-align:left;">'.$value["CenterName"].'</td>';
            $html.= '<td style="text-align:left;">'.$value["ItemName"].'</td>';
            $html.= '<td style="text-align:center;">'.$value["quantity"].' '.$value["unit"].'</td>';
            $TotalWeight += $value["quantity"];
            $html.= '<td style="text-align:center;">'.$status.'</td>';
            
            if($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] !=""){
               $PCSoftStatus =  $value["pcsoft_doc_ref"];
            }else if($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] ==""){
                $PCSoftStatus = '<button type="button" onclick=ReSendTradeToPcSoft("'.$value["BookingID"].'") id="ReSendTradeToPcSoft" class="btn btn-info">Send To NewERP</button>';
            }else{
                $PCSoftStatus = "--";
            }
            $html.= '<td>'.$PCSoftStatus.'</td>';
            
            $html.= '</tr>';
            $sr++;
        }
        $html.= '<tr>';
        $html.= '<td style="text-align:right;" colspan="7"><b>Total</b></td>';
        $html .= '<td style="text-align:right">'.number_format($TotalWeight, 2, '.', '').'</td>';
        $html.= '<td style="text-align:right;" colspan="2"></td>';
        $html.= '</tr>';
        echo json_encode($html);
    }
    
    public function GetBookingListDetails($BookingID)
    { 
        $data['title']  = "Booking Details";
        $data['OrderDetails']  = $this->Booking_list_model->GetBookingListDetailsDB($BookingID);
        $data['OrderList']  = $this->Booking_list_model->GetBookingsFromBookingIDDB($BookingID);
        $this->load->view('admin/booking_list/booking_list_details',$data);    
    }
    
    public function increment_number($CenterID,$TType)
    {
        $this->db->set('Number', 'Number+1', false);
        $this->db->WHERE('CenterID', $CenterID);
        $this->db->WHERE('TType', $TType);
        $this->db->update(db_prefix() . 'numberformat');
    }
    
    public function saveGateControl()
	{
		    $BookingID = $this->input->post('BookingID');
		    $check_asn = $this->Booking_list_model->CheckAsnLockDB($BookingID);
		   
		    if(empty($check_asn)){
		        $this->db->where('BookingID', $BookingID);
    			$details = $this->db->get('tbllead_master')->row();
    			$CenterID = $details->CenterID;
    			
    			$new_Number = get_number($CenterID,'ASN');
                $number = str_pad($new_Number, 4, '0', STR_PAD_LEFT);
                $AsnID = "ASN".$CenterID.date('d').date('m').date('y').$number;
                
                $this->increment_number($CenterID,'ASN');
                
    			$data = array(
                    'AccountID' => $this->input->post('AccountID'),
                    'ASNID' => $AsnID,
                    'BookingID' => $this->input->post('BookingID'),
                    'basic_rate' => $this->input->post('basic_rate'),
                    'ItemID' => $this->input->post('ItemID'),
                    'quantity' => $this->input->post('Quantity'),
                    'unit' => $this->input->post('Unit'),
                    'asn_date' => date('Y-m-d H:i:s'),
                    'asn_by' => $this->session->userdata('username'),
                    'TType' => $this->input->post('TType'),
                    'TType2' => $this->input->post('TType2'),
    			);
    			
    			$result = $this->GateControl_model->saveGateControlDb($data);
    			//$response = array();
    			$response->result = $result;
    			$response->ASNID = $AsnID;
    			
    			echo json_encode($response);
		    }
		    else{
		        echo json_encode(null);
		    }
	}
	
	public function generateAsn($BookingID,$ASNID)
	{
			/* Load QR Code Library */
			$this->load->library('ciqrcode');
			
			/* Data */
			$hex_data   = bin2hex($ASNID);
			$save_name  = $hex_data.'.png';
			
			/* QR Code File Directory Initialize */
			$dir = 'assets/media/qrcode/';
			if (!file_exists($dir)) {
				mkdir($dir, 0775, true);
			}
			
			/* QR Configuration  */
			$config['cacheable']    = true;
			$config['imagedir']     = $dir;
			$config['quality']      = true;
			$config['size']         = '1024';
			$config['black']        = array(255,255,255);
			$config['white']        = array(255,255,255);
			$this->ciqrcode->initialize($config);
			
			/* QR Data  */
			$params['data']     = $ASNID.','.$BookingID.','."1";
			$params['level']    = 'L';
			$params['size']     = 10;
			$params['savename'] = FCPATH.$config['imagedir']. $save_name;
			
			$this->ciqrcode->generate($params);
			
			/* Return Data */
			$QR = array(
            'content' => $ASNID.','.$BookingID,
            'file'    => $dir. $save_name,
            'name'    => $save_name
			);
			
			$this->db->where('BookingID',$BookingID);
			$this->db->where('ASNID',$ASNID);
			$this->db->set('ASNQR',$QR['name']);
			$this->db->set('status',1);
			$this->db->update('tblGateMaster');
			$flag = 1;
			$this->data['AsnDetails'] = $this->GateControl_model->getSingleGateControl($BookingID,$ASNID,$flag);
			$this->data['RootCompany'] = $this->GateControl_model->getRootCompany();
			$this->load->library('asn_pdf');
			$this->load->view('asn/asn_pdf',$this->data);
			
		}
		
		public function export_alltradelist()
        {
            
            if(!class_exists('XLSXReader_fin')){
                require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
            }
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
            
            if($this->input->post()){
                
                $company_detail = $this->sale_reports_model->get_company_detail();
                $data = array(
                    'AccountID' => $this->input->post('AccountID'),
                    'from_date' => $this->input->post('from_date'),
                    'to_date'  => $this->input->post('to_date'),
                    'WHID'  => $this->input->post('w_id'),
                    'CenterID'  => $this->input->post('CenterID'),
                    'ItemID'  => $this->input->post('ItemID'),
                    'BookingType' => $this->input->post('BookingType'),
                    'IsApprove'  => $this->input->post('IsApprove'),
                 );
                 
                 $result = $this->Booking_list_model->GetAllBookingsDB($data);
          
                
                $writer = new XLSXWriter();
                
                $company_name = array($company_detail->company_name);
                $writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 13);  //merge cells
                $writer->writeSheetRow('Sheet1', $company_name);
    
                $address = $company_detail->address;
                $center_addr = array($address,);
                $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 13);  //merge cells
                $writer->writeSheetRow('Sheet1', $center_addr);
                
                
                $set_col_tk = [];
                $set_col_tk["Sr.No."] = 'Sr. No.';
                $set_col_tk["Account Name"] =  'Account Name';
                $set_col_tk["Booking Type"] = 'Booking Type';
                $set_col_tk["BookingID"] = 'BookingID';
                $set_col_tk["Booking Date"] = 'Booking Date';
                // $set_col_tk["WH Name"] = 'WH Name';
                $set_col_tk["Center Name"] =  'Center Name';
                $set_col_tk["Item Name"] = 'Item Name';
                $set_col_tk["Quantity"] = 'Quantity';
                $set_col_tk["Status"] = 'Status';
                $set_col_tk["PcSoftID"] = 'PcSoftID';
                $writer_header = $set_col_tk;
                $writer->writeSheetRow('Sheet1', $writer_header);
                $i = 1;
                foreach ($result as $k => $value) {
                    if($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] !=""){
                       $PCSoftStatus =  $value["pcsoft_doc_ref"];
                    }else if($value['IsApprove'] == 'Y' && $value['ClientApprove'] == "Y" && $value['BrokerApprove'] == "Y" && $value["GIC_Reference"] ==""){
                        $PCSoftStatus = 'Sending data Fail To PcSoft';
                    }else{
                        $PCSoftStatus = "--";
                    }
                    
                    
                    if($value["IsApprove"]=='Y'){
                        $status ='Accepted';
                    }
                    elseif($value["IsApprove"]=='N'){
                        $status ='Rejected';
                    }else{
                        $status ='No Action';
                    }
                    
                    $list_add = [];
                    $list_add[] = $i;
                    $list_add[] = $value["firstname"]." ".$value["lastname"];
                    $list_add[] = $value["TType2"];
                    $list_add[] = $value["BookingID"];
                    $list_add[] = _d(substr($value['TransDate'],0,10));
                    // $list_add[] = $value["w_name"];
                    $list_add[] = $value["CenterName"];
                    $list_add[] = $value["ItemName"];
                    $list_add[] = $value["e_quantity"];
                    $list_add[] = $status;
                    $list_add[] = $PCSoftStatus;
                    // $list_add[] = $value["IsApprove"];
        
                    $list_add[] = $row_a;
                    
                    $writer->writeSheetRow('Sheet1', $list_add);
                    $i++;
                }
        
                $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
                foreach($files as $file){
                    if(is_file($file)) {
                        unlink($file); 
                    }
                }
                $filename = 'BookingList.xlsx';
                $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
                echo json_encode([
                    'site_url'          => site_url(),
                    'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
                ]);
                die;
            }
        }
}