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
				<div class="row">
				    <div class="col-md-6">
				        <div class="panel_s">
        				    <div class="panel-body">
        					    <div class="row">
                                    <div class="col-md-3 col-sm-3">
                                        <label for="PurchFilterType">Purchase Filter</label>
                                        <select id="PurchFilterType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="CENTERWISE">Center Wise</option>
                                            <option value="ITEMWISE">Item Wise</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <label for="PurchResultCount">Top By</label>
                                        <select id="PurchResultCount" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="5">Top 5</option>
                                            <option value="10">Top 10</option>
                                            <option value="20">Top 20</option>
                                            <option value="30">Top 30</option>
                                            <option value="40">Top 40</option>
                                            <option value="50">Top 50</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
        								<label class="control-label" for="PurchBrandList">Brand List</label>
        								<select name="PurchBrandList" id="PurchBrandList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($PurchBrandList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["BrandName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
        							<div class="col-md-3">
        								<label class="control-label" for="PurchCategoryList">Category List</label>
        								<select name="PurchCategoryList" id="PurchCategoryList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($PurchCategoryList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["SubcategoryName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
                                    <div class="col-md-3">
        								<label class="control-label" for="PurchChartType">Chart Type</label>
        								<select name="PurchChartType" id="PurchChartType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        									<option value="column">Bar Chart</option>
        									<option value="pie">Pie Chart</option>
        								</select>
        							</div>
        							
                                    <div class="col-md-2 col-sm-2 d-flex align-items-end" style="margin-top: 20px;">
                                        <button class="btn btn-primary w-100" onclick="PurchaseChartReport()">Apply</button>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-12">
                                        <div id="Purchasewise_chartReport"></div>
                                    </div>
        			            </div>
        			        </div>
        			    </div>
				    </div>
				    
    			    <div class="col-md-6">
    			        <div class="panel_s">
        				    <div class="panel-body">
        					    <div class="row">
                                    <div class="col-md-3">
                                        <label for="SalesFilterType">Filter By</label>
                                        <select id="SalesFilterType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="CENTERWISE">Center Wise</option>
                                            <option value="ITEMWISE">Item Wise</option>
                                            <option value="VILLAGEWISE">Village Wise</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <label for="SalesResultCount">Top By</label>
                                        <select id="SalesResultCount" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="5">Top 5</option>
                                            <option value="10">Top 10</option>
                                            <option value="20">Top 20</option>
                                            <option value="30">Top 30</option>
                                            <option value="40">Top 40</option>
                                            <option value="50">Top 50</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
        								<label class="control-label">Brand List</label>
        								<select name="SalesBrandList" id="SalesBrandList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($BrandList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["BrandName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
        							<div class="col-md-3">
        								<label class="control-label">Category List</label>
        								<select name="SalesCategoryList" id="SalesCategoryList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($CategoryList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["SubcategoryName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
                                    <div class="col-md-3">
        								<label class="control-label">Chart Type</label>
        								<select name="SalesChartType" id="SalesChartType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        									<option value="column">Bar Chart</option>
        									<option value="pie">Pie Chart</option>
        								</select>
        							</div>
                                            
                                    <div class="col-md-2 col-sm-2 d-flex align-items-end" style="margin-top: 20px;">
                                        <button class="btn btn-primary w-100" onclick="SalesChartReport()">Apply</button>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-m-12">
                                        <div id="Salewise_chartReport"></div>
                                    </div>
        			            </div>
        			        </div>
        			    </div>
    			    </div>
    			    
    			    <div class="col-md-6">
    			        <div class="panel_s">
        				    <div class="panel-body">
        					    <div class="row">
                                    <div class="col-md-3 col-sm-3">
                                        <label for="HighStockResultCount">Top By</label>
                                        <select id="HighStockResultCount" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="5">Top 5</option>
                                            <option value="10">Top 10</option>
                                            <option value="20">Top 20</option>
                                            <option value="30">Top 30</option>
                                            <option value="40">Top 40</option>
                                            <option value="50">Top 50</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
        								<label for="HighStockBrandList" class="control-label">Brand List</label>
        								<select name="HighStockBrandList" id="HighStockBrandList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($BrandList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["BrandName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
        							<div class="col-md-3">
        								<label for="HighStockCategoryList" class="control-label">Category List</label>
        								<select name="HighStockCategoryList" id="HighStockCategoryList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($CategoryList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["SubcategoryName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
                                    <div class="col-md-3">
        								<label class="control-label">Chart Type</label>
        								<select name="HighStockChartType" id="HighStockChartType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        									<option value="column">Bar Chart</option>
        									<option value="pie">Pie Chart</option>
        								</select>
        							</div>        
                                    <div class="col-md-2 col-sm-2 d-flex align-items-end" style="margin-top: 20px;">
                                        <button class="btn btn-primary w-100" onclick="HighStockInventory()">Apply</button>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-m-12">
                                        <div id="HighStockInventoryChartReport"></div>
                                    </div>
        			            </div>
        			        </div>
        			    </div>
    			    </div>
    			    
    			    <div class="col-md-6">
    			        <div class="panel_s">
        				    <div class="panel-body">
        					    <div class="row">
        					        
        					         <div class="col-md-3 col-sm-3">
                                        <label for="SalesPurchResultCount">Top By</label>
                                        <select id="SalesPurchResultCount" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="5">Top 5</option>
                                            <option value="10">Top 10</option>
                                            <option value="20">Top 20</option>
                                            <option value="30">Top 30</option>
                                            <option value="40">Top 40</option>
                                            <option value="50">Top 50</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="SalesPurchFilterType">Filter By</label>
                                        <select id="SalesPurchFilterType" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
                                            <option value="CENTERWISE">Center Wise</option>
                                            <option value="ITEMWISE">Item Wise</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
        								<label for="SalesPurchBrandList" class="control-label">Brand List</label>
        								<select name="SalesPurchBrandList" id="SalesPurchBrandList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($BrandList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["BrandName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
        							<div class="col-md-3">
        								<label for="SalesPurchCategoryList" class="control-label">Category List</label>
        								<select name="SalesPurchCategoryList" id="SalesPurchCategoryList" class="selectpicker" data-none-selected-text="None selected" data-width="100%" data-live-search="true" tabindex="-98">
        								    <option value="">None selected</option>
        								<?php
        									foreach($CategoryList as $key=>$val){
        								?>
        								        <option value="<?php echo $val["id"];?>"><?php echo $val["SubcategoryName"];?></option>
        								<?php
        									}
        								?>
        								</select>
        							</div>
                                       
                                    <div class="col-md-2 col-sm-2 d-flex align-items-end" style="margin-top: 20px;">
                                        <button class="btn btn-primary w-100" onclick="SaleVsPurchChartReport()">Apply</button>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-m-12">
                                        <div id="SaleVsPurchChartReport"></div>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/full-screen.js"></script>
