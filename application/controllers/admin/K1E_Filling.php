<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class K1E_Filling extends AdminController
{
   
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('K1E_Filling_Model');
        require_once module_dir_path(TIMESHEETS_MODULE_NAME) . '/third_party/excel/PHPExcel.php';
    }  
//================= GST Sale Report PAge Load ==================================
    public function k1sale_gst_report()
    {
        if (!has_permission_new('k1GSTR_sales', '', 'view')) {
            access_denied('k1GSTR_sales');
        }
        $title = _l('GST Sales Report');
        $data['title'] = $title;
        $this->load->model('misc_reports_model');
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['VendorList'] = $this->K1E_Filling_Model->get_sales_vendor_list();
        $this->load->view('admin/K1E_Filling/k1gst_sale_report', $data);
    }
//========================= K1 GSt Sale Report data fetch ======================
    public function load_table()
    {
        if (!has_permission_new('k1GSTR_sales', '', 'view')) {
            access_denied('k1GSTR_sales');
        }
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'bill_type'  => $this->input->post('bill_type'),
           'bill_wise_type'  => $this->input->post('bill_wise_type'),
           'gst_type'  => $this->input->post('gst_type'),
           'act_name' => $this->input->post('act_name'),
        );
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $accountId = $this->input->post('accountId');
        $bill_type = $this->input->post('bill_type');
        $bill_wise_type = $this->input->post('bill_wise_type');
        $gst_type = $this->input->post('gst_type');
        $account_full_name = $this->input->post('account_full_name');
        $act_name = $this->input->post('act_name');
          
        $body_data = $this->K1E_Filling_Model->get_data_for_table($filterdata);
        $GstType = $this->K1E_Filling_Model->get_GstType($filterdata);
        $GstTypeWiseValue = $this->K1E_Filling_Model->get_GstTypeWiseValue($filterdata);
        
        $gstTypeArray = array();
        foreach ($GstType as $key1 => $value1) {
            $GSTS = 0;
            $GSTS = $value1['cgst'] + $value1['sgst'] + $value1['igst'];
            array_push($gstTypeArray, $GSTS);
        }
        $GSTType_unq = array_unique($gstTypeArray);
        sort($GSTType_unq);
       
        $table_width = '100%';
        $colspan = 6;
        $html = '';
        if($filterdata['bill_wise_type'] == 2){
        // Day Wise report
            $html .= '<table class="table-striped table-bordered production_report" id="production_report" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;">';
            if($accountId != ''){
                $html .= 'Account: '.$account_full_name.',';
            }else{
                $html .= 'Account: All,';
            }
            if($bill_type == 1){
                $html .= 'Bill Type: All Bills,';
            }else if($bill_type == 2){
                $html .= 'Bill Type: GST Bills,';
            }else if($bill_type == 3){
                $html .= 'Bill Type: Non-GST Bills,';
            }
            $html .= 'Day Wise Summary,';
            $html .= 'form date:'.$from_date.', to date:'.$to_date.'</span></td>';
            $html .= '</tr>';
                
            $html .= '<tr>';
            $html .= '<td align="center" colspan="2"></td>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $html .= '<td align="center" colspan="4">GST '.sprintf('%0.2f', $value2).'%</td>';
                }
            }
            
            $html .= '<td align="center" colspan="3">Total</td>';
            $html .= '</tr>';
            
            $html .= '<tr>';
            $html .= '<th align="center">SrNo</th>';
            $html .= '<th align="center">Date</th>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $html .= '<th align="center" >Taxable '.sprintf('%0.2f', $value2).'%</th>';
                    $html .= '<th align="center" >CGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                    $html .= '<th align="center" >SGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                    $html .= '<th align="center" >IGST '.sprintf('%0.2f', $value2).'%</th>';
                }
            }
            
            $html .= '<th align="center">TaxableAmt</th>';
            $html .= '<th align="center">GSTAmt</th>';
            $html .= '<th align="center">BillAmt</th>';
            $html .= '</tr>';
                
            
            $html .= '</thead>';
            $html .= '<tbody>';
            $total_taxable_amt = 0;
            $total_gst_amt = 0;
            $total_bill_amt = 0;
            $i = 1;
            foreach ($body_data as $key => $value) {
                if($value["SaleAmt"] == 0 || $value["SaleAmt"] == 0.00){
            
                }else{
                    $html .= '<tr>';
                    $html .= '<td align="center">'.$i.'</td>';
                    $html .= '<td align="center">'.substr(_d($value["Transdate"]),0,10).'</td>';
                    
                    $DayTotalSaleAmt = 0;
                    $DayTotaGSAmt = 0;
                    //if($gst_type !== "2"){   
                        foreach ($GSTType_unq as $value2) {
                           $match1 = 0;$taxAmt = 0;$cgstAmt = 0;$sgstAmt = 0;$igstAmt = 0;
                           foreach ($GstTypeWiseValue as $key3 => $value3) {
                               $gstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                                if($gstP2 == $value2 && substr($value["Transdate"],0,10) == substr($value3["TransDate"],0,10)){
                                    $match1 = 1;
                                    $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                        			if($ExGSTAmt > 0){
                        			    $taxAmt += $value3["taxableAmt"];
                                        $cgstAmt += $value3["cgstsum"];
                                        $sgstAmt += $value3["sgstsum"];
                                        $igstAmt += $value3["igstsum"];
                                        $GSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                        			}else{
                        			    $TaxableAmt = $value3["taxableAmt"] / (1+($gstP2/100));
                        			    $taxAmt += $TaxableAmt;
                        			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                        			    if($value3['igst']>0){
                        			        $igstAmt += $GSTAmt;
                        			    }else{
                        			        $sgstAmt += $GSTAmt/2;
                        			        $cgstAmt += $GSTAmt/2;
                        			    }
                        			}
                        			$DayTotaGSAmt += $GSTAmt;
                        			$DayTotalSaleAmt += $taxAmt;
                                }
                            }
                            if($match1 == 0 && $gst_type !== "2"){
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                            }elseif($gst_type !== "2"){
                                $html .= '<td align="center" >'.number_format($taxAmt,2).' </td>';
                                $html .= '<td align="center" >'.number_format($cgstAmt,2).' </td>';
                                $html .= '<td align="center" >'.number_format($sgstAmt,2).' </td>';
                                $html .= '<td align="center" >'.number_format($igstAmt,2).' </td>';
                            }  
                        }
                    //}
                    $total_taxable_amt +=$DayTotalSaleAmt;
                    $html .= '<td align="right"><b>'.number_format($DayTotalSaleAmt,2).'</b></td>';
                    $total_gst_amt +=$DayTotaGSAmt;
                   
                    $html .= '<td align="right"><b>'.number_format($DayTotaGSAmt,2).'</b></td>';
                    $bill_amt = $DayTotalSaleAmt+$DayTotaGSAmt;
                    $total_bill_amt +=$bill_amt;
                    $html .= '<td align="right"><b>'.number_format($bill_amt,2).'</b></td>';
                    $html .= '</tr>'; 
                    $i++;
                }
            }
            $html .= '</tbody>';
               
            $html .= '<tfoot>';
            $html .= '<tr>';
              
            $html .= '<td ></td>';
            $html .= '<td ><span style="color:#e93232;font-weight:700;text-align:right;">Total</span></td>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt = 0;
                    $fcgstAmt = 0;
                    $fsgstAmt = 0;
                    $figstAmt = 0;
                    foreach ($GstTypeWiseValue as $key3 => $value3) {
                        $fgstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                        if($fgstP2 == $value2){
                            $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                			if($ExGSTAmt > 0){
                			    $ftaxAmt += $value3["taxableAmt"];
                                $fcgstAmt += $value3["cgstsum"];
                                $fsgstAmt += $value3["sgstsum"];
                                $figstAmt += $value3["igstsum"];
                                $GSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                			}else{
                			    $TaxableAmt = $value3["taxableAmt"] / (1+($fgstP2/100));
                			    $ftaxAmt += $TaxableAmt;
                			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                			    if($value3['igst']>0){
                			        $figstAmt += $GSTAmt;
                			    }else{
                			        $fsgstAmt += $GSTAmt/2;
                			        $fcgstAmt += $GSTAmt/2;
                			    }
                			}
                        }
                    }
                    $html .= '<td align="center" ><b>'.number_format($ftaxAmt,2).' </b></td>';
                    $html .= '<td align="center" ><b>'.number_format($fcgstAmt,2).' </b></td>';
                    $html .= '<td align="center" >'.number_format($fsgstAmt,2).' </b></td>';
                    $html .= '<td align="center" ><b>'.number_format($figstAmt,2).' </b></td>';
                }
            }
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_taxable_amt,2).'</span></td>';
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_gst_amt,2).'</span></td>';
            
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_bill_amt,2).'</span></td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        }else{
        // Bill Wise report
            
            $html .= '<table class="table-striped table-bordered production_report" id="production_report" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
            $html .= '<tr style="display:none;" >';
            $html .= '<td colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;">';
            if($accountId != ''){
                $html .= 'Account: '.$account_full_name.',';
            }else{
                $html .= 'Account: All,';
            }
            if($bill_type == 1){
                $html .= 'Bill Type: All Bills,';
            }else if($bill_type == 2){
                $html .= 'Bill Type: GST Bills,';
            }else if($bill_type == 3){
                $html .= 'Bill Type: Non-GST Bills,';
            }
            $html .= 'Bill Wise Summary,';
            $html .= 'form date:'.$from_date.', to date:'.$to_date.'</span></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td align="center" colspan="3"></td>';
            $html .= '<td align="center" colspan="2">Account Details</td>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $html .= '<td align="center" colspan="4">GST '.sprintf('%0.2f', $value2).'%</td>';
                }
            }
            $html .= '<td align="center" colspan="3">Total</td>';
            $html .= '</tr>';
        
            $html .= '<tr>';
            $html .= '<th align="center">SrNo</th>';
            $html .= '<th align="center">BillNo</th>';
            $html .= '<th align="center">Date</th>';
            $html .= '<th align="center">Account Name</th>';
            $html .= '<th align="center">GSTIN</th>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $html .= '<th align="center">Taxable '.sprintf('%0.2f', $value2).'%</th>';
                    $html .= '<th align="center" >CGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                    $html .= '<th align="center" >SGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                    $html .= '<th align="center" >IGST '.sprintf('%0.2f', $value2).'%</th>';
                }
            }
            $html .= '<th align="center">TaxableAmt</th>';
            $html .= '<th align="center">GSTAmt</th>';
            $html .= '<th align="center">BillAmt</th>';
            $html .= '</tr>';
                
            
            $html .= '</thead>';
            $html .= '<tbody>';
            $total_taxable_amt = 0;
            $total_gst_amt = 0;
            $total_bill_amt = 0;
            $i = 1;
            
            foreach ($body_data as $key => $value) {
                if(($value["SaleAmt"] == 0) || $value["SaleAmt"] == 0.00){
            
                }else{
                    $html .= '<tr>';
                    $html .= '<td align="center">'.$i.'</td>';
                    $html .= '<td align="center">'.$value["SalesID"].'</td>';
                    $html .= '<td align="center">'.substr(_d($value["Transdate"]),0,10).'</td>';
                    $html .= '<td align="left">'.$value["company"].'</td>';
                    $html .= '<td align="center">'.$value["gstno"].'</td>';
                    $BillTotalSaleAmt = 0;
                    $BillTotalGSAmt = 0;
                    /*if($gst_type !== "2"){  */ 
                        foreach ($GSTType_unq as $value2) {
                            $match = 0;
                            foreach ($GstTypeWiseValue as $key3 => $value3) {
                                $gstP = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                                if($gstP == $value2 && $value["SalesID"] == $value3["TransID"]){
                                    $match = 1;$taxAmt = 0;$cgstAmt = 0;$sgstAmt = 0;$igstAmt = 0;$GSTAmt = 0;
                                    $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                        			if($ExGSTAmt > 0){
                        			    $taxAmt = $value3["taxableAmt"];
                                        $cgstAmt = $value3["cgstsum"];
                                        $sgstAmt = $value3["sgstsum"];
                                        $igstAmt = $value3["igstsum"];
                                        $GSTAmt = $ExGSTAmt;
                        			}else{
                        			    $TaxableAmt = $value3["taxableAmt"] / (1+($gstP/100));
                        			    $taxAmt = $TaxableAmt;
                        			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                        			    if($value3['igst']>0){
                        			        $igstAmt = $GSTAmt;
                        			    }else{
                        			        $sgstAmt = $GSTAmt/2;
                        			        $cgstAmt = $GSTAmt/2;
                        			    }
                        			}
                        			$BillTotalGSAmt += $GSTAmt;
                        			$BillTotalSaleAmt += $taxAmt;
                        			
                        			if($gst_type !== "2"){  
                        			    $html .= '<td align="center" >'.number_format($taxAmt,2).' </td>';
                                        $html .= '<td align="center" >'.number_format($cgstAmt,2).' </td>';
                                        $html .= '<td align="center" >'.number_format($sgstAmt,2).' </td>';
                                        $html .= '<td align="center" >'.number_format($igstAmt,2).' </td>';
                        			}
                                }
                            }
                            if($match == 0 && $gst_type !== "2"){
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                                $html .= '<td align="center" > </td>';
                            }  
                        }
                    //}
            
                    $html .= '<td align="right"><b>'.number_format($BillTotalSaleAmt,2).'</b></td>';
                    $total_taxable_amt += $BillTotalSaleAmt;
                    $total_gst_amt +=$BillTotalGSAmt;
                    $html .= '<td align="right"><b>'.number_format($BillTotalGSAmt,2).'</b></td>';
                    $bill_amt = $BillTotalSaleAmt+$BillTotalGSAmt;
                    $total_bill_amt +=$bill_amt;
                    $html .= '<td align="right"><b>'.number_format($bill_amt,2).'</b></td>';
                    $html .= '</tr>'; 
                    $i++;
                }
            }
            $html .= '</tbody>';
            $html .= '<tfoot>';
            $html .= '<tr>';
            $html .= '<td ></td>';
            $html .= '<td ><span style="color:#e93232;font-weight:700;text-align:right;">Total</span></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt2 = 0;
                    $fcgstAmt2 = 0;
                    $fsgstAmt2 = 0;
                    $figstAmt2 = 0;
                    foreach ($GstTypeWiseValue as $keyf => $valuef) {
                        $gstPF = $valuef['igst'] + $valuef['cgst'] + $valuef['sgst'];
                        if($gstPF == $value2 ){
                            $ExGSTAmt = $valuef['sgstsum'] + $valuef['cgstsum'] + $valuef['igstsum'];
                			if($ExGSTAmt > 0){
                			    $ftaxAmt2 += $valuef['taxableAmt'];
                                $fcgstAmt2 += $valuef['cgstsum'];
                                $fsgstAmt2 += $valuef['sgstsum'];
                                $figstAmt2 += $valuef['igstsum'];
                                $GSTAmt = $ExGSTAmt;
                			}else{
                			    $TaxableAmt = $valuef["taxableAmt"] / (1+($gstPF/100));
                			    $ftaxAmt2 += $TaxableAmt;
                			    $GSTAmt = $valuef["taxableAmt"] - $TaxableAmt;
                			    if($valuef['igst']>0){
                			        $figstAmt2 += $GSTAmt;
                			    }else{
                			        $fsgstAmt2 += $GSTAmt/2;
                			        $fcgstAmt2 += $GSTAmt/2;
                			    }
                			}
                                
                        }
                    }
                    $html .= '<td align="center" ><b>'.number_format($ftaxAmt2,2).'</b> </td>';
                    $html .= '<td align="center" ><b>'.number_format($fcgstAmt2,2).' </b></td>';
                    $html .= '<td align="center" ><b>'.number_format($fsgstAmt2,2).' </b></td>';
                    $html .= '<td align="center" ><b>'.number_format($figstAmt2,2).' </b></td>';
                    
                }
            }
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_taxable_amt,2).'</span></td>';
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_gst_amt,2).'</span></td>';
            
            $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_bill_amt,2).'</span></td>';
            $html .= '</tr>';
            $html .= '</tfoot>';
            $html .= '</table>';
        }
        echo json_encode($html);
        die;
    }
