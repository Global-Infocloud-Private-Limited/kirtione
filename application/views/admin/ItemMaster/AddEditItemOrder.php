<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">      
		<div class="row">
            <div class="col-md-12">
                <div class="panel_s">
					
					<div class="panel-body">
						<div class="_buttons"> 
							<?php
								$fy = $this->session->userdata('finacial_year');
							?>
							<div class="col-md-2">
								<div class="form-group">
									<label for="orderid"> <small class="req text-danger">* </small>OrderID</label>
									<div class="input-group">
										<span class="input-group-addon">ORD<span id="prefix_year"><?php echo $fy;?></span></span>
										<input type="text" name="orderid" id="orderid" class="form-control receiptsid" value="<?php echo $NextOrderId;?>" data-isedit="" data-original-number="">
									</div>
								</div>
							</div>                                         
							
							<!--<div class="col-md-2">
								<?php                                             
									$current_date = date('d/m/Y');
									$attr = array('readonly'=>'readonly');
									echo render_date_input('posted_date', 'Order Date',$current_date,$attr);
								?>                               
							</div> -->
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="posted_date">
									<label for="posted_date" class="control-label">Order Date</label>
									<div class="input-group date"><input type="text" id="posted_date" name="posted_date" class="form-control datepicker hasDatepicker" readonly="readonly" value="<?= $current_date?>" autocomplete="off">
										<div class="input-group-addon">
											<i class="fa fa-calendar calendar-icon"></i>
										</div>
									</div>
								</div>                               
							</div>
							
							<div class="col-md-4">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="centername" class="control-label">Center Name</label>
									<select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option>   
										<?php
											foreach($centermaster as $center) 
											{
												echo '<option value="' . $center['CenterID'] . '">' . $center['CenterName'] . '</option>';
											} 
										?>                                                                                                                                    
									</select>
								</div>
							</div>  
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="pin">
									<label for="ledgerbal" class="control-label">Closing Balance</label>
									<input type="text"  name="ledgerbal" id="ledgerbal" class="form-control" readonly>
								</div>
							</div> 
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="pin">
									<label for="rewards" class="control-label">Reward Point Balance</label>
									<input type="text"  name="rewards" id="rewards" class="form-control" readonly>
								</div>
							</div>    
							
							<div class="col-md-4">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="AccountID" class="control-label">Select Party</label>
									<select name="AccountID" id="AccountID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option> 
										<option value="new">New Party</option> 
										<?php
											foreach($clients as $value) 
											{
												echo '<option value="' . $value['AccountID'] . '">' . $value['company']. ' (' . $value['AccountID'] . ')</option>';
											} 
										?>                                                                 
									</select>
								</div>
							</div>
							
							<div class="col-md-2" id="party_name">
								<div class="form-group" app-field-wrapper="partyname">
									<small class="req text-danger">* </small>
									<label for="partyname" class="control-label">Party Name</label>
									<input type="text" id="partyname" name="partyname" class="form-control" value="">
								</div>
							</div>   
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="phonenumber">
									<small class="req text-danger">* </small>
									<label for="phonenumber" class="control-label">Mobile Number</label>
									<input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)" readonly>
								</div>
							</div>    
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="villagename">    
									<label for="villagename" class="control-label">Village Name</label>
									<input type="text" id="villagename" name="villagename" class="form-control" value="">
								</div>
							</div>   
							
							<div class="col-md-2" id="billstate-container">
								<div class="form-group" app-field-wrapper="State">      
									<small class="req text-danger">* </small>                                
									<label for="billstate">Billing State</label>
									<select name="billstate" id="billstateid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option> 
										<?php
											foreach($states as $statelist) 
											{
												echo '<option value="' . $statelist['short_name'] . '">' . $statelist['state_name'] . '</option>';
											} 
										?>                                                                      
									</select>                                                          
								</div>
							</div>                
							
							<div class="col-md-2">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="ordstat" class="control-label">Order Status</label>
									<select name="ordstat" id="ordstat" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="O">Pending</option>
										<option value="C">Cancel</option>
										<option value="F">Completed</option>                                                                                                   
									</select>
								</div>
							</div>       
							
							
							<div class="col-md-2">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="ordtype" class="control-label">Order Type</label>
									<select name="ordtype" id="ordtype" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                   
										<option value="1">Cash Order</option>
										<option value="2">Credit Order</option>                                                                                                                                  
									</select>
								</div>
							</div>   
							
							<div class="col-md-2" id="paymode-container">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="paymentmode" class="control-label">Payment Mode</label>
									<select name="paymentmode" id="paymentmode" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="1">Cash</option>
										<option value="2">Online</option>                                                                                                                                  
									</select>
								</div>
							</div>   
							
							<div class="col-md-2" id="paymethod-container">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="paymentmethod" class="control-label">Payment Method</label>
									<select name="paymentmethod" id="paymentmethod" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option>
										<option value="1">UPI</option>
										<option value="2">Bank Transfer</option>   
										<option value="3">Credit/Debit Card</option>                                                                                                                                 
									</select>
								</div>
							</div>   
							
							<div class="col-md-2" id="refernececont">                       
								<div class="form-group">
									<label for="referenceno">Reference No</label>     
									<input type="text" class="form-control" id="referenceno" />                
								</div>                        
							</div>
							
							<div class="col-md-2" id="effect-container">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="Effecton" class="control-label">Effect On</label>
									<select name="Effecton" id="Effecton" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<?php
											foreach($EffectOn as $val1) 
											{
												echo '<option value="' . $val1['AccountID'] . '">' . $val1['company'] . '</option>';
											} 
										?>                                                                                                                                                           
									</select>
								</div>
							</div>  
							
							<div class="col-md-2">                            
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="ordfrom" class="control-label">Order From</label>
									<select name="ordfrom" id="ordfrom" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                   
										<option value="WEB">Web</option>
										<option value="APP">App</option>                                                                                                                                  
									</select>
								</div>
							</div>  
							
							<div class="col-md-2" id="bill_no">
								<div class="form-group" app-field-wrapper="pin">
									<label for="billno" class="control-label">Bill No</label>
									<input type="text"  name="billno" id="billno" class="form-control">
								</div>
							</div> 
							<div class="col-md-2" >
								<div class="form-group" app-field-wrapper="pin">
									<label for="OtherAmt" class="control-label">Other Amt</label>
									<input type="text" onkeyup="calculateTotalNetAmount()" name="OtherAmt" id="OtherAmt" class="form-control">
								</div>
							</div> 
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="">
									<small class="req text-danger"> </small>
									<label for="EffectOnOtherAmt" class="control-label">Effect On Other Amt</label>
									<select id="EffectOnOtherAmt" name="EffectOnOtherAmt"  class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option>
										<?php
											foreach($DirectIncome as $val1) 
											{
												echo '<option value="' . $val1['AccountID'] . '">' . $val1['company'] . '</option>';
											} 
										?>                                  
									</select>
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="phonenumber">
									<small class="req text-danger">* </small>
									<label for="type" class="control-label">Delivery Type</label>
									<select id="type" name="type" class="form-control">                                    
										<option value="1">Pickup</option>
										<option value="2">Home Delivery</option>                                   
									</select>
								</div>
							</div> 
							<div class="col-md-2" id="pin-container">
								<div class="form-group" app-field-wrapper="pin">
									<label for="pin" class="control-label">Delivery PinCode</label>
									<input type="text"  name="pin" id="pin" class="form-control">
								</div>
							</div>           
							
							<div class="col-md-2" id="state-container">
								<div class="form-group" app-field-wrapper="State">      
									<small class="req text-danger">* </small>                                
									<label for="state">Delivery State</label>
									<select name="state" id="stateid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value=""></option> 
										<?php
											foreach($states as $statelist) 
											{
												echo '<option value="' . $statelist['short_name'] . '">' . $statelist['state_name'] . '</option>';
											} 
										?>                                                                      
									</select>                                                          
								</div>
							</div>   
                            
							<div class="col-md-2" id="city-container">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="city" class="control-label">Delivery City</label>
									<select name="city" id="city" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                
										<option value=""></option> 
										<?php
											foreach($citylist as $city) 
											{
												echo '<option value="' . $city['id'] . '">' . $city['city_name'] . '</option>';
											} 
										?>       
									</select>
								</div>
							</div>
							
							<div class="col-md-2" id="taluka-container">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="subdist" class="control-label">Delivery Taluka</label>
									<select name="subdist" id="subdist" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">        
										<option value=""></option>    
										<?php
											foreach($talukalist as $taluka) 
											{
												echo '<option value="' . $taluka['id'] . '">' . $taluka['TalukaName'] . '</option>';
											} 
										?>   
									</select>
								</div>
							</div>
							
							<div class="col-md-2" id="loc-container">
								<?php echo render_input('loc', 'Delivery Locality'); ?>
							</div>
							
							<div class="col-md-2" id="street-container">
								<?php echo render_input('street', 'Delivery Street'); ?>
							</div>
							
							<div class="col-md-2" id="house-container">
								<?php echo render_input('house', 'Delivery House'); ?>
							</div>                    
							
							<!-- <div class="col-md-2">
								<div class="form-group" app-field-wrapper="pin">
                                <label for="convo" class="control-label">Conversion</label>
                                <input type="text"  name="convo" id="convo" class="form-control" readonly>
								</div>
							</div> -->
							
						</div>                      
					</div>
				</div>
			</div>
		</div>
		
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <p class="bold p_style">Item Details</p>
                        <hr style="border: 1px solid #d81b60;"/>
                        <div class="" id="example">
						</div>
                        <?php echo form_hidden('sale_invoice_detail'); ?>
                        
                        <div class="col-md-12 ">
                            <table class="table">
								<tbody>
									<tr id="total_td">
										<td>
											<label for="total_qty_in_mt">Total Qty</label> 
											<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="">
										</td>
										<td>
											<label for="total_amt_in_mt">SubTotal</label> 
											<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="">
										</td>
										<td>
											<label for="total_disc_in_mt">Discount Amt</label> 
											<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="">
										</td>
										<td>
											<label  for="Total_value">Taxable Amt</label>  
											<input  type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value"  value="" >
										</td>  
										
										<td>  
											<label  for="total_cgst_amt">CGST Amt</label>  
											<input type="text" readonly value="" class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt">
										</td>
										<td>  
											<label  for="total_sgst_amt">SGST Amt</label>
											<input type="text" readonly value="" class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt">
										</td>
										<td>  
											<label  for="total_igst_amt">IGST Amt</label> 
											<input type="text" readonly value="" class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt">
										</td>
										
										<td>  
											<label  for="total_roundoff_amt">RoundOff Amt</label> 
											<input type="text" readonly value="" class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt">
										</td>                            
										
										<td>  
											<label  for="netpayableamt">Invoice Amt</label> 
											<input type="text" readonly value="" class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt">
										</td>                                    
									</tr>        
								</tbody>
							</table>
						</div>               
					</div>
				</div>		
                <div class="btn-bottom-toolbar text-right" style="width: 100%;">		   
					<a href="#" id="viewlist" class="btn btn-warning edit-new-order mleft10" style="background-color: #ff6f00; color: white;">View List</a> 
					<button type="button" id="savebtn" class="btn-tr save_detail btn btn-info mleft10" >Save & Proceed</button>
					<button type="button" id="updatebtn" class="btn-tr update_detail btn btn-info mleft10 hidden">Update</button>  	
					<button type="button" id="cancelbtn" class="btn-tr cancel_detail btn btn-danger mleft10 hidden">Cancel</button> 	            	   
				</div>       
				
				<div class="clearfix"></div>       
				
				<!-- Iteme List Model-->            
                <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
							<div class="modal-header" style="padding:5px 10px;">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Order Details</h4>
							</div>
							<div class="modal-body" style="padding:0px 5px !important">  
								<?php
									$fy = $this->session->userdata('finacial_year');
									$fy_new  = $fy + 1;
									$lastdate_date = '20'.$fy_new.'-03-31';
									$firstdate_date = '20'.$fy_new.'-04-01';
									$curr_date = date('Y-m-d');
									$curr_date_new    = new DateTime($curr_date);
									$last_date_yr = new DateTime($lastdate_date);
									if($last_date_yr < $curr_date_new){
										$to_date = '31/03/20'.$fy_new;
										$from_date = '01/03/20'.$fy_new;
										}else{
										$from_date = "01/".date('m')."/".date('Y');
										$to_date = date('d/m/Y');
									}
								?> 
								<div class="row">
									<div class="col-md-3">
										<?php echo render_date_input('from_date','From',$from_date); ?>
									</div>
									<div class="col-md-3">
										<?php echo render_date_input('to_date','To',$to_date);?>
									</div>
									<div class="col-md-3">
										<br>
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
									</div>
									<div class="col-md-3">
										<br>
										<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
									</div>
								</div>
								<div class="table-Item_List tableFixHead2">
									<table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
										<thead>
											<tr style="display:none;">
												<td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
											</tr>
											<tr>
												<th id="sl" style="text-align:left; width: 10%;">Sr No.</th>                                                        
												<th style="text-align:left; width: 15%;">OrderID</th>
												<th style="text-align:left; width: 15%;">Order Date</th> 
												<th style="text-align:left; width: 15%;">Customer Name</th>
												<th style="text-align:left; width: 15%;">Bill No</th>  
												<th style="text-align:left; width: 10%;">Total Qty</th>   
												<th style="text-align:left; width: 10%;">Total Disc Amt</th>  
												<th style="text-align:left; width: 10%;">Taxable Amt</th>  
												<th style="text-align:left; width: 10%;">Total GST</th> 
												<th style="text-align:left; width: 10%;">Invoice Amt</th>  
												<th style="text-align:left; width: 10%;">Order Status</th>      
												<th style="text-align:left; width: 10%;">Order From</th>                                                                                                                              
											</tr>
										</thead>
										<tbody>                                       
											
										</tbody>
									</table>   
								</div>
							</div>
							<div class="modal-footer" style="padding:0px;">
								<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
                <!-- /.modal -->		
				
				
			</div>            
		</div>           
	</div>
