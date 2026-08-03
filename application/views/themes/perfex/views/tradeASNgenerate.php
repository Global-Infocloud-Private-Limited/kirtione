<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    #AccountID {
        text-transform: uppercase;
    }

    #table_trade_List td:hover {
        cursor: pointer;
    }

    #table_trade_List tr:hover {
        background-color: #ccc;
    }

    .table-trade_List {
        overflow: auto;
        max-height: 50vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-trade_List thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-trade_List tbody th {
        position: sticky;
        left: 0;
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
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <h4 style="margin-left:2%;">Generate ASN</h4>
                            <br>
                            <div class="col-md-2" style="display:none;">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <input type="hidden" id="basic_rate" name="basic_rate" class="form-control" value="<?php echo $bookingDetails->basic_rate; ?>">
                                    <input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="<?php echo $bookingDetails->CurrentRate; ?>">
                                    <input type="hidden" id="TType" name="TType" class="form-control" value="<?php echo $bookingDetails->TType; ?>">
                                    <input type="hidden" id="TType2" name="TType2" class="form-control" value="<?php echo $bookingDetails->TType2; ?>">
								</div>
							</div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="BookingID">
                                    <small class="req text-danger">* </small>
                                    <label for="BookingID" class="control-label">BookingID</label>
                                    <input type="text" id="BookingID" name="BookingID" class="form-control" value="<?php echo $bookingDetails->BookingID; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="CenterID">
                                    <small class="req text-danger">* </small>
                                    <label for="CenterID" class="control-label">CenterID</label>
                                    <input type="text" id="CenterID" name="CenterID" class="form-control" value="<?php echo $bookingDetails->CenterID; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="PartyType">
                                    <small class="req text-danger">* </small>
                                    <label for="PartyType" class="control-label">Party Type</label>
                                    <input type="text" id="PartyType" name="PartyType" class="form-control" value="<?php echo $bookingDetails->CustomerType; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="PartyName">
                                    <small class="req text-danger">* </small>
                                    <label for="PartyName" class="control-label">PartyName</label>
                                    <input type="text" id="PartyName" name="PartyName" class="form-control" value="<?php echo $bookingDetails->company; ?>" readonly>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="Item">
                                    <small class="req text-danger">* </small>
                                    <label for="Item" class="control-label">Item Name</label>
                                    <input type="text" id="Item" name="Item" class="form-control" value="<?php echo $bookingDetails->ItemName; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="Quantity">
                                    <small class="req text-danger">* </small>
                                    <label for="Quantity" class="control-label"> Trade Qty</label>
                                    <input type="text" id="Quantity" name="Quantity" class="form-control" value="<?php echo $bookingDetails->quantity; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="Unit">
                                    <small class="req text-danger">* </small>
                                    <label for="Unit" class="control-label">Trade Unit</label>
                                    <input type="text" id="Unit" name="Unit" class="form-control" value="<?php echo $bookingDetails->unit; ?>" readonly>

                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="asn_qty_bag">
                                    <small class="req text-danger">* </small>
                                    <label for="asn_qty_bag" class="control-label">ASN Qty(Bag)</label>
                                    <input type="text" id="asn_qty_bag" name="asn_qty_bag" class="form-control" value="" onkeypress="return isNumber(event)">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="asn_qty_mt">
                                    <small class="req text-danger">* </small>
                                    <label for="asn_qty_mt" class="control-label">ASN Qty(MT)</label>
                                    <input type="text" id="asn_qty_mt" name="asn_qty_mt" class="form-control" value="">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <?php $from_date = date('d/m/Y');
                                $attr = array('disabled' => 'disabled');
                                ?>

                                <?php echo render_date_input('delivery_date_time', 'Delivery DateTime', $from_date, $attr); ?>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="VehicleNo">
                                    <label for="VehicleNo" class="control-label">Vehicle No</label>
                                    <input type="text" id="VehicleNo" name="VehicleNo" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="Phone">
                                    <label for="Phone" class="control-label">Phone No</label>
                                    <input type="tel" id="Phone" name="Phone" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info asnBtn" style="margin-right: 25px;">Generate ASN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode = 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<script>
    $(document).ready(function() {

        $('#BookingID').keyup(function() {
            $(this).val($(this).val().toUpperCase());
        });
        $('#VehicleNo').keyup(function() {
            $(this).val($(this).val().toUpperCase());
        });
    });
</script>

<script>
    $('.asnBtn').click(function() {
        var BookingID = $('#BookingID').val();
        var PartyType = $('#PartyType').val();
        var PartyName = $('#PartyName').val();
        var ItemID = $('#Item').val();
        var Quantity = $('#Quantity').val();
        var Unit = $('#Unit').val();
        var basic_rate = $('#basic_rate').val();
        var CurrentRate = $('#CurrentRate').val();
        var TType = $('#TType').val();
        var TType2 = $('#TType2').val();
        var asn_qty_bag = $('#asn_qty_bag').val();
        var asn_qty_mt = $('#asn_qty_mt').val();
        var VehicleNo = $('#VehicleNo').val();
        var Phone = $('#Phone').val();

        if (asn_qty_bag == "") {
            alert('please enter ASN Qty in bag');
        } else if (asn_qty_mt == "") {
            alert("please enter ASN Qty in MT");
        } else if ((BookingID == '') || (PartyType == '') || (PartyName == '') || (ItemID == '') || (Quantity == '') || (Unit == '')) {
            alert('data loading erroe please refresh page and try again');
        } else {
            if (PartyType == "1" && (parseFloat(basic_rate) != parseFloat(CurrentRate))) {
                var msg = 'Current rate and booking rate is not matched So current rate is : ' + parseFloat(CurrentRate) + ' and booking rate is : ' + parseFloat(basic_rate) + ' Do you want to continue with new rate';
                if (parseFloat(basic_rate) > parseFloat(CurrentRate)) {
                    var NewRate = parseFloat(basic_rate);
                } else {
                    var NewRate = parseFloat(CurrentRate);
                }
                if (confirm(msg)) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>Clients/insertASN",
                        method: "POST",
                        dataType: "JSON",
                        data: {
                            BookingID: BookingID,
                            ItemID: ItemID,
                            basic_rate: NewRate,
                            Unit: Unit,
                            TType: TType,
                            TType2: TType2,
                            asn_qty_bag: asn_qty_bag,
                            asn_qty_mt: asn_qty_mt,
                            VehicleNo: VehicleNo,
                            Phone: Phone
                        },
                        success: function(data) {
                            if (data.result == true) {
                                var ASNID = data.ASNID;
                                var BookingID = data.BookingID;
                                $('input').val('');
                                alert("ASN Generated");
                                window.location.href = "<?php echo base_url(); ?>Clients/CropsSellDetails/" + BookingID;
                                // window.open("<?php echo admin_url(); ?>GateControl/generateAsn/" + BookingID + "/" + ASNID, "_blank");
                            } else {
                                alert('This Trade has been settled or cancel');
                                window.location.reload(true);
                            }
                        }
                    });
                }
            } else {
                $.ajax({
                    url: "<?php echo base_url(); ?>Clients/insertASN",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        BookingID: BookingID,
                        ItemID: ItemID,
                        basic_rate: basic_rate,
                        Unit: Unit,
                        TType: TType,
                        TType2: TType2,
                        asn_qty_bag: asn_qty_bag,
                        asn_qty_mt: asn_qty_mt,
                        VehicleNo: VehicleNo,
                        Phone: Phone
                    },
                    success: function(data) {
                        if (data.result == true) {
                            var ASNID = data.ASNID;
                            var BookingID = data.BookingID;
                            $('input').val('');
                            alert("ASN Generated");
                            window.location.href = "<?php echo base_url(); ?>Clients/CropsSellDetails/" + BookingID;
                            // window.open("<?php echo admin_url(); ?>GateControl/generateAsn/" + BookingID + "/" + ASNID, "_blank");
                        } else {
                            alert('This Trade has been settled or cancel');
                            window.location.reload(true);
                        }
                    }
                });
            }
        }
    });
</script>
<script type="text/javascript">
    $('#asn_qty_mt').on('keypress', function(event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
            event.preventDefault();
        }
    });
</script>