//======================== K1 GSt Sale Report data Export ======================
    public function export_gst_sale_report()
    {
        if (!has_permission_new('k1GSTR_sales', '', 'export')) {
            access_denied('k1GSTR_sales');
        }
    	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
            $filterdata = array(
                'from_date' => $this->input->post('from_date'),
                'to_date'  => $this->input->post('to_date'),
                'accountId'  => $this->input->post('accountId'),
                'bill_type'  => $this->input->post('bill_type'),
                'bill_wise_type'  => $this->input->post('bill_wise_type'),
                'gst_type'  => $this->input->post('gst_type')
            );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $accountId = $this->input->post('accountId');
            $bill_type = $this->input->post('bill_type');
            $bill_wise_type = $this->input->post('bill_wise_type');
            $gst_type = $this->input->post('gst_type');
            $account_full_name = $this->input->post('account_full_name');
            
            $body_data = $this->K1E_Filling_Model->get_data_for_table($filterdata);
            $GstType = $this->K1E_Filling_Model->get_GstType($filterdata);
            $GstTypeWiseValue = $this->K1E_Filling_Model->get_GstTypeWiseValue($filterdata);
        
            $gstTypeArray = array();
            foreach ($GstType as $key1 => $value1) {
                $GSTS = 0;
                $GSTS = $value1['cgst'] + $value1['sgst'] + $value1['igst'];
                array_push($gstTypeArray, $GSTS);
            }
            $GSTType_unq = array_unique($gstTypeArray);
            sort($GSTType_unq);
        
            $this->load->model('misc_reports_model');
        	$selected_company_details = $this->misc_reports_model->get_company_detail();
            if($gst_type !== "2"){
                if($bill_wise_type == 2){
            	    // Day Wise
            	    $default = 4;
            	    $otherColmn = count($GSTType_unq) * 4;
            	    $totColmn = $default + $otherColmn - 1;
            	}else{
            	    // Bill Wise
            	    $default = 7;
            	    $otherColmn = count($GSTType_unq) * 4;
            	    $totColmn = $default + $otherColmn - 1;
            	}	
            }else{
                 if($bill_wise_type == 2){
            	    // Day Wise
            	    $default = 4;$totColmn = $default - 1;
            	}else{
            	    // Bill Wise
            	    $default = 7;$totColmn = $default - 1;
            	}
            }	
    	
            $writer = new XLSXWriter();
        	$border = array( 'border'=>'left,right,top,bottom');
    	    $border_style = array( 'border-style'=>'solid');
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = $totColmn);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    		
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = $totColmn);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		
    		$html = 'Sales (';
    		if($accountId != ''){
                $html .= 'Accounts: '.$account_full_name.',';
            }else{
                $html .= 'Accounts: All';
            }
                
            $html .= ') form '.$from_date.' To '.$to_date.'';
            $filter = array($html);
            $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = $totColmn);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    		
    	    if($bill_wise_type == 2){
    	    // Day wise report
                $set_col_tk = [];
    		    $set_col_tk["Date"] =  'Date';
    		    if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $set_col_tk["Taxable'".$value2."'"] =  'Taxable'.sprintf('%0.2f', $value2).'%';
                        $set_col_tk["CGST'".($value2 / 2)."'"] =  'CGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                        $set_col_tk["SGST'".($value2 / 2)."'"] =  'SGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                        $set_col_tk["IGST'".$value2."'"] =  'IGST'.sprintf('%0.2f', $value2);
                    }
                }
        		$set_col_tk["TaxableAmt"] =  'TaxableAmt';
        		$set_col_tk["GSTAmt"] =  'GSTAmt';
        		$set_col_tk["BillAmt"] =  'BillAmt';
        		$writer_header = $set_col_tk;
        		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	        $total_taxable_amt = 0;
                $total_gst_amt = 0;
                $total_bill_amt = 0;
                
                foreach ($body_data as $key => $value) {
                    if($value["SaleAmt"] == 0 || $value["SaleAmt"] == 0.00){
                
                    }else{
                        $list_add = [];
                   	    $list_add[] = substr(_d($value["Transdate"]),0,10);
                   	    $DayTotalSaleAmt = 0;
                        $DayTotaGSAmt = 0;
                        //if($gst_type !== "2"){   
                            foreach ($GSTType_unq as $value2) {
                                $match1 = 0;$taxAmt = 0;$cgstAmt = 0;$sgstAmt = 0;$igstAmt = 0;
                                foreach ($GstTypeWiseValue as $key3 => $value3) {
                                    $gstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                                    if($gstP2 == $value2 && substr($value["Transdate"],0,10) == substr($value3["TransDate"],0,10)){
                                        $match1 = 1;
                                        $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                            			if($ExGSTAmt > 0){
                            			    $taxAmt += $value3["taxableAmt"];
                                            $cgstAmt += $value3["cgstsum"];
                                            $sgstAmt += $value3["sgstsum"];
                                            $igstAmt += $value3["igstsum"];
                                            $GSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                            			}else{
                            			    $TaxableAmt = $value3["taxableAmt"] / (1+($gstP2/100));
                            			    $taxAmt += $TaxableAmt;
                            			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                            			    if($value3['igst']>0){
                            			        $igstAmt += $GSTAmt;
                            			    }else{
                            			        $sgstAmt += $GSTAmt/2;
                            			        $cgstAmt += $GSTAmt/2;
                            			    }
                            			}
                            			$DayTotaGSAmt += $GSTAmt;
                            			$DayTotalSaleAmt += $taxAmt;
                                    }
                                }
                                if($match1 == 0 && $gst_type !== "2"){
                                    $list_add[] = "";
                                    $list_add[] = "";
                                    $list_add[] = "";
                                    $list_add[] = "";
                                }elseif($gst_type !== "2"){
                                    $list_add[] = sprintf('%0.2f', $taxAmt);
                                    $list_add[] = sprintf('%0.2f', $cgstAmt);
                                    $list_add[] = sprintf('%0.2f', $sgstAmt);
                                    $list_add[] = sprintf('%0.2f', $igstAmt);
                                }  
                            }
                        //}
                        $total_taxable_amt +=$DayTotalSaleAmt;
                   	    $list_add[] = sprintf('%0.2f', $DayTotalSaleAmt);
                   	    $total_gst_amt +=$DayTotaGSAmt;
                   	    $list_add[] = sprintf('%0.2f', $DayTotaGSAmt);
                   	    $bill_amt = $DayTotalSaleAmt+$DayTotaGSAmt;
                        $total_bill_amt +=$bill_amt;
                   	    $list_add[] = sprintf('%0.2f', $bill_amt);
                        $writer->writeSheetRow('Sheet1', $list_add);
                    }
                }
                $list_add = [];
                $list_add[] = "Total";
                if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $ftaxAmt = 0;$fcgstAmt = 0;$fsgstAmt = 0;$figstAmt = 0;
                        foreach ($GstTypeWiseValue as $key3 => $value3) {
                            $fgstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                            if($fgstP2 == $value2){
                                $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                    			if($ExGSTAmt > 0){
                    			    $ftaxAmt += $value3["taxableAmt"];
                                    $fcgstAmt += $value3["cgstsum"];
                                    $fsgstAmt += $value3["sgstsum"];
                                    $figstAmt += $value3["igstsum"];
                                    $GSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                    			}else{
                    			    $TaxableAmt = $value3["taxableAmt"] / (1+($fgstP2/100));
                    			    $ftaxAmt += $TaxableAmt;
                    			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                    			    if($value3['igst']>0){
                    			        $figstAmt += $GSTAmt;
                    			    }else{
                    			        $fsgstAmt += $GSTAmt/2;
                    			        $fcgstAmt += $GSTAmt/2;
                    			    }
                    			}
                            }
                        }
                        $list_add[] = $ftaxAmt;
                        $list_add[] = $fcgstAmt;
                        $list_add[] = $fsgstAmt;
                        $list_add[] = $figstAmt;
                    }
                }
                $list_add[] = $total_taxable_amt;
                $list_add[] = $total_gst_amt;
                $list_add[] = $total_bill_amt;
                $writer->writeSheetRow('Sheet1', $list_add);
    	    }else{
    	        // Bill Wise export
    	        $list_add = [];
    	        $list_add[] = " ";
    	        $list_add[] = "";
        	    if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $list_add[] = "";
                    }
        	    }
        	    $list_add[] = "";
        	    $writer->writeSheetRow('Sheet1', $list_add);
        	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 1);  //merge cells
        	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 2, $end_row = 3, $end_col = 3);  //merge cells
        	    if($gst_type !== "2"){
        	        $i = 4;$j = 7;
                    foreach ($GSTType_unq as $value2) {
                        $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = $i, $end_row = 3, $end_col = $j);  //merge cells
                        $i += 4;$j += 4;
                    }
        	    }
        	    $lastThree = $totColmn -2;
        	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = $lastThree, $end_row = 3, $end_col = $totColmn);  //merge cells
        	    
                $set_col_tk = [];
        		$set_col_tk["BillNo"] =  'BillNo';
        		$set_col_tk["Date"] =  'Date';
        		$set_col_tk["Account Name"] =  'Account Name';
        		$set_col_tk["GSTIN"] =  'GSTIN';
        		if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $set_col_tk["Taxable'".$value2."'"] =  'Taxable'.sprintf('%0.2f', $value2).'%';
                        $set_col_tk["CGST'".($value2 / 2)."'"] =  'CGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                        $set_col_tk["SGST'".($value2 / 2)."'"] =  'SGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                        $set_col_tk["IGST'".$value2."'"] =  'IGST'.sprintf('%0.2f', $value2);
                    }
                }
        		$set_col_tk["TaxableAmt"] =  'TaxableAmt';
        		$set_col_tk["GSTAmt"] =  'GSTAmt';
        		$set_col_tk["BillAmt"] =  'BillAmt';
               
        		$writer_header = $set_col_tk;
        		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	        $total_taxable_amt = 0;$total_gst_amt = 0;$total_bill_amt = 0;
                foreach ($body_data as $key => $value) {
                    if($value["SaleAmt"] == 0 || $value["SaleAmt"] == 0.00){
                        
                    }else{
                        $list_add = [];
                   	    $list_add[] = $value["SalesID"];
                        $list_add[] = substr(_d($value["Transdate"]),0,10);
                        $list_add[] = $value["company"];
                        $list_add[] = $value["gstno"];
                   	    $BillTotalSaleAmt = 0;
                        $BillTotalGSAmt = 0;
                   	    //if($gst_type !== "2"){   
                            foreach ($GSTType_unq as $value2) {
                                $match = 0;
                                foreach ($GstTypeWiseValue as $key3 => $value3) {
                                    $gstP = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                                    if($gstP == $value2 && $value["SalesID"] == $value3["TransID"]){
                                        $match = 1;$taxAmt = 0;$cgstAmt = 0;$sgstAmt = 0;$igstAmt = 0;$GSTAmt = 0;
                                        $ExGSTAmt = $value3['sgstsum'] + $value3['cgstsum'] + $value3['igstsum'];
                            			if($ExGSTAmt > 0){
                            			    $taxAmt = $value3["taxableAmt"];
                                            $cgstAmt = $value3["cgstsum"];
                                            $sgstAmt = $value3["sgstsum"];
                                            $igstAmt = $value3["igstsum"];
                                            $GSTAmt = $ExGSTAmt;
                            			}else{
                            			    $TaxableAmt = $value3["taxableAmt"] / (1+($gstP/100));
                            			    $taxAmt = $TaxableAmt;
                            			    $GSTAmt = $value3["taxableAmt"] - $TaxableAmt;
                            			    if($value3['igst']>0){
                            			        $igstAmt = $GSTAmt;
                            			    }else{
                            			        $sgstAmt = $GSTAmt/2;
                            			        $cgstAmt = $GSTAmt/2;
                            			    }
                            			}
                            			$BillTotalGSAmt += $GSTAmt;
                            			$BillTotalSaleAmt += $taxAmt;
                            			if($gst_type !== "2"){ 
                            			    $list_add[] = sprintf('%0.2f', $taxAmt);
                                            $list_add[] = sprintf('%0.2f', $cgstAmt);
                                            $list_add[] = sprintf('%0.2f', $sgstAmt);
                                            $list_add[] = sprintf('%0.2f', $igstAmt);
                            			}
                                    }
                                }
                                if($match == "0" && $gst_type !== "2"){
                                    $list_add[] = '';
                                    $list_add[] = '';
                                    $list_add[] = '';
                                    $list_add[] = '';
                                }  
                            }
                        //}                    
                        $total_taxable_amt += $BillTotalSaleAmt;
                        $total_gst_amt +=$BillTotalGSAmt;
                       	$list_add[] = sprintf('%0.2f', $BillTotalSaleAmt);
                       	$list_add[] = sprintf('%0.2f', $BillTotalGSAmt);
                       	$bill_amt = $BillTotalSaleAmt + $BillTotalGSAmt;
                        $total_bill_amt +=$bill_amt;
                       	$list_add[] = sprintf('%0.2f', $bill_amt);
                        $writer->writeSheetRow('Sheet1', $list_add);
                    }
                }
              
                $list_add = [];
                $list_add[] = "Total";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
                if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $ftaxAmt2 = 0;$fcgstAmt2 = 0;$fsgstAmt2 = 0;$figstAmt2 = 0;
                        foreach ($GstTypeWiseValue as $keyf => $valuef) {
                            $gstPF = $valuef['igst'] + $valuef['cgst'] + $valuef['sgst'];
                            if($gstPF == $value2 ){
                                $ExGSTAmt = $valuef['sgstsum'] + $valuef['cgstsum'] + $valuef['igstsum'];
                    			if($ExGSTAmt > 0){
                    			    $ftaxAmt2 += $valuef['taxableAmt'];
                                    $fcgstAmt2 += $valuef['cgstsum'];
                                    $fsgstAmt2 += $valuef['sgstsum'];
                                    $figstAmt2 += $valuef['igstsum'];
                                    $GSTAmt = $ExGSTAmt;
                    			}else{
                    			    $TaxableAmt = $valuef["taxableAmt"] / (1+($gstPF/100));
                    			    $ftaxAmt2 += $TaxableAmt;
                    			    $GSTAmt = $valuef["taxableAmt"] - $TaxableAmt;
                    			    if($valuef['igst']>0){
                    			        $figstAmt2 += $GSTAmt;
                    			    }else{
                    			        $fsgstAmt2 += $GSTAmt/2;
                    			        $fcgstAmt2 += $GSTAmt/2;
                    			    }
                    			}
                            }
                        }
                        $list_add[] = sprintf('%0.2f', $ftaxAmt2);
                        $list_add[] = sprintf('%0.2f', $fcgstAmt2);
                        $list_add[] = sprintf('%0.2f', $fsgstAmt2);
                        $list_add[] = sprintf('%0.2f', $figstAmt2);
                    }
                }
                $list_add[] = sprintf('%0.2f', $total_taxable_amt);
                $list_add[] = sprintf('%0.2f', $total_gst_amt);
                $list_add[] = sprintf('%0.2f', $total_bill_amt);
                  
                $writer->writeSheetRow('Sheet1', $list_add);
    	    }
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'K1 GST Sale Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    
//===================== GSTR Purchase Page Load ================================ 
    public function k1purchase_gst_report()
    { 
        if (!has_permission_new('k1GSTR_purchase', '', 'view')) {
            access_denied('k1GSTR_purchase');
        }
        $title = _l('GST Purchase Report');
        $data['title'] = $title;
        $this->load->model('misc_reports_model');
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $data['VendorList'] = $this->K1E_Filling_Model->get_vendor_list();
        $this->load->view('admin/K1E_Filling/k1gst_purchase_report',$data);
    }
