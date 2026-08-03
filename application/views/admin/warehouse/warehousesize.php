<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Warehouse</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Warehouse Chamber Management</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
							</div>
       <!--                     <div class="col-md-2">-->
       <!--                         <div class="form-group">-->
       <!--                             <small class="req text-danger">* </small>-->
       <!--                             <label for="WarehouseID" class="control-label">WHID</label>-->
       <!--                             <input type="text" style="text-transform:uppercase" id="WarehouseID" name="WarehouseID" class="form-control" value="">-->
							<!--	</div>-->
							<!--</div>-->
							
       <!--                     <div class="col-md-4">-->
       <!--                         <div class="form-group">-->
       <!--                             <small class="req text-danger">* </small>-->
       <!--                             <label for="Warehousename" class="control-label">WH Name</label>-->
       <!--                             <input type="text" style="text-transform:uppercase" id="Warehousename" name="Warehousename" class="form-control" readonly value="">-->
							<!--	</div>-->
							<!--</div>-->
						</div>
						<div class="row">
						    <div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Warehousename" class="control-label">CHID</label>
                                    <input type="text" style="text-transform:uppercase" id="chid" name="chid" class="form-control" value="">
    							</div>
						    </div>
						    <div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Warehousename" class="control-label">Chamber Name</label>
                                    <input type="text" style="text-transform:uppercase" id="chambername" name="chambername" class="form-control" value="">
    							</div>

                                
						    </div>
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
						    
						</div>
						<div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="length" class="control-label">Length (m)</label>
                                    <input required type="text" id="length" onkeyup="GetCalculation()" name="length" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="Width" class="control-label">Width (m)</label>
                                    <input required type="text" id="Width" onkeyup="GetCalculation()" name="Width" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="height" class="control-label">Height (Ft)</label>
                                    <input required type="text" id="height" onkeyup="GetCalculation()" name="height" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="margin" class="control-label">Margin</label>
                                    <input required type="text" id="margin" maxlength="2" onkeyup="GetCalculation()" name="margin" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="area" class="control-label">Total Area (sq.m)</label>
                                    <input required type="text" readonly id="area" name="area" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-3" >
                                <div class="form-group">
                                    <label for="volume" class="control-label">Volume</label>
                                    <input required type="text" readonly id="volume" name="volume" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="utilizearea" class="control-label">Utilize Area</label>
                                    <input required type="text" readonly id="utilizearea" name="utilizearea" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							<div class="col-md-3" >
                                <div class="form-group">
                                    <label for="capacity" class="control-label">Capacity(MT)</label>
                                    <input required type="text" readonly id="capacity" name="capacity" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
								</div>
							</div>
							
							
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<?php if (has_permission_new('WHspacemgmt', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php } else{ ?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php } ?>
								<?php if (has_permission_new('WHspacemgmt', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
								<?php }else{ ?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
							    <?php } ?>
								<button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
						</div>
						
						<!------------ Modal ------------->
						<div class="modal fade warehouse_List" id="warehouse_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Chamber List</h4>
									</div>
                                    
									<div class="modal-body" style="padding:0px 5px !important">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <?php if (has_permission_new('WHspacemgmt', '', 'export')) {
								            ?>
                                            <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                                            <?php } ?>
                                            
                                            <?php if (has_permission_new('WHspacemgmt', '', 'print')) {
								            ?>
                                            <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;margin-left:10px;"  onclick="printPage();">Print</a>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-4">
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
                                        <div class="col-md-4">
                                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                                        </div>
                                    </div>
                                    
										<div class="table-StackListTable tableFixHead2">
											<table class="table table-striped table-bordered table-hover" id="table_warehouse_List" width="100%">
												<thead>
													<tr style="display:none;">
														<td colspan="11" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
													</tr>
													<tr>
														<th id="sl" style="text-align:left;">CHID <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
														<th style="text-align:left;">Chamber Name</th>
														<th style="text-align:left;">WHID</th>
														<th style="text-align:left;">Warehouse Name</th>
														<th style="text-align:left;">Length</th>
														<th style="text-align:left;">Width</th>
														<th style="text-align:left;">Height</th>
														<th style="text-align:left;">Margin</th>
														<th style="text-align:left;">Total Area</th>
														<th style="text-align:left;">Utilize Area</th>
														<th style="text-align:left;">Volume</th>
														<th style="text-align:left;">Capacity</th>
													</tr>
												</thead>
												<tbody id="warehouse_List_body">
													
												</tbody>
											</table>   
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
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
<?php init_tail(); ?>
<script>
	function GetCalculation(){
		WarehouseID = $("#WarehouseID").val();
		length = $("#length").val();
		Width = $("#Width").val();
		height = $("#height").val();
		margin = $("#margin").val();
		
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
		var area = parseFloat(length)*parseFloat(Width);
		var volume = parseFloat(length)*parseFloat(Width)*parseFloat(height);
		$("#area").val(Math.round(volume).toFixed(2));
		$("#volume").val(Math.round(volume).toFixed(2));
		var newlength = length-margin;
		var newWidth = Width-margin;
		var newHeight = height-margin;
		
		var utilizearea = newWidth*newlength*newHeight;
		$("#capacity").val(Math.round(utilizearea/5).toFixed(2));
		$("#utilizearea").val(Math.round(utilizearea).toFixed(2));
	}
</script>
<script>
    $(document).ready(function(){
		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('.saveBtn2').show();
		$('.updateBtn2').hide();
		$('#diameter_div').hide();
		$('#plinth_height_div').hide();
		$('#cooling_system_div').hide();
		$('#insulation_div').hide();
		$('#temprature_div').hide();
		$('#no_of_lock_div').hide();
		$('#lock_point_functional_div').hide();
		$('#no_of_chambers_div').hide();
		$('#no_of_floors_div').hide();
		$('#no_of_shutter_div').hide();
		$('#no_of_window_div').hide();
		$('#no_of_ventilator_div').hide();
		$('#insurance_by_div').hide();
		$('#insurance_compound_div').hide();
		$('#policy_div').hide();
		$('#assured_sum_div').hide();
		$('#validity_div').hide();
		$('#watchman_div').hide();
		$('#security_type_div').hide();
		$('#weigh_bridge_type_div').hide();
		$('#no_of_weighbridge_div').hide();
		$('#distance_from_weighbridge_div').hide();
		$('#weigh_bridge_type_div').hide();
	});    
</script>
<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_warehouse_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            td8 = tr[i].getElementsByTagName("td")[8];
            td9 = tr[i].getElementsByTagName("td")[9];
            td10 = tr[i].getElementsByTagName("td")[10];
            td11 = tr[i].getElementsByTagName("td")[11];
			if(td) {
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
								}else if(td4){
								txtValue = td4.textContent || td4.innerText;
								if (txtValue.toUpperCase().indexOf(filter) > -1) {
									tr[i].style.display = "";
									}else if(td5){
									txtValue = td5.textContent || td5.innerText;
									if (txtValue.toUpperCase().indexOf(filter) > -1) {
										tr[i].style.display = "";
										}else if(td6){
										txtValue = td6.textContent || td6.innerText;
										if (txtValue.toUpperCase().indexOf(filter) > -1) {
											tr[i].style.display = "";
											}else if(td7){
											txtValue = td7.textContent || td7.innerText;
											if (txtValue.toUpperCase().indexOf(filter) > -1) {
												tr[i].style.display = "";
												}else if(td8){
												txtValue = td8.textContent || td8.innerText;
												if (txtValue.toUpperCase().indexOf(filter) > -1) {
													tr[i].style.display = "";
													}else if(td9){
													txtValue = td9.textContent || td9.innerText;
													if (txtValue.toUpperCase().indexOf(filter) > -1) {
														tr[i].style.display = "";
														}else if(td10){
														txtValue = td10.textContent || td10.innerText;
														if (txtValue.toUpperCase().indexOf(filter) > -1) {
															tr[i].style.display = "";
															}else if(td11){
															txtValue = td11.textContent || td11.innerText;
															if (txtValue.toUpperCase().indexOf(filter) > -1) {
																tr[i].style.display = "";
																}else{
																tr[i].style.display = "none";
															} 
														}
													}}}}}}}
						}
					}
				}     
			}
		}
	}
