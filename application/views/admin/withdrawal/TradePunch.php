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
            							<li class="breadcrumb-item active" aria-current="page"><b>Withdrawal Trade Punch</b></li>
									</ol>
								</nav>
            					<hr class="hr_style" style="margin-Bottom:5px !important;">
							</div>
                            <div class="col-md-12" style="height:10px;margin-bottom:3px;">
                                <span class="searchh2" style="display:none;">Please wait while fetching data.</span>
                                <span class="searchh3" style="display:none;">Please wait while creating new record.</span>
                                <span class="searchh4" style="display:none;">Please wait while updating data.</span>
							</div>
							
							<div class="col-md-2">
                                <div class="form-group" app-field-wrapper="TradeType">
                                    <label for="TradeType" class="form-label">Trade Type</label>
                                    <select name="TradeType" id="TradeType" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="D">Deposit</option>
                                        <option value="T">Trade Finance</option>
                                        <option value="A">Anamat</option>
                                    </select>
                                </div>
                            </div>
                            
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="AccountID" class="control-label">AccountID/Mobile number</label>
									<input type="text" id="AccountID" name="AccountID" class="form-control" value="">
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
										
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="item">
									<small class="req text-danger">* </small>
									<label for="item" class="form-label">Item Name</label>
									<select name="item" id="item" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
									</select>
								</div>
							</div>
							
							<div class="clearfix"></div>
							
							<div class="col-md-8">
								<div class="table-purchase_request tableFixHead2">
									<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
										<thead>
											<tr>
												<th>Tag</th>
												<th>Trade ID</th>
												<th>Gatein ID</th>
												<th>Inward Qty</th>
												<th>Withdraw Qty</th>
												<th>Available Qty</th>
												<th>Qty</th>
											</tr>
										</thead>
										<tbody id="TradeBody">
											
										</tbody>
									</table>   
								</div>
							</div>
							
							
                            <div class="col-md-12" style="margin-top: 10px;">
                                <?php if (has_permission_new('WithdrawalTradePunch', '', 'create')) {
								?>
                                <button type="button" class="btn btn-info SaveBtn" style="margin-right: 25px;">Save</button>
                                <?php }else{
                                    ?>
                                    <button type="button" class="btn btn-info SaveBtn2" disabled style="margin-right: 25px;">Save</button>
                                <?php
                                } ?>
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
        $('#accountName').val('');
        $('#AccountID').val('');
        $('#AccountType').val('');
        $('#center').find('option').remove();
        $("#center").selectpicker("refresh");
        $("#item").find('option').remove();
        $("#item").selectpicker("refresh");
	});
    $('#AccountID').blur(function() {
        var AccountID = $('#AccountID').val();
        var TradeType =$('#TradeType').val();
        if(AccountID){
            $.ajax({
                url: "<?php echo admin_url(); ?>Withdrawal/fetchClientData",
                method:"POST",
                dataType:"JSON",
                data:{
                    AccountID:AccountID,TradeType:TradeType,
				},
    			beforeSend: function(){
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
				},
                complete: function () {
                    $('.searchh2').css('display','none');
				},
                success:function(data){
					var centers = data.CenterList;
                    if(data == null){
                        alert('No records found');
                        $('#accountName').val('');
                        $('#AccountID').val('');
                        $('#AccountType').val('');
						}else{
        				$('#accountName').val(data.company);
        				$('#AccountType').val(data.CustomerType);
					}
					
					$('#center').empty(); // Remove old options
					$('#center').append('<option value="">Non Selected</option>'); // Default option
					
					$.each(centers, function(id, name) {
						$('#center').append('<option value="' + id + '">' + name + '</option>');
					});
                    $("#center").selectpicker("refresh");
                    $("#item").find('option').remove();
                    $("#item").selectpicker("refresh");
                    
				}
			});
		}
		
	});
    
	
</script>