//======================= GSTR Purchase data load ==============================
    public function purchase_gst_table()
    {
        if (!has_permission_new('k1GSTR_purchase', '', 'view')) {
            access_denied('k1GSTR_purchase');
        }
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'bill_type'  => $this->input->post('bill_type'),
           'bill_wise_type'  => $this->input->post('bill_wise_type'),
           'gst_type'  => $this->input->post('gst_type')
        );
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $accountId = $this->input->post('accountId');
        $bill_type = $this->input->post('bill_type');
        $bill_wise_type = $this->input->post('bill_wise_type');
        $gst_type = $this->input->post('gst_type');
        $account_full_name = $this->input->post('account_full_name');
          
        $body_data = $this->K1E_Filling_Model->get_purchase_data_for_table($filterdata);
        $GstType = $this->K1E_Filling_Model->get_GstTypeP($filterdata); 
        $GstTypeWiseValue = $this->K1E_Filling_Model->get_GstTypeWiseValueP($filterdata);
        $gstTypeArray = array();
        foreach ($GstType as $key1 => $value1) {
            $GSTS = 0;
            $GSTS = $value1['cgst'] + $value1['sgst'] + $value1['igst'];
            array_push($gstTypeArray, $GSTS);
        }
        $GSTType_unq = array_unique($gstTypeArray);
        sort($GSTType_unq);
        
        $table_width = '100%';
        $colspan = 6;
        $html = '';
        if($filterdata['bill_wise_type'] == 2)
        {
            // Day Wise report
            $html .= '<table class="table-striped table-bordered gstr_purchase_report" id="gstr_purchase_report" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
           
            $html .= '<tr style="display:none;" >';
                
            $html .= '<th colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;">';
                 if($accountId != ''){
                 $html .= 'Account: '.$account_full_name.',';
                 }else{
                      $html .= 'Account: All';
                 }
                if($bill_type == 1){
                 $html .= 'Bill Type: All Bills,';
                 }else if($bill_type == 2){
                  $html .= 'Bill Type: GST Bills,';
                 }else if($bill_type == 3){
                  $html .= 'Bill Type: Non-GST Bills,';
                 }
                 $html .= 'Day Wise Summary,';
                 $html .= 'form date:'.$from_date.', to date:'.$to_date.'</span></th>';
                $html .= '</tr>';
                
                $html .= '<tr>';
                $html .= '<th align="center" colspan="2"></th>';
                if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $html .= '<th align="center" colspan="4">GST '.sprintf('%0.2f', $value2).'%</th>';
                    }
                }
                //$html .= '<th align="center" colspan="2">Account Details</th>';
                $html .= '<th align="center" colspan="3">Total</th>';
                $html .= '</tr>';
            
                $html .= '<tr>';
                $html .= '<th align="center">S.no</th>';
                $html .= '<th align="center">Date</th>';
                if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $html .= '<th align="center" >Taxable '.sprintf('%0.2f', $value2).'%</th>';
                        $html .= '<th align="center" >CGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                        $html .= '<th align="center" >SGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                        $html .= '<th align="center" >IGST '.sprintf('%0.2f', $value2).'%</th>';
                    }
                }
                $html .= '<th align="center">TaxableAmt</th>';
                $html .= '<th align="center">GSTAmt</th>';
                $html .= '<th align="center">BillAmt</th>';
                $html .= '</tr>';
                
            
               $html .= '</thead>';
               $html .= '<tbody>';
               $total_taxable_amt = 0;
               $total_gst_amt = 0;
               $total_bill_amt = 0;
               $i = 1;
               foreach ($body_data as $key => $value) {
                   /*if(($value["sgstamt"] == 0) && ($value["cgstamt"] == 0) && ($value["igstamt"] == 0)){*/
                    if($value["Purchamt"] == 0 || $value["Purchamt"] == 0.00){
                
                    }else{
                  $html .= '<tr>';
                   $html .= '<td align="center">'.$i.'</td>';
                   $html .= '<td align="center">'.substr(_d($value["Transdate"]),0,10).'</td>';
                   $total_taxable_amt +=$value["Purchamt"];
                   
                   if($gst_type !== "2"){   
                   foreach ($GSTType_unq as $value2) {
                       $match1 = 0;
                       $taxAmt = 0;
                       $cgstAmt = 0;
                       $sgstAmt = 0;
                       $igstAmt = 0;
                       foreach ($GstTypeWiseValue as $key3 => $value3) {
                           
                           $gstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                           
                           if($gstP2 == $value2 && substr($value["Transdate"],0,10) == substr($value3["TransDate"],0,10)){
                               $match1 = 1;
                               $taxAmt += $value3["taxableAmt"];
                               $cgstAmt += $value3["cgstsum"];
                               $sgstAmt += $value3["sgstsum"];
                               $igstAmt += $value3["igstsum"];
                                
                           }
                        }
                        if($match1 == 0){
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                        }else{
                            $html .= '<td align="center" >'.number_format($taxAmt,2).' </td>';
                            $html .= '<td align="center" >'.number_format($cgstAmt,2).' </td>';
                            $html .= '<td align="center" >'.number_format($sgstAmt,2).' </td>';
                            $html .= '<td align="center" >'.number_format($igstAmt,2).' </td>';
                        }  
                    }
                }
                
                   $html .= '<td align="right">'.number_format($value["Purchamt"],2).'</td>';
                   $gst = $value["sgstamt"]+$value["cgstamt"]+$value["igstamt"];
                   $total_gst_amt +=$gst;
                   $html .= '<td align="right">'.number_format($gst,2).'</td>';
                   $bill_amt = $value["Purchamt"]+$gst;
                   $total_bill_amt +=$value["Invamt"];
                   $html .= '<td align="right">'.number_format($value["Invamt"],2).'</td>';
                   $html .= '</tr>'; 
                   $i++;
                   }
                   
               }
               $html .= '</tbody>';
               
               $html .= '<tfoot>';
               $html .= '<tr>';
              
             $html .= '<td ></td>';
                $html .= '<td ><span style="color:#e93232;font-weight:700;text-align:right;">Total</span></td>';
                if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt = 0;
                    $fcgstAmt = 0;
                    $fsgstAmt = 0;
                    $figstAmt = 0;
                    foreach ($GstTypeWiseValue as $key3 => $value3) {
                        $fgstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                        if($fgstP2 == $value2){
                            $ftaxAmt += $value3["taxableAmt"];
                            $fcgstAmt += $value3["cgstsum"];
                            $fsgstAmt += $value3["sgstsum"];
                            $figstAmt += $value3["igstsum"];
                        }
                    }
                    $html .= '<td align="center" >'.number_format($ftaxAmt,2).' </td>';
                    $html .= '<td align="center" >'.number_format($fcgstAmt,2).' </td>';
                    $html .= '<td align="center" >'.number_format($fsgstAmt,2).' </td>';
                    $html .= '<td align="center" >'.number_format($figstAmt,2).' </td>';
                }
                }
               
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_taxable_amt,2).'</span></td>';
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_gst_amt,2).'</span></td>';
              
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_bill_amt,2).'</span></td>';
               $html .= '</tr>';
               $html .= '</tfoot>';
         
            $html .= '</table>';
        }else{
            // Bill Wise report
            $html .= '<table class="table-striped table-bordered gstr_purchase_report" id="gstr_purchase_report" width="'.$table_width.'">';
            $html .= '<thead style="font-size:11px;">';
           
            $html .= '<tr style="display:none;" >';
            $html .= '<th colspan="'.$colspan.'" style="font-size:16px;font-weight:600;" align="center"><span class="report_for" style="font-size:10px;">';
                 if($accountId != ''){
                 $html .= 'Account: '.$account_full_name.',';
                 }else{
                      $html .= 'Accounts: All,';
                 }
                if($bill_type == 1){
                 $html .= 'Bill Type: All Bills,';
                 }else if($bill_type == 2){
                  $html .= 'Bill Type: GST Bills,';
                 }else if($bill_type == 3){
                  $html .= 'Bill Type: Non-GST Bills,';
                 }
                 $html .= 'Bill Wise Summary,';
                 $html .= 'form date:'.$from_date.', to date:'.$to_date.'</span></th>';
                $html .= '</tr>';
                
                $html .= '<tr>';
                $html .= '<th align="center" colspan="3"></th>';
                $html .= '<th align="center" colspan="2">Account Details</th>';
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $html .= '<th align="center" colspan="4">GST '.sprintf('%0.2f', $value2).'%</th>';
                }
            }
                $html .= '<th align="center" colspan="3">Total</th>';
                $html .= '</tr>';
                
            
                $html .= '<tr>';
                $html .= '<th align="center">S.no</th>';
                $html .= '<th align="center">BillNo</th>';
                $html .= '<th align="center">Date</th>';
                $html .= '<th align="center">Account Name</th>';
                $html .= '<th align="center">GSTIN</th>';
                if($gst_type !== "2"){
                    foreach ($GSTType_unq as $value2) {
                        $html .= '<th align="center">Taxable '.sprintf('%0.2f', $value2).'%</th>';
                        $html .= '<th align="center" >CGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                        $html .= '<th align="center" >SGST '.sprintf('%0.2f', ($value2 / 2)).'%</th>';
                        $html .= '<th align="center" >IGST '.sprintf('%0.2f', $value2).'%</th>';
                    }
                }
                $html .= '<th align="center">TaxableAmt</th>';
                $html .= '<th align="center">GSTAmt</th>';
                $html .= '<th align="center">BillAmt</th>';
                $html .= '</tr>';
            
               $html .= '</thead>';
               $html .= '<tbody>';
               $total_taxable_amt = 0;
               $total_gst_amt = 0;
               $total_bill_amt = 0;
               $i = 1;
            
               foreach ($body_data as $key => $value) {
                   
                   /*if(($value["sgstamt"] == 0) && ($value["cgstamt"] == 0) && ($value["igstamt"] == 0)){*/
                    if($value["Purchamt"] == 0 || $value["Purchamt"] == 0.00){
                   }else{
                   $html .= '<tr>';
                   $html .= '<td align="center">'.$i.'</td>';
                   $html .= '<td align="center">'.$value["PurchID"].'</td>';
                   $html .= '<td align="center">'.substr(_d($value["Transdate"]),0,10).'</td>';
                   $html .= '<td align="left">'.$value["company"].'</td>';
                   $html .= '<td align="center">'.$value["GSTIN"].'</td>';
                   if($gst_type !== "2"){   
                   foreach ($GSTType_unq as $value2) {
                       $match = 0;
                       foreach ($GstTypeWiseValue as $key3 => $value3) {
                           $gstP = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                           if($gstP == $value2 && $value["PurchID"] == $value3["OrderID"]){
                               $match = 1;
                                $html .= '<td align="center" >'.number_format($value3["taxableAmt"],2).' </td>';
                                $html .= '<td align="center" >'.number_format($value3["cgstsum"],2).' </td>';
                                $html .= '<td align="center" >'.number_format($value3["sgstsum"],2).' </td>';
                                $html .= '<td align="center" >'.number_format($value3["igstsum"],2).' </td>';
                           }
                        }
                        if($match == 0){
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                            $html .= '<td align="center" > </td>';
                        }  
                    }
                }
                   $total_taxable_amt +=$value["Purchamt"];
                   $html .= '<td align="right">'.number_format($value["Purchamt"],2).'</td>';
                   $gst = $value["sgstamt"]+$value["cgstamt"]+$value["igstamt"];
                   $total_gst_amt +=$gst;
                   $html .= '<td align="right">'.number_format($gst,2).'</td>';
                   $bill_amt = $value["Purchamt"]+$gst;
                   $total_bill_amt +=$value["Invamt"];
                   $html .= '<td align="right">'.number_format($value["Invamt"],2).'</td>';
                   $html .= '</tr>'; 
                   $i++;
                   }
                   
               }
               $html .= '</tbody>';
               
               $html .= '<tfoot>';
               $html .= '<tr>';
              
             $html .= '<td ></td>';
                $html .= '<td ><span style="color:#e93232;font-weight:700;text-align:right;">Total</span></td>';
               $html .= '<td></td>';
               $html .= '<td></td>';
               $html .= '<td></td>';
               if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt2 = 0;
                    $fcgstAmt2 = 0;
                    $fsgstAmt2 = 0;
                    $figstAmt2 = 0;
                    foreach ($GstTypeWiseValue as $keyf => $valuef) {
                           $gstPF = $valuef['igst'] + $valuef['cgst'] + $valuef['sgst'];
                           if($gstPF == $value2 ){
                                $ftaxAmt2 += $valuef['taxableAmt'];
                                $fcgstAmt2 += $valuef['cgstsum'];
                                $fsgstAmt2 += $valuef['sgstsum'];
                                $figstAmt2 += $valuef['igstsum'];
                           }
                    }
                    $html .= '<td align="center" >'.number_format($ftaxAmt2,2).' </td>';
                    $html .= '<td align="center" >'.number_format($fcgstAmt2,2).' </td>';
                    $html .= '<td align="center" >'.number_format($fsgstAmt2,2).' </td>';
                    $html .= '<td align="center" >'.number_format($figstAmt2,2).' </td>';
                    
                }
            }
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_taxable_amt,2).'</span></td>';
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_gst_amt,2).'</span></td>';
              
               $html .= '<td align="right" ><span style="color:#e93232;font-weight:700;text-align:right;">'.number_format($total_bill_amt,2).'</span></td>';
               $html .= '</tr>';
               $html .= '</tfoot>';
         
            $html .= '</table>';
        }
            
        echo json_encode($html);
        die;
    }
    
    public function k1export_gst_purchase_report()
    {
        if (!has_permission_new('k1GSTR_purchase', '', 'export')) {
            access_denied('k1GSTR_purchase');
        }
    	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
       $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date'),
           'accountId'  => $this->input->post('accountId'),
           'bill_type'  => $this->input->post('bill_type'),
           'bill_wise_type'  => $this->input->post('bill_wise_type'),
           'gst_type'  => $this->input->post('gst_type')
          );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $accountId = $this->input->post('accountId');
            $bill_type = $this->input->post('bill_type');
            $bill_wise_type = $this->input->post('bill_wise_type');
            $gst_type = $this->input->post('gst_type');
            $account_full_name = $this->input->post('account_full_name');
          
        $body_data = $this->K1E_Filling_Model->get_purchase_data_for_table($filterdata);
        
        $GstType = $this->K1E_Filling_Model->get_GstTypeP($filterdata);
        $GstTypeWiseValue = $this->K1E_Filling_Model->get_GstTypeWiseValueP($filterdata);
        
        $gstTypeArray = array();
        foreach ($GstType as $key1 => $value1) {
            $GSTS = 0;
            $GSTS = $value1['cgst'] + $value1['sgst'] + $value1['igst'];
            array_push($gstTypeArray, $GSTS);
        }
        $GSTType_unq = array_unique($gstTypeArray);
        sort($GSTType_unq);
        
        $this->load->model('misc_reports_model');
    	$selected_company_details    = $this->misc_reports_model->get_company_detail();
    	if($gst_type !== "2"){
            if($bill_wise_type == 2){
        	    // Day Wise
        	    $default = 4;
        	    $otherColmn = count($GSTType_unq) * 4;
        	    $totColmn = $default + $otherColmn - 1;
        	}else{
        	    // Bill Wise
        	    $default = 7;
        	    $otherColmn = count($GSTType_unq) * 4;
        	    $totColmn = $default + $otherColmn - 1;
        	}	
        }else{
             if($bill_wise_type == 2){
        	    $default = 4;
        	    $totColmn = $default - 1;
        	}else{
        	    
        	    $default = 7;
        	    $totColmn = $default - 1;
        	}
        }
    
    		$writer = new XLSXWriter();
    	    
    		
    		$company_name = array($selected_company_details->company_name);
    		
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = $totColmn);  
    		$writer->writeSheetRow('Sheet1', $company_name);
    
    		$address = $selected_company_details->address;
    		$company_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = $totColmn);  
    		$writer->writeSheetRow('Sheet1', $company_addr);
    		 
    	
    		$html = 'Purchase (';
    		 if($accountId != ''){
                 $html .= 'Accounts: '.$account_full_name.',';
                 }else{
                      $html .= 'Accounts: All';
                 }
                
            $html .= ') form :'.$from_date.', To :'.$to_date.'';
            $filter = array($html);
            $writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = $totColmn); 
    		$writer->writeSheetRow('Sheet1', $filter);
    	 if($bill_wise_type == 2){
    	     
            $set_col_tk = [];
    		$set_col_tk["Date"] =  'Date';
    		if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $set_col_tk["Taxable'".$value2."'"] =  'Taxable'.sprintf('%0.2f', $value2).'%';
                    $set_col_tk["CGST'".($value2 / 2)."'"] =  'CGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                    $set_col_tk["SGST'".($value2 / 2)."'"] =  'SGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                    $set_col_tk["IGST'".$value2."'"] =  'IGST'.sprintf('%0.2f', $value2);
                }
            }
    		$set_col_tk["TaxableAmt"] =  'TaxableAmt';
    		$set_col_tk["GSTAmt"] =  'GSTAmt';
    		$set_col_tk["BillAmt"] =  'BillAmt';
           
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	      $total_taxable_amt = 0;
               $total_gst_amt = 0;
               $total_bill_amt = 0;
           
                    foreach ($body_data as $key => $value) {
                        /*if(($value["sgstamt"] == 0) && ($value["cgstamt"] == 0) && ($value["igstamt"] == 0)){*/
                        if($value["Purchamt"] == 0 || $value["Purchamt"] == 0.00){
                
                   }else{
                   $list_add = [];
                   	
                   	$list_add[] = substr(_d($value["Transdate"]),0,10);
                if($gst_type !== "2"){   
                   foreach ($GSTType_unq as $value2) {
                       $match1 = 0;
                       $taxAmt = 0;
                       $cgstAmt = 0;
                       $sgstAmt = 0;
                       $igstAmt = 0;
                       foreach ($GstTypeWiseValue as $key3 => $value3) {
                           $gstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                           if($gstP2 == $value2 && substr($value["Transdate"],0,10) == substr($value3["TransDate"],0,10)){
                               $match1 = 1;
                               $taxAmt += $value3["taxableAmt"];
                               $cgstAmt += $value3["cgstsum"];
                               $sgstAmt += $value3["sgstsum"];
                               $igstAmt += $value3["igstsum"];
                                
                           }
                        }
                        if($match1 == 0){
                            
                            $list_add[] = "";
                            $list_add[] = "";
                            $list_add[] = "";
                            $list_add[] = "";
                        }else{
                            $list_add[] = sprintf('%0.2f', $taxAmt);
                            $list_add[] = sprintf('%0.2f', $cgstAmt);
                            $list_add[] = sprintf('%0.2f', $sgstAmt);
                            $list_add[] = sprintf('%0.2f', $igstAmt);
                        }  
                    }
                }
                    $total_taxable_amt +=$value["Purchamt"];
                   	$list_add[] = sprintf('%0.2f', $value["Purchamt"]);
                
                   	$gst = $value["sgstamt"]+$value["cgstamt"]+$value["igstamt"];
                    $total_gst_amt +=$gst;
                   	$list_add[] = sprintf('%0.2f', $gst);
                   	 $bill_amt = $value["Purchamt"]+$gst;
                   $total_bill_amt +=$bill_amt;
                   	$list_add[] = sprintf('%0.2f', $bill_amt);
                   
                    $writer->writeSheetRow('Sheet1', $list_add);
                   }
               
               }
              
               $list_add = [];
                $list_add[] = "Total";
                if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt = 0;
                    $fcgstAmt = 0;
                    $fsgstAmt = 0;
                    $figstAmt = 0;
                    foreach ($GstTypeWiseValue as $key3 => $value3) {
                        $fgstP2 = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                        if($fgstP2 == $value2 ){
                            $ftaxAmt += $value3["taxableAmt"];
                            $fcgstAmt += $value3["cgstsum"];
                            $fsgstAmt += $value3["sgstsum"];
                            $figstAmt += $value3["igstsum"];
                        }
                    }
                    $list_add[] = $ftaxAmt;
                    $list_add[] = $fcgstAmt;
                    $list_add[] = $fsgstAmt;
                    $list_add[] = $figstAmt;
                    
                    }
            }
                $list_add[] = $total_taxable_amt;
                $list_add[] = $total_gst_amt;
                $list_add[] = $total_bill_amt;
                  
            $writer->writeSheetRow('Sheet1', $list_add);
          
    	 }else{
    	    
    	    // Bill Wise report 
    	    
    	    $list_add = [];
    	    
    	    $list_add[] = " ";
    	    //$list_add[] = "Account Details";
    	    $list_add[] = "";
    	    if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    //$list_add[] = "GST".$value2."%";
                    $list_add[] = "";
                }
    	    }
    	    $list_add[] = "";
    	    $writer->writeSheetRow('Sheet1', $list_add);
    	    //$writer->markMergedCell("A4:B4"); 
    	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 1);  //merge cells
    	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 2, $end_row = 3, $end_col = 3);  //merge cells
    	    if($gst_type !== "2"){
    	        $i = 4;
                $j = 7;
                foreach ($GSTType_unq as $value2) {
                    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = $i, $end_row = 3, $end_col = $j);  //merge cells
                    $i += 4;
                    $j += 4;
                }
    	    }
    	    $lastThree = $totColmn -2;
    	    $writer->markMergedCell('Sheet1', $start_row = 3, $start_col = $lastThree, $end_row = 3, $end_col = $totColmn);  //merge cells
            
            $set_col_tk = [];
    		$set_col_tk["BillNo"] =  'BillNo';
    		$set_col_tk["Date"] =  'Date';
    		$set_col_tk["Account Name"] =  'Account Name';
    		$set_col_tk["GSTIN"] =  'GSTIN';
    		if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $set_col_tk["Taxable'".$value2."'"] =  'Taxable'.sprintf('%0.2f', $value2).'%';
                    $set_col_tk["CGST'".($value2 / 2)."'"] =  'CGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                    $set_col_tk["SGST'".($value2 / 2)."'"] =  'SGST'.sprintf('%0.2f', ($value2 / 2)).'%';
                    $set_col_tk["IGST'".$value2."'"] =  'IGST'.sprintf('%0.2f', $value2);
                }
            }
    		$set_col_tk["TaxableAmt"] =  'TaxableAmt';
    		$set_col_tk["GSTAmt"] =  'GSTAmt';
    		$set_col_tk["BillAmt"] =  'BillAmt';
           
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	      $total_taxable_amt = 0;
               $total_gst_amt = 0;
               $total_bill_amt = 0;
           
                    foreach ($body_data as $key => $value) {
                    /*    if(($value["sgstamt"] == 0) && ($value["cgstamt"] == 0) && ($value["igstamt"] == 0)){*/
                    if($value["Purchamt"] == 0 || $value["Purchamt"] == 0.00){
                   }else{
                   $list_add = [];
                   	$list_add[] = $value["PurchID"];
                   	
                   	$list_add[] = substr(_d($value["Transdate"]),0,10);
                   	$list_add[] = $value["company"];
                   	$list_add[] = $value["GSTIN"];
                if($gst_type !== "2"){   
                   foreach ($GSTType_unq as $value2) {
                       $match = 0;
                       foreach ($GstTypeWiseValue as $key3 => $value3) {
                           $gstP = $value3['igst'] + $value3['cgst'] + $value3['sgst'];
                           if($gstP == $value2 && $value["PurchID"] == $value3["OrderID"]){
                               $match = 1;
                                $list_add[] = sprintf('%0.2f', $value3["taxableAmt"]);
                                $list_add[] = sprintf('%0.2f', $value3["cgstsum"]);
                                $list_add[] = sprintf('%0.2f', $value3["sgstsum"]);
                                $list_add[] = sprintf('%0.2f', $value3["igstsum"]);
                           }
                        }
                        if($match == "0"){
                            $list_add[] = '';
                            $list_add[] = '';
                            $list_add[] = '';
                            $list_add[] = '';
                        }  
                    }
                }
                    $total_taxable_amt +=$value["Purchamt"];
                   	$list_add[] = sprintf('%0.2f', $value["Purchamt"]);
                   	$gst = $value["sgstamt"]+$value["cgstamt"]+$value["igstamt"];
                    $total_gst_amt +=$gst;
                   	$list_add[] = sprintf('%0.2f', $gst);
                   	$bill_amt = $value["Purchamt"]+$gst;
                    $total_bill_amt +=$bill_amt;
                   	$list_add[] = sprintf('%0.2f', $bill_amt);
                   
                    $writer->writeSheetRow('Sheet1', $list_add);
                   }
               }
              
               $list_add = [];
                $list_add[] = "Total";
                $list_add[] = "";
                $list_add[] = "";
                $list_add[] = "";
            if($gst_type !== "2"){
                foreach ($GSTType_unq as $value2) {
                    $ftaxAmt2 = 0;
                    $fcgstAmt2 = 0;
                    $fsgstAmt2 = 0;
                    $figstAmt2 = 0;
                    foreach ($GstTypeWiseValue as $keyf => $valuef) {
                           $gstPF = $valuef['igst'] + $valuef['cgst'] + $valuef['sgst'];
                           if($gstPF == $value2 ){
                                $ftaxAmt2 += $valuef['taxableAmt'];
                                $fcgstAmt2 += $valuef['cgstsum'];
                                $fsgstAmt2 += $valuef['sgstsum'];
                                $figstAmt2 += $valuef['igstsum'];
                           }
                    }
                    $list_add[] = sprintf('%0.2f', $ftaxAmt2);
                    $list_add[] = sprintf('%0.2f', $fcgstAmt2);
                    $list_add[] = sprintf('%0.2f', $fsgstAmt2);
                    $list_add[] = sprintf('%0.2f', $figstAmt2);
                }
            }
                $list_add[] = sprintf('%0.2f', $total_taxable_amt);
                $list_add[] = sprintf('%0.2f', $total_gst_amt);
                $list_add[] = sprintf('%0.2f', $total_bill_amt);
                  
            $writer->writeSheetRow('Sheet1', $list_add);
    	 }
    		
    		// empty row
    	
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'K1GST Purchase Report.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
//=================== K1 GSTR 1 Report Page load ===============================
    public function K1GSTR1()
    {
        if (!has_permission_new('k1GSTR1', '', 'view')) {
            access_denied('k1GSTR1');
        }
        $title = _l('GST-R 1');
        $data['title'] = "Kirti One GSTR 1";
        $this->load->model('misc_reports_model');
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $this->load->view('admin/K1E_Filling/K1GSTR1', $data);
    }
