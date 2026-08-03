<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #AccountID {
        text-transform: uppercase;
    }

    #table_warehouse_List td:hover {
        cursor: pointer;
    }

    #table_warehouse_List tr:hover {
        background-color: #ccc;
    }


    table {
        border-collapse: collapse;
        width: 100%;
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
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-10">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <br>

                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <?php
                                    if ($GateINDetails->company == "" || $GateINDetails->company == null) {
                                        $partyName = $GateINDetails->firstname . ' ' . $GateINDetails->lastname;
                                    } else {
                                        $partyName = $GateINDetails->company;
                                    }
                                    ?>
                                    <div class="form-group" app-field-wrapper="AccountID">
                                        <small class="req text-danger">* </small>
                                        <label for="AccountID" class="control-label">Party Name</label>
                                        <input type="text" id="AccountID" readonly name="AccountID" class="form-control"
                                            value="<?php echo $partyName; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="BookingID">
                                        <small class="req text-danger">* </small>
                                        <label for="BookingID" class="control-label">Booking ID</label>
                                        <input type="text" id="BookingID" name="BookingID" readonly class="form-control"
                                            value="<?php echo $GateINDetails->BookingID; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="GateINID">
                                        <small class="req text-danger">* </small>
                                        <label for="GateINID" class="control-label">Gate-in ID</label>
                                        <input type="text" id="GateINID" name="GateINID" readonly class="form-control"
                                            value="<?php echo $GateINDetails->Gate_in_ID; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="clearfix"></div>
                                <?php
                                $NetWeight = $GateINDetails->LoadedWeight - $GateINDetails->TareWeight;
                                $Amt = $NetWeight * $GateINDetails->final_rate;

                                $BookingAmt = $NetWeight * $GateINDetails->basic_rate;
                                $DebitAmt = $BookingAmt - $Amt;
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="booking_rate">
                                        <small class="req text-danger">* </small>
                                        <label for="booking_rate" class="control-label">Booking Rate</label>
                                        <input type="text" id="booking_rate" name="booking_rate" readonly
                                            class="form-control" value="<?php echo $GateINDetails->basic_rate; ?>">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="booking_rate">
                                        <small class="req text-danger">* </small>
                                        <label for="booking_rate" class="control-label">Final Rate</label>
                                        <input type="text" id="booking_rate" name="booking_rate" readonly
                                            class="form-control" value="<?php echo $GateINDetails->final_rate; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="net_weight">
                                        <small class="req text-danger">* </small>
                                        <label for="net_weight" class="control-label">Net Weight(Qtls)</label>
                                        <input type="text" id="net_weight" name="net_weight" readonly
                                            class="form-control"
                                            value="<?php echo number_format($NetWeight, 2, '.', ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <?php
                            
                            $TotalDeduction = 0;
                            foreach($QCparameter as $Qkey=>$Qval){
            				    foreach($QCList as $QVKey=>$QVVal){
            				        if($QVVal["ItemParameterID"]==$Qval["ItemParameterID"]){
            				            $TotalDeduction += $QVVal["deductionAmt"];
            				        }
            				    }
            				}
                            if($GateINDetails->CustomerType == '1'){
                                $taxableAmount = $historyDetails->OrderAmt;
                                $DBGstAmt = 0;
                				$netDeduction = 0;
                            }else{
                                $taxableAmount = $historyDetails->OrderAmt - $TotalDeduction;
                                $DBGstAmt = ($TotalDeduction * $GateINDetails->taxrate ) / 100;
                				$netDeduction = $DBGstAmt + $TotalDeduction;
                            }
                            ?>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="tax_amountdbAmt">
                                        <small class="req text-danger">* </small>
                                        <label for="tax_amountdbAmt" class="control-label">Debit Note Amt</label>
                                        <input type="text" id="tax_amountdbAmt" name="tax_amountdbAmt" readonly
                                            class="form-control" value="<?php echo number_format($TotalDeduction, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="tax_amountdbNoteGST">
                                        <small class="req text-danger">* </small>
                                        <label for="tax_amountdbNoteGST" class="control-label">Debit Note GST</label>
                                        <input type="text" id="tax_amountdbNoteGST" name="tax_amountdbNoteGST" readonly
                                            class="form-control" value="<?php echo number_format($DBGstAmt, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                 <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="tax_amountdbNetAmt">
                                        <small class="req text-danger">* </small>
                                        <label for="tax_amountdbNetAmt" class="control-label">Debit Note Net Amt</label>
                                        <input type="text" id="tax_amountdbNetAmt" name="tax_amountdbNetAmt" readonly
                                            class="form-control" value="<?php echo number_format($netDeduction, 2, '.', ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="clearfix"></div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="tax_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="tax_amount" class="control-label">Taxable Amount</label>
                                        <input type="text" id="tax_amount" name="tax_amount" readonly
                                            class="form-control" value="<?php echo number_format($taxableAmount, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <?php
                                if ($PaidAmts->PaidAmt == "" || $PaidAmts->PaidAmt == null) {
                                    $paid = 0;
                                } else {
                                    $paid = $PaidAmts->PaidAmt;
                                }
                                $PendingAmt = $historyDetails->OrderAmt - $paid;
                                    $GateINDetails->PaymentPer;
                                ?>
                                <?php
                                $gst_Amt = $historyDetails->cgstamt + $historyDetails->sgstamt + $historyDetails->igstamt;
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="gst_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="gst_amount" class="control-label">Gst Amount</label>
                                        <input type="text" id="gst_amount" name="gst_amount" readonly
                                            class="form-control" value="<?php echo number_format($gst_Amt - $DBGstAmt, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <?php
                                $net_Amt = $Amt;
                                +$gst_Amt;
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="net_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="net_amount" class="control-label">Net Amount</label>
                                        <input type="text" id="net_amount" name="net_amount" readonly
                                            class="form-control" value="<?php echo number_format($historyDetails->NetOrderAmt - $netDeduction, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="gst_type">

                                    </div>
                                </div>

                            </div>
                            <?php 
                            if($PendingAmt < 0){
                                $Tax = 0;
                                $PendingAmt = $historyDetails->NetOrderAmt - $paid;
                            }else{
                                if($GateINDetails->CustomerType == '1'){
                                    $PendingAmt = $historyDetails->OrderAmt - $paid;
                                }else{
                                    $PendingAmt = ($historyDetails->OrderAmt - $TotalDeduction) - $paid;
                                }
                                $Tax = 1;
                            }
                            ?>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="gst_type">
                                        <label>Select Gst Type</label>
                                        <select class="selectpicker display-block" data-width="100%" name="gst_type"
                                            id="gst_type" class="form-control required">
                                            <?php
                                                if($Tax == "0"){
                                                    ?>
                                                    <option value="With Gst">With Gst</option>
                                                <?php
                                                }else{
                                                    ?>
                                                    <option value="Without Gst">Without Gst</option>
                                                    <option value="With Gst">With Gst</option>
                                                <?php
                                                }
                                            ?>
                                            
                                            
                                        </select>

                                        <span id="salerate_error_message" style="color:red"></span>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="paid_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="paid_amount" class="control-label">Paid Amount</label>
                                        <input type="text" id="paid_amount" name="paid_amount" readonly
                                            class="form-control" value="<?php echo $paid; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="due_amount">
                                        <small class="req text-danger">* </small>
                                        <label for="due_amount" class="control-label">Due Amount</label>
                                        <input type="text" id="due_amount" name="due_amount" readonly
                                            class="form-control" value="<?php echo number_format($PendingAmt, 2, '.', ''); ?>">
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="clearfix"></div>
                                <?php
                                if($GateINDetails->PaymentPer == '' || $GateINDetails->PaymentPer == NULL){
                                    $percent = 100;
                                }else{
                                    $percent = $GateINDetails->PaymentPer;
                                }
                                $Asper_per_amt = $PendingAmt * ($percent / 100);
                                ?>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="payment_per">
                                        <small class="req text-danger">* </small>
                                        <label for="payment_per" class="control-label">Approve Payment % </label>
                                        <input type="text" id="payment_per" name="payment_per" class="form-control"
                                            onkeypress="return isNumber(event)"
                                            value="<?php echo $percent; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="payment_amt">
                                        <small class="req text-danger">* </small>
                                        <label for="payment_amt" class="control-label">Amount</label>
                                        <input type="text" id="payment_amt" name="payment_amt" class="form-control"
                                            readonly value="<?php echo  number_format($Asper_per_amt, 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <br>
                                <?php
                                if (has_permission_new('PurchasePaymentList', '', 'create')) {
                                    ?>
                                    <?php if($GateINDetails->QCApprove == 'N') { 
                                        $disabled = 'disabled';
                                    } else { 
                                         $disabled = '';
                                    } ?>
                                    <div class="col-md-1">
                                        <button type="button"  class="btn btn-info CreatePayment" id="CreatePayment"
                                            style="margin-right: 35px;">Paid</button>
                                    </div>
                                    <?php
                                }
                                ?>
                                
                                <?php
                                $IsCD = $GateINDetails->IsCD;
                                if ($IsCD == "N" && has_permission_new('PurchasePaymentList', '', 'create')) {
                                    ?>
                                    <div class="col-md-3">
                                        <!--<button type="button" id="CreateDN" class="btn btn-primary">Generate Debit-->
                                        <!--    Note</button>-->
                                    </div>
                                    <?php

                                }
                                ?>

                            </div>
                            <?php if($GateINDetails->QCApprove == 'N') { 
                                $labelText = '';
                                if($GateINDetails->CustomerType == '1'){
                                    $labelText = 'Please approve center QC before making payment';
                                }else{
                                    $labelText = 'Please approve HO QC before making payment';
                                }
                            ?>
                                <div class="col-md-12">
                                    <div class="form-group" app-field-wrapper="payment_per">
                                        <label for="payment_amt" class="control-label" style="color:red;margin-left: 15px;"><?php echo $labelText; ?></label>
                                    </div>
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
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode = 46 && charCode > 31
            && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<script>

    $("#payment_per").keyup(function () {
        var due_amount = $('#due_amount').val();
        var val = $(this).val();
        if (val == "") {
            $('#payment_amt').val('0.00');
        } else {
            if (val > 100) {
                alert('please enter less than equal to 100%');
                $('#payment_amt').val('0.00');
                $(this).val('0');
            } else {
                var PayAmt = parseFloat(due_amount) * (parseFloat(val) / 100);
                $('#payment_amt').val(parseFloat(PayAmt).toFixed(2));
            }
        }
    })

    $('#CreatePayment').click(function () {
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        var payment_amt = $('#payment_amt').val();
        if (GateINID == "") {
            alert('please relaod page, something went wrong');
        } else if (BookingID == "") {
            alert('please relaod page, something went wrong');
        } else if (payment_amt == '' || payment_amt == "0") {
            alert('please enter payment amount');
        } else {
            if (confirm("Do you want to Create Payment Voucher?") == true) {
                $.ajax({
                    url: "<?php echo admin_url(); ?>GateControl/GaneratePayment",
                    dataType: "json",
                    method: "POST",
                    data: { GateINID: GateINID, BookingID: BookingID, payment_amt: payment_amt },
                    beforeSend: function () {
                        $('#sendrequest').html('Please wait request sending.');
                    },
                    success: function (r) {
                        if (r == true) {
                            window.location.reload("<?php echo admin_url(); ?>GateControl/PaymentDetails/" + GateINID);
                        } else {
                            window.location.reload("<?php echo admin_url(); ?>GateControl/PaymentDetails/" + GateINID);
                        }
                    }
                });
            }
        }
    })

    $('#CreateDN').click(function () {
        var GateINID = $('#GateINID').val();
        var BookingID = $('#BookingID').val();
        if (confirm("Do you want to Create Debit Note?") == true) {
            $.ajax({
                url: "<?php echo admin_url(); ?>GateControl/CreateDebitNote",
                dataType: "json",
                method: "POST",
                data: { GateINID: GateINID, BookingID: BookingID },
                beforeSend: function () {
                    $('#sendrequest').html('Please wait request sending.');
                },
                success: function (r) {
                    if (r == true) {
                        window.location.reload("<?php echo admin_url(); ?>GateControl/PaymentDetails/" + GateINID);
                    } else {
                        window.location.reload("<?php echo admin_url(); ?>GateControl/PaymentDetails/" + GateINID);
                    }
                }
            });
        }
    })
</script>

<script>
    document.getElementById("gst_type").addEventListener("change", function () 
    {
        var selectedValue = this.value;
        var gstAmountInput = document.getElementById("gst_amount");
        var netAmountInput =  document.getElementById("net_amount");
        var dueAmountInput = document.getElementById("due_amount");
        var payAmountInput =  document.getElementById("pay_amount");
        var paidAmountInput =  document.getElementById("paid_amount");
        var taxAmountInput =  document.getElementById("tax_amount");

        if (selectedValue === "With Gst") {
            
            dueAmountInput.value = netAmountInput.value - paidAmountInput.value;
           
        } else {
            dueAmountInput.value = taxAmountInput.value - paidAmountInput.value;
        }
        
        var due_amount = $('#due_amount').val();
        var val = $('#payment_per').val();

        var PayAmt = parseFloat(due_amount) * (parseFloat(val) / 100);
        $('#payment_amt').val(parseFloat(PayAmt).toFixed(2));
    });



</script>