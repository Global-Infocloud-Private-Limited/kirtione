<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb" >
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>K1 Inventory </b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>As On Date Stock New</b></li>
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
								$attr = array('readonly'=>'readonly');
							?>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-3">
										<?php echo render_date_input('ON_date','Date',$formatted_date,$attr);  ?>
									</div>
									
									<div class="col-md-3">
										<div class="form-group" app-field-wrapper="CenterID">
											<small class="req text-danger">* </small>
											<label for="CenterID" class="form-label">Center Name</label> 
											<select name="CenterID" id="CenterID" data-actions-box="true" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true" <?php echo $GodownStatus;?>>
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
									<!--<div class="col-md-3">
										<div class="form-group" app-field-wrapper="GodownID">
										<small class="req text-danger">* </small>
										<label for="GodownID" class="form-label">GodownID Name</label> 
										<select name="GodownID" id="GodownID" multiple class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">
										<option value="">None selected</option>
										</select>
										</div>
									</div>-->
									<div class="col-md-3">
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
									
									<div class="col-md-3">
										<div class="form-group" app-field-wrapper="PartyID">
											<label class="form-label">Stock for</label>
											<select class="selectpicker"  data-actions-box="true" name="PartyID" id="PartyID" data-width="100%" multiple data-none-selected-text="None selected" data-live-search="true">
												<option value="<?php echo $company_detail->comp_short;?>"Selected><?php echo $company_detail->company_name;?></option>
												
												<?php
													foreach($KirtiOneAccessList as $key=>$val){
													?>
													<option value="<?php echo $val["AccountID"];?>"><?php echo $val["company"];?></option>
													<?php
													}
												?>
											</select>
										</div>
									</div>
									
									<!--<div class="col-md-3">
										<div class="form-group" app-field-wrapper="Service_type">
										<small class="req text-danger">* </small>
										<label for="Service_type" class="form-label">Service Type</label> 
										<select name="Service_type" id="Service_type" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" <?php echo $GodownStatus;?>>
										<option value="SP">Kirti Sell / Purchase</option>
										<option value="DW">Deposit / Withdraw</option>
										</select>
										</div>
									</div>-->
									<div class="col-md-6">
										<div class="custom_button">
											<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-right:15px;margin-top: 15px;">Show</button>
											<?php if (has_permission_new('K1AsOnStockPosition', '', 'export')) {
											?>
											<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-right: 15px;margin-top: 15px;"><span>Export to Excel</span></a>
											<?php } ?>
											
											<?php if (has_permission_new('K1AsOnStockPosition', '', 'print')) {
											?>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-top: 15px;">Print</a>
											<?php } ?>
										</div>
									</div>
									
									
								</div>
							</div>
							
						</div>
						
						<div class="clearfix"></div>
						<div class="row">
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
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function()
    {
        var maxEndDate = new Date('Y/m/d');
        var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
        var year = "20"+fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);
        if(cur_y > fin_y){
            var year2 = parseInt(fin_y) + parseInt(1);
            var year2_new = "20"+year2;
            var e_dat = new Date(year2_new+'/03/31');
            var maxEndDate_new = e_dat;
			}else{
            var maxEndDate_new = maxEndDate;
		}
        var minStartDate = new Date(year, 03);
		
        $('#ON_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false
		});
	});
</script> 
<script>
	$(document).ready(function(){
		
		$('#CenterID').on('change',function(){
			var CenterID = $("#CenterID").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>K1InventoryMaster/GetGodownListByCenterID",
				dataType:"JSON",
				method:"POST",
				data:{CenterID:CenterID},
				success:function(data){
					let GodownList = data;
					$("#GodownID").children().remove();
					for (var i = 0; i < GodownList.length; i++) {
						$("#GodownID").append('<option value="'+GodownList[i]["AccountID"]+'">'+GodownList[i]["w_name"]+'</option>');
					}
					$('.selectpicker').selectpicker('refresh');
					
					$('#GodownID').selectpicker('val', data.dist);
					$('.selectpicker').selectpicker('refresh');
				} 
			});
		});
		
		$('#search_data').on('click',function(){
			var on_date = $("#ON_date").val();
			var CenterID = $("#CenterID").val();
			var PartyID = $("#PartyID").val();
			var ItemGroup = $("#ItemID").val();   
			let html = '';
			
			$.ajax({
				url:"<?php echo admin_url(); ?>K1InventoryMaster/FilterAsondateStockReport",
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
					html = `<table class="table-striped table-bordered stock_position" id="stock_position" width="100%">
										<thead style="font-size:11px;">
											<tr style="display:none;">
												<th colspan="9"><b><?= $company_detail->company_name ?></b></th>
											</tr>
											<tr style="display:none;">
												<th colspan="9"><b><?= $company_detail->address ?></b></th>
											</tr>
											<tr >
											<th align="left" rowspan="2">SrNo</th>
											<th align="left" rowspan="2">Item ID</th>
											<th align="left" rowspan="2">Item Name</th>
											<th align="center">UOM</th>
											<th align="center">Packing Qty</th>
											<th align="center">Qty (Loose)</th>
										</tr>
									</thead>
									<tbody>`;
					if(data.length > 0){
						$.each(data,function(i,v){
							html += `<tr>
										<td>`+i+`</td>
										<td>`+v["ItemID"]+`</td>
										<td>`+v["ItemName"]+`</td>
										<td align="center">`+v["UOM"]+`</td>
										<td align="center">`+v["PackingQty"]+`</td>
										<td align="center">`+v["Qty"]+`</td>
									</tr>`;
						});
					}else{
						html += `<tr>
									<td align="center" colspan="6">No Data Found</td>
								</tr>`;
					}
					html += `</tbody>
								</table>`;
					$('.load_data').html(html);
				}
			});
		});
		
		
		$("#caexcel").click(function(){
			var on_date = $("#ON_date").val();
			var CenterID = $("#CenterID").val();
			var PartyID = $("#PartyID").val();
			var ItemGroup = $("#ItemID").val();
				$.ajax({
					url:"<?php echo admin_url(); ?>K1InventoryMaster/export_Asondate_stock_report",
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