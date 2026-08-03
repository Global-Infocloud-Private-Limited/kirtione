<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 40vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead  { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
td:hover {
    cursor: pointer;
}
tr:hover {
    background-color: #ccc;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		 <div class="col-md-12">
		<div class="panel_s" style="margin-bottom: 8px;">
		        <?php
                    $from_date = "01/"."04"."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
        <div class="panel-body">
		    <div class="_buttons">
		        <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Misc dashboard</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
                <div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="ItemID">
                        <label for="ItemID" class="form-label">Item Name</label>
                        <select name="ItemID" id="ItemID" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($items as $value){
                                    ?>
                                        <option value="<?php echo $value['ItemID']; ?>" ><?php echo $value['ItemName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="Center">
                        <label for="Center" class="form-label">Center</label>
                        <select name="Center" id="Center" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($centers as $value){
                                    ?>
                                        <option value="<?php echo $value['CenterID']; ?>" ><?php echo $value['CenterName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="TType">
                        <label for="TType" class="form-label">Booking Type</label>
                        <select name="TType" id="TType" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="P" >Purchase</option>
                            <option value="D">Deposit</option> 
                            <option value="W" >Withdrawal</option>
                            <option value="A" >Anamat</option>
                            <option value="T" >Trade Finance</option>
                            <option value="S" >Sell</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group" app-field-wrapper="FeildOfficer">
                        <small class="req text-danger">* </small>
                        <label for="FeildOfficer" class="control-label">Select Field Officer</label>
                        <select name="FeildOfficer" id="FeildOfficer" class="selectpicker form-control" data-live-search="true">
                            <option value="" >Non Selected</option>
                        <?php
                            foreach($StaffList as $key=>$val){
                        ?>
                                <option value="<?php echo $val["AccountID"];?>" <?php if($val["AccountID"] == $details->FeildOfficer){ echo "selected";}?>><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                        <?php
                            }
                        ?>
						</select>
					</div>
				</div>
							
				<!--<div class="col-md-2">
					<label class="control-label">Chart Type</label>
					<select name="ChartType" id="ChartType" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
						<option value="Bar">Bar Chart</option>
						<option value="Pie">Pie Chart</option>
					</select>
				</div>-->
               
                <div class="col-md-6">
                    <?php if (has_permission_new('MISDashboard', '', 'view')) {
                    ?>
                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                    <?php } ?>
                    
                    
               </div>
                <br>
            </div>
        </div>
				<div class="row" style="margin-top: 10px;">
                    <!-- First Panel -->
                    <div class="col-md-5">
                        <div class="panel_s">
                            <div class="panel-body">
                                <?php if (has_permission_new('MISDashboard', '', 'export')) {
                                ?>
                                <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 5px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="centerwiseexcel"><span>Export to excel</span></a>
                                <?php } ?>
                                <?php if (has_permission_new('MISDashboard', '', 'print')) {
                                ?>
                                <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 5px;margin-left:5px;"  onclick="printPage();">Print</a>
                                <?php } ?>
                                <div class="table-purchase_request tableFixHead2" >
                                    <table class="table-striped table-bordered stock_position"  id="CenterWisePurchase">
                                        <thead>
                                            <tr>
                                                <th colspan="3" style="text-align:center;">Center Wise Purchase</th>
                                            </tr>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th >Center Name</th>
                                                <th>Quantity (MT)</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Panel -->
                    <div class="col-md-7" >
                        <div class="panel_s">
                            <div class="panel-body">
                                <?php if (has_permission_new('MISDashboard', '', 'export')) {
                                ?>
                                <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 5px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="staffwiseexcel"><span>Export to excel</span></a>
                                <?php } ?>
                                <?php if (has_permission_new('MISDashboard', '', 'print')) {
                                ?>
                                <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 5px;margin-left:5px;"  onclick="staffwiseprintPage();">Print</a>
                                <?php } ?>
                                <div class="table-purchase_request tableFixHead2" >
                                    <table class="table-striped table-bordered stock_position"  id="CenterWiseStaffWisePurchase">
                                        <thead>
                                            <tr>
                                                <th colspan="4" style="text-align:center;">Center Wise Staff Wise Purchase</th>
                                            </tr>
                                            <tr>
                                                <th >Sr. No.</th>
                                                <th >Center Name</th>
                                                <th >Staff Name</th>
                                                
                                                <th >Quantity (MT)</th>
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
				
				<div class="row">
                    <div class="col-md-12">
        				<div class="panel_s">
        					<div class="panel-body">
        					    <div class="row">
        							<div id="CenterWisePurchaseChart"></div>
        						</div>
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
<?php //$this->load->view('admin/dashboard/dashboard_js'); ?>
	<script>
		
		$(document).ready(function () {
			
			CenterWisePurchaseChart();	
			CenterWisePurchase();
			CenterWiseStaffWisePurchaseQuantity();
			
		});
		$('#search_data').on('click',function(){
			CenterWisePurchaseChart('CenterWisePurchaseChart', '', 'Center Wise Purchase');
			
			CenterWisePurchase();
			CenterWiseStaffWisePurchaseQuantity();
	
		});
		function CenterWiseStaffWisePurchaseQuantity() 
		{
		    var fromDate = $('#from_date').val();
			var toDate = $('#to_date').val();
			var ItemID = $('#ItemID').val();
			var Center = $('#Center').val();
			var TType = $('#TType').val();
			var FeildOfficer = $('#FeildOfficer').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>Misc_Dashboard/CenterWiseStaffWisePurchase",
				type: 'POST',
				dataType: 'json',
				data: { from_date: fromDate, to_date: toDate, ItemID:ItemID, CenterID:Center, TType:TType,FeildOfficer:FeildOfficer},
				success: function (response) {
					let rows = '';
					let Total = 0;
					$.each(response, function (i, row) {
					     Total += parseFloat(row.QtyMt);
						rows += `
							<tr>
								<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;">${i + 1}</td>
								<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;">${row.CenterName || ''}</td>
								<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;"> ${row.firstname ? row.firstname : ''} ${row.lastname ? row.lastname : ''}</td>
								
								<td  style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;">${parseFloat(row.QtyMt).toFixed(2)}</td>
							</tr>
						`;
					});
					rows += `
							<tr>
								<td colspan="3" style="font-weight:700;font-size:14px;">Total</td>
								<td style="font-weight:700;font-size:14px;text-align: right;">${parseFloat(Total).toFixed(2)}</td>
							</tr>
						`;
					$('#CenterWiseStaffWisePurchase tbody').html(rows);
				}
			});
			}
		
		function CenterWisePurchase() {
			var fromDate = $('#from_date').val();
			var toDate = $('#to_date').val();
			var ItemID = $('#ItemID').val();
			var Center = $('#Center').val();
			var TType = $('#TType').val();
			var FeildOfficer = $('#FeildOfficer').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>Misc_Dashboard/CenterWisePurchase",
				type: 'POST',
				dataType: 'json',
				data: { from_date: fromDate, to_date: toDate, ItemID:ItemID, CenterID:Center, TType:TType,FeildOfficer:FeildOfficer},
				success: function (response) {
					let rows = '';
					let Total = 0;
					$.each(response, function (i, row) {
					    Total += parseFloat(row.QtyMt);
						rows += `
							<tr>
								<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;">${i + 1}</td>
								<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;">${row.CenterName}</td>
								<td  style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;">${parseFloat(row.QtyMt).toFixed(2)}</td>
							</tr>
						`;
					});
					rows += `
							<tr>
								<td colspan="2" style="font-weight:700;font-size:14px;">Total</td>
								<td style="font-weight:700;font-size:14px;text-align: right;">${parseFloat(Total).toFixed(2)}</td>
							</tr>
						`;
					$('#CenterWisePurchase tbody').html(rows);
				}
			});
			
		}
				
		//=========== Top Purches Item Chart Report =========================		
		let originalTop5SellingChartOptions = null;
		
		function CenterWisePurchaseChart() {
			var ChartType = $("#ChartType").val();
			var fromDate = $('#from_date').val();
			var toDate = $('#to_date').val();
			var ItemID = $('#ItemID').val();
			var Center = $('#Center').val();
			var FeildOfficer = $('#FeildOfficer').val();
			
			$.ajax({
				url: "<?php echo admin_url(); ?>Misc_Dashboard/CenterWisePurchaseChart",
				dataType: "JSON",
				method: "POST",
				data: { ChartType: ChartType, from_date: fromDate, to_date: toDate, ItemID: ItemID, CenterID: Center, FeildOfficer: FeildOfficer},
				success: function (response) {
					const isPie = (ChartType === "Pie");
					
					// Validate and format pie chart data
					if (isPie) {
						response.ChartData = response.ChartData.map(item => ({
							name: item.name || item.label || "Unnamed",
							y: parseFloat(item.y || item.value || 0),
							ProductID: item.ProductID || item.id || 0
						}));
					}
					
					if (!response.ChartData || response.ChartData.length === 0) {
						alert("No data to display.");
						return;
					}
					
					const options = {
						chart: {
							type: isPie ? 'pie' : 'column'
						},
						title: {
							text: 'Center Wise Purchase'
						},
						xAxis: !isPie ? {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
								style: {
									fontSize: '11px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						} : undefined,
						yAxis: !isPie ? {
							min: 0,
							title: {
								text: 'Purchase Amount'
							}
						} : undefined,
						legend: { enabled: false },
						tooltip: {
							pointFormat: isPie
							? '<b>{point.y:.1f}%</b>'
							: 'Purchase Amount: <b>{point.y:.1f}</b>'
						},
						series: [{
							name: 'Items',
							colorByPoint: true,
							data: response.ChartData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: (ChartType === "Pie") ? '{point.y:.1f}%' : '{point.y:.1f}',
								y: 10,
								style: {
									fontSize: '13px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						}]
					};
					originalTop5SellingChartOptions = options;
					Highcharts.chart("CenterWisePurchaseChart", options);
				}
			});
		}
	</script>
<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-purchase_request");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
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
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        
        var table = document.getElementsByTagName('table')[0];
        var cloneTable = table.cloneNode(true);
        var thead = cloneTable.querySelector('thead');
        if (thead) {
            var rows = Array.from(thead.rows);
            rows.forEach(function(row) {
                var firstCell = row.cells[0];
                if (firstCell && firstCell.colSpan > 1 && firstCell.style.backgroundColor === 'rgb(58, 82, 106)') {
                    thead.removeChild(row);
                }
            });
        }

         var tableData = '<table border="1" cellpadding="0" cellspacing="0" width="100%" ' +
                    'class="tree table table-striped table-bordered" style="font-size:12px;">' +
                    cloneTable.innerHTML +
                    '</table>';
        //var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Center Wise Purchase</td>';
        heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
    
    function staffwiseprintPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        
        var table = document.getElementsByTagName('table')[1];
        var cloneTable = table.cloneNode(true);
        var thead = cloneTable.querySelector('thead');
        if (thead) {
            var rows = Array.from(thead.rows);
            rows.forEach(function(row) {
                var firstCell = row.cells[0];
                if (firstCell && firstCell.colSpan > 1 && firstCell.style.backgroundColor === 'rgb(58, 82, 106)') {
                    thead.removeChild(row);
                }
            });
        }

        var tableData = '<table border="1" cellpadding="0" cellspacing="0" width="100%" ' +
                    'class="tree table table-striped table-bordered" style="font-size:12px;">' +
                    cloneTable.innerHTML +
                    '</table>';
        //var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Center Wise Staff Wise Purchase</td>';
        heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    }
</script>
 <script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script>

<script>
//===================== Center Wise Purchase Export ============================
    $("#centerwiseexcel").click(function()
    {
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var TType = $("#TType :selected").val();
	    var ItemID = $("#ItemID :selected").val();
	    var CenterID = $("#Center :selected").val();
	    var FeildOfficer = $("#FeildOfficer :selected").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>Misc_Dashboard/ExportCenterWisePurchase",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, TType:TType,ItemID:ItemID,CenterID:CenterID,FeildOfficer:FeildOfficer},
            beforeSend: function () {
                $('#search1').css('display','block');
            },
            complete: function () {
                $('#search1').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
//=============== Center Wise Staff Wise Purchase ==============================
    $("#staffwiseexcel").click(function()
    {
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var TType = $("#TType :selected").val();
	    var ItemID = $("#ItemID :selected").val();
	    var CenterID = $("#Center :selected").val();
	    var FeildOfficer = $("#FeildOfficer :selected").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>Misc_Dashboard/ExportCenterWiseStaffWisePurchase",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, TType:TType,ItemID:ItemID,CenterID:CenterID,FeildOfficer:FeildOfficer},
            beforeSend: function () {
                $('#search1').css('display','block');
            },
            complete: function () {
                $('#search1').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
</script>  
</body>
</html>
