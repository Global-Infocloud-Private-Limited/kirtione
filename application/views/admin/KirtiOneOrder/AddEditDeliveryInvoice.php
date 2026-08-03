<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
				echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
				
			?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12"> 
							<div class="panel-body">
								<nav aria-label="breadcrumb" >
									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
										<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
										<li class="breadcrumb-item active text-capitalize"><b>Sale</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>Delivery Invoice</b></li>
									</ol>
								</nav>
								<hr class="hr_style">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->AccountID : ''); ?>
												<?php $Isdisabled = (isset($challan_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="vendor">                           
													<label for="vendor">Select Party</label>							
													<select name="vendor" id="vendor"  onchange="GetChallan(this.value)" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>
														<option value=""></option> 
														<?php
															if ($Isdisabled):
														?>
														<option selected value="<?= $challan_details->AccountID;?>"><?= $challan_details->company;?></option>
														<?php
															else:
															foreach($party_list as $vendor) 
															{										
																echo '<option value="' . $vendor['AccountID'] . '" 
																data-partyid="' . $vendor['state'] . '" 
																' . ($value == $vendor['AccountID'] ? 'selected' : '') . '>' 
																. $vendor['company'] . 
																'</option>';
															} 
															
															endif;
														?>    
													</select>
													<?php if ($Isdisabled): ?>								
													<input type="hidden" name="vendor" id="vendor_value" value="<?php echo $value; ?>" />
													<?php else: ?>								
													<input type="hidden" name="vendor" id="vendor_value" value="<?php echo ($value ?: ''); ?>" />
													<?php endif; ?>								
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->ChallanID : ''); ?>
												<?php $Isdisabled = (isset($challan_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="ChallanID">                           
													<label for="ChallanID">Challan ID</label>							
													<select name="ChallanID" id="ChallanID" onchange="GetChallanDetails(this.value)"  class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>
														<option value=""></option>
														<?php if ($Isdisabled){ ?>
															<option value="<?= $value;?>" selected><?= $value;?></option>
														<?php } ?>
													</select>
													<?php if ($Isdisabled){ ?>								
														<input type="hidden" name="ChallanID" id="Po_no_value" value="<?php echo $value; ?>" />
													<?php } ?>						
												</div>
											</div>
											
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->phonenumber : ''); ?>
												<div class="form-group">
													<label for="mobile_no">Mobile No.</label>
													<input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $challan_details->phonenumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $challan_details->phonenumber; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->CenterName : ''); ?>
												<?php $value2 = (isset($challan_details) ? $challan_details->CenterStateShort : ''); ?>
												<?php $value3 = (isset($challan_details) ? $challan_details->CenterID : ''); ?>
												<?php $value4 = (isset($challan_details) ? $challan_details->GSTIN : ''); ?>
												<?php $value5 = (isset($challan_details) ? $challan_details->OrderID : ''); ?>
												<?php $value6 = (isset($challan_details) ? $challan_details->SalesID : ''); ?>
												<div class="form-group">
													<label for="mobile_no">Center Name</label>
													<input type="text" name="CenterName" id="CenterName" class="form-control" value="<?php echo $value; ?>"  readonly>
													<input type="hidden" name="CenterState" id="CenterState" class="form-control" value="<?php echo $value2; ?>"  readonly>
													<input type="hidden" name="CenterID" id="CenterID" class="form-control" value="<?php echo $value3; ?>"  readonly>
													<input type="hidden" name="OrderID" id="OrderID" class="form-control" value="<?php echo $value5; ?>"  readonly>
													<input type="hidden" name="TransID" id="TransID" class="form-control" value="<?php echo $value6; ?>"  readonly>
													<input type="hidden" name="PartyGSTIN" id="PartyGSTIN" class="form-control" value="<?php echo $value4; ?>">
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->gst : ''); ?>
												<div class="form-group">
													<label for="gst">GSTIN</label>
													<input type="text" name="gst" id="gst" class="form-control" value="<?php echo $challan_details->GSTIN; ?>" <?= $challan_details->GSTIN != '' ? 'readonly' : '';?> oninput="$('#PartyGSTIN').val(this.value);" >
												</div>
											</div> 
											
											<div class="col-md-2">							
												<?php $value = (isset($challan_details) ? $challan_details->state : ''); ?>	
												<?php $Isdisabled = (isset($challan_details) ? 'disabled' : ''); ?>
												
												<div class="form-group">
													<label for="state">State</label>								
													<div>									
														<select name="state" id="state" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled;?>>
															<option value=""></option> 
															 
														 <?php foreach($statelist as $val1) { ?>
                                                            <option value="<?php echo $val1['short_name']; ?>"
                                                                <?php echo ($value == $val1['short_name'] ? 'selected' : ''); ?>>
                                                                <?php echo $val1['state_name']; ?>
                                                            </option>
                                                        <?php } ?> 										
														</select>
													<?php if ($Isdisabled): ?>
                                                        <input type="hidden" name="state" value="<?php echo $value; ?>" />
                                                    <?php endif; ?>									
													</div>
												</div>
											</div> 
											<div class="clearfix"></div>
											
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->vehicleno : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="vehicleno" class="control-label">Vehicle No. </label>
													<input type="text"  name="vehicleno" id="vehicleno" value="<?= $value?>" class="form-control">
												</div>
											</div> 
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->DriverName : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="DriverName" class="control-label">Driver Name </label>
													<input type="text"  name="DriverName" id="DriverName" value="<?= $value?>" class="form-control">
												</div>
											</div> 
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->DriverMobile : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="DriverMobile" class="control-label">Driver Mobile </label>
													<input type="text" maxlength="10" name="DriverMobile" id="DriverMobile" value="<?= $value?>" class="form-control">
												</div>
											</div>  
											<div class="col-md-4">
												<?php $value = (isset($challan_details) ? $challan_details->TranportName : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="TranportName" class="control-label">Tranport Name</label>
													<input type="text"  name="TranportName" id="TranportName" value="<?= $value?>" class="form-control">
												</div>
											</div>
											<div class="col-md-2" id="bill_no">
												<?php $value = (isset($challan_details) ? $challan_details->PartyBillNo : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="billno" class="control-label">Bill No</label>
													<input type="text"  name="billno" id="billno" value="<?= $value?>" class="form-control">
												</div>
											</div>  
											
											<div class="clearfix"></div>
											
                							<div class="col-md-2">							
                								<?php $value = (isset($challan_details) ? $challan_details->EffectOnOtherAmt : ''); ?>	
                								<div class="form-group">
                									<label for="OthEffectOn">Other Amt Effect On </label>								
                									<div>									
                										<select name="OthEffectOn" id="OthEffectOn" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected">
                											<option value=""></option> 
                											<?php
                												foreach($DirectIncome as $val1) 
                												{ ?>
                												<option value="<?php echo $val1["AccountID"];?>" <?php if($val1["AccountID"] == $value){ echo "selected";}?>><?php echo $val1["company"]?></option> 
                												<?php												
                												} 
                											?>      										
                										</select>										
                									</div>
                								</div>
                							</div>
                							<div class="col-md-2">						
                							    <?php $value = (isset($challan_details) ? $challan_details->OtherAmt : '0'); ?>	
                								<div class="form-group" app-field-wrapper="OtherAmt">
                									<label for="OtherAmt" class="control-label">Other Amt</label>
                									<input type="text" name="OtherAmt" id="OtherAmt" class="form-control" value = "<?php echo $value; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->InvoiceNo; ?>">
                								</div>
                							</div>
											<div class="col-md-2">           
												<?php $value = (isset($challan_details) ? $challan_details->OrderFrom : ''); ?> 
												<div class="form-group" app-field-wrapper="AccountID">
													<label for="ordfrom" class="control-label">Order From</label>
													<select name="ordfrom" id="ordfrom" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                   
														<option  <?php if($value == 'WEB'){echo "selected";}?> value="WEB">Web</option>
														<option  <?php if($value == 'APP'){echo "selected";}?> value="APP">App</option>                                                                                                                                  
													</select>
												</div>
											</div>  
											
											
											
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->E_Invoice_No : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="E_Invoice_No" class="control-label">E-Invoice Number </label>
													<input type="text"  name="E_Invoice_No" id="E_Invoice_No" value="<?= $value?>" class="form-control">
												</div>
											</div> 
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->ewaybill_no : ''); ?> 
												<div class="form-group" app-field-wrapper="pin">
													<label for="ewayno" class="control-label">E-Way Bill No</label>
													<input type="text"  name="ewayno" id="ewayno" value="<?= $value?>" class="form-control" readonly>
												</div>
											</div> 
											
											 
											<div class="col-md-2">
												<?php $value = (isset($challan_details) ? $challan_details->DeliveryType : ''); ?>
												<div class="form-group" app-field-wrapper="type">
													<label for="type" class="control-label">Delivery Type</label>
													<select id="type" name="type" class="form-control">                                    
														<option <?php if($value == 1){echo "selected";}?> value="1">Pickup</option>
														<option <?php if($value == 2){echo "selected";}?> value="2">Home Delivery</option>                                   
													</select>
												</div>
											</div>   
											<div class="col-md-2" id="shipping-container">
												<div class="form-group" app-field-wrapper="ShippingID">                                 
													<label for="ShippingID">Shipping Address</label>
													<select name="ShippingID" id="ShippingID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
														<option value=""></option>
														<option value="new">New Address</option>  
														<?php
															foreach($challan_details->ShippingList as $ShipTo){
															?>
															<option <?php if($challan_details->ShippingID == $ShipTo['id']){echo "selected";}?> value="<?= $ShipTo['id']?>"><?= $ShipTo['shipping_label']?></option>
															<?php
															}
														?>
														
													</select>                                                          
												</div>
											</div>
											<!--<div class="clearfix"></div>-->
											<div class="col-md-2 ShippingField"> 
												<div class="form-group">
													<label for="ShippingHouse" class="control-label">House/Building No</label>
													<input type="text"  name="ShippingHouse" id="ShippingHouse" value="" class="form-control">
												</div>
											</div> 
											<div class="col-md-2 ShippingField"> 
												<div class="form-group">
													<label for="ShippingStreet" class="control-label">Street</label>
													<input type="text"  name="ShippingStreet" id="ShippingStreet" value="" class="form-control">
												</div>
											</div> 
											<div class="col-md-2 ShippingField"> 
												<div class="form-group">
													<label for="ShippingLocality" class="control-label">Locality</label>
													<input type="text"  name="ShippingLocality" id="ShippingLocality" value="" class="form-control">
												</div>
											</div>  
											<div class="col-md-2 ShippingField"> 
												<div class="form-group">
													<label for="ShippingBlock" class="control-label">Block</label>
													<input type="text"  name="ShippingBlock" id="ShippingBlock" value="" class="form-control">
												</div>
											</div> 
											<div class="col-md-2 ShippingField"> 
												<div class="form-group">                                     
													<label for="ShippingPincode" class="control-label">Pincode</label>
													<input type="text"  name="ShippingPincode" id="ShippingPincode" value="" class="form-control">
												</div>
											</div> 
											<div class="col-md-2 ShippingField">
												<div class="form-group">                                      
													<label for="ShippingState">State</label>
													<select name="ShippingState" id="ShippingState" class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">
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
											
											<div class="col-md-2 ShippingField" >
												<div class="form-group">
													<label for="ShippingCity" class="control-label">City</label>
													<select name="ShippingCity" id="ShippingCity" class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">                                
														<option value=""></option> 
														      
													</select>
												</div>
											</div>
											
										</div>
										
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body mtop10">
						<div class="row col-md-12">
							<p class="bold p_style"><?php echo _l('Delivery Order Detail'); ?></p>
							<hr class="hr_style"/>
							<div class="" id="example">
							</div>
							<?php echo form_hidden('pur_order_detail'); ?>
							
							<div class="col-md-12 ">
								<table class="table">
									<tbody>
										<tr id="total_td">
											
											<td>
												<label for="total_qty_in_mt">Total Qty</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="<?php echo $challan_details->TotalOrderQty; ?>">
											</td>
											<td>
												<label for="total_amt_in_mt">SubTotal</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="<?php echo $challan_details->SaleAmt;?>">
											</td>
											<td>
												<label for="total_disc_in_mt">Discount Amt</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="<?php echo $challan_details->DiscAmt;?>">
											</td>
											<td>
												<label  for="Total_value">Taxable Amt</label>  
												<input  type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="<?php echo $challan_details->taxable_amt;?>" >
											</td>  
											
											<td>  
												<label  for="total_cgst_amt">CGST Amt</label>  
												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="<?php echo $challan_details->cgstamt;?>">
											</td>
											<td>  
												<label  for="total_sgst_amt">SGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $challan_details->sgstamt;?>">
											</td>
											<td>  
												<label  for="total_igst_amt">IGST Amt</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="<?php echo $challan_details->igstamt;?>">
											</td>
											<?php 
											    $difference = $challan_details->BillAmt - $challan_details->RndAmt;
											?>
											<td>  
												<label  for="total_roundoff_amt">RoundOff Amt</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="<?php echo $difference;?>">
											</td>                            
											
											<td>  
												<label  for="netpayableamt">Invoice Amt</label> 
												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="<?php echo $challan_details->BillAmt;?>">
											</td>                    
										</tr>
									</tbody>
								</table>
							</div> 
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body bottom-transaction">
								
								<div id="vendor_data">
									
								</div>
								
								<div class="btn-bottom-toolbar text-right" style="width: 100%; display: flex; justify-content: flex-end; align-items: center;">
									
									<div class="col-md-1" style="margin-left: 0px; margin-right: -30px;">
										<a href="#" class="btn btn-default reset-new-order" id="resetbutton">Reset</a>						
									</div>	
									
									<div class="col-md-1" style="margin-left: 10px;">
										<a href="#" class="btn btn-warning edit-new-order">View List</a>
									</div>
									<?php if (has_permission_new('DeliveryInvoice', '', 'create')){
									?>	
									<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
										<?php echo _l('submit'); ?>
									</button>
									<?php
									}
									?>	
									<?php if (has_permission_new('DeliveryInvoice', '', 'edit')){
										$value = (isset($challan_details) ? $challan_details->OrderStatus : '');
										if(isset($challan_details) && $value == 'F' && !empty($challan_details->ChallanID)){
										?>	
										<button type="button" class="mleft10 pull-right btn btn-success InvoicePrint"><i class="fa fa-eye"></i> Invoice Print </button>
										<button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit hidden"> UPDATE</button>
										<?php
										}
									}
									?>	
									
								   <?php
								        if (has_permission_new('DeliveryInvoice', '', 'edit') && !empty($challan_details) && empty($challan_details->ewaybill_no) && $challan_details->DeliveryType == "2"){
								   ?>
								    <button type="button"  class="mleft10 pull-right btn btn-primary EWayBill"><i class="fa fa-spinner fa-spin EWayBillSpinner" style="display:none" ></i> Generate E-Way Bill </button>
								  <?php 
								        }
								  ?>
								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
				
			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>
<style>
	/*    @media (min-width: 768px)*/ 
	/*        .modal-xl {*/
	/*    width: 90%;*/
	/*    max-width: 1230px;*/
	/*}*/
</style>
<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delivery Order List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
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
						<input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search.." title="Type in a name" style="float: right;">
					</div>
					<div class="col-md-12">
						<div class="table_purchase_report">
							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
								<thead>
									<tr style="display:none;">
										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
									</tr>
									<tr>
										<th style="width:8% ">ChallanID</th>
										<th style="width:8% ">Challan Date</th>
										<th style="width:8% ">ChallanID</th>
										<th style="width:15% text-align:left;">Sale Party</th>
										<th style="width:15% text-align:left;">Challan Status</th> 								
										<th style="width:5% text-align:left;">Sale Amt</th>
										<th style="width:3% text-align:left;">Disc Amt</th>
										<th style="width:5% text-align:left;">CGST Amt</th>
										<th style="width:5% text-align:left;">SGST Amt</th>
										<th style="width:5% text-align:left;">IGST Amt</th>                                   
										<th style="width:5% text-align:left;">Inv. Amt</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">Loading.....</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<style>
    .table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
	.table_purchase_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table_purchase_report table  { border-collapse: collapse; width: 100%; }
	.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.table_purchase_report th     { background: #50607b;color: #fff !important; }
	
	
	#table_purchase_report tr:hover {
    background-color: #ccc;
	}
	
	#table_purchase_report td:hover {
    cursor: pointer;
	}
