<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
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

.highcharts-menu-item {
    font-size: 14px !important;
}
</style>

<!-------<div id="wrapper"> 
	<div class="content">
		<div class="row">------>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb" >
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Inventory </b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>As On Date Stock</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<?php
								$fy = $this->session->userdata('finacial_year');
								$fy_new  = $fy + 1;
								$lastdate_date = '20'.$fy_new.'-03-31';
								$firstdate_date = '20'.$fy_new.'-04-01';
								$curr_date = date('Y-m-d'); // e.g., "2025-05-13"
								$date_obj = new DateTime($curr_date); // Create DateTime object
								$formatted_date = $date_obj->format('d/m/Y'); // Format to "13/05/25"
							?>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<?php echo render_date_input('ON_date','Date',$formatted_date);  ?>
									</div>
									
									<div class="col-md-4">
										<div class="form-group" app-field-wrapper="CenterID">
											<small class="req text-danger">* </small>
											<label for="CenterID" class="form-label">Center Name</label> 
											<select name="CenterID" id="CenterID" data-actions-box="true" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true" <?php echo $GodownStatus;?>>
												<!--<option value="">ALL</option>-->
												<?php
													foreach($CenterMaster as $key => $value) {
													?>
													<option value="<?php echo $value['CenterID'];?>" ><?php echo $value['CenterName'];?></option>
													<?php
													}
												?>
											</select>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group" app-field-wrapper="ItemID">
											<small class="req text-danger">* </small>
											<label for="ItemID" class="form-label">Item Category</label> 
											<select name="ItemID" id="ItemID" data-actions-box="true"  class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
												
												<?php
													foreach ($Category as $key => $value) {
													?>
													<option value="<?php echo $value['id'];?>" ><?php echo $value['SubCategoryName'];?></option>
													<?php
													}
												?>
											</select>
										</div>
									</div>
									
									<?php 
									$LogInUser = $this->session->userdata('AccountID');
									?>
									<input type="hidden" name="PartyID" id="PartyID" value="<?php echo $LogInUser;?>">
								
									<div class="col-md-6">
										<div class="custom_button">
											<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-right:15px;">Show</button>
											<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-right: 15px;"><span>Export to Excel</span></a>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" >Print</a>
										</div>
									</div>
									
								</div>
							</div>
							
						</div>
						
						<div class="clearfix"></div>
						<div class="row" style="margin-top:10px;">
							<div class="col-md-12">
								<span id="searchh" style="display:none;">Please wait data loading.</span>
								<span id="searchh2" style="display:none;">Please wait exporting data.....</span>
								<div>
								<div class="stock_position load_data"></div>
								</div>
							</div>
						</div>
						
						
					</div>
				</div>
			</div>
			
			<div class="col-md-6" id="chart"; style="display:none;">
			    <div class="panel_s">
					<div class="panel-body">
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
		<!-----</div>
	</div>
