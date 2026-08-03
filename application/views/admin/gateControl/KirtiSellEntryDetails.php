<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
tbody#for_uppercase{
    text-transform:uppercase;
}

.btn-top-toolbar {
    position: fixed;
    top: 8.5%;
    padding:5px 0px;
    -webkit-box-shadow: 0 -4px 1px -4px rgba(0,0,0,.1);
    box-shadow: 0 -4px 1px -4px rgba(0,0,0,.1);
    /*background: #50607b;*/
    color:#fff;
    /*width: calc(100% - 211px);*/
    /*width:100%;*/
    z-index: 5;
    border-top: 1px solid #ededed;
}

</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    <div class="col-md-8">
		        <div class="panel_s">
                    <div class="panel-body">
                    <div class="clearfix mtop20"></div>
                        <div class="row">
		                    <div class="col-md-12">
		                        <h4>Gate Control Details</h4>
		                        <div class="table-purchase_request tableFixHead2">
                                    <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                                    
                                    <?php $status = $details->status;
                                        $IsCD = $details->IsCD;
                                        $IsPayment = $details->IsPayment;
                                    ?>
                                    <input id="Main_id" value="<?php echo $details->id; ?>" hidden>
                                    <tbody id="for_uppercase">
                                        <tr>
                                            <td><b>Account ID : </b></td>
                                            <td><?php echo $details->AccountID; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Booking ID : </b></td>
                                            <td><b><?php echo $details->BookingID; ?></b></td>
                                        </tr>
                                        <tr>
                                            <?php 
                                                if($details->TType == 'S'){
                                                    if($details->status == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }else if($details->status == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }else if($details->status == 3){
                                                        $status_val = "EMPTY VEHICLE WEIGHT DONE";
                                                    }else if($details->status == 4){
                                                        $status_val = "LOADING IN PROGRESS ";
                                                    }else if($details->status == 5){
                                                        $status_val = "LOADING FINISHED ";
                                                    }else if($details->status == 6){
                                                        $status_val = "LOADING QC DONE";
                                                    }else if($details->status == 7){
                                                        $status_val = "GROSS WEIGHT CAPTURED";
                                                    }else if($details->status == 8){
                                                        $status_val = "PAYMENT DONE";
                                                    }else if($details->status == 9){
                                                        $status_val = "GATE OUT GANERATED";
                                                    }else if($details->status == 10){
                                                        $status_val = "MARK AS EXIT";
                                                    }
                                                }
                                            ?>
                                            <td><b>Status : </b></td>
                                            <td><?php echo $status_val; ?></td>
                                        </tr>
                                        <tr>
                                            <?php 
                                                if($details->CustomerType == 1){
                                                    $PartyType = 'Farmer';
                                                }
                                                if($details->CustomerType == 2){
                                                    $PartyType = 'Broker';
                                                }
                                                if($details->CustomerType == 3){
                                                    $PartyType = 'Trader';
                                                }
                                                if($details->CustomerType == 4){
                                                    $PartyType = 'Corporate/Processor';
                                                    
                                                }
                                            ?>
                                            <td><b>Party Type : </b></td>
                                            <td><?php echo $PartyType; ?></td>
                                        </tr>
                                        <tr>
                                            <?php 
                                                if($details->company != ''){
                                                    $PartyName = $details->company;
                                                }
                                                else{
                                                    $PartyName = $details->firstname.' '.$details->lastname;
                                                }
                                            ?>
                                            <td><b>Party Name : </b></td>
                                            <td><?php echo $PartyName; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Item ID : </b></td>
                                            <td><?php echo $details->ItemID; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Item Name : </b></td>
                                            <td><?php echo $details->ItemName; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>ASN By : </b></td>
                                            <td><?php echo ($SName['asn_by']->firstname.' '.$SName['asn_by']->lastname) ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>ASN Date: </b></td>
                                            <td><?php echo _d($details->asn_date); ?></td>
                                        </tr>
                                        <?php 
                                            if(($details->status == 1) || ($details->status > 1)){
                                                ?><tr>
                                                    <td><b>ASN : </b></td>
                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewAsn/<?php echo $details->BookingID."/".$details->ASNID; ?>" target="_blank">View ASN</a></td>
                                                </tr><?php
                                            }
                                        ?>
                                        <?php 
                                            if(($details->status == 2) || ($details->status > 2)){
                                                ?>
                                                <tr>
                                                    <td><b>Gate In Pass : </b></td>
                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewGetInPass/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" target="_blank">View Gate In Pass</a></td>
                                                </tr><?php
                                            }
                                        ?>
                                        <tr>
                                            <td><b>ASN Quantity(MT): </b></td>
                                            <td><?php echo number_format($details->Asn_WT_MT, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>ASN Quantity(Bag): </b></td>
                                            <td><?php echo number_format($details->quantity, 2, '.', ''); ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Gate In By : </b></td>
                                            <td><?php echo ($SName['gate_in_by']->firstname.' '.$SName['gate_in_by']->lastname) ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Gate In Date : </b></td>
                                            <td><?php echo _d($details->gate_in_date); ?></td>
                                        </tr>
                                    </tbody>
                                    </table>  
                                </div>
		                    </div>
		                </div>
		                
		                <!--------- For Kirti Sell ----------->
		                
		                <?php 
		                    if($details->TType == 'S'){ ?>
    		                
    		                <?php
    		                    if(($status == 3) || ($status > 3)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Empty Vehicle Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Total Weight</th>
            		                                    <th>Top Image</th>
            		                                    <th>Front Image</th>
            		                                    <th>Side Image</th>
            		                                    <th>Loaded By</th>
            		                                    <th>Loaded Date-Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->TareWeight ?></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>" target="_blank">View Image</a></td>
            		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->TWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
    		                <?php
    		                    if(($status > 4) || ($status == 4)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Loading Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Layer No.</th>
            		                                    <th>Quantity</th>
            		                                    <th>Unit</th>
            		                                    <th>Done By</th>
            		                                    <th>Done Date</th>
            		                                    <?php 
            		                                    $QCName = $layers[0]["parameter_detail"];
            		                                    foreach($QCName as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                    <th>QC Done By</th>
            		                                    <th>QC Done Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <?php
                		                                foreach($layers as $key=>$value){ 
                		                                ?>
                		                                    <tr>
                    		                                    <td><?php echo $value['layer_number'] ?></td>
                    		                                    <td><?php echo $value['qty'] ?></td>
                    		                                    <td><?php echo $value['unit'] ?></td>
                    		                                    <td><?php echo ($value['firstname'].' '.$value['lastname']) ?></td>
                    		                                    <td><?php echo _d($value['Transdate']); ?></td>
                    		                                    
                    		                                    <?php 
                    		                                        $m = 0; 
                    		                                        $TransDate = "";
                    		                                        $UserID = "";
                    		                                    ?>
                    		                                        <?php foreach($value['parameter_detail'] as $key3=>$value3){ ?>
                    		                                            <?php //if($value3['ItemParameterID'] == $value2['ItemParameterID']){
                    		                                                $m++;
                    		                                                $TransDate = $value3['TransDate'];
                    		                                                $UserID = $value3['firstname'].' '.$value3['lastname'];
                    		                                            ?>
                    		                                                <td><?php echo $value3['ParameterValue']; ?></td>
                    		                                        <?php } ?>
                    		                                     <?php
                    		                                        if($m > 0){
                    		                                            ?>
                    		                                            <td><?php echo $UserID;?></td>
                    		                                            <td><?php echo _d($TransDate);?></td>
                    		                                            <?php
                    		                                        }
                    		                                     ?>
                    		                                     
                    		                                </tr>
                		                            <?php  }
            		                                ?>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
    		                <?php } ?>
    		                
    		                <?php
    		                    if(($status > 7) || ($status == 7)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Loaded Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Loaded Weight</th>
            		                                    <th>Top Image</th>
            		                                    <th>Front Image</th>
            		                                    <th>Side Image</th>
            		                                    <th>loaded By</th>
            		                                    <th>loaded Date-Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->LoadedWeight ?></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlTopImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlFrontImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VHLSideImage ?>">View Image</a></td>
            		                                    <td><?php echo ($staffName['LWUserID']->firstname.' '.$staffName['LWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->LWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php 
            		            if($status == 7){ ?>
            		            <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Payment Details</h4>
    		                            
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <div class="row">
            		                        <form id="final_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/SaveKirtiSellPayment" enctype="multipart/form-data">
		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
		                                        <input type="text" name="CenterID" value="<?php echo $details->CenterID ?>" hidden>
		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
		                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
		                                        <input type="text" name="SalesID" value="<?php echo $details->SalesID ?>" hidden>
		                                        <input type="text" name="PartyName" value="<?php echo $PartyName; ?>" hidden>
            		                            <div class="col-md-3">
            		                                <?php 
            		                                    $total_taxable_amount = $details->basic_rate * ($details->LoadedWeight - $details->TareWeight);
            		                                    $GstAmt = ($total_taxable_amount * $details->taxrate) / 100;
            		                                    $total_payable_amount = $total_taxable_amount + $GstAmt;
            		                                ?>
            		                                
            		                                <div class="form-group" app-field-wrapper="payment_amount">
                                                        <small class="req text-danger">* </small>
                                                        <label for="payment_amount" class="control-label">Payment Amount</label>
                                                        <input type="text" name="payment_amount" id="payment_amount" value="<?php echo $total_payable_amount; ?>" class="form-control" >
                                					    <input type="text" hidden name="payment_amounthidden"  id="payment_amounthidden" value="<?php echo $total_payable_amount; ?>"  >
                                					</div>
            		                            </div>
            		                            <div class="col-md-3">
            		                                <div class="form-group" >
                                                        <br>
                		                                <button id="saveBtn" type="submit" class="btn btn-info">Make Payment</button> 
            		                                </div>
            		                            </div>
            		                            </form>
            		                        </div>
            		                    </div>
            		                </div>
            		                
            		        <?php } ?>
            		        
            		        <?php 
            		            if($status == 8){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gate Out Pass</h4>
            		                    <div class="col-md-12" style="padding:0px;">
    		                                    <button class="GenerateGateOut btn btn-info" id="GenerateGateOut" type="button">Generate Gate Out</button>
            		                        <!--
            		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" onclick="setTimeout('location.reload(true);', 2000);">Generate Gate Out</a>-->
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php
    		                    if($status >= 9){ ?>
    		                        <div class="row" style="margin:auto;width:100%;margin-top:2%;">
    		                            <h4>Gate Out Pass &nbsp;&nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" target="_blank">View Gate Out Pass</a></h4>
    		                            <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th style="width:20%">Gate Out By</th>
            		                                    <th>Gate Out Date</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo($SName['gate_out_by']->firstname.' '.$SName['gate_out_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->gate_out_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		        <?php } ?>
            		        <?php
    		                    if($status == 9){ ?>              
                                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
                                            <h4>Mark Vehicle Exit</h4>
                		                    <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExitKirtiSell">
                		                        <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
                		                          <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                          <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;" >Mark Exit</button>
                		                    </form>
        		                        </div>  
            		          <?php } ?>
            		          <?php
    		                    if($status >= 10){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Exit Marked</h4>
    		                            <div class="col-md-12" style="padding:0px;margin-bottom:20px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th style="width:20%">Exit By</th>
            		                                    <th>Exit Date</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo ($SName['exit_by']->firstname.' '.$SName['exit_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->exit_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		          <?php } ?>
            		          
            		           <?php
                                if ($status >= 10) { ?>
                                    <div class="row">
                                        <h4><a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewSellPayment/<?php echo $details->BookingID . '/' . $details->Gate_in_ID; ?>"> View Invoice</a></h4>
                                    </div>
                                <?php } ?>
            		        
            		        <?php 
            		            }
            		        ?>
                    </div>
                </div>
		    </div>
		</div>
		<div class="col-md-4">
    	    <div class="btn-top-toolbar bottom-transaction sm:tw-flex sm:tw-items-center sm:tw-justify-between">
                <div class="col-md-6">
                    <a href="#" class="btn btn-success mright5" data-toggle="tooltip" data-title="page reload" onclick="reloadCurrentPage(); return false;" data-original-title="" title="">
                        <i class="fa fa-refresh"> &nbsp;&nbsp;&nbsp;&nbsp;Reload Page</i>
                    </a>
                </div>
            </div>
    	</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
    function reloadCurrentPage(){
        location.reload();
    }
    
    $('.exitBtn').click(function(){
        $('#exit_form').submit();
    });
    </script>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
    }
    
    $('#payment_amount').on('keypress',function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        var inputCheck = $('#payment_amounthidden').val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2 )) {
            event.preventDefault();
        }
        
    });
    
     $('#payment_amount').on('keyup',function (event) {
         var input = $('#payment_amount').val();
        var inputCheck = $('#payment_amounthidden').val();
         if(input < inputCheck){
            $('#saveBtn').prop('disabled', true)
        }else{
            $('#saveBtn').prop('disabled', false)
        }
     });
     
     $('#payment_amount').on('keydown',function (event) {
         var input = $('#payment_amount').val();
        var inputCheck = $('#payment_amounthidden').val();
         if(input < inputCheck){
            $('#saveBtn').prop('disabled', true)
        }else{
            $('#saveBtn').prop('disabled', false)
        }
     });
     
    $('#GenerateGateOut').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#TareWeight').val();
        var KYC = $('#KYC').val();
        var BookingID = $('#BookingID').val();
        var GateINID = $('#GateINID').val();
        var BookingType = $('#BookingType').val();
        
        if(KYC < 6){
            alert('please complate KYC first');
        }else if(TareWeight <= 0){
            alert('please enter Tare Weight');
        }else if(GrossWeight <= 0){
            alert('please enter Gross Weight');
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/generateGateOut",
                dataType:"json",
                method:"POST",
                data:{BookingID:BookingID,GateINID:GateINID,BookingType:BookingType},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                    if(r == true){
                        window.open("<?php echo admin_url(); ?>GateControl/viewGateOut/"+BookingID+"/"+GateINID, '_blank');
                        window.location.reload();
                    }else{
                        window.location.reload();
                    }
                }
            });
        }
    });
    
</script>
<script>
$(document).ready(function(){
    var unit = $('#unit_val').val();
    var quantity = $('#quantity').val();
    $('#unit').val(unit).selectpicker('refresh');
    $('#qty').val(quantity);
});
</script>
<script>
$(document).ready(function(){
    $('#payment_approve').change(function(){
        var payment = $('#payment_approve :selected').val();
        var id = $('#id').val();
        
        if((payment != '') && (id != '')){
            if (confirm("Do you want to Update Payment Status?") == true) {
			    $('#approve_payment_form').submit();
    		} else {
    			return false;
    		}
        }
    }); 
});
</script>

</body>
</html>
