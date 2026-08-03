<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>



<div id="wrapper">

	<div class="content">      

		<div class="row">

			<?php

				echo form_open($this->uri->uri_string(),array('id'=>'sale_order-form','class'=>'_transaction_form'));

			?>

            <div class="col-md-12">

                <div class="panel_s">

					

					<div class="panel-body">

						<div class="_buttons"> 

							<?php

								$fy = $this->session->userdata('finacial_year');

								if(!empty($OrderDetails)){

									$NextOrderId = substr($OrderDetails->OrderID,5,11);

									$ChallanID = $OrderDetails->ChallanID;

									$OrderID = $OrderDetails->OrderID;

									$fy = $OrderDetails->FY;

									}else{

									$NextOrderId = $NextOrderId;

									$ChallanID = '';

									$OrderID = '';

								}

								$irn = $OrderDetails->irn;

							?>

							

							<?php	

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

							<div class="col-md-2">

								<div class="form-group">

									<label for="orderid"> <small class="req text-danger">* </small>OrderID</label>

									<div class="input-group">

										<span class="input-group-addon">ORD<span id="prefix_year"><?php echo $fy;?></span></span>

										<input type="text" name="orderid" id="orderid" class="form-control receiptsid" value="<?php echo $NextOrderId;?>" data-isedit="" data-original-number="">

										<input type="hidden" name="ChallanID" id="ChallanID" class="form-control" value="<?php echo $ChallanID;?>" data-isedit="" data-original-number="">

										<input type="hidden" name="HideOrderID" id="HideOrderID" class="form-control" value="<?php echo $OrderID;?>" >

									</div>

								</div>

							</div>                

							<?php         

								if(!empty($OrderDetails)){

									$current_date = substr(_d($OrderDetails->Transdate),0,10);

									

									}else{

									$current_date = $to_date;

								}

								

								$attr = array('readonly'=>'readonly');

							?>  

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="posted_date" style="<?= is_admin() ? '' : 'pointer-events: none !important;'?>">

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

									<small class="req text-danger"> </small>

									<label for="centername" class="control-label">Center Name</label>

									<select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value=""></option>   

										<?php

											foreach($centermaster as $center) 

											{

											?>

											<option <?php if($OrderDetails->CenterID == $center['CenterID']){echo "selected";}?> value="<?= $center['CenterID']?>" data-statsid="<?= $center['state']?>"><?= $center['CenterName']?></option>

											<?php

											} 

										?>                                                                                                                                    

									</select>

								</div>

							</div>  

							

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="pin">

									<label for="ledgerbal" class="control-label">Closing Balance</label>

									<input type="text"  name="ledgerbal" id="ledgerbal" class="form-control" value="<?php if(isset($OrderDetails->ClosingBalance)){echo $OrderDetails->ClosingBalance;}?>" readonly>

								</div>

							</div> 

							

							<div class="col-md-2">                            

								<div class="form-group" app-field-wrapper="AccountID">

									<small class="req text-danger"> </small>

									<label for="ordstat" class="control-label">Order Status</label>

									<select name="ordstat" id="ordstat" disabled class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option <?php if($OrderDetails->OrderStatus == "F"){echo 'selected';}?> value="F">Completed</option>

										<option <?php if($OrderDetails->OrderStatus == "O"){echo 'selected';}?> value="O">Pending</option>

										<!--<option <?php if($OrderDetails->OrderStatus == "C"){echo 'selected';}?> value="C">Cancel</option>-->

									</select>

								</div>

							</div>  

							<!--<div class="col-md-2">

								<div class="form-group" app-field-wrapper="pin">

									<label for="rewards" class="control-label">Reward Point Balance</label>

									<input type="text"  name="rewards" id="rewards" class="form-control" readonly>

								</div>

							</div>   --> 

							<div class="clearfix"></div>

							<div class="col-md-4">                            

								<div class="form-group" app-field-wrapper="AccountID">

									<small class="req text-danger"> </small>

									<label for="AccountID" class="control-label">Select Party</label>
									<style>
										.ajax-search-box{
												position:relative;
												width:100%;
										}
										.search-results{
												position:absolute;
												top:100%;
												left:0;
												width:100%;
												background:#fff;
												border:1px solid #ddd;
												max-height:250px;
												overflow-y:auto;
												display:none;
												z-index:9999;
										}
										.search-item{
												padding:10px;
												cursor:pointer;
												border-bottom:1px solid #eee;
										}
										.search-item:hover{
												background:#f5f5f5;
										}
									</style>
									<div class="ajax-search-box" data-url="<?= admin_url('KirtiOneOrder/get_party'); ?>">
											<input type="text" class="search-input form-control" placeholder="Search name... or Type 'New'" value="<?= (isset($OrderDetails->company) ? $OrderDetails->company : ''); ?>">
											<input type="hidden" class="selected-id" id="AccountID" name="AccountID" value="<?= (isset($OrderDetails->AccountID) ? $OrderDetails->AccountID : ''); ?>">
											<div class="search-results"></div>
									</div>


									<!-- <select name="AccountID" id="AccountID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value=""></option> 

										<?php

											// if(empty($OrderDetails)){

											?>

											<option value="new">New Party</option> 

											<?php

											// }

											// foreach($clients as $value) 

											// {

											?>

											<option <?php // if($OrderDetails->AccountID == $value['AccountID']){echo "selected";}?> value="<?php // $value['AccountID'];?>"><?php // $value['company']."(".$value['AccountID'].")";?></option>';

											<?php

											// } 

										?>                                                                 

									</select> -->

								</div>

							</div>

							

							<div class="col-md-2" id="party_name">

								<div class="form-group" app-field-wrapper="partyname">

									<small class="req text-danger"> </small>

									<label for="partyname" class="control-label">Party Name</label>

									<input type="text" id="partyname" name="partyname" class="form-control" value="">

								</div>

							</div>   

							

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="phonenumber">

									<small class="req text-danger"> </small>

									<label for="phonenumber" class="control-label">Mobile Number</label>

									<input type="text" id="phonenumber" name="phonenumber" class="form-control" value="<?php if(isset($OrderDetails->phonenumber)){echo $OrderDetails->phonenumber;}?>" maxlength="10" minlength="10" onkeypress="return isNumber(event)" readonly>

									<input type="hidden" id="partystate" name="partystate" class="form-control" value="<?php if(isset($OrderDetails->state)){echo $OrderDetails->state;}?>">

								</div>

							</div>    

							

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="villagename">    

									<label for="villagename" class="control-label">Village Name</label>

									<input type="text" id="villagename" name="villagename" class="form-control" value="<?php if(isset($OrderDetails->VillageName)){echo $OrderDetails->VillageName;}?>">

								</div>

							</div>   

							

							<div class="col-md-2" id="billstate-container">

								<div class="form-group" app-field-wrapper="State">      

									<small class="req text-danger"> </small>                                

									<label for="billstate">Billing State</label>

									<select name="billstate" id="billstateid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value=""></option> 

										<?php

											foreach($states as $statelist) 

											{

											?>

											<option <?php if($OrderDetails->billstate == $statelist['short_name']){echo "selected";}?> value="<?= $statelist['short_name'];?>"><?= $statelist['state_name'];?></option>';

											<?php

											} 

										?>                                                                      

									</select>                                                          

								</div>

							</div>                

							

							<div class="clearfix"></div>

							

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="CategoryType">

									<small class="req text-danger"> </small>

									<label for="CategoryType" class="control-label">Category Type</label>

									<select id="CategoryType" name="CategoryType" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php if(isset($OrderDetails->CategoryType)){echo "disabled";}?>>

										<option value=""></option>

										<option <?php if($OrderDetails->CategoryType == 'Grocery'){echo "Selected";}?> value="Grocery">Grocery</option>

										<option <?php if($OrderDetails->CategoryType == 'Non Grocery'){echo "Selected";}?> value="Non Grocery">Non Grocery</option>                                   

									</select>

								</div>

							</div> 

							

							<div class="col-md-2">                            

								<div class="form-group" app-field-wrapper="ordtype">

									<small class="req text-danger"> </small>

									<label for="ordtype" class="control-label">Order Type</label>

									<select name="ordtype" id="ordtype" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                   

										<option <?php if($OrderDetails->OrderPaymentType == "1"){echo 'selected';}?> value="1">Cash Order</option>

										<option <?php if($OrderDetails->OrderPaymentType == "2"){echo 'selected';}?> value="2">Credit Order</option>

									</select>

								</div>

							</div>   

							<div class="CashOrderDiv" id="CashOrderDiv">

							    <div class="col-md-2">								

    								<div class="form-group">

    									<?php $value = (isset($OrderDetails) ? $OrderDetails->CashAmt : '0'); ?>	

    									<label for="CashAmt" class="control-label">Cash Amt</label>

    									<input type="text" name="CashAmt" id="CashAmt" class="form-control" value = "<?php echo $value; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->InvoiceNo; ?>">

    									

    								</div>

    							</div>

    							<div class="col-md-2">								

    								<div class="form-group">

    									<?php $value = (isset($OrderDetails) ? $OrderDetails->OnlineAmt : '0'); ?>	

    									<label for="OnlineAmt" class="control-label">Online Amt</label>

    									<input type="text" name="OnlineAmt" id="OnlineAmt" class="form-control" value = "<?php echo $value; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->InvoiceNo; ?>">

    									

    								</div>

    							</div>

    							<div class="col-md-2">								

    								<div class="form-group">

    									<label for="Effecton" class="control-label">Online Amt Effect On</label>

    									<select name="Effecton" id="Effecton" class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">

    										<option value=""></option>

    										<?php

    											foreach($EffectOn as $val1) 

    											{

    											?>

    											<option <?php if($OrderDetails->Effecton == $val1['AccountID']){echo "selected";}?> value="<?= $val1['AccountID'];?>"><?= $val1['company'];?></option>

    											<?php

    											} 

    										?>                                                   

    									</select>

    								</div>

    							</div>

    							<div class="col-md-2">								

    								<div class="form-group">

    									<label for="referenceno">Reference No</label>     

    									<input type="text" class="form-control" name="referenceno" id="referenceno" value="<?php if(isset($OrderDetails->RefNo)){echo $OrderDetails->RefNo;}?>"/>

    								</div>

    							</div>

							</div>

							

							<div class="clearfix"></div>

							<div class="col-md-2" id="bill_no">

								<div class="form-group" app-field-wrapper="pin">

									<label for="billno" class="control-label">Bill No</label>

									<input type="text"  name="billno" id="billno" class="form-control" value="<?php if(isset($OrderDetails->BIllNo)){echo $OrderDetails->BIllNo;}?>">

								</div>

							</div> 

							<div class="col-md-2">                            

								<div class="form-group" app-field-wrapper="AccountID">

									<small class="req text-danger"> </small>

									<label for="ordfrom" class="control-label">Order From</label>

									<select name="ordfrom" id="ordfrom" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">                                   

										<option <?php if($OrderDetails->order_type == 'WEB'){echo "selected";}?> value="WEB">Web</option>

										<option <?php if($OrderDetails->order_type == 'APP'){echo "selected";}?> value="APP">App</option>                                                                                                                                  

									</select>

								</div>

							</div>  

							

							

							<div class="col-md-2">							

								<?php $value = (isset($OrderDetails) ? $OrderDetails->EffectOnOtherAmt : ''); ?>	

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

							    <?php $value = (isset($OrderDetails) ? $OrderDetails->OtherAmt : '0'); ?>	

								<div class="form-group" app-field-wrapper="OtherAmt">

									<label for="OtherAmt" class="control-label">Other Amt</label>

									<input type="text" name="OtherAmt" id="OtherAmt" class="form-control" value = "<?php echo $value; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->InvoiceNo; ?>">

								</div>

							</div>

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="FarmerID">

									<label for="FarmerID" class="control-label">Farmer ID</label>

									<input type="text" name="FarmerID" id="FarmerID" class="form-control" value = "<?= $OrderDetails->FarmerID ?? ''; ?>" oninput="this.value = this.value.replace(/ /g,'');">

								</div>

							</div>

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="FarmerAadhaar">

									<label for="FarmerAadhaar" class="control-label">Farmer Aadhaar</label>

									<input type="text" name="FarmerAadhaar" id="FarmerAadhaar" class="form-control" value = "<?= $OrderDetails->FarmerAadhaar ?? ''; ?>" maxlength="12" minlength="12" oninput="this.value = this.value.replace(/[^0-9]/g,'');">

								</div>

							</div>

							

							<div class="col-md-2">

								<div class="form-group" app-field-wrapper="type">

									<small class="req text-danger"> </small>

									<label for="type" class="control-label">Delivery Type</label>

									<select id="type" name="type" class="form-control">                                    

										<option <?php if($OrderDetails->DeliveryType == '1'){echo "selected";}?> value="1">Pickup</option>

										<option <?php if($OrderDetails->DeliveryType == '2'){echo "selected";}?> value="2">Home Delivery</option>                                   

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

											foreach($OrderDetails->ShippingList as $ShipTo){

											?>

											<option <?php if($OrderDetails->ShippingID == $ShipTo['id']){echo "selected";}?> value="<?= $ShipTo['id']?>"><?= $ShipTo['shipping_label']?></option>

											<?php

											}

										?>

										

									</select>                                                          

								</div>

							</div>

							

							<div class="clearfix"></div>

							<div class ="ShippingField">

							    <div class="col-md-2"> 

    								<div class="form-group">

    									<label for="ShippingHouse" class="control-label">House/Building No</label>

    									<input type="text"  name="ShippingHouse" id="ShippingHouse" value="" class="form-control">

    								</div>

    							</div> 

    							<div class="col-md-2"> 

    								<div class="form-group">

    									<label for="ShippingStreet" class="control-label">Street</label>

    									<input type="text"  name="ShippingStreet" id="ShippingStreet" value="" class="form-control">

    								</div>

    							</div> 

    							<div class="col-md-2"> 

    								<div class="form-group">

    									<label for="ShippingLocality" class="control-label">Locality</label>

    									<input type="text"  name="ShippingLocality" id="ShippingLocality" value="" class="form-control">

    								</div>

    							</div>  

    							<div class="col-md-2"> 

    								<div class="form-group">

    									<label for="ShippingBlock" class="control-label">Block</label>

    									<input type="text"  name="ShippingBlock" id="ShippingBlock" value="" class="form-control">

    								</div>

    							</div> 

    							<div class="col-md-2"> 

    								<div class="form-group">                                     

    									<label for="ShippingPincode" class="control-label">Pincode</label>

    									<input type="text"  name="ShippingPincode" id="ShippingPincode" value="" class="form-control">

    								</div>

    							</div> 

    							<div class="col-md-2">

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

    							

    							<div class="col-md-2" >

    								<div class="form-group">

    									<label for="ShippingCity" class="control-label">City</label>

    									<select name="ShippingCity" id="ShippingCity" class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">                                

    										<option value=""></option> 

    										

    									</select>

    								</div>

    							</div>

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

                <?php

                    if($this->session->userdata('username') != "PAYU"){

                ?>

					<a href="#" id="viewlist" class="btn btn-warning edit-new-order mleft10" style="background-color: #ff6f00; color: white;">View List </a>

				<?php } ?>

					<button type="button"  class="btn btn-default" id="resetbutton"> Reset</button>

					<?php  

						if(!empty($OrderDetails)){

							if (has_permission_new('OrderMaster', '', 'edit')){

							?>

							<button type="submit" id="updatebtn" class="btn-tr update_detail btn btn-info mleft10 ">Update</button>

							<?php

						        if ($OrderDetails->DeliveryType == 2 && empty($OrderDetails->ewaybill_no)){

							?>

							    <button type="button"  class="mleft10 pull-right btn btn-primary EWayBill"><i class="fa fa-spinner fa-spin EWayBillSpinner" style="display:none" ></i> Generate E-Way Bill </button>

							<?php 

							    }

							?>

							<!--<button type="button" id="cancelbtn" class="btn-tr cancel_detail btn btn-danger mleft10 ">Cancel</button> -->

							<?php if($irn != 'Y'){ ?>

								<!--<button type="button"  class="mleft10 pull-right btn btn-primary EInvoice"> Generate E-Invoice </button>-->

								<?php

								} 

							}

						?>

						

						

						<button type="button" class="mleft10 pull-right btn btn-success InvoicePrint"><i class="fa fa-eye"></i> Invoice Print </button>

						<?php

						}else{

							if (has_permission_new('OrderMaster', '', 'create')){

							?>

							<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10" >Save & Proceed</button> 

							<?php

							}

						}

					?>  	

					

				</div>       

				<?php echo form_close(); ?>

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

        								<div class="form-group" app-field-wrapper="CategoryTypeFilter">

        									<small class="req text-danger"> </small>

        									<label for="CategoryTypeFilter" class="control-label">Category Type</label>

        									<select id="CategoryTypeFilter" name="CategoryTypeFilter" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php if(isset($OrderDetails->CategoryType)){echo "disabled";}?>>

        										<option value="">All</option>

        										<option  value="Grocery">Grocery</option>

        										<option value="Non Grocery">Non Grocery</option>                                   

        									</select>

        								</div>

        							</div> 

        							<!--<div class="clearfix"></div>-->

									<div class="col-md-3">

									    <br>

										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>

									</div>

									<!--<div class="col-md-4">

										<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">

									</div>-->

								</div>

								<div class="table-Item_List tableFixHead2">

									<table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">

										<thead>

											<tr style="display:none;">

												<td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>

											</tr>

											<tr>

												<th id="sl" style="text-align:left; width: 5%;">Sr. No.</th>                                                        

												<th style="text-align:left; width: 10%;">OrderID</th>

												<th style="text-align:left; width: 10%;">Order Date</th> 

												<th style="text-align:left; width: 15%;">Customer Name</th>

												<th style="text-align:left; width: 15%;">Center Name</th>   

												<th style="text-align:left; width: 15%;">Bill No</th>  

												<th style="text-align:left; width: 10%;">Order Amt</th> 

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