<?php init_tail(); ?>
<script>
	$(document).ready(function () {
	    SalesChartReport();
		PurchaseChartReport();
		HighStockInventory();
		/*Top5SellingItem_ChartReport();
		Top5PurchaseItem_ChartReport();*/
		SaleVsPurchChartReport();
	});
	$('#search').on('click',function(){
	    SalesChartReport();
		PurchaseChartReport();
		HighStockInventory();
		/*Top5SellingItem_ChartReport('Top5SellingItem_ChartReport', '', 'Top 5 Selling Item');
		Top5PurchaseItem_ChartReport('Top5PurchaseItem_ChartReport', '', 'Top 5 Purchase Item');*/
		SaleVsPurchChartReport('SaleVsPurchChartReport', '', 'Center Wise Sale and Purchase');
	});
		
	// Sales Chart
    function SalesChartReport() 
    {   
        var SalesCategoryList = $("#SalesCategoryList").val();
        var SalesBrandList = $("#SalesBrandList").val();
        var SalesResultCount = $("#SalesResultCount").val();
        var SalesChartType = $("#SalesChartType").val();
        var filterType = $("#SalesFilterType").val();
        var SeriesName = "";
        if(filterType == "CENTERWISE"){
            SeriesName = "Center Name";
        }else if(filterType == "ITEMWISE"){
            SeriesName = "Item Name";
        }else if(filterType == "VILLAGEWISE"){
            SeriesName = "Village Name";
        }
        $.ajax({
            url: "<?php echo admin_url(); ?>K1Dashboard/SaleChartDataLoad",
            method: "POST",
            dataType: "json",
            data: {SalesChartType:SalesChartType,FilterType:filterType,
            SalesResultCount:SalesResultCount,SalesBrandList:SalesBrandList,SalesCategoryList:SalesCategoryList },
            success: function (response) {
                if(SalesChartType == "pie"){
                    format = '<span style="font-size:11px;color:{point.color}">{point.name} ({point.y:.2f}%)</span>';
                    pointformat = '{point.y:.2f}%';
                }else{
                    format = '<span style="font-size:11px;color:{point.color}">{point.y:.2f}</span>';
                    pointformat = '{point.y:.2f}';
                }
                Highcharts.chart('Salewise_chartReport', {
                    chart: {
                        type: SalesChartType
                    },
                    title: {
                        text: 'Sales Report Chart'
                    },
                    subtitle: {
                        text: 'Sale Trade'
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {
                        title: {
                            text: 'Total Sale Amount'
                        }
                
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                //format: '{point.y:.1f}'
                                format: format
                            }
                        }
                    },
                
                    tooltip: {
                        Format: '<span style="font-size:11px">{point.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}"> ' +'<b>'+pointformat+'</b>'
                    },
                    series: [
                        {
                            name: SeriesName,
                            colorByPoint: true,
                            data: response.ChartData
                        }
                    ],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            }
                        },
                        series: response.SecondLayerData
                    }
                });
            },
            error: function () {
                alert("Failed to load purchase data.");
            }
        });
    }
	
	// PURCHASE CHART
	
    function PurchaseChartReport() 
    {
        var PurchCategoryList = $("#PurchCategoryList").val();
        var PurchBrandList = $("#PurchBrandList").val();
        var PurchResultCount = $("#PurchResultCount").val();
        var PurchChartType = $("#PurchChartType").val();
        var filterType = $("#PurchFilterType").val();
        var SeriesName = "";
        if(filterType == "CENTERWISE"){
            SeriesName = "Center Name";
        }else if(filterType == "ITEMWISE"){
            SeriesName = "Item Name";
        }
        $.ajax({
            url: "<?php echo admin_url(); ?>K1Dashboard/PurchaseChartDataLoad",
            method: "POST",
            dataType: "json",
            data: {PurchChartType:PurchChartType,FilterType:filterType,
            PurchResultCount:PurchResultCount,PurchBrandList:PurchBrandList,PurchCategoryList:PurchCategoryList },
            success: function (response) {
                if(PurchChartType == "pie"){
                    format = '<span style="font-size:11px;color:{point.color}">{point.name} ({point.y:.2f}%)</span>';
                    pointformat = '{point.y:.2f}%';
                }else{
                    format = '<span style="font-size:11px;color:{point.color}">{point.y:.2f}</span>';
                    pointformat = '{point.y:.2f}';
                }
                Highcharts.chart('Purchasewise_chartReport', {
                    chart: {
                        type: PurchChartType
                    },
                    title: {
                        text: 'Purchase Report Chart'
                    },
                    subtitle: {
                        text: 'Purchase Trade'
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {
                        title: {
                            text: 'Total Purchase Amount'
                        }
                
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                //format: '{point.y:.1f}'
                                format: format
                            }
                        }
                    },
                
                    tooltip: {
                        headerFormat: '<span style="font-size:11px">{point.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}"> ' +'<b>'+pointformat+'</b>'
                    },
                    series: [
                        {
                            name: SeriesName,
                            colorByPoint: true,
                            data: response.ChartData
                        }
                    ],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            }
                        },
                        series: response.SecondLayerData
                    }
                });
            },
            error: function () {
                alert("Failed to load purchase data.");
            }
        });
    }
    function HighStockInventory() 
    {
        var HighStockCategoryList = $("#HighStockCategoryList").val();
        var HighStockBrandList = $("#HighStockBrandList").val();
        var HighStockResultCount = $("#HighStockResultCount").val();
        var HighStockChartType = $("#HighStockChartType").val();
        
        if(HighStockChartType == "pie"){
                format = '<span style="font-size:11px;color:{point.color}">{point.name} ({point.y:.2f}%)</span>';
                pointformat = '{point.y:.2f}%';
            }else{
                format = '<span style="font-size:11px;color:{point.color}">{point.y:.2f}</span>';
                pointformat = '{point.y:.2f}';
            }
        SeriesName = "Item Name";
        $.ajax({
            url: "<?php echo admin_url(); ?>K1Dashboard/HighStockChartDataLoad",
            method: "POST",
            dataType: "json",
            data: {HighStockChartType:HighStockChartType,HighStockResultCount:HighStockResultCount,HighStockBrandList:HighStockBrandList,
            HighStockCategoryList:HighStockCategoryList },
            success: function (response) {
                Highcharts.chart('HighStockInventoryChartReport', {
                    chart: {
                        type: HighStockChartType
                    },
                    title: {
                        text: 'High Stock Inventory Chart'
                    },
                    subtitle: {
                        text: 'Inventory Trade'
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {
                        title: {
                            text: 'Total Stock Quantity'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: format
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:11px">{point.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}"> ' +'<b>'+pointformat+'</b>'
                    },
                    series: [
                        {
                            name: SeriesName,
                            colorByPoint: true,
                            data: response.ChartData
                        }
                    ],
                    drilldown: {
                        breadcrumbs: {
                            position: {
                                align: 'right'
                            }
                        },
                        series: response.SecondLayerData
                    }
                });
            },
            error: function () {
                alert("Failed to load purchase data.");
            }
        });
    }
//=================== Center Wise Sale Purchase ================================
    function SaleVsPurchChartReport() 
    {
        var SalesPurchResultCount = $("#SalesPurchResultCount").val();
        var SalesPurchFilterType = $("#SalesPurchFilterType").val();
        var SalesPurchBrandList = $("#SalesPurchBrandList").val();
        var SalesPurchCategoryList = $("#SalesPurchCategoryList").val();
        
		$.ajax({
			url: "<?php echo admin_url(); ?>K1Dashboard/SaleVsPurchaseChartReport",  
			dataType: "JSON",
			method: "POST",
			data: {SalesPurchResultCount:SalesPurchResultCount,SalesPurchFilterType:SalesPurchFilterType,SalesPurchBrandList:SalesPurchBrandList,SalesPurchCategoryList:SalesPurchCategoryList },
		    success: function (response) {
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
    					color: sale ? sale.color : '#00e272'
    				});
    				purchaseData.push({
    					y: purchase ? purchase.y : 0,
    					color: purchase ? purchase.color : '#fe6a35'
    				});
    			});
			
			    Highcharts.chart('SaleVsPurchChartReport', {
				    chart: { type: 'column' },
    				title: { text: 'Purchase vs Sale' },
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
