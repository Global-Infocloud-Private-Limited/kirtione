<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	// $halfA4 = array(210, 148.5); // width x height in mm
// $pdf = new TCPDF('P', 'mm', $halfA4, true, 'UTF-8', false);
// $pdf->AddPage();
	// $pdf->setPageBreak(false);
	$dimensions = $pdf->getPageDimensions();
	
	$pdf->SetMargins(5, 7, 5, 0);
	//$pdf->Ln(0);
	
	$get_order_list = k1get_order_list($invoice->OrderID);
	$count = 0;
	$count_order = count($get_order_list);
	$PartGSTIN = $invoice->PartyGSTIN;
	// print_r($get_order_list);
	$html = '';
	foreach ($get_order_list as $key => $order_detail) {
		
        $client_detail = get_client_detail($order_detail["AccountID"]);
        $client_details2 = get_client_detail($order_detail["AccountID"]);
        $FY = $order_detail["FY"];
        $PlantDetail = GetPlantDetails($order_detail["PlantID"],$order_detail["FY"]);
        $gst_type = get_gst_type();
        $sales_detail = K1get_sales_details($invoice->ChallanID,$order_detail["OrderID"]);
		// print_r('iokok');die;
        $PlantID = $sales_detail->PlantID;
        $state_detail = get_state_detail($client_detail->state);
        $billing_state_detail = get_state_detail($client_detail->billing_state);
        $shipping_state_detail = get_state_detail($client_detail->shipping_state);
        
		if(!empty($sales_detail->ShippingID)){
		$addressdata = K1get_shipping_details($sales_detail->ShippingID);
		$deliveryAdd = $addressdata->address;
		}else{
		$deliveryAdd = $client_detail->VendorAddress;
		}
		
        $qty = 0;
        $amt = 0;
        $dis_amt = 0;
        $taxable_amt_item = 0;
        $order_total = 0;
        
        $title = "";
        // if($order_detail["OrderType"] == "TaxItems"){
		// }
            $title = "INVOICE";
        // if($order_detail["OrderType"] == "NonTaxItems"){
            // $title = "BILL OF SUPPLY";
		// }
		$orderType='';
        if($order_detail["OrderPaymentType"] == "1"){
            $orderType = "Cash Order";
		}
        if($order_detail["OrderPaymentType"] == "2"){
            $orderType = "Credit Order";
		}
        $pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 1));
        //$html .= '<div class="page-break-after: always;">';
		
        $html .= '<p style="text-align:center;font-size:14px; padding-bottom:0px;"><b>TAX INVOICE</b></p>'; 
        $html .= '<table style="width: 100%; font-size:12px;font-weight:400; padding-top:0px;margin-top:0px;" cellspacing="1" cellpadding="3" border="1" >';
		
        $html .= '<thead>';
        $html .= '<tr >
        <th colspan="4" style="width: 33.40%;"><p ><span style="text-align:left;font-size:14px;"><b>'.$PlantDetail->FIRMNAME.'</b></span><br><span style="text-align:left;font-size:13px;"><b>'.$order_detail["CenterName"].'</b></span><br><span style="text-align:left;font-size:12px;">'.$order_detail["CenterAddress"].'<br><b>GSTIN : </b>'.$order_detail["CenterGst"].' <br><b>Contact No. : </b>'.$order_detail["CenterMobile"].'</span></p></th>';
		
        $html .= '<th colspan="4" style="width: 33.40%;">';
        $html .= '<p style="text-align:left;font-size:12px;"><b>Invoice No : </b>'.$sales_detail->SalesID.'<br><b>Date : </b>'. _d(substr($sales_detail->Transdate,0,10)) .'<br>';
        $html .= '<b>Order No. :</b> '.$sales_detail->OrderID.'<br><b>Vehicle No : </b>'.$sales_detail->vehicleno.'<br>';
        if($sales_detail->DeliveryType == "2"){
            $DeliveryType = "Home Delivery";
        }else{
            $DeliveryType = "Pick-Up";
        }
        $html .= '<b>Delivery Type :</b> '.$DeliveryType.'<br>';
        if($sales_detail->DeliveryType == "2"){
            $html .= '<b>E-Way No. :</b> '.$sales_detail->ewaybill_no.'<br><b>E-Way Date :</b> '.$sales_detail->ewaybill_date .'';
        }
        
		$html .= '</p></th>';
        $html .= '<th colspan="4" style="width: 32.40%;"><p style="text-align:left;font-size:12px;"><b>To : </b>'.$client_details2->company.'<br>'.$deliveryAdd.'<br><b>GSTIN : </b>'.$PartGSTIN.'<br></p></th>
        </tr>';
		
		
        
        
        $item_name_width = "22%";
        $hsn_width = "7%";
        $html .= '<tr>
        <th width="3.6%"  style="text-align:center;"><b>Sr.No.</b></th>
        <th width="'.$item_name_width.'"><b>Name of Product</b></th>
        <th width="'.$hsn_width.'" ><b>HSN Code</b></th>
        <th width="8%" style="text-align:center;"><b>Brand</b></th>
        <th width="7%"><b>Batch No.</b></th>
        <th width="8%"><b>Exp Date</b></th>
        <th width="5%" style="text-align:center;"><b>Pack Qty</b></th>
        <th width="4.5%" style="text-align:center;"><b>Qty</b></th>
        <th width="6%" style="text-align:center;"><b>Rate</b></th>
        <th width="6%" style="text-align:center;"><b>Dis Amt</b></th>
        <th width="8%" style="text-align:center;"><b>Taxable Amount</b></th>
		<th style="text-align:center;" width="5%" ><b>GST %</b></th>
		<th style="text-align:center;"  width="9%"><b>Amount</b></th>
		';
		
		
		//$html .= '<td '.$rowspan.' style="text-align:center;">Total Amt</td>';    
		$html .= '</tr>';
        
        $html .= '</thead>';
        $html .= '<tbody>';
        
        
		$inv_item = GetItemDetailsFrK1Gatepass($order_detail["OrderID"]);
		// echo "</pre>";print_r($inv_item);die;
		$i = 1;
		$total_item_count = count($inv_item);
		
		if($total_item_count <= 13 ){
			$empty_height = 330;
		}
		
		if($total_item_count > 13 && $total_item_count<=33){
			$empty_height = 700;
			$empty_height1 = 340;
		}
		if($total_item_count > 33 ){
			$empty_height = 340;
		}
		
		$qty = 0;
		$units = 0;
		$amt = 0;
		$dis_amt = 0;
		$taxable_amt_item = 0;
		$order_total = 0;
		foreach ($inv_item as $item) {
			$hsn_code = get_prod_hsn_byitem_id($item['ItemID']);
			if($total_item_count <= 13 ){
				$empty_height = $empty_height - 23;
			}
			if($total_item_count > 13 && $total_item_count<=33){
				$empty_height = $empty_height - 22;
			}
			if($total_item_count > 13 && $total_item_count<=33 && $i > 33){
				$empty_height1 = $empty_height1 - 22;
			}
			if($total_item_count > 33 && $i > 33){
				$empty_height = $empty_height - 22;
			}
			$html .= '<tr>'; 
			$html .= '<td width="3.6%" style="text-align:center;">'.$i.'</td>'; 
			$html .= '<td width="'.$item_name_width.'" class="description" align="left;" width="'.$item_name_width.'">'.$item['ProductName'].'</td>';
			$html .= '<td width="'.$hsn_width.'" style="text-align:center;">'.$hsn_code->hsn_code.'</td>';
			$html .= '<td width="8%" style="text-align:left;">'. $item['BrandName'].'</td>';
			$html .= '<td width="7%" style="text-align:left;">'. $item['BatchNo'].'</td>';
			$html .= '<td width="8%" style="text-align:center;">'. _d(substr($item['ExpDate'],0,10)).'</td>';
			$html .= '<td width="5%" style="text-align:center;">'. (int) $item['CaseQty'].'</td>';
			
			$html .= '<td width="4.5%" style="text-align:right;">'. (int) $item['OrderQty'].'</td>';
			$units = $units + $item['OrderQty'];
            $qty = $qty + (int) $item['CaseQty'];
			$html .= '<td width="6%" style="text-align:right;">'.number_format($item['BasicRate'], 2, '.', '').'</td>';
			$amt = $amt + $item['ChallanAmt'];
			$html .= '<td width="6%" style="text-align:right;">'.round($item['DiscAmt'],2) .'</td>';
			$dis_amt = $dis_amt + $item['DiscAmt'];
			if($client_detail->state == "MH"){
				$gst_rate = $item['cgst'] + $item['sgst'];
				$gst_rate = $gst_rate.".00";
				$scgst = $item['cgstamt'] * 2;
				$gst_total = $gst_total + $scgst;
			}else {
				$gst_rate = $item['igst'];
				$gst_total = $gst_total + $item['igstamt'];
			}
			$TaxableAmt = ($item['NetChallanAmt'] / (1 + ($gst_rate/100)));
			$html .= '<td width="8%" style="text-align:right;">'.number_format($TaxableAmt, 2, '.', '').'</td>';
			
			$taxable_amt_item = $taxable_amt_item + $TaxableAmt;
			
			
			
			
			
			$html .= '<td width="5%" style="text-align:center;">'.$gst_rate.'</td>';
			$html .= '<td width="9%" style="text-align:right;">'.$item['NetChallanAmt'].'</td>';
			$order_total = $order_total + $item['NetChallanAmt'];
			$html .= '</tr>';
			
			$i++;
		}
		$amt = (double) $amt;
		
		if(!empty($inv_item)){
			$html .='<tr>';
			$html .='<td colspan="2" style="text-align:center;"><b>Total</b></td>'; 
			$html .='<td style="text-align:center;"></td>';
			$html .='<td style="text-align:center;"></td>';
			$html .='<td style="text-align:center;"></td>';
			$html .='<td></td>';
			$html .='<td style="text-align:right;"><b>'.$qty.'</b></td>';
			$html .='<td style="text-align:right;"><b>'.$units.'</b></td>';
			$html .='<td style="text-align:right;"><b></b></td>';
			$html .='<td style="text-align:right;"><b>'.round($dis_amt,2).'</b></td>';
			$html .='<td style="text-align:right;"><b>'.number_format($taxable_amt_item, 2, '.', '').'</b></td>';
			
			$html .='<td style="text-align:center;"><b></b></td>'; 
			$html .='<td style="text-align:right;"><b>'.number_format($order_total, 2, '.', '').'</b></td>';
			$html .='</tr>';
		}
		
		//if($total_item_count > 17 && $total_item_count <=33){
        $html .='<tr><td colspan="12" width="99.3%" height="'.$empty_height.'px"></td></tr>';
        if($total_item_count > 13 && $total_item_count<=33){
			$html .='<tr><td colspan="12" height="'.$empty_height1.'px"></td></tr>';
		}
		
		
        $html .= '</tbody>';
        
        $html .= '<tfoot style="width:100%;position:fixed !important;bottom:0 !important">';
		
		$html .='<tr>
		<td colspan="2" width="20%" style="text-align:center;"><b>GST Breakup</b></td>
		<td width="6%" style="text-align:center;"><b>GST %'.'</b></td>
		<td width="13.2%" style="text-align:center;"><b>Taxable Amt</b></td>
		<td width="7%" style="text-align:center;"><b>CGST %</b></td>
		<td width="9%" style="text-align:center;"><b>CGST Amt</b></td>
		<td width="7%" style="text-align:center;"><b>SGST %</b></td>
		<td width="9%" style="text-align:center;"><b>SGST Amt</b></td>
		<td width="6%" style="text-align:center;"><b>IGST %</b></td>
		<td width="8%" style="text-align:center;"><b>IGST Amt</b></td>
		<td width="9%" style="text-align:center;"><b>GST Amt</b></td>
		<td width="5%" style="text-align:center;"><b>Item </b></td>
		
		</tr>';
		$TotalTaxableAmt = 0;
		$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;
		if($client_detail->state == "MH"){
			$gst_detail = get_k1gst_details($order_detail["OrderID"]);
			
			$gst_count = count($gst_detail);
			$bill_gst_total = 0.00;
			$i = 0;
			if($gst_count == "1"){
				$gst_brk_after_space_h = 22;
				}if($gst_count == "2"){
				$gst_brk_after_space_h = 0;
			}
			if($gst_count == "3"){
				$gst_brk_after_space_h = 0;
			}
			foreach ($gst_detail as $gvalue) {
                $html .='<tr>';
                if($i == 0){
                    $html .='<td rowspan="'.$gst_count.'" colspan="2" width="20%"></td>';
				}
                $gst_per = $gvalue["cgst"] * 2;
                $gst_per = $gst_per;
                $taxable_amt = get_k1gst_taxable_amt($order_detail["OrderID"],$gvalue["cgst"]);
                $cs_gst_amt = get_k1gst_amt($order_detail["OrderID"],$gvalue["cgst"]);
                $gst_total_amt = $cs_gst_amt * 2;
                $item_count = get_k1gst_item_count($order_detail["OrderID"],$gvalue["cgst"]);
                $item_count_new = count($item_count);
                //$TaxableAmt = ($taxable_amt / (1 + ($gst_per/100)));
                $TaxableAmt = $taxable_amt;
                $TotalTaxableAmt += $TaxableAmt;
                //$GSTAmt = $taxable_amt - $TaxableAmt;
                $GSTAmt = $gst_total_amt;
                $TotalCGSTAmt += $GSTAmt/2;
                $TotalSGSTAmt += $GSTAmt/2;
                $html .='<td width="6%" style="text-align:center;">'.$gst_per.'.00</td>
                
                <td width="13.2%" style="text-align:center;">'.number_format($TaxableAmt, 2, '.', '').'</td>
                <td width="7%" style="text-align:center;">'.number_format($gvalue["cgst"], 2, '.', '').'</td>
                <td width="9%" style="text-align:center;">'.number_format($GSTAmt/2, 2, '.', '').'</td>
                <td width="7%" style="text-align:center;">'.number_format($gvalue["cgst"], 2, '.', '').'</td>
                <td width="9%" style="text-align:center;">'.number_format($GSTAmt/2, 2, '.', '').'</td>
                <td width="6%" style="text-align:center;"></td>
                <td width="8%" style="text-align:center;"></td>
                <td width="9%" style="text-align:center;">'. number_format($GSTAmt, 2, '.', '').'</td>
                <td width="5%" style="text-align:center;">'.$item_count_new .'</td>
                
                </tr>';
                $bill_gst_total = $bill_gst_total + $gst_total_amt;
				$i++;
			}
		}else {
            $igst_detail = get_k1igst_details($order_detail["OrderID"]);
			
            $igst_count = count($igst_detail);
            $i = 0;
            
			foreach ($igst_detail as $igvalue) {
                # code...
                
                $html .='<tr>';
                if($i == 0){
                    $html .='<td rowspan="'.$igst_count.'" colspan="2" width="20%"></td>';
				}
                $igst_per = $igvalue["igst"];
                $igst_per = $igst_per;
                $taxable_amt = get_k1igst_taxable_amt($order_detail["OrderID"],$igvalue["igst"]);
                $i_gst_amt = get_k1igst_amt($order_detail["OrderID"],$igvalue["igst"]);
                $i_item_count = get_k1igst_item_count($order_detail["OrderID"],$igvalue["igst"]);
                $i_item_count_new = count($i_item_count);
                //$TaxableAmt = ($taxable_amt / (1 + ($igst_per/100)));
                $TaxableAmt = $taxable_amt;
                $TotalTaxableAmt += $TaxableAmt;
                //$GSTAmt = $taxable_amt - $TaxableAmt;
                $GSTAmt = $i_gst_amt;
                $TotalIGSTAmt += $GSTAmt;
                $html .='<td width="6%" style="text-align:center;">'.$igst_per.'</td>
                <td width="13.2%" style="text-align:center;">'.number_format($TaxableAmt, 2, '.', '').'</td>
                <td width="7%" style="text-align:center;"></td>
                <td width="9%" style="text-align:center;"></td>
                <td width="7%" style="text-align:center;"></td>
                <td width="9%" style="text-align:center;"></td>
                <td width="6%" style="text-align:center;">'.$igvalue["igst"].'</td>
                <td width="8%" style="text-align:center;">'. number_format($GSTAmt, 2, '.', '').'</td>
                <td width="9%" style="text-align:center;">'. number_format($GSTAmt, 2, '.', '').'</td>
                <td width="5%" style="text-align:center;">'.$i_item_count_new.'</td>
                
                </tr>';
                $bill_gst_total = $bill_gst_total + $GSTAmt;
				$i++;
			}  
		}
        if($gst_count>1){}else{
            $html .='<tr><td colspan="12" style="height:'.$gst_brk_after_space_h.'px;"></td></tr>';
		}
        
        
		$html .='<tr>'; 
		$html .='<td colspan="8" style="border-right:none;" width="60%"></td>';
		
		
		$html .='<td colspan="3" width="25%">Taxable Value/ Amt</td>';
		$html .='<td  style="text-align:right;" width="14.3%">'. number_format($TotalTaxableAmt, 2, '.', '').'</td>';
		$html .='</tr>'; 
		
		
		$html .='<tr>'; 
		if($client_detail->state == "MH"){
			$bank_rowspan='rowspan="7"';
			}else {
			$bank_rowspan='rowspan="6"';
		}
		if($PlantID == "1"){
		    $terms = base_url()."Terms/terms.jpeg";
		$isNonGroc = "none";
		if($order_detail["CategoryType"] == "Non Grocery"){
			$isNonGroc = "block";
		}
			$BankMsg = 'Bank A/c Details - KIRTI AGRI SOLUTIONS PRIVATE LIMITED<br>1. Central Bank of India - A/C - 5543709295, IFSC-CBIN0280682, Latur Branch, Latur <br>2. UPI Details - 11715519@cbin<br><br>
			<span style="display:'.$isNonGroc.';font-size:10px;" ><img src="'.$terms.'" title="Link to Google.com" /></span>';
		}else{
			$BankMsg = '';
		}
		$html .='<td colspan="8" '.$bank_rowspan.'>'.$BankMsg.'</td>';
		if($client_detail->state == "MH"){
			$html .='<td colspan="3">Add CGST</td>';
			$grand_csgst = $gst_total / 2;
			$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format($TotalCGSTAmt, 2, '.', '').'</td>';
		}else {
			$html .='<td colspan="3">Add IGST</td>';
			$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format($TotalIGSTAmt, 2, '.', '').'</td>';
		}
		
		$html .='</tr>'; 
		
		if($client_detail->state == "MH"){
			$html .='<tr>'; 
			$html .='<td colspan="3">Add SGST</td>';
			$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format($TotalSGSTAmt, 2, '.', '').'</td>';
			$html .='</tr>'; 
		}
		$sale_data = get_k1_is_tcs($order_detail["SalesID"]);
		 //print_r($sale_data);die;
		$html .='<tr>'; 
		$html .='<td colspan="3">Add TCS @ '.round($sale_data->tcs,2).'%</td>';
		$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format($sale_data->tcsAmt, 2, '.', '').'</td>';
		$html .='</tr>'; 
		$html .='<tr>'; 
		$html .='<td colspan="3">Discount</td>';
		$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format($sales_detail->DiscAmt, 2, '.', '').'</td>';
		$html .='</tr>'; 
		$html .='<tr>'; 
		$html .='<td colspan="3">Amount after GST</td>';
		$tcs_amt = $sale_data->tcsAmt;
		$inc_tcs_amt = $order_total + $tcs_amt;
		$TotalAmt = $bill_gst_total + $taxable_amt_item;
		$TotamInvAmt = $TotalTaxableAmt + $TotalSGSTAmt + $TotalCGSTAmt + $TotalIGSTAmt - $sales_detail->DiscAmt;
		$html .='<td '.$colspan_taxable_amt.' style="text-align:right;">'.number_format(round($TotamInvAmt), 2, '.', '').'</td>';
		$html .='</tr>'; 
		
		$html .='<tr>'; 
		$html .='<td colspan="3">Previous Balance</td>';
		$html .='<td '.$colspan_taxable_amt.'></td>';
		$html .='</tr>';
		$html .='<tr>'; 
		$html .='<td colspan="3">Balance Amt (Rnd)</td>';
		$html .='<td '.$colspan_taxable_amt.' style="text-align:right;"></td>';
		$html .='</tr>';
		$html .='<tr>'; 
		
		$src= 'https://chart.googleapis.com/chart?chs=115x115&cht=qr&chl='.$sales_detail->Qrcode.'&choe=UTF-8';
		// $html .='<td colspan="8">';
		// if($sales_detail->irn !== null){
			// $html .='<img src="'.$src.'" title="Link to Google.com" /><br><b>IRN '.$sales_detail->irn.'</b>';
		// }
		// $html .='</td>';
		$html .='<td colspan="4"><p style="text-align:left;display:'.$isNonGroc.'"><b>Ferti.Lic. No. : </b>'.$order_detail["Fertikizers"].'<br><b>Pesti.Lic. No. : </b>'.$order_detail["Insecticides"].'<br><b>Seeds Lic. No. : </b>'.$order_detail["Seeds"].'<br><b>Cotton Lic. No. : </b>'.$order_detail["Cotton"].'</p></td>';
		$html .='<td colspan="4"><p style="text-align:left;"><br><br><br><br><span style="text-align:center;">Receivers Signature</span></p></td>';
		$html .='<td colspan="4">For : <b> '.$PlantDetail->FIRMNAME.'</b><br><br><br><br>Authorized Signatory</td>';
		$html .='</tr>';
		
        $html .= '</tfoot>';
        $html .= '</table>';
		$html .= '<p style="text-align:center;font-size:14px;">SUBJECT TO LATUR JURISDICTION</p>';
        //$html .= '</div>';
		
	}
	
	
	$pdf->writeHTML($html, true, false, false, false, '');
?>