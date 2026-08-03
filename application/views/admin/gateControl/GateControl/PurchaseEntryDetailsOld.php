<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%;margin-top:0px; }
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
                            <div class="col-md-12 text-centerr"  >
            					<nav aria-label="breadcrumb" >
            						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
            							<li class="breadcrumb-item active text-capitalize"><b>Gate Control</b></li>
            							<li class="breadcrumb-item active" aria-current="page"><b>Gate Control Details</b></li>
            							
            						</ol>
            					</nav>
            					<hr style="margin-Bottom:12px !important;">
            				</div>
                            <div class="col-md-12">
		                        <div class="table-purchase_request tableFixHead2">
                                    <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                                    
                                    <?php $status = $details->status;
                                        $IsCD = $details->IsCD;
                                        $IsPayment = $details->IsPayment;
                                    ?>
                                    <input id="Main_id" value="<?php echo $details->id; ?>" hidden>
                                    <tbody id="for_uppercase">
                                        <tr>
                                            <td><b>Account ID : </b></td>
                                            <td><?php echo $details->AccountID; ?></td>
                                            <?php 
                                                if($details->company != ''){
                                                    $PartyName = $details->company;
                                                }else{
                                                    $PartyName = $details->firstname.' '.$details->lastname;
                                                }
                                            ?>
                                            <td><b>Party Name : </b></td>
                                            <td><?php echo $PartyName; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>Booking ID : </b></td>
                                            <td><b><?php echo $details->BookingID; ?></b></td>
                                            <?php 
                                                if($details->CustomerType == 1){
                                                    $PartyType = 'Farmer';
                                                }elseif($details->CustomerType == 2){
                                                    $PartyType = 'Broker';
                                                }elseif($details->CustomerType == 3){
                                                    $PartyType = 'Trader';
                                                }elseif($details->CustomerType == 4){
                                                    $PartyType = 'Corporate/Processor';
                                                }
                                            ?>
                                            <td><b>Party Type : </b></td>
                                            <td><?php echo $PartyType; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>Item ID : </b></td>
                                            <td><?php echo $details->ItemID; ?></td>
                                            <td><b>Item Name : </b></td>
                                            <td><?php echo $details->ItemName; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>ASN By : </b></td>
                                            <td><?php echo ($SName['asn_by']->firstname.' '.$SName['asn_by']->lastname) ?></td>
                                            <td><b>ASN Date: </b></td>
                                            <td><?php echo _d($details->asn_date); ?></td>
                                        </tr>
                                        <tr>
                                        <?php 
                                            if(($details->status == 1) || ($details->status > 1)){
                                                ?>
                                                    <td><b>ASN : </b></td>
                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewAsn/<?php echo $details->BookingID."/".$details->ASNID; ?>" target="_blank">View ASN</a></td>
                                                <?php
                                            }
                                        ?>
                                        <?php 
                                            if(($details->status == 2) || ($details->status > 2)){
                                                ?>
                                                    <td><b>Gate In Pass : </b></td>
                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewGetInPass/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" target="_blank">View Gate In Pass</a></td>
                                                <?php
                                            }
                                        ?>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>ASN Quantity(MT): </b></td>
                                            <td><?php echo number_format($details->Asn_WT_MT, 2, '.', ''); ?></td>
                                            <td><b>ASN Quantity(Bag): </b></td>
                                            <td><?php echo number_format($details->quantity, 2, '.', ''); ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b>Gate In By : </b></td>
                                            <td><?php echo ($SName['gate_in_by']->firstname.' '.$SName['gate_in_by']->lastname) ?></td>
                                            <td><b>Gate In Date : </b></td>
                                            <td><?php echo _d($details->gate_in_date); ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Trade Rate (MT) : </b></td>
                                            <td><?php echo number_format($details->basic_rate * 10, 2, '.', ''); ?></td>
                                            <td><b>Vehicle No. : </b></td>
                                            <td><?php echo $details->VehicleNo; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Center Name : </b></td>
                                            <td><?php echo $details->CenterName; ?></td>
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
                                    </tbody>
                                    </table>  
                                </div>
		                    </div>
		                    <?php
		                        if($SendInwardToPcSoftCheck->pcsoft_doc_ref){
		                            $disableCheck = "disabled";
		                        }else{
		                            $disableCheck = ""; 
		                        }
	                            $BagWeight = 0;
	                            $IsBagUpdate = "N";
	                            foreach($ActualOtherDeductionList as $key=>$val){
	                                if($val["ItemID"] == "BG"){
	                                    $IsBagUpdate = "Y";
	                                    $BagWeight = $val["quantity"];
	                                    $UserName = $val["firstname"]." ".$val["lastname"];
	                                    $Transdate = $val["TransDate"];
	                                }
	                            }
		                        // Check stages for add or update Stack details
		                            // Gross Weight, tare Weight, Bag Weight
		                            if($details->LoadedWeight > 0 && $details->TareWeight > 0 && $IsBagUpdate == "Y"){
		                                $ChkStackAddUpdate = "";
		                            }else{
		                                $ChkStackAddUpdate = "disabled";
		                            }
		                        // Lock Tare Weight,gross weight and bag weight after add stack details
		                            if($StackList){
		                                $LockWeight = 'disabled';
		                                $UnlockQc = "";
		                            }else{
		                                $LockWeight = "";
		                                $UnlockQc = "disabled";
		                            }
		                        
		                        // After Center QC done do not change Gross Weight and tare weight 
		                        if(!$finalQC){
		                            $disableWeight = "";
		                        }else{
		                            $disableWeight = "disabled";
		                        }
		                    ?>
                        </div><!-- First row end-->
                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Peripheral QC Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="peripheral_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updatePeripheralQC"> 
                		                        <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                		                            <thead>
                		                                <tr>
                		                                    <?php 
                		                                        $number_of_para = 0;
                		                                    foreach($peripheral as $key=>$value){ 
                		                                        $number_of_para++;
                		                                    ?>
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
                		                                        <td><input style="width: 70px;" id="parameterValue<?php echo $count; ?>" name="parameterValue<?php echo $count; ?>" value="<?php echo $value['ParameterValue']; ?>" onkeypress="return isNumber(this,event)"></td>
                		                                        <input hidden id="parameterId<?php echo $count; ?>" name="parameterId<?php echo $count; ?>" value="<?php echo $value['ItemParameterID']; ?>">
                		                                    <?php 
                		                                        $count++;
                		                                    } ?>
                		                                    <input type="text" name="count" value="<?php echo $number_of_para; ?>" hidden>
                		                                    <input type="text" name="BookingID" id="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                                    <input type="text" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                                    		                <input type="text" name="WHID" id="WHID" value="<?php echo $details->GodownID ?>" hidden>
                		                                    <td><?php echo $UserID;?></td>
                		                                    <td><?php echo _d($TransDate);?></td>
                		                                    <td><button class="updateCheck" <?php echo $disableCheck;?>  type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
            		                    </div>
            		                </div> <!-- PERIPHERAL Row End-->
            		                
            		    <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Gross Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="gross_weight_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateGrossWeightDetails"> 
                		                        <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
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
                		                                    <td><input style="width:70px;" id="total_weight" name="total_weight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>" onkeypress="return isNumber(this,event)"> </td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlTopImage ?>" target="_blank">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VhlFrontImage ?>" target="_blank">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->VHLSideImage ?>" target="_blank">View Image</a></td>
                		                                    <td><?php echo ($staffName['LWUserID']->firstname.' '.$staffName['LWUserID']->lastname) ?></td>
                		                                    <td><?php echo _d($details->LWTransDate); ?></td>
                		                                    
                		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                		                                    <td><button class="updateCheck" <?php echo $LockWeight;?> type="button" id="GrossWeightSubmit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
            		                    </div>
            		                </div> <!-- Gross Weight Row End-->  
            		                
            		    
            		    <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Vehicle Details</h4>
    		                            <h5>Total Layers: <?php echo $details->no_of_layers; ?><button class="updateCheck" <?php echo $LockWeight;?> type="button" style="float:right;" onclick="addLayer()"><i class="fa fa-plus"></i></button></h5>
    		                            <input hidden id="no_of_layers" value="<?php echo $details->no_of_layers; ?>">
    		                            
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th>Layer No.</th>
            		                                    <th>Quantity</th>
            		                                    <th>Unit</th>
            		                                    <th>Done By</th>
            		                                    <th>Done Date</th>
            		                                    <?php 
            		                                        $inner_parameter_count = 0;
            		                                    foreach($peripheral as $key=>$value){
            		                                        $inner_parameter_count++;
            		                                      ?>
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
                    		                                    <td><input style="width: 50px;" id="layer_quantity<?php echo $count ?>" value="<?php echo $value['qty'] ?>" onkeypress="return isNumber(this,event)"></td>
                    		                                    <td><?php echo $value['unit'] ?></td>
                    		                                    <td><?php echo ($value['firstname'].' '.$value['lastname']) ?>
                    		                                        <input hidden id="unloading_by<?php echo $count ?>" value="<?php echo $value['UserID']; ?>">
                    		                                    </td>
                    		                                    <td id="unloading_date<?php echo $count ?>"><?php echo _d($value['Transdate']); ?></td>
                    		                                    <?php 
                    		                                    ?>
                    		                                    <?php 
                    		                                    
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
                    		                                                <td><input style="width: 50px;" id="unloadingParameterValue_<?php echo $count ?>_<?php echo $value2['ItemParameterID']; ?>" value="<?php echo $value3['ParameterValue']; ?>" onkeypress="return isNumber(this,event)"></td>
                    		                                                
                    		                                            <?php } ?>
                    		                                            
                    		                                        <?php } ?>
                    		                                     <?php 
                    		                                     } ?>
                    		                                     
                    		                                     
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
            		                        <input hidden id="inner_item_count" value="<?php echo $inner_parameter_count; ?>">
            		                        <input type="text" name="ItemIDLayer" id="ItemIDLayer" value="<?php echo $details->ItemID ?>" hidden>
            		                        <button class="btn btn-success btn-sm updateCheck" onclick="update_unloading_qc()" <?php echo $disableCheck;?> style="float: right;">Update Layer Bag & QC</button>
            		                    </div>
            		                </div>  <!-- Unloading Row End-->   
            		                
            		                <div class="row">
            		                    <div class="col-md-6">
            		                        <div class="row" style="margin:auto;width:100%;">
            		                            <h4>Cleaning Details</h4>
                    		                    <div class="col-md-12" style="padding:0px;">
                    		                        <form id="cleaning_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateCleaningDetails"> 
                        		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                        		                            <thead>
                        		                                <tr>
                        		                                    <th>FM (kg)</th>
                        		                                    <th>Cleaning By</th>
                        		                                    <th>Cleaning Date Time</th>
                        		                                    <th>Update</th>
                        		                                </tr>
                        		                            </thead>
                        		                            <tbody>
                        		                                <tr>
                        		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                        		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                            		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                                            		                
                                            		                <td><input style="width:70px;" id="fm_cleaning" name="fm_cleaning" value="<?php echo $details->FMQty; ?>" onkeypress="return isNumber(this,event)"> </td>
                        		                                    <td><?php echo ($staffName['FMUserID']->firstname.' '.$staffName['FMUserID']->lastname) ?></td>
                        		                                    <td><?php echo _d($details->FMTransDate); ?></td>
                        		                                    <td><button class="updateCheck" <?php echo $LockWeight;?> type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                        		                                </tr>
                        		                            </tbody>
                        		                        </table>
                    		                        </form>
                    		                    </div>
                    		                </div> <!-- Cleaning Details Row End-->
            		                    </div>
            		                    <?php
            		                    if($details->TType == "P"){
            		                        $TransID = $details->PurchID;
            		                    }elseif($details->TType == "S"){
            		                        $TransID = $details->SalesID;
            		                    }else{
            		                        $TransID = "";
            		                    }
            		                ?>
            		                    <div class="col-md-6">
            		                        
            		                        <div class="row" style="margin:auto;width:100%;">
        		                            <h4>Bag Weight Details</h4>
                    		                    <div class="col-md-12" style="padding:0px;">
                    		                        <form id="bag_weight_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateBagWeightDetails"> 
                        		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                        		                            <thead>
                        		                                <tr>
                        		                                    <th>Empty Bag Weight(kg)</th>
                        		                                    <th>Added By</th>
                        		                                    <th>Add Date Time</th>
                        		                                    <th>Update</th>
                        		                                </tr>
                        		                            </thead>
                        		                            <tbody>
                        		                                <tr>
                        		                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID; ?>" hidden>
                        		                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>
                                            		                <input type="text" name="id" value="<?php echo $details->id; ?>" hidden>
                                            		                <input type="text" name="basic_rate" value="<?php echo $details->basic_rate; ?>" hidden>
                                            		                <input type="text" name="TransID" value="<?php echo $TransID; ?>" hidden>
                                            		                <td><input style="width:70px;" id="bag_weight" name="bag_weight" value="<?php echo $BagWeight; ?>" onkeypress="return isNumber(this,event)"> </td>
                        		                                    <td><?php echo $UserName; ?></td>
                        		                                    <td><?php echo _d(substr($Transdate,0,19)); ?></td>
                        		                                    <td><button class="updateCheck " <?php echo $LockWeight;?> type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                        		                                </tr>
                        		                            </tbody>
                        		                        </table>
                    		                        </form>
                    		                    </div>
                    		                </div> <!-- Bag Weight Details Row End-->
            		                    </div>
            		                </div>
            		                
            		                    
                		                
                		                
                		                
                		                
                        <div class="row" style="margin:auto;width:100%;">
    		                            <h4>Tare Weight Details</h4>
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <form id="tare_weight_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateTareWeightDetails"> 
                		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
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
                		                                    <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>" hidden>
                                    		                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
                                    		                
                                    		                <td><input style="width:70px;" id="tare_weight" name="tare_weight" value="<?php echo number_format(($details->TareWeight/10), 3, '.', ''); ?>" onkeypress="return isNumber(this,event)"> </td>
                		                                    
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlTopImage ?>">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVhlFrontImage ?>">View Image</a></td>
                		                                    <td><a target="_blank" href="<?php echo base_url().$details->TWVHLSideImage ?>">View Image</a></td>
                		                                    <td><?php echo ($staffName['TWUserID']->firstname.' '.$staffName['TWUserID']->lastname) ?></td>
                		                                    <td><?php echo _d($details->TWTransDate); ?></td>
                		                                    <td><button class="updateCheck" <?php echo $LockWeight;?> id="TareWeightSubmit" type="button"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
                		                                </tr>
                		                            </tbody>
                		                        </table>
            		                        </form>
            		                    </div>
            		                </div> <!-- Tare Weight Row End-->
            		                
            		                <div class="row">
            		                    <form id="stack_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateStackDetails"> 
            		                    <div class="col-md-10">
            		                        <h4>Stack Details </h4>
            		                        <input type="text" name="ItemID" value="<?php echo $details->ItemID; ?>" hidden>
	                                        <input type="text" name="id" value="<?php echo $details->id; ?>" hidden>
	                                        <input type="text" name="TransID" value="<?php echo $TransID; ?>" hidden>
	                                        <input type="text" name="PartyID" value="<?php echo $details->PartyID; ?>" hidden>
	                                        <input type="text" name="WHID" value="<?php echo $details->GodownID; ?>" hidden>
	                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID; ?>" hidden>
	                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID; ?>" hidden>
	                                        <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType; ?>" hidden>
	                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>
            		                        
    		                                <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <th width="25%">Chamber</th>
            		                                    <th width="25%">Stack</th>
            		                                    <th width="25%">Lot</th>
            		                                    <th width="10%">Weight(MT)</th>
            		                                    <th width="10%">Bag Qty</th>
            		                                    <th width="5%">Action</th>
            		                                </tr>
            		                            </thead>
            		                            <tbody id="stack_tbody">
            		                                <tr class="item">
            		                                    <td>
            		                                        <div class="form-group" app-field-wrapper="Select Chamber">
                                                                <select name="chamber" id="chamber" class="selectpicker form-control" data-live-search="true">
                                                                    <option value="" >Non Selected</option>
                        										</select>
                        									</div>
            		                                    </td>
            		                                    <td>
            		                                        <div class="form-group" app-field-wrapper="Select Stack">
                                                                <select name="Stack" id="Stack" class="selectpicker form-control" data-live-search="true">
                                                                    <option value="" >Non Selected</option>
                        										</select>
                        									</div>
            		                                    </td>
            		                                    <td>
            		                                        <div class="form-group" app-field-wrapper="Select LOT">
                                                                <select name="LOTID" id="LOTID" class="selectpicker form-control" data-live-search="true">
                                                                    <option value="" >Non Selected</option>
                        										</select>
                        									</div>
            		                                    </td>
            		                                    <td>
            		                                        <div class="form-group" app-field-wrapper="Select LOT">
            		                                            <input style="height:36px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control" id="lot_weight" value="" onkeypress="return isNumber(this,event)">
            		                                        </div>
            		                                    </td>
            		                                    <td>
            		                                        <div class="form-group" app-field-wrapper="Select LOT">
            		                                            <input style="height:36px;width:100%;" class="form-control" id="lot_quantity" value="" onkeypress="return isNumber(this,event)">
            		                                        </div>
            		                                    </td>
            		                                    <td>
            		                                        <button class="updateCheck form-control" type="button" style="font-size:20px;color:green;" onclick="addrow()"><i class="fa fa-plus"></i></button>
            		                                    </td>
            		                                </tr>
            		                                <?php
            		                                    $i = 0;
            		                                    $StackWeight = 0;
            		                                    $StockWtCheckForGateOut = 0;
            		                                    foreach($StackList as $key=>$val){
            		                                        $i++;
            		                                        $StackWeight += $val['Weight'];
            		                                        $StockWtCheckForGateOut += $val['Weight'];
            		                              ?>
            		                                <tr class="item">
                                                        <td><?php echo $val["CHID"];?> <input hidden name="StackList[<?php echo $i;?>][Chamber]" value="<?php echo $val["CHID"];?>"></td>
                                                        <td><?php echo $val["StackID"];?> <input hidden name="StackList[<?php echo $i;?>][Stack]" value="<?php echo $val["StackID"];?>"></td>
                                                        <td><?php echo $val["LOTID"];?><input hidden name="StackList[<?php echo $i;?>][Lot]" value="<?php echo $val["LOTID"];?>"></td>
                                                        <td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control"  name="StackList[<?php echo $i;?>][WeightMT]" value="<?php echo $val["Weight"];?>" onkeypress="return isNumber(this,event)"></td>
                                                        <td> <input style="height:30px;width:100%;"  class="form-control" name="StackList[<?php echo $i;?>][BagQty]" value="<?php echo $val["BagQty"];?>" onkeypress="return isNumber(this,event)"></td>
                                                        <td> <button class="remove form-control" type="button" style="font-size:20px;color:red;" ><i class="fa fa-trash"></i></button></td>
                                                    </tr>
            		                              <?php
            		                                    }
            		                                ?>
    		                                    </tbody>
            		                            </table>
            		                    </div>
            		                    <div class="col-md-3" >
            		                        <input type="hidden" class="form-control" id="TotalStackWeight" name="TotalStackWeight" value="<?php echo $StackWeight;?>">
            		                        <input type="hidden" class="form-control" id="TotalLot" name="TotalLot" value="<?php echo $i;?>">
                                            <div class="form-group" >
        								        <button class="updateCheck btn btn-success btn-sm" style="margin-top: 10px;" <?php echo $ChkStackAddUpdate;?> type="button" id="StackSubmit">Update Stack Details</button>
        								        
        								    </div>
        								</div>
        								</form>
            		                </div>
            		                
            		                <?php  
            		                
            		                    $getControl_details = get_control_details($details->Gate_in_ID);
            		                    /*echo "<pre>";
            		                print_r($getControl_details);
            		                die;*/
            		                    $taxrate = $getControl_details->taxrate;
            		                    $RatePerKg = $getControl_details->basic_rate / 100;
                                        if($getControl_details->CustomerType == "1"){
                                            $taxrate = 0;
                                            $PurchaseWeight = ($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10;
                                            $WeightShortInKg = 0;
                                        }else{
                                            $PurchaseWeight = $getControl_details->Asn_WT_MT;
                                            $actWt = ($getControl_details->LoadedWeight - $getControl_details->TareWeight)/10;
                                            if($PurchaseWeight > $actWt){
                                                $WeightShortInKg = ($PurchaseWeight - (($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10) ) * 1000;
                                            }else{
                                                $WeightShortInKg = 0;
                                            }
                                            
                                        }
                                        $PurchaseValue = $PurchaseWeight * ($getControl_details->basic_rate * 10);
                                        $GstAmt = $PurchaseValue * ($taxrate / 100);
                                        $NetPurchaseAmt = $PurchaseValue + $GstAmt;
                                        
                                        $NetWeight_MT = $PurchaseWeight - ($BagWeight / 1000) - ($WeightShortInKg / 1000);
                                        
                                    ?>
            		                
            		                <?php
            		                        
            		              if($details->LoadedWeight != NULL && $details->TareWeight != NULL){
	                                   ?>
            		                <div class="row">
            		                    <div class="col-md-5">
            		                        <h4><b>Deduction Matrix</b></h4>
            		                  
	                                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
	                                            <thead>
	                                                <tr>
	                                                    <th>Parameter</th>
	                                                    <th>Amount</th>
	                                                </tr>
	                                            </thead>
	                                            <tbody>
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Net Weight(MT)</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($PurchaseWeight, 3, '.', '');?></td>
	                                                </tr>
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Purchase Amount</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($PurchaseValue, 2, '.', '');?></td>
	                                                </tr> 
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Actual Weight (MT)</b></td>
	                                                    <?php $InwardWt = ($getControl_details->LoadedWeight - $getControl_details->TareWeight) / 10; ?>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($InwardWt, 3, '.', '');?></td>
	                                                </tr>
	                                                <tr>
	                                                    <?php 
	                                                        if($PurchaseWeight <=$NetWeight_MT){
	                                                            $ActualInwardWeightMT = $PurchaseWeight;
	                                                        }else{
	                                                            $ActualInwardWeightMT = $NetWeight_MT;
	                                                        }
	                                                    ?>
	                                                    <td style="font-size:13px"><b>Actual Inward Weight (MT)</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($ActualInwardWeightMT, 3, '.', '');?></td>
	                                                </tr>
	                                                <?php
	                                                if($finalQC){
	                                                $TotalDeduction = 0;
	                                                foreach($finalQC as $key=>$value1){ 
	                                                    $TotalDeduction += $value1['deductionAmt'];
	                                                    ?>
	                                                    <tr>
	                                                        <td><?php echo $value1['ItemParameterName'];?></td>
	                                                        <td style="text-align:right;"><?php echo $value1['deductionAmt'];?></td>
	                                                    </tr>
	                                                   <?php
	                                                }
	                                                
	                                                foreach($ActualOtherDeductionList as $ADKey =>$ADVal){
                                                        $TotalDeduction += $ADVal["Amount"];
                                                        ?>
	                                                    <tr>
	                                                        <td><?php echo $ADVal['ItemName'];?></td>
	                                                        <td style="text-align:right;"><?php echo $ADVal['Amount'];?></td>
	                                                    </tr>
	                                                   <?php
                                                    }
	                                                
	                                                $Finalrate = ($PurchaseValue - $TotalDeduction) / $ActualInwardWeightMT;
	                                                $NetValue = $Finalrate * $ActualInwardWeightMT;
	                                                ?>
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Total Deduction</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($TotalDeduction, 2, '.', '');?></td>
	                                                </tr>
	                                                
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Final Rate/MT</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($Finalrate, 3, '.', '');?></td>
	                                                </tr>
	                                                <tr>
	                                                    <td style="font-size:13px"><b>Net Amount</b></td>
	                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($NetValue, 2, '.', '');?></td>
	                                                </tr>
	                                                <?php
	                                        }
	                                    ?>
	                                            </tbody>
	                                        </table>
	                                   
            		                    </div>
            		                </div>
            		        <?php
	                           }
	                       ?>
            		                
            		                
            		            <div class="row" style="margin:auto;width:100%;">
    		                        <h4>Final QC Details</h4>
    		                            <?php
	                                        if($finalQC){
	                                            $url = admin_url().'GateControl/updateFinalQC';
	                                            $btn_label = 'Update Center QC';
	                                        }else{
	                                            $url = admin_url().'GateControl/saveFinalQC';
	                                            $btn_label = 'Save Center QC';
	                                        }
	                                    ?>
    		                            <form id="final_qc_form" method="POST" action="<?php echo $url;?>" enctype="multipart/form-data">
            		                    <div class="col-md-12" style="padding:0px;">
            		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
            		                            <thead>
            		                                <tr>
            		                                    <?php foreach($peripheral as $key=>$value){
            		                                        
            		                                    ?>
            		                                        <th><?php echo $value['ItemParameterName']; ?></th>
            		                                    <?php } ?>
            		                                </tr>
            		                            </thead>
            		                            <tbody>
            		                                <tr>
        		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
        		                                        <input type="text" name="ItemID" value="<?php echo $details->ItemID ?>" hidden>
        		                                        <input type="text" name="id" value="<?php echo $details->id ?>" hidden>
        		                                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
        		                                        <input type="text" name="AccountID" value="<?php echo $details->AccountID ?>" hidden>
        		                                        <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>
        		                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
        		                                        <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>" hidden>
        		                                        <input type="text" name="TareWeight" id="TareWeight" value="<?php echo number_format(($details->TareWeight/10), 3, '.', ''); ?>" hidden>
        		                                        <input type="text" name="QC_for" id="QC_for" value="Center" hidden>
        		                                        <?php foreach($peripheral as $key=>$value){
        		                                            $paraValue = "";
            		                                        foreach($finalQC as $key=>$value1){ 
                		                                        if($value1['ItemParameterID']==$value['ItemParameterID']){
                		                                            $paraValue = $value1['ParameterValue'];
                		                                        }
            		                                        }
        		                                        ?>
        		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" value = "<?php echo number_format($paraValue, 2, '.', '');?>" class="form-control" onkeypress="return isNumber(this,event)"></td>
        		                                        <?php } ?>
            		                                </tr>
            		                            </tbody>
            		                        </table>
            		                       </div>
            		                        <?php
            		                        if($details->status >= 14){
            		                            
            		                        }else{
            		                            
            		                            ?>
            		                        <div class="col-md-4">
                		                        <div class="form-group" app-field-wrapper="fQCSlip">
                                                    <small class="req text-danger">* </small>
                                                    <label for="fQCSlip" class="control-label">Upload QC Slip</label>
                                                    <input type="file" name="fQCSlip" id="fQCSlip" class="form-control">
                            					</div>
                        					</div>
                        					<div class="col-md-4">
                		                        <div class="form-group" app-field-wrapper="fQCSlip">
                                                    <small class="req text-danger">* </small>
                                                    <label for="QCApproval" class="control-label">QC Approval</label>
                                                    <select name="QCApproval" id="QCApproval" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                                        <option value="1">Auto Approval</option>
                                                        <option value="2">Farmer / Trader Approval</option>
                                                    </select>
                            					</div>
                        					</div>
                        					<div class="col-md-4">
                		                        <div class="form-group" app-field-wrapper="fQCSlip">
                                                    <small class="req text-danger">* </small>
                                                    <label for="QCApprovalstatus" class="control-label">QC Approval Status</label>
                                                    <select name="QCApprovalstatus" id="QCApprovalstatus" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                                        <option value="Y" <?php if($details->QCApprove == "Y"){ echo 'selected';}?>>Approved</option>
                                                        <option value="N" <?php if($details->QCApprove == "N"){ echo 'selected';}?>>Rejected</option>
                                                        <option value="NA" <?php if($details->QCApprove == "NA"){ echo 'selected';}?>>Pending</option>
                                                    </select>
                            					</div>
                        					</div>
            		                        <div class="clearfix"></div>
                		                    <div class="col-md-6">
                		                        <button class="saveBtn btn btn-info" <?php echo $UnlockQc;?> id="saveBtn" type="button"><?php echo $btn_label;?></button>
                                            </div>
            		                    
            		                    
            		                    <?php } 
            		                  ?>
            		                  </form>
            		                </div> <!-- Center QC Row End-->
            		                
            		     <?php
    		          if($status < 11){ ?>           
            	        <div class="row" style="margin:auto;width:100%;">
    		                <h4>Gate Out Pass</h4>
		                    <div class="col-md-12" style="padding:0px;">
		                        <input type="text" name="KYC" id="KYC" value="<?php echo $details->KYCStatus;?>" hidden>
		                        <input type="text" name="NetWeightCheck" id="NetWeightCheck" value="<?php echo $NetWeight_MT;?>" hidden>
		                        <input type="text" name="StockWeightCheck" id="StockWeightCheck" value="<?php echo $StockWtCheckForGateOut;?>" hidden>
		                        
                                <button class="GenerateGateOut btn btn-info" id="GenerateGateOut" type="button">Generate Gate Out</button>
		                        <!--
		                        <a class="btn btn-info" target="_blank" href="<?php echo admin_url(); ?>GateControl/generateGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" onclick="setTimeout('location.reload(true);', 2000);">Generate Gate Out</a>-->
		                    </div>
		                </div><!-- Gate Out Row End-->
            		                
            		   <?php } ?>   
            		    
        		     <?php
		                if($status >= 11){ ?>
	                        <div class="row" style="margin:auto;width:100%;">
	                            <h4>Gate Out Pass &nbsp;&nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewGateOut/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>" target="_blank">View Gate Out Pass</a></h4>
	                            <div class="col-md-12" style="padding:0px;">
    		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
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
    		              </div>
        		       <?php } ?>
            		        
    		        <?php
	                    if($status == 11){ ?>              
                            <div class="row" style="margin:auto;width:100%;margin-top:2%">
                                <h4>Mark Vehicle Exit</h4>
    		                    <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExit">
    		                        <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
    		                          <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
    		                          <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>
    		                          <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;" >Mark Exit</button>
    		                    </form>
	                        </div>  
    		          <?php } ?>
            		    
            		    <?php
	                    if($status >= 12){ ?>
	                        <div class="row" style="margin:auto;width:100%;">
	                            <h4>Exit Marked</h4>
	                            <div class="col-md-12" style="padding:0px;margin-bottom:20px;">
    		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
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
        		                <div class="row" style="margin-top:10px;">
        		                    <div class="col-md-12">
        		                        <button type="button" class="btn btn-info " onclick="ViewPaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >View & Send Payment Advice</button>
        		                    </div>
        		                </div>
        		        <?php } ?>
            		        
        		        <?php
		                    if($status == 13 ){ ?>
        		                <div class="row" style="margin-top:10px;">
        		                    <div class="col-md-12">
        		                        <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Update RO QC</button>
        		                    </div>
        		                </div>
        		        <?php } ?>
            		        <?php
		                    if($status == 14 ){ ?>
        		                <div class="row" style="margin-top:10px;">
        		                    <div class="col-md-12">
        		                        <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Update HO QC</button>
        		                    </div>
        		                </div>
            		        <?php } ?>
            		        
            		        <?php
    		                    if($status == 15 ){ ?>
            		                <div class="row" style="margin-top:10px;">
            		                    <div class="col-md-12">
            		                        <button type="button" class="btn btn-info " onclick="ApprovePaymentAdvice('<?php echo $details->Gate_in_ID; ?>')" style="margin-right: 25px;" >Approve Payment Advise</button>
            		                    </div>
            		                </div>
            		        <?php } ?>
            		        
            		        <?php
            		            if($status > 15){
            		                ?>
            		                <div class="row" style="margin-top:10px;">
            		                    <div class="col-md-12">
        		                      <?php
        		                        if($SendInwardToPcSoftCheck->pcsoft_doc_ref){
        		                            
        		                        }else{
        		                            ?>
    		                                <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/SendDataToPcSoft">
            		                            <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>
                		                        <input type="text" id="GateINID" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>
                		                        <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>
                		                        <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;" >Send Data To PcSoft</button>
                		                    </form>
        		                            <?php
        		                        }
        		                      ?>
            		                    </div>
            		                </div>
            		                
            		                <?php
            		            }
            		        ?>
            		        
            		        
        		        <?php
		                    if($status == 16){ ?>
		                        <div class="row" style="margin-top:10px;">
		                            <div class="col-md-12">
		                                <h4><a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewPayment/<?php echo $details->BookingID.'/'.$details->Gate_in_ID; ?>"> View Payment Slip</a></h4>
		                            </div>
		                        </div>  
        		        <?php } ?>
                    </div><!-- End Panel Body-->
                </div><!-- End Panel-->
            </div><!-- End Col-md-8-->
            <div class="col-md-4">
        	    <div class="btn-top-toolbar bottom-transaction sm:tw-flex sm:tw-items-center sm:tw-justify-between">
                    <div class="col-md-6">
                        <a href="#" class="btn btn-success mright5" data-toggle="tooltip" data-title="page reload" onclick="reloadCurrentPage(); return false;" data-original-title="" title="">
                            <i class="fa fa-refresh"> &nbsp;&nbsp;&nbsp;&nbsp;Reload Page</i>
                        </a>
                    </div>
                </div>
        	</div>
        </div><!-- End Main Row-->
        
        <!-- Model Code-->
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
			            $QcDetails = $this->GateControl_model->getSingleFinalQc($details->BookingID,$details->Gate_in_ID);
			            
			            
			            if($getControl_details->state == "MH"){
			                $CGSTPer = $taxrate/2;
			                $SGSTPer = $taxrate/2;
			                $IGSTPer = 0;
			            }else{
			                $IGSTPer = $taxrate;
			                $SGSTPer = 0;
			                $CGSTPer = 0;
			            }
			            $status = $details->status;
                      ?>
                      <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID;?>">
                      <input type="hidden" name="GatID" id="GatID" value="<?php echo $details->id;?>">
                      <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID;?>">
                    <table class=" table-striped table-bordered">
                        
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
                                <?php  ?> 
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($PurchaseWeight, 2, '.', '') ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($getControl_details->basic_rate, 2, '.', '') ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($PurchaseValue, 2, '.', '') ?></td>
                                
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo number_format($GstAmt, 2, '.', ''); ?></td>
                                
                                <td style="border-right: 1px solid #333;text-align:right;"></td>
                                <td style="text-align:right;"><?php echo number_format($NetPurchaseAmt, 2, '.', ''); ?></td>
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
                                        $deductionAmt = $val['deductionAmt'];
                                        $totalDeduction += $deductionAmt;
                                    }
                                }
                                
                                foreach($ActualOtherDeductionList as $ADKey =>$ADVal){
                                    $totalDeduction += $ADVal["Amount"];
                                }
                            ?>
                            
                            <tr>
                                <td style="border-right: 1px solid #333;">01</td>
                                <td colspan="3" style="border-right: 1px solid #333;"><?php echo $getControl_details->ItemName; ?></td>
                                <td style="border-right: 1px solid #333;"><?php echo $getControl_details->hsn_code; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo $getControl_details->quantity; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format($Finalrate, 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo $getControl_details->basic_rate * 10; ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format(($getControl_details->LoadedWeight/10), 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format(($getControl_details->TareWeight/10), 2, '.', ''); ?></td>
                                <td style="border-right: 1px solid #333;text-align:right;"><?php echo  number_format($ActualInwardWeightMT, 2, '.', ''); ?></td>
                                <td style="text-align:right;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>
                            </tr>
        
                            <tr>
                                <td colspan="11" style="border-bottom: 1px solid #333;border-top: 1px solid #333;border-right: 1px solid #333;"><b>Total</b></td>
        
                                <td style="text-align:right;border-bottom: 1px solid #333;border-top: 1px solid #333;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>
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
                            $QualityDeduction = 0;
                            $totalDeduction = 0;
                            $AllDeduction = 0;
                            foreach($QcDetails as $key=>$val){
                                $deductionAmt = $val['deductionAmt'];
                            ?>
                            <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;"><?php echo $i ?></td>
                                <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;"><?php echo $val['ItemParameterName']; ?></td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($val['BaseValue'], 3, '.', ''); ?></td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['ParameterValue'], 3, '.', ''); ?></td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['EParameterValue'], 3, '.', ''); ?></td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;font-weight:bold;"><?php echo number_format($val['HParameterValue'], 3, '.', ''); ?></td>
                                <?php if($details->CustomerType == 1) { 
                                        $diff = $val['ParameterValue'] - $val['BaseValue'];
                                    }elseif($details->IsHoUpdate == 'Y') { 
                                      $diff = $val['HParameterValue'] - $val['BaseValue'];
                                    }elseif($details->IsQcUpdate == 'Y') { 
                                      $diff = $val['EParameterValue'] - $val['BaseValue'];
                                    }else { 
                                      $diff = $val['ParameterValue'] - $val['BaseValue'];
                                    } ?>
                                
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($diff, 3, '.', ''); ?></td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><?php echo $deductionAmt; ?></td>
                                <?php $totalDeduction += $deductionAmt; 
                                      $QualityDeduction += $deductionAmt;
                                      $AllDeduction += $deductionAmt; ?>
                            </tr>
                        <?php
                            $i++;
                            }
                        ?>
                    <?php
                        foreach($ActualOtherDeductionList as $okey=>$oval){
                            $AllDeduction += $oval["Amount"];
                            if($oval["ParticularItemID"] == "QOD" || $getControl_details->CustomerType == "1"){
                                ?>
                                <tr>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;"><?php echo $i ?></td>
                                <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;"><?php echo $oval['ItemName']; ?></td>
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">-</td>
                                <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>
                                <td  style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;font-weight:bold;">-</td>
                                
                                <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">-</td>
                                <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($oval["Amount"], 3, '.', ''); ?></td>
                                <?php $totalDeduction += $oval["Amount"]; ?>
                            </tr>
                            <?php
                            }
                            ?>
                            
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
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Qty/Nos</td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;border-right: 1px solid #333;text-align:right;">Rate/Nos</td>
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;">Amount</td>
                            </tr>
                            <?php
                                $i = 1;
                                $rate_per_kg = ($getControl_details->basic_rate / 100);
                                $NetWt_in_kg = $ActualInwardWeightMT * 1000;
                                $quantity = 0;
                                foreach($DebitNoteItem as $DNKey=>$DNVal){
                                    $particularAmt = 0;
                                    foreach($ActualOtherDeductionList as $ADKey =>$ADVal){
                                        if($DNVal["ItemID"] == $ADVal["ParticularItemID"]){
                                            $particularAmt += $ADVal["Amount"];
                                            $quantity = $ADVal["quantity"];
                                        }
                                    }
                                    if($DNVal["ItemID"] == "QOD"){
                                        $particularAmt += $QualityDeduction;
                                        $rate_per_kg = $particularAmt / $NetWt_in_kg;
                                        $quantity = $NetWt_in_kg;
                                    }
                                    ?>
                                        <tr>
                                            <td style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;"><?php echo $i;?></td>
                                            <td colspan="3" style="border-right: 1px solid #333;border-bottom: 1px solid #333;"><?php echo $DNVal["ItemName"];?></td>
                                            <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:center;">12010090</td>
                                            <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($quantity, 2, '.', ''); ?></td>
                                            <td colspan="2" style="border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($rate_per_kg, 3, '.', ''); ?></td>
                                            <td colspan="2" style="border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($particularAmt, 2, '.', ''); ?></td>
                                        </tr>
                                <?php
                                $i++;
                                }
                                
                            ?>
        
                            <?php
                                $QCGstAmt = ($AllDeduction * $taxrate) /100;
                                $final_deduction = $AllDeduction + $QCGstAmt;
                                if($getControl_details->state == "MH"){
                                    $QCCGSTAmt = $QCGstAmt / 2;
                                    $QCSGSTAmt = $QCGstAmt / 2;
                                    $QCIGSTAmt = 0;
                                }else{
                                    $QCIGSTAmt = $QCGstAmt;
                                    $QCSGSTAmt = 0;
                                    $QCCGSTAmt = 0;
                                }
                                
                            ?>
                            
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
                                <td colspan="2" style="width:15%;border-bottom: 1px solid #333;text-align:right;"><b><?php echo number_format($AllDeduction, 2, '.', ''); ?></b></td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Purchase Invoice</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($PurchaseValue, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($GstAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($NetPurchaseAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">CGST + @<?php echo number_format($CGSTPer, 2, '.', ''); ?>%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCCGSTAmt, 2, '.', ''); ?></td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Debit Note</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($AllDeduction, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCGstAmt, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($final_deduction, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">SGST + @<?php echo number_format($SGSTPer, 2, '.', ''); ?>%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCSGSTAmt, 2, '.', ''); ?></td>
                            </tr>
        
                            <tr>
                                <td colspan="2" style="width:17%;border-right: 1px solid #333;border-bottom: 1px solid #333;">Net</td>
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>
                                <td style="width:10%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format(($GstAmt - $QCGstAmt), 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($NetPurchaseAmt - $final_deduction, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:15%;border-right: 1px solid #333;border-bottom: 1px solid #333;">IGST + @<?php echo number_format($IGSTPer, 2, '.', ''); ?>%</td>
                                <td colspan="2" style="width:15%;text-align:right;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($QCIGSTAmt, 2, '.', ''); ?></td>
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
                                <td colspan="12" style="height:20px;width:100%;border-top: 1px solid #333;border-bottom: 1px solid #333;"><b>Final Rate: <?php echo number_format($Finalrate, 2, '.', ''); ?></td>
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
                                <td colspan="2" style="width:20%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>
                                <td colspan="2" style="width:18%;text-align:right;border-right: 1px solid #333;border-bottom: 1px solid #333;text-align:right;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>
                                <td colspan="6"></td>
                            </tr>
                    <?php } ?>
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
                        if($status == "13" && $details->QCApprove == "Y"){
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
                            
                            <div class="col-md-3" style="margin-top:2%">
                                <button type="button" id="changeQC" class="btn btn-primary">Change Center QC</button>
                            </div>
                            <div class="col-md-3 ml-1" style="margin-top:2%;margin-left: 3%;">
                                <button type="button" id="ContinueQC" class="btn btn-primary">Continue with Center QC</button>
                            </div>
                        </div>
                        
                        <div class="row" id="modify_qc" style="display:none">
                            <div class="col-md-12" style="padding:0px;">
                                <form id="final_qc_form_RO" method="POST" enctype="multipart/form-data" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
            		            <table class=" table-striped table-bordered" >
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
                                        <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>
                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
                                        <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>" hidden>
                                        <input type="text" name="TareWeight" id="TareWeight" value="<?php echo number_format(($details->TareWeight/10), 3, '.', ''); ?>" hidden>
                                        <input type="text" name="QC_for" id="QC_for" value="RO" hidden>
            		                  <?php foreach($peripheral as $key=>$value){
                                            $paraValue = "";
	                                        foreach($finalQC as $key=>$value1){ 
		                                        if($value1['ItemParameterID']==$value['ItemParameterID']){
		                                            $paraValue = $value1['EParameterValue'];
		                                        }
	                                        }
                                        ?>
                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" value = "<?php echo number_format($paraValue, 2, '.', '');?>" class="form-control" onkeypress="return isNumber(this,event)"></td>
                                        <?php } ?>
            		                  
            		                  </tr>
            		                </tbody>
            		          </table>
            		        </div>
            		        <div class="col-md-4">
		                        <div class="form-group" app-field-wrapper="fQCSlip_ro">
                                    <small class="req text-danger">* </small>
                                    <label for="fQCSlip_ro" class="control-label">Upload QC Slip</label>
                                    <input type="file" name="fQCSlip_ro" id="fQCSlip_ro" class="form-control">
            					</div>
        					</div>
        					<div class="col-md-4">
		                        <div class="form-group" app-field-wrapper="QCApproval">
                                    <small class="req text-danger">* </small>
                                    <label for="QCApproval" class="control-label">QC Approval</label>
                                    <select name="QCApproval" id="QCApproval" class=" form-control" >
                                        <option value="1" selected>Auto Approval</option>
                                        <option value="2">Farmer / Trader Approval</option>
                                    </select>
            					</div>
        					</div>
            		        <div class="col-md-2" style="margin-top:2%">
                                <button type="button" id="ModifyROQC" class="btn btn-primary">Update RO QC</button>
                            </div>
            		        </form>
                            
                            <div class="col-md-2" style="margin-top:2%">
                                <button type="button" id="CancelModifyQC" class="btn btn-default">Cancel</button>
                            </div>
                        </div>
                    <?php
                        }elseif($status == "13"){
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Waiting for Center QC Approval from Party</h4>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                    <?php
                        if(($status == "14" && $details->IsQcUpdate == "Y") || ($details->IsHoUpdate == "N" && $status == "15")){
                            ?>
                            <div class="row">
                                <div class="col-md-3" style="margin-top:2%">
                                    <button type="button" id="changeQCHO" class="btn btn-primary">Change RO QC Parameter</button>
                                </div>
                                <div class="col-md-3" style="margin-top:2%; margin-left:2%;">
                                    <button type="button" id="ContinueQCHO" class="btn btn-primary">Continue with RO QC</button>
                                </div>
                            </div>
                            
                            <!-- HO QC Form -->
                            <div class="row" id="modify_qcHO" style="display:none">
                                <form id="final_qc_form_HO" method="POST" enctype="multipart/form-data" action="<?php echo admin_url(); ?>GateControl/updateFinalQC">
                                <div class="col-md-12" style="padding:0px;">
                		            <table class=" table-striped table-bordered" >
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
    	                                        <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>
    	                                        <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>
    	                                        <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight/10), 3, '.', ''); ?>" hidden>
    	                                        <input type="text" name="TareWeight" id="TareWeight" value="<?php echo number_format(($details->TareWeight/10), 3, '.', ''); ?>" hidden>
    	                                        <input type="text" name="QC_for" id="QC_for" value="HO" hidden>
                                        <?php 
                                            foreach($peripheral as $key=>$value){
                                                $paraValue = "";
    	                                        foreach($finalQC as $key=>$value1){ 
    		                                        if($value1['ItemParameterID']==$value['ItemParameterID']){
    		                                            $paraValue = $value1['HParameterValue'];
    		                                        }
    	                                        }
                                            ?>
                                                <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" value = "<?php echo number_format($paraValue, 2, '.', '');?>" class="form-control" onkeypress="return isNumber(this,event)"></td>
                                        <?php 
                                            }
                                        ?>
                		                  </tr>
                		                </tbody>
                		          </table>
                		        </div>
                		        <div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="fQCSlip">
                                        <small class="req text-danger">* </small>
                                        <label for="fQCSlip" class="control-label">Upload QC Slip</label>
                                        <input type="file" name="fQCSlip" id="fQCSlip" class="form-control">
                					</div>
            					</div>          
            					<div class="col-md-4">
    		                        <div class="form-group" app-field-wrapper="QCApproval">
                                        <small class="req text-danger">* </small>
                                        <label for="QCApproval" class="control-label">QC Approval</label>
                                        <select name="QCApproval" id="QCApproval" class="form-control" >
                                            <option value="1" selected>Auto Approval</option>
                                            <option value="2">Farmer / Trader Approval</option>
                                        </select>
                					</div>
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
                        }elseif($status == "14"){
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Waiting for RO QC Approval from Party</h4>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                    <?php 
                        if($status == "15" && $details->IsHoUpdate == "Y") {
                            
                    ?>
                        <div class="row">
                            <div class="col-md-6">
                                <form id="Add_Other_Deduction" method="POST" action="<?php echo admin_url(); ?>GateControl/Add_Other_Deduction">
                                <input type="text" name="id" value="<?php echo $details->id; ?>" hidden>
                                <input type="text" name="BookingID" value="<?php echo $details->BookingID; ?>" hidden>
                                <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>
                                <input type="text" name="TransID" value="<?php echo $TransID; ?>" hidden>
                                <input type="text" name="WeightShortInKg" id="WeightShortInKg" value="<?php echo $WeightShortInKg; ?>" hidden>
                                <input type="text" name="RatePerKg" id="RatePerKg" value="<?php echo $RatePerKg; ?>" hidden>
                		            <table class=" table-striped table-bordered" >
                		                <thead>
                		                    <tr>
                		                        <th>Particular</th>
                		                        <th>Amount</th>
                		                    </tr>
                		                </thead>
                		                <tbody>
                		                    <?php 
                		                        
                		                        $IsCDApplicable = $details->cd_applicable;
                		                        $CDPercentage = $details->cd_percentage;
                		                        $lebletext = 'Add Other Deduction';
                		                        foreach($OtherDeductionMasterList as $key=>$val){
                		                            $Amount = 0;
                		                            $match = 0;
                		                            $ItemName = $val["ItemName"];
                		                            if($val["ItemID"] == "CD" && $IsCDApplicable == "Y"){
                		                                $ItemName = $val["ItemName"] ." (".$CDPercentage."%)";
                		                                $Amount = $PurchaseValue * ($CDPercentage / 100);
                		                            }
                		                            foreach($ActualOtherDeductionList as $Vkey=>$Vval){
                		                                if($val["ItemID"]==$Vval["ItemID"]){
                		                                     $Amount = $Vval["Amount"];
                		                                     $lebletext = 'Update Other Deduction';
                		                                     $match++;
                		                                }
                		                            }
                		                            //Auto Calculate Unloading Charges
                		                            if($val['ItemID'] == "UNL" && $Amount == "0" && $match == "0"){
                		                               $Amount = $NetWeight_MT * 20;
                		                            }
                		                            // Set Defualt Bank Commission
                		                            
                		                            if($val['ItemID'] == "BNKCOMM" && $Amount == "0" && $match == "0"){
                		                               $Amount = 30;
                		                            }
                		                            ?>
                		                                <tr>
                                		                    <td><?php echo $ItemName;?></td>
                                		                    <td><input style="height:30px;width:100%;"  class="form-control" name="OthDeduction[<?php echo $val['ItemID'];?>]" value="<?php echo $Amount;?>"></td>
                                		                </tr>
                		                           <?php
                		                        }
                		                    ?>
                		                   
                		                  <tr>
                		                      <td colspan="2" style="text-align:right;height:46px;"><button type="submit" class="btn btn-primary"><?php echo $lebletext;?></button></td>
                		                  </tr>
                		                </tbody>
                		          </table>
                		        </form>
                		    </div>
                		    
                		    <div class="col-md-6">
                		        <?php 
                                    
                                    $defualtAmtPay = (($NetValue) - (($NetValue * 20) / 100) );
                                    $deduction_added = "N";
                                    if($ActualOtherDeductionList){
                                        $deduction_added = "Y";
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="hidden" name="NetAmt" id="NetAmt" value="<?php echo $NetValue;?>">
                                        <small class="req text-danger">* </small>
                                        <label for="payment_perc" class="form-label">Enter Payment %(Excl. GST Amt)</label>
                                        <input type="text" name="payment_perc" id="payment_perc" class="form-control" value="80" onkeypress="return isNumber(this,event)"> 
                                    </div>
                                    <div class="col-md-4">
                                        <label for="payment_perc" class="form-label">Amount (₹)</label>
                                        <input type="hidden" name="CustomerType" id="CustomerType" value="<?php echo $getControl_details->CustomerType; ?>">
                                        <input type="hidden" name="final_rate" id="final_rate" value="<?php echo $Finalrate; ?>">
                                        <input type="hidden" name="other_deduction_add" id="other_deduction_add" value="<?php echo $deduction_added; ?>">
                                        <input type="text" name="payment_Amt" id="payment_Amt" class="form-control" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>" readonly>
                                        <input type="hidden" name="payment_Amt2" id="payment_Amt2" value="<?php echo number_format($defualtAmtPay, 2, '.', ''); ?>">
                                        <input type="hidden" name="ActualWeight" id="ActualWeight" value="<?php echo number_format($NetWeight_MT, 3, '.', ''); ?>">
                                        
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2" style="margin-top:2%" id="SendButton">
                                        <button type="button" id="ForApproval" class="btn btn-primary">Approve</button>
                                    </div>
                                </div>
                		    </div>
                        </div>
                    <?php 
                        }elseif($status == "15" && $details->IsHoUpdate == "NA"){
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Waiting for HO QC Approval from Party</h4>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                    
                  </div>
                </div>
              </div>
            </div>
        
    </div><!-- End Content div-->
</div><!-- End Wrapper div-->

<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        var WHID = $("#WHID").val();
        GetChamberList(WHID);
    });
    function GetChamberList(WHID){
	    $.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetChamberList",
			method:"POST",
			dataType:"JSON",
			data:{
				WHID:WHID
			},
			success:function(fin){
				var options = "<option value=''>Non Selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.CHID + "'>" + value.ChaumberName  + "</option>";
				});
				chamber_hidden = $('#chamber_hidden').val();
				$('select[name=chamber]').html(options);
				$('.selectpicker').selectpicker('refresh');
				$('select[name=chamber]').val(chamber_hidden);
				$('.selectpicker').selectpicker('refresh');
				if(chamber_hidden !=""){
				    GetStackList(chamber_hidden);
				}
			}	
		});
	}
	
	function GetStackList(CHID){
	    $.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetWarehouseStackList",
			method:"POST",
			dataType:"JSON",
			data:{
				CHID:CHID
			},
			success:function(fin){
				var options = "<option value=''>Non Selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.StackID + "'>" + value.StackName + "</option>";
				});
				Stack_hidden = $('#Stack_hidden').val();
				$('select[name=Stack]').html(options);
				$('.selectpicker').selectpicker('refresh');
				$('select[name=Stack]').val(Stack_hidden);
				$('.selectpicker').selectpicker('refresh');
				if(Stack_hidden !=""){
				    GetLotList(Stack_hidden);
				}
			}	
		});
	}
    	
    $('#chamber').change(function(){
	    var Value = $(this).val();
	    GetStackList(Value);
	});
	function GetLotList(StackID){
	    $.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetStackLotList",
			method:"POST",
			dataType:"JSON",
			data:{
				StackID:StackID
			},
			success:function(fin){
				var options = "<option value=''>Non Selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.LOTID + "'>" + value.LotName + "</option>";
				});
				LOTID_hidden = $('#LOTID_hidden').val();
				$('select[name=LOTID]').html(options);
				$('.selectpicker').selectpicker('refresh');
				$('select[name=LOTID]').val(LOTID_hidden);
				$('.selectpicker').selectpicker('refresh');
			}	
		});
	}
	
	$('#Stack').change(function(){
	    var Value = $(this).val();
	    GetLotList(Value);
	});
