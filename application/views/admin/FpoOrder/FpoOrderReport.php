<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
	    
	    <!--print-->
	    <div class="row" style="display:none;">
			<div class="col-md-12">
				<table id="print_table" style="width: 100%; border-collapse: collapse; table-layout: fixed;" border="1">
					<thead>
						<tr>
							<th align="center" colspan="12"><?php echo $company_detail->company_name; ?></th>
						</tr>
						<tr>
							<th align="center" colspan="12"><?php echo $company_detail->address; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="12"><center><b>Fpo Order</b></center></td>
						</tr>
						<tr>
							<td colspan="3"><b>PO No :</b> <span id="po_no"></span></td>
							<td colspan="3"><b>PO Date : </b><span id="po_date"></span></td>
							<td colspan="6"><b>Fpo Name :</b> <span id="fpo_name"></span></td>
						</tr>
						<tr>
							<td colspan="12"><b>Item Name : </b><span id="item_name"></span></td>
						</tr>
						<tr>
							<td align="center" colspan="12"><b>Item Details</b></td>
						</tr>
						<tr>
							<td colspan="4" align="left">Farmer Name</td>
							<td colspan="2" align="center">Farmer Rate</td>
							<td align="center">Order Wt(In Qtl)</td>
							<td colspan="2" align="center">Net Rate</td>
							<td align="center">Order Amt</td>
							<td align="center">Deduction</td>
							<td align="center">Net Amt</td>
						</tr>
					
						<tr style="white-space: nowrap;">
							<td colspan="4" align="left"><span id="farmer_name"></span></td>
							<td colspan="2" align="center"><span id="farmer_rate"></td>
							<td align="center"><span id="order_wt"></td>
							<td colspan="2" align="center"><span id="net_rate"></td>
							<td align="center"><span id="Amt"></td>
							<td align="center"><span id="deduction"></td>
							<td align="center"><span id="netamt"></td>
						</tr>   
						
					</tbody>
				</table>
				
        		 <table id="deduction_matrix" style="width: 100%; border-collapse: collapse; table-layout: fixed;" border="1" style="margin-top:10px;">
                  <thead>
                    <tr><th colspan="12" style="text-align:center;"><b>Deduction Matrix</b></th></tr>
                    
                    <tr>
                      <th colspan="2">Sr.No.</th>
                      <th colspan="4">Parameter Name</th>
                      <th colspan="2">QC Value</th>
                      <th colspan="4">Deduction Amt</th>
                    </tr>
                  </thead>
                  <tbody id="deduction_matrix_body">
                    <!-- dynamic rows go here -->
                  </tbody>
                </table>
		
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb" >
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Transaction </b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>FPO Order Report</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
						    <?php
                                    $fy = $this->session->userdata('finacial_year');
                                    $fy_new  = $fy + 1;
                                    $lastdate_date = '20'.$fy_new.'-03-31';
                                    $firstdate_date = '20'.$fy_new.'-04-01';
                                    $curr_date = date('Y-m-d');
                                    $curr_date_new    = new DateTime($curr_date);
                                    $last_date_yr = new DateTime($lastdate_date);
                                    if($last_date_yr < $curr_date_new){
                                        $to_date = '31/03/20'.$fy_new;
                                        $from_date = '01/03/20'.$fy_new;
                                    }else{
                                        $from_date = "01/".date('m')."/".date('Y');
                                        $to_date = date('d/m/Y');
                                    }
                            ?> 
							<div class="col-md-12">
							    <input type="hidden" name="comp_name" id="comp_name" value="<?php echo $company_detail->company_name;?>">
							    <input type="hidden" name="comp_addr" id="comp_addr" value="<?php echo $company_detail->address;?>">
								<div class="row">
								    <div class="col-md-2">
                                        <?php
                                            echo render_date_input('from_date','From',$from_date);
                                        ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php
                                            echo render_date_input('to_date','To',$to_date);
                                        ?>
                                    </div>
                                    
                                    <div class="col-md-3">
                                      <div class="form-group">
                                        <label for="fpolist">FPO List</label>
                                        <select name="fpolist" id="fpolist" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                        <option value=""></option>
                                        <?php
                                            foreach($TraderList as $key=>$val){
                                                ?>
                                                    <option value="<?php echo $val["AccountID"]?>"><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                                <?php
                                            }
                                        ?>
                                        </select>
                                      </div>
                                    </div>
								
									<div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ItemID">ItemID</label>
                                            <select name="ItemID" id="ItemID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value=""></option>
                                            <?php 
                                                foreach($ItemList as $key=>$value){ ?>
                                                    <option value="<?php echo $value['ItemID']; ?>" <?php if($ItemID == $value['ItemID']){ echo 'selected';} ?>><?php echo $value['ItemName']; ?></option>
                                            <?php 
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value="">ALL</option>
                                                <option value="1">Pending</option>
                                                <option value="2">In Progress</option>
                                                <option value="3">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="paystatus">Payment Status</label>
                                            <select name="paystatus" id="paystatus" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value="">ALL</option>
                                                <option value="1">Unpaid</option>
                                                <option value="2">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                    
									<div class="col-md-6">
										<div class="custom_button">
											<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-top: 15px;">Show</button>
											<?php if (has_permission_new('FpoOrder_Report', '', 'export')) {
											?>
											<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-left:5px;margin-top: 15px;"><span>Export to Excel</span></a>
											<?php } ?>
											
											<?php if (has_permission_new('FpoOrder_Report', '', 'print')) {
											?>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-left:5px;margin-top: 15px;">Print</a>
											<?php } ?>
										</div>
									</div>
									<div class="col-md-4" style="margin-top:1%;">
                                        <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
                                    </div>
									
								</div>
							</div>
							
						</div>
						
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-md-12">
                                <span id="searchh2" style="display:none;">Loading.....</span>
                                <div class="table_purchase_report">
                                    <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                              
                                        <thead>
                                            <tr style="display:none;">
                                                <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                            </tr>
                                            <tr>
                                                <th style="width:7% ">Sr.No</th>
                                                <th style="width:7% ">PO.No</th>
                                                <th style="width:7% ">PO Date</th>
                                                <th style="width:7% ">FPO Name</th>
                                                <th style="width:7% ">FPO Rate</th>
                                                <th style="width:7% ">Item Name</th>
                                                <th style="width:7% ">Status</th>
                                                <th style="width:7% ">Farmer Name</th>
                                                <th style="width:7% ">Farmer Rate</th>
                                                <th style="width:7% ">Order Wt(In Qtl)</th>
                                                <th style="width:7% ">Dispatch Wt(In Qtl)</th>
                                                <th style="width:7% ">Pending Wt(In Qtl)</th>
                                                <th style="width:7% ">Net Rate</th>
                                                <th style="width:7% ">Amount</th>
                                                <!--<th style="width:7% ">Print</th>-->
                                                <th style="width:7% ">Payment Status</th>
                                                <th style="width:7% ">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>   
                                </div>
                            </div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        load_data();
    		
    	$("#caexcel").click(function(){
    		    var from_date = $("#from_date").val();
    	        var to_date = $("#to_date").val();
    			var Fpolist = $("#fpolist").val();
    			var Item = $("#ItemID").val();  
    			var status = $('#status').val();
    			var payment_status = $('#paystatus').val();
    			
    			var FpoListText = $("#fpolist option:selected").text();
    			var ItemText = $("#ItemID option:selected").text();
    			var statusText = $("#status option:selected").text();
    			var paymentText = $("#paystatus option:selected").text();
    		
    				$.ajax({
    					url:"<?php echo admin_url(); ?>FpoOrder/export_FpoOrder_Report",
    					method:"POST",
    					data:{
    				        from_date:from_date,to_date:to_date,Fpolist:Fpolist,Item:Item,status:status,FpoListText:FpoListText,statusText:statusText,
    					    ItemText:ItemText,payment_status:payment_status,paymentText:paymentText
    					},
    					beforeSend: function () {
    						$('#searchh2').css('display','block');
    					},
    					complete: function () {
    						$('#searchh2').css('display','none');
    					},
    					success:function(data){
    						response = JSON.parse(data);
    						window.location.href = response.site_url+response.filename;
    					}
    				});
    		});
    }); 
    
    var accountDispatchSummary = {};
    function load_data()
    {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
		var Fpolist = $("#fpolist").val();
		var Item = $("#ItemID").val();  
		var status = $('#status').val();
		var payment_status = $('#paystatus').val();
		$.ajax({
            url:"<?php echo admin_url(); ?>FpoOrder/GetFilterFpoOrderData",
            dataType:"JSON",
            method:"POST",
            data:{from_date:from_date,to_date:to_date,Fpolist:Fpolist,Item:Item,status:status,payment_status:payment_status},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('.table_purchase_report tbody').css('display','none');
            },
            complete: function () {
                $('.table_purchase_report tbody').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                var html = '';
                var i=1;
                var orderCounts = {};
                data.forEach(function (row) {
                    if (!orderCounts[row.OrderID]) {
                        orderCounts[row.OrderID] = 0;
                    }
                    orderCounts[row.OrderID]++;
                });
                var rendered = {};
                for(var count = 0; count < data.length; count++)
                {
                    if(data[count].Status == 1)
                    { Status = "Pending"; }
                    else if(data[count].Status == 2)
                    { Status = "In Progress"; }
                    else if(data[count].Status == 3)
                    { Status = "Completed"; }
                    
                    var url = "<?php echo admin_url(); ?>FpoOrder/FpoOrderDispatch/" + data[count].OrderID;
                    var paymenturl =  "<?php echo admin_url(); ?>FpoOrder/FpoPayment/" + data[count].OrderID;
                    html += '<tr>';
                    if (!rendered[data[count].OrderID]) {
                        var rowspan = orderCounts[data[count].OrderID];
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + i + '</td>';
                        i++;
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + data[count].OrderID + '</td>';
                        var date = data[count].Transdate.substring(0, 10);
                        var date_new = date.split("-").reverse().join("/");
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + date_new + '</td>';
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">' + data[count].company + '</td>';
                        html += '<td style="text-align:right;" rowspan="' + rowspan + '">' + data[count].FpoRate + '</td>';
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">' + data[count].ItemName + '</td>';
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + Status + '</td>';
                    }
                    html += '<td style="text-align:left;">'+data[count].farmer_name+'</td>';
                    html += '<td style="text-align:right;">'+data[count].farmer_rate+'</td>';
                    html += '<td style="text-align:center;">'+data[count].weight+'</td>';
                    
                    var dispatchWeightTotal = parseFloat(data[count].DispatchWt) || 0;
                    html += '<td style="text-align:center;">' + dispatchWeightTotal.toFixed(2) + '</td>';
                    
                    var PendingWt = parseFloat(data[count].weight) - dispatchWeightTotal;
                    html += '<td style="text-align:center;">' + PendingWt.toFixed(2) + '</td>';
                    
                    html += '<td style="text-align:center;">'+data[count].NetRate+'</td>';
                    html += '<td style="text-align:right;">'+data[count].NetAmt +'</td>';
                    
                    /*html += '<td style="text-align:right;">' +
                        '<a href="javascript:void(0);" onclick="printSingleRow(\'' 
                          + data[count].OrderID + '\', \'' 
                          + date_new + '\', \'' 
                          + data[count].company + '\', \'' 
                          + data[count].ItemName + '\', \'' 
                          + data[count].farmer_name + '\', \'' 
                          + data[count].farmer_rate + '\', \'' 
                          + data[count].weight + '\', \'' 
                          + data[count].NetRate + '\', \'' 
                          + data[count].NetAmt + '\', \'' 
                          + data[count].Deduction + '\', \'' 
                          + encodeURIComponent(JSON.stringify(data[count].QcDetails))
                        + '\')" title="Print">' +
                          '<i class="fa fa-print" aria-hidden="true"></i>' +
                        '</a>' +
                    '</td>';*/
                    
                    if (!rendered[data[count].OrderID]) {
                        var rowspan = orderCounts[data[count].OrderID];
                        if(data[count].PaymentStatus == 1)
                        {  PaymentStatus = "UNPAID"; }
                        else if(data[count].PaymentStatus == 2)  
                        {  PaymentStatus = "PAYMENT DONE"; }
                        
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + PaymentStatus + '</td>';
                        
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">';
                    <?php
                        if (has_permission_new('FpoOrder_Dispatch', '', 'create')) {
                    ?>
                        if (data[count].PaymentStatus != 2 || data[count].Status !=3) {
                            html += '<a href="' + url + '" class="btn btn-sm btn-primary" style="margin-right: 5px;" target="_blank" title="Dispatch"><i class="fa fa-truck"></i></a>';
                        }
                    <?php } ?>
                    <?php
                        if (has_permission_new('FpoOrder_Payment', '', 'create')) {
                    ?>
                        if (data[count].PaymentStatus == 1) {
                            html += '<button class="btn btn-sm btn-success make-payment-btn" ' +
                                'data-url="' + paymenturl + '" ' +
                                'data-accountid="' + data[count].FPOID + '" ' +
                                'data-account-name="' + data[count].company + '" ' +
                                'title="Create Inward">MAKE PAYMENT</button>';
                                
                            //html += '<a href="' + paymenturl + '" class="btn btn-sm btn-success" target="_blank" title="Create Inward">MAKE PAYMENT</a>';
                        } else if (data[count].PaymentStatus == 2) {
                            html += '<a href="' + paymenturl + '" class="btn btn-sm btn-info" target="_blank" title="View Payment">VIEW PAYMENT</a>';
                        }
                    <?php } ?>    
                        html += '</td>';
                        rendered[data[count].OrderID] = true;
                    }
                    html += '</tr>';
                }
                
                accountDispatchSummary = {};

                data.forEach(function(row) {
                    if (row.PaymentStatus == 2) { 
                        var accId = row.FPOID;
                
                        if (!accountDispatchSummary[accId]) {
                            accountDispatchSummary[accId] = {
                                totalOrderedWeight: 0,
                                totalPendingWeight: 0,
                                totalDispatchedWeight: 0,
                                accountName: row.company  
                            };
                        }
                
                        var dispatchWeight = parseFloat(row.DispatchWt) || 0;
                        var orderedWeight = parseFloat(row.weight) || 0;
                        var pendingWeight = orderedWeight - dispatchWeight;
                        
                        accountDispatchSummary[accId].totalOrderedWeight += orderedWeight;
                        accountDispatchSummary[accId].totalDispatchedWeight += dispatchWeight;
                        accountDispatchSummary[accId].totalPendingWeight += pendingWeight;
                    }
                });
                    
                for (let accId in accountDispatchSummary) {
                    let acc = accountDispatchSummary[accId];
                    acc.dispatchedPercent = (acc.totalDispatchedWeight / acc.totalOrderedWeight) * 100 || 0;
                    acc.pendingPercent = (acc.totalPendingWeight / acc.totalOrderedWeight) * 100 || 0;
                }
                
                $('.table_purchase_report tbody').html(html);
            }
        });
    }
   
    $(document).on('click', '.make-payment-btn', function () {
        var accId = $(this).data('accountid');
        var accountName = $(this).data('account-name');
        var paymentUrl = $(this).data('url');
    
        if (!accountDispatchSummary || !accountDispatchSummary[accId]) {
            window.open(paymentUrl, '_blank');
            return;
        }
    
        var summary = accountDispatchSummary[accId];
        var pending = summary.totalPendingWeight;
        var ordered = summary.totalOrderedWeight;
        var dispatch = summary.totalDispatchedWeight;
    
        if (ordered > 0) {
            var pendingPercent = (pending / ordered) * 100;
    
            if (pendingPercent > 20) {
                alert(
                    "🚫 Payment Blocked for Account: " + accountName +
                    "\nPending dispatch is " + pendingPercent.toFixed(2) + "% of total order weight from previous paid orders." +
                    "\n\nPlease complete pending dispatch before making further payments."
                );
                return false;
            }
        }
        window.open(paymentUrl, '_blank');
    });
    
    $('#search_data').on('click',function()
	{
	   load_data();
	});