</style>

<script type="text/javascript">

	//For E WayBill
    $('.EWayBill').on('click', function() 
    {
		var $btn = $(this);
        var ChallanID = $('#ChallanID').val(); 
        
        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/generateEwayBill";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ChallanID: ChallanID},
            dataType:'json',
			beforeSend: function () {
				$('.EWayBillSpinner').show();
				$btn.prop('disabled', true);
			},
			complete: function () {
				$btn.prop('disabled', false);
				$('.EWayBillSpinner').hide();
			},
            success: function(data) {
				if (data.Status === true) {
				    // 	alert_float('success', 'E-Way Bill generated Successfully');
				    var msg = data.Message;
					if(confirm(msg)){
				        window.location.reload();
					}
				}else {
					var msg = data.Message;
					if(confirm(msg)){
				// 		window.location.reload();
					}
				}
			}
		})
	})
	
	$('.InvoicePrint').on('click', function() {
        var OrderID = $('#OrderID').val();
        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/GetSaleOrderDetails";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {OrderID: OrderID},
            dataType:'json',
            success: function(data) {
                var Link = '<?php echo admin_url(); ?>KirtiOneOrder/B2BSaleInvoicePdf/'+OrderID+'?output_type=I';
				window.open(Link,'_blank');
			}
		})
	})
    /*$('.InvoicePrint').on('click', function() {
        var ChallanID = $('#ChallanID').val();
        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/GetTaxableTransaction";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ChallanID: ChallanID},
            dataType:'json',
            success: function(data) {
                var Link = '<?php echo admin_url(); ?>KirtiOneOrder/invoicepdf/'+ChallanID+'?output_type=I';
                var NotMAtch = 0;
                for(var count = 0; count < data.length; count++)
                {
                    if(data[count].PlantID !== "3"){
                        if(data[count].gstno == null || data[count].gstno == ''){
							
							}else{
                            if(data[count].irn == null && data[count].BillAmt > 0){
                                NotMAtch++;
							}
						}
					}
				}
				window.open(Link,'_blank');
			}
		})
	})*/

	function printPage(){
		
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;color:#fff;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		var print_data = stylesheet+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () 
    {
		var selectElement = document.getElementById('Effecton');
		var Delivery_type = $("#type").val();
		if(Delivery_type == 1)
		{
			$("#shipping-container").hide();
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
    $(document).ready(function() 
    {  
		
		$("#ShippingID").change(function() 
        {
			var ShippingID = $("#ShippingID").val();
			if(ShippingID == "new")
			{
				$(".ShippingField").show();
			}
			else
			{
				$(".ShippingField").hide();
			}
		});
		$("#ShippingID").trigger('change'); 
		$('#ShippingState').on('change', function() {
			var StateID = $(this).val();
			var url = "<?php echo base_url(); ?>admin/clients/GetCity";
			jQuery.ajax({
				type: 'POST',
				url:url,
				data: {StateID: StateID},
				dataType:'json',
				success: function(data) {
					$("#ShippingCity").find('option').remove();
					$("#ShippingCity").selectpicker("refresh");
					for (var i = 0; i < data.length; i++) {
						$("#ShippingCity").append(new Option(data[i].city_name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});
		
        $("#type").change(function() 
        {          
            var Delivery_type = $("#type").val();
            if(Delivery_type == 2)
            {
                $("#shipping-container").show();
			}
            else if(Delivery_type == 1)
            {
                $("#shipping-container").hide();
                $(".ShippingField").hide();
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
		       
	});
</script>
<script type="text/javascript" language="javascript" >
	function GetChallan(VenId)
	{
		var dataObject2 = []; 
		hot.loadData(dataObject2);
		
		if(VenId == "" || VenId == null)
		{
			var optionsHtml = '<option value="">None Selected</option>';
			$('#ChallanID').html(optionsHtml);
			$('.selectpicker').selectpicker('refresh');
			
			var optionsHtml2 = '<option value="">None Selected</option>';
				optionsHtml2 += '<option value="new">New Address</option>';
			
			$('#ShippingID').html(optionsHtml2);
			$('.selectpicker').selectpicker('refresh');
			
		}
		else
		{
			
			$.ajax({
				url:"<?php echo admin_url(); ?>KirtiOneOrder/GetApprovedChallanByVendor",
				dataType:"JSON",
				method:"POST",
				data:{VenId:VenId},
				
				success:function(rtndata){
					var optionsHtml = '<option value="">None Selected</option>';
					
					for (var i = 0; i < rtndata.length; i++) {
						optionsHtml += '<option value="' + rtndata[i].ChallanID + '">' + rtndata[i].ChallanID + '</option>';
					}
					$('#ChallanID').html(optionsHtml);
					$('.selectpicker').selectpicker('refresh');
					
					
				}
			});
		}
	}
	
	function GetChallanDetails(ChallanID){
		if(ChallanID == '')
		{
			var dataObject2 = []; 
			hot.loadData(dataObject2);
			
			$('#CenterName').val('');
			$('#CenterState').val('');
			
			$('input[name="total_qty_in_mt"]').val('');
			$('input[name="total_amt_in_mt"]').val('');
			$('input[name="total_disc_in_mt"]').val('');
			$('input[name="total_cgst_amt"]').val('');
			$('input[name="total_sgst_amt"]').val('');
			$('input[name="total_igst_amt"]').val('');
			$('input[name="total_igst_amt"]').val('');
			$('input[name="total_roundoff_amt"]').val('');
			$('input[name="netpayableamt"]').val('');
			$('input[name="Total_value"]').val('');
		}
		else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>KirtiOneOrder/GetChallanItemData",
				dataType:"JSON",
				method:"POST",
				data:{ChallanID:ChallanID},
				
				success:function(rtndata){
				    $('#TransID').val(rtndata.ChallanData.SalesID);
				    $('#OrderID').val(rtndata.ChallanData.OrderID);
				    $('#CenterID').val(rtndata.ChallanData.CenterID);
					$('#CenterName').val(rtndata.ChallanData.CenterName);
					$('#CenterState').val(rtndata.ChallanData.CenterStateShort);
					$('#gst').val(rtndata.ChallanData.GSTIN);
					$('#PartyGSTIN').val(rtndata.ChallanData.GSTIN);
					if(rtndata.ChallanData.GSTIN != null && rtndata.ChallanData.GSTIN != ''){
						$('#gst').prop('readonly', true);
					}else{
						$('#gst').prop('readonly', false);
					}
					var dataObject2 = [];   
					if(rtndata.historytbl.length > 0)
					{
						hot.loadData(rtndata.historytbl);
					}
					else
					{
						hot.loadData(dataObject2);
					}
					
					setTimeout(function () {
						if (hot.countRows() > 0) {
							const row = 0; // First row
							const col = 8; // OrderQty column
							const currentValue = hot.getDataAtCell(row, col);
							
							// This will re-trigger afterChange hook
							hot.setDataAtCell(row, col, currentValue);
						}
					}, 2000);
				}
			});
			
		}
	}
	
	$('#Freight_AMT, #Other_amt').on('keypress', function (event) {
		var key = event.which;
		var input = $(this).val();
		var char = String.fromCharCode(key);
		
		// Allow control keys (e.g. backspace)
		if (event.ctrlKey || event.metaKey || key < 32) {
			return;
		}
		
		// Allow one '-' only at the beginning
		if (char === '-') {
			if (input.indexOf('-') !== -1 || $(this)[0].selectionStart !== 0) {
				event.preventDefault();
			}
			return;
		}
		
		// Allow only one dot
		if (char === '.') {
			if (input.indexOf('.') !== -1) {
				event.preventDefault();
			}
			return;
		}
		
		// Allow only digits
		if (char < '0' || char > '9') {
			event.preventDefault();
		}
		
		// Optional: limit to 2 decimal places
		if (input.indexOf('.') !== -1) {
			var decimalPart = input.split('.')[1];
			if (decimalPart && decimalPart.length >= 2 && $(this)[0].selectionStart > input.indexOf('.')) {
				event.preventDefault();
			}
		}
	});
	
	$(document).ready(function(){ 
		var url = window.location.href;
		var regex = /\/AddEditDeliveryInvoice\/([^\/?#]+)/;	
		if (url.match(regex)) {
			$('#updatebtn').removeClass('hidden');
			$('#printbtn').removeClass('hidden');
			$('#cancelbtn').removeClass('hidden');
			$('#savebtn').addClass('hidden');
			} else {		
			$('#updatebtn').addClass('hidden');
			$('#printbtn').addClass('hidden');
			$('#cancelbtn').addClass('hidden');
			$('#savebtn').removeClass('hidden');
		}
		
		function load_data(from_date,to_date)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>KirtiOneOrder/load_data_for_delivery_invoice",            
				method:"POST",
				data:{from_date:from_date, to_date:to_date},
				beforeSend: function () {
					$('#searchh2').css('display','block');
					$('.table_purchase_report tbody').css('display','none');
				},
				complete: function () {
					$('.table_purchase_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					$('.table_purchase_report tbody').html(data);
				}
			});
		}
		
		$('.edit-new-order').on('click', function(){
			$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
			$('#transfer-modal').modal('show');
			
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();	    
			load_data(from_date,to_date);
		});	
		
		
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();	    
			var msg = "Sales Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date);
		});
		
		$('.cancel-new-order').on('click', function()
		{
			var url = window.location.href;		
			var segments = url.split('/');
			var poId = segments[segments.length - 1].split('#')[0]; 		
			
			var userConfirmed = confirm("Are you sure you want to cancel the order?");      
			
			if (userConfirmed) 
			{              
				$.ajax({
					
					url: "<?php echo admin_url(); ?>PurchaseMaster/CancelOrderWisePOItems", 
					
					type: 'POST', 
					
					data: {poId:poId}, 
					
					dataType: 'json',
					
					success: function(response) 
					{                 
						if (response.success) 
						{                
							alert_float('success', 'Order Cancelled Successfully...');  
							
							$("#ordstat").val("C").selectpicker('refresh');
							
							hot.getData().forEach(function(rowData, rowIndex) {                           
								if (rowData && rowData[8] != undefined && rowData[15] != undefined) {
									hot.setDataAtCell(rowIndex, 8, 0.00); 
									hot.setDataAtCell(rowIndex, 10, 0.00); 
									hot.setDataAtCell(rowIndex, 12, 0.00); 
									hot.setDataAtCell(rowIndex, 13, 0.00); 
									hot.setDataAtCell(rowIndex, 14, 0.00); 
									hot.setDataAtCell(rowIndex, 15, 0.00); 
								}
							});
							hot.render();                       
							} else {     
							alert_float('warning', 'Something went wrong...');    
						}
					},
					error: function(xhr, status, error) {   
						$('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
					}
				});  
			} 
			else {               
				console.log("Order cancellation was cancelled by the user.");
			}        		
		});	
	});
</script>

<script>
    $(document).ready(function() 
	{
        $('#resetbutton').on('click', function(e) {
            e.preventDefault();  
			window.location.href = '<?php echo admin_url(); ?>KirtiOneOrder/AddEditDeliveryInvoice';  
		});		
		
	});
</script>

<script type="text/javascript" language="javascript" >
	$(document).ready(function()
	{	
	    
		$('#vendor').on('change', function() {
			$('#vendor_value').val($(this).val());
			var selectedPartyId = $('#vendor option:selected').data('partyid');
			$('#state_value').val(selectedPartyId);
		});
		
		$('#state').on('change', function() {
			if (!$(this).prop('disabled')) {
				$('#state_value').val($(this).val());
			}
		});
		
		$('#vendor').on('change', function(){
			var vendor_id = $(this).val();
			$.ajax({
				url:"<?php echo admin_url(); ?>KirtiOneOrder/GetVendorDetails",
				dataType:"JSON",
				method:"POST",
				data:{vendor_id:vendor_id},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data)
				{	   
					var optionsHtml = '<option value="">None Selected</option>';
					var optionsHtml = '<option value="new">New Address</option>';
					
					for (var i = 0; i < data.ShippingData.length; i++) {
						optionsHtml += '<option value="' + data.ShippingData[i].id + '">' + data.ShippingData[i].shipping_label + '</option>';
					}
					$('#ShippingID').html(optionsHtml);
					$('.selectpicker').selectpicker('refresh');
					
					$('#mobile_no').val(data.phonenumber); 
					$('#gst').val(data.gstin);
					if(!data.gstin){
						document.getElementById('gst').removeAttribute('readonly');
					}else{
						document.getElementById('gst').readOnly = true;
					}
					if (data.state_name && data.state)
                    {
                        var stateSelect = document.getElementById('state');
                        var found = false;
                    
                        for (var i = 0; i < stateSelect.options.length; i++)
                        {
                            if (stateSelect.options[i].text.trim().toUpperCase() === data.state_name.trim().toUpperCase())
                            {
                                // Set value (MH)
                                stateSelect.value = data.state;
                    
                                // Disable dropdown
                                stateSelect.setAttribute('disabled', 'disabled');
                    
                                // Refresh bootstrap select
                                $(stateSelect).selectpicker('refresh');
                    
                                // Remove old hidden if exists
                                $('#hidden_state').remove();
                    
                                // Add hidden input to POST value
                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'hidden_state',
                                    name: 'state',
                                    value: data.state
                                }).appendTo('form');
                    
                                found = true;
                                break;
                            }
                        }
                    
                        if (!found)
                        {
                            stateSelect.value = "";
                            stateSelect.removeAttribute('disabled');
                            $(stateSelect).selectpicker('refresh');
                            $('#hidden_state').remove();
                        }
                    }
                    else
                    {
                        var stateSelect = document.getElementById('state');
                        stateSelect.value = "";
                        stateSelect.removeAttribute('disabled');
                        $(stateSelect).selectpicker('refresh');
                        $('#hidden_state').remove();
                    }				
				}
			});
		});
		
	});
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table_purchase_report");
        tr = table.getElementsByTagName("tr");
		
        for (i = 2; i < tr.length; i++) {
            var tdArray = tr[i].getElementsByTagName("td");
            var rowContainsSearchTerm = false;
            for (var j = 0; j < tdArray.length; j++) {
                td = tdArray[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsSearchTerm = true;
                        break;
					}
				}
			}
            if (rowContainsSearchTerm) {
				
                tr[i].style.display = "";
				} else {
                tr[i].style.display = "none";
			}
		}
	}
</script>
<script>
    $('.add-new-transfer').on('click', function(){
		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
		$('#transfer-modal').modal('show');
		init_journal_entry_table();
	});
</script>
<script>
    $(document).ready(function()
    {
        var maxEndDate = new Date('Y/m/d');
        var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
        var year = "20"+fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);
        if(cur_y > fin_y){
            var year2 = parseInt(fin_y) + parseInt(1);
            var year2_new = "20"+year2;
            var e_dat = new Date(year2_new+'/03/31');
            var maxEndDate_new = e_dat;
			}else{
            var maxEndDate_new = maxEndDate;
		}
        var minStartDate = new Date(year, 03);
		
        $('#prd_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false
		});
	});
</script> 
<script>
    $(document).ready(function()
    {
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
        $('#from_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false
		});
		
        $('#to_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false,
            showOtherMonths: false,
            pickTime: false,
			orientation: "left",
		});    
	});
</script>
</html>

<?php require 'kirtione_delivery_invoice_js.php';?>

