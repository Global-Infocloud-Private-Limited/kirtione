<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php echo form_open($this->uri->uri_string(), array('id' => 'godown_transfer-form', 'class' => '_transaction_form')); ?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12">
							<div class="panel-body">
								<nav aria-label="breadcrumb">
									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
										<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
										<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>Godown Stock Transfer</b></li>
									</ol>
								</nav>
								<hr class="hr_style">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											<div class="col-md-2">
												<?php
												$fy = $this->session->userdata('finacial_year');
												$prefix = 'GTR<span id="prefix_year">' . $fy . '</span>';
												$TrfNumber = isset($godown_transfer_number) ? $godown_transfer_number : '0000021';
												?>
												<div class="form-group">
													<label for="transferid">Transfer.No.</label>
													<div class="input-group">
														<span class="input-group-addon"><?php echo $prefix; ?></span>
														<input type="text" name="transferid" id="transferid" class="form-control receiptsid" value="<?php echo $TrfNumber; ?>" data-isedit="" readonly>
														<input type="hidden" value="" name="edittransferid" id="edittransferid">
													</div>
												</div>
											</div>
											<div class="col-md-2">
												<?php
												$fy_new = $fy + 1;
												$lastdate_date = '20' . $fy_new . '-03-31';
												$curr_date = date('Y-m-d');
												$curr_date_new = new DateTime($curr_date);
												$last_date_yr = new DateTime($lastdate_date);
												$date = ($last_date_yr < $curr_date_new) ? $lastdate_date : date('Y-m-d');
												$value = _d($date);
												echo render_date_input('trf_date', 'Transfer Date', $value, ['readonly' => true]);
												?>
											</div>
											<div class="col-md-2">
												<?php $value = ''; ?>
												<div class="form-group" app-field-wrapper="centername">
													<label for="centername" class="control-label">Center Name</label>
													<select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
														<option value=""></option>
														<?php foreach ($centermaster as $center) {
															echo '<option value="' . $center['CenterID'] . '" data-stateid="' . $center['state'] . '" ' . ($value == $center['CenterID'] ? 'selected' : '') . '>' . $center['CenterName'] . '</option>';
														} ?>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="ordstat" class="control-label">Order Status</label>
													<select name="ordstat" id="ordstat" class="selectpicker form-control" data-none-selected-text="Non Selected" disabled>
														<option value="D" selected>Draft</option>
														<option value="F">Completed</option>
														<option value="C">Cancel</option>
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
							<p class="bold p_style">Godown Stock Transfer Detail</p>
							<hr class="hr_style" />
							<div class="" id="example"></div>
							<?php echo form_hidden('pur_order_detail'); ?>
							<div class="col-md-12">
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
												<label for="Total_value">Taxable Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="Total_value" id="Total_value" value="">
											</td>
											<td>
												<label for="total_cgst_amt">CGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" id="total_cgst_amt" name="total_cgst_amt" value="">
											</td>
											<td>
												<label for="total_sgst_amt">SGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_sgst_amt" id="total_sgst_amt" value="">
											</td>
											<td>
												<label for="total_igst_amt">IGST Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_igst_amt" id="total_igst_amt" value="">
											</td>
											<td>
												<label for="total_roundoff_amt">RoundOff Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="total_roundoff_amt" id="total_roundoff_amt" value="">
											</td>
											<td>
												<label for="netpayableamt">Invoice Amt</label>
												<input type="text" readonly class="form-control pull-left text-right" name="netpayableamt" id="netpayableamt" value="">
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
								<div class="btn-bottom-toolbar text-right" style="width: 100%; display: flex; justify-content: flex-end; align-items: center;">
									<div class="col-md-1" style="margin-left: 0px; margin-right: -30px;">
										<a href="#" class="btn btn-primary reset-new-order" id="resetbutton">Reset</a>
									</div>
									<div class="col-md-1" style="margin-left: 10px;">
										<a href="#" class="btn btn-warning edit-new-order">View List</a>
									</div>
									<?php if (!empty($can_create)) { ?>
										<button type="button" id="savebtn" class="btn btn-info mleft10"><?php echo _l('submit'); ?></button>
									<?php } ?>
									<?php if (!empty($can_edit)) { ?>
										<button type="button" id="updatebtn" class="btn btn-info mleft10 hidden"> UPDATE</button>
										<a href="#" id="approvebtn" class="btn btn-success approve-new-order mleft10 hidden">Approve</a>
										<a href="#" id="cancelbtn" class="btn btn-danger cancel-new-order mleft10 hidden">Cancel</a>
									<?php } ?>
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
<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-xl" style="max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Godown Stock Transfer List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				<?php
				$fy_new = $fy + 1;
				$lastdate_date = '20' . $fy_new . '-03-31';
				$curr_date = date('Y-m-d');
				$curr_date_new = new DateTime($curr_date);
				$last_date_yr = new DateTime($lastdate_date);
				if ($last_date_yr < $curr_date_new) {
					$to_date = '31/03/20' . $fy_new;
					$from_date = '01/03/20' . $fy_new;
				} else {
					$from_date = '01/' . date('m') . '/' . date('Y');
					$to_date = date('d/m/Y');
				}
				?>
				<div class="row">
					<div class="col-md-3"><?php echo render_date_input('from_date', 'From', $from_date); ?></div>
					<div class="col-md-3"><?php echo render_date_input('to_date', 'To', $to_date); ?></div>
					<div class="col-md-3"><br><button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button></div>
					<div class="col-md-3"><br><input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." style="float: right;"></div>
					<div class="col-md-12">
						<div class="table_purchase_report">
							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
								<thead>
									<tr>
										<th>TransferID</th>
										<th>Transfer Date</th>
										<th>Center Name</th>
										<th>Order Status</th>
										<th>Inv. Amt</th>
									</tr>
								</thead>
								<tbody></tbody>
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
	.table_purchase_report { overflow: auto; max-height: 60vh; width: 100%; position: relative; top: 0px; }
	.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; background: #50607b; color: #fff !important; }
	#table_purchase_report tr:hover { background-color: #ccc; }
	#table_purchase_report td:hover { cursor: pointer; }
	#example .handsontable thead th,
	#example .ht_clone_top thead th,
	#example .ht_clone_top_inline_start thead th {
		font-weight: bold !important;
	}
	#example .handsontable thead th .colHeader,
	#example .ht_clone_top thead th .colHeader {
		font-weight: bold !important;
	}
	#transferid[readonly],
	#trf_date[readonly] {
		background-color: #eee;
		cursor: not-allowed;
	}
