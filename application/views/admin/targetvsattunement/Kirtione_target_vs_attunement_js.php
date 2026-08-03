<script>
	
    function removeCommas(str) {
		"use strict";
		return(str.replace(/,/g,''));
	}   
	
	// $(function(){
		// "use strict";
		// validate_purorder_form();
		// function validate_purorder_form(selector) {
			// selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;
			// appValidateForm($(selector), {
				// pro_orderid: 'required',
				// prd_date: 'required',            
				// centername:'required',
				// vendor:'required',
				// Pr_no:'required',
				// mobile_no:'required',
				// state:'required',
				// SalesRtnType:'required',
				
				// prd_date: {
					// remote: {
						// url: site_url + "admin/misc/checkpurch_val",
						// type: 'post',
						// data: {
							// order_date: function() {
								// return $('input[name="prd_date"]').val();
							// },
							// PurchID: function() {
								// return $('input[name="pur_order_number"]').val();
							// }
						// }
					// }
				// },           
				// /*OthEffectOn: {
					// required: function(element) {
					// return $('#OtherAmt').val() != '';
					// }
				// },*/
				
			// });
		// }
	// });
	
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
			data: 'AccountID', 
			readOnly: true 
		},
        {
			data: 'staff_name',
			readOnly: true
		},
        { 
			data: 'APR',
			type: 'numeric',           
			
		},
        {
			data: 'MAY',
			type: 'numeric',                    
			   
		},
        {
			data: 'JUN',
			type: 'numeric',
			
			        
		},
        {
			data: 'JUL',
			type: 'numeric',        
			           
		},
        {
			data: 'AUG',
			type: 'numeric',          
			
		},
        {
			data: 'SEP',
			type: 'numeric',
			
		},
		 {
			data: 'OCT',
			type: 'numeric',
			   
		},
        {
			data: 'NOV',
			type: 'numeric',          
			
		},
        {
			data: 'DESC',
			type: 'numeric',
			 
		},
        {
			data: 'JAN',
			type: 'numeric',         
			
		},
        {
			data: 'FEB',
			type: 'numeric',          
			
		},
        { 
			data: 'MAR',
			type: 'numeric',           
			
		},
        
		],
		stretchH: 'all',
        rowHeaders: true,
        width: '100%',
        height: 500,
        licenseKey: 'non-commercial-and-evaluation',
		// licenseKey: 'non-commercial-and-evaluation',
		// stretchH: 'all',
		// width: '100%',
		// columnHeaderHeight: 40,
		// minRows: 20,
		// maxRows: 500,
		// rowHeaders: true,
		// colWidths: [100,80, 60, 60, 70,70,50,70, 60, 50,50,50,50,70],
		columnSorting: true,
		hiddenColumns: {
        columns: [0],
        indicators: false
    },
		colHeaders: [
		'<?php echo _l('StaffID'); ?>',
        '<?php echo _l('Staff Name'); ?>',
        '<?php echo _l('Apr'); ?>',
        '<?php echo _l('May'); ?>',
        '<?php echo _l('Jun'); ?>',
        '<?php echo _l('Jul'); ?>',
		'<?php echo _l('Aug'); ?>',
        '<?php echo _l('Sep'); ?>',
        '<?php echo _l('Oct'); ?>',
        '<?php echo _l('Nov'); ?>',
        '<?php echo _l('Dec'); ?>',
        '<?php echo _l('Jan'); ?>',
        '<?php echo _l('Feb'); ?>',
		'<?php echo _l('Mar'); ?>',
        
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
				vendor_id = $("#vendor").val();
				
				if(prop == 'id')
				{
					vendor_id = $("#vendor").val();
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
				}
				else if(prop == 'ReturnOrderQty')
				{	
					if(prop == 'ReturnOrderQty'){
						let PIQty = hot.getDataAtCell(row, 7);  
						if(hot.getDataAtCell(row, 8) > PIQty){
							hot.setDataAtCell(row, 8, PIQty);
							alert('Qty Should Be Less Then Or Equals To SI Qty');
							return;
						}
					}
					let ProductName = hot.getDataAtCell(row, 0);  
					let qtyValue = hot.getDataAtCell(row, 8);  
					let rate = hot.getDataAtCell(row, 9);     
					let discount = hot.getDataAtCell(row, 10); 				
					//hot.setDataAtCell(row, 8, discount); 
					
					let unit  = hot.getDataAtCell(row, 3);  
					let packingqty = hot.getDataAtCell(row, 4);  
					let saleunit = hot.getDataAtCell(row, 6); 
					let gst = hot.getDataAtCell(row, 11);
					
					let statsid = $("#centername option:selected").data("statsid");
					let vendorstate = $('#state').val();				
					
					if (ProductName) 
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
						if(vendorstate == 'MH')
						{                                                       
							cgstamt = cgst;
							sgstamt = sgst;
							igstamt = 0.00;                                   
						} 
						else if(vendorstate != 'MH')
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
	

	
	$(document).off('submit', '#pur_order-form').on('submit', '#pur_order-form', function (e) {
		// console.log('Validation triggered');
		
		var isValid = true;
		var data = hot.getData(); // your Handsontable instance
		
		// for (let i = 0; i < data.length; i++) {
			// let batchNo = data[i][15]; // BatchNo column
			// let expDate = data[i][16]; // ExpDate column
			
			// // Skip empty rows (if Item Name is empty)
			// if (!data[i][0]) continue;
			
			// if (!batchNo || batchNo.trim() === '') {
				// alert(`Batch No is required in row ${i + 1}`);
				// isValid = false;
				// break;
			// }
			
			// if (!expDate || expDate.toString().trim() === '') {
				// alert(`Expiry Date is required in row ${i + 1}`);
				// isValid = false;
				// break;
			// }
		// }
		
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





