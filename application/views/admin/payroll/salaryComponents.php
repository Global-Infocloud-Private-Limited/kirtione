<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait!! fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait!! Creating new Salary Head...</div>
								<div class="searchh4" style="display:none;">Please wait!! updating Salary Head...</div>
							</div>
							<input type="hidden" name="group_codehidden" id="group_codehidden" class="form-control" value="">
							<div class="col-md-4">
								<div class="form-group">
								    <small class="req text-danger">* </small>
									<label for="group_code">Component Code</label>
									<input type="text" name="group_code" id="group_code" class="form-control" value="">
									
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
								    <small class="req text-danger">* </small>
									<label for="group_name">Component Name</label>
									<input type="text" name="group_name" id="group_name" class="form-control" value="">
									<input type="hidden" name="form_mode" id="form_mode" value="add">
								</div>
							</div>
							
						</div>
						<div class="row"> 
							<div class="col-md-4">
								<div class="form-group">
								    <small class="req text-danger">* </small>
									<label for="group_type">Salary Head </label>
									<select name="group_type" id="group_type" class="selectpicker" data-live-search="true" data-width="100%">
									    <option value="">Not Selected</option>
										<option value="1">Earning</option>
										<option value="2">Deduction</option>
									</select>
								</div>
							</div>
							
							<div class="col-md-4">
								<div class="form-group">
								    <small class="req text-danger">* </small>
									<label for="movement_type">Measured In</label>
									<select name="movement_type" id="movement_type" class="selectpicker" data-live-search="true" data-width="100%">
									    <option value="">Not Selected</option>
										<option value="1">Fixed Amount</option>
										<option value="2">Percentage</option>
									</select>
								</div>
							</div>
							
							<div class="col-md-4 ESIC_calculated" id="ESIC_calculated" style="display:none">
								<div class="form-group">
									<label for="is_esic_cal">Is ESIC Calculated</label>
									<select name="is_esic_cal" id="is_esic_cal" class="selectpicker" data-live-search="true" data-width="100%">
									    <option value="">Not Selected</option>
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
							
						</div>
						<div class="row" id="per_div" style="display:none"> 
							<div class="col-md-4">
							    <div class="form-group">
									<label for="group_name">Percentage</label>
									<input type="text" name="percentage" id="percentage" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="calculatedBy">Calculated By</label>
									<select name="calculatedBy" id="calculatedBy" class="selectpicker" data-live-search="true" data-width="100%">
									    <option value="">Not Selected</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row"> 
							<div class="col-md-12">
								<?php if (has_permission_new('salaryComponents', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission_new('salaryComponents', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
								<?php
									}else{
									    alert("You are not allowed to update record");
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								}?>
								
								<button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
						</div>
					
						<div class="clearfix"></div>
						
						<div class="modal fade AccountGroup" id="AccountGroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">SalaryHead List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-AccountGroup tableFixHead2">
											<table class="tree table table-striped table-bordered table-AccountGroup tableFixHead2" id="table_AccountGroup" width="100%">
												<thead>
													<tr style="display:none;">
														<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
													</tr>
													<tr>
														<th id="sl" style="text-align:left;">Component Code</th>
														<th style="text-align:left;">Component Name</th>
														<th style="text-align:left;">Salary Head</th>
														<th style="text-align:left;">Measured In</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($salary_head_table as $key => $value) {
														?>
														<tr class="get_AccountID" data-id="<?php echo $value["code"]; ?>">
															<td><?php echo $value["code"];?></td>
															<td><?php echo $value["name"];?></td>
															<?php
																if($value["type"]=="1"){
																	$groupType = "Earning";
																	}elseif($value["type"]=="2"){
																	$groupType = "Deduction";
																}
																else{
																	$groupType = "";
																}
															?>
															<td><?php echo $groupType;?></td>
															<?php 
																if($value["mesuredIn"]=="1"){
																	$movement = "Fixed Amount";
																	}elseif($value["mesuredIn"]=="2"){
																	$movement = "Percentage";
																	}else{
																	$movement = "";
																}
															?>
															<td><?php echo $movement;?></td>
														</tr>
													<?php } ?>
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
<script type="text/javascript">
	$('#percentage').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
			event.preventDefault();
		}
	});
