<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.highcharts-menu-item {
    font-size: 14px !important;
}

#table_Commision_List td:hover {
	cursor: pointer;
    }
    #table_Commision_List tr:hover {
	background-color: #ccc;
    }
    .table_Commision_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_Commision_List thead th { position: sticky; top: 0; z-index: 1; }
    .table_Commision_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
<div class="row">
	<div class="col-md-12 section-client-dashboard">
		<h3 id="greeting" class="no-mtop"></h3>
		<?php if(has_contact_permission('projects')) { ?>
			<div class="panel_s">
				<div class="panel-body">
					<h3 class="text-success projects-summary-heading no-mtop mbot15"><?php echo _l('projects_summary'); ?></h3>
					<div class="row">
						<?php get_template_part('projects/project_summary'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="row">
		    <div class="col-md-6">
		        <div class="panel_s">
        			<div class="panel-body">
    					    <?php
								$fy = $this->session->userdata('finacial_year');
								$fy_new  = $fy + 1;
								$lastdate_date = '20'.$fy_new.'-03-31';
								$firstdate_date = '20'.$fy_new.'-04-01';
								$curr_date = date('Y-m-d'); // e.g., "2025-05-13"
								$date_obj = new DateTime($curr_date); // Create DateTime object
								$formatted_date = $date_obj->format('d/m/Y'); // Format to "13/05/25"
								$LogInUser = $this->session->userdata('AccountID');
							?>
							<input type="hidden" name="ON_date" id="ON_date" value="<?php echo $formatted_date;?>">
							<input type="hidden" name="PartyID" id="PartyID" value="<?php echo $LogInUser;?>">
    						<div class="row">
    						    <span id="searchh12" style="display:none;">Please wait data loading.</span>
    						    <span id="searchh23" style="display:none;">Please wait exporting data.....</span>
    							<div>
    							    <div id="ItemQtyChartContainer" style="width: 100%; height: 400px;"></div>
    							</div>
    						</div>
        			</div>
        		</div>
		    </div>
		    
		    <div class="col-md-6">
		        <div class="panel_s">
        			<div class="panel-body">
    						<div class="row">
    						    <span id="searchh13" style="display:none;">Please wait data loading.</span>
    							<div>
    							    <div id="InwardQtyChartContainer" style="width: 100%; height: 400px;"></div>
    							</div>
    						</div>
        			</div>
        		</div>
		    </div>
		    <div class="clearfix"></div>
		    
		    <div class="col-md-6">
		        <div class="panel_s">
        			<div class="panel-body">
    						<div class="row">
    						    <span id="searchh14" style="display:none;">Please wait data loading.</span>
    							<div>
    							    <div id="LeanQtyChartContainer" style="width: 100%; height: 400px;"></div>
    							</div>
    						</div>
        			</div>
        		</div>
		    </div>
		    <div class="col-md-6">
		         <div class="panel_s">
		             <div class="panel-body">
        				<h3 class="projects-summary-heading no-mtop mbot15 text-center" style="color: #314e73;">Item And Rate Wise Commission Report</h3>
    						<div class="row">
    							<div class="col-md-12">
    								<span id="searchh" style="display:none;">Please wait data loading.</span>
    								<span id="searchh2" style="display:none;">Please wait exporting data.....</span>
    								<div class="commsion load_data">
    									
    								</div>
    							</div>
    						</div>
        			</div>
		         </div>
		    </div>
		    
		</div>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/drilldown.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/full-screen.js"></script>
		<?php hooks()->do_action('client_area_after_project_overview'); ?>
		<div class="panel_s">
			<?php
			if(has_contact_permission('invoices')){ ?>
				<div class="panel-body">
					<p class="bold"><?php echo _l('clients_quick_invoice_info'); ?></p>
					<?php //if(has_contact_permission('invoices')){ ?>
						<a href="<?php echo site_url('clients/statement'); ?>"><?php echo _l('view_account_statement'); ?></a>
					<?php// } ?>
					<hr />
					<?php get_template_part('invoices_stats'); ?>
					<hr />
					<div class="row">
						<div class="col-md-3">
							<?php if(count($payments_years) > 0){ ?>
								<div class="form-group">
									<select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" class="form-control" id="payments_year" name="payments_years" data-width="100%" onchange="total_income_bar_report();" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php foreach($payments_years as $year) { ?>
											<option value="<?php echo $year['year']; ?>"<?php if($year['year'] == date('Y')){echo 'selected';} ?>>
												<?php echo $year['year']; ?>
											</option>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
							<?php if(is_client_using_multiple_currencies()){ ?>
								<div id="currency" class="form-group mtop15" data-toggle="tooltip" title="<?php echo _l('clients_home_currency_select_tooltip'); ?>">
									<select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" class="form-control" name="currency">
										<?php foreach($currencies as $currency){
											$selected = '';
											if($currency['isdefault'] == 1){
												$selected = 'selected';
											}
											?>
											<option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['symbol']; ?> - <?php echo $currency['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="relative" style="max-height:400px;">
								<canvas id="client-home-chart" height="400" class="animated fadeIn"></canvas>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<script>
		var greetDate = new Date();
		var hrsGreet = greetDate.getHours();

		var greet;
		if (hrsGreet < 12)
			greet = "<?php echo _l('good_morning'); ?>";
		else if (hrsGreet >= 12 && hrsGreet <= 17)
			greet = "<?php echo _l('good_afternoon'); ?>";
		else if (hrsGreet >= 17 && hrsGreet <= 24)
			greet = "<?php echo _l('good_evening'); ?>";

		if(greet) {
			document.getElementById('greeting').innerHTML =
			'<b>' + greet + ' <?php echo $contact->firstname; ?>!</b>';
		}
	</script>
	
<script>
    $(document).ready(function () 
    {
        var on_date = $("#ON_date").val();
        var PartyID = $("#PartyID").val();
        var CenterID = ""; 
        var ItemGroup = ""; 
        AsOndateStock(on_date,CenterID, PartyID, ItemGroup);
        InwardQtyTransactionChart(on_date,CenterID, PartyID, ItemGroup);
        LeanQtyTransactionChart(on_date,CenterID, PartyID, ItemGroup);
        loadCommissionTable(CenterID, PartyID, ItemGroup);
    });
	    
    function LeanQtyTransactionChart(on_date,CenterID, PartyID, ItemGroup)
    {
        $.ajax({
            url: "<?php echo base_url(); ?>K1InventoryMaster/GetLeanTransactionChartData",
            dataType: "JSON",
            method: "POST",
            cache: false,
            data: {
                on_date: on_date,
                ItemGroup: ItemGroup,
                CenterID: CenterID,
                PartyID: PartyID
            },
            beforeSend: function () {
                $('#searchh14').show();
            },
            complete: function () {
                $('#searchh14').hide(); 
            },
            success: function (response) {
                if (!response || response.length === 0) {
                    alert("No data to display.");
                    return;
                }
    
                const chartData = [];
                const drilldownSeries = [];
    
                response.forEach(item => {
                    const itemName = item.ItemName || 'Unnamed';
                    const drilldownId = itemName.replace(/\s+/g, '_'); 
                    const totalQty = parseFloat(item.Qty || 0);
                    
                    chartData.push({
                        name: itemName,
                        y: totalQty,
                        drilldown: drilldownId
                    });
                    
                    if (item.CenterWiseData && Array.isArray(item.CenterWiseData)) {
                        const centerData = item.CenterWiseData.map(center => [
                            center.CenterName || 'Unknown',
                            parseFloat(center.Qty || 0)
                        ]);
    
                        drilldownSeries.push({
                            name: itemName + ' - Center-wise Qty',
                            id: drilldownId,
                            data: centerData
                        });
                    }
                });
                
                Highcharts.chart('LeanQtyChartContainer', {
                    chart: {
                        type: 'column',
                        events: {
                            drilldown: function (e) {
                                this.xAxis[0].setTitle({ text: 'Centers' });
                            },
                            drillup: function (e) {
                                this.xAxis[0].setTitle({ text: 'Items' });
                            }
                        }
                    },
                    title: {
                        text: 'Inward Transaction Chart'
                    },
                    xAxis: {
                        type: 'category',
                        title: {
                            text: 'Items',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Quantity',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    tooltip: {
                        pointFormat: 'Quantity: <b>{point.y}</b>'
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.0f}',
                                style: { fontSize: '12px', fontWeight: 'bold' }
                            }
                        }
                    },
                    series: [{
                        name: 'Items',
                        colorByPoint: true,
                        data: chartData
                    }],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            },
                            showFullPath: true
                        },
                        series: drilldownSeries
                    }
                });
            },
            error: function () {
                alert("Failed to load stock chart data.");
            }
        });
    }
    function InwardQtyTransactionChart(on_date,CenterID, PartyID, ItemGroup)
    {
        $.ajax({
            url: "<?php echo base_url(); ?>K1InventoryMaster/GetInwardTransactionChartData",
            dataType: "JSON",
            method: "POST",
            cache: false,
            data: {
                on_date: on_date,
                ItemGroup: ItemGroup,
                CenterID: CenterID,
                PartyID: PartyID
            },
            beforeSend: function () {
                $('#searchh13').show();
            },
            complete: function () {
                $('#searchh13').hide(); 
            },
            success: function (response) {
                if (!response || response.length === 0) {
                    alert("No data to display.");
                    return;
                }
    
                const chartData = [];
                const drilldownSeries = [];
    
                response.forEach(item => {
                    const itemName = item.ItemName || 'Unnamed';
                    const drilldownId = itemName.replace(/\s+/g, '_'); 
                    const totalQty = parseFloat(item.Qty || 0);
                    
                    chartData.push({
                        name: itemName,
                        y: totalQty,
                        drilldown: drilldownId
                    });
                    
                    if (item.CenterWiseData && Array.isArray(item.CenterWiseData)) {
                        const centerData = item.CenterWiseData.map(center => [
                            center.CenterName || 'Unknown',
                            parseFloat(center.Qty || 0)
                        ]);
    
                        drilldownSeries.push({
                            name: itemName + ' - Center-wise Qty',
                            id: drilldownId,
                            data: centerData
                        });
                    }
                });
                
                Highcharts.chart('InwardQtyChartContainer', {
                    chart: {
                        type: 'column',
                        events: {
                            drilldown: function (e) {
                                this.xAxis[0].setTitle({ text: 'Centers' });
                            },
                            drillup: function (e) {
                                this.xAxis[0].setTitle({ text: 'Items' });
                            }
                        }
                    },
                    title: {
                        text: 'Inward Transaction Chart'
                    },
                    xAxis: {
                        type: 'category',
                        title: {
                            text: 'Items',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Quantity',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    tooltip: {
                        pointFormat: 'Quantity: <b>{point.y}</b>'
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.0f}',
                                style: { fontSize: '12px', fontWeight: 'bold' }
                            }
                        }
                    },
                    series: [{
                        name: 'Items',
                        colorByPoint: true,
                        data: chartData
                    }],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            },
                            showFullPath: true
                        },
                        series: drilldownSeries
                    }
                });
            },
            error: function () {
                alert("Failed to load stock chart data.");
            }
        });
    }
    function AsOndateStock(on_date,CenterID, PartyID, ItemGroup)
    {
        $.ajax({
            url: "<?php echo base_url(); ?>K1InventoryMaster/GetAsondateStockChartData",
            dataType: "JSON",
            method: "POST",
            cache: false,
            data: {
                on_date: on_date,
                ItemGroup: ItemGroup,
                CenterID: CenterID,
                PartyID: PartyID
            },
            beforeSend: function () {
                $('#searchh12').show();
            },
            complete: function () {
                $('#searchh12').hide(); 
            },
            success: function (response) {
                if (!response || response.length === 0) {
                    alert("No data to display.");
                    return;
                }
    
                const chartData = [];
                const drilldownSeries = [];
    
                response.forEach(item => {
                    const itemName = item.ItemName || 'Unnamed';
                    const drilldownId = itemName.replace(/\s+/g, '_'); 
                    const totalQty = parseFloat(item.Qty || 0);
                    
                    chartData.push({
                        name: itemName,
                        y: totalQty,
                        drilldown: drilldownId
                    });
                    
                    if (item.CenterWiseData && Array.isArray(item.CenterWiseData)) {
                        const centerData = item.CenterWiseData.map(center => [
                            center.CenterName || 'Unknown',
                            parseFloat(center.Qty || 0)
                        ]);
    
                        drilldownSeries.push({
                            name: itemName + ' - Center-wise Qty',
                            id: drilldownId,
                            data: centerData
                        });
                    }
                });
                
                Highcharts.chart('ItemQtyChartContainer', {
                    chart: {
                        type: 'column',
                        events: {
                            drilldown: function (e) {
                                this.xAxis[0].setTitle({ text: 'Centers' });
                            },
                            drillup: function (e) {
                                this.xAxis[0].setTitle({ text: 'Items' });
                            }
                        }
                    },
                    title: {
                        text: 'As On Date Stock Chart'
                    },
                    xAxis: {
                        type: 'category',
                        title: {
                            text: 'Items',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Quantity',
                            style: { fontSize: '14px', fontWeight: 'bold' }
                        }
                    },
                    tooltip: {
                        pointFormat: 'Quantity: <b>{point.y}</b>'
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '{point.y:.0f}',
                                style: { fontSize: '12px', fontWeight: 'bold' }
                            }
                        }
                    },
                    series: [{
                        name: 'Items',
                        colorByPoint: true,
                        data: chartData
                    }],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            },
                            showFullPath: true
                        },
                        series: drilldownSeries
                    }
                });
            },
            error: function () {
                alert("Failed to load stock chart data.");
            }
        });
    }
    function loadCommissionTable(centername, vendor, ItemCode) 
    {
        $.ajax({
            url: "<?php echo base_url(); ?>K1InventoryMaster/GetCommisionData",
            dataType: "html",
            method: "POST",
            data: {centername: centername, filtervendor: vendor, filterItemCode: ItemCode},
            beforeSend: function () {
                $('#searchh').show();
                $('.load_data').hide();
            },
            complete: function () {
                $('.load_data').show();
                $('#searchh').hide();
            },
            success: function (data) {
                $('.load_data').html(data);
            },
            error: function () {
                $('.load_data').html('<p style="color:red;">Failed to load data.</p>');
            }
        });
    }
</script>
