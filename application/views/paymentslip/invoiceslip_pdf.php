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

$CompanyName = $PlantDetails->PlantName;
$CompanyAddress = $PlantDetails->address.", Tq : ".$PlantDetails->TalukaName.", Dist : ".$PlantDetails->city_name." (".$PlantDetails->state.") - ".$PlantDetails->pincode;
$CompanyGST = $PlantDetails->GstNo;
$fssaiNo = $PlantDetails->fssai_no;

$html1 = '';
    $GstAmt = $GetInvoiceItemDetails->sgstamt + $GetInvoiceItemDetails->cgstamt + $GetInvoiceItemDetails->igstamt;
    if($GstAmt > 0){
        $InvoiceName = "Tax Invoice";
    }else{
        $InvoiceName = "Bill Of Supply";
    }
        $html1 .='<table style="width: 100%;border:1px solid #333; font-size:10px;font-weight:400;" cellspacing="1" cellpadding="3">';
    // Company Information
        $html1 .= '<thead>';
        
        $html1 .='<tr>';
        $html1 .='<td><img src="' . site_url() . '/uploads/company/a093e544716efb366a062b996f0ca635.png"></td>
        <td style="" colspan="12"><span style="text-align:center"><b>'.$InvoiceName.'</b></span><br><b style="width: 100%;text-align:center; font-size:18px;font-weight:700;">'.$CompanyName.'</b><br><span style="width: 100%;text-align:center; font-size:10px;">'.$CompanyAddress.'<br><b>GSTIN:</b> '.$CompanyGST.',  <b>FSSAI No: </b> ' . $fssaiNo.'</span></td>';
        $html1 .='</tr>';
        
            $html1 .='<tr>';
            $html1 .='<th colspan="2" style="width:14%;border-bottom: 1px solid #333;border-top: 1px solid #333;"><b>GateIN ID</b></th>';
            $html1 .='<th colspan="2" style="width:19%;border-right: 1px solid #333;border-top: 1px solid #333;border-bottom: 1px solid #333;" >: '.$GetInvoiceItemDetails->OrderID.'</th>';
            $html1 .='<td colspan="2" style="width:14%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>GateIN Date</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-top: 1px solid #333;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($GetInvoiceItemDetails->gate_in_date).'</td>';
            $html1 .='<td colspan="2" style="width:14%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Trade ID</b></td>';
            $html1 .='<td colspan="2" style="width:20%;border-top: 1px solid #333;border-bottom: 1px solid #333;">: '.$GetInvoiceItemDetails->BookingID.'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<th colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Invoice No.</b></th>';
            $html1 .='<th colspan="2" style="width:19%;border-right: 1px solid #333;border-bottom: 1px solid #333;" >: '.$GetInvoiceItemDetails->SalesID.'</th>';
            $html1 .='<th colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Invoice Date</b></th>';
            $html1 .='<th colspan="2" style="width:19%;border-right: 1px solid #333;border-bottom: 1px solid #333;" >: '._d($GetInvoiceItemDetails->Transdate).'</th>';
            $html1 .='<th colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Lorry No.</b></th>';
            $html1 .='<th colspan="2" style="width:20%;border-bottom: 1px solid #333;" >: '.$GetInvoiceItemDetails->VehicleID.'</th>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>E-Way Bill</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$GetInvoiceItemDetails->ewayno.'</td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>E-Way Bill Date</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($GetInvoiceItemDetails->eway_date).'</td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Driver Mobile</b></td>';
            $html1 .='<td colspan="2" style="width:20%;border-bottom: 1px solid #333;">: '.$GetInvoiceItemDetails->DriverID.'</td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Ack No.</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$GetInvoiceItemDetails->ackno.'</td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Ack Date</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '._d($GetInvoiceItemDetails->ackdate).'</td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Trans. Mode</b></td>';
            $html1 .='<td colspan="2" style="width:20%;border-bottom: 1px solid #333;">: </td>';
            $html1 .='</tr>';
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Place of Supply</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$PartyDetails->CenterName.' </td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>State Name</b></td>';
            $html1 .='<td colspan="2" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: '.$PartyDetails->state_name.' </td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>State Code</b></td>';
            $html1 .='<td colspan="2" style="width:20%;border-bottom: 1px solid #333;">: '.$PartyDetails->state_code.'</td>';
            $html1 .='</tr>';
            
            
            $html1 .='<tr>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Broker</b></td>';
            $html1 .='<td colspan="4" style="width:36%;border-bottom: 1px solid #333;border-right: 1px solid #333;">: </td>';
            $html1 .='<td colspan="2" style="width:14%;border-bottom: 1px solid #333;"><b>Trans Name</b></td>';
            $html1 .='<td colspan="4" style="width:36%;border-bottom: 1px solid #333;">: </td>';
            
            $html1 .='</tr>';
            
            
            $html1 .='<tr>';
            $html1 .='<td colspan="6" style="height:100px;width:50%;border-bottom: 1px solid #333;border-right: 1px solid #333;line-height:18px;"><b style="">BILL TO (DETAILS OF BUYER)</b>
            <br>'.$PartyDetails->business_name.'
            <br>'.$PartyDetails->address.'
            <br><b>State : </b>'.strtoupper($PartyDetails->PartyState).'
            <br><b>GSTIN : </b>'.$PartyDetails->BuyerGSTIN.'
            </td>';
            
            $html1 .='<td colspan="6" style="height:100px;width:50%;border-bottom: 1px solid #333;line-height:18px;"><b>SHIP TO (DELIVERY AT)</b>
            <br>'.$PartyDetails->business_name.'
            <br>'.$PartyDetails->address.'
            <br><b>State : </b>'.strtoupper($PartyDetails->PartyState).'
            <br><b>GSTIN : </b>'.$PartyDetails->BuyerGSTIN.'</td>';
            $html1 .='</tr>';
            /*$html1 .='<td colspan="6" style="height:100px;width:50%;border-bottom: 1px solid #333;line-height:18px;"><b>Ship To,</b>
            
            <br>'.$CompanyName.'<br>'.$PartyDetails->CenterName. ", <br>" .$PartyDetails->CenterAddress . ', Tq. : '.$PartyDetails->TalukaName.', Dist : '.$PartyDetails->city_name.' <br><b>State :</b> '.$PartyDetails->state_name.'<br><b>GSTIN:</b> '.$CompanyGST.'  <br><b>FSSAI No: </b> ' . $fssaiNo.'
            </td>';
            $html1 .='</tr>';*/
            
            
            
        $html1 .= '</thead>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;text-align:center; font-size:12px;font-weight:700;background-color:#BEBEBE;border-bottom: 1px solid #333;" colspan="12"><b>Sales Invoice Details</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Sr.No</b></td>';
        $html1 .='<td colspan="4" style="width:22%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Item Name</b></td>';
        $html1 .='<td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>HSN</b></td>';
        $html1 .='<td style="width:8%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Qty(MT)</b></td>';
        $html1 .='<td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Rate(MT)</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>Basic Value</b></td>';
        $html1 .='<td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>GST Rate</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;"><b>GST Amt</b></td>';
        $html1 .='<td style="width:12%;border-bottom: 1px solid #333;text-align:center;"><b>Net Amt</b></td>';
        $html1 .='</tr>';
        
        $i = 1;
        $NetAmount = 0;
        $TaxableAmt = 0;
        $CGSTAmt = 0;
        $SGSTAmt = 0;
        $IGSTAmt = 0;
        $CGSTPer = 0;
        $SGSTPer = 0;
        $IGSTPer = 0;
        foreach($GetInvoiceItemDetails->ItemDetails as $key=>$val){
            $NetAmount += $val["NetChallanAmt"];
            $TaxableAmt += $val["ChallanAmt"];
            $CGSTAmt += $val["cgstamt"];
            $SGSTAmt += $val["sgstamt"];
            $IGSTAmt += $val["igstamt"];
            $CGSTPer = $val["cgst"];
            $SGSTPer = $val["sgst"];
            $IGSTPer = $val["igst"];
            $GSTPer = $val["cgst"] + $val["sgst"] + $val["igst"];
            $html1 .='<tr>';
            $html1 .='<td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">'.$i.'</td>';
            $html1 .='<td colspan="4" style="border-right: 1px solid #333;border-bottom: 1px solid #333;">'.$val["ItemName"].'</td>';
            $html1 .='<td style="border-right: 1px solid #333;text-align:center;border-bottom: 1px solid #333;">'.$val["hsn_code"].'</td>';
            
            $html1 .='<td style="border-right: 1px solid #333;text-align:center;border-bottom: 1px solid #333;">'. number_format($val["BilledQty"], 2, '.', '').'</td>';
            $html1 .='<td style="border-right: 1px solid #333;text-align:right;border-bottom: 1px solid #333;">'.number_format($val["final_rate"] , 2, '.', '').'</td>';
            
            $html1 .='<td style="border-right: 1px solid #333;text-align:right;border-bottom: 1px solid #333;">'. number_format($val["ChallanAmt"], 2, '.', '').'</td>';
            $GstAmt = $val["cgstamt"] + $val["sgstamt"] + $val["igstamt"];
            $html1 .='<td style="border-right: 1px solid #333;text-align:right;border-bottom: 1px solid #333;">'.number_format($GSTPer, 2, '.', '').'%</td>';
            $html1 .='<td style="border-right: 1px solid #333;text-align:right;border-bottom: 1px solid #333;">'.number_format($GstAmt, 2, '.', '').'</td>';
            $html1 .='<td style="text-align:right;border-bottom: 1px solid #333;">'.number_format($val["NetChallanAmt"], 2, '.', '').'</td>';
            $html1 .='</tr>';
            
            $i++;
        }
        
        $rowspan = 6;
        $html1 .='<tr>';
        $html1 .='<td style="text-align:left;border-bottom: 1px solid #333; font-size:11px;" colspan="11"><b>Total</b></td>';
        $html1 .='<td style="text-align:right;border-bottom: 1px solid #333; font-size:11px;"><b>'.number_format($NetAmount, 2, '.', '').'</b></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td style="width: 100%;height:120px;text-align:center;border-bottom: 1px solid #333; font-size:12px;font-weight:700;" colspan="12"></td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td colspan="4" rowspan="'.$rowspan.'" style="border-right: 1px solid #333;width:35%;border-bottom: 1px solid #333;line-height:18px;">
        <b>Subject to LATUR Jurisdiction</b><br>
        <b>GST NO.</b> : '.$CompanyGST.' <br>
        <b>FSSAI LIC NO</b> : '.$fssaiNo.' <br>
        <b>PAN NO</b> : '.substr($CompanyGST,2,10).' <br>
        <b>CREDIT / CASH</b> : CREDIT 
        </td>';
        $html1 .='<td colspan="3" rowspan="'.$rowspan.'" style="border-right: 1px solid #333;width:30%;border-bottom: 1px solid #333;"></td>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">Taxable Amount</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;">'.number_format($TaxableAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
            
        $html1 .='<tr>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">CGST ('.$CGSTPer.'%)</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;">'.number_format($CGSTAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">SGST ('.$SGSTPer.'%)</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;">'.number_format($SGSTAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
        
        $html1 .='<tr>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">IGST ('.$IGSTPer.'%)</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;">'.number_format($IGSTAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
            
        
        $html1 .='<tr>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">TCS ('.number_format($GetInvoiceItemDetails->tcs, 2, '.', '').'%)</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;">'.number_format($GetInvoiceItemDetails->tcsAmt, 2, '.', '').'</td>';
        $html1 .='</tr>';
        $NetAmount += $GetInvoiceItemDetails->tcsAmt;
        $rndAmt = round($NetAmount);
        $rnd =  $rndAmt - $NetAmount;
        
        $html1 .='<tr>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">Round Off </td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;"><b>'.number_format($rnd, 2, '.', '').'</b></td>';
        $html1 .='</tr>';
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $text = $f->format($rndAmt);
        $html1 .='<tr>';
        $html1 .='<td colspan="7" style="border-right: 1px solid #333;width:65%;border-bottom: 1px solid #333;"><b>(In Words) : </b>'.ucwords($text).' </td>';
        $html1 .='<td colspan="3" style="border-right: 1px solid #333;width:20%;text-align:right;border-bottom: 1px solid #333;">Net Amount</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;"><b>'.number_format($rndAmt, 2, '.', '').'</b></td>';
        $html1 .='</tr>';
      
        
       
        
        $html1 .='<tr>';
        $html1 .='<td colspan="7" style="border-right: 1px solid #333;width:65%;font-size:9px;border-bottom: 1px solid #333;"><b>*Declaration : </b> <br>We declare that this invoice shows the actual price of the goods described
and that all particulars are true and correct<br>
**1) We are not responsible for any breakage,leakage,damage or loss in transit.Our
responsibility ceases after loading lorry,factory gate sale.<br>
2) We hereby certify that food/foods mentioned in this invoice is warranted to be of the
nature and quality which is purport to be.<br>
3) Transportation is being arranged at your request.<br>
4) Payment must be cleared within two days otherwise interest will be charged
@18%p.a.<br>
5) Do not give cash payment.We are not responsible for cash payment.<br>
6) Batch no. details & lab report attach with this bill.<br>
7) Goods once sold will not be taken back or exchanged.
</td>';
        $html1 .='<td colspan="5" style="text-align:right;width:35%;text-align:center;border-bottom: 1px solid #333;"><b> For '.$CompanyName.'</b>
        <br><br><br><br><br><br><br><br><br><br><b>Authorized Signature</b>
        </td>';
        
        $html1 .='</tr>';
        
    $html1 .='<tr>';
        $html1 .='<td colspan="6" style="width:50%;height:60px;"><br><br><br><br><br><b>Printed By : </b>'. get_staff_full_name($this->session->userdata('staffid')).'</td>';
        $html1 .='<td colspan="3" style="width:35%;text-align:left;height:60px;"><br><br><br><br><br><b>Printed On : </b>'._d(date("Y-m-d H:i:s")).'</td>';
        $html1 .='<td colspan="2" style="width:15%;text-align:right;height:60px;"><br><br><br><br><br>Page 1 of 1</td>';
        $html1 .='</tr>';   
        
        $html1 .='</table>';
  
$pdf->lastPage();
ob_clean();
$pdf->writeHTML($html1, true, false, true, false, '');

$pdf->Output($BookingID.'.pdf', 'I');
