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
            pur_order_number: 'required',
            tds_rate_avl: {
				required: {
					depends: function(element) {
						return (jQuery('select[name="TDSCode"]').val() == "") ? false : true
					}
				}
			},
            PurchaseType:'required',
            LedgerGrp:'required',
            Ledger:'required',
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
            vendor: 'required',
            CenterID: 'required',
            WHID:'required',
            PaymentTerm:'required',
            
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
         
          width: 100,
          chosenOptions: {
              data: <?php echo json_encode($item_code); ?>
          }
        },
        { 
          data: 'ItemName',
          type: 'text',
           width: 150,
          readOnly: true
        },
        {
          data: 'name',
          type: 'text',
          
          width: 150,
         
          readOnly: true
     
        },
         {
          data: 'PurchRate',
          type: 'numeric',
          width: 60,
      
        },
        {
          data: 'OrderQty',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 60,
        },
        {
          data: 'Disc',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 50,
          readOnly: true
        },
        {
          data: 'DiscAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 60,
        },
        {
          data: 'gst',
          type: 'text',
           width: 50,
          readOnly: true
        },
         {
          data: 'cgstamt',
          type: 'numeric',
          
           width: 60,
          readOnly: true
        },
            {
          data: 'sgstamt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 60,
          readOnly: true
        },
            {
          data: 'igstamt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 50,
          readOnly: true
        },
        
        {
          data: 'ChallanAmt',
          type: 'numeric',
          numericFormat: {
            pattern: '0,0'
          },
           width: 90,
        }
      
      ],
      licenseKey: 'non-commercial-and-evaluation',
      stretchH: 'all',
      width: '100%',
      columnHeaderHeight: 40,
      minRows: 10,
      maxRows: 40,
      rowHeaders: true,
      colWidths: [200,10,100,50,100,50,100,50,100,100],
      colHeaders: [
        '<?php echo _l('ItemID'); ?>',
        '<?php echo _l('ItemName'); ?>',
        '<?php echo _l('MainItemGroupName'); ?>',
        '<?php echo _l('PurchRate (MT)'); ?>',
        '<?php echo _l('Qty(MT)'); ?>',
        '<?php echo _l('Disc%'); ?>',
        '<?php echo _l('DiscAmt'); ?>',
        '<?php echo _l('GST%'); ?>',
        '<?php echo _l('CGSTAMT'); ?>',
        '<?php echo _l('SGSTAMT'); ?>',
        '<?php echo _l('IGSTAMT'); ?>',
        '<?php echo _l('Amount'); ?>',
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
            vendor_id = $("#vendor").val();
            
      	    if(prop == 'id'){
      	         vendor_id = $("#vendor").val();
      	         if(newValue == null){
      	                hot.setDataAtCell(row,9, '0');
                      	hot.setDataAtCell(row,10, '0');
                      	hot.setDataAtCell(row,11, '0');
                      	hot.setDataAtCell(row,12, '0');
                      	hot.setDataAtCell(row,13, '0');
      	                hot.setDataAtCell(row,1, '');
                      	hot.setDataAtCell(row,2, '');
                      	hot.setDataAtCell(row,3, '0');
                      	hot.setDataAtCell(row,4, '0');
                      	hot.setDataAtCell(row,5, '0');
                      	hot.setDataAtCell(row,6, '0');
                      	hot.setDataAtCell(row,7, '0');
                      	hot.setDataAtCell(row,8, '0');
                      	
      	        }else{
          	        if(vendor_id == ''){
          	            alert("Please Select vendor");return false;
          	        }else{
          	            // Vendor Item Check Feature
                        /*$.post(admin_url + 'purchase/items_vendor_check/'+newValue+'/'+vendor_id).done(function(response){
          	                response = JSON.parse(response);*/
          	                count++;
      	                $.post(admin_url + 'purchase/GetItemDetails/'+newValue).done(function(response){
                            response = JSON.parse(response);
                            hot.setDataAtCell(row,1, response.value.ItemName);
                            hot.setDataAtCell(row,2, response.value.name);
                            hot.setDataAtCell(row,3, '');
                            hot.setDataAtCell(row,4, '');
                            hot.setDataAtCell(row,5, '0.00');
                            hot.setDataAtCell(row,6, '0.00');
                            hot.setDataAtCell(row,7, response.value.taxrate);
                            hot.setDataAtCell(row,8, '');
                            hot.setDataAtCell(row,9, '');
                            hot.setDataAtCell(row,10, '');
                            hot.setDataAtCell(row,11, '');
                            count++; 
      	                });
                        //});
          	        }
      	        }
            }else if(prop == 'PurchRate'){
                let DiscAmt = hot.getDataAtCell(row,6);
                let PurchAmt = newValue*hot.getDataAtCell(row,4);
                let FinalAmt = PurchAmt - DiscAmt;
      	        hot.setDataAtCell(row,11, FinalAmt);
      	        var state = $("#vendor_state").val();
      	        var gstper = hot.getDataAtCell(row,7);
      	        if(state == 'MH'){
                    let GstAmt = (gstper*FinalAmt)/100;
                    let cgst = GstAmt/2
                    hot.setDataAtCell(row,8, cgst);
                    hot.setDataAtCell(row,9, cgst);
                    hot.setDataAtCell(row,10, '0');
      	        }else{
                    let GstAmt = (gstper*FinalAmt)/100;
                    hot.setDataAtCell(row,8, '0');
                    hot.setDataAtCell(row,9, '0');
                    hot.setDataAtCell(row,10, GstAmt);
      	        }
            }else if(prop == 'OrderQty'){
                let DiscAmt = hot.getDataAtCell(row,6);
                let PurchAmt = newValue*hot.getDataAtCell(row,3);
                let FinalAmt = PurchAmt - DiscAmt;
      	        hot.setDataAtCell(row,11, FinalAmt);
                var state = $("#vendor_state").val();
                var gstper = hot.getDataAtCell(row,7);
                if(state == 'MH'){
                    let GstAmt = (gstper*FinalAmt)/100;
                    let cgst = GstAmt/2
                    hot.setDataAtCell(row,8, cgst);
                    hot.setDataAtCell(row,9, cgst);
                    hot.setDataAtCell(row,10, '0');
                }else{
                    let GstAmt = (gstper*FinalAmt)/100;
                    hot.setDataAtCell(row,8, '0');
                    hot.setDataAtCell(row,9, '0');
                    hot.setDataAtCell(row,10, GstAmt);
                }
            }else if(prop == 'DiscAmt'){
                let DiscAmt = newValue;
                let PurchAmt = hot.getDataAtCell(row,4)*hot.getDataAtCell(row,3);
                let FinalAmt = PurchAmt - DiscAmt;
      	        hot.setDataAtCell(row,11, FinalAmt);
                var state = $("#vendor_state").val();
                var gstper = hot.getDataAtCell(row,7);
                if(state == 'MH'){
                    let GstAmt = (gstper*FinalAmt)/100;
                    let cgst = GstAmt/2
                    hot.setDataAtCell(row,8, cgst);
                    hot.setDataAtCell(row,9, cgst);
                    hot.setDataAtCell(row,10, '0');
                }else{
                    let GstAmt = (gstper*FinalAmt)/100;
                    hot.setDataAtCell(row,8, '0');
                    hot.setDataAtCell(row,9, '0');
                    hot.setDataAtCell(row,10, GstAmt);
                }
            }else if(prop == 'ChallanAmt'){
                updateValue();
            }else if(prop == 'cgstamt'){
                updateValue();
            }else if(prop == 'igstamt'){
                updateValue();
            }
	    });
    }
});
    function updateValue(){
        let grand_total = 0;
        let total_cgst = 0;
        let total_sgst = 0;
        let total_igst = 0;
        let totalDisc = 0;
            
        for (var row_index = 0; row_index <= 40; row_index++) {
            if(parseFloat(hot.getDataAtCell(row_index, 11)) > 0){
                grand_total += (parseFloat(hot.getDataAtCell(row_index, 11)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 8)) > 0){
                total_cgst += (parseFloat(hot.getDataAtCell(row_index, 8)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 9)) > 0){
                total_sgst += (parseFloat(hot.getDataAtCell(row_index, 9)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 10)) > 0){
                total_igst += (parseFloat(hot.getDataAtCell(row_index, 10)));
            }
            if(parseFloat(hot.getDataAtCell(row_index, 6)) > 0){
                totalDisc += (parseFloat(hot.getDataAtCell(row_index, 6)));
            }
        }
        $('input[name="total_mn"]').val(grand_total.toFixed(2));
        $('input[name="dc_total"]').val(totalDisc.toFixed(2));
        $('input[name="CGST_amt"]').val(total_cgst.toFixed(2));
        $('input[name="SGST_AMT"]').val(total_sgst.toFixed(2));
        $('input[name="IGST_amt"]').val(total_igst.toFixed(2));
        
        let tds_per = $('#tds_rate').val();
        let TdsAmt = 0;
        if(tds_per != ''){
            TdsAmt = grand_total * (tds_per / 100);
        }
        $('input[name="tdsAmt"]').val(TdsAmt.toFixed(2));
       /* grand_total -= TdsAmt;*/
        
        let Freight_AMT = $('#Freight_AMT').val();
        if(Freight_AMT == ''){
            Freight_AMT = 0;
        }
        grand_total += parseFloat(Freight_AMT);
        let Other_amt = $('#Other_amt').val();
        if(Other_amt == ''){
            Other_amt = 0;
        }
        grand_total += parseFloat(Other_amt);
        
        grand_total += parseFloat(total_cgst);
        grand_total += parseFloat(total_sgst);
        grand_total += parseFloat(total_igst);
        
        let finalAmt = Math.round(grand_total);
        let roundoffAmt = finalAmt - grand_total;
        $('input[name="Round_OFF"]').val(roundoffAmt.toFixed(2));
        $('input[name="Invoice_amt"]').val(finalAmt.toFixed(2));  
    }
 
    $('#Other_amt').on('blur', function() {
        
        let PurchAmt = $('input[name="total_mn"]').val();
        let DiscAmt = $('input[name="dc_total"]').val();
        let CGSTAmt = $('input[name="CGST_amt"]').val();
        let SGSTAmt = $('input[name="SGST_AMT"]').val();
        let IGSTAmt = $('input[name="IGST_amt"]').val();
        let TdsAmt = $('input[name="tdsAmt"]').val();
        let frtAmt = $('#Freight_AMT').val();
        let OthAmt = $(this).val();
        if(OthAmt == "" || OthAmt == null){
            OthAmt = 0;
        }
        if(frtAmt == "" || frtAmt == null){
            frtAmt = 0;
        }
        let finalAmt = parseFloat(PurchAmt) - parseFloat(DiscAmt) + parseFloat(CGSTAmt) + parseFloat(SGSTAmt) + parseFloat(IGSTAmt) + parseFloat(frtAmt) + parseFloat(OthAmt);
        let NewfinalAmt = Math.round(finalAmt);
        let roundoffAmt = NewfinalAmt - finalAmt;
        $('input[name="Round_OFF"]').val(roundoffAmt.toFixed(2));
        $('input[name="Invoice_amt"]').val(NewfinalAmt.toFixed(2));
    });
    
    $('#Freight_AMT').on('blur', function() {
        
        let PurchAmt = $('input[name="total_mn"]').val();
        let DiscAmt = $('input[name="dc_total"]').val();
        let CGSTAmt = $('input[name="CGST_amt"]').val();
        let SGSTAmt = $('input[name="SGST_AMT"]').val();
        let IGSTAmt = $('input[name="IGST_amt"]').val();
        let TdsAmt = $('input[name="tdsAmt"]').val();
        let OthAmt = $('#Other_amt').val();
        let frtAmt = $(this).val();
        if(frtAmt == "" || frtAmt == null){
            frtAmt = 0;
        }
        if(OthAmt == "" || OthAmt == null){
            OthAmt = 0;
        }
        let finalAmt = parseFloat(PurchAmt) - parseFloat(DiscAmt) + parseFloat(CGSTAmt) + parseFloat(SGSTAmt) + parseFloat(IGSTAmt) + parseFloat(frtAmt) + parseFloat(OthAmt);
        let NewfinalAmt = Math.round(finalAmt);
        let roundoffAmt = NewfinalAmt - finalAmt;
        $('input[name="Round_OFF"]').val(roundoffAmt.toFixed(2));
        $('input[name="Invoice_amt"]').val(NewfinalAmt.toFixed(2));
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
	          value.push(optionsList[index].ItemID);
	      }
	  }
	  value = value.join(", ");

	  Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
	  return td;
}

</script>

<script type="text/javascript">
    $('#Freight_AMT').on('keypress',function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
            event.preventDefault();
        }
    });
    
    $('#Other_amt').on('keypress',function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
            event.preventDefault();
        }
    });
</script>