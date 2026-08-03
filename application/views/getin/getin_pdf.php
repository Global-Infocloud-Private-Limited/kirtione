<?php
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetTitle($GetInPassDetails->BookingID);

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

if(isset($GetInPassDetails)){
     
    foreach($GetInPassDetails as $k=>$v)
    {
        $generate_date = _d($v['gate_in_date']);
        $AccountID = $v['AccountID'];
        $Gate_in_ID = $v['Gate_in_ID'];
        $w_name = $v['w_name'];
        $GodownID = $v['GodownID'];
        $ChamberID = $v['CHID'];
        $StackID = $v['StackID'];
        $LOTID = $v['LOTID'];
        $ChamberName = $v['ChaumberName'];
        $StackName = $v['StackName'];
        $LotName = $v['LotName'];
        $BookingID = $v['BookingID'];
        $VehicleNo = $v['VehicleNo'];
        $Phone = $v['Phone'];
        $ItemName = $v['ItemName'];
        $PartyName = $v['PartyName'];
        $quantity = $v['Asn_WT_MT'];
        $unit = $v['BUnit'];
        $LoadedWeight = ($v['LoadedWeight']) /10;
        $TareWeight = ($v['TareWeight']) / 10;
        if($v["TType"] == "S" || $v["TType"] == "W"){
            $NetWeight = abs($LoadedWeight - $TareWeight);
        }else{
            $NetWeight = abs($TareWeight - $LoadedWeight);
        }
        
        $Dcss = '';
        $scss = '';
        $Wcss = '';
        $acss = '';
        $tfcss = '';
        $pcss = '';
        $transaction_type = '';
        if($v["TType"] == "D"){
            $scss = 'display:none';
            $Wcss = 'display:none';
            $acss = 'display:none';
            $tfcss = 'display:none';
            $pcss = 'display:none';
            $transaction_type = 'Deposit';
        }
        if($v["TType"] == "P"){
            $Dcss = 'display:none';
            $Wcss = 'display:none';
            $acss = 'display:none';
            $tfcss = 'display:none';
            $pcss = 'display:none';
            $transaction_type = 'Sales';
        }
        if($v["TType"] == "S"){
            $Dcss = 'display:none';
            $Wcss = 'display:none';
            $acss = 'display:none';
            $tfcss = 'display:none';
            $scss = 'display:none';
            $transaction_type = 'Purchase';
        }
        if($v["TType"] == "W"){
            $scss = 'display:none';
            $Dcss = 'display:none';
            $acss = 'display:none';
            $tfcss = 'display:none';
            $pcss = 'display:none';
            $transaction_type = 'Withdrawal';
        }
        if($v["TType"] == "A"){
            $scss = 'display:none';
            $Dcss = 'display:none';
            $Wcss = 'display:none';
            $tfcss = 'display:none';
            $pcss = 'display:none';
            $transaction_type = 'Anamat';
        }
        if($v["TType"] == "T"){
            $scss = 'display:none';
            $Dcss = 'display:none';
            $Wcss = 'display:none';
            $acss = 'display:none';
            $pcss = 'display:none';
            $transaction_type = 'T/F';
        }
        if($v["company"] == "" || $v["company"] == null){
            $PartyName = $v["firstname"].' '.$v["lastname"];
        }else{
            $PartyName = $v["company"];
        }
        $coll = '';
        if($v["total_bags"] >0){
            $coll .= $v["total_bags"].' Bags ';
        }
        if($v["total_katta"] >0 ){
            $coll .= $v["total_katta"].' Katta';
        }
        $QR = $v['QR'];
        $vendor_invoice_number = $v['vendor_invoice_number'] ?? '';
        $vendor_invoice_amount = $v['vendor_invoice_amount'] ?? '';
        $vendor_invoice_date = _d($v['vendor_invoice_date'] ?? '');
        $vendor_ewaybill_number = $v['vendor_ewaybill_number'] ?? '';
        //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $Img = $pdf->Image(base_url().'assets/media/qrcode/'.$QR, 55, 178, 30, 30, '', '', '', true, 300, '', false, false, 1, false, false, false);
        
        $html = '
            <table style="width: 100%; font-size:12px;font-weight:700;" cellspacing="1" cellpadding="6" border="0.1">
                <tr>
                    <td style="width: 100%;text-align:center; font-size:24px;font-weight:700;"><b>'.$CompanyName.'</b><br><b style="width: 100%;text-align:center; font-size:16px;font-weight:500;">'.$CompanyAddress.'</b></td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align:center; font-size:14px;font-weight:700;"><b>GATE IN PASS</b></td>
                </tr>
                <tr>
                    <td style="width:25%;"><b>GateIn No.</b></td>
                    <td style="width:25%;">'.$Gate_in_ID.'</td>
                    <td style="width:25%;"><b>BookingID</b></td>
                    <td style="width:24.7%;">'.$BookingID.'</td>
                </tr>
                <tr>
                    <td style="width:25%; "><b>Booking Date </b></td>
                    <td style="width:25%; ">'._d($v['BookingDate']).'</td>
                    <td style="width:25%; "><b>Asn Date</b></td>
                    <td style="width:24.7%; ">'._d($v['asn_date']).'</td>
                </tr>
                <tr>
                    <td style="width:25%; "><b>Vendor Invoice No</b></td>
                    <td style="width:25%; ">'.$vendor_invoice_number.'</td>
                    <td style="width:25%; "><b>Vendor Invoice Date</b></td>
                    <td style="width:25%; ">'.$vendor_invoice_date.'</td>
                </tr>
                <tr>
                    <td style="width:25%; "><b>Vendor Ewaybill No</b></td>
                    <td style="width:75%; ">'.$vendor_ewaybill_number.'</td>
                </tr>
                <tr>    
                    <td style="width:25%; "><b>GateIn Time</b></td>
                    <td style="width:25%; ">'.$generate_date.'</td>
                    <td style="width:25%; "><b>GateOut Time</b></td>
                    <td style="width:24.7%; "></td>
                </tr>
                <tr style="display:none">
                    <td style="width:17%; "><b>Deposit &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$Dcss.'">&#x2713;</span></b></td>
                    <td style="width:17%; " colspan="2"><b>Withdrawal &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$Wcss.'">&#x2713;</span></b></td>
                    <td style="width:16%; "><b>Sales &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$scss.'">&#x2713;</span></b></td>
                    <td style="width:17%; "><b>Purchase &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$pcss.'">&#x2713;</span></b></td>
                    <td style="width:16%; "><b>Anamat &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$acss.'">&#x2713;</span></b></td>
                    <td style="width:16%; "><b>T/F &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;font-weight:bold;'.$tfcss.'">&#x2713;</span></b></td>
                </tr>';
                // <tr>    
                //     <td style="width:25%; "><b>Transaction Type</b></td>
                //     <td style="width:75%; ">'.$transaction_type.'</td>
                // </tr>
            $html .= '<tr>
                    <td style="width: 25%; "><b>Party Name</b></td>
                    <td style="width: 75%; ">'.$PartyName.'</td>
                </tr>
                
                <tr>
                    <td style="width: 20%; "><b>Commodity</b></td>
                     <td style="width: 30%; ">'.$ItemName.'</td>
                    <td style="width: 25%; "><b>Variety</b></td>
                     <td style="width: 24.7%; ">'. $v['base_value'].'</td>
                </tr>
                <tr>
                    <td style="width: 20%; "><b>Vehicle No</b></td>
                    <td style="width: 30%; ">'.$VehicleNo.'</td>
                    <td style="width: 25%; "><b>ASN Weight</b></td>
                    <td style="width: 24.7%; ">'.number_format($quantity, 3, '.', '').' MT</td>
                </tr>';
                if($v["TType"] == "S" || $v["TType"] == "W"){
                    $html .= '<tr>
                    <td style="width: 33.33%; "><b>Empty Vehicle Weight </b></td>
                    <td style="width: 33.33%; "><b>Loaded Vehicle Weight </b></td>
                    <td style="width: 33.1%; "><b>Net Weight </b></td>
                    </tr>
                    <tr>
                        <td style="width: 20%; text-align:center;">'.number_format($TareWeight, 3, '.', '').'</td>
                        <td style="width: 13.2%; ">MT</td>
                        <td style="width: 20%; text-align:center;">'.number_format($LoadedWeight, 3, '.', '').'</td>
                        <td style="width: 13.2%; ">MT</td>
                        <td style="width: 20%; text-align:center;">'.number_format($NetWeight, 3, '.', '').'</td>
                        <td style="width: 13.0%; ">MT</td>
                    </tr>';
                }else{
                    $html .= '<tr>
                    <td style="width: 33.33%; "><b>Gross Weight </b></td>
                    <td style="width: 33.33%; "><b>Tare Weight </b></td>
                    <td style="width: 33.1%; "><b>Net Weight </b></td>
                    </tr>
                    <tr>
                        <td style="width: 20%; ">'.number_format($LoadedWeight, 3, '.', '').'</td>
                        <td style="width: 13.2%; ">MT</td>
                        <td style="width: 20%; ">'.number_format($TareWeight, 3, '.', '').'</td>
                        <td style="width: 13.2%; ">MT</td>
                        <td style="width: 20%; ">'.number_format($NetWeight, 3, '.', '').'</td>
                        <td style="width: 13.0%; ">MT</td>
                    </tr>';
                }

                if($transaction_type !== 'Sales'){
                
                    $html .= '<tr>
                            <td style="width: 30%; "><b>Weigh Bridge</b></td>
                            <td style="width: 70%; "></td>
                        </tr>
                        <tr>
                            <td style="width: 30%; "><b>Weigh Bridge Slip No</b></td>
                            <td style="width: 70%; "></td>
                        </tr>
                        <tr>
                            <td style="width: 30%; "><b>Godown Name </b></td>
                            <td style="width: 70%; ">'.$w_name.' ('.$GodownID.')</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; "><b>Chamber Name</b></td>
                            <td style="width: 70%; ">'.$ChamberName.' ('.$ChamberID.')</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; "><b>Stack No</b></td>
                            <td style="width: 70%; ">'.$StackName.'('.$StackID.')</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; "><b>Lot No </b></td>
                            <td style="width: 70%; ">'.$LotName.'('.$LOTID.')</td>
                        </tr>';
                }
                $html .= '<tr>
                    <td style="width: 30%; "><b>No of Bags</b></td>
                    <td style="width: 70%; ">'.$coll.'</td>
                </tr>
                
                <tr>
                    <td style="width: 50%;height:125px; "><b>QR</b></td>
                    <td style="width: 50%; "><b>Remarks </b><br><br><br><br><br><br><b>Location </b></td>
                </tr>
                <tr>
                    <td style="width: 50%;height:120px; "><br><br><br><br><br><br><br><b>Signature and Name of Driver </b></td>
                    <td style="width: 50%;height:120px; "><br><br><br><br><br><br><br><b>Signature of WH Manager/Supervisor </b></td>
                </tr>
                <tr>
                    <td style="width: 100%; text-align:center;font-size:16px;font-weight:700; ">
                        <b>'.$CompanyName.'</b><br>
                        <b style="font-size:12px;">'.$CompanyAddress.'</b>
                    </td>
                </tr>
            </table>
            <center><img src="'.$Img.'"></center>';
    }
}

$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');