<script>
	
    $('#center').on('change', function(){
        var AccountID = $('#AccountID').val();
        var TradeType = $('#TradeType').val();
        var id = $(this).val();
        $.ajax({
            url:"<?php echo admin_url(); ?>Withdrawal/GetTradeAvailableItems",
            dataType:"JSON",
            method:"POST",
            data:{CenterID:id,AccountID:AccountID,TradeType:TradeType},
            beforeSend: function(){
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
            complete: function () {
                $('.searchh2').css('display','none');
			},
            success:function(data){
				$('#item').empty(); // Remove old options
				$('#item').append('<option value="">Non Selected</option>'); // Default option
				
				$.each(data, function(id, name) {
					$('#item').append('<option value="' + id + '">' + name + '</option>');
				});
				$("#item").selectpicker("refresh");
			}
		});
	})
    
    
    $('#item').on('change', function(){
        var AccountID = $('#AccountID').val();
        var CenterID = $("#center").val();
        var ItemID = $("#item").val();
        var TradeType = $("#TradeType").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>Withdrawal/fetchTradeData",
            dataType:"JSON",
            method:"POST",
            data:{CenterID:CenterID,ItemID:ItemID,AccountID:AccountID,TradeType:TradeType},
            beforeSend: function(){
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
            complete: function () {
                $('.searchh2').css('display','none');
			},
            success:function(data){
				$('#TradeBody').html(data);
			}
		});
	})
	
	function restrictQty(input) {
		let max = parseFloat(input.getAttribute('max'));
		let val = parseFloat(input.value);
		
		if (val > max) {
			alert('Entered quantity exceeds available stock!');
			input.value = max;
		}
	}
	
</script>

<script>
	
	$('.SaveBtn').on('click', function () 
	{
		var AccountID = $('#AccountID').val();
        var center = $('#center').val();
        var item = $('#item').val();
		
		let valid = false;
		let selectedItems = [];
		
		$('tr').each(function () {
			let checkbox = $(this).find('td.check input[type="checkbox"]');
			if (checkbox.prop('checked')) {
				let GateINID = checkbox.attr('name');
				let qtyInput = $(this).find('input.issue_qty');
				let BookingID = $(this).find('input.BookingID').val(); 
				
				let qty = parseFloat(qtyInput.val());
				
				if (!isNaN(qty) && qty > 0) 
				{
					selectedItems.push({
						GateINID: GateINID,
						Qty: qty,
						BookingID: BookingID
					});
					valid = true;
				}else {
					qtyInput.focus();
					valid = false;
					return false; 
				}
			}
		});
		
		// Construct FormData object
		let formData = new FormData();
		formData.append("TradeType", $('#TradeType').val());
		formData.append("AccountID", $('#AccountID').val());
		formData.append("CenterID", $('#center').val());
		formData.append("ItemID", $('#item').val());
		formData.append("SelectedItems", JSON.stringify(selectedItems)); 
		if(AccountID == ''){
            alert('Enter Account ID');
			}else if(center == ''){
            alert('Select Center');
			}else if(item == ''){
            alert('Select Item');
			}else if (!valid) {
			alert('Select row and enter valid quantity.');
			}else{
			$.ajax({
				url: "<?php echo admin_url(); ?>Withdrawal/SaveTradePunch",
				type: "POST",
				data: formData,
				processData: false, // important
				contentType: false, // important
				dataType: "json",
				beforeSend: function () {
					$('.searchh2').show().css('color', 'blue');
				},
				complete: function () {
					$('.searchh2').hide();
				},
				success: function (response) {
					console.log(response);
					if (response.status === true) {
						alert_float('success',""+response.message+"");
						window.location.reload();
						} else {
						alert_float('warning',""+response.message+"");
					}
				},
				error: function (xhr, status, error) {
					console.error("AJAX Error: ", status, error);
					alert("Something went wrong");
				}
			});
		}
	});
	
</script>

<script type="text/javascript">
	$('#Quantity').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
			event.preventDefault();
		}
	});
</script>