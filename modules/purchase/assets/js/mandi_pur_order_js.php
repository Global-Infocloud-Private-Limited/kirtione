<script>

    function removeCommas(str) {
      "use strict";
      return(str.replace(/,/g,''));
    }

    $('.edit-new-order').on('click', function(){
        $('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
        $('#transfer-modal').modal('show');
    });
    
    $(function(){
        "use strict";
    	validate_purorder_form();
        function validate_purorder_form(selector) {
            selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;
            appValidateForm($(selector), {
                pro_orderid: 'required',
                prd_date: 'required',
                ItemID: 'required',
                CenterID: 'required',
                WHID:'required',
            });
        }
    });
    
    function numberWithCommas(x) {
      "use strict";
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
<?php if(!isset($pur_Details)){
 ?>	
    var dataObject = [];
    var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
    
    var hotSettings = {
      data: dataObject,
      columns: [
        { 
          data: 'DocNo',
          type: 'text',
          width:50,
        },
        {
          data: 'id',
          renderer: customDropdownRenderer,
          editor: "chosen",
          width: 180,
          chosenOptions: {
              data: <?php echo json_encode($vendors); ?>
          }
        },
        {
          data: 'Payment_Term',
          renderer: customDropdownRenderer,
          editor: "chosen",
          width: 100,
          chosenOptions: {
              data: <?php echo json_encode($PaymentCycleList); ?>
          }
        },
        { 
          data: 'Bag',
          type: 'numeric',
        },
        {
          data: 'weight_per_bag',
          type: 'numeric',
          width: 70,
        },
        {
          data: 'loose_in_keg',
          type: 'numeric',
          width:70,
        },
        
        {
          data: 'OrderQty_in_quintal',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
          readOnly: true,
        },
        {
          data: 'rate_per_quintal',
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
            pattern: '0,00'
          },
          readOnly: true,
          width:90,
        },
        {
          data: 'brokerage',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'market_levy',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'roundoff',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:60,
        },
        {
          data: 'Gross',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:90,
        },
        {
          data: 'tdsAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          readOnly: true,
          width:70,
        },
        {
          data: 'NetAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:120
        }
      
      ],
      licenseKey: 'non-commercial-and-evaluation',
      stretchH: 'all',
      width: '100%',
      columnHeaderHeight: 40,
      minRows: 10,
      maxRows: 40,
      rowHeaders: true,
      colWidths: [40,180,100,60,60,60,60,110,80,80,60,110,60,130],
      colHeaders: [
        '<?php echo 'Doc No.'; ?>',
        '<?php echo "Vendor Code & Name"; ?>',
        '<?php echo "Payment Term"; ?>',
        '<?php echo "Bag"; ?>',
        '<?php echo "Weight / Bag"; ?>',
        '<?php echo "Loose (kg)"; ?>',
        '<?php echo "Qty (Quintal)"; ?>',
        '<?php echo "Rate / Quintal"; ?>',
        '<?php echo "Value"; ?>',
        '<?php echo "Brokerage"; ?>',
        '<?php echo "Market Levy"; ?>',
        '<?php echo "Round Off"; ?>',
        '<?php echo "Gross"; ?>',
        '<?php echo "TDS Amt"; ?>',
        '<?php echo "Net Amt"; ?>',
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
	        //alert(newValue);
	        var count = 1; 
	        let TdsAmt = 0;
	        let tds_per = $('#tds_rate').val();
	        //if(newValue != ''){
	            if(prop == 'id'){
	                hot.setDataAtCell(row,2, '');
	                hot.setDataAtCell(row,3, 0);
	                hot.setDataAtCell(row,4, 0);
	                hot.setDataAtCell(row,5, 0);
	                hot.setDataAtCell(row,6, 0);
	                hot.setDataAtCell(row,7, 0);
	                hot.setDataAtCell(row,8, 0);
	                hot.setDataAtCell(row,9, 0);
	                hot.setDataAtCell(row,10, 0);
	                hot.setDataAtCell(row,11, 0);
	                hot.setDataAtCell(row,12, 0);
	                hot.setDataAtCell(row,13, 0);
	                hot.setDataAtCell(row,14, 0);
    	        }else if(prop == 'Bag'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = newValue;
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'weight_per_bag'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = newValue;
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'loose_in_keg'){
    	            let loos_in_qtl = newValue / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'rate_per_quintal'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = newValue;
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'brokerage'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = newValue;
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'market_levy'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = newValue;
    	            value += brokerageAmt;
    	            value += marketlevyAmt;
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                let pp = tds_per / 100;
    	                 TdsAmt = value * pp;
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'OrderQty_in_quintal'){
    	            updateValue();
    	        }else if(prop == 'value'){
    	            updateValue();
    	        }else if(prop == 'Gross'){
    	            updateValue();
    	        }else if(prop == 'NetAmt'){
    	            updateValue();
    	        }
	        //}
	    })
	}
});
    