</script>

<script>
    function calculate_total_weight() {
        p = $("#table-purchase_request tbody tr.item");
        var TotalStackWeight = 0;
        p.each(function() {
            var val = $(this).find("[data-quantity]").val();
            //alert(val);
            if(parseFloat(val) > 0){
                TotalStackWeight = parseFloat(TotalStackWeight) + parseFloat(val);
            }
            
            //alert(val);
        })
        $('#TotalStackWeight').val(parseFloat(TotalStackWeight).toFixed(3));
    }
    function addrow()
    {
        var TotalStackWeight = $('#TotalStackWeight').val();
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#TareWeight').val();
        var bag_weight = $('#bag_weight').val();
        var CHID = $('#chamber').val();
        var StackID = $('#Stack').val();
        var LOTID = $('#LOTID').val();
        var lot_weight = $('#lot_weight').val();
        var lot_quantity = $('#lot_quantity').val();
        var NetWeight = parseFloat(GrossWeight).toFixed(3) - parseFloat(TareWeight).toFixed(3) - (parseFloat(bag_weight) / 1000).toFixed(3);
        if(parseFloat(GrossWeight).toFixed(3) <= 0){
            alert('Please Enter Gross Weight');
        }else if(parseFloat(TareWeight).toFixed(3) <= 0){
            alert('Please Enter Tare Weight');
        }else if(parseFloat(TotalStackWeight).toFixed(3) > parseFloat(NetWeight).toFixed(3)){
            alert("Total Stack Weight is greter than Net Weight");
        }else if(CHID == ""){
            alert("Please Select Chamber");
        }else if(StackID == ""){
            alert("Please Select Stack");
        }else if(LOTID == ""){
            alert("Please Select LOT");
        }else if(lot_weight == ""){
            alert("Please Enter Weight in MT");
        }else if(lot_quantity == ""){
            alert("Please Enter Bag Quantity");
        }else{
            var TotalLot = parseInt($('#TotalLot').val());
            TotalLot++;
            
            var html = '';
            html +='<tr class="item">';
            html +='<td>'+CHID+' <input hidden name="StackList[' + TotalLot + '][Chamber]" value="'+CHID+'"></td>';
            html +='<td>'+StackID+' <input hidden name="StackList[' + TotalLot + '][Stack]" value="'+StackID+'"></td>';
            html +='<td>'+LOTID+' <input hidden name="StackList[' + TotalLot + '][Lot]" value="'+LOTID+'"></td>';
            html +='<td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control"  name="StackList[' + TotalLot + '][WeightMT]" value="'+lot_weight+'" onkeypress="return isNumber(this,event)"></td>';
            html +='<td> <input style="height:30px;width:100%;"  class="form-control" name="StackList[' + TotalLot + '][BagQty]" value="'+lot_quantity+'" onkeypress="return isNumber(this,event)"></td>';
            html +='<td> <button class="remove form-control" type="button" style="font-size:20px;color:red;" ><i class="fa fa-trash"></i></button></td>';
            html +='</tr>';
            $('#stack_tbody').append(html);
            $('#chamber').val('');
            $('#Stack').val('');
            $('#LOTID').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#lot_weight').val('');
            $('#lot_quantity').val('');
            $('#TotalLot').val(TotalLot);
        }
    }
    $('#stack_tbody').on('click', '.remove', function () {
        // Removing the current row.
        $(this).closest('tr').remove();
        var TotalLot = parseInt($('#TotalLot').val());
        TotalLot--;
        $('#TotalLot').val(TotalLot);
        calculate_total_weight();
    });
    function addLayer(){
        var no_of_layers = parseInt($('#no_of_layers').val());
        
        var layer_details = <?php echo json_encode($layers); ?>;
        if(layer_details.length > 0){
            var layer_parameters = layer_details[0].parameter_detail;
        }else{
            var layer_parameters = <?php echo json_encode($peripheral); ?>;
        }
        no_of_layers += 1;
        
        var html = '';
        
        html +='<tr>';
        html +='<td id="layer_number'+no_of_layers+'">' + no_of_layers + '</td>';
        html +='<td><input style="width: 50px;" id="layer_quantity'+ no_of_layers +'" value="" onkeypress="return isNumber(this,event)"></td>';
        html +='<td>BAG</td>';
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
    
    
    
    function update_unloading_qc()
    {
        var no_of_layers = $('#no_of_layers').val();
        var inner_item_count = $('#inner_item_count').val();
        
        var Booking_ID = $('#BookingID').val();
        var id = $('#GatID').val();
        var Gate_IN_ID = $('#GateINID').val();
        var ItemID = $('#ItemIDLayer').val();
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
            data:{Booking_ID:Booking_ID,Gate_IN_ID:Gate_IN_ID,id:id,unloading_array:unloading_array,ItemID:ItemID},
            beforeSend:function(){
                $('#sendrequest').html('Please wait request sending.');
            },
            success:function(r){
                window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+GatID);
            }
        });
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
                $('#payment_Amt').val(parseFloat(defualtAmtPay).toFixed(3));
                $('#payment_Amt2').val(parseFloat(defualtAmtPay).toFixed(3));
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
        var NetAmt = $('#NetAmt').val();
        var final_rate = $('#final_rate').val();
        var GatID = $('#GatID').val();
        var PaymentAmt = $('#payment_Amt2').val();
        var PaymentPer = $('#payment_perc').val();
        var other_deduction_add = $('#other_deduction_add').val();
        var CustomerType = $('#CustomerType').val();
        var ActualWeight = $('#ActualWeight').val();
        if(other_deduction_add == "N"){
            alert('Please add other deduction first');
        }else{
            if (confirm("Do you want to Approve Payment Advice?") == true) {
                $.ajax({
                    url:"<?php echo admin_url(); ?>GateControl/ApprovePaymentAdvice",
                    dataType:"json",
                    method:"POST",
                    data:{GateINID:GateINID,BookingID:BookingID,PaymentAmt:PaymentAmt,PaymentPer:PaymentPer,
                    final_rate:final_rate,CustomerType:CustomerType,NetAmt:NetAmt,ActualWeight:ActualWeight},
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
    
    
    function isNumber(txt,event){
        var charCode = (event.which) ? event.which : event.keyCode
        if (charCode == 46) {
            if (txt.value.indexOf(".") < 0)
                return true;
            else
                return false;
        }

        if (txt.value.indexOf(".") > 0) {
            var txtlen = txt.value.length;
            var dotpos = txt.value.indexOf(".");
            //Change the number here to allow more decimal points than 2
            if ((txtlen - dotpos) > 3)
            {
               return false; 
            } 
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
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
    
    //Check for status and restrict edit functionality
    /*var SendData = <?php echo $SendInwardToPcSoftCheck->pcsoft_doc_ref; ?>
    if(SendData == ""){
        $('.updateCheck').prop('disabled', false);
    }else{
        $('.updateCheck').prop('disabled', true);
    }*/
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
    // Save Center QC Validation
    $('#saveBtn').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#TareWeight').val();
        var Qcslip = $('#fQCSlip').val();
        if(GrossWeight <= 0){
            alert('please add Gross Weight first');
        }else if(TareWeight <= 0){
            alert('please add Tare Weight first');
        }else if(Qcslip == ''){
            alert('please QC Slip upload');
        }else{
            $('#final_qc_form').submit();
        }
    });
    
    // Save RO QC Validation
    $('#ModifyROQC').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#TareWeight').val();
        var Qcslip = $('#fQCSlip_ro').val();
        if(GrossWeight <= 0){
            alert('please add Gross Weight first');
        }else if(TareWeight <= 0){
            alert('please add Tare Weight first');
        }else if(Qcslip == ''){
            alert('please QC Slip upload');
        }else{
            $('#final_qc_form_RO').submit();
        }
    });
    
    // Save tare Weight Validation
    $('#TareWeightSubmit').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#tare_weight').val();
        
        if(parseFloat(TareWeight) >= parseFloat(GrossWeight)){
            alert('please enter Tare Weight is less than Gross Weight');
        }else if(parseFloat(TareWeight) <= 0){
            alert('please enter tare weight is grater than zero');
        }else{
            $('#tare_weight_form').submit();
        }
    });
    
    // Save Stack Details and validation Validation
    $('#StackSubmit').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#tare_weight').val();
        var bag_weight = $('#bag_weight').val();
        var NetWeight = GrossWeight - TareWeight - (parseFloat(bag_weight) / 1000);
        var TotalStackWeight = $('#TotalStackWeight').val();
        if(parseFloat(TareWeight) >= parseFloat(GrossWeight)){
            alert('please enter Tare Weight is less than Gross Weight');
        }else if(parseFloat(TareWeight) <= 0){
            alert('please enter tare weight is grater than zero');
        }else if(parseFloat(TotalStackWeight).toFixed(3) < parseFloat(NetWeight).toFixed(3)){
            alert('Please Enter Stack Weight is Equal to Net Weight');
        }else if(parseFloat(TotalStackWeight).toFixed(3) > parseFloat(NetWeight).toFixed(3)){
            alert('Please Enter Stack Weight is Equal to Net Weight');
        }else{
            $('#stack_details_form').submit();
        }
    });
    
    // Save Gross Weight Validation
    $('#GrossWeightSubmit').click(function(){
        var GrossWeight = $('#total_weight').val();
        var TareWeight = $('#tare_weight').val();
        if(parseFloat(TareWeight) >= parseFloat(GrossWeight)){
            alert('please enter Gross Weight is grater than Tare Weight');
        }else if(parseFloat(GrossWeight) <= 0){
            alert('please enter gross weight is grater than zero');
        }else{
            $('#gross_weight_form').submit();
        }
    });
    
    
    
    // Gate Out Validation Validation
    
    $('#GenerateGateOut').click(function(){
        var GrossWeight = $('#GrossWeight').val();
        var TareWeight = $('#TareWeight').val();
        var KYC = $('#KYC').val();
        var BookingID = $('#BookingID').val();
        var GateINID = $('#GateINID').val();
        var BookingType = $('#BookingType').val();
        var StockWeightCheck = $('#StockWeightCheck').val();
        var NetWeightCheck = $('#NetWeightCheck').val();
        if(KYC < 6){
            alert('please complate KYC first');
        }else if(TareWeight <= 0){
            alert('please enter Tare Weight');
        }else if(GrossWeight <= 0){
            alert('please enter Gross Weight');
        }else if(StockWeightCheck > NetWeightCheck){
            alert('Stock details weight is greter than net weight please check and update stock weight');
        }else if(StockWeightCheck < NetWeightCheck){
            alert('Stock details weight is less than net weight please check and update stock weight');
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

</body>
</html>
