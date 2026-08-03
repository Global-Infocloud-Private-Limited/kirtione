<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle($GateDetails->BookingID);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);

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

$CompanyName = $PlantDetails->PlantName;
$CompanyAddress = $PlantDetails->address.", Tq : ".$PlantDetails->TalukaName.", Dist : ".$PlantDetails->city_name." (".$PlantDetails->state.") - ".$PlantDetails->pincode;
$CompanyGST = $PlantDetails->GstNo;
$fssaiNo = $PlantDetails->fssai_no;

//Check customer type // Farmer or trader
$CustomerType = $PaymentDetails[0]["CustomerType"];

if($CustomerType == 1){
    $partyName = 'Party';
    $supplierGSTN = 'Party';
    $supplierGSTNValue = 'Not Applicable';
}else{
    $partyName = 'Supplier';
    $supplierGSTN = 'Sup';
    $supplierGSTNValue = $PurchaseDetails->gstin;
}

$header = '';
$html1 = '';
if(isset($PaymentDetails)){
    foreach($PaymentDetails as $k=>$v){
        
        $cd_details = get_cd_details($v["Gate_in_ID"]);
        $getControl_details = get_control_details($v["Gate_in_ID"]);
        $taxrate = $getControl_details->taxrate;
        
        $BagWeight = 20;
        $RatePerKg = $getControl_details->basic_rate / 100;
        if($getControl_details->CustomerType == "1"){
            $taxrate = 0;
            $PurchaseWeight = ($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10;
            $ActInwardWardWt = $PurchaseWeight;
        }else{
            $PurchaseWeight = $getControl_details->Asn_WT_MT;
            $actWt = ($getControl_details->LoadedWeight - $getControl_details->TareWeight)/10;
            if($PurchaseWeight <= $actWt){
                $ActInwardWardWt = $PurchaseWeight;
            }else{
                $ActInwardWardWt = $actWt;
            }
        }
        $PurchaseValue = $PurchaseWeight * ($getControl_details->basic_rate * 10);
        $GstAmt = $PurchaseValue * ($taxrate / 100);
        $NetPurchaseAmt = $PurchaseValue + $GstAmt;
        
        $html1 .='<table style="width: 100%;border:1px solid #333; font-size:10px;font-weight:400;" cellspacing="1" cellpadding="3">';
    // Company Information
        $header .= '<thead>';
        $header .='<tr>';
        $header .='<th style="border-top: 1px solid #333;"><img src="' . site_url() . '/uploads/company/a093e544716efb366a062b996f0ca635.png"></th>
        <th style="border-top: 1px solid #333;" colspan="12"><b style="width: 100%;text-align:center; font-size:18px;font-weight:700;">'.$CompanyName.'</b><br><span style="width: 100%;text-align:center; font-size:10px;">'.$CompanyAddress.'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>Del. Loc:</b> '.$v['CenterName']. ", " .$v['address'] . ', Tq. : '.$v['TalukaName'].', Dist : '.$v['city_name'].' - '.$v['state'].'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>Buy GSTN:</b> '.$CompanyGST.' , <b>FSSAI No: </b> ' . $fssaiNo . '</span></th>';
        $header .='</tr>';
        
        if($CustomerType != 1){
            $header .='<tr>';
            $header .='<th colspan="2" style="width:10%;border-bottom: 1px solid #333;border-top: 1px solid #333;" rowspan="2"><b>' . $partyName . '</b></th>';
            $header .='<th colspan="2" style="width:28%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" rowspan="2">: '.$v['company'].'</th>';
            $header .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID.</b></td>';
            $header .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$v['BookingID'].'</td>';
            $header .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>DB Note No.</b></td>';
            $header .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '.$cd_details->Billno.'</td>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Trade Dt</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['BookingDate']).'</th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>DB Note Dt.</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;">: '._d($cd_details->Transdate).'</th>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Broker</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Ven Doc No.</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Ven Doc.Dt</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;">: </th>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>' . $supplierGSTN . ' GSTN</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: ' . $supplierGSTNValue . ' </th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>PO No.</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$cd_details->PurchID.'</th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Bill Amt</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;">: '. number_format($NetPurchaseAmt, 2, '.', '').'</th>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Del. Dt</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['gate_in_date']).'</th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;"><b>Lorry No.</b></th>';
            $header .='<th colspan="2" style="border-bottom: 1px solid #333;">: '.$v['VehicleNo'].'</th>';
            $header .='<th colspan="4" style="border-bottom: 1px solid #333;"></th>';
            $header .='</tr>';
        }else{
            
            $header .='<tr>';
            $header .='<th colspan="2" style="width:10%;border-bottom: 1px solid #333;border-top: 1px solid #333;" rowspan="2"><b>' . $partyName . '</b></th>';
            $header .='<th colspan="2" style="width:28%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" rowspan="2">: '.$v['company'].'</th>';
            $header .='<th colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID.</b></th>';
            $header .='<th colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$v['BookingID'].'</th>';
            $header .='<th colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>PO No.</b></th>';
            $header .='<th colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '.$PurchaseDetails->PurchID.'</th>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Trade Dt</b></td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['BookingDate']).'</td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Del. Dt</b></td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;">: '._d($v['gate_in_date']).'</td>';
            $header .='</tr>';
            
            $header .='<tr>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Broker</b></td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Bill Amt</b></td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.number_format($NetPurchaseAmt, 2, '.', '').'</td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Lorry No.</b></td>';
            $header .='<td colspan="2" style="border-bottom: 1px solid #333;">: '.$v['VehicleNo'].'</td>';
            $header .='</tr>';
        }
        $header .= '</thead>';
        $html1 .= $header;
        
        
        $footer .='<tfoot>';
        $footer .='<tr>';
        
        $footer .='<td colspan="8" style="width:65%;border-right: 1px solid #333;border-bottom: 1px solid #333;">
        <br>This settlement advice is full and final. In case of any dispute it should be intimated within 5 days from the advice date. Or the same will be treated as final.
        <br><br><b>Date: </b> '.date('d/m/y').'
        </td>';
        $footer .='<td colspan="4" style="width:35%;text-align:center;border-bottom: 1px solid #333;"><span style="font-size:9px !importants;"><b> for '.$CompanyName.'</b></span>
        <br><br><br><br><br><b>Authorized Signature</b>
        </td>';
        $footer .='</tr>';
        
        $footer .='<tr>';
        $footer .='<td colspan="6" style="width:50%;height:30px;"><br><br><b>Printed By : </b>'. get_staff_full_name($this->session->userdata('staffid')).'</td>';
        $footer .='<td colspan="3" style="width:35%;text-align:left;height:30px;"><br><br><b>Printed On : </b>'._d(date("Y-m-d H:i:s")).'</td>';
        $footer .='<td colspan="3" style="width:15%;text-align:right;height:30px;"><br><br>Page 1 of 1</td>';
        $footer .='</tr>';   
        $footer .='</tfoot>';
        
        
        
        $html1 .= '<tbody>';
        $tr_count = 0;
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center; font-size:12px;font-weight:700;background-color:#BEBEBE;border-bottom: 1px solid #333;" colspan="12"><b>Purchase Invoice Details</b></td>';
        $html1 .='</tr>';
        $tr_count++;
        
       $html1 .='<tr>';
        $html1 .='<td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Sr.No</b></td>';
        $html1 .='<td colspan="5" style="width:25%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Item Name</b></td>';
        $html1 .='<td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>HSN</b></td>';
        $html1 .='<td style="width:13%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Ven. Dis. WT.</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Trade Rate</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Basic Value</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>GST Amt</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;"><b>Payble Amt</b></td>';
        $html1 .='</tr>';
        $tr_count++;
        
        
        /*$html1 .='<tr>';
        $html1 .='<td style="border-right: 1px solid #333;">1</td>';
        $html1 .='<td colspan="5" style="border-right: 1px solid #333;">'.$getControl_details->ItemName.'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:center;">'.$getControl_details->hsn_code.'</td>';
        
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($PurchaseWeight, 2, '.', '').'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($getControl_details->basic_rate * 10 , 2, '.', '').'</td>';
        
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($PurchaseValue, 2, '.', '').'</td>';
        if($getControl_details->CustomerType == "1"){
            $GstAmt = 0;
        }else{
            $GstAmt = $PurchaseValue * ($taxrate / 100);
        }
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($GstAmt, 2, '.', '').'</td>';
        //$totalPayable = $basicValue + $GstAmt;
        $html1 .='<td style="text-align:right;">'.number_format($NetPurchaseAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';*/
        $tr_count++;
        if($tr_count == 3){
            $html1 .= '</body>';
            $html1 .= $footer;
            $html1 .= '</table>';
            ob_clean();
            $pdf->writeHTML($html1, true, false, true, false, '');
            $pdf->AddPage();
            $html2 = '';
            $html2 .='<table style="width: 100%;border:1px solid #333; font-size:10px;font-weight:400;" cellspacing="1" cellpadding="3">';
            $html2 .= $header;
            $html2 .= '<body>';
        }
        
        $html2 .='<tr>';
        $html2 .='<td style="width: 100%;text-align:center;border-bottom: 1px solid #333;border-top: 1px solid #333; font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Bargain Details</b></td>';
        $html2 .='</tr>';
        $tr_count++;
        
        $html2 .='<tr>';
        $html2 .='<td style="width:6%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Sr.No</b></td>';
        $html2 .='<td colspan="5" style="width:22%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Item Name</b></td>';
        $html2 .='<td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>HSN</b></td>';
        $html2 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade Rate</b></td>';
        $html2 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>G. Wt.(MT)</b></td>';
        $html2 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>T. Wt.(MT)</b></td>';
        $html2 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Net Wt.(MT)</b></td>';
        $html2 .='<td style="width:10%;border-bottom: 1px solid #333;"><b>Amount</b></td>';
        $html2 .='</tr>';
        $tr_count++;
        
        if(isset($QCDetails)){
            $i = 1;
            $totalDeduction = 0;
            foreach($QCDetails as $key=>$val){
                $deductionAmt = $val['deductionAmt'];
                $totalDeduction += $deductionAmt;
            }
        }
        
        foreach($ActualOtherDeductionList as $ADKey =>$ADVal){
            $totalDeduction += $ADVal["Amount"];
        }
        
        $Finalrate = ($PurchaseValue - $totalDeduction) / $ActInwardWardWt;
	    $NetValue = ($getControl_details->basic_rate * 10) * $ActInwardWardWt;
        
        $html2 .='<tr>';
        $html2 .='<td style="border-right: 1px solid #333;">01</td>';
        $html2 .='<td colspan="5" style="border-right: 1px solid #333;">'.$getControl_details->ItemName.'</td>';
        $html2 .='<td style="border-right: 1px solid #333;">' . $getControl_details->hsn_code . '</td>';
        //$html2 .='<td style="border-right: 1px solid #333;text-align:right;">' . number_format($Finalrate, 2, '.', '') . '</td>';
        $html2 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($getControl_details->basic_rate * 10 , 2, '.', '').'</td>';
        $html2 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format(($getControl_details->LoadedWeight/10), 2, '.', '').'</td>';
        $html2 .='<td style="border-right: 1px solid #333;text-align:right;">' . number_format(($getControl_details->TareWeight/10), 2, '.', '') . '</td>';
        $html2 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($ActInwardWardWt, 2, '.', '').'</td>';
        $html2 .='<td style="text-align:right;">'.number_format($NetValue, 2, '.', '').'</td>';
        $html2 .='</tr>';
        $tr_count++;
        
        $html2 .='<tr>';
        $html2 .='<td colspan="11" style="border-bottom: 1px solid #333;border-top: 1px solid #333;border-right: 1px solid #333;"><b>Total</b></td>';
        $html2 .='<td style="text-align:right;border-bottom: 1px solid #333;border-top: 1px solid #333;">'.number_format($NetValue, 2, '.', '').'</td>';
        $html2 .='</tr>';
        $tr_count++;
        $html2 .= '</body>';
        $html2 .= $footer;
        $html2 .= '</table>';
        $pdf->writeHTML($html2, true, false, true, false, '');
    }
}

$pdf->lastPage();
ob_clean();


$pdf->Output($BookingID.'.pdf', 'I');
