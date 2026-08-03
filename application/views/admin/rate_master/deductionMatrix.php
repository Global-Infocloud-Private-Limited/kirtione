<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>

#AccountID {
    text-transform: uppercase;
}
#table_warehouse_List td:hover {
    cursor: pointer;
}
#table_warehouse_List tr:hover {
    background-color: #ccc;
}

    
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="commodity">
                                    <small class="req text-danger">* </small>
                                    <label for="commodity" class="control-label">Commodity</label>
                                    <select name="commodity" id="commodity" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>
                                        <?php 
                                            foreach($commodity as $key=>$value){ ?>
                                                <option value="<?php echo $value['ItemID']; ?>"><?php echo $value['ItemName']; ?></option>
                                        <?php 
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="QCparameterName">
                                    <small class="req text-danger">* </small>
                                    <label for="QCparameterName" class="control-label">QC Parameter</label>
                                    <select name="QCparameterName" id="QCparameterName" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>
                                        
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="min_value"> 
                                    <label for="min_value" class="control-label">Min Value</label>
                                    <input type="text" name="min_value" id="min_value" class="form-control" value="" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="max_value"> 
                                    <label for="max_value" class="control-label">Max Value</label>
                                    <input type="text" name="max_value" id="max_value" class="form-control" value="" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="base_value"> 
                                    <label for="base_value" class="control-label">Base Value</label>
                                    <input type="text" name="base_value" id="base_value" class="form-control" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8" style="margin-top:1%;padding:0;">
                            <table class="table items table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="20%">Value</th>
                                        <th width="20%">Deduction(%)</th>
                                        <th width="10%">&nbsp;</th>
                                    </tr>
                                </thead>
                                    <tbody id="parameter_body">
                                        <?php
                                        $allParameter = json_encode($commodity);
                                        ?>
                                            <!--<tr>-->
                                            <!--    <td width="20%"><input id="parameter_value1" class="parameter_values" name="parameter_value" type="text" value="" style="width:100%;"></td>-->
                                            <!--    <td width="20%"><input id="parameter_per" class="parameter_pers" name="parameter_per" type="text" value="" style="width:100%;"></td>-->
                                            <!--    <td width="10%"><button type="button" onclick="add_row()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>-->
                                            <!--</tr>-->
                                            <tr id="tr1">
                                                <td width="20%"><input id="parameter_value1" onkeypress="isNumberKey(event,1)" class="parameter_values" name="parameter_value1" type="text" value="" style="width:100%;"></td>
                                                <td width="20%"><input id="parameter_deduction1" onkeypress="isNumberKeyDeduction(event,1)" class="parameter_deduction" name="parameter_deduction1" type="text" value="" style="width:100%;"></td>
                                                <td width="10%"><button type="button" onclick="addRowNew()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>
                                            </tr>
                                    </tbody>
                                    <input hidden id="totalParamCount" name="totalParamCount" type="text" value="1" style="width:100%;">
                            </table>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12" >
                                    
                                <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                                <button type="button" class="btn btn-default cancelBtn" style="margin-right: 25px;" >Cancel</button>
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
    $(document).ready(function(){
        $('.saveBtn').show();
        $('.updateBtn').hide();
    });
    
    $('#commodity').on('change', function() {
		var ItemID = $(this).val();
		var url = "<?php echo base_url(); ?>admin/rate_master/GetQcParameterByItemID";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ItemID: ItemID},
            dataType:'json',
            success: function(data) {
                $("#min_value").val('');
                $("#max_value").val('');
                $("#base_value").val('');
                
                $("#QCparameterName").find('option').remove();
                $("#QCparameterName").selectpicker("refresh");
                $("#QCparameterName").append(new Option('', 'select Parameter'));
                for (var i = 0; i < data.length; i++) {
                    $("#QCparameterName").append(new Option(data[i].ItemParameterName, data[i].ItemParameterID));
                }
                $('.selectpicker').selectpicker('refresh');
                $(".addedtr").remove();  
            }
        });
	});
	
	$('#QCparameterName').on('change', function() {
		var QCparameterID = $(this).val();
		var ItemID = $("#commodity").val();
		var url = "<?php echo base_url(); ?>admin/rate_master/GetQcParameterDetailsByItemID";
            jQuery.ajax({
                type: 'POST',
                url:url,
                data: {QCparameterID: QCparameterID,ItemID:ItemID},
                dataType:'json',
                success: function(data) {
                    let QCDetails = data.QcParameterDetails;
                    let deduction_matrix = data.deduction_matrix;
                    $("#min_value").val(QCDetails.MinValue);
                    $("#max_value").val(QCDetails.MaxValue);
                    $("#base_value").val(QCDetails.BaseValue);
                    
                    
                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                    
                    
                    if(deduction_matrix.length > 0){
                        $('#totalParamCount').val(deduction_matrix.length);
                        $("#parameter_body").html('');
                        var html = '';
                        var count = 0;
                	    for(let i = 0; i < deduction_matrix.length; i++){
                	        if(i == 0){
                	            html += '<tr id="tr1">';
                                html += '<td width="20%"><input id="parameter_value1" value="' + deduction_matrix[i].Value +'" onkeypress="isNumberKey(event,1)" class="parameter_values" name="parameter_value1" type="text"  style="width:100%;"></td>';
                                html += '<td width="20%"><input id="parameter_deduction1" value="' + deduction_matrix[i].Deduction +'" onkeypress="isNumberKeyDeduction(event,1)" class="parameter_deduction" name="parameter_deduction1" type="text"  style="width:100%;"></td>';
                                html += '<td width="10%"><button type="button" onclick="addRowNew()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>';
                                html += '</tr>';
                	        }else{
                	            var newCount = count + 1;
                	            html += '<tr id="tr'+ newCount +'">';
                                html += '<td width="20%"><input id="parameter_value'+ newCount+'" value="' + deduction_matrix[newCount - 1].Value +'" onkeypress="isNumberKey(event,'+ newCount +')" class="parameter_values" name="parameter_value'+ newCount +'" type="text" value="" style="width:100%;"></td>';
                                html += '<td width="20%"><input id="parameter_deduction'+ newCount +'" value="' + deduction_matrix[newCount - 1].Deduction +'" onkeypress="isNumberKeyDeduction(event,'+ newCount +')" class="parameter_deduction" name="parameter_deduction'+ newCount +'" type="text" value="" style="width:100%;"></td>';
                                html += '<td width="10%"><a href="#" style="padding: 4px;width: 35px;height: 30px;" onclick="removeRow('+ newCount +')" class="btn btn-danger removebtn"><i class="fa fa-times"></i></a></td>';
                                html += '</tr>';
                	        }
                	        count = count + 1;
                	    }
                        $("#parameter_body").append(html);
                    }else{
                        $('#totalParamCount').val(1);
                        $("#parameter_body").html('');
                        var html = '';
                	    html += '<tr id="tr1">';
                        html += '<td width="20%"><input id="parameter_value1" onkeypress="isNumberKey(event,1)" class="parameter_values" name="parameter_value1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="20%"><input id="parameter_deduction1" onkeypress="isNumberKeyDeduction(event,1)" class="parameter_deduction" name="parameter_deduction1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="10%"><button type="button" onclick="addRowNew()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>';
                        html += '</tr>';
                        $("#parameter_body").append(html);
                    }
    //                 $(".addedtr").remove();
    //                 for(var count = 0; count < deduction_matrix.length; count++)
    //                 {
    //                     var tblid = deduction_matrix[count].id;
    //                     var Value = deduction_matrix[count].Value;
    //                     var Deduction = deduction_matrix[count].Deduction;
						
				// 		//var lasttr = $('#parameter_body tr:last td').find("input").attr('id');
				// 		//var num= lasttr.match(/-?\d+\.?\d*/);
				// 		var newcount = parseInt(count)+parseInt(1);
						
				// 		markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><input name='parameter_values1[]' id='parameter_values"+newcount+"' value='"+Value+"' class='form-control parameter_values'></td>";
				// 		markup += "<td><input type='text' name='parameter_per1[]' id='parameter_per"+newcount+"' value='"+Deduction+"' class='form-control parameter_pers'></td>";
				// 		markup += "<td></td></tr>";
				// 		tableBody = $("#parameter_body");
				// 		tableBody.append(markup);
				// 	}
                    /*
                    $("#QCparameterName").find('option').remove();
                    $("#QCparameterName").selectpicker("refresh");
                    $("#QCparameterName").append(new Option('', 'select Parameter'));
                    for (var i = 0; i < data.length; i++) {
                        $("#QCparameterName").append(new Option(data[i].ItemParameterName, data[i].ItemParameterID));
                    }
                    $('.selectpicker').selectpicker('refresh');*/
                }
            });
	});
    
