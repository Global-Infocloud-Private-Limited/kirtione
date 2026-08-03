<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var weekly_payments_statistics;
    var user_dashboard_visibility = <?php echo $user_dashboard_visibility; ?>;
    $(function() {
        $( "[data-container]" ).sortable({
            connectWith: "[data-container]",
            helper:'clone',
            handle:'.widget-dragger',
            tolerance:'pointer',
            forcePlaceholderSize: true,
            placeholder: 'placeholder-dashboard-widgets',
            start:function(event,ui) {
                $("body,#wrapper").addClass('noscroll');
                $('body').find('[data-container]').css('min-height','20px');
            },
            stop:function(event,ui) {
                $("body,#wrapper").removeClass('noscroll');
                $('body').find('[data-container]').removeAttr('style');
            },
            update: function(event, ui) {
                if (this === ui.item.parent()[0]) {
                    var data = {};
                    $.each($("[data-container]"),function(){
                        var cId = $(this).attr('data-container');
                        data[cId] = $(this).sortable('toArray');
                        if(data[cId].length == 0) {
                            data[cId] = 'empty';
                        }
                    });
                    $.post(admin_url+'staff/save_dashboard_widgets_order', data, "json");
                }
            }
        });

        // Read more for dashboard todo items
        $('.read-more').readmore({
            collapsedHeight:150,
            moreLink: "<a href=\"#\"><?php echo _l('read_more'); ?></a>",
            lessLink: "<a href=\"#\"><?php echo _l('show_less'); ?></a>",
        });

        $('body').on('click','#viewWidgetableArea',function(e){
            e.preventDefault();

            if(!$(this).hasClass('preview')) {
                $(this).html("<?php echo _l('hide_widgetable_area'); ?>");
                $('[data-container]').append('<div class="placeholder-dashboard-widgets pl-preview"></div>');
            } else {
                $(this).html("<?php echo _l('view_widgetable_area'); ?>");
                $('[data-container]').find('.pl-preview').remove();
            }

            $('[data-container]').toggleClass('preview-widgets');
            $(this).toggleClass('preview');
        });

        var $widgets = $('.widget');
        var widgetsOptionsHTML = '';
        widgetsOptionsHTML += '<div id="dashboard-options">';
        widgetsOptionsHTML += "<h4><i class='fa fa-question-circle' data-toggle='tooltip' data-placement=\"bottom\" data-title=\"<?php echo _l('widgets_visibility_help_text'); ?>\"></i> <?php echo _l('widgets'); ?></h4><a href=\"<?php echo admin_url('staff/reset_dashboard'); ?>\"><?php echo _l('reset_dashboard'); ?></a>";

        widgetsOptionsHTML += ' | <a href=\"#\" id="viewWidgetableArea"><?php echo _l('view_widgetable_area'); ?></a>';
        widgetsOptionsHTML += '<hr class=\"hr-10\">';

        $.each($widgets,function(){
            var widget = $(this);
            var widgetOptionsHTML = '';
            if(widget.data('name') && widget.html().trim().length > 0) {
                widgetOptionsHTML += '<div class="checkbox checkbox-inline">';
                var wID = widget.attr('id');
                wID = wID.split('widget-');
                wID = wID[wID.length-1];
                var checked= ' ';
                var db_result = $.grep(user_dashboard_visibility, function(e){ return e.id == wID; });
                if(db_result.length >= 0) {
                    // no options saved or really visible
                    if(typeof(db_result[0]) == 'undefined' || db_result[0]['visible'] == 1) {
                        checked = ' checked ';
                    }
                }
                widgetOptionsHTML += '<input type="checkbox" class="widget-visibility" value="'+wID+'"'+checked+'id="widget_option_'+wID+'" name="dashboard_widgets['+wID+']">';
                widgetOptionsHTML += '<label for="widget_option_'+wID+'">'+widget.data('name')+'</label>';
                widgetOptionsHTML += '</div>';
            }
            widgetsOptionsHTML += widgetOptionsHTML;
        });

        $('.screen-options-area').append(widgetsOptionsHTML);
        $('body').find('#dashboard-options input.widget-visibility').on('change',function(){
          if($(this).prop('checked') == false) {
            $('#widget-'+$(this).val()).addClass('hide');
        } else {
            $('#widget-'+$(this).val()).removeClass('hide');
        }

        var data = {};
        var options = $('#dashboard-options input[type="checkbox"]').map(function() {
            return { id: this.value, visible: this.checked ? 1 : 0 };
        }).get();

        data.widgets = options;
/*
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
*/
        $.post(admin_url+'staff/save_dashboard_widgets_visibility',data).fail(function(data) {
            // Demo usage, prevent multiple alerts
            if($('body').find('.float-alert').length == 0) {
                alert_float('danger', data.responseText);
            }
        });
    });

        var tickets_chart_departments = $('#tickets-awaiting-reply-by-department');
        var tickets_chart_status = $('#tickets-awaiting-reply-by-status');
        var leads_chart = $('#leads_status_stats');
        var projects_chart = $('#projects_status_stats');

        if (tickets_chart_departments.length > 0) {
            // Tickets awaiting reply by department chart
            var tickets_dep_chart = new Chart(tickets_chart_departments, {
                type: 'doughnut',
                data: <?php echo $tickets_awaiting_reply_by_department; ?>,
            });
        }
        if (tickets_chart_status.length > 0) {
            // Tickets awaiting reply by department chart
            new Chart(tickets_chart_status, {
                type: 'doughnut',
                data: <?php echo $tickets_reply_by_status; ?>,
                options: {
                   onClick:function(evt){
                    onChartClickRedirect(evt,this);
                }
            },
        });
        }
        if (leads_chart.length > 0) {
            // Leads overview status
            new Chart(leads_chart, {
                type: 'doughnut',
                data: <?php echo $leads_status_stats; ?>,
                options:{
                    maintainAspectRatio:false,
                    onClick:function(evt){
                        onChartClickRedirect(evt,this);
                    }
                }
            });
        }
        if(projects_chart.length > 0){
            // Projects statuses
            new Chart(projects_chart, {
                type: 'doughnut',
                data: <?php echo $projects_status_stats; ?>,
                options: {
                    maintainAspectRatio:false,
                    onClick:function(evt){
                       onChartClickRedirect(evt,this);
                   }
               }
           });
        }

        if($(window).width() < 500) {
            // Fix for small devices weekly payment statistics
            $('#weekly-payment-statistics').attr('height', '250');
        }

        fix_user_data_widget_tabs();
        $(window).on('resize', function(){
            $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').removeAttr('style');
            fix_user_data_widget_tabs();
        });
        // Payments statistics
        init_weekly_payment_statistics( <?php echo $weekly_payment_stats; ?> );
        $('select[name="currency"]').on('change', function() {
            init_weekly_payment_statistics();
        });
    });
    function fix_user_data_widget_tabs(){
        if((app.browser != 'firefox'
                && isRTL == 'false' && is_mobile()) || (app.browser == 'firefox'
                && isRTL == 'false' && is_mobile())){
                $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').css('margin-bottom','26px');
        }
    }
    function init_weekly_payment_statistics(data) {
        if ($('#weekly-payment-statistics').length > 0) {

            if (typeof(weekly_payments_statistics) !== 'undefined') {
                weekly_payments_statistics.destroy();
            }
            if (typeof(data) == 'undefined') {
                var currency = $('select[name="currency"]').val();
                $.get(admin_url + 'home/weekly_payments_statistics/' + currency, function(response) {
                    weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                        type: 'bar',
                        data: response,
                        options: {
                            responsive:true,
                            scales: {
                                yAxes: [{
                                  ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
                }, 'json');
            } else {
                weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                              ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
            }

        }
    }
</script>

<script>
    $("body").on('change', '.onoffswitchT input', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });

    $("body").on('change', '.onoffswitchF input', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });

    $("body").on('change', '.onoffswitchS input', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });
    
function switch_field(field) {
    var status, url, id;
    status = 0;
    if ($(field).prop('checked') === true) {
        status = 1;
    }
    url = $(field).data('switch-url');
    id = $(field).data('id');
    
    $.ajax({
		url:url,
		dataType:"JSON",
		method:"POST",
		data:{id:id,status:status},
		beforeSend: function () {
		},
		complete: function () {
		},
		success:function(data){
		    //alert(data);
		}
	});
    
}
</script> 

<!--  Sale Dashboard Javascript -->

<script>
    function toggle(source) {
        var checkboxes = document.querySelectorAll('input[name="CommodityIDs"]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i] != source)
                checkboxes[i].checked = source.checked;
        }
    }
    function toggle2(source) {
        var checkboxes2 = document.querySelectorAll('input[name="CenterIDs"]');
        for (var i = 0; i < checkboxes2.length; i++) {
            if (checkboxes2[i] != source)
                checkboxes2[i].checked = source.checked;
        }
    }
    function GetSaleDashboardData(){
        // Get Checked Center
        yourCenterIDsArray = Array();    
        $("input:checkbox[name=CenterIDs]:checked").each(function(){
            yourCenterIDsArray.push($(this).val());
        });
        let yourCenterIdsString = yourCenterIDsArray.toString();
        
        // Get Checked Commodity
        yourCommodity = Array();    
        $("input:checkbox[name=CommodityIDs]:checked").each(function(){
            yourCommodity.push($(this).val());
        });
        let yourCommodityString = yourCommodity.toString();
        
        load_data(yourCommodityString,yourCenterIdsString);
    }
</script>

<script>
    function CheckCenter() {
        // Get Checked Center
        yourCenterIDsArray = Array();    
        $("input:checkbox[name=CenterIDs]:checked").each(function(){
            yourCenterIDsArray.push($(this).val());
        });
        let yourCenterIdsString = yourCenterIDsArray.toString();
        
        // Get Checked Commodity
        yourCommodity = Array();    
        $("input:checkbox[name=CommodityIDs]:checked").each(function(){
            yourCommodity.push($(this).val());
        });
        let yourCommodityString = yourCommodity.toString();
        
        load_data(yourCommodityString,yourCenterIdsString);
    }
</script>

<script>
    function CheckCommodity() {
        // Get Checked Commodity
        yourCommodity = Array();    
        $("input:checkbox[name=CommodityIDs]:checked").each(function(){
            yourCommodity.push($(this).val());
        });
        let yourCommodityString = yourCommodity.toString();
        
        // Get Checked Center
        yourCenterIDsArray = Array();    
        $("input:checkbox[name=CenterIDs]:checked").each(function(){
            yourCenterIDsArray.push($(this).val());
        });
        let yourCenterIdsString = yourCenterIDsArray.toString();
        
        load_data(yourCommodityString,yourCenterIdsString);
    }
</script>

<script>
    function load_data(yourCommodityString,yourCenterIdsString)
    {   
        $('#TraderData').html('');
        $('#FarmerData').html('');
        if(yourCommodityString == "" || yourCenterIdsString == ""){
            
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>SaleDashboard/load_data",
                method:"POST",
                dataType:'json',
                data:{yourCommodityString:yourCommodityString, yourCenterIdsString:yourCenterIdsString},
                beforeSend: function () {
                    $('#searchh2').css('display','block');
                },
                complete: function () {
                    $('#searchh2').css('display','none');
                },
                success:function(data){
                    $('#TraderData').html(data.Trader);
                    $('#FarmerData').html(data.Farmer);
                }
            });
        }
    }
