<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    /* Add spacing around cards */
    .card {
	margin-bottom: 20px;
	border: none;
	border-radius: 12px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
	transition: transform 0.2s ease-in-out;
    }
	
    /* Hover effect */
    .card:hover {
	transform: translateY(-5px);
	box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
	
    /* Card content styling */
    .card-body {
	padding: 20px;
	background-color: #f8f9fa;
	border-radius: 12px;
    }
	
    /* Title styling */
    .card-title {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    text-align: center;  /* Correct way to center text */
	}
	
    /* Text styling */
    .card-text {
	color: #666;
	font-size: 14px;
    }
	
    /* Optional: add different colors for each card */
    .card:nth-child(1) .card-body { background-color: #e3f2fd; } /* Light Blue */
    .card:nth-child(2) .card-body { background-color: #fce4ec; } /* Light Pink */
    .card:nth-child(3) .card-body { background-color: #e8f5e9; } /* Light Green */
    .card:nth-child(4) .card-body { background-color: #fff3e0; } /* Light Orange */
	
	.widget-card {
	background: #fff;
	border-radius: 8px;
	padding: 20px;
	
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	text-align: center;
	}
	
	.widget-number {
	font-size: 36px;
	font-weight: bold;
	color: #007bff; /* Blue color */
	text-align: center; /* Moves the number 60px to the left */
	margin-bottom: 5px;
	}
	
	.widget-label {
	font-size: 16px;
	color: #666;
	}
	table {
	width: 100%;
	border-collapse: collapse;
	margin: 20px auto;
	font-family: Arial, sans-serif;
	box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
	
    th, td {
	padding: 12px 15px;
	text-align: center;
	border: 1px solid #ddd;
    }
	
    th {
	text-color: #4CAF50;
	color: black;
    }
	
	
	
    tr:hover {
	background-color: #f1f1f1;
    } 
	
    caption {
	caption-side: top;
	text-align: center;
	padding: 10px;
	font-size: 1.2em;
	font-weight: bold;
    }
	.top_stats_wrapper2 {
    padding: 5px 10px 5px 15px;
    background: #fff;
    border-radius: 3px;
    -webkit-box-shadow: 0 1px 15px 1px rgba(90, 90, 90, .08);
    box-shadow: 0 1px 15px 1px rgba(90, 90, 90, .08);
    border: 1px solid #dce1ef;
    margin-bottom: 15px;
	}
	
    
	.backButton {
	display: none;
	margin-top: 3px;
	margin-bottom: 10px;
	padding: 6px 12px;
	font-size: 14px;
	cursor: pointer;
	background-color: #6c757d;
	color: white;
	border: none;
	border-radius: 4px;
    }
    .backButton:hover {
	background-color: #5a6268;
    }
	
	
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s" style="margin-bottom: 8px;">
					<div class="panel-body">
						<nav aria-label="breadcrumb" >
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Kirti One Dashboard </b></li>
							</ol>
						</nav>
						<hr class="hr_style">
					</div>
				</div>
				<div class="row">
					<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3">
						<div class="top_stats_wrapper2">
							<p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo "Total Village"; ?>
							    <span class="pull-right"> <?php echo $Total_village; ?></span>
							</p>
							
						</div>
					</div>
					<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3">
						<div class="top_stats_wrapper2">
							<p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo "Total SKU(Item)"; ?>
							    <span class="pull-right"> <?php echo $Total_purchaseAmount; ?></span>
							</p>
							
						</div>
					</div>
					<div class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-3">
						<div class="top_stats_wrapper2">
							
							<p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo "Total Purchase AMT"; ?>
							    <span class="pull-right"> <?php echo $Total_purchaseAmount; ?></span>
							</p>
							
						</div>
					</div>
					<div class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-3">
						<div class="top_stats_wrapper2">
							<p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo "Total Sale Amount"; ?>
							    <span class="pull-right"> <?php echo $Total_saleAmount; ?></span>
							</p>
							
						</div>
					</div>
					
				</div>
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
							<div class="col-md-2">
								<label class="control-label">Chart Type</label>
								<select name="ChartType" id="ChartType" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
									<option value="Bar">Bar Chart</option>
									<option value="Pie">Pie Chart</option>
								</select>
							</div>
							<div class="col-md-9" style="margin-top:10px; text-align: right;">
								<button class="btn btn-info pull-left mleft5 search" style="margin-top: 10px;"
								id="search">Show</button>                      
							</div>
							
						</div>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
							<div  class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-6">
								<button id="purchaseBackButton" class="backButton" onclick="loadOriginalPurchaseChart()">⬅ Back</button>
								<div id="Purchasewise_chartReport"></div>
							</div>
							<div  class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-6">
								<button id="backButton" class="backButton" onclick="loadOriginalChart()">⬅ Back</button>
								<div id="Salewise_chartReport"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
							<div class="col-md-6" >
								<table class="table-striped table-bordered stock_position" style="width: 100%; font-size: 12px; border-collapse: collapse;">
									<thead>
										<tr>
											<td colspan="3" style="background-color:#3a526a; color: white; padding: 8px; font-weight: bold;">Center Wise Purchase And Sale</td>
										</tr>
										<tr>
											<th style="text-align: left; width: 20%; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Center Name</th>
											<th style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Purchase</th>
											<th style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Sale</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$centers = [];
											
											foreach ($summary as $row) {
												$centerId = $row->CenterID;
												
												if (!isset($centers[$centerId])) {
													$centers[$centerId] = [
													'CenterName' => $row->CenterName,
													'SALE' => 0,
													'Purchase' => 0
													];
												}
												
												$centers[$centerId][$row->TType2] = $row->TotalAmt;
											}
											
										?>
										<?php foreach ($centers as $row): ?>
										<tr>
											<td  style="text-align: left; width: 50%; padding: 4px 8px; line-height: 1.2;"><?= $row['CenterName'] ?></td>
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;"><?= number_format($row['Purchase'], 2) ?></td>
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;"><?= number_format($row['SALE'], 2) ?></td>
										</tr>
										<?php 
											$totalPurchase += $row['Purchase'];
											$totalSale += $row['SALE'];
										?>
										
										<?php endforeach; ?>
										<tr>
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Total</td>
											<td style="text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">
												<?= number_format($totalPurchase, 2) ?>
											</td>
											<td style="text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">
												<?= number_format($totalSale, 2) ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							
							<div  class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-6">
								
								<div id="CenterWiseSaleandPurechaseChartReport"></div>
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
							<div class="col-md-5" > 
								<table class="table-striped table-bordered stock_position" style="width: 100%; font-size: 12px; border-collapse: collapse;">
									<thead>
										<tr>
											<td colspan="3" style="background-color:#3a526a; color: white; padding: 8px; font-weight: bold;">Top 5 Selling Item</td>
										</tr>
										<tr>
											<th style="width: 2%; text-align: left; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Sr.no</th>
											<th style="text-align: left; width: 20%; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Item Name</th>
											<th style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Amount</th>
											
										</tr>
									</thead>
									<tbody>
										<?php 
											$Items = [];
											$i="1"; 
											foreach ($Top5SellingItem as $row) {
												$productID = $row->ProductID;
												
												if (!isset($products[$productID])) {
													$products[$productID] = [
													'ProductName' => $row->ProductName,
													'TotalAmt' => $row->TotalAmt,
													
													];
												}
												
												$products[$productID][$row->TType2] = $row->TotalAmt;
											}
											
										?>
										<?php foreach ($products as $row): ?>
										<tr>
											<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;"><?= $i ?></td>
											<td  style="text-align: left; width: 50%; padding: 4px 8px; line-height: 1.2;"><?= $row['ProductName'] ?></td>
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;"><?= number_format($row['TotalAmt'], 2) ?></td>
											
										</tr>
										<?php 
											//	$totalPurchase += $row['Purchase'];
											
										?>
										
										<?php 
											$i++;
										endforeach; ?>
										
									</tbody>
								</table>
							</div>
							<div  class="quick-stats-invoices col-xs-7 col-md-7 col-sm-7 col-lg-7">
								<button id="backTop5SellingButton" class="backButton" onclick="loadOriginalTop5SellingItemChart()">⬅ Back</button>
								<div id="Top5SellingItem_ChartReport"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
							<div class="col-md-5" >
								<table class="table-striped table-bordered stock_position" style="width: 100%; font-size: 12px; border-collapse: collapse;">
									<thead>
										<tr>
											<td colspan="3" style="background-color:#3a526a; color: white; padding: 8px; font-weight: bold;">Top 5 Purchase Item</td>
										</tr>
										<tr>
											<th style="width: 2%; text-align: left; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Sr.no</th>
											<th style="text-align: left; width: 20%; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Item Name</th>
											<th style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Amount</th>
											
										</tr>
									</thead>
									<tbody>
										<?php 
											$Items = [];
											$i="1"; 
											
											foreach ($Top5PurchaseItem as $row) {
												$productIDs = $row->ProductID;
												
												if (!isset($product[$productIDs])) {
													$product[$productIDs] = [
													'Pu_name' => $row->Pu_name,
													'TotalPuAmt' => $row->TotalPuAmt,
													
													];
												}
												
												$product[$productIDs][$row->TType2] = $row->TotalPuAmt;
											}
											
										?>
										<?php foreach ($product as $row): ?>
										<tr>
											<td  style="text-align: left; width: 3%; padding: 4px 8px; line-height: 1.2;"><?= $i ?></td>
											<td  style="text-align: left; width: 50%; padding: 4px 8px; line-height: 1.2;"><?= $row['Pu_name'] ?></td>
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;"><?= number_format($row['TotalPuAmt'], 2) ?></td>
											
										</tr>
										<?php 
											//	$totalPurchase += $row['Purchase'];
											
										?>
										
										<?php 
											$i++;
										endforeach; ?>
										
									</tbody>
								</table>
							</div>
							
							<div  class="quick-stats-invoices col-xs-7 col-md-7 col-sm-7 col-lg-7">
								<button id="backTop5PurchaseButton" class="backButton" onclick="loadOriginalTop5PurchaseItemChart()">⬅ Back</button>
								<div id="Top5PurchaseItem_ChartReport"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6" >
								<table class="table-striped table-bordered stock_position" style="width: 100%; font-size: 12px; border-collapse: collapse;">
									<thead>
										<tr>
											<td colspan="3" style="background-color:#3a526a; color: white; padding: 8px; font-weight: bold;">Top 5 Highest Stock (Inventory)</td>
										</tr>
										<tr>
											<th style="text-align: left; width: 20%; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Item Name</th>
											
											<th style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2; font-weight: bold;">Total Qty
											</th>
										</tr>
									</thead>
									<tbody>
										
										<?php foreach ($Top5HighStockItem as $row): ?>
										<tr>
											<td  style="text-align: left; width: 50%; padding: 4px 8px; line-height: 1.2;"><?= htmlspecialchars($row['ProductName']) ?></td>
											
											<td style="width: 15%; text-align: right; padding: 4px 8px; line-height: 1.2;"> <?= number_format((float)$row['BalanceQty'], 2) ?></td>
										</tr>
										
										
										<?php endforeach; ?>
										
									</tbody>
								</table>
							</div>
							
							<div  class="quick-stats-invoices col-xs-6 col-md-6 col-sm-6 col-lg-6">
								<button id="backTop5Button" class="backButton" onclick="loadOriginalTop5Chart()">⬅ Back</button>
								<div id="Top5HighStock_ChartReport"></div>
							</div>
							
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	
	
	
	<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
	<script>
		
		$(document).ready(function () {
			Purchasewise_chartReport();
			Salewise_chartReport();
			Top5HighStockItem_ChartReport();
			Top5SellingItem_ChartReport();
			Top5PurchaseItem_ChartReport();
			CenterWisePurchaseAndSaleChartReport();
		});
		$('#search').on('click',function(){
			Purchasewise_chartReport('Purchasewise_chartReport', '', 'Center Wise Purchase Amount');
			Salewise_chartReport('Salewise_chartReport', '', 'Center Wise Sale Amount');
			Top5HighStockItem_ChartReport('Top5HighStockItem_ChartReport', '', 'Top 5 Highest Stock');
			Top5SellingItem_ChartReport('Top5SellingItem_ChartReport', '', 'Top 5 Selling Item');
			Top5PurchaseItem_ChartReport('Top5PurchaseItem_ChartReport', '', 'Top 5 Purchase Item');
			CenterWisePurchaseAndSaleChartReport('CenterWisePurchaseAndSaleChartReport', '', 'Center-wise Purchase and Sale');
		});
		
		// GLOBAL FLAGS
		let originalChartData = [];
		let currentChartType = '';
		let currentView = '';
		let currentDrillLevel = "center"; // "center" → "village" → "item"
		let drillStack = [];
		
		function Salewise_chartReport() {
			var ChartType = $("#ChartType").val();
			
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/Sale_wise_chart",
				method: "POST",
				dataType: "json",
				data: { ChartType: ChartType },
				success: function (response) {
					originalChartData = response.ChartData;
					currentChartType = ChartType;
					document.getElementById('backButton').style.display = 'none';
					
					if (ChartType == "Bar") {
						Highcharts.chart('Salewise_chartReport', {
							chart: { type: 'column' },
							title: { text: 'Sale Amount by Center' },
							xAxis: {
								type: 'category',
								labels: {
									autoRotation: [-45, -90],
									style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
								}
							},
							yAxis: { min: 0, title: { text: 'Amount' } },
							series: [{
								name: 'Sale',
								data: response.ChartData,
								cursor: 'pointer',
								point: { events: { click: pointClickHandler } }
							}]
						});
						} else if (ChartType == "Pie") {
						Highcharts.chart("Salewise_chartReport", {
							chart: { type: 'pie' },
							title: { text: 'Center Wise Sale Amount' },
							series: [{
								name: 'Sale',
								data: response.ChartData,
								cursor: 'pointer',
								point: { events: { click: pointClickHandler } }
							}]
						});
					}
				}
			});
		}
		function pointClickHandler() {
			if (currentDrillLevel === 'center') {
				var CenterID = this.CenterID;
				const center = this.name;
				
				drillStack = [{ CenterID }]; // Reset stack
				currentDrillLevel = 'village';
				document.getElementById('backButton').style.display = 'inline-block';
				
				fetch('<?php echo admin_url(); ?>K1Dashboard/VillageWisedatabyCenterID', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ CenterID, ChartType: currentChartType })
				})
				.then(response => response.json())
				
				.then(response => renderChart(response.ChartData, center, 'Village Wise Sale Amount'));
				
				
				} else if (currentDrillLevel == 'village') {
				const VillageID = this.VillageName;
				const CenterIDs = this.CenterID;
				const village = this.name;
				
				drillStack.push({ VillageID });
				currentDrillLevel = 'Item';
				
				fetch('<?php echo admin_url(); ?>K1Dashboard/ItemWiseSaleByVillage', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ VillageID, CenterIDs, ChartType: currentChartType })
				})
				.then(response => response.json())
				.then(response => renderChart(response.ChartData, village, 'Item Wise Sale Amount'));
				
				
			}
		}
		function renderChart(chartData, titleName, chartTitle) {
			if (currentChartType === "Bar") {
				Highcharts.chart("Salewise_chartReport", {
					chart: { type: 'column' },
					title: { text: chartTitle },
					subtitle: { text: '<b>' + titleName + '</b>' },
					xAxis: {
						type: 'category',
						labels: {
							autoRotation: [-45, -90],
							style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
						}
					},
					yAxis: { min: 0, title: { text: 'Amount' } },
					series: [{
						name: "Sale",
						colorByPoint: true,
						data: chartData,
						cursor: 'pointer',
						point: { events: { click: pointClickHandler } },
						dataLabels: {
							enabled: true,
							rotation: -90,
							color: '#FFFFFF',
							inside: true,
							verticalAlign: 'top',
							format: '{point.y:.1f}',
							style: { fontSize: '13px', fontFamily: 'Verdana, sans-serif' }
							
						}
					}]
				});
				} else if (currentChartType === "Pie") {
				Highcharts.chart("Salewise_chartReport", {
					chart: { type: 'pie' },
					title: { text: chartTitle },
					subtitle: { text: '<b>' + titleName + '</b>' },
					series: [{
						name: "Sale",
						colorByPoint: true,
						data: chartData,
						cursor: 'pointer',
						point: { events: { click: pointClickHandler } },
						dataLabels: {
							enabled: true,
							format: '{point.y:.1f}%',
							style: { fontSize: '13px', fontFamily: 'Verdana, sans-serif' }
						}
					}]
				});
			}
		}
		function loadOriginalChart() {
			currentDrillLevel = 'center';
			drillStack = [];
			
			if (currentChartType === "Bar") {
				Highcharts.chart('Salewise_chartReport', {
					chart: { type: 'column' },
					title: { text: 'Sale Amount by Center' },
					xAxis: {
						type: 'category',
						labels: {
							autoRotation: [-45, -90],
							style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
						}
					},
					yAxis: { min: 0, title: { text: 'Amount' } },
					series: [{
						name: 'Sale',
						data: originalChartData,
						cursor: 'pointer',
						point: { events: { click: pointClickHandler } }
					}]
				});
				} else if (currentChartType === "Pie") {
				Highcharts.chart("Salewise_chartReport", {
					chart: { type: 'pie' },
					title: { text: 'Center Wise Sale Amount' },
					series: [{
						name: 'Sale',
						data: originalChartData,
						cursor: 'pointer',
						point: { events: { click: pointClickHandler } }
					}]
				});
			}
			
			document.getElementById('backButton').style.display = 'none';
		}
		
		// PURCHASE CHART
		let originalPurchaseChartData = [];
		let currentPurchaseChartType = '';
		let currentView3 = '';
		
		function Purchasewise_chartReport() {
			var ChartType = $("#ChartType").val();
			currentPurchaseChartType = ChartType;
			
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/Purchase_wise_chart",
				method: "POST",
				dataType: "json",
				data: { ChartType: ChartType },
				success: function (response) {
					originalPurchaseChartData = response.ChartData;
					document.getElementById('purchaseBackButton').style.display = 'none';
					
					if (ChartType === "Bar") {
						Highcharts.chart('Purchasewise_chartReport', {
							chart: { type: 'column' },
							title: { text: 'Purchase Amount by Center' },
							xAxis: {
								type: 'category',
								labels: {
									autoRotation: [-45, -90],
									style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
								}
							},
							yAxis: { min: 0, title: { text: 'Amount' } },
							series: [{
								name: 'Purchase',
								data: response.ChartData,
								cursor: 'pointer',
								point: { events: { click: purchasePointClickHandler } }
							}]
						});
						} else if (ChartType === "Pie") {
						Highcharts.chart('Purchasewise_chartReport', {
							chart: { type: 'pie' },
							title: { text: 'Center Wise Purchase Amount' },
							series: [{
								name: 'Purchase',
								data: response.ChartData,
								cursor: 'pointer',
								point: { events: { click: purchasePointClickHandler } }
							}]
						});
					}
				}
			});
		}
		
		function purchasePointClickHandler() {
			const CenterID = this.CenterID;
			const center = this.name;
			
			currentView3 = 'purchase';
			document.getElementById('purchaseBackButton').style.display = 'inline-block';
			
			fetch('<?php echo admin_url(); ?>K1Dashboard/centerby_ItemPurchase_wise_chart', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ CenterID: CenterID, ChartType: currentPurchaseChartType })
			})
			.then(response => response.json())
			.then(response => {
				if (currentPurchaseChartType === "Bar") {
					Highcharts.chart("Purchasewise_chartReport", {
						chart: { type: 'column' },
						title: { text: 'Center Wise Purchase Amount' },
						subtitle: { text: '<b>' + center + '</b>' },
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
								style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
							}
						},
						yAxis: { min: 0, title: { text: 'Amount' } },
						series: [{
							name: "Items",
							colorByPoint: true,
							data: response.ChartData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}',
								style: { fontSize: '13px', fontFamily: 'Verdana, sans-serif' }
							}
						}]
					});
					} else if (currentPurchaseChartType === "Pie") {
					Highcharts.chart("Purchasewise_chartReport", {
						chart: { type: 'pie' },
						title: { text: 'Center Wise Purchase Amount' },
						subtitle: { text: '<b>' + center + '</b>' },
						series: [{
							colorByPoint: true,
							data: response.ChartData,
							dataLabels: {
								enabled: true,
								format: '{point.y:.1f}%',
								style: { fontSize: '13px', fontFamily: 'Verdana, sans-serif' }
							}
						}]
					});
				}
			});
		}
		
		function loadOriginalPurchaseChart() {
			if (currentPurchaseChartType === "Bar") {
				Highcharts.chart('Purchasewise_chartReport', {
					chart: { type: 'column' },
					title: { text: 'Purchase Amount by Center' },
					xAxis: {
						type: 'category',
						labels: {
							autoRotation: [-45, -90],
							style: { fontSize: '11px', fontFamily: 'Verdana, sans-serif' }
						}
					},
					yAxis: { min: 0, title: { text: 'Amount' } },
					series: [{
						name: 'Purchase',
						data: originalPurchaseChartData,
						cursor: 'pointer',
						point: { events: { click: purchasePointClickHandler } }
					}]
				});
				} else if (currentPurchaseChartType === "Pie") {
				Highcharts.chart("Purchasewise_chartReport", {
					chart: { type: 'pie' },
					title: { text: 'Center Wise Purchase Amount' },
					series: [{
						name: 'Purchase',
						data: originalPurchaseChartData,
						cursor: 'pointer',
						point: { events: { click: purchasePointClickHandler } }
					}]
				});
			}
			
			document.getElementById('purchaseBackButton').style.display = 'none';
		}
		
		
		var originalTOp5ChartData = null;
		
		function Top5HighStockItem_ChartReport() {
			var ChartType = $("#ChartType").val();
			
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/Top5HighStockItem_ChartReport",
				dataType: "JSON",
				method: "POST",
				data: { ChartType: ChartType },
				success: function (response) {
					
					
					// Store the original data
					originalTOp5ChartData = {
						options: getCommonOptions(ChartType, response.ChartData),
						ChartType: ChartType
					};
					
					// Show the back button if we're not already showing the original
					$("#backButton").hide();
					
					// Draw the chart
					Highcharts.chart("Top5HighStock_ChartReport", originalTOp5ChartData.options);
				}
			});
		}
		
		// Helper function to create common options
		function getCommonOptions(ChartType, chartData) {
			return {
				chart: {
					type: (ChartType === "Pie") ? 'pie' : 'column'
				},
				title: {
					text: 'Top 5 Highest Stock (Inventory)'
				},
				subtitle: {
					text: ''
				},
				xAxis: {
					type: 'category',
					labels: {
						autoRotation: [-45, -90],
						style: {
							fontSize: '11px',
							fontFamily: 'Verdana, sans-serif'
						}
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Top 5 Highest Stock (Inventory)'
					}
				},
				legend: {
					enabled: false
				},
				tooltip: {
					pointFormat: (ChartType === "Pie") 
					? 'Center Wise Item Stock Qty: <b>{point.y:.1f} %</b>' 
					: 'Stock Qty: <b>{point.y:.1f}</b>'
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									var ProductID = this.ProductID;
									loadCenterWiseChart(this.ProductID, this.name);
								}
							}
						}
					}
				},
				series: [{
					colorByPoint: true,
					groupPadding: 0,
					maxPointWidth: 50,
					data: chartData,
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
		}
		
		function loadCenterWiseChart(ProductID, name) {
			var ChartType = $("#ChartType").val();
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/CenterWiseStockChart",
				method: "POST",
				data: { ProductID: ProductID, ChartType: ChartType},
				dataType: "json",
				success: function (response) {
					var options;
					
					if (ChartType === "Bar") {
						options = {
							chart: {
								type: 'column'
							},
							title: {
								text:'Top 5 Highest Stock (Inventory)'
								// text: 'Center: ' + name 
							},
							xAxis: {
								type: 'category'
							},
							yAxis: {
								title: {
									text: 'Item Qty'
								}
							},
							series: [{
								name: 'Items',
								colorByPoint: true,
								data: response.ChartData
							}]
						};
						} else if (ChartType === "Pie") {
						options = {
							chart: { type: 'pie' },
							title:  { text: 'Top 5 Highest Stock (Inventory)' },
							// title: { text: 'Center: ' + name },
							series: [{
								name: 'Items',
								colorByPoint: true,
								data: response.ChartData,
								dataLabels: {
									enabled: true,
									format: '{point.y:.1f}%',
									style: { 
										fontSize: '13px', 
										fontFamily: 'Verdana, sans-serif' 
									}
								}
							}]
						};
					}
					
					Highcharts.chart("Top5HighStock_ChartReport", options);
					
					// Show the back button when viewing drill-down data
					$("#backTop5Button").show();
				}
			});
		}
		
		// Function to load the original chart
		function loadOriginalTop5Chart() {
			if (originalTOp5ChartData) {
				Highcharts.chart("Top5HighStock_ChartReport", originalTOp5ChartData.options);
				$("#backTop5Button").hide();
			}
		}
		
		
		//=========== Top 5 Selling Item Chart Report =========================		
		let originalTop5SellingChartOptions = null;
		
		function Top5SellingItem_ChartReport() {
			var ChartType = $("#ChartType").val();
			
			$("#backTop5SellingButton").hide();
			
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/Top5HighSellingItemChartReport",
				dataType: "JSON",
				method: "POST",
				data: { ChartType: ChartType },
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
							text: 'Top 5 Selling Item'
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
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											loadSellingCenterWiseChart(this.ProductID, this.name);
										}
									}
								}
							},
							isPie: {
								allowPointSelect: true,
								cursor: 'pointer',
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
							}
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
					Highcharts.chart("Top5SellingItem_ChartReport", options);
				}
			});
		}
		
		function loadSellingCenterWiseChart(ProductID, name) {
			var ChartType = $("#ChartType").val();
			const isPie = (ChartType === "Pie");
			
			$("#backTop5SellingButton").show();
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/ItemWiseSaleCenterChart",
				method: "POST",
				dataType: "json",
				data: {
					ProductID: ProductID,
					ChartType: ChartType
				},
				success: function (response) {
					if (!response.ChartData || response.ChartData.length === 0) {
						alert("No center-wise data to display.");
						return;
					}
					
					if (isPie) {
						response.ChartData = response.ChartData.map(item => ({
							name: item.name || item.label || "Unnamed",
							y: parseFloat(item.y || item.value || 0)
						}));
					}
					
					const options = {
						chart: {
							type: isPie ? 'pie' : 'column'
						},
						title: {
							text: 'Center-wise Sale for ' + name
						},
						xAxis: !isPie ? {
							type: 'category'
						} : undefined,
						yAxis: !isPie ? {
							title: {
								text: 'Amount'
							}
						} : undefined,
						tooltip: {
							pointFormat: isPie
							? '{point.name}: {point.y:.1f}%'
							: 'Amount: {point.y:.1f}'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.name}: {point.y:.1f}%',
									style: {
										fontSize: '13px',
										fontFamily: 'Verdana, sans-serif'
									}
								}
							},
							column: {
								dataLabels: {
									enabled: true,
									format: '{point.y:.1f}',
									style: {
										fontSize: '13px',
										fontFamily: 'Verdana, sans-serif'
									}
								}
							}
						},
						series: [{
							name: 'Centers',
							colorByPoint: true,
							data: response.ChartData
						}]
					};
					
					Highcharts.chart("Top5SellingItem_ChartReport", options);
				}
			});
		}
		
		function loadOriginalTop5SellingItemChart() { 
			if (originalTop5SellingChartOptions) {
				Highcharts.chart("Top5SellingItem_ChartReport", originalTop5SellingChartOptions);
				$("#backTop5SellingButton").hide(); // Hide back after restoring original
			}
		}
		
		
		
		//=========== Top 5 Purchase Item Chart Report =========================		
		let originalTop5PurchaseChartOptions = null;
		function Top5PurchaseItem_ChartReport() {
			var ChartType = $("#ChartType").val();
			$("#backTop5PurchaseButton").hide();
			
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/Top5PurchaseItemChartReport",
				dataType: "JSON",
				method: "POST",
				data: { ChartType: ChartType },
				success: function (response) {
					
					// Add ProductID to Pie Data Points (if needed for drilldown)
					if (ChartType === "Pie") {
						response.ChartData.forEach(function (item) {
							if (typeof item.ProductID === 'undefined') {
								item.ProductID = item.id || 0; // add this only if backend skips it
							}
						});
					}
					
					const Pie = (ChartType === "Pie");
					
					let options = {
						chart: {
							type: Pie ? 'pie' : 'column'
						},
						title: {
							text: 'Top 5 Purchase Item'
						},
						xAxis: !Pie ? {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
								style: {
									fontSize: '11px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						} : undefined,
						yAxis: !Pie ? {
							min: 0,
							title: {
								text: 'Purchase Amount'
							}
						} : undefined,
						legend: { enabled: false },
						tooltip: {
							pointFormat: Pie
							? '<b>{point.y:.1f}%</b>'
							: 'Purchase Amount: <b>{point.y:.1f}</b>'
						},
						plotOptions: {
							series: {
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											loadPurchaseCenterWiseChart(this.ProductID, this.name);
										}
									}
								}
							},
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
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
							}
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
					originalTop5PurchaseChartOptions = options;
					Highcharts.chart("Top5PurchaseItem_ChartReport", options);
				}
			});
		}
		
		function loadPurchaseCenterWiseChart(ProductID, name) {
			var ChartType = $("#ChartType").val();
			const Pie = (ChartType === "Pie");
			$("#backTop5PurchaseButton").show();
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/ItemWisePurchaseCenterChart",
				method: "POST",
				dataType: "json",
				data: {
					ProductID: ProductID,
					ChartType: ChartType
				},
				success: function (response) {
					const options = {
						chart: {
							type: Pie ? 'pie' : 'column'
						},
						title: {
							text: 'Center-wise Purchase for ' + name
						},
						xAxis: !Pie ? { type: 'category' } : undefined,
						yAxis: !Pie ? {
							title: {
								text: 'Amount'
							}
						} : undefined,
						tooltip: {
							pointFormat: Pie 
							? '<b>{point.y:.1f}%</b><br/>Amount: {point.amount}' 
							: '<b>{point.y:.1f}</b>'
						},
						series: [{
							name: 'Centers',
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
					
					// Always render in same container
					Highcharts.chart("Top5PurchaseItem_ChartReport", options);
				}
			});
		}
		
		
		function loadOriginalTop5PurchaseItemChart() { 
			if (originalTop5PurchaseChartOptions) {
				Highcharts.chart("Top5PurchaseItem_ChartReport", originalTop5PurchaseChartOptions);
				$("#backTop5PurchaseButton").hide(); // Hide back after restoring original
			}
		}
		
		function CenterWisePurchaseAndSaleChartReport() {
			$.ajax({
				url: "<?php echo admin_url(); ?>K1Dashboard/CenterWisePurchaseAndSaleChartRepor",  
				dataType: "JSON",
				method: "POST",
		success: function (response) {
			// console.log(response);
			let parsed = typeof response === 'string' ? JSON.parse(response) : response;
			let chartData = parsed.ChartData;
			
			let categories = [];
			let saleData = [];
			let purchaseData = [];
			
			chartData.forEach(function (center) {
				categories.push(center.name);
				
				let sale = (center.data || []).find(d => d.name === 'Sale');
				let purchase = (center.data || []).find(d => d.name === 'Purchase');
				
				saleData.push({
					y: sale ? sale.y : 0,
					color: sale ? sale.color : '#7cb5ec'
				});
				
				purchaseData.push({
					y: purchase ? purchase.y : 0,
					color: purchase ? purchase.color : '#434348'
				});
			});
			
			Highcharts.chart('CenterWiseSaleandPurechaseChartReport', {
				chart: { type: 'column' },
				title: { text: 'Center-wise Purchase and Sale' },
				xAxis: {
					categories: categories,
					crosshair: true,
					title: { text: 'Centers' }
				},
				yAxis: {
					min: 0,
					title: { text: 'Amount' }
				},
				tooltip: {
					shared: true,
					valuePrefix: '₹'
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [
				{ name: 'Sale', data: saleData },
				{ name: 'Purchase', data: purchaseData }
				]
			});
		}
			});
		}
	</script>
