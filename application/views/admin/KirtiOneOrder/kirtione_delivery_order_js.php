<script>
	function removeCommas(str) {

		"use strict";

		return (str.replace(/,/g, ''));

	}



	$(function() {

		"use strict";

		validate_purorder_form();

		function validate_purorder_form(selector) {

			selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;

			appValidateForm($(selector), {

				pro_orderid: 'required',

				prd_date: 'required',

				vendor: 'required',

				OrderID: 'required',

				state: 'required',



			});

		}

	});



	function numberWithCommas(x) {

		"use strict";

		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	}



	<?php

	if (!empty($chl_item_detail)) { ?>

		var dataObject = <?php echo html_entity_decode($chl_item_detail); ?>;

	<?php

	} else { ?>

		var dataObject = [];

	<?php } ?>



	var hotElement = document.querySelector('#example');

	var hotElementContainer = hotElement.parentNode;

	var allItemList = <?php echo json_encode($item_code); ?>;
	var saleOrderItemList = [];
	var saleOrderItemData = [];

	if (Array.isArray(dataObject) && dataObject.length > 0)
	{
		dataObject.forEach(function(item)
		{
			if (!item || !item.id)
			{
				return;
			}
			
			saleOrderItemData.push(item);
			
			var alreadyExists = saleOrderItemList.some(function(listItem)
			{
				return String(listItem.id) === String(item.id);
			});


			if (!alreadyExists)
			{
				saleOrderItemList.push({
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

	function getSaleOrderItemData(itemId)
	{
		for (var i = 0; i < saleOrderItemData.length; i++)
		{
			if (String(saleOrderItemData[i].id) === String(itemId))
			{
				return saleOrderItemData[i];
			}
		}

		return null;
	}


	/*
	|--------------------------------------------------------------------------
	| Create batch dropdown options
	|--------------------------------------------------------------------------
	*/
	function createBatchOptions(batchList)
	{
		var batchOptions = [];

		if (!Array.isArray(batchList))
		{
			return batchOptions;
		}

		batchList.forEach(function(batch)
		{
			if (parseFloat(batch.Stock) > 0)
			{
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
	function applyBatchListToRow(row, batchList, selectFirstBatch)
	{
		var batchOptions = createBatchOptions(batchList);

		hot.setCellMeta(row, 6, 'chosenOptions', {
			data: batchOptions
		});

		if (selectFirstBatch === true && batchOptions.length > 0)
		{
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
		}
		else
		{
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

			// 		data: <?php // echo json_encode($item_code); ?>

			// 	},

			// 	readOnly: true

			// },

			{

				data: 'hsn_code',

				type: 'text',

				readOnly: true

			},

			{

				data: 'BrandID',

				type: 'text',

				readOnly: true

			},

			{

				data: 'UOM',

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

				data: 'SaleUnit',

				type: 'text',

				readOnly: true

			},

			{

				data: 'BatchList',

				renderer: customRenderer,

				editor: "chosen",

				chosenOptions: {

					data: []

				}

			},

			{

				data: 'StockQty',

				type: 'numeric',

				className: 'htLeft',

				readOnly: true

			},

			{

				data: 'SOQty',

				type: 'text',

				readOnly: true

			},

			{

				data: 'DOQty',

				type: 'numeric',

				className: 'htLeft'

			},

			{

				data: 'BasicRate',

				type: 'text',

				readOnly: false

			},

			{

				data: 'DiscAmt',

				type: 'numeric',

				className: 'htLeft'

			},

			{

				data: 'GSTPer',

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

				data: 'ExpDate',

				type: 'text',

				width: 70,

				readOnly: true

			},



		],

		cells: function(row, col)
		{
			var cellProperties = {};
			
			if (col === 0)
			{
				cellProperties.editor = "chosen";
				cellProperties.renderer = customDropdownRenderer;
				cellProperties.readOnly = false;

				cellProperties.chosenOptions = {
					data: saleOrderItemList
				};
			}

			return cellProperties;
		},

		minSpareRows: 1,

		licenseKey: 'non-commercial-and-evaluation',

		stretchH: 'all',

		width: '100%',

		columnHeaderHeight: 40,

		minRows: 10,

		maxRows: 40,

		rowHeaders: true,

		colWidths: [100, 80, 60, 60, 60, 60, 70, 70, 50, 70, 60, 50, 50, 50, 50, 70],

		colHeaders: [

			'<?php echo _l('Item Name'); ?>',

			'<?php echo _l('Hsn Code'); ?>',

			'<?php echo _l('Brand Name'); ?>',

			'<?php echo _l('UOM'); ?>',

			'<?php echo _l('Pack Qty'); ?>',

			'<?php echo _l('Sale Unit'); ?>',

			'<?php echo _l('Batch List'); ?>',

			'<?php echo _l('Stock Qty'); ?>',

			'<?php echo _l('SO Qty'); ?>',

			'<?php echo _l('DO Qty'); ?>',

			'<?php echo _l('Rate/Unit'); ?>',

			'<?php echo _l('Disc Amt/Unit'); ?>',

			'<?php echo _l('GST%'); ?>',

			'<?php echo _l('CGST Amt'); ?>',

			'<?php echo _l('SGST Amt'); ?>',

			'<?php echo _l('IGST Amt'); ?>',

			'<?php echo _l('Net Amt'); ?>',

			'<?php echo _l('Exp. Date'); ?>',

		],

		columnSorting: {

			indicator: true

		},

		autoColumnSize: {

			samplingRatio: 23

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

	hot.addHook('afterChange', function(changes, src) {

		if (
			changes === null ||
			src === 'loadData' ||
			src === 'item-detail-load' ||
			src === 'item-batch-load'
		)
		{
			return;
		}

		if (changes !== null)
		{

			changes.forEach(([row, prop, oldValue, newValue]) =>

				{

					var count = 1;

					vendor_id = $("#vendor").val();



					if (
						prop === 'id' &&
						src !== 'loadData' &&
						src !== 'item-detail-load' &&
						src !== 'item-batch-load'
					)
					{
						if (newValue == null || newValue === '')
						{
							return;
						}


						/*
						|--------------------------------------------------------------------------
						| Find complete Sale Order detail
						|--------------------------------------------------------------------------
						*/

						var selectedOrderItem = getSaleOrderItemData(newValue);

						if (!selectedOrderItem)
						{
							alert('Selected item is not available in Sale Order.');

							hot.setDataAtRowProp(
								row,
								'id',
								oldValue || '',
								'item-detail-load'
							);

							return;
						}


						/*
						|--------------------------------------------------------------------------
						| Copy Sale Order details to selected row
						|--------------------------------------------------------------------------
						*/

						hot.setDataAtRowProp(
							row,
							'hsn_code',
							selectedOrderItem.hsn_code || '',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'BrandID',
							selectedOrderItem.BrandID || '',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'UOM',
							selectedOrderItem.UOM || '',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'PackingQty',
							selectedOrderItem.PackingQty || '',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'SaleUnit',
							selectedOrderItem.SaleUnit || '',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'SOQty',
							selectedOrderItem.SOQty || 0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'BasicRate',
							selectedOrderItem.BasicRate || 0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'DiscAmt',
							selectedOrderItem.DiscAmt || 0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'GSTPer',
							selectedOrderItem.GSTPer || 0,
							'item-detail-load'
						);


						/*
						|--------------------------------------------------------------------------
						| Reset delivery-specific values
						|--------------------------------------------------------------------------
						*/

						hot.setDataAtRowProp(
							row,
							'DOQty',
							'',
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'cgstamt',
							0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'sgstamt',
							0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'igstamt',
							0,
							'item-detail-load'
						);

						hot.setDataAtRowProp(
							row,
							'Netamt',
							0,
							'item-detail-load'
						);


						/*
						|--------------------------------------------------------------------------
						| Apply this item's batch list to selected row
						|--------------------------------------------------------------------------
						*/

						applyBatchListToRow(
							row,
							selectedOrderItem.BatchList,
							true
						);


						hot.render();

						return;
					}
					else if (prop == 'DOQty' || prop == 'BasicRate' || prop == 'DiscAmt')

					{

						let StockQty = hot.getDataAtCell(row, 7);

						let OrderQty = hot.getDataAtCell(row, 8);

						let DOQty = hot.getDataAtCell(row, 9);

						let BasicRate = hot.getDataAtCell(row, 10);

						let discAmt = hot.getDataAtCell(row, 11);

						let GSTPer = hot.getDataAtCell(row, 12);



						if (parseFloat(DOQty) > parseFloat(StockQty)) {

							alert('Please enter DO quantity is less than or equal to available stock quantity');

							hot.setDataAtCell(row, 9, StockQty);

							return;

						}

						// if (parseFloat(DOQty) > parseFloat(OrderQty)) {

						// 	alert('Please enter DO quantity is less than or equal to order quantity');

						// 	hot.setDataAtCell(row, 9, OrderQty);

						// 	return;

						// }

						// Validate total DO Qty item-wise
						let currentItemID = hot.getDataAtCell(row, 0);
						let totalItemDOQty = 0;

						for (let i = 0; i < hot.countRows(); i++)
						{
							let rowItemID = hot.getDataAtCell(i, 0);

							if (String(rowItemID) === String(currentItemID))
							{
								let rowDOQty = parseFloat(hot.getDataAtCell(i, 9)) || 0;
								totalItemDOQty += rowDOQty;
							}
						}

						if (totalItemDOQty > parseFloat(OrderQty))
						{
							let otherRowsQty = totalItemDOQty - (parseFloat(DOQty) || 0);
							let remainingQty = parseFloat(OrderQty) - otherRowsQty;

							if (remainingQty < 0) {
								remainingQty = 0;
							}

							alert(
								'Total DO quantity for this item cannot exceed Sale Order quantity. ' +
								'Remaining quantity: ' + remainingQty
							);

							hot.setDataAtCell(row, 9, remainingQty);

							return;
						}
						// End Validate total DO Qty item-wise

						if (parseFloat(BasicRate) < parseFloat(discAmt)) {

							alert('Please enter Basic rate is greter than discount amount');

							hot.setDataAtCell(row, 10, oldValue);

							return;

						}



						let statsid = $("#CenterState").val();

						let vendorstate = $('#state').val();



						if (BasicRate && OrderQty)

						{

							let ItemAmt = parseFloat(DOQty) * parseFloat(BasicRate);

							var ItemDiscAmt = 0;

							if (discAmt) {

								ItemDiscAmt = parseFloat(DOQty) * parseFloat(discAmt);

							}

							let ItemTaxableAamt = parseFloat(ItemAmt) - parseFloat(ItemDiscAmt);

							/*alert(ItemAmt);

					    alert(ItemDiscAmt);*/

							let ItemGSTAmt = 0;

							if (GSTPer > 0) {

								ItemGSTAmt = (ItemTaxableAamt * (GSTPer / 100));

							}

							let ItemNetAmt = parseFloat(ItemTaxableAamt) + parseFloat(ItemGSTAmt);

							let CGSTAmt = 0;
							let SGSTAmt = 0;
							let IGSTAmt = 0;

							if (vendorstate == statsid && ItemGSTAmt > 0)

							{

								CGSTAmt = parseFloat(ItemGSTAmt) / 2;

								SGSTAmt = parseFloat(ItemGSTAmt) / 2;

								IGSTAmt = 0;

							} else if (ItemGSTAmt > 0)

							{

								CGSTAmt = 0;

								SGSTAmt = 0;

								IGSTAmt = ItemGSTAmt;

							}



							hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));

							hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));

							hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2));

							hot.setDataAtCell(row, 16, parseFloat(ItemNetAmt).toFixed(2));

						} else {

							hot.setDataAtCell(row, 11, 0);

							hot.setDataAtCell(row, 13, 0);

							hot.setDataAtCell(row, 14, 0);

							hot.setDataAtCell(row, 15, 0);

							hot.setDataAtCell(row, 16, 0);

						}

					}
					/*else if(prop == 'Discount')

									{

										let qtyValue = hot.getDataAtCell(row, 9);  

										let rate = hot.getDataAtCell(row, 10);   

										let discount = hot.getDataAtCell(row, 11); 				

										//hot.setDataAtCell(row, 8, discount); 

										

										let unit  = hot.getDataAtCell(row, 3);  

										let packingqty = hot.getDataAtCell(row, 4);  

										let saleunit = hot.getDataAtCell(row, 5); 

										let gst = hot.getDataAtCell(row, 12);

										

										let statsid = $("#CenterState").val();

										let vendorstate = $('#state').val();

										

										if (rate && qtyValue) 

										{         

											let amount;

											if (unit !== saleunit && packingqty) {

												let newRate = (rate / packingqty) * qtyValue;

												rate= newRate  

												amount =rate;                                   

											}

											else 

											{

												amount = rate * qtyValue;      

											}                                 

											

											var discountPercent = 0;

											if (discount) {

											discount = discount * qtyValue;

												// amount = amount - (amount * discount / 100);

												    discountPercent = (discount / amount) * 100;

													amount = amount - discount;

											}					

											

											let netAmount = amount + (amount * gst / 100); 

											netAmount = netAmount.toFixed(2);  

											

											let totalGST = amount * gst / 100; 

											let cgst = totalGST / 2;

											let sgst = totalGST / 2;

											let igst = totalGST;                                   

											

											let cgstamt;

											let sgstamt;

											let igstamt;

											if(vendorstate == statsid)

											{                                                       

												cgstamt = cgst;

												sgstamt = sgst;

												igstamt = 0.00;                                   

											} 

											else if(vendorstate != statsid)

											{

												cgstamt = 0.00;

												sgstamt = 0.00;

												igstamt = igst;                                   

											} 					  

											

											hot.setDataAtCell(row, 16, parseFloat(netAmount).toFixed(2));               

											hot.setDataAtCell(row, 13, parseFloat(cgstamt).toFixed(2));  

											hot.setDataAtCell(row, 14, parseFloat(sgstamt).toFixed(2));  

											hot.setDataAtCell(row, 15, parseFloat(igstamt).toFixed(2));                                                                                      

										} 					

									}*/
					else if (prop == 'BatchList')

					{

						let itemID = hot.getDataAtCell(row, 0);

						let CenterID = $("#CenterID").val();

						if (newValue != null && newValue != '')

						{

							$.post(admin_url + 'KirtiOneOrder/GetItemBatchStock/', {
								ItemID: itemID,
								BatchID: newValue,
								CenterID: CenterID
							}).done(function(response) {

								response = JSON.parse(response);

								hot.setDataAtCell(row, 7, response[0].Stock);

								hot.setDataAtCell(row, 17, response[0].ExpDate);

								/*hot.setDataAtCell(row,9, '0');	*/

								count++;

							});

						} else {

							hot.setDataAtCell(row, 7, '');

							hot.setDataAtCell(row, 17, '');

						}

					}

					validateBatchNo();
					calculateTotal();

				});

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



	$('.save_detail').on('click', function() {

		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));

	});



	$('.save_detail').on('click', function() {

		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));

	});





	/*function customRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		Handsontable.renderers.TextRenderer.apply(this, arguments);

		if(td.innerHTML != ''){

			td.innerHTML = td.innerHTML + '%'

			td.className = 'htRight';

		}

	}*/

	function customRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		Handsontable.renderers.TextRenderer.apply(this, arguments);



	}



	function customDropdownRenderer(
		instance,
		td,
		row,
		col,
		prop,
		value,
		cellProperties
	)
	{
		"use strict";

		var optionsList = [];

		if (
			cellProperties.chosenOptions &&
			Array.isArray(cellProperties.chosenOptions.data)
		)
		{
			optionsList = cellProperties.chosenOptions.data;
		}

		var displayValue = value || '';

		for (var i = 0; i < optionsList.length; i++)
		{
			if (
				String(optionsList[i].id) === String(value)
			)
			{
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

				value.push(optionsList[index].label);

			}

		}

		value = value.join(", ");



		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);

		return td;

	}

	function validateBatchNo() {
		console.log('Validating Batch No...');
    var isValid = true;
    var totalRows = hot.countRows();
    for (var row = 0; row < totalRows; row++) {
			var batchNo = hot.getDataAtCell(row, 7);  // Batch No is in column 7
			if (batchNo == '') {
				isValid = false;
				break;
			}
			// console.log('Row ' + row + ' Batch No: ' + batchNo);
    }

    $('#savebtn').prop('disabled', !isValid);
	}

	function calculateTotal()

	{

		let CenterState = $("#CenterState").val();

		let Partystate = $("#state").val();

		var totalQuantity = 0;

		var TotalItemSum = 0;

		var TotalItemDiscSum = 0;

		var TotalCGSTAmt = 0;
		var TotalSGSTAmt = 0;
		var TotalIGSTAmt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			if (hot.getDataAtCell(i, 0)) {

				var qty = hot.getDataAtCell(i, 9);

				if (!isNaN(qty)) {

					totalQuantity += parseFloat(qty);

				}



				// Calculate Item Total

				var BasicRate = hot.getDataAtCell(i, 10);

				if (!isNaN(qty) && !isNaN(BasicRate)) {

					var ItemTotal = parseFloat(qty) * parseFloat(BasicRate);

					TotalItemSum += parseFloat(ItemTotal);

				}

				// Calculate Discount

				var DiscAmt = hot.getDataAtCell(i, 11) || 0; // Disc Amt Per Unit

				if (!isNaN(qty) && !isNaN(DiscAmt)) {

					var ItemDisc = parseFloat(qty) * parseFloat(DiscAmt);

					TotalItemDiscSum += parseFloat(ItemDisc);

				}

				// Calculate GST Amt

				var GSTPer = hot.getDataAtCell(i, 12);

				var TaxableAmt = parseFloat(ItemTotal) - parseFloat(ItemDisc);

				var GSTAmt = parseFloat(TaxableAmt) * (parseFloat(GSTPer) / 100);

				if (CenterState == Partystate && !isNaN(GSTAmt)) {

					TotalCGSTAmt += parseFloat(parseFloat(GSTAmt) / 2);

					TotalSGSTAmt += parseFloat(parseFloat(GSTAmt) / 2);

				} else if (!isNaN(GSTAmt)) {

					TotalIGSTAmt += parseFloat(GSTAmt);

				}

			}

		}

		$("#total_qty_in_mt").val(totalQuantity);

		$("#total_amt_in_mt").val(TotalItemSum);

		$("#total_disc_in_mt").val(TotalItemDiscSum);

		var TotalTaxableAmt = parseFloat(TotalItemSum) - parseFloat(TotalItemDiscSum);

		$("#Total_value").val(TotalTaxableAmt);

		$("#total_cgst_amt").val(TotalCGSTAmt.toFixed(3));

		$("#total_sgst_amt").val(TotalSGSTAmt.toFixed(3));

		$("#total_igst_amt").val(TotalIGSTAmt.toFixed(3));

		var TotalNetAmt = parseFloat(TotalTaxableAmt) + parseFloat(TotalCGSTAmt) + parseFloat(TotalSGSTAmt) + parseFloat(TotalIGSTAmt);



		var Round = Math.round(TotalNetAmt);

		var difference = (parseFloat(TotalNetAmt) - Round);

		$("#total_roundoff_amt").val(difference.toFixed(3));

		$("#netpayableamt").val(Round.toFixed(3));

	}
</script>



<style>
	.htLeft {

		text-align: left !important;

	}
</style>