</div>

<?php init_tail(); ?>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link href="https://cdn.jsdelivr.net/npm/handsontable@11.1.0/dist/handsontable.full.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/handsontable@11.1.0/dist/handsontable.full.min.js"></script>
<?php require 'ItemOrderScript_js.php';?>

<script>
	$('#phonenumber').on('blur', function() {
		var phonenumber = $(this).val();
		var AccountID = $('#AccountID').val();
		if(phonenumber !=="" && AccountID == "new"){
		    var url = "<?php echo admin_url(); ?>ItemMaster/CheckMobileNumber";
            jQuery.ajax({
                type: 'POST',
                url:url,
                data: {phonenumber: phonenumber,AccountID:AccountID},
                dataType:'json',
                success: function(data) {
                    if(data){
                        alert('Mobile number already used..');
                        $('#phonenumber').val('');
                        $('#phonenumber').focus();
					}
				}
			});   
		}
	});
	
    function refreshTable() 
    {
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
        $.ajax({
            url:  "<?php echo admin_url(); ?>ItemMaster/Order_table_data",
            method:"POST",
			data:{from_date:from_date, to_date:to_date},
            dataType: "json", 
			beforeSend: function () {
					$('#searchh2').css('display','block');
					$('#table_Item_List tbody').css('display','none');
				},
				complete: function () {
					$('#table_Item_List tbody').css('display','');
					$('#searchh2').css('display','none');
				}, 
            success: function(data) 
            {               
				var tableBody = $("#table_Item_List tbody"); 
				tableBody.empty();                         
                var redirectUrl = "<?php echo admin_url(); ?>ItemMaster/AddEditItemOrder";     
                $.each(data, function(index, value) 
                {
                    var OrderStat;
                    if (value.OrderStatus == "O") {
                        OrderStat = "Pending";
						} else if (value.OrderStatus == "F") {
                        OrderStat = "Completed";
						} else if (value.OrderStatus == "C") {
                        OrderStat = "Cancelled";
					}
					
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.OrderID + "' onclick=\"window.open('" + redirectUrl + "?OrderId=" + value.OrderID + "', '_blank');\">");
                    newRow.append("<td>" + (index + 1) + "</td>");
                    newRow.append("<td>" + value.OrderID + "</td>");
                    newRow.append("<td>" + value.formattedDate + "</td>");
                    newRow.append("<td>" + value.name + ' (' + value.AccountID + ")" + "</td>");
					newRow.append("<td>" + (value.BIllNo || "") + "</td>");
					
                    newRow.append("<td>" + value.totalQuantity + "</td>");  
                    newRow.append("<td style='text-align: right;'>" + value.totalDiscountAmt.toFixed(2) + "</td>");   
                    newRow.append("<td style='text-align: right;'>" + value.totalValueAmt.toFixed(2) + "</td>");          
                    newRow.append("<td style='text-align: right;'>" + value.totalTaxAmt.toFixed(2) + "</td>");
                    newRow.append("<td style='text-align: right;'>" + value.totalNetAmt.toFixed(2) + "</td>");          
                    newRow.append("<td>" + OrderStat + "</td>");       
					newRow.append("<td>" + value.order_type + "</td>");                                        
                    tableBody.append(newRow); 
				});
                
			},
            error: function(xhr, status, error) {
                console.error("Error occurred while fetching data: " + error);
			}
		});
	}
	$('#search_data').on('click',function(){
			refreshTable();
		});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () 
    {
		var selectElement = document.getElementById('Effecton');
		var Delivery_type = $("#type").val();
		if(Delivery_type == 1)
		{
			$("#state-container").hide();
            $("#city-container").hide();
            $("#taluka-container").hide();
            $("#po-container").hide();
            $("#loc-container").hide();
            $("#street-container").hide();
            $("#house-container").hide();
            $("#pin-container").hide();
		}
		
		var checkboxValue = this.checked ? 0 : 1;
		if(checkboxValue == 1)
		{
            $("#shipping-container").hide();
		}       
		
		var PaymentMode = $("#paymentmode").val();
		if(PaymentMode ==1)
		{
            $("#paymethod-container").hide();
            $("#refernececont").hide();            
		}
		
		if (selectElement.options.length > 0) {
            selectElement.selectedIndex = 0;  
		}
        for (var i = 1; i < selectElement.options.length; i++) {
            selectElement.options[i].style.display = 'none';  
		}
	});