</script>

<script type="text/javascript">
    
    function isNumberKey(event, id){
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $('#parameter_value' + id).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2 )) {
            event.preventDefault();
        }
    }
    
    function isNumberKeyDeduction(event,id){
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var inputDeduction = $('#parameter_deduction' + id).val();
        if ((inputDeduction.indexOf('.') != -1) && (inputDeduction.substring(inputDeduction.indexOf('.')).length > 2 )) {
            event.preventDefault();
        }
    }
    
</script>
<script>
	
// 	function add_row(){
// 		var ItemID = $("#ItemID1").val();
// 		var DMGAmt = $("#DMGAmt").val();
		
// 		if(ItemID != '' && DMGAmt !='' ){
// 			var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
// 			var num= lasttr.match(/-?\d+\.?\d*/);
// 			var newcount = parseInt(num)+parseInt(1);
// 			var allParameter = <?= $allParameter?>;
			
// 			markup = "<tr class='addedtr'><td><select name='ItemID1[]'  required id='ItemID"+newcount+"' value='"+ItemID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
// 			markup += "<td><input name='DMGAmt1[]' id='DMGAmt"+newcount+"' value='"+DMGAmt+"' class='form-control'></td>";
// 			markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px; float:right;' style='float:right' id='removebtn' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
// 			tableBody = $("#parameter_body");
// 			tableBody.append(markup);
			