</script>

<script type="text/javascript">
    function printPage() 
    {
       var from_date = $("#from_date").val();
	   var to_date = $("#to_date").val();
        var comp_name = $("#comp_name").val();
        var comp_addr = $("#comp_addr").val();
        var FpoList = $("#fpolist option:selected").text() || "ALL";
        var ItemGroup = $("#ItemID option:selected").text() || "ALL";
        
        var tableContent = document.querySelector('.table_purchase_report'); 
        
        var tableHTML = tableContent.innerHTML;
        var stylesheet = `
            <style>
                body { font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid black; padding: 5px; }
                .hide_in_print { display: none !important; }
            </style>
        `;

        var heading_data = `
            <table>
                <tr><td colspan="9" style="text-align:center;">${comp_name}</td></tr>
                <tr><td colspan="9" style="text-align:center;">${comp_addr}</td></tr>
                <tr><td colspan="9" style="text-align:center;">Filters:- &nbsp;From Date: ${from_date} &nbsp;To Date: ${to_date} &nbsp;FPO Name: ${FpoList} &nbsp;Item Name: ${ItemGroup}</td></tr>
            </table>
        `;
        
        var printContent = `
            ${stylesheet}
            ${heading_data}
            ${tableHTML}
        `;
        
        var newWin = window.open('', '', 'width=900,height=700');
        newWin.document.write('<html><head><title>Print</title></head><body>');
        newWin.document.write(printContent);
        newWin.document.write('</body></html>');
        newWin.document.close();
       
        newWin.onload = function () {
            newWin.focus();
            newWin.print();
            newWin.close();
        };
    }
    
    function printSingleRow(orderId,orderDate,FpoNAme,ItemName,FarmerName,FarmerRate,OrderWt,NetRate,Amt,Deduction,qcDetailsEncoded)
    {
        var OrderAmt = parseFloat(FarmerRate * OrderWt) || 0;
        var formattedOrderAmt = OrderAmt.toFixed(2);
        
        let qcDetails = [];
        try {
            qcDetails = JSON.parse(decodeURIComponent(qcDetailsEncoded));
        } catch (err) {
            console.error("Could not parse qcDetails:", err);
        }
        
        document.getElementById('po_no').textContent = orderId;
        document.getElementById('po_date').textContent = orderDate;
        document.getElementById('fpo_name').textContent = FpoNAme;
        document.getElementById('item_name').textContent = ItemName;
        document.getElementById('farmer_name').textContent = FarmerName;
        document.getElementById('farmer_rate').textContent = FarmerRate;
        document.getElementById('order_wt').textContent = OrderWt;
        document.getElementById('net_rate').textContent = NetRate;
        document.getElementById('Amt').textContent = formattedOrderAmt;
        document.getElementById('deduction').textContent = Deduction;
        document.getElementById('netamt').textContent = Amt;
        
        let deductionRowsHtml = '';
          qcDetails.forEach((qc, idx) => {
            deductionRowsHtml += `
              <tr>
                <td colspan="2" align="center">${idx + 1}</td>
                <td colspan="4" align="left">${qc.ItemParameterName}</td>
                <td colspan="2" align="center">${qc.Qc_Value}</td>
                <td colspan="4" align="center">${qc.Qc_Amt}</td>
              </tr>
            `;
          });
        
          // Insert those rows into the deduction_matrix_body
          const dedBodyElem = document.getElementById('deduction_matrix_body');
          if (dedBodyElem) {
            dedBodyElem.innerHTML = deductionRowsHtml;
          }

        
        
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;color:#fff;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+document.getElementsByTagName('table')[1].innerHTML+'</table>';
		var print_data = stylesheet+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
    }
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.trim();       
        table = document.getElementById("table_purchase_report");
        tr = table.getElementsByTagName("tr"); 

        for (i = 2; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 

            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
                    }
                }
            }
        }
   }
</script>

<style>
.table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
.table_purchase_report tbody th { position: sticky; left: 0; }

.table_purchase_report table  { border-collapse: collapse; width: 100%; }
.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
.table_purchase_report th     { background: #50607b;color: #fff !important; }


#table_purchase_report tr:hover {
    background-color: #ccc;
}

#table_purchase_report td:hover {
    cursor: pointer;
}
</style>
