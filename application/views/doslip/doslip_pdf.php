<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle($GateDetails->BookingID);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 9);
$pdf->setJPEGQuality(75);
// add a page
$pdf->AddPage();

$CompanyName = $DODetails->PlantName;
$CompanyAddress = $DODetails->address.", Tq : ".$DODetails->TalukaName.", Dist : ".$DODetails->city_name." (".$DODetails->state.") - ".$DODetails->pincode;
$CompanyGST = $DODetails->GstNo;
$fssaiNo = $DODetails->fssai_no;
$CenterName = $DODetails->CenterName;
$CenterAddress = $DODetails->CenterAddress;
$CState = $DODetails->CState;
$CCityName = $DODetails->CCityName;
$CTalukaName = $DODetails->CTalukaName;
$html1 = "";

    $html1 .='<table style="width: 100%;border:1px solid #333; font-size:10px;font-weight:400;" cellspacing="1" cellpadding="3">';
    // Company Information
    $html1 .= '<thead>';
        
        $html1 .='<tr>';
        $html1 .='<td><img src="' . site_url() . '/uploads/company/a093e544716efb366a062b996f0ca635.png"></td>
        <td style="" colspan="12"><b style="width: 100%;text-align:center; font-size:18px;font-weight:700;">'.$CompanyName.'</b><br><span style="width: 100%;text-align:center; font-size:10px;">'.$CompanyAddress.'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>Del. Loc:</b> '.$CenterName. ", " .$CenterAddress . ', Tq. : '.$CTalukaName.', Dist : '.$CCityName.' - '.$CState.'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>GSTIN:</b> '.$CompanyGST.' , <b>FSSAI No: </b> ' . $fssaiNo . '</span></td>';
        $html1 .='</tr>';
    
    
    $html1 .='<tr>';
            $html1 .='<th colspan="2" style="width:10%;border-bottom: 1px solid #333;border-top: 1px solid #333;" rowspan="2"><b>Party Name</b></th>';
            $html1 .='<th colspan="2" style="width:28%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" rowspan="2">: '.$DODetails->company.'</th>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->BookingID.'</td>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade Date</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '._d($DODetails->BookingDate).'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>ASN ID</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->ASNID.'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>ASN Date</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '._d($DODetails->ASNDate).'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Party GST</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->vat.'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>DO No</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->DOID.'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>DO Date</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '._d(substr($DODetails->DODate,0,19)).'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="3" style="border-bottom: 1px solid #333;"><b>Party Representative</b></td>';
            $html1 .='<td colspan="4" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->SalesRepName.'</td>';
            $html1 .='<td colspan="3" style="border-bottom: 1px solid #333;"><b>Party Rep. Mobile</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '.$DODetails->SalesRepMobile.'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="3" style="border-bottom: 1px solid #333;"><b>DO Generated By</b></td>';
            $html1 .='<td colspan="9" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$DODetails->firstname." ".$DODetails->lastname.'</td>';
            
            $html1 .='</tr>';
            
    $html1 .= '</thead>';
    $html1 .= '<tbody>';
    $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center; font-size:12px;font-weight:700;background-color:#BEBEBE;border-bottom: 1px solid #333;" colspan="12"><b>Delivery Order Details</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Sr.No</b></td>';
        $html1 .='<td colspan="5" style="width:25%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Item Name</b></td>';
        $html1 .='<td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>HSN</b></td>';
        $html1 .='<td style="width:13%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>DO Wt.(MT)</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Trade Rate(MT)</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Basic Value</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>GST Amt</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;text-align:center;"><b>DO Amt</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="border-right: 1px solid #333;">1</td>';
        $html1 .='<td colspan="5" style="border-right: 1px solid #333;">'.$DODetails->ItemName.'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:center;">'.$DODetails->hsn_code.'</td>';
        
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($DODetails->Asn_WT_MT, 2, '.', '').'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($DODetails->basic_rate * 10 , 2, '.', '').'</td>';
        $PurchaseValue = ($DODetails->basic_rate * 10) * $DODetails->Asn_WT_MT;
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($PurchaseValue, 2, '.', '').'</td>';
        if($DODetails->taxrate){
            $taxrate = $DODetails->taxrate;
        }else{
            $taxrate = 0;
        }
        $GstAmt = $PurchaseValue * ($taxrate / 100);
        $NetPurchaseAmt = $PurchaseValue + $GstAmt;
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($GstAmt, 2, '.', '').'</td>';
        $html1 .='<td style="text-align:right;">'.number_format($NetPurchaseAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
        
        
        $html1 .='<tr>';
        $html1 .='<td colspan="12" style="height:160px;width:100%;border-top: 1px solid #333;border-bottom: 1px solid #333;"></td>';
        $html1 .='</tr>';
            
        
        $html1 .='<tr>';
        $html1 .='<td colspan="8" style="width:65%;border-right: 1px solid #333;"> </td>';
        $html1 .='<td colspan="4" style="width:35%;text-align:center;">
        for '.$CompanyName.'<br><br><br><br><br><b>Authorized Signature</b>
        </td>';
        $html1 .='</tr>';
        
    $html1 .= '</tbody>';
    $html1 .='</table>';

$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html1, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');