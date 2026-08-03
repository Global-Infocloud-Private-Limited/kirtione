<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle($AsnDetails->BookingID);

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

if(isset($AsnDetails)){
    foreach($AsnDetails as $k=>$v){
        $id = $v['id'];
        $ASNID = $v['ASNID'];
        $generate_date = _d($v['asn_date']);
        $AccountID = $v['AccountID'];
        $BookingID = $v['BookingID'];
        if($v['CustomerType'] == 1){
            $PartyType = 'Farmer';
        }
        if($v['CustomerType'] == 2){
            $PartyType = 'Broker';
        }
        if($v['CustomerType'] == 3){
            $PartyType = 'Trader';
        }
        if($v['CustomerType'] == 4){
            $PartyType = 'Corporate/Processor';
        }
        if($v['company'] != ''){
            $PartyName = $v['company'];
        }
        else{
            $PartyName = $v['firstname'].' '.$v['lastname'];
        }
        $ItemID = $v['ItemID'];
        $ItemName = $v['ItemName'];
        $quantity = $v['quantity'];
        $Qty_MT = $v['Asn_WT_MT'];
        $QR = $v['ASNQR'];
        
        $Img = $pdf->Image(base_url().'uploads/'.$BookingID.'/'.$ASNID."/".$QR, 140, 53, 50, 50, '', '', '', true, 150, '', false, false, 1, false, false, false);
        
        $html = '
            <table style="width: 100%; font-size:12px;font-weight:700;border:1px solid black;" cellspacing="1" cellpadding="4">
                <tr>
                    <td style="width: 100%;text-align:center; font-size:24px;font-weight:700;"><b>'.$CompanyName.'</b></td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align:center; font-size:16px;font-weight:700;"><b>'.$CompanyAddress.'</b></td>
                </tr>
            </table>    
            <table style="width: 100%; font-size:12px;font-weight:700;border:1px solid black;" cellspacing="1" cellpadding="4">
                <tr>
                    <td style="width: 100%;text-align:center; font-size:16px;font-weight:700;"><b>ASN DETAILS</b></td>
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
                    <td style="width: 70%;">'.$PartyType.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Party Name: </b></td>
                    <td style="width: 70%;">'.$PartyName.'</td>
                </tr>
                
                <tr>
                    <td style="width: 30%;"><b>Item Name: </b></td>
                    <td style="width: 70%;">'.$ItemName.'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Quantity: </b></td>
                    <td style="width: 70%;">'.$quantity.' Bags</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Weight: </b></td>
                    <td style="width: 70%;">'.$Qty_MT.' MT</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Vehicle No. : </b></td>
                    <td style="width: 70%;">'.$v['VehicleNo'].'</td>
                </tr>
                <tr>
                    <td style="width: 30%;"><b>Contact No. : </b></td>
                    <td style="width: 70%;">'.$v['Phone'].'</td>
                </tr>
            </table>
            <center><img src="'.$Img.'"></center>';
    }
}

$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');
