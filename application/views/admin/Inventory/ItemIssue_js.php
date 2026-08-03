<script>

    $(function(){
        "use strict";
    	validate_purorder_form();
        function validate_purorder_form(selector) {
            selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;
            appValidateForm($(selector), {
                issue_orderid: 'required',
                issue_date: 'required',
                CenterID: 'required',
                WHID:'required',
            });
        }
    });
    
    var dataObject = [];
    var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
    
    var hotSettings = {
      data: dataObject,
      columns: [
        
        {
          data: 'id',
          renderer: customDropdownRenderer,
          editor: "chosen",
          width: 180,
          chosenOptions: {
              data: <?php echo json_encode($ItemList); ?>
          }
        },
        
        {
          data: 'uom',
          type: 'text',
          width:70,
          readOnly: true,
        },
        {
          data: 'system_qty',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
          readOnly: true,
        },
        {
          data: 'isuue_qty',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'rate',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'value',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:90,
        },
        {
          data: 'reason',
          type: 'text',
          width:290,
        }
      
      ],
      licenseKey: 'non-commercial-and-evaluation',
      stretchH: 'all',
      width: '100%',
    //   autoWrapRow: true,
    //   rowHeights: 30,
      columnHeaderHeight: 40,
      minRows: 10,
      maxRows: 40,
      rowHeaders: true,
      colWidths: [40,180,100,60,60,60,60,110,80,80,60,110,60,130],
      colHeaders: [
        '<?php echo "Item"; ?>',
        '<?php echo "UOM"; ?>',
        '<?php echo "System Qty(MT)"; ?>',
        '<?php echo "Issue Qty(MT)"; ?>',
        '<?php echo "Rate / MT"; ?>',
        '<?php echo "Value"; ?>',
        '<?php echo "Reason"; ?>',
      ],
       columnSorting: {
        indicator: true
      },
      autoColumnSize: {
        samplingRatio: 23
      },
    //   dropdownMenu: true,
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
	    changes.forEach(([row, prop, oldValue, newValue]) => {
	        var count = 1; 
            if(prop == 'id'){
                hot.setDataAtCell(row,1, '');
                hot.setDataAtCell(row,2, 0);
                hot.setDataAtCell(row,3, 0);
                hot.setDataAtCell(row,4, 0);
                hot.setDataAtCell(row,5, 0);
                hot.setDataAtCell(row,6, '');
	        }else if(prop == 'isuue_qty'){
	            let rate = hot.getDataAtCell(row,4);
	            let isuue_qty = newValue;
	            let value = rate * isuue_qty;
	            hot.setDataAtCell(row,5, parseFloat(value).toFixed(2));
	            
	        }else if(prop == 'rate'){
	            let rate = newValue;
	            let isuue_qty = hot.getDataAtCell(row,3);
	            let value = rate * isuue_qty;
	            hot.setDataAtCell(row,5, parseFloat(value).toFixed(2));
	        }else if(prop == 'value'){
	            updateValue();
	        }
	    })
	}
});
    function updateValue(){
        let total_qty = 0;
        let total_value = 0;
        
        for (var row_index = 0; row_index <= 40; row_index++) {
            if(parseFloat(hot.getDataAtCell(row_index, 3)) > 0){
                total_qty += (parseFloat(hot.getDataAtCell(row_index, 3)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 5)) > 0){
                total_value += (parseFloat(hot.getDataAtCell(row_index, 5)));
            }
        }
        
        $('input[name="total_issue_qty"]').val(total_qty.toFixed(2));
        $('input[name="total_issue_amt"]').val(total_value.toFixed(2));
        
    }
    
    $('.save_detail').on('click', function() {
      $('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
    });
    
    function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) 
    {
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
    	          value.push(optionsList[index].label);
    	      }
    	  }
    	  value = value.join(", ");
    
    	  Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
    	  return td;
    }
</script>