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
	 .No-Padding {
    padding:0px;
}
.fixTableHead {
      overflow-y: auto;
      max-height: 175px;
    }
    .fixTableHead thead th {
      position: sticky;
      top: 0;
    }
    .fixTableHead table {
      border-collapse: collapse;        
      width: 100%;
      
    }
   .fixTableHead th,
    td {
      padding: 5px 5px;
      border: 2px solid #529432;
      white-space: nowrap;
    }
    .fixTableHead th {
      background: #51647c;
      padding: 5px 5px;
      text-align: left;
    vertical-align: middle;
    }
	#itemdivision td { padding: 0px 5px !important; border:1px solid !important;font-size:11px; line-height:0.7!important;vertical-align: middle !important;}
	#itemdivision th { padding: 0px 5px !important; border:1px solid !important;font-size:11px; line-height:0.7!important;vertical-align: middle !important;}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
				
                <div class="panel_s">
                    <div class="panel-body">
						
                        <div class="row"> 
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
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
                            <?php echo render_date_input('from_date','FROM',$from_date);  ?>
                        </div>
                
                        <div class="col-md-4">
                            <?php echo render_date_input('to_date','TO',$to_date); ?>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group" app-field-wrapper="item_main_group">
                                <small class="req text-danger">* </small>
                                <label class="control-label">Item Group</label>
                                <select name="item_main_group" id="item_main_group" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true">
                                    <?php
                                    foreach ($main_item_group as $key => $value) {
                                    ?>
                                        <option value="<?php echo $value["id"];?>"><?php echo $value["name"];?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" app-field-wrapper="CenterID">
                                <small class="req text-danger">* </small>
                                <label for="CenterID" class="form-label">Center Name</label> 
                                <select name="CenterID" id="CenterID" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true" <?php echo $GodownStatus;?>>
                                    <!--<option value="">ALL</option>-->
                                <?php
                                    foreach ($CenterMaster as $key => $value) {
                                ?>
                                        <option value="<?php echo $value['CenterID'];?>" ><?php echo $value['CenterName'];?></option>
                                <?php
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" app-field-wrapper="GodownID">
                                <small class="req text-danger">* </small>
                                <label for="GodownID" class="form-label">GodownID Name</label> 
                                <select name="GodownID" id="GodownID" multiple class="selectpicker form-control" data-none-selected-text="All" data-live-search="true" <?php echo $GodownStatus;?>>
                                    <!--<option value="">ALL</option>-->
                                <?php
                                    foreach ($GodownData as $key => $value) {
                                ?>
                                        <option value="<?php echo $value['AccountID'];?>" ><?php echo $value['w_name'];?></option>
                                <?php
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" app-field-wrapper="PartyID">
                                <small class="req text-danger">* </small>
                                <label for="PartyID" class="form-label">Plant Name</label> 
                                <select name="PartyID" id="PartyID" multiple class="selectpicker form-control" data-none-selected-text="All" data-live-search="true" <?php echo $GodownStatus;?>>
                                    <!--<option value="">ALL</option>-->
                                <?php
                                    foreach ($PartyMaster as $key => $value) {
                                ?>
                                        <option value="<?php echo $value['PlantID'];?>" ><?php echo $value['PlantName'];?></option>
                                <?php
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" app-field-wrapper="Service_type">
                                <small class="req text-danger">* </small>
                                <label for="Service_type" class="form-label">Service Type</label> 
                                <select name="Service_type" id="Service_type" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php echo $GodownStatus;?>>
                                    <option value="SP">Kirti Sell / Purchase</option>
                                    <option value="DW">Deposit / Withdraw</option>
                                    <option value="A">Anamat</option>
                                    <option value="TF">Trade Finance</option>
                                </select>
                            </div>
                        </div>
						<div class="col-md-4">
								<label class="control-label">Chart Type</label>
								<select name="ChartType" id="ChartType" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
									<option value="Bar">Bar Chart</option>
									<option value="Pie">Pie Chart</option>
								</select>
							</div>
                        
                        <div class="col-md-4">
                            <div class="custom_button">
                                <button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-right:15px;margin-top: 20px;">Show</button>
                               
                            </div>
                        </div>
                
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12" style="border:1px solid #ccc;height:114px;padding: 0px;">
                        <div class='fixTableHead '>
                            <div class="form-group">
                                <table id="itemdivision" class="table-striped table-bordered itemdivision">
                                    <thead>
                                        <tr>
                                            <th style="border:none !important;"><input id="All" name="All" type="checkbox" value="true" onclick="toggle(this);">
                                                <input name="All" type="hidden" value="true"> &nbsp;All
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="display:grid;grid-template-columns: 3fr 3fr 3fr 3fr;" class="itemgroup_body" >
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            
                        </div>
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
$(document).ready(function(){
    
    var main_item_group_id = $("#item_main_group").val();
    var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	
	get_item_group(main_item_group_id,from_date,to_date);
 
  function get_item_group(main_item_group_id,from_date,to_date)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>misc_reports/get_item_groupFR_StkP",
      dataType:"JSON",
      method:"POST",
      data:{main_item_group_id:main_item_group_id,from_date:from_date,to_date:to_date},
      success:function(data){
	  console.log(data);
          var html = '';
          if(data.length === 0){
            html += '<tr>';
            html += '<td style="border-bottom:none;width:100px;"> Item Group not available...</td></tr>';
            $('.itemgroup_body').html(html);
        }else{
            
            for(var count = 0; count < data.length; count++)
            {
                
            html += '<tr>';
            html += '<td style="border:none !important;">';
            html += '<input id="'+data[count].id+'" name="chk" class="chk" type="checkbox" value="'+data[count].id+'" checked>';
            html += '</td>';
            html += '<td style="border:none !important;">';
            html += '<label for="'+data[count].name+'" style="font-size:11px;">'+data[count].name+'</label>';
            html += '</td>';
            html += '</tr>';
            }
            toggle(true);
            $('.itemgroup_body').html(html);
        }
        }
    });
  }
 
  $('#from_date').on('change',function(){
        
        var main_item_group_id = $("#item_main_group").val();
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    get_item_group(main_item_group_id,from_date,to_date);
        
 });
 
 $('#to_date').on('change',function(){
        
        var main_item_group_id = $("#item_main_group").val();
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    get_item_group(main_item_group_id,from_date,to_date);
        
 });
 $('#item_main_group').on('change',function(){
     
        
	    var main_item_group_id = $("#item_main_group").val();
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    get_item_group(main_item_group_id,from_date,to_date);
        
 });
 
$('#CenterID').on('change', function() {
	var CenterID = $(this).val();
	var url = "<?php echo base_url(); ?>admin/Misc_reports/GetWHListByCenterID";
        jQuery.ajax({
        type: 'POST',
        url:url,
        data: {CenterID: CenterID},
        dataType:'json',
        success: function(data) {
            $("#GodownID").children().remove();
            //$('#GodownID').append('<option value="">Non Selected</option>');
            $.each(data, function (index, value) {
                // APPEND OR INSERT DATA TO SELECT ELEMENT.
                $('#GodownID').append('<option value="' + value.AccountID + '">' + value.w_name + '</option>');
            });
            $("#GodownID").selectpicker("refresh");
        }
    });
});
		$('#search_data').on('click',function(){
			villagereportcharts_filterwise('villagereportcharts_filterwise', '', 'Village Wise charts');
		});
		villagereportcharts_filterwise('villagereportcharts_filterwise', '', 'Village Wise charts');
		function villagereportcharts_filterwise(id, value, title_c){
			
			var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var CenterID = $("#CenterID").val();
	    var GodownID = $("#GodownID").val();
	    var PartyID = $("#PartyID").val();
	    var Service_type = $("#Service_type").val();
	    var ItemMainGroup = $("#ItemMainGroup").val();
	    var ItemGroup = '';
	    var favorite = [];
            $.each($("input[name='chk']:checked"), function(){
                favorite.push($(this).val());
            });
	    var ItemGroup = favorite.join(",");
			// GroupBy = $("#GroupBy").val();
			var ChartType = $("#ChartType").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>misc_reports/get_stock_data_chart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,to_date:to_date,ItemGroup:ItemGroup,ItemMainGroup:ItemMainGroup,
          CenterID:CenterID,GodownID:GodownID,PartyID:PartyID,Service_type:Service_type,ChartType:ChartType},
				success:function(response){
				console.log(response);
				if(ChartType == "Pie"){
								Highcharts.chart(id, {
						chart: {
							type: 'pie'
						},
						title: {
							text: 'stock_position Count'
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
								text: 'stock_position Count'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'stock_position Count : <b>{point.y:.1f} % </b>'
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
						console.log("hello");
							Highcharts.chart(id, {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Stock Position Count'
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
								text: 'Staff Wise Village Count'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Stock Position Count : <b>{point.y:.1f} </b>'
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
	function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}

		
						
						
		
	
	
</script>
<style>
    input[type=checkbox], input[type=radio] {
    margin: 4px 4px 0px;
    line-height: normal;
}
</style>