</div>-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/full-screen.js"></script>
<script>
	$(document).ready(function(){
		
		$('#search_data').on('click',function(){
		    
			var on_date = $("#ON_date").val();
			var CenterID = $("#CenterID").val();
			var PartyID = $("#PartyID").val();
			var ItemGroup = $("#ItemID").val();   
			
			//table
			$.ajax({
				url:"<?php echo base_url(); ?>K1InventoryMaster/GetAsondateStockReport",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{on_date:on_date,ItemGroup:ItemGroup,
				CenterID:CenterID,PartyID:PartyID},
				beforeSend: function () {
					$('#searchh').css('display','block');
					$('.load_data').css('display','none');
				},
				complete: function () {
					$('.load_data').css('display','');
					$('#searchh').css('display','none');
				},
				success:function(data){
					$('.load_data').html(data);
				}
			});
			
			//chart
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
                    $('#chart').hide(); 
                },
                complete: function () {
                    $('#searchh12').hide();
                    $('#chart').show(); 
                },
                success: function (response) {
                    if (!response || response.length === 0) {
                        $('#chart').hide(); 
                        alert("No data to display.");
                        return;
                    }
        
                    const chartData = response.map(item => ({
                        name: item.ItemName || item.label || 'Unnamed',
                        y: parseFloat(item.Qty || 0)
                    }));
        
                    Highcharts.chart('ItemQtyChartContainer', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'As On Date Stock Chart'
                        },
                        exporting: {
                            enabled: true,  
                            menuItemStyle: {
                                fontSize: '18px',       
                                fontWeight: 'bold',    
                                color: '#000000'       
                            },
                            buttons: {
                                contextButton: {
                                    menuItems: [
                                        'printChart',
                                        'separator',
                                        'downloadPNG',
                                        'downloadJPEG',
                                        'downloadPDF',
                                        'downloadSVG',
                                        'separator',
                                        'viewFullscreen'
                                    ]
                                }
                            }
                        },
                        xAxis: {
                            type: 'category',
                            title: {
                                text: 'Items',
                                style: { fontSize: '15px', fontWeight: 'bold' }
                            },
                            labels: {
                                rotation: -45,
                                style: { fontSize: '12px' }
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Quantity',
                                style: { fontSize: '15px', fontWeight: 'bold' }
                            }
                        },
                        legend: { enabled: false },
                        tooltip: {
                            pointFormat: 'Quantity: <b>{point.y}</b>'
                        },
                        series: [{
                            name: 'Qty',
                            colorByPoint: true,
                            data: chartData,
                            dataLabels: {
                                enabled: true,
                                rotation: 0,
                                color: '#FFFFFF',
                                align: 'right',
                                format: '{point.y:.0f}',
                                y: -10,
                                style: { fontSize: '13px' }
                            }
                        }]
                    });
                }
            });
		});
		
		$("#caexcel").click(function(){
			var on_date = $("#ON_date").val();
			var CenterID = $("#CenterID").val();
			var PartyID = $("#PartyID").val();
			var ItemGroup = $("#ItemID").val();
				$.ajax({
					url:"<?php echo base_url(); ?>K1InventoryMaster/export_Asondate_stock_report",
					method:"POST",
					data:{on_date:on_date,ItemGroup:ItemGroup,
				CenterID:CenterID,PartyID:PartyID},
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
</script>

<script type="text/javascript">
    function printPage(){ 
        var on_date = $("#on_date").val();
	    var comp_name = $("#comp_name").val();
	    var comp_addr = $("#comp_addr").val();
	    var filterdate = $("#filterdate").val();
	    var rate_base = $("#rate_base").val();                
	    var filter_group = $("#filter_group").val();
		var Center_name = $("#Center_name").val();
		var PartyName = $("#PartyName").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .hide_in_print{ display:none; }</style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementById('stock_position').innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9">'+comp_name+'</td></tr><tr><td style="text-align:center;" colspan="9">'+comp_addr+'</td></tr>';
        
        heading_data += '<tr><td style="text-align:left;"colspan="9">'+filterdate+'</td></tr>';
		heading_data += '<tr><td style="text-align:left;"colspan="9">'+Center_name+'</td></tr>';
        heading_data += '<tr><td style="text-align:left;"colspan="9">'+filter_group+'</td></tr>';
		heading_data += '<tr><td style="text-align:left;"colspan="9">'+PartyName+'</td></tr>';
        
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
	};
</script>
<style>
.stock_position          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
	.stock_position thead th { position: sticky; top: 0; z-index: 1;font-size:12px;font-weight:bold; }
.stock_position tbody th { position: sticky; left: 0; }
	.stock_position tbody td { font-size:12px; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
    
    
	.fixed_headers tbody td {
    border: 1px solid #E3E3E3;
    padding: 0px 5px; 
	}
    
	.fixed_headers thead tr th{
    background-color: #f5f5f5 !important;
    color: #333;
    height: 20px;
    /*width: 100%;*/
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