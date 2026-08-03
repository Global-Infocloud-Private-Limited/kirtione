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
		    <div class="col-md-8" style="padding-right: 0px;">
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
                                                if($details->TType == 'A'){
                                                    if($details->status == 1){
                                                        $status_val = "ASN GENERATED";
                                                    }else if($details->status == 2){
                                                        $status_val = "GATE IN GENERATED";
                                                    }else if($details->status == 3){
                                                        $status_val = "PERIPHERAL DONE";
                                                    }else if($details->status == 4){
                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    }else if($details->status == 5){
                                                        $status_val = "UNLOADING IN PROGRESS ";
                                                    }else if($details->status == 6){
                                                        $status_val = "UNLOADING FINISHED ";
                                                    }else if($details->status == 7){
                                                        $status_val = "QC DONE ";
                                                    }else if($details->status == 8){
                                                        $status_val = "CLEANING DONE ";
                                                    }else if($details->status == 9){
                                                        $status_val = "TARE WEIGHT CAPTURED";
                                                    }else if($details->status == 10){
                                                        $status_val = "FINAL QC DONE ";
                                                    }else if($details->status == 11){
                                                        $status_val = "GATE OUT GANERATED";
                                                    }else if($details->status == 12){
                                                        $status_val = "MARK AS EXIT";
                                                    }else if($details->status == 13){
                                                        $status_val = "LOAN DISBRUSMENT DONE";
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
		                
		                <!--------- For Purchase ----------->
		                
		                <?php 
		                    if($details->TType == 'A'){ ?>
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
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlTopImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlFrontImage ?>" target="_blank">View Image</a></td>
            		                                    <td><a target="_blank" href="<?php echo base_url().$details->VHLSideImage ?>" target="_blank">View Image</a></td>
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
                		                    <h4>Cleaning Bag Details</h4>
                		                    
                		                    <div class="col-md-12" style="padding:0px;">
                		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <th>Number</th>
                		                                    <th>Weight</th>
                		                                    <th>Cleaning By</th>
                		                                    <th>Cleaning Date-Time</th>
                		                                </tr>
                		                            </thead>
                		                            <tbody>
                		                                <?php foreach($cleaningBagDetails as $c){ ?>
                    		                                <tr>
                    		                                    <td><?php echo $c['number'] ?></td>
                    		                                    <td><?php echo $c['weight'] ?></td>
                    		                                    <td><?php echo $c['firstname'] . ' ' . $c['lastname'] ?></td>
                    		                                    <td><?php echo _d($c['TransDate']); ?></td>
                    		                                </tr>
                		                                <?php } ?>
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
    		                            <form id="final_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/saveFinalQC" enctype="multipart/form-data">
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
            		                                    
            		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
            		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
            		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
            		                                        <input type="text" name="BookingType" value="<?php echo $details->TType ?>" hidden>
            		                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
            		                                        <?php foreach($peripheral as $key=>$value){ ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" class="form-control" onkeypress="return isNumber(event)"></td>
            		                                        <?php } ?>
            		                                        
            		                                        
            		                                    
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                        <div class="col-md-5">
                		                        <div class="form-group" app-field-wrapper="fQCSlip">
                                                    <small class="req text-danger">* </small>
                                                    <label for="fQCSlip" class="control-label">Upload QC Slip</label>
                                                    <input type="file" name="fQCSlip" id="fQCSlip" class="form-control">
                            					</div>
                        					</div>
            		                    </div>
            		                    <div class="row" style="width:100%;margin:auto;">
                                        <button id="saveBtn" class="btn btn-info">Save Final QC</button> 
                                        </div>
            		                    </form>
            		                </div>
            		                
            		        <?php } ?>
            		        <?php 
            		            if($status >= 10){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Final QC Details &nbsp;&nbsp;&nbsp;
    		                            <!--<a target="_blank" href="<?php echo base_url().'uploads/QC/'.$details->Gate_in_ID.'/'.$details->FinalQCSlip ?>">View QC Slip</a>-->
    		                            <a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewQc/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View QC Slip</a></h4>
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
            		                                        <?php 
            		                                        foreach($peripheral as $key1=>$value1){
            		                                        foreach($finalQC as $key=>$value){ 
                		                                        if($value1['ItemParameterID']==$value['ItemParameterID']){
                		                                            $ParaID = $value['id'];
                		                                            $paraValue = $value['ParameterValue'];
                		                                        }
            		                                        }
            		                                        ?>
            		                                            <td><input style="width:100%;" type="text" name="<?php echo $ParaID; ?>" value="<?php echo $paraValue; ?>" onkeypress="return isNumber(event)"></td>
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
            		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" onclick="setTimeout('location.reload(true);', 2000);">Generate Gate Out</a>
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
    		                    if($status>=12){ ?>
    		                        <div class="row">
    		                            <div class="col-md-12">
    		                                <h4><a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewPayment/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View Payment Details</a></h4>
    		                            </div>
    		                        </div>  
    		                        
            		        <?php } ?>
            		        <?php 
    		                    if($status>=13){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Disbrusment Details</h4>
    		                            <div class="col-md-12" style="padding:0px;margin-bottom:20px;">
            		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th style="width:20%">Loan Amount</th>
            		                                    <th>ROI</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
            		                                    <td><?php echo $details->Amount; ?></td>
            		                                    <td><?php echo $details->ROC; ?></td>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                    </div>
            		                </div> 
            		        <?php } ?>
            		        
            		        <?php 
            		            if($status == 12){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Disbrusment Details</h4>
    		                            <form id="loan_dis_form" method="POST" action="<?php echo admin_url(); ?>GateControl/loan_dis_submit" >
    		                                <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
            		                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
        		                            <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
        		                            <input type="text" name="BookingType" value="<?php echo $details->TType ?>" hidden>
        		                            <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
            		                    <div class="col-md-3">
            		                        <div class="form-group" app-field-wrapper="dis_amount">
                                                <small class="req text-danger">* </small>
                                                <label for="dis_amount" class="control-label">Disbrusment Amount</label>
                                                <input type="text" name="dis_amount" id="dis_amount" class="form-control">
                        					</div>
                    					</div>
                    					<div class="col-md-3">
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
                    					
            		                    <div class="col-md-2" style="width:100%;margin:auto;">
                                            <button id="saveBtn" class="btn btn-info">Save</button> 
                                        </div>
            		                    </form>
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
                <a href="#" class="btn btn-success mright5" data-toggle="tooltip" data-title="page reload" onclick="reloadCurrentPage(); return false;" data-original-title="" title="">
                    <i class="fa fa-refresh"> &nbsp;&nbsp;&nbsp;&nbsp;Reload Page</i>
                </a>
            </div>
    	</div>
    	
	</div>
</div>
<?php init_tail(); ?>

<script>
    function reloadCurrentPage(){
        location.reload();
    }
</script>
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
    $('#saveBtn').click(function(){
        var check = $('input').val();
        var Qcslip = $('#fQCSlip').val();
        if((check != null) || (check != '')){
            if(Qcslip != '' ){
                $('#final_qc_form').submit();
            }else {
                alert('please QC Slip upload');
            }
        }
    });
</script>
