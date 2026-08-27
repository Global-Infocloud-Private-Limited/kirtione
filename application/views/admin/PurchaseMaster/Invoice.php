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
					<table id="print_table">
						<thead>
							<tr>
								<th align="center" colspan="13"><?php echo $company_detail->company_name; ?></th>
							</tr>
							<tr>
								<th align="center" colspan="13"><?php echo $company_detail->address; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="16"><center><b>Purchase Invoice Ledger</b></center></td>
							</tr>
							<tr>
								<td colspan="3"><b>Inv No :</b> <?php echo $purchase_details->Inv_No  ?? ''; ?></td>
								<td colspan="4"><b>Inv Date : </b><?php echo _d(substr($purchase_details->Transdate,0,10)) ?? ''; ?></td>
								<td colspan="4" style=" white-space: nowrap;"><b>Center Name : </b><?php echo $purchase_details->CenterName ?? ''; ?></td>
								<td colspan="4" style=" white-space: nowrap;"><b>Center State : </b><?php echo $purchase_details->StateCenter ?? ''; ?></td>
							</tr>
							<tr>
								<td colspan="3" style=" white-space: nowrap;"><b>Vendor Name : </b><?php echo $purchase_details->company ?? ''; ?></td>
								<td colspan="4"><b>GST : </b><?php echo  $purchase_details->gst ?? ''; ?></td>
								<td colspan="8" style=" white-space: nowrap;"><b>Address : </b><?php echo $purchase_details->VendorAddress ?? ''; ?></td>
							</tr>
							<tr>
								<td align="center" colspan="12"><b>ITEM DETAILS</b></td>
							</tr>
							<tr class="print_item_h" style=" white-space: nowrap;">
								<td width="20%" align="center">Item Name</td>
								<td width="10%" align="center">Brand</td>
								<td width="10%" align="center">Measured In</td>
								<td width="10%" align="center">Pack Qty</td>
								<td width="10%" align="center">Pack Weight</td>
								<td width="10%" align="center">Unit</td>
								<td width="10%" align="center">Qty</td>
								<td width="10%" align="center">Rate</td>
								<td width="10%" align="center">Discount %</td>
								<td width="10%" align="center">Gst</td>
								<td width="10%" align="center">Gst Amt</td>
								<td width="10%" align="center">Net Amt</td>
							</tr>
							<?php
								$items = json_decode($pur_order_detail, true);
								if(isset($items)){
									foreach ($items as $key => $value) {
									?>
									<tr style=" white-space: nowrap;">
										<td align="center"><?php echo $value["ProductName"]; ?></td>
										<td align="left"><?php echo $value["Brand"]; ?></td>
										<td align="ceter"><?php echo $value["Measuredin"]; ?></td>
										<td align="center"><?php echo $value["PackingQty"]; ?></td>
										<td align="center"><?php echo $value["Packingwgt"]; ?></td>
										<td align="left"><?php echo $value["PurchUnit"]; ?></td>
										<td align="right"><?php echo $value["OrderQty"]; ?></td>
										<td align="right"><?php echo $value["PurchRate"]; ?></td>
										<td align="right"><?php echo $value["Discount"]; ?></td>
										<td align="right"><?php echo $value["gst"]; ?></td>
										<td align="right"><?php echo ($value["cgstamt"]+$value["sgstamt"]+$value["igstamt"]); ?></td>
										<td align="right"><?php echo $value["Netamt"]; ?></td>
									</tr>
									<?php
									}

								}

							?>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Subtotal Amt</td>
								<td align="right"><?php echo $purchase_details->Purchamt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Discount Amt</td>
								<td align="right"><?php echo $purchase_details->Discamt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Taxable Amt</td>
								<td align="right"><?php echo $purchase_details->taxable_amt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">CGST Amt</td>
								<td align="right"><?php echo $purchase_details->cgstamt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">SGST Amt</td>
								<td align="right"><?php echo $purchase_details->sgstamt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">IGST Amt</td>
								<td align="right"><?php echo $purchase_details->igstamt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Round Off Amt</td>
								<td align="right"><?php echo $purchase_details->RoundOffAmt ?? ''; ?></td>
							</tr>
							<tr class="" style=" white-space: nowrap;">
								<td colspan="11" align="right">Inv Amt</td>
								<td align="right"><?php echo $purchase_details->Invamt ?? ''; ?></td>
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
										<li class="breadcrumb-item active" aria-current="page"><b>Purchase Invoice Ledger</b></li>
									</ol>
								</nav>
								<hr class="hr_style">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											<?php
												$selected_company = $this->session->userdata('root_company');
												$fy = $this->session->userdata('finacial_year');
												if(isset($purchase_details)){
													$isedit = "Y";
													}else{
													$isedit = "";
												}

											?>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->AccountID : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="vendor">
													<label for="vendor">Select Vendor</label>
													<select name="vendor" id="vendor"  onchange="GetPI(this.value)" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>
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
												<?php $value = (isset($purchase_details) ? $purchase_details->Inv_No : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="PurchID">
													<label for="PurchID">Purch Inward No</label>
													<select name="PurchID" id="PurchID" onchange="GetPIDetails(this.value)"  class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>
														<option value=""></option>
														<?php if ($Isdisabled){ ?>
															<option value="<?= $value;?>" selected><?= $value;?></option>
														<?php } ?>
													</select>
													<?php if ($Isdisabled){ ?>
														<input type="hidden" name="PurchID" id="Po_no_value" value="<?php echo $value; ?>" />
													<?php } ?>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="CenterName">Center Name</label>
													<input type="text" name="CenterName" id="CenterName" class="form-control" value="<?php echo $purchase_details->CenterName ?? ''; ?>"  readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->phonenumber : ''); ?>
												<div class="form-group">
													<label for="mobile_no">Mobile No.</label>
													<input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $purchase_details->phonenumber ?? ''; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->phonenumber ?? ''; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->gst : ''); ?>
												<div class="form-group">
													<label for="gst">Gst</label>
													<input type="text" name="gst" id="gst" class="form-control" value="<?php echo $purchase_details->gst ?? ''; ?>" data-isedit="<?php echo $isedit; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> readonly>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($purchase_details) ? $purchase_details->state : ''); ?>
												<?php //$Isdisabled = !empty($value) ? 'disabled' : '';  ?>
												<?php $Isdisabled = (isset($value) ? 'disabled' : ''); ?>
												<div class="form-group">
													<label for="state">State</label>
													<div>
														<select name="state" id="state" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled;?>>
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
											<div class="clearfix"></div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="VendorDocNo">
													<label for="VendorDocNo" class="control-label">Vendor Invoice No.</label>
													<input type="text" name="VendorDocNo" id="VendorDocNo" class="form-control" value = "<?php echo $purchase_details->InvoiceNo ?? ''; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->InvoiceNo ?? ''; ?>">
												</div>
											</div>
											<div class="col-md-2">
												<?php
													$value = (isset($purchase_details) ? _d(substr($purchase_details->InvoiceNoDate,0,10)) : date('Y-m-d'));
													$attr = array('readonly'=>'readonly');
													echo render_date_input('InvoiceNoDate', 'Vendor Invoice Date', $value, '');
												?>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="DeliveryChallanNo">
													<label for="DeliveryChallanNo" class="control-label">Delivery Challan No.</label>
													<input type="text" name="DeliveryChallanNo" id="DeliveryChallanNo" class="form-control" value = "<?php echo $purchase_details->DeliveryChallanNo ?? ''; ?>" readonly data-original-number="<?php echo $purchase_details->DeliveryChallanNo ?? ''; ?>">
												</div>
											</div>
											<div class="col-md-2">
												<?php
													$value = (isset($purchase_details) ? _d(substr($purchase_details->DeliveryChallanNoDate,0,10)) : date('Y-m-d'));
													$attr = array('readonly'=>'readonly');
													echo render_date_input('DeliveryChallanNoDate', 'Delivery Challan Date', $value, $attr);
												?>
											</div>
											<div class="col-md-2" id="purchase-container">
												<?php $value = (isset($purchase_details) ? $purchase_details->PurchaseType : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group">
													<label for="purchasetype">Purchase Type</label>
													<select name="purchasetype" id="purchasetype" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled;?>>
														<option value="1" <?php echo ($value == 1) ? 'selected' : ''; ?>>Cash Purchase</option>
														<option value="2" <?php echo ($value == 2) ? 'selected' : ''; ?>>Credit Purchase</option>
													</select>
													<?php if ($Isdisabled): ?>
													<input type="hidden" name="purchasetype" id="entry_value" value="<?php echo $value; ?>" />
													<?php else: ?>
													<input type="hidden" name="purchasetype" id="entry_value" value="<?php echo ($value ?: '1'); ?>" />
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-2" id="paymode-container">
												<?php $value = (isset($purchase_details) ? $purchase_details->PaymentMode : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group">
													<label for="paymode">Payment Mode</label>
													<select name="paymode" id="paymode" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled;?>>
														<option value="1" <?php echo ($value == 1) ? 'selected' : ''; ?>>Cash</option>
														<option value="2" <?php echo ($value == 2) ? 'selected' : ''; ?>>Online</option>
													</select>
													<?php if ($Isdisabled): ?>
													<input type="hidden" name="paymode" id="mode_value" value="<?php echo $value; ?>" />
													<?php else: ?>
													<input type="hidden" name="paymode" id="mode_value" value="<?php echo ($value ?: '1'); ?>" />
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-2" id="paymethod-container">
												<?php $value = (isset($purchase_details) ? $purchase_details->PaymentMethod : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="AccountID">
													<label for="paymentmethod" class="control-label">Payment Method</label>
													<select name="paymentmethod" id="paymentmethod" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php echo $Isdisabled;?>>
														<option value=""></option>
														<option value="1" <?php echo ($value == 1) ? 'selected' : ''; ?>>UPI</option>
														<option value="2" <?php echo ($value == 2) ? 'selected' : ''; ?>>Bank Transfer</option>
														<option value="3" <?php echo ($value == 3) ? 'selected' : ''; ?>>Credit/Debit Card</option>
														<?php if ($Isdisabled): ?>
														<input type="hidden" name="paymentmethod" value="<?php echo $value; ?>" />
														<?php else: ?>
														<input type="hidden" name="paymentmethod" value="<?php echo ($value ?: '1'); ?>" />
														<?php endif; ?>
													</select>
												</div>
											</div>
											<div class="col-md-2" id="refernececont">
												<?php $value = (isset($purchase_details) ? $purchase_details->RefNo : ''); ?>
												<div class="form-group">
													<label for="referenceno">Reference No</label>
													<input type="text" class="form-control" name="referenceno" id="referenceno" value="<?php echo $purchase_details->RefNo ?? ''; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->RefNo ?? ''; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> />
												</div>
											</div>
											<div class="col-md-2" id="effect-container">
												<?php $value = (isset($purchase_details) ? $purchase_details->EffectOn : ''); ?>
												<?php $Isdisabled = (isset($purchase_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="AccountID">
													<label for="Effecton" class="control-label">Effect On</label>
													<select name="Effecton" id="Effecton" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php echo $Isdisabled;?>>
														<?php
															$firstAccountID = isset($EffectOn[0]) ? $EffectOn[0]['AccountID'] : '';
															$secondAccountID = isset($EffectOn[1]) ? $EffectOn[1]['AccountID'] : '';
															$selectedAccountID = ($purchase_details->PaymentMode == 1) ? $firstAccountID : $secondAccountID;
															foreach($EffectOn as $val1)
															{
																echo '<option value="' . $val1['AccountID'] . '"
																' . ($value == $val1['AccountID'] ? 'selected' : '') . '>'
																. $val1['company'] .
																'</option>';
															}

														?>
													</select>
													<?php if ($Isdisabled): ?>
													<input type="hidden" name="Effecton" id="on_value" value="<?php echo $value; ?>" />
													<?php else: ?>
													<input type="hidden" name="Effecton" id="on_value" value="<?php echo ($value ?: $selectedAccountID); ?>" />
													<?php endif; ?>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="col-md-6">
													<div class="row expense-row">
															<div class="col-md-4">
																	<div class="form-group">
																			<label class="control-label">Expense Type</label>
																			<select name="expense_type[]" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected">
																					<option value=""></option>
																					<?php foreach($DirectExp as $val1) { ?>
																							<option value="<?php echo $val1["AccountID"];?>"><?php echo $val1["company"]?></option>
																					<?php } ?>
																			</select>
																	</div>
															</div>
															<div class="col-md-4">
																	<div class="form-group">
																			<label class="control-label">Expense Amt</label>
																			<input type="text" name="expense_amt[]" class="form-control" value="" onchange="Totalvalueset();" onkeyup="Totalvalueset();" onblur="Totalvalueset();">
																	</div>
															</div>
															<div class="col-md-2 ptop10">
																	<button type="button" class="btn btn-success btn-add-expense-row mtop10">+</button>
															</div>
													</div>
													<div id="expense-container">
														<?php
														if(isset($expense_ledger) && !empty($income_ledger)){
															foreach($expense_ledger as $key => $value){
																echo '<div class="row expense-row">
																	<div class="col-md-4">
																		<div class="form-group">
																			<label class="control-label">Expense Type</label>
																			<select name="expense_type[]" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected">
																					<option value=""></option>
																					';
																					foreach($DirectExp as $val1) {
																						echo '<option value="'.$val1["AccountID"].'" '.($val1["AccountID"] == $value['LedgerType'] ? 'selected' : '').'>'.$val1["company"].'</option> ';
																					}

																					echo '
																			</select>
																		</div>
																	</div>
																	<div class="col-md-4">
																		<div class="form-group">
																			<label class="control-label">Expense Amt</label>
																			<input type="text" name="expense_amt[]" class="form-control" value="'.$value['Amount'].'" onchange="Totalvalueset();" onkeyup="Totalvalueset();" onblur="Totalvalueset();">
																		</div>
																	</div>
																	<div class="col-md-2 ptop10">
																		<button type="button" class="btn btn-danger btn-remove-expense-row mtop10">-</button>
																	</div>
																</div>';
															}
														}
														?>
													</div>
											</div>
											<div class="col-md-6">
												<div class="row income-row">
													<div class="col-md-4">
														<div class="form-group" app-field-wrapper="">
															<label for="" class="control-label">Direct Income Type</label>
															<select name="income_type[]" id="" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected">
																<option value=""></option>
																<?php
																	foreach($DirectInc as $val1)
																	{ ?>
																			<option value="<?php echo $val1["AccountID"];?>"><?php echo $val1["company"]?></option>
																<?php
																	}
																?>
														</select>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group" app-field-wrapper="">
															<label for="" class="control-label">Direct Income Amt</label>
															<input type="text" name="income_amt[]" id="" class="form-control" value="" onchange="Totalvalueset();" onkeyup="Totalvalueset();" onblur="Totalvalueset();">
														</div>
													</div>
													<div class="col-md-2 ptop10">
														<button type="button" class="btn btn-success btn-add-income-row mtop10">+</button>
													</div>
												</div>
												<div id="income-container">
													<?php
													if(isset($income_ledger) && !empty($income_ledger)){
														foreach($income_ledger as $key => $value){
															echo '<div class="row income-row">
																	<div class="col-md-4">
																		<div class="form-group" app-field-wrapper="">
																			<label for="" class="control-label">Direct Income Type</label>
																			<select name="income_type[]" id="" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected">
																				<option value=""></option>
																				';
																				foreach($DirectInc as $val1)
																				{
																					echo '<option value="'.$val1["AccountID"].'" '.($val1["AccountID"] == $value['LedgerType'] ? 'selected' : '').'>'.$val1["company"].'</option>';
																				}
																				echo '
																			</select>
																		</div>
																	</div>
																	<div class="col-md-4">
																		<div class="form-group" app-field-wrapper="">
																			<label for="" class="control-label">Direct Income Amt</label>
																			<input type="text" name="income_amt[]" id="" class="form-control" value = "'.$value['Amount'].'" onchange="Totalvalueset();" onkeyup="Totalvalueset();" onblur="Totalvalueset();">
																		</div>
																	</div>
																	<div class="col-md-2 ptop10">
																		<button type="button" class="btn btn-danger btn-remove-income-row mtop10">-</button>
																	</div>
																</div>';
														}
													}
													?>
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
												<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="<?php echo $purchase_details->TotalOrderQty ?? ''; ?>">
											</td>
											<td>
												<label for="total_amt_in_mt">SubTotal</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="<?php echo $purchase_details->Purchamt ?? ''; ?>">
											</td>
											<td>
												<label for="total_disc_in_mt">Discount Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="<?php echo $purchase_details->Discamt ?? ''; ?>">
											</td>
											<td>
												<label  for="Total_value">Taxable Amt</label>
												<input  type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="<?php echo $purchase_details->taxable_amt ?? ''; ?>" >
											</td>
											<td>
												<label  for="total_cgst_amt">CGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="<?php echo $purchase_details->cgstamt ?? ''; ?>">
											</td>
											<td>
												<label  for="total_sgst_amt">SGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $purchase_details->sgstamt ?? ''; ?>">
											</td>
											<td>
												<label  for="total_igst_amt">IGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="<?php echo $purchase_details->igstamt ?? ''; ?>">
											</td>
											<td>
												<label  for="total_roundoff_amt">RoundOff Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="<?php echo $purchase_details->RoundOffAmt ?? ''; ?>">
											</td>
											<td>
												<label  for="netpayableamt">Invoice Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="<?php echo $purchase_details->Invamt ?? ''; ?>">
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
									<?php
										if(!isset($purchase_details)){
										?>
										<div class="col-md-1" style="margin-left: 10px;">
											<a href="#" class="btn btn-danger Pending-Entry">Pending Entry</a>
										</div>
										<?php
										}

									?>
									<div class="col-md-1" style="margin-left: 0px; margin-right: -30px;">
										<a href="#" class="btn btn-default reset-new-order" id="resetbutton">Reset</a>
									</div>
									<div class="col-md-1" style="margin-left: 10px;">
										<a href="#" class="btn btn-warning edit-new-order">View List</a>
									</div>
									<?php if (has_permission_new('PurchaseInvoiceLedger', '', 'create')){
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
									<?php if (has_permission_new('PurchaseInvoiceLedger', '', 'edit')){
									?>
									    <button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit hidden"> UPDATE</button>
									    <button type="button" id="cancelbtn" class="btn-tr cancelbtn btn btn-danger mleft10"> Cancel</button>
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
<div class="modal fade" id="transfer-modal-pending">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Pending For Ledger Entry</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				<div class="row">
					<div class="col-md-3">
						<?php echo render_date_input('from_date2','From',$from_date); ?>
					</div>
					<div class="col-md-3">
						<?php echo render_date_input('to_date2','To',$to_date);?>
					</div>
					<div class="col-md-3">
						<br>
						<button class="btn btn-info pull-left mleft5 search_data2" id="search_data2"><?php echo _l('rate_filter'); ?></button>
					</div>
					<div class="col-md-3">
						<br>
						<input type="text" id="myInput2" onkeyup="myFunction3()" placeholder="Search for names.." title="Type in a name" style="float: right;">
					</div>
					<div class="col-md-12">
						<div class="table_purchase_report_pending">
							<table class="tree table table-striped table-bordered table_purchase_report_pending" id="table_purchase_report_pending" width="100%">
								<thead>
									<tr style="display:none;">
										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
									</tr>
									<tr>
										<th style="width:8% ">PurchID</th>
										<th style="width:8% ">PO Date</th>
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

    .table_purchase_report_pending { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_purchase_report_pending thead th { position: sticky; top: 0; z-index: 1; }
	.table_purchase_report_pending tbody th { position: sticky; left: 0; }
	/* Just common table stuff. Really. */
	.table_purchase_report_pending table  { border-collapse: collapse; width: 100%; }
	.table_purchase_report_pending th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.table_purchase_report_pending th     { background: #50607b;color: #fff !important; }
	#table_purchase_report_pending tr:hover {
    background-color: #ccc;
	}

	#table_purchase_report_pending td:hover {
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
    $('#VendorDocNo').on('blur', function(){
		var VendorDocNo = $(this).val();
		var PurchInvoiceID = $("#PurchID").val();
		if(PurchInvoiceID == ""){
		    alert("Please select purchase invoice.");
		}else if(VendorDocNo !="" || VendorDocNo != null){
		    $.ajax({
    			url:"<?php echo admin_url(); ?>PurchaseMaster/CheckVendorDocNo",
    			dataType:"JSON",
    			method:"POST",
    			data:{VendorDocNo:VendorDocNo,PurchInvoiceID:PurchInvoiceID},
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
		}

	});
	$(document).on('click', '.GetPendingLedger', function()
	{
		VendorID = $(this).attr("data-id");
		Inv_no = $(this).attr("data-invoice");
		$('#vendor').val(VendorID);
		$('.selectpicker').selectpicker('refresh');
		$('#vendor').change();
		// Delay only this part
		setTimeout(function() {
			$('#PurchID').val(Inv_no);
			$('.selectpicker').selectpicker('refresh');
			$('#PurchID').change();
		}, 2000); // 2 seconds delay
		$('#transfer-modal-pending').modal('hide');
	});
	function GetPI(VenId)
	{
		var dataObject2 = [];
		hot.loadData(dataObject2);
		if(VenId == "" || VenId == null)
		{
			var optionsHtml = '<option value="">None Selected</option>';
			$('#PurchID').html(optionsHtml);
			$('.selectpicker').selectpicker('refresh');
		}
		else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>PurchaseMaster/PendingPIByVendor",
				dataType:"JSON",
				method:"POST",
				data:{VenId:VenId},
				success:function(rtndata){
					var optionsHtml = '<option value="">None Selected</option>';
					for (var i = 0; i < rtndata.length; i++) {
						optionsHtml += '<option value="' + rtndata[i].Inv_No + '">' + rtndata[i].Inv_No + '</option>';
					}

					$('#PurchID').html(optionsHtml);
					$('.selectpicker').selectpicker('refresh');
				}

			});
		}

	}

	function GetPIDetails(PINo){
		$('#CenterName').val('');
		if(PINo == '')
		{
			var dataObject2 = [];
			hot.loadData(dataObject2);
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
				url:"<?php echo admin_url(); ?>PurchaseMaster/GetPIItemData",
				dataType:"JSON",
				method:"POST",
				data:{PINo:PINo},
				success:function(rtndata){
					$('#CenterName').val(rtndata.InvoiceData.CenterName);
					$('#VendorDocNo').val(rtndata.InvoiceData.InvoiceNo);
					$('#DeliveryChallanNo').val(rtndata.InvoiceData.DeliveryChallanNo);
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
		var regex = /\/Invoice\/([^\/?#]+)/;
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
				url:"<?php echo admin_url(); ?>PurchaseMaster/load_data_for_purchase_invoice_ledger",
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
		$('#cancelbtn').on('click',function(){
			var PurchInvoiceID = $("#PurchID").val();
			var userConfirmed = confirm("Are you sure you want to cancel the purchase payment entry?");
			if (userConfirmed)
			{
			    $.ajax({
    				url:"<?php echo admin_url(); ?>PurchaseMaster/CancelPurchaseInvoice",
    				method:"POST",
    				data:{PurchInvoiceID:PurchInvoiceID,},
    				beforeSend: function () {
    					$('#searchh2').css('display','block');
    					$('.table_purchase_report_pending tbody').css('display','none');
    				},
    				complete: function () {
    					$('.table_purchase_report_pending tbody').css('display','');
    					$('#searchh2').css('display','none');
    				},
    				success:function(res){
    				    if(res){
    				        alert("The purchase invoice ledger has been cancelled successfully.");
    				        window.location.href = '<?php echo admin_url();?>'+'PurchaseMaster/Invoice';
    				    }else{
    				        alert('Error while cancelling purchase invoice ledger details. Please try again.');
    				        //location.reload();
    				    }

    				}

    			});
			}

		});
		$('.Pending-Entry').on('click', function(){
			$('#transfer-modal-pending').find('button[type="submit"]').prop('disabled', false);
			$('#transfer-modal-pending').modal('show');
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();
			load_data_pending(from_date,to_date);
		});
		$('#search_data2').on('click',function(){
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();
			load_data_pending(from_date,to_date);
		});
		function load_data_pending(from_date,to_date)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>PurchaseMaster/load_data_for_purchase_invoice_pending_ledger",
				method:"POST",
				data:{from_date:from_date, to_date:to_date},
				beforeSend: function () {
					$('#searchh2').css('display','block');
					$('.table_purchase_report_pending tbody').css('display','none');
				},
				complete: function () {
					$('.table_purchase_report_pending tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					$('.table_purchase_report_pending tbody').html(data);
				}

			});
		}

		/*$('.cancel-new-order').on('click', function()
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

		});	*/
	});
</script>
<script>
    $(document).ready(function()
	{
        $('#resetbutton').on('click', function(e) {
            e.preventDefault();
			window.location.href = '<?php echo admin_url(); ?>PurchaseMaster/Invoice';
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
		$('#entrytype').on('change', function() {
			$('#type_value').val($(this).val());
		});
		$('#purchasetype').on('change', function() {
			$('#entry_value').val($(this).val());
		});
		$('#Effecton').on('change', function() {
			$('#on_value').val($(this).val());
		});
		$('#paymode').on('change', function() {
			$('#mode_value').val($(this).val());
			var paymentMode = $(this).val();
			if (paymentMode == 1) {
				$('#on_value').val('<?php echo $firstAccountID ?? ''; ?>');
				} else if (paymentMode == 2) {
				$('#on_value').val('<?php echo $secondAccountID ?? ''; ?>');
			}

		});
		var EntryType = $("#entrytype").val();
		if(EntryType == 1)
		{
			$("#paymethod-container").hide();
			$("#refernececont").hide();
			var Order_type = $("#purchasetype").val();
			if(Order_type == 2)
			{
				$("#paymode-container").hide();
				$("#effect-container").hide();
			}
			else
			{
				$("#paymode-container").show();
				$("#effect-container").show();
			}

		}
		else if(EntryType == 2)
		{
			$("#purchase-container").hide();
			$("#paymode-container").hide();
			$("#effect-container").hide();
			$("#paymethod-container").hide();
			$("#refernececont").hide();
		}

		$("#entrytype").change(function()
		{
			var entry = $("#entrytype").val();
			if(entry == 2)
			{
				$("#purchase-container").hide();
				$("#paymode-container").hide();
				$("#effect-container").hide();
				$("#paymethod-container").hide();
				$("#refernececont").hide();
			}
			else if(entry == 1)
			{
				$("#purchase-container").show();
				$("#paymode-container").show();
				$("#effect-container").show();
				var PayMode = $("#paymode").val();
				if(PayMode == 1)
				{
					$("#paymethod-container").hide();
					$("#refernececont").hide();
				}
				else
				{
					$("#paymethod-container").show();
					$("#refernececont").show();
				}

			}

		});
		var PaymentMode = $("#paymode").val();
		if(PaymentMode ==1)
		{
			$("#paymethod-container").hide();
			$("#refernececont").hide();
		}

		$("#purchasetype").change(function()
		{
			var Order_type = $("#purchasetype").val();
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
		$("#purchasetype").change();
		$("#paymode").change(function()
		{
			var Order_type = $("#purchasetype").val();
			var PaymentMode = $("#paymode").val();
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
		$("#paymode").change(function()
		{
			var isEdit = <?php echo json_encode($isedit);?>
			if(isEdit == '')
			{
				var PaymentMode = $("#paymode").val();
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
			}

		});
		$("#paymode").trigger('change');
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

    function myFunction3()
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput2");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table_purchase_report_pending");
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
		$('#InvoiceNoDate').datetimepicker({
			format: 'd/m/Y',
			// minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
		$('#DeliveryChallanNoDate').datetimepicker({
			format: 'd/m/Y',
			// minDate: minStartDate,
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
			$('.btn-add-expense-row').click(function() {
					// Clone the first row
					var $firstRow = $('.expense-row').first();
					var $newRow = $firstRow.clone();
					$newRow.find('.bootstrap-select').each(function() {
							var $select = $(this).find('select');
							$(this).replaceWith($select); // restore raw select
					});
					// Reset values
					$newRow.find('select').val('');
					$newRow.find('input').val('');
					// Replace + with - button
					$newRow.find('.btn-add-expense-row')
							.removeClass('btn-success btn-add-expense-row')
							.addClass('btn-danger btn-remove-expense-row')
							.text('-');
					// Append new row
					$('#expense-container').append($newRow);
					// Destroy previous selectpicker and reinitialize for cloned row
					$newRow.find('select.selectpicker').selectpicker('destroy').selectpicker();
			});
			// Remove dynamic row
			$(document).on('click', '.btn-remove-expense-row', function() {
					$(this).closest('.expense-row').remove();
			});
	});
	$(document).ready(function() {
			$('.btn-add-income-row').click(function() {
					// Clone the first row
					var $firstRow = $('.income-row').first();
					var $newRow = $firstRow.clone();
					$newRow.find('.bootstrap-select').each(function() {
							var $select = $(this).find('select');
							$(this).replaceWith($select); // restore raw select
					});
					// Reset values
					$newRow.find('select').val('');
					$newRow.find('input').val('');
					// Replace + with - button
					$newRow.find('.btn-add-income-row')
							.removeClass('btn-success btn-add-income-row')
							.addClass('btn-danger btn-remove-income-row')
							.text('-');
					// Append new row
					$('#income-container').append($newRow);
					// Destroy previous selectpicker and reinitialize for cloned row
					$newRow.find('select.selectpicker').selectpicker('destroy').selectpicker();
			});
			// Remove dynamic row
			$(document).on('click', '.btn-remove-income-row', function() {
					$(this).closest('.income-row').remove();
			});
	});
</script>
</html>
<?php require 'kirtione_pur_invoice_ledger_js.php';?>