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
								<li class="breadcrumb-item active" aria-current="page"><b>Salable Report</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
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
								$attr = array('readonly'=>'readonly');
							?>
							<div class="col-md-12">
							    <input type="hidden" name="comp_name" id="comp_name" value="<?php echo $company_detail->company_name;?>">
							    <input type="hidden" name="comp_addr" id="comp_addr" value="<?php echo $company_detail->address;?>">
								<div class="row">
								    <div class="col-md-3">
										<?php echo render_date_input('from_date','From',$from_date);  ?>
									</div>
									
									<div class="col-md-3">
										<?php echo render_date_input('to_date','To',$to_date); ?>
									</div>
									
								    <div class="col-md-3">
										<div class="form-group" app-field-wrapper="CenterID">
											<small class="req text-danger">* </small>
											<label for="CenterID" class="form-label">Center Name</label> 
											<select name="CenterID" id="CenterID" data-actions-box="true" class="selectpicker form-control" data-none-selected-text="All" data-live-search="true">
											    <option value=""></option>
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
									
									<div class="col-md-3">
										<div class="form-group" app-field-wrapper="ItemID">
											<small class="req text-danger">* </small>
											<label for="ItemID" class="form-label">Item Category</label> 
											<select name="ItemID" id="ItemID" data-actions-box="true"  class="selectpicker form-control" data-none-selected-text="All" data-live-search="true">
												 <option value=""></option>
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
										<div class="form-group" app-field-wrapper="reportby">
											<small class="req text-danger">* </small>
											<label for="reportby" class="form-label">Report By</label> 
											<select name="reportby" id="reportby" data-actions-box="true" class="selectpicker form-control" data-none-selected-text="All" data-live-search="true">
											    <option value="Qty">Qty</option>
											    <option value="Amt">Amount</option>
											</select>
										</div>
									</div>
									
									<div class="col-md-6">
										<div class="custom_button">
											<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-right:10px;margin-top: 20px;">Show</button>
											<?php if (has_permission_new('K1SalableReport', '', 'export')) {
											?>
											<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-right: 10px;margin-top: 20px;"><span>Export to Excel</span></a>
											<?php } ?>
											
											<?php if (has_permission_new('K1SalableReport', '', 'print')) {
											?>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-right: 10px;margin-top: 20px;">Print</a>
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
	$(document).ready(function()
	{
	     var FromDate = $("#from_date").val();
		 var ToDate = $("#to_date").val();
		 var ReportBy = $("#reportby").val();
		 
		 load_data(FromDate,ToDate,null,null,ReportBy);
	    
		$('#search_data').on('click',function()
		{
		    var FromDate = $("#from_date").val();
		    var ToDate = $("#to_date").val();
			var CenterID = $("#CenterID").val();
			var ItemID = $("#ItemID").val();   
		    var ReportBy = $("#reportby").val();
		    load_data(FromDate,ToDate,CenterID,ItemID,ReportBy);
		});
		
		function load_data(FromDate,ToDate,CenterID,ItemID,ReportBy)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>K1InventoryMaster/GetSalableReportData",
				dataType:"html",
				method:"POST",
				cache: false,
				data:{FromDate:FromDate,ToDate:ToDate,CenterID:CenterID,ItemID:ItemID,ReportBy:ReportBy},
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
		}
		
		$("#caexcel").click(function(){
			var FromDate = $("#from_date").val();
		    var ToDate = $("#to_date").val();
			var CenterID = $("#CenterID").val();
			var ItemID = $("#ItemID").val();   
		    var ReportBy = $("#reportby").val();
		
				$.ajax({
					url:"<?php echo admin_url(); ?>K1InventoryMaster/export_Salable_report",
					method:"POST",
					data:{
				        FromDate:FromDate,ToDate:ToDate,CenterID:CenterID,ItemID:ItemID,ReportBy:ReportBy},
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
   function printPage() 
   {
        var comp_name = $("#comp_name").val();
        var comp_addr = $("#comp_addr").val();
        var Center_Name = $("#CenterID option:selected").text() || "ALL";
        var ItemGroup = $("#ItemID option:selected").text() || "ALL";
        var ReportBy = $("#reportby option:selected").text() || "ALL";
        var FromDate = $("#from_date").val();
		var ToDate = $("#to_date").val();
        
        var tableContent = document.querySelector('.stock_position');
    
        if (!tableContent) {
            alert('Stock table not found.');
            return;
        }
        
        var tableHTML = tableContent.innerHTML;
        var stylesheet = `
            <style>
                body { font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid black; padding: 5px; }
                .hide_in_print { display: none !important; }
            </style>
        `;

        var heading_data = `
            <table>
                <tr><td colspan="9" style="text-align:center;">${comp_name}</td></tr>
                <tr><td colspan="9" style="text-align:center;">${comp_addr}</td></tr>
                <tr><td colspan="9" style="text-align:center;">Filters:- &nbsp;From Date: ${FromDate} &nbsp;&nbsp; To Date: ${ToDate} &nbsp;&nbsp; Center: ${Center_Name} &nbsp;&nbsp; ReportBy: ${ReportBy} &nbsp;&nbsp; Item Category: ${ItemGroup}</td></tr>
            </table>
        `;
        
        var printContent = `
            ${stylesheet}
            ${heading_data}
            ${tableHTML}
        `;
        
        var newWin = window.open('', '', 'width=900,height=700');
        newWin.document.write('<html><head><title>Print</title></head><body>');
        newWin.document.write(printContent);
        newWin.document.write('</body></html>');
        newWin.document.close();
       
        newWin.onload = function () {
            newWin.focus();
            newWin.print();
            newWin.close();
        };
    }

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