</script>

<script>
	function addShippingRow() 
    {
        var pincode = $("#pincode").val();      
        var pincode_text = $("#pincode option:selected").text();
        var statename = $("#statename").val();   
        var cityname = $("#cityname").val(); 
        var talukaname = $("#talukaname").val(); 
        
        if (pincode.trim() === '') {            
            $('#pincode-error').text('Select Pincode');
            setTimeout(() => {
                $('#pincode-error').text('');  
			}, 2000); 
            return;            
			}else if(statename.trim() === '') {            
            $('#state-error').text('Enter state name.');
            setTimeout(() => {
                $('#state-error').text('');  
			}, 2000); 
            return; 
			}else if(cityname.trim() === '') {            
            $('#city-error').text('Enter city name.');
            setTimeout(() => {
                $('#city-error').text('');  
			}, 2000); 
            return; 
			}else if(talukaname.trim() === '') {            
            $('#taluka-error').text('Enter taluka name.');
            setTimeout(() => {
                $('#taluka-error').text('');  
			}, 2000); 
            return; 
		}            
		
        var newRow = $("<tr class='addedtr'></tr>");        
        newRow.append("<td><input type='hidden' name='pincode[]' value='" + pincode + "'>" + pincode_text + "</td>");        
        newRow.append("<td><input type='text' name='statename[]' class='form-control' value='" + statename + "'></td>");
        newRow.append("<td><input type='text' name='cityname[]' class='form-control' value='" + cityname + "'></td>");
        newRow.append("<td><input type='text' name='talukaname[]' class='form-control' value='" + talukaname + "'></td>");
        newRow.append("<td><a href='#' class='btn btn-danger removeshippingbtn'><i class='fa fa-times'></i></a></td>");
        
        // Append the new row to the table body
        $("#shippingtbody").append(newRow);         
		
        // Clear input fields after adding row              
        $("#pincode").val('');
        $("#statename").val('');    
        $("#cityname").val('');    
        $("#talukaname").val('');       
	}
	
    $(document).on('click', '.removeshippingbtn', function() {
		$(this).closest('tr').remove();
	});
