<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

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
    th,td {

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
                            <div class="col-md-12 text-centerr"  >
            					<nav aria-label="breadcrumb" >
            						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
            							<li class="breadcrumb-item active" aria-current="page"><b>Deposit Trade</b></li>
            						</ol>
            					</nav>
            					<hr class="hr_style" style="margin-Bottom:5px !important;">
            				</div>
                            <div class="col-md-12" style="height:10px;margin-bottom:3px;">
                                <span class="searchh2" style="display:none;">Please wait while fetching data.</span>
                                <span class="searchh3" style="display:none;">Please wait while creating new record.</span>
                                <span class="searchh4" style="display:none;">Please wait while updating data.</span>
                            </div>
                            
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="AccountID" class="control-label">AccountID/Mobile number</label>
									<input type="text" id="AccountID" name="AccountID" class="form-control" value=""  maxlength="10" pattern="^\d{1,10}$" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10);" >  
                      				<input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="">
									<input type="hidden" id="AccountType" name="AccountType" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="accountName">
									<small class="req text-danger">* </small>
									<label for="accountName" class="control-label">AccountName</label>
									<input type="text" id="accountName" name="accountName" class="form-control" value="" readonly>
								</div>
							</div>
                                
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="center">
									<small class="req text-danger">* </small>
									<label for="center" class="form-label">Center Name</label>
									<select name="center" id="center" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
										foreach ($center as $key => $value) {
											?>
											<option value="<?php echo $value['CenterID']; ?>"><?php echo $value['CenterName']; ?> </option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
								
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="center">
									<small class="req text-danger">* </small>
									<label for="warehouse" class="form-label">Warehouse</label>
									<select name="warehouse" id="warehouse" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>                                           
									</select>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="Commodity">
									<small class="req text-danger">* </small>
									<label for="Commodity" class="form-label">Commodity Name</label>
									<select name="Commodity" id="Commodity" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
									</select>
								</div>
							</div>
						
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="item" class="form-label">Item Name</label>
									<select name="item" id="item" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
									</select>
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Quantity">
									<small class="req text-danger">* </small>
									<label for="tradeqty" class="control-label">Trade Qty</label>
									<input type="text" id="tradeqty" name="tradeqty" class="form-control" value="" pattern="^\d+(\.\d{1,3})?$" 
										oninput="this.value = this.value.replace(/[^\d.]/g, '').replace(/(\.\d{3})\d+/g, '$1');" >
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Quantity">
									<small class="req text-danger">* </small>
									<label for="minqty" class="control-label">Min Qty</label>
									<input type="text" id="minqty" name="minqty" class="form-control" value="" pattern="^\d+(\.\d{1,3})?$" 
										oninput="this.value = this.value.replace(/[^\d.]/g, '').replace(/(\.\d{3})\d+/g, '$1');">
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Quantity">
									<small class="req text-danger">* </small>
									<label for="depositperiod" class="control-label">Deposit Period</label>
									<input type="text" id="depositperiod" name="depositperiod" class="form-control" value="" onkeypress="return isNumber(event)">
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="lockingperiod" class="form-label">Locking Period</label>
									<select name="lockingperiod" id="lockingperiod" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
										foreach ($lockingPeriod as $key => $value) {
											?>
											<option value="<?php echo $value['LockID']; ?>"><?php echo $value['LockName']; ?> </option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="billcycle" class="form-label">Billing Cycle</label>
									<select name="billcycle" id="billcycle" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
										foreach ($BillingCycle as $key => $value) {
											?>
											<option value="<?php echo $value['CycleID']; ?>"><?php echo $value['CycleName']; ?> </option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="Quantity">
									<small class="req text-danger">* </small>
									<label for="chargerate" class="control-label">Charge Rate(MT/Month)</label>
									<input type="text" id="chargerate" name="chargerate" class="form-control" value="" pattern="^\d+(\.\d{1,3})?$" 
										oninput="this.value = this.value.replace(/[^\d.]/g, '').replace(/(\.\d{3})\d+/g, '$1');">
								</div>
							</div>
								
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="ratetype" class="form-label">Rate Type</label>
									<select name="ratetype" id="ratetype" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<option value="1">Including GST</option>
										<option value="2">Excluding GST</option>
									</select>
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="fumigationcharge" class="form-label">Is Fumigation</label>
									<select name="fumigationcharge" id="fumigationcharge" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<option value="1">Yes</option>
										<option value="2">No</option>
									</select>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="ratefumigationcharge" class="form-label">Rate Inc.Fumigation</label>
									<select name="ratefumigationcharge" id="ratefumigationcharge" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<option value="1">Yes</option>
										<option value="2">No</option>
									</select>
								</div>
							</div>								
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="chargeamt">
									<small class="req text-danger">* </small>
									<label for="fumigationChargeAmt" class="control-label">Fumigation Charges Amt(MT/Month)</label>
									<input type="text" id="fumigationChargeAmt" name="fumigationChargeAmt" class="form-control" value="" pattern="^\d+(\.\d{1,3})?$" 
										oninput="this.value = this.value.replace(/[^\d.]/g, '').replace(/(\.\d{3})\d+/g, '$1');">
								</div>
							</div>
								
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="days">
									<small class="req text-danger">* </small>
									<label for="creditdays" class="control-label">Credit Days</label>
									<input type="text" id="creditdays" name="creditdays" class="form-control" value="" onkeypress="return isNumber(event)">
								</div>
							</div>  
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="TradeType">
                                    <small class="req text-danger">* </small>
                                    <label for="TradeType" class="form-label">Trade Type</label>
                                    <select name="TradeType" id="TradeType" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="D">Deposit</option>
                                        <option value="T">Trade Finance</option>
                                        <option value="A">Anamat</option>
                                    </select>
                                </div>
                            </div>
							
							<div class="col-md-2">
                                <div class="form-group">
                                    <?php $value = date('d/m/Y');?>
                                    <?php echo render_date_input( 'saudadate', 'Sauda Date',$value,'text'); ?>
                                </div>
                            </div>
						   
							<div class="clearfix"></div>                          
							
							<div class="col-md-12" style="margin-top: 10px;">
								<?php if (has_permission_new('DepositeTradePunch', '', 'create')) {
								?>
								<button type="button" class="btn btn-info asnBtn" style="margin-right: 25px;">Save</button>
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
    $(document).ready(function() {
	$('#fumigationChargeAmt').closest('.col-md-3').hide();
 
        let timerOn = true;
        function timer(remaining) {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;

            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('timer').innerHTML = m + ':' + s;
            remaining -= 1;

            if (remaining >= 0 && timerOn) {
                setTimeout(function() {
                    timer(remaining);
                }, 1000);
                return;
            }

            if (!timerOn) {
                // Do validate stuff here
                return;
            }
            $('#timer_id').css('display', 'none');
            $('#resend_otp').css('display', 'block');
        }

        $('#resend_email').on('click', function() {
            var phoneNumber = $('#AccountID').val();
            var center = $('#center').val();
            var Commodity = $('#Commodity').val();
            var item = $('#item').val();
            var CurrentRate = $('#currentrate').val();
            var Quantity = $('#Quantity').val();
            var QuantityBag = $('#QuantityBag').val();
            var vehicle_number = $('#vehicle_number').val();
            var driver_mobile = $('#driver_mobile').val();
            if(phoneNumber == ''){
                alert('Enter mobile number');
            }else if(center == ''){
                alert('Select Center');
            }else if(Commodity == ''){
                alert('Select Commodity');
            }else if(item == ''){
                alert('Select Item');
            }else if(Quantity == ''){
                alert('Enter Quantity');
            }else if(QuantityBag == ''){
                alert('Enter Bag Quantity');
            }else if(vehicle_number == ''){
                alert('Enter Vehicle Number');
            }else if(driver_mobile == ''){
                alert('Enter Driver Mobile Number');
            }else{
                $.ajax({
                    url: "<?php echo site_url(); ?>authentication/sendOTP",
                    method: "POST",
                    dataType:"json",
                    data: {
                        phoneNumber: phoneNumber
                    },
                    success: function(data) {
                        if(data == false){
                            console.log("Please register first, number does not exist in the database.");
                            alert("Please enter your registered Mobile Number");
                        }else{
                            $(".send_otp_div").css("display", "none");
                            $(".submit_otp_div").css("display", "block");
                            $(".submit_otp").css("display", "block");
                            $('#timer_id').css('display', 'block');
                            $('#resend_otp').css('display', 'none');
                            timer(120);
                        }
                    }
                });
            }
        });

        $('.send_otp').on('click', function() {
            var phoneNumber = $('#AccountID').val();
            var center = $('#center').val();
            var Commodity = $('#Commodity').val();
            var item = $('#item').val();
            var CurrentRate = $('#currentrate').val();
            var Quantity = $('#Quantity').val();
            var QuantityBag = $('#QuantityBag').val();
            var vehicle_number = $('#vehicle_number').val();
            var driver_mobile = $('#driver_mobile').val();
            if(phoneNumber == ''){
                alert('Enter mobile number');
            }else if(center == ''){
                alert('Select Center');
            }else if(Commodity == ''){
                alert('Select Commodity');
            }else if(item == ''){
                alert('Select Item');
            }else if(Quantity == ''){
                alert('Enter Quantity');
            }else if(QuantityBag == ''){
                alert('Enter Bag Quantity');
            }else if(vehicle_number == ''){
                alert('Enter Vehicle Number');
            }else if(driver_mobile == ''){
                alert('Enter Driver Mobile Number');
            }else{
                $.ajax({
                    url: "<?php echo site_url(); ?>authentication/sendOTP",
                    method: "POST",
                    dataType:"json",
                    data: {
                        phoneNumber: phoneNumber
                    },
                    success: function(data) {
                        if(data == false){
                            console.log("Please register first, number does not exist in the database.");
                            alert("Please enter your registered Mobile Number");
                        }else{
                            $(".send_otp_div").css("display", "none");
                            $(".submit_otp_div").css("display", "block");
                            $(".submit_otp").css("display", "block");
                            $('#timer_id').css('display', 'block');
                            $('#resend_otp').css('display', 'none');
                            timer(120);
                        }
                    }
                });
            }
        });

        $('#verifyOTP').on('click', function() {
            var phoneNumber = $('#AccountID').val();
            var otp = $('#enter_otp').val();
            $.ajax({
                url: "<?php echo admin_url(); ?>order/verifyOTP",
                method: "POST",
                data: {
                    phoneNumber: phoneNumber,
                    otp: otp
                },
                success: function(data) {
                    if (data) {
                        $('#verifyOTP').css("display", "none");
                        $('#conf_msg').css("display", "block");
                        $('#is_otp_veryfied').val('1');
                        $('#resend_otp').css("display", "none");
                        $('#timer_id').css("display", "none");
                        
                        //alert('veryfication done');
                    } else {
                        alert("Verification failed. Please check your OTP and try again.");
                    }
                }
            });

        });
    });
</script>
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
    $('#AccountID').focus(function() {
        $(".send_otp_div").css("display", "none");
        $(".submit_otp_div").css("display", "none");
        $("#verifyOTP").css("display", "none");
        $("#conf_msg").css("display", "none");
        $("#timer_id").css("display", "none");
        $("#resend_otp").css("display", "none");
        $('#accountName').val('');
        $('#AccountID').val('');
        $('#AccountType').val('');
        $('#is_otp_veryfied').val('');
        $('#Quantity').val('');
        $('#currentrate').val('');
        $('#Mastercurrentrate').val('');
        $('#QuantityBag').val('');
        $('#vehicle_number').val('');
        $('#driver_mobile').val('');
        $('#center').val('');
        $("#center").selectpicker("refresh");
        $("#Commodity").find('option').remove();
        $("#Commodity").selectpicker("refresh");
        $("#item").find('option').remove();
        $("#item").selectpicker("refresh");
        $("#currentrate").attr('disabled','disabled');
    });
    $('#AccountID').blur(function() {
        var AccountID = $('#AccountID').val();
        if(AccountID){
            $.ajax({
                url: "<?php echo admin_url(); ?>order/fetchClientData",
                method:"POST",
                dataType:"JSON",
                data:{
                    AccountID:AccountID,
    			},
    			beforeSend: function(){
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
                },
                complete: function () {
                    $('.searchh2').css('display','none');
                },
                success:function(data){
                    if(data == null){
                        $(".send_otp_div").css("display", "none");
                        $(".submit_otp_div").css("display", "none");
                        $("#verifyOTP").css("display", "none");
                        $("#conf_msg").css("display", "none");
                        $("#timer_id").css("display", "none");
                        $("#resend_otp").css("display", "none");
                        alert('No records found');
                        $('#accountName').val('');
                        $('#AccountID').val('');
                        $('#AccountType').val('');
                        $('#is_otp_veryfied').val('');
                    }else{
                        //$(".send_otp_div").css("display", "block");
                        $(".submit_otp_div").css("display", "none");
                        $("#verifyOTP").css("display", "none");
                        $("#conf_msg").css("display", "none");
                        $("#timer_id").css("display", "none");
                        $("#resend_otp").css("display", "none");
        				$('#accountName').val(data.company);
        				$('#AccountType').val(data.CustomerType);
        				$('#is_otp_veryfied').val('');
        				if(data.CustomerType == "1"){
        				    $("#currentrate").removeAttr('disabled');
        				}else{
        				    $("#currentrate").attr('disabled','disabled');
        				}
                    }
                    $('#Quantity').val('');
                    $('#currentrate').val('');
                    $('#Mastercurrentrate').val('');
                    $('#QuantityBag').val('');
                    $('#vehicle_number').val('');
                    $('#driver_mobile').val('');
                    $('#center').val('');
                    $("#center").selectpicker("refresh");
                    $("#Commodity").find('option').remove();
                    $("#Commodity").selectpicker("refresh");
                    $("#item").find('option').remove();
                    $("#item").selectpicker("refresh");
                    
    			}
    		});
        }
		
	});   
   
</script>

<script>
	
	$('#ratefumigationcharge').on('change', function(){
		var RateincludingChargeId = $(this).val();
		var ChargeId = $('#fumigationcharge').val();		
		
		if(ChargeId == 1 && RateincludingChargeId == 2)
		{
			$('#fumigationChargeAmt').closest('.col-md-3').show();
		}
		else
		{ $('#fumigationChargeAmt').closest('.col-md-3').hide();
		}
	})		
	
	$('#fumigationcharge').on('change', function(){
		var ChargeId = $(this).val();	
		var RateincludingChargeId = $('#ratefumigationcharge').val();
		if(ChargeId == 1 && RateincludingChargeId == 2)
		{
			$('#fumigationChargeAmt').closest('.col-md-3').show();
		}
		else
		{ $('#fumigationChargeAmt').closest('.col-md-3').hide();
		}
	})
		
	$('#center').on('change', function(){
        var id = $(this).val();
        $.ajax({
            url:"<?php echo admin_url(); ?>PurchaseTradeMaster/GetWarehouse",
            dataType:"JSON",
            method:"POST",
            data:{CenterID:id},
            beforeSend: function(){
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
            },
            complete: function () {
                $('.searchh2').css('display','none');
            },
            success:function(data){
                $("#warehouse").find('option').remove();
                $("#warehouse").selectpicker("refresh");
                var html = "";
                html += '<option value=""></option>';
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="'+ data[i].AccountID +'">'+data[i].w_name+'</option>';
                }
                $('#warehouse').append(html);
                $('.selectpicker').selectpicker('refresh');           
            }
        });
    })

    $('#center').on('change', function(){
        var id = $(this).val();
        $.ajax({
            url:"<?php echo admin_url(); ?>order/GetCommodity",
            dataType:"JSON",
            method:"POST",
            data:{CenterID:id},
            beforeSend: function(){
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
            },
            complete: function () {
                $('.searchh2').css('display','none');
            },
            success:function(data){
                $("#Commodity").find('option').remove();
                $("#Commodity").selectpicker("refresh");
                var html = "";
                html += '<option value=""></option>';
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="'+ data[i].GroupCode +'">'+data[i].name+'</option>';
                }
                $('#Commodity').append(html);
                $('.selectpicker').selectpicker('refresh');
                
                $("#item").find('option').remove();
                $("#item").selectpicker("refresh");
                $('#currentrate').val(''); 
                $('#Mastercurrentrate').val(''); 
            }
        });
    })
    
    $('#Commodity').on('change', function(){
        var center = $("#center").val();
        var CommodityID = $("#Commodity").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>order/GetItemId",
            dataType:"JSON",
            method:"POST",
            data:{center:center,CommodityID:CommodityID,},
            beforeSend: function(){
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
            },
            complete: function () {
                $('.searchh2').css('display','none');
            },
            success:function(data){
                $("#item").find('option').remove();
                $("#item").selectpicker("refresh");
                $('#item').append('<option value=""></option>');
                var html = "";
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="'+ data[i].ItemID +'">'+data[i].ItemName+'</option>';
                }
                $('#item').append(html);
                $('.selectpicker').selectpicker('refresh');
                $('#currentrate').val('');
                $('#Mastercurrentrate').val('');
            }
        });
    })  

