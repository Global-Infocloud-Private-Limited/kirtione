<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 90vh;width:100%;position:relative;top: 0px; }
	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
	.table-daily_report tbody th { position: sticky; left: 0; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
    
	.for-item-id{
    position: sticky !important;
    left: 0;
    width: 43px;
    background-color:#fff;
    }
	.for-item-name{
    position: sticky;
    width: 81px;
    left: 0px;
    background-color:#fff;
    }
    
	.for-item-idth{
    position: sticky !important;
    left: 0;
    width: 43px;
    }
    
    .for-item-nameth{
	position: sticky;
    width: 81px;
    left: 0px;
    }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12 text-centerr"  >
								<nav aria-label="breadcrumb" >
									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
										<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
										<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>MSP Rate Master</b></li>
										
									</ol>
								</nav>
								<hr style="margin-Bottom:12px !important;">
							</div>
							
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group"><small class="req text-danger">* </small> 
													<label for="Commodity" class="control-label">Select Item</label>
													<select class="selectpicker" name="Commodity" data-live-search="true" id="Commodity" data-width="100%">
														<option value="">Non Selected</option>
														<?php
															foreach($commodity as $value){
															?>
															<option value="<?php echo $value['ItemID'];?>"><?php echo strtoupper($value['ItemName']);?></option>
															<?php
															}
														?>
													</select>
												</div>
											</div>
											
											
											<div class="col-md-2"><small class="req text-danger">* </small>
												<label for="new_rate" class="control-label">New rate</label>
												<input type="text"  onkeypress="return isNumber(event)" name="new_rate" id="new_rate" class="form-control" value="" >
											</div>
											
											<div class="col-md-2">
												
												<?php if (has_permission_new('MSPRate', '', 'edit')) {
												?>
												<button type="button" class="btn btn-info updateBtn" style="margin-top: 20px;">Update</button>
												<?php
													}else{
												?>
												<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
												<?php
												}?>
												
											</div>
										</div>
									</div>
									<?php
										//if(is_admin()){
									?>
									
									<?php //} ?>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							
							<div class="col-md-5">
								<div class="table-daily_report tableFixHead2">
									<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
										
										<thead>
											<tr>
												<!--<th style="text-align:left;" class="for-item-idth">ItemID</th>-->
												<th style="text-align:left;" class="for-item-nameth">Item Name</th>
												<th style="text-align:center;width:50px;font-size:10px;" colspan=""><b>MSP Rate</b></th>
												<th style="text-align:center;width:50px;font-size:10px;" colspan=""><b>Last Updated By</b></th>
												<th style="text-align:center;width:50px;font-size:10px;" colspan=""><b>Last Updated Time</b></th>
												
											</tr>
										</thead>
										<tbody id="rate_update_table" >
											
											<?php
												foreach($commodity as $ItemID1 => $ItemValue1){
												?>
												<tr>
													<!--<td class="for-item-id"><?php echo $ItemValue1["ItemID"]; ?></td>-->
													<td class="for-item-name"><?php echo strtoupper($ItemValue1["ItemName"]); ?></td>
													<?php
														
														$rate = "";
														$css = '';
														$staff = '';
														$date = '';
														foreach($Rate as $rateKey =>$RateValue){
															if($RateValue["ItemID"] == $ItemValue1["ItemID"]){
																$rate = $RateValue["Rate"];
																$staff = $RateValue["firstname"];
																$date = $RateValue["TransDate"];
															}
														}
														
													?>
													<td style="text-align:center;font-weight:500;<?php echo $css;?>">
														<input type="text" onkeypress="return isNumber(event)" style="border:none;height:25px;width:100%;<?php echo $css;?>" id="<?php echo $ItemValue1['ItemID']; ?>" name="<?php echo $ItemValue1['ItemID']; ?>" value = "<?php echo $rate;?>" >
													</td>
													<td style="text-align:center;font-weight:500;">
														<?= $staff;?>
													</td>
													<td style="text-align:center;font-weight:500;">
														<?= _d($date);?>
													</td>
													<?php
														
														
													?>
													
												</tr>
												<?php
												}
											?>
										</tbody>
									</table>   
									
								</div>
								
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 btn-bottom-toolbar text-left" style="margin-left:0px;">
								
								<?php if (has_permission_new('MSPRate', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtnAll" style="margin-top: 20px;">Update All</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update All</button>
								<?php
								}?>
								
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php init_tail(); ?>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
	<script>
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
				return false;
			}
			return true;
		}
	</script>
	<script>
		function toggle(source) {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i] != source)
				checkboxes[i].checked = source.checked;
				//alert('hello');
			}
		}
	</script>
	<script>
		$(document).ready(function(){
			$('input:checkbox').change(function () {
				alert('check me');
			})
			
			$('#Commodity').on('change', function() {
				var ItemID = $(this).val();
				var url = "<?php echo admin_url(); ?>rate_master/GetItemWiseCenter";
				jQuery.ajax({
					type: 'POST',
					url:url,
					data: {ItemID: ItemID},
					dataType:'json',
					success: function(data) {
						$("#CenterSelect").find('option').remove();
						$("#CenterSelect").selectpicker("refresh");
						for (var i = 0; i < data.length; i++) {
							$("#CenterSelect").append(new Option(data[i].CenterName, data[i].CenterID));
						}
						$('.selectpicker').selectpicker('refresh');
					}
				});
			});
			
			
			
			$('.updateBtn').click(function(){
				ItemID = $("#Commodity").val();
				new_rate = $("#new_rate").val();
				if(ItemID == ""){
					alert('please select item');
					}else if(new_rate == ""){
					alert('please enter new rate');
					}else{
					$.ajax({
						url:"<?php echo admin_url(); ?>rate_master/UpdateRateMSPSingle",
						method:"POST",
						data:{ItemID:ItemID,new_rate:new_rate}, 
						dataType:'json',
						success: function(data){
							if(data == '1'){
								Swal.fire({
									position: 'top-end',
									title: 'New Rate Updated!',
									padding: '5px',
									icon: 'success',
									timer: 3000,
									showConfirmButton: false,
									timerProgressBar: false,
								})  
								}else{
								Swal.fire({
									position: 'top-end',
									title: 'Rate not updated',
									padding: '5px',
									icon: 'warning',
									timer: 3000,
									showConfirmButton: false,
									timerProgressBar: false,
								})
							}
							window.location.reload();
						}
					});
				}
			});
			
			$('.updateBtnAll').click(function(){
				// Get the tbody element by its ID
				var tbody = document.getElementById("rate_update_table");
				
				// Get all input elements within the tbody that have a type of "text"
				var inputs = tbody.querySelectorAll('input[type="text"]');
				// Loop through the inputs and get their values and id attributes
				
				var formdata = {};
				inputs.forEach(function(input) {
					formdata[input.id] = input.value; // Use the input ID as key and its value as the value
				});
				
				$.ajax({
					url:"<?php echo admin_url(); ?>rate_master/UpdateRateMSP",
					method:"POST",
					data:{formdata:formdata}, 
					dataType:'json',
					success: function(data){
						if(data == '1'){
							Swal.fire({
								position: 'top-end',
								title: 'New Rate Updated!',
								padding: '5px',
								icon: 'success',
								timer: 3000,
								showConfirmButton: false,
								timerProgressBar: false,
							})  
							}else{
							Swal.fire({
								position: 'top-end',
								title: 'Rate not updated',
								padding: '5px',
								icon: 'warning',
								timer: 3000,
								showConfirmButton: false,
								timerProgressBar: false,
							})
						}
						window.location.reload();
					}
				});
			});
			
		});
	</script>
	
