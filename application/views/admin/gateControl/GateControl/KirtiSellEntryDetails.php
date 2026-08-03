<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style>
    .table-purchase_request {
        overflow: auto;
        max-height: 65vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-purchase_request thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-purchase_request tbody th {
        position: sticky;
        left: 0;
    }





    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 0px;
    }

    th,
    td {
        padding: 1px 5px !important;
        white-space: nowrap;
        border: 1px solid !important;
        font-size: 11px;
        line-height: 1.42857143 !important;
        vertical-align: middle !important;
    }

    th {
        background: #50607b;

        color: #fff !important;
    }

    tbody#for_uppercase {

        text-transform: uppercase;

    }



    .btn-top-toolbar {

        position: fixed;

        top: 8.5%;

        padding: 5px 0px;

        -webkit-box-shadow: 0 -4px 1px -4px rgba(0, 0, 0, .1);

        box-shadow: 0 -4px 1px -4px rgba(0, 0, 0, .1);

        /*background: #50607b;*/

        color: #fff;

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

                            <div class="col-md-12 text-centerr">

                                <nav aria-label="breadcrumb">

                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

                                        <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

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

                                                if ($details->company != '') {

                                                    $PartyName = $details->company;
                                                } else {

                                                    $PartyName = $details->firstname . ' ' . $details->lastname;
                                                }

                                                ?>

                                                <td><b>Party Name : </b></td>

                                                <td><?php echo $PartyName; ?></td>

                                            </tr>



                                            <tr>

                                                <td><b>Booking ID : </b></td>

                                                <td><b><?php echo $details->BookingID; ?></b></td>

                                                <?php

                                                if ($details->CustomerType == 1) {

                                                    $PartyType = 'Farmer';
                                                } elseif ($details->CustomerType == 2) {

                                                    $PartyType = 'Broker';
                                                } elseif ($details->CustomerType == 3) {

                                                    $PartyType = 'Trader';
                                                } elseif ($details->CustomerType == 4) {

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

                                                <td><?php echo ($SName['asn_by']->firstname . ' ' . $SName['asn_by']->lastname) ?></td>

                                                <td><b>ASN Date: </b></td>

                                                <td><?php echo _d($details->asn_date); ?></td>

                                            </tr>

                                            <tr>

                                                <?php

                                                if (($details->status == 1) || ($details->status > 1)) {

                                                ?>

                                                    <td><b>ASN : </b></td>

                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewAsn/<?php echo $details->BookingID . "/" . $details->ASNID; ?>" target="_blank">View ASN</a></td>

                                                <?php

                                                }

                                                ?>

                                                <?php

                                                if (($details->status == 2) || ($details->status > 2)) {

                                                ?>

                                                    <td><b>Gate In Pass : </b></td>

                                                    <td><a href="<?php echo admin_url(); ?>GateControl/viewGetInPass/<?php echo $details->BookingID . '/' . $details->Gate_in_ID; ?>" target="_blank">View Gate In Pass</a></td>

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

                                                <td><?php echo ($SName['gate_in_by']->firstname . ' ' . $SName['gate_in_by']->lastname) ?></td>

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

                                                if ($details->status == 1) {

                                                    $status_val = "ASN GENERATED";
                                                } else if ($details->status == 2) {

                                                    $status_val = "GATE IN GENERATED";
                                                } else if ($details->status == 3) {

                                                    $status_val = "EMPTY VEHICLE WEIGHT DONE";
                                                } else if ($details->status == 4) {

                                                    $status_val = "LOADING IN PROGRESS ";
                                                } else if ($details->status == 5) {

                                                    $status_val = "LOADING FINISHED ";
                                                } else if ($details->status == 6) {

                                                    $status_val = "LOADING QC DONE";
                                                } else if ($details->status == 7) {

                                                    $status_val = "GROSS WEIGHT CAPTURED";
                                                } else if ($details->status == 8) {

                                                    $status_val = "PAYMENT DONE";
                                                } else if ($details->status == 9) {

                                                    $status_val = "GATE OUT GANERATED";
                                                } else if ($details->status == 10) {

                                                    $status_val = "MARK AS EXIT";
                                                }

                                                ?>

                                                <td><b>Status : </b></td>

                                                <td><?php echo $status_val; ?></td>

                                            </tr>

                                            <tr>

                                                <td><b>Delivery Order : </b></td>

                                                <?php

                                                $InvUrl = admin_url() . "GateControl/ViewDO/" . $details->BookingID . "/" . $details->ASNID;

                                                ?>

                                                <td><a href="<?php echo $InvUrl; ?>" target="_blank" title="View Delivery Order">Click to View Delivery Order</a></td>

                                                <td><b>Vendor Invoice Date : </b></td>
                                                <td><?= _d($details->vendor_invoice_date ?? ''); ?></td>

                                            </tr>

                                            <tr>
                                                <td><b>Vendor Invoice Number : </b></td>
                                                <td><?= $details->vendor_invoice_number ?? ''; ?></td>
                                                <td><b>Vendor Invoice Doc : </b></td>
                                                <td><?= (!empty($details->vendor_invoice_doc)) ? '<a href="' . base_url() . $details->vendor_invoice_doc . '" target="_blank" title="View Vendor Invoice Doc">Click to View</a>' : ''; ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Vendor Ewaybill Number : </b></td>
                                                <td><?= $details->vendor_ewaybill_number ?? ''; ?></td>
                                                <td><b>Vendor Ewaybill Doc : </b></td>
                                                <td><?= (!empty($details->vendor_ewaybill_doc)) ? '<a href="' . base_url() . $details->vendor_ewaybill_doc . '" target="_blank" title="View Vendor Ewaybill Doc">Click to View</a>' : ''; ?></td>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                            <?php

                            if ($SendInwardToPcSoftCheck->pcsoft_doc_ref) {

                                $disableCheck = "disabled";
                            } else {

                                $disableCheck = "";
                            }

                            // Check stages for add or update Stack details

                            // Gross Weight, tare Weight, Bag Weight

                            if ($details->LoadedWeight > 0 && $details->TareWeight > 0) {

                                if ($details->status >= 8) {

                                    // After Generate Challan button disabled

                                    $ChkStackAddUpdate = "disabled";
                                } else {

                                    // After add Tare weight and gross weight , before generate challan stack button will enable

                                    $ChkStackAddUpdate = "";
                                }
                            } else {

                                // before enter gross weight and tare weight stack button will disabled

                                $ChkStackAddUpdate = "disabled";
                            }

                            // Lock Tare Weight,gross weight and bag weight after add stack details

                            if ($StackList) {

                                $LockWeight = 'disabled';

                                $UnlockChallan = "";
                            } else {

                                $LockWeight = "";

                                $UnlockQc = "disabled";

                                $UnlockChallan = "disabled";
                            }



                            ?>

                            <!-- Add Field Officer Name -->

                            <form id="AddFieldOfficer" method="POST" action="<?php echo admin_url(); ?>GateControl/AddFieldOfficer">

                                <div class="col-md-3">

                                    <input type="text" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>

                                    <input type="text" name="id" id="id" value="<?php echo $details->id ?>" hidden>

                                    <div class="form-group" app-field-wrapper="FeildOfficer">

                                        <small class="req text-danger">* </small>

                                        <label for="FeildOfficer" class="control-label">Select Field Officer</label>

                                        <select name="FeildOfficer" id="FeildOfficer" class="selectpicker form-control" data-live-search="true">

                                            <option value="">Non Selected</option>

                                            <?php

                                            foreach ($StaffList as $key => $val) {

                                            ?>

                                                <option value="<?php echo $val["AccountID"]; ?>" <?php if ($val["AccountID"] == $details->FeildOfficer) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $val["firstname"] . " " . $val["lastname"]; ?></option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-2" style="margin-top: 20px;">

                                    <button type="submit" class="btn btn-info btn-sm">Add Field Officer</button>

                                </div>

                            </form>



                            <!-- Update Godown details-->

                            <form id="UpdateGodown" method="POST" action="<?php echo admin_url(); ?>GateControl/UpdateGodown">

                                <div class="col-md-3">

                                    <input type="text" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>

                                    <input type="text" name="id" id="id" value="<?php echo $details->id ?>" hidden>

                                    <div class="form-group" app-field-wrapper="GodownID">

                                        <small class="req text-danger">* </small>

                                        <label for="GodownID" class="control-label">Send Vehicle To</label>

                                        <select name="GodownID" id="GodownID" class="selectpicker form-control" data-live-search="true">

                                            <option value="">Non Selected</option>

                                            <?php

                                            foreach ($GodownList as $key => $val) {

                                            ?>

                                                <option value="<?php echo $val["AccountID"]; ?>" <?php if ($val["AccountID"] == $details->GodownID) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $val["w_name"]; ?></option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </div>

                                </div>

                                <div class="col-md-2" style="margin-top: 20px;">

                                    <?php

                                    if (empty($StackList)) {

                                        $GodownDisabled = '';
                                    } else {

                                        $GodownDisabled = 'disabled';
                                    }

                                    ?>

                                    <button type="submit" class="btn btn-success btn-sm" <?php echo $GodownDisabled; ?>>Update Godown</button>

                                </div>

                            </form>

                            <div class="clearfix"></div>



                            <div class="col-md-4">

                                <small class="req text-danger">* </small>

                                <label for="reason" class="form-label">Rejection Reason</label>

                                <textarea name="rejection_reason" <?php echo $RejectButton; ?> id="rejection_reason" class="form-control"></textarea>

                            </div>

                            <div class="col-md-2" style="margin-top: 20px;">

                                <?php

                                if ($details->TareWeight > 0 || $details->status == 8) {

                                    $RejectButton = "disabled";
                                } else {

                                    $RejectButton = "";
                                }

                                ?>

                                <button type="button" class="btn btn-danger btn-sm" id="RejectOutward" <?php echo $RejectButton; ?>>Reject Outward</button>

                            </div>



                        </div><!-- First row end-->

                        <div class="row" style="margin:auto;width:100%;">

                            <h4>Loaded Vehicle Weight Details</h4>

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

                                                <td><input style="width:70px;" id="total_weight" name="total_weight" value="<?php echo number_format(($details->LoadedWeight / 10), 3, '.', ''); ?>" onkeypress="return isNumber(this,event)"> </td>

                                                <td><a target="_blank" href="<?php echo base_url() . $details->VhlTopImage ?>" target="_blank">View Image</a></td>

                                                <td><a target="_blank" href="<?php echo base_url() . $details->VhlFrontImage ?>" target="_blank">View Image</a></td>

                                                <td><a target="_blank" href="<?php echo base_url() . $details->VHLSideImage ?>" target="_blank">View Image</a></td>

                                                <td><?php echo ($staffName['LWUserID']->firstname . ' ' . $staffName['LWUserID']->lastname) ?></td>

                                                <td><?php echo _d($details->LWTransDate); ?></td>



                                                <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>

                                                <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>

                                                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>

                                                <td><button class="updateCheck" <?php echo $LockWeight; ?> type="button" id="GrossWeightSubmit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </form>

                            </div>

                        </div> <!-- Tare Weight Row End-->



                        <div class="row" style="margin:auto;width:100%;">

                            <h4>Empty Vehicle Weight Details</h4>

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

                                                <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight / 10), 3, '.', ''); ?>" hidden>

                                                <input type="text" name="id" value="<?php echo $details->id ?>" hidden>



                                                <td><input style="width:70px;" id="tare_weight" name="tare_weight" value="<?php echo number_format(($details->TareWeight / 10), 3, '.', ''); ?>" onkeypress="return isNumber(this,event)"> </td>



                                                <td><a target="_blank" href="<?php echo base_url() . $details->TWVhlTopImage ?>">View Image</a></td>

                                                <td><a target="_blank" href="<?php echo base_url() . $details->TWVhlFrontImage ?>">View Image</a></td>

                                                <td><a target="_blank" href="<?php echo base_url() . $details->TWVHLSideImage ?>">View Image</a></td>

                                                <td><?php echo ($staffName['TWUserID']->firstname . ' ' . $staffName['TWUserID']->lastname) ?></td>

                                                <td><?php echo _d($details->TWTransDate); ?></td>

                                                <td><button class="updateCheck" <?php echo $LockWeight; ?> id="TareWeightSubmit" type="button"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </form>





                            </div>

                        </div> <!-- Gross Weight Row End-->



                        <?php

                        if ($details->TType == "P") {

                            $TransID = $details->PurchID;
                        } elseif ($details->TType == "S") {

                            $TransID = $details->SalesID;
                        } else {

                            $TransID = "";
                        }

                        ?>

                        <div class="row">

                            <form id="stack_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateStackDetails">

                                <div class="col-md-10">

                                    <h4>Stack Details </h4>

                                    <input type="text" name="ItemID" value="<?php echo $details->ItemID; ?>" hidden>

                                    <input type="text" name="id" value="<?php echo $details->id; ?>" hidden>

                                    <input type="text" name="TransID" value="<?php echo $TransID; ?>" hidden>

                                    <input type="text" name="PartyID" value="<?php echo $details->PartyID; ?>" hidden>

                                    <input type="text" name="WHID" id="WHID" value="<?php echo $details->GodownID; ?>" hidden>

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

                                                            <option value="">Non Selected</option>

                                                        </select>

                                                    </div>

                                                </td>

                                                <td>

                                                    <div class="form-group" app-field-wrapper="Select Stack">

                                                        <select name="Stack" id="Stack" class="selectpicker form-control" data-live-search="true">

                                                            <option value="">Non Selected</option>

                                                        </select>

                                                    </div>

                                                </td>

                                                <td>

                                                    <div class="form-group" app-field-wrapper="Select LOT">

                                                        <select name="LOTID" id="LOTID" class="selectpicker form-control" data-live-search="true">

                                                            <option value="">Non Selected</option>

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

                                            foreach ($StackList as $key => $val) {

                                                $i++;

                                                $StackWeight += $val['Weight'];

                                                $StockWtCheckForGateOut += $val['Weight'];

                                            ?>

                                                <tr class="item">

                                                    <td><?php echo $val["CHID"]; ?> <input hidden name="StackList[<?php echo $i; ?>][Chamber]" value="<?php echo $val["CHID"]; ?>"></td>

                                                    <td><?php echo $val["StackID"]; ?> <input hidden name="StackList[<?php echo $i; ?>][Stack]" value="<?php echo $val["StackID"]; ?>"></td>

                                                    <td><?php echo $val["LOTID"]; ?><input hidden name="StackList[<?php echo $i; ?>][Lot]" value="<?php echo $val["LOTID"]; ?>"></td>

                                                    <td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control" name="StackList[<?php echo $i; ?>][WeightMT]" value="<?php echo $val["Weight"]; ?>" onkeypress="return isNumber(this,event)"></td>

                                                    <td> <input style="height:30px;width:100%;" class="form-control" name="StackList[<?php echo $i; ?>][BagQty]" value="<?php echo $val["BagQty"]; ?>" onkeypress="return isNumber(this,event)"></td>

                                                    <td> <button class="remove form-control" type="button" style="font-size:20px;color:red;"><i class="fa fa-trash"></i></button></td>

                                                </tr>

                                            <?php

                                            }

                                            ?>

                                        </tbody>

                                    </table>

                                </div>

                                <div class="col-md-3">

                                    <input type="hidden" class="form-control" id="TotalStackWeight" name="TotalStackWeight" value="<?php echo $StackWeight; ?>">

                                    <input type="hidden" class="form-control" id="TotalLot" name="TotalLot" value="<?php echo $i; ?>">

                                    <div class="form-group">

                                        <button class="updateCheck btn btn-success btn-sm" style="margin-top: 10px;" <?php echo $ChkStackAddUpdate; ?> type="button" id="StackSubmit">Update Stack Details</button>



                                    </div>

                                </div>

                            </form>

                        </div>



                        <?php

                        $getControl_details = get_control_details($details->Gate_in_ID);

                        $taxrate = $getControl_details->taxrate;

                        $RatePerKg = $getControl_details->basic_rate / 100;

                        if ($getControl_details->CustomerType == "1") {

                            $taxrate = 0;

                            $PurchaseWeight = ($getControl_details->TareWeight - $getControl_details->LoadedWeight) / 10;

                            $WeightShortInKg = 0;
                        } else {

                            $PurchaseWeight = $getControl_details->Asn_WT_MT;

                            $WeightShortInKg = ($PurchaseWeight - (($getControl_details->TareWeight - $getControl_details->LoadedWeight) / 10)) * 1000;
                        }

                        $PurchaseValue = $PurchaseWeight * ($getControl_details->basic_rate * 10);

                        $GstAmt = $PurchaseValue * ($taxrate / 100);

                        $NetPurchaseAmt = $PurchaseValue + $GstAmt;



                        $NetWeight_MT = $PurchaseWeight - ($WeightShortInKg / 1000);



                        ?>



                        <?php



                        if ($details->LoadedWeight != NULL && $details->TareWeight != NULL) {

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

                                                <td style="font-size:13px"><b>DO Weight(MT)</b></td>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($PurchaseWeight, 3, '.', ''); ?></td>

                                            </tr>

                                            <tr>

                                                <td style="font-size:13px"><b>Sale Amount</b></td>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($PurchaseValue, 2, '.', ''); ?></td>

                                            </tr>

                                            <tr>

                                                <td style="font-size:13px"><b>Actual Weight (MT)</b></td>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($NetWeight_MT, 3, '.', ''); ?></td>

                                            </tr>

                                            <?php

                                            if ($finalQC) {

                                                $TotalDeduction = 0;

                                                foreach ($finalQC as $key => $value1) {

                                                    $TotalDeduction += $value1['deductionAmt'];

                                            ?>

                                                    <tr>

                                                        <td><?php echo $value1['ItemParameterName']; ?></td>

                                                        <td style="text-align:right;"><?php echo $value1['deductionAmt']; ?></td>

                                                    </tr>

                                                <?php

                                                }



                                                foreach ($ActualOtherDeductionList as $ADKey => $ADVal) {

                                                    $TotalDeduction += $ADVal["Amount"];

                                                ?>

                                                    <tr>

                                                        <td><?php echo $ADVal['ItemName']; ?></td>

                                                        <td style="text-align:right;"><?php echo $ADVal['Amount']; ?></td>

                                                    </tr>

                                                <?php

                                                }



                                                $Finalrate = ($PurchaseValue - $TotalDeduction) / $NetWeight_MT;

                                                $NetValue = $Finalrate * $NetWeight_MT;

                                                ?>

                                                <tr>

                                                    <td style="font-size:13px"><b>Total Deduction</b></td>

                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($TotalDeduction, 2, '.', ''); ?></td>

                                                </tr>



                                                <tr>

                                                    <td style="font-size:13px"><b>Final Rate/MT</b></td>

                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($Finalrate, 3, '.', ''); ?></td>

                                                </tr>

                                                <tr>

                                                    <td style="font-size:13px"><b>Net Amount</b></td>

                                                    <td style="font-size:13px;text-align:right;"><?php echo number_format($NetValue, 2, '.', ''); ?></td>

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





                        <!--<div class="row" style="margin:auto;width:100%;">

    		                        <h4>Final QC Details</h4>

    		                            <?php

                                        if ($finalQC) {

                                            $url = admin_url() . 'GateControl/updateFinalQC';

                                            $btn_label = 'Update Center QC';
                                        } else {

                                            $url = admin_url() . 'GateControl/saveFinalQC';

                                            $btn_label = 'Save Center QC';
                                        }

                                        ?>

    		                            <form id="final_qc_form" method="POST" action="<?php echo $url; ?>" enctype="multipart/form-data">

            		                    <div class="col-md-12" style="padding:0px;">

            		                        <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">

            		                            <thead>

            		                                <tr>

            		                                    <?php foreach ($peripheral as $key => $value) {



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

        		                                        <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight / 10), 3, '.', ''); ?>" hidden>

        		                                        <input type="text" name="TareWeight" id="TareWeight" value="<?php echo number_format(($details->TareWeight / 10), 3, '.', ''); ?>" hidden>

        		                                        <input type="text" name="QC_for" id="QC_for" value="Center" hidden>

        		                                        <?php foreach ($peripheral as $key => $value) {

                                                            $paraValue = "";

                                                            foreach ($finalQC as $key => $value1) {

                                                                if ($value1['ItemParameterID'] == $value['ItemParameterID']) {

                                                                    $paraValue = $value1['ParameterValue'];
                                                                }
                                                            }

                                                        ?>

        		                                            <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" value = "<?php echo number_format($paraValue, 2, '.', ''); ?>" class="form-control" onkeypress="return isNumber(this,event)"></td>

        		                                        <?php } ?>

            		                                </tr>

            		                            </tbody>

            		                        </table>

            		                       </div>

            		                        <?php

                                            if ($details->status >= 8) {
                                            } else {

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

            		                        <div class="clearfix"></div>

                		                    <div class="col-md-6">

                		                        <button class="saveBtn btn btn-info" <?php echo $UnlockQc; ?> id="saveBtn" type="button"><?php echo $btn_label; ?></button>

                                            </div>

            		                    

            		                    

            		                    <?php }

                                        ?>

            		                  </form>

            		                </div>--> <!-- Center QC Row End-->

                        <?php

                        $GateInButtonCheck = "disabled";

                        if ($details->ChallanID == NULL) {

                        ?>

                            <div class="row" style="margin:auto;width:100%;">

                                <h4>Generate Invoice</h4>

                                <div class="col-md-12" style="padding:0px;">

                                    <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID; ?>">

                                    <input type="hidden" name="GatID" id="GatID" value="<?php echo $details->id; ?>">

                                    <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID; ?>">

                                    <input type="text" name="KYC" id="KYC" value="<?php echo $details->KYCStatus; ?>" hidden>

                                    <input type="text" name="NetWeightCheck" id="NetWeightCheck" value="<?php echo $NetWeight_MT; ?>" hidden>

                                    <input type="text" name="StockWeightCheck" id="StockWeightCheck" value="<?php echo $StockWtCheckForGateOut; ?>" hidden>

                                    <input type="text" name="WeightShortInKg" id="WeightShortInKg" value="<?php echo $WeightShortInKg; ?>" hidden>

                                    <input type="text" name="SalesID" id="SalesID" value="<?php echo $details->SalesID; ?>" hidden>

                                    <input type="text" name="VehicleID" id="VehicleID" value="<?php echo $details->VehicleNo; ?>" hidden>

                                    <input type="text" name="DriverID" id="DriverID" value="<?php echo $details->Phone; ?>" hidden>

                                    <input type="text" name="ChallanAmt" id="ChallanAmt" value="<?php echo $details->BillAmt; ?>" hidden>

                                    <button class="GenerateChallan btn btn-info" id="GenerateChallan" <?php echo $UnlockChallan; ?> type="button">Generate Invoice</button>

                                </div>

                            </div>

                        <?php } ?>

                        <?php

                        if ($details->ChallanID != NULL) {

                            $GateInButtonCheck = "";

                        ?>

                            <div class="row" style="margin-top:10px;">

                                <div class="col-md-12">

                                    <h4><a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewSellPayment/<?php echo $details->BookingID . '/' . $details->Gate_in_ID; ?>"> View Invoice</a></h4>

                                </div>

                            </div>

                        <?php } ?>

                        <?php

                        if ($details->gate_out_date  == NULL) {

                        ?>

                            <div class="row" style="margin:auto;width:100%;">

                                <h4>Gate Out Pass</h4>



                                <div class="col-md-12" style="padding:0px;">

                                    <input type="hidden" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID; ?>">

                                    <input type="hidden" name="GatID" id="GatID" value="<?php echo $details->id; ?>">

                                    <input type="hidden" name="BookingID" id="BookingID" value="<?php echo $details->BookingID; ?>">

                                    <input type="text" name="KYC" id="KYC" value="<?php echo $details->KYCStatus; ?>" hidden>

                                    <input type="text" name="NetWeightCheck" id="NetWeightCheck" value="<?php echo $NetWeight_MT; ?>" hidden>

                                    <input type="text" name="StockWeightCheck" id="StockWeightCheck" value="<?php echo $StockWtCheckForGateOut; ?>" hidden>



                                    <button class="GenerateGateOut btn btn-info" id="GenerateGateOut" <?php echo $UnlockChallan; ?> type="button">Generate Gate Out</button>

                                </div>

                            </div><!-- Gate Out Row End-->

                        <?php } ?>

                        <?php

                        $MarkExitButtonCheck = "disabled";

                        if ($details->gate_out_date != NULL) {

                            $MarkExitButtonCheck = "";

                        ?>

                            <div class="row" style="margin:auto;width:100%;">

                                <h4>Gate Out Pass &nbsp;&nbsp;&nbsp;&nbsp;<a style="font-size:14px;" target="_blank" href="<?php echo admin_url(); ?>GateControl/viewGateOut/<?php echo $details->BookingID . '/' . $details->Gate_in_ID; ?>" target="_blank">View Gate Out Pass</a></h4>

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

                                                <td><?php echo ($SName['gate_out_by']->firstname . ' ' . $SName['gate_out_by']->lastname); ?></td>

                                                <td><?php echo _d($details->gate_out_date); ?></td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        <?php }

                        if ($details->exit_date == NULL) {

                        ?>

                            <div class="row" style="margin:auto;width:100%;margin-top:2%">

                                <h4>Mark Vehicle Exit</h4>

                                <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExit">

                                    <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>

                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>

                                    <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>

                                    <button type="submit" class="btn btn-info exitBtn" <?php echo $MarkExitButtonCheck; ?> style="margin-right: 25px;">Mark Exit</button>

                                </form>

                            </div>

                        <?php

                        }

                        ?>





                        <?php

                        if ($details->exit_date != NULL) {

                        ?>

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

                                                <td><?php echo ($SName['exit_by']->firstname . ' ' . $SName['exit_by']->lastname); ?></td>

                                                <td><?php echo _d($details->exit_date); ?></td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>



                            </div>

                        <?php

                        }

                        ?>

                        <?php

                        if ($status >= 10) {

                        ?>

                            <div class="row" style="margin-top:10px;">

                                <div class="col-md-12">

                                    <?php

                                    if ($SendInwardToPcSoftCheck->pcsoft_doc_ref) {
                                    } else {

                                    ?>

                                        <form id="sendpcsoft_form" method="POST" action="<?php echo admin_url(); ?>GateControl/SendDataToPcSoft">

                                            <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>

                                            <input type="text" id="GateINID" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>

                                            <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>

                                            <button type="button" class="btn btn-info sendpcsoft" style="margin-right: 25px;">Send Data To PcSoft</button>

                                        </form>

                                    <?php

                                    }

                                    ?>



                                </div>

                            </div>



                        <?php

                        }

                        ?>





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





    </div><!-- End Content div-->

</div><!-- End Wrapper div-->



<?php init_tail(); ?>

<script>
    $(document).ready(function() {

        var WHID = $("#WHID").val();

        GetChamberList(WHID);

    });



    // Cancel Outward   

    $('#RejectOutward').click(function() {

        var GateINID = $('#GateINID').val();

        var rejection_reason = $('#GateINID').val();

        var id = $('#id').val();

        if (rejection_reason == "") {

            alert("Please Provide Rejection Reason.");

        } else {

            if (confirm("Do you want to Reject Inward?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/RejecteInward",

                    dataType: "json",

                    method: "POST",

                    data: {
                        GateINID: GateINID,
                        id: id,
                        rejection_reason: rejection_reason
                    },

                    beforeSend: function() {

                        $('#sendrequest').html('Please wait request sending.');

                    },

                    success: function(r) {

                        if (r == true) {

                            $('#modifyModal').modal('hide');

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + id);

                        } else {

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + id);

                        }

                    }

                });

            }

        }

    });

    function GetChamberList(WHID) {

        $.ajax({

            url: "<?php echo admin_url(); ?>GateControl/GetChamberList",

            method: "POST",

            dataType: "JSON",

            data: {

                WHID: WHID

            },

            success: function(fin) {

                var options = "<option value=''>Non Selected</option>";

                $.each(fin, function(index, value) {

                    options += "<option value='" + value.CHID + "'>" + value.ChaumberName + "</option>";

                });

                chamber_hidden = $('#chamber_hidden').val();

                $('select[name=chamber]').html(options);

                $('.selectpicker').selectpicker('refresh');

                $('select[name=chamber]').val(chamber_hidden);

                $('.selectpicker').selectpicker('refresh');

                if (chamber_hidden != "") {

                    GetStackList(chamber_hidden);

                }

            }

        });

    }



    function GetStackList(CHID) {

        $.ajax({

            url: "<?php echo admin_url(); ?>GateControl/GetWarehouseStackList",

            method: "POST",

            dataType: "JSON",

            data: {

                CHID: CHID

            },

            success: function(fin) {

                var options = "<option value=''>Non Selected</option>";

                $.each(fin, function(index, value) {

                    options += "<option value='" + value.StackID + "'>" + value.StackName + "</option>";

                });

                Stack_hidden = $('#Stack_hidden').val();

                $('select[name=Stack]').html(options);

                $('.selectpicker').selectpicker('refresh');

                $('select[name=Stack]').val(Stack_hidden);

                $('.selectpicker').selectpicker('refresh');

                if (Stack_hidden != "") {

                    GetLotList(Stack_hidden);

                }

            }

        });

    }



    $('#chamber').change(function() {

        var Value = $(this).val();

        GetStackList(Value);

    });

    function GetLotList(StackID) {

        $.ajax({

            url: "<?php echo admin_url(); ?>GateControl/GetStackLotList",

            method: "POST",

            dataType: "JSON",

            data: {

                StackID: StackID

            },

            success: function(fin) {

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



    $('#Stack').change(function() {

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

            if (parseFloat(val) > 0) {

                TotalStackWeight = parseFloat(TotalStackWeight) + parseFloat(val);

            }

        })

        $('#TotalStackWeight').val(parseFloat(TotalStackWeight).toFixed(3));

    }

    function addrow()

    {

        var TotalStackWeight = $('#TotalStackWeight').val();

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var NetWeight = parseFloat(TareWeight).toFixed(3) - parseFloat(GrossWeight).toFixed(3);



        if (parseFloat(GrossWeight).toFixed(3) <= 0) {

            alert('Please Enter Gross Weight');

        } else if (parseFloat(TareWeight).toFixed(3) <= 0) {

            alert('Please Enter Tare Weight');

        } else if (parseFloat(TotalStackWeight).toFixed(3) > parseFloat(NetWeight).toFixed(3)) {

            alert("Total Stack Weight is greter than Net Weight");

        } else {

            var TotalLot = parseInt($('#TotalLot').val());

            TotalLot++;

            var CHID = $('#chamber').val();

            var StackID = $('#Stack').val();

            var LOTID = $('#LOTID').val();

            var lot_weight = $('#lot_weight').val();

            var lot_quantity = $('#lot_quantity').val();

            var html = '';

            html += '<tr class="item">';

            html += '<td>' + CHID + ' <input hidden name="StackList[' + TotalLot + '][Chamber]" value="' + CHID + '"></td>';

            html += '<td>' + StackID + ' <input hidden name="StackList[' + TotalLot + '][Stack]" value="' + StackID + '"></td>';

            html += '<td>' + LOTID + ' <input hidden name="StackList[' + TotalLot + '][Lot]" value="' + LOTID + '"></td>';

            html += '<td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control"  name="StackList[' + TotalLot + '][WeightMT]" value="' + lot_weight + '" onkeypress="return isNumber(this,event)"></td>';

            html += '<td> <input style="height:30px;width:100%;"  class="form-control" name="StackList[' + TotalLot + '][BagQty]" value="' + lot_quantity + '" onkeypress="return isNumber(this,event)"></td>';

            html += '<td> <button class="remove form-control" type="button" style="font-size:20px;color:red;" ><i class="fa fa-trash"></i></button></td>';

            html += '</tr>';

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

    $('#stack_tbody').on('click', '.remove', function() {

        // Removing the current row.

        $(this).closest('tr').remove();

        var TotalLot = parseInt($('#TotalLot').val());

        TotalLot--;

        $('#TotalLot').val(TotalLot);

        calculate_total_weight();

    });

    function addLayer() {

        var no_of_layers = parseInt($('#no_of_layers').val());



        var layer_details = <?php echo json_encode($layers); ?>;

        if (layer_details.length > 0) {

            var layer_parameters = layer_details[0].parameter_detail;

        } else {

            var layer_parameters = <?php echo json_encode($peripheral); ?>;

        }

        no_of_layers += 1;



        var html = '';



        html += '<tr>';

        html += '<td id="layer_number' + no_of_layers + '">' + no_of_layers + '</td>';

        html += '<td><input style="width: 50px;" id="layer_quantity' + no_of_layers + '" value="" onkeypress="return isNumber(this,event)"></td>';

        html += '<td>BAG</td>';

        html += '<td><input hidden id="unloading_by' + no_of_layers + '" value=""></td>';

        html += '<td id="unloading_date' + no_of_layers + '"></td>';



        for (let i = 0; i < layer_parameters.length; i++) {

            html += '<td>';

            html += '<input style="width: 50px;" id="unloadingParameterValue_' + no_of_layers + '_' + layer_parameters[i].ItemParameterID + '" value="">';

            html += '</td>';

        }

        html += '<td><input hidden id="qc_done_by_' + no_of_layers + '" value=""></td>';

        html += '<td id="qc_done_date_' + no_of_layers + '"></td>';

        html += '</tr>';



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



        for (let i = 0; i < no_of_layers; i++) {



            if ($('#layer_quantity' + (i + 1)).val() != '') {

                var inner_item_array = [];

                for (let j = 0; j < inner_item_count; j++) {



                    var item_id = 'unloadingParameterValue_' + (i + 1) + '_' + (j + 1);

                    const parts = item_id.split('_');

                    const item_parameter_id = parts[parts.length - 1];



                    var item_object = {

                        'item_id': item_parameter_id,

                        'item_value': $('#unloadingParameterValue_' + (i + 1) + '_' + (j + 1)).val(),

                        'qc_done_by': $('#qc_done_by_' + (i + 1)).val(),

                        'qc_done_date': $('#qc_done_date_' + (i + 1)).text(),

                    }

                    inner_item_array.push(item_object);

                }

                var inner_object = {

                    'layer_no': $('#layer_number' + (i + 1)).text(),

                    'layer_quantity': $('#layer_quantity' + (i + 1)).val(),

                    'unloading_by': $('#unloading_by' + (i + 1)).val(),

                    'unloading_date': $('#unloading_date' + (i + 1)).text(),

                    'layer_details': inner_item_array

                }

                unloading_array.push(inner_object);

            }

        }

        $.ajax({

            url: "<?php echo admin_url(); ?>GateControl/updateLayerDetails",

            dataType: "json",

            method: "POST",

            data: {
                Booking_ID: Booking_ID,
                Gate_IN_ID: Gate_IN_ID,
                id: id,
                unloading_array: unloading_array,
                ItemID: ItemID
            },

            beforeSend: function() {

                $('#sendrequest').html('Please wait request sending.');

            },

            success: function(r) {

                window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

            }

        });

    }







    function reloadCurrentPage() {

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
    $('.exitBtn').click(function() {

        $('#exit_form').submit();

    });



    $('#ModifyAdvice').click(function() {

        $('#modify_row').css('display', 'block');

        $('#SendButton').css('display', 'none');

    });



    $('#changeQC').click(function() {

        $('#modify_qc').css('display', 'block');

        $('#SendButton').css('display', 'none');

    });



    $('#changeQCHO').click(function() {

        $('#modify_qcHO').css('display', 'block');

        $('#SendButton').css('display', 'none');

    });



    $('#CancelModifyQC').click(function() {

        $('#modify_qcHO').css('display', 'none');

        $('#modify_qc').css('display', 'none');

        $('#SendButton').css('display', 'block');

    });



    $('#CancelModifyUpdate').click(function() {

        $('#modify_row').css('display', 'none');

        $('#SendButton').css('display', 'block');

    });



    $("#payment_perc").keyup(function() {

        var NetAmt = $('#NetAmt').val();

        var val = $(this).val();

        if (val == "") {

            $('#payment_Amt').val('0.00');

            $('#payment_Amt2').val('0.00');

        } else {

            if (val > 100) {

                alert('please enter less than equal to 100%');

                $('#payment_Amt').val('0.00');

                $('#payment_Amt2').val('0.00');

                $(this).val('0');

            } else {

                var per = 100 - parseFloat(val);

                var defualtAmtPay = ((parseFloat(NetAmt)) - (parseFloat(NetAmt) * parseFloat(per)) / 100);

                $('#payment_Amt').val(parseFloat(defualtAmtPay).toFixed(3));

                $('#payment_Amt2').val(parseFloat(defualtAmtPay).toFixed(3));

            }

        }

    })



    $('#ContinueQC').click(function() {

        var GateINID = $('#GateINID').val();

        var BookingID = $('#BookingID').val();

        var GatID = $('#GatID').val();

        if (confirm("Do you want to Continue with Center Office QC?") == true) {

            $.ajax({

                url: "<?php echo admin_url(); ?>GateControl/continue_same_Qc",

                dataType: "json",

                method: "POST",

                data: {
                    GateINID: GateINID,
                    BookingID: BookingID
                },

                beforeSend: function() {

                    $('#sendrequest').html('Please wait request sending.');

                },

                success: function(r) {

                    if (r == true) {

                        $('#modifyModal').modal('hide');

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    } else {

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    }

                }

            });

        }

    });



    $('#ContinueQCHO').click(function() {

        var GateINID = $('#GateINID').val();

        var BookingID = $('#BookingID').val();

        var GatID = $('#GatID').val();

        if (confirm("Do you want to Continue with RO Office QC?") == true) {

            $.ajax({

                url: "<?php echo admin_url(); ?>GateControl/continue_same_ROQc",

                dataType: "json",

                method: "POST",

                data: {
                    GateINID: GateINID,
                    BookingID: BookingID
                },

                beforeSend: function() {

                    $('#sendrequest').html('Please wait request sending.');

                },

                success: function(r) {

                    if (r == true) {

                        $('#modifyModal').modal('hide');

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    } else {

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    }

                }

            });

        }

    });

    $('#ForApproval').click(function() {

        var GateINID = $('#GateINID').val();

        var BookingID = $('#BookingID').val();

        var NetAmt = $('#NetAmt').val();

        var final_rate = $('#final_rate').val();

        var GatID = $('#GatID').val();

        var PaymentAmt = $('#payment_Amt2').val();

        var PaymentPer = $('#payment_perc').val();

        var other_deduction_add = $('#other_deduction_add').val();

        var CustomerType = $('#CustomerType').val();

        if (other_deduction_add == "N") {

            alert('Please add other deduction first');

        } else {

            if (confirm("Do you want to Approve Payment Advice?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/ApprovePaymentAdvice",

                    dataType: "json",

                    method: "POST",

                    data: {
                        GateINID: GateINID,
                        BookingID: BookingID,
                        PaymentAmt: PaymentAmt,
                        PaymentPer: PaymentPer,

                        final_rate: final_rate,
                        CustomerType: CustomerType,
                        NetAmt: NetAmt
                    },

                    beforeSend: function() {

                        $('#sendrequest').html('Please wait request sending.');

                    },

                    success: function(r) {

                        if (r == true) {

                            $('#modifyModal').modal('hide');

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                        } else {

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                        }

                    }

                });

            }

        }

    })







    $('#SendForApproval').click(function() {

        var reasonAmt = $('#reasonAmt').val();

        var modify_reason = $('#reason').val();

        var GateINID = $('#GateINID').val();

        var GatID = $('#GatID').val();

        if ((GatID != '') && (GateINID != '')) {

            if (confirm("Do you want to sent Payment Advice?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/UpdatePaymentAdvice",

                    dataType: "json",

                    method: "POST",

                    data: {
                        reasonAmt: reasonAmt,
                        modify_reason: modify_reason,
                        GateINID: GateINID
                    },

                    beforeSend: function() {

                        $('#sendrequest').html('Please wait request sending.');

                    },

                    success: function(r) {

                        if (r == true) {

                            $('#modifyModal').modal('hide');

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                        } else {

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                        }

                    }

                });

            } else {

                return false;

            }

        }

    })

    $('#ModifyUpdate').click(function() {

        var reasonAmt = $('#reasonAmt').val();

        var modify_reason = $('#reason').val();

        var GateINID = $('#GateINID').val();

        var GatID = $('#GatID').val();

        if (reasonAmt == '') {

            alert('please enter amount');

        } else if (modify_reason == "") {

            alert('please provide reason for modification');

        } else {

            $.ajax({

                url: "<?php echo admin_url(); ?>GateControl/UpdatePaymentAdvice",

                dataType: "json",

                method: "POST",

                data: {
                    reasonAmt: reasonAmt,
                    modify_reason: modify_reason,
                    GateINID: GateINID
                },

                beforeSend: function() {

                    $('#sendrequest').html('Please wait request sending.');

                },

                success: function(r) {

                    if (r == true) {

                        $('#modifyModal').modal('hide');

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    } else {

                        window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + GatID);

                    }

                }

            });

        }

    });
</script>



<script>
    function isNumber(txt, event) {

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

        if (charCode > 31 && (charCode < 48 || charCode > 57)) {

            return false;

        }

        return true;

    }
</script>

<script>
    $(document).ready(function() {

        $('#payment_approve').change(function() {

            var payment = $('#payment_approve :selected').val();

            var id = $('#id').val();



            if ((payment != '') && (id != '')) {

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

    $('#saveBtn').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var Qcslip = $('#fQCSlip').val();

        if (GrossWeight <= 0) {

            alert('please add Gross Weight first');

        } else if (TareWeight <= 0) {

            alert('please add Tare Weight first');

        } else if (Qcslip == '') {

            alert('please QC Slip upload');

        } else {

            $('#final_qc_form').submit();

        }

    });



    // Save RO QC Validation

    $('#ModifyROQC').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var Qcslip = $('#fQCSlip_ro').val();

        if (GrossWeight <= 0) {

            alert('please add Gross Weight first');

        } else if (TareWeight <= 0) {

            alert('please add Tare Weight first');

        } else if (Qcslip == '') {

            alert('please QC Slip upload');

        } else {

            $('#final_qc_form_RO').submit();

        }

    });



    // Save tare Weight Validation

    $('#TareWeightSubmit').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#tare_weight').val();



        if (parseFloat(TareWeight) <= parseFloat(GrossWeight) && parseFloat(GrossWeight) > 0) {

            alert('please enter Tare Weight is less than Gross Weight');

        } else if (parseFloat(TareWeight) <= 0) {

            alert('please enter tare weight is grater than zero');

        } else {

            $('#tare_weight_form').submit();

        }

    });



    // Save Stack Details and validation Validation

    $('#StackSubmit').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#tare_weight').val();

        var NetWeight = TareWeight - GrossWeight;

        var TotalStackWeight = $('#TotalStackWeight').val();

        if (parseFloat(TareWeight) <= parseFloat(GrossWeight)) {

            alert('please enter Tare Weight is less than Gross Weight');

        } else if (parseFloat(TareWeight) <= 0) {

            alert('please enter tare weight is grater than zero');

        } else if (parseFloat(TotalStackWeight).toFixed(3) != parseFloat(NetWeight).toFixed(3)) {

            alert('Please Enter Stack Weight is Equal to Net Weight');

        } else {

            $('#stack_details_form').submit();

        }

    });



    // Save Gross Weight Validation

    $('#GrossWeightSubmit').click(function() {

        var GrossWeight = $('#total_weight').val();

        var TareWeight = $('#tare_weight').val();

        if (parseFloat(TareWeight) >= parseFloat(GrossWeight)) {

            alert('please enter Gross Weight is grater than Tare Weight');

        } else if (parseFloat(GrossWeight) <= 0) {

            alert('please enter gross weight is grater than zero');

        } else {

            $('#gross_weight_form').submit();

        }

    });



    // Generate Challan Validation

    $('#GenerateChallan').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var KYC = $('#KYC').val();

        var BookingID = $('#BookingID').val();

        var GateINID = $('#GateINID').val();

        var BookingType = $('#BookingType').val();

        var StockWeightCheck = $('#StockWeightCheck').val();

        var NetWeightCheck = $('#NetWeightCheck').val();

        var SalesID = $('#SalesID').val();

        var VehicleID = $('#VehicleID').val();

        var DriverID = $('#DriverID').val();

        var ChallanAmt = $('#ChallanAmt').val();

        if (KYC < 6) {

            alert('please complate KYC first');

        } else if (TareWeight <= 0) {

            alert('please enter Tare Weight');

        } else if (GrossWeight <= 0) {

            alert('please enter Gross Weight');

        } else if (StockWeightCheck > NetWeightCheck) {

            alert('Stock details weight is greter than net weight please check and update stock weight');

        } else if (StockWeightCheck < NetWeightCheck) {

            alert('Stock details weight is less than net weight please check and update stock weight');

        } else {

            if (confirm("Do you want to Generate Challan?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/GenerateChallan",

                    dataType: "json",

                    method: "POST",

                    data: {
                        BookingID: BookingID,
                        GateINID: GateINID,
                        StockWeightCheck: StockWeightCheck,
                        SalesID: SalesID,

                        ChallanAmt: ChallanAmt,
                        VehicleID: VehicleID,
                        DriverID: DriverID

                    },

                    beforeSend: function() {

                        $('#sendrequest').html('Please wait request sending.');

                    },

                    success: function(r) {

                        if (r == true) {

                            window.open("<?php echo admin_url(); ?>GateControl/viewSellPayment/" + BookingID + "/" + GateINID, '_blank');

                            window.location.reload();

                        } else {

                            window.location.reload();

                        }

                    }

                });

            } else {

                return false;

            }

        }

    });



    // Gate Out Validation 



    $('#GenerateGateOut').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var KYC = $('#KYC').val();

        var BookingID = $('#BookingID').val();

        var GateINID = $('#GateINID').val();

        var BookingType = $('#BookingType').val();

        var StockWeightCheck = $('#StockWeightCheck').val();

        var NetWeightCheck = $('#NetWeightCheck').val();



        if (KYC < 6) {

            alert('please complate KYC first');

        } else if (TareWeight <= 0) {

            alert('please enter Tare Weight');

        } else if (GrossWeight <= 0) {

            alert('please enter Gross Weight');

        } else if (StockWeightCheck > NetWeightCheck) {

            alert('Stock details weight is greter than net weight please check and update stock weight');

        } else if (StockWeightCheck < NetWeightCheck) {

            alert('Stock details weight is less than net weight please check and update stock weight');

        } else {

            $.ajax({

                url: "<?php echo admin_url(); ?>GateControl/generateGateOut",

                dataType: "json",

                method: "POST",

                data: {
                    BookingID: BookingID,
                    GateINID: GateINID,
                    BookingType: BookingType
                },

                beforeSend: function() {

                    $('#sendrequest').html('Please wait request sending.');

                },

                success: function(r) {

                    if (r == true) {

                        window.open("<?php echo admin_url(); ?>GateControl/viewGateOut/" + BookingID + "/" + GateINID, '_blank');

                        window.location.reload();

                    } else {

                        window.location.reload();

                    }

                }

            });

        }

    });
</script>



</body>

</html>