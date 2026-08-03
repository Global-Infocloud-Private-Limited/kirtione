<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>

.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    border:1px solid #02a9f4;
    background-color:#51647c;
    color:#fff;
}
.nav-tabs>li>a {
    border:1px solid #D0D0D0;
}
    #ck-button {
    margin:4px;
    /*background-color:#EFEFEF;*/
    border-radius:4px;
    border:1px solid #D0D0D0;
    overflow:auto;
    float:left;
}
div label input {
   margin-right:100px;
}


#ck-button label {
    float:left;
    /*width:4.0em;*/
    width:100%;
    display:contents;
}

#ck-button label span {
    text-align:center;
    padding:5px 10px;
    display:block;
}

#ck-button label input {
    position:absolute;
    top:-20px;
    display: none;
}

#ck-button input:checked + span {
    background-color:#51647c;
    color:#fff;
}
</style>
<style>
    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table-daily_report table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.table-daily_report th     { background: #50607b;color: #fff !important; }

#GroupWiseDetails td:hover {
    cursor: pointer;
}
#GroupWiseDetails td:hover {
    background-color: #ccc;
}
#ItemWiseDetails td:hover {
    cursor: pointer;
}
#ItemWiseDetails td:hover {
    background-color: #ccc;
}
.onoffswitch1 {
    position: relative;
    width: 50px;
    -webkit-user-select: none;
</style>
<?php
$this->load->model('hr_profile/hr_profile_model');
$data_dash = $this->hr_profile_model->get_hr_profile_dashboard_data();

//$staff_chart_by_age = json_encode($this->hr_profile_model->staff_chart_by_age());
//$contract_type_chart = json_encode($this->hr_profile_model->contract_type_chart());
$staff_departments_chart = json_encode($this->hr_profile_model->staff_chart_by_departments_main_dashboard());
$staff_chart_by_job_positions = json_encode($this->hr_profile_model->staff_chart_by_job_positions());
?>
<div id="wrapper">
    <div class="screen-options-area"></div>
    <div class="screen-options-btn">
        <?php echo _l('dashboard_options'); ?>
    </div>
    <div class="content">
        
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <?php //hooks()->do_action( 'before_start_render_dashboard_content' ); ?>

            <div class="clearfix"></div>

            <div class="col-md-12 mtop30" data-container="top-12">
                <?php render_dashboard_widgets('top-12'); ?>
            </div>
            
            <div class="col-md-12" data-container="top2-12">
                <?php render_dashboard_widgets('top2-12'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_top_container'); ?>

            <!--<div class="col-md-6" data-container="middle-left-6">
                <?php //render_dashboard_widgets('middle-left-6'); ?>
            </div>-->
            <!--<div class="col-md-6" data-container="middle-right-6">
                <?php //render_dashboard_widgets('middle-right-6'); ?>
            </div>-->

            <?php hooks()->do_action('after_dashboard_half_container'); ?>

            <div class="col-md-8" data-container="left-8">
                <?php render_dashboard_widgets('left-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php render_dashboard_widgets('right-4'); ?>
            </div>

            <div class="clearfix"></div>
            <div class="col-md-6" data-container="left-6">
                <?php render_dashboard_widgets('left-6'); ?>
            </div>
            <div class="col-md-6" data-container="right-6">
                <?php render_dashboard_widgets('right-6'); ?>
            </div>
            
            <div class="col-md-8" data-container="left-8">
                <?php render_dashboard_widgets('left-bottom-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php render_dashboard_widgets('right-bottom-4'); ?>
            </div>
            <!--<div class="col-md-4" data-container="bottom-left-4">
                <?php //render_dashboard_widgets('bottom-left-4'); ?>
            </div>
             <div class="col-md-4" data-container="bottom-middle-4">
                <?php //render_dashboard_widgets('bottom-middle-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-right-4">
                <?php //render_dashboard_widgets('bottom-right-4'); ?>
            </div>-->

            <?php hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>
<script>
    app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
    
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
<script>
$(document).ready(function(){
        
    var idleTime = 0;
    // Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 1000); // 1 minute

    // Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });
    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > 10) { // 20 minutes
            //window.location.reload();
            var to_date_deposite = $("#to_date_deposite").val();
            var from_date_deposite = $("#from_date_deposite").val();
            centerwise_commoditywise_deposit('centerwise_commoditywise_deposit', '', '');
            idleTime = 0;
        }
    }
    <?php
        if(is_admin()){
            ?>
            var CenterID = $("#CenterID").val();
        var BookingType = $("#BookingType").val();
        TradeTypeCenterWiseReport(CenterID,BookingType);
    <?php
        }
    ?>
        
        function TradeTypeCenterWiseReport(CenterID,BookingType){
            $.ajax({
                url:"<?php echo admin_url(); ?>GateControl/TradeTypeCenterWiseReport",
                method:"POST",
                data:{
                    CenterID:CenterID,
                    BookingType:BookingType
                },
                beforeSend: function () {
                    $('#table_purchase_request tbody').html('');
                },
                success:function(data){
                    if(data == ''){ 
                        data = '<span style="color:red;">No records found...</span>';
                    }else{
                        $('#table_purchase_request tbody').html(data);
                    }
                }
            });
        }
        
        $('#CenterID').on('change',function(){
            var CenterID = $("#CenterID").val();
            var BookingType = $("#BookingType").val();
            TradeTypeCenterWiseReport(CenterID,BookingType);
        })
        staff_chart_by_age('staff_chart_by_job_positions',<?php echo html_entity_decode($staff_chart_by_job_positions); ?>, <?php echo json_encode(_l('hr_chart_by_job_positions')); ?>);
	    staff_chart_by_age('staff_departments_chart',<?php echo html_entity_decode($staff_departments_chart); ?>, <?php echo json_encode(_l('hr_chart_by_department')); ?>);
	    centerwise_commoditywise_purchase('centerwise_commoditywise_purchase', '', '');
	    centerwise_commoditywise_purchase_stock('centerwise_commoditywise_purchase_stock', '', '');
	    centerwise_commoditywise_deposit('centerwise_commoditywise_deposit', '', '');
	    centerwise_commoditywise_deposit_stock('centerwise_commoditywise_deposit_stock', '', '');
	    
	    $('#to_date_deposit_stock').on('change',function(){
            var to_date_deposit_stock = $("#to_date_deposit_stock").val();
            var from_date_deposit_stock = $("#from_date_deposit_stock").val();
            centerwise_commoditywise_deposit_stock('centerwise_commoditywise_deposit_stock', '', '');
        })
        
        $('#from_date_deposit_stock').on('change',function(){
            var to_date_deposit_stock = $("#to_date_deposit_stock").val();
            var from_date_deposit_stock = $("#from_date_deposit_stock").val();
            centerwise_commoditywise_deposit_stock('centerwise_commoditywise_deposit_stock', '', '');
        })
        
        $('#to_date_purchase_stock').on('change',function(){
            var to_date_purchase_stock = $("#to_date_purchase_stock").val();
            var from_date_purchase_stock = $("#from_date_purchase_stock").val();
            centerwise_commoditywise_purchase_stock('centerwise_commoditywise_purchase_stock', '', '');
        })
        
        $('#from_date_purchase_stock').on('change',function(){
            var to_date_purchase_stock = $("#to_date_purchase_stock").val();
            var from_date_purchase_stock = $("#from_date_purchase_stock").val();
            centerwise_commoditywise_purchase_stock('centerwise_commoditywise_purchase_stock', '', '');
        })
	
	function centerwise_commoditywise_purchase(id, value, title_c){
        'use strict';
    	
    	requestGetJSON('GateControl/centerwise_commoditywise_purchase').done(function (response) {
            
           //get data for hightchart
           Highcharts.setOptions({
           	chart: {
           		style: {
           			fontFamily: 'inherit !important',
           			fill: 'black'
           		}
           	},
           	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
           });
           Highcharts.chart(id, {
           	chart: {
           		type: 'column'
           	},
           	title: {
           		text: 'Center Wise Commodity Wise Purchase'
           	},
           	credits: {
           		enabled: false
           	},
           	xAxis: {
           		categories: response.categories,
           		crosshair: true
           	},
           	yAxis: {
           		min: 0,
           		title: {
           			text: ''
           		}
           	},
           	tooltip: {
           		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
           		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
           		'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
           		footerFormat: '</table>',
           		shared: true,
           		useHTML: true
           	},
           	plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0
           		}
           	},
           	series: response.data
           });
       });
    }
    
    function centerwise_commoditywise_deposit(id, value, title_c){
        'use strict';
    	
    	requestGetJSON('GateControl/centerwise_commoditywise_deposit').done(function (response) {
            
           //get data for hightchart
           Highcharts.setOptions({
           	chart: {
           		style: {
           			fontFamily: 'inherit !important',
           			fill: 'black'
           		}
           	},
           	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
           });
           Highcharts.chart(id, {
           	chart: {
           		type: 'column'
           	},
           	title: {
           		text: 'Center Wise Commodity Wise Deposit'
           	},
           	credits: {
           		enabled: false
           	},
           	xAxis: {
           		categories: response.categories,
           		crosshair: true
           	},
           	yAxis: {
           		min: 0,
           		title: {
           			text: ''
           		}
           	},
           	tooltip: {
           		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
           		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
           		'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
           		footerFormat: '</table>',
           		shared: true,
           		useHTML: true
           	},
           	plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0
           		}
           	},
           	series: response.data
           });
       });
    }
    
    function centerwise_commoditywise_deposit_stock(id, value, title_c){
        'use strict';
    	
    	requestGetJSON('GateControl/centerwise_commoditywise_deposit_stock').done(function (response) {
            
           //get data for hightchart
           Highcharts.setOptions({
           	chart: {
           		style: {
           			fontFamily: 'inherit !important',
           			fill: 'black'
           		}
           	},
           	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
           });
           Highcharts.chart(id, {
           	chart: {
           		type: 'column'
           	},
           	title: {
           		text: 'Center Wise Commodity Wise Deposit Stock'
           	},
           	credits: {
           		enabled: false
           	},
           	xAxis: {
           		categories: response.categories,
           		crosshair: true
           	},
           	yAxis: {
           		min: 0,
           		title: {
           			text: ''
           		}
           	},
           	tooltip: {
           		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
           		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
           		'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
           		footerFormat: '</table>',
           		shared: true,
           		useHTML: true
           	},
           	plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0
           		}
           	},
           	series: response.data
           });
       });
    }
    
    function centerwise_commoditywise_purchase_stock(id, value, title_c){
        'use strict';
    	
    	requestGetJSON('GateControl/centerwise_commoditywise_purchase_stock').done(function (response) {
            
           //get data for hightchart
           Highcharts.setOptions({
           	chart: {
           		style: {
           			fontFamily: 'inherit !important',
           			fill: 'black'
           		}
           	},
           	colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
           });
           Highcharts.chart(id, {
           	chart: {
           		type: 'column'
           	},
           	title: {
           		text: 'Center Wise Commodity Wise Purchase Stock'
           	},
           	credits: {
           		enabled: false
           	},
           	xAxis: {
           		categories: response.categories,
           		crosshair: true
           	},
           	yAxis: {
           		min: 0,
           		title: {
           			text: ''
           		}
           	},
           	tooltip: {
           		headerFormat: '<span class="font-size-10">{point.key}</span><table>',
           		pointFormat: '<tr><td class="padding-0" style="color:{series.color}">{series.name}: </td>' +
           		'<td class="padding-0"><b>{point.y:.1f}</b></td></tr>',
           		footerFormat: '</table>',
           		shared: true,
           		useHTML: true
           	},
           	plotOptions: {
           		column: {
           			pointPadding: 0.2,
           			borderWidth: 0
           		}
           	},
           	series: response.data
           });
       });
    }
	function staff_chart_by_age(id, value, title_c){
	    Highcharts.setOptions({
			chart: {
				style: {
					fontFamily: 'inherit !important',
					fontWeight:'normal',
					fill: 'black'
				}
			},
			colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
		});
		Highcharts.chart(id, {
			chart: {
				backgroundcolor: '#fcfcfc8a',
				type: 'column'
			},
			accessibility: {
				description: null
			},
			title: {
				text: title_c
			},
			credits: {
				enabled: false
			},
			tooltip: {
				pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode(_l('invoice_table_quantity_heading')); ?>+'</span>: <b>{point.y}</b> <br/>',
				shared: true
			},
			legend: {
				enabled: false
			},
			xAxis: {
				type: 'category'
			},
			yAxis: {
				title: {
					text: ''
				}

			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					depth: 35,
					dataLabels: {
						enabled: true,
						format: '{point.name}'
					}        
				}
			},
			series: [{
				name: "",
				colorByPoint: true,
				data: value,

			}]
		});
	}
    });
</script>
</body>
</html>