<?php require 'KirtiOneOrder_js.php';?>

<script>
	let currentRequest = null;

	function debounce(callback, delay = 300)
	{
			let timer;

			return function ()
			{
					let context = this;
					let args = arguments;

					clearTimeout(timer);

					timer = setTimeout(function ()
					{
							callback.apply(context, args);
					}, delay);
			};
	}

	$(document).on(
			'keyup',
			'.search-input',
			debounce(function ()
			{
					let $input = $(this);
					let keyword = $.trim($input.val());

					let $container = $input.closest('.ajax-search-box');
					let $results = $container.find('.search-results');
					let url = $container.data('url');

					let html = '<div class="search-item" data-id="new" data-name="Create New Party">Create New Party </div>';
					$results.html(html).show();

					if (keyword.length < 2)
					{
							// $results.empty().hide();
							return;
					}

					if (currentRequest)
					{
							currentRequest.abort();
					}

					currentRequest = $.ajax({
							url: url,
							type: 'GET',
							dataType: 'json',
							data: {
									keyword: keyword
							},
							success: function (response)
							{
									$.each(response, function (index, item)
									{
											html += `
													<div class="search-item"
															data-id="${item.AccountID}"
															data-name="${item.company}">
															${item.company} - ${item.AccountID}
													</div>
											`;
									});

									$results.html(html).show();
							},
							error: function ()
							{
									$results.empty().hide();
							}
					});

			}, 300)
	);

	$(document).on('click', '.search-item', function (){
			let $item = $(this);
			let $container = $item.closest('.ajax-search-box');
			$container.find('.search-input').val($item.data('name'));
			$container.find('.selected-id').val($item.data('id')).trigger('change');
			$container.find('.search-results').hide();
	});

	$(document).on('click', function (e){
			if (!$(e.target).closest('.ajax-search-box').length){
					$('.search-results').hide();
			}
	});
