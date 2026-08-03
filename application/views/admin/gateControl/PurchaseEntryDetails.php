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
                                                if($details->TType == 'P'){
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
                                                        $status_val = "PAYMENT ADVICE GANERATED";
                                                    }else if($details->status == 14){
                                                        $status_val = "RO OFFICE QC DONE";
                                                    }else if($details->status == 15){
                                                        $status_val = "HO OFFICE QC DONE";
                                                    }else if($details->status == 16){
                                                        $status_val = "PAYMENT ADVICE APPROVED";
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
		                    if($details->TType == 'P'){ ?>
    		                <?php
    		                    if(($status == 3) || ($status > 3)){
    		                        ?><div class="row" style="margin:auto;width:100%;">
    		                            <h4>Peripheral QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="peripheral_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updatePeripheralQC"> 
                		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <?php foreach($peripheral as $key=>$value){ ?>
                		                                        <th><?php echo $value['ItemParameterName']; ?></th>
                		                                    <?php } ?>
                		                                    <th>UserID</th>
                		                                    <th>Date Time</th>
                		                                    <th>Update</th>
                		                                </tr>
                		                            </thead>
                		                            <tbody>
                		                                <tr>
                		                                    <?php
                		                                        
                		                                        $TransDate = $peripheral[0]["TransDate"];
                		                                        $UserID = $peripheral[0]["firstname"].' '.$peripheral[0]["lastname"];
                		                                        $count = 1;
                		                                    ?>
                		                                    <?php foreach($peripheral as $key=>$value){ ?>
                		                                        <td><input style="width: 70px;" id="parameterValue<?php echo $count; ?>" name="parameterValue<?php echo $count; ?>" value="<?php echo $value['ParameterValue']; ?>"></td>
                		                                        <input hidden id="parameterId<?php echo $count; ?>" name="parameterId<?php echo $count; ?>" value="<?php echo $value['ItemParameterID']; ?>">
                		                                    <?php 
                		                                    $count++;
                		                                    } ?>
                		                                    <input type="text" name="count" value="<?php echo $count; ?>" hidden>
                		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                		                                    <td><?php echo $UserID;?></td>
                		                                    <td><?php echo _d($TransDate);?></td>
                		                                    <td><button class="updateCheck" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
            		                    </div>
            		                </div><?php
    		                    }
    		                ?>
    		                <?php
    		                    if(($status == 4) || ($status > 4)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gross Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="gross_weight_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateGrossWeightDetails"> 
                		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <th>Total Weight(MT)</th>
                		                                    <th>Top Image</th>
                		                                    <th>Front Image</th>
                		                                    <th>Side Image</th>
                		                                    <th>Loaded By</th>
                		                                    <th>Loaded Date-Time</th>
                		                                    <th>Update</th>
                		                                </tr>
                		                            </thead>
                		                            <tbody>
                		                                <tr>
                		                                    <td><input style="width:70px;" id="total_weight" name="total_weight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>"> </td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlTopImage ?>" target="_blank">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlFrontImage ?>" target="_blank">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VHLSideImage ?>" target="_blank">View Image</a></td>
                		                                    <td><?php echo ($staffName['LWUserID']->firstname.' '.$staffName['LWUserID']->lastname) ?></td>
                		                                    <td><?php echo _d($details->LWTransDate); ?></td>
                		                                    
                		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                		                                    <td><button class="updateCheck" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
            		                    </div>
            		                </div>
            		        <?php } ?>
    		                <?php
    		                    if(($status > 5) || ($status == 5)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Unloading Details</h4>
    		                            <h5>Total Layers: <?php echo $details->no_of_layers; ?><button class="updateCheck" type="button" style="float:right;" onclick="addLayer()"><i class="fa fa-plus"></i></button></h5>
    		                            <input hidden id="no_of_layers" value="<?php echo $details->no_of_layers; ?>">
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
            		                            <tbody id="layer_tbody">
            		                                <?php
            		                                    $count = 1;
                		                                foreach($layers as $key=>$value){ 
                		                                ?>
                		                                    <tr>
                    		                                    <td id="layer_number<?php echo $count ?>"><?php echo $value['layer_number'] ?></td>
                    		                                    <td><input style="width: 50px;" id="layer_quantity<?php echo $count ?>" value="<?php echo $value['qty'] ?>"></td>
                    		                                    <td><?php echo $value['unit'] ?></td>
                    		                                    <td><?php echo ($value['firstname'].' '.$value['lastname']) ?>
                    		                                        <input hidden id="unloading_by<?php echo $count ?>" value="<?php echo $value['UserID']; ?>">
                    		                                    </td>
                    		                                    <td id="unloading_date<?php echo $count ?>"><?php echo _d($value['Transdate']); ?></td>
                    		                                    <?php 
                    		                                    ?>
                    		                                    <?php 
                    		                                    $inner_parameter_count = 0;
                    		                                    foreach($peripheral as $key2=>$value2){
                    		                                        $m = 0; 
                    		                                    $TransDate = "";
                    		                                    $UserID = "";
                    		                                    ?>
                    		                                        <?php foreach($value['parameter_detail'] as $key3=>$value3){ ?>
                    		                                            <?php if($value3['ItemParameterID'] == $value2['ItemParameterID']){
                    		                                                $m++;
                    		                                                $TransDate = $value3['TransDate'];
                    		                                                $UserID = $value3['firstname'].' '.$value3['lastname'];
                    		                                                $UserID2 = $value3['UserID'];
                    		                                            ?>
                    		                                                <td><input style="width: 50px;" id="unloadingParameterValue_<?php echo $count ?>_<?php echo $value2['ItemParameterID']; ?>" value="<?php echo $value3['ParameterValue']; ?>"></td>
                    		                                                
                    		                                            <?php } ?>
                    		                                            
                    		                                        <?php } ?>
                    		                                     <?php 
                    		                                     $inner_parameter_count++;
                    		                                     } ?>
                    		                                     
                    		                                     <input hidden id="inner_item_count" value="<?php echo $inner_parameter_count; ?>">
                    		                                     <?php
                    		                                        if($m > 0){
                    		                                            ?>
                    		                                            <td><?php echo $UserID;?>
                    		                                                <input hidden id="qc_done_by_<?php echo $count; ?>" value="<?php echo $UserID2; ?>">
                    		                                            </td>
                    		                                            <td id="qc_done_date_<?php echo $count; ?>"><?php echo _d($TransDate);?></td>
                    		                                            <?php
                    		                                        }
                    		                                     ?>
                    		                                     
                    		                                </tr>
                		                            <?php  
                		                                    $count++;
                		                                }
            		                                ?>
            		                            </tbody>
            		                        </table>
            		                        <button class="btn btn-success btn-sm updateCheck" onclick="update_unloading_qc()" style="float: right;">Update</button>
            		                    </div>
            		                </div>
    		                <?php } ?>
    		                <?php
    		                    if(($status > 8) || ($status == 8)){ ?>
        		                       <div class="row" style="margin:auto;width:100%;">
        		                            <h4>Cleaning Details</h4>
                		                    <div class="col-md-12" style="padding:0px;">
                		                        <form id="cleaning_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateCleaningDetails"> 
                    		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                    		                            <thead>
                    		                                <tr>
                    		                                    <th>FM (kg)</th>
                    		                                    <th>Cleaning By</th>
                    		                                    <th>Cleaning Date-Time</th>
                    		                                    <th>Update</th>
                    		                                </tr>
                    		                            </thead>
                    		                            <tbody>
                    		                                <tr>
                    		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                    		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                        		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                                        		                
                                        		                <td><input style="width:70px;" id="fm_cleaning" name="fm_cleaning" value="<?php echo $details->FMQty; ?>"> </td>
                    		                                    <td><?php echo ($staffName['FMUserID']->firstname.' '.$staffName['FMUserID']->lastname) ?></td>
                    		                                    <td><?php echo _d($details->FMTransDate); ?></td>
                    		                                    <td><button class="updateCheck" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                    		                                </tr>
                    		                            </tbody>
                    		                        </table>
                		                        </form>
                		                    </div>
                		                </div>
                		    <?php  } ?>
    		                <?php
    		                    if(($status > 9) || ($status == 9)){ ?>
    		                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Tare Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="tare_weight_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateTareWeightDetails"> 
                		                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <th>Tare Weight(MT)</th>
                		                                    <th>Top Image</th>
                		                                    <th>Front Image</th>
                		                                    <th>Side Image</th>
                		                                    <th>Unloaded By</th>
                		                                    <th>Unloaded Date-Time</th>
                		                                    <th>Update</th>
                		                                </tr>
                		                            </thead>
                		                            <tbody>
                		                                <tr>
                		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                                    		                
                                    		                <td><input style="width:70px;" id="tare_weight" name="tare_weight" value="<?php echo number_format(($details->TareWeight/10), 3, '.', ''); ?>"> </td>
                		                                    
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>">View Image</a></td>
                		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
                		                                    <td><?php echo _d($details->TWTransDate); ?></td>
                		                                    <td><button class="updateCheck" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
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
    		                    if($status == 12){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
            		                    <button type="button" class="btn btn-info " onclick="ViewPaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >View & Send Payment Advice</button>
            		                    
            		                </div>
            		        <?php } ?>
            		        
            		        <?php
    		                    if($status == 13 ){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
            		                    <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Update RO QC</button>
            		                    
            		                </div>
            		        <?php } ?>
            		        <?php
    		                    if($status == 14 ){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
            		                    <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Update HO QC</button>
            		                    
            		                </div>
            		        <?php } ?>
            		        
            		        <?php
    		                    if($status == 15 ){ ?>
            		                <div class="row" style="margin:auto;width:100%;">
            		                    <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Approve Payment Advise</button>
            		                    
            		                </div>
            		        <?php } ?>
            		        
            		        <?php /*
    		                    if($status == 15){ ?>
    		                        <?php if($IsCD =="Y" && $IsPayment =="Y"){
    		                        }else {
    		                        ?>
    		                            <div class="row" style="margin:auto;width:100%;">
            		                        <button type="button" class="btn btn-info " onclick="Generate_DN_Payment('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Generate DN & Payment</button>
            		                    </div>
    		                        <?php
    		                        }
    		                        ?>
            		                
            		        <?php } */ ?>
            		        
            		        
            		        <?php
    		                    if($status == 16){ ?>
    		                        <div class="row">
    		                            <h4><a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewPayment/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View Payment Slip</a></h4>
    		                        </div>  
    		                        
            		        <?php } ?>
            		        
            		        <?php 
            		        }
            		        ?>
            		
            <div class="modal fade" id="modifyModal" tabindex="-1" role="dialog" aria-labelledby="modifyModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding:5px 10px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Payment Advice View</h4>
                    </div>
                  <div class="modal-body">
                      <?php
                        $flag = 2;
			            //$PaymentDetails = $this->GateControl_model->getSingleGateControl($details->BookingID,$details->Gate_in_ID,$flag);
			            $QcDetails = $this->GateControl_model->getSingleFinalQc($details->BookingID,$details->Gate_in_ID);
			            $getControl_details = get_control_details($details->Gate_in_ID);
			            $taxrate = $getControl_details->taxrate;
			            $status = $details->status;
                      ?>
                      <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID;?>">
                      <input type="hidden" name="GatID" id="GatID" value="<?php echo $details->id;?>">
                      <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID;?>">
                    <table class="table table-striped table-bordered">
                        
                        <tbody>
                            <tr>
                                <td style="width: 100%;text-align:center; font-size:12px;font-weight:700;background-color:#BEBEBE;border-bottom: 1px solid #333;" colspan="12"><b>Purchase Invoice Details</b></td>
                            </tr>
                            <tr>
                                <td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Sr.No.</b></td>
                                <td colspan="4" style="width:25%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Item Name</b></td>
                                <td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>HSN</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Vendor Dis WT.</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Rate</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Basic Value</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>GST Amt/ Rate</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>TDS</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;"><b>Payble Amt</b></td>
                            </tr>
                            
                            <tr>
                                <td style="border-right: 1px solid #333;">1</td>
                                <td colspan="4" style="border-right: 1px solid #333;"><?php echo $getControl_details->ItemName; ?></td>
                                <td style="border-right: 1px solid #333;text-align:center;"><?php echo $getControl_details->hsn_code; ?></td>
                                <?php $ItemWeight = ($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10; ?> 
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($ItemWeight, 2, '.', '') ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($getControl_details->basic_rate, 2, '.', '') ?></td>
                                <?php $basicValue = $getControl_details->basic_rate * ($getControl_details->LoadedWeight - $getControl_details->TareWeight); ?>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($basicValue, 2, '.', '') ?></td>
                                <?php  
                                    if($getControl_details->CustomerType == "1"){
                                        $GstAmt = 0;
                                    }else{
                                        $GstAmt = $basicValue * ($getControl_details->taxrate / 100);
                                    }
                                ?>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($GstAmt, 2, '.', ''); ?></td>
                                <?php $totalPayable = $basicValue + $GstAmt; ?>
                                <td style="border-right: 1px solid #333;text-align:right;"></td>
                                <td style="text-align:right;"><?php echo number_format($totalPayable, 2, '.', ''); ?></td>
                            </tr>
                            <tr>
                                <td style="width: 100%;text-align:center;border-bottom: 1px solid #333;border-top: 1px solid #333; font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Bargain Details</b></td>
                            </tr>
        
                            <tr>
                                <td style="width:6%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Sr.No</td>
                                <td colspan="3" style="width:19%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Item Name</td>
                                <td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;">HSN</td>
                                <td style="width:6%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Bag</td>
                                <td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Applicable Rate</td>
                                <td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Trade Rate</td>
                                <td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Gross Weight</td>
                                <td style="width:9%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Tare Weight</td>
                                <td style="width:10%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Net Wt.</td>
                                <td style="width:10%;border-bottom: 1px solid #333;">Amount</td>
                            </tr>
                            <?php
                        if(isset($QcDetails)){
                            $i = 1;
                            $totalDeduction = 0;
                            foreach($QcDetails as $key=>$val){
                                // if($val['ItemParameterID'] == "3"){
                                //     $deductionAmt = 0.00;
                                // }else{
                                    $deductionAmt = $val['deductionAmt'];
                                // }
                                $totalDeduction += $deductionAmt;
                            }
                        }
                    ?>
                            <?php 
                            // $FinalRate = ($totalPayable - $totalDeduction) / ($ItemWeight * 10);
                            $FinalRate = ($basicValue - $totalDeduction) / ($ItemWeight * 10);
                            
                                if($getControl_details->CustomerType == "1"){
                                    $POAmount = $FinalRate * ($ItemWeight * 10);
                                }else{
                                    $POAmount = $totalPayable;
                                }
                            ?> 
                            <tr>
                                <td style="border-right: 1px solid #333;">01</td>
                                <td colspan="3" style="border-right: 1px solid #333;"><?php echo $getControl_details->ItemName; ?></td>
                                <td style="border-right: 1px solid #333;"><?php echo $getControl_details->hsn_code; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo $getControl_details->quantity; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format($FinalRate, 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo $getControl_details->basic_rate; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format(($getControl_details->LoadedWeight/10), 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format(($getControl_details->TareWeight/10), 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format($ItemWeight, 2, '.', ''); ?></td>
                                <td style="text-align:right;"><?php echo number_format($POAmount, 2, '.', ''); ?></td>
                            </tr>
        
                            <tr>
                                <td colspan="11" style="border-bottom: 1px solid #333;border-top: 1px solid #333;border-right: 1px solid #333;"><b>Total</b></td>
        
                                <td style="text-align:right;border-bottom: 1px solid #333;border-top: 1px solid #333;"><?php echo number_format($POAmount, 2, '.', ''); ?></td>
                            </tr>
        
                            <tr>
                                <td style="width: 100%;text-align:center; border-bottom: 1px solid #333;font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Quality and Other Deductions</b></td>
                            </tr>
                            
                            <tr>
                                <td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Sr.No.</td>
                                <td colspan="3" style="width:19%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Particulars</td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Required</td>
                                <td colspan="2" style="width:18%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Center Actual</td>
                                <td  style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">RO Actual</td>
                                <td  style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">HO Actual</td>
                                <td style="width:9%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Diff.</td>
                                <td colspan="2" style="width:20%;border-bottom: 1px solid #333;text-align:right;">Value</td>
                            </tr>
                            
                    <?php
                        if(isset($QcDetails)){
                            $i = 1;
                            $totalDeduction = 0;
                            foreach($QcDetails as $key=>$val){
                                // if($val['ItemParameterID'] == "3"){
                                //     $deductionAmt = 0.00;
                                // }else{
                                    $deductionAmt = $val['deductionAmt'];
                                // }
                            ?>
                            <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;"><?php echo $i ?></td>
                                <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;"><?php echo $val['ItemParameterName']; ?></td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($val['BaseValue'], 3, '.', ''); ?></td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['ParameterValue'], 3, '.', ''); ?></td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['EParameterValue'], 3, '.', ''); ?></td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['HParameterValue'], 3, '.', ''); ?></td>
                                <?php if($details->CustomerType == 1) { 
                                    $diff = $val['ParameterValue'] - $val['BaseValue'];
                                } elseif($details->IsHoUpdate == 'Y') { 
                                  $diff = $val['HParameterValue'] - $val['BaseValue'];
                                 } elseif($details->IsQcUpdate == 'Y') { 
                                  $diff = $val['EParameterValue'] - $val['BaseValue'];
                                 } else { 
                                  $diff = $val['ParameterValue'] - $val['BaseValue'];
                                 } ?>
                                <?php /* $diff = $val['EParameterValue'] - $val['BaseValue']; */ ?>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($diff, 3, '.', ''); ?></td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><?php echo $deductionAmt; ?></td>
                                <?php $totalDeduction += $deductionAmt; ?>
                            </tr>
                        <?php
                            $i++;
                            }
                        ?>
                            <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;" colspan="10">Total</td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><b><?php echo number_format($totalDeduction, 3, '.', ''); ?></b></td>
                            </tr>
                <?php
                    }
                ?>
                    <?php if($getControl_details->CustomerType != "1"){ ?>
                            <tr>
                                <td style="width: 100%;text-align:center;border-bottom: 1px solid #333; font-size:12px;font-weight:700;background-color:#BEBEBE;" colspan="12"><b>Debit Note Details</b></td>
                            </tr>
        
                            <tr>
                                <td style="width:6%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Sr.No.</td>
                                <td colspan="3"style="width:34%;border-bottom: 1px solid #333;border-right: 1px solid #333;">Particulars</td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:center;">HSN </td>
                                <td style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Qty/Nos</td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Rate</td>
                                <td style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">GstAmt</td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;">Amount</td>
                            </tr>
        
        
                            <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;">01</td>
                                <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;">Weight Shortage</td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">12010090</td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;">0.00</td>
                            </tr>
                            <?php
                                $QCGstAmt = ($totalDeduction * $taxrate) /100;
                                $final_deduction = $totalDeduction + $QCGstAmt;
                            ?>
                            <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;">02</td>
                                <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;">Quality and Other Deduction</td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">12010090</td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format(1, 2, '.', ''); ?></td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($totalDeduction, 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCGstAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($final_deduction, 3, '.', ''); ?></td>
                            </tr>
                            
                            <tr>
                                <td colspan="12" style="height:20px;border-bottom: 1px solid #333;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="width:17%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Document</b></td>
                                <td colspan="2" style="width:20%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Basic Value Net of TDS</b></td>
                                <td style="width:10%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>GST Amt</b></td>
                                <td colspan="2" style="width:18%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Net Payable</b></td>
                                <td style="width:5%;border-right: 1px solid #333;" rowspan="6"></td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Sub Total</b></td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;"><b><?php echo number_format($totalDeduction, 2, '.', ''); ?></b></td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Purchase Invoice</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($basicValue, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($GstAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($totalPayable, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">CGST + @0%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Debit Note</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($totalDeduction, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCGstAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($final_deduction, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">SGST + @0%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Net</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($basicValue - $totalDeduction, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format(($GstAmt - $QCGstAmt), 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($totalPayable - $final_deduction, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">IGST + @0%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                            </tr>
        
                            <tr>
                                <td colspan="8" style="width:65%;"></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Round Off</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;">0.00</td>
                            </tr>
        
                            <tr>
                                <td colspan="8" style="width:65%;"></td>
                                <td colspan="2" style="width:15%;"><b>Total Amount</b></td>
                                <td colspan="2" style="width:15%;text-align:right;text-align:right;"><b><?php echo number_format($final_deduction, 2, '.', ''); ?></b></td>
                            </tr>
        
                            
                            <tr>
                                <td colspan="12" style="height:20px;width:100%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Final Rate: <?php echo number_format($FinalRate, 2, '.', ''); ?></td>
                            </tr>
                        <?php }else{
                        ?>
                            <tr>
                                <td colspan="2" style="width:17%;border-bottom: 1px solid #333;border-right: 1px solid #333;"><b>Document</b></td>
                                <td colspan="2" style="width:20%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Basic Value</b></td>
                                <td colspan="2" style="width:18%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;"><b>Net Payable</b></td>
                                <td colspan="6" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Net Amount</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($POAmount, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($POAmount, 2, '.', ''); ?></td>
                                <td colspan="6"></td>
                            </tr>
                        <?php
                        } ?>
                        </tbody>
                    </table>
                    
                    <br>
                    <?php
                        if($status == "12"){
                    ?>
                    <div class="row" id="modify_row" style="display:none">
                        <div class="col-md-12">
                        <div id="sendrequest"></div>
                        </div>
                        <div class="col-md-4">
                            <small class="req text-danger">* </small>
                            <label for="reason" class="form-label">Reason</label>
                            <textarea name="reason" id="reason" class="form-control"></textarea>
                        </div>
                        <div class="col-md-2">
                            <small class="req text-danger">* </small>
                            <label for="reasonAmt" class="form-label">Amount</label>
                            <input type="text" name="reasonAmt" id="reasonAmt" class="form-control">
                        </div>
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="ModifyUpdate" class="btn btn-primary">Send for Approval</button>
                        </div>
                        <div class="col-md-2" style="margin-top:2%">
                            <button type="button" id="CancelModifyUpdate" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                    <div class="row" id="SendButton">
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="SendForApproval" class="btn btn-primary">Send for Approval</button>
                        </div>
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="ModifyAdvice" class="btn btn-default">Modify</button>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <?php
                        if($status == "13"){
                    ?>
                        <?php
                            if($details->modify_reason !==""){
                        ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><b>Amount : </b><?php echo $details->reasonAmt; ?></p>
                                </div>
                                <div class="col-md-12">
                                    <p><b>Reason : </b><?php echo $details->modify_reason; ?></p>
                                </div>
                            </div>
                        <?php
                            }
                        ?>
                        <div class="row">
                            <?php
                            // $NetAmtPay = $totalPayable - $totalDeduction;
                            
                            $NetAmtPay = $POAmount - $final_deduction;
                            $defualtAmtPay = (($NetAmtPay) - (($NetAmtPay * 20) / 100) );
                            ?>
                            <?php
                                if($details->IsQcUpdate == "N"){
                            ?>
                                <div class="col-md-3" style="margin-top:2%">
                                    <button type="button" id="changeQC" class="btn btn-primary">Change Center QC</button>
                                </div>
                                <div class="col-md-3 ml-1" style="margin-top:2%;margin-left: 3%;">
                                    <button type="button" id="ContinueQC" class="btn btn-primary">Continue with Center QC</button>
                                </div>
                            <?php
                                }else{
                                    
                            ?>
                                <div class="col-md-3">
                                    <input type="hidden" name="NetAmt" id="NetAmt" value="<?php echo $NetAmtPay;?>">
                                    <small class="req text-danger">* </small>
                                    <label for="payment_perc" class="form-label">Enter Payment %</label>
                                    <input type="text" name="payment_perc" id="payment_perc" class="form-control" value="80" onkeypress="return isNumber(event)"> 
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_perc" class="form-label">Amount (₹)</label>
                                    <input type="hidden" name="final_rate" id="final_rate" value="<?php echo $FinalRate; ?>">
                                    <input type="text" name="payment_Amt" id="payment_Amt" class="form-control" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>" readonly>
                                    <input type="hidden" name="payment_Amt2" id="payment_Amt2" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>">
                                </div>
                                <div class="row" id="SendButton">
                                    <div class="col-md-3" style="margin-top:2%">
                                        <button type="button" id="ForApproval" class="btn btn-primary">Approve</button>
                                    </div>
                                </div>
                                
                            <?php  
                                }
                            ?>
                            
                            
                        </div>
                        
                        <div class="row" id="modify_qc" style="display:none">
                            <div class="col-md-12" style="padding:0px;">
                                <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
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
            		                  <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID;?>">
                                        <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID;?>">
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
            		                  
            		                  </tr>
            		                </tbody>
            		          </table>
            		        </div>
            		        <div class="col-md-2" style="margin-top:2%">
                                <button type="submit" id="ModifyQC" class="btn btn-primary">Update RO QC</button>
                            </div>
            		        </form>
                            
                            <div class="col-md-2" style="margin-top:2%">
                                <button type="button" id="CancelModifyQC" class="btn btn-default">Cancel</button>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                    
                    <?php 
                        if($status == "14" || $status == "15") {
                    ?>
                        <div class="row">
                            <?php 
                            // $NetAmtPay = $totalPayable - $totalDeduction;
                            $NetAmtPay = $POAmount - $final_deduction;
                            $defualtAmtPay = (($NetAmtPay) - (($NetAmtPay * 20) / 100) );
                            ?>
                            <?php
                                if($details->IsHoUpdate == "N" && $status == "14"){
                            ?>
                                <div class="col-md-3" style="margin-top:2%">
                                    <button type="button" id="changeQCHO" class="btn btn-primary">Change RO QC Parameter</button>
                                </div>
                                <div class="col-md-3" style="margin-top:2%; margin-left:2%;">
                                    <button type="button" id="ContinueQCHO" class="btn btn-primary">Continue with RO QC</button>
                                </div>
                            <?php
                                }else{
                            ?>
                                <div class="col-md-3">
                                    <input type="hidden" name="NetAmt" id="NetAmt" value="<?php echo $NetAmtPay;?>">
                                    <small class="req text-danger">* </small>
                                    <label for="payment_perc" class="form-label">Enter Payment %</label>
                                    <input type="text" name="payment_perc" id="payment_perc" class="form-control" value="80" onkeypress="return isNumber(event)"> 
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_perc" class="form-label">Amount (₹)</label>
                                    <input type="hidden" name="final_rate" id="final_rate" value="<?php echo $FinalRate; ?>">
                                    <input type="text" name="payment_Amt" id="payment_Amt" class="form-control" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>" readonly>
                                    <input type="hidden" name="payment_Amt2" id="payment_Amt2" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>">
                                </div>
                                <div class="row" id="SendButton">
                                    <div class="col-md-3" style="margin-top:2%">
                                        <button type="button" id="ForApproval" class="btn btn-primary">Approve</button>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                        <div class="row" id="modify_qcHO" style="display:none">
                            <div class="col-md-12" style="padding:0px;">
                                <form id="final_qc_form2" method="POST" action="<?php echo admin_url(); ?>GateControl/updateHOQC">
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
            		                  <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID;?>">
                                        <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID;?>">
            		                  <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
            		                  <?php 
            		                      foreach($peripheral as $key1=>$value1){
            		                        foreach($finalQC as $key=>$value){ 
                		                        if($value1['ItemParameterID']==$value['ItemParameterID']){
                		                              $ParaID = $value['id'];
                		                              $paraValue = $value['EParameterValue'];
                		                        }
            		                        }
            		                   ?>
            		                        <td><input style="width:100%;" type="text" name="<?php echo $ParaID; ?>" value="<?php echo $paraValue; ?>" onkeypress="return isNumber(event)"></td>
            		              <?php } ?>
            		                  
            		                  </tr>
            		                </tbody>
            		          </table>
            		        </div>
            		        <div class="col-md-2" style="margin-top:2%">
                                <button type="submit" id="ModifyQCHO" class="btn btn-primary">Update HO QC</button>
                            </div>
            		        </form>
                            
                            <div class="col-md-2" style="margin-top:2%">
                                <button type="button" id="CancelModifyQC" class="btn btn-default">Cancel</button>
                            </div>
                        </div>
                    <?php 
                        } 
                    ?>
                    
                  </div>
                </div>
              </div>
            </div>
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

    function addLayer(){
        
        var no_of_layers = parseInt($('#no_of_layers').val());
        
        var layer_details = <?php echo json_encode($layers); ?>;
        
        var layer_parameters = layer_details[0].parameter_detail;
        
        no_of_layers += 1;
        
        var html = '';
        
        html +='<tr>';
        html +='<td id="layer_number'+no_of_layers+'">' + no_of_layers + '</td>';
        html +='<td><input style="width: 50px;" id="layer_quantity'+ no_of_layers +'" value=""></td>';
        html +='<td>' + layer_details[0].unit + '</td>';
        html +='<td><input hidden id="unloading_by' + no_of_layers + '" value=""></td>';
        html +='<td id="unloading_date' + no_of_layers + '"></td>';
        
        for(let i = 0; i < layer_parameters.length; i++){
            html += '<td>';
            html += '<input style="width: 50px;" id="unloadingParameterValue_'+ no_of_layers +'_' + layer_parameters[i].ItemParameterID + '" value="">';
            html += '</td>';
        }
        html +='<td><input hidden id="qc_done_by_' + no_of_layers + '" value=""></td>';
        html +='<td id="qc_done_date_' + no_of_layers + '"></td>';
        html +='</tr>';
        
        $('#no_of_layers').val(no_of_layers);
        $('#layer_tbody').append(html);
    }
    
    function update_unloading_qc(){
        var no_of_layers = $('#no_of_layers').val();
        var inner_item_count = $('#inner_item_count').val();
        
        var Booking_ID = $('#BookingID').val();
        var id = $('#GatID').val();
        var Gate_IN_ID = $('#GateINID').val();
        
        var unloading_array = [];
        
        for(let i = 0 ; i < no_of_layers; i++){
            
            if($('#layer_quantity' + (i + 1)).val() != '' ){
                var inner_item_array = [];
                for(let j = 0; j < inner_item_count; j++){
                    
                    var item_id = 'unloadingParameterValue_' + (i + 1) + '_' + (j + 1);
                    const parts = item_id.split('_');
                    const item_parameter_id = parts[parts.length - 1];
                    
                    var item_object = {
                        'item_id' : item_parameter_id,
                        'item_value' : $('#unloadingParameterValue_' + (i + 1) + '_' + (j + 1)).val(),
                        'qc_done_by' : $('#qc_done_by_' + (i + 1)).val(),
                        'qc_done_date' : $('#qc_done_date_' + (i + 1)).text(),
                    }
                    inner_item_array.push(item_object);
                }
                var inner_object = {
                    'layer_no' : $('#layer_number' + (i + 1)).text(),
                    'layer_quantity' : $('#layer_quantity' + (i + 1)).val(),
                    'unloading_by' : $('#unloading_by' + (i + 1)).val(),
                    'unloading_date' : $('#unloading_date' + (i + 1)).text(),
                    'layer_details' : inner_item_array
                }
                unloading_array.push(inner_object);   
            }
        }
        $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/updateLayerDetails",
            dataType:"json",
            method:"POST",
            data:{Booking_ID:Booking_ID,Gate_IN_ID:Gate_IN_ID,id:id,unloading_array:unloading_array},
            beforeSend:function(){
                $('#sendrequest').html('Please wait request sending.');
            },
            success:function(r){
                window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
            }
        });
        
        console.log(unloading_array);
    }
    
    
    
    function reloadCurrentPage(){
        location.reload();
    }

function Generate_DN_Payment(GateINID)
    {
        $('#modifyModal').modal('show');
    }
    function ApprovePaymentAdvice(GateINID)
    {
        $('#modifyModal').modal('show');
    }
    
    function ViewPaymentAdvice(GateINID)
    {
        $('#modifyModal').modal('show');
    };
</script>
<script>
    $('.exitBtn').click(function(){
        $('#exit_form').submit();
    });
    
    $('#ModifyAdvice').click(function(){
        $('#modify_row').css('display','block');
        $('#SendButton').css('display','none');
    });
    
    $('#changeQC').click(function(){
        $('#modify_qc').css('display','block');
        $('#SendButton').css('display','none');
    });
    
    $('#changeQCHO').click(function(){
        $('#modify_qcHO').css('display','block');
        $('#SendButton').css('display','none');
    });
    
    $('#CancelModifyQC').click(function(){
        $('#modify_qcHO').css('display','none');
        $('#modify_qc').css('display','none');
        $('#SendButton').css('display','block');
    });
    
    $('#CancelModifyUpdate').click(function(){
        $('#modify_row').css('display','none');
        $('#SendButton').css('display','block');
    });
    
    $("#payment_perc").keyup(function(){
        var NetAmt = $('#NetAmt').val();
        var val = $(this).val();
        if(val == ""){
            $('#payment_Amt').val('0.00');
            $('#payment_Amt2').val('0.00');
        }else{
            if(val > 100){
              alert('please enter less than equal to 100%'); 
              $('#payment_Amt').val('0.00');
              $('#payment_Amt2').val('0.00');
              $(this).val('0');
            }else{
                var per = 100 - parseFloat(val);
                var defualtAmtPay = ((parseFloat(NetAmt)) - (parseFloat(NetAmt) * parseFloat(per)) / 100 );
                $('#payment_Amt').val(parseFloat(defualtAmtPay).toFixed(2));
                $('#payment_Amt2').val(parseFloat(defualtAmtPay).toFixed(2));
            }
        }
    })
    
    $('#ContinueQC').click(function(){
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var GatID = $('#GatID').val();
        if (confirm("Do you want to Continue with Center Office QC?") == true) {
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/continue_same_Qc",
                dataType:"json",
                method:"POST",
                data:{GateINID:GateINID,BookingID:BookingID},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                if(r == true){
                    $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    });
    
    $('#ContinueQCHO').click(function(){
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var GatID = $('#GatID').val();
        if (confirm("Do you want to Continue with RO Office QC?") == true) {
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/continue_same_ROQc",
                dataType:"json",
                method:"POST",
                data:{GateINID:GateINID,BookingID:BookingID},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                if(r == true){
                    $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    });
    $('#ForApproval').click(function(){
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var final_rate = $('#final_rate').val();
        var GatID = $('#GatID').val();
        var PaymentAmt = $('#payment_Amt2').val();
        var PaymentPer = $('#payment_perc').val();
        if (confirm("Do you want to Approve Payment Advice?") == true) {
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/ApprovePaymentAdvice",
                dataType:"json",
                method:"POST",
                data:{GateINID:GateINID,BookingID:BookingID,PaymentAmt:PaymentAmt,PaymentPer:PaymentPer,final_rate:final_rate},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                if(r == true){
                    $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    })
    
    $('#CreateDN').click(function(){
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var GatID = $('#GatID').val();
        if (confirm("Do you want to Create Debit Note?") == true) {
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/CreateDebitNote",
                dataType:"json",
                method:"POST",
                data:{GateINID:GateINID,BookingID:BookingID},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                    if(r == true){
                        $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    })
    
    $('#CreatePayment').click(function(){
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var GatID = $('#GatID').val();
        if (confirm("Do you want to Create Payment Voucher?") == true) {
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/CreatePayment",
                dataType:"json",
                method:"POST",
                data:{GateINID:GateINID,BookingID:BookingID},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                if(r == true){
                    $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    })
    
    $('#SendForApproval').click(function(){
        var reasonAmt = $('#reasonAmt').val();
        var modify_reason = $('#reason').val();
        var GateINID = $('#GateINID').val();
        var GatID = $('#GatID').val();
        if((GatID != '') && (GateINID != '')){
            if (confirm("Do you want to sent Payment Advice?") == true) {
			    $.ajax({
                    url:"<?php echo admin_url(); ?>GateControl/UpdatePaymentAdvice",
                    dataType:"json",
                    method:"POST",
                    data:{reasonAmt:reasonAmt, modify_reason:modify_reason,GateINID:GateINID},
                    beforeSend:function(){
                        $('#sendrequest').html('Please wait request sending.');
                    },
                    success:function(r){
                    if(r == true){
                        $('#modifyModal').modal('hide');
                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                        }else{
                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                        }
                    }
                });
    		} else {
    			return false;
    		}
        }
    })
    $('#ModifyUpdate').click(function(){
        var reasonAmt = $('#reasonAmt').val();
        var modify_reason = $('#reason').val();
        var GateINID = $('#GateINID').val();
        var GatID = $('#GatID').val();
        if(reasonAmt == ''){
            alert('please enter amount');
        }else if(modify_reason == ""){
            alert('please provide reason for modification');
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/UpdatePaymentAdvice",
                dataType:"json",
                method:"POST",
                data:{reasonAmt:reasonAmt, modify_reason:modify_reason,GateINID:GateINID},
                beforeSend:function(){
                    $('#sendrequest').html('Please wait request sending.');
                },
                success:function(r){
                    if(r == true){
                        $('#modifyModal').modal('hide');
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }else{
                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
                    }
                }
            });
        }
    });
</script>

<script>
    // function isNumber(evt) {
    // evt = (evt) ? evt : window.event;
    // var charCode = (evt.which) ? evt.which : evt.keyCode;
    // if (charCode = 46 && charCode > 31 
    //         && (charCode < 48 || charCode > 57)){
    //     return false;
    // }
    // return true;
    // }
    
    function isNumber(event){
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2 )) {
            event.preventDefault();
        }
    }
</script>
<script>
$(document).ready(function(){
    
    var unit = $('#unit_val').val();
    var quantity = $('#quantity').val();
    $('#unit').val(unit).selectpicker('refresh');
    $('#qty').val(quantity);
    
    //Check for status and restrict edit functionality
    
    var status = "<?php echo $details->status ?>";
    if(status > 9){
        $('.updateCheck').prop('disabled', true);
    }else{
        $('.updateCheck').prop('disabled', false);
    }
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
