<script>
    document.addEventListener('DOMContentLoaded', function () {
        var products = <?php echo json_encode($products); ?>;
        var productOptions = products.map(function(product) {
            return { 
                value: product.ProductID,  
                name: product.ProductName 
			};
		});
		
        var productNames = productOptions.map(function(product) {
            return product.name;  
		});
		
        var data = [
		["", "", "", "", "", ""]
        ];
		
        var container = document.querySelector('#example');
        
        var hot = new Handsontable(container, 
        {
            data: data,
            rowHeaders: true,
            colHeaders: ["Item Name","Brand","MeasuredIn","PackingQty","PackingWeight","Sale Unit","Qty","Basic Rate","Discount(%)","GST%","CGSTAMT","SGSTAMT","IGSTAMT","Net Amt"],
            columns: [
			{data:0, type: 'dropdown', source: productNames,
				renderer: function (instance, td, row, col, prop, value, cellProperties) {
                    var selectedProduct = productOptions.find(function (product) {
                        return product.name === value;
					});
                    if (selectedProduct) {                       
                        td.innerHTML = selectedProduct.name;
						} else {
                        td.innerHTML = value;
					}
                    return td;
				},               
                allowInvalid: false
			},
			{data: 1, readOnly: true},
			{data: 2, readOnly: true},                
			{data: 3, readOnly: true},
			{data: 4, readOnly: true},   
			{data: 5, 
				type: 'dropdown',
				source: ["Loose"],
				renderer: function (instance, td, row, col, prop, value, cellProperties) {
					td.innerHTML = value; 
					return td;
				},
				allowInvalid: false
			},                     
			{data: 6, type: 'numeric',
				numericFormat: {pattern: '0'},  
				validator: function (value, callback) {
					if (Number.isInteger(value)) {
						callback(true);
                        } else {
						alert('Enter valid Quantity'); 
						callback(false);
					}
				}
			},
			{
				data: 7, 
				type: 'numeric',
				validator: function(value, callback) {
					if (value === null) {
						callback(true);  
						return;
					}
					
					// Check if the value is a valid number (integer or float)
					if (!isNaN(parseFloat(value)) && isFinite(value)) {
						callback(true);  
                        } else {
						alert('Please enter a valid rate');
						callback(false);  
					}
				}
			},      
			{data: 8, type: 'numeric',validator: function(value, callback) {
				if (value === null) {
					callback(true);
					return;
				}
				if (Number.isInteger(value) || !isNaN(parseFloat(value))) {
					callback(true); 
                    } else {
					alert('Please enter a valid number (integer or float).');
					callback(false); 
				}
			}},
			{data: 9, readOnly: true},
			{data: 10, readOnly: true},
			{data: 11, readOnly: true},  
			{data: 12, readOnly: true}, 
			{data: 13, readOnly: true}       
            ],
            contextMenu: true,
            stretchH: 'all', 
            width: '100%', 
            colWidths: [100,80, 60, 60, 70,70,50,50, 60, 50,50,50,50,70],
            minRows: 10, 
            maxRows: 10,
            afterChange: function(changes, source) 
            {                     
                if (source === 'edit') 
                {
                    changes.forEach(function(change) 
                    {                       
                        var row = change[0];
                        var col = change[1];
                        var newValue = change[3]; 
						
                        var accountID = document.getElementById('AccountID').value;
                        var CenterName =  document.getElementById('centername').value;
						
                        if (col === 0 && newValue && !accountID) {
                            alert('Please select an Account ID before selecting a product.');
                            hot.setDataAtCell(row, 0, ''); 
                            return; 
						}
						
                        if (col === 5 && newValue && !accountID) {
                            alert('Please select an Account ID before selecting a sale unit.');
                            hot.setDataAtCell(row, 5, ''); 
                            return; 
						}
						
						var OrderId = getQueryParameter('OrderId');  
                        if (col === 0 && newValue && !CenterName && (OrderId === null || OrderId === "")) {
                            alert('Please select Center before selecting a product.');
                            hot.setDataAtCell(row, 0, ''); 
                            return; 
						}
                        
                        var productSelected = false;
                        var isRedirecting = false; 
						
                        var urlParams = new URLSearchParams(window.location.search); // This parses the query parameters in the URL
                        var orderId = urlParams.get('OrderId');
                        if (orderId) {
                            isRedirecting = true;                            
						} 
						
                        if (col === 0 && newValue && !isRedirecting) {
                            
                            var selectedProduct = productOptions.find(function(product) {
                                return product.name === newValue; 
							});
							
                            if (selectedProduct) 
                            {                             
                                var productID = selectedProduct.value;                               
                                fetchProductDetails(productID,row,CenterName,accountID);    
                                productSelected = true;                                                          
							}
						}                           
						
                        if ((col === 7 || col === 6 ||col === 5) && newValue && !productSelected && !isRedirecting) 
                        {                        
                            var qtyValue = hot.getDataAtCell(row, 6);  
                            var rate = hot.getDataAtCell(row, 7);     
                            var discount = hot.getDataAtCell(row, 8); 
                            hot.setDataAtCell(row, 8, discount); 
							
                            var unit  = hot.getDataAtCell(row, 2);  
                            var packingqty = hot.getDataAtCell(row, 3);  
                            var saleunit = hot.getDataAtCell(row, 5);    
							
                            var gst = hot.getDataAtCell(row, 9); 
                            if (rate && qtyValue) 
                            {                                 
                                if (unit !== saleunit && packingqty) {
                                    var newRate = (rate / packingqty) * qtyValue;
                                    rate= newRate  
                                    amount =rate;                                   
								}
                                else 
                                {
                                    var amount = rate * qtyValue;      
								}                                 
								
                                if (discount) {
                                    amount = amount - (amount * discount / 100);
								}
                                
                                var netAmount = amount + (amount * gst / 100); 
                                netAmount = netAmount.toFixed(2);  
                                
                                var totalGST = amount * gst / 100; 
                                var cgst = totalGST / 2;
                                var sgst = totalGST / 2;
                                var igst = totalGST;                                   
                                
                                var cgstcell = hot.getDataAtCell(row, 10);                                  
                                var igstcell = hot.getDataAtCell(row, 12); 
								
                                if(igstcell == null)
                                {                                                       
                                    cgstamt = cgst;
                                    sgstamt = sgst;
                                    igstamt = 0.00;                                   
								} 
                                else if(cgstcell == null)
                                {
                                    cgstamt = 0.00;
                                    sgstamt = 0.00;
                                    igstamt = igst;                                   
								} 
                                else if(cgstcell > 0)
                                {
                                    cgstamt = cgst;
                                    sgstamt = sgst;
                                    igstamt = 0.00;
								}           
                                else if(igstcell > 0)    
                                {
                                    cgstamt = 0.00;
                                    sgstamt = 0.00;
                                    igstamt = igst;      
								}     
								
                                hot.setDataAtCell(row, 13, parseFloat(netAmount));               
                                hot.setDataAtCell(row, 10, parseFloat(cgstamt));  
                                hot.setDataAtCell(row, 11, parseFloat(sgstamt));  
                                hot.setDataAtCell(row, 12, parseFloat(igstamt));                                                                                      
							}
						}                                                             
                        else if (col === 8 && !productSelected && !isRedirecting) 
                        { 
                            var qtyValue = hot.getDataAtCell(row, 6);  
                            var rate = hot.getDataAtCell(row, 7);      
                            var discount = hot.getDataAtCell(row, 8);  
                            var gst = hot.getDataAtCell(row, 9);       
							
                            var unit  = hot.getDataAtCell(row, 2);  
                            var packingqty = hot.getDataAtCell(row, 3);  
                            var saleunit = hot.getDataAtCell(row, 5);   
							
                            if (rate && qtyValue) 
                            {                                 
                                if (unit !== saleunit && packingqty) {
                                    var newRate = (rate / packingqty) * qtyValue;
                                    rate= newRate  
                                    amount =rate;                                   
								}
                                else 
                                {
                                    var amount = rate * qtyValue;      
								}    
								
                                if (discount) {
                                    amount = amount - (amount * discount / 100);
								}                               
                                var netAmount = amount + (amount * gst / 100);                                 
                                netAmount = netAmount.toFixed(2);  
								
                                var totalGST = amount * gst / 100; 
                                var cgst = totalGST / 2;
                                var sgst = totalGST / 2;  
                                var igst = totalGST;     
                                
                                var cgstcell = hot.getDataAtCell(row, 10);                                  
                                var igstcell = hot.getDataAtCell(row, 12); 
								
                                if(igstcell == null)
                                {                                                       
                                    cgstamt = cgst;
                                    sgstamt = sgst;
                                    igstamt = 0.00;                                   
								} 
                                else if(cgstcell == null)
                                {
                                    cgstamt = 0.00;
                                    sgstamt = 0.00;
                                    igstamt = igst;                                   
								} 
                                else if(cgstcell > 0)
                                {
                                    cgstamt = cgst;
                                    sgstamt = sgst;
                                    igstamt = 0.00;
								}           
                                else if(igstcell > 0)    
                                {
                                    cgstamt = 0.00;
                                    sgstamt = 0.00;
                                    igstamt = igst;      
								}                   
								
                                hot.setDataAtCell(row, 13, parseFloat(netAmount));     
                                hot.setDataAtCell(row, 10, parseFloat(cgstamt));  
                                hot.setDataAtCell(row, 11, parseFloat(sgstamt));  
                                hot.setDataAtCell(row, 12, parseFloat(igstamt));                                                                                                                         
							}
						}                   
                        
					});   
                    
                    calculateTotalQuantity();
                    calaulateSubTotal();
                    calculateTotalDiscount();
                    calculateTotalValue();
                    calculateTotalCgstAmt();
                    calculateTotalSgstAmt();
                    calculateTotalIgstAmt();                   
                    calculateTotalNetAmount();  
				}            
			}           
		});  
		
        document.querySelector('.btn-tr.save_detail').addEventListener('click', function() 
        {
            var PostedDate = $("#posted_date").val();       
            var OrderId = $("#orderid").val();
            var AccountId = $("#AccountID").val();
            var CenterId = $("#centername").val();
			
			var MobileNo = $("#phonenumber").val();
            var PartyName = $("#partyname").val();
            var BillingState = $("#billstateid").val();
            var BillNo = $("#billno").val(); 
			var VillageName = $("#villagename").val();
			// var OrderAmt = $("#total_net_value").val();        
            // var LedgerAmt = $("#ledgeramt").val();
            // var RewardAmt = $("#rewardamt").val();
            var TotalNetPayableAmt = $("#netpayableamt").val();            
            var Referenceno = $("#refrenceno").val();          
			
            var TotalCgstAmt = $("#total_cgst_amt").val();
            var TotalSgstAmt = $("#total_sgst_amt").val();
            var IgstAmt = $("#total_igst_amt").val();
            var TotalValue = $("#Total_value").val();
            var TotalDiscountAmt = $("#total_disc_in_mt").val();
            var OrderType = $("#ordtype").val();
            var PaymentMode = $("#paymentmode").val();
            var PaymentMethod = $("#paymentmethod").val();
            var ReferenceNo = $("#referenceno").val();
            var Effecton = $("#Effecton").val();   
			var RndAmt = $("#total_roundoff_amt").val();          
			var OtherAmt = $("#OtherAmt").val();          
			var EffectOnOtherAmt = $("#EffectOnOtherAmt").val();          
            
            if (OrderType == 2) 
            {
                InvoiceType = "CREDIT";                
				} else if(OrderType == 1){
                InvoiceType = "CASH";                
			}                    
			
            var tableData = hot.getData(); 
			
            var orderData = tableData.map(function(row) {
                var productName = row[0];            
                var product = productOptions.find(function(product) {
                    return product.name === productName;
				});
				
                var productId = product ? product.value : null;          
				
                return {
                    ProductID: productId,                    
                    Brand: row[1],
                    MeasuredIn: row[2],
                    PackingQty : row[3],
                    PackingWeight : row[4],
                    SaleUnit : row[5],
                    Qty: row[6],
                    Amount: row[7],
                    Discount: row[8],
                    GST: row[9],
                    CgstAmt: row[10],
                    SgstAmt: row[11],
                    IgstAmt: row[12],
                    NetAmount: row[13]
				};
			});
			
            var hasAtLeastOneRow = tableData.some(function(row) {              
                return row.some(function(cell) {
                    return cell !== "" && cell !== null; 
				});
			});          
			
            var isSaleUnitValid = tableData.every(function(row) {
                return row[5] !== null && row[5] !== ""; 
			});
			
			
            if(AccountId =="")
            {
                alert_float('warning', 'Select Account..');     
                $('#myModal').modal('show'); 
				}else if (CenterId == "") { 
                alert_float('warning', 'Select Center');               
				}else if(AccountId == "new" && PartyName == ""){
                alert_float('warning', 'Please Enter Party Name..'); 
				}else if (PartyName.startsWith(" ")) {
                alert_float('warning', 'Please Enter Valid Name..');
                $(this).val(PartyName.trim()); 
				}else if(AccountId == "new" && MobileNo == ""){
                alert_float('warning', 'Please Enter Mobile No..');   
				}else if(AccountId == "new" && BillingState == ""){
                alert_float('warning', 'Please Select State..');   
				}else if (VillageName.startsWith(" ")) {
                alert_float('warning', 'Please Enter Valid Village Name..');
                $(this).val(VillageName.trim()); 
				}else if (OtherAmt !== "" && EffectOnOtherAmt == "") { 
                alert_float('warning', 'Select Effect On Other Amt');               
				}else if (!hasAtLeastOneRow) {
                alert_float('warning', 'Add product before inserting order');               
			}            
            else
            {
                $.ajax({
                    url: "<?php echo admin_url(); ?>ItemMaster/AddEditOrder", 
                    type: 'POST', 
                    data: {PostedDate:PostedDate,OrderId:OrderId,AccountId:AccountId,CenterId:CenterId,
                        InvoiceType:InvoiceType,TotalCgstAmt:TotalCgstAmt,TotalSgstAmt:TotalSgstAmt,IgstAmt:IgstAmt,TotalValue:TotalValue,TotalDiscountAmt:TotalDiscountAmt,TotalNetPayableAmt:TotalNetPayableAmt,
						MobileNo:MobileNo,PartyName:PartyName,BillingState:BillingState,BillNo:BillNo,VillageName:VillageName,
					Effecton:Effecton,RndAmt:RndAmt,OrderType:OrderType,PaymentMode:PaymentMode,PaymentMethod:PaymentMethod,ReferenceNo:ReferenceNo,orderData: JSON.stringify(orderData),OtherAmt:OtherAmt,EffectOnOtherAmt:EffectOnOtherAmt }, 
                    dataType: 'json',
                    success: function(response) {                    
                        if (response.success) {  
                            orderId = response.nextOrdernumber;
                            var prefix = "ORD";
                            var fy = "<?php echo $this->session->userdata('finacial_year'); ?>"; 
                            var concatenated_value = prefix + fy;
							
                            var orderNumber = parseInt(orderId, 10); 
                            orderNumber++;
                            var newOrderNumber = concatenated_value + orderNumber.toString().padStart(orderId.length, '0');                           
							
                            $("#orderid").val(orderId);                
                            alert_float('success', 'Record Created Successfully...');    
                            
                            hot.loadData([["", "", "", "", "", "", "", "", ""]]);  
							
                            var currentDate = new Date();
                            var formattedDate = ("0" + currentDate.getDate()).slice(-2) + '/' + 
							("0" + (currentDate.getMonth() + 1)).slice(-2) + '/' + 
							currentDate.getFullYear();
                            $("#posted_date").val(formattedDate);
                            $("#AccountID").val('');
                            $('#AccountID').selectpicker('refresh');    
                            $('#centername').val('');
                            $('#centername').selectpicker('refresh'); 
                            $('#phonenumber').val('');
                            $('#ledgerbal').val('');
                            $('#name').val('');
                            $('#pin').val('');
                            $('#stateid').val('');
                            $('#stateid').selectpicker('refresh'); 
                            $('#city').val('');
                            $('#city').selectpicker('refresh'); 
                            $('#subdist').val('');
                            $('#subdist').selectpicker('refresh'); 
							$('#billstateid').val('');
                            $('#billstateid').selectpicker('refresh');
							$('#partyname').val('');
                            $('#billno').val('');
							$('#villagename').val('');
                            $('#loc').val('');
                            $('#street').val('');
                            $('#house').val('');
                            $("#total_amt_in_mt").val('');
                            $("#total_qty_in_mt").val('');
                            $("#total_disc_in_mt").val('');
                            $("#Total_value").val('');
                            $("#total_roundoff_amt").val('');
                            $("#netpayableamt").val('');
                            $("#OtherAmt").val('');
                            $("#EffectOnOtherAmt").val('');
                            $('#EffectOnOtherAmt').selectpicker('refresh'); 
                            document.querySelector('input[name="total_cgst_amt"]').value = '';
                            document.querySelector('input[name="total_sgst_amt"]').value = '';
                            document.querySelector('input[name="total_igst_amt"]').value = '';
							} else {                    
                            alert_float('warning', 'Something went wrong...');                            
						}
					},
                    error: function(xhr, status, error) {                
                        $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
					}
				});                    
			}            
		});	
		
        $('#AccountID').on('change', function() 
        {           
            var AccountID = $("#AccountID").val();    
			if(AccountID == "new")
            {
                $("#villagename").val(''); 
			}               
            
            $.ajax({
                url:"<?php echo admin_url(); ?>ItemMaster/GetAccountWiseFarmerDetails",
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
					
					$("#villagename").val(OrderDetails.VillageName);              
                    
                    $('#pin').val(ClientData.zip);                            
                    $('#loc').val(ClientData.loc);   
                    $('#street').val(ClientData.street);     
                    $('#house').val(ClientData.house);  
					
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
				}
			});
		});     
		
		
        function fetchProductDetails(productID,row,CenterName,accountID) 
        {             
            var OrderId = getQueryParameter('OrderId');                     
            $.ajax({
                url: "<?php echo admin_url(); ?>ItemMaster/GetProductDetailById",  
                dataType: "json",  
                method: "POST",    
                data: { productID: productID,CenterName:CenterName,accountID:accountID,OrderId:OrderId}, 
				
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
						var BillingState = $("#billstateid").val();  
                        var Centers = data.centerDetails;
                        var Clients = data.clientDetails;
                        var ItemDetail = data.historyDetails;                                     
						
                        var gst = data.product.taxrate;
						
                        if(data.clientDetails != null)
                        {
                            var isSameState = (Centers.state && Clients.state) && (Centers.state === Clients.state); 
						}
                        else if(data.clientDetails == null && BillingState != "")
                        {
                            var isSameState = (Centers.state && BillingState) && (Centers.state === BillingState); 
						}
						
                        var totalGST = data.product.rate * gst / 100;
                        var cgst = totalGST / 2;
                        var sgst = totalGST / 2;
                        var igst = totalGST;  
						
                        hot.setDataAtCell(row, 1, data.product.BrandName);         
                        hot.setDataAtCell(row, 2, data.product.unit);   
                        hot.setDataAtCell(row, 3, data.product.PackingQty);   
                        hot.setDataAtCell(row, 4, data.product.PackingWeight);    
                        hot.setDataAtCell(row, 7, data.product.rate);                                                                                                  
                        hot.setDataAtCell(row, 9, gst);                     
						
                        var unit = data.product.unit; 
                        var rowDropdownSource = ["Loose"];
                        if (!rowDropdownSource.includes(unit)) {
                            rowDropdownSource.push(unit);
						}
                        hot.setCellMeta(row, 5, 'source', rowDropdownSource);
                        hot.render();
                        hot.setDataAtCell(row, 5, unit);
						
                        if(isSameState == false)
                        {        
                            hot.setDataAtCell(row, 10, null); 
                            hot.setDataAtCell(row, 11, null); 
                            hot.setDataAtCell(row, 12, 0.00);
						}
                        else
                        {
                            hot.setDataAtCell(row, 10, 0.00); 
                            hot.setDataAtCell(row, 11, 0.00); 
                            hot.setDataAtCell(row, 12, null); 
						}                       
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
		
        //redirecting on update
        function getQueryParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);  
		}               
        
        var OrderId = getQueryParameter('OrderId');          
        
        $.ajax({
            url:"<?php echo admin_url(); ?>ItemMaster/GetOrderWiseItemDetails",
            dataType:"JSON",
            method:"POST",
            data:{OrderId:OrderId},
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
                var salesMasterDetails = data.salesMasterDetails;  
                var SalesDetails = data.salesDetails;  
                var CenterDetail = data.centerDetails; 		 		
				var LedgerBalance = data.ClosingBalance;  		
				var postedDate = OrderDetails.Transdate;  		
				var date = new Date(postedDate .split(' ')[0]);                 		
				var formattedDate = ("0" + date.getDate()).slice(-2) + '/' +("0" + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();                            $("#posted_date").val(formattedDate );					
                var oredernumber = OrderDetails.OrderID.replace(/[^\d]/g, ''); 
                var extractedNumber = oredernumber.substring(2);   		
				
				if (LedgerBalance == null || LedgerBalance === '') {
					$('#ledgerbal').val(0);  
					$('#ledgerbal').removeClass('redText greenText'); 
				}
				else if (LedgerBalance > 0 || LedgerBalance.includes("Dr")) 
				{   $('#ledgerbal').val(LedgerBalance).addClass('redText').removeClass('greenText');                
					} else { $('#ledgerbal').val(LedgerBalance).addClass('greenText').removeClass('redText');                
				}           
				
                $('#AccountID').val(ClientData.AccountID);
                $('.selectpicker').selectpicker('refresh') 	 						
                $("#orderid").val(extractedNumber);
                $("#ordstat").val(OrderDetails.OrderStatus);
                $('.selectpicker').selectpicker('refresh') 
				$("#billno").val(OrderDetails.BIllNo);
				
				$("#ordfrom").val(OrderDetails.order_type);
                $('.selectpicker').selectpicker('refresh') 
				
				$("#villagename").val(OrderDetails.VillageName);
				
                $("#centername").val(OrderDetails.CenterID);
                $('.selectpicker').selectpicker('refresh')                 
                $('#phonenumber').val(ClientData.phonenumber);                   
                $('#pin').val(ClientData.zip);                            
                $('#loc').val(ClientData.loc);   
                $('#street').val(ClientData.street);     
                $('#house').val(ClientData.house);  
                $('#stateid').val(ClientData.state);
                $('.selectpicker').selectpicker('refresh') 
                $('#city').val(ClientData.dist);
                $('.selectpicker').selectpicker('refresh') 
                $('#subdist').val(ClientData.subdist);
                $('.selectpicker').selectpicker('refresh')  
				
				$("#OtherAmt").val(salesMasterDetails.OtherAmt);          
				$("#EffectOnOtherAmt").val(salesMasterDetails.EffectOnOtherAmt); 
                $('.selectpicker').selectpicker('refresh') 
                ProductDetails.forEach(function(detail, index) 
                {                   
                    var row = index; 
                    var Billqty = detail.BilledQty;    
                    var packingqty = detail.PackingQty; 
                    var unit = detail.SuppliedIn; 
                    if(unit !== "Loose")
                    {
                        var TotalQty = Billqty / packingqty;
					}
                    else
                    {
                        var TotalQty = Billqty;
					}                    
					
                    var cgstamt = detail.cgstamt;  
                    var sgstamt = detail.sgstamt;  
                    var igstamt = detail.igstamt; 
                    
                    if(detail.DiscPerc > 0)
                    {
                        discount = detail.DiscPerc;
						}else{
                        discount = 0.00;
					}             
                    
                    if(detail.igstamt == null)
                    {
                        igstamt = 0;
					}
                    else
                    {
                        igstamt = detail.igstamt;
					}
                    hot.setDataAtCell(row, 0, detail.ProductName); 
                    hot.setDataAtCell(row, 1, detail.brandname); 
                    hot.setDataAtCell(row, 2, detail.MeasuredIn); 
                    hot.setDataAtCell(row, 3, detail.PackingQty); 
                    hot.setDataAtCell(row, 4, detail.PackingWeight); 
					
                    var saleunit = detail.SuppliedIn; 
                    var rowDropdownSource = ["Loose"];
                    if (!rowDropdownSource.includes(saleunit)) {
                        rowDropdownSource.push(saleunit);
					}
                    hot.setCellMeta(row, 5, 'source', rowDropdownSource);
                    hot.render();
                    hot.setDataAtCell(row, 5, saleunit);
					
                    hot.setDataAtCell(row, 8, discount);   
                    hot.setDataAtCell(row, 5, unit);                     
                    hot.setDataAtCell(row, 6, TotalQty);  
                    hot.setDataAtCell(row, 7, detail.BasicRate);      
                    hot.setDataAtCell(row, 9, detail.gst);     
                    hot.setDataAtCell(row, 10, parseFloat(detail.cgstamt));     
                    hot.setDataAtCell(row, 11, parseFloat(detail.sgstamt));    
                    hot.setDataAtCell(row, 12, parseFloat(igstamt));                                   
                    hot.setDataAtCell(row, 13, detail.NetOrderAmt);                     
                    
				});                   
				
                if(OrderDetails.order_type=="WEB")
                {                    
                    $("#updatebtn").removeClass("hidden").show().prop('disabled', true);   
				}
                else
                {
                    $("#updatebtn").removeClass("hidden").show();   
				}
                $("#savebtn").hide();                
				$("#viewlist").hide();   
				$("#cancelbtn").removeClass("hidden").show();              
			}
		});
		
		$("#cancelbtn").click(function()
        {	   
			var userConfirmed = confirm("Are you sure you want to cancel the order?");      
			
			if (userConfirmed) 
            {              
                $.ajax({
					
                    url: "<?php echo admin_url(); ?>ItemMaster/CancelOrderWiseItems", 
					
                    type: 'POST', 
					
                    data: {OrderId:OrderId}, 
					
                    dataType: 'json',
					
                    success: function(response) 
                    {                 
                        if (response.success) 
                        {                
                            alert_float('success', 'Order Cancelled Successfully...');  
							
                            $("#ordstat").val("C").selectpicker('refresh');
							
							$("#ordstat").val("C").selectpicker('refresh');
							$("#ordfrom").val("WEB");
							$('.selectpicker').selectpicker('refresh') 
							
                            hot.getData().forEach(function(rowData, rowIndex) {                           
                                if (rowData && rowData[6] != undefined && rowData[13] != undefined) {
                                    hot.setDataAtCell(rowIndex, 6, 0.00); 
                                    hot.setDataAtCell(rowIndex, 10, 0.00); 
                                    hot.setDataAtCell(rowIndex, 11, 0.00); 
                                    hot.setDataAtCell(rowIndex, 12, 0.00); 
                                    hot.setDataAtCell(rowIndex, 13, 0.00); 
								}
							});
                            hot.render();                       
							} else {     
                            alert_float('warning', 'Something went wrong...');    
						}
					},
                    error: function(xhr, status, error) {   
                        $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
					}
					
				});  
			} 
            else {               
                console.log("Order cancellation was cancelled by the user.");
			}
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
		
        function calculateTotalQuantity() {
            var totalQuantity = 0;
            for (var i = 0; i < hot.countRows(); i++) {
                var qty = hot.getDataAtCell(i, 6);
				
                var unit  = hot.getDataAtCell(i, 2);  
                var packingqty = hot.getDataAtCell(i, 3);  
                var saleunit = hot.getDataAtCell(i, 5);    
                
                if (unit === saleunit && packingqty) {
                    qty = packingqty * qty;
				}
				
                if (qty) {
                    totalQuantity += qty;
				}
			}          
            var totalQtyInput = document.getElementById('total_qty_in_mt');
            if (totalQtyInput) {
                totalQtyInput.value = totalQuantity.toFixed(2); 
			}                       
		}
		
        function calaulateSubTotal(){
            var totalamt = 0;
            for (var i = 0; i < hot.countRows(); i++) {
                var qty = hot.getDataAtCell(i, 6); 
                var amountval = hot.getDataAtCell(i, 7);             
				
                var unit  = hot.getDataAtCell(i, 2);  
                var packingqty = hot.getDataAtCell(i, 3);  
                var saleunit = hot.getDataAtCell(i, 5);                
				
                if (unit !== saleunit && packingqty) {
                    amount = (amountval / packingqty) * qty;                    
                    
                    if (!isNaN(qty) && !isNaN(amount)  && qty > 0 && amount > 0) {                       
                        totalamt += amount;  
					}                   
				}
                else
                {
                    amount = amountval;
                    if (!isNaN(qty) && !isNaN(amount) && qty > 0 && amount > 0) {
                        var totalAmount = qty * amount ;
                        totalamt += totalAmount;  
					}
				}     
			}
            var totalAmtInput = document.getElementById('total_amt_in_mt');
            if (totalAmtInput) {
                totalAmtInput.value = totalamt.toFixed(2); 
			}      
            
		}
		
        function calculateTotalDiscount(){
            var totalDisc = 0;
            for (var i = 0; i < hot.countRows(); i++) {
                var qty = hot.getDataAtCell(i, 6); 
                var amountval = hot.getDataAtCell(i, 7);
                var disc = hot.getDataAtCell(i, 8);
				
                var unit  = hot.getDataAtCell(i, 2);  
                var packingqty = hot.getDataAtCell(i, 3);  
                var saleunit = hot.getDataAtCell(i, 5); 
				
                if (unit !== saleunit && packingqty) {
                    amount = (amountval / packingqty) * qty;  
                    
                    if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {
                        var discountAmount = amount * (disc / 100);
                        totalDisc += discountAmount;  
					}
				}
                else
                {
                    amount = amountval;
                    if (!isNaN(qty) && !isNaN(amount) && !isNaN(disc) && qty > 0 && amount > 0 && disc > 0) {
                        var discountAmount = qty * amount * (disc / 100);
                        totalDisc += discountAmount;  
					}
				}                     
			}          
            var totalDiscInput = document.getElementById('total_disc_in_mt');
            if (totalDiscInput) {
                totalDiscInput.value = totalDisc.toFixed(2); 
			}      
		}
		
        function calculateTotalValue(){
            var totalValue = 0;
            for (var i = 0; i < hot.countRows(); i++) {
                var qty = hot.getDataAtCell(i, 6);  
                var amountval = hot.getDataAtCell(i, 7);   
                var discount = hot.getDataAtCell(i, 8);   
				
                var unit  = hot.getDataAtCell(i, 2);  
                var packingqty = hot.getDataAtCell(i, 3);  
                var saleunit = hot.getDataAtCell(i, 5);                 
                
                if (unit !== saleunit && packingqty) {
                    amount = (amountval / packingqty) * qty;  
                    if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {                    
                        var discountedAmount = amount * (1 - discount / 100);                        
                        totalValue += discountedAmount;
					}                                         
				}
                else
                {
                    amount = amountval;
                    if (!isNaN(qty) && !isNaN(amount) && !isNaN(discount) && qty > 0 && amount > 0) {                    
                        var discountedAmount = amount * (1 - discount / 100);                        
                        totalValue += qty * discountedAmount;
					}   
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
                if (cgstamt) {
                    totalCgstAmt += cgstamt;
				}
			}          
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
                if (sgstamt) {
                    totalSgstAmt += sgstamt;
				}
			}          
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
                if (Igst) {
                    totalIgstAmt += Igst;
				}
			}          
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
				
                var unit  = hot.getDataAtCell(i, 2);  
                var packingqty = hot.getDataAtCell(i, 3);  
                var saleunit = hot.getDataAtCell(i, 5);                 
                
                if (unit !== saleunit && packingqty) {
                    rate = (rateval / packingqty) * qty;  
				}
				
                if (unit !== saleunit && packingqty) {
                    amount = (rateval / packingqty) * qty;  
					
                    if (!isNaN(qty) && !isNaN(amount) && !isNaN(gst) && qty > 0 && amount > 0) {
                        // var amount = rate * qty;  
                        
                        if (discount) {
                            amount = amount - (amount * discount / 100); 
						}
						
                        var gstAmount = (amount * gst) / 100;                    
                        var netAmount = amount + gstAmount;
						
                        netAmount = parseFloat(netAmount.toFixed(2));                   
                        totalNetAmount += netAmount;                   
					}                                                         
				}
                else
                {
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
				}       
                
                var roundedNetAmount = totalNetAmount.toFixed(2);
                var decimalPart = parseFloat(roundedNetAmount.split('.')[1]);
                // console.log(decimalPart);
                if (decimalPart >= 50) {
                    totalNetAmounts = Math.ceil(totalNetAmount); 
					} else {
                    totalNetAmounts = Math.floor(totalNetAmount); 
				}
				
                var netPayableAmount = parseFloat(totalNetAmounts).toFixed(2);
			}           
			
            var totalNetPaybleAmount = document.querySelector('input[name="netpayableamt"]');
            var otherAmtInput = document.querySelector('input[name="OtherAmt"]');
			var otherAmount = otherAmtInput && otherAmtInput.value.trim() !== '' ? parseFloat(otherAmtInput.value) || 0 : 0;
			
			netPayableAmount = parseFloat(netPayableAmount) + parseFloat(otherAmount);
			totalNetAmount = parseFloat(totalNetAmount) + parseFloat(otherAmount);
			// console.log(otherAmount);
			
            if (totalNetPaybleAmount) {
                totalNetPaybleAmount.value = netPayableAmount;
			}        
			
			var difference = (totalNetAmount.toFixed(2) - parseFloat(netPayableAmount)).toFixed(2);
			
            var totalRoundOff = document.querySelector('input[name="total_roundoff_amt"]');
            if (totalRoundOff) {
                totalRoundOff.value = difference;
			}            
		}           
	});  
	
</script>

<style>   
    .handsontable th {
	font-weight: bold;
    }
	
    .handsontable th {       
	font-size: 12px; 
    }
	
    .redText {
	color: red !important;
	font-size: 13px !important;
	font-weight: bold !important;
    }
	
    .greenText {
	color: green !important;
	font-size: 13px !important;
	font-weight: bold !important;
    }
</style>