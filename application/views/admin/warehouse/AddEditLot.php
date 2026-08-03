<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<?php //echo form_open('admin/accounts_master/manage_account_group',array('id'=>'account_group_form')); ?>
						<nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Warehouse</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Warehouse Lot Management</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Group...</div>
								<div class="searchh4" style="display:none;">Please wait update Group...</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="LotID">LotID</label>
									<input type="text" name="LotID" id="LotID" class="form-control text-uppercase" value="" >
									
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group">
									<label for="LotName">Lot Name</label>
									<input type="text" name="LotName" id="LotName" class="form-control" value="">
								</div>
							</div>
							<?php /*
							<div class="col-md-2">
								<div class="form-group">
									<label for="WHName">WH Name</label>
									<select name="WHName" id="WHName" onchange="GetWarehouseStackList(this.value)" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
										<option value="">Non selected</option>
										<?php
											foreach ($WHList as $key => $value) {
												# code...
											?>
											<option value="<?php echo $value["WHID"];?>"><?php echo $value["w_name"];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							*/ ?>
							<div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Warehousename" class="control-label">Warehouse</label>
                                    <select class="selectpicker display-block" data-width="100%" id="warehouse" name="warehouse" data-none-selected-text="<?php echo 'Select Warehouse'; ?>" data-live-search="true">
                                        <option value=""></option>    
                                        <?php foreach($warehouses as $w) { ?>
                                            <option value="<?= $w['AccountID'] ?>"><?= $w['w_name'] ?></option>    
                                        <?php } ?>
                                    </select>
    							</div>
						    </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="ChambersName" class="control-label">Chamber</label>
                                    <select class="selectpicker display-block" data-width="100%" id="chamber" name="chamber" data-none-selected-text="<?php echo 'Select Chamber'; ?>" data-live-search="true">
                                        <option value=""></option>    
                                    </select>
    							</div>
						    </div>
							<!--<div class="col-md-2">-->
							<!--	<div class="form-group">-->
							<!--		<label for="StackName">Stack Name</label>-->
							<!--		<select name="StackName" id="StackName" onchange="GetStackLotSpace(this.value)" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">-->
							<!--			<option value="">Non selected</option>-->
							<!--		</select>-->
							<!--	</div>-->
							<!--</div>-->
							<div class="col-md-2">
								<div class="form-group">
									<label for="StackName">Stack Name</label>
									<select name="stack" id="stack"  class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
										<option value="">Non selected</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="allocated_area">Allocated Area</label>
									<input type="text" name="allocated_area" id="allocated_area" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="empty_area">Empty Area</label>
									<input type="text" name="empty_area" id="empty_area" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="length">Length</label>
									<input type="text" name="length" id="length" onkeyup="GetCalculation()" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
									<input type="hidden" name="length_hidden" id="length_hidden" class="form-control" value="0" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="width">Width</label>
									<input type="text" name="width" id="width" onkeyup="GetCalculation()" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
									<input type="hidden" name="width_hidden" id="width_hidden" class="form-control" value="0" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="height">Height</label>
									<input type="text" name="height" id="height" onkeyup="GetCalculation()" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="margin">Margin</label>
									<input type="text" name="margin" id="margin" onkeyup="GetCalculation()" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="total_area">Total Area</label>
									<input type="text" name="total_area" id="total_area" onkeyup="GetCalculation()" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="utilize_area">Utilize Area</label>
									<input type="text" name="utilize_area" id="utilize_area" onkeyup="GetCalculation()" class="form-control" value="" readonly>
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group">
									<label for="volume">Volume</label>
									<input type="text" name="volume" id="volume" class="form-control" value="" readonly>
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group">
									<label for="capacity">Capacity</label>
									<input type="text" name="capacity" id="capacity" class="form-control" value="" readonly>
								</div>
							</div>
							
						</div>
						
						<div class="row"> 
						
							
							
							
							
							
						</div>
						
						
						<div class="row"> 
							<!--<div class="col-md-12">
								<div class="add_button" id="add_button">
								<?php
									if( has_permission_new('account_groups', '', 'create')) { ?>
									<button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Add</button>
									<?php }else{
										echo "<h5 style='color:red'>Not permitted to add record..</h5>";
									} ?>
									</div>
									<div class="edit_button" id="edit_button">
									<?php
										if( has_permission_new('account_groups', '', 'edit')) { ?>
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Update</button>
										<?php }else{
											echo "<h5 style='color:red'>Not permitted to edit record..</h5>";
										} ?>
										</div>
							</div>-->
							
							<div class="col-md-12">
								<?php if (has_permission_new('WHLotMgmt', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission_new('WHLotMgmt', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								}?>
								
								<button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
						</div>
						
						<?php //echo form_close(); ?>
						<div class="clearfix"></div>
						
						<div class="modal fade LotListTable" id="LotListTable" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Lot Plan List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <?php if (has_permission_new('WHLotMgmt', '', 'export')) {
								            ?>
                                            <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                                            <?php } ?>
                                            
                                            <?php if (has_permission_new('WHLotMgmt', '', 'print')) {
								            ?>
                                            <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;margin-left:10px;"  onclick="printPage();">Print</a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Warehouse</label>
                                                <select class="selectpicker display-block" data-width="100%" id="warehouse1" name="warehouse1" data-none-selected-text="<?php echo 'Select Warehouse'; ?>" data-live-search="true">
                                                    <option value=""></option>    
                                                    <?php foreach($warehouses as $w) { ?>
                                                        <option value="<?= $w['AccountID'] ?>"><?= $w['w_name'] ?></option>    
                                                        <?php } ?>
                                                    </select>

                                            <span id="salerate_error_message" style="color:red"></span>
                                        </div>

                                        </div>

                                      <div class="col-md-2">
                                         <div class="form-group">
                                                <label for="ChambersName" class="control-label">Chamber</label>
                                                <select class="selectpicker display-block" data-width="100%" id="chamber1" name="chamber1" data-none-selected-text="<?php echo 'Select Chamber'; ?>" data-live-search="true">
                                                    <option value=""></option>    
                                                </select>
    							          </div>
                                        </div>
                                         <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="StackName">Stack Name</label>
                                            <select name="stack1" id="stack1"  class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
                                                <option value="">Non selected</option>
                                            </select>
								       </div>
							        </div>
                                        <div class=" col-md-1">
                                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>

                                        </div>
                                    </div>
										<div class="table-LotListTable tableFixHead2">
											<table class="tree table table-striped table-bordered table-LotListTable tableFixHead2" id="table_LotListTable" width="100%">
												<thead>
													<tr style="display:none;">
														<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
													</tr>
													<tr>
														<th style="text-align:left;">Lot ID</th>
														<th style="text-align:left;">Lot Name</th>
														<th style="text-align:left;">Stack ID</th>
														<th style="text-align:left;">Stack Name</th>
														<th style="text-align:left;">CHID</th>
														<th style="text-align:left;">Chamber Name</th>
														<th style="text-align:left;">WHID</th>
														<th style="text-align:left;">Warehouse Name</th>
														<th style="text-align:left;">Length</th>
														<th style="text-align:left;">Width</th>
														<th style="text-align:left;">Height</th>
														<th style="text-align:left;">Margin</th>
														<th style="text-align:left;">Total Area</th>
														<th style="text-align:left;">Capacity</th>
													</tr>
												</thead>
												<tbody id="stackPlan_List_body">
													
												</tbody>
											</table>   
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
									</div>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php init_tail(); ?>
<!--new update -->
<script>

    $('#warehouse').on('change', function(){
        var whid = $(this).val();
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchChambers",
			dataType:"JSON",
			method:"POST",
			data:{
				whid : whid,
			},
			success:function(data){
				console.log(data);
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
				    $('#chamber').html(data);
				    
				    $('.selectpicker').selectpicker('refresh');
				}
			}
		}); 
    });
    
    $('#chamber').on('change', function(){
        var chid = $(this).val();
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchAllStackDetails",
			dataType:"JSON",
			method:"POST",
			data:{
				chid : chid,
			},
			success:function(data){
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
				    $('#stack').html(data);
				    
				    $('.selectpicker').selectpicker('refresh');
				    // GetWarehouseStackSpace(data.WHID);
				    // $('#length').val(data.length);
				    // $('#width').val(data.width);
				    // $('#height').val(data.height);
				    // $('#margin').val(data.margin);
				    // $('#total_area').val(data.total_area);
				    // $('#utilize_area').val(data.utilize_area);
				    // $('#volume').val(data.volume);
				    // $('#capacity').val(data.capacity);
				}
			}
		}); 
    });
    
    $('#stack').on('change', function(){
        var stckid = $(this).val();
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchStackDetails",
			dataType:"JSON",
			method:"POST",
			data:{
				stckid : stckid,
			},
			success:function(data){
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
				    GetWarehouseStackSpace(data.WHID);
				    $('#length').val(data.length);
				    $('#width').val(data.width);
				    $('#height').val(data.height);
				    $('#margin').val(data.margin);
				    $('#total_area').val(data.total_area);
				    $('#utilize_area').val(data.utilize_area);
				    $('#volume').val(data.volume);
				    $('#capacity').val(data.capacity);
				}
			}
		}); 
    });
    
    function GetWarehouseStackSpace(wh){
		if(wh !== "")
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/GetWarehouseStackSpace",
				dataType:"JSON",
				method:"POST",
				data:{
					wh : wh,
				},
				success:function(data){
					console.log(data);
					if(empty(data)){
						alert('Data Not Found');
					}
					else
					{
						var allocated_area = data.AllocatedSpace;
						var emptySpace = parseFloat(data.WHUtilizeSpace) - parseFloat(allocated_area);
						$('#allocated_area').val(allocated_area);
						$('#empty_area').val(emptySpace);
					}
				}
			}); 
		}
		else
		{
			$(':input').val('');
			$('select[name=WHName]').val('').prop('disabled', false);
			$('.selectpicker').selectpicker('refresh');
		}
		
	}
    
	function GetWarehouseStackList(wh){
		if(wh !== "")
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/GetWarehouseStackList",
				dataType:"JSON",
				method:"POST",
				data:{
					wh : wh,
				},
				success:function(data){
					$('#allocated_area').val('');
					$('#empty_area').val('');
					if(empty(data)){
						options = "<option value=''>Non selected</option>";
						$('select[name=StackName]').html(options).prop('disabled', false);
						$('.selectpicker').selectpicker('refresh');
					}
					else
					{
						var options = "<option value=''>Non selected</option>";
						$.each(data, function(index, value) {
							options += "<option value='" + value.StackID + "'>" + value.StackName + "</option>";
						});
						$('select[name=StackName]').html(options).prop('disabled', false);
						$('.selectpicker').selectpicker('refresh');
					}
				}
			}); 
		}
		else
		{
			$(':input').val('');
			$('select[name=WHName]').val('').prop('disabled', false);
			options = "<option value=''>Non selected</option>";
			$('select[name=StackName]').html(options).prop('disabled', false);
			$('.selectpicker').selectpicker('refresh');
		}
		
	}
	
	function GetStackLotSpace(StackID){
		if(StackID !== "")
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/GetStackLotSpace",
				dataType:"JSON",
				method:"POST",
				data:{
					StackID : StackID,
				},
				success:function(data){
					console.log(data);
					if(empty(data)){
						alert('Stack Not Found');
						$(':input').val('');
						$('select[name=StackName]').val('').prop('disabled', false);
						$('.selectpicker').selectpicker('refresh');
						
					}
					else
					{
						var allocated_area = data.AllocatedSpace;
						var emptySpace = parseFloat(data.LotUtilizeSpace) - parseFloat(allocated_area);
						$('#allocated_area').val(allocated_area);
						$('#empty_area').val(emptySpace);
					}
				}
			}); 
		}
		else
		{
			$(':input').val('');
			$('#empty_area').val('');
			$('select[name=StackName]').val('').prop('disabled', false);
			$('.selectpicker').selectpicker('refresh');
		}
		
	}
