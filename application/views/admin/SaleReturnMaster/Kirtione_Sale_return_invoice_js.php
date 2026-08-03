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

				pro_orderid: 'required',

				prd_date: 'required',            

				centername:'required',

				vendor:'required',

				Pr_no:'required',

				mobile_no:'required',

				state:'required',

				SalesRtnType:'required',

				SaleID:'required',

				prd_date: {

					remote: {

						url: site_url + "admin/misc/checkpurch_val",

						type: 'post',

						data: {

							order_date: function() {

								return $('input[name="prd_date"]').val();

							},

							PurchID: function() {

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

	

	<?php 

		if(!empty($pur_order_detail)){ 

		    

		    ?>

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

			},        

			readOnly: true

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

			data: 'SOQty',

			type: 'numeric',

			className: 'htLeft',

			readOnly: true 

		},

		 {

			data: 'ReturnOrderQty',

			type: 'numeric',

			className: 'htLeft'          

		},

        {

			data: 'SaleRate',

			type: 'text',          

			readOnly: false,

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

		},

		{

			data: 'BatchNo',

			type: 'text',  

			readOnly: true 

		},

        {

			data: 'ExpDate',

			type: 'date',

			width: 70,

			readOnly: true 

			

		},

		

		],

		licenseKey: 'non-commercial-and-evaluation',

		stretchH: 'all',

		width: '100%',

		columnHeaderHeight: 40,

		minRows: 20,

		maxRows: 40,

		rowHeaders: true,

		colWidths: [100,80, 60, 60, 70,70,50,70, 60, 50,50,50,50,70],

		colHeaders: [

        '<?php echo _l('Item Name'); ?>',

        '<?php echo _l('Hsn Code'); ?>',

        '<?php echo _l('Brand'); ?>',

        '<?php echo _l('MeasuredIn'); ?>',

        '<?php echo _l('Pack Qty'); ?>',

        '<?php echo _l('SI Qty(loose)'); ?>',

        '<?php echo _l('Return Qty'); ?>',

        '<?php echo _l('Rate(loose)'); ?>',

        '<?php echo _l('Disc Amt'); ?>',

        '<?php echo _l('GST%'); ?>',

        '<?php echo _l('CGSTAmt'); ?>',

        '<?php echo _l('SGSTAmt'); ?>',

		'<?php echo _l('IGSTAmt'); ?>',

		'<?php echo _l('Net Amt'); ?>',

		'<?php echo _l('Batch No.'); ?>',

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

		if(changes !== null){

			changes.forEach(([row, prop, oldValue, newValue]) => 

			{			 

				var count = 1; 

				if(prop == 'id')

				{

					if(newValue == null)

					{

      	                hot.setDataAtCell(row,1, '');

                      	hot.setDataAtCell(row,2, '');

                      	hot.setDataAtCell(row,3, '');

                      	hot.setDataAtCell(row,4, '');

                      	hot.setDataAtCell(row,5, '');

                      	hot.setDataAtCell(row,6, '');

                      	hot.setDataAtCell(row,7, '');

                      	hot.setDataAtCell(row,8, '');

      	                hot.setDataAtCell(row,9, '');

                      	hot.setDataAtCell(row,10, '');

                      	hot.setDataAtCell(row,11, '');

                      	hot.setDataAtCell(row,12, '');

                      	hot.setDataAtCell(row,13, '');

					}

				}else if(prop == 'ReturnOrderQty')

				{	

					if(prop == 'ReturnOrderQty'){

						let PIQty = hot.getDataAtCell(row, 5);  

						if(hot.getDataAtCell(row, 6) > PIQty){

							hot.setDataAtCell(row, 6, PIQty);

							alert('Qty Should Be Less Then Or Equals To SI Qty');

							return;

						}

					}

					let ProductName = hot.getDataAtCell(row, 0);  

					let qtyValue = hot.getDataAtCell(row, 6);  

					let rate = hot.getDataAtCell(row, 7);     

					let discount = hot.getDataAtCell(row, 8); 	

					

					let statsid = $("#centername option:selected").data("statsid");

					let vendorstate = $('#state').val();

					let SaleType = $('#SaleType').val();

					let gst = 0;

					if(SaleType == "T"){

					    gst = hot.getDataAtCell(row, 9);

					}

					if (ProductName) 

					{         

						let amount = 0;                 

						amount = rate * qtyValue; 

						var discountPercent = 0;

						if (discount) {

							discount = discount * qtyValue;

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

						if(vendorstate == 'MH')

						{                                                       

							cgstamt = cgst;

							sgstamt = sgst;

							igstamt = 0.00;                                   

						} else

						{

							cgstamt = 0.00;

							sgstamt = 0.00;

							igstamt = igst;                                   

						} 					  

						

						hot.setDataAtCell(row, 13, parseFloat(netAmount).toFixed(2));               

						hot.setDataAtCell(row, 10, parseFloat(cgstamt).toFixed(2));  

						hot.setDataAtCell(row, 11, parseFloat(sgstamt).toFixed(2));  

						hot.setDataAtCell(row, 12, parseFloat(igstamt).toFixed(2));                                                                                      

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

		if(td.innerHTML != ''){

			td.innerHTML = td.innerHTML + '%'

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

			var qty = hot.getDataAtCell(i, 6);

			totalQuantity += qty;

		}          

		var totalQtyInput = document.getElementById('total_qty_in_mt');

		if (totalQtyInput) {

			totalQtyInput.value = totalQuantity.toFixed(2); 

		}                       

	}

	

	function calaulateSubTotal()

	{

		var totalamt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var qty = hot.getDataAtCell(i, 6); 

			var amountval = hot.getDataAtCell(i, 7);  

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

			var qty = hot.getDataAtCell(i, 6); 

			var amountval = hot.getDataAtCell(i, 7);

			var disc = hot.getDataAtCell(i, 8);

			amount = amountval;

			if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {

				var discountAmount = disc*qty;

				totalDisc += discountAmount;  

			}             

		}          

		var totalDiscInput = document.getElementById('total_disc_in_mt');

		if (totalDiscInput) {

			totalDiscInput.value = parseFloat(totalDisc).toFixed(2); 

		}      

	}

	

	function calculateTotalValue()

	{

		var totalValue = 0;

		for (var i = 0; i < hot.countRows(); i++) {

			var qty = hot.getDataAtCell(i, 6);  

			var amountval = hot.getDataAtCell(i, 7);   

			var discount = hot.getDataAtCell(i, 8) * qty;   

			amount = amountval;

			if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {   

				var discountedAmount = discount;                        

				totalValue += (amount*qty) - discountedAmount;

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

			var cgstamt = hot.getDataAtCell(i, 10); 

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

			var sgstamt = hot.getDataAtCell(i, 11);

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

			var Igst = hot.getDataAtCell(i, 12);

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

			var qty = hot.getDataAtCell(i, 6);  

			var rateval = hot.getDataAtCell(i, 7); 

			var discount = hot.getDataAtCell(i, 8); 

			var gst = hot.getDataAtCell(i, 9);    

			

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

	

	function Totalvalueset(){
		
		function toNumber(value){
			if (value === null || value === undefined || value === '')
			{
				return 0;
			}

			// Remove commas if value is formatted like 1,250.00
			value = String(value).replace(/,/g, '').trim();
			var number = parseFloat(value);
			return isNaN(number) ? 0 : number;
		}

		var Taxable = toNumber(
			$('input[name="Total_value"]').val()
		);

		var cgst = toNumber(
			$('input[name="total_cgst_amt"]').val()
		);

		var sgst = toNumber(
			$('input[name="total_sgst_amt"]').val()
		);

		var igst = toNumber(
			$('input[name="total_igst_amt"]').val()
		);

		var total_tcs_amt = toNumber(
			$('input[name="total_tcs_amt"]').val()
		);

		var Other_amt = 0;
		var TotalInvAmount = Taxable + cgst + sgst + igst - Other_amt + total_tcs_amt;

		// Final safety check
		if (!isFinite(TotalInvAmount))
		{
			TotalInvAmount = 0;
		}


		var Round = Math.round(TotalInvAmount);

		var difference = TotalInvAmount - Round;


		var totalNetPaybleAmount = document.querySelector(
			'input[name="netpayableamt"]'
		);

		if (totalNetPaybleAmount)
		{
			totalNetPaybleAmount.value = Round.toFixed(2);
		}


		var totalRoundOff = document.querySelector(
			'input[name="total_roundoff_amt"]'
		);

		if (totalRoundOff)
		{
			totalRoundOff.value = difference.toFixed(2);
		}
	}

	

	$('#OtherAmt').on('blur', function() {

        Totalvalueset();

	});

    

    $('#total_tcs_amt').on('blur', function() {

        Totalvalueset();

	});

	

	$(document).off('submit', '#pur_order-form').on('submit', '#pur_order-form', function (e) {

		// console.log('Validation triggered');

		

		var isValid = true;

		var data = hot.getData(); // your Handsontable instance

		let TotalRtnQty = 0;

		for (let i = 0; i < data.length; i++) {

			let batchNo = data[i][14]; // BatchNo column

			let expDate = data[i][15]; // ExpDate column

			if(data[i][6] > 0){

			    TotalRtnQty += parseFloat(data[i][6]);

			}

			// Skip empty rows (if Item Name is empty)

			if (!data[i][0]) continue;

			

			if (!batchNo || batchNo.trim() === '') {

				alert(`Batch No is required in row ${i + 1}`);

				isValid = false;

				break;

			}

			

			if (!expDate || expDate.toString().trim() === '') {

				alert(`Expiry Date is required in row ${i + 1}`);

				isValid = false;

				break;

			}

		}

		if(TotalRtnQty <= 0){

		    alert("Please add return qty atleast one item.");

		}

		if (!isValid || TotalRtnQty <= 0) {

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