//========================== Fetch K1GSTR1 Report data =========================
    public function K1GSTR1Reports()
    {
        if (!has_permission_new('k1GSTR1', '', 'view')) {
            access_denied('k1GSTR1');
        }
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date')
        );
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $GSTR1_data = $this->K1E_Filling_Model->GetDataForGSTR1($filterdata);
        
    //===================== B2B HTML ===========================================
    // GST Registered party Invoice wise GST percentage wise sale list
        $TotalTaxableAmt = 0;$TotalInvValue = 0;$srNo = 1;
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                if($GSTPer > 0){
                    $NetAmt = $value["BillAmt"];
                    $OrderInvAmt = 0;
                    $html .= '<tr>';
                    $html .= '<td align="center">'.$srNo.'</td>';
                    foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                        if($value2['SalesID'] == $value["TransID"]){
                            $OrderInvAmt = $value2["INVAMT"];
                            $html .= '<td align="center">'.$value2["GSTIN"].'</td>';
                            $html .= '<td align="center">'.$value["TransID"].'</td>';
                            $html .= '<td align="center">'._d(substr($value["TransDate"],0,10)).'</td>';
                            $html .= '<td align="right">'.number_format(round($OrderInvAmt),2).'</td>';
                            $html .= '<td align="center">'.$value2["state"].'-'.$value2["state"].'</td>';
                        }
                    }
                    $TotalInvValue += round($OrderInvAmt);
                    $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                    $TotalTaxableAmt += $TaxableAmt;
    			    $GSTAmt = $NetAmt - $TaxableAmt;
                    $html .= '<td align="center">N</td>';
                    $html .= '<td align="center">Regular</td>';
                    $html .= '<td></td>';
                    $html .= '<td align="center">'.number_format($GSTPer,2).'</td>';
                    $html .= '<td align="right">'.number_format($TaxableAmt,2).'</td>';
                    $html .= '<td></td>';
                    $html .= '</tr>';
                    $srNo++;
                }
            }
        }
        $html .= '<tr>';
        $html .= '<td></td>';
        $html .= '<td><b>Total</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td align="right"><b>'.number_format(round($TotalInvValue),2).'</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td align="right"><b>'.number_format($TotalTaxableAmt,2).'</b></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // B2CL
        $html2 = '';
        $srNo2 = 001;
        $TotalTaxableAmt = 0;
        $TotalInvoiceAmt = 0;
        foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
            if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                
            }else{
                foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                    $invAmt2 = 0;
                    if($value3['SalesID'] == $value4['TransID']){
                        $GSTPer = 0.00;
                        $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                        if($GSTPer > 0){
                            $NetAmt = $value4["BillAmt"];
                            $TotalInvoiceAmt += $NetAmt;
                            $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                            $TotalTaxableAmt += $TaxableAmt;
                            $html2 .= '<tr>';
                            $html2 .= '<td align="center">'.$srNo2.'</td>';
                            $html2 .= '<td>'.$value4["TransID"].'</td>';
                            $html2 .= '<td align="center">'._d(substr($value3["BillDate"],0,10)).'</td>';
                            $html2 .= '<td align="right">'.number_format($NetAmt,2).'</td>';
                            $html2 .= '<td>'.$value3["state"].'-'.$value3["state_name"].'</td>';
                            $html2 .= '<td align="center">'.number_format($GSTPer,2).'</td>';
                            $html2 .= '<td align="right">'.number_format($TaxableAmt,2).'</td>';
                            $html2 .= '<td></td>';
                            $html2 .= '<td></td>';
                            $html2 .= '</tr>';
                            $srNo2++;
                        }
                    }
                }
            }
        }
        $html2 .= '<tr>';
        $html2 .= '<td></td>';
        $html2 .= '<td>Total</td>';
        $html2 .= '<td></td>';
        $html2 .= '<td align="right">'.number_format(round($TotalInvoiceAmt),2).'</td>';
        $html2 .= '<td></td>';
        $html2 .= '<td></td>';
        $html2 .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>';
        $html2 .= '<td></td>';
        $html2 .= '<td></td>';
        $html2 .= '</tr>';
        
        
        //B2CS
        $srNo3 = 1;
        $TotalTaxableAmt = 0;
        $html3 = '';
        
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer > 0){
                $html3 .= '<tr>';
                $html3 .= '<td align="center">'.$srNo3.'</td>';
                $html3 .= '<td>OE</td>';
                $html3 .= '<td>'.$value6['state'].'</td>';
                $html3 .= '<td align="center">'.number_format($GSTPer,2).'</td>';
                $NetAmt = $value6['BillAmt'];
                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                $TotalTaxableAmt += $TaxableAmt;
                $html3 .= '<td align="right">'.number_format($TaxableAmt,2).'</td>';
                $html3 .= '<td></td>';
                $html3 .= '<td></td>';
                $html3 .= '</tr>';
                $srNo3++;
            }   
        }
        
        $html3 .= '<tr>';
        $html3 .= '<td></td>';
        $html3 .= '<td><b>Total</b></td>';
        $html3 .= '<td></td>';
        $html3 .= '<td></td>';
        $html3 .= '<td align="right"><b>'.number_format($TotalTaxableAmt,2).'</b></td>';
        $html3 .= '<td></td>';
        $html3 .= '<td></td>';
        $html3 .= '</tr>';
        
        $html4 = '';
        $srNo4 = 1;
        $TotalTaxableAmt = 0; $TotalInvAmt = 0;
        foreach ($GSTR1_data['CDNRSaleList'] as $key77 => $value77) {
            foreach ($GSTR1_data['CDNRhistoryData'] as $key7 => $value7) {
                $GST = 0.00;
                if($value77['SalesRtnID']==$value7['OrderID']){
                    if($value7["BillAmt"] == "0.00" || $value7["BillAmt"] == NULL || $value7["BillAmt"] == ''){
                        
                    }else{
                        $GSTPer = $value7["cgst"] + $value7["sgst"] + $value7["igst"];
                        if($GSTPer > 0){
                            $html4 .= '<tr>';
                            $html4 .= '<td align="center">'.$srNo4.'</td>';
                            $html4 .= '<td>'.$value77["GSTIN"].'</td>';
                            $html4 .= '<td>'.$value7["SaleID"].'</td>';
                            $html4 .= '<td align="center">'._d(substr($value77["SaleDate"],0,10)).'</td>';
                            $html4 .= '<td>'.$value7["SalesRtnID"].'</td>';
                            $html4 .= '<td align="center">'._d(substr($value77["SaleRTNDate"],0,10)).'</td>';
                            $html4 .= '<td>01 SalesReturn</td>';
                            $html4 .= '<td align="center">C</td>';
                            $html4 .= '<td>'.$value77["state"].'</td>';
                            $NetAmt = $value7['BillAmt'];
                            $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                            $TotalTaxableAmt += $TaxableAmt;
                            $html4 .= '<td align="right">'.number_format(round($NetAmt),2).'</td>';
                            $html4 .= '<td align="right">0.00</td>';
                            $html4 .= '<td align="right">'.number_format($TaxableAmt,2).'</td>';
                            $html4 .= '<td align="center">N</td>';
                            $TotalInvAmt += round($NetAmt);
                            $html4 .= '<td align="center">'.number_format($GSTPer,2).'</td>';
                            $html4 .= '<td>'.$value77["company"].'</td>';
                            $html4 .= '</tr>';
                            $srNo4++;
                        }
                    }   
                }
            } 
        }
        
        $html4 .= '<tr>';
        $html4 .= '<td></td>';
        $html4 .= '<td>Total</td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td align="right">'.number_format($TotalInvAmt,2).'</td>';
        $html4 .= '<td></td>';
        $html4 .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '<td></td>';
        $html4 .= '</tr>';
        
        $html5 = '';
        $srNo5 = 1;
        $TotalTaxableAmt = 0; $TotalInvAmt = 0;
        foreach ($GSTR1_data['CDNURSaleList'] as $key55 => $value55) {
            foreach ($GSTR1_data['CDNURhistoryData'] as $key5 => $value5) {
                $GST = 0.00;
                if($value55['SalesRtnID']==$value5['OrderID']){
                    if($value5["BillAmt"] == "0.00" || $value5["BillAmt"] == NULL || $value5["BillAmt"] == ''){
                        
                    }else{
                        $GSTPer = $value5["cgst"] + $value5["sgst"] + $value5["igst"];
                        if($GSTPer > 0){
                            $html5 .= '<tr>';
                            $html5 .= '<td align="center">'.$srNo5.'</td>';
                            $html5 .= '<td>'.$value55["GSTIN"].'</td>';
                            $html5 .= '<td>'.$value55["SaleID"].'</td>';
                            $html5 .= '<td align="center">'._d(substr($value55["SaleDate"],0,10)).'</td>';
                            $html5 .= '<td>'.$value55["SalesRtnID"].'</td>';
                            $html5 .= '<td align="center">'._d(substr($value55["SaleRTNDate"],0,10)).'</td>';
                            $html5 .= '<td>01 SalesReturn</td>';
                            $html5 .= '<td align="center">C</td>';
                            $html5 .= '<td>'.$value55["state"].'</td>';
                            $NetAmt = $value5['BillAmt'];
                            $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                            $TotalTaxableAmt += $TaxableAmt;
                            $TotalInvAmt += round($NetAmt);
                            $html5 .= '<td align="right">'.number_format(round($NetAmt),2).'</td>';
                            $html5 .= '<td align="right">0.00</td>';
                            $html5 .= '<td align="right">'.number_format($TaxableAmt,2).'</td>';
                            $html5 .= '<td align="center">N</td>';
                            $html5 .= '<td align="center">'.number_format($GSTPer,2).'</td>';
                            $html5 .= '<td>'.$value55["company"].'</td>';
                            $html5 .= '</tr>';
                            $srNo5++;
                        }
                    }   
                }
            } 
        }
        
        $html5 .= '<tr>';
        $html5 .= '<td></td>';
        $html5 .= '<td>Total</td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td align="right">'.number_format($TotalInvAmt,2).'</td>';
        $html5 .= '<td></td>';
        $html5 .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '<td></td>';
        $html5 .= '</tr>';
        
        
        // Exempt Sale in GSTR-1
        $IntraStateRegSale = 0;
        $InterStateRegSale = 0;
        $IntraStateUnRegSale = 0;
        $InterStateUnRegSale = 0;
        // Register Party Sale
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                $OrderInvAmt = 0;
                if($GSTPer <= 0){
                    foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                        if($value2['SalesID'] == $value["TransID"]){
                            $OrderInvAmt = $value2["INVAMT"];
                            $State = $value2["state"];
                            if($State == "MH" || $State == ""){
                                $IntraStateRegSale += $OrderInvAmt;
                            }else{
                                $InterStateRegSale += $OrderInvAmt;
                            }
                        }
                    }
                }
            }
        }
        
        // Unregistered PAry Sale
        
        foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
            if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                
            }else{
                foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                    $invAmt2 = 0;
                    if($value3['SalesID'] == $value4['TransID']){
                        $GSTPer = 0.00;
                        $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                        if($GSTPer <= 0){
                            $NetAmt = $value4["BillAmt"];
                            if($value3["state"] == "MH" || $value3["state"] == ""){
                                $IntraStateUnRegSale += $NetAmt; 
                            }else{
                                $InterStateUnRegSale += $NetAmt; 
                            }
                        }
                    }
                }
            }
        }
        
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) 
        {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer <= 0){
                $NetAmt = $value6['BillAmt'];
                if($value6["state"] == "MH" || $value6["state"] == ""){
                    $IntraStateUnRegSale += $NetAmt; 
                }else{
                    $InterStateUnRegSale += $NetAmt; 
                }
            }   
        }
        
        $html6 = '';
        $srNo6 = 001;
        $total = 0.00;
        
        $html6 .= '<tr>';
        $html6 .= '<td align="center">'.$srNo6.'</td>';
        $html6 .= '<td>Inter-State Supplies to registered persons</td>';
        $html6 .= '<td></td>';
        $html6 .= '<td align="right">'.number_format($InterStateRegSale,2).'</td>';
        $total += $InterStateRegSale;
        $html6 .= '<td></td>';
        $html6 .= '</tr>';
        $srNo6++;
        
        $html6 .= '<tr>';
        $html6 .= '<td align="center">'.$srNo6.'</td>';
        $html6 .= '<td>Intra-State Supplies to registered persons</td>';
        $html6 .= '<td></td>';
        $html6 .= '<td align="right">'.number_format($IntraStateRegSale,2).'</td>';
        $total += $IntraStateRegSale;
        $html6 .= '<td></td>';
        $html6 .= '</tr>';
        $srNo6++;
        
        $html6 .= '<tr>';
        $html6 .= '<td align="center">'.$srNo6.'</td>';
        $html6 .= '<td>Inter-State Supplies to Unregistered persons</td>';
        $html6 .= '<td></td>';
        $html6 .= '<td align="right">'.number_format($InterStateUnRegSale,2).'</td>';
        $total += $InterStateUnRegSale;
        $html6 .= '<td></td>';
        $html6 .= '</tr>';
        $srNo6++;
        
        $html6 .= '<tr>';
        $html6 .= '<td align="center">'.$srNo6.'</td>';
        $html6 .= '<td>Intra-State Supplies to Unregistered persons</td>';
        $html6 .= '<td></td>';
        $html6 .= '<td align="right">'.number_format($IntraStateUnRegSale,2).'</td>';
        $total += $IntraStateUnRegSale;
        $html6 .= '<td></td>';
        $html6 .= '</tr>';
        $srNo6++;
        $html6 .= '<tr>';
        $html6 .= '<td></td>';
        $html6 .= '<td>Total</td>';
        $html6 .= '<td></td>';
        $html6 .= '<td align="right">'.number_format($total,2).'</td>';
        $html6 .= '<td></td>';
        $html6 .= '</tr>';
        $srNo7 = 1;$html7 = '';
        $AllBillQty = 0;$AllBillAmt = 0;$AllTaxableAmt = 0;$AllIGSTAmt = 0;$AllCGSTAmt = 0;$AllSGSTAmt = 0;
        foreach ($GSTR1_data['HSNList'] as $HSNvalue) {
            foreach($GSTR1_data['TaxrateList'] as $Taxvalue){
                $TotalQty = 0;$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;$match = 0;
                foreach($GSTR1_data['HSNWiseData'] as $Transkey => $Transvalue){
                    $Taxrate = $Transvalue["cgst"] + $Transvalue["sgst"] + $Transvalue["igst"]; // history taxrate
                    if($Transvalue["hsn_code"] == $HSNvalue && number_format($Taxrate,2) == number_format($Taxvalue,2) && $Transvalue["TType"] == "O")
                    {   
                        $hsnDesc = $Transvalue["hsndesc"];
                        $match++;
                        $TotalQty +=  $Transvalue["TotalSaleQty"]; 
                        $NetAmt = $Transvalue["NetAmt"]; 
                        $TotalNetAmt += $NetAmt;
                        $TaxableAmt = $NetAmt / (1+($Taxrate/100));
                        $TotalTaxableAmt += $TaxableAmt;
                        $GSTAmt = $NetAmt - $TaxableAmt;
                        if($Transvalue["igst"]>0){
                            $TotalIGSTAmt += $GSTAmt;
                        }else{
                            $TotalCGSTAmt += $GSTAmt/2;
                            $TotalSGSTAmt += $GSTAmt/2;
                        }
                    }elseif($Transvalue["hsn_code"] == $HSNvalue && number_format($Taxrate,2) == number_format($Taxvalue,2) && $Transvalue["TType"] == "SR"){
                        $match++;
                        $TotalQty -=  $Transvalue["TotalSaleQty"]; 
                        $NetAmt = $Transvalue["NetAmt"]; 
                        $TotalNetAmt -= $NetAmt;
                        $TaxableAmt = $NetAmt / (1+($Taxrate/100));
                        $TotalTaxableAmt -= $TaxableAmt;
                        $GSTAmt = $NetAmt - $TaxableAmt;
                        if($Transvalue["igst"]>0){
                            $TotalIGSTAmt -= $GSTAmt;
                        }else{
                            $TotalCGSTAmt -= $GSTAmt/2;
                            $TotalSGSTAmt -= $GSTAmt/2;
                        }
                    }
                }
                if($match > 0){
                    $html7 .= '<tr>'; 
                    $html7 .= '<td align="center">'.$srNo7.'</td>'; 
                    $html7 .= '<td align="center">'.$HSNvalue.'</td>'; 
                    $html7 .= '<td>'.$hsnDesc.'</td>'; 
                    $html7 .= '<td align="center">PCS-PIECES</td>'; 
                    $html7 .= '<td align="right">'.number_format($TotalQty,2).'</td>'; 
                    $AllBillQty += $TotalQty;
                    $html7 .= '<td align="right">'.number_format($TotalNetAmt,2).'</td>'; 
                    $AllBillAmt += $TotalNetAmt;
                    $html7 .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>'; 
                    $AllTaxableAmt += $TotalTaxableAmt;
                    $html7 .= '<td align="right">'.number_format($TotalIGSTAmt,2).'</td>'; 
                    $AllIGSTAmt += $TotalIGSTAmt;
                    $html7 .= '<td align="right">'.number_format($TotalCGSTAmt,2).'</td>'; 
                    $AllCGSTAmt += $TotalCGSTAmt;
                    $html7 .= '<td align="right">'.number_format($TotalSGSTAmt,2).'</td>'; 
                    $AllSGSTAmt += $TotalSGSTAmt;
                    $html7 .= '<td></td>'; 
                    $html7 .= '<td align="center">'.number_format($Taxvalue,2).'</td>'; 
                    $html7 .= '<tr>'; 
                    $srNo7++;
                }
            }
        }
        $html7 .= '<tr>';
        $html7 .= '<td></td>';
        $html7 .= '<td>Total</td>';
        $html7 .= '<td></td>';
        $html7 .= '<td></td>';
        $html7 .= '<td align="right">'.number_format($AllBillQty,2).'</td>';
        $html7 .= '<td align="right">'.number_format($AllBillAmt,2).'</td>';
        $html7 .= '<td align="right">'.number_format($AllTaxableAmt,2).'</td>';
        $html7 .= '<td align="right">'.number_format($AllIGSTAmt,2).'</td>';
        $html7 .= '<td align="right">'.number_format($AllCGSTAmt,2).'</td>';
        $html7 .= '<td align="right">'.number_format($AllSGSTAmt,2).'</td>';
        $html7 .= '<td></td>';
        $html7 .= '<td></td>';
        $html7 .= '</tr>';
        
        $srNo8 = 1;
        $html8 = '';
        
        $html8 .= '<tr>';
        $html8 .= '<td align="center">'.$srNo8.'</td>';
        $html8 .= '<td>Invoice for Outward Supply</td>';
        $html8 .= '<td align="center">'.$GSTR1_data["FirstInv"].'</td>';
        $html8 .= '<td align="center">'.$GSTR1_data["LastInv"].'</td>';
        $html8 .= '<td align="center">'.$GSTR1_data["OkInvoice"].'</td>';
        $html8 .= '<td align="center">'.$GSTR1_data["CancelInvoice"].'</td>';
        $html8 .= '</tr>';
        $srNo8++;
        
        $response  = array();
        $response['B2BHTML']= $html;
        $response['B2CL']= $html2;
        $response['B2CS']= $html3;
        $response['CDNR']= $html4;
        $response['CDNUR']= $html5;
        $response['EXEMP']= $html6;
        $response['HSN']= $html7;
        $response['DOCS']= $html8;
        echo json_encode($response);
        //echo json_encode($GSTR1_data);
    }
