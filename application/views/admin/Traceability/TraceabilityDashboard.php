<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content" >
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
				$from_date = date('01/m/Y');
				$to_date = date('d/m/Y');
			}
		?>
		<div class="row">
		    <div class="col-md-12">
		        <div class="panel_s" style="border-radius:12px;border:1px solid #e2e8f0;box-shadow:0 2px 10px rgba(15,23,42,0.05);">
					<div class="panel-body" style="background:#fff;border-radius:12px;">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Misc. Reports</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Traceability Dashboard</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
					    <div class="row">
							
							<div class="col-md-12 dash-stat-section">
							    <div class="row dash-stat-grid">
							        <?php
							        $dash_stat_cards = array(
							            array('theme' => 'green',  'icon' => 'fa-tags', 'id' => 'TotalSaleAmt', 'spinner' => 'TotalSaleAmtSpinner', 'value' => '25', 'label' => _l('Total Commodities')),
							            array('theme' => 'gray',   'icon' => 'fa-user-plus', 'id' => 'TotalDiscAmt', 'spinner' => 'TotalDiscAmtSpinner', 'value' => '32743', 'label' => _l('Farmers Registered')),
							            array('theme' => 'blue',  'icon' => 'fa-cubes', 'id' => 'TotalFreshRtnAmt', 'spinner' => 'TotalFreshRtnAmtSpinner', 'value' => '70', 'label' => _l('Total Procurement Centers')),
							            array('theme' => 'green',    'icon' => 'fa-calculator', 'id' => 'TotalDamageRtnAmt', 'spinner' => 'TotalDamageRtnAmtSpinner', 'value' => '232', 'label' => _l('Todays Purchase (in MT)')),
							            array('theme' => 'gray',   'icon' => 'fa-shopping-cart', 'id' => 'TotalOrders', 'spinner' => 'TotalOrdersSpinner', 'value' => '73', 'label' => _l('Stock in Transit (in MT)')),
							            array('theme' => 'red',   'icon' => 'fa-times-circle', 'id' => 'TotalInvoice', 'spinner' => 'TotalInvoiceSpinner', 'value' => '28', 'label' => _l('Rejected Inwards (in MT)')),
							            array('theme' => 'tan', 'icon' => 'fa-tags', 'id' => 'PendingOrder', 'spinner' => 'PendingOrderSpinner', 'value' => 'Latur', 'label' => _l('Top Location')),
							            array('theme' => 'blue',    'icon' => 'fa-times-circle', 'id' => 'CancelOrder', 'spinner' => 'CancelOrderSpinner', 'value' => '165', 'label' => _l('Total Production Batches in Month')),
							            array('theme' => 'purple', 'icon' => 'fa-calculator', 'id' => 'AvgOrderValue', 'spinner' => 'AvgOrderValueSpinner', 'value' => '94', 'label' => _l('Sell Orders (in MT)')),
							            array('theme' => 'gray',   'icon' => 'fa-calculator', 'id' => 'AvgInvoiceValue', 'spinner' => 'AvgInvoiceValueSpinner', 'value' => '5', 'label' => _l('Avg Sale Order Qty (in MT)')),
							            array('theme' => 'green',  'icon' => 'fa-cubes', 'id' => 'TotalSoldQty', 'spinner' => 'TotalSoldQtySpinner', 'value' => 'Soybean (115 MT)', 'label' => _l('Todays Top Production Item')),
							         //   array('theme' => 'tan',    'icon' => 'fa-inr', 'id' => 'GSTCollectionAmt', 'spinner' => 'GSTCollectionAmtSpinner', 'label' => _l('GST Collection Amt')),
							         //   array('theme' => 'blue',   'icon' => 'fa-tags', 'id' => 'TotalSKU', 'spinner' => 'TotalSKUSpinner', 'label' => _l('Total SKU')),
							         //   array('theme' => 'blue',   'icon' => 'fa-user-plus', 'id' => 'NewCustomer', 'spinner' => 'NewCustomerSpinner', 'label' => _l("New Party's")),
							        );
							        foreach ($dash_stat_cards as $card) {
							        ?>
							        <div class="quick-stats-invoices col-xs-12 col-sm-6 col-md-3">
										<div class="top_stats_wrapper dash-stat-card dash-stat-<?php echo $card['theme']; ?>">
											<div class="dash-stat-card__inner">
												<div class="dash-stat-icon"><i class="fa <?php echo $card['icon']; ?>"></i></div>
												<div class="dash-stat-content">
													<p class="dash-stat-value">
														<span class="<?php echo $card['spinner']; ?> dash-stat-spinner"><i class="fa fa-spinner fa-spin"></i></span>
														<span class="labeltxt" id="<?php echo $card['id']; ?>"><?php echo $card['value']; ?></span>
													</p>
													<p class="dash-stat-label title"><?php echo $card['label']; ?></p>
												</div>
											</div>
										</div>
									</div>
							        <?php } ?>
							        
							        <div class="quick-stats-invoices col-xs-12 col-sm-3 col-md-3">
									<div class="top_stats_wrapper dash-stat-card dash-stat-purple dash-stat-card-wide">
										<div class="dash-stat-card__inner">
											<div class="dash-stat-icon"><i class="fa fa-trophy"></i></div>
											<div class="dash-stat-content">
												<p class="dash-stat-value dash-stat-value--product">
													<span class="BestSellerSKUSpinner dash-stat-spinner"><i class="fa fa-spinner fa-spin"></i></span>
													<span class="dash-stat-product-wrap" id="BestSellerSKU">
														<span class="dash-stat-product-name labeltxt" id="BestSellerSKUName">Soybean</span>
														<span class="dash-stat-product-amt labeltxt" id="BestSellerSKUAmt">894</span>
													</span>
												</p>
												<p class="dash-stat-label title"><?php echo _l('Top Purchased Commodity (in MT)'); ?></p>
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
		</div>
		
    <div class="row dashboard-charts-row">
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="top_prod_scans"></div>
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="pie-legend"></div>
                        <p class="highcharts-description"></p>
                    </figure>
                </div>
            </div>
        </div>
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="pie-legend-purchase"></div>
                        <p class="highcharts-description"></p>
                    </figure>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="columns-compare"></div>
                        <p class="highcharts-description"></p>
                    </figure>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 dashboard-chart-col">
            <div class="panel_s dashboard-chart-panel">
                <div class="panel-body">
                    <figure class="highcharts-figure">
                        <div id="guage-speed"></div>
                        <p class="highcharts-description"></p>
                    </figure>
                </div>
            </div>
        </div>
        
        
        
	</div>
	
	</div>