</style>
<script type="text/javascript">
	var godownAjaxUrl = "<?php echo admin_url('K1Stock_transfer/GetGodownStockTransferData'); ?>";
	var godownPageUrl = "<?php echo admin_url('K1Stock_transfer/AddEditGodownStockTransfer'); ?>";

	function parseGodownResponse(response) {
		return typeof response === 'string' ? JSON.parse(response) : response;
	}

	function getGodownTransferId() {
		return $('#edittransferid').val() || '';
	}

	function recalculateGodownFooterTotals() {
		calculateTotalQuantity();
		calaulateSubTotal();
		calculateTotalDiscount();
		calculateTotalValue();
		calculateTotalCgstAmt();
		calculateTotalSgstAmt();
		calculateTotalIgstAmt();
		calculateTotalNetAmount();
	}

	function clearGodownFooterTotals() {
		$('#total_qty_in_mt, #total_amt_in_mt, #total_disc_in_mt, #Total_value, #total_cgst_amt, #total_sgst_amt, #total_igst_amt, #total_roundoff_amt, #netpayableamt').val('');
		recalculateGodownFooterTotals();
	}

	function applyGodownItemCodeToGrid(itemCode) {
		if (typeof hot === 'undefined' || !itemCode) return;
		var ItemsOptions = itemCode.map(function(Listitems) {
			return { id: String(Listitems.id), label: Listitems.label, ProductName: Listitems.ProductName };
		});
		var colIndex = hot.propToCol('id');
		var rowCount = hot.countRows();
		for (var row = 0; row < rowCount; row++) {
			hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });
		}
		hot.render();
	}

	function applyGodownBatchOptionsToGrid(items, centerId, transferId) {
		if (typeof hot === 'undefined' || !items || !items.length || !centerId) return;
		var batchColIndex = hot.propToCol('BatchNo');
		items.forEach(function(rowData, row) {
			var itemId = rowData.id || rowData.ItemID;
			var currentBatch = rowData.BatchNo ? String(rowData.BatchNo) : '';
			if (!itemId) return;
			$.post(godownAjaxUrl, {
				action: 'item_details',
				ItemID: itemId,
				CenterID: centerId,
				TransferID: transferId || ''
			}).done(function(response) {
				response = parseGodownResponse(response);
				var batchOptions = buildGodownBatchChosenOptions(response.BatchList, currentBatch);
				hot.setCellMeta(row, batchColIndex, 'chosenOptions', { data: batchOptions });
				if (currentBatch) {
					hot.setDataAtCell(row, batchColIndex, currentBatch, 'loadGodownBatch');
				}
				hot.render();
			});
		});
	}

	function setGodownFormReadonly() {
		$('#centername').prop('disabled', true).selectpicker('refresh');
		if (typeof hot !== 'undefined') {
			hot.updateSettings({ readOnly: true });
		}
	}

	function setGodownFormEditable() {
		$('#centername').prop('disabled', false).selectpicker('refresh');
		if (typeof hot !== 'undefined') {
			hot.updateSettings({ readOnly: false });
		}
	}

	/** Toolbar: new | draft (D) | completed (F) | cancelled (C) */
	function applyGodownToolbar(orderStatus) {
		$('#savebtn, #updatebtn, #approvebtn, #cancelbtn').addClass('hidden');
		if (orderStatus === 'new') {
			$('#savebtn').removeClass('hidden');
		} else if (orderStatus === 'D') {
			$('#updatebtn, #approvebtn, #cancelbtn').removeClass('hidden');
		}
	}

	function applyGodownFormMode(orderStatus) {
		if (orderStatus === 'D') {
			setGodownFormEditable();
		} else if (orderStatus !== 'new') {
			setGodownFormReadonly();
		}
		applyGodownToolbar(orderStatus);
	}

	function populateGodownFormFromAjax(data) {
		var h = data.header;
		$('#edittransferid').val(h.TransferID);
		$('#transferid').val(h.transfer_suffix).prop('readonly', true).attr('data-isedit', 'Y');
		$('input[name="trf_date"]').val(h.TransferDate);
		$('#centername').val(h.CenterID).selectpicker('refresh');
		$('#ordstat').val(h.OrderStatus).selectpicker('refresh');
		$('#total_qty_in_mt').val(h.total_qty_in_mt);
		$('#total_amt_in_mt').val(h.total_amt_in_mt);
		$('#total_disc_in_mt').val(h.total_disc_in_mt);
		$('#Total_value').val(h.Total_value);
		$('#total_cgst_amt').val(h.total_cgst_amt);
		$('#total_sgst_amt').val(h.total_sgst_amt);
		$('#total_igst_amt').val(h.total_igst_amt);
		$('#total_roundoff_amt').val(h.total_roundoff_amt);
		$('#netpayableamt').val(h.netpayableamt);
		if (typeof hot !== 'undefined') {
			var items = (data.items || []).map(function(item) {
				if (item.id !== undefined && item.id !== null && item.id !== '') {
					item.id = String(item.id);
				}
				if (item.BatchNo !== undefined && item.BatchNo !== null && item.BatchNo !== '') {
					item.BatchNo = String(item.BatchNo);
				}
				return item;
			});
			hot.loadData(items);
			applyGodownItemCodeToGrid(data.item_code || []);
			applyGodownBatchOptionsToGrid(items, h.CenterID, h.TransferID);
			hot.render();
		}
		if (h.OrderStatus === 'D') {
			applyGodownFormMode('D');
		} else {
			applyGodownFormMode(h.OrderStatus);
		}
		if (window.history && window.history.pushState) {
			window.history.pushState(null, '', godownPageUrl + '/' + h.TransferID);
		}
	}

	function loadGodownTransfer(transferId) {
		$.post(godownAjaxUrl, { action: 'load_transfer', TransferID: transferId }, function(response) {
			response = parseGodownResponse(response);
			if (response.success) {
				populateGodownFormFromAjax(response);
				$('#transfer-modal').modal('hide');
			} else {
				alert_float('warning', response.message || 'Failed to load transfer');
			}
		}, 'json');
	}

	function resetGodownForm() {
		$.post(godownAjaxUrl, { action: 'new_transfer' }, function(response) {
			response = parseGodownResponse(response);
			if (!response.success) return;
			$('#edittransferid').val('');
			$('#transferid').val(response.transfer_number).prop('readonly', true).attr('data-isedit', '');
			$('#ordstat').val('D').selectpicker('refresh');
			$('#centername').val('').selectpicker('refresh');
			setGodownFormEditable();
			applyGodownToolbar('new');
			if (typeof hot !== 'undefined') {
				hot.loadData([]);
				hot.render();
			}
			clearGodownFooterTotals();
			if (window.history && window.history.pushState) {
				window.history.pushState(null, '', godownPageUrl);
			}
		}, 'json');
	}

	function submitGodownTransfer(action) {
		if (!$('#godown_transfer-form').valid()) return;
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));
		var postData = $('#godown_transfer-form').serializeArray();
		postData.push({ name: 'action', value: action });
		if (action === 'update') {
			postData.push({ name: 'TransferID', value: getGodownTransferId() });
		}
		$.post(godownAjaxUrl, postData, function(response) {
			response = parseGodownResponse(response);
				if (response.success) {
					alert_float('success', response.message);
					if (action === 'save' && response.TransferID) {
						loadGodownTransfer(response.TransferID);
					} else if (action === 'update' && getGodownTransferId()) {
						loadGodownTransfer(getGodownTransferId());
					}
				} else {
				alert_float('warning', response.message || 'Something went wrong...');
			}
		}, 'json');
	}

	function loadGodownItemListForCenter() {
		var centerId = $('#centername').val();
		hot.loadData([]);
		clearGodownFooterTotals();
		if (!centerId) return;
		$.post(godownAjaxUrl, { action: 'item_list', CenterID: centerId }, function(data) {
			applyGodownItemCodeToGrid(data);
		}, 'json');
	}
	$(document).ready(function() {
		$('#godown_transfer-form').on('submit', function(e) { e.preventDefault(); });
		$('#centername').on('change', loadGodownItemListForCenter);
		applyGodownToolbar('new');
		$('#transferid, input[name="trf_date"]').prop('readonly', true);
		setTimeout(function() {
			var $trfDate = $('input[name="trf_date"]');
			if ($trfDate.hasClass('hasDatepicker')) {
				$trfDate.datepicker('destroy');
			}
			$trfDate.closest('.input-group.date').find('.input-group-addon').css('pointer-events', 'none');
		}, 300);

		var urlMatch = window.location.href.match(/\/AddEditGodownStockTransfer\/([^\/?#]+)/);
		if (urlMatch && urlMatch[1]) {
			loadGodownTransfer(urlMatch[1]);
		}

		function load_data(from_date, to_date) {
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Stock_transfer/load_data_for_godownstocktransfer",
				method: "POST",
				data: { from_date: from_date, to_date: to_date },
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
		$(document).on('click', '.godown-list-row', function() {
			loadGodownTransfer($(this).data('transfer-id'));
		});
		$('.edit-new-order').on('click', function() {
			$('#transfer-modal').modal('show');
			load_data($('#from_date').val(), $('#to_date').val());
		});
		$('#search_data').on('click', function() {
			load_data($('#from_date').val(), $('#to_date').val());
		});
		$('.cancel-new-order').on('click', function(e) {
			e.preventDefault();
			var transferId = getGodownTransferId();
			if (!transferId || !confirm('Are you sure you want to cancel the order?')) return;
			$.post(godownAjaxUrl, { action: 'cancel', TransferID: transferId }, function(response) {
				response = parseGodownResponse(response);
				if (response.success) {
					alert_float('success', response.message);
					$('#ordstat').val('C').selectpicker('refresh');
					applyGodownFormMode('C');
				} else {
					alert_float('warning', response.message || 'Something went wrong...');
				}
			}, 'json');
		});
		$('.approve-new-order').on('click', function(e) {
			e.preventDefault();
			var transferId = getGodownTransferId();
			if (!transferId || !confirm('Are you sure you want to approve this order?')) return;
			$.post(godownAjaxUrl, { action: 'approve', TransferID: transferId }, function(response) {
				response = parseGodownResponse(response);
				if (response.success) {
					alert_float('success', response.message);
					$('#ordstat').val('F').selectpicker('refresh');
					applyGodownFormMode('F');
				} else {
					alert_float('warning', response.message || 'Something went wrong...');
				}
			}, 'json');
		});
		$('#savebtn').on('click', function() { submitGodownTransfer('save'); });
		$('#updatebtn').on('click', function() { submitGodownTransfer('update'); });
		$('#resetbutton').on('click', function(e) {
			e.preventDefault();
			resetGodownForm();
		});
	});
	function myFunction2() {
		var input = document.getElementById("myInput1");
		var filter = input.value.toUpperCase();
		var table = document.querySelector(".table_purchase_report");
		var tr = table.getElementsByTagName("tr");
		for (var i = 2; i < tr.length; i++) {
			var tdArray = tr[i].getElementsByTagName("td");
			var rowContainsSearchTerm = false;
			for (var j = 0; j < tdArray.length; j++) {
				if (tdArray[j]) {
					var txtValue = tdArray[j].textContent || tdArray[j].innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						rowContainsSearchTerm = true;
						break;
					}
				}
			}
			tr[i].style.display = rowContainsSearchTerm ? "" : "none";
		}
	}
</script>
<link href="https://cdn.jsdelivr.net/npm/handsontable@11.1.0/dist/handsontable.full.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/handsontable@11.1.0/dist/handsontable.full.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/mydea/handsontable-chosen-editor@master/handsontable-chosen-editor.js"></script>
<?php require 'GodownStock_transfer_js.php'; ?>
