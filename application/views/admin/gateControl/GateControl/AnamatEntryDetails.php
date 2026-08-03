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

        color: #fff;

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

                                <h4><b>Booking Details</b></h4>

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

                                                if ($details->TType == 'D') {

                                                    if ($details->status == 1) {

                                                        $status_val = "ASN GENERATED";
                                                    } else if ($details->status == 2) {

                                                        $status_val = "GATE IN GENERATED";
                                                    } else if ($details->status == 3) {

                                                        $status_val = "PERIPHERAL DONE";
                                                    } else if ($details->status == 4) {

                                                        $status_val = "GROSS WEIGHT CAPTURED ";
                                                    } else if ($details->status == 9) {

                                                        $status_val = "TARE WEIGHT CAPTURED";
                                                    } else if ($details->status == 10) {

                                                        $status_val = "FINAL QC DONE ";
                                                    } else if ($details->status == 11) {

                                                        $status_val = "GATE OUT GANERATED";
                                                    } else if ($details->status == 12) {

                                                        $status_val = "MARK AS EXIT";
                                                    }
                                                }

                                                ?>

                                                <td><b>Status : </b></td>

                                                <td><?php echo $status_val; ?></td>

                                            </tr>
                                            <tr>
                                                <td><b>Vendor Invoice Date : </b></td>
                                                <td><?= _d($details->vendor_invoice_date ?? ''); ?></td>
                                                <td><b>Vendor Invoice Number : </b></td>
                                                <td><?= $details->vendor_invoice_number ?? ''; ?></td>

                                            </tr>
                                            <tr>
                                                <td><b>Vendor Invoice Doc : </b></td>
                                                <td><?= (!empty($details->vendor_invoice_doc)) ? '<a href="' . base_url() . $details->vendor_invoice_doc . '" target="_blank" title="View Vendor Invoice Doc">Click to View</a>' : ''; ?></td>
                                                <td></td>
                                                <td></td>
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

                            if ($details->LoadedWeight > 0 && $details->TareWeight > 0 && $details->status < 11) {

                                $ChkStackAddUpdate = "";
                            } else {

                                $ChkStackAddUpdate = "disabled";
                            }



                            // Lock Tare Weight,gross weight and bag weight after add stack details

                            if ($StackList && $details->status < 10) {

                                $LockWeight = 'disabled'; // Gross Weight and Tare Weight not Edit after add QC and Stack details

                            } else {

                                $LockWeight = ""; // Gross Weight and Tare Weight Edit before add QC and Stack details

                            }

                            ?>

                            <form id="AddFieldOfficer" method="POST" action="<?php echo admin_url(); ?>GateControl/AddFieldOfficer">

                                <div class="col-md-6">

                                    <div class="row">

                                        <div class="col-md-8">

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

                                        <div class="col-md-4" style="margin-top: 20px;">

                                            <button type="submit" class="btn btn-info btn-sm">Add Field Officer</button>

                                        </div>

                                    </div>

                                </div>

                            </form>



                            <!-- Update Godown details-->

                            <form id="UpdateGodown" method="POST" action="<?php echo admin_url(); ?>GateControl/UpdateGodown">

                                <div class="col-md-6">

                                    <div class="row">

                                        <div class="col-md-6">

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

                                        <div class="col-md-4" style="margin-top: 20px;">

                                            <?php

                                            if (empty($StackList)) {

                                                $GodownDisabled = '';
                                            } else {

                                                $GodownDisabled = 'disabled';
                                            }

                                            ?>

                                            <button type="submit" class="btn btn-success btn-sm" <?php echo $GodownDisabled; ?>>Update Godown</button>

                                        </div>

                                        <!---->

                                    </div>

                                </div>

                            </form>

                            <div class="col-md-6">

                                <div class="row">

                                    <div class="col-md-8">

                                        <small class="req text-danger">* </small>

                                        <label for="reason" class="form-label">Rejection Reason</label>

                                        <textarea name="rejection_reason" <?php echo $RejectButton; ?> id="rejection_reason" class="form-control"><?php echo $details->rejection_reason; ?></textarea>

                                    </div>

                                    <div class="col-md-4" style="margin-top: 20px;">

                                        <?php

                                        if ($details->TareWeight > 0 || $details->status == 18) {

                                            $RejectButton = "disabled";
                                        } else {

                                            $RejectButton = "";
                                        }

                                        ?>

                                        <button type="button" class="btn btn-danger btn-sm" id="RejectInward" <?php echo $RejectButton; ?>>Reject Inward</button>

                                    </div>

                                </div>

                            </div>

                            <form id="AddFieldOfficer" method="POST" action="<?php echo admin_url(); ?>GateControl/AddArrivalDateTime">

                                <div class="col-md-6">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <?php

                                            $DateTime = _d(substr($details->VchlArrivalDateTime, 0, 16));

                                            //$DateTime = date("d/m/Y H:i");

                                            ?>

                                            <input type="text" name="GateINID" id="GateINID" value="<?php echo $details->Gate_in_ID ?>" hidden>

                                            <input type="text" name="id" id="id" value="<?php echo $details->id ?>" hidden>

                                            <div class="form-group" app-field-wrapper="ArrivalDateTime">

                                                <small class="req text-danger">* </small>

                                                <label for="ArrivalDateTime" class="control-label">Arrival Date Time</label>

                                                <div class="input-group date"><input type="text" id="ArrivalDateTime" name="ArrivalDateTime" class="form-control datetimepicker" value="<?php echo $DateTime; ?>" autocomplete="off">

                                                    <div class="input-group-addon">

                                                        <i class="fa-regular fa-calendar calendar-icon"></i>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-md-4" style="margin-top: 20px;">

                                            <button type="submit" class="btn btn-info btn-sm">Vehicle Arrival Date Time</button>

                                        </div>

                                    </div>

                                </div>

                            </form>



                        </div>



                        <div class="row">

                            <div class="col-md-3">

                                <label for="invamt" class="form-label">Invoice Amt</label>

                                <input type="text" name="invamt" id="invamt" class="form-control" value="<?php echo htmlspecialchars($details->InvoiceAmt); ?>" readonly />

                            </div>



                            <div class="col-md-4">

                                <small class="req text-danger">* </small>

                                <label for="rateval" class="form-label">Rate(MT)</label>

                                <input type="text" name="rateval" id="rateval" class="form-control" value="<?php echo htmlspecialchars($details->basic_rate * 10); ?>" />

                            </div>

                            <div class="col-md-3" style="margin-top: 20px;">

                                <button type="button" class="btn btn-success btn-sm" id="UpdateRate">Add Rate</button>

                            </div>

                        </div>



                        <div class="row">

                            <div class="col-md-12">

                                <h4>Peripheral QC Details</h4>

                                <form id="peripheral_qc_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updatePeripheralQC">

                                    <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">

                                        <thead>

                                            <tr>

                                                <?php

                                                $number_of_para = 0;

                                                foreach ($peripheral as $key => $value) {

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

                                                $UserID = $peripheral[0]["firstname"] . ' ' . $peripheral[0]["lastname"];

                                                $count = 1;

                                                ?>

                                                <?php foreach ($peripheral as $key => $value) { ?>

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

                                                <td><?php echo $UserID; ?></td>

                                                <td><?php echo _d($TransDate); ?></td>

                                                <td><button class="updateCheck" <?php echo $disableCheck; ?> type="submit"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </form>

                            </div>

                        </div>



                        <div class="row">

                            <div class="col-md-12">

                                <h4>Gross Weight Details</h4>

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

                        </div> <!-- Gross Weight Row End-->



                        <div class="row">

                            <div class="col-md-12">

                                <h4>Tare Weight Details</h4>

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

                        </div> <!-- Tare Weight Row End-->





                        <div class="row">

                            <form id="stack_details_form" method="POST" action="<?php echo admin_url(); ?>GateControl/updateStackDetails">

                                <div class="col-md-12">

                                    <h4>Center QC & Stack Details </h4>

                                    <input type="text" name="GateINDate" value="<?php echo $details->gate_in_date; ?>" hidden>

                                    <input type="text" name="basic_rate" value="<?php echo $details->basic_rate; ?>" hidden>

                                    <input type="text" name="ItemID" value="<?php echo $details->ItemID; ?>" hidden>

                                    <input type="text" name="id" value="<?php echo $details->id; ?>" hidden>

                                    <input type="text" name="TransID" value="<?php echo $TransID; ?>" hidden>

                                    <input type="text" name="PartyID" value="<?php echo $details->PartyID; ?>" hidden>

                                    <input type="text" name="WHID" value="<?php echo $details->GodownID; ?>" hidden>

                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID; ?>" hidden>

                                    <input type="text" name="AccountID" value="<?php echo $details->AccountID; ?>" hidden>

                                    <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType; ?>" hidden>

                                    <input type="text" name="GateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>

                                    <input type="text" name="GateINDate" value="<?php echo $details->gate_in_date; ?>" hidden>

                                    <input type="text" name="GrossWeight" id="GrossWeight" value="<?php echo number_format(($details->LoadedWeight / 10), 3, '.', ''); ?>" hidden>

                                    <input type="text" name="TareWeight" id="TareWeight" value="<?php echo number_format(($details->TareWeight / 10), 3, '.', ''); ?>" hidden>

                                    <input type="text" name="QC_for" id="QC_for" value="Center" hidden>



                                    <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">

                                        <thead>

                                            <tr>

                                                <?php foreach ($peripheral as $key => $value) {

                                                ?>

                                                    <th width="15%"><?php echo $value['ItemParameterName']; ?></th>

                                                <?php } ?>

                                                <th width="5%">QC Approval</th>

                                                <th width="15%">Chamber</th>

                                                <th width="15%">Stack</th>

                                                <th width="15%">Lot</th>

                                                <th width="10%">Weight(MT)</th>

                                                <th width="10%">Bag Qty</th>

                                                <th width="10%">QC Status</th>

                                                <th width="5%">Action</th>

                                            </tr>

                                        </thead>

                                        <tbody id="stack_tbody">

                                            <tr class="item">

                                                <?php foreach ($peripheral as $key => $value) {

                                                ?>

                                                    <td><input style="width:100%;" type="text" name="<?php echo $value['ItemParameterID']; ?>" id="<?php echo $value['ItemParameterID']; ?>" value="" class="form-control" onkeypress="return isNumber(this,event)"></td>

                                                <?php } ?>

                                                <td>

                                                    <div class="form-group" app-field-wrapper="QcApproval">

                                                        <select name="QcApproval" id="QcApproval" class="selectpicker form-control" data-live-search="true">

                                                            <option value="Y">Auto Approval</option>

                                                            <option value="N">Party Approval</option>

                                                        </select>

                                                    </div>

                                                </td>

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

                                                    --

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

                                                if ($val['TType'] == "A") {

                                                    $i++;

                                                    $StackWeight += $val['Weight'];

                                                    $StockWtCheckForGateOut += $val['Weight'];

                                                    $QCDetails = $val['QcDetails'];

                                                    $QCStatus = $val["CenterQCApprove"];

                                                    if ($val["CenterQCApprove"] == "NA") {

                                                        $statusName = "Approval Pending";
                                                    } else if ($val["CenterQCApprove"] == "Y") {

                                                        $statusName = "Approved";
                                                    } else if ($val["CenterQCApprove"] == "N") {

                                                        $statusName = "Rejected";
                                                    } else {

                                                        $statusName = "Approval Pending";
                                                    }

                                            ?>

                                                    <tr class="item">

                                                        <?php

                                                        $QCParaCount = 0;

                                                        foreach ($peripheral as $key => $value) {

                                                            $paraValue = "";

                                                            $QCParaCount++;

                                                            foreach ($QCDetails as $key => $value1) {

                                                                if ($value1['ItemParameterID'] == $value['ItemParameterID']) {

                                                                    $paraValue = $value1['ParameterValue'];
                                                                }
                                                            }

                                                        ?>

                                                            <td><input style="width:100%;" type="text" name="StackList[<?php echo $i; ?>][<?php echo $value['ItemParameterID']; ?>]" id="<?php echo $value['ItemParameterID']; ?>" value="<?php echo number_format($paraValue, 2, '.', ''); ?>" class="form-control" onkeypress="return isNumber(this,event)"></td>

                                                        <?php } ?>



                                                        <td>

                                                            <div class="form-group" app-field-wrapper="QcApproval">

                                                                <select name="StackList[<?php echo $i; ?>][QcApproval]" class="selectpicker form-control" data-live-search="true">

                                                                    <option value="Y">Auto Approval</option>

                                                                    <option value="N">Trader/Farmer Approval</option>

                                                                </select>

                                                            </div>

                                                        </td>

                                                        <td style="text-align:center;"><?php echo $val["CHID"]; ?> <input hidden name="StackList[<?php echo $i; ?>][Chamber]" value="<?php echo $val["CHID"]; ?>"></td>

                                                        <td style="text-align:center;"><?php echo $val["StackID"]; ?> <input hidden name="StackList[<?php echo $i; ?>][Stack]" value="<?php echo $val["StackID"]; ?>"></td>

                                                        <td style="text-align:center;"><?php echo $val["LOTID"]; ?><input hidden name="StackList[<?php echo $i; ?>][Lot]" value="<?php echo $val["LOTID"]; ?>"></td>

                                                        <td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control" name="StackList[<?php echo $i; ?>][WeightMT]" value="<?php echo $val["Weight"]; ?>" onkeypress="return isNumber(this,event)"></td>

                                                        <td> <input style="height:30px;width:100%;" class="form-control" name="StackList[<?php echo $i; ?>][BagQty]" value="<?php echo $val["BagQty"]; ?>" onkeypress="return isNumber(this,event)"></td>

                                                        <td style="text-align:center;"><?php echo $statusName; ?> <input hidden name="StackList[<?php echo $i; ?>][QCStatus]" value="<?php echo $QCStatus; ?>"></td>

                                                        <td> <button class="remove form-control" type="button" style="font-size:20px;color:red;"><i class="fa fa-trash"></i></button></td>



                                                    </tr>

                                            <?php

                                                }
                                            }

                                            ?>

                                        </tbody>

                                    </table>

                                </div>

                                <div class="col-md-3">

                                    <input type="hidden" class="form-control" id="TotalStackWeight" name="TotalStackWeight" value="<?php echo $StackWeight; ?>">

                                    <input type="hidden" class="form-control" id="TotalLot" name="TotalLot" value="<?php echo $i; ?>">

                                    <div class="form-group">

                                        <button class=" btn btn-success btn-sm" style="margin-top: 10px;" <?php echo $ChkStackAddUpdate; ?> type="button" id="StackSubmit">Update Stack Details</button>



                                    </div>

                                </div>

                            </form>

                        </div>





                        <?php

                        $PurchaseWeight = $details->Asn_WT_MT;

                        if ($details->LoadedWeight != NULL && $details->TareWeight != NULL) {

                        ?>

                            <div class="row">

                                <div class="col-md-5">

                                    <h4><b>Weight Details</b></h4>



                                    <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">

                                        <thead>

                                            <tr>

                                                <th>Parameter</th>

                                                <th>Amount</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <tr>

                                                <td style="font-size:13px"><b>ASN Weight(MT)</b></td>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($PurchaseWeight, 3, '.', ''); ?></td>

                                            </tr>

                                            <tr>

                                                <td style="font-size:13px"><b>Actual Weight (MT)</b></td>

                                                <?php $InwardWt = ($details->LoadedWeight - $details->TareWeight) / 10; ?>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($InwardWt, 3, '.', ''); ?></td>

                                            </tr>

                                            <tr>

                                                <?php

                                                $ActualInwardWeightMT = $InwardWt;

                                                ?>

                                                <td style="font-size:13px"><b>Actual Inward Weight (MT)</b></td>

                                                <td style="font-size:13px;text-align:right;"><?php echo number_format($ActualInwardWeightMT, 3, '.', ''); ?></td>

                                            </tr>



                                        </tbody>

                                    </table>



                                </div>

                            </div>

                        <?php

                        }

                        ?>

                        <?php

                        if ($status < 11) { ?>

                            <div class="row" style="margin:auto;width:100%;">

                                <h4>Gate Out Pass</h4>

                                <div class="col-md-12" style="padding:0px;">

                                    <input type="text" name="KYC" id="KYC" value="<?php echo $details->KYCStatus; ?>" hidden>

                                    <input type="text" name="NetWeightCheck" id="NetWeightCheck" value="<?php echo $ActualInwardWeightMT; ?>" hidden>

                                    <input type="text" name="StockWeightCheck" id="StockWeightCheck" value="<?php echo $StockWtCheckForGateOut; ?>" hidden>



                                    <button class="GenerateGateOut btn btn-info" id="GenerateGateOut" type="button">Generate Gate Out</button>

                                </div>

                            </div><!-- Gate Out Row End-->



                        <?php } ?>



                        <?php

                        if ($status >= 11) { ?>

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

                        <?php } ?>



                        <?php

                        if ($status == 11) { ?>

                            <div class="row" style="margin:auto;width:100%;margin-top:2%">

                                <h4>Mark Vehicle Exit</h4>

                                <form id="exit_form" method="POST" action="<?php echo admin_url(); ?>GateControl/markExit">

                                    <input type="text" id="id" name="id" value="<?php echo $details->id; ?>" hidden>

                                    <input type="text" name="BookingID" value="<?php echo $details->BookingID ?>" hidden>

                                    <input type="text" name="BookingType" id="BookingType" value="<?php echo $details->TType ?>" hidden>

                                    <button type="button" class="btn btn-info exitBtn" style="margin-right: 25px;">Mark Exit</button>

                                </form>

                            </div>

                        <?php } ?>



                        <?php

                        if ($status >= 12) { ?>

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

                        <?php } ?>



                        <div class="row">

                            <?php

                            if ($status > 3) { ?>

                                <div class="col-md-3">

                                    <input type="text" id="Rid" name="Rid" value="<?php echo $details->id; ?>" hidden>

                                    <input type="text" id="RGateINID" name="RGateINID" value="<?php echo $details->Gate_in_ID; ?>" hidden>

                                    <input type="text" name="RBookingID" id="RBookingID" value="<?php echo $details->BookingID ?>" hidden>

                                    <button type="button" class="btn btn-info" id="MoveToGrossWeight" style="margin-right: 25px;">Move To Gross Weight</button>

                                </div>

                            <?php } ?>

                        </div>







                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<?php init_tail(); ?>

<script>
    $(document).ready(function() {

        var WHID = $("#WHID").val();

        GetChamberList(WHID);

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



    function calculate_total_weight() {

        p = $("#table-purchase_request tbody tr.item");

        var TotalStackWeight = 0;

        p.each(function() {

            var val = $(this).find("[data-quantity]").val();

            //alert(val);

            if (parseFloat(val) > 0) {

                TotalStackWeight = parseFloat(TotalStackWeight) + parseFloat(val);

            }



            //alert(val);

        })

        $('#TotalStackWeight').val(parseFloat(TotalStackWeight).toFixed(3));

    }

    function addrow()

    {

        var QcParameterList = <?php echo json_encode($peripheral); ?>;

        var QcApproval = $("#QcApproval").val();

        var TotalStackWeight = $('#TotalStackWeight').val();

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        var CHID = $('#chamber').val();

        var StackID = $('#Stack').val();

        var LOTID = $('#LOTID').val();

        var lot_weight = $('#lot_weight').val();

        var lot_quantity = $('#lot_quantity').val();

        var NetWeight = parseFloat(GrossWeight).toFixed(3) - parseFloat(TareWeight).toFixed(3);

        if (parseFloat(GrossWeight).toFixed(3) <= 0) {

            alert('Please Enter Gross Weight');

        } else if (parseFloat(TareWeight).toFixed(3) <= 0) {

            alert('Please Enter Tare Weight');

        } else if (parseFloat(TotalStackWeight) > parseFloat(NetWeight)) {

            alert("Total Stack Weight is greter than Net Weight");

        } else if (CHID == "") {

            alert("Please Select Chamber");

        } else if (StackID == "") {

            alert("Please Select Stack");

        } else if (LOTID == "") {

            alert("Please Select LOT");

        } else if (lot_weight == "") {

            alert("Please Enter Weight in MT");

        } else if (lot_quantity == "") {

            alert("Please Enter Bag Quantity");

        } else {

            var TotalLot = parseInt($('#TotalLot').val());

            TotalLot++;



            var html = '';

            html += '<tr class="item">';

            for (let i = 0; i < QcParameterList.length; i++) {

                var value = $('#' + QcParameterList[i].ItemParameterID).val();

                html += '<td>';

                html += '<input style="width: 100%;height:30px" class="form-control" name="StackList[' + TotalLot + '][' + QcParameterList[i].ItemParameterID + ']" id="StackList[' + TotalLot + '][' + QcParameterList[i].ItemParameterID + ']" value="' + value + '">';

                html += '</td>';

                $('#' + QcParameterList[i].ItemParameterID).val('');

            }

            html += '<td>' + QcApproval + ' <input hidden name="StackList[' + TotalLot + '][QcApproval]" value="' + QcApproval + '"></td>';

            html += '<td>' + CHID + ' <input hidden name="StackList[' + TotalLot + '][Chamber]" value="' + CHID + '"></td>';

            html += '<td>' + StackID + ' <input hidden name="StackList[' + TotalLot + '][Stack]" value="' + StackID + '"></td>';

            html += '<td>' + LOTID + ' <input hidden name="StackList[' + TotalLot + '][Lot]" value="' + LOTID + '"></td>';

            html += '<td> <input style="height:30px;width:100%;" onchange="calculate_total_weight();" data-quantity class="form-control"  name="StackList[' + TotalLot + '][WeightMT]" value="' + lot_weight + '" onkeypress="return isNumber(this,event)"></td>';

            html += '<td> <input style="height:30px;width:100%;"  class="form-control" name="StackList[' + TotalLot + '][BagQty]" value="' + lot_quantity + '" onkeypress="return isNumber(this,event)"></td>';

            html += '<td>-</td>';

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
</script>



<script>
    document.getElementById('rateval').addEventListener('input', function(e) {

        this.value = this.value.replace(/[^0-9.]/g, '');

        const parts = this.value.split('.');

        if (parts.length > 2) {

            this.value = parts[0] + '.' + parts[1];

        }

    });
</script>



<script>
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



    // Save tare Weight Validation

    $('#TareWeightSubmit').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#tare_weight').val();



        if (parseFloat(TareWeight) >= parseFloat(GrossWeight)) {

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

        var NetWeight = GrossWeight - TareWeight;

        var TotalStackWeight = $('#TotalStackWeight').val();

        //alert(parseFloat(TotalStackWeight).toFixed(3));

        if (parseFloat(TareWeight) >= parseFloat(GrossWeight)) {

            alert('please enter Tare Weight is less than Gross Weight');

        } else if (parseFloat(TareWeight) <= 0) {

            alert('please enter tare weight is grater than zero');

        } else if (parseFloat(TotalStackWeight).toFixed(3) < parseFloat(NetWeight).toFixed(3)) {

            alert('Please Enter Stack Weight is Equal to Net Weight');

        } else if (parseFloat(TotalStackWeight).toFixed(3) > parseFloat(NetWeight).toFixed(3)) {

            alert('Please Enter Stack Weight is Equal to Net Weight');

        } else {

            $('#stack_details_form').submit();

        }

    });



    //==================== Gate Out Validation Validation ==========================

    $('#GenerateGateOut').click(function() {

        var GrossWeight = $('#GrossWeight').val();

        var TareWeight = $('#TareWeight').val();

        //var KYC = $('#KYC').val();

        var BookingID = $('#BookingID').val();

        var GateINID = $('#GateINID').val();

        var BookingType = $('#BookingType').val();

        var StockWeightCheck = $('#StockWeightCheck').val();

        var NetWeightCheck = $('#NetWeightCheck').val();

        /*if(KYC < 6){

            alert('please complate KYC first');

        }else */
        if (TareWeight <= 0) {

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



    $('.exitBtn').click(function() {

        $('#exit_form').submit();

    });



    //====================== Cancel Inward ========================================= 

    $('#RejectInward').click(function() {

        var GateINID = $('#GateINID').val();

        var rejection_reason = $('#rejection_reason').val();

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



    //====================== Update Rate ========================================= 

    $('#UpdateRate').click(function() {

        var GateINID = $('#GateINID').val();

        var id = $('#id').val();

        var Rate = $('#rateval').val();



        if (Rate == "") {

            alert("Please Enter Rate.");

        } else

        {

            if (confirm("Do you want to Edit Rate?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/EditGateControlRate",

                    dataType: "json",

                    method: "POST",

                    data: {
                        GateINID: GateINID,
                        id: id,
                        Rate: Rate
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



    //================= Inward Move to Enter Gross Weight ==========================  

    $('#MoveToGrossWeight').click(function() {

        var RGateINID = $('#RGateINID').val();

        var RBookingID = $('#RBookingID').val();

        var Rid = $('#Rid').val();

        if (RGateINID == "" || RBookingID == "" || Rid == "") {

            alert("Please reload page some data is not fetched.");

        } else {

            if (confirm("Do you want to Move Inward stage to Gross Weight Add?") == true) {

                $.ajax({

                    url: "<?php echo admin_url(); ?>GateControl/MoveInwardToGrossWeight",

                    dataType: "json",

                    method: "POST",

                    data: {
                        RGateINID: RGateINID,
                        Rid: Rid,
                        RBookingID: RBookingID
                    },

                    beforeSend: function() {

                        $('#sendrequest').html('Please wait request sending.');

                    },

                    success: function(r) {

                        if (r == true) {

                            $('#modifyModal').modal('hide');

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + Rid);

                        } else {

                            window.location.reload("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/" + Rid);

                        }

                    }

                });

            }

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