</script>

<script type="text/javascript">

    $('.EWayBill').on('click', function(){

		var $btn = $(this);

        var OrderID = $('#HideOrderID').val(); 

        

        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/generateOrderEwayBill";

        jQuery.ajax({

            type: 'POST',

            url:url,

            data: {OrderID: OrderID},

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

		});

	});

	

    $('#resetbutton').on('click', function(e) {

        e.preventDefault();  

		window.location.href = '<?php echo admin_url();?>KirtiOneOrder';  

	});	

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

                        alert('This mobile number is already registered. You can select party from list or use another mobile number.');

                        $('#phonenumber').val("");

                        //$('.selectpicker').selectpicker('refresh');

                        $('#phonenumber').focus();

					}

				}

			});   

		}

	});

	$('.InvoicePrint').on('click', function() {

        var OrderID = $('#HideOrderID').val();

        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/GetSaleOrderDetails";

        jQuery.ajax({

            type: 'POST',

            url:url,

            data: {OrderID: OrderID},

            dataType:'json',

            success: function(data) {

                var Link = '<?php echo admin_url(); ?>KirtiOneOrder/DirectSalePdf/'+OrderID+'?output_type=I';

				window.open(Link,'_blank');

			}

		})

	})

	

	

	//For E Invoicing

    $('.EInvoice').on('click', function() {

        var ChallanID = $('#ChallanID').val();

        var url = "<?php echo base_url(); ?>admin/KirtiOneOrder/generateEInvoice";

        jQuery.ajax({

            type: 'POST',

            url:url,

            data: {ChallanID: ChallanID},

            dataType:'json',

            success: function(data) {

                window.location.reload();

			}

		})

	})

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

    function refreshTable() 

    {

		var from_date = $("#from_date").val();

		var to_date = $("#to_date").val();

		var CategoryTypeFilter = $("#CategoryTypeFilter").val();

        $.ajax({

            url:  "<?php echo admin_url(); ?>KirtiOneOrder/Order_table_data",

            method:"POST",

			data:{from_date:from_date, to_date:to_date,CategoryTypeFilter:CategoryTypeFilter},

            //dataType: "json", 

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

				$("#table_Item_List tbody").html(data); 

				/*tableBody.empty();                         

                $.each(data, function(index, value) 

                {

					var redirectUrl = "<?php echo admin_url(); ?>KirtiOneOrder/AddEditSaleOrder/"+ value.OrderID;     

                    var OrderStat;

                    if (value.OrderStatus == "O") {

                        OrderStat = "Pending";

						} else if (value.OrderStatus == "F") {

                        OrderStat = "Completed";

						} else if (value.OrderStatus == "C") {

                        OrderStat = "Cancelled";

					}

					

                    var newRow = $("<tr class='get_ItemID' data-id='" + value.OrderID + "' onclick=\"location.href='" + redirectUrl + "';\">");

					

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

				});*/

                

			},

            error: function(xhr, status, error) {

                console.error("Error occurred while fetching data: " + error);

			}

		});

	}

	$('#search_data').on('click',function(){

		refreshTable();

	});

	

	$(document).ready(function() {

	    

	    

		

		$('#pin').blur(function() {

			var zip = $('#pin').val();

			if (zip === "" || zip.length < 6) {

				$('#city').val("");

				$('.selectpicker').selectpicker('refresh');

				$('#stateid').val("");

				$('.selectpicker').selectpicker('refresh');

				} else {

				$.ajax({

					url: "<?php echo admin_url(); ?>KirtiOneOrder/FetchAddressDetailsByPincode",

					method: "POST",

					dataType: 'json',

					data: { zip: zip },

					beforeSend: function () {

						$('.searchh6').css('display', 'block');

						$('.searchh6').css('color', 'blue');

					},

					complete: function () {

						$('.searchh6').css('display', 'none');

					},

					success: function(data) {

						let result = data[0]["PostOffice"];

						if (result == null) {

							alert(data[0]["Message"]);

							$('#city').val('');

							$('#stateid').val('');

							} else {

							var District = result[0]["District"];

							var State = result[0]["State"];

							

							// Set CITY dropdown by matching text

							$('#city option').filter(function() {

								return $(this).text().trim().toLowerCase() === District.trim().toLowerCase();

							}).prop('selected', true);

							

							// Set STATE dropdown by matching text

							$('#stateid option').filter(function() {

								return $(this).text().trim().toLowerCase() === State.trim().toLowerCase();

							}).prop('selected', true);

							

							// Refresh both selectpickers to reflect the selection

							$('.selectpicker').selectpicker('refresh');

						}

					}

				});

			}

		});

	});

	

	