</script>

<script>
    $(document).ready(function() 
    {        
		$("#party_name").hide();
		$("#viewlist").click(function(){			
            $('#Item_List').modal('show');	    
            $('#Item_List').on('shown.bs.modal', function () {
                $('#myInput1').focus();
				refreshTable();
			})		    
		});
		
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy',  
            changeMonth: true,
            changeYear: true
		});  
		
		$("#AccountID").change(function() 
        {
			var AccountID = $("#AccountID").val();
			if(AccountID == "new")
			{
				$("#party_name").show();
				$("#ledgerbal").val('');
				$("#phonenumber").val('');
				$("#phonenumber").prop("readonly", false);
			}
			else
			{
				$("#party_name").hide();
				$("#phonenumber").prop("readonly", true);
			}
		});
		
        $("#type").change(function() 
        {          
            var Delivery_type = $("#type").val();
            if(Delivery_type == 2)
            {
                $("#state-container").show();
                $("#city-container").show();
                $("#taluka-container").show();
                $("#po-container").show();
                $("#loc-container").show();
                $("#street-container").show();
                $("#house-container").show();
                $("#pin-container").show();
			}
            else if(Delivery_type == 1)
            {
                $("#state-container").hide();
                $("#city-container").hide();
                $("#taluka-container").hide();
                $("#po-container").hide();
                $("#loc-container").hide();
                $("#street-container").hide();
                $("#house-container").hide();
                $("#pin-container").hide();
			}
		});
		
        $("#checkbox").change(function() 
        {
            var checkboxValue = this.checked ? 0 : 1;
            if(checkboxValue == 0)
            {
                $("#shipping-container").hide();
			}
            else if(checkboxValue == 1)
            {
                $("#shipping-container").show();
			}
		});     
		
        $("#ordtype").change(function() 
        {
            var Order_type = $("#ordtype").val();          
			
            if(Order_type == 2)
            {
                $("#paymode-container").hide();
                $("#effect-container").hide();
                $("#paymethod-container").hide();
                $("#refernececont").hide();               
			}
            else if(Order_type == 1)
            {
                $("#paymode-container").show();
                $("#effect-container").show();                              
			}            
		});
		
        $("#paymentmode").change(function() 
        {
            var Order_type = $("#ordtype").val();       
            var PaymentMode = $("#paymentmode").val();
			
            if(Order_type == 1 && PaymentMode == 2)
            {
                $("#paymethod-container").show();
                $("#refernececont").show();                 
			}
            else if(Order_type == 1 && PaymentMode == 1)
            {
                $("#paymethod-container").hide();
                $("#refernececont").hide();                    
			}
		});
		
        $("#paymentmode").change(function() 
        {
            var PaymentMode = $("#paymentmode").val();          
            var selectElement = document.getElementById('Effecton');
            var firstVisibleOption = null;
			
            for (var i = 0; i < selectElement.options.length; i++) 
            {
                var option = selectElement.options[i];
				
                if (PaymentMode == 2) 
                {                   
                    if (option.value == 'CASH') {  
                        option.style.display = 'none';  
						} else {
                        option.style.display = 'block';  
                        if (firstVisibleOption === null && i > 0) {
                            firstVisibleOption = option;
						}
					}
				}
                else if (PaymentMode == 1) 
                {                   
                    if (option.value == 'CASH') { 
                        option.style.display = 'block';  
                        if (i === 0) {
							selectElement.value = option.value; 
						}
						} else {
                        option.style.display = 'none';  
					}
				}
			} 
			
            if (firstVisibleOption !== null) 
            {
                selectElement.value = firstVisibleOption.value; 
			}            
            $(selectElement).selectpicker('refresh');
		});    
        $("#paymentmode").trigger('change');        
	});