</script>
<script>
    function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}  
</script>
<script>
    function fetchChambers(whid,chid){
        var whid = whid;
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchChambers",
			dataType:"JSON",
			method:"POST",
			data:{
				whid : whid,
			},
			success:function(data1){
				if(empty(data1)){
					alert('Data Not Found');
				}
				else
				{
				    $('#chamber').html(data1);
				    $('.selectpicker').selectpicker('refresh');
				    $('select[name=chamber]').val(chid).prop('disabled', true);
				    $('.selectpicker').selectpicker('refresh');
				}
			}
		}); 
    }
    
    function fetchStack(chid,stckid){
        var chid = chid;
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchAllStackDetails",
			dataType:"JSON",
			method:"POST",
			data:{
				chid : chid,
			},
			success:function(data){
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
				    $('#stack').html(data);
				    $('.selectpicker').selectpicker('refresh');
				    $('select[name=stack]').val(stckid).prop('disabled', true);
				    $('.selectpicker').selectpicker('refresh');
				}
			}
		}); 
    }
    
    function GetCalculation(){
		length_hidden = $("#length_hidden").val();
		width_hidden = $("#width_hidden").val();
		
		if(length_hidden == "")
		{
			length_hidden =0;
		}
		if(width_hidden == "")
		{
			width_hidden =0;
		}
		
		length = $("#length").val();
		Width = $("#width").val();
		height = $("#height").val();
		margin = $("#margin").val();
		
		empty_area = $("#empty_area").val();
		
		if(length == "")
		{
			length = 1;
		}
		
		if(Width == "")
		{
			Width = 1;
		}
		
		if(height == "")
		{
			height = 1;
		}
		
		if(margin == "")
		{
			margin = 0;
		}
		
		margin = parseFloat(margin)*2;
		var area = parseFloat(length)*parseFloat(Width)*parseFloat(height);
		var volume = parseFloat(length)*parseFloat(Width)*parseFloat(height);
		if(empty_area >= area){
		    $("#total_area").val(area);
    		$("#volume").val(volume);
    		var newlength = length-margin;
    		var newWidth = Width-margin;
    		var newHeight = height-margin;
    		
    		var utilizearea = newWidth*newlength*newHeight;
    		$("#capacity").val(utilizearea/5);
    		$("#utilize_area").val(utilizearea);
			}else{
		    alert('please enter value less than area value');
			$("#width").val(width_hidden);
			$("#length").val(length_hidden);
			GetCalculation()
		}
	}
	
	$('.saveBtn').click(function(){
        
        var LotID =	$('#LotID').val();
		var LotName =   $('#LotName').val();
// 		var WHName =	$('#WHName').val();
// 		var StackName =	$('#StackName').val();
		
		
		var warehouse =	$('#warehouse').val();
		var chamber =	$('#chamber').val();
		var stack =	$('#stack').val();
		
		var length =  $('#length').val();
		var width = $('#width').val();
		var height = $('#height').val();
		var margin =  $('#margin').val();
		var total_area =  $('#total_area').val();
		var utilize_area =   $('#utilize_area').val();
		var volume = $('#volume').val();
		var capacity =  $('#capacity').val();
		if(LotID == "" || LotName == ""  || length == "" || warehouse == "" || chamber == ""  || stack == "" || width == "" || height == "" || margin == "" || total_area == "" || utilize_area == "" || volume == "" || capacity == ""){
			alert("Enter Required Details !")
		}
		else{
			
            $.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/SaveStackLotPlan",
				dataType:"JSON",
				method:"POST",
				data:{
					LotID : LotID,
					LotName : LotName,
					warehouse : warehouse,
					stack : stack,
					chamber : chamber,
					length : length,
					width : width,
					height : height,
					margin : margin,
					total_area : total_area,
					utilize_area : utilize_area,
					volume : volume,
					capacity : capacity,
				},
				success:function(data){
					if(data == true){
						$(':input').val('');
						$('select[name=warehouse]').val('').prop('disabled', false);
            			options = "<option value=''>Non selected</option>";
            			$('select[name=chamber]').html(options).prop('disabled', false);
            			$('select[name=stack]').html(options).prop('disabled', false);
						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						alert('Record created successfully...');
					}
				}
			}); 
		}
	});
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		$("#LotID").dblclick(function(){
			$('#LotListTable').modal('show');
			$('#LotListTable').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
				var AccountID = '';
				$.ajax({
					url:"<?php echo admin_url(); ?>Warehouse/GetLotPlanList",
					//dataType:"JSON",
					method:"POST",
					cache: false,
					data:{AccountID:AccountID,},
					success:function(data){
						if(empty(data)){
							
							}else{
							$("#stackPlan_List_body").html(data);
							$('.get_AccountID').click(function(){ 
								LotID = $(this).attr("data-id");
								$.ajax({
									url: "<?php echo admin_url(); ?>Warehouse/GetSingleLotPlan",
									dataType:"JSON",
									method:"POST",
									data:{
										LotID:LotID,
									},
									success:function(data){
										var allocated_area = data.AllocatedSpace;
										var emptySpace = parseFloat(data.WHUtilizeSpace) - parseFloat(allocated_area);
										$('#allocated_area').val(allocated_area);
										$('#empty_area').val(emptySpace);
										$('#LotID').val(data.LOTID);
										$('#LotName').val(data.LotName);
										$('#length').val(data.length);
										$('#width').val(data.width);
										$('#length_hidden').val(data.length);
										$('#width_hidden').val(data.width);
										$('#height').val(data.height);
										$('#margin').val(data.margin);
										$('#total_area').val(data.total_area);
										$('#utilize_area').val(data.utilize_area);
										$('#volume').val(data.volume);
										$('#capacity').val(data.capacity);
								// 		$('select[name=WHName]').val(data.WHID).prop('disabled', true);
								// 		options = "<option value='" + data.StackID + "'>" + data.StackName + "</option>";
								// 		$('select[name=StackName]').html(options).prop('disabled', true);
								
								        $('select[name=warehouse]').val(data.WHID).prop('disabled', true);
								        fetchChambers(data.WHID,data.CHID);
								        fetchStack(data.CHID,data.StackID);
										$('.selectpicker').selectpicker('refresh');
										$('.saveBtn').hide();
										$('.updateBtn').show();
										$('.saveBtn2').hide();
										$('.updateBtn2').show();
										
									},
								});
								$('#LotListTable').modal('hide');
							})
						}
					}
				});
			})
		});
		
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		
		// Focus on LotID
		$('#LotID').on('focus',function(){
			$(':input').val('');
			$('select[name=warehouse]').val('').prop('disabled', false);
			options = "<option value=''>Non selected</option>";
			$('select[name=chamber]').html(options).prop('disabled', false);
			$('select[name=stack]').html(options).prop('disabled', false);
			$('.selectpicker').selectpicker('refresh');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		});
		
		// Cancel selected data
		$(".cancelBtn").click(function(){
			$(':input').val('');
			$('select[name=warehouse]').val('').prop('disabled', false);
			options = "<option value=''>Non selected</option>";
			$('select[name=chamber]').html(options).prop('disabled', false);
			$('select[name=stack]').html(options).prop('disabled', false);
			$('.selectpicker').selectpicker('refresh');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		});
		
		// Get Group Detail by Group ID
		$('#LotID').on('blur',function(){
			var LotID = $(this).val();
			if(LotID == ""){
				$('.saveBtn').show();
				$('.saveBtn2').show();
				$('.updateBtn').hide();
				$('.updateBtn2').hide();
				$(':input').val('');
				$('select[name=WHName]').val('');
				$('select[name=StackName]').val('');
				$('.selectpicker').selectpicker('refresh');
				}else{
				$.ajax({
					url: "<?php echo admin_url(); ?>Warehouse/GetSingleLotPlan",
					dataType:"JSON",
					method:"POST",
					data:{
						LotID:LotID,
					},
					success:function(data){
						if(empty(data)){
							$('.saveBtn').show();
							$('.saveBtn2').show();
							$('.updateBtn').hide();
							$('.updateBtn2').hide();
							$('#allocated_area').val('');
							$('#empty_area').val('');
							$('#LotName').val('');
							$('#length').val('');
							$('#width').val('');
							$('#height').val('');
							$('#margin').val('');
							$('#total_area').val('');
							$('#utilize_area').val('');
							$('#volume').val('');
							$('#capacity').val('');
							$('select[name=WHName]').val('').prop('disabled', false);
							$('.selectpicker').selectpicker('refresh');
							}else{
							var allocated_area = data.AllocatedSpace;
							var emptySpace = parseFloat(data.WHUtilizeSpace) - parseFloat(allocated_area);
							$('#allocated_area').val(allocated_area);
							$('#empty_area').val(emptySpace);
							$('#LotID').val(data.LOTID);
							$('#LotName').val(data.LotName);
							$('#length').val(data.length);
							$('#width').val(data.width);
							$('#length_hidden').val(data.length);
							$('#width_hidden').val(data.width);
							$('#height').val(data.height);
							$('#margin').val(data.margin);
							$('#total_area').val(data.total_area);
							$('#utilize_area').val(data.utilize_area);
							$('#volume').val(data.volume);
							$('#capacity').val(data.capacity);
				// 			$('select[name=WHName]').val(data.WHID).prop('disabled', true);
				// 			options = "<option value='" + data.StackID + "'>" + data.StackName + "</option>";
				// 			$('select[name=StackName]').html(options).prop('disabled', true);
            				$('select[name=warehouse]').val(data.WHID).prop('disabled', true);
					        fetchChambers(data.WHID,data.CHID);
					        fetchStack(data.CHID,data.StackID);
							$('.selectpicker').selectpicker('refresh');
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						}
					},
				});
			}
		});
		
		// Update Exiting Item
        $('.updateBtn').on('click',function(){ 
			var LotID =	$('#LotID').val();
			var LotName =   $('#LotName').val();
			var length =  $('#length').val();
			var width = $('#width').val();
			var height = $('#height').val();
			var margin =  $('#margin').val();
			var total_area =  $('#total_area').val();
			var utilize_area =   $('#utilize_area').val();
			var volume = $('#volume').val();
			var capacity =  $('#capacity').val();
			if(LotID == "" || LotName == "" || length == "" || width == "" || height == "" || margin == "" || total_area == "" || utilize_area == "" || volume == "" || capacity == ""){
				alert("Enter Required Details !")
			}
			else{
				
				$.ajax({
					url:"<?php echo admin_url(); ?>Warehouse/UpdateStackLotPlan",
					dataType:"JSON",
					method:"POST",
					data:{
						LotID : LotID,
						LotName : LotName,
						length : length,
						width : width,
						height : height,
						margin : margin,
						total_area : total_area,
						utilize_area : utilize_area,
						volume : volume,
						capacity : capacity,
					},
					success:function(data){
						if(data == true){
							$(':input').val('');
							$('select[name=warehouse]').val('').prop('disabled', false);
                			options = "<option value=''>Non selected</option>";
                			$('select[name=chamber]').html(options).prop('disabled', false);
                			$('select[name=stack]').html(options).prop('disabled', false);
							$('.selectpicker').selectpicker('refresh');
							$('.saveBtn').show();
							$('.updateBtn').hide();
							$('.saveBtn2').show();
							$('.updateBtn2').hide();
							alert('Record Updated successfully...');
						}
					}
				}); 
			}
		});
		
	});
	
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_AccountGroup");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						} else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3){
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else{
								tr[i].style.display = "none";
							} 
						}
					}
				}    
			}
		}
	}
