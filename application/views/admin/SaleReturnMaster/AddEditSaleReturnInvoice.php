<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

	<div class="content">

		<div class="row">

			<?php

			echo form_open($this->uri->uri_string(), array('id' => 'pur_order-form', 'class' => '_transaction_form'));



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

								<td colspan="16">
									<center><b>Sale Order</b></center>
								</td>

							</tr>

							<tr>

								<td colspan="3"><b>Inv No :</b> <?php echo $Sale_details->SaleID; ?></td>

								<td colspan="4"><b>Inv Date : </b><?php echo _d(substr($Sale_details->Transdate, 0, 10)); ?></td>

								<td colspan="4" style=" white-space: nowrap;"><b>Center Name : </b><?php echo $Sale_details->CenterName; ?></td>

								<td colspan="4" style=" white-space: nowrap;"><b>Center State : </b><?php echo $Sale_details->StateCenter; ?></td>

							</tr>

							<tr>

								<td colspan="3" style=" white-space: nowrap;"><b>Vendor Name : </b><?php echo $Sale_details->company; ?></td>

								<td colspan="4"><b>GST : </b><?php echo  $Sale_details->gst; ?></td>

								<td colspan="8" style=" white-space: nowrap;"><b>Address : </b><?php echo $Sale_details->VendorAddress; ?></td>

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

								<td width="10%" align="center">Pi Qty</td>

								<td width="10%" align="center">Return Qty</td>

								<td width="10%" align="center">Rate</td>

								<td width="10%" align="center">Discount %</td>

								<td width="10%" align="center">Gst</td>

								<td width="10%" align="center">Gst Amt</td>

								<td width="10%" align="center">Net Amt</td>

							</tr>

							<?php

							$items = json_decode($pur_order_detail, true);

							if (isset($items)) {

								foreach ($items as $key => $value) {

							?>

									<tr style=" white-space: nowrap;">

										<td align="center"><?php echo $value["ProductName"]; ?></td>

										<td align="left"><?php echo $value["Brand"]; ?></td>

										<td align="ceter"><?php echo $value["Measuredin"]; ?></td>

										<td align="center"><?php echo $value["PackingQty"]; ?></td>

										<td align="center"><?php echo $value["Packingwgt"]; ?></td>

										<td align="left"><?php echo $value["PurchUnit"]; ?></td>

										<td align="right"><?php echo $value["PendingQty"]; ?></td>

										<td align="right"><?php echo $value["ReturnOrderQty"]; ?></td>

										<td align="right"><?php echo $value["SaleRate"]; ?></td>

										<td align="right"><?php echo $value["DiscPerc"]; ?></td>

										<td align="right"><?php echo $value["gst"]; ?></td>

										<td align="right"><?php echo ($value["cgstamt"] + $value["sgstamt"] + $value["igstamt"]); ?></td>

										<td align="right"><?php echo $value["Netamt"]; ?></td>

									</tr>

							<?php

								}
							}

							?>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">Subtotal Amt</td>

								<td align="right"><?php echo $Sale_details->BillAmt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">Discount Amt</td>

								<td align="right"><?php echo $Sale_details->DiscAmt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">Taxable Amt</td>

								<td align="right"><?php echo $Sale_details->taxable_amt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">CGST Amt</td>

								<td align="right"><?php echo $Sale_details->cgstamt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">SGST Amt</td>

								<td align="right"><?php echo $Sale_details->sgstamt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">IGST Amt</td>

								<td align="right"><?php echo $Sale_details->igstamt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">Round Off Amt</td>

								<td align="right"><?php echo $Sale_details->RndAmt; ?></td>

							</tr>

							<tr class="" style=" white-space: nowrap;">

								<td colspan="12" align="right">Inv Amt</td>

								<td align="right"><?php echo $Sale_details->BillAmt; ?></td>

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

								<nav aria-label="breadcrumb">

									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

										<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

										<li class="breadcrumb-item active text-capitalize"><b>Sale Return</b></li>

										<li class="breadcrumb-item active" aria-current="page"><b>Sale Return Invoice</b></li>

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

												$prefix = 'SR';

												$prefix = $prefix . '<span id="prefix_year">' . $fy . '</span>';

												if (isset($Sale_details)) {

													$SONumber = substr($Sale_details->SalesRtnID, 4);

													$isedit = "Y";
												} else {

													$new_sale_return_Inv_Number = get_option('next_Sale_rtn_number_for_kirtione');

													$new_sale_return_Inv_Number;

													$__number = $new_sale_return_Inv_Number;

													$SONumber = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

													$isedit = '';
												}

												?>

												<div class="form-group">

													<label for="pro_orderid">SI Return.ID.</label>

													<div class="input-group">

														<span class="input-group-addon">

															<?php echo $prefix; ?>

														</span>

														<input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo $SONumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $Sale_details->SaleRtnID; ?>" <?php echo ($isedit) ? 'disabled' : '' ?>>

													</div>

												</div>

												<?php

												$SaleType = "B";

												if ($Sale_details->cgstamt > 0 || $Sale_details->igstamt > 0) {

													$SaleType = "T";
												}

												?>

												<input type="hidden" name="SaleType" id="SaleType" value="<?php echo $SaleType; ?>">

											</div>



											<div class="col-md-2">

												<?php

												$fy = $this->session->userdata('finacial_year');

												$fy_new  = $fy + 1;

												$lastdate_date = '20' . $fy_new . '-03-31';

												$curr_date = date('Y-m-d');

												$curr_date_new    = new DateTime($curr_date);

												$last_date_yr = new DateTime($lastdate_date);

												if ($last_date_yr < $curr_date_new) {

													$date = $lastdate_date;
												} else {

													$date = date('Y-m-d');
												}

												?>

												<?php

												$value = (isset($Sale_details) ? _d(substr($Sale_details->Transdate, 0, 10)) : _d($date));

												$attr = array('readonly' => 'readonly');

												echo render_date_input('prd_date', 'Sale Return Date', $value, $attr);

												?>

											</div>



											<div class="col-md-2">

												<?php $value = (isset($Sale_details) ? $Sale_details->CenterID : ''); ?>

												<div class="form-group" app-field-wrapper="centername">

													<label for="centername" class="control-label">Center Name</label>

													<select name="centername" id="centername" onchange="Getptyl(this.value)" class="selectpicker form-control" data-live-search="true">

														<option value=""></option>

														<?php

														foreach ($SaleCenterList as $center) {

															echo '<option value="' . $center['CenterID'] . '" '

																. ($value == $center['CenterID'] ? 'selected' : '') . '>'

																. $center['CenterName'] .

																'</option>';
														}

														?>

													</select>

												</div>

											</div>



											<div class="col-md-2">

												<?php

												$value = isset($Sale_details) ? $Sale_details->AccountID : '';

												$CenterID = isset($Sale_details) ? $Sale_details->CenterID : '';

												$Isdisabled = isset($Sale_details);

												?>

												<div class="form-group" app-field-wrapper="vendor">

													<label for="vendor">Party List</label>

													<select name="vendor" id="vendor" onchange="GetSO(this.value)" class="selectpicker form-control"

														data-live-search="true" data-width="100%" data-none-selected-text="None Selected"

														<?= $Isdisabled ? 'disabled' : '' ?>>

														<?php if ($Isdisabled): ?>

															<option selected value="<?= $value; ?>"><?= $Sale_details->company; ?></option>

														<?php else: ?>

															<option value="">None Selected</option>

															<!-- Vendors will be loaded via AJAX -->

															<?php foreach ($trader_list as $vendor): ?>

																<option value="<?= $vendor['AccountID']; ?>" data-statsid="<?= $vendor['state']; ?>">

																	<?= $vendor['company']; ?>

																</option>

															<?php endforeach; ?>

														<?php endif; ?>

													</select>

												</div>

											</div>



											<!-- Hidden input to retain vendor AccountID for submission -->

											<input type="hidden" name="vendor" id="vendor_value" value="<?= $value ?: ''; ?>" />



											<div class="col-md-2">

												<?php $value = (isset($Sale_details) ? $Sale_details->SaleID : ''); ?>

												<?php $Isdisabled = (isset($Sale_details) ? 'disabled' : ''); ?>

												<div class="form-group" app-field-wrapper="SaleID">

													<label for="SaleID">Sale Invioce No</label>

													<select name="SaleID" id="SaleID" onchange="GetPODetails(this.value)" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>

														<option value=""></option>

														<?php if ($Isdisabled) { ?>

															<option value="<?= $value; ?>" selected><?= $value; ?></option>

														<?php } ?>

													</select>

													<?php if ($Isdisabled) { ?>

														<input type="hidden" name="SaleID" id="Po_no_value" value="<?php echo $value; ?>" />

													<?php } ?>

												</div>

											</div>



											<div class="col-md-2">

												<?php $value = (isset($Sale_details) ? $Sale_details->phonenumber : ''); ?>

												<div class="form-group">

													<label for="mobile_no">Mobile No.</label>

													<input type="text" name="mobile_no" id="mobile_no" class="form-control" value="<?php echo $Sale_details->phonenumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $Sale_details->phonenumber; ?>" <?php echo ($isedit) ? 'readonly' : '' ?> readonly>

												</div>

											</div>





											<div class="col-md-2">



												<?php $value = (isset($Sale_details) ? $Sale_details->state : ''); ?>

												<?php //$Isdisabled = !empty($value) ? 'disabled' : '';  
												?>

												<?php $Isdisabled = (isset($value) ? 'disabled' : ''); ?>

												<div class="form-group">

													<label for="state">State</label>

													<div>

														<select name="state" id="state" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?php echo $Isdisabled; ?>>

															<option value=""></option>

															<?php

															foreach ($statelist as $val1) {





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

												<?php $value = (isset($Sale_details) ? $Sale_details->SalesRtnTypeID : ''); ?>

												<div class="form-group" app-field-wrapper="SalesRtnType">

													<label for="SalesRtnType" class="control-label">Sale Return Type</label>

													<select name="SalesRtnType" id="SalesRtnType" class="selectpicker form-control" data-live-search="true" data-none-selected-text="Non Selected">

														<option value=""></option>

														<option value="1" <?php echo ($value == '1' ? 'selected' : ''); ?>>Fresh Return</option>

														<option value="2" <?php echo ($value == '2' ? 'selected' : ''); ?>>Damage Return</option>

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

							<p class="bold p_style"><?php echo _l('Sale Order Detail'); ?></p>

							<hr class="hr_style" />

							<div class="" id="example">

							</div>

							<?php echo form_hidden('pur_order_detail'); ?>



							<div class="col-md-12 ">

								<table class="table">

									<tbody>

										<tr id="total_td">



											<td>

												<label for="total_qty_in_mt">Total Qty</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="<?php echo $Sale_details->TotalOrderQty; ?>">

											</td>

											<td>

												<label for="total_amt_in_mt">SubTotal</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="<?php echo $Sale_details->SaleAmt; ?>">

											</td>

											<td>

												<label for="total_disc_in_mt">Discount Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="<?php echo $Sale_details->DiscAmt; ?>">

											</td>

											<td>

												<label for="Total_value">Taxable Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="<?php echo $Sale_details->taxable_amt; ?>">

											</td>



											<td>

												<label for="total_cgst_amt">CGST Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="<?php echo $Sale_details->cgstamt; ?>">

											</td>

											<td>

												<label for="total_sgst_amt">SGST Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $Sale_details->sgstamt; ?>">

											</td>

											<td>

												<label for="total_igst_amt">IGST Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="<?php echo $Sale_details->igstamt; ?>">

											</td>

											<!--<td>  

												<label  for="total_tcs_amt">TCS Amt</label> 

												<input type="text" class="form-control pull-left text-right" name="total_tcs_amt" id="total_tcs_amt" value="<?php echo $Sale_details->tcsAmt; ?>">

											</td>-->

											<td>

												<label for="total_roundoff_amt">RoundOff Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="<?php echo $Sale_details->RndAmt; ?>">

											</td>



											<td>

												<label for="netpayableamt">Invoice Amt</label>

												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="<?php echo $Sale_details->BillAmt; ?>">

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

									<?php if (has_permission_new('SaleReturnInvoice', '', 'create')) {

									?>

										<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">

											<?php echo _l('submit'); ?>

										</button>

									<?php

									}

									?>

									<?php if (has_permission_new('SaleReturnInvoice', '', 'print')) { ?>

										<div class="col-md-1" style="margin-left: 10px;">

											<button type="button" class="btn btn-default hidden" id="printbtn" onclick="printPage();"><i class="fa fa-print"></i> Print</button>

										</div>

									<?php

									}

									?>





									<?php if (has_permission_new('SaleReturnInvoice', '', 'edit')) {

										$value = (isset($Sale_details) ? $Sale_details->Is_Ledger : '');

										if (isset($Sale_details) && $value == 'N' && !empty($Sale_details->SaleID)) {

									?>

											<button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit hidden"> UPDATE</button>

									<?php

										}
									}

									?>



									<!--<div class="col-md-1" style="margin-left: -5px;">

										<a href="#" id="cancelbtn" class="btn btn-danger cancel-new-order hidden">Cancel</a>

									</div>-->



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

				<h4 class="modal-title">Sale Order List</h4>

			</div>

			<div class="modal-body" style="padding:5px;">

				<?php

				$fy = $this->session->userdata('finacial_year');

				$fy_new  = $fy + 1;

				$lastdate_date = '20' . $fy_new . '-03-31';

				$firstdate_date = '20' . $fy_new . '-04-01';

				$curr_date = date('Y-m-d');

				$curr_date_new    = new DateTime($curr_date);

				$last_date_yr = new DateTime($lastdate_date);

				if ($last_date_yr < $curr_date_new) {

					$to_date = '31/03/20' . $fy_new;

					$from_date = '01/03/20' . $fy_new;
				} else {

					$from_date = "01/" . date('m') . "/" . date('Y');

					$to_date = date('d/m/Y');
				}

				?>

				<div class="row">

					<div class="col-md-3">

						<?php echo render_date_input('from_date', 'From', $from_date); ?>

					</div>

					<div class="col-md-3">

						<?php echo render_date_input('to_date', 'To', $to_date); ?>

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

										<td colspan="9">
											<h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5>
										</td>

									</tr>

									<tr>

										<th style="width:8% ">SaleR_ID</th>

										<th style="width:8% ">SO Date</th>

										<th style="width:15% text-align:left;">Sale From</th>



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
	.table_purchase_report {
		overflow: auto;
		max-height: 60vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table_purchase_report thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table_purchase_report tbody th {
		position: sticky;
		left: 0;
	}



	/* Just common table stuff. Really. */

	.table_purchase_report table {
		border-collapse: collapse;
		width: 100%;
	}

	.table_purchase_report th,
	td {
		padding: 3px 3px !important;
		white-space: nowrap;
		font-size: 11px;
		line-height: 1.42857143;
		vertical-align: middle;
	}

	.table_purchase_report th {
		background: #50607b;
		color: #fff !important;
	}





	#table_purchase_report tr:hover {

		background-color: #ccc;

	}



	#table_purchase_report td:hover {

		cursor: pointer;

	}