</script>

<script>
    $('.asnBtn').on('click',function()
	{
        var AccountID = $('#AccountID').val();
        var AccountName = $('#accountName').val();
        var center = $('#center').val();
		var Warehouse = $('#warehouse').val();
        var Commodity = $('#Commodity').val();
        var item = $('#item').val();
		var TradeQty = $('#tradeqty').val();
		var MinQty = $('#minqty').val();
		var DepositPeriod = $('#depositperiod').val();
		var LockingPeriod =  $('#lockingperiod').val();
		var BillCycle = $('#billcycle').val();
		var ChargeRate = $('#chargerate').val();
		var RateType = $('#ratetype').val();
		var FumigationCharge = $('#fumigationcharge').val();
		var RateFumigationCharge = $('#ratefumigationcharge').val();
		var FumigationChargeAmt = $('#fumigationChargeAmt').val();
		var CreditDays = $('#creditdays').val();
		var SaudaDate =  $('#saudadate').val(); 
		var TradeType = $('#TradeType').val(); 

		if ($('#fumigationChargeAmt').closest('.col-md-3').is(':visible')) {
            var fumigationChargeAmtValue = $('#fumigationChargeAmt').val();                        
        }
		else{
			var fumigationChargeAmtValue = "";			 
		}		        
		
        if(AccountID == ''){
            alert('Enter Account ID');
        }else if(center == ''){
            alert('Select Center');
        }else if(Warehouse == ''){
            alert('Select Warehouse');
        }else if(Commodity == ''){
            alert('Select Commodity');
        }else if(item == ''){
            alert('Select Item');
        }else if(TradeQty == ''){
            alert('Enter Quantity');
        }else if(MinQty == ''){
            alert('Enter Min Quantity');
        }else if(DepositPeriod == ''){
            alert('Enter Deposit Period');
        }else if(LockingPeriod == ''){
            alert('Select Locking Period');
        }else if(BillCycle == ''){
            alert('Select Bill Cycle');
        }else if(ChargeRate == ''){
            alert('Enter Charge Rate');
        }else if(RateType == ''){
            alert('Select Rate Type');
        }else if(FumigationCharge == ''){
            alert('Select Is Fumigation');
        }else if(RateFumigationCharge == ''){
            alert('Select Rate Inc.Fumigation');
        }else if(CreditDays == ''){
            alert('Enter Credit Days');
        }else{
            $.ajax({
                url: "<?php echo admin_url(); ?>PurchaseTradeMaster/SaveOrder",
                method:"POST",
                dataType:"JSON",
                data:{
                    AccountID:AccountID,AccountName:AccountName,center:center,Warehouse:Warehouse,Commodity:Commodity,item:item,TradeQty:TradeQty,MinQty:MinQty,DepositPeriod:DepositPeriod,SaudaDate:SaudaDate,TradeType:TradeType,
                    LockingPeriod:LockingPeriod,BillCycle:BillCycle,ChargeRate:ChargeRate,RateType:RateType,FumigationCharge:FumigationCharge,RateFumigationCharge:RateFumigationCharge,CreditDays:CreditDays,fumigationChargeAmtValue:fumigationChargeAmtValue
                },
                beforeSend: function(){
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
                },
                complete: function () {
                    $('.searchh2').css('display','none');
                },
                success:function(data)
				{
                    if(data == true){
                        alert('Trade added successfully');
                        window.location.reload();
                    }else{
                        alert('Error occured');
                        window.location.reload();
                    }
                }
            });   
        }
    })
</script>

<script type="text/javascript">
   $('#depositperiod').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
        event.preventDefault();
    }
});

 $('#creditdays').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
        event.preventDefault();
    }
});
</script>