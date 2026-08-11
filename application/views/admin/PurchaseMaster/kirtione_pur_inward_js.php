<script>
	function removeCommas(str) {
		"use strict";
		return (str.replace(/,/g, ''));
	}
	$(function () {
		"use strict";
		validate_purorder_form();
		function validate_purorder_form(selector) {
			selector = typeof (selector) == 'undefined' ? '#pur_order-form' : selector;
			appValidateForm($(selector), {
				pro_orderid: 'required',
				prd_date: 'required',
				centername: 'required',
				vendor: 'required',
				Pr_no: 'required',
				mobile_no: 'required',
				state: 'required',
				// OthEffectOn:'required',
				// OtherAmt:'required',
				//InvoiceNo:'required',
				prd_date: {
					remote: {
						url: site_url + "admin/misc/checkpurch_val",
						type: 'post',
						data: {
							order_date: function () {
								return $('input[name="prd_date"]').val();
							},
							PurchID: function () {
								return $('input[name="pur_order_number"]').val();
							}
						}
					}
				},
				/*OthEffectOn: {
					required: function(element) {
					return $('#OtherAmt').val() != '';
					}
				},*/
			});
		}
	});
	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	function normalizeHotNumeric(value) {
		"use strict";
		var parsedValue = parseFloat(value);
		return isNaN(parsedValue) ? 0 : parsedValue;
	}
	function validateOrderQtyAgainstPoLimit(hotInstance, row, oldValue, newValue) {
		"use strict";
		if (newValue === undefined || newValue === null || newValue === '') {
			return true;
		}
		var enteredQty = normalizeHotNumeric(newValue);
		var rowPOrderQty = normalizeHotNumeric(hotInstance.getDataAtCell(row, 7));
		if (enteredQty > rowPOrderQty) {
			var revertedValue = (oldValue === undefined || oldValue === null || oldValue === '') ? 0 : oldValue;
			hotInstance.setDataAtCell(row, 8, revertedValue);
			alert('Qty should be less than or equal to PO Qty');
			return false;
		}
		var itemId = hotInstance.getDataAtCell(row, 0);
		if (itemId === undefined || itemId === null || itemId === '') {
			return true;
		}
		var firstMatchRow = -1;
		var totalSameItemQty = 0;
		var totalRows = hotInstance.countRows();
		for (var i = 0; i < totalRows; i++) {
			if (String(hotInstance.getDataAtCell(i, 0)) !== String(itemId)) {
				continue;
			}
			if (firstMatchRow === -1) {
				firstMatchRow = i;
			}
			var currentQty = normalizeHotNumeric(hotInstance.getDataAtCell(i, 8));
			if (i === row) {
				currentQty = enteredQty;
			}
			totalSameItemQty += currentQty;
		}
		if (firstMatchRow !== -1) {
			var firstRowPoQty = normalizeHotNumeric(hotInstance.getDataAtCell(firstMatchRow, 7));
			if (totalSameItemQty > firstRowPoQty) {
				var revertedValueForDuplicate = (oldValue === undefined || oldValue === null || oldValue === '') ? 0 : oldValue;
				hotInstance.setDataAtCell(row, 8, revertedValueForDuplicate);
				alert('Total qty for duplicate items should not exceed the first PO Qty');
				return false;
			}
		}
		return true;
	}
	<?php
	if (isset($pur_order_detail) && !empty($pur_order_detail)) { ?>
		var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;
	<?php
	} else { ?>
		var dataObject = [];
	<?php } ?>
	var hotElement = document.querySelector('#example');
	var hotElementContainer = hotElement.parentNode;
	var allItemList = <?php echo json_encode($item_code); ?>;
	var purchaseOrderItemList = [];
	var purchaseOrderItemData = [];
	if (Array.isArray(dataObject) && dataObject.length > 0) {
		dataObject.forEach(function (item) {
			if (!item || !item.id) {
				return;
			}
			purchaseOrderItemData.push(item);
			var alreadyExists = purchaseOrderItemList.some(function (listItem) {
				return String(listItem.id) === String(item.id);
			});
			if (!alreadyExists) {
				purchaseOrderItemList.push({
					id: item.id,
					ProductName: item.ProductName,
					hsn_code: item.hsn_code,
					BrandID: item.BrandID,
					UOM: item.UOM,
					PackingQty: item.PackingQty,
					SaleUnit: item.SaleUnit,
					SOQty: item.SOQty,
					BasicRate: item.BasicRate,
					DiscAmt: item.DiscAmt,
					GSTPer: item.GSTPer
				});
			}
		});
	}
	function getpurchaseOrderItemData(itemId) {
		for (var i = 0; i < purchaseOrderItemData.length; i++) {
			if (String(purchaseOrderItemData[i].id) === String(itemId)) {
				return purchaseOrderItemData[i];
			}
		}
		return null;
	}
	/*
	|--------------------------------------------------------------------------
	| Create batch dropdown options
	|--------------------------------------------------------------------------
	*/
	function createBatchOptions(batchList) {
		var batchOptions = [];
		if (!Array.isArray(batchList)) {
			return batchOptions;
		}
		batchList.forEach(function (batch) {
			if (parseFloat(batch.Stock) > 0) {
				batchOptions.push({
					id: batch.BatchNo,
					label: batch.BatchNo,
					Stock: batch.Stock,
					ExpDate: batch.ExpDate || ''
				});
			}
		});
		return batchOptions;
	}
	/*
	|--------------------------------------------------------------------------
	| Apply batch dropdown to a Handsontable row
	|--------------------------------------------------------------------------
	*/
	function applyBatchListToRow(row, batchList, selectFirstBatch) {
		var batchOptions = createBatchOptions(batchList);
		hot.setCellMeta(row, 6, 'chosenOptions', {
			data: batchOptions
		});
		if (selectFirstBatch === true && batchOptions.length > 0) {
			hot.setDataAtRowProp(
				row,
				'BatchList',
				batchOptions[0].id,
				'item-batch-load'
			);
			hot.setDataAtRowProp(
				row,
				'StockQty',
				batchOptions[0].Stock,
				'item-batch-load'
			);
			hot.setDataAtRowProp(
				row,
				'ExpDate',
				batchOptions[0].ExpDate || '',
				'item-batch-load'
			);
		} else {
			hot.setDataAtRowProp(
				row,
				'BatchList',
				'',
				'item-batch-load'
			);
			hot.setDataAtRowProp(
				row,
				'StockQty',
				'',
				'item-batch-load'
			);
			hot.setDataAtRowProp(
				row,
				'ExpDate',
				'',
				'item-batch-load'
			);
		}
		return batchOptions;
	}
	var hotSettings = {
		data: dataObject,
		columns: [
			{
				data: 'id',
				renderer: customDropdownRenderer,
				editor: "chosen",
				readOnly: false
			},
			// {
			// 	data: 'id',
			// 	renderer: customDropdownRenderer,
			// 	editor: "chosen",      
			// 	chosenOptions: {
			// 		data: <?php // echo json_encode($item_code); 
										?>
			// 	},        
			// 	readOnly: true
			// },
			{
				data: 'hsn_code',
				type: 'text',
				readOnly: true
			},
			{
		data: 'Brand',
			type: 'text',
				readOnly: true
	},
	{
		data: 'Measuredin',
			type: 'text',
				readOnly: true
	},
	{
		data: 'PackingQty',
			type: 'text',
				className: 'htLeft',
					readOnly: true
	},
	{
		data: 'Packingwgt',
			type: 'text',
				className: 'htLeft',
					readOnly: true
	},
	{
		data: 'PurchUnit',
			type: 'text',
				readOnly: true
	},
	{
		data: 'POrderQty',
			type: 'numeric',
				className: 'htLeft',
					readOnly: true
	},
	{
		data: 'OrderQty',
			type: 'numeric',
				className: 'htLeft'
	},
	{
		data: 'PurchRate',
			type: 'text',
				readOnly: false
	},
	{
		data: 'Discount',
			type: 'numeric',
				className: 'htLeft'
	},
	{
		data: 'gst',
			type: 'text',
				readOnly: true
	},
	{
		data: 'cgstamt',
			type: 'text',
				readOnly: true
	},
	{
		data: 'sgstamt',
			type: 'text',
				readOnly: true
	},
	{
		data: 'igstamt',
			type: 'text',
				readOnly: true
	},
	{
		data: 'Netamt',
			type: 'text',
				readOnly: true
	},
	{
		data: 'BatchNo',
			type: 'text',
				readOnly: false
	},
	{
		data: 'ExpDate',
			type: 'date',
				width: 70,
			},
	{
		data: 'Ordinalno',
			type: 'text',
				width: 20,
			},
		],
	cells: function(row, col) {
		var cellProperties = {};
		if (col === 0) {
			cellProperties.editor = "chosen";
			cellProperties.renderer = customDropdownRenderer;
			cellProperties.readOnly = false;
			cellProperties.chosenOptions = {
				data: purchaseOrderItemList
			};
		}
		return cellProperties;
	},
	minSpareRows: 1,
		licenseKey: 'non-commercial-and-evaluation',
			stretchH: 'all',
				width: '100%',
					height: '400px',
						columnHeaderHeight: 40,
							minRows: 200,
								maxRows: 150,
									rowHeaders: true,
										colWidths: [100, 80, 60, 60, 70, 70, 50, 70, 70, 60, 50, 50, 50, 50, 70],
											colHeaders: [
												'<?php echo _l('Item Name'); ?>',
												'<?php echo _l('Hsn Code'); ?>',
												'<?php echo _l('Brand'); ?>',
												'<?php echo _l('MeasuredIn'); ?>',
												'<?php echo _l('Pack Qty'); ?>',
												'<?php echo _l('Pack Weight'); ?>',
												'<?php echo _l('Unit'); ?>',
												'<?php echo 'PO Qty'; ?>',
												'<?php echo _l('Qty'); ?>',
												'<?php echo _l('Rate'); ?>',
												'<?php echo _l('Dis Amt'); ?>',
												'<?php echo _l('GST % '); ?>',
												'<?php echo _l('CGSTAMT'); ?>',
												'<?php echo _l('SGSTAMT'); ?>',
												'<?php echo _l('IGSTAMT'); ?>',
												'<?php echo _l('Net Amt'); ?>',
												'<?php echo _l('Batch No.'); ?>',
												'<?php echo _l('Exp.Date'); ?>',
											],
												columnSorting: {
		indicator: true
	},
	autoColumnSize: {
		samplingRatio: 23
	},
	hiddenColumns: {
		columns: [18],
			indicators: false
	},
	mergeCells: true,
		contextMenu: true,
			manualRowMove: true,
				manualColumnMove: true,
					multiColumnSorting: {
		indicator: true
	},
	filters: true,
		manualRowResize: true,
			manualColumnResize: true
	};
	var hot = new Handsontable(hotElement, hotSettings);
	var hotRecalcLock = false;
	hot.addHook('afterChange', function (changes, src) {
		if (hotRecalcLock || !Array.isArray(changes) || changes.length === 0) {
			return;
		}

		hotRecalcLock = true;
		try {
			changes.forEach(function (change) {
				var row = change[0];
				var prop = change[1];
				var oldValue = change[2];
				var newValue = change[3];
				var count = 1;
				vendor_id = $("#vendor").val();
				if (prop == 'id') {
					vendor_id = $("#vendor").val();
					if (newValue == null) {
						hot.setDataAtCell(row, 1, '');
						hot.setDataAtCell(row, 2, '');
						hot.setDataAtCell(row, 3, '');
						hot.setDataAtCell(row, 4, '');
						hot.setDataAtCell(row, 5, '');
						hot.setDataAtCell(row, 6, '');
						hot.setDataAtCell(row, 7, '0');
						hot.setDataAtCell(row, 8, '0');
						hot.setDataAtCell(row, 9, '');
						hot.setDataAtCell(row, 10, '0');
						hot.setDataAtCell(row, 11, '0');
						hot.setDataAtCell(row, 12, '0');
						hot.setDataAtCell(row, 13, '0');
						hot.setDataAtCell(row, 14, '0');
						hot.setDataAtCell(row, 15, '0');
					} else {
						if (vendor_id == '') {
							alert("Please Select vendor");
							return;
						}
						var OrderID = $('#PurchID').val();
						$.post(admin_url + 'PurchaseMaster/GetItemDetailsPO/' + newValue + '/' + OrderID).done(function (response) {
							response = JSON.parse(response);
							hot.setDataAtCell(row, 1, response.hsn_code);
							hot.setDataAtCell(row, 2, response.BrandName);
							hot.setDataAtCell(row, 3, response.unit);
							hot.setDataAtCell(row, 4, response.PackingQty);
							hot.setDataAtCell(row, 5, response.PackingWeight);
							hot.setDataAtCell(row, 6, response.unit);
							hot.setDataAtCell(row, 7, response.POrderQty);
							hot.setDataAtCell(row, 8, '0');
							hot.setDataAtCell(row, 9, response.PurchRate);
							hot.setDataAtCell(row, 10, '0');
							hot.setDataAtCell(row, 11, response.taxrate);
							hot.setDataAtCell(row, 12, '0');
							hot.setDataAtCell(row, 13, '0');
							hot.setDataAtCell(row, 14, '0');
							hot.setDataAtCell(row, 15, '0');
						});
					}
				} else if (prop == 'OrderQty' || prop == 'PurchRate' || prop == 'Discount') {
					if (prop == 'OrderQty') {
						if (!validateOrderQtyAgainstPoLimit(hot, row, oldValue, newValue)) {
							return;
						}
					}

					var ProductName = hot.getDataAtCell(row, 0);
					var qtyValue = hot.getDataAtCell(row, 8);
					var rate = hot.getDataAtCell(row, 9);
					var discount = hot.getDataAtCell(row, 10);
					var unit = hot.getDataAtCell(row, 3);
					var packingqty = hot.getDataAtCell(row, 4);
					var saleunit = hot.getDataAtCell(row, 6);
					var gst = hot.getDataAtCell(row, 11);
					var statsid = $("#centername option:selected").data("statsid") || $("#CenterState").val();
					var vendorstate = $('#state').val();

					if (ProductName && rate && qtyValue) {
						var amount;
						if (unit !== saleunit && packingqty) {
							amount = (rate / packingqty) * qtyValue;
						} else {
							amount = rate * qtyValue;
						}

						if (discount) {
							amount = amount - (amount * discount / 100);
						}

						var netAmount = amount + (amount * gst / 100);
						netAmount = parseFloat(netAmount).toFixed(2);
						var totalGST = amount * gst / 100;
						var cgst = totalGST / 2;
						var sgst = totalGST / 2;
						var igst = totalGST;
						var cgstamt;
						var sgstamt;
						var igstamt;

						if (vendorstate == statsid) {
							cgstamt = cgst;
							sgstamt = sgst;
							igstamt = 0.00;
						} else if (vendorstate != statsid) {
							cgstamt = 0.00;
							sgstamt = 0.00;
							igstamt = igst;
						}

						hot.setDataAtCell(row, 12, parseFloat(cgstamt).toFixed(2));
						hot.setDataAtCell(row, 13, parseFloat(sgstamt).toFixed(2));
						hot.setDataAtCell(row, 14, parseFloat(igstamt).toFixed(2));
						hot.setDataAtCell(row, 15, parseFloat(netAmount).toFixed(2));
					}
				}
			});

			calculateTotalQuantity();
			calaulateSubTotal();
			calculateTotalDiscount();
			calculateTotalValue();
			calculateTotalCgstAmt();
			calculateTotalSgstAmt();
			calculateTotalIgstAmt();
			calculateTotalNetAmount();
		} finally {
			hotRecalcLock = false;
		}
	});
	$('.save_detail').on('click', function () {
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));
	});
	hot.addHook('afterBeginEditing', function (row, col) {
		if (col === 16) {
			const editor = this.getActiveEditor();
			if (editor && editor.TEXTAREA) {
				editor.TEXTAREA.removeAttribute('readonly');
			}
		}
	});
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
	function customRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		if (td.innerHTML != '') {
			td.innerHTML = td.innerHTML + '%'
			td.className = 'htRight';
		}
	}
	function customDropdownRenderer(
		instance,
		td,
		row,
		col,
		prop,
		value,
		cellProperties
	) {
		"use strict";
		var optionsList = [];
		if (
			cellProperties.chosenOptions &&
			Array.isArray(cellProperties.chosenOptions.data)
		) {
			optionsList = cellProperties.chosenOptions.data;
		}
		var displayValue = value || '';
		for (var i = 0; i < optionsList.length; i++) {
			if (
				String(optionsList[i].id) === String(value)
			) {
				displayValue = optionsList[i].ProductName;
				break;
			}
		}
		Handsontable.renderers.TextRenderer(
			instance,
			td,
			row,
			col,
			prop,
			displayValue,
			cellProperties
		);
		return td;
	}
	function customDropdownRenderer2(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		var selectedId;
		var optionsList = cellProperties.chosenOptions.data;
		if (typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
			Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
			return td;
		}
		var values = (value + "").split("|");
		value = [];
		for (var index = 0; index < optionsList.length; index++) {
			if (values.indexOf(optionsList[index].id + "") > -1) {
				selectedId = optionsList[index].id;
				value.push(optionsList[index].ProductName);
			}
		}
		value = value.join(", ");
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}
	function calculateTotalQuantity() {
		var totalQuantity = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var pqty = hot.getDataAtCell(i, 7);
			var qty = hot.getDataAtCell(i, 8);
			var unit = hot.getDataAtCell(i, 3);
			var packingqty = hot.getDataAtCell(i, 4);
			var saleunit = hot.getDataAtCell(i, 6);
			if (unit === saleunit && packingqty) {
				qty = packingqty * qty;
			}
			if (qty) {
				totalQuantity += qty;
			}
		}
		var totalQtyInput = document.getElementById('total_qty_in_mt');
		if (totalQtyInput) {
			totalQtyInput.value = totalQuantity.toFixed(2);
		}
	}
	function calaulateSubTotal() {
		var totalamt = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var pqty = hot.getDataAtCell(i, 8);
			var qty = hot.getDataAtCell(i, 7);
			var amountval = hot.getDataAtCell(i, 8);
			var unit = hot.getDataAtCell(i, 3);
			var packingqty = hot.getDataAtCell(i, 4);
			var saleunit = hot.getDataAtCell(i, 6);
			if (unit !== saleunit && packingqty) {
				amount = (amountval / packingqty) * qty;
				if (!isNaN(qty) && !isNaN(amount) && qty > 0 && amount > 0) {
					totalamt += amount;
				}
			} else {
				amount = amountval;
				if (!isNaN(qty) && !isNaN(amount) && qty > 0 && amount > 0) {
					var totalAmount = qty * amount;
					totalamt += totalAmount;
				}
			}
		}
		var totalAmtInput = document.getElementById('total_amt_in_mt');
		if (totalAmtInput) {
			totalAmtInput.value = totalamt.toFixed(2);
		}
	}
	function calculateTotalDiscount() {
		var totalDisc = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var pqty = hot.getDataAtCell(i, 7);
			var qty = hot.getDataAtCell(i, 8);
			var amountval = hot.getDataAtCell(i, 9);
			var disc = hot.getDataAtCell(i, 10);
			var unit = hot.getDataAtCell(i, 3);
			var packingqty = hot.getDataAtCell(i, 4);
			var saleunit = hot.getDataAtCell(i, 6);
			if (unit !== saleunit && packingqty) {
				amount = (amountval / packingqty) * qty;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {
					// var discountAmount = amount * (disc / 100);
					var discountAmount = disc * qty;
					totalDisc += discountAmount;
				}
			} else {
				amount = amountval;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {
					// var discountAmount = qty * amount * (disc / 100);
					var discountAmount = disc * qty;
					totalDisc += discountAmount;
				}
			}
		}
		var totalDiscInput = document.getElementById('total_disc_in_mt');
		if (totalDiscInput) {
			totalDiscInput.value = parseFloat(totalDisc).toFixed(2);
		}
	}
	function calculateTotalValue() {
		var totalValue = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var pqty = hot.getDataAtCell(i, 7);
			var qty = hot.getDataAtCell(i, 8);
			var amountval = hot.getDataAtCell(i, 9);
			var discount = hot.getDataAtCell(i, 10) * qty;
			var unit = hot.getDataAtCell(i, 3);
			var packingqty = hot.getDataAtCell(i, 4);
			var saleunit = hot.getDataAtCell(i, 6);
			if (unit !== saleunit && packingqty) {
				amount = (amountval / packingqty) * qty;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {
					// var discountedAmount = amount * (1 - discount / 100);                     
					var discountedAmount = amount - discount;
					totalValue += discountedAmount;
				}
			} else {
				amount = amountval;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {
					// var discountedAmount = amount * (1 - discount / 100);                        
					var discountedAmount = discount;
					totalValue += (amount * qty) - discountedAmount;
				}
			}
		}
		var totalValueInput = document.getElementById('Total_value');
		if (totalValueInput) {
			totalValueInput.value = totalValue.toFixed(2);
		}
	}
	function calculateTotalCgstAmt() {
		var totalCgstAmt = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var cgstamt = hot.getDataAtCell(i, 12);
			if (cgstamt && !isNaN(cgstamt)) {
				totalCgstAmt += parseFloat(cgstamt);
			}
		}
		totalCgstAmt = !isNaN(totalCgstAmt) ? totalCgstAmt : 0;
		var totalCgstInput = document.getElementById('total_cgst_amt');
		if (totalCgstInput) {
			totalCgstInput.value = totalCgstAmt.toFixed(2);
		}
	}
	function calculateTotalSgstAmt() {
		var totalSgstAmt = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var sgstamt = hot.getDataAtCell(i, 13);
			if (sgstamt && !isNaN(sgstamt)) {
				totalSgstAmt += parseFloat(sgstamt);
			}
		}
		totalSgstAmt = !isNaN(totalSgstAmt) ? totalSgstAmt : 0;
		var totalSgstInput = document.getElementById('total_sgst_amt');
		if (totalSgstInput) {
			totalSgstInput.value = totalSgstAmt.toFixed(2);
		}
	}
	function calculateTotalIgstAmt() {
		var totalIgstAmt = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var Igst = hot.getDataAtCell(i, 14);
			if (Igst && !isNaN(Igst)) {
				totalIgstAmt += parseFloat(Igst);
			}
		}
		totalIgstAmt = !isNaN(totalIgstAmt) ? totalIgstAmt : 0;
		var totalIgstInput = document.getElementById('total_igst_amt');
		if (totalIgstInput) {
			totalIgstInput.value = totalIgstAmt.toFixed(2);
		}
	}
	function calculateTotalNetAmount() {
		var totalNetAmount = 0;
		for (var i = 0; i < hot.countRows(); i++) {
			var pqty = hot.getDataAtCell(i, 7);
			var qty = hot.getDataAtCell(i, 8);
			var rateval = hot.getDataAtCell(i, 9);
			var discount = hot.getDataAtCell(i, 10);
			var gst = hot.getDataAtCell(i, 11);
			var unit = hot.getDataAtCell(i, 3);
			var packingqty = hot.getDataAtCell(i, 4);
			var saleunit = hot.getDataAtCell(i, 6);
			if (unit !== saleunit && packingqty) {
				rate = (rateval / packingqty) * qty;
			}
			if (unit !== saleunit && packingqty) {
				amount = (rateval / packingqty) * qty;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(gst) && qty > 0 && amount > 0) {
					// var amount = rate * qty;  
					if (discount) {
						// amount = amount - (amount * discount / 100); 
						amount = amount - discount;
					}
					var gstAmount = (amount * gst) / 100;
					var netAmount = amount + gstAmount;
					netAmount = parseFloat(netAmount.toFixed(2));
					totalNetAmount += netAmount;
				}
			} else {
				amount = rateval * qty;
				if (!isNaN(qty) && !isNaN(amount) && !isNaN(gst) && qty > 0 && amount > 0) {
					if (discount) {
						// amount = amount - (amount * discount / 100); 
						amount = amount - discount;
					}
					var gstAmount = (amount * gst) / 100;
					var netAmount = amount + gstAmount;
					netAmount = parseFloat(netAmount.toFixed(2));
					totalNetAmount += netAmount;
				}
			}
			var roundedNetAmount = totalNetAmount.toFixed(2);
			var decimalPart = parseFloat(roundedNetAmount.split('.')[1]);
			if (decimalPart >= 50) {
				totalNetAmounts = Math.ceil(totalNetAmount);
			} else {
				totalNetAmounts = Math.floor(totalNetAmount);
			}
			var netPayableAmount = parseFloat(totalNetAmounts).toFixed(2);
		}
		Totalvalueset();
	}
	function Totalvalueset() {
		var Taxabele = $('input[name="Total_value"]').val();
		var cgst = $('input[name="total_cgst_amt"]').val();
		var sgst = $('input[name="total_sgst_amt"]').val();
		var igst = $('input[name="total_igst_amt"]').val();
		total_tcs_amt = $('input[name="total_tcs_amt"]').val();;
		Other_amt = $('input[name="OtherAmt"]').val();
		if (Other_amt == "") {
			Other_amt = 0;
		}
		if (total_tcs_amt == "") {
			total_tcs_amt = 0;
		}
		TotalInvAmount = parseFloat(Taxabele) + parseFloat(cgst) + parseFloat(sgst) + parseFloat(igst) + parseFloat(Other_amt) + parseFloat(total_tcs_amt);
		var Round = Math.round(TotalInvAmount).toFixed(2);
		var difference = (parseFloat(TotalInvAmount) - Round).toFixed(2);
		var totalNetPaybleAmount = document.querySelector('input[name="netpayableamt"]');
		if (totalNetPaybleAmount) {
			totalNetPaybleAmount.value = Round;
		}
		var totalRoundOff = document.querySelector('input[name="total_roundoff_amt"]');
		if (totalRoundOff) {
			totalRoundOff.value = difference;
		}
	}
	$('#OtherAmt').on('blur', function () {
		Totalvalueset();
	});
	$('#total_tcs_amt').on('blur', function () {
		Totalvalueset();
	});
	$(document).off('submit', '#pur_order-form').on('submit', '#pur_order-form', function (e) {
		// console.log('Validation triggered');
		var isValid = true;
		var data = hot.getData(); // your Handsontable instance
		for (let i = 0; i < data.length; i++) {
			let batchNo = data[i][16]; // BatchNo column
			let batchQty = data[i][8]; // BatchNo column
			let expDate = data[i][17]; // ExpDate column
			// Skip empty rows (if Item Name is empty)
			if (!data[i][0]) continue;
			if (batchQty > 0 && (!batchNo || batchNo.trim() === '')) {
				alert(`Batch No is required in row ${i + 1}`);
				isValid = false;
				break;
			}
			if (batchQty > 0 && (!expDate || expDate.toString().trim() === '')) {
				alert(`Expiry Date is required in row ${i + 1}`);
				isValid = false;
				break;
			}
		}
		if (!isValid) {
			e.preventDefault(); // Stop form submission
			$('#pur_order-form button[type="submit"]').prop('disabled', false);
		}
	});
</script>
<style>
	.htLeft {
		text-align: left !important;
	}
</style>