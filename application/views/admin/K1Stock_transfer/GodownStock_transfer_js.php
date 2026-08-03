<script>
	$(function() {
		appValidateForm($('#godown_transfer-form'), {
			transferid: 'required',
			centername: 'required',
			trf_date: {
				required: true,
				remote: {
					url: site_url + 'admin/misc/checkpurch_val',
					type: 'post',
					data: {
						order_date: function() { return $('input[name="trf_date"]').val(); },
						PurchID: function() { return $('input[name="transferid"]').val(); }
					}
				}
			}
		});
	});

	function buildGodownBatchChosenOptions(batchList, currentBatch) {
		var options = [];
		var current = currentBatch ? String(currentBatch) : '';
		if (batchList && batchList.length) {
			batchList.forEach(function(batch) {
				if (!batch || !batch.BatchNo) return;
				if (parseFloat(batch.Stock) > 0 || (current && String(batch.BatchNo) === current)) {
					options.push({ id: String(batch.BatchNo), label: String(batch.BatchNo) });
				}
			});
		}
		if (current && !options.some(function(opt) { return opt.id === current; })) {
			options.push({ id: current, label: current });
		}
		return options;
	}

	function clearGodownGridRow(row) {
		for (var col = 1; col <= 15; col++) {
			hot.setDataAtCell(row, col, '');
		}
	}

	function fillGodownItemDetails(row, item) {
		hot.setDataAtCell(row, 1, item.BrandName);
		hot.setDataAtCell(row, 2, item.unit);
		hot.setDataAtCell(row, 3, item.PackingQty);
		hot.setDataAtCell(row, 4, item.PackingWeight);
		hot.setDataAtCell(row, 8, '');
		hot.setDataAtCell(row, 10, '');
		hot.setDataAtCell(row, 11, item.taxrate);
		hot.setDataAtCell(row, 12, '0');
		hot.setDataAtCell(row, 13, '0');
		hot.setDataAtCell(row, 14, '0');
		hot.setDataAtCell(row, 15, '0');
		hot.setDataAtCell(row, 9, item.PurchRate);

		var batchCol = hot.propToCol('BatchNo');
		var batchOptions = buildGodownBatchChosenOptions(item.BatchList, '');
		hot.setCellMeta(row, batchCol, 'chosenOptions', { data: batchOptions });
		hot.setDataAtCell(row, batchCol, batchOptions.length ? batchOptions[0].id : '');
		hot.render();
	}

	function recalculateGodownRowAmount(row) {
		var qty = parseFloat(hot.getDataAtCell(row, 8)) || 0;
		var rate = parseFloat(hot.getDataAtCell(row, 9)) || 0;
		var discount = parseFloat(hot.getDataAtCell(row, 10)) || 0;
		var gst = parseFloat(hot.getDataAtCell(row, 11)) || 0;
		if (!rate || !qty) return;

		var amount = rate * qty;
		if (discount) {
			amount -= amount * discount / 100;
		}
		var totalGst = amount * gst / 100;
		hot.setDataAtCell(row, 15, (amount + totalGst).toFixed(2));
		hot.setDataAtCell(row, 12, (totalGst / 2).toFixed(2));
		hot.setDataAtCell(row, 13, (totalGst / 2).toFixed(2));
		hot.setDataAtCell(row, 14, '0.00');
	}

	<?php if (!empty($pur_order_detail)) { ?>
	var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;
	<?php } else { ?>
	var dataObject = [];
	<?php } ?>

	var hot = new Handsontable(document.querySelector('#example'), {
		data: dataObject,
		columns: [
			{ data: 'id', renderer: customDropdownRenderer, editor: 'chosen', chosenOptions: { data: <?php echo json_encode($item_code); ?> } },
			{ data: 'Brand', type: 'text', readOnly: true },
			{ data: 'Measuredin', type: 'text', readOnly: true },
			{ data: 'PackingQty', type: 'text', className: 'htLeft', readOnly: true },
			{ data: 'Packingwgt', type: 'text', className: 'htLeft', readOnly: true },
			{ data: 'BatchNo', renderer: customRenderer, editor: 'chosen', chosenOptions: { data: [] } },
			{ data: 'StockQty', type: 'numeric', className: 'htLeft', readOnly: true },
			{ data: 'ExpDate', type: 'date', width: 70, readOnly: true },
			{ data: 'OrderQty', type: 'numeric', className: 'htLeft' },
			{ data: 'PurchRate', type: 'text', readOnly: true },
			{ data: 'Discount', type: 'numeric', className: 'htLeft', readOnly: true },
			{ data: 'gst', type: 'text', readOnly: true },
			{ data: 'cgstamt', type: 'text', readOnly: true },
			{ data: 'sgstamt', type: 'text', readOnly: true },
			{ data: 'igstamt', type: 'text', readOnly: true },
			{ data: 'Netamt', type: 'text', readOnly: true }
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		columnHeaderHeight: 40,
		minRows: 10,
		minSpareRows: 1,
		rowHeaders: true,
		colWidths: [100, 80, 60, 60, 70, 70, 50, 70, 60, 50, 50, 50, 50, 50, 70],
		colHeaders: [
			'<?php echo _l('Item Name'); ?>',
			'<?php echo _l('Brand'); ?>',
			'<?php echo _l('Unit'); ?>',
			'<?php echo _l('Pack Qty'); ?>',
			'<?php echo _l('Pack Wt(kg)'); ?>',
			'<?php echo _l('Batch No'); ?>',
			'<?php echo _l('Stock(Loose)'); ?>',
			'<?php echo _l('Exp.Date'); ?>',
			'<?php echo _l('Qty(Loose)'); ?>',
			'<?php echo _l('Purch Rate'); ?>',
			'<?php echo _l('Disc Amt'); ?>',
			'<?php echo _l('GST %'); ?>',
			'<?php echo _l('CGST Amt'); ?>',
			'<?php echo _l('SGST Amt'); ?>',
			'<?php echo _l('IGST Amt'); ?>',
			'<?php echo _l('Net Amt'); ?>'
		],
		columnSorting: { indicator: true },
		autoColumnSize: { samplingRatio: 23 },
		mergeCells: true,
		contextMenu: true,
		manualRowMove: true,
		manualColumnMove: true,
		multiColumnSorting: { indicator: true },
		filters: true,
		manualRowResize: true,
		manualColumnResize: true
	});

	hot.addHook('afterChange', function(changes, src) {
		if (!changes) return;

		changes.forEach(function(change) {
			var row = change[0];
			var prop = change[1];
			var newValue = change[3];
			var centerId = $('#centername').val();
			var transferId = getGodownTransferId();

			if (prop === 'id') {
				if (newValue == null) {
					clearGodownGridRow(row);
					return;
				}
				if (!centerId) {
					alert('Please Select Center');
					hot.setDataAtCell(row, 0, null);
					return;
				}
				$.post(godownAjaxUrl, {
					action: 'item_details',
					ItemID: newValue,
					CenterID: centerId,
					TransferID: transferId
				}).done(function(response) {
					fillGodownItemDetails(row, parseGodownResponse(response));
				});
				return;
			}

			if (prop === 'BatchNo') {
				if (src === 'loadGodownBatch') return;
				var itemId = hot.getDataAtCell(row, 0);
				if (!newValue) {
					hot.setDataAtCell(row, 8, '0');
					hot.setDataAtCell(row, 9, '0');
					return;
				}
				$.post(godownAjaxUrl, {
					action: 'batch_stock',
					ItemID: itemId,
					BatchID: newValue,
					CenterID: centerId,
					TransferID: transferId
				}).done(function(response) {
					response = parseGodownResponse(response);
					if (response.length > 0) {
						hot.setDataAtCell(row, 6, response[0].Stock);
						hot.setDataAtCell(row, 7, response[0].ExpDate);
						hot.setDataAtCell(row, 9, response[0].PurchRate);
					} else {
						hot.setDataAtCell(row, 6, '');
						hot.setDataAtCell(row, 7, '');
						hot.setDataAtCell(row, 9, '');
					}
				});
				return;
			}

			if (prop === 'OrderQty') {
				var stock = parseFloat(hot.getDataAtCell(row, 6)) || 0;
				var qty = parseFloat(newValue) || 0;
				if (qty > stock) {
					alert('Qty Should Be Less Than Available Stock');
					hot.setDataAtCell(row, 8, '0');
					hot.setDataAtCell(row, 12, '0');
					hot.setDataAtCell(row, 13, '0');
					hot.setDataAtCell(row, 14, '0');
					hot.setDataAtCell(row, 15, '0');
					return;
				}
			}

			if (prop === 'OrderQty' || prop === 'Discount') {
				recalculateGodownRowAmount(row);
			}

			recalculateGodownFooterTotals();
		});
	});

	function customRenderer(instance, td, row, col, prop, value, cellProperties) {
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		if (td.innerHTML !== '') {
			td.className = 'htRight';
		}
	}

	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
		var optionsList = cellProperties.chosenOptions.data;
		if (!optionsList || !optionsList.length) {
			Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
			return td;
		}
		var labels = [];
		var values = String(value).split('|');
		for (var i = 0; i < optionsList.length; i++) {
			if (values.indexOf(String(optionsList[i].id)) > -1) {
				labels.push(optionsList[i].ProductName);
			}
		}
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, labels.join(', '), cellProperties);
		return td;
	}

	function calculateTotalQuantity() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var qty = parseFloat(hot.getDataAtCell(i, 8));
			if (qty) total += qty;
		}
		$('#total_qty_in_mt').val(total.toFixed(2));
	}

	function calaulateSubTotal() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var qty = parseFloat(hot.getDataAtCell(i, 8));
			var rate = parseFloat(hot.getDataAtCell(i, 9));
			if (qty > 0 && rate > 0) total += qty * rate;
		}
		$('#total_amt_in_mt').val(total.toFixed(2));
	}

	function calculateTotalDiscount() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var qty = parseFloat(hot.getDataAtCell(i, 8));
			var rate = parseFloat(hot.getDataAtCell(i, 9));
			var disc = parseFloat(hot.getDataAtCell(i, 10));
			if (qty > 0 && rate > 0 && disc > 0) total += qty * rate * (disc / 100);
		}
		$('#total_disc_in_mt').val(total.toFixed(2));
	}

	function calculateTotalValue() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var qty = parseFloat(hot.getDataAtCell(i, 8));
			var rate = parseFloat(hot.getDataAtCell(i, 9));
			var disc = parseFloat(hot.getDataAtCell(i, 10)) || 0;
			if (qty > 0 && rate > 0) total += qty * rate * (1 - disc / 100);
		}
		$('#Total_value').val(total.toFixed(2));
	}

	function calculateTotalCgstAmt() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var val = parseFloat(hot.getDataAtCell(i, 12));
			if (val) total += val;
		}
		$('#total_cgst_amt').val(total.toFixed(2));
	}

	function calculateTotalSgstAmt() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var val = parseFloat(hot.getDataAtCell(i, 13));
			if (val) total += val;
		}
		$('#total_sgst_amt').val(total.toFixed(2));
	}

	function calculateTotalIgstAmt() {
		var total = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var val = parseFloat(hot.getDataAtCell(i, 14));
			if (val) total += val;
		}
		$('#total_igst_amt').val(total.toFixed(2));
	}

	function calculateTotalNetAmount() {
		var totalNetAmount = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var qty = parseFloat(hot.getDataAtCell(i, 8));
			var rate = parseFloat(hot.getDataAtCell(i, 9));
			var discount = parseFloat(hot.getDataAtCell(i, 10)) || 0;
			var gst = parseFloat(hot.getDataAtCell(i, 11)) || 0;
			if (qty > 0 && rate > 0) {
				var amount = rate * qty;
				if (discount) amount -= amount * discount / 100;
				totalNetAmount += amount + (amount * gst / 100);
			}
		}
		var rounded = Math.round(totalNetAmount);
		$('input[name="netpayableamt"]').val(rounded.toFixed(2));
		$('input[name="total_roundoff_amt"]').val((totalNetAmount - rounded).toFixed(2));
	}
</script>

<style>
	.htLeft { text-align: left !important; }
</style>