</script>

<script type="text/javascript" language="javascript" >
    $(document).ready(function(){
        $('#search_data').on('click',function(){
            var from_date = $("#from_date").val();
    	    var to_date = $("#to_date").val();
    	    var msg = "Sales Report "+from_date +" To " + to_date;
    	    $(".report_for").text(msg);
            load_data(from_date,to_date);
        });
    });
</script>
<script>
    $("body").on('change', '.onoffswitch1 input', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });
    
function switch_field(field) {
    var status, url, id;
    status = 0;
    if ($(field).prop('checked') === true) {
        status = 1;
    }
    url = $(field).data('switch-url');
    id = $(field).data('id');
    
    $.ajax({
		url:url,
		dataType:"JSON",
		method:"POST",
		data:{id:id,status:status},
		beforeSend: function () {
		},
		complete: function () {
		},
		success:function(data){
		    //alert(data);
		}
	});
    
    //requestGet(url + '/' + id + '/' + status);
}
</script>
<script>
    function GetDetails($GroupID,$CenterID,$GroupName,$CenterName,$Type){
        //alert($CenterID);
        $.ajax({
			url:"<?php echo admin_url(); ?>SaleDashboard/load_data_by_groupwise_centerwise",
			dataType:"JSON",
			method:"POST",
			data:{$GroupID:$GroupID,$CenterID:$CenterID,$GroupName:$GroupName,$CenterName:$CenterName,$Type:$Type},
			beforeSend: function () {
			},
			complete: function () {
			},
			success:function(data){
			    $('#ShowDetails').html(data);
			}
		});
    }
</script>

<script>
    function RateMaster(id){
        if(id == "1"){
            window.open("<?php echo admin_url(); ?>Rate_master/FarmerRateUpdate",'_blank');
        }else if(id == "3"){
            window.open("<?php echo admin_url(); ?>Rate_master/RateUpdate",'_blank');
        }else if(id == "2"){
            window.open("<?php echo admin_url(); ?>Rate_master/CompRateUpdate",'_blank');
        }else if(id == "4"){
            window.open("<?php echo admin_url(); ?>Rate_master/MandiRateUpdate",'_blank');
        }
    }
    function TradeCnf(){
        window.open("<?php echo admin_url(); ?>order/PurchaseRequest",'_blank');
    }
</script>
