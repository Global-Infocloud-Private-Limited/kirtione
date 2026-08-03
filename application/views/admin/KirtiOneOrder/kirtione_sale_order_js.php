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

				centername: 'required',

				state: 'required',

				PaymentTerm: 'required',

			});

		}

	});



	function numberWithCommas(x) {

		"use strict";

		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	}



	<?php

	if (!empty($pur_order_detail)) { ?>

		var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;

	<?php

	} else { ?>

		var dataObject = [];

	<?php } ?>



	var hotElement = document.querySelector('#example');

	var hotElementContainer = hotElement.parentNode;



	var hotSettings = {

		data: dataObject,

		columns: [

			{

				data: 'id',

				renderer: customDropdownRenderer,

				editor: "chosen",

				chosenOptions: {

					data: <?php echo json_encode($item_code); ?>

				}

			},

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

				data: 'SaleUnit',

				type: 'text',

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

			}



		],

		licenseKey: 'non-commercial-and-evaluation',

		stretchH: 'all',

		width: '100%',

		columnHeaderHeight: 40,

		minRows: 10,

		maxRows: 40,

		rowHeaders: true,

		colWidths: [100, 80, 60, 60, 70, 70, 50, 70, 60, 50, 50, 50, 50, 70],

		colHeaders: [

			'<?php echo _l('Item Name'); ?>',

			'<?php echo _l('HSN Code'); ?>',

			'<?php echo _l('Brand'); ?>',

			'<?php echo _l('UOM'); ?>',

			'<?php echo _l('Pack Qty'); ?>',

			'<?php echo _l('Pack Wt(kg)'); ?>',

			'<?php echo _l('Sale Unit'); ?>',

			'<?php echo _l('Qty(Unit)'); ?>',

			'<?php echo _l('Rate/Unit'); ?>',

			'<?php echo _l('Disc Amt/Unit'); ?>',

			'<?php echo _l('GST%'); ?>',

			'<?php echo _l('CGST Amt'); ?>',

			'<?php echo _l('SGST Amt'); ?>',

			'<?php echo _l('IGST Amt'); ?>',

			'<?php echo _l('Net Amt'); ?>',

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

		if (changes !== null) {

			changes.forEach(([row, prop, oldValue, newValue]) =>

				{

					var count = 1;

					vendor_id = $("#vendor").val();

					centername = $("#centername").val();



					if (prop == 'id')

					{

						vendor_id = $("#vendor").val();

						if (newValue == null)

						{

							hot.setDataAtCell(row, 1, '');

							hot.setDataAtCell(row, 2, '');

							hot.setDataAtCell(row, 3, '0');

							hot.setDataAtCell(row, 4, '0');

							hot.setDataAtCell(row, 5, '0');

							hot.setDataAtCell(row, 6, '0');

							hot.setDataAtCell(row, 7, '0');

							hot.setDataAtCell(row, 8, '');

							hot.setDataAtCell(row, 9, '0');

							hot.setDataAtCell(row, 10, '0');

							hot.setDataAtCell(row, 11, '0');

							hot.setDataAtCell(row, 12, '0');

							hot.setDataAtCell(row, 13, '0');



						} else {

							if (vendor_id == '') {

								alert("Please Select vendor");
								return false;

							} else if (centername == '') {

								alert("Please Select Center Name ");
								return false;

							} else {

								count++;

								$.post(admin_url + 'KirtiOneOrder/GetItemDetails/' + newValue).done(function(response) {

									response = JSON.parse(response);

									hot.setDataAtCell(row, 1, response.hsn_code);

									hot.setDataAtCell(row, 2, response.BrandName);

									hot.setDataAtCell(row, 3, response.unit);

									hot.setDataAtCell(row, 4, response.PackingQty);

									hot.setDataAtCell(row, 5, parseFloat(response.PackingWeight) * parseFloat(response.PackingQty));

									hot.setDataAtCell(row, 6, response.unit);

									hot.setDataAtCell(row, 9, '');

									hot.setDataAtCell(row, 7, '');

									hot.setDataAtCell(row, 8, response.rate);

									hot.setDataAtCell(row, 10, response.taxrate);

									hot.setDataAtCell(row, 11, '0');

									hot.setDataAtCell(row, 12, '0');

									hot.setDataAtCell(row, 13, '0');

									hot.setDataAtCell(row, 14, '0');

									count++;

								});

							}

						}

					} else if (prop == 'OrderQty' || prop == 'PurchRate')

					{

						let qtyValue = hot.getDataAtCell(row, 7);

						let rate = hot.getDataAtCell(row, 8);

						let discount = hot.getDataAtCell(row, 9);

						//hot.setDataAtCell(row, 8, discount); 



						let unit = hot.getDataAtCell(row, 3);

						let packingqty = hot.getDataAtCell(row, 4);

						let saleunit = hot.getDataAtCell(row, 6);

						let gst = hot.getDataAtCell(row, 10);



						let statsid = $("#centername option:selected").data("statsid");

						let vendorstate = $('#state').val();



						if (rate && qtyValue)

						{

							let amount;

							if (unit !== saleunit && packingqty) {

								let newRate = (rate / packingqty) * qtyValue;

								rate = newRate

								amount = rate;

							} else

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

							if (vendorstate == statsid)

							{

								cgstamt = cgst;

								sgstamt = sgst;

								igstamt = 0.00;

							} else if (vendorstate != statsid)

							{

								cgstamt = 0.00;

								sgstamt = 0.00;

								igstamt = igst;

							}



							hot.setDataAtCell(row, 14, parseFloat(netAmount).toFixed(2));

							hot.setDataAtCell(row, 11, parseFloat(cgstamt).toFixed(2));

							hot.setDataAtCell(row, 12, parseFloat(sgstamt).toFixed(2));

							hot.setDataAtCell(row, 13, parseFloat(igstamt).toFixed(2));

						}

					} else if (prop == 'Discount')

					{

						let qtyValue = hot.getDataAtCell(row, 7);

						let rate = hot.getDataAtCell(row, 8);

						let discount = hot.getDataAtCell(row, 9);

						//hot.setDataAtCell(row, 8, discount); 



						let unit = hot.getDataAtCell(row, 3);

						let packingqty = hot.getDataAtCell(row, 4);

						let saleunit = hot.getDataAtCell(row, 6);

						let gst = hot.getDataAtCell(row, 10);



						let statsid = $("#centername option:selected").data("statsid");

						let vendorstate = $('#state').val();



						if (rate && qtyValue)

						{

							let amount;

							if (unit !== saleunit && packingqty) {

								let newRate = (rate / packingqty) * qtyValue;

								rate = newRate

								amount = rate;

							} else

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

							if (vendorstate == statsid)

							{

								cgstamt = cgst;

								sgstamt = sgst;

								igstamt = 0.00;

							} else if (vendorstate != statsid)

							{

								cgstamt = 0.00;

								sgstamt = 0.00;

								igstamt = igst;

							}



							hot.setDataAtCell(row, 14, parseFloat(netAmount).toFixed(2));

							hot.setDataAtCell(row, 11, parseFloat(cgstamt).toFixed(2));

							hot.setDataAtCell(row, 12, parseFloat(sgstamt).toFixed(2));

							hot.setDataAtCell(row, 13, parseFloat(igstamt).toFixed(2));

						}

					}

					calculateTotalQuantity();

					calaulateSubTotal();

					calculateTotalDiscount();

					calculateTotalValue();

					calculateTotalCgstAmt();

					calculateTotalSgstAmt();

					calculateTotalIgstAmt();

					calculateTotalNetAmount();

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





	function customRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		Handsontable.renderers.TextRenderer.apply(this, arguments);

		if (td.innerHTML != '') {

			td.innerHTML = td.innerHTML + '%'

			td.className = 'htRight';

		}

	}



	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {

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



	function calculateTotalQuantity()

	{

		var totalQuantity = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var qty = hot.getDataAtCell(i, 7);



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

			totalQtyInput.value = parseFloat(totalQuantity).toFixed(2);

		}

	}



	function calaulateSubTotal()

	{

		var totalamt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

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

			} else

			{

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



	function calculateTotalDiscount()

	{

		var totalDisc = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var qty = hot.getDataAtCell(i, 7);

			var amountval = hot.getDataAtCell(i, 8);

			var disc = hot.getDataAtCell(i, 9);



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

			} else

			{

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

			totalDiscInput.value = totalDisc.toFixed(2);

		}

	}



	function calculateTotalValue()

	{

		var totalValue = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var qty = hot.getDataAtCell(i, 7);

			var amountval = hot.getDataAtCell(i, 8);

			var discount = hot.getDataAtCell(i, 9) * qty;



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

			} else

			{

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



	function calculateTotalCgstAmt()

	{

		var totalCgstAmt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var cgstamt = hot.getDataAtCell(i, 11);

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



	function calculateTotalSgstAmt()

	{

		var totalSgstAmt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var sgstamt = hot.getDataAtCell(i, 12);

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

	function calculateTotalIgstAmt()

	{

		var totalIgstAmt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var Igst = hot.getDataAtCell(i, 13);

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

	function calculateTotalNetAmount()

	{

		var totalNetAmount = 0;



		for (var i = 0; i < hot.countRows(); i++)

		{

			var qty = hot.getDataAtCell(i, 7);

			var rateval = hot.getDataAtCell(i, 8);

			var discount = hot.getDataAtCell(i, 9);

			var gst = hot.getDataAtCell(i, 10);



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

			} else

			{

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



		Freight_AMT = 0;

		Other_amt = 0;

		TotalInvAmount = parseFloat(Taxabele) + parseFloat(cgst) + parseFloat(sgst) + parseFloat(igst) + parseFloat(Freight_AMT) + parseFloat(Other_amt);



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



	$('#Other_amt').on('blur', function() {

		Totalvalueset();

	});



	$('#Freight_AMT').on('blur', function() {

		Totalvalueset();

	});
</script>



<style>
	.htLeft {

		text-align: left !important;

	}
</style>