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
                                    <!--<?php echo '<pre>';print_r($details); ?>-->
                                    <!--<?php echo '<pre>';print_r($layers); ?>-->
                                    <!--<?php echo '<pre>';print_r($peripheral); ?>-->
                                    <?php $status = $details->status; ?>
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
                                                if($details->TType == 'P'){
                                                    if($details->status == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($details->status == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($details->status == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }
                                                    if($details->status == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }
                                                    if($details->status == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }
                                                    if($details->status == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }
                                                    if($details->status == 7){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($details->status == 8){
                                                        $status_val = "CLEANING DONE ";
                                                    }
                                                    if($details->status == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($details->status == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }
                                                    if($details->status == 11){
                                                        $status_val = "READY TO EXIT";
                                                    }
                                                    if($details->status == 12){
                                                        $status_val = "EXIT ";
                                                    }
                                                    if($details->status == 13){
                                                        $status_val = "PAYMENT APPROVED ";
                                                    }
                                                }
                                                
                                                if($details->TType == 'D'){
                                                    if($details->status == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($details->status == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($details->status == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }
                                                    if($details->status == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }
                                                    if($details->status == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }
                                                    if($details->status == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }
                                                    if($details->status == 7){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($details->status == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($details->status == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }
                                                    if($details->status == 11){
                                                        $status_val = "READY TO EXIT ";
                                                    }
                                                    if($details->status == 12){
                                                        $status_val = "EXIT";
                                                    }
                                                    if($details->status == 13){
                                                        $status_val = "PAYMENT APPROVED";
                                                    }
                                                }
                                                
                                                if($details->TType == 'W'){
                                                    if($details->status == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }
                                                    if($details->status == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }
                                                    if($details->status == 3){
                                                        $status_val = "TARE WEIGHT CAPTURED ";
                                                    }
                                                    if($details->status == 4){
                                                        $status_val = "LOADING IN PROGRESS ";
                                                    }
                                                    if($details->status == 5){
                                                        $status_val = "LOADING FINISHED ";
                                                    }
                                                    if($details->status == 6){
                                                        $status_val = "QC DONE ";
                                                    }
                                                    if($details->status == 7){
                                                        $status_val = "FINAL QC DONE";
                                                    }
                                                    if($details->status == 8){
                                                        $status_val = "GROSS WEIGHT CAPTURED";
                                                    }
                                                    if($details->status == 9){
                                                        $status_val = "READY TO EXIT";
                                                    }
                                                    if($details->status == 10){
                                                        $status_val = "EXIT";
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
                                            <td><b>Quantity : </b></td>
                                            <td><?php echo $details->quantity.' '.$details->unit; ?></td>
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
		                
		                <!--------- For Purchase ----------->
		                
		                <?php 
		                    if($details->TType == 'P'){ ?>
    		                <?php
    		                    if(($status == 3) || ($status > 3)){
    		                        ?><div class="row" style="margin:auto;width:100%;">
    		                            <h4>Peripheral QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                    <th>UserID</th>
            		                                    <th>Date Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <?php
            		                                        
            		                                        $TransDate = $peripheral[0]["TransDate"];
            		                                        $UserID = $peripheral[0]["firstname"].' '.$peripheral[0]["lastname"];
            		                                    ?>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <td><?php echo $value['ParameterValue']; ?></td>
            		                                    <?php } ?>
            		                                    <td><?php echo $UserID;?></td>
            		                                    <td><?php echo _d($TransDate);?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div><?php
    		                    }
    		                ?>
    		                <?php
    		                    if(($status == 4) || ($status > 4)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gross Weight Details</h4>
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
            		                                    <td><?php echo $details->LoadedWeight ?></td>
            		                                    <td><a href="<?php echo base_url().$details->VhlTopImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a href="<?php echo base_url().$details->VhlFrontImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a href="<?php echo base_url().$details->VHLSideImage ?>" target="_blank">View Image</a></td>
            		                                    <td><?php echo ($staffName['LWUserID']->firstname.' '.$staffName['LWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->LWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
    		                <?php
    		                    if(($status > 5) || ($status == 5)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Unloading Details</h4>
    		                            <h5>Total Layers: <?php echo $details->no_of_layers; ?></h5>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Layer No.</th>
            		                                    <th>Quantity</th>
            		                                    <th>Unit</th>
            		                                    <th>Done By</th>
            		                                    <th>Done Date</th>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
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
                    		                                    ?>
                    		                                    <?php foreach($peripheral as $key2=>$value2){
                    		                                        $m = 0; 
                    		                                    $TransDate = "";
                    		                                    $UserID = "";
                    		                                    ?>
                    		                                        <?php foreach($value['parameter_detail'] as $key3=>$value3){ ?>
                    		                                            <?php if($value3['ItemParameterID'] == $value2['ItemParameterID']){
                    		                                                $m++;
                    		                                                $TransDate = $value3['TransDate'];
                    		                                                $UserID = $value3['firstname'].' '.$value3['lastname'];
                    		                                            ?>
                    		                                                <td><?php echo $value3['ParameterValue']; ?></td>
                    		                                                
                    		                                            <?php } ?>
                    		                                            
                    		                                        <?php } ?>
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
    		                    if(($status > 8) || ($status == 8)){ ?>
        		                       <div class="row" style="margin:auto;width:100%;">
        		                            <h4>Cleaning Details</h4>
                		                    <div class="col-md-12" style="padding:0px;">
                		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <th>FM (kg)</th>
                		                                    <th>Cleaning By</th>
                		                                    <th>Cleaning Date-Time</th>
                		                                </tr>
                		                            </thead>
                		                            <tbody>
                		                                <tr>
                		                                    <td><?php echo $details->FMQty ?></td>
                		                                    <td><?php echo ($staffName['FMUserID']->firstname.' '.$staffName['FMUserID']->lastname) ?></td>
                		                                    <td><?php echo _d($details->FMTransDate); ?></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
                		                    </div>
                		                </div>
                		    <?php  } ?>
    		                <?php
    		                    if(($status > 9) || ($status == 9)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Tare Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Tare Weight</th>
            		                                    <th>Top Image</th>
            		                                    <th>Front Image</th>
            		                                    <th>Side Image</th>
            		                                    <th>Unloaded By</th>
            		                                    <th>Unloaded Date-Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->TareWeight ?></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>">View Image</a></td>
            		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->TWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php 
            		            if($status == 9){ ?>
            		            <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/saveFinalQC">
            		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
            		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
            		                                        <input type="text" name="BookingType" value="<?php echo $details->TType ?>" hidden>
            		                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
            		                                        <?php foreach($peripheral as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		                <div class="row" style="width:100%;margin:auto;">
                                        <button id="saveBtn" class="btn btn-info">Save Final QC</button> 
                                    </div>
            		        <?php } ?>
            		        <?php 
            		            if($status >= 10){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewQc/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View QC Slip</a></h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="table table-striped table-bordered" >
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <?php foreach($finalQC as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['id']; ?>" value="<?php echo $value['ParameterValue']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php 
            		            if($status == 10){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gate Out Pass</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>">Generate Gate Out</a>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php
    		                    if($status >= 11){ ?>
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
    		                    if($status == 11){ ?>              
                                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
                                            <h4>Mark Vehicle Exit</h4>
                		                    <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExit">
                		                        <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
                		                          <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                          <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;" >Mark Exit</button>
                		                    </form>
        		                        </div>  
            		          <?php } ?>
            		          <?php
    		                    if($status >= 12){ ?>
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
    		                    if($status == 12){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
            		                    <h4>Payment Approval</h4>
        		                            <div class="col-md-4" style="padding:0px;">
        		                                <form id="approve_payment_form" method="POST" action="<?php echo admin_url(); ?>GateControl/approvePayment">
            		                                <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
            		                                <select id="payment_approve" name="payment_approve" class="selectpicker">
            		                                    <option value="">--Select--</option>
            		                                    <option value="1">Approve Payment</option>
            		                                    <option value="2">Hold Payment</option>
            		                                </select>
        		                                </form>
        		                            </div>
            		                </div>
            		        <?php } ?>
            		        <?php
    		                    if($status == 13){ ?>
    		                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
    		                            <h4>Payment Approval &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewPayment/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View Payment Slip</a></h4>
    		                            <div class="col-md-4" style="padding:0px;">
    		                                <?php 
    		                                    if($details->payment_done == 1){
    		                                        $payment = 'Payment Approved';
    		                                    }
    		                                    if($details->payment_done == 2){
    		                                        $payment = 'Payment On Hold';
    		                                    }
    		                                ?>
    		                                <input type="text" value="<?php echo $payment ?>" readonly>
    		                            </div>
    		                        </div>  
    		                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
    		                            <h4>Account Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th style="width:20%">Approved By</th>
            		                                    <th>Approved Date</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo ($SName['payment_approved_by']->firstname.' '.$SName['payment_approved_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->payment_approved_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		    
            		    <!--------- For Deposit ----------->
            		    
            		    <?php } 
            		        elseif($details->TType == 'D'){ ?>
    		                <?php
    		                    if(($status == 3) || ($status > 3)){
    		                        ?><div class="row" style="margin:auto;width:100%;">
    		                            <h4>Peripheral QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                    <th>UserID</th>
            		                                    <th>Date Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <?php
            		                                        
            		                                        $TransDate = $peripheral[0]["TransDate"];
            		                                        $UserID = $peripheral[0]["firstname"].' '.$peripheral[0]["lastname"];
            		                                    ?>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <td><?php echo $value['ParameterValue']; ?></td>
            		                                    <?php } ?>
            		                                    <td><?php echo $UserID;?></td>
            		                                    <td><?php echo _d($TransDate);?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div><?php
    		                    }
    		                ?>
    		                <?php
    		                    if(($status == 4) || ($status > 4)){
    		                        ?><div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gross Weight Details</h4>
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
            		                </div><?php
    		                    }
    		                ?>
    		                <?php
    		                    if(($status > 5) || ($status == 5)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Unloading Details</h4>
    		                            <h5>Total Layers: <?php echo $details->no_of_layers; ?></h5>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Layer No.</th>
            		                                    <th>Quantity</th>
            		                                    <th>Unit</th>
            		                                    <th>Done By</th>
            		                                    <th>Done Date</th>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
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
                    		                                    ?>
                    		                                    <?php foreach($peripheral as $key2=>$value2){
                    		                                        $m = 0; 
                    		                                    $TransDate = "";
                    		                                    $UserID = "";
                    		                                    ?>
                    		                                        <?php foreach($value['parameter_detail'] as $key3=>$value3){ ?>
                    		                                            <?php if($value3['ItemParameterID'] == $value2['ItemParameterID']){
                    		                                                $m++;
                    		                                                $TransDate = $value3['TransDate'];
                    		                                                $UserID = $value3['firstname'].' '.$value3['lastname'];
                    		                                            ?>
                    		                                                <td><?php echo $value3['ParameterValue']; ?></td>
                    		                                                
                    		                                            <?php } ?>
                    		                                            
                    		                                        <?php } ?>
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
    		                    if(($status > 9) || ($status == 9)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Tare Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Tare Weight</th>
            		                                    <th>Top Image</th>
            		                                    <th>Front Image</th>
            		                                    <th>Side Image</th>
            		                                    <th>Unloaded By</th>
            		                                    <th>Unloaded Date-Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->TareWeight ?></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>">View Image</a></td>
            		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->TWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php 
            		            if($status == 9){ ?>
            		            <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/saveFinalQC">
            		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
            		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
            		                                        <input type="text" name="BookingType" value="<?php echo $details->TType ?>" hidden>
            		                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
            		                                        <?php foreach($peripheral as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		                <div class="row" style="width:100%;margin:auto;">
                                        <button id="saveBtn" class="btn btn-info">Save Final QC</button> 
                                    </div>
            		        <?php } ?>
            		        <?php 
            		            if($status >= 10){ ?>
            		            <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewQc/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View QC Slip</a></h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="table table-striped table-bordered" >
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <?php foreach($finalQC as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['id']; ?>" value="<?php echo $value['ParameterValue']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>          
            		        <?php 
            		            if($status == 10){ ?>
            		                <div class="row" style="width:100%;margin:auto;">
                                        <button id="updateBtn" class="btn btn-info">Update Final QC</button> 
                                    </div>
                                    <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gate Out Pass</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>">Generate Gate Out</a>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        <?php 
            		            if($status >= 11){ ?>
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
            		                                    <td><?php echo ($SName['gate_out_by']->firstname.' '.$SName['gate_out_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->gate_out_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		        <?php } ?>
            		        <?php 
            		            if($status == 11){ ?>
                                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
                                            <h4>Mark Vehicle Exit</h4>
                		                    <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExit">
                		                        <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
                		                          <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                          <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;" >Mark Exit</button>
                		                    </form>
        		                        </div>  
            		        <?php } ?>      
            		        <?php
    		                    if($status >= 12){ ?>
    		                        <div class="row" style="margin:auto;width:100%;margin-top:2%;">
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
    		                    if($status == 12){ ?>              
    		                            <h4>Payment Approval</h4>
        		                            <div class="col-md-4" style="padding:0px;">
        		                                <form id="approve_payment_form" method="POST" action="<?php echo admin_url(); ?>GateControl/approvePayment">
            		                                <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
            		                                <select id="payment_approve" name="payment_approve" class="selectpicker">
            		                                    <option value="">--Select--</option>
            		                                    <option value="1">Approve Payment</option>
            		                                    <option value="2">Hold Payment</option>
            		                                </select>
        		                                </form>
        		                            </div>
    	                            </div>
            		          <?php } ?>
            		        <?php
    		                    if($status == 13){ ?>
    		                       <div class="row" style="margin:auto;width:100%;margin-top:2%">
    		                            <h4>Payment Approval &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewPayment/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View Payment Slip</a></h4>
    		                            <div class="col-md-4" style="padding:0px;">
    		                                <?php 
    		                                    if($details->payment_done == 1){
    		                                        $payment = 'Payment Approved';
    		                                    }
    		                                    if($details->payment_done == 2){
    		                                        $payment = 'Payment On Hold';
    		                                    }
    		                                ?>
    		                                <input type="text" value="<?php echo $payment ?>" readonly>
    		                            </div>
    		                        </div>  
    		                        <div class="row" style="margin:auto;width:100%;margin-top:2%">
    		                            <h4>Account Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th style="width:20%">Approved By</th>
            		                                    <th>Approved Date</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo ($SName['payment_approved_by']->firstname.' '.$SName['payment_approved_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->payment_approved_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		   
            		    <!--------- For Withdrawal ----------->
            		    
            		    <?php } 
            		        elseif($details->TType == 'W'){ ?>
    		                <?php
    		                    if(($status == 3) || ($status > 3)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Tare Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Tare Weight</th>
            		                                    <th>Top Image</th>
            		                                    <th>Front Image</th>
            		                                    <th>Side Image</th>
            		                                    <th>Unloaded By</th>
            		                                    <th>Unloaded Date-Time</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->TareWeight ?></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>">View Image</a></td>
            		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
            		                                    <td><?php echo _d($details->TWTransDate); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		        <?php } ?>
    		                <?php
    		                    if(($status == 4) || ($status > 4)){ ?>
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
            		                                    <?php foreach($withdrawalQc as $key=>$value){ ?>
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
                    		                                     <?php foreach($withdrawalQc as $key2=>$value2){
                    		                                        $m = 0; 
                        		                                    $TransDate = "";
                        		                                    $UserID = "";
                        		                                    foreach($value['parameter_detail'] as $key3=>$value3){
                    		                                             if($value3['ItemParameterID'] == $value2['ItemParameterID']){
                    		                                                $m++;
                    		                                                $TransDate = $value3['TransDate'];
                    		                                                $UserID = $value3['firstname'].' '.$value3['lastname'];
                    		                                            ?>
                    		                                                <td><?php echo $value3['ParameterValue']; ?></td>
                    		                                                
                    		                                            <?php } ?>
                    		                                            
                    		                                        <?php } ?>
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
            		            if($status == 6){ ?>
                                    <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($withdrawalQc as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/saveFinalQCWithdrawal">
            		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
            		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
            		                                        <input type="text" name="BookingType" value="<?php echo $details->TType ?>" hidden>
            		                                        <?php foreach($withdrawalQc as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		                <div class="row" style="width:100%;margin:auto;">
                                        <button id="saveBtn" class="btn btn-info">Save Final QC</button> 
                                    </div>            		                
            		        <?php } ?>
            		        <?php
    		                    if($status == 7){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewQc/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View QC Slip</a></h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="table table-striped table-bordered" >
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($withdrawalQc as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateFinalQCWithdrawal">
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <?php foreach($finalQC as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['id']; ?>" value="<?php echo $value['ParameterValue']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
            		                <div class="row" style="width:100%;margin:auto;">
                                        <button id="updateBtn" class="btn btn-info">Update Final QC</button> 
                                    </div>
    		                <?php } ?>
    		                <?php
    		                    if(($status > 8) || ($status == 8)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details &nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewQc/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View QC Slip</a></h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="table table-striped table-bordered" >
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($withdrawalQc as $key=>$value){ ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <?php foreach($finalQC as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['id']; ?>" value="<?php echo $value['ParameterValue']; ?>"></td>
            		                                        <?php } ?>
            		                                    </form>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gross Weight Details</h4>
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
    		                    if($status == 8){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gate Out Pass</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOutWithdrawal/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>">Generate Gate Out</a>
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
            		                                    <td><?php echo ($SName['gate_out_by']->firstname.' '.$SName['gate_out_by']->lastname); ?></td>
            		                                    <td><?php echo _d($details->gate_out_date); ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		          <?php } ?>
            		        <?php
    		                    if($status == 9){ ?>
            		                    <h4>Mark Vehicle Exit</h4>
            		                    <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExitWithdrawal">
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
            		    <?php } ?>
                    </div>
                </div>
		    </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
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
<script>
    $('#saveBtn').click(function(){
        var check = $('input').val();
        if((check != null) || (check != '')){
            $('#final_qc_form').submit();
        }
    });
</script>
<script>
    $('#updateBtn').click(function(){
        var check = $('input').val();
        if((check != null) || (check != '')){
            $('#final_qc_form2').submit();
        }
    });
</script>
</body>
</html>