</script>
<script>
    $('#movement_type').on('change',function(){
       var value = $("#movement_type").val();
        if(value == "2"){
            $('#per_div').css('display','block');
                $.ajax({
        		    url:"<?php echo admin_url(); ?>payroll/GetSalaryHeadList",
        			dataType:"JSON",
        			method:"POST",
        			cache: false,
        			data:{value:value,},
        			success:function(data){
        				var optionsHTMLHead = '<option value="">Non selected</option>';
    					$.each(data, function(index, option) {
    						optionsHTMLHead += '<option value="' + option.code + '">' + option.name + '</option>';
    					});
    					$('select[name=calculatedBy]').html(optionsHTMLHead);
    					$('.selectpicker').selectpicker('refresh');
        			}
        		});
        }else{
            $('#per_div').css('display','none');
        }
    })
    
    $('#group_type').on('change',function(){
       var value = $("#group_type").val();
        if(value == "1"){
            $('#ESIC_calculated').css('display','block');
        }else{
            $('#ESIC_calculated').css('display','none');
        }
    })
    
    
    // Get head Detail by head code
		$('#group_code').on('blur',function(){
			var head_code = $('#group_code').val();
 			if(head_code == ""){
 				$('.saveBtn').show();
 				$('.saveBtn2').show();
 				$('.updateBtn').hide();
 				$('.updateBtn2').hide();
 				$('#group_name').val('');
 				$('select[name=group_type]').val('');
 				$('.selectpicker').selectpicker('refresh');
 				$('select[name=movement_type]').val('');
 				$('.selectpicker').selectpicker('refresh');
			}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>payroll/get_salary_head_details",
					dataType:"JSON",
					method:"POST",
					cache: false,
					data:{head_code:head_code,},
					success:function(data){
						if(empty(data)){
							$('.saveBtn').show();
							$('.saveBtn2').show();
							$('.updateBtn').hide();
							$('.updateBtn2').hide();
							//$('#group_code').val('');
							$('#group_name').val('');
							$('select[name=group_type]').val('');
							$('.selectpicker').selectpicker('refresh');
							$('select[name=movement_type]').val('');
							$('.selectpicker').selectpicker('refresh');
							$('#percentage').val('');
                            $('#per_div').css('display','none');
                            $('select[name=is_esic_cal]').val('N');
                			$('.selectpicker').selectpicker('refresh');
                			$('#ESIC_calculated').css('display','none');
						}else{
    							$('#group_code').val(data.code);
    							$('#group_name').val(data.name);
    							$('select[name=group_type]').val(data.type);
    							$('.selectpicker').selectpicker('refresh');
    							$('select[name=movement_type]').val(data.mesuredIn);
    							$('.selectpicker').selectpicker('refresh')
    							if(data.type =="1"){
                                    $('#ESIC_calculated').css('display','block');
                                    $('select[name=is_esic_cal]').val(data.ESIC_Calculated);
                                    $('.selectpicker').selectpicker('refresh');
                                }
    							if(data.mesuredIn == "2"){
    							    $('#per_div').css('display','block');
    							    var value = '';
    							    $.ajax({
                            		    url:"<?php echo admin_url(); ?>payroll/GetSalaryHeadList",
                            			dataType:"JSON",
                            			method:"POST",
                            			cache: false,
                            			data:{value:value,},
                            			success:function(data){
                            				var optionsHTMLHead = '<option value="">Non selected</option>';
                        					$.each(data, function(index, option) {
                        						optionsHTMLHead += '<option value="' + option.code + '">' + option.name + '</option>';
                        					});
                        					$('select[name=calculatedBy]').html(optionsHTMLHead);
                        					$('.selectpicker').selectpicker('refresh');
                            			}
                            		});
    							    $('#percentage').val(data.percentage);
    							    $('select[name=calculatedBy]').val(data.calculatedBy);
    							    $('.selectpicker').selectpicker('refresh');
    							}
    							$('.saveBtn').hide();
    							$('.updateBtn').show();
    							$('.saveBtn2').hide();
    							$('.updateBtn2').show();
						}
					}
				});
 			}
		})
	
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$("#group_code").dblclick(function(){
			$('#AccountGroup').modal('show');
			$('#AccountGroup').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
			})
		});
		
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		
	// Focus on head code
 		$('#group_code').on('focus',function(){
 			$('#group_code').val('');
 			$('#group_name').val('');
 			$('select[name=group_type]').val('');
 			$('.selectpicker').selectpicker('refresh');
 			$('select[name=movement_type]').val('');
 			$('.selectpicker').selectpicker('refresh');
 			$('#percentage').val('');
            $('#per_div').css('display','none');
            $('select[name=is_esic_cal]').val('N');
			$('.selectpicker').selectpicker('refresh');
			$('#ESIC_calculated').css('display','none');
 			$('.saveBtn').show();
 			$('.saveBtn2').show();
			$('.updateBtn').hide();
 			$('.updateBtn2').hide();
 		});
		
	// Cancel selected data
		$(".cancelBtn").click(function(){
			$('#group_code').val('');
			$('#group_name').val('');
			$('select[name=group_type]').val('');
			$('.selectpicker').selectpicker('refresh');
			$('select[name=movement_type]').val('');
			$('.selectpicker').selectpicker('refresh');
			$('#percentage').val('');
            $('#per_div').css('display','none');
            $('select[name=is_esic_cal]').val('N');
			$('.selectpicker').selectpicker('refresh');
			$('#ESIC_calculated').css('display','none');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		});
		

		$('.get_AccountID').on('click',function(){ 
            head_code = $(this).attr("data-id");
            $.ajax({
				url:"<?php echo admin_url(); ?>payroll/get_salary_head_details",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{head_code:head_code,},
				success:function(data){
                    if(empty(data)){
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
						$('#group_code').val('');
                        $('#group_name').val('');
                        $('select[name=group_type]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val('');
    				    $('#percentage').val('');
                        $('#per_div').css('display','none');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=is_esic_cal]').val('N');
			            $('.selectpicker').selectpicker('refresh');
			            $('#ESIC_calculated').css('display','none');
					}else{
                        $('#group_code').val(data.code);
                        $('#group_name').val(data.name);
                        $('select[name=group_type]').val(data.type);
                        $('.selectpicker').selectpicker('refresh');
                        if(data.type =="1"){
                            $('#ESIC_calculated').css('display','block');
                            $('select[name=is_esic_cal]').val(data.ESIC_Calculated);
                            $('.selectpicker').selectpicker('refresh');
                        }
                        $('select[name=movement_type]').val(data.mesuredIn);
                        $('.selectpicker').selectpicker('refresh')
                        if(data.mesuredIn == "2"){
    						$('#per_div').css('display','block');
    						var value = '';
    						var calBy = data.calculatedBy;
    						$.ajax({
                                url:"<?php echo admin_url(); ?>payroll/GetSalaryHeadList",
                                dataType:"JSON",
                            	method:"POST",
                            	cache: false,
                            	data:{value:value,},
                            	success:function(data){
                            	    var optionsHTMLHead = '<option value="">Non selected</option>';
                        			$.each(data, function(index, option) {
                        			    optionsHTMLHead += '<option value="' + option.code + '">' + option.name + '</option>';
                        		    });
                        			$('select[name=calculatedBy]').html(optionsHTMLHead);
                        			$('select[name=calculatedBy]').val(calBy);
                        			$('.selectpicker').selectpicker('refresh');
                            	}
                            });
    						$('#percentage').val(data.percentage);
    					}
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
					}
				}
			});
            $('#AccountGroup').modal('hide');
		});
		
		// Save New salary head
        $('.saveBtn').on('click',function(){ 
            HeadCode = $('#group_code').val();
            HeadName = $('#group_name').val();
            type = $('#group_type').val();
            mesuredIn = $('#movement_type').val();
            if(mesuredIn == "2"){
                percentage = $('#percentage').val();
                calculatedBy = $('#calculatedBy').val();
            }else{
                percentage = '';
                calculatedBy = '';
            }
            
            if(type ==""){
                alert("please select Salery Head Type");
            }else if(mesuredIn == ""){
                alert("please select MesuredIN");
            }else if(HeadCode == ""){
                alert("please enter salay head code");
            }else if(HeadName == ""){
                alert("please enter salary head name");
            }else if(mesuredIn == "2" && percentage ==""){
                alert("please enter percentage");
            }else if(mesuredIn == "2" && calculatedBy ==""){
                alert("please select CalcualtedBy");
            }else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>payroll/SaveHead",
                    dataType:"JSON",
                    method:"POST",
                    data:{HeadCode:HeadCode,HeadName:HeadName,type:type,mesuredIn:mesuredIn,percentage:percentage,calculatedBy:calculatedBy
    				},
                    beforeSend: function () {
    					$('.searchh3').css('display','block');
    					$('.searchh3').css('color','blue');
    				},
                    complete: function () {
    					$('.searchh3').css('display','none');
    				},
                    success:function(data){
    					if(data == true){
    						alert_float('success', 'Record created successfully...');
    						$('.saveBtn').show();
    						$('.saveBtn2').show();
    						$('.updateBtn').hide();
    						$('.updateBtn2').hide();
    						$('#group_code').val('');
    						$('#group_codehidden').val();
    						$('#group_name').val('');
    						$('select[name=group_type]').val('');
    						$('.selectpicker').selectpicker('refresh');
    						$('select[name=movement_type]').val('');
    						$('.selectpicker').selectpicker('refresh');
    						$('#percentage').val('');
    						$('#per_div').css('display','none');
    						$('select[name=is_esic_cal]').val('N');
                			$('.selectpicker').selectpicker('refresh');
                			$('#ESIC_calculated').css('display','none');
    					}else{
    						alert_float('warning', 'Something went wrong...');
    					}
    				}
    			});
            }
		}); 
        
	// Update Exiting head
        $('.updateBtn').on('click',function(){ 
            HeadCode = $('#group_code').val();
            HeadName = $('#group_name').val();
            HeadType = $('#group_type').val();
            if(HeadType == "1"){
                var ESIC_Calculated = $('#is_esic_cal').val();
            }else{
                var ESIC_Calculated = "";
            }
            measuredIn = $('#movement_type').val();
            if(measuredIn == "2"){
                percentage = $('#percentage').val();
                calculatedBy = $('#calculatedBy').val();
            }else{
                percentage = '';
                calculatedBy = '';
            }
           if(HeadType ==""){
                alert("please select Salery Head Type");
            }else if(measuredIn == ""){
                alert("please select MesuredIN");
            }else if(HeadCode == ""){
                alert("please enter salay head code");
            }else if(HeadName == ""){
                alert("please enter salary head name");
            }else if(measuredIn == "2" && percentage ==""){
                alert("please enter percentage");
            }else if(measuredIn == "2" && calculatedBy ==""){
                alert("please select CalcualtedBy");
            }else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>payroll/UpdateSalaryHead",
                    dataType:"JSON",
                    method:"POST",
                    data:{HeadCode:HeadCode,HeadName:HeadName,HeadType:HeadType,measuredIn:measuredIn,percentage:percentage,calculatedBy:calculatedBy,ESIC_Calculated:ESIC_Calculated
    				},
                    beforeSend: function () {
    					$('.searchh4').css('display','block');
    					$('.searchh4').css('color','blue');
    				},
                    complete: function () {
    					$('.searchh4').css('display','none');
    				},
                    success:function(data){
                        if(data == true){
    						alert_float('success', 'Record updated successfully...');
                            $('.saveBtn').show();
                            $('.saveBtn2').show();
                            $('.updateBtn').hide();
                            $('.updateBtn2').hide();
    					    $('#group_code').val('');
                            $('#group_name').val('');
                            $('select[name=group_type]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $('select[name=movement_type]').val('');
                            $('.selectpicker').selectpicker('refresh');
    						$('#per_div').css('display','none');
    						$('select[name=is_esic_cal]').val('N');
                			$('.selectpicker').selectpicker('refresh');
                			$('#ESIC_calculated').css('display','none');
    					}else{
    						alert_float('warning', 'Something went wrong...');
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

<style>
    #table_AccountGroup td:hover {
    cursor: pointer;
	}
	#table_AccountGroup tr:hover {
    background-color: #ccc;
	}
	
    .table-AccountGroup          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-AccountGroup thead th { position: sticky; top: 0; z-index: 1; }
    .table-AccountGroup tbody th { position: sticky; left: 0; }
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