</script>



<script>

    document.addEventListener('DOMContentLoaded', function () 

    {

		// var selectElement = document.getElementById('Effecton');

		// var Delivery_type = $("#type").val();

		// if(Delivery_type == 1)

		// {

		// $("#state-container").hide();

		// $("#city-container").hide();

		// $("#taluka-container").hide();

		// $("#po-container").hide();

		// $("#loc-container").hide();

		// $("#street-container").hide();

		// $("#house-container").hide();

		// $("#pin-container").hide();

		

		// }

		var Delivery_type = $("#type").val();

		if(Delivery_type == 1)

		{

			$("#shipping-container").hide();

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

		

		var ordtype = $("#ordtype").val();

		if(ordtype == '2')

		{

            $("#paymode-container").hide();

			$("#effect-container").hide();

			$("#paymethod-container").hide();

			$("#refernececont").hide();             

		}

		

		// if (selectElement.options.length > 0) {

		// selectElement.selectedIndex = 0;  

		// }

        // for (var i = 1; i < selectElement.options.length; i++) {

        // selectElement.options[0].style.display = 'none';  

		// }

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

			}else

			{

				$("#party_name").hide();

				$("#phonenumber").prop("readonly", true);

			}

		});

		

		$("#ShippingID").change(function() 

        {

			var ShippingID = $("#ShippingID").val();

			if(ShippingID == "new")

			{

				$(".ShippingField").show();

			}else

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

			}else if(Delivery_type == 1)

            {

                $("#shipping-container").hide();

			}

		});

		   

		

        $("#ordtype").change(function() 

        {

            var Order_type = $("#ordtype").val();    

            if(Order_type == 2)

            {

                $("#CashOrderDiv").hide();       

			}else if(Order_type == 1)

            {

                $("#CashOrderDiv").show();                            

			}            

		});

		$("#type").trigger('change');

	});

	

	$('#OtherAmt,#CashAmt,#OnlineAmt').on('keypress', function (event) {

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
	$(document).ready(function() {
			// Get today's date
			var today = new Date();

			// Example: allow only today and one back date (e.g., yesterday)
			var yesterday = new Date();
			yesterday.setDate(today.getDate() - 1);

			// List of allowed dates
			var allowedDates = [
					formatDate(yesterday),
					formatDate(today)
			];

			function formatDate(date) {
					// Format as d/m/Y for the datetimepicker
					var day = ("0" + date.getDate()).slice(-2);
					var month = ("0" + (date.getMonth() + 1)).slice(-2);
					var year = date.getFullYear();
					return day + "/" + month + "/" + year;
			}

			$('#posted_date').datetimepicker({
					format: 'd/m/Y',
					timepicker: false,
					scrollMonth: false,
					scrollInput: false,
					scrollTime: false,
					beforeShowDay: function(date) {
							var d = formatDate(date);
							// Enable only if date is in allowedDates array
							<?php
							
							if(!is_admin()) {
								echo "if (allowedDates.includes(d)) { return [true, '', 'Available']; } else { return [false, '', 'Unavailable']; }";
							}else{
								echo "return [true, '', 'Available'];";
							}
							?>
							// if (allowedDates.includes(d)) {
							// 		return [true, '', 'Available'];
							// } else {
							// 		return [false, '', 'Unavailable'];
							// }
					}
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