</style>

<script type="text/javascript">
	function printPage() {



		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;color:#fff;} </style>';

		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';

		var print_data = stylesheet + tableData

		console.log(print_data);

		newWin = window.open("");

		newWin.document.write(print_data);

		newWin.print();

		newWin.close();

	};
</script>

<script type="text/javascript" language="javascript">
	function Getptyl(CenterID) {
		var optionsHtml = '<option value="">None Selected</option>';
		if (CenterID == "" || CenterID == null) {
			$('#vendor').html(optionsHtml);
			$('.selectpicker').selectpicker('refresh');
			return;
		}

		$.ajax({
			url: "<?php echo admin_url(); ?>SaleReturn/GetPIByCenterWiseVendor",
			method: "POST",
			dataType: "JSON",
			data: {
				CenterID: CenterID
			},
			beforeSend: function() {
				optionsHtml = '<option value="">Searching...</option>';
				$('#vendor').html(optionsHtml);
				$('.selectpicker').selectpicker('refresh');
			},
			// complete: function() {
			// 	optionsHtml = '<option value="">Updating List...</option>';
			// 	$('#vendor').html(optionsHtml);
			// 	$('.selectpicker').selectpicker('refresh');
			// },
			success: function(rtndata) {
				optionsHtml = '<option value="">None Selected</option>';
				$('#vendor').html(optionsHtml);
				if (Array.isArray(rtndata)) {
					optionsHtml = '';
					for (var i = 0; i < rtndata.length; i++) {
						optionsHtml += '<option value="' + rtndata[i].AccountID + '" data-state="' + rtndata[i].state + '">'
							+
							rtndata[i].company + ' (' + rtndata[i].AccountID + ')'
							+
							'</option>';

						if ((i + 1) % 50 === 0) {
							$('#vendor').append(optionsHtml);
							optionsHtml = ''; // Clear buffer
            }
					}
					// Append remaining records
        	if (optionsHtml !== '') {
            $('#vendor').append(optionsHtml);
        	}
				}
				// $('#vendor').html(optionsHtml);
				$('.selectpicker').selectpicker('refresh');
			},
			error: function(xhr, status, error) {
				console.error("AJAX Error:", status, error);
				console.log("Response text:", xhr.responseText);
			}
		});
	}

	function GetSO(VenId)

	{

		var dataObject2 = [];

		hot.loadData(dataObject2);



		if (VenId == "" || VenId == null)

		{

			var optionsHtml = '<option value="">None Selected</option>';

			$('#SaleID').html(optionsHtml);

			$('.selectpicker').selectpicker('refresh');



		} else

		{

			var CenterID = $('#centername').val();

			$.ajax({

				url: "<?php echo admin_url(); ?>SaleReturn/GetPIByVendorAndCenter",

				dataType: "JSON",

				method: "POST",

				data: {
					VenId: VenId,
					CenterID: CenterID
				},



				success: function(rtndata) {

					var optionsHtml = '<option value="">None Selected</option>';



					for (var i = 0; i < rtndata.length; i++) {

						optionsHtml += '<option value="' + rtndata[i].SalesID + '">' + rtndata[i].SalesID + '</option>';

					}

					$('#SaleID').html(optionsHtml);

					$('.selectpicker').selectpicker('refresh');





				}

			});

		}

	}







	function GetPODetails(PINo) {

		if (PINo == '')

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

		} else

		{

			$.ajax({

				url: "<?php echo admin_url(); ?>SaleReturn/GetSIretuenItemData",

				dataType: "JSON",

				method: "POST",

				data: {
					PINo: PINo
				},



				success: function(rtndata) {

					var dataObject2 = [];

					$("#SaleType").val(rtndata.SaleType);

					if (rtndata.ItemDetails.length > 0)

					{

						hot.loadData(rtndata.ItemDetails);

					} else

					{

						hot.loadData(dataObject2);

					}

					setTimeout(function() {

						const totalRows = hot.countRows();

						const targetCol = 8; // e.g., OrderQty column

						for (let row = 0; row < totalRows; row++) {

							const currentValue = hot.getDataAtCell(row, targetCol);

							hot.setDataAtCell(row, targetCol, currentValue); // Triggers afterChange

						}

					}, 2000);

				}

			});



		}

	}





	$('#total_tcs_amt, #OtherAmt').on('keypress', function(event) {

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



	$(document).ready(function() {

		var url = window.location.href;

		var regex = /\/AddEditSaleReturnInvoice\/([^\/?#]+)/;

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



		function load_data(from_date, to_date)

		{

			$.ajax({

				url: "<?php echo admin_url(); ?>SaleReturn/load_data_for_sale_return_invoice",

				method: "POST",

				data: {
					from_date: from_date,
					to_date: to_date
				},

				beforeSend: function() {

					$('#searchh2').css('display', 'block');

					$('.table_purchase_report tbody').css('display', 'none');

				},

				complete: function() {

					$('.table_purchase_report tbody').css('display', '');

					$('#searchh2').css('display', 'none');

				},

				success: function(data) {

					$('.table_purchase_report tbody').html(data);

				}

			});

		}



		$('.edit-new-order').on('click', function() {

			$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);

			$('#transfer-modal').modal('show');



			var from_date = $("#from_date").val();

			var to_date = $("#to_date").val();

			load_data(from_date, to_date);

		});





		$('#search_data').on('click', function() {

			var from_date = $("#from_date").val();

			var to_date = $("#to_date").val();

			var msg = "Sales Report " + from_date + " To " + to_date;

			$(".report_for").text(msg);

			load_data(from_date, to_date);

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

						data: {
							poId: poId
						},

						dataType: 'json',

						success: function(response)

						{

							if (response.success)

							{

								alert_float('success', 'Order Cancelled Successfully...');

								$("#ordstat").val("C").selectpicker('refresh');

								hot.getData().forEach(function(rowData, rowIndex) {

									if (rowData && rowData[6] != undefined && rowData[13] != undefined) {

										hot.setDataAtCell(rowIndex, 6, 0.00);

										hot.setDataAtCell(rowIndex, 8, 0.00);

										hot.setDataAtCell(rowIndex, 10, 0.00);

										hot.setDataAtCell(rowIndex, 11, 0.00);

										hot.setDataAtCell(rowIndex, 12, 0.00);

										hot.setDataAtCell(rowIndex, 13, 0.00);

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

				} else {

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

				window.location.href = '<?php echo admin_url(); ?>SaleReturn/AddEditSaleReturnInvoice';

			});



		});
</script>



<script type="text/javascript" language="javascript">
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

					$('#on_value').val('<?php echo $firstAccountID; ?>');

				} else if (paymentMode == 2) {

					$('#on_value').val('<?php echo $secondAccountID; ?>');

				}

			});



			var EntryType = $("#entrytype").val();

			if (EntryType == 1)

			{

				$("#paymethod-container").hide();

				$("#refernececont").hide();

				var Order_type = $("#purchasetype").val();

				if (Order_type == 2)

				{

					$("#paymode-container").hide();

					$("#effect-container").hide();

				} else

				{

					$("#paymode-container").show();

					$("#effect-container").show();

				}

			} else if (EntryType == 2)

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

					if (entry == 2)

					{

						$("#purchase-container").hide();

						$("#paymode-container").hide();

						$("#effect-container").hide();

						$("#paymethod-container").hide();

						$("#refernececont").hide();

					} else if (entry == 1)

					{

						$("#purchase-container").show();

						$("#paymode-container").show();

						$("#effect-container").show();



						var PayMode = $("#paymode").val();

						if (PayMode == 1)

						{

							$("#paymethod-container").hide();

							$("#refernececont").hide();

						} else

						{

							$("#paymethod-container").show();

							$("#refernececont").show();

						}

					}

				});





			var PaymentMode = $("#paymode").val();

			if (PaymentMode == 1)

			{

				$("#paymethod-container").hide();

				$("#refernececont").hide();

			}



			$("#purchasetype").change(function()

				{

					var Order_type = $("#purchasetype").val();



					if (Order_type == 2)

					{

						$("#paymode-container").hide();

						$("#effect-container").hide();

						$("#paymethod-container").hide();

						$("#refernececont").hide();

					} else if (Order_type == 1)

					{

						$("#paymode-container").show();

						$("#effect-container").show();

					}

				});



			$("#paymode").change(function()

				{

					var Order_type = $("#purchasetype").val();

					var PaymentMode = $("#paymode").val();



					if (Order_type == 1 && PaymentMode == 2)

					{

						$("#paymethod-container").show();

						$("#refernececont").show();

					} else if (Order_type == 1 && PaymentMode == 1)

					{

						$("#paymethod-container").hide();

						$("#refernececont").hide();

					}

				});



			$("#paymode").change(function()

				{

					var isEdit = <?php echo json_encode($isedit); ?>

					if (isEdit == '')

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

							} else if (PaymentMode == 1)

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



			$('#vendor').on('change', function() {

				var vendor_id = $(this).val();

				$.ajax({

					url: "<?php echo admin_url(); ?>PurchaseMaster/GetVendorDetails",

					dataType: "JSON",

					method: "POST",

					data: {
						vendor_id: vendor_id
					},

					beforeSend: function() {

						$('.searchh2').css('display', 'block');

						$('.searchh2').css('color', 'blue');

					},

					complete: function() {

						$('.searchh2').css('display', 'none');

					},

					success: function(data)

					{

						$('#mobile_no').val(data.phonenumber);

						$('#state_value').val(data.state);

						$('#SalesRtnType_value').val(data.SalesRtnTypeID);



						//$('#gst').val(data.gstin);

						// if(!data.gstin){

						// document.getElementById('gst').removeAttribute('readonly');

						// }else{

						// document.getElementById('gst').readOnly = true;

						// }

						if (data.state_name)

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

						} else

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
</script>

<script>
	$('.add-new-transfer').on('click', function() {

		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);

		$('#transfer-modal').modal('show');

		init_journal_entry_table();

	});
</script>

<script>
	$(document).ready(function()

		{

			var maxEndDate = new Date('Y/m/d');

			var fin_y = "<?php echo $this->session->userdata('finacial_year') ?>";

			var year = "20" + fin_y;

			var cur_y = new Date().getFullYear().toString().substr(-2);

			if (cur_y > fin_y) {

				var year2 = parseInt(fin_y) + parseInt(1);

				var year2_new = "20" + year2;

				var e_dat = new Date(year2_new + '/03/31');

				var maxEndDate_new = e_dat;

			} else {

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

			var fin_y = "<?php echo $this->session->userdata('finacial_year') ?>";



			var year = "20" + fin_y;

			var cur_y = new Date().getFullYear().toString().substr(-2);

			if (cur_y => fin_y) {

				var year2 = parseInt(fin_y) + parseInt(1);

				var year2_new = "20" + year2;

				var e_dat = new Date(year2_new + '/03/31');

				var maxEndDate_new = e_dat;

			} else {

				var e_dat2 = new Date(year2 + '/03/31');

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



<?php require 'Kirtione_Sale_return_invoice_js.php'; ?>