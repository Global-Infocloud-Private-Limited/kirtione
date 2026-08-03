<script>

	

    function removeCommas(str) {

		"use strict";

		return(str.replace(/,/g,''));

	}   

	

	$(function() {

		"use strict";

		

		validate_purorder_form();

		

		function validate_purorder_form(selector) {

			selector = typeof(selector) == 'undefined' ? '#sale_order-form' : selector;

			

			// Add custom validator for OthEffectOn

			$.validator.addMethod("othEffectRequired", function(value, element) {

				var otherAmt = parseFloat($('input[name="OtherAmt"]').val()) || 0;

				if (otherAmt > 0) {

					return $.trim(value) !== '';

				}

				return true; // not required if OtherAmt is 0

			}, "This field is required when Other Amount is greater than zero");

			

			$.validator.addMethod("EffectonRequired", function(value, element) {

				var OnlineAmt = parseFloat($('input[name="OnlineAmt"]').val()) || 0;

				if (OnlineAmt > 0) {

					return $.trim(value) !== '';

				}

				return true; // not required if OtherAmt is 0

			}, "This field is required when Online Amount is greater than zero");

			

			$.validator.addMethod("CheckTotalAmt", function(value, element) {

			    var ordtype = $('input[name="ordtype"]').val()

			    if(ordtype == "1"){

			        var CashAmt = parseFloat($('input[name="CashAmt"]').val()) || 0;

    				var OnlineAmt = parseFloat($('input[name="OnlineAmt"]').val()) || 0;

    				var TotalAmt = CashAmt + OnlineAmt;

    				var netpayableamt = parseFloat($('input[name="netpayableamt"]').val()) || 0;

    				if (TotalAmt != netpayableamt) {

    					return false;

    				}

    				return true;

			    }else{

			        return true;

			    }

			}, "Please Add Cash/Online Amt is equal to Invoice Amt");

			appValidateForm($(selector), {

				orderid: 'required',

				posted_date: 'required',

				centername: 'required',

				AccountID: 'required',

				phonenumber: 'required',

				billstate: 'required',

				ordstat: 'required',

				type: 'required',

				ordtype: 'required',

				ordfrom: 'required',

				OtherAmt: 'required',

				/*CashAmt: 'required',

				OnlineAmt: 'required',*/

				CashAmt: {

				    CheckTotalAmt: true

				},

				OnlineAmt: {

				    CheckTotalAmt: true

				},

				Effecton: {

					EffectonRequired: true

				},

				referenceno: {

					EffectonRequired: true

				},

				OthEffectOn: {

					othEffectRequired: true

				},

				CategoryType: 'required'

			});

		}

	});

	

	var newarr = [

    {

        id: "Loose",

        label: "Loose"

	},

	

    <?php

		$items = json_decode($item_order_detail, true);

		$addedUnits = ['Loose']; // start with "Loose" since it's already added

		

		foreach ($items as $each) {

			$unit = $each['SaleUnit'];

			if (!in_array($unit, $addedUnits)) {

				$addedUnits[] = $unit;

			?>

            {

                id: "<?= $unit ?>",

                label: "<?= $unit ?>"

			},

            <?php

			}

		}

	?>

	];

	

	var ApplyGstarr = [

    {

        id: "Including",

        label: "Including"

	},

	{

        id: "Excluding",

        label: "Excluding"

	},

	];

	

	function numberWithCommas(x) {

		"use strict";

		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

	}

	

	<?php 	

		if(!empty($item_order_detail)){ ?>

        var dataObject = <?php echo html_entity_decode($item_order_detail); ?>;

		<?php 

	}else{ ?>

        var dataObject = []; 

		

	<?php }?>  

	<?php

        if(is_admin()){

		?>

		var hidden_array = [];

        <?php

			}else{

		?>

		var hidden_array = [8];

        <?php

		}

	?>

	

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

				data: <?php echo json_encode($products); ?>

				// data: []

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

			data: 'GSTApply',

			renderer: customRenderer,

            editor: "chosen",

            chosenOptions: {

				data:  ApplyGstarr

			}          

		},

        {

			data: 'SaleUnit',

			renderer: customRenderer,

            editor: "chosen",

            chosenOptions: {

				data:  newarr

			}

		},

        {

			data: 'BatchNo',

			renderer: customRenderer,

            editor: "chosen",

            chosenOptions: {

				data:  []

			}

		},

        {

			data: 'StockQty',

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

			type: 'numeric', 

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

		minRows: 10,

		maxRows: 40,

		rowHeaders: true,

		colWidths: [100,50,60, 50, 50, 50,50,50,50, 60, 50,50,50,50,70],

		colHeaders: [

        '<?php echo _l('Item Name'); ?>',

        '<?php echo _l('HSN Code'); ?>',

        '<?php echo _l('Brand'); ?>',

        '<?php echo _l('Unit'); ?>',

        '<?php echo _l('Pack Qty'); ?>',

        '<?php echo _l('GST'); ?>',

        '<?php echo _l('Sale Unit'); ?>',

        '<?php echo _l('Batch No'); ?>',

        '<?php echo _l('Stock(Loose)'); ?>',

        '<?php echo _l('Qty'); ?>',

        '<?php echo _l('Sale Rate/ Unit'); ?>',

        '<?php echo _l('Disc/Unit'); ?>',

        '<?php echo _l('GST %'); ?>',

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

		hiddenColumns: {

			columns: hidden_array,

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

	hot.addHook('afterChange', function(changes, src) {

		if(changes !== null){

			changes.forEach(([row, prop, oldValue, newValue]) => 

			{			 

				var count = 1; 

				AccountID = $("#AccountID").val();

				centername = $("#centername").val();

				billstateid = $("#billstateid").val();

				CategoryType = $("#CategoryType").val();

				

				if(prop == 'id')

				{

					AccountID = $("#AccountID").val();

					if(newValue == null || newValue == '')

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

					    hot.setDataAtCell(row,14, '');

					    hot.setDataAtCell(row,15, '');

					    hot.setDataAtCell(row,16, '');

					    hot.setDataAtCell(row,17, '');

      	                //hot.loadData([]);

						calculateTotal();

                      	

					}else{

						if(AccountID == ''){

							alert("Please Select Party");

							hot.setDataAtCell(row,0, '');

							return false;

						}else if(centername == ''){

							alert("Please Select Center Name ");

							hot.setDataAtCell(row,0, '');

							return false;

						}else if(billstateid == ''){

							alert("Please Select Billing State");

							hot.setDataAtCell(row,0, '');

							return false;

						}else if(CategoryType == ''){

							alert("Please Select Category Type");

							hot.setDataAtCell(row,0, '');

							return false;

						}else{          	            

							count++;	
							console.log('newValue',newValue);
							console.log('centername',centername);
							console.log('AccountID',AccountID);

							$.post(admin_url + 'KirtiOneOrder/GetItemDetailsForDirectSaleOld/'+newValue+ '/'+centername+ '/'+AccountID).done(function(response){
							// $.post(admin_url + 'KirtiOneOrder/GetItemDetailsForDirectSale/'+newValue+ '/'+centername+ '/'+AccountID).done(function(response){

								response = JSON.parse(response);

								let UnitSaleRate = parseFloat(response.SaleRate) / parseFloat(response.PackingQty);

								

								hot.setDataAtCell(row,1, response.hsn_code);					

								hot.setDataAtCell(row,2, response.BrandName);

								hot.setDataAtCell(row,3, response.unit);

								hot.setDataAtCell(row,4, response.PackingQty);

								hot.setDataAtCell(row,5, "Including");

								hot.setDataAtCell(row,6, response.unit);   

								// hot.setDataAtCell(row,8, response.StockQty);							

								hot.setDataAtCell(row,8, '0');							

								hot.setDataAtCell(row,9, '0');							

								

								hot.setDataAtCell(row,11, '0');	

								hot.setDataAtCell(row,12, response.taxrate);

								hot.setDataAtCell(row,13, '0');

								hot.setDataAtCell(row,14, '0');

								hot.setDataAtCell(row,15, '0');

								hot.setDataAtCell(row,16, '0');

								

								var saleunit = response.unit; // e.g., "BOX"

								

								// Ensure new option is added to newarr (the global options list)

								if (!newarr.some(opt => opt.id === saleunit)) {

									newarr.push({ id: saleunit, label: saleunit });

								}

								

								// Force Handsontable to refresh the chosenOptions

								hot.updateSettings({

									columns: hot.getSettings().columns.map((col, index) => {

										if (index === 6) { // Assuming column index 6 is SaleUnit

											return {

												...col,

												chosenOptions: {

													data: newarr

												}

											};

										}

										return col;

									})

								});

								// Set the cell value and re-render

								hot.setDataAtCell(row, 6, "Loose");

								hot.setDataAtCell(row,10, UnitSaleRate); 

								hot.render();

								let colIndex = hot.propToCol('BatchNo'); // Replace 'id' with your dropdown column property

								if(!empty(response.BatchList)){

								        var ItemsOptions = response.BatchList.map(function(ListBatch) {

    								    if(ListBatch.Stock > 0){

    								        return {

        										id: ListBatch.BatchNo,

        										label: ListBatch.BatchNo,

        									};

    								    }

    								});

    								

    								let rowCount = hot.countRows();

    								hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });

    								hot.setDataAtCell(row, colIndex, ItemsOptions[0].id);

								}else{

								    hot.setCellMeta(row, colIndex, 'chosenOptions', { data: [] });

    								hot.setDataAtCell(row, colIndex, "");

								}

								

								hot.setDataAtCell(row,10, response.sale_rate);

								// Re-render the table after applying changes

								hot.render();

								count++; 

								

							});                        

						}

					}

				}else if(prop == 'OrderQty')

				{				

					

					

					let StockQty = hot.getDataAtCell(row, 8);  

					let qtyValue = hot.getDataAtCell(row, 9);  

					let SaleUnit = hot.getDataAtCell(row, 6);  

					let Unit = hot.getDataAtCell(row, 3);  

					let rate = hot.getDataAtCell(row, 10);     

					let discount = hot.getDataAtCell(row, 11);

					let packingqty = hot.getDataAtCell(row, 4);  

					

					if(parseFloat(qtyValue) > parseFloat(StockQty)){

						alert('Stock Not Available');

						hot.setDataAtCell(row, 9, 0); 

						return;

					}

					

					let ItemTotalAmt = 0;

					let TotalDisc = 0;

					TotalDisc = (parseFloat(discount)) * parseFloat(qtyValue);

					ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

					

					let TaxableAmt = 0;

					let NetAmt = 0;

					TaxableAmt = ItemTotalAmt - TotalDisc;

					let gst = hot.getDataAtCell(row, 12);

					let ApplyGst = hot.getDataAtCell(row, 5);

					let statsid = $("#centername option:selected").data("statsid");

					let partystate = $('#billstateid').val();

					let SGSTAmt = 0;

					let CGSTAmt = 0;

					let IGSTAmt = 0;

					if(ApplyGst == "Excluding"){

					    if(statsid == partystate){

    					    let CGSTPer = parseFloat(gst) / 2;

    					    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

    					    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

							}else{

    					    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

						}

					}

					NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt)

					

					hot.setDataAtCell(row, 16, parseFloat(NetAmt).toFixed(2));               

					hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2));     

				}else if(prop == 'PurchRate')

				{				

					let qtyValue = hot.getDataAtCell(row, 9);  

					let SaleUnit = hot.getDataAtCell(row, 6);  

					let Unit = hot.getDataAtCell(row, 3);  

					let rate = hot.getDataAtCell(row, 10);     

					let discount = hot.getDataAtCell(row, 11);

					let packingqty = hot.getDataAtCell(row, 4);  

					let ItemTotalAmt = 0;

					let TotalDisc = 0;

					TotalDisc = (parseFloat(discount)) * parseFloat(qtyValue);

					ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

					let TaxableAmt = 0;

					let NetAmt = 0;

					TaxableAmt = ItemTotalAmt - TotalDisc;

					let gst = hot.getDataAtCell(row, 11);

					let ApplyGst = hot.getDataAtCell(row, 5);

					let statsid = $("#centername option:selected").data("statsid");

					let partystate = $('#billstateid').val();

					let SGSTAmt = 0;

					let CGSTAmt = 0;

					let IGSTAmt = 0;

					if(ApplyGst == "Excluding"){

					    if(statsid == partystate){

    					    let CGSTPer = parseFloat(gst) / 2;

    					    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

    					    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

							}else{

    					    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

						}

					}

					NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt)

					

					hot.setDataAtCell(row, 16, parseFloat(NetAmt).toFixed(2));               

					hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2));      

				}else if(prop == 'SaleUnit')

				{			

				    hot.setDataAtCell(row, 9, 0);

				    let packingqty = hot.getDataAtCell(row, 4);  

					let Stockqty = hot.getDataAtCell(row, 8); 

					if(oldValue == "Loose"){

					    newStockQty = Stockqty / packingqty;

					}else{

					    newStockQty = Stockqty * packingqty;

					}

					hot.setDataAtCell(row, 8, parseFloat(newStockQty).toFixed(3));

					let qtyValue = hot.getDataAtCell(row, 9);  

					let SaleUnit = hot.getDataAtCell(row, 6);  

					let Unit = hot.getDataAtCell(row, 3);  

					let rate = 0;     

					let discount = 0;

					

					let ItemTotalAmt = 0;

					let TotalDisc = 0;

					TotalDisc = (parseFloat(discount)) * parseFloat(qtyValue);

					ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

					let TaxableAmt = 0;

					let NetAmt = 0;

					TaxableAmt = ItemTotalAmt - TotalDisc;

					let gst = hot.getDataAtCell(row, 12);

					let ApplyGst = hot.getDataAtCell(row, 5);

					let statsid = $("#centername option:selected").data("statsid");

					let partystate = $('#billstateid').val();

					let SGSTAmt = 0;

					let CGSTAmt = 0;

					let IGSTAmt = 0;

					if(ApplyGst == "Excluding"){

					    if(statsid == partystate){

    					    let CGSTPer = parseFloat(gst) / 2;

    					    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

    					    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

							}else{

    					    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

						}

					}

					NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt)

				    hot.setDataAtCell(row, 11, 0);// If change sale unit then discount will set as zero

				    hot.setDataAtCell(row, 10, 0);// If change sale unit then discount will set as zero

					hot.setDataAtCell(row, 16, parseFloat(NetAmt).toFixed(2));               

					hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2));    

				}else if(prop == 'Discount')

				{

					let qtyValue = hot.getDataAtCell(row, 9);  

					let SaleUnit = hot.getDataAtCell(row, 6);  

					let Unit = hot.getDataAtCell(row, 3);  

					let rate = hot.getDataAtCell(row, 10);     

					let discount = hot.getDataAtCell(row, 11);

					let packingqty = hot.getDataAtCell(row, 4);  

					let ItemTotalAmt = 0;

					let TotalDisc = 0;

					

					if(parseFloat(discount) > parseFloat(rate)){

						alert('Discount Should Be Less Than Sale Rate.');

						hot.setDataAtCell(row, 11, 0); 

						return;

					}

					

					TotalDisc = (parseFloat(discount)) * parseFloat(qtyValue);

					ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

					let TaxableAmt = 0;

					let NetAmt = 0;

					TaxableAmt = ItemTotalAmt - TotalDisc;

					let gst = hot.getDataAtCell(row, 12);

					let ApplyGst = hot.getDataAtCell(row, 5);

					let statsid = $("#centername option:selected").data("statsid");

					let partystate = $('#billstateid').val();

					let SGSTAmt = 0;

					let CGSTAmt = 0;

					let IGSTAmt = 0;

					if(ApplyGst == "Excluding"){

					    if(statsid == partystate){

    					    let CGSTPer = parseFloat(gst) / 2;

    					    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

    					    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

							}else{

    					    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

						}

					}

					NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt)

					

					hot.setDataAtCell(row, 16, parseFloat(NetAmt).toFixed(2));               

					hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2)); 					

				}else if(prop == 'GSTApply')

				{

					let qtyValue = hot.getDataAtCell(row, 9);  

					let SaleUnit = hot.getDataAtCell(row, 6);  

					let Unit = hot.getDataAtCell(row, 3);  

					let rate = hot.getDataAtCell(row, 10);     

					let discount = hot.getDataAtCell(row, 11);

					let packingqty = hot.getDataAtCell(row, 4);  

					let ItemTotalAmt = 0;

					let TotalDisc = 0;

					TotalDisc = (parseFloat(discount)) * parseFloat(qtyValue);

					ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

					let TaxableAmt = 0;

					let NetAmt = 0;

					TaxableAmt = ItemTotalAmt - TotalDisc;

					let gst = hot.getDataAtCell(row, 12);

					let ApplyGst = hot.getDataAtCell(row, 5);

					let statsid = $("#centername option:selected").data("statsid");

					let partystate = $('#billstateid').val();

					let SGSTAmt = 0;

					let CGSTAmt = 0;

					let IGSTAmt = 0;

					if(ApplyGst == "Excluding"){

					    if(statsid == partystate){

    					    let CGSTPer = parseFloat(gst) / 2;

    					    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

    					    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

							}else{

    					    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

						}

					}

					NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt)

					

					hot.setDataAtCell(row, 16, parseFloat(NetAmt).toFixed(2));               

					hot.setDataAtCell(row, 13, parseFloat(CGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 14, parseFloat(SGSTAmt).toFixed(2));  

					hot.setDataAtCell(row, 15, parseFloat(IGSTAmt).toFixed(2)); 					

				}else if(prop == 'BatchNo')

				{

					let itemID = hot.getDataAtCell(row, 0); 

					

					if(newValue != null && newValue != '')

					{

					    $.post(admin_url + 'KirtiOneOrder/GetItemBatchStock/',{ItemID:itemID,BatchID:newValue,CenterID : centername}).done(function(response){
					    // $.post(admin_url + 'KirtiOneOrder/GetItemBatchStockDSO/',{ItemID:itemID,BatchID:newValue,CenterID : centername}).done(function(response){

    						response = JSON.parse(response);

    						hot.setDataAtCell(row,8, response[0].Stock);

    						hot.setDataAtCell(row,17, response[0].ExpDate);

    						hot.setDataAtCell(row,9, '0');	

    						count++; 

    					});

					}else{

					    hot.setDataAtCell(row, 8, '0');

					    hot.setDataAtCell(row, 9, '0');

					}

				}else if(prop == 'Netamt')

				{

				    calculateTotal();

				}				

			});

		}

	});

	

	calculateTotal();

	

	function isNumber(evt) {

		evt = (evt) ? evt : window.event;

		var charCode = (evt.which) ? evt.which : evt.keyCode;

		if (charCode > 31 && (charCode < 48 || charCode > 57)) {

			return false;

		}

		return true;

	}

	

	$('.save_detail').on('click', function() {

	    var ordtype = $('#ordtype').val();

	    if(ordtype == "1"){

	        var CashAmt = parseFloat($('#CashAmt').val()) || 0;

            var OnlineAmt = parseFloat($('#OnlineAmt').val()) || 0;

            var OtherAmt = parseFloat($('#OtherAmt').val()) || 0;

            var InvoiceAmt = parseFloat($('#netpayableamt').val()) || 0;

            

            var totalPaid = CashAmt + OnlineAmt;

        

            if (totalPaid === InvoiceAmt) {

                $('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData()));   

            } else {

                alert('Cash Amounts do not match the Invoice Total!');

                return false; 

            }

	    }else{

	        $('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData()));  

	    }

	    

		//$('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData()));   

	});

	

	$('.update_detail').on('click', function() {

	    var ordtype = $('#ordtype').val();

	    if(ordtype == "1"){

	        var CashAmt = parseFloat($('#CashAmt').val()) || 0;

            var OnlineAmt = parseFloat($('#OnlineAmt').val()) || 0;

            var OtherAmt = parseFloat($('#OtherAmt').val()) || 0;

            var InvoiceAmt = parseFloat($('#netpayableamt').val()) || 0;

            

            var totalPaid = CashAmt + OnlineAmt;

        

            if (totalPaid === InvoiceAmt) {

                $('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData())); 

            } else {

                alert('Cash Amounts do not match the Invoice Total!');

                return false; 

            }

	    }else{

	        $('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData())); 

	    }

		//$('input[name="sale_invoice_detail"]').val(JSON.stringify(hot.getData()));   

	});

	

	

	// function customRenderer(instance, td, row, col, prop, value, cellProperties) {

	// "use strict";

	// Handsontable.renderers.TextRenderer.apply(this, arguments);

	// if(td.innerHTML != ''){

	// td.innerHTML = td.innerHTML + '%'

	// td.className = 'htRight';

	// }

	// }

	function customRenderer(instance, td, row, col, prop, value, cellProperties) {

		"use strict";

		Handsontable.renderers.TextRenderer.apply(this, arguments);

		

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

	function calculateTotal() 

	{

		let TotalLooseQty = 0;

		let TotalItemAmt = 0;

		let TotalDiscAmt = 0;

		let TotalTaxableAmt = 0;

		let TotalCGSTAmt = 0;

		let TotalSGSTAmt = 0;

		let TotalIGSTAmt = 0;

		let TotalNetAmt = 0;

		for (var i = 0; i < hot.countRows(); i++) {

		    if(hot.getDataAtCell(i, 0)){

		        let SaleUnit = hot.getDataAtCell(i, 6);  

    			let Unit = hot.getDataAtCell(i, 3); 

    			let packingqty = hot.getDataAtCell(i, 4);  

    			let qtyValue = hot.getDataAtCell(i, 9);

    			let rate = hot.getDataAtCell(i, 10); // rate in per loose qty

    			let GstApply = hot.getDataAtCell(i, 5);

    			let LooseQty = 0;

    			let ItemTotalAmt = 0;

    			let TaxableAmt = 0;

    			let gst = hot.getDataAtCell(i, 12);

    			if(SaleUnit == Unit){

    			    LooseQty = parseFloat(qtyValue) * parseFloat(packingqty);

					}else{

    			    LooseQty = parseFloat(qtyValue);

				}

		        TotalLooseQty += LooseQty;

		        ItemTotalAmt = parseFloat(qtyValue) * parseFloat(rate);

		        

		        TotalItemAmt += ItemTotalAmt;

		        let discount = hot.getDataAtCell(i, 11);

		        let TotalDisc = 0;

		        if(parseFloat(discount) > 0){

		            TotalDisc = (parseFloat(discount) * parseFloat(qtyValue));

				}

		        TotalDiscAmt += TotalDisc;

		        TaxableAmt = ItemTotalAmt - TotalDisc;

		        TotalTaxableAmt += TaxableAmt;

		        let statsid = $("#centername option:selected").data("statsid");

    			let partystate = $('#billstateid').val();

    			let SGSTAmt = 0;

    			let CGSTAmt = 0;

    			let IGSTAmt = 0;

    			let NetAmt = 0;

		        if(GstApply == "Including"){

		            

					}else{

		            if(statsid == partystate){

        			    let CGSTPer = parseFloat(gst) / 2;

        			    SGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

        			    CGSTAmt = parseFloat(TaxableAmt)*(parseFloat(CGSTPer)/100);

						}else{

        			    IGSTAmt = parseFloat(TaxableAmt)*(parseFloat(gst)/100);

					}

				}

		        TotalCGSTAmt += CGSTAmt;

    			TotalSGSTAmt += SGSTAmt;

    			TotalIGSTAmt += IGSTAmt;

    			NetAmt = parseFloat(TaxableAmt) + parseFloat(SGSTAmt) + parseFloat(CGSTAmt) + parseFloat(IGSTAmt);

    			TotalNetAmt += 	NetAmt;	

			}

		}

		//alert(TotalNetAmt);

		Other_amt = $('input[name="OtherAmt"]').val();

		if(Other_amt == ""){

		    Other_amt = 0;

		}

		TotalNetAmt += parseFloat(Other_amt);

		BillAmt = Math.round(TotalNetAmt);

		RoundOff = TotalNetAmt - BillAmt; 

		$("#total_qty_in_mt").val(parseFloat(TotalLooseQty).toFixed(2));

		$("#total_amt_in_mt").val(parseFloat(TotalItemAmt).toFixed(2));

		$("#total_disc_in_mt").val(parseFloat(TotalDiscAmt).toFixed(2));

		$("#Total_value").val(parseFloat(TotalTaxableAmt).toFixed(2));

		$("#total_cgst_amt").val(parseFloat(TotalCGSTAmt).toFixed(2));

		$("#total_sgst_amt").val(parseFloat(TotalSGSTAmt).toFixed(2));

		$("#total_igst_amt").val(parseFloat(TotalIGSTAmt).toFixed(2));

		$("#total_roundoff_amt").val(parseFloat(RoundOff).toFixed(2));

		$("#netpayableamt").val(parseFloat(BillAmt).toFixed(2));

	}

	$('#OtherAmt').on('blur', function() {

        calculateTotal();

	});

	

	function fetchCenterName(CenterName,accountID,callback)

	{

		$.ajax({

			url: "<?php echo admin_url(); ?>ItemMaster/GetCenterName",  

			dataType: "json",  

			method: "POST",    

			data: {CenterName:CenterName,accountID:accountID }, 

			

			beforeSend: function () {                   

				$('.searchh2').css('display', 'block');

				$('.searchh2').css('color', 'blue');

			},

			

			complete: function () {                    

				$('.searchh2').css('display', 'none');

			},

			

			success: function (data) {            

				if (data.success) 

				{ 

					var Centers = data.centerDetails;

					var Clients = data.clientDetails;   

					var isSameState = (Centers.state && Clients.state) && (Centers.state === Clients.state);                                                        

					callback(isSameState);

                    } else {                    

					alert('Product details not found!');

				}

			},

			error: function (xhr, status, error) {               

				console.error('Error fetching product details:', error);

				alert('Error fetching product details');

			}

		});  

	}

	

	$('#AccountID').on('change', function() 

	{           

		var AccountID = $("#AccountID").val();    

 	    if(AccountID == "new")

		{

			$("#villagename").val(''); 

		}               

		if(AccountID != "new"){

		    $.ajax({

    			url:"<?php echo admin_url(); ?>KirtiOneOrder/GetAccountWiseFarmerDetails",

    			dataType:"JSON",

    			method:"POST",

    			data:{AccountID:AccountID},

    			beforeSend: function () {

    				$('.searchh2').css('display','block');

    				$('.searchh2').css('color','blue');

    			},

    			complete: function () {

    				$('.searchh2').css('display','none');

    			},

    			success:function(data)

    			{	            

    				var ClientData = data.clientDetails;     

    				var ProductDetails = data.historyDetails;

    				var OrderDetails = data.orderDetails;   

    				var SalesDetails = data.salesDetails;  

    				var LedgerBalance = data.ClosingBalance;                   

    				

    				$('#phonenumber').val(ClientData.phonenumber);   

    				$('#partystate').val(ClientData.state);   

    				$('#pin').val(ClientData.zip);                            

    				$('#loc').val(ClientData.loc);   

    				$('#street').val(ClientData.street);     

    				$('#house').val(ClientData.house);  

    				

    				$('#billstateid').val(ClientData.state);

    				$('.selectpicker').selectpicker('refresh') 

    				$('#stateid').val(ClientData.state);

    				$('.selectpicker').selectpicker('refresh') 

    				

    				$('#city').val(ClientData.dist);

    				$('.selectpicker').selectpicker('refresh') 

    				

    				$('#subdist').val(ClientData.subdist);

    				$('.selectpicker').selectpicker('refresh')     

    				

    				if (LedgerBalance == null || LedgerBalance === '') {

    					$('#ledgerbal').val(0);  

    					$('#ledgerbal').removeClass('redText greenText'); 

    				}

    				else if (LedgerBalance > 0 || LedgerBalance.includes("Dr")) {

    					$('#ledgerbal').val(LedgerBalance).addClass('redText').removeClass('greenText');

                        } else {

    					$('#ledgerbal').val(LedgerBalance).addClass('greenText').removeClass('redText');

    				}

    				$('#ShippingID').empty().selectpicker('refresh');

    				var optionsHtml = '<option value="">None Selected</option>';

    				var optionsHtml = '<option value="new">New Address</option>';

    				

    				console.log(ClientData.ShippingData.length);

    				for (var i = 0; i < ClientData.ShippingData.length; i++) {

    					optionsHtml += '<option value="' + ClientData.ShippingData[i].id + '">' + ClientData.ShippingData[i].shipping_label + '</option>';

    				}

    				$('#ShippingID').html(optionsHtml);

    				$('.selectpicker').selectpicker('refresh');

    			}

    		});

		}else{

		    $('#phonenumber').val("");   

			$('#partystate').val("");   

			$('#pin').val("");                            

			$('#loc').val("");   

			$('#street').val("");     

			$('#house').val("");  

			$('#billstateid').val("");

			$('.selectpicker').selectpicker('refresh') 

			$('#stateid').val("");

			$('.selectpicker').selectpicker('refresh') 

			

			$('#city').val("");

			$('.selectpicker').selectpicker('refresh') 

			

			$('#subdist').val("");

			$('.selectpicker').selectpicker('refresh')     

			$('#ledgerbal').val(0);  

			$('#ShippingID').empty().selectpicker('refresh');

			var optionsHtml = '<option value="">None Selected</option>';

			var optionsHtml = '<option value="new">New Address</option>';

			$('#ShippingID').html(optionsHtml);

			$('.selectpicker').selectpicker('refresh');

		}

	});

	

	

	

    $('#centername').on('change', function(){

        ItemDataLoad();

        $('#CategoryType').val("");

        $('.selectpicker').selectpicker('refresh');

    })

	$('#CategoryType').on('change', function(){

		ItemDataLoad();

	});

    function ItemDataLoad()

    {

        var CategoryType = $("#CategoryType").val();

		var CenterID = $("#centername").val();

		if(CenterID != "" && CenterID != null && CategoryType != "" && CategoryType != null){

		    hot.loadData([]);

		    calculateTotal();

		    $.ajax({

				url:"<?php echo admin_url(); ?>KirtiOneOrder/GetItemListData",

				dataType:"JSON",

				method:"POST",

				data:{CategoryType:CategoryType,CenterID:CenterID},

				beforeSend: function () {

					$('.searchh2').css('display','block');

					$('.searchh2').css('color','blue');

				},

				complete: function () {

					$('.searchh2').css('display','none');

				},

				success:function(response)

				{	

				    let rowCount = hot.countRows();

				    for (let row = 0; row < rowCount; row++) {

				        let ItemID = hot.getDataAtCell(row, 0);

				        //if(ItemID == "" || ItemID == null){

				            hot.updateSettings({

    							columns: hot.getSettings().columns.map((col, index) => {

    								if (index === 0) { // Assuming column index 6 is SaleUnit

    									return {

    										...col,

    										chosenOptions: {

    											data: response

    										}

    									};

    								}

    								return col;

    							})

    						});   

				        //}

				    }

				    hot.render(); 

				}

			});

		}else{

		    hot.loadData([]);

		}

    }

	

</script>



<style>

	.htLeft {

    text-align: left !important;  

	}

</style>











