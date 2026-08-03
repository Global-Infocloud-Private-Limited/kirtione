<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	#AccountID {
    text-transform: uppercase;
	}
	#table-purchase_request td:hover {
    cursor: pointer;
	}
	#table-purchase_request tr:hover {
    background-color: #ccc;
	}

    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
    h4{
        color:50607b;
    }
</style>
<div id="wrapper">
    <div class="content">
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
                                    if($OrderDetails->IsApprove == 'Y'){
                                        $status = 'ACCEPTED';
                                    }
                                    if($OrderDetails->IsApprove == 'Y' && $OrderDetails->ClientApprove == 'N'){
                                        $status = "Waiting for party approval";
                                    }
                                    if($OrderDetails->IsApprove == 'N'){
                                        $status = 'REJECTED';
                                    }
                                    if($OrderDetails->IsApprove == 'NA'){
                                        $status = 'NO ACTION';
                                    }
                                    if($OrderDetails->status == '2'){
                                        $status = "COMPLATED";
                                    }
                                    if($OrderDetails->status == '3'){
                                        $status = "PARTIAL COMPLATED";
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
                                <tr>
                                    <td>Status</td>
                                    <td colspan="3"><?php echo $status;?></td>
                                </tr>
                            </thead>
                        </table>   
                    </div>
    		        </div>
                </div>
                
                
                    <hr>
                <div class="row" style="margin:auto;width:100%;">
                    <div class="col-md-6">
                        <h4>Transaction Details</h4>
                        <hr>
                        <div class="table-purchase_request tableFixHead2">
                            <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr no</th>
                                        <th>ASNID</th>
                                        <th>Gate Pass No</th>
                                        <th>TransDate</th>
                                        <th>ItemID</th>
                                        <th>Net Weight(MT)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <?php $sr = 1; 
                                    $gate_in_list = array();
                                ?>
                                <tbody>
                                    <?php foreach($OrderList as $key=>$value){ ?>
                                        <tr class="GetDetails" data-id="<?php echo $value['id']; ?>">
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
                                                array_push($gate_in_list,$value['Gate_in_ID'])
                                             ?>
                                            <?php 
                                                if($value['TType'] == 'P'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }elseif($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }elseif($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }elseif($value['status'] == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }elseif($value['status'] == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }elseif($value['status'] == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }elseif($value['status'] == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }elseif($value['status'] == 7){
                                                        $status_val = "QC DONE ";
                                                    }elseif($value['status'] == 8){
                                                        $status_val = "CLEANING DONE ";
                                                    }elseif($value['status'] == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }elseif($value['status'] == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }elseif($value['status'] == 11){
                                                        $status_val = "READY TO EXIT";
                                                    }elseif($value['status'] == 12){
                                                        $status_val = "EXIT ";
                                                    }elseif($value['status'] == 13){
                                                        $status_val = "PAYMENT APPROVED ";
                                                    }
                                                }
                                                        
                                                if($value['TType'] == 'D'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }elseif($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }elseif($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }elseif($value['status'] == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }elseif($value['status'] == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }elseif($value['status'] == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }elseif($value['status'] == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }elseif($value['status'] == 7){
                                                        $status_val = "QC DONE ";
                                                    }elseif($value['status'] == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }elseif($value['status'] == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }elseif($value['status'] == 11){
                                                        $status_val = "READY TO EXIT ";
                                                    }elseif($value['status'] == 12){
                                                        $status_val = "EXIT";
                                                    }elseif($value['status'] == 13){
                                                        $status_val = "PAYMENT APPROVED";
                                                    }
                                                }
                                                        
                                                if($value['TType'] == 'W'){
                                                    if($value['status'] == 0){
                                                        $status_val = "NO ACTION";
                                                    }elseif($value['status'] == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }elseif($value['status'] == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }elseif($value['status'] == 3){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }elseif($value['status'] == 4){
                                                        $status_val = "LOADING IN PROGRESS ";
                                                    }elseif($value['status'] == 5){
                                                        $status_val = "LOADING FINISHED ";
                                                    }elseif($value['status'] == 6){
                                                        $status_val = "QC DONE ";
                                                    }elseif($value['status'] == 7){
                                                        $status_val = "FINAL QC DONE";
                                                    }elseif($value['status'] == 8){
                                                        $status_val = "GROSS WEIGHT CAPTURED";
                                                    }elseif($value['status'] == 9){
                                                        $status_val = "READY TO EXIT";
                                                    }elseif($value['status'] == 10){
                                                        $status_val = "EXIT";
                                                    }
                                                }
                                            ?>
                                            <td><?php echo $sr; ?></td>
                                            <td><?php echo $value['ASNID']; ?></td>
                                            <td><?php echo $value['Gate_in_ID']; ?></td>
                                            <td><?php echo _d($value['asn_date']); ?></td>
                                            <td><?php echo $value['ItemID']; ?></td>
                                            <td style="text-align:right"><?php echo number_format($net_weight, 2, '.', ''); ?></td>
                                            <td><?php echo $status_val; ?></td>
                                        </tr>
                                    <?php $sr++; } ?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Disbrusment Details</h4>
                        <hr>
                        <div class="row">
                            <!--<form id="loan_dis_form" method="POST" action="<?php echo admin_url(); ?>GateControl/loan_dis_submit" >-->
	                        <input type="text" id="AccountID" value="<?php echo $OrderDetails->AccountID; ?>" hidden>
                            <input type="text" id="BookingID" value="<?php echo $OrderDetails->BookingID; ?>" hidden>
                            <input type="text" id="TType" value="<?php echo $OrderDetails->TType; ?>" hidden>
    		                    <div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="wr_list">
                                        <small class="req text-danger">* </small>
                                        <label for="wr_list" class="control-label">Select WR</label>
                                        <select class = "selectpicker" multiple name="wr_list" id="wr_list" data-live-search="true">
                                            <option></option>
                                        <?php 
                                            foreach($gate_in_list as $val){
                                        ?>
                                            <option value="<?php echo $val;?>"><?php echo $val;?></option>
                                        <?php
                                            }
                                        ?>
                                        </select>
                					</div>
            					</div>
            					
            					<div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="wr_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="wr_amount" class="control-label">WR value</label>
                                        <input type="text" name="wr_amount" id="wr_amount" readonly class="form-control">
                					</div>
            					</div>
            					
            					<div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="wr_weight">
                                        <small class="req text-danger">* </small>
                                        <label for="wr_weight" class="control-label">WR Weight</label>
                                        <input type="text" name="wr_weight" id="wr_weight" readonly class="form-control">
                					</div>
            					</div>
            					
            					<div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="dis_per">
                                        <small class="req text-danger">* </small>
                                        <label for="dis_per" class="control-label">Disbrusment Percentage</label>
                                        <input type="text" name="dis_per" id="dis_per" class="form-control">
                					</div>
            					</div>
            					
    		                    <div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="dis_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="dis_amount" class="control-label">Disbrusment Amount</label>
                                        <input type="text" name="dis_amount" id="dis_amount" class="form-control">
                					</div>
            					</div>
            					<div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="ROI">
                                        <small class="req text-danger">* </small>
                                        <label for="ROI" class="control-label">Select ROI</label>
                                        <select class = "selectpicker" name="ROI" id="ROI" data-live-search="true">
                                            <option></option>
                                            <option value="10">10%</option>
                                            <option value="11">11%</option>
                                            <option value="12">12%</option>
                                        </select>
                					</div>
            					</div>
                    					
    		                    <div class="col-md-4" style="width:100%;margin:auto;">
                                    <button id="saveloanBtn" class="btn btn-info">Save</button> 
                                </div>
            		        <!--</form>-->
                        </div>
                        
                    </div>
                    
                    
                </div>
            </div>
        </div>       
	</div>
