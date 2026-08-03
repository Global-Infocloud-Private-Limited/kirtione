<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report {
	overflow: auto;
	max-height: 55vh;
	width: 100%;
	position: relative;
	top: 0px;
    }
	
    .table-daily_report thead th {
	position: sticky;
	top: 0;
	z-index: 1;
    }
	
    .table-daily_report tbody th {
	position: sticky;
	left: 0;
    }
	
	
    table {
	border-collapse: collapse;
	width: 100%;
    }
	
    th,
	
    td {
	padding: 1px 5px !important;
	white-space: nowrap;
	border: 1px solid !important;
	font-size: 11px;
	line-height: 1.42857143 !important;
	vertical-align: middle !important;
    }
	
    th {
	background: #50607b;
	color: #fff !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
				
                <div class="panel_s">
                    <div class="panel-body">
						
                        <div class="row ">
                            <?php
								$fy = $this->session->userdata('finacial_year');
								$fy_new = $fy + 1;
								$lastdate_date = '20' . $fy_new . '-03-31';
								$firstdate_date = '20' . $fy_new . '-04-01';
								$curr_date = date('Y-m-d');
								$curr_date_new = new DateTime($curr_date);
								$last_date_yr = new DateTime($lastdate_date);
								if ($last_date_yr < $curr_date_new) {
									$to_date = '31/03/20' . $fy_new;
									$from_date = '01/03/20' . $fy_new;
									} else {
									$from_date = "01/" . date('m') . "/" . date('Y');
									$to_date = date('d/m/Y');
								}
							?>
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date', 'From', $from_date); ?>
							</div>
							
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'To', $to_date); ?>
							</div>                         
                            <div class="col-md-2">                               
									<label for="Account_State">State</label>
									<select name="Account_State" id="Account_State" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">All</option> 
										<?php foreach ($States as $val): ?>
										<option value="<?php echo $val["short_name"]; ?>"><?php echo $val["state_name"]; ?></option>
										<?php endforeach; ?>
									</select>
								</div>  
								
								<div class="col-md-2">                               
									<label for="Account_district">District</label>
									<select name="Account_district" id="Account_district" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Select District</option> 
										
									</select>
								</div>                                    
								<div class="col-md-2">
									<label for="Account_taluka">Taluka</label>
									<select name="Account_taluka" id="Account_taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Select Taluka</option>
									</select>
								</div>
							 <div class="col-md-2">                               
                               <label for="Staff_Id">Staff By</label>
                               <select name="Staff_Id" id="Staff_Id" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                    <option value="">All</option> 
                                    <?php foreach ($staff as $val): ?>
                                        <option value="<?php echo $val["staffid"]; ?>"><?php echo $val["firstname"]." ".$val["lastname"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
							
							</div>	
					 <div class="row ">
					 <div class="col-md-2">                               
								<label for="ReportFor">Report For</label>
								<select name="ReportFor" id="ReportFor" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
									<option value="1">Created By</option> 
									<option value="2">Representative By</option> 
                                    
								</select>
							</div>
							<div class="col-md-2">                               
								<label for="GroupBy">Group By</label>
								<select name="GroupBy" id="GroupBy" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
									<option value="1">Staff Wise</option> 
									<option value="2">District Wise</option> 
                                    
								</select>
							</div>
						<div class="col-md-2">
								<label class="control-label">Chart Type</label>
								<select name="ChartType" id="ChartType" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
									<option value="Bar">Bar Chart</option>
									<option value="Pie">Pie Chart</option>
								</select>
							</div>
						
						<div class="col-md-6" style="margin-top:10px;">
							<button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;"
							id="search_data">Show</button>                           
						</div>
					</div>	
						
						<div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12">
							<div id="villagereportcharts_filterwise"></div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail() ?>
<script>
	$(document).ready(function() {
        $('#Staff_Id').on('change', function() {
            var Staff_Id = $(this).val();
            if(empty(Staff_Id)){
                $('#GroupBy').val('1').selectpicker('refresh');
            }else{
                $('#GroupBy').val('2').selectpicker('refresh');
            }
        });
        $('#GroupBy').on('change', function() {
            var GroupBy = $(this).val();
            var Staff_Id = $("#Staff_Id").val();
            if(GroupBy == "1" && !empty(Staff_Id)){
                alert("Please remove selected staff and change report group");
                $('#GroupBy').val('2').selectpicker('refresh');
            }
        });
    });
	$(document).ready(function() {
			$('#Account_State').change(function () {
			var StateId = $(this).val();
			
			if (StateId !== "") {
				$.ajax({
					url: "<?php echo admin_url(); ?>misc_reports/getDistrict", 
					type: "POST",
					data: { StateId: StateId },
					dataType: "json",
					success: function (response) {
						var $district = $('#Account_district');
						$district.empty().append('<option value="">Select District</option>');
						
						$.each(response, function (index, item) {
							$district.append('<option value="' + item.id + '">' + item.city_name + '</option>');
						});
						
						$district.selectpicker('refresh');
						$('#Account_taluka').empty().append('<option value="">Select Taluka</option>').selectpicker('refresh'); // Reset taluka
					}
				});
				} else {
				$('#Account_district').html('<option value="">Select District</option>').selectpicker('refresh');
				$('#Account_taluka').html('<option value="">Select Taluka</option>').selectpicker('refresh');
			}
		});
		
		// On District change
		$('#Account_district').change(function () {
			var DistrictId = $(this).val();
			
			if (DistrictId !== "") {
				$.ajax({
					url: "<?php echo admin_url(); ?>misc_reports/getTaluka",
					type: "POST",
					data: { DistrictId: DistrictId },
					dataType: "json",
					success: function (response) {
						var $taluka = $('#Account_taluka');
						$taluka.empty().append('<option value="">Select Taluka</option>');
						
						$.each(response, function (index, item) {
							$taluka.append('<option value="' + item.id + '">' + item.TalukaName + '</option>');
						});
						
						$taluka.selectpicker('refresh');
					}
				});
				} else {
				$('#Account_taluka').html('<option value="">Select Taluka</option>').selectpicker('refresh');
			}
		});
		
		
		
		$('#search_data').on('click',function(){
			villagereportcharts_filterwise('villagereportcharts_filterwise', '', 'Village Wise charts');
		});
		villagereportcharts_filterwise('villagereportcharts_filterwise', '', 'Village Wise charts');
		function villagereportcharts_filterwise(id, value, title_c){
			
			from_date = $('#from_date').val();
			to_date = $('#to_date').val();
			State = $("#Account_State").val(); 
			District = $("#Account_district").val(); 
			Taluka = $("#Account_taluka").val();
			ReportFor = $("#ReportFor").val();
			Staff_Id = $("#Staff_Id").val();
			GroupBy = $("#GroupBy").val();
			ChartType = $("#ChartType").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>misc_reports/Survey_wise_chart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,to_date:to_date,State:State,District:District,Taluka:Taluka,ReportFor:ReportFor,Staff_Id:Staff_Id,ChartType:ChartType,GroupBy:GroupBy},
				success:function(response){
				console.log(response.ChartData);
						
						if(ChartType == "Pie"){
								Highcharts.chart(id, {
						chart: {
							type: 'pie'
						},
						title: {
							text: 'Staff Wise Survey Count'
						},
						subtitle: {
							text: '<b></b>'
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
								text: 'Staff Wise Survey Count'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Staff Wise Survey Count : <b>{point.y:.1f} % </b>'
						},
						series: [{
							// colors: [ '#691af3',
							// '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
							// '#3667c9', '#2f72c3', '#277dbd', '#1f88b7', '#1693b1', '#0a9eaa',
							// '#03c69b',  '#00f194'
							// ],
							colorByPoint: true,
							groupPadding: 0,
							data: response.ChartData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}'+'%', // one decimal
								y: 10, // 10 pixels down from the top
								style: {
									fontSize: '13px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						}]
					});
					}
					
					
					
					if(ChartType == "Bar"){
					Highcharts.chart(id, {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Staff Wise Survey Count'
						},
						subtitle: {
							text: '<b></b>'
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
								text: 'Staff Wise Survey Count'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Staff Wise Survey Count : <b>{point.y:.1f} </b>'
						},
						series: [{
							// colors: [ '#691af3',
							// '#6225ed', '#5b30e7', '#533be1', '#4c46db', '#4551d5', '#3e5ccf',
							// '#3667c9', '#2f72c3', '#277dbd', '#1f88b7', '#1693b1', '#0a9eaa',
							// '#03c69b',  '#00f194'
							// ],
							colorByPoint: true,
							groupPadding: 0,
							data: response.ChartData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								style: {
									fontSize: '13px',
									fontFamily: 'Verdana, sans-serif'
								}
							}
						}]
					});
				  }
				
					
					
					
				}
			});
			
		}
		
		
		
		
		
	});	
	
	
	
	
</script>