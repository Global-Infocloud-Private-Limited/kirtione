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

// $CompanyName = $RootCompany->company_name;
// $CompanyAddress = $RootCompany->address;
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
        $html1 .= '<thead>';
        
        $html1 .='<tr>';
        $html1 .='<td style="border-top: 1px solid #333;"><img src="' . site_url() . '/uploads/company/a093e544716efb366a062b996f0ca635.png"></td>
        <td style="border-top: 1px solid #333;" colspan="12"><b style="width: 100%;text-align:center; font-size:18px;font-weight:700;">'.$CompanyName.'</b><br><span style="width: 100%;text-align:center; font-size:10px;">'.$CompanyAddress.'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>Del. Loc:</b> '.$v['CenterName']. ", " .$v['address'] . ', Tq. : '.$v['TalukaName'].', Dist : '.$v['city_name'].' - '.$v['state'].'</span><br><span style="width: 100%;text-align:center; font-size:10px;"><b>Buy GSTN:</b> '.$CompanyGST.' , <b>FSSAI No: </b> ' . $fssaiNo . '</span></td>';
        $html1 .='</tr>';
        
        if($CustomerType != 1){
            $html1 .='<tr>';
            $html1 .='<th colspan="2" style="width:10%;border-bottom: 1px solid #333;border-top: 1px solid #333;" rowspan="2"><b>' . $partyName . '</b></th>';
            $html1 .='<th colspan="2" style="width:28%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" rowspan="2">: '.$v['company'].'</th>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID.</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$v['BookingID'].'</td>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>DB Note No.</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '.$cd_details->Billno.'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Trade Dt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['BookingDate']).'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>DB Note Dt.</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '._d($cd_details->Transdate).'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Broker</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Ven Doc No.</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Ven Doc.Dt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: </td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>' . $supplierGSTN . ' GSTN</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: ' . $supplierGSTNValue . ' </td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>PO No.</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$cd_details->PurchID.'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Bill Amt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '. number_format($NetPurchaseAmt, 2, '.', '').'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Del. Dt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['gate_in_date']).'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Lorry No.</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '.$v['VehicleNo'].'</td>';
            $html1 .='<td colspan="4" style="border-bottom: 1px solid #333;"></td>';
            $html1 .='</tr>';
        }else{
            
            $html1 .='<tr>';
            $html1 .='<th colspan="2" style="width:10%;border-bottom: 1px solid #333;border-top: 1px solid #333;" rowspan="2"><b>' . $partyName . '</b></th>';
            $html1 .='<th colspan="2" style="width:28%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" rowspan="2">: '.$v['company'].'</th>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID.</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$v['BookingID'].'</td>';
            $html1 .='<td colspan="2" style="width:12%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>PO No.</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '.$PurchaseDetails->PurchID.'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Trade Dt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($v['BookingDate']).'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Del. Dt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '._d($v['gate_in_date']).'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Broker</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: </td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Bill Amt</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.number_format($NetPurchaseAmt, 2, '.', '').'</td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;"><b>Lorry No.</b></td>';
            $html1 .='<td colspan="2" style="border-bottom: 1px solid #333;">: '.$v['VehicleNo'].'</td>';
            $html1 .='</tr>';
            
        }
        
        $html1 .= '</thead>';
        
        
        $html1 .= '<tbody>';
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center; font-size:12px;font-weight:700;background-color:#BEBEBE;border-bottom: 1px solid #333;" colspan="12"><b>Purchase Invoice Details</b></td>';
        $html1 .='</tr>';
        
        
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
        
        
        
        $html1 .='<tr>';
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
        $html1 .='</tr>';
        
        
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center;border-bottom: 1px solid #333;border-top: 1px solid #333; font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Bargain Details</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        
        
        $html1 .='<td style="width:6%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Sr.No</b></td>';
        $html1 .='<td colspan="5" style="width:22%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Item Name</b></td>';
        $html1 .='<td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>HSN</b></td>';
        $html1 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade Rate</b></td>';
        $html1 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>G. Wt.(MT)</b></td>';
        $html1 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>T. Wt.(MT)</b></td>';
        $html1 .='<td style="width:13%;border-right: 1px solid #333;border-bottom: 1px solid #333;"><b>Net Wt.(MT)</b></td>';
        $html1 .='<td style="width:10%;border-bottom: 1px solid #333;"><b>Amount</b></td>';
        $html1 .='</tr>';
        
        
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
        
        $html1 .='<tr>';
        $html1 .='<td style="border-right: 1px solid #333;">01</td>';
        $html1 .='<td colspan="5" style="border-right: 1px solid #333;">'.$getControl_details->ItemName.'</td>';
        $html1 .='<td style="border-right: 1px solid #333;">' . $getControl_details->hsn_code . '</td>';
        //$html1 .='<td style="border-right: 1px solid #333;text-align:right;">' . number_format($Finalrate, 2, '.', '') . '</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'.number_format($getControl_details->basic_rate * 10 , 2, '.', '').'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format(($getControl_details->LoadedWeight/10), 2, '.', '').'</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">' . number_format(($getControl_details->TareWeight/10), 2, '.', '') . '</td>';
        $html1 .='<td style="border-right: 1px solid #333;text-align:right;">'. number_format($ActInwardWardWt, 2, '.', '').'</td>';
        $html1 .='<td style="text-align:right;">'.number_format($NetValue, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td colspan="11" style="border-bottom: 1px solid #333;border-top: 1px solid #333;border-right: 1px solid #333;"><b>Total</b></td>';
        
        $html1 .='<td style="text-align:right;border-bottom: 1px solid #333;border-top: 1px solid #333;">'.number_format($NetValue, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center; border-bottom: 1px solid #333;font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Quality Deductions</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Sr.No</b></td>';
        $html1 .='<td colspan="3" style="width:20%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Particulars</b></td>';
        $html1 .='<td colspan="2" style="width:16%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Required</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Actual</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Diff.</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Weight(MT)</b></td>';
        $html1 .='<td style="width:11%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Bag Qty</b></td>';
        $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;text-align:center;"><b>Value</b></td>';
        $html1 .='</tr>';
        
        $QCSrNo = 1;
        $totalDeduction = 0;
        $QualityDeduction = 0;
        $QCParaCount = count($QCStackDetails["0"]['QcDetails']);
        $StackCount = count($QCStackDetails);
        foreach($QCStackDetails as $stackKey=>$stackVal)
        {
            $html1 .= '<tr>';
            $html1 .= '<td style="width: 100%;text-align:center; border-bottom: 1px solid #333;font-size:12px;font-weight:700;" colspan="12"><b>QC '.$QCSrNo.' Details</b></td>';
            $html1 .= '</tr>';
            $i = 1;
            $LotWeight = $stackVal["Weight"];
            $LotBag = $stackVal["BagQty"];
            $row = 1;
            //$QCParaCount = 3;
            foreach($stackVal["QcDetails"] as $QCKey=>$QCVal)
            {
                $deductionAmt = $QCVal["deductionAmt"];
                $totalDeduction += $deductionAmt;
                $QualityDeduction += $QCVal["deductionAmt"];
                
                $html1 .= '<tr>';
                $html1 .= '<td style="width:6%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">'.$i.'</td>';
                $html1 .= '<td colspan="3" style="width:20%;border-right: 1px solid #333;border-bottom: 1px solid #333;">'.$QCVal['ItemParameterName'] .'</td>';
                $html1 .= '<td colspan="2" style="width:16%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($QCVal['BaseValue'], 3, '.', '').'</td>';
                $html1 .= '<td style="width:11%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;">'.number_format($QCVal['HParameterValue'], 3, '.', '').'</td>';
                $diff = $QCVal['HParameterValue'] - $QCVal['BaseValue'];
                $html1 .= '<td style="width:11%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'. number_format($diff, 3, '.', '').'</td>';
                if($row == "1"){
                    $html1 .= '<td style="width:11%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;" rowspan="'.$QCParaCount.'">'.number_format($LotWeight, 3, '.', '').'</td>';
                    $html1 .= '<td style="width:11%;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;" rowspan="'.$QCParaCount.'">'.number_format($LotBag, 3, '.', '').'</td>';
                }
                $html1 .= '<td colspan="2" style="width:14%;border-bottom: 1px solid #333;text-align:right;">'.$QCVal['deductionAmt'].'</td>';
                $html1 .= '</tr>';
                $i++;
                $row++;
            }
            $QCSrNo++;
        }
        $html1 .= '<tr>';
        $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;" colspan="10">Total Quality Deduction</td>';
        $html1 .= '<td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><b>'.number_format($QualityDeduction, 3, '.', '').'</b></td>';
        $html1 .= '</tr>';
        
        
        if($ActualOtherDeductionList){
            $match = 0;
            foreach($ActualOtherDeductionList as $key=>$val){
                if($val["ParticularItemID"] == "QOD" ){
                    $match++;
                }
            }
            if($match >0){
                $html1 .= '<tr>';
                $html1 .= '<td style="width: 100%;text-align:center; border-bottom: 1px solid #333;font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Other Deductions</b></td>';
                $html1 .= '</tr>';
                $i = 1;
                $OthDeduction = 0;
                foreach($ActualOtherDeductionList as $okey=>$oval){
                    $totalDeduction += $oval["Amount"];
                    $OthDeduction += $oval["Amount"];
                    if($oval["ParticularItemID"] == "QOD" ){
                        $html1 .= '<tr>';
                        $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">'.$i.'</td>';
                        $html1 .= '<td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;">'.$oval['ItemName'].'</td>';
                        $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">-</td>';
                        $html1 .= '<td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>';
                        $html1 .= '<td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>';
                        $html1 .= '<td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>';
                        $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">-</td>';
                        $html1 .= '<td colspan="2" style="border-bottom: 1px solid #333;text-align:right;">'.number_format($oval["Amount"], 3, '.', '').'</td>';
                        $html1 .= '</tr>';
                        $i++;
                    }
                    
                }
                $html1 .= '<tr>';
                $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;" colspan="10">Total Other Deduction</td>';
                $html1 .= '<td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><b>'.number_format($OthDeduction, 3, '.', '').'</b></td>';
                $html1 .= '</tr>';
            }
        }
       
        
        
        if($CustomerType != 1){
            $html1 .='<tr>';
            $html1 .='<td style="width: 100%;text-align:center;border-bottom: 1px solid #333; font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Debit Note Details</b></td>';
            $html1 .='</tr>';
            
            $html1 .= '<tr>';
            $html1 .= '<td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Sr.No.</td>';
            $html1 .= '<td colspan="5" style="width:34%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Particulars</td>';
            $html1 .= '<td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;">HSN </td>';
            $html1 .= '<td style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Qty/Nos</td>';
            $html1 .= '<td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Rate</td>';
            $html1 .= '<td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;">Amount</td>';
            $html1 .= '</tr>';
            
            $i = 1;
            $rate_per_kg = ($getControl_details->basic_rate / 100);
            $NetWt_in_kg = $PurchaseWeight * 1000;
            $quantity = 0;
            $totalDeduction = 0;
            foreach($DebitNoteItem as $DNKey=>$DNVal){
                $particularAmt = 0;
                foreach($ActualOtherDeductionList as $ADKey =>$ADVal){
                    if($DNVal["ItemID"] == $ADVal["ParticularItemID"]){
                        $particularAmt += $ADVal["Amount"];
                        $quantity = $ADVal["quantity"];
                    }
                }
                if($DNVal["ItemID"] == "QOD"){
                    $particularAmt += $QualityDeduction;
                    $rate_per_kg = $particularAmt / $NetWt_in_kg;
                    $quantity = $NetWt_in_kg;
                }
                $totalDeduction += $particularAmt;
                $html1 .= '<tr>';
                $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">'.$i.'</td>';
                $html1 .= '<td colspan="5" style="border-right: 1px solid #333;border-bottom: 1px solid #333;">'.$DNVal["ItemName"].'</td>';
                $html1 .= '<td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">12010090</td>';
                $html1 .= '<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($quantity, 2, '.', '').'</td>';
                $html1 .= '<td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($rate_per_kg, 3, '.', '').'</td>';
                $html1 .= '<td colspan="2" style="border-bottom: 1px solid #333;text-align:right;">'.number_format($particularAmt, 2, '.', '').'</td>';
                $html1 .= '</tr>';
                $i++;
            }
            
            $QCGstAmt = ($AllDeduction * $taxrate) /100;
            $final_deduction = $AllDeduction + $QCGstAmt;
            
            if($getControl_details->state == "MH"){
                $QCCGSTAmt = $QCGstAmt / 2;
                $QCSGSTAmt = $QCGstAmt / 2;
                $QCIGSTAmt = 0;
                $CGSTPer = $taxrate/2;
                $SGSTPer = $taxrate/2;
                $IGSTPer = 0;
            }else{
                $QCIGSTAmt = $QCGstAmt;
                $QCSGSTAmt = 0;
                $QCCGSTAmt = 0;
                
                $IGSTPer = $taxrate;
                $SGSTPer = 0;
                $CGSTPer = 0;
            }
            $height = '20px';
            if($StackCount > 2 ){
                $height = '150px';
                //$pdf->AddPage();
            }
            $html1 .='<tr>';
            $html1 .='<td colspan="13" style="height:'.$height.';border-bottom: 1px solid #333;"></td>';
            $html1 .='</tr>';
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:17%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Document</b></td>';
            $html1 .='<td colspan="2" style="width:20%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Basic Value Net of TDS</b></td>';
            $html1 .='<td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>GST Amt</b></td>';
            $html1 .='<td colspan="2" style="width:18%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Net Payable</b></td>';
            $html1 .='<td style="width:5%;border-right: 1px solid #333;" rowspan="6"></td>';
            $html1 .='<td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Sub Total</b></td>';
            $html1 .='<td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;"><b>'.number_format($AllDeduction, 2, '.', '').'</b></td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Purchase Invoice</td>';
            $html1 .='<td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($PurchaseValue, 2, '.', '').'</td>';
            $html1 .='<td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($GstAmt, 2, '.', '').'</td>';
            $html1 .='<td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($NetPurchaseAmt, 2, '.', '').'</td>';
            //$html1 .='<td style="width:5%;"></td>';
            $html1 .='<td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">CGST + @'.number_format($CGSTPer, 2, '.', '').'%</td>';
            $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">'.number_format($QCCGSTAmt, 2, '.', '').'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Debit Note</td>';
            $html1 .='<td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($AllDeduction, 2, '.', '').'</td>';
            $html1 .='<td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">' . number_format($QCGstAmt, 2, '.', '') . '</td>';
            $html1 .='<td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($final_deduction, 2, '.', '').'</td>';
            //$html1 .='<td style="width:5%;"></td>';
            $html1 .='<td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">SGST + @'.number_format($SGSTPer, 2, '.', '').'%</td>';
            $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">'.number_format($QCSGSTAmt, 2, '.', '').'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Net</td>';
            $html1 .='<td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($NetValue - $AllDeduction, 2, '.', '').'</td>';
            $html1 .='<td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format(($GstAmt - $QCGstAmt), 2, '.', '') .'</td>';
            $html1 .='<td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">'.number_format($NetPurchaseAmt - $final_deduction, 2, '.', '').'</td>';
            //$html1 .='<td style="width:5%;"></td>';
            $html1 .='<td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">IGST + @'.number_format($IGSTPer, 2, '.', '').'%</td>';
            $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">'.number_format($QCIGSTAmt, 2, '.', '').'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="6" style="width:65%;"></td>';
            //$html1 .='<td style="width:5%;"></td>';
            $html1 .='<td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Round Off</td>';
            $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">0.00</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="6" style="width:65%;"></td>';
            //$html1 .='<td style="width:5%;"></td>';
            $html1 .='<td colspan="2" style="width:15%;"><b>Total Amount</b></td>';
            $html1 .='<td colspan="2" style="width:15%;text-align:right;text-align:right;"><b>'.number_format($final_deduction, 2, '.', '').'</b></td>';
            $html1 .='</tr>';
            
            
            $html1 .='<tr>';
            $html1 .='<td colspan="10" style="height:60px;width:100%;border-top: 1px solid #333;border-bottom: 1px solid #333;"></td>';
            $html1 .='</tr>';
        }
        $html1 .= '</tbody>';
        $html1 .='<tfoot style="width:100%;position:fixed !important;bottom:0 !important">';
        $html1 .='<tr>';
        
        $html1 .='<td colspan="8" style="width:65%;border-right: 1px solid #333;border-bottom: 1px solid #333;">
        <br>This settlement advice is full and final. In case of any dispute it should be intimated within 5 days from the advice date. Or the same will be treated as final.
        <br><br><b>Date: </b> '.date('d/m/y').'
        </td>';
        $html1 .='<td colspan="4" style="width:35%;text-align:center;border-bottom: 1px solid #333;"><span style="font-size:9px !importants;"><b> for '.$CompanyName.'</b></span>
        <br><br><br><br><br><b>Authorized Signature</b>
        </td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td colspan="6" style="width:50%;height:30px;"><br><br><b>Printed By : </b>'. get_staff_full_name($this->session->userdata('staffid')).'</td>';
        $html1 .='<td colspan="3" style="width:35%;text-align:left;height:30px;"><br><br><b>Printed On : </b>'._d(date("Y-m-d H:i:s")).'</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;height:30px;"><br><br>Page 1 of 1</td>';
        $html1 .='</tr>';   
        $html1 .='</tfoot>';
        $html1 .='</table>';
       
    }
}

$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html1, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');
