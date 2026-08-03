<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
#table-purchase_request td:hover {
    cursor: pointer;
}
#table-purchase_request tr:hover {
    background-color: #ccc;
}
</style>
<div class="panel_s">
  <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h4>Booking Details</h4>
		        <div class="table-purchase_request tableFixHead2">
                    <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" style="width:70%;">
                        <thead>
                            <?php
                                if($OrderDetails->company == null){
                                    $party_name = $OrderDetails->firstname.' '.$OrderDetails->lastname; 
                                }
                                else{
                                    $party_name = $OrderDetails->company;
                                }
                            ?>
                            <tr>
                                <td>Booking ID : </td>
                                <td><?php echo $OrderDetails->BookingID; ?></td>
                                <td>TransDate : </td>
                                <td><?php echo _d($OrderDetails->TransDate); ?></td>
                            </tr>
                            <tr>
                                <td>AccountID : </td>
                                <td><?php echo $OrderDetails->AccountID; ?></td>
                                <td>Party Name :</td>
                                <td><?php echo $party_name; ?></td>
                            </tr>
                            <tr>
                                <td>Item Name : </td>
                                <td><?php echo $OrderDetails->ItemName; ?></td>
                                <td>Quantity</td>
                                <td><?php echo $OrderDetails->quantity.' '.$OrderDetails->unit; ?></td>
                            </tr>
                        </thead>
                    </table>   
                </div>
		    </div>
        </div>
            <input type="text" id="AccountID" value="<?php echo $OrderDetails->AccountID; ?>" hidden>
            <input type="text" id="BookingID" value="<?php echo $OrderDetails->BookingID; ?>" hidden>
            <input type="text" id="ItemID" value="<?php echo $OrderDetails->ItemID; ?>" hidden>
            <input type="text" id="basic_rate" value="0.00" hidden>
            <input type="text" id="quantity" value="<?php echo $OrderDetails->quantity; ?>" hidden>
            <input type="text" id="unit" value="<?php echo $OrderDetails->unit; ?>" hidden>
            <input type="text" id="TType" value="<?php echo $OrderDetails->TType; ?>" hidden>
            <input type="text" id="TType2" value="<?php echo $OrderDetails->TType2; ?>" hidden>
            
            <div class="row" style="margin:auto;width:100%;">
                <button class="btn btn-info" id="generate_asn">Generate ASN</button>
            </div>
        <div class="row">
            <hr>
        </div>
        <div class="row" style="margin:auto;width:100%;">
            <h4>Transaction Details</h4>
            
            <div class="table-purchase_request tableFixHead2">
                    <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                        <thead>
                            <tr>
                                <th>Sr no</th>
                                <th>ASNID</th>
                                <th>Gate Pass No</th>
                                <th>TransDate</th>
                                <th>ItemID</th>
                                <th>Net Weight (Qtl)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <?php $sr = 1; ?>
                        <tbody>
                            <?php foreach($OrderList as $key=>$value){ ?>
                                <tr class="GetDetails" data-type="<?php echo $value['TType']; ?>" data-asn="<?php echo $value['ASNID']; ?>" data-id="<?php echo $value['Gate_in_ID']; ?>">
                                    <?php 
                                        if(($value['LoadedWeight'] != null) || ($value['LoadedWeight'] != '')){
                                            if(($value['TareWeight'] != null) || ($value['TareWeight'] != '')){
                                                $net_weight = $value['LoadedWeight'] - $value['TareWeight'];
                                            }else{
                                                $net_weight = '0';
                                            }
                                        }else{
                                            $net_weight = '0';
                                        }
                                     ?>
                                    <?php 
                                        if($value['TType'] == 'P'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }
                                                    if($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($value['status'] == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }
                                                    if($value['status'] == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }
                                                    if($value['status'] == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }
                                                    if($value['status'] == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }
                                                    if($value['status'] == 7){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($value['status'] == 8){
                                                        $status_val = "CLEANING DONE ";
                                                    }
                                                    if($value['status'] == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($value['status'] == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }
                                                    if($value['status'] == 11){
                                                        $status_val = "READY TO EXIT";
                                                    }
                                                    if($value['status'] == 12){
                                                        $status_val = "EXIT ";
                                                    }
                                                    if($value['status'] == 13){
                                                        $status_val = "PAYMENT APPROVED ";
                                                    }
                                                }
                                                
                                        if($value['TType'] == 'D'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }
                                                    if($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($value['status'] == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }
                                                    if($value['status'] == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }
                                                    if($value['status'] == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }
                                                    if($value['status'] == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }
                                                    if($value['status'] == 7){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($value['status'] == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($value['status'] == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }
                                                    if($value['status'] == 11){
                                                        $status_val = "READY TO EXIT ";
                                                    }
                                                    if($value['status'] == 12){
                                                        $status_val = "EXIT";
                                                    }
                                                    if($value['status'] == 13){
                                                        $status_val = "PAYMENT APPROVED";
                                                    }
                                                }
                                                
                                        if($value['TType'] == 'W'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }
                                                    if($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($value['status'] == 3){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($value['status'] == 4){
                                                        $status_val = "LOADING IN PROGRESS ";
                                                    }
                                                    if($value['status'] == 5){
                                                        $status_val = "LOADING FINISHED ";
                                                    }
                                                    if($value['status'] == 6){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($value['status'] == 7){
                                                        $status_val = "FINAL QC DONE";
                                                    }
                                                    if($value['status'] == 8){
                                                        $status_val = "GROSS WEIGHT CAPTURED";
                                                    }
                                                    if($value['status'] == 9){
                                                        $status_val = "READY TO EXIT";
                                                    }
                                                    if($value['status'] == 10){
                                                        $status_val = "EXIT";
                                                    }
                                                }
                                    ?>
                                    <td><?php echo $sr; ?></td>
                                    <td><?php echo $value['ASNID']; ?></td>
                                    <td><?php echo $value['Gate_in_ID']; ?></td>
                                    <td><?php echo _d($value['asn_date']); ?></td>
                                    <td><?php echo $value['ItemID']; ?></td>
                                    <td><?php echo $net_weight; ?></td>
                                    <td><?php echo $status_val; ?></td>
                                </tr>
                            <?php $sr++; } ?>
                        </tbody>
                    </table>   
                </div>
    </div>
</div>
<script>
    $('.GetDetails').on('click',function(){ 
        Gate_in_ID = $(this).attr("data-id");
        ASNID = $(this).attr("data-asn");
        TType = $(this).attr("data-type");
        if(Gate_in_ID != ''){
            if(TType == 'P'){
                window.open("<?php echo base_url(); ?>Clients/CropSellDetails/"+Gate_in_ID,'_blank');
            }    
            if(TType == 'D'){
                window.open("<?php echo base_url(); ?>Clients/GetWarehouseBookingDetails/"+Gate_in_ID,'_blank');
            }
            if(TType == 'W'){
                window.open("<?php echo base_url(); ?>Clients/GetWithdrawalDetails/"+Gate_in_ID,'_blank');
            }
        }    
        else{
            if(TType == 'P'){
                window.open("<?php echo base_url(); ?>Clients/CropSellDetailsByASN/"+ASNID,'_blank');
            }    
            if(TType == 'D'){
                window.open("<?php echo base_url(); ?>Clients/GetWarehouseBookingDetailsByASN/"+ASNID,'_blank');
            }
            if(TType == 'W'){
                window.open("<?php echo base_url(); ?>Clients/GetWithdrawalDetailsByASN/"+ASNID,'_blank');
            }
        }
    });
</script>
<script>
    $('#generate_asn').click(function(){
        var AccountID = $('#AccountID').val();
        var BookingID = $('#BookingID').val();
        var ItemID = $('#ItemID').val();
        var basic_rate = $('#basic_rate').val();
        var Quantity = $('#quantity').val();
        var Unit = $('#unit').val();
        var TType = $('#TType').val();
        var TType2 = $('#TType2').val();
        
        if(($('#AccountID').val() != '') && ($('#BookingID').val() != '') && ($('#TType').val() != '') && ($('#TType2').val() != '') && ($('#ItemID').val() != '') && ($('#Quantity').val() != '') && ($('#Unit').val() != '')){
            $.ajax({
                url:"<?php echo base_url(); ?>Clients/saveGateControl",
                method: "POST",
                dataType: "JSON",
                data:{
                    AccountID: AccountID,
                    BookingID: BookingID,
                    ItemID: ItemID,
                    basic_rate:basic_rate,
                    Quantity: Quantity,
                    Unit: Unit,
                    TType:TType,
                    TType2:TType2
				},
                success:function(data){
                    console.log(data);
                    if(data != null){
                        var ASNID = data.ASNID;
                        window.open("<?php echo base_url(); ?>Clients/generateAsn/"+BookingID+"/"+ASNID, "_blank");
                        window.location.replace("<?php echo base_url(); ?>Clients/GetBookingListDetails/"+BookingID);
					}
					else{
					    alert('One Order is already in Process !');
					}
				}    
			});
		}
        else{
            alert("Select Required Data !");   
		}
    });
</script>