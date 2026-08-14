<script>

	

    function removeCommas(str) {

		"use strict";

		return(str.replace(/,/g,''));

	}   

	

	$(function(){

		"use strict";

		validate_purorder_form();

		function validate_purorder_form(selector) {

			selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;

			appValidateForm($(selector), {

				transferid: 'required',

				trf_date: 'required',            

				fromcentername:'required',   

				tocentername:'required', 	

				AccountID:'required', 		

				VehicleNo:'required', 		

				DriverName:'required', 		

				DriverMobile:'required', 		

				trf_date: {

					remote: {

						url: site_url + "admin/misc/checkpurch_val",

						type: 'post',

						data: {

							order_date: function() {

								return $('input[name="trf_date"]').val();

							},

							PurchID: function() {

								return $('input[name="transferid"]').val();

							}

						}

					}

				}, 

			});

		}

	});

	

	function numberWithCommas(x) {

		"use strict";

		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	}

	

	<?php 	

		if(!empty($pur_order_detail)){ ?>

        var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;

		<?php 

		}else{ ?>

        var dataObject = []; 

	<?php }?>    	

	

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

			data: 'BatchNo',

			renderer: customRenderer,

            editor: "chosen",

            chosenOptions: {

				data:  []

			},

		},

        {

			data: 'StockQty',

			type: 'numeric',

			className: 'htLeft',

			readOnly: true            

		},

		{

			data: 'ExpDate',

			type: 'date',

			width: 70,

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

			readOnly: true

		},

        {

			data: 'Discount',

			type: 'numeric',

			className: 'htLeft', 	         

			readOnly: true	  

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

		minRows: 50,

		maxRows: 100,

		rowHeaders: true,

		colWidths: [100,80, 60, 60, 70,70,50,70, 60, 50,50,50,50,70],

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

		if(changes !== null){

			changes.forEach(([row, prop, oldValue, newValue]) => 

			{			 

				var count = 1; 

				AccountID = $("#AccountID").val();

				FromCenterID = $("#fromcentername").val();

				ToCenterID = $("#tocentername").val();

				if(prop == 'id')

				{   

					if(newValue == null)

					{

						hot.setDataAtCell(row,9, '');

						hot.setDataAtCell(row,10, '');

						hot.setDataAtCell(row,11, '');

						hot.setDataAtCell(row,12, '');

						hot.setDataAtCell(row,13, '');

						hot.setDataAtCell(row,14, '');

						hot.setDataAtCell(row,15, '');

						hot.setDataAtCell(row,1, '');

						hot.setDataAtCell(row,2, '');

						hot.setDataAtCell(row,3, '');

						hot.setDataAtCell(row,4, '');

						hot.setDataAtCell(row,5, '');

						hot.setDataAtCell(row,6, '');

						hot.setDataAtCell(row,7, '');

						hot.setDataAtCell(row,8, '');

                      	

						}else{

						if(FromCenterID == ""){

							alert("Please Select From Center");

							hot.setDataAtCell(row,0, null);

							return false;

							}else if(ToCenterID == ""){

							alert("Please Select To Center");

							hot.setDataAtCell(row,0, null);

							return false;

							}else if(AccountID == ''){

							alert("Please Select vendor");

							hot.setDataAtCell(row,0, null);

							return false;

							}else{          	            

							count++;					

							$.post(admin_url + 'KirtiOneOrder/GetItemDetailsForDirectSaleOld/'+newValue+ '/'+FromCenterID+ '/'+AccountID).done(function(response){

								response = JSON.parse(response);							

								hot.setDataAtCell(row,1, response.BrandName);

								hot.setDataAtCell(row,2, response.unit);

								hot.setDataAtCell(row,3, response.PackingQty);

								hot.setDataAtCell(row,4, response.PackingWeight);

								hot.setDataAtCell(row,5, response.unit);   

								hot.setDataAtCell(row,10, '');	

								hot.setDataAtCell(row,8, '');							

								hot.setDataAtCell(row,9, (parseFloat(response.rate)));     													

								hot.setDataAtCell(row,11, response.taxrate);

								hot.setDataAtCell(row,12, '0');

								hot.setDataAtCell(row,13, '0');

								hot.setDataAtCell(row,14, '0');

								hot.setDataAtCell(row,15, '0');

								

								var ItemsOptions = response.BatchList.map(function(ListBatch) {

								    if(ListBatch.Stock > 0){

								        return {

    										id: ListBatch.BatchNo,

    										label: ListBatch.BatchNo,

										};

									}

								});

								let colIndex = hot.propToCol('BatchNo'); // Replace 'id' with your dropdown column property

								let rowCount = hot.countRows();

								hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });

								hot.setDataAtCell(row, colIndex, ItemsOptions[0].id);

								hot.setDataAtCell(row,9, response.PurchRate);

								// Re-render the table after applying changes

								hot.render();

								count++; 								

							});                        

						}

					}

				}else if(prop == 'BatchNo')

				{

					let itemID = hot.getDataAtCell(row, 0); 

					

					if(newValue != null && newValue != '')

					{

						$.post(admin_url + 'KirtiOneOrder/GetItemBatchStock/',{ItemID:itemID,BatchID:newValue,CenterID : FromCenterID}).done(function(response){

							response = JSON.parse(response);

							if (response.length > 0) {

								hot.setDataAtCell(row,6, response[0].Stock);

								hot.setDataAtCell(row,7, response[0].ExpDate);

								hot.setDataAtCell(row,9, (parseFloat(response[0].PurchRate)));

								}else{

								hot.setDataAtCell(row,6, '');

								hot.setDataAtCell(row,7, '');

								hot.setDataAtCell(row,9, '');

							}

							//hot.setDataAtCell(row,9, '0');	

							count++; 

						});

						}else{

						hot.setDataAtCell(row, 8, '0');

						hot.setDataAtCell(row, 9, '0');

					}

				}else if(prop == 'OrderQty')

				{				

					let stock = hot.getDataAtCell(row, 6);  

					let qtyValue = hot.getDataAtCell(row, 8);  

					let rate = hot.getDataAtCell(row, 9);     

					let discount = hot.getDataAtCell(row, 10); 				

					//hot.setDataAtCell(row, 8, discount); 

					

					let unit  = hot.getDataAtCell(row, 2);  

					let packingqty = hot.getDataAtCell(row, 3);  

					let saleunit = hot.getDataAtCell(row, 5); 

					let gst = hot.getDataAtCell(row, 11);				

					

					if (!qtyValue) 

					{ 

						qtyValue = 0;

					}

					// console.log(stock);

					if(parseFloat(qtyValue) > parseFloat(stock)){

						alert("Qty Should Be Less Than Available Stock");

						hot.setDataAtCell(row, 8, '0');

						hot.setDataAtCell(row, 12, '0');

						hot.setDataAtCell(row, 13, '0');

						hot.setDataAtCell(row, 14, '0');

						hot.setDataAtCell(row, 15, '0');

						return false;

					}

					let fromcenter = $("#fromcentername option:selected").data("fromstatsid");							

					let tocenter = $("#tocentername option:selected").data("tostatsid");

					if (rate && qtyValue) 

					{         

						

						amount = rate * qtyValue;      

						

						

						if (discount) {

							amount = amount - (amount * discount / 100);

						}					

						

						let netAmount = amount + (amount * gst / 100); 

						netAmount = netAmount.toFixed(2);  

						

						let totalGST = amount * gst / 100; 

						let cgst = totalGST / 2;

						let sgst = totalGST / 2;

						let igst = totalGST;                                   

						

						let cgstcell = hot.getDataAtCell(row, 12);                                  

						let igstcell = hot.getDataAtCell(row, 14); 

						let cgstamt;

						let sgstamt;

						let igstamt;

						if(fromcenter == tocenter)

						{                                                       

							cgstamt = cgst;

							sgstamt = sgst;

							igstamt = 0.00;                                   

						} 

						else if(fromcenter != tocenter)

						{

							cgstamt = 0.00;

							sgstamt = 0.00;

							igstamt = igst;                                   

						} 					  

						

						hot.setDataAtCell(row, 15, parseFloat(netAmount).toFixed(2));               

						hot.setDataAtCell(row, 12, parseFloat(cgstamt).toFixed(2));  

						hot.setDataAtCell(row, 13, parseFloat(sgstamt).toFixed(2));  

						hot.setDataAtCell(row, 14, parseFloat(igstamt).toFixed(2));                                                                                      

					} 								

				}	

				else if(prop == 'Discount')

				{

					let qtyValue = hot.getDataAtCell(row, 8);  

					let rate = hot.getDataAtCell(row, 9);     

					let discount = hot.getDataAtCell(row, 10); 				

					//hot.setDataAtCell(row, 8, discount); 

					

					let unit  = hot.getDataAtCell(row, 2);  

					let packingqty = hot.getDataAtCell(row, 3);  

					let saleunit = hot.getDataAtCell(row, 5); 

					let gst = hot.getDataAtCell(row, 11);

					

					let fromcenter = $("#fromcentername option:selected").data("fromstatsid");							

					let tocenter = $("#tocentername option:selected").data("tostatsid");	

					

					if (rate && qtyValue) 

					{         

						

						amount = rate * qtyValue;      

						

						

						if (discount) {

							amount = amount - (amount * discount / 100);

						}					

						

						let netAmount = amount + (amount * gst / 100); 

						netAmount = netAmount.toFixed(2);  

						

						let totalGST = amount * gst / 100; 

						let cgst = totalGST / 2;

						let sgst = totalGST / 2;

						let igst = totalGST;                                   

						

						let cgstcell = hot.getDataAtCell(row, 12);                                  

						let igstcell = hot.getDataAtCell(row, 14); 

						let cgstamt;

						let sgstamt;

						let igstamt;

						if(fromcenter == tocenter)

						{                                                       

							cgstamt = cgst;

							sgstamt = sgst;

							igstamt = 0.00;                                   

						} 

						else if(fromcenter != tocenter)

						{

							cgstamt = 0.00;

							sgstamt = 0.00;

							igstamt = igst;                                   

						} 					  

						

						hot.setDataAtCell(row, 15, parseFloat(netAmount).toFixed(2));               

						hot.setDataAtCell(row, 12, parseFloat(cgstamt).toFixed(2));  

						hot.setDataAtCell(row, 13, parseFloat(sgstamt).toFixed(2));  

						hot.setDataAtCell(row, 14, parseFloat(igstamt).toFixed(2));                                                                                      

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

	

	

	function customRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		Handsontable.renderers.TextRenderer.apply(this, arguments);

		if(td.innerHTML != ''){

			td.innerHTML = td.innerHTML

			td.className = 'htRight';

		}

	}

	

	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		var selectedId;

		var optionsList = cellProperties.chosenOptions.data; 

		

		if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {

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

			var qty = hot.getDataAtCell(i, 8);

			

			var unit  = hot.getDataAtCell(i, 2);  

			var packingqty = hot.getDataAtCell(i, 3);  

			var saleunit = hot.getDataAtCell(i, 5);    

			

			

			

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

			var qty = hot.getDataAtCell(i, 8); 

			var amountval = hot.getDataAtCell(i, 9);             

			

			var unit  = hot.getDataAtCell(i, 2);  

			var packingqty = hot.getDataAtCell(i, 3);  

			var saleunit = hot.getDataAtCell(i, 5);                

			

			amount = amountval;

			if (!isNaN(qty) && !isNaN(amount) && qty > 0 && amount > 0) {

				var totalAmount = qty * amount ;

				totalamt += totalAmount;  

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

			var qty = hot.getDataAtCell(i, 8); 

			var amountval = hot.getDataAtCell(i, 9);

			var disc = hot.getDataAtCell(i, 10);

			

			var unit  = hot.getDataAtCell(i, 2);  

			var packingqty = hot.getDataAtCell(i, 3);  

			var saleunit = hot.getDataAtCell(i, 5); 

			

			

			amount = amountval;

			if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {

				var discountAmount = qty * amount * (disc / 100);

				totalDisc += discountAmount;  

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

			var qty = hot.getDataAtCell(i, 8);  

			var amountval = hot.getDataAtCell(i, 9);   

			var discount = hot.getDataAtCell(i, 10);   

			

			var unit  = hot.getDataAtCell(i, 2);  

			var packingqty = hot.getDataAtCell(i, 3);  

			var saleunit = hot.getDataAtCell(i, 5);                 

			

			

			amount = amountval;

			if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {                    

				var discountedAmount = amount * (1 - discount / 100);                        

				totalValue += qty * discountedAmount;

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

	

	function calculateTotalSgstAmt()

	{

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

	function calculateTotalIgstAmt()

	{

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

	function calculateTotalNetAmount()

	{

		var totalNetAmount = 0;  

		

		for (var i = 0; i < hot.countRows(); i++) 

		{

			var qty = hot.getDataAtCell(i, 8);  

			var rateval = hot.getDataAtCell(i, 9); 

			var discount = hot.getDataAtCell(i, 10); 

			var gst = hot.getDataAtCell(i, 11);    

			

			var unit  = hot.getDataAtCell(i, 2);  

			var packingqty = hot.getDataAtCell(i, 3);  

			var saleunit = hot.getDataAtCell(i, 5);                 

			

			

			

			amount = rateval * qty;

			if (!isNaN(qty) && !isNaN(amount) && !isNaN(gst) && qty > 0 && amount > 0) {                   

				if (discount) {

					amount = amount - (amount * discount / 100); 

				}

				

				var gstAmount = (amount * gst) / 100;                    

				var netAmount = amount + gstAmount;

				

				netAmount = parseFloat(netAmount.toFixed(2));                   

				totalNetAmount += netAmount;                   

			}                    

			

			

			var roundedNetAmount = totalNetAmount.toFixed(2);

			var decimalPart = parseFloat(roundedNetAmount.split('.')[1]);		

			if (decimalPart >= 50) {

				totalNetAmounts = Math.ceil(totalNetAmount); 

				} else {

				totalNetAmounts = Math.floor(totalNetAmount); 

			}

			

			var netPayableAmount = totalNetAmounts.toFixed(2);

		}           

		

		var totalNetPaybleAmount = document.querySelector('input[name="netpayableamt"]');

		if (totalNetPaybleAmount) {

			totalNetPaybleAmount.value = netPayableAmount;

		}        

		

		var difference = (totalNetAmount.toFixed(2) - parseFloat(netPayableAmount)).toFixed(2);

		

		var totalRoundOff = document.querySelector('input[name="total_roundoff_amt"]');

		if (totalRoundOff) {

			totalRoundOff.value = difference;

		}            

	}       

</script>



<style>

	.htLeft {

    text-align: left !important;  

	}

</style>











