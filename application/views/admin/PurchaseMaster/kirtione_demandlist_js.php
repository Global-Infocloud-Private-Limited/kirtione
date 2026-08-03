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
				centername: 'required',
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
			data: 'Item_Name',
			type: 'text',       
		    readOnly: false
		},
        { 
			data: 'Qty',
			type: 'numeric',          
			readOnly: false,
			numericFormat: {
             pattern: '0' 
            },
            validator: function(value, callback) {
                if (value === null || value === '') {
                    return callback(true);
                }
                const isValid = Number.isInteger(+value) && +value > 0;
                callback(isValid);
            },
             allowInvalid: true,
             className: 'htLeft'
		}
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		columnHeaderHeight: 40,
		minRows: 20,
		maxRows: 40,
		rowHeaders: true,
		colWidths: [100,80,80, 60, 60, 70,70,50,70, 60, 50,50,50,50,70],
		colHeaders: [
        '<?php echo _l('Item Name'); ?>',
        '<?php echo _l('Qty'); ?>',
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
	hot.addHook('beforeKeyDown', function(event) {
      const selected = hot.getSelectedLast();
      if (!selected) return;
    
      const colIndex = selected[1];
      const colProp = hot.colToProp(colIndex);
    
      if (colProp === 'Qty') {
        const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
        const isNumberKey = /^[0-9]$/.test(event.key);
    
        if (!isNumberKey && !allowedKeys.includes(event.key)) {
          event.preventDefault(); 
        }
      }
    });
	hot.addHook('afterChange', function(changes, src) {
		if(changes !== null){
			changes.forEach(([row, prop, oldValue, newValue]) => 
			{			 
				//var count = 1; 
				canterid = $("#centername").val();
				if(canterid == '')
				{
				     alert("Please Select center");return false;
				}
			});
		}
	});
	
	let isClearingInvalid = false;

    hot.addHook('afterValidate', function(isValid, value, row, prop, source) {
        if (!isValid && prop === 'Qty' && !isClearingInvalid) {
            isClearingInvalid = true;
            alert("Please enter valid qty.");
            hot.setDataAtCell(row, hot.propToCol(prop), '');
            isClearingInvalid = false;
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
	
	$('.save_detail').on('click', function() 
	{
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
</script>

<style>
	.htLeft {
    text-align: left !important;  
	}
</style>