<?php } else{ ?>
    
	var dataObject = <?php echo html_entity_decode($pur_Details); ?>;
    var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
    var hotSettings = {
      data: dataObject,
      columns: [
        { 
          data: 'TransID',
          type: 'text',
          width:50,
        },
        {
          data: 'AccountID',
          renderer: customDropdownRenderer,
          editor: "chosen",
          width: 180,
          chosenOptions: {
              data: <?php echo json_encode($vendors); ?>
          }
        },
        {
          data: 'Payment_term',
          renderer: customDropdownRenderer,
          editor: "chosen",
          width: 100,
          chosenOptions: {
              data: <?php echo json_encode($PaymentCycleList); ?>
          }
        },
        { 
          data: 'bag',
          type: 'numeric',
        },
        {
          data: 'wt_per_bag',
          type: 'numeric',
          width: 70,
        },
        {
          data: 'loose_in_kg',
          type: 'numeric',
          width:70,
        },
        
        {
          data: 'OrderQty',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
          readOnly: true,
        },
        {
          data: 'PurchRate',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'OrderAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          readOnly: true,
          width:90,
        },
        {
          data: 'BrokerAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'MrtLevyAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          width:70,
        },
        {
          data: 'Round_off',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:60,
        },
        {
          data: 'GrossAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:90,
        },
        {
          data: 'TDSAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,00'
          },
          readOnly: true,
          width:70,
        },
        {
          data: 'NetOrderAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
          readOnly: true,
          width:120
        }
      
      ],
      licenseKey: 'non-commercial-and-evaluation',
      stretchH: 'all',
      width: '100%',
      columnHeaderHeight: 40,
      minRows: 10,
      maxRows: 40,
      rowHeaders: true,
      colWidths: [40,180,100,60,60,60,60,110,80,80,60,110,60,130],
      colHeaders: [
        '<?php echo 'Doc No.'; ?>',
        '<?php echo "Vendor Code & Name"; ?>',
        '<?php echo "Payment Term"; ?>',
        '<?php echo "Bag"; ?>',
        '<?php echo "Weight / Bag"; ?>',
        '<?php echo "Loose (kg)"; ?>',
        '<?php echo "Qty (Quintal)"; ?>',
        '<?php echo "Rate / Quintal"; ?>',
        '<?php echo "Value"; ?>',
        '<?php echo "Brokerage"; ?>',
        '<?php echo "Market Levy"; ?>',
        '<?php echo "Round Off"; ?>',
        '<?php echo "Gross"; ?>',
        '<?php echo "TDS Amt"; ?>',
        '<?php echo "Net Amt"; ?>',
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
	        //alert(newValue);
	        var count = 1; 
	        let TdsAmt = 0;
	        let tds_per = $('#tds_rate').val();
	        //alert(tds_per);
	        //if(newValue != ''){
	            if(prop == 'TransID'){
	                hot.setDataAtCell(row,2, '');
	                hot.setDataAtCell(row,3, 0);
	                hot.setDataAtCell(row,4, 0);
	                hot.setDataAtCell(row,5, 0);
	                hot.setDataAtCell(row,6, 0);
	                hot.setDataAtCell(row,7, 0);
	                hot.setDataAtCell(row,8, 0);
	                hot.setDataAtCell(row,9, 0);
	                hot.setDataAtCell(row,10, 0);
	                hot.setDataAtCell(row,11, 0);
	                hot.setDataAtCell(row,12, 0);
	                hot.setDataAtCell(row,13, 0);
	                hot.setDataAtCell(row,14, 0);
    	        }else if(prop == 'bag'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = newValue;
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'wt_per_bag'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = newValue;
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'loose_in_kg'){
    	            let loos_in_qtl = newValue / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'PurchRate'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = newValue;
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                 TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'BrokerAmt'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = newValue;
    	            let marketlevyAmt = hot.getDataAtCell(row,10);
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                TdsAmt = value * (tds_per / 100);
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'MrtLevyAmt'){
    	            let loos_in_qtl = hot.getDataAtCell(row,5) / 100;
    	            let weight_per_bag = hot.getDataAtCell(row,4);
    	            let bag_qty = hot.getDataAtCell(row,3);
    	            let qty = (weight_per_bag * bag_qty) / 100;
    	            let total_qty = loos_in_qtl + qty;
    	            hot.setDataAtCell(row,6, parseFloat(total_qty).toFixed(2));
    	            let rate_in_qtl = hot.getDataAtCell(row,7);
    	            let value = rate_in_qtl * total_qty;
    	            hot.setDataAtCell(row,8, parseFloat(value).toFixed(2));
    	            let brokerageAmt = hot.getDataAtCell(row,9);
    	            let marketlevyAmt = newValue;
    	            value += parseFloat(brokerageAmt);
    	            value += parseFloat(marketlevyAmt);
    	            let roundoff = Math.round(value);
    	            let roundoffAmt = roundoff - value;
    	            hot.setDataAtCell(row,11, parseFloat(roundoffAmt).toFixed(2));
    	            hot.setDataAtCell(row,12, parseFloat(roundoff).toFixed(2));
    	            if(tds_per != ''){
    	                let pp = tds_per / 100;
    	                 TdsAmt = value * pp;
    	            }
    	            hot.setDataAtCell(row,13, parseFloat(Math.round(TdsAmt)).toFixed(2));
    	            roundoff -= Math.round(TdsAmt);
    	            hot.setDataAtCell(row,14, parseFloat(roundoff).toFixed(2));
    	        }else if(prop == 'OrderQty'){
    	            updateValue();
    	        }else if(prop == 'OrderAmt'){
    	            updateValue();
    	        }else if(prop == 'GrossAmt'){
    	            updateValue();
    	        }else if(prop == 'NetOrderAmt'){
    	            updateValue();
    	        }
	        //}
	    })
	}
});