// 			for (var i = 0; i < allParameter.length; i++) {
// 				$("#ItemID"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));
// 			}
			
// 			$("#ItemID"+newcount).val(ItemID);
// 			$("#ItemID"+newcount).selectpicker('refresh');
			
			
// 			$("#ItemID1").val('');
// 			$('#ItemID1').selectpicker('refresh');
// 			$("#DMGAmt").val('');
			
// 		}
// 		else
// 		{
// 			alert('All Fields Are Required');
// 		}
		
// 	}
	
	function addRowNew(){
	    var itemTotal = $('#totalParamCount').val();
	    var newCount = parseInt(itemTotal) + 1;
	    var html = '';
	    html += '<tr id="tr'+ newCount +'">';
        html += '<td width="20%"><input id="parameter_value'+ newCount +'" onkeypress="isNumberKey(event,' + newCount + ')" class="parameter_values" name="parameter_value'+ newCount +'" type="text" value="" style="width:100%;"></td>';
        html += '<td width="20%"><input id="parameter_deduction'+ newCount +'" onkeypress="isNumberKeyDeduction(event,' + newCount + ')" class="parameter_deduction" name="parameter_deduction'+ newCount +'" type="text" value="" style="width:100%;"></td>';
        html += '<td width="10%"><a href="#" style="padding: 4px;width: 35px;height: 30px;" onclick="removeRow('+ newCount +')" class="btn btn-danger removebtn"><i class="fa fa-times"></i></a></td>';
        html += '</tr>';
        $("#parameter_body").append(html);
        $('#totalParamCount').val(parseInt(itemTotal) + 1);
	}
	
	function removeRow(id){
	    $('#totalParamCount').val(parseInt($('#totalParamCount').val()) - 1);
	    $('#tr' + id).remove();
	}
	
	$('.saveBtn').click(function(){
	    var itemArray = [];
        var valueInputs = document.getElementsByClassName("parameter_values");
        var deductionInputs = document.getElementsByClassName("parameter_deduction");
        var flag = false;
        for (var i = 0; i < valueInputs.length; i++) {
            if(valueInputs[i].value == '' || deductionInputs[i].value == ''){
                flag = true;
            }
            var innerObject = {
                "value": valueInputs[i].value,
                "deduction": deductionInputs[i].value
            }
            itemArray.push(innerObject);
        }
        
        if(flag != true){
            var commodity = $('#commodity').val();
            var QCparameterName = $('#QCparameterName').val();
            if(commodity != '' || QCparameterName){
                var url = "<?php echo base_url(); ?>admin/rate_master/addDeductionMatrix";
                jQuery.ajax({
                    type: 'POST',
                    url:url,
                    data: {commodity: commodity, QCparameterName:QCparameterName,itemArray:itemArray},
                    dataType:'json',
                    success: function(data) {
                        $("#min_value").val('');
                        $("#max_value").val('');
                        $("#base_value").val('');
                        $("#commodity option:first").prop("selected", true);
                        $("#QCparameterName").html("");
                        $("#QCparameterName option:first").prop("selected", true);
                        $('.selectpicker').selectpicker('refresh');
                        $('#totalParamCount').val(1);
                        $("#parameter_body").html('');
                        var html = '';
                	    html += '<tr id="tr1">';
                        html += '<td width="20%"><input id="parameter_value1" onkeypress="isNumberKey(event,1)" class="parameter_values" name="parameter_value1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="20%"><input id="parameter_deduction1" onkeypress="isNumberKeyDeduction(event,1)" class="parameter_deduction" name="parameter_deduction1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="10%"><button type="button" onclick="addRowNew()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>';
                        html += '</tr>';
                        $("#parameter_body").append(html);
                        alert('Deduction matrix added successfully');
                    }
                });
            }else{
                alert('Please select commodity and QC Parameter');
            }
        }else{
            alert('Please fill all fields');
        }
	});
	
	$('.updateBtn').click(function(){
	    var itemArray = [];
        var valueInputs = document.getElementsByClassName("parameter_values");
        var deductionInputs = document.getElementsByClassName("parameter_deduction");
        var flag = false;
        for (var i = 0; i < valueInputs.length; i++) {
            if(valueInputs[i].value == '' || deductionInputs[i].value == ''){
                flag = true;
            }
            var innerObject = {
                "value": valueInputs[i].value,
                "deduction": deductionInputs[i].value
            }
            itemArray.push(innerObject);
        }
        
        if(flag != true){
            var commodity = $('#commodity').val();
            var QCparameterName = $('#QCparameterName').val();
            if(commodity != '' || QCparameterName){
                var url = "<?php echo base_url(); ?>admin/rate_master/updateDeductionMatrix";
                jQuery.ajax({
                    type: 'POST',
                    url:url,
                    data: {commodity: commodity, QCparameterName:QCparameterName,itemArray:itemArray},
                    dataType:'json',
                    success: function(data) {
                        $("#min_value").val('');
                        $("#max_value").val('');
                        $("#base_value").val('');
                        $("#commodity option:first").prop("selected", true);
                        $("#QCparameterName").html("");
                        $("#QCparameterName option:first").prop("selected", true);
                        $('.selectpicker').selectpicker('refresh');
                        $('#totalParamCount').val(1);
                        $("#parameter_body").html('');
                        var html = '';
                	    html += '<tr id="tr1">';
                        html += '<td width="20%"><input id="parameter_value1" onkeypress="isNumberKey(event,1)" class="parameter_values" name="parameter_value1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="20%"><input id="parameter_deduction1" onkeypress="isNumberKeyDeduction(event,1)" class="parameter_deduction" name="parameter_deduction1" type="text" value="" style="width:100%;"></td>';
                        html += '<td width="10%"><button type="button" onclick="addRowNew()" class="btn btn-success" title="Add parameter"><i class="fa fa-plus"></i></button></td>';
                        html += '</tr>';
                        $("#parameter_body").append(html);
                        alert('Deduction matrix updated successfully');
                    }
                });
            }else{
                alert('Please select commodity and QC Parameter');
            }
        }else{
            alert('Please fill all fields');
        }
	});
	