</div>
<?php init_tail(); ?>
<script>
    $('.GetDetails').on('click',function(){ 
        id = $(this).attr("data-id");
        window.open("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+id,'_blank');
    });
    // Calculate amount
    $("#dis_per").keyup(function () {
        var wr_amount = $('#wr_amount').val();
        var val = $(this).val();
        if (val == "") {
            $('#dis_amount').val('0.00');
        } else {
            if (val > 70) {
                alert('please enter less than equal to 70%');
                $('#dis_amount').val('0.00');
                $(this).val('0');
            } else {
                var PayAmt = parseFloat(wr_amount) * (parseFloat(val) / 100);
                $('#dis_amount').val(parseFloat(PayAmt).toFixed(2));
            }
        }
    })
    // Calculate percentage
    $("#dis_amount").keyup(function () {
        var wr_amount = $('#wr_amount').val();
        //var dis_amount = $('#dis_amount').val();
        var wr_amt_limit = wr_amount-(wr_amount*0.30);
        var val = $(this).val();
        if (val == "") {
            $('#dis_per').val('0.00');
        } else {
            if (val > wr_amt_limit) {
                alert('please enter less than equal to 70% of WR value');
                $('#dis_per').val('0.00');
                $(this).val('0');
            } else {
                var dis_per = Math.round((val / wr_amount) * 100);
                $('#dis_per').val(parseFloat(dis_per).toFixed(2));
            }
        }
    })
    // add WR in loan amount
    $('#wr_list').change(function () {
        var wr_list = $('#wr_list').val();
        $.ajax({
            url: "<?php echo admin_url(); ?>GateControl/Ganerate_wr_details",
            dataType: "json",
            method: "POST",
            data: { wr_list: wr_list},
            beforeSend: function () {
                $('#sendrequest').html('Please wait request sending.');
            },
            success: function (data) {
                $('#wr_amount').val(data.total_amount);
                $('#wr_weight').val(data.total_weight);
                $('#dis_per').val('0');
                $('#dis_amount').val('0');
            }
        });
    });
    // Save loan details
    $('#saveloanBtn').click(function () {
        var AccountID = $('#AccountID').val();
        var BookingID = $('#BookingID').val();
        var TType = $('#TType').val();
        var wr_list = $('#wr_list').val();
        var wr_amount = $('#wr_amount').val();
        var wr_weight = $('#wr_weight').val();
        var dis_per = $('#dis_per').val();
        var dis_amount = $('#dis_amount').val();
        var ROI = $('#ROI').val();
        if(isNaN(parseFloat(wr_amount))){
            alert('WR amount not loaded please refresh page');
        }else if(isNaN(parseFloat(wr_weight))){
            alert('WR weight not loaded please refresh page');
        }else if(isNaN(parseFloat(dis_per))){
            alert('please enter loan amount percentage');
        }else if(isNaN(parseFloat(dis_amount))){
            alert('please enter loan amount');
        }else if(isNaN(parseFloat(ROI))){
            alert('please selec loan ROI');
        }else{
            if (confirm('Do you want to add Disbrusment details')) {
                   
                $.ajax({
                    url: "<?php echo admin_url(); ?>GateControl/loan_dis_submit",
                    //dataType: "json",
                    method: "POST",
                    data: { AccountID:AccountID,BookingID:BookingID,TType:TType,wr_list: wr_list,wr_amount:wr_amount,wr_weight:wr_weight,
                        dis_per:dis_per,dis_amount:dis_amount,ROI:ROI
                    },
                    beforeSend: function () {
                        $('#sendrequest').html('Please wait request sending.');
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        }
        
        /**/
    });
</script>
<script type="text/javascript">
   $('#dis_per,#dis_amount').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
        event.preventDefault();
    }
});
</script>