<?php

defined('BASEPATH') or exit('No direct script access allowed');


$dimensions = $pdf->getPageDimensions();
$PlantDetail = $invoice['RootCompany'];
$InvoiceDetail = $invoice['InvoiceDetails'];
$BookingDetails = $invoice['BookingDetails'];
$CompanyDetails = $invoice['CompDetails'];

$pdf->SetMargins(5, 7, 5, 0);
$title = "TAX INVOICE";
$html = '';
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 1));
    $html .= '<table style="width: 100%; font-size:12px;font-weight:400;" cellspacing="1" cellpadding="3" border="1" >';
    $html .= '<thead>';
    $html .= '<tr >
        <th colspan="4" style="border: 1px solid #333;"><p style="text-align:center;font-size:14px;"><b>'.$title.'</b><br><b>'.$PlantDetail->FIRMNAME.'</b><br><b>'.$PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2.'<br></b><b>GSTIN '.$PlantDetail->GSTNO.', <i>fssai</i> Lic.no '.$PlantDetail->FLNO1.' </b><br><b>Contact No. : '.$PlantDetail->PHONENO.'</b></p></th>
        </tr>';
    $html .= '<tr>
        <th style="border-left: 1px solid #333;" width="20%">Invoice No.</th>
        <th style="border-right: 1px solid #333;" width="30%"><b>'.$InvoiceDetail->TransID.'</b></th>
        <th width="20%" style="border-left: 1px solid #333;">Ack No.</th>
        <th style="border-right: 1px solid #333;" width="30%"><b>'.$InvoiceDetail->AckNo.'</b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;" width="20%">Invoice Date</th>
        <th style="border-right: 1px solid #333;" width="30%"><b>'. _d(substr($InvoiceDetail->TransDate,0,10)) .'</b></th>
        <th width="20%" style="border-left: 1px solid #333;">Ack Date</th>
        <th style="border-right: 1px solid #333;" width="30%"><b>'.$InvoiceDetail->AckDate.'</b></th>
        </tr>';
        
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-bottom: 1px solid #333;" width="20%">Booking ID</th>
        <th style="border-right: 1px solid #333;border-bottom: 1px solid #333;" width="30%"><b>'. $InvoiceDetail->BookingID .'</b></th>
        <th width="20%" style="border-bottom: 1px solid #333;border-left: 1px solid #333;"></th>
        <th style="border-right: 1px solid #333;border-bottom: 1px solid #333;" width="30%"><b></b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2"><b>Bill To</b></th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2"><b>Ship To</b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2"><b>'.$CompanyDetails->PlantName.'</b></th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2"><b>'.$CompanyDetails->PlantName.'</b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2">'.$CompanyDetails->address.', '.$CompanyDetails->TalukaName.'</th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2">'.$CompanyDetails->address.', '.$CompanyDetails->TalukaName.'</th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2">'.$CompanyDetails->city_name.' '.$CompanyDetails->state_name.' - '.$CompanyDetails->pincode.'</th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2">'.$CompanyDetails->city_name.' '.$CompanyDetails->state_name.' - '.$CompanyDetails->pincode.'</th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2">GSTIN  <b>'.$CompanyDetails->GstNo.'</b></th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2">GSTIN  <b>'.$CompanyDetails->GstNo.'</b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;" width="50%" colspan="2">Contact Number  <b>'.$CompanyDetails->contact_number.'</b></th>
        <th style="border-right: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2">Contact Number   <b>'.$CompanyDetails->contact_number.'</b></th>
        </tr>';
        
        $html .= '<tr>
        <th style="border-left: 1px solid #333;border-right: 1px solid #333;border-bottom: 1px solid #333;" width="50%" colspan="2">Food Lic No  <b>'.$CompanyDetails->FLNO1.'</b></th>
        <th style="border-right: 1px solid #333;border-bottom: 1px solid #333;border-left: 1px solid #333;" width="50%" colspan="2">Food Lic No  <b>'.$CompanyDetails->FLNO1.'</b></th>
        </tr>';
        
        $rowspan = 'rowspan="2"';
        $item_name_width = "34%";
        $hsn_width = "7%";
        
        $html .= '<tr>
        <th width="3.6%" '.$rowspan.' style="text-align:center;"><b>Sr. No.</b></th>
        <th width="'.$item_name_width.'" '.$rowspan.' colspan="2" style="text-align:center;"><b>Particulars</b></th>
        <th width="'.$hsn_width.'" '.$rowspan.'><b>HSN</b></th>
        <th width="6%" '.$rowspan.' style="text-align:center;"><b>Qty.</b></th>
        <th width="6.5%" '.$rowspan.' style="text-align:center;"><b>Units</b></th>
        <th width="5%" '.$rowspan.' style="text-align:center;"><b>Rate</b></th>
        <th width="8%" '.$rowspan.' style="text-align:center;"><b>Amount</b></th>
        <th width="6%" '.$rowspan.' style="text-align:center;"><b>Disc Amount</b></th>
        <th width="8%" '.$rowspan.' style="text-align:center;"><b>Taxable Amount</b></th>';
    
    $html .= '<th style="text-align:center;" width="6%"><b>GST</b></th>';
    $html .= '<th style="text-align:center;" width="9%"><b>Total</b></th>';
    //$html .= '<td '.$rowspan.' style="text-align:center;">Total Amt</td>';    
    $html .= '</tr>';
    $html .= '<tr>
        <th style="text-align:center;"><b>%</b></th>
        <th style="text-align:center;"><b>Amount</b></th>
        </tr>';
        
    $html .= '<thead>';
    $html .= '<tbody>';
    
        $html .= '<tr>'; 
        $html .= '<td width="3.6%" style="text-align:center;">1</td>'; 
           $html .= '<td width="'.$item_name_width.'" class="description" align="left;" width="'.$item_name_width.'" colspan="2"><b>E-Market Services- Private Auctions</b></td>';
           $html .= '<td width="'.$hsn_width.'" style="text-align:center;"><b>998599</b></td>';
           
           $html .= '<td width="6%" style="text-align:right;"><b>'. $InvoiceDetail->InwardWT.'</b></td>';
           $html .= '<td width="6.5%" style="text-align:right;"><b>Qtls</b></td>';
           $html .= '<td width="5%" style="text-align:right;"><b>'.number_format($InvoiceDetail->Rate, 2, '.', '').'</b></td>';
           $html .= '<td width="8%" style="text-align:right;"><b>'.number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>';
           $html .= '<td width="6%" style="text-align:right;"><b>0.00</b></td>';
           $html .= '<td width="8%" style="text-align:right;"><b>'.number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>';
           
           $gst_rate = $InvoiceDetail->cgst + $InvoiceDetail->sgst + $InvoiceDetail->igst;
           $html .= '<td width="6%" style="text-align:center;"><b>'.number_format($gst_rate, 2, '.', '').'</b></td>';
           $html .= '<td width="9%" style="text-align:right;"><b>'.number_format($InvoiceDetail->InvoiceAmt, 2, '.', '').'</b></td>';
           $html .= '</tr>';
           
        $html .='<tr><td colspan="12" height="200px"></td></tr>';
        
        $html .='<tr>';
        $html .='<td colspan="3" style="text-align:center;"><b>Total</b></td>'; 
        $html .='<td style="text-align:center;"></td>';
        $html .='<td style="text-align:right;"><b>'.$InvoiceDetail->InwardWT.'</b></td>';
        $html .='<td style="text-align:right;"><b></b></td>';
        $html .='<td></td>';
        $html .='<td style="text-align:right;"><b>'.number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>';
        $html .='<td style="text-align:right;"><b>0.00</b></td>';
        $html .='<td style="text-align:right;"><b>'.number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>';
        
        $html .='<td style="text-align:center;"><b></b></td>'; 
        $html .='<td style="text-align:right;"><b>'.number_format($InvoiceDetail->InvoiceAmt, 2, '.', '').'</b></td>';
        $html .='</tr>';
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $InvoiceWord =  $f->format($InvoiceDetail->InvoiceAmt);
        $html .='<tr><td colspan="12" height="30px"></td></tr>';
        
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
    $html .='<tr>';
    $totalGst = $InvoiceDetail->cgstAmt + $InvoiceDetail->sgstAmt + $InvoiceDetail->igstAmt;
    $html .='<td colspan="2" width="20%"></td>
        <td width="6%" style="text-align:center;"><b>'.number_format($gst_rate, 2, '.', '').'</b></td>
        <td width="13.2%" style="text-align:center;"><b>'.number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>
                <td width="7%" style="text-align:center;"><b>'.number_format($InvoiceDetail->cgst, 2, '.', '').'</b></td>
                <td width="9%" style="text-align:center;"><b>'.number_format($InvoiceDetail->cgstAmt, 2, '.', '').'</b></td>
                <td width="7%" style="text-align:center;"><b>'.number_format($InvoiceDetail->sgst, 2, '.', '').'</b></td>
                <td width="9%" style="text-align:center;"><b>'.number_format($InvoiceDetail->sgstAmt, 2, '.', '').'</b></td>
                <td width="6%" style="text-align:center;"><b>'.number_format($InvoiceDetail->igst, 2, '.', '').'</b></td>
                <td width="8%" style="text-align:center;"><b>'.number_format($InvoiceDetail->igstAmt, 2, '.', '').'</b></td>
                <td width="9%" style="text-align:center;"><b>'. number_format($totalGst, 2, '.', '').'</b></td>
                <td width="5%" style="text-align:center;"><b>1</b></td>
                
                </tr>';
    
    $html .='<tr>
    <td colspan="12" height="50px" >Amount Chargeable (in words)<br><b><span style="font-size:16px" >INR '.ucfirst($InvoiceWord).' Only</span></b></td>
    </tr>';
    
    $html .='<tr>'; 
    if($CompanyDetails->state == "MH"){
        $bank_rowspan='rowspan="4"';
    }else {
        $bank_rowspan='rowspan="3"';
    }
    $BankMsg = '<b>Bank A/c Details :<br> KIRTI AGREEVENTURE PVT. LTD.<br>1. STATE BANK OF INDIA - A/C - 30634015673, IFSC-SBIN0000086, Bank Road, Latur <br>2. PUNJAB NATIONAL BANK - A/C - 1875002100047113, IFSC-PUNB0187500, Jubilee Road, Latur<br>3. UPI Details - 9415212586@kotak</b>';
    
    $html .='<td colspan="7" '.$bank_rowspan.'>'.$BankMsg.'</td>';
    
    $html .='<td colspan="3"><b>Taxable Value/ Amt</b></td>';
    $html .='<td  style="text-align:right;" colspan="2"><b>'. number_format($InvoiceDetail->Amount, 2, '.', '').'</b></td>';
    
    $html .='</tr>'; 
    
    $html .='<tr>'; 
    if($CompanyDetails->state == "MH"){
        $html .='<td colspan="3"><b>Add CGST</b></td>';
        $grand_csgst = $gst_total / 2;
        $html .='<td colspan="2" style="text-align:right;"><b>'.number_format($InvoiceDetail->cgstAmt, 2, '.', '').'</b></td>';
    }else {
        $html .='<td colspan="3"><b>Add IGST</b></td>';
        $html .='<td colspan="2" style="text-align:right;"><b>'.number_format($InvoiceDetail->igstAmt, 2, '.', '').'</b></td>';
    }
    $html .='</tr>'; 
    
    if($CompanyDetails->state == "MH"){
    $html .='<tr>'; 
    $html .='<td colspan="3"><b>Add SGST</b></td>';
    $html .='<td colspan="2" style="text-align:right;"><b>'.number_format($InvoiceDetail->cgstAmt, 2, '.', '').'</b></td>';
    $html .='</tr>'; 
    }
    
    $html .='<tr>'; 
    $html .='<td colspan="3"><b>Amount after GST</b></td>';
    $html .='<td colspan="2" style="text-align:right;"><b>'.number_format($InvoiceDetail->InvoiceAmt, 2, '.', '').'</b></td>';
    $html .='</tr>'; 
    
    /*$html .='<tr>'; 
    $html .='<td colspan="3"><b>Previous Balance</b></td>';
    $html .='<td colspan="2"></td>';
    $html .='</tr>';
    $html .='<tr>'; 
    $html .='<td colspan="3"><b>Balance Amt (Rnd)</b></td>';
    $html .='<td colspan="2" style="text-align:right;"></td>';
    $html .='</tr>';*/
    $html .='<tr>'; 
    
    $src= 'https://chart.googleapis.com/chart?chs=115x115&cht=qr&chl='.$InvoiceDetail->QRCode.'&choe=UTF-8';
    $html .='<td colspan="7">';
    if($sales_detail->irn !== null){
    $html .='<img src="'.$src.'" title="Link to Google.com" /><br><b>IRN '.$InvoiceDetail->IRN.'</b>';
    }
    $html .='</td>';
    $html .='<td colspan="5">For<b> '.$PlantDetail->FIRMNAME.'<br><br><br><br><br><br><br>Authorized Signatory</b></td>';
    $html .='</tr>';
        
    $html .= '</tbody>';
    $html .= '<tfoot>';
    $html .= '</tfoot>';
    $html .= '</table>';
$pdf->writeHTML($html, true, false, false, false, '');
?>