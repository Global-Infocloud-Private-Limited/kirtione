<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
										<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>Kirti One Stock Transfer</b></li>
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
													$prefix = "TRF";
													$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
													if(isset($stock_details)){
														$TrfNumber = substr($stock_details->TransferID,5);
														$isedit = "Y";
														}else{
														$new_stocktransferNumbar = get_option('next_K1Stocktransfer_number_for_kirti');
														$new_stocktransferNumbar = '1' . $new_stocktransferNumbar;
														$__number = $new_stocktransferNumbar;
														$TrfNumber = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
														$isedit = '';
													}
												?>
												<div class="form-group">
													<label for="transferid">Transfer.No.</label>
													<div class="input-group">
														<span class="input-group-addon">
															<?php echo $prefix; ?>
														</span>
														<input type="text" name="transferid" id="transferid" class="form-control receiptsid" value="<?php echo $TrfNumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $stock_details->TransferID; ?>" <?php echo ($isedit) ? 'disabled' : '' ?>>
													    <input type="hidden" value="<?php echo $stock_details->TransferID;?>" name="edittransferid" id="edittransferid">
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
													$value = (isset($stock_details) ? _d(substr($stock_details->TransferDate,0,10)) : _d($date));
													echo render_date_input('trf_date','Transfer Date',$value);
												?>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($stock_details) ? $stock_details->TransferFrom : ''); ?>
												<div class="form-group" app-field-wrapper="centername">
													<label for="fromcentername" class="control-label">From Center Name</label>
													<select name="fromcentername" id="fromcentername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
														<option value=""></option>
														<?php
															foreach($centermaster as $center)
															{
																//echo '<option value="' . $center['CenterID'] . '" data-fromstatsid="' . $center['state'] . '">' . $center['CenterName'] . '</option>';
																echo '<option value="' . $center['CenterID'] . '"
																data-fromstatsid="' . $center['state'] . '"
																' . ($value == $center['CenterID'] ? 'selected' : '') . '>'
																. $center['CenterName'] .
																'</option>';
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<?php $value = (isset($stock_details) ? $stock_details->TransferTo : ''); ?>
												<div class="form-group" app-field-wrapper="centername">
													<label for="tocentername" class="control-label">To Center Name</label>
													<select name="tocentername" id="tocentername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
														<option value=""></option>
														<?php
															foreach($centermaster as $center)
															{
																//echo '<option value="' . $center['CenterID'] . '" data-tostatsid="' . $center['state'] . '">' . $center['CenterName'] . '</option>';
																echo '<option value="' . $center['CenterID'] . '"
																data-tostatsid="' . $center['state'] . '"
																' . ($value == $center['CenterID'] ? 'selected' : '') . '>'
																. $center['CenterName'] .
																'</option>';
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<?php $value = (isset($stock_details) ? $stock_details->AccountID : ''); ?>
												<?php $Isdisabled = (isset($stock_details) ? 'disabled' : ''); ?>
												<div class="form-group" app-field-wrapper="AccountID">
													<label for="AccountID" class="control-label">Select Party</label>
													<select name="AccountID" id="AccountID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php echo $Isdisabled; ?>>
														<option value=""></option>
														<?php
															foreach($clients as $client)
															{
																//echo '<option value="' . $value['AccountID'] . '">' . $value['company']. ' (' . $value['AccountID'] . ')</option>';
																$selected = ($value == $client['AccountID']) ? 'selected' : '';
																echo '<option value="' . $client['AccountID'] . '" ' . $selected . '>'
																. $client['company'] . ' (' . $client['AccountID'] . ')</option>';
															}
														?>
													</select>
													<?php if ($Isdisabled): ?>
													<input type="hidden" name="AccountID" id="vendor_value" value="<?php echo $value; ?>" />
													<?php else: ?>
													<input type="hidden" name="AccountID" id="vendor_value" value="<?php echo ($value ?: ''); ?>" />
													<?php endif; ?>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="AccountID">
													<?php $StatusVal = (isset($stock_details) ? $stock_details->OrderStatus : ''); ?>
													<label for="ordstat" class="control-label">Order Status</label>
													<select name="ordstat" id="ordstat" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" disabled>
														<option value=""></option>
														<option value="F"<?php echo ($StatusVal == "F") ? 'selected' : ''; ?>>Completed</option>
														<option value="C"<?php echo ($StatusVal == "C") ? 'selected' : ''; ?>>Cancel</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="VehicleNo">
													<?php $value = (isset($stock_details) ? $stock_details->VehicleNo : ''); ?>
													<label for="VehicleNo" class="control-label">Vehicle No</label>
													<input name="VehicleNo" id="VehicleNo" class="form-control" value="<?= $value;?>">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="DriverName">
													<?php $value = (isset($stock_details) ? $stock_details->DriverName : ''); ?>
													<label for="DriverName" class="control-label">Driver Name</label>
													<input name="DriverName" id="DriverName" class="form-control" value="<?= $value;?>">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="DriverMobile">
													<?php $value = (isset($stock_details) ? $stock_details->DriverMobile : ''); ?>
													<label for="DriverMobile" class="control-label">Driver Mobile</label>
													<input name="DriverMobile" id="DriverMobile" class="form-control" value="<?= $value;?>" onkeypress="return isNumber(event)">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="EwayBillNo">
													<?php $value = (isset($stock_details) ? $stock_details->EwayBillNo : ''); ?>
													<label for="EwayBillNo" class="control-label">E-way Bill No</label>
													<input name="EwayBillNo" id="EwayBillNo" class="form-control" value="<?= $value;?>" onkeypress="return isNumber(event)" readonly>
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
							<p class="bold p_style">Stock Transfer Detail</p>
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
												<input type="text" readonly class="form-control pull-left text-right" name="total_qty_in_mt" id="total_qty_in_mt" value="<?php echo $stock_details->TotalOrderQty; ?>">
											</td>
											<td>
												<label for="total_amt_in_mt">SubTotal</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_amt_in_mt" id="total_amt_in_mt" value="<?php echo $stock_details->Purchamt;?>">
											</td>
											<td>
												<label for="total_disc_in_mt">Discount Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_disc_in_mt" id="total_disc_in_mt" value="<?php echo $stock_details->Discamt;?>">
											</td>
											<td>
												<label  for="Total_value">Taxable Amt</label>
												<input  type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="<?php echo $stock_details->taxable_amt;?>" >
											</td>
											<td>
												<label  for="total_cgst_amt">CGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="<?php echo $stock_details->cgstamt;?>">
											</td>
											<td>
												<label  for="total_sgst_amt">SGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $stock_details->sgstamt;?>">
											</td>
											<td>
												<label  for="total_igst_amt">IGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="<?php echo $stock_details->igstamt;?>">
											</td>
											<td>
												<label  for="total_roundoff_amt">RoundOff Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="<?php echo $stock_details->RoundOffAmt;?>">
											</td>
											<td>
												<label  for="netpayableamt">Invoice Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="<?php echo $stock_details->Invamt;?>">
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
									<?php if (has_permission_new('KirtiOneStockTransfer', '', 'create')){
									?>
									<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit"><?php echo _l('submit'); ?></button>
									<?php
									}
									if (has_permission_new('KirtiOneStockTransfer', '', 'edit')){
										if($StatusVal !== 'C'){
										?>
										<button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit hidden"> UPDATE</button>
										<div class="col-md-1" style="margin-left: -5px;">
											<a href="#" id="cancelbtn" class="btn btn-danger cancel-new-order hidden">Cancel</a>
										</div>
										<?php
										}
									}
									?>
									<?php
								        if (has_permission_new('KirtiOneStockTransfer', '', 'edit') && !empty($stock_details) && empty($stock_details->EwayBillNo)){
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
				<h4 class="modal-title">Stock Transfer List</h4>
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
										<th style="width:8% ">TransferID</th>
										<th style="width:8% ">AccountID</th>
										<th style="width:8% ">Transfer Date</th>
										<th style="width:15% text-align:left;">Transfer From</th>
										<th style="width:15% text-align:left;">Transfer To</th>
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
<script type="text/javascript" language="javascript" >
    //For E WayBill
    $('.EWayBill').on('click', function()
    {
		var $btn = $(this);
        var TransferID = $('#edittransferid').val();
        var url = "<?php echo base_url(); ?>admin/K1Stock_transfer/generateEwayBill";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {TransferID: TransferID},
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
					alert_float('success', 'E-Way Bill generated Successfully');
				}else {
					var msg = data.Message;
					if(confirm(msg)){
						window.location.reload();
					}
				}
				window.location.reload();
			}
		})
	})
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
	$('#fromcentername').on('change', function(){
        ItemDataLoad();
	})
    function ItemDataLoad()
    {
		var CenterID = $("#fromcentername").val();
		hot.loadData([]);
		calculateTotalQuantity();
		calaulateSubTotal();
		calculateTotalDiscount();
		calculateTotalValue();
		calculateTotalCgstAmt();
		calculateTotalSgstAmt();
		calculateTotalIgstAmt();
		calculateTotalNetAmount();
		//alert(CenterID);
		if(CenterID != "" && CenterID != null){
		    $.ajax({
				url:"<?php echo admin_url(); ?>K1Stock_transfer/GetItemListData",
				dataType:"JSON",
				method:"POST",
				data:{CenterID:CenterID},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data)
				{
					var ItemsOptions = data.map(function(Listitems) {
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
					hot.render();
				}
			});
		}
	}
	$(document).ready(function(){
		$('#AccountID').on('change', function() {
			$('#vendor_value').val($(this).val());
		});
		var url = window.location.href;
		var regex = /\/AddEditStockTransfer\/([^\/?#]+)/;
		if (url.match(regex)) {
			$('#updatebtn').removeClass('hidden');
			$('#cancelbtn').removeClass('hidden');
			$('#savebtn').addClass('hidden');
			} else {
			$('#updatebtn').addClass('hidden');
			$('#cancelbtn').addClass('hidden');
			$('#savebtn').removeClass('hidden');
		}
		function load_data(from_date,to_date)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>K1Stock_transfer/load_data_for_stocktransfer",
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
			load_data(from_date,to_date);
		});
		$('.cancel-new-order').on('click', function()
		{
			var url = window.location.href;
			var segments = url.split('/');
			var TransferId = segments[segments.length - 1].split('#')[0];
			var userConfirmed = confirm("Are you sure you want to cancel the order?");
			if (userConfirmed)
			{
				$.ajax({
					url: "<?php echo admin_url(); ?>K1Stock_transfer/CancelItemsStockOrderWise",
					type: 'POST',
					data: {TransferId:TransferId},
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
			window.location.href = '<?php echo admin_url(); ?>K1Stock_transfer/AddEditStockTransfer';
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
<?php require 'K1Stock_transfer_js.php';?>
