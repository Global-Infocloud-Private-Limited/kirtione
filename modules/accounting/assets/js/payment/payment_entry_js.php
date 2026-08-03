<script type="text/javascript">
	var commodity_type_value, data;
(function($) {
	"use strict";

  acc_init_currency();
	appValidateForm($('#payment-entry-form'), {
		payment_date: {
			remote: {
				url: site_url + "admin/misc/checkpayment_val",
				type: 'post',
				data: {
					payment_date: function() {
						return $('input[name="payment_date"]').val();
					},
					VoucheriD: function() {
						return $('input[name="VoucheriD"]').val();
					}
				}
			}
		},
		payment_number: 'required',
		ganeral_account: 'required',
		/*payment_date1: {
			remote: {
				url: site_url + "admin/misc/checkpayment_val",
				type: 'post',
				data: {
					payment_date: function() {
						return $('input[name="payment_date1"]').val();
					},
					VoucheriD: function() {
						return $('input[name="VoucheriD"]').val();
					}
				}
			}
		},*/
    });

  <?php if(isset($payment_entry)){ ?>
    data = <?php echo json_encode($payment_entry->details); ?>
  <?php }else{ ?>
    data = [];
  <?php } ?>

	var hotElement1 = document.querySelector('#receipt_entry_container');
    let searchTimer = null;
    let currentRequest = null;
    let accountList = {};
    let accountCache = {};
    var isEditMode = <?php echo isset($payment_entry) ? 'true' : 'false'; ?>;
    var commodity_type = new Handsontable(hotElement1, {
      contextMenu: true,
      manualRowMove: true,
      autoWrapRow: true,
      rowHeights: 10,
      stretchH: 'all',
      defaultRowHeight: 10,
      minRows: 20,
      licenseKey: 'non-commercial-and-evaluation',
      rowHeaders: true,
      autoColumnSize: {
        samplingRatio: 10
      },
      filters: true,
      manualRowResize: true,
      manualColumnResize: true,
      columnHeaderHeight: 10,
      colWidths: [50, 200, 50, 50, 30, 40, 50, 40,130],
      rowHeights: 10,
      rowHeaderWidth: [20],
      data: data,
      cells: function (row, col) {
          var cellProperties = {};
          // Replace 0 with your Company column index
          if (isEditMode && col === 1) {
              var company = this.instance.getDataAtCell(row, 1);
              // Only lock rows that already have a company
              if (company && company.toString().trim() !== '') {
                  cellProperties.readOnly = true;
              }
          }
          return cellProperties;
      },
      columns: [
            {
                type: 'text',
                data: 'AccountID',
              },
	          /*{
		        data: 'company',
		        renderer: customDropdownRenderer,
		        editor: "chosen",
		        chosenOptions: {
		            data: <?php echo json_encode($account_to_select); ?>
		        }
		      },*/
		      {
                data: 'company',
                type: 'autocomplete',
                strict: false,
                filter: false,

                source: function(query, process) {

                    var hot = this.instance;
                    var row = this.row;
                    var col = this.col;

                    // Current cell value
                    var currentValue = hot.getDataAtCell(row, col);

                    if (!query || query.length < 2) {

                        if (currentValue) {
                            process([currentValue]);   // Keep existing value
                        } else {
                            process([]);
                        }

                        return;
                    }

                    // Make cache case-insensitive
                    let cacheKey = query.toLowerCase();

                    if (accountCache[cacheKey]) {
                        window.accountList = accountCache[cacheKey];
                        process(accountCache[cacheKey].map(item =>
                            `${item.AccountID} - ${item.company}`
                        ));
                        return;
                    }

                    clearTimeout(searchTimer);

                    searchTimer = setTimeout(function () {

                        // Abort previous request
                        if (currentRequest) {
                            currentRequest.abort();
                        }

                        currentRequest = $.ajax({
                            url: admin_url + "accounting/searchAccounts",
                            type: "POST",
                            data: {
                                search: query
                            },
                            success: function (res) {

                                let data = JSON.parse(res);

                                accountCache[cacheKey] = data;
                                window.accountList = data;

                                process(data.map(item =>
                                    `${item.AccountID} - ${item.company}`
                                ));
                            },
                            error: function (xhr, status) {
                                if (status !== "abort") {
                                    process([]);
                                }
                            }
                        });

                    }, 250);
                }
            },
		      {
		        data: 'center',
		        renderer: customDropdownRenderer,
		        editor: "chosen",
		        chosenOptions: {
		            data: <?php echo json_encode($all_centers); ?>
		        }
		      },
		      {
		        data: 'commodity',
		        renderer: customDropdownRenderer,
		        editor: "chosen",
		        chosenOptions: {
		            data: <?php echo json_encode($all_commodities); ?>
		        }
		      },
		      {
		        data: 'entryfor',
		        renderer: customDropdownRenderer,
		        editor: "chosen",
		        chosenOptions: {
		            data: <?php echo json_encode($entryfor); ?>
		        }
		      },
		      {
		        data: 'party',
		        renderer: customDropdownRenderer,
		        editor: "chosen",
		        chosenOptions: {
		            data: <?php echo json_encode($all_parties); ?>
		        }
		      },
              {
                type: 'numeric',
                data: 'debit',
                numericFormat: {
			        pattern: '0.00',
			    },
              },
              {
                type: 'text',
                data: 'cheque_no',
              },
              {
                type: 'text',
                data: 'description',
              },
        ],
      colHeaders: [
        '<?php echo "AccountID"; ?>',
	    '<?php echo "Account Name"; ?>',
	    '<?php echo "Center"; ?>',
	    '<?php echo "Commodity"; ?>',
	    '<?php echo "EntryFor"; ?>',
	    '<?php echo "Party ID"; ?>',
	    '<?php echo "Amount"; ?>',
	    '<?php echo "Cheque/utr no."; ?>',
	    '<?php echo "Narration"; ?>'
	  ],
      afterChange: (changes) => {
        if(changes != null){
            changes.forEach(([row, prop, oldValue, newValue]) => {
                /*if(prop == 'AccountID'){
                    var AccountName = commodity_type.getDataAtCell(row,1);
                    if(AccountName == ""){
                        if(newValue == null && newValue == ""){
              	            commodity_type.setDataAtCell(row,1, '0');
                            commodity_type.setDataAtCell(row,2, '0');
                            commodity_type.setDataAtCell(row,3, '0');
                            commodity_type.setDataAtCell(row,4, '0');
                            commodity_type.setDataAtCell(row,5, '0');
                            commodity_type.setDataAtCell(row,6, '0');
                            commodity_type.setDataAtCell(row,7, '0');
              	        }else{
                  	        $.post(admin_url + 'accounting/AccountChange/'+newValue).done(function(response){
                  	            response = JSON.parse(response);
                  	            if(response.value == null){
                  	                alert('AccountID Not available...');
                  	            }else{
                              	    commodity_type.setDataAtCell(row,1, response.value.AccountID);
                                  	commodity_type.setDataAtCell(row,2, '');
                                  	commodity_type.setDataAtCell(row,3, '');
                                  	commodity_type.setDataAtCell(row,4, '');
                                  	commodity_type.setDataAtCell(row,5, '');
                                  	commodity_type.setDataAtCell(row,6, '');
                                  	commodity_type.setDataAtCell(row,7, '');
                  	            }
                  	        });
          	            }
                    }
      	        }*/
      	        /*if(prop == 'company'){
                    if(newValue !== '' || newValue !== null){
                        commodity_type.setDataAtCell(row,0,newValue);
                        commodity_type.setDataAtCell(row,2,''); 
                        commodity_type.setDataAtCell(row,3,''); 
                        commodity_type.setDataAtCell(row,4,''); 
                        commodity_type.setDataAtCell(row,5,''); 
                        commodity_type.setDataAtCell(row,6,'');
                        commodity_type.setDataAtCell(row,7,'');
                    }
                }*/
                if (prop == 'company' && newValue) {
      	            let AccountID = newValue.split(' - ')[0];
                    
                    if (AccountID) {
                        // Set AccountID
                        commodity_type.setDataAtCell(row, 0, AccountID);
                        // Optional: reset other fields
                        commodity_type.setDataAtCell(row, 2, '');
                        commodity_type.setDataAtCell(row, 3, '');
                        commodity_type.setDataAtCell(row, 4, '');
                        commodity_type.setDataAtCell(row, 5, '');
                        commodity_type.setDataAtCell(row, 6, '');
                        commodity_type.setDataAtCell(row, 7, '');
                        commodity_type.setDataAtCell(row, 8, '');
                    }
                }
            })
          var payment_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
          var total_debit = 0, total_credit = 0;

          $.each(payment_entry, function(index, value) {
            if(value[6] != '' && value[6] != null){
              total_debit += parseFloat(value[6]);
            }
          });
          
          $('.total_debit').html(format_money(total_debit));
        }
      }
    });
    commodity_type_value = commodity_type;
    commodity_type.addHook('afterSelection', function (row, col) {

    const colProp = commodity_type.colToProp(col);

    if (colProp === 'company') {

        setTimeout(() => {
            const editor = commodity_type.getActiveEditor();

            if (editor && !editor.isOpened()) {
                editor.beginEditing('');
            }
        }, 50);
    }
});


function getCachedResults(query) {
    for (let key in accountCache) {
        if (query.startsWith(key)) {
            return accountCache[key];
        }
    }
    return null;
}
    $('.payment-entry-form-submiter').on('click', function() {
	    $('input[name="payment_entry"]').val(JSON.stringify(commodity_type_value.getData()));
    	var payment_entry = JSON.parse($('input[name="payment_entry"]').val());
        var total_debit = 0, total_credit = 0;
        //let cheque_arr = [];
	    $.each(payment_entry, function(index, value) {
            if(value[6] != '' && value[6] != null){
                total_debit += parseFloat(value[6].toFixed(2));
                //cheque_arr.push(value[6]);
            }
        });
        if(total_debit > 0){
	    	$('input[name="amount"]').val(total_debit);
	    	$('#payment-entry-form').submit();
	    }else{
	    	alert('<?php echo _l('you_must_fill_out_at_least_two_detail_lines'); ?>');
	    }
	});
})(jQuery);

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

function calculate_amount_total(){
  "use strict";
  var payment_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
  var total_debit = 0, total_credit = 0;
  $.each(payment_entry, function(index, value) {
    if(value[6] != ''){
      total_debit += parseFloat(value[6]);
    }
  });

  $('.total_debit').html(format_money(total_debit));
  $('.total_credit').html(format_money(total_credit));
}

// Set the currency for accounting
function acc_init_currency() {
  "use strict";
  
  var selectedCurrencyId = <?php echo html_entity_decode($currency->id); ?>;

  requestGetJSON('misc/get_currency/' + selectedCurrencyId)
      .done(function(currency) {
          // Used for formatting money
          accounting.settings.currency.decimal = currency.decimal_separator;
          accounting.settings.currency.thousand = currency.thousand_separator;
          accounting.settings.currency.symbol = currency.symbol;
          accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
      });
}

</script>