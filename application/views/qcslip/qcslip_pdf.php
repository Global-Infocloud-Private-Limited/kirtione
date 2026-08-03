<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle($GateDetails->BookingID);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);

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
$pdf->SetFont('dejavusans', '', 10);
$pdf->setJPEGQuality(75);
// add a page
$pdf->AddPage();

$CompanyName = $RootCompany->company_name;
$CompanyAddress = $RootCompany->address;

if(isset($GateDetails)){
    $NetWeight = 0;
    foreach($GateDetails as $k=>$v){
        $id = $v['id'];
        $generate_date = _d($v['asn_date']);
        $AccountID = $v['AccountID'];
        $BookingID = $v['BookingID'];
        $Name = $v['CustomerType'];
        if($v['CustomerType'] == "1"){
            $Name = "Farmer";
        }else if($v['CustomerType'] == "2"){
            $Name = "Trader";
        }else if($v['CustomerType'] == "3"){
            $Name = "Corporate";
        }else if($v['CustomerType'] == "4"){
            $Name = "Broker";
        }
        if($v['company'] == null || $v['company'] == ""){
            $PartyName = $v['firstname'].' '.$v['lastname'];
        }else{
            $PartyName = $v['company'];
        }
        
        $ItemID = $v['ItemID'];
        $ItemName = $v['ItemName'];
        $quantity = $v['quantity'];
        $unit = $v['unit'];
        $QR = $v['QR'];
        $NetWeight = $v['LoadedWeight'] - $v['TareWeight'];
    }
}



        $Img = $pdf->Image(base_url().'assets/media/qrcode/'.$QR, 140, 44, 50, 50, '', '', '', true, 150, '', false, false, 1, false, false, false);
        
        $html = '
            <table style="width: 100%; font-size:12px;font-weight:700;border:1px solid black;" cellspacing="1" cellpadding="4">
                <tr>
                    <td style="width: 100%;text-align:center; font-size:24px;font-weight:700;"><b>'.$CompanyName.'</b></td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align:center; font-size:16px;font-weight:700;"><b>'.$CompanyAddress.'</b></td>
                </tr>
            </table>    
            <table style="width: 100%; font-size:12px;font-weight:700;" cellspacing="1" cellpadding="4" border="1">
                <tr>
                    <td><b>BookingID: </b>'.$BookingID.'</td>
                    <td><b>AccountID: </b>'.$AccountID.'</td>
                    <td style="text-align:center;"><b>Date: </b>'.$generate_date.'</td>
                    
                </tr>
            </table>
            <table style="width: 66.66%; font-size:12px;font-weight:700;" cellspacing="1" cellpadding="8" border="1">
                <tr>
                    <td style="width: 30%;"><b>Party Type: </b></td>
                    <td style="width: 70%;">'.$Name.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Party Name: </b></td>
                    <td style="width: 70%;">'.$PartyName.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Item ID: </b></td>
                    <td style="width: 70%;">'.$ItemID.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Item Name: </b></td>
                    <td style="width: 70%;">'.$ItemName.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Quantity: </b></td>
                    <td style="width: 70%;">'.$quantity.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Unit: </b></td>
                    <td style="width: 70%;">'.$unit.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Net Weight: </b></td>
                    <td style="width: 70%;"><b>'.$NetWeight.' Qtls</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;"><b>QC DETAILS</b></td>
                </tr>';
            if(isset($QcDetails)){
                foreach($QcDetails as $key=>$val){
                    $html .= '<tr>';
                    $html .= '<th style="width:30%;">'.$val['ItemParameterName'].'</th>
                    <th style="width:70%;">'.$val['ParameterValue'].'</th>';
                    $html .= '</tr>';
                }
            }   
            $html .= '</table>
            
            
            <center><img src="'.$Img.'"></center>';
    

$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');