<?php } ?>

    function updateValue(){
        var Qty_quintal = 0;
        var total_value = 0;
        var total_brokerage = 0;
        var total_market_levy = 0;
        var total_gross_value = 0;
        var total_tds_value = 0;
        var total_net_value = 0;
            
        for (var row_index = 0; row_index <= 40; row_index++) {
            if(parseFloat(hot.getDataAtCell(row_index, 6)) > 0){
                Qty_quintal += (parseFloat(hot.getDataAtCell(row_index, 6)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 8)) > 0){
                total_value += (parseFloat(hot.getDataAtCell(row_index, 8)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 9)) > 0){
                total_brokerage += (parseFloat(hot.getDataAtCell(row_index, 9)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 10)) > 0){
                total_market_levy += (parseFloat(hot.getDataAtCell(row_index, 10)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 12)) > 0){
                total_gross_value += (parseFloat(hot.getDataAtCell(row_index, 12)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 13)) > 0){
                total_tds_value += (parseFloat(hot.getDataAtCell(row_index, 13)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 14)) > 0){
                total_net_value += (parseFloat(hot.getDataAtCell(row_index, 14)));
            }
        }
        
        $('input[name="total_qty_in_qtl"]').val(Qty_quintal.toFixed(2));
        $('input[name="Total_value"]').val(total_value.toFixed(2));
        $('input[name="total_brokerage"]').val(total_brokerage.toFixed(2));
        $('input[name="total_mrkt_levy"]').val(total_market_levy.toFixed(2));
        $('input[name="total_gross_value"]').val(total_gross_value.toFixed(2));
        $('input[name="total_tds"]').val(total_tds_value.toFixed(2));
        $('input[name="total_net_value"]').val(total_net_value.toFixed(2));
    }

    $('.save_detail').on('click', function() {
      $('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
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
	          value.push(optionsList[index].label);
	      }
	  }
	  value = value.join(", ");

	  Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
	  return td;
}

</script>