</script>
<script>
    $('.saveBtn').click(function(){
        
//      var WarehouseID =	$('#WarehouseID').val();
// 		var Warehousename =   $('#Warehousename').val();
		var warehouse =  $('#warehouse').val();
		var chid =  $('#chid').val();
		var chambername =  $('#chambername').val();

		var length =  $('#length').val();
		var Width = $('#Width').val();
		var height = $('#height').val();
		var margin =  $('#margin').val();
		var area =  $('#area').val();
		var utilizearea =   $('#utilizearea').val();
		var volume = $('#volume').val();
		var capacity =  $('#capacity').val();
		if(warehouse == "" || chid == "" || chambername == ""|| length == "" || Width == "" || height == "" || margin == "" || area == "" || utilizearea == "" || volume == "" || capacity == ""){
			alert("Enter Required Details !")
		}
		else{
			
            $.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/SaveWarehouseSize",
				dataType:"JSON",
				method:"POST",
				data:{
				// 	WarehouseID : WarehouseID,
				// 	Warehousename : Warehousename,
					warehouse : warehouse,
					chid : chid,
					chambername : chambername,
					length : length,
					Width : Width,
					height : height,
					margin : margin,
					area : area,
					utilizearea : utilizearea,
					volume : volume,
					capacity : capacity,
				},
				success:function(data){
					if(data == true){
						$(':input').val('');
						$('select[name=warehouse]').val('').prop('disabled', false);
                    	$('.selectpicker').selectpicker('refresh');
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
<script>
    $('.updateBtn').click(function(){
// 		var WarehouseID =	$('#WarehouseID').val();
// 		var Warehousename =   $('#Warehousename').val();
		
		var warehouse =  $('#warehouse').val();
		var chid =  $('#chid').val();
		var chambername =  $('#chambername').val();
		
		var length =  $('#length').val();
		var Width = $('#Width').val();
		var height = $('#height').val();
		var margin =  $('#margin').val();
		var area =  $('#area').val();
		var utilizearea =   $('#utilizearea').val();
		var volume = $('#volume').val();
		var capacity =  $('#capacity').val();
		if(warehouse == "" || chid == "" || chambername == "" || length == "" || Width == "" || height == "" || margin == "" || area == "" || utilizearea == "" || volume == "" || capacity == ""){
			alert('Enter Required Details !');
		}
		else{
			$.ajax({
				url:"<?php echo admin_url(); ?>Warehouse/UpdateWarehouseSize",
				dataType:"JSON",
				method:"POST",
				data:{
				// 	WarehouseID : WarehouseID,
				// 	Warehousename : Warehousename,
    				warehouse : warehouse,
					chid : chid,
					chambername : chambername,
					length : length,
					Width : Width,
					height : height,
					margin : margin,
					area : area,
					utilizearea : utilizearea,
					volume : volume,
					capacity : capacity,
				},
				success:function(data){
					if(data == true){
					    $('select[name=warehouse]').val('').prop('disabled', false);
                    	$('.selectpicker').selectpicker('refresh');
						$(':input').val('');
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
</script>
<script>
    $("#chid").dblclick(function()
    {
        $('#warehouse_List').modal('show');
        $('#warehouse_List').on('shown.bs.modal', function () {
            $('#myInput1').val('');
            $('#myInput1').focus();
            var AccountID = '';
            $.ajax({
                url:"<?php echo admin_url(); ?>Warehouse/getWarehouseSizeData",
				//dataType:"JSON",
                method:"POST",
                cache: false,
                data:{AccountID:AccountID,},
                success:function(data){
                    if(empty(data)){
						
						}else{
                        $("#warehouse_List_body").html(data);
                        $('.get_AccountID').click(function(){ 
                            AccountID = $(this).attr("data-id");
                            $.ajax({
                                url: "<?php echo admin_url(); ?>Warehouse/getSingleWarehouseSize",
                                dataType:"JSON",
                                method:"POST",
                                data:{
                                    AccountID:AccountID,
								},
                                success:function(data){
                                    if(empty(data)){
										}else{
                                        // $('#WarehouseID').val(data.WHID);
                                        // $('#Warehousename').val(data.w_name);
                                        
                                        $('select[name=warehouse]').val(data.WHID);
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                        $('#chid').val(data.CHID);
            							$('#chambername').val(data.ChaumberName);
                                        $('#length').val(data.length);
                                        $('#Width').val(data.width);
                                        $('#height').val(data.height);
                                        $('#margin').val(data.margin);
                                        $('#area').val(data.total_area);
                                        $('#utilizearea').val(data.utilize_area);
                                        $('#volume').val(data.volume);
                                        $('#capacity').val(data.capacity);
                                        $('.saveBtn').hide();
                                        $('.updateBtn').show();
                                        $('.saveBtn2').hide();
                                        $('.updateBtn2').show();
									}
								},
							});
                            $('#warehouse_List').modal('hide');
						})
					}
				}
			});
		})
	});
</script>
<script>
	
	$(".cancelBtn").click(function(){
    	$(':input').val('');
    	$('select[name=warehouse]').val('').prop('disabled', false);
    	$('.selectpicker').selectpicker('refresh');
    	$('.saveBtn').show();
    	$('.saveBtn2').show();
    	$('.updateBtn').hide();
    	$('.updateBtn2').hide();
    });
</script>
<script>
	$('#chid').on('focus',function(){
		$(':input').val('');
		$('select[name=warehouse]').val('').prop('disabled', false);
		$('.selectpicker').selectpicker('refresh');
		$('.saveBtn').show();
		$('.saveBtn2').show();
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
	});
</script>
<script>
    $("#chid").on('blur', function(e) {
		var keyCode = e.keyCode || e.which;
		var AccountID = $('#chid').val();
		if(AccountID !== ""){
			e.preventDefault(); 
			$.ajax({
				url: "<?php echo admin_url(); ?>Warehouse/getSingleWarehouseSize",
				dataType:"JSON",
				method:"POST",
				data:{
					AccountID:AccountID,
				},
				success:function(data){
					if(empty(data)){
				// 		$(':input').val('');
                        }else{
						if(data.id == null)
						{
							$(':input').val('');
							$('#WarehouseID').val(AccountID);
							$('#Warehousename').val(data.w_name);
							$('.saveBtn').show();
							$('.updateBtn').hide();
							$('.saveBtn2').show();
							$('.updateBtn2').hide();
						}
						else
						{
						    $('select[name=warehouse]').val(data.WHID);
                            $('.selectpicker').selectpicker('refresh');
                            $('#chid').val(data.CHID);
							$('#chambername').val(data.ChaumberName);
                            $('#length').val(data.length);
                            $('#Width').val(data.width);
                            $('#height').val(data.height);
                            $('#margin').val(data.margin);
                            $('#area').val(data.total_area);
                            $('#utilizearea').val(data.utilize_area);
                            $('#volume').val(data.volume);
                            $('#capacity').val(data.capacity);
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
						}
						
					}
				},
			});
		}
	});
</script>
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Chamber List</td>';
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
        url:"<?php echo admin_url(); ?>Warehouse/export_Chamberlist",
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
            $.ajax({
                url: "<?php echo admin_url(); ?>Warehouse/chamberlist",
                dataType: "json",
                method: "POST",
                data: {warehouse1: warehouse,},
                beforeSend: function () {
                    $('#warehouse_List_body').html();
                    $('#searchh22').css('display', 'none');
                    $('#searchh2').css('display', 'block');
                },
                complete: function () {
                    $('#searchh2').css('display', 'none');
                },
                success: function (data) {
                    $('#warehouse_List_body').html();
                    $('#warehouse_List_body').html(data);
                    $('.get_AccountID').click(function(){ 
                            AccountID = $(this).attr("data-id");
                            $.ajax({
                                url: "<?php echo admin_url(); ?>Warehouse/getSingleWarehouseSize",
                                dataType:"JSON",
                                method:"POST",
                                data:{
                                    AccountID:AccountID,
								},
                                success:function(data){
                                    if(empty(data)){
										}else{
                                        // $('#WarehouseID').val(data.WHID);
                                        // $('#Warehousename').val(data.w_name);
                                        
                                        $('select[name=warehouse]').val(data.WHID);
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                        $('#chid').val(data.CHID);
            							$('#chambername').val(data.ChaumberName);
                                        $('#length').val(data.length);
                                        $('#Width').val(data.width);
                                        $('#height').val(data.height);
                                        $('#margin').val(data.margin);
                                        $('#area').val(data.total_area);
                                        $('#utilizearea').val(data.utilize_area);
                                        $('#volume').val(data.volume);
                                        $('#capacity').val(data.capacity);
                                        $('.saveBtn').hide();
                                        $('.updateBtn').show();
                                        $('.saveBtn2').hide();
                                        $('.updateBtn2').show();
									}
								},
							});
                            $('#warehouse_List').modal('hide');
						})
                }
            });
    });

</script>

<style>
	
	#table_warehouse_List td:hover {
    cursor: pointer;
	}
	#table_warehouse_List tr:hover {
    background-color: #ccc;
	}
	.col-md-2{
    margin-bottom:20px;
	}
	
    .table-StackListTable          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-StackListTable thead th { position: sticky; top: 0; z-index: 1; }
    .table-StackListTable tbody th { position: sticky; left: 0; }
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