</script>

<script>
    function handlePincodeSelect() 
    {
        var pincode = $("#pincode").val();  
		
        $.ajax({
			url:"<?php echo admin_url(); ?>ItemMaster/GetPincodeDetailbyId",
			dataType:"JSON",
			method:"POST",
			data:{pincode:pincode},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	              
			    $('#statename').val(data.State);                         		
			}
		});
	}
</script>

<script>
    $(document).ready(function(){
		var maxEndDate = new Date('Y/m/d');
		var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
		
		var year = "20"+fin_y;
		var cur_y = new Date().getFullYear().toString().substr(-2);
		if(cur_y => fin_y){
			var year2 = parseInt(fin_y) + parseInt(1);
			var year2_new = "20"+year2;
			var e_dat = new Date(year2_new+'/03/31');
			var maxEndDate_new = e_dat;
			}else{
			var e_dat2 = new Date(year2+'/03/31');
			var maxEndDate_new = e_dat2;
		}
		
		var minStartDate = new Date(year, 03);
		$('#posted_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
		
		
	});
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Item_List");
        tr = table.getElementsByTagName("tr");
		
        for (i = 2; i < tr.length; i++) {           
            tr[i].style.display = "none";           
            
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
				if (td[j]) {
					txtValue = td[j].textContent || td[j].innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = ""; 
						break; 
					}
				}
			}
		}
	}