</div>
<style>
#wrapper > .content {
	background-color: #f1f5f9;
    }
    .CrateLedger { overflow: auto;max-height: 58vh;position:relative;top: 0px; }
	.CrateLedger thead th { position: sticky; top: 0; z-index: 1; }
	.CrateLedger tbody th { position: sticky; left: 0; }
	.CrateLedger table  { border-collapse: collapse; }
	.CrateLedger th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.CrateLedger th     { background: #50607b;color: #fff !important; }

.dashboard-charts-row {
	display: flex;
	flex-wrap: wrap;
	align-items: stretch;
	}
	.dashboard-charts-row > .col-md-6,
	.dashboard-charts-row > [class*="col-md-6"] {
	display: flex;
	flex-direction: column;
	padding-left: 8px;
	padding-right: 8px;
	margin-bottom: 10px;
	float: none;
	width: 50%;
	flex: 0 0 50%;
	max-width: 50%;
	}
	@media (max-width: 991px) {
	.dashboard-charts-row > .col-md-6,
	.dashboard-charts-row > [class*="col-md-6"] {
	width: 100%;
	flex: 0 0 100%;
	max-width: 100%;
	}
	}
	.dashboard-charts-row > .col-md-6 > .panel_s,
	.dashboard-charts-row > [class*="col-md-6"] > .panel_s {
	flex: 1 1 auto;
	display: flex;
	flex-direction: column;
	width: 100%;
	margin-bottom: 0 !important;
	border-radius: 12px;
	border: 1px solid #e2e8f0;
	box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
	background: #ffffff;
	overflow: hidden;
	}
	.dashboard-chart-panel {
	position: relative;
	}
	.dashboard-chart-panel::before {
	content: '';
	display: block;
	height: 4px;
	background: var(--dash-chart-accent, #93c5fd);
	}
	.TopCustomer .dashboard-chart-panel,
	.TopCustomer .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #6ee7b7; }
	.TopGroupItem .dashboard-chart-panel,
	.TopGroupItem .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #c4b5fd; }
	.StationWiseTopSale .dashboard-chart-panel,
	.StationWiseTopSale .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #93c5fd; }
	.CityWiseTopSale .dashboard-chart-panel,
	.CityWiseTopSale .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #5eead4; }
	.TopCustomerReturnRate .dashboard-chart-panel,
	.TopCustomerReturnRate .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #fda4af; }
	.TopReturnRateByItemGroup .dashboard-chart-panel,
	.TopReturnRateByItemGroup .top_stats_wrapper.dashboard-chart-panel { --dash-chart-accent: #fcd34d; }
	.MonthlySalesVsReturnCol .dashboard-chart-panel { --dash-chart-accent: #a5b4fc; }
	.MonthlySalesReturnCol .dashboard-chart-panel { --dash-chart-accent: #fda4af; }
	.MonthlyBestSellerCol .dashboard-chart-panel { --dash-chart-accent: #6ee7b7; }
	.YOYMonthlySalesCol .dashboard-chart-panel { --dash-chart-accent: #93c5fd; }
	.dashboard-chart-item-list {
	font-size: 12px;
	color: #64748b;
	margin: 0 0 6px;
	min-height: 18px;
	line-height: 1.4;
	}
	.dashboard-charts-row > .col-md-6:nth-child(1) .dashboard-chart-panel { --dash-chart-accent: #6ee7b7; }
	.dashboard-charts-row > .col-md-6:nth-child(2) .dashboard-chart-panel { --dash-chart-accent: #c4b5fd; }
	.DailySaleChartCol .dashboard-chart-panel { --dash-chart-accent: #c4b5fd; }
	.dashboard-charts-row > .col-md-6:nth-child(3) .dashboard-chart-panel { --dash-chart-accent: #fda4af; }
	.dashboard-charts-row > .col-md-6:nth-child(4) .dashboard-chart-panel { --dash-chart-accent: #c4b5fd; }
	.dashboard-charts-row > .col-md-6:nth-child(5) .dashboard-chart-panel { --dash-chart-accent: #fcd34d; }
	.dashboard-charts-row > .col-md-6:nth-child(6) .dashboard-chart-panel { --dash-chart-accent: #a5b4fc; }
	.dashboard-chart-panel > .panel-body {
	flex: 1 1 auto;
	display: flex;
	flex-direction: column;
	padding: 12px 14px !important;
	min-height: auto;
	max-height: none;
	overflow: hidden;
	position: relative;
	background: #ffffff;
	}
	.dashboard-chart-panel .highcharts-subtitle {
	font-family: Inter, sans-serif !important;
	}
	.dashboard-chart-figure {
	margin: 0;
	width: 100%;
	flex: 1 1 auto;
	min-height: 0;
	}
	.dashboard-chart-container,
	.dashboard-charts-row .highcharts-figure > div,
	.dashboard-charts-row .highcharts-container {
	width: 100% !important;
	min-height: 340px;
	height: 340px !important;
	}
	.highcharts-fullscreen {
	width: 100vw !important;
	height: 100vh !important;
	min-height: 100vh !important;
	max-height: none !important;
	box-sizing: border-box;
	padding: 20px 24px;
	background: #ffffff !important;
	display: flex;
	flex-direction: column;
	}
	.highcharts-fullscreen .dashboard-chart-container,
	.highcharts-fullscreen .highcharts-figure,
	.highcharts-fullscreen .highcharts-figure > div,
	.highcharts-fullscreen .highcharts-container,
	.highcharts-fullscreen > .highcharts-container {
	width: 100% !important;
	flex: 1 1 auto;
	min-height: 0 !important;
	height: auto !important;
	max-height: none !important;
	}
	:fullscreen .dashboard-chart-container,
	:fullscreen .highcharts-container {
	width: 100% !important;
	height: 100% !important;
	min-height: 100% !important;
	max-height: none !important;
	}
	.dashboard-chart-panel > .panel-body > .row {
	flex: 1 1 auto;
	display: flex;
	flex-direction: column;
	width: 100%;
	margin: 0;
	}
	.dashboard-chart-panel > .panel-body > .row > .col-md-12:last-child {
	flex: 1 1 auto;
	display: flex;
	flex-direction: column;
	}
	.dashboard-chart-title {
	text-align: center;
	margin: 0 0 8px;
	font-size: 14px;
	}
	.dashboard-canvas-wrap {
	position: relative;
	width: 100%;
	height: 340px;
	max-height: 340px;
	}
	.dashboard-canvas-wrap canvas {
	width: 100% !important;
	height: 100% !important;
	}
	.Padding_right{
	padding-right: 8px;
	}
	.Padding_left{
	padding-left: 8px;
	}
	.Padding_left_right{
	padding-left: 8px;
	padding-right: 8px;
	}
	.highcharts-credits {
    display: none;
	}
	.table-table_staff tbody{
	display: block;
	max-height: 450px;
	overflow-y: scroll;
	width: calc(100% - -8.9em);
	}
	.table-table_staff thead, .table-table_staff tbody tr{
	display: table;
	table-layout: fixed;
	width: 100%;
	}
	.table-table_staff thead{
	width: calc(100% - -5.9em);
	}
	.table-table_staff thead{
	position: relative;
	}
	.table-table_staff thead th:last-child:after{
	content: ' ';
	position: absolute;
	background-color: #337ab7;
	width: 1.3em;
	height: 38px;
	right: -1.3em;
	top: 0;
	border-bottom: 2px solid #ddd;
	}
	.table-table_staff th td{padding: 32px -20px 12px 14px;
	}
	.fontsize{
	font-size:13px;
	}
	.fontsize2{
	font-size:15px;
	}
    thead tr:nth-child(2) th {
	top: 20px;
    }

.table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
	.table-daily_report tbody th { position: sticky; left: 0; }
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 0px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
    .dash-filter-toolbar {
	margin-bottom: 10px;
    }
    #dash_filter_toggle {
	font-weight: 500;
	border-color: #cbd5e1;
	color: #334155;
    }
    #dash_filter_toggle:hover,
    #dash_filter_toggle.active {
	background: #f1f5f9;
	border-color: #94a3b8;
	color: #0f172a;
    }
    #dash_filter_toggle .fa {
	margin-right: 4px;
    }
    .dash-filter-panel {
	margin-bottom: 4px;
    }
    .dash-filter-row {
	margin-bottom: 12px;
    }
    .dash-stat-section {
	margin-top: 8px;
	padding: 0 4px 8px;
    }
    .dash-stat-grid {
	margin-left: -6px;
	margin-right: -6px;
    }
    .dash-stat-grid > .quick-stats-invoices {
	padding: 6px;
    }
    .dash-stat-card {
	height: 72px;
	border-radius: 10px;
	border-left: 4px solid transparent;
	box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
	padding: 0 !important;
	margin-bottom: 0 !important;
	overflow: hidden;
	transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .dash-stat-card:hover {
	box-shadow: 0 6px 16px rgba(15, 23, 42, 0.1);
	transform: translateY(-1px);
    }
    .dash-stat-card__inner {
	display: flex;
	align-items: center;
	height: 100%;
	padding: 10px 12px;
	gap: 12px;
    }
    .dash-stat-icon {
	flex-shrink: 0;
	width: 42px;
	height: 42px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 18px;
	color: #334155;
    }
    .dash-stat-content {
	flex: 1;
	min-width: 0;
	text-align: left;
    }
    .dash-stat-value {
	margin: 0 0 2px;
	line-height: 1.2;
    }
    .dash-stat-value .labeltxt {
	font-size: 18px;
	font-weight: 700;
	color: #0f172a;
	text-align: left;
	display: inline-block;
	margin: 0;
    }
    .dash-stat-label,
    .dash-stat-label.title {
	font-size: 12px;
	font-weight: 400;
	color: #64748b;
	text-align: left;
	margin: 0;
	line-height: 1.3;
    }
    .dash-stat-value .dash-stat-spinner {
	display: none;
	align-items: center;
	justify-content: center;
	min-height: auto;
	width: auto;
	margin-right: 6px;
	font-size: 16px;
	color: #64748b;
	position: static;
	vertical-align: middle;
    }
    .dash-stat-value .dash-stat-spinner.is-loading {
	display: inline-flex !important;
    }
    .dash-stat-value .dash-stat-spinner .fa {
	display: inline-block;
	font-size: 16px;
	color: #60a5fa;
    }
    .dash-stat-value--product .dash-stat-product-wrap {
	display: flex;
	flex-direction: column;
	gap: 2px;
    }
    .dash-stat-product-name {
	font-size: 11px !important;
	font-weight: 500 !important;
	color: #475569 !important;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	max-width: 100%;
	display: block;
    }
    .dash-stat-product-amt {
	font-size: 18px !important;
	font-weight: 700 !important;
	color: #0f172a !important;
    }
    .dash-stat-green  { background: #ecfdf5; border-left-color: #34d399; }
    .dash-stat-green .dash-stat-icon  { background: rgba(52, 211, 153, 0.25); }
    .dash-stat-gray   { background: #f1f5f9; border-left-color: #94a3b8; }
    .dash-stat-gray .dash-stat-icon   { background: rgba(148, 163, 184, 0.3); }
    .dash-stat-blue   { background: #eff6ff; border-left-color: #60a5fa; }
    .dash-stat-blue .dash-stat-icon   { background: rgba(96, 165, 250, 0.28); }
    .dash-stat-yellow { background: #fffbeb; border-left-color: #fbbf24; }
    .dash-stat-yellow .dash-stat-icon { background: rgba(251, 191, 36, 0.28); }
    .dash-stat-red    { background: #fff1f2; border-left-color: #fb7185; }
    .dash-stat-red .dash-stat-icon    { background: rgba(251, 113, 133, 0.25); }
    .dash-stat-purple { background: #f5f3ff; border-left-color: #a78bfa; }
    .dash-stat-purple .dash-stat-icon { background: rgba(167, 139, 250, 0.25); }
    .dash-stat-tan    { background: #fffbeb; border-left-color: #d4a574; }
    .dash-stat-tan .dash-stat-icon    { background: rgba(212, 165, 116, 0.3); }
    .dash-stat-card-wide .dash-stat-card__inner {
	padding: 10px 16px;
    }
    .col-md-3.col-sm-3.col-xs-12.quick-stats-invoices,
    .dash-stat-grid > .quick-stats-invoices {
	padding: 6px;
    }
    .panel_s{
	margin-bottom:5px !important;
    }
    .labeltxt{
	margin:0;
    }
    .SpinnerCSS,
    p.SpinnerCSS {
	display: none;
	align-items: center;
	justify-content: center;
	min-height: 280px;
	width: 100%;
	margin: 0 !important;
	font-size: 0 !important;
	color: transparent !important;
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	z-index: 10;
	background: rgba(255, 255, 255, 0.75);
    }
    .SpinnerCSS.dash-chart-loading,
    p.SpinnerCSS.dash-chart-loading {
	display: flex !important;
    }
    .SpinnerCSS .fa {
	display: none !important;
    }
    .SpinnerCSS::after {
	content: '';
	width: 44px;
	height: 44px;
	border-radius: 50%;
	border: 3px solid #e2e8f0;
	border-top-color: #93c5fd;
	border-right-color: #6ee7b7;
	animation: dash-loader-spin 0.75s linear infinite;
    }
    .SpinnerCSS::before {
	content: '';
	position: absolute;
	width: 56px;
	height: 56px;
	border-radius: 50%;
	background: rgba(241, 245, 249, 0.85);
    }
    @keyframes dash-loader-spin {
	to { transform: rotate(360deg); }
    }
    .dash-chart-legend-swatch {
	display: inline-block;
	width: 14px;
	height: 14px;
	border-radius: 4px;
	vertical-align: middle;
	margin-right: 6px;
    }
    .dash-chart-legend-swatch--damage { background-color: #fef3c7; }
    .dash-chart-legend-swatch--fresh { background-color: #bfdbfe; }
    .title{
	margin:0px;
    }
    .top_stats_wrapper{
	margin-top: 0px;
	border-radius: 10px;
	padding:0px !important;
	margin-bottom: 0 !important;
    }
	#CityWiseSale .highcharts-null-point,
	#CityWiseCustomer .highcharts-null-point{
	fill: #cbd5e1;
	}
</style>


<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/full-screen.js"></script>
<script src="https://code.highcharts.com/modules/offline-exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<?php init_tail(); ?>
<script src="https://code.highcharts.com/dashboards/datagrid.js"></script>
<script src="https://code.highcharts.com/dashboards/dashboards.js"></script>
<script src="https://code.highcharts.com/dashboards/modules/layout.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/funnel3d.js"></script>


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<script type="text/javascript">
	var DASHBOARD_PASTEL_COLORS = [
		'#a7f3d0', '#bfdbfe', '#ddd6fe', '#fde68a', '#fecdd3',
		'#fef3c7', '#e2e8f0', '#99f6e4', '#fbcfe8', '#c7d2fe',
		'#bae6fd', '#bbf7d0', '#e9d5ff', '#fecaca'
	];
	var DASHBOARD_PASTEL_LINE_COLORS = [
		'#6ee7b7', '#93c5fd', '#c4b5fd', '#fcd34d', '#fda4af',
		'#d4a574', '#94a3b8', '#5eead4', '#f9a8d4', '#a5b4fc',
		'#7dd3fc', '#86efac', '#d8b4fe', '#fca5a5'
	];
	var DASHBOARD_MAP_HEATMAP = {
		minColor: '#bfdbfe',
		maxColor: '#1d4ed8',
		stops: [[0, '#bfdbfe'], [0.45, '#60a5fa'], [1, '#1d4ed8']],
		nullColor: '#cbd5e1',
		borderColor: '#64748b',
		pointColor: '#1d4ed8',
		pointLineColor: '#ffffff',
		labelColor: '#1e293b'
	};
	var DASHBOARD_DUAL_SERIES = {
		lines: ['#d4a574', '#93c5fd'],
		fills: ['#fef3c7', '#bfdbfe']
	};
	var DASHBOARD_CHART_HEIGHT = 340;
	function dashFullscreenHeight() {
		return Math.max(window.innerHeight - 80, 400);
	}
	function dashApplyFullscreenSize(chart) {
		if (!chart) {
			return;
		}
		var h = dashFullscreenHeight();
		chart.setSize(null, h, false);
		if (chart.renderTo) {
			chart.renderTo.style.height = h + 'px';
			chart.renderTo.style.minHeight = h + 'px';
			chart.renderTo.style.maxHeight = 'none';
		}
		chart.reflow();
	}
	function dashRestoreChartSize(chart) {
		if (!chart) {
			return;
		}
		var h = chart._dashSavedHeight || DASHBOARD_CHART_HEIGHT;
		chart.setSize(null, h, false);
		if (chart.renderTo) {
			chart.renderTo.style.height = h + 'px';
			chart.renderTo.style.minHeight = '';
			chart.renderTo.style.maxHeight = '';
		}
		chart.reflow();
	}
	function dashOnBrowserFullscreenChange() {
		var fsEl = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement;
		(Highcharts.charts || []).forEach(function (chart) {
			if (!chart || !chart.renderTo) {
				return;
			}
			var inFs = fsEl && (fsEl === chart.renderTo || fsEl.contains(chart.renderTo) ||
				(chart.renderTo.closest && chart.renderTo.closest('.highcharts-fullscreen') &&
				fsEl.contains(chart.renderTo.closest('.highcharts-fullscreen'))));
			if (inFs) {
				if (!chart._dashSavedHeight) {
					chart._dashSavedHeight = chart.chartHeight || DASHBOARD_CHART_HEIGHT;
				}
				chart._dashInFullscreen = true;
				dashApplyFullscreenSize(chart);
			} else if (chart._dashInFullscreen) {
				chart._dashInFullscreen = false;
				dashRestoreChartSize(chart);
				chart._dashSavedHeight = null;
			}
		});
	}
	Highcharts.theme = {
		colors: DASHBOARD_PASTEL_COLORS,
		chart: {
			backgroundColor: 'transparent',
			plotBackgroundColor: '#ffffff',
			plotBorderWidth: 0,
			style: {
				fontFamily: 'Inter, sans-serif'
			},
			events: {
				fullscreenOpen: function () {
					this._dashSavedHeight = this.chartHeight || DASHBOARD_CHART_HEIGHT;
					this._dashInFullscreen = true;
					var self = this;
					setTimeout(function () { dashApplyFullscreenSize(self); }, 0);
					setTimeout(function () { dashApplyFullscreenSize(self); }, 100);
				},
				fullscreenClose: function () {
					this._dashInFullscreen = false;
					dashRestoreChartSize(this);
					this._dashSavedHeight = null;
				}
			}
		},
		exporting: {
			enabled: true
		},
		title: {
			style: {
				color: '#334155',
				fontSize: '14px',
				fontWeight: '600'
			}
		},
		subtitle: {
			style: {
				color: '#334155',
				fontSize: '14px',
				fontWeight: '600'
			}
		},
		xAxis: {
			gridLineColor: '#E2E8F0',
			labels: {
				style: {
					color: '#64748B',
					fontSize: '12px'
				}
			},
			title: {
				style: {
					color: '#64748B',
					fontSize: '12px'
				}
			}
		},
		yAxis: {
			gridLineColor: '#E2E8F0',
			labels: {
				style: {
					color: '#64748B',
					fontSize: '12px'
				}
			},
			title: {
				style: {
					color: '#64748B',
					fontSize: '12px'
				}
			}
		},
		legend: {
			itemStyle: {
				color: '#475569',
				fontSize: '12px',
				fontWeight: '400'
			}
		},
		tooltip: {
			style: {
				color: '#475569',
				fontSize: '12px'
			}
		}
	};
	Highcharts.setOptions(Highcharts.theme);
	document.addEventListener('fullscreenchange', dashOnBrowserFullscreenChange);
	document.addEventListener('webkitfullscreenchange', dashOnBrowserFullscreenChange);
	$(window).on('resize.dashChartsFullscreen', function () {
		(Highcharts.charts || []).forEach(function (chart) {
			if (!chart) {
				return;
			}
			if (chart._dashInFullscreen || (chart.fullscreen && chart.fullscreen.isOpen)) {
				dashApplyFullscreenSize(chart);
			}
		});
	});
	function dashPastelSlice(n, useLineColors) {
		var palette = useLineColors ? DASHBOARD_PASTEL_LINE_COLORS : DASHBOARD_PASTEL_COLORS;
		var out = [];
		for (var i = 0; i < n; i++) {
			out.push(palette[i % palette.length]);
		}
		return out;
	}
	function dashPastelLineColor(index) {
		return DASHBOARD_PASTEL_LINE_COLORS[index % DASHBOARD_PASTEL_LINE_COLORS.length];
	}
	function dashPastelFillColor(index) {
		return DASHBOARD_PASTEL_COLORS[index % DASHBOARD_PASTEL_COLORS.length];
	}
	function dashChartLoaderOn(chartKey) {
		$('.' + chartKey + 'Spinner').addClass('dash-chart-loading');
		$('.' + chartKey + 'Figure, .' + chartKey + 'ChartWrap').hide();
	}
	function dashChartLoaderOff(chartKey) {
		$('.' + chartKey + 'Spinner').removeClass('dash-chart-loading');
		$('.' + chartKey + 'Figure, .' + chartKey + 'ChartWrap').show();
	}
	function dashDestroyHighchart(containerId) {
		var existingChart = Highcharts.charts.filter(function (chart) {
			return chart && chart.renderTo && chart.renderTo.id === containerId;
		})[0];
		if (existingChart) {
			existingChart.destroy();
		}
	}
	function chartJsToHighchartsSeries(datasets, options) {
		options = options || {};
		var types = options.types || [];
		var lineColors = options.colors || DASHBOARD_DUAL_SERIES.lines;
		var fillColors = options.fillColors || DASHBOARD_DUAL_SERIES.fills;
		var names = options.names || null;
		return (datasets || []).map(function (ds, index) {
			var lineColor = lineColors[index] || dashPastelLineColor(index);
			var fillColor = fillColors[index] || dashPastelFillColor(index);
			var seriesType = types[index] || 'areaspline';
			var name = (names && names[index]) ? names[index] : (ds.label || ('Series ' + (index + 1)));
			return {
				name: name,
				type: seriesType,
				data: (ds.data || []).map(function (v) { return parseFloat(v) || 0; }),
				color: lineColor,
				fillColor: Highcharts.color(fillColor).setOpacity(0.55).get('rgba'),
				lineWidth: 2,
				marker: {
					enabled: true,
					radius: 4,
					fillColor: lineColor,
					lineColor: '#ffffff',
					lineWidth: 1
				}
			};
		});
	}
	function dashStatLoaderOn() {
		$('.dash-stat-value .labeltxt, .dash-stat-product-wrap').hide();
		$('.dash-stat-value .dash-stat-spinner').addClass('is-loading');
	}
	function dashStatLoaderOff() {
		$('.dash-stat-value .dash-stat-spinner').removeClass('is-loading');
		$('.dash-stat-value .labeltxt, .dash-stat-product-wrap').show();
	}
	function dashBindChartLoader(ajaxOpts, chartKey) {
		var userBefore = ajaxOpts.beforeSend;
		var userComplete = ajaxOpts.complete;
		var userError = ajaxOpts.error;
		ajaxOpts.beforeSend = function (xhr, settings) {
			dashChartLoaderOn(chartKey);
			if (typeof userBefore === 'function') {
				userBefore.call(this, xhr, settings);
			}
		};
		ajaxOpts.complete = function (xhr, status) {
			if (chartKey !== 'CityWiseSale' && chartKey !== 'CityWiseCustomer') {
				dashChartLoaderOff(chartKey);
			}
			if (typeof userComplete === 'function') {
				userComplete.call(this, xhr, status);
			}
		};
		ajaxOpts.error = function (xhr, status, error) {
			dashChartLoaderOff(chartKey);
			if (typeof userError === 'function') {
				userError.call(this, xhr, status, error);
			}
		};
		return ajaxOpts;
	}
	function generateChartData(data) {
		if (!data || !data.length) {
			return [];
		}
        const firstWeekday = new Date(data[0].date).getDay(),
		monthLength = data.length,
		lastElement = data[monthLength - 1].date,
		lastWeekday = new Date(lastElement).getDay(),
		lengthOfWeek = 6,
		emptyTilesFirst = firstWeekday,
		chartData = [];
        for (let emptyDay = 0; emptyDay < emptyTilesFirst; emptyDay++) {
            chartData.push({
                x: emptyDay,
                y: 5,
                value: null,
                date: null,
                custom: {
                    empty: true
				}
			});
		}
        for (let day = 1; day <= monthLength; day++) {
            const date = data[day - 1].date;
            const xCoordinate = (emptyTilesFirst + day - 1) % 7;
            const yCoordinate = Math.floor((firstWeekday + day - 1) / 7);
            const id = day;
            const temperature = data[day - 1].temperature;
            chartData.push({
                x: xCoordinate,
                y: 5 - yCoordinate,
                value: temperature,
                date: new Date(date).getTime(),
                custom: {
                    monthDay: id
				}
			});
		}
        return chartData;
	}
</script>
<script>
	var DASHBOARD_YOY_LAST_YEAR_LABEL = <?php echo json_encode('Last Year Sale(April-'.($fy-1).' To March-'.$fy.')'); ?>;
	var DASHBOARD_YOY_CURRENT_YEAR_LABEL = <?php echo json_encode('Current Year Sale(April-'.$fy.' To March-'.($fy+1).')'); ?>;
	var DASH_INDIA_MAP_TOPO = 'https://code.highcharts.com/mapdata/countries/in/custom/in-all-disputed.topo.json';

	function dashNotifyError(message) {
		if (typeof alert_float === 'function') {
			alert_float('danger', message);
		}
	}

	function dashGetFilters() {
		return {
			from_date: $('#from_date2').val(),
			to_date: $('#to_date2').val(),
			TradeType: $('#TradeType').val(),
			AccountID: $('#AccountID').val(),
			MainItemGroup: $('#MainItemGroup').val(),
			SubGroup1: $('#SubGroup1').val(),
			SubGroup2: $('#SubGroup2').val(),
			ItemID: $('#ItemID').val(),
			ItemType: $('#ItemType').val(),
			Station: $('#Station').val(),
			City: $('#City').val(),
			month: $('#month').val(),
			month_return: $('#month_sale_return').val() || $('#month').val()
		};
	}

	function dashFilterPostData(f, extra) {
		var data = {
			from_date: f.from_date,
			to_date: f.to_date,
			TradeType: f.TradeType,
			AccountID: f.AccountID,
			MainItemGroup: f.MainItemGroup,
			SubGroup1: f.SubGroup1,
			SubGroup2: f.SubGroup2,
			ItemID: f.ItemID,
			ItemType: f.ItemType,
			Station: f.Station,
			City: f.City
		};
		if (extra) {
			$.extend(data, extra);
		}
		return data;
	}

	function dashAjax(options) {
		var chartKey = options.chartKey;
		var useStatLoader = options.statLoader;
		var ajaxOpts = {
			url: options.url,
			type: 'POST',
			dataType: 'json',
			data: options.data,
			success: options.success,
			error: function () {
				if (chartKey) {
					dashChartLoaderOff(chartKey);
				}
				if (useStatLoader) {
					dashStatLoaderOff();
				}
				dashNotifyError(options.errorMessage || 'Failed to load dashboard data.');
			}
		};
		if (chartKey) {
			ajaxOpts = dashBindChartLoader(ajaxOpts, chartKey);
		} else if (useStatLoader) {
			var userBefore = ajaxOpts.beforeSend;
			var userComplete = ajaxOpts.complete;
			ajaxOpts.beforeSend = function (xhr, settings) {
				dashStatLoaderOn();
				if (typeof userBefore === 'function') {
					userBefore.call(this, xhr, settings);
				}
			};
			ajaxOpts.complete = function (xhr, status) {
				dashStatLoaderOff();
				if (typeof userComplete === 'function') {
					userComplete.call(this, xhr, status);
				}
			};
		}
		$.ajax(ajaxOpts);
	}

	function dashCategoryColumn(chartKey, containerId, subtitleHtml, url, f, colors) {
		dashAjax({
			url: url,
			chartKey: chartKey,
			data: dashFilterPostData(f),
			errorMessage: 'Failed to load ' + chartKey + ' chart.',
			success: function (returndata) {
				dashDestroyHighchart(containerId);
				Highcharts.chart(containerId, {
					chart: { type: 'column', height: DASHBOARD_CHART_HEIGHT },
					title: { text: '' },
					subtitle: { text: subtitleHtml },
					xAxis: { type: 'category', labels: { autoRotation: [-45, -90] } },
					yAxis: { min: 0, title: { text: 'Total Amt' } },
					legend: { enabled: false },
					tooltip: { pointFormat: 'Total Amt: <b>{point.y:.1f} </b>' },
					series: [{
						name: 'Amount',
						colors: colors || DASHBOARD_PASTEL_COLORS,
						colorByPoint: true,
						groupPadding: 0,
						data: returndata.TransData,
						dataLabels: {
							enabled: true,
							rotation: -90,
							color: '#1e293b',
							inside: true,
							verticalAlign: 'top',
							format: '{point.y:.1f}',
							y: 10
						}
					}]
				});
			}
		});
	}

	function dashLoadCounters(f) {
		dashAjax({
			url: "<?php echo admin_url(); ?>Sale_reports/GetSalesDashboardCounters",
			statLoader: true,
			data: dashFilterPostData(f),
			errorMessage: 'Failed to load summary counters.',
			success: function (returndata) {
				// $('#TotalSaleAmt').html(returndata.TotalSaleAmt ?? '0');
				// $('#TotalDiscAmt').html(returndata.TotalDiscAmt ?? '0');
				// $('#TotalFreshRtnAmt').html(returndata.TotalFreshRtnAmt ?? '0');
				// $('#TotalDamageRtnAmt').html(returndata.TotalDamageRtnAmt ?? '0');
				// $('#TotalOrders').html(returndata.TotalOrders ?? '0');
				// $('#PendingOrder').html(returndata.TotalPendingOrder ?? '0');
				// $('#CancelOrder').html(returndata.CancelOrder ?? '0');
				// $('#TotalInvoice').html(returndata.TotalInvoice ?? '0');
				// $('#AvgOrderValue').html(returndata.AvgOrderValue ?? '0');
				// $('#AvgInvoiceValue').html(returndata.AvgInvoiceValue ?? '0');
				// $('#TotalSoldQty').html(returndata.TotalSoldQty ?? '0');
				// $('#GSTCollectionAmt').html(returndata.GSTCollectionAmt ?? '0');
				// $('#TotalSKU').html(returndata.ItemCount ?? '0');
				// $('#NewCustomer').html(returndata.NewPartys ?? '0');
				// $('#BestSellerSKUName').html(returndata.BestSellerSKUName ?? '-');
				// $('#BestSellerSKUAmt').html(returndata.BestSellerSKUAmt ?? '0'); 
				
			}
		});
		
	}

	function dashLoadDashboard(f) {
		var fd = f.from_date;
		var td = f.to_date;

		dashLoadCounters(f);

		dashCategoryColumn('TopCustomer', 'TopCustomer',
			'<b>Top Customer From ' + fd + ' To ' + td + '</b>',
			"<?php echo admin_url(); ?>Sale_reports/GetTopCustomer", f);

		dashCategoryColumn('TopGroupItem', 'TopGroupItem',
			'<b>Top Group/Items From ' + fd + ' To ' + td + '</b>',
			"<?php echo admin_url(); ?>Sale_reports/GetTopGroupItem", f, dashPastelSlice(4));

		dashCategoryColumn('StationWiseTopSale', 'StationWiseTopSale',
			'<b>Station Wise Top Sale From ' + fd + ' To ' + td + '</b>',
			"<?php echo admin_url(); ?>Sale_reports/GetStationWiseTopSale", f);

		dashCategoryColumn('CityWiseTopSale', 'CityWiseTopSale',
			'<b>City Wise Top Sale From ' + fd + ' To ' + td + '</b>',
			"<?php echo admin_url(); ?>Sale_reports/GetCityWiseTopSale", f, dashPastelSlice(4));

		dashCategoryColumn('TopCustomerReturnRate', 'TopCustomerReturnRate',
			'<b>Top Customer Wise Return Amount From ' + fd + ' To ' + td + '</b>',
			"<?php echo admin_url(); ?>Sale_reports/GetTopCustomerReturnRate", f);
	}
</script>

<script type="text/javascript">
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Month wise Purchase for 2026'
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'March', 'Apr', 'May', 'June'],
        crosshair: true,
        accessibility: {
            description: 'Month Name'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'in MT'
        }
    },
    tooltip: {
        valueSuffix: ' MT'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'Monthly Purchase in MT',
            data: [1482, 1233, 1792, 1977, 2380, 1221]
        }
    ]
});
		</script>
		
		<script type="text/javascript">
Highcharts.chart('top_prod_scans', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Month wise purchase of Soybean'
    },
    xAxis: {
        categories: ['Jan', 'Feb', 'March', 'Apr', 'May', 'June'],
        crosshair: true,
        accessibility: {
            description: 'Month Name'
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Purchase in MT'
        }
    },
    tooltip: {
        valueSuffix: ' MT'
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [
        {
            name: 'Monthly Purchase of Soybean',
            data: [304, 498, 939, 1042, 1732, 894]
        }
    ]
});
		</script>
		
		<script type="text/javascript">
Highcharts.chart('pie-legend', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Group wise Production in May 2026'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Oil',
        colorByPoint: true,
        data: [{
            name: 'Soybean Oil',
            y: 72.77,
            sliced: true,
            selected: true
        },  {
            name: 'Sunflower Oil',
            y: 12.82
        },  {
            name: 'Groundnut Oil',
            y: 5.63
        }, {
            name: 'Safflower Oil',
            y: 4.44
        }, {
            name: 'Cotton Seed Oil',
            y: 4.02
        }, {
            name: 'Other',
            y: 5.28
        }]
    }]
});
		</script>
		
		<script type="text/javascript">
Highcharts.chart('pie-legend-purchase', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Traceability Coverage'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Status',
        colorByPoint: true,
        data: [{
            name: 'Traceable',
            y: 84.42,
            sliced: true,
            selected: true
        },  {
            name: 'Partially Traeable',
            y: 9.92
        },  {
            name: 'Pending Traceability',
            y: 5.63
        }]
    }]
}); //64.77,14.82,6.63,4.44,4.02,5.28
		</script>
		
		<script type="text/javascript">

Highcharts.chart('columns-compare', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Center Wise Commodity Wise Available Stock',
        align: 'left'
    },
    xAxis: {
        categories: ['Akola', 'Beed', 'Latur', 'Hinganghat']
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Available Stock'
        },
        stackLabels: {
            enabled: true
        }
    },
    legend: {
        align: 'left',
        x: 70,
        verticalAlign: 'top',
        y: 70,
        floating: true,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || 'white',
        borderColor: '#CCC',
        borderWidth: 1,
        shadow: false
    },
    tooltip: {
        headerFormat: '<b>{category}</b><br/>',
        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: true
            }
        }
    },
    series: [{
        name: 'Soybean',
        data: [10, 25, 33, 13]
    }, {
        name: 'Sunflower',
        data: [8, 8, 14, 12]
    }, {
        name: 'Chana',
        data: [6, 12, 5, 3]
    }]
});
		</script>
		
		<script type="text/javascript">
Highcharts.chart('guage-speed', {

    chart: {
        type: 'gauge',
        plotBackgroundColor: null,
        plotBackgroundImage: null,
        plotBorderWidth: 0,
        plotShadow: false,
        height: '80%'
    },

    title: {
        text: 'Production Yield for Soybean'
    },

    pane: {
        startAngle: -90,
        endAngle: 95.9,
        background: null,
        center: ['50%', '75%'],
        size: '110%'
    },

    // the value axis
    yAxis: {
        min: 0,
        max: 100,
        tickPixelInterval: 72,
        tickPosition: 'inside',
        tickColor: Highcharts.defaultOptions.chart.backgroundColor || '#FFFFFF',
        tickLength: 20,
        tickWidth: 2,
        minorTickInterval: null,
        labels: {
            distance: 20,
            style: {
                fontSize: '14px'
            }
        },
        lineWidth: 0,
        plotBands: [{
            from: 81,
            to: 100,
            color: '#55BF3B', // green
            thickness: 20,
            borderRadius: '50%'
        }, {
            from: 0,
            to: 50,
            color: '#DF5353', // red
            thickness: 20,
            borderRadius: '50%'
        }, {
            from: 51,
            to: 80,
            color: '#DDDF0D', // yellow
            thickness: 20
        }]
    },

    series: [{
        name: 'Yield',
        data: [90],
        tooltip: {
            valueSuffix: ' %'
        },
        dataLabels: {
            format: '{y} %',
            borderWidth: 0,
            color: (
                Highcharts.defaultOptions.title &&
                Highcharts.defaultOptions.title.style &&
                Highcharts.defaultOptions.title.style.color
            ) || '#333333',
            style: {
                fontSize: '16px'
            }
        },
        dial: {
            radius: '80%',
            backgroundColor: 'gray',
            baseWidth: 12,
            baseLength: '0%',
            rearLength: '0%'
        },
        pivot: {
            backgroundColor: 'gray',
            radius: 6
        }

    }]

});

// Add some life
setInterval(() => {
    const chart = Highcharts.charts[0];
    if (chart && !chart.renderer.forExport) {
        const point = chart.series[0].points[0],
            inc = Math.round((Math.random() - 0.5) * 20);

        let newVal = point.y + inc;
        if (newVal < 0 || newVal > 200) {
            newVal = point.y - inc;
        }

        point.update(newVal);
    }

}, 3000);
		</script>