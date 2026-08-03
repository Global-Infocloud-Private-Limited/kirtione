<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	
	$dimensions = $pdf->getPageDimensions();
	
	$pdf->SetMargins(3, 0, 3, 0);
	$pdf->Ln(0);
	$getItemList = GetItemDetailsFrK1GatepassBYChallan($invoice->ChallanID);
	// print_r($getItemList);
	$route_detail = '';
	$PlantDetail = GetPlantDetails($invoice->PlantID,$invoice->FY);
	$user_detail = '';
	$user_detail2 = '';
	
	$count = 0;
	$count_order = count($get_order_list);
	
	$html = '<div>
	<br>
	<table style="width: 100%; font-size:12px;font-weight:700;" cellspacing="1" cellpadding="4" border="1">
	<tr>
	<td colspan="10" style="text-align:center;">
	<span style="text-align:center;font-size:12px;padding:0px;margin:0px;"><b><u>Gate Pass</u> </b></span><br>
	<span style="font-size:14px;font-weight:700;"> <b>'.$PlantDetail->FIRMNAME.'  </b></span><br>
	<span><b>'.$PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2.'</b></span><br>
	<span><b>(GSTIN '.$PlantDetail->GSTNO.') Contact No. : '.$PlantDetail->PHONENO.'</b></span>
	</td>
	</tr>
	<tr>
	<td style="border-left: 0px solid #333;" colspan="2"><b>Challan ID</b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '. $invoice->ChallanID .'</b></td>
	
	<td style="border-left: 0px solid #333;" colspan="2"><b>Party Name. </b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '.$invoice->company.'</b></td>
	</tr>
	<tr>
	<td style="border-left: 0px solid #333;" colspan="2"><b>Date</b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '. _d(substr($invoice->Transdate,0,10)) .'</b></td>
	<td style="border-left: 0px solid #333;" colspan="2"><b>Vehicle No. </b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '. $invoice->vehicleno.'</b></td>
	</tr>
	<tr>
	<td style="border-left: 0px solid #333;" colspan="2"><b>Center</b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '. $invoice->CenterName.'</b></td>
	<td style="border-left: 0px solid #333;" colspan="2"><b>Driver Name</b></td>
	<td style="border-right: 1px solid #333;" colspan="3"> <b>: '. $invoice->DriverName.'</b></td>
	
	</tr>
	<tr><td colspan="10"></td></tr>
	<tr>
	<td align="center;"><b>Sr.No.</b></td>
	<td colspan="6"><b>Item Name</b></td>
	<td align="center;"><b>Packing</b></td>
	<td align="center;"><b>Supplied In</b></td>
	<td align="right;"><b>Qty</b></td>
	</tr>';
    
    $empty_height= 750;   
    $TotalQty = 0;
    $TotalCases = 0;
    $sr = 1;
    foreach ($getItemList as $key => $ItemDetails) {
        if((int) $ItemDetails['OrderQty'] > 0){
            if($sr>1){
                $empty_height = $empty_height - 25;
			}
                $TotalQty += $ItemDetails['OrderQty'];
            
            $html .= '<tr>'; 
            $html .= '<td style="text-align:center;"><b>'.$sr.'</b></td>'; 
            $html .= '<td class="description" align="left;" colspan="6"><b>'.$ItemDetails['ProductName'].'</b></td>';
            $html .= '<td class="description" align="center;"><b>'. (int) $ItemDetails['PackingQty'].'</b></td>';
			$html .= '<td class="description" align="center;"><b>'.$ItemDetails['SuppliedIn'].'</b></td>';
            $html .= '<td class="description" align="right;"><b>'. (int) $ItemDetails['OrderQty'] .'</b></td>';
            $html .= '</tr>';
            $sr++;
		}
        
	}
    
	
    $html .= '<tr>
	<td colspan="9"><b>Total Qty</b></td>
	<td align="right;"><b>'.$TotalQty.'</b></td>
	</tr>';
	// $html .= '<tr>
	// <td colspan="9"><b>Total Cases / Gatta</b></td>
	// <td align="right;"><b>'.$TotalCases.'</b></td>
	// </tr>';
	//$html .= '<tr><td colspan="10" style="height:'.$empty_height.'px;"></td></tr>';
	$html .= '</table></div>';
	$pdf->writeHTML($html, true, false, false, false, '');
?>