//================== Export GSTR1 Report =======================================
    public function GSTR1ReportsExport()
    {
        if (!has_permission_new('k1GSTR1', '', 'export')) {
            access_denied('k1GSTR1');
        }   
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
        	$filterdata = array(
               'from_date' => $this->input->post('from_date'),
               'to_date'  => $this->input->post('to_date')
            );
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $GSTR1_data = $this->K1E_Filling_Model->GetDataForGSTR1($filterdata);
            
            $this->load->model('misc_reports_model');
        	$selected_company_details    = $this->misc_reports_model->get_company_detail();
        	
        	$writer = new XLSXWriter();
        	$company_name = array($selected_company_details->company_name);
    		$address = $selected_company_details->address;
    		$company_addr = array($address);
    		
    		//================ B2B Sheet =======================================
    		$header = array('SrNo' => '0','GSTIN' => 'string','InvNumber' => 'string','InvDate' => 'string','InvValue' => '0.00','PlaceOfSupply' => 'string',
    		'RevCharge' => 'string','InvoiceType' => 'string','E-comGSTIN' => 'string','GSTRate' => '0.00','TaxableValue' => '0.00','CessAmount' => '0.00');
    	    
    	    $headers = array($selected_company_details->company_name,'Subject','Content');
    	    // GST Registered party Invoice wise GST percentage wise sale list
                $TotalTaxableAmt = 0;$TotalInvValue = 0;
                $rows = array();
                $srNo = 1;
                foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
                    if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                        
                    }else{
                        $GSTPer = 0.00;
                        $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                        if($GSTPer > 0){
                            $NetAmt = $value["BillAmt"];
                            $OrderInvAmt = 0;
                            $GSTNo = "";
                            $state = "";
                            foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                                if($value2['SalesID'] == $value["TransID"]){
                                    $GSTNo = $value2["GSTIN"];
                                    $OrderInvAmt = $value2["INVAMT"];
                                    $state = $value2["state"].'-'.$value2["state"];
                                }
                            }
                            $TotalInvValue += round($OrderInvAmt);
                            $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                            $TotalTaxableAmt += $TaxableAmt;
                            
                            $row = array($srNo,$GSTNo,$value["TransID"], _d(substr($value["TransDate"],0,10)), $OrderInvAmt, $state, 'N', 'Regular','',$GSTPer,$TaxableAmt,'');
                            $srNo++;
                            array_push($rows, $row);
                        
                        }
                    }
                }
                $row = array('','Total','', '', number_format(round($TotalInvValue),2), '', '', '','','',number_format($TotalTaxableAmt,2),'');
                array_push($rows, $row);
                $writer->writeSheet($rows, 'B2B', $header);
            
            
            
            // B2CL Export
            
            $header2 = array('SrNo' => '0','InvNumber' => 'string','InvDate' => 'string','InvAmt' => '0.00','PlaceOfSupply' => 'string',
    		'Rate' => '0.00','TaxableValue' => '0.00','CessAmount' => 'string','E-comGSTIN' => 'string');
    	    
    	    $srNo2 = 001;
            $TotalTaxableAmt = 0;
            $TotalInvoiceAmt = 0;
            $rows2 = array();
            foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
                if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                    
                }else{
                    foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                        $invAmt2 = 0;
                        if($value3['SalesID'] == $value4['TransID']){
                            $GSTPer = 0.00;
                            $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                            if($GSTPer > 0){
                                $NetAmt = $value4["BillAmt"];
                                $TotalInvoiceAmt += $NetAmt;
                                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                                $TotalTaxableAmt += $TaxableAmt;
                                $State = $value3["state"].'-'.$value3["state_name"];
                                
                                $row2 = array($srNo2,$value4["TransID"],_d(substr($value3["BillDate"],0,10)),$NetAmt,$State,
                                            $GSTPer,$TaxableAmt,'','');
                                array_push($rows2, $row2);
                                $srNo2++;
                            }
                        }
                    }
                }
            }
            $row2 = array('','Total','',number_format(round($TotalInvoiceAmt),2),'','',number_format($TotalTaxableAmt,2),'','');
            array_push($rows2, $row2);
            $writer->writeSheet($rows2, 'B2CL', $header2);
            
            
            // B2CS Sheet Preparation
            $header3 = array('SrNo' => '0','InvType' => 'string','PlaceOfSupply' => 'string',
    		'Rate' => '0.00','TaxableValue' => '0.00','CessAmount' => 'string','E-comGSTIN' => 'string');
    		$srNo3 = 1;
    		$rows3 = array();
            $TotalTaxableAmt = 0;
            foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) {
                $GSTPer = 0.00;
                $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
                if($GSTPer > 0){
                    $NetAmt = $value6['BillAmt'];
                    $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                    $TotalTaxableAmt += $TaxableAmt;
                    
                    $row3 = array($srNo3,'OE',$value6['state'],$GSTPer,number_format($TaxableAmt,2),'','');
                    array_push($rows3, $row3);
                    $srNo3++;
                }   
            }
            $row3 = array('','Total','','',number_format($TotalTaxableAmt,2),'','');
            array_push($rows3, $row3);
            $writer->writeSheet($rows3, 'B2CS', $header3);
            
        
            
            // CDNR Export Sheet
            
            $header4 = array('SrNo' => '0','GSTINUINofRecipient'=>'string','InvoiceAdvanceReceiptNumber' => 'string','InvoiceAdvanceReceiptDate' => 'string',
    		'NoteRefundVoucherNumber' => 'string','NoteRefundVoucherDate' => 'string','DocumentType' => 'string',
    		'ReasonForIssuingdicument' => 'string','PlaceOfSupply' => 'string','NoteRefundVoucherValue'=>'0.00','Rate'=>'0.00',
    		'TaxableValue'=>'0.00','CessAmt'=>'0.00','PreGst'=>'string','ReceiverName'=>'string');
            
            $rows4 = array();
            $srNo4 = 1;
            $TotalTaxableAmt = 0; $TotalInvAmt = 0;
            foreach ($GSTR1_data['CDNRSaleList'] as $key77 => $value77) {
                foreach ($GSTR1_data['CDNRhistoryData'] as $key7 => $value7) {
                    $GST = 0.00;
                    if($value77['SalesRtnID']==$value7['OrderID']){
                        if($value7["BillAmt"] == "0.00" || $value7["BillAmt"] == NULL || $value7["BillAmt"] == ''){
                            
                        }else{
                            $GSTPer = $value7["cgst"] + $value7["sgst"] + $value7["igst"];
                            if($GSTPer > 0){
                                $NetAmt = $value7['BillAmt'];
                                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                                $TotalTaxableAmt += $TaxableAmt;
                                $TotalInvAmt += round($NetAmt);
                                $row4 = array($srNo4,$value77["GSTIN"],$value7["SaleID"],_d(substr($value77["SaleDate"],0,10)),$value7["SalesRtnID"],_d(substr($value77["SaleRTNDate"],0,10)),
                                '01 SalesReturn','C',$value77["state"],number_format(round($NetAmt),2),'0.00',
                                number_format($TaxableAmt,2),'N',number_format($GSTPer,2),$value77["company"]);
                                array_push($rows4, $row4);
                                $srNo4++;
                            }
                        }   
                    }
                } 
            }
            $row4 = array('','Total','','','','','','','',number_format($TotalInvAmt,2),'',number_format($TotalTaxableAmt,2),'','','');
            array_push($rows4, $row4);
            $writer->writeSheet($rows4, 'CDNR', $header4);
            
            
            // CDNUR Export
            $header5 = array('SrNo' => '0','GSTINUINofRecipient'=>'string','InvoiceAdvanceReceiptNumber' => 'string','InvoiceAdvanceReceiptDate' => 'string',
    		'NoteRefundVoucherNumber' => 'string','NoteRefundVoucherDate' => 'string','DocumentType' => 'string',
    		'ReasonForIssuingdicument' => 'string','PlaceOfSupply' => 'string','NoteRefundVoucherValue'=>'0.00','Rate'=>'0.00',
    		'TaxableValue'=>'0.00','CessAmt'=>'0.00','PreGst'=>'string','ReceiverName'=>'string');
            
            $rows5 = array();
            $srNo5 = 1;
            $TotalTaxableAmt = 0; $TotalInvAmt = 0;
            foreach ($GSTR1_data['CDNURSaleList'] as $key55 => $value55) {
                foreach ($GSTR1_data['CDNURhistoryData'] as $key5 => $value5) {
                    $GST = 0.00;
                    if($value55['SalesRtnID']==$value5['OrderID']){
                        if($value5["BillAmt"] == "0.00" || $value5["BillAmt"] == NULL || $value5["BillAmt"] == ''){
                            
                        }else{
                            $GSTPer = $value5["cgst"] + $value5["sgst"] + $value5["igst"];
                            if($GSTPer > 0){
                                $NetAmt = $value5['BillAmt'];
                                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                                $TotalTaxableAmt += $TaxableAmt;
                                $TotalInvAmt += round($NetAmt);
                                
                                $row5 = array($srNo5,$value55["GSTIN"],$value55["SaleID"],_d(substr($value55["SaleDate"],0,10)),$value55["SalesRtnID"],_d(substr($value55["SaleRTNDate"],0,10)),
                                '01 SalesReturn','C',$value55["state"],number_format(round($NetAmt),2),'0.00',
                                number_format($TaxableAmt,2),'N',number_format($GSTPer,2),$value55["company"]);
                                array_push($rows5, $row5);
                                $srNo5++;
                            }
                        }   
                    }
                } 
            }
            $row5 = array('','Total','','','','','','','',number_format($TotalInvAmt,2),'',number_format($TotalTaxableAmt,2),'','','');
            array_push($rows5, $row5);
            $writer->writeSheet($rows5, 'CDNUR', $header5);
            
            //EXEMP Export
            
            $IntraStateRegSale = 0;
            $InterStateRegSale = 0;
            $IntraStateUnRegSale = 0;
            $InterStateUnRegSale = 0;
            // Register Party Sale
            foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
                if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                    
                }else{
                    $GSTPer = 0.00;
                    $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                    $OrderInvAmt = 0;
                    if($GSTPer <= 0){
                        foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                            if($value2['SalesID'] == $value["TransID"]){
                                $OrderInvAmt = $value2["INVAMT"];
                                $State = $value2["state"];
                                if($State == "MH" || $State == ""){
                                    $IntraStateRegSale += $OrderInvAmt;
                                }else{
                                    $InterStateRegSale += $OrderInvAmt;
                                }
                            }
                        }
                    }
                }
            }
            
            // Unregistered PAry Sale
            
            foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
                if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                    
                }else{
                    foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                        $invAmt2 = 0;
                        if($value3['SalesID'] == $value4['TransID']){
                            $GSTPer = 0.00;
                            $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                            if($GSTPer <= 0){
                                $NetAmt = $value4["BillAmt"];
                                if($value3["state"] == "MH" || $value3["state"] == ""){
                                    $IntraStateUnRegSale += $NetAmt; 
                                }else{
                                    $InterStateUnRegSale += $NetAmt; 
                                }
                            }
                        }
                    }
                }
            }
            
            foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) 
            {
                $GSTPer = 0.00;
                $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
                if($GSTPer <= 0){
                    $NetAmt = $value6['BillAmt'];
                    if($value6["state"] == "MH" || $value6["state"] == ""){
                        $IntraStateUnRegSale += $NetAmt; 
                    }else{
                        $InterStateUnRegSale += $NetAmt; 
                    }
                }   
            }
        
        
            $header6 = array('SrNo' => '0','Description' => 'string','NilRatedSupplies' => 'string',
    		'Exempted' => '0.00','NonGSTSupplies' => 'string');
    		$rows6 = array();
    		
    		
            $srNo6 = 001;
            $total = 0.00;
       
            $total += $InterStateRegSale;
            $row6 = array($srNo6,'Inter-State Supplies to registered persons','',$InterStateRegSale,'');
            array_push($rows6, $row6);
            $srNo6++;
                
            $total += $IntraStateRegSale;
            $row6 = array($srNo6,'Intra-State Supplies to registered persons','',$IntraStateRegSale,'');
            array_push($rows6, $row6);
            $srNo6++;
                
            $total += $InterStateUnRegSale;
            $row6 = array($srNo6,'Inter-State Supplies to Unregistered persons','',$InterStateUnRegSale,'');
            array_push($rows6, $row6);
            $srNo6++;
                
            $total += $IntraStateUnRegSale;
            $row6 = array($srNo6,'Intra-State Supplies to Unregistered persons','',$IntraStateUnRegSale,'');
            array_push($rows6, $row6);
            $srNo6++;
                
            $row6 = array('','Total','',$total,'');
            array_push($rows6, $row6);
            $writer->writeSheet($rows6, 'EXEMP', $header6);
            
        // HSN Export
        
            $srNo7 = 001;$AllBillQty = 0;$AllBillAmt = 0;$AllTaxableAmt = 0;$AllIGSTAmt = 0;$AllCGSTAmt = 0;$AllSGSTAmt = 0;
            
            $rows7 = array();
            $header7 = array('SrNo' => '0','HSN' => 'string','Description' => 'string','UQC'=>'string',
    		'TotalQty' => '0.00','TotalValue' => '0.00','TaxableValue'=>'0.00','IntegratedTax'=>'0.00','CentralTax'=>'0.00',
    		'State/UTTax'=>'0.00','CessAmount'=>'0.00','GST%'=>'0.00');
            
            foreach ($GSTR1_data['HSNList'] as $HSNvalue) {
                foreach($GSTR1_data['TaxrateList'] as $Taxvalue){
                    $TotalQty = 0;$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;$match = 0;
                    foreach($GSTR1_data['HSNWiseData'] as $Transkey => $Transvalue){
                        $Taxrate = $Transvalue["cgst"] + $Transvalue["sgst"] + $Transvalue["igst"]; // history taxrate
                        if($Transvalue["hsn_code"] == $HSNvalue && number_format($Taxrate,2) == number_format($Taxvalue,2) && $Transvalue["TType"] == "O")
                        {   
                            $hsnDesc = $Transvalue["hsndesc"];
                            $match++;
                            $TotalQty +=  $Transvalue["TotalSaleQty"]; 
                            $NetAmt = $Transvalue["NetAmt"]; 
                            $TotalNetAmt += $NetAmt;
                            $TaxableAmt = $NetAmt / (1+($Taxrate/100));
                            $TotalTaxableAmt += $TaxableAmt;
                            $GSTAmt = $NetAmt - $TaxableAmt;
                            if($Transvalue["igst"]>0){
                                $TotalIGSTAmt += $GSTAmt;
                            }else{
                                $TotalCGSTAmt += $GSTAmt/2;
                                $TotalSGSTAmt += $GSTAmt/2;
                            }
                        }elseif($Transvalue["hsn_code"] == $HSNvalue && number_format($Taxrate,2) == number_format($Taxvalue,2) && $Transvalue["TType"] == "SR"){
                            $match++;
                            $TotalQty -=  $Transvalue["TotalSaleQty"]; 
                            $NetAmt = $Transvalue["NetAmt"]; 
                            $TotalNetAmt -= $NetAmt;
                            $TaxableAmt = $NetAmt / (1+($Taxrate/100));
                            $TotalTaxableAmt -= $TaxableAmt;
                            $GSTAmt = $NetAmt - $TaxableAmt;
                            if($Transvalue["igst"]>0){
                                $TotalIGSTAmt -= $GSTAmt;
                            }else{
                                $TotalCGSTAmt -= $GSTAmt/2;
                                $TotalSGSTAmt -= $GSTAmt/2;
                            }
                        }
                    }
                    if($match > 0){
                        $AllBillQty += $TotalQty;
                        $AllBillAmt += $TotalNetAmt;
                        $AllTaxableAmt += $TotalTaxableAmt;
                        $AllIGSTAmt += $TotalIGSTAmt;
                        $AllCGSTAmt += $TotalCGSTAmt;
                        $AllSGSTAmt += $TotalSGSTAmt;
                        $row7 = array($srNo7,$HSNvalue,$hsnDesc,'PCS-PIECES',number_format($TotalQty,2),number_format($TotalQty,2),number_format($TotalTaxableAmt,2),
                        number_format($TotalIGSTAmt,2),number_format($TotalCGSTAmt,2),number_format($TotalSGSTAmt,2),'',number_format($Taxvalue,2));
                        array_push($rows7, $row7);
                        $srNo7++;
                    }
                }
            }
            $row7 = array('','Total','','',number_format($AllBillQty,2),number_format($AllBillAmt,2),number_format($AllTaxableAmt,2),
            number_format($AllIGSTAmt,2),number_format($AllCGSTAmt,2),number_format($AllSGSTAmt,2),'','');
            array_push($rows7, $row7);
            $writer->writeSheet($rows7, 'HSN', $header7);
            
        // Docs Export Sheet
            
            $header8 = array('SrNo' => '0','NatureofDocument' => 'string','SrNoFrom' => 'string','SrNoTo'=>'string',
    		'TotalNumber' => '0.00','Cancelled' => '0.00');
    		$rows8 = array();
            $srNo8 = 1;
            
            $row8 = array($srNo8,'Invoice for Outward Supply',$GSTR1_data["FirstInv"],$GSTR1_data["LastInv"],$GSTR1_data["OkInvoice"],$GSTR1_data["CancelInvoice"]);
            array_push($rows8, $row8);
            $srNo8++;
            
            /*$row8 = array($srNo8,'Invoice for Outward Supply',$DOCS_data["BStart"],$DOCS_data["BEnd"],$DOCS_data["BTotal"],$DOCS_data["BTotalC"]);
            array_push($rows8, $row8);
            $srNo8++;
            
            $row8 = array($srNo8,'Invoice for Outward Supply',$DOCS_data["MStart"],$DOCS_data["MEnd"],$DOCS_data["MTotal"],$DOCS_data["MTotalC"]);
            array_push($rows8, $row8);
            $srNo8++;*/
            
            $writer->writeSheet($rows8, 'DOCS', $header8);
    	}
    	
    	$filename = 'K1GSTR1.xlsx';
    	$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    	echo json_encode([
    		'site_url'          => site_url(),
    		'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    	]);
    }
//======================== GSTR 3B Page Load ===================================
    public function K1GSTR3B()
    {
        if (!has_permission_new('K1GSTR3B', '', 'view')) {
            access_denied('K1GSTR3B');
        }
        $data['title'] = "K1 GSTR 3B";
        $this->load->model('misc_reports_model');
        $data['company_detail'] = $this->misc_reports_model->get_company_detail();
        $this->load->view('admin/K1E_Filling/K1GSTR3B', $data);
    }
//=========================== Load GSTR 3B Data ================================
    public function loadGSRT3B()
    {  
        if (!has_permission_new('K1GSTR3B', '', 'view')) {
            access_denied('K1GSTR3B');
        }
        $filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date')
        );
        $GSTR1_data = $this->K1E_Filling_Model->GetDataForGSTR1($filterdata);
        
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $html = '';
        $html .= '<table class="table-striped table-bordered production_report" id="gstr3B" width="100%">';
        $html .= '<thead style="font-size:11px;">';
        
        $html .= '<tr>';
        $html .= '<th align="center">level</th>';
        $html .= '<th align="center">Nature of Supplies</th>';
        $html .= '<th align="center">Taxable Value</th>';
        $html .= '<th align="center">IntgegratedTax</th>';
        $html .= '<th align="center">CentralTax</th>';
        $html .= '<th align="center">State/UTTax</th>';
        $html .= '<th align="center">Cess</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        // 3.1
        $html .= '<tr>';
        $html .= '<td><b>GSTR3B_3.1</b></td>';
        $html .= '<td><b>Detail of Outward supplies and Inward supplies liable to reverse charges</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // Section 3.1    
        // 3.1(a) – Outward taxable supplies (other than zero rated, nil rated and exempted) = State and Central Sales in which you charge GST and it’s Tax amount.
        // B2B + B2C taxable sales (IGST/CGST/SGST)
        $TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                if($GSTPer > 0){
                    $NetAmt = $value["BillAmt"];
                    $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                    $TotalTaxableAmt += $TaxableAmt;
    			    $GSTAmt = $NetAmt - $TaxableAmt;
    			    if($value['igst'] > 0){
    			        $TotalIGSTAmt += $GSTAmt;
    			    }else{
    			        $TotalSGSTAmt += $GSTAmt/2;
    			        $TotalCGSTAmt += $GSTAmt/2;
    			    }
                }
            }
        }
        
        foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
            $GSTPer = 0.00;
            $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
            if($GSTPer > 0){
                $NetAmt = $value4["BillAmt"];
                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                $TotalTaxableAmt += $TaxableAmt;
                $GSTAmt = $NetAmt - $TaxableAmt;
			    if($value4['igst'] > 0){
			        $TotalIGSTAmt += $GSTAmt;
			    }else{
			        $TotalSGSTAmt += $GSTAmt/2;
			        $TotalCGSTAmt += $GSTAmt/2;
			    }
            }
        }
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.1.a</td>';
        $html .= '<td>(a). Outward taxable supplies(other than zero rate, nil rated and exmpted)</td>';
        $html .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($TotalIGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($TotalCGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($TotalSGSTAmt,2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // 3.1(b) – Outward taxable supplies (zero rated) = Supplies with Zero GST rate, i.e, exports or supplies made to SEZ.
        $html .= '<tr>';
        $html .= '<td><b>GSTR3B_3.1.b</b></td>';
        $html .= '<td><b>(b). Outward taxable supplies(zero rated)</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // 3.1(c) – Other outward supplies (Nil rated, exempted) = Supplies with Nill Rated and Exempt such as Milk and Salt.
        // Exempt Sale in GSTR-1
        $TotalTaxableAmt = 0;
        
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                $OrderInvAmt = 0;
                if($GSTPer <= 0){
                    foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                        if($value2['SalesID'] == $value["TransID"]){
                            $OrderInvAmt = $value2["INVAMT"];
                            $TotalTaxableAmt += $OrderInvAmt;
                        }
                    }
                }
            }
        }
        foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
            if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                
            }else{
                foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                    if($value3['SalesID'] == $value4['TransID']){
                        $GSTPer = 0.00;
                        $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                        if($GSTPer <= 0){
                            $NetAmt = $value4["BillAmt"];
                            $TotalTaxableAmt += $NetAmt;
                        }
                    }
                }
            }
        }
        
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) 
        {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer <= 0){
                $NetAmt = $value6['BillAmt'];
                $TotalTaxableAmt += $NetAmt;
            }   
        }
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.1.c</td>';
        $html .= '<td>Other Outward supplies,(Nil rated, exmpted)</td>';
        $html .= '<td align="right">'.number_format($TotalTaxableAmt,2).'</td>';
        $html .= '<td align="right">'.number_format(0,2).'</td>';
        $html .= '<td align="right">'.number_format(0,2).'</td>';
        $html .= '<td align="right">'.number_format(0,2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        
        
        // 3.1(d) – Inward supplies (liable to reverse charge) = Purchases made from UnRegistered suppliers for which you need to create an invoice for yourself to pay the GST.
        $gstr_3_1_d = $this->K1E_Filling_Model->get_data_for_gstr_3_1_d($filterdata);
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.1.d</td>';
        $html .= '<td>Inward supplies(liable to reverse charges) - UnRegistered and Taxable</td>';
        $html .= '<td align="right">'.number_format($gstr_3_1_d["TaxableAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_3_1_d["IAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_3_1_d["CAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_3_1_d["SAmt"],2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        // 3.1(e) – Non-GST outward supplies = Goods that are not covered in GST, eg., Alcohol, Petroleum products etc.
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.1.e</td>';
        $html .= '<td>Not GST Outward supplies</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // 3.2.a Supplies made to Unregistered Persons = Capture Interstate sales to Unregistered Persons.
        $ATotalTaxableAmt = 0;$ATotalCGSTAmt = 0;$ATotalSGSTAmt = 0;$ATotalIGSTAmt = 0;
        $BTotalTaxableAmt = 0;$BTotalCGSTAmt = 0;$BTotalSGSTAmt = 0;$BTotalIGSTAmt = 0;
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer > 0){
                $NetAmt = $value6['BillAmt'];
                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                $GSTAmt = $NetAmt - $TaxableAmt;
                if($value6['state'] != "MH" && $value6['state'] != ""){
                    $ATotalTaxableAmt += $TaxableAmt;
                    $ATotalIGSTAmt += $GSTAmt;
                }elseif($value6['state'] == "MH" || $value6['state'] == ""){
                    $BTotalTaxableAmt += $TaxableAmt;
                    $BTotalSGSTAmt += $GSTAmt/2;
                    $BTotalCGSTAmt += $GSTAmt/2;
                }
            }   
        }
        //3.2
        $html .= '<tr>';
        $html .= '<td><b>GSTR3B_3.2</b></td>';
        $html .= '<td><b>3.2 of the supplies show in 3.1(a) above, details of the inter-State supplies made to unregistered persons.</b></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        
         //3.2.a
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.2.a</td>';
        $html .= '<td>Supplies made to unregisterd persons(InterState Sale)</td>';
        $html .= '<td align="right">'.number_format($ATotalTaxableAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($ATotalIGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($ATotalCGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($ATotalSGSTAmt,2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
       
        // 3.2.b Supplies made to Composition Taxable Persons = Interstate sales made to Composition Tax Payers.
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.2.b</td>';
        $html .= '<td>Supplies made to Composition Taxable Persons(IntraState Sale)</td>';
        $html .= '<td align="right">'.number_format($BTotalTaxableAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($BTotalIGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($BTotalCGSTAmt,2).'</td>';
        $html .= '<td align="right">'.number_format($BTotalSGSTAmt,2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
         //3.2.c
        $html .= '<tr>';
        $html .= '<td>GSTR3B_3.2.c</td>';
        $html .= '<td>Supplies made to UIN Holders</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.A
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A</td>';
        $html .= '<td>(A) ITC Available (whether in full or part)</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.A.1
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A.1</td>';
        $html .= '<td>(1) Import of goods</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.A.2
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A.2</td>';
        $html .= '<td>(2) Import of services</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.A.3
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A.3</td>';
        $html .= '<td>(3) Inward supplies liable to reverse charge (other than 1 & 2 above)</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.A.4
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A.4</td>';
        $html .= '<td>(4) Inward supplies from ISD</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        
        // 4.A.5 (5)All other ITC = ITC from all regular purchases (goods + services + capital goods) excluding imports, RCM, ISD.
        $gstr_4_A_5 = $this->K1E_Filling_Model->get_data_for_gstr_4_A_5($filterdata);
        //4.A.5
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.A.5</td>';
        $html .= '<td>(5) All other ITC</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["TaxableAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["IAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["CAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["SAmt"],2).'</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        //4.B
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.B</td>';
        $html .= '<td>(B) ITC Reversed</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.B.1
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.B.1</td>';
        $html .= '<td>(1) As per rules 42 & 43 of CGST Rules</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
         //4.B.2
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.B.2</td>';
        $html .= '<td>(2) Others</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
          //4.C
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.C</td>';
        $html .= '<td>(C) Net ITC Available (A) – (B)</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
          //4.D
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.D</td>';
        $html .= '<td>(D) Ineligible ITC</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
          //4.D.1
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.D.1</td>';
        $html .= '<td>(1) As per section 17(5)</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
          //4.D.2
        $html .= '<tr>';
        $html .= '<td>GSTR3B_4.D.2</td>';
        $html .= '<td>(2) Others</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        // Empty row
        $html .= '<tr style="height:20px;">';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '</tbody>';
        $html .= '</table>';
        // 5.1 From a supplier under composition scheme, Exempt and Nil rated supply = Inter-state and Intra-State purchase of goods 0%, Exempt etc.
        
        $html .= '<table class="table-striped table-bordered gstr3B_2" id="gstr3B_2" width="100%" style="margin-top:20px;">';
        $html .= '<thead style="font-size:11px;">';
        
        $html .= '<tr>';
        $html .= '<th align="center">level</th>';
        $html .= '<th align="center">Nature of Supplies</th>';
        $html .= '<th align="center">Inter State Supplies</th>';
        $html .= '<th align="center">Intra State Supplies</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        //gstr3B_5.1
        $html .= '<tr>';
        $html .= '<td>gstr3B_5.1</td>';
        $html .= '<td>Values of exempt, nil-rated and non-GST inward supplies</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["InterStateTaxableAmt"],2).'</td>';
        $html .= '<td align="right">'.number_format($gstr_4_A_5["IntraStateTaxableAmt"],2).'</td>';
        $html .= '</tr>';
        
        // gstr3B_5.2
        $html .= '<tr>';
        $html .= '<td>gstr3B_5.2</td>';
        $html .= '<td>Non GST supply</td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '</tbody>';
        $html .= '</table>';
        echo json_encode($html);
        die;
    }
//========================= Export GSTR 3B =====================================
    public function export_GSTR3B_report()
    {  
        if (!has_permission_new('K1GSTR3B', '', 'export')) {
            access_denied('K1GSTR3B');
        }
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	$filterdata = array(
           'from_date' => $this->input->post('from_date'),
           'to_date'  => $this->input->post('to_date')
        );
        $GSTR1_data = $this->K1E_Filling_Model->GetDataForGSTR1($filterdata);
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        $this->load->model('misc_reports_model');
    	$selected_company_details    = $this->misc_reports_model->get_company_detail();
    	
        $writer = new XLSXWriter();
        $company_name = array($selected_company_details->company_name);
    	$writer->markMergedCell('GSTR3B', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 6);  //merge cells
    	$writer->writeSheetRow('GSTR3B', $company_name);
    
    	$address = $selected_company_details->address;
    	$company_addr = array($address,);
    	$writer->markMergedCell('GSTR3B', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 6);  //merge cells
    	$writer->writeSheetRow('GSTR3B', $company_addr);
    	
    	// Header
    	$set_col_tk = [];
    	$set_col_tk["level"] =  'level';
    	$set_col_tk["Nature of Supplies"] =  'Nature of Supplies';
    	$set_col_tk["Taxable Value"] =  'Taxable Value';
    	$set_col_tk["IntgegratedTax"] =  'IntgegratedTax';
    	$set_col_tk["CentralTax"] =  'CentralTax';
    	$set_col_tk["State/UTTax"] =  'State/UTTax';
    	$set_col_tk["Cess"] =  'Cess';
    	
    	$writer_header = $set_col_tk;
        $writer->writeSheetRow('GSTR3B', $writer_header);
    // GSTR3B_3.1
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1';
        $list_add[] = 'Detail of Outward supplies and Inward supplies liable to reverse charges';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //3.1.a
        // Section 3.1    
        // 3.1(a) – Outward taxable supplies (other than zero rated, nil rated and exempted) = State and Central Sales in which you charge GST and it’s Tax amount.
        // B2B + B2C taxable sales (IGST/CGST/SGST)
        $TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
                
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                if($GSTPer > 0){
                    $NetAmt = $value["BillAmt"];
                    $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                    $TotalTaxableAmt += $TaxableAmt;
    			    $GSTAmt = $NetAmt - $TaxableAmt;
    			    if($value['igst'] > 0){
    			        $TotalIGSTAmt += $GSTAmt;
    			    }else{
    			        $TotalSGSTAmt += $GSTAmt/2;
    			        $TotalCGSTAmt += $GSTAmt/2;
    			    }
                }
            }
        }
        
        foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
            $GSTPer = 0.00;
            $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
            if($GSTPer > 0){
                $NetAmt = $value4["BillAmt"];
                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                $TotalTaxableAmt += $TaxableAmt;
                $GSTAmt = $NetAmt - $TaxableAmt;
			    if($value4['igst'] > 0){
			        $TotalIGSTAmt += $GSTAmt;
			    }else{
			        $TotalSGSTAmt += $GSTAmt/2;
			        $TotalCGSTAmt += $GSTAmt/2;
			    }
            }
        }
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1.a';
        $list_add[] = '(a). Outward taxable supplies(other than zero rate, nil rated and exmpted)';
        $list_add[] = number_format($TotalTaxableAmt,2);
        $list_add[] = number_format($TotalIGSTAmt,2);
        $list_add[] = number_format($TotalCGSTAmt,2);
        $list_add[] = number_format($TotalSGSTAmt,2);
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //3.1.b
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1.b';
        $list_add[] = '(b). Outward taxable supplies(zero rated)';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    // 3.1(c) – Other outward supplies (Nil rated, exempted) = Supplies with Nill Rated and Exempt such as Milk and Salt.
        // Exempt Sale in GSTR-1
        $TotalTaxableAmt = 0;
        
        foreach ($GSTR1_data['B2BhistoryData'] as $key => $value) {
            if($value["BillAmt"] == "0.00" || $value["BillAmt"] == NULL || $value["BillAmt"] == ''){
            }else{
                $GSTPer = 0.00;
                $GSTPer = $value['igst'] + $value['cgst'] + $value['sgst'];
                $OrderInvAmt = 0;
                if($GSTPer <= 0){
                    foreach ($GSTR1_data['B2BSaleList'] as $key2 => $value2) {
                        if($value2['SalesID'] == $value["TransID"]){
                            $OrderInvAmt = $value2["INVAMT"];
                            $TotalTaxableAmt += $OrderInvAmt;
                        }
                    }
                }
            }
        }
        foreach ($GSTR1_data['B2CLSaleList'] as $key3 => $value3) {
            if($value3["INVAMT"] == "0.00" || $value3["INVAMT"] == NULL || $value3["INVAMT"] == ''){
                
            }else{
                foreach ($GSTR1_data['B2CLhistoryData'] as $key4 => $value4) {
                    if($value3['SalesID'] == $value4['TransID']){
                        $GSTPer = 0.00;
                        $GSTPer = $value4['igst'] + $value4['cgst'] + $value4['sgst'];
                        if($GSTPer <= 0){
                            $NetAmt = $value4["BillAmt"];
                            $TotalTaxableAmt += $NetAmt;
                        }
                    }
                }
            }
        }
        
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) 
        {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer <= 0){
                $NetAmt = $value6['BillAmt'];
                $TotalTaxableAmt += $NetAmt;
            }   
        }
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1.c';
        $list_add[] = 'Other Outward supplies,(Nil rated, exmpted)';
        $list_add[] = number_format($TotalTaxableAmt,2);
        $list_add[] = '0.00';
        $list_add[] = '0.00';
        $list_add[] = '0.00';
        $list_add[] = '0.00';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //3.1.d
    // 3.1(d) – Inward supplies (liable to reverse charge) = Purchases made from UnRegistered suppliers for which you need to create an invoice for yourself to pay the GST.
    $gstr_3_1_d = $this->K1E_Filling_Model->get_data_for_gstr_3_1_d($filterdata);
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1.d';
        $list_add[] = 'Inward supplies(liable to reverse charges)';
        $list_add[] = number_format($gstr_3_1_d["TaxableAmt"],2);
        $list_add[] = number_format($gstr_3_1_d["IAmt"],2);
        $list_add[] = number_format($gstr_3_1_d["CAmt"],2);
        $list_add[] = number_format($gstr_3_1_d["SAmt"],2);
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //3.1.e
        $list_add = [];
        $list_add[] = 'GSTR3B_3.1.e';
        $list_add[] = 'Not GST Outward supplies';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    // 3.2.a Supplies made to Unregistered Persons = Capture Interstate sales to Unregistered Persons.
        $ATotalTaxableAmt = 0;$ATotalCGSTAmt = 0;$ATotalSGSTAmt = 0;$ATotalIGSTAmt = 0;
        $BTotalTaxableAmt = 0;$BTotalCGSTAmt = 0;$BTotalSGSTAmt = 0;$BTotalIGSTAmt = 0;
        foreach ($GSTR1_data['B2CS2'] as $key6 => $value6) {
            $GSTPer = 0.00;
            $GSTPer = $value6['sgst'] + $value6['cgst']+$value6['igst'];
            if($GSTPer > 0){
                $NetAmt = $value6['BillAmt'];
                $TaxableAmt = $NetAmt / (1+($GSTPer/100));
                $GSTAmt = $NetAmt - $TaxableAmt;
                if($value6['state'] != "MH" && $value6['state'] != ""){
                    $ATotalTaxableAmt += $TaxableAmt;
                    $ATotalIGSTAmt += $GSTAmt;
                }elseif($value6['state'] == "MH" || $value6['state'] == ""){
                    $BTotalTaxableAmt += $TaxableAmt;
                    $BTotalSGSTAmt += $GSTAmt/2;
                    $BTotalCGSTAmt += $GSTAmt/2;
                }
            }   
        }
        $list_add = [];
        $list_add[] = 'GSTR3B_3.2';
        $list_add[] = '3.2 of the supplies show in 3.1(a) above, details of the inter-State supplies made to unregistered persons.';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //3.2.a
        $list_add = [];
        $list_add[] = 'GSTR3B_3.2.a';
        $list_add[] = 'Supplies made to unregisterd persons';
        $list_add[] = number_format($ATotalTaxableAmt,2);
        $list_add[] = number_format($ATotalIGSTAmt,2);
        $list_add[] = number_format($ATotalCGSTAmt,2);
        $list_add[] = number_format($ATotalSGSTAmt,2);
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //3.2.b
        $list_add = [];
        $list_add[] = 'GSTR3B_3.2.b';
        $list_add[] = 'Supplies made to Composition Taxable Persons';
        $list_add[] = number_format($BTotalTaxableAmt,2);
        $list_add[] = number_format($BTotalIGSTAmt,2);
        $list_add[] = number_format($BTotalCGSTAmt,2);
        $list_add[] = number_format($BTotalSGSTAmt,2);
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //3.2.c
        $list_add = [];
        $list_add[] = 'GSTR3B_3.2.c';
        $list_add[] = 'Supplies made to UIN Holders';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.A
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A';
        $list_add[] = '(A) ITC Available (whether in full or part)';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.A.1
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A.1';
        $list_add[] = '(1) Import of goods';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.A.2
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A.2';
        $list_add[] = '(2) Import of services';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
        
    //4.A.3
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A.3';
        $list_add[] = '(3) Inward supplies liable to reverse charge (other than 1 & 2 above)';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //4.A.4
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A.4';
        $list_add[] = '(4) Inward supplies from ISD';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    // 4.A.5 (5)All other ITC = ITC from all regular purchases (goods + services + capital goods) excluding imports, RCM, ISD.
    $gstr_4_A_5 = $this->K1E_Filling_Model->get_data_for_gstr_4_A_5($filterdata);
    //4.A.5
        $list_add = [];
        $list_add[] = 'GSTR3B_4.A.5';
        $list_add[] = '(5) All other ITC';
        $list_add[] = number_format($gstr_4_A_5["TaxableAmt"],2);
        $list_add[] = number_format($gstr_4_A_5["IAmt"],2);
        $list_add[] = number_format($gstr_4_A_5["CAmt"],2);
        $list_add[] = number_format($gstr_4_A_5["SAmt"],2);
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.B
        $list_add = [];
        $list_add[] = 'GSTR3B_4.B';
        $list_add[] = '(B) ITC Reversed';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.B.1
        $list_add = [];
        $list_add[] = 'GSTR3B_4.B.1';
        $list_add[] = '(1) As per rules 42 & 43 of CGST Rules';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.B.2
        $list_add = [];
        $list_add[] = 'GSTR3B_4.B.2';
        $list_add[] = '(2) Others';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.C
        $list_add = [];
        $list_add[] = 'GSTR3B_4.C';
        $list_add[] = '(C) Net ITC Available (A) – (B)';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.D
        $list_add = [];
        $list_add[] = 'GSTR3B_4.D';
        $list_add[] = '(D) Ineligible ITC';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //4.D.1
        $list_add = [];
        $list_add[] = 'GSTR3B_4.D.1';
        $list_add[] = '(1) As per section 17(5)';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    //4.D.2
        $list_add = [];
        $list_add[] = 'GSTR3B_4.D.2';
        $list_add[] = '(2) Others';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '0';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //Empty Row
        $list_add = [];
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    //Empty Row
        $list_add = [];
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $list_add[] = '';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
    // Second table
        $list_add = [];
        $list_add[] = 'level';
        $list_add[] = 'Nature of Supplies';
        $list_add[] = 'Inter State Supplies';
        $list_add[] = 'Intra State Supplies';
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    // gstr3B_5.1
        $list_add = [];
        $list_add[] = 'gstr3B_5.1';
        $list_add[] = 'Values of exempt, nil-rated and non-GST inward supplies';
        $list_add[] = number_format($gstr_4_A_5["InterStateTaxableAmt"],2);
        $list_add[] = number_format($gstr_4_A_5["IntraStateTaxableAmt"],2);
        $writer->writeSheetRow('GSTR3B', $list_add);
        
    // gstr3B_5.2
        $list_add = [];
        $list_add[] = 'gstr3B_5.2';
        $list_add[] = 'Non GST supply';
        $list_add[] = '';
        $list_add[] = '';
        $writer->writeSheetRow('GSTR3B', $list_add);
    
        $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
        	foreach($files as $file){
        		if(is_file($file)) {
        			unlink($file); 
        		}
        	}
        $filename = 'K1GSTR3B.xlsx';
        $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
        	echo json_encode([
        			'site_url'          => site_url(),
        			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
        		]);
         die;
    }

}
?>