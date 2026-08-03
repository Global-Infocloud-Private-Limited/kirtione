<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
				echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			?>
			<div class="row" style="display:none;">
				<div class="col-md-12">
					<table id="print_table" style="width: 100%; border-collapse: collapse; table-layout: fixed;" border="1">
						<thead>
							<tr>
								<th align="center" colspan="12"><?php echo $company_detail->company_name; ?></th>
							</tr>
							<tr>
								<th align="center" colspan="12"><?php echo $company_detail->address; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="12"><center><b>Purchase Order</b></center></td>
							</tr>
							<tr>
								<td><b>Center Name : </b><?php echo $purchase_details->CenterName ; ?> </td>
								<td colspan="4"><b>Center State : </b><?php echo $purchase_details->StateCenter ; ?></td>
								<td colspan="4"><b>PO No :</b> <?php echo $purchase_details->PurchID ; ?></td>
								<td colspan="4"><b>PO Date : </b><?php echo _d(substr($purchase_details->Transdate,0,10)) ; ?></td>
							</tr>
							<tr>
								<td><b>Vendor Name : </b><?php echo $purchase_details->company ; ?></td>
								<td colspan="4"><b>GST : </b><?php echo  $purchase_details->gst ; ?></td>
								<td colspan="7"><b>Address : </b><?php echo $purchase_details->VendorAddress ; ?></td>
							</tr>
							<tr>
								<td align="center" colspan="12"><b>Item Details</b></td>
							</tr>
							<tr>
								<td width="20%" align="center">Item Name</td>
								<td width="10%" align="center">Brand</td>
								<td width="5%" align="center">UOM</td>
								<td width="5%" align="center">Pack Qty</td>
								<td width="5%" align="center">Pack Wt(kg)</td>
								<td width="8%" align="center">Purch Unit</td>
								<td width="8%" align="center">Qty</td>
								<td width="8%" align="center">Basic Rate</td>
								<td width="8%" align="center">Disc Amt</td>
								<td width="5%" align="center">GST %</td>
								<td width="8%" align="center">GST Amt</td>
								<td width="10%" align="center">Net Amount</td>
							</tr>
							<?php
								$items = json_decode($pur_order_detail, true);
								if(isset($items)){
									foreach ($items as $key => $value) {
									?>
									<tr style="white-space: nowrap;">
										<td align="left"><?php echo $value["ProductName"]; ?></td>
										<td align="left"><?php echo $value["Brand"]; ?></td>
										<td align="ceter"><?php echo $value["Measuredin"]; ?></td>
										<td align="center"><?php echo $value["PackingQty"]; ?></td>
										<td align="center"><?php echo $value["Packingwgt"]; ?></td>
										<td align="left"><?php echo $value["PurchUnit"]; ?></td>
										<td align="right"><?php echo number_format($value["OrderQty"], 2, '.', ''); ?></td>
										<td align="right"><?php echo number_format($value["PurchRate"], 2, '.', ''); ?></td>
										<td align="right"><?php echo number_format($value["Discount"], 2, '.', ''); ?></td>
										<td align="right"><?php echo $value["gst"]; ?></td>
										<?php $GstAmt = ($value["cgstamt"]+$value["sgstamt"]+$value["igstamt"]);?>
										<td align="right"><?php echo number_format($GstAmt, 2, '.', ''); ?></td>
										<td align="right"><?php echo number_format($value["Netamt"], 2, '.', ''); ?></td>
									</tr>
									<?php
									}
								}
							?>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Subtotal Amt</td>
								<td align="right"><?php echo $purchase_details->Purchamt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Discount Amt</td>
								<td align="right"><?php echo $purchase_details->Discamt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Taxable Amt</td>
								<td align="right"><?php echo $purchase_details->taxable_amt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">CGST Amt</td>
								<td align="right"><?php echo $purchase_details->cgstamt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">SGST Amt</td>
								<td align="right"><?php echo $purchase_details->sgstamt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">IGST Amt</td>
								<td align="right"><?php echo $purchase_details->igstamt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Round Off Amt</td>
								<td align="right"><?php echo $purchase_details->RoundOffAmt;?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Inv Amt</td>
								<td align="right"><?php echo $purchase_details->Invamt;?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12">
							<div class="panel-body">
								<nav aria-label="breadcrumb" >
									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
										<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
										<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>Purchase Order</b></li>
									</ol>
								</nav>
								<hr class="hr_style">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											<div class="col-md-2">
												<?php
													$selected_company = $this->session->userdata('root_company');
													$fy = $this->session->userdata('finacial_year');
													$prefix = 'PO';
													$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
													if(isset($purchase_details)){
														$PONumber = substr($purchase_details->PurchID,4);
														$isedit = $purchase_details->PurchID;
													}else{
														$new_purchase_orderNumbar = get_option('next_purchase_number_for_kirtione');
														$new_purchase_orderNumbar = '1' . $new_purchase_orderNumbar;
														$__number = $new_purchase_orderNumbar;
														$PONumber = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
														$isedit = '';
													}
												?>
												<input type="hidden" name="isedit" id="isedit" value="<?php echo $isedit;?>">
												<div class="form-group">
													<label for="pro_orderid">PO.No.</label>
													<div class="input-group">
														<span class="input-group-addon">
															<?php echo $prefix; ?>
														</span>
														<input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo $PONumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->PurchID; ?>" <?php echo ($isedit) ? 'disabled' : '' ?>>
													</div>
												</div>
											</div>
											<div class="col-md-2">
												<?php
													$fy = $this->session->userdata('finacial_year');
													$fy_new  = $fy + 1;
													$lastdate_date = '20'.$fy_new.'-03-31';
													$curr_date = date('Y-m-d');
													$curr_date_new    = new DateTime($curr_date);
													$last_date_yr = new DateTime($lastdate_date);
													if($last_date_yr < $curr_date_new){
														$date = $lastdate_date;
														}else{
														$date = date('Y-m-d');
													}
												?>
												<?php
													$value = (isset($purchase_details) ? _d(substr($purchase_details->Transdate,0,10)) : _d($date));
													$attr = array('readonly'=>'readonly');
													echo render_date_input('prd_date','PR Date',$value,$attr);
												?>
											</div>
											<!--<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->CenterID : ''); ?>
												<div class="form-group" app-field-wrapper="centername">
												<label for="centername" class="control-label">Center Name</label>
												<select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
												<option value=""></option>
												<?php
													foreach($centermaster as $center)
													{
														echo '<option value="' . $center['CenterID'] . '"
														data-statsid="' . $center['state'] . '"
														' . ($value == $center['CenterID'] ? 'selected' : '') . '>'
														. $center['CenterName'] .
														'</option>';
													}
												?>
												</select>
												</div>
											</div>-->
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->AccountID : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="vendor">
													<label for="vendor">Select Vendor</label>
													<select name="vendor" id="vendor"  onchange="GetPR(this.value)" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" <?php echo $Isdisabled; ?>>
														<option value=""></option>
														<?php
															if ($Isdisabled):
														?>
														<option selected value="<?= $purchase_details->AccountID;?>"><?= $purchase_details->company;?></option>
														<?php
															else:
															foreach($trader_list as $vendor)
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
												<?php $value = (isset($purchase_details) ? $purchase_details->Pr_no : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="Pr_no">
													<label for="Pr_no">Purch Request No</label>
													<select name="Pr_no" id="Pr_no" onchange="GetPRDetails(this.value)"  class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" <?php echo $Isdisabled; ?>>
														<option value=""></option>
														<?php if ($Isdisabled){ ?>
															<option value="<?= $value;?>" selected><?= $value;?></option>
														<?php } ?>
													</select>
													<?php if ($Isdisabled){ ?>
														<input type="hidden" name="Pr_no" id="Pr_no_value" value="<?php echo $value; ?>" />
													<?php } ?>
												</div>
											</div>
											<!--<div class="col-md-2">
												<div class="form-group">
													<label for="CenterName">Center Name</label>
													<input type="text" name="CenterName" id="CenterName" class="form-control" value="<?php echo $purchase_details->CenterName; ?>"  readonly>
												</div>
											</div>-->
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->CenterID : ''); ?>
												<div class="form-group" app-field-wrapper="CenterName">
													<label for="CenterName" class="control-label">Center Name</label>
													<select name="CenterName" id="CenterName" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">
														<option value=""></option>
														<?php
															foreach($centermaster as $center)
															{
																echo '<option value="' . $center['CenterID'] . '"
																data-statsid="' . $center['state'] . '"
																' . ($value == $center['CenterID'] ? 'selected' : '') . '>'
																. $center['CenterName'] .
																'</option>';
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->phonenumber : ''); ?>
												<div class="form-group">
													<label for="mobile_no">Mobile No.</label>
													<input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $purchase_details->phonenumber; ?>"  data-original-number="<?php echo $purchase_details->phonenumber; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->gst : ''); ?>
												<div class="form-group">
													<label for="gst">Gst</label>
													<input type="text" name="gst" id="gst" class="form-control" value="<?php echo $purchase_details->gst; ?>"  <?php echo ($isedit) ? 'readonly' : '' ?> readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->state : ''); ?>
												<?php //$Isdisabled = !empty($value) ? 'disabled' : '';  ?>
												<?php $Isdisabled = (isset($value) ? 'disabled' : ''); ?>
												<div class="form-group">
													<label for="state">State</label>
													<div>
														<select name="state" id="state" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" <?php echo $Isdisabled;?>>
															<option value=""></option>
															<?php
																foreach($statelist as $val1)
																{
																	echo '<option value="' . $val1['short_name'] . '"
																	' . ($value == $val1['short_name'] ? 'selected' : '') . '>'
																	. $val1['state_name'] .
																	'</option>';
																}
															?>
														</select>
														<?php if ($Isdisabled): ?>
														<input type="hidden" name="state" id="state_value" value="<?php echo $value; ?>" />
														<?php else: ?>
														<input type="hidden" name="state" id="state_value" value="<?php echo ($value ?: ''); ?>" />
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="AccountID">
													<?php $value = (isset($purchase_details) ? $purchase_details->OrderStatus : ''); ?>
													<label for="ordstat" class="control-label">Order Status</label>
													<select name="ordstat" id="ordstat" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true" disabled>
														<option value="P"<?php echo ($value == "P") ? 'selected' : ''; ?>>Pending</option>
														<option value="C"<?php echo ($value == "A") ? 'selected' : ''; ?>>Approved</option>
														<option value="C"<?php echo ($value == "C") ? 'selected' : ''; ?>>Cancel</option>
														<option value="F"<?php echo ($value == "F") ? 'selected' : ''; ?>>Completed</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="VendorDocNo">Vendor Doc No.</label>
													<input type="text" name="VendorDocNo" id="VendorDocNo" class="form-control" value="<?php echo $purchase_details->InvoiceNo; ?>" >
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
							<p class="bold p_style"><?php echo _l('Purchase Order Detail'); ?></p>
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
												<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="<?php echo $purchase_details->TotalOrderQty; ?>">
											</td>
											<td>
												<label for="total_amt_in_mt">SubTotal</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="<?php echo $purchase_details->Purchamt;?>">
											</td>
											<td>
												<label for="total_disc_in_mt">Discount Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="<?php echo $purchase_details->Discamt;?>">
											</td>
											<td>
												<label  for="Total_value">Taxable Amt</label>
												<input  type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="<?php echo $purchase_details->taxable_amt;?>" >
											</td>
											<td>
												<label  for="total_cgst_amt">CGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="<?php echo $purchase_details->cgstamt;?>">
											</td>
											<td>
												<label  for="total_sgst_amt">SGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $purchase_details->sgstamt;?>">
											</td>
											<td>
												<label  for="total_igst_amt">IGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="<?php echo $purchase_details->igstamt;?>">
											</td>
											<td>
												<label  for="total_roundoff_amt">RoundOff Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="<?php echo $purchase_details->RoundOffAmt;?>">
											</td>
											<td>
												<label  for="netpayableamt">Invoice Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="<?php echo $purchase_details->Invamt;?>">
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-md-12 mtop15">
								<p class="bold p_style">Reminder</p>
								<hr class="hr_style"/>
								<div class="row">
									<div class="col-md-3">
										<?php
											$reminder_date_value = (isset($purchase_details) && !empty($purchase_details->ReminderDate)) ? _d($purchase_details->ReminderDate) : '';
											echo render_date_input('reminder_date', 'Reminder Date', $reminder_date_value);
										?>
									</div>
									<div class="col-md-9">
										<?php
											$reminder_remark_value = (isset($purchase_details) && !empty($purchase_details->ReminderRemark)) ? $purchase_details->ReminderRemark : '';
										?>
										<div class="form-group" app-field-wrapper="reminder_remark">
											<label for="reminder_remark" class="control-label">Remark</label>
											<textarea name="reminder_remark" id="reminder_remark" class="form-control" rows="3" placeholder="Enter reminder notes"><?php echo html_escape($reminder_remark_value); ?></textarea>
										</div>
									</div>
								</div>
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
									<?php if (has_permission_new('PurchaseOrder', '', 'create')){
									?>
									<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
										<?php echo _l('submit'); ?>
									</button>
									<?php
									}
									?>
									<div class="col-md-1" style="margin-left: 10px;">
										<button type="button"  class="btn btn-default hidden" id="printbtn" onclick="printPage();"><i class="fa fa-print"></i> Print</button>
									</div>
									<?php if (has_permission_new('PurchaseOrder', '', 'edit')){
										$value = (isset($purchase_details) ? $purchase_details->OrderStatus : '');
										if($value == 'P'){
										?>
										<button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit hidden"> UPDATE</button>
										<div class="col-md-1" style="margin-left: -5px;">
											<a href="#" id="cancelbtn" class="btn btn-danger cancel-new-order hidden">Cancel</a>
										</div>
										<?php
										}
									}
									?>
									<?php if (has_permission_new('PurchaseOrderApprove', '', 'edit')){
									    if(isset($purchase_details) && $purchase_details->OrderStatus == "P"){
									   ?>
									    <div class="col-md-1" style="margin-left: -5px;">
											<a href="#" id="Approvebtn" class="btn btn-info Approve-new-order">Approve</a>
										</div>
									   <?php
									    }
									}?>
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
				<h4 class="modal-title">Purchase Order List</h4>
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
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
					</div>
					<div class="col-md-12">
						<div class="table_purchase_report">
							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
								<thead>
									<tr style="display:none;">
										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
									</tr>
									<tr>
										<th style="width:8% ">PurchID</th>
										<th style="width:8% ">PO Date</th>
										<th style="width:8% ">PR No.</th>
										<th style="width:15% text-align:left;">Purchased From</th>
										<th style="width:15% text-align:left;">Center</th>
										<th style="width:15% text-align:left;">Order Status</th>
										<th style="width:5% text-align:left;">Purch Amt</th>
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
<script type="text/javascript" language="javascript" >
	function GetPR(VenId)
	{
	    $('#CenterName').val('');
	    $('#CenterName').prop('disabled', false);
        $('#CenterName').selectpicker('refresh');
		var dataObject2 = [];
		hot.loadData(dataObject2);
		if(VenId == "" || VenId == null)
		{
			var optionsHtml = '<option value="">None selected</option>';
			$('#Pr_no').html(optionsHtml);
			$('.selectpicker').selectpicker('refresh');
		}else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>PurchaseMaster/GetPRByVendor",
				dataType:"JSON",
				method:"POST",
				data:{VenId:VenId},
				success:function(rtndata){
					var optionsHtml = '<option value="">None selected</option>';
					for (var i = 0; i < rtndata.length; i++) {
						optionsHtml += '<option value="' + rtndata[i].PurchID + '">' + rtndata[i].PurchID + '</option>';
					}
					$('#Pr_no').html(optionsHtml);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		}
	}
	function GetPRDetails(PrNo){
		$('#CenterName').val('');
		if(PrNo == '')
		{
		    $('#CenterName').prop('disabled', false);
            $('#CenterName').selectpicker('refresh');
			var dataObject2 = [];
			hot.loadData(dataObject2);
		}else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>PurchaseMaster/GetPRItemData",
				dataType:"JSON",
				method:"POST",
				data:{PrNo:PrNo},
				success:function(rtndata){
					$('#CenterName').val(rtndata.RequestData.CenterID);
					$('#CenterName').selectpicker('refresh');
					$('#CenterName').prop('disabled', true);
                    $('#CenterName').selectpicker('refresh');
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
		var regex = /\/AddEditPurchaseOrderNew\/([^\/?#]+)/;
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
				url:"<?php echo admin_url(); ?>PurchaseMaster/load_data_for_purchase_order",
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
								if (rowData && rowData[8] != undefined) {
									hot.setDataAtCell(rowIndex, 8, 0.00);
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
		$('.Approve-new-order').on('click', function()
		{
			var url = window.location.href;
			var segments = url.split('/');
			var poId = segments[segments.length - 1].split('#')[0];
			var userConfirmed = confirm("Are you sure you want to Approve the order?");
			if (userConfirmed)
			{
				$.ajax({
					url: "<?php echo admin_url(); ?>PurchaseMaster/ApprovePurchaseOrder",
					type: 'POST',
					data: {poId:poId},
					dataType: 'json',
					success: function(response)
					{
						if (response.success)
						{
							alert_float('success', 'Order Approved Successfully...');
						} else {
							alert_float('warning', 'Something went wrong...');
						}
					},
					error: function(xhr, status, error) {
						$('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
					}
				});
			}else {
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
			window.location.href = '<?php echo admin_url(); ?>PurchaseMaster/AddEditPurchaseOrderNew';
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
				url:"<?php echo admin_url(); ?>PurchaseMaster/GetVendorDetails",
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
				    var ItemsOptions = data.Listitems.map(function(Listitems) {
    					return {
    						id: Listitems.id,
    						label: Listitems.label,
    						ProductName: Listitems.ProductName,
    					};
    				});
    				let colIndex = hot.propToCol('id'); // Replace 'id' with your dropdown column property
    				let rowCount = hot.countRows();
    				for (let row = 0; row < rowCount; row++) {
    					hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });
    				}
    				// Re-render the table after applying changes
    				hot.render();
					$('#mobile_no').val(data.phonenumber);
					$('#gst').val(data.gstin);
					if(!data.gstin){
						document.getElementById('gst').removeAttribute('readonly');
					}else{
						document.getElementById('gst').readOnly = true;
					}
					if(data.state_name)
					{
						var stateSelect = document.getElementById('state');
						var found = false;
						for (var i = 0; i < stateSelect.options.length; i++)
						{
							if (stateSelect.options[i].text === data.state_name)
							{
								stateSelect.value = stateSelect.options[i].value;
								stateSelect.setAttribute('disabled', 'disabled');
								$(stateSelect).selectpicker('refresh');
								found = true;
								break;
							}
						}
						if (!found) {
							stateSelect.value = "";
							stateSelect.removeAttribute('disabled');
							$(stateSelect).selectpicker('refresh');
						}
					}
					else
					{
						var stateSelect = document.getElementById('state');
						stateSelect.value = "";
						stateSelect.removeAttribute('disabled');
						$(stateSelect).selectpicker('refresh');
					}
					/*$('#mobile_no').val(data.phonenumber);
					$('#gst').val(data.gstin);
					if(!data.gstin){
						document.getElementById('gst').removeAttribute('readonly');
						}else{
						document.getElementById('gst').readOnly = true;
					}
					if(data.state_name)
					{
						var stateSelect = document.getElementById('state');
						var found = false;
						for (var i = 0; i < stateSelect.options.length; i++)
						{
							if (stateSelect.options[i].text === data.state_name)
							{
								stateSelect.value = stateSelect.options[i].value;
								stateSelect.setAttribute('disabled', 'disabled');
								$(stateSelect).selectpicker('refresh');
								found = true;
								break;
							}
						}
						if (!found) {
							stateSelect.value = "";
							stateSelect.removeAttribute('disabled');
							$(stateSelect).selectpicker('refresh');
						}
					}
					else
					{
						var stateSelect = document.getElementById('state');
						stateSelect.value = "";
						stateSelect.removeAttribute('disabled');
						$(stateSelect).selectpicker('refresh');
					}*/
				}
			});
		});
		$('#VendorDocNo').on('blur', function(){
			var VendorDocNo = $(this).val();
			var isedit = $("#isedit").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>PurchaseMaster/CheckVendorDocNo",
				dataType:"JSON",
				method:"POST",
				data:{VendorDocNo:VendorDocNo,isedit:isedit},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data)
				{
					if(data){
					    alert("The entered vendor document number already exists for this purchase.");
					    $("#VendorDocNo").val("");
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
<script>
	$(document).ready(function() {
		var today = new Date();
		today.setHours(0, 0, 0, 0);
		$('#reminder_date').datetimepicker({
			format: 'd/m/Y',
			minDate: today,
			timepicker: false
		});

		$('#pur_order-form').on('submit', function(e) {
			var reminderDate = $('#reminder_date').val();
			if (reminderDate) {
				var parts = reminderDate.split('/');
				if (parts.length === 3) {
					var selectedDate = new Date(parts[2], parts[1] - 1, parts[0]);
					selectedDate.setHours(0, 0, 0, 0);
					var now = new Date();
					now.setHours(0, 0, 0, 0);
					if (selectedDate < now) {
						alert('Reminder Date must be today or a future date.');
						e.preventDefault();
						return false;
					}
				}
			}
		});
	});
</script>
</html>
<?php require 'kirtione_pur_order_new_js.php';?>