</script>

<style>
    .hidden {
	display: none !important;
    }
	
    .p_style {
	color: #d81b60;
    }   
	
    .custom-width {
	
	width: 100%;
    }
	
    .custom-align-right {
	text-align: right;
	width: 100%;       
	margin-right: 30px;
	margin-left: -100px;
    }
	
    .class-width{
	width: 70%;        
    }
	
    .adj-width{
	width: 15%;        
    }
	
    .ref-width{
	width: 30%;     
    }    
	
    .custom-margin-left {
	margin-left: 0px;
    }
	
	
    #myModal .modal-header {
	width: 600px; 
	height: 50px;
	margin: 0 auto; 
    }
    .modal-header .close {
	position: absolute;  
	top: 10px; 
	right: 15px; 
	padding: 0;  
	font-size: 25px;
    }  
	
    .custom-width table  { border-collapse: collapse; width: 100%; }
	
    .custom-width th, .custom-width td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	
    .custom-width th { background: #50607b;
	
    color: #fff !important; }
	
	
	#table_Item_List td:hover {
    cursor: pointer;
	}
	#table_Item_List tr:hover {
    background-color: #ccc;
	}
	
	.hidden-button {
    display: none;
	}
	
	.table-Item_List {
    overflow: auto;
    max-height: 65vh;
    width: 100%;
    position: relative;
    top: 0px;
    border-collapse: collapse; /* Make sure the borders collapse */
	}
	
	/* Table Header Styling */
	.table-Item_List thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #50607b; /* Blue background */
    color: #fff !important; /* White text */
    padding: 1px 5px !important;
    white-space: nowrap;
    border: 1px solid !important;
    font-size: 11px;
    line-height: 1.42857143 !important;
    vertical-align: middle !important;
	}
	
	/* Table Body Header Styling (if you need sticky columns as well) */
	.table-Item_List tbody th {
    position: sticky;
    left: 0;
    background: #50607b; /* Blue background */
    color: #fff !important; /* White text */
    padding: 1px 5px !important;
    white-space: nowrap;
    border: 1px solid !important;
    font-size: 11px;
    line-height: 1.42857143 !important;
    vertical-align: middle !important;
	}
	
	/* Table Cell Styling for th and td */
	.table-Item_List th, .table-Item_List td {
    padding: 1px 5px !important;
    white-space: nowrap;
    border: 1px solid !important;
    font-size: 11px;
    line-height: 1.42857143 !important;
    vertical-align: middle !important;
	}
	
</style>
