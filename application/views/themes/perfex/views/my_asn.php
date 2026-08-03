<?php

defined('BASEPATH') or exit('No direct script access allowed');

$pdf->SetMargins(3, 0, 3, 0);
$pdf->Ln(0);
// $getAsnList = GetAsnDetailsFrGateIn($AsnDetails->BookingID);
$pdf->AddPage();
$html2 = '<h1>Hello</h1>';
// $html =
//     '<h1 class="text-center bg-info">ASN DETAILS</h1>
//     <div class="container" style="margin-top:5%;">
//         <div class="row">
//             <div class="col-md-10">
//                 <p><b>ASN ID: </b>'.$getAsnList.''.$AsnDetails->id.'</p>
//             </div>
//             <div class="col-md-2">
//                 <p><b>Date: </b>'.substr($AsnDetails->generate_date,0,-8).'</p>
//             </div>
//         </div>
//         <div class="row">
//             <div class="col-md-10">
//                 <p><b>AccountID: </b>'.$AsnDetails->AccountID.'</p>
//             </div>
//             <div class="col-md-2">
//                 <p><b>Time: </b>'.substr($AsnDetails->generate_date,10).'</p>
//             </div>
//         </div>
//         <div class="row">
//             <div class="col-md-9">
//                 <p><b>BookingID: </b>'.$AsnDetails->BookingID.'</p>
//             </div>    
//         </div>
//         <div class="row" style="width:100%;margin:auto;margin-top:3%;">
//             <div class="">
//                 <table class="table table-bordered">
//                     <thead>
//                         <tr>
//                             <th>Party Type</th>
//                             <th>Party Name</th>
//                             <th>Item ID</th>
//                             <th>Item Name</th>
//                             <th>Quantity</th>
//                             <th>Unit</th>
//                         </tr>
//                     </thead>
//                     <tbody>
//                         <tr>
//                             <td>'.$AsnDetails->Name.'</td>
//                             <td>'.$AsnDetails->PartyName.'</td>
//                             <td>'.$AsnDetails->ItemID.'</td>
//                             <td>'.$AsnDetails->ItemName.'</td>
//                             <td>'.$AsnDetails->quantity.'</td>
//                             <td>'.$AsnDetails->unit.'</td>
//                         </tr>
//                     </tbody>
//                 </table>
//             </div>
//         </div>
//         <div class="row">
//             <center><img src=base_url()."/assets/media/qrcode/"'.$QR.'"></center>
//         </div>
//     </div>';
    $pdf->writeHTMLCell($html2, true, false, false, false, '');
?>