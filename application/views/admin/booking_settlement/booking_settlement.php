<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #filter_table td:hover {
        cursor: pointer;
    }
    #filter_table tr:hover {
        background-color: #ccc;
    }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
    
    #bottom_row{
        position: fixed;
        bottom: 0%;
        right: 0%;
        background-color: #fff;
        width: 100%;
        margin: auto;
        padding: 1% 2%;
        border-top:1px solid #323a45;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Trade Settlement</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
                        <div class="row">
                            <input type="hidden" id="CompID" name="CompID" class="form-control" value="">
                            <input type="hidden" id="ItemID" name="ItemID" class="form-control" value="">
                            <input type="hidden" id="CenterID" name="CenterID" class="form-control" value="">
                            <input type="hidden" id="BrokerID" name="BrokerID" class="form-control" value="">
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="BookingType">
                                    <small class="req text-danger">* </small>
                                    <label for="BookingType" class="control-label">BookingType</label>
                                    <select name="BookingType" id="BookingType" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="P">Kirti Purchase</option>
                                        <option value="S">Kirti Sell</option>
                                        <option value="D">Deposit</option>
                                        <option value="W">Withdrawal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="PartyName">
                                    <small class="req text-danger">* </small>
                                    <label for="PartyName" class="control-label">Party/Trader Name</label>
                                    <select name="PartyName" id="PartyName" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="BookingID">
                                    <small class="req text-danger">* </small>
                                    <label for="BookingID" class="control-label">BookingID</label>
                                    <select name="BookingID" id="BookingID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <?php 
                                    $attr = array("disabled"=>"disabled")
                                ?>
                                <?php echo render_date_input( 'booking_date', 'Booking Date','','text',$attr); ?>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="booking_rate">
                                    <small class="req text-danger">* </small>
                                    <label for="booking_rate" class="control-label">Booking Rate</label>
                                    <input type="text" id="booking_rate" name="booking_rate" readonly class="form-control" value="">
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="BookingQty">
                                    <small class="req text-danger">* </small>
                                    <label for="BookingQty" class="control-label">Booking Weight(Qtls)</label>
                                    <input type="text" id="BookingQty" name="BookingQty" readonly class="form-control" value="">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="inw_Weight">
                                    <small class="req text-danger">* </small>
                                    <label for="inw_Weight" class="control-label">Inward Weight(Qtls)</label>
                                    <input type="text" id="inw_Weight" name="inw_Weight" readonly class="form-control" value="">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="today_rate">
                                    <small class="req text-danger">* </small>
                                    <label for="today_rate" class="control-label">Today Rate</label>
                                    <input type="text" id="today_rate" name="today_rate" readonly class="form-control" value="">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="diff_qty">
                                    <small class="req text-danger">* </small>
                                    <label for="diff_qty" class="control-label">Shortage Quantity(Qtls)</label>
                                    <input type="text" id="diff_qty" name="diff_qty" readonly class="form-control" value="">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="shortageAmt">
                                    <small class="req text-danger">* </small>
                                    <label for="shortageAmt" class="control-label">Shortage Amt</label>
                                    <input type="text" id="shortageAmt" name="shortageAmt" class="form-control" value="">
                                </div>
                            </div>
                            
                            
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="Status">
                                    <small class="req text-danger">* </small>
                                    <label for="Status" class="control-label">Status</label>
                                    <select name="Status" id="Status" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=''>Not Selected</option>
                                        <option value='2'>Completed</option>
                                        <option value='3'>Partial Completed</option>
                                    </select>
                                </div>
                            </div>
                            
                            
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="is_invoice">
                                    <label for="is_invoice" class="control-label">Generate Shortage Invoice</label>
                                    <select name="is_invoice" id="is_invoice" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=''>Not Selected</option>
                                        <option value='Y'>Yes</option>
                                        <option value='N'>No</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="is_not_delivered">
                                    <label for="is_not_delivered" class="control-label">Not Delivered Charges</label>
                                    <select name="is_not_delivered" id="is_not_delivered" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=''>Not Selected</option>
                                        <option value='Y'>Yes</option>
                                        <option value='N'>No</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2" id="NotDeliveredAmt">
                                <div class="form-group" app-field-wrapper="not_del_charge_amt">
                                    <small class="req text-danger">* </small>
                                    <label for="not_del_charge_amt" class="control-label">Not Delivered Charge Amt</label>
                                    <input type="text" id="not_del_charge_amt" name="not_del_charge_amt" class="form-control" value="">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="Remark">
                                    <small class="req text-danger">* </small>
                                    <label for="Remark" class="control-label">Remark</label>
                                    <textarea name="Remark" id="Remark" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="searchh" style="display:none;">Please wait while updating settlement data.</div>
                            </div>
                            
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-9">
                                <table class="table table-bordered" id="filter_table">
                                    <thead>
                                        <tr>
                                            <th>ASN No.</th>
                                            <th>GATE PASS</th>
                                            <th>Inward Date</th>
                                            <th>AccountID</th>
                                            <th>PartyName</th>
                                            <th>BookingID</th>
                                            <th>ItemID</th>
                                            <th>ItemName</th>
                                            <th>Net Weight (Qtl)</th>
                                            <th style="text-align:left;">Total Bag</th>
                                            <th style="text-align:left;">Total Katta</th>
                                            <th style="text-align:left;">Total Layer</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_data">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row" id="bottom_row">
                            <div class="col-md-12">
                                <?php if (has_permission_new('Booking_settlement', '', 'create')) {
                                ?>
                                <button type="button" class="btn btn-info saveBtn" id="saveBtn" style="margin-right: 25px;float:right">Save</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>
<?php init_tail(); ?>

<script>

    $("#shortageAmt").keypress(function (e) {
        if(e.which == 46){
            if($(this).val().indexOf('.') != -1) {
                return false;
            }
        }
        if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    $('#BookingType').on('change',function(){
        var BookingType = $('#BookingType :selected').val();
        if(BookingType != ''){
            $.ajax({
                url: "<?php echo admin_url(); ?>GateControl/GetAllClientsName",
                method:"POST",
                data:{
                    BookingType:BookingType
                },
                beforeSend: function(){
                    $('#PartyName').html('');
                    $('#PartyName').selectpicker('refresh');
                },
                success:function(data){ 
                    $('#today_rate').val('');
                    $('#CompID').val('');
                    $('#ItemID').val('');
                    $('#CenterID').val('');
                    $('#BrokerID').val('');
                    $('#diff_qty').val('');
                    $('#shortageAmt').val('');
                    $('select[name=Status]').val(data.state);
                    $('.selectpicker').selectpicker('refresh');
                    $('#Remark').val('');
                    $('select[name=is_invoice]').val(data.state);
                    $('.selectpicker').selectpicker('refresh');
                    $('#booking_rate').val('');
                    $('#booking_date').val('');
                    $('#BookingQty').val('');
                    $('#inw_Weight').val('');
                    $("#PartyName").children().remove();
                    $('#PartyName').selectpicker('refresh');
                    $("#BookingID").children().remove();
                    $('#BookingID').selectpicker('refresh');
                    $('#PartyName').html(data);
                    $('#PartyName').selectpicker('refresh');
                    $('select[name=is_not_delivered]').val('');
                    $('.selectpicker').selectpicker('refresh');
                    $('#NotDeliveredAmt').css('display','none');
                    $('#table_data').html('');
                    
                }
            });
        }else{
            $('#ItemID').val('');
            $('#CenterID').val('');
            $('#BrokerID').val('');
            $('#CompID').val('');
            $('#today_rate').val('');
            $('#diff_qty').val('');
            $('#shortageAmt').val('');
            $('select[name=Status]').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#Remark').val('');
            $('select[name=is_invoice]').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#booking_rate').val('');
            $('#booking_date').val('');
            $('#BookingQty').val('');
            $('#inw_Weight').val('');
            $('#table_data').html('');
            $("#PartyName").children().remove();
            $('#PartyName').selectpicker('refresh');
            $("#BookingID").children().remove();
            $('#BookingID').selectpicker('refresh');
            $('select[name=is_not_delivered]').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#NotDeliveredAmt').css('display','none');
        }
    });
</script>
<script>
    $('#PartyName').on('change',function(){
        var BookingType = $('#BookingType :selected').val();
        var PartyName = $('#PartyName :selected').val();
        $.ajax({
            url: "<?php echo admin_url(); ?>GateControl/GetAllBookingID",
            method:"POST",
            data:{
                BookingType:BookingType,
                PartyName:PartyName
            },
            beforeSend: function(){
                $('#BookingID').html('');
                $('#BookingID').selectpicker('refresh');
            },
            success:function(data){ 
                $('#ItemID').val('');
                $('#CenterID').val('');
                $('#BrokerID').val('');
                $('#CompID').val('');
                $('#today_rate').val('');
                $('#diff_qty').val('');
                $('#shortageAmt').val('');
                $('select[name=Status]').val('');
                $('.selectpicker').selectpicker('refresh');
                $('#Remark').val('');
                $('select[name=is_invoice]').val('');
                $('.selectpicker').selectpicker('refresh');
                $('#booking_rate').val('');
                $('#booking_date').val('');
                $('#BookingQty').val('');
                $('#inw_Weight').val('');
                $("#BookingID").children().remove();
                $('#BookingID').html(data);
                $('#BookingID').selectpicker('refresh');
                $('select[name=is_not_delivered]').val('');
                $('.selectpicker').selectpicker('refresh');
                $('#NotDeliveredAmt').css('display','none');
                $('#table_data').html('');
            }
        });     
    });
</script>
<script>
    $(document).ready(function(){
    $('#NotDeliveredAmt').css('display','none');
    $('#is_not_delivered').on('change',function(){
        var value = $(this).val();
        if(value == "Y"){
            $('#NotDeliveredAmt').css('display','block');
        }else{
            $('#NotDeliveredAmt').css('display','none');
        }
    })
    $('#BookingID').on('change',function(){
        var BookingType = $('#BookingType :selected').val();
        var Name = $('#PartyName :selected').val();
        var BookingID = $('#BookingID :selected').val();
        if(BookingType != '' && Name != '' && BookingID != ''){
            $.ajax({
                url: "<?php echo admin_url(); ?>GateControl/GetTableData",
                method:"POST",
                data:{
                    BookingType:BookingType,
                    Name:Name,
                    BookingID:BookingID,
                },
                beforeSend: function(){
                    $('#table_data').html('');
                },
                success:function(data){ 
                    $('#table_data').html(data);
                }
            });
            
            $.ajax({
                url: "<?php echo admin_url(); ?>GateControl/GetBookingDetails",
                method:"POST",
                dataType:"JSON",
                data:{
                    BookingType:BookingType,
                    Name:Name,
                    BookingID:BookingID,
                },
                beforeSend: function(){
                    $('#table_data').html('');
                },
                success:function(data){
                    if(data.BookingDate !== null){
                        $('#booking_rate').val(data.BookingRate);
                        var Booking_d = data.BookingDate;
                        var date = Booking_d.substring(0, 10);
                        var date_new = date.split("-").reverse().join("/");
                        $('#booking_date').val(date_new);
                    }else{
                        $('#booking_date').val('');
                    }
                    $('#ItemID').val(data.ItemID);
                    $('#CenterID').val(data.CenterID);
                    $('#BrokerID').val(data.BrokerID);
                    $('#CompID').val(data.PartyID);
                    $('#BookingQty').val(data.BookingWeight);
                    $('#inw_Weight').val(data.NetWeight);
                    /*if(data.NetWeight == "0.00"){
                        $("#is_not_delivered").removeAttr('disabled');
                    }else{
                        $("#is_not_delivered").attr('disabled','disabled');
                    }*/
                    $('#diff_qty').val(data.diff_qty);
                    $('#today_rate').val(data.TodayRate);
                    $('#shortageAmt').val(data.ChargesAmt);
                    $('#is_not_delivered').val('');
                    $('select[name=is_invoice]').val('');
                    $('.selectpicker').selectpicker('refresh');
                    $('select[name=is_not_delivered]').val('');
                    $('.selectpicker').selectpicker('refresh');
                    $('#NotDeliveredAmt').css('display','none');
                }
            });
        }
    });
    $('#saveBtn').on('click',function(){
        //alert('hello');
        var BookingType = $('#BookingType :selected').val();
        var PartyName = $('#PartyName :selected').val();
        var BookingID = $('#BookingID :selected').val();
        var booking_rate = $('#booking_rate').val();
        var BookingQty = $('#BookingQty').val();
        var inw_Weight = $('#inw_Weight').val();
        var today_rate = $('#today_rate').val();
        var diff_qty = $('#diff_qty').val();
        var shortageAmt = $('#shortageAmt').val();
        var Status = $('#Status :selected').val();
        var Remark = $('#Remark').val();
        var CompID = $('#CompID').val();
        var ItemID = $('#ItemID').val();
        var CenterID = $('#CenterID').val();
        var BrokerID = $('#BrokerID').val();
        var is_not_delivered = $('#is_not_delivered').val();
        if(is_not_delivered == "Y"){
            var NotDelAmt = $('#not_del_charge_amt').val();
        }else{
            var NotDelAmt = 0;
        }
        var is_invoice = $('#is_invoice :selected').val();
            if(BookingType == ''){
                alert('please select booking type');
            }else if(PartyName == ''){
                alert('please select Party Name');
            }else if(BookingID == ''){
                alert('please select booking ID');
            }else if(booking_rate == ''){
                alert('some required fields are missing please refresh page');
            }else if(BookingQty == ''){
                alert('some required fields are missing please refresh page');
            }else if(inw_Weight == ''){
                alert('some required fields are missing please refresh page');
            }else if(today_rate == ''){
                alert('some required fields are missing please refresh page');
            }else if(diff_qty == ''){
                alert('some required fields are missing please refresh page');
            }else if(shortageAmt == ''){
                alert('some required fields are missing please refresh page');
            }else if(CompID == ''){
                alert('some required fields are missing please refresh page');
            }else if(ItemID == ''){
                alert('some required fields are missing please refresh page');
            }else if(CenterID == ''){
                alert('some required fields are missing please refresh page');
            }else if(Status == ''){
                alert('please select status');
            }else if(Remark == ''){
                alert('please enter remark');
            }else if(is_not_delivered == 'Y' && NotDelAmt == "0"){
                alert('Please enter not delivered charges amount');
            }else{
                if (confirm("Do you want to settle this Trade!") == true) {
                    $.ajax({
                        url: "<?php echo admin_url(); ?>GateControl/SaveSettlement",
                        method:"POST",
                        dataType:"JSON",
                        data:{
                            BookingType:BookingType,PartyName:PartyName,BookingID:BookingID,inw_Weight:inw_Weight,today_rate:today_rate,Status:Status,is_not_delivered:is_not_delivered,
                            Remark:Remark,is_invoice:is_invoice,shortageAmt:shortageAmt,CompID:CompID,BookingQty:BookingQty,CenterID:CenterID,ItemID:ItemID,BrokerID:BrokerID,NotDelAmt:NotDelAmt
                        },
                        beforeSend: function(){
                            $('.searchh').css('display','block');
                            $('.searchh').css('color','blue');
                        },
                        complete: function () {
                            $('.searchh').css('display','none');
                        },
                        success:function(data){
                            if(data == true){
                                alert('Trade settlement done successfully');
                            }else{
                                alert('something went wrong, please try again');
                            }
                            window.location.reload(true);
                        }
                    });
                }
            }
    })
    });
</script>
