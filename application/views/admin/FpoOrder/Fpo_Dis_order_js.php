<script>
    function loadHandsontable(dynamicCols = [], dynamicHeaders = [])
    {
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
                    FPO_Date: 'required',
                    ItemID: 'required',
                    CenterID: 'required',
                    fpolist:'required',
                    rate:'required',
                    vehicle_no:'required',
                });
            }
        });
    
        function numberWithCommas(x) {
          "use strict";
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        //const dataObject = [];
        <?php if (!isset($pur_Details) || empty($pur_Details)) { ?>
            const dataObject = [];
        <?php } else { ?>
            const dataObject = <?php echo $pur_Details; ?>;
        <?php } ?>
            const QcDetails = <?php echo $QcDetails; ?>;
        
        const container = document.getElementById('example');
        if (window.hot) window.hot.destroy();
    
        const staticColsBefore = [
            {
              data: 'id',
              renderer: customDropdownRenderer,
              editor: "chosen",
              width: 150,
              chosenOptions: {
                data: <?php echo json_encode($FarmerList); ?>
              }
            },
            { data: 'NetWeight', type: 'numeric', width: 70 },
            { data: 'PendingQty', type: 'numeric', numericFormat: { pattern: '0,0' }, readOnly: true, width: 60 },
            { data: 'DispatchQty', type: 'numeric', numericFormat: { pattern: '0,0' }, width: 60 },
            { data: 'DispatchBag', type: 'numeric', width: 60 },
            { data: 'UOM', type: 'text', width: 60, readOnly: true },
            { data: 'Bag', type: 'numeric', width: 60 },
            {
              data: 'Rate',
              type: 'numeric',
              numericFormat: { pattern: '0,00' },
              width: 60
            }
          ];

          const staticHeadersBefore = [
            'Farmer',
            'Net Wt(Qtl)',
            'Pending Qty',
            'Dispatch Qty',
            'Dispatch Bag',
            'UOM',
            'Bag',
            'Rate/Qtl'
          ];

          const staticColsAfter = [
            { data: 'Deduction', type: 'numeric', numericFormat: { pattern: '0,0' }, readOnly: true, width: 80 },
            { data: 'NetRate', type: 'numeric', numericFormat: { pattern: '0,0' }, readOnly: true, width: 70 },
            { data: 'tenweight', type: 'numeric', numericFormat: { pattern: '0,00' }, readOnly: true, width: 70 },
            { data: 'NetAmt', type: 'numeric', numericFormat: { pattern: '0,0' }, readOnly: true, width: 70 }
          ];

          const staticHeadersAfter = [
            'Total Deduction',
            'Net Rate/Qtl',
            'Tentative Wt(In Qtl)',
            'Net Amt'
          ];
          
          const updatedDynamicCols = dynamicCols.map(col => {
            const prop = col.data;
            if (prop.endsWith('_Amt') || prop.endsWith(' Amt')) {
                return {
                    ...col,
                    readOnly: true,
                    className: 'htDimmed' 
                };
            }
        
            return col;
        });
          
          const dynamicPropKeys = updatedDynamicCols.map(col => col.data);
          window.dynamicPropKeys = dynamicPropKeys; 
          

          const allCols = [...staticColsBefore, ...updatedDynamicCols, ...staticColsAfter];
          const allHeaders = [...staticHeadersBefore, ...dynamicHeaders, ...staticHeadersAfter];

            dataObject.forEach(row => {
              if (!row.UOM || row.UOM === '') {
                row.UOM = 'Qtl';
              }
            });

          window.hot = new Handsontable(container, {
            data: dataObject,
            columns: allCols,
            colHeaders: allHeaders,
            rowHeaders: true,
            licenseKey: 'non-commercial-and-evaluation',
            stretchH: 'all',
            width: '100%',
            columnHeaderHeight: 40,
            minRows: 10,
            maxRows: 40,
            columnSorting: { indicator: true },
            autoColumnSize: { samplingRatio: 23 },
            mergeCells: true,
            contextMenu: true,
            manualRowMove: true,
            manualColumnMove: true,
            multiColumnSorting: { indicator: true },
            filters: true,
            manualRowResize: true,
            manualColumnResize: true
          });
          
        if (QcDetails && Array.isArray(QcDetails) && QcDetails.length > 0) {
            const rowCount = hot.countRows();
        
            for (let rowIndex = 0; rowIndex < rowCount; rowIndex++) {
                const rowId = hot.getDataAtRowProp(rowIndex, 'id'); 
        
                if (!rowId) continue; 
                const filteredQc = QcDetails.filter(qc => qc.AccountID === rowId);
        
                filteredQc.forEach(qc => {
                    const param = qc.ItemParameterName;
                    const val = parseFloat(qc.Qc_Value) || 0;
                    const amt = parseFloat(qc.Qc_Amt) || 0;
                    
                    if (window.dynamicPropKeys.includes(param)) {
                        hot.setDataAtRowProp(rowIndex, param, val);
                    }
                    
                    const amtKey1 = param + '_Amt';
                    const amtKey2 = param + ' Amt';
        
                    if (window.dynamicPropKeys.includes(amtKey1)) {
                        hot.setDataAtRowProp(rowIndex, amtKey1, amt);
                    } else if (window.dynamicPropKeys.includes(amtKey2)) {
                        hot.setDataAtRowProp(rowIndex, amtKey2, amt);
                    }
                });
            }
        }
    
        let isUpdating = false;
        let hasShownAlert = false;
        //var hot = new Handsontable(hotElement, hotSettings);
        hot.addHook('afterChange', function(changes, src) {
             if (isUpdating || changes === null) return;
        	if(changes !== null){
        	    changes.forEach(([row, prop, oldValue, newValue]) => {
        	        if(prop == 'id'){
        	            hot.setDataAtCell(row,5, 'Qtl');
        	        }
        	        
        	        if(prop == 'DispatchQty'){
        	          const dispatchqtyold = parseFloat(oldValue) || 0;
        	          const dispatchqty = parseFloat(newValue) || 0;
                      const pendingqty = parseFloat(hot.getDataAtRowProp(row, 'PendingQty')) || 0;
        	           
                       if(dispatchqty > pendingqty)
                       {
                           alert('Dispatch quantity cannot be greater than Pending quantity.');
                           hot.setDataAtRowProp(row, 'DispatchQty', dispatchqtyold);
                       }
        	        }
        	        
                    if (
                        (window.dynamicPropKeys || []).includes(prop) &&
                        !prop.endsWith('_Amt') &&
                        !prop.endsWith(' Amt') &&
                        !['id', 'NetWeight', 'Bag', 'Rate'].includes(prop)
                    ) {
                        const farmer = hot.getDataAtRowProp(row, 'id');
                        const weight = hot.getDataAtRowProp(row, 'NetWeight');
                        const bag = hot.getDataAtRowProp(row, 'Bag');
                        const rate = hot.getDataAtRowProp(row, 'Rate');
            
                        let missingField = '';
                        let missingProp = '';
            
                        if (!farmer) {
                            missingField = 'Farmer';
                            missingProp = 'id';
                        } else if (!weight) {
                            missingField = 'Net Weight';
                            missingProp = 'NetWeight';
                        } else if (!bag) {
                            missingField = 'Bag';
                            missingProp = 'Bag';
                        } else if (!rate) {
                            missingField = 'Rate';
                            missingProp = 'Rate';
                        }
            
                        if (missingField) {
                            isUpdating = true;
            
                            setTimeout(() => {
                                hot.setDataAtRowProp(row, prop, '');
            
                                const colIndex = hot.propToCol(missingProp);
                                hot.selectCell(row, colIndex);
            
                                setTimeout(() => {
                                    const editor = hot.getActiveEditor();
                                    if (editor) {
                                        editor.beginEditing();
                                        
                                        if (missingProp === 'id') {
                                            const $dropdown = $('.handsontableInputHolder select');
                                            if ($dropdown.length) {
                                                $dropdown.focus().click();
                                            }
                                        }
                                    }
            
                                    isUpdating = false;
                                }, 50);
                            }, 10);
            
                            if (!hasShownAlert) {
                                alert(`Please enter ${missingField} before entering QC values.`);
                                hasShownAlert = true;
                            }
            
                            return;
                        }
                    }
                    
                    if (window.dynamicPropKeys.includes(prop) && !prop.endsWith('_Amt') && !prop.endsWith(' Amt')) {
                        const colIndex = hot.propToCol(prop);
                        const headerName = hot.getColHeader(colIndex);
                        var ItemID = $('#ItemID').val();
                        
                        $.ajax({
                            url: "<?php echo admin_url(); ?>FpoOrder/GetDeductionMatrix",
                            method: 'POST',
                            dataType: 'JSON',
                            data: { parameterName: headerName,ItemID:ItemID,newValue:newValue },  
                            success: function(deductionData) {
                                
                                const rate = parseFloat(hot.getDataAtRowProp(row, 'Rate')) || 0;
                                const weight = parseFloat(hot.getDataAtRowProp(row, 'NetWeight')) || 0;
                                const inputValue = parseFloat(newValue) || 0;  
                                
                                var deductionPercent = deductionData['Deduction'];
                                const itemParamID = deductionData['ItemParameterID']; 
                                
                                let deductionAmount = 0;
                                if (itemParamID == 2) {
                                    deductionAmount = weight * deductionPercent;
                                } else {
                                    deductionAmount = rate * weight * (deductionPercent / 100);
                                }
                                
                                const amtField1 = prop + '_Amt';
                                const amtField2 = prop + ' Amt';
                    
                                isUpdating = true;  
                    
                                if (window.dynamicPropKeys.includes(amtField1)) {
                                    hot.setDataAtRowProp(row, amtField1, deductionAmount);
                                } else if (window.dynamicPropKeys.includes(amtField2)) {
                                    hot.setDataAtRowProp(row, amtField2, deductionAmount);
                                }
                                
                                setTimeout(() => {
                                    let totalDeduction = 0;
                    
                                    window.dynamicPropKeys.forEach(key => {
                                        if (key.endsWith('_Amt') || key.endsWith(' Amt')) {
                                            const val = parseFloat(hot.getDataAtRowProp(row, key));
                                            if (!isNaN(val)) {
                                                totalDeduction += val;
                                            }
                                        }
                                    });
                                    hot.setDataAtRowProp(row, 'Deduction', totalDeduction);
                                    
                                    const totalAmt = rate * weight;
                                    let netRate = 0;
                                    if (weight > 0) {
                                        netRate = (totalAmt - totalDeduction) / weight;
                                    }
                                    hot.setDataAtRowProp(row, 'NetRate', netRate);
                                    
                                    const bag = parseFloat(hot.getDataAtRowProp(row, 'Bag')) || 0;
                                    let tentativeWeight = weight + ((bag * 700) / 100000);
                                    hot.setDataAtRowProp(row, 'tenweight', tentativeWeight);
                                    
                                    const netAmt = netRate * weight;
                                    hot.setDataAtRowProp(row, 'NetAmt', netAmt);
                                    
                                    isUpdating = false;
                                }, 50); 
                            },
                            error: function() {
                                alert('Failed to fetch deduction matrix for ' + headerName);
                            }
                        });
                    }
                    
                    if (['Rate', 'DispatchQty','DispatchBag'].includes(prop)) {
                        const rate = parseFloat(hot.getDataAtRowProp(row, 'Rate')) || 0;
                        const weight = parseFloat(hot.getDataAtRowProp(row, 'NetWeight')) || 0;
                        const ItemID = $('#ItemID').val();
                        const dispatchqtywt = parseFloat(hot.getDataAtRowProp(row, 'DispatchQty')) || 0;
                    
                        window.dynamicPropKeys.forEach((qcProp) => {
                            if (qcProp.endsWith('_Amt') || qcProp.endsWith(' Amt')) return;
                    
                            const value = parseFloat(hot.getDataAtRowProp(row, qcProp)) || 0;
                            const colIndex = hot.propToCol(qcProp);
                            const headerName = hot.getColHeader(colIndex);
                    
                            $.ajax({
                                url: "<?php echo admin_url(); ?>FpoOrder/GetDeductionMatrix",
                                method: 'POST',
                                dataType: 'JSON',
                                data: {
                                    parameterName: headerName,
                                    ItemID: ItemID,
                                    newValue: value
                                },
                                success: function(deductionData) {
                                    const deductionPercent = deductionData['Deduction'];
                                    const itemParamID = deductionData['ItemParameterID'];
                    
                                    let deductionAmount = 0;
                                    if (itemParamID == 2) {
                                        deductionAmount = weight * deductionPercent;
                                    } else {
                                        deductionAmount = rate * weight * (deductionPercent / 100);
                                    }
                    
                                    const amtField1 = qcProp + '_Amt';
                                    const amtField2 = qcProp + ' Amt';
                    
                                    isUpdating = true;
                    
                                    if (window.dynamicPropKeys.includes(amtField1)) {
                                        hot.setDataAtRowProp(row, amtField1, deductionAmount);
                                    } else if (window.dynamicPropKeys.includes(amtField2)) {
                                        hot.setDataAtRowProp(row, amtField2, deductionAmount);
                                    }
                                    
                                    setTimeout(() => {
                                        let totalDeduction = 0;
                        
                                        window.dynamicPropKeys.forEach(key => {
                                            if (key.endsWith('_Amt') || key.endsWith(' Amt')) {
                                                const val = parseFloat(hot.getDataAtRowProp(row, key));
                                                if (!isNaN(val)) {
                                                    totalDeduction += val;
                                                }
                                            }
                                        });
                                        hot.setDataAtRowProp(row, 'Deduction', totalDeduction);
                                        
                                        const totalAmt = rate * weight;
                                        let netRate = 0;
                                        if (weight > 0) {
                                            netRate = (totalAmt - totalDeduction) / weight;
                                        }
                                        hot.setDataAtRowProp(row, 'NetRate', netRate);
                                        
                                        const bag = parseFloat(hot.getDataAtRowProp(row, 'DispatchBag')) || 0;
                                        let tentativeWeight = dispatchqtywt + ((bag * 700) / 100000);
                                        hot.setDataAtRowProp(row, 'tenweight', tentativeWeight);
                                        
                                        const netAmt = netRate * weight;
                                        hot.setDataAtRowProp(row, 'NetAmt', netAmt);
                                    
                                        isUpdating = false;
                                    }, 50); 
                                    
                                },
                                error: function () {
                                    console.warn('Failed to recalculate deduction for ' + headerName);
                                }
                            });
                        });
                    }

        	    })
        	}
        });
    
    $('.save_detail').on('click', function() {
      $('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
            const hotData = hot.getData();          
            const hotHeaders = hot.getColHeader();  
            const dynamicColumnIndexes = dynamicHeaders.map(dh => hotHeaders.indexOf(dh)).filter(i => i !== -1);
            const accountIdIndex = 0; 
            const groupedDynamicData = {};
    
            hotData.forEach(row => {
                const accountId = row[accountIdIndex];
                if (!accountId || row.every(cell => cell === null || cell === '')) {
                    return;
                }
                let obj = {};
                dynamicColumnIndexes.forEach(idx => {
                    obj[hotHeaders[idx]] = row[idx];
                });
                if (!groupedDynamicData[accountId]) {
                    groupedDynamicData[accountId] = [];
                }
                groupedDynamicData[accountId].push(obj);
            });
        
        $('input[name="dynamic_param_data"]').val(JSON.stringify(groupedDynamicData));
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
}
</script>