</script>
<!--<script>-->
<!--    $(document).ready(function () {-->
<!--        $('#mandiID').keyup(function(){-->
<!--            $(this).val($(this).val().toUpperCase());-->
<!--        });-->
<!--    });-->
<!--</script>-->
<!--<script>-->
<!--    $('#mandiID').on('keydown', function(e) {-->
<!--        var keyCode = e.keyCode || e.which;-->
<!--        var mandiID = $('#mandiID').val();-->
<!--        if(keyCode == 9){-->
<!--            e.preventDefault(); -->
<!--            $.ajax({-->
<!--                url: "<?php echo admin_url(); ?>Cluster/getSingleMandi",-->
<!--                method:"POST",-->
<!--                dataType:"JSON",-->
<!--                data:{-->
<!--                    center_id:mandiID,-->
<!--                },-->
<!--                success:function(data){-->
<!--                    $('#mandiID').val(data.CenterID);-->
<!--                    $('#mandi').val(data.CenterName);-->
                   
<!--                    var competitor = data.CompetitorID.split(",");-->
<!--                    var valc = "";-->
                
<!--                    for(j=0;j<competitor.length;j++){-->
<!--                        if(j!=competitor.length-1){-->
<!--                            valc += competitor[j] +", ";-->
<!--                        }else{-->
<!--                            valc += competitor[j];-->
<!--                        }-->
<!--                    }-->
                    
<!--                    $('select[name=competitor]').selectpicker('val',valc.split(", "));-->
                    
<!--                    var commodity = data.commodity.split(",");-->
<!--                    var valc = "";-->
                
<!--                    for(j=0;j<commodity.length;j++){-->
<!--                        if(j!=commodity.length-1){-->
<!--                            valc += commodity[j] +", ";-->
<!--                        }-->
<!--                        else{-->
<!--                            valc += commodity[j];-->
<!--                        }-->
<!--                    }-->
                    
<!--                    let ItemParameter = data.Parameter;-->
<!--					for(var count = 0; count < ItemParameter.length; count++)-->
<!--                        {-->
<!--                            var tblid = ItemParameter[count].id;-->
<!--                            var ItemID = ItemParameter[count].ItemID;-->
<!--                            var DMGAmt = ItemParameter[count].DMGAmt;-->
<!--							var lasttr = $('#parameter_body tr:last td').find("select").attr('id');-->
<!--							var num= lasttr.match(/-?\d+\.?\d*/);-->
<!--							var newcount = parseInt(num)+parseInt(1);-->
								
<!--							var allParameter = <?= $allParameter?>;-->
								
<!--							markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='ItemID1[]' required id='ItemID"+newcount+"' value='"+ItemID+"' class='form-control selectpicker' data-live-search='true'></select></td>";-->
<!--							markup += "<td><input name='DMGAmt1[]' id='DMGAmt"+newcount+"' value='"+DMGAmt+"' class='form-control '></td>";-->
<!--							markup += "<td></td></tr>";-->
<!--							tableBody = $("#parameter_body");-->
<!--							tableBody.append(markup);-->
								
<!--							for (var i = 0; i < allParameter.length; i++) {-->
<!--								$("#ItemID"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));-->
<!--							}-->
								
<!--							$("#ItemID"+newcount).val(ItemID);-->
<!--							$("#ItemID"+newcount).selectpicker('refresh');-->
<!--						}-->
                    
<!--                    $('select[name=commodity]').selectpicker('val',valc.split(", "));-->
<!--                    $('#competitor').val(data.competitor).selectpicker('refresh');-->
<!--                    $('select[name=city]').change();-->
<!--                    $('select[name=state]').val(data.state).selectpicker('refresh');-->
<!--                    var state_id = $('#state :selected').val();-->
<!--                    $.ajax({-->
<!--                        url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",-->
<!--                        type: "post",-->
<!--                        data: {-->
<!--                            state_id: state_id,-->
<!--                        },-->
<!--                        beforeSend: function(){-->
<!--                            $('select[name=city]').val('').selectpicker('refresh');-->
<!--                        },-->
<!--                        success: function(c){-->
<!--                            $('select[name=city]').append(c).selectpicker('refresh');-->
<!--                            $('select[name=city]').val(data.city).selectpicker('refresh');-->
                            
<!--                            var city_id = $('#city :selected').val();-->
<!--                            $.ajax({-->
<!--                                url : "<?php echo admin_url(); ?>clients/GetTaluka",-->
<!--                                type: "post",-->
<!--                                data: {-->
<!--                                    city_id: city_id,-->
<!--                                },-->
<!--                                beforeSend: function(){-->
<!--                                    $('select[name=taluka]').val('').selectpicker('refresh');-->
<!--                                },-->
<!--                                success: function(t){-->
<!--                                    $('select[name=taluka]').append(t).selectpicker('refresh');-->
<!--                                    $('select[name=taluka]').val(data.taluka).selectpicker('refresh');-->
<!--                                }-->
<!--                            });-->
                    
<!--                        }-->
<!--                    });-->
                    
<!--                    $('.saveBtn').hide();-->
<!--                    $('.updateBtn').show();-->
<!--                    $('#mandiID').blur();-->
<!--                },-->
<!--            });-->
<!--        }-->
<!--    });-->
<!--</script>-->
<!--<script>-->
<!--    $('.cancelBtn').click(function(){-->
<!--        $('input').val('');-->
<!--        $('.selectpicker').val('').selectpicker('refresh');-->
<!--        $(".addedtr").remove(); -->
<!--        $('.saveBtn').show();-->
<!--        $('.updateBtn').hide();-->
<!--    });-->
<!--</script>-->
<!--<script>-->
<!--    
<!--</script>-->
<!--<script>-->
<!--    $('.saveBtn').click(function(){-->
<!--        var mandiID = $('#mandiID').val();-->
<!--        var mandi = $('#mandi').val();-->
<!--        var commodity = $('#commodity').val();-->
<!--        var competitor = $('#competitor').val();-->
<!--        var state = $('#state :selected').val();-->
<!--        var city = $('#city :selected').val();-->
<!--        var taluka = $('#taluka :selected').val();-->
        
<!--        let DMGdataArr = [];-->
<!--		    var i = 1;-->
<!--		    var ItemIDs = $("select[name='ItemID1[]']")-->
<!--    		.map(function(){return $(this).val();}).get();-->
<!--    		ItemIDs.forEach(function callback(value, index) {-->
<!--    			if(value != "")-->
<!--    			{-->
<!--    				var DMGAmt = $("input[name='DMGAmt1[]']")-->
<!--    				.map(function(){return $(this).val();}).get()[index];-->
    				
<!--    				var ii = i - 1;-->
<!--    				DMGdataArr[ii]=new Array();-->
<!--    				DMGdataArr[ii][0]=value;-->
<!--    				DMGdataArr[ii][1]=DMGAmt;-->
<!--    				i++;-->
<!--    			}-->
<!--    		});-->
		
<!--		    let DMGdataArrlen = DMGdataArr.length;-->
<!--		    var ItemdataSerializedArr = JSON.stringify(DMGdataArr);-->
        
<!--        if((mandiID != '') && (mandi != '') && (state != '') && (city != '')){-->
<!--            $.ajax({-->
<!--                url:"<?php echo admin_url(); ?>Cluster/saveMandi",-->
<!--                method: "POST",-->
<!--                dataType: "JSON",-->
<!--                data:{-->
<!--                    mandiID: mandiID,-->
<!--                    mandi: mandi,-->
<!--                    commodity:commodity,-->
<!--                    competitor:competitor,-->
<!--                    state: state,-->
<!--                    city: city,-->
<!--                    taluka:taluka,ItemdataSerializedArr:ItemdataSerializedArr,DMGdataArrlen:DMGdataArrlen-->
<!--                },-->
<!--                success:function(data){-->
<!--                    if(data == true){-->
<!--                        $('input').val('');-->
<!--                        $('.selectpicker').val('').selectpicker('refresh');-->
<!--                        alert('Center Created Successful');-->
<!--                        $(".addedtr").remove();-->
<!--                        $('.saveBtn').show();-->
<!--                        $('.updateBtn').hide();-->
<!--                    }-->
<!--                }    -->
<!--            });-->
<!--        }-->
<!--        else{-->
<!--            alert("Select Required Data !");   -->
<!--        }-->
<!--    });-->
<!--</script>-->
<!--<script>-->
<!--    $('.updateBtn').click(function(){-->
<!--        var mandiID = $('#mandiID').val();-->
<!--        var mandi = $('#mandi').val();-->
<!--        var commodity = $('#commodity').val();-->
<!--        var competitor = $('#competitor').val();-->
<!--        var state = $('#state :selected').val();-->
<!--        var city = $('#city :selected').val();-->
<!--        var taluka = $('#taluka :selected').val();-->
<!--        let ParadataArr = [];-->
<!--		var i = 1;-->
<!--		var ItemID = $("select[name='ItemID1[]']")-->
<!--    	.map(function(){return $(this).val();}).get();-->
<!--    	ItemID.forEach(function callback(value, index) {-->
<!--        	if(value != "")-->
<!--        	{-->
<!--        		var DMGAmt = $("input[name='DMGAmt1[]']")-->
<!--        		.map(function(){return $(this).val();}).get()[index];-->
        				
<!--        		var addtblid = $("input[name='addtblid[]']")-->
<!--    			.map(function(){return $(this).val();}).get()[index];-->
        				
<!--        		var ii = i - 1;-->
<!--        		ParadataArr[ii]=new Array();-->
<!--        		ParadataArr[ii][0]=value;-->
<!--        		ParadataArr[ii][1]=DMGAmt;-->
<!--        		ParadataArr[ii][2]=addtblid;-->
<!--        		i++;-->
<!--        	}-->
<!--        });-->
		
<!--		    let paradataArraylength = ParadataArr.length;-->
<!--		    var paradataSerializedArr = JSON.stringify(ParadataArr);-->
	        
<!--        if((mandiID != '') && (mandi != '') && (state != '') && (city != '')){-->
<!--            $.ajax({-->
<!--                url:"<?php echo admin_url(); ?>Cluster/updateMandi",-->
<!--                method: "POST",-->
<!--                dataType: "JSON",-->
<!--                data:{-->
<!--                    mandiID: mandiID,-->
<!--                    mandi: mandi,-->
<!--                    commodity:commodity,-->
<!--                    competitor:competitor,-->
<!--                    state: state,-->
<!--                    city: city,-->
<!--                    taluka:taluka,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr-->
<!--                },-->
<!--                success:function(data){-->
<!--                    if(data == true){-->
<!--                        $('input').val('');-->
<!--                        $('.selectpicker').val('').selectpicker('refresh');-->
<!--                        alert('Center Updated Successful');-->
<!--                        $(".addedtr").remove(); -->
<!--                        $('.saveBtn').show();-->
<!--                        $('.updateBtn').hide();-->
<!--                    }-->
<!--                }    -->
<!--            });-->
<!--        }-->
<!--        else{-->
<!--            alert("Select Required Data !");   -->
<!--        }-->
<!--    });-->
<!--</script>-->
<!--<script>-->
<!--    function fill_data(center_id){-->
<!--        $('#region_list').modal('hide');-->
<!--        $.ajax({-->
<!--            url: "<?php echo admin_url(); ?>Cluster/getSingleMandi",-->
<!--            method:"POST",-->
<!--            dataType:"JSON",-->
<!--            data:{-->
<!--                center_id:center_id,-->
<!--            },-->
<!--            success:function(data){-->
<!--                $('#mandiID').val(data.CenterID);-->
<!--                $('#mandi').val(data.CenterName);-->
<!--                var competitor = data.CompetitorID.split(",");-->
<!--                    var valc = "";-->
                
<!--                    for(j=0;j<competitor.length;j++){-->
<!--                        if(j!=competitor.length-1){-->
<!--                            valc += competitor[j] +", ";-->
<!--                        }-->
<!--                        else{-->
<!--                            valc += competitor[j];-->
<!--                        }-->
<!--                    }-->
                    
<!--                $('select[name=competitor]').selectpicker('val',valc.split(", "));-->

<!--                var commodity = data.commodity.split(",");-->
<!--                    var valc = "";-->
                
<!--                    for(j=0;j<commodity.length;j++){-->
<!--                        if(j!=commodity.length-1){-->
<!--                            valc += commodity[j] +", ";-->
<!--                        }-->
<!--                        else{-->
<!--                            valc += commodity[j];-->
<!--                        }-->
<!--                    }-->
                    
<!--                $('select[name=commodity]').selectpicker('val',valc.split(", "));-->
                
<!--                let ItemParameter = data.Parameter;-->
<!--				for(var count = 0; count < ItemParameter.length; count++)-->
<!--                    {-->
<!--                        var tblid = ItemParameter[count].id;-->
<!--                        var ItemID = ItemParameter[count].ItemID;-->
<!--                        var DMGAmt = ItemParameter[count].DMGAmt;-->
<!--						var lasttr = $('#parameter_body tr:last td').find("select").attr('id');-->
<!--						var num= lasttr.match(/-?\d+\.?\d*/);-->
<!--						var newcount = parseInt(num)+parseInt(1);-->
								
<!--						var allParameter = <?= $allParameter?>;-->
								
<!--						markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='ItemID1[]' required id='ItemID"+newcount+"' value='"+ItemID+"' class='form-control selectpicker' data-live-search='true'></select></td>";-->
<!--						markup += "<td><input name='DMGAmt1[]' id='DMGAmt"+newcount+"' value='"+DMGAmt+"' class='form-control '></td>";-->
<!--						markup += "<td></td></tr>";-->
<!--						tableBody = $("#parameter_body");-->
<!--						tableBody.append(markup);-->
								
<!--						for (var i = 0; i < allParameter.length; i++) {-->
<!--							$("#ItemID"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));-->
<!--						}-->
								
<!--						$("#ItemID"+newcount).val(ItemID);-->
<!--						$("#ItemID"+newcount).selectpicker('refresh');-->
<!--					}-->
<!--                $('select[name=state]').val(data.state).selectpicker('refresh');-->
<!--                var state_id = $('#state :selected').val();-->
<!--                $.ajax({-->
<!--                    url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",-->
<!--                    type: "post",-->
<!--                    data: {-->
<!--                        state_id: state_id,-->
<!--                    },-->
<!--                    beforeSend: function(){-->
<!--                        $('select[name=city]').val('').selectpicker('refresh');-->
<!--                    },-->
<!--                    success: function(c){-->
<!--                        $('select[name=city]').append(c).selectpicker('refresh');-->
<!--                        $('select[name=city]').val(data.city).selectpicker('refresh');-->
<!--                        $('select[name=city]').change();-->
<!--                        var city_id = $('#city :selected').val();-->
<!--                        $.ajax({-->
<!--                            url : "<?php echo admin_url(); ?>clients/GetTaluka",-->
<!--                            type: "post",-->
<!--                            data: {-->
<!--                                city_id: city_id,-->
<!--                            },-->
<!--                            beforeSend: function(){-->
<!--                                $('select[name=taluka]').val('').selectpicker('refresh');-->
<!--                            },-->
<!--                            success: function(t){-->
<!--                                $('select[name=taluka]').append(t).selectpicker('refresh');-->
<!--                                $('select[name=taluka]').val(data.taluka).selectpicker('refresh');-->
<!--                            }-->
<!--                        });-->
<!--                    }-->
<!--                });-->
                
<!--                $('.saveBtn').hide();-->
<!--                $('.updateBtn').show();-->
<!--                $('#mandiID').blur();-->
<!--            },-->
<!--        });-->
<!--    }-->
<!--</script>-->
<!--<script>-->
<!--    $('#mandiID').focus(function(){-->
<!--        $('input').val('');-->
<!--        $('.selectpicker').val('').selectpicker('refresh');-->
<!--        $(".addedtr").remove(); -->
<!--        $('.saveBtn').show();-->
<!--        $('.updateBtn').hide();-->
<!--    });-->
<!--</script>-->