</script>

<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Lot Plan List</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
<script>
$("#caexcel").click(function(){
    var data_val = "data";
    $.ajax({
        url:"<?php echo admin_url(); ?>Warehouse/export_lotplanlist",
        method:"POST",
        data:{data_val:data_val,},
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});
</script>


<script>

    $('#search_data').on('click', function () {
        var warehouse = $("#warehouse1").val();
        var chamber = $("#chamber1").val();
        var stack = $("#stack1").val();

            $.ajax({
                url: "<?php echo admin_url(); ?>Warehouse/Lotplanlist",
                dataType: "json",
                method: "POST",
                data: {warehouse1: warehouse,chamber1:chamber,stack1:stack},
                beforeSend: function () {
                    $('#stackPlan_List_body').html();
                    $('#searchh22').css('display', 'none');
                    $('#searchh2').css('display', 'block');
                },
                complete: function () {
                    $('#searchh2').css('display', 'none');
                },
                success: function (data) {
                    $('#stackPlan_List_body').html();
                    $('#stackPlan_List_body').html(data);
                    $('.get_AccountID').click(function(){ 
						LotID = $(this).attr("data-id");
						$.ajax({
							url: "<?php echo admin_url(); ?>Warehouse/GetSingleLotPlan",
							dataType:"JSON",
							method:"POST",
							data:{
								LotID:LotID,
							},
							success:function(data){
								var allocated_area = data.AllocatedSpace;
								var emptySpace = parseFloat(data.WHUtilizeSpace) - parseFloat(allocated_area);
								$('#allocated_area').val(allocated_area);
								$('#empty_area').val(emptySpace);
								$('#LotID').val(data.LOTID);
								$('#LotName').val(data.LotName);
								$('#length').val(data.length);
								$('#width').val(data.width);
								$('#length_hidden').val(data.length);
								$('#width_hidden').val(data.width);
								$('#height').val(data.height);
								$('#margin').val(data.margin);
								$('#total_area').val(data.total_area);
								$('#utilize_area').val(data.utilize_area);
								$('#volume').val(data.volume);
								$('#capacity').val(data.capacity);
						        $('select[name=warehouse]').val(data.WHID).prop('disabled', true);
						        fetchChambers(data.WHID,data.CHID);
						        fetchStack(data.CHID,data.StackID);
								$('.selectpicker').selectpicker('refresh');
								$('.saveBtn').hide();
								$('.updateBtn').show();
								$('.saveBtn2').hide();
								$('.updateBtn2').show();
								
							},
						});
						$('#LotListTable').modal('hide');
					})
                    
                }
            });
    });


</script>

<script>
    $('#warehouse1').on('change', function(){
        var whid = $(this).val();
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchChambers",
			dataType:"JSON",
			method:"POST",
			data:{
				whid : whid,
			},
			success:function(data){
				console.log(data);
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
				    $('#chamber1').html(data);
				    
				    $('.selectpicker').selectpicker('refresh');
                    $("#stack1").children().remove();
                    $('.selectpicker').selectpicker('refresh');
				}
			}
		}); 
    });

   

    $('#chamber1').on('change', function(){
        var chid = $(this).val();
        $.ajax({
			url:"<?php echo admin_url(); ?>Warehouse/fetchstackListByChamberID",
			dataType:"JSON",
			method:"POST",
			data:{
				chid : chid,
			},
			success:function(data){
				if(empty(data)){
					alert('Data Not Found');
				}
				else
				{
                    $('#stack1').html(data);
				    
				    $('.selectpicker').selectpicker('refresh')
				}
			}
		}); 
    });


</script>

<style>
    #table_LotListTable td:hover {
    cursor: pointer;
	}
	#table_LotListTable tr:hover {
    background-color: #ccc;
	}
	
    .table-LotListTable          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-LotListTable thead th { position: sticky; top: 0; z-index: 1; }
    .table-LotListTable tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>

<style type="text/css">
	body{
    overflow: hidden;
	}
</style>

